<?php

require_once __DIR__ . '/../config.php';

class AccelerationNodeManager
{
    private string $nodesFile;
    private array $nodes = [];

    public function __construct()
    {
        $this->nodesFile = NODES_FILE;
        $this->loadNodes();
    }

    private function loadNodes(): void
    {
        if (!file_exists($this->nodesFile)) {
            $this->nodes = $this->getDefaultNodes();
            $this->saveNodes();
            return;
        }
        $data = file_get_contents($this->nodesFile);
        $this->nodes = json_decode($data, true) ?: $this->getDefaultNodes();
    }

    private function getDefaultNodes(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'jsDelivr CDN',
                'url' => 'https://cdn.jsdelivr.net/gh/{owner}/{repo}@{branch}/{path}',
                'type' => 'cdn',
                'enabled' => true,
                'priority' => 10,
                'note' => '全球 CDN 加速，自动选择最近节点',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'raw.githubusercontent.com',
                'url' => 'https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'direct',
                'enabled' => true,
                'priority' => 20,
                'note' => 'GitHub 官方原始地址',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'ghproxy.com 加速',
                'url' => 'https://ghproxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 30,
                'note' => '国内加速代理',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'gh.con.sh 加速',
                'url' => 'https://gh.con.sh/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 40,
                'note' => 'GitHub 镜像加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'name' => 'mirror.ghproxy.com',
                'url' => 'https://mirror.ghproxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => true,
                'priority' => 50,
                'note' => '备用镜像加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 6,
                'name' => 'raw.kgithub.com',
                'url' => 'https://raw.kgithub.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 60,
                'note' => 'KGitHub 加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 7,
                'name' => 'gh-proxy.com',
                'url' => 'https://gh-proxy.com/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 70,
                'note' => 'GH Proxy 加速',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 8,
                'name' => 'ghps.cc 加速',
                'url' => 'https://ghps.cc/https://raw.githubusercontent.com/{owner}/{repo}/{branch}/{path}',
                'type' => 'proxy',
                'enabled' => false,
                'priority' => 80,
                'note' => 'GHPS 加速',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }

