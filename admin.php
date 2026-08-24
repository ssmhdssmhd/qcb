<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/ResourceSiteManager.php';
require_once __DIR__ . '/src/UpdateManager.php';

$siteManager = new ResourceSiteManager();
$updateManager = new UpdateManager();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        switch ($action) {
            case 'add_site':
                $site = $siteManager->addSite([
                    'name' => $_POST['name'] ?? '',
                    'url' => $_POST['url'] ?? '',
                    'type' => $_POST['type'] ?? 'custom',
                    'enabled' => isset($_POST['enabled']),
                    'priority' => intval($_POST['priority'] ?? 100),
                    'note' => $_POST['note'] ?? ''
                ]);
                echo json_encode(['success' => true, 'data' => $site]);
                break;

            case 'update_site':
                $site = $siteManager->updateSite(intval($_POST['id']), [
                    'name' => $_POST['name'] ?? '',
                    'url' => $_POST['url'] ?? '',
                    'type' => $_POST['type'] ?? 'custom',
                    'enabled' => isset($_POST['enabled']),
                    'priority' => intval($_POST['priority'] ?? 100),
                    'note' => $_POST['note'] ?? ''
                ]);
                echo json_encode(['success' => $site !== null, 'data' => $site]);
                break;

            case 'delete_site':
                $success = $siteManager->deleteSite(intval($_POST['id']));
                echo json_encode(['success' => $success]);
                break;

            case 'toggle_site':
                $site = $siteManager->toggleSite(intval($_POST['id']));
                echo json_encode(['success' => $site !== null, 'data' => $site]);
                break;

            case 'test_site':
                $site = $siteManager->getSiteById(intval($_POST['id']));
                if ($site) {
                    $testResult = $siteManager->testSite($site);
                    echo json_encode(['success' => true, 'data' => $testResult]);
                } else {
                    echo json_encode(['success' => false, 'error' => '站点不存在']);
                }
                break;

            case 'speed_test_all':
                $results = $siteManager->testAllSites();
                $best = $siteManager->getBestSite($results);
                $updateManager->getCache();
                echo json_encode([
                    'success' => true,
                    'best_site' => $best,
                    'results' => $results
                ]);
                break;

            case 'check_update':
                $result = $updateManager->checkUpdate();
                echo json_encode(['success' => true, 'data' => $result]);
                break;

            case 'fetch_file':
                $result = $updateManager->fetchWithFallback($_POST['path'] ?? 'config.php');
                echo json_encode(['success' => $result['success'], 'data' => $result]);
                break;

            case 'get_status':
                $status = $updateManager->getUpdateStatus();
                echo json_encode(['success' => true, 'data' => $status]);
                break;

            default:
                echo json_encode(['success' => false, 'error' => '未知操作']);
        }
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

