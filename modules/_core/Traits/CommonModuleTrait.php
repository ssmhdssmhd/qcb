<?php
/**
 * 通用模块 Trait：实现 ModuleInterface 的默认空实现
 * 业务模块 use 这个 Trait 就只需要重写关心的方法，不用全量写 5 个空函数。
 */

declare(strict_types=1);

namespace App\Modules\_Core\Traits;

use App\Modules\_Core\Loader\ModuleManifest;

trait CommonModuleTrait {
    protected ModuleManifest $manifest;

    public function __construct(ModuleManifest $m) { $this->manifest = $m; }
    public function getId(): string { return $this->manifest->id; }
    public function bootstrap(): void { /* 默认空 */ }
    public function onRequest(string $entryPoint, ?string $action): void { /* 默认空 */ }
    public function healthCheck(): array { return ['healthy' => true, 'details' => []]; }
    public function install(): array { return ['success' => true, 'message' => $this->manifest->name . ' 无需安装']; }
    public function uninstall(bool $force = false): array { return ['success' => true, 'message' => $this->manifest->name . ' 无需清理']; }
}
