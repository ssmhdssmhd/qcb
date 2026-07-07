<?php
/**
 * 数据库配置文件
 *
 * 默认使用 MySQL 数据库
 * 如需使用 SQLite（文件型，无需安装服务），请将 type 改为 sqlite
 */

return [
    'type' => 'mysql',
    'mysql_host' => '127.0.0.1',
    'mysql_port' => 3306,
    'mysql_dbname' => 'm3u8_ad',
    'mysql_username' => 'root',
    'mysql_password' => '',
    'mysql_charset' => 'utf8mb4',
    'sqlite_path' => __DIR__ . '/data.db',
];
