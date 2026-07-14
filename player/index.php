<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$url = $_GET['url'] ?? '';
$playerType = $_GET['player'] ?? '';

$configFile = __DIR__ . '/../gz/player_config.php';
$localConfigFile = __DIR__ . '/player_config.php';

$playerConfig = [
    'player' => 'dplayer',
    'autoplay' => false,
    'preload' => 'auto',
    'api_base_url' => '',
    'default_player' => 'dplayer',
    'commercial_players' => [],
    'players' => [],
];

if (file_exists($localConfigFile)) {
    $localConfig = require $localConfigFile;
    if (is_array($localConfig)) {
        $playerConfig = array_merge($playerConfig, $localConfig);
    }
}

if (file_exists($configFile)) {
    $fileConfig = require $configFile;
    if (is_array($fileConfig)) {
        $playerConfig = array_merge($playerConfig, $fileConfig);
    }
}

if (!empty($playerConfig['api_base_url'])) {
    $baseUrl = rtrim($playerConfig['api_base_url'], '/');
} else {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = dirname($requestUri);
    $basePath = $basePath === '/' || $basePath === '\\' ? '' : $basePath;
    $baseUrl = $scheme . '://' . $host . $basePath;
}

$apiUrl = $baseUrl . '/../mx.php?action=mxjx&url=';
$officialReplaceUrl = $baseUrl . '/../mx.php?action=official_replace/info&url=';

if (!empty($playerType)) {
    $playerConfig['player'] = $playerType;
}

$videoUrl = '';
$originalUrl = $url;
$isOfficialUrl = false;

$officialDomains = [
    'v.qq.com',
    'iqiyi.com',
    'youku.com',
    'mgtv.com',
    'bilibili.com',
    'sohu.com',
    'pptv.com'
];

if (!empty($url)) {
    $parsedUrl = parse_url($url);
    $host = $parsedUrl['host'] ?? '';

    foreach ($officialDomains as $domain) {
        if (strpos($host, $domain) !== false) {
            $isOfficialUrl = true;
            break;
        }
    }

    if ($isOfficialUrl) {
        $videoUrl = $officialReplaceUrl . urlencode($url);
    } else {
        $videoUrl = $apiUrl . urlencode($url);
    }
}

$allPlayers = $playerConfig['players'] ?? [];
if (empty($allPlayers)) {
    $allPlayers = [
        'dplayer' => ['name' => 'DPlayer', 'category' => '开源'],
        'videojs' => ['name' => 'Video.js', 'category' => '开源'],
        'shaka' => ['name' => 'Shaka Player', 'category' => '开源'],
        'clappr' => ['name' => 'Clappr', 'category' => '开源'],
        'dashjs' => ['name' => 'dash.js', 'category' => '开源'],
        'hlsjs' => ['name' => 'hls.js 原生', 'category' => '开源'],
        'muiplayer' => ['name' => 'MuiPlayer', 'category' => '开源'],
        'artplayer' => ['name' => 'ArtPlayer', 'category' => '开源'],
        'nplayer' => ['name' => 'NPlayer', 'category' => '开源'],
        'native' => ['name' => '原生 Video', 'category' => '系统'],
        'jwplayer' => ['name' => 'JW Player', 'category' => '商业', 'license_required' => true],
        'bitmovin' => ['name' => 'Bitmovin', 'category' => '商业', 'license_required' => true],
        'theoplayer' => ['name' => 'THEOplayer', 'category' => '商业', 'license_required' => true],
        'flowplayer' => ['name' => 'Flowplayer', 'category' => '商业', 'license_required' => true],
        'radiant' => ['name' => 'Radiant Media Player', 'category' => '商业', 'license_required' => true],
        'nexplayer' => ['name' => 'NexPlayer', 'category' => '商业', 'license_required' => true],
        'castlabs' => ['name' => 'castLabs PRESTOplay', 'category' => '商业', 'license_required' => true],
        'visualon' => ['name' => 'VisualON', 'category' => '商业', 'license_required' => true],
    ];
}

