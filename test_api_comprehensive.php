<?php
/**
 * 全面 API 接口测试脚本
 * 
 * 功能：
 * 1. 连接测试（检查接口是否可访问）
 * 2. 返回结果内容验证
 * 3. 统计成功率与失败项
 */

$apiBase = 'http://localhost:8888/mx.php?action=';
$testM3u8Url = 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/index.m3u8';
$testM3u8Url2 = 'https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8';

$totalTests = 0;
$passed = 0;
$failed = 0;
$results = [];

function testApi($name, $action, $params = [], $post = null, $options = []) {
    global $apiBase, $totalTests, $passed, $failed, $results;
    $totalTests++;

    $url = $apiBase . $action;
    if (!empty($params)) {
        foreach ($params as $k => $v) {
            $url .= '&' . urlencode($k) . '=' . urlencode($v);
        }
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout'] ?? 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $headers = $options['headers'] ?? [];
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        $headers[] = 'Content-Type: application/json';
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $startTime = microtime(true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    $duration = round((microtime(true) - $startTime) * 1000, 0);

    $result = [
        'name' => $name,
        'action' => $action,
        'http_code' => $httpCode,
        'duration_ms' => $duration,
        'content_type' => $contentType,
    ];

    if ($error) {
        $result['status'] = 'fail';
        $result['error'] = 'CURL错误: ' . $error;
        $failed++;
        $results[] = $result;
        return false;
    }

    $isM3u8 = strpos($contentType, 'mpegurl') !== false;
    $data = null;

    if ($isM3u8) {
        $isValidM3u8 = strpos($response, '#EXTM3U') === 0 || strpos(trim($response), '#EXTM3U') !== false;
        $result['is_m3u8'] = $isValidM3u8;
        $result['response_length'] = strlen($response);
        $result['response_preview'] = substr($response, 0, 200);
        if ($isValidM3u8) {
            $result['status'] = 'pass';
            $passed++;
        } else {
            $result['status'] = 'fail';
            $result['error'] = 'M3U8格式无效';
            $failed++;
        }
    } else {
        $data = json_decode($response, true);
        if ($data === null) {
            $result['status'] = 'fail';
            $result['error'] = 'JSON解析失败';
            $result['response_preview'] = substr($response, 0, 300);
            $failed++;
            $results[] = $result;
            return false;
        }

        $result['has_success'] = isset($data['success']);
        $result['success_value'] = $data['success'] ?? null;

        if ($httpCode >= 400) {
            $result['status'] = 'fail';
            $result['error'] = 'HTTP ' . $httpCode . ': ' . ($data['message'] ?? '未知错误');
            $result['response_preview'] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $failed++;
        } elseif (isset($data['success']) && $data['success'] === false) {
            $result['status'] = 'fail';
            $result['error'] = $data['message'] ?? '操作失败';
            $result['response_preview'] = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            $failed++;
        } else {
            $result['status'] = 'pass';
            $result['data_keys'] = array_keys($data);
            $passed++;
        }
    }

    $results[] = $result;
    return $result['status'] === 'pass';
}

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           M3U8 广告分析系统 - API 接口全面测试              ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

echo "测试地址: $apiBase\n";
echo "测试时间: " . date('Y-m-d H:i:s') . "\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  一、系统信息接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('info 系统信息', 'info');
testApi('version 版本信息', 'version');
testApi('db/status 数据库状态', 'db/status');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  二、规则管理接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('rules/list 规则列表', 'rules/list');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  三、资源站管理接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('sites/list 资源站列表', 'sites/list');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  四、官推站点接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('official/list 官推站点列表', 'official/list');
testApi('official_sites/list 官推站点列表(v2)', 'official_sites/list');
testApi('official_sites/status 官推站点状态', 'official_sites/status');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  五、官替接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('official/platforms 官替平台列表', 'official/platforms');
testApi('official_replace/config 官替配置', 'official_replace/config');
testApi('official_replace/platforms 官替平台列表(v2)', 'official_replace/platforms');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  六、代理接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('proxies/list 代理列表', 'proxies/list');
testApi('proxy/list 代理列表(v2)', 'proxy/list');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  七、更新管理接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('update/version 更新版本', 'update/version');
testApi('update/backup/list 备份列表', 'update/backup/list');
testApi('update/system_info 系统信息', 'update/system_info');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  八、授权接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('auth/info 授权信息', 'auth/info');
testApi('auth/validate 授权验证', 'auth/validate');
testApi('auth/config/get 授权配置', 'auth/config/get');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  九、播放器配置接口\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('player/config 播放器配置', 'player/config');

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  十、视频分析接口（需联网）\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "测试视频: $testM3u8Url\n\n";

testApi('moxi 沫兮API', 'moxi', ['url' => $testM3u8Url], null, ['timeout' => 30]);
testApi('mxjx/info 去广告信息', 'mxjx/info', ['url' => $testM3u8Url], null, ['timeout' => 30]);
testApi('analyze 分析接口', 'analyze', ['url' => $testM3u8Url], null, ['timeout' => 60]);

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  十一、M3U8 输出接口（需联网）\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

testApi('mxjx 去广告M3U8输出', 'mxjx', ['url' => $testM3u8Url], null, ['timeout' => 30]);

echo "\n══════════════════════════════════════════════════════════════\n";
echo "  测试结果汇总\n";
echo "══════════════════════════════════════════════════════════════\n\n";

echo "总测试数: $totalTests\n";
echo "通过:     $passed\n";
echo "失败:     $failed\n";
echo "成功率:   " . ($totalTests > 0 ? round($passed / $totalTests * 100, 1) : 0) . "%\n\n";

if ($failed > 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  失败的接口:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $i = 1;
    foreach ($results as $r) {
        if ($r['status'] === 'fail') {
            echo "  " . $i . ". [" . $r['action'] . "] " . $r['name'] . "\n";
            echo "     错误: " . ($r['error'] ?? '未知') . "\n";
            echo "     HTTP: " . $r['http_code'] . " | 耗时: " . $r['duration_ms'] . "ms\n";
            if (!empty($r['response_preview'])) {
                echo "     响应: " . substr($r['response_preview'], 0, 200) . "\n";
            }
            echo "\n";
            $i++;
        }
    }
}

echo "══════════════════════════════════════════════════════════════\n";
