<?php
/**
 * M3U8 广告分析系统 - PHP 接口调用示例
 * 
 * 本文件包含各种接口的 PHP 调用示例代码
 * 使用方法：在你的项目中引入相关函数，或者参考示例代码自行实现
 * 
 * 版本: v2.30.1
 */

// ==================== 基础配置 ====================

// 你的系统域名（请根据实际情况修改）
define('API_BASE_URL', 'https://your-domain.com/mx.php?action=');

// ==================== 通用 HTTP 请求函数 ====================

/**
 * GET 请求
 * @param string $url 请求地址
 * @param int $timeout 超时时间（秒）
 * @return array ['success' => bool, 'data' => mixed, 'error' => string]
 */
function api_get($url, $timeout = 30) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept: application/json, */*',
        ],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        return ['success' => false, 'data' => null, 'error' => $error];
    }
    
    $data = json_decode($response, true);
    if ($data === null) {
        return ['success' => false, 'data' => $response, 'error' => 'JSON 解析失败'];
    }
    
    return ['success' => true, 'data' => $data, 'http_code' => $httpCode];
}

/**
 * POST 请求 (JSON)
 * @param string $url 请求地址
 * @param array $postData POST 数据
 * @param int $timeout 超时时间（秒）
 * @return array ['success' => bool, 'data' => mixed, 'error' => string]
 */
function api_post_json($url, $postData = [], $timeout = 30) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData, JSON_UNESCAPED_UNICODE),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json; charset=utf-8',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept: application/json, */*',
        ],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        return ['success' => false, 'data' => null, 'error' => $error];
    }
    
    $data = json_decode($response, true);
    if ($data === null) {
        return ['success' => false, 'data' => $response, 'error' => 'JSON 解析失败'];
    }
    
    return ['success' => true, 'data' => $data, 'http_code' => $httpCode];
}

/**
 * POST 请求 (表单)
 * @param string $url 请求地址
 * @param array $postData POST 数据
 * @param int $timeout 超时时间（秒）
 * @return array ['success' => bool, 'data' => mixed, 'error' => string]
 */
function api_post_form($url, $postData = [], $timeout = 30) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
            'Accept: application/json, */*',
        ],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($response === false) {
        return ['success' => false, 'data' => null, 'error' => $error];
    }
    
    $data = json_decode($response, true);
    if ($data === null) {
        return ['success' => false, 'data' => $response, 'error' => 'JSON 解析失败'];
    }
    
    return ['success' => true, 'data' => $data, 'http_code' => $httpCode];
}

// ==================== 视频分析接口 ====================

/**
 * 分析视频广告
 * @param string $videoUrl 视频 m3u8 URL
 * @param bool $autoLearn 是否自动学习
 * @return array
 */
function analyzeVideo($videoUrl, $autoLearn = false) {
    $url = API_BASE_URL . 'analyze&url=' . urlencode($videoUrl);
    if ($autoLearn) {
        $url .= '&auto_learn=1';
    }
    return api_get($url);
}

// ==================== 规则管理接口 ====================

/**
 * 获取所有规则列表
 * @return array
 */
function getRulesList() {
    return api_get(API_BASE_URL . 'rules/list');
}

/**
 * 获取指定域名的规则
 * @param string $domain 域名
 * @return array
 */
function getDomainRules($domain) {
    $url = API_BASE_URL . 'rules/get&domain=' . urlencode($domain);
    return api_get($url);
}

/**
 * 保存域名规则
 * @param string $domain 域名
 * @param array $rules 规则配置
 * @return array
 */
function saveDomainRules($domain, $rules) {
    return api_post_json(API_BASE_URL . 'rules/save', [
        'domain' => $domain,
        'rules' => $rules
    ]);
}

/**
 * 删除域名规则
 * @param string $domain 域名
 * @return array
 */
function deleteDomainRules($domain) {
    return api_post_json(API_BASE_URL . 'rules/delete', [
        'domain' => $domain
    ]);
}

/**
 * 根据视频自动生成规则
 * @param string $videoUrl 视频 URL
 * @return array
 */
