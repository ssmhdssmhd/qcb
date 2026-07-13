<?php
/**
 * 专业广告检测器 (ProfessionalAdDetector)
 *
 * 基于多维度特征融合的广告识别引擎，大幅提升识别准确率：
 *
 * 1. 统计学异常检测：Z-Score + IQR 双重检测，识别时长异常的片段
 * 2. 时长分布聚类：基于 K-Means 思想的时长聚类，区分广告群和内容群
 * 3. DISCONTINUITY 上下文分析：结合前后片段特征精确界定广告边界
 * 4. URI 结构特征分析：文件名编号连续性、路径深度、命名规律
 * 5. 序列号突变检测：识别非连续的序列号跳跃
 * 6. 多维度置信度评分：融合所有特征，0-100 分制评分
 * 7. 广告簇边界精确识别：避免误删内容片段
 * 8. 误判保护机制：内容片段占比下限保护
 */

require_once __DIR__ . '/../src/M3U8Parser.php';

class ProfessionalAdDetector {

    private $segments = [];
    private $analysisResult = [];
    private $options = [];

    public function __construct($options = []) {
        $this->options = array_merge([
            'zscore_threshold' => 2.0,        // Z-Score 异常阈值
            'iqr_multiplier' => 1.5,          // IQR 倍数
            'min_content_ratio' => 0.3,       // 最小内容占比保护
            'min_ad_cluster_size' => 2,       // 最小广告簇大小
            'duration_tolerance' => 0.3,      // 时长容差
            'confidence_threshold' => 55,     // 广告置信度阈值
            'enable_boundary_refinement' => true, // 边界精细识别
        ], $options);
    }

    /**
     * 对片段进行专业级广告检测
     */
    public function detect($segments) {
        $this->segments = $segments;
        $total = count($segments);

        if ($total === 0) {
            return $this->emptyResult();
        }

        // 第一步：提取每个片段的多维度特征
        $features = $this->extractFeatures($segments);

        // 第二步：统计学异常检测
        $statisticalAnomalies = $this->detectStatisticalAnomalies($features);

        // 第三步：时长分布聚类分析
        $durationClusters = $this->clusterByDuration($features);

        // 第四步：DISCONTINUITY 上下文分析
        $discontinuityContext = $this->analyzeDiscontinuityContext($segments, $features);

        // 第五步：URI 结构特征分析
        $uriStructuralFeatures = $this->analyzeUriStructure($segments);

        // 第六步：序列号突变检测
        $sequenceAnomalies = $this->detectSequenceAnomalies($segments);

        // 第七步：多维度置信度评分
        $scoredSegments = $this->scoreSegments($features, $statisticalAnomalies, $durationClusters,
                                               $discontinuityContext, $uriStructuralFeatures, $sequenceAnomalies);

        // 第八步：广告簇识别与边界精修
        $adClusters = $this->identifyAdClusters($scoredSegments, $discontinuityContext);

        // 第九步：误判保护
        $adClusters = $this->applySafeguard($adClusters, $scoredSegments);

        // 构建最终结果
        return $this->buildResult($scoredSegments, $adClusters, $features, [
            'statistical' => $statisticalAnomalies,
            'duration_clusters' => $durationClusters,
            'discontinuity' => $discontinuityContext,
            'uri_structure' => $uriStructuralFeatures,
            'sequence' => $sequenceAnomalies,
        ]);
    }

