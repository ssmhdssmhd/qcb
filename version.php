<?php
return [
    'version' => 'v5.10.3',
    'build' => '20260810',
    'version_code' => 51003,
    'commit' => 'douju-tv-default',
    'updated_at' => '2026-08-10',
    'changelog' => [
        'v5.10.3' => [
            'date' => '2026-08-10',
            'title' => '【官替升级】新增抖剧TV 默认官替资源站 + 官替优先度 1-2000+',
            'changes' => [
                '新增官替资源站：抖剧TV（采集接口 https://www.douju.tv/api.php/provide/vod/，根源来源 www.360kan.com）',
                '设置抖剧TV 为默认官替资源站（default_site=抖剧TV，官替 search_sites 数组第1位=抖剧TV）',
                '抖剧TV 资源站 priority=1（数字越小越优先，1-2000+ 支持任意数字）',
                '抖剧TV 官替平台 priority=1，与腾讯/爱奇艺/优酷/芒果/B站 同为最优先梯队，但在 search_sites 固定第一',
                '后台官替管理页面：新增默认官替站显示、priority 升序表格 + 彩色徽章（⭐默认官替 / 高优先 / 普通 / 低优先）',
                '官替优先度编辑器：min=1 max=2000 step=1，附"数字越小越优先，1=最高"说明',
                '后台官替 API 配置：新增 default_site 输入框 + priority 规则说明渐变色提示条',
                'DbOfficialReplaceManager::searchInSites() 改为优先使用 DbResourceSiteManager（含 priority ASC 排序 + 最新资源站）',
                'DbOfficialReplaceManager::getDefaultConfig() 默认 default_site=抖剧TV，search_sites 头位抖剧TV，match_threshold=70',
                '官替匹配平台查询：getAllPlatforms / getPlatformByDomain 全部按 priority ASC 排序（1=最先匹配）',
                '资源站列表：抖剧TV 归类 group_name=官替资源站，root_source=www.360kan.com，official_replace=1',
            ],
        ],
        'v5.10.2' => [
            'date' => '2026-08-09',
            'title' => '后台菜单完善 + 官替平台 editor 修复',
            'changes' => [
                '后台 mxadmin.php 新增 AI自动学习 / CCTV直播源 菜单分组和页面',
                '官替平台 editor 支持 priority 输入',
                '修复 header Warning、反射私有属性访问、DataMigration rules 列白名单等问题',
            ],
        ],
        'v5.10.1' => [
            'date' => '2026-08-08',
            'title' => '新增 CCTV 直播源扩展模块',
            'changes' => [
                '新增 cctv/CctvSourceManager.php：GitHub 官方源抓取解析 + 定时自动更新',
                '新增 cctv/CctvSourceVerifier.php：源可用性验证',
                '新增 cctv/CctvPlaylistGenerator.php：M3U8 生成 + 播放页面集成',
            ],
        ],
        'v5.10.0' => [
            'date' => '2026-08-05',
            'title' => '新增 AI 自动学习模块（苹果CMS10接口分析）',
            'changes' => [
                '新增 ai_learn/MacCMS10Analyzer.php：苹果CMS10 采集接口分析 + 去除非正片内容',
                '新增 ai_learn/AutoLearnEngine.php：从资源站随机抓取视频 → 广告分析 → 学习规则',
                '新增 ai_learn/TaskScheduler.php：定时任务调度，确保无广告且正片',
                '新增 ai_learn/AiEndpointRouter.php：AI 接口地址配置化自动切换',
            ],
        ],
        'v5.9.7' => [
            'date' => '2026-08-03',
            'title' => 'M3U8 广告过滤系统基础版本',
            'changes' => [
                '核心 M3U8AdSkipper + AdFilter：M3U8 广告分析与过滤',
                'DataMigration 数据库迁移 + 官替/资源站/规则 SQLite 表',
                '后台资源站管理 + 规则管理 + 数据库管理',
            ],
        ],
    ],
];