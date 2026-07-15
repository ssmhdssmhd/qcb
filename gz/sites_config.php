<?php
/**
 * 资源站配置列表
 * 包含支持的资源站信息：官网、采集接口、备注等
 * 自动学习功能会从这些资源站获取视频进行规则学习
 */

return [
    'version' => '4.0.1',
    'update_date' => '2026-07-15',
    'sites' => [
        [
            'name' => '量子',
            'site_url' => 'http://23.224.101.30',
            'api_url' => 'https://cj.lziapi.com/api.php/provide/vod/from/lzm3u8/',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '推荐',
            'priority' => 1
        ],
        [
            'name' => '暴风',
            'site_url' => 'http://bfzv8.lv',
            'api_url' => 'https://bfzyapi.com/api.php/provide/vod/from/bfzym3u8/',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '推荐',
            'priority' => 1
        ],
        [
            'name' => '非凡',
            'site_url' => 'https://www.ffzy.tv',
            'api_url' => 'https://www.ffzy.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '推荐，API已更新',
            'priority' => 1
        ],
        [
            'name' => '优质',
            'site_url' => 'https://www.yzzy-app.com',
            'api_url' => 'https://api.yzzy-app.com/mc.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '天影',
            'site_url' => 'https://wsyzy.cc',
            'api_url' => 'https://wsyzy.cc/api.php/provide/vod/from/wysm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '卡',
            'priority' => 2
        ],
        [
            'name' => '6度资源',
            'site_url' => 'https://www.6duzy.com',
            'api_url' => 'https://www.mdzyapi.com/api.php/provide/vod/from/lium3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 2
        ],
        [
            'name' => '360酷',
            'site_url' => 'https://www.360ku.com',
            'api_url' => 'https://360ku.com/api.php/provide/vod/from/360ku/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '已失效',
            'priority' => 99
        ],
        [
            'name' => '八豆',
            'site_url' => 'https://badouzy.com',
            'api_url' => 'https://api.akuapi.com/api.php/provide/vod/from/ukm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '豆包',
            'site_url' => 'https://dbzy.tv',
            'api_url' => 'https://dbzy.tv/api.php/provide/vod/from/dbm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 2
        ],
        [
            'name' => '猫眼',
            'site_url' => 'https://www.maoyanzy.com',
            'api_url' => 'https://www.maoyanzy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 3
        ],
        [
            'name' => '10樱花',
            'site_url' => 'https://yhznyy.com',
            'api_url' => 'https://yhznyy.com/api.php/provide/vod/from/hm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '爱奇艺',
            'site_url' => 'https://iqiyiapi.w1612.cc',
            'api_url' => 'https://iqiyiapi.w1612.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '12官方',
            'site_url' => 'https://www.gfzyw.com',
            'api_url' => 'https://api.gfzyw.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '404失效',
            'priority' => 99
        ],
        [
            'name' => '索尼',
            'site_url' => 'https://suonizy.cc',
            'api_url' => 'https://suonizy.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 4
        ],
        [
            'name' => '最大',
            'site_url' => 'https://www.zuidazy.co',
            'api_url' => 'https://www.zuidazy.co/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 4
        ],
        [
            'name' => 'OK资源',
            'site_url' => 'https://okzyw.cc',
            'api_url' => 'https://okzyw.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 4
        ],
        [
            'name' => '牛牛',
            'site_url' => 'https://www.niuniuzy.vip',
            'api_url' => 'https://api.niuniuzy.com/api.php/provide/vod/from/nnm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '快车',
            'site_url' => 'https://kuaichezy.com',
            'api_url' => 'https://kuaichezy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 4
        ],
        [
            'name' => '闪电',
            'site_url' => 'https://shandianzy.com',
            'api_url' => 'https://shandianzy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 4
        ],
        [
            'name' => '丫丫（鸭鸭）',
            'site_url' => 'https://yayazy.com',
            'api_url' => 'https://cj.yayazy.net/api.php/provide/vod/from/yym3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 4
        ],
        [
            'name' => '无尽',
            'site_url' => 'https://wujinwszy.com',
            'api_url' => 'http://api.wujinwszy.net/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '无水印，但卡',
            'priority' => 5
        ],
        [
            'name' => '新浪',
            'site_url' => 'https://www.xinlangzy.net',
            'api_url' => 'https://www.xinlangapi.com/xinlangapi.php/provide/vod/from/xlm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '速播',
            'site_url' => 'https://www.subozy.com',
            'api_url' => 'https://www.subozy.com/api.php/provide/vod/',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 5
        ],
        [
            'name' => '红牛',
            'site_url' => 'https://hongniuzY2.com',
            'api_url' => 'https://www.hongniuzY2.com/api.php/provide/vod/from/hnm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 5
        ],
        [
            'name' => '极速',
            'site_url' => 'https://www.jisusy.com',
            'api_url' => 'https://jsszyapi.com/api.php/provide/vod/from/jsm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '豪华',
            'site_url' => 'https://hhzyapi.com',
            'api_url' => 'https://hhzyapi.com/api.php/provide/vod/from/hhm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 5
        ],
        [
            'name' => '光速',
            'site_url' => 'https://guangsuzy.net',
            'api_url' => 'https://api.guangsuapi.com/api.php/provide/vod/from/gsm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 5
        ],
        [
            'name' => '淘片',
            'site_url' => 'https://www.taopianzy.com/index.php',
            'api_url' => 'https://taopianapi.com/cjapi/mc10/vod/json/m3u8.html',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '茅台',
            'site_url' => 'https://mtzy.me',
            'api_url' => 'https://caiji.maotai999.vip/api.php/provide/vod/from/mtm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 6
        ],
        [
            'name' => '10樱花',
            'site_url' => 'https://www.huayuapi.com',
            'api_url' => 'https://m3u8.apihyzy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '虎牙',
            'site_url' => 'https://jinyangzy.com',
            'api_url' => 'https://jyzypy.com/provide/vod/from/jinyangm3u8/at/json',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '13大众',
            'site_url' => 'https://www.dzzy.com',
            'api_url' => 'http://cdn.dzzyapi.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '仅支持HTTP',
            'priority' => 6
        ],
        [
            'name' => 'ikun',
            'site_url' => 'https://ikunzyapi.com',
            'api_url' => 'https://ikunzypi.com/api.php/provide/vod/',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => 'SSL连接失败',
            'priority' => 99
        ],
        [
            'name' => '如意',
            'site_url' => 'https://www.ryzy.tv',
            'api_url' => 'https://www.ryzy.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'API已更新',
            'priority' => 7
        ],
        [
            'name' => '天繁',
            'site_url' => 'https://www.tianfanzy.com',
            'api_url' => 'https://caiji.dyttzyapi.com/api.php/provide/vod/from/dyttm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => 'IP验证无法播放，开全局代理或返回原始链接',
            'priority' => 7
        ],
        [
            'name' => '西瓜',
            'site_url' => 'https://xgzy.tv',
            'api_url' => 'https://xgzy.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '',
            'priority' => 7
        ],
        [
            'name' => '飞刀资源',
            'site_url' => 'http://www.feidaozy.com',
            'api_url' => 'https://www.feidaozy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '乐视',
            'site_url' => 'https://leshizy.com',
            'api_url' => 'https://leshiapi.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '好看',
            'site_url' => 'https://xhkanzy9.com',
            'api_url' => 'https://xkanzy10.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '奇快',
            'site_url' => 'https://qikuzy.tv/',
            'api_url' => 'https://caiji.qhzyapi.com/api.php/provide/vod/from/qhm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '九月',
            'site_url' => 'https://jiuyuezy.com',
            'api_url' => 'https://kuaikan-api.com/api.php/provide/vod/from/kykuakan/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '黑木耳',
            'site_url' => 'https://heimuer.tv',
            'api_url' => 'https://api.mdzyapi.com/api.php/provide/vod/from/lym3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '停更',
            'priority' => 99
        ],
        [
            'name' => '飞速',
            'site_url' => 'https://www.feisuzy.com',
            'api_url' => 'https://www.feisuzyapi.com/api.php/provide/vod/from/fsm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '停更，调用量大到ip，正常链接和广告链接混合一起，无',
            'priority' => 99
        ],
        [
            'name' => '天空',
            'site_url' => 'http://tiankongzy.cc',
            'api_url' => 'http://tiankongzy.cc/api.php/provide/vod/from/tkm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '优优',
            'site_url' => 'https://jk2.yycmsszywapi.cc',
            'api_url' => 'https://jk2.yycmsszywapi.cc/api.php/provide/vod',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '无线',
            'site_url' => 'https://wuxianzy8.com',
            'api_url' => 'https://api.wuxianzy.net/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '跑路',
            'priority' => 99
        ],
        [
            'name' => '12鱼乐',
            'site_url' => 'https://104.161.22.125',
            'api_url' => 'https://104.161.22.125/api.php/provide/vod/from/lem3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '很多服务器不支持，翻墙了',
            'priority' => 99
        ],
        [
            'name' => '13华为',
            'site_url' => 'https://cjhwba.com',
            'api_url' => 'https://cajhwba.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '',
            'priority' => 99
        ],
        [
            'name' => '蜂巢',
            'site_url' => 'https://fczy908.com',
            'api_url' => 'https://api.fczy888.me/api.php/provide/vod/from/fcm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '停运',
            'priority' => 99
        ],
        [
            'name' => '15魔术',
            'site_url' => 'https://mzzy.me',
            'api_url' => 'https://mozhuazy.com/api.php/provide/vod/from/mzm3u8/?ac=list',
            'type' => 'maccms',
            'status' => 'paused',
            'note' => '',
            'priority' => 99
        ],
        [
            'name' => '蓝光',
            'site_url' => 'https://lgzyz.xyz',
            'api_url' => 'https://lgzyz.xyz/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增，4K资源',
            'priority' => 5
        ],
        [
            'name' => '魔都',
            'site_url' => 'https://www.moduzy.cc',
            'api_url' => 'https://www.moduzy.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 5
        ],
        [
            'name' => '看看',
            'site_url' => 'https://kankan01.com',
            'api_url' => 'https://kankan01.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 5
        ],
        [
            'name' => '樱花',
            'site_url' => 'https://yhzy.cc',
            'api_url' => 'https://yhzy.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 5
        ],
        [
            'name' => '好花',
            'site_url' => 'https://www.haohuazy.com',
            'api_url' => 'https://www.haohuazy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 5
        ],
        [
            'name' => '百度',
            'site_url' => 'https://bdzy.tv',
            'api_url' => 'http://bdzy.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增，仅支持HTTP',
            'priority' => 6
        ],
        [
            'name' => '电影天堂',
            'site_url' => 'https://dyttzyw.tv',
            'api_url' => 'https://dyttzyw.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 5
        ],
        [
            'name' => '爱奇艺资',
            'site_url' => 'https://iqiyizy.cc',
            'api_url' => 'https://iqiyizy.cc/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 6
        ],
        [
            'name' => '牛牛6',
            'site_url' => 'https://niuniuzy6.com',
            'api_url' => 'https://niuniuzy6.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 6
        ],
        [
            'name' => '蓝志',
            'site_url' => 'https://www.lzzy.tv',
            'api_url' => 'https://www.lzzy.tv/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 6
        ],
        [
            'name' => '天逸',
            'site_url' => 'https://tyyszy.com',
            'api_url' => 'https://tyyszy.com/api.php/provide/vod/?ac=list',
            'type' => 'maccms',
            'status' => 'active',
            'note' => '新增',
            'priority' => 6
        ]
    ],
    'auto_learn' => [
        'enabled' => true,
        'interval_days' => 3,
        'videos_per_site' => 5,
        'max_sites_per_run' => 5,
        'min_segments' => 50,
        'max_ad_percentage' => 90
    ]
];
