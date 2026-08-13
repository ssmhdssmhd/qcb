<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';
$mgr = new OfficialReplaceManager();

$rDetect = new ReflectionMethod($mgr, 'detectPlatform');
$rDetect->setAccessible(true);
$platform = $rDetect->invoke($mgr, $url);
if (!$platform) { echo "detectPlatform FAIL\n"; exit(1); }
echo "platform.name={$platform['name']}\n";

$r1 = new ReflectionMethod($mgr, 'fetchVideoInfo');
$r1->setAccessible(true);
$videoIds = ['video_id' => '', 'cover_id' => ''];
$info = $r1->invoke($mgr, $url, $platform, $videoIds);
echo "=== fetchVideoInfo result ===\n";
echo "title            = {$info['title']}\n";
echo "base_title       = " . ($info['base_title'] ?? '') . "\n";
echo "episode_num      = " . var_export($info['episode_num'] ?? null, true) . "\n";
echo "episode          = " . var_export($info['episode'] ?? '', true) . "\n";
echo "episode_subtitle = " . var_export($info['episode_subtitle'] ?? '', true) . "\n";
echo "season_num       = " . var_export($info['season_num'] ?? null, true) . "\n";
echo "episode_info     = " . json_encode($info['episode_info'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";

// 调 buildSearchKeywords
$r2 = new ReflectionMethod($mgr, 'buildSearchKeywords');
$r2->setAccessible(true);
$kws = $r2->invoke($mgr, $info, $platform['name']);
echo "\n=== buildSearchKeywords 输出 (共 " . count($kws) . ") ===\n";
foreach ($kws as $i => $kw) {
    echo "  [$i] " . json_encode($kw, JSON_UNESCAPED_UNICODE) . "\n";
}
