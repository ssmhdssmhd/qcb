<?php

require_once __DIR__ . '/../src/AdRuleEngine.php';

class EnhancedAdRuleEngine extends AdRuleEngine {

    private $domainRules = [];
    private $currentDomain = null;
    private $adaptiveMode = false;
    private $originalThreshold = 50;
    private $safeguardEnabled = true;
    private $minContentRatio = 0.2;

    public function __construct($options = []) {
        $defaultOptions = [
            'checkCueMarkers' => true,
            'checkScte35' => true,
            'checkAdTags' => true,
            'checkAdClusters' => true,
            'checkPreRoll' => true,
            'checkPostRoll' => true,
            'adThreshold' => 50,
            'safeguardEnabled' => true,
            'minContentRatio' => 0.2
        ];
        $options = array_merge($defaultOptions, $options);
        parent::__construct($options);
        $this->originalThreshold = $options['adThreshold'] ?? 50;
        $this->safeguardEnabled = $options['safeguardEnabled'] ?? true;
        $this->minContentRatio = $options['minContentRatio'] ?? 0.2;
        $this->loadAllDomainRules();
    }

    private function loadAllDomainRules() {
        $gzDir = __DIR__;
        $files = glob($gzDir . '/rules_*.php');
        foreach ($files as $file) {
            $rules = require $file;
            if (is_array($rules) && isset($rules['domain'])) {
                $this->domainRules[$rules['domain']] = $rules;
            }
        }
    }

    public function setDomain($domain) {
        $this->currentDomain = $domain;
        if (isset($this->domainRules[$domain])) {
            $this->applyDomainRules($this->domainRules[$domain]);
        }
    }

    public function getDomainRules() {
        return $this->domainRules;
    }

    public function getCurrentDomainRules() {
        return $this->currentDomain ? ($this->domainRules[$this->currentDomain] ?? null) : null;
    }

    private function applyDomainRules($rules) {
        if (isset($rules['duration_rules'])) {
            foreach ($rules['duration_rules'] as $rule) {
                if ($rule['enabled']) {
                    $this->addDurationRule($rule);
                }
            }
        }

        if (isset($rules['discontinuity_rules'])) {
            foreach ($rules['discontinuity_rules'] as $rule) {
                if ($rule['enabled']) {
                    $this->addDiscontinuityRule($rule);
                }
            }
        }

        if (isset($rules['sequence_jump_rules'])) {
            foreach ($rules['sequence_jump_rules'] as $rule) {
                if ($rule['enabled']) {
                    $this->addSequenceJumpRule($rule);
                }
            }
        }

        if (isset($rules['filename_patterns'])) {
            foreach ($rules['filename_patterns'] as $pattern) {
                if (!empty($pattern)) {
                    $this->addFilenamePatternRule($pattern);
                }
            }
        }
    }

    private function addDurationRule($rule) {
        $threshold = $rule['threshold'] ?? 2;
        $operator = $rule['operator'] ?? '<';
        $this->addRule([
            'name' => $rule['name'] ?? 'custom-duration',
            'description' => $rule['reason'] ?? '时长规则',
            'check' => function($segment) use ($threshold, $operator) {
                $duration = $segment['duration'] ?? 0;
                switch ($operator) {
                    case '<': return $duration < $threshold;
                    case '>': return $duration > $threshold;
                    case '<=': return $duration <= $threshold;
                    case '>=': return $duration >= $threshold;
                    case '==': return abs($duration - $threshold) < 0.001;
                    default: return $duration < $threshold;
                }
            }
        ]);
    }

    private function addDiscontinuityRule($rule) {
        $this->addRule([
            'name' => $rule['name'] ?? 'custom-discontinuity',
            'description' => $rule['reason'] ?? 'DISCONTINUITY 标记',
            'check' => function($segment) {
                return !empty($segment['discontinuity']);
            }
        ]);
    }

