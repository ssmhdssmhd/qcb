<?php
/**
 * 超级嗅探 - PHP 版本服务端
 * 
 * 功能：通过解析服务嗅探视频播放地址（.m3u8 / .mp4）
 * 支持：腾讯视频、爱奇艺、优酷、芒果TV 等主流平台
 * 
 * 自动适配国内外服务器（客户端直连方案）：
 *   方案零：两阶段解析 - 服务器生成API参数，客户端（国内）直接调用腾讯API
 *   方案一：第三方JSON解析接口
 *   方案二：第三方HTML解析接口
 *   方案三：Chrome Headless 嗅探
 * 
 * 用法：server.php?url=VIDEO_URL
 *       server.php?url=VIDEO_URL&debug=1  (调试模式)
 *       server.php?url=VIDEO_URL&phase=2&api_data=BASE64_ENCODED_JSON  (阶段2：客户端回传API数据)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$debug = isset($_GET['debug']) && $_GET['debug'] == '1';
$debugLog = [];

if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'URL parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$videoUrl = trim($_GET['url']);

if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo json_encode(['code' => 400, 'message' => 'Invalid URL format'], JSON_UNESCAPED_UNICODE);
    exit;
}

$host = parse_url($videoUrl, PHP_URL_HOST) ?? '';
$videoLink = null;

if (preg_match('/v\.qq\.com/i', $host)) {
    if (isset($_GET['phase']) && $_GET['phase'] == '2') {
        $videoLink = processTencentApiData($videoUrl, $_GET['api_data'] ?? '', $debugLog);
    } else {
        echo json_encode([
            'code' => 206,
            'message' => '需要客户端直连腾讯API',
            'phase' => 1,
            'task' => generateTencentApiRequests($videoUrl),
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    $videoLink = extractVideoByDirectApi($videoUrl, $debugLog);
}

if ($debug && !isset($_GET['phase'])) {
    $debugLog[] = '方案零(官方API): ' . ($videoLink ? '成功' : '失败');
}

if (!$videoLink) {
    $videoLink = extractVideoByJsonApi($videoUrl, $debugLog);
    if ($debug) {
        $debugLog[] = '方案一(第三方JSON): ' . ($videoLink ? '成功' : '失败');
    }
}

if (!$videoLink) {
    $videoLink = extractVideoByHtmlApi($videoUrl, $debugLog);
    if ($debug) {
        $debugLog[] = '方案二(第三方HTML): ' . ($videoLink ? '成功' : '失败');
    }
}

if (!$videoLink && isChromeAvailable()) {
    $videoLink = extractVideoByChromeHeadless($videoUrl);
    if ($debug) {
        $debugLog[] = '方案三(Chrome): ' . ($videoLink ? '成功' : '失败');
    }
}

if ($videoLink) {
    $response = ['code' => 200, 'url' => $videoLink];
} else {
    http_response_code(500);
    $response = ['code' => 500, 'message' => 'Failed to extract video URL'];
}

if ($debug) {
    $response['debug'] = $debugLog;
    $response['php_version'] = PHP_VERSION;
    $response['curl_loaded'] = extension_loaded('curl');
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;


// ============================================================
//  腾讯视频两阶段解析方案
// ============================================================

function generateTencentApiRequests(string $videoUrl): array
{
    $vid = null;
    if (preg_match('/\/x\/cover\/\w+\/(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/vid=(\w+)/i', $videoUrl, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/\/x\/page\/(\w+)\.html/i', $videoUrl, $m)) {
        $vid = $m[1];
    }

    if (!$vid) {
        return [];
    }

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $mobileUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1';
    $guid = str_pad((string)mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999), 32, '0', STR_PAD_LEFT);

    $defnList = ['shd', 'fhd', 'hd', 'sd'];
    $apiHosts = [
        ['host' => 'https://vv.video.qq.com',  'ehost' => 'https://v.qq.com',  'ua' => $ua,        'name' => 'PC端'],
        ['host' => 'https://h5vv.video.qq.com', 'ehost' => 'https://m.v.qq.com', 'ua' => $mobileUa, 'name' => 'H5端'],
    ];

    $requests = [];
    foreach ($apiHosts as $apiInfo) {
        foreach ($defnList as $defn) {
            $apiUrl = "{$apiInfo['host']}/getinfo?vids={$vid}&platform=101001&charge=0&otype=json&defn={$defn}&guid={$guid}&ehost=" . urlencode($apiInfo['ehost']);
            $requests[] = [
                'url'     => $apiUrl,
                'ua'      => $apiInfo['ua'],
                'referer' => $apiInfo['ehost'] . '/',
                'name'    => $apiInfo['name'] . '-' . $defn,
            ];
        }
    }

    return [
        'vid'     => $vid,
        'guid'    => $guid,
        'ua'      => $ua,
        'mobileUa' => $mobileUa,
        'requests' => $requests,
        'callback' => $_SERVER['REQUEST_URI'] . '&phase=2',
    ];
}

function processTencentApiData(string $videoUrl, string $apiDataBase64, array &$debugLog = []): ?string
{
    $debugLog[] = '腾讯: 阶段2处理 - 客户端回传API数据';

    $apiData = base64_decode($apiDataBase64);
    if (!$apiData) {
        $debugLog[] = '腾讯: API数据解码失败';
        return null;
    }

    $apiData = urldecode($apiData);
    $data = json_decode($apiData, true);
    if (!$data || !isset($data['vl']['vi'][0])) {
        $debugLog[] = '腾讯: API数据格式错误或无视频信息';
        return null;
    }

    $debugLog[] = '腾讯: 客户端直连成功，获取到视频信息';

    $vi = $data['vl']['vi'][0];
    $fn = $vi['fn'] ?? '';
    $servers = $vi['ul']['ui'] ?? [];
    $fvkey = $vi['fvkey'] ?? '';
    $vid = $vi['vid'] ?? '';
    $guid = $_GET['guid'] ?? '';

    if (!$fn || empty($servers)) {
        $debugLog[] = '腾讯: 未获取到文件名或服务器列表';
        return null;
    }
    $debugLog[] = "腾讯: 文件名={$fn} 服务器数=" . count($servers) . " fvkey=" . (empty($fvkey) ? '空' : '已获取');

    $vkey = $fvkey;
    if (!$vkey && $vid && $guid) {
        $debugLog[] = '腾讯: fvkey为空，尝试调用getkey';
        $format = '2';
        if (preg_match('/\.f(\d+)\.mp4$/i', $fn, $m)) {
            $format = $m[1];
        }
        $keyUrl = "https://vv.video.qq.com/getkey?format={$format}&otype=json&vid={$vid}&guid={$guid}&filename={$fn}&platform=101001";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $keyUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            CURLOPT_REFERER        => 'https://v.qq.com/',
            CURLOPT_TIMEOUT        => 10,
        ]);
        $resp2 = curl_exec($ch);
        curl_close($ch);

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
//  方案零：平台官方 API 直连（非腾讯平台）
// ============================================================

function extractVideoByDirectApi(string $videoUrl, array &$debugLog = []): ?string
{
    $host = parse_url($videoUrl, PHP_URL_HOST) ?? '';

    if (preg_match('/iqiyi\.com/i', $host)) {
        $debugLog[] = '检测到爱奇艺，调用官方API';
        return extractIqiyiVideo($videoUrl);
    }

    if (preg_match('/youku\.com/i', $host)) {
        $debugLog[] = '检测到优酷，调用官方API';
        return extractYoukuVideo($videoUrl);
    }

    if (preg_match('/mgtv\.com/i', $host)) {
        $debugLog[] = '检测到芒果TV，调用官方API';
        return extractMgtvVideo($videoUrl);
    }

    $debugLog[] = '未匹配到已知平台，跳过官方API';
    return null;
}


// ============================================================
//  爱奇艺 / 优酷 / 芒果TV 解析
// ============================================================

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

function extractVideoByJsonApi(string $videoUrl, array &$debugLog = []): ?string
{
    $encodedUrl = urlencode($videoUrl);

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

    $urlFields = ['url', 'video', 'src', 'play', 'm3u8', 'mp4', 'data'];
    foreach ($urlFields as $field) {
        $val = $data[$field] ?? null;
        if (is_string($val) && preg_match('/\.(m3u8|mp4)(\?|$)/i', $val)) {
            return $val;
        }
    }

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

function extractVideoByHtmlApi(string $videoUrl, array &$debugLog = []): ?string
{
    $encodedUrl = urlencode($videoUrl);

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

function extractVideoFromHtml(string $html): ?string
{
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

    $jsPatterns = [
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.m3u8[^\'"`\s]*)[\'"`]/i',
        '/(?:var|let|const|player|video|source|url|src|link)\s*[:=]\s*[\'"`](https?:\/\/[^\'"`\s]+\.mp4[^\'"`\s]*)[\'"`]/i',
    ];
    foreach ($jsPatterns as $pattern) {
        if (preg_match($pattern, $html, $matches)) {
            return html_entity_decode($matches[1]);
        }
    }

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

    if (!$videoLink && file_exists($outputFile)) {
        $domContent = file_get_contents($outputFile);
        $videoLink = extractVideoFromHtml($domContent);
        @unlink($outputFile);
    }

    return $videoLink;
}

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