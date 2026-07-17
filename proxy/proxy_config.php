<?php
/**
 * 代理池配置
 * 自动更新于: 2026-07-01 16:45:41
 */

return [
    'version' => '1.1',
    'enabled' => true,
    'auto_switch' => true,
    'check_interval' => 300,
    'timeout' => 10,
    'proxies' => [
        [
            'id' => 'default_http_1',
            'name' => 'Default HTTP Proxy',
            'type' => 'http',
            'host' => '127.0.0.1',
            'port' => 8080,
            'username' => '',
            'password' => '',
            'status' => 'active',
            'priority' => 100,
            'success_count' => 0,
            'fail_count' => 0,
            'last_check' => null,
            'last_success' => null,
            'response_time' => 0
        ]
    ]
];
