/**
 * 沫兮官替 · 核心版本管理框架 (MoxiGTI Core Version Manager)
 * -----------------------------------------------------------
 * 版本进位规则(100 进制 patch):
 *   初始版本为 v.0.0.1；
 *   patch 每维护/修改一次 +1；
 *   当 patch 累计到 100 时进一位 minor 并归零，即 v.0.0.100 -> v.0.1.0；
 *   随后 patch 继续从 1 递增：v.0.1.1 ... v.0.1.100 -> v.0.2.0，依次类推。
 *
 * 因此每个 minor 周期包含 101 个版本：{minor}.0(过渡位) + {minor}.1..100。
 */

/** 单个 minor 周期内的版本步长(含过渡位 .0) */
export const STEP = 100;

/** 版本元组 [major, minor, patch] */
export function parseVersion(str) {
  if (typeof str !== 'string') {
    throw new TypeError('版本字符串必须为字符串类型');
  }
  const match = /^v?\.?(\d+)\.(\d+)\.(\d+)$/.exec(str.trim());
  if (!match) {
    throw new Error(`无法解析版本号: ${str}，期望形如 v.0.0.1`);
  }
  return [Number(match[1]), Number(match[2]), Number(match[3])];
}

/** 从累计修订计数 N(=1,2,3...) 反推版本元组 */
export function versionFromRevision(rev) {
  if (!Number.isInteger(rev) || rev < 1) {
    throw new Error(`修订计数必须为 >= 1 的整数，收到: ${rev}`);
  }
  const minor = Math.floor(rev / (STEP + 1));
  let patch = rev % (STEP + 1); // 0..STEP
  // patch==0 表示恰好落在进位过渡位 v.0.{minor}.0
  return [0, minor, patch];
}

/** 版本元组 -> 展示字符串 v.MAJOR.MINOR.PATCH */
export function formatVersion(tuple) {
  return `v.${tuple[0]}.${tuple[1]}.${tuple[2]}`;
}

/**
 * 核心类 VersionManager
 * 维护当前版本，并提供 bump(维护/修订) 逻辑。
 */
export class VersionManager {
  constructor(major = 0, minor = 0, patch = 1) {
    this.version = [major, minor, patch];
    this.log = [];          // 版本变更日志
  }

  /** 当前版本字符串 */
  get current() {
    return formatVersion(this.version);
  }

  /** 当前累计修订计数 */
  get revision() {
    const [, minor, patch] = this.version;
    return minor * (STEP + 1) + patch;
  }

  /**
   * 执行一次版本修订(维护/修改)。
   * 可附加备注 reason，写入变更日志。
   */
  bump(reason = '') {
    let [major, minor, patch] = this.version;
    if (patch === STEP) {
      // 累计满 100：进一位 minor 并归零 -> v.0.{minor+1}.0
      minor += 1;
      patch = 0;
    } else {
      patch += 1;
    }
    const before = this.current;
    this.version = [major, minor, patch];
    const entry = {
      at: new Date().toISOString(),
      from: before,
      to: this.current,
      reason: reason || '(维护)'
    };
    this.log.push(entry);
    return entry;
  }

  /** 最近一条变更记录 */
  get lastEntry() {
    return this.log[this.log.length - 1];
  }

  /** 导出可持久化状态 */
  toJSON() {
    return {
      version: this.version,
      log: this.log
    };
  }

  /** 从持久化状态恢复 */
  static fromJSON(data) {
    const mgr = new VersionManager();
    if (data && Array.isArray(data.version) && data.version.length === 3) {
      mgr.version = data.version.map(Number);
    }
    if (data && Array.isArray(data.log)) {
      mgr.log = data.log;
    }
    return mgr;
  }
}