<?php
/**
 * 超级嗅探 - 对外 API 接口
 *
 * 功能：接收前端视频链接，调用本地 server.php 进行解析，返回视频播放地址
 * 用法：api.php?url=VIDEO_URL
 */

// 设置返回 JSON 格式
header('Content-Type: application/json; charset=utf-8');

// 检查是否提供了 URL 参数
if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    echo json_encode(['code' => 400, 'msg' => '请提供需要解析的链接'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 获取需要解析的链接
$video_url = trim($_GET['url']);

// 校验 URL 格式
if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['code' => 400, 'msg' => '链接格式不正确'], JSON_UNESCAPED_UNICODE);
    exit;
}

// ============ 直接调用本地 server.php（PHP 版本，不再依赖 Node.js） ============
// 自动获取当前协议和主机
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// 获取 server.php 所在目录
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$serverPhpPath = rtrim($scriptDir, '/') . '/server.php';

$target_url = $protocol . '://' . $host . $serverPhpPath . '?url=' . urlencode($video_url);

// 使用 cURL 调用本地解析服务
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $target_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// 检查是否成功获取内容
if ($error) {
    echo json_encode(['code' => 500, 'msg' => '解析服务请求失败: ' . $error], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($httpCode !== 200) {
    $errorMsg = $response ?: '未知错误';
    echo json_encode(['code' => $httpCode, 'msg' => '解析失败: ' . $errorMsg], JSON_UNESCAPED_UNICODE);
    exit;
}

// 直接透传 server.php 的 JSON 响应
echo $response;