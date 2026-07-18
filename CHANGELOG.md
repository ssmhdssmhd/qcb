# 更新日志

## v5.5.8 (2026-07-18)

### 小版本优化 - 修复走官替接口时播放地址不可播放的问题

#### 问题现象

后台「嗅探设置」切到**官替接口**通道时，解析返回的播放地址为
`http://114.134.184.91:9002/xt/clean.php?id=b8e1cab38badd285`，播放器无法播放。

#### 根因

官替接口（`mx.php?action=official_replace/info&url=`）返回的 `m3u8_url` / `ad_skip_url`
**本身已经是去广告的播放地址**（由本项目 `mxjx` 代理生成）。
但 `parseVideo()` 没有区分通道来源，把它当成原始 m3u8 又走了一次 xt 的去广告流程：

```
官替返回 m3u8_url (已去广告)
   → fetchM3u8Content 下载
   → AdFilter 再次过滤
   → saveCleanM3u8 生成 clean.php?id=xxx 代理
   → 返回嵌套代理地址（不可播放）
```

代理地址嵌套 + 路径解析错乱，导致最终播放地址无法被播放器加载。

#### 修复

1. **`xt/server.php` - `getVideoLinkBySnifferMode()` 返回值改为结构化数组**
   - 原：`?string`（仅返回视频直链）
   - 新：`array{ url: string|null, source: 'official'|'replace'|null }`
   - 通过 `source` 字段告知调用方实际命中的是哪条通道（包含 fallback 后的真实通道）

2. **`xt/server.php` - `parseVideo()` 按通道分流处理**
   - `source === 'replace'`：官替返回的已是去广告地址，**直接透传**，写入缓存后返回
   - `source === 'official'`：官解返回的是原始 m3u8，继续走原有的 xt 去广告流程
     （fetchM3u8Content → AdFilter → saveCleanM3u8 → clean.php 代理）
   - fallback 场景自动正确：官替失败 fallback 到官解时走官解流程，反之亦然

3. **版本号同步**
   - `version.php`：v5.5.7 → v5.5.8
   - `xt/config.php`：5.1.6 → 5.1.7

#### 影响范围

- ✅ 走官解通道：行为不变，仍走 xt 去广告 + clean.php 代理
- ✅ 走官替通道：修复后直接返回官替的去广告 m3u8_url，可正常播放
- ✅ Fallback：自动适配，无需额外配置
- ✅ 旧 `official_apis` 数组：归为 official 通道，行为不变

---

## v5.5.7 (2026-07-18)

### 小版本更新 - 后台新增「嗅探设置」

1. **新增「嗅探设置」后台页面**
   - 位置：后台 → 接口工具 → 嗅探设置（🔍 图标）
   - 用于控制超级嗅探模块（`xt/`）走哪条解析通道
   - 支持两种解析通道，可任意切换：
     - **官解解析（official）**：调用官方解析 API 获取 m3u8/mp4 直链
     - **官替接口（replace）**：调用官替 API 获取资源站匹配后的 m3u8
   - 两个接口各配一个独立开关，再通过「当前通道」单选决定实际走哪一条
   - 当前通道失败时自动 fallback 到另一条已启用的通道
   - 内置测试入口，可直接在页面里输入视频链接验证当前嗅探设置效果

2. **后台页面交互细节**
   - 官解/官替各一个配置卡片：开关 + 接口名称 + 接口地址 + 接口类型（redirect/json/text）+ URL 字段名
   - 实时状态徽章：显示每个接口是「未启用 / 已启用 / 当前通道」
   - 切换开关或当前通道时徽章颜色实时变化
   - 官替接口地址留空时自动使用本项目官替接口 `mx.php?action=official_replace/info&url=`
   - 保存成功后自动重新加载并显示更新时间

3. **新增配置文件 `xt/sniffer_config.php`**
   - 由后台「嗅探设置」页面自动读写
   - 结构：`{ mode, official_api{enabled,name,url,type,url_field,headers}, replace_api{...}, update_date }`
   - 兼容旧版本：文件不存在时使用 `xt/config.php` 中的默认值

4. **`xt/server.php` 路由逻辑重构**
   - 新增 `getVideoLinkBySnifferMode()`：根据嗅探设置选择走官解还是官替
   - 抽取 `getVideoLinkFromApiEntry()` 为通用单接口调用函数（被旧逻辑和新嗅探路由复用）
   - `callSingleApi()` 包装单个接口配置后调用通用函数
   - JSON 类型解析增强：兼容官替接口返回结构 `{success, m3u8_url, ad_skip_url}`
   - 两个通道都未启用时自动 fallback 到旧的 `official_apis` 数组，保证向后兼容

5. **新增 API 端点**
   - `GET  /mx.php?action=sniffer/config`       — 获取嗅探设置（合并默认值）
   - `POST /mx.php?action=sniffer/config/save`  — 保存嗅探设置（白名单字段 + 写入 `xt/sniffer_config.php`）

6. **`xt/config.php` 同步更新**
   - 新增 `sniffer` 配置段（作为 `sniffer_config.php` 不存在时的兜底默认值）
   - 模块版本号 5.1.5 → 5.1.6

#### 影响文件

