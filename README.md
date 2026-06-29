<<<<<<< HEAD
# m3u8-ad-skipper (PHP 版本)
=======
# m3u8-ad-skipper
>>>>>>> trae/agent-4VQjX0

> M3U8 播放列表去广告工具 - 自动识别并移除插播广告片段，生成干净的播放链接

## 功能特性

- 🎯 **多维度广告检测** - 支持关键词、文件名模式、时长范围、不连续标记等多种检测规则
- 🧠 **智能聚类算法** - 自动识别广告片段集群，减少误判
<<<<<<< HEAD
- ⚡ **高性能解析** - 纯 PHP 实现，无需外部依赖
- 📦 **多种输入输出** - 支持本地文件、远程 URL，输出 m3u8 或 JSON 格式
- 🌐 **Web API 服务** - 支持通过 URL 参数直接调用，返回 JSON
=======
- ⚡ **高性能解析** - 纯原生实现，无需外部依赖
- 📦 **多种输入输出** - 支持本地文件、远程 URL，输出 m3u8 或 JSON 格式
- 🌐 **Web API 服务** - 内置 HTTP 服务器，通过 URL 参数直接调用
- 🖥️ **命令行工具** - 开箱即用的 CLI 界面
>>>>>>> trae/agent-4VQjX0
- 🔧 **高度可配置** - 灵活的规则配置，支持自定义规则
- 📊 **详细统计** - 展示移除的广告数量、时长、占比等信息
- 🔓 **CORS 支持** - 支持跨域访问，可直接在前端调用

<<<<<<< HEAD
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
=======
## 安装

```bash
# 克隆仓库
git clone https://github.com/ssmhdssmhd/qcb.git
cd qcb
```

**Node.js 版本**（src/ 目录）：
```bash
# 直接使用（无需安装依赖）
node src/cli.js --help
```

**PHP 版本**（php/ 目录）：
```bash
# 将 php/ 目录部署到 PHP 网站即可使用
# 本地测试：
cd php
php -S 0.0.0.0:8000 index.php
```

## 快速开始

### Web API 服务

```bash
# 启动服务（默认端口 3000）
npm start
# 或
node src/server.js

# 指定端口
PORT=8080 node src/server.js
```

启动后访问：
```
http://localhost:3000/?url=https://example.com/playlist.m3u8
```

返回 JSON 格式的去广告结果。

### 命令行使用

```bash
# 处理本地 m3u8 文件
node src/cli.js input.m3u8

# 处理远程 m3u8 地址
node src/cli.js https://example.com/playlist.m3u8

# 输出到文件
node src/cli.js input.m3u8 -o output.m3u8

# JSON 格式输出
node src/cli.js input.m3u8 --json

# 自定义时长阈值
node src/cli.js input.m3u8 --min-duration 3 --max-duration 20
```

### 代码中使用

```javascript
const M3U8AdSkipper = require('./src/index');

async function main() {
  const skipper = new M3U8AdSkipper({
    minSegmentDuration: 2,
    maxSegmentDuration: 30,
    adKeywords: ['ad', '广告', 'commercial'],
  });

  const result = await skipper.process('https://example.com/playlist.m3u8');

  console.log('原始片段数:', result.stats.totalSegments);
  console.log('保留片段数:', result.stats.keptSegments);
  console.log('移除片段数:', result.stats.removedSegments);
  console.log('广告占比:', result.stats.adPercentage + '%');
  console.log('');
  console.log('过滤后的播放列表:');
  console.log(result.output);
}

main();
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

```javascript
/ad[s]?[-_]?\d+/i,
/advert/i,
/commercial/i,
/pre[-_]?roll/i,
/mid[-_]?roll/i,
/post[-_]?roll/i,
/sponsor/i,
/^ad\//i
```

### 自定义规则

```javascript
const skipper = new M3U8AdSkipper();

