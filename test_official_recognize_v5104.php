<?php
/**
 * 官替识别验证脚本 - v5.10.4
 * 验证：检测平台 → 提取ID → 搜索资源站(抖剧TV) → 匹配
 * run: php /workspace/test_official_recognize_v5104.php
 */
declare(strict_types=1);

require_once __DIR__ . '/db/autoload.php';

$officialMgr = new DbOfficialReplaceManager();
$siteMgr = new DbResourceSiteManager();

echo "=== 验证1：官替识别 (detectPlatform) ===\n";

$testUrls = [
    // 腾讯视频
    ['https://v.qq.com/x/cover/mzc00200mp8vo9b/e0046j2c4rp.html', 'v.qq.com'],
    ['https://m.v.qq.com/x/cover/mzc00200mp8vo9b.html', 'v.qq.com'],
    // 爱奇艺 (移动+pc)
    ['https://www.iqiyi.com/v_19rr8e0y9o.html', 'iqiyi.com'],
    ['https://m.iqiyi.com/v_19rrcuim39.html', 'iqiyi.com'],
    // 优酷
    ['https://v.youku.com/v_show/id_XNTk4NjM2NDgyNA==.html', 'youku.com'],
    // 芒果
    ['https://www.mgtv.com/b/560752/21852659.html', 'mgtv.com'],
    ['https://m.mgtv.com/b/384777/19283745.html', 'mgtv.com'],
    // B站
    ['https://www.bilibili.com/video/BV1GJ411x7h7/', 'bilibili.com'],
    ['https://b23.tv/BV1GJ411x7h7', 'bilibili.com'],  // 短链
    // 抖剧TV & 360kan
    ['https://www.douju.tv/vod/detail/id/12345.html', 'douju.tv'],
    ['https://www.360kan.com/ct/4EdoaUMhYGkUDn.html', 'douju.tv'],   // 360kan 根源 → 抖剧TV
    // 搜狐
    ['https://tv.sohu.com/v/dXMvMzI1MDM4Ny8zMjU5ODgyLnNodG1s.html', 'sohu.com'],
    // PPTV
    ['https://v.pptv.com/show/abcdefghijk.html', 'pptv.com'],
];

$passed = 0;
$total = count($testUrls);
$refDetect = new ReflectionMethod($officialMgr, 'detectPlatform');
$refDetect->setAccessible(true);
$refFuzzy = new ReflectionMethod($officialMgr, 'detectPlatformFuzzy');
$refFuzzy->setAccessible(true);
foreach ($testUrls as [$url, $expectDomain]) {
    $plat = $refDetect->invoke($officialMgr, $url);
    if (!$plat) {
        $plat = $refFuzzy->invoke($officialMgr, $url);
    }
    $ok = $plat && ($plat['domain'] === $expectDomain);
    $passed += $ok ? 1 : 0;
    $host = parse_url($url, PHP_URL_HOST);
    echo sprintf("  %s %-28s -> expect=%-14s got=%-14s\n",
        $ok ? "✅" : "❌",
        $host,
        $expectDomain,
        $plat ? ($plat['domain'] . "(".$plat['name'].")") : "NULL"
    );
}
echo "  识别通过率: $passed / $total\n\n";

echo "=== 验证2：抖剧TV 资源站 end-to-end 解析 (搜索+匹配) ===\n";
$doujuSite = $siteMgr->getSiteByName('抖剧TV');
if (!$doujuSite) {
    echo "❌ 抖剧TV资源站不存在！\n"; exit(1);
}
echo "抖剧TV site id: ".$doujuSite['id']." api: ".$doujuSite['api_url']."\n";

$searchWords = ['狂飙 第1集', '庆余年 第二季', '九门', '漫长的季节'];
$totalSearch = count($searchWords);
$searchPassed = 0;
foreach ($searchWords as $wd) {
    $res = $siteMgr->searchVideos($doujuSite['api_url'], $wd, 1, 5);
    $cnt = count($res['videos'] ?? []);
    $ok = $res['success'] && $cnt > 0;
    $searchPassed += $ok ? 1 : 0;
    echo sprintf("  %s 搜索[%s] -> videos=%d strategy=%s msg=%s\n",
        $ok ? "✅":"❌",
        $wd,
        $cnt,
        $res['strategy'] ?? '-',
        $ok ? "" : ($res['message'] ?? 'unknown')
    );
}
echo "  资源站搜索通过率: $searchPassed / $totalSearch\n\n";

echo "=== 验证3：官替配置（default_site, search_sites[0]）=== \n";
$cfg = $officialMgr->getConfig();
echo "  enabled: " . ($cfg['enabled']?"YES":"NO") . "\n";
echo "  default_site: " . ($cfg['default_site'] ?? '-') . "\n";
$headSites = array_slice($cfg['search_sites'] ?? [], 0, 3);
echo "  search_sites头3: " . implode(", ", $headSites) . "\n";
echo "  match_threshold: " . ($cfg['match_threshold'] ?? '-') . "\n";
$okDefault = ($cfg['default_site'] ?? '') === '抖剧TV' && !empty($cfg['search_sites']) && $cfg['search_sites'][0] === '抖剧TV';
echo "  官替配置正确? " . ($okDefault ? "✅ YES" : "❌ NO") . "\n\n";

echo "=== 验证4：cleanTitle 实体解码 & 清洗 ===\n";
$cleanTests = [
    ['&#12298;狂飙&#12299; 第1集 高启强扫黑除恶_腾讯视频', '狂飙'],
    ['\u5e86\u4f59\u5e74\u7b2c\u4e8c\u5b63 4K蓝光-爱奇艺', '庆余年第二季'],
    ['庆余年2【高清未删减完整版】预告片1080P在线观看 - 优酷视频', '庆余年2'],
    ['《与凤行》定档预告 林更新赵丽颖_芒果TV', '与凤行'],
];
$cleanPassed = 0;
$m = new DbOfficialReplaceManager();
$reflectionCleanTitle = new ReflectionMethod($m, 'cleanTitle');
$reflectionCleanTitle->setAccessible(true);
foreach ($cleanTests as [$raw, $expect]) {
    $got = $reflectionCleanTitle->invoke($m, $raw);
    $ok = $got !== null && mb_strpos($got, $expect) !== false;
    $cleanPassed += $ok ? 1 : 0;
    echo sprintf("  %s clean('%s') = '%s' (expect contains '%s')\n",
        $ok ? "✅":"❌",
        mb_substr($raw, 0, 40).(mb_strlen($raw)>40?'...':''),
        $got ?? 'NULL',
        $expect
    );
}
echo "  cleanTitle通过率: $cleanPassed / ".count($cleanTests)."\n";

echo "\n=== 总计 ===\n";
$grand = $passed + $searchPassed + $cleanPassed + ($okDefault ? 1 : 0);
$grandTotal = $total + $totalSearch + count($cleanTests) + 1;
echo "综合通过率: $grand / $grandTotal\n";
if ($grand === $grandTotal) echo "🎉 全部通过！官替识别优化 v5.10.4 成功！\n";