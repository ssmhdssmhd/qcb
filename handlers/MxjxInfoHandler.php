<?php
/**
 * MxjxInfoHandler —— 去广告解析详情（mxjx/info）
 *
 * action：mxjx/info
 * 迁移自 mx.php 的 mxjx/info case。
 * 返回清理后的 M3U8 统计信息 + 播放地址；广告占比过高时自动回退原始 M3U8。
 *
 * @package handlers
 * @since   5.14.0
 */
class MxjxInfoHandler extends BaseHandler {

    /** 入口：mxjx/info */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut([
                'code' => 400,
                'success' => false,
                'message' => '缺少 url 参数',
            ], 400);
        }

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';
        $mediaUrl = M3u8UrlHelper::resolveMasterPlaylist($url);
        if ($mediaUrl !== $url) {
            $parsedUrl = parse_url($mediaUrl);
            $domain = $parsedUrl['host'] ?? '';
        }
        $url = $mediaUrl;

        $skipper = new M3U8AdSkipper();
        $reflection = new ReflectionClass($skipper);
        $ruleEngineProp = $reflection->getProperty('ruleEngine');
        $ruleEngineProp->setAccessible(true);

        $enhancedEngine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $enhancedEngine->setDomain($domain);
        $ruleEngineProp->setValue($skipper, $enhancedEngine);

        $filterProp = $reflection->getProperty('filter');
        $filterProp->setAccessible(true);
        $filter = $filterProp->getValue($skipper);

        $filterReflection = new ReflectionClass($filter);
        $filterEngineProp = $filterReflection->getProperty('ruleEngine');
        $filterEngineProp->setAccessible(true);
        $filterEngineProp->setValue($filter, $enhancedEngine);

        $result = $skipper->process($url);

        $stats = $result['stats'] ?? [];
        $adPercentage = $stats['adPercentage'] ?? 0;
        $safeguardTriggeredInfo = false;
        $safeguardReasonInfo = '';
        if ($adPercentage >= 90 && $stats['totalSegments'] > 10) {
            $safeguardTriggeredInfo = true;
            $safeguardReasonInfo = '广告占比过高 (' . round($adPercentage, 1) . '%)，可能存在误判，已回退原始M3U8';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            $newM3U8Content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($newM3U8Content === false || $httpCode !== 200) {
                $newM3U8Content = $result['output'];
                $safeguardTriggeredInfo = false;
            }
        } else {
            $newM3U8Content = $result['output'];
        }

        $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
        if ($isRemote) {
            $newM3U8Content = M3u8UrlHelper::rewriteRelativeUrls($newM3U8Content, $url);
        }

        $playUrl = SelfUrlHelper::mxjxUrl($mediaUrl);

        $stats = $result['stats'] ?? [];
        $hasRules = $enhancedEngine->getCurrentDomainRules() !== null;

        $this->jsonOut([
            'code' => 200,
            'success' => true,
            'message' => '解析成功',
            'data' => [
                'original_url' => $this->param('url', ''),
                'media_url' => $mediaUrl,
                'domain' => $domain,
                'play_url' => $playUrl,
                'has_domain_rules' => $hasRules,
                'safeguard_triggered' => $safeguardTriggeredInfo,
                'safeguard_reason' => $safeguardReasonInfo,
                'stats' => [
                    'total_segments' => $stats['totalSegments'] ?? 0,
                    'kept_segments' => $stats['keptSegments'] ?? 0,
                    'removed_segments' => $stats['removedSegments'] ?? 0,
                    'original_duration' => $stats['originalDuration'] ?? 0,
                    'filtered_duration' => $stats['filteredDuration'] ?? 0,
                    'saved_duration' => $stats['savedDuration'] ?? 0,
                    'ad_percentage' => $stats['adPercentage'] ?? 0,
                ],
            ],
        ]);
    }
}
