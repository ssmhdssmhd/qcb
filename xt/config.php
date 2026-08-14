<?php
/**
 * 超级嗅探 - 全局配置文件
 *
 * 管理官解接口、AI 大模型 API、缓存等配置
 */

return [

    // ============ 版本信息 ============
    'version' => '5.11.0',
    'build' => '20260815-md5-placeholder',

    // ============ 嗅探设置（后台「嗅探设置」页面维护） ============
    // sniffer_config.php 由后台写入，此处作为兜底默认值
    // 合并优先级：sniffer_config.php > 此处默认值
    'sniffer' => [
        // 当前解析通道：official=官解解析 / replace=官替接口
        // v5.10.9 默认改为 replace 优先：先识别平台 → 资源站官替匹配 → AI 去广告/去插播
        //   官替(资源站)相比官解(虾米)更稳定，不受上游加密验证影响
        'mode' => 'replace',
        // 官解接口（支持多个，按优先级排列；后台可动态增删）
        // 注意：single_api 模式下也可只配置一条
        'official_apis' => [
            // v5.13.3-D4：2026-08-14 替换虾米官解到新地址 https://jx.xmflv.cc/?url=（Cloudflare CDN 节点）
            //   新接口是 HTML 播放器页面（<title>虾米播放器…</title> + <div id=Xmflv>），返回给
            //   客户端 play_url = 「https://jx.xmflv.cc/?url=URLENCODED&ref=URLENCODED(平台Referer)」
            //   整段链接，直接 302 跳转或 <iframe src=> 即可播放。无需再抽 play_url JSON。
            [
                'enabled'    => true,
                'name'       => '虾米官解(jx.xmflv.cc新地址v5.13.3 HTML播放器，{url}/{ref}占位符，302/iframe直接播放)',
                'url'        => 'https://jx.xmflv.cc/?url={url}&ref={ref}',
                'type'       => 'html_player',
                'url_field'  => 'play_url',
                'headers'    => [
                    'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'Accept-Language'           => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
                    'sec-ch-ua'                 => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                    'sec-ch-ua-mobile'          => '?0',
                    'sec-ch-ua-platform'        => '"Windows"',
                    'Upgrade-Insecure-Requests' => '1',
                ],
            ],
            // 可在后台添加更多官解接口...
        ],
        // 单接口兼容字段（保留，后台旧配置可能只有这一条）
        'official_api' => [
            // v5.13.3-D4：单接口兼容配置，同样替换为 jx.xmflv.cc 新地址 + enabled=true
            'enabled'    => true,
            'name'       => '虾米官解(单接口兼容 jx.xmflv.cc HTML播放器)',
            'url'        => 'https://jx.xmflv.cc/?url={url}&ref={ref}',
            'type'       => 'html_player',
            'url_field'  => 'play_url',
            'headers'    => [
                'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language'           => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ],
        // 官替接口（开关 + 接口参数）
        // v5.10.9 默认开启官替：资源站搜索匹配 + AI 智能识别去广告/去插播/去水印
        //   流程：识别视频平台/标题 → 多个资源站并发搜索 → AI/规则智能匹配 → 返回无广告播放地址
        'replace_api' => [
            'enabled'    => true,
            'name'       => '本地官替',
            'url'        => '',
            'type'       => 'json',
            'url_field'  => 'ad_skip_url',
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
        // v5.13.3-D4：fallback 官解数组同样替换为 jx.xmflv.cc 新 HTML 播放器接口，
        //             保证即便后台 sniffer_config 里全关了官解，old getVideoLinkFromOfficialApi()
        //             仍然能 fallback 到可用结果。
        [
            'name'       => '虾米官解(fallback:jx.xmflv.cc v5.13.3 HTML播放器，直接302跳转/iframe播放)',
            'url'        => 'https://jx.xmflv.cc/?url={url}&ref={ref}',
            'type'       => 'html_player',
            'url_field'  => 'play_url',
            'headers'    => [
                'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language'           => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ],
    ],

    // ============ AI 大模型配置（辅助广告识别） ============
    'ai' => [
        // 是否启用 AI 辅助识别（规则引擎无法判断时调用）
        'enabled'    => false,
        // API 提供商: openai / qwen / deepseek
        'provider'   => 'qwen',
        // API 密钥
        'api_key'    => 'YOUR_AI_API_KEY',
        // 模型名称
        'model'      => 'qwen-plus',
        // API 端点（按需修改）
        'api_url'    => 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions',
        // 最大 token 数
        'max_tokens' => 2000,
        // 触发 AI 的条件：规则引擎识别置信度低于此值时调用 AI（0-1）
        'confidence_threshold' => 0.6,
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
    // v5.13.3-D4：user_agent 默认升级 Chrome 126（Cloudflare/WAF 会检查 UA，老 Chrome120 / 极简 UA 容易被 403）
    'http' => [
        'timeout'        => 20,
        'connect_timeout'=> 10,
        'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
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
