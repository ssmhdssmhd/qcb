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
    
    return false;
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
    $browserName = getBrowserName();
    echo <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8 播放器 - QCB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #000;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .header {
            padding: 12px 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border-bottom: 1px solid #2a2a4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }
        .browser-tag {
            background: #3b82f6;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .video-container {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
        }
        video {
            width: 100%;
            height: 100%;
            max-height: calc(100vh - 60px);
            object-fit: contain;
        }
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #999;
        }
        .loading .spinner {
            width: 48px;
            height: 48px;
            border: 3px solid #333;
            border-top-color: #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 16px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .controls {
            padding: 10px 20px;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            gap: 12px;
            justify-content: center;
            border-top: 1px solid #222;
        }
        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-secondary {
            background: #374151;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .info-bar {
            padding: 8px 20px;
            background: #111;
            color: #666;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎬 M3U8 无广告播放器</h1>
        <span class="browser-tag">{$browserName}</span>
    </div>
    
    <div class="video-container">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>正在加载视频...</p>
        </div>
        <video id="player" controls autoplay playsinline></video>
    </div>
    
    <div class="controls">
        <button class="btn btn-secondary" onclick="document.getElementById('player').play()">▶️ 播放</button>
        <button class="btn btn-secondary" onclick="document.getElementById('player').pause()">⏸️ 暂停</button>
        <button class="btn btn-primary" onclick="copyLink()">📋 复制播放链接</button>
    </div>
    
    <div class="info-bar">
        <span>缓存ID: {$cacheId}</span>
        <span>去广告已生效</span>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8/dist/hls.min.js"></script>
    <script>
        var video = document.getElementById('player');
        var loading = document.getElementById('loading');
        var hls = new Hls({
            enableWorker: true,
            lowInitialPlaylist: true,
        });
        
        hls.loadSource('{$m3u8Url}');
        hls.attachMedia(video);
        
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            loading.style.display = 'none';
            video.play().catch(function() {});
        });
        
        hls.on(Hls.Events.ERROR, function(event, data) {
            if (data.fatal) {
                loading.innerHTML = '<p>❌ 加载失败，请刷新重试</p>';
            }
        });
        
        function copyLink() {
            navigator.clipboard.writeText('{$m3u8Url}').then(function() {
                alert('✅ 链接已复制到剪贴板');
            }).catch(function() {
                alert('复制失败，请手动复制');
            });
        }
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
