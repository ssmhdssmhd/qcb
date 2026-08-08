<?php

class AiEndpointRouter {
    private $endpoints = [];
    private $db = null;
    private $lastCheckResults = [];

    public function __construct($endpoints = null) {
        if ($endpoints !== null && !empty($endpoints)) {
            $this->endpoints = $this->normalizeEndpoints($endpoints);
        } else {
            $this->endpoints = $this->loadConfigEndpoints();
        }
        $this->tryApplyStoredOrder();
    }

    private function normalizeEndpoints($eps) {
        $result = [];
        $idx = 0;
        foreach ($eps as $ep) {
            if (is_string($ep)) {
                $result[] = [
                    'name' => 'endpoint_' . $idx,
                    'url' => $ep,
                    'api_key' => '',
                    'type' => 'openai_compatible',
                    'priority' => 100 + $idx,
                    'enabled' => true
                ];
            } elseif (is_array($ep)) {
                $result[] = array_merge([
                    'name' => 'endpoint_' . $idx,
                    'url' => '',
                    'api_key' => '',
                    'type' => 'openai_compatible',
                    'priority' => 100 + $idx,
                    'enabled' => true
                ], $ep);
            }
            $idx++;
        }
        return $result;
    }

    private function loadConfigEndpoints() {
        $eps = [];
        $xtConfigFile = __DIR__ . '/../xt/config.php';
        if (file_exists($xtConfigFile)) {
            $xtConfig = require $xtConfigFile;
            if (!empty($xtConfig['ai']) && is_array($xtConfig['ai'])) {
                $ai = $xtConfig['ai'];
                $eps[] = [
                    'name' => $ai['provider'] ?? 'default_ai',
                    'url' => $ai['api_url'] ?? '',
                    'api_key' => $ai['api_key'] ?? '',
                    'type' => 'openai_compatible',
                    'priority' => 1,
                    'enabled' => !empty($ai['enabled']),
                    'model' => $ai['model'] ?? '',
                    'max_tokens' => $ai['max_tokens'] ?? 2000
                ];
            }
        }
        if (!empty($eps)) {
            return $eps;
        }
        $fallback = [
            'name' => 'fallback_openai',
            'url' => 'https://api.openai.com/v1/chat/completions',
            'api_key' => '',
            'type' => 'openai_compatible',
            'priority' => 1,
            'enabled' => false
        ];
        return [$fallback];
    }

    private function getDb() {
        if ($this->db !== null) {
            return $this->db;
        }
        $dbFile = __DIR__ . '/../db/Database.php';
        if (!file_exists($dbFile)) {
            return null;
        }
        try {
            require_once $dbFile;
            $this->db = Database::getInstance();
            return $this->db;
        } catch (Throwable $e) {
            $this->db = null;
            return null;
        }
    }

    private function tryApplyStoredOrder() {
        $db = $this->getDb();
        if ($db === null) {
            return;
        }
        try {
            if (!$db->tableExists('sys_config')) {
                return;
            }
            $row = $db->queryOne('SELECT config_value FROM sys_config WHERE config_key = ?', ['ai_endpoints_order']);
            if (empty($row) || empty($row['config_value'])) {
                return;
            }
            $order = json_decode($row['config_value'], true);
            if (!is_array($order) || empty($order)) {
                return;
            }
            $byName = [];
            foreach ($this->endpoints as $i => $ep) {
                $key = $ep['name'] . '|' . md5($ep['url'] ?? '');
                $byName[$key] = $i;
            }
            $newOrder = [];
            $usedIndices = [];
            foreach ($order as $item) {
                $name = $item['name'] ?? '';
                $url = $item['url'] ?? '';
                $key = $name . '|' . md5($url);
                if (isset($byName[$key])) {
                    $idx = $byName[$key];
                    $newOrder[] = $this->endpoints[$idx];
                    $usedIndices[$idx] = true;
                }
            }
            foreach ($this->endpoints as $i => $ep) {
                if (!isset($usedIndices[$i])) {
                    $newOrder[] = $ep;
                }
            }
            if (!empty($newOrder)) {
                $this->endpoints = $newOrder;
            }
        } catch (Throwable $e) {
        }
    }

