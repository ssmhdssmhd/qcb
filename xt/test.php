<?php
/**
 * 测试脚本 - 直接测试视频解析功能
 */

header('Content-Type: application/json; charset=utf-8');

$videoUrl = $_GET['url'] ?? 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';

echo "=== 测试视频解析 ===\n";
echo "目标链接: " . $videoUrl . "\n\n";

$parseApiUrl = 'https://jx.xmflv.com/?url=' . urlencode($videoUrl);
echo "解析接口: " . $parseApiUrl . "\n\n";

echo "正在请求解析接口...\n";

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
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "请求错误: " . $error . "\n";
    exit;
}

echo "HTTP状态码: " . $httpCode . "\n";
echo "最终URL: " . $effectiveUrl . "\n";
echo "HTML长度: " . strlen($html) . " 字节\n\n";

echo "=== 开始提取视频链接 ===\n";

$videoLink = null;

$patterns = [
    '/<video[^>]+src=[\'"](https?:\/\/[^\'"]+\.m3u8[^\'"]*)[\'"][^>]*>/i',
    '/<video[^>]+src=[\'"](https?:\/\/[^\'"]+\.mp4[^\'"]*)[\'"][^>]*>/i',
    '/<source[^>]+src=[\'"](https?:\/\/[^\'"]+\.m3u8[^\'"]*)[\'"][^>]*>/i',
    '/<source[^>]+src=[\'"](https?:\/\/[^\'"]+\.mp4[^\'"]*)[\'"][^>]*>/i',
];
foreach ($patterns as $pattern) {
    if (preg_match($pattern, $html, $matches)) {
        $videoLink = html_entity_decode($matches[1]);
        echo "模式1 (video/source标签) 匹配成功: " . $videoLink . "\n";
        break;
    }
}

if (!$videoLink) {
    $jsPatterns = [
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.m3u8[^\'"`\s]*)[\'"`]/i',
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.mp4[^\'"`\s]*)[\'"`]/i',
    ];
    foreach ($jsPatterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            $videoLink = html_entity_decode($matches[1]);
            echo "模式2 (JS变量) 匹配成功: " . $videoLink . "\n";
            break;
        }
    }
}

if (!$videoLink) {
    if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\]+?\.(?:m3u8|mp4)(?:[^\s\'"<>\\\)\\\\]*)?/i', $html, $allMatches)) {
        echo "模式3 (通用匹配) 找到 " . count($allMatches[0]) . " 个候选链接:\n";
        foreach ($allMatches[0] as $idx => $match) {
            echo "  [" . ($idx + 1) . "] " . $match . "\n";
        }
        foreach ($allMatches[0] as $match) {
            if (preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|ttf)/i', $match)) {
                continue;
            }
            if (preg_match('/(video|media|play|stream|cdn|m3u8|mp4|vod)/i', $match)) {
                $videoLink = rtrim($match, '\\');
                echo "  -> 优先匹配CDN关键词: " . $videoLink . "\n";
                break;
            }
        }
        if (!$videoLink && !empty($allMatches[0][0])) {
            $videoLink = rtrim($allMatches[0][0], '\\');
            echo "  -> 使用第一个匹配: " . $videoLink . "\n";
        }
    }
}

if (!$videoLink) {
    if (preg_match('/["\'](https?:\/\/[^"\']+\.m3u8[^"\']*)["\']/i', $html, $matches)) {
        $videoLink = html_entity_decode($matches[1]);
        echo "模式4 (JSON-m3u8) 匹配成功: " . $videoLink . "\n";
    }
}
if (!$videoLink) {
    if (preg_match('/["\'](https?:\/\/[^"\']+\.mp4[^"\']*)["\']/i', $html, $matches)) {
        $videoLink = html_entity_decode($matches[1]);
        echo "模式4 (JSON-mp4) 匹配成功: " . $videoLink . "\n";
    }
}

echo "\n=== 最终结果 ===\n";
if ($videoLink) {
    echo "✅ 解析成功！视频地址: " . $videoLink . "\n";
} else {
    echo "❌ 解析失败，未能提取到视频链接\n";
    echo "\nHTML前500字符预览:\n";
    echo substr($html, 0, 500) . "\n";
}
