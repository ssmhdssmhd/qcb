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

$xtProxyMgr = null;
if (file_exists(__DIR__ . '/../proxy/ProxyManager.php')) {
    require_once __DIR__ . '/../proxy/ProxyManager.php';
    $xtProxyMgr = new ProxyManager(__DIR__ . '/../proxy/proxy_config.php');
    $xtProxyMgr->ensureProxyAvailable();
}

function xt_rate_limit_wait($apiHost, $minIntervalMs = 800) {
    $rateLimitDir = __DIR__ . '/tmp';
    if (!is_dir($rateLimitDir)) {
        @mkdir($rateLimitDir, 0755, true);
    }
    $lockFile = $rateLimitDir . '/ratelimit_' . md5($apiHost) . '.dat';

    $fp = @fopen($lockFile, 'c+');
    if (!$fp) {
        return;
    }

    if (flock($fp, LOCK_EX)) {
        $now = microtime(true);
        $lastTime = 0;
        $existing = @fread($fp, 1024);
        if ($existing !== false && is_numeric(trim($existing))) {
            $lastTime = (float)trim($existing);
        }

        $elapsedMs = ($now - $lastTime) * 1000;
        if ($elapsedMs < $minIntervalMs) {
            $waitUs = (int)(($minIntervalMs - $elapsedMs) * 1000);
            if ($waitUs > 0) {
                usleep($waitUs);
            }
        }

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, (string)microtime(true));
        fflush($fp);
        flock($fp, LOCK_UN);
    }

    fclose($fp);
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

    // 步骤1：调用官解接口获取视频直链
    $videoLink = getVideoLinkFromOfficialApi($videoUrl, $config);

    if (!$videoLink) {
        return buildResult(500, '解析失败', '所有官解接口均未能解析出视频地址', null, $startTime);
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
 * 调用官解接口获取视频直链
 */
function getVideoLinkFromOfficialApi(string $videoUrl, array $config): ?string
{
    global $xtProxyMgr;

    foreach ($config['official_apis'] as $api) {
        $targetUrl = $api['url'] . urlencode($videoUrl);
        $apiHost = parse_url($targetUrl, PHP_URL_HOST);
        $maxRetries = 3;
        $resultUrl = null;

        for ($retry = 0; $retry < $maxRetries; $retry++) {
            if ($retry > 0) {
                usleep(rand(300000, 800000));
            }
            xt_rate_limit_wait($apiHost, 800);

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

            $usedProxyId = null;
            if ($xtProxyMgr !== null && $xtProxyMgr->isEnabled()) {
                $proxy = $xtProxyMgr->getProxy();
                if ($proxy !== null) {
                    $usedProxyId = $proxy['id'] ?? null;
                    $proxyType = strtoupper($proxy['type']);
                    $proxyAuth = '';
                    if (!empty($proxy['username'])) {
                        $proxyAuth = urlencode($proxy['username']) . ':' . urlencode($proxy['password']) . '@';
                    }
                    curl_setopt($ch, CURLOPT_PROXY, "$proxyType://$proxyAuth{$proxy['host']}:{$proxy['port']}");
                }
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $error = curl_error($ch);
            curl_close($ch);

            $isBanned = ($httpCode === 500 || (is_string($response) && (
                stripos($response, 'ban') !== false
            )));

            if ($response !== false && $httpCode === 200 && !$isBanned) {
                if ($xtProxyMgr !== null && $usedProxyId !== null) {
                    $xtProxyMgr->markProxySuccess($usedProxyId);
                }
            } else {
                if ($xtProxyMgr !== null && $usedProxyId !== null) {
                    $xtProxyMgr->markProxyFailed($usedProxyId);
                }
                if ($isBanned && $xtProxyMgr !== null) {
                    $xtProxyMgr->ensureProxyAvailable();
                }
                if ($retry < $maxRetries - 1) {
                    continue;
                }
                break;
            }

            switch ($api['type']) {
                case 'redirect':
                    if (preg_match('/\.(m3u8|mp4)(\?|$)/i', $effectiveUrl)) {
                        $resultUrl = $effectiveUrl;
                    } else {
                        $extracted = extractVideoUrl($response);
                        if ($extracted) {
                            $resultUrl = $extracted;
                        }
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
                            $resultUrl = $url;
                        } else {
                            $foundUrl = findUrlInArray($data);
                            if ($foundUrl) {
                                $resultUrl = $foundUrl;
                            }
                        }
                    }
                    break;

                case 'text':
                    $trimmed = trim($response);
                    if (filter_var($trimmed, FILTER_VALIDATE_URL)) {
                        $resultUrl = $trimmed;
                    }
                    break;
            }

            if ($resultUrl !== null) {
                break 2;
            }
        }

        if ($resultUrl !== null) {
            return $resultUrl;
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
