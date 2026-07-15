<?php
/**
 * kz/cache.php - 缓存型 M3U8 解析入口
 *
 * 用法:
 *   1. 解析并输出可播放 M3U8:
 *      kz/cache.php?url=https://cache.xxx.xyz/Cache/qq/xxx.m3u8?vkey=xxx
 *
 *   2. 解析并返回 JSON 信息:
 *      kz/cache.php?url=xxx&mode=json
 *
 *   3. 代理 TS 分片（防盗链场景）:
 *      kz/cache.php?ts=https://cache.xxx.xyz/Cache/qq/xxx.ts
 *
 *   4. 分析 vkey 参数:
 *      kz/cache.php?vkey=xxx&mode=analyze
 *
 *   5. 带代理（分片通过本PHP代理，防防盗链）:
 *      kz/cache.php?url=xxx&proxy=1
 */

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/CacheM3u8Parser.php';

$parser = new CacheM3u8Parser();

// 模式判断
$mode = $_GET['mode'] ?? 'play';

// TS 分片代理
$tsUrl = $_GET['ts'] ?? '';
if (!empty($tsUrl)) {
    $parser->proxyTs($tsUrl);
    exit;
}

// vkey 分析
if ($mode === 'analyze' && !empty($_GET['vkey'])) {
    $vkey = $_GET['vkey'];
    $result = $parser->analyzeVkey($vkey);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// URL 解析
$url = $_GET['url'] ?? $_POST['url'] ?? '';
if (empty($url)) {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => '缺少 url 参数',
        'usage' => [
            '解析播放' => 'kz/cache.php?url=M3U8链接',
            'JSON模式' => 'kz/cache.php?url=M3U8链接&mode=json',
            '代理分片' => 'kz/cache.php?ts=分片链接',
            '分析vkey' => 'kz/cache.php?vkey=参数&mode=analyze',
            '带代理' => 'kz/cache.php?url=M3U8链接&proxy=1',
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 代理基础URL（用于分片重写）
$proxyBase = null;
if (isset($_GET['proxy']) && $_GET['proxy'] == '1') {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = dirname($requestUri);
    $basePath = $basePath === '/' ? '' : $basePath;
    $proxyBase = $scheme . '://' . $host . $basePath . '/cache.php';
}

// 执行解析
$result = $parser->parse($url, $proxyBase);

// JSON 模式
if ($mode === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 播放模式：直接输出 M3U8
if ($result['success']) {
    header('Content-Type: application/vnd.apple.mpegurl');
    header('Content-Length: ' . strlen($result['m3u8']));
    echo $result['m3u8'];
} else {
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