    private function addSequenceJumpRule($rule) {
        $threshold = $rule['threshold'] ?? 100000;
        $direction = $rule['direction'] ?? 'any';
        $this->addRule([
            'name' => $rule['name'] ?? 'sequence-jump',
            'description' => $rule['reason'] ?? '序列号跳跃检测',
            'check' => function($segment, $index, $segments) use ($threshold, $direction) {
                if ($index === 0) return false;
                $currentSeq = $this->extractSequenceNumber($segment['uri'] ?? '');
                $prevSeq = $this->extractSequenceNumber($segments[$index - 1]['uri'] ?? '');
                if ($currentSeq === null || $prevSeq === null) return false;
                $jump = $currentSeq - $prevSeq;
                if ($direction === 'forward') {
                    return $jump > $threshold;
                } elseif ($direction === 'backward') {
                    return $jump < -$threshold;
                } else {
                    return abs($jump) > $threshold;
                }
            }
        ]);
    }

    private function addFilenamePatternRule($pattern) {
        $this->addRule([
            'name' => 'custom-filename-pattern',
            'description' => '文件名模式匹配: ' . $pattern,
            'check' => function($segment) use ($pattern) {
                $uri = $segment['uri'] ?? '';
                $filename = basename($uri);
                return @preg_match($pattern, $filename) || @preg_match($pattern, $uri);
            }
        ]);
    }

