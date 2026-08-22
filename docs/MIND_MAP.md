# 思维构思导图

> 用「问题 / 根因 / 方案 / 落地 / 验证」的树状结构，还原本次模块化重构的思考过程。

## 一、业务愿景（Why）

```
M3U8 去广告系统 —— 让「任何链接」都能无广告、无非正片、秒开可播
│
├── 输入能力
│   ├── 官方平台：腾讯/爱奇艺/优酷/芒果TV/B站/搜狐/PPTV
│   ├── 直接 m3u8
│   └── 上游官解（第三方）
├── 核心承诺
│   ├── 资源站优先（资源站可播但有广告 → 去广告/去非正片）
│   ├── 永不卡死（全局超时预算）
│   └── 播放器不中断（等时长静音占位）
```

## 二、问题诊断（What's wrong）

```
解析不了 / 一直转圈
│
├── R1 官替 resolve 无全局超时 → 串行堆积网络请求（最严重）
│       先抓官页(30s×3) → 平台API → 最多40资源站逐站24s → 100s 无响应
├── R2 强依赖外部第三方 → 任一环抽风整链失败
│       虾米官解 114.134.184.91:9002 已加签名失效；jx.xmflv.cc 返回 HTML 播放器页
├── R3 三链路各写各的，无统一门面 → 难定位、重复逻辑多
└── R4 资源站可播但带广告/非正片 → 去广告分散不可控
```

## 三、方案设计（How）

```
总原则：增量、模块化、可验证、兼容旧链路
│
├── ① 全局总预算（治 R1）
│      parse/Timer.php —— 硬截止计时器，网络循环每迭代 ok()
│
├── ② 资源站优先 + 本地去广告（治 R4 & 用户最高优先级）
│      官方 → gz/OfficialReplaceManager::resolve()（预算内）→ m3u8
│             → parse/LocalM3u8Cleaner（关键词+时长 → 等时长静音占位）
│
├── ③ 统一门面 + 归一化输出（治 R3）
│       parse/ParserFacade::parse → parse/ParseResult::toArray()
│
└── ④ 架构分层（避免文件过大）
       入口层(HTTP) / 门面层(parse) / 引擎层(src·gz·xt) / 适配层(pt) / 数据层(db)
```

## 四、落地顺序（里程碑）

```
M1 快速止血 ──→ 全局预算 Timer + 旧 resolve 预算钩子 ✅
M2 门面骨架 ──→ parse/config + Timer + UrlClassifier + ParseResult + ParserFacade ✅
M3 去广告引擎 ──→ LocalM3u8Cleaner（可离线跑）✅
M4 官替接入 ──→ ResourceFirstResolver（预算内复用 resolve）✅
M5 验证 ──→ fixtures + cli_verify.php ✅（21/21）
M6 文档 ──→ ARCHITECTURE / MIND_MAP / MAINTENANCE / SECONDARY_DEV / CHANGELOG ✅
M7 接线 ──→ moxi/parse 链路接入 ParserFacade + 资源站优先（预算内）✅
M8 整体重构 ──→ 解析核心迁入 handlers/ + mx.php 瘦身为路由层 + UpdateManager 保护 ✅
```

> ✅ = 已完成

## 五、整体重构（v5.14.0）思考脉络

```
问题：mx.php 6116 行巨型文件 → 维护难、单文件过大影响解析速度效率
│
├── R5 单文件过大 → 解析核心与路由/管理混杂，改一处风险波及全站
├── R6 远程更新会覆盖本地重构成果 → 辛辛苦苦重构被 update 回退
├── R7 三链路（官解/官替/直链）各写各的 → 无统一入口，前端难切换
│
方案：
├── ① 瘦路由（治 R5）
│      mx.php 只留路由 + 管理类 action；解析类 action 全部迁出
├── ② handlers/ 模块（治 R5）
│      HandlerDispatcher(分发) → *Handler(action 处理) → helpers(工具)
│      新 action 只需在 MAP 注册 + 写一个 handler，不碰 mx.php
├── ③ 远程更新保护（治 R6）
│      UpdateManager 孤儿清理/覆盖复制跳过 handlers/ parse/ docs/ 及本地文档
└── ④ 统一链路（治 R7）
       parse/parse/info/jx/jx/info → ParseHandler → MoxiHandler / ParserFacade
       官方→资源站优先（预算内）→ 本地去广告；输出兼容旧字段，前端无感
```

## 六、验收标准

- 语法：全项目 PHP 文件 `php -l` 0 错误 ✅
- 离线：`php parse/tests/cli_verify.php` 21/21 通过 ✅
- 冒烟：`php -S 127.0.0.1:8899 router.php`，version/parse/moxi/mxjx/xiami_jx/skip 全部正确路由，moxi 24s 预算内返回不卡死 ✅
- 兼容：旧入口 + API 契约不回退；`handlers/` `parse/` `docs/` 可被远程更新保留 ✅