<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';
$mgr = new OfficialReplaceManager();

$rDetect = new ReflectionMethod($mgr, 'detectPlatform');
$rDetect->setAccessible(true);
$platform = $rDetect->invoke($mgr, $url);
if (!$platform) { echo "detectPlatform FAIL\n"; exit(1); }

$r1 = new ReflectionMethod($mgr, 'fetchVideoInfo');
$r1->setAccessible(true);
$videoIds = ['video_id' => '', 'cover_id' => ''];
$videoInfo = $r1->invoke($mgr, $url, $platform, $videoIds);

// ======== 仿 resolve() 第 229-248 行的 base_title/episode 字段迁移 ========
if (!empty($videoInfo['episode_info']['base_title_guess'])
    && mb_strlen($videoInfo['episode_info']['base_title_guess']) >= 2
    && (empty($videoInfo['base_title']) || mb_strlen($videoInfo['base_title']) < 2)) {
    $videoInfo['base_title'] = $videoInfo['episode_info']['base_title_guess'];
}
if (!empty($videoInfo['episode_info']['subtitle_guess'])) {
    if (empty($videoInfo['episode']) || mb_strlen($videoInfo['episode']) < 3) {
        $videoInfo['episode'] = $videoInfo['episode_info']['subtitle_guess'];
    }
    $videoInfo['episode_subtitle'] = $videoInfo['episode_info']['subtitle_guess'];
}
if (!empty($videoInfo['episode_info']['episode_name']) && empty($videoInfo['episode'])) {
    $videoInfo['episode'] = $videoInfo['episode_info']['episode_name'];
}
if (empty($videoInfo['episode_num']) && !empty($videoInfo['episode_info']['episode_num'])) {
    $videoInfo['episode_num'] = $videoInfo['episode_info']['episode_num'];
    $videoInfo['episode'] = $videoInfo['episode_info']['episode_name'] ?: ($videoInfo['episode'] ?? '第' . $videoInfo['episode_num'] . '集');
}
echo "=== 仿 resolve() 迁移后 videoInfo 核心字段 ===\n";
echo "base_title       = " . var_export($videoInfo['base_title'] ?? null, true) . "\n";
echo "episode_num      = " . var_export($videoInfo['episode_num'] ?? null, true) . "\n";
echo "episode          = " . var_export($videoInfo['episode'] ?? '', true) . "\n";
echo "episode_subtitle = " . var_export($videoInfo['episode_subtitle'] ?? '', true) . "\n";
echo "title            = {$videoInfo['title']}\n";

$r2 = new ReflectionMethod($mgr, 'buildSearchKeywords');
$r2->setAccessible(true);
$kws = $r2->invoke($mgr, $videoInfo, $platform['name']);
echo "\n=== buildSearchKeywords 输出 (共 " . count($kws) . ") ===\n";
foreach ($kws as $i => $kw) {
    echo "  [$i] " . json_encode($kw, JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n=== 断言 ===\n";
$pass = true;
foreach ($kws as $kw) {
    if (strpos($kw, '吴老狗达成合作 第2集 张启山') !== false) {
        echo "FAIL: 搜索词 '$kw' 出现副标题重复拼接\n"; $pass = false;
    }
    if (preg_match('/门第[^0-9第]/u', $kw)) {
        echo "FAIL: 搜索词 '$kw' 出现垃圾 '门第X' 序列\n"; $pass = false;
    }
}
if (in_array("九门 第2集 张启山和吴老狗达成合作", $kws, true)) echo "PASS: 核心最高优先级搜索词「九门 第2集 张启山和吴老狗达成合作」存在\n";
else { echo "FAIL: 核心搜索词缺失！\n"; $pass = false; }
if (in_array("九门 第2集", $kws, true)) echo "PASS: 纯集数搜索词「九门 第2集」存在\n";
else { echo "FAIL: 纯集数搜索词缺失\n"; $pass = false; }
if (in_array("九门", $kws, true)) echo "PASS: base_title 搜索词「九门」存在\n";
else { echo "FAIL: base_title 搜索词缺失\n"; $pass = false; }
echo "\n最终：" . ($pass ? "✅ 全部断言通过" : "⚠ 有断言失败，见上方") . "\n";