function generateRules($videoUrl) {
    $url = API_BASE_URL . 'rules/generate&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 学习并更新规则
 * @param string $videoUrl 视频 URL
 * @return array
 */
function learnRules($videoUrl) {
    $url = API_BASE_URL . 'rules/learn&url=' . urlencode($videoUrl);
    return api_get($url);
}

// ==================== 资源站管理接口 ====================

/**
 * 获取资源站列表
 * @return array
 */
function getSitesList() {
    return api_get(API_BASE_URL . 'sites/list');
}

/**
 * 在指定资源站搜索视频
 * @param string $keyword 搜索关键词
 * @param string $siteName 资源站名称
 * @param int $page 页码
 * @param int $limit 每页数量
 * @return array
 */
function searchSiteVideos($keyword, $siteName = '', $page = 1, $limit = 20) {
    $url = API_BASE_URL . 'sites/search&keyword=' . urlencode($keyword);
    if (!empty($siteName)) {
        $url .= '&name=' . urlencode($siteName);
    }
    $url .= '&page=' . $page . '&limit=' . $limit;
    return api_get($url);
}

/**
 * 在所有资源站搜索视频
 * @param string $keyword 搜索关键词
 * @param int $maxSites 最大站点数
 * @param int $limitPerSite 每站数量
 * @return array
 */
function searchAllSites($keyword, $maxSites = 5, $limitPerSite = 10) {
    $url = API_BASE_URL . 'sites/search_all&keyword=' . urlencode($keyword)
         . '&max_sites=' . $maxSites . '&limit_per_site=' . $limitPerSite;
    return api_get($url);
}

// ==================== 学习相关接口 ====================

/**
 * 搜索并学习（搜索影视学习一体化）
 * @param string $keyword 搜索关键词
 * @param array $options 选项：
 *   - site_name: 资源站名称，默认 all
 *   - max_sites: 最大站点数，默认 5
 *   - limit_per_site: 每站数量，默认 10
 *   - multi_thread: 是否启用多线程，默认 false
 *   - concurrency: 并发数，默认 5
 *   - min_segments: 最少片段数，默认 50
 *   - max_ad_percentage: 最大广告占比，默认 90
 * @return array
 */
function searchAndLearn($keyword, $options = []) {
    $defaultOptions = [
        'site_name' => 'all',
        'max_sites' => 5,
        'limit_per_site' => 10,
        'multi_thread' => false,
        'concurrency' => 5,
        'min_segments' => 50,
        'max_ad_percentage' => 90,
    ];
    $postData = array_merge($defaultOptions, $options, ['keyword' => $keyword]);
    return api_post_json(API_BASE_URL . 'sites/search_and_learn', $postData);
}

/**
 * 从指定视频 URL 学习规则
 * @param string $videoUrl 视频 URL
 * @param array $options 选项
 * @return array
 */
function learnVideo($videoUrl, $options = []) {
    $postData = array_merge(['url' => $videoUrl], $options);
    return api_post_json(API_BASE_URL . 'sites/learn_video', $postData);
}

/**
 * 批量学习视频
 * @param array $urls 视频 URL 数组
 * @param bool $multiThread 是否启用多线程
 * @param int $concurrency 并发数
 * @return array
 */
function learnBatchVideos($urls, $multiThread = true, $concurrency = 5) {
    return api_post_json(API_BASE_URL . 'sites/learn_batch', [
        'urls' => $urls,
        'multi_thread' => $multiThread,
        'concurrency' => $concurrency
    ]);
}

// ==================== 自动学习接口 ====================

/**
 * 获取自动学习配置
 * @return array
 */
function getAutoLearnConfig() {
    return api_get(API_BASE_URL . 'sites/auto_learn/config');
}

/**
 * 保存自动学习配置
 * @param array $config 配置
 * @return array
 */
function saveAutoLearnConfig($config) {
    return api_post_json(API_BASE_URL . 'sites/auto_learn/config/save', $config);
}

/**
 * 执行自动学习
 * @param bool $multiThread 是否启用多线程
 * @param int $concurrency 并发数
 * @return array
 */
function runAutoLearn($multiThread = true, $concurrency = 5) {
    return api_post_json(API_BASE_URL . 'sites/auto_learn/run', [
        'multi_thread' => $multiThread,
        'concurrency' => $concurrency
    ]);
}

/**
 * 获取自动学习状态
 * @return array
 */
function getAutoLearnStatus() {
    return api_get(API_BASE_URL . 'sites/auto_learn/status');
}

// ==================== 官方替换接口 ====================

/**
 * 官替解析 - 完整结果
 * @param string $videoUrl 官方视频 URL
 * @return array
 */
function officialReplaceResolve($videoUrl) {
    $url = API_BASE_URL . 'official_replace/resolve&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 官替解析 - 精简信息
 * @param string $videoUrl 官方视频 URL
 * @return array
 */
function officialReplaceInfo($videoUrl) {
    $url = API_BASE_URL . 'official_replace/info&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 获取官替配置
 * @return array
 */
function getOfficialReplaceConfig() {
    return api_get(API_BASE_URL . 'official_replace/config');
}

// ==================== 解析接口 ====================

/**
 * 去广告 m3u8 解析
 * @param string $videoUrl m3u8 视频 URL
 * @return string m3u8 内容
 */
function getAdFreeM3u8($videoUrl) {
    $url = API_BASE_URL . 'mxjx&url=' . urlencode($videoUrl);
    $result = api_get($url);
    if ($result['success'] && is_string($result['data'])) {
        return $result['data'];
    }
    return false;
}

/**
 * 去广告解析信息
 * @param string $videoUrl m3u8 视频 URL
 * @return array
 */
function getMxjxInfo($videoUrl) {
    $url = API_BASE_URL . 'mxjx/info&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 虾米解析 - 全网 VIP 视频解析
 * @param string $videoUrl 视频播放页 URL
 * @return array
 */
function xiamiParse($videoUrl) {
    $url = API_BASE_URL . 'xiami_jx&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 虾米解析详情
 * @param string $videoUrl 视频播放页 URL
 * @return array
 */
function xiamiParseInfo($videoUrl) {
    $url = API_BASE_URL . 'xiami_jx/info&url=' . urlencode($videoUrl);
    return api_get($url);
}

/**
 * 沫兮 API 解析
 * @param string $videoUrl 视频 URL
 * @return array
 */
function moxiApi($videoUrl) {
    $url = API_BASE_URL . 'moxi&url=' . urlencode($videoUrl);
    return api_get($url);
}

// ==================== 系统更新接口 ====================

/**
 * 获取当前版本
 * @return array
 */
function getCurrentVersion() {
    return api_get(API_BASE_URL . 'update/version');
}

/**
 * 检查更新
 * @return array
 */
function checkUpdate() {
    return api_get(API_BASE_URL . 'update/check');
}

/**
 * 获取系统信息
 * @return array
 */
function getSystemInfo() {
    return api_get(API_BASE_URL . 'update/system_info');
}

// ==================== 数据库接口 ====================

/**
 * 获取数据库状态
 * @return array
 */
function getDbStatus() {
    return api_get(API_BASE_URL . 'db/status');
}

/**
 * 保存数据库配置
 * @param array $config 数据库配置
 * @return array
 */
function saveDbConfig($config) {
    return api_post_json(API_BASE_URL . 'db/config/save', $config);
}

/**
 * 测试数据库连接
 * @param array $config 数据库配置
 * @return array
 */
function testDbConnection($config = []) {
    return api_post_json(API_BASE_URL . 'db/test_connection', $config);
}

// ==================== 其他接口 ====================

/**
 * 获取系统信息
 * @return array
 */
function getInfo() {
    return api_get(API_BASE_URL . 'info');
}

/**
 * 获取版本信息
 * @return array
 */
function getVersion() {
    return api_get(API_BASE_URL . 'version');
}

/**
 * 获取播放器配置
 * @return array
 */
function getPlayerConfig() {
    return api_get(API_BASE_URL . 'player/config');
}

// ==================== 使用示例 ====================

// 以下是使用示例，取消注释即可测试

/*
// 示例 1: 分析视频
$result = analyzeVideo('https://example.com/video.m3u8');
if ($result['success']) {
    echo "分析成功，广告占比: " . ($result['data']['ad_percentage'] ?? '未知') . "%\n";
} else {
    echo "分析失败: " . $result['error'] . "\n";
}

// 示例 2: 搜索视频
$result = searchAllSites('庆余年', 3, 5);
if ($result['success']) {
    echo "找到 " . ($result['data']['total_videos'] ?? 0) . " 个视频\n";
}

// 示例 3: 搜索并学习（多线程）
$result = searchAndLearn('庆余年', [
    'site_name' => 'all',
    'max_sites' => 3,
    'limit_per_site' => 5,
    'multi_thread' => true,
    'concurrency' => 5
]);
if ($result['success']) {
    echo "学习完成: 成功 " . ($result['data']['total_learned'] ?? 0) 
         . " 个，失败 " . ($result['data']['total_failed'] ?? 0) . " 个\n";
}

// 示例 4: 虾米解析
$result = xiamiParse('https://v.youku.com/v_show/id_xxx.html');
if ($result['success'] && !empty($result['data']['success'])) {
    echo "解析成功，播放地址: " . $result['data']['data']['media_url'] . "\n";
}

// 示例 5: 执行自动学习
$result = runAutoLearn(true, 5);
if ($result['success']) {
    echo "自动学习完成\n";
}

// 示例 6: 获取去广告 m3u8
$m3u8Content = getAdFreeM3u8('https://example.com/video.m3u8');
if ($m3u8Content) {
    file_put_contents('ad_free.m3u8', $m3u8Content);
    echo "去广告 m3u8 已保存\n";
}
*/

// ==================== CLI 测试模式 ====================

// 如果直接在命令行运行此文件，显示使用说明
if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    echo "╔══════════════════════════════════════════════════════════╗\n";
    echo "║     M3U8 广告分析系统 - PHP 接口调用示例库               ║\n";
    echo "╠══════════════════════════════════════════════════════════╣\n";
    echo "║                                                          ║\n";
    echo "║  使用方法:                                               ║\n";
    echo "║  1. 在你的项目中引入此文件                                ║\n";
    echo "║     require_once 'api_helper.php';                       ║\n";
    echo "║                                                          ║\n";
    echo "║  2. 修改 API_BASE_URL 为你的实际地址                     ║\n";
    echo "║                                                          ║\n";
    echo "║  3. 调用对应的函数即可                                    ║\n";
    echo "║                                                          ║\n";
    echo "║  可用函数分类:                                           ║\n";
    echo "║  🎬 视频分析: analyzeVideo()                             ║\n";
    echo "║  📋 规则管理: getRulesList(), saveDomainRules()...      ║\n";
    echo "║  📺 资源站: searchSiteVideos(), searchAllSites()...     ║\n";
    echo "║  📖 学习相关: searchAndLearn(), learnVideo()...         ║\n";
    echo "║  🤖 自动学习: runAutoLearn(), getAutoLearnStatus()...   ║\n";
    echo "║  🔄 官方替换: officialReplaceResolve()...               ║\n";
    echo "║  🔗 解析接口: xiamiParse(), moxiApi(), getAdFreeM3u8()  ║\n";
    echo "║  🔧 系统更新: getCurrentVersion(), checkUpdate()...     ║\n";
    echo "║  🗄️  数据库: getDbStatus(), testDbConnection()...       ║\n";
    echo "║                                                          ║\n";
    echo "║  通用请求:                                               ║\n";
    echo "║  - api_get($url)                    GET 请求             ║\n";
    echo "║  - api_post_json($url, $data)       POST JSON           ║\n";
    echo "║  - api_post_form($url, $data)       POST 表单           ║\n";
    echo "║                                                          ║\n";
    echo "╚══════════════════════════════════════════════════════════╝\n";
    echo "\n当前 API 地址: " . API_BASE_URL . "\n";
    echo "请修改文件顶部的 API_BASE_URL 为你的实际地址。\n";
}
