# 沫兮官替 · 修订/维护流程(第 3 张)

> 展示"每次维护或修改代码时如何递增版本"的完整流程。
> 核心版本框架见 [02-core-framework.md](02-core-framework.md)（★第 2 张）。

## 图 · 维护/修订版本流程

```mermaid
sequenceDiagram
    participant Dev as 开发者
    participant CLI as CLI (index.php)
    participant State as 状态层 (State.php)
    participant Core as 核心框架 (VersionManager)
    participant File as .version.json

    Dev->>CLI: php index.php bump "修订说明"
    CLI->>State: State::load()
    State->>File: file_get_contents() 读取持久化状态
    File-->>State: { version, log }
    State-->>CLI: VersionManager 实例

    CLI->>Core: $mgr->bump(reason)
    Core->>Core: patch<100 ? patch+1 : minor+1, patch=0
    Core-->>CLI: 变更记录 {from,to,at,reason}

    CLI->>State: State::save($mgr)
    State->>File: file_put_contents() 写回新版本与日志
    CLI-->>Dev: 打印 当前版本/修订计数/时间
```

**运维约定**

- 每次功能新增 / Bug 修复 / 文档维护，都执行一次 `php index.php bump "说明"`。
- 可使用 `php index.php history` 随时回看版本演进历史。
- 版本达到 `v.0.0.100` 后自动进位为 `v.0.1.0`，全程无需手工干预。