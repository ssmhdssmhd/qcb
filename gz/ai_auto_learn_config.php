<?php
// AI 自动学习配置 - 由后台自动维护，无需手动编辑
// 此功能针对频繁更新规则的资源站，每隔几小时自动从指定资源站获取热门/更新视频，
// 提取 rym3u8 等指定 play_from 的地址进行深度广告分析并更新规则
return [
    'enabled' => false,
    // 执行间隔（小时），默认 4 小时
    'interval_hours' => 4,
    // 目标资源站名称列表（对应 sites_config.php 中的 name 字段）
    'target_sites' => ['如意'],
    // 仅学习包含指定 play_from 标识的视频（为空则不限制）
    'play_from_patterns' => ['rym3u8'],
    // 每次运行每个站点学习多少个视频
    'videos_per_site' => 5,
    // 每次运行最大站点数
    'max_sites_per_run' => 3,
    // 最小片段数（低于此数视为无效视频）
    'min_segments' => 50,
    // 最大广告占比（%），超过则视为异常跳过
    'max_ad_percentage' => 90,
    // 单个视频最大执行时间（秒）
    'max_exec_time_per_video' => 30,
    // 优先学习最近更新/热门的视频（按 vod_remarks/vod_time 排序）
    'prefer_hot_videos' => true,
    // 已学习视频去重记录保留天数
    'dedup_retention_days' => 7,
    // 仅对频繁更新规则的域名启用（learn_count 达到此值才纳入）
    'min_learn_count_to_track' => 1,
    // 访问密钥（为空则不校验，建议设置）
    'access_key' => '',
    // 上次执行时间（自动维护）
    'last_run_time' => NULL,
];
