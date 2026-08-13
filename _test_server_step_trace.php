<?php
/**
 * v5.12 端到端验证脚本
 *  1) 调 xt/server.php 的 parseVideo(优酷URL)，校验是否走本地官替直调（replace_direct）
 *  2) 输出 step_trace 数量 + 前几步，确认透传成功
 *  3) 校验 base_title 是否为「九门」（平台独立解析器结果）
 */
chdir(__DIR__);
require_once __DIR__ . '/xt/server.php';

// 载入配置（parseVideo 里会用到 global $config）
$config = require __DIR__ . '/xt/config.php';
global $config;

$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';
$t   = microtime(true);
$res = parseVideo($url);
$elapsed = round((microtime(true) - $t) * 1000, 1);

echo "==== parseVideo($url) ====\n";
echo "total_elapsed_ms={$elapsed}ms\n";
echo "code={$res['code']}  ZT={$res['ZT']}  msg=".mb_substr($res['msg'] ?? '', 0, 80)."\n";
echo "time={$res['time']}\n";
echo "channel=" . ($res['channel'] ?? '(n/a)') . "  source=" . ($res['source'] ?? '(n/a)') . "\n";
echo "video_title="  . ($res['video_title']  ?? '') . "\n";
echo "base_title="   . ($res['base_title']   ?? '') . "\n";
echo "episode_num="  . ($res['episode_num']  ?? 'null') . "\n";
echo "platform="     . ($res['platform']     ?? '') . "\n";
echo "site="         . ($res['site']         ?? '') . "  match_score=" . ($res['match_score'] ?? '-') . "\n";
echo "url_head="     . substr($res['url'] ?? '', 0, 120) . (strlen($res['url'] ?? '')>120?' ...':'') . "\n";

$st = $res['step_trace'] ?? [];
echo "\nstep_trace.count=" . count($st) . "\n";
if (count($st) > 0) {
    echo "--- step_trace 前 6 项 ---\n";
    foreach (array_slice($st, 0, 6) as $i => $s) {
        $em = $s['elapsed_ms'] ?? '-';
        if (is_numeric($em)) $em = number_format($em, 1) . 'ms';
        echo sprintf("  [%d] %-6s | %-10s | %s | %s\n",
            $i+1,
            $s['status'] ?? '?',
            $em,
            $s['title'] ?? '(untitled)',
            mb_substr($s['summary'] ?? '', 0, 60)
        );
    }
    if (count($st) > 6) echo "  ... 还有 " . (count($st) - 6) . " 步\n";
} else {
    echo "  ❌ 没有 step_trace（可能未启用官替直调或解析未进入官替通道）\n";
}

if (!empty($res['debug_info'])) {
    echo "\ndebug_info: " . json_encode($res['debug_info'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
}

echo "\n--- 断言检查 ---\n";
$ok = true;
if (($res['channel'] ?? '') !== 'replace_direct') { echo "FAIL: 未走 replace_direct 通道\n"; $ok=false; }
else echo "PASS: 走 replace_direct 本地官替快速通道\n";

if (!empty($res['base_title'])) {
    if ($res['base_title'] === '九门') echo "PASS: base_title=九门（平台独立解析器提取成功）\n";
    else { echo "FAIL: base_title={$res['base_title']}（期望 '九门'，优酷解析器未命中？）\n"; $ok=false; }
} else echo "WARN: base_title 未设置（官替可能还没搜索这步）\n";

if (!empty($res['episode_num']) && $res['episode_num'] == 2) echo "PASS: episode_num=2（集数提取正确）\n";
else echo "INFO: episode_num=" . ($res['episode_num'] ?? 'null') . "（资源站未命中也可能为空）\n";

if (count($st) >= 5) echo "PASS: step_trace 包含 " . count($st) . " 步（>=5，步骤链路完整）\n";
else { echo "FAIL: step_trace 只有 " . count($st) . " 步（链路不完整）\n"; $ok=false; }

echo "\n最终结果: " . ($ok ? "✅ ALL PASS" : "⚠ SOME CHECKS FAILED (详见上方)") . "\n";
