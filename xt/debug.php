<?php
/**
 * 调试脚本 - 抓取并输出完整HTML
 */

$videoUrl = 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';
$parseApiUrl = 'https://jx.xmflv.com/?url=' . urlencode($videoUrl);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $parseApiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_REFERER        => $parseApiUrl,
    CURLOPT_ENCODING       => '',
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP状态码: " . $httpCode . "\n";
echo "HTML长度: " . strlen($html) . " 字节\n\n";
echo "=== 完整HTML ===\n";
echo $html . "\n";
