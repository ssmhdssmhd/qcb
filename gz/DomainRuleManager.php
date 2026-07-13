<?php

class DomainRuleManager {

    private $gzDir;

    public function __construct() {
        $this->gzDir = __DIR__;
    }

    public function getAllRules() {
        $rules = [];
        $files = glob($this->gzDir . '/rules_*.php');
        if (!is_array($files)) return $rules;
        foreach ($files as $file) {
            try {
                $domainRules = @require $file;
                if (is_array($domainRules) && !empty($domainRules['domain']) && is_string($domainRules['domain'])) {
                    $domainRules = $this->normalizeRules($domainRules);
                    $filename = basename($file);
                    $domainRules['_filename'] = $filename;
                    $domainRules['_filemtime'] = filemtime($file);
                    $rules[$domainRules['domain']] = $domainRules;
                }
            } catch (Throwable $e) {
                error_log('加载规则文件失败: ' . basename($file) . ' - ' . $e->getMessage());
            }
        }
        return $rules;
    }

    public function getAllRulesLite() {
        $rules = [];
        $files = glob($this->gzDir . '/rules_*.php');
        if (!is_array($files)) return $rules;
        foreach ($files as $file) {
            try {
                $domainRules = @require $file;
                if (is_array($domainRules) && !empty($domainRules['domain']) && is_string($domainRules['domain'])) {
                    $domainRules = $this->normalizeRules($domainRules);
                    $lite = [
                        'domain' => $domainRules['domain'],
                        'name' => $domainRules['name'] ?? $domainRules['domain'],
                        'note' => $domainRules['note'] ?? '',
                        'learn_count' => intval($domainRules['learn_count'] ?? 0),
                        'ad_threshold' => intval($domainRules['ad_threshold'] ?? 50),
                        'confidence_score' => intval($domainRules['confidence_score'] ?? 0),
                        'analysis_date' => $domainRules['analysis_date'] ?? '',
                        'last_learn_date' => $domainRules['last_learn_date'] ?? '',
                        '_filename' => basename($file),
                        '_filemtime' => filemtime($file),
                        'duration_rule_count' => isset($domainRules['duration_rules']) && is_array($domainRules['duration_rules']) ? count($domainRules['duration_rules']) : 0,
                        'discontinuity_rule_count' => isset($domainRules['discontinuity_rules']) && is_array($domainRules['discontinuity_rules']) ? count($domainRules['discontinuity_rules']) : 0,
                        'sequence_jump_rule_count' => isset($domainRules['sequence_jump_rules']) && is_array($domainRules['sequence_jump_rules']) ? count($domainRules['sequence_jump_rules']) : 0,
                        'filename_pattern_count' => isset($domainRules['filename_patterns']) && is_array($domainRules['filename_patterns']) ? count($domainRules['filename_patterns']) : 0,
                        'has_history' => !empty($domainRules['history_stats']) && is_array($domainRules['history_stats']) && count($domainRules['history_stats']) > 0,
                        'history_count' => isset($domainRules['history_stats']) && is_array($domainRules['history_stats']) ? count($domainRules['history_stats']) : 0,
                    ];
                    if (isset($domainRules['analysis_stats']) && is_array($domainRules['analysis_stats'])) {
                        $stats = $domainRules['analysis_stats'];
                        $lite['total_segments'] = $stats['totalSegments'] ?? 0;
                        $lite['ad_segments'] = $stats['adSegments'] ?? 0;
                        $lite['ad_percentage'] = $stats['adPercentage'] ?? 0;
                    } else {
                        $lite['total_segments'] = 0;
                        $lite['ad_segments'] = 0;
                        $lite['ad_percentage'] = 0;
                    }
                    if (isset($domainRules['marker_stats']) && is_array($domainRules['marker_stats'])) {
                        $lite['marker_stats'] = [
                            'discontinuity_count' => $domainRules['marker_stats']['discontinuity_count'] ?? 0,
                            'cue_marker_count' => $domainRules['marker_stats']['cue_marker_count'] ?? 0,
                            'scte35_count' => $domainRules['marker_stats']['scte35_count'] ?? 0,
                            'ad_tag_count' => $domainRules['marker_stats']['ad_tag_count'] ?? 0,
                        ];
                    } else {
                        $lite['marker_stats'] = [
                            'discontinuity_count' => 0,
                            'cue_marker_count' => 0,
                            'scte35_count' => 0,
                            'ad_tag_count' => 0,
                        ];
                    }
                    $rules[$domainRules['domain']] = $lite;
                }
            } catch (Throwable $e) {
                error_log('加载规则文件失败: ' . basename($file) . ' - ' . $e->getMessage());
            }
        }
        return $rules;
    }

