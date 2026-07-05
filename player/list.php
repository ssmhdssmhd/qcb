<?php
@ini_set('display_errors', 1);
@ini_set('html_errors', 1);
error_reporting(E_ALL);

$configFile = __DIR__ . '/player_config.php';
$players = [];

if (file_exists($configFile)) {
    $config = require $configFile;
    $players = $config['players'] ?? [];
}

if (empty($players)) {
    $players = [
        'dplayer' => ['name' => 'DPlayer', 'category' => '开源', 'description' => '优秀的开源 HTML5 弹幕视频播放器', 'type' => 'hls'],
        'videojs' => ['name' => 'Video.js', 'category' => '开源', 'description' => '最流行的开源 HTML5 视频播放器', 'type' => 'hls'],
        'shaka' => ['name' => 'Shaka Player', 'category' => '开源', 'description' => 'Google 开源的自适应码率流媒体播放器', 'type' => 'shaka'],
        'clappr' => ['name' => 'Clappr', 'category' => '开源', 'description' => '基于插件架构的开源 HTML5 播放器', 'type' => 'hls'],
        'dashjs' => ['name' => 'dash.js', 'category' => '开源', 'description' => 'DASH 行业论坛官方 MPEG-DASH 播放器', 'type' => 'dash'],
        'hlsjs' => ['name' => 'hls.js 原生', 'category' => '开源', 'description' => 'HLS.js + 原生 video 轻量播放器', 'type' => 'hls'],
        'muiplayer' => ['name' => 'MuiPlayer', 'category' => '开源', 'description' => '国人开发的 HTML5 视频播放器', 'type' => 'hls'],
        'artplayer' => ['name' => 'ArtPlayer', 'category' => '开源', 'description' => '现代化的 HTML5 视频播放器', 'type' => 'hls'],
        'nplayer' => ['name' => 'NPlayer', 'category' => '开源', 'description' => '支持弹幕的视频播放器', 'type' => 'hls'],
        'native' => ['name' => '原生 Video', 'category' => '系统', 'description' => '浏览器原生 video 标签播放', 'type' => 'native'],
        'jwplayer' => ['name' => 'JW Player', 'category' => '商业', 'description' => '流行的端到端视频解决方案', 'license_required' => true],
        'bitmovin' => ['name' => 'Bitmovin', 'category' => '商业', 'description' => '顶级视频流媒体技术提供商', 'license_required' => true],
        'theoplayer' => ['name' => 'THEOplayer', 'category' => '商业', 'description' => '获奖的视频播放器技术', 'license_required' => true],
        'flowplayer' => ['name' => 'Flowplayer', 'category' => '商业', 'description' => '轻量级全栈视频播放器方案', 'license_required' => true],
        'radiant' => ['name' => 'Radiant Media Player', 'category' => '商业', 'description' => '现代 HTML5 跨设备视频播放器', 'license_required' => true],
        'nexplayer' => ['name' => 'NexPlayer', 'category' => '商业', 'description' => '自主开发的 HLS/DASH 播放器', 'license_required' => true],
        'castlabs' => ['name' => 'castLabs PRESTOplay', 'category' => '商业', 'description' => '基于 Shaka 的商业播放器', 'license_required' => true],
        'visualon' => ['name' => 'VisualON', 'category' => '商业', 'description' => '主流播放器 SDK 提供商', 'license_required' => true],
    ];
}

$categories = [];
foreach ($players as $key => $p) {
    $cat = $p['category'] ?? '其他';
    if (!isset($categories[$cat])) {
        $categories[$cat] = [];
    }
    $categories[$cat][$key] = $p;
}

