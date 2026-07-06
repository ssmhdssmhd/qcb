<?php
/**
 * 数据库配置管理器
 * 管理 sys_config 表的键值对配置
 */

class DbConfigManager {
    private $db;
    private $cache = [];

    public function __construct($db = null) {
        if ($db === null) {
            $this->db = Database::getInstance();
        } else {
            $this->db = $db;
        }
    }

    public function get($key, $default = null) {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $row = $this->db->queryOne(
            'SELECT config_value FROM sys_config WHERE config_key = ?',
            [$key]
        );

        if ($row === null) {
            $this->cache[$key] = $default;
            return $default;
        }

        $value = $row['config_value'];
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->cache[$key] = $decoded;
            return $decoded;
        }

        $this->cache[$key] = $value;
        return $value;
    }

    public function set($key, $value, $description = '') {
        $encoded = is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;

        $existing = $this->db->queryOne(
            'SELECT id FROM sys_config WHERE config_key = ?',
            [$key]
        );

        if ($existing) {
            $this->db->update(
                'sys_config',
                [
                    'config_value' => $encoded,
                    'description' => $description,
                ],
                'config_key = :where_key',
                [':where_key' => $key]
            );
        } else {
            $this->db->insert('sys_config', [
                'config_key' => $key,
                'config_value' => $encoded,
                'description' => $description,
            ]);
        }

        $this->cache[$key] = $value;
        return true;
    }

    public function delete($key) {
        $result = $this->db->delete(
            'sys_config',
            'config_key = ?',
            [$key]
        );
        unset($this->cache[$key]);
        return $result;
    }

    public function getAll() {
        $rows = $this->db->query('SELECT config_key, config_value, description FROM sys_config');
        $result = [];
        foreach ($rows as $row) {
            $key = $row['config_key'];
            $value = $row['config_value'];
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $result[$key] = [
                    'value' => $decoded,
                    'description' => $row['description'],
                ];
            } else {
                $result[$key] = [
                    'value' => $value,
                    'description' => $row['description'],
                ];
            }
        }
        return $result;
    }

    public function has($key) {
        $row = $this->db->queryOne(
            'SELECT COUNT(*) as cnt FROM sys_config WHERE config_key = ?',
            [$key]
        );
        return $row && $row['cnt'] > 0;
    }

    public function clearCache() {
        $this->cache = [];
    }
}
