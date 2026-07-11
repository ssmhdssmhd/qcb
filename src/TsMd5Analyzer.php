<?php
/**
 * TS 片段 MD5 特征码分析器
 * 通过分析 TS 文件的 MD5 哈希值来识别广告和插播片段
 * 
 * 原理：
 * - 同一资源站的广告片段通常是相同的，具有相同的 MD5 值
 * - 通过分析多个视频的 TS 片段 MD5，可以识别出重复出现的广告片段
 * - 内容片段通常是唯一的，而广告片段会在多个视频中重复出现
 */

class TsMd5Analyzer {
    private $db;
    private $domain;
    
    public function __construct($domain = '') {
        $this->domain = $domain;
        try {
            $this->db = Database::getInstance();
        } catch (Throwable $e) {
            $this->db = null;
        }
    }
    
    /**
     * 计算单个 TS 文件的 MD5 哈希值
     */
    public function calculateTsMd5($tsUrl) {
        try {
            $content = $this->fetchTsContent($tsUrl);
            if ($content === false || empty($content)) {
                return null;
            }
            return md5($content);
        } catch (Throwable $e) {
            return null;
        }
    }
    
    /**
     * 批量计算 TS 片段的 MD5
     */
    public function batchCalculateMd5($segments, $baseUrl = '') {
        $results = [];
        $maxCount = min(count($segments), 50);
        
        for ($i = 0; $i < $maxCount; $i++) {
            $seg = $segments[$i];
            $uri = $seg['uri'] ?? '';
            if (empty($uri)) continue;
            
            $fullUrl = $this->resolveTsUrl($uri, $baseUrl);
            $md5 = $this->calculateTsMd5($fullUrl);
            
            $results[] = [
                'index' => $i,
                'uri' => $uri,
                'url' => $fullUrl,
                'md5' => $md5,
                'duration' => $seg['duration'] ?? 0,
                'media_sequence' => $seg['mediaSequence'] ?? null
            ];
        }
        
        return $results;
    }
    
