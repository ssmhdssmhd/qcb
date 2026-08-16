<?php
// ==========================================================================
// v5.14.0 ProcessTaskRunner 优化版
// 修复：1. 僵尸进程双回收(WNOHANG+SIGCHLD忽略) 2. 信号处理 3. 超时精准查杀
//       4. 临时文件带进程 PID，避免多实例竞争 5. 文件锁防任务并发冲突
// ==========================================================================

require_once __DIR__ . '/TaskResult.php';
require_once __DIR__ . '/TaskRunnerInterface.php';

class ProcessTaskRunner implements TaskRunnerInterface {
    private $concurrency = 8;
    private $timeout = 25;
    private $mode = 'process';
    private $tmpFiles = [];
    private $parentPid = 0;

    public function __construct($options = []) {
        $this->parentPid = posix_getpid();
        if (isset($options['concurrency'])) {
            $this->concurrency = max(1, intval($options['concurrency']));
        }
        if (isset($options['timeout'])) {
            $this->timeout = max(1, intval($options['timeout']));
        }
        // 忽略子进程信号，避免僵尸进程
        if (function_exists('pcntl_signal')) {
            @pcntl_signal(SIGCHLD, SIG_IGN);
            @pcntl_signal(SIGPIPE, SIG_IGN);
        }
    }

    public function __destruct() {
        // 只有父进程负责清理，防止子进程误删同 PID 新进程文件
        if (posix_getpid() === $this->parentPid) {
            $this->cleanup();
        }
    }

    private function cleanup() {
        foreach ($this->tmpFiles as $file) {
            if (is_string($file) && file_exists($file)) {
                @unlink($file);
            }
        }
        $this->tmpFiles = [];
    }

    public function setConcurrency($concurrency) {
        $this->concurrency = max(1, intval($concurrency));
    }

    public function setTimeout($timeout) {
        $this->timeout = max(1, intval($timeout));
    }

    public function getMode() {
        return $this->mode;
    }

