<?php
require_once 'db/autoload.php';

echo "=== 数据库连接测试 ===\n";

try {
    $db = Database::getInstance();
    echo "✓ 数据库连接成功\n";
    echo "  类型: " . $db->getDbType() . "\n";
    
    if ($db->getDbType() === 'mysql') {
        $pdo = $db->getPdo();
        $stmt = $pdo->query("SELECT VERSION() as v");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  MySQL版本: " . $row['v'] . "\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = DATABASE()");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  现有表数量: " . $row['c'] . "\n";
    }
    
    $tables = ['sys_config', 'domain_rules', 'resource_sites'];
    echo "\n=== 表存在性检查 ===\n";
    foreach ($tables as $table) {
        $exists = $db->tableExists($table);
        echo ($exists ? "✓" : "✗") . " $table: " . ($exists ? "存在" : "不存在") . "\n";
    }
    
    echo "\n=== 初始化表结构 ===\n";
    $result = $db->initTables();
    echo "✓ 表结构初始化完成\n";
    
    echo "\n=== 再次检查表 ===\n";
    foreach ($tables as $table) {
        $exists = $db->tableExists($table);
        echo ($exists ? "✓" : "✗") . " $table: " . ($exists ? "存在" : "不存在") . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ 错误: " . $e->getMessage() . "\n";
    echo "  文件: " . $e->getFile() . "\n";
    echo "  行号: " . $e->getLine() . "\n";
}

echo "\n=== 测试完成 ===\n";
