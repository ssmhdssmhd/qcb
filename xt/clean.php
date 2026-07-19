<?php
/**
 * 去广告 m3u8 播放代理
 *
 * 功能：根据缓存 ID 输出去广告后的 m3u8 内容，供前端播放器直接使用
 * 用法：clean.php?id=CACHE_ID
 *      clean.php?id=CACHE_ID&player=1  (在浏览器中显示播放器页面)
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

function shouldShowPlayerPage(): bool
{
    if (isset($_GET['player']) && $_GET['player'] === '1') {
        return true;
    }
    
    $acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';
    if (strpos($acceptHeader, 'text/html') === false) {
        return false;
    }
    
    if (strpos($acceptHeader, 'application/vnd.apple.mpegurl') !== false) {
        return false;
    }
    
    if (strpos($acceptHeader, 'application/x-mpegurl') !== false) {
        return false;
    }
    
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    
    $playerKeywords = [
        'hls.js',
        'videojs',
        'video.js',
        'jwplayer',
        'flowplayer',
        'clappr',
        'mediaelement',
        'shaka',
        'exoplayer',
        'avplayer',
        'vlc',
        'mpv',
        'ffmpeg',
        'curl',
        'wget',
        'python',
        'node',
        'okhttp',
        'nsurlsession',
    ];
    
    foreach ($playerKeywords as $keyword) {
        if (strpos($userAgent, $keyword) !== false) {
            return false;
        }
    }
    
    $rangeHeader = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : '';
    if (!empty($rangeHeader)) {
        return false;
    }
    
    return true;
}

function getBrowserName(): string
{
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    
    if (strpos($userAgent, 'edg/') !== false) {
        return 'Microsoft Edge';
    } elseif (strpos($userAgent, 'chrome/') !== false && strpos($userAgent, 'edg/') === false) {
        return 'Google Chrome';
    } elseif (strpos($userAgent, 'firefox/') !== false) {
        return 'Mozilla Firefox';
    } elseif (strpos($userAgent, 'safari/') !== false && strpos($userAgent, 'chrome/') === false) {
        return 'Apple Safari';
    } elseif (strpos($userAgent, 'opera/') !== false || strpos($userAgent, 'opr/') !== false) {
        return 'Opera';
    } elseif (strpos($userAgent, 'brave/') !== false) {
        return 'Brave';
    } elseif (strpos($userAgent, 'vivaldi/') !== false) {
        return 'Vivaldi';
    } elseif (strpos($userAgent, 'msie') !== false || strpos($userAgent, 'trident/') !== false) {
        return 'Internet Explorer';
    }
    
    return 'Unknown Browser';
}

if (shouldShowPlayerPage()) {
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
        . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $m3u8Url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . $_SERVER['HTTP_HOST']
        . strtok($_SERVER['REQUEST_URI'], '?')
        . '?id=' . $cacheId;
    
    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>M3U8 播放器</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #000; min-height: 100vh; overflow: hidden; }
        video { width: 100%; height: 100vh; object-fit: contain; }
    </style>
</head>
<body>
    <video id="player" controls autoplay playsinline></video>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8/dist/hls.min.js"></script>
    <script>
        var video = document.getElementById('player');
        var hls = new Hls({ enableWorker: true, lowInitialPlaylist: true });
        hls.loadSource('{$m3u8Url}');
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() { video.play().catch(function() {}); });
        hls.on(Hls.Events.ERROR, function(event, data) { if (data.fatal) { video.innerHTML = '<p style="color:#fff;text-align:center;">加载失败</p>'; } });
    </script>
</body>
</html>
HTML;
    exit;
}

header('Content-Type: application/vnd.apple.mpegurl');
header('Cache-Control: public, max-age=3600');
header('ETag: "' . md5($cleanM3u8) . '"');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $fileMtime) . ' GMT');

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH'], '"') === md5($cleanM3u8)) {
    http_response_code(304);
    exit;
}

echo $cleanM3u8;
