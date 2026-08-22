<?php
/**
 * HandlerDispatcher —— 解析 action 分发器
 *
 * 将「解析类」action 路由到对应 handler；返回 false 表示未命中
 * （未命中时由 mx.php 继续处理管理类 action）。
 *
 * 支持动作映射（含别名）：
 *   moxi / moxi/api          → MoxiHandler
 *   mxjx                     → MxjxHandler
 *   mxjx/info                → MxjxInfoHandler
 *   mxjx/deep                → MxjxDeepHandler
 *   xiami_jx / xiami_jx/info → XiamiJxHandler
 *   skip                     → SkipHandler
 *   parse / parse/parse / jx / parse/info / jx/info → ParseHandler
 *
 * @package handlers
 * @since   5.14.0
 */
class HandlerDispatcher {

    /** action → handler 类名映射（含别名） */
    const MAP = [
        'moxi'           => 'MoxiHandler',
        'moxi/api'       => 'MoxiHandler',
        'mxjx'           => 'MxjxHandler',
        'mxjx/info'      => 'MxjxInfoHandler',
        'mxjx/deep'      => 'MxjxDeepHandler',
        'xiami_jx'       => 'XiamiJxHandler',
        'xiami_jx/info'  => 'XiamiJxHandler',
        'skip'           => 'SkipHandler',
        'parse'          => 'ParseHandler',
        'parse/parse'    => 'ParseHandler',
        'jx'             => 'ParseHandler',
        'parse/info'     => 'ParseHandler',
        'jx/info'        => 'ParseHandler',
    ];

    /**
     * 尝试分发。命中则调用 handler 的 handle() 并返回 true；未命中返回 false。
     *
     * @param string           $action
     * @param HandlersContext  $ctx
     * @return bool
     */
    public static function dispatch($action, HandlersContext $ctx) {
        $handlerClass = self::MAP[$action] ?? '';
        if (empty($handlerClass)) {
            return false;
        }

        if (!class_exists($handlerClass)) {
            require_once __DIR__ . '/' . $handlerClass . '.php';
        }
        if (!class_exists($handlerClass)) {
            throw new RuntimeException('handler 不存在: ' . $handlerClass);
        }

        $handler = new $handlerClass($ctx);
        if (!method_exists($handler, 'handle')) {
            throw new RuntimeException('handler 缺少 handle() 方法: ' . $handlerClass);
        }

        $handler->handle();
        return true;
    }
}
