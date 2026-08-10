<?php
return [
    'id'              => 'cctv',
    'name'            => 'CCTV央视直播源',
    'version'         => '5.11.0',
    'description'     => 'GitHub 官方源抓取解析 / 定时自动更新 / M3U8 生成 / 播放页面集成。不需要可直接 false。',
    'requires'        => [],
    'default_enabled' => true,
    'priority'        => 200,
    'provides'        => ['cctv_live', 'm3u8_generator'],
];
