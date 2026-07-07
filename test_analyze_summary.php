<?php
ob_start();
$_SERVER["REQUEST_METHOD"] = "GET";
$_GET["action"] = "analyze";
$_GET["url"] = "https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8";
$_GET["skip_cache"] = "1";
include "mx.php";
$output = ob_get_clean();

$json = json_decode($output, true);

if (!$json) {
    echo "JSON解析失败\n";
    echo "原始输出: " . substr($output, 0, 2000) . "\n";
    exit;
}

echo "=== 接口调用结果 ===\n";
echo "success: " . ($json["success"] ? "true" : "false") . "\n";
echo "message: " . ($json["message"] ?? "无") . "\n";
echo "fastMode: " . ($json["fastMode"] ? "true" : "false") . "\n";
echo "hasDomainRules: " . ($json["hasDomainRules"] ? "true" : "false") . "\n";
echo "cached: " . ($json["cached"] ? "true" : "false") . "\n";
echo "\n";

echo "=== stats ===" . "\n";
echo json_encode($json["stats"] ?? [], JSON_PRETTY_PRINT) . "\n";
echo "\n";

echo "=== 序列跳跃数量 ===" . "\n";
echo count($json["sequenceJumps"] ?? []) . "\n";
echo "\n";

echo "=== 广告簇数量 ===" . "\n";
echo count($json["adClusters"] ?? []) . "\n";
echo "\n";

echo "=== 时长分布 ===" . "\n";
$dist = $json["durationDistribution"] ?? [];
echo "min: " . ($dist["min"] ?? 0) . "\n";
echo "max: " . ($dist["max"] ?? 0) . "\n";
echo "avg: " . ($dist["avg"] ?? 0) . "\n";
echo "\n";

echo "=== 问题分析 ===" . "\n";
$stats = $json["stats"] ?? [];
$adSegments = $stats["adSegments"] ?? 0;
$totalSegments = $stats["totalSegments"] ?? 0;

if ($adSegments == 0) {
    echo "警告: 未检测到任何广告片段!\n";
    echo "提示: 当前域名使用默认规则检测，可能需要调整阈值\n";
} else {
    echo "成功检测到 {$adSegments} 个广告片段（共 {$totalSegments} 个）\n";
}
?>