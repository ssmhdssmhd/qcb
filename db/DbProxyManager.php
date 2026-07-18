<?php
/**
 * 数据库版代理池管理器
 * 使用数据库存储代理配置和代理列表，完全兼容 ProxyManager 接口
 */

require_once __DIR__ . '/Database.php';

class DbProxyManager {
    private $db;
    private $config;
    private $failedProxies = [];
    private $lastCheckFile;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->lastCheckFile = __DIR__ . '/proxy_check_state.php';
        $this->ensureTables();
        $this->loadConfig();
    }

    private function ensureTables() {
        if (!$this->db->tableExists('proxies')) {
            $this->db->initTables();
        }
        if (!$this->db->tableExists('sys_config')) {
            $this->db->initTables();
        }
    }

    private function loadConfig() {
        $row = $this->db->queryOne(
            'SELECT config_value FROM sys_config WHERE config_key = ?',
            ['proxy_config']
        );
        if ($row && !empty($row['config_value'])) {
            $this->config = json_decode($row['config_value'], true);
            if (!is_array($this->config)) {
                $this->config = $this->getDefaultConfig();
            }
        } else {
            $this->config = $this->getDefaultConfig();
            $this->saveConfigToDb();
        }
    }

    private function getDefaultConfig() {
        return [
            'version' => '1.0',
            'enabled' => false,
            'auto_switch' => true,
            'check_interval' => 300,
            'timeout' => 10
        ];
    }

    private function saveConfigToDb() {
        $configValue = json_encode($this->config, JSON_UNESCAPED_UNICODE);
        $existing = $this->db->queryOne(
            'SELECT id FROM sys_config WHERE config_key = ?',
            ['proxy_config']
        );
        if ($existing) {
            $this->db->update(
                'sys_config',
                ['config_value' => $configValue],
                'config_key = ?',
                ['proxy_config']
            );
        } else {
            $this->db->insert('sys_config', [
                'config_key' => 'proxy_config',
                'config_value' => $configValue,
                'description' => '代理配置'
            ]);
        }
    }

    public function isEnabled() {
        $proxies = $this->getAllProxies();
        return !empty($this->config['enabled']) && !empty($proxies);
    }

    public function setEnabled($enabled) {
        $this->config['enabled'] = (bool)$enabled;
        $this->saveConfigToDb();
        return ['success' => true, 'message' => '已更新'];
    }

    public function setAutoSwitch($autoSwitch) {
        $this->config['auto_switch'] = (bool)$autoSwitch;
        $this->saveConfigToDb();
        return ['success' => true, 'message' => '已更新'];
    }

    public function getConfig() {
        return $this->config;
    }

    public function saveConfig($config) {
        $this->config = array_merge($this->config, $config);
        $this->saveConfigToDb();
        return ['success' => true, 'message' => '配置已保存'];
    }

    private function rowToProxy($row) {
        if (!$row) return null;
        return [
            'id' => $row['proxy_id'],
            'proxy_id' => $row['proxy_id'],
            'name' => $row['name'] ?? '',
            'type' => $row['type'] ?? 'http',
            'host' => $row['host'] ?? '',
            'port' => (int)($row['port'] ?? 0),
            'username' => $row['username'] ?? '',
            'password' => $row['password'] ?? '',
            'status' => $row['status'] ?? 'active',
            'priority' => (int)($row['priority'] ?? 100),
            'success_count' => (int)($row['success_count'] ?? 0),
            'fail_count' => (int)($row['fail_count'] ?? 0),
            'last_check' => $row['last_check'] ?? null,
            'last_success' => $row['last_success'] ?? null,
            'response_time' => (int)($row['response_time'] ?? 0),
            'source' => $row['source'] ?? 'manual'
        ];
    }

    public function getAllProxies() {
        $rows = $this->db->query('SELECT * FROM proxies ORDER BY priority ASC, id ASC');
        $proxies = [];
        foreach ($rows as $row) {
            $proxies[] = $this->rowToProxy($row);
        }
        return $proxies;
    }

    public function getActiveProxies() {
        $rows = $this->db->query(
            'SELECT * FROM proxies WHERE status = ? ORDER BY priority ASC, id ASC',
            ['active']
        );
        $proxies = [];
        foreach ($rows as $row) {
            $proxies[] = $this->rowToProxy($row);
        }
        return $proxies;
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
            'response_time' => 0,
            'source' => 'manual'
        ], $proxyData);

        if (empty($proxy['host']) || empty($proxy['port'])) {
            return ['success' => false, 'message' => '主机和端口不能为空'];
        }

        if (empty($proxy['id'])) {
            $proxy['id'] = md5($proxy['type'] . '://' . $proxy['host'] . ':' . $proxy['port'] . '_' . time());
        }

        $proxyId = $proxy['id'];

        $existing = $this->db->queryOne(
            'SELECT id FROM proxies WHERE proxy_id = ?',
            [$proxyId]
        );
        if ($existing) {
            return ['success' => false, 'message' => '代理已存在'];
        }

        if (empty($proxy['name'])) {
            $proxy['name'] = strtoupper($proxy['type']) . ' ' . $proxy['host'] . ':' . $proxy['port'];
        }

        $this->db->insert('proxies', [
            'proxy_id' => $proxyId,
            'name' => $proxy['name'],
            'type' => $proxy['type'],
            'host' => $proxy['host'],
            'port' => $proxy['port'],
            'username' => $proxy['username'],
            'password' => $proxy['password'],
            'status' => $proxy['status'],
            'priority' => $proxy['priority'],
            'success_count' => $proxy['success_count'],
            'fail_count' => $proxy['fail_count'],
            'last_check' => $proxy['last_check'],
            'last_success' => $proxy['last_success'],
            'response_time' => $proxy['response_time'],
            'source' => $proxy['source']
        ]);

        return ['success' => true, 'message' => '添加成功', 'id' => $proxyId];
    }

    public function updateProxy($id, $proxyData) {
        $existing = $this->db->queryOne(
            'SELECT * FROM proxies WHERE proxy_id = ?',
            [$id]
        );
        if (!$existing) {
            return ['success' => false, 'message' => '代理不存在'];
        }

        $updateData = [];
        $allowedFields = ['name', 'type', 'host', 'port', 'username', 'password', 'status', 'priority', 'success_count', 'fail_count', 'last_check', 'last_success', 'response_time', 'source'];

        foreach ($allowedFields as $field) {
            if (isset($proxyData[$field])) {
                $updateData[$field] = $proxyData[$field];
            }
        }

        if (isset($proxyData['id'])) {
            unset($proxyData['id']);
        }

        if (!empty($updateData)) {
            $this->db->update(
                'proxies',
                $updateData,
                'proxy_id = ?',
                [$id]
            );
        }

        return ['success' => true, 'message' => '更新成功'];
    }

    public function deleteProxy($id) {
        $existing = $this->db->queryOne(
            'SELECT id FROM proxies WHERE proxy_id = ?',
            [$id]
        );
        if (!$existing) {
            return ['success' => false, 'message' => '代理不存在'];
        }

        $this->db->delete('proxies', 'proxy_id = ?', [$id]);
        return ['success' => true, 'message' => '删除成功'];
    }

    public function clearInactiveProxies() {
        $count = $this->db->count('proxies', 'status != ?', ['active']);
        $this->db->delete('proxies', 'status != ?', ['active']);
        return ['success' => true, 'cleared' => $count];
    }

    public function clearAllProxies() {
        $count = $this->db->count('proxies');
        $this->db->execute('DELETE FROM proxies');
        return ['success' => true, 'cleared' => $count];
    }

    public function recordResult($proxyId, $success, $responseTime = 0) {
        if ($success) {
            $this->markProxySuccess($proxyId, $responseTime);
        } else {
            $this->markProxyFailed($proxyId);
        }
    }

    public function getRandomProxy() {
        if (!$this->isEnabled()) {
            return null;
        }

        $activeProxies = $this->getActiveProxies();
        if (empty($activeProxies)) {
            return null;
        }

        $available = [];
        foreach ($activeProxies as $proxy) {
            if (!isset($this->failedProxies[$proxy['id']])) {
                $available[] = $proxy;
            }
        }

        if (empty($available)) {
            $this->failedProxies = [];
            $available = $activeProxies;
        }

        if (empty($available)) {
            return null;
        }

        return $available[array_rand($available)];
    }

    public function getProxy() {
        if (!$this->isEnabled()) {
            return null;
        }

        $activeProxies = $this->getActiveProxies();
        if (empty($activeProxies)) {
            return null;
        }

        // 按响应时间从快到慢排序（速度越快越优先）
        usort($activeProxies, function($a, $b) {
            $ta = $a['response_time'] ?? 0;
            $tb = $b['response_time'] ?? 0;
            // 有响应时间的排在前面
            if ($ta > 0 && $tb <= 0) return -1;
            if ($ta <= 0 && $tb > 0) return 1;
            // 都有响应时间，按快到慢排序
            if ($ta > 0 && $tb > 0) return $ta - $tb;
            // 都没有响应时间，按失败次数少的优先
            $fa = $a['fail_count'] ?? 0;
            $fb = $b['fail_count'] ?? 0;
            if ($fa != $fb) return $fa - $fb;
            // 最后按优先级
            return ($a['priority'] ?? 100) - ($b['priority'] ?? 100);
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
        $existing = $this->db->queryOne(
            'SELECT fail_count FROM proxies WHERE proxy_id = ?',
            [$proxyId]
        );
        if ($existing) {
            $this->db->update(
                'proxies',
                [
                    'fail_count' => (int)$existing['fail_count'] + 1,
                    'last_check' => date('Y-m-d H:i:s')
                ],
                'proxy_id = ?',
                [$proxyId]
            );
        }
    }

    public function markProxySuccess($proxyId, $responseTime = 0) {
        unset($this->failedProxies[$proxyId]);
        $existing = $this->db->queryOne(
            'SELECT success_count, response_time FROM proxies WHERE proxy_id = ?',
            [$proxyId]
        );
        if ($existing) {
            $updateData = [
                'success_count' => (int)$existing['success_count'] + 1,
                'last_success' => date('Y-m-d H:i:s'),
                'last_check' => date('Y-m-d H:i:s')
            ];
            if ($responseTime > 0) {
                $updateData['response_time'] = (int)$responseTime;
            }
            $this->db->update('proxies', $updateData, 'proxy_id = ?', [$proxyId]);
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
        $all = $this->getAllProxies();
        foreach ($all as $p) {
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
        $proxies = $this->getAllProxies();
        foreach ($proxies as $proxy) {
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
            'total_proxies' => count($this->getAllProxies()),
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
        $allProxies = $this->getAllProxies();
        $total = count($allProxies);
        $active = count($this->getActiveProxies());
        $totalSuccess = 0;
        $totalFail = 0;
        $avgResponseTime = 0;
        $withResponse = 0;

        foreach ($allProxies as $proxy) {
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

    public function fetchProxiesFromWeb($verify = true, $maxPerSource = 20) {
        $proxyFetcherFile = __DIR__ . '/../proxy/ProxyFetcher.php';
        if (!file_exists($proxyFetcherFile)) {
            return [
                'success' => false,
                'added' => 0,
                'total_fetched' => 0,
                'sources' => [],
                'message' => 'ProxyFetcher 类不存在'
            ];
        }

        require_once $proxyFetcherFile;

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
                'status' => 'active',
                'source' => 'web_fetch'
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
}
