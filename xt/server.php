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
require_once __DIR__ . '/../gz/Md5AdPlaceholderEngine.php';

// ====== jiami 分支：核心解析逻辑（findUrlInArray/callOfficialReplaceDirect/...）从加密 jiami_core.php 载入
//        若文件损坏或被篡改，HMAC 签名校验直接抛 RuntimeException 阻断调用。
//        修改业务逻辑 → main 分支改 xt/server.php / PerformanceOptimizer.php 对应明文函数，
//        再通过构建脚本重新生成 jiami_core.php 切到本分支提交。
$_jiami_core_file = __DIR__ . '/jiami_core.php';
if (!file_exists($_jiami_core_file)) {
    throw new \RuntimeException(
        '[jiami] 缺少 xt/jiami_core.php（加密核心文件）。请确保在 jiami 分支部署完整代码，或切换到 main 分支明文版。'
    );
}
$_jiami_meta = require_once $_jiami_core_file;
if (!is_array($_jiami_meta) || !isset($_jiami_meta['core_version'])) {
    throw new \RuntimeException('[jiami] xt/jiami_core.php 加载失败：格式不正确。请重新从 jiami 分支获取该文件。');
}
unset($_jiami_core_file, $_jiami_meta);

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

    // ===== v5.12 新增：本地官替快速通道（避免 HTTP 回环丢 step_trace）
    //   当嗅探配置 replace_api.enabled=true 且未指定远端 url（或 replace_api 指向本地 mx.php），
    //   直接实例化 OfficialReplaceManager，调用 resolve 拿完整结果（含 step_trace）。
    //
    //   v5.13 加固（B2）：对直调增加「预算时间保护」——FPM/nginx 超时一般是 30s，
    //   这里在 25s 处主动软中断，返回带 step_trace 的结构化失败 JSON，
    //   由后续 HTTP 官替/官解兜底；避免 nginx 直接输出 502 Bad Gateway HTML 给前端。
    $replaceDirect = null;
    $sniffer = $config['sniffer'] ?? [];
    $replaceApi = $sniffer['replace_api'] ?? [];
    $forceReplaceDirect = false;
    if (!empty($sniffer['mode']) && $sniffer['mode'] === 'replace' && !empty($replaceApi['enabled'])) {
        $replaceUrl = (string)($replaceApi['url'] ?? '');
        // 没填 URL 或填的就是本地 mx.php action，都判定为本地官替
        if ($replaceUrl === '' || (strpos($replaceUrl, 'official_replace/info') !== false) || (strpos($replaceUrl, 'official_replace/resolve') !== false)) {
            $forceReplaceDirect = true;
        }
    }

    // ===== v5.13 新增：官替直调时间预算保护（防止 PHP-FPM 超时导致 nginx 502）
    //   预算时间 = min(嗅探 performance.timeout, 25 秒)，留至少 5 秒给后续 fallback 通道
    $perfCfg = $config['performance'] ?? [];
    $directBudget = (float)($perfCfg['timeout'] ?? 15.0);
    if ($directBudget <= 0 || $directBudget > 25.0) $directBudget = 25.0;
    $directDeadline = $startTime + $directBudget;
    // 预先注入软超时到 OfficialReplaceManager（若它读取 ini_get('max_execution_time') 可感知）
    if (function_exists('ini_set') && !ini_get('safe_mode')) {
        $oldMaxExec = (int)ini_get('max_execution_time');
        if ($oldMaxExec === 0 || $oldMaxExec > (int)ceil($directBudget) + 10) {
            @ini_set('max_execution_time', (string)((int)ceil($directBudget) + 10));
        }
    }
    $replaceDirectFailReason = null;  // 用于失败摘要带出去

    // 只有明文版 V2 callOfficialReplaceDirectV2 存在（jiami_core 还没覆盖到）时才走本地直调
    if ($forceReplaceDirect && function_exists('callOfficialReplaceDirectV2')) {
        try {
            // 在全局空间声明 deadline，OfficialReplaceManager 内部若有长循环可读取并主动返回
            $GLOBALS['XT_REPLACE_DIRECT_DEADLINE'] = $directDeadline;
            $replaceDirect = callOfficialReplaceDirectV2($videoUrl, $config);
        } catch (\Throwable $e) {
            // 任何直调异常都不允许冒泡导致 FPM 崩溃 → 降级走 HTTP 官替
            $replaceDirectFailReason = sprintf('直调异常: %s (line %d)', $e->getMessage() ?: get_class($e), $e->getLine());
            $replaceDirect = ['direct' => false];
        }
    }

    $replaceDirectUsed = ($replaceDirect && !empty($replaceDirect['direct']));
    if ($replaceDirectUsed) {
        $orm = $replaceDirect['orm']; // OfficialReplaceManager resolve 完整结果
        $adSkipUrl = (string)($orm['ad_skip_url'] ?? '');
        $elapsedDirect = microtime(true) - $startTime;
        $directTimedOut = $elapsedDirect >= $directBudget - 0.5;  // 到达预算 99% 视为临近超时
        if ($directTimedOut && (!$orm || empty($orm['success']))) {
            // 直调超时，标记失败原因，继续 fallback 到 HTTP 官替通道
            $replaceDirectFailReason = sprintf('官替直调临近超时(%.1fs ≥ 预算%.1fs)，已降级走 HTTP 官替', $elapsedDirect, $directBudget);
            $replaceDirectUsed = false;
        }
    }

    if ($replaceDirectUsed) {
        $orm = $replaceDirect['orm'];
        $adSkipUrl = (string)($orm['ad_skip_url'] ?? '');
        if (!empty($orm['success']) && $adSkipUrl !== '') {
            // 写入解析缓存
            setCache($cacheKey, ['url' => $adSkipUrl], $config);
            $extras = [
                'channel'       => 'replace_direct',
                'source'        => 'replace',
                'video_title'   => $orm['video_title'] ?? '',
                'platform'      => $orm['platform'] ?? '',
                'site'          => $orm['site'] ?? '',
                'match_score'   => $orm['match_score'] ?? null,
                'base_title'    => $orm['base_title'] ?? '',
                'episode_num'   => $orm['episode_num'] ?? null,
                'match_method'  => $orm['match_method'] ?? '',
                'used_keyword'  => $orm['used_keyword'] ?? '',
                'step_trace'    => $orm['step_trace'] ?? [],
            ];
            return buildResult(200, '解析成功', $adSkipUrl, $adSkipUrl, $startTime, false, $extras);
        }
        // 本地官替失败：把详细错误 + step_trace 带出去（供前端时间线 UI 显示哪一步错）
        $failMsg = (string)($orm['message'] ?? '官替解析失败');
        if ($replaceDirectFailReason) $failMsg .= '；' . $replaceDirectFailReason;
        $extras = [
            'channel'      => 'replace_direct',
            'source'       => 'replace',
            'video_title'  => $orm['video_title'] ?? '',
            'platform'     => $orm['platform'] ?? '',
            'base_title'   => $orm['base_title'] ?? '',
            'search_keywords' => $orm['search_keywords'] ?? [],
            'step_trace'   => $orm['step_trace'] ?? [],
            'debug_info'   => [
                'successful_sites' => $orm['successful_sites'] ?? [],
                'failed_sites'     => array_slice($orm['failed_sites'] ?? [], 0, 20),
                'searched_sites'   => $orm['searched_sites'] ?? 0,
                'matched_sites'    => $orm['matched_sites'] ?? 0,
                'error_code'        => $orm['error_code'] ?? null,
                'replace_direct_fail_reason' => $replaceDirectFailReason,
                'replace_direct_elapsed'    => isset($elapsedDirect) ? round($elapsedDirect * 1000, 1) . 'ms' : null,
            ],
        ];
        return buildResult(500, '解析失败', $failMsg, null, $startTime, false, $extras);
    }

    // 步骤1：根据嗅探设置选择走官解解析还是官替接口
    //   - 官解通道 (official)：调用虾米官解接口，返回原始 m3u8/mp4 直链，需 xt 去广告
    //   - 官替通道 (replace) ：从资源站匹配 + AI 去广告/去插播/去水印，输出最终链接
    //   - 同时调用 (concurrent)：官解 + 官替同时并发请求，最快成功的立即返回（v5.13.4）
    //
    // v5.7.5 新增：concurrent_race_enabled 开启时，同时并发调用官解 + 官替，
    //              最快成功的接口立即返回，自动识别通道（official/replace）
    // v5.13.4 新增：mode=concurrent 作为第三选项，由后台嗅探设置直接选择，
    //               兼容旧配置：concurrent_race_enabled=true 且 mode=replace 时自动升级为 concurrent
    $snifferMode = $config['sniffer']['mode'] ?? 'concurrent';
    if ($snifferMode === 'replace' && !empty($config['performance']['concurrent_race_enabled'])) {
        // 旧配置兼容：mode=replace + concurrent_race_enabled=true → 自动升级为 concurrent
        $snifferMode = 'concurrent';
    }
    $concurrentRace = ($snifferMode === 'concurrent');
    if ($concurrentRace) {
        $sniffResult = getVideoLinkByConcurrentRace($videoUrl, $config);
    } else {
        $sniffResult = getVideoLinkBySnifferMode($videoUrl, $config);
    }
    $videoLink   = $sniffResult['url'];
    $sniffSource = $sniffResult['source'];  // 'official' | 'replace' | null

    if (!$videoLink) {
        // ===== v5.13 B4 修复：嗅探全部失败时，构造一条「嗅探诊断」时间线附加条目 =====
        // 让前端时间线明确告诉用户：当前模式是啥 / 启用了哪些接口 / 有没有官替直调失败
        $extras = [];
        $existingTrace = [];
        if (!empty($sniffResult['orm_full']['step_trace'])) {
            $existingTrace = $sniffResult['orm_full']['step_trace'];
            $extras['video_title'] = $sniffResult['orm_full']['video_title'] ?? '';
            $extras['platform']    = $sniffResult['orm_full']['platform'] ?? '';
            $extras['search_keywords'] = $sniffResult['orm_full']['search_keywords'] ?? [];
            $extras['debug_info'] = [
                'successful_sites' => $sniffResult['orm_full']['successful_sites'] ?? [],
                'failed_sites'     => array_slice($sniffResult['orm_full']['failed_sites'] ?? [], 0, 20),
                'searched_sites'   => $sniffResult['orm_full']['searched_sites'] ?? 0,
                'matched_sites'    => $sniffResult['orm_full']['matched_sites'] ?? 0,
            ];
        } else {
            $extras['debug_info'] = [];
        }

        // 收集当前嗅探配置 + 通道状态，作为时间线新增的最后一条展示
        $sniffer = $config['sniffer'] ?? [];
        $mode    = $sniffer['mode'] ?? 'official';
        $officialApisEnabled = [];
        if (!empty($sniffer['official_apis']) && is_array($sniffer['official_apis'])) {
            foreach ($sniffer['official_apis'] as $a) {
                if (!empty($a['enabled'])) $officialApisEnabled[] = ($a['name'] ?: '未命名官解') . '→' . (strlen($a['url'] ?? '') > 48 ? substr($a['url'],0,48).'…' : ($a['url'] ?? '无URL'));
            }
        }
        $offApiSingle = $sniffer['official_api'] ?? [];
        if (!empty($offApiSingle['enabled'])) $officialApisEnabled[] = ($offApiSingle['name'] ?: '默认官解') . '→' . (strlen($offApiSingle['url'] ?? '') > 48 ? substr($offApiSingle['url'],0,48).'…' : ($offApiSingle['url'] ?? '无URL'));

        $replaceApi = $sniffer['replace_api'] ?? [];
        $replaceEnabled = !empty($replaceApi['enabled']);
        $replaceUrl    = (string)($replaceApi['url'] ?? '');
        $replaceIsLocal = ($replaceUrl === '' || stripos($replaceUrl, 'official_replace/info') !== false || stripos($replaceUrl, 'official_replace/resolve') !== false);

        $perf = $config['performance'] ?? [];
        $overallStatus = 'fail';
        $overallTitle = '🕵 嗅探通道诊断（全部失败，点击展开详情）';
        $summaryLines = [];
        $summaryLines[] = '当前嗅探模式：' . ($mode === 'replace' ? '官替接口(replace) ✅推荐' : '官解解析(official)');
        $summaryLines[] = '并发竞速模式：' . (empty($perf['concurrent_race_enabled']) ? '关（串行 fallback）' : '开（官解+官替同时请求）');
        $summaryLines[] = '官解解析接口已启用 ' . count($officialApisEnabled) . ' 条：' . (count($officialApisEnabled) ? implode('；', $officialApisEnabled) : '⚠ 全部未启用——请在嗅探设置里至少勾一条「启用此接口」');
        $summaryLines[] = '官替接口：' . ($replaceEnabled ? ($replaceIsLocal ? '✅启用，未填 URL → 走本地直调 OfficialReplaceManager（比 HTTP 回环快 30-70%）' : '启用，走远端：' . (strlen($replaceUrl) > 48 ? substr($replaceUrl,0,48).'…' : $replaceUrl)) : '⚠ 未启用 → 官替通道不会被尝试');
        if (!empty($replaceDirectFailReason)) {
            $summaryLines[] = '官替直调失败原因：' . $replaceDirectFailReason;
        }
        if (isset($elapsedDirect)) {
            $summaryLines[] = '官替直调用时：' . round($elapsedDirect * 1000, 1) . 'ms（预算 ' . round(($directBudget ?? 0) * 1000, 0) . 'ms）';
        }

        // v5.13.2-C4 新增：从 PerformanceOptimizer::recordFailedApi 写入的全局变量读取每条官解接口的失败明细
        $failedApiReqs = [];
        if (!empty($GLOBALS['XT_FAILED_API_REQUESTS']) && is_array($GLOBALS['XT_FAILED_API_REQUESTS'])) {
            $failedApiReqs = $GLOBALS['XT_FAILED_API_REQUESTS'];
        }
        if (!empty($failedApiReqs)) {
            $summaryLines[] = '官解接口失败明细(' . count($failedApiReqs) . ' 条)：';
            foreach ($failedApiReqs as $i => $fr) {
                $one = '  ' . ($i + 1) . '. ' . ($fr['name'] ?? '未命名官解') . ' → ' . ($fr['reason'] ?? '未知原因') . '；HTTP=' . ($fr['http_code'] ?? 0) . '；resp_len=' . ($fr['response_len'] ?? 0);
                if (!empty($fr['biz_message'])) $one .= '；上游原消息=' . $fr['biz_message'];
                if (strlen($one) > 200) $one = substr($one, 0, 200) . '…';
                $summaryLines[] = $one;
            }
        }
        $summaryLines[] = '最终返回通道：嗅探所有通道均未得到有效播放地址（见下）';

        // 最后：给出可执行修复建议（与前端 B3 的建议一致但后端也给一份）
        $fixTips = [];
        if ($mode === 'official' && count($officialApisEnabled) === 0) $fixTips[] = '当前模式=官解但没启用任何官解接口 → 请切到官替 replace 模式（v5.11 推荐）';
        if ($mode === 'official' && !$replaceEnabled) $fixTips[] = '同时启用官替接口（即使不用也可作为 fallback 兜底）';
        if (count($officialApisEnabled) > 0) {
            foreach ($officialApisEnabled as $oe) {
                if (stripos($oe, '114.134.184.91') !== false || stripos($oe, ':9002') !== false) {
                    $fixTips[] = '⚠ 检测到配置了虾米官解（114.134.184.91:9002），该服务器 2026-08-14 起已加签名验证，任意请求都返回「验证失败!」→ 请取消勾选该官解接口的「启用」改走官替本地直调';
                }
            }
        }
        // v5.13.2-C4：按上游业务级错误自动出建议
        if (!empty($failedApiReqs)) {
            foreach ($failedApiReqs as $fr) {
                $bm = $fr['biz_message'] ?? '';
                if (stripos($bm, '验证失败') !== false && empty($fixTips)) {
                    $fixTips[] = '官解上游返回「验证失败!」，说明此服务器需要签名/白名单，未授权IP无法使用 → 切到官替 replace 模式 + 官替URL留空走本地直调即可。';
                    break;
                }
                if (($fr['http_code'] ?? 0) === 0 && empty($fixTips)) {
                    $fixTips[] = '官解 curl 连接失败（超时/连接被拒绝/IP不通）→ 请取消该外部官解接口启用，改走官替本地直调。';
                    break;
                }
            }
        }
        if ($mode === 'replace' && !$replaceIsLocal) $fixTips[] = '官替接口不要填远端地址，留空=本地直调（跳过 HTTP 回环，速度更快 + 避免再遇到 502/超时）';
        if (empty($fixTips)) $fixTips[] = '建议：把嗅探设置 → 官替接口 URL 留空（本地直调）+ 临时取消所有外部官解接口的启用，避免请求宕机服务器。';
        $summaryLines[] = '修复建议：' . implode('；', $fixTips);

        $diagnosticStep = [
            'title'      => $overallTitle,
            'status'     => $overallStatus,
            'summary'    => implode("\n", $summaryLines),
            'elapsed_ms' => (microtime(true) - $startTime) * 1000,
            'detail'     => [
                'sniffer_mode'              => $mode,
                'concurrent_race_enabled'   => !empty($perf['concurrent_race_enabled']),
                'official_apis_enabled'     => $officialApisEnabled,
                'replace_enabled'           => $replaceEnabled,
                'replace_url'               => $replaceUrl,
                'replace_url_is_local'      => $replaceIsLocal,
                'replace_direct_fail_reason' => $replaceDirectFailReason ?? null,
                'replace_direct_elapsed_ms' => isset($elapsedDirect) ? round($elapsedDirect * 1000, 1) : null,
                'direct_budget_ms'          => isset($directBudget) ? round($directBudget * 1000, 0) : null,
                'failed_api_requests'       => $failedApiReqs,
                'fix_tips'                  => $fixTips,
                'sniffer_source'            => $sniffSource,
                'fallback_channel_tried'    => $concurrentRace ? 'concurrent_race' : 'serial_fallback',
            ],
        ];
        $existingTrace[] = $diagnosticStep;
        $extras['step_trace']  = $existingTrace;

        // debug_info 也带上 B4 的失败摘要（旧的前端逻辑也能在失败摘要上看到）
        if (empty($extras['debug_info'])) $extras['debug_info'] = [];
        $extras['debug_info']['sniffer_diagnostic'] = [
            'mode'                      => $mode,
            'official_apis_enabled'     => count($officialApisEnabled),
            'replace_enabled'           => $replaceEnabled,
            'replace_direct_fail_reason' => $replaceDirectFailReason ?? null,
            'failed_api_requests'       => $failedApiReqs,
            'fix_tips'                  => $fixTips,
        ];

        // 失败消息用更具体的第一条 fix tip 替换默认的"当前通道未能解析"
        $failMsg = '嗅探设置中当前通道未能解析出视频地址';
        if (!empty($fixTips)) $failMsg = $fixTips[0];
        return buildResult(500, '解析失败', $failMsg, null, $startTime, false, $extras);
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
 * v5.12 新增：本地官替快速通道 V2（不走 HTTP，避免回环丢 step_trace）
 *   - 直接 require OfficialReplaceManager，调用 resolve 拿完整结果（含 step_trace）
 *   - 返回 ['direct'=>true, 'orm'=>$ormFullResult] 或 ['direct'=>false]
 *
 * 注意：函数名加 V2 后缀，避免与 jiami_core.php 里同名老签名函数冲突；
 *       jiami 分支升级 jiami_core.php 时需要同步提供 V2 版本（或重命名覆盖）。
 */
if (!function_exists('callOfficialReplaceDirectV2')) {
    function callOfficialReplaceDirectV2(string $videoUrl, array $config): array {
        static $loaded = false;
        if (!$loaded) {
            $ormFile = __DIR__ . '/../gz/OfficialReplaceManager.php';
            if (!file_exists($ormFile)) return ['direct' => false];
            @require_once $ormFile;
            if (!class_exists('OfficialReplaceManager')) return ['direct' => false];
            $loaded = true;
        }
        try {
            $mgr = new \OfficialReplaceManager();
            $result = $mgr->resolve($videoUrl);
            return ['direct' => true, 'orm' => is_array($result) ? $result : []];
        } catch (Throwable $e) {
            return [
                'direct' => true,
                'orm' => [
                    'success' => false,
                    'message' => '本地官替异常: ' . $e->getMessage(),
                    'error_code' => 'INTERNAL_ERROR',
                    'step_trace' => [[
                        'name' => 'replace_direct_exception',
                        'title' => '本地官替异常',
                        'status' => 'fail',
                        'summary' => get_class($e) . ': ' . $e->getMessage(),
                        'detail' => ['file' => $e->getFile(), 'line' => $e->getLine()],
                        'elapsed_ms' => null,
                        'ts' => microtime(true),
                    ]],
                ],
            ];
        }
    }
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

            // ===== v5.11 S4 接入：AI+MD5 非正片占位（保留所有段数 + 广告段替换为静音黑屏占位 ts）=====
            $placeholderContext = [
                'provider' => 'official_channel',
                'video_base_title' => '',
                'site_host' => (string)parse_url($videoLink, PHP_URL_HOST),
            ];
            $cleanM3u8Before = $result['clean_content'];
            $cleanContentMd5Passed = runMd5PlaceholderPass($cleanM3u8Before, $filter, $videoLink, $config, $placeholderContext);

            $cleanContent = convertRelativeToAbsolute($cleanContentMd5Passed, $videoLink);

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

    // ===== v5.11 S4 接入：AI+MD5 非正片占位（保留所有段数 + 广告段替换为静音黑屏占位 ts）=====
    $placeholderContext = [
        'provider' => 'replace_channel',
        'video_base_title' => '',
        'site_host' => (string)parse_url($realM3u8Url, PHP_URL_HOST),
    ];
    $cleanM3u8Before = $result['clean_content'];
    $cleanContentMd5Passed = runMd5PlaceholderPass($cleanM3u8Before, $filter, $realM3u8Url, $enhancedConfig, $placeholderContext);

    if (empty($cleanContentMd5Passed) || $cleanContentMd5Passed === $realM3u8Content) {
        $hasValidTs = preg_match('/\.ts(\?|$)/i', $realM3u8Content) > 0;
        if (!$hasValidTs) {
            setCache($cacheKey, ['url' => $realM3u8Url], $config);
            return buildResult(200, '解析成功', $realM3u8Url, $realM3u8Url, $startTime);
        }
    }

    $cleanContent = convertRelativeToAbsolute($cleanContentMd5Passed, $realM3u8Url);

    $cacheId = generateCacheId();
    $finalUrl = saveCleanM3u8($cacheId, $cleanContent, $realM3u8Url, $enhancedConfig);

    setCache($cacheKey, ['url' => $finalUrl], $config);
    return buildResult(200, '解析成功', $finalUrl, $finalUrl, $startTime);
}

/**
 * 构建统一结果数组
 * v5.12 新增：支持 $extras 透传（step_trace / debug_info 等）
 */
function buildResult(int $code, string $zt, string $msg, ?string $url, float $startTime, bool $fromCache = false, array $extras = []): array
{
    global $config;
    $elapsed = round(microtime(true) - $startTime, 3);

    $base = [
        'code' => $code,
        'ZT'   => $zt,
        'msg'  => $msg,
        'url'  => $url ?? '',
        'time' => $elapsed . 's',
        'KFZ'  => $config['developer']['name'] . '|' . $config['developer']['author'],
    ];
    if ($fromCache) $base['from_cache'] = true;
    foreach ($extras as $k => $v) {
        if (!array_key_exists($k, $base)) $base[$k] = $v;
    }
    return $base;
}

/**
 * v5.11 新增：AI + MD5 非正片占位二次处理
 *  - 读取 AdFilter 解析快照（带 is_ad 标记）；
 *  - 合并到 Md5AdPlaceholderEngine；
 *  - 生成保留全部段数 + 占位替换广告段的 m3u8（不会导致进度条中断、播放器断流）。
 * 返回字符串 m3u8 content。如果引擎不可用/异常，返回 fallbackContent。
 */
function runMd5PlaceholderPass(string $fallbackContent, AdFilter $filter, string $baseUrl, array $cfg, array $context = []): string
{
    try {
        $snap = $filter->getSnapshot();
        if (empty($snap['segments'])) return $fallbackContent;

        $md5Cfg = $cfg['md5_placeholder'] ?? [];
        if (!is_array($md5Cfg)) $md5Cfg = [];
        // 合并 AdFilter 的 AI 配置
        if (!empty($cfg['ai']) && is_array($cfg['ai'])) {
            if (empty($md5Cfg['mode']) && !empty($cfg['ai']['enabled'])) $md5Cfg['mode'] = 'auto';
        }
        if (!isset($md5Cfg['placeholder_mode'])) $md5Cfg['placeholder_mode'] = 'local_proxy';
        $engine = new Md5AdPlaceholderEngine($md5Cfg);

        // 把 AdFilter 的 segments 转成 Md5AdPlaceholderEngine 需要的 playlist 结构
        $playlist = ['segments' => []];
        $adIdx = [];
        foreach ($snap['segments'] as $seg) {
            $playlist['segments'][] = [
                'uri'      => $seg['url'] ?? '',
                'duration' => floatval($seg['duration'] ?? 0),
                'tags'     => $seg['extra_tags'] ?? [],
                'raw'      => $seg,
            ];
            if (!empty($seg['is_ad'])) {
                $adIdx[] = count($playlist['segments']) - 1;
            }
        }

        // Phase 1+2
        $res = $engine->process($playlist, $baseUrl, $context);

        // Phase 合并：把 AdFilter 已经判定的广告段也强制 placeholder（即使 MD5 引擎没命中），避免误放
        $segsFinal = $res['playlist']['segments'];
        $extraPlaceholder = 0;
        foreach ($adIdx as $i) {
            if (!isset($segsFinal[$i])) continue;
            $origDur = floatval($segsFinal[$i]['duration'] ?? 0);
            // 仅当其还没有被 placeholder（uri 还包含 ts/m3u8 正常地址）时才强制替换
            $curUri = $segsFinal[$i]['uri'] ?? '';
            if (strpos($curUri, 'action=placeholder_ts') !== false || strpos($curUri, 'data:video/mp2t') === 0) continue;
            if ($origDur <= 0) $origDur = floatval($snap['segments'][$i]['duration'] ?? 0);
            $abs = (string)(($snap['segments'][$i]['resolved_url'] ?? '') ?: $baseUrl);
            $scheme = 'http'; $host = '127.0.0.1'; $path = '';
            $p = parse_url($abs ?: $baseUrl);
            if ($p) {
                if (!empty($p['scheme'])) $scheme = $p['scheme'];
                if (!empty($p['host'])) $host = $p['host'];
                if (!empty($p['port'])) $host .= ':' . $p['port'];
                if (!empty($p['path'])) { $pp = dirname($p['path']); if ($pp && $pp !== '/' && $pp !== '.') $path = '/' . trim($pp, '/'); }
            } elseif ((PHP_SAPI !== 'cli') && !empty($_SERVER['HTTP_HOST'])) {
                $host = $_SERVER['HTTP_HOST'];
                if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')) $scheme = 'https';
            }
            $q = http_build_query([
                'action' => 'placeholder_ts',
                'd' => max(0.1, round($origDur, 3)),
                'i' => $i,
                'r' => 'adfilter_' . ($snap['segments'][$i]['ad_reason'] ?? 'rules')
            ]);
            $segsFinal[$i]['uri'] = "{$scheme}://{$host}{$path}/mx.php?{$q}";
            $extraPlaceholder++;
        }

        // 输出 m3u8：保留所有 tags + 所有段（不剔除），广告段替换为 placeholder
        $lines = ['#EXTM3U'];
        $lines[] = '#EXT-X-VERSION:3';
        $maxDur = 0;
        foreach ($segsFinal as $s) if (floatval($s['duration']) > $maxDur) $maxDur = floatval($s['duration']);
        $lines[] = '#EXT-X-TARGETDURATION:' . (int)ceil(max(1, $maxDur));
        if (!empty($snap['ext_key'])) $lines[] = $snap['ext_key'];
        $lines[] = '#EXT-X-MEDIA-SEQUENCE:0';
        foreach ($segsFinal as $idx => $seg) {
            $dur = floatval($seg['duration'] ?? 0);
            $extinfTitle = '';
            if (!empty($snap['segments'][$idx])) {
                $orig = $snap['segments'][$idx];
                $rawLine = $orig['extinf_line'] ?? '';
                if ($rawLine !== '' && preg_match('/#EXTINF:[\d.]+,(.*)/', $rawLine, $mm)) {
                    $extinfTitle = $mm[1];
                }
                if (!empty($orig['after_discontinuity'])) $lines[] = '#EXT-X-DISCONTINUITY';
                if (!empty($orig['daterange'])) $lines[] = $orig['daterange'];
                if (!empty($orig['extra_tags'])) foreach ($orig['extra_tags'] as $t) $lines[] = $t;
            }
            $lines[] = '#EXTINF:' . sprintf('%.6f', $dur) . ',' . $extinfTitle;
            $lines[] = $seg['uri'] ?? '';
        }
        $lines[] = '#EXT-X-ENDLIST';
        return implode("\n", $lines);
    } catch (Throwable $e) {
        // 任何异常都不中断原解析，直接回退到 AdFilter 输出的 clean 版
        return $fallbackContent;
    }
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
        $host = $_SERVER['HTTP_HOST'] ?? '';
        // CLI/后台脚本场景下 $_SERVER['HTTP_HOST'] 可能为空或格式不合法，
        // 退化为 127.0.0.1 + 显式 SCRIPT_NAME，避免出现 "localhost." 等非法域名
        if ($host === '' || PHP_SAPI === 'cli' || strpos($host, '.') === strlen($host) - 1 || !preg_match('/^[a-zA-Z0-9.\-:\[\]]+$/', $host)) {
            $host = '127.0.0.1';
        }
        $scriptDir = '';
        if (!empty($_SERVER['SCRIPT_NAME'])) {
            $sd = dirname($_SERVER['SCRIPT_NAME']);
            if ($sd && $sd !== '/' && $sd !== '.') {
                $scriptDir = '/' . trim($sd, '/');
            }
        }
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