    /**
     * 提取每个片段的多维度特征
     */
    private function extractFeatures($segments) {
        $features = [];
        $durations = [];

        foreach ($segments as $i => $seg) {
            $duration = $seg['duration'] ?? 0;
            $durations[] = $duration;

            $features[$i] = [
                'index' => $i,
                'duration' => $duration,
                'uri' => $seg['uri'] ?? '',
                'has_discontinuity' => !empty($seg['discontinuity']),
                'media_sequence' => $seg['mediaSequence'] ?? null,
                'filename' => basename($seg['uri'] ?? ''),
                'path_parts' => $this->parsePath($seg['uri'] ?? ''),
                'file_number' => $this->extractFileNumber($seg['uri'] ?? ''),
                'is_first' => $i === 0,
                'is_last' => $i === count($segments) - 1,
            ];
        }

        // 计算全局统计特征
        $stats = $this->calculateStatistics($durations);
        foreach ($features as $i => &$f) {
            $f['global_stats'] = $stats;
            $f['prev_duration'] = $i > 0 ? ($features[$i - 1]['duration'] ?? 0) : null;
            $f['next_duration'] = $i < count($features) - 1 ? ($segments[$i + 1]['duration'] ?? 0) : null;
            $f['duration_diff_prev'] = $f['prev_duration'] !== null
                ? abs($f['duration'] - $f['prev_duration']) : null;
        }

        return $features;
    }

    /**
     * 统计学异常检测（Z-Score + IQR 双重检测）
     */
    private function detectStatisticalAnomalies($features) {
        $durations = array_column($features, 'duration');
        $stats = $this->calculateStatistics($durations);

        $anomalies = [];

        foreach ($features as $i => $f) {
            $duration = $f['duration'];
            $reasons = [];
            $score = 0;

            // Z-Score 检测
            if ($stats['stddev'] > 0) {
                $zscore = abs($duration - $stats['mean']) / $stats['stddev'];
                if ($zscore >= $this->options['zscore_threshold']) {
                    $reasons[] = 'zscore_anomaly';
                    $score += min(30, $zscore * 10);
                }
            }

            // IQR 检测
            if ($stats['q1'] !== null && $stats['q3'] !== null) {
                $iqr = $stats['q3'] - $stats['q1'];
                $lowerBound = $stats['q1'] - $this->options['iqr_multiplier'] * $iqr;
                $upperBound = $stats['q3'] + $this->options['iqr_multiplier'] * $iqr;

                if ($duration < $lowerBound) {
                    $reasons[] = 'iqr_low_outlier';
                    $score += 25;
                } elseif ($duration > $upperBound) {
                    $reasons[] = 'iqr_high_outlier';
                    $score += 20;
                }
            }

            // 与主时长分布偏离
            if ($stats['mode'] !== null && $stats['mode'] > 0) {
                $deviation = abs($duration - $stats['mode']) / $stats['mode'];
                if ($deviation > 0.5) {
                    $reasons[] = 'mode_deviation';
                    $score += 15;
                }
            }

            if (!empty($reasons)) {
                $anomalies[$i] = [
                    'reasons' => $reasons,
                    'score' => min(40, $score),
                    'zscore' => $zscore ?? 0,
                    'duration' => $duration,
                ];
            }
        }

        return $anomalies;
    }

