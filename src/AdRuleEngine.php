<?php

class AdRuleEngine {
    private $options = [];
    private $rules = [];
    private $durationCache = null;
    private $durationCacheKey = null;

    public function __construct($options = []) {
        $this->options = array_merge([
            'minSegmentDuration' => isset($options['minSegmentDuration']) ? $options['minSegmentDuration'] : 2,
            'maxSegmentDuration' => isset($options['maxSegmentDuration']) ? $options['maxSegmentDuration'] : 30,
            'adKeywords' => isset($options['adKeywords']) ? $options['adKeywords'] : [
                'ad', 'ads', 'advert', 'advertisement',
                'pre-roll', 'mid-roll', 'post-roll',
                'preroll', 'midroll', 'postroll',
                'commercial', 'promo', 'sponsor',
                '广告', '插播', '贴片', '片头', '片尾'
            ],
            'adFilenamePatterns' => isset($options['adFilenamePatterns']) ? $options['adFilenamePatterns'] : [
                '/(?:^|[-_\.\/])ad[s]?[-_]?\d+/i',
                '/(?:^|[-_\.\/])advert/i',
                '/(?:^|[-_\.\/])commercial/i',
                '/(?:^|[-_\.\/])pre[-_]?roll/i',
                '/(?:^|[-_\.\/])mid[-_]?roll/i',
                '/(?:^|[-_\.\/])post[-_]?roll/i',
                '/(?:^|[-_\.\/])sponsor/i',
                '/(?:^|[-_\.\/])promo[-_]?\d*/i',
                '/(?:^|[-_\.\/])\d{6}_\d+\.ts/i'
            ],
            'durationTolerance' => isset($options['durationTolerance']) ? $options['durationTolerance'] : 0.5,
            'adThreshold' => isset($options['adThreshold']) ? $options['adThreshold'] : 60,
            'minAdClusterSize' => isset($options['minAdClusterSize']) ? $options['minAdClusterSize'] : 3,
            'maxAdClusterGap' => isset($options['maxAdClusterGap']) ? $options['maxAdClusterGap'] : 1,
            'checkShortSegments' => !isset($options['checkShortSegments']) || $options['checkShortSegments'] !== false,
            'checkLongSegments' => isset($options['checkLongSegments']) && $options['checkLongSegments'] === true,
            'checkKeywords' => !isset($options['checkKeywords']) || $options['checkKeywords'] !== false,
            'checkFilenamePatterns' => !isset($options['checkFilenamePatterns']) || $options['checkFilenamePatterns'] !== false,
            'checkDiscontinuity' => isset($options['checkDiscontinuity']) && $options['checkDiscontinuity'] === true,
            'checkRepetitiveDuration' => isset($options['checkRepetitiveDuration']) && $options['checkRepetitiveDuration'] === true
        ], $options);

        $this->initRules();
    }

