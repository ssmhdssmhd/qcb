<?php
/**
 * 数据库版推荐采集站管理器
 * 使用数据库存储配置，完全兼容原 OfficialSiteManager 接口
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../gz/ResourceSiteManager.php';

class DbOfficialSiteManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureTables();
    }

    private function ensureTables() {
        if (!$this->db->tableExists('official_sites')) {
            $this->db->initTables();
        }
        if (!$this->db->tableExists('sys_config')) {
            $this->db->initTables();
        }
    }

    private function parseSiteRow($row) {
        if (!$row) return null;
        $site = $row;

        if (!empty($site['domains'])) {
            $domains = json_decode($site['domains'], true);
            if (is_array($domains)) {
                $site['domains'] = $domains;
            } else {
                $site['domains'] = [];
            }
        } else {
            $site['domains'] = [];
        }

        if (!empty($site['config'])) {
            $config = json_decode($site['config'], true);
            if (is_array($config)) {
                $site = array_merge($config, $site);
            }
        }
        unset($site['config']);

        if (!isset($site['is_official'])) {
            $site['is_official'] = true;
        }

        return $site;
    }

    private function prepareSiteData($siteData) {
        $coreFields = ['name', 'api_path', 'active_domain_index', 'domains', 'status', 'priority', 'note'];
        $coreData = [];
        $extraConfig = [];

        foreach ($siteData as $key => $value) {
            if (in_array($key, $coreFields)) {
                if ($key === 'domains') {
                    if (is_array($value)) {
                        $coreData[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                    } else {
                        $domains = array_filter(array_map('trim', explode("\n", $value)));
                        $coreData[$key] = json_encode($domains, JSON_UNESCAPED_UNICODE);
                    }
                } elseif ($key === 'priority' || $key === 'active_domain_index') {
                    $coreData[$key] = intval($value);
                } else {
                    $coreData[$key] = $value;
                }
            } else {
                $extraConfig[$key] = $value;
            }
        }

        if (!empty($extraConfig)) {
            $coreData['config'] = json_encode($extraConfig, JSON_UNESCAPED_UNICODE);
        }

        return $coreData;
    }

    public function getAllSites($includePaused = false) {
        $sql = 'SELECT * FROM official_sites';
        $params = [];
        if (!$includePaused) {
            $sql .= ' WHERE status = :status';
            $params[':status'] = 'active';
        }
        $sql .= ' ORDER BY priority ASC';

        $rows = $this->db->query($sql, $params);
        $sites = [];
        foreach ($rows as $row) {
            $sites[] = $this->parseSiteRow($row);
        }
        return $sites;
    }

    public function getSiteByName($name) {
        $row = $this->db->queryOne('SELECT * FROM official_sites WHERE name = ?', [$name]);
        return $this->parseSiteRow($row);
    }

    public function getSiteById($id) {
        $row = $this->db->queryOne('SELECT * FROM official_sites WHERE id = ?', [$id]);
        return $this->parseSiteRow($row);
    }

    public function addSite($siteData) {
        $site = array_merge([
            'name' => '',
            'code' => '',
            'site_url' => '',
            'api_url' => '',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 99,
            'is_official' => true,
            'domains' => [],
            'active_domain_index' => 0,
            'api_path' => '/api.php/provide/vod/'
        ], $siteData);

        if (empty($site['name'])) {
            return ['success' => false, 'message' => '资源站名称不能为空'];
        }

        $exists = $this->getSiteByName($site['name']);
        if ($exists) {
            return ['success' => false, 'message' => '资源站名称已存在'];
        }

        $data = $this->prepareSiteData($site);
        $this->db->insert('official_sites', $data);

        return ['success' => true, 'message' => '添加成功'];
    }

    public function updateSite($name, $siteData) {
        $exists = $this->getSiteByName($name);
        if (!$exists) {
            return ['success' => false, 'message' => '资源站不存在'];
        }

        if (isset($siteData['name'])) {
            unset($siteData['name']);
        }

        $data = $this->prepareSiteData($siteData);
        if (empty($data)) {
            return ['success' => true, 'message' => '更新成功'];
        }

        $this->db->update('official_sites', $data, 'name = ?', [$name]);
        return ['success' => true, 'message' => '更新成功'];
    }

    public function deleteSite($name) {
        $exists = $this->getSiteByName($name);
        if (!$exists) {
            return ['success' => false, 'message' => '资源站不存在'];
        }

        $this->db->delete('official_sites', 'name = ?', [$name]);
        return ['success' => true, 'message' => '删除成功'];
    }

    public function isEnabled() {
        $config = $this->getFullConfig();
        return !empty($config['enabled']);
    }

    public function setEnabled($enabled) {
        $config = $this->getFullConfig();
        $config['enabled'] = (bool)$enabled;
        $config['update_date'] = date('Y-m-d');
        $this->saveFullConfig($config);
        return ['success' => true, 'message' => '设置已更新'];
    }

    public function getSettings() {
        $config = $this->getFullConfig();
        return $config['settings'] ?? [
            'auto_switch_domain' => true,
            'max_retry_per_domain' => 2,
            'timeout' => 10,
            'default_limit' => 20
        ];
    }

    public function saveSettings($settings) {
        $config = $this->getFullConfig();
        $currentSettings = $config['settings'] ?? [
            'auto_switch_domain' => true,
            'max_retry_per_domain' => 2,
            'timeout' => 10,
            'default_limit' => 20
        ];
        $config['settings'] = array_merge($currentSettings, $settings);
        $config['update_date'] = date('Y-m-d');
        $this->saveFullConfig($config);
        return ['success' => true, 'message' => '设置已更新'];
    }

    private function getFullConfig() {
        $row = $this->db->queryOne('SELECT config_value FROM sys_config WHERE config_key = ?', ['official_sites']);
        if ($row && !empty($row['config_value'])) {
            $config = json_decode($row['config_value'], true);
            if (is_array($config)) {
                return $config;
            }
        }
        return [
            'version' => '1.0',
            'update_date' => date('Y-m-d'),
            'enabled' => true,
            'settings' => [
                'auto_switch_domain' => true,
                'max_retry_per_domain' => 2,
                'timeout' => 10,
                'default_limit' => 20
            ]
        ];
    }

    private function saveFullConfig($config) {
        $configJson = json_encode($config, JSON_UNESCAPED_UNICODE);

        $exists = $this->db->queryOne('SELECT id FROM sys_config WHERE config_key = ?', ['official_sites']);
        if ($exists) {
            $this->db->update('sys_config', [
                'config_value' => $configJson,
                'description' => '推荐采集配置'
            ], 'config_key = ?', ['official_sites']);
        } else {
            $this->db->insert('sys_config', [
                'config_key' => 'official_sites',
                'config_value' => $configJson,
                'description' => '推荐采集配置'
            ]);
        }
    }

    public function getApiUrl($site, $domainIndex = null) {
        if ($domainIndex === null) {
            $domainIndex = $site['active_domain_index'] ?? 0;
        }
        $domains = $site['domains'] ?? [];
        if (empty($domains)) {
            return $site['api_url'] ?? '';
        }
        $domainIndex = min(max(0, $domainIndex), count($domains) - 1);
        $domain = $domains[$domainIndex];
        $apiPath = $site['api_path'] ?? '/api.php/provide/vod/';
        return 'https://' . $domain . $apiPath;
    }

    public function setActiveDomain($siteName, $domainIndex) {
        $site = $this->getSiteByName($siteName);
        if (!$site) {
            return;
        }
        $this->db->update('official_sites', [
            'active_domain_index' => intval($domainIndex)
        ], 'name = ?', [$siteName]);
    }

    public function fetchVideos($siteName, $page = 1, $limit = 20) {
        $site = $this->getSiteByName($siteName);
        if (!$site) {
            return ['success' => false, 'message' => '推荐采集资源站不存在'];
        }
        return $this->requestWithFallback($site, 'list', [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function searchVideos($siteName, $keyword, $page = 1, $limit = 20) {
        $site = $this->getSiteByName($siteName);
        if (!$site) {
            return ['success' => false, 'message' => '推荐采集资源站不存在'];
        }
        return $this->requestWithFallback($site, 'search', [
            'keyword' => $keyword,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    private function requestWithFallback($site, $type, $params = []) {
        $domains = $site['domains'] ?? [];
        $settings = $this->getSettings();
        $maxRetry = $settings['max_retry_per_domain'] ?? 2;
        $autoSwitch = !empty($settings['auto_switch_domain']);

        if (empty($domains)) {
            return $this->doRequest($site['api_url'], $type, $params);
        }

        $startIndex = $site['active_domain_index'] ?? 0;
        $domainCount = count($domains);

        for ($i = 0; $i < $domainCount; $i++) {
            $domainIndex = ($startIndex + $i) % $domainCount;
            $apiUrl = $this->getApiUrl($site, $domainIndex);
            $domain = $domains[$domainIndex];

            for ($retry = 0; $retry <= $maxRetry; $retry++) {
                $result = $this->doRequest($apiUrl, $type, $params);
                if ($result['success']) {
                    $result['domain_used'] = $domain;
                    if ($domainIndex !== $startIndex && $autoSwitch) {
                        $this->setActiveDomain($site['name'], $domainIndex);
                    }
                    return $result;
                }

                if (!$autoSwitch) {
                    break;
                }
            }

            if (!$autoSwitch) {
                break;
            }
        }

        return ['success' => false, 'message' => '所有域名均请求失败'];
    }

    private function doRequest($apiUrl, $type, $params = []) {
        $siteMgr = new ResourceSiteManager();

        if ($type === 'list') {
            return $siteMgr->fetchVideos(
                $apiUrl,
                $params['page'] ?? 1,
                $params['limit'] ?? 20
            );
        } elseif ($type === 'search') {
            return $siteMgr->searchVideos(
                $apiUrl,
                $params['keyword'] ?? '',
                $params['page'] ?? 1,
                $params['limit'] ?? 20
            );
        }

        return ['success' => false, 'message' => '未知请求类型'];
    }

    private function getActiveDomain($site) {
        $domains = $site['domains'] ?? [];
        if (empty($domains)) {
            return $site['site_url'] ?? '';
        }
        $index = $site['active_domain_index'] ?? 0;
        $index = min(max(0, $index), count($domains) - 1);
        return 'https://' . $domains[$index];
    }

    public function searchAllSites($keyword, $maxSites = 5, $limitPerSite = 10) {
        $sites = $this->getAllSites(false);
        $sites = array_slice($sites, 0, $maxSites);

        $results = [];
        $totalVideos = 0;

        foreach ($sites as $site) {
            $searchResult = $this->searchVideos($site['name'], $keyword, 1, $limitPerSite);
            if ($searchResult['success']) {
                $videos = $searchResult['videos'] ?? [];
                foreach ($videos as &$video) {
                    $video['site_name'] = $site['name'];
                    $video['site_url'] = $this->getActiveDomain($site);
                    $video['is_official'] = true;
                }
                unset($video);
                $results[] = [
                    'site' => $site['name'],
                    'site_url' => $this->getActiveDomain($site),
                    'is_official' => true,
                    'count' => count($videos),
                    'videos' => $videos,
                    'domain_used' => $searchResult['domain_used'] ?? ''
                ];
                $totalVideos += count($videos);
            } else {
                $results[] = [
                    'site' => $site['name'],
                    'is_official' => true,
                    'count' => 0,
                    'videos' => [],
                    'error' => $searchResult['message']
                ];
            }
        }

        return [
            'success' => true,
            'keyword' => $keyword,
            'sites_searched' => count($sites),
            'total_videos' => $totalVideos,
            'results' => $results
        ];
    }
}
