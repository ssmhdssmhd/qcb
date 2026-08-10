<?php
/**
 * 模块化自检脚本：跑一次验证 Loader 框架 + 模块依赖拓扑 + 健康检查全部 OK
 * 用法：php diagnose_modules.php [--only=xxx,yyy] [--except=zzz]
 */

declare(strict_types=1);

$rootDir = __DIR__;
require_once $rootDir . '/modules/_core/autoload.php';

$onlyIds = null; $exceptIds = null;
foreach ($argv as $a) {
    if (str_starts_with($a, '--only=')) $onlyIds = explode(',', substr($a,7));
    if (str_starts_with($a, '--except=')) $exceptIds = explode(',', substr($a,9));
}

use App\Modules\_Core\Loader\ModuleLoader;

$loader = ModuleLoader::getInstance($rootDir . '/modules');
$loader->boot($onlyIds, $exceptIds);

echo "┌──────────────────────────────────────────────────────┐\n";
echo "│  🧩 模块化自检 (kzfz 分支 v5.11.0)                      │\n";
echo "└──────────────────────────────────────────────────────┘\n\n";

echo "【1/5】 模块扫描结果：" . count($loader->allManifests()) . " 个模块找到\n";
echo str_repeat('─', 60) . "\n";
foreach ($loader->allManifests() as $id => $m) {
    $enabled = $loader->isEnabled($id) ? '✅ 启用' : '⬜ 禁用';
    printf("  %-22s v%-8s pri=%-3d %s  依赖: %s\n",
        $id, $m->version, $m->priority, $enabled,
        $m->requires ? implode(',', $m->requires) : '(无)');
}

echo "\n【2/5】 用户开关配置文件：{$loader->getModulesConfigFile()}\n";
echo str_repeat('─', 60) . "\n";
if (file_exists($loader->getModulesConfigFile())) {
    $cfg = include $loader->getModulesConfigFile();
    foreach (($cfg['modules'] ?? []) as $id => $on) {
        echo "  {$id}: " . ($on ? 'true ' : 'false') . "\n";
    }
} else {
    echo "  (不存在，默认全部按 manifest.defaultEnabled)\n";
}

echo "\n【3/5】 拓扑启动顺序（按依赖 + priority）\n";
echo str_repeat('─', 60) . "\n";
$i = 0;
foreach ($loader->enabledManifests() as $id => $m) {
    $i++;
    echo "  {$i}. {$id}  priority={$m->priority}\n";
}

echo "\n【4/5】 被禁用的模块 & 原因\n";
echo str_repeat('─', 60) . "\n";
$byUser = $loader->getDisabledByUser(); $missDeps = $loader->getDisabledMissingDeps();
if (!$byUser && !$missDeps) {
    echo "  ✅ 全部模块已启用（无禁用）\n";
}
foreach ($byUser as $id => $reason) echo "  🔕 {$id}: {$reason}\n";
foreach ($missDeps as $id => $miss) echo "  ⚠️  {$id}: 缺少依赖 [" . implode(', ', $miss) . "]\n";

echo "\n【5/5】 启用模块健康检查\n";
echo str_repeat('─', 60) . "\n";
$health = $loader->healthAll();
foreach ($health['modules'] as $id => $h) {
    $ok = !empty($h['healthy']) ? '🟢' : '🔴';
    $detail = [];
    foreach (($h['details'] ?? []) as $k => $v) $detail[] = "{$k}=" . (is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v);
    echo "  {$ok} {$id}: " . ($detail ? implode('; ', $detail) : 'OK') . "\n";
}
if (!empty($health['warnings'])) {
    echo "\n⚠️  系统告警：\n";
    foreach ($health['warnings'] as $w) echo "  - {$w}\n";
}

echo "\n──────────────────────────────────────────────────────\n";
echo $health['healthy'] ? "✅ 模块化框架自检 **全部通过**！" : "⚠️  部分模块健康异常，请检查上方 🔴 项";
echo "\n──────────────────────────────────────────────────────\n";
exit($health['healthy'] ? 0 : 2);
