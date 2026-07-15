# 更新日志

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
