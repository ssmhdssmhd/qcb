<?php
/**
 * 调试脚本 - 测试备用解析接口
 */

$videoUrl = 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';

$backupApis = [
    'https://jx.xmflv.com/?url=',
    'https://jx.bozrc.com:4433/player/?url=',
];

foreach ($backupApis as $idx => $api) {
    $testUrl = $api . urlencode($videoUrl);
    echo "\n=== 测试接口 " . ($idx + 1) . ": " . $api . " ===\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $testUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_REFERER        => $testUrl,
        CURLOPT_ENCODING       => '',
    ]);
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    
    echo "HTTP状态码: " . $httpCode . "\n";
    echo "最终URL: " . $effectiveUrl . "\n";
    echo "HTML长度: " . strlen($html) . " 字节\n";
    
    if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\]+?\.(?:m3u8|mp4)(?:[^\s\'"<>\\\)\\\\]*)?/i', $html, $allMatches)) {
        echo "找到 " . count($allMatches[0]) . " 个视频链接候选:\n";
        foreach ($allMatches[0] as $m) {
            echo "  - " . $m . "\n";
        }
    } else {
        echo "未找到直接视频链接\n";
        echo "HTML前1000字符:\n";
        echo substr($html, 0, 1000) . "\n";
    }
}
