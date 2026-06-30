<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8 广告分析后台</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/dplayer/1.27.1/DPlayer.min.css">
    <script src="https://cdn.bootcdn.net/ajax/libs/hls.js/1.5.15/hls.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/dplayer/1.27.1/DPlayer.min.js"></script>
    <style>
        :root {
            --primary: #667eea;
            --primary-light: #764ba2;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-bg: #ecf5ff;
            --primary-text: #409eff;
            --bg-page: #f5f7fa;
            --bg-card: #ffffff;
            --text-primary: #303133;
            --text-regular: #606266;
            --text-secondary: #909399;
            --text-placeholder: #c0c4cc;
            --border-base: #dcdfe6;
            --border-light: #e4e7ed;
            --border-lighter: #ebeef5;
            --fill-light: #fafafa;
            --fill-lighter: #f5f7fa;
            --success: #67c23a;
            --success-light: #f0f9eb;
            --success-border: #e1f3d8;
            --warning: #e6a23c;
            --warning-light: #fdf6ec;
            --danger: #f56c6c;
            --danger-light: #fef0f0;
            --danger-border: #fbc4c4;
            --shadow-base: 0 2px 12px rgba(0,0,0,0.05);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.1);
        }

        [data-theme="gold"] {
            --primary: #9f6d1d;
            --primary-light: #fff89c;
            --primary-gradient: linear-gradient(135deg, #9f6d1d 0%, #d4a017 100%);
            --primary-bg: #fdf6ec;
            --primary-text: #e6a23c;
            --success: #95c44a;
            --success-light: #f0f9eb;
            --success-border: #e1f3d8;
        }

        [data-theme="green"] {
            --primary: #217e25;
            --primary-light: #baff54;
            --primary-gradient: linear-gradient(135deg, #217e25 0%, #52c41a 100%);
            --primary-bg: #f0f9eb;
            --primary-text: #67c23a;
            --success: #52c41a;
        }

        [data-theme="blue"] {
            --primary: #171be1;
            --primary-light: #80f1ff;
            --primary-gradient: linear-gradient(135deg, #171be1 0%, #409eff 100%);
            --primary-bg: #ecf5ff;
            --primary-text: #409eff;
        }

        [data-theme="cyan"] {
            --primary: #03626c;
            --primary-light: #6efaff;
            --primary-gradient: linear-gradient(135deg, #03626c 0%, #13c2c2 100%);
            --primary-bg: #e6fffb;
            --primary-text: #13c2c2;
        }

        [data-theme="red"] {
            --primary: #c41d1d;
            --primary-light: #ff9c9c;
            --primary-gradient: linear-gradient(135deg, #c41d1d 0%, #f56c6c 100%);
            --primary-bg: #fff1f0;
            --primary-text: #f56c6c;
        }

        [data-theme="dark"] {
            --primary: #667eea;
            --primary-light: #764ba2;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-bg: rgba(102, 126, 234, 0.15);
            --primary-text: #85a5ff;
            --bg-page: #141414;
            --bg-card: #1f1f1f;
            --text-primary: #e8e8e8;
            --text-regular: #bfbfbf;
            --text-secondary: #8c8c8c;
            --text-placeholder: #595959;
            --border-base: #434343;
            --border-light: #303030;
            --border-lighter: #262626;
            --fill-light: #262626;
            --fill-lighter: #1f1f1f;
            --success: #52c41a;
            --success-light: rgba(82, 196, 26, 0.15);
            --success-border: rgba(82, 196, 26, 0.3);
            --warning: #faad14;
            --warning-light: rgba(250, 173, 20, 0.15);
            --danger: #ff4d4f;
            --danger-light: rgba(255, 77, 79, 0.15);
            --danger-border: rgba(255, 77, 79, 0.3);
            --shadow-base: 0 2px 12px rgba(0,0,0,0.3);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }
        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .header h1 { font-size: 24px; font-weight: 600; }
        .header p { opacity: 0.9; margin-top: 5px; font-size: 14px; }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .theme-switcher {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .theme-label {
            font-size: 13px;
            opacity: 0.9;
        }
        .theme-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.2s;
            position: relative;
        }
        .theme-dot:hover { transform: scale(1.15); border-color: white; }
        .theme-dot.active {
            border-color: white;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
        }
        .theme-dot.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .theme-dot[data-theme-name="default"] { background: linear-gradient(135deg, #667eea, #764ba2); }
        .theme-dot[data-theme-name="gold"] { background: linear-gradient(135deg, #9f6d1d, #d4a017); }
        .theme-dot[data-theme-name="green"] { background: linear-gradient(135deg, #217e25, #52c41a); }
        .theme-dot[data-theme-name="blue"] { background: linear-gradient(135deg, #171be1, #409eff); }
        .theme-dot[data-theme-name="cyan"] { background: linear-gradient(135deg, #03626c, #13c2c2); }
        .theme-dot[data-theme-name="red"] { background: linear-gradient(135deg, #c41d1d, #f56c6c); }
        .theme-dot[data-theme-name="dark"] { background: linear-gradient(135deg, #1f1f1f, #434343); }
        .nav {
            background: var(--bg-card);
            padding: 0 30px;
            display: flex;
            gap: 5px;
            border-bottom: 1px solid var(--border-light);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .nav::-webkit-scrollbar { display: none; }
        .nav-item {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
            color: var(--text-regular);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .nav-item:hover { color: var(--primary); }
        .nav-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 500;
        }
        .container { padding: 30px; }
        .page { display: none; }
        .page.active { display: block; }
        .card {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-base);
            transition: background 0.3s;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--text-primary);
        }
        .input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border-base);
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
            background: var(--bg-card);
            color: var(--text-primary);
        }
        .input-group input:focus { border-color: var(--primary); }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-sm {
            padding: 6px 14px;
            font-size: 12px;
        }
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .btn-secondary {
            background: var(--primary-bg);
            color: var(--primary-text);
        }
        .btn-secondary:hover { opacity: 0.85; }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { opacity: 0.9; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { opacity: 0.9; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-base);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-value.warning { color: var(--warning); -webkit-text-fill-color: var(--warning); background: none; }
        .stat-value.danger { color: var(--danger); -webkit-text-fill-color: var(--danger); background: none; }
        .stat-value.success { color: var(--success); -webkit-text-fill-color: var(--success); background: none; }
        .stat-label {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 6px;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--primary);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .segment-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid var(--border-lighter);
            border-radius: 6px;
        }
        .segment-item {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-lighter);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .segment-item:last-child { border-bottom: none; }
        .segment-item.ad {
            background: var(--danger-light);
            border-left: 3px solid var(--danger);
        }
        .segment-name { font-family: monospace; color: var(--text-primary); }
        .segment-duration { color: var(--text-secondary); }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 6px;
        }
        .tag-red { background: var(--danger-light); color: var(--danger); }
        .tag-blue { background: var(--primary-bg); color: var(--primary-text); }
        .tag-green { background: var(--success-light); color: var(--success); }
        .tag-orange { background: var(--warning-light); color: var(--warning); }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
        .jump-item {
            padding: 12px;
            background: var(--warning-light);
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--text-primary);
        }
        .jump-item .jump-arrow { color: var(--warning); font-weight: bold; }
        .rules-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rules-table th, .rules-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-lighter);
            font-size: 14px;
        }
        .rules-table th {
            background: var(--fill-light);
            color: var(--text-regular);
            font-weight: 500;
        }
        .rules-table tr:hover { background: var(--fill-light); }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: var(--text-regular);
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-base);
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            background: var(--bg-card);
            color: var(--text-primary);
        }
        .form-group textarea { min-height: 100px; font-family: monospace; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--primary);
        }
        .form-group input:disabled, .form-group textarea:disabled {
            background: var(--fill-lighter);
            color: var(--text-secondary);
            cursor: not-allowed;
        }
        .rule-section {
            border: 1px solid var(--border-lighter);
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .fast-mode-banner {
            background: var(--success-light);
            border: 1px solid var(--success-border);
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .fast-mode-banner .icon {
            font-size: 24px;
        }
        .fast-mode-banner .content {
            flex: 1;
        }
        .fast-mode-banner .title {
            font-weight: 600;
            color: var(--success);
            font-size: 15px;
            margin-bottom: 4px;
        }
        .fast-mode-banner .desc {
            color: var(--text-regular);
            font-size: 13px;
        }
        .fast-mode-banner .domain-tag {
            display: inline-block;
            background: var(--bg-card);
            padding: 2px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            color: var(--success);
            margin-left: 8px;
            border: 1px solid var(--success-border);
        }
        .rule-section-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: var(--text-placeholder);
        }
        .code-block {
            background: #282c34;
            color: #abb2bf;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 12px;
        }
        .tab-bar {
            display: flex;
            gap: 2px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border-light);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tab-bar::-webkit-scrollbar { display: none; }
        .tab-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            color: var(--text-regular);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tab-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        .toast.success { background: var(--success); }
        .toast.error { background: var(--danger); }
        .toast.info { background: var(--primary); }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            color: white;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }
        .copy-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .copy-btn:active {
            transform: scale(0.95);
        }
        .access-item {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }
        .access-item code {
            cursor: pointer;
            user-select: all;
            word-break: break-all;
        }
        .access-item code:hover {
            text-decoration: underline;
        }
        .bar-chart { display: flex; align-items: flex-end; gap: 2px; height: 120px; padding: 10px 0; }
        .bar {
            flex: 1;
            background: var(--primary-gradient);
            border-radius: 2px 2px 0 0;
            min-height: 2px;
            transition: all 0.3s;
        }
        .bar:hover { opacity: 0.8; }
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: var(--text-regular);
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 6px;
        }

        @media (max-width: 768px) {
            .header { padding: 16px 20px; }
            .header h1 { font-size: 18px; }
            .header p { font-size: 12px; }
            .header-actions { margin-top: 10px; }
            .theme-label { display: none; }
            .theme-dot { width: 20px; height: 20px; }
            .nav { padding: 0 16px; gap: 0; }
            .nav-item { padding: 12px 14px; font-size: 13px; }
            .container { padding: 16px; }
            .card { padding: 16px; margin-bottom: 16px; }
            .card-title { font-size: 15px; margin-bottom: 12px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 14px; }
            .stat-value { font-size: 22px; }
            .stat-label { font-size: 12px; }
            .input-group { flex-direction: column; }
            .input-group .btn { width: 100%; }
            .rules-table { font-size: 12px; }
            .rules-table th, .rules-table td { padding: 8px 6px; font-size: 12px; }
            .rules-table th:nth-child(n+4), .rules-table td:nth-child(n+4) { display: none; }
            .toast { left: 20px; right: 20px; text-align: center; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .card { padding: 12px; }
            .form-group { margin-bottom: 12px; }
            .form-group label { font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>M3U8 广告分析与规则管理后台</h1>
        <p>靶机测试工具 - 分析视频广告特征，管理域名去广告规则</p>
        <div class="header-actions">
            <div class="theme-switcher">
                <span class="theme-label">主题:</span>
                <div class="theme-dot active" data-theme-name="default" title="默认紫" onclick="switchTheme('default')"></div>
                <div class="theme-dot" data-theme-name="gold" title="金色" onclick="switchTheme('gold')"></div>
                <div class="theme-dot" data-theme-name="green" title="绿色" onclick="switchTheme('green')"></div>
                <div class="theme-dot" data-theme-name="blue" title="蓝色" onclick="switchTheme('blue')"></div>
                <div class="theme-dot" data-theme-name="cyan" title="青色" onclick="switchTheme('cyan')"></div>
                <div class="theme-dot" data-theme-name="red" title="红色" onclick="switchTheme('red')"></div>
                <div class="theme-dot" data-theme-name="dark" title="深色" onclick="switchTheme('dark')"></div>
            </div>
        </div>
    </div>

    <div class="nav">
        <div class="nav-item active" data-page="analyze">视频分析</div>
        <div class="nav-item" data-page="rules">规则管理</div>
        <div class="nav-item" data-page="sites">资源站管理</div>
        <div class="nav-item" data-page="official_sites">官采专区</div>
        <div class="nav-item" data-page="official_replace">官替管理</div>
        <div class="nav-item" data-page="play">在线播放</div>
        <div class="nav-item" data-page="update">系统更新</div>
        <div class="nav-item" data-page="auth">授权管理</div>
    </div>

    <div id="accessPreview" style="background:var(--primary-gradient);color:white;padding:20px 30px;font-size:13px">
        <div style="display:flex;flex-wrap:wrap;gap:20px">
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">后台管理</div>
                <div class="access-item">
                    <code id="preview-admin" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-admin').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">分析接口</div>
                <div class="access-item">
                    <code id="preview-api" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-api').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">网页播放器已去插播去广告接口</div>
                <div class="access-item">
                    <code id="preview-parse" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-parse').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">信息接口(JSON)</div>
                <div class="access-item">
                    <code id="preview-player" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-player').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">官替API接口</div>
                <div class="access-item">
                    <code id="preview-official-replace" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-official-replace').textContent)">复制</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page active" id="page-analyze">
            <div class="card">
                <div class="card-title">视频广告分析</div>
                <div class="input-group">
                    <input type="text" id="analyzeUrl" placeholder="输入 M3U8 视频链接，例如：https://example.com/video/index.m3u8"
                        value="https://v.lfthirtytwo.com/20260623/7885_1d9dba16/index.m3u8">
                    <button class="btn btn-primary" onclick="analyzeVideo()">开始分析</button>
                </div>
                <div style="font-size:12px;color:#909399">
                    提示：系统将自动检测 Master Playlist 并追踪到实际视频进行分析
                </div>
            </div>

            <div id="analyzeResult" style="display:none">
                <div id="fastModeBanner" style="display:none"></div>

                <div class="stats-grid" id="statsGrid"></div>

                <div class="detail-grid" id="detailGrid">
                    <div class="card">
                        <div class="card-title">序列号跳跃检测</div>
                        <div id="jumpList"></div>
                    </div>
                    <div class="card">
                        <div class="card-title">时长分布</div>
                        <div class="bar-chart" id="durationChart"></div>
                        <div class="legend">
                            <div class="legend-item"><div class="legend-color" style="background:linear-gradient(to top,#667eea,#764ba2)"></div>片段数量</div>
                        </div>
                        <div id="durationStats" style="margin-top:12px;font-size:13px;color:#606266"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">广告片段详情</div>
                    <div class="tab-bar">
                        <div class="tab-item active" onclick="switchSegmentTab(this, 'ad')">广告片段</div>
                        <div class="tab-item" onclick="switchSegmentTab(this, 'all')">全部片段</div>
                        <div class="tab-item" onclick="switchSegmentTab(this, 'cluster')">广告聚类</div>
                    </div>
                    <div class="segment-list" id="segmentList"></div>
                </div>

                <div class="card">
                    <div class="card-title">无广告播放链接</div>
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
                        <code id="analyzeMxjxUrl" style="background:#f5f7fa;padding:8px 12px;border-radius:4px;word-break:break-all;flex:1;min-width:200px;cursor:pointer" onclick="copyText(this.textContent)" title="点击复制"></code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText(document.getElementById('analyzeMxjxUrl').textContent)">复制链接</button>
                        <button class="btn btn-sm btn-primary" onclick="window.open(document.getElementById('analyzeMxjxUrl').textContent, '_blank')">新窗口播放</button>
                        <button class="btn btn-sm btn-success" onclick="playVideo()">内置播放器播放</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">操作</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="generateRules()">自动生成规则</button>
                        <button class="btn btn-success" onclick="goToRules()">查看规则管理</button>
                        <button class="btn btn-secondary" id="learnBtn" onclick="learnRules()">学习并更新规则</button>
                    </div>
                    <div id="learnStatus" style="margin-top:12px;font-size:13px;color:#606266;display:none"></div>
                </div>
            </div>
        </div>

        <div class="page" id="page-rules">
            <div class="card">
                <div class="card-title">域名规则列表</div>
                <div style="margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="showAddRule()">+ 新增规则</button>
                    <button class="btn btn-secondary" onclick="refreshRules()">刷新列表</button>
                    <button class="btn btn-secondary" onclick="exportAllRules()">导出全部规则</button>
                    <button class="btn btn-secondary" onclick="document.getElementById('importFileInput').click()">导入规则</button>
                    <input type="file" id="importFileInput" accept=".json" style="display:none" onchange="importRulesFromFile(event)">
                </div>
                <div id="rulesTable"></div>
            </div>

            <div class="card" id="ruleEditor" style="display:none">
                <div class="card-title" id="ruleEditorTitle">编辑规则</div>
                <div class="form-group">
                    <label>资源名称</label>
                    <input type="text" id="ruleName" placeholder="例如：芒果TV">
                </div>
                <div class="form-group">
                    <label>域名</label>
                    <input type="text" id="ruleDomain" placeholder="例如：v.lfthirtytwo.com">
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">时长规则</div>
                    <div id="durationRules"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addDurationRule()">+ 添加时长规则</button>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">DISCONTINUITY 规则</div>
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="discontinuityEnabled"> 启用 DISCONTINUITY 检测
                    </label>
                    <p style="font-size:12px;color:#909399;margin-top:6px">检测到 #EXT-X-DISCONTINUITY 标记时，标记该片段为广告插播点</p>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">序列号跳跃规则</div>
                    <div id="sequenceJumpRules"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addSeqJumpRule()">+ 添加序列号跳跃规则</button>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">文件名模式</div>
                    <div id="filenamePatterns"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addFilenamePattern()">+ 添加文件名模式</button>
                </div>

                <div class="form-group">
                    <label>备注</label>
                    <textarea id="ruleNote" placeholder="规则说明备注"></textarea>
                </div>

                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveRule()">保存规则</button>
                    <button class="btn btn-secondary" onclick="cancelRuleEdit()">取消</button>
                </div>
            </div>
        </div>

        <div class="page" id="page-sites">
            <div class="card">
                <div class="card-title">自动学习配置</div>
                <div class="stats-grid" id="autoLearnStats">
                    <div class="stat-card">
                        <div class="stat-value" id="totalSites">-</div>
                        <div class="stat-label">资源站总数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="activeSites">-</div>
                        <div class="stat-label">活跃资源站</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="lastLearnTime">-</div>
                        <div class="stat-label">上次学习时间</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="autoLearnStatus">-</div>
                        <div class="stat-label">自动学习状态</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>启用自动学习</label>
                        <select id="autoLearnEnabled">
                            <option value="true">启用</option>
                            <option value="false">禁用</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>更新间隔 (天)</label>
                        <input type="number" id="intervalDays" min="1" max="30" value="3">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>每站视频数</label>
                        <input type="number" id="videosPerSite" min="1" max="20" value="5">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>每次最大站点数</label>
                        <input type="number" id="maxSitesPerRun" min="1" max="20" value="5">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>最小片段数</label>
                        <input type="number" id="minSegments" min="10" max="500" value="50">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>最大广告占比 (%)</label>
                        <input type="number" id="maxAdPercentage" min="10" max="100" value="90">
                    </div>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="saveAutoLearnConfig()">保存配置</button>
                    <button class="btn btn-success" onclick="runAutoLearn()">立即执行学习</button>
                    <button class="btn btn-secondary" onclick="refreshSites()">刷新</button>
                </div>
                <div id="autoLearnResult" style="margin-top:16px;display:none"></div>
            </div>

            <div class="card">
                <div class="card-title">搜索影视学习</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">搜索指定或热门影视名称，查看返回的M3U8视频链接，用对应的视频链接进行学习，用M3U8的域名进行学习更新规则。</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
                    <input type="text" id="searchKeyword" placeholder="输入影视名称，如：流浪地球、庆余年..." style="flex:1;min-width:250px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px">
                    <select id="searchSiteSelect" style="padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px;min-width:150px">
                        <option value="all">全部资源站</option>
                    </select>
                    <input type="number" id="searchMaxSites" value="5" min="1" max="20" placeholder="最大站点数" style="width:120px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px">
                    <button class="btn btn-primary" onclick="searchVideos()">🔍 搜索</button>
                    <button class="btn btn-secondary" onclick="clearSearchResults()">清空</button>
                </div>
                <div id="searchResults" style="display:none">
                    <div id="searchSummary" style="padding:10px 12px;background:#ecf5ff;border:1px solid #d9ecff;border-radius:6px;margin-bottom:12px;font-size:13px;color:#409eff"></div>
                    <div id="searchActions" style="display:none;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                        <button class="btn btn-success" onclick="batchLearnAll()">📚 一键学习全部</button>
                        <button class="btn btn-primary" onclick="batchAnalyzeAll()">🔍 一键分析全部</button>
                        <span style="font-size:12px;color:#909399;margin-left:auto" id="searchStats">准备就绪</span>
                    </div>
                    <div id="batchResult" style="display:none;margin-bottom:12px;padding:12px;border-radius:6px"></div>
                    <div id="searchVideoList"></div>
                </div>
                <div id="searchLoading" style="display:none;text-align:center;padding:20px;color:#909399">
                    <div class="loading" style="display:inline-block">正在搜索中，请稍候...</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    资源站列表
                    <span style="margin-left:12px;font-size:12px;color:#909399;font-weight:normal">共 <span id="sitesCount">0</span> 个资源站</span>
                </div>
                <div style="margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="showAddSite()">+ 新增资源站</button>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" id="showPaused" onchange="refreshSites()"> 显示已暂停
                    </label>
                    <input type="text" id="siteSearch" placeholder="搜索资源站名称..." style="flex:1;min-width:200px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px" oninput="filterSites()">
                </div>
                <div id="sitesTable"></div>
            </div>

            <div class="card" id="siteEditor" style="display:none">
                <div class="card-title" id="siteEditorTitle">新增资源站</div>
                <div class="form-group">
                    <label>资源站名称</label>
                    <input type="text" id="siteName" placeholder="例如：量子">
                </div>
                <div class="form-group">
                    <label>官网地址</label>
                    <input type="text" id="siteUrl" placeholder="例如：https://example.com">
                </div>
                <div class="form-group">
                    <label>采集接口</label>
                    <input type="text" id="siteApiUrl" placeholder="例如：https://example.com/api.php/provide/vod/">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                    <div class="form-group">
                        <label>类型</label>
                        <select id="siteType">
                            <option value="maccms">MacCMS</option>
                            <option value="custom">自定义</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>状态</label>
                        <select id="siteStatus">
                            <option value="active">正常</option>
                            <option value="paused">暂停</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>优先级</label>
                        <input type="number" id="sitePriority" min="1" max="99" value="50">
                    </div>
                </div>
                <div class="form-group">
                    <label>扩展备注</label>
                    <textarea id="siteNote" placeholder="备注信息，如：推荐、卡、停更等"></textarea>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveSite()">保存</button>
                    <button class="btn btn-secondary" onclick="cancelSiteEdit()">取消</button>
                </div>
            </div>

            <div class="card" id="siteVideos" style="display:none">
                <div class="card-title">
                    <span id="siteVideosTitle">视频列表</span>
                    <button class="btn btn-sm btn-secondary" style="float:right" onclick="closeSiteVideos()">关闭</button>
                </div>
                <div id="siteVideosList"></div>
            </div>
        </div>

        <div class="page" id="page-official_sites">
            <div class="card">
                <div class="card-title" style="display:flex;justify-content:space-between;align-items:center">
                    <span>官采资源站列表</span>
                    <div style="display:flex;gap:10px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-regular)">
                            <input type="checkbox" id="officialSitesEnabled" onchange="toggleOfficialSites()">
                            启用官采专区
                        </label>
                        <button class="btn btn-sm btn-primary" onclick="showAddOfficialSite()">+ 添加官采站</button>
                    </div>
                </div>
                <div id="officialSitesList"></div>
            </div>

            <div class="card">
                <div class="card-title">官采专区设置</div>
                <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
                    <div class="form-group">
                        <label>自动切换域名</label>
                        <select id="osAutoSwitch">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>每域名最大重试次数</label>
                        <input type="number" id="osMaxRetry" value="2" min="0" max="10">
                    </div>
                    <div class="form-group">
                        <label>请求超时（秒）</label>
                        <input type="number" id="osTimeout" value="10" min="5" max="60">
                    </div>
                    <div class="form-group">
                        <label>默认每页条数</label>
                        <input type="number" id="osDefaultLimit" value="20" min="5" max="100">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="saveOfficialSettings()">保存设置</button>
            </div>

            <div class="card" id="officialSiteVideos" style="display:none">
                <div class="card-title">
                    <span id="officialSiteVideoTitle">视频列表</span>
                    <button class="btn btn-sm btn-secondary" style="float:right" onclick="closeOfficialSiteVideos()">关闭</button>
                </div>
                <div style="margin-bottom:12px;display:flex;gap:10px">
                    <input type="text" id="officialVideoSearch" placeholder="搜索关键词..." style="flex:1;padding:10px 14px;border:1px solid var(--border-base);border-radius:6px;outline:none">
                    <button class="btn btn-primary" onclick="searchOfficialVideos()">搜索</button>
                    <button class="btn btn-secondary" onclick="refreshOfficialVideos()">刷新</button>
                </div>
                <div id="officialSiteVideosList"></div>
            </div>
        </div>

        <div class="page" id="page-official_replace">
            <div class="card">
                <div class="card-title">官替 API 配置</div>
                <div class="stats-grid" id="officialReplaceStats">
                    <div class="stat-card">
                        <div class="stat-value" id="orTotalPlatforms">-</div>
                        <div class="stat-label">支持平台数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orStatus">-</div>
                        <div class="stat-label">功能状态</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orSearchSites">-</div>
                        <div class="stat-label">搜索资源站</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orThreshold">-</div>
                        <div class="stat-label">匹配阈值</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>启用官替功能</label>
                        <select id="orEnabled">
                            <option value="true">启用</option>
                            <option value="false">禁用</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>匹配阈值 (0-100)</label>
                        <input type="number" id="orThresholdInput" min="0" max="100" value="60">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>最大搜索站点数</label>
                        <input type="number" id="orMaxSites" min="1" max="20" value="5">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>搜索资源站 (逗号分隔)</label>
                        <input type="text" id="orSearchSitesInput" placeholder="留空表示搜索全部">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="saveOfficialReplaceConfig()">保存配置</button>
            </div>

            <div class="card">
                <div class="card-title">
                    <span>支持平台列表</span>
                    <button class="btn btn-sm btn-primary" style="float:right" onclick="addOfficialPlatform()">+ 添加平台</button>
                </div>
                <div id="officialPlatformsList"></div>
            </div>

            <div class="card">
                <div class="card-title">官替 API 测试</div>
                <div class="input-group">
                    <input type="text" id="officialTestUrl" placeholder="输入官方视频链接，如：https://v.qq.com/x/cover/xxx.html">
                    <button class="btn btn-primary" onclick="testOfficialReplace()">测试解析</button>
                </div>
                <div style="font-size:12px;color:#909399;margin-bottom:16px">
                    支持：腾讯视频、爱奇艺、优酷、芒果TV、哔哩哔哩 等平台
                </div>
                <div id="officialTestResult" style="display:none">
                    <div id="officialTestInfo"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">API 接口说明</div>
                <div style="font-size:13px;line-height:1.8;color:#606266">
                    <p><strong>完整解析接口：</strong></p>
                    <div style="background:#f5f7fa;padding:12px;border-radius:6px;margin:8px 0">
                        <code id="api-resolve-url"></code>
                    </div>
                    <p><strong>精简信息接口 (JSON)：</strong></p>
                    <div style="background:#f5f7fa;padding:12px;border-radius:6px;margin:8px 0">
                        <code id="api-info-url"></code>
                    </div>
                    <p><strong>参数说明：</strong></p>
                    <ul style="margin-left:20px">
                        <li><code>url</code> - 官方视频播放页面链接</li>
                    </ul>
                    <p><strong>返回示例：</strong></p>
                    <pre style="background:#f5f7fa;padding:12px;border-radius:6px;overflow:auto;font-size:12px">{
  "success": true,
  "platform": "腾讯视频",
  "video_title": "庆余年",
  "match_score": 95.5,
  "site": "量子",
  "m3u8_url": "https://.../index.m3u8",
  "ad_skip_url": "https://你的域名/mx.php?action=mxjx&url=...",
  "episodes": 42
}</pre>
                </div>
            </div>
        </div>

        <div class="page" id="page-play">
            <div class="card">
                <div class="card-title">无广告播放测试</div>
                <div class="input-group">
                    <input type="text" id="playUrl" placeholder="输入 M3U8 视频链接">
                    <button class="btn btn-primary" onclick="playVideo()">播放</button>
                </div>
                <div id="playerContainer" style="display:none;margin-top:20px">
                    <div id="dplayer" style="width:100%;border-radius:8px;overflow:hidden"></div>
                    <div style="margin-top:12px;font-size:13px;color:#606266" id="playInfo"></div>
                </div>
            </div>
        </div>

        <div class="page" id="page-update">
            <div class="card">
                <div class="card-title">系统版本信息</div>
                <div class="stats-grid" id="versionStats">
                    <div class="stat-card">
                        <div class="stat-value" id="currentVersion">-</div>
                        <div class="stat-label">当前版本</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="latestVersion">-</div>
                        <div class="stat-label">最新版本</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success" id="updateStatus">检查中...</div>
                        <div class="stat-label">更新状态</div>
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="checkUpdate()">检查更新</button>
                    <button class="btn btn-success" id="updateBtn" onclick="doUpdate()" disabled>立即更新</button>
                    <button class="btn btn-secondary" onclick="createBackup()">创建备份</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">缓存清理</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">清理浏览器缓存、Service Worker、localStorage 等，解决更新后页面不生效问题。</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-warning" onclick="clearAllCaches()">立即清理缓存</button>
                    <button class="btn btn-secondary" onclick="clearBrowserCache()">清理浏览器缓存</button>
                    <button class="btn btn-secondary" onclick="clearServiceWorker()">清理 Service Worker</button>
                </div>
                <div id="cacheClearResult" style="margin-top:12px;font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">完整性检查</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">检查授权文件、核心文件完整性和系统状态。</p>
                <button class="btn btn-primary" onclick="checkIntegrity()">检查系统完整性</button>
                <div id="integrityResult" style="margin-top:12px;font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">更新结果</div>
                <div id="updateResult" style="font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">备份管理</div>
                <div id="backupList"></div>
            </div>
        </div>

        <div class="page" id="page-auth">
            <div class="card">
                <div class="card-title">授权状态</div>
                <div class="stats-grid" id="authStats">
                    <div class="stat-card">
                        <div class="stat-value" id="sqFileStatus">检查中...</div>
                        <div class="stat-label">授权文件</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="localAuthStatus">检查中...</div>
                        <div class="stat-label">本地验证</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="remoteAuthStatus">检查中...</div>
                        <div class="stat-label">远程验证</div>
                    </div>
                </div>
                <div id="authDetails" style="margin-top:16px;font-size:13px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">授权配置</div>
                <div class="form-group">
                    <label>授权服务器 IP</label>
                    <input type="text" id="authServerIp" placeholder="例如：114.134.184.91">
                </div>
                <div class="form-group">
                    <label>授权服务器端口</label>
                    <input type="text" id="authServerPort" placeholder="例如：9001">
                </div>
                <div class="form-group">
                    <label>授权文件名</label>
                    <input type="text" id="authFile" placeholder="例如：sq.php">
                </div>
                <div class="form-group">
                    <label>对比文件名</label>
                    <input type="text" id="authFileCompare" placeholder="例如：sqm.php">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="enableRemoteVerify"> 启用远程验证
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="enableTimestampCheck"> 启用时间戳检查
                    </label>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveAuthConfig()">保存配置</button>
                    <button class="btn btn-secondary" onclick="refreshAuthInfo()">刷新状态</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">设置授权码</div>
                <div class="form-group">
                    <label>输入授权码</label>
                    <textarea id="authCodeInput" placeholder="请输入授权码..."></textarea>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-success" onclick="setAuthCode()">设置授权码</button>
                    <button class="btn btn-secondary" onclick="generateAuthCode()">生成测试授权码</button>
                </div>
                <div style="margin-top:12px;font-size:12px;color:#909399">
                    授权异常或需要授权请联系 QQ: 2094332348
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer"></div>

    <script>
        const API_BASE = (function() {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const path = window.location.pathname;
            const baseDir = path.substring(0, path.lastIndexOf('/'));
            return protocol + '//' + host + baseDir + '/mx.php';
        })();
        let currentAnalysis = null;
        let currentSegmentTab = 'ad';
        let editingRules = null;
        let dp = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.textContent = message;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                item.classList.add('active');
                const pageId = 'page-' + item.dataset.page;
                document.getElementById(pageId).classList.add('active');
                const page = item.dataset.page;
                if (page === 'rules') refreshRules();
                if (page === 'auth') refreshAuthInfo();
                if (page === 'update') { checkUpdate(); loadVersion(); loadBackupList(); }
            });
        });

        async function analyzeVideo() {
            const url = document.getElementById('analyzeUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '分析中...';
            document.getElementById('analyzeResult').style.display = 'none';
            try {
                const res = await fetch(API_BASE + '?action=analyze&url=' + encodeURIComponent(url));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                currentAnalysis = data;
                renderAnalysis(data);
                document.getElementById('analyzeResult').style.display = 'block';
                showToast('分析完成', 'success');
            } catch (e) {
                showToast('分析失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '开始分析';
            }
        }

        function renderAnalysis(data) {
            const url = document.getElementById('analyzeUrl').value.trim();
            const mxjxUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            const analyzeMxjxEl = document.getElementById('analyzeMxjxUrl');
            if (analyzeMxjxEl) {
                analyzeMxjxEl.textContent = mxjxUrl;
            }

            const bannerEl = document.getElementById('fastModeBanner');
            const detailGrid = document.getElementById('detailGrid');
            const segmentCard = document.querySelector('#page-analyze .card:nth-of-type(4)');
            const actionCard = document.querySelector('#page-analyze .card:nth-of-type(5)');

            if (data.fastMode) {
                bannerEl.style.display = 'flex';
                bannerEl.innerHTML = `
                    <div class="icon">⚡</div>
                    <div class="content">
                        <div class="title">快速模式 - 已有域名规则<span class="domain-tag">${data.domain}</span></div>
                        <div class="desc">${data.message || '检测到已有域名规则，使用规则快速去广告，无需重复分析'}</div>
                    </div>
                    <button class="btn btn-sm btn-secondary" onclick="goToRules()">查看规则</button>
                `;
                if (detailGrid) detailGrid.style.display = 'none';
                if (segmentCard) segmentCard.style.display = 'none';
            } else {
                bannerEl.style.display = 'none';
                if (detailGrid) detailGrid.style.display = 'grid';
                if (segmentCard) segmentCard.style.display = 'block';
            }

            const stats = data.stats;
            const pct = stats.totalSegments > 0 ? (stats.adSegments / stats.totalSegments * 100).toFixed(1) : 0;

            if (data.fastMode) {
                document.getElementById('statsGrid').innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${stats.totalSegments}</div>
                        <div class="stat-label">总片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value danger">${stats.adSegments}</div>
                        <div class="stat-label">广告片段数 (${pct}%)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.keptSegments}</div>
                        <div class="stat-label">保留片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value warning">${(stats.savedDuration / 60).toFixed(1)}分钟</div>
                        <div class="stat-label">节省时长</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.adPercentage}%</div>
                        <div class="stat-label">广告占比</div>
                    </div>
                `;
            } else {
                document.getElementById('statsGrid').innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${stats.totalSegments}</div>
                        <div class="stat-label">总片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value danger">${stats.adSegments}</div>
                        <div class="stat-label">广告片段数 (${pct}%)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value warning">${stats.discontinuityCount}</div>
                        <div class="stat-label">DISCONTINUITY 标记</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${stats.sequenceJumpCount}</div>
                        <div class="stat-label">序列号跳跃</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.adClusterCount}</div>
                        <div class="stat-label">广告聚类</div>
                    </div>
                `;
            }

            if (data.fastMode) return;

            const jumps = data.sequenceJumps;
            const jumpHtml = jumps.length === 0
                ? '<div class="empty">未检测到明显序列号跳跃</div>'
                : jumps.map(j => `
                    <div class="jump-item">
                        <div style="font-family:monospace;font-size:11px;color:#909399;margin-bottom:4px">
                            索引 ${j.index}
                        </div>
                        <div>
                            <span style="font-family:monospace">${basename(j.prevUri)}</span>
                            <span class="jump-arrow"> → </span>
                            <span style="font-family:monospace">${basename(j.currentUri)}</span>
                        </div>
                        <div style="margin-top:4px;color:#e6a23c">
                            跳跃: ${j.jump > 0 ? '+' : ''}${j.jump} (${j.prevSeq} → ${j.currentSeq})
                        </div>
                    </div>
                `).join('');
            document.getElementById('jumpList').innerHTML = jumpHtml;

            const dist = data.durationDistribution;
            if (dist && dist.buckets) {
                const buckets = Object.entries(dist.buckets).sort((a, b) => parseFloat(a[0]) - parseFloat(b[0]));
                const maxCount = Math.max(...buckets.map(b => b[1]));
                const chartHtml = buckets.map(([dur, count]) => {
                    const h = (count / maxCount * 100);
                    return `<div class="bar" style="height:${h}%" title="时长: ${dur}s, 数量: ${count}"></div>`;
                }).join('');
                document.getElementById('durationChart').innerHTML = chartHtml;
                document.getElementById('durationStats').innerHTML = `
                    最短: ${dist.min.toFixed(2)}s | 最长: ${dist.max.toFixed(2)}s | 平均: ${dist.avg.toFixed(2)}s
                `;
            }

            renderSegmentList();

            const learnStatusEl = document.getElementById('learnStatus');
            const learnBtn = document.getElementById('learnBtn');
            if (learnStatusEl && learnBtn) {
                if (data.fastMode) {
                    learnBtn.style.display = 'none';
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 快速模式：已有域名规则，直接使用规则去广告</span>（学习次数: ' + (data.learn_count || 0) + '次）';
                } else if (data.autoLearned) {
                    learnBtn.style.display = 'none';
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 自动学习完成，规则已更新</span>（学习次数: ' + (data.learn_count || 0) + '次）';
                } else {
                    learnBtn.style.display = 'inline-block';
                    learnStatusEl.style.display = 'none';
                }
            }
        }

        function basename(path) {
            return path.split('/').pop();
        }

        function switchSegmentTab(el, tab) {
            document.querySelectorAll('#page-analyze .tab-item').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            currentSegmentTab = tab;
            renderSegmentList();
        }

        function renderSegmentList() {
            if (!currentAnalysis) return;
            const listEl = document.getElementById('segmentList');

            if (currentSegmentTab === 'ad') {
                const ads = currentAnalysis.adSegments;
                if (ads.length === 0) {
                    listEl.innerHTML = '<div class="empty">未检测到广告片段</div>';
                    return;
                }
                listEl.innerHTML = ads.map(s => `
                    <div class="segment-item ad">
                        <div>
                            <span class="segment-name">${basename(s.segment.uri)}</span>
                            ${s.matchedRules.map(r => `<span class="tag tag-red">${r.name}</span>`).join('')}
                        </div>
                        <span class="segment-duration">${s.segment.duration.toFixed(2)}s</span>
                    </div>
                `).join('');
            } else if (currentSegmentTab === 'cluster') {
                const clusters = currentAnalysis.adClusters;
                if (clusters.length === 0) {
                    listEl.innerHTML = '<div class="empty">无广告聚类</div>';
                    return;
                }
                let totalAdDuration = 0;
                currentAnalysis.adSegments.forEach(s => totalAdDuration += s.segment.duration);
                listEl.innerHTML = clusters.map((c, i) => {
                    const segSubset = currentAnalysis.adSegments.filter(s => s.index >= c.start && s.index <= c.end);
                    const clusterDuration = segSubset.reduce((sum, s) => sum + s.segment.duration, 0);
                    return `
                        <div class="segment-item ad">
                            <div>
                                <span class="tag tag-red">聚类 #${i + 1}</span>
                                <span style="margin-left:8px">索引 ${c.start} - ${c.end}</span>
                            </div>
                            <span class="segment-duration">${c.count}个片段 / ${clusterDuration.toFixed(2)}s</span>
                        </div>
                    `;
                }).join('');
            } else {
                const segs = currentAnalysis.allSegments;
                listEl.innerHTML = segs.map(s => `
                    <div class="segment-item ${s.isAd ? 'ad' : ''}">
                        <div>
                            <span style="color:#909399;font-size:11px;margin-right:8px">#${s.index}</span>
                            <span class="segment-name">${basename(s.segment.uri)}</span>
                            ${s.isAd ? s.matchedRules.map(r => `<span class="tag tag-red">${r.name}</span>`).join('') : ''}
                            ${s.segment.discontinuity ? '<span class="tag tag-orange">DISCON</span>' : ''}
                        </div>
                        <span class="segment-duration">${s.segment.duration.toFixed(2)}s</span>
                    </div>
                `).join('');
            }
        }

        async function generateRules() {
            if (!currentAnalysis) { showToast('请先分析视频', 'error'); return; }
            try {
                const res = await fetch(API_BASE + '?action=rules/generate&url=' + encodeURIComponent(currentAnalysis.url));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                editingRules = data.rules;
                document.querySelector('.nav-item[data-page="rules"]').click();
                showRuleEditor(data.rules, true);
                showToast('规则已生成，请编辑保存', 'success');
            } catch (e) {
                showToast('生成规则失败: ' + e.message, 'error');
            }
        }

        async function learnRules() {
            if (!currentAnalysis) { showToast('请先分析视频', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '学习中...';
            try {
                const res = await fetch(API_BASE + '?action=rules/learn&url=' + encodeURIComponent(currentAnalysis.url));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                const learnStatusEl = document.getElementById('learnStatus');
                if (learnStatusEl) {
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 学习完成，规则已更新</span>（学习次数: ' + data.learn_count + '次）';
                }
                btn.style.display = 'none';
                showToast('规则学习成功', 'success');
            } catch (e) {
                showToast('学习失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '学习并更新规则';
            }
        }

        function goToRules() {
            document.querySelector('.nav-item[data-page="rules"]').click();
        }

        function playVideo() {
            const playPageActive = document.querySelector('.nav-item[data-page="play"]')?.classList.contains('active');
            let url;
            if (playPageActive) {
                url = document.getElementById('playUrl')?.value?.trim();
            } else {
                url = document.getElementById('analyzeUrl')?.value?.trim() || document.getElementById('playUrl')?.value?.trim();
            }
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            document.querySelector('.nav-item[data-page="play"]').click();
            document.getElementById('playUrl').value = url;
            const mxjxUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            document.getElementById('playerContainer').style.display = 'block';
            document.getElementById('playInfo').innerHTML = `
                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
                    <span style="color:#606266">无广告链接:</span>
                    <code id="playMxjxUrl" style="background:#f5f7fa;padding:4px 8px;border-radius:4px;word-break:break-all;flex:1;min-width:200px;cursor:pointer" onclick="copyText('${mxjxUrl}')" title="点击复制">${mxjxUrl}</code>
                    <button class="btn btn-sm btn-secondary" onclick="copyText('${mxjxUrl}')">复制链接</button>
                    <button class="btn btn-sm btn-primary" onclick="window.open('${mxjxUrl}', '_blank')">新窗口打开</button>
                </div>
                <div id="playStatus" style="margin-top:8px;color:#909399;font-size:12px">正在加载视频...</div>
            `;

            if (dp) {
                try { dp.destroy(); } catch(e) {}
                dp = null;
            }

            if (typeof Hls === 'undefined') {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">错误: hls.js 加载失败，请检查网络或刷新页面</span>';
                showToast('hls.js 加载失败', 'error');
                return;
            }
            if (typeof DPlayer === 'undefined') {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">错误: DPlayer 加载失败，请检查网络或刷新页面</span>';
                showToast('DPlayer 加载失败', 'error');
                return;
            }

            try {
                dp = new DPlayer({
                    container: document.getElementById('dplayer'),
                    video: {
                        url: mxjxUrl,
                        type: 'customHls',
                        customType: {
                            customHls: function(video, player) {
                                if (Hls.isSupported()) {
                                    const hls = new Hls({
                                        xhrSetup: function(xhr, url) {
                                            xhr.withCredentials = false;
                                        },
                                        startLevel: -1,
                                        preloadFile: 3,
                                        lowLatencyMode: false,
                                        backBufferLength: 30,
                                        maxBufferLength: 60,
                                        maxMaxBufferLength: 120,
                                        enableWorker: true,
                                        fragLoadingTimeOut: 20000,
                                        manifestLoadingTimeOut: 10000
                                    });
                                    hls.loadSource(video.src);
                                    hls.attachMedia(video);
                                    player.hls = hls;

                                    hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频解析完成，正在加载...</span>';
                                        if (player.video && player.video.paused) {
                                            player.video.play().catch(function() {
                                                document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频已加载，点击播放按钮开始播放</span>';
                                            });
                                        }
                                    });

                                    hls.on(Hls.Events.ERROR, function(event, data) {
                                        if (data.fatal) {
                                            let errMsg = '视频加载失败';
                                            switch (data.type) {
                                                case Hls.ErrorTypes.NETWORK_ERROR:
                                                    errMsg = '网络错误: ' + (data.details || '无法加载视频资源');
                                                    break;
                                                case Hls.ErrorTypes.MEDIA_ERROR:
                                                    errMsg = '媒体错误: ' + (data.details || '视频解码失败');
                                                    try {
                                                        hls.recoverMediaError();
                                                        errMsg += '，正在尝试恢复...';
                                                    } catch(e) {}
                                                    break;
                                                default:
                                                    errMsg = '播放错误: ' + (data.details || data.type);
                                            }
                                            statusUpdated = true;
                                            document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">' + errMsg + '</span>';
                                            showToast(errMsg, 'error');
                                        }
                                    });
                                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                    video.src = mxjxUrl;
                                }
                            }
                        },
                        preload: 'auto'
                    },
                    autoplay: true,
                    muted: false,
                    theme: '#667eea',
                    lang: 'zh-cn',
                    screenshot: true,
                    volume: 0.7,
                    playbackSpeed: [0.5, 0.75, 1, 1.25, 1.5, 2],
                    mutex: true
                });

                let statusUpdated = false;

                dp.on('loadstart', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频加载中...</span>';
                    }
                });

                dp.on('loadedmetadata', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频元数据加载完成，正在缓冲...</span>';
                    }
                });

                dp.on('loadeddata', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频首帧已加载，准备播放...</span>';
                    }
                });

                dp.on('canplay', function() {
                    statusUpdated = true;
                    document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">视频加载成功，正在播放...</span>';
                    if (dp && dp.video && dp.video.paused) {
                        dp.video.play().catch(function() {
                            document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频已加载，点击播放按钮开始播放</span>';
                        });
                    }
                });

                dp.on('playing', function() {
                    statusUpdated = true;
                    document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                });

                dp.on('pause', function() {
                    if (statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#909399">已暂停</span>';
                    }
                });

                dp.on('waiting', function() {
                    if (statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">缓冲中...</span>';
                    }
                });

                dp.on('error', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">播放器错误，请检查视频链接</span>';
                        showToast('播放器错误，请检查视频链接', 'error');
                    }
                });

                setTimeout(function() {
                    if (!statusUpdated && dp && dp.video && dp.video.readyState >= 2) {
                        statusUpdated = true;
                        document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">视频加载成功，点击播放按钮开始播放</span>';
                        if (dp.video.paused) {
                            dp.video.play().catch(function() {});
                        }
                    }
                }, 3000);
            } catch (e) {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">播放器初始化失败: ' + e.message + '</span>';
                showToast('播放失败: ' + e.message, 'error');
            }
        }

        async function refreshRules() {
            try {
                const res = await fetch(API_BASE + '?action=rules/list&_t=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseErr) {
                    console.error('规则列表响应解析失败:', text.substring(0, 500));
                    throw new Error('服务器返回非JSON数据: ' + text.substring(0, 100));
                }
                if (!data.success) throw new Error(data.message || '获取失败');
                renderRulesTable(data.rules);
            } catch (e) {
                console.error('获取规则列表错误:', e);
                showToast('获取规则列表失败: ' + e.message, 'error');
            }
        }

        function renderRulesTable(rules) {
            const domains = Object.keys(rules);
            if (domains.length === 0) {
                document.getElementById('rulesTable').innerHTML = '<div class="empty">暂无域名规则</div>';
                return;
            }
            let html = '<table class="rules-table"><thead><tr><th>资源名称</th><th>域名</th><th>时长规则</th><th>DISCON规则</th><th>序列号跳跃</th><th>学习次数</th><th>更新时间</th><th>操作</th></tr></thead><tbody>';
            for (const domain of domains) {
                const r = rules[domain];
                const name = escapeHtml(r.name || domain);
                const durCount = (r.duration_rules || []).filter(x => x.enabled).length;
                const disCount = (r.discontinuity_rules || []).filter(x => x.enabled).length;
                const seqCount = (r.sequence_jump_rules || []).filter(x => x.enabled).length;
                const learnCount = r.learn_count || 0;
                const mtime = r.last_learn_date || r.analysis_date || (r._filemtime ? new Date(r._filemtime * 1000).toLocaleString() : '-');
                html += `
                    <tr>
                        <td><span style="color:#606266">${name}</span></td>
                        <td><strong>${escapeHtml(domain)}</strong></td>
                        <td><span class="tag tag-blue">${durCount}条</span></td>
                        <td>${disCount > 0 ? '<span class="tag tag-orange">启用</span>' : '<span style="color:#c0c4cc">未启用</span>'}</td>
                        <td>${seqCount > 0 ? '<span class="tag tag-red">' + seqCount + '条</span>' : '<span style="color:#c0c4cc">无</span>'}</td>
                        <td><span class="tag tag-green">${learnCount}次</span></td>
                        <td style="color:#909399;font-size:12px">${mtime}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="exportSingleRule('${escapeHtml(domain)}')">导出</button>
                            <button class="btn btn-sm btn-secondary" onclick="editRule('${escapeHtml(domain)}')">编辑</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRule('${escapeHtml(domain)}')">删除</button>
                        </td>
                    </tr>
                `;
            }
            html += '</tbody></table>';
            document.getElementById('rulesTable').innerHTML = html;
        }

        function showAddRule() {
            editingRules = {
                domain: '',
                name: '',
                duration_rules: [{ name: 'short_segment', enabled: true, type: 'duration', operator: '<', threshold: 2, reason: '极短片段 (<2秒) 可能是广告', weight: 30 }],
                discontinuity_rules: [{ name: 'discontinuity', enabled: false, type: 'discontinuity', reason: 'DISCONTINUITY 标记表示插播切换', weight: 80 }],
                sequence_jump_rules: [],
                filename_patterns: [],
                note: ''
            };
            showRuleEditor(editingRules, true);
        }

        async function editRule(domain) {
            try {
                const res = await fetch(API_BASE + '?action=rules/get&domain=' + encodeURIComponent(domain));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                editingRules = data.rules;
                showRuleEditor(data.rules, false);
            } catch (e) {
                showToast('获取规则失败: ' + e.message, 'error');
            }
        }

        function showRuleEditor(rules, isNew) {
            document.getElementById('ruleEditor').style.display = 'block';
            document.getElementById('ruleEditorTitle').textContent = isNew ? '新增规则' : '编辑规则';
            document.getElementById('ruleName').value = rules.name || '';
            document.getElementById('ruleDomain').value = rules.domain || '';
            document.getElementById('ruleDomain').disabled = !isNew;
            document.getElementById('ruleNote').value = rules.note || '';

            const disRules = rules.discontinuity_rules || [];
            const disEnabled = disRules.length > 0 && disRules[0].enabled;
            document.getElementById('discontinuityEnabled').checked = disEnabled;

            renderDurationRules(rules.duration_rules || []);
            renderSeqJumpRules(rules.sequence_jump_rules || []);
            renderFilenamePatterns(rules.filename_patterns || []);
        }

        function cancelRuleEdit() {
            document.getElementById('ruleEditor').style.display = 'none';
            editingRules = null;
        }

        function renderDurationRules(rules) {
            const container = document.getElementById('durationRules');
            if (rules.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无时长规则</div>';
                return;
            }
            container.innerHTML = rules.map((r, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="checkbox" ${r.enabled ? 'checked' : ''} onchange="editingRules.duration_rules[${i}].enabled = this.checked">
                    <select onchange="editingRules.duration_rules[${i}].operator = this.value">
                        <option value="<" ${r.operator === '<' ? 'selected' : ''}><</option>
                        <option value=">" ${r.operator === '>' ? 'selected' : ''}>></option>
                        <option value="<=" ${r.operator === '<=' ? 'selected' : ''}><=</option>
                        <option value=">=" ${r.operator === '>=' ? 'selected' : ''}>>=</option>
                        <option value="==" ${r.operator === '==' ? 'selected' : ''}>==</option>
                    </select>
                    <input type="number" step="0.1" value="${r.threshold}" style="width:80px;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.duration_rules[${i}].threshold = parseFloat(this.value)">
                    <span style="color:#909399">秒</span>
                    <input type="text" value="${r.reason || ''}" placeholder="说明" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.duration_rules[${i}].reason = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeDurationRule(${i})">删除</button>
                </div>
            `).join('');
        }

        function addDurationRule() {
            if (!editingRules) editingRules = { duration_rules: [] };
            if (!editingRules.duration_rules) editingRules.duration_rules = [];
            editingRules.duration_rules.push({
                name: 'duration_rule_' + Date.now(),
                enabled: true,
                type: 'duration',
                operator: '<',
                threshold: 2,
                reason: '',
                weight: 30
            });
            renderDurationRules(editingRules.duration_rules);
        }

        function removeDurationRule(idx) {
            editingRules.duration_rules.splice(idx, 1);
            renderDurationRules(editingRules.duration_rules);
        }

        function renderSeqJumpRules(rules) {
            const container = document.getElementById('sequenceJumpRules');
            if (rules.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无序列号跳跃规则</div>';
                return;
            }
            container.innerHTML = rules.map((r, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="checkbox" ${r.enabled ? 'checked' : ''} onchange="editingRules.sequence_jump_rules[${i}].enabled = this.checked">
                    <select onchange="editingRules.sequence_jump_rules[${i}].direction = this.value">
                        <option value="forward" ${r.direction === 'forward' ? 'selected' : ''}>向前跳跃</option>
                        <option value="backward" ${r.direction === 'backward' ? 'selected' : ''}>向后跳跃</option>
                        <option value="any" ${r.direction === 'any' ? 'selected' : ''}>任意方向</option>
                    </select>
                    <span style="color:#909399">阈值</span>
                    <input type="number" value="${r.threshold}" style="width:100px;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.sequence_jump_rules[${i}].threshold = parseInt(this.value)">
                    <input type="text" value="${r.reason || ''}" placeholder="说明" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.sequence_jump_rules[${i}].reason = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeSeqJumpRule(${i})">删除</button>
                </div>
            `).join('');
        }

        function addSeqJumpRule() {
            if (!editingRules) editingRules = { sequence_jump_rules: [] };
            if (!editingRules.sequence_jump_rules) editingRules.sequence_jump_rules = [];
            editingRules.sequence_jump_rules.push({
                name: 'seq_jump_' + Date.now(),
                enabled: true,
                type: 'sequence_jump',
                direction: 'forward',
                threshold: 100000,
                reason: '',
                weight: 90
            });
            renderSeqJumpRules(editingRules.sequence_jump_rules);
        }

        function removeSeqJumpRule(idx) {
            editingRules.sequence_jump_rules.splice(idx, 1);
            renderSeqJumpRules(editingRules.sequence_jump_rules);
        }

        function renderFilenamePatterns(patterns) {
            const container = document.getElementById('filenamePatterns');
            if (patterns.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无文件名模式</div>';
                return;
            }
            container.innerHTML = patterns.map((p, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="text" value="${p}" placeholder="正则模式，例如：/ad/i" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px;font-family:monospace"
                        onchange="editingRules.filename_patterns[${i}] = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeFilenamePattern(${i})">删除</button>
                </div>
            `).join('');
        }

        function addFilenamePattern() {
            if (!editingRules) editingRules = { filename_patterns: [] };
            if (!editingRules.filename_patterns) editingRules.filename_patterns = [];
            editingRules.filename_patterns.push('');
            renderFilenamePatterns(editingRules.filename_patterns);
        }

        function removeFilenamePattern(idx) {
            editingRules.filename_patterns.splice(idx, 1);
            renderFilenamePatterns(editingRules.filename_patterns);
        }

        async function saveRule() {
            const domain = document.getElementById('ruleDomain').value.trim();
            if (!domain) { showToast('请输入域名', 'error'); return; }
            if (!editingRules) editingRules = {};
            editingRules.domain = domain;
            editingRules.name = document.getElementById('ruleName').value.trim();
            editingRules.note = document.getElementById('ruleNote').value;
            const disEnabled = document.getElementById('discontinuityEnabled').checked;
            if (!editingRules.discontinuity_rules || editingRules.discontinuity_rules.length === 0) {
                editingRules.discontinuity_rules = [{
                    name: 'discontinuity',
                    enabled: disEnabled,
                    type: 'discontinuity',
                    reason: 'DISCONTINUITY 标记表示插播切换',
                    weight: 80
                }];
            } else {
                editingRules.discontinuity_rules[0].enabled = disEnabled;
            }
            try {
                const res = await fetch(API_BASE + '?action=rules/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domain: domain, rules: editingRules })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('规则保存成功', 'success');
                cancelRuleEdit();
                refreshRules();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function deleteRule(domain) {
            if (!confirm('确定要删除该域名的规则吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=rules/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domain: domain })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('规则已删除', 'success');
                refreshRules();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function exportAllRules() {
            try {
                const res = await fetch(API_BASE + '?action=rules/export&download=1&_t=' + Date.now());
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'all_rules.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                showToast('导出成功', 'success');
            } catch (e) {
                showToast('导出失败: ' + e.message, 'error');
            }
        }

        async function exportSingleRule(domain) {
            try {
                const res = await fetch(API_BASE + '?action=rules/export&domain=' + encodeURIComponent(domain) + '&download=1&_t=' + Date.now());
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'rules_' + domain + '.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                showToast('导出成功', 'success');
            } catch (e) {
                showToast('导出失败: ' + e.message, 'error');
            }
        }

        async function importRulesFromFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                const text = await file.text();
                const importData = JSON.parse(text);
                const res = await fetch(API_BASE + '?action=rules/import', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(importData)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast(data.message || '导入成功', 'success');
                refreshRules();
            } catch (e) {
                showToast('导入失败: ' + e.message, 'error');
            } finally {
                event.target.value = '';
            }
        }

        async function checkUpdate() {
            try {
                const res = await fetch(API_BASE + '?action=update/check');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                document.getElementById('currentVersion').textContent = data.current_version;
                document.getElementById('latestVersion').textContent = data.latest_commit?.substring(0, 7) || '-';
                
                const statusEl = document.getElementById('updateStatus');
                const updateBtn = document.getElementById('updateBtn');
                
                if (data.has_update) {
                    statusEl.textContent = '有新版本';
                    statusEl.className = 'stat-value warning';
                    updateBtn.disabled = false;
                } else {
                    statusEl.textContent = '已是最新';
                    statusEl.className = 'stat-value success';
                    updateBtn.disabled = true;
                }
                
                showToast('检查更新完成', 'success');
            } catch (e) {
                document.getElementById('updateStatus').textContent = '检查失败';
                document.getElementById('updateStatus').className = 'stat-value danger';
                showToast('检查更新失败: ' + e.message, 'error');
            }
        }

        async function loadVersion() {
            try {
                const res = await fetch(API_BASE + '?action=update/version');
                const data = await res.json();
                if (data.success) {
                    document.getElementById('currentVersion').textContent = data.current_version;
                }
            } catch (e) {}
            loadBackupList();
        }

        async function createBackup() {
            try {
                const res = await fetch(API_BASE + '?action=update/backup/create');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份创建成功: ' + data.filename, 'success');
                loadBackupList();
            } catch (e) {
                showToast('创建备份失败: ' + e.message, 'error');
            }
        }

        async function loadBackupList() {
            try {
                const res = await fetch(API_BASE + '?action=update/backup/list');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                const backups = data.backups || [];
                const container = document.getElementById('backupList');
                
                if (backups.length === 0) {
                    container.innerHTML = '<div class="empty">暂无备份文件</div>';
                    return;
                }
                
                container.innerHTML = `
                    <table class="rules-table">
                        <thead>
                            <tr>
                                <th>备份文件名</th>
                                <th>大小</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${backups.map(b => `
                                <tr>
                                    <td><strong>${b.filename}</strong></td>
                                    <td>${b.size_formatted}</td>
                                    <td style="color:#909399;font-size:12px">${b.created_formatted}</td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" onclick="restoreBackup('${b.filename}')">恢复</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBackup('${b.filename}')">删除</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } catch (e) {
                document.getElementById('backupList').innerHTML = '<div class="empty">加载备份列表失败</div>';
            }
        }

        async function restoreBackup(filename) {
            if (!confirm('确定要恢复该备份吗？当前数据将被覆盖。')) return;
            try {
                const res = await fetch(API_BASE + '?action=update/backup/restore', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename: filename })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份恢复成功', 'success');
            } catch (e) {
                showToast('恢复失败: ' + e.message, 'error');
            }
        }

        async function deleteBackup(filename) {
            if (!confirm('确定要删除该备份吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=update/backup/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename: filename })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份已删除', 'success');
                loadBackupList();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function clearBrowserCache() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理浏览器缓存...</span>';
            try {
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                }
                resultEl.innerHTML = '<span style="color:#67c23a">浏览器缓存已清理</span>';
                showToast('浏览器缓存已清理', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function clearServiceWorker() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理 Service Worker...</span>';
            try {
                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(registrations.map(reg => reg.unregister()));
                }
                resultEl.innerHTML = '<span style="color:#67c23a">Service Worker 已清理</span>';
                showToast('Service Worker 已清理', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function clearAllCaches() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理所有缓存...</span>';
            const logs = [];
            try {
                try {
                    const res = await fetch(API_BASE + '?action=update/clear_cache');
                    const data = await res.json();
                    if (data.success) {
                        logs.push('服务器 PHP 缓存: 已清理');
                    } else {
                        logs.push('服务器 PHP 缓存: 清理失败 - ' + (data.message || '未知错误'));
                    }
                } catch (e) {
                    logs.push('服务器 PHP 缓存: 清理失败 - ' + e.message);
                }

                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    logs.push('浏览器缓存: 已清理');
                }
                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(registrations.map(reg => reg.unregister()));
                    logs.push('Service Worker: 已清理');
                }
                localStorage.removeItem('m3u8_rules_cache');
                sessionStorage.clear();
                logs.push('localStorage/sessionStorage: 已清理');

                const meta = document.createElement('meta');
                meta.httpEquiv = 'Cache-Control';
                meta.content = 'no-cache, no-store, must-revalidate';
                document.head.appendChild(meta);
                logs.push('页面缓存策略: 已禁用');

                resultEl.innerHTML = logs.map(l => '<div style="color:#67c23a">' + l + '</div>').join('');
                showToast('所有缓存已清理完成', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function doUpdate() {
            if (!confirm('确定要更新系统吗？更新前会自动创建备份，并自动清理差异文件。')) return;
            const btn = document.getElementById('updateBtn');
            btn.disabled = true;
            btn.textContent = '更新中...';
            const statusEl = document.getElementById('updateResult') || document.createElement('div');
            try {
                statusEl.innerHTML = '<div style="color:#409eff">正在验证授权...</div>';
                const authRes = await fetch(API_BASE + '?action=update/integrity');
                const authData = await authRes.json();
                if (!authData.auth_valid) {
                    throw new Error('授权验证失败: ' + (authData.issues?.join(', ') || '未知错误'));
                }
                statusEl.innerHTML += '<div style="color:#67c23a">授权验证通过</div>';
                statusEl.innerHTML += '<div style="color:#409eff">正在下载更新...</div>';
                const res = await fetch(API_BASE + '?action=update/download');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                statusEl.innerHTML += '<div style="color:#67c23a">更新完成</div>';
                if (data.cleaned_files && data.cleaned_files.length > 0) {
                    statusEl.innerHTML += '<div style="color:#e6a23c">清理了 ' + data.cleaned_files.length + ' 个差异文件:</div>';
                    data.cleaned_files.forEach(f => {
                        statusEl.innerHTML += '<div style="color:#909399;font-size:11px">' + escapeHtml(f) + '</div>';
                    });
                }
                if (data.integrity_check) {
                    if (data.integrity_check.success) {
                        statusEl.innerHTML += '<div style="color:#67c23a">完整性检查通过</div>';
                    } else {
                        statusEl.innerHTML += '<div style="color:#f56c6c">完整性检查发现问题:</div>';
                        data.integrity_check.issues.forEach(i => {
                            statusEl.innerHTML += '<div style="color:#f56c6c;font-size:11px">' + escapeHtml(i) + '</div>';
                        });
                    }
                }
                showToast('更新成功！备份: ' + data.backup_file, 'success');
                statusEl.innerHTML += '<div style="color:#409eff">正在清理缓存...</div>';
                await clearAllCaches();
                statusEl.innerHTML += '<div style="color:#67c23a">缓存已清理，2秒后刷新页面...</div>';
                setTimeout(() => location.reload(true), 2000);
            } catch (e) {
                statusEl.innerHTML = '<div style="color:#f56c6c">更新失败: ' + escapeHtml(e.message) + '</div>';
                showToast('更新失败: ' + e.message, 'error');
                btn.disabled = false;
                btn.textContent = '立即更新';
            }
        }

        async function checkIntegrity() {
            const statusEl = document.getElementById('integrityResult');
            statusEl.innerHTML = '<span style="color:#409eff">正在检查...</span>';
            try {
                const res = await fetch(API_BASE + '?action=update/integrity');
                const data = await res.json();
                if (data.success) {
                    statusEl.innerHTML = '<span style="color:#67c23a">完整性检查通过</span>';
                    showToast('系统完整性正常', 'success');
                } else {
                    const issues = data.issues || [];
                    statusEl.innerHTML = '<div style="color:#f56c6c">发现 ' + issues.length + ' 个问题:<br>' +
                        issues.map(i => '<div style="font-size:12px">' + escapeHtml(i) + '</div>').join('') + '</div>';
                    showToast('完整性检查发现问题', 'error');
                }
            } catch (e) {
                statusEl.innerHTML = '<span style="color:#f56c6c">检查失败: ' + escapeHtml(e.message) + '</span>';
            }
        }

        async function refreshAuthInfo() {
            try {
                const res = await fetch(API_BASE + '?action=auth/info');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                const sqStatusEl = document.getElementById('sqFileStatus');
                const localStatusEl = document.getElementById('localAuthStatus');
                const remoteStatusEl = document.getElementById('remoteAuthStatus');
                
                if (data.sq_file_exists) {
                    sqStatusEl.textContent = '存在';
                    sqStatusEl.className = 'stat-value success';
                } else {
                    sqStatusEl.textContent = '不存在';
                    sqStatusEl.className = 'stat-value danger';
                }
                
                if (data.local_valid) {
                    localStatusEl.textContent = '通过';
                    localStatusEl.className = 'stat-value success';
                } else {
                    localStatusEl.textContent = '失败';
                    localStatusEl.className = 'stat-value danger';
                }
                
                remoteStatusEl.textContent = '未检查';
                remoteStatusEl.className = 'stat-value warning';
                
                let details = '';
                if (data.domain) {
                    details += `<div>授权域名: <strong>${data.domain}</strong></div>`;
                }
                if (data.timestamp_formatted) {
                    details += `<div>授权时间: ${data.timestamp_formatted}</div>`;
                }
                document.getElementById('authDetails').innerHTML = details;
                
                if (data.auth_config) {
                    document.getElementById('authServerIp').value = data.auth_config.auth_server_ip || '';
                    document.getElementById('authServerPort').value = data.auth_config.auth_server_port || '';
                    document.getElementById('authFile').value = data.auth_config.auth_file || '';
                    document.getElementById('authFileCompare').value = data.auth_config.auth_file_compare || '';
                    document.getElementById('enableRemoteVerify').checked = data.auth_config.enable_remote_verify ?? true;
                    document.getElementById('enableTimestampCheck').checked = data.auth_config.enable_timestamp_check ?? true;
                }
            } catch (e) {
                showToast('获取授权信息失败: ' + e.message, 'error');
            }
        }

        async function saveAuthConfig() {
            const config = {
                auth_server_ip: document.getElementById('authServerIp').value.trim(),
                auth_server_port: document.getElementById('authServerPort').value.trim(),
                auth_file: document.getElementById('authFile').value.trim(),
                auth_file_compare: document.getElementById('authFileCompare').value.trim(),
                enable_remote_verify: document.getElementById('enableRemoteVerify').checked,
                enable_timestamp_check: document.getElementById('enableTimestampCheck').checked
            };
            try {
                const res = await fetch(API_BASE + '?action=auth/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ config: config })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('配置保存成功', 'success');
                refreshAuthInfo();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function setAuthCode() {
            const authCode = document.getElementById('authCodeInput').value.trim();
            if (!authCode) {
                showToast('请输入授权码', 'error');
                return;
            }
            try {
                const res = await fetch(API_BASE + '?action=auth/set', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ auth_code: authCode })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('授权码设置成功', 'success');
                refreshAuthInfo();
            } catch (e) {
                showToast('设置失败: ' + e.message, 'error');
            }
        }

        async function generateAuthCode() {
            const domain = prompt('请输入授权域名:', 'localhost');
            if (!domain) return;
            try {
                const res = await fetch(API_BASE + '?action=auth/generate&domain=' + encodeURIComponent(domain));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                document.getElementById('authCodeInput').value = data.auth_code;
                showToast('授权码已生成', 'success');
            } catch (e) {
                showToast('生成失败: ' + e.message, 'error');
            }
        }

        let currentSites = [];
        let editingSite = null;

        async function refreshSites() {
            try {
                const includePaused = document.getElementById('showPaused')?.checked ? '1' : '0';
                const res = await fetch(API_BASE + '?action=sites/list&include_paused=' + includePaused + '&_t=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                currentSites = data.sites || [];
                renderSitesTable(currentSites);
                renderAutoLearnStats(data);
                populateSearchSiteSelect();
                document.getElementById('sitesCount').textContent = currentSites.length;
            } catch (e) {
                console.error('获取资源站列表错误:', e);
                showToast('获取资源站列表失败: ' + e.message, 'error');
            }
        }

        function renderAutoLearnStats(data) {
            const allSites = currentSites.length;
            const activeCount = currentSites.filter(s => s.status === 'active').length;
            document.getElementById('totalSites').textContent = allSites;
            document.getElementById('activeSites').textContent = activeCount;
            document.getElementById('lastLearnTime').textContent = data.last_learn_time || '从未学习';

            const statusEl = document.getElementById('autoLearnStatus');
            const config = data.auto_learn_config || {};
            if (config.enabled) {
                if (data.should_auto_learn) {
                    statusEl.textContent = '待执行';
                    statusEl.className = 'stat-value warning';
                } else {
                    statusEl.textContent = '运行中';
                    statusEl.className = 'stat-value success';
                }
            } else {
                statusEl.textContent = '已禁用';
                statusEl.className = 'stat-value danger';
            }

            document.getElementById('autoLearnEnabled').value = config.enabled ? 'true' : 'false';
            document.getElementById('intervalDays').value = config.interval_days ?? 3;
            document.getElementById('videosPerSite').value = config.videos_per_site ?? 5;
            document.getElementById('maxSitesPerRun').value = config.max_sites_per_run ?? 5;
            document.getElementById('minSegments').value = config.min_segments ?? 50;
            document.getElementById('maxAdPercentage').value = config.max_ad_percentage ?? 90;
        }

        function filterSites() {
            const keyword = document.getElementById('siteSearch').value.trim().toLowerCase();
            if (!keyword) {
                renderSitesTable(currentSites);
                return;
            }
            const filtered = currentSites.filter(s =>
                s.name.toLowerCase().includes(keyword) ||
                (s.note || '').toLowerCase().includes(keyword)
            );
            renderSitesTable(filtered);
        }

        function renderSitesTable(sites) {
            const container = document.getElementById('sitesTable');
            if (sites.length === 0) {
                container.innerHTML = '<div class="empty">暂无资源站</div>';
                return;
            }
            let html = '<table class="rules-table"><thead><tr><th>优先级</th><th>名称</th><th>官网</th><th>采集接口</th><th>状态</th><th>扩展备注</th><th>操作</th></tr></thead><tbody>';
            for (const site of sites) {
                const priority = site.priority || 99;
                const statusTag = site.status === 'active'
                    ? '<span class="tag tag-green">正常</span>'
                    : '<span class="tag tag-orange">暂停</span>';
                const siteUrl = site.site_url || '#';
                const apiUrl = site.api_url || '';
                const note = escapeHtml(site.note || '');
                html += `
                    <tr>
                        <td><span class="tag tag-blue">${priority}</span></td>
                        <td><strong>${escapeHtml(site.name)}</strong></td>
                        <td>
                            ${site.site_url ? `<a href="${escapeHtml(siteUrl)}" target="_blank" style="color:#409eff;text-decoration:none;font-size:12px">访问官网 ↗</a>` : '-'}
                        </td>
                        <td style="font-size:12px;color:#909399;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${escapeHtml(apiUrl)}">
                            ${escapeHtml(apiUrl)}
                        </td>
                        <td>${statusTag}</td>
                        <td style="font-size:12px;color:#606266;max-width:150px">${note || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="fetchSiteVideos('${escapeHtml(site.name)}')">视频</button>
                            <button class="btn btn-sm btn-secondary" onclick="editSite('${escapeHtml(site.name)}')">编辑</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSite('${escapeHtml(site.name)}')">删除</button>
                        </td>
                    </tr>
                `;
            }
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function showAddSite() {
            editingSite = null;
            document.getElementById('siteEditorTitle').textContent = '新增资源站';
            document.getElementById('siteName').value = '';
            document.getElementById('siteUrl').value = '';
            document.getElementById('siteApiUrl').value = '';
            document.getElementById('siteType').value = 'maccms';
            document.getElementById('siteStatus').value = 'active';
            document.getElementById('sitePriority').value = 50;
            document.getElementById('siteNote').value = '';
            document.getElementById('siteName').disabled = false;
            document.getElementById('siteEditor').style.display = 'block';
            document.getElementById('siteEditor').scrollIntoView({ behavior: 'smooth' });
        }

        async function editSite(name) {
            try {
                const res = await fetch(API_BASE + '?action=sites/get&name=' + encodeURIComponent(name));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                editingSite = data.site;
                document.getElementById('siteEditorTitle').textContent = '编辑资源站';
                document.getElementById('siteName').value = data.site.name || '';
                document.getElementById('siteUrl').value = data.site.site_url || '';
                document.getElementById('siteApiUrl').value = data.site.api_url || '';
                document.getElementById('siteType').value = data.site.type || 'maccms';
                document.getElementById('siteStatus').value = data.site.status || 'active';
                document.getElementById('sitePriority').value = data.site.priority || 50;
                document.getElementById('siteNote').value = data.site.note || '';
                document.getElementById('siteName').disabled = true;
                document.getElementById('siteEditor').style.display = 'block';
                document.getElementById('siteEditor').scrollIntoView({ behavior: 'smooth' });
            } catch (e) {
                showToast('获取资源站失败: ' + e.message, 'error');
            }
        }

        function cancelSiteEdit() {
            document.getElementById('siteEditor').style.display = 'none';
            editingSite = null;
        }

        async function saveSite() {
            const name = document.getElementById('siteName').value.trim();
            const siteUrl = document.getElementById('siteUrl').value.trim();
            const apiUrl = document.getElementById('siteApiUrl').value.trim();
            const type = document.getElementById('siteType').value;
            const status = document.getElementById('siteStatus').value;
            const priority = parseInt(document.getElementById('sitePriority').value) || 50;
            const note = document.getElementById('siteNote').value.trim();

            if (!name) { showToast('请输入资源站名称', 'error'); return; }
            if (!apiUrl) { showToast('请输入采集接口地址', 'error'); return; }

            const siteData = {
                name: name,
                site_url: siteUrl,
                api_url: apiUrl,
                type: type,
                status: status,
                priority: priority,
                note: note
            };

            try {
                const action = editingSite ? 'sites/update' : 'sites/add';
                const res = await fetch(API_BASE + '?action=' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(siteData)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast(data.message || '保存成功', 'success');
                cancelSiteEdit();
                refreshSites();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function deleteSite(name) {
            if (!confirm('确定要删除该资源站吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=sites/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: name })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('删除成功', 'success');
                refreshSites();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function fetchSiteVideos(name) {
            document.getElementById('siteVideosTitle').textContent = name + ' - 视频列表';
            document.getElementById('siteVideos').style.display = 'block';
            document.getElementById('siteVideosList').innerHTML = '<div class="loading">正在获取视频列表...</div>';
            document.getElementById('siteVideos').scrollIntoView({ behavior: 'smooth' });
            try {
                const res = await fetch(API_BASE + '?action=sites/fetch_videos&name=' + encodeURIComponent(name) + '&limit=10');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                renderSiteVideos(data.videos || []);
            } catch (e) {
                document.getElementById('siteVideosList').innerHTML = '<div class="empty">获取失败: ' + escapeHtml(e.message) + '</div>';
                showToast('获取视频失败: ' + e.message, 'error');
            }
        }

        function renderSiteVideos(videos) {
            const container = document.getElementById('siteVideosList');
            if (videos.length === 0) {
                container.innerHTML = '<div class="empty">暂无视频数据</div>';
                return;
            }
            let html = '<div style="max-height:400px;overflow-y:auto">';
            videos.forEach((v, i) => {
                html += `
                    <div class="segment-item" style="border-bottom:1px solid #ebeef5">
                        <div style="flex:1">
                            <div style="font-weight:500;color:#303133">${i + 1}. ${escapeHtml(v.name || '未知')}</div>
                            <div style="font-size:12px;color:#909399;margin-top:4px;word-break:break-all;font-family:monospace">${escapeHtml(v.url || '')}</div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(v.url || '')}')">复制</button>
                            <button class="btn btn-sm btn-primary" onclick="analyzeFromSite('${escapeHtml(v.url || '')}')">分析</button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        function closeSiteVideos() {
            document.getElementById('siteVideos').style.display = 'none';
        }

        function analyzeFromSite(url) {
            document.getElementById('analyzeUrl').value = url;
            document.querySelector('.nav-item[data-page="analyze"]').click();
            setTimeout(() => analyzeVideo(), 300);
        }

        async function saveAutoLearnConfig() {
            const config = {
                enabled: document.getElementById('autoLearnEnabled').value === 'true',
                interval_days: parseInt(document.getElementById('intervalDays').value) || 3,
                videos_per_site: parseInt(document.getElementById('videosPerSite').value) || 5,
                max_sites_per_run: parseInt(document.getElementById('maxSitesPerRun').value) || 5,
                min_segments: parseInt(document.getElementById('minSegments').value) || 50,
                max_ad_percentage: parseInt(document.getElementById('maxAdPercentage').value) || 90
            };
            try {
                const res = await fetch(API_BASE + '?action=sites/auto_learn/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(config)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('配置保存成功', 'success');
                refreshSites();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function runAutoLearn() {
            if (!confirm('确定要立即执行自动学习吗？这可能需要一些时间。')) return;
            const resultEl = document.getElementById('autoLearnResult');
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div class="loading">正在执行自动学习，请稍候...</div>';
            try {
                const res = await fetch(API_BASE + '?action=sites/auto_learn/run', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                let html = '<div style="padding:12px;background:#f0f9eb;border:1px solid #c2e7b0;border-radius:6px">';
                html += '<div style="font-weight:600;color:#67c23a;margin-bottom:8px">✅ ' + escapeHtml(data.message || '自动学习完成') + '</div>';
                html += '<div style="font-size:13px;color:#606266">';
                html += '处理站点: ' + data.sites_processed + ' 个 | ';
                html += '学习成功: <span style="color:#67c23a">' + data.total_learned + '</span> 个 | ';
                html += '失败: <span style="color:#f56c6c">' + data.total_failed + '</span> 个';
                html += '</div>';
                if (data.details && data.details.length > 0) {
                    html += '<div style="margin-top:12px">';
                    data.details.forEach(d => {
                        html += '<div style="padding:8px;background:white;border-radius:4px;margin-bottom:6px;font-size:12px">';
                        html += '<strong>' + escapeHtml(d.site) + '</strong>: ';
                        html += '检查 ' + d.videos_checked + ' 个, 学习 ' + d.videos_learned + ' 个, 失败 ' + d.videos_failed + ' 个';
                        if (d.error) {
                            html += ' <span style="color:#f56c6c">(' + escapeHtml(d.error) + ')</span>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
                resultEl.innerHTML = html;
                showToast('自动学习完成', 'success');
                refreshSites();
            } catch (e) {
                resultEl.innerHTML = '<div style="padding:12px;background:#fef0f0;border:1px solid #fbc4c4;border-radius:6px;color:#f56c6c">学习失败: ' + escapeHtml(e.message) + '</div>';
                showToast('学习失败: ' + e.message, 'error');
            }
        }

        function populateSearchSiteSelect() {
            const select = document.getElementById('searchSiteSelect');
            if (!select || currentSites.length === 0) return;
            const currentValue = select.value;
            let html = '<option value="all">全部资源站</option>';
            currentSites.forEach(site => {
                if (site.status === 'active') {
                    html += '<option value="' + escapeHtml(site.name) + '">' + escapeHtml(site.name) + '</option>';
                }
            });
            select.innerHTML = html;
            if (currentValue) select.value = currentValue;
        }

        async function searchVideos() {
            const keyword = document.getElementById('searchKeyword').value.trim();
            if (!keyword) {
                showToast('请输入搜索关键词', 'warning');
                return;
            }

            const siteName = document.getElementById('searchSiteSelect').value;
            const maxSites = parseInt(document.getElementById('searchMaxSites').value) || 5;

            document.getElementById('searchLoading').style.display = 'block';
            document.getElementById('searchResults').style.display = 'none';

            try {
                let url;
                if (siteName === 'all') {
                    url = API_BASE + '?action=sites/search_all&keyword=' + encodeURIComponent(keyword) + '&max_sites=' + maxSites + '&limit_per_site=10&_t=' + Date.now();
                } else {
                    url = API_BASE + '?action=sites/search&name=' + encodeURIComponent(siteName) + '&keyword=' + encodeURIComponent(keyword) + '&limit=20&_t=' + Date.now();
                }

                const res = await fetch(url, {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);

                renderSearchResults(data, siteName);
                document.getElementById('searchLoading').style.display = 'none';
                document.getElementById('searchResults').style.display = 'block';
            } catch (e) {
                document.getElementById('searchLoading').style.display = 'none';
                showToast('搜索失败: ' + e.message, 'error');
            }
        }

        function renderSearchResults(data, siteName) {
            const summaryEl = document.getElementById('searchSummary');
            const listEl = document.getElementById('searchVideoList');
            const actionsEl = document.getElementById('searchActions');
            const statsEl = document.getElementById('searchStats');

            currentSearchData = data;
            currentSearchSiteName = siteName;

            let totalVideos = 0;
            let sitesCount = 0;
            let successSites = 0;
            let failedSites = 0;

            if (siteName === 'all') {
                totalVideos = data.total_videos || 0;
                sitesCount = data.sites_searched || 0;
                const results = data.results || [];
                successSites = results.filter(r => (r.videos || []).length > 0).length;
                failedSites = results.filter(r => r.error).length;
                summaryEl.innerHTML = `搜索"${escapeHtml(data.keyword || '')}" - 搜索 ${sitesCount} 个站点，成功 ${successSites} 个，找到 ${totalVideos} 个视频`;
            } else {
                totalVideos = (data.videos || []).length;
                sitesCount = 1;
                summaryEl.innerHTML = `搜索"${escapeHtml(data.keyword || '')}" - 找到 ${totalVideos} 个视频`;
            }

            if (totalVideos > 0) {
                actionsEl.style.display = 'flex';
                statsEl.textContent = `共 ${totalVideos} 个视频可学习`;
            } else {
                actionsEl.style.display = 'none';
            }

            let html = '';

            if (siteName === 'all') {
                const results = data.results || [];
                const successResults = results.filter(r => (r.videos || []).length > 0);
                const failedResults = results.filter(r => r.error);
                
                if (successResults.length === 0 && failedResults.length === 0) {
                    html += '<div class="empty">未找到相关视频</div>';
                }
                
                successResults.forEach(siteResult => {
                    const videos = siteResult.videos || [];
                    html += `<div style="margin-bottom:16px">
                        <div style="padding:8px 12px;background:#f5f7fa;border-radius:6px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center">
                            <strong>${escapeHtml(siteResult.site || '')}</strong>
                            <span style="font-size:12px;color:#67c23a">
                                ${videos.length} 个视频
                            </span>
                        </div>
                        <div style="max-height:300px;overflow-y:auto;border:1px solid #ebeef5;border-radius:6px">`;
                    videos.forEach((v, i) => {
                        html += renderSearchVideoItem(v, siteResult.site);
                    });
                    html += '</div></div>';
                });

                if (failedResults.length > 0) {
                    html += `<div style="margin-top:16px;padding:12px;background:#fafafa;border:1px solid #ebeef5;border-radius:6px">
                        <div style="font-size:13px;color:#909399;margin-bottom:8px;cursor:pointer;user-select:none" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'">
                            <span id="failedToggle">▶</span> ${failedResults.length} 个资源站暂不可用 <span style="font-size:11px">(点击展开/收起)</span>
                        </div>
                        <div id="failedSitesList" style="display:none;font-size:12px;color:#909399">`;
                    failedResults.forEach(r => {
                        html += `<div style="padding:4px 0;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between">
                            <span>${escapeHtml(r.site || '')}</span>
                            <span style="color:#c0c4cc">${escapeHtml(r.error || '未知错误')}</span>
                        </div>`;
                    });
                    html += '</div></div>';
                }
            } else {
                const videos = data.videos || [];
                if (videos.length > 0) {
                    html += '<div style="max-height:500px;overflow-y:auto;border:1px solid #ebeef5;border-radius:6px">';
                    videos.forEach((v, i) => {
                        html += renderSearchVideoItem(v, siteName);
                    });
                    html += '</div>';
                } else {
                    html += '<div class="empty">未找到相关视频</div>';
                }
            }

            listEl.innerHTML = html;
        }

        function renderSearchVideoItem(video, siteName) {
            const videoName = video.name || '未知';
            const firstUrl = video.first_url || video.url || '';
            const urls = video.urls || (firstUrl ? [{name: '默认', url: firstUrl}] : []);
            const domain = firstUrl ? (new URL(firstUrl).hostname) : '';

            let html = `<div class="segment-item" style="border-bottom:1px solid #ebeef5;flex-wrap:wrap">
                <div style="flex:1;min-width:200px">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                        <div style="font-weight:500;color:#303133">${escapeHtml(videoName)}</div>
                        <div style="display:flex;gap:4px">
                            <button class="btn btn-sm btn-success" onclick="learnFromVideoUrl('${escapeHtml(firstUrl)}', '${escapeHtml(videoName)}')" style="padding:4px 10px;font-size:11px">📚 学习</button>
                            <button class="btn btn-sm btn-primary" onclick="analyzeFromSite('${escapeHtml(firstUrl)}')" style="padding:4px 10px;font-size:11px">🔍 分析</button>
                        </div>
                    </div>
                    <div style="font-size:12px;color:#909399;margin-top:4px">
                        <span style="background:#ecf5ff;color:#409eff;padding:2px 8px;border-radius:4px;margin-right:8px">${escapeHtml(siteName || '')}</span>
                        ${domain ? '<span style="background:#f0f9eb;color:#67c23a;padding:2px 8px;border-radius:4px">' + escapeHtml(domain) + '</span>' : ''}
                        ${video.remarks ? '<span style="margin-left:8px">' + escapeHtml(video.remarks) + '</span>' : ''}
                    </div>`;

            if (urls.length > 0) {
                html += '<div style="margin-top:8px;font-size:12px">';
                urls.slice(0, 3).forEach((u, idx) => {
                    html += `<div style="padding:4px 0;display:flex;align-items:center;gap:8px">
                        <span style="color:#909399;white-space:nowrap">${escapeHtml(u.name || '剧集' + (idx + 1))}:</span>
                        <code style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;background:#f5f7fa;padding:2px 6px;border-radius:4px;font-size:11px" title="${escapeHtml(u.url)}">${escapeHtml(u.url)}</code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(u.url)}')" style="padding:2px 8px;font-size:11px">复制</button>
                    </div>`;
                });
                if (urls.length > 3) {
                    html += `<div style="color:#909399;padding:4px 0">... 还有 ${urls.length - 3} 个播放源</div>`;
                }
                html += '</div>';
            }

            html += `</div>
            </div>`;

            return html;
        }

        async function learnFromVideoUrl(url, videoName) {
            if (!url) {
                showToast('视频URL为空', 'error');
                return;
            }

            if (!confirm('确定要学习该视频的广告规则吗？\n\n视频: ' + (videoName || '未知') + '\n域名: ' + (new URL(url).hostname))) return;

            const btn = event.target;
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = '学习中...';

            try {
                const res = await fetch(API_BASE + '?action=sites/learn_video', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ url: url })
                });
                const data = await res.json();

                if (data.success) {
                    showToast('学习成功！域名: ' + (data.domain || ''), 'success');
                    console.log('学习结果:', data);
                } else {
                    showToast('学习失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('学习请求失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        let currentSearchData = null;
        let currentSearchSiteName = 'all';
        let batchLearning = false;

        function collectAllVideoUrls() {
            const urls = [];
            if (!currentSearchData) return urls;

            if (currentSearchSiteName === 'all') {
                const results = currentSearchData.results || [];
                results.forEach(siteResult => {
                    const videos = siteResult.videos || [];
                    videos.forEach(v => {
                        const firstUrl = v.first_url || v.url || '';
                        if (firstUrl) {
                            urls.push({
                                url: firstUrl,
                                name: v.name || '未知',
                                site: siteResult.site || ''
                            });
                        }
                    });
                });
            } else {
                const videos = currentSearchData.videos || [];
                videos.forEach(v => {
                    const firstUrl = v.first_url || v.url || '';
                    if (firstUrl) {
                        urls.push({
                            url: firstUrl,
                            name: v.name || '未知',
                            site: currentSearchSiteName
                        });
                    }
                });
            }
            return urls;
        }

        async function batchLearnAll() {
            if (batchLearning) {
                showToast('正在批量学习中，请稍候...', 'warning');
                return;
            }

            const videos = collectAllVideoUrls();
            if (videos.length === 0) {
                showToast('没有可学习的视频', 'warning');
                return;
            }

            if (!confirm(`确定要批量学习 ${videos.length} 个视频吗？\n\n学习成功后将自动更新规则管理中的对应域名规则。`)) return;

            batchLearning = true;
            const resultEl = document.getElementById('batchResult');
            resultEl.style.display = 'block';
            resultEl.style.background = '#fffbe6';
            resultEl.style.border = '1px solid #ffe58f';
            resultEl.innerHTML = '<div class="loading">正在批量学习中，请稍候... (0/' + videos.length + ')</div>';

            let successCount = 0;
            let failCount = 0;
            const results = [];
            const learnedDomains = new Set();

            for (let i = 0; i < videos.length; i++) {
                const video = videos[i];
                try {
                    const res = await fetch(API_BASE + '?action=sites/learn_video', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ url: video.url })
                    });
                    const data = await res.json();

                    if (data.success) {
                        successCount++;
                        if (data.domain) {
                            learnedDomains.add(data.domain);
                        }
                        results.push({
                            name: video.name,
                            site: video.site,
                            success: true,
                            domain: data.domain,
                            segments: data.segments_count,
                            ad_count: data.ad_count
                        });
                    } else {
                        failCount++;
                        results.push({
                            name: video.name,
                            site: video.site,
                            success: false,
                            message: data.message
                        });
                    }
                } catch (e) {
                    failCount++;
                    results.push({
                        name: video.name,
                        site: video.site,
                        success: false,
                        message: e.message
                    });
                }

                resultEl.innerHTML = '<div class="loading">正在批量学习中，请稍候... (' + (i + 1) + '/' + videos.length + ')</div>';
                document.getElementById('searchStats').textContent = `已完成 ${i + 1}/${videos.length}，成功 ${successCount}，失败 ${failCount}`;
            }

            let html = '<div style="margin-bottom:8px;font-weight:600">';
            if (successCount > 0) {
                html += '<span style="color:#67c23a">✅ 批量学习完成</span>';
                resultEl.style.background = '#f0f9eb';
                resultEl.style.border = '1px solid #c2e7b0';
            } else {
                html += '<span style="color:#f56c6c">❌ 批量学习失败</span>';
                resultEl.style.background = '#fef0f0';
                resultEl.style.border = '1px solid #fbc4c4';
            }
            html += '</div>';
            html += '<div style="font-size:13px;color:#606266;margin-bottom:8px">';
            html += `总计: ${videos.length} 个 | 成功: <span style="color:#67c23a">${successCount}</span> | 失败: <span style="color:#f56c6c">${failCount}</span>`;
            if (learnedDomains.size > 0) {
                html += ` | 更新域名: <span style="color:#409eff">${learnedDomains.size}</span> 个`;
            }
            html += '</div>';

            if (learnedDomains.size > 0) {
                html += '<div style="font-size:12px;color:#909399;margin-bottom:8px">';
                html += '已更新域名: ' + Array.from(learnedDomains).join(', ');
                html += '</div>';
            }

            const failedResults = results.filter(r => !r.success);
            if (failedResults.length > 0 && failedResults.length <= 10) {
                html += '<div style="font-size:12px"><details><summary style="cursor:pointer;color:#909399">查看失败详情</summary><div style="margin-top:8px">';
                failedResults.forEach(r => {
                    html += `<div style="padding:4px 0;color:#f56c6c">${escapeHtml(r.name)} - ${escapeHtml(r.message || '未知错误')}</div>`;
                });
                html += '</div></details></div>';
            }

            resultEl.innerHTML = html;
            document.getElementById('searchStats').textContent = `完成 - 成功 ${successCount}，失败 ${failCount}，更新 ${learnedDomains.size} 个域名`;

            batchLearning = false;
            showToast(`批量学习完成：成功 ${successCount}，失败 ${failCount}`, successCount > 0 ? 'success' : 'error');
        }

        async function batchAnalyzeAll() {
            const videos = collectAllVideoUrls();
            if (videos.length === 0) {
                showToast('没有可分析的视频', 'warning');
                return;
            }

            if (!confirm(`确定要批量分析 ${videos.length} 个视频吗？`)) return;

            const firstVideo = videos[0];
            if (firstVideo && firstVideo.url) {
                switchPage('analyze');
                document.getElementById('videoUrl').value = firstVideo.url;
                await analyzeVideo();
                showToast('已跳转到分析页面并分析第一个视频', 'info');
            }
        }

        function clearSearchResults() {
            document.getElementById('searchKeyword').value = '';
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('searchLoading').style.display = 'none';
            document.getElementById('batchResult').style.display = 'none';
            document.getElementById('searchActions').style.display = 'none';
            currentSearchData = null;
        }

        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', () => {
                const page = item.dataset.page;
                if (page === 'rules') refreshRules();
                if (page === 'sites') refreshSites();
                if (page === 'auth') refreshAuthInfo();
                if (page === 'update') { checkUpdate(); loadVersion(); loadBackupList(); }
            });
        });

        function copyText(text) {
            if (!text) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('复制成功', 'success');
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.top = '-1000px';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showToast('复制成功', 'success');
            } catch (e) {
                showToast('复制失败，请手动复制', 'error');
            }
            document.body.removeChild(textarea);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('mxadmin_theme') || 'default';
            switchTheme(savedTheme, false);
        }

        function switchTheme(themeName, save = true) {
            if (themeName === 'default') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', themeName);
            }

            document.querySelectorAll('.theme-dot').forEach(dot => {
                if (dot.dataset.themeName === themeName) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            if (save) {
                localStorage.setItem('mxadmin_theme', themeName);
            }
        }

        function initAccessPreview() {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const path = window.location.pathname;
            const baseDir = path.substring(0, path.lastIndexOf('/'));
            const base = protocol + '//' + host + baseDir;

            document.getElementById('preview-admin').textContent = base + '/mxadmin.php';
            document.getElementById('preview-api').textContent = base + '/mx.php?action=analyze&url=';
            document.getElementById('preview-parse').textContent = base + '/mx.php?action=mxjx&url=';
            document.getElementById('preview-player').textContent = base + '/mx.php?action=mxjx/info&url=';
            document.getElementById('preview-official-replace').textContent = base + '/mx.php?action=official_replace/info&url=';
        }

        let currentOfficialConfig = null;
        let editingPlatformIndex = -1;

        async function loadOfficialReplaceConfig() {
            try {
                const res = await fetch(API_BASE + '?action=official_replace/config&_t=' + Date.now());
                const data = await res.json();
                if (data.success && data.config) {
                    currentOfficialConfig = data.config;
                    const cfg = data.config;
                    document.getElementById('orTotalPlatforms').textContent = (cfg.platforms || []).length;
                    document.getElementById('orStatus').textContent = cfg.enabled ? '已启用' : '已禁用';
                    document.getElementById('orStatus').style.color = cfg.enabled ? '#67c23a' : '#f56c6c';
                    document.getElementById('orSearchSites').textContent = (cfg.search_sites || []).length > 0 ? (cfg.search_sites || []).length + '个' : '全部';
                    document.getElementById('orThreshold').textContent = cfg.match_threshold || 60;

                    document.getElementById('orEnabled').value = cfg.enabled ? 'true' : 'false';
                    document.getElementById('orThresholdInput').value = cfg.match_threshold || 60;
                    document.getElementById('orMaxSites').value = cfg.max_search_sites || 5;
                    document.getElementById('orSearchSitesInput').value = (cfg.search_sites || []).join(',');

                    const base = window.location.protocol + '//' + window.location.host + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                    document.getElementById('api-resolve-url').textContent = base + '/mx.php?action=official_replace/resolve&url=';
                    document.getElementById('api-info-url').textContent = base + '/mx.php?action=official_replace/info&url=';

                    renderOfficialPlatforms(cfg.platforms || []);
                }
            } catch (e) {
                showToast('加载官替配置失败: ' + e.message, 'error');
            }
        }

        function renderOfficialPlatforms(platforms) {
            const listEl = document.getElementById('officialPlatformsList');
            if (platforms.length === 0) {
                listEl.innerHTML = '<div class="empty">暂无平台配置</div>';
                return;
            }

            let html = '<table class="rule-table"><thead><tr><th>平台名称</th><th>域名</th><th>状态</th><th>优先级</th><th>操作</th></tr></thead><tbody>';
            platforms.forEach((p, index) => {
                html += `<tr>
                    <td><strong>${escapeHtml(p.name || '')}</strong></td>
                    <td><code>${escapeHtml(p.domain || '')}</code></td>
                    <td><span style="color:${p.enabled ? '#67c23a' : '#f56c6c'}">${p.enabled ? '启用' : '禁用'}</span></td>
                    <td>${p.priority || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editOfficialPlatform(${index})">编辑</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteOfficialPlatform(${index})">删除</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            listEl.innerHTML = html;
        }

        async function saveOfficialReplaceConfig() {
            if (!currentOfficialConfig) return;
            
            const newConfig = JSON.parse(JSON.stringify(currentOfficialConfig));
            newConfig.enabled = document.getElementById('orEnabled').value === 'true';
            newConfig.match_threshold = parseInt(document.getElementById('orThresholdInput').value) || 60;
            newConfig.max_search_sites = parseInt(document.getElementById('orMaxSites').value) || 5;
            
            const sitesInput = document.getElementById('orSearchSitesInput').value.trim();
            newConfig.search_sites = sitesInput ? sitesInput.split(',').map(s => s.trim()).filter(s => s) : [];

            try {
                const res = await fetch(API_BASE + '?action=official_replace/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newConfig)
                });
                const data = await res.json();
                if (data.success) {
                    showToast('配置保存成功', 'success');
                    loadOfficialReplaceConfig();
                } else {
                    showToast('保存失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        function addOfficialPlatform() {
            editingPlatformIndex = -1;
            showPlatformEditor({
                name: '',
                domain: '',
                enabled: true,
                pattern: '',
                title_selector: '',
                priority: 10
            });
        }

        function editOfficialPlatform(index) {
            if (!currentOfficialConfig || !currentOfficialConfig.platforms) return;
            editingPlatformIndex = index;
            showPlatformEditor(currentOfficialConfig.platforms[index]);
        }

        function showPlatformEditor(platform) {
            const html = `<div id="platformEditorModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:1000">
                <div style="background:white;border-radius:8px;padding:24px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto">
                    <h3 style="margin-bottom:16px">${editingPlatformIndex >= 0 ? '编辑' : '添加'}平台</h3>
                    <div class="form-group"><label>平台名称</label><input type="text" id="pe-name" value="${escapeHtml(platform.name || '')}"></div>
                    <div class="form-group"><label>域名</label><input type="text" id="pe-domain" placeholder="如: v.qq.com" value="${escapeHtml(platform.domain || '')}"></div>
                    <div class="form-group"><label>启用</label><select id="pe-enabled"><option value="true" ${platform.enabled ? 'selected' : ''}>启用</option><option value="false" ${!platform.enabled ? 'selected' : ''}>禁用</option></select></div>
                    <div class="form-group"><label>URL 匹配正则</label><input type="text" id="pe-pattern" placeholder="如: /v\\.qq\\.com\\/.*?(?:vid=|\\/)([a-zA-Z0-9]+)/i" value="${escapeHtml(platform.pattern || '')}"></div>
                    <div class="form-group"><label>标题选择器</label><input type="text" id="pe-title_selector" placeholder="如: meta[property=og:title]" value="${escapeHtml(platform.title_selector || '')}"></div>
                    <div class="form-group"><label>优先级</label><input type="number" id="pe-priority" value="${platform.priority || 10}"></div>
                    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px">
                        <button class="btn btn-secondary" onclick="closePlatformEditor()">取消</button>
                        <button class="btn btn-primary" onclick="savePlatformEditor()">保存</button>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
        }

        function closePlatformEditor() {
            const modal = document.getElementById('platformEditorModal');
            if (modal) modal.remove();
        }

        async function savePlatformEditor() {
            const platformData = {
                name: document.getElementById('pe-name').value.trim(),
                domain: document.getElementById('pe-domain').value.trim(),
                enabled: document.getElementById('pe-enabled').value === 'true',
                pattern: document.getElementById('pe-pattern').value.trim(),
                title_selector: document.getElementById('pe-title_selector').value.trim(),
                priority: parseInt(document.getElementById('pe-priority').value) || 10
            };

            if (!platformData.name || !platformData.domain) {
                showToast('请填写平台名称和域名', 'error');
                return;
            }

            try {
                const action = editingPlatformIndex >= 0 ? 'official_replace/platform/update' : 'official_replace/platform/add';
                const body = editingPlatformIndex >= 0 
                    ? { index: editingPlatformIndex, ...platformData }
                    : platformData;

                const res = await fetch(API_BASE + '?action=' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(editingPlatformIndex >= 0 ? '更新成功' : '添加成功', 'success');
                    closePlatformEditor();
                    loadOfficialReplaceConfig();
                } else {
                    showToast('操作失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('操作失败: ' + e.message, 'error');
            }
        }

        async function deleteOfficialPlatform(index) {
            if (!confirm('确定要删除这个平台吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=official_replace/platform/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ index: index })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('删除成功', 'success');
                    loadOfficialReplaceConfig();
                } else {
                    showToast('删除失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function testOfficialReplace() {
            const url = document.getElementById('officialTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }

            const resultEl = document.getElementById('officialTestResult');
            const infoEl = document.getElementById('officialTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在解析...</div>';

            try {
                const res = await fetch(API_BASE + '?action=official_replace/resolve&url=' + encodeURIComponent(url) + '&_t=' + Date.now());
                const data = await res.json();
                
                if (data.success) {
                    let seasonHtml = '';
                    if (data.season) {
                        seasonHtml = `<p><strong>季数:</strong> <span style="color:#409eff">${escapeHtml(data.season)}</span></p>`;
                    }
                    let episodeHtml = '';
                    if (data.episode) {
                        episodeHtml = `<p><strong>集数:</strong> <span style="color:#409eff">${escapeHtml(data.episode)}</span>`;
                        if (data.target_episode) {
                            episodeHtml += ` → 定位到: <span style="color:#67c23a">${escapeHtml(data.target_episode)}</span>`;
                        }
                        episodeHtml += '</p>';
                    }
                    let partHtml = '';
                    if (data.part) {
                        partHtml = `<p><strong>篇章:</strong> <span style="color:#e6a23c">${escapeHtml(data.part)}</span></p>`;
                    }
                    let versionHtml = '';
                    if (data.version) {
                        versionHtml = `<p><strong>版本:</strong> <span style="color:#909399">${escapeHtml(data.version)}</span></p>`;
                    }
                    let seasonMatchHtml = '';
                    if (data.season_match !== undefined) {
                        seasonMatchHtml = `<span style="color:${data.season_match ? '#67c23a' : '#f56c6c'};margin-left:8px;font-size:11px">
                            ${data.season_match ? '季数匹配 ✓' : '季数不匹配 ✗'}
                        </span>`;
                    }

                    let html = `<div style="background:#f0f9eb;padding:16px;border-radius:8px;border:1px solid #e1f3d8">
                        <div style="color:#67c23a;font-weight:600;margin-bottom:8px">✓ 解析成功</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>平台:</strong> ${escapeHtml(data.platform || '')}</p>
                            <p><strong>原始标题:</strong> ${escapeHtml(data.video_title || '')}</p>
                            <p><strong>基础名称:</strong> <code>${escapeHtml(data.base_title || '')}</code></p>
                            ${seasonHtml}
                            ${episodeHtml}
                            ${partHtml}
                            ${versionHtml}
                            <p><strong>匹配资源站:</strong> ${escapeHtml(data.site || '')}</p>
                            <p><strong>总匹配度:</strong> <span style="color:#67c23a;font-size:16px;font-weight:600">${data.match_score || 0}%</span>
                                ${seasonMatchHtml}
                            </p>
                            <p style="font-size:11px;color:#909399">基础匹配度: ${data.base_score || 0}%</p>
                            <p><strong>M3U8 地址:</strong> <code style="word-break:break-all;font-size:11px">${escapeHtml(data.m3u8_url || '')}</code></p>
                            <p><strong>总集数:</strong> ${(data.all_urls || []).length} 集</p>
                        </div>
                    </div>`;
                    
                    if (data.alternatives && data.alternatives.length > 1) {
                        html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">其他候选结果:</div>';
                        data.alternatives.slice(1, 6).forEach(v => {
                            html += `<div style="padding:8px;background:#f5f7fa;border-radius:6px;margin-bottom:6px;display:flex;justify-content:space-between;align-items:center">
                                <div>
                                    <div style="font-weight:500">${escapeHtml(v.name || '未知')}</div>
                                    <div style="font-size:12px;color:#909399">${escapeHtml(v.site || '')}</div>
                                </div>
                                <button class="btn btn-sm btn-primary" onclick="learnFromVideoUrl('${escapeHtml(v.first_url || v.url || '')}', '${escapeHtml(v.name || '')}')">学习</button>
                            </div>`;
                        });
                        html += '</div>';
                    }
                    infoEl.innerHTML = html;
                } else {
                    let seasonHtml = '';
                    if (data.season) {
                        seasonHtml = `<p><strong>解析季数:</strong> ${escapeHtml(data.season)}</p>`;
                    }
                    let episodeHtml = '';
                    if (data.episode) {
                        episodeHtml = `<p><strong>解析集数:</strong> ${escapeHtml(data.episode)}</p>`;
                    }

                    let html = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600;margin-bottom:8px">✗ 解析失败</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>原因:</strong> ${escapeHtml(data.message || '未知错误')}</p>
                            ${data.platform ? `<p><strong>识别平台:</strong> ${escapeHtml(data.platform)}</p>` : ''}
                            ${data.video_title ? `<p><strong>提取标题:</strong> ${escapeHtml(data.video_title)}</p>` : ''}
                            ${data.base_title ? `<p><strong>基础名称:</strong> <code>${escapeHtml(data.base_title)}</code></p>` : ''}
                            ${seasonHtml}
                            ${episodeHtml}
                        </div>
                    </div>`;
                    
                    if (data.candidates && data.candidates.length > 0) {
                        html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">候选结果 (匹配度不足):</div>';
                        data.candidates.forEach(v => {
                            html += `<div style="padding:8px;background:#f5f7fa;border-radius:6px;margin-bottom:6px">
                                <div style="font-weight:500">${escapeHtml(v.name || '未知')}</div>
                                <div style="font-size:12px;color:#909399">${escapeHtml(v.site || '')}</div>
                            </div>`;
                        });
                        html += '</div>';
                    }
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        let currentOfficialSite = '';

        async function loadOfficialSites() {
            const res = await fetch(API_BASE + '?action=official_sites/list&include_paused=1&_t=' + Date.now());
            const data = await res.json();
            if (data.success) {
                document.getElementById('officialSitesEnabled').checked = data.enabled;
                if (data.settings) {
                    document.getElementById('osAutoSwitch').value = data.settings.auto_switch_domain ? '1' : '0';
                    document.getElementById('osMaxRetry').value = data.settings.max_retry_per_domain ?? 2;
                    document.getElementById('osTimeout').value = data.settings.timeout ?? 10;
                    document.getElementById('osDefaultLimit').value = data.settings.default_limit ?? 20;
                }
                renderOfficialSites(data.sites || []);
            }
        }

        function renderOfficialSites(sites) {
            const container = document.getElementById('officialSitesList');
            if (sites.length === 0) {
                container.innerHTML = '<div class="empty">暂无官采资源站</div>';
                return;
            }
            let html = '<table class="rules-table">';
            html += '<thead><tr><th>状态</th><th>名称</th><th>域名</th><th>类型</th><th>备注</th><th>优先级</th><th>操作</th></tr></thead>';
            html += '<tbody>';
            sites.forEach(site => {
                const isPaused = site.status === 'paused';
                const domains = site.domains || [];
                const activeIdx = site.active_domain_index ?? 0;
                const domainBadges = domains.map((d, i) => {
                    const isActive = i === activeIdx;
                    return `<span class="tag ${isActive ? 'tag-green' : 'tag-blue'}" style="cursor:pointer" onclick="switchOfficialDomain('${escapeHtml(site.name)}', ${i})" title="点击切换">${escapeHtml(d)}</span>`;
                }).join(' ');

                html += `<tr>
                    <td>${isPaused ? '<span class="tag tag-orange">停用</span>' : '<span class="tag tag-green">运行中</span>'}</td>
                    <td style="font-weight:500">
                        ${escapeHtml(site.name || '')}
                        <span class="tag tag-red" style="background:linear-gradient(135deg,#f56c6c,#e74c3c);color:white">官采</span>
                    </td>
                    <td><div style="max-width:280px;display:flex;flex-wrap:wrap;gap:4px">${domainBadges}</div></td>
                    <td>${escapeHtml(site.type || 'maccms')}</td>
                    <td>${escapeHtml(site.note || '')}</td>
                    <td>${site.priority ?? 99}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewOfficialSiteVideos('${escapeHtml(site.name)}')">视频</button>
                        <button class="btn btn-sm btn-primary" onclick="editOfficialSite('${escapeHtml(site.name)}')">编辑</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteOfficialSite('${escapeHtml(site.name)}')">删除</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        async function switchOfficialDomain(siteName, domainIndex) {
            const res = await fetch(API_BASE + '?action=official_sites/set_domain', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: siteName, domain_index: domainIndex })
            });
            const data = await res.json();
            if (data.success) {
                showToast('域名切换成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '切换失败', 'error');
            }
        }

        async function toggleOfficialSites() {
            const enabled = document.getElementById('officialSitesEnabled').checked;
            const res = await fetch(API_BASE + '?action=official_sites/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ enabled: enabled })
            });
            const data = await res.json();
            if (data.success) {
                showToast(enabled ? '官采专区已启用' : '官采专区已停用', 'success');
            } else {
                showToast(data.message || '操作失败', 'error');
            }
        }

        async function saveOfficialSettings() {
            const settings = {
                auto_switch_domain: document.getElementById('osAutoSwitch').value === '1',
                max_retry_per_domain: parseInt(document.getElementById('osMaxRetry').value),
                timeout: parseInt(document.getElementById('osTimeout').value),
                default_limit: parseInt(document.getElementById('osDefaultLimit').value)
            };
            const res = await fetch(API_BASE + '?action=official_sites/settings/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings)
            });
            const data = await res.json();
            if (data.success) {
                showToast('设置保存成功', 'success');
            } else {
                showToast(data.message || '保存失败', 'error');
            }
        }

        function showAddOfficialSite() {
            const name = prompt('请输入官采站名称：');
            if (!name) return;
            const domains = prompt('请输入域名（一行一个）：', 'cj.10010888.xyz\ncj.tianwe.cn\ntianwei.qzz.io');
            if (!domains) return;
            const apiPath = prompt('请输入API路径：', '/api.php/provide/vod/') || '/api.php/provide/vod/';
            const note = prompt('备注：', '官采推荐') || '';
            addOfficialSite({ name, domains, api_path: apiPath, note, type: 'maccms', priority: 1 });
        }

        async function addOfficialSite(siteData) {
            const domainList = siteData.domains.split('\n').map(d => d.trim()).filter(d => d);
            const siteUrl = 'https://' + domainList[0];
            const apiUrl = siteUrl + siteData.api_path;
            const res = await fetch(API_BASE + '?action=official_sites/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: siteData.name,
                    code: siteData.name.toLowerCase(),
                    site_url: siteUrl,
                    api_url: apiUrl,
                    type: siteData.type || 'maccms',
                    status: 'active',
                    note: siteData.note || '',
                    priority: siteData.priority || 1,
                    domains: domainList,
                    api_path: siteData.api_path || '/api.php/provide/vod/'
                })
            });
            const data = await res.json();
            if (data.success) {
                showToast('添加成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '添加失败', 'error');
            }
        }

        async function editOfficialSite(name) {
            const res = await fetch(API_BASE + '?action=official_sites/get&name=' + encodeURIComponent(name));
            const data = await res.json();
            if (!data.success) {
                showToast(data.message || '获取失败', 'error');
                return;
            }
            const site = data.site;
            const newNote = prompt('修改备注：', site.note || '');
            if (newNote === null) return;
            const newPriority = prompt('修改优先级（数字越小越靠前）：', site.priority ?? 99);
            if (newPriority === null) return;

            const updateRes = await fetch(API_BASE + '?action=official_sites/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: name,
                    note: newNote,
                    priority: parseInt(newPriority)
                })
            });
            const updateData = await updateRes.json();
            if (updateData.success) {
                showToast('更新成功', 'success');
                loadOfficialSites();
            } else {
                showToast(updateData.message || '更新失败', 'error');
            }
        }

        async function deleteOfficialSite(name) {
            if (!confirm('确定删除官采站「' + name + '」吗？')) return;
            const res = await fetch(API_BASE + '?action=official_sites/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: name })
            });
            const data = await res.json();
            if (data.success) {
                showToast('删除成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '删除失败', 'error');
            }
        }

        async function viewOfficialSiteVideos(siteName) {
            currentOfficialSite = siteName;
            document.getElementById('officialSiteVideoTitle').textContent = siteName + ' - 视频列表';
            document.getElementById('officialSiteVideos').style.display = 'block';
            document.getElementById('officialVideoSearch').value = '';
            document.getElementById('officialSiteVideosList').innerHTML = '<div class="loading">加载中...</div>';
            try {
                const res = await fetch(API_BASE + '?action=official_sites/fetch_videos&name=' + encodeURIComponent(siteName) + '&_t=' + Date.now());
                const data = await res.json();
                renderOfficialVideos(data);
            } catch (e) {
                document.getElementById('officialSiteVideosList').innerHTML = 
                    `<div style="color:#f56c6c;text-align:center;padding:20px">加载失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        function renderOfficialVideos(data) {
            const container = document.getElementById('officialSiteVideosList');
            if (!data.success || !data.videos || data.videos.length === 0) {
                container.innerHTML = `<div class="empty">${data.message || '暂无视频数据'}</div>`;
                return;
            }
            const videos = data.videos;
            let html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">';
            videos.forEach(v => {
                html += `<div class="stat-card" style="cursor:pointer" onclick="learnOfficialVideo('${escapeHtml(v.first_url || v.url || '')}', '${escapeHtml(v.name || '')}')">
                    <div style="font-weight:500;margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escapeHtml(v.name || '未知')}</div>
                    <div style="font-size:12px;color:var(--text-secondary)">${escapeHtml(v.remarks || v.type || '')}</div>
                    <div style="margin-top:8px"><span class="tag tag-blue">${v.total || 0} 集</span></div>
                </div>`;
            });
            html += '</div>';
            if (data.domain_used) {
                html = `<div style="margin-bottom:12px;padding:8px 12px;background:var(--primary-bg);border-radius:6px;font-size:13px;color:var(--primary-text)">
                    当前使用域名: <strong>${escapeHtml(data.domain_used)}</strong>
                </div>` + html;
            }
            container.innerHTML = html;
        }

        async function searchOfficialVideos() {
            const keyword = document.getElementById('officialVideoSearch').value.trim();
            if (!keyword || !currentOfficialSite) return;
            document.getElementById('officialSiteVideosList').innerHTML = '<div class="loading">搜索中...</div>';
            try {
                const res = await fetch(API_BASE + '?action=official_sites/search&name=' + encodeURIComponent(currentOfficialSite) + '&keyword=' + encodeURIComponent(keyword) + '&_t=' + Date.now());
                const data = await res.json();
                renderOfficialVideos(data);
            } catch (e) {
                document.getElementById('officialSiteVideosList').innerHTML = 
                    `<div style="color:#f56c6c;text-align:center;padding:20px">搜索失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        function refreshOfficialVideos() {
            if (currentOfficialSite) {
                viewOfficialSiteVideos(currentOfficialSite);
            }
        }

        function closeOfficialSiteVideos() {
            document.getElementById('officialSiteVideos').style.display = 'none';
            currentOfficialSite = '';
        }

        async function learnOfficialVideo(url, name) {
            if (!confirm('学习视频「' + name + '」的广告规则？')) return;
            const res = await fetch(API_BASE + '?action=analyze&url=' + encodeURIComponent(url) + '&auto_learn=1&_t=' + Date.now());
            const data = await res.json();
            if (data.success) {
                showToast('学习成功，广告占比: ' + (data.stats?.adPercentage || 0).toFixed(1) + '%', 'success');
            } else {
                showToast(data.message || '学习失败', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initTheme();
            refreshRules();
            initAccessPreview();
            loadOfficialSites();
            loadOfficialReplaceConfig();
        });
    </script>
</body>
</html>
