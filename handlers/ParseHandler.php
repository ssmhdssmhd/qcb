<?php
/**
 * ParseHandler —— 统一解析入口（parse / parse/info / jx / jx/info）
 *
 * action：parse / parse/parse / jx / parse/info / jx/info
 * 迁移自 mx.php 的 parse_internal_unified。
 * 行为：
 *   ① 自动识别 URL 类型（缓存 m3u8 / 直链 m3u8 / 官方平台）
 *   ② 按 type 分发：cache → kz 缓存解析；mxjx → 去广告；
 *      xiami → 虾米官解；moxi → 沫兮（资源站优先）；official → 官替
 *   ③ 全程全局预算保护（Timer），绝不卡死
 *
 * @package handlers
 * @since   5.14.0
 */
class ParseHandler extends BaseHandler {

    /** 官方平台域名清单 */
    const OFFICIAL_DOMAINS = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];

    /** 入口：parse / parse/parse / jx / parse/info / jx/info */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut([
                'success' => false,
                'code' => 400,
                'message' => '缺少 url 参数',
            ], 400);
        }

        $parseType = $this->param('type', 'parse');
        $result = $this->parse($url, $parseType);
        $this->jsonOut($result);
    }

    /**
     * 统一解析核心（返回数组，供 parse / parse/info 共用）
     *
     * @param string $url
     * @param string $parseType parse|auto|智能|cache|mxjx|xiami|moxi|official|...
     * @return array
     */
    public function parse($url, $parseType = 'parse') {
        $selfUrl = SelfUrlHelper::base();
        $parsedUrl = parse_url($url);
        $urlHost = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '';

        $isOfficialUrl = false;
        foreach (self::OFFICIAL_DOMAINS as $domain) {
            if (strpos($urlHost, $domain) !== false) {
                $isOfficialUrl = true;
                break;
            }
        }
        $isM3u8Url = (stripos($path, '.m3u8') !== false);
        $isCacheM3u8 = class_exists('CacheM3u8Parser') ? CacheM3u8Parser::isCacheM3u8($url) : false;

        if ($parseType === 'parse' || $parseType === 'auto' || $parseType === '智能') {
            if ($isCacheM3u8) {
                $parseType = 'cache';
            } elseif ($isM3u8Url) {
                $parseType = 'mxjx';
            } elseif ($isOfficialUrl) {
                $parseType = 'xiami';
            } else {
                $parseType = 'mxjx';
            }
        }

        $playUrl = '';
        $videoName = '';
        $msg = '';
        $code = 200;
        $typeName = '';
        $extra = [];

        switch ($parseType) {
            case 'cache':
            case '缓存解析':
                $playUrl = $selfUrl . '/kz/cache.php?url=' . urlencode($url);
                $msg = '缓存型M3U8解析';
                $typeName = '缓存型M3U8解析';
                $extra['is_cache_m3u8'] = true;
                break;

            case 'mxjx':
            case 'adskip':
            case '去广告':
                $playUrl = SelfUrlHelper::mxjxUrl($url);
                $msg = '去广告解析';
                $typeName = '去广告解析';
                break;

            case 'xiami':
            case '虾米':
            case '虾米解析':
                $xiamiHandler = new XiamiJxHandler($this->ctx);
                $xiamiResult = $xiamiHandler->api($url);
                if (!empty($xiamiResult['success'])) {
                    $playUrl = $xiamiResult['play_url'];
                    $videoName = '';
                    $msg = '虾米解析成功';
                    $extra = $xiamiResult;
                } else {
                    $code = 500;
                    $msg = $xiamiResult['message'] ?? '虾米解析失败';
                }
                $typeName = '虾米解析';
                break;

            case 'moxi':
            case '沫兮':
            case '沫兮解析':
                $moxiHandler = new MoxiHandler($this->ctx);
                $moxiResult = $moxiHandler->resolve($url);
                if (!empty($moxiResult['success'])) {
                    $playUrl = $moxiResult['play_url'];
                    $videoName = $moxiResult['video_name'] ?? '';
                    $msg = '沫兮解析成功';
                    $extra = $moxiResult;
                } else {
                    $code = 500;
                    $msg = $moxiResult['message'] ?? '沫兮解析失败';
                }
                $typeName = '沫兮解析';
                break;

            case 'official':
            case '官替':
            case '官方替换':
                $orResult = $this->resolveOfficial($url);
                if (!empty($orResult['success'])) {
                    $m3u8Url = $orResult['m3u8_url'] ?? '';
                    $playUrl = $m3u8Url;
                    $videoName = $orResult['video_title'] ?? '';
                    $msg = '官方替换成功';
                    $extra = $orResult;
                } else {
                    $code = 500;
                    $msg = $orResult['message'] ?? '未找到匹配资源';
                }
                $typeName = '官方替换';
                break;

            default:
                $code = 400;
                $msg = '不支持的解析类型: ' . $parseType;
                $typeName = '未知类型';
                break;
        }

        $response = [
            'success' => ($code == 200),
            'code' => $code,
            'message' => $msg,
            'type' => $parseType,
            'type_name' => $typeName,
            'original_url' => $url,
            'play_url' => $playUrl,
            'video_name' => $videoName,
            'is_official' => $isOfficialUrl,
            'is_m3u8' => $isM3u8Url,
        ];
        if (!empty($extra)) {
            $response['raw'] = $extra;
        }
        return $response;
    }
}
