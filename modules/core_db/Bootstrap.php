<?php

declare(strict_types=1);

namespace App\Modules\core_db;

use App\Modules\_Core\Contracts\ModuleInterface;
use App\Modules\_Core\Traits\CommonModuleTrait;

class Bootstrap implements ModuleInterface {
    use CommonModuleTrait;

    public function bootstrap(): void {
        // 按需加载 db/autoload.php（用户可能用文件存储降级，这里不强制 require）
        if (!defined('MODULE_CORE_DB_LOADED')) define('MODULE_CORE_DB_LOADED', true);
    }

    public function install(): array {
        // 建表：执行 DataMigration->migrateAll()（幂等）
        try {
            if (file_exists(__DIR__ . '/../../db/Database.php')) {
                require_once __DIR__ . '/../../db/Database.php';
                require_once __DIR__ . '/../../db/DataMigration.php';
                $db = \Database::getInstance();
                if (method_exists(\DataMigration::class, 'migrateAll')) {
                    $m = new \DataMigration($db);
                    if (method_exists($m, 'migrateAll')) $m->migrateAll();
                    elseif (method_exists($m, 'runAll')) $m->runAll();
                    elseif (method_exists($m, 'migrate')) $m->migrate();
                }
                return ['success' => true, 'message' => '数据库 schema 迁移完成'];
            }
            return ['success' => false, 'message' => 'Database.php 不存在'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function healthCheck(): array {
        $ok = false; $detail = [];
        try {
            if (class_exists(\Database::class)) {
                $db = \Database::getInstance();
                $ok = $db->tableExists('sys_config');
                $detail['sys_config表'] = $ok ? '存在' : '缺失';
                $tables = ['resource_sites','domain_rules','official_platforms','official_replace_sites'];
                foreach ($tables as $t) $detail[$t] = $db->tableExists($t) ? '存在' : '缺失';
            } else {
                $detail = ['未加载 Database 类'];
            }
        } catch (\Throwable $e) { $detail = ['error' => $e->getMessage()]; }
        return ['healthy' => $ok, 'details' => $detail];
    }
}
