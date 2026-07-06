<?php

class TaskResult {
    public $success;
    public $data;
    public $error;
    public $taskId;
    public $duration;
    public $workerId;

    public function __construct($taskId = null) {
        $this->taskId = $taskId;
        $this->success = false;
        $this->data = null;
        $this->error = null;
        $this->duration = 0;
        $this->workerId = null;
    }

    public static function success($taskId, $data, $duration = 0) {
        $result = new self($taskId);
        $result->success = true;
        $result->data = $data;
        $result->duration = $duration;
        return $result;
    }

    public static function failure($taskId, $error, $duration = 0) {
        $result = new self($taskId);
        $result->success = false;
        $result->error = $error;
        $result->duration = $duration;
        return $result;
    }

    public function toArray() {
        return [
            'task_id' => $this->taskId,
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
            'duration' => $this->duration,
            'worker_id' => $this->workerId
        ];
    }
}