    /**
     * 时长分布聚类分析（区分广告群和内容群）
     */
    private function clusterByDuration($features) {
        $durations = [];
        foreach ($features as $f) {
            if ($f['duration'] > 0) {
                $durations[] = $f['duration'];
            }
        }

        if (count($durations) < 3) {
            return ['clusters' => [], 'ad_cluster' => null, 'content_cluster' => null];
        }

        // 简化的 K-Means（K=2）：找出两个聚类中心
        sort($durations);
        $n = count($durations);

        // 初始中心：最小值和最大值
        $c1 = $durations[0];
        $c2 = $durations[$n - 1];

        // 迭代优化
        for ($iter = 0; $iter < 10; $iter++) {
            $cluster1 = [];
            $cluster2 = [];

            foreach ($durations as $d) {
                if (abs($d - $c1) <= abs($d - $c2)) {
                    $cluster1[] = $d;
                } else {
                    $cluster2[] = $d;
                }
            }

            $newC1 = empty($cluster1) ? $c1 : array_sum($cluster1) / count($cluster1);
            $newC2 = empty($cluster2) ? $c2 : array_sum($cluster2) / count($cluster2);

            if (abs($newC1 - $c1) < 0.01 && abs($newC2 - $c2) < 0.01) {
                break;
            }

            $c1 = $newC1;
            $c2 = $newC2;
        }

        // 判断哪个是广告群（通常广告片段更短且数量较少）
        $cluster1Stats = $this->calculateStatistics($cluster1);
        $cluster2Stats = $this->calculateStatistics($cluster2);

        $adCluster = null;
        $contentCluster = null;

        if (count($cluster1) < count($cluster2)) {
            $adCluster = ['center' => $c1, 'count' => count($cluster1), 'stats' => $cluster1Stats];
            $contentCluster = ['center' => $c2, 'count' => count($cluster2), 'stats' => $cluster2Stats];
        } else {
            $adCluster = ['center' => $c2, 'count' => count($cluster2), 'stats' => $cluster2Stats];
            $contentCluster = ['center' => $c1, 'count' => count($cluster1), 'stats' => $cluster1Stats];
        }

        // 为每个片段标记所属聚类
        $segmentClusters = [];
        foreach ($features as $i => $f) {
            $d = $f['duration'];
            if (abs($d - $adCluster['center']) <= abs($d - $contentCluster['center'])) {
                $segmentClusters[$i] = 'ad';
            } else {
                $segmentClusters[$i] = 'content';
            }
        }

        return [
            'clusters' => $segmentClusters,
            'ad_cluster' => $adCluster,
            'content_cluster' => $contentCluster,
        ];
    }

    /**
     * DISCONTINUITY 上下文分析
     */
    private function analyzeDiscontinuityContext($segments, $features) {
        $discontinuityPoints = [];
        $contextAnalysis = [];

        // 找出所有 DISCONTINUITY 点
        foreach ($features as $i => $f) {
            if ($f['has_discontinuity']) {
                $discontinuityPoints[] = $i;
            }
        }

        // 分析每个 DISCONTINUITY 点的上下文
        foreach ($discontinuityPoints as $point) {
            $beforeDuration = $point > 0 ? ($features[$point - 1]['duration'] ?? 0) : 0;
            $atDuration = $features[$point]['duration'] ?? 0;
            $afterDuration = ($point < count($features) - 1) ? ($features[$point + 1]['duration'] ?? 0) : 0;

            // 前后时长差异
            $diffBefore = $beforeDuration > 0 ? abs($atDuration - $beforeDuration) / $beforeDuration : 0;
            $diffAfter = $afterDuration > 0 ? abs($atDuration - $afterDuration) / $afterDuration : 0;

            $contextAnalysis[$point] = [
                'before_duration' => $beforeDuration,
                'at_duration' => $atDuration,
                'after_duration' => $afterDuration,
                'diff_before_ratio' => round($diffBefore, 3),
                'diff_after_ratio' => round($diffAfter, 3),
                'is_ad_boundary' => $diffBefore > 0.3 || $diffAfter > 0.3,
            ];
        }

        // 识别 DISCONTINUITY 对（广告簇的开始和结束）
        $adRanges = [];
        $inAdRange = false;
        $rangeStart = -1;

        for ($i = 0; $i < count($features); $i++) {
            if ($features[$i]['has_discontinuity']) {
                if (!$inAdRange) {
                    // DISCONTINUITY 后的片段可能是广告开始
                    $inAdRange = true;
                    $rangeStart = $i;
                } else {
                    // 第二个 DISCONTINUITY 可能是广告结束
                    $adRanges[] = [
                        'start' => $rangeStart,
                        'end' => $i - 1,
                        'duration' => array_sum(array_column(array_slice($features, $rangeStart, $i - $rangeStart), 'duration')),
                        'segment_count' => $i - $rangeStart,
                    ];
                    $inAdRange = false;
                }
            }
        }

        // 如果最后还在广告范围内，闭合
        if ($inAdRange && $rangeStart >= 0) {
            $adRanges[] = [
                'start' => $rangeStart,
                'end' => count($features) - 1,
                'duration' => array_sum(array_column(array_slice($features, $rangeStart), 'duration')),
                'segment_count' => count($features) - $rangeStart,
            ];
        }

        return [
            'points' => $discontinuityPoints,
            'context' => $contextAnalysis,
            'ad_ranges' => $adRanges,
            'count' => count($discontinuityPoints),
        ];
    }

