<?php
/**
 * parse 模块 —— 集中配置
 *
 * 所有新解析链路的超时预算、开关、平台清单、占位策略等统一在此维护，
 * 避免散落在各入口文件（mx.php / xt/server.php）里导致文件过大、难以维护。
 *
 * 用法：
 *      $parseCfg = require __DIR__ . '/parse/config.php';
 *
 * @package parse
 * @since   5.13.9
 */

return [

    // ===== 全局总超时预算（治「解析卡死」） =====
    // 全链路硬性预算，任何通道超过该秒数立即“快速失败 + 明确 message”。
    'global_budget'       => 25.0,   // 主预算（秒）
    'hard_deadline'       => 80.0,   // 终极硬上限（秒），作为 max_execution_time 兜底
    'budget_soft_ratio'   => 0.8,    // 达预算 80% 即触发“软中断提示”，预留压缩/收尾时间

    // ===== URL 类型判定 =====
    'official_domains'    => [
        'v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com',
        'bilibili.com', 'sohu.com', 'pptv.com',
    ],
    'm3u8_suffix'         => ['.m3u8', '.m3u'],
    'video_suffix'        => ['.mp4', '.mkv', '.ts', '.flv', '.mov', '.webm'],

    // ===== 本地去广告 =====
    'cleaner'             => [
        // 广告 URL 关键词黑名单（命中即视为广告/非正片段）
        'url_blacklist'   => [
            'ad/', 'ads/', '/ad/vert', 'advert', 'guanggao', 'gg_', 'gg.',
            'pre-roll', 'pre_roll', 'mid-roll', 'mid_roll', 'post-roll',
            'postroll', 'preroll', 'bumper', 'promo', 'trailer', 'sponsor',
            '贴片', '片头', '插播', '推广', '广告',
        ],
        // 单段最短/最长安全时长（秒）
        'ad_duration_max' => 3.0,   // ≤3s 的短视频片段高度疑似贴片/缓冲广告
        'main_duration_max' => 30.0, // 正片单段通常 ≤30s，过长需警惕
        // 占位策略：local_proxy=服务器生成静音 ts / data_uri=行内 data 地址
        'placeholder'     => [
            'mode'        => 'local_proxy',
            'local_base'  => '',        // 例：http://host/mx.php?action=placeholder_ts&d=
            'data_uri_ts' => '',        // 备选：可直接内联的极短静音黑屏 ts（base64）
        ],
        // 是否调用重型引擎（M3U8Parser / EnhancedAdRuleEngine / Md5AdPlaceholderEngine）
        'use_heavy_engine'=> false,    // 框架默认内置轻量自检；接入重型引擎后置 true
    ],

    // ===== 官方替换（资源站优先） =====
    'official_replace'    => [
        'enabled'         => true,
        'attempt_budget'  => 22.0,     // 官替内部预算（应 < global_budget）
        'max_sites'       => 8,        // 每站搜索上限（控制并发/总耗时）
        'site_query_ms'   => 4000,     // 单个资源站查询超时（毫秒）
    ],

    // ===== 输出 =====
    'output'              => [
        'include_step_trace' => true,  // 返回 step_trace 便于诊断
        'include_debug'    => true,
    ],
];