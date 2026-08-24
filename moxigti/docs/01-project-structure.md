# 沫兮官替 · 源码框架结构图(第 1 张)

> 本图展示 MoxiGTI 项目的整体模块划分与依赖关系。
> **第 2 张图为版本管理的核心框架图**，请参见 [02-core-framework.md](02-core-framework.md)。

## 图一 · 项目整体结构与模块依赖

```mermaid
graph TD
    CLI["CLI 入口</br>src/index.js</br>current / bump / history"] --> STATE["状态层</br>src/state.js</br>读写 .version.json"]
    CLI --> CORE["核心框架</br>src/core/version-manager.js"]

    CORE --> PARSE["parseVersion()</br>解析/校验版本串"]
    CORE --> REV["versionFromRevision()</br>修订计数 → 版本元组"]
    CORE --> FMT["formatVersion()</br>版本元组 → v.x.y.z"]
    CORE --> MGR["VersionManager</br>bump() 进位逻辑"]

    STATE --> STORE["state 持久化</br>.version.json</br>(version + log)"]

    TEST["单元测试</br>test/version-manager.test.js</br>node --test"] --> CORE

    subgraph DOCS["文档 (docs 目录)"]
        S1["01-project-structure</br>整体结构图</br>(本文件)"]
        S2["02-core-framework</br>★核心框架图</br>(第 2 张)"]
        S3["03-maintenance-flow</br>修订维护流程</br>(第 3 张)"]
    end
```

**说明**

- 核心框架 `src/core/version-manager.js` 是纯逻辑、零依赖，可被 CLI / 状态层 / 测试复用。
- 状态层 `src/state.js` 负责把版本与日志持久化到根目录 `.version.json`。
- CLI `src/index.js` 面向用户，提供 `current`、`bump`、`history` 三个命令。