$sites = $siteManager->getAllSites();
$status = $updateManager->getUpdateStatus();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> v<?php echo APP_VERSION; ?> - 后台管理</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "PingFang SC", "Microsoft YaHei", sans-serif; background: #f0f2f5; color: #333; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 30px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(30,60,114,0.3); }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .header .subtitle { opacity: 0.9; font-size: 14px; }
        .header .badge { display: inline-block; background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 12px; font-size: 12px; margin-left: 10px; }
        .tabs { display: flex; gap: 5px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
        .tab { padding: 12px 24px; cursor: pointer; border: none; background: none; font-size: 15px; color: #666; border-bottom: 3px solid transparent; transition: all 0.3s; }
        .tab.active { color: #1e3c72; border-bottom-color: #1e3c72; font-weight: 600; }
        .tab:hover { color: #1e3c72; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .card { background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
        .card h2 { font-size: 18px; margin-bottom: 16px; color: #1e3c72; display: flex; align-items: center; gap: 8px; }
        .card h3 { font-size: 15px; margin: 16px 0 10px; color: #555; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .btn-primary { background: #1e3c72; color: white; }
        .btn-primary:hover { background: #16305a; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30,60,114,0.3); }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #22863c; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .btn-warning { background: #ffc107; color: #333; }
        .btn-warning:hover { background: #e0a800; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); text-align: center; }
        .stat-card .value { font-size: 28px; font-weight: bold; color: #1e3c72; }
        .stat-card .label { font-size: 13px; color: #888; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: 600; font-size: 13px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover { background: #f8f9fa; }
        .status { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 500; }
        .status-enabled { background: #d4edda; color: #155724; }
        .status-disabled { background: #f8d7da; color: #721c24; }
        .status-success { background: #d4edda; color: #155724; }
        .status-failed { background: #f8d7da; color: #721c24; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-fastest { background: #c3e6cb; color: #1e7e34; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; border-radius: 12px; padding: 30px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal h3 { margin-bottom: 20px; color: #1e3c72; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #1e3c72; }
        .form-group textarea { resize: vertical; min-height: 60px; }
        .form-check { display: flex; align-items: center; gap: 8px; }
        .form-check input { width: 18px; height: 18px; }
        .action-btns { display: flex; gap: 8px; }
        .loading { display: inline-block; width: 16px; height: 16px; border: 2px solid #f3f3f3; border-top: 2px solid #1e3c72; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .toast { position: fixed; top: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; font-weight: 500; z-index: 2000; animation: slideIn 0.3s ease-out; }
        .toast-success { background: #28a745; }
        .toast-error { background: #dc3545; }
        .toast-info { background: #17a2b8; }
        @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .speed-bar { display: inline-block; height: 8px; border-radius: 4px; min-width: 20px; }
        .speed-fast { background: #28a745; }
        .speed-medium { background: #ffc107; }
        .speed-slow { background: #dc3545; }
        .json-view { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 8px; font-family: 'Consolas', 'Monaco', monospace; font-size: 13px; max-height: 400px; overflow: auto; white-space: pre-wrap; word-break: break-all; }
        .badge-best { background: #ff6b6b; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; margin-left: 5px; }
        .api-url { background: #f8f9fa; padding: 10px 15px; border-radius: 8px; font-family: monospace; font-size: 13px; color: #1e3c72; margin: 10px 0; word-break: break-all; }
        .copy-btn { background: #1e3c72; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 12px; margin-left: 10px; }
        .copy-btn:hover { background: #16305a; }
        .update-log { background: #f8f9fa; border-radius: 8px; padding: 15px; font-family: monospace; font-size: 12px; max-height: 300px; overflow: auto; }
        .update-log .log-entry { margin-bottom: 8px; padding: 5px 10px; border-left: 3px solid #17a2b8; background: white; }
        .update-log .log-success { border-left-color: #28a745; }
        .update-log .log-error { border-left-color: #dc3545; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
        .empty-state .icon { font-size: 48px; margin-bottom: 10px; }
        .priority-badge { display: inline-block; background: #e9ecef; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🚀 <?php echo APP_NAME; ?> <span class="badge">v<?php echo APP_VERSION; ?></span></h1>
        <div class="subtitle">资源站管理 & 在线更新系统 | 仓库: <?php echo DEFAULT_GITHUB_REPO; ?> | 分支: <?php echo DEFAULT_GITHUB_BRANCH; ?></div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="value" id="stat-total"><?php echo count($sites); ?></div>
            <div class="label">资源站总数</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-enabled"><?php echo count(array_filter($sites, fn($s) => $s['enabled'])); ?></div>
            <div class="label">已启用</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-best-site">-</div>
            <div class="label">最优站点</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-last-update">-</div>
            <div class="label">最后更新</div>
        </div>
    </div>

    <div class="tabs">
        <button class="tab active" data-tab="sites">📡 资源站管理</button>
        <button class="tab" data-tab="update">🔄 在线更新</button>
        <button class="tab" data-tab="api">🌐 API 接口</button>
        <button class="tab" data-tab="log">📋 更新日志</button>
    </div>

    <!-- 资源站管理 -->
    <div class="tab-content active" id="tab-sites">
        <div class="card">
            <h2>📡 资源站列表</h2>
            <div style="margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="openAddModal()">➕ 添加资源站</button>
                <button class="btn btn-warning" onclick="runSpeedTest()" id="btn-speed-test">⚡ 一键测速</button>
                <button class="btn btn-secondary" onclick="refreshSites()">🔄 刷新</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>URL 模板</th>
                        <th>类型</th>
                        <th>状态</th>
                        <th>优先级</th>
                        <th>备注</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="sites-table-body">
                    <?php foreach ($sites as $site): ?>
                    <tr id="site-row-<?php echo $site['id']; ?>">
                        <td><?php echo $site['id']; ?></td>
                        <td><?php echo htmlspecialchars($site['name']); ?></td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($site['url']); ?>">
                            <code><?php echo htmlspecialchars($site['url']); ?></code>
                        </td>
                        <td><span class="badge badge-type"><?php echo $site['type']; ?></span></td>
                        <td>
                            <span class="status <?php echo $site['enabled'] ? 'status-enabled' : 'status-disabled'; ?>">
                                <?php echo $site['enabled'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td><span class="priority-badge"><?php echo $site['priority']; ?></span></td>
                        <td><?php echo htmlspecialchars($site['note']); ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-secondary" onclick="testSite(<?php echo $site['id']; ?>)">⚡测速</button>
                                <button class="btn btn-sm btn-warning" onclick="openEditModal(<?php echo $site['id']; ?>)">✏️编辑</button>
                                <button class="btn btn-sm btn-secondary" onclick="toggleSite(<?php echo $site['id']; ?>)"><?php echo $site['enabled'] ? '禁用' : '启用'; ?></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteSite(<?php echo $site['id']; ?>)">🗑️删除</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card" id="speed-test-result" style="display: none;">
            <h2>⚡ 测速结果</h2>
            <div id="speed-test-content"></div>
        </div>
    </div>

    <!-- 在线更新 -->
    <div class="tab-content" id="tab-update">
        <div class="card">
            <h2>🔄 在线更新</h2>
            <p style="color: #666; margin-bottom: 15px;">从 GitHub 仓库 <?php echo DEFAULT_GITHUB_REPO; ?> 分支 <?php echo DEFAULT_GITHUB_BRANCH; ?> 拉取最新代码，自动选择最优加速站</p>
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="btn btn-success" onclick="checkUpdate()" id="btn-check-update">🔍 检查更新</button>
                <button class="btn btn-primary" onclick="runSpeedTest()">⚡ 重新测速</button>
                <button class="btn btn-warning" onclick="fetchFilePrompt()">📥 获取指定文件</button>
            </div>
            <div id="update-status" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <div id="current-best-site">当前最优站点: <span class="status status-pending">未测速</span></div>
                <div id="current-last-test">上次测速: -</div>
                <div id="current-last-update">最后更新: -</div>
            </div>
        </div>

        <div class="card" id="update-result" style="display: none;">
            <h2>📦 更新结果</h2>
            <div id="update-result-content"></div>
        </div>
    </div>

    <!-- API 接口 -->
    <div class="tab-content" id="tab-api">
        <div class="card">
            <h2>🌐 API 接口说明</h2>
            <p style="color: #666; margin-bottom: 20px;">所有接口返回 JSON 格式数据</p>
            
            <h3>1. 获取系统状态</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=status'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('status')">测试接口</button></p>

            <h3>2. 获取所有资源站</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=sites'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('sites')">测试接口</button></p>

            <h3>3. 测速所有站点</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=speed_test'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('speed_test')">测试接口</button></p>

            <h3>4. 检查更新</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=check_update'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('check_update')">测试接口</button></p>

            <h3>5. 获取远程文件</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=fetch_file&path=config.php'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('fetch_file&path=config.php')">测试接口</button></p>

            <h3>接口返回示例</h3>
            <div id="api-response" class="json-view">点击"测试接口"查看返回结果...</div>
        </div>
    </div>

    <!-- 更新日志 -->
    <div class="tab-content" id="tab-log">
        <div class="card">
            <h2>📋 更新日志</h2>
            <div id="log-content">
                <?php if (!empty($status['cache']['speed_test_results'])): ?>
                    <h3>最近测速记录</h3>
                    <table>
                        <thead>
                            <tr><th>站点</th><th>状态</th><th>响应时间(ms)</th><th>速度</th><th>时间</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status['cache']['speed_test_results'] as $test): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($test['site_name']); ?></td>
                                <td><span class="status <?php echo $test['success'] ? 'status-success' : 'status-failed'; ?>"><?php echo $test['success'] ? '✓ 成功' : '✗ 失败'; ?></span></td>
                                <td><?php echo $test['response_time_ms']; ?></td>
                                <td><?php echo $test['speed_bps']; ?> B/s</td>
                                <td><?php echo $test['tested_at'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (!empty($status['cache']['update_results'])): ?>
                    <h3>最近更新记录</h3>
                    <table>
                        <thead>
                            <tr><th>文件</th><th>状态</th><th>使用站点</th><th>响应时间(ms)</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status['cache']['update_results'] as $result): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($result['path']); ?></code></td>
                                <td><span class="status <?php echo $result['success'] ? 'status-success' : 'status-failed'; ?>"><?php echo $result['success'] ? '✓ 成功' : '✗ 失败'; ?></span></td>
                                <td><?php echo htmlspecialchars($result['site_used'] ?? '-'); ?></td>
                                <td><?php echo $result['response_time_ms'] ?? '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <?php if (empty($status['cache']['speed_test_results']) && empty($status['cache']['update_results'])): ?>
                    <div class="empty-state">
                        <div class="icon">📋</div>
                        <p>暂无日志记录</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 编辑/添加模态框 -->
<div class="modal-overlay" id="site-modal">
    <div class="modal">
        <h3 id="modal-title">添加资源站</h3>
        <form id="site-form" onsubmit="return saveSite(event)">
            <input type="hidden" id="site-id">
            <div class="form-group">
                <label>名称 *</label>
                <input type="text" id="site-name" required>
            </div>
            <div class="form-group">
                <label>URL 模板 *</label>
                <input type="text" id="site-url" required placeholder="https://example.com/{owner}/{repo}/{branch}/{path}">
                <small style="color: #888;">可用变量: {owner}, {repo}, {branch}, {path}</small>
            </div>
            <div class="form-group">
                <label>类型</label>
                <select id="site-type">
                    <option value="cdn">CDN 加速</option>
                    <option value="proxy">代理加速</option>
                    <option value="direct">直连</option>
                    <option value="custom">自定义</option>
                </select>
            </div>
            <div class="form-group">
                <label>优先级</label>
                <input type="number" id="site-priority" value="100" min="0" max="999">
            </div>
            <div class="form-group">
                <label>备注</label>
                <textarea id="site-note"></textarea>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="site-enabled" checked>
                <label for="site-enabled" style="margin: 0;">启用此站点</label>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">取消</button>
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast 提示 -->
<div id="toast-container"></div>

<script>
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => showToast('已复制到剪贴板'));
}

function callApi(data) {
    return fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams(data).toString()
    }).then(r => r.json());
}

// Tab 切换
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});

// 资源站管理
function refreshSites() {
    location.reload();
}

function openAddModal() {
    document.getElementById('modal-title').textContent = '添加资源站';
    document.getElementById('site-form').reset();
    document.getElementById('site-id').value = '';
    document.getElementById('site-enabled').checked = true;
    document.getElementById('site-priority').value = '100';
    document.getElementById('site-modal').classList.add('active');
}

function openEditModal(id) {
    const row = document.getElementById('site-row-' + id);
    const cells = row.querySelectorAll('td');
    document.getElementById('modal-title').textContent = '编辑资源站 #' + id;
    document.getElementById('site-id').value = id;
    document.getElementById('site-name').value = cells[1].textContent.trim();
    document.getElementById('site-url').value = cells[2].textContent.trim();
    document.getElementById('site-type').value = cells[3].textContent.trim();
    document.getElementById('site-priority').value = cells[5].textContent.trim().replace(/\D/g, '');
    document.getElementById('site-note').value = cells[6].textContent.trim();
    document.getElementById('site-enabled').checked = cells[4].textContent.trim() === '启用';
    document.getElementById('site-modal').classList.add('active');
}

function closeModal() {
    document.getElementById('site-modal').classList.remove('active');
}

function saveSite(e) {
    e.preventDefault();
    const id = document.getElementById('site-id').value;
    const action = id ? 'update_site' : 'add_site';
    const data = {
        action,
        id: id || undefined,
        name: document.getElementById('site-name').value,
        url: document.getElementById('site-url').value,
        type: document.getElementById('site-type').value,
        priority: document.getElementById('site-priority').value,
        note: document.getElementById('site-note').value,
        enabled: document.getElementById('site-enabled').checked ? '1' : ''
    };
    callApi(data).then(r => {
        if (r.success) {
            showToast(id ? '更新成功' : '添加成功');
            closeModal();
            refreshSites();
        } else {
            showToast(r.error || '操作失败', 'error');
        }
    });
    return false;
}

function deleteSite(id) {
    if (!confirm('确定删除此资源站？')) return;
    callApi({ action: 'delete_site', id }).then(r => {
        if (r.success) { showToast('删除成功'); refreshSites(); }
        else showToast('删除失败', 'error');
    });
}

function toggleSite(id) {
    callApi({ action: 'toggle_site', id }).then(r => {
        if (r.success) showToast(r.data.enabled ? '已启用' : '已禁用');
        refreshSites();
    });
}

function testSite(id) {
    showToast('正在测速...', 'info');
    callApi({ action: 'test_site', id }).then(r => {
        const el = document.getElementById('speed-test-result');
        el.style.display = 'block';
        const d = r.data;
        const speedBar = d.success ? (d.response_time_ms < 500 ? 'speed-fast' : d.response_time_ms < 2000 ? 'speed-medium' : 'speed-slow') : '';
        document.getElementById('speed-test-content').innerHTML = `
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <span class="status ${d.success ? 'status-success' : 'status-failed'}">${d.success ? '✓ 成功' : '✗ 失败'}</span>
                <strong>${d.site_name}</strong>
                <span>响应时间: <strong>${d.response_time_ms}</strong> ms</span>
                <span>下载: <strong>${d.download_size}</strong> bytes</span>
                <span>速度: <strong>${d.speed_bps}</strong> B/s</span>
                <span class="speed-bar ${speedBar}" style="width: ${Math.min(100, d.success ? 1000 / d.response_time_ms * 100 : 5)}%"></span>
            </div>
            ${d.error ? `<div style="color: #dc3545; margin-top: 10px;">错误: ${d.error}</div>` : ''}
        `;
        showToast(d.success ? '测速完成' : '测速失败', d.success ? 'success' : 'error');
    });
}

function runSpeedTest() {
    showToast('正在测速所有资源站...', 'info');
    const btn = document.getElementById('btn-speed-test');
    if (btn) btn.innerHTML = '<span class="loading"></span> 测速中...';
    
    callApi({ action: 'speed_test_all' }).then(r => {
        const el = document.getElementById('speed-test-result');
        el.style.display = 'block';
        let html = '<table><thead><tr><th>站点</th><th>状态</th><th>响应时间(ms)</th><th>速度(B/s)</th><th>评级</th></tr></thead><tbody>';
        r.results.forEach(r => {
            const statusClass = r.success ? 'status-success' : 'status-failed';
            const rating = !r.success ? '<span class="status status-failed">失败</span>' :
                r.response_time_ms < 500 ? '<span class="status status-fastest">🚀 极快</span>' :
                r.response_time_ms < 1000 ? '<span class="status status-success">⚡ 快</span>' :
                r.response_time_ms < 3000 ? '<span class="status status-pending">⏱ 一般</span>' :
                '<span class="status status-failed">🐢 慢</span>';
            html += `<tr>
                <td>${r.site_name} ${r.site_id === (r.best_site?.site_id) ? '' : ''}</td>
                <td><span class="status ${statusClass}">${r.success ? '✓' : '✗'}</span></td>
                <td>${r.response_time_ms}</td>
                <td>${r.success ? r.speed_bps : '-'}</td>
                <td>${rating}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        if (r.best_site) {
            html += `<div style="margin-top: 15px; padding: 15px; background: #d4edda; border-radius: 8px;">
                <strong>🏆 最优站点: ${r.best_site.site_name}</strong>
                <span style="float: right;">响应时间: ${r.best_site.response_time_ms} ms</span>
            </div>`;
        }
        document.getElementById('speed-test-content').innerHTML = html;
        
        if (btn) btn.innerHTML = '⚡ 一键测速';
        updateStats();
        showToast('测速完成');
    }).catch(() => {
        if (btn) btn.innerHTML = '⚡ 一键测速';
    });
}

function checkUpdate() {
    showToast('正在检查更新...', 'info');
    const btn = document.getElementById('btn-check-update');
    btn.innerHTML = '<span class="loading"></span> 检查中...';
    
    callApi({ action: 'check_update' }).then(r => {
        const el = document.getElementById('update-result');
        el.style.display = 'block';
        const d = r.data;
        let html = `<div style="margin-bottom: 15px;">
            <span class="status ${d.status === 'success' ? 'status-success' : 'status-failed'}">${d.message}</span>
            <span style="float: right; color: #888;">${d.checked_at}</span>
        </div>`;
        
        if (d.speed_test?.best_site) {
            html += `<div style="padding: 10px; background: #d4edda; border-radius: 8px; margin-bottom: 15px;">
                最优站点: <strong>${d.speed_test.best_site.site_name}</strong> (${d.speed_test.best_site.response_time_ms} ms)
            </div>`;
        }
        
        html += '<h3>文件检查结果</h3><table><thead><tr><th>文件</th><th>状态</th><th>使用站点</th><th>响应时间(ms)</th></tr></thead><tbody>';
        d.files.forEach(f => {
            html += `<tr>
                <td><code>${f.path}</code></td>
                <td><span class="status ${f.success ? 'status-success' : 'status-failed'}">${f.success ? '✓' : '✗'}</span></td>
                <td>${f.site_used || '-'}</td>
                <td>${f.response_time_ms || '-'}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        
        if (d.summary) {
            html += `<div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                总计: ${d.summary.total_files} 个文件，成功: <strong style="color: #28a745;">${d.summary.success_count}</strong>，失败: <strong style="color: #dc3545;">${d.summary.fail_count}</strong>
            </div>`;
        }
        
        document.getElementById('update-result-content').innerHTML = html;
        btn.innerHTML = '🔍 检查更新';
        updateStatusDisplay();
        showToast(d.message);
    }).catch(() => {
        btn.innerHTML = '🔍 检查更新';
    });
}

function fetchFilePrompt() {
    const path = prompt('请输入要获取的文件路径 (例如: config.php)');
    if (!path) return;
    showToast('正在获取文件...', 'info');
    callApi({ action: 'fetch_file', path }).then(r => {
        const el = document.getElementById('update-result');
        el.style.display = 'block';
        if (r.success) {
            let html = `<div style="padding: 10px; background: #d4edda; border-radius: 8px; margin-bottom: 15px;">
                <span class="status status-success">✓ 获取成功</span>
                使用站点: <strong>${r.data.site_used}</strong>
                响应时间: <strong>${r.data.response_time_ms} ms</strong>
            </div>`;
            html += '<h3>文件内容</h3><pre class="json-view">' + escapeHtml(r.data.data || '') + '</pre>';
            document.getElementById('update-result-content').innerHTML = html;
        } else {
            document.getElementById('update-result-content').innerHTML = `<div style="padding: 15px; background: #f8d7da; border-radius: 8px; color: #721c24;">获取失败: ${r.error}</div>`;
        }
        showToast(r.success ? '获取成功' : '获取失败', r.success ? 'success' : 'error');
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateStats() {
    callApi({ action: 'get_status' }).then(r => {
        const d = r.data;
        document.getElementById('stat-total').textContent = d.site_count;
        document.getElementById('stat-enabled').textContent = d.enabled_site_count;
        document.getElementById('stat-best-site').textContent = d.cache.best_site_name || '-';
        document.getElementById('stat-last-update').textContent = d.cache.last_update || '-';
    });
}

function updateStatusDisplay() {
    callApi({ action: 'get_status' }).then(r => {
        const d = r.data;
        document.getElementById('current-best-site').innerHTML = '当前最优站点: ' + 
            (d.cache.best_site_name ? `<span class="status status-success">${d.cache.best_site_name}</span>` : '<span class="status status-pending">未测速</span>');
        document.getElementById('current-last-test').textContent = '上次测速: ' + (d.cache.last_speed_test || '-');
        document.getElementById('current-last-update').textContent = '最后更新: ' + (d.cache.last_update || '-');
    });
}

function testApi(action) {
    fetch('api.php?action=' + action)
        .then(r => r.text())
        .then(text => {
            document.getElementById('api-response').textContent = text;
            document.getElementById('api-response').scrollIntoView({ behavior: 'smooth' });
        })
        .catch(e => {
            document.getElementById('api-response').textContent = '请求失败: ' + e.message;
        });
}

// 初始化
updateStats();
updateStatusDisplay();
</script>
</body>
</html>
