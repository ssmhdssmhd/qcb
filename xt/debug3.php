<?php
/**
 * 调试脚本 - 测试更多解析接口
 */

$videoUrl = 'https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html';

$testApis = [
    'https://jx.xmflv.com/?url=',
    'https://jx.bozrc.com:4433/player/?url=',
    'https://jx.m3u8.tv/jiexi/?url=',
    'https://player.yemu.xyz/?url=',
    'https://jx.yparse.com/index.php?url=',
    'https://jx.aidouer.net/?url=',
    'https://www.8090g.cn/?url=',
    'https://jx.denguen.com/?url=',
    'https://v.xn--7kq017abvrc7o.com/?url=',
    'https://jx.quankan.app/?url=',
    'https://jx.mtydata.com/?url=',
];

$successApis = [];

foreach ($testApis as $idx => $api) {
    $testUrl = $api . urlencode($videoUrl);
    echo "\n[" . ($idx + 1) . "/" . count($testApis) . "] 测试: " . substr($api, 0, 50) . "\n";
    
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
    
    $html = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error || $httpCode !== 200) {
        echo "  ❌ 失败: " . ($error ?: "HTTP " . $httpCode) . "\n";
        continue;
    }
    
    $videoLinks = [];
    if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\,;]+?\.(?:m3u8|mp4)(?:\?[^\s\'"<>\\\)\\\\,]*)?/i', $html, $allMatches)) {
        foreach ($allMatches[0] as $m) {
            if (!preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|ttf)(\?|$)/i', $m)) {
                $videoLinks[] = $m;
            }
        }
    }
    
    if (!empty($videoLinks)) {
        echo "  ✅ 成功! 找到 " . count($videoLinks) . " 个视频链接\n";
        foreach ($videoLinks as $vl) {
            echo "     - " . substr($vl, 0, 80) . (strlen($vl) > 80 ? "..." : "") . "\n";
        }
        $successApis[] = ['api' => $api, 'links' => $videoLinks];
    } else {
        echo "  ⚠️  页面加载成功(200)，但未找到直接视频链接 (HTML长度: " . strlen($html) . ")\n";
    }
}

echo "\n\n=== 总结 ===\n";
echo "共测试 " . count($testApis) . " 个接口，成功 " . count($successApis) . " 个\n";
foreach ($successApis as $s) {
    echo "  - " . $s['api'] . "\n";
}
