<?php
/**
 * 图片视频统一解析接口 (img.php)
 *
 * 集成多种视频解析能力，提供统一的调用入口
 *
 * 调用方式:
 * 1. 直接解析: img.php?url=视频链接
 * 2. 指定类型: img.php?type=xiami&url=视频链接
 * 3. 获取详情: img.php?action=info&url=视频链接
 * 4. 接口列表: img.php?action=list
 *
 * 支持的解析类型:
 * - mxjx      去广告解析 (M3U8 去广告)
 * - xiami     虾米解析 (全网 VIP 视频)
 * - moxi      沫兮解析 (官方视频替换)
 * - official  官方替换 (智能匹配资源站)
 * - parse     智能解析 (自动判断类型)
 */

@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    ob_end_flush();
    exit;
}

$rootDir = __DIR__;

function img_send_response($data, $code = 200) {
    http_response_code($code);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function img_format_time() {
    $weekMap = ['日', '一', '二', '三', '四', '五', '六'];
    return date('Y') . '年' . date('m') . '月' . date('d') . '日 星期' . $weekMap[date('w')] . ' ' . date('H:i:s');
}

function img_get_self_url() {
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $basePath = dirname($requestUri);
    $basePath = $basePath === '/' ? '' : $basePath;
    return $scheme . '://' . $host . $basePath;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$type = $_GET['type'] ?? $_POST['type'] ?? 'parse';
$url = $_GET['url'] ?? $_POST['url'] ?? '';

if ($action === 'list' || $action === 'help') {
    $selfUrl = img_get_self_url();
    img_send_response([
        'code' => 200,
        'msg' => '图片视频统一解析接口',
        'name' => 'IMG API - 统一解析接口',
        'version' => 'v1.0.0',
        'time' => img_format_time(),
        'base_url' => $selfUrl . '/img.php',
        'usage' => [
            '直接解析' => 'img.php?url=视频链接',
            '指定类型' => 'img.php?type=xiami&url=视频链接',
            '获取详情' => 'img.php?action=info&url=视频链接',
            '接口列表' => 'img.php?action=list',
        ],
        'supported_types' => [
            [
                'type' => 'parse',
                'name' => '智能解析',
                'desc' => '自动判断视频类型，选择最佳解析方式',
                'params' => ['url' => '视频链接（必填）'],
                'example' => 'img.php?type=parse&url=https://v.youku.com/v_show/id_xxx.html',
            ],
            [
                'type' => 'mxjx',
                'name' => '去广告解析',
                'desc' => 'M3U8 视频去广告，自动识别并移除广告片段',
                'params' => ['url' => 'M3U8 视频链接（必填）'],
                'example' => 'img.php?type=mxjx&url=https://example.com/video.m3u8',
            ],
            [
                'type' => 'xiami',
                'name' => '虾米解析',
                'desc' => '全网 VIP 视频解析，支持腾讯、爱奇艺、优酷、芒果TV等',
                'params' => ['url' => '视频播放页链接（必填）'],
                'example' => 'img.php?type=xiami&url=https://v.youku.com/v_show/id_xxx.html',
            ],
            [
                'type' => 'moxi',
                'name' => '沫兮解析',
                'desc' => '沫兮 API 解析，支持官方视频智能替换',
                'params' => ['url' => '视频链接（必填）'],
                'example' => 'img.php?type=moxi&url=https://v.qq.com/x/cover/xxx.html',
            ],
            [
                'type' => 'official',
                'name' => '官方替换',
                'desc' => '官方视频链接智能替换，自动匹配资源站无广告源',
                'params' => ['url' => '官方视频链接（必填）'],
                'example' => 'img.php?type=official&url=https://www.iqiyi.com/v_xxx.html',
            ],
        ],
        'response_format' => [
            'code' => '状态码，200=成功',
            'msg' => '状态信息',
            'url' => '解析后的播放地址',
            'type' => '解析类型',
            'name' => '视频名称（部分接口支持）',
            'episode' => '集数信息（部分接口支持）',
            'time' => '解析时间',
        ],
    ]);
}

if (empty($url)) {
    img_send_response([
        'code' => 400,
        'msg' => '缺少 url 参数',
        'url' => '',
        'type' => $type,
        'time' => img_format_time(),
        'tip' => '访问 img.php?action=list 查看支持的解析类型',
    ], 400);
}

$selfUrl = img_get_self_url();
$mxPhpUrl = $selfUrl . '/mx.php?action=';

$officialDomains = [
    'v.qq.com',
    'iqiyi.com',
    'youku.com',
    'mgtv.com',
    'bilibili.com',
    'sohu.com',
    'pptv.com',
];

$parsedUrl = parse_url($url);
$urlHost = $parsedUrl['host'] ?? '';
$isOfficialUrl = false;

foreach ($officialDomains as $domain) {
    if (strpos($urlHost, $domain) !== false) {
        $isOfficialUrl = true;
        break;
    }
}

$isM3u8Url = false;
$path = $parsedUrl['path'] ?? '';
if (stripos($path, '.m3u8') !== false) {
    $isM3u8Url = true;
}

if ($type === 'parse' || $type === 'auto' || $type === '智能') {
    if ($isM3u8Url) {
        $type = 'mxjx';
    } elseif ($isOfficialUrl) {
        $type = 'xiami';
    } else {
        $type = 'mxjx';
    }
}

function img_http_get($url, $timeout = 30) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept: application/json, */*',
        ],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    if ($response === false) {
        return ['success' => false, 'error' => $error];
    }
    return ['success' => true, 'data' => $response, 'http_code' => $httpCode];
}

$result = null;
$playUrl = '';
$videoName = '';
$episode = '';
$msg = '';
$code = 200;

switch ($type) {
    case 'mxjx':
    case 'adskip':
    case '去广告':
        $apiUrl = $mxPhpUrl . 'mxjx/info&url=' . urlencode($url);
        $resp = img_http_get($apiUrl);
        if ($resp['success']) {
            $data = json_decode($resp['data'], true);
            if (!empty($data['success']) && !empty($data['data'])) {
                $playUrl = $data['data']['play_url'] ?? '';
                $videoName = $data['data']['media_url'] ?? '';
                $stats = $data['data']['stats'] ?? [];
                $msg = '去广告解析成功，已移除 ' . ($stats['removed_segments'] ?? 0) . ' 个广告片段';
            } else {
                $code = 500;
                $msg = $data['message'] ?? '解析失败';
            }
        } else {
            $playUrl = $mxPhpUrl . 'mxjx&url=' . urlencode($url);
            $msg = '去广告解析';
        }
        $typeName = '去广告解析';
        break;

    case 'xiami':
    case '虾米':
    case '虾米解析':
        $apiUrl = $mxPhpUrl . 'xiami_jx/info&url=' . urlencode($url);
        $resp = img_http_get($apiUrl);
        if ($resp['success']) {
            $data = json_decode($resp['data'], true);
            if (!empty($data['success']) && !empty($data['data'])) {
                $playUrl = $data['data']['play_url'] ?? '';
                $videoName = $data['data']['video_name'] ?? '';
                $msg = '虾米解析成功';
            } elseif (!empty($data['code']) && $data['code'] == 200 && !empty($data['data'])) {
                $playUrl = $data['data']['url'] ?? '';
                $msg = '虾米解析成功';
            } else {
                $code = 500;
                $msg = $data['message'] ?? $data['msg'] ?? '解析失败';
            }
        } else {
            $code = 500;
            $msg = '虾米解析接口调用失败';
        }
        $typeName = '虾米解析';
        break;

    case 'moxi':
    case '沫兮':
    case '沫兮解析':
        $apiUrl = $mxPhpUrl . 'moxi/api&url=' . urlencode($url);
        $resp = img_http_get($apiUrl);
        if ($resp['success']) {
            $data = json_decode($resp['data'], true);
            if (!empty($data['code']) && $data['code'] == 200) {
                $playUrl = $data['url'] ?? '';
                $videoName = $data['jm'] ?? '';
                $episode = $data['js'] ?? '';
                $msg = $data['msg'] ?? '沫兮解析成功';
            } else {
                $code = 500;
                $msg = $data['msg'] ?? '解析失败';
            }
        } else {
            $code = 500;
            $msg = '沫兮解析接口调用失败';
        }
        $typeName = '沫兮解析';
        break;

    case 'official':
    case '官替':
    case '官方替换':
        $apiUrl = $mxPhpUrl . 'official_replace/info&url=' . urlencode($url);
        $resp = img_http_get($apiUrl);
        if ($resp['success']) {
            $data = json_decode($resp['data'], true);
            if (!empty($data['success'])) {
                $playUrl = $data['play_url'] ?? '';
                $videoName = $data['video_title'] ?? '';
                $episode = $data['episode'] ?? '';
                $msg = '官方替换成功';
            } else {
                $code = 500;
                $msg = $data['message'] ?? '未找到匹配资源';
            }
        } else {
            $code = 500;
            $msg = '官方替换接口调用失败';
        }
        $typeName = '官方替换';
        break;

    default:
        $code = 400;
        $msg = '不支持的解析类型: ' . $type;
        $typeName = '未知类型';
        break;
}

if ($action === 'info' || $action === 'detail') {
    $response = [
        'code' => $code,
        'msg' => $msg,
        'type' => $type,
        'type_name' => $typeName,
        'original_url' => $url,
        'play_url' => $playUrl,
        'video_name' => $videoName,
        'episode' => $episode,
        'is_official' => $isOfficialUrl,
        'is_m3u8' => $isM3u8Url,
        'time' => img_format_time(),
    ];
    img_send_response($response, $code);
}

$response = [
    [
        'code' => $code,
        'msg' => $msg,
        'type' => $type,
        'name' => $typeName,
        'url' => $playUrl,
        'video_name' => $videoName,
        'episode' => $episode,
        'time' => img_format_time(),
    ],
];

img_send_response($response, $code);
