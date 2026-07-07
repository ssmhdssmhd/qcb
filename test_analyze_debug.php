<?php
$_SERVER["REQUEST_METHOD"] = "GET";
$_GET["action"] = "analyze";
$_GET["url"] = "https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8";
$_GET["skip_cache"] = "1";

ob_start();
include "mx.php";
$output = ob_get_clean();

$json = json_decode($output, true);
if ($json !== null) {
    echo "SUCCESS: " . ($json["success"] ? "true" : "false") . "\n";
    echo "MESSAGE: " . ($json["message"] ?? "无") . "\n";
    echo "FAST_MODE: " . ($json["fastMode"] ? "true" : "false") . "\n";
    echo "HAS_RULES: " . ($json["hasDomainRules"] ? "true" : "false") . "\n";
    echo "CACHED: " . ($json["cached"] ? "true" : "false") . "\n";
    echo "STATS: " . json_encode($json["stats"] ?? [], JSON_UNESCAPED_UNICODE) . "\n";
    if (isset($json["error_detail"])) {
        echo "ERROR: " . json_encode($json["error_detail"]) . "\n";
    }
    echo "SEGMENTS_COUNT: " . count($json["allSegments"] ?? []) . "\n";
} else {
    echo "JSON PARSE FAILED\n";
    echo "FIRST_2000_CHARS: " . substr($output, 0, 2000) . "\n";
}
