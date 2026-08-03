<?php
/**
 * AI 自动学习引擎
 *
 * 功能：每隔几小时自动从指定资源站（默认如意资源站）获取热门/更新视频，
 * 提取 rym3u8 等指定 play_from 的播放地址，进行深度广告分析并自动更新规则。
 *
 * 与 ResourceSiteManager::runAutoLearn 的区别：
 *   - 按小时调度（interval_hours），更频繁
 *   - 仅针对指定资源站（target_sites），默认如意
 *   - 按 play_from 过滤（rym3u8），精准定位无广告源
 *   - 优先学习热门/更新视频（prefer_hot_videos）
 *   - 视频去重，避免重复学习同一链接
 *   - 深度分析：EnhancedAdRuleEngine + ProfessionalAdDetector
 */

require_once __DIR__ . '/ResourceSiteManager.php';
require_once __DIR__ . '/DomainRuleManager.php';
require_once __DIR__ . '/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/ProfessionalAdDetector.php';

class AiAutoLearner {
    /** @var ResourceSiteManager */
    private $siteManager;
    /** @var DomainRuleManager */
    private $ruleManager;
    /** @var array */
    private $config;
    /** @var string */
    private $configFile;
    /** @var string */
    private $stateFile;
    /** @var string */
    private $logFile;
    /** @var string */
    private $dedupFile;

    public function __construct($siteManager = null, $ruleManager = null) {
        $this->configFile = __DIR__ . '/ai_auto_learn_config.php';
        $this->stateFile  = __DIR__ . '/ai_auto_learn_state.php';
        $this->logFile    = __DIR__ . '/ai_auto_learn_logs.php';
        $this->dedupFile  = __DIR__ . '/ai_auto_learn_dedup.php';

        $this->siteManager = $siteManager ?: new ResourceSiteManager();
        $this->ruleManager = $ruleManager ?: new DomainRuleManager();
        $this->config = $this->loadConfig();
    }

    /**
     * 加载配置（合并默认值）
     */
    public function loadConfig() {
        $defaults = [
            'enabled' => false,
            'interval_hours' => 4,
            'target_sites' => ['如意'],
            'play_from_patterns' => ['rym3u8'],
            'videos_per_site' => 50,
            'max_sites_per_run' => 3,
            'min_segments' => 50,
            'max_ad_percentage' => 90,
            'max_exec_time_per_video' => 30,
            'prefer_hot_videos' => true,
            'dedup_retention_days' => 7,
            'min_learn_count_to_track' => 1,
            'access_key' => '',
            'last_run_time' => null,
        ];

        if (file_exists($this->configFile)) {
            $loaded = require $this->configFile;
            if (is_array($loaded)) {
                return array_merge($defaults, $loaded);
            }
        }
        return $defaults;
    }

    public function getConfig() {
        return $this->config;
    }

    public function saveConfig($newConfig) {
        $current = $this->config;
        foreach ($newConfig as $k => $v) {
            if ($k === 'target_sites' || $k === 'play_from_patterns') {
                if (is_array($v)) {
                    $current[$k] = array_values(array_filter($v));
                }
            } elseif ($k === 'enabled' || $k === 'prefer_hot_videos') {
                $current[$k] = (bool)$v;
            } elseif (in_array($k, ['interval_hours','videos_per_site','max_sites_per_run','min_segments','max_ad_percentage','max_exec_time_per_video','dedup_retention_days','min_learn_count_to_track'])) {
                $current[$k] = intval($v);
            } elseif ($k === 'access_key') {
                $current[$k] = (string)$v;
            }
        }
        $this->config = $current;
        return $this->writeConfigFile($current);
    }

    private function writeConfigFile($config) {
        $content = "<?php\n";
        $content .= "// AI 自动学习配置 - 由后台自动维护，无需手动编辑\n";
        $content .= "// 此功能针对频繁更新规则的资源站，每隔几小时自动从指定资源站获取热门/更新视频，\n";
        $content .= "// 提取 rym3u8 等指定 play_from 的地址进行深度广告分析并更新规则\n";
        $content .= "return " . var_export($config, true) . ";\n";
        $ok = @file_put_contents($this->configFile, $content);
        return $ok !== false;
    }

