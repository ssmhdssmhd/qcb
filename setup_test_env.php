<?php
/**
 * 靶机测试一键部署脚本
 * 
 * 一键部署完整的靶机测试环境：
 * - 生成测试 m3u8 文件
 * - 配置测试资源站
 * - 生成演示规则
 * - 输出测试指南
 * 
 * 使用方法：
 *   php setup_test_env.php
 *   或通过浏览器访问 setup_test_env.php
 */

header('Content-Type: text/plain; charset=utf-8');

$rootDir = __DIR__;
$cacheDir = $rootDir . '/cache';
$testM3u8Dir = $cacheDir . '/m3u8/test';
$configFile = $cacheDir . '/config.php';
$sitesConfig = $cacheDir . '/sites_config.json';

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║           🎬 M3U8 广告分析系统 - 靶机测试环境一键部署             ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// 步骤 1: 生成测试 m3u8 文件
echo "【步骤 1/4】生成测试 M3U8 文件...\n";
echo str_repeat('─', 60) . "\n";

if (!is_dir($testM3u8Dir)) {
    mkdir($testM3u8Dir, 0777, true);
    echo "  ✓ 创建测试目录: $testM3u8Dir\n";
}

// 调用生成器生成测试文件
$_GET['type'] = 'all';
$_SERVER['PHP_SELF'] = '/generate_test_m3u8.php';
ob_start();
require $rootDir . '/generate_test_m3u8.php';
ob_end_clean();

$testFiles = glob($testM3u8Dir . '/*.m3u8');
echo "  ✓ 生成了 " . count($testFiles) . " 个测试 m3u8 文件\n";
foreach ($testFiles as $file) {
    echo "    - " . basename($file) . " (" . filesize($file) . " 字节)\n";
}
echo "\n";

// 步骤 2: 配置测试资源站
echo "【步骤 2/4】配置测试资源站...\n";
echo str_repeat('─', 60) . "\n";

// 检测当前访问协议和域名
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = dirname($_SERVER['PHP_SELF'] ?? '/');
$basePath = $basePath === '.' ? '' : $basePath;
$apiUrl = $protocol . '://' . $host . $basePath . '/test_site_api.php';

$testSites = [
    [
        'name' => '靶机测试站',
        'api_url' => $apiUrl,
        'site_url' => $protocol . '://' . $host . $basePath . '/',
        'type' => 'maccms',
        'status' => 1,
        'weight' => 100,
        'description' => '靶机测试专用资源站，包含各种广告类型的测试视频',
    ],
];

// 保存资源站配置
if (file_exists($sitesConfig)) {
    $existingSites = json_decode(file_get_contents($sitesConfig), true);
    if (!is_array($existingSites)) {
        $existingSites = [];
    }
    
    // 检查是否已存在测试站
    $found = false;
    foreach ($existingSites as &$site) {
        if ($site['name'] === '靶机测试站') {
            $site = array_merge($site, $testSites[0]);
            $found = true;
            break;
        }
    }
    unset($site);
    
    if (!$found) {
        $existingSites = array_merge($testSites, $existingSites);
    }
    
    $allSites = $existingSites;
} else {
    $allSites = $testSites;
}

