<?php

require_once __DIR__ . '/CryptoUtil.php';
require_once __DIR__ . '/AuthConfig.php';

class AuthValidator
{
    private $sqFile;
    private $authConfig;
    private $lastError = '';
    private $rootDir;

    public function __construct()
    {
        $this->rootDir = dirname(__DIR__);
        $this->sqFile = $this->rootDir . '/sq.php';
        $this->authConfig = new AuthConfig();
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function getAuthConfig()
    {
        return $this->authConfig;
    }

    public function checkSqFileExists()
    {
        return file_exists($this->sqFile);
    }

    public function getLocalAuthCode()
    {
        if (!$this->checkSqFileExists()) {
            return null;
        }
        $code = @include $this->sqFile;
        if (is_string($code)) {
            return trim($code);
        }
        return trim(file_get_contents($this->sqFile));
    }

    public function validateLocal()
    {
        if (!$this->checkSqFileExists()) {
            $this->lastError = '授权文件不存在，请联系 QQ2094332348 进行授权';
            return false;
        }

        $authCode = $this->getLocalAuthCode();
        if (empty($authCode)) {
            $this->lastError = '授权文件为空，请联系 QQ2094332348 进行授权';
            return false;
        }

        $domain = '';
        $timestamp = 0;
        if (!CryptoUtil::verifyAuthCode($authCode, $domain, $timestamp)) {
            $this->lastError = '授权码格式无效，请联系 QQ2094332348 进行授权';
            return false;
        }

        return true;
    }

    public function validateRemote()
    {
        if (!$this->authConfig->get('enable_remote_verify', true)) {
            return true;
        }

        $localCode = $this->getLocalAuthCode();
        if (!$localCode) {
            $this->lastError = '本地授权码不存在';
            return false;
        }

        $remoteUrl = $this->authConfig->getAuthUrl();
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remoteUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Auth-Checker');
        $remoteCode = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode !== 200 || $remoteCode === false) {
            $compareUrl = $this->authConfig->getCompareUrl();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $compareUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Auth-Checker');
            $compareContent = curl_exec($ch);
            $httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode2 === 200 && $compareContent !== false) {
                $compareContent = trim($compareContent);
                $localHash = md5($localCode);
                if ($localHash === $compareContent || $localCode === $compareContent) {
                    return true;
                }
            }

            $this->lastError = '无法连接授权服务器，请检查网络或联系 QQ2094332348';
            return false;
        }

        $remoteCode = trim($remoteCode);
        if ($remoteCode !== $localCode) {
            $this->lastError = '授权码与服务器不匹配，请联系 QQ2094332348 进行授权';
            return false;
        }

        return true;
    }

    public function validateAll()
    {
        if (!$this->validateLocal()) {
            return false;
        }

        if (!$this->validateRemote()) {
            return false;
        }

        return true;
    }

    public function setAuthCode($authCode)
    {
        $content = "<?php\nreturn '" . addslashes($authCode) . "';\n";
        $result = file_put_contents($this->sqFile, $content);
        return $result !== false;
    }

    public function generateAuthCode($domain)
    {
        return CryptoUtil::generateAuthCode($domain);
    }

    public function getAuthInfo()
    {
        $info = [
            'sq_file_exists' => $this->checkSqFileExists(),
            'local_valid' => false,
            'remote_valid' => false,
            'auth_config' => $this->authConfig->getConfig(),
            'local' => [
                'file_exists' => false,
                'file_size' => 0,
                'auth_code' => '',
            ],
            'remote' => [],
        ];

        $sqFile = $this->rootDir . '/sq.php';
        if (file_exists($sqFile)) {
            $info['local']['file_exists'] = true;
            $info['local']['file_size'] = filesize($sqFile);
            $content = file_get_contents($sqFile);
            if (preg_match('/return \'(.*?)\';/', $content, $m)) {
                $info['local']['auth_code'] = $m[1];
            }
        }

        if ($info['sq_file_exists']) {
            $authCode = $this->getLocalAuthCode();
            $domain = '';
            $timestamp = 0;
            if (CryptoUtil::verifyAuthCode($authCode, $domain, $timestamp)) {
                $info['domain'] = $domain;
                $info['timestamp'] = $timestamp;
                $info['timestamp_formatted'] = date('Y-m-d H:i:s', $timestamp);
                $info['local_valid'] = true;
            }
        }

        $config = $this->authConfig->getConfig();
        if (!empty($config['auth_server_ip']) && !empty($config['auth_file'])) {
            $remoteUrl = 'http://' . $config['auth_server_ip'] . ':' . ($config['auth_server_port'] ?? '9001') . '/' . $config['auth_file'];
            $remoteResult = $this->curlTest($remoteUrl, 5);
            $info['remote'] = [
                'url' => $remoteUrl,
                'reachable' => $remoteResult['success'],
                'content' => $remoteResult['response'] ?? '',
                'error' => $remoteResult['error'] ?? '',
            ];
        }

        return $info;
    }

    private function curlTest($url, $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response !== false) {
            return ['success' => true, 'response' => $response];
        }

        return [
            'success' => false,
            'error' => $error ?: ('HTTP ' . $httpCode),
        ];
    }
}
