<?php

require_once __DIR__ . '/TaskResult.php';
require_once __DIR__ . '/TaskRunnerInterface.php';
require_once __DIR__ . '/CurlMultiTaskRunner.php';
require_once __DIR__ . '/ProcessTaskRunner.php';

class TaskRunner {
    const MODE_AUTO = 'auto';
    const MODE_PROCESS = 'process';
    const MODE_CURL_MULTI = 'curl_multi';
    const MODE_SERIAL = 'serial';

    private $runner;
    private $mode;
    private $options;

    public function __construct($options = []) {
        $this->options = array_merge([
            'concurrency' => 5,
            'timeout' => 60,
            'mode' => self::MODE_AUTO
        ], $options);

        $this->mode = $this->resolveMode($this->options['mode']);
        $this->runner = $this->createRunner($this->mode, $this->options);
    }

    public static function create($options = []) {
        return new self($options);
    }

    private function resolveMode($mode) {
        if ($mode === self::MODE_AUTO) {
            if (PHP_SAPI === 'cli' && ProcessTaskRunner::isAvailable()) {
                return self::MODE_PROCESS;
            }
            if (CurlMultiTaskRunner::isAvailable()) {
                return self::MODE_CURL_MULTI;
            }
            return self::MODE_SERIAL;
        }

        if ($mode === self::MODE_PROCESS && !ProcessTaskRunner::isAvailable()) {
            return CurlMultiTaskRunner::isAvailable() ? self::MODE_CURL_MULTI : self::MODE_SERIAL;
        }

        if ($mode === self::MODE_CURL_MULTI && !CurlMultiTaskRunner::isAvailable()) {
            return self::MODE_SERIAL;
        }

        return $mode;
    }

    private function createRunner($mode, $options) {
        switch ($mode) {
            case self::MODE_PROCESS:
                return new ProcessTaskRunner($options);
            case self::MODE_CURL_MULTI:
                return new CurlMultiTaskRunner($options);
            case self::MODE_SERIAL:
            default:
                return new SerialTaskRunner($options);
        }
    }

    public function getMode() {
        return $this->mode;
    }

    public function getActualMode() {
        return $this->runner->getMode();
    }

    public function setConcurrency($concurrency) {
        $this->runner->setConcurrency($concurrency);
    }

    public function setTimeout($timeout) {
        $this->runner->setTimeout($timeout);
    }

    public function run(array $tasks, $handler) {
        return $this->runner->run($tasks, $handler);
    }

    public static function getAvailableModes() {
        $modes = [self::MODE_SERIAL];
        if (CurlMultiTaskRunner::isAvailable()) {
            $modes[] = self::MODE_CURL_MULTI;
        }
        if (ProcessTaskRunner::isAvailable()) {
            $modes[] = self::MODE_PROCESS;
        }
        return $modes;
    }

    public static function isMultiThreadAvailable() {
        return CurlMultiTaskRunner::isAvailable() || ProcessTaskRunner::isAvailable();
    }

    public static function getRecommendedMode() {
        if (PHP_SAPI === 'cli' && ProcessTaskRunner::isAvailable()) {
            return self::MODE_PROCESS;
        }
        if (CurlMultiTaskRunner::isAvailable()) {
            return self::MODE_CURL_MULTI;
        }
        return self::MODE_SERIAL;
    }
}

class SerialTaskRunner implements TaskRunnerInterface {
    private $concurrency = 1;
    private $timeout = 60;
    private $mode = 'serial';

    public function __construct($options = []) {
        if (isset($options['timeout'])) {
            $this->timeout = max(1, intval($options['timeout']));
        }
    }

    public function setConcurrency($concurrency) {
        $this->concurrency = 1;
    }

    public function setTimeout($timeout) {
        $this->timeout = max(1, intval($timeout));
    }

    public function getMode() {
        return $this->mode;
    }

    public function run(array $tasks, $handler) {
        $results = [];
        foreach ($tasks as $idx => $task) {
            $start = microtime(true);
            try {
                $data = call_user_func($handler, $task, $idx);
                $duration = round((microtime(true) - $start) * 1000, 2);
                $results[] = TaskResult::success($task['id'] ?? $idx, $data, $duration);
            } catch (Throwable $e) {
                $duration = round((microtime(true) - $start) * 1000, 2);
                $results[] = TaskResult::failure($task['id'] ?? $idx, $e->getMessage(), $duration);
            }
        }
        return $results;
    }
}
