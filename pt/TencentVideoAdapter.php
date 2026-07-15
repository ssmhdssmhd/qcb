<?php
/**
 * 腾讯视频平台适配器
 * 实现 PlatformAdapterInterface，处理 v.qq.com 平台的视频 ID 提取、
 * 视频信息获取、搜索关键词构建、匹配评分与去广告规则。
 *
 * PHP 7.4+ 兼容
 */

require_once __DIR__ . '/AbstractPlatformAdapter.php';

// 可选依赖：标题归一化工具（存在则启用，缺失则使用内置降级实现）
if (!class_exists('TitleNormalizer')) {
    $normalizerPath = __DIR__ . '/../gz/TitleNormalizer.php';
    if (is_file($normalizerPath)) {
        require_once $normalizerPath;
    }
}

class TencentVideoAdapter extends AbstractPlatformAdapter
{
    /** @var string 平台标识 */
    protected $platformId = 'tencent';

    /** @var string 平台名称 */
    protected $platformName = '腾讯视频';

    /** @var string 主域名 */
    protected $domain = 'v.qq.com';

    /**
     * 构造函数
     * @param array $config 平台配置
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'http_timeout'    => 15,
            'match_threshold' => 60,
        ], $config);

        if (!empty($this->config['http_timeout'])) {
            $this->httpTimeout = (int) $this->config['http_timeout'];
        }
    }

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
     * 检测 URL 是否属于腾讯视频
     * @param string $url
     * @return bool
     */
    public function matches($url)
    {
        if (!is_string($url) || $url === '') {
            return false;
        }
        return stripos($url, $this->domain) !== false;
    }

    /**
     * 从 URL 提取视频 ID
     * 支持 /x/cover/{cover_id}/{vid}.html 与 ?vid=xxx 等格式
     * @param string $url
     * @return array ['video_id' => string|null, 'cover_id' => string|null]
     */
    public function extractVideoId($url)
    {
        $videoId = null;
        $coverId = null;

        if (!is_string($url) || $url === '') {
            return ['video_id' => null, 'cover_id' => null];
        }

        // /x/cover/{cover_id}/{vid}.html
        if (preg_match('#/x/cover/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)\.html?#i', $url, $m)) {
            $coverId = $m[1];
            $videoId = $m[2];
        } elseif (preg_match('#/cover/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)#i', $url, $m)) {
            // /cover/{cover_id}/{vid}
            $coverId = $m[1];
            $videoId = $m[2];
        } elseif (preg_match('/[?&]vid=([a-zA-Z0-9]+)/i', $url, $m)) {
            // ?vid=xxx
            $videoId = $m[1];
        } elseif (preg_match('#/x/page/([a-zA-Z0-9]+)#i', $url, $m)) {
            // /x/page/{vid}
            $videoId = $m[1];
        } elseif (preg_match('#/x/cover/([a-zA-Z0-9]+)\.html?#i', $url, $m)) {
            // /x/cover/{cover_id}.html
            $coverId = $m[1];
            $videoId = $m[1];
        } elseif (preg_match('#/play/([a-zA-Z0-9]+)#i', $url, $m)) {
            // /play/{vid}
            $videoId = $m[1];
        } elseif (preg_match('#/([a-zA-Z0-9]{8,16})\.html?$#i', $url, $m)) {
            // /{vid}.html
            $videoId = $m[1];
        }

        return [
            'video_id' => $videoId,
            'cover_id' => $coverId,
        ];
    }

