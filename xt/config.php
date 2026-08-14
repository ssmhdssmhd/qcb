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
            // v5.13.2-C3: 第三方虾米官解(114.134.184.91:9002)已于 2026-08-14 改为签名/白名单校验，
            // 任何请求都返回{"success":false,"message":"验证失败!"}，默认启用会让 fallback 一直等。
            // 需要官解的用户可在后台「嗅探设置」里自己替换为可用的官解接口地址（或保持 false 走官替本地直调）。
            [
                'enabled'    => false,
                'name'       => '虾米官解(已失效，2026-08-14起需签名验证：请替换为可用的官解接口或直接使用官替本地直调)',
                'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
                'type'       => 'json',
                'url_field'  => 'play_url',
                'headers'    => [],
            ],
            // 可在后台添加更多官解接口...
        ],
        // 单接口兼容字段（保留，后台旧配置可能只有这一条）
        'official_api' => [
            // v5.13.2-C3: 见上方 official_apis 注释，第三方服务器 2026-08-14 起需要签名验证，默认 false。
            'enabled'    => false,
            'name'       => '虾米官解(已失效，见注释)',
            'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
            'type'       => 'json',
            'url_field'  => 'play_url',
            'headers'    => [],
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
        // v5.13.2-C3：兜底官解数组同样标记第三方服务器已失效。
        // 若后台嗅探配置(sniffer_config.php)里官解接口全部未启用，会走到这里的兜底数组。
        // 因为服务器已改成签名验证，此数组也留空，避免再尝试必败的请求。
        // [
        //     'name'       => '替换为你自己可用的官解接口',
        //     'url'        => 'https://你的官解服务器/jx?url=',
        //     'type'       => 'json',
        //     'url_field'  => 'play_url',
        //     'headers'    => [],
        // ],
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
