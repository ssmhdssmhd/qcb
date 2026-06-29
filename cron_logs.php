<?php
/**
 * 自动学习日志查看
 * 
 * 功能：查看自动学习任务的执行日志
 * 使用方式：
 *   1. 浏览器访问：http://你的域名/cron_logs.php?key=你的密钥
 *   2. 命令行执行：php /path/to/cron_logs.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: text/html; charset=utf-8');

$config = [
    'access_key' => '',
    'log_file' => __DIR__ . '/gz/autolearn_logs.php',
];

$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    if (!empty($config['access_key'])) {
        $providedKey = $_GET['key'] ?? '';
        if ($providedKey !== $config['access_key']) {
            http_response_code(403);
            echo '访问密钥错误';
            exit;
        }
    }
}

$logs = [];
if (file_exists($config['log_file'])) {
    $logs = require $config['log_file'];
    if (!is_array($logs)) $logs = [];
}

$lastLearnTime = null;
$lastLearnFile = __DIR__ . '/gz/auto_learn_state.php';
if (file_exists($lastLearnFile)) {
    $state = require $lastLearnFile;
    $lastLearnTime = $state['last_learn_time'] ?? null;
}

if ($isCli) {
    echo "========================================\n";
    echo "  自动学习执行日志\n";
    echo "========================================\n";
    echo "上次学习时间: " . ($lastLearnTime ?: '从未学习') . "\n";
    echo "日志总数: " . count($logs) . " 条\n";
    echo "========================================\n\n";
    
    if (empty($logs)) {
        echo "暂无日志记录\n";
    } else {
        foreach (array_slice($logs, 0, 20) as $idx => $log) {
            $typeIcon = [
                'info' => 'ℹ️',
                'success' => '✅',
                'warning' => '⚠️',
                'error' => '❌'
            ][$log['type'] ?? 'info'] ?? 'ℹ️';
            
            echo ($idx + 1) . ". [" . ($log['time'] ?? '') . "] $typeIcon " . ($log['message'] ?? '') . "\n";
        }
    }
    echo "\n========================================\n";
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自动学习执行日志</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #303133;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #ebeef5;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        .stat-card {
            background: #f5f7fa;
            padding: 16px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #409eff;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 13px;
            color: #909399;
        }
        .log-list {
            max-height: 600px;
            overflow-y: auto;
        }
        .log-item {
            padding: 12px;
            border-bottom: 1px solid #ebeef5;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .log-item:last-child { border-bottom: none; }
        .log-icon {
            font-size: 18px;
            flex-shrink: 0;
            width: 24px;
            text-align: center;
        }
        .log-content { flex: 1; }
        .log-message {
            color: #303133;
            margin-bottom: 4px;
        }
        .log-time {
            font-size: 12px;
            color: #909399;
        }
        .log-item.info .log-icon { color: #409eff; }
        .log-item.success .log-icon { color: #67c23a; }
        .log-item.warning .log-icon { color: #e6a23c; }
        .log-item.error .log-icon { color: #f56c6c; }
        .empty {
            text-align: center;
            padding: 40px;
            color: #909399;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #409eff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .btn:hover { background: #66b1ff; }
        .btn-success { background: #67c23a; }
        .btn-success:hover { background: #85ce61; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-title">📊 自动学习状态</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $lastLearnTime ? date('m-d H:i', strtotime($lastLearnTime)) : '从未'; ?></div>
                    <div class="stat-label">上次学习时间</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo count($logs); ?></div>
                    <div class="stat-label">日志总数</div>
                </div>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <button class="btn btn-success" onclick="location.href='cron_autolearn.php?force=1<?php echo !empty($_GET['key']) ? '&key=' . urlencode($_GET['key']) : ''; ?>'">
                    立即执行学习
                </button>
                <button class="btn" onclick="location.reload()">刷新日志</button>
                <a class="btn" href="mxadmin.php" style="background: #909399;">返回后台</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-title">📝 执行日志</div>
            <div class="log-list">
                <?php if (empty($logs)): ?>
                    <div class="empty">暂无日志记录</div>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="log-item <?php echo $log['type'] ?? 'info'; ?>">
                            <div class="log-icon">
                                <?php
                                $icons = [
                                    'info' => 'ℹ️',
                                    'success' => '✅',
                                    'warning' => '⚠️',
                                    'error' => '❌'
                                ];
                                echo $icons[$log['type'] ?? 'info'] ?? 'ℹ️';
                                ?>
                            </div>
                            <div class="log-content">
                                <div class="log-message"><?php echo htmlspecialchars($log['message'] ?? ''); ?></div>
                                <div class="log-time"><?php echo htmlspecialchars($log['time'] ?? ''); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
