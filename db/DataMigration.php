<?php
/**
 * 数据迁移工具
 * 将原文件存储的数据迁移到数据库中
 */

class DataMigration {
    private $db;
    private $configManager;
    private $baseDir;
    private $gzDir;
    private $proxyDir;
    private $errors = [];
    private $summary = [];

    const MIGRATION_FLAG_KEY = 'data_migration_completed';

    public function __construct($db = null) {
        if ($db === null) {
            $this->db = Database::getInstance();
        } else {
            $this->db = $db;
        }
        $this->configManager = new DbConfigManager($this->db);
        $this->baseDir = dirname(__DIR__);
        $this->gzDir = $this->baseDir . '/gz';
        $this->proxyDir = $this->baseDir . '/proxy';
    }

    public function migrateAll() {
        $this->errors = [];
        $this->summary = [];

        try {
            $this->db->initTables();
        } catch (Exception $e) {
            $this->addError('init_tables', '初始化数据库表失败: ' . $e->getMessage());
            return $this->buildResult(false);
        }

        $this->migrateDomainRules();
        $this->migrateResourceSites();
        $this->migrateProxies();
        $this->migrateOfficialSites();
        $this->migrateOfficialPlatforms();
        $this->migrateAutoLearnConfig();

        $this->markMigrated();

        return $this->buildResult(empty($this->errors));
    }

