<?php
/**
 * 超级嗅探 - 服务端核心
 *
 * 功能：
 *   1. 调用官解接口获取视频 m3u8/mp4 直链
 *   2. 下载 m3u8 内容，通过规则引擎 + AI 识别广告
 *   3. 生成去广告 m3u8 文件
 *   4. 缓存命中，加速重复解析
 *
 * 本文件提供 parseVideo() 核心函数，由 api.php 调用并控制输出格式
 */

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/AdFilter.php';

// 合并后台「嗅探设置」覆盖配置（sniffer_config.php 由后台写入）
$snifferConfigFile = __DIR__ . '/sniffer_config.php';
if (file_exists($snifferConfigFile)) {
    $snifferOverrides = require $snifferConfigFile;
    if (is_array($snifferOverrides) && !empty($snifferOverrides)) {
        if (isset($config['sniffer']) && is_array($config['sniffer'])) {
            $config['sniffer'] = array_merge($config['sniffer'], $snifferOverrides);
        } else {
            $config['sniffer'] = $snifferOverrides;
        }
    }
}

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

    if (empty($videoUrl) || !filter_var($videoUrl, FILTER_VALIDATE_URL)) {
        return buildResult(400, '解析失败', '链接格式不正确', null, $startTime);
    }

    // 检查缓存命中
    $cacheKey = md5($videoUrl);
    $cached = getCache($cacheKey, $config);
    if ($cached) {
        return buildResult(200, '解析成功', $cached['url'], $cached['url'], $startTime, true);
    }

    // 概率触发过期缓存清理
    maybeCleanExpiredCache($config);

    // 步骤1：根据嗅探设置选择走官解解析还是官替接口
    $videoLink = getVideoLinkBySnifferMode($videoUrl, $config);

    if (!$videoLink) {
        return buildResult(500, '解析失败', '嗅探设置中当前通道未能解析出视频地址', null, $startTime);
    }

    // 步骤2：判断格式，处理广告
    $isM3u8 = preg_match('/\.m3u8(\?|$)/i', $videoLink);
    $playUrl = $videoLink;

    if ($isM3u8) {
        $m3u8Content = fetchM3u8Content($videoLink, $config);

        if ($m3u8Content) {
            $resolved = resolveMultiLevelM3u8($m3u8Content, $videoLink, $config);
            if ($resolved['url'] !== $videoLink) {
                $videoLink = $resolved['url'];
                $m3u8Content = $resolved['content'];
            }

            $filter = new AdFilter($config);
            $result = $filter->process($m3u8Content, $videoLink);

            $cleanContent = convertRelativeToAbsolute($result['clean_content'], $videoLink);

            $cacheId = generateCacheId();
            $playUrl = saveCleanM3u8($cacheId, $cleanContent, $videoLink, $config);

            // 写入解析缓存（用 videoUrl 做 key，下次直接返回）
            setCache($cacheKey, ['url' => $playUrl], $config);
        }
    } else {
        // mp4 等直链也写入缓存
        setCache($cacheKey, ['url' => $playUrl], $config);
    }

    return buildResult(200, '解析成功', $playUrl, $playUrl, $startTime);
}

/**
 * 构建统一结果数组
 */
function buildResult(int $code, string $zt, string $msg, ?string $url, float $startTime, bool $fromCache = false): array
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
 * 根据后台「嗅探设置」选择走官解解析还是官替接口
 *
 * 路由规则：
 *   1. mode=official 且 official_api.enabled=true → 调用官解接口
 *   2. mode=replace  且 replace_api.enabled=true  → 调用官替接口
 *   3. 当前通道失败时，自动 fallback 到另一通道（若对方已启用）
 *   4. 两个通道都未启用时，回退到旧的 official_apis 数组
 *
 * @param string $videoUrl 视频页面 URL
 * @param array  $config   全局配置
 * @return string|null     视频直链 (m3u8/mp4)，失败返回 null
 */
