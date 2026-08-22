# M3U8 去广告系统 —— 解析故障诊断与模块化重构方案

> 版本：方案稿 v1.0 ｜ 适用：main v5.13.8 ｜ 目标：打通「资源站优先 → 本地去广告/去非正片」主链路，杜绝解析卡死，模块化可验证。

---

## 1. 现状问题（用户实际报障）

用户通过 `http://114.134.184.91:9002/mx.php?action=moxi&url=...` 调用 moxi 接口，对以下输入均出现「解析不了 / 一直转圈」：

- 官方平台：腾讯视频 / 爱奇艺 / 优酷 / 芒果TV / 哔哩哔哩 / 搜狐视频 / PP视频
- 直接 M3U8 链接
- URL 类型三链路：「M3U8 去广告 / 官方替换 / 上游官解」

### 1.1 链路结构

`moxi`（[mx.php#L4443-L4548](file:///workspace/mx.php#L4443-L4548)）把输入**二分**：

- 官方平台域名 → `OfficialReplaceManager::resolve($url)`（官替＝资源站搜索匹配）
- 其它 → 本地 `mxjx`（本地去广告）

### 1.2 根因（实测证据）

| # | 根因 | 证据 | 影响 |
|---|------|------|------|
| R1 | **官替 `resolve()` 无全局总超时，串行堆积网络请求** | [OfficialReplaceManager.php#L142](file:///workspace/gz/OfficialReplaceManager.php#L142)：先抓官方页（[httpGet 30s×3重试#L3613](file:///workspace/gz/OfficialReplaceManager.php#L3613)）→ 平台 API → 最多 40 个资源站逐站搜索（[每站20s#L3056](file:///workspace/gz/OfficialReplaceManager.php#L3056)）。实测对优酷链接 **100s 未返回直接挂起** | 前端超时 → 表现「解析不了」，对每个平台通用 |
| R2 | **强依赖外部第三方，任一环抽风整链失败** | 官解走虾米 `jx.xmflv.cc`（HTML 播放器页，APK 不可直播）；老 `114.134.184.91:9002` 已签名失效；资源站 maccms 接口常下架 | 稳定性差，链路脆弱 |
| R3 | 三链路共享同一批外部依赖，无独立兜底与并行 | 官解/官替/直链各写各的，未统一门面 | 无法快速定位、难以维护、重复逻辑多 |
| R4 | 资源站**本可播放但带广告/非正片**，去广告清理分散且不可控 | 用户明确反馈「资源站可以播放，但有广告，需要去广告或非视频正片内容」 | 观感差、进度条跳动 |

### 1.3 关键澄清（用户确认）

> **优先级最高**：以资源站资源为主。资源站返回的 m3u8 可播放，但含广告/非正片 → 需要本地去广告 + 非正片占位。同时要求本地可运行验证、兼容远程更新。

---

## 2. 设计目标与约束

| 目标 | 说明 |
|------|------|
| G1 资源站优先 | 官方链接 → 资源站搜索匹配 → 取 m3u8 → **本地去广告/去非正片占位** |
| G2 永不卡死 | 全链路硬性总超时预算，超限立即返回明确失败 + step_trace |
| G3 模块化 | 新增 `parse/` 门面层：路由 + 预算 + 输出归一化；引擎复用现有成熟件 |
| G4 兼容 | 旧链路不动、并行共存；新模块通过新 action 暴露，远程更新不回退 |
| G5 可验证 | 离线确定性验证本地去广告；在线部分提供 CLI 开关 |

---

## 3. 目标架构

```
用户 / 播放器
   │  官方URL or M3U8
   ▼
parse/ParserFacade::parse(url, cfg)        ← 统一门面（新）
   │  ① URL 类型判定（m3u8 / 官方 / 上游官解）
   │  ② 开启全局预算 Timer
   ├── dispatch: m3u8 ──────────────► parse/LocalM3u8Cleaner   （本地去广告/占位）
   ├── dispatch: 官方URL ───────────► parse/ResourceFirstResolver
   │        └ reuses: OfficialReplaceManager::resolve(带预算)   （资源站匹配）
   │                 └ 得 m3u8_url ──► LocalM3u8Cleaner（去广告）
   └── dispatch: 上游官解 ──────────► 保留旧 xt 链路（可选 fallback）
   ▼
   归一化结果 {code,url,official_url,replace_url,title,episode,step_trace,elapsed}
```

新增文件（互不依赖、可独立运行、不影响旧代码）：
- `parse/ParserFacade.php`  入口门面：路由 + 总预算 + 输出归一化
- `parse/Timer.php`         全局预算计时器（hwdeadline）
- `parse/LocalM3u8Cleaner.php`  本地去广告/去非正片（复用 M3U8Parser + EnhancedAdRuleEngine + Md5AdPlaceholderEngine）
- `parse/config.php`        集中超时/开关/平台清单

最小侵入旧代码：仅给 `OfficialReplaceManager::resolve()` / `httpGet()` / `searchInSites()` 插入**预算检查**（数行），保证「资源站优先」复用路径不卡死；不改其外部契约。

---

## 4. 关键技术点

### 4.1 全局总预算（治 R1/G2）
统一用 `Timer`（读自 `parse/config.php` 的 `global_budget=25s`）。在任意网络循环的每次迭代 `if (!$timer->ok()) return 超时结果`。所有第三方请求都别再各自 30s×3 无限叠加，超预算就「快速失败 + 明确 message」。

### 4.2 资源站优先（治 R4/G1）
复用 `OfficialReplaceManager::resolve()` 即为资源站优先通道：识别平台→提标题→`searchInSites`→`findEpisodeUrl`。拿到 `m3u8_url` 后交给 `LocalM3u8Cleaner`：
- `M3U8Parser::parse` 解析
- `EnhancedAdRuleEngine` 规则去广告，`Md5AdPlaceholderEngine` 对广告段做**等时长黑屏静音占位**（不删段 → 进度条/解码器不中断）

### 4.3 输出归一化（治 R3）
所有通道返回统一结构，前端只看 `code/url/…`，不再理会内部差异。

### 4.4 多通道并发（可选增强）
官方标准做法：官方通道与资源站通道 `curl_multi`/并行，谁快成功用谁；但本轮 G1 已按「资源站优先」定序，并发作为里程碑二。

---

## 5. 里程碑

| 里程碑 | 内容 | 交付物 |
|--------|------|--------|
| M0 ✅ | 本文档定稿 | REFACTOR_PLAN.md |
| M1 | 快速止血：全局预算 + 旧 resolve 预算钩子 | parse/Timer + 旧文件数行改动 |
| M2 | 模块化门面 + 本地去广告引擎 | parse/ParserFacade + LocalM3u8Cleaner |
| M3 | 资源站优先接入 | parse/ResourceFirstResolver（复用 resolve） |
| M4 | 可运行验证 | 假 m3u8 样本 + CLI 测试脚本跑通 |
| M5 | 文档同步 | README/CHANGELOG 记录 |

> 里程碑每次落地都保持 `php -l` 0 错误；旧链路与 API 契约不回退。

---

## 6. 验证方法

- **离线（确定性）**：`parse/tests/fixtures/ad.m3u8`（含 CUE-OUT/短时长/广告关键词段），跑 `LocalM3u8Cleaner`，断言广告段被替换为占位地址、段数不变、EXTINF 时长不变。
- **在线（受网络影响）**：CLI `parse/tests/cli_verify.php url=官方链接`，经沙箱代理验证 resolve 不卡死、有预算保护。
- **语法**：`php -l` 全部新增文件 0 错误。

---

## 7. 远程更新兼容说明

新模块全部为**新增文件**（`parse/*`），远程 `update.php` 只覆盖仓库既有文件清单；建议把旧 `mx.php` 等的改动尽量收敛在「预算钩子」等最小面，并在此文档与 CHANGELOG 中留痕，便于升级时合并。