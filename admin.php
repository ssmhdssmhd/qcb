<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/AccelerationNodeManager.php';
require_once __DIR__ . '/src/UpdateManager.php';

$nodeManager = new AccelerationNodeManager();
$updateManager = new UpdateManager();

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        switch ($action) {
            case 'add_node':
                $node = $nodeManager->addNode([
                    'name' => $_POST['name'] ?? '',
                    'url' => $_POST['url'] ?? '',
                    'type' => $_POST['type'] ?? 'custom',
                    'enabled' => isset($_POST['enabled']),
                    'priority' => intval($_POST['priority'] ?? 100),
                    'note' => $_POST['note'] ?? ''
                ]);
                echo json_encode(['success' => true, 'data' => $node]);
                break;

            case 'update_node':
                $node = $nodeManager->updateNode(intval($_POST['id']), [
                    'name' => $_POST['name'] ?? '',
                    'url' => $_POST['url'] ?? '',
                    'type' => $_POST['type'] ?? 'custom',
                    'enabled' => isset($_POST['enabled']),
                    'priority' => intval($_POST['priority'] ?? 100),
                    'note' => $_POST['note'] ?? ''
                ]);
                echo json_encode(['success' => $node !== null, 'data' => $node]);
                break;

            case 'delete_node':
                $success = $nodeManager->deleteNode(intval($_POST['id']));
                echo json_encode(['success' => $success]);
                break;

            case 'toggle_node':
                $node = $nodeManager->toggleNode(intval($_POST['id']));
                echo json_encode(['success' => $node !== null, 'data' => $node]);
                break;

            case 'test_node':
                $node = $nodeManager->getNodeById(intval($_POST['id']));
                if ($node) {
                    $testResult = $nodeManager->testNode($node);
                    echo json_encode(['success' => true, 'data' => $testResult]);
                } else {
                    echo json_encode(['success' => false, 'error' => '节点不存在']);
                }
                break;

            case 'speed_test_all':
                $result = $updateManager->speedTest();
                $status = $updateManager->getUpdateStatus();
                echo json_encode([
                    'success' => true,
                    'best_node' => $result['best_node'],
                    'results' => $result['results'],
                    'smart_ranking' => $status['smart_ranking']
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

$nodes = $nodeManager->getAllNodes();
$status = $updateManager->getUpdateStatus();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QCB 在线更新系统</title>
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
        <div class="subtitle">加速节点管理 & 在线更新系统 | 仓库: <?php echo DEFAULT_GITHUB_REPO; ?> | 分支: <?php echo DEFAULT_GITHUB_BRANCH; ?></div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="value" id="stat-total"><?php echo count($nodes); ?></div>
            <div class="label">加速节点总数</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-enabled"><?php echo count(array_filter($nodes, fn($n) => $n['enabled'])); ?></div>
            <div class="label">已启用</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-best-node">-</div>
            <div class="label">最优节点</div>
        </div>
        <div class="stat-card">
            <div class="value" id="stat-last-update">-</div>
            <div class="label">最后更新</div>
        </div>
    </div>

    <div class="tabs">
        <button class="tab active" data-tab="nodes">📡 加速节点管理</button>
        <button class="tab" data-tab="update">🔄 在线更新</button>
        <button class="tab" data-tab="api">🌐 API 接口</button>
        <button class="tab" data-tab="log">📋 更新日志</button>
    </div>

    <!-- 加速节点管理 -->
    <div class="tab-content active" id="tab-nodes">
        <div class="card">
            <h2>📡 加速节点列表</h2>
            <div style="margin-bottom: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="openAddNodeModal()">➕ 添加加速节点</button>
                <button class="btn btn-warning" onclick="runSpeedTest()" id="btn-speed-test">⚡ 一键测速</button>
                <button class="btn btn-secondary" onclick="refreshNodes()">🔄 刷新</button>
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
                <tbody id="nodes-table-body">
                    <?php foreach ($nodes as $node): ?>
                    <tr id="node-row-<?php echo $node['id']; ?>">
                        <td><?php echo $node['id']; ?></td>
                        <td><?php echo htmlspecialchars($node['name']); ?></td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($node['url']); ?>">
                            <code><?php echo htmlspecialchars($node['url']); ?></code>
                        </td>
                        <td><span class="badge badge-type"><?php echo $node['type']; ?></span></td>
                        <td>
                            <span class="status <?php echo $node['enabled'] ? 'status-enabled' : 'status-disabled'; ?>">
                                <?php echo $node['enabled'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td><span class="priority-badge"><?php echo $node['priority']; ?></span></td>
                        <td><?php echo htmlspecialchars($node['note']); ?></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-secondary" onclick="testNode(<?php echo $node['id']; ?>)">⚡测速</button>
                                <button class="btn btn-sm btn-warning" onclick="openEditNodeModal(<?php echo $node['id']; ?>)">✏️编辑</button>
                                <button class="btn btn-sm btn-secondary" onclick="toggleNode(<?php echo $node['id']; ?>)"><?php echo $node['enabled'] ? '禁用' : '启用'; ?></button>
                                <button class="btn btn-sm btn-danger" onclick="deleteNode(<?php echo $node['id']; ?>)">🗑️删除</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

            <div class="card" id="speed-test-result" style="display: none;">
            <h2>⚡ 测速结果 <span style="font-size: 12px; color: #888; font-weight: normal;">（每节点3轮测试，取中位数）</span></h2>
            <div id="speed-test-content"></div>
        </div>

        <div class="card" id="smart-ranking" style="display: none;">
            <h2>🧠 智能评分排行 <span style="font-size: 12px; color: #888; font-weight: normal;">（基于历史成功率和连续失败惩罚）</span></h2>
            <div id="smart-ranking-content"></div>
        </div>
    </div>

    <!-- 在线更新 -->
    <div class="tab-content" id="tab-update">
        <div class="card">
            <h2>🔄 在线更新</h2>
            <p style="color: #666; margin-bottom: 15px;">从 GitHub 仓库 <?php echo DEFAULT_GITHUB_REPO; ?> 分支 <?php echo DEFAULT_GITHUB_BRANCH; ?> 拉取最新代码，自动选择最优加速节点</p>
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
                <button class="btn btn-success" onclick="checkUpdate()" id="btn-check-update">🔍 检查更新</button>
                <button class="btn btn-primary" onclick="runSpeedTest()">⚡ 重新测速</button>
                <button class="btn btn-warning" onclick="fetchFilePrompt()">📥 获取指定文件</button>
            </div>
            <div id="update-status" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <div id="current-best-node">当前最优节点: <span class="status status-pending">未测速</span></div>
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

            <h3>2. 获取所有加速节点</h3>
            <div class="api-url" onclick="copyToClipboard(this.textContent)"><?php echo (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php?action=nodes'; ?></div>
            <p><button class="btn btn-sm btn-primary" onclick="testApi('nodes')">测试接口</button></p>

            <h3>3. 测速所有节点</h3>
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
                            <tr><th>节点</th><th>状态</th><th>响应时间(ms)</th><th>速度</th><th>时间</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status['cache']['speed_test_results'] as $test): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($test['node_name']); ?></td>
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
                            <tr><th>文件</th><th>状态</th><th>使用节点</th><th>响应时间(ms)</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($status['cache']['update_results'] as $result): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($result['path']); ?></code></td>
                                <td><span class="status <?php echo $result['success'] ? 'status-success' : 'status-failed'; ?>"><?php echo $result['success'] ? '✓ 成功' : '✗ 失败'; ?></span></td>
                                <td><?php echo htmlspecialchars($result['node_used'] ?? '-'); ?></td>
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
<div class="modal-overlay" id="node-modal">
    <div class="modal">
        <h3 id="modal-title">添加加速节点</h3>
        <form id="node-form" onsubmit="return saveNode(event)">
            <input type="hidden" id="node-id">
            <div class="form-group">
                <label>名称 *</label>
                <input type="text" id="node-name" required>
            </div>
            <div class="form-group">
                <label>URL 模板 *</label>
                <input type="text" id="node-url" required placeholder="https://example.com/{owner}/{repo}/{branch}/{path}">
                <small style="color: #888;">可用变量: {owner}, {repo}, {branch}, {path}</small>
            </div>
            <div class="form-group">
                <label>类型</label>
                <select id="node-type">
                    <option value="cdn">CDN 加速</option>
                    <option value="proxy">代理加速</option>
                    <option value="direct">直连</option>
                    <option value="custom">自定义</option>
                </select>
            </div>
            <div class="form-group">
                <label>优先级</label>
                <input type="number" id="node-priority" value="100" min="0" max="999">
            </div>
            <div class="form-group">
                <label>备注</label>
                <textarea id="node-note"></textarea>
            </div>
            <div class="form-group form-check">
                <input type="checkbox" id="node-enabled" checked>
                <label for="node-enabled" style="margin: 0;">启用此节点</label>
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

document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
    });
});

