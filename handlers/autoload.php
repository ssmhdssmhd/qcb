<?php
/**
 * handlers 模块 —— 自动加载器
 *
 * 加载 helpers + 各 action handler + 分发器。
 * 依赖 mx.php 已加载 src/gz 等基础类；本文件仅负责 handlers 自身文件。
 *
 * @package handlers
 * @since   5.14.0
 */
if (defined('HANDLERS_AUTOLOAD_LOADED')) {
    return;
}
define('HANDLERS_AUTOLOAD_LOADED', 1);

$handlersDir = __DIR__;

require_once $handlersDir . '/helpers/SelfUrlHelper.php';
require_once $handlersDir . '/helpers/M3u8UrlHelper.php';
require_once $handlersDir . '/helpers/TitleExtractor.php';

require_once $handlersDir . '/HandlersContext.php';
require_once $handlersDir . '/BaseHandler.php';
require_once $handlersDir . '/MoxiHandler.php';
require_once $handlersDir . '/MxjxHandler.php';
require_once $handlersDir . '/MxjxInfoHandler.php';
require_once $handlersDir . '/MxjxDeepHandler.php';
require_once $handlersDir . '/XiamiJxHandler.php';
require_once $handlersDir . '/SkipHandler.php';
require_once $handlersDir . '/ParseHandler.php';
require_once $handlersDir . '/HandlerDispatcher.php';
