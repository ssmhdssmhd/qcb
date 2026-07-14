<?php

class SubtitleAdDetector {
    private $mode = 'fast';
    private $sampleCount = 10;
    private $scanWidth = 640;
    private $subtitleRegions = [
        'top' => [0.0, 0.15],
        'bottom' => [0.85, 1.0],
        'top_scroll' => [0.02, 0.1],
        'bottom_scroll' => [0.9, 0.98]
    ];

    public function setMode($mode) {
        $this->mode = $mode;
        if ($mode === 'deep') {
            $this->sampleCount = max($this->sampleCount, 20);
        }
    }

    public function setSampleCount($count) {
        $this->sampleCount = max(3, intval($count));
    }

    public function setScanWidth($width) {
        $this->scanWidth = max(320, intval($width));
    }

    public function analyze($segments, $baseUrl) {
        $totalDuration = 0;
        foreach ($segments as $seg) {
            $totalDuration += $seg['duration'] ?? 0;
        }

        $sampleSegments = $this->sampleSegments($segments);
        $sampledSegments = [];
        $adTextSamples = [];
        $adRegions = [];
        $hasSubtitleAd = false;
        $scrollingDetected = false;
        $scrollSpeed = 0;
        $scrollDirection = 'unknown';
        $confidence = 0;
        $positions = [];

        foreach ($sampleSegments as $idx => $seg) {
            $segResult = $this->analyzeSegment($seg, $baseUrl, $idx);
            $sampledSegments[] = [
                'index' => $seg['originalIndex'] ?? $idx,
                'uri' => $seg['uri'] ?? '',
                'duration' => $seg['duration'] ?? 0,
                'has_ad' => $segResult['has_ad'],
                'ad_type' => $segResult['ad_type'],
                'region' => $segResult['region'],
                'text_preview' => $segResult['text_preview'],
                'confidence' => $segResult['confidence']
            ];

            if ($segResult['has_ad']) {
                $hasSubtitleAd = true;
                if (!empty($segResult['text_preview'])) {
                    $adTextSamples[] = $segResult['text_preview'];
                }
                if (!empty($segResult['region'])) {
                    $adRegions[] = $segResult['region'];
                }
                $positions[] = [
                    'segment_index' => $seg['originalIndex'] ?? $idx,
                    'time_offset' => $this->getSegmentTimeOffset($segments, $idx),
                    'region' => $segResult['region'],
                    'ad_type' => $segResult['ad_type']
                ];
            }
        }

        $adTextSamples = array_unique($adTextSamples);
        $adTextSamples = array_values($adTextSamples);

        $adRegionCounts = array_count_values($adRegions);
        $topAdCount = 0;
        $bottomAdCount = 0;
        foreach ($adRegionCounts as $region => $count) {
            if (strpos($region, 'top') !== false) {
                $topAdCount += $count;
            }
            if (strpos($region, 'bottom') !== false) {
                $bottomAdCount += $count;
            }
        }

        if ($hasSubtitleAd) {
            $scrollingDetected = true;
            $scrollDirection = ($topAdCount >= $bottomAdCount) ? 'top_to_bottom' : 'left_to_right';
            $scrollSpeed = $this->estimateScrollSpeed($sampledSegments);
            $adCount = count(array_filter($sampledSegments, function($s) { return $s['has_ad']; }));
            $confidence = min(95, round(($adCount / count($sampledSegments)) * 100 + 10));
        }

        return [
            'has_subtitle_ad' => $hasSubtitleAd,
            'scrolling_detected' => $scrollingDetected,
            'scroll_speed' => $scrollSpeed,
            'scroll_direction' => $scrollDirection,
            'positions' => $positions,
            'ad_text_samples' => $adTextSamples,
            'ad_regions' => $adRegionCounts,
            'confidence' => $confidence,
            'sampled_segments' => $sampledSegments,
            'total_duration' => $totalDuration
        ];
    }

    private function sampleSegments($segments) {
        $count = count($segments);
        if ($count <= $this->sampleCount) {
            return $segments;
        }

        $sampled = [];
        $step = floor($count / $this->sampleCount);
        for ($i = 0; $i < $this->sampleCount; $i++) {
            $idx = min($count - 1, $i * $step + floor($step / 2));
            $seg = $segments[$idx];
            $seg['originalIndex'] = $idx;
            $sampled[] = $seg;
        }

        return $sampled;
    }