function refreshNodes() {
    location.reload();
}

function openAddNodeModal() {
    document.getElementById('modal-title').textContent = '添加加速节点';
    document.getElementById('node-form').reset();
    document.getElementById('node-id').value = '';
    document.getElementById('node-enabled').checked = true;
    document.getElementById('node-priority').value = '100';
    document.getElementById('node-modal').classList.add('active');
}

function openEditNodeModal(id) {
    const row = document.getElementById('node-row-' + id);
    const cells = row.querySelectorAll('td');
    document.getElementById('modal-title').textContent = '编辑加速节点 #' + id;
    document.getElementById('node-id').value = id;
    document.getElementById('node-name').value = cells[1].textContent.trim();
    document.getElementById('node-url').value = cells[2].textContent.trim();
    document.getElementById('node-type').value = cells[3].textContent.trim();
    document.getElementById('node-priority').value = cells[5].textContent.trim().replace(/\D/g, '');
    document.getElementById('node-note').value = cells[6].textContent.trim();
    document.getElementById('node-enabled').checked = cells[4].textContent.trim() === '启用';
    document.getElementById('node-modal').classList.add('active');
}

function closeModal() {
    document.getElementById('node-modal').classList.remove('active');
}

function saveNode(e) {
    e.preventDefault();
    const id = document.getElementById('node-id').value;
    const action = id ? 'update_node' : 'add_node';
    const data = {
        action,
        id: id || undefined,
        name: document.getElementById('node-name').value,
        url: document.getElementById('node-url').value,
        type: document.getElementById('node-type').value,
        priority: document.getElementById('node-priority').value,
        note: document.getElementById('node-note').value,
        enabled: document.getElementById('node-enabled').checked ? '1' : ''
    };
    callApi(data).then(r => {
        if (r.success) {
            showToast(id ? '更新成功' : '添加成功');
            closeModal();
            refreshNodes();
        } else {
            showToast(r.error || '操作失败', 'error');
        }
    });
    return false;
}

