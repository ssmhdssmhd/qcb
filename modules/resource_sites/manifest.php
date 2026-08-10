<?php
/**
 * 模块元数据：资源站管理（resource_sites）
 * 若想禁用资源站管理：在 config/modules.php 中把 resource_sites 设为 false
 */
return [
    'id'              => 'resource_sites',
    'name'            => '资源站管理',
    'version'         => '5.11.0',
    'description'     => '采集资源站 CRUD、健康巡检、批量导入导出、优先级排序。依赖：core_db（数据库层）',
    'requires'        => ['core_db'],     // 必须先有 DB 模块（核心默认启用）
    'suggests'        => ['proxy'],       // 存在 proxy 模块时会自动挂 ProxyManager
    'default_enabled' => true,
    'priority'        => 30,
    'provides'        => ['resource_sites', 'sites_crud'],
];
