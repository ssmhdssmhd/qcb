<?php
/**
 * 数据库配置文件
 *
 * 默认使用 SQLite 文件数据库，开箱即用，无需安装 MySQL
 * 如需使用 MySQL，请复制 db_config.php.example 并重命名为 db_config.php
 * 修改其中的 type 为 mysql，并配置 MySQL 连接信息
 */

return [
    'type' => 'sqlite',
    'sqlite_path' => __DIR__ . '/data.db',
];
