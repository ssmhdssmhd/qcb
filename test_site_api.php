<?php
/**
 * 靶机测试 - 模拟资源站 API 接口
 * 
 * 模拟一个完整的视频资源站采集接口
 * 用于测试搜索、学习、批量处理等功能
 * 
 * 支持的接口:
 *   - ?ac=list            视频列表
 *   - ?ac=videolist         视频列表（别名）
 *   - ?ac=detail&ids=xxx      视频详情
 *   - ?wd=关键词              搜索视频
 *   - ?ac=detail&ids=xxx&play=1  播放地址
 * 
 * 使用方法：
 *   1. 在此目录下启动 PHP 内置服务器: php -S localhost:8080
 *   2. 或直接通过 Web 服务器访问此文件
 *   3. 在后台资源站管理中添加此接口
 */

header('Content-Type: application/json; charset=utf-8');

// 基础配置
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$m3u8Base = $baseUrl . '/cache/m3u8/test';

// 模拟视频数据
$videoData = [
    [
        'vod_id' => 1,
        'vod_name' => '庆余年 第一季',
        'vod_sub' => '庆余年',
        'vod_en' => 'QingYuNian',
        'vod_status' => 1,
        'vod_letter' => 'Q',
        'vod_douban_id' => 0,
        'vod_douban_score' => '9.2',
        'vod_reurl' => '',
        'vod_rel' => '',
        'vod_pic' => '',
        'vod_pic_thumb' => '',
        'vod_pic_slide' => '',
        'vod_pic_screenshot' => '',
        'vod_actor' => '张若昀,李沁,陈道明',
        'vod_director' => '孙皓',
        'vod_content' => '范闲十五岁的夏天，范闲按照父亲范建及监察院院长陈萍萍派来的老师费介，开始学习医术和毒术。。。',
        'vod_blurb' => '范闲十五岁那年。。。',
        'vod_remarks' => '46集全',
        'vod_pubdate' => '2019',
        'vod_total' => 46,
        'vod_serial' => '',
        'vod_touch' => 0,
        'vod_score' => '9.2',
        'vod_score_all' => 0,
        'vod_score_num' => 0,
        'vod_hits' => 12580,
        'vod_hits_day' => 0,
        'vod_hits_week' => 0,
        'vod_hits_month' => 0,
        'vod_isend' => 1,
        'vod_lock' => 0,
        'vod_level' => 0,
        'vod_copyright' => 0,
        'vod_points' => 0,
        'vod_points_play' => 0,
        'vod_points_down' => 0,
        'vod_stint' => 0,
        'vod_stint' => 0,
        'vod_stint_down' => 0,
        'vod_addtime' => 1700000000,
        'vod_time' => 1700000000,
        'vod_play_from' => 'm3u8',
        'vod_play_server' => '',
        'vod_play_note' => '',
        'vod_play_url' => '第01集$' . $m3u8Base . '/mixed.m3u8' . "\n" .
                          '第02集$' . $m3u8Base . '/pre_roll.m3u8' . "\n" .
                          '第03集$' . $m3u8Base . '/mid_roll.m3u8' . "\n" .
                          '第04集$' . $m3u8Base . '/post_roll.m3u8' . "\n" .
                          '第05集$' . $m3u8Base . '/long_movie.m3u8' . "\n" .
                          '第06集$' . $m3u8Base . '/short_segments.m3u8' . "\n" .
                          '第07集$' . $m3u8Base . '/basic.m3u8',
        'vod_down_from' => '',
        'vod_down_server' => '',
        'vod_down_url' => '',
        'vod_plot' => 0,
        'vod_plot_name' => '',
        'vod_plot_detail' => '',
        'type_id' => 1,
        'type_id_1' => 1,
        'type_name' => '国产剧',
    ],
    [
        'vod_id' => 2,
        'vod_name' => '狂飙',
        'vod_sub' => '狂飙',
        'vod_en' => 'KuangBiao',
        'vod_status' => 1,
        'vod_letter' => 'K',
        'vod_douban_score' => '8.9',
        'vod_pic' => '',
        'vod_actor' => '张译,张颂文,李一桐',
        'vod_director' => '徐纪周',
        'vod_content' => '2000年，意气风发的刑警安欣与倍受欺负的鱼贩子高启强相识。。。',
        'vod_blurb' => '刑警与黑恶势力的20年正邪较量',
        'vod_remarks' => '39集全',
        'vod_pubdate' => '2023',
        'vod_score' => '8.9',
        'vod_hits' => 9860,
        'vod_isend' => 1,
        'vod_addtime' => 1700001000,
        'vod_time' => 1700001000,
        'vod_play_from' => 'm3u8',
        'vod_play_url' => '第01集$' . $m3u8Base . '/mixed.m3u8' . "\n" .
                          '第02集$' . $m3u8Base . '/mid_roll.m3u8' . "\n" .
                          '第03集$' . $m3u8Base . '/long_movie.m3u8',
        'type_id' => 1,
        'type_id_1' => 1,
        'type_name' => '国产剧',
    ],
    [
        'vod_id' => 3,
        'vod_name' => '长津湖',
        'vod_sub' => '长津湖',
        'vod_en' => 'ChangJinHu',
        'vod_status' => 1,
        'vod_letter' => 'C',
        'vod_douban_score' => '7.4',
        'vod_pic' => '',
        'vod_actor' => '吴京,易烊千玺,段奕宏',
        'vod_director' => '陈凯歌,徐克,林超贤',
        'vod_content' => '电影以抗美援朝战争第二次战役中的长津湖战役为背景。。。',
        'vod_blurb' => '抗美援朝史诗巨制',
        'vod_remarks' => '高清',
        'vod_pubdate' => '2021',
        'vod_score' => '7.4',
        'vod_hits' => 15680,
        'vod_isend' => 1,
        'vod_addtime' => 1700002000,
        'vod_time' => 1700002000,
        'vod_play_from' => 'm3u8',
        'vod_play_url' => '正片$' . $m3u8Base . '/long_movie.m3u8',
        'type_id' => 2,
        'type_id_1' => 2,
        'type_name' => '动作片',
    ],
    [
        'vod_id' => 4,
        'vod_name' => '流浪地球2',
        'vod_sub' => '流浪地球',
        'vod_en' => 'LiuLangDiQiu2',
        'vod_status' => 1,
        'vod_letter' => 'L',
        'vod_douban_score' => '8.3',
        'vod_pic' => '',
        'vod_actor' => '吴京,刘德华,李雪健',
        'vod_director' => '郭帆',
        'vod_content' => '太阳即将毁灭，人类在地球表面建造出巨大的推进器。。。',
        'vod_blurb' => '中国科幻史诗巨制',
        'vod_remarks' => '高清',
        'vod_pubdate' => '2023',
        'vod_score' => '8.3',
        'vod_hits' => 18920,
        'vod_isend' => 1,
        'vod_addtime' => 1700003000,
        'vod_time' => 1700003000,
        'vod_play_from' => 'm3u8',
        'vod_play_url' => '正片$' . $m3u8Base . '/mixed.m3u8',
        'type_id' => 2,
        'type_id_1' => 2,
        'type_name' => '科幻片',
    ],
    [
        'vod_id' => 5,
        'vod_name' => '三体',
        'vod_sub' => '三体',
        'vod_en' => 'SanTi',
        'vod_status' => 1,
        'vod_letter' => 'S',
        'vod_douban_score' => '8.7',
        'vod_pic' => '',
        'vod_actor' => '张鲁一,于和伟,陈瑾',
        'vod_director' => '杨磊',
        'vod_content' => '2007年，地球基础科学出现了异常的扰动。。。',
        'vod_blurb' => '根据刘慈欣同名小说改编',
        'vod_remarks' => '30集全',
        'vod_pubdate' => '2023',
        'vod_score' => '8.7',
        'vod_hits' => 11250,
        'vod_isend' => 1,
        'vod_addtime' => 1700004000,
        'vod_time' => 1700004000,
        'vod_play_from' => 'm3u8',
        'vod_play_url' => '第01集$' . $m3u8Base . '/pre_roll.m3u8' . "\n" .
                          '第02集$' . $m3u8Base . '/mid_roll.m3u8' . "\n" .
                          '第03集$' . $m3u8Base . '/post_roll.m3u8',
        'type_id' => 1,
        'type_id_1' => 1,
        'type_name' => '国产剧',
    ],
];

