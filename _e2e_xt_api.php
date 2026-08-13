<?php
// 端到端测试：模拟 xt/api.php 请求，检查 step_trace 透传
$_GET = [
    'url' => 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html',
    '_t'  => (string)time(),
];
$cwd = getcwd();
chdir(__DIR__ . '/xt');
ob_start();
include __DIR__ . '/xt/api.php';
$out = ob_get_clean();
chdir($cwd);

$data = json_decode($out, true);
if (!is_array($data)) {
    echo "NON-JSON (len=" . strlen($out) . "):\n" . substr($out, 0, 800) . "\n";
    exit(1);
}
echo "code={$data['code']}  ZT={$data['ZT']}\n";
echo "msg=" . mb_substr($data['msg'] ?? '', 0, 120) . "\n";
echo "channel=" . ($data['channel'] ?? '(n/a)') . "  source=" . ($data['source'] ?? '(n/a)') . "\n";
echo "platform=" . ($data['platform'] ?? '') . "  base_title=" . ($data['base_title'] ?? '') . "  ep=" . ($data['episode_num'] ?? 'null') . "\n";
$st = $data['step_trace'] ?? [];
echo "step_trace.count=" . count($st) . "\n";
foreach (array_slice($st, 0, 7) as $i => $s) {
    $em = $s['elapsed_ms'] ?? '-';
    if (is_numeric($em)) $em = number_format($em, 1) . 'ms';
    echo sprintf("  [%d] %-6s | %-10s | %-30s | %s\n",
        $i + 1,
        $s['status'] ?? '?',
        $em,
        mb_substr($s['title'] ?? '(untitled)', 0, 30),
        mb_substr($s['summary'] ?? '', 0, 80)
    );
}
if (count($st) > 7) echo "  ... 还有 " . (count($st) - 7) . " 步\n";
if (!empty($data['debug_info'])) {
    echo "debug_info=" . json_encode($data['debug_info'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
}
if (!empty($data['url'])) {
    echo "url_head=" . substr($data['url'], 0, 200) . (strlen($data['url']) > 200 ? ' ...' : '') . "\n";
}
