<?php
/**
 * 搜狐视频 平台适配器
 * 适配 tv.sohu.com 视频平台
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

class SohuAdapter extends AbstractPlatformAdapter
{
    /**
     * 获取平台标识
     * @return string
     */
    public function getPlatformId()
    {
        return 'sohu';
    }

    /**
     * 获取平台名称
     * @return string
     */
    public function getPlatformName()
    {
        return '搜狐视频';
    }

    /**
     * 检测 URL 是否属于搜狐视频
     * 仅匹配 tv.sohu.com，排除新闻等其他 sohu.com 页面
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }

        // 仅匹配 tv.sohu.com 视频域名，排除新闻等页面
        if (preg_match('#https?://([^/]+\.)*tv\.sohu\.com#i', $url)) {
            return true;
        }

        // my.tv.sohu.com 用户视频也属于视频平台
        if (preg_match('#https?://([^/]+\.)*my\.tv\.sohu\.com#i', $url)) {
            return true;
        }

        // film.sohu.com 影视频道
        if (preg_match('#https?://([^/]+\.)*film\.sohu\.com#i', $url)) {
            return true;
        }

        return false;
    }

    /**
     * 从 URL 提取视频 ID
     * 搜狐视频 URL 格式: https://tv.sohu.com/20180315/n592834567.shtml
     * @param string $url
     * @return array ['video_id' => '', 'cover_id' => '']
     */
    public function extractVideoId($url)
    {
        $result = ['video_id' => '', 'cover_id' => ''];

        if (empty($url) || !is_string($url)) {
            return $result;
        }

        // 匹配 /xxx.shtml 格式（视频 ID 通常以 n 开头）
        if (preg_match('#/([a-zA-Z]?\d{5,})\.shtml#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 /xxx.html 格式
        if (preg_match('#/([a-zA-Z]?\d{6,})\.html?#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 兼容 query 参数形式
        if (preg_match('/[?&]vid=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }
        if (preg_match('/[?&]video_id=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }
        if (preg_match('/[?&]aid=([^&]+)/i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            if (empty($result['video_id'])) {
                $result['video_id'] = $matches[1];
            }
            return $result;
        }
        if (preg_match('/[?&]cid=([^&]+)/i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            if (empty($result['video_id'])) {
                $result['video_id'] = $matches[1];
            }
            return $result;
        }

        // 兼容 /v/xxx 数字 ID 格式
        if (preg_match('#/v/(\d+)#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 兼容 /album/xxx 专辑格式
        if (preg_match('#/album/(\d+)#i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            $result['video_id'] = $matches[1];
            return $result;
        }

        return $result;
    }

    /**
     * 获取视频信息
     * 使用 API: https://tv.sohu.com/upload/static/quality/videoinfo/{video_id}.json
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

        // 调用搜狐视频多个信息接口
        $apiUrls = [
            'https://tv.sohu.com/upload/static/quality/videoinfo/' . urlencode($videoId) . '.json',
            'https://api.tv.sohu.com/video/info?vid=' . urlencode($videoId),
            'https://open.sohu.com/video/info?vid=' . urlencode($videoId),
        ];

        $titleKeys = ['tvName', 'title', 'name', 'videoName', 'video_title', 'mainTitle', 'subTitle'];
        $coverKeys = ['picUrl', 'cover', 'image', 'pic', 'thumb', 'thumbnail', 'vpic', 'bigPic'];

        foreach ($apiUrls as $apiUrl) {
            try {
                $response = $this->httpGet($apiUrl, [
                    'Referer: https://tv.sohu.com/',
                ]);
                if (empty($response)) {
                    continue;
                }

                $data = $this->safeJsonDecode($response);
                if (empty($data) || !is_array($data)) {
                    continue;
                }

                // 递归查找标题和封面
                $found = $this->findTitleAndCover($data, $titleKeys, $coverKeys);

                if (empty($result['title']) && !empty($found['title'])) {
                    $result['title'] = $this->cleanTitle($found['title']);
                }
                if (empty($result['cover']) && !empty($found['cover'])) {
                    $result['cover'] = $found['cover'];
                }

                // 提取剧集信息
                $episodeInfo = $this->parseEpisodeFromData($data);
                if (!empty($episodeInfo['episode_num']) || !empty($episodeInfo['total_episodes'])) {
                    if (empty($result['episode_info']['episode_num'])) {
                        $result['episode_info']['episode_num'] = $episodeInfo['episode_num'];
                    }
                    if (empty($result['episode_info']['episode_name'])) {
                        $result['episode_info']['episode_name'] = $episodeInfo['episode_name'];
                    }
                    if (empty($result['episode_info']['total_episodes'])) {
                        $result['episode_info']['total_episodes'] = $episodeInfo['total_episodes'];
                    }
                }

                if (!empty($result['title']) && !empty($result['cover'])) {
                    break;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        // HTML 页面兜底
        if (empty($result['title']) || mb_strlen($result['title']) < 3) {
            $html = $this->httpGet($url, [
                'Referer: https://tv.sohu.com/',
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
     * 递归查找数据中的标题和封面
     * @param array $data
     * @param array $titleKeys
     * @param array $coverKeys
     * @return array
     */
    private function findTitleAndCover($data, $titleKeys, $coverKeys)
    {
        $result = ['title' => null, 'cover' => null];
        if (!is_array($data)) {
            return $result;
        }

        $invalidTitles = ['hd', 'shd', 'fhd', '4k', '8k', '标清', '高清', '超清', '蓝光', '1080p', '720p', 'vip', '免费', '预告', '花絮'];

        array_walk_recursive($data, function ($value, $key) use (&$result, $titleKeys, $coverKeys, $invalidTitles) {
            if (empty($result['title']) && in_array($key, $titleKeys, true) && is_string($value) && mb_strlen($value) >= 2 && mb_strlen($value) <= 100) {
                $lower = mb_strtolower($value);
                foreach ($invalidTitles as $inv) {
                    if ($lower === mb_strtolower($inv)) {
                        return;
                    }
                }
                $result['title'] = $value;
            }
            if (empty($result['cover']) && in_array($key, $coverKeys, true) && is_string($value) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $value)) {
                $result['cover'] = $value;
            }
        });

        return $result;
    }

    /**
     * 从 API 数据中解析剧集信息
     * @param array $data
     * @return array
     */
    private function parseEpisodeFromData($data)
    {
        $info = [
            'episode_num' => null,
            'episode_name' => '',
            'total_episodes' => null,
        ];

        $sources = [];
        $candidates = [
            isset($data['data']) ? $data['data'] : null,
            isset($data['info']) ? $data['info'] : null,
            isset($data['data']['videoInfo']) ? $data['data']['videoInfo'] : null,
            isset($data['data']['albumInfo']) ? $data['data']['albumInfo'] : null,
            isset($data['videoInfo']) ? $data['videoInfo'] : null,
            isset($data['albumInfo']) ? $data['albumInfo'] : null,
        ];
        foreach ($candidates as $c) {
            if (is_array($c)) {
                $sources[] = $c;
            }
        }

        foreach ($sources as $src) {
            if ($info['total_episodes'] === null) {
                foreach (['total', 'count', 'total_episodes', 'totalCount', 'total_count', 'episode_count', 'video_count', 'seriesCount', 'series_count'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['total_episodes'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_num'] === null) {
                foreach (['episode', 'ep', 'index', 'order', 'seq', 'current', 'episode_num', 'epIndex', 'ep_index', 'episodeIndex'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['episode_num'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_name'] === '') {
                foreach (['episode_name', 'name', 'title', 'short_title', 'sub_title', 'subTitle', 'epName', 'ep_name'] as $k) {
                    if (isset($src[$k]) && is_string($src[$k]) && $src[$k] !== '') {
                        $info['episode_name'] = (string)$src[$k];
                        break;
                    }
                }
            }
        }

        // 从标题中解析集数
        if ($info['episode_num'] === null) {
            foreach ($sources as $src) {
                $text = '';
                if (!empty($src['title'])) $text = $src['title'];
                elseif (!empty($src['name'])) $text = $src['name'];
                elseif (!empty($src['tvName'])) $text = $src['tvName'];
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
    private function chineseToNumber($str)
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

        // 搜狐视频常见后缀清理（如"第X集"）
        if (preg_match('/^(.+?)\s*第[一二三四五六七八九十\d]+集/u', $cleanTitle, $m)) {
            if (!empty($m[1]) && !in_array($m[1], $keywords)) {
                $keywords[] = $m[1];
            }
        }

        // 去掉季数描述
        if (preg_match('/^(.+?)\s*第[一二三四五六七八九十\d]+季/u', $cleanTitle, $m)) {
            if (!empty($m[1]) && !in_array($m[1], $keywords)) {
                $keywords[] = $m[1];
            }
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

        $score = 0;
        $titleScore = 0;
        $title = isset($videoInfo['title']) ? $videoInfo['title'] : '';
        $candidateTitle = '';
        if (is_array($candidate)) {
            $candidateTitle = isset($candidate['title']) ? $candidate['title'] : (isset($candidate['name']) ? $candidate['name'] : '');
        }

        if (!empty($title) && !empty($candidateTitle)) {
            $cleanOfficial = $this->cleanTitle($title);
            $cleanCandidate = $this->cleanTitle($candidateTitle);
            $titleScore = $this->calculateBaseScore($cleanOfficial, $cleanCandidate);
        }

        $score = $titleScore * 0.7;

        // 年份匹配加成
        $year = isset($videoInfo['year']) ? $videoInfo['year'] : '';
        $candidateYear = '';
        if (is_array($candidate)) {
            $candidateYear = isset($candidate['year']) ? $candidate['year'] : '';
        }
        if (!empty($year) && !empty($candidateYear) && (string)$year === (string)$candidateYear) {
            $score += 15;
        }

        // 导演/演员匹配加成
        $actor = '';
        if (is_array($candidate)) {
            $actor = isset($candidate['actor']) ? $candidate['actor'] : (isset($candidate['director']) ? $candidate['director'] : '');
        }
        $videoActor = isset($videoInfo['actor']) ? $videoInfo['actor'] : '';
        if (!empty($videoActor) && !empty($actor)) {
            $actorScore = $this->calculateBaseScore($videoActor, $actor);
            $score += $actorScore * 0.1;
        }

        // 类型匹配加成
        $type = isset($videoInfo['type']) ? $videoInfo['type'] : '';
        $candidateType = '';
        if (is_array($candidate)) {
            $candidateType = isset($candidate['type']) ? $candidate['type'] : '';
        }
        if (!empty($type) && !empty($candidateType)) {
            $typeScore = $this->calculateBaseScore($type, $candidateType);
            $score += $typeScore * 0.05;
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
            'platform' => 'sohu',
            // 广告请求域名屏蔽
            'block_domains' => [
                'ads.sohu.com',
                'imp.optaim.com',
                'wm.amazon-adsystem.com',
                'hd.sohu.com.cn',
                'cpro.baidu.com',
            ],
            // 广告请求 URL 关键字
            'block_url_patterns' => [
                '/\/ads\//i',
                '/\/advert/i',
                '/vrsdk/i',
                '/adplayer/i',
                '/gg\.sohu/i',
            ],
            // 播放器广告标记
            'ad_markers' => [
                'pre_ad',
                'mid_ad',
                'post_ad',
                'pause_ad',
            ],
            // 替换策略
            'replace_rules' => [
                'skip_ad_request' => true,
                'force_high_quality' => false,
                'remove_ad_section' => true,
            ],
        ];
    }

    /**
     * 搜狐视频特定标题清理
     * @param string $title
     * @return string
     */
    public function cleanTitle($title)
    {
        $title = trim($title);
        if (empty($title)) {
            return null;
        }

        // 搜狐视频常见后缀清理
        $title = preg_replace('/[-_|]\s*搜狐视频.*$/u', '', $title);
        $title = preg_replace('/[-_|]\s*sohu\.com.*$/i', '', $title);
        $title = preg_replace('/_搜狐.*$/u', '', $title);
        $title = preg_replace('/高清版.*?$/u', '', $title);

        // 调用父类通用清理
        return parent::cleanTitle($title);
    }
}
