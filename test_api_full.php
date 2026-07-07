<?php
/**
 * 更全面的 API 接口测试 - 包含更多接口
 */

$apiBase = 'http://localhost:8888/mx.php?action=';
$testM3u8Url = 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/index.m3u8';

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
    ];

    $isM3u8 = strpos($contentType, 'mpegurl') !== false;
    $status = 'pass';
    $errorMsg = '';

    if ($error) {
        $status = 'fail';
        $errorMsg = 'CURL: ' . $error;
    } elseif ($isM3u8) {
        if (strpos($response, '#EXTM3U') === false && strpos(trim($response), '#EXTM3U') === false) {
            $status = 'fail';
            $errorMsg = 'M3U8格式无效';
        }
        $result['response_len'] = strlen($response);
    } else {
        $data = json_decode($response, true);
        if ($data === null) {
            $status = 'fail';
            $errorMsg = 'JSON解析失败: ' . substr($response, 0, 100);
        } elseif ($httpCode >= 400) {
            $status = 'fail';
            $errorMsg = 'HTTP ' . $httpCode . ': ' . ($data['message'] ?? '');
        } elseif (isset($data['success']) && $data['success'] === false) {
            $status = 'fail';
            $errorMsg = $data['message'] ?? '操作失败';
        } else {
            $result['keys'] = implode(', ', array_keys($data));
        }
    }

    $result['status'] = $status;
    $result['error'] = $errorMsg;

    if ($status === 'pass') {
        $passed++;
    } else {
        $failed++;
    }
    $results[] = $result;

    $icon = $status === 'pass' ? '✓' : '✗';
    printf("  %s %-45s %3dms  %s\n", $icon, $name, $duration, $status === 'pass' ? '' : $errorMsg);

    return $status === 'pass';
}

echo "╔═══════════════════════════════════════════════════════════════════╗\n";
echo "║     M3U8 广告分析系统 - API 接口全面测试 (扩展版)                ║\n";
echo "╚═══════════════════════════════════════════════════════════════════╝\n\n";

echo "【 1. 系统信息类 】\n";
testApi('info 系统信息', 'info');
testApi('version 版本信息', 'version');
testApi('db/status 数据库状态', 'db/status');
testApi('db/init 数据库初始化', 'db/init');

echo "\n【 2. 规则管理类 】\n";
testApi('rules/list 规则列表', 'rules/list');
testApi('rules/get (无domain)', 'rules/get', ['domain' => '']);

echo "\n【 3. 资源站管理类 】\n";
testApi('sites/list 资源站列表', 'sites/list');
testApi('sites/auto_learn/config 自动学习配置', 'sites/auto_learn/config');

echo "\n【 4. 官推站点类 】\n";
testApi('official/list 官推站点(旧)', 'official/list');
testApi('official_sites/list 官推站点', 'official_sites/list');
testApi('official_sites/status 官推状态', 'official_sites/status');

echo "\n【 5. 官替类 】\n";
testApi('official/platforms 官替平台(旧)', 'official/platforms');
testApi('official_replace/config 官替配置', 'official_replace/config');
testApi('official_replace/platforms 官替平台', 'official_replace/platforms');

echo "\n【 6. 代理类 】\n";
testApi('proxies/list 代理列表(旧)', 'proxies/list');
testApi('proxy/list 代理列表', 'proxy/list');

echo "\n【 7. 更新管理类 】\n";
testApi('update/version 更新版本', 'update/version');
testApi('update/backup/list 备份列表', 'update/backup/list');
testApi('update/system_info 系统信息', 'update/system_info');
testApi('update/clear_cache 清理缓存', 'update/clear_cache');

echo "\n【 8. 授权类 】\n";
testApi('auth/info 授权信息', 'auth/info');
testApi('auth/validate 授权验证', 'auth/validate');
testApi('auth/config/get 授权配置', 'auth/config/get');

echo "\n【 9. 播放器类 】\n";
testApi('player/config 播放器配置', 'player/config');

echo "\n【 10. 视频分析类 (需联网) 】\n";
echo "   测试视频: $testM3u8Url\n";
testApi('moxi 沫兮API', 'moxi', ['url' => $testM3u8Url], null, ['timeout' => 30]);
testApi('mxjx/info 去广告信息', 'mxjx/info', ['url' => $testM3u8Url], null, ['timeout' => 30]);
testApi('analyze 视频分析', 'analyze', ['url' => $testM3u8Url], null, ['timeout' => 60]);

echo "\n【 11. M3U8 输出类 (需联网) 】\n";
testApi('mxjx 去广告M3U8', 'mxjx', ['url' => $testM3u8Url], null, ['timeout' => 30]);

echo "\n【 12. 官替解析类 (需联网) 】\n";
testApi('official_replace/info (测试URL)', 'official_replace/info', ['url' => 'https://v.qq.com/x/cover/mzc00200mp8vo9b.html'], null, ['timeout' => 15]);

echo "\n═══════════════════════════════════════════════════════════════════\n";
printf("  总计: %d  |  通过: %d  |  失败: %d  |  成功率: %.1f%%\n",
    $totalTests, $passed, $failed,
    $totalTests > 0 ? $passed / $totalTests * 100 : 0);
echo "═══════════════════════════════════════════════════════════════════\n";

if ($failed > 0) {
    echo "\n失败详情:\n";
    $i = 1;
    foreach ($results as $r) {
        if ($r['status'] === 'fail') {
            echo "  $i. [{$r['action']}] {$r['name']}\n";
            echo "     错误: {$r['error']}\n";
            echo "     HTTP: {$r['http_code']} | 耗时: {$r['duration_ms']}ms\n\n";
            $i++;
        }
    }
}
