<?php

require_once __DIR__ . '/AutoLearnEngine.php';
require_once __DIR__ . '/AiEndpointRouter.php';

class TaskScheduler {
    private $tasks = [];
    private $cacheDir;
    private $lockDir;
    private $lastRunDir;
    private $statusDir;

    public function __construct($cacheDir = null) {
        if ($cacheDir === null) {
            $cacheDir = __DIR__ . '/../cache/scheduler';
        }
        $this->cacheDir = $cacheDir;
        $this->lockDir = $cacheDir;
        $this->lastRunDir = $cacheDir;
        $this->statusDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        if (!is_dir($this->lockDir)) {
            @mkdir($this->lockDir, 0755, true);
        }
        if (!is_dir($this->lastRunDir)) {
            @mkdir($this->lastRunDir, 0755, true);
        }
        if (!is_dir($this->statusDir)) {
            @mkdir($this->statusDir, 0755, true);
        }
        $this->registerDefaultTasks();
    }

    private function registerDefaultTasks() {
        $this->registerTask('auto_learn', 3600 * 6, function($opts) {
            $engine = new AutoLearnEngine($opts['engine_opts'] ?? []);
            $maxSites = $opts['max_sites'] ?? 5;
            $perSite = $opts['per_site'] ?? 5;
            return $engine->run($maxSites, $perSite);
        }, [
            'enabled' => true,
            'max_sites' => 5,
            'per_site' => 5
        ]);

        $this->registerTask('cctv_update', 3600 * 12, function() {
            if (file_exists(__DIR__ . '/../cctv/CctvSourceManager.php')) {
                require_once __DIR__ . '/../cctv/CctvSourceManager.php';
                require_once __DIR__ . '/../cctv/CctvSourceVerifier.php';
                require_once __DIR__ . '/../cctv/CctvPlaylistGenerator.php';
                $mgr = new CctvSourceManager();
                $verifier = new CctvSourceVerifier();
                $generator = new CctvPlaylistGenerator();
                return 'CCTV模块已调用（占位）';
            }
            return 'CCTV模块暂未就绪（stub占位）';
        }, ['enabled' => true]);

        $this->registerTask('ai_health_check', 1800, function($opts) {
            $endpoints = $opts['endpoints'] ?? null;
            if ($endpoints === null && file_exists(__DIR__ . '/../xt/config.php')) {
                $xtConfig = require __DIR__ . '/../xt/config.php';
                if (!empty($xtConfig['ai'])) {
                    $aiConfig = $xtConfig['ai'];
                    $endpoints = [[
                        'name' => $aiConfig['provider'] ?? 'default',
                        'url' => $aiConfig['api_url'] ?? '',
                        'api_key' => $aiConfig['api_key'] ?? '',
                        'type' => 'openai_compatible',
                        'priority' => 1
                    ]];
                }
            }
            $router = new AiEndpointRouter($endpoints ?: []);
            $healthy = $router->getHealthyEndpoints();
            try {
                $router->updateConfigStoredOrder();
            } catch (Throwable $e) {
            }
            return [
                'healthy_count' => count($healthy),
                'healthy_endpoints' => $healthy
            ];
        }, ['enabled' => true]);
    }

