# 模块化架构说明（kzfz 分支 v5.11.0+）

## 一、项目当前为什么能拆（结论：**非常适合模块化改造**）

现有目录结构天然就按功能领域分好了，只是缺了一层"统一开关 + 依赖声明 + 加载管理"：

| 原目录 | 对应新模块 id | 说明 | 是否建议独立开关 |
|---|---|---|---|
| `db/Database.php` 及 `Db*Manager.php` | `core_db` | 底层依赖，强制启用 | 否（强制 true） |
| `src/*`（M3U8Parser/AdAnalyzer...） | `ad_filter` | 解析/过滤核心 | 建议保留 true |
| `pt/*`（腾讯/爱奇艺/B站 Adapter） | `platform_sniffer` | 官解平台适配 | 可关（没官解用户） |
| `gz/ResourceSiteManager.php` + `gz/sites_config.php` | `resource_sites` | 资源站管理 | 可关（纯播放器项目） |
| `gz/OfficialReplaceManager.php` | `official_replace` | 官替识别 | 可关（只走官解不用） |
| `gz/AiAutoLearner.php` | `ai_learn` | AI 自动学习 | 可关（手动维护规则的环境） |
| `proxy/*` | `proxy` | 代理池 | 可关（无 429 问题的线路） |
| `cctv/*`（之前写过的直播源） | `cctv` | CCTV 直播源 | 可关（完全不看直播） |
| `multi_thread/*` | （留作后续 multi_thread 模块） | 多线程跑任务 | 可关（低成本服务器） |

**可行性评估：✅ 完全可行，风险极低。** 关键原因：
1. 现有代码已经按领域分目录，不是"一坨面条"
2. 模块边界天然清晰（ResourceSiteManager 内部自己闭环，不强耦合 AI 学习）
3. `ModuleLoader::boot()` 是"附加层"，不启用 Loader 时，原来的 `require_once` 链路完全不变，向后兼容 100%
4. 模块禁用 = 连文件都不 require，PHP 零开销，不会出现"类不存在"错（因为禁用模块的 API 路由也不会注册，请求打不到它）

---

## 二、架构分层图

```
 ┌─────────────────────────────────────────────────────────┐
 │  入口层（mx.php / mxadmin.php / gx.php / jiexi.php）      │
 │  - 判断是否启用模块化：                                   │
 │     require modules/_core/autoload.php                   │
 │     $loader = ModuleLoader::getInstance()                │
 │     $loader->boot()         ← 只加载 enabled 模块        │
 │     $loader->dispatchRequest('mx.php', $action)          │
 └──────────────────────┬──────────────────────────────────┘
                        │
 ┌──────────────────────▼──────────────────────────────────┐
 │  modules/_core/Loader/（通用框架，不写业务）              │
 │   ├── ModuleLoader          扫描/依赖拓扑/开关/实例化    │
 │   ├── ModuleManifest        manifest 值对象              │
 │   ├── autoload.php          App\Modules\*  PSR-4 加载    │
 │   ├── Contracts/            接口契约（install/health…）  │
 │   └── Traits/               通用默认实现 Trait           │
 └──────────────────────┬──────────────────────────────────┘
                        │ require 时根据 config/modules.php 过滤
 ┌──────────────────────▼──────────────────────────────────┐
 │  modules/* 业务模块目录（8 个，按需启用）                 │
 │    modules/ad_filter/        M3U8广告过滤核心（priority10）│
 │    modules/platform_sniffer/ 平台适配器      （priority20）│
 │    modules/core_db/          核心-数据库层   （priority1） │
 │    modules/resource_sites/   资源站管理      （priority30）│
 │    modules/official_replace/ 官替识别        （priority40）│
 │    modules/proxy/            IP代理池        （priority50）│
 │    modules/ai_learn/         AI自动学习      （priority60）│
 │    modules/cctv/             CCTV直播源      （priority200）│
 │                                                            │
 │  每个模块约定：manifest.php（必填） + Bootstrap.php（可选）│
 └──────────────────────────────────────────────────────────┘
```

---

## 三、添加一个新模块的 5 个步骤（5 分钟搞定）

假设你要加 `module_foo`：

1. **建目录**：`mkdir modules/module_foo && touch modules/module_foo/manifest.php`
2. **写声明**：manifest.php 里写 `id='module_foo' / name/version / requires=['core_db'] / default_enabled=true / priority=100`
3. **写代码**：把业务类放到 `modules/module_foo/Something.php`（命名空间 `App\Modules\module_foo`）
4. **写 Bootstrap**：`modules/module_foo/Bootstrap.php` 实现 `ModuleInterface`（用 `CommonModuleTrait` 只需要实现需要的方法）
5. **跑测试**：`php diagnose_modules.php` 看依赖拓扑和健康检查 → OK

