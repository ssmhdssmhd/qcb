<?php
/**
 * 广告特征码管理器
 * 管理从M3U8分析中提取的广告特征码，支持去重和权重累积
 */

class DbAdSignature {
    private $db;

    public function __construct($db = null) {
        $this->db = $db ?: Database::getInstance();
    }

    /**
     * 添加或更新特征码（自动去重，命中次数累加）
     */
    public function addSignature($domain, $type, $value, $weight = 30, $confidence = 50) {
        if (empty($domain) || empty($type) || empty($value)) {
            return false;
        }

        // 查找是否已存在相同的特征码
        $existing = $this->db->queryOne(
            'SELECT id, hit_count, weight, confidence FROM ad_signatures WHERE domain = ? AND signature_type = ? AND signature_value = ? AND status = 1',
            [$domain, $type, $value]
        );

        if ($existing) {
            // 更新命中次数和权重
            $newHitCount = $existing['hit_count'] + 1;
            $newWeight = min(100, $existing['weight'] + 5);
            $newConfidence = min(100, $existing['confidence'] + 5);

            $this->db->update('ad_signatures', [
                'hit_count' => $newHitCount,
                'weight' => $newWeight,
                'confidence' => $newConfidence,
                'last_seen' => date('Y-m-d H:i:s'),
            ], 'id = ?', [$existing['id']]);
            return $existing['id'];
        }

        // 新增特征码
        return $this->db->insert('ad_signatures', [
            'domain' => $domain,
            'signature_type' => $type,
            'signature_value' => $value,
            'weight' => $weight,
            'hit_count' => 1,
            'confidence' => $confidence,
            'first_seen' => date('Y-m-d H:i:s'),
            'last_seen' => date('Y-m-d H:i:s'),
            'status' => 1,
        ]);
    }

    /**
     * 批量添加特征码
     */
    public function addSignatures($domain, $signatures) {
        $added = 0;
        foreach ($signatures as $sig) {
            if ($this->addSignature(
                $domain,
                $sig['type'] ?? 'unknown',
                $sig['value'] ?? '',
                $sig['weight'] ?? 30,
                $sig['confidence'] ?? 50
            )) {
                $added++;
            }
        }
        return $added;
    }

    /**
     * 获取域名的所有特征码
     */
    public function getByDomain($domain, $type = null) {
        $sql = 'SELECT * FROM ad_signatures WHERE domain = ? AND status = 1';
        $params = [$domain];
        if ($type) {
            $sql .= ' AND signature_type = ?';
            $params[] = $type;
        }
        $sql .= ' ORDER BY hit_count DESC, confidence DESC';
        return $this->db->query($sql, $params);
    }

    /**
     * 获取域名的特征码（按类型分组）
     */
    public function getGroupedByDomain($domain) {
        $rows = $this->getByDomain($domain);
        $grouped = [];
        foreach ($rows as $row) {
            $type = $row['signature_type'];
            if (!isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $row;
        }
        return $grouped;
    }

    /**
     * 将特征码转换为规则格式
     */
    public function getRulesForDomain($domain) {
        $grouped = $this->getGroupedByDomain($domain);
        $rules = [
            'duration_rules' => [],
            'discontinuity_rules' => [],
            'sequence_jump_rules' => [],
            'filename_patterns' => [],
        ];

        foreach ($grouped as $type => $sigs) {
            foreach ($sigs as $sig) {
                $value = $sig['signature_value'];
                $weight = (int)$sig['weight'];

                switch ($type) {
                    case 'duration':
                        $rules['duration_rules'][] = [
                            'name' => 'duration_' . md5($value),
                            'threshold' => floatval($value),
                            'operator' => '<',
                            'weight' => $weight,
                            'enabled' => true,
                            'reason' => '特征码:时长<' . $value . 's',
                        ];
                        break;
                    case 'discontinuity':
                        $rules['discontinuity_rules'][] = [
                            'name' => 'discontinuity_' . md5($value),
                            'weight' => $weight,
                            'enabled' => true,
                            'reason' => '特征码:不连续标记',
                        ];
                        break;
                    case 'sequence':
                        $rules['sequence_jump_rules'][] = [
                            'name' => 'sequence_' . md5($value),
                            'jump_threshold' => intval($value),
                            'weight' => $weight,
                            'enabled' => true,
                            'reason' => '特征码:序列跳跃>' . $value,
                        ];
                        break;
                    case 'filename':
                        $rules['filename_patterns'][] = [
                            'pattern' => $value,
                            'weight' => $weight,
                            'enabled' => true,
                        ];
                        break;
                }
            }
        }

        return $rules;
    }

    /**
     * 清理低置信度的特征码
     */
    public function cleanLowConfidence($minConfidence = 30) {
        return $this->db->execute(
            'DELETE FROM ad_signatures WHERE confidence < ? AND hit_count < 3',
            [$minConfidence]
        );
    }

    /**
     * 获取特征码统计
     */
    public function getStats($domain = null) {
        if ($domain) {
            $total = $this->db->queryOne('SELECT COUNT(*) as cnt FROM ad_signatures WHERE domain = ?', [$domain]);
            $types = $this->db->query('SELECT signature_type, COUNT(*) as cnt FROM ad_signatures WHERE domain = ? GROUP BY signature_type', [$domain]);
        } else {
            $total = $this->db->queryOne('SELECT COUNT(*) as cnt FROM ad_signatures');
            $types = $this->db->query('SELECT signature_type, COUNT(*) as cnt FROM ad_signatures GROUP BY signature_type');
        }
        return [
            'total' => $total['cnt'] ?? 0,
            'by_type' => $types,
        ];
    }
}
