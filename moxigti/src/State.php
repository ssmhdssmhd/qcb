<?php
/**
 * 沫兮官替 · 状态持久化层
 */

declare(strict_types=1);

namespace MoxiGti;

use MoxiGti\Core\VersionManager;

/**
 * Class State
 * 负责读取/保存 .version.json 版本状态文件。
 */
class State
{
    /** 默认状态文件路径 */
    public static function defaultStateFile(): string
    {
        return __DIR__ . '/../.version.json';
    }

    /**
     * 读取持久化状态(文件不存在时返回初始 v.0.0.1)
     */
    public static function load(string $file = null): VersionManager
    {
        $file = $file ?? self::defaultStateFile();
        $mgr = new VersionManager();

        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            if ($raw !== false) {
                $data = json_decode($raw, true);
                if (is_array($data)) {
                    $mgr = VersionManager::fromArray($data);
                }
            }
        }

        return $mgr;
    }

    /**
     * 持久化当前状态到文件
     */
    public static function save(VersionManager $mgr, string $file = null): string
    {
        $file = $file ?? self::defaultStateFile();
        $dir = dirname($file);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $content = json_encode($mgr->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($file, $content, LOCK_EX);

        return $file;
    }
}