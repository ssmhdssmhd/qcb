<?php
/**
 * 资源站管理器
 * 负责资源站列表管理、采集接口调用、自动学习调度
 */

class ResourceSiteManager {

    private $configFile;
    private $config;

    public function __construct() {
        $this->configFile = __DIR__ . '/sites_config.php';
        if (file_exists($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            $this->config = [
                'version' => '1.0',
                'update_date' => date('Y-m-d H:i:s'),
                'sites' => [],
                'auto_learn' => [
                    'enabled' => true,
                    'interval_days' => 3,
                    'videos_per_site' => 5,
                    'max_sites_per_run' => 5,
                    'min_segments' => 50,
                    'max_ad_percentage' => 90
                ]
            ];
        }
    }

    public function getAllSites($includePaused = false) {
        $sites = $this->config['sites'] ?? [];
        if (!$includePaused) {
            $sites = array_filter($sites, function($s) {
                return ($s['status'] ?? 'active') === 'active';
            });
        }
        usort($sites, function($a, $b) {
            return ($a['priority'] ?? 99) - ($b['priority'] ?? 99);
        });
        return $sites;
    }

    public function getSiteByName($name) {
        foreach ($this->config['sites'] as $site) {
            if ($site['name'] === $name) {
                return $site;
            }
        }
        return null;
    }

    public function getSitesByDomain($domain) {
        $result = [];
        foreach ($this->config['sites'] as $site) {
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

        $this->config['sites'][] = $site;
        $this->config['update_date'] = date('Y-m-d H:i:s');
        $this->saveConfig();

        return ['success' => true, 'message' => '添加成功'];
    }

    public function updateSite($name, $siteData) {
        foreach ($this->config['sites'] as &$site) {
            if ($site['name'] === $name) {
                $site = array_merge($site, $siteData);
                $site['name'] = $name;
                $this->config['update_date'] = date('Y-m-d H:i:s');
                $this->saveConfig();
                return ['success' => true, 'message' => '更新成功'];
            }
        }
        return ['success' => false, 'message' => '资源站不存在'];
    }

    public function deleteSite($name) {
        $newSites = [];
        $found = false;
        foreach ($this->config['sites'] as $site) {
            if ($site['name'] !== $name) {
                $newSites[] = $site;
            } else {
                $found = true;
            }
        }
        if ($found) {
            $this->config['sites'] = $newSites;
            $this->config['update_date'] = date('Y-m-d H:i:s');
            $this->saveConfig();
            return ['success' => true, 'message' => '删除成功'];
        }
        return ['success' => false, 'message' => '资源站不存在'];
    }

    public function fetchVideos($apiUrl, $page = 1, $limit = 20) {
        $url = $apiUrl;
        if (strpos($url, '?') === false) {
            $url .= '?';
        } else {
            $url .= '&';
        }
        $url .= 'ac=list&pg=' . intval($page) . '&limit=' . intval($limit);

        $response = $this->httpGet($url);
        if ($response === false) {
            return ['success' => false, 'message' => '请求失败'];
        }

        $data = json_decode($response, true);
        if (!$data) {
            return ['success' => false, 'message' => '解析JSON失败'];
        }

        $videos = [];
        $list = $data['list'] ?? $data['data'] ?? [];
        foreach ($list as $item) {
            $vodPlayUrl = $item['vod_play_url'] ?? $item['play_url'] ?? '';
            $vodName = $item['vod_name'] ?? $item['name'] ?? '';
            $vodId = $item['vod_id'] ?? $item['id'] ?? 0;

            if (empty($vodPlayUrl)) continue;

            $m3u8Url = $this->extractM3u8Url($vodPlayUrl);
            if ($m3u8Url) {
                $videos[] = [
                    'id' => $vodId,
                    'name' => $vodName,
                    'url' => $m3u8Url,
                    'raw_play_url' => $vodPlayUrl
                ];
            }
        }

        return [
            'success' => true,
            'total' => $data['total'] ?? $data['page']['pagecount'] ?? count($videos),
            'page' => $page,
            'pagecount' => $data['pagecount'] ?? $data['page']['pagecount'] ?? 1,
            'videos' => $videos
        ];
    }

    private function extractM3u8Url($playUrl) {
        if (empty($playUrl)) return null;

        if (preg_match('/https?:\/\/[^\s\$\r\n]+\.m3u8[^\s\$\r\n]*/i', $playUrl, $matches)) {
            return $matches[0];
        }

        if (strpos($playUrl, '$') !== false) {
            $parts = explode('$', $playUrl);
            foreach ($parts as $part) {
                if (preg_match('/https?:\/\/[^\s]+\.m3u8[^\s]*/i', $part, $matches)) {
                    return $matches[0];
                }
            }
        }

        if (strpos($playUrl, "\r\n") !== false || strpos($playUrl, "\n") !== false) {
            $lines = preg_split('/\r\n|\n/', $playUrl);
            foreach ($lines as $line) {
                if (preg_match('/https?:\/\/[^\s]+\.m3u8[^\s]*/i', $line, $matches)) {
                    return $matches[0];
                }
            }
        }

        return null;
    }

    public function getAutoLearnConfig() {
        return $this->config['auto_learn'] ?? [
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
        $this->config['auto_learn'] = array_merge($default, $config);
        $this->config['update_date'] = date('Y-m-d H:i:s');
        $this->saveConfig();
        return ['success' => true, 'message' => '配置已更新'];
    }

    public function runAutoLearn($domainRuleManager, $options = []) {
        $config = $this->getAutoLearnConfig();
        if (empty($config['enabled'])) {
            return ['success' => false, 'message' => '自动学习未启用'];
        }

        $maxSites = $options['max_sites'] ?? $config['max_sites_per_run'] ?? 5;
        $videosPerSite = $options['videos_per_site'] ?? $config['videos_per_site'] ?? 5;
        $minSegments = $config['min_segments'] ?? 50;
        $maxAdPercentage = $config['max_ad_percentage'] ?? 90;

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

            $fetchResult = $this->fetchVideos($site['api_url'], 1, $videosPerSite * 3);
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

                try {
                    $parsedUrl = parse_url($video['url']);
                    $videoDomain = $parsedUrl['host'] ?? '';
                    if (empty($videoDomain)) continue;

                    if (!isset($siteResult['domains'][$videoDomain])) {
                        $siteResult['domains'][$videoDomain] = 0;
                    }

                    $mediaUrl = $this->resolveMasterPlaylist($video['url']);
                    $parser = new M3U8Parser();
                    $playlist = $parser->parse($mediaUrl);

                    if (empty($playlist['segments']) || count($playlist['segments']) < $minSegments) {
                        continue;
                    }

                    $engine = new EnhancedAdRuleEngine([
                        'checkDiscontinuity' => true,
                        'checkRepetitiveDuration' => true
                    ]);
                    $engine->setDomain($videoDomain);
                    $analysis = $engine->analyzeAllSegments($playlist['segments']);

                    $adPercentage = $analysis['totalCount'] > 0
                        ? ($analysis['adCount'] / $analysis['totalCount'] * 100)
                        : 0;

                    if ($adPercentage >= $maxAdPercentage) {
                        continue;
                    }

                    $domainResult = $domainRuleManager->learnFromAnalysis($videoDomain, $analysis);
                    if ($domainResult) {
                        $siteResult['videos_learned']++;
                        $siteResult['domains'][$videoDomain]++;
                        $totalLearned++;
                        $learnedCount++;
                    } else {
                        $siteResult['videos_failed']++;
                        $totalFailed++;
                    }
                } catch (Exception $e) {
                    $siteResult['videos_failed']++;
                    $totalFailed++;
                }
            }

            $results[] = $siteResult;
        }

        $this->setLastLearnTime();

        return [
            'success' => true,
            'message' => '自动学习完成',
            'sites_processed' => count($sites),
            'total_learned' => $totalLearned,
            'total_failed' => $totalFailed,
            'details' => $results
        ];
    }

    private function resolveMasterPlaylist($url) {
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
        } catch (Exception $e) {
        }
        return $url;
    }

