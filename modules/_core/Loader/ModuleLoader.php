<?php
/**
 * =============================================================
 * 模块加载器（单例）
 * =============================================================
 * 职责：
 *  1. 扫描 modules/* 目录，加载每个模块的 manifest.php
 *  2. 读取 config/modules.php 的模块开关配置（用户决定启用/禁用）
 *  3. 做依赖检查 + 拓扑排序（按 requires 和 priority）
 *  4. 依次实例化 Bootstrap.php，调用 bootstrap()
 *  5. 在请求入口（mx.php/mxadmin.php/gx.php）调用 onRequest() 分发给对应模块
 *  6. 提供：enabledModules() / get($id) / routes() / healthAll() 等查询 API
 *
 * 设计原则：
 *  - 模块禁用时，连 require_once 都不会做，纯"零成本"跳过
 *  - 依赖缺失的模块不会 fatal，而是标记 "disabled_missing_deps" 并进入日志
 *  - 对旧代码 100% 兼容：不启用 Loader 时，原 require_once 链路完全不受影响
 *    （渐进式迁移：旧入口可以先手动 $loader->loadModules(['core','resource_sites']) 跑）
 */

declare(strict_types=1);

namespace App\Modules\_Core\Loader;

class ModuleLoader {
    private static ?self $instance = null;
    private string $modulesRoot;
    private string $modulesConfigFile;

    /** @var ModuleManifest[] 扫描到的全部模块（含禁用的） */
    private array $manifests = [];
    /** @var ModuleManifest[] 经过依赖校验&开关判定后 实际启用的模块（按拓扑序） */
    private array $enabled = [];
    /** @var array<string, object> 已实例化的 Bootstrap */
    private array $bootstraps = [];
    /** @var array<string, bool> 用户开关配置 id=>bool */
    private array $userEnabledConfig = [];
    /** @var array<string, string[]> id => [缺失的依赖id] */
    private array $disabledMissingDeps = [];
    /** @var array<string, string> id => 禁用原因 */
    private array $disabledByUser = [];
    private bool $bootstrapped = false;

    public static function getInstance(?string $modulesRoot = null): self {
        if (self::$instance === null) {
            self::$instance = new self($modulesRoot ?: dirname(__DIR__, 2));
        }
        return self::$instance;
    }

    private function __construct(string $modulesRoot) {
        $this->modulesRoot = rtrim($modulesRoot, '/');
        // modulesConfigFile：和 modules 目录同级的 config/modules.php （即 /workspace/config/modules.php）
        $projectRoot = dirname($this->modulesRoot);
        $this->modulesConfigFile = $projectRoot . '/config/modules.php';
    }

    /** 读 config/modules.php（不存在就创建默认，全走 manifest.defaultEnabled） */
    public function loadUserConfig(): void {
        if (file_exists($this->modulesConfigFile)) {
            $cfg = @include $this->modulesConfigFile;
            if (is_array($cfg) && !empty($cfg['modules']) && is_array($cfg['modules'])) {
                foreach ($cfg['modules'] as $id => $on) {
                    if (is_bool($on) || in_array($on, [0,1,'0','1',true,false], true)) {
                        $this->userEnabledConfig[(string)$id] = (bool)$on;
                    }
                }
            }
        }
    }

    /**
     * 扫描 modules/*，收集 manifest。
     * 对 modules/_core 跳过（核心不是业务模块）
     */
    public function scan(): void {
        $this->manifests = [];
        $dirs = glob($this->modulesRoot . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $slug = basename($dir);
            if ($slug === '_core') continue;
            $manifestFile = $dir . '/manifest.php';
            if (!file_exists($manifestFile)) continue;
            $manifestArr = @include $manifestFile;
            if (!is_array($manifestArr)) continue;
            try {
                $m = new ModuleManifest($manifestArr, $dir);
                $this->manifests[$m->id] = $m;
            } catch (\Throwable $e) {
                error_log('[ModuleLoader] manifest 解析失败: ' . $manifestFile . ' -> ' . $e->getMessage());
            }
        }
    }

