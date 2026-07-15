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

// ============ 方案一：纯 cURL + 正则解析（推荐，无需 Chrome） ============
$videoLink = extractVideoByCurl($videoUrl);

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
    $chromePaths = [
        'chromium-browser',
        'chromium',
        'google-chrome',
        'google-chrome-stable',
    ];
    foreach ($chromePaths as $cmd) {
        $check = shell_exec(sprintf('which %s 2>/dev/null', escapeshellcmd($cmd)));
        if (!empty($check)) {
            return true;
        }
    }
    return false;
}
