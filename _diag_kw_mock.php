<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
require_once __DIR__ . '/gz/OfficialReplaceManager.php';

$mgr = new OfficialReplaceManager();
$r2 = new ReflectionMethod($mgr, 'buildSearchKeywords');
$r2->setAccessible(true);

// 构造模拟的 videoInfo：base_title=九门, ep=2, subtitle=张启山和吴老狗达成合作
$videoInfo = [
    'base_title'       => '九门',
    'episode_num'      => 2,
    'episode'          => '第2集 张启山和吴老狗达成合作',
    'episode_subtitle' => '张启山和吴老狗达成合作',
    'title'            => '九门 第2集 张启山和吴老狗达成合作',
];
$kws = $r2->invoke($mgr, $videoInfo, '优酷');

echo "=== 纯 mock 测试 buildSearchKeywords（优酷九门E02） ===\n";
foreach ($kws as $i => $kw) {
    echo "  [$i] " . json_encode($kw, JSON_UNESCAPED_UNICODE) . "\n";
}
echo "\n=== 断言 ===\n";
$pass = true;
foreach ($kws as $kw) {
    if (strpos($kw, '张启山和吴老狗达成合作 第2集 张启山') !== false
        || substr_count($kw, '张启山和吴老狗达成合作') >= 2) {
        echo "FAIL: 搜索词 '$kw' 副标题重复拼接\n"; $pass = false;
    }
    if (preg_match('/门第[^0-9第·\-\s]/u', $kw)) {
        echo "FAIL: 搜索词 '$kw' 存在畸形 '门第X' 垃圾\n"; $pass = false;
    }
}
$expected = ["九门 第2集 张启山和吴老狗达成合作", "九门 第2集", "九门"];
foreach ($expected as $e) {
    if (in_array($e, $kws, true)) echo "PASS: 搜索词「$e」存在\n";
    else { echo "FAIL: 核心搜索词「$e」缺失（列表：" . implode("、", $kws) . "）\n"; $pass = false; }
}
echo "\n最终：" . ($pass ? "✅ 全部断言通过" : "⚠ 存在断言失败（见上方）") . "\n";
