<?php
require_once 'db/autoload.php';

echo "=== 数据迁移测试 ===\n";

try {
    $db = Database::getInstance();
    echo "✓ 数据库连接成功\n";
    
    $migration = new DataMigration($db);
    echo "\n=== 开始迁移数据 ===\n";
    $result = $migration->migrateAll();
    
    echo "\n=== 迁移结果 ===\n";
    echo "成功: " . ($result['success'] ? "是" : "否") . "\n";
    
    if (!empty($result['summary'])) {
        echo "\n迁移摘要:\n";
        foreach ($result['summary'] as $table => $stats) {
            echo "  $table: 迁移 " . $stats['migrated'] . " 条, 跳过 " . $stats['skipped'] . " 条\n";
        }
    }
    
    if (!empty($result['errors'])) {
        echo "\n错误列表:\n";
        foreach ($result['errors'] as $error) {
            echo "  [" . $error['category'] . "] " . $error['message'] . "\n";
        }
    }
    
    echo "\n=== 验证数据 ===\n";
    $ruleCount = $db->count('domain_rules');
    $siteCount = $db->count('resource_sites');
    $proxyCount = $db->count('proxies');
    $configCount = $db->count('sys_config');
    
    echo "  domain_rules: $ruleCount 条\n";
    echo "  resource_sites: $siteCount 条\n";
    echo "  proxies: $proxyCount 条\n";
    echo "  sys_config: $configCount 条\n";
    
} catch (Exception $e) {
    echo "✗ 错误: " . $e->getMessage() . "\n";
    echo "  文件: " . $e->getFile() . "\n";
    echo "  行号: " . $e->getLine() . "\n";
}

echo "\n=== 迁移测试完成 ===\n";
