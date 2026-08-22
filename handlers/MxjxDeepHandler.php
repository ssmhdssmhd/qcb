<?php
/**
 * MxjxDeepHandler —— 深度去广告分析（mxjx/deep）
 *
 * action：mxjx/deep
 * 迁移自 mx.php 的 mxjx/deep case。
 * 输出清理后的 m3u8 内容 + 规则/TS-MD5 两路广告时间段 + 统计。
 *
 * @package handlers
 * @since   5.14.0
 */
class MxjxDeepHandler extends BaseHandler {

    /** 入口：mxjx/deep */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut([
                'code' => 400,
                'success' => false,
                'message' => '缺少 url 参数',
            ], 400);
        }

        @set_time_limit(60);
        try {
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = M3u8UrlHelper::resolveMasterPlaylist($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
            }
            $url = $mediaUrl;

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);

            if ($this->ctx->useDb && $this->ctx->ruleManager) {
                $dbRules = $this->ctx->ruleManager->getRules($domain);
                if (!empty($dbRules)) {
                    $engineReflection = new ReflectionClass($enhancedEngine);
                    $applyMethod = $engineReflection->getMethod('applyDomainRules');
                    $applyMethod->setAccessible(true);
                    $applyMethod->invoke($enhancedEngine, $dbRules);
                }
                if (class_exists('DbAdSignature')) {
                    $adSignature = new DbAdSignature();
                    $sigRules = $adSignature->getRulesForDomain($domain);
                    if (!empty($sigRules)) {
                        $engineReflection = new ReflectionClass($enhancedEngine);
                        $applyMethod = $engineReflection->getMethod('applyDomainRules');
                        $applyMethod->setAccessible(true);
                        $applyMethod->invoke($enhancedEngine, $sigRules);
                    }
                }
            }

            $ruleEngineProp->setValue($skipper, $enhancedEngine);
            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);
            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->processWithSafeguard($url, ['filterSubtitles' => true, 'filterAdTags' => true]);
            $safeguardTriggered = !empty($result['safeguardTriggered']);
            $stats = $result['stats'] ?? [];

            // 获取规则过滤后的广告时间段
            $adTimeRanges = [];
            $filteredPlaylist = $result['filtered'] ?? [];
            $removedSegments = $filteredPlaylist['removedSegments'] ?? [];
            $originalPlaylist = $result['original'] ?? [];
            $allSegments = $originalPlaylist['segments'] ?? [];
            $currentTime = 0;
            $removedUriSet = [];
            foreach ($removedSegments as $rm) {
                $removedUriSet[$rm['uri'] ?? ''] = true;
            }
            foreach ($allSegments as $seg) {
                $segDur = $seg['duration'] ?? 0;
                if (isset($removedUriSet[$seg['uri'] ?? ''])) {
                    $adTimeRanges[] = [
                        'start' => round($currentTime, 2),
                        'end' => round($currentTime + $segDur, 2),
                        'duration' => round($segDur, 2),
                        'source' => 'rule',
                    ];
                }
                $currentTime += $segDur;
            }

            // 深度TS MD5分析
            $deepAdRemoved = 0;
            $deepAdRanges = [];
            $newM3U8Content = $result['output'] ?? '';

            if (!$safeguardTriggered && strpos($url, 'http') === 0) {
                $deepAdRemoved = $this->deepAnalysis($skipper, $url, $parsedUrl, $domain, $newM3U8Content, $deepAdRanges);
            }

            // URL重写
            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
            if ($isRemote) {
                $newM3U8Content = M3u8UrlHelper::rewriteRelativeUrls($newM3U8Content, $url);
            }

            // 合并所有广告时间段
            $allAdRanges = array_merge($adTimeRanges, $deepAdRanges);
            usort($allAdRanges, function($a, $b) { return $a['start'] <=> $b['start']; });
            $mergedRanges = [];
            foreach ($allAdRanges as $range) {
                if (!empty($mergedRanges) && $range['start'] <= $mergedRanges[count($mergedRanges)-1]['end'] + 0.5) {
                    $mergedRanges[count($mergedRanges)-1]['end'] = max($mergedRanges[count($mergedRanges)-1]['end'], $range['end']);
                    $mergedRanges[count($mergedRanges)-1]['duration'] = round($mergedRanges[count($mergedRanges)-1]['end'] - $mergedRanges[count($mergedRanges)-1]['start'], 2);
                } else {
                    $mergedRanges[] = $range;
                }
            }

            $deepPlayUrl = SelfUrlHelper::mxjxUrl($mediaUrl, true);

            $this->jsonOut([
                'code' => 200,
                'success' => true,
                'message' => '深度去广告分析完成',
                'data' => [
                    'original_url' => $this->param('url', ''),
                    'media_url' => $mediaUrl,
                    'domain' => $domain,
                    'm3u8_content' => $newM3U8Content,
                    'deep_play_url' => $deepPlayUrl,
                    'safeguard_triggered' => $safeguardTriggered,
                    'safeguard_reason' => $result['safeguardReason'] ?? '',
                    'stats' => [
                        'total_segments' => $stats['totalSegments'] ?? 0,
                        'kept_segments' => $stats['keptSegments'] ?? 0,
                        'removed_segments' => $stats['removedSegments'] ?? 0,
                        'deep_ad_removed' => $deepAdRemoved,
                        'original_duration' => $stats['originalDuration'] ?? 0,
                        'filtered_duration' => $stats['filteredDuration'] ?? 0,
                        'saved_duration' => $stats['savedDuration'] ?? 0,
                        'ad_percentage' => $stats['adPercentage'] ?? 0,
                    ],
                    'ad_time_ranges' => $mergedRanges,
                    'ad_ranges_count' => count($mergedRanges),
                    'deep_ad_ranges' => $deepAdRanges,
                    'rule_ad_ranges' => $adTimeRanges,
                ],
            ]);
        } catch (\Exception $e) {
            $this->jsonOut([
                'code' => 500,
                'success' => false,
                'message' => '深度去广告分析失败',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 深度 TS MD5 分析并移除广告段，返回移除数量与时间段
     *
     * @param M3U8AdSkipper $skipper
     * @param string        $url
     * @param array         $parsedUrl
     * @param string        $domain
     * @param string        $content      引用修改
     * @param array         $deepAdRanges 输出的广告时间段（引用）
     * @return int
     */
    protected function deepAnalysis($skipper, $url, $parsedUrl, $domain, &$content, &$deepAdRanges) {
        $deepAdRemoved = 0;
        $deepAdRanges = [];
        try {
            if (!class_exists('TsMd5Analyzer')) {
                require_once __DIR__ . '/../src/TsMd5Analyzer.php';
            }

            $parser = $skipper->getParser();
            $parsedPlaylist = $parser->parse($content);
            $keptSegments = $parsedPlaylist['segments'] ?? [];

            if (count($keptSegments) <= 3) {
                return 0;
            }

            $tsAnalyzer = new TsMd5Analyzer($domain);
            $tsAnalyzer->setFastMode(true);

            $tsBaseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
            if (isset($parsedUrl['port'])) {
                $tsBaseUrl .= ':' . $parsedUrl['port'];
            }
            $tsPathDir = dirname($parsedUrl['path'] ?? '');
            $tsPathDir = $tsPathDir === '.' ? '' : $tsPathDir;
            $tsBasePath = $tsBaseUrl . $tsPathDir;

            $tsAnalysis = $tsAnalyzer->analyzeMd5Signatures(
                $keptSegments,
                $tsBasePath,
                ['max_count' => 30, 'sample_mode' => 'auto']
            );

            $adMd5Set = [];
            foreach ($tsAnalysis['ad_candidates'] as $adCandidate) {
                if ($adCandidate['count'] >= 2) {
                    $adMd5Set[$adCandidate['md5']] = true;
                }
            }

            // 计算深度检测到的广告时间段
            $deepAdUris = [];
            $currentTime = 0;
            foreach ($keptSegments as $seg) {
                $tsUri = $seg['uri'] ?? '';
                $segDur = $seg['duration'] ?? 0;

                $isDeepAd = false;
                if ($this->ctx->useDb) {
                    $tsUrl = $tsUri;
                    if (!filter_var($tsUrl, FILTER_VALIDATE_URL)) {
                        $tsUrl = rtrim($tsBasePath, '/') . '/' . ltrim($tsUri, '/');
                    }
                    $md5 = $tsAnalyzer->calculateTsMd5($tsUrl);
                    if ($md5) {
                        $detectResult = $tsAnalyzer->detectAdByMd5($md5, $domain);
                        if ($detectResult['is_ad']) {
                            $isDeepAd = true;
                            $adMd5Set[$md5] = true;
                        }
                    }
                } else {
                    foreach ($tsAnalysis['md5_details'] ?? [] as $item) {
                        if ($item['uri'] === $tsUri && isset($adMd5Set[$item['md5']])) {
                            $isDeepAd = true;
                            break;
                        }
                    }
                }

                if ($isDeepAd) {
                    $deepAdUris[$tsUri] = true;
                    $deepAdRanges[] = [
                        'start' => round($currentTime, 2),
                        'end' => round($currentTime + $segDur, 2),
                        'duration' => round($segDur, 2),
                        'source' => 'ts_md5',
                    ];
                }
                $currentTime += $segDur;
            }

            // 从m3u8中移除深度检测到的广告片段
            if (!empty($deepAdUris)) {
                $before = $content;
                $content = M3u8UrlHelper::removeSegments($content, $deepAdUris);
                $deepAdRemoved = count($deepAdUris);

                if ($this->ctx->useDb && method_exists($tsAnalyzer, 'saveMd5Signatures')) {
                    $md5UriMap = [];
                    foreach ($tsAnalysis['md5_details'] ?? [] as $item) {
                        if (!empty($item['md5']) && !empty($item['uri'])) {
                            $md5UriMap[$item['uri']] = $item['md5'];
                        }
                    }
                    $sigData = [];
                    foreach (array_keys($deepAdUris) as $uri) {
                        $md5 = $md5UriMap[$uri] ?? '';
                        if ($md5) {
                            $sigData[] = ['md5' => $md5, 'count' => 1, 'avg_duration' => 0];
                        }
                    }
                    if (!empty($sigData)) {
                        $tsAnalyzer->saveMd5Signatures($domain, $sigData);
                    }
                }
            }
        } catch (\Throwable $e) {
            // 深度分析失败不影响主链路
        }
        return $deepAdRemoved;
    }
}
