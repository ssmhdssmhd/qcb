<?php

require_once __DIR__ . '/MacCMS10Analyzer.php';
require_once __DIR__ . '/../db/Database.php';
require_once __DIR__ . '/../db/DbDomainRuleManager.php';
require_once __DIR__ . '/../src/M3U8AdSkipper.php';

class AutoLearnEngine {
    private $opts;
    private $db;
    private $domainRuleManager;

    public function __construct($opts = []) {
        $this->opts = array_merge([
            'min_segments' => 50,
            'max_ad_percentage' => 85,
            'videos_per_site' => 5,
            'max_urls_per_run' => 20,
            'safeguard_min_keep_ratio' => 0.6
        ], $opts);
        $this->db = Database::getInstance();
        $this->domainRuleManager = new DbDomainRuleManager();
    }

    public function pickRandomSites($max = 5) {
        try {
            $rows = $this->db->query(
                "SELECT * FROM resource_sites WHERE status = 'active' ORDER BY RANDOM() LIMIT " . intval($max)
            );
            $sites = [];
            foreach ($rows as $row) {
                if (!empty($row['config'])) {
                    $config = json_decode($row['config'], true);
                    if (is_array($config)) {
                        $row = array_merge($config, $row);
                    }
                    unset($row['config']);
                }
                $sites[] = $row;
            }
            return $sites;
        } catch (Throwable $e) {
            error_log('[AutoLearnEngine] pickRandomSites 失败: ' . $e->getMessage());
            return [];
        }
    }

    public function pickRandomEpisodes($site, $count = 5) {
        $apiUrl = $site['api_url'] ?? '';
        if (empty($apiUrl)) {
            return [];
        }
        $analyzer = new MacCMS10Analyzer($apiUrl);
        $allEpisodes = [];
        $pagesToFetch = 2;
        for ($page = 1; $page <= $pagesToFetch; $page++) {
            $listResult = $analyzer->listVideos($page, 20);
            if (empty($listResult['success']) || empty($listResult['list'])) {
                continue;
            }
            foreach ($listResult['list'] as $vodItem) {
                $normalized = $analyzer->normalizeVideo($vodItem);
                $vodId = $normalized['id'];
                $vodName = $normalized['name'];
                foreach ($normalized['play_lines'] as $line) {
                    $from = $line['from'];
                    foreach ($line['episodes'] as $ep) {
                        if ($analyzer->isLikely正片($ep['name'], $vodName)) {
                            $allEpisodes[] = [
                                'vod_id' => $vodId,
                                'vod_name' => $vodName,
                                'episode_name' => $ep['name'],
                                'm3u8_url' => $ep['url'],
                                'site_name' => $site['name'] ?? '',
                                'site_url' => $site['site_url'] ?? ''
                            ];
                        }
                    }
                }
            }
        }
        if (empty($allEpisodes)) {
            return [];
        }
        shuffle($allEpisodes);
        return array_slice($allEpisodes, 0, min($count, count($allEpisodes)));
    }

