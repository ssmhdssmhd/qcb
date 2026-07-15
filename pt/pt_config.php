<?php
/**
 * pt 平台适配配置
 */

return [
    'version' => '4.0.2',
    'update_date' => '2026-07-15',
    'enabled' => true,

    // 匹配阈值（pt 默认更低，因为 pt 有平台特定算法）
    'match_threshold' => 50,
    'best_effort_threshold' => 40,

    // 搜索站点
    'search_sites' => ['量子', '暴风', '非凡', '天影', '6度资源', '豆包', '猫眼', '索尼', '最大', 'OK资源', '快车', '闪电', '丫丫（鸭鸭）', '无尽', '速播', '红牛', '豪华', '光速', '蓝光', '魔都', '看看', '樱花', '好花', '电影天堂', '茅台', '13大众', '百度', '爱奇艺资', '牛牛6', '蓝志', '天逸', '如意', '天繁', '西瓜'],
    'max_search_sites' => 40,

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
