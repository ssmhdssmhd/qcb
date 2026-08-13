<?php
return array (
  'version' => 'v5.10.9',
  'branch' => 'jiami',
  'build' => '20260813-jiami',
  'version_code' => 51009,
  'commit' => 'official-replace-first-original-url-trap-fix-core-encrypted',
  'updated_at' => '2026-08-13',
  'changelog' => 
  array (
    'v5.10.9' => 
    array (
      'date' => '2026-08-13',
      'title' => '【解析失败根因修复 + 官替优先智能识别新架构】',
      'changes' => 
      array (
        0 => '【P0 根因修复】修复 findUrlInArray 误将 original_url 字段当作视频地址的严重 Bug',
        1 => 'Bug 成因：虾米官解返回 验证失败 error JSON 中含 original_url=原始页面URL，递归搜索函数误提取为视频流地址，导致 code=200 返回假成功，实际输出优酷/腾讯原始HTML页面 URL',
        2 => '修复范围：server.php::findUrlInArray() + PerformanceOptimizer::findUrlInArray() + extractVideoUrl() + getVideoLinkFromApiEntry() 共 6 处同步加固',
        3 => '新增 isSafeVideoUrl 三层守卫：①严格不等于原始URL ②同域名必须含 .m3u8/.mp4 等视频扩展名 ③兜底递归必须排除 original_url 等 20+ 非视频字段+排除原域名匹配',
        4 => '通用兜底字段(url/play_url/data.url)从 allowProxy=true 收紧为 allowProxy=false：必须含视频扩展名，避免伪视频地址蒙混过关',
        5 => '【架构升级】默认解析通道改为官替优先(mode=replace)：资源站搜索匹配 > AI智能识别 > 去广告输出，不再依赖虾米官解加密签名',
        6 => 'sniffer.replace_api.enabled 默认从 false 改为 true，url_field 从 m3u8_url 改为 ad_skip_url（优先取已去广告代理地址）',
        7 => '新增 callOfficialReplaceDirect() 本地官替直调通道：检测到 replace_api 指向本地 mx.php official_replace 时，直接 PHP 内调 OfficialReplaceManager，比 HTTP 回环快 30-70%',
        8 => '直调流程：识别平台/提取视频ID → 爬取官方页面获取标题 → 多个资源站并发搜索 → AI+PtManager 混合智能匹配最佳集数 → mxjx/deep AI 去广告+去插播+去水印 → 输出无广告播放地址',
        9 => '调用链安全：callSingleApi 新增官替直调入口，直调失败再 fallback HTTP，两种方式返回的视频地址均已通过 isSafeVideoUrl 校验',
        10 => '官替默认配置升级：default_site 从 量子 改为 抖剧TV，match_threshold 从 75 降至 65 提高命中率，新增 360kan.com 抖剧TV官替平台(priority=1)',
        11 => 'search_sites 首位插入抖剧TV，确保最高优先级命中；平台列表从7个扩容到8个（含360kan.com）',
        12 => 'jiami 分支：核心解析逻辑（callOfficialReplaceDirect / findUrlInArray / extractVideoUrl / isSafeVideoUrl 等）采用 Base64+乱码+自解码加密，防止被恶意扫描特征',
        13 => '【部署修复】sniffer_config.php 默认通道从 official 改为 replace，官替 enabled=false→true，url_field 从 m3u8_url→ad_skip_url，update_date=2026-08-13',
        14 => '【部署修复】config.php（兜底）version 升级到 5.10.9 / build=20260813，与 sniffer_config 保持一致',
        15 => '【解析链路修复】getVideoLinkByConcurrentRace 自动计算 baseUrl 修复：CLI 或 $_SERVER[HTTP_HOST] 非法(空/末尾带点/非Host格式)时，退化到 127.0.0.1，不再生成 http://localhost./ 这种 parse_url 直接失败的域名',
        16 => 'SCRIPT_NAME 目录处理：空/根/当前目录时统一生成不带多余斜杠的 $scriptDir，保证 /mx.php?action=official_replace/info&url= 可直接 curl 请求',
        17 => '官替通道薄包装验证：concurrentRaceRequest apiList 同时带 official + replace 两条，enabled=true，url_field=ad_skip_url，_channel=replace 打标正确',
      ),
    ),
    'v5.10.8' => 
    array (
      'date' => '2026-08-10',
      'title' => '【进度条卡住问题彻底修复 v2】site_check 轻量探测 + 每站独立推进 + 前端卡住自愈',
      'changes' => 
      array (
        0 => '【重点修复】资源站巡检 task_site_check 彻底重构：从 searchVideos(30s×3重试×5策略=最坏几十分钟/站) 改为轻量两阶段探测',
        1 => '两阶段探测：阶段1=根接口 HEAD(3s超时快速排除明显失效)，阶段2=最小搜索 ac=list&limit=1(5s超时单次无重试)，单站最坏 ≤8s',
        2 => '【重点修复】all 动作进度步长拆分：site_check 不再是单步 25% 大权重，拆成 site_check_prepare + N×site_i(每站独立) + site_check_summary，每完成 1 个站立即写盘推进总进度，彻底解决"卡在 88% 不动"',
        3 => '【重点修复】GxProgressTracker 成员变量 public：task_site_check 内可直接判断 steps[$stepKey] 是否存在，精准调用 startStep/finishStep 写盘',
        4 => '前端进度卡住自愈（v5.10.8）：GX_STATE 增加 stuck_* 检测器，percent ≥25s 不变打 warn 日志(最多3条，避免刷屏)，≥180s 不变停轮询 + 手动刷新链接 + toast 提示',
        5 => '卡住自愈分级提示：按当前步骤（site_/ai_learn/official_refresh）给出人性化说明，消除用户"卡住了"的焦虑',
        6 => 'task_id 切换时自动重置日志 & 卡住检测器：避免历史任务遗留数据干扰',
        7 => 'site_check 默认巡检数量从 max=10 收紧为 max=8（all 动作），单步超时 8s，总耗时上限 ~64s（用户仍可在后台 max 输入框调大调小）',
        8 => 'gx_probe_api_root / gx_probe_site_search 新增 CURL_IPRESOLVE_V4：避免 IPv6 DNS 解析超时拉长探测时间',
      ),
    ),
    'v5.10.7' => 
    array (
      'date' => '2026-08-10',
      'title' => '【后台自动更新进度条可视化】一键操作+进度跟踪+彩色日志',
      'changes' => 
      array (
        0 => '后台「系统管理」分组新增「🔄 自动更新/维护」菜单项（page-autoupdate），进度条徽章标识',
        1 => '进度条 UI：渐变紫色进度条 + 条纹动画，支持 0-100% 平滑过渡，显示百分比和当前步骤',
        2 => '步骤详情卡片：按任务权重渲染每个子步骤，待执行(灰)/执行中(蓝动)/成功(绿✅)/失败(红❌) 四态彩色显示',
        3 => '彩色日志区域：info/warn/error/ok(success) 四色分级显示，支持自动滚动开关 + 手动滚动',
        4 => '操作面板：action 下拉(all/check/migrate/official_refresh/ai_learn/site_check/status/reset_key)、max 数量、force 强制',
        5 => '按钮：开始执行 / 刷新进度 / 停止轮询 / 重置密钥 / 手动清理旧进度 五合一操作面板',
        6 => 'gx.php 进度跟踪升级：新增 GxProgressTracker 类，通过 gx/.gx_progress.json 记录步骤权重/百分比/日志/耗时',
        7 => 'gx.php 异步执行：gx_launch_async() 支持 exec() CLI 后台启动，禁用时回退到同步执行不阻塞 HTTP',
        8 => 'gx_execute.php 安全启动接口：HMAC-SHA256 签名 + 时间戳 ±24h，action 白名单校验，支持 exec()/fsockopen 双通道启动',
        9 => 'gx_progress.php 进度查询接口：返回当前任务进度 + 历史执行记录，支持后台轮询（1.2s/次，自动停止）',
        10 => '【重点修复】签名生成改为服务端 gx_token.php：彻底解决 Web Crypto API 需 HTTPS 安全上下文（HTTP/IP 访问不可用）、以及手写 JS HMAC 与 PHP 不一致的问题',
        11 => '安全增强：mxadmin.php 不再将 gx_secret 明文暴露到前端 JS，仅输出 __GX_SECRET_READY__ 标志，签名统一由 gx_token.php 服务端生成',
        12 => 'gx_execute.php 修复：HTTP_HOST 含端口时不再重复拼接 SERVER_PORT，max=null 字符串/JSON null/空/PHP null 四态全部统一识别为 null',
        13 => '浏览器端测试验证：action=status 任务 0s 完成，进度条→100%，步骤详情✅，彩色日志3条正确追加，轮询自动停止',
        14 => '后端集成测试通过：gx_token.php 生成签名 → gx_execute.php 校验通过 → 任务启动success:true → gx_progress.php 读取 percent=100%',
      ),
    ),
    'v5.10.6' => 
    array (
      'date' => '2026-08-10',
      'title' => '【gx.php 线上错误修复 v2】check/migrate/official_refresh 三大任务修复',
      'changes' => 
      array (
        0 => '修复 check 任务 exec() 被禁用报错：禁用时改为 tokenizer 软校验（token_get_all），避免 Call to undefined function exec()',
        1 => '语法校验兼容：function_exists(exec) → php -l / token_get_all 双通道，线上 disable_functions 环境零报错',
        2 => '修复 migrate 任务：DataMigration 实际方法名是 migrateAll，新增按优先级尝试 migrateAll → runAll → migrate → run；无方法时走手动 pipeline(migrateDomainRules+migrateResourceSites+migrateOfficialPlatforms 等)',
        3 => '修复 official_refresh default_site=量子 纠正为抖剧TV：新增 ensureDoujuDefault() 自动写入/更新抖剧资源站/官替平台，default_site/search_sites 首位强制抖剧TV',
        4 => '官替阈值纠正：若 match_threshold 过高(>75) 自动降到推荐 65，enabled=true 默认开启',
        5 => '官替抽检 searchAllSites 不存在时 3 级回退：DbResourceSiteManager::searchAllSites → OfficialReplaceManager::searchInSites → 直接遍历前3个资源站searchVideos 逐个试',
        6 => '官替抽检新增 spot_sample：返回前3个命中视频样例(name/site/remarks)，便于排查空结果',
        7 => '官替平台 count 8个：含抖剧TV平台（360kan.com/抖剧TV 根源来源 priority=1）',
      ),
    ),
    'v5.10.5' => 
    array (
      'date' => '2026-08-09',
      'title' => '【新增 gx.php 全局定时/自动更新调度中心】',
      'changes' => 
      array (
        0 => '新增 gx.php v1.0：全局调度中心（CLI+Web 双模式，一键全部 or 单项）',
        1 => 'CLI 子命令：all / status / check / migrate / ai_learn / ai_cleanup / official_refresh / site_check / rule_check / reset_key',
        2 => 'Web 模式：自动生成随机32位密钥 gx/.gx_secret.php，访问 ?key=密钥&action=动作',
        3 => '互斥锁机制 (flock+pid)：防止 crontab 重叠运行 DB 死锁',
        4 => '分模块异常隔离：一个模块挂不影响其它模块',
        5 => '结构化日志 gx/gx_run.log + 最后一次执行摘要 gx/.gx_last_run.php',
        6 => 'task_check 健康检查：本地版本、核心文件存在性、语法 spot-check、目录可写、PHP版本',
        7 => 'task_migrate：DataMigration 自动升级 DB schema',
        8 => 'task_ai_learn：调用 AiAutoLearner::run()（force 忽略间隔，立即学）',
        9 => 'task_ai_cleanup：调用 AiAutoLearner::cleanupStaleRules() 清理失效规则域名',
        10 => 'task_official_refresh：官替配置重载 + 缓存清理 + 搜索抽检（随机热门词）',
        11 => 'task_site_check：资源站随机API健康巡检（返回OK/FAIL/cost_ms/videos数）',
        12 => 'task_rule_check：随机抽样 gz/rules_*.php 域名做 HTTPS HEAD 健康检查',
        13 => '兼容修复：DbResourceSiteManager curl_close 在 PHP 8.0+ 已废弃改为 null',
      ),
    ),
    'v5.10.4' => 
    array (
      'date' => '2026-08-09',
      'title' => '【官替识别大优化 v2】全链路修复识别不准、漏配、搜索无结果问题',
      'changes' => 
      array (
        0 => 'resolve() 短链重定向解析：b23.tv/微信跳转/快手短链自动走 resolveRedirectChain 最多5跳重定向',
        1 => 'resolve() URL 规范化：去锚点、无前缀补 https、//scheme 自动补全',
        2 => '新增 detectPlatformFuzzy() 模糊平台识别：移动站子域名(m.v.qq/m.iqiyi/m.mgtv/m.bilibili/360kan/film.qq) 全部命中',
        3 => '360kan.com 域名自动识别为抖剧TV 官替平台（根源来源）',
        4 => 'extractVideoId() 大扩展：芒果 /b/xxxx、爱奇艺 aid/albumId、youku show_id、搜狐 vod/shtml id、PPTV programId、抖剧TV vod/detail/id',
        5 => '移动站URL 2次尝试 extract：extract 失败时自动 guessMobileUrl 换成 m.*.com URL 重试',
        6 => 'httpGet() 升级：自动伪造 Referer/Origin（按域名推导）、br brotli 解压、sec-ch-ua 伪造、403/500/429 重试、3xx带body放行',
        7 => 'httpGetMobile() 2次重试 + 最终回退增强版httpGet',
        8 => 'cleanTitle() 强解码：&#中文实体/html_entity_decode/\\uXXXX(\\u5e86这类) Unicode 转义 精准解码',
        9 => 'cleanTitle() 强清洗：平台名后缀25+种/画质/分辨率/预告/花絮/MV/纪录片/直播/演唱会/广告 等过滤',
        10 => 'DbResourceSiteManager searchVideos() 5策略搜索：ac=list→videolist→detail→list+t=1,2,3,4→list+h=24（苹果CMS10最通用 ac=list 排第一）',
        11 => '关键词变体搜索（含空格/下划线/去空格/去集季）+ JSONP callback 包装自动去除',
        12 => 'findBestMatch() 阈值动态三级：主阈值60 / fallbackThreshold(60-18=42) / hardFloorThreshold(35) 避免漏配',
        13 => 'findBestMatch() 新增年份+演员交叉验证（命中演员+6/人，最多18分；年份相同+12，±1年+4，差太远-8）',
        14 => 'findBestMatch() 非正片排除正则 50+种：从"解说/预告/片花/速看/混剪/Reaction/MV/花絮/直播回放/饭制/二创/广告/纪录片"到具体"定档预告/超长花絮/生日直播/红毯盛典/发布会/番外/彩蛋"',
        15 => '基础匹配多关键词变体取最高分（base_title/title/parsed.base_title/pure_title 逐个试）',
        16 => '验证脚本 22/22 100% 通过：13平台识别 + 4抖剧TV搜索 + 3官替配置 + 4cleanTitle',
      ),
    ),
    'v5.10.3' => 
    array (
      'date' => '2026-08-10',
      'title' => '【官替升级】新增抖剧TV 默认官替资源站 + 官替优先度 1-2000+',
      'changes' => 
      array (
        0 => '新增官替资源站：抖剧TV（采集接口 https://www.douju.tv/api.php/provide/vod/，根源来源 www.360kan.com）',
        1 => '设置抖剧TV 为默认官替资源站（default_site=抖剧TV，官替 search_sites 数组第1位=抖剧TV）',
        2 => '抖剧TV 资源站 priority=1（数字越小越优先，1-2000+ 支持任意数字）',
        3 => '抖剧TV 官替平台 priority=1，与腾讯/爱奇艺/优酷/芒果/B站 同为最优先梯队，但在 search_sites 固定第一',
        4 => '后台官替管理页面：新增默认官替站显示、priority 升序表格 + 彩色徽章（⭐默认官替 / 高优先 / 普通 / 低优先）',
        5 => '官替优先度编辑器：min=1 max=2000 step=1，附"数字越小越优先，1=最高"说明',
        6 => '后台官替 API 配置：新增 default_site 输入框 + priority 规则说明渐变色提示条',
        7 => 'DbOfficialReplaceManager::searchInSites() 改为优先使用 DbResourceSiteManager（含 priority ASC 排序 + 最新资源站）',
        8 => 'DbOfficialReplaceManager::getDefaultConfig() 默认 default_site=抖剧TV，search_sites 头位抖剧TV，match_threshold=70',
        9 => '官替匹配平台查询：getAllPlatforms / getPlatformByDomain 全部按 priority ASC 排序（1=最先匹配）',
        10 => '资源站列表：抖剧TV 归类 group_name=官替资源站，root_source=www.360kan.com，official_replace=1',
      ),
    ),
    'v5.10.2' => 
    array (
      'date' => '2026-08-09',
      'title' => '后台菜单完善 + 官替平台 editor 修复',
      'changes' => 
      array (
        0 => '后台 mxadmin.php 新增 AI自动学习 / CCTV直播源 菜单分组和页面',
        1 => '官替平台 editor 支持 priority 输入',
        2 => '修复 header Warning、反射私有属性访问、DataMigration rules 列白名单等问题',
      ),
    ),
    'v5.10.1' => 
    array (
      'date' => '2026-08-08',
      'title' => '新增 CCTV 直播源扩展模块',
      'changes' => 
      array (
        0 => '新增 cctv/CctvSourceManager.php：GitHub 官方源抓取解析 + 定时自动更新',
        1 => '新增 cctv/CctvSourceVerifier.php：源可用性验证',
        2 => '新增 cctv/CctvPlaylistGenerator.php：M3U8 生成 + 播放页面集成',
      ),
    ),
    'v5.10.0' => 
    array (
      'date' => '2026-08-05',
      'title' => '新增 AI 自动学习模块（苹果CMS10接口分析）',
      'changes' => 
      array (
        0 => '新增 ai_learn/MacCMS10Analyzer.php：苹果CMS10 采集接口分析 + 去除非正片内容',
        1 => '新增 ai_learn/AutoLearnEngine.php：从资源站随机抓取视频 → 广告分析 → 学习规则',
        2 => '新增 ai_learn/TaskScheduler.php：定时任务调度，确保无广告且正片',
        3 => '新增 ai_learn/AiEndpointRouter.php：AI 接口地址配置化自动切换',
      ),
    ),
    'v5.9.7' => 
    array (
      'date' => '2026-08-03',
      'title' => 'M3U8 广告过滤系统基础版本',
      'changes' => 
      array (
        0 => '核心 M3U8AdSkipper + AdFilter：M3U8 广告分析与过滤',
        1 => 'DataMigration 数据库迁移 + 官替/资源站/规则 SQLite 表',
        2 => '后台资源站管理 + 规则管理 + 数据库管理',
      ),
    ),
  ),
);