file_put_contents($sitesConfig, json_encode($allSites, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "  ✓ 测试资源站配置已保存\n";
echo "    站点名称: 靶机测试站\n";
echo "    接口地址: $apiUrl\n";
echo "    视频数量: 5 部 (电视剧3部 + 电影2部)\n";
echo "\n";

// 步骤 3: 生成演示规则
echo "【步骤 3/4】生成演示规则...\n";
echo str_repeat('─', 60) . "\n";

$demoDomain = 'test.demo.local';
$demoRulesFile = $cacheDir . '/rules_' . $demoDomain . '.php';

$demoRules = [
    'domain' => $demoDomain,
    'name' => '演示规则（测试用）',
    'ad_segment_pattern' => '/ad/i',
    'content_segment_pattern' => '/content|main|正片/i',
    'ad_keywords' => ['广告', 'ad', 'pre', 'mid', 'post', '赞助'],
    'min_ad_duration' => 5,
    'max_ad_percentage' => 20,
    'skip_first' => 0,
    'skip_last' => 0,
    'description' => '靶机测试演示规则，请勿用于生产环境',
    'updated_at' => time(),
];

// 保存演示规则
$phpContent = '<?php' . "\nreturn " . var_export($demoRules, true) . ";\n";
file_put_contents($demoRulesFile, $phpContent);
echo "  ✓ 演示规则已生成\n";
echo "    域名: $demoDomain\n";
echo "    规则文件: " . basename($demoRulesFile) . "\n";
echo "\n";

// 步骤 4: 输出测试指南
echo "【步骤 4/4】测试指南\n";
echo str_repeat('─', 60) . "\n\n";

echo "📋 测试资源站地址:\n";
echo "  $apiUrl\n\n";

echo "🎞️  测试 M3U8 文件地址（请替换为你的域名）:\n";
foreach ($testFiles as $file) {
    $filename = basename($file);
    echo "  $protocol://$host$basePath/cache/m3u8/test/$filename\n";
}
echo "\n";

echo "🧪 测试场景:\n\n";

echo "  1. 【视频分析测试】\n";
echo "     入口: 后台 → 视频分析\n";
echo "     测试用例:\n";
echo "       - basic.m3u8         纯内容，无广告（验证广告识别准确率）\n";
echo "       - pre_roll.m3u8      前置广告（验证前向广告检测）\n";
echo "       - mid_roll.m3u8      中插广告（验证中段广告检测）\n";
echo "       - post_roll.m3u8     后置广告（验证后向广告检测）\n";
echo "       - mixed.m3u8         混合广告（综合场景测试）\n";
echo "       - short_segments.m3u8 短片段广告（边界情况测试）\n";
echo "       - long_movie.m3u8    长电影多段广告（大文件测试）\n\n";

echo "  2. 【规则学习测试】\n";
echo "     入口: 后台 → 搜索影视学习\n";
echo "     步骤:\n";
echo "       ① 选择资源站: 靶机测试站\n";
echo "       ② 搜索关键词: 庆余年 或 狂飙\n";
echo "       ③ 点击视频，查看自动学习效果\n\n";

echo "  3. 【批量学习测试】\n";
echo "     入口: 后台 → 搜索影视学习 → 批量学习\n";
echo "     或使用接口: sites/search_and_learn\n";
echo "     功能: 批量学习多个视频，验证多线程并发\n\n";

echo "  4. 【自动学习测试】\n";
echo "     入口: 后台 → 自动学习\n";
echo "     功能: 全自动从资源站学习规则\n\n";

echo "  5. 【去广告效果测试】\n";
echo "     入口: mx.php?action=mxjx&url=测试m3u8地址\n";
echo "     功能: 验证去广告后的 m3u8 输出\n\n";

echo "  6. 【官方替换测试】\n";
echo "     入口: mx.php?action=official_replace/resolve&url=视频地址\n";
echo "     功能: 测试官替解析流程\n\n";

echo "  7. 【虾米解析测试】\n";
echo "     入口: mx.php?action=xiami_jx&url=视频播放页地址\n";
echo "     功能: 测试第三方解析接口\n\n";

echo "🔗 PHP 调用示例:\n";
echo "  require_once 'api_helper.php';\n\n";
echo "  // 视频分析\n";
echo '  $result = analyzeVideo(' . "'$protocol://$host$basePath/cache/m3u8/test/mixed.m3u8');\n\n";
echo "  // 搜索并学习\n";
echo '  $result = searchAndLearn(' . "'庆余年', ['site_name' => '靶机测试站', 'multi_thread' => true]);\n\n";

echo str_repeat('═', 60) . "\n";
echo "  ✅ 靶机测试环境部署完成！\n";
echo str_repeat('═', 60) . "\n\n";

echo "📝 下一步操作:\n";
echo "  1. 打开后台管理页面: mxadmin.php\n";
echo "  2. 进入【视频分析】模块，粘贴测试 m3u8 地址\n";
echo "  3. 进入【搜索影视学习】模块，选择靶机测试站\n";
echo "  4. 测试各种广告场景，验证识别准确率\n";
echo "  5. 测试批量学习和自动学习功能\n\n";

echo "💡 小提示:\n";
echo "  - 测试 m3u8 文件使用注释标记广告位置，便于验证\n";
echo "  - 可以通过 generate_test_m3u8.php 生成更多自定义测试文件\n";
echo "  - 所有测试数据都在 cache/ 目录下，可以随时清理\n";
echo "\n";
