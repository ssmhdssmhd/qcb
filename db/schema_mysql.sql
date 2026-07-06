-- M3U8 广告分析系统 - 数据库表结构 (MySQL 版本)
-- 字符集: utf8mb4

-- ============================================
-- 1. 系统配置表
-- ============================================
CREATE TABLE IF NOT EXISTS `sys_config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `config_key` VARCHAR(100) NOT NULL COMMENT '配置键名',
    `config_value` TEXT COMMENT '配置值(JSON格式)',
    `description` VARCHAR(255) DEFAULT '' COMMENT '配置说明',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置表';

-- ============================================
-- 2. 域名广告规则表
-- ============================================
CREATE TABLE IF NOT EXISTS `domain_rules` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `domain` VARCHAR(255) NOT NULL COMMENT '域名',
    `name` VARCHAR(255) DEFAULT '' COMMENT '资源名称',
    `note` VARCHAR(500) DEFAULT '' COMMENT '备注',
    `ad_threshold` INT DEFAULT 50 COMMENT '广告判定阈值',
    `confidence_score` INT DEFAULT 0 COMMENT '置信度分数',
    `learn_count` INT DEFAULT 0 COMMENT '学习次数',
    `analysis_date` DATETIME DEFAULT NULL COMMENT '首次分析时间',
    `last_learn_date` DATETIME DEFAULT NULL COMMENT '最后学习时间',
    `duration_rules` TEXT COMMENT '时长规则数组(JSON)',
    `discontinuity_rules` TEXT COMMENT 'DISCON规则数组(JSON)',
    `sequence_jump_rules` TEXT COMMENT '序列号跳跃规则数组(JSON)',
    `filename_patterns` TEXT COMMENT '文件名模式数组(JSON)',
    `insertion_patterns` TEXT COMMENT '插播模式数据(JSON)',
    `ad_type_stats` TEXT COMMENT '广告类型统计(JSON)',
    `psychological_profile` TEXT COMMENT '心理画像数据(JSON)',
    `marker_stats` TEXT COMMENT '标记统计数据(JSON)',
    `analysis_stats` TEXT COMMENT '分析统计概览(JSON)',
    `history_stats` MEDIUMTEXT COMMENT '历史学习数据(JSON, 大字段)',
    `marker_detection` TEXT COMMENT '标记检测配置(JSON)',
    `confidence` TEXT COMMENT '置信度配置(JSON)',
    `enable_marker_detection` TINYINT(1) DEFAULT 0 COMMENT '是否启用标记检测',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1启用 0禁用',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_domain` (`domain`),
    KEY `idx_status` (`status`),
    KEY `idx_updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='域名广告规则表';

-- ============================================
-- 3. 资源站配置表
-- ============================================
CREATE TABLE IF NOT EXISTS `resource_sites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT '资源站名称',
    `site_url` VARCHAR(500) DEFAULT '' COMMENT '官网地址',
    `api_url` VARCHAR(500) DEFAULT '' COMMENT '采集API地址',
    `type` VARCHAR(50) DEFAULT 'maccms' COMMENT '类型: maccms, other',
    `status` VARCHAR(20) DEFAULT 'active' COMMENT '状态: active启用 paused暂停',
    `priority` INT DEFAULT 99 COMMENT '优先级，数字越小越优先',
    `note` VARCHAR(500) DEFAULT '' COMMENT '备注',
    `config` TEXT COMMENT '额外配置(JSON)',
    `last_check_time` DATETIME DEFAULT NULL COMMENT '最后检测时间',
    `last_check_status` VARCHAR(50) DEFAULT '' COMMENT '最后检测状态',
    `response_time` INT DEFAULT 0 COMMENT '响应时间(ms)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_status` (`status`),
    KEY `idx_priority` (`priority`),
    KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='资源站配置表';

-- ============================================
-- 4. 推荐采集（官采）资源站表
-- ============================================
CREATE TABLE IF NOT EXISTS `official_sites` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT '站点名称',
    `api_path` VARCHAR(200) DEFAULT '/api.php/provide/vod/' COMMENT 'API路径',
    `active_domain_index` INT DEFAULT 0 COMMENT '当前使用的域名索引',
    `domains` TEXT COMMENT '域名列表数组(JSON)',
    `status` VARCHAR(20) DEFAULT 'active' COMMENT '状态',
    `priority` INT DEFAULT 99 COMMENT '优先级',
    `note` VARCHAR(500) DEFAULT '' COMMENT '备注',
    `config` TEXT COMMENT '额外配置(JSON)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_status` (`status`),
    KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='推荐采集资源站表';