    /**
     * 获取视频信息（标题、封面等）
     * 依次尝试多个 API 与 HTML 页面解析。
     * @param string $url
     * @param array  $videoIds extractVideoId 返回的 ID 集合
     * @return array ['title' => '', 'cover' => '', 'episode_info' => [], ...]
     */
    public function fetchVideoInfo($url, $videoIds)
    {
        $videoId = $videoIds['video_id'] ?? '';
        $coverId = $videoIds['cover_id'] ?? '';

        $title = null;
        $cover = null;
        $episodeInfo = [
            'episode_num'   => null,
            'episode_name'  => '',
            'total_episodes' => null,
        ];
        $isExpired = false;

        // 1) 多 API 获取
        $apiInfo = $this->fetchVideoInfoFromApi($videoId, $coverId, $url);
        if (is_array($apiInfo)) {
            if (!empty($apiInfo['title'])) {
                $title = $apiInfo['title'];
            }
            if (!empty($apiInfo['cover'])) {
                $cover = $apiInfo['cover'];
            }
        }

        // 2) HTML 页面解析兜底
        if ((empty($title) || mb_strlen($title) < 3) && !empty($url)) {
            $html = $this->httpGet($url);
            if ($html) {
                $htmlTitle = $this->extractTitleFromHtml($html);
                if (!empty($htmlTitle) && mb_strlen($htmlTitle) >= 3) {
                    $title = $htmlTitle;
                }
                if (empty($cover)) {
                    $htmlCover = $this->extractCoverFromHtml($html);
                    if (!empty($htmlCover)) {
                        $cover = $htmlCover;
                    }
                }
                $parsedEpisode = $this->extractEpisodeFromHtml($html);
                if (!empty($parsedEpisode['episode_num'])) {
                    $episodeInfo = $parsedEpisode;
                }
            }
        }

        // 3) URL 兜底标题
        if (empty($title) || mb_strlen($title) < 3) {
            $urlTitle = $this->extractTitleFromUrl($url, $coverId);
            if (!empty($urlTitle) && mb_strlen($urlTitle) >= 3) {
                $title = $urlTitle;
            }
        }

        if ($title && $this->isExpiredTitle($title)) {
            $isExpired = true;
        }

        return [
            'title'        => $title,
            'cover'        => $cover,
            'url'          => $url,
            'platform'     => $this->platformName,
            'episode_info' => $episodeInfo,
            'video_id'     => $videoId,
            'cover_id'     => $coverId,
            'is_expired'   => $isExpired,
        ];
    }

