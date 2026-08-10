<?php
/**
 * 核心模块：Database + Db*Manager（默认强制启用）
 * 没有它 resource_sites / official_replace / ai_learn / proxy 这些 DB 型模块都跑不起来
 */
return [
    'id'              => 'core_db',
    'name'            => '核心-数据库层',
    'version'         => '5.11.0',
    'description'     => 'Database 单例 / DataMigration / DbConfigManager / 所有 Db*Manager。模块底层依赖，默认强制启用。',
    'requires'        => [],
    'default_enabled' => true,
    'priority'        => 1,      // 最先启动
    'provides'        => ['db', 'database', 'sys_config'],
];
