<?php
/**
 * 超级嗅探 - PHP 版本服务端
 * 
 * 功能：通过解析服务嗅探视频播放地址（.m3u8 / .mp4）
 * 支持：腾讯视频、爱奇艺、优酷、芒果TV 等主流平台
 * 
 * 自动适配国内外服务器（用户IP注入方案）：
 *   方案零：代理转发 + 用户IP注入 - 服务器转发腾讯API请求时，将用户的真实国内IP注入X-Forwarded-For
 *   方案一：第三方JSON解析接口
 *   方案二：第三方HTML解析接口
 *   方案三：Chrome Headless 嗅探
 * 
 * 核心原理：用户在国内访问海外服务器，服务器能获取到用户的国内IP。
 *   转发腾讯API请求时注入 X-Forwarded-For=用户国内IP，腾讯按国内IP鉴权返回em=0。
 * 
 * 用法：server.php?url=VIDEO_URL
 *       server.php?url=VIDEO_URL&debug=1  (调试模式)
 *       server.php?action=proxy&url=xxx    (API代理转发)
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Referer, User-Agent');

if (isset($_GET['action']) && $_GET['action'] == 'proxy') {
    proxyRequest();
    exit;
}

header('Content-Type: application/json; charset=utf-8');

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
    $videoLink = extractTencentVideoWithProxy($videoUrl, $debugLog);
} else {
    $videoLink = extractVideoByDirectApi($videoUrl, $debugLog);
}

if ($debug) {
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
//  代理转发功能（注入用户真实IP）
// ============================================================

function proxyRequest()
{
    $targetUrl = $_GET['url'] ?? '';
    if (!$targetUrl) {
        http_response_code(400);
        echo '{"code":400,"message":"URL parameter is required"}';
        exit;
    }

    $userIp = getUserRealIp();
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    $headers = [
        'X-Forwarded-For: ' . $userIp,
        'Client-IP: ' . $userIp,
        'X-Real-IP: ' . $userIp,
        'Forwarded: for=' . $userIp,
        'Accept: */*',
        'User-Agent: ' . $ua,
    ];
    if ($referer) {
        $headers[] = 'Referer: ' . $referer;
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $targetUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => $ua,
        CURLOPT_REFERER        => $referer ?: $targetUrl,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_HTTPHEADER     => $headers,
    ]);

    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    header('Content-Type: application/json; charset=utf-8');
    http_response_code($httpCode);
    echo $resp;
}

function getUserRealIp(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '220.181.38.148';
    }

    return $ip;
}


// ============================================================
//  腾讯视频解析（代理转发 + 用户IP注入）
// ============================================================

function extractTencentVideoWithProxy(string $videoUrl, array &$debugLog = []): ?string
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
        $debugLog[] = '腾讯: 未提取到视频ID';
        return null;
    }
    $debugLog[] = "腾讯: 视频ID={$vid} [代理转发+用户IP注入模式]";

    $userIp = getUserRealIp();
    $debugLog[] = "腾讯: 用户真实IP={$userIp}";

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $mobileUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1';
    $guid = str_pad((string)mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999), 32, '0', STR_PAD_LEFT);

    $defnList = ['shd', 'fhd', 'hd', 'sd'];
    $apiHosts = [
        ['host' => 'https://vv.video.qq.com',  'ehost' => 'https://v.qq.com',  'ua' => $ua,        'name' => 'PC端'],
        ['host' => 'https://h5vv.video.qq.com', 'ehost' => 'https://m.v.qq.com', 'ua' => $mobileUa, 'name' => 'H5端'],
    ];

    $data = null;
    foreach ($apiHosts as $apiInfo) {
        foreach ($defnList as $defn) {
            $apiUrl = "{$apiInfo['host']}/getinfo?vids={$vid}&platform=101001&charge=0&otype=json&defn={$defn}&guid={$guid}&ehost=" . urlencode($apiInfo['ehost']);

            $resp = curlGetWithUserIp($apiUrl, [
                'ua'        => $apiInfo['ua'],
                'referer'   => $apiInfo['ehost'] . '/',
                'timeout'   => 10,
                'user_ip'   => $userIp,
            ]);

            if (!$resp) {
                $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} 请求失败";
                continue;
            }

            $resp = preg_replace('/^QZOutputJson=/', '', $resp);
            $resp = rtrim($resp, ';');
            $data = json_decode($resp, true);
            $em = $data['em'] ?? 'null';
            $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} em={$em} [用户IP={$userIp}]";

            if ($data && ($data['em'] ?? 1) === 0 && isset($data['vl']['vi'][0])) {
                $debugLog[] = "腾讯: {$apiInfo['name']}-{$defn} 成功获取视频信息";
                break 2;
            }
            $data = null;
        }
    }

    if (!$data || !isset($data['vl']['vi'][0])) {
        $debugLog[] = '腾讯: 代理转发+用户IP注入模式失败';
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

    $vkey = $fvkey;
    if (!$vkey) {
        $debugLog[] = '腾讯: fvkey为空，尝试调用getkey';
        $format = '2';
        if (preg_match('/\.f(\d+)\.mp4$/i', $fn, $m)) {
            $format = $m[1];
        }
        $keyUrl = "https://vv.video.qq.com/getkey?format={$format}&otype=json&vid={$vid}&guid={$guid}&filename={$fn}&platform=101001";
        $resp2 = curlGetWithUserIp($keyUrl, [
            'ua'        => $ua,
            'referer'   => 'https://v.qq.com/',
            'timeout'   => 10,
            'user_ip'   => $userIp,
        ]);
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
//  带用户IP注入的curl请求
// ============================================================

function curlGetWithUserIp(string $url, array $options = []): ?string
{
    $userIp = $options['user_ip'] ?? getUserRealIp();

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

    $headers = $options['headers'] ?? [];
    $headers[] = 'X-Forwarded-For: ' . $userIp;
    $headers[] = 'Client-IP: ' . $userIp;
    $headers[] = 'X-Real-IP: ' . $userIp;
    $headers[] = 'Forwarded: for=' . $userIp;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);

    if (!$resp || $httpCode !== 200) {
        return null;
    }

    return $resp;
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