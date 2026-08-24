# 沫兮官替 · 核心框架图(第 2 张 · ★核心)

> 这是本项目**最核心**的一张图：完整展示**版本进位核心框架**的状态机与算法思路。

## 版本规则

> 初始版本 `v.0.0.1`；patch 每 `bump()` 一次 +1；当 patch 累计到 **100** 时进一位 minor
> 并归零：`v.0.0.100 → v.0.1.0`；之后 patch 从 `1` 重新递增，依次类推。
> 每个 minor 周期含 101 个版本（过渡位 `.0` + `.1..100`）。

## 图二 · 版本进位核心框架(状态机)

```mermaid
stateDiagram-v2
    [*] --> P1: 初始化 v.0.0.1 (minor=0)
    P1: patch = 1..100<br>(minor=0)

    P1 --> P1: bump() 且 patch < 100<br>patch + 1

    P1 --> M10: patch == 100<br>进一位 minor+1, patch=0
    M10: v.0.1.0 (过渡位)<br>minor=1, patch=0

    M10 --> P2: bump()<br>patch = 1

    P2: patch = 1..100<br>(minor=1)

    P2 --> P2: bump() 且 patch < 100<br>patch + 1

    P2 --> M20: patch == 100<br>minor+1, patch=0
    M20: v.0.2.0 (过渡位)

    P1 --> [*]
    M10 --> [*]
    P2 --> [*]
    M20 --> [*]
```

## 图 · 核心算法(bump()) 伪代码

```mermaid
flowchart TD
    A["bump(reason)"] --> B{"patch == 100 ?"}
    B -- 否 --> C["patch += 1"]
    B -- 是 --> D["minor += 1 ; patch = 0"]
    C --> E["生成变更记录<br>(from, to, at, reason)"]
    D --> E
    E --> F["写入 $this->log"]
    F --> G["返回变更记录"]
```

**核心思路**

1. 用三元组 `[major, minor, patch]` 表示版本，输出格式 `v.{major}.{minor}.{patch}`。
2. `bump()` 只做一件事：patch 未满则 +1，满 100 则 minor 进位并归零。
3. 每次修订都会写入带时间戳的变更日志，便于追溯"何时改了什么版本"。
4. 由修订计数 `revision = minor × (STEP+1) + patch` 可反推版本，保证计数与版本一一对应。