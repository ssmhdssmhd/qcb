<?php
/**
 * 超级嗅探 - 统一 API 入口
 *
 * 支持多端调用：
 *   1. App / 网页 / 电视   → api.php?url=视频链接              返回精简 JSON
 *   2. 影视 JSON 解析接口   → api.php?url=视频链接&type=api     返回影视标准 JSON
 *   3. 播放器直接调用       → api.php?url=视频链接&type=raw     302跳转到播放地址
 *   4. 服务端调用           → api.php?url=视频链接&type=json    返回精简 JSON（默认）
 *
 * 返回 JSON 结构：
 * {
 *   "code": 200,          // 状态码：200成功 / 400参数错误 / 500解析失败
 *   "ZT": "解析成功",      // 状态文本：解析成功 / 解析失败
 *   "msg": "解析成功",     // 提示信息
 *   "url": "http://...",  // 去广告后的播放地址
 *   "time": "1.23s",      // 耗时
 *   "KFZ": "超级嗅探|XT"   // 开发者信息
 * }
 *
 * 影视标准格式（type=api）：
 * {
 *   "code": 1,            // 1=成功 0=失败
 *   "url": "http://...",  // 播放地址
 *   "msg": "解析成功"
 * }
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// 支持POST和GET
$videoUrl = trim($_GET['url'] ?? $_POST['url'] ?? '');
$outputType = trim($_GET['type'] ?? $_POST['type'] ?? 'json');

// 参数校验
if (empty($videoUrl)) {
    outputResult([
        'code' => 400,
        'ZT'   => '解析失败',
        'msg'  => '请提供需要解析的链接',
        'url'  => '',
        'time' => '0s',
        'KFZ'  => '超级嗅探|XT',
    ], $outputType);
}

// 加载服务端核心
require_once __DIR__ . '/server.php';

// 执行解析
$result = parseVideo($videoUrl);

// 输出结果
outputResult($result, $outputType);


/**
 * 根据类型输出结果
 *
 * @param array  $result  解析结果
 * @param string $type    输出类型：json / api / raw
 */
function outputResult(array $result, string $type): void
{
    switch ($type) {
        case 'raw':
            // 302 跳转，供播放器直接调用
            if ($result['code'] === 200 && !empty($result['url'])) {
                header('Location: ' . $result['url']);
                http_response_code(302);
            } else {
                http_response_code(500);
                echo $result['msg'];
            }
            exit;

        case 'api':
            // 影视 JSON 解析标准格式
            echo json_encode([
                'code' => $result['code'] === 200 ? 1 : 0,
                'url'  => $result['url'],
                'msg'  => $result['msg'],
            ], JSON_UNESCAPED_UNICODE);
            exit;

        case 'json':
        default:
            // 精简 JSON（默认，支持 App / 网页 / 电视 / 服务端调用）
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
    }
}