    private function initRules() {
        if ($this->options['checkShortSegments']) {
            $this->rules[] = [
                'name' => 'short-duration',
                'description' => '片段时长过短，可能是广告',
                'weight' => 30,
                'check' => function($segment, $index, $segments) {
                    return $segment['duration'] < $this->options['minSegmentDuration'];
                }
            ];
        }

        if ($this->options['checkLongSegments']) {
            $this->rules[] = [
                'name' => 'long-duration',
                'description' => '片段时长过长，可能是广告',
                'weight' => 30,
                'check' => function($segment, $index, $segments) {
                    return $segment['duration'] > $this->options['maxSegmentDuration'];
                }
            ];
        }

        if ($this->options['checkKeywords'] && count($this->options['adKeywords']) > 0) {
            $this->rules[] = [
                'name' => 'keyword-match',
                'description' => '标题或文件名包含广告关键词',
                'weight' => 40,
                'check' => function($segment) {
                    $uri = $segment['uri'] ?? '';
                    $title = $segment['title'] ?? '';
                    $text = mb_strtolower($title . ' ' . basename($uri));
                    
                    foreach ($this->options['adKeywords'] as $kw) {
                        $kwLower = mb_strtolower($kw);
                        $kwLen = mb_strlen($kwLower);
                        
                        if ($kwLen <= 2) {
                            if (preg_match('/(?:^|[-_\.\/\s])' . preg_quote($kwLower, '/') . '(?:$|[-_\.\/\s])/i', $text)) {
                                return true;
                            }
                        } else {
                            if (mb_strpos($text, $kwLower) !== false) {
                                return true;
                            }
                        }
                    }
                    return false;
                }
            ];
        }

        if ($this->options['checkFilenamePatterns'] && count($this->options['adFilenamePatterns']) > 0) {
            $this->rules[] = [
                'name' => 'filename-pattern',
                'description' => '文件名匹配广告命名模式',
                'weight' => 35,
                'check' => function($segment) {
                    $uri = $segment['uri'] ?? '';
                    $pathParts = explode('/', $uri);
                    $filename = end($pathParts);
                    foreach ($this->options['adFilenamePatterns'] as $pattern) {
                        if (preg_match($pattern, $filename) || preg_match($pattern, $uri)) {
                            return true;
                        }
                    }
                    return false;
                }
            ];
        }

        if ($this->options['checkDiscontinuity']) {
            $this->rules[] = [
                'name' => 'discontinuity',
                'description' => '存在不连续标记，可能是插播广告',
                'weight' => 25,
                'check' => function($segment, $index, $segments) {
                    return !empty($segment['discontinuity']);
                }
            ];
        }

        if ($this->options['checkRepetitiveDuration']) {
            $this->rules[] = [
                'name' => 'repetitive-duration',
                'description' => '重复出现相近时长的短片段，可能是广告',
                'weight' => 45,
                'check' => function($segment, $index, $segments) {
                    if (count($segments) < 20) return false;
                    
                    $duration = $segment['duration'];
                    $tolerance = $this->options['durationTolerance'];
                    
                    $cacheKey = md5(serialize(array_column($segments, 'duration')));
                    if ($this->durationCacheKey !== $cacheKey || $this->durationCache === null) {
                        $this->durationCacheKey = $cacheKey;
                        $this->durationCache = $this->computeDurationStats($segments, $tolerance);
                    }
                    
                    $stats = $this->durationCache;
                    $durationKey = $this->getDurationBucketKey($duration, $tolerance);
                    
                    if (!isset($stats[$durationKey])) {
                        return false;
                    }
                    
                    $bucketStats = $stats[$durationKey];
                    $similarCount = $bucketStats['count'];
                    $maxConsecutive = $bucketStats['maxConsecutive'];
                    $totalCount = count($segments);
                    
                    $isShort = $duration >= 0.5 && $duration <= 5;
                    $similarRatio = $similarCount / $totalCount;
                    
                    $isVeryShortCluster = $duration <= 3 && $similarRatio >= 0.1 && $similarRatio <= 0.4;
                    $isShortConsecutiveAd = $isShort && $maxConsecutive >= 8 && $similarRatio <= 0.5;
                    
                    $hasOtherDurationClusters = false;
                    foreach ($stats as $key => $bStats) {
                        if ($key !== $durationKey && $bStats['count'] / $totalCount >= 0.2) {
                            $hasOtherDurationClusters = true;
                            break;
                        }
                    }
                    
                    if (!$hasOtherDurationClusters && $similarRatio > 0.6) {
                        return false;
                    }
                    
                    return ($isVeryShortCluster && $hasOtherDurationClusters)
                        || ($isShortConsecutiveAd && $hasOtherDurationClusters);
                }
            ];
        }
    }

    public function checkSegment($segment, $index, $segments) {
        $matchedRules = [];
        $totalWeight = 0;

        foreach ($this->rules as $rule) {
            try {
                if (call_user_func($rule['check'], $segment, $index, $segments)) {
                    $weight = $rule['weight'] ?? 50;
                    $matchedRules[] = [
                        'name' => $rule['name'],
                        'description' => $rule['description'],
                        'weight' => $weight
                    ];
                    $totalWeight += $weight;
                }
            } catch (Exception $e) {
            }
        }

        $adThreshold = $this->options['adThreshold'] ?? 50;

        return [
            'isAd' => $totalWeight >= $adThreshold,
            'confidence' => $totalWeight,
            'matchedRules' => $matchedRules
        ];
    }

    public function checkAllSegments($segments) {
        $results = [];
        foreach ($segments as $index => $segment) {
            $result = $this->checkSegment($segment, $index, $segments);
            $results[] = array_merge([
                'segment' => $segment,
                'index' => $index
            ], $result);
        }

        $minClusterSize = $this->options['minAdClusterSize'] ?? 2;
        if ($minClusterSize > 1) {
            $results = $this->filterSingleAdSegments($results, $minClusterSize);
        }

        return $results;
    }

