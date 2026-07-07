<?php

require_once __DIR__ . '/Database.php';

class DbDomainRuleManager {

    private $db;
    private $tableName = 'domain_rules';

    public function __construct() {
        $this->db = Database::getInstance();
        $this->initTable();
    }

    private function initTable() {
        if ($this->db->tableExists($this->tableName)) {
            return;
        }

        $dbType = $this->db->getDbType();

        if ($dbType === 'mysql') {
            $sql = "CREATE TABLE {$this->tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                domain VARCHAR(191) NOT NULL UNIQUE,
                name VARCHAR(255) DEFAULT '',
                note TEXT,
                learn_count INT DEFAULT 0,
                ad_threshold INT DEFAULT 50,
                confidence_score INT DEFAULT 0,
                analysis_date DATETIME,
                last_learn_date DATETIME,
                duration_rules TEXT,
                discontinuity_rules TEXT,
                sequence_jump_rules TEXT,
                filename_patterns TEXT,
                insertion_patterns TEXT,
                ad_type_stats TEXT,
                psychological_profile TEXT,
                marker_stats TEXT,
                analysis_stats TEXT,
                marker_detection TEXT,
                confidence TEXT,
                enable_marker_detection TINYINT(1) DEFAULT 0,
                history_stats MEDIUMTEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_domain (domain)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        } else {
            $sql = "CREATE TABLE {$this->tableName} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                domain TEXT NOT NULL UNIQUE,
                name TEXT DEFAULT '',
                note TEXT,
                learn_count INTEGER DEFAULT 0,
                ad_threshold INTEGER DEFAULT 50,
                confidence_score INTEGER DEFAULT 0,
                analysis_date TEXT,
                last_learn_date TEXT,
                duration_rules TEXT,
                discontinuity_rules TEXT,
                sequence_jump_rules TEXT,
                filename_patterns TEXT,
                insertion_patterns TEXT,
                ad_type_stats TEXT,
                psychological_profile TEXT,
                marker_stats TEXT,
                analysis_stats TEXT,
                marker_detection TEXT,
                confidence TEXT,
                enable_marker_detection INTEGER DEFAULT 0,
                history_stats TEXT,
                created_at TEXT DEFAULT (datetime('now')),
                updated_at TEXT DEFAULT (datetime('now'))
            )";
        }

