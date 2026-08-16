# M3U8 视频解析后台 · v5.14.0

> 高性能视频解析与广告去除服务端：**完整性守卫 + 三级缓存 + 多进程并发修复 + AES-256-GCM 认证加密**
>
> 本次大版本（v5.14.0）围绕：**功能修复 · 秒级响应 · 防篡改 · 核心竞争力加密** 四条主线进行全面升级。

---

## ✨ v5.14.0 版本亮点

| 维度 | 升级内容 |
|------|---------|
| **P0 功能修复** | `index.php` 首次运行自动生成开发授权 `sq.php`（仅限 localhost / 回环 / RFC1918 内网 / CLI），本地环境不再 403 Forbidden。 |
| **P1 并发性能** | `CurlMultiTaskRunner` 并发从 5→10；select 从 500ms→50ms；新增 `curl_share`（DNS+SSL 会话复用）、HTTP/2 Pipelining、Brotli 解压、进程内静态缓存；CLI 下回调模式 `pcntl_fork` 真并行。 |
| **P1 僵尸进程** | `ProcessTaskRunner` 修复：①SIGCHLD 自动忽略 ②SIGTERM→200ms→SIGKILL 分级查杀 ③临时文件带父 PID+随机后缀 ④子进程 exit(0) ⑤shutdown_function 兜底 ⑥硬截止线 ⑦精准 WNOHANG |
| **P2 秒级响应** | `CacheManager` **三级缓存**：L0 同请求静态内存（~1µs，1024 条近似 LRU）→ L1 APCu 共享内存（0.1ms）→ L2 文件（1ms）。热数据 0 IO；新增 `getMulti/setMulti` 批量接口。 |
| **P2 网络性能** | `curl_multi` do-while `CURLM_CALL_MULTI_PERFORM` 自旋；`MAXCONNECTS=并发×2`；连接超时从 15s→5s；User-Agent Chrome126 + sec-ch/gzip/br 全 Accept。 |
| **P3 防篡改守卫** | 新增 `IntegrityGuard` 三层防护：①**启动期** 8 个核心文件 HMAC + 受保护目录抽样 CRC/XXH128 + Merkle-ish 全局哈希；②**运行时** `register_shutdown_function` 二次抽检；③**调试扩展检测** xdebug(仅调试模式报警)/xhprof/tideways/uopz/runkit。`integrity_alerts.log` 自动落盘。 |
| **P3 加密升级** | `CryptoUtil` 升级 **AES-256-GCM 认证加密**：MAGIC(5)+salt(16)+nonce(12)+tag(16)+tagMac(32)+cipher，HKDF 派生 enc/auth 双密钥，AAD 绑定 context；签名从 SHA-256 → **SHA3-256**；100% 向后兼容（旧 CBC 授权码 + 旧 SHA-256 签名 × 3 key × 3 JSON flags）。 |
| **P3 核心代码保护** | `xt/jiami_core.php` 六重加密：Base64 双层 + ROT13 位移 + 16B 自定义 XOR 密钥 + `gzinflate()` 压缩混淆 + Closure 闭包工厂。4 个核心函数（findUrlInArray / callOfficialReplaceDirect / getVideoLinkFromApiEntry / callSingleApi）+ 2 个 PO 解密闭包工厂完全不可读。 |
| **P3 授权体系** | 授权码 GCM 随机 payload（含 domain+timestamp+4B nonce）+ SHA3 签名双重校验；远端不可达时 **fail-open** 不影响离线部署；本地开发/内网自动生成授权。 |
| **代码质量** | 10 个关键文件 PHP Lint **0 语法错误**；22/22 冒烟测试 **全部通过**。 |

---

## 🚀 快速开始

### 环境要求
- PHP 7.4+（推荐 8.1+，可使用 XXH128 极速指纹）
- 推荐扩展：`apcu`（共享内存 L1 缓存）、`curl`（并发 HTTP）、`pcntl`/`posix`（多进程）、`openssl`（GCM 加密）
- 磁盘空间：≥ 50MB（缓存、日志、数据库）

### 启动方式
```bash
# 方式一：PHP 内置服务器（开发 / 冒烟测试）
php -S 0.0.0.0:8080 -t .

# 方式二：Nginx + PHP-FPM（生产推荐）
# Nginx root 指向项目根；try_files $uri /index.php$is_args$args

# 方式三：直接调用 API
curl "http://127.0.0.1:8080/?health=1"
```

首次访问 localhost / 内网地址时，系统自动生成开发授权 `sq.php`，**无需手动配置即可使用**。

---

## 📡 API 接口速查

| 路径 | 方法 | 说明 |
|------|------|------|
| `/health` | GET | 健康检查：返回 `{status:"ok", version, ts, uptime}` |
| `/` | GET | 去广告接口：`?url=<视频 URL>`，输出播放 JSON 或直链 |
| `/parse` | GET | 统一解析接口：自动判断官解 / 直链 / 嗅探 |
| `/api/skip` | GET | 去广告 m3u8 统一入口 |
| `/mxjx` | GET | m3u8 媒体流输出 |
| `/xt/api.php` | GET/POST | 超级嗅探 XT 旧版接口，兼容老客户端 |

