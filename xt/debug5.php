<?php
/**
 * 调试脚本 - 测试更多类型的解析接口
 */

$videoUrl = 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';

$testApis = [
    // JSON API 类型
    ['name' => 'json.php 类型1', 'url' => 'https://jx.xmflv.com/?url=', 'type' => 'html'],
    ['name' => 'api.qianqi.net', 'url' => 'https://api.qianqi.net/vip/?url=', 'type' => 'json'],
    ['name' => 'jx.618g.com', 'url' => 'https://jx.618g.com/?url=', 'type' => 'html'],
    ['name' => 'api.xfsub.com', 'url' => 'https://api.xfsub.com/index.php?url=', 'type' => 'json'],
    ['name' => 'jx.iiilab.com', 'url' => 'https://jx.iiilab.com/?url=', 'type' => 'html'],
    ['name' => 'yyets.vip', 'url' => 'https://www.yyets.vip/jx.php?url=', 'type' => 'html'],
    ['name' => 'jx.izty.xyz', 'url' => 'https://jx.izty.xyz/?url=', 'type' => 'html'],
    ['name' => 'api.panguijy.cn', 'url' => 'https://api.panguijy.cn/?url=', 'type' => 'json'],
    ['name' => 'jx.playerjy.com', 'url' => 'https://jx.playerjy.com/?url=', 'type' => 'html'],
];

$successResults = [];

foreach ($testApis as $idx => $api) {
    $testUrl = $api['url'] . urlencode($videoUrl);
    echo "\n[" . ($idx + 1) . "/" . count($testApis) . "] " . $api['name'] . "\n";
    echo "  URL: " . substr($api['url'], 0, 50) . "\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $testUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_REFERER        => $testUrl,
        CURLOPT_ENCODING       => '',
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        echo "  ❌ 失败: " . ($error ?: "HTTP " . $httpCode) . "\n";
        continue;
    }
    
    echo "  Content-Type: " . $contentType . "\n";
    echo "  响应长度: " . strlen($response) . " 字节\n";
    
    $videoLink = null;
    
    if (strpos($contentType, 'json') !== false) {
        echo "  JSON响应，尝试解析...\n";
        $data = json_decode($response, true);
        if ($data) {
            echo "  JSON解析成功\n";
            array_walk_recursive($data, function($value, $key) use (&$videoLink) {
                if (is_string($value) && preg_match('/\.(m3u8|mp4)(\?|$)/i', $value)) {
                    if (!$videoLink) $videoLink = $value;
                }
            });
        }
    }
    
    if (!$videoLink) {
        if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\,;]+?\.(?:m3u8|mp4)(?:\?[^\s\'"<>\\\)\\\\,]*)?/i', $response, $allMatches)) {
            foreach ($allMatches[0] as $m) {
                if (!preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|ttf)(\?|$)/i', $m)) {
                    $videoLink = $m;
                    break;
                }
            }
        }
    }
    
    if ($videoLink) {
        echo "  ✅ 成功! 视频链接: " . substr($videoLink, 0, 100) . (strlen($videoLink) > 100 ? "..." : "") . "\n";
        $successResults[] = ['name' => $api['name'], 'url' => $api['url'], 'video' => $videoLink];
    } else {
        echo "  ⚠️  未找到视频链接\n";
        if (strlen($response) < 500) {
            echo "  响应内容: " . $response . "\n";
        }
    }
}

echo "\n\n========== 总结 ==========\n";
echo "成功 " . count($successResults) . "/" . count($testApis) . " 个接口\n";
foreach ($successResults as $s) {
    echo "  ✅ " . $s['name'] . ": " . $s['url'] . "\n";
    echo "     视频: " . substr($s['video'], 0, 80) . "...\n";
}
