<?php
/**
 * pt 平台适配配置
 */

return [
    'version' => '4.0.0',
    'update_date' => '2026-07-15',
    'enabled' => true,

    // 匹配阈值（pt 默认更低，因为 pt 有平台特定算法）
    'match_threshold' => 50,
    'best_effort_threshold' => 40,

    // 搜索站点
    'search_sites' => ['量子', '暴风', '非凡', '天影', '猫眼', '最大', '索尼', 'OK资源', '红牛'],
    'max_search_sites' => 10,

    // AI 引擎
    'enable_ai' => true,
    'ai_learning' => true,

    // 去广告引擎
    'enable_ad_skip' => true,
    'ad_skip_mode' => 'blank', // blank: 用空白片段替代广告

    // 平台适配器开关
    'platforms' => [
        'tencent' => ['enabled' => true],
        'iqiyi' => ['enabled' => true],
        'youku' => ['enabled' => true],
        'mgtv' => ['enabled' => true],
        'bilibili' => ['enabled' => true],
        'sohu' => ['enabled' => true],
    ],
];
