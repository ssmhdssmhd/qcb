<?php
return [
    'id'              => 'platform_sniffer',
    'name'            => '平台适配器/官解通道',
    'version'         => '5.11.0',
    'description'     => 'pt/* 下的腾讯/爱奇艺/优酷/芒果/B站/搜狐/PPTV 平台 Adapter，官解通道 PtManager。',
    'requires'        => ['ad_filter'],
    'default_enabled' => true,
    'priority'        => 20,
    'provides'        => ['platform_adapters', 'official_channel', 'pt_manager'],
];
