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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #303133;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        }
        .header h1 { font-size: 24px; font-weight: 600; }
        .header p { opacity: 0.9; margin-top: 5px; font-size: 14px; }
        .nav {
            background: white;
            padding: 0 30px;
            display: flex;
            gap: 5px;
            border-bottom: 1px solid #e4e7ed;
        }
        .nav-item {
            padding: 15px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
            color: #606266;
        }
        .nav-item:hover { color: #667eea; }
        .nav-item.active {
            color: #667eea;
            border-bottom-color: #667eea;
            font-weight: 500;
        }
        .container { padding: 30px; }
        .page { display: none; }
        .page.active { display: block; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #303133;
        }
        .input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
        }
        .input-group input:focus { border-color: #667eea; }
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .btn-secondary {
            background: #ecf5ff;
            color: #409eff;
        }
        .btn-secondary:hover { background: #d9ecff; }
        .btn-success { background: #67c23a; color: white; }
        .btn-success:hover { background: #5daf34; }
        .btn-danger { background: #f56c6c; color: white; }
        .btn-danger:hover { background: #e74c3c; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .stat-value.warning { color: #e6a23c; -webkit-text-fill-color: #e6a23c; background: none; }
        .stat-value.danger { color: #f56c6c; -webkit-text-fill-color: #f56c6c; background: none; }
        .stat-value.success { color: #67c23a; -webkit-text-fill-color: #67c23a; background: none; }
        .stat-label {
            color: #909399;
            font-size: 13px;
            margin-top: 6px;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #909399;
        }
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #667eea;
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
            border: 1px solid #ebeef5;
            border-radius: 6px;
        }
        .segment-item {
            padding: 10px 14px;
            border-bottom: 1px solid #ebeef5;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .segment-item:last-child { border-bottom: none; }
        .segment-item.ad {
            background: #fef0f0;
            border-left: 3px solid #f56c6c;
        }
        .segment-name { font-family: monospace; color: #303133; }
        .segment-duration { color: #909399; }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 6px;
        }
        .tag-red { background: #fef0f0; color: #f56c6c; }
        .tag-blue { background: #ecf5ff; color: #409eff; }
        .tag-green { background: #f0f9eb; color: #67c23a; }
        .tag-orange { background: #fdf6ec; color: #e6a23c; }
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
            background: #fdf6ec;
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .jump-item .jump-arrow { color: #e6a23c; font-weight: bold; }
        .rules-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rules-table th, .rules-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ebeef5;
            font-size: 14px;
        }
        .rules-table th {
            background: #fafafa;
            color: #606266;
            font-weight: 500;
        }
        .rules-table tr:hover { background: #fafafa; }
        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #606266;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        .form-group textarea { min-height: 100px; font-family: monospace; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: #667eea;
        }
        .form-group input:disabled, .form-group textarea:disabled {
            background: #f5f7fa;
            color: #909399;
            cursor: not-allowed;
        }
        .rule-section {
            border: 1px solid #ebeef5;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .fast-mode-banner {
            background: linear-gradient(135deg, #f0f9eb 0%, #e1f3d8 100%);
            border: 1px solid #c2e7b0;
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
            color: #67c23a;
            font-size: 15px;
            margin-bottom: 4px;
        }
        .fast-mode-banner .desc {
            color: #606266;
            font-size: 13px;
        }
        .fast-mode-banner .domain-tag {
            display: inline-block;
            background: white;
            padding: 2px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            color: #67c23a;
            margin-left: 8px;
            border: 1px solid #c2e7b0;
        }
        .rule-section-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #303133;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #c0c4cc;
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
            border-bottom: 1px solid #e4e7ed;
        }
        .tab-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            color: #606266;
        }
        .tab-item.active {
            color: #667eea;
            border-bottom-color: #667eea;
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
        .toast.success { background: #67c23a; }
        .toast.error { background: #f56c6c; }
        .toast.info { background: #409eff; }
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
            background: linear-gradient(to top, #667eea, #764ba2);
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
            color: #606266;
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>M3U8 广告分析与规则管理后台</h1>
        <p>靶机测试工具 - 分析视频广告特征，管理域名去广告规则</p>
    </div>

    <div class="nav">
        <div class="nav-item active" data-page="analyze">视频分析</div>
        <div class="nav-item" data-page="rules">规则管理</div>
        <div class="nav-item" data-page="sites">资源站管理</div>
        <div class="nav-item" data-page="play">在线播放</div>
        <div class="nav-item" data-page="update">系统更新</div>
        <div class="nav-item" data-page="auth">授权管理</div>
    </div>

    <div id="accessPreview" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px 30px;font-size:13px">
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
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">去广告接口</div>
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
                    <input type="text" id="siteName" placeholder="例如：墨子">
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
        }

        document.addEventListener('DOMContentLoaded', () => {
            refreshRules();
            initAccessPreview();
        });
    </script>
</body>
</html>
