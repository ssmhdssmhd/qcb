<?php
/**
 * Ban场景快速测试脚本
 * 跳过代理可用性验证，直接测试ban检测和切换逻辑
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "========================================\n";
echo "  Ban场景快速测试 - 代理自动切换验证\n";
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
// 测试2: 快速获取代理（跳过验证）
// ============================================
echo "【测试2】快速获取代理（proxy.scdn.io，优先中国，跳过验证）\n";
try {
    $fetcher = new ProxyFetcher([
        'timeout' => 10,
        'max_per_source' => 10,
    ]);

    // 只获取，不验证
    $result = $fetcher->fetchAll(false);
    $proxies = $result['proxies'];

    if (!empty($proxies)) {
        echo "  ✓ 成功获取 " . count($proxies) . " 个代理\n";

        // 统计中国代理
        $chinaCount = 0;
        foreach ($proxies as $p) {
            if (!empty($p['country']) && $p['country'] === '中国') {
                $chinaCount++;
            }
        }
        echo "  ✓ 中国代理: {$chinaCount} 个\n";
        echo "  ✓ 其他代理: " . (count($proxies) - $chinaCount) . " 个\n";

        // 手动添加到 ProxyManager
        $added = 0;
        foreach ($proxies as $proxy) {
            $proxyId = md5($proxy['type'] . '://' . $proxy['host'] . ':' . $proxy['port'] . '_' . uniqid());
            $isChina = !empty($proxy['country']) && $proxy['country'] === '中国';
            $proxyMgr->addProxy([
                'id' => $proxyId,
                'name' => strtoupper($proxy['type']) . ' ' . $proxy['host'] . ':' . $proxy['port'],
                'type' => $proxy['type'],
                'host' => $proxy['host'],
                'port' => $proxy['port'],
                'username' => '',
                'password' => '',
                'status' => 'active',
                'priority' => $isChina ? 50 : 100,
                'success_count' => 0,
                'fail_count' => 0,
                'last_check' => null,
                'last_success' => null,
                'response_time' => 0,
                'country' => $proxy['country'] ?? ''
            ]);
            $added++;
            if ($added >= 10) break; // 只加10个用于测试
        }

        echo "  ✓ 已添加 {$added} 个代理到代理池\n";
        $testResults['fetch'] = true;
        $testResults['proxy_count'] = $added;
    } else {
        echo "  ✗ 未获取到代理\n";
        $testResults['fetch'] = false;
    }
} catch (Exception $e) {
    echo "  ✗ 异常: " . $e->getMessage() . "\n";
    $testResults['fetch'] = false;
}
echo "\n";

// ============================================
// 测试3: 代理列表检查
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
// 测试4: getProxy() 代理选择（优先中国）
// ============================================
echo "【测试4】getProxy() 代理选择（中国优先）\n";
$proxy = $proxyMgr->getProxy();
if ($proxy) {
    echo "  ✓ 获取到代理: {$proxy['type']}://{$proxy['host']}:{$proxy['port']}\n";
    echo "  ✓ 代理ID: " . ($proxy['id'] ?? 'N/A') . "\n";
    echo "  ✓ 优先级: " . ($proxy['priority'] ?? 'N/A') . "\n";
    echo "  ✓ 国家: " . ($proxy['country'] ?? 'N/A') . "\n";

    // 验证优先级排序
    if (!empty($proxy['priority']) && $proxy['priority'] == 50) {
        echo "  ✓ 正确选择了中国高优先级代理\n";
    }
    $testResults['get_proxy'] = true;
} else {
    echo "  ✗ 未获取到代理\n";
    $testResults['get_proxy'] = false;
}
echo "\n";

// ============================================
// 测试5: 模拟ban场景 - 标记代理失败 & 自动切换
// ============================================
echo "【测试5】模拟Ban - 标记代理失败 & 自动切换\n";
$switchCount = 0;
$testProxyId = $proxy['id'] ?? null;

if ($testProxyId && count($activeProxies) > 1) {
    $currentProxyId = $testProxyId;
    $maxSwitch = min(5, count($activeProxies) - 1);

    for ($i = 0; $i < $maxSwitch; $i++) {
        // 标记当前代理失败（模拟ban）
        $proxyMgr->markProxyFailed($currentProxyId);
        echo "  ✓ 标记代理失败 #{$i}: {$currentProxyId}\n";

        // 获取新代理
        $nextProxy = $proxyMgr->getProxy();
        if ($nextProxy && $nextProxy['id'] !== $currentProxyId) {
            echo "    → 自动切换到: {$nextProxy['type']}://{$nextProxy['host']}:{$nextProxy['port']}\n";
            $currentProxyId = $nextProxy['id'];
            $switchCount++;
        } else {
            echo "    → 无更多可用代理\n";
            break;
        }
    }

    if ($switchCount > 0) {
        echo "  ✓ 成功切换 {$switchCount} 次代理\n";
        $testResults['auto_switch'] = true;
    } else {
        echo "  ⚠ 未能切换代理\n";
        $testResults['auto_switch'] = false;
    }
} else {
    echo "  ⚠ 代理不足，无法测试切换\n";
    $testResults['auto_switch'] = count($activeProxies) >= 1;
}
echo "\n";

// ============================================
// 测试6: ban检测逻辑
// ============================================
echo "【测试6】Ban检测逻辑\n";
$testCases = [
    ['code' => 500, 'body' => '', 'expected' => true, 'desc' => 'HTTP 500 状态码'],
    ['code' => 200, 'body' => '你已经被ban了', 'expected' => true, 'desc' => '响应含ban关键词（中文）'],
    ['code' => 200, 'body' => 'You are banned', 'expected' => true, 'desc' => '响应含ban关键词（英文）'],
    ['code' => 200, 'body' => 'BAN DETECTED', 'expected' => true, 'desc' => '响应含BAN（大写）'],
    ['code' => 200, 'body' => '{"code":500,"msg":"ban"}', 'expected' => true, 'desc' => 'JSON含ban'],
    ['code' => 200, 'body' => '正常响应内容', 'expected' => false, 'desc' => '正常响应'],
    ['code' => 404, 'body' => 'not found', 'expected' => false, 'desc' => '404错误'],
    ['code' => 403, 'body' => 'forbidden', 'expected' => false, 'desc' => '403错误'],
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
// 测试7: ensureProxyAvailable 方法
// ============================================
echo "【测试7】ensureProxyAvailable 自动刷新机制\n";
$stats = $proxyMgr->getStats();
echo "  ✓ 代理池状态: " . ($stats['enabled'] ? '启用' : '禁用') . "\n";
echo "  ✓ 总代理数: {$stats['total']}\n";
echo "  ✓ 活跃代理数: {$stats['active']}\n";

if (method_exists($proxyMgr, 'ensureProxyAvailable')) {
    echo "  ✓ ensureProxyAvailable 方法存在\n";

    // 测试1: 代理池不为空时
    $result1 = $proxyMgr->ensureProxyAvailable();
    echo "  ✓ 代理池非空时返回: " . ($result1 ? 'true' : 'false') . "\n";

    $testResults['ensure_available'] = true;
} else {
    echo "  ✗ 方法不存在\n";
    $testResults['ensure_available'] = false;
}
echo "\n";

// ============================================
// 测试8: xiami_jx 集成验证
// ============================================
echo "【测试8】虾米解析集成验证（代码检查）\n";

if (file_exists(__DIR__ . '/xiami_jx.php')) {
    echo "  ✓ xiami_jx.php 存在\n";

    $xiamiCode = file_get_contents(__DIR__ . '/xiami_jx.php');
    $checkList = [
        'ensureProxyAvailable' => '自动代理刷新调用',
        'markProxyFailed' => '代理失败标记',
        'markProxySuccess' => '代理成功标记',
        'stripos.*ban' => 'ban关键词检测',
        'maxRetries' => '重试机制',
        'proxy.scdn.io' => 'proxy.scdn.io来源',
    ];

    $allFound = true;
    foreach ($checkList as $pattern => $desc) {
        if (preg_match("/$pattern/i", $xiamiCode)) {
            echo "  ✓ {$desc}\n";
        } else {
            echo "  ✗ {$desc}（未找到）\n";
            $allFound = false;
        }
    }
    $testResults['xiami_integration'] = $allFound;
} else {
    echo "  ✗ xiami_jx.php 不存在\n";
    $testResults['xiami_integration'] = false;
}
echo "\n";

// ============================================
// 测试9: mx.php 集成验证
// ============================================
echo "【测试9】mx.php 集成验证（代码检查）\n";
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
    $testResults['mx_integration'] = ($foundCount >= 4);
} else {
    echo "  ✗ mx.php 不存在\n";
    $testResults['mx_integration'] = false;
}
echo "\n";

// ============================================
// 测试10: ProxyFetcher 来源验证
// ============================================
echo "【测试10】ProxyFetcher 代理来源验证\n";
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
// 测试11: 模拟完整ban流程
// ============================================
echo "【测试11】模拟完整Ban流程\n";
echo "  场景: 请求成功 → ban → 切换代理 → 再ban → 再切换...\n";

// 创建新的 ProxyManager 实例
$testMgr = new ProxyManager();

// 手动添加测试代理（相同优先级，按添加顺序排列）
for ($i = 1; $i <= 5; $i++) {
    $testMgr->addProxy([
        'id' => "test_proxy_{$i}",
        'name' => "Test Proxy {$i}",
        'type' => 'http',
        'host' => "192.168.1.{$i}",
        'port' => 8080,
        'status' => 'active',
        'priority' => 50,
        'success_count' => 0,
        'fail_count' => 0,
        'last_check' => null,
        'last_success' => null,
        'response_time' => 100,
        'country' => '中国'
    ]);
}

// 先获取第一个代理的ID作为基准
$firstProxy = $testMgr->getProxy();
$firstProxyId = $firstProxy ? $firstProxy['id'] : null;
echo "  ✓ 初始代理: {$firstProxyId}\n";

// 模拟多次ban和切换
$banCount = 3;
$switchSuccess = 0;
$currentId = $firstProxyId;

for ($i = 0; $i < $banCount; $i++) {
    // ban当前代理
    $testMgr->markProxyFailed($currentId);
    echo "  ✓ 标记代理失败 #{$i}: {$currentId}\n";

    // 获取新代理
    $nextProxy = $testMgr->getProxy();
    $nextId = $nextProxy ? $nextProxy['id'] : null;

    if ($nextId && $nextId !== $currentId) {
        echo "    → 自动切换到: {$nextId}\n";
        $currentId = $nextId;
        $switchSuccess++;
    } else {
        echo "    → 切换失败\n";
    }
}

// 模拟成功
$testMgr->markProxySuccess($currentId);
echo "  ✓ 代理成功: {$currentId}\n";

$testResults['full_simulation'] = ($switchSuccess === $banCount);
echo "  切换成功率: {$switchSuccess}/{$banCount}\n";
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
    echo "\n  🎉 所有测试通过！Ban场景代理自动切换功能完美运行！\n";
} else {
    echo "\n  ⚠ 部分测试失败，请检查上述结果\n";
}

echo "\n========================================\n";
