<?php
/**
 * 推荐采集资源站配置列表
 * 推荐采集专区 - 支持多域名采集
 */

return [
    'version' => '1.0',
    'update_date' => '2026-06-30',
    'enabled' => true,
    'sites' => [
        [
            'name' => 'TW推荐采集',
            'code' => 'tw',
            'site_url' => 'https://cj.10010888.xyz',
            'api_url' => 'https://cj.10010888.xyz/api.php/provide/vod/',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '推荐采集',
            'priority' => 1,
            'is_official' => true,
            'domains' => [
                'cj.10010888.xyz',
                'cj.tianwe.cn',
                'tianwei.qzz.io'
            ],
            'active_domain_index' => 0,
            'api_path' => '/api.php/provide/vod/'
        ]
    ],
    'settings' => [
        'auto_switch_domain' => true,
        'max_retry_per_domain' => 2,
        'timeout' => 10,
        'default_limit' => 20
    ]
];
