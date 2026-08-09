<?php
/**
 * gx_execute.php — 管理员触发自动更新任务（页面按钮点击后调用）
 * 安全：用 gx/.gx_secret 里的 key 做签名（HMAC-SHA256 + 24h 时间戳）
 *     （页面同源即可，后端验证签名防止外部 CSRF 恶意调用）
 *
 * 入参 (POST):
 *   token:    HMAC-SHA256(secret, action|max|force|ts)
 *   ts:       时间戳 (unix) ± 24h 内有效
 *   action:   all|check|migrate|official_refresh|ai_learn|ai_cleanup|site_check|rule_check  （默认 all）
 *   max:      数字 (默认 null，使用 gx.php 各自默认值)
 *   force:    0|1  (默认 0)
 *
 * 返回 JSON: { success, async, pid/task_id, message, ... }
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// 跨域策略：仅同源使用。可以再加 X-Requested-With 判断。
if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] !== '') {
    // 允许同源：HTTP_ORIGIN 与 SERVER_NAME 匹配即可
    $same = false;
    $serverHost = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
    foreach (['http://', 'https://'] as $scheme) {
        if ($_SERVER['HTTP_ORIGIN'] === $scheme . $serverHost) { $same = true; break; }
    }
    if (!$same) {
        http_response_code(403);
        echo json_encode(['success' => false, 'code' => 403, 'message' => '跨域禁止，仅同源可触发'], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 允许的 actions
$ALLOWED_ACTIONS = ['all','check','migrate','official_refresh','ai_learn','ai_cleanup','site_check','rule_check','status','reset_key'];

$GX_ROOT = __DIR__;
$GX_DIR  = $GX_ROOT . '/gx';
$GX_SECRET_FILE_PHP = $GX_DIR . '/.gx_secret.php';
$GX_SECRET_FILE_OLD = $GX_DIR . '/.gx_secret';
$GX_PROGRESS_FILE = $GX_DIR . '/.gx_progress.json';

function gxex_load_secret(): string {
    global $GX_SECRET_FILE_PHP, $GX_SECRET_FILE_OLD;
    if (file_exists($GX_SECRET_FILE_PHP)) {
        $cfg = @include $GX_SECRET_FILE_PHP;
        if (is_array($cfg) && !empty($cfg['gx_key']) && strlen($cfg['gx_key']) >= 16) {
            return $cfg['gx_key'];
        }
    }
    if (file_exists($GX_SECRET_FILE_OLD)) {
        $s = trim(file_get_contents($GX_SECRET_FILE_OLD));
        if (strlen($s) >= 16) return $s;
    }
    return '';
}

function gxex_fail(int $code, string $msg): void {
    if ($code >= 400) @http_response_code($code);
    echo json_encode(['success' => false, 'code' => $code, 'message' => $msg], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    gxex_fail(405, '请使用 POST 请求');
}

if (!file_exists($GX_SECRET_FILE_PHP) && !file_exists($GX_SECRET_FILE_OLD)) {
    // 未初始化密钥：引导用户先运行 gx.php?action=reset_key 或直接访问 gx.php 自动生成
    gxex_fail(412, 'gx_secret 未初始化，请先访问一次 /gx.php 自动生成密钥，或点击重置密钥按钮');
}

$secret = gxex_load_secret();
if (strlen($secret) < 16) {
    gxex_fail(500, 'gx_secret 长度不足，请到 /gx.php?action=reset_key 重置');
}

// 读取 POST（兼容 JSON / form）
$rawBody = file_get_contents('php://input');
$P = [];
if ($rawBody && in_array(($_SERVER['CONTENT_TYPE'] ?? ''), ['application/json','application/json; charset=utf-8','application/json;charset=utf-8'])) {
    $dec = json_decode($rawBody, true);
    if (is_array($dec)) $P = $dec;
} else {
    $P = $_POST;
}

$token  = trim($P['token'] ?? '');
$ts     = intval($P['ts'] ?? 0);
$action = trim($P['action'] ?? 'all');
// max 参数兼容：JSON null / PHP null / 字符串 "null"/"undefined"/空字符串 → 都视为 null；其余转 int
if (!array_key_exists('max', $P) || $P['max'] === null || $P['max'] === '' || $P['max'] === 'null' || $P['max'] === 'undefined') {
    $max = null;
} else {
    $max = intval($P['max']);
}
$force  = !empty($P['force']) && $P['force'] !== '0' && $P['force'] !== 'false';

if (!in_array($action, $ALLOWED_ACTIONS, true)) {
    gxex_fail(400, '非法 action 参数：' . $action);
}
if ($ts <= 0 || abs(time() - $ts) > 86400) {
    gxex_fail(401, '时间戳失效，请刷新页面重试');
}

// 签名校验 —— 必须和前端 mxadmin.js 的 payload 拼接方式完全一致
$payload = $action . '|' . ($max === null ? 'null' : $max) . '|' . ($force ? 1 : 0) . '|' . $ts;
$expected = hash_hmac('sha256', $payload, $secret, false);
if (!hash_equals($expected, strtolower($token))) {
    gxex_fail(401, '签名校验失败，请刷新页面重试');
}

// 构造启动调用：通过内部 require gx.php 的方式也可以，但为了保持“后台长任务”不阻塞 HTTP，
//            这里直接调用 gx.php 的 progress+async 接口（用 key 参数）。
//            并且 gx.php 已经处理了 lock 文件，保证串行。

// 启动任务：优先用 CLI + exec（不依赖 HTTP 自调用，避免单线程服务器死锁）；
// 禁用 exec 时 fallback 到同步 require+执行（会阻塞 HTTP，这是环境限制）。
$asyncPid  = '';
$taskId    = '';
$launchErr = '';
$GX_ROOT   = __DIR__;
$GX_DIR    = $GX_ROOT . '/gx';

// 1) CLI exec 异步启动（强烈推荐方式）
if (function_exists('exec')) {
    $phpBin = PHP_BINARY;
    $script = $GX_ROOT . '/gx.php';
    $args = [$action];
    if ($force) $args[] = 'force';
    if ($max !== null) $args[] = '--max=' . $max;
    $logFile = $GX_DIR . '/gx_run.log';
    $cmd = $phpBin . ' ' . escapeshellarg($script) . ' ' . implode(' ', array_map('escapeshellarg', $args))
         . ' >> ' . escapeshellarg($logFile) . ' 2>&1 & echo $!';
    $out = []; $ret = -1;
    @exec($cmd, $out, $ret);
    $asyncPid = (string)($out[0] ?? '');
    if ($asyncPid !== '' && $ret === 0) {
        // 启动成功：等待最多 1.2s，让子进程写入 progress.json
        $deadline = microtime(true) + 1.2;
        do {
            usleep(100000); // 100ms
            if (file_exists($GX_PROGRESS_FILE)) {
                $raw = @file_get_contents($GX_PROGRESS_FILE);
                $dec = $raw ? @json_decode($raw, true) : null;
                if (is_array($dec) && !empty($dec['task_id'])) {
                    $taskId = (string)$dec['task_id'];
                    break;
                }
            }
        } while (microtime(true) < $deadline);
    } else {
        $launchErr = "exec CLI 启动失败 ret=$ret pid=$asyncPid cmd=$cmd";
    }
}

// 2) exec 禁用/启动失败 → 用 fsockopen 发送异步 HTTP 请求（只发送不等待响应，避免单线程服务器死锁）
if ($asyncPid === '' || $taskId === '') {
    // 预先构建初始 progress：避免 task_id 为空
    $weightMap = [];
    $maxForW = $max ?? 10;
    switch ($action) {
        case 'status':            $weightMap = ['status' => 100]; break;
        case 'reset_key':         $weightMap = ['reset_key' => 100]; break;
        case 'check':             $weightMap = ['check' => 100]; break;
        case 'migrate':           $weightMap = ['migrate' => 100]; break;
        case 'ai_learn':          $weightMap = ['ai_learn' => 100]; break;
        case 'ai_cleanup':        $weightMap = ['ai_cleanup' => 100]; break;
        case 'official_refresh':  $weightMap = ['official_refresh' => 100]; break;
        case 'rule_check':        $weightMap = ['rule_check' => 100]; break;
        case 'site_check':
            $n = max(1, min($maxForW, 20));
            $w = []; for($i=0;$i<$n;$i++) $w["site_{$i}"] = 1;
            $weightMap = array_merge(['site_check_prepare'=>1], $w, ['site_check_summary'=>1]);
            break;
        case 'all':
        default:
            $weightMap = ['check'=>10,'migrate'=>10,'official_refresh'=>25,'ai_learn'=>30,'site_check'=>25];
    }
    // 构造 progress 初始 JSON（写一个 initial 状态，让后续 gx.php 接管）—— 通过 async HTTP 模式启动后 gx.php 自己会覆盖
    $launchErr .= ($launchErr ? '；' : '') . (!function_exists('exec') ? 'exec 禁用' : 'exec 启动失败') . '，fallback 到异步 HTTP(fsockopen) 触发';

    // 构造 gx.php 的调用 URL（含 async=1 + key + action）
    $scheme   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'ssl' : '';
    $hostPart = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
    // 分离 host 和 port（如果 HTTP_HOST 已含端口）
    $port = null;
    if (preg_match('/^(.+):(\d+)$/', $hostPart, $m)) {
        $hostPart = $m[1];
        $port = intval($m[2]);
    } else {
        $p = $_SERVER['SERVER_PORT'] ?? null;
        $port = $p ? intval($p) : ($scheme === 'ssl' ? 443 : 80);
    }
    $uriBase = '';
    if (!empty($_SERVER['REQUEST_URI'])) {
        $d = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
        if ($d !== '.' && $d !== '/') $uriBase = $d;
    }
    $queryParams = [
        'key'    => $secret,
        'action' => $action,
        'async'  => 1,
        'force'  => $force ? 1 : 0,
    ];
    if ($max !== null) $queryParams['max'] = $max;
    $path = $uriBase . '/gx.php?' . http_build_query($queryParams);

    // fsockopen 异步发送请求（只发送不读响应，避免单线程服务器死锁）
    $ok = false;
    $errno = 0; $errstr = '';
    $fsockHost = ($scheme === 'ssl' ? 'ssl://' : '') . $hostPart;
    $fp = @fsockopen($fsockHost, $port, $errno, $errstr, 3);
    if ($fp) {
        stream_set_timeout($fp, 2);
        $request = "GET {$path} HTTP/1.1\r\n";
        $request .= "Host: " . ($_SERVER['HTTP_HOST'] ?? ($hostPart . ':' . $port)) . "\r\n";
        $request .= "User-Agent: GxExecuteFallback/1.0\r\n";
        $request .= "Connection: Close\r\n\r\n";
        $written = @fwrite($fp, $request);
        $ok = ($written !== false);
        // 立即关闭，不等待响应
        @fclose($fp);
    }
    if (!$ok) {
        $launchErr .= '；异步 HTTP 发送失败 fsockopen err=' . ($errstr ?: "$errno");
    } else {
        // 等待 1.5s，让 gx.php 在当前请求结束后尽快写入 progress（尽管它会排队）
        usleep(500000);
        if (file_exists($GX_PROGRESS_FILE)) {
            $raw = @file_get_contents($GX_PROGRESS_FILE);
            $dec = $raw ? @json_decode($raw, true) : null;
            if (is_array($dec) && !empty($dec['task_id'])) {
                $taskId = (string)$dec['task_id'];
            }
        }
    }
}

// 构造响应
$resp = [
    'success'  => true,
    'async'    => ($asyncPid !== ''),
    'pid'      => $asyncPid ?: null,
    'task_id'  => $taskId ?: null,
    'message'  => $taskId ? '任务已启动，通过 progress 轮询获取进度' : '任务提交，请稍后刷新进度查看',
    'progress_file' => 'gx/.gx_progress.json',
];
if ($launchErr !== '') {
    $resp['warning'] = $launchErr;
}
// 追加当前 progress（立即有数据，前端不必等下一次轮询）
if (file_exists($GX_PROGRESS_FILE)) {
    $pRaw = @file_get_contents($GX_PROGRESS_FILE);
    $pDec = $pRaw ? @json_decode($pRaw, true) : null;
    if (is_array($pDec)) $resp['progress'] = $pDec;
}
echo json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
