<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/AccelerationNodeManager.php';

class UpdateManager
{
    private AccelerationNodeManager $nodeManager;
    private string $cacheFile;
    private array $cache = [];

    public function __construct()
    {
        $this->nodeManager = new AccelerationNodeManager();
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
            'best_node_id' => null,
            'best_node_name' => null,
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
        $results = $this->nodeManager->testAllNodes($repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch']);
        $bestNode = $this->nodeManager->getBestNode($results);

        if ($bestNode) {
            $this->cache['best_node_id'] = $bestNode['node_id'];
            $this->cache['best_node_name'] = $bestNode['node_name'];
            $this->cache['best_response_time_ms'] = $bestNode['response_time_ms'];
        } else {
            $this->cache['best_node_id'] = null;
            $this->cache['best_node_name'] = null;
            $this->cache['best_response_time_ms'] = null;
        }
        $this->cache['last_speed_test'] = date('Y-m-d H:i:s');
        $this->cache['speed_test_results'] = $results;

        $history = $this->cache['history'] ?? [];
        $history[] = [
            'time' => $this->cache['last_speed_test'],
            'best_node' => $bestNode ? $bestNode['node_name'] : null,
            'best_time_ms' => $bestNode ? $bestNode['response_time_ms'] : null,
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
            'message' => $bestNode ? '测速完成' : '所有加速节点均不可用',
            'best_node' => $bestNode,
            'results' => $results,
            'tested_at' => $this->cache['last_speed_test']
        ];
    }

    public function fetchRemoteFile(string $path, ?array $node = null, bool $useAntiCache = true): array
    {
        $repoInfo = $this->getRepoInfo();

        if ($node === null) {
            if ($this->cache['best_node_id']) {
                $node = $this->nodeManager->getNodeById($this->cache['best_node_id']);
            }
            if ($node === null) {
                $enabledNodes = $this->nodeManager->getEnabledNodes();
                $node = !empty($enabledNodes) ? $enabledNodes[0] : null;
            }
        }

        if (!$node) {
            return [
                'success' => false,
                'error' => '无可用的加速节点',
                'data' => null
            ];
        }

        $url = $this->nodeManager->buildUrl($node, $repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch'], $path, $useAntiCache);

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
            'node_name' => $node['name'],
            'node_id' => $node['id'],
            'url' => $url,
            'response_time_ms' => $totalTime,
            'data' => $httpCode === 200 ? $response : null,
            'error' => $error ?: ($httpCode !== 200 ? "HTTP {$httpCode}" : '')
        ];
    }

    public function fetchWithFallback(string $path): array
    {
        $repoInfo = $this->getRepoInfo();
        $enabledNodes = $this->nodeManager->getEnabledNodes();

        $sortedNodes = [];
        if (!empty($this->cache['speed_test_results'])) {
            $nodeIds = array_column($this->cache['speed_test_results'], 'node_id');
            foreach ($nodeIds as $id) {
                $node = $this->nodeManager->getNodeById($id);
                if ($node) {
                    $sortedNodes[] = $node;
                }
            }
            $remaining = array_values(array_filter($enabledNodes, fn($n) => !in_array($n['id'], $nodeIds)));
            $sortedNodes = array_merge($sortedNodes, $remaining);
        } else {
            $sortedNodes = $enabledNodes;
        }

        if (empty($sortedNodes)) {
            return [
                'success' => false,
                'error' => '无可用的加速节点',
                'attempts' => 0
            ];
        }

        $results = [];
        $lastResult = null;
        foreach ($sortedNodes as $node) {
            $result = $this->fetchRemoteFile($path, $node);
            $results[] = $result;
            $lastResult = $result;
            if ($result['success']) {
                $this->recordNodeSuccess($node['id']);
                return $result;
            }
            $this->recordNodeFailure($node['id']);
        }

        return [
            'success' => false,
            'error' => '所有加速节点请求失败',
            'attempts' => count($results),
            'last_result' => $lastResult
        ];
    }

    private function recordNodeSuccess(int $nodeId): void
    {
        $key = 'node_stats_' . $nodeId;
        $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
        $stats['success']++;
        $stats['consecutive_fail'] = 0;
        $this->cache[$key] = $stats;
        $this->saveCache();
    }

    private function recordNodeFailure(int $nodeId): void
    {
        $key = 'node_stats_' . $nodeId;
        $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
        $stats['fail']++;
        $stats['consecutive_fail']++;
        $this->cache[$key] = $stats;
        $this->saveCache();
    }

    public function getSmartNodes(): array
    {
        $enabledNodes = $this->nodeManager->getEnabledNodes();
        $scored = [];
        foreach ($enabledNodes as $node) {
            $key = 'node_stats_' . $node['id'];
            $stats = $this->cache[$key] ?? ['success' => 0, 'fail' => 0, 'consecutive_fail' => 0];
            $total = $stats['success'] + $stats['fail'];
            $rate = $total > 0 ? round($stats['success'] / $total * 100, 1) : 100;
            $consecutivePenalty = min($stats['consecutive_fail'] * 10, 50);
            $score = $rate - $consecutivePenalty;
            $scored[] = [
                'node' => $node,
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
                'node_used' => $result['node_name'] ?? null,
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
        $smartNodes = $this->getSmartNodes();
        return [
            'cache' => $this->cache,
            'node_count' => count($this->nodeManager->getAllNodes()),
            'enabled_node_count' => count($this->nodeManager->getEnabledNodes()),
            'repo' => $this->getRepoInfo(),
            'smart_ranking' => $smartNodes
        ];
    }

    public function getConfigData(): array
    {
        $result = $this->fetchWithFallback('config.php');
        if ($result['success'] && $result['data']) {
            return [
                'success' => true,
                'raw' => $result['data'],
                'node_used' => $result['node_name']
            ];
        }
        return [
            'success' => false,
            'error' => $result['error'] ?? '获取配置失败'
        ];
    }
}
