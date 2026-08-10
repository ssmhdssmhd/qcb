<?php
/**
 * 资源站模块 Bootstrap
 *  - 启用时：注册资源站相关 API 路由到 mx.php 的 switch 分发
 *  - 禁用时：Loader 不会 require 本文件，所有资源站 CRUD API 自动 404（零侵入，不会出错）
 */

declare(strict_types=1);

namespace App\Modules\resource_sites;

use App\Modules\_Core\Contracts\ModuleInterface;
use App\Modules\_Core\Traits\CommonModuleTrait;

class Bootstrap implements ModuleInterface {
    use CommonModuleTrait;

    public function bootstrap(): void {
        // 【v0 骨架】演示：注册资源站路由（实际集成到 mx.php/ModuleLoader::collectRoutes）
        // 这里只做轻量钩子，不做 DB/IO 重活
        if (!defined('MODULE_RESOURCE_SITES_LOADED')) {
            define('MODULE_RESOURCE_SITES_LOADED', true);
        }
    }

    public function onRequest(string $entryPoint, ?string $action): void {
        // 比如 gx.php?action=site_check 时，这里可以挂载进度钩子
    }

    public function healthCheck(): array {
        // 健康检查示例：统计资源站总数/活跃数
        $total = 0; $active = 0;
        if (class_exists(\DbResourceSiteManager::class)) {
            try {
                $sm = new \DbResourceSiteManager();
                $all = $sm->getAllSites(true);
                $total = count($all);
                foreach ($all as $s) if (($s['status'] ?? '') === 'active') $active++;
            } catch (\Throwable $e) {}
        } elseif (class_exists(\ResourceSiteManager::class)) {
            try {
                $sm = new \ResourceSiteManager();
                $all = $sm->getAllSites();
                $total = count($all);
                foreach ($all as $s) if (($s['status'] ?? '') === 'active') $active++;
            } catch (\Throwable $e) {}
        }
        return [
            'healthy' => $total > 0,
            'details' => [
                '总数'   => $total,
                '活跃'   => $active,
                '禁用'   => $total - $active,
                '实现'   => class_exists(\DbResourceSiteManager::class, false) ? 'DB存储' : (class_exists(\ResourceSiteManager::class, false) ? '文件存储' : '未加载'),
            ],
        ];
    }

    public function install(): array {
        // 迁移建表/默认抖剧TV资源站初始化（已有就幂等跳过）
        try {
            if (method_exists(\DataMigration::class, 'migrateResourceSites')) {
                \DataMigration::migrateResourceSites();
            }
            return ['success' => true, 'message' => '资源站模块初始化完成'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
