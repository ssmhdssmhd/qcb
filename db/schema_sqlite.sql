-- M3U8 广告分析系统 - 数据库表结构 (SQLite 版本)

-- ============================================
-- 1. 系统配置表
-- ============================================
CREATE TABLE IF NOT EXISTS sys_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    config_key TEXT NOT NULL UNIQUE,
    config_value TEXT,
    description TEXT DEFAULT '',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_sys_config_key ON sys_config(config_key);

-- ============================================
-- 2. 域名广告规则表
-- ============================================
CREATE TABLE IF NOT EXISTS domain_rules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    domain TEXT NOT NULL UNIQUE,
    name TEXT DEFAULT '',
    note TEXT DEFAULT '',
    ad_threshold INTEGER DEFAULT 50,
    confidence_score INTEGER DEFAULT 0,
    learn_count INTEGER DEFAULT 0,
    analysis_date DATETIME,
    last_learn_date DATETIME,
    duration_rules TEXT,
    discontinuity_rules TEXT,
    sequence_jump_rules TEXT,
    filename_patterns TEXT,
    insertion_patterns TEXT,
    ad_type_stats TEXT,
    psychological_profile TEXT,
    marker_stats TEXT,
    analysis_stats TEXT,
    history_stats TEXT,
    marker_detection TEXT,
    confidence TEXT,
    enable_marker_detection INTEGER DEFAULT 0,
    status INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_domain_rules_domain ON domain_rules(domain);
CREATE INDEX IF NOT EXISTS idx_domain_rules_status ON domain_rules(status);
CREATE INDEX IF NOT EXISTS idx_domain_rules_updated ON domain_rules(updated_at);

-- ============================================
-- 3. 资源站配置表
-- ============================================
CREATE TABLE IF NOT EXISTS resource_sites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    site_url TEXT DEFAULT '',
    api_url TEXT DEFAULT '',
    type TEXT DEFAULT 'maccms',
    status TEXT DEFAULT 'active',
    priority INTEGER DEFAULT 99,
    note TEXT DEFAULT '',
    config TEXT,
    last_check_time DATETIME,
    last_check_status TEXT DEFAULT '',
    response_time INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_resource_sites_status ON resource_sites(status);
CREATE INDEX IF NOT EXISTS idx_resource_sites_priority ON resource_sites(priority);
CREATE INDEX IF NOT EXISTS idx_resource_sites_name ON resource_sites(name);

-- ============================================
-- 4. 推荐采集资源站表
-- ============================================
CREATE TABLE IF NOT EXISTS official_sites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    api_path TEXT DEFAULT '/api.php/provide/vod/',
    active_domain_index INTEGER DEFAULT 0,
    domains TEXT,
    status TEXT DEFAULT 'active',
    priority INTEGER DEFAULT 99,
    note TEXT DEFAULT '',
    config TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_official_sites_status ON official_sites(status);
CREATE INDEX IF NOT EXISTS idx_official_sites_priority ON official_sites(priority);

-- ============================================
-- 5. 代理池表
-- ============================================
CREATE TABLE IF NOT EXISTS proxies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    proxy_id TEXT NOT NULL UNIQUE,
    name TEXT DEFAULT '',
    type TEXT DEFAULT 'http',
    host TEXT NOT NULL,
    port INTEGER NOT NULL,
    username TEXT DEFAULT '',
    password TEXT DEFAULT '',
    status TEXT DEFAULT 'active',
    priority INTEGER DEFAULT 100,
    success_count INTEGER DEFAULT 0,
    fail_count INTEGER DEFAULT 0,
    last_check DATETIME,
    last_success DATETIME,
    response_time INTEGER DEFAULT 0,
    source TEXT DEFAULT 'manual',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_proxies_proxy_id ON proxies(proxy_id);
CREATE INDEX IF NOT EXISTS idx_proxies_status ON proxies(status);
CREATE INDEX IF NOT EXISTS idx_proxies_type ON proxies(type);
CREATE INDEX IF NOT EXISTS idx_proxies_host_port ON proxies(host, port);

-- ============================================
-- 6. 官替平台配置表
-- ============================================
CREATE TABLE IF NOT EXISTS official_platforms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    domain TEXT NOT NULL,
    enabled INTEGER DEFAULT 1,
    pattern TEXT DEFAULT '',
    title_selector TEXT DEFAULT '',
    priority INTEGER DEFAULT 1,
    config TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_official_platforms_domain ON official_platforms(domain);
CREATE INDEX IF NOT EXISTS idx_official_platforms_enabled ON official_platforms(enabled);

-- ============================================
-- 7. 自动学习运行记录表
-- ============================================
CREATE TABLE IF NOT EXISTS auto_learn_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    run_time DATETIME NOT NULL,
    status TEXT DEFAULT 'success',
    sites_processed INTEGER DEFAULT 0,
    videos_processed INTEGER DEFAULT 0,
    rules_updated INTEGER DEFAULT 0,
    rules_created INTEGER DEFAULT 0,
    skipped INTEGER DEFAULT 0,
    duration INTEGER DEFAULT 0,
    error_message TEXT,
    details TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_auto_learn_logs_run_time ON auto_learn_logs(run_time);
CREATE INDEX IF NOT EXISTS idx_auto_learn_logs_status ON auto_learn_logs(status);

-- ============================================
-- 8. 播放器配置表
-- ============================================
CREATE TABLE IF NOT EXISTS player_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    config_key TEXT NOT NULL UNIQUE,
    config_value TEXT,
    description TEXT DEFAULT '',
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 初始数据
-- ============================================
INSERT OR IGNORE INTO sys_config (config_key, config_value, description) VALUES
('auto_learn', '{"enabled":true,"interval_days":3,"videos_per_site":5,"max_sites_per_run":5,"min_segments":50,"max_ad_percentage":90}', '自动学习配置'),
('proxy_config', '{"enabled":false,"auto_switch":true,"check_interval":300,"timeout":10}', '代理配置'),
('official_replace', '{"enabled":true,"default_site":"量子","max_search_sites":5,"cache_ttl":3600}', '官替API配置'),
('official_sites', '{"enabled":true,"settings":{"auto_switch_domain":true,"max_retry_per_domain":2,"timeout":10,"default_limit":20}}', '推荐采集配置'),
('system_version', '"v2.22.0"', '系统版本');
