<?php
return [
    'id'              => 'ad_filter',
    'name'            => 'M3U8广告过滤核心',
    'version'         => '5.11.0',
    'description'     => 'M3U8Parser / AdAnalyzer / AdRuleEngine / M3U8AdSkipper。嗅探解析核心，建议不关闭。',
    'requires'        => ['core_db'],
    'default_enabled' => true,
    'priority'        => 10,
    'provides'        => ['m3u8_parser', 'ad_rule_engine', 'ad_filter'],
];
