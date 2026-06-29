<?php
/**
 * 最简单的诊断页面
 * 直接在浏览器访问此文件，查看 API 是否正常工作
 */
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>API 诊断工具</title>
<style>
body{font-family:Arial,sans-serif;margin:20px;background:#f5f7fa}
.card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);margin-bottom:15px}
h2{color:#667eea}
.success{color:#67c23a}.error{color:#f56c6c}.info{color:#409eff}.warn{color:#e6a23c}
pre{background:#f5f7fa;padding:10px;border-radius:4px;overflow-x:auto;font-size:12px;max-height:300px}
.btn{display:inline-block;padding:10px 20px;background:#667eea;color:#fff;text-decoration:none;border-radius:4px;margin:5px;border:none;cursor:pointer}
.btn-danger{background:#f56c6c}.btn-success{background:#67c23a}
</style>
</head>
<body>
<div class="card">
<h2>API 诊断工具</h2>
<p class="info">测试规则列表 API 是否正常工作</p>
</div>

<div class="card">
<h3>1. 直接调用 API (PHP)</h3>
<?php
$_GET['action'] = 'rules/list';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/mx.php?action=rules/list';
$_SERVER['SCRIPT_NAME'] = '/mx.php';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'off';

ob_start();
try {
    require __DIR__ . '/mx.php';
} catch (Throwable $e) {
    echo "<p class='error'>异常: " . htmlspecialchars($e->getMessage()) . "</p>";
}
$output = ob_get_clean();

$json = json_decode($output, true);
if ($json !== null && $json['success']) {
    echo "<p class='success'>✓ API 调用成功！规则数量: " . count($json['rules']) . "</p>";
} else {
    echo "<p class='error'>✗ API 调用失败</p>";
    echo "<p>响应:</p><pre>" . htmlspecialchars(substr($output, 0, 500)) . "</pre>";
}
?>
</div>

<div class="card">
<h3>2. Fetch 调用 (JS)</h3>
<p>点击按钮测试 fetch 调用 API：</p>
<button class="btn" onclick="testFetch()">测试 fetch</button>
<button class="btn btn-success" onclick="testFetchNoCache()">测试 fetch (无缓存)</button>
<div id="fetchResult"></div>
</div>

<div class="card">
<h3>3. 修复操作</h3>
<button class="btn btn-danger" onclick="clearCache()">清除浏览器缓存</button>
<button class="btn" onclick="location.reload()">刷新页面</button>
</div>

<script>
async function testFetch() {
    const result = document.getElementById('fetchResult');
    result.innerHTML = '<p class="info">测试中...</p>';
    try {
        const res = await fetch('mx.php?action=rules/list');
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
            result.innerHTML = '<p class="success">✓ 成功！规则数量: ' + Object.keys(data.rules).length + '</p>';
        } catch (e) {
            result.innerHTML = '<p class="error">✗ JSON 解析失败</p><p>响应:</p><pre>' + text.substring(0, 500) + '</pre>';
        }
    } catch (e) {
        result.innerHTML = '<p class="error">✗ 请求失败: ' + e.message + '</p>';
    }
}

async function testFetchNoCache() {
    const result = document.getElementById('fetchResult');
    result.innerHTML = '<p class="info">测试中...</p>';
    try {
        const res = await fetch('mx.php?action=rules/list&_t=' + Date.now(), {
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-cache' }
        });
        const text = await res.text();
        let data;
        try {
            data = JSON.parse(text);
            result.innerHTML = '<p class="success">✓ 成功！规则数量: ' + Object.keys(data.rules).length + '</p>';
        } catch (e) {
            result.innerHTML = '<p class="error">✗ JSON 解析失败</p><p>响应:</p><pre>' + text.substring(0, 500) + '</pre>';
        }
    } catch (e) {
        result.innerHTML = '<p class="error">✗ 请求失败: ' + e.message + '</p>';
    }
}

function clearCache() {
    if ('caches' in window) {
        caches.keys().then(names => {
            names.forEach(name => caches.delete(name));
        });
    }
    location.reload(true);
}
</script>
</body>
</html>
