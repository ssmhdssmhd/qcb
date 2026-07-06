<?php
/**
 * 多线程/多进程任务执行器 - 使用示例
 *
 * 支持三种模式:
 * - serial: 串行执行（兼容模式）
 * - curl_multi: 并发 HTTP 请求（Web 环境推荐）
 * - process: pcntl_fork 多进程（CLI 环境可用）
 */

require_once __DIR__ . '/autoload.php';

echo "========================================\n";
echo "  TaskRunner 使用示例\n";
echo "========================================\n\n";

echo "📊 当前环境:\n";
echo "   PHP SAPI: " . PHP_SAPI . "\n";
echo "   多线程可用: " . (TaskRunner::isMultiThreadAvailable() ? '是' : '否') . "\n";
echo "   推荐模式: " . TaskRunner::getRecommendedMode() . "\n";
echo "   可用模式: " . implode(', ', TaskRunner::getAvailableModes()) . "\n\n";

echo "========================================\n";
echo "  示例 1: 基本用法 - 串行模式\n";
echo "========================================\n";

$tasks = [
    ['id' => 0, 'name' => '任务1', 'data' => 'hello'],
    ['id' => 1, 'name' => '任务2', 'data' => 'world'],
    ['id' => 2, 'name' => '任务3', 'data' => 'foo'],
];

$runner = TaskRunner::create([
    'mode' => TaskRunner::MODE_SERIAL,
    'concurrency' => 3
]);

echo "模式: " . $runner->getActualMode() . "\n";

$start = microtime(true);
$results = $runner->run($tasks, function($task) {
    usleep(100000);
    return strtoupper($task['data']);
});
$elapsed = round((microtime(true) - $start) * 1000, 2);

echo "耗时: {$elapsed} ms\n";
echo "结果:\n";
foreach ($results as $r) {
    echo "  任务 {$r->taskId}: " . ($r->success ? $r->data : '失败: ' . $r->error) . " ({$r->duration}ms)\n";
}
echo "\n";

echo "========================================\n";
echo "  示例 2: curl_multi 并发 HTTP 请求\n";
echo "========================================\n";

if (CurlMultiTaskRunner::isAvailable()) {
    $runner2 = TaskRunner::create([
        'mode' => TaskRunner::MODE_CURL_MULTI,
        'concurrency' => 3,
        'timeout' => 15
    ]);

    echo "模式: " . $runner2->getActualMode() . "\n";

    $httpTasks = [
        ['id' => 0, 'url' => 'https://httpbin.org/get?num=1'],
        ['id' => 1, 'url' => 'https://httpbin.org/get?num=2'],
        ['id' => 2, 'url' => 'https://httpbin.org/get?num=3'],
    ];

    $start = microtime(true);
    $results2 = $runner2->run($httpTasks, function($task) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $task['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            return [
                'http_code' => $httpCode,
                'args' => $data['args'] ?? null
            ];
        }
        throw new Exception("HTTP $httpCode");
    });
    $elapsed = round((microtime(true) - $start) * 1000, 2);

    echo "耗时: {$elapsed} ms\n";
    echo "结果:\n";
    foreach ($results2 as $r) {
        if ($r->success) {
            echo "  任务 {$r->taskId}: 成功 - HTTP {$r->data['http_code']} ({$r->duration}ms)\n";
        } else {
            echo "  任务 {$r->taskId}: 失败 - {$r->error} ({$r->duration}ms)\n";
        }
    }
} else {
    echo "跳过: curl_multi 不可用\n";
}
echo "\n";

echo "========================================\n";
echo "  示例 3: 自动选择最佳模式\n";
echo "========================================\n";

$runner3 = TaskRunner::create([
    'concurrency' => 5,
    'timeout' => 30
]);

echo "请求模式: auto\n";
echo "实际模式: " . $runner3->getActualMode() . "\n";
echo "并发数: 5\n\n";

echo "✅ 所有示例完成！\n";
echo "\n使用方法:\n";
echo "  \$runner = TaskRunner::create(['concurrency' => 5, 'mode' => 'auto']);\n";
echo "  \$results = \$runner->run(\$tasks, function(\$task) {\n";
echo "      // 处理任务\n";
echo "      return \$result;\n";
echo "  });\n";
