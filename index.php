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

function sendIndexJson($data, $code = 200) {
    http_response_code($code);
    ob_clean();
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $rootDir = __DIR__;
    $requiredFiles = [
        $rootDir . '/src/M3U8AdSkipper.php',
        $rootDir . '/src/M3U8Parser.php',
        $rootDir . '/src/CryptoUtil.php',
        $rootDir . '/src/AuthConfig.php',
        $rootDir . '/src/AuthValidator.php',
        $rootDir . '/gz/EnhancedAdRuleEngine.php',
    ];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            sendIndexJson(['success' => false, 'error' => '文件缺失: ' . basename($file)], 500);
        }
        require_once $file;
    }
} catch (Throwable $e) {
    sendIndexJson([
        'success' => false,
        'error' => '初始化失败',
        'message' => $e->getMessage()
    ], 500);
}

try {
    $authValidator = new AuthValidator();
    $sqFile = __DIR__ . '/sq.php';

    if (!file_exists($sqFile) || !$authValidator->validateLocal()) {
        sendIndexJson([
            'success' => false,
            'error' => 'Forbidden',
            'message' => '授权异常，请联系 QQ2094332348 进行授权',
            'contact_qq' => '2094332348'
        ], 403);
    }
} catch (Throwable $e) {
    sendIndexJson([
        'success' => false,
        'error' => '授权验证失败',
        'message' => $e->getMessage()
    ], 500);
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = basename($_SERVER['SCRIPT_NAME']);
$basePath = '';
if ($scriptName === 'index.php') {
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

if ($relativePath === '/health' || $relativePath === '/api/health') {
    sendIndexJson([
        'status' => 'ok',
        'service' => 'm3u8-ad-skipper',
        'version' => '1.5.0-php',
        'language' => 'PHP',
        'timestamp' => date('c')
    ]);
}

if ($relativePath === '/mxjx' || $relativePath === '/api/mxjx') {
    $url = $_GET['url'] ?? '';
    if (empty($url)) {
        sendIndexJson(['success' => false, 'error' => 'Bad Request', 'message' => '缺少 url 参数'], 400);
    }

    try {
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        $parser = new M3U8Parser();
        $playlist = $parser->parse($url);

        if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
            $firstVariant = $playlist['variants'][0]['uri'] ?? '';
            if ($firstVariant) {
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;
                if (strpos($firstVariant, '/') === 0) {
                    $url = $baseUrl . $firstVariant;
                } else {
                    $url = $baseUrl . $pathDir . '/' . $firstVariant;
                }
                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
            }
        }

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

    } catch (Throwable $e) {
        sendIndexJson([
            'success' => false,
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ], 500);
    }
}

if ($relativePath === '/' || $relativePath === '/api/skip' || $relativePath === '/index.php') {
    $url = $_GET['url'] ?? '';

    if (empty($url)) {
        sendIndexJson([
            'success' => false,
            'error' => 'Bad Request',
            'message' => '缺少 url 参数',
            'example' => '/?url=https://example.com/playlist.m3u8',
            'endpoints' => [
                ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
                ['path' => '/api/skip', 'method' => 'GET', 'description' => '去广告接口'],
                ['path' => '/mxjx', 'method' => 'GET', 'description' => '去广告m3u8输出'],
                ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
            ]
        ], 400);
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

        sendIndexJson([
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
        ]);

    } catch (Throwable $e) {
        sendIndexJson([
            'success' => false,
            'error' => 'Internal Server Error',
            'message' => $e->getMessage()
        ], 500);
    }
}

sendIndexJson([
    'error' => 'Not Found',
    'message' => '接口不存在',
    'path' => $relativePath,
    'availableEndpoints' => [
        ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/api/skip', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/mxjx', 'method' => 'GET', 'description' => '去广告m3u8输出'],
        ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
    ]
], 404);
