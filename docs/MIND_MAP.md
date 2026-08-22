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
M1 快速止血 ──→ 全局预算 Timer + 旧 resolve 预算钩子
M2 门面骨架 ──→ parse/config + Timer + UrlClassifier + ParseResult + ParserFacade ✅
M3 去广告引擎 ──→ LocalM3u8Cleaner（可离线跑）✅
M4 官替接入 ──→ ResourceFirstResolver（预算内复用 resolve）
M5 验证 ──→ fixtures + cli_verify.php ✅（21/21）
M6 文档 ──→ ARCHITECTURE / MIND_MAP / MAINTENANCE / SECONDARY_DEV / CHANGELOG（本轮）
M7 接线 ──→ 在 mx.php moxi 接入 ParserFacade（后续）
```

> ✅ = 本轮已完成

## 五、验收标准

- 语法：所有新增文件 `php -l` 0 错误 ✅
- 离线：`php parse/tests/cli_verify.php` 21/21 通过 ✅
- 兼容：旧入口 + API 契约不回退；`parse/` 可被远程更新保留