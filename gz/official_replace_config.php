<?php
/**
 * 官替 API 配置
 * 自动生成于: 2026-06-30 20:00:12
 */

return [
    'version' => '3.2.12',
    'update_date' => '2026-07-13',
    'enabled' => true,
    'default_site' => '量子',
    'max_search_sites' => 5,
    'cache_ttl' => 3600,
    'platforms' => [
        [
            'name' => '腾讯视频',
            'domain' => 'v.qq.com',
            'enabled' => true,
            'pattern' => '/v\\.qq\\.com\\/.*?(?:vid=|\\/)([a-zA-Z0-9]+)/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video_title, h1',
            'priority' => 1
        ],
        [
            'name' => '爱奇艺',
            'domain' => 'iqiyi.com',
            'enabled' => true,
            'pattern' => '/iqiyi\\.com\\/.*?([a-zA-Z0-9]{5,})/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .main_title, h1',
            'priority' => 1
        ],
        [
            'name' => '优酷',
            'domain' => 'youku.com',
            'enabled' => true,
            'pattern' => '/youku\\.com\\/.*?id_([a-zA-Z0-9]+)/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .title, h1',
            'priority' => 1
        ],
        [
            'name' => '芒果TV',
            'domain' => 'mgtv.com',
            'enabled' => true,
            'pattern' => '/mgtv\\.com\\/.*?\\/([a-zA-Z0-9]+)\\.html/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .player-title, h1',
            'priority' => 1
        ],
        [
            'name' => '哔哩哔哩',
            'domain' => 'bilibili.com',
            'enabled' => true,
            'pattern' => '/bilibili\\.com\\/video\\/(BV[a-zA-Z0-9]+)/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video-title, h1',
            'priority' => 1
        ],
        [
            'name' => '搜狐视频',
            'domain' => 'sohu.com',
            'enabled' => true,
            'pattern' => '/sohu\\.com\\/.*?(\\d+)\\.shtml/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
            'priority' => 2
        ],
        [
            'name' => 'PP视频',
            'domain' => 'pptv.com',
            'enabled' => true,
            'pattern' => '/pptv\\.com\\/showpage\\/([a-zA-Z0-9_-]+)/i',
            'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
            'priority' => 2
        ]
    ],
    'search_sites' => [],
    'match_threshold' => 60
];