    /**
     * 计算最终启用列表 + 拓扑排序
     * 规则：
     *   1) config/modules.php 显式 false 的 -> disabled
     *   2) manifest.defaultEnabled=false 且 配置未指定 true -> disabled
     *   3) requires 里有未启用/不存在的 id -> disabled_missing_deps
     *   4) 剩下的按 priority ASC + 依赖先于 被依赖 做 Kahn 拓扑
     */
    public function resolveEnabled(): array {
        $this->enabled = [];
        $this->disabledMissingDeps = [];
        $this->disabledByUser = [];

        // (a) 先按用户开关+defaultEnabled 判定第一阶段通过名单
        $stage1 = [];
        foreach ($this->manifests as $id => $m) {
            if (array_key_exists($id, $this->userEnabledConfig)) {
                $on = $this->userEnabledConfig[$id];
                if (!$on) {
                    $this->disabledByUser[$id] = 'config/modules.php 中显式禁用';
                    continue;
                }
                $stage1[$id] = $m;
                continue;
            }
            if (!$m->defaultEnabled) {
                $this->disabledByUser[$id] = 'manifest.defaultEnabled=false 且未在配置中启用';
                continue;
            }
            $stage1[$id] = $m;
        }

        // (b) 依赖检查：requires 必须全部在 stage1 或 manifests 里且在 stage1
        $passIds = array_keys($stage1);
        foreach ($stage1 as $id => $m) {
            $missing = [];
            foreach ($m->requires as $dep) {
                if (!in_array($dep, $passIds, true)) $missing[] = $dep;
            }
            if ($missing) {
                $this->disabledMissingDeps[$id] = $missing;
                unset($stage1[$id]);
            }
        }

        // (c) Kahn 拓扑排序：先 priority，再依赖顺序
        $inDegree = [];
        $adj = [];
        foreach ($stage1 as $id => $m) { $inDegree[$id] = 0; $adj[$id] = []; }
        foreach ($stage1 as $id => $m) {
            foreach ($m->requires as $dep) {
                if (isset($stage1[$dep])) {
                    $adj[$dep][] = $id;
                    $inDegree[$id]++;
                }
            }
        }
        // 优先级队列：priority ASC 再 id ASC
        $pq = new \SplPriorityQueue();
        $serial = 0;
        foreach ($inDegree as $id => $d) {
            if ($d === 0) {
                $pri = - ($stage1[$id]->priority * 100000 + $serial++);
                $pq->insert($id, $pri);
            }
        }
        $sorted = [];
        while (!$pq->isEmpty()) {
            $id = $pq->extract();
            $sorted[] = $id;
            foreach ($adj[$id] as $nextId) {
                $inDegree[$nextId]--;
                if ($inDegree[$nextId] === 0) {
                    $pri = - ($stage1[$nextId]->priority * 100000 + $serial++);
                    $pq->insert($nextId, $pri);
                }
            }
        }
        // 环里残留的，丢到最后（一般不会有）
        foreach ($inDegree as $id => $d) {
            if ($d > 0 && !in_array($id, $sorted, true)) $sorted[] = $id;
        }

        foreach ($sorted as $id) $this->enabled[$id] = $stage1[$id];
        return $this->enabled;
    }

    /**
     * 全流程入口：扫描 -> 读配置 -> 解析 -> 实例化 Bootstrap -> bootstrap()
     * 调用点：mx.php / mxadmin.php / gx.php 主流程的 try 开头
     */
    public function boot(array $onlyIds = null, array $exceptIds = null): array {
        if ($this->bootstrapped) return $this->enabled;
        $this->loadUserConfig();
        $this->scan();
        $this->resolveEnabled();
        if ($onlyIds !== null) $onlyIds = array_flip(array_map('strval', $onlyIds));
        if ($exceptIds !== null) $exceptIds = array_flip(array_map('strval', $exceptIds));

        foreach ($this->enabled as $id => $manifest) {
            if ($onlyIds !== null && !isset($onlyIds[$id])) continue;
            if ($exceptIds !== null && isset($exceptIds[$id])) continue;
            $file = $manifest->getBootstrapFile();
            if (!file_exists($file)) continue; // 纯声明式模块（只提供 manifest/routes/config）
            require_once $file;
            $cls = 'App\\Modules\\' . str_replace('/', '\\', $id) . '\\Bootstrap';
            if (!class_exists($cls)) {
                error_log("[ModuleLoader] Bootstrap 类缺失: $cls (期望 $file)");
                continue;
            }
            try {
                $obj = new $cls($manifest);
                $obj->bootstrap();
                $this->bootstraps[$id] = $obj;
            } catch (\Throwable $e) {
                error_log("[ModuleLoader] {$id} bootstrap() FATAL: " . $e->getMessage());
            }
        }
        $this->bootstrapped = true;
        return $this->enabled;
    }

