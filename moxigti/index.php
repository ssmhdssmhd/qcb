#!/usr/bin/env php
<?php
/**
 * 沫兮官替 (MoxiGTI) 命令行入口
 *
 * 用法:
 *   php index.php current   查看当前版本
 *   php index.php bump      执行一次版本修订(维护/修改时 +1)
 *   php index.php history   查看版本变更日志
 */

declare(strict_types=1);

require_once __DIR__ . '/src/Core/VersionManager.php';
require_once __DIR__ . '/src/State.php';

use MoxiGti\Core\VersionManager;
use MoxiGti\State;

$cmd = $argv[1] ?? 'current';
$reason = isset($argv[2]) ? implode(' ', array_slice($argv, 2)) : '';

function showHeader(): void
{
    echo "==========================================\n";
    echo "  沫兮官替 · MoxiGTI 版本管理框架 (PHP)\n";
    echo "==========================================\n";
}

switch ($cmd) {
    case 'current': {
        $mgr = State::load();
        showHeader();
        echo "当前版本 : " . $mgr->current() . "\n";
        echo "修订计数 : #" . $mgr->revision() . "\n";
        break;
    }

    case 'bump': {
        $mgr = State::load();
        $entry = $mgr->bump($reason !== '' ? $reason : '(维护)');
        State::save($mgr);
        showHeader();
        echo "版本修订 : {$entry['from']} -> {$entry['to']}\n";
        if ($entry['reason'] !== '(维护)') {
            echo "修订说明 : {$entry['reason']}\n";
        }
        echo "修订计数 : #" . $mgr->revision() . "\n";
        echo "生成时间 : {$entry['at']}\n";
        break;
    }

    case 'history': {
        $mgr = State::load();
        showHeader();
        echo "当前版本 : " . $mgr->current() . "  |  累计修订 #{$mgr->revision()}\n";
        echo "------------------------------------------\n";

        $log = $mgr->log();
        if (count($log) === 0) {
            echo "(暂无比对历史)\n";
        } else {
            $recent = array_slice($log, -20);
            foreach ($recent as $e) {
                echo "  {$e['from']} -> {$e['to']}  {$e['reason']}  @{$e['at']}\n";
            }
        }
        break;
    }

    default:
        echo "未知命令: {$cmd}\n";
        echo "可用命令: current | bump | history\n";
        exit(1);
}