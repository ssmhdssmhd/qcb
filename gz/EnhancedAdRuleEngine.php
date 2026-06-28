<?php

require_once __DIR__ . '/../src/AdRuleEngine.php';

class EnhancedAdRuleEngine extends AdRuleEngine {

    private $domainRules = [];
    private $currentDomain = null;

    public function __construct($options = []) {
        parent::__construct($options);
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

    public function analyzeAllSegments($segments) {
        $results = $this->checkAllSegments($segments);
        $jumpInfo = $this->detectAllSequenceJumps($segments);
        $durationDistribution = $this->analyzeDurationDistribution($segments);
        $discontinuityCount = 0;
        foreach ($segments as $s) {
            if (!empty($s['discontinuity'])) $discontinuityCount++;
        }
        return [
            'segments' => $results,
            'totalCount' => count($segments),
            'adCount' => count(array_filter($results, function($r) { return $r['isAd']; })),
            'discontinuityCount' => $discontinuityCount,
            'sequenceJumps' => $jumpInfo,
            'durationDistribution' => $durationDistribution,
            'adClusters' => $this->findAdClusters($results)
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
            $bucket = floor($d * 10) / 10;
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
}