    /**
     * 调用腾讯视频多个 API 获取标题与封面
     * @param string $videoId
     * @param string $coverId
     * @param string $originalUrl
     * @return array|null ['title' => '', 'cover' => '']
     */
    private function fetchVideoInfoFromApi($videoId, $coverId = '', $originalUrl = '')
    {
        if (empty($videoId)) {
            return null;
        }

        $result = ['title' => null, 'cover' => null];

        $apiUrls = [
            // float_vinfo2 接口
            'https://node.video.qq.com/x/api/float_vinfo2?vid=' . urlencode($videoId),
            // pbaccess GetCmsVidInfoAll 接口
            'https://pbaccess.video.qq.com/trpc.vidplay.vidplay_2_0_fcgi.VidPlay2_0Fcgi/GetCmsVidInfoAll?data=' . urlencode(json_encode([
                'vid'      => $videoId,
                'appVer'   => '3.5.57',
                'platform' => '40000',
            ])),
        ];

        // HTML 页面作为兜底来源
        if (!empty($coverId)) {
            $apiUrls[] = 'https://v.qq.com/x/cover/' . urlencode($coverId) . '.html';
            $apiUrls[] = 'https://v.qq.com/x/cover/' . urlencode($coverId) . '/' . urlencode($videoId) . '.html';
        } else {
            $apiUrls[] = 'https://v.qq.com/x/page/' . urlencode($videoId) . '.html';
        }
        if (!empty($originalUrl)) {
            $apiUrls[] = $originalUrl;
        }

        // JSON 数据中标题的可能路径
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

        // JSON 数据中封面的可能路径
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
                if (!$response) {
                    continue;
                }

                $isHtml = stripos($apiUrl, '.html') !== false || stripos($response, '<html') !== false && stripos($response, '{') === false;
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

                $data = $this->safeJsonDecode($response);
                if (!$data) {
                    continue;
                }

                foreach ($titlePaths as $path) {
                    $val = $data;
                    foreach ($path as $key) {
                        if (!is_array($val) || !isset($val[$key])) {
                            $val = null;
                            break;
                        }
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
                        if (!is_array($val) || !isset($val[$key])) {
                            $val = null;
                            break;
                        }
                        $val = $val[$key];
                    }
                    if (is_string($val) && preg_match('/\.(jpg|jpeg|png|webp|gif)/i', $val) && empty($result['cover'])) {
                        $result['cover'] = $val;
                        break;
                    }
                }

                // 递归兜底查找
                if (empty($result['title']) || empty($result['cover'])) {
                    $found = $this->findTitleInData($data);
                    if (!empty($found['title']) && mb_strlen($found['title']) >= 3 && empty($result['title'])) {
                        $result['title'] = $found['title'];
                    }
                    if (!empty($found['cover']) && empty($result['cover'])) {
                        $result['cover'] = $found['cover'];
                    }
                }

                if (!empty($result['title'])) {
                    break;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return $result;
    }

    /**
     * 构建搜索关键词
     * 包含：标题、去标点版本、主标题，以及季数等组合。
     * @param array $videoInfo fetchVideoInfo 返回的视频信息
     * @return array
     */
    public function buildSearchKeywords($videoInfo)
    {
        $keywords = [];
        $baseTitle = $videoInfo['base_title'] ?? '';
        $originalTitle = $videoInfo['title'] ?? '';
        $seasonNum = $videoInfo['season_num'] ?? null;
        $version = $videoInfo['version'] ?? '';
        $part = $videoInfo['part'] ?? '';
        $videoId = $videoInfo['video_id'] ?? '';

        if (!empty($baseTitle)) {
            $keywords[] = $baseTitle;

            $normalizedBase = $this->normalizeTitle($baseTitle);
            if ($normalizedBase && $normalizedBase !== $baseTitle) {
                $keywords[] = $normalizedBase;
            }

            // 去标点版本
            $noPunctTitle = preg_replace('/[:：,，。、\s]+/u', '', $baseTitle);
            if ($noPunctTitle && $noPunctTitle !== $baseTitle) {
                $keywords[] = $noPunctTitle;
            }

            // 主标题（冒号、破折号前的部分）
            if (preg_match('/^(.+?)[:：——]/u', $baseTitle, $mainMatch)) {
                $mainTitle = trim($mainMatch[1]);
                if (mb_strlen($mainTitle) >= 2 && $mainTitle !== $baseTitle) {
                    $keywords[] = $mainTitle;
                }
            }

            // 季数组合
            if ($seasonNum) {
                $cnNum = $this->numberToChinese($seasonNum);
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '季';
                $keywords[] = $baseTitle . '第' . $seasonNum . '季';
                if ($cnNum) {
                    $keywords[] = $baseTitle . ' 第' . $cnNum . '季';
                    $keywords[] = $baseTitle . '第' . $cnNum . '季';
                }
                $keywords[] = $baseTitle . ' S' . $seasonNum;
                $keywords[] = $baseTitle . ' 第' . $seasonNum . '部';
            }

            if (!empty($part)) {
                $keywords[] = $baseTitle . ' ' . $part;
            }
            if (!empty($version)) {
                $keywords[] = $baseTitle . ' ' . $version;
            }
        }

        if (!empty($originalTitle) && $originalTitle !== $baseTitle) {
            $keywords[] = $originalTitle;
            $normalizedOrig = $this->normalizeTitle($originalTitle);
            if ($normalizedOrig && $normalizedOrig !== $originalTitle && !in_array($normalizedOrig, $keywords, true)) {
                $keywords[] = $normalizedOrig;
            }
        }

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
     * 腾讯特定匹配评分算法
     * 标题精确匹配权重高，季数匹配加分，预告/片花等扣分。
     * @param array $videoInfo 官方视频信息
     * @param array $candidate 资源站候选项
     * @return float 0-100
     */
    public function calculateMatchScore($videoInfo, $candidate)
    {
        $officialTitle = $this->cleanTitle($videoInfo['title'] ?? '');
        $officialBase = $videoInfo['base_title'] ?? $officialTitle;

        $candidateTitleRaw = $candidate['title'] ?? ($candidate['name'] ?? '');
        $candidateTitle = $this->cleanTitle($candidateTitleRaw);

        if (empty($officialTitle) || empty($candidateTitle)) {
            return 0.0;
        }

        $score = 0.0;

        // 标题精确匹配权重最高
        if ($officialTitle === $candidateTitle) {
            $score = 100.0;
        } elseif (mb_strtolower($officialTitle) === mb_strtolower($candidateTitle)) {
            $score = 98.0;
        } else {
            $score = (float) $this->calculateBaseScore($officialTitle, $candidateTitle);
            // 基础标题再比较一次
            if (!empty($officialBase) && $officialBase !== $officialTitle) {
                $baseScore = (float) $this->calculateBaseScore($officialBase, $candidateTitle);
                if ($baseScore > $score) {
                    $score = $baseScore;
                }
            }
            // 归一化标题比较
            $normOfficial = $this->normalizeTitle($officialTitle);
            $normCandidate = $this->normalizeTitle($candidateTitle);
            if ($normOfficial && $normCandidate && $normOfficial !== $officialTitle) {
                $normScore = (float) $this->calculateBaseScore($normOfficial, $normCandidate);
                if ($normScore > $score) {
                    $score = $normScore;
                }
            }
        }

        // 季数匹配加分
        $officialSeason = $videoInfo['season_num'] ?? null;
        $candidateSeason = $this->extractSeasonFromTitle($candidateTitle);
        if ($officialSeason && $candidateSeason) {
            if ((int) $officialSeason === (int) $candidateSeason) {
                $score = min(100.0, $score + 10.0);
            } else {
                $score = max(0.0, $score - 15.0);
            }
        }

        // 集数匹配加分
        $officialEpisode = $videoInfo['episode_num'] ?? null;
        $candidateEpisode = $this->extractEpisodeFromTitle($candidateTitle);
        if ($officialEpisode && $candidateEpisode && (int) $officialEpisode === (int) $candidateEpisode) {
            $score = min(100.0, $score + 5.0);
        }

        // 排除预告/片花/解说等非正片内容
        $excludePatterns = [
            '/预告片/i',
            '/预告/i',
            '/片花/i',
            '/花絮/i',
            '/解说/i',
            '/速看/i',
            '/混剪/i',
            '/剪辑/i',
            '/盘点/i',
            '/reaction/i',
            '/片头曲/i',
            '/片尾曲/i',
            '/主题曲/i',
            '/MV/i',
        ];
        foreach ($excludePatterns as $pattern) {
            if (preg_match($pattern, $candidateTitleRaw)) {
                $score = max(0.0, $score - 30.0);
                break;
            }
        }

        return round($score, 2);
    }

    /**
     * 获取腾讯视频特定的去广告规则
     * 识别片头广告、片中插播等。
     * @return array
     */
    public function getAdRules()
    {
        return [
            'platform'    => $this->platformId,
            'description' => '腾讯视频去广告规则：识别片头广告、片中插播及广告片段 URI 特征',
            'rules'       => [
                [
                    'name'        => 'tencent-preroll-ad',
                    'category'    => 'pre_roll',
                    'description' => '片头广告：正片开始前的广告片段，通常位于分片序列头部',
                    'patterns'    => [
                        '/\/ad[_-]?preroll/i',
                        '/\/preroll/i',
                        '/\/ads\//i',
                        '/\/adzone/i',
                        '/ad_time/i',
                        '/\/gg[_-]?p/i',
                    ],
                    'action'      => 'remove',
                    'weight'      => 95,
                ],
                [
                    'name'        => 'tencent-midroll-ad',
                    'category'    => 'mid_roll',
                    'description' => '片中插播：正片播放过程中插入的广告片段，伴随序列号跳跃或 CUE 标记',
                    'patterns'    => [
                        '/\/mid[_-]?roll/i',
                        '/\/adjump\//i',
                        '/\/commercial/i',
                        '/\/promo/i',
                        '/\/sponsor/i',
                        '/\/advert/i',
                    ],
                    'action'      => 'remove',
                    'weight'      => 90,
                ],
                [
                    'name'        => 'tencent-ad-uri-pattern',
                    'category'    => 'pattern',
                    'description' => '广告 URI 特征：腾讯 CDN 广告资源路径与广告标记参数',
                    'patterns'    => [
                        '/\/advertisement\//i',
                        '/[?&]ad[_-]?(id|type|tag)=/i',
                        '/\/post[_-]?roll/i',
                        '/adzone/i',
                        '/\/adservice/i',
                    ],
                    'action'      => 'remove',
                    'weight'      => 85,
                ],
                [
                    'name'        => 'tencent-sequence-jump',
                    'category'    => 'marker',
                    'description' => '序列号异常跳跃：分片序号非连续递增，疑似广告插播点',
                    'patterns'    => [],
                    'action'      => 'mark',
                    'weight'      => 70,
                ],
                [
                    'name'        => 'tencent-short-segment',
                    'category'    => 'duration',
                    'description' => '短时分片：时长显著短于正片分片的疑似广告片段',
                    'patterns'    => [],
                    'action'      => 'mark',
                    'weight'      => 60,
                ],
            ],
        ];
    }

    /**
     * 清理腾讯视频特定的标题后缀
     * 处理 "_腾讯视频"、"- 腾讯视频" 等后缀
     * @param string $title
     * @return string|null
     */
    public function cleanTitle($title)
    {
        $title = trim((string) $title);
        if ($title === '') {
            return null;
        }

        // 提取书名号/引号内标题
        if (preg_match('/^《([^《》]+)》/u', $title, $m)) {
            $title = $m[1];
        } elseif (preg_match('/^"([^"]+)"/', $title, $m)) {
            $title = $m[1];
        }

        // 腾讯视频特定后缀清理
        $tencentSuffixPatterns = [
            '/_腾讯视频/iu',
            '/-?\s*腾讯视频/iu',
            '/_QQ视频/iu',
            '/-?\s*QQ视频/iu',
            '/\|\s*腾讯视频/iu',
            '/—\s*腾讯视频/iu',
            '/_v\.qq\.com/iu',
            '/-?\s*v\.qq\.com/iu',
        ];
        foreach ($tencentSuffixPatterns as $pattern) {
            $title = preg_replace($pattern, '', $title);
        }

        // 通用后缀描述
        $title = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/高清.*?$/u', '', $title);
        $title = preg_replace('/完整版.*?$/u', '', $title);
        $title = preg_replace('/最新一期.*?$/u', '', $title);
        $title = preg_replace('/第.*?期.*?$/u', '', $title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|·");

        // 仅剩平台名视为无效
        $invalidTitles = ['腾讯视频', 'QQ视频', 'v.qq.com'];
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

    /* ---------------------------------------------------------------------
     * 辅助方法
     * ------------------------------------------------------------------- */

    /**
     * 从 HTML 中提取集数信息
     * @param string $html
     * @return array
     */
    private function extractEpisodeFromHtml($html)
    {
        $info = [
            'episode_num'    => null,
            'episode_name'   => '',
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

        if (preg_match('/(?:共|全)\s*(\d+)\s*集/u', $html, $m)) {
            $info['total_episodes'] = (int) $m[1];
        }

        return $info;
    }

    /**
     * 从 URL 中提取兜底标题（cover_id 等无法直接得到标题，仅作占位）
     * @param string $url
     * @param string $coverId
     * @return string
     */
    private function extractTitleFromUrl($url, $coverId = '')
    {
        if (!empty($coverId)) {
            return '';
        }
        return '';
    }

    /**
     * 判断标题是否表示视频已失效
     * @param string $title
     * @return bool
     */
    private function isExpiredTitle($title)
    {
        $expiredKeywords = [
            '那条视频不见了', '视频不存在', '视频已删除', '视频已下架',
            '视频失效', '链接失效', '该视频不存在', '该视频已删除',
            '该视频已下架', '无法找到该视频', '抱歉，该视频',
            '视频无法播放', '已失效', 'invalid', 'not found',
        ];
        $lower = mb_strtolower((string) $title);
        foreach ($expiredKeywords as $kw) {
            if (stripos($title, $kw) !== false || stripos($lower, mb_strtolower($kw)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 在嵌套数据中递归查找标题与封面
     * @param array $data
     * @return array ['title' => null, 'cover' => null]
     */
    private function findTitleInData($data)
    {
        $result = ['title' => null, 'cover' => null];
        if (!is_array($data)) {
            return $result;
        }

        $titleKeys = ['title', 'name', 'ti', 'videoName', 'video_title', 'vidName', 'subTitle', 'mainTitle', 'tvName'];
        $coverKeys = ['cover', 'pic', 'image', 'imageUrl', 'poster', 'thumb', 'thumbnail', 'vpic'];

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
     * 标题归一化（优先使用 TitleNormalizer，缺失则降级）
     * @param string $title
     * @return string
     */
    private function normalizeTitle($title)
    {
        if (class_exists('TitleNormalizer')) {
            try {
                return TitleNormalizer::normalize($title);
            } catch (Throwable $e) {
                // 降级到内置实现
            }
        }
        $title = preg_replace('/\s+/', ' ', (string) $title);
        $title = preg_replace('/[:：,，。、]+/u', '', $title);
        return trim($title);
    }

    /**
     * 从标题中提取季数
     * @param string $title
     * @return int|null
     */
    private function extractSeasonFromTitle($title)
    {
        $title = (string) $title;
        if ($title === '') {
            return null;
        }

        $patterns = [
            '/第\s*([0-9]+)\s*季/u',
            '/第\s*([一二三四五六七八九十]+)\s*季/u',
            '/Season\s*(\d+)/i',
            '/\bS(\d{1,2})\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $title, $m)) {
                $num = $this->chineseToNumber($m[1]);
                if ($num !== null) {
                    return $num;
                }
            }
        }

        // Ⅱ / Ⅲ 罗马数字
        if (preg_match('/Ⅱ/u', $title)) {
            return 2;
        }
        if (preg_match('/Ⅲ/u', $title)) {
            return 3;
        }

        return null;
    }

    /**
     * 从标题中提取集数
     * @param string $title
     * @return int|null
     */
    private function extractEpisodeFromTitle($title)
    {
        $title = (string) $title;
        if ($title === '') {
            return null;
        }

        $patterns = [
            '/第\s*([0-9]+)\s*集/u',
            '/第\s*([一二三四五六七八九十百]+)\s*集/u',
            '/EP\s*(\d+)/i',
            '/\bE\s*(\d+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $title, $m)) {
                $num = $this->chineseToNumber($m[1]);
                if ($num !== null) {
                    return $num;
                }
            }
        }

        return null;
    }

    /**
     * 数字转中文（0-10）
     * @param int $num
     * @return string|null
     */
    private function numberToChinese($num)
    {
        $cnNumbers = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九', '十'];
        $num = (int) $num;
        if ($num >= 0 && $num <= 10) {
            return $cnNumbers[$num];
        }
        return null;
    }

    /**
     * 中文/阿拉伯数字转整数
     * @param string $str
     * @return int|null
     */
    private function chineseToNumber($str)
    {
        if ($str === '' || $str === null) {
            return null;
        }
        $str = (string) $str;

        if (ctype_digit($str)) {
            return (int) $str;
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
}
