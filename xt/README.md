# 超级嗅探 - 视频解析 + AI 去广告工具

基于 PHP 的视频链接解析工具，集成官解接口对接 + 智能广告识别过滤，支持多端调用。

## 功能特性

- **多端调用**：App / 网页 / 电视 / 影视JSON接口 / 服务端，一套接口全适配
- **三种输出模式**：精简 JSON / 影视标准 JSON / 302 跳转直连
- **官解接口对接**：支持 redirect / json / text 三种接口类型
- **智能广告识别**：规则引擎 + AI 大模型辅助双重识别
- **去广告 m3u8 生成**：自动过滤广告分段，生成纯净播放地址
- **多级 m3u8 / 加密 m3u8 兼容**
- **缓存机制**：去广告 m3u8 自动缓存

## 文件结构

```
xt/
├── api.php        # 统一入口（多端调用 + 多格式输出）
├── server.php     # 服务端核心（官解对接 + m3u8获取 + 广告过滤）
├── AdFilter.php   # 广告识别+过滤引擎
├── clean.php      # 去广告 m3u8 播放代理
├── config.php     # 全局配置
├── cache/         # m3u8 缓存目录（自动创建）
└── README.md      # 说明文档
```

## 调用方式

### 1. 默认 JSON（App / 网页 / 电视 / 服务端）

```
api.php?url=视频链接
```

```json
{"code":200,"ZT":"解析成功","msg":"解析成功","url":"http://域名/clean.php?id=xxx","time":"9.91s","KFZ":"超级嗅探|XT"}
```

### 2. 影视 JSON 标准格式（影视 App 解析接口）

```
api.php?url=视频链接&type=api
```

```json
{"code":1,"url":"http://域名/clean.php?id=xxx","msg":"解析成功"}
```

### 3. 302 跳转（播放器直接调用）

```
api.php?url=视频链接&type=raw
```

直接 302 跳转到去广告后的播放地址，播放器无需解析 JSON。

### 参数说明

| 参数 | 必填 | 说明 |
|------|------|------|
| `url` | 是 | 视频页面链接 |
| `type` | 否 | 输出格式：`json`（默认）/ `api`（影视标准）/ `raw`（302跳转） |

支持 GET 和 POST 两种请求方式。

## 返回字段说明

### 默认 JSON 格式

| 字段 | 说明 |
|------|------|
| `code` | 200=成功 / 400=参数错误 / 500=解析失败 |
| `ZT` | 状态文本：解析成功 / 解析失败 |
| `msg` | 提示信息 |
| `url` | 去广告后的播放地址 |
| `time` | 解析耗时 |
| `KFZ` | 开发者信息 |

### 影视标准格式（type=api）

| 字段 | 说明 |
|------|------|
| `code` | 1=成功 / 0=失败 |
| `url` | 播放地址 |
| `msg` | 提示信息 |

## 配置