skipper.ruleEngine.addRule({
  name: 'custom-rule',
  description: '自定义广告检测规则',
  check: (segment, index, segments) => {
    return segment.uri.includes('my-ad-prefix');
  }
});
```

## API 文档

### M3U8AdSkipper

#### 构造函数

```javascript
new M3U8AdSkipper(options)
```

**参数：**

| 参数 | 类型 | 默认值 | 说明 |
|------|------|--------|------|
| `minSegmentDuration` | number | 2 | 最小片段时长（秒），小于则视为广告 |
| `maxSegmentDuration` | number | 30 | 最大片段时长（秒），大于则视为广告 |
| `adKeywords` | string[] | 见上 | 广告关键词列表 |
| `adFilenamePatterns` | RegExp[] | 见上 | 广告文件名正则模式 |
| `durationTolerance` | number | 0.5 | 时长比较容差 |
| `checkShortSegments` | boolean | true | 是否启用短时长检测 |
| `checkLongSegments` | boolean | false | 是否启用长时长检测 |
| `checkKeywords` | boolean | true | 是否启用关键词检测 |
| `checkFilenamePatterns` | boolean | true | 是否启用文件名模式检测 |
| `checkDiscontinuity` | boolean | false | 是否启用不连续标记检测 |
| `checkRepetitiveDuration` | boolean | false | 是否启用重复时长检测 |

#### process(input, options)

处理 m3u8 播放列表，返回去广告后的结果。

**参数：**
- `input` - m3u8 文件路径、URL 或内容字符串
- `options` - 输出选项

**返回：**
```javascript
{
  original: {...},        // 原始播放列表对象
  filtered: {...},        // 过滤后的播放列表对象
  output: '...',          // 输出的 m3u8 字符串
  stats: {
    totalSegments: 19,    // 原始片段总数
    keptSegments: 10,     // 保留片段数
    removedSegments: 9,   // 移除的广告片段数
    originalDuration: 124.4,  // 原始总时长
    filteredDuration: 86.4,   // 过滤后总时长
    savedDuration: 38,        // 节省时长
    adPercentage: 30.55       // 广告占比
  }
}
```

### M3U8Parser

M3U8 解析器，支持解析本地文件、远程 URL 和字符串内容。

### AdRuleEngine

广告规则引擎，支持添加、移除、检查规则。

### AdFilter

广告过滤器，基于规则引擎过滤广告片段。

### OutputGenerator

输出生成器，支持生成 m3u8、JSON 等格式。

## 命令行参数

```
用法:
  m3u8-ad-skipper <input> [options]

参数:
  input                    M3U8 地址或本地文件路径

选项:
  -o, --output <file>      输出文件路径
  -f, --format <format>    输出格式: m3u8, json (默认: m3u8)
      --json               以 JSON 格式输出
      --min-duration <s>   最小片段时长，小于该值视为广告 (默认: 2)
      --max-duration <s>   最大片段时长，大于该值视为广告 (默认: 30)
      --no-smart           禁用智能广告聚类检测
      --no-stats           不显示统计信息
  -q, --quiet              静默模式
  -h, --help               显示帮助
  -v, --version            显示版本号
```

## 项目结构

```
.
├── src/
│   ├── index.js          # 主入口，M3U8AdSkipper 类
│   ├── parser.js         # M3U8 解析器
│   ├── rules.js          # 广告规则引擎
│   ├── filter.js         # 广告过滤器
│   ├── output.js         # 输出生成器
│   └── cli.js            # 命令行工具
├── test/
│   ├── test.js           # 测试套件
│   ├── sample_with_ads.m3u8   # 带广告的示例
│   ├── sample_clean.m3u8      # 纯净示例
│   └── sample_master.m3u8     # 主播放列表示例
└── package.json
```

## 运行测试

```bash
npm test
# 或
node test/test.js
```

## 示例

### 示例 1: 基本使用

```bash
node src/cli.js test/sample_with_ads.m3u8
```

输出：
```
  m3u8-ad-skipper v1.0.0
  正在处理: test/sample_with_ads.m3u8

#EXTM3U
#EXT-X-VERSION:3
#EXT-X-TARGETDURATION:10
#EXT-X-MEDIA-SEQUENCE:0
#EXTINF:8.000,content_001
segment_001.ts
...
#EXT-X-ENDLIST

  ┌─────────────────────────────────────┐
  │          处理统计信息               │
  ├─────────────────────────────────────┤
  │  原始片段数:   19                    │
  │  保留片段数:   10                    │
  │  移除片段数:   9                     │
  ├─────────────────────────────────────┤
  │  原始时长:     124.4s                │
  │  过滤后时长:   86.4s                 │
  │  节省时长:     38s                   │
  │  广告占比:     30.55%                │
  └─────────────────────────────────────┘
```

### 示例 2: 作为模块使用

```javascript
const M3U8AdSkipper = require('./src/index');

