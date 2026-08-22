<?php
/**
 * BaseHandler —— 各 action handler 的基类
 *
 * 提供：
 *   ① 上下文访问（$this->ctx）
 *   ② JSON 输出（复用 mx.php 的 sendJsonResponse，兼容旧输出结构）
 *   ③ 预算保护（Timer：超预算快速失败，绝不卡死）
 *   ④ 官替解析（带预算兜底）
 *
 * @package handlers
 * @since   5.14.0
 */
class BaseHandler {

    /** @var HandlersContext */
    protected $ctx;

    public function __construct(HandlersContext $ctx) {
        $this->ctx = $ctx;
    }

    /** 读取参数（GET 优先） */
    protected function param($key, $default = '') {
        return $this->ctx->param($key, $default);
    }

    /** JSON 输出并退出（行为与旧 mx.php 一致） */
    protected function jsonOut($data, $code = 200) {
        sendJsonResponse($data, $code);
    }

    /**
     * 创建全局预算 Timer
     *
     * @param float|null $budget 指定预算（默认取全局预算）
     * @return Timer
     */
    protected function newTimer($budget = null) {
        $budget = $budget !== null ? (float)$budget : (float)$this->ctx->globalBudget;
        return new Timer($budget);
    }

    /**
     * 带预算的官替解析（治「解析卡死」）
     *
     * 复用已配置好代理/DB 的 $officialReplaceMgr；解析前为它设置内部预算，
     * 超预算由引擎返回 504 快速失败，绝不无限等待。
     *
     * @param string $url
     * @param float  $budgetSec
     * @return array
     */
    protected function resolveOfficial($url, $budgetSec = 22.0) {
        $mgr = $this->ctx->officialReplaceMgr;
        if (!$mgr) {
            return [
                'success' => false,
                'message' => '官替模块未初始化',
                'code' => 500,
                'step_trace' => ['official_replace: 未初始化'],
            ];
        }
        if (method_exists($mgr, 'setBudget')) {
            $mgr->setBudget($budgetSec);
        }
        try {
            return $mgr->resolve($url);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => '官替解析异常: ' . $e->getMessage(),
                'code' => 512,
                'step_trace' => ['official_replace: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * 调用 parse/ 框架门面（如已加载），返回 ParseResult 或 null（未加载）
     *
     * @param string $url
     * @param array  $opts force_channel / orm / self
     * @return ParseResult|null
     */
    protected function facade($url, array $opts = []) {
        if (!$this->ctx->parseLoaded || !class_exists('ParserFacade')) {
            return null;
        }
        try {
            $facade = new ParserFacade();
            $opts['orm'] = $this->ctx->officialReplaceMgr;
            $opts['self'] = SelfUrlHelper::base();
            return $facade->parse($url, $opts);
        } catch (\Throwable $e) {
            return ParseResult::fail(512, '解析门面异常: ' . $e->getMessage(), ['channel' => 'error']);
        }
    }
}
