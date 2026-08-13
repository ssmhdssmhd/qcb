<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';
$mgr = new OfficialReplaceManager();
$rDetect = new ReflectionMethod($mgr, 'detectPlatform');
$rDetect->setAccessible(true);
$platform = $rDetect->invoke($mgr, $url);

$r1 = new ReflectionMethod($mgr, 'fetchVideoInfo');
$r1->setAccessible(true);
$videoInfo = $r1->invoke($mgr, $url, $platform, ['video_id' => '', 'cover_id' => '']);

// 仿 resolve() 字段迁移
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

$baseTitle = $videoInfo['base_title'] ?? '';
$episodeNum = $videoInfo['episode_num'] ?? null;
$episodeSubtitle = $videoInfo['episode_subtitle'] ?? '';
echo "baseTitle=$baseTitle\n";
echo "episodeNum="; var_dump($episodeNum);
echo "episodeSubtitle=$episodeSubtitle\n";
echo "(!empty(\$baseTitle))=" . (!empty($baseTitle) ? 'true' : 'false') . "\n";
echo "(\$episodeNum) 的布尔 = " . ($episodeNum ? 'true' : 'false') . "\n";

if ($episodeNum) {
    $nospace = $baseTitle . '第' . $episodeNum . '集' . $episodeSubtitle;
    echo " 分支 有集数 nospace=$nospace\n";
} else {
    $nospace = (mb_strlen($baseTitle) <= 3) ? ($baseTitle . $episodeSubtitle) : '';
    echo " 分支 无集数 nospace=$nospace (mb_strlen(baseTitle)=" . mb_strlen($baseTitle) . ")\n";
}
