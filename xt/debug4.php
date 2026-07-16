<?php
/**
 * 调试脚本 - 深度分析 yparse 接口返回内容
 */

$videoUrl = 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';
$apiUrl = 'https://jx.yparse.com/index.php?url=' . urlencode($videoUrl);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    CURLOPT_REFERER        => $apiUrl,
    CURLOPT_ENCODING       => '',
]);

$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP状态码: " . $httpCode . "\n";
echo "HTML长度: " . strlen($html) . " 字节\n\n";

echo "=== 查找所有URL ===\n";
if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\]+/i', $html, $allUrls)) {
    echo "共找到 " . count($allUrls[0]) . " 个URL:\n";
    foreach ($allUrls[0] as $idx => $url) {
        echo "  [" . ($idx + 1) . "] " . substr($url, 0, 100) . (strlen($url) > 100 ? "..." : "") . "\n";
    }
}

echo "\n=== 查找所有 script 标签内容 ===\n";
if (preg_match_all('/<script[^>]*>([\s\S]*?)<\/script>/i', $html, $scripts)) {
    echo "找到 " . count($scripts[1]) . " 个script块\n";
    foreach ($scripts[1] as $idx => $script) {
        $script = trim($script);
        if (empty($script)) continue;
        echo "\n--- Script " . ($idx + 1) . " (长度: " . strlen($script) . ") ---\n";
        echo substr($script, 0, 500) . (strlen($script) > 500 ? "..." : "") . "\n";
    }
}

echo "\n=== 查找 JSON 数据 ===\n";
if (preg_match('/\{[^{}]*"url"[^{}]*\}/i', $html, $jsonMatch)) {
    echo "找到疑似JSON: " . $jsonMatch[0] . "\n";
}

echo "\n=== 完整HTML前3000字符 ===\n";
echo substr($html, 0, 3000) . "\n";