        $this->db->execute($sql);
    }

    private function jsonEncode($data) {
        if ($data === null || !is_array($data)) {
            return null;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function jsonDecode($data) {
        if ($data === null || $data === '') {
            return [];
        }
        $decoded = json_decode($data, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function rowToRules($row) {
        if (!$row) {
            return null;
        }

        $jsonFields = [
            'duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
            'filename_patterns', 'insertion_patterns', 'ad_type_stats',
            'psychological_profile', 'marker_stats', 'analysis_stats',
            'marker_detection', 'confidence', 'history_stats'
        ];

        $rules = [];
        foreach ($row as $key => $value) {
            if ($key === 'id' || $key === 'created_at' || $key === 'updated_at') {
                continue;
            }
            if ($key === 'enable_marker_detection') {
                $rules[$key] = (bool)$value;
                continue;
            }
            if (in_array($key, $jsonFields)) {
                $rules[$key] = $this->jsonDecode($value);
            } else {
                $rules[$key] = $value;
            }
        }

        $rules = $this->normalizeRules($rules);
        $rules['_filename'] = 'db_' . $row['id'];
        $rules['_filemtime'] = strtotime($row['updated_at']);

        return $rules;
    }

    private function rulesToRow($rules) {
        $jsonFields = [
            'duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
            'filename_patterns', 'insertion_patterns', 'ad_type_stats',
            'psychological_profile', 'marker_stats', 'analysis_stats',
            'marker_detection', 'confidence', 'history_stats'
        ];

        $row = [];
        foreach ($rules as $key => $value) {
            if ($key === '_filename' || $key === '_filemtime' || $key === 'id' ||
                $key === 'created_at' || $key === 'updated_at') {
                continue;
            }
            if ($key === 'enable_marker_detection') {
                $row[$key] = $value ? 1 : 0;
                continue;
            }
            if (in_array($key, $jsonFields)) {
                $row[$key] = $this->jsonEncode($value);
            } else {
                $row[$key] = $value;
            }
        }

        return $row;
    }

    public function getAllRules() {
        $rules = [];
        try {
            $rows = $this->db->query("SELECT * FROM {$this->tableName} ORDER BY id");
            foreach ($rows as $row) {
                $domainRules = $this->rowToRules($row);
                if ($domainRules && !empty($domainRules['domain'])) {
                    $rules[$domainRules['domain']] = $domainRules;
                }
            }
        } catch (Throwable $e) {
            error_log('加载规则失败: ' . $e->getMessage());
        }
        return $rules;
    }

    public function getAllRulesLite() {
        $rules = [];
        try {
            $rows = $this->db->query("SELECT * FROM {$this->tableName} ORDER BY id");
            foreach ($rows as $row) {
                $domainRules = $this->rowToRules($row);
                if ($domainRules && !empty($domainRules['domain'])) {
                    $lite = [
                        'domain' => $domainRules['domain'],
                        'name' => $domainRules['name'] ?? $domainRules['domain'],
                        'note' => $domainRules['note'] ?? '',
                        'learn_count' => intval($domainRules['learn_count'] ?? 0),
                        'ad_threshold' => intval($domainRules['ad_threshold'] ?? 50),
                        'confidence_score' => intval($domainRules['confidence_score'] ?? 0),
                        'analysis_date' => $domainRules['analysis_date'] ?? '',
                        'last_learn_date' => $domainRules['last_learn_date'] ?? '',
                        '_filename' => $domainRules['_filename'],
                        '_filemtime' => $domainRules['_filemtime'],
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
            }
        } catch (Throwable $e) {
            error_log('加载规则失败: ' . $e->getMessage());
        }
        return $rules;
    }

    public function getRules($domain) {
        try {
            $row = $this->db->queryOne(
                "SELECT * FROM {$this->tableName} WHERE domain = ?",
                [$domain]
            );
            if ($row) {
                return $this->rowToRules($row);
            }
        } catch (Throwable $e) {
            error_log('获取规则失败: ' . $e->getMessage());
        }
        return null;
    }

    public function normalizeRules($rules) {
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
        try {
            $ruleData['domain'] = $domain;
            if (!isset($ruleData['analysis_date'])) {
                $ruleData['analysis_date'] = date('Y-m-d H:i:s');
            }
            if (!isset($ruleData['learn_count'])) {
                $ruleData['learn_count'] = 1;
            }
            $ruleData['last_learn_date'] = date('Y-m-d H:i:s');

            $existing = $this->db->queryOne(
                "SELECT id FROM {$this->tableName} WHERE domain = ?",
                [$domain]
            );

            $row = $this->rulesToRow($ruleData);
            $row['updated_at'] = date('Y-m-d H:i:s');

            if ($existing) {
                $this->db->update(
                    $this->tableName,
                    $row,
                    'domain = :where_domain',
                    [':where_domain' => $domain]
                );
            } else {
                $row['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert($this->tableName, $row);
            }

            return true;
        } catch (Throwable $e) {
            error_log('保存规则失败: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteRules($domain) {
        try {
            $existing = $this->db->queryOne(
                "SELECT id FROM {$this->tableName} WHERE domain = ?",
                [$domain]
            );
            if (!$existing) {
                return false;
            }
            $this->db->delete(
                $this->tableName,
                'domain = ?',
                [$domain]
            );
            return true;
        } catch (Throwable $e) {
            error_log('删除规则失败: ' . $e->getMessage());
            return false;
        }
    }

    public function clearAllRules() {
        try {
            $count = $this->db->count($this->tableName);
            $this->db->execute("DELETE FROM {$this->tableName}");
            return $count;
        } catch (Throwable $e) {
            error_log('清空规则失败: ' . $e->getMessage());
            return 0;
        }
    }

    public function getRuleCount() {
        try {
            return $this->db->count($this->tableName);
        } catch (Throwable $e) {
            error_log('获取规则数量失败: ' . $e->getMessage());
            return 0;
        }
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
                'sequenceJumps' => is_array($analysisResult['sequenceJumps'] ?? null) ? count($analysisResult['sequenceJumps']) : 0,
                'adClusters' => is_array($analysisResult['adClusters'] ?? null) ? count($analysisResult['adClusters']) : 0,
                'confidence' => $analysisResult['confidence'] ?? 0
            ]
        ];
        return array_merge($defaultRules, $customConfig);
    }
}
