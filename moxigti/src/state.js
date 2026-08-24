import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { VersionManager } from './core/version-manager.js';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const STATE_FILE = path.resolve(__dirname, '..', '.version.json');

export function defaultStateFile() {
  return STATE_FILE;
}

/** 读取持久化状态(文件不存在时返回初始 v.0.0.1) */
export function loadManager(file = STATE_FILE) {
  let mgr = new VersionManager(); // 初始 v.0.0.1
  if (fs.existsSync(file)) {
    try {
      const raw = JSON.parse(fs.readFileSync(file, 'utf8'));
      mgr = VersionManager.fromJSON(raw);
    } catch (err) {
      console.error(`[warn] 读取版本状态失败(${file})，将重置为初始版本:`, err.message);
    }
  }
  return mgr;
}

/** 持久化当前状态到文件 */
export function saveManager(mgr, file = STATE_FILE) {
  fs.mkdirSync(path.dirname(file), { recursive: true });
  fs.writeFileSync(file, JSON.stringify(mgr.toJSON(), null, 2) + '\n', 'utf8');
  return file;
}