function deleteNode(id) {
    if (!confirm('确定删除此加速节点？')) return;
    callApi({ action: 'delete_node', id }).then(r => {
        if (r.success) { showToast('删除成功'); refreshNodes(); }
        else showToast('删除失败', 'error');
    });
}

function toggleNode(id) {
    callApi({ action: 'toggle_node', id }).then(r => {
        if (r.success) showToast(r.data.enabled ? '已启用' : '已禁用');
        refreshNodes();
    });
}

function testNode(id) {
    showToast('正在测速（3轮）...', 'info');
    callApi({ action: 'test_node', id }).then(r => {
        const el = document.getElementById('speed-test-result');
        el.style.display = 'block';
        const d = r.data;
        const speedBar = d.success ? (d.response_time_ms < 500 ? 'speed-fast' : d.response_time_ms < 2000 ? 'speed-medium' : 'speed-slow') : '';
        const errorLabel = {
            'none': '',
            'timeout': '⏱ 超时',
            'connect_timeout': '🔌 连接超时',
            'dns_failure': '🌐 DNS 失败',
            'connection_refused': '🚫 连接被拒',
            'ssl_error': '🔒 SSL 错误',
            'network_error': '📡 网络错误',
            'client_error': '⚠️ 客户端错误',
            'server_error': '☠️ 服务端错误'
        };
        const label = errorLabel[d.error_type] || d.error_type;
        let attemptsHtml = '';
        if (d.all_attempts && d.all_attempts.length > 1) {
            attemptsHtml = '<details style="margin-top: 10px;"><summary style="cursor: pointer; color: #1e3c72; font-size: 13px;">查看 ' + d.retry_count + ' 轮测试详情</summary><div style="margin-top: 8px; padding-left: 10px; border-left: 2px solid #e0e0e0;">';
            d.all_attempts.forEach((a, i) => {
                attemptsHtml += `<div style="font-size: 12px; color: #666; margin: 2px 0;">第${i+1}轮: ${a.success ? '✓' : '✗'} ${a.response_time_ms}ms ${a.error_type && a.error_type !== 'none' ? '(' + (errorLabel[a.error_type] || a.error_type) + ')' : ''}</div>`;
            });
            attemptsHtml += '</div></details>';
        }
        document.getElementById('speed-test-content').innerHTML = `
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; flex-wrap: wrap;">
                <span class="status ${d.success ? 'status-success' : 'status-failed'}">${d.success ? '✓ 成功' : '✗ 失败'}</span>
                <strong>${d.node_name}</strong>
                <span>中位数: <strong>${d.response_time_ms}</strong> ms</span>
                <span>成功次数: <strong>${d.success_count}/${d.retry_count}</strong></span>
                <span>速度: <strong>${d.speed_bps}</strong> B/s</span>
                ${label ? '<span class="status status-failed">' + label + '</span>' : ''}
                <span class="speed-bar ${speedBar}" style="width: ${Math.min(100, d.success ? 1000 / Math.max(1, d.response_time_ms) * 100 : 5)}%"></span>
            </div>
            ${d.error ? `<div style="color: #dc3545; margin-top: 10px;">错误详情: ${d.error}</div>` : ''}
            ${attemptsHtml}
        `;
        showToast(d.success ? '测速完成' : '测速失败', d.success ? 'success' : 'error');
    });
}

