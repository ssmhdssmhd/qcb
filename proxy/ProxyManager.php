<?php
/**
 * 代理池管理器
 * 支持多种代理类型：HTTP、HTTPS、SOCKS5
 * 自动检测代理可用性，失败自动切换
 */

class ProxyManager {
    private $configFile;
    private $config;
    private $proxies = [];
    private $failedProxies = [];
    private $lastCheckFile;

    public function __construct($configFile = null) {
        if ($configFile === null) {
            $configFile = __DIR__ . '/proxy_config.php';
        }
        $this->configFile = $configFile;
        $this->lastCheckFile = __DIR__ . '/proxy_check_state.php';
        $this->loadConfig();
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            $this->config = [
                'version' => '1.0',
                'enabled' => false,
                'auto_switch' => true,
                'check_interval' => 300,
                'timeout' => 10,
                'proxies' => []
            ];
            $this->saveConfig();
        }
        $this->proxies = $this->config['proxies'] ?? [];
    }

    public function isEnabled() {
        return !empty($this->config['enabled']) && !empty($this->proxies);
    }

    public function setEnabled($enabled) {
        $this->config['enabled'] = (bool)$enabled;
        $this->saveConfig();
        return ['success' => true, 'message' => '已更新'];
    }

    public function setAutoSwitch($autoSwitch) {
        $this->config['auto_switch'] = (bool)$autoSwitch;
        $this->saveConfig();
        return ['success' => true, 'message' => '已更新'];
    }

    public function getAllProxies() {
        return $this->proxies;
    }

    public function getActiveProxies() {
        return array_filter($this->proxies, function($p) {
            return ($p['status'] ?? 'active') === 'active';
        });
    }

    public function addProxy($proxyData, $skipSave = false) {
        $proxy = array_merge([
            'id' => '',
            'name' => '',
            'type' => 'http',
            'host' => '',
            'port' => 0,
            'username' => '',
            'password' => '',
            'status' => 'active',
            'priority' => 100,
            'success_count' => 0,
            'fail_count' => 0,
            'last_check' => null,
            'last_success' => null,
            'response_time' => 0
        ], $proxyData);

        if (empty($proxy['host']) || empty($proxy['port'])) {
            return ['success' => false, 'message' => '主机和端口不能为空'];
        }

        // 去重检查：同一 host:port 不重复添加
        $dedupKey = strtolower($proxy['host']) . ':' . $proxy['port'];
        foreach ($this->proxies as $existing) {
            if (strtolower($existing['host']) . ':' . $existing['port'] === $dedupKey) {
                return ['success' => false, 'message' => '代理已存在', 'id' => $existing['id']];
            }
        }

        if (empty($proxy['id'])) {
            $proxy['id'] = md5($proxy['type'] . '://' . $proxy['host'] . ':' . $proxy['port']);
        }

        if (empty($proxy['name'])) {
            $proxy['name'] = strtoupper($proxy['type']) . ' ' . $proxy['host'] . ':' . $proxy['port'];
        }

        $this->proxies[] = $proxy;
        $this->config['proxies'] = $this->proxies;

        if (!$skipSave) {
            $this->saveConfig();
        }

        return ['success' => true, 'message' => '添加成功', 'id' => $proxy['id']];
    }

    /**
     * 批量添加代理（只写一次文件，性能优化）
     *
     * @param array $proxiesData 代理数据数组
     * @return array ['added' => int, 'duplicated' => int]
     */
    public function addProxiesBatch(array $proxiesData) {
        $added = 0;
        $duplicated = 0;

        foreach ($proxiesData as $proxyData) {
            $result = $this->addProxy($proxyData, true); // skipSave = true
            if ($result['success']) {
                $added++;
            } else {
                $duplicated++;
            }
        }

        // 只写一次文件
        if ($added > 0) {
            $this->config['proxies'] = $this->proxies;
            $this->saveConfig();
        }

        return ['added' => $added, 'duplicated' => $duplicated];
    }

    public function updateProxy($id, $proxyData) {
        foreach ($this->proxies as &$proxy) {
            if ($proxy['id'] === $id) {
                $proxy = array_merge($proxy, $proxyData);
                $proxy['id'] = $id;
                $this->config['proxies'] = $this->proxies;
                $this->saveConfig();
                return ['success' => true, 'message' => '更新成功'];
            }
        }
        return ['success' => false, 'message' => '代理不存在'];
    }

    public function deleteProxy($id) {
        $newProxies = [];
        $found = false;
        foreach ($this->proxies as $proxy) {
            if ($proxy['id'] !== $id) {
                $newProxies[] = $proxy;
            } else {
                $found = true;
            }
        }
        if ($found) {
            $this->proxies = $newProxies;
            $this->config['proxies'] = $this->proxies;
            $this->saveConfig();
            return ['success' => true, 'message' => '删除成功'];
        }
        return ['success' => false, 'message' => '代理不存在'];
    }

    public function getProxy() {
        if (!$this->isEnabled()) {
            return null;
        }

        $activeProxies = $this->getActiveProxies();
        if (empty($activeProxies)) {
            return null;
        }

        usort($activeProxies, function($a, $b) {
            $pa = $a['priority'] ?? 100;
            $pb = $b['priority'] ?? 100;
            if ($pa != $pb) return $pa - $pb;
            $fa = $a['fail_count'] ?? 0;
            $fb = $b['fail_count'] ?? 0;
            return $fa - $fb;
        });

        foreach ($activeProxies as $proxy) {
            if (!isset($this->failedProxies[$proxy['id']])) {
                return $proxy;
            }
        }

        $this->failedProxies = [];
        return reset($activeProxies);
    }

    public function markProxyFailed($proxyId) {
        $this->failedProxies[$proxyId] = time();
        foreach ($this->proxies as &$proxy) {
            if ($proxy['id'] === $proxyId) {
                $proxy['fail_count'] = ($proxy['fail_count'] ?? 0) + 1;
                $proxy['last_check'] = date('Y-m-d H:i:s');
                $this->config['proxies'] = $this->proxies;
                $this->saveConfig();
                break;
            }
        }
    }

    public function markProxySuccess($proxyId, $responseTime = 0) {
        unset($this->failedProxies[$proxyId]);
        foreach ($this->proxies as &$proxy) {
            if ($proxy['id'] === $proxyId) {
                $proxy['success_count'] = ($proxy['success_count'] ?? 0) + 1;
                $proxy['last_success'] = date('Y-m-d H:i:s');
                $proxy['last_check'] = date('Y-m-d H:i:s');
                if ($responseTime > 0) {
                    $proxy['response_time'] = $responseTime;
                }
                $this->config['proxies'] = $this->proxies;
                $this->saveConfig();
                break;
            }
        }
    }

    public function applyProxyToCurl($ch, $proxy = null) {
        if ($proxy === null) {
            $proxy = $this->getProxy();
        }
        if ($proxy === null) {
            return null;
        }

        $type = strtolower($proxy['type'] ?? 'http');
        $host = $proxy['host'];
        $port = $proxy['port'];

        if ($type === 'http') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        } elseif ($type === 'https') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTPS);
        } elseif ($type === 'socks5') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        } else {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        }

        curl_setopt($ch, CURLOPT_PROXY, $host . ':' . $port);

        if (!empty($proxy['username']) && !empty($proxy['password'])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['username'] . ':' . $proxy['password']);
        }

        return $proxy;
    }

    public function testProxy($proxyId) {
        $proxy = null;
        foreach ($this->proxies as $p) {
            if ($p['id'] === $proxyId) {
                $proxy = $p;
                break;
            }
        }
        if (!$proxy) {
            return ['success' => false, 'message' => '代理不存在'];
        }

        $result = $this->testProxyDirect($proxy);
        
        if ($result['success']) {
            $this->markProxySuccess($proxyId, $result['response_time']);
        } else {
            $this->markProxyFailed($proxyId);
        }
        
        return $result;
    }

    /**
     * 直接测试代理（不修改状态）
     */
    private function testProxyDirect($proxy) {
        $testUrl = 'http://www.baidu.com/'; // 使用百度作为测试URL，稳定可靠
        $timeout = $this->config['timeout'] ?? 8;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        $this->applyProxyToCurl($ch, $proxy);

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        // 百度返回 200 或 30x 都算成功
        $success = ($httpCode >= 200 && $httpCode < 400 && $response !== false);
        
        return [
            'success' => $success,
            'message' => $success ? '测试成功' : ($error ?: ('HTTP ' . $httpCode)),
            'response_time' => $responseTime,
            'http_code' => $httpCode
        ];
    }

    /**
     * 并发检测所有代理（使用 curl_multi）
     */
    public function checkAllProxies() {
        if (empty($this->proxies)) {
            return [];
        }

        $results = [];
        $batchSize = 10;
        $total = count($this->proxies);
        $testUrl = 'http://www.baidu.com/';
        $timeout = $this->config['timeout'] ?? 8;

        for ($offset = 0; $offset < $total; $offset += $batchSize) {
            $batch = array_slice($this->proxies, $offset, $batchSize, true);
            if (empty($batch)) break;

            $mh = curl_multi_init();
            $handles = [];
            $proxyMap = [];

            foreach ($batch as $idx => $proxy) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $testUrl,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => $timeout,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_NOSIGNAL       => 1,
                    CURLOPT_ENCODING       => 'gzip',
                ]);

                $type = strtolower($proxy['type'] ?? 'http');
                if ($type === 'socks5') {
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                } else {
                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                }
                curl_setopt($ch, CURLOPT_PROXY, $proxy['host'] . ':' . $proxy['port']);

                if (!empty($proxy['username']) && !empty($proxy['password'])) {
                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['username'] . ':' . $proxy['password']);
                }

                curl_multi_add_handle($mh, $ch);
                $handles[$idx] = $ch;
                $proxyMap[$idx] = $proxy;
            }

            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
                if ($mrc != CURLM_OK) break;
                if ($active > 0) {
                    curl_multi_select($mh, 0.3);
                }
            } while ($active > 0);

            foreach ($handles as $idx => $ch) {
                $proxy = $proxyMap[$idx];
                $startTime = microtime(true);
                $response = curl_multi_getcontent($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);

                $success = ($httpCode >= 200 && $httpCode < 400 && $response !== false);

                if ($success) {
                    $this->markProxySuccess($proxy['id'], $responseTime);
                } else {
                    $this->markProxyFailed($proxy['id']);
                }

                $results[] = [
                    'id' => $proxy['id'],
                    'name' => $proxy['name'],
                    'success' => $success,
                    'message' => $success ? '测试成功' : ($error ?: ('HTTP ' . $httpCode)),
                    'response_time' => $responseTime
                ];
            }
            curl_multi_close($mh);
        }

        $this->saveCheckState();
        return $results;
    }

    private function saveCheckState() {
        $state = [
            'last_check' => date('Y-m-d H:i:s'),
            'last_check_timestamp' => time(),
            'total_proxies' => count($this->proxies),
            'active_proxies' => count($this->getActiveProxies())
        ];
        $content = '<?php' . "\n";
        $content .= '// 代理检测状态' . "\n";
        $content .= 'return ' . var_export($state, true) . ';' . "\n";
        file_put_contents($this->lastCheckFile, $content);
    }

    public function getLastCheckState() {
        if (file_exists($this->lastCheckFile)) {
            return require $this->lastCheckFile;
        }
        return null;
    }

    public function shouldCheck() {
        $state = $this->getLastCheckState();
        if (!$state) return true;
        $interval = $this->config['check_interval'] ?? 300;
        return (time() - ($state['last_check_timestamp'] ?? 0)) >= $interval;
    }

    public function importProxies($text, $type = 'http') {
        $lines = preg_split('/[\r\n]+/', trim($text));
        $added = 0;
        $failed = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^(https?|socks5):\/\/(.+):(\d+)$/i', $line, $m)) {
                $proxyType = strtolower($m[1]);
                $host = $m[2];
                $port = intval($m[3]);
                $username = '';
                $password = '';
            } elseif (preg_match('/^(https?|socks5):\/\/(.+):(.+)@(.+):(\d+)$/i', $line, $m)) {
                $proxyType = strtolower($m[1]);
                $username = $m[2];
                $password = $m[3];
                $host = $m[4];
                $port = intval($m[5]);
            } elseif (preg_match('/^(.+):(\d+)$/', $line, $m)) {
                $proxyType = $type;
                $host = $m[1];
                $port = intval($m[2]);
                $username = '';
                $password = '';
            } else {
                $failed++;
                continue;
            }

            $result = $this->addProxy([
                'type' => $proxyType,
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password
            ]);

            if ($result['success']) {
                $added++;
            } else {
                $failed++;
            }
        }

        return ['success' => true, 'added' => $added, 'failed' => $failed];
    }

    public function getStats() {
        $total = count($this->proxies);
        $active = count($this->getActiveProxies());
        $totalSuccess = 0;
        $totalFail = 0;
        $avgResponseTime = 0;
        $withResponse = 0;

        foreach ($this->proxies as $proxy) {
            $totalSuccess += $proxy['success_count'] ?? 0;
            $totalFail += $proxy['fail_count'] ?? 0;
            if (!empty($proxy['response_time'])) {
                $avgResponseTime += $proxy['response_time'];
                $withResponse++;
            }
        }

        if ($withResponse > 0) {
            $avgResponseTime = round($avgResponseTime / $withResponse, 2);
        }

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'total_success' => $totalSuccess,
            'total_fail' => $totalFail,
            'avg_response_time' => $avgResponseTime,
            'enabled' => $this->isEnabled(),
            'auto_switch' => !empty($this->config['auto_switch'])
        ];
    }

    private function saveConfig() {
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * 代理池配置' . "\n";
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

    public function fetchProxiesFromWeb($verify = true, $maxPerSource = 20) {
        require_once __DIR__ . '/ProxyFetcher.php';

        $fetcher = new ProxyFetcher([
            'timeout' => $this->config['timeout'] ?? 6,
            'connect_timeout' => 3,
            'verify_timeout' => 4,
            'max_per_source' => $maxPerSource,
            'cache_ttl' => 120,
        ]);

        $result = $fetcher->fetchAll($verify);
        $fromCache = !empty($result['from_cache']);

        // 批量添加（只写一次文件）
        $batchData = [];
        foreach ($result['proxies'] as $proxy) {
            $batchData[] = [
                'type' => $proxy['type'],
                'host' => $proxy['host'],
                'port' => $proxy['port'],
                'username' => $proxy['username'] ?? '',
                'password' => $proxy['password'] ?? '',
                'response_time' => $proxy['response_time'] ?? 0,
                'status' => 'active'
            ];
        }
        $batchResult = $this->addProxiesBatch($batchData);
        $added = $batchResult['added'];

        // 获取到代理后自动启用代理池
        if ($added > 0 && empty($this->config['enabled'])) {
            $this->config['enabled'] = true;
            $this->saveConfig();
        }

        $cacheNote = $fromCache ? '（来自缓存）' : '';
        return [
            'success' => true,
            'added' => $added,
            'duplicated' => $batchResult['duplicated'],
            'total_fetched' => $result['total'],
            'sources' => $result['sources'],
            'from_cache' => $fromCache,
            'auto_enabled' => $added > 0,
            'message' => "成功获取并添加 {$added} 个可用代理{$cacheNote}"
        ];
    }

    /**
     * 快速同步代理池（不验证，直接导入）
     */
    public function syncProxiesFast($maxPerSource = 20) {
        require_once __DIR__ . '/ProxyFetcher.php';

        $fetcher = new ProxyFetcher([
            'timeout' => 6,
            'connect_timeout' => 3,
            'max_per_source' => $maxPerSource,
            'cache_ttl' => 120,
        ]);

        $result = $fetcher->fetchAll(false);
        $fromCache = !empty($result['from_cache']);

        // 批量添加（只写一次文件）
        $batchData = [];
        foreach ($result['proxies'] as $proxy) {
            $batchData[] = [
                'type' => $proxy['type'],
                'host' => $proxy['host'],
                'port' => $proxy['port'],
                'username' => $proxy['username'] ?? '',
                'password' => $proxy['password'] ?? '',
                'response_time' => 0,
                'status' => 'active'
            ];
        }
        $batchResult = $this->addProxiesBatch($batchData);
        $added = $batchResult['added'];

        // 获取到代理后自动启用代理池
        if ($added > 0 && empty($this->config['enabled'])) {
            $this->config['enabled'] = true;
            $this->saveConfig();
        }

        $cacheNote = $fromCache ? '（来自缓存）' : '';
        return [
            'success' => true,
            'added' => $added,
            'duplicated' => $batchResult['duplicated'],
            'total_fetched' => $result['total'],
            'sources' => $result['sources'],
            'from_cache' => $fromCache,
            'auto_enabled' => $added > 0,
            'message' => "快速同步完成，共导入 {$added} 个代理{$cacheNote}"
        ];
    }

    /**
     * 清除代理获取缓存
     *
     * @return array
     */
    public function clearFetchCache() {
        require_once __DIR__ . '/ProxyFetcher.php';
        $fetcher = new ProxyFetcher();
        $result = $fetcher->clearCache();
        return [
            'success' => true,
            'cleared' => $result,
            'message' => $result ? '缓存已清除' : '无缓存可清除'
        ];
    }

    public function clearInactiveProxies() {
        $activeProxies = [];
        $cleared = 0;
        foreach ($this->proxies as $proxy) {
            if (($proxy['status'] ?? 'active') === 'active') {
                $activeProxies[] = $proxy;
            } else {
                $cleared++;
            }
        }
        $this->proxies = $activeProxies;
        $this->config['proxies'] = $this->proxies;
        $this->saveConfig();
        return ['success' => true, 'cleared' => $cleared];
    }

    public function clearAllProxies() {
        $count = count($this->proxies);
        $this->proxies = [];
        $this->config['proxies'] = [];
        $this->saveConfig();
        return ['success' => true, 'cleared' => $count];
    }
}
