<?php
/**
 * 新增抖剧TV 资源站/官替平台并设置为默认官替优先
 * run: php /workspace/add_douju_tv.php
 */
declare(strict_types=1);

require_once __DIR__ . '/db/autoload.php';

$siteMgr = new DbResourceSiteManager();
$officialMgr = new DbOfficialReplaceManager();

echo "========================================\n";
echo "Step 1: 添加抖剧TV 到资源站 resource_sites (priority=1 最高优先)\n";
echo "========================================\n";

$doujuSite = [
    'name' => '抖剧TV',
    'site_url' => 'https://www.douju.tv',
    'api_url' => 'https://www.douju.tv/api.php/provide/vod/',
    'type' => 'maccms',
    'status' => 'active',
    'priority' => 1,
    'note' => '默认官替资源站｜根源来源 www.360kan.com｜苹果CMS10 采集接口｜官替优先1',
    'root_source' => 'www.360kan.com',
    'group_name' => '官替资源站',
    'official_replace' => 1,
];

$exists = $siteMgr->getSiteByName($doujuSite['name']);
if ($exists) {
    echo "⚠️ 抖剧TV 资源站已存在，更新字段...\n";
    $res = $siteMgr->updateSiteById($exists['id'], [
        'site_url' => $doujuSite['site_url'],
        'api_url' => $doujuSite['api_url'],
        'type' => $doujuSite['type'],
        'status' => $doujuSite['status'],
        'priority' => $doujuSite['priority'],
        'note' => $doujuSite['note'],
        'root_source' => $doujuSite['root_source'],
        'group_name' => $doujuSite['group_name'],
        'official_replace' => 1,
    ]);
    echo "  结果：" . ($res['success'] ? "✅ OK" : "❌ " . $res['message']) . "\n";
} else {
    $res = $siteMgr->addSite($doujuSite);
    echo "  结果：" . ($res['success'] ? "✅ OK" : "❌ " . $res['message']) . "\n";
}

echo "\n========================================\n";
echo "Step 2: 添加抖剧TV 到官替平台 official_platforms (priority=1 最优先)\n";
echo "========================================\n";

$doujuOfficialPlatform = [
    'name' => '抖剧TV',
    'domain' => 'douju.tv',
    'enabled' => true,
    'pattern' => '/douju\.tv\/.*?\/(?:vid|vod|play\/id)\/?([a-zA-Z0-9_-]+)/i',
    'title_selector' => 'meta[property=og:title],title,h1.title,h1',
    'priority' => 1,
    'config' => json_encode([
        'root_source' => 'www.360kan.com',
        'api_url' => 'https://www.douju.tv/api.php/provide/vod/',
        'official_default' => true,
        'search_keyword_template' => '{base_title}{episode}',
    ], JSON_UNESCAPED_UNICODE),
];

$existsPlatform = $officialMgr->getPlatformByName($doujuOfficialPlatform['name']);
if ($existsPlatform) {
    echo "⚠️ 抖剧TV 官替平台已存在，更新字段...\n";
    $updateData = $doujuOfficialPlatform;
    unset($updateData['name']);
    $res = $officialMgr->updatePlatform($doujuOfficialPlatform['name'], $updateData);
    echo "  结果：" . ($res['success'] ? "✅ OK" : "❌ " . ($res['message'] ?? '未知错误')) . "\n";
} else {
    $res = $officialMgr->addPlatform($doujuOfficialPlatform);
    echo "  结果：" . ($res['success'] ? "✅ OK" : "❌ " . ($res['message'] ?? '未知错误')) . "\n";
}

echo "\n========================================\n";
echo "Step 3: 更新官替配置 default_site=抖剧TV + search_sites 优先使用抖剧TV\n";
echo "========================================\n";

$config = $officialMgr->getConfig();
$origDefault = $config['default_site'] ?? '';
$config['default_site'] = '抖剧TV';
$origSearchSites = $config['search_sites'] ?? [];
// 将抖剧TV 放在 search_sites 第一位（官替优先站）
$newSearchSites = [];
foreach (['抖剧TV'] as $s) {
    if (!in_array($s, $newSearchSites, true)) $newSearchSites[] = $s;
}
// 把原来 search_sites 的其他站保留，但抖剧TV排第一
foreach ($origSearchSites as $s) {
    if ($s === '抖剧TV') continue;
    if (!in_array($s, $newSearchSites, true)) $newSearchSites[] = $s;
}
$config['search_sites'] = $newSearchSites;
$config['match_threshold'] = $config['match_threshold'] ?? 70;
$config['max_search_sites'] = $config['max_search_sites'] ?? 8;
$config['default_priority_rule'] = '数字越小越优先，范围 1-1000+，1=最高优先';

$res = $officialMgr->saveConfig($config);
echo "  default_site: $origDefault → " . $config['default_site'] . "\n";
echo "  search_sites(头5个): " . implode(', ', array_slice($config['search_sites'], 0, 5)) . " ...\n";
echo "  结果：" . ($res ? "✅ 配置已保存" : "❌ 保存失败") . "\n";

echo "\n========================================\n";
echo "Step 4: 验证抖剧TV API（苹果CMS10）是否可连通\n";
echo "========================================\n";
$testApi = 'https://www.douju.tv/api.php/provide/vod/?ac=list&limit=1';
echo "  GET " . $testApi . " ...\n";
$ch = curl_init($testApi);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36');
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "  ❌ CURL 错误: " . $err . "\n";
} else {
    $json = json_decode($resp, true);
    echo "  HTTP Code: $code, body len: " . strlen($resp) . ", json: " . ($json ? "OK" : "NO JSON") . "\n";
    if ($json) {
        echo "  字段: " . implode(", ", array_keys($json)) . "\n";
        if (isset($json['list']) && is_array($json['list'])) {
            echo "  list count: " . count($json['list']) . "\n";
            if (count($json['list']) > 0) {
                $first = $json['list'][0];
                echo "  first[vod_name]: " . ($first['vod_name'] ?? '-') . " | id: " . ($first['vod_id'] ?? '-') . "\n";
            }
        }
    }
}

echo "\n========================================\n";
echo "Step 5: 最终验证列表\n";
echo "========================================\n";

$plats = $officialMgr->getAllPlatforms(false);
echo "--- 官替平台（按 priority ASC 升序）---\n";
foreach ($plats as $p) {
    echo sprintf("  [priority=%2d] %-10s domain=%-20s enabled=%s\n",
        $p["priority"] ?? 10, $p["name"], $p["domain"], $p["enabled"] ? "ON":"OFF");
}

$finalSites = $siteMgr->getAllSites(true);
echo "--- 活跃资源站头 10（按 priority ASC）---\n";
$count = 0;
foreach ($finalSites as $s) {
    if ($count++ >= 10) break;
    echo sprintf("  [priority=%2d] %-12s api=%s\n",
        $s["priority"] ?? 99, $s["name"],
        strlen($s["api_url"]) > 50 ? substr($s["api_url"],0,50)."..." : $s["api_url"]);
}

echo "\n✅ 抖剧TV 安装完成!\n";
echo "   - 官替 default_site: 抖剧TV\n";
echo "   - 官替/资源站 priority: 1 (最高优先)\n";
echo "   - search_sites 顺序第 1 个: 抖剧TV\n";
echo "   - 采集接口: https://www.douju.tv/api.php/provide/vod/\n";
echo "   - 根源: www.360kan.com\n";