<?php

require_once __DIR__ . '/TaskResult.php';
require_once __DIR__ . '/TaskRunnerInterface.php';

class ProcessTaskRunner implements TaskRunnerInterface {
    private $concurrency = 5;
    private $timeout = 60;
    private $mode = 'process';
    private $tmpFiles = [];

    public function __construct($options = []) {
        if (isset($options['concurrency'])) {
            $this->concurrency = max(1, intval($options['concurrency']));
        }
        if (isset($options['timeout'])) {
            $this->timeout = max(1, intval($options['timeout']));
        }
    }

    public function __destruct() {
        $this->cleanup();
    }

    private function cleanup() {
        foreach ($this->tmpFiles as $file) {
            if (file_exists($file)) {
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
        if ($total === 0) {
            return $results;
        }

        if (!self::isAvailable() || PHP_SAPI !== 'cli') {
            return $this->runFallback($tasks, $handler);
        }

        $queue = array_keys($tasks);
        $runningPids = [];
        $resultFiles = [];
        $tmpFiles = &$this->tmpFiles;

        $startWorker = function($taskIdx) use (&$tasks, &$runningPids, &$resultFiles, &$tmpFiles, $handler) {
            $task = $tasks[$taskIdx];
            $resultFile = tempnam(sys_get_temp_dir(), 'task_res_');
            if ($resultFile === false) {
                return false;
            }
            $resultFiles[$taskIdx] = $resultFile;
            $tmpFiles[] = $resultFile;

            $pid = pcntl_fork();

            if ($pid === -1) {
                @unlink($resultFile);
                return false;
            }

            if ($pid === 0) {
                $startTime = microtime(true);
                try {
                    $data = call_user_func($handler, $task, $taskIdx);
                    $duration = round((microtime(true) - $startTime) * 1000, 2);
                    $result = TaskResult::success($task['id'] ?? $taskIdx, $data, $duration);
                } catch (Throwable $e) {
                    $duration = round((microtime(true) - $startTime) * 1000, 2);
                    $result = TaskResult::failure($task['id'] ?? $taskIdx, $e->getMessage(), $duration);
                }

                file_put_contents($resultFile, serialize($result));
                exit(0);
            }

            $runningPids[$pid] = [
                'task_idx' => $taskIdx,
                'start_time' => time()
            ];

            return true;
        };

        $initialCount = min($this->concurrency, count($queue));
        for ($i = 0; $i < $initialCount; $i++) {
            if (count($queue) > 0) {
                $taskIdx = array_shift($queue);
                $startWorker($taskIdx);
            }
        }

        while (count($runningPids) > 0) {
            $status = null;
            $pid = pcntl_wait($status, WNOHANG);

            if ($pid > 0) {
                if (isset($runningPids[$pid])) {
                    $workerInfo = $runningPids[$pid];
                    $taskIdx = $workerInfo['task_idx'];
                    $resultFile = $resultFiles[$taskIdx] ?? null;

                    if ($resultFile && file_exists($resultFile)) {
                        $result = unserialize(file_get_contents($resultFile));
                        if ($result instanceof TaskResult) {
                            $results[$taskIdx] = $result;
                        }
                        @unlink($resultFile);
                    }

                    unset($runningPids[$pid]);

                    if (count($queue) > 0) {
                        $nextTaskIdx = array_shift($queue);
                        $startWorker($nextTaskIdx);
                    }
                }
            } elseif ($pid === 0) {
                usleep(10000);
            } else {
                break;
            }

            foreach ($runningPids as $runPid => $info) {
                if (time() - $info['start_time'] > $this->timeout) {
                    posix_kill($runPid, SIGKILL);
                    $taskIdx = $info['task_idx'];
                    $results[$taskIdx] = TaskResult::failure(
                        $tasks[$taskIdx]['id'] ?? $taskIdx,
                        '任务超时 (>' . $this->timeout . 's)'
                    );
                    unset($runningPids[$runPid]);

                    if (count($queue) > 0) {
                        $nextTaskIdx = array_shift($queue);
                        $startWorker($nextTaskIdx);
                    }
                    break;
                }
            }
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
        return array_values($results);
    }

    public static function isAvailable() {
        return function_exists('pcntl_fork') && function_exists('posix_kill') && PHP_SAPI === 'cli';
    }
}
