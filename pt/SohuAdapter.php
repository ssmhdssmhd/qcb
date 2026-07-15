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

        // 兼容 query 参数形式
        if (preg_match('/[?&]vid=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }
        if (preg_match('/[?&]video_id=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 兼容 /v/xxx 数字 ID 格式
        if (preg_match('#/v/(\d+)#i', $url, $matches)) {
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
            'episode_info' => [],
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

        // 调用搜狐视频信息接口
        $apiUrl = 'https://tv.sohu.com/upload/static/quality/videoinfo/' . urlencode($videoId) . '.json';
        $response = $this->httpGet($apiUrl, [
            'Referer: https://tv.sohu.com/',
        ]);

        if (empty($response)) {
            return $result;
        }

        $data = $this->safeJsonDecode($response);

        if (empty($data) || !is_array($data)) {
            return $result;
        }

        // 搜狐视频 API 返回结构: { data: { tvName, picUrl, ... } }
        $info = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $info = $data['data'];
        } elseif (isset($data['info']) && is_array($data['info'])) {
            $info = $data['info'];
        } else {
            $info = $data;
        }

        if (!empty($info)) {
            if (!empty($info['tvName'])) {
                $result['title'] = $this->cleanTitle($info['tvName']);
            } elseif (!empty($info['title'])) {
                $result['title'] = $this->cleanTitle($info['title']);
            } elseif (!empty($info['name'])) {
                $result['title'] = $this->cleanTitle($info['name']);
            }

            if (!empty($info['picUrl'])) {
                $result['cover'] = $info['picUrl'];
            } elseif (!empty($info['cover'])) {
                $result['cover'] = $info['cover'];
            } elseif (!empty($info['image'])) {
                $result['cover'] = $info['image'];
            }

            // 集数信息
            if (!empty($info['seriesId']) || !empty($info['total'])) {
                $result['episode_info'] = [
                    'series_id' => isset($info['seriesId']) ? $info['seriesId'] : (isset($info['series_id']) ? $info['series_id'] : ''),
                    'total' => isset($info['total']) ? intval($info['total']) : (isset($info['count']) ? intval($info['count']) : 0),
                    'current' => isset($info['episode']) ? intval($info['episode']) : 1,
                ];
            }
        }

        return $result;
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
