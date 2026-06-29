<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return;
    $errors = [
        'error' => true,
        'type' => $errno,
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ];
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(['success' => false, 'message' => $errstr . ' (' . basename($errfile) . ':' . $errline . ')', 'error_detail' => $errors]);
    exit;
}
set_error_handler('jsonErrorHandler');

function jsonFatalHandler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode([
            'success' => false,
            'message' => $error['message'] . ' (' . basename($error['file']) . ':' . $error['line'] . ')',
            'fatal_error' => true
        ]);
    }
}
register_shutdown_function('jsonFatalHandler');

require_once __DIR__ . '/src/M3U8AdSkipper.php';
require_once __DIR__ . '/src/UpdateManager.php';
require_once __DIR__ . '/src/CryptoUtil.php';
require_once __DIR__ . '/src/AuthConfig.php';
require_once __DIR__ . '/src/AuthValidator.php';
require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/gz/DomainRuleManager.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = basename($_SERVER['SCRIPT_NAME']);
$basePath = '';

if ($scriptName === 'mx.php') {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
}

$relativePath = substr($requestUri, strlen($basePath));
if ($relativePath === false) {
    $relativePath = $requestUri;
}
$relativePath = '/' . ltrim($relativePath, '/');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$ruleManager = new DomainRuleManager();
$updateManager = new UpdateManager();
$authValidator = new AuthValidator();