function runSpeedTest() {
    showToast('正在测速所有加速节点（3轮/节点）...', 'info');
    const btn = document.getElementById('btn-speed-test');
    if (btn) btn.innerHTML = '<span class="loading"></span> 测速中...';
    
    callApi({ action: 'speed_test_all' }).then(r => {
        const el = document.getElementById('speed-test-result');
        el.style.display = 'block';
        const errorLabel = {
            'none': '-', 'timeout': '⏱超时', 'connect_timeout': '🔌连接超时',
            'dns_failure': '🌐DNS失败', 'connection_refused': '🚫连接被拒',
            'ssl_error': '🔒SSL错误', 'network_error': '📡网络错误',
            'client_error': '⚠️客户端错误', 'server_error': '☠️服务端错误'
        };
        let html = '<table><thead><tr><th>节点</th><th>状态</th><th>中位数(ms)</th><th>成功/轮数</th><th>速度(B/s)</th><th>错误类型</th><th>评级</th></tr></thead><tbody>';
        r.results.forEach(r => {
            const statusClass = r.success ? 'status-success' : 'status-failed';
            const errorTag = r.error_type && r.error_type !== 'none' ? errorLabel[r.error_type] || r.error_type : '-';
            const rating = !r.success ? '<span class="status status-failed">失败</span>' :
                r.response_time_ms < 500 ? '<span class="status status-fastest">🚀 极快</span>' :
                r.response_time_ms < 1000 ? '<span class="status status-success">⚡ 快</span>' :
                r.response_time_ms < 3000 ? '<span class="status status-pending">⏱ 一般</span>' :
                '<span class="status status-failed">🐢 慢</span>';
            html += `<tr>
                <td>${r.node_name}</td>
                <td><span class="status ${statusClass}">${r.success ? '✓' : '✗'}</span></td>
                <td>${r.response_time_ms}</td>
                <td>${r.success_count}/${r.retry_count}</td>
                <td>${r.success ? r.speed_bps : '-'}</td>
                <td>${errorTag}</td>
                <td>${rating}</td>
            </tr>`;
        });
        html += '</tbody></table>';
        if (r.best_node) {
            html += `<div style="margin-top: 15px; padding: 15px; background: #d4edda; border-radius: 8px;">
                <strong>🏆 最优节点: ${r.best_node.node_name}</strong>
                <span style="float: right;">中位数响应时间: ${r.best_node.response_time_ms} ms</span>
            </div>`;
        }
        document.getElementById('speed-test-content').innerHTML = html;

        if (r.smart_ranking && r.smart_ranking.length > 0) {
            const srEl = document.getElementById('smart-ranking');
            srEl.style.display = 'block';
            let srHtml = '<table><thead><tr><th>排名</th><th>节点</th><th>评分</th><th>成功率</th><th>连续失败</th><th>总请求</th></tr></thead><tbody>';
            r.smart_ranking.forEach((s, i) => {
                const rankColor = i === 0 ? '#ff6b6b' : i === 1 ? '#ffa502' : i === 2 ? '#ffd43b' : '#666';
                srHtml += `<tr>
                    <td><strong style="color: ${rankColor};">#${i+1}</strong></td>
                    <td>${s.node.name}</td>
                    <td><strong>${s.score.toFixed(1)}</strong></td>
                    <td>${s.success_rate}%</td>
                    <td>${s.consecutive_fail}</td>
                    <td>${s.total_requests}</td>
                </tr>`;
            });
            srHtml += '</tbody></table>';
            document.getElementById('smart-ranking-content').innerHTML = srHtml;
        }
        
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
        
        if (d.speed_test?.best_node) {
            html += `<div style="padding: 10px; background: #d4edda; border-radius: 8px; margin-bottom: 15px;">
                最优节点: <strong>${d.speed_test.best_node.node_name}</strong> (${d.speed_test.best_node.response_time_ms} ms)
            </div>`;
        }
        
        html += '<h3>文件检查结果</h3><table><thead><tr><th>文件</th><th>状态</th><th>使用节点</th><th>响应时间(ms)</th></tr></thead><tbody>';
        d.files.forEach(f => {
            html += `<tr>
                <td><code>${f.path}</code></td>
                <td><span class="status ${f.success ? 'status-success' : 'status-failed'}">${f.success ? '✓' : '✗'}</span></td>
                <td>${f.node_used || '-'}</td>
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
                使用节点: <strong>${r.data.node_name}</strong>
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
        document.getElementById('stat-total').textContent = d.node_count;
        document.getElementById('stat-enabled').textContent = d.enabled_node_count;
        document.getElementById('stat-best-node').textContent = d.cache.best_node_name || '-';
        document.getElementById('stat-last-update').textContent = d.cache.last_update || '-';
    });
}

function updateStatusDisplay() {
    callApi({ action: 'get_status' }).then(r => {
        const d = r.data;
        document.getElementById('current-best-node').innerHTML = '当前最优节点: ' + 
            (d.cache.best_node_name ? `<span class="status status-success">${d.cache.best_node_name}</span>` : '<span class="status status-pending">未测速</span>');
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

updateStats();
updateStatusDisplay();
</script>
</body>
</html>
