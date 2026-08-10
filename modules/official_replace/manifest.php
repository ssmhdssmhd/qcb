<?php
return [
    'id'              => 'official_replace',
    'name'            => '官替识别',
    'version'         => '5.11.0',
    'description'     => '官替平台/资源站配置、URL识别/提取、搜索抽检、抖剧TV默认官替纠偏。',
    'requires'        => ['core_db'],
    'suggests'        => ['resource_sites', 'ai_learn'],
    'default_enabled' => true,
    'priority'        => 40,
    'provides'        => ['official_replace', 'official_detect'],
];
