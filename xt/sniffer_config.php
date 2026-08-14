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
    // concurrent = 同时调用官解+官替（curl_multi 并发，最快成功的立即返回）v5.13.4
    // official   = 走官解解析（调用官方解析 API 获取直链）
    // replace    = 走官替接口（调用官替 API 获取资源站匹配后的 m3u8）
    'mode' => 'concurrent',

    // ============ 官解接口（支持多个，AI 学习自动排序） ============
    // v5.13.3-D4：2026-08-14 替换虾米官解到 https://jx.xmflv.cc/?url=（新 HTML 播放器）
    //             play_url 返回整段播放器链接 → 302 跳转或 <iframe src= 直接播放。
    'official_apis' => [
        [
            'enabled'    => true,
            'name'       => '虾米官解(jx.xmflv.cc v5.13.3新地址，HTML播放器{url}{ref}占位符，直接302跳转/iframe播放)',
            'url'        => 'https://jx.xmflv.cc/?url={url}&ref={ref}',
            'type'       => 'html_player',
            'url_field'  => 'play_url',
            'headers'    => [
                'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language'           => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
                'sec-ch-ua'                 => '"Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"',
                'sec-ch-ua-mobile'          => '?0',
                'sec-ch-ua-platform'        => '"Windows"',
                'Upgrade-Insecure-Requests' => '1',
            ],
        ],
    ],

    // ============ 官解接口（单接口兼容，保留旧结构） ============
    'official_api' => [
        // v5.13.3-D4：单接口兼容同样替换为 jx.xmflv.cc 新地址 + enabled=true
        'enabled'    => true,
        'name'       => '虾米官解(单接口兼容：jx.xmflv.cc新地址 HTML播放器)',
        // 接口地址：支持 {url} {ref} {origin} {ts} {t} 占位符（v5.13.3+）
        'url'        => 'https://jx.xmflv.cc/?url={url}&ref={ref}',
        // 接口类型：redirect / json / text / html_player（v5.13.3 新增 html_player=直接把 URL 作为play_url）
        'type'       => 'html_player',
        // json 类型时，视频地址所在的字段名
        'url_field'  => 'play_url',
        // 自定义请求头
        'headers'    => [
            'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
            'Accept-Language'           => 'zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7',
            'Upgrade-Insecure-Requests' => '1',
        ],
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
