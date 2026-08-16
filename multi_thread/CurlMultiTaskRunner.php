<?php
// ==========================================================================
// v5.14.0 优化版：CurlMultiTaskRunner
// 修复：1. 连接复用 2. 并发数提升 3. 回调模式用共享内存+子进程伪并行 4. 毫秒级 select 优化
// ==========================================================================

require_once __DIR__ . '/TaskResult.php';
require_once __DIR__ . '/TaskRunnerInterface.php';

class CurlMultiTaskRunner implements TaskRunnerInterface {
    private $concurrency = 10;          // 默认并发从 5 提升到 10
    private $timeout = 30;              // 默认超时从 60 降到 30
    private $connectTimeout = 5;        // 连接超时从 15 降到 5
    private $mode = 'curl_multi';
    private static $shareCache = [];    // 进程内静态缓存（避免重复请求）
    private static $curlShareHandle;    // curl SHARE handle（DNS+Cookie复用）

    public function __construct($options = []) {
        if (isset($options['concurrency'])) {
            $this->concurrency = max(1, intval($options['concurrency']));
        }
        if (isset($options['timeout'])) {
            $this->timeout = max(1, intval($options['timeout']));
        }
        if (isset($options['connect_timeout'])) {
            $this->connectTimeout = max(1, intval($options['connect_timeout']));
        }
        // 初始化共享句柄（DNS 缓存复用）
        if (self::$curlShareHandle === null && function_exists('curl_share_init')) {
            self::$curlShareHandle = @curl_share_init();
            if (self::$curlShareHandle) {
                @curl_share_setopt(self::$curlShareHandle, CURLSHOPT_SHARE, CURL_LOCK_DATA_DNS);
                @curl_share_setopt(self::$curlShareHandle, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION);
            }
        }
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

        // 单任务直接执行，跳过并发开销
        if ($total === 1) {
            $idx = array_key_first($tasks);
            $task = $tasks[$idx];
            $start = microtime(true);
            try {
                if (is_string($handler)) {
                    $url = $this->buildUrl($handler, $task);
                    $data = $this->singleFetch($url, $task);
                } else {
                    $data = call_user_func($handler, $task, $idx);
                }
                $duration = round((microtime(true) - $start) * 1000, 2);
                return [TaskResult::success($task['id'] ?? $idx, $data, $duration)];
            } catch (Throwable $e) {
                $duration = round((microtime(true) - $start) * 1000, 2);
                return [TaskResult::failure($task['id'] ?? $idx, $e->getMessage(), $duration)];
            }
        }

        if (is_string($handler)) {
            return $this->runWithUrls($tasks, $handler);
        } elseif (is_callable($handler)) {
            // 回调模式：可用 pcntl_fork 时真并行，否则分片批处理
            if (PHP_SAPI === 'cli' && function_exists('pcntl_fork') && function_exists('posix_kill') && $total >= 4) {
                return $this->runWithCallbackParallel($tasks, $handler);
            }
            return $this->runWithCallbackBatched($tasks, $handler);
        }
        return $this->runWithCallbackBatched($tasks, $handler);
    }

