# 项目架构 / 项目导图

> 本文档梳理整体重构后的架构。`v5.14.0` 将解析核心迁入 `handlers/` 模块，
> `mx.php` 瘦身为路由层；`parse/` 模块化解析框架为资源站优先 + 全局预算主链路，
> 旧链路（xt/server.php / gz / pt / src）保持兼容并行共存。

## 一、顶层结构

```
workspace/  (M3U8 去广告系统, main v5.14.0+)
│
├── mx.php / jiexi.php / index.php / xt.php / gx.php   入口（HTTP API 中枢）
├── mxadmin.php                                         后台管理前端
│
├── handlers/               ★ v5.14.0 新增·解析核心模块（action 处理器）
│   ├── HandlerDispatcher.php 解析 action 分发器（命中即输出并 exit）
│   ├── HandlersContext.php   依赖注入上下文（rootDir/config/db 等）
│   ├── BaseHandler.php       handler 基类（参数/JSON 输出）
│   ├── ParseHandler.php      统一解析入口（parse/parse/info/jx/jx/info）
│   ├── MoxiHandler.php       沫兮解析（moxi/moxi/api）
│   ├── MxjxHandler.php       去广告 M3U8 输出（mxjx）
│   ├── MxjxInfoHandler.php   去广告解析详情（mxjx/info）
│   ├── MxjxDeepHandler.php   深度去广告分析（mxjx/deep）
│   ├── XiamiJxHandler.php    虾米上游官解（xiami_jx/xiami_jx/info）
│   ├── SkipHandler.php       去广告分析（skip）
│   ├── helpers/              SelfUrlHelper / M3u8UrlHelper / TitleExtractor
│   └── autoload.php          模块自动加载器
│
├── parse/                  ★ 新·模块化解析框架（框架主链路）
│   ├── config.php            集中配置：超时预算/平台清单/去广告策略
│   ├── Timer.php             全局总预算计时器（防止卡死）
│   ├── UrlClassifier.php     URL 类型判定（m3u8 / official / other）
│   ├── ParseResult.php       归一化解析结果模型
│   ├── LocalM3u8Cleaner.php  本地去广告 / 去非正片（轻量自包含）
│   ├── ResourceFirstResolver.php  官方链接 → 资源站优先（预算内）
│   ├── ParserFacade.php      统一门面：路由 + 预算 + 归一化输出
│   ├── autoload.php          模块自动加载器
│   └── tests/
│       ├── fixtures/ad.m3u8      离线测试夹具
│       └── cli_verify.php        CLI 可运行验证脚本
│
├── src/                    基础引擎（M3U8Parser / AdFilter / M3U8AdSkipper …）
├── gz/                     高级引擎（官替 OfficialReplaceManager / 规则 / MD5占位）
├── xt/                     超级嗅探解析引擎（官解 server.php / PerformanceOptimizer）
├── pt/                     平台适配器（腾讯/爱奇艺/优酷/芒果/B站/搜狐 …）
├── db/                     数据库层（Database / 各表 Manager / autoload）
├── multi_thread/           多线程调度（CurlMulti / Process TaskRunner）
├── proxy/                  代理池管理
├── kz/ / lz/ / ai/         缓存、资源脚本、AI 扩展
├── player/                 前端播放器页
└── docs/                   ★ 新·技术文档（本目录）
```

## 一·A、请求路由（mx.php 瘦身）

```
请求 → mx.php（瘦路由：加载 handlers/ + parse/ → HandlerDispatcher::dispatch）
        ├─ 命中解析类 action → handlers/*Handler（输出并 exit）
        │     moxi/moxi/api → MoxiHandler     mxjx → MxjxHandler
        │     mxjx/info → MxjxInfoHandler     mxjx/deep → MxjxDeepHandler
        │     xiami_jx/xiami_jx/info → XiamiJxHandler   skip → SkipHandler
        │     parse/parse/parse/info/jx/jx/info → ParseHandler
        └─ 未命中 → 继续原 switch（analyze/rules/sites/update/auth/official_replace/db/proxy… 管理类）
```

## 二、新解析链路（parse 门面）

```
用户 / 播放器
     │  官方URL or M3U8
     ▼
parse/ParserFacade::parse(url, cfg)
     │  ① URL 类型判定（m3u8 / official / other）
     │  ② 开启全局预算 Timer
     ├── m3u8 / other ─────────► parse/LocalM3u8Cleaner   （本地去广告/占位）
     ├── official ──────────────► parse/ResourceFirstResolver
     │        └ 预算内复用 gz/OfficialReplaceManager::resolve()  （资源站优先）
     │                 └ 得 m3u8_url ──► LocalM3u8Cleaner（去广告/去非正片）
     ▼
parse/ParseResult 归一化输出 { code,url,official_url,replace_url,title,episode,channel,step_trace,elapsed }
```

Mermaid（可在 GitHub / 支持 mermaid 的编辑器渲染）：

```mermaid
flowchart TD
    U[用户 / 播放器] --> F[ParserFacade::parse]
    F --> C[UrlClassifier 类型判定]
    C -->|m3u8 / other| L[LocalM3u8Cleaner<br/>本地去广告/占位]
    C -->|official| R[ResourceFirstResolver<br/>资源站优先]
    R --> O[OfficialReplaceManager::resolve<br/>（预算内）]
    O -->|m3u8_url| L
    F --> T[Timer 全局预算]
    F --> P[ParseResult 归一化]
```

## 三、设计要点

| 原则 | 落地 |
|------|------|
| 小而专 | 每个类聚焦单一职责，避免再出现 200KB+ 巨型文件；mx.php 由 6116 行瘦身为路由层（4358 行） |
| 路由先行 | 解析核心迁入 `handlers/`，`HandlerDispatcher` 统一分发 action，未命中回退 mx.php 原 switch |
| 绝不卡死 | 全链路 `Timer` 总预算（官替 25s 硬截止），超限即“快速失败+明确 message+step_trace” |
| 资源站优先 | 官方链接→官替匹配 m3u8→本地去广告/占位 |
| 不中断播放 | 广告段用“等时长静音黑屏占位”，不删段 |
| 向后兼容 | 旧入口不动，`parse/` `handlers/` 增量共存，远程更新不回退（UpdateManager 已保护本地模块） |

## 四、约定

- 解析类 action 统一放 `handlers/`（用 `handlers/autoload.php` 加载），新 action 在 `HandlerDispatcher::MAP` 注册。
- 框架类模块统一放 `parse/`，用 `parse/autoload.php` 加载。
- 对外统一返回 `ParseResult::toArray()` 结构。
- 网络循环务必在每次迭代调用 `$timer->ok()`。
- `mx.php` 仅做路由 + 管理类 action，新增解析逻辑一律写入 handler，避免再次膨胀。