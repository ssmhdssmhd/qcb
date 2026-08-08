<?php

class AuthConfig
{
    private $configFile;
    private $config = [];

    public function __construct()
    {
        $this->configFile = dirname(__DIR__) . '/auth_config.php';
        $this->loadConfig();
    }

    private function loadConfig()
    {
        $loaded = false;
        if (file_exists($this->configFile)) {
            $config = @include $this->configFile;
            if (is_array($config)) {
                $this->config = $config;
                $loaded = true;
            }
        }

        if (!$loaded) {
            $jsonFile = dirname(__DIR__) . '/auth_config.json';
            if (file_exists($jsonFile)) {
                $content = file_get_contents($jsonFile);
                $jsonConfig = json_decode($content, true);
                if (is_array($jsonConfig)) {
                    $this->config = $jsonConfig;
                    $this->config['auth_file'] = 'sq.txt';
                    $this->config['auth_file_compare'] = 'sq.txt';
                    $this->saveConfig();
                    $loaded = true;
                }
            }
        }
        
        if (empty($this->config)) {
            $this->config = [
                'auth_server_ip' => '114.134.184.91',
                'auth_server_port' => '9001',
                'auth_file' => 'sq.txt',
                'auth_file_compare' => 'sq.txt',
                'enable_remote_verify' => true,
                'enable_timestamp_check' => true,
                'timestamp_tolerance' => 86400,
                // 统一全局联系信息（授权、售后）
                'contact_qq' => '2094332348',
                'developer_name' => 'M3U8官方解析团队',
                'official_api_domain' => '114.134.184.91',
                'official_api_port' => '9002'
            ];
            $this->saveConfig();
        }
    }

    /**
     * 获取全局联系QQ（集中管理，避免多处硬编码）
     */
    public function getContactQQ()
    {
        return $this->get('contact_qq', '2094332348');
    }

    /**
     * 获取官解 API 基础 URL（根据配置动态拼接）
     */
    public function getOfficialParseApiUrl()
    {
        $domain = $this->get('official_api_domain', '114.134.184.91');
        $port = $this->get('official_api_port', '9002');
        return 'http://' . $domain . ':' . $port . '/mx.php?action=api/v2&type=parse&url=';
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
        $content = "<?php\nreturn " . var_export($this->config, true) . ";\n";
        $result = file_put_contents($this->configFile, $content);
        return $result !== false;
    }

    public function getAuthUrl()
    {
        $ip = $this->get('auth_server_ip', '114.134.184.91');
        $port = $this->get('auth_server_port', '9001');
        $file = $this->get('auth_file', 'sq.php');
        return 'http://' . $ip . ':' . $port . '/' . $file;
    }

    public function getCompareUrl()
    {
        $ip = $this->get('auth_server_ip', '114.134.184.91');
        $port = $this->get('auth_server_port', '9001');
        $file = $this->get('auth_file_compare', 'sqm.php');
        return 'http://' . $ip . ':' . $port . '/' . $file;
    }
}
