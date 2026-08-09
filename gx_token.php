<?php
/**
 * gx_token.php — 为后台自动更新按钮生成签名启动 Token（服务端生成，避免客户端HMAC兼容问题）
 *
 * 为什么需要这个文件：
 *   现代浏览器要求 crypto.subtle (Web Crypto API) 必须在【安全上下文】(HTTPS 或 localhost) 下才能使用。
 *   很多用户用 HTTP + IP 访问后台（如 http://114.134.184.91:9002/mxadmin.php），此时 crypto.subtle 不可用，
 *   手写的纯 JS HMAC-SHA256 又容易出 bug（与 PHP hash_hmac 不一致）。
 *   所以改为服务端生成签名 token，再回传给前端，100% 与后端校验逻辑对齐。
 *
 * 入参 (GET/POST):
 *   action: all|check|migrate|official_refresh|...
 *   max:    数字 或 'null' / 空
 *   force:  1|0
 *
 * 返回 (JSON):
 *   { ok:true,  data:{ token, ts, action, max, force } }
 *   { ok:false, error:'...' }
 *
 * 安全：
 *   - 同源即可（后台页面与本文件同域），相当于把签名能力放在同源服务器上。
 *   - 若担心第三方站点直接调用本接口，可以在此处增加 admin session 校验（需根据 mxadmin.php 的实际鉴权方式补充）。
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

$GX_ROOT = __DIR__;
$GX_DIR  = $GX_ROOT . '/gx';
$GX_SECRET_FILE_PHP = $GX_DIR . '/.gx_secret.php';
$GX_SECRET_FILE_OLD = $GX_DIR . '/.gx_secret';

function gxtok_fail(int $code, string $msg) {
    http_response_code($code >= 400 ? $code : 200);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

function gxtok_load_secret(): string {
    global $GX_SECRET_FILE_PHP, $GX_SECRET_FILE_OLD;
    if (file_exists($GX_SECRET_FILE_PHP)) {
        $cfg = @include $GX_SECRET_FILE_PHP;
        if (is_array($cfg) && !empty($cfg['gx_key']) && is_string($cfg['gx_key'])) {
            return trim($cfg['gx_key']);
        }
    }
    if (file_exists($GX_SECRET_FILE_OLD)) {
        $s = @file_get_contents($GX_SECRET_FILE_OLD);
        if (is_string($s)) return trim($s);
    }
    return '';
}

// 1) 读取密钥
$secret = gxtok_load_secret();
if (strlen($secret) < 16) {
    gxtok_fail(412, 'gx_secret 未初始化或长度不足，请先访问一次 /gx.php 自动生成密钥，或点击重置密钥按钮');
}

// 2) 读取入参（兼容 GET / POST / JSON）
$RAW = file_get_contents('php://input');
$P = [];
if (!empty($RAW) && $RAW[0] === '{') {
    $j = json_decode($RAW, true);
    if (is_array($j)) $P = $j;
}
if (empty($P)) {
    if (!empty($_POST)) $P = array_merge($P, $_POST);
    if (!empty($_GET))  $P = array_merge($P, $_GET);
}

$action = isset($P['action']) ? (string)$P['action'] : 'all';
$force  = !empty($P['force']) && in_array((string)$P['force'], ['1','true','yes','on'], true);

$maxRaw = $P['max'] ?? null;
if ($maxRaw === null || $maxRaw === '' || $maxRaw === 'null' || $maxRaw === 'undefined') {
    $max = null;
} else {
    $max = intval($maxRaw);
    if ($max <= 0) $max = null;
}

// 3) 允许的 action 白名单（与 gx_execute.php 保持一致）
$ALLOWED = ['all','check','migrate','official_refresh','ai_learn','ai_cleanup','site_check','rule_check','status','reset_key'];
if (!in_array($action, $ALLOWED, true)) {
    gxtok_fail(400, '非法 action 参数');
}

// 4) 生成签名 —— 必须与 gx_execute.php 的 payload 拼接方式、签名算法 100% 一致
$ts = time();
$payload = $action . '|' . ($max === null ? 'null' : $max) . '|' . ($force ? 1 : 0) . '|' . $ts;
$token   = hash_hmac('sha256', $payload, $secret, false);

echo json_encode([
    'ok'   => true,
    'data' => [
        'token'  => $token,
        'ts'     => $ts,
        'action' => $action,
        'max'    => $max,   // 注意：这里是 int|null，与前端 gxBuildSignedPayload 返回一致
        'force'  => $force ? 1 : 0,
    ],
], JSON_UNESCAPED_UNICODE);
