<?php
/**
 * 官替 API v3.2.20 功能测试
 * 测试 safeJsonDecode、解析器等核心功能
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/gz/TitleNormalizer.php';
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

echo "========================================\n";
echo " 官替 API v3.2.20 功能测试\n";
echo "========================================\n\n";

$testsPassed = 0;
$testsFailed = 0;

function test($name, $condition) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "✅ $name\n";
        $testsPassed++;
    } else {
        echo "❌ $name\n";
        $testsFailed++;
    }
}

// 测试 1: 类加载
echo "--- 基础测试 ---\n";
test('OfficialReplaceManager 类存在', class_exists('OfficialReplaceManager'));
test('TitleNormalizer 类存在', class_exists('TitleNormalizer'));

// 测试 2: 实例化
try {
    $mgr = new OfficialReplaceManager();
    test('实例化 OfficialReplaceManager 成功', true);
} catch (Throwable $e) {
    test('实例化 OfficialReplaceManager 成功', false);
    echo "  错误: " . $e->getMessage() . "\n";
}

// 测试 3: safeJsonDecode 方法测试
echo "\n--- safeJsonDecode 测试 ---\n";

$reflection = new ReflectionClass('OfficialReplaceManager');
$method = $reflection->getMethod('safeJsonDecode');
$method->setAccessible(true);

// 标准 JSON
$standardJson = '{"name":"test","value":123}';
$result = $method->invoke($mgr, $standardJson);
test('标准 JSON 解析', is_array($result) && $result['name'] === 'test' && $result['value'] === 123);

// 带变量前缀
$prefixJson = 'var data={"name":"test","value":456}';
$result = $method->invoke($mgr, $prefixJson);
test('变量前缀 JSON 解析', is_array($result) && $result['name'] === 'test' && $result['value'] === 456);

// JSONP 格式
$jsonpJson = 'callback({"name":"test","value":789})';
$result = $method->invoke($mgr, $jsonpJson);
test('JSONP 格式解析', is_array($result) && $result['name'] === 'test' && $result['value'] === 789);

// 带分号结尾
$semicolonJson = '{"name":"test","value":111};';
$result = $method->invoke($mgr, $semicolonJson);
test('分号结尾 JSON 解析', is_array($result) && $result['name'] === 'test' && $result['value'] === 111);

// 带注释
$commentJson = '/* comment */{"name":"test","value":222}';
$result = $method->invoke($mgr, $commentJson);
test('注释前缀 JSON 解析', is_array($result) && $result['name'] === 'test' && $result['value'] === 222);

// 空字符串
$emptyJson = '';
$result = $method->invoke($mgr, $emptyJson);
test('空字符串返回 null', $result === null);

// 非 JSON 内容（HTML）
$htmlContent = '<html><head><title>Test</title></head></html>';
$result = $method->invoke($mgr, $htmlContent);
test('HTML 内容返回 null', $result === null);

// 测试 4: 平台检测
echo "\n--- 平台检测测试 ---\n";

$detectPlatformMethod = $reflection->getMethod('detectPlatform');
$detectPlatformMethod->setAccessible(true);

$platforms = [
    'https://v.qq.com/x/cover/abc123/xyz789.html' => 'v.qq.com',
    'https://www.iqiyi.com/v_abc123def45.html' => 'iqiyi.com',
    'https://www.mgtv.com/b/12345/67890.html' => 'mgtv.com',
    'https://v.youku.com/v_show/id_abc123==.html' => 'youku.com',
    'https://www.bilibili.com/video/BV1xx411c7mD' => 'bilibili.com',
    'https://tv.sohu.com/v/dXMvMjAzNzYxLzk3NDcwNzQuc2h0bWw=.shtml' => 'sohu.com',
    'https://v.pptv.com/show/abc123def.html' => 'pptv.com',
];

foreach ($platforms as $url => $expectedDomain) {
    $platform = $detectPlatformMethod->invoke($mgr, $url);
    $platformDomain = $platform['domain'] ?? '';
    test("平台检测: $expectedDomain", $platformDomain === $expectedDomain);
}

