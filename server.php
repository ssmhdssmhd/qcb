<?php
/**
 * 超级嗅探 - PHP 版本服务端
 * 
 * 功能：通过解析服务嗅探视频播放地址（.m3u8 / .mp4）
 * 支持：腾讯视频、爱奇艺、优酷、芒果TV 等主流平台
 * 
 * 自动适配国内外服务器：
 *   方案零：平台官方API直连（国内IP最快）
 *   方案零B：通过公共代理转发API（海外IP自动回退）
 *   方案一：第三方JSON解析接口
 *   方案二：第三方HTML解析接口
 *   方案三：Chrome Headless 嗅探
 * 
 * 用法：server.php?url=VIDEO_URL
 *       server.php?url=VIDEO_URL&debug=1  (调试模式)
 */

// 设置响应头
header('Content-Type: application/json; charset=utf-8');

// 调试模式：server.php?url=xxx&debug=1
$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
$debugLog = [];

// 检查是否提供了 URL 参数
if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'URL parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$videoUrl = trim($_GET['url']);

// 校验 URL 格式
if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'Invalid URL format'], JSON_UNESCAPED_UNICODE);
    exit;
}

$videoLink = null;

// ============ 方案零：直接调用平台官方 API（国内IP推荐） ============
$videoLink = extractVideoByDirectApi($videoUrl, $debugLog);
if ($debug) {
    $debugLog[] = '方案零(官方API): ' . ($videoLink ? '成功' : '失败');
}

// ============ 方案零B：通过代理转发调用官方API（海外IP自动回退） ============
if (!$videoLink) {
    $videoLink = extractVideoByProxyApi($videoUrl, $debugLog);
    if ($debug) {
        $debugLog[] = '方案零B(代理转发API): ' . ($videoLink ? '成功' : '失败');
    }
}

// ============ 方案一：第三方 JSON 解析接口 ============
if (!$videoLink) {
    $videoLink = extractVideoByJsonApi($videoUrl, $debugLog);
    if ($debug) {
        $debugLog[] = '方案一(第三方JSON): ' . ($videoLink ? '成功' : '失败');
    }
}

// ============ 方案二：第三方 HTML 解析接口 ============
if (!$videoLink) {
    $videoLink = extractVideoByHtmlApi($videoUrl, $debugLog);
    if ($debug) {
        $debugLog[] = '方案二(第三方HTML): ' . ($videoLink ? '成功' : '失败');
    }
}

// ============ 方案三：Chrome Headless 嗅探 ============
if (!$videoLink && isChromeAvailable()) {
    $videoLink = extractVideoByChromeHeadless($videoUrl);
    if ($debug) {
        $debugLog[] = '方案三(Chrome): ' . ($videoLink ? '成功' : '失败');
    }
}

