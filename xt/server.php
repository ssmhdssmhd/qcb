<?php
/**
 * 超级嗅探 - 服务端核心
 *
 * 核心解析逻辑（两条通道）：
 *
 * 【官解通道 official】
 *   调用虾米官解接口（parse_internal_xiami）→ 返回 m3u8/mp4 直链
 *   → 下载 m3u8 → 规则引擎 + AI 去广告 → 输出可播放的链接
 *
 * 【官替通道 replace】
 *   从资源站中匹配对应视频 → AI 自动失败重试 + 智能匹配输出对应链接
 *   → 下载 m3u8 → AI 自动去广告 + 去插播 + 去水印 → 输出最终播放链接
 *
 * 本文件提供 parseVideo() 核心函数，由 api.php 调用并控制输出格式
 */

$config = require __DIR__ . '/config.php';
require_once __DIR__ . '/AdFilter.php';
require_once __DIR__ . '/PerformanceOptimizer.php';

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
 * 根据后台「嗅探设置」当前通道分流处理：
 *
 * 【官解通道 official】
 *   调用虾米官解接口 → 获取 m3u8/mp4 直链
 *   → 下载 m3u8 → 规则引擎 + AI 去广告 → 输出可播放的链接
 *
 * 【官替通道 replace】
 *   从资源站匹配视频 → AI 智能匹配 + 失败重试 → 输出对应链接
 *   → 下载 m3u8 → AI 去广告 + 去插播 + 去水印 → 输出最终播放链接
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
    //   - 官解通道 (official)：调用虾米官解接口，返回原始 m3u8/mp4 直链，需 xt 去广告
    //   - 官替通道 (replace) ：从资源站匹配 + AI 去广告/去插播/去水印，输出最终链接
    $sniffResult = getVideoLinkBySnifferMode($videoUrl, $config);
    $videoLink   = $sniffResult['url'];
    $sniffSource = $sniffResult['source'];  // 'official' | 'replace' | null

    if (!$videoLink) {
        return buildResult(500, '解析失败', '嗅探设置中当前通道未能解析出视频地址', null, $startTime);
    }

    // ============ 官替通道：从资源站匹配 + AI 去广告/去插播/去水印 ============
    // 流程：下载 m3u8 → 规则引擎 + AI 识别广告/插播/水印 → 生成去广告 m3u8 → 输出最终链接
    if ($sniffSource === 'replace') {
        return parseVideoByReplaceChannel($videoUrl, $videoLink, $cacheKey, $startTime);
    }

    // ============ 官解通道：虾米接口 + xt 去广告 ============
    // 流程：调用虾米接口 → 下载 m3u8 → 规则引擎 + AI 去广告 → 输出可播放链接
    return parseVideoByOfficialChannel($videoUrl, $videoLink, $cacheKey, $startTime);
}

/**
 * 官解通道处理：调用虾米接口获取直链 → xt 去广告 → 输出可播放链接
 *
 * 虾米接口返回的 play_url 是原始 m3u8/mp4 直链，需经过 xt 模块的
 * 规则引擎 + AI 去广告处理后输出最终可播放链接。
 *
 * @param string $videoUrl  原始视频页面 URL
 * @param string $videoLink 虾米接口返回的 m3u8/mp4 直链
 * @param string $cacheKey  缓存 key
 * @param float  $startTime 解析起始时间
 * @return array
 */
