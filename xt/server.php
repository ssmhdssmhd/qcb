<?php
/**
 * 超级嗅探 - 服务端核心
 *
 * 功能：
 *   1. 调用官解接口获取视频 m3u8/mp4 直链
 *   2. 下载 m3u8 内容，通过规则引擎 + AI 识别广告
 *   3. 生成去广告 m3u8 文件
 *
 * 本文件提供 parseVideo() 核心函数，由 api.php 调用并控制输出格式
 */

// 加载配置和广告过滤引擎
$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/AdFilter.php';

/**
 * 核心解析函数
 *
 * @param string $videoUrl 视频页面 URL
 * @return array 解析结果
 */
function parseVideo(string $videoUrl): array
{
    global $config;

    $startTime = microtime(true);

    // 参数校验
    if (empty($videoUrl) || !filter_var($videoUrl, FILTER_VALIDATE_URL)) {
        return buildResult(400, '解析失败', '链接格式不正确', null, $startTime);
    }

    // 步骤1：调用官解接口获取视频直链
    $videoLink = getVideoLinkFromOfficialApi($videoUrl, $config);

    if (!$videoLink) {
        return buildResult(500, '解析失败', '所有官解接口均未能解析出视频地址', null, $startTime);
    }

    // 步骤2：判断格式，处理广告
    $isM3u8 = preg_match('/\.m3u8(\?|$)/i', $videoLink);
    $playUrl = $videoLink; // 默认用原始直链

    if ($isM3u8) {
        $m3u8Content = fetchM3u8Content($videoLink, $config);

        if ($m3u8Content) {
            // 处理多级 m3u8
            $resolved = resolveMultiLevelM3u8($m3u8Content, $videoLink, $config);
            if ($resolved['url'] !== $videoLink) {
                $videoLink = $resolved['url'];
                $m3u8Content = $resolved['content'];
            }

            // 广告过滤
            $filter = new AdFilter($config);
            $result = $filter->process($m3u8Content, $videoLink);

            // 保存去广告 m3u8 到缓存，生成播放地址
            $cacheId = generateCacheId($videoUrl);
            $playUrl = saveCleanM3u8($cacheId, $result['clean_content'], $videoLink, $config);
        }
    }

    return buildResult(200, '解析成功', $playUrl, $playUrl, $startTime);
}

/**
 * 构建统一结果数组
 */
function buildResult(int $code, string $zt, string $msg, ?string $url, float $startTime): array
{
    global $config;
    $elapsed = round(microtime(true) - $startTime, 3);

    return [
        'code' => $code,
        'ZT'   => $zt,
        'msg'  => $msg,
        'url'  => $url ?? '',
        'time' => $elapsed . 's',
        'KFZ'  => $config['developer']['name'] . '|' . $config['developer']['author'],
    ];
}


// ==================== 核心函数 ====================

/**
 * 调用官解接口获取视频直链
 */
function getVideoLinkFromOfficialApi(string $videoUrl, array $config): ?string
{
    foreach ($config['official_apis'] as $api) {
        $targetUrl = $api['url'] . urlencode($videoUrl);

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

        if ($error) {
            continue;
        }

        switch ($api['type']) {
            case 'redirect':
                if (preg_match('/\.(m3u8|mp4)(\?|$)/i', $effectiveUrl)) {
                    return $effectiveUrl;
                }
                $extracted = extractVideoUrl($response);
                if ($extracted) {
                    return $extracted;
                }
                break;

            case 'json':
                $data = json_decode($response, true);
                if ($data) {
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
                    $foundUrl = findUrlInArray($data);
                    if ($foundUrl) {
                        return $foundUrl;
                    }
                }
                break;

            case 'text':
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
 */
function resolveMultiLevelM3u8(string $content, string $m3u8Url, array $config): array
{
    if (strpos($content, '#EXTINF') !== false) {
        return ['content' => $content, 'url' => $m3u8Url];
    }

    if (preg_match_all('/#EXT-X-STREAM-INF[^#]*\n([^\n#]+)/', $content, $streams)) {
        $allStreams = [];
        foreach ($streams[1] as $streamUrl) {
            $streamUrl = trim($streamUrl);
            if (!preg_match('/^https?:\/\//i', $streamUrl)) {
                $streamUrl = dirname($m3u8Url) . '/' . ltrim($streamUrl, '/');
            }
            $allStreams[] = $streamUrl;
        }
        if (!empty($allStreams)) {
            $subUrl = end($allStreams);
            $subContent = fetchM3u8Content($subUrl, $config);
            if ($subContent) {
                return ['content' => $subContent, 'url' => $subUrl];
            }
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
    $data = [
        'content'      => $content,
        'original_url' => $originalUrl,
        'created_at'   => time(),
    ];
    file_put_contents($filePath, json_encode($data));

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
