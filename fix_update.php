<?php
/**
 * 一键修复更新器脚本
 * 上传到网站根目录，浏览器访问 fix_update.php 运行一次即可
 * 运行后请删除此文件
 */

$githubRepo = 'ssmhdssmhd/qcb';
$branch = 'main';
$rootDir = __DIR__;

echo "<h2>M3U8 去广告工具 - 一键修复更新器</h2>";
echo "<p>正在从 GitHub 拉取最新的 UpdateManager.php...</p>";

$rawUrl = "https://raw.githubusercontent.com/$githubRepo/$branch/src/UpdateManager.php";

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

if ($error) {
    echo "<p style='color:red'>下载失败: $error</p>";
    exit;
}

if ($httpCode !== 200 || !$content) {
    echo "<p style='color:red'>下载失败，HTTP 状态码: $httpCode</p>";
    exit;
}

if (strpos($content, 'class UpdateManager') === false) {
    echo "<p style='color:red'>下载的文件内容不正确</p>";
    exit;
}

$targetFile = $rootDir . '/src/UpdateManager.php';
if (!is_dir($rootDir . '/src')) {
    mkdir($rootDir . '/src', 0755, true);
}

$result = file_put_contents($targetFile, $content);
if ($result === false) {
    echo "<p style='color:red'>写入文件失败，请检查目录权限</p>";
    exit;
}

echo "<p style='color:green'>✓ UpdateManager.php 修复成功！</p>";
echo "<p>文件大小: " . strlen($content) . " 字节</p>";

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p style='color:green'>✓ OPcache 已清除</p>";
}

echo "<hr>";
echo "<p><strong>现在可以删除此文件，然后去后台点击更新即可正常工作。</strong></p>";
echo "<p><a href='mxadmin.php'>前往后台管理</a></p>";
