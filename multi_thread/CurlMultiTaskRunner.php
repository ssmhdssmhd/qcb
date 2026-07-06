<?php

require_once __DIR__ . '/TaskResult.php';
require_once __DIR__ . '/TaskRunnerInterface.php';

class CurlMultiTaskRunner implements TaskRunnerInterface {
    private $concurrency = 5;
    private $timeout = 60;
    private $mode = 'curl_multi';

    public function __construct($options = []) {
        if (isset($options['concurrency'])) {
            $this->concurrency = max(1, intval($options['concurrency']));
        }
        if (isset($options['timeout'])) {
            $this->timeout = max(1, intval($options['timeout']));
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

        if (is_string($handler)) {
            $urlTemplate = $handler;
            $isUrlTemplate = true;
        } elseif (is_callable($handler)) {
            $isUrlTemplate = false;
        } else {
            $isUrlTemplate = false;
        }

        if ($isUrlTemplate) {
            return $this->runWithUrls($tasks, $urlTemplate);
        } else {
            return $this->runWithCallback($tasks, $handler);
        }
    }

    private function runWithUrls(array $tasks, $urlTemplate) {
        $results = [];
        $queue = array_keys($tasks);
        $activeHandles = [];
        $mh = curl_multi_init();

        $startNextBatch = function() use (&$queue, &$activeHandles, &$tasks, $mh, $urlTemplate) {
            while (count($activeHandles) < $this->concurrency && count($queue) > 0) {
                $taskIdx = array_shift($queue);
                $task = $tasks[$taskIdx];

                $url = $this->buildUrl($urlTemplate, $task);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

                if (!empty($task['post_data'])) {
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($task['post_data']) ? json_encode($task['post_data']) : $task['post_data']);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                }

                curl_multi_add_handle($mh, $ch);
                $activeHandles[(int)$ch] = [
                    'task_idx' => $taskIdx,
                    'task' => $task,
                    'ch' => $ch,
                    'start_time' => microtime(true)
                ];
            }
        };

        $startNextBatch();
        $active = null;

        do {
            $mrc = curl_multi_exec($mh, $active);
            if ($mrc != CURLM_OK) {
                break;
            }

            if ($active > 0) {
                curl_multi_select($mh, 0.5);
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
                            if ($data === null) {
                                $results[$taskIdx] = TaskResult::success($task['id'] ?? $taskIdx, $response, $duration);
                            } else {
                                $results[$taskIdx] = TaskResult::success($task['id'] ?? $taskIdx, $data, $duration);
                            }
                        } else {
                            $results[$taskIdx] = TaskResult::failure($task['id'] ?? $taskIdx, "HTTP $httpCode: $response", $duration);
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

    private function runWithCallback(array $tasks, $callback) {
        $results = [];
        foreach ($tasks as $idx => $task) {
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
        return array_values($results);
    }

    private function buildUrl($template, $task) {
        if (is_callable($template)) {
            return call_user_func($template, $task);
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
}