$currentPlayer = $playerConfig['player'] ?? 'dplayer';
$currentPlayerName = $allPlayers[$currentPlayer]['name'] ?? $currentPlayer;
$commercialConfig = $playerConfig['commercial_players'] ?? [];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线视频播放器 - <?php echo $currentPlayerName; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.13/dist/hls.min.js"></script>
    <?php if ($currentPlayer === 'dplayer'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
    <?php elseif ($currentPlayer === 'videojs'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/video.js@8.12.0/dist/video-js.min.css">
    <script src="https://cdn.jsdelivr.net/npm/video.js@8.12.0/dist/video.min.js"></script>
    <?php elseif ($currentPlayer === 'shaka'): ?>
    <script src="https://cdn.jsdelivr.net/npm/shaka-player@4.10.8/dist/shaka-player.compiled.js"></script>
    <?php elseif ($currentPlayer === 'clappr'): ?>
    <script src="https://cdn.jsdelivr.net/npm/clappr@0.4.26/dist/clappr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.13/dist/hls.min.js"></script>
    <?php elseif ($currentPlayer === 'dashjs'): ?>
    <script src="https://cdn.jsdelivr.net/npm/dashjs@4.7.4/dist/dash.all.min.js"></script>
    <?php elseif ($currentPlayer === 'hlsjs'): ?>
    <?php elseif ($currentPlayer === 'muiplayer'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mui-player@1.8.2/dist/mui-player.min.css">
    <script src="https://cdn.jsdelivr.net/npm/mui-player@1.8.2/dist/mui-player.min.js"></script>
    <?php elseif ($currentPlayer === 'artplayer'): ?>
    <script src="https://cdn.jsdelivr.net/npm/artplayer@5.1.4/dist/artplayer.min.js"></script>
    <?php elseif ($currentPlayer === 'nplayer'): ?>
    <script src="https://cdn.jsdelivr.net/npm/nplayer@1.0.15/dist/NPlayer.min.js"></script>
    <?php elseif ($currentPlayer === 'jwplayer'): ?>
    <script src="https://cdn.jwplayer.com/libraries/<?php echo $commercialConfig['jwplayer']['license_key'] ?? 'demo'; ?>.js"></script>
    <?php elseif ($currentPlayer === 'bitmovin'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bitmovin-player@8.152.0/bitmovinplayer-ui.css">
    <script src="https://cdn.jsdelivr.net/npm/bitmovin-player@8.152.0/bitmovinplayer.min.js"></script>
    <?php elseif ($currentPlayer === 'theoplayer'): ?>
    <link rel="stylesheet" href="https://cdn.theoplayer.com/dash/theoplayer/ui.css">
    <script type="text/javascript" src="https://cdn.theoplayer.com/dash/theoplayer/THEOplayer.js"></script>
    <?php elseif ($currentPlayer === 'flowplayer'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flowplayer@7.2.7/dist/skin/skin.css">
    <script src="https://cdn.jsdelivr.net/npm/flowplayer@7.2.7/dist/flowplayer.min.js"></script>
    <?php elseif ($currentPlayer === 'radiant'): ?>
    <script src="https://cdn.radiantmediaplayer.com/latest/8/rmp.min.js"></script>
    <?php elseif ($currentPlayer === 'native'): ?>
    <?php endif; ?>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #0a0a0a;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 20px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 600;
            background: linear-gradient(90deg, #00d4ff, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .subtitle {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 4px;
        }

        .player-switch {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .player-switch label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .player-switch select {
            padding: 8px 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }

        .player-switch select:focus {
            border-color: #00d4ff;
        }

        .player-switch select option {
            background: #1a1a2e;
            color: #fff;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .player-wrapper {
            background: #1a1a2e;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
        }

        #videoPlayer {
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000 !important;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
        }

        #videoPlayer .dplayer {
            width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
            position: absolute !important;
            top: 0;
            left: 0;
        }

        #videoPlayer .dplayer-video-wrap {
            width: 100% !important;
            height: 100% !important;
            background: #000 !important;
        }

        #videoPlayer .dplayer-video-wrap .dplayer-video {
            object-fit: contain !important;
            width: 100% !important;
            height: 100% !important;
            background: #000 !important;
            display: block !important;
        }
        
        #videoPlayer .dplayer-loading {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        
        #videoPlayer .dplayer-loading-icon {
            width: 50px !important;
            height: 50px !important;
        }

        #videoPlayer .dplayer-bar-wrap {
            position: relative !important;
        }

        .info-panel {
            background: #1a1a2e;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .info-panel h2 {
            font-size: 16px;
            margin-bottom: 16px;
            color: #00d4ff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-panel h2::before {
            content: '';
            width: 4px;
            height: 16px;
            background: linear-gradient(180deg, #00d4ff, #7c3aed);
            border-radius: 2px;
        }

        .url-display {
            background: #0f0f1a;
            border-radius: 8px;
            padding: 12px 16px;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
            word-break: break-all;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .url-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 6px;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 400px;
            gap: 16px;
            text-align: center;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .empty-title {
            font-size: 20px;
            font-weight: 600;
        }

        .empty-desc {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            max-width: 400px;
        }

        .input-group {
            display: flex;
            gap: 12px;
            margin-top: 20px;
            width: 100%;
            max-width: 500px;
        }

        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: #00d4ff;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            background: #10b981;
            color: #fff;
            font-size: 14px;
            z-index: 9999;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .toast.show {
            transform: translateX(0);
        }

        .toast.error {
            background: #ef4444;
        }

        .video::-webkit-media-controls {
            display: flex !important;
        }

        @media (max-width: 768px) {
            .header {
                padding: 16px 20px;
            }

            .header h1 {
                font-size: 18px;
            }

            .container {
                padding: 12px;
            }

            .info-panel {
                padding: 16px;
            }

            .btn {
                padding: 8px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>🎬 在线视频播放器</h1>
            <div class="subtitle">当前播放器: <?php echo $currentPlayerName; ?> · 支持 M3U8 在线解析播放 · 去广告播放 · 官解链接自动替换</div>
        </div>
        <div class="player-switch">
            <label for="playerSelect">切换播放器:</label>
            <select id="playerSelect" onchange="switchPlayer(this.value)">
                <optgroup label="🔥 推荐">
                    <option value="dplayer" <?php echo $currentPlayer === 'dplayer' ? 'selected' : ''; ?>>DPlayer</option>
                    <option value="videojs" <?php echo $currentPlayer === 'videojs' ? 'selected' : ''; ?>>Video.js</option>
                </optgroup>
                <optgroup label="📦 开源播放器">
                    <option value="shaka" <?php echo $currentPlayer === 'shaka' ? 'selected' : ''; ?>>Shaka Player (Google)</option>
                    <option value="clappr" <?php echo $currentPlayer === 'clappr' ? 'selected' : ''; ?>>Clappr</option>
                    <option value="dashjs" <?php echo $currentPlayer === 'dashjs' ? 'selected' : ''; ?>>dash.js (DASH)</option>
                    <option value="hlsjs" <?php echo $currentPlayer === 'hlsjs' ? 'selected' : ''; ?>>hls.js 原生</option>
                    <option value="muiplayer" <?php echo $currentPlayer === 'muiplayer' ? 'selected' : ''; ?>>MuiPlayer</option>
                    <option value="artplayer" <?php echo $currentPlayer === 'artplayer' ? 'selected' : ''; ?>>ArtPlayer</option>
                    <option value="nplayer" <?php echo $currentPlayer === 'nplayer' ? 'selected' : ''; ?>>NPlayer</option>
                    <option value="native" <?php echo $currentPlayer === 'native' ? 'selected' : ''; ?>>原生 Video 标签</option>
                </optgroup>
                <optgroup label="💎 商业播放器 (需License)">
                    <option value="jwplayer" <?php echo $currentPlayer === 'jwplayer' ? 'selected' : ''; ?>>JW Player</option>
                    <option value="bitmovin" <?php echo $currentPlayer === 'bitmovin' ? 'selected' : ''; ?>>Bitmovin</option>
                    <option value="theoplayer" <?php echo $currentPlayer === 'theoplayer' ? 'selected' : ''; ?>>THEOplayer</option>
                    <option value="flowplayer" <?php echo $currentPlayer === 'flowplayer' ? 'selected' : ''; ?>>Flowplayer</option>
                    <option value="radiant" <?php echo $currentPlayer === 'radiant' ? 'selected' : ''; ?>>Radiant Media Player</option>
                    <option value="nexplayer" <?php echo $currentPlayer === 'nexplayer' ? 'selected' : ''; ?>>NexPlayer</option>
                    <option value="castlabs" <?php echo $currentPlayer === 'castlabs' ? 'selected' : ''; ?>>castLabs PRESTOplay</option>
                    <option value="visualon" <?php echo $currentPlayer === 'visualon' ? 'selected' : ''; ?>>VisualON</option>
                </optgroup>
            </select>
        </div>
    </div>

    <div class="container">
        <div class="player-wrapper">
            <div id="videoPlayer"></div>
        </div>

        <?php if (empty($url)): ?>
        <div class="info-panel">
            <h2>视频播放</h2>
            <div class="empty-state">
                <div class="empty-icon">🎥</div>
                <div class="empty-title">请输入视频链接</div>
                <div class="empty-desc">在地址栏后面加上 ?url=视频链接 即可播放，或在下方输入框中输入视频地址</div>
                <div class="empty-desc" style="margin-top: 12px; color: #00d4ff;">
                    💡 支持官方视频链接（腾讯/爱奇艺/优酷/芒果TV/哔哩哔哩等），会自动替换为资源站播放
                </div>
                <div class="input-group">
                    <input type="text" id="urlInput" placeholder="请输入 M3U8 视频链接或官解链接..." />
                    <button class="btn btn-primary" onclick="playVideo()">
                        <span>▶</span> 播放
                    </button>
                </div>
            </div>
        </div>
        <?php else: ?>
        <?php if ($isOfficialUrl): ?>
        <div class="info-panel" style="border-left: 4px solid #00d4ff;">
            <h2>🎯 官解链接自动替换</h2>
            <div class="url-label">原始官解地址</div>
            <div class="url-display" id="originalUrl"><?php echo htmlspecialchars($originalUrl); ?></div>
            <div class="url-label">平台检测</div>
            <div class="url-display" id="platformInfo">正在检测平台...</div>
            <div class="url-label" id="videoTitleLabel" style="display:none;">视频信息</div>
            <div class="url-display" id="videoTitle" style="display:none;"></div>
            <div class="url-label" id="playUrlLabel" style="display:none;">资源站播放地址</div>
            <div class="url-display" id="playUrl" style="display:none;"></div>
            <div class="url-label" id="statusLabel" style="display:none;">解析状态</div>
            <div class="url-display" id="status" style="display:none;"></div>
            <div class="btn-group" style="margin-top: 12px;">
                <button class="btn btn-secondary" onclick="copyUrl('originalUrl')">
                    <span>📋</span> 复制原始地址
                </button>
                <button class="btn btn-secondary" onclick="copyUrl('playUrl')">
                    <span>📋</span> 复制播放地址
                </button>
                <button class="btn btn-secondary" id="toggleAdBtn" onclick="toggleAdSkip()" style="display: none;">
                    <span>🎬</span> 原始地址
                </button>
                <button class="btn btn-primary" onclick="reloadPlayer()">
                    <span>🔄</span> 重新加载
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="info-panel">
            <h2>播放信息</h2>
            <div class="url-label">原始视频地址</div>
            <div class="url-display" id="originalUrl"><?php echo htmlspecialchars($url); ?></div>
            <div class="url-label">解析播放地址</div>
            <div class="url-display" id="playUrl"><?php echo htmlspecialchars($videoUrl); ?></div>
            <div class="btn-group">
                <button class="btn btn-secondary" onclick="copyUrl('originalUrl')">
                    <span>📋</span> 复制原始地址
                </button>
                <button class="btn btn-secondary" onclick="copyUrl('playUrl')">
                    <span>📋</span> 复制播放地址
                </button>
                <button class="btn btn-primary" onclick="reloadPlayer()">
                    <span>🔄</span> 重新加载
                </button>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="footer">
        在线视频播放器 · 支持 M3U8 格式解析播放 · 当前播放器: <?php echo $currentPlayerName; ?>
    </div>

    <div class="toast" id="toast"></div>

    <script>
        let player = null;
        let hls = null;
        const videoUrl = <?php echo json_encode($videoUrl); ?>;
        const originalUrl = <?php echo json_encode($originalUrl); ?>;
        const isOfficialUrl = <?php echo json_encode($isOfficialUrl); ?>;
        const officialReplaceUrl = <?php echo json_encode($officialReplaceUrl); ?>;
        const apiUrl = <?php echo json_encode($apiUrl); ?>;
        const currentPlayer = <?php echo json_encode($currentPlayer); ?>;
        const autoplay = <?php echo json_encode($playerConfig['autoplay']); ?>;
        const preload = <?php echo json_encode($playerConfig['preload']); ?>;

        const hlsConfig = {
            enableWorker: true,
            lowLatencyMode: false,
            maxBufferLength: 60,
            maxMaxBufferLength: 900,
            minBufferLength: 5,
            maxBufferSize: 120 * 1000 * 1000,
            maxBufferHole: 0.3,
            highBufferWatchdogPeriod: 0.5,
            startLevel: -1,
            capLevelToPlayerSize: true,
            liveSyncDurationCount: 3,
            liveMaxLatencyDurationCount: 10,
            fragLoadingTimeOut: 30000,
            manifestLoadingTimeOut: 20000,
            levelLoadingTimeOut: 15000,
            backBufferLength: 60,
            startFragPrefetch: true,
            enableSoftwareAES: true,
            abrEwmaDefaultEstimate: 2000000,
            abrBandWidthFactor: 0.75,
            abrEwmaFastLive: 3.0,
            abrEwmaSlowLive: 9.0,
            maxFragLookUpTolerance: 0.2,
            initialLiveManifestSize: 1,
            debug: false,
            enableCEA708Captions: false,
            enableWebVTT: false,
            enableIMSC1: false,
            renderTextTracksNatively: false,
            xhrSetup: function(xhr) {
                xhr.withCredentials = false;
                xhr.timeout = 30000;
            }
        };

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + (type === 'error' ? 'error' : '');
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        function copyUrl(elementId) {
            const text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text).then(() => {
                showToast('复制成功！');
            }).catch(() => {
                showToast('复制失败，请手动复制', 'error');
            });
        }

        function playVideo() {
            const url = document.getElementById('urlInput').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }
            const params = new URLSearchParams(window.location.search);
            params.set('url', url);
            window.location.search = params.toString();
        }

        function switchPlayer(playerType) {
            const params = new URLSearchParams(window.location.search);
            params.set('player', playerType);
            window.location.search = params.toString();
        }

        function destroyPlayer() {
            if (hls) {
                try { hls.destroy(); } catch(e) {}
                hls = null;
            }
            if (player) {
                try {
                    switch (currentPlayer) {
                        case 'dplayer':
                        case 'artplayer':
                            if (player.destroy) player.destroy();
                            break;
                        case 'videojs':
                        case 'nplayer':
                            if (player.dispose) player.dispose();
                            break;
                        case 'shaka':
                            if (player.destroy) player.destroy();
                            break;
                        case 'clappr':
                            if (player.destroy) player.destroy();
                            break;
                        case 'dashjs':
                            if (player.reset) player.reset();
                            break;
                        case 'muiplayer':
                            if (player.destroy) player.destroy();
                            break;
                        case 'jwplayer':
                            if (player.remove) player.remove();
                            break;
                        case 'bitmovin':
                            if (player.destroy) player.destroy();
                            break;
                        case 'theoplayer':
                            if (player.destroy) player.destroy();
                            break;
                        case 'flowplayer':
                            if (player.shutdown) player.shutdown();
                            break;
                        case 'radiant':
                            if (player.dispose) player.dispose();
                            break;
                        case 'hlsjs':
                        case 'native':
                            break;
                    }
                } catch (e) { console.warn('销毁播放器出错:', e); }
                player = null;
            }
            const container = document.getElementById('videoPlayer');
            if (container) {
                container.innerHTML = '';
            }
        }

        function reloadPlayer() {
            destroyPlayer();
            if (isOfficialUrl) {
                if (useAdSkip && currentPlayUrl) {
                    updatePlayStatus('loading', '正在重新加载...');
                    initPlayer(currentPlayUrl);
                } else if (!useAdSkip && currentRawUrl) {
                    updatePlayStatus('loading', '正在重新加载...');
                    initPlayer(currentRawUrl);
                } else {
                    handleOfficialUrl();
                }
            } else {
                initPlayer(videoUrl);
            }
        }

        function getPlatformName(url) {
            const platforms = {
                'v.qq.com': '腾讯视频',
                'iqiyi.com': '爱奇艺',
                'youku.com': '优酷',
                'mgtv.com': '芒果TV',
                'bilibili.com': '哔哩哔哩',
                'sohu.com': '搜狐视频',
                'pptv.com': 'PP视频'
            };
            for (const [domain, name] of Object.entries(platforms)) {
                if (url.indexOf(domain) !== -1) {
                    return name;
                }
            }
            return '未知平台';
        }

        let currentPlayUrl = '';
        let currentRawUrl = '';
        let useAdSkip = true;
        
        async function handleOfficialUrl() {
            if (!isOfficialUrl) {
                currentPlayUrl = videoUrl;
                currentRawUrl = videoUrl;
                initPlayer(videoUrl);
                return;
            }
            const platformName = getPlatformName(originalUrl);
            document.getElementById('platformInfo').textContent = '✅ 检测到 ' + platformName + ' 链接';
            try {
                showToast('正在解析官解链接...', 'success');
                const timestamp = Date.now();
                const response = await fetch(officialReplaceUrl + encodeURIComponent(originalUrl) + '&_t=' + timestamp);
                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (jsonErr) {
                    throw new Error('服务器返回非JSON响应: ' + responseText.substring(0, 200));
                }
                if (data.success) {
                    if (data.video_title) {
                        document.getElementById('videoTitleLabel').style.display = 'block';
                        document.getElementById('videoTitle').style.display = 'block';
                        document.getElementById('videoTitle').textContent = data.video_title;
                    }
                    
                    let playUrl = '';
                    let rawUrl = '';
                    if (data.m3u8_url) {
                        playUrl = data.m3u8_url;
                        rawUrl = data.m3u8_url;
                    } else if (data.ad_skip_url) {
                        playUrl = data.ad_skip_url;
                        rawUrl = data.ad_skip_url;
                    } else {
                        throw new Error('未获取到播放地址');
                    }
                    
                    currentPlayUrl = playUrl;
                    currentRawUrl = rawUrl;
                    
                    document.getElementById('playUrlLabel').style.display = 'block';
                    document.getElementById('playUrl').style.display = 'block';
                    document.getElementById('playUrl').textContent = playUrl;
                    document.getElementById('statusLabel').style.display = 'block';
                    document.getElementById('status').style.display = 'block';
                    document.getElementById('status').innerHTML = '<span style="color: #10b981;">✅ 解析成功</span>，正在加载播放器...';
                    showToast('官解链接解析成功！', 'success');
                    
                    const toggleBtn = document.getElementById('toggleAdBtn');
                    if (toggleBtn) {
                        toggleBtn.style.display = 'inline-flex';
                    }
                    
                    initPlayer(playUrl);
                } else {
                    throw new Error(data.message || '解析失败');
                }
            } catch (error) {
                console.error('官解解析错误:', error);
                document.getElementById('statusLabel').style.display = 'block';
                document.getElementById('status').style.display = 'block';
                document.getElementById('status').innerHTML = '<span style="color: #ef4444;">❌ 解析失败</span>：' + error.message;
                showToast('官解链接解析失败：' + error.message, 'error');
            }
        }
        
        function toggleAdSkip() {
            useAdSkip = !useAdSkip;
            const btn = document.getElementById('toggleAdBtn');
            if (useAdSkip) {
                btn.innerHTML = '<span>📺</span> 去广告播放';
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-secondary');
                if (currentPlayUrl) {
                    updatePlayStatus('loading', '正在切换到去广告播放...');
                    initPlayer(currentPlayUrl);
                }
            } else {
                btn.innerHTML = '<span>🎬</span> 原始地址';
                btn.classList.remove('btn-secondary');
                btn.classList.add('btn-warning');
                if (currentRawUrl) {
                    updatePlayStatus('loading', '正在切换到原始地址播放...');
                    initPlayer(currentRawUrl);
                }
            }
        }

        let loadTimeout = null;
        
        function clearLoadTimeout() {
            if (loadTimeout) {
                clearTimeout(loadTimeout);
                loadTimeout = null;
            }
        }

        function createHls(video, url) {
            if (hls) {
                hls.destroy();
            }
            clearLoadTimeout();
            
            if (Hls.isSupported()) {
                hls = new Hls(hlsConfig);
                hls.loadSource(url);
                hls.attachMedia(video);
                
                loadTimeout = setTimeout(function() {
                    if (video.readyState < 2) {
                        console.warn('视频加载超时，尝试原始地址');
                        showToast('视频加载较慢，正在尝试其他方式...', 'warning');
                    }
                }, 15000);
                
                hls.on(Hls.Events.MANIFEST_PARSED, function () {
                    console.log('HLS 加载完成');
                    clearLoadTimeout();
                });
                hls.on(Hls.Events.ERROR, function (event, data) {
                    console.error('HLS 错误:', data);
                    if (data.fatal) {
                        clearLoadTimeout();
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.log('网络错误，尝试恢复...');
                                try {
                                    hls.startLoad();
                                } catch(e) {
                                    showToast('网络错误，视频加载失败', 'error');
                                    updatePlayStatus('error', '网络错误，请检查网络或尝试切换播放源');
                                }
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.log('媒体错误，尝试恢复...');
                                try {
                                    hls.recoverMediaError();
                                } catch(e) {
                                    showToast('媒体错误，视频加载失败', 'error');
                                    updatePlayStatus('error', '媒体格式错误，请尝试切换播放源');
                                }
                                break;
                            default:
                                showToast('视频加载失败，请检查链接是否正确', 'error');
                                updatePlayStatus('error', '视频加载失败，请尝试重新加载');
                                break;
                        }
                    }
                });
                return hls;
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                return null;
            } else {
                showToast('您的浏览器不支持 HLS 播放', 'error');
                return null;
            }
        }
        
        function updatePlayStatus(type, message) {
            const statusEl = document.getElementById('status');
            if (!statusEl) return;
            
            if (type === 'error') {
                statusEl.innerHTML = '<span style="color: #ef4444;">❌ ' + message + '</span>';
            } else if (type === 'success') {
                statusEl.innerHTML = '<span style="color: #10b981;">✅ ' + message + '</span>';
            } else if (type === 'loading') {
                statusEl.innerHTML = '<span style="color: #f59e0b;">⏳ ' + message + '</span>';
            }
        }

        function initPlayer(url) {
            if (!url) return;

            const container = document.getElementById('videoPlayer');
            destroyPlayer();

            updatePlayStatus('loading', '正在加载播放器...');

            switch (currentPlayer) {
                case 'dplayer':
                    initDPlayer(container, url);
                    break;
                case 'videojs':
                    initVideoJs(container, url);
                    break;
                case 'shaka':
                    initShakaPlayer(container, url);
                    break;
                case 'clappr':
                    initClappr(container, url);
                    break;
                case 'dashjs':
                    initDashJs(container, url);
                    break;
                case 'hlsjs':
                    initHlsJs(container, url);
                    break;
                case 'muiplayer':
                    initMuiPlayer(container, url);
                    break;
                case 'artplayer':
                    initArtPlayer(container, url);
                    break;
                case 'nplayer':
                    initNPlayer(container, url);
                    break;
                case 'native':
                    initNativePlayer(container, url);
                    break;
                case 'jwplayer':
                    initJWPlayer(container, url);
                    break;
                case 'bitmovin':
                    initBitmovin(container, url);
                    break;
                case 'theoplayer':
                    initTHEOplayer(container, url);
                    break;
                case 'flowplayer':
                    initFlowplayer(container, url);
                    break;
                case 'radiant':
                    initRadiant(container, url);
                    break;
                case 'nexplayer':
                case 'castlabs':
                case 'visualon':
                    showCommercialPlayerNotice();
                    break;
                default:
                    initDPlayer(container, url);
            }
        }

        function showCommercialPlayerNotice() {
            const container = document.getElementById('videoPlayer');
            container.innerHTML = `
                <div style="display:flex;align-items:center;justify-content:center;height:100%;flex-direction:column;gap:16px;padding:40px;text-align:center;">
                    <div style="font-size:48px;">🔐</div>
                    <div style="font-size:18px;font-weight:600;">商业播放器授权提示</div>
                    <div style="font-size:14px;color:rgba(255,255,255,0.6);max-width:400px;line-height:1.6;">
                        该播放器为商业产品，需要 License Key 才能使用。<br>
                        请在 <code style="background:rgba(255,255,255,0.1);padding:2px 6px;border-radius:4px;">player/player_config.php</code> 中配置授权信息。
                    </div>
                    <button onclick="switchPlayer('dplayer')" style="padding:10px 24px;background:#00d4ff;color:#000;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
                        切换到 DPlayer
                    </button>
                </div>
            `;
            updatePlayStatus('error', '商业播放器需要配置 License Key');
        }

        function initDPlayer(container, url) {
            if (player && player.destroy) {
                try {
                    player.destroy();
                } catch(e) {}
                player = null;
            }
            if (hls) {
                try {
                    hls.destroy();
                } catch(e) {}
                hls = null;
            }
            clearLoadTimeout();

            let firstFrameReady = false;
            let playAttempted = false;
            let posterGenerated = false;

            const tryPlay = function(video) {
                if (playAttempted) return;
                if (firstFrameReady && video && video.paused) {
                    playAttempted = true;
                    video.play().catch(function(e) {
                        console.warn('自动播放被阻止:', e);
                        showToast('点击播放按钮开始播放', 'warning');
                    });
                }
            };

            const generatePoster = function(video, dp) {
                if (posterGenerated || !video || !dp) return;
                if (video.readyState >= 2 && video.videoWidth > 0) {
                    try {
                        const canvas = document.createElement('canvas');
                        canvas.width = 640;
                        canvas.height = 360;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, 640, 360);
                        const posterUrl = canvas.toDataURL('image/jpeg', 0.8);
                        posterGenerated = true;
                        
                        const posterImg = container.querySelector('.dplayer-video-wrap .dplayer-poster');
                        if (posterImg) {
                            posterImg.style.backgroundImage = 'url(' + posterUrl + ')';
                            posterImg.style.backgroundSize = 'cover';
                            posterImg.style.backgroundPosition = 'center';
                        }
                    } catch(e) {
                        console.warn('生成海报图失败:', e);
                    }
                }
            };

            player = new DPlayer({
                container: container,
                video: {
                    url: url,
                    type: 'customHls',
                    customType: {
                        customHls: function (video, dp) {
                            if (Hls.isSupported()) {
                                hls = new Hls(hlsConfig);
                                hls.loadSource(url);
                                hls.attachMedia(video);
                                dp.hls = hls;
                                
                                loadTimeout = setTimeout(function() {
                                    if (video.readyState < 2) {
                                        showToast('视频加载较慢，请耐心等待...', 'warning');
                                        updatePlayStatus('loading', '视频加载中，请耐心等待...');
                                    }
                                }, 8000);
                                
                                hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
                                    console.log('HLS 清单解析完成, 共', data.levels.length, '个清晰度');
                                    clearLoadTimeout();
                                    updatePlayStatus('loading', '视频解析完成，正在缓冲首帧...');
                                });

                                hls.on(Hls.Events.FRAG_LOADED, function (event, data) {
                                    console.log('片段加载完成, 索引:', data.frag.sn, '时长:', data.frag.duration.toFixed(2) + 's');
                                    clearLoadTimeout();
                                    firstFrameReady = true;
                                    generatePoster(video, dp);
                                    if (!playAttempted) {
                                        updatePlayStatus('success', '首帧加载完成，即将播放...');
                                    }
                                    if (autoplay) {
                                        tryPlay(video);
                                    }
                                });
                                
                                hls.on(Hls.Events.FRAG_BUFFERED, function(event, data) {
                                    generatePoster(video, dp);
                                });

                                hls.on(Hls.Events.LEVEL_SWITCHED, function (event, data) {
                                    console.log('清晰度切换到:', data.level);
                                });

                                hls.on(Hls.Events.BUFFER_FULL, function() {
                                    console.log('缓冲区已满');
                                });

                                let seekRetryCount = 0;
                                const MAX_SEEK_RETRIES = 5;
                                let consecutiveErrors = 0;
                                const MAX_CONSECUTIVE_ERRORS = 3;
                                
                                hls.on(Hls.Events.ERROR, function (event, data) {
                                    console.error('HLS 错误:', data.type, data.details, data.fatal ? '(致命)' : '');
                                    if (data.fatal) {
                                        consecutiveErrors++;
                                        clearLoadTimeout();
                                        switch (data.type) {
                                            case Hls.ErrorTypes.NETWORK_ERROR:
                                                console.log('网络错误，尝试恢复...');
                                                updatePlayStatus('loading', '网络波动，正在恢复...');
                                                if (consecutiveErrors < MAX_CONSECUTIVE_ERRORS) {
                                                    try {
                                                        hls.startLoad();
                                                        consecutiveErrors = 0;
                                                    } catch(e) {
                                                        setTimeout(function() {
                                                            try {
                                                                hls.loadSource(url);
                                                                hls.startLoad();
                                                            } catch(e2) {
                                                                hls.destroy();
                                                                hls = new Hls(hlsConfig);
                                                                hls.loadSource(url);
                                                                hls.attachMedia(video);
                                                            }
                                                        }, 2000);
                                                    }
                                                } else {
                                                    showToast('网络错误，正在重新加载...', 'error');
                                                    updatePlayStatus('error', '网络错误，正在重新加载...');
                                                    setTimeout(function() {
                                                        initDPlayer(container, url);
                                                    }, 3000);
                                                }
                                                break;
                                            case Hls.ErrorTypes.MEDIA_ERROR:
                                                console.log('媒体错误，尝试恢复...');
                                                updatePlayStatus('loading', '媒体错误，正在恢复...');
                                                try {
                                                    hls.recoverMediaError();
                                                    consecutiveErrors = 0;
                                                } catch(e) {
                                                    try {
                                                        hls.swapAudioCodec();
                                                        hls.recoverMediaError();
                                                        consecutiveErrors = 0;
                                                    } catch(e2) {
                                                        if (seekRetryCount < MAX_SEEK_RETRIES) {
                                                            seekRetryCount++;
                                                            console.log('媒体错误重试，第' + seekRetryCount + '次...');
                                                            setTimeout(function() {
                                                                try {
                                                                    const currentTime = video.currentTime;
                                                                    hls.destroy();
                                                                    hls = new Hls(hlsConfig);
                                                                    hls.loadSource(url);
                                                                    hls.attachMedia(video);
                                                                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                                                        video.currentTime = currentTime;
                                                                        video.play().catch(function() {});
                                                                    });
                                                                } catch(e3) {
                                                                    initDPlayer(container, url);
                                                                }
                                                            }, 500);
                                                        } else {
                                                            showToast('媒体错误，正在重新加载...', 'error');
                                                            updatePlayStatus('error', '媒体格式错误，正在重新加载...');
                                                            setTimeout(function() {
                                                                initDPlayer(container, url);
                                                            }, 1500);
                                                        }
                                                    }
                                                }
                                                break;
                                            case Hls.ErrorTypes.PARSE_ERROR:
                                                console.log('解析错误，尝试重新加载...');
                                                updatePlayStatus('loading', '解析错误，正在重新加载...');
                                                hls.destroy();
                                                hls = new Hls(hlsConfig);
                                                hls.loadSource(url);
                                                hls.attachMedia(video);
                                                break;
                                            default:
                                                showToast('视频加载失败', 'error');
                                                updatePlayStatus('error', '视频加载失败，请尝试重新加载');
                                                break;
                                        }
                                    } else {
                                        consecutiveErrors = 0;
                                    }
                                });

                                let seekingTimer = null;
                                video.addEventListener('seeking', function() {
                                    console.log('跳转到:', video.currentTime.toFixed(2) + 's');
                                    updatePlayStatus('loading', '跳转中，正在加载数据...');
                                    if (seekingTimer) clearTimeout(seekingTimer);
                                    seekingTimer = setTimeout(function() {
                                        if (video.paused) {
                                            video.play().catch(function() {});
                                        }
                                    }, 500);
                                });

                                video.addEventListener('seeked', function() {
                                    console.log('跳转完成');
                                    seekRetryCount = 0;
                                    if (seekingTimer) {
                                        clearTimeout(seekingTimer);
                                        seekingTimer = null;
                                    }
                                    generatePoster(video, dp);
                                    if (video.paused) {
                                        video.play().catch(function() {});
                                    }
                                });

                                video.addEventListener('waiting', function() {
                                    updatePlayStatus('loading', '缓冲中...');
                                });

                                video.addEventListener('playing', function() {
                                    seekRetryCount = 0;
                                    consecutiveErrors = 0;
                                });

                                video.addEventListener('loadedmetadata', function() {
                                    generatePoster(video, dp);
                                });

                                video.addEventListener('canplay', function() {
                                    generatePoster(video, dp);
                                });

                                video.addEventListener('ended', function() {
                                    updatePlayStatus('success', '播放结束');
                                });

                                video.addEventListener('stalled', function() {
                                    updatePlayStatus('loading', '视频卡顿，正在缓冲...');
                                });

                                video.addEventListener('suspend', function() {
                                    updatePlayStatus('loading', '视频暂停加载...');
                                });
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = url;
                                video.addEventListener('loadedmetadata', function() {
                                    generatePoster(video, player);
                                });
                                if (autoplay) {
                                    video.play().catch(function(e) {
                                        console.warn('自动播放被阻止:', e);
                                    });
                                }
                            }
                        }
                    }
                },
                autoplay: false,
                theme: '#00d4ff',
                loop: false,
                lang: 'zh-cn',
                screenshot: true,
                hotkey: true,
                preload: preload,
                volume: 0.7,
                mutex: true,
                airplay: true,
                playbackSpeed: [0.5, 0.75, 1, 1.25, 1.5, 2],
                danmaku: {
                    id: 'm3u8_player_' + Date.now(),
                    api: 'https://api.prprpr.me/dplayer/',
                    addition: ['https://api.prprpr.me/dplayer/v3/bilibili?aid=41571429'],
                    user: '游客'
                }
            });

            player.on('error', function () {
                console.error('DPlayer 错误');
                showToast('视频播放出错', 'error');
            });
            
            player.on('play', function() {
                clearLoadTimeout();
                updatePlayStatus('success', '正在播放');
            });

            player.on('pause', function() {
                updatePlayStatus('success', '已暂停');
            });

            player.on('waiting', function() {
                updatePlayStatus('loading', '缓冲中...');
            });

            player.on('canplay', function() {
                updatePlayStatus('success', '视频已就绪');
            });

            player.on('loadedmetadata', function() {
                generatePoster(player.video, player);
            });

            initThumbnailPreview(container, player);
        }

        function initThumbnailPreview(container, dp) {
            if (!dp || !dp.video) return;

            const barWrap = container.querySelector('.dplayer-bar-wrap');
            if (!barWrap) return;

            const thumbnailEl = document.createElement('div');
            thumbnailEl.className = 'dplayer-thumbnail-preview';
            thumbnailEl.style.cssText = `
                position: absolute;
                bottom: 40px;
                transform: translateX(-50%);
                width: 160px;
                background: rgba(0,0,0,0.9);
                border-radius: 6px;
                overflow: hidden;
                display: none;
                pointer-events: none;
                z-index: 20;
                border: 1px solid rgba(255,255,255,0.15);
                box-shadow: 0 4px 20px rgba(0,0,0,0.6);
            `;

            const thumbCanvas = document.createElement('canvas');
            thumbCanvas.width = 160;
            thumbCanvas.height = 90;
            thumbCanvas.style.cssText = 'width:100%;height:auto;display:block;background:#000;';
            thumbnailEl.appendChild(thumbCanvas);

            const timeLabel = document.createElement('div');
            timeLabel.style.cssText = 'padding:4px 8px;text-align:center;font-size:12px;color:#fff;background:rgba(0,0,0,0.7);font-family:monospace;';
            timeLabel.textContent = '00:00';
            thumbnailEl.appendChild(timeLabel);

            const thumbCtx = thumbCanvas.getContext('2d');
            let isHovering = false;
            let lastSnapTime = -1;

            function formatTime(seconds) {
                if (!seconds || seconds < 0) seconds = 0;
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = Math.floor(seconds % 60);
                if (h > 0) {
                    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                }
                return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            }

            function tryCaptureCurrentFrame() {
                try {
                    if (dp.video && dp.video.readyState >= 2 && dp.video.videoWidth > 0) {
                        thumbCtx.drawImage(dp.video, 0, 0, 160, 90);
                        lastSnapTime = dp.video.currentTime;
                        return true;
                    }
                } catch(e) {}
                return false;
            }

            function updateThumbnail(percentage) {
                if (!dp.duration) return;
                const targetTime = percentage * dp.duration;
                timeLabel.textContent = formatTime(targetTime);

                if (Math.abs(targetTime - (dp.video?.currentTime || 0)) < 2) {
                    tryCaptureCurrentFrame();
                }
            }

            barWrap.addEventListener('mouseenter', function() {
                isHovering = true;
                thumbnailEl.style.display = 'block';
                tryCaptureCurrentFrame();
            });

            barWrap.addEventListener('mouseleave', function() {
                isHovering = false;
                thumbnailEl.style.display = 'none';
            });

            barWrap.addEventListener('mousemove', function(e) {
                if (!isHovering) return;
                const rect = barWrap.getBoundingClientRect();
                const percentage = Math.min(Math.max((e.clientX - rect.left) / rect.width, 0), 1);
                const thumbX = e.clientX - rect.left;
                const thumbWidth = 160;
                const maxLeft = rect.width - thumbWidth / 2;
                const minLeft = thumbWidth / 2;
                const finalLeft = Math.max(minLeft, Math.min(maxLeft, thumbX));
                thumbnailEl.style.left = finalLeft + 'px';
                updateThumbnail(percentage);
            });

            setInterval(function() {
                if (!isHovering) return;
                tryCaptureCurrentFrame();
            }, 500);

            barWrap.appendChild(thumbnailEl);
        }

        function initVideoJs(container, url) {
            const videoEl = document.createElement('video');
            videoEl.id = 'videojs-player';
            videoEl.className = 'video-js vjs-big-play-centered';
            videoEl.controls = true;
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            container.appendChild(videoEl);

            player = videojs('videojs-player', {
                autoplay: autoplay,
                controls: true,
                preload: preload,
                language: 'zh-CN',
                fluid: true,
                aspectRatio: '16:9',
                controlBar: {
                    children: [
                        'playToggle',
                        'volumePanel',
                        'currentTimeDisplay',
                        'timeDivider',
                        'durationDisplay',
                        'progressControl',
                        'fullscreenToggle',
                    ]
                }
            });

            if (Hls.isSupported()) {
                createHls(videoEl, url);
            } else {
                player.src(url);
            }

            player.on('error', function () {
                showToast('视频播放出错，请检查链接是否有效', 'error');
            });
        }

        function initMuiPlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.id = 'muiplayer-video';
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            container.appendChild(videoEl);

            if (Hls.isSupported()) {
                createHls(videoEl, url);
            } else {
                videoEl.src = url;
            }

            player = new MuiPlayer({
                container: '#videoPlayer',
                video: {
                    url: url,
                    poster: '',
                },
                autoplay: autoplay,
                preload: preload,
                volume: 0.7,
                themeColor: '#00d4ff',
                mode: 'both',
                language: 'zh-CN',
                showRate: true,
                title: '视频播放',
            });

            if (Hls.isSupported()) {
                player.video.addEventListener('loadedmetadata', function () {
                    console.log('MuiPlayer HLS 加载完成');
                });
            }
        }

        function initArtPlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            container.appendChild(videoEl);

            player = new Artplayer({
                container: container,
                url: url,
                autoplay: autoplay,
                preload: preload,
                volume: 0.7,
                theme: '#00d4ff',
                lang: 'zh-cn',
                setting: true,
                playbackRate: true,
                fullscreen: true,
                fullscreenWeb: true,
                miniProgressBar: true,
                mutex: true,
                pip: true,
                autoSize: false,
                playsInline: true,
                autoPlayback: true,
                fastForward: true,
                customType: {
                    m3u8: function (video, url, art) {
                        if (Hls.isSupported()) {
                            const hlsInstance = new Hls(hlsConfig);
                            hlsInstance.loadSource(url);
                            hlsInstance.attachMedia(video);
                            art.hls = hlsInstance;
                        } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                            video.src = url;
                        }
                    }
                },
            });

            player.on('error', function () {
                showToast('视频播放出错，请检查链接是否有效', 'error');
            });
        }

        function initNPlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.id = 'nplayer-video';
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            container.appendChild(videoEl);

            if (Hls.isSupported()) {
                createHls(videoEl, url);
            } else {
                videoEl.src = url;
            }

            player = new NPlayer.Player({
                container: container,
                video: videoEl,
                volume: 0.7,
                autoplay: autoplay,
                themeColor: '#00d4ff',
            });

            if (Hls.isSupported()) {
                videoEl.addEventListener('loadedmetadata', function () {
                    console.log('NPlayer HLS 加载完成');
                });
            }
        }

        function initNativePlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.id = 'native-video';
            videoEl.controls = true;
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.autoplay = autoplay;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.background = '#000';
            videoEl.style.objectFit = 'contain';
            container.appendChild(videoEl);

            if (url.indexOf('.m3u8') !== -1 || url.indexOf('m3u8') !== -1) {
                if (Hls.isSupported()) {
                    createHls(videoEl, url);
                    videoEl.addEventListener('loadeddata', function() {
                        updatePlayStatus('success', '视频加载成功');
                        if (autoplay) videoEl.play().catch(() => {});
                    });
                } else if (videoEl.canPlayType('application/vnd.apple.mpegurl')) {
                    videoEl.src = url;
                    updatePlayStatus('success', '视频加载成功');
                } else {
                    updatePlayStatus('error', '当前浏览器不支持 HLS 播放');
                }
            } else {
                videoEl.src = url;
                videoEl.addEventListener('loadeddata', function() {
                    updatePlayStatus('success', '视频加载成功');
                });
            }

            videoEl.addEventListener('error', function() {
                updatePlayStatus('error', '视频加载失败');
            });
        }

        function initHlsJs(container, url) {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;width:100%;height:100%;background:#000;';

            const videoEl = document.createElement('video');
            videoEl.controls = true;
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.objectFit = 'contain';
            videoEl.style.background = '#000';
            wrap.appendChild(videoEl);
            container.appendChild(wrap);

            if (Hls.isSupported()) {
                hls = new Hls(hlsConfig);
                hls.loadSource(url);
                hls.attachMedia(videoEl);

                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    console.log('hls.js 清单解析完成');
                    updatePlayStatus('success', '视频加载成功');
                    if (autoplay) videoEl.play().catch(() => {});
                });

                hls.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                hls.recoverMediaError();
                                break;
                            default:
                                updatePlayStatus('error', '视频播放错误');
                                break;
                        }
                    }
                });
            } else if (videoEl.canPlayType('application/vnd.apple.mpegurl')) {
                videoEl.src = url;
                updatePlayStatus('success', '视频加载成功');
                if (autoplay) videoEl.play().catch(() => {});
            } else {
                updatePlayStatus('error', '当前浏览器不支持 HLS 播放');
            }

            player = { video: videoEl, destroy: function() { hls && hls.destroy(); } };
        }

        function initShakaPlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.controls = true;
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.objectFit = 'contain';
            videoEl.style.background = '#000';
            container.appendChild(videoEl);

            if (typeof shaka === 'undefined') {
                updatePlayStatus('error', 'Shaka Player 加载失败');
                return;
            }

            shaka.polyfill.installAll();

            if (shaka.Player.isBrowserSupported()) {
                const shakaPlayer = new shaka.Player(videoEl);
                player = shakaPlayer;

                shakaPlayer.configure({
                    abr: {
                        enabled: true,
                        defaultBandwidthEstimate: 500000
                    },
                    streaming: {
                        bufferingGoal: 30,
                        rebufferingGoal: 2,
                        bufferBehind: 30
                    }
                });

                shakaPlayer.addEventListener('error', function(event) {
                    console.error('Shaka 错误:', event.detail);
                    updatePlayStatus('error', '播放错误: ' + event.detail.code);
                });

                shakaPlayer.addEventListener('buffering', function(e) {
                    if (e.buffering) {
                        updatePlayStatus('loading', '缓冲中...');
                    } else {
                        updatePlayStatus('success', '正在播放');
                    }
                });

                shakaPlayer.load(url).then(function() {
                    console.log('Shaka Player 加载完成');
                    updatePlayStatus('success', '视频加载成功');
                    if (autoplay) videoEl.play().catch(() => {});
                }).catch(function(error) {
                    console.error('Shaka 加载错误:', error);
                    updatePlayStatus('error', '视频加载失败');
                });
            } else {
                updatePlayStatus('error', '当前浏览器不支持 Shaka Player');
            }
        }

        function initClappr(container, url) {
            const playerEl = document.createElement('div');
            playerEl.id = 'clappr-player';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            container.appendChild(playerEl);

            if (typeof Clappr === 'undefined') {
                updatePlayStatus('error', 'Clappr 加载失败');
                return;
            }

            player = new Clappr.Player({
                source: url,
                parentId: '#clappr-player',
                width: '100%',
                height: '100%',
                autoPlay: autoplay,
                preload: preload,
                poster: '',
                mediacontrol: {
                    seekbar: '#00d4ff',
                    buttons: '#00d4ff'
                },
                hlsjsConfig: hlsConfig
            });

            player.on(Clappr.Events.PLAYER_READY, function() {
                console.log('Clappr 加载完成');
                updatePlayStatus('success', '视频加载成功');
            });

            player.on(Clappr.Events.PLAYER_ERROR, function() {
                updatePlayStatus('error', '视频加载失败');
            });
        }

        function initDashJs(container, url) {
            const videoEl = document.createElement('video');
            videoEl.controls = true;
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            videoEl.preload = preload;
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.objectFit = 'contain';
            videoEl.style.background = '#000';
            container.appendChild(videoEl);

            if (typeof dashjs === 'undefined') {
                updatePlayStatus('error', 'dash.js 加载失败');
                return;
            }

            const dashPlayer = dashjs.MediaPlayer().create();
            player = dashPlayer;

            dashPlayer.updateSettings({
                streaming: {
                    abr: {
                        autoSwitchBitrate: {
                            video: true,
                            audio: true
                        }
                    },
                    buffer: {
                        fastSwitchEnabled: true
                    }
                }
            });

            dashPlayer.on(dashjs.MediaPlayer.events.STREAM_INITIALIZED, function() {
                console.log('dash.js 流初始化完成');
                updatePlayStatus('success', '视频加载成功');
                if (autoplay) videoEl.play().catch(() => {});
            });

            dashPlayer.on(dashjs.MediaPlayer.events.ERROR, function(e) {
                console.error('dash.js 错误:', e);
                updatePlayStatus('error', '播放错误');
            });

            if (url.indexOf('.m3u8') !== -1) {
                videoEl.src = url;
                updatePlayStatus('success', '视频加载成功');
                if (autoplay) videoEl.play().catch(() => {});
            } else {
                dashPlayer.initialize(videoEl, url, autoplay);
            }
        }

        function initJWPlayer(container, url) {
            if (typeof jwplayer === 'undefined') {
                showCommercialPlayerNotice();
                return;
            }

            const playerEl = document.createElement('div');
            playerEl.id = 'jwplayer-container';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            container.appendChild(playerEl);

            player = jwplayer('jwplayer-container').setup({
                file: url,
                width: '100%',
                height: '100%',
                autoplay: autoplay,
                preload: preload,
                skin: {
                    name: 'dark'
                }
            });

            player.on('ready', function() {
                console.log('JW Player 就绪');
                updatePlayStatus('success', '视频加载成功');
            });

            player.on('error', function(e) {
                updatePlayStatus('error', '播放错误: ' + (e.message || ''));
            });
        }

        function initBitmovin(container, url) {
            if (typeof bitmovin === 'undefined') {
                showCommercialPlayerNotice();
                return;
            }

            const playerEl = document.createElement('div');
            playerEl.id = 'bitmovin-player';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            container.appendChild(playerEl);

            const source = {
                hls: url.indexOf('.m3u8') !== -1 ? url : undefined,
                dash: url.indexOf('.mpd') !== -1 ? url : undefined,
                progressive: (url.indexOf('.mp4') !== -1 || url.indexOf('.webm') !== -1) ? url : undefined
            };

            if (!source.hls && !source.dash && !source.progressive) {
                source.hls = url;
            }

            const config = {
                key: '',
                source: source,
                playback: {
                    autoplay: autoplay,
                    muted: false
                },
                ui: {
                    enabled: true
                }
            };

            player = new bitmovin.player.Player(playerEl, config);

            player.on(bitmovin.player.PlayerEvent.Ready, function() {
                console.log('Bitmovin 就绪');
                updatePlayStatus('success', '视频加载成功');
            });

            player.on(bitmovin.player.PlayerEvent.Error, function(e) {
                updatePlayStatus('error', '播放错误: ' + (e.message || ''));
            });
        }

        function initTHEOplayer(container, url) {
            if (typeof THEOplayer === 'undefined') {
                showCommercialPlayerNotice();
                return;
            }

            const playerEl = document.createElement('div');
            playerEl.className = 'theoplayer-container';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            playerEl.style.background = '#000';
            container.appendChild(playerEl);

            const element = new THEOplayer.Player(playerEl, {
                libraryLocation: 'https://cdn.theoplayer.com/dash/theoplayer/',
                license: ''
            });

            player = element;

            const source = {
                sources: [{
                    src: url,
                    type: url.indexOf('.m3u8') !== -1 ? 'application/x-mpegurl' : (url.indexOf('.mpd') !== -1 ? 'application/dash+xml' : 'video/mp4')
                }]
            };

            element.source = source;
            element.autoplay = autoplay;

            element.addEventListener('playing', function() {
                updatePlayStatus('success', '正在播放');
            });

            element.addEventListener('error', function(e) {
                updatePlayStatus('error', '播放错误');
            });
        }

        function initFlowplayer(container, url) {
            if (typeof flowplayer === 'undefined') {
                showCommercialPlayerNotice();
                return;
            }

            const playerEl = document.createElement('div');
            playerEl.id = 'flowplayer-container';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            container.appendChild(playerEl);

            player = flowplayer('#flowplayer-container', {
                src: url,
                autoplay: autoplay,
                preload: preload,
                token: '',
                skin: 'minimalist'
            });

            player.on('ready', function() {
                console.log('Flowplayer 就绪');
                updatePlayStatus('success', '视频加载成功');
            });

            player.on('error', function(e) {
                updatePlayStatus('error', '播放错误');
            });
        }

        function initRadiant(container, url) {
            if (typeof RadiantMP === 'undefined') {
                showCommercialPlayerNotice();
                return;
            }

            const playerEl = document.createElement('div');
            playerEl.id = 'rmpPlayer';
            playerEl.style.width = '100%';
            playerEl.style.height = '100%';
            container.appendChild(playerEl);

            const settings = {
                licenseKey: '',
                src: {
                    hls: url
                },
                autoplay: autoplay,
                width: '100%',
                height: '100%',
                skin: 'dark'
            };

            const rmp = new RadiantMP('rmpPlayer');
            rmp.init(settings);
            player = { player: rmp, dispose: function() { rmp.destroy(); } };

            rmp.addEventListener('ready', function() {
                console.log('Radiant Media Player 就绪');
                updatePlayStatus('success', '视频加载成功');
            });

            rmp.addEventListener('error', function() {
                updatePlayStatus('error', '播放错误');
            });
        }

        document.getElementById('urlInput')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                playVideo();
            }
        });

        if (videoUrl) {
            if (isOfficialUrl) {
                handleOfficialUrl();
            } else {
                initPlayer(videoUrl);
            }
        }
    </script>
</body>
</html>