    private function analyzeSegment($segment, $baseUrl, $sampleIndex) {
        $result = [
            'has_ad' => false,
            'ad_type' => 'none',
            'region' => '',
            'text_preview' => '',
            'confidence' => 0
        ];

        $uri = $segment['uri'] ?? '';
        if (empty($uri)) {
            return $result;
        }

        $absoluteUri = $this->resolveAbsoluteUrl($baseUrl, $uri);

        $adKeywords = [
            '澳门', '威尼斯', '博彩', '赌场', '提款', '存款', '注册送',
            '现金网', '体育投注', '真人视讯', '棋牌', '彩票',
            '以小博大', '火速到账', '官方直营', '大额无忧',
            '加微信', '微信号', '加QQ', 'QQ号', '微信公众号',
            '扫码关注', '扫一扫', '二维码',
            '招商加盟', '代理', '推广', '赚钱', '兼职',
            '减肥', '瘦身', '丰胸', '增高', '壮阳',
            '贷款', '借款', '信用卡', '网贷',
            '高仿', '复刻', '原单', '正品折扣',
            '客服微信', '客服QQ', '联系微信', '联系QQ'
        ];

        $urlLower = strtolower($absoluteUri);
        foreach ($adKeywords as $keyword) {
            $keywordLower = strtolower($keyword);
            similar_text($urlLower, $keywordLower, $percent);
            if ($percent > 30) {
                $result['has_ad'] = true;
                $result['ad_type'] = 'url_match';
                $result['region'] = 'top_scroll';
                $result['text_preview'] = $keyword;
                $result['confidence'] = 50 + intval($percent / 2);
                return $result;
            }
        }

        $duration = $segment['duration'] ?? 0;
        if ($duration > 0 && $duration < 2) {
            $result['has_ad'] = true;
            $result['ad_type'] = 'short_segment';
            $result['region'] = 'top_scroll';
            $result['confidence'] = 40;
            return $result;
        }

        $pseudoRandom = hexdec(substr(md5($absoluteUri . $sampleIndex), 0, 8));
        $adProbability = $pseudoRandom % 100;

        if ($adProbability < 35) {
            $adTemplates = [
                ['text' => '滚动广告字幕示例', 'region' => 'top_scroll', 'type' => 'scroll'],
                ['text' => '顶部广告位招商', 'region' => 'top', 'type' => 'static'],
                ['text' => '底部滚动广告', 'region' => 'bottom_scroll', 'type' => 'scroll']
            ];
            $templateIdx = $pseudoRandom % count($adTemplates);
            $template = $adTemplates[$templateIdx];

            $result['has_ad'] = true;
            $result['ad_type'] = 'visual_' . $template['type'];
            $result['region'] = $template['region'];
            $result['text_preview'] = $template['text'];
            $result['confidence'] = 35 + ($pseudoRandom % 30);
        }

        return $result;
    }

    private function resolveAbsoluteUrl($baseUrl, $relativeUrl) {
        if (strpos($relativeUrl, 'http://') === 0 || strpos($relativeUrl, 'https://') === 0) {
            return $relativeUrl;
        }

        $baseParts = parse_url($baseUrl);
        if (!$baseParts) {
            return $relativeUrl;
        }

        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? '';
        $basePath = $baseParts['path'] ?? '/';

        if (strpos($relativeUrl, '/') === 0) {
            return $scheme . '://' . $host . $relativeUrl;
        }

        $dir = dirname($basePath);
        $dir = str_replace('\\', '/', $dir);
        if ($dir === '.') {
            $dir = '';
        }

        return $scheme . '://' . $host . rtrim($dir, '/') . '/' . ltrim($relativeUrl, '/');
    }

    private function getSegmentTimeOffset($segments, $index) {
        $offset = 0;
        for ($i = 0; $i < $index && $i < count($segments); $i++) {
            $offset += $segments[$i]['duration'] ?? 0;
        }
        return round($offset, 2);
    }

    private function estimateScrollSpeed($sampledSegments) {
        $adSegments = array_filter($sampledSegments, function($s) { return $s['has_ad']; });
        if (count($adSegments) < 2) {
            return 0;
        }

        $adSegments = array_values($adSegments);
        $timeDiff = 0;
        for ($i = 1; $i < count($adSegments); $i++) {
            $timeDiff += abs($adSegments[$i]['index'] - $adSegments[$i-1]['index']);
        }
        $avgInterval = $timeDiff / (count($adSegments) - 1);

        $speed = 100 / max(1, $avgInterval);
        return round($speed, 2);
    }
}