    /**
     * 单次 HTTP 获取（带进程内缓存）
     */
    private function singleFetch(string $url, array $task) {
        $cacheKey = md5($url . json_encode($task['post_data'] ?? []));
        if (isset(self::$shareCache[$cacheKey])) {
            return self::$shareCache[$cacheKey];
        }
        $ch = $this->createCurlHandle($url, $task);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
            $data = json_decode($response, true);
            $result = $data === null ? $response : $data;
            if (count(self::$shareCache) < 256) {
                self::$shareCache[$cacheKey] = $result;
            }
            return $result;
        }
        throw new RuntimeException("HTTP $httpCode");
    }

    /**
     * 创建优化后的 curl 句柄（TCP 快速打开 + 连接复用）
     */
    private function createCurlHandle(string $url, array $task) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36');
        // TCP Fast Open + 连接保活
        if (defined('CURLOPT_TCP_FASTOPEN')) {
            @curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
        }
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 60);
        // 共享 DNS/SSL Session
        if (self::$curlShareHandle !== null) {
            @curl_setopt($ch, CURLOPT_SHARE, self::$curlShareHandle);
        }
        // 自定义 Header
        $headers = [
            'Accept: */*',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
        ];
        if (!empty($task['post_data'])) {
            curl_setopt($ch, CURLOPT_POST, true);
            $postFields = is_array($task['post_data']) ? json_encode($task['post_data'], JSON_UNESCAPED_UNICODE) : $task['post_data'];
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($postFields);
        } else {
            $headers[] = 'Connection: keep-alive';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (!empty($task['headers']) && is_array($task['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, $task['headers']));
        }
        return $ch;
    }

    private function runWithUrls(array $tasks, $urlTemplate) {
        $results = [];
        $queue = array_keys($tasks);
        $activeHandles = [];
        $mh = curl_multi_init();
        // 优化：设置更低的 pipelining
        if (defined('CURLPIPE_MULTIPLEX')) {
            @curl_multi_setopt($mh, CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX);
        }
        @curl_multi_setopt($mh, CURLMOPT_MAXCONNECTS, $this->concurrency * 2);

        $startNextBatch = function() use (&$queue, &$activeHandles, &$tasks, $mh, $urlTemplate) {
            while (count($activeHandles) < $this->concurrency && count($queue) > 0) {
                $taskIdx = array_shift($queue);
                $task = $tasks[$taskIdx];

                $url = $this->buildUrl($urlTemplate, $task);
                // 命中静态缓存跳过 HTTP
                $cacheKey = md5($url . json_encode($task['post_data'] ?? []));
                if (isset(self::$shareCache[$cacheKey])) {
                    $results[$taskIdx] = TaskResult::success($task['id'] ?? $taskIdx, self::$shareCache[$cacheKey], 0.01);
                    continue;
                }

                $ch = $this->createCurlHandle($url, $task);
                curl_multi_add_handle($mh, $ch);
                $activeHandles[(int)$ch] = [
                    'task_idx' => $taskIdx,
                    'task' => $task,
                    'ch' => $ch,
                    'start_time' => microtime(true),
                    'cache_key' => $cacheKey,
                ];
            }
        };

        $startNextBatch();
        $active = null;

        do {
            // 非阻塞轮询（比 select 0.5s 更快）
            while (($mrc = curl_multi_exec($mh, $active)) === CURLM_CALL_MULTI_PERFORM) {}
            if ($mrc != CURLM_OK) {
                break;
            }

            if ($active > 0) {
                // 0.05s = 50ms 粒度，比原先 0.5s 响应快 10 倍
                curl_multi_select($mh, 0.05);
            }

            while ($info = curl_multi_info_read($mh)) {
                $ch = $info['handle'];
                $handleKey = (int)$ch;
                $handleInfo = $activeHandles[$handleKey] ?? null;

                if ($handleInfo) {
                    $taskIdx = $handleInfo['task_idx'];
                    $task = $handleInfo['task'];
                    $duration = round((microtime(true) - $handleInfo['start_time']) * 1000, 2);

                    if ($info['result'] === CURLE_OK) {
                        $response = curl_multi_getcontent($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        if ($httpCode >= 200 && $httpCode < 300) {
                            $data = json_decode($response, true);
                            $payload = $data === null ? $response : $data;
                            // 写静态缓存（限 256 条防内存膨胀）
                            if (count(self::$shareCache) < 256) {
                                self::$shareCache[$handleInfo['cache_key']] = $payload;
                            }
                            $results[$taskIdx] = TaskResult::success($task['id'] ?? $taskIdx, $payload, $duration);
                        } else {
                            $results[$taskIdx] = TaskResult::failure($task['id'] ?? $taskIdx, "HTTP $httpCode", $duration);
                        }
                    } else {
                        $error = curl_error($ch);
                        $results[$taskIdx] = TaskResult::failure($task['id'] ?? $taskIdx, $error, $duration);
                    }

                    curl_multi_remove_handle($mh, $ch);
                    curl_close($ch);
                    unset($activeHandles[$handleKey]);
                }
            }

            $startNextBatch();

        } while ($active > 0 || count($queue) > 0);

        curl_multi_close($mh);

        ksort($results);
        return array_values($results);
    }

    /**
     * 回调并行版：用 pcntl_fork 真正并发跑回调
     */
    private function runWithCallbackParallel(array $tasks, $callback): array {
        $results = [];
        $queue = array_keys($tasks);
        $runningPids = [];
        $tmpDir = sys_get_temp_dir();

        $startWorker = function($taskIdx) use (&$tasks, &$runningPids, &$results, $callback, $tmpDir) {
            $task = $tasks[$taskIdx];
            $resultFile = $tmpDir . '/cmtr_' . posix_getpid() . '_' . $taskIdx . '_' . uniqid('', true) . '.tmp';
            $pid = pcntl_fork();
            if ($pid === -1) return false;
            if ($pid === 0) {
                // 子进程
                $startTime = microtime(true);
                try {
                    $data = call_user_func($callback, $task, $taskIdx);
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
                'start_time' => microtime(true),
                'result_file' => $resultFile,
            ];
            return true;
        };

        // 首批
        $initial = min($this->concurrency, count($queue));
        for ($i = 0; $i < $initial; $i++) {
            if (!empty($queue)) $startWorker(array_shift($queue));
        }

        while (!empty($runningPids)) {
            $status = null;
            $pid = pcntl_wait($status, WNOHANG);
            if ($pid > 0) {
                if (isset($runningPids[$pid])) {
                    $info = $runningPids[$pid];
                    $taskIdx = $info['task_idx'];
                    if (file_exists($info['result_file'])) {
                        $res = @unserialize(file_get_contents($info['result_file']));
                        if ($res instanceof TaskResult) $results[$taskIdx] = $res;
                        @unlink($info['result_file']);
                    }
                    unset($runningPids[$pid]);
                    if (!empty($queue)) $startWorker(array_shift($queue));
                }
            } elseif ($pid === 0) {
                usleep(2000); // 2ms 粒度
            } else {
                break;
            }
            // 超时检查
            $now = microtime(true);
            foreach ($runningPids as $runPid => $info) {
                if (($now - $info['start_time']) > $this->timeout) {
                    posix_kill($runPid, SIGKILL);
                    $taskIdx = $info['task_idx'];
                    $results[$taskIdx] = TaskResult::failure(
                        $tasks[$taskIdx]['id'] ?? $taskIdx,
                        '任务超时 (>' . $this->timeout . 's)'
                    );
                    @unlink($info['result_file']);
                    unset($runningPids[$runPid]);
                    if (!empty($queue)) $startWorker(array_shift($queue));
                    break;
                }
            }
        }
        ksort($results);
        return array_values($results);
    }

    /**
     * 回调分片批处理版（非 CLI 环境兜底）
     */
    private function runWithCallbackBatched(array $tasks, $callback): array {
        $results = [];
        $batchSize = (int)ceil(count($tasks) / max(1, $this->concurrency));
        $batches = array_chunk($tasks, $batchSize, true);
        foreach ($batches as $batch) {
            foreach ($batch as $idx => $task) {
                $start = microtime(true);
                try {
                    $data = call_user_func($callback, $task, $idx);
                    $duration = round((microtime(true) - $start) * 1000, 2);
                    $results[$idx] = TaskResult::success($task['id'] ?? $idx, $data, $duration);
                } catch (Throwable $e) {
                    $duration = round((microtime(true) - $start) * 1000, 2);
                    $results[$idx] = TaskResult::failure($task['id'] ?? $idx, $e->getMessage(), $duration);
                }
            }
        }
        ksort($results);
        return array_values($results);
    }

    private function buildUrl($template, $task) {
        if (is_callable($template)) {
            return call_user_func($template, $task);
        }
        if (is_string($template) && $template === '{url}' && isset($task['url'])) {
            return $task['url'];
        }
        $url = $template;
        foreach ($task as $key => $value) {
            if (is_string($value) || is_numeric($value)) {
                $url = str_replace('{' . $key . '}', urlencode($value), $url);
            }
        }
        return $url;
    }

    public static function isAvailable() {
        return function_exists('curl_multi_init');
    }

    public function __destruct() {
        if (self::$curlShareHandle !== null && function_exists('curl_share_close')) {
            @curl_share_close(self::$curlShareHandle);
            self::$curlShareHandle = null;
        }
    }
}