// 分类列表
$typeList = [
    ['type_id' => 1, 'type_name' => '国产剧', 'type_mid' => 'dianshiju'],
    ['type_id' => 2, 'type_name' => '动作片', 'type_mid' => 'dongzuopian'],
    ['type_id' => 3, 'type_name' => '科幻片', 'type_mid' => 'kehuanpian'],
    ['type_id' => 4, 'type_name' => '喜剧片', 'type_mid' => 'xijupian'],
];

// 获取参数
$ac = $_GET['ac'] ?? '';
$wd = $_GET['wd'] ?? '';
$ids = $_GET['ids'] ?? '';
$page = isset($_GET['pg']) ? intval($_GET['pg']) : (isset($_GET['page']) ? intval($_GET['page']) : 1);
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : (isset($_GET['size']) ? intval($_GET['size']) : 20);

// 搜索处理
if (!empty($wd)) {
    $keyword = strtolower($wd);
    $filtered = array_filter($videoData, function($v) use ($keyword) {
        return strpos(strtolower($v['vod_name']), $keyword) !== false
            || strpos(strtolower($v['vod_sub']), $keyword) !== false
            || strpos(strtolower($v['vod_en']), $keyword) !== false
            || strpos(strtolower($v['vod_actor']), $keyword) !== false;
    });
    
    $total = count($filtered);
    $offset = ($page - 1) * $limit;
    $pageData = array_slice(array_values($filtered), $offset, $limit);
    
    $result = [
        'code' => 1,
        'msg' => 'ok',
        'page' => $page,
        'pagecount' => ceil($total / $limit),
        'limit' => $limit,
        'total' => $total,
        'list' => array_values($pageData),
    ];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 详情处理
if ($ac === 'detail' && !empty($ids)) {
    $id = intval($ids);
    $found = null;
    foreach ($videoData as $v) {
        if ($v['vod_id'] == $id) {
            $found = $v;
            break;
        }
    }
    
    if ($found) {
        $result = [
            'code' => 1,
            'msg' => 'ok',
            'list' => [$found],
        ];
    } else {
        $result = [
            'code' => 0,
            'msg' => '视频不存在',
            'list' => [],
        ];
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 列表处理
if ($ac === 'list' || $ac === 'videolist' || $ac === '') {
    $total = count($videoData);
    $offset = ($page - 1) * $limit;
    $pageData = array_slice($videoData, $offset, $limit);
    
    $result = [
        'code' => 1,
        'msg' => 'ok',
        'page' => $page,
        'pagecount' => ceil($total / $limit),
        'limit' => $limit,
        'total' => $total,
        'list' => array_values($pageData),
    ];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

// 默认返回
echo json_encode([
    'code' => 0,
    'msg' => '参数错误',
    'list' => [],
], JSON_UNESCAPED_UNICODE);