    /**
     * 判断是否到达执行时间
     */
    public function shouldRun() {
        if (empty($this->config['enabled'])) return false;
        $last = $this->config['last_run_time'] ?? null;
        if (empty($last)) return true;
        $intervalHours = max(1, intval($this->config['interval_hours'] ?? 4));
        $lastTs = strtotime($last);
        if ($lastTs === false) return true;
        return (time() - $lastTs) >= ($intervalHours * 3600);
    }

    /**
     * 获取状态信息
     */
    public function getStatus() {
        return [
            'enabled' => $this->config['enabled'],
            'should_run' => $this->shouldRun(),
            'last_run_time' => $this->config['last_run_time'] ?? null,
            'interval_hours' => $this->config['interval_hours'] ?? 4,
            'target_sites' => $this->config['target_sites'] ?? [],
            'play_from_patterns' => $this->config['play_from_patterns'] ?? [],
        ];
    }

    /**
     * 写日志
     */
    public function writeLog($message, $type = 'info') {
        $entry = [
            'time' => date('Y-m-d H:i:s'),
            'type' => $type,
            'message' => $message,
        ];
        $logs = [];
        if (file_exists($this->logFile)) {
            $logs = require $this->logFile;
            if (!is_array($logs)) $logs = [];
        }
        array_unshift($logs, $entry);
        $logs = array_slice($logs, 0, 100);
        $content = "<?php\n// AI 自动学习日志 - 自动生成\nreturn " . var_export($logs, true) . ";\n";
        @file_put_contents($this->logFile, $content);
    }

    public function getLogs($limit = 50) {
        if (!file_exists($this->logFile)) return [];
        $logs = require $this->logFile;
        if (!is_array($logs)) return [];
        return array_slice($logs, 0, $limit);
    }

    /**
     * 读取去重表
     */
    private function loadDedup() {
        if (!file_exists($this->dedupFile)) return [];
        $data = require $this->dedupFile;
        if (!is_array($data)) return [];
        return $data;
    }

    /**
     * 写去重表（清理过期记录）
     */
    private function saveDedup($dedup) {
        $retentionDays = max(1, intval($this->config['dedup_retention_days'] ?? 7));
        $cutoff = time() - ($retentionDays * 86400);
        $cleaned = [];
        foreach ($dedup as $url => $ts) {
            if ($ts > $cutoff) {
                $cleaned[$url] = $ts;
            }
        }
        $content = "<?php\n// AI 自动学习去重表 - 自动生成\nreturn " . var_export($cleaned, true) . ";\n";
        @file_put_contents($this->dedupFile, $content);
        return $cleaned;
    }

    /**
     * 更新最后运行时间
     */
    private function updateLastRunTime() {
        $this->config['last_run_time'] = date('Y-m-d H:i:s');
        $this->writeConfigFile($this->config);
    }

    /**
     * 过滤视频：按 play_from 匹配
     */
    private function filterVideosByPlayFrom($videos, $patterns) {
        if (empty($patterns)) return $videos;
        $filtered = [];
        foreach ($videos as $video) {
            $urls = $video['urls'] ?? [];
            $matchedUrls = [];
            foreach ($urls as $u) {
                $pf = strtolower($u['play_from'] ?? '');
                foreach ($patterns as $pat) {
                    if ($pf !== '' && stripos($pf, strtolower($pat)) !== false) {
                        $matchedUrls[] = $u;
                        break;
                    }
                }
            }
            if (!empty($matchedUrls)) {
                $video['urls'] = $matchedUrls;
                $video['first_url'] = $matchedUrls[0]['url'] ?? $video['first_url'] ?? '';
                $filtered[] = $video;
            }
        }
        return $filtered;
    }

    /**
     * 按热门/更新排序
     */
    private function sortByHotness($videos) {
        if (empty($this->config['prefer_hot_videos'])) return $videos;
        usort($videos, function($a, $b) {
            $ra = $a['remarks'] ?? '';
            $rb = $b['remarks'] ?? '';
            // 更新到xx集 的排前面
            $scoreA = $this->hotScore($ra);
            $scoreB = $this->hotScore($rb);
            return $scoreB - $scoreA;
        });
        return $videos;
    }

