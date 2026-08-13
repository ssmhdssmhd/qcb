<?php
// AI 自动学习配置 - 由后台自动维护，无需手动编辑
// 此功能针对频繁更新规则的资源站，每隔几小时自动从指定资源站获取热门/更新视频，
// 提取 rym3u8 等指定 play_from 的地址进行深度广告分析并更新规则
return array (
  'enabled' => true,
  'interval_hours' => 4,
  'target_mode' => 'all',
  'target_sites' => 
  array (
    0 => '如意',
    1 => '量子',
  ),
  'play_from_patterns' => 
  array (
    0 => 'rym3u8',
  ),
  'videos_per_site' => 50,
  'max_sites_per_run' => 3,
  'min_segments' => 50,
  'max_ad_percentage' => 90,
  'max_exec_time_per_video' => 30,
  'prefer_hot_videos' => true,
  'dedup_retention_days' => 7,
  'min_learn_count_to_track' => 1,
  'access_key' => '',
  'last_run_time' => '2026-08-13 22:03:53',
  'auto_trigger_on_request' => true,
  'auto_cleanup_stale_rules' => true,
  'stale_rule_days' => 30,
  'cleanup_health_timeout' => 6,
  'cleanup_interval_hours' => 24,
  'last_cleanup_time' => '2026-08-09 23:04:04',
);
