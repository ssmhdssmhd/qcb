<?php
// gx.php 最后一次执行记录
return array (
  'success' => true,
  'cost_seconds' => 22.26,
  'tasks_count' => 5,
  'failed_tasks' => 
  array (
  ),
  'task_results' => 
  array (
    'check' => 
    array (
      'success' => true,
      'checks' => 
      array (
        'core_files' => 
        array (
          'ok' => true,
        ),
        'syntax' => 
        array (
          'ok' => true,
          'method' => 'php -l via exec()',
        ),
        'memory_limit' => '512M',
        'sapi' => 'cli',
        'writable_dirs' => 
        array (
          'gx' => true,
          'db' => true,
          'gz' => true,
        ),
      ),
      'local_version' => 'v5.10.8',
      'local_build' => '20260810',
      'php_version' => '8.5.6-dev',
      '_cost_s' => 0.074,
    ),
    'migrate' => 
    array (
      'success' => true,
      'method_used' => 'migrateAll',
      'result' => 
      array (
        'success' => false,
        'summary' => 
        array (
          'domain_rules' => 
          array (
            'migrated' => 0,
            'skipped' => 14,
          ),
          'resource_sites' => 
          array (
            'migrated' => 0,
            'skipped' => 122,
          ),
          'proxies' => 
          array (
            'migrated' => 0,
            'skipped' => 0,
          ),
          'official_sites' => 
          array (
            'migrated' => 0,
            'skipped' => 1,
          ),
          'official_platforms' => 
          array (
            'migrated' => 0,
            'skipped' => 7,
          ),
          'auto_learn_config' => 
          array (
            'migrated' => 0,
            'skipped' => 4,
          ),
        ),
        'errors' => 
        array (
          0 => 
          array (
            'category' => 'domain_rules',
            'message' => '迁移文件 rules_cdn7.ryplay7.com.php 失败: SQLSTATE[HY000]: General error: 1 table domain_rules has no column named rules',
            'time' => '2026-08-09 23:45:26',
          ),
          1 => 
          array (
            'category' => 'domain_rules',
            'message' => '迁移文件 rules_svip.ryiplay18.com.php 失败: SQLSTATE[HY000]: General error: 1 table domain_rules has no column named rules',
            'time' => '2026-08-09 23:45:26',
          ),
        ),
      ),
      'already_migrated' => true,
      '_cost_s' => 0.081,
    ),
    'official_refresh' => 
    array (
      'success' => true,
      'enabled' => true,
      'default_site' => '抖剧TV',
      'match_threshold' => 70,
      'platforms_count' => 8,
      'platforms' => 
      array (
        0 => 
        array (
          'name' => '腾讯视频',
          'domain' => 'v.qq.com',
          'priority' => 1,
        ),
        1 => 
        array (
          'name' => '爱奇艺',
          'domain' => 'iqiyi.com',
          'priority' => 1,
        ),
        2 => 
        array (
          'name' => '优酷',
          'domain' => 'youku.com',
          'priority' => 1,
        ),
        3 => 
        array (
          'name' => '芒果TV',
          'domain' => 'mgtv.com',
          'priority' => 1,
        ),
        4 => 
        array (
          'name' => '哔哩哔哩',
          'domain' => 'bilibili.com',
          'priority' => 1,
        ),
        5 => 
        array (
          'name' => '抖剧TV',
          'domain' => 'douju.tv',
          'priority' => 1,
        ),
        6 => 
        array (
          'name' => '搜狐视频',
          'domain' => 'sohu.com',
          'priority' => 2,
        ),
        7 => 
        array (
          'name' => 'PP视频',
          'domain' => 'pptv.com',
          'priority' => 2,
        ),
      ),
      'bootstrap' => 
      array (
        'douju_site_created' => false,
        'douju_platform_created' => false,
        'douju_set_as_default' => true,
        'default_site_corrected_from' => NULL,
        'douju_site_updated' => true,
      ),
      'cache_delete_error' => 'SQLSTATE[HY000]: General error: 1 no such function: NOW',
      'spot_fallback_tried' => 3,
      'spot_check_keyword' => '庆余年',
      'spot_check_video_count' => 21,
      'spot_check_site_count' => 3,
      'spot_sample' => 
      array (
        0 => 
        array (
          'name' => '庆余年第二季',
          'remarks' => '已完结',
          'site' => '',
        ),
        1 => 
        array (
          'name' => '庆余年[电影解说]',
          'remarks' => '已完结',
          'site' => '',
        ),
        2 => 
        array (
          'name' => '庆余年第一季',
          'remarks' => '已完结',
          'site' => '',
        ),
      ),
      '_cost_s' => 9.771,
    ),
    'ai_learn' => 
    array (
      'success' => true,
      'message' => 'AI 自动学习完成',
      'sites_processed' => 3,
      'total_learned' => 0,
      'total_failed' => 0,
      'total_skipped' => 0,
      'learned_domains' => 
      array (
      ),
      'duration_seconds' => 4.84,
      'details' => 
      array (
        0 => 
        array (
          'site' => '量子',
          'videos_checked' => 0,
          'videos_learned' => 0,
          'videos_failed' => 0,
          'videos_skipped' => 0,
          'domains' => 
          array (
          ),
          'details' => 
          array (
          ),
        ),
        1 => 
        array (
          'site' => '暴风',
          'videos_checked' => 0,
          'videos_learned' => 0,
          'videos_failed' => 0,
          'videos_skipped' => 0,
          'domains' => 
          array (
          ),
          'details' => 
          array (
          ),
        ),
        2 => 
        array (
          'site' => '非凡',
          'videos_checked' => 0,
          'videos_learned' => 0,
          'videos_failed' => 0,
          'videos_skipped' => 0,
          'domains' => 
          array (
          ),
          'details' => 
          array (
          ),
        ),
      ),
      '_cost_s' => 14.612,
    ),
    'site_check' => 
    array (
      'success' => true,
      'total_sites' => 122,
      'checked_count' => 4,
      'ok_count' => 3,
      'fail_count' => 1,
      'checks' => 
      array (
        0 => 
        array (
          'name' => '量子',
          'domain' => 'cj.lziapi.com',
          'keyword' => '庆余年',
          'status' => 'OK',
          'cost_ms' => 1185.0,
          'videos' => 0,
          'msg' => '',
        ),
        1 => 
        array (
          'name' => '暴风',
          'domain' => 'bfzyapi.com',
          'keyword' => '三体',
          'status' => 'OK',
          'cost_ms' => 3362.0,
          'videos' => 0,
          'msg' => '',
        ),
        2 => 
        array (
          'name' => '非凡',
          'domain' => 'cj.ffzyapi.com',
          'keyword' => '九门',
          'status' => 'OK',
          'cost_ms' => 2909.0,
          'videos' => 0,
          'msg' => '',
        ),
        3 => 
        array (
          'name' => '聚合资源',
          'domain' => 'vod.korge.cn',
          'keyword' => '九门',
          'status' => 'FAIL',
          'cost_ms' => 189.0,
          'videos' => 0,
          'msg' => '根接口不通：HTTP0 OpenSSL SSL_connect: SSL_ERROR_SYSCALL in connection to vod.korge.cn:443',
        ),
      ),
      '_cost_s' => 22.263,
    ),
  ),
  'action' => 'all',
  'started_at' => '2026-08-09 23:45:26',
  'finished_at' => '2026-08-09 23:45:48',
  'saved_at' => '2026-08-09 23:45:48',
);
