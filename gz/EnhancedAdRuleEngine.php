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
            'checkSequenceJump' => true,
            'checkAdUriPattern' => true,
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
        $this->addEnhancedRules();
    }

    private function addEnhancedRules() {
        if (!empty($this->options['checkAdUriPattern'])) {
            $this->addRule([
                'name' => 'ad-uri-pattern',
                'description' => 'URI包含广告特征路径',
                'category' => 'pattern',
                'weight' => 90,
                'check' => function($segment) {
                    $uri = $segment['uri'] ?? '';
                    $adPatterns = [
                        '/\/adjump\//i',
                        '/\/ad\//i',
                        '/\/advertisement\//i',
                        '/\/commercial\//i',
                        '/\/promo\//i',
                        '/\/sponsor\//i',
                        '/\/ads\//i',
                        '/\/pre[-_]?roll\//i',
                        '/\/mid[-_]?roll\//i',
                        '/\/post[-_]?roll\//i',
                        '/\/advert\//i',
                        '/ad_time/i',
                        '/ad_time/i',
                        '/adzone/i',
                        '/adzone/i'
                    ];
                    foreach ($adPatterns as $pattern) {
                        if (preg_match($pattern, $uri)) {
                            return true;
                        }
                    }
                    return false;
                }
            ]);
        }

        if (!empty($this->options['checkSequenceJump'])) {
            $this->addRule([
                'name' => 'extreme-sequence-jump',
                'description' => '序列号出现极端跳跃（超大值或负值），可能是广告跳转',
                'category' => 'marker',
                'weight' => 85,
                'check' => function($segment, $index, $segments) {
                    if ($index === 0) return false;
                    $uri = $segment['uri'] ?? '';
                    $filename = basename($uri, '.ts');
                    if (preg_match('/(\d+)$/', $filename, $matches)) {
                        $currentSeq = intval($matches[1]);
                        if ($currentSeq > 1000000000000) {
                            return true;
                        }
                    }
                    return false;
                }
            ]);
        }
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
        if (!empty($rules['duration_rules']) && is_array($rules['duration_rules'])) {
            foreach ($rules['duration_rules'] as $rule) {
                if (!empty($rule['enabled'])) {
                    $this->addDurationRule($rule);
                }
            }
        }

        if (!empty($rules['discontinuity_rules']) && is_array($rules['discontinuity_rules'])) {
            foreach ($rules['discontinuity_rules'] as $rule) {
                if (!empty($rule['enabled'])) {
                    $this->addDiscontinuityRule($rule);
                }
            }
        }

        if (!empty($rules['sequence_jump_rules']) && is_array($rules['sequence_jump_rules'])) {
            foreach ($rules['sequence_jump_rules'] as $rule) {
                if (!empty($rule['enabled'])) {
                    $this->addSequenceJumpRule($rule);
                }
            }
        }

        if (!empty($rules['filename_patterns']) && is_array($rules['filename_patterns'])) {
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
            $result['adapted'] = false;
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

        $originalThreshold = $this->getAdThreshold();
        $bestResult = $result;
        $bestAdPercentage = $adPercentage;
        $bestContentCount = $result['contentCount'];
        $bestThreshold = $originalThreshold;
        $attempts = 0;
        $maxAttempts = 5;
        $currentThreshold = $originalThreshold;

        while ($attempts < $maxAttempts) {
            $attempts++;
            $currentThreshold += 20;
            if ($currentThreshold > 200) {
                $currentThreshold = 200;
            }

            $tempAdCount = 0;
            $tempAdDuration = 0;
            $tempContentDuration = 0;
            $segResults = &$result['segments'];
            $segCount = count($segResults);

            for ($i = 0; $i < $segCount; $i++) {
                $conf = $segResults[$i]['confidence'] ?? 0;
                if ($conf >= $currentThreshold) {
                    $tempAdCount++;
                    $tempAdDuration += $segments[$i]['duration'] ?? 0;
                } else {
                    $tempContentDuration += $segments[$i]['duration'] ?? 0;
                }
            }

            $tempAdPercentage = $total > 0 ? round($tempAdCount / $total * 100, 1) : 0;
            $tempContentCount = $total - $tempAdCount;

            if ($tempAdPercentage <= 70 && $tempContentCount >= $total * 0.3) {
                $bestThreshold = $currentThreshold;
                break;
            }

            if ($tempAdPercentage < $bestAdPercentage && $tempContentCount > $bestContentCount) {
                $bestAdPercentage = $tempAdPercentage;
                $bestContentCount = $tempContentCount;
                $bestThreshold = $currentThreshold;
            }
        }

        if ($bestThreshold !== $originalThreshold) {
            $newSegments = [];
            $newAdCount = 0;
            $newContentCount = 0;
            $newAdDuration = 0;
            $newContentDuration = 0;
            $segResults = &$result['segments'];

            for ($i = 0; $i < $total; $i++) {
                $r = $segResults[$i];
                $isAd = ($r['confidence'] ?? 0) >= $bestThreshold;
                $r['isAd'] = $isAd;
                $newSegments[] = $r;
                if ($isAd) {
                    $newAdCount++;
                    $newAdDuration += $segments[$i]['duration'] ?? 0;
                } else {
                    $newContentCount++;
                    $newContentDuration += $segments[$i]['duration'] ?? 0;
                }
            }

            $bestResult = $result;
            $bestResult['segments'] = $newSegments;
            $bestResult['adCount'] = $newAdCount;
            $bestResult['contentCount'] = $newContentCount;
            $bestResult['adDuration'] = round($newAdDuration, 2);
            $bestResult['contentDuration'] = round($newContentDuration, 2);
            $bestResult['adPercentage'] = $total > 0 ? round($newAdCount / $total * 100, 1) : 0;

            $newAdClusters = $this->rebuildAdClusters($newSegments, $segments);
            $bestResult['adClusters'] = $newAdClusters;

            $insertionPoints = $this->rebuildInsertionPoints($newAdClusters, $total, $newAdDuration);
            $bestResult['insertionPoints'] = $insertionPoints;

            $psychFeatures = $this->calculatePsychologicalFeaturesFast(
                count($newAdClusters),
                $bestResult['adPercentage'],
                $newAdDuration,
                $newAdDuration + $newContentDuration
            );
            $bestResult['psychologicalFeatures'] = $psychFeatures;

            $adTypes = $this->detectAdTypesFast($newAdClusters, $insertionPoints);
            $bestResult['adTypes'] = $adTypes;
        }

        $bestResult['adapted'] = true;
        $bestResult['adaptationReason'] = $reason;
        $bestResult['originalThreshold'] = $originalThreshold;
        $bestResult['adaptedThreshold'] = $bestThreshold;
        $bestResult['adaptationAttempts'] = $attempts;

        return $bestResult;
    }

    private function rebuildAdClusters($segResults, $segments) {
        $clusters = [];
        $currentCluster = null;
        $currentClusterDuration = 0;
        $total = count($segResults);

        for ($i = 0; $i < $total; $i++) {
            if (!empty($segResults[$i]['isAd'])) {
                if ($currentCluster === null) {
                    $currentCluster = [
                        'start' => $i,
                        'end' => $i,
                        'count' => 1,
                        'firstDiscontinuity' => false
                    ];
                    $currentClusterDuration = $segments[$i]['duration'] ?? 0;
                } else {
                    $currentCluster['end'] = $i;
                    $currentCluster['count']++;
                    $currentClusterDuration += $segments[$i]['duration'] ?? 0;
                }
            } else {
                if ($currentCluster !== null) {
                    $currentCluster['duration'] = round($currentClusterDuration, 2);
                    $clusters[] = $currentCluster;
                    $currentCluster = null;
                    $currentClusterDuration = 0;
                }
            }
        }

        if ($currentCluster !== null) {
            $currentCluster['duration'] = round($currentClusterDuration, 2);
            $clusters[] = $currentCluster;
        }

        return $clusters;
    }

    private function rebuildInsertionPoints($adClusters, $totalCount, $totalAdDuration) {
        $insertionPoints = [
            'pre_roll' => ['found' => false, 'count' => 0, 'duration' => 0],
            'mid_roll' => ['found' => false, 'count' => 0, 'total_duration' => 0],
            'post_roll' => ['found' => false, 'count' => 0, 'duration' => 0]
        ];

        foreach ($adClusters as $idx => $cluster) {
            $startRatio = $totalCount > 0 ? $cluster['start'] / $totalCount : 0;
            $endRatio = $totalCount > 0 ? $cluster['end'] / $totalCount : 0;
            $duration = $cluster['duration'] ?? 0;

            if ($startRatio < 0.1) {
                $insertionPoints['pre_roll']['found'] = true;
                $insertionPoints['pre_roll']['count']++;
                $insertionPoints['pre_roll']['duration'] += $duration;
            } elseif ($endRatio > 0.9) {
                $insertionPoints['post_roll']['found'] = true;
                $insertionPoints['post_roll']['count']++;
                $insertionPoints['post_roll']['duration'] += $duration;
            } else {
                $insertionPoints['mid_roll']['found'] = true;
                $insertionPoints['mid_roll']['count']++;
                $insertionPoints['mid_roll']['total_duration'] += $duration;
            }
        }

        return $insertionPoints;
    }

    private function calculatePsychologicalFeaturesFast($clusterCount, $adPercentage, $adDuration, $totalDuration) {
        $interruptionPattern = '无广告';
        if ($clusterCount === 1 && $adPercentage < 15) {
            $interruptionPattern = '单段插播';
        } elseif ($clusterCount === 2 && $adPercentage < 30) {
            $interruptionPattern = '前后双段';
        } elseif ($clusterCount >= 3) {
            $interruptionPattern = '多段插播';
        } elseif ($adPercentage >= 30) {
            $interruptionPattern = '高频插播';
        }

        $uxImpact = '轻微';
        if ($adPercentage >= 50) {
            $uxImpact = '严重';
        } elseif ($adPercentage >= 30) {
            $uxImpact = '中等';
        } elseif ($adPercentage >= 15) {
            $uxImpact = '较轻';
        }

        $attentionGrabScore = min(100, $adPercentage * 1.5 + $clusterCount * 5);
        $frequencyScore = min(100, $clusterCount * 15 + $adPercentage * 0.8);
        $watchabilityScore = max(0, 100 - $adPercentage * 0.8 - $clusterCount * 3);

        return [
            'interruption_pattern' => $interruptionPattern,
            'user_experience_impact' => $uxImpact,
            'ad_density' => round($adPercentage, 1),
            'attention_grab_score' => round($attentionGrabScore, 0),
            'frequency_score' => round($frequencyScore, 0),
            'watchability_score' => round($watchabilityScore, 0)
        ];
    }

    private function detectAdTypesFast($adClusters, $insertionPoints) {
        $types = [];

        if (!empty($insertionPoints['pre_roll']['found'])) {
            $types[] = [
                'type' => 'pre_roll',
                'name' => '片头广告',
                'description' => '视频开始前的广告',
                'count' => $insertionPoints['pre_roll']['count']
            ];
        }
        if (!empty($insertionPoints['mid_roll']['found'])) {
            $types[] = [
                'type' => 'mid_roll',
                'name' => '片中插播广告',
                'description' => '视频播放过程中的插播广告',
                'count' => $insertionPoints['mid_roll']['count']
            ];
        }
        if (!empty($insertionPoints['post_roll']['found'])) {
            $types[] = [
                'type' => 'post_roll',
                'name' => '片尾广告',
                'description' => '视频结束后的广告',
                'count' => $insertionPoints['post_roll']['count']
            ];
        }

        return $types;
    }

    public function analyzeAllSegments($segments) {
        $total = count($segments);
        if ($total === 0) {
            return [
                'segments' => [], 'totalCount' => 0, 'adCount' => 0, 'contentCount' => 0,
                'totalDuration' => 0, 'adDuration' => 0, 'contentDuration' => 0, 'adPercentage' => 0,
                'discontinuityCount' => 0, 'cueMarkerCount' => 0, 'scte35Count' => 0, 'adTagCount' => 0,
                'sequenceJumps' => [], 'durationDistribution' => [], 'adClusters' => [],
                'insertionPoints' => [], 'adTypes' => [], 'psychologicalFeatures' => [],
                'confidence' => 0
            ];
        }

        $results = [];
        $adCount = 0;
        $contentCount = 0;
        $totalAdDuration = 0;
        $totalContentDuration = 0;
        $discontinuityCount = 0;
        $cueMarkerCount = 0;
        $scte35Count = 0;
        $adTagCount = 0;

        $durationBuckets = [];
        $minDuration = PHP_FLOAT_MAX;
        $maxDuration = 0;
        $sumDuration = 0;

        $adClusters = [];
        $currentCluster = null;
        $currentClusterDuration = 0;

        $jumpInfo = [];
        $prevSeq = null;
        $prevUri = '';

        $preRoll = ['found' => false, 'start_index' => -1, 'end_index' => -1, 'duration' => 0, 'segment_count' => 0];
        $postRoll = ['found' => false, 'start_index' => -1, 'end_index' => -1, 'duration' => 0, 'segment_count' => 0];
        $midRollPoints = [];

        $preRollAdCount = 0;
        $postRollAdCount = 0;
        $midRollAdCount = 0;
        $preRollAdDuration = 0;
        $postRollAdDuration = 0;
        $midRollAdDuration = 0;

        $markerAdCount = 0;
        $patternAdCount = 0;
        $durationAdCount = 0;
        $markerAdDuration = 0;
        $patternAdDuration = 0;
        $durationAdDuration = 0;

        $totalConfidence = 0;
        $highConfidenceCount = 0;

        $rules = $this->rules;
        $adThreshold = $this->options['adThreshold'] ?? 50;

        for ($i = 0; $i < $total; $i++) {
            $seg = $segments[$i];
            $duration = $seg['duration'] ?? 0;

            $matchedRules = [];
            $totalWeight = 0;
            $categories = [];
            $hasMarker = false;
            $hasPattern = false;
            $hasDurationRule = false;

            foreach ($rules as $rule) {
                try {
                    if (call_user_func($rule['check'], $seg, $i, $segments)) {
                        $weight = $rule['weight'] ?? 50;
                        $category = $rule['category'] ?? 'unknown';
                        $matchedRules[] = [
                            'name' => $rule['name'],
                            'weight' => $weight,
                            'category' => $category
                        ];
                        $totalWeight += $weight;
                        if (!isset($categories[$category])) {
                            $categories[$category] = 0;
                        }
                        $categories[$category] += $weight;
                        if ($category === 'marker') $hasMarker = true;
                        if ($category === 'pattern') $hasPattern = true;
                        if ($category === 'duration') $hasDurationRule = true;
                    }
                } catch (Throwable $e) {
                }
            }

            $isAd = $totalWeight >= $adThreshold;
            $results[] = [
                'isAd' => $isAd,
                'matchedRules' => $matchedRules,
                'confidence' => min(100, $totalWeight),
                'categories' => $categories,
                'totalWeight' => $totalWeight,
                'duration' => $duration
            ];

            if ($isAd) {
                $adCount++;
                $totalAdDuration += $duration;
                if ($totalWeight >= 70) $highConfidenceCount++;
                $totalConfidence += min(100, $totalWeight);

                if ($currentCluster === null) {
                    $currentCluster = ['start' => $i, 'end' => $i, 'count' => 1];
                    $currentClusterDuration = $duration;
                    if ($hasMarker) $currentCluster['has_marker'] = true;
                    if ($hasPattern) $currentCluster['has_pattern'] = true;
                    if ($hasDurationRule) $currentCluster['has_duration'] = true;
                } else {
                    $currentCluster['end'] = $i;
                    $currentCluster['count']++;
                    $currentClusterDuration += $duration;
                    if ($hasMarker) $currentCluster['has_marker'] = true;
                    if ($hasPattern) $currentCluster['has_pattern'] = true;
                    if ($hasDurationRule) $currentCluster['has_duration'] = true;
                }
            } else {
                $contentCount++;
                $totalContentDuration += $duration;

                if ($currentCluster !== null) {
                    $currentCluster['duration'] = round($currentClusterDuration, 2);
                    $adClusters[] = $currentCluster;
                    $currentCluster = null;
                    $currentClusterDuration = 0;
                }
            }

            if (!empty($seg['discontinuity'])) $discontinuityCount++;
            if (!empty($seg['cueMarkers'])) $cueMarkerCount += count($seg['cueMarkers']);
            if (!empty($seg['scte35'])) $scte35Count++;
            if (!empty($seg['adMarkers'])) $adTagCount += count($seg['adMarkers']);

            if ($duration < $minDuration) $minDuration = $duration;
            if ($duration > $maxDuration) $maxDuration = $duration;
            $sumDuration += $duration;
            $bucket = (string)(floor($duration * 10) / 10);
            if (!isset($durationBuckets[$bucket])) $durationBuckets[$bucket] = 0;
            $durationBuckets[$bucket]++;

            $uri = $seg['uri'] ?? '';
            $currentSeq = $this->extractSequenceNumber($uri);
            if ($currentSeq !== null && $prevSeq !== null) {
                $jump = $currentSeq - $prevSeq;
                if (abs($jump) > 1) {
                    $jumpInfo[] = [
                        'index' => $i,
                        'prevSeq' => $prevSeq,
                        'currentSeq' => $currentSeq,
                        'jump' => $jump,
                        'prevUri' => $prevUri,
                        'currentUri' => $uri
                    ];
                }
            }
            $prevSeq = $currentSeq;
            $prevUri = $uri;
        }

        if ($currentCluster !== null) {
            $currentCluster['duration'] = round($currentClusterDuration, 2);
            $adClusters[] = $currentCluster;
        }

        foreach ($adClusters as $idx => $cluster) {
            $startRatio = $cluster['start'] / $total;
            $endRatio = $cluster['end'] / $total;
            $clusterDuration = $cluster['duration'] ?? 0;

            if ($startRatio < 0.15 && $cluster['count'] >= 2) {
                $preRoll = [
                    'found' => true,
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $clusterDuration,
                    'segment_count' => $cluster['count']
                ];
                $preRollAdCount++;
                $preRollAdDuration += $clusterDuration;
            } elseif ($endRatio > 0.85 && $cluster['count'] >= 2) {
                $postRoll = [
                    'found' => true,
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $clusterDuration,
                    'segment_count' => $cluster['count']
                ];
                $postRollAdCount++;
                $postRollAdDuration += $clusterDuration;
            } elseif ($cluster['count'] >= 2) {
                $midRollPoints[] = [
                    'start_index' => $cluster['start'],
                    'end_index' => $cluster['end'],
                    'duration' => $clusterDuration,
                    'segment_count' => $cluster['count'],
                    'position_ratio' => round($startRatio, 3)
                ];
                $midRollAdCount++;
                $midRollAdDuration += $clusterDuration;
            }

            if (!empty($cluster['has_marker'])) {
                $markerAdCount++;
                $markerAdDuration += $clusterDuration;
            }
            if (!empty($cluster['has_pattern'])) {
                $patternAdCount++;
                $patternAdDuration += $clusterDuration;
            }
            if (!empty($cluster['has_duration'])) {
                $durationAdCount++;
                $durationAdDuration += $clusterDuration;
            }
        }

        $totalDuration = $totalAdDuration + $totalContentDuration;
        $adPercentage = $totalDuration > 0 ? ($totalAdDuration / $totalDuration * 100) : 0;
        $adDensity = $total > 0 ? ($adCount / $total) : 0;
        $clusterCount = count($adClusters);

        $interruptionPattern = '无广告';
        $uxImpact = '轻微';
        $attentionGrabScore = 0;
        $frequencyScore = 0;
        $watchabilityScore = 100;

        if ($clusterCount > 0) {
            if ($clusterCount === 1 && $preRoll['found'] && !$postRoll['found']) {
                $interruptionPattern = '仅片头';
            } elseif ($clusterCount === 1 && $postRoll['found'] && !$preRoll['found']) {
                $interruptionPattern = '仅片尾';
            } elseif ($clusterCount <= 2 && $preRoll['found'] && $postRoll['found']) {
                $interruptionPattern = '片头+片尾';
            } elseif ($clusterCount >= 5) {
                $interruptionPattern = '频繁插播';
            } else {
                $interruptionPattern = '中间插播';
            }

            if ($adPercentage >= 50 || $clusterCount >= 6) {
                $uxImpact = '严重';
            } elseif ($adPercentage >= 30 || $clusterCount >= 3) {
                $uxImpact = '中等';
            } elseif ($adPercentage >= 15) {
                $uxImpact = '轻微';
            }

            $attentionGrabScore = min(100, round($adPercentage * 0.8 + $clusterCount * 8));
            $frequencyScore = min(100, $clusterCount * 15);
            $watchabilityScore = max(0, round(100 - $adPercentage * 0.8 - $clusterCount * 5));
        }

        $overallConfidence = $adCount > 0 ? min(100, round($totalConfidence / $adCount)) : 0;
        if ($highConfidenceCount < $adCount * 0.3 && $adCount > 10) {
            $overallConfidence = max(0, $overallConfidence - 20);
        }
        if ($discontinuityCount > 5 || $cueMarkerCount > 0 || $scte35Count > 0) {
            $overallConfidence = min(100, $overallConfidence + 15);
        }

        return [
            'segments' => $results,
            'totalCount' => $total,
            'adCount' => $adCount,
            'contentCount' => $contentCount,
            'totalDuration' => round($totalDuration, 2),
            'adDuration' => round($totalAdDuration, 2),
            'contentDuration' => round($totalContentDuration, 2),
            'adPercentage' => round($adPercentage, 2),
            'discontinuityCount' => $discontinuityCount,
            'cueMarkerCount' => $cueMarkerCount,
            'scte35Count' => $scte35Count,
            'adTagCount' => $adTagCount,
            'sequenceJumps' => $jumpInfo,
            'durationDistribution' => [
                'min' => $minDuration === PHP_FLOAT_MAX ? 0 : $minDuration,
                'max' => $maxDuration,
                'avg' => $total > 0 ? $sumDuration / $total : 0,
                'buckets' => $durationBuckets
            ],
            'adClusters' => $adClusters,
            'insertionPoints' => [
                'pre_roll' => $preRoll,
                'mid_roll' => [
                    'found' => count($midRollPoints) > 0,
                    'count' => count($midRollPoints),
                    'points' => $midRollPoints
                ],
                'post_roll' => $postRoll
            ],
            'adTypes' => [
                'pre_roll_ad' => ['count' => $preRollAdCount, 'duration' => round($preRollAdDuration, 2)],
                'mid_roll_ad' => ['count' => $midRollAdCount, 'duration' => round($midRollAdDuration, 2)],
                'post_roll_ad' => ['count' => $postRollAdCount, 'duration' => round($postRollAdDuration, 2)],
                'marker_based_ad' => ['count' => $markerAdCount, 'duration' => round($markerAdDuration, 2)],
                'pattern_based_ad' => ['count' => $patternAdCount, 'duration' => round($patternAdDuration, 2)],
                'duration_based_ad' => ['count' => $durationAdCount, 'duration' => round($durationAdDuration, 2)]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => $interruptionPattern,
                'ad_density' => round($adDensity * 100, 2),
                'attention_grab_score' => $attentionGrabScore,
                'frequency_score' => $frequencyScore,
                'user_experience_impact' => $uxImpact,
                'watchability_score' => $watchabilityScore
            ],
            'confidence' => $overallConfidence
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
