<?php
/**
 * 模块元数据值对象：解析 manifest.php，做校验/依赖拓扑排序
 */

declare(strict_types=1);

namespace App\Modules\_Core\Loader;

class ModuleManifest {
    public string $id;
    public string $name;
    public string $version;
    public string $description = '';
    /** @var string[] 依赖模块 id 列表（被依赖的先启动） */
    public array $requires = [];
    /** @var string[] 可选依赖：存在就用，不存在不报错 */
    public array $suggests = [];
    /** 默认是否启用。若 config/modules.php 显式设置则以其为准 */
    public bool $defaultEnabled = true;
    /** 加载优先级：数字小先启动 */
    public int $priority = 100;
    /** 模块提供的能力标签，如 ['official_replace', 'resource_sites'] */
    public array $provides = [];
    /** 模块所在目录（绝对路径） */
    public string $baseDir;
    /** manifest 原始数组 */
    public array $raw;

    public function __construct(array $manifest, string $baseDir) {
        if (empty($manifest['id'])) throw new \InvalidArgumentException('manifest 缺少 id: ' . $baseDir);
        if (empty($manifest['name'])) $manifest['name'] = $manifest['id'];
        if (empty($manifest['version'])) $manifest['version'] = '1.0.0';
        $this->id = (string)$manifest['id'];
        $this->name = (string)$manifest['name'];
        $this->version = (string)$manifest['version'];
        $this->description = (string)($manifest['description'] ?? '');
        $this->requires = array_values(array_filter(array_map('strval', (array)($manifest['requires'] ?? []))));
        $this->suggests = array_values(array_filter(array_map('strval', (array)($manifest['suggests'] ?? []))));
        $this->defaultEnabled = (bool)($manifest['default_enabled'] ?? true);
        $this->priority = intval($manifest['priority'] ?? 100);
        $this->provides = array_values(array_filter(array_map('strval', (array)($manifest['provides'] ?? [$this->id]))));
        $this->baseDir = rtrim($baseDir, '/');
        $this->raw = $manifest;
    }

    public function getBootstrapFile(): string {
        return $this->baseDir . '/Bootstrap.php';
    }

    public function getConfigFile(): string {
        return $this->baseDir . '/config.php';
    }

    public function getRoutesFile(): string {
        return $this->baseDir . '/routes.php';
    }

    public function getHealthCheckClass(): string {
        $ns = 'App\\Modules\\' . str_replace('/', '\\', $this->id) . '\\SecurityCheck\\Health';
        return str_replace('\\_Core\\', '\\_Core\\', $ns);
    }
}
