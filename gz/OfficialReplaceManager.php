<?php
/**
 * 官替 API 管理器
 * 负责将官方视频平台链接（腾讯、爱奇艺、优酷等）替换为资源站 M3U8 地址
 */

require_once __DIR__ . '/ResourceSiteManager.php';
require_once __DIR__ . '/TitleNormalizer.php';

class OfficialReplaceManager {
    private $configFile;
    private $config;
    private $lastHttpError = '';

    public function __construct() {
        $this->configFile = __DIR__ . '/official_replace_config.php';
        $this->loadConfig();
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
            'default_site' => '量子',
            'max_search_sites' => 5,
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
                    'pattern' => '/youku\.com\/.*?id_([a-zA-Z0-9]+)/i',
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
            'search_sites' => ['量子', '最大', '猫眼', '红牛'],
            'match_threshold' => 60
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
        try {
            if (empty($url)) {
                return ['success' => false, 'message' => 'URL不能为空'];
            }

            if (!$this->config['enabled']) {
                return ['success' => false, 'message' => '官替功能已禁用'];
            }

            $platform = $this->detectPlatform($url);
            if (!$platform) {
                return ['success' => false, 'message' => '不支持的视频平台'];
            }

            $videoId = $this->extractVideoId($url, $platform);

            $videoInfo = $this->fetchVideoInfo($url, $platform);
            $videoTitle = '';

            if ($videoInfo && !empty($videoInfo['is_expired'])) {
                return ['success' => false, 'message' => '链接已失效', 'platform' => $platform['name']];
            }

            if ($videoInfo && !empty($videoInfo['title'])) {
                $videoTitle = $videoInfo['title'];
            } elseif ($videoId) {
                $videoTitle = $videoId;
            } else {
                return ['success' => false, 'message' => '无法获取视频信息', 'platform' => $platform['name']];
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
                    'search_keywords' => $searchKeywords
                ];
            }

            $bestMatch = $this->findBestMatch($videoInfo, $searchResult['videos']);
            $allMatches = $this->findAllMatches($videoInfo, $searchResult['videos']);

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
                    'candidates' => array_slice($allMatches, 0, 5)
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
                'used_keyword' => $usedKeyword,
                'search_keywords' => $searchKeywords,
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

    private function buildAdSkipUrl($m3u8Url) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $basePath = dirname($requestUri);
        $basePath = $basePath === '/' ? '' : $basePath;
        $selfUrl = $scheme . '://' . $host . $basePath;
        return $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
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
        $originalTitle = $videoInfo['title'] ?? '';

        if (!empty($baseTitle)) {
            $normalizedBase = TitleNormalizer::normalize($baseTitle);
            $keywords[] = $baseTitle;
            $keywords[] = $normalizedBase;

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

            $keywords[] = $normalizedBase;
        }

        if (!empty($originalTitle) && $originalTitle !== $baseTitle) {
            $keywords[] = $originalTitle;
            $normalizedOrig = TitleNormalizer::normalize($originalTitle);
            if ($normalizedOrig !== $baseTitle) {
                $keywords[] = $normalizedOrig;
            }
        }

        $videoId = $videoInfo['video_id'] ?? '';
        if (!empty($videoId)) {
            $keywords[] = $videoId;
        }

        $keywords = array_values(array_unique(array_filter($keywords, function($kw) {
            return !empty($kw) && mb_strlen($kw) >= 2;
        })));

        if (count($keywords) > 8) {
            $keywords = array_slice($keywords, 0, 8);
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

        foreach ($videos as $video) {
            $videoName = $video['name'] ?? '';
            $videoRemarks = $video['remarks'] ?? '';
            $fullName = $videoName . ' ' . $videoRemarks;

            $videoParsed = $this->parseVideoTitle($fullName);
            $videoBaseTitle = $videoParsed['base_title'];
            $videoSeason = $videoParsed['season_num'];
            $videoPart = $videoParsed['part'];
            $videoVersion = $videoParsed['version'];

            $keyword = $videoInfo['base_title'] ?? $videoInfo['title'];
            $targetSeason = $videoInfo['season_num'] ?? null;
            $targetPart = $videoInfo['part'] ?? null;
            $targetVersion = $videoInfo['version'] ?? null;

            $baseScore = $this->calculateBaseMatchScore($keyword, $videoBaseTitle);
            $score = $baseScore;
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

            $score = min(100, max(0, $score));

            if ($score >= $threshold * 0.8) {
                $matches[] = [
                    'video' => $video,
                    'score' => round($score, 2),
                    'base_score' => round($baseScore, 2),
                    'season_match' => $seasonMatch,
                    'site' => $video['site'] ?? ''
                ];
            }
        }

        usort($matches, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return $matches;
    }

    private function extractVideoId($url, $platform) {
        $videoId = null;
        $domain = $platform['domain'] ?? '';

        if ($domain === 'v.qq.com') {
            if (preg_match('/\/([a-zA-Z0-9]{8,16})\.html?$/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/vid=([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/cover\/([a-zA-Z0-9]+)\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[2];
            } elseif (preg_match('/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
                $videoId = $matches[1];
            } elseif (preg_match('/x\/([a-zA-Z0-9]+)/i', $url, $matches)) {
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

        return $videoId;
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

    private function fetchVideoInfo($url, $platform) {
        $videoId = $this->extractVideoId($url, $platform);
        $title = null;
        $cover = null;
        $episodeInfo = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null
        ];
        $isExpired = false;

        $apiInfo = $this->fetchVideoInfoFromApi($videoId, $platform);
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
        }

        if (empty($title) || mb_strlen($title) < 3) {
            $urlTitle = $this->extractTitleFromUrl($url, $platform);
            if (!empty($urlTitle) && mb_strlen($urlTitle) >= 3) {
                $title = $urlTitle;
            }
        }

        if ($title && $this->isExpiredVideoTitle($title)) {
            $isExpired = true;
        }

        return [
            'title' => $title,
            'cover' => $cover,
            'url' => $url,
            'platform' => $platform['name'],
            'episode_info' => $episodeInfo,
            'video_id' => $videoId,
            'is_expired' => $isExpired
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

    private function fetchVideoInfoFromApi($videoId, $platform) {
        if (empty($videoId)) {
            return null;
        }

        $platformName = $platform['name'] ?? '';
        $result = ['title' => null, 'cover' => null];

        if ($platformName === '腾讯视频') {
            $apiUrls = [
                'https://pbaccess.video.qq.com/trpc.vidplay.vidplay_2_0_fcgi.VidPlay2_0Fcgi/GetCmsVidInfoAll?data={"vid":"' . urlencode($videoId) . '","appVer":"3.5.57","platform":"40000"}',
                'https://access.video.qq.com/cgi-bin/varietycheck?vid=' . urlencode($videoId),
                'http://vv.video.qq.com/getinfo?vids=' . urlencode($videoId) . '&platform=101001&charge=0&otype=json',
            ];

            foreach ($apiUrls as $apiUrl) {
                $response = $this->httpGet($apiUrl);
                if (!$response) continue;

                $jsonStr = $response;
                $jsonStr = preg_replace('/^\w+=/', '', $jsonStr);
                $jsonStr = preg_replace('/;$/', '', $jsonStr);
                $data = json_decode($jsonStr, true);

                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }

                if (!empty($result['title'])) break;
            }

            if (empty($result['title'])) {
                $apiUrl2 = 'https://node.video.qq.com/x/api/float_vinfo2?vid=' . urlencode($videoId);
                $response2 = $this->httpGet($apiUrl2);
                if ($response2) {
                    $data2 = json_decode($response2, true);
                    if ($data2 && isset($data2['c']['title'])) {
                        $result['title'] = $data2['c']['title'];
                        if (!empty($data2['c']['pic'])) {
                            $result['cover'] = $data2['c']['pic'];
                        }
                    }
                }
            }
        }

        if ($platformName === '爱奇艺') {
            $apiUrls = [
                'https://pcw-api.iqiyi.com/video/video/baseinfo/' . urlencode($videoId),
                'https://cache.video.iqiyi.com/jp/avlist/20210316/' . urlencode($videoId) . '.json',
            ];
            foreach ($apiUrls as $apiUrl) {
                $response = $this->httpGet($apiUrl);
                if (!$response) continue;
                $data = json_decode($response, true);
                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }
                if (!empty($result['title'])) break;
            }
        }

        if ($platformName === '芒果TV') {
            $apiUrls = [
                'https://pcweb.api.mgtv.com/episode/list?video_id=' . urlencode($videoId),
                'https://pcweb.api.mgtv.com/video/info?video_id=' . urlencode($videoId),
            ];
            foreach ($apiUrls as $apiUrl) {
                $response = $this->httpGet($apiUrl);
                if (!$response) continue;
                $data = json_decode($response, true);
                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }
                if (!empty($result['title'])) break;
            }
        }

        if ($platformName === '优酷') {
            $apiUrls = [
                'https://v.youku.com/service/getVideoInfo?vid=' . urlencode($videoId),
                'https://openapi.youku.com/v2/videos/show.json?client_id=23e50e2e09490776&video_id=' . urlencode($videoId),
            ];
            foreach ($apiUrls as $apiUrl) {
                $response = $this->httpGet($apiUrl);
                if (!$response) continue;
                $data = json_decode($response, true);
                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }
                if (!empty($result['title'])) break;
            }
        }

        if ($platformName === '哔哩哔哩') {
            $apiUrls = [
                'https://api.bilibili.com/x/web-interface/view?bvid=' . urlencode($videoId),
                'https://api.bilibili.com/x/web-interface/view?aid=' . str_replace('av', '', urlencode($videoId)),
            ];
            foreach ($apiUrls as $apiUrl) {
                $response = $this->httpGet($apiUrl);
                if (!$response) continue;
                $data = json_decode($response, true);
                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }
                if (!empty($result['title'])) break;
            }
        }

        if ($platformName === '搜狐视频') {
            $apiUrl = 'https://sohu.com/api/getVideoInfo?vid=' . urlencode($videoId);
            $response = $this->httpGet($apiUrl);
            if ($response) {
                $data = json_decode($response, true);
                if ($data) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }
            }
        }

        if (!empty($result['title'])) {
            return $result;
        }
        return null;
    }

    private function findTitleInData($data) {
        $result = ['title' => null, 'cover' => null];
        if (!is_array($data)) return $result;

        $titleKeys = ['title', 'name', 'ti', 'videoName', 'video_title', 'vidName', 'subTitle', 'mainTitle'];
        $coverKeys = ['cover', 'pic', 'image', 'imageUrl', 'poster', 'thumb', 'thumbnail', 'vpic'];

        array_walk_recursive($data, function($value, $key) use (&$result, $titleKeys, $coverKeys) {
            if (in_array($key, $titleKeys) && is_string($value) && mb_strlen($value) >= 2 && mb_strlen($value) <= 100) {
                if (empty($result['title']) && preg_match('/[\x{4e00}-\x{9fa5}a-zA-Z]/u', $value)) {
                    $result['title'] = $value;
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
        $title = preg_replace('/[-_|【】\[\]（）()].*?$/u', '', $title);
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

    private function parseVideoTitle($title) {
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

        if ($result['season_num'] === null) {
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
                        $result['season'] = $matches[2];
                        $result['season_num'] = $numPart;
                    }
                }
            }
        }

        $episodePatterns = [
            '/第\s*(\d+)\s*集/u' => 'num',
            '/第\s*([一二三四五六七八九十百千\d]+)\s*集/u' => 'cn',
            '/EP\s*(\d+)/i' => 'num',
            '/E\s*(\d+)/i' => 'num',
            '/(\d+)集/u' => 'num_suffix',
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
                }
                break;
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
        $allVideos = [];
        $searchedSites = 0;

        if (empty($sites)) {
            $activeSites = $siteMgr->getAllSites(false);
            $maxSites = $this->config['max_search_sites'] ?? count($activeSites);
            $activeSites = array_slice($activeSites, 0, $maxSites);
            $allVideos = $this->searchSitesConcurrent($activeSites, $keyword);
            $searchedSites = count($activeSites);
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
            $allVideos = $this->searchSitesConcurrent($targetSites, $keyword);
            $searchedSites = count($targetSites);
        }

        return [
            'success' => !empty($allVideos),
            'videos' => $allVideos,
            'searched_sites' => $searchedSites
        ];
    }

    private function searchSitesConcurrent($sites, $keyword) {
        if (empty($sites)) return [];

        $allVideos = [];
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
                            return ['site' => $siteName, 'videos' => [], 'success' => false];
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

                        return ['site' => $siteName, 'videos' => [], 'success' => false];
                    });

                    foreach ($results as $result) {
                        if ($result->isSuccess()) {
                            $data = $result->getData();
                            if ($data['success'] && !empty($data['videos'])) {
                                $allVideos = array_merge($allVideos, $data['videos']);
                            }
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
                try {
                    $result = $siteMgr->searchVideos($apiUrl, $keyword, 1, 10);
                    if ($result && $result['success'] && !empty($result['videos'])) {
                        foreach ($result['videos'] as $v) {
                            $v['site'] = $site['name'];
                            $allVideos[] = $v;
                        }
                    }
                } catch (Throwable $e) {
                }
            }
        }

        return $allVideos;
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
            $videoPart = $videoParsed['part'];
            $videoVersion = $videoParsed['version'];

            $baseScore = $this->calculateBaseMatchScore($keyword, $videoBaseTitle);
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
                    'site' => $video['site'] ?? ''
                ];
            }
        }

        if ($bestScore >= $threshold) {
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

            if ($ratio >= 0.7) {
                return 100;
            } elseif ($ratio >= 0.5) {
                if ($startsWith) {
                    if ($seasonSuffix) return 95;
                    if ($spinOffSuffix) return 55;
                    return 90;
                }
                return $endsWith ? 85 : 70;
            } elseif ($ratio >= 0.35) {
                if ($startsWith) {
                    if ($seasonSuffix) return 92;
                    if ($spinOffSuffix) return 45;
                    return 75;
                } elseif ($endsWith) {
                    return 70;
                } else {
                    return 45;
                }
            } else {
                if ($startsWith) {
                    if ($seasonSuffix) return 85;
                    if ($spinOffSuffix) return 35;
                    return 55;
                } elseif ($endsWith) {
                    return 45;
                } else {
                    return 25;
                }
            }
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

        $finalScore = $charSimilarity * 0.5 + $prefixBonus + $suffixBonus;

        return round(min(100, max(0, $finalScore)), 2);
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

    private function httpGet($url, $timeout = 30, $retry = 3) {
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        $proxyMgr = null;
        $proxyFile = __DIR__ . '/../proxy/ProxyManager.php';
        if (file_exists($proxyFile)) {
            require_once $proxyFile;
            $proxyMgr = new ProxyManager();
        }

        for ($attempt = 0; $attempt <= $retry; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br'
            ]);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookie'));
            curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookie'));

            $currentProxy = null;
            if ($proxyMgr && $proxyMgr->isEnabled() && $attempt > 0) {
                $currentProxy = $proxyMgr->applyProxyToCurl($ch);
            }

            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            curl_close($ch);

            if ($currentProxy) {
                if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                    $proxyMgr->markProxySuccess($currentProxy['id'], $responseTime);
                    return $response;
                } else {
                    $proxyMgr->markProxyFailed($currentProxy['id']);
                }
            }

            if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
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
