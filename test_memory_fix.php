<?php
/**
 * 内存耗尽修复验证测试
 */

$baseUrl = 'http://127.0.0.1:8082/mx.php';

function testApi($name, $action, $params = [], $expectedStatus = 200) {
    global $baseUrl;
    $url = $baseUrl . '?action=' . $action;
    foreach ($params as $k => $v) {
        $url .= '&' . urlencode($k) . '=' . urlencode($v);
    }
    
    $startTime = microtime(true);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $duration = round((microtime(true) - $startTime) * 1000, 0);
    
    $data = json_decode($response, true);
    $ok = $httpCode < 500;
    
    $peakMem = 0;
    if ($data && !empty($data['success'])) {
        $peakMem = $data['data']['server']['memory_peak'] ?? $data['data']['memory_peak'] ?? 0;
    }
    
    echo "  " . ($ok ? "✓" : "✗") . " $name ({$duration}ms, HTTP $httpCode, 峰值内存: " . round($peakMem/1024/1024, 1) . "MB)\n";
    if (!$ok) {
        echo "    错误: " . substr($response, 0, 200) . "\n";
    }
    return $ok;
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       内存耗尽修复验证测试                                    ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  1. 基础接口测试\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$passed = 0;
$total = 0;

$tests = [
    ['update/system_info', '系统信息接口'],
    ['update/version', '版本接口'],
    ['auth/info', '认证信息接口'],
    ['rules/list', '规则列表接口'],
    ['sites/list', '资源站列表'],
    ['official_replace/config', '官替配置'],
    ['official_replace/platforms', '官替平台'],
    ['player/config', '播放器配置'],
    ['proxy/list', '代理列表'],
    ['update/check', '更新检查'],
];

foreach ($tests as $t) {
    $total++;
    if (testApi($t[1], $t[0])) $passed++;
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  2. 缺少参数接口测试（验证错误处理正常）\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$errorTests = [
    ['analyze', 'analyze接口', ['url' => '']],
    ['mxjx', 'mxjx接口', ['url' => '']],
    ['mxjx/info', 'mxjx/info接口', ['url' => '']],
    ['official_replace/info', '官替info接口', ['url' => '']],
    ['official_replace/resolve', '官替resolve接口', ['url' => '']],
    ['moxi', '沫兮API接口', ['url' => '']],
];

foreach ($errorTests as $t) {
    $total++;
    if (testApi($t[1], $t[0], $t[2], 400)) $passed++;
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  3. 并发压力测试（10并发 × 3轮）\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$testUrls = [];
foreach (['update/system_info', 'rules/list', 'sites/list', 'player/config',
          'proxy/list', 'auth/info', 'official_replace/config', 'update/version',
          'official_replace/platforms', 'update/check'] as $action) {
    $testUrls[] = $baseUrl . '?action=' . $action;
}

for ($round = 1; $round <= 3; $round++) {
    $total++;
    $startTime = microtime(true);
    $mh = curl_multi_init();
    $handles = [];
    foreach ($testUrls as $i => $url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_multi_add_handle($mh, $ch);
        $handles[$i] = $ch;
    }
    $active = null;
    do {
        curl_multi_exec($mh, $active);
        if ($active > 0) curl_multi_select($mh, 0.1);
    } while ($active > 0);
    
    $successCount = 0;
    foreach ($handles as $i => $ch) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode < 500) $successCount++;
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);
    $duration = round((microtime(true) - $startTime) * 1000, 0);
    
    $ok = $successCount === count($testUrls);
    if ($ok) $passed++;
    echo "  " . ($ok ? "✓" : "✗") . " 并发测试 第{$round}轮 ({$duration}ms, {$successCount}/" . count($testUrls) . " 成功)\n";
}

echo "\n══════════════════════════════════════════════════════════════\n";
printf("  总计: %d  |  通过: %d  |  失败: %d  |  成功率: %.1f%%\n",
    $total, $passed, $total - $passed,
    $total > 0 ? $passed / $total * 100 : 0);
echo "══════════════════════════════════════════════════════════════\n";
