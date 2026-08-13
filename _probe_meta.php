<?php
/**
 * mini runner: 只测 fetchVideoInfo / 平台独立解析器 输出，不跑资源站搜索
 */
declare(strict_types=1);

require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$url = $argv[1] ?? 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';

try {
    $mgr = new \OfficialReplaceManager();

    // 拿到 platform
    $platform = null;
    $r = new ReflectionMethod($mgr, 'detectPlatform');
    $r->setAccessible(true);
    $platform = $r->invoke($mgr, $url);
    if (!$platform) {
        echo "detectPlatform FAIL\n"; exit(1);
    }
    echo "platform = ", json_encode($platform, JSON_UNESCAPED_UNICODE), "\n\n";

    $t = microtime(true);
    $r2 = new ReflectionMethod($mgr, 'fetchVideoInfo');
    $r2->setAccessible(true);
    $videoIds = ['video_id' => '', 'cover_id' => ''];
    $info = $r2->invoke($mgr, $url, $platform, $videoIds);
    $elapsed = round((microtime(true) - $t) * 1000, 1);

    echo "fetchVideoInfo elapsed_ms=$elapsed\n";
    echo "title         = ", ($info['title'] ?? ''), "\n";
    echo "description   = ", mb_substr($info['description'] ?? '', 0, 120), "\n";
    echo "episode_info  = ", json_encode($info['episode_info'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), "\n";
    echo "sources_used  = ", json_encode($info['sources_used'] ?? []), "\n";

    // 再拿 step_trace 看看
    $r3 = new ReflectionMethod($mgr, 'getStepTrace');
    $r3->setAccessible(true);
    $trace = $r3->invoke($mgr);
    echo "\n==== step_trace (fetch part) ====\n";
    foreach ($trace as $s) {
        $icon = ['ok'=>'✓','warn'=>'△','fail'=>'✕','info'=>'ℹ'][$s['status']] ?? '·';
        $ms = $s['elapsed_ms'] !== null ? number_format($s['elapsed_ms'],1).'ms' : '';
        echo "  $icon {$s['title']}  $ms\n";
        echo "       summary: {$s['summary']}\n";
        if (!empty($s['detail']) && is_array($s['detail'])) {
            foreach ($s['detail'] as $dk => $dv) {
                if (in_array($dk, ['platform_parser_base_title','platform_parser_ep_num','platform_parser','platform_meta_ok','_platform_picked','hits','inline','keywords','picked','searched_sites','successful_sites','failed_sites','top5'], true)) {
                    echo "       detail[$dk] = " . (is_string($dv) ? $dv : json_encode($dv, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . "\n";
                }
            }
        }
    }
} catch (Throwable $e) {
    echo "EXCEPTION: ", $e->getMessage(), "\n";
    echo $e->getTraceAsString(), "\n";
    exit(1);
}
