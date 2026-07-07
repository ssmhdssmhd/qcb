<?php
/**
 * M3U8分析结果缓存管理器
 * 避免重复分析相同URL，支持过期清理
 */

class DbAnalysisCache {
    private $db;
    private $cacheTtl = 86400; // 默认缓存24小时

    public function __construct($db = null) {
        $this->db = $db ?: Database::getInstance();
    }

    private function hashUrl($url) {
        return hash('sha256', $url);
    }

    /**
     * 获取缓存的分析结果
     */
    public function get($url) {
        $hash = $this->hashUrl($url);
        $row = $this->db->queryOne(
            'SELECT * FROM m3u8_analysis_cache WHERE url_hash = ? AND (expires_at IS NULL OR expires_at > NOW())',
            [$hash]
        );
        if (!$row) return null;

        // 解码JSON字段
        $jsonFields = ['duration_rules', 'discontinuity_rules', 'sequence_jump_rules', 'filename_patterns', 'ad_signatures', 'stats'];
        foreach ($jsonFields as $field) {
            if (!empty($row[$field])) {
                $decoded = json_decode($row[$field], true);
                $row[$field] = $decoded !== null ? $decoded : [];
            } else {
                $row[$field] = [];
            }
        }
        return $row;
    }

    /**
     * 保存分析结果到缓存
     */
    public function save($url, $domain, $mediaUrl, $result, $fastMode = false, $safeguardTriggered = false) {
        $hash = $this->hashUrl($url);
        $stats = $result['stats'] ?? [];
        $totalSegments = $stats['totalSegments'] ?? 0;
        $adSegments = $stats['adSegments'] ?? $stats['removedSegments'] ?? 0;
        $adPercentage = $stats['adPercentage'] ?? 0;
        $keptSegments = $stats['keptSegments'] ?? ($totalSegments - $adSegments);
        $originalDuration = (float)($stats['originalDuration'] ?? 0);
        $filteredDuration = (float)($stats['filteredDuration'] ?? 0);
        $savedDuration = (float)($stats['savedDuration'] ?? 0);

        $data = [
            'url_hash' => $hash,
            'url' => $url,
            'domain' => $domain,
            'media_url' => $mediaUrl,
            'total_segments' => $totalSegments,
            'ad_segments' => $adSegments,
            'kept_segments' => $keptSegments,
            'original_duration' => $originalDuration,
            'filtered_duration' => $filteredDuration,
            'saved_duration' => $savedDuration,
            'ad_percentage' => $adPercentage,
            'duration_rules' => json_encode($result['duration_rules'] ?? [], JSON_UNESCAPED_UNICODE),
            'discontinuity_rules' => json_encode($result['discontinuity_rules'] ?? [], JSON_UNESCAPED_UNICODE),
            'sequence_jump_rules' => json_encode($result['sequence_jump_rules'] ?? [], JSON_UNESCAPED_UNICODE),
            'filename_patterns' => json_encode($result['filename_patterns'] ?? [], JSON_UNESCAPED_UNICODE),
            'ad_signatures' => json_encode($result['ad_signatures'] ?? [], JSON_UNESCAPED_UNICODE),
            'stats' => json_encode($stats, JSON_UNESCAPED_UNICODE),
            'fast_mode' => $fastMode ? 1 : 0,
            'safeguard_triggered' => $safeguardTriggered ? 1 : 0,
            'expires_at' => date('Y-m-d H:i:s', time() + $this->cacheTtl),
        ];

        $exists = $this->db->queryOne('SELECT id FROM m3u8_analysis_cache WHERE url_hash = ?', [$hash]);
        if ($exists) {
            unset($data['url_hash'], $data['url']);
            $this->db->update('m3u8_analysis_cache', $data, 'url_hash = ?', [$hash]);
        } else {
            $this->db->insert('m3u8_analysis_cache', $data);
        }
        return true;
    }

    /**
     * 清理过期缓存
     */
    public function cleanExpired() {
        return $this->db->execute('DELETE FROM m3u8_analysis_cache WHERE expires_at IS NOT NULL AND expires_at <= NOW()');
    }

    /**
     * 按域名清理缓存
     */
    public function cleanByDomain($domain) {
        return $this->db->execute('DELETE FROM m3u8_analysis_cache WHERE domain = ?', [$domain]);
    }

    /**
     * 获取缓存统计
     */
    public function getStats() {
        $total = $this->db->queryOne('SELECT COUNT(*) as cnt FROM m3u8_analysis_cache');
        $expired = $this->db->queryOne('SELECT COUNT(*) as cnt FROM m3u8_analysis_cache WHERE expires_at IS NOT NULL AND expires_at <= NOW()');
        return [
            'total' => $total['cnt'] ?? 0,
            'expired' => $expired['cnt'] ?? 0,
        ];
    }
}
