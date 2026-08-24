<?php
/**
 * 沫兮官替 · 单元测试
 * 运行: php test/VersionManagerTest.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Core/VersionManager.php';
require_once __DIR__ . '/../src/State.php';

use MoxiGti\Core\VersionManager;
use MoxiGti\State;

$passed = 0;
$failed = 0;

function assertEq(mixed $expected, mixed $actual, string $msg): void
{
    global $passed, $failed;
    $ok = $expected === $actual;
    if ($ok) {
        $passed++;
        echo "✔ {$msg}\n";
    } else {
        $failed++;
        echo "✘ {$msg}\n";
        echo "  期望: " . var_export($expected, true) . "\n";
        echo "  实际: " . var_export($actual, true) . "\n";
    }
}

function assertThrows(callable $fn, string $exceptionClass, string $msg): void
{
    global $passed, $failed;
    try {
        $fn();
        $failed++;
        echo "✘ {$msg} (未抛出异常)\n";
    } catch (\Throwable $e) {
        if ($e instanceof $exceptionClass) {
            $passed++;
            echo "✔ {$msg}\n";
        } else {
            $failed++;
            echo "✘ {$msg} (抛出了 " . get_class($e) . ")\n";
        }
    }
}

echo "===== 沫兮官替 · MoxiGTI 单元测试 (PHP) =====\n\n";

// ===== 初始版本 =====
$mgr = new VersionManager();
assertEq('v.0.0.1', $mgr->current(), '初始版本为 v.0.0.1');
assertEq(1, $mgr->revision(), '初始 revision 为 1');

// ===== patch 递增 =====
$mgr->bump();
assertEq('v.0.0.2', $mgr->current(), 'patch 递增: v.0.0.1 -> v.0.0.2');

// ===== 累计到 \MoxiGti\Core\STEP =====
$mgr = new VersionManager();
for ($i = 1; $i < \MoxiGti\Core\STEP; $i++) {
    $mgr->bump();
}
assertEq('v.0.0.100', $mgr->current(), '累计到100仍为 v.0.0.100');

// ===== 满100进位 =====
$mgr = new VersionManager();
for ($i = 0; $i < \MoxiGti\Core\STEP; $i++) {
    $mgr->bump();
}
assertEq('v.0.1.0', $mgr->current(), '满100进位为 v.0.1.0');

// ===== 进位后继续递增 =====
$mgr->bump();
assertEq('v.0.1.1', $mgr->current(), '进位后从 v.0.1.1 继续递增');

// ===== 再一组进位 =====
for ($i = 0; $i < \MoxiGti\Core\STEP; $i++) {
    $mgr->bump();
}
assertEq('v.0.2.0', $mgr->current(), '再一组100进位为 v.0.2.0');

// ===== revision 连续性 =====
$mgr = new VersionManager();
$revisions = [];
for ($i = 0; $i < 250; $i++) {
    $mgr->bump();
    $revisions[] = $mgr->revision();
}
$expectedRevs = range(2, 251);
assertEq($expectedRevs, $revisions, 'revision 与 bump 保持连续一致');

// ===== versionFromRevision 一致性 =====
$mgr = new VersionManager();
$consistent = true;
for ($i = 0; $i < 350; $i++) {
    $expected = $mgr->current();
    $tuple = VersionManager::versionFromRevision($mgr->revision());
    $actual = VersionManager::formatVersion($tuple);
    if ($actual !== $expected) {
        $consistent = false;
        break;
    }
    $mgr->bump();
}
assertEq(true, $consistent, 'versionFromRevision 与 VersionManager 版本一致');

// ===== parseVersion 校验 =====
assertThrows(fn() => VersionManager::parseVersion('abc'), \InvalidArgumentException::class, 'parseVersion: 非法输入抛异常');
assertThrows(fn() => VersionManager::parseVersion('v1.0'), \InvalidArgumentException::class, 'parseVersion: 格式不足抛异常');

$tuple = VersionManager::parseVersion('v.1.2.3');
assertEq([1, 2, 3], $tuple, 'parseVersion: 正确解析 v.1.2.3');

$tuple2 = VersionManager::parseVersion('0.0.1');
assertEq([0, 0, 1], $tuple2, 'parseVersion: 兼容无v前缀');

// ===== bump 日志 =====
$mgr = new VersionManager();
$entry = $mgr->bump('新增图表功能');
assertEq('v.0.0.1', $entry['from'], 'bump 日志 from 正确');
assertEq('v.0.0.2', $entry['to'], 'bump 日志 to 正确');
assertEq('新增图表功能', $entry['reason'], 'bump 日志 reason 正确');
assertEq($entry, $mgr->lastEntry(), 'bump 日志 lastEntry 一致');

// ===== 状态持久化 =====
$testFile = __DIR__ . '/../.version.test.json';
@unlink($testFile);

$mgr = new VersionManager();
$mgr->bump('测试持久化');
State::save($mgr, $testFile);

$restored = State::load($testFile);
assertEq('v.0.0.2', $restored->current(), '状态持久化: 读取版本正确');
assertEq('测试持久化', $restored->lastEntry()['reason'] ?? '', '状态持久化: 日志恢复正确');

@unlink($testFile);

// ===== 修订历史 =====
$mgr = new VersionManager();
$mgr->bump('第一版');
$mgr->bump('第二版');
$log = $mgr->log();
assertEq(2, count($log), '日志条目数量正确');

echo "\n===== 结果: 通过 {$passed} / " . ($passed + $failed) . ($failed > 0 ? "，失败 {$failed}" : "") . " =====\n";
exit($failed > 0 ? 1 : 0);