function sendJson($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

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

switch ($action) {
    case 'analyze':
        $url = $_GET['url'] ?? $_POST['url'] ?? '';
        if (empty($url)) {
            sendJson(['success' => false, 'message' => '缺少 url 参数'], 400);
        }

        try {
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
                sendJson(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);

            $domainRules = $ruleManager->getRules($domain);
            $hasDomainRules = $domainRules !== null;

            sendJson([
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

        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'rules/list':
        $rules = $ruleManager->getAllRules();
        sendJson(['success' => true, 'rules' => $rules]);
        break;

    case 'rules/get':
        $domain = $_GET['domain'] ?? '';
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $rules = $ruleManager->getRules($domain);
        if ($rules === null) {
            sendJson(['success' => false, 'message' => '规则不存在'], 404);
        }
        sendJson(['success' => true, 'domain' => $domain, 'rules' => $rules]);
        break;

    case 'rules/save':
        $input = getInputJson();
        $domain = $input['domain'] ?? '';
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }

        $ruleData = $input['rules'] ?? [];
        $result = $ruleManager->saveRules($domain, $ruleData);

        if ($result) {
            sendJson(['success' => true, 'message' => '规则保存成功', 'domain' => $domain]);
        } else {
            sendJson(['success' => false, 'message' => '规则保存失败'], 500);
        }
        break;

    case 'rules/delete':
        $input = getInputJson();
        $domain = $input['domain'] ?? '';
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $result = $ruleManager->deleteRules($domain);
        if ($result) {
            sendJson(['success' => true, 'message' => '规则删除成功']);
        } else {
            sendJson(['success' => false, 'message' => '规则删除失败或不存在'], 400);
        }
        break;

    case 'rules/generate':
        $url = $_GET['url'] ?? $_POST['url'] ?? '';
        if (empty($url)) {
            sendJson(['success' => false, 'message' => '缺少 url 参数'], 400);
        }

        try {
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
                sendJson(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            $generatedRules = $ruleManager->createFromAnalysis($domain, $analysis);
            $generatedRules['sample_url'] = $url;

            sendJson([
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

        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'skip':
        $url = $_GET['url'] ?? '';
        if (empty($url)) {
            sendJson(['success' => false, 'message' => '缺少 url 参数'], 400);
        }

        try {
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $skipper = new M3U8AdSkipper();

            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true
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
            $selfUrl = $scheme . '://' . $host . $basePath;
            $mxjxUrl = $selfUrl . '/admin_api.php?action=mxjx&url=' . urlencode($url);

            sendJson([
                'success' => true,
                'url' => $url,
                'mxjx' => $mxjxUrl,
                'stats' => $result['stats']
            ]);

        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'mxjx':
        $url = $_GET['url'] ?? '';
        if (empty($url)) {
            sendJson(['success' => false, 'message' => '缺少 url 参数'], 400);
        }

        try {
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
                'checkDiscontinuity' => true
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
            echo $newM3U8Content;
            exit;

        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/version':
        sendJson([
            'success' => true,
            'current_version' => $updateManager->getCurrentVersion(),
            'version_file' => file_exists(__DIR__ . '/version.php') ? trim(include __DIR__ . '/version.php') : ''
        ]);
        break;

    case 'update/check':
        $result = $updateManager->checkUpdate();
        sendJson($result);
        break;

    case 'update/integrity':
        $result = $updateManager->checkIntegrity();
        sendJson($result);
        break;

    case 'update/backup/list':
        $backups = $updateManager->getBackupList();
        sendJson([
            'success' => true,
            'backups' => $backups
        ]);
        break;

    case 'update/backup/create':
        $result = $updateManager->createBackup();
        sendJson($result);
        break;

    case 'update/backup/restore':
        $input = getInputJson();
        $filename = $input['filename'] ?? $_GET['filename'] ?? '';
        if (empty($filename)) {
            sendJson(['success' => false, 'message' => '缺少 filename 参数'], 400);
        }
        $result = $updateManager->restoreBackup($filename);
        sendJson($result);
        break;

    case 'update/backup/delete':
        $input = getInputJson();
        $filename = $input['filename'] ?? $_GET['filename'] ?? '';
        if (empty($filename)) {
            sendJson(['success' => false, 'message' => '缺少 filename 参数'], 400);
        }
        $result = $updateManager->deleteBackup($filename);
        sendJson($result);
        break;

    case 'update/download':
        $result = $updateManager->downloadUpdate();
        sendJson($result);
        break;

    case 'auth/info':
        $info = $authValidator->getAuthInfo();
        $info['success'] = true;
        $info['contact'] = 'QQ2094332348';
        sendJson($info);
        break;

    case 'auth/validate':
        $localValid = $authValidator->validateLocal();
        $remoteValid = $authValidator->validateRemote();
        sendJson([
            'success' => true,
            'local_valid' => $localValid,
            'remote_valid' => $remoteValid,
            'all_valid' => $localValid && $remoteValid,
            'error' => $authValidator->getLastError()
        ]);
        break;

    case 'auth/config/get':
        $config = $authValidator->getAuthConfig()->getConfig();
        sendJson([
            'success' => true,
            'config' => $config
        ]);
        break;

    case 'auth/config/save':
        $input = getInputJson();
        $config = $input['config'] ?? [];
        $result = $authValidator->getAuthConfig()->setConfig($config);
        sendJson([
            'success' => $result,
            'message' => $result ? '配置保存成功' : '配置保存失败'
        ]);
        break;

    case 'auth/set':
        $input = getInputJson();
        $authCode = $input['auth_code'] ?? '';
        if (empty($authCode)) {
            sendJson(['success' => false, 'message' => '缺少 auth_code 参数'], 400);
        }
        $result = $authValidator->setAuthCode($authCode);
        sendJson([
            'success' => $result,
            'message' => $result ? '授权码设置成功' : '授权码设置失败'
        ]);
        break;

    case 'auth/generate':
        $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $authCode = $authValidator->generateAuthCode($domain);
        sendJson([
            'success' => true,
            'domain' => $domain,
            'auth_code' => $authCode
        ]);
        break;

    default:
        sendJson([
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
                'update/integrity' => '完整性检查（授权验证+文件验证）',
                'update/backup/list' => '备份列表',
                'update/backup/create' => '创建备份',
                'update/backup/restore' => '恢复备份',
                'update/backup/delete' => '删除备份',
                'update/download' => '下载更新',
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
