<?php
/**
 * 哔哩哔哩 平台适配器
 * 适配 bilibili.com 视频平台
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

class BilibiliAdapter extends AbstractPlatformAdapter
{
    /**
     * 获取平台标识
     * @return string
     */
    public function getPlatformId()
    {
        return 'bilibili';
    }

    /**
     * 获取平台名称
     * @return string
     */
    public function getPlatformName()
    {
        return '哔哩哔哩';
    }

    /**
     * 检测 URL 是否属于哔哩哔哩
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }
        return stripos($url, 'bilibili.com') !== false;
    }

    /**
     * 从 URL 提取视频 ID（BV号或AV号）
     * 支持格式:
     *   https://www.bilibili.com/video/BV1xx411c7mD
     *   https://www.bilibili.com/video/av170001
     *   https://b23.tv/xxxxx (短链需先跳转)
     *   https://www.bilibili.com/bangumi/play/ss12345 (番剧)
     *   https://www.bilibili.com/bangumi/play/ep123456 (番剧集)
     * @param string $url
     * @return array ['video_id' => '', 'cover_id' => '']
     */
    public function extractVideoId($url)
    {
        $result = ['video_id' => '', 'cover_id' => ''];

        if (empty($url) || !is_string($url)) {
            return $result;
        }

        // 匹配 BV 号（BV 后跟 10 位字符，也可能有更多字符）
        if (preg_match('#/(BV[0-9A-Za-z]{10,12})#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 av 号
        if (preg_match('#/av(\d+)#i', $url, $matches)) {
            $result['video_id'] = 'av' . $matches[1];
            return $result;
        }

        // 匹配番剧 ss 号
        if (preg_match('#/ss(\d+)#i', $url, $matches)) {
            $result['cover_id'] = 'ss' . $matches[1];
            $result['video_id'] = 'ss' . $matches[1];
            return $result;
        }

        // 匹配番剧 ep 号
        if (preg_match('#/ep(\d+)#i', $url, $matches)) {
            $result['video_id'] = 'ep' . $matches[1];
            return $result;
        }

        // 兼容 query 参数形式
        if (preg_match('/[?&]bvid=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }
        if (preg_match('/[?&]aid=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = 'av' . $matches[1];
            return $result;
        }
        if (preg_match('/[?&]season_id=([^&]+)/i', $url, $matches)) {
            $result['cover_id'] = 'ss' . $matches[1];
            if (empty($result['video_id'])) {
                $result['video_id'] = 'ss' . $matches[1];
            }
            return $result;
        }
        if (preg_match('/[?&]ep_id=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = 'ep' . $matches[1];
            return $result;
        }

        // 匹配 b23.tv 短链接（从路径中提取）
        if (preg_match('#b23\.tv/([a-zA-Z0-9]+)#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        return $result;
    }

    /**
     * 获取视频信息
     * 使用 API: https://api.bilibili.com/x/web-interface/view?bvid=xxx
     * 番剧使用: https://api.bilibili.com/pgc/view/web/season?season_id=xxx
     * @param string $url
     * @param array $videoIds
     * @return array ['title' => '', 'cover' => '', 'episode_info' => []]
     */
    public function fetchVideoInfo($url, $videoIds)
    {
        $result = [
            'title' => '',
            'cover' => '',
            'episode_info' => [
                'episode_num' => null,
                'episode_name' => '',
                'total_episodes' => null,
            ],
        ];

        $videoId = '';
        if (is_array($videoIds) && !empty($videoIds['video_id'])) {
            $videoId = $videoIds['video_id'];
        } elseif (is_string($videoIds) && $videoIds !== '') {
            $videoId = $videoIds;
        }

        if (empty($videoId)) {
            $extracted = $this->extractVideoId($url);
            $videoId = $extracted['video_id'];
        }

        if (empty($videoId)) {
            return $result;
        }

        $lowerId = strtolower($videoId);
        $isBangumi = strpos($lowerId, 'ss') === 0 || strpos($lowerId, 'ep') === 0;

        if ($isBangumi) {
            $result = $this->fetchBangumiInfo($videoId);
        } else {
            $result = $this->fetchVideoInfoByApi($videoId);
        }

        // HTML 页面兜底
        if (empty($result['title']) || mb_strlen($result['title']) < 3) {
            $html = $this->httpGet($url, [
                'Referer: https://www.bilibili.com/',
            ]);
            if ($html) {
                $htmlTitle = $this->extractTitleFromHtml($html);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3) {
                    $result['title'] = $this->cleanTitle($htmlTitle);
                }
                if (empty($result['cover'])) {
                    $htmlCover = $this->extractCoverFromHtml($html);
                    if (!empty($htmlCover)) {
                        $result['cover'] = $htmlCover;
                    }
                }
                // 从 HTML 提取集数
                $htmlEpisode = $this->extractEpisodeFromHtml($html);
                if (!empty($htmlEpisode['episode_num']) && empty($result['episode_info']['episode_num'])) {
                    $result['episode_info'] = $htmlEpisode;
                }
            }
        }

        return $result;
    }

    /**
     * 获取普通视频信息
     * @param string $videoId
     * @return array
     */
    private function fetchVideoInfoByApi($videoId)
    {
        $result = [
            'title' => '',
            'cover' => '',
            'episode_info' => [
                'episode_num' => null,
                'episode_name' => '',
                'total_episodes' => null,
            ],
        ];

        // 构建请求参数：BV 号直接用 bvid，av 号转换或使用 aid
        $apiUrl = '';
        $lowerId = strtolower($videoId);
        if (strpos($lowerId, 'bv') === 0) {
            $apiUrl = 'https://api.bilibili.com/x/web-interface/view?bvid=' . urlencode($videoId);
        } elseif (strpos($lowerId, 'av') === 0) {
            $aid = substr($videoId, 2);
            $apiUrl = 'https://api.bilibili.com/x/web-interface/view?aid=' . urlencode($aid);
        } else {
            $apiUrl = 'https://api.bilibili.com/x/web-interface/view?bvid=' . urlencode($videoId);
        }

        $response = $this->httpGet($apiUrl, [
            'Referer: https://www.bilibili.com/',
        ]);

        if (empty($response)) {
            return $result;
        }

        $data = $this->safeJsonDecode($response);

        if (empty($data) || !is_array($data)) {
            return $result;
        }

        $info = isset($data['data']) && is_array($data['data']) ? $data['data'] : null;

        if (!empty($info)) {
            if (!empty($info['title'])) {
                $result['title'] = $this->cleanTitle($info['title']);
            }
            if (!empty($info['pic'])) {
                $result['cover'] = $info['pic'];
            }

            // 分 P 信息
            if (!empty($info['videos']) && intval($info['videos']) > 1) {
                $result['episode_info'] = [
                    'aid' => isset($info['aid']) ? $info['aid'] : '',
                    'bvid' => isset($info['bvid']) ? $info['bvid'] : $videoId,
                    'total_episodes' => isset($info['videos']) ? intval($info['videos']) : 1,
                    'episode_num' => isset($info['cid']) ? 1 : 0,
                ];
            }

            // UP 主信息附加
            if (!empty($info['owner']['name'])) {
                $result['owner'] = $info['owner']['name'];
            }
        }

        return $result;
    }

    /**
     * 获取番剧信息
     * @param string $bangumiId
     * @return array
     */
    private function fetchBangumiInfo($bangumiId)
    {
        $result = [
            'title' => '',
            'cover' => '',
            'episode_info' => [
                'episode_num' => null,
                'episode_name' => '',
                'total_episodes' => null,
            ],
        ];

        $lowerId = strtolower($bangumiId);
        $apiUrl = '';

        if (strpos($lowerId, 'ss') === 0) {
            $seasonId = substr($bangumiId, 2);
            $apiUrl = 'https://api.bilibili.com/pgc/view/web/season?season_id=' . urlencode($seasonId);
        } elseif (strpos($lowerId, 'ep') === 0) {
            $epId = substr($bangumiId, 2);
            $apiUrl = 'https://api.bilibili.com/pgc/view/web/episode?ep_id=' . urlencode($epId);
        }

        if (empty($apiUrl)) {
            return $result;
        }

        $response = $this->httpGet($apiUrl, [
            'Referer: https://www.bilibili.com/',
        ]);

        if (empty($response)) {
            return $result;
        }

        $data = $this->safeJsonDecode($response);

        if (empty($data) || !is_array($data)) {
            return $result;
        }

        $info = isset($data['result']) && is_array($data['result']) ? $data['result'] : (isset($data['data']) && is_array($data['data']) ? $data['data'] : null);

        if (!empty($info)) {
            if (!empty($info['title'])) {
                $result['title'] = $this->cleanTitle($info['title']);
            } elseif (!empty($info['season_title'])) {
                $result['title'] = $this->cleanTitle($info['season_title']);
            }

            if (!empty($info['cover'])) {
                $result['cover'] = $info['cover'];
            } elseif (!empty($info['square_cover'])) {
                $result['cover'] = $info['square_cover'];
            }

            // 剧集信息
            $episodes = isset($info['episodes']) && is_array($info['episodes']) ? $info['episodes'] : [];
            if (!empty($episodes)) {
                $result['episode_info']['total_episodes'] = count($episodes);

                // 如果是 ep 开头，尝试找到对应集数
                if (strpos($lowerId, 'ep') === 0) {
                    $epId = substr($bangumiId, 2);
                    foreach ($episodes as $idx => $ep) {
                        if (isset($ep['id']) && strval($ep['id']) === strval($epId)) {
                            $result['episode_info']['episode_num'] = $idx + 1;
                            if (!empty($ep['long_title'])) {
                                $result['episode_info']['episode_name'] = $ep['long_title'];
                            } elseif (!empty($ep['share_copy'])) {
                                $result['episode_info']['episode_name'] = $ep['share_copy'];
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 从 HTML 中提取集数信息
     * @param string $html
     * @return array
     */
    private function extractEpisodeFromHtml($html)
    {
        $info = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null,
        ];

        if (!is_string($html) || $html === '') {
            return $info;
        }

        if (preg_match('/第\s*([0-9零一二三四五六七八九十百]+)\s*集/u', $html, $m)) {
            $num = $this->chineseToNumber($m[1]);
            if ($num !== null) {
                $info['episode_num'] = $num;
                $info['episode_name'] = $m[0];
            }
        }

        if (!$info['episode_num'] && preg_match('/[Ee][Pp]?\s*(\d{1,4})/', $html, $m)) {
            $info['episode_num'] = (int)$m[1];
            $info['episode_name'] = $m[0];
        }

        if (preg_match('/(?:共|全|总计)\s*(\d+)\s*集/u', $html, $m)) {
            $info['total_episodes'] = (int)$m[1];
        }

        return $info;
    }

    /**
     * 中文数字转整数
     * @param string $str
     * @return int|null
     */
    protected function chineseToNumber($str)
    {
        if ($str === '' || $str === null) {
            return null;
        }
        $str = (string)$str;

        if (ctype_digit($str)) {
            return (int)$str;
        }

        $cnNumbers = [
            '零' => 0, '一' => 1, '二' => 2, '两' => 2, '三' => 3,
            '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8,
            '九' => 9, '十' => 10, '百' => 100, '千' => 1000,
        ];

        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $result = 0;
        $temp = 0;

        foreach ($chars as $char) {
            if (!isset($cnNumbers[$char])) {
                continue;
            }
            $val = $cnNumbers[$char];
            if ($val >= 10) {
                if ($temp == 0) {
                    $temp = 1;
                }
                $result += $temp * $val;
                $temp = 0;
            } else {
                $temp = $val;
            }
        }
        $result += $temp;

        return $result > 0 ? $result : null;
    }

    /**
     * 构建搜索关键词
     * @param array $videoInfo
     * @return array
     */
    public function buildSearchKeywords($videoInfo)
    {
        $keywords = [];

        if (empty($videoInfo) || !is_array($videoInfo)) {
            return $keywords;
        }

        $title = isset($videoInfo['title']) ? $videoInfo['title'] : '';

        if (empty($title)) {
            return $keywords;
        }

        $cleanTitle = $this->cleanTitle($title);

        if (!empty($cleanTitle)) {
            $keywords[] = $cleanTitle;
        }

        // 哔哩哔哩常见后缀清理（如"【xxx】"、EP 标记）
        if (preg_match('/【([^】]+)】/u', $cleanTitle, $m)) {
            // 括号内内容作为关键词
            if (!empty($m[1]) && mb_strlen($m[1]) >= 2) {
                $keywords[] = $m[1];
            }
        }

        // 去掉 EP / P 标记
        if (preg_match('/^(.+?)\s*[Pp]\d+/u', $cleanTitle, $m)) {
            if (!empty($m[1]) && !in_array($m[1], $keywords)) {
                $keywords[] = trim($m[1]);
            }
        }

        // 去掉方括号内的标记
        $stripped = preg_replace('/\[[^\]]+\]/u', '', $cleanTitle);
        $stripped = trim($stripped);
        if (!empty($stripped) && $stripped !== $cleanTitle && !in_array($stripped, $keywords)) {
            $keywords[] = $stripped;
        }

        return array_values(array_filter(array_unique($keywords)));
    }

    /**
     * 计算匹配分数
     * @param array $videoInfo 官方视频信息
     * @param array $candidate 资源站候选项
     * @return float 0-100
     */
    public function calculateMatchScore($videoInfo, $candidate)
    {
        if (empty($videoInfo) || empty($candidate)) {
            return 0;
        }

        $title = isset($videoInfo['title']) ? $videoInfo['title'] : '';
        $candidateTitle = '';
        $candidateSubTitle = '';
        if (is_array($candidate)) {
            $candidateTitle = isset($candidate['title']) ? $candidate['title'] : (isset($candidate['name']) ? $candidate['name'] : '');
            $candidateSubTitle = isset($candidate['sub_title']) ? $candidate['sub_title'] : '';
        }

        $score = 0;
        $titleScore = 0;

        if (!empty($title) && !empty($candidateTitle)) {
            $cleanOfficial = $this->cleanTitle($title);
            $cleanCandidate = $this->cleanTitle($candidateTitle);
            $titleScore = $this->calculateBaseScore($cleanOfficial, $cleanCandidate);
        }

        $score = $titleScore * 0.6;

        // 副标题匹配加成
        if (!empty($candidateSubTitle)) {
            $cleanOfficial = $this->cleanTitle($title);
            $subScore = $this->calculateBaseScore($cleanOfficial, $this->cleanTitle($candidateSubTitle));
            $score += $subScore * 0.2;
        }

        // UP主/作者匹配加成
        $owner = isset($videoInfo['owner']) ? $videoInfo['owner'] : '';
        $candidateActor = '';
        if (is_array($candidate)) {
            $candidateActor = isset($candidate['actor']) ? $candidate['actor'] : (isset($candidate['author']) ? $candidate['author'] : '');
        }
        if (!empty($owner) && !empty($candidateActor)) {
            $ownerScore = $this->calculateBaseScore($owner, $candidateActor);
            $score += $ownerScore * 0.1;
        }

        // 年份匹配加成
        $year = isset($videoInfo['year']) ? $videoInfo['year'] : '';
        $candidateYear = '';
        if (is_array($candidate)) {
            $candidateYear = isset($candidate['year']) ? $candidate['year'] : '';
        }
        if (!empty($year) && !empty($candidateYear) && (string)$year === (string)$candidateYear) {
            $score += 10;
        }

        return min(100, max(0, round($score, 2)));
    }

    /**
     * 获取去广告规则
     * @return array
     */
    public function getAdRules()
    {
        return [
            'platform' => 'bilibili',
            // 广告请求域名屏蔽
            'block_domains' => [
                'cm.bilibili.com',
                'api.bilibili.com/x/cm',
                'pos.baidu.com',
                'integral.eastmoney.com',
            ],
            // 广告请求 URL 关键字
            'block_url_patterns' => [
                '/\/x\/cm\//i',
                '/\/sdk\/ad/i',
                '/sponsor/i',
                '/bilibili-shop/i',
            ],
            // 播放器广告标记
            'ad_markers' => [
                'inline_ad',
                'pause_ad',
                'banner_ad',
                'sponsor_list',
            ],
            // 替换策略
            'replace_rules' => [
                'skip_ad_request' => true,
                'remove_sponsor_segment' => true,
                'force_bangumi_without_ad' => true,
            ],
        ];
    }

    /**
     * 哔哩哔哩特定标题清理
     * @param string $title
     * @return string
     */
    public function cleanTitle($title)
    {
        $title = trim($title);
        if (empty($title)) {
            return null;
        }

        // 哔哩哔哩常见后缀清理
        $title = preg_replace('/[-_|]\s*哔哩哔哩.*$/u', '', $title);
        $title = preg_replace('/[-_|]\s*bilibili.*$/i', '', $title);
        $title = preg_replace('/_哔哩哔哩_bilibili.*$/i', '', $title);

        // 调用父类通用清理
        return parent::cleanTitle($title);
    }
}
