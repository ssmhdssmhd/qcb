<?php

class AdRuleEngine {
    private $options = [];
    private $rules = [];

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
                'description' => '重复出现完全相同时长的片段，可能是广告',
                'check' => function($segment, $index, $segments) {
                    if (count($segments) < 10) return false;
                    
                    $duration = $segment['duration'];
                    $exactCount = 0;
                    foreach ($segments as $s) {
                        if (abs($s['duration'] - $duration) < 0.001) {
                            $exactCount++;
                        }
                    }
                    
                    $isShortAd = $duration >= 2 && $duration <= 6;
                    
                    return $exactCount >= 4 && $exactCount > count($segments) * 0.5 && $isShortAd;
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
}
