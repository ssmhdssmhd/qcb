<?php
/**
 * URL 转 JSON 专用入口（v5.13.9）
 *
 * 用途：将网页播放器 URL / VIP 平台视频页 URL / 直链 URL 统一转为结构化 JSON 输出。
 * 与 jiexi.php / xt.php 使用同一套解析引擎 + 缓存，嗅探配置完全复用 xt/sniffer_config.php。
 *
 * 用法示例：
 *   ① 独立入口（推荐）：
 *     url2json.php?url=https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html&type=json
 *     url2json.php?wd=…&type=302    // 302 跳转直链
 *     url2json.php?v=…&type=api     // 影视 CMS 标准 (code=1)
 *     url2json.php?video=…&type=xml // 老盒子 XML
 *     url2json.php?t=…&callback=cb  // JSONP 回调
 *
 *   ② mx.php 路由（兼容老项目）：
 *     mx.php?action=url2json&url=…  (与上面等价，会直接 require 本文件 exit)
 *
 * 返回（type=json 默认）：
 * {
 *   "code": 200,
 *   "ZT": "解析成功",
 *   "msg": "https://资源站/xxx.m3u8",
 *   "url": "https://资源站/xxx.m3u8",
 *   "time": "2.34s",
 *   "KFZ": "超级嗅探|XT",
 *   "info": "URL转JSON专用解析",
 *   "type": "m3u8",              // m3u8 | html_player | mp4 | flv | other
 *   "is_m3u8": true,
 *   "is_html_player": false,
 *   "source": "replace",         // replace | official | cache | direct | unknown
 *   "official_url": "https://jx.xmflv.cc/?url=…",  // concurrent 模式有
 *   "replace_url":  "https://资源站/xxx.m3u8",      // concurrent 模式有
 *   "video_url": "https://v.youku.com/v_show/id_…", // 原始输入 URL
 *   "platform": "youku"          // youku | iqiyi | tencent | mgtv | bilibili | sohu | pptv | unknown | null
 * }
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Range');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// -------- 工具函数 --------
if (!function_exists('u2j_getVideoUrl')) {
    function u2j_getVideoUrl(): string {
        $params = ['url', 'wd', 'v', 'video', 't', 'u', 'play', 'src'];
        foreach ($params as $p) {
            if (isset($_GET[$p]) && ($s = trim((string)$_GET[$p])) !== '') return $s;
        }
        if (isset($_POST['url']) && is_string($_POST['url'])) return trim($_POST['url']);
        return '';
    }
}
if (!function_exists('u2j_getFormat')) {
    function u2j_getFormat(): string {
        $type = isset($_GET['type']) ? strtolower(trim((string)$_GET['type'])) : '';
        $fmt  = isset($_GET['format']) ? strtolower(trim((string)$_GET['format'])) : '';
        if ($type === '302' || $type === 'redirect' || $type === 'raw' || $fmt === 'm3u8') return '302';
        if ($type === 'api' || $type === 'cms') return 'api';
        if ($type === 'xml') return 'xml';
        return 'json';
    }
}
if (!function_exists('u2j_guessPlatform')) {
    function u2j_guessPlatform(?string $url): ?string {
        if (!$url) return null;
        $host = (string)parse_url($url, PHP_URL_HOST);
        if ($host === '') return null;
        $map = [
            'youku.com' => 'youku',
            'iqiyi.com' => 'iqiyi',
            'qiyi.com'  => 'iqiyi',
            'v.qq.com'  => 'tencent',
            'qq.com'    => 'tencent',
            'mgtv.com'  => 'mgtv',
            'bilibili.com' => 'bilibili',
            'sohu.com'  => 'sohu',
            'le.com'    => 'le',
            'pptv.com'  => 'pptv',
        ];
        foreach ($map as $k => $v) {
            if (stripos($host, $k) !== false) return $v;
        }
        return 'unknown';
    }
}
if (!function_exists('u2j_guessUrlType')) {
    function u2j_guessUrlType(?string $url): array {
        if (!$url) return ['type' => 'other', 'is_m3u8' => false, 'is_html_player' => false];
        $low = strtolower($url);
        $host = (string)parse_url($url, PHP_URL_HOST);
        $isHtmlPlayer = false;
        if (stripos($host, 'xmflv.cc') !== false || stripos($host, 'jmflv') !== false ||
            stripos($host, 'jx.xm') === 0 || stripos($host, 'xmplayer') !== false) {
            $isHtmlPlayer = true;
        }
        if ($isHtmlPlayer) {
            return ['type' => 'html_player', 'is_m3u8' => false, 'is_html_player' => true];
        }
        if (strpos($low, '.m3u8') !== false) return ['type' => 'm3u8', 'is_m3u8' => true, 'is_html_player' => false];
        if (strpos($low, '.mp4') !== false)  return ['type' => 'mp4',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.flv') !== false)  return ['type' => 'flv',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.mkv') !== false)  return ['type' => 'mkv',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.ts') !== false)   return ['type' => 'ts',   'is_m3u8' => false, 'is_html_player' => false];
        return ['type' => 'other', 'is_m3u8' => false, 'is_html_player' => false];
    }
}
if (!function_exists('u2j_outputJson')) {
    function u2j_outputJson(array $data, ?string $callback): void {
        if ($callback !== null && $callback !== '' && preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $callback)) {
            header('Content-Type: application/javascript; charset=utf-8');
            echo $callback . '(' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
            return;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
if (!function_exists('u2j_outputError')) {
    function u2j_outputError(string $message, string $format, ?string $callback, array $ctx = []): void {
        $parseTime = (string)($ctx['time'] ?? '0s');
        $kfz = (string)($ctx['KFZ'] ?? '超级嗅探|XT');
        $videoUrl = (string)($ctx['video_url'] ?? '');
        switch ($format) {
            case '302':
                header('Content-Type: text/plain; charset=utf-8');
                echo '解析失败: ' . $message;
                exit;
            case 'api':
                u2j_outputJson([
                    'code' => 0,
                    'msg'  => $message,
                    'url'  => '',
                    'video_url' => $videoUrl,
                ], $callback);
                exit;
            case 'xml':
                header('Content-Type: text/xml; charset=utf-8');
                echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                echo '<result>' . "\n";
                echo '  <code>0</code>' . "\n";
                echo '  <msg>' . htmlspecialchars($message) . '</msg>' . "\n";
                echo '  <url></url>' . "\n";
                if ($videoUrl !== '') echo '  <video_url><![CDATA[' . htmlspecialchars($videoUrl) . ']]></video_url>' . "\n";
                echo '</result>';
                exit;
            default:
                u2j_outputJson([
                    'code' => 400,
                    'ZT'   => $message,
                    'msg'  => $message,
                    'url'  => '',
                    'time' => $parseTime,
                    'KFZ'  => $kfz,
                    'info' => 'URL转JSON专用解析',
                    'video_url' => $videoUrl,
                    'platform' => u2j_guessPlatform($videoUrl ?: null),
                ], $callback);
                exit;
        }
    }
}

// -------- 主流程 --------
$videoUrl = u2j_getVideoUrl();
$format   = u2j_getFormat();
$callback = isset($_GET['callback']) ? trim((string)$_GET['callback']) : null;

if ($videoUrl === '') {
    u2j_outputError('请提供视频链接（支持 url/wd/v/video/t 参数）', $format, $callback);
}
if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    u2j_outputError('链接格式不正确，必须是以 http:// 或 https:// 开头的完整 URL', $format, $callback, ['video_url' => $videoUrl]);
}

require_once __DIR__ . '/xt/server.php';

$result = parseVideo($videoUrl);

$parseTime = (string)($result['time'] ?? '0s');
$kfz = (string)($result['KFZ'] ?? '超级嗅探|XT');
$zt  = (string)($result['ZT'] ?? '');

// v5.13.5-G4 concurrent 双通道
$officialUrl = null;
$replaceUrl  = null;
if (!empty($GLOBALS['XT_CONCURRENT_RESULTS']) && is_array($GLOBALS['XT_CONCURRENT_RESULTS'])) {
    $cr = $GLOBALS['XT_CONCURRENT_RESULTS'];
    if (!empty($cr['official_url'])) $officialUrl = (string)$cr['official_url'];
    if (!empty($cr['replace_url']))  $replaceUrl  = (string)$cr['replace_url'];
}

if (($result['code'] ?? 500) !== 200 || empty($result['url'])) {
    u2j_outputError(($result['msg'] ?? '') ?: '解析失败', $format, $callback, [
        'time' => $parseTime,
        'KFZ'  => $kfz,
        'video_url' => $videoUrl,
    ]);
}

$playUrl = (string)$result['url'];

// source 推断
$source = 'unknown';
if (!empty($result['from_cache'])) $source = 'cache';
elseif ($replaceUrl !== null && $playUrl === $replaceUrl) $source = 'replace';
elseif ($officialUrl !== null && $playUrl === $officialUrl) $source = 'official';
else {
    $t = u2j_guessUrlType($playUrl);
    if ($t['is_html_player']) $source = 'official';
    elseif ($t['is_m3u8'] || in_array($t['type'], ['mp4','flv','mkv','ts'], true)) $source = 'direct';
}

// URL 类型
$ut = u2j_guessUrlType($playUrl);

// enrich（保留 parseVideo 已有字段的优先级）
$enrich = $result;
$enrich['info'] = 'URL转JSON专用解析';
if ($officialUrl !== null && !isset($enrich['official_url'])) $enrich['official_url'] = $officialUrl;
if ($replaceUrl  !== null && !isset($enrich['replace_url']))  $enrich['replace_url']  = $replaceUrl;
$enrich['source'] = $source;
$enrich['type']   = $ut['type'];
$enrich['is_m3u8'] = $ut['is_m3u8'];
$enrich['is_html_player'] = $ut['is_html_player'];
$enrich['video_url'] = $videoUrl;
if (!isset($enrich['platform']) || $enrich['platform'] === null) {
    $enrich['platform'] = u2j_guessPlatform($videoUrl);
}
// 失败 ZT 兜底
if (empty($enrich['ZT'])) $enrich['ZT'] = '解析成功';

// -------- 按 format 输出 --------
switch ($format) {
    case '302':
        header('Location: ' . $playUrl, true, 302);
        exit;

    case 'api':
        $out = [
            'code' => 1,
            'msg'  => $enrich['ZT'] ?: '解析成功',
            'url'  => $playUrl,
        ];
        foreach (['official_url','replace_url','source','type','is_m3u8','is_html_player','video_url','platform','time','KFZ'] as $k) {
            if (isset($enrich[$k])) $out[$k] = $enrich[$k];
        }
        u2j_outputJson($out, $callback);
        exit;

    case 'xml':
        header('Content-Type: text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<result>' . "\n";
        echo '  <code>1</code>' . "\n";
        echo '  <msg>' . htmlspecialchars($enrich['ZT'] ?: '解析成功') . '</msg>' . "\n";
        echo '  <url><![CDATA[' . $playUrl . ']]></url>' . "\n";
        echo '  <video_url><![CDATA[' . htmlspecialchars($videoUrl) . ']]></video_url>' . "\n";
        if (!empty($enrich['official_url'])) echo '  <official_url><![CDATA[' . $enrich['official_url'] . ']]></official_url>' . "\n";
        if (!empty($enrich['replace_url']))  echo '  <replace_url><![CDATA['  . $enrich['replace_url']  . ']]></replace_url>' . "\n";
        echo '  <source>' . htmlspecialchars($enrich['source']) . '</source>' . "\n";
        echo '  <type>' . htmlspecialchars($enrich['type']) . '</type>' . "\n";
        echo '  <is_m3u8>' . ($enrich['is_m3u8'] ? '1' : '0') . '</is_m3u8>' . "\n";
        echo '  <is_html_player>' . ($enrich['is_html_player'] ? '1' : '0') . '</is_html_player>' . "\n";
        if (!empty($enrich['platform'])) echo '  <platform>' . htmlspecialchars($enrich['platform']) . '</platform>' . "\n";
        echo '  <time>' . htmlspecialchars($parseTime) . '</time>' . "\n";
        echo '  <KFZ>' . htmlspecialchars($kfz) . '</KFZ>' . "\n";
        echo '</result>';
        exit;

    default: // json
        // 保持跟 jiexi.php 完全兼容的字段：code/ZT/msg/url/time/KFZ/info + URL2JSON 专有的 enrich 字段
        $out = [
            'code' => 200,
            'ZT'   => $enrich['ZT'],
            'msg'  => $playUrl,
            'url'  => $playUrl,
            'time' => $parseTime,
            'KFZ'  => $kfz,
            'info' => $enrich['info'],
        ];
        foreach (['type','is_m3u8','is_html_player','source','official_url','replace_url','video_url','platform','from_cache'] as $k) {
            if (isset($enrich[$k]) && $enrich[$k] !== null) $out[$k] = $enrich[$k];
        }
        u2j_outputJson($out, $callback);
        exit;
}
