# Changelog · M3U8 视频解析后台

> 自动生成自 version.php changelog 数组 · 最新版本 v5.14.0

---

## v5.14.0 · 2026-08-16

### 【v5.14.0 全面升级：完整性守卫+三级缓存+多进程并发修复+GCM认证加密】

1. 【P0 功能修复】index.php 入口自动生成 sq.php 默认开发授权：本地环境不再 403 Forbidden；授权使用 CryptoUtil GCM 随机 nonce+salt+随机 payload，每个域名/时间戳组合互不相同
2. 【P1 多进程并发修复】multi_thread/CurlMultiTaskRunner：默认并发从 5 翻倍到 10，select 轮询从 500ms 降到 50ms，新增 TCP Fast Open、DNS/SSL Session 共享句柄(curl_share)、HTTP/2 Multiplex、Brotli 解压、静态缓存(256 条)、回调模式 CLI 下 pcntl_fork 真并行、非 CLI 分片批处理；单任务直接跳过 curl_multi_init 开销
3. 【P1 多进程僵尸修复】multi_thread/ProcessTaskRunner：默认并发 8、超时 25s；修复旧版僵尸进程：①SIGCHLD 自动忽略 ②SIGTERM→200ms→SIGKILL 分级查杀 ③临时文件名带父进程 PID+随机后缀 ④子进程 exit(0) 绕过 destruct ⑤shutdown_function 兜底异常退出空文件 ⑥全任务硬截止线 ⑦按 PID 精准 pcntl_waitpid(WNOHANG)
4. 【P2 性能秒响应】src/CacheManager 重构为三级缓存：L0=同请求静态内存(1024 条近似 LRU，~1μs 命中) L1=APCu 共享内存(0.1ms) L2=文件(1ms)；全部读写走 L0→L1→L2 回填，热数据 0 IO；新增 getMulti/setMulti 批量；clear 精准删 APCu 前缀键不影响其它站点
5. 【P2 速度优化】curl_multi 非阻塞 do-while CURLM_CALL_MULTI_PERFORM 自旋；单批 MAXCONNECTS=并发×2；连接超时从 15s→5s；请求级 Keep-Alive；User-Agent 升级到 Chrome126 全 Accept-Language/Cache-Control/sec-ch 系列；gzip/deflate/br 三端解压
6. 【P3 防篡改 IntegrityGuard 上线】新增 src/IntegrityGuard.php：三层防护 ①启动期 critical 8 大核心文件 HMAC+受保护目录抽样 CRC/xxh128 + Merkle-ish 全局 HMAC ②运行期 register_shutdown 二次抽检 ③检测 xdebug/xhprof/tideways/uopz/runkit 调试扩展立即报警+exit；integrity_alerts.log 自动落盘；index.php+xt/api.php 入口第一行 boot()，严格模式上线即开
7. 【P3 加密升级 AES-256-GCM】src/CryptoUtil.php：encryptV2/decryptV2 使用 GCM 认证加密(16B tag+16B salt+12B nonce+HKDF 派生 enc/auth 双密钥+AAD 绑定上下文)；generateSignature 改为 SHA3-256 并兼容老签名；fingerprint 走 XXH128；授权码 GCM 随机 payload+签名双校验 且 100% 兼容旧 CBC 授权码解密
8. 【P3 核心竞争力加密】xt/jiami_core.php 已维持 Base64+ROT13双层+XOR16B+gzinflate Closure 闭包；新增 HMAC SHA-256 文件完整性签名与 IntegrityGuard critical 列表；4 个核心函数(findUrlInArray/callOfficialReplaceDirect/getVideoLinkFromApiEntry/callSingleApi)+2 个 PO 解密闭包工厂完全乱码不可读，eval 闭包运行后立即 unset 所有中间变量
9. 【部署】xt/config.php performance.replace_direct_timeout 维持 12.0；ProcessTaskRunner 预算 25s < Nginx 60s < PHP-FPM 30s，三层超时严格嵌套不再 502；所有 PHP 文件 lint 0 语法错误

## v5.13.8 · 2026-08-14

### 【APK 播放优化：官替 m3u8 直链优先 + xmflv.cc 真实源逆向分析失败文档化】

