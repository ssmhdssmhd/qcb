<?php
/**
 * 超级嗅探 - 嗅探设置配置文件
 *
 * 后台「嗅探设置」页面读写此文件：
 *   - mode         当前使用的解析通道：official=官解解析 / replace=官替接口
 *   - official_api 官解接口配置（开关 + 接口参数）
 *   - replace_api  官替接口配置（开关 + 接口参数）
 *
 * 由 mx.php?action=sniffer/config 和 sniffer/config/save 维护
 */

return [
    // ============ 当前解析通道 ============
    // official = 走官解解析（调用官方解析 API 获取直链）
    // replace  = 走官替接口（调用官替 API 获取资源站匹配后的 m3u8）
    'mode' => 'official',

    // ============ 官解接口 ============
    'official_api' => [
        // 总开关：false 时即便 mode=official 也不会调用此接口
        'enabled'    => true,
        'name'       => '虾米官解',
        // 接口地址，使用时会自动拼接 urlencode($videoUrl)
        'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
        // 接口类型：redirect / json / text
        'type'       => 'json',
        // json 类型时，视频地址所在的字段名
        'url_field'  => 'play_url',
        // 自定义请求头
        'headers'    => [],
    ],

    // ============ 官替接口 ============
    'replace_api' => [
        // 总开关：false 时即便 mode=replace 也不会调用此接口
        'enabled'    => false,
        'name'       => '本地官替',
        // 默认调用本项目的官替接口（mx.php?action=official_replace/info）
        // 也可填写第三方官替接口
        'url'        => '',
        // 接口类型：redirect / json / text
        'type'       => 'json',
        // json 类型时，视频地址所在的字段名
        // 官替接口默认返回 {success, m3u8_url, ad_skip_url}，优先取 m3u8_url
        'url_field'  => 'm3u8_url',
        // 自定义请求头
        'headers'    => [],
    ],

    // ============ 更新时间 ============
    'update_date' => '2026-07-18',
];
