<?php

class DomainRuleManager {

    private $gzDir;

    public function __construct() {
        $this->gzDir = __DIR__;
    }

    public function getAllRules() {
        $rules = [];
        $files = glob($this->gzDir . '/rules_*.php');
        foreach ($files as $file) {
            $domainRules = require $file;
            if (is_array($domainRules) && isset($domainRules['domain'])) {
                $filename = basename($file);
                $domainRules['_filename'] = $filename;
                $domainRules['_filemtime'] = filemtime($file);
                $rules[$domainRules['domain']] = $domainRules;
            }
        }
        return $rules;
    }

    public function getRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            return require $file;
        }
        return null;
    }

    public function saveRules($domain, $ruleData) {
        $ruleData['domain'] = $domain;
        if (!isset($ruleData['analysis_date'])) {
            $ruleData['analysis_date'] = date('Y-m-d H:i:s');
        }
        if (!isset($ruleData['learn_count'])) {
            $ruleData['learn_count'] = 1;
        }
        $ruleData['last_learn_date'] = date('Y-m-d H:i:s');
        $file = $this->getRuleFilePath($domain);
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * ' . $domain . ' 域名广告和插播规则' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($ruleData) . ';' . "\n";
        return file_put_contents($file, $content) !== false;
    }

    public function deleteRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    public function learnFromAnalysis($domain, $analysisResult) {
        $existingRules = $this->getRules($domain);
        $newRules = $this->createFromAnalysis($domain, $analysisResult);

        if ($existingRules === null) {
            $newRules['learn_count'] = 1;
            $newRules['history_stats'] = [$analysisResult];
            return $this->saveRules($domain, $newRules);
        }

        $mergedRules = $this->mergeRules($existingRules, $newRules, $analysisResult);
        $mergedRules['learn_count'] = ($existingRules['learn_count'] ?? 0) + 1;
        if (!isset($mergedRules['history_stats'])) {
            $mergedRules['history_stats'] = [];
        }
        $mergedRules['history_stats'][] = $analysisResult;
        if (count($mergedRules['history_stats']) > 10) {
            $mergedRules['history_stats'] = array_slice($mergedRules['history_stats'], -10);
        }

        return $this->saveRules($domain, $mergedRules);
    }

    private function mergeRules($existing, $new, $analysisResult) {
        $merged = $existing;

        $adDurationStats = $this->extractAdDurationStats($analysisResult);
        if (!empty($adDurationStats)) {
            $merged['duration_rules'] = $this->mergeDurationRules(
                $existing['duration_rules'] ?? [],
                $adDurationStats
            );
        }

        $discoCount = $analysisResult['discontinuityCount'] ?? 0;
        if ($discoCount > 3) {
            $hasDiscoRule = false;
            foreach ($merged['discontinuity_rules'] ?? [] as $rule) {
                if (!empty($rule['enabled'])) {
                    $hasDiscoRule = true;
                    break;
                }
            }
            if (!$hasDiscoRule) {
                $merged['discontinuity_rules'][] = [
                    'name' => 'discontinuity',
                    'enabled' => true,
                    'type' => 'discontinuity',
                    'reason' => 'DISCONTINUITY 标记表示插播切换',
                    'weight' => 80
                ];
            }
        }

        $seqJumps = $analysisResult['sequenceJumps'] ?? [];
        if (count($seqJumps) > 0) {
            $merged['sequence_jump_rules'] = $this->mergeSeqJumpRules(
                $existing['sequence_jump_rules'] ?? [],
                $seqJumps
            );
        }

        $adFilenames = $this->extractAdFilenamePatterns($analysisResult);
        if (!empty($adFilenames)) {
            $merged['filename_patterns'] = array_unique(array_merge(
                $existing['filename_patterns'] ?? [],
                $adFilenames
            ));
        }

        $adPct = $analysisResult['totalCount'] > 0
            ? ($analysisResult['adCount'] / $analysisResult['totalCount'] * 100)
            : 0;
        if ($adPct > 30) {
            $merged['ad_threshold'] = max(30, min(80, ($existing['ad_threshold'] ?? 50) - 5));
        } elseif ($adPct < 10) {
            $merged['ad_threshold'] = min(80, ($existing['ad_threshold'] ?? 50) + 5);
        }

        return $merged;
    }

    private function extractAdDurationStats($analysisResult) {
        $adSegments = $analysisResult['segments'] ?? [];
        $adDurations = [];
        foreach ($adSegments as $seg) {
            if (!empty($seg['isAd'])) {
                $adDurations[] = $seg['segment']['duration'] ?? 0;
            }
        }
        if (count($adDurations) < 3) {
            return null;
        }
        sort($adDurations);
        $count = count($adDurations);
        return [
            'min' => $adDurations[0],
            'max' => $adDurations[$count - 1],
            'avg' => array_sum($adDurations) / $count,
            'median' => $count % 2 === 0
                ? ($adDurations[$count / 2 - 1] + $adDurations[$count / 2]) / 2
                : $adDurations[floor($count / 2)],
            'count' => $count
        ];
    }

    private function mergeDurationRules($existingRules, $stats) {
        $rules = $existingRules;
        $hasShortRule = false;
        $hasLongRule = false;

        foreach ($rules as &$rule) {
            if ($rule['name'] === 'short_segment' || ($rule['operator'] ?? '') === '<') {
                $hasShortRule = true;
                if ($stats['max'] < $rule['threshold']) {
                    $rule['threshold'] = $stats['max'] + 0.5;
                }
            }
            if ($rule['name'] === 'long_segment' || ($rule['operator'] ?? '') === '>') {
                $hasLongRule = true;
                if ($stats['min'] > $rule['threshold']) {
                    $rule['threshold'] = $stats['min'] - 0.5;
                }
            }
        }
        unset($rule);

        if (!$hasShortRule && $stats['avg'] < 5 && $stats['count'] >= 5) {
            $rules[] = [
                'name' => 'ad_duration_' . intval($stats['avg']) . 's',
                'enabled' => true,
                'type' => 'duration',
                'operator' => '<=',
                'threshold' => round($stats['max'] + 0.5, 2),
                'reason' => '广告片段时长通常在 ' . round($stats['min'], 2) . '-' . round($stats['max'], 2) . '秒',
                'weight' => 60
            ];
        }

        return $rules;
    }

    private function mergeSeqJumpRules($existingRules, $seqJumps) {
        $rules = $existingRules;
        $thresholds = [];
        foreach ($seqJumps as $jump) {
            if (isset($jump['jump']) && abs($jump['jump']) > 1000) {
                $thresholds[] = abs($jump['jump']);
            }
        }
        if (!empty($thresholds)) {
            $minThreshold = min($thresholds);
            $hasForward = false;
            $hasBackward = false;
            foreach ($rules as &$rule) {
                if (($rule['direction'] ?? '') === 'forward') {
                    $hasForward = true;
                    $rule['threshold'] = min($rule['threshold'] ?? PHP_INT_MAX, intval($minThreshold * 0.8));
                }
                if (($rule['direction'] ?? '') === 'backward') {
                    $hasBackward = true;
                    $rule['threshold'] = min($rule['threshold'] ?? PHP_INT_MAX, intval($minThreshold * 0.8));
                }
            }
            unset($rule);
        }
        return $rules;
    }

    private function extractAdFilenamePatterns($analysisResult) {
        $patterns = [];
        $adSegments = $analysisResult['segments'] ?? [];
        $adNames = [];
        foreach ($adSegments as $seg) {
            if (!empty($seg['isAd'])) {
                $uri = $seg['segment']['uri'] ?? '';
                $filename = basename($uri);
                $adNames[] = $filename;
            }
        }
        if (count($adNames) >= 3) {
            $prefixes = [];
            foreach ($adNames as $name) {
                $prefix = substr($name, 0, 8);
                if (!isset($prefixes[$prefix])) {
                    $prefixes[$prefix] = 0;
                }
                $prefixes[$prefix]++;
            }
            foreach ($prefixes as $prefix => $count) {
                if ($count >= 3) {
                    $patterns[] = '/^' . preg_quote($prefix, '/') . '/i';
                }
            }
        }
        return $patterns;
    }

    public function exportRules($domain = null) {
        if ($domain !== null) {
            $rules = $this->getRules($domain);
            if ($rules === null) {
                return null;
            }
            unset($rules['_filename'], $rules['_filemtime']);
            return [
                'version' => '1.0',
                'export_date' => date('Y-m-d H:i:s'),
                'type' => 'single',
                'rules' => $rules
            ];
        }

        $allRules = $this->getAllRules();
        $export = [];
        foreach ($allRules as $domain => $rules) {
            unset($rules['_filename'], $rules['_filemtime']);
            $export[$domain] = $rules;
        }
        return [
            'version' => '1.0',
            'export_date' => date('Y-m-d H:i:s'),
            'type' => 'all',
            'count' => count($export),
            'rules' => $export
        ];
    }

    public function importRules($importData) {
        if (!is_array($importData) || !isset($importData['rules'])) {
            return ['success' => false, 'message' => '无效的导入数据格式'];
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        if ($importData['type'] === 'single') {
            $rules = $importData['rules'];
            $domain = $rules['domain'] ?? '';
            if (empty($domain)) {
                return ['success' => false, 'message' => '规则缺少域名信息'];
            }
            $exists = $this->getRules($domain) !== null;
            if ($this->saveRules($domain, $rules)) {
                return [
                    'success' => true,
                    'imported' => $exists ? 0 : 1,
                    'updated' => $exists ? 1 : 0,
                    'message' => $exists ? '规则已更新' : '规则已导入'
                ];
            }
            return ['success' => false, 'message' => '保存规则失败'];
        }

        foreach ($importData['rules'] as $domain => $rules) {
            if (empty($domain) || !is_array($rules)) {
                $errors[] = "无效的规则: $domain";
                continue;
            }
            $rules['domain'] = $domain;
            $exists = $this->getRules($domain) !== null;
            if ($this->saveRules($domain, $rules)) {
                if ($exists) {
                    $updated++;
                } else {
                    $imported++;
                }
            } else {
                $errors[] = "保存失败: $domain";
            }
        }

        return [
            'success' => true,
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
            'message' => "导入完成：新增 $imported 条，更新 $updated 条"
        ];
    }

    public function createFromAnalysis($domain, $analysisResult, $customConfig = []) {
        $defaultRules = [
            'domain' => $domain,
            'duration_rules' => [
                [
                    'name' => 'short_segment',
                    'enabled' => true,
                    'type' => 'duration',
                    'operator' => '<',
                    'threshold' => 2,
                    'reason' => '极短片段 (<2秒) 可能是广告',
                    'weight' => 30
                ]
            ],
            'discontinuity_rules' => [
                [
                    'name' => 'discontinuity',
                    'enabled' => $analysisResult['discontinuityCount'] > 5,
                    'type' => 'discontinuity',
                    'reason' => 'DISCONTINUITY 标记表示插播切换',
                    'weight' => 80
                ]
            ],
            'sequence_jump_rules' => [
                [
                    'name' => 'sequence_jump_forward',
                    'enabled' => count($analysisResult['sequenceJumps']) > 0,
                    'type' => 'sequence_jump',
                    'direction' => 'forward',
                    'threshold' => 100000,
                    'reason' => '序列号向前跳跃可能表示广告插播',
                    'weight' => 90
                ],
                [
                    'name' => 'sequence_jump_backward',
                    'enabled' => count($analysisResult['sequenceJumps']) > 0,
                    'type' => 'sequence_jump',
                    'direction' => 'backward',
                    'threshold' => 100000,
                    'reason' => '序列号向后跳跃可能表示广告结束',
                    'weight' => 90
                ]
            ],
            'filename_patterns' => [],
            'ad_threshold' => 50,
            'confidence' => [
                'high' => 80,
                'medium' => 50,
                'low' => 30
            ],
            'note' => '基于靶机测试分析自动生成的规则',
            'analysis_date' => date('Y-m-d H:i:s'),
            'analysis_stats' => [
                'totalSegments' => $analysisResult['totalCount'],
                'adSegments' => $analysisResult['adCount'],
                'discontinuityCount' => $analysisResult['discontinuityCount'],
                'sequenceJumps' => count($analysisResult['sequenceJumps']),
                'adClusters' => count($analysisResult['adClusters'])
            ]
        ];
        return array_merge($defaultRules, $customConfig);
    }

    private function getRuleFilePath($domain) {
        $safeDomain = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $domain);
        return $this->gzDir . '/rules_' . $safeDomain . '.php';
    }

    private function arrayExport($array, $indent = 0) {
        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        if (empty($array)) {
            return '[]';
        }
        $isList = array_keys($array) === range(0, count($array) - 1);
        $items = [];
        foreach ($array as $key => $value) {
            if ($isList) {
                $items[] = $nextPrefix . $this->exportValue($value, $indent + 1);
            } else {
                $items[] = $nextPrefix . var_export($key, true) . ' => ' . $this->exportValue($value, $indent + 1);
            }
        }
        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }

    private function exportValue($value, $indent) {
        if (is_array($value)) {
            return $this->arrayExport($value, $indent);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        } else {
            return var_export((string)$value, true);
        }
    }
}
