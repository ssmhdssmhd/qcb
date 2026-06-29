<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    ob_end_flush();
    exit;
}

function sendJsonResponse($data, $code = 200) {
    http_response_code($code);
    $output = ob_get_clean();
    if (!empty(trim($output))) {
        $data['debug_output'] = base64_encode($output);
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (in_array($errno, $fatalTypes)) {
        sendJsonResponse([
            'success' => false,
            'message' => $errstr,
            'error_detail' => [
                'type' => $errno,
                'file' => basename($errfile),
                'line' => $errline
            ]
        ], 500);
    }
    return true;
}
set_error_handler('jsonErrorHandler');

function jsonFatalHandler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        @ob_clean();
        echo json_encode([
            'success' => false,
            'message' => $error['message'],
            'error_detail' => [
                'file' => basename($error['file']),
                'line' => $error['line']
            ],
            'fatal_error' => true
        ], JSON_UNESCAPED_UNICODE);
        @ob_end_flush();
    }
}
register_shutdown_function('jsonFatalHandler');

try {
    $rootDir = __DIR__;

    $requiredFiles = [
        $rootDir . '/src/M3U8AdSkipper.php',
        $rootDir . '/src/M3U8Parser.php',
        $rootDir . '/src/UpdateManager.php',
        $rootDir . '/src/CryptoUtil.php',
        $rootDir . '/src/AuthConfig.php',
        $rootDir . '/src/AuthValidator.php',
        $rootDir . '/gz/EnhancedAdRuleEngine.php',
        $rootDir . '/gz/DomainRuleManager.php',
    ];

    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            sendJsonResponse([
                'success' => false,
                'message' => '文件缺失: ' . basename($file)
            ], 500);
        }
        require_once $file;
    }

    ob_clean();

} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => '初始化失败: ' . $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}

try {
    $ruleManager = new DomainRuleManager();
    $updateManager = new UpdateManager();
    $authValidator = new AuthValidator();
} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => '类初始化失败: ' . $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function getInputJson() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

function resolveMasterPlaylist($url) {
    $parser = new M3U8Parser();
    try {
        $playlist = $parser->parse($url);
        if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
            $firstVariant = $playlist['variants'][0]['uri'] ?? '';
            if ($firstVariant) {
                $parsedUrl = parse_url($url);
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;
                if (strpos($firstVariant, '/') === 0) {
                    return $baseUrl . $firstVariant;
                } else {
                    return $baseUrl . $pathDir . '/' . $firstVariant;
                }
            }
        }
    } catch (Exception $e) {
    }
    return $url;
}

