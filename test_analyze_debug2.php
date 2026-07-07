<?php
ob_start();
$_SERVER["REQUEST_METHOD"] = "GET";
$_GET["action"] = "analyze";
$_GET["url"] = "https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8";
$_GET["skip_cache"] = "1";
include "mx.php";
$output = ob_get_clean();

$json = json_decode($output, true);

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

echo "=== 广告检测规则 ===" . "\n";
$domainRules = $json["domainRules"] ?? null;
if ($domainRules) {
    echo "duration_rules: " . count($domainRules["duration_rules"] ?? []) . "\n";
    echo "discontinuity_rules: " . count($domainRules["discontinuity_rules"] ?? []) . "\n";
    echo "sequence_jump_rules: " . count($domainRules["sequence_jump_rules"] ?? []) . "\n";
    echo "filename_patterns: " . count($domainRules["filename_patterns"] ?? []) . "\n";
} else {
    echo "无域名规则\n";
}
echo "\n";

echo "=== 序列跳跃 ===" . "\n";
$jumps = $json["sequenceJumps"] ?? [];
echo "数量: " . count($jumps) . "\n";
if (!empty($jumps)) {
    echo "前5个:\n";
    foreach (array_slice($jumps, 0, 5) as $j) {
        echo "  索引:{$j['index']}, 跳跃:{$j['jump']}, 前序:{$j['prevSeq']}, 当前:{$j['currentSeq']}\n";
    }
}
echo "\n";

echo "=== 时长分布 ===" . "\n";
$dist = $json["durationDistribution"] ?? [];
echo "min: " . ($dist["min"] ?? 0) . "\n";
echo "max: " . ($dist["max"] ?? 0) . "\n";
echo "avg: " . ($dist["avg"] ?? 0) . "\n";

echo "\n=== 广告簇 ===" . "\n";
$clusters = $json["adClusters"] ?? [];
echo "数量: " . count($clusters) . "\n";

echo "\n=== 问题分析 ===" . "\n";
$stats = $json["stats"] ?? [];
$adSegments = $stats["adSegments"] ?? 0;
$totalSegments = $stats["totalSegments"] ?? 0;
$discontinuityCount = $stats["discontinuityCount"] ?? 0;

if ($adSegments == 0) {
    echo "警告: 未检测到任何广告片段!\n";
    if ($discontinuityCount > 0) {
        echo "提示: 存在 {$discontinuityCount} 个 DISCONTINUITY 标记，但未触发广告检测\n";
    }
    if (!($json["hasDomainRules"] ?? false)) {
        echo "提示: 当前域名无自定义规则，使用默认规则检测\n";
    }
}

echo "\n=== 片段时长统计 ===" . "\n";
$allSegments = $json["allSegments"] ?? [];
$shortSegments = 0;
$normalSegments = 0;
$longSegments = 0;
foreach ($allSegments as $seg) {
    $d = $seg["d"] ?? 0;
    if ($d < 2) $shortSegments++;
    elseif ($d > 10) $longSegments++;
    else $normalSegments++;
}
echo "短片段(<2s): {$shortSegments}\n";
echo "正常片段(2-10s): {$normalSegments}\n";
echo "长片段(>10s): {$longSegments}\n";
?>