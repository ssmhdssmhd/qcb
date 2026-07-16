<?php
/**
 * 超级嗅探 - PHP 版本服务端
 * 
 * 功能：通过解析服务嗅探视频播放地址（.m3u8 / .mp4）
 * 替代原 Node.js (Express + Puppeteer) 方案
 * 
 * 用法：server.php?url=VIDEO_URL
 */

// 设置响应头
header('Content-Type: application/json; charset=utf-8');

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

// ============ 方案零：直接调用平台官方 API（推荐，最快最稳定） ============
$videoLink = extractVideoByDirectApi($videoUrl);

// ============ 方案一：纯 cURL + 正则解析（第三方解析接口） ============
if (!$videoLink) {
    $videoLink = extractVideoByCurl($videoUrl);
}

// ============ 方案二：如果方案一失败，尝试通过 Chrome Headless 嗅探 ============
if (!$videoLink && isChromeAvailable()) {
    $videoLink = extractVideoByChromeHeadless($videoUrl);
}

// 返回结果
if ($videoLink) {
    echo json_encode(['code' => 200, 'url' => $videoLink], JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(500);
    echo json_encode(['code' => 500, 'message' => 'Failed to extract video URL'], JSON_UNESCAPED_UNICODE);
}

exit;

/**
 * 方案零：直接调用平台官方 API 获取视频播放地址
 * 支持腾讯视频、爱奇艺、优酷等主流平台
 * 
 * @param string $videoUrl 视频页面URL
 * @return string|null 视频直链或null
 */
function extractVideoByDirectApi(string $videoUrl): ?string
{
    $host = parse_url($videoUrl, PHP_URL_HOST) ?? '';

    // 腾讯视频
    if (preg_match('/v\.qq\.com/i', $host)) {
        return extractTencentVideo($videoUrl);
    }

    // 爱奇艺
    if (preg_match('/iqiyi\.com/i', $host)) {
        return extractIqiyiVideo($videoUrl);
    }

    // 优酷
    if (preg_match('/youku\.com/i', $host)) {
        return extractYoukuVideo($videoUrl);
    }

    // 芒果TV
    if (preg_match('/mgtv\.com/i', $host)) {
        return extractMgtvVideo($videoUrl);
    }

    return null;
}

/**
 * 腾讯视频直接 API 解析
 * 通过 getinfo + getkey 两步获取视频直链
 * 
 * @param string $videoUrl 腾讯视频页面URL
 * @return string|null 视频直链或null
 */
function extractTencentVideo(string $videoUrl): ?string
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
        return null;
    }

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $guid = str_pad((string)mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999), 32, '0', STR_PAD_LEFT);

    // 第一步：调用 getinfo 获取视频元数据
    $infoUrl = "https://vv.video.qq.com/getinfo?vids={$vid}&platform=101001&charge=0&otype=json&defn=shd&guid={$guid}";

    $ch = curl_init($infoUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => 'https://v.qq.com/',
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$resp || $httpCode !== 200) {
        return null;
    }

    // 解析 JSONP 响应
    $resp = preg_replace('/^QZOutputJson=/', '', $resp);
    $resp = rtrim($resp, ';');
    $data = json_decode($resp, true);

    if (!$data || !isset($data['vl']['vi'][0])) {
        return null;
    }

    $vi = $data['vl']['vi'][0];
    $fn = $vi['fn'] ?? '';
    $servers = $vi['ul']['ui'] ?? [];

    if (!$fn || empty($servers)) {
        return null;
    }

    // 从文件名中提取格式号 (如 f10217.mp4 -> 10217)
    $format = '2';
    if (preg_match('/\.f(\d+)\.mp4$/i', $fn, $m)) {
        $format = $m[1];
    }

    // 第二步：调用 getkey 获取 vkey
    $keyUrl = "https://vv.video.qq.com/getkey?format={$format}&otype=json&vid={$vid}&guid={$guid}&filename={$fn}&platform=101001";

    $ch2 = curl_init($keyUrl);
    curl_setopt_array($ch2, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => 'https://v.qq.com/',
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
    ]);
    $resp2 = curl_exec($ch2);
    curl_close($ch2);

    if (!$resp2) {
        return null;
    }

    $resp2 = preg_replace('/^QZOutputJson=/', '', $resp2);
    $resp2 = rtrim($resp2, ';');
    $data2 = json_decode($resp2, true);

    if (!$data2 || !isset($data2['key']) || ($data2['s'] ?? '') !== 'o') {
        return null;
    }

    $vkey = $data2['key'];

    // 第三步：遍历所有服务器，构建并测试 URL
    foreach ($servers as $server) {
        $serverUrl = $server['url'] ?? '';
        if (!$serverUrl) {
            continue;
        }

        $videoLink = $serverUrl . $fn . '?vkey=' . $vkey;

        // 快速验证 URL 是否可访问
        $ch3 = curl_init($videoLink);
        curl_setopt_array($ch3, [
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT      => $ua,
            CURLOPT_REFERER        => 'https://v.qq.com/',
            CURLOPT_TIMEOUT        => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
        ]);
        curl_exec($ch3);
        $verifyCode = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
        curl_close($ch3);

        if ($verifyCode == 200 || $verifyCode == 206) {
            return $videoLink;
        }
    }

    return null;
}

