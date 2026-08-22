<?php
/**
 * Timer —— 全局总预算计时器（治「解析卡死」）
 *
 * 一个很轻的硬截止（deadline）计时器：在任意网络循环的每次迭代
 * 调用 ok()，超预算立即返回 false，调用方据此“快速失败 + 明确 message”。
 *
 * 用法：
 *      $timer = new Timer(25.0);
 *      while (...) { if (!$timer->ok()) { return $timer->timeoutResult(); } ... }
 *
 * @package parse
 * @since   5.13.9
 */
class Timer {

    /** @var float 开始时间戳（微秒） */
    protected $started;

    /** @var float 总预算（秒） */
    protected $budget;

    /** @var float 软中断点（秒，= budget * ratio） */
    protected $softPoint;

    /** @var bool 是否已消耗超预算 */
    protected $exceeded = false;

    /** @var string|null 触发原因 */
    protected $reason = null;

    /** @var string[] 环节目志 */
    protected $trace = [];

    /**
     * @param float|int $budgetSec 总预算秒数（<=0 视为无限）
     */
    public function __construct($budgetSec = 25.0, $softRatio = 0.8) {
        $budgetSec = (float)$budgetSec;
        $this->budget = $budgetSec > 0 ? $budgetSec : 0.0;
        $this->softPoint = $this->budget > 0 ? $this->budget * (float)$softRatio : 0.0;
        $this->started = microtime(true);
        $this->trace[] = 'start:' . number_format($this->started, 4);
    }

    /** @return float 过去秒数 */
    public function elapsed() {
        return (float)(microtime(true) - $this->started);
    }

    /** @return float 剩余可用秒数（预算-已用），不足为 0 */
    public function remaining() {
        if ($this->budget <= 0) {
            return PHP_FLOAT_MAX;
        }
        return max(0.0, $this->budget - $this->elapsed());
    }

    /**
     * 是否仍在预算内（预算<=0 视为无限，恒返回 true）。
     */
    public function ok() {
        if ($this->budget <= 0) {
            return true;
        }
        if ($this->exceeded) {
            return false;
        }
        if ($this->elapsed() >= $this->budget) {
            $this->exceeded = true;
            $this->reason = '超出全局预算 ' . $this->budget . 's';
            return false;
        }
        return true;
    }

    /** 达到软中断点（仍有收尾余量） */
    public function isSoftPoint() {
        if ($this->budget <= 0 || $this->softPoint <= 0) {
            return false;
        }
        return $this->elapsed() >= $this->softPoint;
    }

    /** 显式标记超时（外部检测到更精确耗时后调用） */
    public function beat($why = '') {
        $this->exceeded = true;
        $this->reason = $why ?: '已超预算';
        $this->trace[] = 'beat:' . $why . '@' . number_format($this->elapsed(), 3) . 's';
    }

    /** 耗尽预算之前的推荐单次网络超时（秒） */
    public function sliceTimeout($defaultSec = 4.0) {
        $rem = $this->remaining();
        if ($rem === PHP_FLOAT_MAX) {
            return (float)$defaultSec;
        }
        return max(0.5, min((float)$defaultSec, $rem * 0.6));
    }

    /** 快速失败结果（调用方直接返回） */
    public function timeoutResult($message = '') {
        return [
            'success'   => false,
            'code'      => 504,
            'message'   => $message ?: ($this->reason ?? '解析超时：超全局预算'),
            'timed_out' => true,
            'elapsed'   => round($this->elapsed(), 3),
            'step_trace'=> $this->trace,
        ];
    }

    /** @return array 快照 */
    public function snapshot() {
        return [
            'budget'     => $this->budget,
            'elapsed'    => round($this->elapsed(), 3),
            'remaining'  => $this->budget > 0 ? round($this->remaining(), 3) : null,
            'exceeded'   => $this->exceeded,
            'reason'     => $this->reason,
            'trace'      => $this->trace,
        ];
    }
}