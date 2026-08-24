#!/usr/bin/env node
/**
 * 沫兮官替 (MoxiGTI) 命令行入口
 *
 * 用法:
 *   node src/index.js current   查看当前版本
 *   node src/index.js bump      执行一次版本修订(维护/修改时 +1)
 *   node src/index.js history   查看版本变更日志
 */
import { loadManager, saveManager } from './state.js';

const cmd = process.argv[2] || 'current';
const reason = process.argv.slice(3).join(' ');

function showHeader() {
  console.log('==========================================');
  console.log('  沫兮官替 · MoxiGTI 版本管理框架');
  console.log('==========================================');
}

switch (cmd) {
  case 'current': {
    const mgr = loadManager();
    showHeader();
    console.log(`当前版本 : ${mgr.current}`);
    console.log(`修订计数 : #${mgr.revision}`);
    break;
  }
  case 'bump': {
    const mgr = loadManager();
    const entry = mgr.bump(reason);
    saveManager(mgr);
    showHeader();
    console.log(`版本修订 : ${entry.from} -> ${entry.to}`);
    if (entry.reason && entry.reason !== '(维护)') {
      console.log(`修订说明 : ${entry.reason}`);
    }
    console.log(`修订计数 : #${mgr.revision}`);
    console.log(`生成时间 : ${entry.at}`);
    break;
  }
  case 'history': {
    const mgr = loadManager();
    showHeader();
    console.log(`当前版本 : ${mgr.current}  |  累计修订 #${mgr.revision}`);
    console.log('------------------------------------------');
    if (mgr.log.length === 0) {
      console.log('(暂无比对历史)');
    } else {
      // 仅展示最近 N 条，避免刷屏可自行调整
      const recent = mgr.log.slice(-20);
      for (const e of recent) {
        console.log(`  ${e.from} -> ${e.to}  ${e.reason}  @${e.at}`);
      }
    }
    break;
  }
  default:
    console.log(`未知命令: ${cmd}`);
    console.log('可用命令: current | bump | history');
    process.exitCode = 1;
}