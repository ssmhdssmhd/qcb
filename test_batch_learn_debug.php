<?php
/**
 * 测试多线程批量学习
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/multi_thread/TaskRunner.php';

// 测试：调用一个公开的 API 来验证 curl_multi 是否正常工作
$testTasks = [
    ['id' => 0, 'url' => 'https://httpbin.org/post', 'post_data' => ['url' => 'https://example.com/video1.m3u8']],
    ['id' => 1, 'url' => 'https://httpbin.org/post', 'post_data' => ['url' => 'https://example.com/video2.m3u8']],
    ['id' => 2, 'url' => 'https://httpbin.org/post', 'post_data' => ['url' => 'https://example.com/video3.m3u8']],
];

echo "=== 测试 1: curl_multi POST JSON 请求 ===\n";
$runner = TaskRunner::create([
    'concurrency' => 3,
    'mode' => TaskRunner::MODE_CURL_MULTI,
    'timeout' => 30
]);

$startTime = microtime(true);
$results = $runner->run($testTasks, '{url}');
$totalTime = round((microtime(true) - $startTime) * 1000, 2);

echo "总耗时: {$totalTime}ms\n";
echo "实际模式: " . $runner->getActualMode() . "\n";
echo "结果数量: " . count($results) . "\n\n";

foreach ($results as $i => $result) {
    echo "任务 $i: " . ($result->success ? '成功' : '失败') . "\n";
    if ($result->success) {
        if (is_array($result->data)) {
            echo "  数据类型: array\n";
            echo "  url 字段: " . ($result->data['url'] ?? 'N/A') . "\n";
        } else {
            echo "  数据长度: " . strlen($result->data) . " 字节\n";
            echo "  前 200 字符: " . substr($result->data, 0, 200) . "\n";
        }
    } else {
        echo "  错误: " . $result->error . "\n";
    }
    echo "  耗时: {$result->duration}ms\n\n";
}

echo "\n=== 测试 2: 检查 CurlMultiTaskRunner 的 buildUrl 逻辑 ===\n";

// 用反射测试 buildUrl 方法
$reflection = new ReflectionClass('CurlMultiTaskRunner');
$method = $reflection->getMethod('buildUrl');
$method->setAccessible(true);

$runner2 = new CurlMultiTaskRunner();

// 测试 1: 完整 URL 模板
$testTask1 = ['id' => 0, 'url' => 'https://example.com/test.m3u8', 'post_data' => ['url' => 'test']];
$url1 = $method->invoke($runner2, 'http://localhost/mx.php?action=sites/learn_video', $testTask1);
echo "测试 1 - 完整 URL 模板:\n";
echo "  模板: http://localhost/mx.php?action=sites/learn_video\n";
echo "  结果: $url1\n";
echo "  预期: http://localhost/mx.php?action=sites/learn_video\n";
echo "  匹配: " . ($url1 === 'http://localhost/mx.php?action=sites/learn_video' ? '✓' : '✗') . "\n\n";

// 测试 2: {url} 模板
$testTask2 = ['id' => 1, 'url' => 'https://example.com/test.m3u8'];
$url2 = $method->invoke($runner2, '{url}', $testTask2);
echo "测试 2 - {url} 模板:\n";
echo "  模板: {url}\n";
echo "  结果: $url2\n";
echo "  预期: https://example.com/test.m3u8\n";
echo "  匹配: " . ($url2 === 'https://example.com/test.m3u8' ? '✓' : '✗') . "\n";

echo "\n测试完成！\n";
