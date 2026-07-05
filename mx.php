<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$memoryLimit = @ini_get('memory_limit');
if (return_bytes_func($memoryLimit) < 256 * 1024 * 1024) {
    @ini_set('memory_limit', '256M');
}

function return_bytes_func($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

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
        $rootDir . '/src/CacheManager.php',
        $rootDir . '/gz/EnhancedAdRuleEngine.php',
        $rootDir . '/gz/DomainRuleManager.php',
        $rootDir . '/gz/ResourceSiteManager.php',
        $rootDir . '/gz/OfficialSiteManager.php',
        $rootDir . '/gz/OfficialReplaceManager.php',
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
    $siteManager = new ResourceSiteManager();
    $officialReplaceMgr = new OfficialReplaceManager();
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
            $domainRules = $ruleManager->getRules($domain);
            $hasDomainRules = $domainRules !== null;

            if ($hasDomainRules) {
                $mediaUrl = resolveMasterPlaylist($url);
                $engine = new EnhancedAdRuleEngine([
                    'checkDiscontinuity' => true,
                    'checkRepetitiveDuration' => true
                ]);
                $engine->setDomain($domain);

                $skipper = new M3U8AdSkipper();
                $reflection = new ReflectionClass($skipper);
                $ruleEngineProp = $reflection->getProperty('ruleEngine');
                $ruleEngineProp->setAccessible(true);
                $ruleEngineProp->setValue($skipper, $engine);

                $filterProp = $reflection->getProperty('filter');
                $filterProp->setAccessible(true);
                $filter = $filterProp->getValue($skipper);

                $filterReflection = new ReflectionClass($filter);
                $filterEngineProp = $filterReflection->getProperty('ruleEngine');
                $filterEngineProp->setAccessible(true);
                $filterEngineProp->setValue($filter, $engine);

                $result = $skipper->processWithSafeguard($mediaUrl);
                $stats = $result['stats'] ?? [];
                $safeguardTriggered = !empty($result['safeguardTriggered']);
                $safeguardReason = $result['safeguardReason'] ?? '';
                $safeguardMethod = $result['safeguardMethod'] ?? '';

                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host . $basePath;
                $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($mediaUrl);

                if ($safeguardTriggered && $safeguardMethod === 'none') {
                    $fallbackToFull = true;
                } else {
                    $fallbackToFull = false;
                }

                $fastModeMessage = '检测到已有域名规则，使用规则快速去广告';
                if ($safeguardTriggered) {
                    if ($safeguardMethod === 'smart_filter') {
                        $fastModeMessage = '规则过于激进，已自动切换智能过滤模式';
                    } elseif ($safeguardMethod === 'threshold_adjustment') {
                        $fastModeMessage = '规则过于激进，已自动调整检测阈值';
                    } elseif ($safeguardMethod === 'none') {
                        $fastModeMessage = '规则不匹配当前视频，将使用完整分析模式';
                    }
                }

                if ($fallbackToFull) {
                } else {
                    sendJsonResponse([
                        'success' => true,
                        'url' => $url,
                        'mediaUrl' => $mediaUrl,
                        'domain' => $domain,
                        'hasDomainRules' => true,
                        'fastMode' => true,
                        'safeguardTriggered' => $safeguardTriggered,
                        'safeguardReason' => $safeguardReason,
                        'safeguardMethod' => $safeguardMethod,
                        'learn_count' => $domainRules['learn_count'] ?? 0,
                        'message' => $fastModeMessage,
                        'mxjxUrl' => $mxjxUrl,
                        'playlist' => [
                            'isMaster' => false,
                            'version' => $result['original']['version'] ?? 3,
                            'targetDuration' => $result['original']['targetDuration'] ?? 0,
                            'endlist' => !empty($result['original']['endlist'])
                        ],
                        'stats' => [
                            'totalSegments' => $stats['totalSegments'] ?? 0,
                            'adSegments' => $stats['removedSegments'] ?? 0,
                            'keptSegments' => $stats['keptSegments'] ?? 0,
                            'originalDuration' => $stats['originalDuration'] ?? 0,
                            'filteredDuration' => $stats['filteredDuration'] ?? 0,
                            'savedDuration' => $stats['savedDuration'] ?? 0,
                            'adPercentage' => $stats['adPercentage'] ?? 0
                        ],
                        'domainRules' => $domainRules
                    ]);
                    break;
                }
            }

            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $engine->setDomain($domain);

            $parser = new M3U8Parser();
            $parser->setMaxSegments(5000);
            $playlist = $parser->parse($mediaUrl);
            unset($parser);

            if (empty($playlist['segments'])) {
                unset($playlist);
                unset($engine);
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            unset($engine);

            $autoLearn = isset($_GET['auto_learn']) ? $_GET['auto_learn'] === '1' || $_GET['auto_learn'] === 'true' : true;
            $learnResult = null;
            if ($autoLearn && $analysis['adCount'] > 0) {
                $learnResult = $ruleManager->learnFromAnalysis($domain, $analysis);
            }

            $currentRules = $ruleManager->getRules($domain);

            $playlistInfo = [
                'isMaster' => !empty($playlist['isMaster']),
                'version' => $playlist['version'] ?? 3,
                'targetDuration' => $playlist['targetDuration'] ?? 0,
                'endlist' => !empty($playlist['endlist']),
                'variantCount' => count($playlist['variants'] ?? [])
            ];
            unset($playlist);

            $adSegments = array_values(array_filter($analysis['segments'], function($r) {
                return $r['isAd'];
            }));

            $allSegmentsSummary = [];
            foreach ($analysis['segments'] as $idx => $seg) {
                $allSegmentsSummary[] = [
                    'i' => $idx,
                    'd' => $seg['duration'],
                    'a' => !empty($seg['isAd']) ? 1 : 0
                ];
            }

            sendJsonResponse([
                'success' => true,
                'url' => $url,
                'mediaUrl' => $mediaUrl,
                'domain' => $domain,
                'hasDomainRules' => $currentRules !== null,
                'fastMode' => false,
                'autoLearned' => $learnResult,
                'learn_count' => $currentRules['learn_count'] ?? 0,
                'playlist' => $playlistInfo,
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
                'adSegments' => $adSegments,
                'allSegments' => $allSegmentsSummary
            ]);
            unset($analysis);
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

        case 'rules/learn':
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
            $result = $ruleManager->learnFromAnalysis($domain, $analysis);
            $rules = $ruleManager->getRules($domain);

            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则学习完成' : '规则学习失败',
                'domain' => $domain,
                'learn_count' => $rules['learn_count'] ?? 0,
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumps' => count($analysis['sequenceJumps']),
                    'adClusters' => count($analysis['adClusters'])
                ]
            ]);
            break;

        case 'rules/export':
            $domain = $_GET['domain'] ?? '';
            $exportData = $ruleManager->exportRules($domain ?: null);
            if ($exportData === null) {
                sendJsonResponse(['success' => false, 'message' => '规则不存在'], 404);
            }
            if (!empty($_GET['download'])) {
                $filename = $domain ? "rules_{$domain}.json" : 'all_rules.json';
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Type: application/json; charset=utf-8');
                ob_clean();
                echo json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }
            sendJsonResponse($exportData);
            break;

        case 'rules/import':
            $input = getInputJson();
            if (empty($input)) {
                sendJsonResponse(['success' => false, 'message' => '缺少导入数据'], 400);
            }
            $result = $ruleManager->importRules($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
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
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                header('Content-Type: application/json; charset=utf-8');
                sendJsonResponse(['success' => false, 'code' => 400, 'message' => '缺少 url 参数'], 400);
            }

            try {
                $cacheManager = new CacheManager($rootDir . '/cache');
                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
                
                // 添加时间戳避免相同URL缓存问题
                $timestamp = $_GET['_t'] ?? '';
                $cacheKey = 'mxjx_' . md5($url . '_' . $domain . '_' . $timestamp);
                $cachedContent = $cacheManager->get($cacheKey);

                if ($cachedContent !== null && is_string($cachedContent) && empty($timestamp)) {
                    header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                    header('Content-Disposition: inline; filename="playlist.m3u8"');
                    header('X-Cache: HIT');
                    ob_clean();
                    echo $cachedContent;
                    exit;
                }

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

                $result = $skipper->processWithSafeguard($url);
                $safeguardTriggered = !empty($result['safeguardTriggered']);
                $safeguardReason = $result['safeguardReason'] ?? '';
                $safeguardMethod = $result['safeguardMethod'] ?? '';

                if (!$result['success'] && empty($result['output'])) {
                    header('Content-Type: application/json; charset=utf-8');
                    sendJsonResponse([
                        'success' => false,
                        'code' => 500,
                        'message' => 'M3U8 解析失败',
                        'error' => $result['error'] ?? '未知错误'
                    ], 500);
                }

                $newM3U8Content = $result['output'];

                $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;

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

                // 仅在无时间戳参数时缓存
                if (empty($timestamp)) {
                    $cacheManager->set($cacheKey, $newM3U8Content, 120);
                }

                header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                header('Content-Disposition: inline; filename="playlist.m3u8"');
                header('X-Cache: MISS');
                header('X-Request-Time: ' . time());
                if ($safeguardTriggered) {
                    header('X-Safeguard: triggered');
                    header('X-Safeguard-Reason: ' . rawurlencode($safeguardReason));
                    header('X-Safeguard-Method: ' . $safeguardMethod);
                } else {
                    header('X-Safeguard: not_triggered');
                }
                ob_clean();
                echo $newM3U8Content;
                exit;

            } catch (Exception $e) {
                header('Content-Type: application/json; charset=utf-8');
                sendJsonResponse([
                    'success' => false,
                    'code' => 500,
                    'message' => '处理失败',
                    'error' => $e->getMessage()
                ], 500);
            }
            break;

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

            $stats = $result['stats'] ?? [];
            $adPercentage = $stats['adPercentage'] ?? 0;
            if ($adPercentage >= 95 && $stats['totalSegments'] > 10) {
                $newM3U8Content = file_get_contents($url);
                if ($newM3U8Content === false) {
                    $newM3U8Content = $result['output'];
                }
            } else {
                $newM3U8Content = $result['output'];
            }

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;

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

        case 'update/system_info':
            sendJsonResponse($updateManager->getSystemInfo());
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

        case 'sites/list':
            $includePaused = isset($_GET['include_paused']) && $_GET['include_paused'] === '1';
            $sites = $siteManager->getAllSites($includePaused);
            $config = $siteManager->getAutoLearnConfig();
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            sendJsonResponse([
                'success' => true,
                'sites' => $sites,
                'total' => count($sites),
                'auto_learn_config' => $config,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn
            ]);
            break;

        case 'sites/get':
            $name = $_GET['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $site = $siteManager->getSiteByName($name);
            if ($site === null) {
                sendJsonResponse(['success' => false, 'message' => '资源站不存在'], 404);
            }
            sendJsonResponse(['success' => true, 'site' => $site]);
            break;

        case 'sites/add':
            $input = getInputJson();
            $result = $siteManager->addSite($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/update':
            $input = getInputJson();
            $name = $input['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->updateSite($name, $input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/delete':
            $input = getInputJson();
            $name = $input['name'] ?? $_GET['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->deleteSite($name);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/health_check':
            $maxSites = isset($_GET['max']) ? intval($_GET['max']) : null;
            $result = $siteManager->batchCheckHealth($maxSites);
            sendJsonResponse(['success' => true] + $result);
            break;

        case 'sites/update_status':
            $input = getInputJson();
            $name = $input['name'] ?? '';
            $status = $input['status'] ?? 'active';
            $note = $input['note'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->updateSiteStatus($name, $status, $note);
            sendJsonResponse(['success' => $result, 'message' => $result ? '更新成功' : '更新失败']);
            break;

        case 'sites/fetch_videos':
            $name = $_GET['name'] ?? '';
            $apiUrl = $_GET['api_url'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);

            if (!empty($name)) {
                $site = $siteManager->getSiteByName($name);
                if ($site) {
                    $apiUrl = $site['api_url'];
                }
            }

            if (empty($apiUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请指定资源站名称或采集接口地址'], 400);
            }

            $result = $siteManager->fetchVideos($apiUrl, $page, $limit);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/search':
            $name = $_GET['name'] ?? '';
            $apiUrl = $_GET['api_url'] ?? '';
            $keyword = $_GET['keyword'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);

            if (empty($keyword)) {
                sendJsonResponse(['success' => false, 'message' => '请输入搜索关键词'], 400);
            }

            if (!empty($name)) {
                $site = $siteManager->getSiteByName($name);
                if ($site) {
                    $apiUrl = $site['api_url'];
                }
            }

            if (empty($apiUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请指定资源站名称或采集接口地址'], 400);
            }

            $result = $siteManager->searchVideos($apiUrl, $keyword, $page, $limit);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/search_all':
            $keyword = $_GET['keyword'] ?? '';
            $maxSites = intval($_GET['max_sites'] ?? 5);
            $limitPerSite = intval($_GET['limit_per_site'] ?? 10);

            if (empty($keyword)) {
                sendJsonResponse(['success' => false, 'message' => '请输入搜索关键词'], 400);
            }

            $result = $siteManager->searchAllSites($keyword, $maxSites, $limitPerSite);
            sendJsonResponse($result, 200);
            break;

        case 'sites/learn_video':
            $input = getInputJson();
            $videoUrl = $input['url'] ?? $_GET['url'] ?? '';

            if (empty($videoUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请提供视频URL'], 400);
            }

            $minSegments = isset($input['min_segments']) ? intval($input['min_segments']) : null;
            $maxAdPercentage = isset($input['max_ad_percentage']) ? intval($input['max_ad_percentage']) : null;

            $options = [];
            if ($minSegments !== null) $options['min_segments'] = $minSegments;
            if ($maxAdPercentage !== null) $options['max_ad_percentage'] = $maxAdPercentage;

            $result = $siteManager->learnFromVideoUrl($videoUrl, $ruleManager, $options);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/auto_learn/config':
            $config = $siteManager->getAutoLearnConfig();
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            sendJsonResponse([
                'success' => true,
                'config' => $config,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn
            ]);
            break;

        case 'sites/auto_learn/config/save':
            $input = getInputJson();
            $result = $siteManager->setAutoLearnConfig($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/auto_learn/run':
            $input = getInputJson();
            $options = [
                'max_sites' => $input['max_sites'] ?? null,
                'videos_per_site' => $input['videos_per_site'] ?? null,
                'keyword' => $input['keyword'] ?? ''
            ];
            $result = $siteManager->runAutoLearn($ruleManager, $options);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/auto_learn/status':
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            $config = $siteManager->getAutoLearnConfig();
            sendJsonResponse([
                'success' => true,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn,
                'config' => $config
            ]);
            break;

        case 'official_sites/status':
            $officialMgr = new OfficialSiteManager();
            sendJsonResponse([
                'success' => true,
                'enabled' => $officialMgr->isEnabled(),
                'settings' => $officialMgr->getSettings()
            ]);
            break;

        case 'official_sites/list':
            $officialMgr = new OfficialSiteManager();
            $includePaused = isset($_GET['include_paused']) && $_GET['include_paused'] === '1';
            $sites = $officialMgr->getAllSites($includePaused);
            sendJsonResponse([
                'success' => true,
                'sites' => $sites,
                'total' => count($sites),
                'enabled' => $officialMgr->isEnabled(),
                'settings' => $officialMgr->getSettings()
            ]);
            break;

        case 'official_sites/get':
            $officialMgr = new OfficialSiteManager();
            $name = $_GET['name'] ?? '';
            $site = $officialMgr->getSiteByName($name);
            if ($site) {
                sendJsonResponse(['success' => true, 'site' => $site]);
            } else {
                sendJsonResponse(['success' => false, 'message' => '资源站不存在'], 404);
            }
            break;

        case 'official_sites/add':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $result = $officialMgr->addSite($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/update':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $name = $input['name'] ?? '';
            unset($input['name']);
            $result = $officialMgr->updateSite($name, $input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/delete':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $name = $input['name'] ?? '';
            $result = $officialMgr->deleteSite($name);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/fetch_videos':
            $officialMgr = new OfficialSiteManager();
            $name = $_GET['name'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $result = $officialMgr->fetchVideos($name, $page, $limit);
            sendJsonResponse($result);
            break;

        case 'official_sites/search':
            $officialMgr = new OfficialSiteManager();
            $name = $_GET['name'] ?? '';
            $keyword = $_GET['keyword'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $result = $officialMgr->searchVideos($name, $keyword, $page, $limit);
            sendJsonResponse($result);
            break;

        case 'official_sites/search_all':
            $officialMgr = new OfficialSiteManager();
            $keyword = $_GET['keyword'] ?? '';
            $maxSites = intval($_GET['max_sites'] ?? 5);
            $limitPerSite = intval($_GET['limit_per_site'] ?? 10);
            $result = $officialMgr->searchAllSites($keyword, $maxSites, $limitPerSite);
            sendJsonResponse($result);
            break;

        case 'official_sites/set_domain':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $name = $input['name'] ?? '';
            $domainIndex = intval($input['domain_index'] ?? 0);
            $officialMgr->setActiveDomain($name, $domainIndex);
            sendJsonResponse(['success' => true, 'message' => '已切换域名']);
            break;

        case 'official_sites/settings/save':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $result = $officialMgr->updateSettings($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/toggle':
            $officialMgr = new OfficialSiteManager();
            $input = getInputJson();
            $enabled = !empty($input['enabled']);
            $result = $officialMgr->setEnabled($enabled);
            sendJsonResponse($result);
            break;

        case 'official_replace/config':
            $config = $officialReplaceMgr->getConfig();
            sendJsonResponse([
                'success' => true,
                'config' => $config
            ]);
            break;

        case 'official_replace/config/save':
            $input = getInputJson();
            $result = $officialReplaceMgr->saveConfigData($input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '保存成功' : '保存失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platforms':
            $platforms = $officialReplaceMgr->getPlatforms();
            sendJsonResponse([
                'success' => true,
                'platforms' => $platforms,
                'total' => count($platforms)
            ]);
            break;

        case 'official_replace/platform/add':
            $input = getInputJson();
            $result = $officialReplaceMgr->addPlatform($input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '添加成功' : '添加失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platform/update':
            $input = getInputJson();
            $index = $input['index'] ?? -1;
            if ($index < 0) {
                sendJsonResponse(['success' => false, 'message' => '缺少 index 参数'], 400);
            }
            $result = $officialReplaceMgr->updatePlatform($index, $input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '更新成功' : '更新失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platform/delete':
            $input = getInputJson();
            $index = $input['index'] ?? -1;
            if ($index < 0) {
                sendJsonResponse(['success' => false, 'message' => '缺少 index 参数'], 400);
            }
            $result = $officialReplaceMgr->deletePlatform($index);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '删除成功' : '删除失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/resolve':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $result = $officialReplaceMgr->resolve($url);
            sendJsonResponse($result, $result['success'] ? 200 : 404);
            break;

        case 'official_replace/info':
            // 禁止缓存，每次都重新解析
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $result = $officialReplaceMgr->resolve($url);
            
            if ($result['success']) {
                $m3u8Url = $result['m3u8_url'] ?? '';
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host . $basePath;
                $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
                
                sendJsonResponse([
                    'success' => true,
                    'platform' => $result['platform'],
                    'original_url' => $result['original_url'],
                    'video_title' => $result['video_title'],
                    'video_name' => $result['video_name'] ?? '',
                    'video_pic' => $result['video_pic'] ?? '',
                    'video_remarks' => $result['video_remarks'] ?? '',
                    'match_score' => $result['match_score'],
                    'site' => $result['site'],
                    'm3u8_url' => $m3u8Url,
                    'target_episode' => $result['target_episode'] ?? '',
                    'ad_skip_url' => $mxjxUrl,
                    'all_urls' => $result['all_urls'],
                    'episodes' => $result['episodes'] ?? count($result['all_urls']),
                    'timestamp' => time() // 添加时间戳便于追踪
                ]);
            } else {
                sendJsonResponse($result, 404);
            }
            break;

        case 'player/config':
            $configFile = $rootDir . '/gz/player_config.php';
            $defaultConfig = [
                'player' => 'dplayer',
                'autoplay' => false,
                'preload' => 'auto',
                'api_base_url' => '',
                'hls_config' => [
                    'enableWorker' => true,
                    'lowLatencyMode' => false,
                    'maxBufferLength' => 30,
                    'maxMaxBufferLength' => 600,
                    'minBufferLength' => 2,
                    'maxBufferSize' => 60 * 1000 * 1000,
                    'maxBufferHole' => 0.5,
                    'highBufferWatchdogPeriod' => 2,
                    'startLevel' => -1,
                    'capLevelToPlayerSize' => false,
                ],
            ];
            $config = $defaultConfig;
            if (file_exists($configFile)) {
                $fileConfig = require $configFile;
                if (is_array($fileConfig)) {
                    $config = array_merge($defaultConfig, $fileConfig);
                }
            }
            sendJsonResponse(['success' => true, 'config' => $config]);
            break;

        case 'player/config/save':
            $input = getInputJson();
            $configFile = $rootDir . '/gz/player_config.php';
            
            $allowedKeys = ['player', 'autoplay', 'preload', 'api_base_url', 'hls_config'];
            $newConfig = [];
            
            foreach ($allowedKeys as $key) {
                if (isset($input[$key])) {
                    $newConfig[$key] = $input[$key];
                }
            }
            
            if (empty($newConfig)) {
                sendJsonResponse(['success' => false, 'message' => '没有有效的配置项'], 400);
            }
            
            $existingConfig = [];
            if (file_exists($configFile)) {
                $existingConfig = require $configFile;
                if (!is_array($existingConfig)) {
                    $existingConfig = [];
                }
            }
            
            $finalConfig = array_merge($existingConfig, $newConfig);
            
            $configContent = '<?php' . "\nreturn " . var_export($finalConfig, true) . ';';
            $result = file_put_contents($configFile, $configContent);
            
            sendJsonResponse([
                'success' => $result !== false,
                'message' => $result !== false ? '保存成功' : '保存失败',
                'config' => $finalConfig
            ], $result !== false ? 200 : 400);
            break;

        case 'moxi':
        case 'moxi/api':
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            $playType = $_GET['type'] ?? '';
            
            if (empty($url)) {
                echo json_encode([
                    'code' => 400,
                    'url' => '',
                    'msg' => '缺少 url 参数',
                    'jm' => '',
                    'js' => '',
                    'time' => date('Y-m-d H:i:s'),
                    'kfz' => '沫兮API'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            
            $officialDomains = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];
            $parsedUrl = parse_url($url);
            $urlHost = $parsedUrl['host'] ?? '';
            $isOfficialUrl = false;
            
            foreach ($officialDomains as $domain) {
                if (strpos($urlHost, $domain) !== false) {
                    $isOfficialUrl = true;
                    break;
                }
            }
            
            $playUrl = '';
            $juMing = '';
            $jiShu = '';
            $code = 200;
            $msg = '解析成功';
            
            $extractTitleFromUrl = function($url) use (&$extractEpisodeFromUrl) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                $host = $parsed['host'] ?? '';
                
                if (empty($path)) {
                    return $host ?: '在线视频';
                }
                
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                if (empty($pathParts)) {
                    return $host ?: '在线视频';
                }
                
                $fileName = end($pathParts);
                $fileNameWithoutExt = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $fileName);
                
                $isEpisodeLike = false;
                if (preg_match('/第?\d+[集期话]/u', $fileNameWithoutExt)) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/^(episode|ep|e|集|期|话)[_\-]?\d+$/i', $fileNameWithoutExt)) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/^\d+$/',$fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 4) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/[_\-]\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 15) {
                    $prefix = preg_replace('/[_\-]\d+$/', '', $fileNameWithoutExt);
                    if (in_array(strtolower($prefix), ['episode', 'ep', 'e', '第', '集', ''])) {
                        $isEpisodeLike = true;
                    }
                }
                
                if ($isEpisodeLike || $fileName === 'index.m3u8' || $fileNameWithoutExt === 'index') {
                    $candidates = [];
                    $dirParts = array_slice($pathParts, 0, -1);
                    foreach (array_reverse($dirParts) as $part) {
                        if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                        if (is_numeric($part)) continue;
                        if (strlen($part) < 2) continue;
                        $lowerPart = strtolower($part);
                        if (in_array($lowerPart, ['video', 'videos', 'm3u8', 'movie', 'tv', 'play', 'player'])) continue;
                        $candidates[] = $part;
                    }
                    if (!empty($candidates)) {
                        $title = $candidates[0];
                        $title = preg_replace('/[_-]+/', ' ', $title);
                        $title = trim($title);
                        if (!empty($title)) {
                            if (preg_match('/^[a-z\s]+$/i', $title)) {
                                return ucwords($title);
                            }
                            return $title;
                        }
                    }
                    return $host ?: '在线视频';
                }
                
                $title = $fileNameWithoutExt;
                $title = preg_replace('/[_-]+/', ' ', $title);
                $title = preg_replace('/\s*\d+\s*$/', '', $title);
                $title = trim($title);
                
                if (empty($title) || strlen($title) < 2) {
                    $dirParts = array_slice($pathParts, 0, -1);
                    foreach (array_reverse($dirParts) as $part) {
                        if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                        if (is_numeric($part)) continue;
                        if (strlen($part) < 2) continue;
                        $lowerPart = strtolower($part);
                        if (in_array($lowerPart, ['video', 'videos', 'm3u8', 'movie', 'tv'])) continue;
                        $title = $part;
                        $title = preg_replace('/[_-]+/', ' ', $title);
                        $title = trim($title);
                        if (!empty($title)) {
                            if (preg_match('/^[a-z\s]+$/i', $title)) {
                                return ucwords($title);
                            }
                            return $title;
                        }
                    }
                    return $host ?: '在线视频';
                }
                
                if (preg_match('/^[a-z\s]+$/i', $title)) {
                    return ucwords($title);
                }
                
                return $title;
            };
            
            $extractEpisodeFromUrl = function($url) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                
                if (empty($path)) {
                    return '正片';
                }
                
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                foreach (array_reverse($pathParts) as $part) {
                    $part = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $part);
                    
                    if (preg_match('/第(\d+)[集期话]/u', $part, $matches)) {
                        return '第' . $matches[1] . '集';
                    }
                    
                    if (preg_match('/(?:episode|ep|e)[_\-]?(\d+)/i', $part, $matches)) {
                        return '第' . intval($matches[1]) . '集';
                    }
                    
                    if (preg_match('/^(\d+)$/', $part, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > 0 && $num < 1000) {
                            return '第' . $num . '集';
                        }
                    }
                    
                    if (preg_match('/[_\-](\d+)$/', $part, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > 0 && $num < 1000) {
                            $prefix = preg_replace('/[_\-]\d+$/', '', $part);
                            if (empty($prefix) || in_array(strtolower($prefix), ['episode', 'ep', 'e'])) {
                                return '第' . $num . '集';
                            }
                        }
                    }
                }
                
                return '正片';
            };
            
            if ($isOfficialUrl) {
                $result = $officialReplaceMgr->resolve($url);
                if ($result['success']) {
                    $m3u8Url = $result['m3u8_url'] ?? '';
                    $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
                    $juMing = $result['video_title'] ?? '';
                    $jiShu = $result['target_episode'] ?? ($result['episode'] ?? '');
                    if (empty($jiShu)) {
                        $jiShu = '正片';
                    }
                } else {
                    $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
                    $juMing = $result['video_title'] ?? '';
                    if (empty($juMing)) {
                        $juMing = $extractTitleFromUrl($url);
                    }
                    $jiShu = $result['episode'] ?? '';
                    if (empty($jiShu)) {
                        $jiShu = $extractEpisodeFromUrl($url);
                    }
                    $code = 200;
                    $msg = '解析成功';
                }
            } else {
                $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
                $juMing = $extractTitleFromUrl($url);
                $jiShu = $extractEpisodeFromUrl($url);
                
                $searchKeyword = '';
                $parsedUrl = parse_url($url);
                $path = $parsedUrl['path'] ?? '';
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                foreach ($pathParts as $part) {
                    if (preg_match('/\.(m3u8|mp4|mkv|avi|mov|flv|ts)$/i', $part)) continue;
                    if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                    if (is_numeric($part)) continue;
                    if (strlen($part) < 3) continue;
                    if ($part === 'video' || $part === 'videos' || $part === 'm3u8') continue;
                    $searchKeyword = $part;
                    break;
                }
                
                if (!empty($searchKeyword) && $searchKeyword !== $juMing) {
                    $searchKeyword = preg_replace('/[_-]+/', ' ', $searchKeyword);
                    $searchKeyword = trim($searchKeyword);
                }
                
                if (!empty($searchKeyword) && class_exists('ResourceSiteManager') && isset($siteManager)) {
                    try {
                        $searchResult = $siteManager->searchAllSites($searchKeyword, 3, 5);
                        if ($searchResult['success'] && !empty($searchResult['results'])) {
                            $bestMatch = null;
                            $bestScore = 0;
                            $urlBase = basename($path, '.m3u8');
                            
                            foreach ($searchResult['results'] as $siteResult) {
                                if (empty($siteResult['videos'])) continue;
                                foreach ($siteResult['videos'] as $video) {
                                    $videoName = $video['name'] ?? '';
                                    if (empty($videoName)) continue;
                                    
                                    $score = 0;
                                    similar_text($searchKeyword, $videoName, $score);
                                    
                                    $firstUrl = $video['first_url'] ?? $video['url'] ?? '';
                                    if (!empty($firstUrl)) {
                                        $firstUrlPath = parse_url($firstUrl, PHP_URL_PATH) ?? '';
                                        similar_text($path, $firstUrlPath, $pathScore);
                                        if ($pathScore > $score) {
                                            $score = $pathScore;
                                        }
                                    }
                                    
                                    if ($score > $bestScore && $score > 40) {
                                        $bestScore = $score;
                                        $bestMatch = $video;
                                    }
                                }
                            }
                            
                            if ($bestMatch && $bestScore > 50) {
                                $juMing = $bestMatch['name'] ?? $juMing;
                                if (!empty($bestMatch['remarks'])) {
                                    $jiShu = $bestMatch['remarks'];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            
            $response = [
                'code' => $code,
                'url' => $playUrl,
                'msg' => $playUrl,
                'jm' => $juMing,
                'js' => $jiShu,
                'time' => date('Y-m-d H:i:s'),
                'kfz' => '沫兮API - 在线视频解析'
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
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
                    'sites/list' => '获取资源站列表',
                    'sites/get' => '获取单个资源站',
                    'sites/add' => '添加资源站',
                    'sites/update' => '更新资源站',
                    'sites/delete' => '删除资源站',
                    'sites/fetch_videos' => '从资源站获取视频列表',
                    'sites/search' => '搜索指定资源站视频',
                    'sites/search_all' => '搜索所有资源站视频',
                    'sites/learn_video' => '从指定视频URL学习规则',
                    'sites/auto_learn/config' => '获取自动学习配置',
                    'sites/auto_learn/config/save' => '保存自动学习配置',
                    'sites/auto_learn/run' => '执行自动学习',
                    'sites/auto_learn/status' => '自动学习状态',
                    'official_replace/config' => '官替配置',
                    'official_replace/config/save' => '保存官替配置',
                    'official_replace/platforms' => '官替平台列表',
                    'official_replace/platform/add' => '添加官替平台',
                    'official_replace/platform/update' => '更新官替平台',
                    'official_replace/platform/delete' => '删除官替平台',
                    'official_replace/resolve' => '官替解析-完整结果',
                    'official_replace/info' => '官替解析-精简信息',
                    'moxi' => '沫兮API接口',
                    'moxi/api' => '沫兮API接口(别名)',
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