    public function registerTask($name, $intervalSec, $callable, $opts = []) {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('Task callable must be callable');
        }
        $this->tasks[$name] = [
            'name' => $name,
            'interval_sec' => intval($intervalSec),
            'callable' => $callable,
            'opts' => $opts,
            'enabled' => $opts['enabled'] ?? true
        ];
        return true;
    }

    public function shouldRun($taskName) {
        if (!isset($this->tasks[$taskName])) {
            return false;
        }
        if (empty($this->tasks[$taskName]['enabled'])) {
            return false;
        }
        $task = $this->tasks[$taskName];
        $lastRunFile = $this->lastRunDir . '/' . $taskName . '_last_run.txt';
        if (!file_exists($lastRunFile)) {
            return true;
        }
        $lastRun = intval(@file_get_contents($lastRunFile));
        if ($lastRun <= 0) {
            return true;
        }
        return (time() - $lastRun) >= $task['interval_sec'];
    }

    public function runDueTasks() {
        $results = [];
        foreach ($this->tasks as $name => $task) {
            if (empty($task['enabled'])) {
                $results[$name] = ['status' => 'disabled', 'enabled' => false];
                continue;
            }
            $failCount = $this->getFailCount($name);
            $lastFailDate = $this->getLastFailDate($name);
            $in24Hours = $lastFailDate && (time() - $lastFailDate) < 86400;
            if ($failCount >= 3 && $in24Hours) {
                $msg = "[TaskScheduler] 任务 $name 24小时内已失败 >=3 次，跳过本次运行。失败次数: $failCount, 最后错误: " . $this->getLastError($name);
                error_log($msg);
                $results[$name] = ['status' => 'skipped_failures', 'message' => $msg];
                continue;
            }
            if (!$this->shouldRun($name)) {
                $results[$name] = ['status' => 'not_due', 'next_run' => $this->getNextRunTime($name)];
                continue;
            }
            $lockFile = $this->lockDir . '/' . $name . '.lock';
            $lockFp = @fopen($lockFile, 'w+');
            if ($lockFp === false) {
                $msg = "[TaskScheduler] 无法创建锁文件: $lockFile";
                error_log($msg);
                $results[$name] = ['status' => 'lock_error', 'message' => $msg];
                continue;
            }
            if (!@flock($lockFp, LOCK_EX | LOCK_NB)) {
                @fclose($lockFp);
                $msg = "[TaskScheduler] 任务 $name 正在运行中，已被锁";
                $results[$name] = ['status' => 'locked', 'message' => $msg];
                continue;
            }
            $startTime = microtime(true);
            try {
                $callable = $task['callable'];
                $taskResult = call_user_func($callable, $task['opts'] ?? []);
                $durationMs = round((microtime(true) - $startTime) * 1000, 0);
                $this->markRun($name, true, '', $durationMs);
                $results[$name] = [
                    'status' => 'success',
                    'duration_ms' => $durationMs,
                    'result' => $taskResult
                ];
            } catch (Throwable $e) {
                $durationMs = round((microtime(true) - $startTime) * 1000, 0);
                $errorMsg = $e->getMessage();
                $this->markRun($name, false, $errorMsg, $durationMs);
                error_log("[TaskScheduler] 任务 $name 失败: $errorMsg");
                $results[$name] = [
                    'status' => 'failed',
                    'duration_ms' => $durationMs,
                    'error' => $errorMsg
                ];
            }
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
        }
        return $results;
    }

    public function markRun($taskName, $success, $message = '', $durationMs = 0) {
        $lastRunFile = $this->lastRunDir . '/' . $taskName . '_last_run.txt';
        @file_put_contents($lastRunFile, (string)time());
        $statusFile = $this->statusDir . '/' . $taskName . '_status.json';
        $status = [];
        if (file_exists($statusFile)) {
            $existing = @json_decode(@file_get_contents($statusFile), true);
            if (is_array($existing)) {
                $status = $existing;
            }
        }
        $status['last_run'] = time();
        $status['last_run_date'] = date('Y-m-d H:i:s');
        $status['duration_ms'] = intval($durationMs);
        $status['last_success'] = (bool)$success;
        $status['last_error'] = $success ? '' : (string)$message;
        if ($success) {
            $status['fail_count'] = 0;
            $status['last_fail_date'] = null;
        } else {
            $status['fail_count'] = intval($status['fail_count'] ?? 0) + 1;
            $status['last_fail_date'] = time();
            $status['last_fail_date_str'] = date('Y-m-d H:i:s');
        }
        @file_put_contents($statusFile, json_encode($status, JSON_UNESCAPED_UNICODE));
        return true;
    }

    public function getTaskStatus() {
        $results = [];
        foreach ($this->tasks as $name => $task) {
            $statusFile = $this->statusDir . '/' . $name . '_status.json';
            $status = [];
            if (file_exists($statusFile)) {
                $decoded = @json_decode(@file_get_contents($statusFile), true);
                if (is_array($decoded)) {
                    $status = $decoded;
                }
            }
            $lastRun = intval($status['last_run'] ?? 0);
            $intervalSec = $task['interval_sec'];
            $nextRun = $lastRun > 0 ? ($lastRun + $intervalSec) : time();
            $results[] = [
                'name' => $name,
                'last_run' => $lastRun ? date('Y-m-d H:i:s', $lastRun) : '',
                'last_run_ts' => $lastRun,
                'next_run' => date('Y-m-d H:i:s', $nextRun),
                'next_run_ts' => $nextRun,
                'interval_sec' => $intervalSec,
                'fail_count' => intval($status['fail_count'] ?? 0),
                'last_error' => $status['last_error'] ?? '',
                'last_fail_date' => isset($status['last_fail_date_str']) ? $status['last_fail_date_str'] : '',
                'enabled' => !empty($task['enabled']),
                'last_duration_ms' => intval($status['duration_ms'] ?? 0),
                'last_success' => !empty($status['last_success'])
            ];
        }
        return $results;
    }

    private function getFailCount($taskName) {
        $statusFile = $this->statusDir . '/' . $taskName . '_status.json';
        if (!file_exists($statusFile)) {
            return 0;
        }
        $status = @json_decode(@file_get_contents($statusFile), true);
        if (!is_array($status)) {
            return 0;
        }
        return intval($status['fail_count'] ?? 0);
    }

    private function getLastFailDate($taskName) {
        $statusFile = $this->statusDir . '/' . $taskName . '_status.json';
        if (!file_exists($statusFile)) {
            return null;
        }
        $status = @json_decode(@file_get_contents($statusFile), true);
        if (!is_array($status) || empty($status['last_fail_date'])) {
            return null;
        }
        return intval($status['last_fail_date']);
    }

    private function getLastError($taskName) {
        $statusFile = $this->statusDir . '/' . $taskName . '_status.json';
        if (!file_exists($statusFile)) {
            return '';
        }
        $status = @json_decode(@file_get_contents($statusFile), true);
        if (!is_array($status)) {
            return '';
        }
        return $status['last_error'] ?? '';
    }

    private function getNextRunTime($taskName) {
        if (!isset($this->tasks[$taskName])) {
            return '';
        }
        $task = $this->tasks[$taskName];
        $lastRunFile = $this->lastRunDir . '/' . $taskName . '_last_run.txt';
        if (!file_exists($lastRunFile)) {
            return date('Y-m-d H:i:s');
        }
        $lastRun = intval(@file_get_contents($lastRunFile));
        return date('Y-m-d H:i:s', $lastRun + $task['interval_sec']);
    }

    public function runTask($taskName, $force = false) {
        if (!isset($this->tasks[$taskName])) {
            return ['status' => 'not_found', 'message' => "任务 $taskName 不存在"];
        }
        $task = $this->tasks[$taskName];
        if (!$force && !$this->shouldRun($taskName)) {
            return ['status' => 'not_due', 'message' => "任务 $taskName 未到期", 'next_run' => $this->getNextRunTime($taskName)];
        }
        $lockFile = $this->lockDir . '/' . $taskName . '.lock';
        $lockFp = @fopen($lockFile, 'w+');
        if ($lockFp === false) {
            return ['status' => 'lock_error', 'message' => "无法创建锁文件: $lockFile"];
        }
        if (!@flock($lockFp, LOCK_EX | LOCK_NB)) {
            @fclose($lockFp);
            return ['status' => 'locked', 'message' => "任务 $taskName 正在运行中"];
        }
        $startTime = microtime(true);
        try {
            $callable = $task['callable'];
            $taskResult = call_user_func($callable, $task['opts'] ?? []);
            $durationMs = round((microtime(true) - $startTime) * 1000, 0);
            $this->markRun($taskName, true, '', $durationMs);
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
            return [
                'status' => 'success',
                'duration_ms' => $durationMs,
                'result' => $taskResult
            ];
        } catch (Throwable $e) {
            $durationMs = round((microtime(true) - $startTime) * 1000, 0);
            $errorMsg = $e->getMessage();
            $this->markRun($taskName, false, $errorMsg, $durationMs);
            @flock($lockFp, LOCK_UN);
            @fclose($lockFp);
            error_log("[TaskScheduler] 任务 $taskName 失败: $errorMsg");
            return [
                'status' => 'failed',
                'duration_ms' => $durationMs,
                'error' => $errorMsg
            ];
        }
    }

    public function handleHttpRequest() {
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        $secret = $_GET['secret'] ?? $_POST['secret'] ?? '';
        $configSecret = null;
        if (file_exists(__DIR__ . '/../xt/config.php')) {
            $xtConfig = require __DIR__ . '/../xt/config.php';
            $configSecret = $xtConfig['scheduler_secret'] ?? null;
        }
        header('Content-Type: application/json; charset=utf-8');
        if (!empty($configSecret) && $secret !== $configSecret) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'secret 不正确'], JSON_UNESCAPED_UNICODE);
            return;
        }
        switch ($action) {
            case 'run':
                $taskName = $_GET['task'] ?? $_POST['task'] ?? '';
                $force = !empty($_GET['force']) || !empty($_POST['force']);
                if (empty($taskName)) {
                    $result = $this->runDueTasks();
                } else {
                    $result = $this->runTask($taskName, $force);
                }
                echo json_encode(['success' => true, 'result' => $result], JSON_UNESCAPED_UNICODE);
                break;
            case 'status':
                echo json_encode(['success' => true, 'tasks' => $this->getTaskStatus()], JSON_UNESCAPED_UNICODE);
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => '未知 action，支持: run, status'], JSON_UNESCAPED_UNICODE);
                break;
        }
    }

    public function handleCliRequest($argv) {
        $cmd = $argv[1] ?? '';
        $taskName = $argv[2] ?? '';
        if ($cmd === '' || $cmd === 'help' || $cmd === '--help' || $cmd === '-h') {
            echo "TaskScheduler CLI 使用方法:\n";
            echo "  php ai_learn/scheduler.php run [task_name] [--force]   运行到期任务或指定任务\n";
            echo "  php ai_learn/scheduler.php status                      查看任务状态\n";
            echo "  php ai_learn/scheduler.php list                        列出已注册任务\n";
            echo "支持的任务:\n";
            foreach (array_keys($this->tasks) as $n) {
                echo "  - $n\n";
            }
            return 0;
        }
        switch ($cmd) {
            case 'run':
                $force = false;
                for ($i = 3; $i < count($argv); $i++) {
                    if ($argv[$i] === '--force' || $argv[$i] === '-f') {
                        $force = true;
                    }
                }
                if (empty($taskName)) {
                    $result = $this->runDueTasks();
                    echo "=== 调度结果 ===\n";
                    foreach ($result as $n => $r) {
                        $st = $r['status'] ?? 'unknown';
                        echo "[$n] $st";
                        if (!empty($r['duration_ms'])) echo " ({$r['duration_ms']}ms)";
                        if (!empty($r['error'])) echo "\n  错误: {$r['error']}";
                        if (!empty($r['message'])) echo "\n  消息: {$r['message']}";
                        echo "\n";
                    }
                } else {
                    $result = $this->runTask($taskName, $force);
                    echo "=== 任务: $taskName ===\n";
                    echo "状态: " . ($result['status'] ?? 'unknown') . "\n";
                    if (!empty($result['duration_ms'])) echo "耗时: {$result['duration_ms']}ms\n";
                    if (!empty($result['error'])) echo "错误: {$result['error']}\n";
                    if (!empty($result['message'])) echo "消息: {$result['message']}\n";
                }
                return 0;
            case 'status':
                $statuses = $this->getTaskStatus();
                echo "=== 任务状态 ===\n";
                foreach ($statuses as $s) {
                    echo "[" . ($s['enabled'] ? 'OK' : 'OFF') . "] {$s['name']}\n";
                    echo "  上次运行: " . ($s['last_run'] ?: '未运行') . "\n";
                    echo "  下次运行: {$s['next_run']}\n";
                    echo "  失败次数: {$s['fail_count']}\n";
                    if ($s['last_error']) echo "  上次错误: {$s['last_error']}\n";
                }
                return 0;
            case 'list':
                echo "=== 已注册任务 ===\n";
                foreach ($this->tasks as $name => $task) {
                    $hours = floor($task['interval_sec'] / 3600);
                    $minutes = floor(($task['interval_sec'] % 3600) / 60);
                    $intStr = $task['interval_sec'] . '秒';
                    if ($hours > 0) {
                        $intStr = $hours . '小时' . ($minutes > 0 ? $minutes . '分' : '');
                    } elseif ($minutes > 0) {
                        $intStr = $minutes . '分钟';
                    }
                    echo "  - $name (间隔: $intStr, 状态: " . ($task['enabled'] ? '启用' : '禁用') . ")\n";
                }
                return 0;
            default:
                echo "未知命令: $cmd\n使用 help 查看帮助\n";
                return 1;
        }
    }
}

if (php_sapi_name() === 'cli' && isset($argv) && realpath($argv[0] ?? '') === realpath(__FILE__)) {
    $scheduler = new TaskScheduler();
    $exitCode = $scheduler->handleCliRequest($argv);
    exit($exitCode);
} elseif (php_sapi_name() !== 'cli') {
    $scheduler = new TaskScheduler();
    $scheduler->handleHttpRequest();
}
