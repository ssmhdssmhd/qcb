<?php
/**
 * 域名分析统计管理器
 * 记录各域名的分析和学习统计，用于优化和监控
 */

class DbDomainAnalysisStats {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?: Database::getInstance();
    }

    /**
     * 记录一次分析
     */
    public function recordAnalyze($domain, $totalSegments = 0, $adsDetected = 0, $adPercentage = 0) {
        if (empty($domain)) return false;

        $existing = $this->db->queryOne('SELECT id, analyze_count, total_segments_analyzed, total_ads_detected, avg_ad_percentage FROM domain_analysis_stats WHERE domain = ?', [$domain]);

        if ($existing) {
            $newCount = $existing['analyze_count'] + 1;
            $newTotalSegments = $existing['total_segments_analyzed'] + $totalSegments;
            $newTotalAds = $existing['total_ads_detected'] + $adsDetected;
            $newAvg = $newCount > 0 ? round(($existing['avg_ad_percentage'] * $existing['analyze_count'] + $adPercentage) / $newCount, 2) : 0;

            $this->db->update('domain_analysis_stats', [
                'analyze_count' => $newCount,
                'total_segments_analyzed' => $newTotalSegments,
                'total_ads_detected' => $newTotalAds,
                'avg_ad_percentage' => $newAvg,
                'last_analyze_time' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('domain_analysis_stats', [
                'domain' => $domain,
                'analyze_count' => 1,
                'learn_count' => 0,
                'total_segments_analyzed' => $totalSegments,
                'total_ads_detected' => $adsDetected,
                'avg_ad_percentage' => $adPercentage,
                'last_analyze_time' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }

    /**
     * 记录一次学习
     */
    public function recordLearn($domain) {
        if (empty($domain)) return false;

        $existing = $this->db->queryOne('SELECT id, learn_count FROM domain_analysis_stats WHERE domain = ?', [$domain]);

        if ($existing) {
            $this->db->update('domain_analysis_stats', [
                'learn_count' => $existing['learn_count'] + 1,
                'last_learn_time' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('domain_analysis_stats', [
                'domain' => $domain,
                'analyze_count' => 0,
                'learn_count' => 1,
                'last_learn_time' => date('Y-m-d H:i:s'),
            ]);
        }
        return true;
    }

    /**
     * 获取域名统计
     */
    public function getByDomain($domain) {
        return $this->db->queryOne('SELECT * FROM domain_analysis_stats WHERE domain = ?', [$domain]);
    }

    /**
     * 获取所有统计
     */
    public function getAll() {
        return $this->db->query('SELECT * FROM domain_analysis_stats ORDER BY analyze_count DESC');
    }

    /**
     * 获取热门域名（分析次数最多）
     */
    public function getTopDomains($limit = 10) {
        return $this->db->query('SELECT * FROM domain_analysis_stats ORDER BY analyze_count DESC LIMIT ?', [$limit]);
    }
}
