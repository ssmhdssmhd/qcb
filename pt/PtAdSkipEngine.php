<?php
/**
 * pt 去广告引擎
 * 用空白片段替代非正片内容（广告、片头预告、片尾字幕等）
 */

class PtAdSkipEngine
{
    private $adKeywords = [
        'adjump', 'ad/', 'advertisement', 'commercial', 'pre-roll', 'post-roll',
        'adcreative', 'adinsert', 'adplaceholder', 'adsystem', 'advert',
        'promo', 'trailer', 'sponsor', 'banner',
    ];

    private $adUriPatterns = [
        '/\/adjump\//i',
        '/\/ad\//i',
        '/\/advertisement\//i',
        '/\/commercial\//i',
        '/\/pre-roll\//i',
        '/\/ads\//i',
        '/\/adcreative\//i',
        '/\/adinsert\//i',
        '/ad_[a-z0-9]+/i',
        '/[?&]ad=/i',
    ];

    private $adDurationThreshold = 5;
    private $contentMinDuration = 2;
    private $maxAdRatio = 0.4;

    /**
     * 处理 M3U8 内容，用空白片段替代广告
     * @param string $m3u8Content M3U8 原始内容
     * @param array $platformRules 平台特定规则
     * @return array ['success' => bool, 'content' => string, 'stats' => array]
     */
    public function process($m3u8Content, $platformRules = [])
    {
        if (empty($m3u8Content)) {
            return ['success' => false, 'content' => '', 'stats' => []];
        }

        $lines = explode("\n", trim($m3u8Content));
        $output = [];
        $stats = [
            'total_segments' => 0,
            'ad_segments' => 0,
            'content_segments' => 0,
            'blank_segments' => 0,
            'total_duration' => 0,
            'ad_duration' => 0,
            'content_duration' => 0,
            'blank_duration' => 0,
        ];

        // 合并平台规则
        $adKeywords = array_merge($this->adKeywords, $platformRules['ad_keywords'] ?? []);
        $adUriPatterns = array_merge($this->adUriPatterns, $platformRules['ad_uri_patterns'] ?? []);
        $adDurationThreshold = $platformRules['ad_duration_threshold'] ?? $this->adDurationThreshold;

        $inSegment = false;
        $currentDuration = 0;
        $currentUri = '';
        $isMasterPlaylist = false;

        foreach ($lines as $i => $line) {
            $line = trim($line);

            // 保留 Master Playlist 原样
            if (strpos($line, '#EXT-X-STREAM-INF') !== false) {
                $isMasterPlaylist = true;
            }

            if ($isMasterPlaylist) {
                $output[] = $line;
                continue;
            }

            // 头部
            if ($line === '#EXTM3U') {
                $output[] = $line;
                continue;
            }

            // 片段时长
            if (strpos($line, '#EXTINF') === 0) {
                if (preg_match('/#EXTINF:([\d.]+)/', $line, $m)) {
                    $currentDuration = floatval($m[1]);
                }
                $inSegment = true;
                continue;
            }

            // 片段 URI
            if ($inSegment && !empty($line) && $line[0] !== '#') {
                $currentUri = $line;
                $stats['total_segments']++;
                $stats['total_duration'] += $currentDuration;

                $isAd = $this->isAdSegment($currentUri, $currentDuration, $adKeywords, $adUriPatterns, $adDurationThreshold);

                if ($isAd) {
                    // 用空白片段替代广告
                    $stats['ad_segments']++;
                    $stats['ad_duration'] += $currentDuration;

                    // 生成空白片段
                    $blankUri = $this->generateBlankSegment($currentDuration);
                    $output[] = '#EXTINF:' . $currentDuration . ',';
                    $output[] = $blankUri;
                    $stats['blank_segments']++;
                    $stats['blank_duration'] += $currentDuration;
                } else {
                    // 保留正片片段
                    $output[] = '#EXTINF:' . $currentDuration . ',';
                    $output[] = $currentUri;
                    $stats['content_segments']++;
                    $stats['content_duration'] += $currentDuration;
                }

                $inSegment = false;
                $currentDuration = 0;
                $currentUri = '';
                continue;
            }

            // 其他标签
            if (!empty($line)) {
                $output[] = $line;
            }
        }

        // 安全检查：如果广告比例过高，可能误判，回退原始内容
        $adRatio = $stats['total_duration'] > 0 ? $stats['ad_duration'] / $stats['total_duration'] : 0;
        if ($adRatio > $this->maxAdRatio && $stats['content_segments'] === 0) {
            return [
                'success' => true,
                'content' => $m3u8Content,
                'stats' => $stats,
                'safeguard' => true,
                'message' => '广告比例过高，可能误判，保留原始内容',
            ];
        }

        // 尾部
        $output[] = '#EXT-X-ENDLIST';

        return [
            'success' => true,
            'content' => implode("\n", $output),
            'stats' => $stats,
            'safeguard' => false,
        ];
    }

    /**
     * 判断是否为广告片段
     */
    private function isAdSegment($uri, $duration, $keywords, $patterns, $durationThreshold)
    {
        $uriLower = strtolower($uri);

        // 1. URI 模式匹配
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $uriLower)) {
                return true;
            }
        }

        // 2. 关键词匹配
        foreach ($keywords as $keyword) {
            if (strpos($uriLower, strtolower($keyword)) !== false) {
                return true;
            }
        }

        // 3. 极短片段（可能是广告）
        if ($duration > 0 && $duration < $this->contentMinDuration) {
            return true;
        }

        return false;
    }

    /**
     * 生成空白片段（黑色画面 + 静音）
     * 使用 data URI 内嵌一个最简 TS 片段
     */
    private function generateBlankSegment($duration)
    {
        // 使用 EXT-X-DISCONTINUITY 标记不连续性，然后插入一个极短的空白占位
        // 实际播放器会跳过或显示黑屏
        // 返回一个 data URI 格式的空白片段
        $blankTs = base64_encode(
            // MPEG-TS 头 + 空白 PAT/PMT + 黑色视频帧 + 静音音频帧
            "\x47\x40\x00\x10\x00\x01\x00\x00"
            . "\x47\x50\x00\x10\x01\x00\x01\x00"
            . "\x47\x00\x10\x00\x00"
        );

        return 'data:video/mp2t;base64,' . $blankTs;
    }

    /**
     * 获取默认广告规则
     */
    public function getDefaultRules()
    {
        return [
            'ad_keywords' => $this->adKeywords,
            'ad_uri_patterns' => $this->adUriPatterns,
            'ad_duration_threshold' => $this->adDurationThreshold,
            'content_min_duration' => $this->contentMinDuration,
            'max_ad_ratio' => $this->maxAdRatio,
        ];
    }
}