function getVideoLinkBySnifferMode(string $videoUrl, array $config): ?string
{
    $sniffer  = $config['sniffer'] ?? [];
    $mode     = $sniffer['mode'] ?? 'official';
    $official = $sniffer['official_api'] ?? [];
    $replace  = $sniffer['replace_api']  ?? [];

    // 1) 按当前模式优先尝试
    if ($mode === 'replace') {
        if (!empty($replace['enabled'])) {
            $link = callSingleApi($videoUrl, $replace, $config);
            if ($link) return $link;
        }
        // 当前通道失败 → fallback 到官解
        if (!empty($official['enabled'])) {
            $link = callSingleApi($videoUrl, $official, $config);
            if ($link) return $link;
        }
    } else {
        // mode=official（默认）
        if (!empty($official['enabled'])) {
            $link = callSingleApi($videoUrl, $official, $config);
            if ($link) return $link;
        }
        // 当前通道失败 → fallback 到官替
        if (!empty($replace['enabled'])) {
            $link = callSingleApi($videoUrl, $replace, $config);
            if ($link) return $link;
        }
    }

    // 2) 两个通道都未启用或都失败 → 回退到旧的 official_apis 数组
    if (!empty($config['official_apis'])) {
        return getVideoLinkFromOfficialApi($videoUrl, $config);
    }

    return null;
}

/**
 * 调用单个接口（官解或官替）获取视频直链
 *
 * 接口配置结构：
 *   [
 *     'enabled'   => bool,
 *     'name'      => string,
 *     'url'       => string,  // 接口地址，会拼接 urlencode($videoUrl)
 *     'type'      => string,  // redirect / json / text
 *     'url_field' => string,  // json 类型时视频地址字段名
 *     'headers'   => array,
 *   ]
 *
 * @param string $videoUrl  视频页面 URL
 * @param array  $apiConfig 单个接口配置
 * @param array  $config    全局配置（用于读取 http 超时等参数）
 * @return string|null
 */
function callSingleApi(string $videoUrl, array $apiConfig, array $config): ?string
{
    // url 为空直接返回（保留 enabled 但未配置的情况）
    if (empty($apiConfig['url'])) {
        return null;
    }

    $api = [
        'name'      => $apiConfig['name'] ?? '未命名接口',
        'url'       => $apiConfig['url'],
        'type'      => $apiConfig['type'] ?? 'json',
        'url_field' => $apiConfig['url_field'] ?? '',
        'headers'   => $apiConfig['headers'] ?? [],
    ];

    return getVideoLinkFromApiEntry($videoUrl, $api, $config);
}

/**
 * 调用官解接口获取视频直链（旧逻辑，保留作为 fallback）
 *
 * 遍历 official_apis 数组，依次尝试，任一成功即返回
 */
function getVideoLinkFromOfficialApi(string $videoUrl, array $config): ?string
{
    foreach ($config['official_apis'] as $api) {
        $link = getVideoLinkFromApiEntry($videoUrl, $api, $config);
        if ($link) {
            return $link;
        }
    }
    return null;
}

/**
 * 调用单个 API 接口获取视频直链（核心请求 + 解析逻辑）
 *
 * 支持 redirect / json / text 三种接口类型，被以下两个函数复用：
 *   - getVideoLinkFromOfficialApi（遍历 official_apis 数组）
 *   - callSingleApi（嗅探设置中的官解/官替单接口）
 *
 * @param string $videoUrl 视频页面 URL
 * @param array  $api      单个接口配置（name/url/type/url_field/headers）
 * @param array  $config   全局配置（读取 http 超时等参数）
 * @return string|null
 */
function getVideoLinkFromApiEntry(string $videoUrl, array $api, array $config): ?string
{
    if (empty($api['url'])) {
        return null;
    }

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

    if ($error || $httpCode !== 200) {
        return null;
    }

    switch ($api['type'] ?? 'json') {
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
                // 1) 优先取配置的字段名
                if ($urlField && isset($data[$urlField])) {
                    $url = $data[$urlField];
                }
                // 2) 兼容官替接口返回结构 {success, m3u8_url, ad_skip_url}
                if (!$url && !empty($data['success'])) {
                    $url = $data['m3u8_url'] ?? $data['ad_skip_url'] ?? null;
                }
                // 3) 通用字段兜底
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
        $bestUrl = null;
        $maxBandwidth = 0;

        foreach ($streams[0] as $index => $fullMatch) {
            $streamUrl = trim($streams[1][$index]);
            if (!preg_match('/^https?:\/\//i', $streamUrl)) {
                $streamUrl = resolveRelativeUrl($streamUrl, $m3u8Url);
            }
            $allStreams[] = $streamUrl;

            if (preg_match('/BANDWIDTH=(\d+)/i', $fullMatch, $bw)) {
                if ($bw[1] > $maxBandwidth) {
                    $maxBandwidth = $bw[1];
                    $bestUrl = $streamUrl;
                }
            }
        }

        if (!empty($allStreams)) {
            $subUrl = $bestUrl ?? end($allStreams);
            $subContent = fetchM3u8Content($subUrl, $config);
            if ($subContent) {
                return ['content' => $subContent, 'url' => $subUrl];
            }
        }
    }

    return ['content' => $content, 'url' => $m3u8Url];
}

