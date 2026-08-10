<?php
/**
 * 模块化命名空间自动加载器（PSR-4 风格）
 * 注册到 spl_autoload_register 后：
 *   use App\Modules\resource_sites\Bootstrap;
 *   => 自动加载 /workspace/modules/resource_sites/Bootstrap.php
 *
 * 用法（mx.php 顶部）：
 *   require_once __DIR__ . '/modules/_core/autoload.php';
 *   $loader = App\Modules\_Core\Loader\ModuleLoader::getInstance(__DIR__ . '/modules');
 *   $loader->boot();
 */

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\Modules\\';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    // 修正：_core 首字母大写不匹配目录名的问题：把 _Core 转成 _core
    $relative = preg_replace_callback('/^_Core\\\\/', fn($_m) => '_core\\', $relative, 1);
    $file = dirname(__DIR__, 2) . '/modules/' . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) require_once $file;
}, true, true);
