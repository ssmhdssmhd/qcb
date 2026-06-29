# 自动学习定时任务使用教程

本教程介绍如何配置和使用自动学习定时任务，实现规则的自动更新。

## 📁 文件说明

| 文件 | 说明 |
|------|------|
| `cron_autolearn.php` | 自动学习定时任务主脚本 |
| `cron_logs.php` | 自动学习日志查看页面 |
| `gz/autolearn_logs.php` | 自动学习日志存储文件（自动生成） |
| `gz/autolearn_state.php` | 自动学习状态文件（自动生成） |

---

## ⚙️ 配置方式

### 方式一：宝塔面板（推荐）

#### 1. 访问宝塔面板

登录你的宝塔面板，进入「计划任务」页面。

#### 2. 添加计划任务

点击「添加任务」，按以下配置填写：

| 配置项 | 配置值 | 说明 |
|--------|--------|------|
| 任务类型 | Shell 脚本 | - |
| 任务名称 | M3U8自动学习 | 自定义名称 |
| 执行周期 | 每天 N 点 | 建议每天凌晨2-4点执行 |
| 脚本内容 | 见下方 | - |

**脚本内容（PHP路径请根据实际修改）：**

```bash
#!/bin/bash
php /www/wwwroot/你的网站目录/cron_autolearn.php
```

> 💡 提示：可以先在终端执行 `which php` 查看 PHP 路径

#### 3. 测试执行

- 点击任务列表中的「执行」按钮测试
- 查看执行结果是否成功
- 可通过 `cron_logs.php` 查看日志

---

### 方式二：Linux Cron 定时任务

#### 1. 编辑 crontab

```bash
crontab -e
```

#### 2. 添加定时任务

**每天凌晨3点执行：**
```bash
0 3 * * * /usr/bin/php /www/wwwroot/你的网站目录/cron_autolearn.php >> /www/wwwroot/你的网站目录/gz/cron.log 2>&1
```

**每6小时执行一次：**
```bash
0 */6 * * * /usr/bin/php /www/wwwroot/你的网站目录/cron_autolearn.php >> /www/wwwroot/你的网站目录/gz/cron.log 2>&1
```

**每小时执行一次（仅到时间才执行，未到间隔会跳过）：**
```bash
0 * * * * /usr/bin/php /www/wwwroot/你的网站目录/cron_autolearn.php >> /www/wwwroot/你的网站目录/gz/cron.log 2>&1
```

#### 3. 保存并生效

保存文件后 cron 会自动加载。

---

### 方式三：URL 访问触发（适合虚拟主机）

如果无法使用 cron，可以通过第三方监控服务访问 URL 来触发。

#### 1. 设置访问密钥（可选但推荐）

编辑 `cron_autolearn.php` 文件，设置访问密钥：

```php
$config = [
    'access_key' => 'your_secret_key_here',  // 设置你的密钥
    // ...
];
```

同时编辑 `cron_logs.php` 的密钥保持一致。

#### 2. 触发地址

```
http://你的域名/cron_autolearn.php?key=你的密钥
```

#### 3. 强制立即执行（忽略间隔时间）

```
http://你的域名/cron_autolearn.php?key=你的密钥&force=1
```

#### 4. 使用关键词搜索学习

```
http://你的域名/cron_autolearn.php?key=你的密钥&keyword=庆余年
```

#### 5. 推荐的免费监控服务

