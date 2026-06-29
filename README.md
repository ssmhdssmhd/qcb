# m3u8-ad-skipper (PHP 版本)

> M3U8 播放列表去广告工具 - 自动识别并移除插播广告片段，生成干净的播放链接

## 功能特性

- 🎯 **多维度广告检测** - 支持关键词、文件名模式、时长范围、不连续标记等多种检测规则
- 🧠 **智能聚类算法** - 自动识别广告片段集群，减少误判
- ⚡ **高性能解析** - 纯 PHP 实现，无需外部依赖
- 📦 **多种输入输出** - 支持本地文件、远程 URL，输出 m3u8 或 JSON 格式
- 🌐 **Web API 服务** - 支持通过 URL 参数直接调用，返回 JSON
- 🔧 **高度可配置** - 灵活的规则配置，支持自定义规则
- 📊 **详细统计** - 展示移除的广告数量、时长、占比等信息
- 🔓 **CORS 支持** - 支持跨域访问，可直接在前端调用

## 快速开始

### 部署方式

将 `php/` 目录下的所有文件上传到你的 PHP 网站根目录或任意子目录即可使用。

### 目录结构

```
php/
├── index.php              # Web 接口入口
├── src/
│   ├── M3U8AdSkipper.php  # 主类
│   ├── M3U8Parser.php     # M3U8 解析器
│   ├── AdRuleEngine.php   # 广告规则引擎
│   ├── AdFilter.php       # 广告过滤器
│   └── OutputGenerator.php# 输出生成器
└── test/
    ├── test.php           # 测试套件
    └── sample_*.m3u8      # 示例文件
```

## Web API 使用

### 接口地址

直接部署到网站后访问：
```
http://你的域名/?url=<m3u8地址>
```

如果部署在子目录（如 `m3u8/`）：
```
http://你的域名/m3u8/?url=<m3u8地址>
```

### 接口列表

#### 1. 去广告接口

**GET** `/?url=<m3u8地址>` 或 **GET** `/api/skip?url=<m3u8地址>`

**请求参数：**

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| `url` | string | 是 | M3U8 播放列表地址（URL 编码） |

**响应示例：**

```json
{
  "success": true,
  "input": "https://example.com/playlist.m3u8",
  "processTime": "123ms",
  "stats": {
    "totalSegments": 19,
    "keptSegments": 10,
    "removedSegments": 9,
    "originalDuration": 124.4,
    "filteredDuration": 86.4,
    "savedDuration": 38,
    "adPercentage": 30.55
  },
  "playlist": {
    "m3u8": "#EXTM3U\n#EXT-X-VERSION:3\n...",
    "format": "m3u8",
    "segmentCount": 10
  },
  "removed": [
    {
      "uri": "ad_001.ts",
      "duration": 5,
      "title": "ad_pre_roll_01",
      "matchedRules": ["keyword-match", "filename-pattern"]
    }
  ]
}
```

**错误响应：**

```json
{
  "success": false,
  "error": "Bad Request",
  "message": "缺少 url 参数",
  "example": "/?url=https://example.com/playlist.m3u8"
}
```

#### 2. 健康检查

**GET** `/health` 或 **GET** `/api/health`

**响应示例：**

```json
{
  "status": "ok",
  "service": "m3u8-ad-skipper",
  "version": "1.1.0-php",
  "language": "PHP",
  "timestamp": "2026-06-27T00:00:00+00:00"
}
```

### 使用示例

**浏览器直接访问：**
```
http://你的域名/?url=https%3A%2F%2Fexample.com%2Fplaylist.m3u8
```

**JavaScript fetch：**
```javascript
const m3u8Url = 'https://example.com/playlist.m3u8';
const apiUrl = `http://你的域名/?url=${encodeURIComponent(m3u8Url)}`;

fetch(apiUrl)
  .then(res => res.json())
  .then(data => {
    console.log('广告占比:', data.stats.adPercentage + '%');
    console.log('干净的播放列表:', data.playlist.m3u8);
  });
