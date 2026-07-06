<?php

require_once __DIR__ . '/M3U8Parser.php';
require_once __DIR__ . '/../gz/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/../gz/DomainRuleManager.php';

class AdAnalyzer {

    private $parser;
    private $engine;
    private $ruleManager;
    private $playlist;
    private $analysisResult;
    private $domain;
    private $prefixSum = [];

    public function __construct($options = []) {
        $this->parser = new M3U8Parser();
        $this->engine = new EnhancedAdRuleEngine($options);
        $this->ruleManager = new DomainRuleManager();
    }

    public function analyze($input, $options = []) {
        $autoLearn = $options['auto_learn'] ?? true;
        $detailed = $options['detailed'] ?? true;

        $url = '';
        if (is_string($input) && (strpos($input, 'http://') === 0 || strpos($input, 'https://') === 0)) {
            $url = $input;
            $parsedUrl = parse_url($url);
            $this->domain = $parsedUrl['host'] ?? '';
        }

        $this->playlist = $this->parser->parse($input);

        if (!empty($this->playlist['isMaster']) && !empty($this->playlist['variants'])) {
            $firstVariant = $this->playlist['variants'][0]['uri'] ?? '';
            if ($firstVariant && $url) {
                $mediaUrl = $this->resolveMediaUrl($url, $firstVariant);
                $this->playlist = $this->parser->parse($mediaUrl);
            }
        }

        if ($this->domain) {
            $this->engine->setDomain($this->domain);
        }

        $segments = $this->playlist['segments'] ?? [];
        $this->buildPrefixSum($segments);
        $this->analysisResult = $this->engine->analyzeAllSegments($segments);

        $result = [
            'success' => true,
            'domain' => $this->domain,
            'playlist' => [
                'version' => $this->playlist['version'] ?? 3,
                'targetDuration' => $this->playlist['targetDuration'] ?? 0,
                'mediaSequence' => $this->playlist['mediaSequence'] ?? 0,
                'discontinuitySequence' => $this->playlist['discontinuitySequence'] ?? 0,
                'isMaster' => !empty($this->playlist['isMaster']),
                'endlist' => !empty($this->playlist['endlist']),
                'totalSegments' => count($segments),
                'totalDuration' => $this->analysisResult['totalDuration'] ?? 0
            ],
            'summary' => $this->buildSummary(),
            'insertion_analysis' => $this->analysisResult['insertionPoints'] ?? [],
            'ad_type_analysis' => $this->analysisResult['adTypes'] ?? [],
            'psychological_analysis' => $this->analysisResult['psychologicalFeatures'] ?? [],
            'confidence' => $this->analysisResult['confidence'] ?? 0,
            'ad_markers' => [
                'discontinuity_count' => $this->analysisResult['discontinuityCount'] ?? 0,
                'cue_marker_count' => $this->analysisResult['cueMarkerCount'] ?? 0,
                'scte35_count' => $this->analysisResult['scte35Count'] ?? 0,
                'ad_tag_count' => $this->analysisResult['adTagCount'] ?? 0
            ],
            'ad_clusters' => $this->buildAdClusterDetails(),
            'duration_distribution' => $this->analysisResult['durationDistribution'] ?? [],
            'sequence_jumps' => $this->analysisResult['sequenceJumps'] ?? []
        ];

        if ($detailed) {
            $result['detailed_segments'] = $this->buildDetailedSegments();
        }

        if ($autoLearn && $this->domain && !empty($this->analysisResult['adCount'])) {
            $learnResult = $this->learnRules();
            $result['auto_learn'] = $learnResult;
        }

        return $result;
    }

    private function buildSummary() {
        $ar = $this->analysisResult;
        return [
            'total_segments' => $ar['totalCount'] ?? 0,
            'ad_segments' => $ar['adCount'] ?? 0,
            'content_segments' => $ar['contentCount'] ?? 0,
            'total_duration' => $ar['totalDuration'] ?? 0,
            'ad_duration' => $ar['adDuration'] ?? 0,
            'content_duration' => $ar['contentDuration'] ?? 0,
            'ad_percentage' => $ar['adPercentage'] ?? 0,
            'ad_cluster_count' => count($ar['adClusters'] ?? []),
            'estimated_time_saved' => $ar['adDuration'] ?? 0
        ];
    }

