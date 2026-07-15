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
     * @param string $url
     * @return array ['video_id' => '', 'cover_id' => '']
     */
    public function extractVideoId($url)
    {
        $result = ['video_id' => '', 'cover_id' => ''];

        if (empty($url) || !is_string($url)) {
            return $result;
        }

        // 匹配 BV 号（BV 后跟 10 位字符）
        if (preg_match('#/(BV[0-9A-Za-z]{10})#i', $url, $matches)) {
            $result['video_id'] = $matches[1];
            return $result;
        }

        // 匹配 av 号
        if (preg_match('#/av(\d+)#i', $url, $matches)) {
            $result['video_id'] = 'av' . $matches[1];
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

        return $result;
    }

    /**
     * 获取视频信息
     * 使用 API: https://api.bilibili.com/x/web-interface/view?bvid=xxx
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

        // 构建请求参数：BV 号直接用 bvid，av 号转换或使用 aid
        $apiUrl = '';
        $lowerId = strtolower($videoId);
        if (strpos($lowerId, 'bv') === 0) {
            $apiUrl = 'https://api.bilibili.com/x/web-interface/view?bvid=' . urlencode($videoId);
        } elseif (strpos($lowerId, 'av') === 0) {
            $aid = substr($videoId, 2);
            $apiUrl = 'https://api.bilibili.com/x/web-interface/view?aid=' . urlencode($aid);
        } else {
            // 默认按 bvid 处理
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

        // 哔哩哔哩 API 返回结构: { code: 0, message: '0', data: { title, pic, ... } }
        $info = null;
        if (isset($data['data']) && is_array($data['data'])) {
            $info = $data['data'];
        }

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
                    'total' => isset($info['videos']) ? intval($info['videos']) : 1,
                    'current' => isset($info['cid']) ? 1 : 0,
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