// 返回结果
if ($videoLink) {
    echo json_encode(['code' => 200, 'url' => $videoLink], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    $response = ['code' => 500, 'message' => 'Failed to extract video URL'];
    if ($debug) {
        $response['debug'] = $debugLog;
        $response['php_version'] = PHP_VERSION;
        $response['curl_loaded'] = extension_loaded('curl');
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

exit;


// ============================================================
//  方案零：平台官方 API 直连
// ============================================================

/**
 * 方案零：直接调用平台官方 API 获取视频播放地址
 * 适用于国内服务器 IP
 * 
 * @param string $videoUrl 视频页面URL
 * @param array $debugLog 调试日志
 * @return string|null 视频直链或null
 */
function extractVideoByDirectApi(string $videoUrl, array &$debugLog = []): ?string
{
    $host = parse_url($videoUrl, PHP_URL_HOST) ?? '';

    // 腾讯视频
    if (preg_match('/v\.qq\.com/i', $host)) {
        $debugLog[] = '检测到腾讯视频，调用官方API';
        return extractTencentVideo($videoUrl, $debugLog, false);
    }

    // 爱奇艺
    if (preg_match('/iqiyi\.com/i', $host)) {
        $debugLog[] = '检测到爱奇艺，调用官方API';
        return extractIqiyiVideo($videoUrl);
    }

    // 优酷
    if (preg_match('/youku\.com/i', $host)) {
        $debugLog[] = '检测到优酷，调用官方API';
        return extractYoukuVideo($videoUrl);
    }

    // 芒果TV
    if (preg_match('/mgtv\.com/i', $host)) {
        $debugLog[] = '检测到芒果TV，调用官方API';
        return extractMgtvVideo($videoUrl);
    }

    $debugLog[] = '未匹配到已知平台，跳过官方API';
    return null;
}


// ============================================================
//  方案零B：通过公共代理转发调用官方API
// ============================================================

/**
 * 方案零B：通过公共 CORS 代理转发 API 请求
 * 当服务器在海外（em=80）时，通过国内代理访问腾讯 API
 * 
 * @param string $videoUrl 视频页面URL
 * @param array $debugLog 调试日志
 * @return string|null 视频直链或null
 */
function extractVideoByProxyApi(string $videoUrl, array &$debugLog = []): ?string
{
    $host = parse_url($videoUrl, PHP_URL_HOST) ?? '';

    // 目前只有腾讯视频需要代理（em=80 地域限制）
    if (!preg_match('/v\.qq\.com/i', $host)) {
        return null;
    }

    $debugLog[] = '代理转发: 腾讯视频通过代理重试';
    return extractTencentVideo($videoUrl, $debugLog, true);
}


// ============================================================
//  腾讯视频解析（支持直连+代理两种模式）
// ============================================================

/**
 * 腾讯视频 API 解析
 * 
 * @param string $videoUrl 腾讯视频页面URL
 * @param array $debugLog 调试日志
 * @param bool $useProxy 是否使用代理
 * @return string|null 视频直链或null
 */
function extractTencentVideo(string $videoUrl, array &$debugLog = [], bool $useProxy = false): ?string
{
    // 从 URL 中提取视频 ID
    $vid = null;
    if (preg_match('/\/x\/cover\/\w+\/(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/vid=(\w+)/i', $videoUrl, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/\/x\/page\/(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }

    if (!$vid) {
        $debugLog[] = '腾讯: 未提取到视频ID';
        return null;
    }
    $debugLog[] = "腾讯: 视频ID={$vid}" . ($useProxy ? ' [代理模式]' : '');

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $mobileUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1';
    $guid = str_pad((string)mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999), 32, '0', STR_PAD_LEFT);

    // API 端点配置
    $defnList = ['shd', 'fhd', 'hd', 'sd'];
    $apiHosts = [
        ['host' => 'https://vv.video.qq.com',  'ehost' => 'https://v.qq.com',  'ua' => $ua,        'name' => 'PC端'],
        ['host' => 'https://h5vv.video.qq.com', 'ehost' => 'https://m.v.qq.com', 'ua' => $mobileUa, 'name' => 'H5端'],
    ];

    // 公共 CORS 代理列表（代理模式下使用）
    $corsProxies = [
        function($url) { return 'https://api.allorigins.win/raw?url=' . urlencode($url); },
        function($url) { return 'https://corsproxy.io/?url=' . urlencode($url); },
        function($url) { return 'https://proxy.cors.sh/' . $url; },
    ];

    $data = null;
    foreach ($apiHosts as $apiInfo) {
        foreach ($defnList as $defn) {
            $apiUrl = "{$apiInfo['host']}/getinfo?vids={$vid}&platform=101001&charge=0&otype=json&defn={$defn}&guid={$guid}&ehost=" . urlencode($apiInfo['ehost']);

            if ($useProxy) {
                // 代理模式：通过 CORS 代理转发
                foreach ($corsProxies as $proxyIdx => $proxyFn) {
                    $proxiedUrl = $proxyFn($apiUrl);
                    $resp = curlGet($proxiedUrl, [
                        'ua' => $apiInfo['ua'],
                        'referer' => $apiInfo['ehost'] . '/',
                        'timeout' => 15,
                    ]);
                    if ($resp) {
                        $resp = preg_replace('/^QZOutputJson=/', '', $resp);
                        $resp = rtrim($resp, ';');
                        $data = json_decode($resp, true);
                        $em = $data['em'] ?? 'null';
                        $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} [代理{$proxyIdx}] em={$em}";
                        if ($data && ($data['em'] ?? 1) === 0 && isset($data['vl']['vi'][0])) {
                            $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} [代理] 成功";
                            break 3;
                        }
                        $data = null;
                    }
                }
            } else {
                // 直连模式
                $resp = curlGet($apiUrl, [
                    'ua' => $apiInfo['ua'],
                    'referer' => $apiInfo['ehost'] . '/',
                    'timeout' => 10,
                ]);
                if (!$resp) {
                    continue;
                }
                $resp = preg_replace('/^QZOutputJson=/', '', $resp);
                $resp = rtrim($resp, ';');
                $data = json_decode($resp, true);
                $em = $data['em'] ?? 'null';
                $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} em={$em}";
                if ($data && ($data['em'] ?? 1) === 0 && isset($data['vl']['vi'][0])) {
                    $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} 成功获取视频信息";
                    break 2;
                }
                $data = null;
            }
        }
    }

    if (!$data || !isset($data['vl']['vi'][0])) {
        if (!$useProxy) {
            $debugLog[] = '腾讯: 直连失败（可能是海外IP限制，将尝试代理）';
        } else {
            $debugLog[] = '腾讯: 代理模式也失败';
        }
        return null;
    }

    $vi = $data['vl']['vi'][0];
    $fn = $vi['fn'] ?? '';
    $servers = $vi['ul']['ui'] ?? [];
    $fvkey = $vi['fvkey'] ?? '';

    if (!$fn || empty($servers)) {
        $debugLog[] = '腾讯: 未获取到文件名或服务器列表';
        return null;
    }
    $debugLog[] = "腾讯: 文件名={$fn} 服务器数=" . count($servers) . " fvkey=" . (empty($fvkey) ? '空' : '已获取');

    // 优先使用 getinfo 返回的 fvkey
    $vkey = $fvkey;
    if (!$vkey) {
        $debugLog[] = '腾讯: fvkey为空，尝试调用getkey';
        $format = '2';
        if (preg_match('/\.f(\d+)\.mp4$/i', $fn, $m)) {
            $format = $m[1];
        }
        $keyUrl = "https://vv.video.qq.com/getkey?format={$format}&otype=json&vid={$vid}&guid={$guid}&filename={$fn}&platform=101001";
        $resp2 = curlGet($keyUrl, ['ua' => $ua, 'referer' => 'https://v.qq.com/', 'timeout' => 10]);
        if ($resp2) {
            $resp2 = preg_replace('/^QZOutputJson=/', '', $resp2);
            $resp2 = rtrim($resp2, ';');
            $data2 = json_decode($resp2, true);
            if ($data2 && isset($data2['key']) && ($data2['s'] ?? '') === 'o') {
                $vkey = $data2['key'];
                $debugLog[] = '腾讯: getkey成功';
            }
        }
    }

    if (!$vkey) {
        $debugLog[] = '腾讯: 无法获取vkey';
        return null;
    }

    // 构建视频直链
    foreach ($servers as $i => $server) {
        $serverUrl = $server['url'] ?? '';
        if (!$serverUrl) {
            continue;
        }
        $videoLink = $serverUrl . $fn . '?vkey=' . $vkey;
        $debugLog[] = "腾讯: CDN[{$i}] " . parse_url($videoLink, PHP_URL_HOST);
        return $videoLink;
    }

    $debugLog[] = '腾讯: 无可用CDN服务器';
    return null;
}


