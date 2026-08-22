<?php
/**
 * CLI 验证脚本 —— 框架骨架可运行验证（离线确定性）
 *
 * 用法：
 *      php parse/tests/cli_verify.php
 *      php parse/tests/cli_verify.php m3u8=/path/to/ad.m3u8
 *      php parse/tests/cli_verify.php facade=https://v.qq.com/x/page/xxx.html
 *
 * 退出码：0=全部通过；1=存在失败。
 *
 * @package parse
 * @since   5.13.9
 */

require_once __DIR__ . '/../autoload.php';

$fixture = __DIR__ . '/fixtures/ad.m3u8';
$cliArgs = [];
foreach ($argv as $a) {
    if (strpos($a, '=') !== false) {
        list($k, $v) = explode('=', $a, 2);
        $cliArgs[$k] = $v;
    }
}
if (!empty($cliArgs['m3u8'])) {
    $fixture = $cliArgs['m3u8'];
}

$fail = 0;
$pass = 0;
$check = function ($cond, $name) use (&$fail, &$pass) {
    if ($cond) {
        $pass++;
        echo "  [PASS] {$name}\n";
    } else {
        $fail++;
        echo "  [FAIL] {$name}\n";
    }
};

echo "══════════════════════════════════════════════════════\n";
echo "  parse 模块 —— 框架骨架可运行验证\n";
echo "══════════════════════════════════════════════════════\n\n";

echo "── 1) 类加载自检 ──────────────────────────────────\n";
foreach (['Timer', 'UrlClassifier', 'ParseResult', 'LocalM3u8Cleaner', 'ResourceFirstResolver', 'ParserFacade'] as $cls) {
    $check(class_exists($cls), "class {$cls} 已加载");
}

echo "\n── 2) Timer 预算 ──────────────────────────────────\n";
$timer = new Timer(0.05);
usleep(1000 * 30);
$check($timer->ok(), "0.05s 预算内 30ms 后 ok()=true");
usleep(1000 * 60);
$check(!$timer->ok(), "超过预算后 ok()=false");
$to = $timer->timeoutResult('测试超时');
$check(!empty($to['timed_out']), "timeoutResult 标记 timed_out");

echo "\n── 3) UrlClassifier 分类 ─────────────────────────\n";
$clf = new UrlClassifier($GLOBALS['_PARSE_CFG']);
$check($clf->classify('https://v.qq.com/x/page/a11.html')['type'] === 'official', '腾讯官方页 → official');
$check($clf->classify('https://cdn.com/a/index.m3u8')['type'] === 'm3u8', '直链 m3u8 → m3u8');
$check($clf->classify('https://cdn.com/a/movie.mp4')['type'] === 'other', 'mp4 → other');

echo "\n── 4) LocalM3u8Cleaner 去广告（离线）─────────────\n";
$cleaner = new LocalM3u8Cleaner($GLOBALS['_PARSE_CFG']);
$raw = file_get_contents($fixture);
$clean = $cleaner->clean($raw);
$check(!empty($clean['success']), 'clean 成功');
$check($clean['total_count'] === 9, '总片段数=9（实际 ' . $clean['total_count'] . '）');
$check($clean['ad_count'] >= 4, '广告段≥4（实际 ' . $clean['ad_count'] . '）');
$check($clean['placeholder_count'] === $clean['ad_count'], '广告段全部占位');
$check(strpos($clean['playlist'], '__PLACEHOLDER__') !== false
    || strpos($clean['playlist'], 'placeholder_ts') !== false, '占位 URI 已写入 playlist');
// “不删段”守恒：输出中每个“数据行”（非 # / // 注释、非空行）恰对应一个片段
$dataRows = array_values(array_filter(preg_split('/\r\n|\r|\n/', $clean['playlist']), function ($l) {
    $t = trim($l);
    return $t !== '' && strpos($t, '#') !== 0 && strpos($t, '//') !== 0;
}));
$check(count($dataRows) === $clean['total_count'], '输出数据行数=片段总数（不删段）');
echo "      片段样例：\n";
foreach (array_slice($clean['segments'], 0, 3) as $i => $seg) {
    echo "        #{$i} is_ad=" . ($seg['is_ad'] ? 'true' : 'false') . " uri=" . $seg['uri'] . "\n";
}

echo "\n── 5) ParserFacade 门面（m3u8 链路）──────────────\n";
$facade = new ParserFacade();
$r = $facade->parse($fixture);
$check($r->channel === 'm3u8_clean', "m3u8 文件 → channel=m3u8_clean（实际 {$r->channel}）");
$check($r->success, 'facade 解析成功');

echo "\n── 6) ParserFacade 门面（官方链路，骨架降级）────\n";
$ro = $facade->parse('https://v.qq.com/x/page/a11.html');
$check($ro->channel === 'official_replace', "官方页 → channel=official_replace（实际 {$ro->channel}）");
echo "      提示：" . $ro->message . "\n";

echo "\n══ 结果汇总 ══════════════════════════════════════\n";
echo "  PASS={$pass}  FAIL={$fail}\n";
echo ($fail === 0 ? "  全部通过 ✔\n" : "  存在失败 ✘\n");
exit($fail === 0 ? 0 : 1);