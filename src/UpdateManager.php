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
            $this->cache = json_decode($data, true) ?: [];
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
            'update_results' => [],
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
        }
        $this->cache['last_speed_test'] = date('Y-m-d H:i:s');
        $this->cache['speed_test_results'] = $results;
        $this->saveCache();

        return [
            'status' => 'success',
            'message' => $bestSite ? '测速完成' : '所有加速站均不可用',
            'best_site' => $bestSite,
            'results' => $results,
            'tested_at' => $this->cache['last_speed_test']
        ];
    }

    public function fetchRemoteFile(string $path, ?array $site = null): array
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

        $url = $this->siteManager->buildUrl($site, $repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch'], $path);
        
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; QCB-Update/1.0)'
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
        if ($this->cache['speed_test_results']) {
            $siteIds = array_column($this->cache['speed_test_results'], 'site_id');
            $sortedById = [];
            foreach ($siteIds as $id) {
                $site = $this->siteManager->getSiteById($id);
                if ($site) {
                    $sortedById[] = $site;
                }
            }
            $remaining = array_filter($enabledSites, fn($s) => !in_array($s['id'], $siteIds));
            $sortedSites = array_merge($sortedById, $remaining);
        } else {
            $sortedSites = $enabledSites;
        }

        $results = [];
        foreach ($sortedSites as $site) {
            $result = $this->fetchRemoteFile($path, $site);
            $results[] = $result;
            if ($result['success']) {
                return $result;
            }
        }

        return [
            'success' => false,
            'error' => '所有加速站请求失败',
            'attempts' => count($results),
            'last_result' => end($results)
        ];
    }

    public function checkUpdate(): array
    {
        $testResult = $this->speedTest();
        
        $paths = [
            'config.php',
            'version.php',
            'src/AuthConfig.php'
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
        return [
            'cache' => $this->cache,
            'site_count' => count($this->siteManager->getAllSites()),
            'enabled_site_count' => count($this->siteManager->getEnabledSites()),
            'repo' => $this->getRepoInfo()
        ];
    }

    public function getConfigData(): array
    {
        $result = $this->fetchWithFallback('config.php');
        if ($result['success'] && $result['data']) {
            $content = $result['data'];
            if (preg_match('/return\s+\[(.+?)\];/s', $content, $matches)) {
                return [
                    'success' => true,
                    'data' => null,
                    'raw' => $content,
                    'site_used' => $result['site_name']
                ];
            }
            return [
                'success' => true,
                'raw' => $content,
                'site_used' => $result['site_name']
            ];
        }
        return [
            'success' => false,
            'error' => $result['error'] ?? '获取配置失败'
        ];
    }
}
