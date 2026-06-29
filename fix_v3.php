<?php
/**
 * 终极一键修复脚本 v3
 * 功能：
 * 1. 下载所有最新文件
 * 2. 删除所有旧格式文件（.txt, .json, .html）
 * 3. 设置正确权限
 * 4. 清除缓存
 * 5. 测试 API
 */
set_time_limit(300);
ob_start();

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<title>M3U8 终极修复工具</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#f5f7fa}h2{color:#667eea}.success{color:#67c23a}.error{color:#f56c6c}.warn{color:#e6a23c}.info{color:#409eff}.card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);margin-bottom:15px}</style>";
echo "</head><body>";

$rootDir = __DIR__;
$githubRepo = 'ssmhdssmhd/qcb';
$branch = 'main';

$filesToUpdate = [
    'mx.php',
    'index.php',
    'mxadmin.php',
    'router.php',
    'version.php',
    'sq.php',
    'auth_config.php',
    'src/M3U8AdSkipper.php',
    'src/M3U8Parser.php',
    'src/AdFilter.php',
    'src/AdRuleEngine.php',
    'src/OutputGenerator.php',
    'src/AuthValidator.php',
    'src/AuthConfig.php',
    'src/CryptoUtil.php',
    'src/UpdateManager.php',
    'gz/DomainRuleManager.php',
    'gz/EnhancedAdRuleEngine.php',
    'gz/rules_v.lfthirtytwo.com.php',
];

$oldFilesToDelete = [
    'sq.txt',
    'version.txt',
    'auth_config.json',
    'admin.html',
    'mxadmin.html',
    'admin_api.php',
];

echo "<div class='card'><h2>M3U8 终极修复工具 v3</h2>";
echo "<p class='info'>正在修复规则列表获取失败问题...</p>";
echo "</div>";

// 1. 删除旧文件
echo "<div class='card'><h3>1. 删除旧格式文件</h3>";
foreach ($oldFilesToDelete as $file) {
    $path = $rootDir . '/' . $file;
    if (file_exists($path)) {
        if (@unlink($path)) {
            echo "<p class='success'>✓ 删除: $file</p>";
        } else {
            echo "<p class='warn'>⚠ 无法删除: $file</p>";
        }
    }
}
echo "</div>";

// 2. 下载最新文件
echo "<div class='card'><h3>2. 下载最新文件</h3>";
$successCount = 0;
$failCount = 0;
foreach ($filesToUpdate as $file) {
    if (in_array(basename($file), ['sq.php', 'auth_config.php'])) {
        echo "<p class='warn'>⚠ 跳过授权文件: $file</p>";
        continue;
    }
    
    $url = "https://raw.githubusercontent.com/$githubRepo/$branch/$file";
    $target = $rootDir . '/' . $file;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code === 200 && $content) {
        $dir = dirname($target);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (file_put_contents($target, $content)) {
            echo "<p class='success'>✓ 更新: $file</p>";
            $successCount++;
        } else {
            echo "<p class='error'>✗ 写入失败: $file</p>";
            $failCount++;
        }
    } else {
        echo "<p class='error'>✗ 下载失败: $file (HTTP $code)</p>";
        $failCount++;
    }
}
echo "<p class='info'>成功: $successCount | 失败: $failCount</p>";
echo "</div>";

// 3. 设置权限
echo "<div class='card'><h3>3. 设置文件权限</h3>";
$dirs = ['src', 'gz', 'backups'];
foreach ($dirs as $dir) {
    $path = $rootDir . '/' . $dir;
    if (is_dir($path)) {
        @chmod($path, 0755);
        echo "<p class='success'>✓ 设置权限: $dir</p>";
    }
}
@chmod($rootDir . '/mx.php', 0644);
@chmod($rootDir . '/index.php', 0644);
echo "<p class='success'>✓ 设置文件权限</p>";
echo "</div>";

// 4. 清除缓存
echo "<div class='card'><h3>4. 清除缓存</h3>";
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p class='success'>✓ OPcache 已清除</p>";
}
clearstatcache(true);
echo "<p class='success'>✓ 文件状态缓存已清除</p>";
echo "</div>";

// 5. 测试 API
echo "<div class='card'><h3>5. 测试 API</h3>";
$_GET['action'] = 'rules/list';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/mx.php?action=rules/list';
$_SERVER['SCRIPT_NAME'] = '/mx.php';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['HTTPS'] = 'off';

ob_start();
try {
    require $rootDir . '/mx.php';
} catch (Throwable $e) {
    echo "<p class='error'>✗ 异常: " . $e->getMessage() . "</p>";
}
$output = ob_get_clean();

$json = json_decode($output, true);
if ($json !== null && $json['success']) {
    echo "<p class='success'>✓ API 测试成功！规则数量: " . count($json['rules']) . "</p>";
} else {
    echo "<p class='error'>✗ API 测试失败</p>";
    echo "<p>输出: " . htmlspecialchars(substr($output, 0, 500)) . "</p>";
}
echo "</div>";

echo "<div class='card'>";
echo "<h3>修复完成！</h3>";
echo "<p><a href='mxadmin.php' class='btn'>前往后台管理</a></p>";
echo "<p><strong>注意：请删除此文件 fix_v3.php</strong></p>";
echo "</div>";

echo "<style>.btn{display:inline-block;padding:10px 20px;background:#667eea;color:#fff;text-decoration:none;border-radius:4px}</style>";
echo "</body></html>";
