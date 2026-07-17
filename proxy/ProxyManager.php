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

    public function addProxy($proxyData) {
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

        if (empty($proxy['id'])) {
            $proxy['id'] = md5($proxy['type'] . '://' . $proxy['host'] . ':' . $proxy['port'] . '_' . time());
        }

        if (empty($proxy['name'])) {
            $proxy['name'] = strtoupper($proxy['type']) . ' ' . $proxy['host'] . ':' . $proxy['port'];
        }

        $this->proxies[] = $proxy;
        $this->config['proxies'] = $this->proxies;
        $this->saveConfig();

        return ['success' => true, 'message' => '添加成功', 'id' => $proxy['id']];
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

        $failedCount = count($this->failedProxies);
        if ($failedCount >= count($activeProxies)) {
            $this->failedProxies = [];
            return reset($activeProxies);
        }

        return null;
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

        $testUrl = 'https://httpbin.org/get';
        $timeout = $this->config['timeout'] ?? 10;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

        $this->applyProxyToCurl($ch, $proxy);

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        if ($httpCode == 200 && $response !== false) {
            $this->markProxySuccess($proxyId, $responseTime);
            return [
                'success' => true,
                'message' => '测试成功',
                'response_time' => $responseTime,
                'http_code' => $httpCode
            ];
        } else {
            $this->markProxyFailed($proxyId);
            return [
                'success' => false,
                'message' => $error ? $error : ('HTTP ' . $httpCode),
                'response_time' => $responseTime,
                'http_code' => $httpCode
            ];
        }
    }

    public function checkAllProxies() {
        $results = [];
        foreach ($this->proxies as $proxy) {
            $result = $this->testProxy($proxy['id']);
            $results[] = [
                'id' => $proxy['id'],
                'name' => $proxy['name'],
                'success' => $result['success'],
                'message' => $result['message'],
                'response_time' => $result['response_time']
            ];
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
            'timeout' => $this->config['timeout'] ?? 10,
            'max_per_source' => $maxPerSource
        ]);

        $result = $fetcher->fetchAll($verify);
        $added = 0;

        foreach ($result['proxies'] as $proxy) {
            $addResult = $this->addProxy([
                'type' => $proxy['type'],
                'host' => $proxy['host'],
                'port' => $proxy['port'],
                'username' => $proxy['username'] ?? '',
                'password' => $proxy['password'] ?? '',
                'response_time' => $proxy['response_time'] ?? 0,
                'status' => $verify ? 'active' : 'active'
            ]);
            if ($addResult['success']) {
                $added++;
            }
        }

        return [
            'success' => true,
            'added' => $added,
            'total_fetched' => $result['total'],
            'sources' => $result['sources'],
            'message' => "成功获取并添加 {$added} 个可用代理"
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