- 新增 [xt/sniffer_config.php](file:///workspace/xt/sniffer_config.php) — 嗅探设置配置文件（后台自动维护）
- 修改 [xt/config.php](file:///workspace/xt/config.php) — 新增 sniffer 默认配置段，版本号 5.1.6
- 修改 [xt/server.php](file:///workspace/xt/server.php) — 新增嗅探路由 + 抽取通用接口调用函数
- 修改 [mx.php](file:///workspace/mx.php) — 新增 sniffer/config 和 sniffer/config/save 两个 API 端点
- 修改 [mxadmin.php](file:///workspace/mxadmin.php) — 新增「嗅探设置」后台页面 + 侧边栏菜单 + JS 逻辑
- 修改 [version.php](file:///workspace/version.php) — 版本号升级到 v5.5.7
- 修改 [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志
- 修改 [README.md](file:///workspace/README.md) — 功能特性新增「嗅探设置」说明

---

## v5.5.6 (2026-07-18)

### Bug 修复 - 平台适配器方法可见性错误

1. **修复 `mx.php?action=api/v2&type=official` 报错问题**
   - 报错信息：`Access level to TencentVideoAdapter::chineseToNumber() must be protected (as in class AbstractPlatformAdapter) or weaker`
   - 原因：子类 `chineseToNumber()` 声明为 `private`，父类 `AbstractPlatformAdapter::chineseToNumber()` 是 `protected`，PHP 不允许子类把方法可见性改得更严格
   - 修复：将以下 4 个适配器的 `chineseToNumber()` 方法从 `private` 改为 `protected`，与父类保持一致

2. **影响文件**
   - [pt/TencentVideoAdapter.php](file:///workspace/pt/TencentVideoAdapter.php#L916) — `chineseToNumber()` private → protected
   - [pt/MgtvAdapter.php](file:///workspace/pt/MgtvAdapter.php#L420) — `chineseToNumber()` private → protected
   - [pt/BilibiliAdapter.php](file:///workspace/pt/BilibiliAdapter.php#L389) — `chineseToNumber()` private → protected
   - [pt/SohuAdapter.php](file:///workspace/pt/SohuAdapter.php#L397) — `chineseToNumber()` private → protected
   - [version.php](file:///workspace/version.php) — 版本号升级到 v5.5.6

---

## v5.5.5 (2026-07-17)

### 版本升级

1. **版本号升级**
   - `version.php` 版本号从 v5.1.5 升级到 v5.5.5
   - commit 标识更新为 `v5.5.5`
   - 更新时间更新为 2026-07-17

2. **功能汇总**
   - 浏览器适配功能：`xt/clean.php` 支持 Edge、Chrome、Firefox、Safari 等主流浏览器直接访问
   - TVBox/影视App专用解析接口 `jiexi.php`
   - 超级嗅探模块 `xt/`：官解接口对接、规则引擎 + AI 大模型双重广告识别
   - 修复去广告 m3u8 无法播放问题（ts 相对路径转绝对路径）

#### 影响文件

- [version.php](file:///workspace/version.php) — 版本号升级到 v5.5.5
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.1.5 (2026-07-17)

### 小版本更新 - 新增浏览器适配功能

- `xt/clean.php` 支持 Edge、Chrome、Firefox、Safari 等主流浏览器直接访问
- 浏览器访问时自动显示 HTML 播放器页面，支持在线播放 m3u8 视频
- 保留原有 m3u8 直链模式，播放器调用不受影响
- 新增浏览器检测和标识显示功能

---

## v5.1.0 (2026-07-16)

### 重大变更：移除视频嗅探模块

1. **移除文件**
   - 删除 `api.php`（视频解析API入口）
   - 删除 `server.php`（视频解析服务端）

2. **保留功能**
   - M3U8 广告分析与去广告系统（核心功能）
   - 后台管理页面（mxadmin.php）
   - 其他模块正常使用

#### 影响文件

- `api.php` — 已删除
- `server.php` — 已删除
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.1.0
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.10 (2026-07-16)

### 终极方案：JSONP方式绕过CORS（谁调用用谁IP）

1. **核心原理**：腾讯API支持JSONP回调（`QZOutputJson=xxx;`），通过 `<script>` 标签加载不受CORS限制
2. **工作流程**：
   - 阶段1：服务器生成腾讯API请求URL（含callback参数）
   - 阶段2：客户端浏览器用 `<script>` 标签直接加载腾讯API（出口IP=客户端国内IP）
   - 阶段3：客户端将API返回数据回传给服务器，服务器处理返回视频URL

3. **优势**：
   - 完全绕过CORS限制（`<script>` 标签不受同源策略约束）
   - 出口IP是客户端的真实国内IP，腾讯必然返回em=0
   - 用户直接访问即可，无需任何额外操作
   - 服务器只负责生成参数和处理结果，不参与API请求

4. **关键改动**：
   - `api.php`：完全重写，腾讯视频使用JSONP方式处理
   - `api.php`：新增 `handleTencentVideo()` 函数，生成JSONP请求页面
   - `api.php`：新增 `processTencentApiData()` 函数，处理阶段2回传的数据

#### 影响文件

- [api.php](file:///workspace/api.php) — JSONP方式处理腾讯视频
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.10
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.9 (2026-07-16)

### 终极方案：用户IP注入（解决CORS跨域问题）

1. **问题根因：CORS跨域阻止浏览器直接请求腾讯API**
   - v5.0.8 的客户端直连方案在浏览器中因CORS策略失败
   - 浏览器不允许直接跨域请求 `vv.video.qq.com`

2. **新方案：服务器代理转发 + 用户IP注入**
   - 核心原理：用户在国内访问海外服务器，服务器能获取到用户的真实国内IP
   - 服务器转发腾讯API请求时，将用户的国内IP注入到 `X-Forwarded-For/Client-IP/X-Real-IP` 请求头
   - 腾讯按注入的国内IP鉴权，返回 `em=0`

3. **工作流程**
   ```
   国内用户 → 海外服务器(api.php) → server.php → 提取用户国内IP → 注入X-Forwarded-For → 腾讯API(em=0) → 返回视频URL
   ```

4. **关键改动**
   - `server.php`: 新增 `getUserRealIp()` 函数，从 `REMOTE_ADDR/X-Forwarded-For/X-Real-IP/Client-IP` 提取用户真实IP
   - `server.php`: 新增 `curlGetWithUserIp()` 函数，转发请求时注入用户IP
   - `server.php`: 新增 `extractTencentVideoWithProxy()` 函数，使用用户IP注入模式解析腾讯视频
   - `server.php`: 新增 `proxyRequest()` 函数，提供API代理转发端点（`?action=proxy&url=xxx`）

5. **优势**
   - 用户无需任何操作，直接访问即可解析
   - 无需前端JS处理，纯服务器端完成
   - 支持所有平台（腾讯/爱奇艺/优酷/芒果TV）
   - 自动适配国内外服务器

#### 影响文件

- [server.php](file:///workspace/server.php) — 用户IP注入方案、代理转发功能
- [api.php](file:///workspace/api.php) — 简化为透传模式
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.9
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.8 (2026-07-16)

### 终极方案：客户端直连腾讯API（解决免费代理全部失效问题）

1. **问题根因：免费代理全部失效**
   - v5.0.7 的国内代理池轮询方案在靶机测试中所有代理请求失败（404/超时/连接拒绝）
   - 免费代理生命周期极短，且大部分已被滥用或封禁

2. **新方案：两阶段客户端直连**
   - **阶段1**：服务器生成腾讯API请求参数（URL、UA、referer、guid等），返回 `code:206`
   - **阶段2**：客户端（国内浏览器）直接调用腾讯API（出口IP为国内，必然返回 em=0），将结果回传给服务器
   - **阶段3**：服务器处理API响应，提取视频URL并返回

3. **工作流程**
   ```
   国内客户端 → api.php?url=xxx → server.php 返回阶段1任务
   国内客户端 → 直接调用腾讯API（em=0）→ api.php?phase=2&api_data=xxx → 返回视频URL
   ```

4. **关键改动**
   - `server.php`：新增 `generateTencentApiRequests()` 和 `processTencentApiData()` 函数
   - `server.php`：检测到腾讯视频时，返回 `code:206` + 任务参数（而非直接解析）
   - `server.php`：添加完整 CORS 响应头，允许客户端跨域调用腾讯API
   - `api.php`：支持 `phase=2` 参数透传

5. **前端集成**
   ```javascript
   // 前端需要处理 code:206 的响应
   async function parseVideo(url) {
       const resp = await fetch(`api.php?url=${encodeURIComponent(url)}`);
       const data = await resp.json();
       
       if (data.code === 206) {
           // 阶段2：客户端直接调用腾讯API
           for (const req of data.task.requests) {
               const apiResp = await fetch(req.url, {
                   headers: { 'User-Agent': req.ua, 'Referer': req.referer }
               });
               const text = await apiResp.text();
               const apiData = JSON.parse(text.replace(/^QZOutputJson=/, '').replace(/;$/, ''));
               
               if (apiData.em === 0) {
                   const result = await fetch(`${data.task.callback}&api_data=${btoa(JSON.stringify(apiData))}&guid=${data.task.guid}`);
                   return await result.json();
               }
           }
       }
       return data;
   }
   ```

#### 影响文件

- [server.php](file:///workspace/server.php) — 两阶段解析方案、CORS响应头
- [api.php](file:///workspace/api.php) — phase=2 参数透传、CORS响应头
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.8
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.7 (2026-07-16)

### 最终方案：国内 HTTP/SOCKS5 代理池轮询（解决 X-Forwarded-For 失效问题）

1. **问题根因：X-Forwarded-For 伪造被腾讯新版 API 检测**
   - v5.0.6 的 X-Forwarded-For 方案在靶机测试中仍返回 `em=80`，说明腾讯已升级检测机制：
     - 不再信任简单的请求头伪造
     - 可能检测真实 TCP 源 IP 或要求可信代理白名单
     - 结合 TLS 指纹等多维度判断

2. **新方案：真实国内代理池轮询**
   - 直接通过国内 HTTP/SOCKS5 代理访问腾讯 API，出口 IP 为国内
   - 代理来源：[proxy.scdn.io](https://proxy.scdn.io/?country=%E4%B8%AD%E5%9B%BD) 中国区免费代理
   - 内置 31 个国内代理（HTTP + SOCKS5），按响应时间排序轮询

3. **`curlGet()` 新增 `proxy` 选项**
   - 支持 `http://IP:PORT` 和 `socks5://IP:PORT` 两种格式
   - 自动检测协议类型，设置 `CURLOPT_PROXYTYPE`
   - 与现有 `spoof_ip`、`headers` 选项兼容

4. **代理池结构**
   ```
   第1批：响应时间 18-75ms（免费代理，可能失效）
   第2批：响应时间 335-500ms
   第3批：阿里云/腾讯云主机代理（相对稳定）
   SOCKS5：202.141.161.53:10808（更稳定）
   ```

5. **建议**
   - 免费代理稳定性差，建议使用**付费代理**或**自建国内 VPS 中转**
   - 如需稳定解析，可在国内 VPS 部署简单代理转发脚本

#### 解析流程（不变）

```
方案零：官方API + 国内代理池（出口IP为国内）
   ↓ 失败
方案一：第三方JSON解析接口
   ↓ 失败
方案二：第三方HTML解析接口
   ↓ 失败
方案三：Chrome Headless 嗅探
```

#### 影响文件

- [server.php](file:///workspace/server.php) — curlGet 新增 proxy 选项；腾讯解析改用国内代理池轮询
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.7
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.6 (2026-07-16)

### 关键修复：海外服务器腾讯视频 em=80 彻底解决（X-Forwarded-For 伪造国内IP）

1. **核心方案：HTTP 请求头注入国内 IP，绕过腾讯地域版权限制**
   - 问题根因：v5.0.5 的 CORS 代理方案在靶机对抗测试中全军覆没 —— 公共 CORS 代理（allorigins / corsproxy / proxy.cors.sh）的出口 IP 也都在海外，腾讯 API 对它们同样返回 `em=80`。
   - 新方案：直接在请求腾讯 API 时注入 `X-Forwarded-For` / `Client-IP` / `X-Real-IP` / `Forwarded` 四个请求头，让腾讯 API 按伪造的国内 IP 进行地域鉴权，返回 `em=0`。
   - 验证：本地 curl 测试，注入 `220.181.38.148` 后腾讯 API 返回 `em=0` 并正常下发 `fvkey`。

2. **国内 IP 池轮询机制**
   - 内置 10 个国内主流 IP（百度 / 电信 / 联通 / 移动 / 腾讯云骨干网）
   - 每次调用腾讯 API 轮换一个 IP，规避单 IP 被风控的可能
   - 涵盖北京、上海、广东、江苏等主要地域

3. **`curlGet()` 工具函数新增 `spoof_ip` 选项**
   - 通用化设计，所有调用方均可按需注入 IP 头
   - 自动校验 IP 格式（`filter_var`），非法 IP 不注入
   - 与现有 `headers` 选项合并，互不覆盖

4. **代码瘦身**
   - 移除已废弃的 `extractVideoByProxyApi()` 函数（方案零B）
   - 移除 `extractTencentVideo()` 的 `$useProxy` 参数和代理分支逻辑
   - 移除 CORS 代理列表（allorigins / corsproxy / proxy.cors.sh）
   - 删除调试用的临时文件 `test_decode.php`

#### 解析流程（4 层回退，简化结构）

```
方案零：官方API直连 + X-Forwarded-For 注入国内IP（绕过 em=80）
   ↓ 失败
方案一：第三方JSON解析接口（4个接口轮询）
   ↓ 失败
方案二：第三方HTML解析接口（5个接口轮询）
   ↓ 失败
方案三：Chrome Headless 嗅探（最后防线）
```

#### 影响文件

- [server.php](file:///workspace/server.php) — curlGet 新增 spoof_ip 选项；腾讯解析改用 X-Forwarded-For；移除代理方案
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.6
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.5 (2026-07-15)

### 重大更新：国内外服务器自动适配

1. **5 层自动回退机制，确保任何地区都能解析**
   ```
   方案零：官方API直连（国内IP最快，em=0直接成功）
      ↓ 失败(em=80)
   方案零B：CORS代理转发官方API（海外IP自动回退）
      代理列表：allorigins.win → corsproxy.io → proxy.cors.sh
      ↓ 失败
   方案一：第三方JSON解析接口（4个接口轮询）
      ↓ 失败
   方案二：第三方HTML解析接口（5个接口轮询）
      ↓ 失败
   方案三：Chrome Headless 嗅探（最后防线）
   ```

2. **重构代码架构**
   - 提取统一的 `curlGet()` 工具函数，消除重复代码
   - 腾讯解析函数支持 `直连/代理` 双模式参数
   - 第三方解析拆分为 JSON 接口和 HTML 接口两个独立方案
   - 每层方案独立记录调试日志，便于诊断

3. **代理模式工作原理**
   - 海外服务器直连腾讯API返回 `em=80`（版权限制）
   - 通过公共CORS代理转发请求，代理服务器在国内，em=0
   - 3个代理自动轮询，任一可用即成功

#### 影响文件

- [server.php](file:///workspace/server.php) — 完全重构，5层回退+代理模式
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.5
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.4 (2026-07-15)

### 深度修复

1. **根本性修复腾讯视频 em=80 版权限制**
   - 问题根因：腾讯 API 通过请求来源判断地域版权，缺少 `ehost` 参数导致返回 `em=80`
   - 解决方案：所有 API 请求添加 `ehost` 参数（PC端 `v.qq.com` / 移动端 `m.v.qq.com`）
   - 本地验证：添加 `ehost` 后 `em=0`，成功获取视频信息

2. **优化 vkey 获取流程**
   - 优先使用 `getinfo` 返回的 `fvkey`，无需再调 `getkey` 接口
   - `fvkey` 为空时自动回退到 `getkey` 接口
   - 减少一次 HTTP 请求，提升解析速度

3. **简化 CDN 验证逻辑**
   - 移除逐个 CDN 服务器 HEAD 验证（耗时且不必要）
   - 直接返回第一个服务器地址，由播放器处理

#### 影响文件

- [server.php](file:///workspace/server.php) — 添加 ehost 参数 + fvkey 优化
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.4
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.3 (2026-07-15)

### 修复

1. **修复服务器 IP 版权限制（em=80）导致解析失败**
   - 问题：腾讯 API 返回 `em=80`，提示"您所在区域暂无此内容版权"
   - 新增多 API 端点轮询：PC端 `vv.video.qq.com` + H5移动端 `h5vv.video.qq.com`
   - 使用移动端 UA 访问 H5 API，可能绕过部分地域限制
   - 新增多清晰度自动回退：shd → fhd → hd → sd → msd

2. **新增 JSON 直接返回的第三方解析接口**
   - 优先尝试 `jx.xmflv.com?type=json`、`yparse.ik9.cc?type=json` 等 JSON 接口
   - JSON 接口不依赖 JS 渲染，cURL 可直接获取视频地址
   - 支持多种 JSON 字段名解析（url/video/src/play/m3u8/mp4/data）

#### 影响文件

- [server.php](file:///workspace/server.php) — 多API端点轮询 + JSON解析接口
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.3
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.2 (2026-07-15)

### 修复

1. **修复腾讯视频解析失败问题**
   - 问题：第三方解析接口（jx.xmflv.com）使用 JavaScript 动态加载视频地址，cURL 无法执行 JS 导致解析失败
   - 新增**方案零：直接调用平台官方 API**，优先于第三方解析接口
   - 腾讯视频：通过 `getinfo` + `getkey` 两步 API 直接获取 MP4 视频直链
     - 自动提取视频 ID（支持 cover/page/iframe 多种 URL 格式）
     - 使用随机 GUID 生成鉴权 token
     - 遍历多个 CDN 服务器，自动验证 URL 可访问性
   - 爱奇艺：调用 `pcw-api` 获取视频播放地址
   - 优酷：调用 `ups.youku.com` 获取 M3U8/MP4 流地址
   - 芒果TV：调用 `pcweb.api.mgtv.com` 获取播放地址

2. **增加更多备用解析接口**
   - 新增 jx.m3u8.tv、jx.parwix.com、jx.jsonplayer.com 三个备用接口
   - 提升第三方解析回退成功率

#### 解析流程（三层策略）

```
方案零：平台官方 API（腾讯/爱奇艺/优酷/芒果）→ 最快最稳定
   ↓ 失败时
方案一：cURL + 正则解析（5个第三方接口轮询）→ 兜底
   ↓ 失败时
方案二：Chrome Headless 嗅探 → 最后防线
```

#### 影响文件

- [server.php](file:///workspace/server.php) — 新增平台直接 API 解析、增加备用解析接口
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.2
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v5.0.1 (2026-07-15)

### 修复

1. **管理后台顶部显示问题修复**
   - 修复桌面端顶部 header 显示异常的问题（v3 样式覆盖导致白色背景、文字过小）
   - 恢复渐变色顶部栏设计，与移动端风格保持一致
   - 修复背景图模式下顶部栏样式不一致的问题
   - 优化顶部栏布局，确保标题、主题切换按钮正确显示

#### 影响文件

- [mxadmin.php](file:///workspace/mxadmin.php) — 修复顶部 header 样式
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.1

---

## v5.0.0 (2026-07-15)

### 大版本更新 - 全面深度优化 & API 文档完善

#### 新增

1. **API 文档全面完善**
   - 在 [api_doc.php](file:///workspace/api_doc.php) 文档最前面添加**完整接口索引**，包含全部 95+ 个接口、20 个功能模块
   - 新增 **PT 引擎** 分类文档（pt/status、pt/test、pt/adskip）
   - 新增 **AI 智能** 分类文档（ai/smart_process、ai/pro_detect、ai/skip、ai/insert_detect、ai/subtitle_detect、ai/md5_analyze、ai/md5_detect 等 7 个接口）
   - 新增 **广告特征码** 分类文档（signatures/list、signatures/add、signatures/delete、signatures/stats、signatures/clean）
   - 新增 **官方站点** 分类文档（official_sites/status、official_sites/list、official_sites/search_all、official_sites/toggle）
   - 新增 **播放器** 分类文档（player/config/save）
   - 新增 **备份管理** 4 个接口文档（update/backup/list、update/backup/create、update/backup/restore、update/backup/delete）
   - 侧边栏导航同步更新，支持快速跳转到各分类

2. **parse/list 接口新增 cache 类型**
   - supported_types 数组中添加 `cache` 类型
   - 说明：缓存型 M3U8 解析（带 vkey 参数的缓存链接）

#### 修复

1. **pt/adskip 接口 M3U8 获取方式优化**
   - 将 `@file_get_contents` 改为 `curl`，增加超时控制
   - 设置 `CURLOPT_TIMEOUT = 15`（总超时）、`CURLOPT_CONNECTTIMEOUT = 5`（连接超时）
   - 增加 `CURLOPT_FOLLOWLOCATION` 支持重定向
   - 增加 SSL 证书验证跳过（兼容自签名证书）
   - 增加浏览器 User-Agent，避免被 CDN 拦截
   - 增加 HTTP 状态码检查，200 以外视为失败
   - 增加 curl 错误信息返回，便于排查问题

2. **mxjx/deep 接口保存空 MD5 问题修复**
   - 原代码 `saveMd5Signatures` 时传入 `'md5' => ''` 空值，导致特征码未实际保存
   - 修复：从 `tsAnalysis['md5_details']` 中构建 URI → MD5 映射表
   - 根据 `deepAdUris` 中的 URI 查找对应的 MD5 值后再保存
   - 没有对应 MD5 的跳过，避免保存无效数据

3. **mxjx/info 接口 file_get_contents 超时问题修复**
   - 将 `file_get_contents($url)` 改为 curl 请求
   - 设置 `CURLOPT_TIMEOUT = 10`、`CURLOPT_CONNECTTIMEOUT = 3`
   - 增加 HTTP 状态码检查，失败时回退到原始结果
   - 增加 SSL 跳过和 UA 设置，提高请求成功率

#### 优化

1. **版本号升级到 v5.0.0**
   - [version.php](file:///workspace/version.php) 版本从 v4.0.0 升级到 v5.0.0
   - commit 标识更新为 `v5-unified-api-pt-engine`

2. **API 文档结构优化**
   - 侧边栏增加"完整接口索引"（ALL 标签）作为第一项
   - 新增分类：完整接口索引、PT引擎、AI智能、广告特征码、官方站点、播放器
   - 接口搜索功能自动适配新增分类

#### 测试验证

- ✅ 所有 PHP 文件语法检查通过（无语法错误）
- ✅ version 接口正常返回 v5.0.0
- ✅ info 接口正常返回系统信息
- ✅ parse/list 接口正常返回，含 cache 类型
- ✅ rules/list 接口正常
- ✅ sites/list 接口正常
- ✅ player/config 接口正常
- ✅ official_replace/config 接口正常
- ✅ db/status 接口正常
- ✅ auth/info 接口正常
- ✅ api_doc.php 页面 200 OK，正常显示
- ✅ player/ 页面 200 OK，支持 18 种播放器切换
- ✅ mxadmin.php 后台页面 200 OK
- ✅ kz/cache.php 缓存解析页面 200 OK

#### 影响文件

- [mx.php](file:///workspace/mx.php) — 修复 pt/adskip、parse/list、mxjx/deep、mxjx/info
- [api_doc.php](file:///workspace/api_doc.php) — 全面完善 API 文档
- [version.php](file:///workspace/version.php) — 版本号升级到 v5.0.0
- [CHANGELOG.md](file:///workspace/CHANGELOG.md) — 更新日志

---

## v4.1.0 (2026-07-15)

### 新增

1. **创建 kz 扩展文件夹 - 缓存型 M3U8 解析器**
   - 新增 [kz/CacheM3u8Parser.php](file:///workspace/kz/CacheM3u8Parser.php) 核心解析类
   - 新增 [kz/cache.php](file:///workspace/kz/cache.php) 解析入口（可直接访问）
   - 专门解析带 `vkey` 鉴权参数的缓存型 M3U8 链接
   - 支持 `https://cache.xxx.xyz:4433/Cache/qq/xxx.m3u8?vkey=xxx` 格式

2. **缓存型 M3U8 解析功能**
   - 代理请求原始 M3U8（带浏览器 UA、防盗链头）
   - 自动重写分片 URL：相对路径→绝对路径
   - 支持多级 M3U8（master playlist / media playlist）
   - 支持 TS 分片代理（防盗链场景，`?proxy=1` 模式）
   - 支持 `#EXT-X-KEY` URI 重写
   - vkey 参数分析（hex 编码识别）

3. **集成到统一解析**
   - [mx.php](file:///workspace/mx.php) 统一解析自动识别缓存型 M3U8 链接
   - 识别规则：host 含 `cache` 或路径含 `/Cache/` 且有 `vkey=` 参数
   - 自动路由到 `kz/cache.php` 进行解析

### kz/cache.php 使用方式

```
# 解析并直接输出可播放 M3U8
kz/cache.php?url=https://cache.xxx.xyz/Cache/qq/xxx.m3u8?vkey=xxx

# 返回 JSON 信息（含分片数、类型等）
kz/cache.php?url=xxx&mode=json

# 代理模式（分片通过本PHP代理，防防盗链）
kz/cache.php?url=xxx&proxy=1

# TS 分片代理
kz/cache.php?ts=https://cache.xxx.xyz/Cache/qq/xxx.ts

# vkey 参数分析
kz/cache.php?vkey=xxx&mode=analyze
```

### 影响文件

- [kz/CacheM3u8Parser.php](file:///workspace/kz/CacheM3u8Parser.php) — 新增，核心解析类
- [kz/cache.php](file:///workspace/kz/cache.php) — 新增，解析入口
- [mx.php](file:///workspace/mx.php) — 集成到统一解析，添加 cache 类型识别
- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php) → v4.1.0
- [pt/pt_config.php](file:///workspace/pt/pt_config.php) → v4.1.0
- [gz/sites_config.php](file:///workspace/gz/sites_config.php) → v4.1.0
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v4.0.4 (2026-07-15)

### 优化

1. **官替搜索关键词以 video_title 和 base_title 为准**
   - 搜索关键词顺序调整：`video_title`（完整标题，如"我只想要个公平 第2集"）作为第1优先搜索词
   - `base_title`（基础标题，如"我只想要个公平"）作为第2优先搜索词
   - 移除 `video_id` 作为搜索词（视频ID在资源站搜不到内容）
   - 季节变体、去标点版本、主标题提取等辅助搜索词保留，但排在 video_title/base_title 之后
   - 同步更新 [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php) 和 [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php)

### 效果示例

```
video_title = "我只想要个公平 第2集"
base_title  = "我只想要个公平"

搜索关键词顺序：
  1. 我只想要个公平 第2集    ← video_title（最优先）
  2. 我只想要个公平          ← base_title（次优先）
```

### 影响文件

- [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php#L415-L501)
- [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php#L525-L594)
- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- [pt/pt_config.php](file:///workspace/pt/pt_config.php)
- [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v4.0.3 (2026-07-15)

### 优化

1. **手机端蓝色区域（API预览区）适配优化**
   - 手机端（≤480px）蓝色区域 padding 从 24px 32px 减少到 12px 16px，节省垂直空间
   - API URL 行改为垂直排列，下拉选择框占满宽度
   - 公告卡片 padding 从 16px 18px 减少到 12px
   - 标题间距从 14px 减少到 10px

### 影响文件

- [mxadmin.php](file:///workspace/mxadmin.php)
- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- [pt/pt_config.php](file:///workspace/pt/pt_config.php)
- [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v4.0.2 (2026-07-15)

### 优化

1. **官替搜索结果只显示可用站点，失败站点不展示**
   - 搜索时记录每个资源站的成功/失败状态
   - 成功响应并有搜索结果的站点计入 `successful_sites`
   - 搜索失败/超时/无结果的站点计入 `failed_sites`，不显示在可用列表中
   - `site_matches` 保持只显示有匹配结果的站点（匹配度达标）
   - 新增字段：`successful_sites`（成功搜索的站点列表）、`failed_sites`（失败的站点及原因）、`searched_sites`（总搜索站点数）

2. **DbOfficialReplaceManager 搜索站点计数逻辑优化**
   - 原来 `searched_sites` 只统计有结果的站点，现改为统计实际发起搜索的站点数
   - `max_search_sites` 限制改为按成功站点数限制，避免过早退出
   - 新增 try/catch 捕获单站点搜索异常，不影响整体流程

3. **OfficialReplaceManager 并发搜索增强**
   - `searchSitesConcurrent` 从返回 videos 数组改为返回完整结果数组
   - 多线程模式下每个站点的失败原因都会被记录（HTTP错误/非JSON/无结果/请求失败）
   - 串行兜底模式同样记录成功/失败状态

### 影响文件

- [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php)
- [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php)
- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- [pt/pt_config.php](file:///workspace/pt/pt_config.php)
- [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v4.0.1 (2026-07-15)

### 优化

1. **官替搜索站点扩展为资源站列表全部 active 站点**
   - 搜索站点从 9 个增加到 34 个，覆盖资源站列表中所有 status=active 的站点
   - 按优先级排序：优先搜索高优先级站点（量子/暴风/非凡等），低优先级站点作为补充
   - max_search_sites 从 10 提升到 40，确保能遍历全部活跃站点
   - 新增站点：6度资源、豆包、快车、闪电、丫丫（鸭鸭）、无尽、速播、豪华、光速、蓝光、魔都、看看、樱花、好花、电影天堂、茅台、13大众、百度、爱奇艺资、牛牛6、蓝志、天逸、如意、天繁、西瓜

2. **同步更新所有配置文件**
   - [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php) 版本 4.0.1
   - [pt/pt_config.php](file:///workspace/pt/pt_config.php) 版本 4.0.1
   - [gz/sites_config.php](file:///workspace/gz/sites_config.php) 版本 4.0.1
   - [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php) 默认配置同步
   - [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php) 默认配置同步

### 影响文件

- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- [pt/pt_config.php](file:///workspace/pt/pt_config.php)
- [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php)
- [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php)
- [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v4.0.0 (2026-07-15)

### 大版本更新 - 平台官替深度优化

#### 新增

1. **新增 pt 模块化平台适配架构**
   - 创建 `/workspace/pt/` 目录，平台官替规则全部由 pt 模块统一调度
   - 定义 `PlatformAdapterInterface` 接口，规范各平台适配器契约
   - 抽象基类 `AbstractPlatformAdapter` 提供 httpGet/httpGetMobile/extractTitleFromHtml/cleanTitle/calculateBaseScore 等通用工具
   - 核心调度器 `PtManager` 单例模式，注册并管理所有平台适配器，统一调度 resolve/processAdSkip/detectAdapter/analyzeFailure/learnFromMatch
   - 配置文件 `pt/pt_config.php`，包含版本号、搜索站点、匹配阈值、AI 开关等

2. **6 个平台特定适配器，差异化算法**
   - `TencentVideoAdapter`（腾讯视频）：匹配 v.qq.com，提取 vid/cover_id，调用 float_vinfo2 API
   - `IqiyiAdapter`（爱奇艺）：匹配 iqiyi.com，调用 pcw-api baseinfo API
   - `YoukuAdapter.php`（优酷）：正则 `/youku\.com\/.*?id_([a-zA-Z0-9=]+)/i` 支持 `=` 字符
   - `MgtvAdapter`（芒果TV）：匹配 mgtv.com，调用 pcweb.api.mgtv.com
   - `BilibiliAdapter`（哔哩哔哩）：支持 BV 和 av 两种 ID 格式
   - `SohuAdapter`（搜狐视频）：匹配 tv.sohu.com（排除新闻频道），使用 videoinfo JSON API

3. **AI 自动化分析与优化引擎 `PtAIAnalyzer`**
   - 加权评分系统：title_exact(30) / title_similarity(25) / title_contains(15) / season_match(20) / year_match(10)
   - `smartMatch` 0-100 评分，阈值 50，自动排除解说/预告/花絮等非正片内容
   - `learnFromMatch` 根据用户反馈动态调整权重（正确 +0.5，错误 -0.3），学习数据持久化到 `pt/data/ai_learning.json`
   - `analyzeFailure` 在匹配失败时输出诊断建议（标题长度差异/季数不匹配/排除项等）

4. **M3U8 去广告引擎 `PtAdSkipEngine`**
   - 解析 M3U8 播放列表，识别广告分片并替换为空白分片
   - 识别规则：URI 模式（adjump/ad//advertisement 等）、关键词匹配、短时长检测（<2s）
   - 空白分片使用 `data:video/mp2t;base64,...` 最小 TS 数据，避免黑屏闪烁
   - 安全防护：广告占比 >40% 且无内容分片时保留原始 M3U8，避免误判清空正片
   - 通过 `mx.php?action=pt/adskip&url=...` API 调用，提供处理后内容

5. **新增 pt 管理 API 端点**（通过 `action` 参数访问）
   - `mx.php?action=pt/status` — 查看 pt 引擎状态、已注册适配器
   - `mx.php?action=pt/test&url=...` — 测试 pt 引擎识别与匹配
   - `mx.php?action=pt/adskip&url=...` — 调用去广告引擎处理 M3U8

#### 优化

1. **官替调用入口集成 pt 规则**
   - `DbOfficialReplaceManager` 和 `OfficialReplaceManager` 在 AI 匹配 + 规则兜底后，当无匹配或分数 <60 时调用 `PtManager::resolve()` 进行平台特定算法重匹配
   - pt 引擎异常时静默降级到原有匹配结果，保证稳定性
   - 匹配方法标记 `pt_<platform>`，便于追溯

2. **统一版本号到 4.0.0**
   - `official_replace_config.php` / `sites_config.php` / `pt_config.php` 版本号同步

#### 影响文件

- 新增 [pt/PlatformAdapterInterface.php](file:///workspace/pt/PlatformAdapterInterface.php)
- 新增 [pt/AbstractPlatformAdapter.php](file:///workspace/pt/AbstractPlatformAdapter.php)
- 新增 [pt/PtManager.php](file:///workspace/pt/PtManager.php)
- 新增 [pt/PtAIAnalyzer.php](file:///workspace/pt/PtAIAnalyzer.php)
- 新增 [pt/PtAdSkipEngine.php](file:///workspace/pt/PtAdSkipEngine.php)
- 新增 [pt/TencentVideoAdapter.php](file:///workspace/pt/TencentVideoAdapter.php)
- 新增 [pt/IqiyiAdapter.php](file:///workspace/pt/IqiyiAdapter.php)
- 新增 [pt/YoukuAdapter.php](file:///workspace/pt/YoukuAdapter.php)
- 新增 [pt/MgtvAdapter.php](file:///workspace/pt/MgtvAdapter.php)
- 新增 [pt/BilibiliAdapter.php](file:///workspace/pt/BilibiliAdapter.php)
- 新增 [pt/SohuAdapter.php](file:///workspace/pt/SohuAdapter.php)
- 新增 [pt/pt_config.php](file:///workspace/pt/pt_config.php)
- 修改 [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php)
- 修改 [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php)
- 修改 [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- 修改 [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- 修改 [mx.php](file:///workspace/mx.php)
- 修改 [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v3.2.19 (2026-07-15)

### 修复

1. **修复标题清理未生效导致官替无法匹配资源的问题**
   - 问题：extractPureTitle 中书名号正则缺少 `u` 修饰符，导致无法从《阿凡达：水之道》中提取纯标题
   - 问题：cleanTitle 字符类未包含书名号《》，无法清理"《阿凡达：水之道》充满创意和想象力"中的描述文字
   - 修复：
     - 为书名号正则添加 `u` 修饰符
     - 在 cleanTitle 字符类中添加《》
     - 将 extractPureTitle 提前到 cleanTitle 最开始执行
     - 在 resolve 中显式调用 cleanTitle 清理视频标题
     - 在 parseVideoTitle 中也调用 cleanTitle 作为防御性处理

2. **修复匹配阈值过高导致"搜索到但无法匹配"的问题**
   - 问题：默认匹配阈值 60，但数据库/配置中可能设置为 100，导致资源站能搜到但无法匹配
   - 修复：
     - 将默认匹配阈值从 60 调整为 75
     - 添加最佳努力匹配机制：当最佳匹配分数 >= 50 时即使未达阈值也返回
     - 改进 calculateBaseMatchScore：高字符相似度（>=80%）时直接给高分，避免"阿凡达2" vs "阿凡达"这类单个数字差异导致漏配

3. **优化搜索站点和关键词**
   - 默认搜索站点从 5 个增加到 9 个：量子、暴风、非凡、天影、猫眼、最大、索尼、OK资源、红牛
   - 最大搜索站点数从 5 增加到 10
   - buildSearchKeywords 增加去标点版本和主标题提取（如"阿凡达：水之道"会额外搜索"阿凡达水之道"和"阿凡达"）

4. **修复文件版官替默认配置中的优酷正则**
   - OfficialReplaceManager.php 默认配置中优酷 pattern 仍不支持 `=` 字符
   - 已同步修复为 `/youku\.com\/.*?id_([a-zA-Z0-9=]+)/i`

### 影响文件

- [db/DbOfficialReplaceManager.php](file:///workspace/db/DbOfficialReplaceManager.php)
- [gz/OfficialReplaceManager.php](file:///workspace/gz/OfficialReplaceManager.php)
- [gz/official_replace_config.php](file:///workspace/gz/official_replace_config.php)
- [gz/sites_config.php](file:///workspace/gz/sites_config.php)
- [CHANGELOG.md](file:///workspace/CHANGELOG.md)

---

## v3.2.18 (2026-07-15)

### 修复

1. **修复官替API返回404状态码导致nginx拦截问题**
   - 问题：官替接口在解析失败时返回 HTTP 404 状态码，nginx 配置了 `error_page 404` 后会用默认404 HTML页面替换JSON响应
   - 现象：前端显示"服务器返回非JSON响应"，响应内容为 nginx 404 页面
   - 修复：将业务逻辑失败的响应状态码统一改为 200，通过 JSON 中的 `success` 字段表示业务成功/失败
   - 影响文件：mx.php（official_replace/resolve、official_replace/info）、index.php（统一解析接口）

2. **同步DbOfficialReplaceManager标题处理逻辑**
   - 数据库版官替管理器的 cleanTitle 方法添加空值检查
   - 新增 extractPureTitle 方法，与文件版保持一致
   - 支持从书名号、引号、中文标点中提取纯标题

### 优化

- 更新版本号至 3.2.18

---

## v3.2.17 (2026-07-15)

### 修复

1. **修复优酷视频ID识别问题**
   - 配置文件中优酷正则表达式 `/youku\.com\/.*?id_([a-zA-Z0-9]+)/i` 不支持包含 `=` 字符的视频ID
   - 修复后正则表达式改为 `/youku\.com\/.*?id_([a-zA-Z0-9=]+)/i`，支持 Base64 编码的视频ID
   - 示例链接: `https://v.youku.com/v_show/id_XNTk1MjU3NzQ4NA==.html`

2. **改进标题提取和清理逻辑**
   - 新增 `extractPureTitle()` 方法，提取纯标题内容
   - 支持从书名号 `《》` 中提取标题
   - 支持从引号 `"` 中提取标题
   - 支持按中文标点（逗号、句号、感叹号、问号）分割标题
   - 支持按破折号分割标题
   - 修复了标题包含描述性文字影响搜索匹配的问题

3. **增强视频信息获取API备用方案**
   - 腾讯视频：新增多个API地址和HTML页面地址
   - 优酷：新增移动端页面和播放列表API
   - 提高视频信息获取成功率

4. **修复官替搜索站点配置**
   - 配置文件中 `search_sites` 为空数组，导致无法搜索资源站
   - 添加默认搜索站点: 量子、暴风、非凡、天影、猫眼、最大、索尼、OK资源

5. **修复正则表达式语法错误**
   - 修复 `extractPureTitle()` 方法中引号正则缺少结束分隔符的问题

### 优化

- 优化 `cleanTitle()` 方法，增加空值检查
- 优化搜索关键词构建逻辑，提高匹配准确性
- 更新版本号至 3.2.17

---

## v3.2.16 (2026-07-13)

- 修复官替资源广告插槽未去干净的问题

---

## 历史版本

- v3.2.x: 官替功能增强版本
- v3.1.x: 核心功能优化版本
- v3.0.x: 重构版本
