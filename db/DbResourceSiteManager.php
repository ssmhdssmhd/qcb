<?php
/**
 * 数据库版资源站管理器
 * 负责资源站列表管理、采集接口调用、自动学习调度
 * 使用数据库存储配置，完全兼容原 ResourceSiteManager 接口
 */

require_once __DIR__ . '/Database.php';

class DbResourceSiteManager {
    private $db;
    private $lastHttpError = '';

    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureTables();
    }

    private function ensureTables() {
        if (!$this->db->tableExists('resource_sites')) {
            $this->db->initTables();
        }
        if (!$this->db->tableExists('sys_config')) {
            $this->db->initTables();
        }
    }

    private function parseSiteRow($row) {
        if (!$row) return null;
        $site = $row;
        if (!empty($site['config'])) {
            $config = json_decode($site['config'], true);
            if (is_array($config)) {
                $site = array_merge($config, $site);
            }
        }
        unset($site['config']);
        return $site;
    }

    private function prepareSiteData($siteData) {
        $coreFields = ['name', 'site_url', 'api_url', 'type', 'status', 'priority', 'note', 'last_check_time', 'last_check_status', 'response_time'];
        $coreData = [];
        $extraConfig = [];

        foreach ($siteData as $key => $value) {
            if (in_array($key, $coreFields)) {
                $coreData[$key] = $value;
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
        $sql = 'SELECT * FROM resource_sites';
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
        $row = $this->db->queryOne('SELECT * FROM resource_sites WHERE name = ?', [$name]);
        return $this->parseSiteRow($row);
    }

    public function getSiteById($id) {
        $row = $this->db->queryOne('SELECT * FROM resource_sites WHERE id = ?', [$id]);
        return $this->parseSiteRow($row);
    }

    public function getSitesByDomain($domain) {
        $allSites = $this->getAllSites(true);
        $result = [];
        foreach ($allSites as $site) {
            $siteDomain = parse_url($site['site_url'] ?? '', PHP_URL_HOST);
            $apiDomain = parse_url($site['api_url'] ?? '', PHP_URL_HOST);
            if (stripos($domain, $siteDomain) !== false || stripos($domain, $apiDomain) !== false) {
                $result[] = $site;
            }
        }
        return $result;
    }

    public function addSite($siteData) {
        $site = array_merge([
            'name' => '',
            'site_url' => '',
            'api_url' => '',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 50
        ], $siteData);

        if (empty($site['name']) || empty($site['api_url'])) {
            return ['success' => false, 'message' => '名称和采集接口不能为空'];
        }

        $exists = $this->getSiteByName($site['name']);
        if ($exists) {
            return ['success' => false, 'message' => '资源站名称已存在'];
        }

        $data = $this->prepareSiteData($site);
        $this->db->insert('resource_sites', $data);

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

        $this->db->update('resource_sites', $data, 'name = ?', [$name]);
        return ['success' => true, 'message' => '更新成功'];
    }

    public function updateSiteById($id, $siteData) {
        $exists = $this->getSiteById($id);
        if (!$exists) {
            return ['success' => false, 'message' => '资源站不存在'];
        }

        if (isset($siteData['id'])) {
            unset($siteData['id']);
        }

        $data = $this->prepareSiteData($siteData);
        if (empty($data)) {
            return ['success' => true, 'message' => '更新成功'];
        }

        $this->db->update('resource_sites', $data, 'id = ?', [$id]);
        return ['success' => true, 'message' => '更新成功'];
    }

    public function deleteSite($name) {
        $exists = $this->getSiteByName($name);
        if (!$exists) {
            return ['success' => false, 'message' => '资源站不存在'];
        }

        $this->db->delete('resource_sites', 'name = ?', [$name]);
        return ['success' => true, 'message' => '删除成功'];
    }

    public function deleteSiteById($id) {
        $exists = $this->getSiteById($id);
        if (!$exists) {
            return ['success' => false, 'message' => '资源站不存在'];
        }

        $this->db->delete('resource_sites', 'id = ?', [$id]);
        return ['success' => true, 'message' => '删除成功'];
    }

    public function updateSiteStatus($siteName, $status, $note = '') {
        $exists = $this->getSiteByName($siteName);
        if (!$exists) {
            return false;
        }

        $data = ['status' => $status];
        if ($note) {
            $data['note'] = $note;
        }

        $this->db->update('resource_sites', $data, 'name = ?', [$siteName]);
        return true;
    }

    public function updateSiteStatusById($id, $status, $note = '') {
        $exists = $this->getSiteById($id);
        if (!$exists) {
            return false;
        }

        $data = ['status' => $status];
        if ($note) {
            $data['note'] = $note;
        }

        $this->db->update('resource_sites', $data, 'id = ?', [$id]);
        return true;
    }

    public function checkSiteHealth($site, $timeout = 8) {
        $apiUrl = $site['api_url'] ?? '';
        if (empty($apiUrl)) {
            return ['healthy' => false, 'message' => '无API地址', 'response_time' => 0];
        }

        $startTime = microtime(true);
        $result = $this->fetchVideos($apiUrl, 1, 1);
        $responseTime = round((microtime(true) - $startTime) * 1000, 0);

        if ($result['success'] && !empty($result['videos'])) {
            return [
                'healthy' => true,
                'message' => '正常',
                'video_count' => count($result['videos']),
                'response_time' => $responseTime
            ];
        }

        return [
            'healthy' => false,
            'message' => $result['message'] ?? '未知错误',
            'video_count' => 0,
            'response_time' => $responseTime
        ];
    }

    public function batchCheckHealth($maxSites = null, $timeoutPerSite = 8) {
        $sites = $this->getAllSites(true);
        $results = [];
        $activeCount = 0;
        $failedCount = 0;

        foreach ($sites as $idx => $site) {
            if ($maxSites !== null && $idx >= $maxSites) break;

            $health = $this->checkSiteHealth($site, $timeoutPerSite);
            $results[] = [
                'name' => $site['name'],
                'api_url' => $site['api_url'],
                'status' => $site['status'] ?? 'active',
                'priority' => $site['priority'] ?? 99,
                'healthy' => $health['healthy'],
                'message' => $health['message'],
                'response_time' => $health['response_time']
            ];

            if ($health['healthy']) {
                $activeCount++;
            } else {
                $failedCount++;
            }
        }

        return [
            'total' => count($results),
            'healthy' => $activeCount,
            'failed' => $failedCount,
            'results' => $results
        ];
    }

    private function isDomainFailureError($error) {
        if (!$error) return false;
        $error = strtolower($error);
        $patterns = [
            'could not resolve',
            'dns',
            'name lookup',
            'no such host',
            'host not found',
            'ssl_error_syscall',
            'connection refused',
            'connection timed out',
            'failed to connect',
            'operation timed out',
            'timed out after',
        ];
        foreach ($patterns as $pattern) {
            if (strpos($error, strtolower($pattern)) !== false) {
                return true;
            }
        }
        return false;
    }

    public function fetchVideos($apiUrl, $page = 1, $limit = 20) {
        $urlsToTry = $this->generateApiUrlVariants($apiUrl);
        $fetchStrategies = [
            ['ac' => 'detail'],
            ['ac' => 'videolist'],
        ];

        $lastError = '';
        $isDomainFailure = false;
        $dnsHost = '';
        foreach ($urlsToTry as $tryUrl) {
            foreach ($fetchStrategies as $strategy) {
                $params = array_merge($strategy, [
                    'pg' => intval($page),
                    'limit' => intval($limit)
                ]);
                $url = $this->buildApiUrl($tryUrl, $params);

                $response = $this->httpGet($url);
                if ($response === false) {
                    $lastError = $this->lastHttpError ?? '未知错误';
                    if ($this->isDomainFailureError($lastError)) {
                        $isDomainFailure = true;
                        $parsed = parse_url($tryUrl);
                        $dnsHost = $parsed['host'] ?? '';
                    }
                    continue;
                }

                $data = json_decode($response, true);
                if (!$data) {
                    $lastError = '解析JSON失败';
                    continue;
                }

                $result = $this->parseVideoList($data);
                if ($result['success']) {
                    $result['page'] = $page;
                    return $result;
                }
                $lastError = $result['message'] ?? '无视频数据';
            }
        }

        if ($isDomainFailure) {
            $hostDisplay = $dnsHost ? '（域名: ' . $dnsHost . '）' : '';
            return [
                'success' => false,
                'message' => '资源站API无法连接' . $hostDisplay . '，该资源站可能已失效，请更换其他资源站',
                'error_type' => 'dns_failure',
                'dns_host' => $dnsHost
            ];
        }

        return ['success' => false, 'message' => '获取失败: ' . $lastError];
    }

    public function searchVideos($apiUrl, $keyword, $page = 1, $limit = 20) {
        $urlsToTry = $this->generateApiUrlVariants($apiUrl);
        $searchStrategies = [
            ['ac' => 'detail', 'wd' => $keyword],
            ['ac' => 'videolist', 'wd' => $keyword],
        ];

        $lastError = '';
        $isDomainFailure = false;
        $dnsHost = '';
        foreach ($urlsToTry as $tryUrl) {
            foreach ($searchStrategies as $strategy) {
                $params = array_merge($strategy, [
                    'pg' => intval($page),
                    'limit' => intval($limit)
                ]);
                $url = $this->buildApiUrl($tryUrl, $params);

                $response = $this->httpGet($url);
                if ($response === false) {
                    $lastError = $this->lastHttpError ?? '未知错误';
                    if ($this->isDomainFailureError($lastError)) {
                        $isDomainFailure = true;
                        $parsed = parse_url($tryUrl);
                        $dnsHost = $parsed['host'] ?? '';
                    }
                    continue;
                }

                $data = json_decode($response, true);
                if (!$data) {
                    $lastError = '解析JSON失败';
                    continue;
                }

                $code = $data['code'] ?? $data['status'] ?? 0;
                if ($code != 200 && $code != 1 && !empty($data['msg']) && empty($data['list']) && empty($data['data'])) {
                    $lastError = $data['msg'] ?? '接口返回错误';
                    continue;
                }

                $result = $this->parseVideoList($data);
                if ($result['success']) {
                    $result['page'] = $page;
                    return $result;
                }
                $lastError = $result['message'] ?? '搜索无结果';
            }
        }

        if ($isDomainFailure) {
            $hostDisplay = $dnsHost ? '（域名: ' . $dnsHost . '）' : '';
            return [
                'success' => false,
                'message' => '资源站API无法连接' . $hostDisplay . '，该资源站可能已失效，请更换其他资源站',
                'error_type' => 'dns_failure',
                'dns_host' => $dnsHost
            ];
        }

        return ['success' => false, 'message' => '搜索失败: ' . $lastError];
    }

    public function generateApiUrlVariants($apiUrl) {
        $urls = [$apiUrl];
        $parsed = parse_url($apiUrl);
        if (!$parsed) return $urls;

        $path = $parsed['path'] ?? '';

        if (preg_match('#^(.*)/from/[^/]+/?$#', $path, $m)) {
            $newPath = $m[1];
            $newUrl = $this->buildUrl($parsed, $newPath);
            if ($newUrl !== $apiUrl) {
                $urls[] = $newUrl;
            }
        }

        $existingParams = [];
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $existingParams);
            if (isset($existingParams['ac'])) {
                unset($existingParams['ac']);
                $newUrl = $this->buildUrl($parsed, $path, $existingParams);
                if ($newUrl !== $apiUrl && !in_array($newUrl, $urls)) {
                    $urls[] = $newUrl;
                }
            }
        }

        $host = $parsed['host'] ?? '';
        if (strpos($host, 'www.') === 0) {
            $newHost = substr($host, 4);
            $newParsed = $parsed;
            $newParsed['host'] = $newHost;
            $newUrl = $this->buildUrl($newParsed, $path, $existingParams);
            if (!in_array($newUrl, $urls)) {
                $urls[] = $newUrl;
            }
        }

        return array_unique($urls);
    }

    private function buildUrl($parsed, $path = null, $params = null) {
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $path ?? ($parsed['path'] ?? '/');

        $url = $scheme . '://' . $host . $port . $path;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        } elseif (!empty($parsed['query']) && $params === null) {
            $url .= '?' . $parsed['query'];
        }

        return $url;
    }

    public function parseVideoList($data) {
        $videos = [];
        $list = $data['list'] ?? $data['data'] ?? [];

        if (empty($list)) {
            return ['success' => false, 'message' => '无视频数据'];
        }

        foreach ($list as $item) {
            $vodPlayUrl = $item['vod_play_url'] ?? $item['play_url'] ?? '';
            $vodPlayFrom = $item['vod_play_from'] ?? $item['play_from'] ?? '';
            $vodName = $item['vod_name'] ?? $item['name'] ?? '';
            $vodId = $item['vod_id'] ?? $item['id'] ?? 0;
            $vodPic = $item['vod_pic'] ?? $item['pic'] ?? '';
            $vodRemarks = $item['vod_remarks'] ?? $item['remarks'] ?? '';

            if (empty($vodPlayUrl)) continue;

            $allUrls = $this->extractAllPlayUrls($vodPlayUrl, $vodPlayFrom);
            $m3u8Urls = array_filter($allUrls, function($u) {
                return stripos($u['url'] ?? '', '.m3u8') !== false;
            });
            $m3u8Urls = array_values($m3u8Urls);

            if (!empty($m3u8Urls)) {
                $videos[] = [
                    'id' => $vodId,
                    'name' => $vodName,
                    'pic' => $vodPic,
                    'remarks' => $vodRemarks,
                    'urls' => $m3u8Urls,
                    'first_url' => $m3u8Urls[0]['url'] ?? '',
                    'raw_play_url' => $vodPlayUrl,
                    'play_from' => $vodPlayFrom,
                    'has_non_m3u8' => count($allUrls) > count($m3u8Urls),
                    'all_urls_count' => count($allUrls)
                ];
            } elseif (!empty($allUrls)) {
                $videos[] = [
                    'id' => $vodId,
                    'name' => $vodName,
                    'pic' => $vodPic,
                    'remarks' => $vodRemarks,
                    'urls' => $allUrls,
                    'first_url' => $allUrls[0]['url'] ?? '',
                    'raw_play_url' => $vodPlayUrl,
                    'play_from' => $vodPlayFrom,
                    'is_non_m3u8' => true,
                    'all_urls_count' => count($allUrls)
                ];
            }
        }

        if (empty($videos)) {
            return ['success' => false, 'message' => '无有效视频'];
        }

        return [
            'success' => true,
            'total' => $data['total'] ?? $data['page']['pagecount'] ?? count($videos),
            'pagecount' => $data['pagecount'] ?? $data['page']['pagecount'] ?? 1,
            'videos' => $videos
        ];
    }

    private function extractAllPlayUrls($playUrl, $playFrom = '') {
        if (empty($playUrl)) return [];

        $fromGroups = [];
        if (!empty($playFrom) && strpos($playFrom, '$$$') !== false && strpos($playUrl, '$$$') !== false) {
            $fromParts = explode('$$$', $playFrom);
            $urlParts = explode('$$$', $playUrl);
            $count = min(count($fromParts), count($urlParts));
            for ($i = 0; $i < $count; $i++) {
                $fromGroups[] = [
                    'from' => trim($fromParts[$i]),
                    'url' => trim($urlParts[$i])
                ];
            }
        } else {
            $fromGroups[] = [
                'from' => trim($playFrom) ?: 'default',
                'url' => $playUrl
            ];
        }

        $allUrls = [];
        $seen = [];

        foreach ($fromGroups as $group) {
            $fromName = $group['from'];
            $groupUrlStr = $group['url'];
            $groupUrls = $this->parsePlayUrlGroup($groupUrlStr, $fromName);

            foreach ($groupUrls as $u) {
                $cleanUrl = preg_replace('/#.*$/', '', $u['url']);
                $key = $fromName . '|' . $cleanUrl;
                if (!isset($seen[$key])) {
                    $seen[$key] = true;
                    $u['play_from'] = $fromName;
                    $allUrls[] = $u;
                }
            }
        }

        return $allUrls;
    }

    private function parsePlayUrlGroup($playUrl, $fromName = '') {
        $urls = [];
        if (empty($playUrl)) return $urls;

        $lines = [];
        if (strpos($playUrl, "\r\n") !== false || strpos($playUrl, "\n") !== false) {
            $lines = preg_split('/\r\n|\n/', $playUrl);
        } else {
            $lines = [$playUrl];
        }

        $lineNum = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $lineNum++;

            if (strpos($line, '$') !== false) {
                $parts = explode('$', $line);
                $urlIndices = [];
                foreach ($parts as $i => $part) {
                    $part = trim($part);
                    if (preg_match('/^https?:\/\//i', $part)) {
                        $urlIndices[] = $i;
                    }
                }
                foreach ($urlIndices as $idx) {
                    $url = $parts[$idx];
                    $name = '';

                    if ($idx > 0) {
                        $prev = trim($parts[$idx - 1] ?? '');
                        if (!preg_match('/^https?:\/\//i', $prev) && !empty($prev)) {
                            if (preg_match('/^\d+$/', $prev)) {
                                $name = '第' . $prev . '集';
                            } else {
                                $name = $prev;
                            }
                        }
                    }

                    if (empty($name)) {
                        $epFromUrl = $this->extractEpisodeFromUrl($url);
                        if ($epFromUrl) {
                            if (preg_match('/^\d+$/', $epFromUrl)) {
                                $name = '第' . $epFromUrl . '集';
                            } else {
                                $name = $epFromUrl;
                            }
                        }
                    }

                    if (empty($name)) {
                        $frag = parse_url($url, PHP_URL_FRAGMENT);
                        if ($frag && preg_match('/^\d+$/', $frag)) {
                            $name = '第' . $frag . '集';
                        }
                    }

                    if (empty($name)) {
                        $num = count($urls) + 1;
                        $name = '第' . $num . '集';
                    }

                    $urls[] = ['name' => $name, 'url' => $url];
                }
            } else {
                if (preg_match('/^https?:\/\//i', $line)) {
                    $name = $fromName ? ($fromName . ' 第' . $lineNum . '集') : ('第' . $lineNum . '集');
                    $urls[] = ['name' => $name, 'url' => $line];
                }
            }
        }

        return $urls;
    }

    public function searchAllSites($keyword, $maxSites = 5, $limitPerSite = 10) {
        $sites = $this->getAllSites(false);
        $sites = array_slice($sites, 0, $maxSites);

        $results = [];
        $totalVideos = 0;

        foreach ($sites as $site) {
            $searchResult = $this->searchVideos($site['api_url'], $keyword, 1, $limitPerSite);
            if ($searchResult['success']) {
                foreach ($searchResult['videos'] as &$video) {
                    $video['site_name'] = $site['name'];
                    $video['site_url'] = $site['site_url'] ?? '';
                }
                unset($video);
                $results[] = [
                    'site' => $site['name'],
                    'site_url' => $site['site_url'] ?? '',
                    'count' => count($searchResult['videos']),
                    'videos' => $searchResult['videos']
                ];
                $totalVideos += count($searchResult['videos']);
            } else {
                $results[] = [
                    'site' => $site['name'],
                    'site_url' => $site['site_url'] ?? '',
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

    private function extractEpisodeFromUrl($url) {
        if (preg_match('/#(.+)$/i', $url, $m)) {
            $frag = trim($m[1]);
            if (!empty($frag) && preg_match('/第[^\s]+/u', $frag)) {
                return $frag;
            }
            if (!empty($frag) && mb_strlen($frag) <= 20) {
                return $frag;
            }
        }
        return null;
    }

    public function getAutoLearnConfig() {
        $row = $this->db->queryOne('SELECT config_value FROM sys_config WHERE config_key = ?', ['auto_learn']);
        if ($row && !empty($row['config_value'])) {
            $config = json_decode($row['config_value'], true);
            if (is_array($config)) {
                return $config;
            }
        }
        return [
            'enabled' => true,
            'interval_days' => 3,
            'videos_per_site' => 5,
            'max_sites_per_run' => 5,
            'min_segments' => 50,
            'max_ad_percentage' => 90
        ];
    }

    public function setAutoLearnConfig($config) {
        $default = $this->getAutoLearnConfig();
        $mergedConfig = array_merge($default, $config);
        $configJson = json_encode($mergedConfig, JSON_UNESCAPED_UNICODE);

        $exists = $this->db->queryOne('SELECT id FROM sys_config WHERE config_key = ?', ['auto_learn']);
        if ($exists) {
            $this->db->update('sys_config', ['config_value' => $configJson], 'config_key = ?', ['auto_learn']);
        } else {
            $this->db->insert('sys_config', [
                'config_key' => 'auto_learn',
                'config_value' => $configJson,
                'description' => '自动学习配置'
            ]);
        }

        return ['success' => true, 'message' => '配置已更新'];
    }

    public function saveAutoLearnConfig($config) {
        return $this->setAutoLearnConfig($config);
    }

    public function runAutoLearn($domainRuleManager, $options = []) {
        try {
            $config = $this->getAutoLearnConfig();
            if (empty($config['enabled'])) {
                return ['success' => false, 'message' => '自动学习未启用'];
            }

            $maxSites = $options['max_sites'] ?? $config['max_sites_per_run'] ?? 5;
            $videosPerSite = $options['videos_per_site'] ?? $config['videos_per_site'] ?? 5;
            $minSegments = $config['min_segments'] ?? 50;
            $maxAdPercentage = $config['max_ad_percentage'] ?? 90;
            $keyword = $options['keyword'] ?? '';

            $sites = $this->getAllSites(false);
            $sites = array_slice($sites, 0, $maxSites);

            $results = [];
            $totalLearned = 0;
            $totalFailed = 0;

            foreach ($sites as $site) {
                $siteResult = [
                    'site' => $site['name'],
                    'videos_checked' => 0,
                    'videos_learned' => 0,
                    'videos_failed' => 0,
                    'domains' => []
                ];

                try {
                    if (!empty($keyword)) {
                        $fetchResult = $this->searchVideos($site['api_url'], $keyword, 1, $videosPerSite * 3);
                    } else {
                        $fetchResult = $this->fetchVideos($site['api_url'], 1, $videosPerSite * 3);
                    }

                    if (!$fetchResult['success']) {
                        $siteResult['error'] = $fetchResult['message'];
                        $results[] = $siteResult;
                        continue;
                    }

                    $videos = $fetchResult['videos'] ?? [];
                    $learnedCount = 0;

                    foreach ($videos as $video) {
                        if ($learnedCount >= $videosPerSite) break;

                        $siteResult['videos_checked']++;

                        $videoUrl = $video['url'] ?? $video['first_url'] ?? '';
                        if (empty($videoUrl)) continue;

                        $learnResult = $this->learnFromVideoUrl($videoUrl, $domainRuleManager, [
                            'min_segments' => $minSegments,
                            'max_ad_percentage' => $maxAdPercentage
                        ]);

                        if ($learnResult['success']) {
                            $videoDomain = $learnResult['domain'] ?? '';
                            if ($videoDomain) {
                                if (!isset($siteResult['domains'][$videoDomain])) {
                                    $siteResult['domains'][$videoDomain] = 0;
                                }
                                $siteResult['domains'][$videoDomain]++;
                            }
                            $siteResult['videos_learned']++;
                            $totalLearned++;
                            $learnedCount++;
                        } else {
                            $siteResult['videos_failed']++;
                            $totalFailed++;
                        }

                        unset($learnResult);
                        if (function_exists('gc_collect_cycles')) {
                            gc_collect_cycles();
                        }
                    }

                    unset($videos);
                    unset($fetchResult);
                    if (function_exists('gc_collect_cycles')) {
                        gc_collect_cycles();
                    }
                } catch (Throwable $e) {
                    $siteResult['error'] = $e->getMessage();
                    $siteResult['videos_failed']++;
                    $totalFailed++;
                }

                $results[] = $siteResult;
            }

            $this->setLastLearnTime();

            return [
                'success' => true,
                'message' => '自动学习完成',
                'keyword' => $keyword,
                'sites_processed' => count($sites),
                'total_learned' => $totalLearned,
                'total_failed' => $totalFailed,
                'details' => $results
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => '自动学习异常: ' . $e->getMessage(),
                'error_file' => basename($e->getFile()),
                'error_line' => $e->getLine()
            ];
        }
    }

    public function learnFromVideoUrl($videoUrl, $domainRuleManager, $options = []) {
        $minSegments = $options['min_segments'] ?? 50;
        $maxAdPercentage = $options['max_ad_percentage'] ?? 90;

        try {
            $parsedUrl = parse_url($videoUrl);
            $videoDomain = $parsedUrl['host'] ?? '';
            if (empty($videoDomain)) {
                return ['success' => false, 'message' => '无法解析域名'];
            }

            if (function_exists('memory_get_usage')) {
                $currentLimit = @ini_get('memory_limit');
                $currentLimitBytes = $this->return_bytes($currentLimit);
                if ($currentLimitBytes < 256 * 1024 * 1024) {
                    @ini_set('memory_limit', '256M');
                }
            }

            $mediaUrl = $this->resolveMasterPlaylist($videoUrl);

            if (!class_exists('M3U8Parser')) {
                require_once __DIR__ . '/../src/M3U8Parser.php';
            }
            $parser = new M3U8Parser();
            $parser->setMaxSegments(3000);
            $playlist = $parser->parse($mediaUrl);
            unset($parser);

            if (empty($playlist['segments']) || count($playlist['segments']) < $minSegments) {
                unset($playlist);
                return ['success' => false, 'message' => '片段数不足', 'domain' => $videoDomain];
            }

            if (!class_exists('EnhancedAdRuleEngine')) {
                require_once __DIR__ . '/../gz/EnhancedAdRuleEngine.php';
            }
            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $engine->setDomain($videoDomain);
            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            unset($engine);

            $segmentsCount = count($playlist['segments']);
            unset($playlist);

            $adPercentage = $analysis['totalCount'] > 0
                ? ($analysis['adCount'] / $analysis['totalCount'] * 100)
                : 0;

            if ($adPercentage >= $maxAdPercentage) {
                unset($analysis);
                return ['success' => false, 'message' => '广告占比过高', 'domain' => $videoDomain, 'ad_percentage' => $adPercentage];
            }

            $domainResult = $domainRuleManager->learnFromAnalysis($videoDomain, $analysis);

            // ===== 保存广告特征码到数据库 =====
            $adSignature = new DbAdSignature($this->db);
            $signatures = [];
            if (!empty($analysis['durationDistribution'])) {
                foreach ($analysis['durationDistribution'] as $dur => $count) {
                    if ((float)$dur < 3.0 && $count > 1) {
                        $signatures[] = ['type' => 'duration', 'value' => (string)$dur, 'weight' => min(50, $count * 5), 'confidence' => min(80, $count * 10)];
                    }
                }
            }
            if (!empty($analysis['adClusters'])) {
                foreach ($analysis['adClusters'] as $cluster) {
                    if (!empty($cluster['avgDuration']) && $cluster['avgDuration'] < 3.0) {
                        $signatures[] = ['type' => 'duration', 'value' => (string)round($cluster['avgDuration'], 2), 'weight' => 40, 'confidence' => 60];
                    }
                }
            }
            if (!empty($analysis['sequenceJumps'])) {
                foreach ($analysis['sequenceJumps'] as $jump) {
                    if (!empty($jump['jump']) && $jump['jump'] > 1) {
                        $signatures[] = ['type' => 'sequence', 'value' => (string)$jump['jump'], 'weight' => 35, 'confidence' => 50];
                    }
                }
            }
            if ($analysis['discontinuityCount'] > 0) {
                $signatures[] = ['type' => 'discontinuity', 'value' => 'true', 'weight' => 30, 'confidence' => 50];
            }
            $adSignature->addSignatures($videoDomain, $signatures);

            // ===== 记录域名分析统计 =====
            $domainStats = new DbDomainAnalysisStats($this->db);
            $domainStats->recordLearn($videoDomain);
            $domainStats->recordAnalyze($videoDomain, $analysis['totalCount'] ?? 0, $analysis['adCount'] ?? 0, $adPercentage);

            unset($analysis);

            if ($domainResult) {
                return [
                    'success' => true,
                    'domain' => $videoDomain,
                    'segments_count' => $segmentsCount,
                    'ad_count' => 0,
                    'ad_percentage' => $adPercentage,
                    'rule_updated' => $domainResult
                ];
            } else {
                return ['success' => false, 'message' => '规则学习失败', 'domain' => $videoDomain];
            }
        } catch (Throwable $e) {
            if (isset($playlist)) unset($playlist);
            if (isset($engine)) unset($engine);
            if (isset($analysis)) unset($analysis);
            $msg = $e->getMessage();
            if (strpos($msg, 'memory') !== false || strpos($msg, 'Allowed memory') !== false) {
                return ['success' => false, 'message' => '内存不足，视频过大', 'domain' => $videoDomain ?? ''];
            }
            return ['success' => false, 'message' => $msg];
        }
    }

    private function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    private function resolveMasterPlaylist($url) {
        if (!class_exists('M3U8Parser')) {
            require_once __DIR__ . '/../src/M3U8Parser.php';
        }
        $parser = new M3U8Parser();
        try {
            $playlist = $parser->parse($url);
            if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
                $firstVariant = $playlist['variants'][0]['uri'] ?? '';
                if ($firstVariant) {
                    $parsedUrl = parse_url($url);
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (isset($parsedUrl['port'])) {
                        $baseUrl .= ':' . $parsedUrl['port'];
                    }
                    $pathDir = dirname($parsedUrl['path'] ?? '');
                    $pathDir = $pathDir === '.' ? '' : $pathDir;
                    if (strpos($firstVariant, '/') === 0) {
                        return $baseUrl . $firstVariant;
                    } else {
                        return $baseUrl . $pathDir . '/' . $firstVariant;
                    }
                }
            }
        } catch (Throwable $e) {
        }
        return $url;
    }

    public function getLastLearnTime() {
        $row = $this->db->queryOne('SELECT config_value FROM sys_config WHERE config_key = ?', ['auto_learn_state']);
        if ($row && !empty($row['config_value'])) {
            $state = json_decode($row['config_value'], true);
            return $state['last_learn_time'] ?? null;
        }
        return null;
    }

    public function setLastLearnTime() {
        $state = [
            'last_learn_time' => date('Y-m-d H:i:s'),
            'last_learn_timestamp' => time()
        ];
        $stateJson = json_encode($state, JSON_UNESCAPED_UNICODE);

        $exists = $this->db->queryOne('SELECT id FROM sys_config WHERE config_key = ?', ['auto_learn_state']);
        if ($exists) {
            $this->db->update('sys_config', ['config_value' => $stateJson], 'config_key = ?', ['auto_learn_state']);
        } else {
            $this->db->insert('sys_config', [
                'config_key' => 'auto_learn_state',
                'config_value' => $stateJson,
                'description' => '自动学习状态'
            ]);
        }
    }

    public function shouldAutoLearn() {
        $config = $this->getAutoLearnConfig();
        if (empty($config['enabled'])) return false;

        $lastTime = $this->getLastLearnTime();
        if (!$lastTime) return true;

        $intervalDays = $config['interval_days'] ?? 3;
        $lastTimestamp = strtotime($lastTime);
        return (time() - $lastTimestamp) >= ($intervalDays * 86400);
    }

    private function buildApiUrl($baseUrl, $params = []) {
        $parsed = parse_url($baseUrl);
        if ($parsed === false) {
            return $baseUrl;
        }

        $existingParams = [];
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $existingParams);
        }

        $mergedParams = array_merge($existingParams, $params);

        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '/';

        $url = $scheme . '://' . $host . $port . $path;
        if (!empty($mergedParams)) {
            $url .= '?' . http_build_query($mergedParams);
        }

        return $url;
    }

    private function httpGet($url, $timeout = 30, $retry = 3) {
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        $proxyMgr = null;
        $proxyFile = __DIR__ . '/../proxy/ProxyManager.php';
        if (file_exists($proxyFile)) {
            require_once $proxyFile;
            $proxyMgr = new ProxyManager();
        }

        $sslVersions = [null, CURL_SSLVERSION_TLSv1_2, CURL_SSLVERSION_TLSv1_1, CURL_SSLVERSION_SSLv2 | CURL_SSLVERSION_SSLv3];

        for ($attempt = 0; $attempt <= $retry; $attempt++) {
            foreach ($sslVersions as $sslIdx => $sslVersion) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                if ($sslVersion !== null) {
                    curl_setopt($ch, CURLOPT_SSLVERSION, $sslVersion);
                }
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
                curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json, text/plain, */*',
                    'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                    'Referer: ' . (parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/')
                ]);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

                $currentProxy = null;
                if ($proxyMgr && $proxyMgr->isEnabled() && $attempt > 0) {
                    $currentProxy = $proxyMgr->applyProxyToCurl($ch);
                }

                $startTime = microtime(true);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                curl_close($ch);

                if ($currentProxy) {
                    if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                        $proxyMgr->markProxySuccess($currentProxy['id'], $responseTime);
                        return $response;
                    } else {
                        $proxyMgr->markProxyFailed($currentProxy['id']);
                    }
                }

                if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                    return $response;
                }

                $lastError = $error ? $error : ('HTTP ' . $httpCode);

                $isSslError = $error && (
                    stripos($error, 'SSL') !== false ||
                    stripos($error, 'tls') !== false ||
                    stripos($error, 'certificate') !== false
                );

                if (!$isSslError) {
                    break;
                }
            }

            $isRetryable = $lastError && (
                strpos($lastError, 'Could not resolve') !== false ||
                strpos($lastError, 'Connection timed out') !== false ||
                strpos($lastError, 'Failed to connect') !== false ||
                strpos($lastError, 'Operation timed out') !== false ||
                stripos($lastError, 'SSL') !== false ||
                stripos($lastError, 'tls') !== false
            ) || ($httpCode >= 500 || $httpCode == 429);

            if ($attempt < $retry && $isRetryable) {
                usleep(300000 + $attempt * 200000);
            }
        }

        $this->lastHttpError = $lastError;
        return false;
    }
}
