<?php
require_once 'db/autoload.php';

echo "=== API 功能测试 ===\n";

$tests = [
    '规则列表' => function() {
        $manager = new DbDomainRuleManager();
        $rules = $manager->getAllRulesLite();
        return ['success' => true, 'count' => count($rules)];
    },
    '资源站列表' => function() {
        $manager = new DbResourceSiteManager();
        $sites = $manager->getAllSites(true);
        return ['success' => true, 'count' => count($sites)];
    },
    '官替平台列表' => function() {
        $manager = new DbOfficialReplaceManager();
        $platforms = $manager->getPlatforms();
        return ['success' => true, 'count' => count($platforms)];
    },
    '推荐采集站列表' => function() {
        $manager = new DbOfficialSiteManager();
        $sites = $manager->getAllSites(true);
        return ['success' => true, 'count' => count($sites)];
    },
    '代理列表' => function() {
        $manager = new DbProxyManager();
        $proxies = $manager->getAllProxies();
        return ['success' => true, 'count' => count($proxies)];
    },
    '配置管理' => function() {
        $manager = new DbConfigManager();
        $config = $manager->getAll();
        return ['success' => true, 'count' => count($config)];
    },
    '保存规则测试' => function() {
        $manager = new DbDomainRuleManager();
        $testRules = [
            'domain' => 'test.example.com',
            'name' => '测试域名',
            'ad_threshold' => 50,
            'learn_count' => 1,
            'duration_rules' => [],
            'discontinuity_rules' => [],
            'sequence_jump_rules' => [],
            'filename_patterns' => [],
        ];
        $result = $manager->saveRules('test.example.com', $testRules);
        $saved = $manager->getRules('test.example.com');
        $manager->deleteRules('test.example.com');
        return ['success' => $result && $saved !== null];
    },
];

$successCount = 0;
$failCount = 0;

foreach ($tests as $name => $testFn) {
    try {
        $result = $testFn();
        if ($result['success']) {
            echo "✓ $name";
            if (isset($result['count'])) {
                echo " ({$result['count']} 条)";
            }
            echo "\n";
            $successCount++;
        } else {
            echo "✗ $name: 失败\n";
            $failCount++;
        }
    } catch (Exception $e) {
        echo "✗ $name: " . $e->getMessage() . "\n";
        $failCount++;
    }
}

echo "\n=== 测试结果 ===\n";
echo "成功: $successCount\n";
echo "失败: $failCount\n";
echo "总计: " . ($successCount + $failCount) . "\n";

if ($failCount === 0) {
    echo "\n✓ 所有测试通过！数据库功能正常。\n";
} else {
    echo "\n✗ 部分测试失败，请检查错误信息。\n";
}