    public function getRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            $rules = require $file;
            if (is_array($rules)) {
                return $this->normalizeRules($rules);
            }
        }
        return null;
    }

    private function normalizeRules($rules) {
        if (!is_array($rules)) $rules = [];

        $arrayFields = ['duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
                        'filename_patterns', 'insertion_patterns', 'ad_type_stats',
                        'psychological_profile', 'history_stats', 'marker_stats'];
        foreach ($arrayFields as $field) {
            if (!isset($rules[$field]) || !is_array($rules[$field])) {
                $rules[$field] = [];
            }
        }

        if (!isset($rules['learn_count']) || !is_numeric($rules['learn_count'])) {
            $rules['learn_count'] = 0;
        } else {
            $rules['learn_count'] = intval($rules['learn_count']);
        }

        if (!isset($rules['ad_threshold']) || !is_numeric($rules['ad_threshold'])) {
            $rules['ad_threshold'] = 50;
        } else {
            $rules['ad_threshold'] = intval($rules['ad_threshold']);
        }

        if (!isset($rules['confidence_score']) || !is_numeric($rules['confidence_score'])) {
            $rules['confidence_score'] = 0;
        } else {
            $rules['confidence_score'] = intval($rules['confidence_score']);
        }

        if (!isset($rules['name']) || !is_string($rules['name'])) {
            $rules['name'] = $rules['domain'] ?? '';
        }

        if (!isset($rules['note']) || !is_string($rules['note'])) {
            $rules['note'] = '';
        }

        return $rules;
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

        $ruleData = $this->filterLargeFields($ruleData);

        $file = $this->getRuleFilePath($domain);
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * ' . $domain . ' 域名广告和插播规则' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($ruleData) . ';' . "\n";
        return file_put_contents($file, $content) !== false;
    }

    private function filterLargeFields($data) {
        if (isset($data['history_stats']) && is_array($data['history_stats'])) {
            $slimHistory = [];
            foreach ($data['history_stats'] as $stat) {
                if (!is_array($stat)) continue;
                $slimStat = [
                    'totalCount' => $stat['totalCount'] ?? 0,
                    'adCount' => $stat['adCount'] ?? 0,
                    'adPercentage' => $stat['adPercentage'] ?? 0,
                    'discontinuityCount' => $stat['discontinuityCount'] ?? 0,
                    'cueMarkerCount' => $stat['cueMarkerCount'] ?? 0,
                    'scte35Count' => $stat['scte35Count'] ?? 0,
                    'adTagCount' => $stat['adTagCount'] ?? 0,
                    'confidence' => $stat['confidence'] ?? 0,
                    'analyzed_at' => $stat['analyzed_at'] ?? date('Y-m-d H:i:s'),
                ];
                if (isset($stat['adClusters']) && is_array($stat['adClusters'])) {
                    $slimStat['adClusterCount'] = count($stat['adClusters']);
                }
                if (isset($stat['stats']) && is_array($stat['stats'])) {
                    $slimStat['totalSegments'] = $stat['stats']['totalSegments'] ?? $stat['totalCount'] ?? 0;
                    $slimStat['adSegments'] = $stat['stats']['adSegments'] ?? $stat['adCount'] ?? 0;
                }
                if (isset($stat['psychologicalFeatures']) && is_array($stat['psychologicalFeatures'])) {
                    $slimStat['ad_density'] = $stat['psychologicalFeatures']['ad_density'] ?? 0;
                }
                $slimHistory[] = $slimStat;
            }
            $data['history_stats'] = $slimHistory;
        }

        $bigFields = ['segments', 'adSegments', 'contentSegments', 'allSegments',
                      'segmentDetails', 'rawSegments', 'parsedSegments',
                      'sequence_jump_details', 'jump_details', 'fullAnalysis'];
        foreach ($bigFields as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }

        if (isset($data['analysis_stats']) && is_array($data['analysis_stats'])) {
            $stats = &$data['analysis_stats'];
            unset($stats['segments']);
            unset($stats['adSegmentList']);
            unset($stats['contentSegmentList']);
        }

        return $data;
    }

    public function deleteRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    public function clearAllRules() {
        $count = 0;
        $files = glob($this->gzDir . '/rules_*.php');
        if (!is_array($files)) return $count;
        foreach ($files as $file) {
            if (unlink($file)) {
                $count++;
            }
        }
        return $count;
    }

    public function getRuleCount() {
        $files = glob($this->gzDir . '/rules_*.php');
        return is_array($files) ? count($files) : 0;
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

        // 确保所有需要是数组的字段确实是数组（同时处理 $existing 和 $merged）
        $arrayFields = ['duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
                        'filename_patterns', 'insertion_patterns', 'ad_type_stats',
                        'psychological_profile', 'history_stats', 'marker_stats'];
        foreach ($arrayFields as $field) {
            if (!isset($merged[$field]) || !is_array($merged[$field])) {
                $merged[$field] = [];
            }
            if (!isset($existing[$field]) || !is_array($existing[$field])) {
                $existing[$field] = [];
            }
        }

        $adDurationStats = $this->extractAdDurationStats($analysisResult);
        if (!empty($adDurationStats)) {
            $merged['duration_rules'] = $this->mergeDurationRules(
                $existing['duration_rules'],
                $adDurationStats
            );
        }

        $discoCount = $analysisResult['discontinuityCount'] ?? 0;
        if ($discoCount > 3) {
            $hasDiscoRule = false;
            foreach ($merged['discontinuity_rules'] as $rule) {
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
                $existing['sequence_jump_rules'],
                $seqJumps
            );
        }

        $adFilenames = $this->extractAdFilenamePatterns($analysisResult);
        if (!empty($adFilenames)) {
            $merged['filename_patterns'] = array_unique(array_merge(
                $existing['filename_patterns'],
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
                $existing['insertion_patterns'],
                $insertionPoints
            );
        }

        $adTypes = $analysisResult['adTypes'] ?? null;
        if ($adTypes) {
            $merged['ad_type_stats'] = $this->mergeAdTypeStats(
                $existing['ad_type_stats'],
                $adTypes
            );
        }

        $psychFeatures = $analysisResult['psychologicalFeatures'] ?? null;
        if ($psychFeatures) {
            $merged['psychological_profile'] = $this->mergePsychologicalProfile(
                $existing['psychological_profile'],
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

        $existingMarkerStats = is_array($merged['marker_stats']) ? $merged['marker_stats'] : [];
        $merged['marker_stats'] = [
            'discontinuity_count' => ($existingMarkerStats['discontinuity_count'] ?? 0) + $discoCount,
            'cue_marker_count' => ($existingMarkerStats['cue_marker_count'] ?? 0) + $cueMarkerCount,
            'scte35_count' => ($existingMarkerStats['scte35_count'] ?? 0) + $scte35Count,
            'ad_tag_count' => ($existingMarkerStats['ad_tag_count'] ?? 0) + $adTagCount
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
            if (!isset($result['pre_roll']) || !is_array($result['pre_roll'])) {
                $result['pre_roll'] = [
                    'detected_count' => 0,
                    'avg_duration' => 0,
                    'avg_segment_count' => 0
                ];
            }
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
            if (!isset($result['mid_roll']) || !is_array($result['mid_roll'])) {
                $result['mid_roll'] = [
                    'detected_count' => 0,
                    'avg_clip_count' => 0,
                    'avg_duration_per_clip' => 0,
                    'positions' => []
                ];
            }
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
            if (!isset($result['post_roll']) || !is_array($result['post_roll'])) {
                $result['post_roll'] = [
                    'detected_count' => 0,
                    'avg_duration' => 0,
                    'avg_segment_count' => 0
                ];
            }
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
            if (!isset($result[$key]) || !is_array($result[$key])) {
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
                if (!isset($result[$field]) || !is_array($result[$field])) {
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
            if (!isset($result['pattern_distribution']) || !is_array($result['pattern_distribution'])) {
                $result['pattern_distribution'] = [];
            }
            $pattern = $new['interruption_pattern'];
            if (!isset($result['pattern_distribution'][$pattern])) {
                $result['pattern_distribution'][$pattern] = 0;
            }
            $result['pattern_distribution'][$pattern]++;
        }

        if (!empty($new['user_experience_impact'])) {
            if (!isset($result['ux_impact_distribution']) || !is_array($result['ux_impact_distribution'])) {
                $result['ux_impact_distribution'] = [];
            }
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
                    'weight' => 30,
                    'confidence' => 75,
                    'description' => '过滤极短片段（<2秒），通常为广告或片头'
                ]
            ],
            'discontinuity_rules' => [
                [
                    'name' => 'discontinuity',
                    'enabled' => $analysisResult['discontinuityCount'] > 5,
                    'type' => 'discontinuity',
                    'reason' => 'DISCONTINUITY 标记表示插播切换',
                    'weight' => 80,
                    'confidence' => 90,
                    'description' => '检测 #EXT-X-DISCONTINUITY 标记，用于识别插播内容'
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
                    'weight' => 90,
                    'confidence' => 85,
                    'description' => '序列号向前跳跃检测，识别广告开始'
                ],
                [
                    'name' => 'sequence_jump_backward',
                    'enabled' => count($analysisResult['sequenceJumps']) > 0,
                    'type' => 'sequence_jump',
                    'direction' => 'backward',
                    'threshold' => 100000,
                    'reason' => '序列号向后跳跃可能表示广告结束',
                    'weight' => 90,
                    'confidence' => 85,
                    'description' => '序列号向后跳跃检测，识别广告结束'
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
                'sequenceJumps' => is_array($analysisResult['sequenceJumps'] ?? null) ? count($analysisResult['sequenceJumps']) : 0,
                'adClusters' => is_array($analysisResult['adClusters'] ?? null) ? count($analysisResult['adClusters']) : 0,
                'confidence' => $analysisResult['confidence'] ?? 0
            ],
            'rules' => []
        ];

        // 构建扁平化规则数组，供前端显示
        $rules = [];
        foreach ($defaultRules['duration_rules'] as $r) {
            $rules[] = array_merge($r, ['category' => 'duration']);
        }
        foreach ($defaultRules['discontinuity_rules'] as $r) {
            $rules[] = array_merge($r, ['category' => 'discontinuity']);
        }
        foreach ($defaultRules['sequence_jump_rules'] as $r) {
            $rules[] = array_merge($r, ['category' => 'sequence']);
        }

        // 添加文件名规则
        $rules[] = [
            'name' => 'ad_keyword_filename',
            'enabled' => true,
            'type' => 'filename',
            'category' => 'filename',
            'pattern' => '/(?:ad|advert|commercial|promo|sponsor|pre-roll|mid-roll|post-roll)/i',
            'reason' => '文件名包含广告关键词',
            'weight' => 70,
            'confidence' => 80,
            'description' => '检测文件名中包含广告相关关键词'
        ];
        $rules[] = [
            'name' => 'ad_path_keyword',
            'enabled' => true,
            'type' => 'filename',
            'category' => 'filename',
            'pattern' => '/\/(?:ad|advert|commercial|promo|sponsor)\//i',
            'reason' => '路径包含广告关键词',
            'weight' => 85,
            'confidence' => 90,
            'description' => '检测 URL 路径中包含广告相关目录名'
        ];

        $defaultRules['rules'] = $rules;

        return array_merge($defaultRules, $customConfig);
    }

    public function generateDiscontinuityRegexRules($analysisResult, $segments = []) {
        $rules = [];
        $adClusters = $analysisResult['adClusters'] ?? [];
        $discontinuityCount = $analysisResult['discontinuityCount'] ?? 0;

        if ($discontinuityCount === 0 || empty($adClusters)) {
            return $rules;
        }

        $adDurations = [];
        foreach ($adClusters as $cluster) {
            $clusterDuration = $cluster['duration'] ?? 0;
            $clusterSegments = $cluster['count'] ?? 0;
            if ($clusterSegments >= 2 && $clusterDuration > 0) {
                $adDurations[] = [
                    'duration' => $clusterDuration,
                    'segments' => $clusterSegments,
                    'avg_duration' => round($clusterDuration / $clusterSegments, 3)
                ];
            }
        }

        $hasIntegerDuration = false;
        $hasDecimalDuration = false;
        $specificDurations = [];

        if (!empty($segments)) {
            $inAdCluster = false;
            foreach ($segments as $seg) {
                if (!empty($seg['discontinuity'])) {
                    $inAdCluster = true;
                }
                if ($inAdCluster && isset($seg['duration'])) {
                    $dur = $seg['duration'];
                    $specificDurations[] = $dur;
                    if (floor($dur) == $dur) {
                        $hasIntegerDuration = true;
                    } else {
                        $hasDecimalDuration = true;
                    }
                }
            }
        }

        if (!$hasIntegerDuration && !$hasDecimalDuration) {
            foreach ($adDurations as $ad) {
                $avg = $ad['avg_duration'];
                $specificDurations[] = $avg;
                if (floor($avg) == $avg) {
                    $hasIntegerDuration = true;
                } else {
                    $hasDecimalDuration = true;
                }
            }
        }

        // 基于实际数据的精确规则（最高置信度 100%）
        if (!empty($specificDurations)) {
            $durationCounts = [];
            foreach ($specificDurations as $d) {
                $key = (string)$d;
                $durationCounts[$key] = ($durationCounts[$key] ?? 0) + 1;
            }
            arsort($durationCounts);
            foreach ($durationCounts as $dur => $cnt) {
                if ($cnt >= 2) {
                    $rules[] = [
                        'name' => "discontinuity_exact_{$dur}s",
                        'type' => 'regex',
                        'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:' . $dur . '\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+/',
                        'description' => "精确匹配 {$dur} 秒时长的广告片段（数据驱动，置信度 100%）",
                        'example' => "#EXT-X-DISCONTINUITY\\n#EXTINF:{$dur},\\nad.ts\\n",
                        'match_type' => 'discontinuity_exact',
                        'confidence' => 100,
                        'duration_sources' => $cnt,
                        'exact_duration' => $dur
                    ];
                }
            }
        }

        // 整数时长广告正则规则（暴风模式）
        if ($hasIntegerDuration) {
            $rules[] = [
                'name' => 'discontinuity_integer_duration',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:\\d+\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+/',
                'description' => '整数时长广告片段匹配（暴风模式）',
                'example' => '#EXT-X-DISCONTINUITY\\n#EXTINF:5,\\nad.ts\\n',
                'match_type' => 'discontinuity_group',
                'confidence' => 95
            ];
        }

        // 一位小数时长广告正则规则（暴风模式）
        if ($hasDecimalDuration) {
            $rules[] = [
                'name' => 'discontinuity_decimal_duration',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:\\d+\\.\\d{1}\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+/',
                'description' => '一位小数时长广告片段匹配（暴风模式）',
                'example' => '#EXT-X-DISCONTINUITY\\n#EXTINF:4.3,\\nad.ts\\n',
                'match_type' => 'discontinuity_group',
                'confidence' => 95
            ];
        }

        // 任意时长通用模式
        $rules[] = [
            'name' => 'discontinuity_any_duration',
            'type' => 'regex',
            'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:\\d+(?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+/',
            'description' => '任意时长广告片段匹配（通用模式）',
            'example' => '#EXT-X-DISCONTINUITY\\n#EXTINF:5.0,\\nad.ts\\n',
            'match_type' => 'discontinuity_group',
            'confidence' => 85
        ];

        // 固定片段数精确匹配
        $avgAdSegments = 0;
        foreach ($adDurations as $ad) {
            $avgAdSegments += $ad['segments'];
        }
        $avgAdSegments = count($adDurations) > 0 ? round($avgAdSegments / count($adDurations)) : 3;

        if ($avgAdSegments >= 2 && $avgAdSegments <= 20) {
            $rules[] = [
                'name' => 'discontinuity_fixed_count',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:' . str_repeat('#EXTINF:\\d+(?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?', $avgAdSegments) . ')/',
                'description' => "固定 {$avgAdSegments} 片段广告匹配（精确计数）",
                'example' => "匹配 DISCONTINUITY 后连续 {$avgAdSegments} 个 EXTINF 片段",
                'match_type' => 'discontinuity_group_count',
                'confidence' => 95,
                'expected_count' => $avgAdSegments
            ];
        }

        // 短广告片段匹配
        $shortAdCount = 0;
        $longAdCount = 0;
        foreach ($adDurations as $ad) {
            if ($ad['duration'] < 10) {
                $shortAdCount++;
            } elseif ($ad['duration'] > 30) {
                $longAdCount++;
            }
        }

        if ($shortAdCount > 0) {
            $rules[] = [
                'name' => 'discontinuity_short_ad',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:[0-5](?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?){1,5}/',
                'description' => '短广告片段匹配（<5秒/片，1-5片）',
                'match_type' => 'discontinuity_short',
                'confidence' => 90
            ];
        }

        // 长广告片段匹配
        if ($longAdCount > 0) {
            $rules[] = [
                'name' => 'discontinuity_long_ad',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:\\d{2,}(?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?){2,}/',
                'description' => '长广告片段匹配（>=10秒/片，2片以上）',
                'match_type' => 'discontinuity_long',
                'confidence' => 85
            ];
        }

        // 统一时长匹配
        $uniqueAvgDurations = [];
        foreach ($adDurations as $ad) {
            $key = (string)floor($ad['avg_duration']);
            if (!isset($uniqueAvgDurations[$key])) {
                $uniqueAvgDurations[$key] = 0;
            }
            $uniqueAvgDurations[$key]++;
        }

        if (count($uniqueAvgDurations) === 1) {
            $duration = array_key_first($uniqueAvgDurations);
            $rules[] = [
                'name' => "discontinuity_uniform_{$duration}s",
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:' . $duration . '(?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+/',
                'description' => "统一时长约 {$duration} 秒的广告片段匹配",
                'match_type' => 'discontinuity_uniform',
                'confidence' => 98,
                'uniform_duration' => $duration
            ];
        }

        // DISCONTINUITY 对匹配（广告簇边界）
        if ($discontinuityCount >= 2) {
            $rules[] = [
                'name' => 'discontinuity_pair',
                'type' => 'regex',
                'pattern' => '/#EXT-X-DISCONTINUITY\\r?\\n(?:#EXTINF:\\d+(?:\\.\\d+)?\\,\\r?\\n[^\\r\\n]+\\r?\\n?)+?#EXT-X-DISCONTINUITY/',
                'description' => '匹配两个 DISCONTINUITY 标记之间的所有片段（广告簇完整匹配）',
                'example' => '#EXT-X-DISCONTINUITY\\n#EXTINF:5,\\nad1.ts\\n#EXTINF:5,\\nad2.ts\\n#EXT-X-DISCONTINUITY',
                'match_type' => 'discontinuity_pair',
                'confidence' => 98,
                'discontinuity_pair_count' => floor($discontinuityCount / 2)
            ];
        }

        return $rules;
    }

    public function analyzeAdClustersDetail($analysisResult, $segments = []) {
        $clusters = $analysisResult['adClusters'] ?? [];
        $totalCount = $analysisResult['totalCount'] ?? 0;
        $details = [];

        foreach ($clusters as $idx => $cluster) {
            $startIdx = $cluster['start'] ?? 0;
            $endIdx = $cluster['end'] ?? 0;
            $count = $cluster['count'] ?? ($endIdx - $startIdx + 1);
            $duration = $cluster['duration'] ?? 0;

            $startRatio = $totalCount > 0 ? round($startIdx / $totalCount * 100, 1) : 0;
            $endRatio = $totalCount > 0 ? round(($endIdx + 1) / $totalCount * 100, 1) : 0;

            $position = 'middle';
            $positionLabel = '中间插播';
            if ($startRatio < 15) {
                $position = 'opening';
                $positionLabel = '片头广告';
            } elseif ($endRatio > 85) {
                $position = 'ending';
                $positionLabel = '片尾广告';
            }

            $clusterSegments = [];
            $discontinuityPositions = [];
            $discontinuityCount = 0;
            if (!empty($segments) && is_array($segments)) {
                for ($i = $startIdx; $i <= $endIdx && $i < count($segments); $i++) {
                    $seg = $segments[$i];
                    $hasDisc = !empty($seg['discontinuity']);
                    if ($hasDisc) {
                        $discontinuityCount++;
                        $discontinuityPositions[] = $i;
                    }
                    $clusterSegments[] = [
                        'index' => $i,
                        'uri' => $seg['uri'] ?? '',
                        'duration' => $seg['duration'] ?? 0,
                        'discontinuity' => $hasDisc,
                        'media_sequence' => $seg['mediaSequence'] ?? null,
                        'title' => $seg['title'] ?? ''
                    ];
                }
            }

            $hasDiscontinuity = $discontinuityCount > 0;
            if (!$hasDiscontinuity && !empty($clusterSegments)) {
                $hasDiscontinuity = !empty($cluster['has_marker']);
            } else if (!$hasDiscontinuity) {
                $hasDiscontinuity = !empty($cluster['has_marker']);
            }

            // 计算置信度：综合 DISCONTINUITY 数量、时长特征、位置等
            $confidence = 70;
            if ($discontinuityCount > 0) {
                $confidence += $discontinuityCount * 10;
            }
            if ($count >= 2) {
                $confidence += 10;
            }
            if ($position === 'opening' || $position === 'ending') {
                $confidence += 5;
            }
            $confidence = min(100, $confidence);

            $details[] = [
                'index' => $idx,
                'start_index' => $startIdx,
                'end_index' => $endIdx,
                'segment_count' => $count,
                'duration' => round($duration, 2),
                'avg_segment_duration' => $count > 0 ? round($duration / $count, 3) : 0,
                'start_position_percent' => $startRatio,
                'end_position_percent' => $endRatio,
                'position_type' => $position,
                'position_label' => $positionLabel,
                'has_discontinuity' => $hasDiscontinuity,
                'discontinuity_count' => $discontinuityCount,
                'discontinuity_positions' => $discontinuityPositions,
                'confidence' => $confidence,
                'segments' => $clusterSegments
            ];
        }

        return $details;
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
