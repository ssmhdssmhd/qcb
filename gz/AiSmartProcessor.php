<?php

require_once __DIR__ . '/../src/M3U8Parser.php';
require_once __DIR__ . '/../src/AdFilter.php';
require_once __DIR__ . '/../src/OutputGenerator.php';
require_once __DIR__ . '/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/DomainRuleManager.php';
require_once __DIR__ . '/ProfessionalAdDetector.php';

class AiSmartProcessor {
    private $parser;
    private $enhancedEngine;
    private $filter;
    private $outputGenerator;
    private $ruleManager;
    private $domain;
    private $proDetector;

    public function __construct($options = []) {
        $this->parser = new M3U8Parser();
        $this->enhancedEngine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $this->filter = new AdFilter($this->enhancedEngine);
        $this->outputGenerator = new OutputGenerator();
        $this->ruleManager = new DomainRuleManager();
        $this->proDetector = new ProfessionalAdDetector($options);
    }

    public function setDomain($domain) {
        $this->domain = $domain;
        $this->enhancedEngine->setDomain($domain);
    }

    public function processUrl($url, $options = []) {
        $startTime = microtime(true);
        $result = [
            'success' => false,
            'url' => $url,
            'domain' => $this->extractDomain($url),
            'steps' => []
        ];

        try {
            $playlist = $this->parser->parse($url);
            $segments = $playlist['segments'] ?? [];
            $result['steps'][] = '✅ 解析 M3U8 完成，共 ' . count($segments) . ' 个片段';

            if (empty($segments)) {
                $result['message'] = '未找到视频片段';
                return $result;
            }

            $analysis = $this->enhancedEngine->analyzeAllSegments($segments);
            $result['analysis'] = $analysis;
            $result['steps'][] = '🔍 智能分析完成，检测到 ' . ($analysis['discontinuityCount'] ?? 0) . ' 个 DISCONTINUITY 标记';

            // 专业级广告检测
            $proResult = $this->proDetector->detect($segments);
            $result['professional_analysis'] = $proResult;
            $result['steps'][] = '🔬 专业检测完成，识别 ' . $proResult['ad_segment_count'] . ' 个广告片段（置信度评分）';

            $adClusters = $this->ruleManager->analyzeAdClustersDetail($analysis, $segments);
            // 合并专业检测的广告簇
            if (!empty($proResult['ad_clusters'])) {
                $adClusters = array_merge($adClusters, $proResult['ad_clusters']);
            }
            $result['ad_clusters'] = $adClusters;
            $result['steps'][] = '🎯 广告簇分析完成，识别出 ' . count($adClusters) . ' 个广告片段集群';

            $discontinuityRules = $this->ruleManager->generateDiscontinuityRegexRules($analysis, $segments);
            $result['discontinuity_regex_rules'] = $discontinuityRules;
            $result['steps'][] = '⚙️ 自动生成 ' . count($discontinuityRules) . ' 条 DISCONTINUITY 正则规则';

            $autoRules = $this->ruleManager->createFromAnalysis($this->domain ?: 'unknown', $analysis);
            $result['auto_rules'] = $autoRules;
            $result['steps'][] = '📋 智能生成规则集完成';

            $filteredPlaylist = $this->filter->filter($playlist);
            $result['filtered'] = $filteredPlaylist;
            $result['steps'][] = '🚀 广告过滤完成';

            $output = $this->outputGenerator->generate($filteredPlaylist, $options);
            $result['output'] = $output;

            $stats = $this->calculateStats($playlist, $filteredPlaylist);
            $result['stats'] = $stats;
            $result['steps'][] = '✨ 处理完成，共过滤 ' . $stats['adSegments'] . ' 个广告片段';

            if (!empty($this->domain) && !empty($autoRules)) {
                $saveResult = $this->ruleManager->saveRules($this->domain, $autoRules);
                if ($saveResult) {
                    $result['steps'][] = '💾 规则已自动保存到规则库';
                    $result['rules_saved'] = true;
                }
            }

            $result['success'] = true;
            $result['process_time'] = round((microtime(true) - $startTime) * 1000, 2);

        } catch (Throwable $e) {
            $result['message'] = '处理失败: ' . $e->getMessage();
            $result['error'] = $e->getMessage();
            $result['steps'][] = '❌ 处理失败: ' . $e->getMessage();
        }

        return $result;
    }

