<?php
/**
 * 广告识别与过滤引擎
 *
 * 双层识别机制：
 *   1. 规则引擎 - 基于 m3u8 特征匹配（域名/关键词/不连续标记/时长）
 *   2. AI 辅助  - 规则引擎置信度不足时，调用大模型分析
 *
 * 生成去广告 m3u8 文件，返回广告详情和清洁播放地址
 */

class AdFilter
{
    /** @var array 配置 */
    private $config;

    /** @var array 解析结果 */
    private $segments = [];

    /** @var array 识别到的广告分段 */
    private $adSegments = [];

    /** @var string m3u8 基础 URL（用于解析相对路径） */
    private $baseUrl;

    /** @var array m3u8 全局标签（需保留在清洁版中） */
    private $globalTags = [];

    /** @var string|null 加密密钥标签 */
    private $extKeyLine = null;

    /**
     * 构造函数
     *
     * @param array $config 全局配置
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 处理 m3u8 内容：识别广告 + 生成去广告版本
     *
     * @param string $m3u8Content  m3u8 原始内容
     * @param string $m3u8Url      m3u8 的 URL（用于解析相对路径）
     * @return array  [clean_content, ad_info]
     */
    public function process(string $m3u8Content, string $m3u8Url): array
    {
        $this->baseUrl = $this->getBaseUrl($m3u8Url);

        // 解析 m3u8 为分段结构
        $this->segments = $this->parseM3u8($m3u8Content);

        if (empty($this->segments)) {
            return [
                'clean_content' => $m3u8Content,
                'ad_info'       => $this->buildAdInfo(0, [], 0, 0),
            ];
        }

        // 第一层：规则引擎识别
        $this->detectByRules();

        // 第二层：AI 辅助识别（如果启用且存在不确定分段）
        if ($this->config['ai']['enabled']) {
            $this->detectByAI();
        }

        // 生成去广告 m3u8
        $cleanContent = $this->generateCleanM3u8();

        // 构建广告信息
        $adInfo = $this->buildAdInfo(
            count($this->adSegments),
            $this->adSegments,
            count($this->segments),
            $this->calculateCleanDuration()
        );

        return [
            'clean_content' => $cleanContent,
            'ad_info'       => $adInfo,
        ];
    }

    /**
     * 解析 m3u8 内容为分段结构
     *
     * @param string $content m3u8 原始内容
     * @return array 分段数组
     */
    private function parseM3u8(string $content): array
    {
        $lines = explode("\n", $content);
        $segments = [];
        $currentSegment = null;
        $segIndex = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            // 空行跳过
            if ($line === '') {
                continue;
            }

            // 头部标签
            if ($line === '#EXTM3U') {
                $this->globalTags[] = $line;
                continue;
            }

            // 加密密钥标签（保留到清洁版）
            if (strpos($line, '#EXT-X-KEY') === 0) {
                $this->extKeyLine = $line;
                $this->globalTags[] = $line;
                continue;
            }

            // 版本号、目标时长等全局标签
            if (preg_match('/^#EXT-X-VERSION/', $line)
                || preg_match('/^#EXT-X-TARGETDURATION/', $line)
                || preg_match('/^#EXT-X-PLAYLIST-TYPE/', $line)
                || preg_match('/^#EXT-X-MEDIA-SEQUENCE/', $line)
                || preg_match('/^#EXT-X-ALLOW-CACHE/', $line)
                || preg_match('/^#EXT-X-INDEPENDENT-SEGMENTS/', $line)
            ) {
                $this->globalTags[] = $line;
                continue;
            }

            // #EXTINF - 分段信息行
            if (strpos($line, '#EXTINF') === 0) {
                $duration = 0;
                if (preg_match('/#EXTINF:([\d.]+)/', $line, $m)) {
                    $duration = (float)$m[1];
                }
                $currentSegment = [
                    'index'           => $segIndex,
                    'duration'        => $duration,
                    'extinf_line'     => $line,
                    'url'             => '',
                    'resolved_url'    => '',
                    'domain'          => '',
                    'is_ad'           => false,
                    'ad_reason'        => '',
                    'confidence'      => 0.0,
                ];
                continue;
            }

            // #EXT-X-DISCONTINUITY - 不连续标记
            if (strpos($line, '#EXT-X-DISCONTINUITY') === 0) {
                if ($currentSegment !== null) {
                    $currentSegment['after_discontinuity'] = true;
                }
                continue;
            }

            // #EXT-X-DATERANGE - 可能是 SCTE-35 广告标记
            if (strpos($line, '#EXT-X-DATERANGE') === 0) {
                if ($currentSegment !== null) {
                    $currentSegment['daterange'] = $line;
                    // SCTE-35 广告标记
                    if (preg_match('/SCTE35|AD|BREAK/i', $line)) {
                        $currentSegment['is_ad'] = true;
                        $currentSegment['ad_reason'] = 'SCTE-35广告标记';
                        $currentSegment['confidence'] = 0.9;
                    }
                }
                continue;
            }

            // 分段 URL 行（非 # 开头的行）
            if ($line[0] !== '#' && $currentSegment !== null) {
                $currentSegment['url'] = $line;
                $currentSegment['resolved_url'] = $this->resolveUrl($line);
                $currentSegment['domain'] = $this->extractDomain($currentSegment['resolved_url']);
                $segments[] = $currentSegment;
                $segIndex++;
                $currentSegment = null;
                continue;
            }

            // 其他标签附加到当前分段
            if ($currentSegment !== null && strpos($line, '#') === 0) {
                $currentSegment['extra_tags'][] = $line;
            }
        }