    private function saveNodes(): void
    {
        $dir = dirname($this->nodesFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->nodesFile, json_encode($this->nodes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function getAllNodes(): array
    {
        return $this->nodes;
    }

    public function getEnabledNodes(): array
    {
        $enabled = array_filter($this->nodes, fn($n) => $n['enabled'] === true);
        usort($enabled, fn($a, $b) => $a['priority'] <=> $b['priority']);
        return $enabled;
    }

    public function getNodeById(int $id): ?array
    {
        foreach ($this->nodes as $node) {
            if ($node['id'] === $id) {
                return $node;
            }
        }
        return null;
    }

    public function addNode(array $data): array
    {
        $newId = max(1, (int)end($this->nodes)['id'] + 1);
        $newNode = [
            'id' => $newId,
            'name' => $data['name'],
            'url' => $data['url'],
            'type' => $data['type'] ?? 'custom',
            'enabled' => !empty($data['enabled']),
            'priority' => intval($data['priority'] ?? 100),
            'note' => $data['note'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->nodes[] = $newNode;
        $this->saveNodes();
        return $newNode;
    }

    public function updateNode(int $id, array $data): ?array
    {
        foreach ($this->nodes as &$node) {
            if ($node['id'] === $id) {
                $allowedFields = ['name', 'url', 'type', 'enabled', 'priority', 'note'];
                foreach ($allowedFields as $field) {
                    if (isset($data[$field])) {
                        if ($field === 'enabled') {
                            $node[$field] = !empty($data[$field]);
                        } elseif ($field === 'priority') {
                            $node[$field] = intval($data[$field]);
                        } else {
                            $node[$field] = $data[$field];
                        }
                    }
                }
                $this->saveNodes();
                return $node;
            }
        }
        return null;
    }

    public function deleteNode(int $id): bool
    {
        $count = count($this->nodes);
        $this->nodes = array_values(array_filter($this->nodes, fn($n) => $n['id'] !== $id));
        if (count($this->nodes) < $count) {
            $this->saveNodes();
            return true;
        }
        return false;
    }

    public function toggleNode(int $id): ?array
    {
        foreach ($this->nodes as &$node) {
            if ($node['id'] === $id) {
                $node['enabled'] = !$node['enabled'];
                $this->saveNodes();
                return $node;
            }
        }
        return null;
    }

    public function buildUrl(array $node, string $owner, string $repo, string $branch, string $path, bool $antiCache = true): string
    {
        $url = $node['url'];
        $url = str_replace('{owner}', $owner, $url);
        $url = str_replace('{repo}', $repo, $url);
        $url = str_replace('{branch}', $branch, $url);
        $url = str_replace('{path}', $path, $url);
        if ($antiCache) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . '_t=' . time() . '_' . mt_rand(1000, 9999);
        }
        return $url;
    }

    private function classifyError(int $httpCode, string $error, float $totalTime, float $connectTime): string
    {
        if ($totalTime >= SPEED_TEST_TIMEOUT) {
            return 'timeout';
        }
        if ($connectTime >= 5.0) {
            return 'connect_timeout';
        }
        if ($httpCode === 0 && stripos($error, 'dns') !== false) {
            return 'dns_failure';
        }
        if ($httpCode === 0 && stripos($error, 'connection') !== false) {
            return 'connection_refused';
        }
        if ($httpCode === 0 && stripos($error, 'SSL') !== false) {
            return 'ssl_error';
        }
        if ($httpCode === 0) {
            return 'network_error';
        }
        if ($httpCode >= 400 && $httpCode < 500) {
            return 'client_error';
        }
        if ($httpCode >= 500) {
            return 'server_error';
        }
        return 'unknown';
    }

    private function median(array $values): float
    {
        if (empty($values)) {
            return 0;
        }
        sort($values);
        $count = count($values);
        if ($count % 2 === 1) {
            return $values[(int)floor($count / 2)];
        }
        return ($values[$count / 2 - 1] + $values[$count / 2]) / 2;
    }

    public function testNodeOnce(array $node, string $owner, string $repo, string $branch, string $path, bool $antiCache = true): array
    {
        $url = $this->buildUrl($node, $owner, $repo, $branch, $path, $antiCache);

        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int)SPEED_TEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; QCB-SpeedTest/1.0)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_TCP_KEEPALIVE => 1,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $totalTime = (microtime(true) - $startTime);
        $connectTime = curl_getinfo($ch, CURLINFO_CONNECT_TIME) ?: 0;
        $downloadSize = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) ?: 0;
        $downloadSpeed = curl_getinfo($ch, CURLINFO_SPEED_DOWNLOAD) ?: 0;
        curl_close($ch);

        $totalTimeMs = round($totalTime * 1000, 2);
        $connectTimeMs = round($connectTime * 1000, 2);
        $errorType = $this->classifyError($httpCode, $error, $totalTime, $connectTime);

        return [
            'node_id' => $node['id'],
            'node_name' => $node['name'],
            'url' => $url,
            'http_code' => $httpCode,
            'success' => $httpCode === 200,
            'response_time_ms' => $totalTimeMs,
            'connect_time_ms' => $connectTimeMs,
            'download_size' => $downloadSize,
            'speed_bps' => round($downloadSpeed, 2),
            'error_type' => $errorType,
            'error' => $error,
        ];
    }

    public function testNode(array $node, string $owner = '', string $repo = '', string $branch = DEFAULT_GITHUB_BRANCH, string $testPath = 'config.php'): array
    {
        if (empty($owner)) {
            $owner = DEFAULT_GITHUB_REPO;
        }
        if (empty($repo)) {
            $repo = DEFAULT_GITHUB_REPO;
        }

        $results = [];
        $successTimes = [];
        $success = false;
        $lastResult = null;

        for ($i = 0; $i < 3; $i++) {
            $r = $this->testNodeOnce($node, $owner, $repo, $branch, $testPath, true);
            $lastResult = $r;
            $results[] = $r;
            if ($r['success']) {
                $successTimes[] = $r['response_time_ms'];
            }
            if (!$r['success'] && $r['error_type'] === 'timeout') {
                break;
            }
            if (!$r['success'] && $r['error_type'] === 'dns_failure') {
                break;
            }
            usleep(200000);
        }

        $medianTime = !empty($successTimes) ? round($this->median($successTimes), 2) : ($lastResult ? $lastResult['response_time_ms'] : 0);
        $avgSpeed = 0;
        $successCount = count($successTimes);
        if ($successCount > 0) {
            $totalSpeed = 0;
            foreach ($results as $r) {
                if ($r['success']) {
                    $totalSpeed += $r['speed_bps'];
                }
            }
            $avgSpeed = round($totalSpeed / $successCount, 2);
        }

        $finalSuccess = $successCount > 0;
        $lastSuccessResult = null;
        foreach ($results as $r) {
            if ($r['success']) {
                $lastSuccessResult = $r;
                break;
            }
        }

        $errorType = $finalSuccess ? 'none' : ($lastResult ? $lastResult['error_type'] : 'unknown');
        $errorMsg = $finalSuccess ? '' : ($lastResult ? $lastResult['error'] : '');

        return [
            'node_id' => $node['id'],
            'node_name' => $node['name'],
            'url' => $lastResult['url'] ?? '',
            'http_code' => $finalSuccess ? ($lastSuccessResult['http_code'] ?? 200) : ($lastResult['http_code'] ?? 0),
            'success' => $finalSuccess,
            'response_time_ms' => $medianTime,
            'connect_time_ms' => $lastResult['connect_time_ms'] ?? 0,
            'download_size' => $lastSuccessResult['download_size'] ?? 0,
            'speed_bps' => $avgSpeed,
            'error_type' => $errorType,
            'error' => $errorMsg,
            'retry_count' => count($results),
            'success_count' => $successCount,
            'all_attempts' => $results,
            'tested_at' => date('Y-m-d H:i:s')
        ];
    }

    public function testAllNodes(string $owner = '', string $repo = '', string $branch = DEFAULT_GITHUB_BRANCH): array
    {
        if (empty($owner)) {
            $owner = DEFAULT_GITHUB_REPO;
        }
        if (empty($repo)) {
            $repo = DEFAULT_GITHUB_REPO;
        }

        $enabledNodes = $this->getEnabledNodes();
        $results = [];
        foreach ($enabledNodes as $node) {
            $results[] = $this->testNode($node, $owner, $repo, $branch);
        }

        $successResults = array_filter($results, fn($r) => $r['success']);
        $failResults = array_filter($results, fn($r) => !$r['success']);

        usort($successResults, fn($a, $b) => $a['response_time_ms'] <=> $b['response_time_ms']);
        usort($failResults, fn($a, $b) => $a['response_time_ms'] <=> $b['response_time_ms']);

        return array_merge(array_values($successResults), array_values($failResults));
    }

    public function getBestNode(array $testResults): ?array
    {
        foreach ($testResults as $result) {
            if ($result['success']) {
                return $result;
            }
        }
        return null;
    }
}
