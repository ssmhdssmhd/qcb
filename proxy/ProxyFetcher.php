<?php
/**
 * 免费代理获取器
 * 从多个公开代理API获取代理IP
 * 支持自动验证、去重、批量导入
 *
 * v2.0 优化：
 *   - curl_multi 并发请求所有代理源（原来串行，现在并发）
 *   - 并发验证代理可用性（原来串行，现在并发）
 *   - 本地缓存机制，避免频繁请求 proxy.scdn.io
 *   - 降低超时时间，快速失败
 */

class ProxyFetcher {
    private $timeout = 6;
    private $connectTimeout = 3;
    private $maxPerSource = 15;
    private $verifyTimeout = 4;
    private $maxVerifyCount = 20;
    private $fastFail = true;
    private $sources = [];
    private $cacheFile;
    private $cacheTtl = 120; // 缓存 2 分钟

    public function __construct($options = []) {
        $this->timeout = $options['timeout'] ?? 6;
        $this->connectTimeout = $options['connect_timeout'] ?? 3;
        $this->maxPerSource = $options['max_per_source'] ?? 15;
        $this->verifyTimeout = $options['verify_timeout'] ?? 4;
        $this->maxVerifyCount = $options['max_verify_count'] ?? 20;
        $this->fastFail = $options['fast_fail'] ?? true;
        $this->cacheTtl = $options['cache_ttl'] ?? 120;
        $this->cacheFile = ($options['cache_file'] ?? __DIR__ . '/cache/proxy_fetch_cache.json');
        $this->initSources();
    }

    private function initSources() {
        // proxy.scdn.io 优先级最高（放最前面）
        $this->sources = [
            [
                'name' => 'proxy.scdn.io',
                'url' => 'https://proxy.scdn.io/api/get_proxy.php?protocol=http&count=20',
                'type' => 'json_scdn',
                'enabled' => true,
                'priority' => 1
            ],
            [
                'name' => 'proxy.scdn.io-https',
                'url' => 'https://proxy.scdn.io/api/get_proxy.php?protocol=https&count=20',
                'type' => 'json_scdn',
                'enabled' => true,
                'priority' => 2
            ],
            [
                'name' => 'ProxyScrape',
                'url' => 'https://api.proxyscrape.com/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=all&anonymity=all',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'ProxyList-download',
                'url' => 'https://www.proxy-list.download/api/v1/get?type=http',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'OpenProxyList',
                'url' => 'https://openproxylist.xyz/http.txt',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'ProxySpace',
                'url' => 'https://proxyspace.pro/http.txt',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'RawGithub-sunny9577',
                'url' => 'https://raw.githubusercontent.com/sunny9577/proxy-scraper/master/proxies.txt',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'RawGithub-TheSpeedX',
                'url' => 'https://raw.githubusercontent.com/TheSpeedX/PROXY-List/master/http.txt',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'RawGithub-ShiftyTR',
                'url' => 'https://raw.githubusercontent.com/ShiftyTR/Proxy-List/master/http.txt',
                'type' => 'plain',
                'enabled' => true
            ],
            [
                'name' => 'RawGithub-hookzof',
                'url' => 'https://raw.githubusercontent.com/hookzof/socks5_list/master/proxy.txt',
                'type' => 'plain_socks',
                'enabled' => true
            ],
            [
                'name' => 'ProxyScan-API',
                'url' => 'https://www.proxyscan.io/api/proxy?format=json&type=http&limit=20',
                'type' => 'json_proxyscan',
                'enabled' => true
            ],
            [
                'name' => 'PubProxy',
                'url' => 'http://pubproxy.com/api/proxy?limit=20&format=json&type=http',
                'type' => 'json_pubproxy',
                'enabled' => true
            ]
        ];
    }

    public function getSources() {
        return $this->sources;
    }

    /**
     * 从本地缓存加载代理（如果缓存未过期）
     *
     * @return array|null 缓存的代理列表，无缓存或已过期返回 null
     */
    private function loadCache() {
        if (!file_exists($this->cacheFile)) {
            return null;
        }
        $data = @json_decode(@file_get_contents($this->cacheFile), true);
        if (!$data || !isset($data['proxies']) || !isset($data['expires_at'])) {
            return null;
        }
        if (time() > $data['expires_at']) {
            return null;
        }
        return $data;
    }

