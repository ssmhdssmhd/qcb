<?php
// gx.php 最后一次执行记录
return array (
  'success' => true,
  'cost_seconds' => 0.0,
  'tasks_count' => 1,
  'failed_tasks' => 
  array (
  ),
  'task_results' => 
  array (
    'status' => 
    array (
      'success' => true,
      'last_run' => 
      array (
        'success' => true,
        'cost_seconds' => 0.0,
        'tasks_count' => 1,
        'failed_tasks' => 
        array (
        ),
        'task_results' => 
        array (
          'status' => 
          array (
            'success' => true,
            'last_run' => 
            array (
              'success' => true,
              'cost_seconds' => 0.01,
              'tasks_count' => 1,
              'failed_tasks' => 
              array (
              ),
              'task_results' => 
              array (
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
                        'time' => '2026-08-09 23:05:39',
                      ),
                      1 => 
                      array (
                        'category' => 'domain_rules',
                        'message' => '迁移文件 rules_svip.ryiplay18.com.php 失败: SQLSTATE[HY000]: General error: 1 table domain_rules has no column named rules',
                        'time' => '2026-08-09 23:05:39',
                      ),
                    ),
                  ),
                  'already_migrated' => true,
                  '_cost_s' => 0.007,
                ),
              ),
              'action' => 'migrate',
              'started_at' => '2026-08-09 23:05:39',
              'finished_at' => '2026-08-09 23:05:39',
              'saved_at' => '2026-08-09 23:05:39',
            ),
            'log_size_bytes' => 8170,
            'lock_file_exists' => true,
            'lock_pid' => 2741,
            'gx_key_tip' => '浏览器访问：/gx.php?key=c9724a...&action=all  （完整key详见 gx/.gx_secret.php）',
            'cli_cron_example' => '0 */6 * * * php /workspace/gx.php all >> /workspace/gx/gx_run.log 2>&1',
            'help' => 
            array (
              'all (默认)' => '依次执行 check → migrate → ai_learn → official_refresh → site_check',
              'check' => '版本/核心文件/语法/权限 健康检查',
              'migrate' => '数据库 schema 迁移升级',
              'ai_learn [force]' => 'AI 自动学习（force 跳过间隔，立即执行）',
              'ai_cleanup [force]' => 'AI 清理失效域名规则',
              'official_refresh [--max=8]' => '官替配置刷新 + 匹配抽检',
              'site_check [--max=10]' => '资源站 API 健康巡检',
              'rule_check [--max=20]' => '抽样域名规则健康检查',
              'status' => '查看 gx 运行状态 / 上次执行摘要 / cron 示例',
              'reset_key' => '重置 Web 访问密钥',
            ),
            '_cost_s' => 0.001,
          ),
        ),
        'action' => 'status',
        'started_at' => '2026-08-09 23:16:39',
        'finished_at' => '2026-08-09 23:16:39',
        'saved_at' => '2026-08-09 23:16:39',
      ),
      'log_size_bytes' => 9280,
      'lock_file_exists' => true,
      'lock_pid' => 2754,
      'gx_key_tip' => '浏览器访问：/gx.php?key=c9724a...&action=all  （完整key详见 gx/.gx_secret.php）',
      'cli_cron_example' => '0 */6 * * * php /workspace/gx.php all >> /workspace/gx/gx_run.log 2>&1',
      'help' => 
      array (
        'all (默认)' => '依次执行 check → migrate → ai_learn → official_refresh → site_check',
        'check' => '版本/核心文件/语法/权限 健康检查',
        'migrate' => '数据库 schema 迁移升级',
        'ai_learn [force]' => 'AI 自动学习（force 跳过间隔，立即执行）',
        'ai_cleanup [force]' => 'AI 清理失效域名规则',
        'official_refresh [--max=8]' => '官替配置刷新 + 匹配抽检',
        'site_check [--max=10]' => '资源站 API 健康巡检',
        'rule_check [--max=20]' => '抽样域名规则健康检查',
        'status' => '查看 gx 运行状态 / 上次执行摘要 / cron 示例',
        'reset_key' => '重置 Web 访问密钥',
      ),
      '_cost_s' => 0.001,
    ),
  ),
  'action' => 'status',
  'started_at' => '2026-08-09 23:19:16',
  'finished_at' => '2026-08-09 23:19:16',
  'saved_at' => '2026-08-09 23:19:16',
);