    private function filterSingleAdSegments($results, $minClusterSize = 3) {
        $maxGap = $this->options['maxAdClusterGap'] ?? 1;
        $clusters = [];
        $currentCluster = null;
        $gapCount = 0;

        foreach ($results as $index => $result) {
            if ($result['isAd']) {
                if ($currentCluster === null) {
                    $currentCluster = ['start' => $index, 'end' => $index, 'adCount' => 1, 'hasDiscontinuity' => !empty($result['segment']['discontinuity'])];
                } else {
                    $currentCluster['end'] = $index;
                    $currentCluster['adCount']++;
                    if (!empty($result['segment']['discontinuity'])) {
                        $currentCluster['hasDiscontinuity'] = true;
                    }
                }
                $gapCount = 0;
            } else {
                if ($currentCluster !== null) {
                    $gapCount++;
                    if ($gapCount > $maxGap) {
                        $clusters[] = $currentCluster;
                        $currentCluster = null;
                        $gapCount = 0;
                    } else {
                        $currentCluster['end'] = $index;
                    }
                }
            }
        }
        if ($currentCluster !== null) {
            $clusters[] = $currentCluster;
        }

        foreach ($clusters as $cluster) {
            $adCount = $cluster['adCount'] ?? 0;
            $hasDiscontinuity = !empty($cluster['hasDiscontinuity']);
            
            if ($adCount < $minClusterSize && !$hasDiscontinuity) {
                for ($i = $cluster['start']; $i <= $cluster['end']; $i++) {
                    if ($results[$i]['isAd']) {
                        $results[$i]['isAd'] = false;
                        $results[$i]['filtered'] = true;
                        $results[$i]['filterReason'] = '广告簇过小(' . $adCount . '段)';
                    }
                }
            }
        }

        return $results;
    }

    public function addRule($rule) {
        if (isset($rule['name']) && isset($rule['check']) && is_callable($rule['check'])) {
            $this->rules[] = $rule;
        }
    }

    public function removeRule($ruleName) {
        $this->rules = array_filter($this->rules, function($r) use ($ruleName) {
            return $r['name'] !== $ruleName;
        });
        $this->rules = array_values($this->rules);
    }

    public function getRules() {
        return array_map(function($r) {
            return [
                'name' => $r['name'],
                'description' => $r['description'],
                'weight' => $r['weight'] ?? 50
            ];
        }, $this->rules);
    }

    private function getDurationBucketKey($duration, $tolerance) {
        return (string)(round($duration / ($tolerance * 2)) * ($tolerance * 2));
    }

    private function computeDurationStats($segments, $tolerance) {
        $stats = [];
        $count = count($segments);

        $buckets = [];
        foreach ($segments as $s) {
            $key = $this->getDurationBucketKey($s['duration'], $tolerance);
            if (!isset($buckets[$key])) {
                $buckets[$key] = ['count' => 0, 'consecutive' => 0, 'maxConsecutive' => 0];
            }
        }

        $prevKey = null;
        $consecutiveCounts = [];
        foreach ($segments as $s) {
            $key = $this->getDurationBucketKey($s['duration'], $tolerance);
            if (!isset($consecutiveCounts[$key])) {
                $consecutiveCounts[$key] = ['current' => 0, 'max' => 0];
            }
            if ($key === $prevKey) {
                $consecutiveCounts[$key]['current']++;
            } else {
                $consecutiveCounts[$key]['current'] = 1;
            }
            if ($consecutiveCounts[$key]['current'] > $consecutiveCounts[$key]['max']) {
                $consecutiveCounts[$key]['max'] = $consecutiveCounts[$key]['current'];
            }
            $prevKey = $key;
        }

        $counts = [];
        foreach ($segments as $s) {
            $key = $this->getDurationBucketKey($s['duration'], $tolerance);
            if (!isset($counts[$key])) {
                $counts[$key] = 0;
            }
            $counts[$key]++;
        }

        $result = [];
        foreach ($counts as $key => $cnt) {
            $result[$key] = [
                'count' => $cnt,
                'maxConsecutive' => $consecutiveCounts[$key]['max'] ?? 0
            ];
        }

        return $result;
    }
}
