<?php
/**
 * 一键全量修复脚本
 * 上传到网站根目录，浏览器访问 fix_update.php 运行一次即可
 * 自动从 GitHub 拉取所有最新文件，覆盖旧版本
 * 运行后请删除此文件
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

$githubRepo = 'ssmhdssmhd/qcb';
$branch = 'main';
$rootDir = __DIR__;

$filesToUpdate = [
    'index.php',
    'mx.php',
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

$excludeFromOverwrite = [
    'sq.php',
    'auth_config.php',
];

$oldFilesToDelete = [
    'sq.txt',
    'version.txt',
    'auth_config.json',
    'admin.html',
    'mxadmin.html',
    'admin_api.php',
];

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<title>M3U8 去广告工具 - 一键修复</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#f5f7fa}h2{color:#667eea}.success{color:#67c23a}.error{color:#f56c6c}.info{color:#409eff}.warn{color:#e6a23c}.card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);margin-bottom:15px}</style>";
echo "</head><body><div class='card'>";
echo "<h2>M3U8 去广告工具 - 一键全量修复</h2>";
echo "<p class='info'>正在从 GitHub 拉取最新版本（共 " . count($filesToUpdate) . " 个文件）...</p>";
echo "</div><div class='card'>";

$successCount = 0;
$failCount = 0;
$failFiles = [];

foreach ($filesToUpdate as $file) {
    if (in_array(basename($file), $excludeFromOverwrite)) {
        echo "<p class='warn'>跳过授权文件: $file</p>";
        continue;
    }

    $rawUrl = "https://raw.githubusercontent.com/$githubRepo/$branch/" . $file;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $rawUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Fix-Tool');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error || $httpCode !== 200 || !$content) {
        $failCount++;
        $failFiles[] = $file . ' (' . ($error ?: "HTTP $httpCode") . ')';
        echo "<p class='error'>✗ $file <span style='color:#909399'>- " . ($error ?: "HTTP $httpCode") . "</span></p>";
        continue;
    }

    $targetFile = $rootDir . '/' . $file;
    $targetDir = dirname($targetFile);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $result = file_put_contents($targetFile, $content);
    if ($result === false) {
        $failCount++;
        $failFiles[] = $file . ' (写入失败)';
        echo "<p class='error'>✗ $file - 写入失败</p>";
        continue;
    }

    $successCount++;
    $size = strlen($content);
    echo "<p class='success'>✓ $file <span style='color:#909399'>- {$size} 字节</span></p>";
}

echo "</div><div class='card'>";
echo "<h3>清理旧文件</h3>";
foreach ($oldFilesToDelete as $file) {
    $path = $rootDir . '/' . $file;
    if (file_exists($path)) {
        if (@unlink($path)) {
            echo "<p class='success'>✓ 删除旧文件: $file</p>";
        } else {
            echo "<p class='warn'>⚠ 删除失败: $file (权限不足)</p>";
        }
    }
}
echo "</div><div class='card'>";
echo "<h3>修复结果</h3>";
echo "<p class='success'>成功: $successCount 个文件</p>";
if ($failCount > 0) {
    echo "<p class='error'>失败: $failCount 个文件</p>";
    foreach ($failFiles as $f) {
        echo "<p class='error' style='font-size:12px;margin:2px 0'>- $f</p>";
    }
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p class='success'>✓ OPcache 已清除</p>";
}
if (function_exists('apc_clear_cache')) {
    apc_clear_cache();
    echo "<p class='success'>✓ APC 缓存已清除</p>";
}
clearstatcache(true);

echo "<hr>";

$testFile = $rootDir . '/gz/DomainRuleManager.php';
if (file_exists($testFile)) {
    require_once $testFile;
    $dm = new DomainRuleManager();
    $rules = $dm->getAllRules();
    echo "<p class='" . (count($rules) > 0 ? 'success' : 'warn') . "'>规则加载测试: " . count($rules) . " 个规则</p>";
}

$testUpdate = $rootDir . '/src/UpdateManager.php';
if (file_exists($testUpdate)) {
    require_once $testUpdate;
    $um = new UpdateManager();
    $integrity = $um->checkIntegrity();
    echo "<p class='" . ($integrity['success'] ? 'success' : 'error') . "'>完整性检查: " . ($integrity['success'] ? '通过' : '失败') . "</p>";
    if (!$integrity['success']) {
        foreach ($integrity['issues'] as $issue) {
            echo "<p class='error' style='font-size:12px;margin:2px 0'>- $issue</p>";
        }
    }
}

echo "<hr>";
echo "<p><strong>修复完成！请删除此文件 fix_update.php，然后刷新后台页面。</strong></p>";
echo "<p><a href='mxadmin.php' style='display:inline-block;padding:8px 20px;background:#667eea;color:#fff;text-decoration:none;border-radius:4px'>前往后台管理</a></p>";
echo "</div></body></html>";
