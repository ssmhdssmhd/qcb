<?php

require_once __DIR__ . '/CryptoUtil.php';

class AuthConfig {

    private $configFile;
    private $config;

    public function __construct() {
        $this->configFile = __DIR__ . '/../auth_config.php';
        $this->config = $this->loadConfig();
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $config = require $this->configFile;
            if (is_array($config)) {
                return $config;
            }
        }
        return $this->getDefaultConfig();
    }

    private function getDefaultConfig() {
        return [
            'enabled' => true,
            'local_file' => 'sq.txt',
            'server' => [
                'ip' => '114.134.184.91',
                'port' => 9001,
                'protocol' => 'http',
                'primary_file' => 'sq.txt',
                'backup_file' => 'sqm.txt',
                'use_encryption' => true
            ],
            'validation' => [
                'check_local_first' => true,
                'check_remote' => true,
                'check_timestamp' => true,
                'timestamp_tolerance' => 300,
                'cache_ttl' => 60
            ],
            'contact' => [
                'qq' => '2094332348',
                'message' => '授权异常，请联系 QQ 2094332348 进行授权'
            ],
            'last_check' => 0,
            'last_result' => false,
            'last_remote_content' => null
        ];
    }

    public function getConfig() {
        return $this->config;
    }

    public function get($key, $default = null) {
        $keys = explode('.', $key);
        $value = $this->config;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value;
    }

    public function set($key, $value) {
        $keys = explode('.', $key);
        $config = &$this->config;
        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }
        $config = $value;
        return $this->saveConfig();
    }

    public function saveConfig() {
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * 授权配置文件' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($this->config) . ';' . "\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    public function getServerUrl($useBackup = false) {
        $protocol = $this->get('server.protocol', 'http');
        $ip = $this->get('server.ip', '114.134.184.91');
        $port = $this->get('server.port', 9001);
        $file = $useBackup
            ? $this->get('server.backup_file', 'sqm.txt')
            : $this->get('server.primary_file', 'sq.txt');
        return $protocol . '://' . $ip . ':' . $port . '/' . $file;
    }

    public function getLocalFilePath() {
        return __DIR__ . '/../' . $this->get('local_file', 'sq.txt');
    }

    private function arrayExport($array, $indent = 0) {
        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        if (empty($array)) {
            return '[]';
        }
        $isList = array_keys($array) === range(0, count($array) - 1);
        $items = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $valStr = $this->arrayExport($value, $indent + 1);
            } elseif (is_bool($value)) {
                $valStr = $value ? 'true' : 'false';
            } elseif (is_int($value) || is_float($value)) {
                $valStr = $value;
            } else {
                $valStr = var_export((string)$value, true);
            }
            if ($isList) {
                $items[] = $nextPrefix . $valStr;
            } else {
                $items[] = $nextPrefix . var_export($key, true) . ' => ' . $valStr;
            }
        }
        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }
}
