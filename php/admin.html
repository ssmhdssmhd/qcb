<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8 广告分析后台</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.15/dist/hls.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dplayer@1.27.1/dist/DPlayer.min.js"></script>
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
        .rule-section {
            border: 1px solid #ebeef5;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
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
        <div class="nav-item" data-page="play">在线播放</div>
        <div class="nav-item" data-page="update">系统更新</div>
        <div class="nav-item" data-page="auth">授权管理</div>
    </div>

    <div id="accessPreview" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:20px 30px;font-size:13px">
        <div style="display:flex;flex-wrap:wrap;gap:20px">
            <div>
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">后台管理</div>
                <code id="preview-admin"></code>
            </div>
            <div>
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">API接口</div>
                <code id="preview-api"></code>
            </div>
            <div>
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">直接解析</div>
                <code id="preview-parse"></code>
            </div>
            <div>
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">播放地址</div>
                <code id="preview-player"></code>
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
                <div class="stats-grid" id="statsGrid"></div>

                <div class="detail-grid">
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
                    <div class="card-title">操作</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="generateRules()">自动生成规则</button>
                        <button class="btn btn-success" onclick="goToRules()">查看规则管理</button>
                        <button class="btn btn-primary" onclick="playVideo()">无广告播放</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="page" id="page-rules">
            <div class="card">
                <div class="card-title">域名规则列表</div>
                <div style="margin-bottom:16px;display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="showAddRule()">+ 新增规则</button>
                    <button class="btn btn-secondary" onclick="refreshRules()">刷新列表</button>
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
                    <input type="text" id="authFile" placeholder="例如：sq.txt">
                </div>
                <div class="form-group">
                    <label>对比文件名</label>
                    <input type="text" id="authFileCompare" placeholder="例如：sqm.txt">
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
        const API_BASE = 'admin_api.php';
        let currentAnalysis = null;
        let currentSegmentTab = 'ad';
        let editingRules = null;
        let dp = null;

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
                if (item.dataset.page === 'rules') refreshRules();
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
            const stats = data.stats;
            const pct = stats.totalSegments > 0 ? (stats.adSegments / stats.totalSegments * 100).toFixed(1) : 0;
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

        function goToRules() {
            document.querySelector('.nav-item[data-page="rules"]').click();
        }

        function playVideo() {
            const url = document.getElementById('analyzeUrl')?.value?.trim() || document.getElementById('playUrl')?.value?.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            document.querySelector('.nav-item[data-page="play"]').click();
            document.getElementById('playUrl').value = url;
            const mxjxUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            document.getElementById('playerContainer').style.display = 'block';
            document.getElementById('playInfo').innerHTML = `
                无广告链接: <code style="background:#f5f7fa;padding:2px 6px;border-radius:4px">${mxjxUrl}</code>
            `;

            if (dp) {
                dp.destroy();
            }

            dp = new DPlayer({
                container: document.getElementById('dplayer'),
                video: {
                    url: mxjxUrl,
                    type: 'hls'
                },
                hls: Hls,
                autoplay: true,
                theme: '#667eea',
                lang: 'zh-cn'
            });
        }

        async function refreshRules() {
            try {
                const res = await fetch(API_BASE + '?action=rules/list');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                renderRulesTable(data.rules);
            } catch (e) {
                showToast('获取规则列表失败: ' + e.message, 'error');
            }
        }

        function renderRulesTable(rules) {
            const domains = Object.keys(rules);
            if (domains.length === 0) {
                document.getElementById('rulesTable').innerHTML = '<div class="empty">暂无域名规则</div>';
                return;
            }
            let html = '<table class="rules-table"><thead><tr><th>资源名称</th><th>域名</th><th>时长规则</th><th>DISCON规则</th><th>序列号跳跃</th><th>更新时间</th><th>备注</th><th>操作</th></tr></thead><tbody>';
            for (const domain of domains) {
                const r = rules[domain];
                const name = r.name || domain;
                const note = r.note || '-';
                const durCount = (r.duration_rules || []).filter(x => x.enabled).length;
                const disCount = (r.discontinuity_rules || []).filter(x => x.enabled).length;
                const seqCount = (r.sequence_jump_rules || []).filter(x => x.enabled).length;
                const mtime = r._filemtime ? new Date(r._filemtime * 1000).toLocaleString() : '-';
                html += `
                    <tr>
                        <td><span style="color:#606266">${name}</span></td>
                        <td><strong>${domain}</strong></td>
                        <td><span class="tag tag-blue">${durCount}条</span></td>
                        <td>${disCount > 0 ? '<span class="tag tag-orange">启用</span>' : '<span style="color:#c0c4cc">未启用</span>'}</td>
                        <td><span class="tag tag-red">${seqCount}条</span></td>
                        <td style="color:#909399;font-size:12px">${mtime}</td>
                        <td style="color:#909399;font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis">${note}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editRule('${domain}')">编辑</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRule('${domain}')">删除</button>
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

        async function doUpdate() {
            if (!confirm('确定要更新系统吗？更新前会自动创建备份。')) return;
            const btn = document.getElementById('updateBtn');
            btn.disabled = true;
            btn.textContent = '更新中...';
            try {
                const res = await fetch(API_BASE + '?action=update/download');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('更新成功！备份文件: ' + data.backup_file, 'success');
                setTimeout(() => location.reload(), 1500);
            } catch (e) {
                showToast('更新失败: ' + e.message, 'error');
                btn.disabled = false;
                btn.textContent = '立即更新';
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

        document.querySelectorAll('.nav-item').forEach(item => {
            const origHandler = item.onclick;
            item.addEventListener('click', () => {
                if (item.dataset.page === 'update') {
                    loadVersion();
                }
                if (item.dataset.page === 'auth') {
                    refreshAuthInfo();
                }
            });
        });

        function initAccessPreview() {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const base = protocol + '//' + host;

            document.getElementById('preview-admin').textContent = base + '/mxadmin.html';
            document.getElementById('preview-api').textContent = base + '/admin_api.php?action=analyze&url=xxx';
            document.getElementById('preview-parse').textContent = base + '/?url=xxx';
            document.getElementById('preview-player').textContent = base + '/mxjx?url=xxx';
        }

        document.addEventListener('DOMContentLoaded', () => {
            refreshRules();
            initAccessPreview();
        });
    </script>
</body>
</html>
