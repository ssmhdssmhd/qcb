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
            $rules = require $file;
            if (is_array($rules)) {
                return $rules;
            }
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
        $totalCount = $analysisResult['totalCount'] ?? 0;
        $adCount = $analysisResult['adCount'] ?? 0;
        $adPercentage = $totalCount > 0 ? ($adCount / $totalCount * 100) : 0;

        if ($totalCount >= 10 && $adPercentage >= 85) {
            return [
                'success' => false,
                'reason' => '广告占比过高 (' . round($adPercentage, 1) . '%)，可能是误判，跳过学习',
                'skipped' => true
            ];
        }

        if ($totalCount >= 20 && ($totalCount - $adCount) < $totalCount * 0.15) {
            return [
                'success' => false,
                'reason' => '保留内容过少，可能是误判，跳过学习',
                'skipped' => true
            ];
        }

        if ($totalCount > 0 && $adCount >= $totalCount) {
            return [
                'success' => false,
                'reason' => '所有片段均被判定为广告，跳过学习',
                'skipped' => true
            ];
        }

        $existingRules = $this->getRules($domain);
        $newRules = $this->createFromAnalysis($domain, $analysisResult);

        if ($existingRules === null) {
            $newRules['learn_count'] = 1;
            $newRules['history_stats'] = [$analysisResult];
            $saveResult = $this->saveRules($domain, $newRules);
            return ['success' => $saveResult, 'new_rules' => true, 'skipped' => false];
        }

        $mergedRules = $this->mergeRules($existingRules, $newRules, $analysisResult);
        $mergedRules['learn_count'] = ($existingRules['learn_count'] ?? 0) + 1;
        if (!isset($mergedRules['history_stats']) || !is_array($mergedRules['history_stats'])) {
            $mergedRules['history_stats'] = [];
        }
        $mergedRules['history_stats'][] = $analysisResult;
        if (count($mergedRules['history_stats']) > 10) {
            $mergedRules['history_stats'] = array_slice($mergedRules['history_stats'], -10);
        }

        $saveResult = $this->saveRules($domain, $mergedRules);
        return ['success' => $saveResult, 'new_rules' => false, 'skipped' => false];
    }

    private function mergeRules($existing, $new, $analysisResult) {
        $merged = $existing;

        // 确保所有需要是数组的字段确实是数组
        $arrayFields = ['duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
                        'filename_patterns', 'insertion_patterns', 'ad_type_stats',
                        'psychological_profile', 'history_stats', 'marker_stats'];
        foreach ($arrayFields as $field) {
            if (!isset($merged[$field]) || !is_array($merged[$field])) {
                $merged[$field] = [];
            }
        }

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
        $learnCount = $existing['learn_count'] ?? 1;
        $adjustFactor = max(0.2, 1 / sqrt($learnCount));

        if ($adPct > 30 && $adPct < 70) {
            $adjustment = round(5 * $adjustFactor, 1);
            $merged['ad_threshold'] = max(30, min(90, ($existing['ad_threshold'] ?? 50) - $adjustment));
        } elseif ($adPct < 10) {
            $adjustment = round(3 * $adjustFactor, 1);
            $merged['ad_threshold'] = min(90, ($existing['ad_threshold'] ?? 50) + $adjustment);
        }

        $insertionPoints = $analysisResult['insertionPoints'] ?? null;
        if ($insertionPoints) {
            $merged['insertion_patterns'] = $this->mergeInsertionPatterns(
                $existing['insertion_patterns'] ?? [],
                $insertionPoints
            );
        }

        $adTypes = $analysisResult['adTypes'] ?? null;
        if ($adTypes) {
            $merged['ad_type_stats'] = $this->mergeAdTypeStats(
                $existing['ad_type_stats'] ?? [],
                $adTypes
            );
        }

        $psychFeatures = $analysisResult['psychologicalFeatures'] ?? null;
        if ($psychFeatures) {
            $merged['psychological_profile'] = $this->mergePsychologicalProfile(
                $existing['psychological_profile'] ?? [],
                $psychFeatures
            );
        }

        $confidence = $analysisResult['confidence'] ?? 0;
        $merged['confidence_score'] = $this->updateConfidenceScore(
            $existing['confidence_score'] ?? 0,
            $confidence,
            $existing['learn_count'] ?? 1
        );

        $cueMarkerCount = $analysisResult['cueMarkerCount'] ?? 0;
        $scte35Count = $analysisResult['scte35Count'] ?? 0;
        $adTagCount = $analysisResult['adTagCount'] ?? 0;

        $merged['marker_stats'] = [
            'discontinuity_count' => ($merged['marker_stats']['discontinuity_count'] ?? 0) + $discoCount,
            'cue_marker_count' => ($merged['marker_stats']['cue_marker_count'] ?? 0) + $cueMarkerCount,
            'scte35_count' => ($merged['marker_stats']['scte35_count'] ?? 0) + $scte35Count,
            'ad_tag_count' => ($merged['marker_stats']['ad_tag_count'] ?? 0) + $adTagCount
        ];

        if ($cueMarkerCount > 0 || $scte35Count > 0 || $adTagCount > 0) {
            $merged['enable_marker_detection'] = true;
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
        $rules = is_array($existingRules) ? $existingRules : [];
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
        $rules = is_array($existingRules) ? $existingRules : [];
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

    private function mergeInsertionPatterns($existing, $new) {
        $result = is_array($existing) ? $existing : [];

        if (!empty($new['pre_roll']['found'])) {
            $result['pre_roll'] = $result['pre_roll'] ?? [
                'detected_count' => 0,
                'avg_duration' => 0,
                'avg_segment_count' => 0
            ];
            $prevCount = $result['pre_roll']['detected_count'] ?? 0;
            $result['pre_roll']['detected_count'] = $prevCount + 1;
            $result['pre_roll']['avg_duration'] = round(
                (($result['pre_roll']['avg_duration'] ?? 0) * $prevCount + ($new['pre_roll']['duration'] ?? 0)) / ($prevCount + 1),
                2
            );
            $result['pre_roll']['avg_segment_count'] = round(
                (($result['pre_roll']['avg_segment_count'] ?? 0) * $prevCount + ($new['pre_roll']['segment_count'] ?? 0)) / ($prevCount + 1),
                1
            );
        }

        if (!empty($new['mid_roll']['found'])) {
            $result['mid_roll'] = $result['mid_roll'] ?? [
                'detected_count' => 0,
                'avg_clip_count' => 0,
                'avg_duration_per_clip' => 0,
                'positions' => []
            ];
            $prevCount = $result['mid_roll']['detected_count'] ?? 0;
            $result['mid_roll']['detected_count'] = $prevCount + 1;
            $result['mid_roll']['avg_clip_count'] = round(
                (($result['mid_roll']['avg_clip_count'] ?? 0) * $prevCount + ($new['mid_roll']['count'] ?? 0)) / ($prevCount + 1),
                1
            );
            $totalMidDur = 0;
            foreach ($new['mid_roll']['points'] ?? [] as $p) {
                $totalMidDur += $p['duration'] ?? 0;
            }
            $avgMidDur = count($new['mid_roll']['points'] ?? []) > 0 ? $totalMidDur / count($new['mid_roll']['points']) : 0;
            $result['mid_roll']['avg_duration_per_clip'] = round(
                (($result['mid_roll']['avg_duration_per_clip'] ?? 0) * $prevCount + $avgMidDur) / ($prevCount + 1),
                2
            );
        }

        if (!empty($new['post_roll']['found'])) {
            $result['post_roll'] = $result['post_roll'] ?? [
                'detected_count' => 0,
                'avg_duration' => 0,
                'avg_segment_count' => 0
            ];
            $prevCount = $result['post_roll']['detected_count'] ?? 0;
            $result['post_roll']['detected_count'] = $prevCount + 1;
            $result['post_roll']['avg_duration'] = round(
                (($result['post_roll']['avg_duration'] ?? 0) * $prevCount + ($new['post_roll']['duration'] ?? 0)) / ($prevCount + 1),
                2
            );
            $result['post_roll']['avg_segment_count'] = round(
                (($result['post_roll']['avg_segment_count'] ?? 0) * $prevCount + ($new['post_roll']['segment_count'] ?? 0)) / ($prevCount + 1),
                1
            );
        }

        return $result;
    }

    private function mergeAdTypeStats($existing, $new) {
        $result = is_array($existing) ? $existing : [];
        $typeKeys = ['pre_roll_ad', 'mid_roll_ad', 'post_roll_ad', 'marker_based_ad', 'pattern_based_ad', 'duration_based_ad'];

        foreach ($typeKeys as $key) {
            if (!isset($result[$key])) {
                $result[$key] = ['total_count' => 0, 'total_duration' => 0, 'avg_count_per_video' => 0, 'sample_count' => 0];
            }
            // 确保所有必要的键都存在
            $result[$key]['total_count'] = $result[$key]['total_count'] ?? 0;
            $result[$key]['total_duration'] = $result[$key]['total_duration'] ?? 0;
            $result[$key]['avg_count_per_video'] = $result[$key]['avg_count_per_video'] ?? 0;
            $result[$key]['sample_count'] = $result[$key]['sample_count'] ?? 0;

            if (isset($new[$key])) {
                $prevSamples = $result[$key]['sample_count'];
                $result[$key]['sample_count'] = $prevSamples + 1;
                $result[$key]['total_count'] += $new[$key]['count'] ?? 0;
                $result[$key]['total_duration'] += $new[$key]['duration'] ?? 0;
                $result[$key]['avg_count_per_video'] = $result[$key]['sample_count'] > 0 ? round(
                    $result[$key]['total_count'] / $result[$key]['sample_count'],
                    2
                ) : 0;
            }
        }

        return $result;
    }

    private function mergePsychologicalProfile($existing, $new) {
        $result = is_array($existing) ? $existing : [];

        $fields = ['ad_density', 'attention_grab_score', 'frequency_score', 'watchability_score'];
        foreach ($fields as $field) {
            if (isset($new[$field])) {
                if (!isset($result[$field])) {
                    $result[$field] = ['avg' => 0, 'sample_count' => 0, 'min' => 100, 'max' => 0];
                }
                $prevSamples = $result[$field]['sample_count'] ?? 0;
                $oldAvg = $result[$field]['avg'] ?? 0;
                $result[$field]['avg'] = round(
                    ($oldAvg * $prevSamples + $new[$field]) / ($prevSamples + 1),
                    2
                );
                $result[$field]['sample_count'] = $prevSamples + 1;
                $result[$field]['min'] = min($result[$field]['min'] ?? 100, $new[$field]);
                $result[$field]['max'] = max($result[$field]['max'] ?? 0, $new[$field]);
            }
        }

        if (!empty($new['interruption_pattern'])) {
            $result['pattern_distribution'] = $result['pattern_distribution'] ?? [];
            $pattern = $new['interruption_pattern'];
            if (!isset($result['pattern_distribution'][$pattern])) {
                $result['pattern_distribution'][$pattern] = 0;
            }
            $result['pattern_distribution'][$pattern]++;
        }

        if (!empty($new['user_experience_impact'])) {
            $result['ux_impact_distribution'] = $result['ux_impact_distribution'] ?? [];
            $impact = $new['user_experience_impact'];
            if (!isset($result['ux_impact_distribution'][$impact])) {
                $result['ux_impact_distribution'][$impact] = 0;
            }
            $result['ux_impact_distribution'][$impact]++;
        }

        return $result;
    }

    private function updateConfidenceScore($oldScore, $newScore, $learnCount) {
        if ($learnCount <= 1) {
            return $newScore;
        }
        $weight = min(0.3, 1 / $learnCount);
        return round($oldScore * (1 - $weight) + $newScore * $weight, 0);
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
        $cueMarkerCount = $analysisResult['cueMarkerCount'] ?? 0;
        $scte35Count = $analysisResult['scte35Count'] ?? 0;
        $adTagCount = $analysisResult['adTagCount'] ?? 0;
        $hasMarkers = $cueMarkerCount > 0 || $scte35Count > 0 || $adTagCount > 0;

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
            'marker_detection' => [
                'cue_markers' => $cueMarkerCount > 0,
                'scte35' => $scte35Count > 0,
                'ad_tags' => $adTagCount > 0,
                'enabled' => $hasMarkers
            ],
            'filename_patterns' => [],
            'ad_threshold' => 50,
            'confidence' => [
                'high' => 80,
                'medium' => 50,
                'low' => 30
            ],
            'confidence_score' => $analysisResult['confidence'] ?? 0,
            'insertion_patterns' => $analysisResult['insertionPoints'] ?? [
                'pre_roll' => ['found' => false],
                'mid_roll' => ['found' => false],
                'post_roll' => ['found' => false]
            ],
            'ad_type_stats' => $analysisResult['adTypes'] ?? [],
            'psychological_profile' => $analysisResult['psychologicalFeatures'] ?? [],
            'marker_stats' => [
                'discontinuity_count' => $analysisResult['discontinuityCount'] ?? 0,
                'cue_marker_count' => $cueMarkerCount,
                'scte35_count' => $scte35Count,
                'ad_tag_count' => $adTagCount
            ],
            'note' => '基于靶机测试分析自动生成的规则',
            'analysis_date' => date('Y-m-d H:i:s'),
            'analysis_stats' => [
                'totalSegments' => $analysisResult['totalCount'],
                'adSegments' => $analysisResult['adCount'],
                'contentSegments' => $analysisResult['contentCount'] ?? 0,
                'totalDuration' => $analysisResult['totalDuration'] ?? 0,
                'adDuration' => $analysisResult['adDuration'] ?? 0,
                'contentDuration' => $analysisResult['contentDuration'] ?? 0,
                'adPercentage' => $analysisResult['adPercentage'] ?? 0,
                'discontinuityCount' => $analysisResult['discontinuityCount'],
                'cueMarkerCount' => $cueMarkerCount,
                'scte35Count' => $scte35Count,
                'adTagCount' => $adTagCount,
                'sequenceJumps' => count($analysisResult['sequenceJumps']),
                'adClusters' => count($analysisResult['adClusters']),
                'confidence' => $analysisResult['confidence'] ?? 0
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