示例：
```bash
# 健康检查
curl "http://127.0.0.1:8080/?health=1"

# 解析示例
curl "http://127.0.0.1:8080/parse?url=https://www.iqiyi.com/v_abc.html"
```

---

## 🏗️ 架构总览

```
            ┌────────────────────────────────────────────────┐
 HTTP/CLI   │ index.php / xt/api.php                         │
 入口       │  ✅ IntegrityGuard::boot() 第一行启动           │
            │  ✅ sq.php 自动生成 + 双校验                    │
            └───────────────────────┬────────────────────────┘
                                    │
           ┌────────────────────────▼───────────────────────────┐
           │                    业务路由层                        │
           │   /parse  /  /api/skip  /  /mxjx  /  /health        │
           └─────────┬─────────────────────────────┬────────────┘
                     │                             │
          ┌──────────▼──────────┐    ┌────────────▼────────────┐
          │  CurlMultiTaskRunner │    │  ProcessTaskRunner      │
          │  并发 10 / 真并行    │    │  并发 8 / 防僵尸进程    │
          │  curl_share 复用连接  │    │  SIGTERM→SIGKILL 分级   │
          │  L0 进程内缓存 256   │    │  PID 文件隔离 无竞争     │
          └──────────┬──────────┘    └────────────┬────────────┘
                     │                             │
          ┌──────────▼─────────────────────────────▼────────────┐
          │              CacheManager 三级缓存                    │
          │    L0 (µs级)  →  L1 APCu (0.1ms)  →  L2 文件 (1ms)  │
          │    同请求静态     共享内存跨请求         持久化磁盘    │
          └─────────────────────────────────────────────────────┘
                     │
          ┌──────────▼─────────────────────────────────────────┐
          │  CryptoUtil · AES-256-GCM + HKDF + SHA3-256 签名    │
          │  IntegrityGuard · 启动期 / 运行时 / shutdown 三层   │
          │  jiami_core.php · 六重混淆核心竞争力保护             │
          └─────────────────────────────────────────────────────┘
```

---

## 🔒 防篡改与安全性详解

### IntegrityGuard 三层守卫

| 层级 | 时机 | 动作 |
|------|------|------|
| 第 1 层 | 入口 `boot()` 启动期 | 8 大核心文件强校验 HMAC-SHA256（改 1 字节即刻失败）；PROTECTED_DIRS（`src` / `db` / `xt` / `gz` / `multi_thread` / `pt` / `proxy` / `kz`）抽样 8~20 个文件 CRC32+XXH128；Merkle-ish 全局 HMAC 绑定整个受保护目录树。 |
| 第 2 层 | `register_shutdown_function` 退出时 | 二次随机抽检 2~5 个核心文件，比对启动期 hash 确保运行时没有被 `eval override / auto_prepend / OPcache 注入` 篡改。 |
| 第 3 层 | 运行时全程 | 检测调试扩展（`xhprof`/`tideways`/`uopz`/`runkit`）和动态篡改函数（`runkit_function_redefine` 等）；严格模式下立即 exit。 |

**日志位置**：项目根目录 `integrity_alerts.log`，格式：`[时间] 原因 | ip | ua | referer`

### AES-256-GCM 认证加密链

```
用户明文
   │
   ▼
HKDF(salt(16B), ROOT_PEPPER, context)
   ├──▶ enc_key(32B)  AES-256-GCM 数据加密
   └──▶ auth_key(32B) HMAC-SHA256(tag+cipher) 防侧信道
   │
   ▼
AES-256-GCM 加密
   │   ┌─ nonce(12B random)    ─ 每次都不一样
   │   ├─ aad(bind ctx+salt+nonce) 防止跨上下文替换
   │   └─ tag(16B)           ─ 认证标签
   ▼
blob = MAGIC_V2(5B) + salt(16B) + nonce(12B) + tag(16B) + tagMac(32B) + cipher(N)
   │
   ▼
Base64URL 安全编码 (URL/form/cookie 无需二次转义)
```

### 授权码安全体系

```
授权码结构：<GCM(190~240B)> . <SHA3-256-HMAC(64hex)>

验证流程：
  ① explode('.') 拆分两部分
  ② verifySignature(domain + '|' + timestamp) 通过
  ③ decryptV2(GCM token, 'authcode') 得到 JSON: {d, t, r}
  ④ 同时支持旧版 CBC 授权码（verifyLegacyAuthCode 回退）
```

---

## ⚡ 性能优化明细（秒级响应路径）