    /**
     * URI 结构特征分析
     */
    private function analyzeUriStructure($segments) {
        $features = [];
        $prevNumber = null;
        $pathDepths = [];

        foreach ($segments as $i => $seg) {
            $uri = $seg['uri'] ?? '';
            $number = $this->extractFileNumber($uri);
            $pathDepth = substr_count(parse_url($uri, PHP_URL_PATH) ?: $uri, '/');
            $pathDepths[] = $pathDepth;

            $features[$i] = [
                'file_number' => $number,
                'path_depth' => $pathDepth,
                'number_gap' => ($prevNumber !== null && $number !== null) ? abs($number - $prevNumber) : 0,
                'filename_length' => strlen(basename($uri)),
                'has_ad_keyword' => $this->hasAdKeyword($uri),
            ];

            $prevNumber = $number;
        }

        // 统计路径深度分布
        $depthStats = $this->calculateStatistics($pathDepths);

        // 识别编号异常
        $numberAnomalies = [];
        foreach ($features as $i => $f) {
            if ($f['number_gap'] > 1 && $f['number_gap'] < 1000) {
                $numberAnomalies[$i] = [
                    'gap' => $f['number_gap'],
                    'reason' => 'number_gap',
                ];
            }
        }

        return [
            'segment_features' => $features,
            'number_anomalies' => $numberAnomalies,
            'path_depth_stats' => $depthStats,
        ];
    }

    /**
     * 序列号突变检测
     */
    private function detectSequenceAnomalies($segments) {
        $anomalies = [];
        $prevSeq = null;

        foreach ($segments as $i => $seg) {
            $seq = $seg['mediaSequence'] ?? null;
            if ($seq === null) continue;

            if ($prevSeq !== null) {
                $diff = $seq - $prevSeq;
                if ($diff > 1) {
                    $anomalies[$i] = [
                        'diff' => $diff,
                        'reason' => 'sequence_jump',
                        'score' => min(25, $diff * 5),
                    ];
                } elseif ($diff < 0) {
                    $anomalies[$i] = [
                        'diff' => $diff,
                        'reason' => 'sequence_backward',
                        'score' => 30,
                    ];
                }
            }
            $prevSeq = $seq;
        }

        return $anomalies;
    }

