<?php

require_once __DIR__ . '/../config.php';

class ResourceSiteManager
{
    private string $sitesFile;
    private array $sites = [];

    public function __construct()
    {
        $this->sitesFile = SITES_FILE;
        $this->loadSites();
    }

    private function loadSites(): void
    {
        if (!file_exists($this->sitesFile)) {
            $this->sites = $this->getDefaultSites();
            $this->saveSites();
            return;
        }
        $data = file_get_contents($this->sitesFile);
        $this->sites = json_decode($data, true) ?: $this->getDefaultSites();
    }

    private function getDefaultSites(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'jsDelivr CDN',
                'url' => 'https://cdn.jsdelivr.net/gh/{owner}/{repo}@{branch}/{path}',
                'type' => 'cdn',
                'enabled' => true,
                'priority' => 1,
                'note' => '全球 CDN 加速，自动选择最近节点',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'raw.githubusercontent.com',
                'url' => 'https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'direct',
                'enabled' => true,
                'priority' => 2,
                'note' => 'GitHub 官方原始地址',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'ghproxy.com 加速',
                'url' => 'https://ghproxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 3,
                'note' => '国内加速代理',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'gh.con.sh 加速',
                'url' => 'https://gh.con.sh/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 4,
                'note' => 'GitHub 镜像加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'name' => 'mirror.ghproxy.com',
                'url' => 'https://mirror.ghproxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 5,
                'note' => '备用镜像加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 6,
                'name' => 'raw.kgithub.com',
                'url' => 'https://raw.kgithub.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 6,
                'note' => 'KGitHub 加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 7,
                'name' => 'gh-proxy.com',
                'url' => 'https://gh-proxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 7,
                'note' => 'GH Proxy 加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 8,
                'name' => 'ghps.cc 加速',
                'url' => 'https://ghps.cc/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 8,
                'note' => 'GHPS 加速',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    private function saveSites(): void
    {
        $dir = dirname($this->sitesFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->sitesFile, json_encode($this->sites, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getAllSites(): array
    {
        return $this->sites;
    }

    public function getEnabledSites(): array
    {
        return array_filter($this->sites, fn($s) => $s['enabled'] === true);
    }

    public function getSiteById(int $id): ?array
    {
        foreach ($this->sites as $site) {
            if ($site['id'] === $id) {
                return $site;
            }
        }
        return null;
    }

    public function addSite(array $data): array
    {
        $newId = end($this->sites)['id'] + 1;
        $newSite = [
            'id' => $newId,
            'name' => $data['name'],
            'url' => $data['url'],
            'type' => $data['type'] ?? 'custom',
            'enabled' => $data['enabled'] ?? true,
            'priority' => $data['priority'] ?? ($newId + 10),
            'note' => $data['note'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->sites[] = $newSite;
        $this->saveSites();
        return $newSite;
    }

    public function updateSite(int $id, array $data): ?array
    {
        foreach ($this->sites as &$site) {
            if ($site['id'] === $id) {
                $allowedFields = ['name', 'url', 'type', 'enabled', 'priority', 'note'];
                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        $site[$field] = $data[$field];
                    }
                }
                $this->saveSites();
                return $site;
            }
        }
        return null;
    }

    public function deleteSite(int $id): bool
    {
        $count = count($this->sites);
        $this->sites = array_filter($this->sites, fn($s) => $s['id'] !== $id);
        $this->sites = array_values($this->sites);
        if (count($this->sites) < $count) {
            $this->saveSites();
            return true;
        }
        return false;
    }

    public function toggleSite(int $id): ?array
    {
        foreach ($this->sites as &$site) {
            if ($site['id'] === $id) {
                $site['enabled'] = !$site['enabled'];
                $this->saveSites();
                return $site;
            }
        }
        return null;
    }

    public function buildUrl(array $site, string $owner, string $repo, string $branch, string $path): string
    {
        $url = $site['url'];
        $url = str_replace('{owner}', $owner, $url);
        $url = str_replace('{repo}', $repo, $url);
        $url = str_replace('{branch}', $branch, $url);
        $url = str_replace('{path}', $path, $url);
        return $url;
    }

    public function testSite(array $site, string $owner = DEFAULT_GITHUB_REPO, string $repo = '', string $branch = DEFAULT_GITHUB_BRANCH, string $testPath = 'config.php'): array
    {
        if (empty($repo)) {
            $repo = DEFAULT_GITHUB_REPO;
        }
        $url = $this->buildUrl($site, $owner, $repo, $branch, $testPath);
        
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => SPEED_TEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; QCB-SpeedTest/1.0)'
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $totalTime = round((microtime(true) - $startTime) * 1000, 2);
        $downloadSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
        $speed = $totalTime > 0 ? round($downloadSize / $totalTime * 1000, 2) : 0;
        curl_close($ch);

        return [
            'site_id' => $site['id'],
            'site_name' => $site['name'],
            'url' => $url,
            'http_code' => $httpCode,
            'success' => $httpCode === 200,
            'response_time_ms' => $totalTime,
            'download_size' => $downloadSize,
            'speed_bps' => $speed,
            'error' => $error,
            'tested_at' => date('Y-m-d H:i:s')
        ];
    }

    public function testAllSites(string $owner = DEFAULT_GITHUB_REPO, string $repo = '', string $branch = DEFAULT_GITHUB_BRANCH): array
    {
        if (empty($repo)) {
            $repo = DEFAULT_GITHUB_REPO;
        }
        $results = [];
        foreach ($this->getEnabledSites() as $site) {
            $results[] = $this->testSite($site, $owner, $repo, $branch);
        }
        usort($results, function ($a, $b) {
            if ($a['success'] && !$b['success']) return -1;
            if (!$a['success'] && $b['success']) return 1;
            return $a['response_time_ms'] <=> $b['response_time_ms'];
        });
        return $results;
    }

    public function getBestSite(array $testResults): ?array
    {
        foreach ($testResults as $result) {
            if ($result['success']) {
                return $result;
            }
        }
        return null;
    }
}