/**
 * 爱奇艺视频直接 API 解析
 * 
 * @param string $videoUrl 爱奇艺视频页面URL
 * @return string|null 视频直链或null
 */
function extractIqiyiVideo(string $videoUrl): ?string
{
    // 提取视频 ID
    $vid = null;
    if (preg_match('/iqiyi\.com\/.*?(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    // 爱奇艺 API 获取视频信息
    $apiUrl = "https://pcw-api.iqiyi.com/video/video/baseinfo/" . urlencode($vid);

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => 'https://www.iqiyi.com/',
        CURLOPT_TIMEOUT        => 10,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

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
 * 
 * @param string $videoUrl 优酷视频页面URL
 * @return string|null 视频直链或null
 */
function extractYoukuVideo(string $videoUrl): ?string
{
    // 提取视频 ID
    $vid = null;
    if (preg_match('/id_([a-zA-Z0-9=]+)/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    // 优酷 API
    $apiUrl = "https://ups.youku.com/ups/get.json?vid={$vid}&ccode=0502&client_ip=0.0.0.0&utid=0&client_ts=" . time();

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => 'https://v.youku.com/',
        CURLOPT_TIMEOUT        => 10,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

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
 * 
 * @param string $videoUrl 芒果TV视频页面URL
 * @return string|null 视频直链或null
 */
function extractMgtvVideo(string $videoUrl): ?string
{
    // 提取视频 ID
    $vid = null;
    if (preg_match('/\/b\/(\d+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }
    if (!$vid) {
        return null;
    }

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    // 芒果TV API
    $apiUrl = "https://pcweb.api.mgtv.com/player/video?video_id={$vid}";

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => 'https://www.mgtv.com/',
        CURLOPT_TIMEOUT        => 10,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);

    if (!$resp) {
        return null;
    }

    $data = json_decode($resp, true);
    $playUrl = $data['data']['info']['play_url'] ?? null;

    if ($playUrl && filter_var($playUrl, FILTER_VALIDATE_URL)) {
        return $playUrl;
    }

    // 尝试从 stream 列表中获取
    $streams = $data['data']['stream'] ?? [];
    foreach ($streams as $stream) {
        $url = $stream['url'] ?? '';
        if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
    }

    return null;
}

/**
 * 方案一：纯 cURL 抓取解析页面内容，通过正则提取视频地址
 * 
 * @param string $videoUrl 视频页面URL
 * @return string|null 视频直链或null
 */
function extractVideoByCurl(string $videoUrl): ?string
{
    $parseApiUrl = 'https://jx.xmflv.com/?url=' . urlencode($videoUrl);

    // 第一次请求
    $result = fetchParsePage($parseApiUrl);
    if ($result['videoLink']) {
        return $result['videoLink'];
    }

    // 如果第一次失败，使用返回的页面URL重试（模拟 reload）
    if (!empty($result['effectiveUrl']) && $result['effectiveUrl'] !== $parseApiUrl) {
        $retryResult = fetchParsePage($result['effectiveUrl']);
        if ($retryResult['videoLink']) {
            return $retryResult['videoLink'];
        }
    }

    // 使用备用解析接口重试
    $backupApis = [
        'https://jx.xmflv.com/?url=',
        'https://jx.bozrc.com:4433/player/?url=',
        'https://jx.m3u8.tv/jiexi/?url=',
        'https://jx.parwix.com:4433/player/?url=',
        'https://jx.jsonplayer.com/player/?url=',
    ];
    foreach ($backupApis as $api) {
        $backupUrl = $api . urlencode($videoUrl);
        $backupResult = fetchParsePage($backupUrl);
        if ($backupResult['videoLink']) {
            return $backupResult['videoLink'];
        }
    }

    return null;
}

/**
 * 通过 cURL 抓取解析页面并提取视频链接
 * 
 * @param string $targetUrl 要抓取的URL
 * @return array ['videoLink' => ?string, 'effectiveUrl' => string]
 */
function fetchParsePage(string $targetUrl): array
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $targetUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_REFERER        => $targetUrl,
        CURLOPT_ENCODING       => '',
    ]);

    $html = curl_exec($ch);
    $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error || $httpCode !== 200 || !$html) {
        return ['videoLink' => null, 'effectiveUrl' => $effectiveUrl];
    }

    return [
        'videoLink'    => extractVideoFromHtml($html),
        'effectiveUrl' => $effectiveUrl,
    ];
}

/**
 * 从 HTML 内容中提取视频链接（支持多种格式）
 * 
 * @param string $html 页面HTML内容
 * @return string|null 视频链接或null
 */
function extractVideoFromHtml(string $html): ?string
{
    // 模式1：匹配 <video> 或 <source> 标签中的 src
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
        // 过滤掉播放器本身的JS/CSS链接，只保留真正的视频地址
        foreach ($allMatches[0] as $match) {
            // 排除明显不是视频的链接
            if (preg_match('/\.(js|css|png|jpg|jpeg|gif|svg|ico|woff|ttf)/i', $match)) {
                continue;
            }
            // 优先返回包含常见视频CDN域名的链接
            if (preg_match('/(video|media|play|stream|cdn|m3u8|mp4|vod)/i', $match)) {
                return rtrim($match, '\\');
            }
        }
        // 如果没有CDN关键词匹配，返回第一个
        if (!empty($allMatches[0][0])) {
            return rtrim($allMatches[0][0], '\\');
        }
    }

    // 模式4：匹配 JSON 格式中的视频地址
    if (preg_match('/["\'](https?:\/\/[^"\']+\.m3u8[^"\']*)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1]);
    }
    if (preg_match('/["\'](https?:\/\/[^"\']+\.mp4[^"\']*)["\']/i', $html, $matches)) {
        return html_entity_decode($matches[1]);
    }

    return null;
}

/**
 * 方案二：通过 Chrome Headless 嗅探视频地址
 * 需要服务器安装 Chrome/Chromium
 * 
 * @param string $videoUrl 视频页面URL
 * @return string|null 视频直链或null
 */
function extractVideoByChromeHeadless(string $videoUrl): ?string
{
    $parseUrl = 'https://jx.xmflv.com/?url=' . urlencode($videoUrl);
    $tempDir = sys_get_temp_dir();

    // 生成临时文件路径
    $outputFile = $tempDir . '/video_sniff_' . uniqid() . '.log';
    $harFile = $tempDir . '/video_sniff_' . uniqid() . '.har';

    // Chrome headless 命令，启用网络日志记录
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

    // 从 HAR 文件中提取视频链接
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

    // 如果 HAR 没找到，从输出的 HTML/DOM 中提取
    if (!$videoLink && file_exists($outputFile)) {
        $domContent = file_get_contents($outputFile);
        $videoLink = extractVideoFromHtml($domContent);
        @unlink($outputFile);
    }

    return $videoLink;
}

/**
 * 检测 Chrome/Chromium 是否可用
 * 
 * @return bool
 */
function isChromeAvailable(): bool
{
    if (!function_exists('shell_exec') || !function_exists('exec')) {
        return false;
    }

    $chromePaths = [
        'chromium-browser',
        'chromium',
        'google-chrome',
        'google-chrome-stable',
    ];
    foreach ($chromePaths as $cmd) {
        $check = @shell_exec(sprintf('which %s 2>/dev/null', escapeshellcmd($cmd)));
        if (!empty($check)) {
            return true;
        }
    }
    return false;
}
