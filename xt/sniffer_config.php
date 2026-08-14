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
    'mode' => 'replace',

    // ============ 官解接口（支持多个，AI 学习自动排序） ============
    // v5.13.2-C3：第三方虾米官解 114.134.184.91:9002 已于 2026-08-14 改为签名/白名单校验，
    // 任何请求都返回 {"success":false,"message":"验证失败!"}，故默认关闭。
    'official_apis' => [
        [
            'enabled'    => false,
            'name'       => '虾米官解(第三方服务器2026-08-14起需签名验证：请改为官替本地直调或替换为可用的官解地址)',
            'url'        => 'http://114.134.184.91:9002/mx.php?action=api/v2&type=parse&url=',
            'type'       => 'json',
            'url_field'  => 'play_url',
            'headers'    => [],
        ],
    ],

    // ============ 官解接口（单接口兼容，保留旧结构） ============
    'official_api' => [
        // v5.13.2-C3：总开关默认 false。第三方服务器已加签名验证，不再作为默认通道。
        'enabled'    => false,
        'name'       => '虾米官解(2026-08-14已失效，见上方注释)',
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
        'enabled'    => true,
        'name'       => '本地官替',
        // 默认调用本项目的官替接口（mx.php?action=official_replace/info）
        // 也可填写第三方官替接口
        'url'        => '',
        // 接口类型：redirect / json / text
        'type'       => 'json',
        // json 类型时，视频地址所在的字段名
        // 官替接口默认返回 {success, m3u8_url, ad_skip_url}，优先取去广告后的 ad_skip_url
        'url_field'  => 'ad_skip_url',
        // 自定义请求头
        'headers'    => [],
    ],

    // ============ 更新时间 ============
    'update_date' => '2026-08-14',
];
