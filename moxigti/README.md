# 沫兮官替 · MoxiGTI

一个基于 **Node.js** 的版本管理框架。核心是一套 **100 进制 patch 进位**的版本号演进规则，
用多张结构图表达源码框架与思路，其中**第 2 张为核心框架图**。

> 当前版本：`v.0.0.1`（初始版本） · 修订计数：`#1`

---

## 版本规则

- 初始版本为 **`v.0.0.1`**。
- 每次维护/修改代码执行一次 `bump()`，`patch` +1。
- 当 `patch` 累计到 **100** 时进一位 `minor` 并归零：`v.0.0.100 → v.0.1.0`。
- 随后 `patch` 从 1 重新递增，依次类推（每个 `minor` 周期含 101 个版本）。

```
v.0.0.1 ... v.0.0.100 → v.0.1.0 → v.0.1.1 ... v.0.1.100 → v.0.2.0 → ...
```

---

## 项目结构

```
moxigti/
├── package.json                 # 项目配置与脚本
├── src/
│   ├── index.js                 # CLI 入口 (current / bump / history)
│   ├── state.js                 # 状态层：读写 .version.json
│   └── core/
│       └── version-manager.js   # ★核心框架：版本进位逻辑
├── test/
│   └── version-manager.test.js  # 单元测试 (node --test)
├── docs/
│   ├── 01-project-structure.md  # 第1张 · 整体结构图
│   ├── 02-core-framework.md     # ★第2张 · 核心框架图
│   └── 03-maintenance-flow.md   # 第3张 · 修订维护流程
├── CHANGELOG.md                 # 版本更新日志(自动维护)
└── README.md
```

---

## 快速开始

```bash
cd moxigti

# 查看当前版本
npm run current

# 执行一次版本修订(维护/修改后执行)
npm run bump

# 查看版本演进历史
npm run history

# 运行单元测试
npm test
```

### CLI 用法

```bash
node src/index.js current                 # 查看当前版本
node src/index.js bump "新增XX功能"        # 修订+1(可带说明)
node src/index.js history                 # 查看变更日志
```

---

## 核心 API

```js
import { VersionManager } from './src/core/version-manager.js';

const mgr = new VersionManager();       // 初始 v.0.0.1
mgr.bump();                             // v.0.0.2
console.log(mgr.current);               // v.0.0.2
console.log(mgr.revision);              // 2
```

| API | 说明 |
| --- | --- |
| `new VersionManager(major, minor, patch)` | 创建管理器，默认 `0, 0, 1` |
| `mgr.current` | 当前版本字符串 `v.x.y.z` |
| `mgr.revision` | 累计修订计数 |
| `mgr.bump(reason?)` | 执行一次修订，返回变更记录 |
| `mgr.log` / `mgr.lastEntry` | 变更日志 / 最近一条 |
| `versionFromRevision(rev)` | 修订计数 → 版本元组 |
| `parseVersion(str)` / `formatVersion(tuple)` | 解析 / 格式化 |

---

## 图表说明

本项目用 **3 张 Mermaid 结构图**表达源码框架与思路：

| 图表 | 文件 | 说明 |
| --- | --- | --- |
| 第 1 张 | `docs/01-project-structure.md` | 整体模块结构与依赖 |
| **第 2 张(核心)** | `docs/02-core-framework.md` | **版本进位核心框架/状态机** |
| 第 3 张 | `docs/03-maintenance-flow.md` | 维护修订流程 |

---

## 相关文档

- [第 1 张 · 整体结构图](docs/01-project-structure.md)
- [★ 第 2 张 · 核心框架图](docs/02-core-framework.md)
- [第 3 张 · 修订维护流程](docs/03-maintenance-flow.md)
- [版本更新日志](CHANGELOG.md)