    public function migrateDomainRules() {
        $migrated = 0;
        $skipped = 0;

        try {
            $files = glob($this->gzDir . '/rules_*.php');
            if (!is_array($files)) {
                $files = [];
            }

            foreach ($files as $file) {
                try {
                    $domainRules = @require $file;
                    if (!is_array($domainRules) || empty($domainRules['domain']) || !is_string($domainRules['domain'])) {
                        $skipped++;
                        continue;
                    }

                    $domain = $domainRules['domain'];
                    $normalized = $this->normalizeDomainRules($domainRules);

                    $existing = $this->db->queryOne(
                        'SELECT id FROM domain_rules WHERE domain = ?',
                        [$domain]
                    );

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $jsonFields = [
                        'duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
                        'filename_patterns', 'insertion_patterns', 'ad_type_stats',
                        'psychological_profile', 'marker_stats', 'analysis_stats',
                        'marker_detection', 'confidence'
                    ];

                    $insertData = [];
                    foreach ($normalized as $key => $value) {
                        if ($key === 'history_stats') {
                            $insertData[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
                        } elseif (in_array($key, $jsonFields)) {
                            $insertData[$key] = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : null;
                        } else {
                            $insertData[$key] = $value;
                        }
                    }

                    $insertData['status'] = 1;
                    $this->db->insert('domain_rules', $insertData);
                    $migrated++;

                } catch (Exception $e) {
                    $this->addError('domain_rules', '迁移文件 ' . basename($file) . ' 失败: ' . $e->getMessage());
                    $skipped++;
                }
            }
        } catch (Exception $e) {
            $this->addError('domain_rules', '迁移域名规则失败: ' . $e->getMessage());
        }

        $this->summary['domain_rules'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function migrateResourceSites() {
        $migrated = 0;
        $skipped = 0;

        try {
            $configFile = $this->gzDir . '/sites_config.php';
            if (!file_exists($configFile)) {
                $this->summary['resource_sites'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $config = @require $configFile;
            if (!is_array($config) || empty($config['sites']) || !is_array($config['sites'])) {
                $this->summary['resource_sites'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            foreach ($config['sites'] as $site) {
                try {
                    $name = $site['name'] ?? '';
                    if (empty($name)) {
                        $skipped++;
                        continue;
                    }

                    $existing = $this->db->queryOne(
                        'SELECT id FROM resource_sites WHERE name = ?',
                        [$name]
                    );

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $insertData = [
                        'name' => $name,
                        'site_url' => $site['site_url'] ?? '',
                        'api_url' => $site['api_url'] ?? '',
                        'type' => $site['type'] ?? 'maccms',
                        'status' => $site['status'] ?? 'active',
                        'priority' => $site['priority'] ?? 99,
                        'note' => $site['note'] ?? '',
                    ];

                    $extraConfig = [];
                    foreach ($site as $k => $v) {
                        if (!in_array($k, ['name', 'site_url', 'api_url', 'type', 'status', 'priority', 'note'])) {
                            $extraConfig[$k] = $v;
                        }
                    }
                    if (!empty($extraConfig)) {
                        $insertData['config'] = json_encode($extraConfig, JSON_UNESCAPED_UNICODE);
                    }

                    $this->db->insert('resource_sites', $insertData);
                    $migrated++;

                } catch (Exception $e) {
                    $this->addError('resource_sites', '迁移资源站 ' . ($site['name'] ?? '未知') . ' 失败: ' . $e->getMessage());
                    $skipped++;
                }
            }
        } catch (Exception $e) {
            $this->addError('resource_sites', '迁移资源站失败: ' . $e->getMessage());
        }

        $this->summary['resource_sites'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function migrateProxies() {
        $migrated = 0;
        $skipped = 0;

        try {
            $configFile = $this->proxyDir . '/proxy_config.php';
            if (!file_exists($configFile)) {
                $this->summary['proxies'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $config = @require $configFile;
            if (!is_array($config) || empty($config['proxies']) || !is_array($config['proxies'])) {
                $this->summary['proxies'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            foreach ($config['proxies'] as $proxy) {
                try {
                    $proxyId = $proxy['id'] ?? '';
                    if (empty($proxyId)) {
                        $proxyId = md5(($proxy['type'] ?? 'http') . '://' . ($proxy['host'] ?? '') . ':' . ($proxy['port'] ?? 0));
                    }

                    $existing = $this->db->queryOne(
                        'SELECT id FROM proxies WHERE proxy_id = ?',
                        [$proxyId]
                    );

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $insertData = [
                        'proxy_id' => $proxyId,
                        'name' => $proxy['name'] ?? '',
                        'type' => $proxy['type'] ?? 'http',
                        'host' => $proxy['host'] ?? '',
                        'port' => intval($proxy['port'] ?? 0),
                        'username' => $proxy['username'] ?? '',
                        'password' => $proxy['password'] ?? '',
                        'status' => $proxy['status'] ?? 'active',
                        'priority' => $proxy['priority'] ?? 100,
                        'success_count' => $proxy['success_count'] ?? 0,
                        'fail_count' => $proxy['fail_count'] ?? 0,
                        'response_time' => $proxy['response_time'] ?? 0,
                        'source' => $proxy['source'] ?? 'manual',
                    ];

                    if (!empty($proxy['last_check'])) {
                        $insertData['last_check'] = $proxy['last_check'];
                    }
                    if (!empty($proxy['last_success'])) {
                        $insertData['last_success'] = $proxy['last_success'];
                    }

                    $this->db->insert('proxies', $insertData);
                    $migrated++;

                } catch (Exception $e) {
                    $this->addError('proxies', '迁移代理 ' . ($proxy['name'] ?? ($proxy['host'] ?? '未知')) . ' 失败: ' . $e->getMessage());
                    $skipped++;
                }
            }
        } catch (Exception $e) {
            $this->addError('proxies', '迁移代理失败: ' . $e->getMessage());
        }

        $this->summary['proxies'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function migrateOfficialSites() {
        $migrated = 0;
        $skipped = 0;

        try {
            $configFile = $this->gzDir . '/official_sites_config.php';
            if (!file_exists($configFile)) {
                $this->summary['official_sites'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $config = @require $configFile;
            if (!is_array($config) || empty($config['sites']) || !is_array($config['sites'])) {
                $this->summary['official_sites'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            foreach ($config['sites'] as $site) {
                try {
                    $name = $site['name'] ?? '';
                    if (empty($name)) {
                        $skipped++;
                        continue;
                    }

                    $existing = $this->db->queryOne(
                        'SELECT id FROM official_sites WHERE name = ?',
                        [$name]
                    );

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $insertData = [
                        'name' => $name,
                        'api_path' => $site['api_path'] ?? '/api.php/provide/vod/',
                        'active_domain_index' => $site['active_domain_index'] ?? 0,
                        'domains' => json_encode($site['domains'] ?? [], JSON_UNESCAPED_UNICODE),
                        'status' => $site['status'] ?? 'active',
                        'priority' => $site['priority'] ?? 99,
                        'note' => $site['note'] ?? '',
                    ];

                    $extraConfig = [];
                    foreach ($site as $k => $v) {
                        if (!in_array($k, ['name', 'api_path', 'active_domain_index', 'domains', 'status', 'priority', 'note'])) {
                            $extraConfig[$k] = $v;
                        }
                    }
                    if (!empty($extraConfig)) {
                        $insertData['config'] = json_encode($extraConfig, JSON_UNESCAPED_UNICODE);
                    }

                    $this->db->insert('official_sites', $insertData);
                    $migrated++;

                } catch (Exception $e) {
                    $this->addError('official_sites', '迁移推荐采集站 ' . ($site['name'] ?? '未知') . ' 失败: ' . $e->getMessage());
                    $skipped++;
                }
            }
        } catch (Exception $e) {
            $this->addError('official_sites', '迁移推荐采集站失败: ' . $e->getMessage());
        }

        $this->summary['official_sites'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function migrateOfficialPlatforms() {
        $migrated = 0;
        $skipped = 0;

        try {
            $configFile = $this->gzDir . '/official_replace_config.php';
            if (!file_exists($configFile)) {
                $this->summary['official_platforms'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $config = @require $configFile;
            if (!is_array($config) || empty($config['platforms']) || !is_array($config['platforms'])) {
                $this->summary['official_platforms'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            foreach ($config['platforms'] as $platform) {
                try {
                    $domain = $platform['domain'] ?? '';
                    if (empty($domain)) {
                        $skipped++;
                        continue;
                    }

                    $existing = $this->db->queryOne(
                        'SELECT id FROM official_platforms WHERE domain = ?',
                        [$domain]
                    );

                    if ($existing) {
                        $skipped++;
                        continue;
                    }

                    $insertData = [
                        'name' => $platform['name'] ?? $domain,
                        'domain' => $domain,
                        'enabled' => isset($platform['enabled']) ? intval($platform['enabled']) : 1,
                        'pattern' => $platform['pattern'] ?? '',
                        'title_selector' => $platform['title_selector'] ?? '',
                        'priority' => $platform['priority'] ?? 1,
                    ];

                    $extraConfig = [];
                    foreach ($platform as $k => $v) {
                        if (!in_array($k, ['name', 'domain', 'enabled', 'pattern', 'title_selector', 'priority'])) {
                            $extraConfig[$k] = $v;
                        }
                    }
                    if (!empty($extraConfig)) {
                        $insertData['config'] = json_encode($extraConfig, JSON_UNESCAPED_UNICODE);
                    }

                    $this->db->insert('official_platforms', $insertData);
                    $migrated++;

                } catch (Exception $e) {
                    $this->addError('official_platforms', '迁移官替平台 ' . ($platform['name'] ?? ($platform['domain'] ?? '未知')) . ' 失败: ' . $e->getMessage());
                    $skipped++;
                }
            }
        } catch (Exception $e) {
            $this->addError('official_platforms', '迁移官替平台失败: ' . $e->getMessage());
        }

        $this->summary['official_platforms'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function migrateAutoLearnConfig() {
        $migrated = 0;
        $skipped = 0;

        try {
            $configFile = $this->gzDir . '/sites_config.php';
            if (!file_exists($configFile)) {
                $this->summary['auto_learn_config'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $config = @require $configFile;
            if (!is_array($config) || empty($config['auto_learn']) || !is_array($config['auto_learn'])) {
                $this->summary['auto_learn_config'] = ['migrated' => 0, 'skipped' => 0];
                return ['migrated' => 0, 'skipped' => 0];
            }

            $existing = $this->configManager->get('auto_learn', null);
            if ($existing !== null) {
                $skipped++;
            } else {
                $this->configManager->set('auto_learn', $config['auto_learn'], '自动学习配置');
                $migrated++;
            }

            $officialConfigFile = $this->gzDir . '/official_replace_config.php';
            if (file_exists($officialConfigFile)) {
                $officialConfig = @require $officialConfigFile;
                if (is_array($officialConfig)) {
                    $replaceConfig = [
                        'enabled' => $officialConfig['enabled'] ?? true,
                        'default_site' => $officialConfig['default_site'] ?? '量子',
                        'max_search_sites' => $officialConfig['max_search_sites'] ?? 5,
                        'cache_ttl' => $officialConfig['cache_ttl'] ?? 3600,
                    ];
                    $extraFields = [];
                    foreach ($officialConfig as $k => $v) {
                        if (!in_array($k, ['enabled', 'default_site', 'max_search_sites', 'cache_ttl', 'version', 'update_date', 'platforms', 'search_sites', 'match_threshold'])) {
                            $extraFields[$k] = $v;
                        }
                    }
                    if (!empty($extraFields)) {
                        $replaceConfig = array_merge($replaceConfig, $extraFields);
                    }

                    $existingReplace = $this->configManager->get('official_replace', null);
                    if ($existingReplace === null) {
                        $this->configManager->set('official_replace', $replaceConfig, '官替API配置');
                        $migrated++;
                    } else {
                        $skipped++;
                    }
                }
            }

            $officialSitesFile = $this->gzDir . '/official_sites_config.php';
            if (file_exists($officialSitesFile)) {
                $officialSitesConfig = @require $officialSitesFile;
                if (is_array($officialSitesConfig)) {
                    $sitesConfig = [
                        'enabled' => $officialSitesConfig['enabled'] ?? true,
                        'settings' => $officialSitesConfig['settings'] ?? [
                            'auto_switch_domain' => true,
                            'max_retry_per_domain' => 2,
                            'timeout' => 10,
                            'default_limit' => 20,
                        ],
                    ];

                    $existingSites = $this->configManager->get('official_sites', null);
                    if ($existingSites === null) {
                        $this->configManager->set('official_sites', $sitesConfig, '推荐采集配置');
                        $migrated++;
                    } else {
                        $skipped++;
                    }
                }
            }

            $proxyConfigFile = $this->proxyDir . '/proxy_config.php';
            if (file_exists($proxyConfigFile)) {
                $proxyConfig = @require $proxyConfigFile;
                if (is_array($proxyConfig)) {
                    $proxyDbConfig = [
                        'enabled' => $proxyConfig['enabled'] ?? false,
                        'auto_switch' => $proxyConfig['auto_switch'] ?? true,
                        'check_interval' => $proxyConfig['check_interval'] ?? 300,
                        'timeout' => $proxyConfig['timeout'] ?? 10,
                    ];

                    $existingProxy = $this->configManager->get('proxy_config', null);
                    if ($existingProxy === null) {
                        $this->configManager->set('proxy_config', $proxyDbConfig, '代理配置');
                        $migrated++;
                    } else {
                        $skipped++;
                    }
                }
            }

        } catch (Exception $e) {
            $this->addError('auto_learn_config', '迁移自动学习配置失败: ' . $e->getMessage());
        }

        $this->summary['auto_learn_config'] = [
            'migrated' => $migrated,
            'skipped' => $skipped,
        ];

        return ['migrated' => $migrated, 'skipped' => $skipped];
    }

    public function isMigrated() {
        return $this->configManager->get(self::MIGRATION_FLAG_KEY, false) === true;
    }

    public function markMigrated() {
        $this->configManager->set(self::MIGRATION_FLAG_KEY, true, '数据迁移完成标记');
        return true;
    }

    private function normalizeDomainRules($rules) {
        if (!is_array($rules)) {
            $rules = [];
        }

        $arrayFields = ['duration_rules', 'discontinuity_rules', 'sequence_jump_rules',
                        'filename_patterns', 'insertion_patterns', 'ad_type_stats',
                        'psychological_profile', 'history_stats', 'marker_stats',
                        'analysis_stats', 'marker_detection', 'confidence'];

        foreach ($arrayFields as $field) {
            if (!isset($rules[$field]) || !is_array($rules[$field])) {
                $rules[$field] = [];
            }
        }

        $intFields = ['learn_count', 'ad_threshold', 'confidence_score', 'enable_marker_detection'];
        foreach ($intFields as $field) {
            if (!isset($rules[$field]) || !is_numeric($rules[$field])) {
                $rules[$field] = ($field === 'ad_threshold') ? 50 : 0;
            } else {
                $rules[$field] = intval($rules[$field]);
            }
        }

        if (!isset($rules['name']) || !is_string($rules['name'])) {
            $rules['name'] = $rules['domain'] ?? '';
        }

        if (!isset($rules['note']) || !is_string($rules['note'])) {
            $rules['note'] = '';
        }

        return $rules;
    }

    private function addError($category, $message) {
        $this->errors[] = [
            'category' => $category,
            'message' => $message,
            'time' => date('Y-m-d H:i:s'),
        ];
    }

    private function buildResult($success) {
        return [
            'success' => $success,
            'summary' => $this->summary,
            'errors' => $this->errors,
        ];
    }
}
