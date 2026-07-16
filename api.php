<?php
/**
 * 超级嗅探 - 对外 API 接口
 * 
 * 功能：接收前端视频链接，调用本地 server.php 进行解析，返回视频播放地址
 * 
 * 用法：api.php?url=VIDEO_URL
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    echo json_encode(['code' => 400, 'msg' => '请提供需要解析的链接'], JSON_UNESCAPED_UNICODE);
    exit;
}

$video_url = trim($_GET['url']);

if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
    echo json_encode(['code' => 400, 'msg' => '链接格式不正确'], JSON_UNESCAPED_UNICODE);
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$serverPhpPath = rtrim($scriptDir, '/') . '/server.php';

$target_url = $protocol . '://' . $host . $serverPhpPath . '?url=' . urlencode($video_url);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $target_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode(['code' => 500, 'msg' => '解析服务请求失败: ' . $error], JSON_UNESCAPED_UNICODE);
    exit;
}

echo $response;