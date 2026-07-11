# LZ 去广告接口使用说明

简洁易用的 M3U8 去广告接口，支持广告特征码查询和资源站独立脚本。

## 目录结构

```
lz/
├── lz.php                    # 主入口脚本（通用去广告接口）
├── README.md                 # 说明文档
└── resource_scripts/         # 资源站独立脚本目录
    ├── template.php          # 资源站脚本模板
    └── liangzi.php           # 量子资源站示例脚本
```

## 主接口 lz.php

### 1. 去广告解析（JSON输出）

**接口地址：** `/lz/lz.php?url=视频链接`

**请求方式：** GET

**请求参数：**

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| url | string | 是 | M3U8 视频地址 |
| format | string | 否 | 输出格式：json/m3u8，默认 json |
| site | string | 否 | 指定资源站（可选） |

**响应示例：**

```json
{
    "code": 200,
    "msg": "解析成功",
    "data": {
        "original_url": "https://example.com/playlist.m3u8",
        "media_url": "https://example.com/playlist.m3u8",
        "domain": "v.example.com",
        "process_time": "120ms",
        "safeguard_triggered": false,
        "safeguard_reason": "",
        "safeguard_method": "",
        "m3u8_url": "https://your-domain.com/lz/lz.php?action=m3u8&url=...",
        "stats": {
            "total_segments": 695,
            "ad_segments": 482,
            "kept_segments": 213,
            "original_duration": 2800.5,
            "filtered_duration": 856.2,
            "saved_duration": 1944.3,
            "ad_percentage": 69.43
        },
        "ad_segments": [...],
        "content_segments": [...],
        "ad_segment_count": 482,
        "content_segment_count": 213,
        "has_more_segments": false
    }
}
```

### 2. 去广告解析（M3U8输出）

**接口地址：** `/lz/lz.php?action=m3u8&url=视频链接`

直接返回去广告后的 M3U8 播放列表，可用于播放器直接播放。

### 3. 获取广告特征码

**接口地址：** `/lz/lz.php?action=signatures&domain=域名`

**请求参数：**

| 参数 | 类型 | 必填 | 说明 |
|------|------|------|------|
| domain | string | 是 | 域名 |
| type | string | 否 | 特征码类型：duration/discontinuity/sequence/filename |

**响应示例：**

```json
{
    "code": 200,
    "msg": "成功",
    "data": {
        "domain": "v.example.com",
        "has_rules": true,
        "learn_count": 5,
        "confidence_score": 75,
        "signature_count": 12,
        "signatures": [
            {
                "id": 1,
                "type": "duration",
                "value": "2.5",
                "weight": 45,
                "hit_count": 3,
                "confidence": 65,
                "first_seen": "2026-07-01 12:00:00",
                "last_seen": "2026-07-10 15:30:00"
            }
        ],
        "rules": {
            "duration_rules": [...],
            "discontinuity_rules": [...],
            "sequence_jump_rules": [...],
            "filename_patterns": [...],
            "insertion_patterns": [...]
        }
    }
}
```

### 4. 资源站列表

**接口地址：** `/lz/lz.php?action=sites`

返回所有启用的资源站列表。

### 5. 帮助信息

**接口地址：** `/lz/lz.php?action=help`

返回接口使用说明。

## 资源站独立脚本

### 使用方法

每个资源站有独立的 PHP 脚本，可以单独调用：

```
http://你的域名/lz/resource_scripts/资源站名称.php?url=视频链接
```

### 已创建的资源站脚本

| 脚本文件 | 资源站 | 说明 |
|----------|--------|------|
| liangzi.php | 量子 | 推荐资源站 |
| template.php | 模板 | 资源站脚本模板，可复制修改 |

### 响应格式

资源站脚本的响应格式与主接口一致，额外包含 `site` 字段标识资源站名称。

### 创建新的资源站脚本

1. 复制 `template.php` 为新的文件名（如 `baofeng.php`）
2. 修改 `$SITE_CONFIG` 配置数组：
   - `site_name`: 资源站名称
   - `site_url`: 资源站官网
   - `api_url`: 采集接口地址
   - `type`: 资源站类型（默认 maccms）
   - `default_domain`: 默认视频域名
   - `custom_rules`: 自定义规则（可选）

## 广告特征码类型说明

| 类型 | 说明 | 示例值 |
|------|------|--------|
| duration | 片段时长特征 | "2.5" （小于2.5秒的片段） |
| discontinuity | 不连续标记 | "true" （存在DIS标记） |
| sequence | 序列号跳跃 | "5" （序列号跳跃大于5） |
| filename | 文件名模式 | "/ad.*/i" （文件名匹配广告） |

## 特征码权重说明

- 权重值范围：0-100
- 命中次数越多，权重越高（每次+5，最高100）
- 置信度范围：0-100
- 权重值越高，判定为广告的可能性越大

## 在 mx.php 中的广告特征码 API

除了 lz.php 接口，系统还在 mx.php 中提供了完整的广告特征码管理 API：

| 接口 | 说明 |
|------|------|
| `mx.php?action=signatures/list&domain=xxx` | 获取指定域名广告特征码列表 |
| `mx.php?action=signatures/add` | 添加广告特征码 |
| `mx.php?action=signatures/delete&id=xxx` | 删除广告特征码 |
| `mx.php?action=signatures/stats[&domain=xxx]` | 广告特征码统计 |
| `mx.php?action=signatures/clean[&min_confidence=30]` | 清理低置信度特征码 |

**注意：** 广告特征码功能需要数据库支持（配置 db/db_config.php）。

## 安全机制

- 所有接口支持 CORS 跨域访问
- 错误信息友好提示
- 内存保护机制（自动提升到 256M）
- 安全守护机制（防止误判导致内容全删）

## 调用示例

### PHP 调用示例

```php
<?php
$url = 'https://example.com/playlist.m3u8';
$apiUrl = 'http://your-domain.com/lz/lz.php?url=' . urlencode($url);

$result = file_get_contents($apiUrl);
$data = json_decode($result, true);

if ($data['code'] == 200) {
    echo "去广告成功！\n";
    echo "总片段数: " . $data['data']['stats']['total_segments'] . "\n";
    echo "广告片段: " . $data['data']['stats']['ad_segments'] . "\n";
    echo "节省时长: " . $data['data']['stats']['saved_duration'] . "秒\n";
    echo "M3U8地址: " . $data['data']['m3u8_url'] . "\n";
} else {
    echo "失败: " . $data['msg'] . "\n";
}
```

### JavaScript 调用示例

```javascript
const url = 'https://example.com/playlist.m3u8';
const apiUrl = `http://your-domain.com/lz/lz.php?url=${encodeURIComponent(url)}`;

fetch(apiUrl)
    .then(res => res.json())
    .then(data => {
        if (data.code === 200) {
            console.log('去广告成功:', data.data.stats);
            console.log('M3U8地址:', data.data.m3u8_url);
        } else {
            console.error('失败:', data.msg);
        }
    });
```