const skipper = new M3U8AdSkipper({
  adKeywords: ['my-ad', '广告'],
  minSegmentDuration: 1,
});

skipper.process('playlist.m3u8').then(result => {
  console.log(`去除了 ${result.stats.removedSegments} 个广告片段`);
  console.log(`节省了 ${result.stats.savedDuration} 秒`);
});
```

## Web API 文档

### 基础信息

- **基础 URL**: `http://your-domain:port`
- **默认端口**: `3000`
- **响应格式**: JSON
- **跨域支持**: ✅ (CORS enabled)
>>>>>>> trae/agent-4VQjX0

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
<<<<<<< HEAD
  "version": "1.1.0-php",
  "language": "PHP",
  "timestamp": "2026-06-27T00:00:00+00:00"
=======
  "version": "1.0.0",
  "timestamp": "2026-06-27T00:00:00.000Z"
>>>>>>> trae/agent-4VQjX0
}
```

### 使用示例

**浏览器直接访问：**
```
<<<<<<< HEAD
http://你的域名/?url=https%3A%2F%2Fexample.com%2Fplaylist.m3u8
=======
http://localhost:3000/?url=https%3A%2F%2Fexample.com%2Fplaylist.m3u8
```

**curl 请求：**
```bash
curl "http://localhost:3000/?url=https://example.com/playlist.m3u8"
>>>>>>> trae/agent-4VQjX0
```

**JavaScript fetch：**
```javascript
const m3u8Url = 'https://example.com/playlist.m3u8';
<<<<<<< HEAD
const apiUrl = `http://你的域名/?url=${encodeURIComponent(m3u8Url)}`;
=======
const apiUrl = `http://localhost:3000/?url=${encodeURIComponent(m3u8Url)}`;
>>>>>>> trae/agent-4VQjX0

fetch(apiUrl)
  .then(res => res.json())
  .then(data => {
    console.log('广告占比:', data.stats.adPercentage + '%');
    console.log('干净的播放列表:', data.playlist.m3u8);
  });
```

<<<<<<< HEAD
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
=======
### 环境变量

| 变量 | 默认值 | 说明 |
|------|--------|------|
| `PORT` | 3000 | 服务监听端口 |
| `NODE_ENV` | - | 设为 development 时错误响应包含堆栈信息 |
>>>>>>> trae/agent-4VQjX0

## 注意事项

1. **广告检测准确率** - 广告检测基于规则匹配，可能存在误判或漏判。建议根据实际使用场景调整规则参数。
2. **加密流** - 当前版本不处理 DRM 加密的流。
3. **主播放列表** - 对于 master playlist，仅传递清晰度信息，需要分别处理每个媒体播放列表。
4. **网络请求** - 处理远程 URL 时需要网络连接，支持 HTTP 和 HTTPS。
<<<<<<< HEAD
5. **文件权限** - 确保 PHP 有读取本地文件的权限。
=======
>>>>>>> trae/agent-4VQjX0

## 许可证

MIT License

## 版本历史

<<<<<<< HEAD
### v1.1.0 (2026-06-27)

- 移植到 PHP 版本
- 完整的 Web API 支持
- 保持与 Node.js 版本相同的功能和接口

### v1.0.0 (2026-06-27)

- 初始版本发布（Node.js）
- 实现 M3U8 解析器
- 实现多规则广告检测引擎
- 实现智能广告聚类过滤
=======
### v1.2.0 (2026-06-27)

- 新增 PHP 版本（php/ 目录）
- 完整移植所有核心功能
- 支持通过 index.php 部署到任意 PHP 网站
- 相同的 API 接口和 JSON 返回格式
- PHP 版本独立测试套件（17 个测试用例）

### v1.1.0 (2026-06-27)

- 新增 Web API 服务，支持通过 URL 参数调用
- 支持 `/?url=` 和 `/api/skip` 两个接口路径
- 内置健康检查接口 `/health`
- 完整的 CORS 跨域支持
- 详细的 JSON 响应格式（统计信息、移除的广告列表等）
- 支持环境变量配置端口

### v1.0.0 (2026-06-27)

- 初始版本发布
- 实现 M3U8 解析器
- 实现多规则广告检测引擎
- 实现智能广告聚类过滤
- 提供命令行工具
- 支持多种输入输出格式
- 完整的测试套件
>>>>>>> trae/agent-4VQjX0
