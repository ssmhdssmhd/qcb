<?php
/**
 * 超级嗅探 - 全局配置文件
 *
 * 管理官解接口、AI 大模型 API、缓存等配置
 */

return [

    // ============ 版本信息 ============
    'version' => '5.7.7',

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