function parseVideoByOfficialChannel(string $videoUrl, string $videoLink, string $cacheKey, float $startTime): array
{
    global $config;

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

            // 规则引擎 + AI 去广告
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
 * 官替通道处理：从资源站匹配 → AI 去广告/去插播/去水印 → 输出最终播放链接
 *
 * 官替接口返回的是 mxjx 代理地址或资源站页面 URL，需要：
 *   1. 下载内容获取真正的 m3u8 直链
 *   2. 规则引擎 + AI 识别广告、插播片段、水印片段
 *   3. 生成去广告/去插播/去水印的清洁 m3u8
 *   4. 输出最终播放链接（clean.php 代理地址）
 *
 * @param string $videoUrl  原始视频页面 URL
 * @param string $videoLink 官替接口返回的链接（mxjx 代理或资源站 URL）
 * @param string $cacheKey  缓存 key
 * @param float  $startTime 解析起始时间
 * @return array
 */
function parseVideoByReplaceChannel(string $videoUrl, string $videoLink, string $cacheKey, float $startTime): array
{
    global $config;

    $finalUrl = $videoLink;

    // 步骤1：下载内容获取真正的 m3u8 直链
    $m3u8Content = fetchM3u8Content($videoLink, $config);

    if (!$m3u8Content) {
        // 下载失败，直接缓存原链接并返回
        setCache($cacheKey, ['url' => $finalUrl], $config);
        return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
    }

    // 步骤2：解析 master playlist 获取真实 TS 播放列表
    $resolved = resolveMultiLevelM3u8($m3u8Content, $videoLink, $config);
    $realM3u8Url = $resolved['url'];
    $realM3u8Content = $resolved['content'];

    // 如果解析后仍是代理地址，尝试从内容中提取真正的直链
    if (strpos($realM3u8Url, 'clean.php') !== false || strpos($realM3u8Url, 'mxjx') !== false) {
        $extracted = extractVideoUrl($m3u8Content);
        if ($extracted && filter_var($extracted, FILTER_VALIDATE_URL)) {
            $realM3u8Url = $extracted;
            // 重新下载真正的 m3u8 内容
            $realM3u8Content = fetchM3u8Content($realM3u8Url, $config) ?: $realM3u8Content;
        }
    }

    // 步骤3：判断是否为 m3u8 格式
    $isM3u8 = preg_match('/\.m3u8(\?|$)/i', $realM3u8Url) || strpos($realM3u8Content, '#EXTM3U') !== false;

    if (!$isM3u8) {
        // mp4 等直链，直接返回
        setCache($cacheKey, ['url' => $realM3u8Url], $config);
        return buildResult(200, '解析成功', $realM3u8Url, $realM3u8Url, $startTime);
    }

    // 步骤4：AI 自动去广告 + 去插播 + 去水印
    // 启用 AI 增强模式（强制启用 AI 处理水印和插播）
    $enhancedConfig = $config;
    if (empty($enhancedConfig['ai']['enabled'])) {
        // 官替通道临时启用 AI 增强识别（如果配置了 API key）
        if (!empty($enhancedConfig['ai']['api_key']) && $enhancedConfig['ai']['api_key'] !== 'YOUR_AI_API_KEY') {
            $enhancedConfig['ai']['enabled'] = true;
        }
    }

    $filter = new AdFilter($enhancedConfig);
    $result = $filter->process($realM3u8Content, $realM3u8Url);

    $cleanContent = convertRelativeToAbsolute($result['clean_content'], $realM3u8Url);

    // 步骤5：生成去广告/去插播/去水印的清洁 m3u8，输出最终播放链接
    $cacheId = generateCacheId();
    $finalUrl = saveCleanM3u8($cacheId, $cleanContent, $realM3u8Url, $enhancedConfig);

    setCache($cacheKey, ['url' => $finalUrl], $config);
    return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
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
 *   1. mode=official 且 official_apis 有启用 → 并发请求多个官解接口（竞速模式）
 *   2. mode=replace  且 replace_api.enabled=true  → 调用官替接口
 *   3. 当前通道失败时，自动 fallback 到另一通道
 *   4. 两个通道都失败 → 回退到旧的 official_apis 数组
 *
 * 性能优化：
 *   - AI 学习自动排序：根据成功率、平均耗时自动调整接口优先级
 *   - 多接口并发竞速：多个接口同时请求，最快成功的立即返回
 *   - 失败自动切换：一个接口被禁/失败，自动用下一个
 *
 * @param string $videoUrl 视频页面 URL
 * @param array  $config   全局配置
 * @return array {
 *     url:    string|null  视频直链 (m3u8/mp4)，失败返回 null
 *     source: string|null  实际命中通道 'official' | 'replace' | null
 * }
 */
function getVideoLinkBySnifferMode(string $videoUrl, array $config): array
{
    $sniffer  = $config['sniffer'] ?? [];
    $mode     = $sniffer['mode'] ?? 'official';
    $perfCfg  = $config['performance'] ?? [];

    // 初始化性能优化器
    static $optimizer = null;
    if ($optimizer === null) {
        $optimizer = new PerformanceOptimizer($config);
    }

    // 收集官解接口列表（支持多个）
    // 优先从 sniffer.official_apis 读取，其次从 sniffer.official_api 单接口转换
    $officialApis = [];
    if (!empty($sniffer['official_apis']) && is_array($sniffer['official_apis'])) {
        foreach ($sniffer['official_apis'] as $api) {
            if (!empty($api['enabled']) && !empty($api['url'])) {
                $officialApis[] = $api;
            }
        }
    }
    // 兼容单接口配置
    if (empty($officialApis) && !empty($sniffer['official_api'])) {
        $officialApi = $sniffer['official_api'];
        if (!empty($officialApi['enabled']) && !empty($officialApi['url'])) {
            $officialApis[] = $officialApi;
        }
    }
    // 兜底：旧的 official_apis 数组
    if (empty($officialApis) && !empty($config['official_apis'])) {
        $officialApis = $config['official_apis'];
    }

    // 官替接口（单接口）
    $replaceApi = $sniffer['replace_api'] ?? [];
    $replaceEnabled = !empty($replaceApi['enabled']) && !empty($replaceApi['url']);

    // AI 学习：按性能评分自动排序
    if (!empty($perfCfg['ai_sort_enabled']) && count($officialApis) > 1) {
        $officialApis = $optimizer->sortApisByScore($officialApis);
    }

    $maxConcurrent = $perfCfg['max_concurrent'] ?? 3;
    $timeout = $perfCfg['timeout'] ?? 15.0;
    $raceMode = !empty($perfCfg['race_mode']) && count($officialApis) > 1;

    // 1) 按当前模式优先尝试
    if ($mode === 'replace') {
        if ($replaceEnabled) {
            $link = callSingleApi($videoUrl, $replaceApi, $config);
            if ($link) return ['url' => $link, 'source' => 'replace'];
        }
        // 当前通道失败 → fallback 到官解
        if (!empty($officialApis)) {
            if ($raceMode) {
                $result = $optimizer->concurrentRaceRequest($officialApis, $videoUrl, $maxConcurrent, $timeout);
                if ($result['url']) return ['url' => $result['url'], 'source' => 'official'];
            } else {
                $link = callApisSequential($videoUrl, $officialApis, $config, $optimizer, $timeout);
                if ($link) return ['url' => $link, 'source' => 'official'];
            }
        }
    } else {
        // mode=official（默认）
        if (!empty($officialApis)) {
            if ($raceMode) {
                // 竞速模式：多接口并发，最快成功立即返回
                $result = $optimizer->concurrentRaceRequest($officialApis, $videoUrl, $maxConcurrent, $timeout);
                if ($result['url']) return ['url' => $result['url'], 'source' => 'official'];
            } else {
                $link = callApisSequential($videoUrl, $officialApis, $config, $optimizer, $timeout);
                if ($link) return ['url' => $link, 'source' => 'official'];
            }
        }
        // 当前通道失败 → fallback 到官替
        if ($replaceEnabled) {
            $link = callSingleApi($videoUrl, $replaceApi, $config);
            if ($link) return ['url' => $link, 'source' => 'replace'];
        }
    }

    // 2) 两个通道都未启用或都失败 → 回退到旧的 official_apis 数组
    if (!empty($config['official_apis'])) {
        $link = getVideoLinkFromOfficialApi($videoUrl, $config);
        if ($link) return ['url' => $link, 'source' => 'official'];
    }

    return ['url' => null, 'source' => null];
}

/**
 * 串行调用多个接口（失败自动切换到下一个）
 *
 * @param string                $videoUrl  视频页面 URL
 * @param array                 $apiList   接口列表
 * @param array                 $config    全局配置
 * @param PerformanceOptimizer  $optimizer 性能优化器（用于记录结果）
 * @param float                 $timeout   总超时时间
 * @return string|null
 */
function callApisSequential(string $videoUrl, array $apiList, array $config, PerformanceOptimizer $optimizer, float $timeout = 15.0): ?string
{
    $startTime = microtime(true);
    foreach ($apiList as $api) {
        $callStart = microtime(true);
        $link = callSingleApi($videoUrl, $api, $config);
        $callDuration = microtime(true) - $callStart;
        $apiName = $api['name'] ?? md5($api['url'] ?? 'unknown');
        if ($link) {
            $optimizer->recordApiResult($apiName, $callDuration, true);
            return $link;
        } else {
            $optimizer->recordApiResult($apiName, $callDuration, false);
        }
        if ((microtime(true) - $startTime) > $timeout) {
            break;
        }
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
                //    注意：m3u8_url 是资源站页面URL（非直链），ad_skip_url 是 mxjx 代理地址
                //    必须优先取 ad_skip_url，因为 m3u8_url 播放器无法直接播放
                if (!$url && !empty($data['success'])) {
                    $url = $data['ad_skip_url'] ?? $data['m3u8_url'] ?? null;
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