    /** 入口请求分发：给每个已启用模块发 onRequest */
    public function dispatchRequest(string $entryPoint, ?string $action): void {
        foreach ($this->bootstraps as $id => $bs) {
            try { $bs->onRequest($entryPoint, $action); }
            catch (\Throwable $e) { error_log("[ModuleLoader][$id] onRequest: ".$e->getMessage()); }
        }
    }

    /** 收集所有 enabled 模块的 routes.php（返回路由数组：action => callable） */
    public function collectRoutes(): array {
        $routes = [];
        foreach ($this->enabled as $id => $m) {
            $f = $m->getRoutesFile();
            if (!file_exists($f)) continue;
            $r = include $f;
            if (is_array($r)) $routes = array_merge($routes, $r);
        }
        return $routes;
    }

    /** 整体健康体检：每个启用模块健康检查 + 依赖告警 */
    public function healthAll(): array {
        $out = ['healthy'=>true, 'modules'=>[], 'warnings'=>[]];
        foreach ($this->disabledMissingDeps as $id => $miss) {
            $out['warnings'][] = "模块[$id] 因缺少依赖(".implode(',',$miss).") 未启用";
        }
        foreach ($this->bootstraps as $id => $bs) {
            try {
                $h = $bs->healthCheck();
                $out['modules'][$id] = $h;
                if (empty($h['healthy'])) $out['healthy'] = false;
            } catch (\Throwable $e) {
                $out['modules'][$id] = ['healthy'=>false,'error'=>$e->getMessage()];
                $out['healthy'] = false;
            }
        }
        return $out;
    }

    // ---- 查询 API ----
    public function allManifests(): array { return $this->manifests; }
    public function enabledManifests(): array { return $this->enabled; }
    public function getManifest(string $id): ?ModuleManifest { return $this->manifests[$id] ?? null; }
    public function getBootstrap(string $id): ?object { return $this->bootstraps[$id] ?? null; }
    public function isEnabled(string $id): bool { return isset($this->enabled[$id]); }
    public function getDisabledByUser(): array { return $this->disabledByUser; }
    public function getDisabledMissingDeps(): array { return $this->disabledMissingDeps; }
    public function getModulesConfigFile(): string { return $this->modulesConfigFile; }
    public function getModulesRoot(): string { return $this->modulesRoot; }

    /**
     * 辅助：把用户模块开关写回 config/modules.php（后台模块化管理页面用）
     */
    public function saveUserConfig(array $idToBool): bool {
        $dir = dirname($this->modulesConfigFile);
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $content = "<?php\n/**\n * 模块化开关配置（由后台「模块管理」页面写入）\n * 不需要的模块设为 false 即可零成本关闭，维护无忧\n */\nreturn [\n    'modules' => [\n";
        foreach ($this->manifests as $id => $m) {
            $on = $idToBool[$id] ?? $m->defaultEnabled;
            $content .= sprintf("        '%s' => %s, // %s v%s\n",
                $id, $on ? 'true ' : 'false', addcslashes($m->name, "'"), $m->version);
        }
        $content .= "    ],\n];\n";
        $ok = @file_put_contents($this->modulesConfigFile, $content, LOCK_EX);
        if ($ok) { @chmod($this->modulesConfigFile, 0644); return true; }
        return false;
    }
}
