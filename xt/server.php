<?php
/**
 * 超级嗅探 - PHP 版本服务端
 *
 * 功能：
 *   1. 调用官解接口获取视频 m3u8/mp4 直链
 *   2. 下载 m3u8 内容，通过规则引擎 + AI 识别广告
 *   3. 生成去广告 m3u8 文件，返回结构化 JSON
 *
 * 用法：server.php?url=VIDEO_URL
 */

header('Content-Type: application/json; charset=utf-8');

// 加载配置和广告过滤引擎
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/AdFilter.php';

// ============ 参数校验 ============
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

// ============ 调试日志 ============
$debugLog = [];
if ($config['debug']) {
    $debugLog['input_url'] = $videoUrl;
    $debugLog['timeline'] = [];
}

// ============ 步骤1：调用官解接口获取视频直链 ============
if ($config['debug']) {
    $debugLog['timeline']['step1_start'] = date('H:i:s');
}

$videoLink = getVideoLinkFromOfficialApi($videoUrl, $config, $debugLog);

if (!$videoLink) {
    http_response_code(500);
    $errorResponse = [
        'code'    => 500,
        'message' => '所有官解接口均未能解析出视频地址',
    ];
    if ($config['debug']) {
        $errorResponse['debug'] = $debugLog;
    }
    echo json_encode($errorResponse, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($config['debug']) {
    $debugLog['timeline']['step1_end'] = date('H:i:s');
    $debugLog['video_link'] = $videoLink;
}

// ============ 步骤2：判断格式，处理广告 ============
if ($config['debug']) {
    $debugLog['timeline']['step2_start'] = date('H:i:s');
}

$isM3u8 = preg_match('/\.m3u8(\?|$)/i', $videoLink);
$adInfo = null;
$cleanUrl = null;

if ($isM3u8) {
    // m3u8 格式：下载内容 → 广告识别 → 生成去广告版本
    $m3u8Content = fetchM3u8Content($videoLink, $config);

    if ($m3u8Content) {
        // 检查是否是多级 m3u8（主清单引用子清单）
        $resolvedContent = resolveMultiLevelM3u8($m3u8Content, $videoLink, $config);
        if ($resolvedContent !== $m3u8Content) {
            // 多级 m3u8，更新实际地址
            $videoLink = $resolvedContent['url'] ?? $videoLink;
            $m3u8Content = $resolvedContent['content'] ?? $m3u8Content;
        }

        // 广告过滤
        $filter = new AdFilter($config);
        $result = $filter->process($m3u8Content, $videoLink);
        $adInfo = $result['ad_info'];
        $cleanM3u8Content = $result['clean_content'];

        // 保存去广告 m3u8 到缓存
        $cacheId = generateCacheId($videoUrl);
        $cleanUrl = saveCleanM3u8($cacheId, $cleanM3u8Content, $videoLink, $config);
    }
}

if ($config['debug']) {
    $debugLog['timeline']['step2_end'] = date('H:i:s');
}

// ============ 步骤3：返回结构化 JSON ============
$response = [
    'code' => 200,
    'msg'  => '解析成功',
    'data' => [
        'original_url' => $videoLink,
        'clean_url'    => $cleanUrl,
        'format'       => $isM3u8 ? 'm3u8' : 'mp4',
        'has_ads'      => $adInfo ? $adInfo['has_ads'] : false,
        'ad_info'      => $adInfo,
    ],
];

if ($config['debug']) {
    $response['debug'] = $debugLog;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;


// ==================== 核心函数 ====================

/**
 * 调用官解接口获取视频直链
 */
function getVideoLinkFromOfficialApi(string $videoUrl, array $config, array &$debugLog): ?string
{
    foreach ($config['official_apis'] as $api) {
        $targetUrl = $api['url'] . urlencode($videoUrl);

        if ($config['debug']) {
            $debugLog['timeline']['trying_api'] = $api['name'];
        }

        $ch = curl_init();
        $headers = [];
        foreach ($api['headers'] ?? [] as $key => $value) {
            $headers[] = $key . ': ' . $value;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $targetUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => $config['http']['timeout'],
            CURLOPT_CONNECTTIMEOUT => $config['http']['connect_timeout'],
            CURLOPT_SSL_VERIFYPEER => $config['http']['ssl_verify'],
            CURLOPT_SSL_VERIFYHOST => $config['http']['ssl_verify'] ? 2 : 0,
            CURLOPT_USERAGENT      => $config['http']['user_agent'],
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_ENCODING       => '',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $error = curl_error($ch);
        curl_close($ch);

        if ($config['debug']) {
            $debugLog['api_' . $api['name']] = [
                'http_code'   => $httpCode,
                'error'       => $error,
                'effective'   => $effectiveUrl,
                'resp_length' => strlen($response),
            ];
        }

        if ($error) {
            continue;
        }

        // 根据接口类型解析
        switch ($api['type']) {
            case 'redirect':
                // redirect 类型：最终跳转的 URL 就是视频直链
                if (preg_match('/\.(m3u8|mp4)(\?|$)/i', $effectiveUrl)) {
                    return $effectiveUrl;
                }
                // 也可能在响应体中
                $extracted = extractVideoUrl($response);
                if ($extracted) {
                    return $extracted;
                }
                break;

            case 'json':
                // JSON 类型：解析 JSON 获取视频地址
                $data = json_decode($response, true);
                if ($data) {
                    // 优先使用配置指定的 url_field，再尝试常见字段
                    $urlField = $api['url_field'] ?? null;
                    $url = null;
                    if ($urlField && isset($data[$urlField])) {
                        $url = $data[$urlField];
                    }
                    if (!$url) {
                        $url = $data['url'] ?? $data['play_url'] ?? $data['data']['url']
                            ?? $data['data']['play_url'] ?? $data['video_url'] ?? null;
                    }
                    if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                        return $url;
                    }
                    // 递归查找含 m3u8/mp4 的 URL
                    $foundUrl = findUrlInArray($data);
                    if ($foundUrl) {
                        return $foundUrl;
                    }
                }
                break;

            case 'text':
                // 纯文本类型：响应体就是直链
                $trimmed = trim($response);
                if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
                    return $trimmed;
                }
                break;
        }
    }

    return null;
}

/**
 * 下载 m3u8 文件内容
 */
function fetchM3u8Content(string $m3u8Url, array $config): ?string
{
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $m3u8Url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 5,
        CURLOPT_TIMEOUT        => $config['http']['timeout'],
        CURLOPT_CONNECTTIMEOUT => $config['http']['connect_timeout'],
        CURLOPT_SSL_VERIFYPEER => $config['http']['ssl_verify'],
        CURLOPT_SSL_VERIFYHOST => $config['http']['ssl_verify'] ? 2 : 0,
        CURLOPT_USERAGENT      => $config['http']['user_agent'],
        CURLOPT_REFERER        => $m3u8Url,
        CURLOPT_ENCODING       => '',
    ]);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$content) {
        return null;
    }

    return $content;
}

