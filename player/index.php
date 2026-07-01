<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$url = $_GET['url'] ?? '';
$playerType = $_GET['player'] ?? '';
$apiUrl = 'http://114.134.184.91:9002/mx.php?action=mxjx&url=';
$officialReplaceUrl = 'http://114.134.184.91:9002/mx.php?action=official_replace/info&url=';

$configFile = __DIR__ . '/../gz/player_config.php';
$playerConfig = [
    'player' => 'dplayer',
    'autoplay' => false,
    'preload' => 'auto',
];

if (file_exists($configFile)) {
    $fileConfig = require $configFile;
    $playerConfig = array_merge($playerConfig, $fileConfig);
}

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

$playerNameMap = [
    'dplayer' => 'DPlayer',
    'videojs' => 'Video.js',
    'muiplayer' => 'MuiPlayer',
    'artplayer' => 'ArtPlayer',
    'nplayer' => 'NPlayer',
];

$currentPlayerName = $playerNameMap[$playerConfig['player']] ?? 'DPlayer';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>在线视频播放器 - <?php echo $currentPlayerName; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.13/dist/hls.min.js"></script>
    <?php if ($playerConfig['player'] === 'dplayer'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
    <?php elseif ($playerConfig['player'] === 'videojs'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/video.js@8.12.0/dist/video-js.min.css">
    <script src="https://cdn.jsdelivr.net/npm/video.js@8.12.0/dist/video.min.js"></script>
    <?php elseif ($playerConfig['player'] === 'muiplayer'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mui-player@1.8.2/dist/mui-player.min.css">
    <script src="https://cdn.jsdelivr.net/npm/mui-player@1.8.2/dist/mui-player.min.js"></script>
    <?php elseif ($playerConfig['player'] === 'artplayer'): ?>
    <script src="https://cdn.jsdelivr.net/npm/artplayer@5.1.4/dist/artplayer.min.js"></script>
    <?php elseif ($playerConfig['player'] === 'nplayer'): ?>
    <script src="https://cdn.jsdelivr.net/npm/nplayer@1.0.15/dist/NPlayer.min.js"></script>
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
            background: #000;
        }

        #videoPlayer video {
            width: 100%;
            height: 100%;
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
                <option value="dplayer" <?php echo $playerConfig['player'] === 'dplayer' ? 'selected' : ''; ?>>DPlayer（推荐）</option>
                <option value="videojs" <?php echo $playerConfig['player'] === 'videojs' ? 'selected' : ''; ?>>Video.js</option>
                <option value="muiplayer" <?php echo $playerConfig['player'] === 'muiplayer' ? 'selected' : ''; ?>>MuiPlayer</option>
                <option value="artplayer" <?php echo $playerConfig['player'] === 'artplayer' ? 'selected' : ''; ?>>ArtPlayer</option>
                <option value="nplayer" <?php echo $playerConfig['player'] === 'nplayer' ? 'selected' : ''; ?>>NPlayer</option>
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
        const currentPlayer = <?php echo json_encode($playerConfig['player']); ?>;
        const autoplay = <?php echo json_encode($playerConfig['autoplay']); ?>;
        const preload = <?php echo json_encode($playerConfig['preload']); ?>;

        const hlsConfig = {
            enableWorker: true,
            lowLatencyMode: false,
            maxBufferLength: 30,
            maxMaxBufferLength: 600,
            minBufferLength: 2,
            maxBufferSize: 60 * 1000 * 1000,
            maxBufferHole: 0.5,
            highBufferWatchdogPeriod: 2,
            startLevel: -1,
            capLevelToPlayerSize: false,
            liveSyncDurationCount: 3,
            liveMaxLatencyDurationCount: 10,
            fragLoadingTimeOut: 20000,
            manifestLoadingTimeOut: 10000,
            levelLoadingTimeOut: 10000,
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
                hls.destroy();
                hls = null;
            }
            if (player) {
                try {
                    if (currentPlayer === 'dplayer' && player.destroy) player.destroy();
                    if (currentPlayer === 'videojs' && player.dispose) player.dispose();
                    if (currentPlayer === 'artplayer' && player.destroy) player.destroy();
                    if (currentPlayer === 'nplayer' && player.dispose) player.dispose();
                } catch (e) {}
                player = null;
            }
        }

        function reloadPlayer() {
            destroyPlayer();
            if (isOfficialUrl) {
                handleOfficialUrl();
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

        async function handleOfficialUrl() {
            if (!isOfficialUrl) {
                initPlayer(videoUrl);
                return;
            }
            const platformName = getPlatformName(originalUrl);
            document.getElementById('platformInfo').textContent = '✅ 检测到 ' + platformName + ' 链接';
            try {
                showToast('正在解析官解链接...', 'success');
                const response = await fetch(officialReplaceUrl + encodeURIComponent(originalUrl));
                const data = await response.json();
                if (data.success) {
                    if (data.video_title) {
                        document.getElementById('videoTitleLabel').style.display = 'block';
                        document.getElementById('videoTitle').style.display = 'block';
                        document.getElementById('videoTitle').textContent = data.video_title;
                    }
                    let playUrl = '';
                    if (data.ad_skip_url) {
                        playUrl = data.ad_skip_url;
                    } else if (data.m3u8_url) {
                        playUrl = apiUrl + encodeURIComponent(data.m3u8_url);
                    } else {
                        throw new Error('未获取到播放地址');
                    }
                    document.getElementById('playUrlLabel').style.display = 'block';
                    document.getElementById('playUrl').style.display = 'block';
                    document.getElementById('playUrl').textContent = playUrl;
                    document.getElementById('statusLabel').style.display = 'block';
                    document.getElementById('status').style.display = 'block';
                    document.getElementById('status').innerHTML = '<span style="color: #10b981;">✅ 解析成功</span>，正在加载播放器...';
                    showToast('官解链接解析成功！', 'success');
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

        function createHls(video, url) {
            if (hls) {
                hls.destroy();
            }
            if (Hls.isSupported()) {
                hls = new Hls(hlsConfig);
                hls.loadSource(url);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, function () {
                    console.log('HLS 加载完成');
                });
                hls.on(Hls.Events.ERROR, function (event, data) {
                    console.error('HLS 错误:', data);
                    if (data.fatal) {
                        switch (data.type) {
                            case Hls.ErrorTypes.NETWORK_ERROR:
                                console.log('网络错误，尝试恢复...');
                                hls.startLoad();
                                break;
                            case Hls.ErrorTypes.MEDIA_ERROR:
                                console.log('媒体错误，尝试恢复...');
                                hls.recoverMediaError();
                                break;
                            default:
                                showToast('视频加载失败，请检查链接是否正确', 'error');
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

        function initPlayer(url) {
            if (!url) return;

            const container = document.getElementById('videoPlayer');
            container.innerHTML = '';

            if (currentPlayer === 'dplayer') {
                initDPlayer(container, url);
            } else if (currentPlayer === 'videojs') {
                initVideoJs(container, url);
            } else if (currentPlayer === 'muiplayer') {
                initMuiPlayer(container, url);
            } else if (currentPlayer === 'artplayer') {
                initArtPlayer(container, url);
            } else if (currentPlayer === 'nplayer') {
                initNPlayer(container, url);
            } else {
                initDPlayer(container, url);
            }
        }

        function initDPlayer(container, url) {
            const videoEl = document.createElement('video');
            videoEl.playsInline = true;
            videoEl.webkitPlaysInline = true;
            container.appendChild(videoEl);

            player = new DPlayer({
                container: container,
                video: {
                    url: url,
                    type: 'customHls',
                    customType: {
                        customHls: function (video, dp) {
                            createHls(video, url);
                        }
                    }
                },
                autoplay: autoplay,
                theme: '#00d4ff',
                loop: false,
                lang: 'zh-cn',
                screenshot: true,
                hotkey: true,
                preload: preload,
                volume: 0.7,
                mutex: true,
            });

            player.on('error', function () {
                showToast('视频播放出错，请检查链接是否有效', 'error');
            });
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
