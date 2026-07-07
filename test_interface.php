<?php
ob_start();
$_SERVER["REQUEST_METHOD"] = "GET";
$_GET["action"] = "analyze";
$_GET["url"] = "https://www.w3schools.com/html/mov_bbb.mp4";
$_GET["skip_cache"] = "1";
include "mx.php";
$output = ob_get_clean();

$json = json_decode($output, true);

echo "=== 接口测试结果 ===\n";
echo "输出原始内容:\n";
echo $output . "\n";
echo "\n=== JSON解析结果 ===\n";
echo "success: " . ($json["success"] ? "true" : "false") . "\n";
if (!$json["success"]) {
    echo "message: " . ($json["message"] ?? "无") . "\n";
    echo "error_detail: " . json_encode($json["error_detail"] ?? [], JSON_PRETTY_PRINT) . "\n";
}
?>