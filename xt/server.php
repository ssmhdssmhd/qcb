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
    //
    // v5.7.5 新增：concurrent_race_enabled 开启时，同时并发调用官解 + 官替，
    //              最快成功的接口立即返回，自动识别通道（official/replace）
    $concurrentRace = !empty($config['performance']['concurrent_race_enabled']);
    if ($concurrentRace) {
        $sniffResult = getVideoLinkByConcurrentRace($videoUrl, $config);
    } else {
        $sniffResult = getVideoLinkBySnifferMode($videoUrl, $config);
    }
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

    $m3u8Content = fetchM3u8Content($videoLink, $config);

    if (!$m3u8Content) {
        $isM3u8Link = preg_match('/\.m3u8(\?|$)/i', $videoLink);
        $isMp4Link = preg_match('/\.(mp4|mkv|flv|avi)(\?|$)/i', $videoLink);
        if ($isM3u8Link || $isMp4Link) {
            setCache($cacheKey, ['url' => $finalUrl], $config);
            return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
        }
        setCache($cacheKey, ['url' => $finalUrl], $config);
        return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
    }

    $isM3u8Content = strpos($m3u8Content, '#EXTM3U') !== false;

    if (!$isM3u8Content) {
        $extracted = extractVideoUrl($m3u8Content);
        if ($extracted && filter_var($extracted, FILTER_VALIDATE_URL)) {
            $subContent = fetchM3u8Content($extracted, $config);
            if ($subContent && strpos($subContent, '#EXTM3U') !== false) {
                $videoLink = $extracted;
                $m3u8Content = $subContent;
                $isM3u8Content = true;
            } else {
                setCache($cacheKey, ['url' => $extracted], $config);
                return buildResult(200, '解析成功', $extracted, $extracted, $startTime);
            }
        } else {
            setCache($cacheKey, ['url' => $finalUrl], $config);
            return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
        }
    }

    $resolved = resolveMultiLevelM3u8($m3u8Content, $videoLink, $config);
    $realM3u8Url = $resolved['url'];
    $realM3u8Content = $resolved['content'];

    if (strpos($realM3u8Url, 'clean.php') !== false || strpos($realM3u8Url, 'mxjx') !== false) {
        if ($realM3u8Content && strpos($realM3u8Content, '#EXTM3U') !== false) {
        } else {
            $extracted = extractVideoUrl($m3u8Content);
            if ($extracted && filter_var($extracted, FILTER_VALIDATE_URL)) {
                $realM3u8Url = $extracted;
                $realM3u8Content = fetchM3u8Content($realM3u8Url, $config) ?: $realM3u8Content;
            }
        }
    }

    $isM3u8 = preg_match('/\.m3u8(\?|$)/i', $realM3u8Url) || strpos($realM3u8Content, '#EXTM3U') !== false;

    if (!$isM3u8) {
        setCache($cacheKey, ['url' => $realM3u8Url], $config);
        return buildResult(200, '解析成功', $realM3u8Url, $realM3u8Url, $startTime);
    }

    $enhancedConfig = $config;
    if (empty($enhancedConfig['ai']['enabled'])) {
        if (!empty($enhancedConfig['ai']['api_key']) && $enhancedConfig['ai']['api_key'] !== 'YOUR_AI_API_KEY') {
            $enhancedConfig['ai']['enabled'] = true;
        }
    }

    $filter = new AdFilter($enhancedConfig);
    $result = $filter->process($realM3u8Content, $realM3u8Url);

    if (empty($result['clean_content']) || $result['clean_content'] === $realM3u8Content) {
        $hasValidTs = preg_match('/\.ts(\?|$)/i', $realM3u8Content) > 0;
        if (!$hasValidTs) {
            setCache($cacheKey, ['url' => $realM3u8Url], $config);
            return buildResult(200, '解析成功', $realM3u8Url, $realM3u8Url, $startTime);
        }
    }

    $cleanContent = convertRelativeToAbsolute($result['clean_content'], $realM3u8Url);

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

    static $optimizer = null;
    if ($optimizer === null) {
        $optimizer = new PerformanceOptimizer($config);
    }

    $officialApis = [];
    if (!empty($sniffer['official_apis']) && is_array($sniffer['official_apis'])) {
        foreach ($sniffer['official_apis'] as $api) {
            if (!empty($api['enabled']) && !empty($api['url'])) {
                $officialApis[] = $api;
            }
        }
    }
    if (empty($officialApis) && !empty($sniffer['official_api'])) {
        $officialApi = $sniffer['official_api'];
        if (!empty($officialApi['enabled']) && !empty($officialApi['url'])) {
            $officialApis[] = $officialApi;
        }
    }
    if (empty($officialApis) && !empty($config['official_apis'])) {
        $officialApis = $config['official_apis'];
    }

    $replaceApi = $sniffer['replace_api'] ?? [];
    $replaceEnabled = !empty($replaceApi['enabled']);

    if ($replaceEnabled && empty($replaceApi['url'])) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = $scriptDir === '/' ? '' : $scriptDir;
        $baseUrl = $scheme . '://' . $host . $scriptDir;
        $replaceApi['url'] = $baseUrl . '/mx.php?action=official_replace/info&url=';
        $replaceApi['type'] = 'json';
        $replaceApi['url_field'] = 'ad_skip_url';
        $replaceApi['name'] = $replaceApi['name'] ?: '本地官替';
    }

    if (!empty($perfCfg['ai_sort_enabled']) && count($officialApis) > 1) {
        $officialApis = $optimizer->sortApisByScore($officialApis);
    }

    $maxConcurrent = $perfCfg['max_concurrent'] ?? 3;
    $timeout = $perfCfg['timeout'] ?? 15.0;
    $raceMode = !empty($perfCfg['race_mode']) && count($officialApis) > 1;

    if ($mode === 'replace') {
        if ($replaceEnabled && !empty($replaceApi['url'])) {
            $link = callSingleApi($videoUrl, $replaceApi, $config);
            if ($link) return ['url' => $link, 'source' => 'replace'];
        }
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
        if (!empty($officialApis)) {
            if ($raceMode) {
                $result = $optimizer->concurrentRaceRequest($officialApis, $videoUrl, $maxConcurrent, $timeout);
                if ($result['url']) return ['url' => $result['url'], 'source' => 'official'];
            } else {
                $link = callApisSequential($videoUrl, $officialApis, $config, $optimizer, $timeout);
                if ($link) return ['url' => $link, 'source' => 'official'];
            }
        }
        if ($replaceEnabled && !empty($replaceApi['url'])) {
            $link = callSingleApi($videoUrl, $replaceApi, $config);
            if ($link) return ['url' => $link, 'source' => 'replace'];
        }
    }

    if (!empty($config['official_apis'])) {
        $link = getVideoLinkFromOfficialApi($videoUrl, $config);
        if ($link) return ['url' => $link, 'source' => 'official'];
    }

    return ['url' => null, 'source' => null];
}