        return $segments;
    }

    /**
     * 规则引擎识别广告分段
     */
    private function detectByRules(): void
    {
        $rules = $this->config['ad_rules'];
        $domains = array_filter(array_column($this->segments, 'domain'));
        $domainCounts = array_count_values($domains);

        // 找出主域名（出现次数最多的，视为正片 CDN）
        $mainDomain = '';
        if (!empty($domainCounts)) {
            arsort($domainCounts);
            $mainDomain = array_key_first($domainCounts);
        }

        foreach ($this->segments as &$seg) {
            if ($seg['is_ad']) {
                continue; // 已被 SCTE-35 标记
            }

            $reasons = [];
            $confidence = 0.0;

            // 规则1：URL 关键词匹配
            if ($rules['url_keyword_enabled']) {
                foreach ($rules['url_keywords'] as $keyword) {
                    if (stripos($seg['resolved_url'], $keyword) !== false) {
                        $reasons[] = "URL含关键词'{$keyword}'";
                        $confidence += 0.35;
                        break;
                    }
                }
            }

            // 规则2：不同域名检测
            if ($rules['domain_check_enabled'] && $mainDomain && $seg['domain'] !== $mainDomain) {
                $reasons[] = "不同CDN域名({$seg['domain']}≠{$mainDomain})";
                $confidence += 0.3;
            }

            // 规则3：时长异常检测（广告通常 15/30/45/60 秒）
            if ($rules['duration_check_enabled'] && $seg['duration'] > 0) {
                foreach ($rules['ad_durations'] as $adDur) {
                    if (abs($seg['duration'] - $adDur) <= $rules['duration_tolerance']) {
                        $reasons[] = "时长匹配广告特征({$seg['duration']}s≈{$adDur}s)";
                        $confidence += 0.2;
                        break;
                    }
                }
            }

            // 规则4：不连续标记后的分段更可能是广告
            if ($rules['discontinuity_enabled'] && !empty($seg['after_discontinuity'])) {
                $confidence += 0.15;
                if (empty($reasons)) {
                    $reasons[] = '不连续标记后';
                }
            }

            // 置信度超过阈值则判定为广告
            if ($confidence >= $this->config['ai']['confidence_threshold']) {
                $seg['is_ad'] = true;
                $seg['ad_reason'] = implode('; ', $reasons);
                $seg['confidence'] = min($confidence, 1.0);
            } elseif ($confidence > 0.2) {
                // 低置信度标记，留给 AI 判断
                $seg['ad_reason'] = implode('; ', $reasons);
                $seg['confidence'] = $confidence;
            }
        }
        unset($seg);

        // 后处理：连续的广告分段分组
        $this->groupAdSegments();
    }

    /**
     * 将连续的广告分段分组，记录到 adSegments
     */
    private function groupAdSegments(): void
    {
        $this->adSegments = [];
        $currentGroup = null;
        $segCount = count($this->segments);

        foreach ($this->segments as $seg) {
            if ($seg['is_ad']) {
                if ($currentGroup === null) {
                    $currentGroup = [
                        'type'           => $this->classifyAdType($seg['index'], $segCount),
                        'start_segment'  => $seg['index'],
                        'end_segment'    => $seg['index'],
                        'duration'       => $seg['duration'],
                        'reason'         => $seg['ad_reason'],
                        'confidence'     => $seg['confidence'],
                        'segment_urls'   => [$seg['resolved_url']],
                    ];
                } else {
                    $currentGroup['end_segment'] = $seg['index'];
                    $currentGroup['duration'] += $seg['duration'];
                    $currentGroup['segment_urls'][] = $seg['resolved_url'];
                    $currentGroup['confidence'] = max($currentGroup['confidence'], $seg['confidence']);
                }
            } else {
                if ($currentGroup !== null) {
                    $this->adSegments[] = $currentGroup;
                    $currentGroup = null;
                }
            }
        }
        if ($currentGroup !== null) {
            $this->adSegments[] = $currentGroup;
        }
    }

    /**
     * AI 辅助识别：对低置信度分段调用大模型分析
     */
    private function detectByAI(): void
    {
        // 收集需要 AI 判断的分段（置信度 > 0.2 但未达阈值）
        $uncertainSegs = [];
        foreach ($this->segments as $idx => $seg) {
            if (!$seg['is_ad'] && $seg['confidence'] > 0.2) {
                $uncertainSegs[$idx] = $seg;
            }
        }

        if (empty($uncertainSegs)) {
            return;
        }

        // 构造 AI 请求
        $prompt = $this->buildAIPrompt($uncertainSegs);
        $aiResult = $this->callAI($prompt);

        if ($aiResult && isset($aiResult['ad_segments'])) {
            foreach ($aiResult['ad_segments'] as $segIdx) {
                if (isset($this->segments[$segIdx])) {
                    $this->segments[$segIdx]['is_ad'] = true;
                    $this->segments[$segIdx]['ad_reason'] = 'AI识别: ' . ($aiResult['reasons'][$segIdx] ?? '大模型判定为广告');
                    $this->segments[$segIdx]['confidence'] = 0.8;
                }
            }
            // 重新分组
            $this->groupAdSegments();
        }
    }

    /**
     * 构造 AI 分析提示词
     */
    private function buildAIPrompt(array $uncertainSegs): string
    {
        $segInfo = [];
        foreach ($uncertainSegs as $idx => $seg) {
            $segInfo[] = sprintf(
                '  分段#%d: 时长=%.1fs, 域名=%s, URL=%s, 规则特征=%s',
                $seg['index'],
                $seg['duration'],
                $seg['domain'],
                substr($seg['resolved_url'], 0, 100),
                $seg['ad_reason']
            );
        }

        return sprintf(
            "你是视频流广告识别专家。以下是 HLS m3u8 中规则引擎无法确定是否为广告的分段。\n" .
            "请分析每个分段的特征（域名、URL关键词、时长、位置），判断哪些是广告。\n\n" .
            "分段列表:\n%s\n\n" .
            "主CDN域名: %s\n\n" .
            "请以JSON格式返回，格式如下:\n" .
            '{"ad_segments": [分段索引数组], "reasons": {"索引": "判断原因"}}',
            implode("\n", $segInfo),
            $this->getMainDomain()
        );
    }

    /**
     * 调用 AI 大模型 API
     */
    private function callAI(string $prompt): ?array
    {
        $aiConfig = $this->config['ai'];
        $headers = [
            'Content-Type: application/json',
        ];

        // 按提供商添加认证头
        if ($aiConfig['provider'] === 'openai' || $aiConfig['provider'] === 'qwen' || $aiConfig['provider'] === 'deepseek') {
            $headers[] = 'Authorization: Bearer ' . $aiConfig['api_key'];
        }

        $body = json_encode([
            'model'       => $aiConfig['model'],
            'messages'    => [
                ['role' => 'system', 'content' => '你是视频流广告识别专家，只返回JSON。'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens'  => $aiConfig['max_tokens'],
            'temperature' => 0.1,
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $aiConfig['api_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        if (!$data || !isset($data['choices'][0]['message']['content'])) {
            return null;
        }

        // 从 AI 回复中提取 JSON
        $content = $data['choices'][0]['message']['content'];
        if (preg_match('/\{[\s\S]*\}/', $content, $m)) {
            $result = json_decode($m[0], true);
            return $result ?: null;
        }

        return null;
    }

    /**
     * 生成去广告的 m3u8 内容
     */
    private function generateCleanM3u8(): string
    {
        $cleanSegs = array_filter($this->segments, function ($s) {
            return !$s['is_ad'];
        });

        if (empty($cleanSegs)) {
            // 如果全部被标记为广告（异常情况），返回原始内容
            return $this->rebuildOriginalM3u8();
        }

        $maxDuration = 0;
        foreach ($cleanSegs as $seg) {
            if ($seg['duration'] > $maxDuration) {
                $maxDuration = $seg['duration'];
            }
        }

        $lines = ['#EXTM3U'];
        $lines[] = '#EXT-X-VERSION:3';
        $lines[] = '#EXT-X-TARGETDURATION:' . (int)ceil($maxDuration);

        // 保留加密密钥标签
        if ($this->extKeyLine) {
            $lines[] = $this->extKeyLine;
        }

        $lines[] = '#EXT-X-MEDIA-SEQUENCE:0';

        foreach ($cleanSegs as $seg) {
            $lines[] = $seg['extinf_line'];
            // 保留额外标签
            if (!empty($seg['extra_tags'])) {
                foreach ($seg['extra_tags'] as $tag) {
                    $lines[] = $tag;
                }
            }
            $lines[] = $seg['url'];
        }

        $lines[] = '#EXT-X-ENDLIST';

        return implode("\n", $lines);
    }

    /**
     * 构建广告信息结构
     */
    private function buildAdInfo(int $totalAds, array $adDetails, int $totalSegs, float $cleanDuration): array
    {
        $totalAdDuration = 0;
        $formattedDetails = [];

        foreach ($adDetails as $ad) {
            $totalAdDuration += $ad['duration'];
            $formattedDetails[] = [
                'type'           => $ad['type'],
                'position'       => $this->getAdPositionLabel($ad['type']),
                'start_segment'  => $ad['start_segment'],
                'end_segment'    => $ad['end_segment'],
                'duration'       => round($ad['duration'], 2),
                'reason'         => $ad['reason'],
                'confidence'     => round($ad['confidence'], 2),
            ];
        }

        $totalDuration = $cleanDuration + $totalAdDuration;

        return [
            'has_ads'           => $totalAds > 0,
            'total_ads'         => $totalAds,
            'total_ad_duration' => round($totalAdDuration, 2),
            'details'           => $formattedDetails,
            'video_info'        => [
                'format'          => 'm3u8',
                'total_segments'  => $totalSegs,
                'clean_segments'  => $totalSegs - array_sum(array_map(function ($a) {
                    return $a['end_segment'] - $a['start_segment'] + 1;
                }, $adDetails)),
                'total_duration'  => round($totalDuration, 2),
                'clean_duration'  => round($cleanDuration, 2),
            ],
        ];
    }

    // ============ 工具方法 ============

    /**
     * 计算去广告后的总时长
     */
    private function calculateCleanDuration(): float
    {
        $duration = 0;
        foreach ($this->segments as $seg) {
            if (!$seg['is_ad']) {
                $duration += $seg['duration'];
            }
        }
        return $duration;
    }

    /**
     * 分类广告类型
     */
    private function classifyAdType(int $segIndex, int $totalSegs): string
    {
        if ($segIndex < 3) {
            return 'pre-roll';
        }
        if ($segIndex > $totalSegs - 4) {
            return 'post-roll';
        }
        return 'mid-roll';
    }

    /**
     * 获取广告位置中文标签
     */
    private function getAdPositionLabel(string $type): string
    {
        $labels = [
            'pre-roll'  => '开头广告',
            'mid-roll'  => '中间插播',
            'post-roll' => '结尾广告',
        ];
        return $labels[$type] ?? '未知';
    }

    /**
     * 获取主域名
     */
    private function getMainDomain(): string
    {
        $domains = array_filter(array_column($this->segments, 'domain'));
        if (empty($domains)) {
            return '';
        }
        $counts = array_count_values($domains);
        arsort($counts);
        return array_key_first($counts);
    }

    /**
     * 从 URL 提取域名
     */
    private function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? '';
    }

    /**
     * 解析相对 URL 为绝对 URL
     */
    private function resolveUrl(string $url): string
    {
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }
        if (strpos($url, '//') === 0) {
            return 'https:' . $url;
        }
        // 相对路径
        return rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
    }

    /**
     * 获取 m3u8 的基础 URL（去掉文件名部分）
     */
    private function getBaseUrl(string $url): string
    {
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '/';
        $dir = dirname($path);

        return $scheme . '://' . $host . $port . $dir;
    }

    /**
     * 重建原始 m3u8（兜底）
     */
    private function rebuildOriginalM3u8(): string
    {
        $lines = ['#EXTM3U'];
        foreach ($this->segments as $seg) {
            $lines[] = $seg['extinf_line'];
            if (!empty($seg['extra_tags'])) {
                foreach ($seg['extra_tags'] as $tag) {
                    $lines[] = $tag;
                }
            }
            $lines[] = $seg['url'];
        }
        $lines[] = '#EXT-X-ENDLIST';
        return implode("\n", $lines);
    }
}
