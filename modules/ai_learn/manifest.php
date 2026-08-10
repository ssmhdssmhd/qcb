<?php
return [
    'id'              => 'ai_learn',
    'name'            => 'AI自动学习广告规则',
    'version'         => '5.11.0',
    'description'     => '从资源站抓取样本 -> 广告分析 -> 学习域名规则 -> 清理失效。可独立关。',
    'requires'        => ['core_db', 'ad_filter'],
    'suggests'        => ['resource_sites', 'multi_thread'],
    'default_enabled' => true,
    'priority'        => 60,
    'provides'        => ['ai_learn', 'ad_rules_learn'],
];