```

## PHP 代码中使用

```php
<?php
require_once 'src/M3U8AdSkipper.php';

$skipper = new M3U8AdSkipper();
$result = $skipper->process('https://example.com/playlist.m3u8');

echo '原始片段数: ' . $result['stats']['totalSegments'] . "\n";
echo '保留片段数: ' . $result['stats']['keptSegments'] . "\n";
echo '移除片段数: ' . $result['stats']['removedSegments'] . "\n";
echo '广告占比: ' . $result['stats']['adPercentage'] . "%\n";
echo "\n过滤后的播放列表:\n";
echo $result['output'];
```

## 广告检测规则

### 内置规则

| 规则名称 | 说明 | 默认状态 |
|---------|------|---------|
| `short-duration` | 片段时长过短（< minSegmentDuration） | ✅ 启用 |
| `long-duration` | 片段时长过长（> maxSegmentDuration） | ❌ 禁用 |
| `keyword-match` | 标题或文件名包含广告关键词 | ✅ 启用 |
| `filename-pattern` | 文件名匹配广告命名正则模式 | ✅ 启用 |
| `discontinuity` | 存在 EXT-X-DISCONTINUITY 标记 | ❌ 禁用 |
| `repetitive-duration` | 重复出现相同时长的短片段 | ❌ 禁用 |

### 默认广告关键词

```
ad, ads, advert, advertisement,
pre-roll, mid-roll, post-roll,
preroll, midroll, postroll,
commercial, promo, sponsor,
广告, 插播, 贴片, 片头, 片尾
```

### 默认文件名模式

```
/ad[s]?[-_]?\d+/i
/advert/i
/commercial/i
/pre[-_]?roll/i
/mid[-_]?roll/i
/post[-_]?roll/i
/sponsor/i
/^ad\//i
```

### 自定义配置

```php
<?php
$skipper = new M3U8AdSkipper([
    'minSegmentDuration' => 3,
    'maxSegmentDuration' => 20,
    'adKeywords' => ['自定义广告关键词'],
    'checkDiscontinuity' => true
]);
```

### 自定义规则

```php
<?php
$skipper = new M3U8AdSkipper();
$engine = $skipper->getRuleEngine();

$engine->addRule([
    'name' => 'custom-rule',
    'description' => '自定义广告检测规则',
    'check' => function($segment, $index, $segments) {
        return strpos($segment['uri'], 'my-ad-prefix') !== false;
    }
]);
```

## 本地开发测试

使用 PHP 内置服务器：

```bash
cd php
php -S 0.0.0.0:8000 index.php
```

然后访问：
- http://localhost:8000/health
- http://localhost:8000/?url=test/sample_with_ads.m3u8

运行测试套件：

```bash
php test/test.php
```

## 环境要求

- PHP 5.6 或更高版本
- 启用 cURL 扩展（用于远程 URL 请求）
- 启用 mbstring 扩展（用于多字节字符串处理）

## 注意事项

1. **广告检测准确率** - 广告检测基于规则匹配，可能存在误判或漏判。建议根据实际使用场景调整规则参数。
2. **加密流** - 当前版本不处理 DRM 加密的流。
3. **主播放列表** - 对于 master playlist，仅传递清晰度信息，需要分别处理每个媒体播放列表。
4. **网络请求** - 处理远程 URL 时需要网络连接，支持 HTTP 和 HTTPS。
5. **文件权限** - 确保 PHP 有读取本地文件的权限。

## 许可证

MIT License

## 版本历史

### v1.1.0 (2026-06-27)

- 移植到 PHP 版本
- 完整的 Web API 支持
- 保持与 Node.js 版本相同的功能和接口

### v1.0.0 (2026-06-27)

- 初始版本发布（Node.js）
- 实现 M3U8 解析器
- 实现多规则广告检测引擎
- 实现智能广告聚类过滤