// 测试 5: 配置获取
echo "\n--- 配置测试 ---\n";

$config = $mgr->getConfig();
test('获取配置成功', is_array($config));
test('配置包含 platforms', isset($config['platforms']) && is_array($config['platforms']));
test('配置包含 match_threshold', isset($config['match_threshold']));
test('配置包含 search_sites', isset($config['search_sites']));

// 测试 6: 标题解析
echo "\n--- 标题解析测试 ---\n";

$parseTitleMethod = $reflection->getMethod('parseVideoTitle');
$parseTitleMethod->setAccessible(true);

$testTitles = [
    ['遮天 第2集', '遮天', null, 2],
    ['庆余年 第二季 第3集', '庆余年', 2, 3],
    ['三体S01E05', '三体', 1, 5],
    ['遮天_02', '遮天', null, 2],
    ['遮天-02', '遮天', null, 2],
];

foreach ($testTitles as $tt) {
    [$title, $expectedBase, $expectedSeason, $expectedEpisode] = $tt;
    $parsed = $parseTitleMethod->invoke($mgr, $title);
    $baseOk = ($parsed['base_title'] ?? '') === $expectedBase;
    $seasonOk = ($parsed['season_num'] ?? null) === $expectedSeason;
    $episodeOk = ($parsed['episode_num'] ?? null) === $expectedEpisode;
    test("标题解析: $title", $baseOk && $seasonOk && $episodeOk);
    if (!$baseOk) echo "    base_title: 期望 '{$expectedBase}', 实际 '{$parsed['base_title']}'\n";
    if (!$seasonOk) echo "    season_num: 期望 " . var_export($expectedSeason, true) . ", 实际 " . var_export($parsed['season_num'], true) . "\n";
    if (!$episodeOk) echo "    episode_num: 期望 " . var_export($expectedEpisode, true) . ", 实际 " . var_export($parsed['episode_num'], true) . "\n";
}

// 测试 7: resolve 方法基本错误处理
echo "\n--- resolve 错误处理测试 ---\n";

$result = $mgr->resolve('');
test('空 URL 返回失败', isset($result['success']) && $result['success'] === false);

$result = $mgr->resolve('https://www.unknown-site.com/video.html');
test('不支持的平台返回失败', isset($result['success']) && $result['success'] === false);

// 测试 8: TitleNormalizer 功能
echo "\n--- TitleNormalizer 测试 ---\n";

test('TitleNormalizer normalize 存在', method_exists('TitleNormalizer', 'normalize'));

$normTest = TitleNormalizer::normalize('  遮天 动画版  ');
test('标题标准化: 去除空格', trim($normTest) === $normTest && !empty($normTest));

// 测试 9: 数据库版管理器测试
echo "\n--- 数据库版管理器测试 ---\n";

if (file_exists(__DIR__ . '/db/DbOfficialReplaceManager.php')) {
    require_once __DIR__ . '/db/DbOfficialReplaceManager.php';
    test('DbOfficialReplaceManager 类存在', class_exists('DbOfficialReplaceManager'));
    
    // 检查 safeJsonDecode 方法是否存在
    $dbReflection = new ReflectionClass('DbOfficialReplaceManager');
    test('DbOfficialReplaceManager 有 safeJsonDecode 方法', $dbReflection->hasMethod('safeJsonDecode'));
    test('DbOfficialReplaceManager 有 findAllMatches 方法', $dbReflection->hasMethod('findAllMatches'));
    test('DbOfficialReplaceManager 有 fetchVideoInfoFromApi 方法', $dbReflection->hasMethod('fetchVideoInfoFromApi'));
}

// 总结
echo "\n========================================\n";
echo " 测试完成\n";
echo " 通过: $testsPassed\n";
echo " 失败: $testsFailed\n";
echo " 总计: " . ($testsPassed + $testsFailed) . "\n";
echo "========================================\n";

exit($testsFailed > 0 ? 1 : 0);
