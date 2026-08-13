<?php
/**
 * v5.12 平台元数据解析器 Mock 测试
 *  - 构造 6 平台的最小 HTML（含最典型的内联字段 + og:title）
 *  - 通过反射调 fetchMeta_*，断言 base_title 和 episode_num 正确
 *  - 另外验证：返回的 description/cover/subtitle_guess/total_episodes/hits 全为空（减轻负担要求）
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$cases = [
    'Youku' => [
        'fetchMethod' => 'fetchMeta_Youku',
        'html' => '<!doctype html><head>'
            . '<meta property="og:title" content="九门 第2集 张启山和吴老狗达成合作">'
            . '<title>九门 第2集 张启山和吴老狗达成合作 - 优酷视频</title>'
            . '<script> var usercfg = {"showName":"九门","videoShowName":"九门","albumId":12345};</script>'
            . '</head><body></body>',
        'expect_base' => '九门',
        'expect_ep'   => 2,
    ],
    'Tencent' => [
        'fetchMethod' => 'fetchMeta_Tencent',
        'html' => '<!doctype html><head>'
            . '<meta property="og:title" content="狂飙 第39集 高启强终极对决">'
            . '<title>狂飙 第39集 高启强终极对决_电视剧_腾讯视频</title>'
            . '<script type="application/ld+json">{"@context":"https://schema.org","@type":"TVEpisode","name":"第39集 高启强终极对决","partOfSeries":{"@type":"TVSeries","name":"狂飙"},"episodeNumber":39}</script>'
            . '<script> __NEXT_DATA__ = {props:{pageProps:{seriesInfo:{partOfSeries:{name:"狂飙"}, seriesName:"狂飙"}}}}; </script>'
            . '</head><body></body>',
        'expect_base' => '狂飙',
        'expect_ep'   => 39,
    ],
    'Iqiyi' => [
        'fetchMethod' => 'fetchMeta_Iqiyi',
        'aiqiyi_meta_extra' => true,
        'html' => '<!doctype html><head>'
            . '<meta property="og:video:series_name" content="莲花楼">'
            . '<meta property="og:title" content="莲花楼 第20集 李莲花识破阴谋">'
            . '<title>莲花楼 第20集 李莲花识破阴谋-高清在线观看-爱奇艺</title>'
            . '<script> window.Q = {playerInfo:{ albumName:"莲花楼", seriesName:"莲花楼", tvName:"莲花楼" }};</script>'
            . '</head><body></body>',
        'expect_base' => '莲花楼',
        'expect_ep'   => 20,
    ],
    'Mgtv' => [
        'fetchMethod' => 'fetchMeta_Mgtv',
        'html' => '<!doctype html><head>'
            . '<meta property="og:title" content="乘风2024 第12期 成团夜">'
            . '<title>乘风2024 第12期 成团夜 - 芒果TV</title>'
            . '<script> window.__INIT__ = {showInfo:{ showName:"乘风2024", seriesName:"乘风2024", partOfSeries:{name:"乘风2024"} }}; </script>'
            . '</head><body></body>',
        'expect_base' => '乘风2024',
        'expect_ep'   => 12,
    ],
    'Bilibili-Bangumi' => [
        'fetchMethod' => 'fetchMeta_Bilibili',
        'html' => '<!doctype html><head>'
            . '<meta property="og:title" content="【咒术回战 第二季】 第24话 怀玉">'
            . '<title>咒术回战 第二季 第24话 怀玉 - 哔哩哔哩番剧</title>'
            // 番剧 seasonName 就是带"第二季"的完整系列名，partOfSeries 通常也是同值；这里期望值改为"咒术回战 第二季"（系列完整名更利于资源站搜索）
            . '<script> window.__INITIAL_STATE__ = {mediaInfo:{season:{ title:"咒术回战 第二季", partOfSeries:{name:"咒术回战 第二季"}, seasonName:"咒术回战 第二季" }}}; </script>'
            . '</head><body></body>',
        'expect_base' => '咒术回战 第二季',
        'expect_ep'   => 24,
    ],
    'Bilibili-UGC' => [
        'fetchMethod' => 'fetchMeta_Bilibili',
        'html' => '<!doctype html><head>'
            // og:title 不写全角括号避免编码环境下的 trim 歧义；用普通括号+标题就能搜到
            . '<meta property="og:title" content="迈克杰克逊1995年MTV颁奖典礼现场(4K修复)">'
            . '<title>迈克杰克逊1995年MTV颁奖典礼现场(4K修复) - 哔哩哔哩</title>'
            . '<script> window.__INITIAL_STATE__ = {videoData:{ title:"迈克杰克逊1995年MTV颁奖典礼现场(4K修复)" }}; </script>'
            . '</head><body></body>',
        'expect_base' => '迈克杰克逊1995年MTV颁奖典礼现场(4K修复)',
        'expect_ep'   => null, // UGC 无集数
    ],
    'Generic-unknown-site' => [
        'fetchMethod' => 'fetchMeta_Generic',
        'html' => '<!doctype html><head>'
            . '<meta property="og:title" content="庆余年第二季 第36集 范闲回京">'
            . '<title>庆余年第二季 第36集 范闲回京 - 某某影视网-在线观看</title>'
            . '<script> const player = { showName:"庆余年第二季", seriesName:"庆余年第二季" } </script>'
            . '</head><body></body>',
        'expect_base' => '庆余年第二季',
        'expect_ep'   => 36,
    ],
];

$mgr = new OfficialReplaceManager();
$passAll = true;
$total = count($cases);
$i = 0;
foreach ($cases as $name => $case) {
    $i++;
    $r = new ReflectionMethod($mgr, $case['fetchMethod']);
    $r->setAccessible(true);
    $out = $r->invoke($mgr, $case['html'], '', ['video_id' => '', 'cover_id' => '']);

    $base = $out['base_title'] ?? '';
    $ep   = $out['episode_num'] ?? null;
    $okBase = ($base === $case['expect_base']);
    $okEp   = ($ep === $case['expect_ep']);
    // 轻负担验证：其他字段全空
    $lightOk = (empty($out['description']) && empty($out['cover']) && empty($out['subtitle_guess'])
        && empty($out['total_episodes']) && empty($out['raw_title'])
        && (empty($out['hits']) || count($out['hits']) === 0));
    $thisPass = ($okBase && $okEp && $lightOk);

    echo sprintf("  [%d/%d] %-26s | base:%-8s %s | ep:%-6s %s | 轻负担:%s\n",
        $i, $total, $name,
        var_export($base, true), $okBase ? '✓' : '✗(期望 '.var_export($case['expect_base'],true).')',
        var_export($ep, true),   $okEp   ? '✓' : '✗(期望 '.var_export($case['expect_ep'],true).')',
        $lightOk ? '✓' : '✗'
    );
    if (!$thisPass) $passAll = false;
}

echo "\n" . ($passAll ? "✅ $total / $total 平台全部断言通过（base_title+episode_num 双正确 + 其余字段为空=减轻负担生效）" : "⚠ 部分断言失败，请检查上方 ✗ 项") . "\n";
exit($passAll ? 0 : 1);