| 组件 | v5.13.8 | v5.14.0 | 提升 |
|------|---------|---------|------|
| CacheManager | 仅文件缓存（~1ms/次） | L0 静态(1µs)+L1 APCu(0.1ms)+L2 文件 三级回填 | **100×~1000×** |
| CurlMultiTaskRunner 并发 | 5 | **10** | ×2 |
| curl_multi_select 轮询间隔 | 500ms | **50ms** | ×10 灵敏 |
| 连接超时 CURLOPT_CONNECTTIMEOUT | 15s | **5s** | ×3 快速失败 |
| CURLOPT_TIMEOUT | 60s | **30s** | 更短尾延迟 |
| ProcessTaskRunner 并发 | 2 | **8** | ×4 |
| ProcessTaskRunner 超时 | 30s (N>60s) | **25s (严格 < PHP-FPM 30s)** | 不再 502 |
| AuthValidator 远程校验总耗时 | 20s (10+10) | **≤ 6s (3+3)** | ≥ ×3 |
| IntegrityGuard boot 首次 | - | **< 10ms** | 无感 |

---

## 📁 核心变更文件清单 (v5.13.8 → v5.14.0)

```
┌ index.php                       # 入口：IntegrityGuard boot + 自动生成开发授权
├ xt/api.php                      # XT 入口：IntegrityGuard boot
├ sq.php                          # 授权文件：首次运行自动生成（GCM+签名）
├ version.php                     # 版本号 v5.14.0 / code 51400 + changelog
├ src/
│  ├ CacheManager.php             # ★★★ 三级缓存重构 (L0/L1 APCu/L2)
│  ├ CryptoUtil.php               # ★★★ AES-256-GCM+HKDF+SHA3签名
│  ├ IntegrityGuard.php           # ★★★ 全新：三层防篡改守卫
│  └ AuthValidator.php            # ★ 远端 fail-open + 本地开发环境判定
├ multi_thread/
│  ├ CurlMultiTaskRunner.php      # ★★ 并发/超时/共享/缓存全面优化
│  └ ProcessTaskRunner.php        # ★★ 僵尸进程修复 + 分级查杀
└ xt/jiami_core.php               # ★★ 六重混淆核心代码保护
```

★★★ = 架构级重构   ★★ = 重大功能升级   ★ = 关键修复 / 增强

---

## ✅ 冒烟测试报告（PHP 8.5.10-dev · 本仓库内执行）

```
  1. CacheManager set/get 闭环                          ✅ PASS
  2. CacheManager setMulti/getMulti 批量                ✅ PASS
  3. GCM encrypt/decrypt 闭环 x2                        ✅ PASS
  4. GCM nonce 随机(同明文不同密文)                      ✅ PASS
  5. GCM 篡改 1 字节被检测                               ✅ PASS
  6. GCM 上下文参数隔离                                  ✅ PASS
  7. 旧版 v1 CBC encrypt/decrypt 兼容                   ✅ PASS
  8. 新版 SHA3-256 签名验证                             ✅ PASS
  9. 旧版 SHA256 签名 3key × 3flags 全兼容              ✅ PASS
 10. IntegrityGuard boot(false) 通过                    ✅ PASS
 11. IntegrityGuard boot 耗时 < 10ms                    ✅ PASS
 12. TaskRunner 3模式齐全                               ✅ PASS
 13. AuthValidator validateAll 通过                     ✅ PASS
 14. sq.php PHP Lint 通过                               ✅ PASS
 15. 授权码 GCM+签名双验证 domain=[my.app.internal]     ✅ PASS
 16. 版本 v5.14.0 (code 51400)                          ✅ PASS
 17. CurlMultiTaskRunner 实例化 getMode=curl_multi      ✅ PASS
 18. CurlMultiTaskRunner isAvailable() 可用             ✅ PASS
 19. ProcessTaskRunner 实例化 getMode=process           ✅ PASS
 20. ProcessTaskRunner isAvailable() 返回 bool          ✅ PASS
 21. 10 个关键文件 PHP Lint 全部通过                    ✅ PASS
 22. HTTP 接口：/health + /parse + /xt/api.php          ✅ PASS
─────────────────────────────────────────────────────────────
  📊 总计: 22 PASS / 0 FAIL   🎉 全部通过，代码可运行无报错
```

---

## 📦 部署建议

| 环境 | IntegrityGuard `strict` 参数 | 推荐 |
|------|-------------------------------|------|
| 本地开发 (localhost / 内网) | `false` | 自动生成授权，远端 fail-open |
| 测试 / 预发布 | `false` + `integrity_alerts.log` 监控 | 观察告警不阻断 |
| **正式生产** | **`true`** | 篡改即 exit；远端严格校验；公网域名不自动生成授权 |

三层超时嵌套（严格避免 502）：
```
ProcessTaskRunner 单任务 25s
      <  PHP-FPM request_terminate_timeout 30s
            <  Nginx fastcgi_read_timeout 60s
```

---

## 📄 License & 更新记录

- 版本号：**v5.14.0**（version_code `51400`）
- 分支：`main`
- 构建 ID：`20260816-v5-14-0-integrity-guard-perf-boost-concurrency`
- 详细更新日志：见 [CHANGELOG.md](./CHANGELOG.md)
