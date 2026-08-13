<?php
/**
 * 官替 API 管理器
 * 负责将官方视频平台链接（腾讯、爱奇艺、优酷等）替换为资源站 M3U8 地址
 */

require_once __DIR__ . '/ResourceSiteManager.php';
require_once __DIR__ . '/TitleNormalizer.php';
require_once __DIR__ . '/../pt/PtManager.php';

class OfficialReplaceManager {
    private $configFile;
    private $config;
    private $lastHttpError = '';
    private $lastHttpCode = 0;
    private $proxyManager = null;
    private $useProxyOnFirstTry = true;
    /** @var array step_trace: 每一步解析过程，用于前后端排错 */
    private $stepTrace = [];

    public function __construct() {
        $this->configFile = __DIR__ . '/official_replace_config.php';
        $this->loadConfig();
    }

    public function setProxyManager($proxyManager) {
        $this->proxyManager = $proxyManager;
    }

    public function setUseProxyOnFirstTry($use) {
        $this->useProxyOnFirstTry = (bool)$use;
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            $this->config = $this->getDefaultConfig();
            $this->saveConfig();
        }
    }

    private function getDefaultConfig() {
        return [
            'version' => '1.0',
            'update_date' => date('Y-m-d H:i:s'),
            'enabled' => true,
            'default_site' => '抖剧TV',
            'max_search_sites' => 40,
            'cache_ttl' => 3600,
            'platforms' => [
                [
                    'name' => '腾讯视频',
                    'domain' => 'v.qq.com',
                    'enabled' => true,
                    'pattern' => '/v\.qq\.com\/.*?(?:vid=|\/)([a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video_title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '爱奇艺',
                    'domain' => 'iqiyi.com',
                    'enabled' => true,
                    'pattern' => '/iqiyi\.com\/.*?([a-zA-Z0-9]{5,})/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .main_title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '优酷',
                    'domain' => 'youku.com',
                    'enabled' => true,
                    'pattern' => '/youku\.com\/.*?id_([a-zA-Z0-9=]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '芒果TV',
                    'domain' => 'mgtv.com',
                    'enabled' => true,
                    'pattern' => '/mgtv\.com\/.*?\/([a-zA-Z0-9]+)\.html/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .player-title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '哔哩哔哩',
                    'domain' => 'bilibili.com',
                    'enabled' => true,
                    'pattern' => '/bilibili\.com\/video\/(BV[a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video-title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '抖剧TV',
                    'domain' => '360kan.com',
                    'enabled' => true,
                    'pattern' => '/360kan\.com\/.*?\/(vod|play)\/[^\/]*?([a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
                    'priority' => 1
                ],
                [
                    'name' => '搜狐视频',
                    'domain' => 'sohu.com',
                    'enabled' => true,
                    'pattern' => '/sohu\.com\/.*?(\d+)\.shtml/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
                    'priority' => 2
                ],
                [
                    'name' => 'PP视频',
                    'domain' => 'pptv.com',
                    'enabled' => true,
                    'pattern' => '/pptv\.com\/showpage\/([a-zA-Z0-9_-]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
                    'priority' => 2
                ]
            ],
            'search_sites' => ['抖剧TV', '量子', '暴风', '非凡', '天影', '6度资源', '豆包', '猫眼', '索尼', '最大', 'OK资源', '快车', '闪电', '丫丫（鸭鸭）', '无尽', '速播', '红牛', '豪华', '光速', '蓝光', '魔都', '看看', '樱花', '好花', '电影天堂', '茅台', '13大众', '百度', '爱奇艺资', '牛牛6', '蓝志', '天逸', '如意', '天繁', '西瓜'],
            'match_threshold' => 65
        ];
    }

    public function getConfig() {
        return $this->config;
    }

    public function saveConfigData($config) {
        $config['update_date'] = date('Y-m-d H:i:s');
        $this->config = $config;
        return $this->saveConfig();
    }

    private function saveConfig() {
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * 官替 API 配置' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($this->config) . ';' . "\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    public function resolve($url) {
        $this->resetStepTrace();
        $tStart = microtime(true);
        $this->pushStep('start', '启动官替解析', 'info', "URL: {$url}", ['url' => $url]);

        try {
            if (empty($url)) {
                $this->pushStep('validate', '入参校验', 'fail', 'URL 为空');
                return ['success' => false, 'message' => 'URL不能为空', 'step_trace' => $this->getStepTrace()];
            }

            if (!$this->config['enabled']) {
                $this->pushStep('validate', '入参校验', 'fail', '官替功能已被后台禁用', ['config_enabled' => false]);
                return ['success' => false, 'message' => '官替功能已禁用', 'step_trace' => $this->getStepTrace()];
            }

            $t = microtime(true);
            $platform = $this->detectPlatform($url);
            if (!$platform) {
                $this->pushStep('detect_platform', '① 识别视频平台', 'fail', '域名未匹配到支持的平台', [
                    'host' => parse_url($url, PHP_URL_HOST) ?: '',
                ], (microtime(true) - $t) * 1000);
                return ['success' => false, 'message' => '不支持的视频平台', 'step_trace' => $this->getStepTrace()];
            }
            $this->pushStep('detect_platform', '① 识别视频平台', 'ok',
                "识别为 {$platform['name']}（{$platform['domain']}）",
                ['platform' => $platform],
                (microtime(true) - $t) * 1000);

            $t = microtime(true);
            $videoIds = $this->extractVideoId($url, $platform);
            $videoId = $videoIds['video_id'] ?? '';
            $coverId = $videoIds['cover_id'] ?? '';
            $this->pushStep('extract_id', '② 提取视频ID',
                ($videoId ? 'ok' : 'warn'),
                $videoId ? "video_id={$videoId}" . ($coverId ? " cover_id={$coverId}" : '') : '未能从 URL 中提取到 video_id，后续只能依赖页面抽取',
                ['video_ids' => $videoIds],
                (microtime(true) - $t) * 1000);

            $t = microtime(true);
            $videoInfo = $this->fetchVideoInfo($url, $platform, $videoIds);
            $videoTitle = '';

            if ($videoInfo && !empty($videoInfo['is_expired'])) {
                $this->pushStep('fetch_meta', '③ 获取官方页面信息', 'fail', '链接已失效（title命中过期词）',
                    ['video_title' => $videoInfo['title'] ?? ''],
                    (microtime(true) - $t) * 1000);
                return [
                    'success' => false,
                    'message' => '链接已失效',
                    'platform' => $platform['name'],
                    'step_trace' => $this->getStepTrace(),
                ];
            }

            if ($videoInfo && !empty($videoInfo['title'])) {
                $videoTitle = $videoInfo['title'];
            } elseif ($videoId) {
                $videoTitle = $videoId;
                $this->pushStep('fetch_meta', '③ 获取官方页面信息', 'warn',
                    "未从页面拿到正式 title，回退用 video_id={$videoId} 作为搜索标题",
                    ['video_info' => $videoInfo],
                    (microtime(true) - $t) * 1000);
            } else {
                $this->pushStep('fetch_meta', '③ 获取官方页面信息', 'fail',
                    'API / 页面HTML / 移动页 / URL 兜底四个来源都未能提取到标题',
                    ['video_info' => $videoInfo],
                    (microtime(true) - $t) * 1000);
                return [
                    'success' => false,
                    'message' => '无法获取视频信息',
                    'platform' => $platform['name'],
                    'step_trace' => $this->getStepTrace(),
                ];
            }

            $parsedInfo = $this->parseVideoTitle($videoTitle);
            $videoInfo['parsed'] = $parsedInfo;
            $videoInfo['base_title'] = $parsedInfo['base_title'];
            $videoInfo['season'] = $parsedInfo['season'];
            $videoInfo['season_num'] = $parsedInfo['season_num'];
            $videoInfo['episode'] = $parsedInfo['episode'];
            $videoInfo['episode_num'] = $parsedInfo['episode_num'];
            $videoInfo['part'] = $parsedInfo['part'];
            $videoInfo['version'] = $parsedInfo['version'];
            $videoInfo['video_id'] = $videoId;

            // ===== v5.11 新增：优先使用 extractRichMetaFromHtml 里的 base_title_guess（比 parseVideoTitle 更准，因为基于 description / og:type）=====
            if (!empty($videoInfo['episode_info']['base_title_guess'])
                && mb_strlen($videoInfo['episode_info']['base_title_guess']) >= 2
                && (empty($videoInfo['base_title']) || mb_strlen($videoInfo['base_title']) < 2)) {
                $videoInfo['base_title'] = $videoInfo['episode_info']['base_title_guess'];
            }
            if (!empty($videoInfo['episode_info']['subtitle_guess'])) {
                if (empty($videoInfo['episode']) || mb_strlen($videoInfo['episode']) < 3) {
                    $videoInfo['episode'] = $videoInfo['episode_info']['subtitle_guess'];
                }
                $videoInfo['episode_subtitle'] = $videoInfo['episode_info']['subtitle_guess'];
            }
            if (!empty($videoInfo['episode_info']['episode_name']) && empty($videoInfo['episode'])) {
                $videoInfo['episode'] = $videoInfo['episode_info']['episode_name'];
            }

            if (empty($videoInfo['episode_num']) && !empty($videoInfo['episode_info']['episode_num'])) {
                $videoInfo['episode_num'] = $videoInfo['episode_info']['episode_num'];
                $videoInfo['episode'] = $videoInfo['episode_info']['episode_name'] ?: ($videoInfo['episode'] ?? '第' . $videoInfo['episode_num'] . '集');
            }

            if (empty($videoInfo['total_episodes']) && !empty($videoInfo['episode_info']['total_episodes'])) {
                $videoInfo['total_episodes'] = $videoInfo['episode_info']['total_episodes'];
            }
            if (empty($videoInfo['description']) && !empty($videoInfo['description'] ?? '')) {
                // noop (兼容字段)
            }

            $this->pushStep('fetch_meta', '③ 获取官方页面信息', 'ok',
                "title={$videoTitle} · base_title=" . ($videoInfo['base_title'] ?? '') . ' · 第' . ((string)($videoInfo['episode_num'] ?? '?')) . '集',
                [
                    'video_title'   => $videoTitle,
                    'base_title'    => $videoInfo['base_title'] ?? '',
                    'season_num'    => $videoInfo['season_num'] ?? null,
                    'episode_num'   => $videoInfo['episode_num'] ?? null,
                    'episode_name'  => $videoInfo['episode'] ?? '',
                    'description'   => mb_substr($videoInfo['description'] ?? '', 0, 200),
                    'rich_meta_ok'  => !empty($videoInfo['episode_info']['base_title_guess']) || !empty($videoInfo['episode_info']['subtitle_guess']),
                ],
                (microtime(true) - $t) * 1000);

            $searchKeywords = $this->buildSearchKeywords($videoInfo, $platform);
            $this->pushStep('build_keywords', '④ 生成搜索关键词', 'ok',
                '共 ' . count($searchKeywords) . ' 个搜索词，优先使用分剧名/第X集组合',
                ['keywords' => $searchKeywords]);

            $searchResult = null;
            $usedKeyword = '';

            $tSearch = microtime(true);
            foreach ($searchKeywords as $ki => $keyword) {
                if (empty($keyword)) continue;
                $t1 = microtime(true);
                $result = $this->searchInSites($keyword);
                $videoCount = empty($result['videos']) ? 0 : count($result['videos']);
                $this->pushStep('search_site',
                    "⑤ 资源站搜索 关键词" . ($ki + 1) . "/" . count($searchKeywords),
                    $videoCount > 0 ? 'ok' : 'warn',
                    ($videoCount > 0 ? "命中 {$videoCount} 条，停止继续搜索" : "无结果") . " · 搜索词：{$keyword}",
                    [
                        'keyword'          => $keyword,
                        'videos_count'     => $videoCount,
                        'searched_sites'   => $result['searched_sites'] ?? 0,
                        'successful_sites' => $result['successful_sites'] ?? [],
                        'failed_sites'     => array_slice($result['failed_sites'] ?? [], 0, 10),
                    ],
                    (microtime(true) - $t1) * 1000);
                if ($result['success'] && !empty($result['videos'])) {
                    $searchResult = $result;
                    $usedKeyword = $keyword;
                    break;
                }
            }

            if (!$searchResult || empty($searchResult['videos'])) {
                $this->pushStep('search_site_final', '⑤ 资源站搜索（汇总）', 'fail',
                    "所有 " . count($searchKeywords) . " 个搜索词都没有命中资源站视频",
                    [
                        'search_keywords'  => $searchKeywords,
                        'successful_sites' => $searchResult['successful_sites'] ?? [],
                        'failed_sites'     => $searchResult['failed_sites'] ?? [],
                        'searched_sites'   => $searchResult['searched_sites'] ?? 0,
                    ],
                    (microtime(true) - $tSearch) * 1000);
                $this->logResolve($url, $platform['name'], $videoTitle, 0, '', '', false);
                return [
                    'success' => false,
                    'message' => '未找到匹配的资源',
                    'platform' => $platform['name'],
                    'video_title' => $videoTitle,
                    'base_title' => $videoInfo['base_title'],
                    'season' => $videoInfo['season'],
                    'episode' => $videoInfo['episode'],
                    'video_id' => $videoId,
                    'search_keywords' => $searchKeywords,
                    'site_matches' => [],
                    'matched_sites' => 0,
                    'successful_sites' => $searchResult['successful_sites'] ?? [],
                    'failed_sites' => $searchResult['failed_sites'] ?? [],
                    'searched_sites' => $searchResult['searched_sites'] ?? 0,
                    'step_trace' => $this->getStepTrace(),
                ];
            }

            $t = microtime(true);
            $aiMatchResult = $this->aiSmartMatch($videoInfo, $searchResult['videos']);
            $bestMatch = $aiMatchResult['best_match'] ?? null;
            $allMatches = $aiMatchResult['all_matches'] ?? [];
            $matchMethod = $aiMatchResult['method'] ?? 'rule_based';

            if (!$bestMatch) {
                $bestMatch = $this->findBestMatch($videoInfo, $searchResult['videos']);
                $allMatches = $this->findAllMatches($videoInfo, $searchResult['videos']);
                $matchMethod = 'rule_based_fallback';
            }

            // pt 引擎增强匹配
            if (!$bestMatch || ($bestMatch['score'] ?? 0) < 60) {
                try {
                    $ptManager = PtManager::getInstance();
                    $ptResult = $ptManager->resolve($url, $videoInfo, $searchResult['videos']);
                    if (!empty($ptResult['matches'])) {
                        $ptBest = $ptResult['best_match'];
                        if ($ptBest && (!$bestMatch || $ptBest['score'] > ($bestMatch['score'] ?? 0))) {
                            $bestMatch = [
                                'video' => $ptBest['video'],
                                'score' => $ptBest['score'],
                                'base_score' => $ptBest['score'],
                                'season_match' => true,
                                'site' => $ptBest['video']['site_name'] ?? $ptBest['video']['site'] ?? '',
                            ];
                            $allMatches = array_map(function($m) {
                                return [
                                    'video' => $m['video'],
                                    'score' => $m['score'],
                                    'base_score' => $m['score'],
                                    'season_match' => true,
                                    'site' => $m['video']['site_name'] ?? $m['video']['site'] ?? '',
                                ];
                            }, $ptResult['matches']);
                            $matchMethod = 'pt_' . $ptResult['adapter'];
                        }
                    }
                } catch (Throwable $e) {
                    $this->pushStep('pt_engine', 'Pt 引擎增强匹配', 'warn', '异常，已降级回规则匹配',
                        ['error' => $e->getMessage(), 'file' => basename($e->getFile()), 'line' => $e->getLine()]);
                }
            }

            $siteMatches = $this->groupMatchesBySite($allMatches);

            $this->pushStep('match', '⑥ AI+规则智能匹配最优候选',
                $bestMatch ? 'ok' : 'fail',
                $bestMatch
                    ? ("命中站点：" . ($bestMatch['site'] ?? '?') . " · score=" . round((float)($bestMatch['score'] ?? 0), 1) . " · method={$matchMethod} · 候选数=" . count($allMatches))
                    : ("匹配失败，所有候选分数均低于阈值 " . ($this->config['match_threshold'] ?? 60)),
                [
                    'match_method'        => $matchMethod,
                    'used_keyword'        => $usedKeyword,
                    'total_candidates'    => count($allMatches),
                    'top5'                => array_values(array_map(function($m) {
                        return [
                            'name'  => $m['video']['name'] ?? '',
                            'site'  => $m['site'] ?? ($m['video']['site_name'] ?? $m['video']['site'] ?? ''),
                            'score' => $m['score'] ?? null,
                            'id'    => $m['video']['id'] ?? null,
                        ];
                    }, array_slice($allMatches, 0, 5))),
                    'sites'               => array_keys($siteMatches),
                ],
                (microtime(true) - $t) * 1000);

            if (!$bestMatch) {
                $this->logResolve($url, $platform['name'], $videoTitle, 0, '', '', false);
                $fail = [
                    'success' => false,
                    'message' => '未找到匹配度足够的资源',
                    'platform' => $platform['name'],
                    'video_title' => $videoTitle,
                    'base_title' => $videoInfo['base_title'],
                    'season' => $videoInfo['season'],
                    'episode' => $videoInfo['episode'],
                    'video_id' => $videoId,
                    'used_keyword' => $usedKeyword,
                    'candidates' => array_slice($allMatches, 0, 5),
                    'site_matches' => $siteMatches,
                    'matched_sites' => count($siteMatches),
                    'successful_sites' => $searchResult['successful_sites'] ?? [],
                    'failed_sites' => $searchResult['failed_sites'] ?? [],
                    'searched_sites' => $searchResult['searched_sites'] ?? 0,
                    'step_trace' => $this->getStepTrace(),
                ];
                return $fail;
            }

            $targetEpisodeUrl = $bestMatch['video']['first_url'] ?? $bestMatch['video']['url'] ?? '';
            $targetEpisodeName = '';
            $allUrls = $bestMatch['video']['urls'] ?? [];
            $episodeFromPlaylist = false;

            $t = microtime(true);
            if (!empty($videoInfo['episode_num']) && !empty($allUrls)) {
                $epResult = $this->findEpisodeUrl($allUrls, $videoInfo['episode_num'], $videoInfo);
                if ($epResult) {
                    $targetEpisodeUrl = $epResult['url'];
                    $targetEpisodeName = $epResult['name'];
                    $episodeFromPlaylist = true;
                }
            }

            if (empty($targetEpisodeUrl) && !empty($allUrls)) {
                $firstEpisode = reset($allUrls);
                if (is_array($firstEpisode) && isset($firstEpisode['url'])) {
                    $targetEpisodeUrl = $firstEpisode['url'];
                    $targetEpisodeName = $firstEpisode['name'] ?? '';
                } elseif (is_string($firstEpisode)) {
                    $targetEpisodeUrl = $firstEpisode;
                }
            }

            $targetEpisodeUrl = preg_replace('/#.*$/', '', $targetEpisodeUrl);
            
            foreach ($allUrls as &$urlItem) {
                if (is_array($urlItem) && isset($urlItem['url'])) {
                    $urlItem['url'] = preg_replace('/#.*$/', '', $urlItem['url']);
                } elseif (is_string($urlItem)) {
                    $urlItem = preg_replace('/#.*$/', '', $urlItem);
                }
            }
            unset($urlItem);

            $this->pushStep('episode', '⑦ 按集数匹配单集链接',
                ($episodeFromPlaylist ? 'ok' : (empty($targetEpisodeUrl) ? 'fail' : 'warn')),
                $episodeFromPlaylist
                    ? ("按第{$videoInfo['episode_num']}集精准匹配：" . ($targetEpisodeName ?: $targetEpisodeUrl))
                    : (empty($targetEpisodeUrl)
                        ? '播放列表为空/未能匹配，无法生成最终 m3u8'
                        : ("未能按集数匹配，回退使用列表首项：" . ($targetEpisodeName ?: basename((string)parse_url($targetEpisodeUrl, PHP_URL_PATH))))),
                [
                    'target_ep_num'    => $videoInfo['episode_num'] ?? null,
                    'playlist_count'   => is_countable($allUrls) ? count($allUrls) : 0,
                    'episode_name'     => $targetEpisodeName,
                    'episode_from_playlist' => $episodeFromPlaylist,
                    'target_url_preview' => $targetEpisodeUrl ? substr($targetEpisodeUrl, 0, 150) : '',
                ],
                (microtime(true) - $t) * 1000);

            if (empty($targetEpisodeUrl)) {
                $this->logResolve($url, $platform['name'], $videoTitle, 0, $bestMatch['site'] ?? '', '', false);
                return [
                    'success' => false,
                    'message' => '匹配到的视频没有可用播放地址',
                    'step_trace' => $this->getStepTrace(),
                ];
            }

            $adSkipUrl = '';
            if (!empty($targetEpisodeUrl)) {
                $adSkipUrl = $this->buildAdSkipUrl($targetEpisodeUrl);
            }

            $this->pushStep('output', '⑧ 组装输出（ad_skip_url 已含 AI+MD5 占位链路）', 'ok',
                "m3u8_url=已生成 · ad_skip_url=已生成 · 广告占位在 mxjx/deep 子步骤执行",
                [
                    'm3u8_url_preview'     => substr($targetEpisodeUrl, 0, 150),
                    'ad_skip_url_preview'  => substr($adSkipUrl, 0, 150),
                ],
                (microtime(true) - $tStart) * 1000);

            $this->logResolve($url, $platform['name'], $videoTitle, $bestMatch['score'] ?? 0, $bestMatch['site'] ?? '', $targetEpisodeUrl, !empty($targetEpisodeUrl));

            return [
                'success' => true,
                'platform' => $platform['name'],
                'original_url' => $url,
                'video_title' => $bestMatch['video']['name'] ?: $videoTitle,
                'video_name' => $bestMatch['video']['name'] ?? '',
                'video_pic' => $bestMatch['video']['pic'] ?? '',
                'video_remarks' => $bestMatch['video']['remarks'] ?? '',
                'original_title' => $videoTitle,
                'base_title' => $videoInfo['base_title'],
                'season' => $videoInfo['season'],
                'season_num' => $videoInfo['season_num'],
                'episode' => $videoInfo['episode'],
                'episode_num' => $videoInfo['episode_num'],
                'part' => $videoInfo['part'],
                'version' => $videoInfo['version'],
                'video_id' => $videoId,
                'match_score' => $bestMatch['score'],
                'base_score' => $bestMatch['base_score'],
                'season_match' => $bestMatch['season_match'],
                'episode_match' => $episodeFromPlaylist || !empty($bestMatch['episode_match']),
                'site' => $bestMatch['site'],
                'video' => $bestMatch['video'],
                'm3u8_url' => $targetEpisodeUrl,
                'ad_skip_url' => $adSkipUrl,
                'target_episode' => $targetEpisodeName,
                'all_urls' => $allUrls,
                'episodes' => count($allUrls),
                'alternatives' => array_slice($allMatches, 1, 5),
                'all_matches' => $allMatches,
                'site_matches' => $siteMatches,
                'matched_sites' => count($siteMatches),
                'used_keyword' => $usedKeyword,
                'search_keywords' => $searchKeywords,
                'match_method' => $matchMethod,
                'ai_enabled' => true,
                'successful_sites' => $searchResult['successful_sites'] ?? [],
                'failed_sites' => $searchResult['failed_sites'] ?? [],
                'searched_sites' => $searchResult['searched_sites'] ?? 0,
                'request_time' => time(),
                'total_ms' => round((microtime(true) - $tStart) * 1000, 1),
                'step_trace' => $this->getStepTrace(),
            ];
        } catch (Throwable $e) {
            $this->pushStep('exception', '解析异常', 'fail',
                get_class($e) . ': ' . $e->getMessage(),
                [
                    'class' => get_class($e),
                    'message' => $e->getMessage(),
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
            $this->logResolve($url, '', '', 0, '', '', false);
            return [
                'success' => false,
                'message' => '处理异常: ' . $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR',
                'debug_info' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                ],
                'step_trace' => $this->getStepTrace(),
            ];
        }
    }

    private function buildAdSkipUrl($m3u8Url) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $rootDir = dirname(__DIR__);
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        $relativePath = '';
        if (!empty($documentRoot)) {
            $rootDirReal = realpath($rootDir);
            $docRootReal = realpath($documentRoot);
            if ($rootDirReal && $docRootReal && strpos($rootDirReal, $docRootReal) === 0) {
                $relativePath = substr($rootDirReal, strlen($docRootReal));
            }
        }
        if (empty($relativePath)) {
            $relativePath = '';
        }
        $selfUrl = $scheme . '://' . $host . $relativePath;
        return $selfUrl . '/mx.php?action=mxjx&deep=1&url=' . urlencode($m3u8Url);
    }

    private function logResolve($url, $platform, $title, $score, $site, $m3u8Url, $success) {
        try {
            $logDir = __DIR__ . '/../cache/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/official_replace_' . date('Y-m-d') . '.log';
            $logLine = sprintf(
                "[%s] %s | 平台: %s | 标题: %s | 匹配度: %.1f | 站点: %s | 成功: %s | URL: %s\n",
                date('Y-m-d H:i:s'),
                $success ? 'SUCCESS' : 'FAIL',
                $platform,
                $title,
                $score,
                $site,
                $success ? '是' : '否',
                $url
            );
            @file_put_contents($logFile, $logLine, FILE_APPEND);
        } catch (Throwable $e) {
        }
    }

    /**
     * 追加 step trace 记录（给 mxadmin 测试区时间线展示）
     *
     * @param string $name   步骤英文 key（稳定）
     * @param string $title  步骤标题（展示用中文）
     * @param string $status ok | warn | fail | info
     * @param string|array $summary 一句话摘要（展示用字符串，也支持结构化数组）
     * @param array  $detail 展开可见的详情（可长）
     * @param float  $elapsedMs 可选：本步耗时（ms）
     */
    private function pushStep($name, $title, $status, $summary = '', $detail = [], $elapsedMs = null) {
        $this->stepTrace[] = [
            'name'      => (string)$name,
            'title'     => (string)$title,
            'status'    => in_array($status, ['ok','warn','fail','info'], true) ? $status : 'info',
            'summary'   => is_array($summary) ? json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$summary,
            'detail'    => is_array($detail) ? $detail : ['raw' => (string)$detail],
            'elapsed_ms'=> $elapsedMs === null ? null : round((float)$elapsedMs, 2),
            'ts'        => microtime(true),
        ];
    }

    /** 重置 step trace（一般在 resolve 开头调用） */
    private function resetStepTrace() {
        $this->stepTrace = [];
    }

    /** 取出 step_trace 数组快照 */
    public function getStepTrace() {
        return $this->stepTrace;
    }

    private function buildSearchKeywords($videoInfo, $platform) {
        $keywords = [];
        $baseTitle = $videoInfo['base_title'] ?? '';
        $seasonNum = $videoInfo['season_num'] ?? null;
        $version = $videoInfo['version'] ?? '';
        $part = $videoInfo['part'] ?? '';
        $videoTitle = $videoInfo['title'] ?? '';
        $episodeNum = $videoInfo['episode_num'] ?? null;
        $episodeSubtitle = $videoInfo['episode_subtitle'] ?? '';
        $episodeStr = $videoInfo['episode'] ?? '';

        // ===== v5.11 新增：最高优先级——官方页面解析出的精准组合词 = base_title + 第X集 + 分剧名(如"张启山和吴老狗达成合作") =====
        // 资源站很多资源都命名为"九门 第2集 张启山和吴老狗达成合作"，这种搜索词命中率 80%+
        if (!empty($baseTitle)) {
            if ($episodeNum) {
                $kw = $baseTitle . ' 第' . $episodeNum . '集';
                if ($episodeSubtitle) {
                    $keywords[] = $kw . ' ' . $episodeSubtitle;
                }
                $keywords[] = $kw;
                // v5.12 修复：$episodeStr 有时被赋成分剧名(副标题)，不是"第X集"的形式，
                //   这时直接 $baseTitle.' '.$episodeStr 会导致副标题重复拼接。
                //   只在 $episodeStr 是「集数相关描述」（包含"第X集"/"第X话"/长度较短且含数字）时才保留。
                if ($episodeStr && $episodeStr !== $kw && mb_strlen($episodeStr) <= 60) {
                    $episodeStrLooksLikeEp = (bool)preg_match('/第\s*\d+\s*[集话期部季]/u', $episodeStr)
                        || (bool)preg_match('/\bEP\s*\d+/ui', $episodeStr)
                        || (is_numeric(trim($episodeStr)) && mb_strlen(trim($episodeStr)) <= 4);
                    if ($episodeStrLooksLikeEp) {
                        $candidate = $baseTitle . ' ' . $episodeStr;
                        if (!in_array($candidate, $keywords, true)) $keywords[] = $candidate;
                    }
                }
            } elseif ($episodeSubtitle) {
                $keywords[] = $baseTitle . ' 第1集 ' . $episodeSubtitle; // 兜底：可能是第1集没数字
                $keywords[] = $baseTitle . ' ' . $episodeSubtitle;
            } else {
                $keywords[] = $baseTitle . ' 全集';
            }
        }
        // v5.11 把分剧名本身也作为 fallback 搜索词（资源站可能直接按"张启山和吴老狗达成合作"命名 90% 命中率）
        if ($episodeSubtitle && !in_array($episodeSubtitle, $keywords, true)) {
            $keywords[] = $episodeSubtitle;
            // v5.12 修复：无空格版本必须有意义，避免产生 "九门第张启山..." 这种垃圾词。
            //   - 有集数时 → base + 第X集 + subtitle（无空格），如「九门第2集张启山和吴老狗达成合作」
            //   - 无集数时 → base + subtitle（无空格），如「狂飙第39集」这种也很少出现，只在 base=2字以内启用
            if (!empty($baseTitle)) {
                if ($episodeNum) {
                    $nospace = $baseTitle . '第' . $episodeNum . '集' . $episodeSubtitle;
                } else {
                    $nospace = (mb_strlen($baseTitle) <= 3) ? ($baseTitle . $episodeSubtitle) : '';
                }
                if ($nospace && !in_array($nospace, $keywords, true) && mb_strlen($nospace) <= 40) $keywords[] = $nospace;
            }
        }

        // 以 video_title 和 base_title 为准，作为最优先搜索词
        if (!empty($videoTitle) && $videoTitle !== ($baseTitle ?? '')) {
            $keywords[] = $videoTitle;
        }
        if (!empty($baseTitle)) {
            $keywords[] = $baseTitle;
        }

        if (!empty($baseTitle)) {
            $normalizedBase = TitleNormalizer::normalize($baseTitle);
            if ($normalizedBase && $normalizedBase !== $baseTitle) {
                $keywords[] = $normalizedBase;
            }

            // 添加去标点版本
            $noPunctTitle = preg_replace('/[:：,，\s]+/u', '', $baseTitle);
            if ($noPunctTitle && $noPunctTitle !== $baseTitle) {
                $keywords[] = $noPunctTitle;
            }

            // 提取主标题（冒号、破折号前）
            if (preg_match('/^(.+?)[:：]/u', $baseTitle, $mainMatch)) {
                $mainTitle = trim($mainMatch[1]);
                if (mb_strlen($mainTitle) >= 2) {
                    $keywords[] = $mainTitle;
                }
            }

            if ($seasonNum) {
                $cnNum = $this->numberToChinese($seasonNum);
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '季';
                $keywords[] = $baseTitle . '第' . $seasonNum . '季';
                if ($cnNum) {
                    $keywords[] = $baseTitle . ' 第' . $cnNum . '季';
                    $keywords[] = $baseTitle . '第' . $cnNum . '季';
                }
                $keywords[] = $baseTitle . $seasonNum;
                $keywords[] = $baseTitle . ' S' . $seasonNum;
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '部';
                if ($seasonNum == 2) {
                    $keywords[] = $baseTitle . ' 第二季';
                    $keywords[] = $baseTitle . 'Ⅱ';
                } elseif ($seasonNum == 3) {
                    $keywords[] = $baseTitle . ' 第三季';
                    $keywords[] = $baseTitle . 'Ⅲ';
                }
            }

            if (!empty($part)) {
                $keywords[] = $baseTitle . ' ' . $part;
            }

            if (!empty($version)) {
                $keywords[] = $baseTitle . ' ' . $version;
            }

            if ($seasonNum && !empty($version)) {
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '季 ' . $version;
            }
        }

        // video_title 归一化版本（如有）
        if (!empty($videoTitle)) {
            $normalizedVideo = TitleNormalizer::normalize($videoTitle);
            if ($normalizedVideo && $normalizedVideo !== $videoTitle && $normalizedVideo !== $baseTitle) {
                $keywords[] = $normalizedVideo;
            }
        }

        $keywords = array_values(array_unique(array_filter($keywords, function($kw) {
            if (empty($kw) || mb_strlen($kw) < 2) return false;
            // v5.12 修复：排除明显无意义的垃圾词
            //   a) 「门第X」: 九门第张启山... 这种「集数数字被吞掉」的畸形拼接
            if (preg_match('/门第[^0-9第·\-\s]/u', $kw)) return false;
            //   b) 连续重复分剧名 >= 2 次（如 "九门 张启山... 张启山..."）
            if (preg_match('/(.{4,}).*?(?=\1).*?\1/us', $kw)) return false;
            return true;
        })));

        if (count($keywords) > 10) {
            $keywords = array_slice($keywords, 0, 10);
        }

        return $keywords;
    }

    private function numberToChinese($num) {
        $cnNumbers = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
        if ($num >= 0 && $num <= 10) {
            return $cnNumbers[$num];
        }
        return null;
    }

    private function findAllMatches($videoInfo, $videos) {
        $threshold = $this->config['match_threshold'] ?? 60;
        $matches = [];

        $excludePatterns = [
            '/电影解说/i',
            '/预告片/i',
            '/片花/i',
            '/花絮/i',
            '/剪辑/i',
            '/解说/i',
            '/速看/i',
            '/混剪/i',
            '/盘点/i',
            '/reaction/i',
            '/MV/i',
            '/主题曲/i',
            '/片尾曲/i',
            '/片头曲/i',
            '/OST/i',
        ];

        $keyword = $videoInfo['base_title'] ?? $videoInfo['title'];
        $targetSeason = $videoInfo['season_num'] ?? null;
        $targetPart = $videoInfo['part'] ?? null;
        $targetVersion = $videoInfo['version'] ?? null;
        $targetEpisode = $videoInfo['episode_num'] ?? null;
        $originalTitle = $videoInfo['title'] ?? '';

        $searchKeywords = [];
        if (!empty($keyword)) {
            $searchKeywords[] = $keyword;
            $normKeyword = TitleNormalizer::normalize($keyword);
            if ($normKeyword !== $keyword) {
                $searchKeywords[] = $normKeyword;
            }
        }
        if (!empty($originalTitle) && $originalTitle !== $keyword) {
            $searchKeywords[] = $originalTitle;
            $normOrig = TitleNormalizer::normalize($originalTitle);
            if ($normOrig !== $originalTitle) {
                $searchKeywords[] = $normOrig;
            }
        }
        $searchKeywords = array_values(array_unique(array_filter($searchKeywords)));

        foreach ($videos as $video) {
            $videoName = $video['name'] ?? '';
            $videoRemarks = $video['remarks'] ?? '';
            $fullName = $videoName . ' ' . $videoRemarks;

            $isExcluded = false;
            foreach ($excludePatterns as $pattern) {
                if (preg_match($pattern, $videoName)) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) {
                continue;
            }

            $videoParsed = $this->parseVideoTitle($fullName);
            $videoBaseTitle = $videoParsed['base_title'];
            $videoSeason = $videoParsed['season_num'];
            $videoEpisode = $videoParsed['episode_num'];
            $videoPart = $videoParsed['part'];
            $videoVersion = $videoParsed['version'];

            $bestBaseScore = 0;
            foreach ($searchKeywords as $kw) {
                $currentScore = $this->calculateBaseMatchScore($kw, $videoBaseTitle);
                if ($currentScore > $bestBaseScore) {
                    $bestBaseScore = $currentScore;
                }
            }

            $baseScore = $bestBaseScore;

            if ($baseScore < 40) {
                continue;
            }

            $score = $baseScore;
            $seasonMatch = false;
            $episodeMatch = false;

            if (!empty($videoBaseTitle)) {
                foreach ($searchKeywords as $kw) {
                    if (mb_strpos($videoBaseTitle, $kw) !== false) {
                        $score += 8;
                        break;
                    }
                }
                foreach ($searchKeywords as $kw) {
                    if (mb_strpos($kw, $videoBaseTitle) !== false) {
                        $score += 4;
                        break;
                    }
                }
            }

            if ($targetSeason !== null && $videoSeason !== null) {
                if ($targetSeason == $videoSeason) {
                    $score += 25;
                    $seasonMatch = true;
                } else {
                    $seasonDiff = abs($targetSeason - $videoSeason);
                    $penalty = min(25, 15 + $seasonDiff * 5);
                    $score -= $penalty;
                }
            } elseif ($targetSeason !== null && $videoSeason === null) {
                if ($targetSeason == 1) {
                    $score += 8;
                } else {
                    $score -= 5;
                }
            } elseif ($targetSeason === null && $videoSeason !== null) {
                if ($videoSeason == 1) {
                    $score += 3;
                }
            }

            if ($targetEpisode !== null && $videoEpisode !== null) {
                if ($targetEpisode == $videoEpisode) {
                    $score += 20;
                    $episodeMatch = true;
                }
            }

            if ($targetPart && $videoPart) {
                if ($targetPart == $videoPart) {
                    $score += 15;
                } else {
                    $score -= 10;
                }
            }

            if ($targetVersion && $videoVersion) {
                if ($targetVersion == $videoVersion) {
                    $score += 10;
                } else {
                    $score -= 3;
                }
            }

            if (!empty($videoRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片/u', $videoRemarks)) {
                    $score += 5;
                }
            }

            if (!empty($videoName) && !empty($keyword)) {
                $videoNorm = TitleNormalizer::normalize($videoName);
                $keywordNorm = TitleNormalizer::normalize($keyword);
                if (!empty($videoNorm) && !empty($keywordNorm)) {
                    similar_text($keywordNorm, $videoNorm, $simScore);
                    if ($simScore > $baseScore) {
                        $score = max($score, $simScore * 0.8);
                    }
                }
            }

            $score = min(100, max(0, $score));

            if ($score >= $threshold * 0.45) {
                $videoEpisodeInfo = $videoParsed;
                $matchVideo = $video;
                $matchVideo['parsed_season'] = $videoSeason;
                $matchVideo['parsed_season_num'] = $videoSeason;
                $matchVideo['parsed_episode'] = $videoParsed['episode'] ?? '';
                $matchVideo['parsed_episode_num'] = $videoEpisode;
                $matchVideo['parsed_part'] = $videoPart;
                $matchVideo['parsed_version'] = $videoVersion;
                $matchVideo['first_url'] = $video['first_url'] ?? $video['url'] ?? '';
                $matchVideo['total_episodes'] = isset($video['urls']) ? count($video['urls']) : 0;
                $matches[] = [
                    'video' => $matchVideo,
                    'score' => round($score, 2),
                    'base_score' => round($baseScore, 2),
                    'season_match' => $seasonMatch,
                    'episode_match' => $episodeMatch,
                    'site' => $video['site'] ?? '',
                    'video_season' => $videoSeason,
                    'video_episode' => $videoEpisode,
                    'video_part' => $videoPart,
                    'video_version' => $videoVersion,
                    'video_name' => $videoBaseTitle
                ];
            }
        }

        usort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return $matches;
    }

    private function groupMatchesBySite($matches) {
        $siteMap = [];

        foreach ($matches as $match) {
            $siteName = $match['site'] ?? '未知';
            if (!isset($siteMap[$siteName])) {
                $siteMap[$siteName] = [
                    'site' => $siteName,
                    'match_count' => 0,
                    'best_score' => 0,
                    'best_match' => null,
                    'matches' => []
                ];
            }
            $siteMap[$siteName]['match_count']++;
            $siteMap[$siteName]['matches'][] = $match;
            if ($match['score'] > $siteMap[$siteName]['best_score']) {
                $siteMap[$siteName]['best_score'] = $match['score'];
                $siteMap[$siteName]['best_match'] = $match;
            }
        }

        $result = array_values($siteMap);
        usort($result, function($a, $b) {
            return $b['best_score'] - $a['best_score'];
        });

        return $result;
    }

    private function extractVideoId($url, $platform) {
        $videoId = null;
        $coverId = null;
        $domain = $platform['domain'] ?? '';

        if ($domain === 'v.qq.com') {
            if (preg_match('/cover\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $coverId = $matches[1];
                $videoId = $matches[2];
            } elseif (preg_match('/\/([a-zA-Z0-9]{8,16})\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/vid=([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/cover\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $coverId = $matches[1];
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/x\/page\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'iqiyi.com') {
            if (preg_match('/\/([a-zA-Z0-9]{10,24})\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/v_([a-zA-Z0-9_]+)\.html/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/a_([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'youku.com') {
            if (preg_match('/id_([a-zA-Z0-9=]+)\.html/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/v_show\/id_([a-zA-Z0-9=]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/show\/id_([a-zA-Z0-9=]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/video\/id_([a-zA-Z0-9=]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'mgtv.com') {
            if (preg_match('/\/([a-zA-Z0-9]+)\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/v\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'bilibili.com') {
            if (preg_match('/(BV[a-zA-Z0-9]{10,12})/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/av(\d+)/i', $url, $matches)) {
                $videoId = 'av' . $matches[1];
            } elseif (preg_match('/video\/([a-zA-Z0-9]{10,})/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'sohu.com') {
            if (preg_match('/(\d+)\.shtml$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/(\d+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'pptv.com') {
            if (preg_match('/showpage\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        }

        return [
            'video_id' => $videoId,
            'cover_id' => $coverId
        ];
    }

    private function detectPlatform($url) {
        foreach ($this->config['platforms'] as $platform) {
            if (empty($platform['enabled'])) continue;
            if (stripos($url, $platform['domain']) !== false) {
                return $platform;
            }
        }
        return null;
    }

    // =========================================================================
    // v5.12 新增：各平台独立的元数据解析方法（策略模式）
    //   每个平台走自己的 DOM / JSON 提取逻辑，好维护。
    //   统一返回结构：[
    //      'ok'              => bool,        // 本平台是否成功解析出有效 base_title
    //      'base_title'      => string,      // 剧名/系列名（搜索资源站用，最重要）
    //      'episode_num'     => int|null,    // 集号
    //      'episode_name'    => string,      // 单集名/分剧名（可空）
    //      'subtitle_guess'  => string,      // 单集副标题（可空，用来构造搜索词）
    //      'cover'           => string,      // 封面
    //      'description'     => string,      // 简介
    //      'total_episodes'  => int|null,    // 总集数
    //      'raw_title'       => string,      // 页面原始 og:title（debug 用）
    //      'hits'            => array,       // 命中了哪些字段（debug/step_trace 用）
    //   ]
    // =========================================================================

    // ================================================================
    // v5.12 平台独立元数据解析器（极简版）
    //   用户要求：只要「影视剧名(base_title) + 集数(episode_num)」两项，其他一概不要，减轻服务器负担。
    //   每个平台 < 15 行，共用一个 _extractQuickBaseAndEpisode 小助手。
    //   资源站搜索、AI+MD5 去广告/去非正片占位链路后续保持不变。
    // ================================================================

    /**
     * 快速提取：剧名(系列名) + 集数
     *  - 剧名：先扫 $inlineKeys 指定的内联 JS 键（如 showName），命中即返回；否则扫 $metaKeys 指定的 meta 标签；最后扫 og:title/<title> 正则截取 "第X集" 前半段。
     *  - 集数：只在 og:title + <title> 拼接的前 320 字里用一次正则匹配 「第N集/话/期」。
     *  - 不扫 description、cover、keywords、subtitle_guess、total_episodes，不做 json_decode，不建候选池。
     */
    private function _extractQuickBaseAndEpisode(string $html, array $inlineKeys, array $metaKeys, array $banWords): array {
        $base = '';
        $ep = null;

        // ---- 剧名 ----
        // 1) 内联 JS：按 $inlineKeys 顺序扫，命中第一个合法值即 break
        if (!empty($inlineKeys)) {
            $htmlSnippet = substr($html, 0, 260000); // 只扫前 260KB（优酷等的内联 JSON 通常都在 head 里），减少 preg 回溯
            foreach ($inlineKeys as $k) {
                if (strpos($k, '.name') !== false) {
                    // 嵌套对象形式：key: { name: "VALUE" }  或  "key": { "name": "VALUE" }
                    $parent = substr($k, 0, -5); // 去掉 .name
                    $parentQ = preg_quote($parent, '~');
                    $reList = [
                        '~["\']' . $parentQ . '["\']\s*:\s*\{[^\}]{0,600}?["\']name["\']\s*:\s*"([^"\\\\]{2,60})"~s',
                        '~(?:^|[{,])\s*'  . $parentQ . '\s*:\s*\{[^\}]{0,600}?["\']name["\']\s*:\s*"([^"\\\\]{2,60})"~s',
                        '~["\']' . $parentQ . '["\']\s*:\s*\{[^\}]{0,600}?(?:^|[{,])\s*name\s*:\s*"([^"\\\\]{2,60})"~s',
                        '~(?:^|[{,])\s*'  . $parentQ . '\s*:\s*\{[^\}]{0,600}?(?:^|[{,])\s*name\s*:\s*"([^"\\\\]{2,60})"~s',
                    ];
                } else {
                    // 扁平字段形式："key": "VALUE"  或  key: "VALUE" （裸键名常见于腾讯/B站/芒果等对象字面量）
                    $q = preg_quote($k, '~');
                    $reList = [
                        '~["\']' . $q . '["\']\s*:\s*"([^"\\\\]{2,60})"~',
                        '~(?:^|[{,])\s*'  . $q . '\s*:\s*"([^"\\\\]{2,60})"~',
                    ];
                }
                $mm = null;
                $matched = false;
                foreach ($reList as $re) {
                    if (preg_match($re, $htmlSnippet, $mm)) {
                        $matched = true;
                        break;
                    }
                }
                if ($matched && !empty($mm[1])) {
                    $t = html_entity_decode(trim($mm[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $t = trim($t, " \t\n\r《》<>\"'");
                    if ($t && mb_strlen($t) >= 2 && mb_strlen($t) <= 30) {
                        $lower = mb_strtolower($t);
                        foreach ($banWords as $b) if (strpos($lower, $b) !== false) { $t = ''; break; }
                        if ($t) { $base = $t; break; }
                    }
                }
            }
        }
        // 2) meta 标签兜底（og:video:series_name / tv:series_name / ...）
        if ($base === '' && !empty($metaKeys)) {
            foreach ($metaKeys as $prop) {
                $re = '~<meta\s+(?:property|name)=["\']' . preg_quote($prop, '~') . '["\'][^>]*content=["\']([^"\']{2,60})["\']~i';
                $reRev = '~<meta\s+content=["\']([^"\']{2,60})["\'][^>]*(?:property|name)=["\']' . preg_quote($prop, '~') . '["\']~i';
                if (preg_match($re, $html, $mm) || preg_match($reRev, $html, $mm)) {
                    $t = html_entity_decode(trim($mm[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $t = trim($t, " \t\n\r《》<>\"'");
                    if ($t && mb_strlen($t) >= 2 && mb_strlen($t) <= 30) {
                        $lower = mb_strtolower($t);
                        $ok = true; foreach ($banWords as $b) if (strpos($lower, $b) !== false) { $ok = false; break; }
                        if ($ok) { $base = $t; break; }
                    }
                }
            }
        }
        // 3) og:title / <title> 截取 "第X集/话/期" 之前的文本作为剧名兜底
        if ($base === '' && preg_match('~<title[^>]*>([^<]{3,200})</title>~is', $html, $mm)) {
            $tSrc = html_entity_decode(trim($mm[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $tSrc = preg_replace('/\s*[-_|·]+\s*(?:在线观看|高清|电视剧|电影|综艺|动漫|纪录片)\s*$/iu', '', $tSrc);
            if (preg_match('/第\s*\d+\s*[集话期部季]/u', $tSrc, $epPos, PREG_OFFSET_CAPTURE)) {
                $before = trim(mb_substr($tSrc, 0, $epPos[0][1]));
                if ($before) { $before = preg_replace('/^《(.+)》$/u', '$1', $before); $before = trim($before, " \t\n\r《》<>\"'_-"); }
                if ($before && mb_strlen($before) >= 2 && mb_strlen($before) <= 30) {
                    $lower = mb_strtolower($before);
                    $ok = true; foreach ($banWords as $b) if (strpos($lower, $b) !== false) { $ok = false; break; }
                    if ($ok) $base = $before;
                }
            } else {
                // 没集数（如电影名）：整段 title 去平台后缀
                $clean = preg_replace('/\s*[-_|·]+\s*(?:优酷|爱奇艺|腾讯视频|芒果tv|哔哩哔哩|bilibili|b站)\s*$/iu', '', $tSrc);
                $clean = trim($clean, " \t\n\r《》<>\"'");
                if ($clean && mb_strlen($clean) >= 2 && mb_strlen($clean) <= 40) {
                    $lower = mb_strtolower($clean);
                    $ok = true; foreach ($banWords as $b) if (strpos($lower, $b) !== false) { $ok = false; break; }
                    if ($ok) $base = $clean;
                }
            }
        }

        // ---- 集数（只扫一次短文本拼接）----
        if (preg_match('~<meta\s+property=["\']og:title["\'][^>]+content=["\']([^"\']{3,200})["\']~i', $html, $mm1)) {
            $og = html_entity_decode(trim($mm1[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        } elseif (preg_match('~<meta\s+content=["\']([^"\']{3,200})["\'][^>]+property=["\']og:title["\']~i', $html, $mm1)) {
            $og = html_entity_decode(trim($mm1[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        } else {
            $og = '';
        }
        $titleStr = '';
        if (preg_match('~<title[^>]*>([^<]{3,200})</title>~is', $html, $mm2)) {
            $titleStr = html_entity_decode(trim($mm2[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
        $epSrc = $og . ' ' . $titleStr;
        if (mb_strlen($epSrc) > 320) $epSrc = mb_substr($epSrc, 0, 320);
        if (preg_match('/第\s*(\d+)\s*[集话期部季]/u', $epSrc, $mm)) $ep = intval($mm[1]);
        if (!$ep && preg_match('/\bEP\s*(\d+)\b/ui', $epSrc, $mm)) $ep = intval($mm[1]);
        if (!$ep && preg_match('/(?:^|[\s\-_|·])(\d{1,3})\s*\/\s*\d{1,3}(?:$|[\s\-_|·])/u', $epSrc, $mm)) $ep = intval($mm[1]); // "2 / 24 全" 格式

        return [
            'ok' => ($base !== ''),
            'base_title'   => $base,
            'episode_num'  => $ep,
            // 以下字段固定为空/零值：fetchVideoInfo 仍会尝试读取它们，但我们不填，节省内存
            'episode_name' => '', 'subtitle_guess' => '', 'cover' => '',
            'description'  => '', 'total_episodes' => null,
            'raw_title'    => '',
            'hits'         => [], // 保持 key 存在（避免 fetchVideoInfo 读键 Notice），内部不再塞大对象
        ];
    }

    /**
     * 优酷（youku.com）元数据提取（极简）
     * 核心：showName="九门"（内联 JSON），og:title 只抽集数用。其他字段一律丢弃。
     */
    private function fetchMeta_Youku(string $html, string $url, array $videoIds): array {
        return $this->_extractQuickBaseAndEpisode($html,
            ['showName', 'videoShowName', 'seriesName', 'albumName', 'videoSeriesName', 'videoAlbumName', 'pgcTitle'],
            ['og:video:show', 'og:video:series_name', 'og:video:album'],
            ['优酷', '优酷视频', 'youku', '高清视频在线观看', '视频平台', '电视剧', '电影', '综艺']
        );
    }

    /**
     * 腾讯视频（v.qq.com）元数据提取（极简）
     * 核心：ld+json partOfSeries.name / isPartOf.name  → 我们也用正则直接扫 partOfSeries 内联字段，不 json_decode
     */
    private function fetchMeta_Tencent(string $html, string $url, array $videoIds): array {
        return $this->_extractQuickBaseAndEpisode($html,
            ['partOfSeries.name', 'isPartOf.name', 'seriesName', 'showName', 'albumName', 'tvName', 'videoSeriesName', 'mainTitle'],
            ['og:video:series', 'og:video:show', 'og:video:album'],
            ['腾讯视频', '腾讯', 'v.qq.com', 'qq', '在线观看', '电视剧', '电影', '综艺']
        );
    }

    /**
     * 爱奇艺（iqiyi.com）元数据提取（极简）
     * 核心：seriesName / albumName（内联） + og:video:series_name / tv:series_name meta
     */
    private function fetchMeta_Iqiyi(string $html, string $url, array $videoIds): array {
        return $this->_extractQuickBaseAndEpisode($html,
            ['seriesName', 'albumName', 'showName', 'partOfSeries.name', 'isPartOf.name', 'videoSeriesName', 'pageTitle', 'mainTitle'],
            ['og:video:series_name', 'tv:series_name', 'og:video:series', 'video:series', 'og:video:album'],
            ['爱奇艺', 'iqiyi', '高清在线观看', '在线观看', '电视剧', '电影', '综艺']
        );
    }

    /**
     * 芒果TV（mgtv.com）元数据提取（极简）
     * 综艺常写"第N期"——正则一并匹配（[集话期部季] 里含期）
     */
    private function fetchMeta_Mgtv(string $html, string $url, array $videoIds): array {
        return $this->_extractQuickBaseAndEpisode($html,
            ['showName', 'seriesName', 'albumName', 'partOfSeries.name', 'isPartOf.name', 'seriesTitle', 'albumTitle', 'pageTitle', 'mainTitle', 'tvName'],
            ['og:video:series', 'og:video:show', 'og:video:album'],
            ['芒果tv', '芒果', 'mgtv', '在线观看', '湖南卫视', '电视剧', '电影', '综艺']
        );
    }

    /**
     * 哔哩哔哩（bilibili.com / B站）元数据提取（极简）
     * 番剧：partOfSeries.name / season_title；UGC 视频：没系列名 → 用 og:title 去 "-哔哩哔哩" 后缀作剧名，episode_num=null
     */
    private function fetchMeta_Bilibili(string $html, string $url, array $videoIds): array {
        $out = $this->_extractQuickBaseAndEpisode($html,
            ['partOfSeries.name', 'isPartOf.name', 'seasonName', 'seriesName', 'showName', 'albumName', 'videoSeriesName'],
            ['og:video:series', 'og:video:show'],
            ['哔哩哔哩', 'bilibili', 'b站', 'bilibili视频', '弹幕视频网', '在线观看']
        );
        // UGC 兜底：如果没抓到 base_title（非番剧），就用 og:title / <title> 去平台后缀当内容名（让资源站搜分 P / 投稿标题）
        if ($out['base_title'] === '') {
            $og = '';
            if (preg_match('~<meta\s+property=["\']og:title["\'][^>]+content=["\']([^"\']{3,200})["\']~i', $html, $mm)) {
                $og = (string)$mm[1];
            } elseif (preg_match('~<meta\s+content=["\']([^"\']{3,200})["\'][^>]+property=["\']og:title["\']~i', $html, $mm)) {
                $og = (string)$mm[1];
            }
            if ($og === '' && preg_match('~<title[^>]*>([^<]{3,200})</title>~is', $html, $mm)) {
                $og = (string)$mm[1];
            }
            if ($og !== '') {
                $t = html_entity_decode(trim($og), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $t = preg_replace('/\s*[-_|·]+\s*(?:哔哩哔哩|bilibili|b站)\s*$/iu', '', $t);
                $t = trim($t, " \t\n\r《》<>\"'【】\[\]\(\)（）");
                if ($t && mb_strlen($t) >= 2 && mb_strlen($t) <= 80 && preg_match('//u', $t)) { // 合法 UTF-8（避免 �）
                    $out['base_title'] = $t;
                    $out['ok'] = true;
                }
            }
        }
        return $out;
    }

    /**
     * 通用兜底（未识别平台）：只用 og:title/<title> 抽 base_title + 集数（正则最简，不扫 meta）
     */
    private function fetchMeta_Generic(string $html, string $url, array $videoIds): array {
        return $this->_extractQuickBaseAndEpisode($html,
            ['showName', 'seriesName', 'albumName', 'partOfSeries.name', 'isPartOf.name'],
            ['og:video:series', 'og:video:show', 'og:video:album', 'og:video:series_name'],
            ['在线观看', '高清', '完整版', '电视剧', '电影', '综艺', '动漫']
        );
    }

    // -------- 以上平台解析器共用的工具方法 --------

    /** 从一段文本中批量抽出 meta 标签（用于各平台解析器） */
    private function extractMetaTags(string $html): array {
        $m = [];
        $patterns = [
            'og:title'           => '~<meta\s+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']~si',
            'og:title(rev)'      => '~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']og:title["\']~si',
            'twitter:title'      => '~<meta\s+name=["\']twitter:title["\'][^>]+content=["\']([^"\']+)["\']~si',
            'description'        => '~<meta\s+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']~si',
            'description(rev)'   => '~<meta\s+content=["\']([^"\']+)["\'][^>]+name=["\']description["\']~si',
            'og:description'     => '~<meta\s+property=["\']og:description["\'][^>]+content=["\']([^"\']+)["\']~si',
            'og:type'            => '~<meta\s+property=["\']og:type["\'][^>]+content=["\']([^"\']+)["\']~si',
            'og:image'           => '~<meta\s+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']~si',
            'keywords'           => '~<meta\s+name=["\']keywords["\'][^>]+content=["\']([^"\']+)["\']~si',
            'video:series'       => '~<meta\s+property=["\']video:series["\'][^>]+content=["\']([^"\']+)["\']~si',
            'video:show'         => '~<meta\s+property=["\']video:show["\'][^>]+content=["\']([^"\']+)["\']~si',
            'video:album'        => '~<meta\s+property=["\']video:album["\'][^>]+content=["\']([^"\']+)["\']~si',
            'og:video:series_name' => '~<meta\s+property=["\']og:video:series_name["\'][^>]+content=["\']([^"\']+)["\']~si',
            'tv:series_name'     => '~<meta\s+property=["\']tv:series_name["\'][^>]+content=["\']([^"\']+)["\']~si',
            'og:video:tag'       => '~<meta\s+property=["\']og:video:tag["\'][^>]+content=["\']([^"\']+)["\']~si',
        ];
        foreach ($patterns as $k => $re) {
            if (preg_match($re, $html, $mm)) {
                $cleanKey = str_replace(['(rev)'], '', $k);
                if (empty($m[$cleanKey])) {
                    $m[$cleanKey] = html_entity_decode(trim($mm[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                }
            }
        }
        if (preg_match('~<title[^>]*>([^<]+)</title>~is', $html, $mm)) {
            $m['<title>'] = html_entity_decode(trim($mm[1]), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
        return $m;
    }

    /** 批量从 HTML 内联 window.* / <script> 里抽"系列/专辑/剧集名"字段 */
    private function extractInlineSeriesTitles(string $html): array {
        $patterns = [
            'showName'          => '~["\']showName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'seriesName'        => '~["\']seriesName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'albumName'         => '~["\']albumName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'videoSeriesName'   => '~["\']videoSeriesName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'videoShowName'     => '~["\']videoShowName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'tvName'            => '~["\']tvName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'videoAlbumName'    => '~["\']videoAlbumName["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'seriesTitle'       => '~["\']seriesTitle["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'albumTitle'        => '~["\']albumTitle["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'mainTitle'         => '~["\']mainTitle["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'pageTitle'         => '~["\']pageTitle["\']\s*:\s*"([^"\\\\]{2,120})"~',
            'pgcTitle'          => '~["\']pgcTitle["\']\s*:\s*"([^"\\\\]{2,80})"~',
            'partOfSeriesName'  => '~["\']partOfSeries["\']\s*:\s*\{[^}]*["\']name["\']\s*:\s*"([^"\\\\]{2,80})"~s',
            'isPartOfName'      => '~["\']isPartOf["\']\s*:\s*\{[^}]*["\']name["\']\s*:\s*"([^"\\\\]{2,80})"~s',
        ];
        $out = [];
        foreach ($patterns as $k => $re) {
            if (preg_match_all($re, $html, $mm)) {
                $uniq = array_values(array_unique(array_filter(array_map(fn($s)=>html_entity_decode(trim($s),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'), $mm[1]), fn($s)=>$s!=='' && mb_strlen($s)>=2 && mb_strlen($s)<=80)));
                if ($uniq) $out[$k] = $uniq;
            }
        }
        return $out;
    }

    /**
     * 从一个典型拼接串里提取 [base, episode_num, subtitle]
     *   例："九门 第2集 张启山和吴老狗达成合作" → base=九门 ep=2 sub=张启山和吴老狗达成合作
     */
    private function parseTitleEpisodeSubtitle(string $s): array {
        $s = trim($s);
        $r = ['base'=>'', 'episode_num'=>null, 'subtitle'=>''];
        if ($s === '') return $r;
        if (preg_match('/^(.{2,40}?)\s*第\s*(\d+)\s*集\s*[\x{3000}\s:：\-_—]*(.{2,80}?)(?:\s+是在|，|。|\.|\s+-\s+|$)/u', $s, $m)) {
            $r['base']        = trim($m[1], " \t\n\r《》<>\"'");
            $r['episode_num'] = intval($m[2]);
            $r['subtitle']    = preg_replace('/\s+/u',' ', trim($m[3], " \t\r\n:：-_—|·。，,、"));
            return $r;
        }
        // 没 subtitle 版
        if (preg_match('/^(.{2,40}?)\s*第\s*(\d+)\s*集/u', $s, $m)) {
            $r['base']        = trim($m[1], " \t\n\r《》<>\"'");
            $r['episode_num'] = intval($m[2]);
            return $r;
        }
        // 无集号，只拆 base
        $r['base'] = trim($s, " \t\n\r《》<>\"'");
        return $r;
    }

    private function fetchVideoInfo($url, $platform, $videoIds = null) {
        if ($videoIds === null) {
            $videoIds = $this->extractVideoId($url, $platform);
        }
        $videoId = $videoIds['video_id'] ?? '';
        $coverId = $videoIds['cover_id'] ?? '';
        $title = null;
        $cover = null;
        $episodeInfo = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null
        ];
        $isExpired = false;
        $description = '';
        $ogType = '';
        $category = '';
        $releaseDate = '';

        $t = microtime(true);
        $apiInfo = $this->fetchVideoInfoFromApi($videoId, $platform, $coverId, $url);
        $apiUsed = false;
        if ($apiInfo) {
            if (!empty($apiInfo['title'])) {
                $title = $apiInfo['title'];
                $apiUsed = true;
            }
            if (!empty($apiInfo['cover'])) {
                $cover = $apiInfo['cover'];
                $apiUsed = true;
            }
        }
        $this->pushStep('fetch_meta_api', '  └ 来源1：平台API接口',
            $apiUsed ? 'ok' : 'warn',
            $apiUsed ? "API返回了可用 title=" . ($title ?? '') : 'API未能返回有效 title/cover，将尝试页面HTML下载',
            [
                'platform' => $platform['name'],
                'video_id' => $videoId,
                'cover_id' => $coverId,
                'api_title_len' => strlen($apiInfo['title'] ?? ''),
                'api_cover_len' => strlen($apiInfo['cover'] ?? ''),
            ],
            (microtime(true) - $t) * 1000);

        $htmlUsed = false;
        if (empty($title) || mb_strlen($title) < 3) {
            $t = microtime(true);
            $html = $this->httpGet($url);
            $httpCode = (int)($this->lastHttpCode ?: 0);
            $htmlLen = strlen($html ?: '');
            if ($html) {
                $htmlTitle = $this->extractTitle($html, $platform);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3) {
                    $title = $htmlTitle;
                    $htmlUsed = true;
                }
                $htmlCover = $this->extractCover($html);
                if (!empty($htmlCover) && empty($cover)) {
                    $cover = $htmlCover;
                    $htmlUsed = true;
                }
                $htmlEpisodeInfo = $this->extractEpisodeFromHtml($html, $platform);
                if (!empty($htmlEpisodeInfo['episode_num'])) {
                    $episodeInfo = $htmlEpisodeInfo;
                    $htmlUsed = true;
                }

                // ===== v5.12 新增：按平台（Youku/Tencent/Iqiyi/Mgtv/Bilibili）走独立元数据解析器（策略模式）
                //   - 优酷 showName="九门"；腾讯 ld+json partOfSeries.name；爱奇艺 og:video:series_name... 各走各的 DOM/JSON
                //   - 成功后直接覆盖 episode_info / base_title_guess / subtitle_guess，避免 og:title 的拼接串污染搜索词
                $platformMeta = null;
                $platformMetaOk = false;
                $platformMetaName = '';
                try {
                    $domain = $platform['domain'] ?? '';
                    switch ($domain) {
                        case 'youku.com':
                            $platformMeta = $this->fetchMeta_Youku($html, $url, $videoIds);
                            $platformMetaName = 'Youku';
                            break;
                        case 'v.qq.com':
                            $platformMeta = $this->fetchMeta_Tencent($html, $url, $videoIds);
                            $platformMetaName = 'Tencent';
                            break;
                        case 'iqiyi.com':
                            $platformMeta = $this->fetchMeta_Iqiyi($html, $url, $videoIds);
                            $platformMetaName = 'Iqiyi';
                            break;
                        case 'mgtv.com':
                            $platformMeta = $this->fetchMeta_Mgtv($html, $url, $videoIds);
                            $platformMetaName = 'Mgtv';
                            break;
                        case 'bilibili.com':
                            $platformMeta = $this->fetchMeta_Bilibili($html, $url, $videoIds);
                            $platformMetaName = 'Bilibili';
                            break;
                        default:
                            $platformMeta = $this->fetchMeta_Generic($html, $url, $videoIds);
                            $platformMetaName = 'Generic('.($domain ?: '?').')';
                            break;
                    }
                    $platformMetaOk = !empty($platformMeta['ok']);
                } catch (Throwable $e) {
                    $platformMeta = null;
                    $platformMetaName .= '(EX:'.$e->getMessage().')';
                }

                if ($platformMeta && is_array($platformMeta)) {
                    // 覆盖 description / cover / raw_title
                    if (empty($description) && !empty($platformMeta['description'])) $description = $platformMeta['description'];
                    if (empty($cover) && !empty($platformMeta['cover'])) $cover = $platformMeta['cover'];

                    // episodeInfo 合并：平台独立解析优先
                    $newEpisodeInfo = $episodeInfo;
                    if (!empty($platformMeta['episode_num'])) {
                        $newEpisodeInfo['episode_num']  = intval($platformMeta['episode_num']);
                        $newEpisodeInfo['episode_name'] = (string)($platformMeta['episode_name'] ?: ('第'.$newEpisodeInfo['episode_num'].'集'));
                        $episodeInfo = $newEpisodeInfo;
                        $htmlUsed = true;
                    } elseif (!empty($platformMeta['episode_name']) && empty($episodeInfo['episode_name'])) {
                        $episodeInfo['episode_name'] = $platformMeta['episode_name'];
                    }
                    if (!empty($platformMeta['total_episodes'])) $episodeInfo['total_episodes'] = intval($platformMeta['total_episodes']);

                    // base_title_guess / subtitle_guess：直接用平台独立解析的结果（这是最重要的输出）
                    $episodeInfo['base_title_guess'] = (string)($platformMeta['base_title'] ?? '');
                    if (!empty($platformMeta['subtitle_guess'])) {
                        $episodeInfo['subtitle_guess'] = (string)$platformMeta['subtitle_guess'];
                    }
                    if (!empty($platformMeta['raw_title']) && (empty($title) || mb_strlen($title) < 3)) {
                        $title = $platformMeta['raw_title'];
                    }
                    $episodeInfo['_platform_parser'] = $platformMetaName;
                    $episodeInfo['_platform_picked'] = $platformMeta['hits']['picked'] ?? null;
                    $htmlUsed = true;
                }

                // ===== v5.11 新增：官方页面元数据深度解析 =====
                $meta = $this->extractRichMetaFromHtml($html, $title ?: $htmlTitle);
                if (!empty($meta['description']) && empty($description)) {
                    $description = $meta['description'];
                }
                if (empty($episodeInfo['episode_num']) && !empty($meta['episode_num'])) {
                    $episodeInfo['episode_num'] = $meta['episode_num'];
                    $episodeInfo['episode_name'] = $meta['episode_name'] ?? '';
                } elseif (empty($episodeInfo['episode_name']) && !empty($meta['episode_name'])) {
                    $episodeInfo['episode_name'] = $meta['episode_name'];
                }
                if (empty($episodeInfo['total_episodes']) && !empty($meta['total_episodes'])) $episodeInfo['total_episodes'] = $meta['total_episodes'];
                if (empty($episodeInfo['base_title_guess']) && !empty($meta['base_title_guess'])) $episodeInfo['base_title_guess'] = $meta['base_title_guess'];
                if (empty($episodeInfo['subtitle_guess']) && !empty($meta['subtitle_guess'])) $episodeInfo['subtitle_guess'] = $meta['subtitle_guess'];
                if (!empty($meta)) $htmlUsed = true;

                if (!empty($meta['og_type'])) $ogType = $meta['og_type'];
                if (!empty($meta['category'])) $category = $meta['category'];
                if (!empty($meta['release_date'])) $releaseDate = $meta['release_date'];
                // ===== v5.11 新增：如果 og:type 是 video.episode 且从 <title> 解析出的集次/分剧名，强行补上 =====
                if (($ogType === 'video.episode' || strpos($ogType,'episode') !== false) && empty($episodeInfo['episode_num'])) {
                    $tEpi = $this->parseVideoTitle($title ?: $htmlTitle);
                    if (!empty($tEpi['episode_num'])) {
                        $episodeInfo['episode_num'] = $tEpi['episode_num'];
                        $episodeInfo['episode_name'] = $tEpi['episode'] ?? $episodeInfo['episode_name'];
                        $htmlUsed = true;
                    }
                }
            }
            $this->pushStep('fetch_meta_html', '  └ 来源2：PC页面HTML',
                $htmlUsed ? 'ok' : 'warn',
                $htmlUsed
                    ? ("平台解析=".($platformMetaName ?: 'none').($platformMetaOk ? '✓' : '✗')." · title={$title}".
                       (!empty($episodeInfo['base_title_guess']) ? " · base_title_guess=".$episodeInfo['base_title_guess'] : ""))
                    : ($htmlLen > 0
                        ? "已下载 {$htmlLen} 字节（HTTP {$httpCode}）但没有提取到 title"
                        : "页面HTML下载失败（HTTP {$httpCode}），下一步走移动页兜底"),
                [
                    'http_code'    => $httpCode,
                    'html_bytes'   => $htmlLen,
                    'title_source' => $htmlUsed ? 'HTML/og:title' : null,
                    'rich_meta_ok' => !empty($meta),
                    'platform_parser' => $platformMetaName ?? null,
                    'platform_meta_ok' => $platformMetaOk ?? null,
                    'platform_parser_base_title' => $platformMeta['base_title'] ?? null,
                    'platform_parser_ep_num'     => $platformMeta['episode_num'] ?? null,
                    'platform_parser_hits'       => isset($platformMeta['hits']) ? array_slice($platformMeta['hits'], 0, 6) : null,
                ],
                (microtime(true) - $t) * 1000);

            $mUsed = false;
            if ((empty($title) || mb_strlen($title) < 3) && in_array($platform['domain'], ['iqiyi.com', 'youku.com', 'mgtv.com', 'sohu.com', 'pptv.com', 'v.qq.com'])) {
                $t = microtime(true);
                $mobileHtml = $this->httpGetMobile($url);
                $mLen = strlen($mobileHtml ?: '');
                if ($mobileHtml) {
                    $mobileTitle = $this->extractTitle($mobileHtml, $platform);
                    if (!empty($mobileTitle) && mb_strlen($mobileTitle) >= 3) {
                        $title = $mobileTitle;
                        $mUsed = true;
                    }
                    if (empty($cover)) {
                        $mobileCover = $this->extractCover($mobileHtml);
                        if (!empty($mobileCover)) {
                            $cover = $mobileCover;
                            $mUsed = true;
                        }
                    }
                    if (empty($episodeInfo['episode_num'])) {
                        $mobileEpisodeInfo = $this->extractEpisodeFromHtml($mobileHtml, $platform);
                        if (!empty($mobileEpisodeInfo['episode_num'])) {
                            $episodeInfo = $mobileEpisodeInfo;
                            $mUsed = true;
                        }
                    }
                }
                $this->pushStep('fetch_meta_mobile', '  └ 来源3：移动页HTML（兜底）',
                    $mUsed ? 'ok' : 'warn',
                    $mUsed ? "从移动页成功提取 · title={$title}"
                          : ($mLen > 0 ? "已下载 {$mLen} 字节但没有提取到可用标题" : "移动页下载失败或为空"),
                    ['mobile_html_bytes' => $mLen],
                    (microtime(true) - $t) * 1000);
            }
        }

        $urlTitleUsed = false;
        if (empty($title) || mb_strlen($title) < 3) {
            $urlTitle = $this->extractTitleFromUrl($url, $platform);
            if (!empty($urlTitle) && mb_strlen($urlTitle) >= 2) {
                $title = $urlTitle;
                $urlTitleUsed = true;
            }
            $this->pushStep('fetch_meta_url', '  └ 来源4：URL路径兜底',
                $urlTitleUsed ? 'ok' : 'fail',
                $urlTitleUsed ? "从URL路径提取到 title={$title}" : "URL里也没有可识别的中文标题，fetchVideoInfo 将返回空title",
                ['guessed_title' => $urlTitle]);
        }

        if ($title && $this->isExpiredVideoTitle($title)) {
            $isExpired = true;
        }

        // ===== v5.11 新增：从 title 解析出的集次信息，如果 episode_info 仍为空，再次补一遍 =====
        if (empty($episodeInfo['episode_num']) && !empty($title)) {
            $p = $this->parseVideoTitle($title);
            if (!empty($p['episode_num'])) {
                $episodeInfo['episode_num'] = $p['episode_num'];
                $episodeInfo['episode_name'] = $p['episode'] ?? '';
            }
        }

        return [
            'title' => $title,
            'cover' => $cover,
            'url' => $url,
            'platform' => $platform['name'],
            'episode_info' => $episodeInfo,
            'video_id' => $videoId,
            'cover_id' => $coverId,
            'is_expired' => $isExpired,
            'description' => $description,
            'og_type' => $ogType,
            'category' => $category,
            'release_date' => $releaseDate,
            'sources_used' => [
                'api'    => $apiUsed,
                'html'   => $htmlUsed,
                'mobile' => $mUsed ?? false,
                'url'    => $urlTitleUsed,
            ],
        ];
    }

    private function isExpiredVideoTitle($title) {
        $expiredKeywords = [
            '那条视频不见了',
            '视频不存在',
            '视频已删除',
            '视频已下架',
            '视频失效',
            '链接失效',
            '该视频不存在',
            '该视频已删除',
            '该视频已下架',
            '无法找到该视频',
            '抱歉，该视频',
            '视频无法播放',
            '已失效',
            'invalid',
            'not found',
            '404',
            'error'
        ];

        $lowerTitle = strtolower($title);
        foreach ($expiredKeywords as $keyword) {
            if (stripos($title, $keyword) !== false || stripos($lowerTitle, strtolower($keyword)) !== false) {
                return true;
            }
        }
        return false;
    }

    private function extractTitleFromUrl($url, $platform) {
        $domain = $platform['domain'] ?? '';

        if ($domain === 'v.qq.com') {
            if (preg_match('/cover\/([a-zA-Z0-9]+)\//i', $url, $matches)) {
                return null;
            }
        }

        if (preg_match('/\/([^\/\?#]+)\.(?:html|shtml|htm)/i', $url, $matches)) {
            $fileName = $matches[1];
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $fileName) && mb_strlen($fileName) >= 2) {
                $cleanName = preg_replace('/[-_]\d+$/', '', $fileName);
                $cleanName = preg_replace('/[-_]/u', ' ', $cleanName);
                if (mb_strlen($cleanName) >= 2) {
                    return $cleanName;
                }
            }
        }

        return null;
    }

    private function safeJsonDecode($response) {
        if (empty($response) || !is_string($response)) {
            return null;
        }

        $cleaned = trim($response);

        $cleaned = preg_replace('/^\/\*[\s\S]*?\*\//', '', $cleaned);
        $cleaned = trim($cleaned);

        $cleaned = preg_replace('/^(?:var|let|const)\s+\w+\s*=\s*/', '', $cleaned);
        $cleaned = preg_replace('/^\w+\s*=\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        if (preg_match('/^\w+\s*\(/', $cleaned)) {
            $cleaned = preg_replace('/^\w+\s*\(/', '', $cleaned);
            $cleaned = preg_replace('/\)\s*;?\s*$/', '', $cleaned);
            $cleaned = trim($cleaned);
        }

        $cleaned = rtrim($cleaned, ';');
        $cleaned = trim($cleaned);

        if (empty($cleaned)) {
            return null;
        }

        $data = json_decode($cleaned, true);
        if ($data !== null) {
            return $data;
        }

        $cleaned = preg_replace('/^\s*\w+\s*:\s*/', '', $cleaned);
        $cleaned = rtrim($cleaned, ';');
        $cleaned = trim($cleaned);
        $data = json_decode($cleaned, true);
        if ($data !== null) {
            return $data;
        }

        return null;
    }

    private function fetchVideoInfoFromApi($videoId, $platform, $coverId = '', $originalUrl = '') {
        if (empty($videoId)) {
            return null;
        }

        $platformName = $platform['name'] ?? '';
        $result = ['title' => null, 'cover' => null];

        if ($platformName === '腾讯视频') {
            $apiUrls = [
                'https://node.video.qq.com/x/api/float_vinfo2?vid=' . urlencode($videoId),
                'https://pbaccess.video.qq.com/trpc.vidplay.vidplay_2_0_fcgi.VidPlay2_0Fcgi/GetCmsVidInfoAll?data={"vid":"' . urlencode($videoId) . '","appVer":"3.5.57","platform":"40000"}',
                'https://access.video.qq.com/cgi-bin/varietycheck?vid=' . urlencode($videoId),
                'http://vv.video.qq.com/getinfo?vids=' . urlencode($videoId) . '&platform=101001&charge=0&otype=json',
                'https://v.qq.com/x/page/' . urlencode($videoId) . '.html',
                'https://v.qq.com/x/cover/' . urlencode($videoId) . '.html',
                'https://v.qq.com/x/cover/' . urlencode($videoId) . '/' . urlencode($videoId) . '.html',
            ];

            if (!empty($coverId)) {
                $apiUrls[] = 'https://v.qq.com/x/cover/' . urlencode($coverId) . '.html';
                $apiUrls[] = 'https://v.qq.com/x/cover/' . urlencode($coverId) . '/' . urlencode($videoId) . '.html';
            }
            if (!empty($originalUrl)) {
                $apiUrls[] = $originalUrl;
            }

            $titlePaths = [
                ['c', 'title'],
                ['data', 'c', 'title'],
                ['VideoInfo', 'title'],
                ['videoInfo', 'title'],
                ['title'],
                ['name'],
                ['tvName'],
                ['data', 'videoInfo', 'title'],
                ['data', 'vl', 'vi', 0, 'ti'],
            ];

            $coverPaths = [
                ['c', 'pic'],
                ['data', 'c', 'pic'],
                ['c', 'cover'],
                ['VideoInfo', 'pic'],
                ['videoInfo', 'cover'],
                ['pic'],
                ['cover'],
                ['imageUrl'],
                ['data', 'videoInfo', 'cover'],
                ['data', 'vl', 'vi', 0, 'video_pic'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === '爱奇艺') {
            $apiUrls = [
                'https://pcw-api.iqiyi.com/video/video/baseinfo/' . urlencode($videoId),
                'https://pcw-api.iqiyi.com/strategy/pcw/data/baseVideoInfo?ids=' . urlencode($videoId),
                'https://cache.video.iqiyi.com/jp/avlist/20210316/' . urlencode($videoId) . '.json',
                'https://www.iqiyi.com/v_' . urlencode($videoId) . '.html',
            ];

            $titlePaths = [
                ['data', 'name'], ['data', 'title'], ['data', '0', 'name'],
                ['name'], ['title'], ['videoName'], ['data', '0', 'title'],
                ['data', 'videoInfo', 'name'], ['data', 'videoInfo', 'title'],
                ['data', 'albumInfo', 'name'], ['data', 'albumInfo', 'title'],
            ];

            $coverPaths = [
                ['data', 'imageUrl'], ['data', '0', 'imageUrl'],
                ['data', 'image'], ['data', '0', 'image'],
                ['imageUrl'], ['image'], ['data', 'videoInfo', 'imageUrl'],
                ['data', 'albumInfo', 'imageUrl'], ['data', 'albumInfo', 'cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === '芒果TV') {
            $apiUrls = [
                'https://pcweb.api.mgtv.com/episode/list?video_id=' . urlencode($videoId),
                'https://pcweb.api.mgtv.com/video/info?video_id=' . urlencode($videoId),
                'https://pcweb.api.mgtv.com/video/shortSourceInfo?video_id=' . urlencode($videoId),
                'https://www.mgtv.com/b/' . urlencode($videoId) . '.html',
            ];

            $titlePaths = [
                ['data', 'info', 'title'], ['data', 'info', 'title2'],
                ['data', 'title'], ['data', '0', 'title'],
                ['data', '0', 'desc'], ['data', 'info', 'desc'],
                ['data', 'clipInfo', 'title'], ['data', 'videoInfo', 'title'],
            ];

            $coverPaths = [
                ['data', 'info', 'cover'], ['data', 'info', 'image'],
                ['data', '0', 'image'], ['data', 'cover'],
                ['data', 'clipInfo', 'cover'], ['data', 'videoInfo', 'cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === '优酷') {
            $apiUrls = [
                'https://v.youku.com/service/getVideoInfo?vid=' . urlencode($videoId),
                'https://openapi.youku.com/v2/videos/show.json?client_id=23e50e2e09490776&video_id=' . urlencode($videoId),
                'https://v.youku.com/v_show/id_' . urlencode($videoId) . '.html',
                'https://m.youku.com/v_show/id_' . urlencode($videoId) . '.html',
                'https://v.youku.com/player/getPlayList/VideoIDS/' . urlencode($videoId),
            ];

            $titlePaths = [
                ['data', 'title'], ['data', 'name'], ['title'], ['name'],
                ['data', 'video', 'title'], ['data', '0', 'title'],
                ['data', 'videoInfo', 'title'], ['data', 'show', 'title'],
            ];

            $coverPaths = [
                ['data', 'bigPhoto'], ['data', 'photo'], ['bigPhoto'], ['photo'],
                ['data', '0', 'bigPhoto'], ['data', 'image'], ['image'],
                ['data', 'videoInfo', 'cover'], ['data', 'show', 'cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === '哔哩哔哩') {
            $apiUrls = [
                'https://api.bilibili.com/x/web-interface/view?bvid=' . urlencode($videoId),
            ];

            if (preg_match('/^av(\d+)$/i', $videoId, $avMatch)) {
                $apiUrls[] = 'https://api.bilibili.com/x/web-interface/view?aid=' . $avMatch[1];
            } elseif (preg_match('/BV/i', $videoId)) {
                $apiUrls[] = 'https://api.bilibili.com/x/web-interface/view?aid=' . urlencode($videoId);
            }

            $apiUrls[] = 'https://www.bilibili.com/video/' . urlencode($videoId);

            $titlePaths = [
                ['data', 'title'], ['data', 'info', 'title'],
                ['title'], ['name'],
            ];

            $coverPaths = [
                ['data', 'pic'], ['data', 'cover'],
                ['data', 'info', 'pic'], ['pic'], ['cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '/video/') !== false && stripos($apiUrl, 'api.') === false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === '搜狐视频') {
            $apiUrls = [
                'https://sohu.com/api/getVideoInfo?vid=' . urlencode($videoId),
                'https://tv.sohu.com/continfo/' . urlencode($videoId) . '.json',
                'https://my.tv.sohu.com/play/videonew.do?id=' . urlencode($videoId),
                'https://tv.sohu.com/v/' . urlencode($videoId) . '.shtml',
            ];

            $titlePaths = [
                ['data', 'title'], ['data', 'tvName'], ['data', 'name'],
                ['title'], ['tvName'], ['name'],
                ['data', 'videoName'], ['videoName'],
                ['data', 'videoInfo', 'title'], ['data', 'albumInfo', 'name'],
            ];

            $coverPaths = [
                ['data', 'cover'], ['data', 'pic'], ['data', 'image'],
                ['cover'], ['pic'], ['image'],
                ['data', 'bigCover'], ['bigCover'],
                ['data', 'videoInfo', 'cover'], ['data', 'albumInfo', 'cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.shtml') !== false || stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if ($platformName === 'PP视频') {
            $apiUrls = [
                'https://api2.pptv.com/v3/api/tv/playlist.json?pid=' . urlencode($videoId),
                'https://web-api.pptv.com/web/video/info?id=' . urlencode($videoId),
                'https://v.pptv.com/show/' . urlencode($videoId) . '.html',
            ];

            $titlePaths = [
                ['data', 'title'], ['data', 'name'],
                ['title'], ['name'], ['tvName'],
                ['data', 'videoInfo', 'title'], ['data', 'info', 'title'],
            ];

            $coverPaths = [
                ['data', 'cover'], ['data', 'pic'], ['data', 'image'],
                ['cover'], ['pic'], ['image'],
                ['data', 'videoInfo', 'cover'], ['data', 'info', 'cover'],
            ];

            foreach ($apiUrls as $apiUrl) {
                try {
                    $response = $this->httpGet($apiUrl);
                    if (!$response) continue;

                    $isHtml = stripos($apiUrl, '.html') !== false;
                    if ($isHtml) {
                        $htmlTitle = $this->extractTitle($response, $platform);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                            $result['title'] = $htmlTitle;
                        }
                        $htmlCover = $this->extractCover($response);
                        if (!empty($htmlCover) && empty($result['cover'])) {
                            $result['cover'] = $htmlCover;
                        }
                        if (!empty($result['title'])) break;
                        continue;
                    }

                    $data = $this->safeJsonDecode($response);
                    if (!$data) continue;

                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3 && empty($result['title'])) {
                            $result['title'] = $val;
                            break;
                        }
                    }

                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) { $val = null; break; }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                            $result['cover'] = $val;
                            break;
                        }
                    }

                    if (empty($result['title']) || empty($result['cover'])) {
                        $found = $this->findTitleInData($data);
                        if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                            $result['title'] = $found['title'];
                        }
                        if (!empty($found['cover']) && empty($result['cover'])) {
                            $result['cover'] = $found['cover'];
                        }
                    }

                    if (!empty($result['title'])) break;
                } catch (Throwable $e) {
                    continue;
                }
            }
        }

        if (!empty($result['title']) && mb_strlen($result['title']) >= 3) {
            return $result;
        }
        return null;
    }

    private function findTitleInData($data) {
        $result = ['title' => null, 'cover' => null];
        if (!is_array($data)) return $result;

        $titleKeys = ['title', 'name', 'ti', 'videoName', 'video_title', 'vidName', 'subTitle', 'mainTitle'];
        $coverKeys = ['cover', 'pic', 'image', 'imageUrl', 'poster', 'thumb', 'thumbnail', 'vpic'];

        $invalidTitles = [
            'hd', 'shd', 'fhd', '4k', '8k', '标清', '高清', '超清', '蓝光', '1080p', '720p',
            'sd', 'md', 'ld', '流畅', '准高清', 'vip', '免费', '预告', '花絮'
        ];

        $candidates = [];

        array_walk_recursive($data, function($value, $key) use (&$result, $titleKeys, $coverKeys, $invalidTitles, &$candidates) {
            if (in_array($key, $titleKeys) && is_string($value) && mb_strlen($value) >= 2 && mb_strlen($value) <= 100) {
                $lowerValue = mb_strtolower($value);
                $isInvalid = false;
                foreach ($invalidTitles as $inv) {
                    if ($lowerValue === mb_strtolower($inv) || mb_strpos($lowerValue, mb_strtolower($inv)) !== false && mb_strlen($value) < 6) {
                        $isInvalid = true;
                        break;
                    }
                }
                if (!$isInvalid && preg_match('/[\x{4e00}-\x{9fa5}a-zA-Z]/u', $value)) {
                    $candidates[] = $value;
                    if (empty($result['title']) && mb_strlen($value) >= 3) {
                        $result['title'] = $value;
                    }
                }
            }
            if (in_array($key, $coverKeys) && is_string($value) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $value)) {
                if (empty($result['cover'])) {
                    $result['cover'] = $value;
                }
            }
        });

        return $result;
    }

    /**
     * v5.11 新增：从官方页面 HTML 里深度抽取 description / category / og:type / release_date / 集次-分剧名
     * 直接从 meta[name=description] / property=og:description / property=og:type 等取，
     * 再从 description 前半段抽 "九门 第2集 张启山和吴老狗达成合作" 这种「剧+第N集+分剧名」格式，
     * 用于后续 buildSearchKeywords 的最优先搜索词。
     */
    private function extractRichMetaFromHtml($html, $currentTitle = '') {
        $out = [
            'description' => '',
            'category' => '',
            'og_type' => '',
            'release_date' => '',
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null,
            'base_title_guess' => '',
            'subtitle_guess' => ''
        ];

        // ---------- 1. 抽取 description / category / og_type / release_date ----------
        $metaPatterns = [
            ['re'=>'~<meta\s+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'description'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+name=["\']description["\']~si', 'k'=>'description'],
            ['re'=>'~<meta\s+property=["\']og:description["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'description'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']og:description["\']~si', 'k'=>'description'],
            ['re'=>'~<meta\s+property=["\']og:type["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'og_type'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']og:type["\']~si', 'k'=>'og_type'],
            ['re'=>'~<meta\s+name=["\']category["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'category'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+name=["\']category["\']~si', 'k'=>'category'],
            ['re'=>'~<meta\s+property=["\']og:category["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'category'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']og:category["\']~si', 'k'=>'category'],
            ['re'=>'~<meta\s+property=["\']video:release_date["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'release_date'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']video:release_date["\']~si', 'k'=>'release_date'],
            ['re'=>'~<meta\s+property=["\']article:published_time["\'][^>]+content=["\']([^"\']+)["\']~si', 'k'=>'release_date'],
            ['re'=>'~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']article:published_time["\']~si', 'k'=>'release_date'],
        ];
        foreach ($metaPatterns as $p) {
            if (empty($out[$p['k']]) && preg_match($p['re'], $html, $m)) {
                $val = html_entity_decode(trim($m[1]), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
                if ($val !== '') $out[$p['k']] = $val;
            }
        }
        // 优酷 description 里常包含「于 2026-07-30 11:50:01 上线」直接抓上线时间
        if (!$out['release_date'] && $out['description']) {
            if (preg_match('/(\d{4}-\d{2}-\d{2}(?:\s+\d{2}:\d{2}(?::\d{2})?)?)/u', $out['description'], $dm)) {
                $out['release_date'] = $dm[1];
            }
        }

        // ---------- 2. 从 description 开头/title 中抽取「剧+第N集+分剧名」组合 ----------
        $candidates = [];
        if ($out['description']) $candidates[] = mb_substr($out['description'], 0, 120);
        if ($currentTitle) $candidates[] = $currentTitle;

        $foundHead = '';
        $foundEpisodeNum = null;
        $foundSubtitle = '';
        $foundBase = '';
        foreach ($candidates as $c) {
            // 匹配：  ...剧 第X集 分剧名( - 或后面的 - 或第一个句号之前)
            if (preg_match('/([\x{4e00}-\x{9fa5}A-Za-z0-9《》]{2,40})\s*第\s*(\d+)\s*集\s*[\x{3000}\s:：\-_]*(.{2,60}?)(?:(?:\s+是在|，|。|\.|\s|-|$))/u', $c, $mm)) {
                $foundBase = $this->cleanTitle($mm[1]) ?: trim($mm[1], " \t《》\r\n");
                $foundEpisodeNum = intval($mm[2]);
                $foundSubtitle = preg_replace('/\s+/u',' ', trim($mm[3], " \t\r\n:：-_—|·。，,、"));
                $foundHead = $foundBase . ' 第' . $foundEpisodeNum . '集' . ($foundSubtitle ? ' ' . $foundSubtitle : '');
                break;
            }
        }
        // 再从 title 兜底：《九门 第2集 张启山和吴老狗达成合作-电视剧》
        if (!$foundEpisodeNum && $currentTitle) {
            $pt = $this->parseVideoTitle($currentTitle);
            if (!empty($pt['episode_num'])) {
                $foundEpisodeNum = $pt['episode_num'];
                $foundBase = $this->cleanTitle($pt['base_title']) ?: trim($pt['base_title']);
                // 从 title 里截掉"剧名 第X集 " 后的前缀到第一个分隔符之前的部分作为分剧名
                $trimmed = preg_replace('/^(.*?第\s*' . $foundEpisodeNum . '\s*集)\s*/u', '', $currentTitle, 1);
                if ($trimmed && $trimmed !== $currentTitle) {
                    $sub = preg_split('/[-_|·|\s]+在线观看|\s+-\s+(?:电视剧|电影|综艺|动漫|纪录片)|，|。|\.|\s|$/u', $trimmed, 2)[0] ?? '';
                    $sub = preg_replace('/\s+/u', ' ', trim($sub, " \t\r\n:：-_—|·"));
                    if ($sub && mb_strlen($sub) >= 2 && mb_strlen($sub) <= 60) {
                        $foundSubtitle = $sub;
                    }
                }
            }
        }

        if ($foundEpisodeNum) {
            $out['episode_num'] = $foundEpisodeNum;
            $out['episode_name'] = $foundHead ?: ('第' . $foundEpisodeNum . '集' . ($foundSubtitle ? ' ' . $foundSubtitle : ''));
            $out['base_title_guess'] = $foundBase;
            $out['subtitle_guess'] = $foundSubtitle;
        }

        // 从全文里匹配共多少集
        if (preg_match('/共\s*(\d+)\s*集/u', $html, $mm2)) {
            $out['total_episodes'] = intval($mm2[1]);
        } elseif (preg_match('/全\s*(\d+)\s*集/u', $html, $mm2)) {
            $out['total_episodes'] = intval($mm2[1]);
        }

        return $out;
    }

    private function extractEpisodeFromHtml($html, $platform) {
        $info = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null
        ];

        $patterns = [
            '/第\s*(\d+)\s*[集期]/u' => 'episode',
            '/更新至\s*(\d+)\s*[集期]/u' => 'total',
            '/共\s*(\d+)\s*[集期]/u' => 'total',
            '/(\d+)\s*集全/u' => 'total',
            '/(?<![a-zA-Z])EP\s*(\d+)(?![a-zA-Z])/i' => 'episode',
            '/(?<![a-zA-Z])E\s*(\d+)(?![a-zA-Z])/i' => 'episode',
        ];

        foreach ($patterns as $pattern => $type) {
            if (preg_match($pattern, $html, $matches)) {
                if ($type === 'total') {
                    $info['total_episodes'] = intval($matches[1]);
                } else {
                    if ($info['episode_num'] === null) {
                        $info['episode_num'] = intval($matches[1]);
                        $info['episode_name'] = $matches[0];
                    }
                }
            }
        }

        return $info;
    }

    private function extractTitle($html, $platform) {
        $candidates = [];

        $ogPattern = '/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i';
        if (preg_match($ogPattern, $html, $matches)) {
            $candidates[] = trim($matches[1]);
        }

        $twitterPattern = '/<meta[^>]+name=["\']twitter:title["\'][^>]+content=["\']([^"\']+)["\']/i';
        if (preg_match($twitterPattern, $html, $matches)) {
            $candidates[] = trim($matches[1]);
        }

        $titlePattern = '/<title[^>]*>([^<]+)<\/title>/i';
        if (preg_match($titlePattern, $html, $matches)) {
            $candidates[] = trim($matches[1]);
        }

        $videoTitlePatterns = [
            '/<h1[^>]*class=["\'][^"\']*video-title[^"\']*["\'][^>]*>([^<]+)<\/h1>/i',
            '/<h1[^>]*>([^<]+)<\/h1>/i',
            '/class=["\'][^"\']*video_title[^"\']*["\'][^>]*>([^<]+)</i',
            '/class=["\'][^"\']*main_title[^"\']*["\'][^>]*>([^<]+)</i',
            '/class=["\'][^"\']*player-title[^"\']*["\'][^>]*>([^<]+)</i',
        ];
        foreach ($videoTitlePatterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $candidates[] = trim(strip_tags($matches[1]));
            }
        }

        $jsonLdPattern = '/<script[^>]+type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/is';
        if (preg_match_all($jsonLdPattern, $html, $matches)) {
            foreach ($matches[1] as $json) {
                $data = json_decode($json, true);
                if ($data) {
                    if (isset($data['name'])) {
                        $candidates[] = $data['name'];
                    }
                    if (isset($data['headline'])) {
                        $candidates[] = $data['headline'];
                    }
                }
            }
        }

        $inlineJsonPatterns = [
            '/"title"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"name"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"videoName"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"albumName"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"tvName"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"showName"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"videoTitle"\s*:\s*"([^"\\\]{3,80})"/i',
            '/"albumTitle"\s*:\s*"([^"\\\]{3,80})"/i',
        ];
        
        $foundInlineTitles = [];
        foreach ($inlineJsonPatterns as $pattern) {
            if (preg_match_all($pattern, $html, $matches)) {
                foreach ($matches[1] as $title) {
                    $decoded = json_decode('"' . $title . '"');
                    if ($decoded) {
                        $foundInlineTitles[] = $decoded;
                    } else {
                        $foundInlineTitles[] = $title;
                    }
                }
            }
        }
        
        $foundInlineTitles = array_unique($foundInlineTitles);
        foreach ($foundInlineTitles as $title) {
            $title = trim($title);
            if (mb_strlen($title) >= 3 && mb_strlen($title) <= 100) {
                $isCommonWord = in_array($title, ['爱奇艺', '腾讯视频', '优酷', '芒果TV', '哔哩哔哩', '搜狐视频', 'PP视频']);
                $hasChinese = preg_match('/[\x{4e00}-\x{9fa5}]/u', $title);
                if (!$isCommonWord && $hasChinese) {
                    $candidates[] = $title;
                }
            }
        }

        $bestTitle = '';
        $bestScore = 0;

        foreach ($candidates as $candidate) {
            $candidate = $this->cleanTitle($candidate);
            if (empty($candidate)) continue;
            
            $score = mb_strlen($candidate);
            
            if (preg_match('/第\s*\d+\s*[集期部季]/u', $candidate)) {
                $score += 10;
            }
            
            if (mb_strlen($candidate) > 3 && mb_strlen($candidate) < 50) {
                $score += 5;
            }
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestTitle = $candidate;
            }
        }

        return !empty($bestTitle) ? $bestTitle : null;
    }

    private function extractCover($html) {
        $patterns = [
            '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return trim($matches[1]);
            }
        }
        return null;
    }

    private function cleanTitle($title) {
        $title = trim($title);
        if (empty($title)) return null;

        // 优先提取书名号、引号内的纯标题
        $title = $this->extractPureTitle($title);

        // 清理常见后缀描述文字
        $title = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/高清.*?$/u', '', $title);
        $title = preg_replace('/完整版.*?$/u', '', $title);
        $title = preg_replace('/_腾讯视频/i', '', $title);
        $title = preg_replace('/- 腾讯视频/i', '', $title);
        $title = preg_replace('/最新一期.*?$/u', '', $title);
        $title = preg_replace('/第.*?期.*?$/u', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|·");
        $title = trim($title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);

        $invalidTitles = ['腾讯视频', '爱奇艺', '优酷', '芒果TV', '哔哩哔哩', 'bilibili', '搜狐视频', 'PP视频'];
        foreach ($invalidTitles as $inv) {
            if (mb_strtolower($title) === mb_strtolower($inv)) {
                return null;
            }
        }

        if (mb_strlen($title) < 2) {
            return null;
        }

        return $title;
    }

    private function extractPureTitle($title) {
        if (preg_match('/^《([^《》]+)》/u', $title, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^"([^"]+)"/', $title, $matches)) {
            return $matches[1];
        }

        if (preg_match('/^([^，,。！!？?]+)[，,。！!？?]/u', $title, $matches)) {
            $candidate = trim($matches[1]);
            if (mb_strlen($candidate) >= 2) {
                return $candidate;
            }
        }

        if (preg_match('/^([^———]+)[———]/u', $title, $matches)) {
            $candidate = trim($matches[1]);
            if (mb_strlen($candidate) >= 2) {
                return $candidate;
            }
        }

        return $title;
    }

    private function parseVideoTitle($title) {
        $title = $this->cleanTitle($title) ?: $title;

        $result = [
            'base_title' => $title,
            'season' => null,
            'season_num' => null,
            'episode' => null,
            'episode_num' => null,
            'part' => null,
            'version' => null
        ];

        $cleanTitle = $title;

        $seasonPatterns = [
            '/第\s*([一二三四五六七八九十百千\d]+)\s*季/u' => 'cn',
            '/第\s*(\d+)\s*季/u' => 'num',
            '/Season\s*(\d+)/i' => 'num',
            '/S(\d{1,2})/i' => 'num',
            '/Ⅱ/u' => 'fixed_2',
            '/Ⅲ/u' => 'fixed_3',
        ];

        foreach ($seasonPatterns as $pattern => $type) {
            if (preg_match($pattern, $cleanTitle, $matches)) {
                if ($type === 'cn') {
                    $seasonNum = $this->chineseToNumber($matches[1]);
                    $result['season'] = $matches[0];
                    $result['season_num'] = $seasonNum;
                } elseif ($type === 'num') {
                    $result['season'] = $matches[0];
                    $result['season_num'] = intval($matches[1]);
                } elseif ($type === 'fixed_2') {
                    $result['season'] = $matches[0];
                    $result['season_num'] = 2;
                } elseif ($type === 'fixed_3') {
                    $result['season'] = $matches[0];
                    $result['season_num'] = 3;
                }
                break;
            }
        }

        $episodePatterns = [
            '/第\s*(\d+)\s*集/u' => 'num',
            '/第\s*([一二三四五六七八九十百千\d]+)\s*集/u' => 'cn',
            '/EP\s*(\d+)/i' => 'num',
            '/E\s*(\d+)/i' => 'num',
            '/(\d+)集/u' => 'num_suffix',
            '/_(\d{1,3})$/u' => 'underscore_num',
            '/-(\d{1,3})$/u' => 'dash_num',
        ];

        foreach ($episodePatterns as $pattern => $type) {
            if (preg_match($pattern, $cleanTitle, $matches)) {
                if ($type === 'num') {
                    $result['episode'] = $matches[0];
                    $result['episode_num'] = intval($matches[1]);
                } elseif ($type === 'cn') {
                    $epNum = $this->chineseToNumber($matches[1]);
                    $result['episode'] = $matches[0];
                    $result['episode_num'] = $epNum;
                } elseif ($type === 'num_suffix') {
                    if ($matches[1] <= 200) {
                        $result['episode'] = $matches[0];
                        $result['episode_num'] = intval($matches[1]);
                    }
                } elseif ($type === 'underscore_num' || $type === 'dash_num') {
                    $epNum = intval($matches[1]);
                    if ($epNum >= 1 && $epNum <= 200) {
                        $result['episode'] = $matches[0];
                        $result['episode_num'] = $epNum;
                    }
                }
                break;
            }
        }

        if ($result['season_num'] === null && $result['episode_num'] === null) {
            if (preg_match('/^(.+?)(\d{1,2})\s*第.*集$/u', $cleanTitle, $matches)) {
                $basePart = trim($matches[1]);
                $numPart = intval($matches[2]);
                if (mb_strlen($basePart) >= 2 && $numPart >= 1 && $numPart <= 99) {
                    $result['season'] = $matches[2];
                    $result['season_num'] = $numPart;
                }
            } elseif (preg_match('/^(.+?)(\d{1,2})$/u', $cleanTitle, $matches)) {
                $basePart = trim($matches[1]);
                $numPart = intval($matches[2]);
                if (mb_strlen($basePart) >= 2 && $numPart >= 1 && $numPart <= 99) {
                    if (!preg_match('/^\d+$/u', $cleanTitle)) {
                        $result['episode'] = $matches[2];
                        $result['episode_num'] = $numPart;
                    }
                }
            }
        }

        $partPatterns = [
            '/(上|下)部/u' => 'part',
            '/(上|下)篇/u' => 'part',
            '/(前|后)篇/u' => 'part',
            '/Part\s*(\d+)/i' => 'part_num',
        ];

        foreach ($partPatterns as $pattern => $type) {
            if (preg_match($pattern, $cleanTitle, $matches)) {
                $result['part'] = $matches[0];
                break;
            }
        }

        $versionPatterns = [
            '/(TV版|剧场版|电影版|OVA|OAD|特别篇|番外篇|SP|真人版|动画版|漫画版|普通话|粤语|日语|英语|国语)/u' => 'version'
        ];

        foreach ($versionPatterns as $pattern => $type) {
            if (preg_match($pattern, $cleanTitle, $matches)) {
                $result['version'] = $matches[1];
                break;
            }
        }

        $baseTitle = $cleanTitle;
        if ($result['season']) {
            $baseTitle = str_replace($result['season'], '', $baseTitle);
        }
        if ($result['episode']) {
            $baseTitle = str_replace($result['episode'], '', $baseTitle);
        }
        if ($result['part']) {
            $baseTitle = str_replace($result['part'], '', $baseTitle);
        }

        $baseTitle = preg_replace('/\s+/', ' ', $baseTitle);
        $baseTitle = trim($baseTitle, " \t\n\r\0\x0B-_—|·");
        $baseTitle = trim($baseTitle);

        if (empty($baseTitle)) {
            $baseTitle = $cleanTitle;
        }

        $result['base_title'] = $baseTitle;
        return $result;
    }

    private function chineseToNumber($str) {
        $cnNumbers = [
            '零' => 0, '一' => 1, '二' => 2, '两' => 2, '三' => 3,
            '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8,
            '九' => 9, '十' => 10, '百' => 100, '千' => 1000
        ];

        if (ctype_digit($str)) {
            return intval($str);
        }

        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $result = 0;
        $temp = 0;
        $lastUnit = 0;

        foreach ($chars as $char) {
            if (isset($cnNumbers[$char])) {
                $val = $cnNumbers[$char];
                if ($val >= 10) {
                    if ($temp == 0) $temp = 1;
                    $result += $temp * $val;
                    $temp = 0;
                    $lastUnit = $val;
                } else {
                    $temp = $val;
                }
            }
        }
        $result += $temp;

        return $result > 0 ? $result : null;
    }

    private function searchInSites($keyword) {
        $siteMgr = new ResourceSiteManager();
        $sites = $this->config['search_sites'] ?? [];
        $searchResult = null;

        if (empty($sites)) {
            $activeSites = $siteMgr->getAllSites(false);
            $maxSites = $this->config['max_search_sites'] ?? count($activeSites);
            $activeSites = array_slice($activeSites, 0, $maxSites);
            $searchResult = $this->searchSitesConcurrent($activeSites, $keyword);
        } else {
            $allSites = $siteMgr->getAllSites(false);
            $siteMap = [];
            foreach ($allSites as $s) {
                $siteMap[$s['name']] = $s;
            }
            $targetSites = [];
            foreach ($sites as $siteName) {
                if (isset($siteMap[$siteName])) {
                    $targetSites[] = $siteMap[$siteName];
                }
            }
            $maxSites = $this->config['max_search_sites'] ?? count($targetSites);
            $targetSites = array_slice($targetSites, 0, $maxSites);
            $searchResult = $this->searchSitesConcurrent($targetSites, $keyword);
        }

        return [
            'success' => !empty($searchResult['videos']),
            'videos' => $searchResult['videos'],
            'searched_sites' => $searchResult['searched_sites'],
            'successful_sites' => $searchResult['successful_sites'],
            'failed_sites' => $searchResult['failed_sites'],
        ];
    }

    private function searchSitesConcurrent($sites, $keyword) {
        if (empty($sites)) {
            return ['videos' => [], 'searched_sites' => 0, 'successful_sites' => [], 'failed_sites' => []];
        }

        $allVideos = [];
        $successfulSites = [];
        $failedSites = [];
        $hasMultiThread = false;
        $siteMgr = new ResourceSiteManager();

        try {
            require_once __DIR__ . '/../multi_thread/TaskRunner.php';
            if (TaskRunner::isMultiThreadAvailable()) {
                $hasMultiThread = true;
                $tasks = [];
                foreach ($sites as $idx => $site) {
                    $apiUrl = $site['api_url'] ?? '';
                    if (empty($apiUrl)) continue;
                    $tasks[] = [
                        'id' => $idx,
                        'site_name' => $site['name'],
                        'api_url' => $apiUrl,
                        'keyword' => $keyword
                    ];
                }

                if (!empty($tasks)) {
                    $runner = new TaskRunner([
                        'mode' => 'curl_multi',
                        'concurrency' => min(5, count($tasks)),
                        'timeout' => 30
                    ]);

                    $results = $runner->run($tasks, function($task) {
                        $apiUrl = $task['api_url'];
                        $keyword = $task['keyword'];
                        $siteName = $task['site_name'];

                        $url = $apiUrl;
                        if (strpos($url, '?') !== false) {
                            $url .= '&keyword=' . urlencode($keyword);
                        } else {
                            $url .= '?keyword=' . urlencode($keyword);
                        }

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

                        $response = curl_exec($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $error = curl_error($ch);
                        curl_close($ch);

                        if ($error || $httpCode < 200 || $httpCode >= 300) {
                            return ['site' => $siteName, 'videos' => [], 'success' => false, 'error' => $error ?: ('HTTP ' . $httpCode)];
                        }

                        $data = json_decode($response, true);
                        if ($data === null) {
                            return ['site' => $siteName, 'videos' => [], 'success' => false, 'error' => '非JSON响应'];
                        }

                        if (!empty($data['success']) && !empty($data['videos'])) {
                            foreach ($data['videos'] as &$v) {
                                $v['site'] = $siteName;
                            }
                            return ['site' => $siteName, 'videos' => $data['videos'], 'success' => true];
                        }

                        return ['site' => $siteName, 'videos' => [], 'success' => false, 'error' => '无搜索结果'];
                    });

                    foreach ($results as $result) {
                        if ($result->isSuccess()) {
                            $data = $result->getData();
                            if ($data['success'] && !empty($data['videos'])) {
                                $allVideos = array_merge($allVideos, $data['videos']);
                                $successfulSites[] = $data['site'];
                            } else {
                                $failedSites[] = [
                                    'site' => $data['site'],
                                    'error' => $data['error'] ?? '未知错误'
                                ];
                            }
                        } else {
                            $failedSites[] = [
                                'site' => $result->getTask()['site_name'] ?? '未知',
                                'error' => $result->getError() ?: '请求失败'
                            ];
                        }
                    }
                }
            }
        } catch (Throwable $e) {
        }

        if (!$hasMultiThread || empty($allVideos)) {
            foreach ($sites as $site) {
                $apiUrl = $site['api_url'] ?? '';
                if (empty($apiUrl)) continue;
                $siteName = $site['name'];
                try {
                    $result = $siteMgr->searchVideos($apiUrl, $keyword, 1, 10);
                    if ($result && $result['success'] && !empty($result['videos'])) {
                        foreach ($result['videos'] as $v) {
                            $v['site'] = $siteName;
                            $allVideos[] = $v;
                        }
                        if (!in_array($siteName, $successfulSites)) {
                            $successfulSites[] = $siteName;
                        }
                    } else {
                        $failedSites[] = [
                            'site' => $siteName,
                            'error' => '无搜索结果'
                        ];
                    }
                } catch (Throwable $e) {
                    $failedSites[] = [
                        'site' => $siteName,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        return [
            'videos' => $allVideos,
            'searched_sites' => count($sites),
            'successful_sites' => $successfulSites,
            'failed_sites' => $failedSites,
        ];
    }

    private function aiSmartMatch($videoInfo, $videos) {
        try {
            $aiFile = __DIR__ . '/AiVideoMatcher.php';
            if (!file_exists($aiFile)) {
                return [
                    'best_match' => null,
                    'all_matches' => [],
                    'method' => 'ai_not_available'
                ];
            }
            require_once $aiFile;
            if (!class_exists('AiVideoMatcher')) {
                return [
                    'best_match' => null,
                    'all_matches' => [],
                    'method' => 'ai_class_not_found'
                ];
            }
            $matcher = @new AiVideoMatcher();
            $result = @$matcher->smartMatch($videoInfo, $videos);
            return $result;
        } catch (Throwable $e) {
            return [
                'best_match' => null,
                'all_matches' => [],
                'method' => 'ai_error_' . $e->getMessage()
            ];
        }
    }

    private function findBestMatch($videoInfo, $videos) {
        $keyword = $videoInfo['base_title'] ?? $videoInfo['title'];
        $targetSeason = $videoInfo['season_num'] ?? null;
        $targetPart = $videoInfo['part'] ?? null;
        $targetVersion = $videoInfo['version'] ?? null;
        $threshold = $this->config['match_threshold'] ?? 60;
        $bestMatch = null;
        $bestScore = 0;

        $excludePatterns = [
            '/电影解说/i',
            '/预告片/i',
            '/片花/i',
            '/花絮/i',
            '/剪辑/i',
            '/解说/i',
            '/速看/i',
            '/混剪/i',
            '/盘点/i',
            '/reaction/i',
            '/MV/i',
            '/主题曲/i',
            '/片尾曲/i',
            '/片头曲/i',
            '/OST/i',
        ];

        foreach ($videos as $video) {
            $videoName = $video['name'] ?? '';
            $videoRemarks = $video['remarks'] ?? '';
            $fullName = $videoName . ' ' . $videoRemarks;

            $isExcluded = false;
            foreach ($excludePatterns as $pattern) {
                if (preg_match($pattern, $videoName)) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) {
                continue;
            }

            $videoParsed = $this->parseVideoTitle($fullName);
            $videoBaseTitle = $videoParsed['base_title'];
            $videoSeason = $videoParsed['season_num'];
            $videoEpisode = $videoParsed['episode_num'];
            $videoPart = $videoParsed['part'];
            $videoVersion = $videoParsed['version'];

            $baseScore = $this->calculateBaseMatchScore($keyword, $videoBaseTitle);

            if ($baseScore < 50) {
                continue;
            }

            $score = $baseScore;

            if ($keyword && $videoBaseTitle) {
                if (mb_strpos($videoBaseTitle, $keyword) !== false) {
                    $score += 10;
                }
                if (mb_strpos($keyword, $videoBaseTitle) !== false) {
                    $score += 5;
                }
            }

            $seasonMatch = false;
            if ($targetSeason !== null && $videoSeason !== null) {
                if ($targetSeason == $videoSeason) {
                    $score += 25;
                    $seasonMatch = true;
                } else {
                    $score -= 30;
                }
            } elseif ($targetSeason !== null && $videoSeason === null) {
                if ($targetSeason == 1) {
                    $score += 5;
                } else {
                    $score -= 10;
                }
            }

            $episodeMatch = false;
            $targetEpisode = $videoInfo['episode_num'] ?? null;
            if ($targetEpisode !== null && $videoEpisode !== null) {
                if ($targetEpisode == $videoEpisode) {
                    $score += 20;
                    $episodeMatch = true;
                }
            }

            if ($targetPart && $videoPart) {
                if ($targetPart == $videoPart) {
                    $score += 15;
                } else {
                    $score -= 15;
                }
            }

            if ($targetVersion && $videoVersion) {
                if ($targetVersion == $videoVersion) {
                    $score += 10;
                } else {
                    $score -= 5;
                }
            }

            if (!empty($videoRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片/u', $videoRemarks)) {
                    $score += 5;
                }
            }

            $score = min(100, max(0, $score));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = [
                    'video' => $video,
                    'score' => round($score, 2),
                    'base_score' => round($baseScore, 2),
                    'season_match' => $seasonMatch,
                    'episode_match' => $episodeMatch,
                    'site' => $video['site'] ?? ''
                ];
            }
        }

        if ($bestScore >= $threshold) {
            return $bestMatch;
        }

        // 最佳努力匹配：只要有合理分数就返回，避免阈值过高导致漏配
        if ($bestScore >= 50) {
            return $bestMatch;
        }

        return null;
    }

    private function calculateBaseMatchScore($str1, $str2) {
        $str1 = trim($str1);
        $str2 = trim($str2);

        if (empty($str1) || empty($str2)) {
            return 0;
        }

        if ($str1 === $str2) {
            return 100;
        }

        $norm1 = TitleNormalizer::normalize($str1);
        $norm2 = TitleNormalizer::normalize($str2);

        if ($norm1 === $norm2 && !empty($norm1)) {
            return 100;
        }

        $season1 = TitleNormalizer::getSeasonInfo($str1);
        $season2 = TitleNormalizer::getSeasonInfo($str2);
        $seasonPenalty = 0;
        if ($season1 !== null && $season2 !== null && $season1 !== $season2) {
            $seasonPenalty = 40;
        } elseif ($season1 !== null && $season1 > 1 && $season2 === null) {
            $seasonPenalty = 20;
        } elseif ($season2 !== null && $season2 > 1 && $season1 === null) {
            $seasonPenalty = 20;
        }

        $str1 = $norm1;
        $str2 = $norm2;

        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);

        if ($len1 == 0 || $len2 == 0) {
            return 0;
        }

        $short = $len1 < $len2 ? $str1 : $str2;
        $long = $len1 < $len2 ? $str2 : $str1;
        $shortLen = mb_strlen($short);
        $longLen = mb_strlen($long);

        if (mb_strpos($long, $short) !== false) {
            $pos = mb_strpos($long, $short);
            $ratio = $shortLen / $longLen;

            $startsWith = ($pos === 0);
            $endsWith = ($pos + $shortLen === $longLen);

            $seasonSuffix = '';
            $spinOffSuffix = false;
            if ($startsWith) {
                $suffix = mb_substr($long, $shortLen);
                if (preg_match('/^\s*[第\d一二三四五六七八九十百千\s部季期篇辑上下 seasonsS0-9]+$/u', $suffix)) {
                    $seasonSuffix = $suffix;
                }
                if (preg_match('/^\s*[之]/u', $suffix)) {
                    $spinOffSuffix = true;
                }
            }

            $subScore = 0;
            if ($ratio >= 0.7) {
                $subScore = 100;
            } elseif ($ratio >= 0.5) {
                if ($startsWith) {
                    if ($seasonSuffix) $subScore = 95;
                    elseif ($spinOffSuffix) $subScore = 55;
                    else $subScore = 90;
                } else {
                    $subScore = $endsWith ? 85 : 70;
                }
            } elseif ($ratio >= 0.35) {
                if ($startsWith) {
                    if ($seasonSuffix) $subScore = 92;
                    elseif ($spinOffSuffix) $subScore = 45;
                    else $subScore = 75;
                } elseif ($endsWith) {
                    $subScore = 70;
                } else {
                    $subScore = 45;
                }
            } else {
                if ($startsWith) {
                    if ($seasonSuffix) $subScore = 85;
                    elseif ($spinOffSuffix) $subScore = 35;
                    else $subScore = 55;
                } elseif ($endsWith) {
                    $subScore = 45;
                } else {
                    $subScore = 25;
                }
            }

            return round(max(0, $subScore - $seasonPenalty), 2);
        }

        $prefixMatchLen = 0;
        $minLen = min($len1, $len2);
        for ($i = 0; $i < $minLen; $i++) {
            if (mb_substr($str1, $i, 1) === mb_substr($str2, $i, 1)) {
                $prefixMatchLen++;
            } else {
                break;
            }
        }

        $suffixMatchLen = 0;
        for ($i = 1; $i <= $minLen; $i++) {
            if (mb_substr($str1, -$i, 1) === mb_substr($str2, -$i, 1)) {
                $suffixMatchLen++;
            } else {
                break;
            }
        }

        $commonChars = 0;
        $chars1 = preg_split('//u', $str1, -1, PREG_SPLIT_NO_EMPTY);
        $chars2 = preg_split('//u', $str2, -1, PREG_SPLIT_NO_EMPTY);
        $charCount1 = array_count_values($chars1);
        $charCount2 = array_count_values($chars2);

        foreach ($charCount1 as $char => $count) {
            if (isset($charCount2[$char])) {
                $commonChars += min($count, $charCount2[$char]);
            }
        }

        $totalChars = max($len1, $len2);
        $charSimilarity = $totalChars > 0 ? ($commonChars / $totalChars) * 100 : 0;

        $prefixBonus = 0;
        if ($prefixMatchLen > 0) {
            $prefixBonus = ($prefixMatchLen / $minLen) * 30;
        }

        $suffixBonus = 0;
        if ($suffixMatchLen > 0) {
            $suffixBonus = ($suffixMatchLen / $minLen) * 15;
        }

        // 高字符相似度时直接给高分，避免单个数字差异（如阿凡达2 vs 阿凡达）导致漏配
        if ($charSimilarity >= 90) {
            return round(min(100, 90 + $prefixBonus * 0.1 + $suffixBonus * 0.1 - $seasonPenalty), 2);
        }
        if ($charSimilarity >= 80) {
            return round(min(100, 80 + $prefixBonus * 0.2 + $suffixBonus * 0.2 - $seasonPenalty), 2);
        }

        $finalScore = $charSimilarity * 0.6 + $prefixBonus + $suffixBonus;
        $finalScore = min(100, max(0, $finalScore - $seasonPenalty));

        return round($finalScore, 2);
    }

    private function findEpisodeUrl($urls, $episodeInfo, $videoInfo = null) {
        $targetEpNum = null;
        $targetSubtitle = is_array($videoInfo) ? ($videoInfo['episode_subtitle'] ?? '') : '';
        $targetEpisodeName = is_array($videoInfo) ? ($videoInfo['episode'] ?? '') : '';
        if (is_numeric($episodeInfo)) {
            $targetEpNum = intval($episodeInfo);
        } elseif (is_array($episodeInfo) && isset($episodeInfo['episode_num'])) {
            $targetEpNum = $episodeInfo['episode_num'];
            if (empty($targetSubtitle) && !empty($episodeInfo['episode_name'])) $targetSubtitle = $episodeInfo['episode_name'];
        } elseif (is_string($episodeInfo)) {
            $parsed = $this->parseVideoTitle($episodeInfo);
            $targetEpNum = $parsed['episode_num'];
        }

        if ($targetEpNum === null && !$targetSubtitle && !$targetEpisodeName) {
            return null;
        }

        $bestMatch = null;
        $bestDiff = PHP_INT_MAX;
        $bestSubtitleScore = -1;

        $toShort = function($s) {
            $s = preg_replace('/\s+/u', '', (string)$s);
            return preg_replace('/[，。！？：；、\-_]/u', '', $s);
        };
        $targetSubS = $toShort($targetSubtitle);
        $targetEpS = $toShort($targetEpisodeName);

        foreach ($urls as $index => $urlItem) {
            $epName = '';
            $epUrl = '';

            if (is_string($urlItem)) {
                $epUrl = $urlItem;
                $epName = '第' . ($index + 1) . '集';
            } elseif (is_array($urlItem)) {
                $epName = $urlItem['name'] ?? $urlItem['title'] ?? '';
                $epUrl = $urlItem['url'] ?? $urlItem['link'] ?? '';
            }

            if (empty($epUrl)) {
                continue;
            }

            // ===== v5.11 S3：先做分剧名(字幕)匹配，再做集次匹配。对资源站命名「第2集 张启山和吴老狗达成合作」很有效 =====
            if ($targetSubS !== '' || $targetEpS !== '') {
                $nameS = $toShort($epName);
                $subtitleScore = 0;
                if ($targetSubS !== '' && mb_strlen($targetSubS) >= 4) {
                    similar_text($nameS, $targetSubS, $pct1);
                    $subtitleScore = max($subtitleScore, $pct1);
                    if (strpos($nameS, $targetSubS) !== false || strpos($targetSubS, $nameS) !== false) {
                        $subtitleScore = max($subtitleScore, 99.0);
                    }
                }
                if ($targetEpS !== '' && mb_strlen($targetEpS) >= 4) {
                    similar_text($nameS, $targetEpS, $pct2);
                    $subtitleScore = max($subtitleScore, $pct2);
                    if (strpos($nameS, $targetEpS) !== false || strpos($targetEpS, $nameS) !== false) {
                        $subtitleScore = max($subtitleScore, 99.0);
                    }
                }
                // 字幕分足够高时，直接返回该集（可以比单纯集次更准，防止集数错位的情况）
                if ($subtitleScore >= 65 && $subtitleScore > $bestSubtitleScore) {
                    $bestSubtitleScore = $subtitleScore;
                    $epNum = null;
                    $parsed = $this->parseVideoTitle($epName);
                    if ($parsed['episode_num'] !== null) $epNum = $parsed['episode_num'];
                    elseif (preg_match('/第\s*(\d+)\s*集/i', $epName, $mch)) $epNum = intval($mch[1]);
                    elseif (preg_match('/^(\d{1,4})/', $epName, $mch)) $epNum = intval($mch[1]);
                    if ($epNum === null) $epNum = $index + 1;
                    $bestMatch = ['name' => $epName, 'url' => $epUrl, 'episode_num' => $epNum, 'subtitle_score' => $subtitleScore];
                    $bestDiff = 0;
                }
            }

            if ($bestSubtitleScore >= 85) break; // 足够高就不继续扫了

            if ($targetEpNum === null) continue;

            $epNum = null;
            $parsed = $this->parseVideoTitle($epName);
            if ($parsed['episode_num'] !== null) {
                $epNum = $parsed['episode_num'];
            } else {
                if (preg_match('/第\s*(\d+)\s*集/i', $epName, $matches)) {
                    $epNum = intval($matches[1]);
                } elseif (preg_match('/^(\d{1,4})/', $epName, $matches)) {
                    $epNum = intval($matches[1]);
                } elseif (preg_match('/EP\s*(\d+)/i', $epName, $matches)) {
                    $epNum = intval($matches[1]);
                }
            }

            if ($epNum === null) {
                if ($index + 1 == $targetEpNum) {
                    $bestMatch = [
                        'name' => $epName,
                        'url' => $epUrl,
                        'episode_num' => $index + 1
                    ];
                    break;
                }
                continue;
            }

            if ($epNum == $targetEpNum) {
                $bestMatch = [
                    'name' => $epName,
                    'url' => $epUrl,
                    'episode_num' => $epNum
                ];
                break;
            }

            $diff = abs($epNum - $targetEpNum);
            if ($diff < $bestDiff) {
                $bestDiff = $diff;
                $bestMatch = [
                    'name' => $epName,
                    'url' => $epUrl,
                    'episode_num' => $epNum
                ];
            }
        }

        if ($bestMatch && ($bestSubtitleScore >= 65 || $bestDiff <= 10)) {
            return $bestMatch;
        }

        return null;
    }

    private function httpGet($url, $timeout = 30, $retry = 3) {
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        $proxyMgr = $this->proxyManager;
        if ($proxyMgr === null) {
            $proxyFile = __DIR__ . '/../proxy/ProxyManager.php';
            if (file_exists($proxyFile)) {
                @require_once $proxyFile;
                if (class_exists('ProxyManager')) {
                    $proxyMgr = @new ProxyManager();
                }
            }
        }

        for ($attempt = 0; $attempt <= $retry; $attempt++) {
            $ch = @curl_init();
            if (!$ch) {
                $lastError = 'curl_init failed';
                continue;
            }

            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            @curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
            @curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate'
            ]);
            @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $cookieFile = @tempnam(sys_get_temp_dir(), 'curl_cookie_');
            if ($cookieFile) {
                @curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
                @curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            }

            $currentProxy = null;
            if ($proxyMgr && @$proxyMgr->isEnabled() && ($this->useProxyOnFirstTry || $attempt > 0)) {
                $currentProxy = @$proxyMgr->applyProxyToCurl($ch);
            }

            $startTime = microtime(true);
            $response = @curl_exec($ch);
            $httpCode = 0;
            $error = '';
            if ($ch) {
                $httpCode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = @curl_error($ch);
                @curl_close($ch);
            }
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($cookieFile && file_exists($cookieFile)) {
                @unlink($cookieFile);
            }

            if ($currentProxy) {
                if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                    @$proxyMgr->markProxySuccess($currentProxy['id'], $responseTime);
                    return $response;
                } else {
                    @$proxyMgr->markProxyFailed($currentProxy['id']);
                }
            }

            if ($httpCode >= 200 && $httpCode < 300 && $response !== false && is_string($response)) {
                return $response;
            }

            $lastError = $error ? $error : ('HTTP ' . $httpCode);

            $isRetryable = $error && (
                strpos($error, 'Could not resolve') !== false ||
                strpos($error, 'Connection timed out') !== false ||
                strpos($error, 'Failed to connect') !== false ||
                strpos($error, 'Operation timed out') !== false
            ) || ($httpCode >= 500 || $httpCode == 429);

            if ($attempt < $retry && $isRetryable) {
                usleep(500000 + $attempt * 300000);
            }
        }

        $this->lastHttpError = $lastError;
        return false;
    }

    private function httpGetMobile($url, $timeout = 20) {
        $ch = @curl_init();
        if (!$ch) {
            return false;
        }

        $mobileUserAgents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        ];

        @curl_setopt($ch, CURLOPT_URL, $url);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        @curl_setopt($ch, CURLOPT_USERAGENT, $mobileUserAgents[array_rand($mobileUserAgents)]);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
        ]);
        @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $response = @curl_exec($ch);
        $httpCode = 0;
        if ($ch) {
            $httpCode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
            @curl_close($ch);
        }

        if ($httpCode >= 200 && $httpCode < 300 && $response !== false && is_string($response)) {
            return $response;
        }

        return false;
    }

    public function getPlatforms() {
        return $this->config['platforms'] ?? [];
    }

    public function updatePlatform($index, $platformData) {
        if (isset($this->config['platforms'][$index])) {
            $this->config['platforms'][$index] = array_merge($this->config['platforms'][$index], $platformData);
            $this->config['update_date'] = date('Y-m-d H:i:s');
            return $this->saveConfig();
        }
        return false;
    }

    public function addPlatform($platformData) {
        $this->config['platforms'][] = array_merge([
            'name' => '',
            'domain' => '',
            'enabled' => true,
            'pattern' => '',
            'title_selector' => '',
            'priority' => 10
        ], $platformData);
        $this->config['update_date'] = date('Y-m-d H:i:s');
        return $this->saveConfig();
    }

    public function deletePlatform($index) {
        if (isset($this->config['platforms'][$index])) {
            array_splice($this->config['platforms'], $index, 1);
            $this->config['update_date'] = date('Y-m-d H:i:s');
            return $this->saveConfig();
        }
        return false;
    }

    private function arrayExport($array, $indent = 0) {
        if (!is_array($array)) return var_export($array, true);
        if (empty($array)) return '[]';

        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        $isList = range(0, count($array) - 1) === array_keys($array);

        $items = [];
        foreach ($array as $key => $value) {
            $keyStr = $isList ? '' : var_export((string)$key, true) . ' => ';
            $items[] = $nextPrefix . $keyStr . $this->arrayExport($value, $indent + 1);
        }

        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }
}