    private function hotScore($remarks) {
        if (empty($remarks)) return 0;
        $score = 0;
        // "更新至" 说明是连载中的热门剧
        if (stripos($remarks, '更新') !== false) $score += 50;
        if (stripos($remarks, '完结') !== false) $score += 20;
        // 集数越高越热门
        if (preg_match('/(\d+)/', $remarks, $m)) {
            $score += min(30, intval($m[1]));
        }
        // 高清/超清
        if (stripos($remarks, '高清') !== false || stripos($remarks, '超清') !== false) $score += 5;
        return $score;
    }

    /**
     * 从单个视频 URL 深度学习规则
     */
    private function learnFromVideo($videoUrl, $videoName, $options) {
        $startTime = microtime(true);
        $minSegments = $options['min_segments'] ?? 50;
        $maxAdPct = $options['max_ad_percentage'] ?? 90;
        $maxExec = $options['max_exec_time_per_video'] ?? 30;

        try {
            $parsedUrl = parse_url($videoUrl);
            $videoDomain = $parsedUrl['host'] ?? '';
            if (empty($videoDomain)) {
                return ['success' => false, 'message' => '无法解析域名'];
            }

            if (function_exists('memory_get_usage')) {
                $cur = @ini_get('memory_limit');
                if ($this->returnBytes($cur) < 256 * 1024 * 1024) {
                    @ini_set('memory_limit', '256M');
                }
            }
            if (function_exists('set_time_limit')) {
                @set_time_limit($maxExec + 10);
            }

            // 跟随 Master Playlist
            $mediaUrl = $this->resolveMasterPlaylist($videoUrl);

            $elapsed = microtime(true) - $startTime;
            if ($elapsed > $maxExec) {
                return ['success' => false, 'message' => '执行超时', 'domain' => $videoDomain];
            }

            if (!class_exists('M3U8Parser')) {
                require_once __DIR__ . '/../src/M3U8Parser.php';
            }
            $parser = new M3U8Parser();
            $parser->setMaxSegments(1000);
            $parser->setConnectTimeout(8);
            $parser->setTimeout($maxExec - 5);
            $playlist = $parser->parse($mediaUrl);
            unset($parser);

            $elapsed = microtime(true) - $startTime;
            if ($elapsed > $maxExec) {
                unset($playlist);
                return ['success' => false, 'message' => '解析超时', 'domain' => $videoDomain];
            }

            $segments = $playlist['segments'] ?? [];
            if (empty($segments) || count($segments) < $minSegments) {
                unset($playlist);
                return ['success' => false, 'message' => '片段数不足(' . count($segments) . ')', 'domain' => $videoDomain];
            }

            // 深度分析：EnhancedAdRuleEngine
            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true,
            ]);
            $engine->setDomain($videoDomain);
            $analysis = $engine->analyzeAllSegments($segments);
            unset($engine);

            $segCount = count($segments);
            unset($playlist);

            $adPct = $analysis['totalCount'] > 0
                ? ($analysis['adCount'] / $analysis['totalCount'] * 100)
                : 0;

            if ($adPct >= $maxAdPct) {
                unset($analysis);
                return ['success' => false, 'message' => '广告占比过高', 'domain' => $videoDomain, 'ad_percentage' => round($adPct, 2)];
            }

            // 专业级广告检测（补充）
            $proDetector = new ProfessionalAdDetector();
            $proResult = $proDetector->detect($segments);
            unset($proDetector, $segments);

            // 合并专业检测的广告簇到分析结果
            if (!empty($proResult['ad_clusters'])) {
                if (!isset($analysis['ad_clusters'])) {
                    $analysis['ad_clusters'] = [];
                }
                $analysis['ad_clusters'] = array_merge($analysis['ad_clusters'], $proResult['ad_clusters']);
            }
            unset($proResult);

            // 学习并更新域名规则
            $domainResult = $this->ruleManager->learnFromAnalysis($videoDomain, $analysis);
            unset($analysis);

            $elapsed = microtime(true) - $startTime;