    /**
     * 保存代理到本地缓存
     *
     * @param array $proxies 代理列表
     * @param array $sources 源结果
     */
    private function saveCache($proxies, $sources) {
        $cacheDir = dirname($this->cacheFile);
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $data = [
            'proxies' => $proxies,
            'sources' => $sources,
            'expires_at' => time() + $this->cacheTtl,
            'created_at' => time(),
        ];
        @file_put_contents(
            $this->cacheFile,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    /**
     * 并发获取所有代理源（使用 curl_multi）
     *
     * @param bool $verify 是否验证代理可用性
     * @return array
     */
    public function fetchAll($verify = true) {
        // 优先从缓存读取
        $cached = $this->loadCache();
        if ($cached !== null) {
            return [
                'total' => count($cached['proxies']),
                'proxies' => $cached['proxies'],
                'sources' => $cached['sources'],
                'from_cache' => true,
            ];
        }

        $allProxies = [];
        $results = [];

        // ========== 并发请求所有代理源 ==========
        $enabledSources = array_filter($this->sources, function($s) {
            return !empty($s['enabled']);
        });

        $mh = curl_multi_init();
        $handles = [];
        $sourceMap = [];

        foreach ($enabledSources as $idx => $source) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $source['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => $this->timeout,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
                CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_ENCODING       => 'gzip, deflate',
                CURLOPT_NOSIGNAL       => 1,
                CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            ]);
            curl_multi_add_handle($mh, $ch);
            $handles[$idx] = $ch;
            $sourceMap[$idx] = $source;
        }

        // 执行并发请求
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            if ($mrc != CURLM_OK) break;
            if ($active > 0) {
                curl_multi_select($mh, 0.5); // 等待 0.5 秒
            }
        } while ($active > 0);

        // 收集所有响应
        foreach ($handles as $idx => $ch) {
            $source = $sourceMap[$idx];
            $response = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if ($error || $httpCode != 200 || !$response) {
                $results[] = [
                    'source' => $source['name'],
                    'count' => 0,
                    'success' => false,
                    'message' => $error ?: ('HTTP ' . $httpCode),
                ];
                continue;
            }

            $proxies = $this->parseResponse($response, $source);

            if (!empty($proxies)) {
                if (count($proxies) > $this->maxPerSource) {
                    shuffle($proxies);
                    $proxies = array_slice($proxies, 0, $this->maxPerSource);
                }
                $results[] = [
                    'source' => $source['name'],
                    'count' => count($proxies),
                    'success' => true,
                ];
                $allProxies = array_merge($allProxies, $proxies);
            } else {
                $results[] = [
                    'source' => $source['name'],
                    'count' => 0,
                    'success' => false,
                    'message' => '未获取到代理',
                ];
            }
        }
        curl_multi_close($mh);

        // 去重
        $allProxies = $this->deduplicate($allProxies);

        // 并发验证代理
        if ($verify && !empty($allProxies)) {
            $allProxies = $this->verifyProxiesConcurrent($allProxies);
        }

        // 保存缓存
        if (!empty($allProxies)) {
            $this->saveCache($allProxies, $results);
        }