/**
 * 解析相对 URL 为绝对 URL
 */
function resolveRelativeUrl(string $relative, string $baseUrl): string
{
    if (preg_match('/^https?:\/\//i', $relative)) {
        return $relative;
    }

    $baseParts = parse_url($baseUrl);
    $baseDir = $baseParts['scheme'] . '://' . $baseParts['host']
        . (isset($baseParts['port']) ? ':' . $baseParts['port'] : '')
        . dirname($baseParts['path'] ?? '/') . '/';

    if (strpos($relative, '//') === 0) {
        return $baseParts['scheme'] . ':' . $relative;
    }

    if (strpos($relative, '/') === 0) {
        return $baseParts['scheme'] . '://' . $baseParts['host']
            . (isset($baseParts['port']) ? ':' . $baseParts['port'] : '')
            . $relative;
    }

    return rtrim($baseDir, '/') . '/' . ltrim($relative, '/');
}

/**
 * 将 m3u8 中所有相对路径的 ts/key 转为绝对路径
 */
function convertRelativeToAbsolute(string $m3u8Content, string $baseUrl): string
{
    $lines = explode("\n", $m3u8Content);
    $output = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if (empty($trimmed) || strpos($trimmed, '#') === 0) {
            $output[] = $line;
            continue;
        }

        if (preg_match('/^https?:\/\//i', $trimmed)) {
            $output[] = $line;
            continue;
        }

        $output[] = resolveRelativeUrl($trimmed, $baseUrl);
    }

    return implode("\n", $output);
}

/**
 * 生成缓存 ID
 */
function generateCacheId(): string
{
    return substr(md5(uniqid(mt_rand(), true)), 0, 16);
}

/**
 * 保存去广告 m3u8 到缓存文件
 */
function saveCleanM3u8(string $cacheId, string $content, string $originalUrl, array $config): string
{
    $cacheDir = $config['cache']['dir'];

    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
        @file_put_contents($cacheDir . '/.gitkeep', '');
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
 * 获取解析结果缓存
 */
function getCache(string $key, array $config): ?array
{
    if (!$config['cache']['enabled']) {
        return null;
    }

    $file = $config['cache']['dir'] . '/parse_' . $key . '.json';
    if (!file_exists($file)) {
        return null;
    }

    if (time() - filemtime($file) > $config['cache']['ttl']) {
        @unlink($file);
        return null;
    }

    $data = json_decode(file_get_contents($file), true);
    return $data ?: null;
}

/**
 * 设置解析结果缓存
 */
function setCache(string $key, array $data, array $config): void
{
    if (!$config['cache']['enabled']) {
        return;
    }

    $cacheDir = $config['cache']['dir'];
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    $file = $cacheDir . '/parse_' . $key . '.json';
    file_put_contents($file, json_encode($data));
}

/**
 * 概率触发过期缓存清理
 */
function maybeCleanExpiredCache(array $config): void
{
    if (!$config['cache']['enabled']) {
        return;
    }

    $prob = $config['cache']['auto_clean_prob'] ?? 5;
    if (mt_rand(1, 100) > $prob) {
        return;
    }

    $cacheDir = $config['cache']['dir'];
    if (!is_dir($cacheDir)) {
        return;
    }

    $ttl = $config['cache']['ttl'];
    $now = time();
    $files = glob($cacheDir . '/*.m3u8');
    $parseFiles = glob($cacheDir . '/parse_*.json');
    $allFiles = array_merge($files ?: [], $parseFiles ?: []);

    $expiredCount = 0;
    foreach ($allFiles as $file) {
        if ($now - filemtime($file) > $ttl) {
            @unlink($file);
            $expiredCount++;
        }
    }

    // 如果文件数超过上限，删除最旧的
    $maxFiles = $config['cache']['max_files'] ?? 500;
    $remaining = glob($cacheDir . '/*.m3u8');
    $remaining = $remaining ?: [];
    if (count($remaining) > $maxFiles) {
        usort($remaining, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        $toDelete = array_splice($remaining, 0, count($remaining) - $maxFiles);
        foreach ($toDelete as $file) {
            @unlink($file);
        }
    }
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
