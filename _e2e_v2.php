<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/gz/OfficialReplaceManager.php';

// 1) 先直接验证 OfficialReplaceManager->resolve(优酷链接)
echo "==== 1) OfficialReplaceManager::resolve ====\n";
$t = microtime(true);
try {
    $mgr = new OfficialReplaceManager();
    $r = $mgr->resolve('https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html');
    $elapsed = round((microtime(true) - $t) * 1000, 1);
    echo "elapsed_ms={$elapsed}\n";
    echo "success=" . ($r['success'] ? 'true' : 'false') . "\n";
    echo "message=" . mb_substr($r['message'] ?? '', 0, 120) . "\n";
    echo "platform=" . ($r['platform'] ?? '') . "  video_title=" . ($r['video_title'] ?? '') . "\n";
    echo "base_title=" . ($r['base_title'] ?? '') . "  ep=" . ($r['episode_num'] ?? 'null') . "\n";
    echo "search_keywords=" . json_encode($r['search_keywords'] ?? [], JSON_UNESCAPED_UNICODE) . "\n";
    $st = $r['step_trace'] ?? [];
    echo "step_trace.count=" . count($st) . "\n";
    foreach (array_slice($st, 0, 8) as $i => $s) {
        $em = $s['elapsed_ms'] ?? '-'; if (is_numeric($em)) $em = number_format($em, 1) . 'ms';
        echo sprintf("  [%d] %-6s | %-10s | %-30s | %s\n",
            $i+1, $s['status'] ?? '?', $em,
            mb_substr($s['title'] ?? '(untitled)', 0, 30),
            mb_substr($s['summary'] ?? '', 0, 70));
    }
    if (count($st) > 8) echo "  ... 还有 ".(count($st)-8)." 步\n";
} catch (Throwable $e) {
    echo "EXCEPTION: " . get_class($e) . ": " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// 2) 再验证 parseVideo（本地官替直调是否生效）
echo "\n==== 2) xt/server.php parseVideo（本地官替直调）====\n";
$config = require __DIR__ . '/xt/config.php';
global $config;
// 强制模式=官替，且官替接口URL为空（触发我们新加的 replace_direct 分支）
$config['sniffer']['mode'] = 'replace';
$config['sniffer']['replace_api']['enabled'] = true;
$config['sniffer']['replace_api']['url'] = '';
// 先手动定义 callOfficialReplaceDirectV2（因为 require server.php 前，jiami_core 可能不加载，或者可能加载。这里直接 require server.php）
require_once __DIR__ . '/xt/server.php';
$t2 = microtime(true);
$res = parseVideo('https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html');
$elapsed2 = round((microtime(true) - $t2) * 1000, 1);
echo "elapsed_ms={$elapsed2}\n";
echo "code={$res['code']}  ZT={$res['ZT']}\n";
echo "msg=" . mb_substr($res['msg'] ?? '', 0, 120) . "\n";
echo "channel=" . ($res['channel'] ?? '(n/a)') . "  source=" . ($res['source'] ?? '(n/a)') . "\n";
echo "platform=" . ($res['platform'] ?? '') . "  base_title=" . ($res['base_title'] ?? '') . "  ep=" . ($res['episode_num'] ?? 'null') . "\n";
$st2 = $res['step_trace'] ?? [];
echo "step_trace.count=" . count($st2) . "\n";
foreach (array_slice($st2, 0, 8) as $i => $s) {
    $em = $s['elapsed_ms'] ?? '-'; if (is_numeric($em)) $em = number_format($em, 1) . 'ms';
    echo sprintf("  [%d] %-6s | %-10s | %-30s | %s\n",
        $i+1, $s['status'] ?? '?', $em,
        mb_substr($s['title'] ?? '(untitled)', 0, 30),
        mb_substr($s['summary'] ?? '', 0, 70));
}
if (count($st2) > 8) echo "  ... 还有 ".(count($st2)-8)." 步\n";
