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

        // 直接匹配 query 参数
        if (preg_match('/[?&]video_id=([^&]+)/i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配纯数字 ID
        if (preg_match('#/(\d{5,})\.html#i', $url, $matches)) {
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
            'episode_info' => [],
        ];

        $videoId = '';
        if (is_array($videoIds) && !empty($videoIds['video_id'])) {
            $videoId = $videoIds['video_id'];
        } elseif (is_string($videoIds) && $videoIds !== '') {
            $videoId = $videoIds;
        }

        if (empty($videoId)) {
            // 尝试从 URL 重新提取
            $extracted = $this->extractVideoId($url);
            $videoId = $extracted['video_id'];
        }

        if (empty($videoId)) {
            return $result;
        }

        // 调用芒果TV视频信息接口
        $apiUrl = 'https://pcweb.api.mgtv.com/player/video?video_id=' . urlencode($videoId);
        $response = $this->httpGet($apiUrl);

        if (empty($response)) {
            return $result;
        }

        $data = $this->safeJsonDecode($response);

        if (empty($data) || !is_array($data)) {
            return $result;
        }

        // 芒果TV API 返回结构: { code: 200, data: { info: { title, thumb, ... } } }
        $info = null;
        if (isset($data['data']['info']) && is_array($data['data']['info'])) {
            $info = $data['data']['info'];
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $info = $data['data'];
        }

        if (!empty($info)) {
            if (!empty($info['title'])) {
                $result['title'] = $this->cleanTitle($info['title']);
            }
            if (!empty($info['thumb'])) {
                $result['cover'] = $info['thumb'];
            } elseif (!empty($info['image'])) {
                $result['cover'] = $info['image'];
            }

            // 集数信息
            if (!empty($info['series_id']) || !empty($info['count'])) {
                $result['episode_info'] = [
                    'series_id' => isset($info['series_id']) ? $info['series_id'] : '',
                    'total' => isset($info['count']) ? intval($info['count']) : 0,
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