- [UptimeRobot](https://uptimerobot.com/) - 免费网站监控
- 阿里云监控、腾讯云监控等
- 各种站长工具的定时访问功能

> 💡 注意：设置监控间隔建议 >= 1小时，脚本会自动判断是否需要执行

---

## 🖥️ 命令行使用

### 基本执行

```bash
php cron_autolearn.php
```

### 强制执行（忽略间隔时间）

```bash
php cron_autolearn.php force
```

### 指定关键词搜索学习

```bash
php cron_autolearn.php keyword=庆余年
```

### 指定参数执行

```bash
php cron_autolearn.php force keyword=流浪地球 max_sites=3 videos_per_site=5
```

### 查看日志

```bash
php cron_logs.php
```

---

## 🔧 脚本参数说明

### URL 参数

| 参数 | 类型 | 说明 |
|------|------|------|
| `key` | string | 访问密钥（配置后必填） |
| `force` | int | 是否强制执行，1=强制 |
| `keyword` | string | 按关键词搜索影视学习 |
| `max_sites` | int | 最大处理站点数 |
| `videos_per_site` | int | 每站学习视频数 |

### CLI 参数

```bash
php cron_autolearn.php [force] [keyword=xxx] [max_sites=N] [videos_per_site=N]
```

---

## 📊 日志查看

### 网页查看

访问：`http://你的域名/cron_logs.php?key=你的密钥`

功能包括：
- 上次学习时间
- 日志列表（最近100条）
- 一键立即执行
- 跳转到后台管理

### 文件查看

日志存储在 `gz/autolearn_logs.php`，可直接下载查看。

### 日志类型

| 类型 | 图标 | 说明 |
|------|------|------|
| info | ℹ️ | 普通信息 |
| success | ✅ | 执行成功 |
| warning | ⚠️ | 警告/跳过 |
| error | ❌ | 执行失败 |

---

## ⚙️ 自动学习配置

在后台管理「资源站管理」页面可以配置：

| 配置项 | 说明 | 默认值 |
|--------|------|--------|
| 启用自动学习 | 总开关 | 启用 |
| 更新间隔 (天) | 两次学习的最小间隔 | 3天 |
| 每站视频数 | 每个资源站学习几个视频 | 5个 |
| 每次最大站点数 | 每次最多处理几个站点 | 5个 |
| 最小片段数 | 视频最少片段数（过滤短视频） | 50 |
| 最大广告占比 (%) | 超过则不学习该视频 | 90% |

---

## 🔒 安全建议

1. **设置访问密钥** - 防止恶意调用消耗服务器资源
2. **限制执行频率** - 建议每天1-2次，不要过于频繁
3. **保护日志文件** - 日志文件是 PHP 格式，无法直接访问
4. **使用 HTTPS** - 如果通过 URL 触发，建议使用 HTTPS

---

## ❓ 常见问题

### Q1: 如何确认定时任务在运行？

A: 
1. 查看 `cron_logs.php` 是否有定时执行的日志
2. 查看 `gz/autolearn_logs.php` 文件内容
3. 在宝塔/监控服务中查看执行记录

### Q2: 任务执行了但没有学习成功？

A: 可能的原因：
- 资源站接口访问失败（网络问题）
- 视频 M3U8 无法访问
- 视频片段数不足
- 广告占比过高
- 域名规则已是最新

查看日志文件了解具体原因。

### Q3: 可以同时搜索指定影视学习吗？

A: 可以！有两种方式：
1. 访问 `cron_autolearn.php?keyword=影视名`
2. 在后台「搜索影视学习」页面手动搜索学习

### Q4: 学习失败会影响现有规则吗？

A: 不会。只有学习成功才会更新规则，失败时保持原有规则不变。

### Q5: 如何临时停止自动学习？

A: 在后台「资源站管理」页面关闭「启用自动学习」开关即可。

### Q6: 定时任务和手动学习冲突吗？

A: 不会。脚本有锁机制，同时只能有一个实例在运行。

---

## 📝 示例配置

### 推荐配置（每天执行）

**宝塔面板：**
- 执行周期：每天 凌晨3点
- 命令：`php /www/wwwroot/你的域名/cron_autolearn.php`

**后台配置：**
- 更新间隔：3天
- 每站视频数：5个
- 最大站点数：5个
- 最小片段数：50
- 最大广告占比：90%

---

## 🔗 相关文件

- 定时任务脚本：[cron_autolearn.php](cron_autolearn.php)
- 日志查看页面：[cron_logs.php](cron_logs.php)
- 资源站配置：[gz/sites_config.php](gz/sites_config.php)
- 资源站管理类：[gz/ResourceSiteManager.php](gz/ResourceSiteManager.php)
- 后台管理页面：[mxadmin.php](mxadmin.php)

---

**如有问题，请查看日志或联系技术支持。**
