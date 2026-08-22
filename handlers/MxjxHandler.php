<?php
/**
 * MxjxHandler —— 去广告 M3U8 输出（mxjx）
 *
 * action：mxjx
 * 迁移自 mx.php 的 mxjx case。
 * 行为：
 *   ① 缓存（含 _t 时间戳防缓存）
 *   ② Master 列表解析取分片
 *   ③ 增强规则引擎 + 数据库域名规则/广告特征码
 *   ④ 安全守卫 processWithSafeguard
 *   ⑤ deep=1 时深度 TS MD5 广告分析并移除
 *   ⑥ 相对地址补全 → 输出 m3u8（可带 X-Cache/X-Safeguard/X-Deep 头）
 *
 * @package handlers
 * @since   5.14.0
 */
class MxjxHandler extends BaseHandler {

    /** 入口：mxjx */
    public function handle() {
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $url = $this->param('url', '');
        $proxy = $this->param('proxy', '');
        if (empty($url)) {
            header('Content-Type: application/json; charset=utf-8');
            $this->jsonOut(['success' => false, 'code' => 400, 'message' => '缺少 url 参数'], 400);
        }

        try {
            $cacheManager = new CacheManager($this->ctx->rootDir . '/cache');
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            // 添加时间戳避免相同URL缓存问题
            $timestamp = $this->param('_t', '');
            $cacheKey = 'mxjx_' . md5($url . '_' . $domain . '_' . $timestamp . '_' . $proxy);
            $cachedContent = $cacheManager->get($cacheKey);

            if ($cachedContent !== null && is_string($cachedContent) && empty($timestamp)) {
                header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                header('Content-Disposition: inline; filename="playlist.m3u8"');
                header('X-Cache: HIT');
                if ($proxy) {
                    header('X-Proxy: ' . $proxy);
                }
                ob_clean();
                echo $cachedContent;
                exit;
            }

            $mediaUrl = M3u8UrlHelper::resolveMasterPlaylist($url, $proxy);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
            }
            $url = $mediaUrl;

            $skipper = new M3U8AdSkipper();
            if ($proxy) {
                $skipper->getParser()->setForceProxy($proxy);
            }
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);

            // 从数据库加载域名规则和广告特征码
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

            $deepMode = ($this->param('deep', '0') === '1');
            $result = $skipper->processWithSafeguard($url, ['filterSubtitles' => true, 'filterAdTags' => true]);
            $safeguardTriggered = !empty($result['safeguardTriggered']);
            $safeguardReason = $result['safeguardReason'] ?? '';
            $safeguardMethod = $result['safeguardMethod'] ?? '';

            if (!$result['success'] && empty($result['output'])) {
                header('Content-Type: application/json; charset=utf-8');
                $this->jsonOut([
                    'success' => false,
                    'code' => 500,
                    'message' => 'M3U8 解析失败',
                    'error' => $result['error'] ?? '未知错误',
                ], 500);
            }

            $newM3U8Content = $result['output'];

            // 深度去广告：TS片段MD5内容分析
            $deepAdSegments = [];
            $deepAdRemoved = 0;
            if ($deepMode && !$safeguardTriggered && strpos($url, 'http') === 0) {
                $deepAdRemoved = $this->deepClean($skipper, $url, $parsedUrl, $domain, $newM3U8Content, $deepAdSegments);
            }

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
            if ($isRemote) {
                $newM3U8Content = M3u8UrlHelper::rewriteRelativeUrls($newM3U8Content, $url);
            }

            // 仅在无时间戳参数时缓存
            if (empty($timestamp)) {
                $cacheManager->set($cacheKey, $newM3U8Content, 120);
            }

            header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
            header('Content-Disposition: inline; filename="playlist.m3u8"');
            header('X-Cache: MISS');
            header('X-Request-Time: ' . time());
            if ($safeguardTriggered) {
                header('X-Safeguard: triggered');
                header('X-Safeguard-Reason: ' . rawurlencode($safeguardReason));
                header('X-Safeguard-Method: ' . $safeguardMethod);
            } else {
                header('X-Safeguard: not_triggered');
            }
            if ($deepMode) {
                header('X-Deep-Ad-Mode: enabled');
                header('X-Deep-Ad-Removed: ' . $deepAdRemoved);
                header('X-Deep-Ad-Segments: ' . count($deepAdSegments));
            }
            ob_clean();
            echo $newM3U8Content;
            exit;

        } catch (\Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            $this->jsonOut([
                'success' => false,
                'code' => 500,
                'message' => '处理失败',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 深度去广告：TS MD5 内容分析并移除广告段
     *
     * @param M3U8AdSkipper $skipper
     * @param string        $url          当前（分片）m3u8 地址
     * @param array         $parsedUrl    parse_url 结果
     * @param string        $domain
     * @param string        $content      待处理 m3u8 内容（引用修改）
     * @param array         $adSegments   输出的广告段列表（引用）
     * @return int 移除的广告段数量
     */
    protected function deepClean($skipper, $url, $parsedUrl, $domain, &$content, &$adSegments) {
        $removed = 0;
        $adSegments = [];
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

            if ($this->ctx->useDb) {
                foreach ($keptSegments as $seg) {
                    $tsUri = $seg['uri'] ?? '';
                    if (empty($tsUri)) continue;
                    $tsUrl = $tsUri;
                    if (!filter_var($tsUrl, FILTER_VALIDATE_URL)) {
                        $tsUrl = rtrim($tsBasePath, '/') . '/' . ltrim($tsUri, '/');
                    }
                    $md5 = $tsAnalyzer->calculateTsMd5($tsUrl);
                    if ($md5) {
                        $detectResult = $tsAnalyzer->detectAdByMd5($md5, $domain);
                        if ($detectResult['is_ad']) {
                            $adMd5Set[$md5] = true;
                        }
                        if (isset($adMd5Set[$md5])) {
                            $adSegments[] = [
                                'index' => $seg['originalIndex'] ?? 0,
                                'uri' => $tsUri,
                                'duration' => $seg['duration'] ?? 0,
                                'md5' => $md5,
                                'source' => $detectResult['is_ad'] ? 'db' : 'analysis',
                            ];
                        }
                    }
                }
            } else {
                $md5Data = $tsAnalysis['md5_details'] ?? [];
                foreach ($md5Data as $item) {
                    if (isset($adMd5Set[$item['md5']])) {
                        $adSegments[] = [
                            'index' => $item['index'] ?? 0,
                            'uri' => $item['uri'] ?? '',
                            'duration' => $item['duration'] ?? 0,
                            'md5' => $item['md5'],
                            'source' => 'analysis',
                        ];
                    }
                }
            }

            // 从m3u8中移除深度检测到的广告片段
            if (!empty($adSegments)) {
                $adUris = [];
                foreach ($adSegments as $adSeg) {
                    $adUris[$adSeg['uri']] = true;
                }
                $content = M3u8UrlHelper::removeSegments($content, $adUris);
                $removed = count($adSegments);

                if ($this->ctx->useDb && method_exists($tsAnalyzer, 'saveMd5Signatures')) {
                    $tsAnalyzer->saveMd5Signatures($domain, array_map(function($s) {
                        return ['md5' => $s['md5'], 'count' => 1, 'avg_duration' => $s['duration']];
                    }, $adSegments));
                }
            }
        } catch (\Throwable $e) {
            // 深度分析失败不影响主链路
        }
        return $removed;
    }
}