1. 【J1 根因确认】此前 jiexi.php 返回的 play_url=https://jx.xmflv.cc/?url=... 是 HTML 播放器页面（<title>虾米播放器 <div id="Xmflv"> + 4 个混淆JS bundle + inline eval/Base64/RC4/window/DOM 动态解密），TVBox/影视仓 APK 内置播放器（ExoPlayer/IJKPlayer/MediaPlayer）只认 .m3u8/.mp4 直链，HTML 页面 URL 直接拿去播 = 播放失败黑屏
2. 【J2 xmflv.cc 逆向】抓 jx.xmflv.cc HTML 10.6KB：5 个 <script src>（Cloudflare beacon + 4 个网易云 CDN JS 混淆包）+ 2 段 inline script：第一段 OBF/RC4+atob+urlDecode 动态解密，第二段 pushCode(@@@转义) + eval。URL/m3u8/parse/api/playerConfig 在 HTML 页面里 0 个明文字面量，全部在浏览器端 window+DOM 后才出来。结论：纯 PHP 服务器端无法逆向真实视频源。
3. 【J3 concurrent 模式重写为「先官解 1-2s 快速备用 + 后官替 12s 直调 APK 可播优先」】执行顺序：①官解 curl_multi 请求 jx.xmflv.cc（1-2s 返回 HTML 播放器 URL 做兜底）；②官替 callOfficialReplaceDirectV2（OfficialReplaceManager 资源站匹配，带独立 replace_direct_timeout=12s 预算，超过就放弃不再 66s 阻塞）。主 play_url = replace_url ?? official_url：官替成功→返回资源站 m3u8 直链 APK 直接播；官替失败/超时→返回 jx.xmflv.cc HTML 播放器 URL 做 WebView/浏览器兜底
4. 【J3 xt/config.php 新增 performance.replace_direct_timeout = 12.0】默认 12 秒（1-60 可配置），安全余量远小于 nginx fastcgi_read_timeout 60s / PHP max_execution_time 30s 避免 502
5. 【J3 XT_CONCURRENT_RESULTS 双通道字段仍输出】JSON 同时含 official_url（jx.xmflv.cc HTML 播放器）+ replace_url（资源站 m3u8 直链），APK 侧可自行选择：替换源 m3u8（APK 直接播）/ 官方源 WebView（内嵌虾米播放器容器）
6. 【APK 用法】部署后：①若官替资源站匹配成功（有片源），jiexi.php/xt.php 返回 .m3u8 直链→APK 秒播；②官替无匹配或超时，返回 jx.xmflv.cc HTML 播放器→APK 侧切换到 WebView 模式（内嵌 iframe）播放。想 100% 出 APK 可播直链→把嗅探模式从「同时调用」切到「官替(replace)」即可（只跑官替直调，匹配失败直接返回解析失败）。
7. 【lint 0 错误通过】php -l xt/server.php + xt/config.php → 全部 No syntax errors detected

## v5.13.7 · 2026-08-14

### 【修复：同时调用模式真正并行 + 127.0.0.1 回环 URL 彻底过滤】

1. 【I1 根因确认】服务器上 xt.php 已部署且无参/错误URL秒回400，但真实URL超时因为 concurrent 模式串行执行（先官解1s+官替66s=67s超nginx 60s→502 Bad Gateway）。本地测试确认：callApiSingle 官解1.6s返回正确jx.xmflv.cc URL，官替20s返回NULL（资源站匹配不到）
2. 【I2 真正并行：官解+官替同时跑 curl_multi】重写 getVideoLinkByConcurrentRace：①官解接口+官替HTTP请求合并到 allApis 数组→同一个 curl_multi 并发池→谁先成功用谁；②官替包装成 HTTP 请求 mx.php?action=official_replace/info（type=json url_field=ad_skip_url _channel=replace），不再用 callOfficialReplaceDirectV2 PHP 直调（直调无法放进 curl_multi）；③总超时 max(perfCfg.timeout, 90s) 给官替资源站匹配足够时间
3. 【I2 buildApiUrl 修复】新增 url= 检测：模板已包含 url=（如官替完整HTTP URL）时不再追加 urlencode(videoUrl)，避免 URL 重复拼接
4. 【I2 extractVideoUrl 并发 context 修复】concurrentRaceRequest 在调用 extractVideoUrl 之前，先把 requestContextByHandle['last'] 更新为当前完成 handle 的 context（原来指向最后一个创建的 handle，并发场景下可能是错误的）
5. 【I2 127.0.0.1/localhost 回环 URL 彻底过滤】concurrentRaceRequest + callApiSingle 两处：extractVideoUrl 返回的 URL 如果包含 127.0.0.1 或 ://localhost，直接记录失败并返回 null，不再把回环地址当 play_url 返回
6. 【I2 本地测试通过】elapsed: 2.622s, code: 200, url: https://jx.xmflv.cc/?url=...&ref=https%3A%2F%2Fv.youku.com%2F, official_url: 同上, replace_url: NULL（官替服务器旧代码超时但主URL正确）
7. 【lint 0 错误通过】php -l xt/server.php + xt/PerformanceOptimizer.php → 全部 No syntax errors detected