try {
    switch ($action) {
        case 'analyze':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }

            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $engine->setDomain($domain);

            $parser = new M3U8Parser();
            $playlist = $parser->parse($mediaUrl);

            if (empty($playlist['segments'])) {
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            $domainRules = $ruleManager->getRules($domain);
            $hasDomainRules = $domainRules !== null;

            sendJsonResponse([
                'success' => true,
                'url' => $url,
                'mediaUrl' => $mediaUrl,
                'domain' => $domain,
                'hasDomainRules' => $hasDomainRules,
                'playlist' => [
                    'isMaster' => !empty($playlist['isMaster']),
                    'version' => $playlist['version'] ?? 3,
                    'targetDuration' => $playlist['targetDuration'] ?? 0,
                    'endlist' => !empty($playlist['endlist']),
                    'variantCount' => count($playlist['variants'] ?? [])
                ],
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumpCount' => count($analysis['sequenceJumps']),
                    'adClusterCount' => count($analysis['adClusters'])
                ],
                'durationDistribution' => $analysis['durationDistribution'],
                'sequenceJumps' => array_slice($analysis['sequenceJumps'], 0, 20),
                'adClusters' => $analysis['adClusters'],
                'adSegments' => array_values(array_filter($analysis['segments'], function($r) {
                    return $r['isAd'];
                })),
                'allSegments' => $analysis['segments']
            ]);
            break;

        case 'rules/list':
            $rules = $ruleManager->getAllRules();
            sendJsonResponse(['success' => true, 'rules' => $rules]);
            break;

        case 'rules/get':
            $domain = $_GET['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $rules = $ruleManager->getRules($domain);
            if ($rules === null) {
                sendJsonResponse(['success' => false, 'message' => '规则不存在'], 404);
            }
            sendJsonResponse(['success' => true, 'domain' => $domain, 'rules' => $rules]);
            break;

        case 'rules/save':
            $input = getInputJson();
            $domain = $input['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $ruleData = $input['rules'] ?? [];
            $result = $ruleManager->saveRules($domain, $ruleData);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则保存成功' : '规则保存失败',
                'domain' => $domain
            ]);
            break;

        case 'rules/delete':
            $input = getInputJson();
            $domain = $input['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $result = $ruleManager->deleteRules($domain);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则删除成功' : '规则删除失败或不存在'
            ]);
            break;

        case 'rules/generate':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $parser = new M3U8Parser();
            $playlist = $parser->parse($mediaUrl);

            if (empty($playlist['segments'])) {
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            $generatedRules = $ruleManager->createFromAnalysis($domain, $analysis);
            $generatedRules['sample_url'] = $url;

            sendJsonResponse([
                'success' => true,
                'domain' => $domain,
                'rules' => $generatedRules,
                'analysis' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumpCount' => count($analysis['sequenceJumps']),
                    'adClusterCount' => count($analysis['adClusters'])
                ]
            ]);
            break;

        case 'skip':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);
            $ruleEngineProp->setValue($skipper, $enhancedEngine);

            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);

            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->process($url);

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);

            sendJsonResponse([
                'success' => true,
                'url' => $url,
                'mxjx' => $mxjxUrl,
                'stats' => $result['stats']
            ]);
            break;

        case 'mxjx':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
            }
            $url = $mediaUrl;

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);
            $ruleEngineProp->setValue($skipper, $enhancedEngine);

            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);

            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->process($url);

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
            $newM3U8Content = $result['output'];

            if ($isRemote) {
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;

                $lines = explode("\n", $newM3U8Content);
                $newLines = [];
                foreach ($lines as $line) {
                    if (!empty(trim($line)) &&
                        strpos($line, '#') !== 0 &&
                        strpos($line, 'http://') !== 0 &&
                        strpos($line, 'https://') !== 0) {
                        if ($pathDir === '' || $pathDir === '/') {
                            $line = $baseUrl . '/' . ltrim($line, '/');
                        } else {
                            $line = $baseUrl . $pathDir . '/' . ltrim($line, '/');
                        }
                    }
                    $newLines[] = $line;
                }
                $newM3U8Content = implode("\n", $newLines);
            }

            header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
            header('Content-Disposition: inline; filename="playlist.m3u8"');
            ob_clean();
            echo $newM3U8Content;
            exit;

        case 'mxjx/info':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse([
                    'code' => 400,
                    'success' => false,
                    'message' => '缺少 url 参数'
                ], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
            }
            $url = $mediaUrl;

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);
            $ruleEngineProp->setValue($skipper, $enhancedEngine);

            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);

            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->process($url);

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
            $newM3U8Content = $result['output'];

            if ($isRemote) {
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;

                $lines = explode("\n", $newM3U8Content);
                $newLines = [];
                foreach ($lines as $line) {
                    if (!empty(trim($line)) &&
                        strpos($line, '#') !== 0 &&
                        strpos($line, 'http://') !== 0 &&
                        strpos($line, 'https://') !== 0) {
                        if ($pathDir === '' || $pathDir === '/') {
                            $line = $baseUrl . '/' . ltrim($line, '/');
                        } else {
                            $line = $baseUrl . $pathDir . '/' . ltrim($line, '/');
                        }
                    }
                    $newLines[] = $line;
                }
                $newM3U8Content = implode("\n", $newLines);
            }

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $selfPath = dirname(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
            $selfPath = $selfPath === '.' ? '' : $selfPath;
            $selfBase = $protocol . '://' . $host . $selfPath;
            $playUrl = $selfBase . '/mx.php?action=mxjx&url=' . urlencode($mediaUrl);

            $stats = $result['stats'] ?? [];
            $hasRules = $enhancedEngine->getCurrentDomainRules() !== null;

            sendJsonResponse([
                'code' => 200,
                'success' => true,
                'message' => '解析成功',
                'data' => [
                    'original_url' => $_GET['url'] ?? '',
                    'media_url' => $mediaUrl,
                    'domain' => $domain,
                    'play_url' => $playUrl,
                    'has_domain_rules' => $hasRules,
                    'stats' => [
                        'total_segments' => $stats['totalSegments'] ?? 0,
                        'kept_segments' => $stats['keptSegments'] ?? 0,
                        'removed_segments' => $stats['removedSegments'] ?? 0,
                        'original_duration' => $stats['originalDuration'] ?? 0,
                        'filtered_duration' => $stats['filteredDuration'] ?? 0,
                        'saved_duration' => $stats['savedDuration'] ?? 0,
                        'ad_percentage' => $stats['adPercentage'] ?? 0
                    ]
                ]
            ]);
            break;

        case 'update/version':
            sendJsonResponse([
                'success' => true,
                'current_version' => $updateManager->getCurrentVersion(),
                'version_file' => file_exists(__DIR__ . '/version.php') ? trim(include __DIR__ . '/version.php') : ''
            ]);
            break;

        case 'update/check':
            $result = $updateManager->checkUpdate();
            sendJsonResponse($result);
            break;

        case 'update/integrity':
            $result = $updateManager->checkIntegrity();
            sendJsonResponse($result);
            break;

        case 'update/backup/list':
            $backups = $updateManager->getBackupList();
            sendJsonResponse(['success' => true, 'backups' => $backups]);
            break;

        case 'update/backup/create':
            $result = $updateManager->createBackup();
            sendJsonResponse($result);
            break;

        case 'update/backup/restore':
            $input = getInputJson();
            $filename = $input['filename'] ?? $_GET['filename'] ?? '';
            if (empty($filename)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 filename 参数'], 400);
            }
            $result = $updateManager->restoreBackup($filename);
            sendJsonResponse($result);
            break;

        case 'update/backup/delete':
            $input = getInputJson();
            $filename = $input['filename'] ?? $_GET['filename'] ?? '';
            if (empty($filename)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 filename 参数'], 400);
            }
            $result = $updateManager->deleteBackup($filename);
            sendJsonResponse($result);
            break;

        case 'update/download':
            $result = $updateManager->downloadUpdate();
            sendJsonResponse($result);
            break;

        case 'update/clear_cache':
            $result = $updateManager->clearAllCaches();
            sendJsonResponse([
                'success' => true,
                'message' => '缓存清理成功',
                'cache_info' => $result
            ]);
            break;

        case 'auth/info':
            $info = $authValidator->getAuthInfo();
            $info['success'] = true;
            $info['contact'] = 'QQ2094332348';
            sendJsonResponse($info);
            break;

        case 'auth/validate':
            $localValid = $authValidator->validateLocal();
            $remoteValid = $authValidator->validateRemote();
            sendJsonResponse([
                'success' => true,
                'local_valid' => $localValid,
                'remote_valid' => $remoteValid,
                'all_valid' => $localValid && $remoteValid,
                'error' => $authValidator->getLastError()
            ]);
            break;

        case 'auth/config/get':
            $config = $authValidator->getAuthConfig()->getConfig();
            sendJsonResponse(['success' => true, 'config' => $config]);
            break;

        case 'auth/config/save':
            $input = getInputJson();
            $config = $input['config'] ?? [];
            $result = $authValidator->getAuthConfig()->setConfig($config);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '配置保存成功' : '配置保存失败'
            ]);
            break;

        case 'auth/set':
            $input = getInputJson();
            $authCode = $input['auth_code'] ?? '';
            if (empty($authCode)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 auth_code 参数'], 400);
            }
            $result = $authValidator->setAuthCode($authCode);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '授权码设置成功' : '授权码设置失败'
            ]);
            break;

        case 'auth/generate':
            $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $authCode = $authValidator->generateAuthCode($domain);
            sendJsonResponse([
                'success' => true,
                'domain' => $domain,
                'auth_code' => $authCode
            ]);
            break;

        default:
            sendJsonResponse([
                'success' => false,
                'message' => '未知操作',
                'available_actions' => [
                    'analyze' => '分析视频广告',
                    'rules/list' => '获取所有域名规则',
                    'rules/get' => '获取指定域名规则',
                    'rules/save' => '保存域名规则',
                    'rules/delete' => '删除域名规则',
                    'rules/generate' => '根据视频自动生成规则',
                    'skip' => '去广告接口',
                    'mxjx' => '去广告m3u8输出',
                    'update/version' => '获取当前版本',
                    'update/check' => '检查更新',
                    'update/integrity' => '完整性检查',
                    'auth/info' => '授权信息',
                    'auth/validate' => '验证授权',
                    'auth/config/get' => '获取授权配置',
                    'auth/config/save' => '保存授权配置',
                    'auth/set' => '设置授权码',
                    'auth/generate' => '生成授权码'
                ]
            ], 400);
            break;
    }
} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ], 500);
}
