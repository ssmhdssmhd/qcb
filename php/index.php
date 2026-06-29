<?php

require_once __DIR__ . '/src/M3U8AdSkipper.php';
require_once __DIR__ . '/src/CryptoUtil.php';
require_once __DIR__ . '/src/AuthConfig.php';
require_once __DIR__ . '/src/AuthValidator.php';
require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('X-Powered-By: m3u8-ad-skipper-php');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$authValidator = new AuthValidator();
$sqFile = __DIR__ . '/sq.txt';

if (!file_exists($sqFile)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Forbidden',
        'message' => '授权文件不存在，请联系 QQ2094332348 进行授权'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$scriptName = basename($_SERVER['SCRIPT_NAME']);
if ($scriptName === 'index.php') {
    $basePath = dirname($_SERVER['SCRIPT_NAME']);
    if ($basePath === '/' || $basePath === '\\') {
        $basePath = '';
    }
} else {
    $basePath = '';
}

$relativePath = substr($requestUri, strlen($basePath));
if ($relativePath === false) {
    $relativePath = $requestUri;
}
$relativePath = '/' . ltrim($relativePath, '/');

if ($relativePath === '/health' || $relativePath === '/api/health') {
    echo json_encode([
        'status' => 'ok',
        'service' => 'm3u8-ad-skipper',
        'version' => '1.2.0-php',
        'language' => 'PHP',
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($relativePath === '/mxjx' || $relativePath === '/api/mxjx') {
    $url = $_GET['url'] ?? '';
    if (empty($url)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Bad Request',
            'message' => '缺少 url 参数'
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
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
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;
}

if ($relativePath === '/' || $relativePath === '/api/skip' || $relativePath === '/index.php') {
    $url = $_GET['url'] ?? '';

    if (empty($url)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Bad Request',
            'message' => '缺少 url 参数',
            'example' => '/?url=https://example.com/playlist.m3u8',
            'endpoints' => [
                ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
                ['path' => '/api/skip', 'method' => 'GET', 'description' => '去广告接口'],
                ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    try {
        $startTime = microtime(true);

        $skipper = new M3U8AdSkipper();
        $result = $skipper->process($url);

        $processTime = round((microtime(true) - $startTime) * 1000);

        $removed = [];
        foreach ($result['filtered']['removedSegments'] ?? [] as $s) {
            $removed[] = [
                'uri' => $s['uri'],
                'duration' => $s['duration'],
                'title' => $s['title'] ?? '',
                'matchedRules' => array_map(function($r) {
                    return $r['name'];
                }, $s['adInfo']['matchedRules'] ?? [])
            ];
        }

        echo json_encode([
            'success' => true,
            'input' => $url,
            'processTime' => $processTime . 'ms',
            'stats' => $result['stats'],
            'playlist' => [
                'm3u8' => $result['output'],
                'format' => 'm3u8',
                'segmentCount' => count($result['filtered']['segments'] ?? [])
            ],
            'removed' => $removed
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(404);
echo json_encode([
    'error' => 'Not Found',
    'message' => '接口不存在',
    'path' => $relativePath,
    'availableEndpoints' => [
        ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/api/skip', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/mxjx', 'method' => 'GET', 'description' => '去广告m3u8输出'],
        ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
