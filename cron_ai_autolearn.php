<?php
/**
 * AI 自动学习定时任务脚本
 *
 * 功能：每隔几小时自动从指定资源站（默认如意）获取热门/更新视频，
 *       提取 rym3u8 地址进行深度广告分析并更新规则
 *
 * 使用方式：
 *   1. Cron 定时任务：  php /path/to/cron_ai_autolearn.php
 *      推荐 crontab (每4小时)：0 0,4,8,12,16,20 * * * php /path/to/cron_ai_autolearn.php
 *   2. 浏览器访问：     http://你的域名/cron_ai_autolearn.php?key=你的密钥
 *   3. 强制执行：       php cron_ai_autolearn.php force   或   ?key=xxx&force=1
 *
 * 配置文件：gz/ai_auto_learn_config.php（后台 → AI自动学习 页面维护）
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '384M');
ini_set('max_execution_time', 300);

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
}

$startTime = microtime(true);

function sendAiResponse($data, $httpCode = 200) {
    global $isCli;
    if ($isCli) {
        echo "========================================\n";
        echo "  AI 自动学习任务执行结果\n";
        echo "========================================\n";
        echo "状态: " . ($data['success'] ? '成功' : '失败') . "\n";
        echo "消息: " . ($data['message'] ?? '') . "\n";
        if (isset($data['sites_processed'])) echo "处理站点: " . $data['sites_processed'] . " 个\n";
        if (isset($data['total_learned'])) echo "学习成功: " . $data['total_learned'] . " 个\n";
        if (isset($data['total_failed'])) echo "学习失败: " . $data['total_failed'] . " 个\n";
        if (isset($data['total_skipped'])) echo "去重跳过: " . $data['total_skipped'] . " 个\n";
        if (isset($data['duration_seconds'])) echo "执行耗时: " . $data['duration_seconds'] . " 秒\n";
        if (!empty($data['learned_domains'])) echo "更新域名: " . implode(', ', $data['learned_domains']) . "\n";
        echo "========================================\n";
        exit($data['success'] ? 0 : 1);
    }
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 锁文件防止重复执行
$lockFile = __DIR__ . '/gz/ai_autolearn_lock.tmp';
$lockTimeout = 600;

if (file_exists($lockFile)) {
    $lockTime = filemtime($lockFile);
    if ((time() - $lockTime) < $lockTimeout) {
        if ($isCli) {
            echo "AI 自动学习任务正在执行中，请稍后再试。\n";
        }
        sendAiResponse([
            'success' => false,
            'message' => 'AI 自动学习任务正在执行中，请稍后再试',
            'code' => 429,
        ], 429);
    }
    @unlink($lockFile);
}
@file_put_contents($lockFile, date('Y-m-d H:i:s'));

function releaseAiLock() {
    global $lockFile;
    @unlink($lockFile);
}

try {
    require_once __DIR__ . '/gz/AiAutoLearner.php';

    $learner = new AiAutoLearner();
    $config = $learner->getConfig();

    // 访问密钥校验
    if (!$isCli) {
        $accessKey = $config['access_key'] ?? '';
        if (!empty($accessKey)) {
            $provided = $_GET['key'] ?? $_POST['key'] ?? '';
            if ($provided !== $accessKey) {
                releaseAiLock();
                $learner->writeLog('访问密钥验证失败', 'error');
                sendAiResponse([
                    'success' => false,
                    'message' => '访问密钥错误',
                    'code' => 403,
                ], 403);
            }
        }
    }

    // 判断是否到执行时间
    $forceRun = false;
    if ($isCli) {
        $forceRun = isset($argv[1]) && $argv[1] === 'force';
    } else {
        $forceRun = isset($_GET['force']) && $_GET['force'] === '1';
    }

    if (!$forceRun && !$learner->shouldRun()) {
        releaseAiLock();
        $status = $learner->getStatus();
        $msg = '未到 AI 自动学习执行时间，任务跳过';
        if (!$isCli) {
            $learner->writeLog($msg, 'info');
        }
        sendAiResponse([
            'success' => true,
            'message' => $msg,
            'skipped' => true,
            'last_run_time' => $status['last_run_time'],
            'interval_hours' => $status['interval_hours'],
            'code' => 200,
        ]);
    }

    if (empty($config['enabled'])) {
        releaseAiLock();
        sendAiResponse([
            'success' => true,
            'message' => 'AI 自动学习未启用，任务跳过',
            'skipped' => true,
            'code' => 200,
        ]);
    }

    $learner->writeLog('AI 自动学习任务开始执行', 'info');

    $options = [];
    if (!$isCli) {
        if (isset($_GET['max_sites'])) $options['max_sites'] = intval($_GET['max_sites']);
        if (isset($_GET['videos_per_site'])) $options['videos_per_site'] = intval($_GET['videos_per_site']);
    }
    if ($isCli && isset($argv)) {
        foreach ($argv as $arg) {
            if (strpos($arg, 'max_sites=') === 0) $options['max_sites'] = intval(substr($arg, 10));
            if (strpos($arg, 'videos_per_site=') === 0) $options['videos_per_site'] = intval(substr($arg, 16));
        }
    }

    $result = $learner->run($options);
    releaseAiLock();

    $result['code'] = $result['success'] ? 200 : 500;
    sendAiResponse($result, $result['success'] ? 200 : 500);

} catch (Exception $e) {
    releaseAiLock();
    $duration = round(microtime(true) - $startTime, 2);
    if (isset($learner)) {
        $learner->writeLog('任务执行异常：' . $e->getMessage(), 'error');
    }
    sendAiResponse([
        'success' => false,
        'message' => $e->getMessage(),
        'duration_seconds' => $duration,
        'code' => 500,
    ], 500);
}