    /**
     * 多维度置信度评分
     */
    private function scoreSegments($features, $statisticalAnomalies, $durationClusters,
                                   $discontinuityContext, $uriStructure, $sequenceAnomalies) {
        $scored = [];

        foreach ($features as $i => $f) {
            $score = 0;
            $reasons = [];
            $categories = [];

            // 1. 统计学异常评分 (0-40)
            if (isset($statisticalAnomalies[$i])) {
                $score += $statisticalAnomalies[$i]['score'];
                $reasons = array_merge($reasons, $statisticalAnomalies[$i]['reasons']);
                $categories[] = 'statistical';
            }

            // 2. 时长聚类评分 (0-25)
            if (isset($durationClusters['clusters'][$i]) && $durationClusters['clusters'][$i] === 'ad') {
                $score += 25;
                $reasons[] = 'duration_cluster_ad';
                $categories[] = 'duration_cluster';
            }

            // 3. DISCONTINUITY 上下文评分 (0-30)
            foreach ($discontinuityContext['ad_ranges'] as $range) {
                if ($i >= $range['start'] && $i <= $range['end']) {
                    $score += 30;
                    $reasons[] = 'in_discontinuity_range';
                    $categories[] = 'discontinuity';
                    break;
                }
            }

            // 4. URI 结构异常评分 (0-20)
            if (isset($uriStructure['number_anomalies'][$i])) {
                $score += 15;
                $reasons[] = 'uri_number_gap';
                $categories[] = 'uri_structure';
            }

            // 5. 广告关键词评分 (0-30)
            if (isset($uriStructure['segment_features'][$i]['has_ad_keyword']) &&
                $uriStructure['segment_features'][$i]['has_ad_keyword']) {
                $score += 30;
                $reasons[] = 'ad_keyword';
                $categories[] = 'keyword';
            }

            // 6. 序列号异常评分 (0-25)
            if (isset($sequenceAnomalies[$i])) {
                $score += $sequenceAnomalies[$i]['score'];
                $reasons[] = $sequenceAnomalies[$i]['reason'];
                $categories[] = 'sequence';
            }

            // 7. 边界片段特征评分
            if ($f['has_discontinuity']) {
                $score += 10;
                $reasons[] = 'discontinuity_marker';
                $categories[] = 'marker';
            }

            // 8. 与相邻片段的时长差异
            if ($f['duration_diff_prev'] !== null && $f['duration_diff_prev'] > 0) {
                $prevDur = $f['prev_duration'];
                if ($prevDur > 0) {
                    $ratio = $f['duration_diff_prev'] / $prevDur;
                    if ($ratio > 0.5) {
                        $score += 10;
                        $reasons[] = 'duration_jump_from_prev';
                        $categories[] = 'context';
                    }
                }
            }

            $score = min(100, $score);
            $isAd = $score >= $this->options['confidence_threshold'];

            $scored[$i] = [
                'index' => $i,
                'score' => round($score, 1),
                'is_ad' => $isAd,
                'reasons' => array_unique($reasons),
                'categories' => array_unique($categories),
                'duration' => $f['duration'],
                'uri' => $f['uri'],
                'confidence' => $this->getConfidenceLabel($score),
            ];
        }

        return $scored;
    }