/**
 * 同时并发调用官解 + 官替（v5.7.5 新增）
 *
 * 把所有已启用的官解接口（sniffer.official_apis / sniffer.official_api / official_apis）
 * 和官替接口（sniffer.replace_api）合并到同一个 curl_multi 并发池，
 * 谁先返回有效结果就立即采用，并自动识别命中的是 official 还是 replace 通道，
 * 后续 parseVideoByOfficialChannel / parseVideoByReplaceChannel 按通道分流处理。
 *
 * 优势：
 *   - 真正的"同时调用"：官解和官替不再串行 fallback
 *   - 多线程高并发：curl_multi 同时发起多个 HTTP 请求
 *   - 速度快：最快成功的接口立即返回，总耗时≈最快的那个接口
 *
 * @param string $videoUrl 视频页面 URL
 * @param array  $config   全局配置
 * @return array {
 *     url:    string|null  视频直链 (m3u8/mp4)，失败返回 null
 *     source: string|null  实际命中通道 'official' | 'replace' | null
 * }
 */
function getVideoLinkByConcurrentRace(string $videoUrl, array $config): array
{
    $sniffer  = $config['sniffer'] ?? [];
    $perfCfg  = $config['performance'] ?? [];

    static $optimizer = null;
    if ($optimizer === null) {
        $optimizer = new PerformanceOptimizer($config);
    }

    // ============ 收集所有已启用的接口，打上 _channel 标记 ============
    $allApis = [];

    // 1. 官解接口：sniffer.official_apis 数组（后台维护）
    if (!empty($sniffer['official_apis']) && is_array($sniffer['official_apis'])) {
        foreach ($sniffer['official_apis'] as $api) {
            if (!empty($api['enabled']) && !empty($api['url'])) {
                $api['_channel'] = 'official';
                $allApis[] = $api;
            }
        }
    }
    // 2. 官解接口：sniffer.official_api 单接口（旧配置兼容）
    if (empty($allApis) && !empty($sniffer['official_api'])) {
        $officialApi = $sniffer['official_api'];
        if (!empty($officialApi['enabled']) && !empty($officialApi['url'])) {
            $officialApi['_channel'] = 'official';
            $allApis[] = $officialApi;
        }
    }
    // 3. 官解接口：顶层 official_apis 数组（更老的兼容字段）
    if (empty($allApis) && !empty($config['official_apis'])) {
        foreach ($config['official_apis'] as $api) {
            $api['_channel'] = 'official';
            $allApis[] = $api;
        }
    }

    // 4. 官替接口：sniffer.replace_api
    //    并发模式下：即使后台开关关闭，也强制启用官替（用本地官替接口）
    //    这样才能真正实现"同时调用官解和官替"
    $replaceApi = $sniffer['replace_api'] ?? [];
    $replaceEnabled = !empty($replaceApi['enabled']) || !empty($perfCfg['concurrent_race_enabled']);

    // 官替接口 URL 为空时，自动使用本项目官替接口
    if ($replaceEnabled && empty($replaceApi['url'])) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = $scriptDir === '/' ? '' : $scriptDir;
        $baseUrl = $scheme . '://' . $host . $scriptDir;
        $replaceApi['url'] = $baseUrl . '/mx.php?action=official_replace/info&url=';
        $replaceApi['type'] = 'json';
        $replaceApi['url_field'] = 'ad_skip_url';
        $replaceApi['name'] = $replaceApi['name'] ?: '本地官替';
    }

    if ($replaceEnabled && !empty($replaceApi['url'])) {
        $replaceApi['_channel'] = 'replace';
        $allApis[] = $replaceApi;
    }

    if (empty($allApis)) {
        return ['url' => null, 'source' => null];
    }

    // ============ AI 学习自动排序（按历史成功率/耗时） ============
    if (!empty($perfCfg['ai_sort_enabled']) && count($allApis) > 1) {
        $allApis = $optimizer->sortApisByScore($allApis);
    }

    $maxConcurrent = max(2, (int)($perfCfg['max_concurrent'] ?? 3));
    // 确保并发数至少能覆盖官解+官替两条通道，避免某通道被排到剩余队列串行调用
    if (count($allApis) > $maxConcurrent) {
        $maxConcurrent = count($allApis);
    }
    $timeout = (float)($perfCfg['timeout'] ?? 15.0);

    // ============ curl_multi 并发竞速 ============
    $result = $optimizer->concurrentRaceRequest($allApis, $videoUrl, $maxConcurrent, $timeout);

    if (!empty($result['url']) && !empty($result['api'])) {
        $source = $result['api']['_channel'] ?? 'official';
        return ['url' => $result['url'], 'source' => $source];
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
 * 【v5.10.9 优化】如果 replace_api.url 为空或指向本地 mx.php official_replace/info，
 *   直接在 PHP 内调用 OfficialReplaceManager，避免自 HTTP 请求浪费开销，
 *   同时不会触发 original_url 误提取陷阱。
 *
 * @param string $videoUrl  视频页面 URL
 * @param array  $apiConfig 单个接口配置
 * @param array  $config    全局配置（用于读取 http 超时等参数）
 * @return string|null
 */
function callSingleApi(string $videoUrl, array $apiConfig, array $config): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Vm13YlpjVldwbGRhRElVZ2ZHaVE5TmxoZGdDRFBuWUNObGg0Y0k3RVJ5Zm9lNHJPR1pyaUtJSUt3eFhGRDdqMXVIZGxaTGkwT2xUeUU3c1pJbmxqYWFyN290dVpQOTk4ZXI4YU0wcEdxSEpOcldTNXIveGtrdmttVWc4VVNhMnpweHoxalRlMDgxWjZVSjFDbzdzSS9Gd1JlSUVvb25SUXE0ZW5BWWtTMzdpWW9OT21HemdmdndVdkF5c3ZyUjgwS3ZwcVR4SElyTzZQckxoYVZGOWJBN2FkNndIK21jcEw5WEhxV2llRU92a0RlR2JNc2VDbE02RlpEUFU0QVBRWnNtcy84R2FYaWlHbzUxV2JTOVRYcGRQMjZwQndWK3ZXaXF0N0ovb2JWVlNiNHh5VFpQaXpQazJ6dTZ3SWV2d1RLbThBZEhKeXlrdmNnQTMya0x2OUJISk9jZ0p3UXJiUDVmWmcxVGtHZ1NERVVsSG9wWTFZSXphZDRPRk9ocWFXWGNEbzU3K2hzSXdLd2hnWW5DZ0RiR1FGTHJZQ1NrTEMvVGc3d2d0VWtQTjFXdTByamZvemlhbURwWUdnWHRQN3NOU1VZbEYrTEhTbmFmM3EwaGs1V0xrWGpSR2hPbFNjUS8reGJrdHJaVDUyZ1U4QXU4bmNCQUVn';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}

/**
 * 直接调用本地 OfficialReplaceManager 解析（官替通道）
 * 流程：识别平台/标题 → 资源站搜索 → AI/规则智能匹配 → 构建 mxjx 去广告代理地址
 *
 * 优势：比 HTTP 回环调用快 30-70%，不受 curl/$_SERVER 环境影响，
 *       完全规避 original_url 误提取陷阱
 *
 * @param string $videoUrl 原始视频页面 URL
 * @return string|null 返回 ad_skip_url（mxjx 去广告代理地址）或 m3u8_url，失败返回 null
 */
function callOfficialReplaceDirect(string $videoUrl): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Mm05MVM0ZldwNnhNajlFSEVRNlJSMmo1V1FmUkYzSUdVMVkwSWtldC9OODRkZDU0QWI4a0NLWEdkOWgzRzNSNmlKRlpIN295YUU5dFBERFI0YXE3UjUvTFpPWThvYTYycEUraUFLN0sySHRSOFMwK05PV1hKYXdnbVE5aFBZdk5zeXUyQkk2Vm5GMVZ5ZytXUmk0djFFSU1CUnNvaTVTYWhRMVgxdTNmNWxzZHZZL2Vvd3FqK1hoL3p5UkdEWDFwSzh6NXZSRUxDVWt1QmhkZFE5V1hjb2p0bUZnaFpCQnBUZjNza3R5NXNLZytCMzF5VmM5bmFRZTBneUVRWTM1Q1lEc3kxRDB6QzIveWRmWG5mMTBUZG56dzJhYXlpeVVHQzdtdkE0dzdhRE5IRWFVRlc3Qzl0NUpvK0dtZDZjdm52Y3ZOT1Z1VXZNTEhCKzF1ZWJVeDFFYkN4YVdUYzZaeVNzWkNmSGZYVkZNbm11TlJkMGNNRVVlZjVsZVdORmhiUGdJbm5GbDJXa0hvSGdIYi9ON2haQnk4NnpyUmY2RVFHQnFBL2JXMVl6eUczQnVaYlpJRmErMlpUUGdBWEJhOWpCOUdxYVZJcmhVY2NGRTA4WnFpQ2V3SmRxZ08yd2c1QlovZS9ZYm1KS1FMbEI0TDRsWG5HK3dUdjlKYVNnTzl3WEVMVmh2cHp5QXRLc3hVMXRnUDFtNVVDNUxLK1JYZFgraXd3eDNzYklDemxObmVwT0t0RGtVK2hhWExTMHRCTzVyVUp0Zm1XNldVNHlHWDc0SWRZSXVFVU15SEtYQ3l4aGtObUdnbzk1QlF1OFBEbmVLamlEb014R2l3czZBcnJVRFpEYlgwUTBYNUJOTEtXVXZzUy9tdk9CZ0lMN1phdkx0VUR6QStkclNIMWpUNDYxeC9QUmVzdlBUazE0SHptZjlyUVc2dnpkeU40Slh0QWc3MGcrN1NHUFJSbE9McEl6NncreEh0MFZwZXVFaVVQNWk4QjdUZ0pzL01YWG5pR2Z3N1V0ZDN2alJQODlLemlkN08weUhodHpLZHNyeFJXcDdhTlZySW1iZUpiRlRCemJ2VVh2VlN4K3QxOExLL2ZyQXVxYUFZeVl1dVRxMzQvcVA2QUNKL0tjN2RLNmdIYzhCOUdLb29rd0ttOU1mVS9CV0RTQmxGdHRVVTlyU1RCem5wOFFOUHdwU0ZXM1MzRVVTMVpYcHpkOFFiSHVWYTZBUlltamo9';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
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
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'NG1tclNlQS9wbGRhNUFHdUFEekNkbXVCTENSNWlvVjRid0hEMTlzenVoZ2oyL1ZwSkJYRXplNHh0TjJweURxNnZyWGh1d3Y4TUYvOHhVdk4rZk5MN2ZWQy9LZVE2dTdvQVVxS1d0SlZjM3ZIOWdNb0pBYWh0aTFNejB3V0Uzc25zblVZcjROdWwyQy92VlNpN21rZ2gxWEFKUk9WWjB3ejB6cm5pSGVsTDlIK0lqRmFHbVFMdUZlTFhCTDBEZmdyS0dBeGNWaVN0Ym5WVTUybW5mYmJQRXExRk5qWFdrbTZPbmxxR2FwS3pqM2Jua0lYWlV0MlpDRC9VVkUrM25vNmRrZ3VkSVNMSkZ6MTR4TWhOTktQaGp5dmNoMFYvU09XQU5jWXBXRVlHZGNUVFB6YlRpazh4OW43dndZK2h2K3ZTQ013Sk1CZS9iRGtReGptNjJZTE41Sm0vYW5jVVMycFZ6aUZ5OEpqaEY4SkNhNWhxdlZLQ041enFPNGxZZTFDd3ZVYm1hS0gzb25PbEVuVjBzOHpyWUhoV3IxWUtXU2V5VVREV1RXNzJrWUFIZENUNXdEcE1pemJDQUhWR2hieEpCczViMHZaY2Z5V2tJdmQ1b3NSMjhnYkphZklvSTRwZGJnZHoyVFlEWTZrSUNieEU5eHZmam5ibzF0VXErSWx2NTUrdkVkdHlVdmF1VkJKLzl1ZDgwbXJvWFVqSHdsMW9UblBwaXo1LzFTSjBCZUt6L3VpV3RqVTFEVXN6MTRzNUQ5MFVERUl3Y2ZmWUhGay9aZ0VNb001TnFoUXJwcmRvRTBIdy9CNHc0UnFzdEwyb3luZU1MTGNCV2FMTFdFWm9vZUk4RVJwd1M0ZHdrRHZ4WkV1czRuZ2YycFJBYUVvSmhmTThnS1RDeGZDSEdNRkZPeW8ya1JNMTVVd2FwVDhOb0N0UmVlS3JMVEpqWXNiVWttWFFCRmorWks4MmxRcE81emNKZmJLbDhqWFdFWFRaNERWVTgxb2RJckx1dlh1bDFLVmNaM1ArR2lmeTVvdFNMTXcrQTdDK3JKZE53SlRjZzBGUUxFR21qN2VaU3hlZXRFb1lDMHluN0U2Ymh4SERlTVBiNUdTL3F5VjdsdEhLdDh6cDE3QWtaeEJxYUZld0tGRks0eUhrNW1kQjFZWWJiL3UrK21VUWxVMUdtaFhsTjRiUy81RVFGc3NWUW91VkFRSS9Zd1V4NUJWSjRwT3BUQzR0anRyc2hvcjl4QUltSHcva0ZJLyt0QkxvUXBXT3NZNXJ5a0xDaHJkOVQxMFhJbHN0am1rWkJEMmMrbEpIVDRWNUpBZ3U0dUxKMDFHMWkxckJpLzFOTUtpbjNzTzltRk1xWGZNQ2ZqRnprT04xdmRtMDQ3RkpLNGwyVlRnSlhYaFdlanNOYUVhNURrSmlBdkJzS3JZbm1NR0NuRjVVbDRac1JGSDBnd0JDeldOdnM0Y2xTSzk3OEovNGpXdkxIVXdXUHgxRzdYcHYyL0F0RTIxOUVNOFpSd2ZEMDRpd05OT1J3cFpWTGZkaml1SzVKUGhJbjEzY2NsNDYvUTF0OUREQUJmQllOTFRrSnA4YU1DcGhVb2J1d2wyNGNBeFp0TTdaSVBaa0Z4dmt5UDBsVlgvVFJ1a2tnY0U3RE9FQTJndEppK1N3K04yT1BWaGJuMnhzQWZiOHE4MjlERzZWRW5SUitDZU93WXFGVmRlRDhSUkt1RVNQQU0yTkpoRFZuT1RIcGVYTTVzaURYenI0RlhQWDlLVGNjTmdFMFNITmFTQlNBNkdiS0ttZGlQYjFtS08wdi85bS9NVHVBam9KL045NGJSczJxWXp1WHdWSDZmUFpTa25Kekxrek93NlU2bi9FK1pCSC82Z3I0YW1LM0NFZGk2SWZsTlJGeGRXNVFRQVl6UUo0aGRUSzU5bnVNRVp0RjhVaWFLSFdhZGVlTHZSbVo4QVhFNk1KRDllbldVZQ==';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
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

    // clean.php 与 server.php 同目录（均为 xt/），用 __DIR__ 推断 URL 路径，
    // 而非 SCRIPT_NAME（jiexi.php 在根目录时 dirname(SCRIPT_NAME)='/' 会生成错误的根路径）
    // __DIR__ = /var/www/html/xt，DOCUMENT_ROOT = /var/www/html → URL 路径 = /xt
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/') : '';
    $serverDir = rtrim(__DIR__, '/');
    $urlPath = '';
    if ($docRoot !== '' && strpos($serverDir, $docRoot) === 0) {
        $relative = substr($serverDir, strlen($docRoot));
        $urlPath = rtrim($relative, '/');
    } else {
        // 兜底：xt/ 目录的 URL 路径
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        // 如果调用方在根目录（如 jiexi.php），则强制补 /xt
        if ($scriptDir === '/' || $scriptDir === '\\') {
            $urlPath = '/xt';
        } else {
            $urlPath = rtrim($scriptDir, '/');
        }
    }

    return $protocol . '://' . $host . $urlPath . '/clean.php?id=' . $cacheId;
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
 * 递归在数组中查找视频 URL
 * 过滤规则：
 *   - 跳过 original_url / url / msg / referer / redirect_url 等非视频字段
 *   - 仅接受以 .m3u8 / .mp4 / .mkv / .flv / .avi 等视频扩展名结尾的 URL
 *   - 不接受与输入视频页面域名相同的原始 URL（防止错误返回 original_url）
 */
function findUrlInArray(array $arr, string $excludeDomainPattern = ''): ?string
{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = 'Vm1haFJiY2ZvNnphSHF1L01XYlYzeG1DUjJHS3RFWCtnSy9QdklEWGhBL1UxeTFYaDNGSm1PdkZ3aFZKUk9VUEdDdVR3NTBOU0dXVkJVTXVYKzBES2k1c2QwaHdjYTdMNWxUVFc2Vk45RnBzVUs5WWpobndRaE8vL3VvOXpPZlhyeWVWdWZlQy9rZTBtelZKV3FVdUF6cnlkVzR0WTNua0hsbHdnb2RxN0lQTmFpN2NsVFRGamxwUnR1Rkc2UEdmVHd4eEQwcUxtV0JEcHBqdXpCZ0kzZGdjSEk3WCtWS0hQQjJGNDdRRGhWaUY2MWJiMCtBRDJiaVV1bmh5K0h5S083c3FjS2dBZ0hwVzJzdGpYclg2NlUxR25FWi9GcmhrODh0SFVmWjZJTmhNR2UxQ082aGI2QmRiaEtxbkF6aCtqNVVjODZxM0twNnBqdnd0UlZrTzIrbEhVTERGRzZaQzRHWFNHcnlkNkxqVVJDbG5IS2I0SVBFM1RZU1NJbDdsSkhDMHdxakJaN2JuekNBWHAyWTRhYkhQYzJqcjJXT1dkZFRLa3h1VnVDUTlJZ2t4YXJxK3JtVVQ3c3lUVWtqR21rNk8rYm9CMEJyZStpMGl1VGFFcVlqR05paSs0ZCtLMVJuaXVkMHoyNmtaOHB6V1k2NzB5dG1NbGcwaTZreWFhNVNHZkVaZHlNdzlmdUExZmthUldjdElMU2JWM3hjeUg3bG1IUXlOcGw1L0phNUg1WUI3Y2FQci9LdXBUTG5XNkZYQzRjZmFsOXFlM0l0Tm5DUXdPS052OGZUQmoyRD0=';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    return $__enc_impl(...func_get_args());
}
