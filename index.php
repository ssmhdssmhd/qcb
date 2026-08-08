<?php
// 生产环境：对外不显示错误，但完整记录错误到日志文件（避免 error_reporting(0) 让问题静默）
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
@ini_set('log_errors', 1);
@ini_set('error_log', __DIR__ . '/cache/php_error_' . date('Y-m-d') . '.log');
// 同时使用 cache 目录下自定义日志（独立于系统 php.ini 配置）
$GLOBALS['_app_error_log'] = __DIR__ . '/cache/app_error_' . date('Y-m-d') . '.log';
error_reporting(E_ALL);

// 自定义错误处理：把错误写入到项目可访问的日志文件，便于排查
set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    $typeMap = [
        E_WARNING => 'WARNING', E_NOTICE => 'NOTICE', E_DEPRECATED => 'DEPRECATED',
        E_ERROR => 'ERROR', E_PARSE => 'PARSE', E_CORE_ERROR => 'CORE_ERROR',
        E_COMPILE_ERROR => 'COMPILE_ERROR', E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING', E_USER_NOTICE => 'USER_NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR', E_ALL => 'ALL'
    ];
    $type = $typeMap[$severity] ?? 'UNKNOWN';
    $logFile = $GLOBALS['_app_error_log'] ?? (__DIR__ . '/cache/app_error_' . date('Y-m-d') . '.log');
    $line = sprintf("[%s] [%s] %s in %s:%d%s", date('Y-m-d H:i:s'), $type, $message, $file, $line, PHP_EOL);
    @file_put_contents($logFile, $line, FILE_APPEND);
    return true; // 阻止 PHP 内部错误处理继续
});

set_exception_handler(function ($e) {
    $logFile = $GLOBALS['_app_error_log'] ?? (__DIR__ . '/cache/app_error_' . date('Y-m-d') . '.log');
    $line = sprintf("[%s] [EXCEPTION] %s in %s:%d  trace=%s%s",
        date('Y-m-d H:i:s'), $e->getMessage(), $e->getFile(), $e->getLine(),
        str_replace(PHP_EOL, ' | ', $e->getTraceAsString()), PHP_EOL);
    @file_put_contents($logFile, $line, FILE_APPEND);
});

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
        $rootDir . '/gz/OfficialReplaceManager.php',
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
    $authConfig = $authValidator->getAuthConfig();
    $contactQQ = method_exists($authConfig, 'getContactQQ') ? $authConfig->getContactQQ() : '2094332348';

    if (!file_exists($sqFile) || !$authValidator->validateLocal()) {
        sendIndexJson([
            'success' => false,
            'error' => 'Forbidden',
            'message' => '授权异常，请联系 QQ' . $contactQQ . ' 进行授权',
            'contact_qq' => $contactQQ
        ], 403);
    }
} catch (Throwable $e) {
    $contactQQ = '2094332348';
    sendIndexJson([
        'success' => false,
        'error' => '授权验证失败',
        'message' => $e->getMessage(),
        'contact_qq' => $contactQQ
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
        'version' => '1.7.1-php',
        'language' => 'PHP',
        'timestamp' => date('c')
    ]);
}

if ($relativePath === '/parse' || $relativePath === '/api/parse' || $relativePath === '/jiexi') {
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    $url = $_GET['url'] ?? '';
    if (empty($url)) {
        sendIndexJson([
            'code' => 400,
            'original_url' => '',
            'parsed_url' => '',
            'message' => '缺少 url 参数',
            'example' => '/parse?url=https://example.com/playlist.m3u8',
            'endpoints' => [
                ['path' => '/parse', 'method' => 'GET', 'description' => '统一解析接口'],
                ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
                ['path' => '/mxjx', 'method' => 'GET', 'description' => '去广告m3u8输出'],
                ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
            ]
        ], 400);
    }

    $officialDomains = [
        'v.qq.com',
        'iqiyi.com',
        'youku.com',
        'mgtv.com',
        'bilibili.com',
        'sohu.com',
        'pptv.com'
    ];

    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'] ?? '';
    $isOfficialUrl = false;

    foreach ($officialDomains as $domain) {
        if (strpos($host, $domain) !== false) {
            $isOfficialUrl = true;
            break;
        }
    }

    try {
        if ($isOfficialUrl) {
            $officialReplaceMgr = new OfficialReplaceManager();
            $result = $officialReplaceMgr->resolve($url);

            if ($result['success'] && !empty($result['m3u8_url'])) {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host_name = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUriPath);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host_name . $basePath;
                $mxjxUrl = $selfUrl . '/mxjx?url=' . urlencode($result['m3u8_url']);

                sendIndexJson([
                    'code' => 200,
                    'original_url' => $url,
                    'parsed_url' => $mxjxUrl,
                    'type' => 'official_replace',
                    'platform' => $result['platform'],
                    'video_title' => $result['video_title'] ?? ''
                ], 200);
            } else {
                sendIndexJson([
                    'code' => 404,
                    'original_url' => $url,
                    'parsed_url' => '',
                    'type' => 'official_replace',
                    'message' => $result['message'] ?? '未找到匹配资源'
                ], 200);
            }
        } else {
            $skipper = new M3U8AdSkipper();
            $m3u8Result = $skipper->process($url);

            if ($m3u8Result['success'] || !empty($m3u8Result['output'])) {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host_name = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUriPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUriPath);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host_name . $basePath;
                $mxjxUrl = $selfUrl . '/mxjx?url=' . urlencode($url);

                sendIndexJson([
                    'code' => 200,
                    'original_url' => $url,
                    'parsed_url' => $mxjxUrl,
                    'type' => 'ad_skip',
                    'stats' => $m3u8Result['stats'] ?? []
                ], 200);
            } else {
                sendIndexJson([
                    'code' => 500,
                    'original_url' => $url,
                    'parsed_url' => '',
                    'type' => 'ad_skip',
                    'message' => '解析失败'
                ], 500);
            }
        }
    } catch (Throwable $e) {
        sendIndexJson([
            'code' => 500,
            'original_url' => $url,
            'parsed_url' => '',
            'message' => $e->getMessage()
        ], 500);
    }
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

        $enhancedEngine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $enhancedEngine->setDomain($domain);
        $skipper->setRuleEngine($enhancedEngine);

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
                ['path' => '/parse', 'method' => 'GET', 'description' => '统一解析接口（自动判断官解/直连）'],
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
        ['path' => '/parse', 'method' => 'GET', 'description' => '统一解析接口（自动判断官解/直连）'],
        ['path' => '/', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/api/skip', 'method' => 'GET', 'description' => '去广告接口'],
        ['path' => '/mxjx', 'method' => 'GET', 'description' => '去广告m3u8输出'],
        ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
    ]
], 200);
