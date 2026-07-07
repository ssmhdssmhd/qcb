<?php
$_SERVER["REQUEST_METHOD"] = "GET";
$_GET["action"] = "analyze";
$_GET["url"] = "https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8";
$_GET["skip_cache"] = "1";

ob_start();
include "mx.php";
$output = ob_get_clean();

$json = json_decode($output, true);

echo "=== 接口测试结果 ===\n";
echo "success: " . ($json["success"] ? "true" : "false") . "\n";
echo "message: " . ($json["message"] ?? "无") . "\n";
echo "fastMode: " . ($json["fastMode"] ? "true" : "false") . "\n";
echo "hasDomainRules: " . ($json["hasDomainRules"] ? "true" : "false") . "\n";
echo "cached: " . ($json["cached"] ? "true" : "false") . "\n";
echo "\n=== stats ===" . "\n";
echo json_encode($json["stats"] ?? [], JSON_PRETTY_PRINT) . "\n";
echo "\n=== 错误信息 ===" . "\n";
echo "error_detail: " . json_encode($json["error_detail"] ?? []) . "\n";
echo "\n=== 前5个片段示例 ===" . "\n";
$segments = $json["allSegments"] ?? [];
$sample = array_slice($segments, 0, 5);
echo json_encode($sample, JSON_PRETTY_PRINT) . "\n";
