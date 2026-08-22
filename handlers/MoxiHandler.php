<?php
/**
 * MoxiHandler —— 沫兮解析（官方视频替换 + M3U8 去广告）
 *
 * action：moxi / moxi/api
 * 迁移自 mx.php 的 parse_internal_moxi + moxi case。
 *
 * 重构要点（对应用户诉求）：
 *   ① 官方平台链接 → 资源站优先（官替）取可播放 m3u8，全程全局预算保护，绝不卡死；
 *   ② 直接 M3U8 链接 → 本地标题/集数提取 + 可选资源站标题匹配，再交给 mxjx 去广告；
 *   ③ 输出保持旧 moxi 结构（code/url/msg/jm/js/time/kfz），前端无感。
 *
 * @package handlers
 * @since   5.14.0
 */
class MoxiHandler extends BaseHandler {

    /** 官方平台域名清单 */
    const OFFICIAL_DOMAINS = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];

    /** 入口：moxi / moxi/api */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut([
                'code' => 400,
                'url' => '',
                'msg' => '缺少 url 参数',
                'jm' => '',
                'js' => '',
                'time' => date('Y-m-d H:i:s'),
                'kfz' => '沫兮API',
            ], 400);
        }

        $result = $this->resolve($url);

        $this->jsonOut([
            'code' => $result['code'],
            'url' => $result['play_url'],
            'msg' => $result['play_url'] ?: $result['message'],
            'jm' => $result['video_name'],
            'js' => $result['episode'] ?: '正片',
            'time' => date('Y-m-d H:i:s'),
            'kfz' => '沫兮API - 在线视频解析',
        ], $result['code']);
    }

    /**
     * 沫兮解析核心（返回数组，供 moxi / parse / parse/info 共用）
     *
     * @param string $url
     * @return array{
     *   success:bool, code:int, message:string, play_url:string,
     *   video_name:string, episode:string, original_url:string,
     *   is_official:bool, source:string
     * }
     */
    public function resolve($url) {
        $timer = $this->newTimer();
        $isOfficial = $this->isOfficialUrl($url);

        $playUrl = '';
        $jm = '';
        $js = '';
        $code = 200;
        $msg = '解析成功';

        if ($isOfficial) {
            $this->resolveOfficialPath($url, $timer, $playUrl, $jm, $js);
        } else {
            $this->resolveDirectPath($url, $timer, $playUrl, $jm, $js);
        }

        return [
            'success' => true,
            'code' => $code,
            'message' => $msg,
            'play_url' => $playUrl,
            'video_name' => $jm,
            'episode' => $js,
            'original_url' => $url,
            'is_official' => $isOfficial,
            'source' => 'moxi',
        ];
    }

    /** 是否官方平台域名 */
    protected function isOfficialUrl($url) {
        $urlHost = (string)parse_url($url, PHP_URL_HOST);
        foreach (self::OFFICIAL_DOMAINS as $domain) {
            if (strpos($urlHost, $domain) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 官方链接：资源站优先（带预算），失败回退到直接 mxjx
     */
    protected function resolveOfficialPath($url, $timer, &$playUrl, &$jm, &$js) {
        // 首选 parse/ 门面（资源站优先 + 预算保护 + 归一化输出）
        $result = $this->facade($url, ['force_channel' => 'official_replace', 'skip_clean' => true]);
        $m3u8Url = '';
        $title = '';
        $episode = '';

        if ($result !== null && $result->success && !empty($result->url)) {
            $m3u8Url = $result->url;
            $title = $result->title;
            $episode = $result->episode;
        } else {
            // 门面不可用或失败 → 回退旧链路（官替，仍带预算）
            $legacy = $this->resolveOfficial($url);
            if (!empty($legacy['success']) && !empty($legacy['m3u8_url'])) {
                $m3u8Url = $legacy['m3u8_url'];
                $title = $legacy['video_title'] ?? '';
                $episode = $legacy['target_episode'] ?? ($legacy['episode'] ?? '');
            } else {
                $playUrl = SelfUrlHelper::mxjxUrl($url);
                $jm = $legacy['video_title'] ?? TitleExtractor::title($url);
                $js = $legacy['episode'] ?? TitleExtractor::episode($url);
                return;
            }
        }

        $playUrl = SelfUrlHelper::mxjxUrl($m3u8Url, true);
        $jm = $title;
        $js = $episode ?: '正片';
    }

    /**
     * 直接 M3U8 / 其它链接：本地标题提取 + 可选资源站标题匹配，再交给 mxjx 去广告
     */
    protected function resolveDirectPath($url, $timer, &$playUrl, &$jm, &$js) {
        $playUrl = SelfUrlHelper::mxjxUrl($url);
        $jm = TitleExtractor::title($url);
        $js = TitleExtractor::episode($url);

        $searchKeyword = TitleExtractor::searchKeyword($url);
        if (empty($searchKeyword) || $searchKeyword === $jm || !$this->ctx->siteManager) {
            return;
        }

        // 资源站标题匹配（带预算保护，超预算直接放弃）
        if (!$timer->ok()) {
            return;
        }
        try {
            $searchResult = $this->ctx->siteManager->searchAllSites($searchKeyword, 3, 5);
            if (empty($searchResult['success']) || empty($searchResult['results'])) {
                return;
            }
            $bestMatch = null;
            $bestScore = 0;
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            foreach ($searchResult['results'] as $siteResult) {
                if (empty($siteResult['videos'])) continue;
                foreach ($siteResult['videos'] as $video) {
                    $videoName = $video['name'] ?? '';
                    if (empty($videoName)) continue;
                    $score = 0;
                    similar_text($searchKeyword, $videoName, $score);
                    $firstUrl = $video['first_url'] ?? $video['url'] ?? '';
                    if (!empty($firstUrl)) {
                        $firstUrlPath = parse_url($firstUrl, PHP_URL_PATH) ?? '';
                        $pathScore = 0;
                        similar_text($path, $firstUrlPath, $pathScore);
                        if ($pathScore > $score) $score = $pathScore;
                    }
                    if ($score > $bestScore && $score > 40) {
                        $bestScore = $score;
                        $bestMatch = $video;
                    }
                }
            }
            if ($bestMatch && $bestScore > 50) {
                $jm = $bestMatch['name'] ?? $jm;
                if (!empty($bestMatch['remarks'])) {
                    $js = $bestMatch['remarks'];
                }
            }
        } catch (\Throwable $e) {
            // 标题匹配失败不影响主链路
        }
    }
}
