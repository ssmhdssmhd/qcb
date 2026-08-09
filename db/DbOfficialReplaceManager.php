<?php
/**
 * 数据库版官替API管理器
 * 使用数据库存储配置，完全兼容原 OfficialReplaceManager 接口
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/DbOfficialReplaceCache.php';
require_once __DIR__ . '/DbResourceSiteManager.php';
require_once __DIR__ . '/../gz/ResourceSiteManager.php';
require_once __DIR__ . '/../gz/TitleNormalizer.php';
require_once __DIR__ . '/../multi_thread/autoload.php';
require_once __DIR__ . '/../src/M3U8AdSkipper.php';
require_once __DIR__ . '/../gz/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/../pt/PtManager.php';

class DbOfficialReplaceManager {
    private $db;
    private $lastHttpError = '';
    private $proxyManager = null;
    private $useProxyOnFirstTry = true;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureTables();
    }

    public function setProxyManager($proxyManager) {
        $this->proxyManager = $proxyManager;
    }

    public function setUseProxyOnFirstTry($use) {
        $this->useProxyOnFirstTry = (bool)$use;
    }

    private function ensureTables() {
        if (!$this->db->tableExists('official_platforms')) {
            $this->db->initTables();
        }
        if (!$this->db->tableExists('sys_config')) {
            $this->db->initTables();
        }
    }

    private function parsePlatformRow($row) {
        if (!$row) return null;
        $platform = $row;

        if (isset($platform['enabled'])) {
            $platform['enabled'] = (bool)$platform['enabled'];
        }

        if (!empty($platform['config'])) {
            $config = json_decode($platform['config'], true);
            if (is_array($config)) {
                $platform = array_merge($config, $platform);
            }
        }
        unset($platform['config']);

        return $platform;
    }

    private function preparePlatformData($platformData) {
        $coreFields = ['name', 'domain', 'enabled', 'pattern', 'title_selector', 'priority'];
        $coreData = [];
        $extraConfig = [];

        foreach ($platformData as $key => $value) {
            if (in_array($key, $coreFields)) {
                if ($key === 'enabled') {
                    $coreData[$key] = $value ? 1 : 0;
                } elseif ($key === 'priority') {
                    $coreData[$key] = intval($value);
                } else {
                    $coreData[$key] = $value;
                }
            } else {
                $extraConfig[$key] = $value;
            }
        }

        if (!empty($extraConfig)) {
            $coreData['config'] = json_encode($extraConfig, JSON_UNESCAPED_UNICODE);
        }

        return $coreData;
    }

    public function isEnabled() {
        $config = $this->getConfig();
        return !empty($config['enabled']);
    }

    public function setEnabled($enabled) {
        $config = $this->getConfig();
        $config['enabled'] = (bool)$enabled;
        $config['update_date'] = date('Y-m-d H:i:s');
        $this->saveConfig($config);
        return ['success' => true, 'message' => '设置已更新'];
    }

    public function getConfig() {
        $row = $this->db->queryOne('SELECT config_value FROM sys_config WHERE config_key = ?', ['official_replace']);
        if ($row && !empty($row['config_value'])) {
            $config = json_decode($row['config_value'], true);
            if (is_array($config)) {
                if (!isset($config['platforms'])) {
                    $config['platforms'] = $this->getAllPlatforms(true);
                }
                return $config;
            }
        }
        return $this->getDefaultConfig();
    }

    public function saveConfig($config) {
        if (isset($config['platforms'])) {
            unset($config['platforms']);
        }
        $config['update_date'] = date('Y-m-d H:i:s');
        $configJson = json_encode($config, JSON_UNESCAPED_UNICODE);

        $exists = $this->db->queryOne('SELECT id FROM sys_config WHERE config_key = ?', ['official_replace']);
        if ($exists) {
            $this->db->update('sys_config', [
                'config_value' => $configJson,
                'description' => '官替API配置'
            ], 'config_key = ?', ['official_replace']);
        } else {
            $this->db->insert('sys_config', [
                'config_key' => 'official_replace',
                'config_value' => $configJson,
                'description' => '官替API配置'
            ]);
        }
        return true;
    }

    public function saveConfigData($config) {
        return $this->saveConfig($config);
    }

    private function getDefaultConfig() {
        return [
            'version' => '1.1',
            'update_date' => date('Y-m-d H:i:s'),
            'enabled' => true,
            'default_site' => '抖剧TV',
            'max_search_sites' => 8,
            'cache_ttl' => 3600,
            'search_sites' => ['抖剧TV', '量子', '暴风', '非凡', '天影', '6度资源', '豆包', '猫眼', '索尼', '最大', 'OK资源', '快车', '闪电', '丫丫（鸭鸭）', '无尽', '速播', '红牛', '豪华', '光速', '蓝光', '魔都', '看看', '樱花', '好花', '电影天堂', '茅台', '13大众', '百度', '爱奇艺资', '牛牛6', '蓝志', '天逸', '如意', '天繁', '西瓜'],
            'match_threshold' => 70,
            'default_priority_rule' => '数字越小越优先，范围 1-2000+，priority=1=最高优先',
        ];
    }

    public function getAllPlatforms($enabledOnly = false) {
        $sql = 'SELECT * FROM official_platforms';
        $params = [];
        if ($enabledOnly) {
            $sql .= ' WHERE enabled = 1';
        }
        $sql .= ' ORDER BY priority ASC';

        $rows = $this->db->query($sql, $params);
        $platforms = [];
        foreach ($rows as $row) {
            $platforms[] = $this->parsePlatformRow($row);
        }
        return $platforms;
    }

    public function getPlatformByName($name) {
        $row = $this->db->queryOne('SELECT * FROM official_platforms WHERE name = ?', [$name]);
        return $this->parsePlatformRow($row);
    }

    public function getPlatformByDomain($domain) {
        $allPlatforms = $this->getAllPlatforms(true);
        foreach ($allPlatforms as $platform) {
            if (stripos($domain, $platform['domain']) !== false) {
                return $platform;
            }
        }
        return null;
    }

    public function getPlatforms() {
        return $this->getAllPlatforms(true);
    }

    public function addPlatform($platformData) {
        $platform = array_merge([
            'name' => '',
            'domain' => '',
            'enabled' => true,
            'pattern' => '',
            'title_selector' => '',
            'priority' => 10
        ], $platformData);

        if (empty($platform['name']) || empty($platform['domain'])) {
            return ['success' => false, 'message' => '名称和域名不能为空'];
        }

        $exists = $this->getPlatformByName($platform['name']);
        if ($exists) {
            return ['success' => false, 'message' => '平台名称已存在'];
        }

        $data = $this->preparePlatformData($platform);
        $this->db->insert('official_platforms', $data);

        return ['success' => true, 'message' => '添加成功'];
    }

    public function updatePlatform($name, $platformData) {
        $exists = $this->getPlatformByName($name);
        if (!$exists) {
            return ['success' => false, 'message' => '平台不存在'];
        }

        if (isset($platformData['name'])) {
            unset($platformData['name']);
        }

        $data = $this->preparePlatformData($platformData);
        if (empty($data)) {
            return ['success' => true, 'message' => '更新成功'];
        }

        $this->db->update('official_platforms', $data, 'name = ?', [$name]);
        return ['success' => true, 'message' => '更新成功'];
    }

    public function deletePlatform($name) {
        $exists = $this->getPlatformByName($name);
        if (!$exists) {
            return ['success' => false, 'message' => '平台不存在'];
        }

        $this->db->delete('official_platforms', 'name = ?', [$name]);
        return ['success' => true, 'message' => '删除成功'];
    }

    public function resolve($url) {
        try {
            if (empty($url)) {
                return ['success' => false, 'message' => 'URL不能为空'];
            }

            $config = $this->getConfig();
            if (empty($config['enabled'])) {
                return ['success' => false, 'message' => '官替功能已禁用'];
            }

            // Step 0.1: 短链 / 跳转链接 解析（快手/抖音/微信跳转/360kan跳转/各种短链）
            $origUrl = $url;
            $resolvedUrl = $this->resolveRedirectChain($url, 5);
            if ($resolvedUrl && $resolvedUrl !== $url) {
                $url = $resolvedUrl;
            }

            // Step 0.2: URL 规范化（去掉 anchor，补充 https）
            $url = $this->normalizeVideoUrl($url);

            $platform = $this->detectPlatform($url);
            // 若 detectPlatform 失败，可能是移动域名（m.xxx.com / wx.mgtv.com / m.iqiyi.com / m.v.qq.com / m.bilibili.com / 360kan.com / douju.tv）
            if (!$platform) {
                $platform = $this->detectPlatformFuzzy($url);
            }
            if (!$platform) {
                return [
                    'success' => false,
                    'message' => '不支持的视频平台',
                    'original_url' => $origUrl,
                    'normalized_url' => $url,
                ];
            }

            $videoIds = $this->extractVideoId($url, $platform);
            if ((empty($videoIds['video_id']) && empty($videoIds['cover_id']))) {
                // 若一次提取失败，将 url 再次交给 移动站URL 重试 extract
                $mobileGuessUrl = $this->guessMobileUrl($url, $platform);
                if ($mobileGuessUrl && $mobileGuessUrl !== $url) {
                    $videoIds2 = $this->extractVideoId($mobileGuessUrl, $platform);
                    if (!empty($videoIds2['video_id']) || !empty($videoIds2['cover_id'])) {
                        $videoIds = $videoIds2;
                    }
                }
            }
            $videoId = $videoIds['video_id'] ?? '';
            $coverId = $videoIds['cover_id'] ?? '';

            $videoInfo = $this->fetchVideoInfo($url, $platform, $videoIds);
            $videoTitle = '';

            if ($videoInfo && !empty($videoInfo['title'])) {
                $videoTitle = $videoInfo['title'];
            } elseif ($videoId) {
                $videoTitle = $videoId;
            } elseif (!empty($coverId)) {
                $videoTitle = $coverId;
            } else {
                return ['success' => false, 'message' => '无法获取视频信息', 'platform' => $platform['name']];
            }

            $cleanTitle = $this->cleanTitle($videoTitle);
            if (empty($cleanTitle) && !empty($videoTitle)) {
                $cleanTitle = $videoTitle;
            }
            // 极端兜底：URL 路径里猜一个中文标题或ID
            if (empty($cleanTitle) || mb_strlen($cleanTitle) < 2) {
                $fromUrlGuess = $this->extractTitleFromUrl($url, $platform);
                if (!empty($fromUrlGuess) && mb_strlen($fromUrlGuess) >= 2) {
                    $cleanTitle = $fromUrlGuess;
                    $videoTitle = $fromUrlGuess;
                    $videoInfo['title'] = $fromUrlGuess;
                }
            }
            if (!empty($cleanTitle)) {
                $videoTitle = $cleanTitle;
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

            if (empty($videoInfo['episode_num']) && !empty($videoInfo['episode_info']['episode_num'])) {
                $videoInfo['episode_num'] = $videoInfo['episode_info']['episode_num'];
                $videoInfo['episode'] = $videoInfo['episode_info']['episode_name'];
            }

            if (empty($videoInfo['total_episodes']) && !empty($videoInfo['episode_info']['total_episodes'])) {
                $videoInfo['total_episodes'] = $videoInfo['episode_info']['total_episodes'];
            }

            $searchKeywords = $this->buildSearchKeywords($videoInfo, $platform);
            $searchResult = null;
            $usedKeyword = '';

            foreach ($searchKeywords as $keyword) {
                if (empty($keyword)) continue;
                $result = $this->searchInSites($keyword);
                if ($result['success'] && !empty($result['videos'])) {
                    $searchResult = $result;
                    $usedKeyword = $keyword;
                    break;
                }
            }

            if (!$searchResult || empty($searchResult['videos'])) {
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
                ];
            }

            $aiMatchResult = $this->aiSmartMatch($videoInfo, $searchResult['videos']);
            $bestMatch = $aiMatchResult['best_match'] ?? null;
            $allMatches = $aiMatchResult['all_matches'] ?? [];
            $matchMethod = $aiMatchResult['method'] ?? 'rule_based';

            if (!$bestMatch) {
                $bestMatch = $this->findBestMatch($videoInfo, $searchResult['videos']);
                $allMatches = $this->findAllMatches($videoInfo, $searchResult['videos']);
                $matchMethod = 'rule_based_fallback';
            }

            // pt 引擎增强匹配：当原有匹配失败或分数偏低时，使用 pt 平台特定算法重新匹配
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
                    // pt 引擎异常时静默降级到原有匹配结果
                }
            }

            $siteMatches = $this->groupMatchesBySite($allMatches);

            if (!$bestMatch) {
                $this->logResolve($url, $platform['name'], $videoTitle, 0, '', '', false);
                return [
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
                ];
            }

            $targetEpisodeUrl = $bestMatch['video']['first_url'] ?? $bestMatch['video']['url'] ?? '';
            $targetEpisodeName = '';
            $allUrls = $bestMatch['video']['urls'] ?? [];

            if (!empty($videoInfo['episode_num']) && !empty($allUrls)) {
                $epResult = $this->findEpisodeUrl($allUrls, $videoInfo['episode_num']);
                if ($epResult) {
                    $targetEpisodeUrl = $epResult['url'];
                    $targetEpisodeName = $epResult['name'];
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

            $adSkipUrl = '';
            if (!empty($targetEpisodeUrl)) {
                $adSkipUrl = $this->buildAdSkipUrl($targetEpisodeUrl);
            }

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
                'request_time' => time()
            ];
        } catch (Throwable $e) {
            $this->logResolve($url, '', '', 0, '', '', false);
            return [
                'success' => false,
                'message' => '处理异常: ' . $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR',
                'debug_info' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    public function resolveUrl($url) {
        return $this->resolve($url);
    }

    private function buildAdSkipUrl($m3u8Url) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $basePath = dirname($requestUri);
        $basePath = $basePath === '/' ? '' : $basePath;
        $selfUrl = $scheme . '://' . $host . $basePath;
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

    private function buildSearchKeywords($videoInfo, $platform) {
        $keywords = [];
        $baseTitle = $videoInfo['base_title'] ?? '';
        $seasonNum = $videoInfo['season_num'] ?? null;
        $version = $videoInfo['version'] ?? '';
        $part = $videoInfo['part'] ?? '';
        $videoTitle = $videoInfo['title'] ?? '';

        // 以 video_title 和 base_title 为准，作为最优先搜索词
        if (!empty($videoTitle)) {
            $keywords[] = $videoTitle;
        }
        if (!empty($baseTitle)) {
            $keywords[] = $baseTitle;
        }

        if (!empty($baseTitle)) {
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
                if ($cnNum) {
                    $keywords[] = $baseTitle . ' 第' . $cnNum . '季';
                }
                $keywords[] = $baseTitle . $seasonNum;
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

        $keywords = array_values(array_unique(array_filter($keywords, function($kw) {
            return !empty($kw) && mb_strlen($kw) >= 2;
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
        $config = $this->getConfig();
        $threshold = $config['match_threshold'] ?? 60;
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
            } elseif (preg_match('/cover\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $coverId = $matches[1];
                $videoId = null;
            } elseif (preg_match('/\/([a-zA-Z0-9]+)\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/vid=([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/x\/page\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&](?:cid|coverid)=([a-zA-Z0-9]+)/i', $url, $matches)) {
                $coverId = $matches[1];
            } elseif (preg_match('/[?&](?:vid|vid1|vids|tinyid)=([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'iqiyi.com') {
            if (preg_match('/\/([a-zA-Z0-9]{16,})\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/v_([a-zA-Z0-9_]+)\.html/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/a_([a-zA-Z0-9]{10,})/i', $url, $matches)) {
                // 爱奇艺 show/album id
                $coverId = 'a_' . $matches[1];
            } elseif (preg_match('/[?&](?:tvid|albumId|aid|vid|qipuId)=([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                if (stripos($matches[1], 'a_') === 0 || strpos($url, 'album') !== false) {
                    $coverId = $matches[1];
                } else {
                    $videoId = $matches[1];
                }
            }
        } elseif ($domain === 'youku.com') {
            if (preg_match('/id_([a-zA-Z0-9=]+)\.html/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/\/show\/id_([a-zA-Z0-9=]+)/i', $url, $matches)) {
                $coverId = $matches[1];
            } elseif (preg_match('/\/v_playlist\/.*?[?&]vid=([a-zA-Z0-9=]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'mgtv.com') {
            if (preg_match('/\/([a-zA-Z0-9]+)\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/\/b\/([0-9]{4,})\.html/i', $url, $matches)) {
                // 芒果TV新：/b/123456789.html
                $videoId = $matches[1];
                $coverId = $matches[1];
            } elseif (preg_match('/(?:play|h5)\/[^\/?]+\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&](?:vid|videoId|collectionId|fid|cid)=([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'bilibili.com') {
            if (preg_match('/(BV[a-zA-Z0-9]{8,12})/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/av(\d+)/i', $url, $matches)) {
                $videoId = 'av' . $matches[1];
            } elseif (preg_match('/[?&]p=(\d+)/i', $url, $matches)) {
                // 分P号，作为 episode 回退
                $coverId = 'p' . $matches[1];
            }
        } elseif ($domain === 'sohu.com') {
            if (preg_match('/\/(\d{5,})\.shtml/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/(?:v|vod)\/([a-zA-Z0-9_-]+)\.html?/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/my\.tv\.sohu\.com\/.*?[?&]id=(\d+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'pptv.com') {
            if (preg_match('/showpage\/([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/play\/([a-zA-Z0-9_-]+)\.html/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&](?:id|vid|programId)=([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        } elseif ($domain === 'douju.tv') {
            // 抖剧TV 平台链接
            if (preg_match('/(?:vod|play|video|detail)\/(?:id\/)?([a-zA-Z0-9_-]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&](?:id|vid|vod_id)=(\d+)/i', $url, $matches)) {
                $videoId = $matches[1];
            }
        }

        return [
            'video_id' => $videoId,
            'cover_id' => $coverId
        ];
    }

    private function detectPlatform($url) {
        $platforms = $this->getAllPlatforms(true);
        foreach ($platforms as $platform) {
            if (stripos($url, $platform['domain']) !== false) {
                return $platform;
            }
        }
        return null;
    }

    /**
     * 模糊匹配平台：弥补 m.v.qq.com / m.iqiyi.com / m.mgtv.com / www.iqiyi.com / film.qq.com / v.kuaishou.com / 360kan.com 等域名未加入 official_platforms 表时的识别
     */
    private function detectPlatformFuzzy($url) {
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) return null;
        $host = strtolower($host);

        // 移动站 / 子域 -> 主平台映射
        $domainAliases = [
            // 腾讯视频
            'v.qq.com' => 'v.qq.com',
            'm.v.qq.com' => 'v.qq.com',
            'film.qq.com' => 'v.qq.com',
            'v.qq.com,x' => 'v.qq.com',
            '360kan.com' => 'douju.tv',   // 360kan 根源 -> 抖剧TV 官替优先平台
            'www.360kan.com' => 'douju.tv',
            'm.360kan.com' => 'douju.tv',
            'douju.tv' => 'douju.tv',
            'www.douju.tv' => 'douju.tv',
            'm.douju.tv' => 'douju.tv',
            // 爱奇艺
            'iqiyi.com' => 'iqiyi.com',
            'www.iqiyi.com' => 'iqiyi.com',
            'm.iqiyi.com' => 'iqiyi.com',
            'wap.iqiyi.com' => 'iqiyi.com',
            'pcw.iqiyi.com' => 'iqiyi.com',
            // 优酷
            'youku.com' => 'youku.com',
            'www.youku.com' => 'youku.com',
            'm.youku.com' => 'youku.com',
            'v.youku.com' => 'youku.com',
            'player.youku.com' => 'youku.com',
            // 芒果TV
            'mgtv.com' => 'mgtv.com',
            'www.mgtv.com' => 'mgtv.com',
            'm.mgtv.com' => 'mgtv.com',
            'wx.mgtv.com' => 'mgtv.com',
            'hd.mgtv.com' => 'mgtv.com',
            // B站
            'bilibili.com' => 'bilibili.com',
            'www.bilibili.com' => 'bilibili.com',
            'm.bilibili.com' => 'bilibili.com',
            'b23.tv' => 'bilibili.com',   // B站短链
            // 搜狐
            'sohu.com' => 'sohu.com',
            'www.sohu.com' => 'sohu.com',
            'tv.sohu.com' => 'sohu.com',
            'm.tv.sohu.com' => 'sohu.com',
            'my.tv.sohu.com' => 'sohu.com',
            // PPTV/PP视频
            'pptv.com' => 'pptv.com',
            'www.pptv.com' => 'pptv.com',
            'm.pptv.com' => 'pptv.com',
            'v.pptv.com' => 'pptv.com',
            'player.pptv.com' => 'pptv.com',
        ];

        $matchedDomain = null;
        if (isset($domainAliases[$host])) {
            $matchedDomain = $domainAliases[$host];
        } else {
            // 后缀匹配：例 m.v.qq.com -> v.qq.com / www.mgtv.com -> mgtv.com
            foreach ($domainAliases as $alias => $canonical) {
                if (str_ends_with($host, '.' . $alias)) {
                    $matchedDomain = $canonical;
                    break;
                }
            }
        }
        if (!$matchedDomain) return null;

        // 按 canonical 域名从平台列表里找
        $platforms = $this->getAllPlatforms(true);
        foreach ($platforms as $platform) {
            if ($platform['domain'] === $matchedDomain) {
                return $platform;
            }
        }
        // 兜底：还是域名匹配（canonical domain 正好能通过 stripos 命中）
        foreach ($platforms as $platform) {
            if (stripos($matchedDomain, $platform['domain']) !== false) {
                return $platform;
            }
        }
        return null;
    }

    /**
     * 短链/跳转链接 解析：返回重定向后最终 URL
     */
    private function resolveRedirectChain($url, $maxHops = 4) {
        if ($maxHops <= 0 || empty($url)) return $url;
        $ch = @curl_init($url);
        if (!$ch) return $url;
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        @curl_setopt($ch, CURLOPT_HEADER, true);
        @curl_setopt($ch, CURLOPT_NOBODY, true);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        @curl_setopt($ch, CURLOPT_MAXREDIRS, $maxHops);
        @curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Safari/604.1');
        @curl_exec($ch);
        $final = @curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        @curl_close($ch);
        if (!empty($final) && is_string($final) && $final !== $url) {
            return $final;
        }
        return $url;
    }

    /**
     * URL 规范化：去锚点 + http 升级 https（纯 HTTP 站保留）+ 去多余 query
     */
    private function normalizeVideoUrl($url) {
        if (empty($url)) return $url;
        // 去锚点
        if (strpos($url, '#') !== false) {
            $url = explode('#', $url, 2)[0];
        }
        // 无前缀的情况
        if (preg_match('#^//#', $url)) {
            $url = 'https:' . $url;
        } elseif (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * 若 PC URL 提取失败，再试一次移动版
     */
    private function guessMobileUrl($url, $platform) {
        $domain = $platform['domain'] ?? '';
        if ($domain === 'v.qq.com') {
            return preg_replace('#https?://(?:www\.)?v\.qq\.com/#i', 'https://m.v.qq.com/', $url);
        }
        if ($domain === 'iqiyi.com') {
            return preg_replace('#https?://(?:www\.)?iqiyi\.com/#i', 'https://m.iqiyi.com/', $url);
        }
        if ($domain === 'youku.com') {
            return preg_replace('#https?://(?:www\.)?youku\.com/#i', 'https://m.youku.com/', $url);
        }
        if ($domain === 'mgtv.com') {
            return preg_replace('#https?://(?:www\.)?mgtv\.com/#i', 'https://m.mgtv.com/', $url);
        }
        if ($domain === 'bilibili.com') {
            return preg_replace('#https?://(?:www\.)?bilibili\.com/#i', 'https://m.bilibili.com/', $url);
        }
        return $url;
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

        $apiInfo = $this->fetchVideoInfoFromApi($videoId, $platform, $coverId, $url);
        if ($apiInfo) {
            if (!empty($apiInfo['title'])) {
                $title = $apiInfo['title'];
            }
            if (!empty($apiInfo['cover'])) {
                $cover = $apiInfo['cover'];
            }
        }

        if (empty($title) || mb_strlen($title) < 3) {
            $html = $this->httpGet($url);
            if ($html) {
                $htmlTitle = $this->extractTitle($html, $platform);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3) {
                    $title = $htmlTitle;
                }
                $htmlCover = $this->extractCover($html);
                if (!empty($htmlCover) && empty($cover)) {
                    $cover = $htmlCover;
                }
                $htmlEpisodeInfo = $this->extractEpisodeFromHtml($html, $platform);
                if (!empty($htmlEpisodeInfo['episode_num'])) {
                    $episodeInfo = $htmlEpisodeInfo;
                }
            }
            
            if ((empty($title) || mb_strlen($title) < 3) && in_array($platform['domain'], ['iqiyi.com', 'youku.com', 'mgtv.com', 'sohu.com', 'pptv.com'])) {
                $mobileHtml = $this->httpGetMobile($url);
                if ($mobileHtml) {
                    $mobileTitle = $this->extractTitle($mobileHtml, $platform);
                    if (!empty($mobileTitle) && mb_strlen($mobileTitle) >= 3) {
                        $title = $mobileTitle;
                    }
                    if (empty($cover)) {
                        $mobileCover = $this->extractCover($mobileHtml);
                        if (!empty($mobileCover)) {
                            $cover = $mobileCover;
                        }
                    }
                    if (empty($episodeInfo['episode_num'])) {
                        $mobileEpisodeInfo = $this->extractEpisodeFromHtml($mobileHtml, $platform);
                        if (!empty($mobileEpisodeInfo['episode_num'])) {
                            $episodeInfo = $mobileEpisodeInfo;
                        }
                    }
                }
            }
        }

        if (empty($title) || mb_strlen($title) < 3) {
            $urlTitle = $this->extractTitleFromUrl($url, $platform);
            if (!empty($urlTitle) && mb_strlen($urlTitle) >= 3) {
                $title = $urlTitle;
            }
        }

        return [
            'title' => $title,
            'cover' => $cover,
            'url' => $url,
            'platform' => $platform['name'],
            'episode_info' => $episodeInfo,
            'video_id' => $videoId,
            'cover_id' => $coverId
        ];
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
        if (empty($videoId) && empty($coverId)) {
            return null;
        }

        $platformName = $platform['name'] ?? '';
        $result = ['title' => null, 'cover' => null];
        $isCoverOnly = empty($videoId) && !empty($coverId);

        if ($platformName === '腾讯视频') {
            if ($isCoverOnly) {
                return $this->fetchTencentCoverInfo($coverId, $originalUrl);
            }

            $apiUrls = [
                'https://node.video.qq.com/x/api/float_vinfo2?vid=' . urlencode($videoId),
                'https://pbaccess.video.qq.com/trpc.vidplay.vidplay_2_0_fcgi.VidPlay2_0Fcgi/GetCmsVidInfoAll?data={"vid":"' . urlencode($videoId) . '","appVer":"3.5.57","platform":"40000"}',
                'https://access.video.qq.com/cgi-bin/varietycheck?vid=' . urlencode($videoId),
                'http://vv.video.qq.com/getinfo?vids=' . urlencode($videoId) . '&platform=101001&charge=0&otype=json',
            ];

            if (!empty($coverId)) {
                $apiUrls[] = 'https://v.qq.com/x/cover/' . urlencode($coverId) . '.html';
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

    private function fetchTencentCoverInfo($coverId, $originalUrl = '') {
        $result = ['title' => null, 'cover' => null];

        if (empty($coverId)) {
            return $result;
        }

        $coverUrl = !empty($originalUrl) ? $originalUrl : 'https://v.qq.com/x/cover/' . $coverId . '.html';

        try {
            $html = $this->httpGet($coverUrl, 10, 1);
            if ($html) {
                $htmlTitle = $this->extractTitle($html, ['name' => '腾讯视频', 'domain' => 'v.qq.com']);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && $htmlTitle !== '腾讯视频') {
                    $result['title'] = $htmlTitle;
                }
                $htmlCover = $this->extractCover($html);
                if (!empty($htmlCover)) {
                    $result['cover'] = $htmlCover;
                }

                if (empty($result['title'])) {
                    $altTitle = $this->extractTencentTitleFromHtml($html);
                    if (!empty($altTitle) && mb_strlen($altTitle) >= 3) {
                        $result['title'] = $altTitle;
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        return $result;
    }

    private function extractTencentTitleFromHtml($html) {
        $patterns = [
            '/<a[^>]+class="crumb[^"]*"[^>]*>\s*<span[^>]*>([^<]+)<\/span>/i',
            '/<div[^>]+class="[^"]*album[^"]*title[^"]*"[^>]*>([^<]+)<\/div>/i',
            '/<div[^>]+class="[^"]*cover[^"]*name[^"]*"[^>]*>([^<]+)<\/div>/i',
            '/<div[^>]+class="[^"]*video[^"]*name[^"]*"[^>]*>([^<]+)<\/div>/i',
            '/"title"\s*:\s*"([^"]+)"/i',
            '/"tvName"\s*:\s*"([^"]+)"/i',
            '/"video_title"\s*:\s*"([^"]+)"/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $title = trim($matches[1]);
                if (!empty($title) && mb_strlen($title) >= 2 && $title !== '腾讯视频') {
                    return $this->cleanTitle($title);
                }
            }
        }

        if (preg_match('/window\.__INJECT_HEAD_DATA__\s*=\s*(\{.*?\})\s*;/s', $html, $matches)) {
            $data = json_decode($matches[1], true);
            if ($data) {
                if (isset($data['title']) && is_string($data['title'])) {
                    $title = trim($data['title']);
                    if (!empty($title) && mb_strlen($title) >= 2 && $title !== '腾讯视频') {
                        return $this->cleanTitle($title);
                    }
                }
            }
        }

        return null;
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
        $title = trim($title ?? '');
        if (empty($title)) return null;

        // 先做完整 HTML/实体/Unicode 解码（&#12298;《  &#12299;》  &#x300A;等，&amp;、&quot; 等）
        if (strpos($title, '&') !== false || strpos($title, '\\u') !== false || strpos($title, '\\x') !== false) {
            // 先处理 JSON 中的 \uXXXX \u00XX（UTF-16 LE/BE 转义序列）
            if (strpos($title, '\\u') !== false || strpos($title, '\\x') !== false) {
                $jsonWrapped = preg_replace_callback(
                    '/\\\\u([0-9a-fA-F]{4})|\\\\x([0-9a-fA-F]{2})/',
                    function($m) {
                        if (isset($m[2]) && $m[2] !== '') {
                            $c = chr(hexdec($m[2]));
                            return $c;
                        }
                        $cp = hexdec($m[1]);
                        if ($cp < 0x80) return chr($cp);
                        if (function_exists('mb_chr')) {
                            return mb_chr($cp, 'UTF-8');
                        }
                        // UTF-16 -> UTF-8 fallback
                        $s = chr(0xD8 | ($cp >> 10)) . chr(0xDC | ($cp & 0x3FF)); // wrong surrogate but safe null
                        return @iconv('UTF-16BE', 'UTF-8//IGNORE', pack('n', $cp)) ?: '';
                    },
                    $title
                );
                if ($jsonWrapped && $jsonWrapped !== '') {
                    $title = $jsonWrapped;
                }
                // 再用 json_decode 兜底
                $jsonSafe = '"' . addcslashes($title, "\\\"\n\r\t\v\f/") . '"';
                $jsonDecode = json_decode($jsonSafe, true);
                if (is_string($jsonDecode) && $jsonDecode !== '') {
                    $title = $jsonDecode;
                }
            }
            // HTML 实体解码（含中文实体）
            $decoded = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded && $decoded !== '') {
                $title = $decoded;
            }
        }

        // 优先提取书名号、引号内的纯标题
        $title = $this->extractPureTitle($title);

        // 清理常见后缀描述文字（现在用强规则，从平台名、限定词切分）
        // 01. 平台名相关
        $title = preg_replace('/\s*[-_|｜·•·—–]*\s*(?:腾讯视频|爱奇艺|优酷视频?|芒果TV|哔哩哔哩|bilibili|搜狐视频|PP视频|PP聚力|PPTV|西瓜视频|好看视频|土豆视频|快手视频|抖音|6间房|风行|暴风影音|乐视|CNTV|CCTV\d*)\b.*?$/i', '', $title);
        // 02. 通用后缀
        $title = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/免费在线(?:播放|观看).*?$/u', '', $title);
        $title = preg_replace('/全集(?:播放|观看|下载|完整版|高清)?.*?$/u', '', $title);
        $title = preg_replace('/高清(?:未删减|正版|蓝光|原画|中字|国语|英语|完整版)?.*?$/u', '', $title);
        $title = preg_replace('/完整版(?:未删减|超清|高清)?.*?$/u', '', $title);
        $title = preg_replace('/4K(?:超清|高清)?.*?$/u', '', $title);
        $title = preg_replace('/(?:蓝光|1080P|720P|480P|HDR|杜比).*?$/iu', '', $title);
        $title = preg_replace('/_腾讯视频/i', '', $title);
        $title = preg_replace('/- 腾讯视频/i', '', $title);
        $title = preg_replace('/最新一期.*?$/u', '', $title);
        $title = preg_replace('/第\s*\d+\s*期.*?$/u', '', $title);
        // 03. 预约、预告、广告、片花、宣传片、MV、演唱会、发布会、先导片、未播
        $title = preg_replace('/(?:预约|即将上线|未播|敬请期待|首播|发布会|先导(?:片|预告)?|概念|定档预告|终极预告|电影原声带|MV|预告(?:片|版|曲)?|片花|宣传片|花絮|剪辑版?|混剪|剪辑|解说|速看|盘点|Reaction|主题曲|片头曲|片尾曲|推广曲|插曲|OST|特辑|cut|饭制|翻唱|直播回放|合集|合集版|二创|恶搞|P图|幕后记录|综艺|跑男|快乐大本营|脱口秀|春晚|元宵晚会|发布会|盛典|颁奖礼|完整版花絮|超长花絮|观影|彩蛋|纪录片|预告合集|活动|广告|代言|杂志|红毯|直播|先导).*?$/iu', '', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|｜·•‧·–");
        $title = trim($title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);

        $invalidTitles = ['腾讯视频', '爱奇艺', '优酷', '优酷视频', '芒果TV', '哔哩哔哩', 'bilibili', '搜狐视频', 'PP视频', 'PPTV', '西瓜视频', '预告', '片花', '花絮', '宣传片', 'MV', '合集', '正片', '高清', '完整版', '全集', '4K', '蓝光', '官方', '官方版', '电影', '电视剧', '综艺', '动漫'];
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

    public function searchInSites($keyword, $maxSites = 5) {
        // 优先使用 DbResourceSiteManager（数据库版本，含 priority 排序和最新资源站列表）
        $dbSiteMgrClass = __DIR__ . '/DbResourceSiteManager.php';
        $siteMgr = null;
        if (file_exists($dbSiteMgrClass) && class_exists('Database')) {
            require_once $dbSiteMgrClass;
            if (class_exists('DbResourceSiteManager')) {
                try {
                    $siteMgr = new DbResourceSiteManager();
                } catch (Throwable $e) {
                    $siteMgr = null;
                }
            }
        }
        if (!$siteMgr) {
            if (!class_exists('ResourceSiteManager', false)) {
                require_once __DIR__ . '/ResourceSiteManager.php';
            }
            $siteMgr = new ResourceSiteManager();
        }

        $config = $this->getConfig();
        $sites = $config['search_sites'] ?? [];
        $allVideos = [];
        $successfulSites = [];
        $failedSites = [];
        $searchedSites = 0;

        if (empty($sites)) {
            // DbResourceSiteManager 的 getAllSites 已按 priority ASC 排序（1=最优先），抖剧TV 将第一个被搜索
            $result = $siteMgr->searchAllSites($keyword, 3, 5);
            if ($result['success']) {
                foreach (($result['results'] ?? []) as $siteResult) {
                    $siteName = $siteResult['site'] ?? ($siteResult['site_name'] ?? '未知');
                    if (!empty($siteResult['videos'])) {
                        foreach ($siteResult['videos'] as $v) {
                            if (empty($v['site']) && !empty($siteResult['site_name'])) $v['site'] = $siteResult['site_name'];
                            if (empty($v['site'])) $v['site'] = $siteName;
                            $allVideos[] = $v;
                        }
                        $successfulSites[] = $siteName;
                    } else {
                        $failedSites[] = ['site' => $siteName, 'error' => '无搜索结果'];
                    }
                    $searchedSites++;
                }
            }
        } else {
            // DbResourceSiteManager 的 getAllSites 已按 priority ASC 排序；但优先遵循 config.search_sites 指定顺序（抖剧TV第一）
            $allSites = $siteMgr->getAllSites(false);
            $siteMap = [];
            foreach ($allSites as $s) {
                $siteMap[$s['name']] = $s;
            }
            $maxLimit = $config['max_search_sites'] ?? 5;
            foreach ($sites as $siteName) {
                if (!isset($siteMap[$siteName])) continue;
                $apiUrl = $siteMap[$siteName]['api_url'] ?? '';
                if (empty($apiUrl)) continue;
                $searchedSites++;
                try {
                    $result = $siteMgr->searchVideos($apiUrl, $keyword, 1, 10);
                    if ($result && $result['success'] && !empty($result['videos'])) {
                        foreach ($result['videos'] as $v) {
                            if (empty($v['site'])) $v['site'] = $siteName;
                            if (empty($v['site_name'])) $v['site_name'] = $siteName;
                            $allVideos[] = $v;
                        }
                        $successfulSites[] = $siteName;
                    } else {
                        $failedSites[] = ['site' => $siteName, 'error' => '无搜索结果'];
                    }
                } catch (Throwable $e) {
                    $failedSites[] = ['site' => $siteName, 'error' => $e->getMessage()];
                }
                if (count($successfulSites) >= $maxLimit) {
                    break;
                }
            }
        }

        return [
            'success' => !empty($allVideos),
            'videos' => $allVideos,
            'searched_sites' => $searchedSites,
            'successful_sites' => $successfulSites,
            'failed_sites' => $failedSites,
        ];
    }

    private function aiSmartMatch($videoInfo, $videos) {
        try {
            $aiFile = __DIR__ . '/../gz/AiVideoMatcher.php';
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
        $targetYear = $videoInfo['year'] ?? null;
        $targetActors = $videoInfo['actors'] ?? ($videoInfo['actor'] ?? []);
        if (is_string($targetActors)) {
            $targetActors = preg_split('/[\/,，、\s]+/u', $targetActors, -1, PREG_SPLIT_NO_EMPTY);
        }
        $targetEpisode = $videoInfo['episode_num'] ?? null;
        $config = $this->getConfig();
        $threshold = intval($config['match_threshold'] ?? 60);
        // 极端兜底阈值：当高阈值无命中时自动降低门槛（避免用户设置过高）
        $fallbackThreshold = max(38, min(55, $threshold - 18));
        $hardFloorThreshold = 35; // 绝对下限，低于这个的匹配一定不返回
        $bestMatch = null;
        $bestScore = 0;

        $excludePatterns = [
            '/电影解说/i', '/(?:剧情|电影|电视剧|综艺|动漫|番剧|国产剧|日剧|韩剧|欧美剧|泰剧|港剧|台剧)?(?:剧情|独家|深度|硬核)?解说/i',
            '/预告片(?:片|版|曲)?/i', '/(?:终极|先导|定档|情感|剧情|角色|人物|人物关系)?预告(?:片|版|PV|MV)?/i',
            '/片花/i', '/花絮/i', '/(?:删减|精彩|幕后|超长)?花絮(?:合集)?/i',
            '/剪辑/i', '/(?:高能|精彩|情感|剧情|人物|影视)?剪辑(?:版|合集)?/i',
            '/速看(?:视频)?/i', '/(?:X分钟|几分钟)?速(?:看|览|讲|说)/i',
            '/混剪/i', '/盘点/i', '/reaction/i',
            '/(?:官方|剧情|角色|饭制|自制|翻唱|钢琴版|吉他版|音乐)?MV\b/i',
            '/主题曲/i', '/片尾曲/i', '/片头曲/i', '/插曲/i', '/推广(?:曲|曲MV)|宣传(?:曲|MV)|概念曲|定档曲/i',
            '/OST\b/i', '/(?:bgm|BGM)\s*[：:]/',
            '/先导(?:片|预告|版|曲)/i', '/(?:发布会|首映礼|路演|粉丝见面会|综艺|演唱会|生日会|生日特辑|生日直播)/i',
            '/(?:饭制|二创|鬼畜|恶搞|搞笑|沙雕|reaction|REACTION|Reaction)/i',
            '/(?:cut|CUT|Cut)\s*$/i', '/(?:合集|合辑|精选集|名场面合集|高光合集)/i',
            '/(?:广告|赞助|代言|品牌日|冠名|合作视频|植入|VCR|ID视频|杂志|时装周|红毯|颁奖礼|盛典)/i',
            '/(?:纪录片|记录片|专题片|幕后(?:纪录片|记录|花絮)?|超长(?:幕后|花絮)|观影指南|彩蛋|番外|番外篇)/i',
            '/(?:直播回放|全程回放|直播录屏|直播cut|直播精选)/i',
            '/(?:全集|全\d+集)?(?:抢先|超前|预约|即将(?:上线|播出|开播)|未播|定档|档期|开播(?:仪式|盛典)?|首播)(?:版)?/i',
        ];
        $excludeNamesIfContainsKeyword = ['预告片', '片花', '花絮', '剪辑版', '解说', '速看', 'MV', 'OST', 'reaction', '混剪', '盘点', 'cut'];

        $year = $targetYear;
        $actorList = $targetActors;

        foreach ($videos as $video) {
            $videoName = $video['name'] ?? '';
            $videoRemarks = $video['remarks'] ?? '';
            $videoNote = $video['note'] ?? '';
            $videoActors = $video['actor'] ?? ($video['actors'] ?? '');
            $videoYear = $video['year'] ?? null;
            if (empty($videoYear)) {
                if (preg_match('/(?:19|20)\d{2}/u', $videoName . ' ' . $videoRemarks, $ym)) {
                    $videoYear = intval($ym[0]);
                }
            }
            $fullName = trim($videoName . ' ' . $videoRemarks . ' ' . $videoNote);

            $isExcluded = false;
            foreach ($excludePatterns as $pattern) {
                if (@preg_match($pattern, $videoName)) {
                    $isExcluded = true;
                    break;
                }
            }
            if (!$isExcluded) {
                foreach ($excludeNamesIfContainsKeyword as $ex) {
                    if (stripos($videoName, $ex) !== false) {
                        $isExcluded = true;
                        break;
                    }
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

            // 计算基础分（最多种关键词变体尝试，取最大值）
            $baseScore = 0;
            $variants = [$keyword, $videoInfo['title'] ?? null];
            if (!empty($videoInfo['parsed']['base_title'])) $variants[] = $videoInfo['parsed']['base_title'];
            if (!empty($videoInfo['pure_title'] ?? null)) $variants[] = $videoInfo['pure_title'];
            $variants = array_values(array_filter(array_unique($variants)));
            foreach ($variants as $kw) {
                if (empty($kw)) continue;
                $s = $this->calculateBaseMatchScore($kw, $videoBaseTitle);
                if ($s > $baseScore) $baseScore = $s;
            }
            // 完全包含时额外加分
            $anyContains = false;
            foreach ($variants as $kw) {
                if (empty($kw)) continue;
                if ($videoBaseTitle && (mb_strpos($videoBaseTitle, $kw) !== false || mb_strpos($kw, $videoBaseTitle) !== false)) {
                    $anyContains = true;
                    break;
                }
                if ($videoName && (mb_strpos($videoName, $kw) !== false || mb_strpos($kw, $videoName) !== false)) {
                    $anyContains = true;
                    break;
                }
            }

            // 硬门槛：基础分不能太低（变体试过后的最低门槛 32）
            if ($baseScore < 32) {
                continue;
            }

            $score = $baseScore;
            if ($anyContains) $score += 8;

            $seasonMatch = false;
            $episodeMatch = false;
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
                    $score -= 12;
                }
            }

            if ($targetEpisode !== null && $videoEpisode !== null) {
                if ($targetEpisode == $videoEpisode) {
                    $score += 20;
                    $episodeMatch = true;
                } else {
                    // 剧集号错了，但同一部剧，仅扣分
                    $score -= max(0, 15 - abs($targetEpisode - $videoEpisode));
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

            // 年份交叉验证
            if (!empty($year) && !empty($videoYear)) {
                if (intval($year) === intval($videoYear)) {
                    $score += 12;
                } elseif (abs(intval($year) - intval($videoYear)) === 1) {
                    $score += 4; // 跨年 ±1 合理
                } else {
                    $score -= 8;
                }
            }
            // 演员交叉验证（演员名任意一个命中都加分）
            if (!empty($actorList) && is_array($actorList)) {
                $actorsText = $videoActors . ' ' . $videoName . ' ' . $videoRemarks;
                $hitCount = 0;
                foreach ($actorList as $act) {
                    $act = trim($act);
                    if (empty($act) || mb_strlen($act) < 2) continue;
                    if (mb_strpos($actorsText, $act) !== false) $hitCount++;
                }
                if ($hitCount > 0) {
                    $score += min(18, 6 * $hitCount);
                }
            }

            if (!empty($videoRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片|蓝光|1080P|4K|未删减|完整版/u', $videoRemarks)) {
                    $score += 5;
                }
                // 命中 remarks 中包含 keyword（精确部分名）
                if (!empty($keyword) && mb_strpos($videoRemarks, $keyword) !== false) {
                    $score += 6;
                }
            }

            $score = min(110, max(0, $score));

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = [
                    'video' => $video,
                    'score' => round($score, 2),
                    'base_score' => round($baseScore, 2),
                    'season_match' => $seasonMatch,
                    'episode_match' => $episodeMatch,
                    'site' => $video['site'] ?? '',
                    'year_match' => (!empty($year) && !empty($videoYear) && intval($year) === intval($videoYear)),
                    'actor_hit' => $actorList ? count(array_filter($actorList, function($a) use ($videoActors, $videoName, $videoRemarks) {
                        $a = trim($a); if (empty($a) || mb_strlen($a) < 2) return false;
                        return mb_strpos($videoActors . ' ' . $videoName . ' ' . $videoRemarks, $a) !== false;
                    })) : 0,
                ];
            }
        }

        if ($bestScore >= $threshold) {
            return $bestMatch;
        }

        // 门槛二：如果完全包含 / 集数匹配 / 年份匹配 都符合，则适当用 fallbackThreshold 52 放行
        if ($bestMatch && $bestScore >= $fallbackThreshold) {
            if (!empty($bestMatch['season_match']) || !empty($bestMatch['episode_match']) || !empty($bestMatch['year_match']) || ($bestMatch['actor_hit'] ?? 0) >= 1) {
                return $bestMatch;
            }
        }

        // 最佳努力匹配：基础分 + 基础匹配 不低于硬地板
        if ($bestScore >= $hardFloorThreshold && ($bestMatch['base_score'] ?? 0) >= 50) {
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

    private function findEpisodeUrl($urls, $episodeInfo) {
        $targetEpNum = null;
        if (is_numeric($episodeInfo)) {
            $targetEpNum = intval($episodeInfo);
        } elseif (is_array($episodeInfo) && isset($episodeInfo['episode_num'])) {
            $targetEpNum = $episodeInfo['episode_num'];
        } elseif (is_string($episodeInfo)) {
            $parsed = $this->parseVideoTitle($episodeInfo);
            $targetEpNum = $parsed['episode_num'];
        }

        if ($targetEpNum === null) {
            return null;
        }

        $bestMatch = null;
        $bestDiff = PHP_INT_MAX;

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

        if ($bestMatch && $bestDiff <= 10) {
            return $bestMatch;
        }

        return null;
    }

    private function httpGet($url, $timeout = 30, $retry = 3, $referer = null, $origin = null) {
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
        ];

        // 自动伪造 Origin / Referer
        if ($referer === null || $origin === null) {
            $host = parse_url($url, PHP_URL_HOST);
            if ($host) {
                $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
                if ($referer === null) $referer = $scheme . '://' . $host . '/';
                if ($origin === null) $origin = $scheme . '://' . $host;
            }
        }

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
            @curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,br'); // 支持 br brotli
            @curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);

            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8,en-US;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Cache-Control: no-cache',
                'Pragma: no-cache',
                'sec-ch-ua: "Chromium";v="126", "Not)A;Brand";v="24", "Google Chrome";v="126"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Windows"',
            ];
            if (!empty($referer)) $headers[] = 'Referer: ' . $referer;
            if (!empty($origin)) $headers[] = 'Origin: ' . $origin;
            @curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            @curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            // 支持从 HTTP 3xx 中取得 Location
            @curl_setopt($ch, CURLOPT_AUTOREFERER, true);

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
            // 3xx 有 body 也返回
            if ($httpCode >= 300 && $httpCode < 400 && $response !== false && is_string($response) && strlen($response) > 1000) {
                return $response;
            }

            $lastError = $error ? $error : ('HTTP ' . $httpCode);

            $isRetryable = $error && (
                strpos($error, 'Could not resolve') !== false ||
                strpos($error, 'Connection timed out') !== false ||
                strpos($error, 'Failed to connect') !== false ||
                strpos($error, 'Operation timed out') !== false ||
                strpos($error, 'SSL read') !== false ||
                strpos($error, 'stream reset') !== false
            ) || ($httpCode >= 500 || $httpCode == 429 || $httpCode == 403);

            if ($attempt < $retry && $isRetryable) {
                usleep(500000 + $attempt * 300000);
            }
        }

        $this->lastHttpError = $lastError;
        return false;
    }

    private function httpGetMobile($url, $timeout = 20) {
        // 复用 httpGet 但使用移动 UA + 超时短
        $mobileUserAgents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14; Pixel 8 Pro) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Mobile Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
        ];
        $host = parse_url($url, PHP_URL_HOST);
        $scheme = parse_url($url, PHP_URL_SCHEME) ?: 'https';
        $referer = $host ? $scheme . '://' . $host . '/' : null;
        $origin = $host ? $scheme . '://' . $host : null;

        for ($i = 0; $i < 2; $i++) {
            $ch = @curl_init();
            if (!$ch) return false;

            @curl_setopt($ch, CURLOPT_URL, $url);
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate,br');
            @curl_setopt($ch, CURLOPT_USERAGENT, $mobileUserAgents[array_rand($mobileUserAgents)]);
            @curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            ];
            if ($referer) $headers[] = 'Referer: ' . $referer;
            if ($origin) $headers[] = 'Origin: ' . $origin;
            @curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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
        }
        // 最后兜底：直接走增强版 httpGet
        return $this->httpGet($url, $timeout, 1);
    }

    public function getReplaceUrl($url) {
        $cache = new DbOfficialReplaceCache();
        $cached = $cache->get($url);
        if ($cached) return $cached['m3u8_url'];

        // 多线程抓取页面信息
        $pageInfo = $this->fetchPageInfo($url);
        if (!$pageInfo) return false;

        // 多线程搜索资源站
        $siteManager = new DbResourceSiteManager();
        $sites = $siteManager->getAllSites(false);
        $sites = array_slice($sites, 0, 5);

        $allVideos = [];
        if (!empty($sites) && class_exists('TaskRunner') && TaskRunner::isMultiThreadAvailable()) {
            $tasks = [];
            foreach ($sites as $site) {
                $tasks[] = [
                    'id' => $site['name'],
                    'api_url' => $site['api_url'],
                    'keyword' => $pageInfo['title'],
                    'site_name' => $site['name']
                ];
            }

            $runner = TaskRunner::create([
                'concurrency' => 5,
                'mode' => TaskRunner::MODE_CURL_MULTI,
                'timeout' => 60
            ]);

            $results = $runner->run($tasks, function($task) use ($siteManager) {
                $result = $siteManager->searchVideos($task['api_url'], $task['keyword'], 1, 10);
                if ($result['success'] && !empty($result['videos'])) {
                    foreach ($result['videos'] as &$video) {
                        $video['site'] = $task['site_name'];
                    }
                    unset($video);
                    return $result['videos'];
                }
                return [];
            });

            foreach ($results as $result) {
                if ($result->success && is_array($result->data)) {
                    $allVideos = array_merge($allVideos, $result->data);
                }
            }
        } else {
            $searchResult = $siteManager->searchAllSites($pageInfo['title'], 3, 10);
            if ($searchResult['success']) {
                foreach ($searchResult['results'] as $siteResult) {
                    foreach ($siteResult['videos'] as $video) {
                        $video['site'] = $siteResult['site'];
                        $allVideos[] = $video;
                    }
                }
            }
        }

        if (empty($allVideos)) return false;

        // 匹配最佳结果
        $bestMatch = $this->findBestMatch($pageInfo, $allVideos);
        if (!$bestMatch) return false;

        // 匹配具体集数
        $targetUrl = $bestMatch['video']['first_url'] ?? $bestMatch['video']['url'] ?? '';
        $allUrls = $bestMatch['video']['urls'] ?? [];
        if (!empty($pageInfo['episode_num']) && !empty($allUrls)) {
            $epResult = $this->findEpisodeUrl($allUrls, $pageInfo['episode_num']);
            if ($epResult) {
                $targetUrl = $epResult['url'];
            }
        }

        if (empty($targetUrl)) return false;

        // 去广告处理
        $cleanUrl = $this->removeAds($targetUrl);

        // 缓存结果
        $cache->save(
            $url,
            $pageInfo['platform'] ?? '',
            $pageInfo['fullTitle'] ?? $pageInfo['title'],
            $pageInfo['title'],
            $pageInfo['season_num'] ?? null,
            $pageInfo['episode_num'] ?? null,
            $cleanUrl,
            $bestMatch['score'],
            $bestMatch['site'],
            $bestMatch['video']
        );

        return $cleanUrl;
    }

    private function fetchPageInfo($url) {
        $platform = $this->detectPlatform($url);
        if (!$platform) return null;

        $videoIds = $this->extractVideoId($url, $platform);
        $videoId = $videoIds['video_id'] ?? '';
        $coverId = $videoIds['cover_id'] ?? '';

        $html = null;
        $apiInfo = null;

        // 多线程抓取页面HTML和API信息
        if (class_exists('TaskRunner') && TaskRunner::isMultiThreadAvailable()) {
            $tasks = [];
            $tasks[] = ['id' => 'html', 'url' => $url, 'type' => 'html'];
            if ($videoId) {
                $tasks[] = ['id' => 'api', 'video_id' => $videoId, 'cover_id' => $coverId, 'original_url' => $url, 'platform' => $platform, 'type' => 'api'];
            }

            $runner = TaskRunner::create([
                'concurrency' => count($tasks),
                'mode' => TaskRunner::MODE_CURL_MULTI,
                'timeout' => 30
            ]);

            $results = $runner->run($tasks, function($task) {
                if ($task['type'] === 'html') {
                    return $this->httpGet($task['url']);
                } else {
                    return $this->fetchVideoInfoFromApi($task['video_id'], $task['platform'], $task['cover_id'] ?? '', $task['original_url'] ?? '');
                }
            });

            foreach ($results as $result) {
                if ($result->success) {
                    if ($result->id === 'html') {
                        $html = $result->data;
                    } elseif ($result->id === 'api') {
                        $apiInfo = $result->data;
                    }
                }
            }
        } else {
            $html = $this->httpGet($url);
            if ($videoId) {
                $apiInfo = $this->fetchVideoInfoFromApi($videoId, $platform, $coverId, $url);
            }
        }

        if (empty($html) && empty($apiInfo)) return null;

        $title = '';
        if (!empty($html)) {
            $title = $this->extractTitle($html, $platform);
        }
        if (empty($title) && !empty($apiInfo['title'])) {
            $title = $apiInfo['title'];
        }
        if (empty($title)) return null;

        $episodeInfo = [];
        if (!empty($html)) {
            $episodeInfo = $this->extractEpisodeFromHtml($html, $platform);
        }

        $parsed = $this->parseVideoTitle($title);

        return [
            'url' => $url,
            'platform' => $platform['name'] ?? '',
            'title' => $parsed['base_title'] ?? $title,
            'fullTitle' => $title,
            'season' => $parsed['season'],
            'season_num' => $parsed['season_num'],
            'episode' => $parsed['episode'],
            'episode_num' => $parsed['episode_num'] ?? ($episodeInfo['episode_num'] ?? null),
            'part' => $parsed['part'],
            'version' => $parsed['version'],
            'video_id' => $videoId,
            'cover_id' => $coverId
        ];
    }

    private function removeAds($url) {
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        $skipper = new M3U8AdSkipper();
        $engine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $engine->setDomain($domain);

        // 从数据库加载广告特征码并应用到引擎
        if ($this->db->tableExists('ad_signatures')) {
            if (!class_exists('DbAdSignature')) {
                require_once __DIR__ . '/DbAdSignature.php';
            }
            $adSignature = new DbAdSignature($this->db);
            $sigRules = $adSignature->getRulesForDomain($domain);
            if (!empty($sigRules)) {
                $reflection = new ReflectionClass($engine);
                $applyMethod = $reflection->getMethod('applyDomainRules');
                $applyMethod->setAccessible(true);
                $applyMethod->invoke($engine, $sigRules);
            }
        }

        // 注入引擎到skipper
        $reflection = new ReflectionClass($skipper);
        $ruleEngineProp = $reflection->getProperty('ruleEngine');
        $ruleEngineProp->setAccessible(true);
        $ruleEngineProp->setValue($skipper, $engine);

        $filterProp = $reflection->getProperty('filter');
        $filterProp->setAccessible(true);
        $filter = $filterProp->getValue($skipper);

        $filterReflection = new ReflectionClass($filter);
        $filterEngineProp = $filterReflection->getProperty('ruleEngine');
        $filterEngineProp->setAccessible(true);
        $filterEngineProp->setValue($filter, $engine);

        // 执行去广告处理（验证M3U8可解析）
        $skipper->processWithSafeguard($url);

        // 返回原始URL，实际去广告在播放时通过mxjx接口完成
        return $url;
    }
}