-- ============================================
-- 5. 代理池表
-- ============================================
CREATE TABLE IF NOT EXISTS `proxies` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `proxy_id` VARCHAR(64) NOT NULL COMMENT '代理唯一标识',
    `name` VARCHAR(200) DEFAULT '' COMMENT '代理名称',
    `type` VARCHAR(20) DEFAULT 'http' COMMENT '类型: http, https, socks5',
    `host` VARCHAR(255) NOT NULL COMMENT '代理主机',
    `port` INT NOT NULL COMMENT '代理端口',
    `username` VARCHAR(100) DEFAULT '' COMMENT '用户名',
    `password` VARCHAR(100) DEFAULT '' COMMENT '密码',
    `status` VARCHAR(20) DEFAULT 'active' COMMENT '状态: active, inactive, failed',
    `priority` INT DEFAULT 100 COMMENT '优先级',
    `success_count` INT DEFAULT 0 COMMENT '成功次数',
    `fail_count` INT DEFAULT 0 COMMENT '失败次数',
    `last_check` DATETIME DEFAULT NULL COMMENT '最后检测时间',
    `last_success` DATETIME DEFAULT NULL COMMENT '最后成功时间',
    `response_time` INT DEFAULT 0 COMMENT '平均响应时间(ms)',
    `source` VARCHAR(50) DEFAULT 'manual' COMMENT '来源: manual, web_fetch',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_proxy_id` (`proxy_id`),
    KEY `idx_status` (`status`),
    KEY `idx_type` (`type`),
    KEY `idx_host_port` (`host`, `port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='代理池表';

-- ============================================
-- 6. 官替平台配置表
-- ============================================
CREATE TABLE IF NOT EXISTS `official_platforms` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL COMMENT '平台名称',
    `domain` VARCHAR(255) NOT NULL COMMENT '平台域名',
    `enabled` TINYINT(1) DEFAULT 1 COMMENT '是否启用',
    `pattern` VARCHAR(500) DEFAULT '' COMMENT 'URL匹配正则',
    `title_selector` VARCHAR(500) DEFAULT '' COMMENT '标题选择器',
    `priority` INT DEFAULT 1 COMMENT '优先级',
    `config` TEXT COMMENT '额外配置(JSON)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_domain` (`domain`),
    KEY `idx_enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='官替平台配置表';

-- ============================================
-- 7. 自动学习运行记录表
-- ============================================
CREATE TABLE IF NOT EXISTS `auto_learn_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `run_time` DATETIME NOT NULL COMMENT '执行时间',
    `status` VARCHAR(20) DEFAULT 'success' COMMENT '执行状态',
    `sites_processed` INT DEFAULT 0 COMMENT '处理站点数',
    `videos_processed` INT DEFAULT 0 COMMENT '处理视频数',
    `rules_updated` INT DEFAULT 0 COMMENT '更新规则数',
    `rules_created` INT DEFAULT 0 COMMENT '新建规则数',
    `skipped` INT DEFAULT 0 COMMENT '跳过数',
    `duration` INT DEFAULT 0 COMMENT '耗时(秒)',
    `error_message` TEXT COMMENT '错误信息',
    `details` TEXT COMMENT '详细数据(JSON)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_run_time` (`run_time`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='自动学习运行记录表';

-- ============================================
-- 8. 播放器配置表
-- ============================================
CREATE TABLE IF NOT EXISTS `player_config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `config_key` VARCHAR(100) NOT NULL,
    `config_value` TEXT,
    `description` VARCHAR(255) DEFAULT '',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `idx_config_key` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='播放器配置表';

-- ============================================
-- 初始数据
-- ============================================

-- 系统默认配置
INSERT IGNORE INTO `sys_config` (`config_key`, `config_value`, `description`) VALUES
('auto_learn', '{"enabled":true,"interval_days":3,"videos_per_site":5,"max_sites_per_run":5,"min_segments":50,"max_ad_percentage":90}', '自动学习配置'),
('proxy_config', '{"enabled":false,"auto_switch":true,"check_interval":300,"timeout":10}', '代理配置'),
('official_replace', '{"enabled":true,"default_site":"量子","max_search_sites":5,"cache_ttl":3600}', '官替API配置'),
('official_sites', '{"enabled":true,"settings":{"auto_switch_domain":true,"max_retry_per_domain":2,"timeout":10,"default_limit":20}}', '推荐采集配置'),
('system_version', '"v2.22.0"', '系统版本');
