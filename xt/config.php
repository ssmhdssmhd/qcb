<?php
/**
 * 超级嗅探 - 全局配置文件
 *
 * 管理官解接口、AI 大模型 API、缓存等配置
 */

return [

    // ============ 版本信息（单一来源：version.php 同步写入） ============
    'version' => '5.10.1',
    'version_build' => '20260808-2',
    'version_full' => 'v5.10.1 build 20260808-2',

    // ============ 嗅探设置（后台「嗅探设置」页面维护） ============
    // sniffer_config.php 由后台写入，此处作为兜底默认值
    // 合并优先级：sniffer_config.php > 此处默认值
    'sniffer' => [
        // 当前解析通道：official=官解解析 / replace=官替接口
        'mode' => 'official',
        // 官解接口（支持多个，按优先级排列；后台可动态增删）
        // 注意：single_api 模式下也可只配置一条
        'official_apis' => [
            [
                'enabled'    => true,
                'name'       => '虾米官解',
                'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
                'type'       => 'json',
                'url_field'  => 'play_url',
                'headers'    => [],
            ],
            // 可在后台添加更多官解接口...
        ],
        // 单接口兼容字段（保留，后台旧配置可能只有这一条）
        'official_api' => [
            'enabled'    => true,
            'name'       => '虾米官解',
            'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
            'type'       => 'json',
            'url_field'  => 'play_url',
            'headers'    => [],
        ],
        // 官替接口（开关 + 接口参数）
        'replace_api' => [
            'enabled'    => false,
            'name'       => '本地官替',
            'url'        => '',
            'type'       => 'json',
            'url_field'  => 'm3u8_url',
            'headers'    => [],
        ],
    ],

    // ============ 性能优化配置（多接口并发 + AI 学习） ============
    'performance' => [
        // 是否启用竞速模式（多个接口并发请求，最快成功的立即返回）
        // 建议开启：可显著降低首屏等待时间
        'race_mode'         => true,
        // 最大并发请求数（建议 2-5，过多会增加服务器负担）
        'max_concurrent'    => 3,
        // 总超时时间（秒）
        'timeout'           => 15.0,
        // 是否启用 AI 学习自动排序
        // 开启后：根据每个接口的成功率、平均耗时自动调整调用优先级
        'ai_sort_enabled'   => true,
        // v5.7.5 新增：是否同时调用官解和官替（curl_multi 并发，最快成功的立即返回）
        // 开启后：忽略 sniffer.mode 通道选择，把所有已启用的官解接口 + 官替接口
        //         合并到同一个并发池，谁先返回有效结果就用谁
        // 关闭后：按 sniffer.mode 选择的通道优先，失败再 fallback 到另一通道（旧逻辑）
        'concurrent_race_enabled' => true,
        // AI 评分权重配置
        'ai_score_weights' => [
            'success_rate'     => 0.5, // 成功率权重
            'avg_duration'     => 0.4, // 平均耗时权重
            'consec_fail'      => 0.1, // 连续失败惩罚权重
        ],
    ],

    // ============ 官解接口配置（兼容旧逻辑，作为 fallback） ============
    // 官方解析 API，传入视频 URL 返回 m3u8/mp4 直链
    // 支持多个接口，按优先级依次尝试
    // 注意：新版本优先读取 sniffer.official_api，此数组仅在嗅探设置未启用官解时作为兜底
    'official_apis' => [
        [
            'name'       => '虾米官解',
            'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
            'type'       => 'json',
            'url_field'  => 'play_url',  // JSON 中视频地址的字段名
            'headers'    => [],
        ],
        // 可添加更多官解接口...
        // [
        //     'name'      => '官解接口-2',
        //     'url'       => 'https://api2.example.com/jiexi?url=',
        //     'type'      => 'json',
        //     'url_field' => 'url',
        //     'headers'   => [],
        // ],
    ],

    // ============ AI 大模型配置（辅助广告识别 + 接口自动切换） ============
    'ai' => [
        // 是否启用 AI 辅助识别（规则引擎无法判断时调用）
        'enabled'    => false,
        // 触发 AI 的条件：规则引擎识别置信度低于此值时调用 AI（0-1）
        'confidence_threshold' => 0.6,

        // v5.10 新增：多端点自动切换 / 故障转移（AiEndpointRouter 读取）
        // 说明：按 priority 从小到大排序，健康检查失败自动 fallback 到下一个
        'endpoints' => [
            [
                'name'       => '通义千问-官方',
                'enabled'    => true,
                'provider'   => 'qwen',
                'type'       => 'openai_compatible',
                'model'      => 'qwen-plus',
                'api_url'    => 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions',
                'health_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1/models',
                'api_key'    => 'YOUR_AI_API_KEY',
                'max_tokens' => 2000,
                'priority'   => 1,
            ],
            [
                'name'       => 'DeepSeek-V3',
                'enabled'    => true,
                'provider'   => 'deepseek',
                'type'       => 'openai_compatible',
                'model'      => 'deepseek-chat',
                'api_url'    => 'https://api.deepseek.com/v1/chat/completions',
                'health_url' => 'https://api.deepseek.com/v1/models',
                'api_key'    => 'YOUR_DEEPSEEK_API_KEY',
                'max_tokens' => 2000,
                'priority'   => 2,
            ],
            [
                'name'       => '硅基流动-备用',
                'enabled'    => true,
                'provider'   => 'siliconflow',
                'type'       => 'openai_compatible',
                'model'      => 'Qwen/Qwen2.5-7B-Instruct',
                'api_url'    => 'https://api.siliconflow.cn/v1/chat/completions',
                'health_url' => 'https://api.siliconflow.cn/v1/models',
                'api_key'    => 'YOUR_SILICONFLOW_API_KEY',
                'max_tokens' => 2000,
                'priority'   => 3,
            ],
        ],

        // 兼容旧版单接口配置（读取时 fallback）
        'provider'   => 'qwen',
        'api_key'    => 'YOUR_AI_API_KEY',
        'model'      => 'qwen-plus',
        'api_url'    => 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions',
        'max_tokens' => 2000,
    ],

    // ============ v5.10 新增：AI 自动学习引擎配置 ============
    'auto_learn' => [
        // 自动学习总开关
        'enabled'               => true,
        // 学习运行间隔（秒），默认 4 小时（14400s）。也可由 TaskScheduler + cron 触发。
        'interval_seconds'      => 14400,
        // 每次学习最多处理多少个资源站
        'max_sites_per_run'     => 5,
        // 每个资源站最多抽取多少部剧集（视频）
        'videos_per_site'       => 5,
        // 最少要有多少个 segment 才值得学习（避免过短的预告片被当成样本）
        'min_segments'          => 50,
        // 超过此广告率视为极端情况，不学习（防误判正片为广告）
        'max_ad_percentage'     => 90,
        // 触发学习的最低广告率阈值（低于此说明没广告，不值得学习）
        'min_ad_percentage'     => 10,
        // 保底机制触发时：正片片段保留率低于此值则放弃学习（避免高风险样本污染规则）
        'safeguard_min_keep_ratio' => 0.6,
        // 访问秘钥（用于通过 HTTP 触发执行：scheduler.php?action=run&task=auto_learn&secret=xxx）
        'trigger_secret'        => 'm3u8_learn_secret_2026',
        // 是否在 mxadmin 后台显示学习记录日志
        'show_in_admin'         => true,
    ],

    // ============ v5.10 新增：CCTV 直播源扩展模块配置 ============
    'cctv_live' => [
        // CCTV 模块开关
        'enabled'           => true,
        // 抓取源列表（按顺序尝试，成功即止；支持随时在后台增删）
        'fetch_sources' => [
            [
                'name'     => 'ipv6-cn/iptv (GitHub官方主源)',
                'url'      => 'https://raw.githubusercontent.com/ipv6-cn/iptv/main/cctv.m3u',
                'format'   => 'm3u',
                'enabled'  => true,
                'priority' => 1,
            ],
            [
                'name'     => 'SuMaiKaDe/iptv (备用TXT源)',
                'url'      => 'https://raw.githubusercontent.com/SuMaiKaDe/iptv/main/cctv.txt',
                'format'   => 'txt',
                'enabled'  => true,
                'priority' => 2,
            ],
            [
                'name'     => 'yanG-1101/Auto_iptv (备用M3U源)',
                'url'      => 'https://raw.githubusercontent.com/yanG-1101/Auto_iptv/main/m3u/cctv.m3u',
                'format'   => 'm3u',
                'enabled'  => true,
                'priority' => 3,
            ],
        ],
        // 自动更新周期（秒），默认每 6 小时（21600s）
        'update_interval'   => 21600,
        // 缓存 TTL（秒），超过此时间即便源文件没更新也从网络重新拉
        'cache_ttl'         => 21600,
        // 频道过滤：true 只保留 CCTV + 省级卫视；false 保留全部频道
        'filter_cctv_only'  => true,
        // 是否在输出播放列表前执行可用性验证（建议开启，去掉死源）
        'verify_before_save' => true,
        // 验证并发数（越高越快，对服务器网络压力大）
        'verify_concurrent' => 8,
        // 单频道保留最快的 N 个可用源作为 fallback 链
        'best_per_channel'  => 3,
        // 对外输出接口路径（mx.php 路由挂载点）：mx.php?action=cctv/xxx
        'route_prefix'      => 'cctv',
        // 在 mxadmin 后台是否显示「直播源管理」菜单
        'show_in_admin'     => true,
        // HTTP 触发更新秘钥（scheduler.php?action=run&task=cctv_update&secret=xxx）
        'trigger_secret'    => 'm3u8_cctv_secret_2026',
    ],

    // ============ 广告识别规则配置 ============
    'ad_rules' => [
        // 是否启用 URL 关键词匹配
        'url_keyword_enabled'   => true,
        // 广告 URL 关键词（不区分大小写）
        'url_keywords'          => ['ad', 'adv', 'advert', 'promo', 'promotion', 'gg', 'commercial', 'spotad'],
        // 是否启用 #EXT-X-DISCONTINUITY 标记检测
        'discontinuity_enabled' => true,
        // 是否启用不同域名检测（广告 ts 常来自不同 CDN 域名）
        'domain_check_enabled'  => true,
        // 是否启用时长异常检测（广告分段通常 15s/30s 整数倍）
        'duration_check_enabled'=> true,
        // 广告分段常见时长（秒）
        'ad_durations'          => [15, 30, 45, 60],
        // 时长匹配容差（秒）
        'duration_tolerance'    => 1.0,
        // 是否启用插播检测（片头/片尾超长片段）
        'insertion_check_enabled'=> true,
        // 是否启用水印/角标检测
        'watermark_check_enabled'=> true,
        // 水印/角标 URL 关键词（不区分大小写）
        'watermark_keywords'    => ['watermark', 'logo', 'burn', 'overlay'],
    ],

    // ============ 缓存配置 ============
    'cache' => [
        // 是否启用缓存
        'enabled'       => true,
        // 缓存目录
        'dir'           => __DIR__ . '/cache',
        // 缓存过期时间（秒），默认 2 小时
        'ttl'           => 7200,
        // 自动清理过期缓存概率（1-100，数字越大越频繁）
        'auto_clean_prob' => 5,
        // 最多缓存文件数（超过则自动清理最旧的）
        'max_files'     => 500,
    ],

    // ============ 网络请求配置 ============
    'http' => [
        'timeout'        => 20,
        'connect_timeout'=> 10,
        'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        // 是否验证 SSL 证书
        'ssl_verify'     => false,
    ],

    // ============ 开发者信息 ============
    'developer' => [
        'name'  => '超级嗅探',
        'author'=> 'XT',
        'qq'    => '10000',
        'site'  => '',
    ],

    // ============ 调试模式 ============
    'debug' => false,
];