// ============================================================
//  爱奇艺 / 优酷 / 芒果TV 解析
// ============================================================

/**
 * 爱奇艺视频直接 API 解析
 */
function extractIqiyiVideo(string $videoUrl): ?string
{
    $vid = null;
    if (preg_match('/iqiyi\.com\/.*?(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $apiUrl = "https://pcw-api.iqiyi.com/video/video/baseinfo/" . urlencode($vid);
    $resp = curlGet($apiUrl, ['referer' => 'https://www.iqiyi.com/', 'timeout' => 10]);
    if (!$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    $playUrl = $data['data']['playUrl'] ?? null;
    if ($playUrl && filter_var($playUrl, FILTER_VALIDATE_URL)) {
        return $playUrl;
    }
    return null;
}

/**
 * 优酷视频直接 API 解析
 */
function extractYoukuVideo(string $videoUrl): ?string
{
    $vid = null;
    if (preg_match('/id_([a-zA-Z0-9=]+)/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $apiUrl = "https://ups.youku.com/ups/get.json?vid={$vid}&ccode=0502&client_ip=0.0.0.0&utid=0&client_ts=" . time();
    $resp = curlGet($apiUrl, ['referer' => 'https://v.youku.com/', 'timeout' => 10]);
    if (!$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    $ups = $data['data']['stream'] ?? [];
    foreach ($ups as $stream) {
        $m3u8Url = $stream['m3u8_url'] ?? '';
        if ($m3u8Url && filter_var($m3u8Url, FILTER_VALIDATE_URL)) {
            return $m3u8Url;
        }
        $mp4Url = $stream['mp4_url'] ?? '';
        if ($mp4Url && filter_var($mp4Url, FILTER_VALIDATE_URL)) {
            return $mp4Url;
        }
    }
    return null;
}

/**
 * 芒果TV视频直接 API 解析
 */
function extractMgtvVideo(string $videoUrl): ?string
{
    $vid = null;
    if (preg_match('/\/b\/(\d+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $apiUrl = "https://pcweb.api.mgtv.com/player/video?video_id={$vid}";
    $resp = curlGet($apiUrl, ['referer' => 'https://www.mgtv.com/', 'timeout' => 10]);
    if (!$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    $playUrl = $data['data']['info']['play_url'] ?? null;
    if ($playUrl && filter_var($playUrl, FILTER_VALIDATE_URL)) {
        return $playUrl;
    }

    $streams = $data['data']['stream'] ?? [];
    foreach ($streams as $stream) {
        $url = $stream['url'] ?? '';
        if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
    }
    return null;
}


// ============================================================
//  方案一：第三方 JSON 解析接口
// ============================================================

/**
 * 方案一：通过返回 JSON 的第三方解析接口获取视频地址
 * 
 * @param string $videoUrl 视频页面URL
 * @param array $debugLog 调试日志
 * @return string|null 视频直链或null
 */
function extractVideoByJsonApi(string $videoUrl, array &$debugLog = []): ?string
{
    $encodedUrl = urlencode($videoUrl);

    // JSON 直接返回的解析接口列表
    $jsonApis = [
        "https://jx.xmflv.com/?url={$encodedUrl}&type=json",
        "https://jx.xmflv.com/api?url={$encodedUrl}",
        "https://yparse.ik9.cc/index.php?url={$encodedUrl}&type=json",
        "https://jx.parwix.com:4433/player/analysis.php?v={$encodedUrl}",
    ];

    foreach ($jsonApis as $idx => $apiUrl) {
        $result = fetchJsonApi($apiUrl);
        if ($result) {
            $debugLog[] = "JSON接口[{$idx}] 成功: " . substr($result, 0, 60);
            return $result;
        }
        $debugLog[] = "JSON接口[{$idx}] 失败";
    }

    return null;
}

/**
 * 请求 JSON 格式的解析接口
 */
function fetchJsonApi(string $apiUrl): ?string
{
    $resp = curlGet($apiUrl, [
        'timeout' => 10,
        'headers' => ['Accept: application/json'],
    ]);

    if (!$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    if (!$data) {
        return null;
    }

    // 尝试多种 JSON 字段名
    $urlFields = ['url', 'video', 'src', 'play', 'm3u8', 'mp4', 'data'];
    foreach ($urlFields as $field) {
        $val = $data[$field] ?? null;
        if (is_string($val) && preg_match('/\.(m3u8|mp4)(\?|$)/i', $val)) {
            return $val;
        }
    }

    // 嵌套 data 对象
    $inner = $data['data'] ?? null;
    if (is_array($inner)) {
        foreach ($urlFields as $field) {
            $val = $inner[$field] ?? null;
            if (is_string($val) && preg_match('/\.(m3u8|mp4)(\?|$)/i', $val)) {
                return $val;
            }
        }
    }

    return null;
}


// ============================================================
//  方案二：第三方 HTML 解析接口
// ============================================================

/**
 * 方案二：通过 HTML 解析页面提取视频地址
 * 
 * @param string $videoUrl 视频页面URL
 * @param array $debugLog 调试日志
 * @return string|null 视频直链或null
 */
function extractVideoByHtmlApi(string $videoUrl, array &$debugLog = []): ?string
{
    $encodedUrl = urlencode($videoUrl);

    // HTML 解析接口列表
    $htmlApis = [
        'https://jx.xmflv.com/?url=',
        'https://jx.bozrc.com:4433/player/?url=',
        'https://jx.m3u8.tv/jiexi/?url=',
        'https://jx.parwix.com:4433/player/?url=',
        'https://jx.jsonplayer.com/player/?url=',
    ];

    foreach ($htmlApis as $idx => $api) {
        $targetUrl = $api . $encodedUrl;
        $result = fetchParsePage($targetUrl);
        if ($result['videoLink']) {
            $debugLog[] = "HTML接口[{$idx}] 成功: " . substr($result['videoLink'], 0, 60);
            return $result['videoLink'];
        }
        $debugLog[] = "HTML接口[{$idx}] 失败";
    }

    return null;
}

/**
 * 通过 cURL 抓取解析页面并提取视频链接
 */
function fetchParsePage(string $targetUrl): array
{
    $html = curlGet($targetUrl, ['timeout' => 15, 'encoding' => true]);

    if (!$html) {
        return ['videoLink' => null, 'effectiveUrl' => ''];
    }

    return [
        'videoLink'    => extractVideoFromHtml($html),
        'effectiveUrl' => $targetUrl,
    ];
}

/**
 * 从 HTML 内容中提取视频链接（支持多种格式）
 */
function extractVideoFromHtml(string $html): ?string
{
    // 模式1：<video> 或 <source> 标签中的 src
    $patterns = [
        '/<video[^>]+src=[\'"](https?:\/\/[^\'"]+\.m3u8[^\'"]*)[\'"][^>]*>/i',
        '/<video[^>]+src=[\'"](https?:\/\/[^\'"]+\.mp4[^\'"]*)[\'"][^>]*>/i',
        '/<source[^>]+src=[\'"](https?:\/\/[^\'"]+\.m3u8[^\'"]*)[\'"][^>]*>/i',
        '/<source[^>]+src=[\'"](https?:\/\/[^\'"]+\.mp4[^\'"]*)[\'"][^>]*>/i',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            return html_entity_decode($matches[1]);
        }
    }

    // 模式2：JavaScript 变量赋值中的视频地址
    $jsPatterns = [
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.m3u8[^\'"`\s]*)[\'"`]/i',
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.mp4[^\'"`\s]*)[\'"`]/i',
    ];
    foreach ($jsPatterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            return html_entity_decode($matches[1]);
        }
    }

    // 模式3：通用匹配页面中所有 .m3u8 和 .mp4 链接
    if (preg_match_all('/https?:\/\/[^\s\'"<>\\\)\\\\]+?\.(?:m3u8|mp4)(?:[^\s\'"<>\\\)\\\\]*)?/i', $html, $allMatches)) {
        foreach ($allMatches[0] as $match) {
            if (preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|ttf)/i', $match)) {
                continue;
            }
            if (preg_match('/(video|media|play|stream|cdn|m3u8|mp4|vod)/i', $match)) {
                return rtrim($match, '\\');
            }
        }
        if (!empty($allMatches[0][0])) {
            return rtrim($allMatches[0][0], '\\');
        }
    }

    // 模式4：JSON 格式中的视频地址
    if (preg_match('/["\'](https?:\/\/[^"\']+\.m3u8[^"\']*)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1]);
    }
    if (preg_match('/["\'](https?:\/\/[^"\']+\.mp4[^"\']*)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1]);
    }

    return null;
}


// ============================================================
//  方案三：Chrome Headless 嗅探
// ============================================================

/**
 * 方案三：通过 Chrome Headless 嗅探视频地址
 * 需要服务器安装 Chrome/Chromium
 */
function extractVideoByChromeHeadless(string $videoUrl): ?string
{
    $parseUrl = 'https://jx.xmflv.com/?url=' . urlencode($videoUrl);
    $tempDir = sys_get_temp_dir();

    $outputFile = $tempDir . '/video_sniff_' . uniqid() . '.log';
    $harFile = $tempDir . '/video_sniff_' . uniqid() . '.har';

    $chromeCmd = sprintf(
        'chromium-browser --headless --no-sandbox --disable-gpu --disable-setuid-sandbox ' .
        '--user-agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36" ' .
        '--enable-logging --v=1 --log-path=%s --dump-dom --har=%s "%s" 2>/dev/null',
        escapeshellarg($outputFile),
        escapeshellarg($harFile),
        escapeshellarg($parseUrl)
    );

    exec($chromeCmd, $output, $returnCode);

    $videoLink = null;

    // 从 HAR 文件中提取
    if (file_exists($harFile)) {
        $harData = json_decode(file_get_contents($harFile), true);
        if ($harData && isset($harData['log']['entries'])) {
            foreach ($harData['log']['entries'] as $entry) {
                $reqUrl = $entry['request']['url'] ?? '';
                if (preg_match('/\.(m3u8|mp4)(\?|$)/i', $reqUrl)) {
                    $videoLink = $reqUrl;
                    break;
                }
            }
        }
        @unlink($harFile);
    }

    // 从 DOM 中提取
    if (!$videoLink && file_exists($outputFile)) {
        $domContent = file_get_contents($outputFile);
        $videoLink = extractVideoFromHtml($domContent);
        @unlink($outputFile);
    }

    return $videoLink;
}

/**
 * 检测 Chrome/Chromium 是否可用
 */
function isChromeAvailable(): bool
{
    if (!function_exists('shell_exec') || !function_exists('exec')) {
        return false;
    }

    $chromePaths = ['chromium-browser', 'chromium', 'google-chrome', 'google-chrome-stable'];
    foreach ($chromePaths as $cmd) {
        $check = @shell_exec(sprintf('which %s 2>/dev/null', escapeshellcmd($cmd)));
        if (!empty($check)) {
            return true;
        }
    }
    return false;
}


// ============================================================
//  通用工具函数
// ============================================================

/**
 * 统一的 cURL GET 请求封装
 * 
 * @param string $url 请求地址
 * @param array $options 选项 [ua, referer, timeout, headers, encoding]
 * @return string|null 响应内容或null
 */
function curlGet(string $url, array $options = []): ?string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $options['ua'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_REFERER        => $options['referer'] ?? $url,
        CURLOPT_TIMEOUT        => $options['timeout'] ?? 15,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);

    if (!empty($options['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
    }
    if (!empty($options['encoding'])) {
        curl_setopt($ch, CURLOPT_ENCODING, '');
    }

    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);

    if (!$resp || $httpCode !== 200) {
        return null;
    }

    return $resp;
}