$demoUrl = isset($_GET['demo']) && !empty($_GET['demo']) ? $_GET['demo'] : 'https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>播放器集合 - 18种HTML5视频播放器</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
            color: #fff;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 40px 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            background: linear-gradient(90deg, #00d4ff, #7c3aed, #f472b6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .header .subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.5);
        }
        .header .count {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 16px;
            background: rgba(0, 212, 255, 0.15);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 20px;
            font-size: 13px;
            color: #00d4ff;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }
        .demo-input {
            background: #1a1a2e;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .demo-input label {
            display: block;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 10px;
        }
        .demo-input-row {
            display: flex;
            gap: 12px;
        }
        .demo-input input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
        }
        .demo-input input:focus {
            border-color: #00d4ff;
        }
        .demo-input button {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #00d4ff, #7c3aed);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .demo-input button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 212, 255, 0.3);
        }
        .category-section {
            margin-bottom: 40px;
        }
        .category-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .category-title .icon { font-size: 24px; }
        .category-title .count-badge {
            font-size: 12px;
            padding: 2px 10px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }
        .player-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .player-card {
            background: #1a1a2e;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .player-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #00d4ff, #7c3aed);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .player-card:hover {
            transform: translateY(-4px);
            border-color: rgba(0, 212, 255, 0.3);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }
        .player-card:hover::before {
            opacity: 1;
        }
        .player-card.commercial {
            border-color: rgba(245, 158, 11, 0.2);
        }
        .player-card.commercial::before {
            background: linear-gradient(90deg, #f59e0b, #ef4444);
        }
        .player-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .player-name {
            font-size: 17px;
            font-weight: 600;
            color: #fff;
        }
        .player-badge {
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 500;
        }
        .badge-opensource {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }
        .badge-commercial {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }
        .badge-system {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }
        .player-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.6;
            margin-bottom: 14px;
            min-height: 40px;
        }
        .player-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #00d4ff;
            text-decoration: none;
            font-weight: 500;
        }
        .player-link:hover {
            text-decoration: underline;
        }
        .footer {
            text-align: center;
            padding: 30px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .header h1 { font-size: 24px; }
            .container { padding: 20px; }
            .player-grid { grid-template-columns: 1fr; }
            .demo-input-row { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎬 HTML5 视频播放器集合</h1>
        <div class="subtitle">13 款流行 Web 视频播放器 + 5 款国产播放器，一键切换体验</div>
        <div class="count">共 <?php echo count($players); ?> 款播放器</div>
    </div>

    <div class="container">
        <div class="demo-input">
            <label for="demoUrl">🔗 测试视频地址（点击播放器卡片将使用此地址播放）</label>
            <div class="demo-input-row">
                <input type="text" id="demoUrl" value="<?php echo htmlspecialchars($demoUrl); ?>" placeholder="输入 M3U8/MP4 视频地址...">
                <button onclick="updateDemoUrl()">更新地址</button>
            </div>
        </div>

        <?php foreach ($categories as $category => $catPlayers): ?>
        <div class="category-section">
            <div class="category-title">
                <span class="icon">
                    <?php
                    $icons = ['开源' => '📦', '商业' => '💎', '系统' => '⚙️'];
                    echo $icons[$category] ?? '🎯';
                    ?>
                </span>
                <?php echo htmlspecialchars($category); ?>播放器
                <span class="count-badge"><?php echo count($catPlayers); ?> 款</span>
            </div>
            <div class="player-grid">
                <?php foreach ($catPlayers as $key => $p):
                    $isCommercial = !empty($p['license_required']);
                ?>
                <div class="player-card <?php echo $isCommercial ? 'commercial' : ''; ?>" onclick="openPlayer('<?php echo htmlspecialchars($key); ?>')">
                    <div class="player-card-header">
                        <div class="player-name"><?php echo htmlspecialchars($p['name']); ?></div>
                        <span class="player-badge <?php echo $isCommercial ? 'badge-commercial' : ($category === '系统' ? 'badge-system' : 'badge-opensource'); ?>">
                            <?php echo $isCommercial ? '商业' : ($category === '系统' ? '系统' : '开源'); ?>
                        </span>
                    </div>
                    <div class="player-desc"><?php echo htmlspecialchars($p['description'] ?? ''); ?></div>
                    <a class="player-link" href="javascript:void(0)" onclick="event.stopPropagation(); openPlayer('<?php echo htmlspecialchars($key); ?>')">
                        ▶️ 立即体验 →
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="footer">
        播放器集合 · 支持 M3U8 / MP4 / DASH 多种格式 · 基于 juejin.cn/post/7114115814462062628
    </div>

    <script>
        function updateDemoUrl() {
            const url = document.getElementById('demoUrl').value.trim();
            if (!url) {
                alert('请输入视频地址');
                return;
            }
            const params = new URLSearchParams(window.location.search);
            params.set('demo', url);
            window.location.search = params.toString();
        }

        function openPlayer(playerType) {
            const url = document.getElementById('demoUrl').value.trim();
            const target = 'index.php?player=' + encodeURIComponent(playerType) + '&url=' + encodeURIComponent(url);
            window.open(target, '_blank');
        }
    </script>
</body>
</html>
