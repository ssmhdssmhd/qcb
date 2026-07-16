<?php
/**
 * 去广告 m3u8 播放代理
 *
 * 功能：根据缓存 ID 输出去广告后的 m3u8 内容，供前端播放器直接使用
 * 用法：clean.php?id=CACHE_ID
 */

$config = require __DIR__ . '/config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Range, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Cache ID is required';
    exit;
}

$cacheId = preg_replace('/[^a-f0-9]/', '', trim($_GET['id']));

if (strlen($cacheId) !== 16) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Invalid cache ID';
    exit;
}

$filePath = $config['cache']['dir'] . '/' . $cacheId . '.m3u8';

if (!file_exists($filePath)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Cache not found';
    exit;
}

$cacheTtl = $config['cache']['ttl'];
$fileMtime = filemtime($filePath);

if (time() - $fileMtime > $cacheTtl) {
    @unlink($filePath);
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Cache expired';
    exit;
}

$data = json_decode(file_get_contents($filePath), true);
if (!$data || !isset($data['content'])) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Cache corrupted';
    exit;
}

$cleanM3u8 = $data['content'];
$originalUrl = $data['original_url'] ?? '';

header('Content-Type: application/vnd.apple.mpegurl');
header('Cache-Control: public, max-age=3600');
header('ETag: "' . md5($cleanM3u8) . '"');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileMtime) . ' GMT');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === md5($cleanM3u8)) {
    http_response_code(304);
    exit;
}

echo $cleanM3u8;
