<?php
return [
    'id'              => 'proxy',
    'name'            => 'IP代理池',
    'version'         => '5.11.0',
    'description'     => 'ProxyFetcher 爬取代理 / ProxyManager 评分 + 轮换 / 429/封锁时自动切换。不需要可关。',
    'requires'        => ['core_db'],
    'default_enabled' => true,
    'priority'        => 50,
    'provides'        => ['ip_proxy', 'proxy_rotation'],
];
