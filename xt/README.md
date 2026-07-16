# 超级嗅探 - 视频解析 + AI 去广告工具

基于 PHP 的视频链接解析工具，集成官解接口对接 + 智能广告识别过滤，返回结构化 JSON。

## 功能特性

- **官解接口对接**：支持 redirect / json / text 三种接口类型，多接口轮询
- **智能广告识别**：规则引擎（域名/关键词/不连续标记/时长）+ AI 大模型辅助双重识别
- **去广告 m3u8 生成**：自动过滤广告分段，生成纯净播放地址
- **多级 m3u8 支持**：自动解析主清单→子清单，选择最高分辨率
- **加密 m3u8 兼容**：保留 #EXT-X-KEY 加密标签
- **缓存机制**：去广告 m3u8 缓存，减少重复解析
- **结构化 JSON 输出**：包含原始地址、无广告地址、广告详情

## 文件结构

```
xt/
├── api.php        # 对外 API 接口（前端调用入口）
├── server.php     # 服务端（官解对接 + m3u8 获取 + 广告过滤流程）
├── AdFilter.php   # 广告识别+过滤引擎（规则引擎 + AI 辅助）
├── clean.php      # 去广告 m3u8 播放代理（供前端播放器使用）
├── config.php     # 全局配置（官解接口、AI 密钥、规则、缓存）
├── cache/         # m3u8 缓存目录（自动创建）
└── README.md      # 说明文档
```

## 快速开始

### 环境要求

- PHP 7.4+
- cURL 扩展
- （可选）AI 大模型 API 密钥（通义千问/OpenAI/DeepSeek）

### 配置

编辑 [config.php](file:///workspace/xt/config.php) 填入你的配置：

```php
// 1. 填入官解接口
'official_apis' => [
    [
        'name'    => '官解接口-1',
        'url'     => 'https://your-api.com/parse?url=',  // 替换为你的官解地址
        'type'    => 'redirect',  // 接口返回类型：redirect / json / text
        'headers' => [
            'Authorization' => 'Bearer YOUR_TOKEN',      // 替换为你的密钥
        ],
    ],
],

// 2. 启用 AI 辅助（可选）
'ai' => [
    'enabled'  => true,
    'provider' => 'qwen',           // openai / qwen / deepseek
    'api_key'  => 'YOUR_AI_KEY',    // 替换为 AI API 密钥
    'model'    => 'qwen-plus',
],
```

### 部署

将 `xt` 文件夹上传到 PHP 环境的网站根目录，确保 `cache/` 目录可写。

### 调用接口

```
https://你的域名/xt/api.php?url=视频链接
```

## 返回结构

### 成功响应

```json
{
  "code": 200,
  "msg": "解析成功",
  "data": {
    "original_url": "https://cdn.example.com/video.m3u8",
    "clean_url": "https://你的域名/xt/clean.php?id=abc123def456",
    "format": "m3u8",
    "has_ads": true,
    "ad_info": {
      "has_ads": true,
      "total_ads": 2,
      "total_ad_duration": 45.0,
      "details": [
        {
          "type": "pre-roll",
          "position": "开头广告",
          "start_segment": 0,
          "end_segment": 3,
          "duration": 15.0,
          "reason": "URL含关键词'ad'; 不同CDN域名(ad.cdn.com≠video.cdn.com)",
          "confidence": 0.85
        },
        {
          "type": "mid-roll",
          "position": "中间插播",
          "start_segment": 50,
          "end_segment": 56,
          "duration": 30.0,
          "reason": "时长匹配广告特征(30.0s≈30s); 不连续标记后",
          "confidence": 0.65
        }
      ],
      "video_info": {
        "format": "m3u8",
        "total_segments": 120,
        "clean_segments": 111,
        "total_duration": 3600.0,
        "clean_duration": 3555.0
      }
    }
  }
}
```

### 失败响应

```json
{
  "code": 400,
  "msg": "链接格式不正确"
}
```

## 工作原理

### 处理流程

```
api.php 接收请求
    ↓
server.php 调用官解接口获取 m3u8 直链
    ↓
下载 m3u8 内容 → 处理多级 m3u8
    ↓
AdFilter.php 广告识别
    ├─ 规则引擎：URL关键词 / CDN域名差异 / 不连续标记 / 时长异常 / SCTE-35
    └─ AI 辅助：规则置信度不足时调用大模型分析
    ↓
生成去广告 m3u8 → 缓存到 cache/
    ↓
返回结构化 JSON（原始地址 + 无广告地址 + 广告详情）
    ↓
前端播放器使用 clean_url 播放无广告视频
```

### 广告识别规则

| 规则 | 权重 | 说明 |
|------|------|------|
| URL 关键词 | +0.35 | ts 分段 URL 含 ad/adv/promo/gg 等关键词 |
| CDN 域名差异 | +0.30 | 广告 ts 来自与正片不同的域名 |
| 时长异常 | +0.20 | 分段时长为 15/30/45/60 秒（广告常见时长） |
| 不连续标记 | +0.15 | #EXT-X-DISCONTINUITY 后的分段 |
| SCTE-35 标记 | +0.90 | #EXT-X-DATERANGE 含 SCTE35/AD/BREAK |

置信度超过 0.6 判定为广告；0.2~0.6 之间交给 AI 大模型判断。

### 官解接口类型说明

| 类型 | 说明 | 示例 |
|------|------|------|
| `redirect` | 接口直接 302 跳转到 m3u8/mp4 地址 | 最终 URL 即为直链 |
| `json` | 返回 JSON，含 url 字段 | `{"url":"https://xxx.m3u8"}` |
| `text` | 纯文本返回直链 | `https://xxx.m3u8` |

## 前端使用示例

```javascript
// 1. 调用 API 获取解析结果
const res = await fetch('https://你的域名/xt/api.php?url=' + encodeURIComponent(videoUrl));
const data = await res.json();

if (data.code === 200) {
    console.log('原始地址:', data.data.original_url);
    console.log('无广告地址:', data.data.clean_url);
    console.log('检测到广告:', data.data.ad_info.total_ads, '个');
    console.log('广告总时长:', data.data.ad_info.total_ad_duration, '秒');

    // 2. 使用无广告地址播放
    player.src = data.data.clean_url;
}
```

## 版本更新日志

### v2.0.0 (2026-07-16)

- 重构架构：官解接口对接 + AI 去广告
- 新增 `AdFilter.php` 广告识别+过滤引擎
  - 规则引擎：URL 关键词 / CDN 域名差异 / 不连续标记 / 时长异常 / SCTE-35
  - AI 辅助：规则置信度不足时调用大模型分析（通义千问/OpenAI/DeepSeek）
- 新增 `config.php` 全局配置文件
- 新增 `clean.php` 去广告 m3u8 播放代理
- 支持多级 m3u8 自动解析（主清单→子清单）
- 支持加密 m3u8（#EXT-X-KEY 标签保留）
- 返回结构化 JSON（原始地址 + 无广告地址 + 广告详情）
- 缓存机制：去广告 m3u8 自动缓存
- 修复 api.php 单线程死锁问题（改为 require 内联调用）

### v1.0.0 (2026-07-16)

- 初始版本发布
- 纯 PHP 视频解析，cURL 正则 + Chrome Headless 双方案
- 支持 m3u8 和 mp4 格式嗅探