    private function extractSequenceNumber($uri) {
        $filename = basename($uri, '.ts');
        if (preg_match('/(\d+)$/', $filename, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }

    public function setAdThreshold($threshold) {
        parent::setAdThreshold($threshold);
    }

    public function getAdThreshold() {
        return parent::getAdThreshold();
    }

    public function resetThreshold() {
        parent::setAdThreshold($this->originalThreshold);
        $this->adaptiveMode = false;
    }

    public function isAdaptiveMode() {
        return $this->adaptiveMode;
    }

    public function analyzeAllSegmentsWithAdaptation($segments) {
        $result = $this->analyzeAllSegments($segments);

        if (!$this->safeguardEnabled) {
            return $result;
        }

        $total = count($segments);
        $adCount = $result['adCount'];
        $adPercentage = $result['adPercentage'];

        $needsAdaptation = false;
        $reason = '';

        if ($total >= 10 && $adPercentage >= 85) {
            $needsAdaptation = true;
            $reason = '广告占比过高 (' . $adPercentage . '%)';
        }

        if ($total >= 20 && ($total - $adCount) < $total * $this->minContentRatio) {
            $needsAdaptation = true;
            $reason = '保留内容过少 (' . ($total - $adCount) . '/' . $total . ')';
        }

        if ($total > 0 && $adCount >= $total) {
            $needsAdaptation = true;
            $reason = '所有片段均被判定为广告';
        }

        if (!$needsAdaptation) {
            $result['adapted'] = false;
            return $result;
        }

        $this->adaptiveMode = true;
        $originalThreshold = $this->getAdThreshold();
        $attempts = 0;
        $maxAttempts = 5;
        $bestResult = $result;

        while ($attempts < $maxAttempts) {
            $attempts++;
            $newThreshold = $this->getAdThreshold() + 20;
            if ($newThreshold > 200) {
                $newThreshold = 200;
            }
            $this->setAdThreshold($newThreshold);

            $tempResult = $this->analyzeAllSegments($segments);
            $tempAdPercentage = $tempResult['adPercentage'];
            $tempContentCount = $tempResult['contentCount'];

            if ($tempAdPercentage <= 70 && $tempContentCount >= $total * 0.3) {
                $bestResult = $tempResult;
                break;
            }

            if ($tempAdPercentage < $adPercentage && $tempContentCount > $result['contentCount']) {
                $bestResult = $tempResult;
            }
        }

        $bestResult['adapted'] = true;
        $bestResult['adaptationReason'] = $reason;
        $bestResult['originalThreshold'] = $originalThreshold;
        $bestResult['adaptedThreshold'] = $this->getAdThreshold();
        $bestResult['adaptationAttempts'] = $attempts;

        $this->setAdThreshold($originalThreshold);
        $this->adaptiveMode = false;

        return $bestResult;
    }

    public function analyzeAllSegments($segments) {
        $results = $this->checkAllSegments($segments);
        $jumpInfo = $this->detectAllSequenceJumps($segments);
        $durationDistribution = $this->analyzeDurationDistribution($segments);
        $adClusters = $this->findAdClusters($results);
        $insertionPoints = $this->analyzeInsertionPoints($results, $segments);
        $adTypeStats = $this->analyzeAdTypes($results, $segments, $adClusters);
        $psychologicalFeatures = $this->analyzePsychologicalFeatures($results, $segments, $adClusters);

        $discontinuityCount = 0;
        $cueMarkerCount = 0;
        $scte35Count = 0;
        $adTagCount = 0;
        $totalAdDuration = 0;
        $totalContentDuration = 0;

        foreach ($results as $i => $r) {
            $seg = $segments[$i];
            if (!empty($seg['discontinuity'])) $discontinuityCount++;
            if (!empty($seg['cueMarkers'])) $cueMarkerCount += count($seg['cueMarkers']);
            if (!empty($seg['scte35'])) $scte35Count++;
            if (!empty($seg['adMarkers'])) $adTagCount += count($seg['adMarkers']);
            if ($r['isAd']) {
                $totalAdDuration += $seg['duration'] ?? 0;
            } else {
                $totalContentDuration += $seg['duration'] ?? 0;
            }
        }

        $totalDuration = $totalAdDuration + $totalContentDuration;
        $adPercentage = $totalDuration > 0 ? ($totalAdDuration / $totalDuration * 100) : 0;

        return [
            'segments' => $results,
            'totalCount' => count($segments),
            'adCount' => count(array_filter($results, function($r) { return $r['isAd']; })),
            'contentCount' => count(array_filter($results, function($r) { return !$r['isAd']; })),
            'totalDuration' => round($totalDuration, 2),
            'adDuration' => round($totalAdDuration, 2),
            'contentDuration' => round($totalContentDuration, 2),
            'adPercentage' => round($adPercentage, 2),
            'discontinuityCount' => $discontinuityCount,
            'cueMarkerCount' => $cueMarkerCount,
            'scte35Count' => $scte35Count,
            'adTagCount' => $adTagCount,
            'sequenceJumps' => $jumpInfo,
            'durationDistribution' => $durationDistribution,
            'adClusters' => $adClusters,
            'insertionPoints' => $insertionPoints,
            'adTypes' => $adTypeStats,
            'psychologicalFeatures' => $psychologicalFeatures,
            'confidence' => $this->calculateOverallConfidence($results, $adClusters)
        ];
    }

    private function detectAllSequenceJumps($segments) {
        $jumps = [];
        for ($i = 1; $i < count($segments); $i++) {
            $currentSeq = $this->extractSequenceNumber($segments[$i]['uri'] ?? '');
            $prevSeq = $this->extractSequenceNumber($segments[$i - 1]['uri'] ?? '');
            if ($currentSeq !== null && $prevSeq !== null) {
                $jump = $currentSeq - $prevSeq;
                if (abs($jump) > 1) {
                    $jumps[] = [
                        'index' => $i,
                        'prevSeq' => $prevSeq,
                        'currentSeq' => $currentSeq,
                        'jump' => $jump,
                        'prevUri' => $segments[$i - 1]['uri'] ?? '',
                        'currentUri' => $segments[$i]['uri'] ?? ''
                    ];
                }
            }
        }
        return $jumps;
    }

    private function analyzeDurationDistribution($segments) {
        $durations = array_map(function($s) { return $s['duration'] ?? 0; }, $segments);
        if (count($durations) === 0) return [];
        sort($durations);
        $buckets = [];
        foreach ($durations as $d) {
            $bucket = (string)(floor($d * 10) / 10);
            if (!isset($buckets[$bucket])) $buckets[$bucket] = 0;
            $buckets[$bucket]++;
        }
        return [
            'min' => min($durations),
            'max' => max($durations),
            'avg' => array_sum($durations) / count($durations),
            'buckets' => $buckets
        ];
    }

    private function findAdClusters($results) {
        $clusters = [];
        $currentCluster = null;
        foreach ($results as $index => $result) {
            if ($result['isAd']) {
                if ($currentCluster === null) {
                    $currentCluster = ['start' => $index, 'end' => $index, 'count' => 1];
                } else {
                    $currentCluster['end'] = $index;
                    $currentCluster['count']++;
                }
            } else {
                if ($currentCluster !== null) {
                    $clusters[] = $currentCluster;
                    $currentCluster = null;
                }
            }
        }
        if ($currentCluster !== null) {
            $clusters[] = $currentCluster;
        }
        return $clusters;
    }

    private function analyzeInsertionPoints($results, $segments) {
        $total = count($segments);
        $points = [
            'pre_roll' => ['found' => false, 'start_index' => -1, 'end_index' => -1, 'duration' => 0, 'segment_count' => 0],
            'mid_roll' => ['found' => false, 'count' => 0, 'points' => []],
            'post_roll' => ['found' => false, 'start_index' => -1, 'end_index' => -1, 'duration' => 0, 'segment_count' => 0]
        ];

        $adClusters = $this->findAdClusters($results);

        foreach ($adClusters as $cluster) {
            $clusterDuration = 0;
            for ($i = $cluster['start']; $i <= $cluster['end']; $i++) {
                $clusterDuration += $segments[$i]['duration'] ?? 0;
            }
            $cluster['duration'] = round($clusterDuration, 2);

            $startRatio = $cluster['start'] / $total;
            $endRatio = $cluster['end'] / $total;

            if ($startRatio < 0.15 && $cluster['count'] >= 2) {
                $points['pre_roll'] = [
                    'found' => true,
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $cluster['duration'],
                    'segment_count' => $cluster['count']
                ];
            } elseif ($endRatio > 0.85 && $cluster['count'] >= 2) {
                $points['post_roll'] = [
                    'found' => true,
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $cluster['duration'],
                    'segment_count' => $cluster['count']
                ];
            } elseif ($cluster['count'] >= 2) {
                $points['mid_roll']['found'] = true;
                $points['mid_roll']['count']++;
                $points['mid_roll']['points'][] = [
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $cluster['duration'],
                    'segment_count' => $cluster['count'],
                    'position_ratio' => round($startRatio, 3)
                ];
            }
        }

        return $points;
    }

    private function analyzeAdTypes($results, $segments, $adClusters) {
        $types = [
            'pre_roll_ad' => ['count' => 0, 'duration' => 0],
            'mid_roll_ad' => ['count' => 0, 'duration' => 0],
            'post_roll_ad' => ['count' => 0, 'duration' => 0],
            'marker_based_ad' => ['count' => 0, 'duration' => 0],
            'pattern_based_ad' => ['count' => 0, 'duration' => 0],
            'duration_based_ad' => ['count' => 0, 'duration' => 0]
        ];

        $total = count($segments);

        foreach ($adClusters as $cluster) {
            $clusterDuration = 0;
            $hasMarker = false;
            $hasPattern = false;
            $hasDuration = false;

            for ($i = $cluster['start']; $i <= $cluster['end']; $i++) {
                $clusterDuration += $segments[$i]['duration'] ?? 0;
                $r = $results[$i];
                foreach ($r['matchedRules'] ?? [] as $rule) {
                    $cat = $rule['category'] ?? '';
                    if ($cat === 'marker') $hasMarker = true;
                    if ($cat === 'pattern') $hasPattern = true;
                    if ($cat === 'duration') $hasDuration = true;
                }
            }

            $startRatio = $cluster['start'] / $total;
            $endRatio = $cluster['end'] / $total;

            if ($startRatio < 0.15) {
                $types['pre_roll_ad']['count']++;
                $types['pre_roll_ad']['duration'] += $clusterDuration;
            } elseif ($endRatio > 0.85) {
                $types['post_roll_ad']['count']++;
                $types['post_roll_ad']['duration'] += $clusterDuration;
            } else {
                $types['mid_roll_ad']['count']++;
                $types['mid_roll_ad']['duration'] += $clusterDuration;
            }

            if ($hasMarker) {
                $types['marker_based_ad']['count']++;
                $types['marker_based_ad']['duration'] += $clusterDuration;
            }
            if ($hasPattern) {
                $types['pattern_based_ad']['count']++;
                $types['pattern_based_ad']['duration'] += $clusterDuration;
            }
            if ($hasDuration) {
                $types['duration_based_ad']['count']++;
                $types['duration_based_ad']['duration'] += $clusterDuration;
            }
        }

        foreach ($types as &$t) {
            $t['duration'] = round($t['duration'], 2);
        }
        unset($t);

        return $types;
    }

    private function analyzePsychologicalFeatures($results, $segments, $adClusters) {
        $features = [
            'interruption_pattern' => '',
            'ad_density' => 0,
            'attention_grab_score' => 0,
            'frequency_score' => 0,
            'user_experience_impact' => '',
            'watchability_score' => 0
        ];

        $total = count($segments);
        $adCount = count(array_filter($results, function($r) { return $r['isAd']; }));
        $adDensity = $total > 0 ? ($adCount / $total) : 0;
        $features['ad_density'] = round($adDensity * 100, 2);

        $clusterCount = count($adClusters);
        if ($clusterCount === 0) {
            $features['interruption_pattern'] = '无广告';
            $features['user_experience_impact'] = '极佳';
            $features['watchability_score'] = 100;
        } elseif ($clusterCount === 1) {
            $firstCluster = $adClusters[0];
            if ($firstCluster['start'] < $total * 0.15) {
                $features['interruption_pattern'] = '仅片头广告';
                $features['user_experience_impact'] = '轻微';
                $features['watchability_score'] = 85;
            } elseif ($firstCluster['start'] > $total * 0.85) {
                $features['interruption_pattern'] = '仅片尾广告';
                $features['user_experience_impact'] = '轻微';
                $features['watchability_score'] = 80;
            } else {
                $features['interruption_pattern'] = '单处插播';
                $features['user_experience_impact'] = '中等';
                $features['watchability_score'] = 70;
            }
        } elseif ($clusterCount <= 3) {
            $features['interruption_pattern'] = '多处插播';
            $features['user_experience_impact'] = '较大';
            $features['watchability_score'] = 50;
        } else {
            $features['interruption_pattern'] = '频繁插播';
            $features['user_experience_impact'] = '严重';
            $features['watchability_score'] = 30;
        }

        $avgClusterSize = $clusterCount > 0 ? $adCount / $clusterCount : 0;
        $features['attention_grab_score'] = min(100, round(($avgClusterSize * 10) + ($adDensity * 50), 0));
        $features['frequency_score'] = min(100, round($clusterCount * 15 + $adDensity * 30, 0));

        return $features;
    }

    private function calculateOverallConfidence($results, $adClusters) {
        if (count($results) === 0) return 0;

        $highConfCount = 0;
        $mediumConfCount = 0;
        $lowConfCount = 0;

        foreach ($results as $r) {
            if ($r['isAd']) {
                $conf = $r['confidence'] ?? 0;
                if ($conf >= 80) $highConfCount++;
                elseif ($conf >= 50) $mediumConfCount++;
                else $lowConfCount++;
            }
        }

        $totalAd = $highConfCount + $mediumConfCount + $lowConfCount;
        if ($totalAd === 0) return 0;

        $weightedScore = ($highConfCount * 100 + $mediumConfCount * 60 + $lowConfCount * 30) / $totalAd;

        $clusterConsistency = 0;
        if (count($adClusters) > 0) {
            $clusteredAds = 0;
            foreach ($adClusters as $c) {
                $clusteredAds += $c['count'];
            }
            $clusterConsistency = $totalAd > 0 ? ($clusteredAds / $totalAd) * 30 : 0;
        }

        return min(100, round($weightedScore * 0.7 + $clusterConsistency, 0));
    }
}
