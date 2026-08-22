<?php
/**
 * parse 模块 —— 自动加载器
 *
 * 轻量、无 Composer 依赖，直接 require 本文件即可。
 * 用法：require_once __DIR__ . '/parse/autoload.php';
 *
 * @package parse
 * @since   5.13.9
 */
if (defined('PARSE_AUTOLOAD_LOADED')) {
    return;
}
define('PARSE_AUTOLOAD_LOADED', 1);

$parseDir = __DIR__;

// 集中配置（缓存到全局变量，供 ParserFacade 复用）
$GLOBALS['_PARSE_CFG'] = require $parseDir . '/config.php';

require_once $parseDir . '/Timer.php';
require_once $parseDir . '/UrlClassifier.php';
require_once $parseDir . '/ParseResult.php';
require_once $parseDir . '/LocalM3u8Cleaner.php';
require_once $parseDir . '/ResourceFirstResolver.php';
require_once $parseDir . '/ParserFacade.php';