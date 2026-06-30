<?php
/**
 * 官采资源站管理器
 * 支持多域名采集、自动切换
 */

require_once __DIR__ . '/ResourceSiteManager.php';

class OfficialSiteManager {
    private $config;
    private $configFile;

    public function __construct() {
        $this->configFile = __DIR__ . '/official_sites_config.php';
        $this->loadConfig();
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            $this->config = [
                'version' => '1.0',
                'update_date' => date('Y-m-d'),
                'enabled' => true,
                'sites' => [],
                'settings' => [
                    'auto_switch_domain' => true,
                    'max_retry_per_domain' => 2,
                    'timeout' => 10,
                    'default_limit' => 20
                ]
            ];
        }
    }

    public function getAllSites($includePaused = false) {
        $sites = $this->config['sites'] ?? [];
        if (!$includePaused) {
            $sites = array_filter($sites, function($s) {
                return ($s['status'] ?? '') !== 'paused';
            });
        }
        usort($sites, function($a, $b) {
            return ($a['priority'] ?? 99) <=> ($b['priority'] ?? 99);
        });
        return array_values($sites);
    }

    public function getSiteByName($name) {
        foreach ($this->config['sites'] as $site) {
            if ($site['name'] === $name) {
                return $site;
            }
        }
        return null;
    }

    public function getSettings() {
        return $this->config['settings'] ?? [];
    }

    public function isEnabled() {
        return !empty($this->config['enabled']);
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

    public function fetchVideos($siteName, $page = 1, $limit = 20) {
        $site = $this->getSiteByName($siteName);
        if (!$site) {
            return ['success' => false, 'message' => '官采资源站不存在'];
        }
        return $this->requestWithFallback($site, 'list', [
            'page' => $page,
            'limit' => $limit
        ]);
    }

    public function searchVideos($siteName, $keyword, $page = 1, $limit = 20) {
        $site = $this->getSiteByName($siteName);
        if (!$site) {
            return ['success' => false, 'message' => '官采资源站不存在'];
        }
        return $this->requestWithFallback($site, 'search', [
            'keyword' => $keyword,
            'page' => $page,
            'limit' => $limit
        ]);
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

    public function setActiveDomain($siteName, $domainIndex) {
        foreach ($this->config['sites'] as &$site) {
            if ($site['name'] === $siteName) {
                $site['active_domain_index'] = intval($domainIndex);
                break;
            }
        }
        unset($site);
        $this->saveConfig();
    }

    public function addSite($siteData) {
        $site = [
            'name' => $siteData['name'] ?? '',
            'code' => $siteData['code'] ?? '',
            'site_url' => $siteData['site_url'] ?? '',
            'api_url' => $siteData['api_url'] ?? '',
            'type' => $siteData['type'] ?? 'maccms',
            'status' => $siteData['status'] ?? 'active',
            'note' => $siteData['note'] ?? '',
            'priority' => intval($siteData['priority'] ?? 99),
            'is_official' => true,
            'domains' => $siteData['domains'] ?? [],
            'active_domain_index' => 0,
            'api_path' => $siteData['api_path'] ?? '/api.php/provide/vod/'
        ];

        if (empty($site['name'])) {
            return ['success' => false, 'message' => '资源站名称不能为空'];
        }

        foreach ($this->config['sites'] as $existing) {
            if ($existing['name'] === $site['name']) {
                return ['success' => false, 'message' => '资源站名称已存在'];
            }
        }

        $this->config['sites'][] = $site;
        $this->config['update_date'] = date('Y-m-d');
        $this->saveConfig();

        return ['success' => true, 'message' => '添加成功'];
    }

    public function updateSite($siteName, $siteData) {
        foreach ($this->config['sites'] as &$site) {
            if ($site['name'] === $siteName) {
                foreach ($siteData as $key => $value) {
                    if ($key === 'domains') {
                        $site['domains'] = is_array($value) ? $value : array_filter(array_map('trim', explode("\n", $value)));
                    } elseif ($key === 'priority') {
                        $site[$key] = intval($value);
                    } else {
                        $site[$key] = $value;
                    }
                }
                $this->config['update_date'] = date('Y-m-d');
                $this->saveConfig();
                return ['success' => true, 'message' => '更新成功'];
            }
        }
        return ['success' => false, 'message' => '资源站不存在'];
    }

    public function deleteSite($siteName) {
        $this->config['sites'] = array_values(array_filter(
            $this->config['sites'],
            function($s) use ($siteName) {
                return $s['name'] !== $siteName;
            }
        ));
        $this->config['update_date'] = date('Y-m-d');
        $this->saveConfig();
        return ['success' => true, 'message' => '删除成功'];
    }

    public function updateSettings($settings) {
        $this->config['settings'] = array_merge($this->config['settings'], $settings);
        $this->config['update_date'] = date('Y-m-d');
        $this->saveConfig();
        return ['success' => true, 'message' => '设置已更新'];
    }

    public function setEnabled($enabled) {
        $this->config['enabled'] = (bool)$enabled;
        $this->config['update_date'] = date('Y-m-d');
        $this->saveConfig();
        return ['success' => true, 'message' => '设置已更新'];
    }

    private function saveConfig() {
        $content = "<?php\n/**\n * 官采资源站配置列表\n * 官方采集专区 - 支持多域名采集\n */\n\nreturn ";
        $content .= var_export($this->config, true);
        $content .= ";\n";
        file_put_contents($this->configFile, $content);
    }
}
