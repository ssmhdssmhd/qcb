<?php
/**
 * 优酷平台适配器
 *
 * 负责优酷（youku.com）视频的：
 *  - URL 识别与视频 ID 提取（id_ 后可含 = 字符，如 Base64 风格 ID）
 *  - 视频信息抓取（官方 API + HTML 回退）
 *  - 搜索关键词构建
 *  - 资源站候选项匹配评分（优酷特定算法）
 *  - 去广告规则
 *  - 标题清理
 *
 * 依赖：AbstractPlatformAdapter
 * 兼容：PHP 7.4+
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

class YoukuAdapter extends AbstractPlatformAdapter
{
    /**
     * 平台标识
     * @var string
     */
    protected $platformId = 'youku';

    /**
     * 平台名称
     * @var string
     */
    protected $platformName = '优酷';

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
     * 检测 URL 是否属于优酷
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (!is_string($url) || $url === '') {
            return false;
        }
        return stripos($url, 'youku.com') !== false;
    }

    /**
     * 从 URL 提取视频 ID
     * 优酷 ID 形如 id_XXXXXX==.html（可能包含 = 字符），需正确匹配
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

        // 主匹配规则：youku.com/...id_XXX（必须支持 = 字符，优酷 ID 可能为 Base64 风格含 = 填充）
        if (preg_match('/youku\.com\/.*?id_([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]vid=([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // ?vid=xxx 格式
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]video_id=([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // ?video_id=xxx 格式
            $videoId = $matches[1];
        } elseif (preg_match('/\/v_show\/id_([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // 兼容 v_show 路径（已被主规则覆盖，此处保留作为显式分支）
            $videoId = $matches[1];
        } elseif (preg_match('/\/v_play\/id_([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // v_play 路径
            $videoId = $matches[1];
        } elseif (preg_match('/\/play\/show\/id_([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // play/show 路径
            $videoId = $matches[1];
        } elseif (preg_match('/[?&]showid=([a-zA-Z0-9=+\/_\\-]+)/i', $url, $matches)) {
            // showid 参数（专辑/封面 ID）
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

        // 注意：优酷视频 ID 可能含 = 字符（Base64 填充，如 XNTk1MjU3NzQ4NA==）
        // 路径中的 = 不需要编码，只有查询参数值才需要 urlencode
        $rawVid = $videoId;
        $encodedVid = urlencode($videoId);

        // API 列表（查询参数用 urlencode，路径用原始值）
        $apiUrls = [
            // 优酷官方页面（路径用原始 ID，= 不编码）
            'https://v.youku.com/v_show/id_' . $rawVid . '.html',
            'https://m.youku.com/v_show/id_' . $rawVid . '.html',
            // 优酷内部 API（查询参数用编码值）
            'https://v.youku.com/q_ajax/_getVideoInfo?__rt=1&__ro=&vid=' . $encodedVid,
        ];

        foreach ($apiUrls as $apiUrl) {
            try {
                // 优酷页面需要带 Referer 才能正常返回
                $headers = [];
                if (stripos($apiUrl, 'youku.com') !== false) {
                    $headers[] = 'Referer: https://www.youku.com/';
                }
                $response = $this->httpGet($apiUrl, $headers);
                if (!$response) {
                    continue;
                }

                // HTML 页面处理
                $isHtml = stripos($apiUrl, '.html') !== false;
                if ($isHtml) {
                    // 优先从页面内嵌 JSON 数据提取
                    $pageData = $this->extractPageData($response);
                    if (!empty($pageData)) {
                        if (empty($result['title'])) {
                            $title = $this->findValueByKeys($pageData, ['title', 'name', 'showname', 'show_name', 'videoName']);
                            if (is_string($title) && mb_strlen($title) >= 2) {
                                $result['title'] = $title;
                            }
                        }
                        if (empty($result['cover'])) {
                            $cover = $this->findValueByKeys($pageData, ['bigPhoto', 'photo', 'image', 'cover', 'thumb', 'pic', 'poster']);
                            if (is_string($cover) && preg_match('/^https?:\/\//i', $cover)) {
                                $result['cover'] = $cover;
                            }
                        }
                        $episodeInfo = $this->parseEpisodeInfo($pageData);
                        if (!empty($episodeInfo['episode_num']) || !empty($episodeInfo['total_episodes'])) {
                            $result['episode_info'] = $episodeInfo;
                        }
                    }

                    // 回退到 meta 标签提取
                    if (empty($result['title'])) {
                        $htmlTitle = $this->extractTitleFromHtml($response);
                        if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 2) {
                            $result['title'] = $htmlTitle;
                        }
                    }
                    if (empty($result['cover'])) {
                        $htmlCover = $this->extractCoverFromHtml($response);
                        if (!empty($htmlCover)) {
                            $result['cover'] = $htmlCover;
                        }
                    }

                    // 从 HTML 中提取集数信息
                    if (empty($result['episode_info']['episode_num'])) {
                        $htmlEpInfo = $this->extractEpisodeFromHtml($response);
                        if (!empty($htmlEpInfo['episode_num'])) {
                            $result['episode_info']['episode_num'] = $htmlEpInfo['episode_num'];
                        }
                        if (!empty($htmlEpInfo['total_episodes'])) {
                            $result['episode_info']['total_episodes'] = $htmlEpInfo['total_episodes'];
                        }
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

                // 递归搜索标题和封面
                if (empty($result['title'])) {
                    $title = $this->findValueByKeys($data, ['title', 'name', 'showname', 'show_name', 'videoName', 'titleCN', 'title_en']);
                    if (is_string($title) && mb_strlen($title) >= 2) {
                        $result['title'] = $title;
                    }
                }
                if (empty($result['cover'])) {
                    $cover = $this->findValueByKeys($data, ['bigPhoto', 'photo', 'image', 'cover', 'thumb', 'pic', 'poster']);
                    if (is_string($cover) && preg_match('/^https?:\/\//i', $cover)) {
                        $result['cover'] = $cover;
                    }
                }

                // 解析剧集信息
                $episodeInfo = $this->parseEpisodeInfo($data);
                if (!empty($episodeInfo['episode_num']) || !empty($episodeInfo['total_episodes'])) {
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
            $html = $this->httpGet($url, ['Referer: https://www.youku.com/']);
            if ($html) {
                // 尝试从页面内嵌数据提取
                $pageData = $this->extractPageData($html);
                if (!empty($pageData)) {
                    $title = $this->findValueByKeys($pageData, ['title', 'name', 'showname', 'show_name', 'videoName']);
                    if (is_string($title) && mb_strlen($title) >= 2) {
                        $result['title'] = $title;
                    }
                    $cover = $this->findValueByKeys($pageData, ['bigPhoto', 'photo', 'image', 'cover', 'thumb', 'pic', 'poster']);
                    if (is_string($cover) && preg_match('/^https?:\/\//i', $cover)) {
                        $result['cover'] = $cover;
                    }
                }

                // 回退到 meta 标签
                if (empty($result['title'])) {
                    $htmlTitle = $this->extractTitleFromHtml($html);
                    if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 2) {
                        $result['title'] = $htmlTitle;
                    }
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
     * 从优酷页面 HTML 中提取内嵌 JSON 数据
     * 优酷页面通常在 script 标签中嵌入 window.__pageData__ 或 pageData
     * @param string $html
     * @return array|null
     */
    protected function extractPageData($html)
    {
        if (!is_string($html) || $html === '') {
            return null;
        }

        // 匹配 window.__pageData__ = {...}
        $patterns = [
            '/window\.__pageData__\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
            '/window\["__pageData__"\]\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
            '/var\s+pageData\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
            '/window\.pageData\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
            '/window\.__INITIAL_DATA__\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
            '/window\.__DATA__\s*=\s*(\{[\s\S]*?\});?\s*<\/script>/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $m)) {
                $json = trim($m[1]);
                // 移除末尾多余的分号
                $json = rtrim($json, ';');
                $data = json_decode($json, true);
                if (is_array($data)) {
                    return $data;
                }
                // 尝试修复 JSON（优酷有时会有 JS 表达式混入）
                // 移除可能的 JS 注释
                $json = preg_replace('/\/\*[\s\S]*?\*\//', '', $json);
                $json = preg_replace('/\/\/[^\n]*/', '', $json);
                $data = json_decode($json, true);
                if (is_array($data)) {
                    return $data;
                }
            }
        }

        // 尝试从 script 标签中搜索包含 videoInfo 的 JSON 块
        if (preg_match_all('/<script[^>]*>([\s\S]*?)<\/script>/i', $html, $scripts)) {
            foreach ($scripts[1] as $script) {
                // 查找含 videoInfo 或 videoName 的 JSON 对象
                if (preg_match('/(\{[^{}]*"(?:videoInfo|videoName|showname|title)"[^{}]*\})/i', $script, $jm)) {
                    $data = json_decode($jm[1], true);
                    if (is_array($data)) {
                        return $data;
                    }
                }
            }
        }

        return null;
    }

    /**
     * 递归搜索关联数组中指定 key 的值（深度优先）
     * @param array $data
     * @param array $keys 候选 key 列表
     * @return mixed|null
     */
    protected function findValueByKeys($data, $keys)
    {
        if (!is_array($data) || empty($data)) {
            return null;
        }

        // 先在当前层级查找
        foreach ($keys as $key) {
            if (isset($data[$key]) && (is_string($data[$key]) || is_numeric($data[$key]))) {
                if (is_string($data[$key]) && $data[$key] !== '') {
                    return $data[$key];
                }
            }
        }

        // 递归在子数组中查找
        foreach ($data as $val) {
            if (is_array($val)) {
                $found = $this->findValueByKeys($val, $keys);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    /**
     * 从 HTML 中提取集数信息
     * @param string $html
     * @return array
     */
    protected function extractEpisodeFromHtml($html)
    {
        $info = [
            'episode_num' => null,
            'total_episodes' => null,
        ];

        if (!is_string($html) || $html === '') {
            return $info;
        }

        // 匹配 "第X集"
        if (preg_match('/第\s*([0-9零一二三四五六七八九十百]+)\s*集/u', $html, $m)) {
            $num = $this->chineseToNumber($m[1]);
            if ($num !== null) {
                $info['episode_num'] = $num;
            }
        }

        // 匹配 "共X集" 或 "全X集"
        if (preg_match('/(?:共|全)\s*(\d+)\s*集/u', $html, $m)) {
            $info['total_episodes'] = (int)$m[1];
        }

        // 匹配 "更新至第X集"
        if (empty($info['episode_num']) && preg_match('/更新至\s*(?:第)?\s*(\d+)\s*集/u', $html, $m)) {
            $info['episode_num'] = (int)$m[1];
        }

        // 匹配 EPXX
        if (empty($info['episode_num']) && preg_match('/[Ee][Pp]\s*(\d{1,3})/', $html, $m)) {
            $info['episode_num'] = (int)$m[1];
        }

        return $info;
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
            isset($data['data']['show']) ? $data['data']['show'] : null,
            isset($data['data']['video']) ? $data['data']['video'] : null,
            isset($data['data']['episode_info']) ? $data['data']['episode_info'] : null,
            isset($data['data']['episodes']) ? $data['data']['episodes'] : null,
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
                foreach (['total', 'episode_count', 'video_count', 'count', 'episode_total', 'total_episodes', 'totalCount', 'total_count', 'totalEpisodes', 'maxPage', 'max_page', 'episodeTotal'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['total_episodes'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_num'] === null) {
                foreach (['order', 'episode', 'index', 'seq', 'ep', 'epOrder', 'episode_order', 'episodeIndex', 'episode_index', 'page', 'episodeNo', 'episode_no', 'epIndex', 'ep_index'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['episode_num'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_name'] === '') {
                foreach (['episode_name', 'name', 'title', 'showname', 'show_name', 'short_title', 'subTitle', 'sub_title', 'epName', 'ep_name', 'titleText'] as $k) {
                    if (isset($src[$k]) && is_string($src[$k]) && $src[$k] !== '') {
                        $info['episode_name'] = (string)$src[$k];
                        break;
                    }
                }
            }
        }

        // 从 name/title 中解析集数
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
     * 计算匹配分数（优酷特定算法）
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

        // 优酷特定：VIP/会员/独播内容匹配加成
        if (!empty($candidateRemarks)) {
            if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片|蓝光/u', $candidateRemarks)) {
                $score += 5;
            }
            // 优酷"会员/独播"标记轻微加成
            if (preg_match('/vip|会员|独播/i', $candidateRemarks)) {
                $score += 3;
            }
        }

        // 优酷特定：自制剧/优酷出品标题匹配加成
        $videoRemarks = $videoInfo['remarks'] ?? '';
        if (!empty($videoRemarks) && preg_match('/优酷出品|优酷自制|独播/u', $videoRemarks)) {
            if (!empty($candidateRemarks) && preg_match('/优酷|自制|独播/u', $candidateRemarks)) {
                $score += 8;
            }
        }

        return (float)min(100, max(0, $score));
    }

    /**
     * 获取优酷去广告规则
     * @return array
     */
    public function getAdRules()
    {
        return [
            'platform' => $this->platformId,
            'description' => '优酷广告过滤规则',
            // 广告相关的 CDN/请求域名
            'ad_domains' => [
                'atm.youku.com',
                'hudong.alicdn.com',
                'vali.cp31.ott.cibntv.net',
                'pl-ali.youku.com',
                'admarketing.youku.com',
                'iyes.youku.com',
                'hudong.pl.youku.com',
                'ad-cdn.youku.com',
            ],
            // 广告片段 URI 正则模式
            'ad_url_patterns' => [
                '/\/(ads?|advert|commercial|preroll|midroll|postroll)[\/._-]/i',
                '/\/(pf|promote|promo|admp)[\/._-]/i',
                '/\b(ads?|advert|commercial)\b/i',
                '/ad_\d+\./i',
                '/^https?:\/\/[^\/]*(atm|admarketing|iyes|ad-cdn)\.youku\.com\//i',
                '/^https?:\/\/[^\/]*\.(ott\.cibntv\.net|pl-ali\.youku\.com)\//i',
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
     * 清理标题（优酷特定后缀）
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

        // 优酷特定后缀清理（具体短语优先于通用"优酷"，避免误删后残留）
        $cleaned = preg_replace('/[-_\s]?优酷出品/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?优酷自制/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?VIP\s*专享/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?会员专享/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?独播/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?\[?优酷\]?/iu', '', $cleaned);
        $cleaned = preg_replace('/[-_\s]?youku/iu', '', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned, " \t\n\r\0\x0B-_—|·");

        if (mb_strlen($cleaned) < 2) {
            return null;
        }

        return $cleaned;
    }
}