/**
 * 处理多级 m3u8（主清单引用子清单）
 * 如果内容中不包含 #EXTINF 但包含子 m3u8 链接，则下载子清单
 */
function resolveMultiLevelM3u8(string $content, string $m3u8Url, array $config): array
{
    // 如果已包含分段信息，说明是最终清单
    if (strpos($content, '#EXTINF') !== false) {
        return ['content' => $content, 'url' => $m3u8Url];
    }

    // 查找子 m3u8 链接（通常在 #EXT-X-STREAM-INF 之后）
    if (preg_match('/#EXT-X-STREAM-INF[^\n]*\n(.+)/', $content, $m)) {
        $subUrl = trim($m[1]);
        // 解析为绝对 URL
        if (!preg_match('/^https?:\/\//i', $subUrl)) {
            $baseUrl = dirname($m3u8Url);
            $subUrl = $baseUrl . '/' . ltrim($subUrl, '/');
        }

        // 选择最高分辨率的子清单（简单策略：取最后一个 STREAM-INF）
        $allStreams = [];
        if (preg_match_all('/#EXT-X-STREAM-INF[^#]*\n([^\n#]+)/', $content, $streams)) {
            foreach ($streams[1] as $streamUrl) {
                $streamUrl = trim($streamUrl);
                if (!preg_match('/^https?:\/\//i', $streamUrl)) {
                    $baseUrl = dirname($m3u8Url);
                    $streamUrl = $baseUrl . '/' . ltrim($streamUrl, '/');
                }
                $allStreams[] = $streamUrl;
            }
        }

        // 取最后一个（通常是最高分辨率）
        if (!empty($allStreams)) {
            $subUrl = end($allStreams);
        }

        $subContent = fetchM3u8Content($subUrl, $config);
        if ($subContent) {
            return ['content' => $subContent, 'url' => $subUrl];
        }
    }

    return ['content' => $content, 'url' => $m3u8Url];
}

/**
 * 生成缓存 ID
 */
function generateCacheId(string $videoUrl): string
{
    return substr(md5($videoUrl . time()), 0, 16);
}

/**
 * 保存去广告 m3u8 到缓存文件
 */
function saveCleanM3u8(string $cacheId, string $content, string $originalUrl, array $config): string
{
    $cacheDir = $config['cache']['dir'];

    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    $filePath = $cacheDir . '/' . $cacheId . '.m3u8';

    // 保存 m3u8 内容和元数据
    $data = [
        'content'      => $content,
        'original_url' => $originalUrl,
        'created_at'   => time(),
    ];
    file_put_contents($filePath, json_encode($data));

    // 构建 clean_url（供前端播放器直接使用）
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

    return $protocol . '://' . $host . rtrim($scriptDir, '/') . '/clean.php?id=' . $cacheId;
}

/**
 * 从响应内容中提取视频 URL
 */
function extractVideoUrl(string $content): ?string
{
    $patterns = [
        '/https?:\/\/[^\s\'"<>\\\)\\\\,;]+\.m3u8[^\s\'"<>\\\)\\\\,;]*/i',
        '/https?:\/\/[^\s\'"<>\\\)\\\\,;]+\.mp4[^\s\'"<>\\\)\\\\,;]*/i',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $content, $m)) {
            return $m[0];
        }
    }
    return null;
}

/**
 * 递归在数组中查找 URL
 */
function findUrlInArray(array $arr): ?string
{
    foreach ($arr as $key => $value) {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)
            && preg_match('/\.(m3u8|mp4)(\?|$)/i', $value)) {
            return $value;
        }
        if (is_array($value)) {
            $found = findUrlInArray($value);
            if ($found) {
                return $found;
            }
        }
    }
    return null;
}
