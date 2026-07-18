<?php
/**
 * 芒果TV 平台适配器
 * 适配 mgtv.com 视频平台
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

class MgtvAdapter extends AbstractPlatformAdapter
{
    /**
     * 获取平台标识
     * @return string
     */
    public function getPlatformId()
    {
        return 'mgtv';
    }

    /**
     * 获取平台名称
     * @return string
     */
    public function getPlatformName()
    {
        return '芒果TV';
    }

    /**
     * 检测 URL 是否属于芒果TV
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (empty($url) || !is_string($url)) {
            return false;
        }
        return stripos($url, 'mgtv.com') !== false;
    }

    /**
     * 从 URL 提取视频 ID
     * 芒果TV URL 格式: https://www.mgtv.com/b/{clip_id}/{video_id}.html
     * @param string $url
     * @return array ['video_id' => '', 'cover_id' => '']
     */
    public function extractVideoId($url)
    {
        $result = ['video_id' => '', 'cover_id' => ''];

        if (empty($url) || !is_string($url)) {
            return $result;
        }

        // 匹配 /b/{clip_id}/{video_id}.html 格式
        if (preg_match('#/b/(\d+)/(\d+)\.html#i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            $result['video_id'] = $matches[2];
            return $result;
        }

        // 兼容仅有 video_id 的情况
        if (preg_match('#/b/(\d+)\.html#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 /v/{video_id}.html 等格式
        if (preg_match('#/v/(\d+)\.html#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 /play/{video_id} 格式
        if (preg_match('#/play/(\d+)#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 直接匹配 query 参数
        if (preg_match('/[?&]video_id=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        if (preg_match('/[?&]vid=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        if (preg_match('/[?&]cid=([^&]+)/i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            if (empty($result['video_id'])) {
                $result['video_id'] = $matches[1];
            }
            return $result;
        }

        if (preg_match('/[?&]fid=([^&]+)/i', $url, $matches)) {
            $result['cover_id'] = $matches[1];
            if (empty($result['video_id'])) {
                $result['video_id'] = $matches[1];
            }
            return $result;
        }

        // 匹配纯数字 ID
        if (preg_match('#/(\d{5,})\.html#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 mgtv 移动站路径
        if (preg_match('#m\.mgtv\.com.*/(\d{5,})#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        return $result;
    }

    /**
     * 获取视频信息
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

        // 调用芒果TV多个视频信息接口
        $apiUrls = [
            'https://pcweb.api.mgtv.com/player/video?video_id=' . urlencode($videoId),
            'https://pcweb.api.mgtv.com/video/info?video_id=' . urlencode($videoId),
            'https://pcweb.api.mgtv.com/episode/list?video_id=' . urlencode($videoId),
            'https://pcweb.api.mgtv.com/video/shortSourceInfo?video_id=' . urlencode($videoId),
        ];

        $titlePaths = [
            ['data', 'info', 'title'],
            ['data', 'info', 'title2'],
            ['data', 'title'],
            ['data', '0', 'title'],
            ['data', '0', 'desc'],
            ['data', 'info', 'desc'],
            ['data', 'clipInfo', 'title'],
            ['data', 'videoInfo', 'title'],
            ['data', 'video', 'title'],
            ['title'],
            ['name'],
            ['tvName'],
        ];

        $coverPaths = [
            ['data', 'info', 'cover'],
            ['data', 'info', 'image'],
            ['data', 'info', 'thumb'],
            ['data', '0', 'image'],
            ['data', 'cover'],
            ['data', 'clipInfo', 'cover'],
            ['data', 'videoInfo', 'cover'],
            ['data', 'video', 'cover'],
            ['cover'],
            ['image'],
            ['thumb'],
            ['picUrl'],
        ];

        foreach ($apiUrls as $apiUrl) {
            try {
                $response = $this->httpGet($apiUrl);
                if (empty($response)) {
                    continue;
                }

                $data = $this->safeJsonDecode($response);
                if (empty($data) || !is_array($data)) {
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
                        if (is_string($val) && mb_strlen($val) >= 2) {
                            $result['title'] = $this->cleanTitle($val);
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
            $html = $this->httpGet($url);
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
            isset($data['data']['info']) ? $data['data']['info'] : null,
            isset($data['data']['clipInfo']) ? $data['data']['clipInfo'] : null,
            isset($data['data']['videoInfo']) ? $data['data']['videoInfo'] : null,
            isset($data['data']['video']) ? $data['data']['video'] : null,
        ];
        foreach ($candidates as $c) {
            if (is_array($c)) {
                $sources[] = $c;
            }
        }

        foreach ($sources as $src) {
            if ($info['total_episodes'] === null) {
                foreach (['count', 'total', 'total_episodes', 'totalCount', 'total_count', 'episode_count', 'video_count'] as $k) {
                    if (isset($src[$k]) && is_numeric($src[$k])) {
                        $info['total_episodes'] = (int)$src[$k];
                        break;
                    }
                }
            }
            if ($info['episode_num'] === null) {
                foreach (['episode', 'ep', 'index', 'order', 'seq', 'current', 'episode_num', 'epIndex', 'ep_index'] as $k) {
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
                if ($text) {
                    if (preg_match('/第\s*(\d+)\s*集/u', $text, $em)) {
                        $info['episode_num'] = (int)$em[1];
                        break;
                    }
                    if (preg_match('/第\s*(\d+)\s*期/u', $text, $em)) {
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

        if (!$info['episode_num'] && preg_match('/第\s*([0-9零一二三四五六七八九十百]+)\s*期/u', $html, $m)) {
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

        // 芒果TV综艺常见后缀清理（如"第X期"、"第X季"）
        if (preg_match('/^(.+?)\s*第[一二三四五六七八九十\d]+季/u', $cleanTitle, $m)) {
            if (!empty($m[1]) && !in_array($m[1], $keywords)) {
                $keywords[] = $m[1];
            }
        }

        // 去掉综艺期数描述
        if (preg_match('/^(.+?)\s*第[一二三四五六七八九十\d]+期/u', $cleanTitle, $m)) {
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

        // 类型匹配加成
        $type = isset($videoInfo['type']) ? $videoInfo['type'] : '';
        $candidateType = '';
        if (is_array($candidate)) {
            $candidateType = isset($candidate['type']) ? $candidate['type'] : '';
        }
        if (!empty($type) && !empty($candidateType)) {
            $typeScore = $this->calculateBaseScore($type, $candidateType);
            $score += $typeScore * 0.15;
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
            'platform' => 'mgtv',
            // 广告请求域名屏蔽
            'block_domains' => [
                'adsdk.mgtv.com',
                'mon.mgtv.com',
                'click.hexun.com',
                'da.mgtv.com',
            ],
            // 广告请求 URL 关键字
            'block_url_patterns' => [
                '/adsdk/i',
                '/adcontrol/i',
                '/promovie/i',
                '/sdkad/i',
            ],
            // 播放器广告标记
            'ad_markers' => [
                'pre_ad',
                'mid_ad',
                'pause_ad',
            ],
            // 替换策略
            'replace_rules' => [
                'skip_ad_request' => true,
                'force_vip_quality' => false,
            ],
        ];
    }

    /**
     * 芒果TV特定标题清理
     * @param string $title
     * @return string
     */
    public function cleanTitle($title)
    {
        $title = trim($title);
        if (empty($title)) {
            return null;
        }

        // 芒果TV常见后缀清理
        $title = preg_replace('/[-_|]\s*芒果TV.*$/i', '', $title);
        $title = preg_replace('/-MGTV.*$/i', '', $title);
        $title = preg_replace('/_芒果综艺.*$/u', '', $title);

        // 调用父类通用清理
        return parent::cleanTitle($title);
    }
}
