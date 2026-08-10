<?php
/**
 * 模块化开关配置（由后台「模块管理」页面写入，也可以手动改）
 * 规则：
 *   - true  = 强制启用（即使 manifest.defaultEnabled=false 也会启用；但缺依赖仍会被禁用）
 *   - false = 强制禁用（连 require_once 都不做，零成本，该模块的 API/UI 自动消失）
 *   - 没写在这里的 = 走 manifest.defaultEnabled（推荐，新增模块自动生效/失效不用改本文件）
 */
return [
    "modules" => [
        "core_db"           => true , // 核心-数据库层 v5.11.0
        "ad_filter"         => true , // M3U8广告过滤核心 v5.11.0
        "platform_sniffer"  => true , // 平台适配器/官解通道 v5.11.0
        "resource_sites"    => true , // 资源站管理 v5.11.0
        "official_replace"  => true , // 官替识别 v5.11.0
        "proxy"             => true , // IP代理池 v5.11.0
        "ai_learn"          => true , // AI自动学习广告规则 v5.11.0
        "cctv"              => true , // CCTV央视直播源 v5.11.0
    ],
];