    private function buildAdClusterDetails() {
        $clusters = $this->analysisResult['adClusters'] ?? [];
        $segments = $this->playlist['segments'] ?? [];
        $results = $this->analysisResult['segments'] ?? [];
        $total = count($segments);

        $detailed = [];
        foreach ($clusters as $idx => $cluster) {
            $clusterSegments = array_slice($segments, $cluster['start'], $cluster['end'] - $cluster['start'] + 1);
            $clusterResults = array_slice($results, $cluster['start'], $cluster['end'] - $cluster['start'] + 1);

            $duration = 0;
            $avgConfidence = 0;
            $hasMarker = false;
            $ruleCategories = [];

            foreach ($clusterSegments as $i => $seg) {
                $duration += $seg['duration'] ?? 0;
                $r = $clusterResults[$i];
                $avgConfidence += $r['confidence'] ?? 0;
                if (!empty($seg['discontinuity']) || !empty($seg['cueMarkers']) || !empty($seg['scte35'])) {
                    $hasMarker = true;
                }
                foreach ($r['matchedRules'] ?? [] as $rule) {
                    $cat = $rule['category'] ?? 'unknown';
                    if (!isset($ruleCategories[$cat])) {
                        $ruleCategories[$cat] = 0;
                    }
                    $ruleCategories[$cat]++;
                }
            }

            $startRatio = $total > 0 ? round($cluster['start'] / $total * 100, 1) : 0;
            $endRatio = $total > 0 ? round($cluster['end'] / $total * 100, 1) : 0;

            $position = 'middle';
            if ($startRatio < 15) $position = 'pre-roll';
            elseif ($endRatio > 85) $position = 'post-roll';

            $detailed[] = [
                'index' => $idx,
                'start_index' => $cluster['start'],
                'end_index' => $cluster['end'],
                'segment_count' => $cluster['count'],
                'duration' => round($duration, 2),
                'start_position_percent' => $startRatio,
                'end_position_percent' => $endRatio,
                'position_type' => $position,
                'avg_confidence' => $cluster['count'] > 0 ? round($avgConfidence / $cluster['count'], 0) : 0,
                'has_marker' => $hasMarker,
                'rule_categories' => $ruleCategories,
                'start_time' => $this->calculateStartTime($cluster['start']),
                'end_time' => $this->calculateStartTime($cluster['end'] + 1)
            ];
        }

        return $detailed;
    }

    private function buildPrefixSum($segments) {
        $n = count($segments);
        $this->prefixSum = array_fill(0, $n + 1, 0.0);
        for ($i = 0; $i < $n; $i++) {
            $this->prefixSum[$i + 1] = $this->prefixSum[$i] + ($segments[$i]['duration'] ?? 0);
        }
    }

    private function calculateStartTime($segmentIndex) {
        $n = count($this->prefixSum) - 1;
        $idx = max(0, min($segmentIndex, $n));
        return round($this->prefixSum[$idx], 2);
    }

    private function buildDetailedSegments() {
        $results = $this->analysisResult['segments'] ?? [];
        $segments = $this->playlist['segments'] ?? [];
        $detailed = [];

        foreach ($results as $i => $r) {
            $seg = $segments[$i];
            $detailed[] = [
                'index' => $i,
                'duration' => $seg['duration'] ?? 0,
                'uri' => $seg['uri'] ?? '',
                'title' => $seg['title'] ?? '',
                'is_ad' => !empty($r['isAd']),
                'confidence' => $r['confidence'] ?? 0,
                'matched_rules' => array_map(function($rule) {
                    return [
                        'name' => $rule['name'],
                        'description' => $rule['description'],
                        'weight' => $rule['weight'] ?? 0,
                        'category' => $rule['category'] ?? ''
                    ];
                }, $r['matchedRules'] ?? []),
                'discontinuity' => !empty($seg['discontinuity']),
                'has_cue_markers' => !empty($seg['cueMarkers']),
                'has_scte35' => !empty($seg['scte35']),
                'has_ad_tags' => !empty($seg['adMarkers']),
                'start_time' => $this->calculateStartTime($i)
            ];
        }

        return $detailed;
    }

    private function resolveMediaUrl($baseUrl, $variantUri) {
        $parsed = parse_url($baseUrl);
        $base = $parsed['scheme'] . '://' . $parsed['host'];
        if (!empty($parsed['port'])) {
            $base .= ':' . $parsed['port'];
        }
        $pathDir = dirname($parsed['path'] ?? '');
        $pathDir = $pathDir === '.' ? '' : $pathDir;

        if (strpos($variantUri, '/') === 0) {
            return $base . $variantUri;
        } else {
            return $base . $pathDir . '/' . ltrim($variantUri, '/');
        }
    }