    public function getLastLearnTime() {
        $stateFile = __DIR__ . '/auto_learn_state.php';
        if (file_exists($stateFile)) {
            $state = require $stateFile;
            return $state['last_learn_time'] ?? null;
        }
        return null;
    }

    public function setLastLearnTime() {
        $stateFile = __DIR__ . '/auto_learn_state.php';
        $state = [
            'last_learn_time' => date('Y-m-d H:i:s'),
            'last_learn_timestamp' => time()
        ];
        $content = '<?php' . "\n";
        $content .= '// 自动学习状态' . "\n";
        $content .= 'return ' . var_export($state, true) . ';' . "\n";
        file_put_contents($stateFile, $content);
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

    private function httpGet($url, $timeout = 10) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 200 && $httpCode < 300) {
            return $response;
        }
        return false;
    }

    private function saveConfig() {
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * 资源站配置列表' . "\n";
        $content .= ' * 自动更新于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($this->config) . ';' . "\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    private function arrayExport($array, $indent = 0) {
        if (!is_array($array)) return var_export($array, true);
        if (empty($array)) return '[]';

        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        $isList = range(0, count($array) - 1) === array_keys($array);

        $items = [];
        foreach ($array as $key => $value) {
            $keyStr = $isList ? '' : var_export((string)$key, true) . ' => ';
            $valueStr = is_array($value) ? $this->arrayExport($value, $indent + 1) : var_export($value, true);
            $items[] = $nextPrefix . $keyStr . $valueStr;
        }

        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }
}
