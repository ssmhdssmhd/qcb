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
                '/ad[s]?[-_]?\d+/i',
                '/advert/i',
                '/commercial/i',
                '/pre[-_]?roll/i',
                '/mid[-_]?roll/i',
                '/post[-_]?roll/i',
                '/sponsor/i',
                '/^ad\//i'
            ],
            'durationTolerance' => isset($options['durationTolerance']) ? $options['durationTolerance'] : 0.5,
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
                'check' => function($segment, $index, $segments) {
                    return $segment['duration'] < $this->options['minSegmentDuration'];
                }
            ];
        }

        if ($this->options['checkLongSegments']) {
            $this->rules[] = [
                'name' => 'long-duration',
                'description' => '片段时长过长，可能是广告',
                'check' => function($segment, $index, $segments) {
                    return $segment['duration'] > $this->options['maxSegmentDuration'];
                }
            ];
        }

        if ($this->options['checkKeywords'] && count($this->options['adKeywords']) > 0) {
            $this->rules[] = [
                'name' => 'keyword-match',
                'description' => '标题或文件名包含广告关键词',
                'check' => function($segment) {
                    $text = mb_strtolower(($segment['title'] ?? '') . ' ' . ($segment['uri'] ?? ''));
                    foreach ($this->options['adKeywords'] as $kw) {
                        if (mb_strpos($text, mb_strtolower($kw)) !== false) {
                            return true;
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
                'check' => function($segment, $index, $segments) {
                    return !empty($segment['discontinuity']);
                }
            ];
        }

        if ($this->options['checkRepetitiveDuration']) {
            $this->rules[] = [
                'name' => 'repetitive-duration',
                'description' => '重复出现相近时长的短片段，可能是广告',
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
                    
                    $isShort = $duration >= 1 && $duration <= 6;
                    $similarRatio = $similarCount / $totalCount;
                    
                    $isVeryShortCluster = $duration <= 4 && $similarRatio >= 0.15 && $similarRatio <= 0.5;
                    $isShortConsecutiveAd = $isShort && $maxConsecutive >= 5 && $similarRatio <= 0.6;
                    
                    $hasOtherDurationClusters = false;
                    foreach ($stats as $key => $bStats) {
                        if ($key !== $durationKey && $bStats['count'] / $totalCount >= 0.15) {
                            $hasOtherDurationClusters = true;
                            break;
                        }
                    }
                    
                    if (!$hasOtherDurationClusters && $similarRatio > 0.7) {
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

        foreach ($this->rules as $rule) {
            try {
                if (call_user_func($rule['check'], $segment, $index, $segments)) {
                    $matchedRules[] = [
                        'name' => $rule['name'],
                        'description' => $rule['description']
                    ];
                }
            } catch (Exception $e) {
                // 忽略规则检查错误
            }
        }

        return [
            'isAd' => count($matchedRules) > 0,
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
                'description' => $r['description']
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