    public function checkEndpoint($ep, $timeout = 3) {
        $url = $ep['url'] ?? '';
        $type = $ep['type'] ?? 'openai_compatible';
        if (empty($url)) {
            return ['ok' => false, 'latency_ms' => 0, 'error' => '无URL'];
        }
        $checkUrl = $url;
        if ($type === 'openai_compatible') {
            if (strpos($url, '/chat/completions') !== false) {
                $checkUrl = substr($url, 0, strpos($url, '/chat/completions')) . '/models';
            } else {
                $parsed = parse_url($url);
                if ($parsed) {
                    $checkUrl = ($parsed['scheme'] ?? 'https') . '://' . ($parsed['host'] ?? '');
                    if (!empty($parsed['port'])) {
                        $checkUrl .= ':' . $parsed['port'];
                    }
                    $checkUrl .= rtrim($parsed['path'] ?? '/', '/') . '/models';
                }
            }
        }
        $startTime = microtime(true);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $checkUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, min($timeout, 3));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        $apiKey = $ep['api_key'] ?? '';
        if (!empty($apiKey)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $apiKey,
                'Accept: application/json'
            ]);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'AiEndpointRouter/1.0');
        $ok = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        $latency = round((microtime(true) - $startTime) * 1000, 0);
        $resultOk = false;
        $errMsg = '';
        if ($ok === false) {
            $errMsg = $error ?: '请求失败';
        } elseif ($httpCode >= 200 && $httpCode < 500) {
            $resultOk = true;
        } else {
            $errMsg = 'HTTP ' . $httpCode;
        }
        return [
            'ok' => $resultOk,
            'latency_ms' => $latency,
            'http_code' => $httpCode,
            'error' => $errMsg
        ];
    }

    public function getHealthyEndpoints() {
        $results = [];
        foreach ($this->endpoints as $idx => $ep) {
            if (empty($ep['enabled'])) {
                continue;
            }
            $check = $this->checkEndpoint($ep, 3);
            $epCopy = $ep;
            $epCopy['check'] = $check;
            $epCopy['original_index'] = $idx;
            if (!empty($check['ok'])) {
                $results[] = $epCopy;
            }
            $this->lastCheckResults[$ep['name'] . '|' . md5($ep['url'] ?? '')] = $check;
        }
        usort($results, function($a, $b) {
            $pa = intval($a['priority'] ?? 100);
            $pb = intval($b['priority'] ?? 100);
            if ($pa !== $pb) {
                return $pa - $pb;
            }
            $la = intval($a['check']['latency_ms'] ?? 99999);
            $lb = intval($b['check']['latency_ms'] ?? 99999);
            return $la - $lb;
        });
        return $results;
    }

    public function call($messages, $opts = []) {
        $model = $opts['model'] ?? 'gpt-4o-mini';
        $maxTokens = $opts['max_tokens'] ?? 512;
        $temperature = $opts['temperature'] ?? 0.3;
        $timeout = $opts['timeout'] ?? 30;
        $candidates = $this->getHealthyEndpoints();
        if (empty($candidates)) {
            $candidates = [];
            foreach ($this->endpoints as $ep) {
                if (!empty($ep['enabled'])) {
                    $candidates[] = $ep;
                }
            }
        }
        if (empty($candidates)) {
            return [
                'success' => false,
                'error' => '没有可用的 AI 端点',
                'content' => null
            ];
        }
        $candidates = array_slice($candidates, 0, 3);
        $lastError = '';
        foreach ($candidates as $ep) {
            $result = $this->callEndpoint($ep, $messages, [
                'model' => $ep['model'] ?? $model,
                'max_tokens' => $ep['max_tokens'] ?? $maxTokens,
                'temperature' => $temperature,
                'timeout' => $timeout
            ]);
            if (!empty($result['success'])) {
                return $result;
            }
            $lastError = $result['error'] ?? '未知错误';
            $key = $ep['name'] . '|' . md5($ep['url'] ?? '');
            $this->lastCheckResults[$key] = [
                'ok' => false,
                'latency_ms' => 0,
                'error' => $lastError,
                'failed_at' => time()
            ];
        }
        return [
            'success' => false,
            'error' => $lastError ?: '所有 AI 端点均失败',
            'content' => null
        ];
    }

    private function callEndpoint($ep, $messages, $opts) {
        $url = $ep['url'] ?? '';
        $apiKey = $ep['api_key'] ?? '';
        if (empty($url)) {
            return ['success' => false, 'error' => '端点URL为空'];
        }
        $payload = [
            'model' => $opts['model'] ?? 'gpt-4o-mini',
            'messages' => $messages,
            'max_tokens' => intval($opts['max_tokens'] ?? 512),
            'temperature' => floatval($opts['temperature'] ?? 0.3)
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, intval($opts['timeout'] ?? 30));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        if (!empty($apiKey)) {
            $headers[] = 'Authorization: Bearer ' . $apiKey;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'AiEndpointRouter/1.0');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($response === false) {
            return [
                'success' => false,
                'error' => '请求失败: ' . ($error ?: 'curl错误')
            ];
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            return [
                'success' => false,
                'error' => 'HTTP ' . $httpCode . ': ' . (strlen($response) > 200 ? substr($response, 0, 200) : $response)
            ];
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [
                'success' => false,
                'error' => '响应非JSON格式',
                'raw_response' => $response
            ];
        }
        if (isset($data['error'])) {
            $errMsg = '';
            if (is_array($data['error'])) {
                $errMsg = $data['error']['message'] ?? json_encode($data['error'], JSON_UNESCAPED_UNICODE);
            } else {
                $errMsg = (string)$data['error'];
            }
            return [
                'success' => false,
                'error' => 'API错误: ' . $errMsg
            ];
        }
        $content = null;
        if (isset($data['choices'][0]['message']['content'])) {
            $content = $data['choices'][0]['message']['content'];
        } elseif (isset($data['choices'][0]['text'])) {
            $content = $data['choices'][0]['text'];
        } elseif (isset($data['data'][0]['url'])) {
            $content = $data['data'][0]['url'];
        }
        return [
            'success' => true,
            'content' => $content,
            'raw' => $data,
            'endpoint_name' => $ep['name'] ?? 'unknown',
            'endpoint_url' => $url
        ];
    }

    public function updateConfigStoredOrder() {
        $db = $this->getDb();
        if ($db === null) {
            return ['success' => false, 'reason' => '数据库不可用'];
        }
        try {
            $db->initTables();
        } catch (Throwable $e) {
        }
        try {
            $orderData = [];
            foreach ($this->endpoints as $ep) {
                $key = $ep['name'] . '|' . md5($ep['url'] ?? '');
                $check = $this->lastCheckResults[$key] ?? ['ok' => null, 'latency_ms' => 0];
                $orderData[] = [
                    'name' => $ep['name'] ?? '',
                    'url' => $ep['url'] ?? '',
                    'priority' => $ep['priority'] ?? 100,
                    'enabled' => !empty($ep['enabled']),
                    'healthy' => !empty($check['ok']),
                    'latency_ms' => intval($check['latency_ms'] ?? 0)
                ];
            }
            $jsonValue = json_encode($orderData, JSON_UNESCAPED_UNICODE);
            $exists = $db->queryOne('SELECT id FROM sys_config WHERE config_key = ?', ['ai_endpoints_order']);
            if ($exists) {
                $db->update('sys_config', [
                    'config_value' => $jsonValue,
                    'description' => 'AI端点健康排序（AiEndpointRouter自动更新）'
                ], 'config_key = ?', ['ai_endpoints_order']);
            } else {
                $db->insert('sys_config', [
                    'config_key' => 'ai_endpoints_order',
                    'config_value' => $jsonValue,
                    'description' => 'AI端点健康排序（AiEndpointRouter自动更新）'
                ]);
            }
            return ['success' => true, 'endpoints' => count($orderData)];
        } catch (Throwable $e) {
            return ['success' => false, 'reason' => $e->getMessage()];
        }
    }

    public function getAllEndpoints() {
        return $this->endpoints;
    }

    public function getLastCheckResults() {
        return $this->lastCheckResults;
    }
}
