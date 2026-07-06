<?php
/**
 * 数据库模块自动加载器
 * 使用方法: require_once __DIR__ . '/db/autoload.php';
 */

if (!defined('DB_AUTOLOAD_LOADED')) {
    define('DB_AUTOLOAD_LOADED', true);

    $dbDir = __DIR__;

    require_once $dbDir . '/Database.php';
    require_once $dbDir . '/DbConfigManager.php';
    require_once $dbDir . '/DbDomainRuleManager.php';
    require_once $dbDir . '/DbResourceSiteManager.php';
    require_once $dbDir . '/DbProxyManager.php';
    require_once $dbDir . '/DbOfficialSiteManager.php';
    require_once $dbDir . '/DbOfficialReplaceManager.php';
    require_once $dbDir . '/DataMigration.php';
}