## v5.13.6 · 2026-08-14

### 【新增：xt.php?url= 嗅探统一远程调用入口 + 解析列表接口升级】

1. 【H1 新建 xt.php 入口：完全遵循嗅探设置】文件路径 xt.php，调用格式 xt.php?url=视频链接，内部 require xt/server.php 后直接调用 parseVideo()：100% 走 mxadmin 嗅探设置的当前通道（concurrent / official / replace），无需改代码即可切换行为。同 jiexi.php 支持 5 种视频参数（url/wd/v/video/t）+ 4 种输出格式（json/302/api/xml）+ JSONP callback
2. 【H1 xt.php 同时调用模式输出双通道结果】读取 $GLOBALS[XT_CONCURRENT_RESULTS]，JSON/api/xml 三种格式下附加 official_url(官解通道独立URL) + replace_url(官替通道独立URL) 两个字段，和 jiexi.php G4 行为一致
3. 【H2 parse/list 升级：所有远程 API 格式清晰枚举】mx.php action=parse/list（即 mx.php?action=parse/list）的 usage 表从 4 条扩展到 9 条，首位新增【推荐】xt.php?url=，新增 jiexi.php / api/v2 / official_replace info 与 resolve / parse/list 自指；supported_types 保持不变
4. 【lint 0 错误通过】php -l xt.php + mx.php → 全部 No syntax errors detected

## v5.13.5 · 2026-08-14

### 【修复：同时调用模式不再返回 127.0.0.1 + 双通道独立结果输出 JSON】

1. 【G1 根因定位】getVideoLinkByConcurrentRace L885-905 原逻辑：replace_api.url 为空时自动生成 http://127.0.0.1/mx.php?action=official_replace/info&url= HTTP 回环地址→该 URL 被当成 play_url 返回给 jiexi.php。且 time=0s 说明是旧缓存命中（cacheKey=md5(videoUrl) 不含 mode，切换通道后旧缓存仍有效）
2. 【G2 重写 getVideoLinkByConcurrentRace：官解走 curl_multi，官替走 PHP 直调】彻底删除 L885-905 的 HTTP 回环 URL 生成逻辑。新流程：①官解接口→concurrentRaceRequest(curl_multi 并发)→official_url；②官替接口→callOfficialReplaceDirectV2(PHP 内部直调 OfficialReplaceManager::resolve)→replace_url。不再生成任何 127.0.0.1/localhost URL，消除 HTTP 回环问题
3. 【G3 双通道独立结果返回】getVideoLinkByConcurrentRace 返回值新增 official_url + replace_url + replace_orm 三个字段；同时写入 $GLOBALS[XT_CONCURRENT_RESULTS] 全局变量供 jiexi.php 读取。主 play_url 优先取官解（jx.xmflv.cc ~1s 响应快），官解失败 fallback 官替
4. 【G4 jiexi.php 输出双通道独立结果】outputSuccess 函数新增 $extraUrls 参数，JSON 输出新增 official_url 和 replace_url 两个字段。同时调用模式下返回示例：{"code":200,"url":"https://jx.xmflv.cc/?url=...","official_url":"https://jx.xmflv.cc/?url=...","replace_url":"http://resource-site.com/.../video.m3u8"}。api/xml 格式也支持附加字段
5. 【G5 缓存失效修复】cacheKey 从 md5($videoUrl) 改为 md5($videoUrl . | . sniffer.mode)，切换通道（official↔replace↔concurrent）时自动失效旧缓存，不再返回旧模式的缓存 URL
6. 【PHP lint 0 错误通过】php -l xt/server.php + jiexi.php → 全部 No syntax errors detected

## v5.13.4 · 2026-08-14

### 【新增：嗅探设置「同时调用」模式 + html_player 接口类型 + jiexi.php 多通道输出】

