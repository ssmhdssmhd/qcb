<?php

interface TaskRunnerInterface {
    public function setConcurrency($concurrency);
    public function setTimeout($timeout);
    public function run(array $tasks, $handler);
    public function getMode();
}
