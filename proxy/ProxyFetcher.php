<?php
/**
 * 免费代理获取器
 * 从多个公开代理API获取代理IP
 * 支持自动验证、去重、批量导入
 */

class ProxyFetcher {
    private $timeout = 8;
    private $connectTimeout = 5;
    private $maxPerSource = 15;
    private $verifyTimeout = 5;
    private $maxVerifyCount = 15;
    private $fastFail = true;
    private $sources = [];

    public function __construct($options = []) {
        $this->timeout = $options['timeout'] ?? 8;
        $this->connectTimeout = $options['connect_timeout'] ?? 5;
        $this->maxPerSource = $options['max_per_source'] ?? 15;
        $this->verifyTimeout = $options['verify_timeout'] ?? 5;
        $this->maxVerifyCount = $options['max_verify_count'] ?? 15;
        $this->fastFail = $options['fast_fail'] ?? true;
        $this->initSources();
    }

    private function initSources() {
        $this->sources = [
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

    public function fetchAll($verify = true) {
        $allProxies = [];
        $results = [];

        foreach ($this->sources as $source) {
            if (empty($source['enabled'])) continue;

            try {
                $proxies = $this->fetchFromSource($source);
                if (!empty($proxies)) {
                    $results[] = [
                        'source' => $source['name'],
                        'count' => count($proxies),
                        'success' => true
                    ];
                    $allProxies = array_merge($allProxies, $proxies);
                } else {
                    $results[] = [
                        'source' => $source['name'],
                        'count' => 0,
                        'success' => false,
                        'message' => '未获取到代理'
                    ];
                }
            } catch (Exception $e) {
                $results[] = [
                    'source' => $source['name'],
                    'count' => 0,
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        $allProxies = $this->deduplicate($allProxies);

        if ($verify && !empty($allProxies)) {
            $allProxies = $this->verifyProxies($allProxies);
        }

        return [
            'total' => count($allProxies),
            'proxies' => $allProxies,
            'sources' => $results
        ];
    }

    public function fetchFromSource($source) {
        $proxies = [];
        $url = $source['url'];
        $type = $source['type'] ?? 'plain';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode != 200 || !$response) {
            return $proxies;
        }

        switch ($type) {
            case 'plain':
                $proxies = $this->parsePlainList($response, 'http');
                break;
            case 'plain_socks':
                $proxies = $this->parsePlainList($response, 'socks5');
                break;
            case 'json_proxyscan':
                $proxies = $this->parseProxyscanJson($response);
                break;
            case 'json_pubproxy':
                $proxies = $this->parsePubproxyJson($response);
                break;
            default:
                $proxies = $this->parsePlainList($response, 'http');
                break;
        }

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

    public function verifyProxies($proxies, $testUrl = 'https://httpbin.org/get', $timeout = null) {
        if ($timeout === null) {
            $timeout = $this->verifyTimeout;
        }

        $verified = [];
        $total = count($proxies);
        $testCount = min($total, $this->maxVerifyCount);

        for ($i = 0; $i < $testCount; $i++) {
            $proxy = $proxies[$i];
            $result = $this->testSingleProxy($proxy, $testUrl, $timeout);
            if ($result['success']) {
                $proxy['response_time'] = $result['response_time'];
                $proxy['status'] = 'active';
                $verified[] = $proxy;
                if (count($verified) >= 10) {
                    break;
                }
            }
        }

        if (count($verified) < 5 && $testCount < $total) {
            $remaining = array_slice($proxies, $testCount, 20);
            foreach ($remaining as $proxy) {
                $result = $this->testSingleProxy($proxy, $testUrl, $timeout);
                if ($result['success']) {
                    $proxy['response_time'] = $result['response_time'];
                    $proxy['status'] = 'active';
                    $verified[] = $proxy;
                    if (count($verified) >= 10) {
                        break;
                    }
                }
            }
        }

        return $verified;
    }

    private function testSingleProxy($proxy, $testUrl, $timeout) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $testUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, min($timeout, 4));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

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
}