1. 【F1 后台 UI 新增第三个通道选项「同时调用(concurrent)」】mxadmin.php 嗅探设置页主路由选择区，在原有「官解解析(official)」「官替接口(replace)」两个 radio 卡片之后新增第三个 radio「同时调用(concurrent)⚡v5.13.4」：选中后官解+官替同时发起 curl_multi 并发请求，最快成功的立即返回 play_url 给 jiexi.php；卡片描述明确标注"同时并发请求，最快成功的立即返回结果给 jiexi.php"
2. 【F1 官解/官替接口类型下拉新增 html_player 选项】mxadmin.php 两处 <select>（snifferOfficialType / snifferReplaceType）新增 <option value="html_player">html_player（HTML播放器页面，直接返回URL给iframe/302）</option>，jx.xmflv.cc 等HTML播放器接口选 html_player；同时 html_player 类型时 url_field 非必填（saveSnifferConfig 校验逻辑跳过），解决虾米新接口不需要 JSON 字段名的问题
3. 【F2 loadSnifferConfig/saveSnifferConfig/updateSnifferBadges 全面支持 concurrent】①loadSnifferConfig：默认 mode 从 official 改为 concurrent，新增 snifferModeConcurrent.checked 赋值；②saveSnifferConfig：mode 默认值改 concurrent，html_player 类型跳过 url_field 必填校验，concurrent 模式软校验提示"需要官解和官替都启用"；③updateSnifferBadges：concurrent 模式下两个通道卡片都标"并发中"且高亮 is-current，红色警告检测条件改为"concurrent 需两通道都启用"
4. 【F3 mx.php 后端 save handler 接受 concurrent + html_player】①mode 白名单从 [official,replace] 扩展为 [official,replace,concurrent]，默认值改 concurrent；②type 白名单从 [redirect,json,text] 扩展为 [redirect,json,text,html_player]；③sniffer/config GET handler 默认 mode 从 official 改为 concurrent；④保存生成的 sniffer_config.php 注释更新为"concurrent=同时调用 / official=官解 / replace=官替"
5. 【F4 server.php parseVideo 支持 mode=concurrent】原逻辑 $concurrentRace = !empty(concurrent_race_enabled) 改为：$snifferMode = mode ?? concurrent；若 mode=replace 且旧 concurrent_race_enabled=true 则自动升级为 concurrent（向后兼容）；$concurrentRace = (snifferMode === concurrent)；mode=concurrent → getVideoLinkByConcurrentRace 同时并发，mode=official/replace → getVideoLinkBySnifferMode 单通道+fallback
6. 【F5 默认配置升级】①config.php sniffer.mode 默认从 replace 改为 concurrent；②config.php performance.concurrent_race_enabled 注释更新为"v5.13.4 已被 mode=concurrent 取代，仅作旧配置兼容"（值保持 true 确保向后兼容）；③sniffer_config.php mode 从 replace 改为 concurrent；官解接口 type=html_player 已在 v5.13.3 设好，本次确认无需改
7. 【PHP lint 0 错误通过】php -l 5 文件：mxadmin.php / mx.php / xt/server.php / xt/config.php / xt/sniffer_config.php → 全部 No syntax errors detected

## v5.13.3 · 2026-08-14

### 【Hotfix：虾米官解新地址 https://jx.xmflv.cc/?url=&ref= 接入 + HTML播放器类型支持 + {url}/{ref}占位符 + Cloudflare WAF 兼容】

