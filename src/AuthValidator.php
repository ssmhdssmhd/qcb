<?php

require_once __DIR__ . '/AuthConfig.php';
require_once __DIR__ . '/CryptoUtil.php';

class AuthValidator {

    private $config;
    private $authConfig;
    private $lastError = '';
    private $authData = null;

    public function __construct() {
        $this->authConfig = new AuthConfig();
        $this->config = $this->authConfig->getConfig();
    }

    public function validate() {
        if (!$this->config['enabled']) {
            return true;
        }

        $cacheTtl = $this->config['validation']['cache_ttl'] ?? 60;
        $lastCheck = $this->config['last_check'] ?? 0;
        $lastResult = $this->config['last_result'] ?? false;

        if ($lastResult && (time() - $lastCheck) < $cacheTtl) {
            return true;
        }

        if ($this->config['validation']['check_local_first']) {
            $localResult = $this->checkLocal();
            if (!$localResult) {
                $this->updateCheckResult(false);
                return false;
            }
        }

        if ($this->config['validation']['check_remote']) {
            $remoteResult = $this->checkRemote();
            if (!$remoteResult) {
                $this->updateCheckResult(false);
                return false;
            }
        }

        $this->updateCheckResult(true);
        return true;
    }

    private function checkLocal() {
        $localFile = $this->authConfig->getLocalFilePath();

        if (!file_exists($localFile)) {
            $this->lastError = '本地授权文件不存在';
            return false;
        }

        $content = trim(file_get_contents($localFile));
        if (empty($content)) {
            $this->lastError = '本地授权文件为空';
            return false;
        }

        $authData = $this->parseAuthContent($content);
        if (!$authData) {
            $this->lastError = '本地授权文件格式错误';
            return false;
        }

        $this->authData = $authData;

        if ($this->config['validation']['check_timestamp']) {
            if (!$this->checkTimestamp($authData)) {
                return false;
            }
        }

        return true;
    }

    private function checkRemote() {
        $primaryUrl = $this->authConfig->getServerUrl(false);
        $backupUrl = $this->authConfig->getServerUrl(true);

        $remoteContent = $this->fetchRemote($primaryUrl);
        if ($remoteContent === false) {
            $remoteContent = $this->fetchRemote($backupUrl);
        }

        if ($remoteContent === false) {
            $this->lastError = '无法连接授权服务器';
            return false;
        }

        $remoteData = $this->parseAuthContent($remoteContent);
        if (!$remoteData) {
            $this->lastError = '授权服务器返回数据格式错误';
            return false;
        }

        if ($this->authData === null) {
            $localFile = $this->authConfig->getLocalFilePath();
            if (file_exists($localFile)) {
                $localContent = trim(file_get_contents($localFile));
                $localData = $this->parseAuthContent($localContent);
                if ($localData) {
                    $this->authData = $localData;
                }
            }
        }

        if ($this->authData !== null) {
            if (!$this->compareAuthData($this->authData, $remoteData)) {
                $this->lastError = '授权码与服务器不一致';
                return false;
            }
        } else {
            $this->authData = $remoteData;
        }

        if ($this->config['validation']['check_timestamp']) {
            if (!$this->checkTimestamp($remoteData)) {
                return false;
            }
        }

        return true;
    }

    private function parseAuthContent($content) {
        $content = trim($content);

        $json = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($json['code'])) {
            return $json;
        }

        try {
            $decrypted = CryptoUtil::decrypt($content);
            if (is_array($decrypted) && isset($decrypted['code'])) {
                return $decrypted;
            }
        } catch (Exception $e) {
        }

        if (preg_match('/^([a-zA-Z0-9]+)\|(\d+)$/', $content, $matches)) {
            return [
                'code' => $matches[1],
                'timestamp' => intval($matches[2])
            ];
        }

        if (preg_match('/^[a-f0-9]{32}$/i', $content)) {
            return [
                'code' => $content,
                'timestamp' => 0
            ];
        }

        return [
            'code' => $content,
            'timestamp' => 0,
            'raw' => true
        ];
    }

    private function compareAuthData($local, $remote) {
        if (isset($local['code']) && isset($remote['code'])) {
            if ($local['code'] !== $remote['code']) {
                return false;
            }
        }

        if (isset($local['sign']) && isset($remote['sign'])) {
            if ($local['sign'] !== $remote['sign']) {
                return false;
            }
        }

        return true;
    }

    private function checkTimestamp($authData) {
        if (!isset($authData['timestamp']) || $authData['timestamp'] == 0) {
            return true;
        }

        $timestamp = intval($authData['timestamp']);
        $tolerance = $this->config['validation']['timestamp_tolerance'] ?? 300;

        $diff = abs(time() - $timestamp);

        if ($diff > $tolerance) {
            $this->lastError = '授权时间戳无效（' . date('Y-m-d H:i:s', $timestamp) . '）';
            return false;
        }

        return true;
    }

    private function fetchRemote($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Auth-Checker/1.0');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return false;
        }

        return trim($response);
    }

    private function updateCheckResult($result) {
        $this->authConfig->set('last_check', time());
        $this->authConfig->set('last_result', $result);
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function getAuthData() {
        return $this->authData;
    }

    public function getErrorMessage() {
        $msg = $this->config['contact']['message'] ?? '授权异常，请联系管理员';
        $qq = $this->config['contact']['qq'] ?? '';
        $error = $this->lastError ? '（' . $this->lastError . '）' : '';
        return $msg . $error;
    }

    public function generateAuthCode($domain = '') {
        $data = [
            'code' => CryptoUtil::randomString(32),
            'timestamp' => time(),
            'domain' => $domain,
            'version' => '1.0'
        ];
        $data['sign'] = CryptoUtil::sign($data);
        return $data;
    }

    public function generateEncryptedAuth($domain = '') {
        $data = $this->generateAuthCode($domain);
        return CryptoUtil::encrypt($data);
    }

    public function saveLocalAuth($content) {
        $localFile = $this->authConfig->getLocalFilePath();
        return file_put_contents($localFile, $content) !== false;
    }

    public function getLocalAuthContent() {
        $localFile = $this->authConfig->getLocalFilePath();
        if (!file_exists($localFile)) {
            return null;
        }
        return file_get_contents($localFile);
    }

    public function testConnection($ip, $port, $file, $protocol = 'http') {
        $url = $protocol . '://' . $ip . ':' . $port . '/' . $file;
        return $this->fetchRemote($url);
    }
}
