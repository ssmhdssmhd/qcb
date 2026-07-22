<?php
/**
 * 影视解析接口 - 专为 TV Box / 影视 App 打造
 *
 * 支持：TVBox、影视仓、TV影院、喵影视、影迷大院等各种影视APP
 * 兼容：解析 JSON / 302跳转 / JSONP / XML 多种返回格式
 *
 * 用法：
 *   jiexi.php?url=视频链接         (默认 JSON 格式)
 *   jiexi.php?wd=视频链接          (兼容 wd 参数)
 *   jiexi.php?v=视频链接           (兼容 v 参数)
 *   jiexi.php?video=视频链接       (兼容 video 参数)
 *
 *   可选参数:
 *     - type=json       默认，标准 JSON
 *     - type=302        302 跳转直连，播放器直接用
 *     - type=api        影视CMS标准格式 (code=1)
 *     - type=xml        XML 格式（老盒子用）
 *     - callback=xxx    JSONP 回调
 *     - t=xxx           别名 url
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Range');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$videoUrl = getVideoUrl();
$format = getFormat();
$callback = isset($_GET['callback']) ? trim($_GET['callback']) : null;

if (empty($videoUrl)) {
    outputError('请提供视频链接', $format, $callback);
    exit;
}

if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    outputError('链接格式不正确', $format, $callback);
    exit;
}

require_once __DIR__ . '/xt/server.php';

$result = parseVideo($videoUrl);

if ($result['code'] !== 200 || empty($result['url'])) {
    outputError($result['msg'] ?: '解析失败', $format, $callback);
    exit;
}

outputSuccess($result['url'], $format, $callback);

/**
 * 获取视频链接参数，兼容多种参数名
 */
function getVideoUrl(): string
{
    $params = ['url', 'wd', 'v', 'video', 't', 'u', 'play', 'src'];
    foreach ($params as $p) {
        if (isset($_GET[$p]) && !empty(trim($_GET[$p]))) {
            return trim($_GET[$p]);
        }
    }
    if (isset($_POST['url'])) {
        return trim($_POST['url']);
    }
    return '';
}

/**
 * 获取返回格式
 */
function getFormat(): string
{
    $type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
    $format = isset($_GET['format']) ? strtolower(trim($_GET['format'])) : '';

    if ($type === '302' || $type === 'redirect' || $type === 'raw' || $format === 'm3u8') {
        return '302';
    }
    if ($type === 'api' || $type === 'cms') {
        return 'api';
    }
    if ($type === 'xml') {
        return 'xml';
    }
    return 'json';
}

/**
 * 输出成功结果
 */
function outputSuccess(string $playUrl, string $format, ?string $callback): void
{
    switch ($format) {
        case '302':
            header('Location: ' . $playUrl, true, 302);
            exit;

        case 'api':
            $data = [
                'code' => 1,
                'msg'  => '解析成功',
                'url'  => $playUrl,
            ];
            outputJson($data, $callback);
            break;

        case 'xml':
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<result>' . "\n";
            echo '  <code>1</code>' . "\n";
            echo '  <msg>解析成功</msg>' . "\n";
            echo '  <url><![CDATA[' . $playUrl . ']]></url>' . "\n";
            echo '</result>';
            break;

        default:
            $data = [
                'code' => 200,
                'msg'  => $playUrl,
                'url'  => $playUrl,
                'info' => 'TVBox影视专用解析',
            ];
            outputJson($data, $callback);
    }
}

/**
 * 输出错误结果
 */
function outputError(string $message, string $format, ?string $callback): void
{
    switch ($format) {
        case '302':
            header('Content-Type: text/plain; charset=utf-8');
            echo $message;
            exit;

        case 'api':
            $data = [
                'code' => 0,
                'msg'  => $message,
                'url'  => '',
            ];
            outputJson($data, $callback);
            break;

        case 'xml':
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<result>' . "\n";
            echo '  <code>0</code>' . "\n";
            echo '  <msg>' . htmlspecialchars($message) . '</msg>' . "\n";
            echo '  <url></url>' . "\n";
            echo '</result>';
            break;

        default:
            $data = [
                'code' => 400,
                'msg'  => $message,
                'url'  => '',
            ];
            outputJson($data, $callback);
    }
}

/**
 * 输出 JSON（支持 JSONP）
 */
function outputJson(array $data, ?string $callback): void
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    if ($callback && preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $callback)) {
        header('Content-Type: application/javascript; charset=utf-8');
        echo $callback . '(' . $json . ')';
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo $json;
    }
}