            if ($domainResult) {
                return [
                    'success' => true,
                    'domain' => $videoDomain,
                    'video_name' => $videoName,
                    'segments_count' => $segCount,
                    'ad_percentage' => round($adPct, 2),
                    'rule_updated' => $domainResult,
                    'duration' => round($elapsed * 1000, 2),
                ];
            } else {
                return ['success' => false, 'message' => '规则学习失败', 'domain' => $videoDomain, 'duration' => round($elapsed * 1000, 2)];
            }
        } catch (Throwable $e) {
            $elapsed = microtime(true) - $startTime;
            $msg = $e->getMessage();
            if (strpos($msg, 'memory') !== false || strpos($msg, 'Allowed memory') !== false) {
                return ['success' => false, 'message' => '内存不足', 'domain' => $videoDomain ?? ''];
            }
            return ['success' => false, 'message' => $msg, 'duration' => round($elapsed * 1000, 2)];
        }
    }

    /**
     * 主入口：执行 AI 自动学习
     */
    public function run($options = []) {
        $startTime = microtime(true);

        try {
            if (empty($this->config['enabled'])) {
                return ['success' => false, 'message' => 'AI 自动学习未启用'];
            }

            $targetSites = $this->config['target_sites'] ?? ['如意'];
            $playFromPatterns = $this->config['play_from_patterns'] ?? ['rym3u8'];
            $videosPerSite = min(100, max(1, intval($options['videos_per_site'] ?? $this->config['videos_per_site'] ?? 50)));
            $maxSites = min(10, max(1, intval($options['max_sites'] ?? $this->config['max_sites_per_run'] ?? 3)));
            $minSegments = $this->config['min_segments'] ?? 50;
            $maxAdPct = $this->config['max_ad_percentage'] ?? 90;
            $maxExecPerVideo = $this->config['max_exec_time_per_video'] ?? 30;

            // 限定站点数
            $targetSites = array_slice($targetSites, 0, $maxSites);

            $dedup = $this->loadDedup();
            $dedupUpdated = false;

            $results = [];
            $totalLearned = 0;
            $totalFailed = 0;
            $totalSkipped = 0;
            $learnedDomains = [];

            foreach ($targetSites as $siteName) {
                $siteResult = [
                    'site' => $siteName,
                    'videos_checked' => 0,
                    'videos_learned' => 0,
                    'videos_failed' => 0,
                    'videos_skipped' => 0,
                    'domains' => [],
                    'details' => [],
                ];

                try {
                    $site = $this->siteManager->getSiteByName($siteName);
                    if (empty($site)) {
                        $siteResult['error'] = '资源站不存在: ' . $siteName;
                        $results[] = $siteResult;
                        continue;
                    }

                    // 获取视频列表（多取一些用于过滤后仍有足够样本）
                    $fetchLimit = min(500, $videosPerSite * 4);
                    $fetchResult = $this->siteManager->fetchVideos($site['api_url'], 1, $fetchLimit);

                    if (!$fetchResult['success']) {
                        $siteResult['error'] = $fetchResult['message'];
                        $results[] = $siteResult;
                        continue;
                    }

                    $videos = $fetchResult['videos'] ?? [];

                    // 按 play_from 过滤
                    $videos = $this->filterVideosByPlayFrom($videos, $playFromPatterns);

                    // 按热门排序
                    $videos = $this->sortByHotness($videos);

                    $learnedCount = 0;
                    foreach ($videos as $video) {
                        if ($learnedCount >= $videosPerSite) break;

                        $videoUrl = $video['first_url'] ?? '';
                        $videoName = $video['name'] ?? '未知';
                        if (empty($videoUrl)) continue;

                        $siteResult['videos_checked']++;

                        // 去重检查
                        $dedupKey = md5($videoUrl);
                        if (isset($dedup[$dedupKey])) {
                            $siteResult['videos_skipped']++;
                            $totalSkipped++;
                            continue;
                        }

                        $learnResult = $this->learnFromVideo($videoUrl, $videoName, [
                            'min_segments' => $minSegments,
                            'max_ad_percentage' => $maxAdPct,
                            'max_exec_time_per_video' => $maxExecPerVideo,
                        ]);

                        $detail = [
                            'name' => $videoName,
                            'url' => $videoUrl,
                            'play_from' => $video['urls'][0]['play_from'] ?? '',
                            'result' => $learnResult['success'] ? 'success' : 'fail',
                            'message' => $learnResult['message'] ?? '',
                            'domain' => $learnResult['domain'] ?? '',
                            'duration_ms' => $learnResult['duration'] ?? 0,
                        ];

                        if ($learnResult['success']) {
                            $siteResult['videos_learned']++;
                            $totalLearned++;
                            $learnedCount++;
                            $videoDomain = $learnResult['domain'] ?? '';
                            if ($videoDomain) {
                                if (!in_array($videoDomain, $learnedDomains)) {
                                    $learnedDomains[] = $videoDomain;
                                }
                                if (!isset($siteResult['domains'][$videoDomain])) {
                                    $siteResult['domains'][$videoDomain] = 0;
                                }
                                $siteResult['domains'][$videoDomain]++;
                            }
                            $detail['ad_percentage'] = $learnResult['ad_percentage'] ?? 0;
                            $detail['segments_count'] = $learnResult['segments_count'] ?? 0;
                        } else {
                            $siteResult['videos_failed']++;
                            $totalFailed++;
                        }

                        $siteResult['details'][] = $detail;

                        // 记录去重（无论成功失败都记录，避免短期内重复尝试）
                        $dedup[$dedupKey] = time();
                        $dedupUpdated = true;

                        unset($learnResult);
                        if (function_exists('gc_collect_cycles')) {
                            gc_collect_cycles();
                        }
                    }

                    unset($videos, $fetchResult);
                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }
                } catch (Throwable $e) {
                    $siteResult['error'] = $e->getMessage();
                }

                $siteResult['domains'] = array_keys($siteResult['domains']);
                $results[] = $siteResult;
            }

            // 保存去重表
            if ($dedupUpdated) {
                $this->saveDedup($dedup);
            }

            $this->updateLastRunTime();

            $duration = round(microtime(true) - $startTime, 2);
            $logMsg = sprintf(
                'AI自动学习完成：站点 %d，成功 %d，失败 %d，跳过 %d，耗时 %.1fs',
                count($targetSites), $totalLearned, $totalFailed, $totalSkipped, $duration
            );
            $this->writeLog($logMsg, $totalLearned > 0 ? 'info' : 'warning');

            return [
                'success' => true,
                'message' => 'AI 自动学习完成',
                'sites_processed' => count($targetSites),
                'total_learned' => $totalLearned,
                'total_failed' => $totalFailed,
                'total_skipped' => $totalSkipped,
                'learned_domains' => $learnedDomains,
                'duration_seconds' => $duration,
                'details' => $results,
            ];
        } catch (Throwable $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $this->writeLog('AI自动学习异常: ' . $e->getMessage(), 'error');
            return [
                'success' => false,
                'message' => 'AI 自动学习异常: ' . $e->getMessage(),
                'error_file' => basename($e->getFile()),
                'error_line' => $e->getLine(),
                'duration_seconds' => $duration,
            ];
        }
    }

    private function resolveMasterPlaylist($url) {
        if (!class_exists('M3U8Parser')) {
            require_once __DIR__ . '/../src/M3U8Parser.php';
        }
        $parser = new M3U8Parser();
        try {
            $playlist = $parser->parse($url);
            if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
                $firstVariant = $playlist['variants'][0]['uri'] ?? '';
                if ($firstVariant) {
                    $parsedUrl = parse_url($url);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (isset($parsedUrl['port'])) {
                        $baseUrl .= ':' . $parsedUrl['port'];
                    }
                    $pathDir = dirname($parsedUrl['path'] ?? '');
                    $pathDir = $pathDir === '.' ? '' : $pathDir;
                    if (strpos($firstVariant, '/') === 0) {
                        return $baseUrl . $firstVariant;
                    } else {
                        return $baseUrl . $pathDir . '/' . $firstVariant;
                    }
                }
            }
        } catch (Throwable $e) {
        }
        return $url;
    }

    private function returnBytes($val) {
        $val = trim($val);
        if ($val === '' || $val === '-1') return PHP_INT_MAX;
        $last = strtolower($val[strlen($val) - 1]);
        $val = (int)$val;
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }
}
