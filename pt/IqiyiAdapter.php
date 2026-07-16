<?php
/**
 * 爱奇艺平台适配器
 *
 * 负责爱奇艺（iqiyi.com）视频的：
 *  - URL 识别与视频 ID 提取（支持 /v_xxx.html 与 ?vfrm=xxx 等格式）
 *  - 视频信息抓取（官方 API + HTML 回退）
 *  - 搜索关键词构建
 *  - 资源站候选项匹配评分（爱奇艺特定算法）
 *  - 去广告规则
 *  - 标题清理
 *
 * 依赖：AbstractPlatformAdapter
 * 兼容：PHP 7.4+
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

class IqiyiAdapter extends AbstractPlatformAdapter
{
    /**
     * 平台标识
     * @var string
     */
    protected $platformId = 'iqiyi';

    /**
     * 平台名称
     * @var string
     */
    protected $platformName = '爱奇艺';

    /**
     * 获取平台标识
     * @return string
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * 获取平台名称
     * @return string
     */
    public function getPlatformName()
    {
        return $this->platformName;
    }

    /**
     * 检测 URL 是否属于爱奇艺
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (!is_string($url) || $url === '') {
            return false;
        }
        return stripos($url, 'iqiyi.com') !== false;
    }

    /**
     * 从 URL 提取视频 ID
     * 支持 /v_xxx.html、?vfrm=xxx 等格式
     *
     * @param string $url
     * @return array ['video_id' => string|null, 'cover_id' => string|null]
     */
    public function extractVideoId($url)
    {
        $videoId = null;
        $coverId = null;

        if (!is_string($url) || $url === '') {
            return [
                'video_id' => null,
                'cover_id' => null,
            ];
        }

        // /v_xxx.html 标准视频页
        if (preg_match('/\/v_([a-zA-Z0-9_]+)\.html?/i', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]vfrm=([a-zA-Z0-9_]+)/i', $url, $matches)) {
            // ?vfrm=xxx 格式
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]vid=([a-zA-Z0-9_]+)/i', $url, $matches)) {
            // ?vid=xxx 格式
            $videoId = $matches[1];
        } elseif (preg_match('/\/([a-zA-Z0-9]{10,28})\.html?$/i', $url, $matches)) {
            // 纯 ID 形式的 .html - 放宽长度范围
            $videoId = $matches[1];
        } elseif (preg_match('/\/a_([a-zA-Z0-9]+)/i', $url, $matches)) {
            // 专辑/合集 ID，同时作为 cover_id
            $coverId = $matches[1];
            if (!$videoId) {
                $videoId = $matches[1];
            }
        } elseif (preg_match('/\/play\/([a-zA-Z0-9]+)/i', $url, $matches)) {
            // /play/{vid}
            $videoId = $matches[1];
        } elseif (preg_match('/\/albuminfo\/([a-zA-Z0-9]+)/i', $url, $matches)) {
            // 专辑信息页
            $coverId = $matches[1];
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]albumId=([a-zA-Z0-9]+)/i', $url, $matches)) {
            // albumId 参数
            $coverId = $matches[1];
            if (!$videoId) {
                $videoId = $matches[1];
            }
        } elseif (preg_match('/[?&]tvId=([a-zA-Z0-9]+)/i', $url, $matches)) {
            // tvId 参数
            $coverId = $matches[1];
            if (!$videoId) {
                $videoId = $matches[1];
            }
        }

        return [
            'video_id' => $videoId,
            'cover_id' => $coverId,
        ];
    }

    /**
     * 获取视频信息（标题、封面、剧集信息）
     *
     * @param string $url
     * @param array $videoIds extractVideoId() 的返回值
     * @return array ['title' => '', 'cover' => '', 'episode_info' => []]
     */
    public function fetchVideoInfo($url, $videoIds)
    {
        $result = [
            'title' => null,
            'cover' => null,
            'episode_info' => [
                'episode_num' => null,
                'episode_name' => '',
                'total_episodes' => null,
            ],
        ];

        $videoId = is_array($videoIds) ? ($videoIds['video_id'] ?? '') : '';
        if (empty($videoId)) {
            return $result;
        }

        $apiUrls = [
            'https://pcw-api.iqiyi.com/video/video/baseinfo/' . urlencode($videoId),
            'https://pcw-api.iqiyi.com/strategy/pcw/data/baseVideoInfo?ids=' . urlencode($videoId),
            'https://www.iqiyi.com/v_' . urlencode($videoId) . '.html',
        ];

        $titlePaths = [
            ['data', 'name'], ['data', 'title'], ['data', 0, 'name'],
            ['name'], ['title'], ['videoName'], ['data', 0, 'title'],
            ['data', 'videoInfo', 'name'], ['data', 'videoInfo', 'title'],
            ['data', 'albumInfo', 'name'], ['data', 'albumInfo', 'title'],
        ];

        $coverPaths = [
            ['data', 'imageUrl'], ['data', 0, 'imageUrl'],
            ['data', 'image'], ['data', 0, 'image'],
            ['imageUrl'], ['image'], ['data', 'videoInfo', 'imageUrl'],
            ['data', 'albumInfo', 'imageUrl'], ['data', 'albumInfo', 'cover'],
        ];

        foreach ($apiUrls as $apiUrl) {
            try {
                $response = $this->httpGet($apiUrl);
                if (!$response) {
                    continue;
                }

                // HTML 页面处理
                $isHtml = stripos($apiUrl, '.html') !== false;
                if ($isHtml) {
                    $htmlTitle = $this->extractTitleFromHtml($response);
                    if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3 && empty($result['title'])) {
                        $result['title'] = $htmlTitle;
                    }
                    $htmlCover = $this->extractCoverFromHtml($response);
                    if (!empty($htmlCover) && empty($result['cover'])) {
                        $result['cover'] = $htmlCover;
                    }
                    if (!empty($result['title'])) {
                        break;
                    }
                    continue;
                }

                // JSON API 处理
                $data = $this->safeJsonDecode($response);
                if (!$data) {
                    continue;
                }

                // 提取标题
                if (empty($result['title'])) {
                    foreach ($titlePaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) {
                                $val = null;
                                break;
                            }
                            $val = $val[$key];
                        }
                        if (is_string($val) && mb_strlen($val) >= 3) {
                            $result['title'] = $val;
                            break;
                        }
                    }
                }

                // 提取封面
                if (empty($result['cover'])) {
                    foreach ($coverPaths as $path) {
                        $val = $data;
                        foreach ($path as $key) {
                            if (!is_array($val) || !isset($val[$key])) {
                                $val = null;
                                break;
                            }
                            $val = $val[$key];
                        }
                        if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val)) {
                            $result['cover'] = $val;
                            break;
                        }
                    }
                }

                // 解析剧集信息
                $episodeInfo = $this->parseEpisodeInfo($data);
                if (!empty($episodeInfo)) {
                    $result['episode_info'] = $episodeInfo;
                }

                if (!empty($result['title'])) {
                    break;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        // 全部 API 失败时回退到原始 URL 的 HTML
        if (empty($result['title']) && !empty($url)) {
            $html = $this->httpGet($url);
            if ($html) {
                $htmlTitle = $this->extractTitleFromHtml($html);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3) {
                    $result['title'] = $htmlTitle;
                }
                if (empty($result['cover'])) {
                    $htmlCover = $this->extractCoverFromHtml($html);
                    if (!empty($htmlCover)) {
                        $result['cover'] = $htmlCover;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 从 API 数据中解析剧集信息
     * @param mixed $data
     * @return array|null
     */
    protected function parseEpisodeInfo($data)
    {
        if (!is_array($data)) {
            return null;
        }

        $info = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null,
        ];

        $sources = [];
        $candidates = [
            isset($data['data']) ? $data['data'] : null,
            isset($data['data']['videoInfo']) ? $data['data']['videoInfo'] : null,
            isset($data['data']['albumInfo']) ? $data['data']['albumInfo'] : null,
            isset($data['data']['episode_info']) ? $data['data']['episode_info'] : null,
            isset($data['data']['ep']) ? $data['data']['ep'] : null,
            isset($data['ep']) ? $data['ep'] : null,
            isset($data['episode']) ? $data['episode'] : null,
        ];
        foreach ($candidates as $c) {
            if (is_array($c)) {
                $sources[] = $c;
            }
        }

        foreach ($sources as $src) {
            if ($info['total_episodes'] === null) {
                foreach (['total', 'episode_count', 'video_count', 'count', 'total_episodes', 'totalCount', 'total_count', 'maxPage', 'max_page'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['total_episodes'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_num'] === null) {
                foreach (['order', 'episode', 'index', 'ep', 'epOrder', 'episode_order', 'episodeIndex', 'episode_index', 'page'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['episode_num'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_name'] === '') {
                foreach (['episode_name', 'name', 'title', 'short_title', 'subTitle', 'sub_title', 'epName', 'ep_name', 'showName', 'show_name'] as $k) {
                    if (isset($src[$k]) && is_string($src[$k]) && $src[$k] !== '') {
                        $info['episode_name'] = (string)$src[$k];
                        break;
                    }
                }
            }
        }

        // 从 name/title 中解析集数（如 "第5集 标题" 或 "EP05 标题"）
        if ($info['episode_num'] === null) {
            foreach ($sources as $src) {
                $text = '';
                if (!empty($src['name'])) $text = $src['name'];
                elseif (!empty($src['title'])) $text = $src['title'];
                if ($text) {
                    if (preg_match('/第\s*(\d+)\s*集/u', $text, $em)) {
                        $info['episode_num'] = (int)$em[1];
                        break;
                    }
                    if (preg_match('/[Ee][Pp]?\s*(\d{1,3})/', $text, $em)) {
                        $info['episode_num'] = (int)$em[1];
                        break;
                    }
                }
            }
        }

        return $info;
    }

    /**
     * 构建搜索关键词
     * @param array $videoInfo
     * @return array
     */
    public function buildSearchKeywords($videoInfo)
    {
        $keywords = [];
        $title = $videoInfo['title'] ?? '';
        $baseTitle = $videoInfo['base_title'] ?? '';
        if (empty($baseTitle)) {
            $baseTitle = $title;
        }
        $seasonNum = $videoInfo['season_num'] ?? null;
        $version = $videoInfo['version'] ?? '';

        if (!empty($baseTitle)) {
            $keywords[] = $baseTitle;

            // 去标点版本
            $noPunct = preg_replace('/[:：,，\s]+/u', '', $baseTitle);
            if ($noPunct && $noPunct !== $baseTitle) {
                $keywords[] = $noPunct;
            }

            // 主标题（冒号、破折号前）
            if (preg_match('/^(.+?)[:：\-—]/u', $baseTitle, $m)) {
                $main = trim($m[1]);
                if (mb_strlen($main) >= 2) {
                    $keywords[] = $main;
                }
            }

            // 季度关键词
            if ($seasonNum) {
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '季';
                $keywords[] = $baseTitle . '第' . $seasonNum . '季';
                $keywords[] = $baseTitle . ' S' . $seasonNum;
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '部';
            }

            // 版本关键词
            if (!empty($version)) {
                $keywords[] = $baseTitle . ' ' . $version;
            }
        }

        // 原始标题
        if (!empty($title) && $title !== $baseTitle) {
            $keywords[] = $title;
        }

        // 视频ID
        $videoId = $videoInfo['video_id'] ?? '';
        if (!empty($videoId)) {
            $keywords[] = $videoId;
        }

        $keywords = array_values(array_unique(array_filter($keywords, function ($kw) {
            return !empty($kw) && is_string($kw) && mb_strlen($kw) >= 2;
        })));

        if (count($keywords) > 10) {
            $keywords = array_slice($keywords, 0, 10);
        }

        return $keywords;
    }

    /**
     * 计算匹配分数（爱奇艺特定算法）
     * @param array $videoInfo 官方视频信息
     * @param array $candidate 资源站候选项
     * @return float 0-100
     */
    public function calculateMatchScore($videoInfo, $candidate)
    {
        $videoTitle = $videoInfo['title'] ?? ($videoInfo['base_title'] ?? '');
        $candidateTitle = $candidate['name'] ?? ($candidate['title'] ?? '');
        $candidateRemarks = $candidate['remarks'] ?? '';

        $score = $this->calculateBaseScore($videoTitle, $candidateTitle);

        // 季度匹配
        $targetSeason = $videoInfo['season_num'] ?? null;
        $candidateSeason = $candidate['season_num'] ?? $candidate['parsed_season_num'] ?? null;
        if ($targetSeason !== null && $candidateSeason !== null) {
            if ($targetSeason == $candidateSeason) {
                $score += 25;
            } else {
                $score -= min(25, 15 + abs($targetSeason - $candidateSeason) * 5);
            }
        } elseif ($targetSeason !== null && $candidateSeason === null) {
            if ($targetSeason == 1) {
                $score += 8;
            } else {
                $score -= 5;
            }
        }

        // 集数匹配
        $targetEpisode = $videoInfo['episode_num'] ?? null;
        $candidateEpisode = $candidate['episode_num'] ?? $candidate['parsed_episode_num'] ?? null;
        if ($targetEpisode !== null && $candidateEpisode !== null) {
            if ($targetEpisode == $candidateEpisode) {
                $score += 20;
            }
        }

        // 爱奇艺综艺"期"匹配加成
        $episodeName = $videoInfo['episode_name'] ?? '';
        if (!empty($episodeName) && preg_match('/第(\d+)期/u', $episodeName, $em)) {
            $targetIssue = (int)$em[1];
            if (!empty($candidateTitle) && preg_match('/第(\d+)期/u', $candidateTitle, $cm)) {
                if ((int)$cm[1] == $targetIssue) {
                    $score += 15;
                } else {
                    $score -= 8;
                }
            }
        }

        // 备注信息加成（正片/更新状态等）
        if (!empty($candidateRemarks)) {
            if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片/u', $candidateRemarks)) {
                $score += 5;
            }
            // 爱奇艺资源站常见"会员"标记轻微加成
            if (preg_match('/vip|会员/i', $candidateRemarks)) {
                $score += 2;
            }
        }

        return (float)min(100, max(0, $score));
    }

    /**
     * 获取爱奇艺去广告规则
     * @return array
     */
    public function getAdRules()
    {
        return [
            'platform' => $this->platformId,
            'description' => '爱奇艺广告过滤规则',
            // 广告相关的 CDN/请求域名
            'ad_domains' => [
                'pub.iqiyi.com',
                'afp.iqiyi.com',
                'cit.iqiyi.com',
                'msg.qy.net',
                'adselect.iqiyi.com',
                'gap.iqiyi.com',
                'eakorea.iqiyi.com',
            ],
            // 广告片段 URI 正则模式
            'ad_url_patterns' => [
                '/\/(ads?|advert|commercial|preroll|midroll|postroll)[\/._-]/i',
                '/\/(pf|promote|promo)[\/._-]/i',
                '/\b(ads?|advert|commercial)\b/i',
                '/ad_\d+\./i',
                '/^https?:\/\/[^\/]*\.(pub|afp|cit|adselect)\.iqiyi\.com\//i',
            ],
            // 广告关键词（标题/片段名匹配）
            'ad_keywords' => [
                'ad', 'ads', 'advert', 'advertisement',
                'preroll', 'midroll', 'postroll',
                'commercial', 'promo', 'sponsor',
                '广告', '插播', '贴片', '片头', '片尾', '暂停广告',
            ],
            // 短片段阈值（秒）：短于此值多为广告
            'min_segment_duration' => 2,
            // 长片段阈值（秒）
            'max_segment_duration' => 30,
            // 时长容差
            'duration_tolerance' => 0.5,
            // 启用的检测项
            'checks' => [
                'short_segments' => true,
                'long_segments' => false,
                'keywords' => true,
                'url_patterns' => true,
                'discontinuity' => true,
                'repetitive_duration' => true,
            ],
        ];
    }

    /**
     * 清理标题（爱奇艺特定后缀）
     * @param string $title
     * @return string|null
     */
    public function cleanTitle($title)
    {
        $title = is_string($title) ? trim($title) : '';
        if ($title === '') {
            return null;
        }

        // 先调用父类通用清理
        $cleaned = parent::cleanTitle($title);
        if ($cleaned === null) {
            return null;
        }

        // 爱奇艺特定后缀清理
        $cleaned = preg_replace('/[-_\s]?\[?爱奇艺\]?/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?iqiyi/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?VIP\s*专享/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?会员专享/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?独播/iu', '', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned, " \t\n\r\0\x0B-_—|·");

        if (mb_strlen($cleaned) < 2) {
            return null;
        }

        return $cleaned;
    }
}
