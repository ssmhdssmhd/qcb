<?php
/**
 * 超级嗅探 - 全局配置文件
 *
 * 管理官解接口、AI 大模型 API、缓存等配置
 */

return [

    // ============ 官解接口配置 ============
    // 官方解析 API，传入视频 URL 返回 m3u8/mp4 直链
    // 支持多个接口，按优先级依次尝试
    'official_apis' => [
        [
            'name'    => '官解接口-1',
            'url'     => 'https://your-official-api.com/parse?url=',
            'type'    => 'redirect', // redirect: 直接跳转到m3u8 | json: 返回JSON含url字段 | text: 纯文本返回直链
            'headers' => [
                'Authorization' => 'Bearer YOUR_TOKEN_HERE',
            ],
        ],
        // 可添加更多官解接口...
        // [
        //     'name'    => '官解接口-2',
        //     'url'     => 'https://api2.example.com/jiexi?url=',
        //     'type'    => 'json',
        //     'headers' => [],
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
    ],

    // ============ 缓存配置 ============
    'cache' => [
        // 是否启用缓存
        'enabled'     => true,
        // 缓存目录
        'dir'         => __DIR__ . '/cache',
        // 缓存过期时间（秒），默认 2 小时
        'ttl'         => 7200,
        // 自动清理过期缓存
        'auto_clean'  => true,
    ],

    // ============ 网络请求配置 ============
    'http' => [
        'timeout'        => 20,
        'connect_timeout'=> 10,
        'user_agent'     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        // 是否验证 SSL 证书
        'ssl_verify'     => false,
    ],

    // ============ 调试模式 ============
    'debug' => false,
];
