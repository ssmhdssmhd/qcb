<?php
/**
 * 去广告 m3u8 播放代理
 *
 * 功能：根据缓存 ID 输出去广告后的 m3u8 内容，供前端播放器直接使用
 * 用法：clean.php?id=CACHE_ID
 */

$config = require __DIR__ . '/config.php';

// 参数校验
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    http_response_code(400);
    echo 'Cache ID is required';
    exit;
}

$cacheId = preg_replace('/[^a-f0-9]/', '', trim($_GET['id'])); // 只允许十六进制

if (strlen($cacheId) !== 16) {
    http_response_code(400);
    echo 'Invalid cache ID';
    exit;
}

$filePath = $config['cache']['dir'] . '/' . $cacheId . '.m3u8';

// 检查缓存文件是否存在且未过期
if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'Cache not found';
    exit;
}

$cacheTtl = $config['cache']['ttl'];
$fileMtime = filemtime($filePath);

if (time() - $fileMtime > $cacheTtl) {
    @unlink($filePath);
    http_response_code(404);
    echo 'Cache expired';
    exit;
}

// 读取缓存数据
$data = json_decode(file_get_contents($filePath), true);
if (!$data || !isset($data['content'])) {
    http_response_code(500);
    echo 'Cache corrupted';
    exit;
}

$cleanM3u8 = $data['content'];
$originalUrl = $data['original_url'];

// 输出 m3u8 内容
header('Content-Type: application/vnd.apple.mpegurl');
header('Cache-Control: public, max-age=3600');
header('Access-Control-Allow-Origin: *');

echo $cleanM3u8;
