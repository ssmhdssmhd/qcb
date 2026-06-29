<?php
/**
 * 自动学习定时任务脚本
 * 
 * 功能：定时从资源站采集视频，自动学习广告规则
 * 使用方式：
 *   1. 通过 cron 定时任务执行：php /path/to/cron_autolearn.php
 *   2. 通过浏览器访问：http://你的域名/cron_autolearn.php?key=你的密钥
 * 
 * 注意：建议设置访问密钥，防止恶意调用
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

header('Content-Type: application/json; charset=utf-8');

$startTime = microtime(true);

$config = [
    'access_key' => '',
    'log_file' => __DIR__ . '/gz/autolearn_logs.php',
    'max_log_entries' => 100,
    'lock_file' => __DIR__ . '/gz/autolearn_lock.tmp',
    'lock_timeout' => 600,
];

function sendResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function writeLog($message, $type = 'info') {
    global $config;
    
    $logEntry = [
        'time' => date('Y-m-d H:i:s'),
        'type' => $type,
        'message' => $message
    ];
    
    $logs = [];
    if (file_exists($config['log_file'])) {
        $logs = require $config['log_file'];
        if (!is_array($logs)) $logs = [];
    }
    
    array_unshift($logs, $logEntry);
    $logs = array_slice($logs, 0, $config['max_log_entries']);
    
    $content = '<?php' . "\n";
    $content .= '// 自动学习日志 - 自动生成' . "\n";
    $content .= 'return ' . var_export($logs, true) . ';' . "\n";
    
    @file_put_contents($config['log_file'], $content);
}

function acquireLock() {
    global $config;
    
    $lockFile = $config['lock_file'];
    
    if (file_exists($lockFile)) {
        $lockTime = filemtime($lockFile);
        if ((time() - $lockTime) < $config['lock_timeout']) {
            return false;
        }
        @unlink($lockFile);
    }
    
    @file_put_contents($lockFile, date('Y-m-d H:i:s'));
    return true;
}

function releaseLock() {
    global $config;
    @unlink($config['lock_file']);
}

function checkAccessKey() {
    global $config;
    
    if (empty($config['access_key'])) {
        return true;
    }
    
    $providedKey = $_GET['key'] ?? $_POST['key'] ?? '';
    return $providedKey === $config['access_key'];
}

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (!checkAccessKey()) {
        writeLog('访问密钥验证失败', 'error');
        sendResponse([
            'success' => false,
            'message' => '访问密钥错误',
            'code' => 403
        ], 403);
    }
}

if (!acquireLock()) {
    writeLog('任务执行被跳过：上次任务尚未完成', 'warning');
    sendResponse([
        'success' => false,
        'message' => '自动学习任务正在执行中，请稍后再试',
        'code' => 429
    ], 429);
}

try {
    require_once __DIR__ . '/gz/ResourceSiteManager.php';
    require_once __DIR__ . '/gz/DomainRuleManager.php';
    
    $siteManager = new ResourceSiteManager();
    $ruleManager = new DomainRuleManager();
    
    $autoLearnConfig = $siteManager->getAutoLearnConfig();
    
    if (empty($autoLearnConfig['enabled'])) {
        releaseLock();
        writeLog('任务执行被跳过：自动学习未启用', 'warning');
        sendResponse([
            'success' => true,
            'message' => '自动学习未启用，任务跳过',
            'skipped' => true,
            'code' => 200
        ]);
    }
    
    $shouldLearn = $siteManager->shouldAutoLearn();
    
    $forceRun = isset($_GET['force']) && $_GET['force'] === '1';
    if (!$isCli && !$forceRun && !$shouldLearn) {
        releaseLock();
        sendResponse([
            'success' => true,
            'message' => '未到自动学习时间，任务跳过',
            'skipped' => true,
            'last_learn_time' => $siteManager->getLastLearnTime(),
            'interval_days' => $autoLearnConfig['interval_days'] ?? 3,
            'code' => 200
        ]);
    }
    
    if ($isCli && !$shouldLearn && !$forceRun) {
        $forceRun = (isset($argv[1]) && $argv[1] === 'force');
    }
    
    if (!$forceRun && !$shouldLearn && $isCli) {
        releaseLock();
        writeLog('任务执行被跳过：未到学习间隔时间', 'info');
        echo "自动学习未到执行时间，跳过。\n";
        exit(0);
    }
    
    writeLog('自动学习任务开始执行', 'info');
    
    $options = [];
    if (!empty($_GET['keyword'])) {
        $options['keyword'] = $_GET['keyword'];
    }
    if (!empty($_GET['max_sites'])) {
        $options['max_sites'] = intval($_GET['max_sites']);
    }
    if (!empty($_GET['videos_per_site'])) {
        $options['videos_per_site'] = intval($_GET['videos_per_site']);
    }
    
    if ($isCli && isset($argv)) {
        foreach ($argv as $arg) {
            if (strpos($arg, 'keyword=') === 0) {
                $options['keyword'] = substr($arg, 8);
            }
            if (strpos($arg, 'max_sites=') === 0) {
                $options['max_sites'] = intval(substr($arg, 10));
            }
            if (strpos($arg, 'videos_per_site=') === 0) {
                $options['videos_per_site'] = intval(substr($arg, 16));
            }
        }
    }
    
    $result = $siteManager->runAutoLearn($ruleManager, $options);
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    $logMessage = sprintf(
        '自动学习完成：处理 %d 个站点，学习成功 %d 个，失败 %d 个，耗时 %d 秒',
        $result['sites_processed'] ?? 0,
        $result['total_learned'] ?? 0,
        $result['total_failed'] ?? 0,
        $duration
    );
    writeLog($logMessage, $result['success'] ? 'info' : 'error');
    
    releaseLock();
    
    $response = array_merge($result, [
        'duration_seconds' => $duration,
        'code' => $result['success'] ? 200 : 500
    ]);
    
    if ($isCli) {
        echo "========================================\n";
        echo "  自动学习任务执行结果\n";
        echo "========================================\n";
        echo "状态: " . ($result['success'] ? '成功' : '失败') . "\n";
        echo "消息: " . ($result['message'] ?? '') . "\n";
        if (!empty($options['keyword'])) {
            echo "关键词: " . $options['keyword'] . "\n";
        }
        echo "处理站点: " . ($result['sites_processed'] ?? 0) . " 个\n";
        echo "学习成功: " . ($result['total_learned'] ?? 0) . " 个\n";
        echo "学习失败: " . ($result['total_failed'] ?? 0) . " 个\n";
        echo "执行耗时: " . $duration . " 秒\n";
        echo "========================================\n";
        exit($result['success'] ? 0 : 1);
    }
    
    sendResponse($response, $result['success'] ? 200 : 500);
    
} catch (Exception $e) {
    releaseLock();
    writeLog('任务执行异常：' . $e->getMessage(), 'error');
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    if ($isCli) {
        echo "========================================\n";
        echo "  自动学习任务执行异常\n";
        echo "========================================\n";
        echo "错误: " . $e->getMessage() . "\n";
        echo "文件: " . $e->getFile() . ":" . $e->getLine() . "\n";
        echo "执行耗时: " . $duration . " 秒\n";
        echo "========================================\n";
        exit(1);
    }
    
    sendResponse([
        'success' => false,
        'message' => $e->getMessage(),
        'duration_seconds' => $duration,
        'code' => 500
    ], 500);
}