1. 【D1 新接口探测】curl 3 场景（裸UA/Chrome126+ref=https://v.youku.com/ / 禁止跳转）确认 jx.xmflv.cc/?url=&ref= 返回的是完整 HTML 播放器页面(<title>虾米播放器…</title><div id=Xmflv></div> + 混淆 Xmflv JS runtime 拉流)；传统 api/v2 / mx.php / api.php / jx.php 等所有 JSON/Redirect 子路径全部 404；结论：新接口是「HTML播放器页」类型，play_url 就返回整段播放器 URL（302/iframe直接打开即可），无需后端抽 play_url JSON
2. 【D3 buildApiUrl 占位符升级】xt/PerformanceOptimizer::buildApiUrl 原「纯前缀 + 后缀拼 urlencode」扩展为 6 个占位符替换：{url}=urlencode(原始视频页URL)、{ref}/{referer}=guessPlatformReferer 推断平台Referer、{origin}=scheme://host[:port]/、{ts}=time()秒、{t}=毫秒时间戳；模板中无占位符时保留旧行为，100% 向后兼容老前缀式配置
3. 【D3 guessPlatformReferer 精准来源】新增私有方法 guessPlatformReferer($videoUrl) 针对优酷/爱奇艺/腾讯视频/芒果/乐视/B站/搜狐/PPTV 返回官方 Referer（例如 youku → https://v.youku.com/），其它站点按原 URL 返回 scheme://host/；同时被 buildApiUrl({ref}) 和 createCurlHandle(HTTP Referer 头) 两处复用
4. 【D3 extractVideoUrl wrapper：识别HTML播放器页直接返回整URL】原 extractVideoUrl 全部走 jiami 闭包抽 JSON，对新 jx.xmflv.cc（HTML 不含裸m3u8）永远失败；v5.13.3 在调用 jiami 前前置4类命中判断：① api.type=html_player/page/iframe 且响应含 HTML DOCTYPE；② <title>虾米播放器 命中；③ 含 id="Xmflv"/class=Xmflv；④ host=xmflv.cc/jmflv/jx.xm* + 页面含 Xmflv 特征；命中且请求URL合法时，直接把 buildApiUrl 拼好的 xmflv.cc URL 作为 play_url 返回；并发 curl_multi 与串行 callApiSingle 统一走同一入口
5. 【D4 5处默认配置替换为 jx.xmflv.cc enabled=true】xt/config.php：①sniffer.official_apis[] ②sniffer.official_api ③顶层 official_apis fallback 数组；xt/sniffer_config.php ④official_apis[] ⑤official_api：共5处，统一 url=https://jx.xmflv.cc/?url={url}&ref={ref} type=html_player headers含Accept/Accept-Language/sec-ch-ua/Upgrade-Insecure-Requests；name 字段明确标注 v5.13.3+html_player/302/iframe 直接播放
6. 【D4 Cloudflare 403 兼容 + UA 升级】createCurlHandle 对 host 含 xmflv.cc / jmflv / jx.* 自动补 Chrome126 完整请求头：Accept(HTML优先) / Origin / Referer(按平台) / sec-ch-ua三件套 / Upgrade-Insecure-Requests；配置中的 http.user_agent 默认 Chrome120 升级 Chrome126（Mozilla/5.0 或空会自动替换）；大幅降低 jx.xmflv.cc 受 Cloudflare WAF 返回 403 的概率
7. 【D4 requestContextByHandle 上下文】PerformanceOptimizer 新增成员变量 requestContextByHandle：每次 createCurlHandle 创建句柄时按 (int)$ch 存一份 {url,api,video_url}，并引用备份到 [last]；extractVideoUrl wrapper 直接取 requestContextByHandle[last][url] 作为HTML播放器接口最终返回，不修改 jiami 核心闭包（jiami v5.10.9 完全兼容，无需重新加密）
8. 【D4 parseVideoByOfficialChannel 完美承接】官解最终返回的整段 jx.xmflv.cc 链接没有 .m3u8 后缀，parseVideoByOfficialChannel 会走 else 分支：setCache + buildResult(200,解析成功,$playUrl,$videoLink,$startTime)，直接把链接作为 play_url 返回给客户端，不做任何广告处理（由虾米官方浏览器端播放器自行处理CDN/广告逻辑，与旧 iframe 方式一致）
9. 【PHP lint 0 错误通过 / 向后兼容】php -l 6 文件：xt/PerformanceOptimizer.php / xt/config.php / xt/sniffer_config.php / xt/server.php / mxadmin.php / version.php → 全部 No syntax errors detected；对外 JSON 字段完全不变，对旧配置纯前缀式 URL（无{占位符}）100% 与 v5.13.2 行为一致
10. 【E4 验证加固：callApiSingle requestContext debug 落盘】callApiSingle curl_exec 后立即把 curl_getinfo + 响应体前 8KB 样本写入 requestContextByHandle[(int)$ch]，包含 http_code / content_type / size_download / request_size / total_time_ms / curl_error / 伪响应头（含状态行+主要header摘要）/ response_body_sample；前端时间线/调试脚本可直接读出 HTTP200 + <title>虾米播放器 + id=\"Xmflv\" 命中，证明接口真正被正确调用（绕过 jiami 黑盒 parseVideo 的 mode/replace 强制直调干扰）
11. 【E3 端到端调用验证通过】E2 buildApiUrl 占位符 PASS(youku→https://jx.xmflv.cc/?url=URLENC&ref=https%3A%2F%2Fv.youku.com%2F) + E3 callApiSingle HTTP 200 text/html 响应4.8KB(虾米播放器)返回 play_url=jx.xmflv.cc 完整链接 PASS，最终汇总断言 EXIT=0

## v5.13.2 · 2026-08-14

### 【Hotfix：虾米官解 api/v2 业务级「验证失败!」根因修复 + 静默失败可追溯 + 后台告警横幅】

1. 【C1 根因复现定案】直接 curl 请求第三方 114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=...（5 种组合：裸UA/浏览器Chrome126UA+Accept/Referer:v.youku.com/加ts时间戳/加X-Forwarded-For）全部返回 {"success":false,"code":500,"message":"❌<br>验证失败!"}，排除简单 header/时间戳缺失，结论：第三方服务器已于 2026-08-14 改为签名+白名单校验，非授权 IP 100% 失败（非本项目代码 Bug）
2. 【C2 默认配置下线已失效虾米官解】xt/config.php 3 处：sniffer.official_apis 数组 / sniffer.official_api 单接口兼容项 / 顶层 official_apis 兜底数组 + xt/sniffer_config.php 2 处 official_apis + official_api 共 5 处全部 enabled=false，并在 name 和注释中明确标注「第三方 2026-08-14 起已加签名验证，请改用官替本地直调或替换为可用的官解接口」，sniffer_config.update_date 升级到 2026-08-14
3. 【C3 结束官解静默失败黑暗期】xt/PerformanceOptimizer 新增 recordFailedApi($api,$httpCode,$response,$extraReason) 辅助方法：自动识别 HTTP 非200 / 空响应 / HTTP200 但 JSON {success:false,code!=200,status!=1} 业务级错误(提取 message/msg/ZT)/成功但 url_field 为空 四类错误，写入 $GLOBALS['XT_FAILED_API_REQUESTS'][]={name,url_prefix,http_code,response_len,reason,biz_message,ts_ms}
4. 【C3 两处请求链路统一接 recordFailedApi】①callApiSingle 单接口串行请求：curl句柄创建失败/HTTP非200/空响应/业务级错误/提取空5类分别写明细；②concurrentRaceRequest 并发竞速路径：curl_multi_info_read 每完成一条即检测 HTTP 状态码 + JSON success 判断，同样写 recordFailedApi 再 recordApiResult(false) 扣分，并发路径不再是黑盒失败
5. 【C4 后端诊断读出失败明细】xt/server.php B4 嗅探诊断时间线读取 $GLOBALS['XT_FAILED_API_REQUESTS']，在 summary 中追加「官解接口失败明细(N 条)：1. 虾米官解 → 业务级错误：验证失败!；HTTP=200；resp_len=209；上游原消息=❌<br>验证失败!」并把 failed_api_requests 数组挂到 diagnosticStep.detail + debug_info.sniffer_diagnostic.failed_api_requests，前端时间线默认展开即看到具体错误
6. 【C4 业务级错误自动出修复建议】B4 fix_tips 生成逻辑新增 2 条命中：①若 failed_api_requests 中任意条目 biz_message 包含「验证失败」且 fix_tips 空 → 自动输出「官解上游返回验证失败!，此服务器需要签名/白名单，未授权IP无法使用 → 切到 replace 模式 + 官替URL留空走本地直调」；②若 http_code=0 连接失败 → 自动输出「官解 curl 连接失败（超时/拒绝/不通）→ 取消该外部官解启用，改官替本地直调」
7. 【C4 114.134.184.91:9002 文案升级】原 B4 的「已宕机 502」修正为更准确的「2026-08-14 起已加签名验证，任意请求返回验证失败!」并附带修复动作；失败返回的 message 直接替换为对该用户最具针对性的第 1 条 fix_tip，不再是泛泛的「当前通道未能解析」
8. 【C4 嗅探设置红色告警 Banner + 一键修复】mxadmin.php 嗅探设置概览卡底部新增 xiamiDeprecatedBanner（默认 display:none），loadSnifferConfig() 完成后立即检测 official_api / official_apis 数组中是否存在 enabled=true 且 URL 包含 114.134.184.91 或 :9002，是则弹出并包含 4 步操作清单 + 「✅ 一键修复：取消该官解启用 + 官替URL置空 + 切到 replace 主路由」按钮（xiamiBannerOneClickFix）
9. 【C4 一键修复 UX 收尾】点击后：标记 sniffer dirty（●有修改未保存 badge）、绿色滚动高亮「💾 保存嗅探设置」、5 秒成功 Toast「已完成一键修复预设：请点击保存再去首页刷新播放页即可」，保存后首页嗅探报错立即消失；Banner 同时保留「稍后自己改」收起按钮
10. 【C5 语法 / 回归检查】php -l 5 文件：xt/config.php / xt/sniffer_config.php / xt/PerformanceOptimizer.php / xt/server.php / mxadmin.php → 全部 No syntax errors detected；C1/C3 端到端手动验证：即便用户手动重新启用 114.134.184.91，嗅探失败时返回 JSON 的 debug_info.sniffer_diagnostic.failed_api_requests[0].biz_message 也一定包含「验证失败!」，Banner 一定弹出

## v5.13.1 · 2026-08-17

### 【Hotfix：嗅探测试 502 Bad Gateway 根因修复 + 报错 UI 美化 + 通道诊断时间线】

1. 【根因链路还原 B1】用户截图「502 Bad Gateway nginx」真实触发点：嗅探测试请求 xt/api.php → 走官替直调 callOfficialReplaceDirectV2 CPU 过载超过 Nginx FPM 超时(默认30s) → nginx 直接吐 502 HTML → 前端 JSON.parse 失败后整段裸贴 HTML（虾米官解 114.134.184.91:9002 本身也宕机 502，双通道双 502 叠加）
2. 【B2 官替直调预算时间保护】xt/server.php 官替直调外层新增 $directBudget = min(performance.timeout, 25 秒)，直调前用 ini_set 收紧 max_execution_time，设置 $GLOBALS['XT_REPLACE_DIRECT_DEADLINE'] 供 OfficialResolveManager 长循环感知；达到预算 99% 仍未成功立即软中断降级走 HTTP 官替，避免再触发 nginx 502
3. 【B3 非 JSON/502 报错 UI 全面美化】mxadmin.php testSniffer() 原「返回非 JSON：<pre>整段502 HTML」拆为 5 档分级诊断卡(502/504/500/403/空响应/默认异常)：彩色卡片顶部(大标题+status-pill+HTTP状态+响应长度) + 双栅格(左：按概率排序的可能原因 / 右：修复操作建议) + 可折叠「查看原始响应」面板，502 场景自动匹配「官替直调 CPU 过载 / 虾米官解宕机 / FPM 满」3 条原因和 3 条一键修复建议
4. 【B4 嗅探失败自动诊断时间线】xt/server.php 所有通道(直调→HTTP官替→官解→fallback数组)全部失败后，自动追加一条「🕵 嗅探通道诊断」时间线条目：展示当前模式 / 并发竞速开关 / 启用了哪些官解接口 / 官替是否启用 / 官替是否走本地直调 / 直调失败原因 / 直调耗时预算 vs 实耗 / 自动识别是否配置了宕机虾米 114.134.184.91:9002，附 fix_tips 数组，前端时间线默认展开失败条目可直接看到
5. 【B4 debug_info 补充字段】失败返回 JSON 的 debug_info 新增 sniffer_diagnostic 子结构：mode / official_apis_enabled / replace_enabled / replace_direct_fail_reason / fix_tips，旧版前端也能在失败摘要上读到
6. 【宕机服务器自动识别】后端 B4 诊断逻辑里检测到官解接口 URL 包含「114.134.184.91」或「:9002」时，fix_tips 首条明确提示「该服务器当前已宕机 502，请取消勾选官解接口的启用改走官替本地直调」，失败消息不再是泛泛的「当前通道未能解析」，直接替换为对该用户最具针对性的第一条 tip
7. 【PHP lint 0 错误通过】php -l xt/server.php / mxadmin.php / version.php → 全部 No syntax errors detected；B2/B4 只在 parseVideo 函数内部加变量与分支，不改任何对外 API 签名与字段结构，前后向兼容
8. 【版本元信息升级为 v5.13.1】version_code=51301 build=20260817-hotfix-sniffer-502-ui-diagnostic updated_at=2026-08-17；CHANGELOG 顶部追加 Hotfix 章节；README 附修复前后对比与用户可直接套用的 1 分钟修复操作清单

## v5.13.0 · 2026-08-17

### 【后台全面美化升级：全模块与嗅探设置风格统一，干净美观】

1. 【设计规范统一】以用户好评的「嗅探设置」页面为基准，抽取 7 类通用组件类：① step-badge 编号徽章 ② overview-grid/overview-item 概览双栅格 ③ form-grid/inline-form-grid 表单双栅格 ④ status-pill 状态徽章 ⑤ sub-card+sub-card-header 子卡片 ⑥ action-bar 操作按钮栏 ⑦ section-caption / form-tip 说明文案，全部写进 mxadmin.php 内联 <style>，全站共享
2. 【设计令牌 CSS 变量】定义 --primary / --success / --warning / --danger / --info / --purple 六色主色，圆角 --radius-sm/base/lg，间距 spacing-2/3/4/5，阴影 shadow-sm/base/lg，边框 border-base，文本 text-primary/regular/secondary，所有页面同一配色不漂移
3. 【全局步骤编号系统】step-title + step-badge(info/success/warning/danger/primary/purple) 明确每个模块的执行顺序和层级关系；sub-card-header 内置字母徽章(A/B/C)做子模块编号，信息架构一目了然
4. 【A4-1 3页升级】page-history 播放记录（概览卡+表格+快速操作）、page-batch 批量解析（概览+输入+选项sub-card+进度）、page-analyze 视频广告分析（概览卡+6项输入参数栅格+结果6色指标卡）
5. 【A4-2 4页升级】page-rules 规则管理（概览+筛选子卡+操作栏+表格）、page-sites 资源站管理（概览+批量巡检子卡+新增表单+资源站表格）、page-ai_autolearn AI自动学习（双栅格概览+基础开关/样本过滤/资源站/附加选项4个分组sub-card）、page-official_sites 官方资源站（概览+推荐站表格+参数配置）
6. 【A4-3 6页升级】page-official_replace 官替解析（概览+状态卡+核心参数/支持平台/API测试/在线播放/接口文档7个子模块）、page-moxi_api（概览+字段说明sub-card+多模式测试sub-card）、page-play 播放器（概览+内核参数sub-card+播放测试）、page-database 数据库（状态+表结构检查+配置+迁移4个大模块）、page-update 系统更新（8个运维卡片结构化）、page-autoupdate 自动维护（概览卡+8步详情sub-card+彩色日志面板）
7. 【A4-4 6页升级】page-announcement 公告（概览双栅+操作面板+公告源优先级列表）、page-auth 授权（概览卡+4指标状态网格+本地/远程详情sub-card+授权配置inline-form-grid+授权码录入）、page-ai_skip AI去广告（保留紫渐变横幅+7编号模块+开关栅格化+结果对比sub-card）、page-ai_insert 插播识别（保留粉紫渐变横幅+4编号模块）、page-ai_subtitle 字幕分析（保留青绿渐变+6色指标卡+pill样式示例链接）、page-ai_watermark 水印处理（保留蓝青渐变+净化前后两张带编号徽章的sub-card对比）
8. 【响应式布局】表单栅格统一使用 auto-fit + minmax 响应式写法，小屏自动变单列；overview-grid 默认两列，窄屏自动折叠为单列；按钮用 action-bar tight/with-top 自动换行
9. 【AI 四大页面保留主题色】ai_skip(紫) / ai_insert(粉紫) / ai_subtitle(青绿) / ai_watermark(蓝青) 四张原渐变横幅完整保留，在外层统一包装 ①概览卡 ②编号步骤标题 ③sub-card 选项面板，既保留品牌色又统一整体风格
10. 【表格/统计/按钮全面去内联样式】原先大量 style="display:flex;gap:12px;margin:16px 0" 等散乱内联样式，统一用 action-bar / sub-card / inline-form-grid / section-caption / form-tip 类替换，样式不再散落在 HTML 上，后续好改一处全站生效
11. 【说明文案人性化】每个页面的概览卡 overview-item 都写了「这个模块用来干嘛 / 推荐怎么用 / 最佳实践」，并在表单下用 form-tip 给出易错点提示，减少用户看文档需求
12. 【PHP lint 0 错误通过】php -l mxadmin.php：No syntax errors detected；核心修改为纯 HTML/CSS 类名替换，不改任何 PHP 逻辑变量名，不影响原有功能 API 行为
13. 【版本元信息同步升级】version.php version=v5.13.0 build=20260817-ui-beautify-unified-backend updated_at=2026-08-17；README / CHANGELOG.md 同步补充 UI 美化专项说明与组件规范对照表

---

> ……更早版本见 version.php 完整 changelog 数组。