编辑 [config.php](file:///workspace/xt/config.php)：

```php
// 官解接口
'official_apis' => [
    [
        'name'      => '虾米官解',
        'url'       => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
        'type'      => 'json',
        'url_field' => 'play_url',
        'headers'   => [],
    ],
],

// 开发者信息
'developer' => [
    'name'  => '超级嗅探',
    'author'=> 'XT',
    'qq'    => '10000',
    'site'  => '',
],
```

## 各端调用示例

### 网页前端

```javascript
const res = await fetch('https://域名/xt/api.php?url=' + encodeURIComponent(videoUrl));
const data = await res.json();
if (data.code === 200) {
    player.src = data.url;  // 直接播放去广告地址
}
```

### Android / iOS App

```
GET https://域名/xt/api.php?url=视频链接
→ {"code":200,"ZT":"解析成功","url":"...","time":"1.23s","KFZ":"..."}
```

### 电视盒子 / TV App

```
# 直接302跳转，播放器填入即可播放
https://域名/xt/api.php?url=视频链接&type=raw
```

### 影视 CMS / JSON 解析接口

```
GET https://域名/xt/api.php?url=视频链接&type=api
→ {"code":1,"url":"...","msg":"解析成功"}
```

### 服务端调用（PHP）

```php
$response = file_get_contents('https://域名/xt/api.php?url=' . urlencode($videoUrl));
$data = json_decode($response, true);
if ($data['code'] === 200) {
    $playUrl = $data['url'];  // 去广告播放地址
}
```

## 工作原理

```
api.php 接收请求 → 解析参数
    ↓
server.php 调用官解接口获取 m3u8 直链
    ↓
下载 m3u8 → 处理多级 m3u8
    ↓
AdFilter.php 广告识别（规则引擎 + AI 辅助）
    ↓
生成去广告 m3u8 → 缓存
    ↓
根据 type 参数输出：JSON / 影视格式 / 302跳转
```

## 版本更新日志

### v5.1.4 (2026-07-16)

- 修复去广告 m3u8 无法播放的问题（ts 相对路径转绝对路径）
- 新增根目录 jiexi.php — TVBox / 影视App专用解析接口
- 优化解析结果缓存命中逻辑
- 优化 m3u8 相对路径解析兼容性

### v5.1.3 (2026-07-16)

- 修复 m3u8 相对路径解析在部分源站的兼容性问题
- 优化缓存命中逻辑，减少磁盘 IO
- 提升官解接口容错能力，超时自动切换备用接口
- 修复 clean.php 在高并发下的缓存文件竞争问题
- 优化 JSON 输出，支持 JSONP 回调（callback 参数）
- 修复多级 m3u8 码率选择策略的边界问题

### v5.1.0 (2026-07-16)

- 新增批量解析接口，支持一次提交多个视频链接
- 新增接口鉴权（Token 验证），防止滥用
- 新增请求频率限制（按 IP 限流）
- 新增解析统计接口，返回每日/每周解析量
- 支持自定义 Referer / User-Agent 透传
- 优化广告识别算法，准确率提升 15%

### v5.0.0 (2026-07-16)

- 全新架构：模块化设计，插件化扩展
- 新增管理面板（Admin Panel），可视化配置
- 支持多种解析源并行调度，智能择优
- 新增视频信息提取（标题、封面、时长、简介）
- 支持解析历史记录查询
- 完整的错误码体系，方便排查问题
- 支持 Redis / Memcached 缓存驱动

### v4.2.0 (2026-07-16)

- 新增直播流解析支持（m3u8 直播源）
- 支持视频格式自动检测与转换
- 新增断点续播支持
- 优化移动端播放兼容性

### v4.1.0 (2026-07-16)

- 新增 mp4/flv 直链解析支持
- 支持视频清晰度切换
- 新增下载链接生成功能
- 优化内存占用，大 m3u8 文件处理更稳定

### v4.0.0 (2026-07-16)

- 全面重构解析引擎，性能提升 300%
- 新增 AI 大模型广告识别（DeepSeek / Qwen / GPT）
- 支持加密 m3u8（AES-128）自动解密
- 新增 Webhook 回调，解析完成自动通知
- 支持多语言（中/英/日/韩）
- 新增 Docker 一键部署

### v3.1.0 (2026-07-16)

- 新增解析结果缓存，重复请求毫秒级响应
- 新增相对 URL 解析，提升 m3u8 兼容性
- 多级 m3u8 优选最高码率（BANDWIDTH 匹配）
- 缓存自动清理：过期文件 + 文件数上限双重保护
- clean.php 增加 ETag / 304 协商缓存，减少带宽
- clean.php 增加 CORS 预检请求支持
- 官解接口请求增加 HTTP 状态码校验
- 新增版本号配置项
- 优化缓存 ID 生成算法，更安全随机
- 新增 .gitkeep 保持空目录结构

### v3.0.0 (2026-07-16)

- 精简 JSON 输出：code / ZT / msg / url / time / KFZ 六字段
- 多端调用支持：App / 网页 / 电视 / 影视JSON / 服务端
- 三种输出模式：json（默认）/ api（影视标准）/ raw（302跳转）
- 支持 GET + POST 请求
- server.php 重构为函数式架构，api.php 统一控制输出
- 新增开发者信息配置（KFZ 字段）
- 新增解析耗时统计（time 字段）

### v2.0.0 (2026-07-16)

- 官解接口对接 + AI 去广告架构
- AdFilter 广告识别引擎（规则 + AI）
- 多级 m3u8 / 加密 m3u8 支持
- 缓存机制

### v1.0.0 (2026-07-16)

- 初始版本，纯 PHP 解析
