# 更新日志

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
