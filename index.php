<?php
// ===== v5.14.0 启动：防篡改完整性守卫（第一个加载，保护后续所有 require） =====
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$__guardFile = __DIR__ . '/src/IntegrityGuard.php';
if (is_file($__guardFile)) {
    require_once $__guardFile;
    if (class_exists('IntegrityGuard', false)) {
        // 非严格模式：开发环境可容忍；生产部署建议把第二参数改为 true
        IntegrityGuard::boot(__DIR__, false);
    }
}
unset($__guardFile);

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

    // ===== 首次运行：本地/开发环境自动生成默认开发授权码 =====
    // 仅允许 localhost / 内网 / CLI / 回环地址自动生成；公网域名禁止，避免绕过付费授权
    $host = $_SERVER['HTTP_HOST'] ?? '';
    // 去掉端口（如 :8080, :18765）
    if (strpos($host, ':') !== false) {
        $host = preg_replace('#:\d+$#', '', $host);
    }
    $isLocalOrDev = (
        PHP_SAPI === 'cli'
        || $host === ''
        || $host === 'localhost'
        || $host === '::1'
        || (strpos($host, '127.') === 0)                    // 127.0.0.0/8 回环
        || preg_match('#^(192\.168\.|10\.|172\.(1[6-9]|2\d|3[01])\.)#', $host)  // RFC1918 内网
        || (stripos($host, '.local') !== false)              // mDNS 本地
    );

    if (!file_exists($sqFile) && $isLocalOrDev) {
        try {
            $domain = $_SERVER['HTTP_HOST'] ?? (PHP_SAPI === 'cli' ? 'localhost' : 'unknown');
            $devAuthCode = CryptoUtil::generateAuthCode($domain);
            $written = @file_put_contents(
                $sqFile,
                "<?php\nreturn '" . addslashes($devAuthCode) . "';\n",
                LOCK_EX
            );
            if ($written !== false) {
                // 重新实例化 AuthValidator 确保读取新生成的文件
                $authValidator = new AuthValidator();
            }
        } catch (Throwable $ignore) {
            // 生成失败走正常 403 流程
        }
    }

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
