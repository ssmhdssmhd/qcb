<?php
// 快速诊断：单独跑 fetchVideoInfo + buildSearchKeywords，看搜索词在哪里重复
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$mgr = new OfficialReplaceManager();
// 用反射调用 fetchMeta_Youku + buildSearchKeywords
$r1 = new ReflectionMethod($mgr, 'fetchVideoInfo');
$r1->setAccessible(true);
$platform = '优酷';
$videoIds = ['video_id' => 'XNjU0MjcxNTM1Ng==', 'cover_id' => ''];
$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';
$info = $r1->invoke($mgr, $url, $platform, $videoIds);
echo "=== fetchVideoInfo result ===\n";
echo "title       = {$info['title']}\n";
echo "base_title  = " . ($info['base_title'] ?? '') . "\n";
echo "episode_num = " . ($info['episode_num'] ?? 'null') . "\n";
echo "episode     = " . ($info['episode'] ?? '') . "\n";
echo "episode_subtitle = " . ($info['episode_subtitle'] ?? '') . "\n";
echo "season_num  = " . ($info['season_num'] ?? 'null') . "\n";
echo "episode_info = " . json_encode($info['episode_info'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";

// 调 buildSearchKeywords
$r2 = new ReflectionMethod($mgr, 'buildSearchKeywords');
$r2->setAccessible(true);
$kws = $r2->invoke($mgr, $info, $platform);
echo "\n=== buildSearchKeywords 输出 (共 " . count($kws) . ") ===\n";
foreach ($kws as $i => $kw) {
    echo "  [$i] " . json_encode($kw, JSON_UNESCAPED_UNICODE) . "\n";
}