    /**
     * 广告簇识别与边界精修
     */
    private function identifyAdClusters($scoredSegments, $discontinuityContext) {
        $clusters = [];
        $currentCluster = null;

        foreach ($scoredSegments as $i => $seg) {
            if ($seg['is_ad']) {
                if ($currentCluster === null) {
                    $currentCluster = [
                        'start' => $i,
                        'end' => $i,
                        'segments' => [$i],
                        'total_score' => $seg['score'],
                        'reasons' => $seg['reasons'],
                    ];
                } else {
                    $currentCluster['end'] = $i;
                    $currentCluster['segments'][] = $i;
                    $currentCluster['total_score'] += $seg['score'];
                    $currentCluster['reasons'] = array_unique(array_merge($currentCluster['reasons'], $seg['reasons']));
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

        // 边界精修：如果广告簇中夹有低分片段，检查是否应该合并
        if ($this->options['enable_boundary_refinement']) {
            $clusters = $this->refineBoundaries($clusters, $scoredSegments);
        }

        // 过滤太小的簇
        $clusters = array_filter($clusters, function($c) {
            return count($c['segments']) >= $this->options['min_ad_cluster_size'];
        });

        // 添加位置信息和详细统计
        $total = count($this->segments);
        foreach ($clusters as &$cluster) {
            $cluster['segment_count'] = count($cluster['segments']);
            $cluster['avg_score'] = round($cluster['total_score'] / $cluster['segment_count'], 1);
            $cluster['total_duration'] = array_sum(array_map(function($i) {
                return $this->segments[$i]['duration'] ?? 0;
            }, $cluster['segments']));
            $cluster['avg_duration'] = $cluster['segment_count'] > 0
                ? round($cluster['total_duration'] / $cluster['segment_count'], 3) : 0;
            $cluster['position'] = $this->getPositionLabel($cluster['start'], $cluster['end'], $total);
            $cluster['position_type'] = $this->getPositionType($cluster['start'], $cluster['end'], $total);
        }

        return array_values($clusters);
    }

    /**
     * 边界精修：合并被单个内容片段分隔的广告簇
     */
    private function refineBoundaries($clusters, $scoredSegments) {
        if (count($clusters) < 2) return $clusters;

        $refined = [];
        $i = 0;

        while ($i < count($clusters)) {
            $current = $clusters[$i];
            $merged = false;

            if ($i + 1 < count($clusters)) {
                $next = $clusters[$i + 1];
                $gap = $next['start'] - $current['end'] - 1;

                // 如果两个广告簇之间只隔1-2个片段，且这些片段评分接近阈值，则合并
                if ($gap >= 1 && $gap <= 2) {
                    $shouldMerge = true;
                    for ($j = $current['end'] + 1; $j < $next['start']; $j++) {
                        if (isset($scoredSegments[$j]) && $scoredSegments[$j]['score'] < 35) {
                            $shouldMerge = false;
                            break;
                        }
                    }

                    if ($shouldMerge) {
                        $mergedCluster = [
                            'start' => $current['start'],
                            'end' => $next['end'],
                            'segments' => array_merge($current['segments'],
                                range($current['end'] + 1, $next['start'] - 1),
                                $next['segments']),
                            'total_score' => $current['total_score'] + $next['total_score'],
                            'reasons' => array_unique(array_merge($current['reasons'], $next['reasons'])),
                        ];
                        $refined[] = $mergedCluster;
                        $i += 2;
                        $merged = true;
                    }
                }
            }

            if (!$merged) {
                $refined[] = $current;
                $i++;
            }
        }

        return $refined;
    }

    /**
     * 误判保护：确保不会误删过多内容
     */
    private function applySafeguard($adClusters, $scoredSegments) {
        $totalSegments = count($scoredSegments);
        if ($totalSegments === 0 || empty($adClusters)) {
            return $adClusters;
        }

        $adSegmentCount = 0;
        foreach ($adClusters as $cluster) {
            $adSegmentCount += $cluster['segment_count'];
        }

        $adRatio = $adSegmentCount / $totalSegments;

        // 如果广告占比超过保护阈值，移除置信度最低的广告簇
        if ($adRatio > (1 - $this->options['min_content_ratio'])) {
            usort($adClusters, function($a, $b) {
                return $a['avg_score'] <=> $b['avg_score'];
            });

            while (!empty($adClusters) && $adRatio > (1 - $this->options['min_content_ratio'])) {
                $removed = array_shift($adClusters);
                $adSegmentCount -= $removed['segment_count'];
                $adRatio = $adSegmentCount / $totalSegments;
            }

            usort($adClusters, function($a, $b) {
                return $a['start'] <=> $b['start'];
            });
        }

        return $adClusters;
    }

    // ===== 辅助方法 =====

    private function calculateStatistics($values) {
        $values = array_filter($values, function($v) { return $v !== null; });
        if (empty($values)) {
            return ['mean' => 0, 'median' => 0, 'stddev' => 0, 'min' => 0, 'max' => 0,
                    'q1' => null, 'q3' => null, 'mode' => null];
        }

        sort($values);
        $n = count($values);
        $sum = array_sum($values);
        $mean = $sum / $n;

        $variance = 0;
        foreach ($values as $v) {
            $variance += ($v - $mean) ** 2;
        }
        $stddev = $n > 1 ? sqrt($variance / ($n - 1)) : 0;

        $median = $n % 2 === 0
            ? ($values[$n / 2 - 1] + $values[$n / 2]) / 2
            : $values[(int)($n / 2)];

        $q1Index = (int)($n * 0.25);
        $q3Index = (int)($n * 0.75);

        // 众数
        $counts = [];
        foreach ($values as $v) {
            $key = (string)round($v, 1);
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        arsort($counts);
        $mode = !empty($counts) ? (float)array_key_first($counts) : null;

        return [
            'mean' => round($mean, 4),
            'median' => round($median, 4),
            'stddev' => round($stddev, 4),
            'min' => $values[0],
            'max' => $values[$n - 1],
            'q1' => $values[$q1Index] ?? null,
            'q3' => $values[$q3Index] ?? null,
            'mode' => $mode !== null ? (float)$mode : null,
        ];
    }

    private function parsePath($uri) {
        $path = parse_url($uri, PHP_URL_PATH) ?: $uri;
        return array_values(array_filter(explode('/', $path), function($p) { return $p !== ''; }));
    }

    private function extractFileNumber($uri) {
        $filename = basename($uri);
        if (preg_match('/(\d+)/', $filename, $matches)) {
            return intval($matches[1]);
        }
        return null;
    }

    private function hasAdKeyword($uri) {
        $patterns = [
            '/\/ad[s]?\//i', '/advert/i', '/commercial/i', '/promo/i', '/sponsor/i',
            '/pre[-_]?roll/i', '/mid[-_]?roll/i', '/post[-_]?roll/i',
            '/\/ad\//i', '/adzone/i', '/adjump/i',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $uri)) return true;
        }
        return false;
    }

    private function getConfidenceLabel($score) {
        if ($score >= 80) return 'very_high';
        if ($score >= 65) return 'high';
        if ($score >= 55) return 'medium';
        if ($score >= 40) return 'low';
        return 'very_low';
    }

    private function getPositionLabel($start, $end, $total) {
        if ($start === 0) return '片头广告';
        if ($end >= $total - 1) return '片尾广告';
        return '中间插播';
    }

    private function getPositionType($start, $end, $total) {
        if ($start === 0) return 'opening';
        if ($end >= $total - 1) return 'ending';
        return 'middle';
    }

    private function buildResult($scoredSegments, $adClusters, $features, $analysisDetails) {
        $adSegments = [];
        $contentSegments = [];

        $adIndices = [];
        foreach ($adClusters as $cluster) {
            $adIndices = array_merge($adIndices, $cluster['segments']);
        }

        foreach ($scoredSegments as $i => $seg) {
            if (in_array($i, $adIndices)) {
                $adSegments[] = $seg;
            } else {
                $contentSegments[] = $seg;
            }
        }

        $totalDuration = array_sum(array_column($features, 'duration'));
        $adDuration = 0;
        foreach ($adClusters as $cluster) {
            $adDuration += $cluster['total_duration'];
        }

        $stats = $features[0]['global_stats'] ?? [];

        return [
            'success' => true,
            'total_segments' => count($scoredSegments),
            'ad_segment_count' => count($adSegments),
            'content_segment_count' => count($contentSegments),
            'ad_cluster_count' => count($adClusters),
            'total_duration' => round($totalDuration, 2),
            'ad_duration' => round($adDuration, 2),
            'content_duration' => round($totalDuration - $adDuration, 2),
            'ad_percentage' => $totalDuration > 0 ? round(($adDuration / $totalDuration) * 100, 1) : 0,
            'duration_stats' => $stats,
            'scored_segments' => array_values($scoredSegments),
            'ad_segments' => $adSegments,
            'content_segments' => $contentSegments,
            'ad_clusters' => $adClusters,
            'analysis_details' => $analysisDetails,
            'confidence_summary' => [
                'very_high' => count(array_filter($scoredSegments, fn($s) => $s['confidence'] === 'very_high')),
                'high' => count(array_filter($scoredSegments, fn($s) => $s['confidence'] === 'high')),
                'medium' => count(array_filter($scoredSegments, fn($s) => $s['confidence'] === 'medium')),
                'low' => count(array_filter($scoredSegments, fn($s) => $s['confidence'] === 'low')),
                'very_low' => count(array_filter($scoredSegments, fn($s) => $s['confidence'] === 'very_low')),
            ],
        ];
    }

    private function emptyResult() {
        return [
            'success' => true,
            'total_segments' => 0,
            'ad_segment_count' => 0,
            'content_segment_count' => 0,
            'ad_cluster_count' => 0,
            'total_duration' => 0,
            'ad_duration' => 0,
            'content_duration' => 0,
            'ad_percentage' => 0,
            'duration_stats' => [],
            'scored_segments' => [],
            'ad_segments' => [],
            'content_segments' => [],
            'ad_clusters' => [],
            'analysis_details' => [],
            'confidence_summary' => [],
        ];
    }
}
