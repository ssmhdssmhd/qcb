<?php

require_once __DIR__ . '/src/M3U8AdSkipper.php';
require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/gz/DomainRuleManager.php';
require_once __DIR__ . '/src/UpdateManager.php';
require_once __DIR__ . '/src/AuthValidator.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$authValidator = new AuthValidator();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

$publicActions = ['auth/status', 'auth/test', 'auth/getconfig', 'auth/saveconfig', 'auth/setlocal', 'mxjx'];
$needAuth = !in_array($action, $publicActions);

if ($needAuth && !$authValidator->validate()) {
    sendJson([
        'success' => false,
        'code' => 403,
        'auth_error' => true,
        'message' => $authValidator->getErrorMessage(),
        'contact_qq' => '2094332348'
    ], 403);
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
        $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
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
            $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);

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
        try {
            $updateManager = new UpdateManager();
            $current = $updateManager->getCurrentVersion();
            $localSha = $updateManager->getLocalSha();
            sendJson([
                'success' => true,
                'current' => $current,
                'local_sha' => $localSha,
                'local_short_sha' => $localSha ? substr($localSha, 0, 7) : null
            ]);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/check':
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->checkUpdate();
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/backup':
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->createBackup();
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/backuplist':
        try {
            $updateManager = new UpdateManager();
            $list = $updateManager->getBackupList();
            sendJson(['success' => true, 'backups' => $list]);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/deletebackup':
        $filename = $_GET['filename'] ?? $_POST['filename'] ?? '';
        if (empty($filename)) {
            sendJson(['success' => false, 'message' => '缺少 filename 参数'], 400);
        }
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->deleteBackup($filename);
            sendJson(['success' => $result, 'message' => $result ? '删除成功' : '删除失败']);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/restore':
        $filename = $_GET['filename'] ?? $_POST['filename'] ?? '';
        if (empty($filename)) {
            sendJson(['success' => false, 'message' => '缺少 filename 参数'], 400);
        }
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->restoreBackup($filename);
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/download':
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->downloadUpdate();
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/apply':
        $filename = $_GET['filename'] ?? $_POST['filename'] ?? '';
        if (empty($filename)) {
            sendJson(['success' => false, 'message' => '缺少 filename 参数'], 400);
        }
        try {
            $updateManager = new UpdateManager();
            $zipFile = __DIR__ . '/backups/' . basename($filename);
            $result = $updateManager->applyUpdate($zipFile);
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'update/doupdate':
        try {
            $updateManager = new UpdateManager();
            $result = $updateManager->doUpdate();
            sendJson($result);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/status':
        try {
            $validator = new AuthValidator();
            $status = $validator->validate();
            $authConfig = new AuthConfig();
            $config = $authConfig->getConfig();
            sendJson([
                'success' => true,
                'authenticated' => $status,
                'error' => $status ? null : $validator->getLastError(),
                'message' => $status ? '授权正常' : $validator->getErrorMessage(),
                'config' => [
                    'enabled' => $config['enabled'],
                    'local_file' => $config['local_file'],
                    'server_ip' => $config['server']['ip'],
                    'server_port' => $config['server']['port'],
                    'primary_file' => $config['server']['primary_file'],
                    'backup_file' => $config['server']['backup_file'],
                    'contact_qq' => $config['contact']['qq']
                ],
                'local_exists' => file_exists($authConfig->getLocalFilePath()),
                'local_content' => $validator->getLocalAuthContent()
            ]);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/getconfig':
        try {
            $authConfig = new AuthConfig();
            sendJson([
                'success' => true,
                'config' => $authConfig->getConfig()
            ]);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/saveconfig':
        $input = getInputJson();
        try {
            $authConfig = new AuthConfig();
            $config = $input['config'] ?? [];
            if (isset($config['enabled'])) {
                $authConfig->set('enabled', $config['enabled']);
            }
            if (isset($config['local_file'])) {
                $authConfig->set('local_file', $config['local_file']);
            }
            if (isset($config['server'])) {
                $server = $config['server'];
                if (isset($server['ip'])) $authConfig->set('server.ip', $server['ip']);
                if (isset($server['port'])) $authConfig->set('server.port', intval($server['port']));
                if (isset($server['protocol'])) $authConfig->set('server.protocol', $server['protocol']);
                if (isset($server['primary_file'])) $authConfig->set('server.primary_file', $server['primary_file']);
                if (isset($server['backup_file'])) $authConfig->set('server.backup_file', $server['backup_file']);
            }
            if (isset($config['validation'])) {
                $val = $config['validation'];
                if (isset($val['check_local_first'])) $authConfig->set('validation.check_local_first', $val['check_local_first']);
                if (isset($val['check_remote'])) $authConfig->set('validation.check_remote', $val['check_remote']);
                if (isset($val['check_timestamp'])) $authConfig->set('validation.check_timestamp', $val['check_timestamp']);
                if (isset($val['timestamp_tolerance'])) $authConfig->set('validation.timestamp_tolerance', intval($val['timestamp_tolerance']));
            }
            if (isset($config['contact'])) {
                $contact = $config['contact'];
                if (isset($contact['qq'])) $authConfig->set('contact.qq', $contact['qq']);
                if (isset($contact['message'])) $authConfig->set('contact.message', $contact['message']);
            }
            sendJson(['success' => true, 'message' => '配置保存成功']);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/setlocal':
        $input = getInputJson();
        $content = $input['content'] ?? '';
        if (empty($content)) {
            sendJson(['success' => false, 'message' => '授权内容不能为空'], 400);
        }
        try {
            $validator = new AuthValidator();
            $result = $validator->saveLocalAuth($content);
            if ($result) {
                sendJson(['success' => true, 'message' => '本地授权文件已更新']);
            } else {
                sendJson(['success' => false, 'message' => '写入失败'], 500);
            }
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/test':
        $input = getInputJson();
        $ip = $input['ip'] ?? '114.134.184.91';
        $port = $input['port'] ?? 9001;
        $file = $input['file'] ?? 'sq.txt';
        $protocol = $input['protocol'] ?? 'http';
        try {
            $validator = new AuthValidator();
            $result = $validator->testConnection($ip, $port, $file, $protocol);
            if ($result === false) {
                sendJson(['success' => false, 'message' => '连接失败']);
            } else {
                sendJson([
                    'success' => true,
                    'message' => '连接成功',
                    'content' => $result,
                    'content_length' => strlen($result)
                ]);
            }
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
        break;

    case 'auth/generate':
        $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
        try {
            $validator = new AuthValidator();
            $plain = $validator->generateAuthCode($domain);
            $encrypted = $validator->generateEncryptedAuth($domain);
            sendJson([
                'success' => true,
                'plain' => json_encode($plain, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'encrypted' => $encrypted,
                'code' => $plain['code'],
                'timestamp' => $plain['timestamp']
            ]);
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
                'update/backup' => '创建备份',
                'update/backuplist' => '备份列表',
                'update/deletebackup' => '删除备份',
                'update/restore' => '恢复备份',
                'update/download' => '下载更新',
                'update/apply' => '应用更新',
                'update/doupdate' => '一键更新'
            ]
        ], 400);
        break;
}