    public function learnRules() {
        if (!$this->domain || !$this->analysisResult) {
            return ['success' => false, 'message' => '无法学习：缺少域名或分析结果'];
        }

        try {
            $existingRules = $this->ruleManager->getRules($this->domain);
            $isNew = $existingRules === null;

            $result = $this->ruleManager->learnFromAnalysis($this->domain, $this->analysisResult);

            $updatedRules = $this->ruleManager->getRules($this->domain);

            return [
                'success' => $result !== false,
                'is_new_rule' => $isNew,
                'learn_count' => $updatedRules['learn_count'] ?? 1,
                'confidence_score' => $updatedRules['confidence_score'] ?? 0,
                'message' => $isNew ? '规则已创建' : '规则已更新'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function generateReport() {
        if (!$this->analysisResult) {
            return null;
        }

        $summary = $this->buildSummary();
        $insertion = $this->analysisResult['insertionPoints'] ?? [];
        $psych = $this->analysisResult['psychologicalFeatures'] ?? [];

        $report = "=" . str_repeat("=", 58) . "\n";
        $report .= "  M3U8 视频广告分析报告\n";
        $report .= "=" . str_repeat("=", 58) . "\n\n";

        $report .= "【基本信息】\n";
        $report .= "  域名: " . ($this->domain ?: '未知') . "\n";
        $report .= "  总片段数: " . $summary['total_segments'] . "\n";
        $report .= "  总时长: " . $this->formatDuration($summary['total_duration']) . "\n\n";

        $report .= "【广告概览】\n";
        $report .= "  广告片段数: " . $summary['ad_segments'] . "\n";
        $report .= "  广告总时长: " . $this->formatDuration($summary['ad_duration']) . "\n";
        $report .= "  广告占比: " . $summary['ad_percentage'] . "%\n";
        $report .= "  广告簇数量: " . $summary['ad_cluster_count'] . " 处\n";
        $report .= "  可节省时间: " . $this->formatDuration($summary['estimated_time_saved']) . "\n";
        $report .= "  检测置信度: " . ($this->analysisResult['confidence'] ?? 0) . "%\n\n";

        $report .= "【插播分析】\n";
        if (!empty($insertion['pre_roll']['found'])) {
            $report .= "  片头广告: 有 (" . $this->formatDuration($insertion['pre_roll']['duration']) . ")\n";
        } else {
            $report .= "  片头广告: 无\n";
        }
        if (!empty($insertion['mid_roll']['found'])) {
            $report .= "  片中插播: " . ($insertion['mid_roll']['count'] ?? 0) . " 处\n";
        } else {
            $report .= "  片中插播: 无\n";
        }
        if (!empty($insertion['post_roll']['found'])) {
            $report .= "  片尾广告: 有 (" . $this->formatDuration($insertion['post_roll']['duration']) . ")\n";
        } else {
            $report .= "  片尾广告: 无\n";
        }
        $report .= "\n";

        $report .= "【心理特征分析】\n";
        $report .= "  插播模式: " . ($psych['interruption_pattern'] ?? '未知') . "\n";
        $report .= "  用户体验影响: " . ($psych['user_experience_impact'] ?? '未知') . "\n";
        $report .= "  广告密度: " . ($psych['ad_density'] ?? 0) . "%\n";
        $report .= "  注意力抓取指数: " . ($psych['attention_grab_score'] ?? 0) . "/100\n";
        $report .= "  插播频率指数: " . ($psych['frequency_score'] ?? 0) . "/100\n";
        $report .= "  可观看性评分: " . ($psych['watchability_score'] ?? 0) . "/100\n\n";

        $report .= "【广告标记检测】\n";
        $markers = $this->analysisResult['cueMarkerCount'] ?? 0;
        $scte35 = $this->analysisResult['scte35Count'] ?? 0;
        $adTags = $this->analysisResult['adTagCount'] ?? 0;
        $disc = $this->analysisResult['discontinuityCount'] ?? 0;
        $report .= "  DISCONTINUITY 标记: " . $disc . " 个\n";
        $report .= "  CUE-OUT/CUE-IN 标记: " . $markers . " 个\n";
        $report .= "  SCTE-35 信令: " . $scte35 . " 个\n";
        $report .= "  自定义 AD 标签: " . $adTags . " 个\n";

        return $report;
    }

    private function formatDuration($seconds) {
        $seconds = (float)$seconds;
        if ($seconds < 60) {
            return round($seconds, 1) . " 秒";
        } elseif ($seconds < 3600) {
            $min = floor($seconds / 60);
            $sec = round($seconds % 60, 1);
            return $min . " 分 " . $sec . " 秒";
        } else {
            $hour = floor($seconds / 3600);
            $min = floor(($seconds % 3600) / 60);
            $sec = round($seconds % 60, 1);
            return $hour . " 小时 " . $min . " 分 " . $sec . " 秒";
        }
    }

    public function getParser() {
        return $this->parser;
    }

    public function getEngine() {
        return $this->engine;
    }

    public function getRuleManager() {
        return $this->ruleManager;
    }

    public function getPlaylist() {
        return $this->playlist;
    }

    public function getAnalysisResult() {
        return $this->analysisResult;
    }

    public function getDomain() {
        return $this->domain;
    }
}
