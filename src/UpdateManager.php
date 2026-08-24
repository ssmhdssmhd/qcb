<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/ResourceSiteManager.php';

class UpdateManager
{
    private ResourceSiteManager $siteManager;
    private string $cacheFile;
    private array $cache = [];

    public function __construct()
    {
        $this->siteManager = new ResourceSiteManager();
        $this->cacheFile = UPDATE_CACHE_FILE;
        $this->loadCache();
    }

    private function loadCache(): void
    {
        if (file_exists($this->cacheFile)) {
            $data = file_get_contents($this->cacheFile);
            $this->cache = json_decode($data, true) ?: $this->getDefaultCache();
        } else {
            $this->cache = $this->getDefaultCache();
            $this->saveCache();
        }
    }

    private function getDefaultCache(): array
    {
        return [
            'last_update' => null,
            'last_speed_test' => null,
            'best_site_id' => null,
            'best_site_name' => null,
            'best_response_time_ms' => null,
            'best_error_rate' => null,
            'speed_test_results' => [],
            'update_results' => [],
            'history' => [],
            'version' => APP_VERSION
        ];
    }

    private function saveCache(): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->cacheFile, json_encode($this->cache, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    public function getRepoInfo(): array
    {
        return [
            'owner' => DEFAULT_GITHUB_REPO,
            'repo' => DEFAULT_GITHUB_REPO,
            'branch' => DEFAULT_GITHUB_BRANCH
        ];
    }

    public function speedTest(): array
    {
        $repoInfo = $this->getRepoInfo();
        $results = $this->siteManager->testAllSites($repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch']);
        $bestSite = $this->siteManager->getBestSite($results);

        if ($bestSite) {
            $this->cache['best_site_id'] = $bestSite['site_id'];
            $this->cache['best_site_name'] = $bestSite['site_name'];
            $this->cache['best_response_time_ms'] = $bestSite['response_time_ms'];
        } else {
            $this->cache['best_site_id'] = null;
            $this->cache['best_site_name'] = null;
            $this->cache['best_response_time_ms'] = null;
        }
        $this->cache['last_speed_test'] = date('Y-m-d H:i:s');
        $this->cache['speed_test_results'] = $results;

        $history = $this->cache['history'] ?? [];
        $history[] = [
            'time' => $this->cache['last_speed_test'],
            'best_site' => $bestSite ? $bestSite['site_name'] : null,
            'best_time_ms' => $bestSite ? $bestSite['response_time_ms'] : null,
            'success_count' => count(array_filter($results, fn($r) => $r['success'])),
            'total_count' => count($results)
        ];
        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }
        $this->cache['history'] = $history;

        $this->saveCache();

        return [
            'status' => 'success',
            'message' => $bestSite ? '测速完成' : '所有加速站均不可用',
            'best_site' => $bestSite,
            'results' => $results,
            'tested_at' => $this->cache['last_speed_test']
        ];
    }

    public function fetchRemoteFile(string $path, ?array $site = null, bool $useAntiCache = true): array
    {
        $repoInfo = $this->getRepoInfo();

        if ($site === null) {
            if ($this->cache['best_site_id']) {
                $site = $this->siteManager->getSiteById($this->cache['best_site_id']);
            }
            if ($site === null) {
                $enabledSites = $this->siteManager->getEnabledSites();
                $site = !empty($enabledSites) ? $enabledSites[0] : null;
            }
        }

        if (!$site) {
            return [
                'success' => false,
                'error' => '无可用的资源站',
                'data' => null
            ];
        }

        $url = $this->siteManager->buildUrl($site, $repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch'], $path, $useAntiCache);

        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; QCB-Update/1.0)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_ENCODING => '',
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $totalTime = round((microtime(true) - $startTime) * 1000, 2);
        curl_close($ch);

        return [
            'success' => $httpCode === 200,
            'http_code' => $httpCode,
            'site_name' => $site['name'],
            'site_id' => $site['id'],
            'url' => $url,
            'response_time_ms' => $totalTime,
            'data' => $httpCode === 200 ? $response : null,
            'error' => $error ?: ($httpCode !== 200 ? "HTTP {$httpCode}" : '')
        ];
    }

    public function fetchWithFallback(string $path): array
    {
        $repoInfo = $this->getRepoInfo();
        $enabledSites = $this->siteManager->getEnabledSites();

        $sortedSites = [];
        if (!empty($this->cache['speed_test_results'])) {
            $siteIds = array_column($this->cache['speed_test_results'], 'site_id');
            foreach ($siteIds as $id) {
                $site = $this->siteManager->getSiteById($id);
                if ($site) {
                    $sortedSites[] = $site;
                }
            }
            $remaining = array_values(array_filter($enabledSites, fn($s) => !in_array($s['id'], $siteIds)));
            $sortedSites = array_merge($sortedSites, $remaining);
        } else {
            $sortedSites = $enabledSites;
        }

        if (empty($sortedSites)) {
            return [
                'success' => false,
                'error' => '无可用的资源站',
                'attempts' => 0
            ];
        }

        $results = [];
        $lastResult = null;
        foreach ($sortedSites as $site) {
            $result = $this->fetchRemoteFile($path, $site);
            $results[] = $result;
            $lastResult = $result;
            if ($result['success']) {
                $this->recordSiteSuccess($site['id']);
                return $result;
            }
            $this->recordSiteFailure($site['id']);
        }

        return [
            'success' => false,
            'error' => '所有加速站请求失败',
            'attempts' => count($results),
            'last_result' => $lastResult
        ];
    }

    private function recordSiteSuccess(int $siteId): void
    {
        $key = 'site_stats_' . $siteId;
        $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
        $stats['success']++;
        $stats['consecutive_fail'] = 0;
        $this->cache[$key] = $stats;
        $this->saveCache();
    }

    private function recordSiteFailure(int $siteId): void
    {
        $key = 'site_stats_' . $siteId;
        $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
        $stats['fail']++;
        $stats['consecutive_fail']++;
        $this->cache[$key] = $stats;
        $this->saveCache();
    }

    public function getSmartSites(): array
    {
        $enabledSites = $this->siteManager->getEnabledSites();
        $scored = [];
        foreach ($enabledSites as $site) {
            $key = 'site_stats_' . $site['id'];
            $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
            $total = $stats['success'] + $stats['fail'];
            $rate = $total > 0 ? round($stats['success'] / $total * 100, 1) : 100;
            $consecutivePenalty = min($stats['consecutive_fail'] * 10, 50);
            $score = $rate - $consecutivePenalty;
            $scored[] = [
                'site' => $site,
                'score' => $score,
                'success_rate' => $rate,
                'consecutive_fail' => $stats['consecutive_fail'],
                'total_requests' => $total
            ];
        }
        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        return $scored;
    }

    public function checkUpdate(): array
    {
        $testResult = $this->speedTest();

        $paths = [
            'config.php',
            'version.php'
        ];

        $files = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($paths as $path) {
            $result = $this->fetchWithFallback($path);
            $files[] = [
                'path' => $path,
                'success' => $result['success'],
                'site_used' => $result['site_name'] ?? null,
                'response_time_ms' => $result['response_time_ms'] ?? null,
                'content' => $result['success'] ? $result['data'] : null,
                'http_code' => $result['http_code'] ?? null,
                'error' => $result['error'] ?? null
            ];
            if ($result['success']) {
                $successCount++;
            } else {
                $failCount++;
            }
        }

        $this->cache['last_update'] = date('Y-m-d H:i:s');
        $this->cache['update_results'] = $files;
        $this->cache['update_summary'] = [
            'total_files' => count($paths),
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'checked_at' => $this->cache['last_update']
        ];
        $this->saveCache();

        return [
            'status' => $successCount > 0 ? 'success' : 'failed',
            'message' => $successCount > 0 ? "更新检查完成，{$successCount}/" . count($paths) . " 文件成功" : '所有更新检查失败',
            'speed_test' => $testResult,
            'files' => $files,
            'summary' => $this->cache['update_summary'],
            'checked_at' => $this->cache['last_update']
        ];
    }

    public function getUpdateStatus(): array
    {
        $smartSites = $this->getSmartSites();
        return [
            'cache' => $this->cache,
            'site_count' => count($this->siteManager->getAllSites()),
            'enabled_site_count' => count($this->siteManager->getEnabledSites()),
            'repo' => $this->getRepoInfo(),
            'smart_ranking' => $smartSites
        ];
    }

    public function getConfigData(): array
    {
        $result = $this->fetchWithFallback('config.php');
        if ($result['success'] && $result['data']) {
            return [
                'success' => true,
                'raw' => $result['data'],
                'site_used' => $result['site_name']
            ];
        }
        return [
            'success' => false,
            'error' => $result['error'] ?? '获取配置失败'
        ];
    }
}
