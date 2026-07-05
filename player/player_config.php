<?php
return [
    'default_player' => 'dplayer',
    'autoplay' => false,
    'preload' => 'auto',
    'api_base_url' => '',

    'commercial_players' => [
        'jwplayer' => [
            'license_key' => '',
        ],
        'bitmovin' => [
            'license_key' => '',
        ],
        'theoplayer' => [
            'license_key' => '',
        ],
        'flowplayer' => [
            'token' => '',
        ],
        'radiant' => [
            'license_key' => '',
        ],
    ],

    'players' => [
        'dplayer' => [
            'name' => 'DPlayer',
            'category' => '开源',
            'description' => '优秀的开源 HTML5 弹幕视频播放器',
            'type' => 'hls',
        ],
        'videojs' => [
            'name' => 'Video.js',
            'category' => '开源',
            'description' => '最流行的开源 HTML5 视频播放器',
            'type' => 'hls',
        ],
        'shaka' => [
            'name' => 'Shaka Player',
            'category' => '开源',
            'description' => 'Google 开源的自适应码率流媒体播放器',
            'type' => 'shaka',
        ],
        'clappr' => [
            'name' => 'Clappr',
            'category' => '开源',
            'description' => '基于插件架构的开源 HTML5 播放器',
            'type' => 'hls',
        ],
        'dashjs' => [
            'name' => 'dash.js',
            'category' => '开源',
            'description' => 'DASH 行业论坛官方 MPEG-DASH 播放器',
            'type' => 'dash',
        ],
        'hlsjs' => [
            'name' => 'hls.js 原生',
            'category' => '开源',
            'description' => 'HLS.js + 原生 video 轻量播放器',
            'type' => 'hls',
        ],
        'muiplayer' => [
            'name' => 'MuiPlayer',
            'category' => '开源',
            'description' => '国人开发的 HTML5 视频播放器',
            'type' => 'hls',
        ],
        'artplayer' => [
            'name' => 'ArtPlayer',
            'category' => '开源',
            'description' => '现代化的 HTML5 视频播放器',
            'type' => 'hls',
        ],
        'nplayer' => [
            'name' => 'NPlayer',
            'category' => '开源',
            'description' => '支持弹幕的视频播放器',
            'type' => 'hls',
        ],
        'native' => [
            'name' => '原生 Video',
            'category' => '系统',
            'description' => '浏览器原生 video 标签播放',
            'type' => 'native',
        ],
        'jwplayer' => [
            'name' => 'JW Player',
            'category' => '商业',
            'description' => '流行的端到端视频解决方案',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'bitmovin' => [
            'name' => 'Bitmovin',
            'category' => '商业',
            'description' => '顶级视频流媒体技术提供商',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'theoplayer' => [
            'name' => 'THEOplayer',
            'category' => '商业',
            'description' => '获奖的视频播放器技术',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'flowplayer' => [
            'name' => 'Flowplayer',
            'category' => '商业',
            'description' => '轻量级全栈视频播放器方案',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'radiant' => [
            'name' => 'Radiant Media Player',
            'category' => '商业',
            'description' => '现代 HTML5 跨设备视频播放器',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'nexplayer' => [
            'name' => 'NexPlayer',
            'category' => '商业',
            'description' => '自主开发的 HLS/DASH 播放器',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'castlabs' => [
            'name' => 'castLabs PRESTOplay',
            'category' => '商业',
            'description' => '基于 Shaka 的商业播放器',
            'type' => 'commercial',
            'license_required' => true,
        ],
        'visualon' => [
            'name' => 'VisualON',
            'category' => '商业',
            'description' => '主流播放器 SDK 提供商',
            'type' => 'commercial',
            'license_required' => true,
        ],
    ],
];
