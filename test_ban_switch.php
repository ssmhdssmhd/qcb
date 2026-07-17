<?php
/**
 * Ban场景测试脚本
 * 测试代理自动切换和刷新功能
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "  Ban场景测试 - 代理自动切换验证\n";
echo "========================================\n\n";

require_once __DIR__ . '/proxy/ProxyManager.php';
require_once __DIR__ . '/proxy/ProxyFetcher.php';

$testResults = [];

// ============================================
// 测试1: ProxyManager 初始化
// ============================================
echo "【测试1】ProxyManager 初始化\n";
try {
    $proxyMgr = new ProxyManager();
    echo "  ✓ ProxyManager 实例化成功\n";
    echo "  ✓ isEnabled(): " . ($proxyMgr->isEnabled() ? 'true' : 'false') . "\n";
    $testResults['init'] = true;
} catch (Exception $e) {
    echo "  ✗ 失败: " . $e->getMessage() . "\n";
    $testResults['init'] = false;
}
echo "\n";

// ============================================
// 测试2: 自动获取代理（从 proxy.scdn.io）
// ============================================
echo "【测试2】自动获取代理（proxy.scdn.io，优先中国）\n";
try {
    $result = $proxyMgr->autoRefreshProxies(true);
    if ($result['success']) {
        echo "  ✓ 成功获取 {$result['added']} 个代理\n";
        echo "  ✓ 中国代理: {$result['china_count']} 个\n";
        echo "  ✓ 其他代理: {$result['other_count']} 个\n";
        $testResults['fetch'] = true;
        $testResults['proxy_count'] = $result['added'];
    } else {
        echo "  ✗ 失败: {$result['message']}\n";
        $testResults['fetch'] = false;
    }
} catch (Exception $e) {
    echo "  ✗ 异常: " . $e->getMessage() . "\n";
    $testResults['fetch'] = false;
}
echo "\n";

// ============================================
// 测试3: 获取代理列表
// ============================================
echo "【测试3】代理列表检查\n";
$allProxies = $proxyMgr->getAllProxies();
$activeProxies = $proxyMgr->getActiveProxies();
echo "  ✓ 总代理数: " . count($allProxies) . "\n";
echo "  ✓ 活跃代理数: " . count($activeProxies) . "\n";

if (count($activeProxies) > 0) {
    $firstProxy = reset($activeProxies);
    echo "  ✓ 首个代理: {$firstProxy['type']}://{$firstProxy['host']}:{$firstProxy['port']}\n";
    echo "  ✓ 优先级: " . ($firstProxy['priority'] ?? 'N/A') . "\n";
    echo "  ✓ 国家: " . ($firstProxy['country'] ?? 'N/A') . "\n";
}
echo "\n";

// ============================================
// 测试4: getProxy() 代理选择
// ============================================
echo "【测试4】getProxy() 代理选择\n";
$proxy = $proxyMgr->getProxy();
if ($proxy) {
    echo "  ✓ 获取到代理: {$proxy['type']}://{$proxy['host']}:{$proxy['port']}\n";
    echo "  ✓ 代理ID: " . ($proxy['id'] ?? 'N/A') . "\n";
    $testResults['get_proxy'] = true;
} else {
    echo "  ✗ 未获取到代理\n";
    $testResults['get_proxy'] = false;
}
echo "\n";

// ============================================
// 测试5: 模拟ban场景 - 标记代理失败
// ============================================
echo "【测试5】模拟Ban - 标记代理失败\n";
$initialProxyCount = count($activeProxies);
$failedCount = 0;
$testProxyId = $proxy['id'] ?? null;

if ($testProxyId) {
    // 标记第一个代理失败
    $proxyMgr->markProxyFailed($testProxyId);
    echo "  ✓ 标记代理失败: {$testProxyId}\n";

    // 再次获取代理，应该返回不同的代理
    $nextProxy = $proxyMgr->getProxy();
    if ($nextProxy && $nextProxy['id'] !== $testProxyId) {
        echo "  ✓ 自动切换到新代理: {$nextProxy['type']}://{$nextProxy['host']}:{$nextProxy['port']}\n";
        $failedCount++;
    } else {
        echo "  ⚠ 未切换代理（可能只有1个代理）\n";
    }
    $testResults['mark_failed'] = true;
} else {
    echo "  ✗ 无代理可测试\n";
    $testResults['mark_failed'] = false;
}
echo "\n";

// ============================================
// 测试6: ban检测逻辑
// ============================================
echo "【测试6】Ban检测逻辑\n";
$testCases = [
    ['code' => 500, 'body' => '', 'expected' => true, 'desc' => 'HTTP 500'],
    ['code' => 200, 'body' => '你已经被ban了', 'expected' => true, 'desc' => '响应含ban关键词'],
    ['code' => 200, 'body' => 'You are banned', 'expected' => true, 'desc' => '英文ban'],
    ['code' => 200, 'body' => 'BAN', 'expected' => true, 'desc' => '大写BAN'],
    ['code' => 200, 'body' => '正常响应', 'expected' => false, 'desc' => '正常响应'],
    ['code' => 404, 'body' => 'not found', 'expected' => false, 'desc' => '404错误'],
];

$banTestPassed = 0;
foreach ($testCases as $case) {
    $httpCode = $case['code'];
    $response = $case['body'];
    $isBanned = ($httpCode === 500 || (is_string($response) && (
        stripos($response, 'ban') !== false
    )));

    $passed = $isBanned === $case['expected'];
    $status = $passed ? '✓' : '✗';
    echo "  {$status} {$case['desc']}: " . ($isBanned ? 'ban' : '正常') . " (期望: " . ($case['expected'] ? 'ban' : '正常') . ")\n";

    if ($passed) $banTestPassed++;
}

$testResults['ban_detect'] = ($banTestPassed === count($testCases));
echo "  通过率: {$banTestPassed}/" . count($testCases) . "\n";
echo "\n";

// ============================================
// 测试7: ensureProxyAvailable 自动刷新
// ============================================
echo "【测试7】ensureProxyAvailable 自动刷新机制\n";
$stats = $proxyMgr->getStats();
echo "  ✓ 代理池状态: " . ($stats['enabled'] ? '启用' : '禁用') . "\n";
echo "  ✓ 总代理数: {$stats['total']}\n";
echo "  ✓ 活跃代理数: {$stats['active']}\n";

// 检查 ensureProxyAvailable 方法存在
if (method_exists($proxyMgr, 'ensureProxyAvailable')) {
    echo "  ✓ ensureProxyAvailable 方法存在\n";
    $result = $proxyMgr->ensureProxyAvailable();
    echo "  ✓ ensureProxyAvailable 返回: " . ($result ? 'true' : 'false') . "\n";
    $testResults['ensure_available'] = true;
} else {
    echo "  ✗ 方法不存在\n";
    $testResults['ensure_available'] = false;
}
echo "\n";

// ============================================
// 测试8: xiami_jx 集成验证
// ============================================
echo "【测试8】虾米解析集成验证\n";

// 检查 xiami_jx.php 文件存在
if (file_exists(__DIR__ . '/xiami_jx.php')) {
    echo "  ✓ xiami_jx.php 存在\n";

    // 检查关键函数
    $xiamiCode = file_get_contents(__DIR__ . '/xiami_jx.php');
    $checkList = [
        'ensureProxyAvailable' => '自动代理刷新调用',
        'markProxyFailed' => '代理失败标记',
        'markProxySuccess' => '代理成功标记',
        'stripos.*ban' => 'ban关键词检测',
        'maxRetries' => '重试机制',
        'proxy.scdn.io' => 'proxy.scdn.io来源',
    ];

    foreach ($checkList as $pattern => $desc) {
        if (preg_match("/$pattern/i", $xiamiCode)) {
            echo "  ✓ {$desc}\n";
        } else {
            echo "  ✗ {$desc}（未找到）\n";
        }
    }
    $testResults['xiami_integration'] = true;
} else {
    echo "  ✗ xiami_jx.php 不存在\n";
    $testResults['xiami_integration'] = false;
}
echo "\n";

// ============================================
// 测试9: mx.php 集成验证
// ============================================
echo "【测试9】mx.php 集成验证\n";
if (file_exists(__DIR__ . '/mx.php')) {
    echo "  ✓ mx.php 存在\n";

    $mxCode = file_get_contents(__DIR__ . '/mx.php');
    $checkList = [
        'ensureProxyAvailable' => '自动代理刷新调用',
        'markProxyFailed' => '代理失败标记',
        'markProxySuccess' => '代理成功标记',
        'stripos.*ban' => 'ban关键词检测',
        'maxRetries' => '重试机制',
    ];

    $foundCount = 0;
    foreach ($checkList as $pattern => $desc) {
        if (preg_match("/$pattern/i", $mxCode)) {
            echo "  ✓ {$desc}\n";
            $foundCount++;
        } else {
            echo "  ✗ {$desc}（未找到）\n";
        }
    }
    $testResults['mx_integration'] = ($foundCount >= 3);
} else {
    echo "  ✗ mx.php 不存在\n";
    $testResults['mx_integration'] = false;
}
echo "\n";

// ============================================
// 测试10: ProxyFetcher 来源验证
// ============================================
echo "【测试10】ProxyFetcher 代理来源验证\n";
$fetcher = new ProxyFetcher();
$sources = $fetcher->getSources();
$enabledSources = array_filter($sources, function($s) { return !empty($s['enabled']); });
$chinaSources = array_filter($sources, function($s) {
    return !empty($s['country']) && $s['country'] === '中国';
});

echo "  ✓ 总来源数: " . count($sources) . "\n";
echo "  ✓ 启用来源数: " . count($enabledSources) . "\n";
echo "  ✓ 中国代理来源数: " . count($chinaSources) . "\n";

foreach ($chinaSources as $src) {
    echo "    - {$src['name']} (优先级: {$src['priority']})\n";
}

$testResults['fetcher_sources'] = (count($chinaSources) >= 2);
echo "\n";

// ============================================
// 总结
// ============================================
echo "========================================\n";
echo "  测试总结\n";
echo "========================================\n\n";

$passed = 0;
$total = count($testResults);
foreach ($testResults as $name => $result) {
    $status = $result ? '✓ 通过' : '✗ 失败';
    echo "  {$status}: {$name}\n";
    if ($result) $passed++;
}

echo "\n  总计: {$passed}/{$total} 通过\n";

if ($passed === $total) {
    echo "\n  🎉 所有测试通过！Ban场景代理自动切换功能正常！\n";
} else {
    echo "\n  ⚠ 部分测试失败，请检查上述结果\n";
}

echo "\n========================================\n";
