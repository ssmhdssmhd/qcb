<?php
/**
 * 官替结果缓存管理器
 * 缓存官方URL替换结果，避免重复抓取和搜索
 */

class DbOfficialReplaceCache {
    private $db;
    private $cacheTtl = 86400; // 默认缓存24小时

    public function __construct($db = null) {
        $this->db = $db ?: Database::getInstance();
    }

    private function hashUrl($url) {
        return hash('sha256', $url);
    }

    /**
     * 获取缓存的官替结果
     */
    public function get($url) {
        $hash = $this->hashUrl($url);
        $row = $this->db->queryOne(
            'SELECT * FROM official_replace_cache WHERE original_url_hash = ? AND status = 1 AND (expires_at IS NULL OR expires_at > NOW())',
            [$hash]
        );
        if (!$row) return null;

        if (!empty($row['video_info'])) {
            $row['video_info'] = json_decode($row['video_info'], true) ?: [];
        } else {
            $row['video_info'] = [];
        }
        return $row;
    }

    /**
     * 保存官替结果到缓存
     */
    public function save($originalUrl, $platform, $title, $baseTitle, $seasonNum, $episodeNum, $m3u8Url, $matchScore, $site, $videoInfo = []) {
        $hash = $this->hashUrl($originalUrl);

        $data = [
            'original_url_hash' => $hash,
            'original_url' => $originalUrl,
            'platform' => $platform,
            'video_title' => $title,
            'base_title' => $baseTitle,
            'season_num' => $seasonNum,
            'episode_num' => $episodeNum,
            'm3u8_url' => $m3u8Url,
            'match_score' => $matchScore,
            'site' => $site,
            'video_info' => json_encode($videoInfo, JSON_UNESCAPED_UNICODE),
            'status' => 1,
            'expires_at' => date('Y-m-d H:i:s', time() + $this->cacheTtl),
        ];

        $exists = $this->db->queryOne('SELECT id FROM official_replace_cache WHERE original_url_hash = ?', [$hash]);
        if ($exists) {
            unset($data['original_url_hash'], $data['original_url']);
            $this->db->update('official_replace_cache', $data, 'original_url_hash = ?', [$hash]);
        } else {
            $this->db->insert('official_replace_cache', $data);
        }
        return true;
    }

    /**
     * 清理过期缓存
     */
    public function cleanExpired() {
        return $this->db->execute('DELETE FROM official_replace_cache WHERE expires_at IS NOT NULL AND expires_at <= NOW()');
    }

    /**
     * 标记缓存为无效
     */
    public function invalidate($url) {
        $hash = $this->hashUrl($url);
        return $this->db->update('official_replace_cache', ['status' => 0], 'original_url_hash = ?', [$hash]);
    }

    /**
     * 获取缓存统计
     */
    public function getStats() {
        $total = $this->db->queryOne('SELECT COUNT(*) as cnt FROM official_replace_cache');
        $valid = $this->db->queryOne('SELECT COUNT(*) as cnt FROM official_replace_cache WHERE status = 1');
        $expired = $this->db->queryOne('SELECT COUNT(*) as cnt FROM official_replace_cache WHERE expires_at IS NOT NULL AND expires_at <= NOW()');
        return [
            'total' => $total['cnt'] ?? 0,
            'valid' => $valid['cnt'] ?? 0,
            'expired' => $expired['cnt'] ?? 0,
        ];
    }
}
