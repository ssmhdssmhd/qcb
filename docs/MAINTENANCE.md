# 维护 / 运维手册

> 维护好一个庞大 PHP 项目的关键：约定、可运行验证、版本留痕、远程更新兼容。

## 一、日常验证（改动后必跑）

```bash
# 1) 语法检查全部改动文件
php -l parse/ParserFacade.php

# 2) 离线模块化验证（框架骨架）
php parse/tests/cli_verify.php        # 期望 21/21 PASS，退出码 0

# 3) 全项目核心文件语法抽查（旧链路不破坏）
for f in src/M3U8Parser.php src/AdFilter.php gz/Md5AdPlaceholderEngine.php \
         gz/OfficialReplaceManager.php xt/server.php db/Database.php; do
  php -l "$f"
done
```

## 二、目录与职责

| 目录 | 职责 | 改动注意 |
|------|------|----------|
| `parse/` | 新·模块化解析框架 | 新增逻辑优先放这，保持小而专 |
| `src/` | 基础引擎（解析/过滤/缓存） | 改动需跑分析回归 |
| `gz/` | 官替 / 规则 / MD5 占位 | `OfficialReplaceManager.php` 已达 160KB+，**勿再堆积**，新逻辑提取新类 |
| `xt/` | 官解嗅探 | 依赖第三方，注意超时与降级 |
| `pt/` | 平台适配器 | 每平台独立文件 |
| `db/` | 数据库层 | 迁移走 DataMigration |
| `db/autoload.php` | DB 加载 | 新增表类记得登记 |

## 三、常见故障速查

| 现象 | 可能原因 | 处置 |
|------|----------|------|
| 解析一直转圈 | 官替/官解无全局超时 | 用 `parse/Timer` 收敛；网络循环每迭代 `$timer->ok()` |
| 返回 504/超时 | 点击预算外循环 | 检查 `parse/config.php` 的 `global_budget` 与 `hard_deadline` |
| 播放黑屏 | play_url 是 HTML 播放器页 | 切资源站官替优先，保证返回 .m3u8/.mp4 直链 |
| 进度条跳动 | 广告段被删除 | 改用“等时长静音占位”，不删段 |
| 502 nginx | PHP 执行超 CPU | 收紧 `max_execution_time` + 预算软中断 |

## 四、版本与更新

- 每次改动：升 `version.php` 版本、在 `CHANGELOG.md` 顶部追加章节、`README.md` 同步。
- 远程更新（`update.php`）：只覆盖仓库既有文件清单。新增文件（如 `parse/*`）可安全保留；
  对旧文件的改动应尽量收敛为“预算钩子”等最小面，并在 CHANGELOG 留痕，便于升级合并。
- 自动维护：`gx.php` 提供 all/check/migrate/official_refresh/site_check 等调度任务。

## 五、性能与体量规范

- **单文件建议 < 500 行**：过大文件拖慢加载与检索，也难维护。超限应拆分为多个专注类。
- 复用现有引擎（M3U8Parser / EnhancedAdRuleEngine / Md5AdPlaceholderEngine），用
  `class_exists` 守卫接入，接入失败回退轻量路径，绝不中断。