    public function analyzeSingle($m3u8Url, $domain = '') {
        if (empty($domain)) {
            $parsed = parse_url($m3u8Url);
            $domain = $parsed['host'] ?? '';
        }
        if (empty($domain)) {
            return [
                'success' => false,
                'reason' => '无法解析域名',
                'domain' => $domain
            ];
        }
        $skipper = new M3U8AdSkipper();
        $skipper->setDomain($domain);
        $processResult = $skipper->processWithSafeguard($m3u8Url);
        $stats = $processResult['stats'] ?? [];
        $originalStats = $processResult['originalStats'] ?? null;
        $safeguardTriggered = !empty($processResult['safeguardTriggered']);
        $totalSegments = $stats['totalSegments'] ?? 0;
        $keptSegments = $stats['keptSegments'] ?? 0;
        if ($safeguardTriggered) {
            $keptRatio = $totalSegments > 0 ? ($keptSegments / $totalSegments) : 0;
            $skipRatio = 1 - $keptRatio;
            if ($skipRatio > 0.4) {
                return [
                    'success' => false,
                    'reason' => '高风险跳过（safeguard触发且跳过率>40%）',
                    'domain' => $domain,
                    'safeguard_triggered' => true,
                    'skip_ratio' => $skipRatio
                ];
            }
        }
        $adPercentage = $stats['adPercentage'] ?? 0;
        if ($adPercentage < 10) {
            return [
                'success' => false,
                'reason' => '广告率不足10%，不值得学习',
                'domain' => $domain,
                'ad_percentage' => $adPercentage
            ];
        }
        if ($totalSegments < $this->opts['min_segments']) {
            return [
                'success' => false,
                'reason' => 'segment数不足 min_segments',
                'domain' => $domain,
                'total_segments' => $totalSegments
            ];
        }
        $learnedRules = $this->extractLearnedRules($processResult);
        $existingRule = $this->domainRuleManager->getRules($domain);
        if ($existingRule === null) {
            $isNew = true;
            $mergedRule = $learnedRules;
            $mergedRule['learn_count'] = 1;
            $mergedRule['confidence_score'] = 1;
            $mergedRule['last_learn_date'] = date('Y-m-d H:i:s');
            $mergedRule['history_stats'] = [
                [
                    'totalCount' => $totalSegments,
                    'adCount' => $stats['removedSegments'] ?? 0,
                    'adPercentage' => $adPercentage,
                    'analyzed_at' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            $isNew = false;
            $mergedRule = $this->mergeRules($existingRule, $learnedRules);
            $mergedRule['learn_count'] = intval($existingRule['learn_count'] ?? 0) + 1;
            $mergedRule['confidence_score'] = intval($existingRule['confidence_score'] ?? 0) + 1;
            $mergedRule['last_learn_date'] = date('Y-m-d H:i:s');
            if (!isset($mergedRule['history_stats']) || !is_array($mergedRule['history_stats'])) {
                $mergedRule['history_stats'] = [];
            }
            $mergedRule['history_stats'][] = [
                'totalCount' => $totalSegments,
                'adCount' => $stats['removedSegments'] ?? 0,
                'adPercentage' => $adPercentage,
                'analyzed_at' => date('Y-m-d H:i:s')
            ];
            if (count($mergedRule['history_stats']) > 10) {
                $mergedRule['history_stats'] = array_slice($mergedRule['history_stats'], -10);
            }
        }
        $saveResult = $this->domainRuleManager->saveRules($domain, $mergedRule);
        return [
            'success' => $saveResult,
            'new_rule' => $isNew,
            'domain' => $domain,
            'total_segments' => $totalSegments,
            'ad_percentage' => $adPercentage,
            'kept_segments' => $keptSegments,
            'rule' => [
                'duration_rule_count' => count($learnedRules['duration_rules'] ?? []),
                'discontinuity_rule_count' => count($learnedRules['discontinuity_rules'] ?? [])
            ]
        ];
    }

    public function extractLearnedRules($processResult) {
        $stats = $processResult['stats'] ?? [];
        $originalStats = $processResult['originalStats'] ?? null;
        $playlist = $processResult['original'] ?? [];
        $filteredPlaylist = $processResult['filtered'] ?? [];
        $originalSegments = $playlist['segments'] ?? [];
        $filteredSegments = $filteredPlaylist['segments'] ?? [];
        $totalDuration = 0;
        $segmentDurations = [];
        foreach ($originalSegments as $seg) {
            $d = floatval($seg['duration'] ?? 0);
            $segmentDurations[] = $d;
            $totalDuration += $d;
        }
        $filteredUris = [];
        foreach ($filteredSegments as $fs) {
            $filteredUris[md5($fs['uri'] ?? '')] = true;
        }
        $adSegments = [];
        foreach ($originalSegments as $seg) {
            $uri = $seg['uri'] ?? '';
            $key = md5($uri);
            if (!isset($filteredUris[$key])) {
                $adSegments[] = $seg;
            }
        }
        $durationRules = $this->extractDurationRules($adSegments);
        $discontinuityRules = $this->extractDiscontinuityRules($originalSegments, $adSegments);
        $filenamePatterns = $this->extractFilenamePatterns($adSegments);
        $adPercentage = $stats['adPercentage'] ?? 0;
        $totalSegments = count($originalSegments);
        $adSegmentCount = count($adSegments);
        $adTypeStats = [];
        if (count($adSegments) > 0) {
            $firstSeg = reset($originalSegments);
            $lastSeg = end($originalSegments);
            $firstAd = reset($adSegments);
            $lastAd = end($adSegments);
            $preRollCount = 0;
            $postRollCount = 0;
            if ($firstAd && $firstAd === $firstSeg) {
                $preRollCount = 1;
            }
            if ($lastAd && $lastAd === $lastSeg) {
                $postRollCount = 1;
            }
            $adTypeStats = [
                'pre_roll_ad' => ['count' => $preRollCount, 'duration' => 0],
                'mid_roll_ad' => ['count' => max(0, count($adSegments) - $preRollCount - $postRollCount), 'duration' => 0],
                'post_roll_ad' => ['count' => $postRollCount, 'duration' => 0],
                'duration_based_ad' => ['count' => count($durationRules) > 0 ? count($adSegments) : 0, 'duration' => 0]
            ];
        }
        $discontinuityCount = 0;
        foreach ($originalSegments as $seg) {
            if (!empty($seg['discontinuity'])) {
                $discontinuityCount++;
            }
        }
        $markerStats = [
            'discontinuity_count' => $discontinuityCount,
            'cue_marker_count' => 0,
            'scte35_count' => 0,
            'ad_tag_count' => 0
        ];
        $analysisStats = [
            'totalSegments' => $totalSegments,
            'adSegments' => $adSegmentCount,
            'contentSegments' => count($filteredSegments),
            'totalDuration' => round($totalDuration, 2),
            'adDuration' => round($stats['savedDuration'] ?? 0, 2),
            'filteredDuration' => round($stats['filteredDuration'] ?? 0, 2),
            'adPercentage' => $adPercentage,
            'discontinuityCount' => $discontinuityCount
        ];
        $psychologicalProfile = [
            'ad_density' => $totalSegments > 0 ? round($adSegmentCount / $totalSegments, 3) : 0,
            'attention_grab_score' => 0,
            'frequency_score' => 0,
            'watchability_score' => 0
        ];
        $insertionPatterns = [
            'pre_roll' => [
                'found' => count($adSegments) > 0 && reset($originalSegments) === reset($adSegments),
                'detected_count' => 0,
                'avg_duration' => 0,
                'avg_segment_count' => 0
            ],
            'mid_roll' => [
                'found' => false,
                'detected_count' => 0,
                'avg_clip_count' => 0,
                'avg_duration_per_clip' => 0,
                'positions' => []
            ],
            'post_roll' => [
                'found' => count($adSegments) > 0 && end($originalSegments) === end($adSegments),
                'detected_count' => 0,
                'avg_duration' => 0,
                'avg_segment_count' => 0
            ]
        ];
        $sequenceJumpRules = [];
        $prevSeq = null;
        foreach ($originalSegments as $idx => $seg) {
            $uri = $seg['uri'] ?? '';
            if (preg_match('/(\d{3,})/', basename($uri), $m)) {
                $seq = intval($m[1]);
                if ($prevSeq !== null && $seq > 0 && $prevSeq > 0) {
                    $jump = $seq - $prevSeq - 1;
                    if (abs($jump) > 1000) {
                        $sequenceJumpRules[] = [
                            'name' => 'seq_jump_' . $idx,
                            'enabled' => true,
                            'type' => 'sequence_jump',
                            'direction' => $jump > 0 ? 'forward' : 'backward',
                            'threshold' => abs($jump),
                            'reason' => '序列号跳跃检测',
                            'weight' => 90
                        ];
                    }
                }
                $prevSeq = $seq;
            }
        }
        return [
            'duration_rules' => $durationRules,
            'discontinuity_rules' => $discontinuityRules,
            'sequence_jump_rules' => $sequenceJumpRules,
            'filename_patterns' => $filenamePatterns,
            'insertion_patterns' => $insertionPatterns,
            'ad_type_stats' => $adTypeStats,
            'psychological_profile' => $psychologicalProfile,
            'marker_stats' => $markerStats,
            'analysis_stats' => $analysisStats,
            'history_stats' => []
        ];
    }

    private function extractDurationRules($adSegments) {
        $rules = [];
        if (count($adSegments) < 3) {
            return $rules;
        }
        $durations = [];
        foreach ($adSegments as $seg) {
            $d = floatval($seg['duration'] ?? 0);
            if ($d < 15) {
                $durations[] = $d;
            }
        }
        if (count($durations) < 3) {
            return $rules;
        }
        sort($durations);
        $count = count($durations);
        $min = $durations[0];
        $max = $durations[$count - 1];
        $avg = array_sum($durations) / $count;
        $median = $count % 2 === 0
            ? ($durations[$count / 2 - 1] + $durations[$count / 2]) / 2
            : $durations[floor($count / 2)];
        $rules[] = [
            'name' => 'ad_duration_' . intval($avg) . 's',
            'enabled' => true,
            'type' => 'duration',
            'operator' => '<=',
            'threshold' => round($max + 0.5, 2),
            'reason' => '广告片段时长通常在 ' . round($min, 2) . '-' . round($max, 2) . '秒 (中位数: ' . round($median, 2) . 's)',
            'weight' => 60,
            'stats' => [
                'min' => round($min, 2),
                'max' => round($max, 2),
                'avg' => round($avg, 2),
                'median' => round($median, 2),
                'count' => $count
            ]
        ];
        return $rules;
    }

    private function extractDiscontinuityRules($originalSegments, $adSegments) {
        $rules = [];
        if (count($originalSegments) < 5) {
            return $rules;
        }
        $discoShortRuns = 0;
        $consecutiveShort = 0;
        $inDiscoBlock = false;
        foreach ($originalSegments as $seg) {
            $isDisco = !empty($seg['discontinuity']);
            if ($isDisco) {
                $inDiscoBlock = true;
                $consecutiveShort = 0;
                continue;
            }
            if ($inDiscoBlock) {
                $d = floatval($seg['duration'] ?? 0);
                if ($d < 10) {
                    $consecutiveShort++;
                    if ($consecutiveShort >= 3) {
                        $discoShortRuns++;
                    }
                } else {
                    $inDiscoBlock = false;
                    $consecutiveShort = 0;
                }
            }
        }
        if ($discoShortRuns > 0) {
            $rules[] = [
                'name' => 'discontinuity_short_segments',
                'enabled' => true,
                'type' => 'discontinuity',
                'reason' => 'DISCONTINUITY 后出现连续 >=3 个 <10s 短片段，高度疑似广告',
                'weight' => 85,
                'detected_runs' => $discoShortRuns
            ];
        }
        $discoCount = 0;
        foreach ($originalSegments as $seg) {
            if (!empty($seg['discontinuity'])) {
                $discoCount++;
            }
        }
        if ($discoCount > 5) {
            $hasGeneral = false;
            foreach ($rules as $r) {
                if (($r['name'] ?? '') === 'discontinuity') {
                    $hasGeneral = true;
                    break;
                }
            }
            if (!$hasGeneral) {
                $rules[] = [
                    'name' => 'discontinuity',
                    'enabled' => true,
                    'type' => 'discontinuity',
                    'reason' => 'DISCONTINUITY 标记频繁出现，表示插播切换',
                    'weight' => 80
                ];
            }
        }
        return $rules;
    }

    private function extractFilenamePatterns($adSegments) {
        $patterns = [];
        if (count($adSegments) < 3) {
            return $patterns;
        }
        $filenames = [];
        foreach ($adSegments as $seg) {
            $uri = $seg['uri'] ?? '';
            $filename = basename($uri);
            if (!empty($filename)) {
                $filenames[] = $filename;
            }
        }
        if (count($filenames) < 3) {
            return $patterns;
        }
        $prefixes = [];
        foreach ($filenames as $name) {
            $prefix = substr($name, 0, 8);
            if (!isset($prefixes[$prefix])) {
                $prefixes[$prefix] = 0;
            }
            $prefixes[$prefix]++;
        }
        foreach ($prefixes as $prefix => $cnt) {
            if ($cnt >= 3) {
                $patterns[] = '/^' . preg_quote($prefix, '/') . '/i';
            }
        }
        return array_values(array_unique($patterns));
    }

    private function mergeRules($existing, $new) {
        $arrayFields = [
            'duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
            'filename_patterns', 'insertion_patterns', 'ad_type_stats',
            'psychological_profile', 'marker_stats', 'analysis_stats', 'history_stats'
        ];
        $merged = $existing;
        foreach ($arrayFields as $field) {
            if (!isset($merged[$field]) || !is_array($merged[$field])) {
                $merged[$field] = [];
            }
            if (!isset($new[$field]) || !is_array($new[$field])) {
                continue;
            }
            if ($field === 'filename_patterns') {
                $merged[$field] = array_values(array_unique(array_merge($merged[$field], $new[$field])));
                if (count($merged[$field]) > 50) {
                    $merged[$field] = array_slice($merged[$field], 0, 50);
                }
            } elseif ($field === 'duration_rules' || $field === 'discontinuity_rules' || $field === 'sequence_jump_rules') {
                $byName = [];
                foreach ($merged[$field] as $r) {
                    $name = is_array($r) ? ($r['name'] ?? md5(json_encode($r))) : md5(json_encode($r));
                    $byName[$name] = $r;
                }
                foreach ($new[$field] as $r) {
                    $name = is_array($r) ? ($r['name'] ?? md5(json_encode($r))) : md5(json_encode($r));
                    $byName[$name] = $r;
                }
                $merged[$field] = array_values($byName);
                if (count($merged[$field]) > 50) {
                    $merged[$field] = array_slice($merged[$field], 0, 50);
                }
            } elseif ($field === 'history_stats') {
                $merged[$field] = array_merge($merged[$field], $new[$field]);
                if (count($merged[$field]) > 50) {
                    $merged[$field] = array_slice($merged[$field], -50);
                }
            } elseif ($field === 'marker_stats' || $field === 'analysis_stats') {
                $merged[$field] = array_merge($merged[$field], $new[$field]);
            } else {
                $merged[$field] = array_replace_recursive($merged[$field], $new[$field]);
            }
        }
        return $merged;
    }

    public function run($maxSites = 5, $perSite = 5) {
        $startTime = microtime(true);
        $sites = $this->pickRandomSites($maxSites);
        $result = [
            'start_time' => date('Y-m-d H:i:s'),
            'sites_processed' => 0,
            'videos_processed' => 0,
            'rules_updated' => 0,
            'rules_created' => 0,
            'skipped' => 0,
            'domains' => [],
            'details' => []
        ];
        $urlsProcessed = 0;
        $maxUrls = $this->opts['max_urls_per_run'];
        foreach ($sites as $site) {
            $siteName = $site['name'] ?? 'unknown';
            $siteDetail = [
                'site' => $siteName,
                'episodes_picked' => 0,
                'analyzed' => 0,
                'learned' => 0,
                'skipped' => 0,
                'errors' => []
            ];
            try {
                $episodes = $this->pickRandomEpisodes($site, $perSite);
                $siteDetail['episodes_picked'] = count($episodes);
                foreach ($episodes as $ep) {
                    if ($urlsProcessed >= $maxUrls) {
                        break;
                    }
                    $urlsProcessed++;
                    $result['videos_processed']++;
                    $siteDetail['analyzed']++;
                    try {
                        $analyzeResult = $this->analyzeSingle($ep['m3u8_url']);
                        if (!empty($analyzeResult['success'])) {
                            $domain = $analyzeResult['domain'] ?? '';
                            if (!isset($result['domains'][$domain])) {
                                $result['domains'][$domain] = [
                                    'domain' => $domain,
                                    'learn_count' => 0,
                                    'is_new' => false,
                                    'avg_ad_percentage' => 0,
                                    'total_segments' => 0
                                ];
                            }
                            $result['domains'][$domain]['learn_count']++;
                            $adPct = $analyzeResult['ad_percentage'] ?? 0;
                            $cnt = $result['domains'][$domain]['learn_count'];
                            $result['domains'][$domain]['avg_ad_percentage'] = round(
                                (($result['domains'][$domain]['avg_ad_percentage'] * ($cnt - 1)) + $adPct) / $cnt,
                                2
                            );
                            $result['domains'][$domain]['total_segments'] += ($analyzeResult['total_segments'] ?? 0);
                            if (!empty($analyzeResult['new_rule'])) {
                                $result['domains'][$domain]['is_new'] = true;
                                $result['rules_created']++;
                            } else {
                                $result['rules_updated']++;
                            }
                            $siteDetail['learned']++;
                        } else {
                            $result['skipped']++;
                            $siteDetail['skipped']++;
                            $siteDetail['errors'][] = $analyzeResult['reason'] ?? '未知原因';
                        }
                    } catch (Throwable $e) {
                        $result['skipped']++;
                        $siteDetail['skipped']++;
                        $siteDetail['errors'][] = '异常: ' . $e->getMessage();
                    }
                }
            } catch (Throwable $e) {
                $siteDetail['errors'][] = '站点异常: ' . $e->getMessage();
            }
            $result['sites_processed']++;
            $result['details'][] = $siteDetail;
            if ($urlsProcessed >= $maxUrls) {
                break;
            }
        }
        $result['duration_ms'] = round((microtime(true) - $startTime) * 1000, 0);
        $result['end_time'] = date('Y-m-d H:i:s');
        $result['domains'] = array_values($result['domains']);
        return $result;
    }
}
