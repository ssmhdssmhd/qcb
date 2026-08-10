<?php
/**
 * =============================================================
 * 模块化核心 —— 模块契约接口
 * =============================================================
 * 所有需要被 ModuleLoader 识别和管理的业务模块，都必须：
 *   1. 在模块根目录放置 manifest.php（元数据）
 *   2. 提供一个 Bootstrap 类实现本接口
 *
 * 模块目录约定：
 *   modules/{module_slug}/
 *     ├── manifest.php          ← 元数据（必填）：id/名字/版本/依赖/开关/权重
 *     ├── Bootstrap.php         ← 实现 ModuleInterface（可选，若只有 manifest 声明则认为是"纯配置模块"）
 *     ├── Setup/                ← 迁移/安装脚本（可选）
 *     │     ├── Install.php
 *     │     └── Uninstall.php
 *     ├── SecurityCheck/        ← 自检/健康检查（可选）
 *     │     └── Health.php
 *     ├── config.php            ← 默认配置（可选）
 *     └── routes.php            ← 自定义 API 路由（可选）
 */

declare(strict_types=1);

namespace App\Modules\_Core\Contracts;

interface ModuleInterface {
    /**
     * 模块ID：与 manifest.php 中的 id 保持一致
     */
    public function getId(): string;

    /**
     * 模块引导：被 Loader 加载后立即调用（注册路由、绑定容器、挂载事件钩子）
     * 注意：此阶段不要做 DB/IO 重活，重活放到 onRequest 或 lazy service 上
     */
    public function bootstrap(): void;

    /**
     * 请求级初始化：仅当模块实际被请求路径命中时才调用
     * （mxadmin.php/gx.php/mx.php 等入口都会在路由分发前调用）
     */
    public function onRequest(string $entryPoint, ?string $action): void;

    /**
     * 健康自检：返回 ['healthy'=>bool, 'details'=>[...]]
     */
    public function healthCheck(): array;

    /**
     * 安装：建表/写入默认配置
     */
    public function install(): array; // ['success'=>bool,'message'=>string]

    /**
     * 卸载：清理表/配置（但保留用户数据，除非显式 force）
     */
    public function uninstall(bool $force = false): array;
}