想关闭？后台「模块管理」页面一键 false 或 `config/modules.php` 里 `'module_foo' => false`，立即禁用，连文件都不加载。

---

## 四、迁移路线图（渐进式，零中断）

### Phase 0：当前阶段（kzfz 分支刚初始化，完成 ✅）
- modules/_core 框架（ModuleLoader + Manifest + Contract + Trait + autoload）
- 8 个模块 manifest 骨架 + core_db/resource_sites Bootstrap 示例
- `config/modules.php` 默认开关配置
- `diagnose_modules.php` 自检脚本（下一步创建）

### Phase 1：mx.php 入口集成（1~2 天）
- mx.php 顶部 require modules/_core/autoload.php → ModuleLoader::getInstance()->boot()
- 旧 require_once 块加 if (!ModuleLoader::isEnabled('xxx')) 的 fallback：模块启用就走新路径，禁用就走旧路径（双保险不挂）
- 路由层：先尝试 $routes = ModuleLoader::collectRoutes()；没命中的走旧 switch case 兜底

### Phase 2：各模块拆代码（1~2 周，按模块逐个做）
- 每个模块把自己的业务逻辑从 mx.php 的巨型 switch / gz/*.php 迁移到 `modules/<id>/*.php`
- 旧文件留 1~2 周作为 class_alias 兼容期 → 然后删除
- 每迁完一个模块跑测试：关/开各一次，确认行为一致

### Phase 3：后台「模块管理」页面（2~3 天）
- 新增 mxadmin.php `page-modules`：列所有模块 + 状态（启用/禁用/缺依赖）+ 依赖图 + 健康检查
- 提供开关切换按钮 → 写入 `config/modules.php`（调用 ModuleLoader::saveUserConfig）
- 安装/卸载按钮、健康检查详情面板

### Phase 4：gx.php / jiexi.php 所有入口统一接入（1 天）
- 所有入口走同一套 Loader 初始化逻辑，抽 `bootstrap_common.php`
- 最终完成「按需启用」的模块化架构。

---

## 五、3 个常见顾虑

### Q1：禁用模块后，原来引用它的地方会不会报 "Class not found"？
**不会**。原因：
- 模块禁用时，Loader 不会注册它的 routes；请求入口点根本走不到这模块
- 依赖图上，requires 这个被禁用模块的子模块也会被一并禁用（disabled_missing_deps 告警），不会出现"半吊子"运行
- 若有老代码硬 `new Foo()`：Phase 1 提供 fallback class_alias

### Q2：会不会影响性能？
**反而更快**。因为禁用的模块连 require_once 都不做，PHP opcode 加载量减少；依赖拓扑按 priority ASC 启动顺序也更科学。启用 8 个模块整体 bootstrap 时间预估 < 20ms。

### Q3：模块化改造会不会和现有功能冲突？
**完全不冲突**。`ModuleLoader` 是"加一层"不是"改底层"，`boot()` 返回启用列表，即使你完全不调用它，原 mx.php 的 require_once 逻辑 100% 保留。所以这是一个 **零风险的渐进式改造**。

---

## 六、文件速查表

| 文件 | 作用 |
|---|---|
| [modules/_core/autoload.php](file:///workspace/modules/_core/autoload.php) | PSR-4 风格自动加载 `App\Modules\*` |
| [modules/_core/Loader/ModuleLoader.php](file:///workspace/modules/_core/Loader/ModuleLoader.php) | 核心：扫描/开关/依赖拓扑/boot/路由/健康 |
| [modules/_core/Loader/ModuleManifest.php](file:///workspace/modules/_core/Loader/ModuleManifest.php) | manifest 解析和工具方法 |
| [modules/_core/Contracts/ModuleInterface.php](file:///workspace/modules/_core/Contracts/ModuleInterface.php) | 模块契约接口 |
| [modules/_core/Traits/CommonModuleTrait.php](file:///workspace/modules/_core/Traits/CommonModuleTrait.php) | 默认空实现 Trait，业务模块直接 use |
| [config/modules.php](file:///workspace/config/modules.php) | 用户开关配置（后台可写入） |
| [modules/\*/manifest.php](file:///workspace/modules/resource_sites/manifest.php) | 每个模块的元数据声明 |
| [modules/\*/Bootstrap.php](file:///workspace/modules/resource_sites/Bootstrap.php) | 每个模块的引导类 |
