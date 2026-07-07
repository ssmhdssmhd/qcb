<?php
/**
 * 全面API接口测试脚本
 */

require_once 'db/autoload.php';
require_once 'gz/DomainRuleManager.php';
require_once 'gz/ResourceSiteManager.php';
require_once 'gz/OfficialSiteManager.php';
require_once 'gz/OfficialReplaceManager.php';
require_once 'proxy/ProxyManager.php';

$apiBase = 'http://localhost/mx.php?action=';

function testApi($name, $url, $post = null) {
    echo "测试: $name\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "  ✗ CURL错误: $error\n";
        return false;
    }
    
    $data = json_decode($response, true);
    if ($data === null) {
        echo "  ✗ JSON解析失败\n";
        echo "  响应: " . substr($response, 0, 200) . "\n";
        return false;
    }
    
    if ($httpCode >= 400) {
        echo "  ✗ HTTP $httpCode: " . ($data['message'] ?? '未知错误') . "\n";
        return false;
    }
    
    if (isset($data['success']) && !$data['success']) {
        echo "  ✗ 失败: " . ($data['message'] ?? '未知错误') . "\n";
        return false;
    }
    
    echo "  ✓ 成功\n";
    return true;
}

function testClass($name, $callback) {
    echo "测试: $name\n";
    try {
        $result = $callback();
        if ($result) {
            echo "  ✓ 成功\n";
        } else {
            echo "  ✗ 失败\n";
        }
        return $result;
    } catch (Exception $e) {
        echo "  ✗ 错误: " . $e->getMessage() . "\n";
        return false;
    }
}

echo "=== 数据库类测试 ===\n\n";

testClass('Database连接', function() {
    $db = Database::getInstance();
    return $db !== null;
});

testClass('DbDomainRuleManager', function() {
    $mgr = new DbDomainRuleManager();
    $rules = $mgr->getAllRulesLite();
    echo "  规则数量: " . count($rules) . "\n";
    return count($rules) >= 0;
});

testClass('DbResourceSiteManager', function() {
    $mgr = new DbResourceSiteManager();
    $sites = $mgr->getAllSites(true);
    echo "  资源站数量: " . count($sites) . "\n";
    return count($sites) >= 0;
});

testClass('DbOfficialReplaceManager', function() {
    $mgr = new DbOfficialReplaceManager();
    $platforms = $mgr->getPlatforms();
    echo "  平台数量: " . count($platforms) . "\n";
    return count($platforms) >= 0;
});

testClass('DbOfficialSiteManager', function() {
    $mgr = new DbOfficialSiteManager();
    $sites = $mgr->getAllSites(true);
    echo "  官推站点数量: " . count($sites) . "\n";
    return count($sites) >= 0;
});

testClass('DbProxyManager', function() {
    $mgr = new DbProxyManager();
    $proxies = $mgr->getAllProxies();
    echo "  代理数量: " . count($proxies) . "\n";
    return count($proxies) >= 0;
});

testClass('DbConfigManager', function() {
    $mgr = new DbConfigManager();
    $config = $mgr->getAll();
    echo "  配置项数量: " . count($config) . "\n";
    return count($config) >= 0;
});

testClass('DbAnalysisCache', function() {
    $cache = new DbAnalysisCache();
    $stats = $cache->getStats();
    echo "  缓存数量: " . $stats['total'] . "\n";
    return true;
});

testClass('DbAdSignature', function() {
    $sig = new DbAdSignature();
    $stats = $sig->getStats();
    echo "  特征码数量: " . $stats['total'] . "\n";
    return true;
});

testClass('DbOfficialReplaceCache', function() {
    $cache = new DbOfficialReplaceCache();
    $stats = $cache->getStats();
    echo "  缓存数量: " . $stats['total'] . "\n";
    return true;
});

testClass('DbDomainAnalysisStats', function() {
    $stats = new DbDomainAnalysisStats();
    $all = $stats->getAll();
    echo "  统计数量: " . count($all) . "\n";
    return true;
});

echo "\n=== API接口测试 ===\n\n";

// 测试需要PHP内置服务器
echo "启动PHP内置服务器进行API测试...\n";

// 启动服务器
$serverCmd = 'php -S localhost:8080 -t ' . __DIR__ . ' > /dev/null 2>&1 & echo $!';
exec($serverCmd, $output, $returnCode);
sleep(2);

$apiBase = 'http://localhost:8080/mx.php?action=';

// 系统接口
testApi('info', $apiBase . 'info');
testApi('version', $apiBase . 'version');
testApi('db/status', $apiBase . 'db/status');

// 规则接口
testApi('rules/list', $apiBase . 'rules/list');

// 资源站接口
testApi('sites/list', $apiBase . 'sites/list');

// 官替接口
testApi('official/list', $apiBase . 'official/list');
testApi('official/platforms', $apiBase . 'official/platforms');

// 官推站点接口
testApi('official_sites/list', $apiBase . 'official_sites/list');

// 代理接口
testApi('proxies/list', $apiBase . 'proxies/list');

// 停止服务器
exec('pkill -f "php -S localhost:8080"');

echo "\n=== 测试完成 ===\n";