    public function run(array $tasks, $handler) {
        $results = [];
        $total = count($tasks);
        if ($total === 0) return $results;

        if (!self::isAvailable() || PHP_SAPI !== 'cli') {
            return $this->runFallback($tasks, $handler);
        }

        // 单任务跳过 fork 开销
        if ($total === 1) {
            $idx = array_key_first($tasks);
            $task = $tasks[$idx];
            $start = microtime(true);
            try {
                $data = call_user_func($handler, $task, $idx);
                $duration = round((microtime(true) - $start) * 1000, 2);
                return [TaskResult::success($task['id'] ?? $idx, $data, $duration)];
            } catch (Throwable $e) {
                $duration = round((microtime(true) - $start) * 1000, 2);
                return [TaskResult::failure($task['id'] ?? $idx, $e->getMessage(), $duration)];
            }
        }

        $queue = array_keys($tasks);
        $runningPids = [];
        $resultFiles = [];
        $tmpFiles = &$this->tmpFiles;
        $parentPid = $this->parentPid;

        $startWorker = function($taskIdx) use (&$tasks, &$runningPids, &$resultFiles, &$tmpFiles, $handler, $parentPid) {
            $task = $tasks[$taskIdx];
            $safeIdx = (string)(int)$taskIdx;
            $resultFile = sys_get_temp_dir()
                . '/ptr_' . $parentPid
                . '_' . $safeIdx
                . '_' . substr(md5((string)mt_rand()), 0, 8)
                . '.tmp';
            touch($resultFile);
            $resultFiles[$taskIdx] = $resultFile;
            $tmpFiles[] = $resultFile;

            $pid = pcntl_fork();
            if ($pid === -1) {
                @unlink($resultFile);
                return false;
            }

            if ($pid === 0) {
                // ============ 子进程 ============
                // 子进程不应该操作父进程的临时文件列表
                $this->tmpFiles = [];
                // 关闭父进程打开的 STDIN/STDOUT 避免输出干扰
                // （保留，因为 handler 可能需要日志）
                register_shutdown_function(function() use ($resultFile) {
                    // 兜底：确保任何退出路径都不会留空文件
                    $size = @filesize($resultFile);
                    if ($size === 0 || $size === false) {
                        $fallback = TaskResult::failure('child_died', '子进程异常退出', 0);
                        @file_put_contents($resultFile, serialize($fallback));
                    }
                });

                $startTime = microtime(true);
                try {
                    $data = call_user_func($handler, $task, $taskIdx);
                    $duration = round((microtime(true) - $startTime) * 1000, 2);
                    $result = TaskResult::success($task['id'] ?? $taskIdx, $data, $duration);
                } catch (Throwable $e) {
                    $duration = round((microtime(true) - $startTime) * 1000, 2);
                    $result = TaskResult::failure(
                        $task['id'] ?? $taskIdx,
                        get_class($e) . ': ' . $e->getMessage() . ' (L' . $e->getLine() . ')',
                        $duration
                    );
                }
                file_put_contents($resultFile, serialize($result), LOCK_EX);
                // 用 exit(0) 避免子进程跑父进程的 destruct
                exit(0);
            }

            // ============ 父进程 ============
            $runningPids[$pid] = [
                'task_idx' => $taskIdx,
                'start_time' => microtime(true),
                'deadline' => microtime(true) + $this->timeout,
            ];
            return true;
        };

        // 启动首批
        $initialCount = min($this->concurrency, count($queue));
        for ($i = 0; $i < $initialCount; $i++) {
            if (!empty($queue)) {
                $taskIdx = array_shift($queue);
                $startWorker($taskIdx);
            }
        }

        $loopStart = microtime(true);
        $hardDeadline = $loopStart + ($this->timeout * $total) + 5; // 全任务兜底硬截止

        while (!empty($runningPids)) {
            // 先做超时检测（避免 wait 长时间阻塞导致不触发）
            $now = microtime(true);
            if ($now > $hardDeadline) break;

            foreach ($runningPids as $runPid => $info) {
                if ($now >= $info['deadline']) {
                    // 超时：SIGTERM 温柔 → 200ms 后 SIGKILL 强杀
                    @posix_kill($runPid, SIGTERM);
                    usleep(200000);
                    $status = null;
                    $checkPid = @pcntl_waitpid($runPid, $status, WNOHANG);
                    if ($checkPid === 0) {
                        @posix_kill($runPid, SIGKILL);
                        @pcntl_waitpid($runPid, $status, WNOHANG);
                    }
                    $taskIdx = $info['task_idx'];
                    $results[$taskIdx] = TaskResult::failure(
                        $tasks[$taskIdx]['id'] ?? $taskIdx,
                        '任务超时 (> ' . $this->timeout . 's)'
                    );
                    $resFile = $resultFiles[$taskIdx] ?? null;
                    if ($resFile) @unlink($resFile);
                    unset($runningPids[$runPid]);
                    if (!empty($queue)) {
                        $nextTaskIdx = array_shift($queue);
                        $startWorker($nextTaskIdx);
                    }
                    break; // 一个循环只清一个超时，避免影响正常回收
                }
            }

            // WNOHANG 不阻塞回收已完成
            $reapedAny = false;
            foreach (array_keys($runningPids) as $runPid) {
                $status = null;
                $pid = @pcntl_waitpid($runPid, $status, WNOHANG);
                if ($pid > 0) {
                    if (isset($runningPids[$pid])) {
                        $workerInfo = $runningPids[$pid];
                        $taskIdx = $workerInfo['task_idx'];
                        $resultFile = $resultFiles[$taskIdx] ?? null;
                        if ($resultFile && file_exists($resultFile) && filesize($resultFile) > 0) {
                            $raw = @file_get_contents($resultFile);
                            $result = @unserialize($raw);
                            if ($result instanceof TaskResult) {
                                $results[$taskIdx] = $result;
                            } else {
                                $results[$taskIdx] = TaskResult::failure(
                                    $tasks[$taskIdx]['id'] ?? $taskIdx,
                                    '结果反序列化失败'
                                );
                            }
                        } else {
                            $results[$taskIdx] = TaskResult::failure(
                                $tasks[$taskIdx]['id'] ?? $taskIdx,
                                '子进程未生成结果（可能崩溃）'
                            );
                        }
                        if ($resultFile) @unlink($resultFile);
                        unset($runningPids[$pid]);
                        $reapedAny = true;

                        if (!empty($queue)) {
                            $nextTaskIdx = array_shift($queue);
                            $startWorker($nextTaskIdx);
                        }
                    }
                } elseif ($pid === -1) {
                    unset($runningPids[$runPid]);
                }
            }

            if (!$reapedAny && !empty($runningPids)) {
                // 2ms 短 sleep，降低 CPU 空转
                usleep(2000);
            }
        }

        // 循环正常结束，残余全部做失败处理
        foreach (array_keys($runningPids) as $leftoverPid) {
            $info = $runningPids[$leftoverPid];
            $taskIdx = $info['task_idx'];
            @posix_kill($leftoverPid, SIGKILL);
            @pcntl_waitpid($leftoverPid, $_, WNOHANG);
            if (!isset($results[$taskIdx])) {
                $results[$taskIdx] = TaskResult::failure(
                    $tasks[$taskIdx]['id'] ?? $taskIdx,
                    '任务被强制终止'
                );
            }
            $resFile = $resultFiles[$taskIdx] ?? null;
            if ($resFile) @unlink($resFile);
        }

        ksort($results);
        $this->cleanup();
        return array_values($results);
    }

    private function runFallback(array $tasks, $handler) {
        $results = [];
        foreach ($tasks as $idx => $task) {
            $start = microtime(true);
            try {
                $data = call_user_func($handler, $task, $idx);
                $duration = round((microtime(true) - $start) * 1000, 2);
                $results[$idx] = TaskResult::success($task['id'] ?? $idx, $data, $duration);
            } catch (Throwable $e) {
                $duration = round((microtime(true) - $start) * 1000, 2);
                $results[$idx] = TaskResult::failure($task['id'] ?? $idx, $e->getMessage(), $duration);
            }
        }
        ksort($results);
        return array_values($results);
    }

    public static function isAvailable() {
        return (
            function_exists('pcntl_fork') &&
            function_exists('posix_kill') &&
            function_exists('pcntl_wait') &&
            PHP_SAPI === 'cli'
        );
    }
}