    public function processContent($content, $options = []) {
        $startTime = microtime(true);
        $result = [
            'success' => false,
            'steps' => []
        ];

        try {
            $playlist = $this->parser->parse($content);
            $segments = $playlist['segments'] ?? [];
            $result['steps'][] = '✅ 解析 M3U8 完成，共 ' . count($segments) . ' 个片段';

            if (empty($segments)) {
                $result['message'] = '未找到视频片段';
                return $result;
            }

            $analysis = $this->enhancedEngine->analyzeAllSegments($segments);
            $result['analysis'] = $analysis;
            $result['steps'][] = '🔍 智能分析完成';

            $adClusters = $this->ruleManager->analyzeAdClustersDetail($analysis, $segments);
            $result['ad_clusters'] = $adClusters;
            $result['steps'][] = '🎯 广告簇分析完成，识别出 ' . count($adClusters) . ' 个广告片段集群';

            $discontinuityRules = $this->ruleManager->generateDiscontinuityRegexRules($analysis, $segments);
            $result['discontinuity_regex_rules'] = $discontinuityRules;
            $result['steps'][] = '⚙️ 自动生成 ' . count($discontinuityRules) . ' 条 DISCONTINUITY 正则规则';

            $filteredPlaylist = $this->filter->filter($playlist);
            $result['filtered'] = $filteredPlaylist;
            $result['steps'][] = '🚀 广告过滤完成';

            $output = $this->outputGenerator->generate($filteredPlaylist, $options);
            $result['output'] = $output;

            $stats = $this->calculateStats($playlist, $filteredPlaylist);
            $result['stats'] = $stats;

            $result['success'] = true;
            $result['process_time'] = round((microtime(true) - $startTime) * 1000, 2);

        } catch (Throwable $e) {
            $result['message'] = '处理失败: ' . $e->getMessage();
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    public function smartAnalyze($url) {
        $result = [
            'success' => false,
            'url' => $url,
            'domain' => $this->extractDomain($url)
        ];

        try {
            $playlist = $this->parser->parse($url);
            $segments = $playlist['segments'] ?? [];

            if (empty($segments)) {
                $result['message'] = '未找到视频片段';
                return $result;
            }

            $analysis = $this->enhancedEngine->analyzeAllSegments($segments);
            $adClusters = $this->ruleManager->analyzeAdClustersDetail($analysis, $segments);
            $discontinuityRules = $this->ruleManager->generateDiscontinuityRegexRules($analysis, $segments);
            $autoRules = $this->ruleManager->createFromAnalysis($this->domain ?: 'unknown', $analysis);

            // 专业级广告检测
            $proResult = $this->proDetector->detect($segments);
            if (!empty($proResult['ad_clusters'])) {
                $adClusters = array_merge($adClusters, $proResult['ad_clusters']);
            }

            $result = array_merge($result, [
                'success' => true,
                'total_segments' => count($segments),
                'analysis' => $analysis,
                'professional_analysis' => $proResult,
                'ad_clusters' => $adClusters,
                'discontinuity_regex_rules' => $discontinuityRules,
                'auto_rules' => $autoRules,
                'ad_summary' => $this->generateAdSummary($analysis, $adClusters, $segments)
            ]);

        } catch (Throwable $e) {
            $result['message'] = '分析失败: ' . $e->getMessage();
        }

        return $result;
    }

    private function generateAdSummary($analysis, $adClusters, $segments) {
        $summary = [
            'has_ads' => false,
            'ad_count' => 0,
            'ad_duration' => 0,
            'ad_types' => [],
            'positions' => [],
            'confidence' => 0,
            'recommended_strategy' => 'discontinuity'
        ];

        $totalDuration = 0;
        foreach ($segments as $s) {
            $totalDuration += $s['duration'] ?? 0;
        }

        if (!empty($adClusters)) {
            $summary['has_ads'] = true;
            $summary['ad_count'] = count($adClusters);
            $adDuration = 0;
            foreach ($adClusters as $cluster) {
                $adDuration += $cluster['total_duration'] ?? $cluster['duration'] ?? 0;
                $summary['positions'][] = $cluster['position_type'] ?? $cluster['position'] ?? 'unknown';
            }
            $summary['ad_duration'] = $adDuration;
            $summary['ad_percentage'] = $totalDuration > 0 ? round(($adDuration / $totalDuration) * 100, 1) : 0;
        }

        $discontinuityCount = $analysis['discontinuityCount'] ?? 0;
        if ($discontinuityCount > 0) {
            $summary['ad_types'][] = 'discontinuity';
            $summary['confidence'] = max($summary['confidence'], 85);
        }

        if (!empty($analysis['repetitiveDurations'] ?? null)) {
            $summary['ad_types'][] = 'repetitive_duration';
            $summary['confidence'] = max($summary['confidence'], 75);
        }

        if (!empty($analysis['sequenceJumps'] ?? null)) {
            $summary['ad_types'][] = 'sequence_jump';
            $summary['confidence'] = max($summary['confidence'], 70);
        }

        $positions = array_unique($summary['positions']);
        if (in_array('opening', $positions) && in_array('ending', $positions)) {
            $summary['recommended_strategy'] = 'opening_ending';
        } elseif ($discontinuityCount > 0) {
            $summary['recommended_strategy'] = 'discontinuity';
        } elseif (count($positions) > 2) {
            $summary['recommended_strategy'] = 'multi_cluster';
        }

        return $summary;
    }

    private function calculateStats($original, $filtered) {
        $originalSegments = $original['segments'] ?? [];
        $removedSegments = $filtered['removedSegments'] ?? [];
        $keptSegments = $filtered['segments'] ?? [];

        $originalDuration = 0;
        foreach ($originalSegments as $s) {
            $originalDuration += $s['duration'] ?? 0;
        }

        $filteredDuration = 0;
        foreach ($keptSegments as $s) {
            $filteredDuration += $s['duration'] ?? 0;
        }

        $adDuration = $originalDuration - $filteredDuration;
        $adPercentage = $originalDuration > 0 ? round(($adDuration / $originalDuration) * 100, 1) : 0;

        return [
            'totalSegments' => count($originalSegments),
            'adSegments' => count($removedSegments),
            'keptSegments' => count($keptSegments),
            'originalDuration' => round($originalDuration, 2),
            'filteredDuration' => round($filteredDuration, 2),
            'savedDuration' => round($adDuration, 2),
            'adPercentage' => $adPercentage
        ];
    }

    private function extractDomain($url) {
        $parsed = parse_url($url);
        return $parsed['host'] ?? '';
    }
}