        return [
            'total' => count($allProxies),
            'proxies' => $allProxies,
            'sources' => $results,
            'from_cache' => false,
        ];
    }

    /**
     * 解析响应内容
     */
    private function parseResponse($response, $source) {
        $type = $source['type'] ?? 'plain';
        switch ($type) {
            case 'plain':
                return $this->parsePlainList($response, 'http');
            case 'plain_socks':
                return $this->parsePlainList($response, 'socks5');
            case 'json_proxyscan':
                return $this->parseProxyscanJson($response);
            case 'json_pubproxy':
                return $this->parsePubproxyJson($response);
            case 'json_scdn':
                return $this->parseScdnJson($response, $source);
            default:
                return $this->parsePlainList($response, 'http');
        }
    }

    /**
     * 兼容方法：从单个源获取代理（串行，保留向后兼容）
     */
    public function fetchFromSource($source) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $source['url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => 'gzip, deflate',
            CURLOPT_NOSIGNAL       => 1,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode != 200 || !$response) {
            return [];
        }

        $proxies = $this->parseResponse($response, $source);

        if (count($proxies) > $this->maxPerSource) {
            shuffle($proxies);
            $proxies = array_slice($proxies, 0, $this->maxPerSource);
        }

        return $proxies;
    }

    private function parsePlainList($content, $defaultType = 'http') {
        $proxies = [];
        $lines = preg_split('/[\r\n]+/', trim($content));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})$/', $line, $m)) {
                $proxies[] = [
                    'type' => $defaultType,
                    'host' => $m[1],
                    'port' => intval($m[2]),
                    'username' => '',
                    'password' => ''
                ];
            }
            elseif (preg_match('/^(https?|socks5):\/\/(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})$/i', $line, $m)) {
                $proxies[] = [
                    'type' => strtolower($m[1]),
                    'host' => $m[2],
                    'port' => intval($m[3]),
                    'username' => '',
                    'password' => ''
                ];
            }
            elseif (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})\s+(\d{1,5})/', $line, $m)) {
                $proxies[] = [
                    'type' => $defaultType,
                    'host' => $m[1],
                    'port' => intval($m[2]),
                    'username' => '',
                    'password' => ''
                ];
            }
        }

        return $proxies;
    }

    private function parseProxyscanJson($content) {
        $proxies = [];
        $data = json_decode($content, true);

        if (!is_array($data)) return $proxies;

        foreach ($data as $item) {
            $host = $item['Ip'] ?? $item['ip'] ?? '';
            $port = $item['Port'] ?? $item['port'] ?? 0;
            $type = $item['Type'] ?? $item['type'] ?? 'http';

            if (is_array($type)) {
                $type = $type[0] ?? 'http';
            }

            $type = strtolower($type);
            if ($type === 'https') $type = 'http';

            if ($host && $port && filter_var($host, FILTER_VALIDATE_IP)) {
                $proxies[] = [
                    'type' => $type,
                    'host' => $host,
                    'port' => intval($port),
                    'username' => '',
                    'password' => ''
                ];
            }
        }

        return $proxies;
    }

    private function parsePubproxyJson($content) {
        $proxies = [];
        $data = json_decode($content, true);

        if (!isset($data['data']) || !is_array($data['data'])) return $proxies;

        foreach ($data['data'] as $item) {
            $host = $item['ip'] ?? '';
            $port = $item['port'] ?? 0;
            $type = $item['type'] ?? 'http';
            $type = strtolower($type);
            if ($type === 'https') $type = 'http';

            if ($host && $port && filter_var($host, FILTER_VALIDATE_IP)) {
                $proxies[] = [
                    'type' => $type,
                    'host' => $host,
                    'port' => intval($port),
                    'username' => '',
                    'password' => ''
                ];
            }
        }

        return $proxies;
    }

    private function parseScdnJson($content, $source = []) {
        $proxies = [];
        $data = json_decode($content, true);

        if (!isset($data['code']) || $data['code'] != 200) return $proxies;
        if (!isset($data['data']['proxies']) || !is_array($data['data']['proxies'])) return $proxies;

        $url = $source['url'] ?? '';
        $defaultType = 'http';
        if (stripos($url, 'protocol=https') !== false) {
            $defaultType = 'http';
        }

        foreach ($data['data']['proxies'] as $proxyStr) {
            if (preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})$/', trim($proxyStr), $m)) {
                $proxies[] = [
                    'type' => $defaultType,
                    'host' => $m[1],
                    'port' => intval($m[2]),
                    'username' => '',
                    'password' => ''
                ];
            }
        }

        return $proxies;
    }

    private function deduplicate($proxies) {
        $seen = [];
        $unique = [];

        foreach ($proxies as $proxy) {
            $key = $proxy['type'] . '://' . $proxy['host'] . ':' . $proxy['port'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $proxy;
            }
        }

        return $unique;
    }

    /**
     * 并发验证代理可用性（使用 curl_multi）
     *
     * 同时验证多个代理，大幅提升验证速度
     *
     * @param array  $proxies  代理列表
     * @param string $testUrl  测试 URL
     * @param int    $timeout  单个代理超时时间
     * @return array 验证通过的代理列表
     */
    public function verifyProxiesConcurrent($proxies, $testUrl = 'https://httpbin.org/get', $timeout = null) {
        if ($timeout === null) {
            $timeout = $this->verifyTimeout;
        }

        if (empty($proxies)) return [];

        $total = count($proxies);
        $testCount = min($total, $this->maxVerifyCount);

        // 限制并发数，避免过多连接
        $batchSize = min(10, $testCount);
        $verified = [];

        // 分批并发验证
        for ($offset = 0; $offset < $testCount; $offset += $batchSize) {
            $batch = array_slice($proxies, $offset, $batchSize);
            if (empty($batch)) break;

            $mh = curl_multi_init();
            $handles = [];

            foreach ($batch as $i => $proxy) {
                $ch = $this->createVerifyHandle($proxy, $testUrl, $timeout);
                if ($ch) {
                    curl_multi_add_handle($mh, $ch);
                    $handles[$i] = $ch;
                }
            }

            // 执行并发请求
            $active = null;
            do {
                $mrc = curl_multi_exec($mh, $active);
                if ($mrc != CURLM_OK) break;
                if ($active > 0) {
                    curl_multi_select($mh, 0.3);
                }
            } while ($active > 0);

            // 收集验证结果
            foreach ($handles as $i => $ch) {
                $proxy = $batch[$i];
                $startTime = microtime(true);
                $response = curl_multi_getcontent($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);

                if ($httpCode == 200 && $response !== false) {
                    $proxy['response_time'] = $responseTime;
                    $proxy['status'] = 'active';
                    $verified[] = $proxy;
                    if (count($verified) >= 10) {
                        break 2;
                    }
                }
            }
            curl_multi_close($mh);

            if (count($verified) >= 10) break;
        }

        // 如果验证通过的不够，再验证更多
        if (count($verified) < 5 && $testCount < $total) {
            $remaining = array_slice($proxies, $testCount, 20);
            $batchSize = min(10, count($remaining));

            for ($offset = 0; $offset < count($remaining); $offset += $batchSize) {
                $batch = array_slice($remaining, $offset, $batchSize);
                if (empty($batch)) break;

                $mh = curl_multi_init();
                $handles = [];

                foreach ($batch as $i => $proxy) {
                    $ch = $this->createVerifyHandle($proxy, $testUrl, $timeout);
                    if ($ch) {
                        curl_multi_add_handle($mh, $ch);
                        $handles[$i] = $ch;
                    }
                }

                $active = null;
                do {
                    $mrc = curl_multi_exec($mh, $active);
                    if ($mrc != CURLM_OK) break;
                    if ($active > 0) {
                        curl_multi_select($mh, 0.3);
                    }
                } while ($active > 0);

                foreach ($handles as $i => $ch) {
                    $proxy = $batch[$i];
                    $startTime = microtime(true);
                    $response = curl_multi_getcontent($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_multi_remove_handle($mh, $ch);
                    curl_close($ch);
                    $responseTime = round((microtime(true) - $startTime) * 1000, 2);

                    if ($httpCode == 200 && $response !== false) {
                        $proxy['response_time'] = $responseTime;
                        $proxy['status'] = 'active';
                        $verified[] = $proxy;
                        if (count($verified) >= 10) break 2;
                    }
                }
                curl_multi_close($mh);
            }
        }

        // 按响应时间排序
        usort($verified, function($a, $b) {
            return ($a['response_time'] ?? 9999) <=> ($b['response_time'] ?? 9999);
        });

        return $verified;
    }

    /**
     * 创建代理验证的 cURL 句柄
     */
    private function createVerifyHandle($proxy, $testUrl, $timeout) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $testUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => min($timeout, 3),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_NOSIGNAL       => 1,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_ENCODING       => 'gzip',
        ]);

        $type = strtolower($proxy['type'] ?? 'http');
        if ($type === 'http') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        } elseif ($type === 'socks5') {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        }
        curl_setopt($ch, CURLOPT_PROXY, $proxy['host'] . ':' . $proxy['port']);

        if (!empty($proxy['username']) && !empty($proxy['password'])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['username'] . ':' . $proxy['password']);
        }

        return $ch;
    }

    /**
     * 串行验证代理（保留向后兼容，内部调用并发版本）
     */
    public function verifyProxies($proxies, $testUrl = 'https://httpbin.org/get', $timeout = null) {
        return $this->verifyProxiesConcurrent($proxies, $testUrl, $timeout);
    }

    /**
     * 测试单个代理（保留向后兼容）
     */
    private function testSingleProxy($proxy, $testUrl, $timeout) {
        $ch = $this->createVerifyHandle($proxy, $testUrl, $timeout);

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return [
            'success' => ($httpCode == 200 && $response !== false),
            'response_time' => $responseTime,
            'http_code' => $httpCode,
            'error' => $error
        ];
    }

    /**
     * 清除缓存
     */
    public function clearCache() {
        if (file_exists($this->cacheFile)) {
            @unlink($this->cacheFile);
            return true;
        }
        return false;
    }
}
