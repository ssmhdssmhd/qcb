<?php
/**
 * 资源站去广告脚本模板
 * 使用方法：将此文件复制为资源站名称.php，修改下方的配置即可使用
 * 调用方式：http://你的域名/lz/resource_scripts/资源站名称.php?url=视频链接
 */

$SITE_CONFIG = [
    'site_name' => '资源站名称',
    'site_url' => 'https://example.com',
    'api_url' => 'https://example.com/api.php/provide/vod/',
    'type' => 'maccms',
    'default_domain' => 'v.example.com',
    'custom_rules' => [
        'duration_rules' => [],
        'discontinuity_rules' => [],
        'sequence_jump_rules' => [],
        'filename_patterns' => []
    ]
];

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

function sendSiteJson($data, $code = 200) {
    http_response_code($code);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$rootDir = dirname(dirname(__DIR__));

$requiredFiles = [
    $rootDir . '/src/M3U8AdSkipper.php',
    $rootDir . '/src/M3U8Parser.php',
    $rootDir . '/gz/EnhancedAdRuleEngine.php',
    $rootDir . '/gz/DomainRuleManager.php',
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        sendSiteJson([
            'code' => 500,
            'msg' => '文件缺失: ' . basename($file),
            'data' => null
        ], 500);
    }
    require_once $file;
}

$url = $_GET['url'] ?? '';
$format = $_GET['format'] ?? 'json';

if (empty($url)) {
    sendSiteJson([
        'code' => 400,
        'msg' => '缺少 url 参数',
        'data' => null,
        'site' => $SITE_CONFIG['site_name'],
        'example' => $_SERVER['PHP_SELF'] . '?url=https://example.com/playlist.m3u8',
        'usage' => [
            '去广告(JSON)' => $_SERVER['PHP_SELF'] . '?url=视频链接',
            '去广告(M3U8)' => $_SERVER['PHP_SELF'] . '?url=视频链接&format=m3u8',
            '搜索视频' => $_SERVER['PHP_SELF'] . '?action=search&wd=关键词'
        ]
    ], 400);
}

$action = $_GET['action'] ?? 'skip';

function resolveMasterPlaylistSite($url) {
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
                    return $baseUrl . $pathDir . '/' . ltrim($firstVariant, '/');
                }
            }
        }
    } catch (Exception $e) {
    }
    return $url;
}

try {
    switch ($action) {
        case 'search':
            $wd = $_GET['wd'] ?? '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            if (empty($wd)) {
                sendSiteJson(['code' => 400, 'msg' => '缺少 wd 参数', 'data' => null], 400);
            }
            $apiUrl = $SITE_CONFIG['api_url'];
            $searchUrl = $apiUrl . (strpos($apiUrl, '?') === false ? '?' : '&') . 'ac=videolist&wd=' . urlencode($wd) . '&pg=' . $page . '&limit=' . $limit;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $searchUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($response === false || $httpCode != 200) {
                sendSiteJson(['code' => 500, 'msg' => '搜索请求失败', 'data' => null], 500);
            }
            $data = json_decode($response, true);
            sendSiteJson([
                'code' => 200,
                'msg' => '搜索成功',
                'data' => [
                    'site' => $SITE_CONFIG['site_name'],
                    'keyword' => $wd,
                    'page' => $page,
                    'result' => $data
                ]
            ]);
            break;

        case 'skip':
        default:
            $startTime = microtime(true);
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $mediaUrl = resolveMasterPlaylistSite($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
                $url = $mediaUrl;
            }

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

            $result = $skipper->processWithSafeguard($url);
            $stats = $result['stats'] ?? [];
            $processTime = round((microtime(true) - $startTime) * 1000);

            if ($format === 'm3u8') {
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
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('X-Site: ' . $SITE_CONFIG['site_name']);
                header('X-Process-Time: ' . $processTime . 'ms');
                ob_clean();
                echo $newM3U8Content;
                exit;
            }

            sendSiteJson([
                'code' => 200,
                'msg' => '解析成功',
                'data' => [
                    'site' => $SITE_CONFIG['site_name'],
                    'original_url' => $_GET['url'] ?? '',
                    'media_url' => $url,
                    'domain' => $domain,
                    'process_time' => $processTime . 'ms',
                    'safeguard_triggered' => !empty($result['safeguardTriggered']),
                    'safeguard_reason' => $result['safeguardReason'] ?? '',
                    'safeguard_method' => $result['safeguardMethod'] ?? '',
                    'stats' => [
                        'total_segments' => (int)($stats['totalSegments'] ?? 0),
                        'ad_segments' => (int)($stats['adSegments'] ?? $stats['removedSegments'] ?? 0),
                        'kept_segments' => (int)($stats['keptSegments'] ?? 0),
                        'original_duration' => (float)($stats['originalDuration'] ?? 0),
                        'filtered_duration' => (float)($stats['filteredDuration'] ?? 0),
                        'saved_duration' => (float)($stats['savedDuration'] ?? 0),
                        'ad_percentage' => (float)($stats['adPercentage'] ?? 0)
                    ]
                ]
            ]);
            break;
    }

} catch (Throwable $e) {
    sendSiteJson([
        'code' => 500,
        'msg' => '处理失败: ' . $e->getMessage(),
        'data' => null,
        'site' => $SITE_CONFIG['site_name'],
        'error' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}
