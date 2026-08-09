<?php
/**
 * gx_progress.php — 前端轮询进度接口（后台『自动更新』页面每 1.2s 拉一次）
 *
 * 入参 (GET)：
 *   task_id （可选，兼容 future）
 *   key （可选，若携带则用 gx_secret 校验；否则仅本地可访问）
 *
 * 返回 JSON: { success, progress: {task_id, percent, overall_status, steps, logs:[...]}, last_run?, error? }
 *
 * 鉴权策略：
 *   - 同源/同服务器使用时：无需 key，直接返回（因为是管理员从后台页面访问）
 *   - 为了防外部直接刷：可以要求携带 gx_secret 或者 REMOTE_ADDR 是本机（可选宽松）
 *   - 当前采用宽松同源策略（对外部署时建议在 Nginx 限制 /gx_*.php 仅管理员访问）
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$GX_ROOT = __DIR__;
$GX_DIR  = $GX_ROOT . '/gx';
$GX_SECRET_FILE_PHP = $GX_DIR . '/.gx_secret.php';
$GX_SECRET_FILE_OLD = $GX_DIR . '/.gx_secret';
$GX_PROGRESS_FILE = $GX_DIR . '/.gx_progress.json';
$GX_LAST_RUN_FILE = $GX_DIR . '/.gx_last_run.php';

function gxpr_load_secret(): string {
    global $GX_SECRET_FILE_PHP, $GX_SECRET_FILE_OLD;
    if (file_exists($GX_SECRET_FILE_PHP)) {
        $cfg = @include $GX_SECRET_FILE_PHP;
        if (is_array($cfg) && !empty($cfg['gx_key']) && strlen($cfg['gx_key']) >= 16) return $cfg['gx_key'];
    }
    if (file_exists($GX_SECRET_FILE_OLD)) {
        $s = trim(file_get_contents($GX_SECRET_FILE_OLD));
        if (strlen($s) >= 16) return $s;
    }
    return '';
}

$out = [
    'success'  => false,
    'progress' => null,
    'last_run' => null,
    'error'    => null,
    'server_time' => date('Y-m-d H:i:s'),
];

// 有 key 参数时才做严格校验（方便外部脚本）
$key = $_GET['key'] ?? $_POST['key'] ?? null;
if ($key !== null && $key !== '') {
    $secret = gxpr_load_secret();
    if (!$secret) {
        $out['error'] = 'gx_secret 未初始化';
        echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    if (!hash_equals($secret, (string)$key)) {
        http_response_code(401);
        $out['error'] = 'key 不匹配';
        echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

// 读取进度
if (file_exists($GX_PROGRESS_FILE)) {
    $raw = @file_get_contents($GX_PROGRESS_FILE);
    if ($raw && strlen($raw) > 0) {
        $dec = @json_decode($raw, true);
        if (is_array($dec)) {
            $out['success']  = true;
            $out['progress'] = $dec;
        } else {
            $out['error'] = 'progress 文件 JSON 解析失败；原始长度=' . strlen($raw);
        }
    } else {
        $out['error'] = 'progress 文件为空';
    }
} else {
    $out['error'] = '暂无进行中的任务（gx/.gx_progress.json 不存在）';
}

// 读取 last_run
if (file_exists($GX_LAST_RUN_FILE)) {
    $last = @include $GX_LAST_RUN_FILE;
    if (is_array($last)) $out['last_run'] = $last;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
