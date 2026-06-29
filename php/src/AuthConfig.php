<?php

class AuthConfig
{
    private $configFile;
    private $config = [];

    public function __construct()
    {
        $this->configFile = dirname(__DIR__) . '/auth_config.json';
        $this->loadConfig();
    }

    private function loadConfig()
    {
        if (file_exists($this->configFile)) {
            $content = file_get_contents($this->configFile);
            $this->config = json_decode($content, true) ?: [];
        }
        
        if (empty($this->config)) {
            $this->config = [
                'auth_server_ip' => '114.134.184.91',
                'auth_server_port' => '9001',
                'auth_file' => 'sq.txt',
                'auth_file_compare' => 'sqm.txt',
                'enable_remote_verify' => true,
                'enable_timestamp_check' => true,
                'timestamp_tolerance' => 86400
            ];
            $this->saveConfig();
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
        return $this->saveConfig();
    }

    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
        return $this->saveConfig();
    }

    public function saveConfig()
    {
        $result = file_put_contents($this->configFile, json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $result !== false;
    }

    public function getAuthUrl()
    {
        $ip = $this->get('auth_server_ip', '114.134.184.91');
        $port = $this->get('auth_server_port', '9001');
        $file = $this->get('auth_file', 'sq.txt');
        return 'http://' . $ip . ':' . $port . '/' . $file;
    }

    public function getCompareUrl()
    {
        $ip = $this->get('auth_server_ip', '114.134.184.91');
        $port = $this->get('auth_server_port', '9001');
        $file = $this->get('auth_file_compare', 'sqm.txt');
        return 'http://' . $ip . ':' . $port . '/' . $file;
    }
}