    /**
     * 分析 M3U8 中的 TS 片段 MD5 特征
     * 识别可能的广告片段（基于 MD5 重复模式）
     */
    public function analyzeMd5Signatures($segments, $baseUrl = '') {
        $md5Data = $this->batchCalculateMd5($segments, $baseUrl);
        
        $md5Counts = [];
        $md5Segments = [];
        
        foreach ($md5Data as $item) {
            $md5 = $item['md5'];
            if (!$md5) continue;
            
            if (!isset($md5Counts[$md5])) {
                $md5Counts[$md5] = 0;
                $md5Segments[$md5] = [];
            }
            $md5Counts[$md5]++;
            $md5Segments[$md5][] = $item;
        }
        
        $adCandidates = [];
        $contentCandidates = [];
        
        foreach ($md5Counts as $md5 => $count) {
            $segs = $md5Segments[$md5];
            $avgDuration = count($segs) > 0 
                ? array_sum(array_column($segs, 'duration')) / count($segs) 
                : 0;
            
            $signature = [
                'md5' => $md5,
                'count' => $count,
                'avg_duration' => round($avgDuration, 3),
                'segments' => array_slice($segs, 0, 10),
                'total_duration' => round($count * $avgDuration, 3)
            ];
            
            if ($count >= 2 && $avgDuration < 15) {
                $adCandidates[] = $signature;
            } else {
                $contentCandidates[] = $signature;
            }
        }
        
        usort($adCandidates, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return [
            'total_analyzed' => count($md5Data),
            'unique_md5' => count($md5Counts),
            'ad_candidates' => $adCandidates,
            'content_candidates' => $contentCandidates,
            'md5_details' => $md5Data
        ];
    }
    
    /**
     * 保存 MD5 特征码到数据库
     */
    public function saveMd5Signatures($domain, $md5Signatures) {
        if (!$this->db || empty($domain)) return 0;
        
        $saved = 0;
        foreach ($md5Signatures as $sig) {
            $md5 = $sig['md5'] ?? '';
            if (empty($md5)) continue;
            
            $weight = min(100, 30 + ($sig['count'] ?? 1) * 10);
            $confidence = min(100, 40 + ($sig['count'] ?? 1) * 15);
            
            try {
                $existing = $this->db->queryOne(
                    'SELECT id, hit_count FROM ts_md5_signatures WHERE domain = ? AND md5 = ? AND status = 1',
                    [$domain, $md5]
                );
                
                if ($existing) {
                    $this->db->update('ts_md5_signatures', [
                        'hit_count' => $existing['hit_count'] + 1,
                        'last_seen' => date('Y-m-d H:i:s'),
                    ], 'id = ?', [$existing['id']]);
                } else {
                    $this->db->insert('ts_md5_signatures', [
                        'domain' => $domain,
                        'md5' => $md5,
                        'avg_duration' => $sig['avg_duration'] ?? 0,
                        'ad_type' => 'unknown',
                        'weight' => $weight,
                        'hit_count' => 1,
                        'confidence' => $confidence,
                        'first_seen' => date('Y-m-d H:i:s'),
                        'last_seen' => date('Y-m-d H:i:s'),
                        'status' => 1,
                    ]);
                }
                $saved++;
            } catch (Throwable $e) {
            }
        }
        
        return $saved;
    }
    
    /**
     * 从数据库获取域名的 MD5 特征码
     */
    public function getMd5Signatures($domain, $limit = 100) {
        if (!$this->db || empty($domain)) return [];
        
        try {
            return $this->db->query(
                'SELECT * FROM ts_md5_signatures WHERE domain = ? AND status = 1 ORDER BY hit_count DESC, confidence DESC LIMIT ?',
                [$domain, $limit]
            );
        } catch (Throwable $e) {
            return [];
        }
    }
    
    /**
     * 检测 TS 片段是否为广告（基于 MD5 特征库）
     */
    public function detectAdByMd5($md5, $domain = '') {
        if (!$this->db || empty($md5)) return false;
        
        $domain = $domain ?: $this->domain;
        if (empty($domain)) return false;
        
        try {
            $sig = $this->db->queryOne(
                'SELECT * FROM ts_md5_signatures WHERE domain = ? AND md5 = ? AND status = 1',
                [$domain, $md5]
            );
            
            if ($sig && $sig['confidence'] >= 50 && $sig['hit_count'] >= 2) {
                return [
                    'is_ad' => true,
                    'confidence' => $sig['confidence'],
                    'hit_count' => $sig['hit_count'],
                    'weight' => $sig['weight'],
                    'avg_duration' => $sig['avg_duration']
                ];
            }
        } catch (Throwable $e) {
        }
        
        return ['is_ad' => false];
    }
    
    /**
     * 解析 TS URL（相对路径转绝对路径）
     */
    private function resolveTsUrl($uri, $baseUrl) {
        if (empty($baseUrl)) return $uri;
        
        if (filter_var($uri, FILTER_VALIDATE_URL)) {
            return $uri;
        }
        
        $baseInfo = parse_url($baseUrl);
        if (!$baseInfo) return $uri;
        
        $basePath = dirname($baseInfo['path'] ?? '/');
        $basePath = str_replace('\\', '/', $basePath);
        
        if (strpos($uri, '/') === 0) {
            return $baseInfo['scheme'] . '://' . $baseInfo['host'] . $uri;
        }
        
        $path = $basePath . '/' . $uri;
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') continue;
            if ($part === '..') {
                array_pop($parts);
            } else {
                $parts[] = $part;
            }
        }
        
        return $baseInfo['scheme'] . '://' . $baseInfo['host'] . '/' . implode('/', $parts);
    }
    
    /**
     * 获取 TS 文件内容
     */
    private function fetchTsContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RANGE, '0-1048575');
        
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: */*',
            'Accept-Language: zh-CN,zh;q=0.9',
            'Connection: keep-alive',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300 && $content !== false) {
            return $content;
        }
        
        return false;
    }
}
