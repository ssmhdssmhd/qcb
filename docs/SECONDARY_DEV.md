# 二次开发指南

> 面向要扩展/定制本系统的开发者。聚焦「怎么加一个新功能」，并遵守模块化与可运行约定。

## 一、上手

```bash
# 1) 运行前语法自检
php -v

# 2) 跑一次骨架验证，确认环境 OK
php parse/tests/cli_verify.php          # 期望 21/21 PASS, 退出码 0
```

## 二、目录结构速览

```
parse/  新·模块化解析框架（HTTP 无关、可 CLI 跑）
src/    基础引擎
gz/     官替/规则/去广告高级引擎
xt/     官解嗅探
pt/     平台适配
db/     数据访问
```

## 三、最常用扩展姿势

### 3.1 新增一个「解析通道 / 处理器」

1. 在 `parse/` 新建类，类名驼峰、一个文件一个类、职责单一。
2. 构造函数接收 `array $cfg`（来自 `parse/config.php`）。
3. 返回 `ParseResult`（成功用 `new ParseResult([...])`，失败用 `ParseResult::fail(code,msg)`）。
4. 在 `parse/autoload.php` 登记 `require_once`。
5. 在 `parse/ParserFacade` 的 dispatch 分支接入，或新增分支。

```php
class MyPluginResolver {
    private $cfg;
    public function __construct(array $cfg = []) { $this->cfg = $cfg; }
    public function resolve($url) {
        $timer = new Timer($this->cfg['global_budget'] ?? 25.0);
        if (!$timer->ok()) {
            return ParseResult::fail(504, '解析超时', ['channel' => 'my'] );
        }
        return new ParseResult(['url' => $m3u8, 'title' => '标题', 'channel' => 'my']);
    }
}
```

### 3.2 扩展去广告规则

去广告策略集中在 `parse/config.php` 的 `cleaner`：
- `url_blacklist`：追加广告 URL 关键词（如 `'/heartbeat'`）。
- `ad_duration_max`：调整“短时长视为广告”的阈值。
- `placeholder.mode`：`local_proxy`（服务器生成静音 ts）或 `data_uri`。

> 深度规则引擎（关键词/时长/CUE-OUT/MD5 指纹）在 `gz/EnhancedAdRuleEngine.php` 与
> `gz/Md5AdPlaceholderEngine.php`，可用 `parse/config.php` 的 `cleaner.use_heavy_engine=true` 接入。

### 3.3 新增官方平台

打开 `parse/config.php` → `official_domains` 追加平台域名（如 `'b23.tv'`），
并到 `gz/official_replace_config.php` 的 `platforms` 增补 `domain/pattern/priority`。

### 3.4 新增数据库表

复制 `db/` 某个 *Manager.php 的写法，在 `db/autoload.php` 登记，
并到 `db/DataMigration.php` 补迁移逻辑，确保 `gx.php migrate` 能建表。

## 四、编码约定

- **每个文件一个 class**，文件名 = 类名，PHP 8 语法（`?->`/`match`/构造属性提升均可）。
- 注释用中文，注明新增版本号（`@since 5.13.9`）。
- 网络请求务必接 `Timer` 预算，循环里每迭代 `ok()`。
- 对外返回统一 `ParseResult::toArray()`；生产环境不要 dump 敏感调试字段。
- **改动后跑** `php parse/tests/cli_verify.php`，保证 21/21 通过再提交。

## 五、提交与发布

1. 更新 `version.php`（version/version_code/build/commit/updated_at/changelog）。
2. `CHANGELOG.md` 顶部追加章节。
3. `README.md` 功能清单同步。
4. 涉及旧文件改动→在 CHANGELOG 留痕，便于远程 update 合并。