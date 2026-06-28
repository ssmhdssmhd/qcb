<?php

require_once __DIR__ . '/src/M3U8AdSkipper.php';
require_once __DIR__ . '/src/AuthValidator.php';

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
if (!$authValidator->validate()) {
    echo json_encode([
        'success' => false,
        'code' => 403,
        'auth_error' => true,
        'message' => $authValidator->getErrorMessage(),
        'contact_qq' => '2094332348'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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
        'version' => '1.1.0-php',
        'language' => 'PHP',
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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
        ['path' => '/health', 'method' => 'GET', 'description' => '健康检查']
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
