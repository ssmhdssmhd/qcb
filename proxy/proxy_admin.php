<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once __DIR__ . '/ProxyManager.php';
$proxyMgr = new ProxyManager();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!empty($action) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    
    $result = ['success' => false, 'message' => '未知操作'];
    
    switch ($action) {
        case 'get_stats':
            $result = ['success' => true, 'data' => $proxyMgr->getStats()];
            break;
            
        case 'get_proxies':
            $allProxies = $proxyMgr->getAllProxies();
            // 过滤掉失败的代理，只返回启用的
            $allProxies = array_filter($allProxies, function($p) {
                return ($p['status'] ?? 'active') === 'active';
            });
            // 按响应时间从快到慢排序
            usort($allProxies, function($a, $b) {
                $ta = $a['response_time'] ?? 0;
                $tb = $b['response_time'] ?? 0;
                if ($ta > 0 && $tb <= 0) return -1;
                if ($ta <= 0 && $tb > 0) return 1;
                if ($ta > 0 && $tb > 0) return $ta - $tb;
                return ($a['priority'] ?? 100) - ($b['priority'] ?? 100);
            });
            $allProxies = array_values($allProxies);
            $result = ['success' => true, 'data' => $allProxies];
            break;
            
        case 'add_proxy':
            $result = $proxyMgr->addProxy($_POST);
            break;
            
        case 'update_proxy':
            $id = $_POST['id'] ?? '';
            $result = $proxyMgr->updateProxy($id, $_POST);
            break;
            
        case 'delete_proxy':
            $id = $_POST['id'] ?? '';
            $result = $proxyMgr->deleteProxy($id);
            break;
            
        case 'test_proxy':
            $id = $_POST['id'] ?? '';
            $result = $proxyMgr->testProxy($id);
            break;
            
        case 'check_all':
            $result = ['success' => true, 'data' => $proxyMgr->checkAllProxies()];
            break;
            
        case 'toggle_enabled':
            $enabled = !empty($_POST['enabled']);
            $result = $proxyMgr->setEnabled($enabled);
            break;
            
        case 'toggle_auto_switch':
            $autoSwitch = !empty($_POST['auto_switch']);
            $result = $proxyMgr->setAutoSwitch($autoSwitch);
            break;
            
        case 'import_proxies':
            $text = $_POST['proxies'] ?? '';
            $type = $_POST['type'] ?? 'http';
            $result = $proxyMgr->importProxies($text, $type);
            break;

        case 'fetch_from_web':
            $verify = !isset($_POST['verify']) || $_POST['verify'] == 'true';
            $maxPerSource = intval($_POST['max_per_source'] ?? 20);
            $result = $proxyMgr->fetchProxiesFromWeb($verify, $maxPerSource);
            break;

        case 'sync_fast':
            $maxPerSource = intval($_POST['max_per_source'] ?? 20);
            $result = $proxyMgr->syncProxiesFast($maxPerSource);
            break;

        case 'clear_fetch_cache':
            $result = $proxyMgr->clearFetchCache();
            break;

        case 'clear_inactive':
            $result = $proxyMgr->clearInactiveProxies();
            break;

        case 'clear_all':
            $result = $proxyMgr->clearAllProxies();
            break;
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$stats = $proxyMgr->getStats();
$proxies = $proxyMgr->getAllProxies();

// 过滤掉失败的代理，只显示启用的
$proxies = array_filter($proxies, function($p) {
    return ($p['status'] ?? 'active') === 'active';
});

// 按响应时间从快到慢排序（有响应时间的在前，无响应时间的在后）
usort($proxies, function($a, $b) {
    $ta = $a['response_time'] ?? 0;
    $tb = $b['response_time'] ?? 0;
    // 有响应时间的排在前面
    if ($ta > 0 && $tb <= 0) return -1;
    if ($ta <= 0 && $tb > 0) return 1;
    // 都有响应时间，按快到慢排序
    if ($ta > 0 && $tb > 0) return $ta - $tb;
    // 都没有响应时间，按优先级排序
    return ($a['priority'] ?? 100) - ($b['priority'] ?? 100);
});
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>代理池管理 - M3U8广告分析系统</title>
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
            --border-base: #dcdfe6;
            --border-light: #e4e7ed;
            --success: #67c23a;
            --success-light: #f0f9eb;
            --danger: #f56c6c;
            --danger-light: #fef0f0;
            --warning: #e6a23c;
            --warning-light: #fdf6ec;
            --shadow-base: 0 2px 12px rgba(0,0,0,0.05);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            padding: 20px;
        }
        
        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 { font-size: 24px; font-weight: 600; }
        .header .back-btn {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 14px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-base);
        }
        
        .stat-card .label { color: var(--text-secondary); font-size: 14px; margin-bottom: 8px; }
        .stat-card .value { font-size: 28px; font-weight: 600; color: var(--primary); }
        .stat-card .sub { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }
        
        .card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-base);
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .settings-row {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        
        .setting-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .switch {
            position: relative;
            width: 50px;
            height: 26px;
            background: #ccc;
            border-radius: 13px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .switch.active { background: var(--primary); }
        
        .switch::after {
            content: '';
            position: absolute;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: transform 0.3s;
        }
        
        .switch.active::after { transform: translateX(24px); }
        
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-success { background: var(--success); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        
        .btn-group { display: flex; gap: 8px; flex-wrap: wrap; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
        }
        
        th {
            background: var(--fill-lighter, #f5f7fa);
            font-weight: 600;
            font-size: 14px;
            color: var(--text-regular);
        }
        
        tr:hover { background: var(--fill-lighter, #f5f7fa); }
        
        .status-badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            display: inline-block;
        }
        
        .status-active { background: var(--success-light); color: var(--success); }
        .status-inactive { background: var(--danger-light); color: var(--danger); }
        
        input, select, textarea {
            padding: 8px 12px;
            border: 1px solid var(--border-base);
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 13px; color: var(--text-regular); }
        
        .import-section textarea {
            width: 100%;
            min-height: 100px;
            font-family: monospace;
            font-size: 13px;
        }
        
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-overlay.show { display: flex; }
        
        .modal {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-title { font-size: 18px; font-weight: 600; margin-bottom: 16px; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px; }
        
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            z-index: 2000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s;
        }
        
        .toast.show { opacity: 1; transform: translateX(0); }
        .toast.success { background: var(--success); }
        .toast.error { background: var(--danger); }
        
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header { padding: 16px 20px; }
            .header h1 { font-size: 20px; }
            th, td { padding: 8px; font-size: 13px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔄 代理池管理</h1>
        <a href="../mxadmin.php" class="back-btn">← 返回后台</a>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">代理总数</div>
            <div class="value" id="stat-total"><?php echo $stats['total']; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">可用代理</div>
            <div class="value" style="color:var(--success)" id="stat-active"><?php echo $stats['active']; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">总成功次数</div>
            <div class="value" id="stat-success"><?php echo $stats['total_success']; ?></div>
        </div>
        <div class="stat-card">
            <div class="label">平均响应</div>
            <div class="value" style="color:var(--warning)" id="stat-rt"><?php echo $stats['avg_response_time']; ?>ms</div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">全局设置</div>
        <div class="settings-row">
            <div class="setting-item">
                <span>启用代理池</span>
                <div class="switch <?php echo $stats['enabled'] ? 'active' : ''; ?>" id="switch-enabled" onclick="toggleEnabled()"></div>
            </div>
            <div class="setting-item">
                <span>自动切换代理</span>
                <div class="switch <?php echo $stats['auto_switch'] ? 'active' : ''; ?>" id="switch-auto" onclick="toggleAutoSwitch()"></div>
            </div>
            <div class="btn-group">
                <button class="btn btn-success" onclick="syncFast()">⚡ 快速同步代理池</button>
                <button class="btn btn-success" onclick="fetchFromWeb()">🌐 一键获取代理</button>
                <button class="btn btn-success" onclick="checkAllProxies()">🔍 检测全部</button>
                <button class="btn btn-primary" onclick="showAddModal()">➕ 添加代理</button>
                <button class="btn btn-warning" onclick="showImportModal()">📥 批量导入</button>
                <button class="btn btn-secondary" onclick="clearInactive()">🗑️ 清理失效</button>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-title">代理列表</div>
        <div style="overflow-x:auto">
            <table>
                <thead>
                    <tr>
                        <th>名称</th>
                        <th>类型</th>
                        <th>地址</th>
                        <th>状态</th>
                        <th>优先级</th>
                        <th>成功/失败</th>
                        <th>响应时间</th>
                        <th>最后检测</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody id="proxy-table">
                    <?php foreach ($proxies as $proxy): ?>
                    <tr data-id="<?php echo $proxy['id']; ?>">
                        <td><?php echo htmlspecialchars($proxy['name']); ?></td>
                        <td><?php echo strtoupper($proxy['type']); ?></td>
                        <td><?php echo htmlspecialchars($proxy['host'] . ':' . $proxy['port']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $proxy['status'] === 'active' ? 'active' : 'inactive'; ?>">
                                <?php echo $proxy['status'] === 'active' ? '启用' : '停用'; ?>
                            </span>
                        </td>
                        <td><?php echo $proxy['priority'] ?? 100; ?></td>
                        <td>
                            <span style="color:var(--success)"><?php echo $proxy['success_count'] ?? 0; ?></span>
                            /
                            <span style="color:var(--danger)"><?php echo $proxy['fail_count'] ?? 0; ?></span>
                        </td>
                        <td><?php echo !empty($proxy['response_time']) ? $proxy['response_time'] . 'ms' : '-'; ?></td>
                        <td style="font-size:12px;color:var(--text-secondary)"><?php echo $proxy['last_check'] ?? '-'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="testProxy('<?php echo $proxy['id']; ?>')">测试</button>
                            <button class="btn btn-sm btn-warning" onclick="editProxy('<?php echo $proxy['id']; ?>')">编辑</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProxy('<?php echo $proxy['id']; ?>')">删除</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($proxies)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center;color:var(--text-secondary);padding:40px">
                            暂无代理，点击"添加代理"开始使用
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="modal-overlay" id="add-modal">
        <div class="modal">
            <div class="modal-title" id="modal-title">添加代理</div>
            <input type="hidden" id="edit-id">
            <div class="form-row">
                <div class="form-group">
                    <label>代理类型</label>
                    <select id="proxy-type">
                        <option value="http">HTTP</option>
                        <option value="https">HTTPS</option>
                        <option value="socks5">SOCKS5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>代理地址</label>
                    <input type="text" id="proxy-host" placeholder="例如: 192.168.1.1">
                </div>
                <div class="form-group">
                    <label>端口</label>
                    <input type="number" id="proxy-port" placeholder="例如: 8080">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>用户名 (可选)</label>
                    <input type="text" id="proxy-username" placeholder="代理认证用户名">
                </div>
                <div class="form-group">
                    <label>密码 (可选)</label>
                    <input type="password" id="proxy-password" placeholder="代理认证密码">
                </div>
                <div class="form-group">
                    <label>优先级</label>
                    <input type="number" id="proxy-priority" value="100" placeholder="数字越小优先级越高">
                </div>
            </div>
            <div class="form-group">
                <label>名称 (可选)</label>
                <input type="text" id="proxy-name" placeholder="留空自动生成">
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="hideModal('add-modal')">取消</button>
                <button class="btn btn-primary" onclick="saveProxy()">保存</button>
            </div>
        </div>
    </div>
    
    <div class="modal-overlay" id="import-modal">
        <div class="modal">
            <div class="modal-title">批量导入代理</div>
            <div class="form-group">
                <label>代理类型</label>
                <select id="import-type">
                    <option value="http">HTTP</option>
                    <option value="https">HTTPS</option>
                    <option value="socks5">SOCKS5</option>
                </select>
            </div>
            <div class="form-group import-section">
                <label>代理列表 (每行一个，格式: host:port 或 http://host:port)</label>
                <textarea id="import-text" placeholder="192.168.1.1:8080&#10;http://192.168.1.2:3128&#10;socks5://user:pass@192.168.1.3:1080"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="hideModal('import-modal')">取消</button>
                <button class="btn btn-primary" onclick="importProxies()">导入</button>
            </div>
        </div>
    </div>
    
    <div class="toast" id="toast"></div>
    
    <script>
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast ' + type;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
        
        function showModal(id) {
            document.getElementById(id).classList.add('show');
        }
        
        function hideModal(id) {
            document.getElementById(id).classList.remove('show');
        }
        
        function showAddModal() {
            document.getElementById('modal-title').textContent = '添加代理';
            document.getElementById('edit-id').value = '';
            document.getElementById('proxy-type').value = 'http';
            document.getElementById('proxy-host').value = '';
            document.getElementById('proxy-port').value = '';
            document.getElementById('proxy-username').value = '';
            document.getElementById('proxy-password').value = '';
            document.getElementById('proxy-priority').value = '100';
            document.getElementById('proxy-name').value = '';
            showModal('add-modal');
        }
        
        function editProxy(id) {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;
            
            const cells = row.querySelectorAll('td');
            document.getElementById('modal-title').textContent = '编辑代理';
            document.getElementById('edit-id').value = id;
            showModal('add-modal');
        }
        
        function showImportModal() {
            document.getElementById('import-text').value = '';
            showModal('import-modal');
        }
        
        async function apiCall(action, data = {}) {
            const formData = new FormData();
            formData.append('action', action);
            for (const key in data) {
                formData.append(key, data[key]);
            }
            
            const scriptName = window.location.pathname.split('/').pop() || 'proxy_admin.php';
            const res = await fetch(scriptName, {
                method: 'POST',
                body: formData
            });
            
            if (!res.ok) {
                throw new Error('网络请求失败: ' + res.status);
            }
            
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('返回数据格式错误');
            }
        }
        
        async function saveProxy() {
            const id = document.getElementById('edit-id').value;
            const data = {
                type: document.getElementById('proxy-type').value,
                host: document.getElementById('proxy-host').value.trim(),
                port: document.getElementById('proxy-port').value,
                username: document.getElementById('proxy-username').value.trim(),
                password: document.getElementById('proxy-password').value,
                priority: document.getElementById('proxy-priority').value,
                name: document.getElementById('proxy-name').value.trim()
            };
            
            if (!data.host || !data.port) {
                showToast('请填写代理地址和端口', 'error');
                return;
            }
            
            let result;
            if (id) {
                data.id = id;
                result = await apiCall('update_proxy', data);
            } else {
                result = await apiCall('add_proxy', data);
            }
            
            if (result.success) {
                showToast(result.message || '保存成功');
                hideModal('add-modal');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(result.message || '保存失败', 'error');
            }
        }
        
        async function deleteProxy(id) {
            if (!confirm('确定要删除这个代理吗？')) return;
            const result = await apiCall('delete_proxy', { id });
            if (result.success) {
                showToast('删除成功');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(result.message || '删除失败', 'error');
            }
        }
        
        async function testProxy(id) {
            showToast('正在测试...', 'success');
            const result = await apiCall('test_proxy', { id });
            if (result.success) {
                showToast(`测试成功，响应时间 ${result.response_time}ms`);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('测试失败: ' + result.message, 'error');
            }
        }
        
        async function checkAllProxies() {
            if (!confirm('确定要检测全部代理吗？这可能需要一些时间。')) return;
            showToast('正在检测...', 'success');
            const result = await apiCall('check_all');
            if (result.success) {
                const successCount = result.data.filter(r => r.success).length;
                showToast(`检测完成，可用 ${successCount}/${result.data.length} 个`);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('检测失败', 'error');
            }
        }
        
        async function toggleEnabled() {
            const sw = document.getElementById('switch-enabled');
            const isActive = sw.classList.contains('active');
            const result = await apiCall('toggle_enabled', { enabled: isActive ? 0 : 1 });
            if (result.success) {
                sw.classList.toggle('active');
                showToast(isActive ? '已停用代理池' : '已启用代理池');
            }
        }
        
        async function toggleAutoSwitch() {
            const sw = document.getElementById('switch-auto');
            const isActive = sw.classList.contains('active');
            const result = await apiCall('toggle_auto_switch', { auto_switch: isActive ? 0 : 1 });
            if (result.success) {
                sw.classList.toggle('active');
                showToast(isActive ? '已关闭自动切换' : '已开启自动切换');
            }
        }
        
        async function importProxies() {
            const text = document.getElementById('import-text').value.trim();
            const type = document.getElementById('import-type').value;
            
            if (!text) {
                showToast('请输入代理列表', 'error');
                return;
            }
            
            const result = await apiCall('import_proxies', { proxies: text, type });
            if (result.success) {
                showToast(`导入成功：新增 ${result.added} 个，失败 ${result.failed} 个`);
                hideModal('import-modal');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message || '导入失败', 'error');
            }
        }

        async function syncFast() {
            showToast('正在从 proxy.scdn.io 等代理源并发同步...', 'success');
            
            const btn = event?.target;
            const originalText = btn ? btn.textContent : '';
            if (btn) {
                btn.disabled = true;
                btn.textContent = '同步中...';
            }
            
            try {
                const result = await apiCall('sync_fast', { 
                    max_per_source: 20 
                });
                if (result.success) {
                    let msg = result.message || `成功同步 ${result.added} 个代理`;
                    if (result.sources) {
                        const successSources = result.sources.filter(s => s.success).length;
                        msg += `（${successSources}/${result.sources.length} 个源成功）`;
                    }
                    if (result.from_cache) {
                        msg += ' [缓存]';
                    }
                    showToast(msg, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || '同步失败', 'error');
                }
            } catch (e) {
                showToast('同步失败: ' + e.message, 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        }

        async function fetchFromWeb() {
            const verify = confirm('获取后要自动验证代理可用性吗？\n\n验证会更慢但能确保代理可用。\n点击「确定」= 验证（推荐）\n点击「取消」= 不验证，快速获取');
            
            showToast('正在从网络获取代理，请稍候...', 'success');
            
            const btn = event?.target;
            const originalText = btn ? btn.textContent : '';
            if (btn) {
                btn.disabled = true;
                btn.textContent = '获取中...';
            }
            
            try {
                const result = await apiCall('fetch_from_web', { 
                    verify: verify ? 'true' : 'false', 
                    max_per_source: 15 
                });
                if (result.success) {
                    let msg = `成功添加 ${result.added} 个可用代理`;
                    if (result.sources) {
                        const successSources = result.sources.filter(s => s.success).length;
                        msg += `（${successSources}/${result.sources.length} 个源成功）`;
                    }
                    showToast(msg, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(result.message || '获取失败', 'error');
                }
            } catch (e) {
                showToast('获取失败: ' + e.message, 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            }
        }

        async function clearInactive() {
            if (!confirm('确定要清理所有失效的代理吗？')) return;
            
            const result = await apiCall('clear_inactive');
            if (result.success) {
                showToast(`已清理 ${result.cleared} 个失效代理`);
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(result.message || '清理失败', 'error');
            }
        }
        
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) overlay.classList.remove('show');
            });
        });
    </script>
</body>
</html>
