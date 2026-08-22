<?php
/**
 * ParseResult —— 归一化解析结果模型
 *
 * 所有通道（m3u8 去广告 / 官方替换 / 上游官解）无论内部实现差异多大，
 * 最终都归一化成这一个结构，前端只认 code/url/…，便于维护与排查。
 *
 * @package parse
 * @since   5.13.9
 */
class ParseResult {

    /** @var int */
    public $code = 200;

    /** @var string 最终可播放地址（m3u8/直链） */
    public $url = '';

    /** @var string 官方源地址 */
    public $official_url = '';

    /** @var string 资源站替换源地址 */
    public $replace_url = '';

    /** @var string 剧名 */
    public $title = '';

    /** @var string 集数 */
    public $episode = '';

    /** @var string 消息 */
    public $message = '解析成功';

    /** @var bool */
    public $success = true;

    /** @var string 走哪条链路：m3u8_clean|official_replace|direct|timeout|error */
    public $channel = '';

    /** @var array step_trace 诊断时间线 */
    public $step_trace = [];

    /** @var array 附加调试信息 */
    public $debug = [];

    /** @var float 总耗时（秒） */
    public $elapsed = 0.0;

    /** @var array 去广告统计（由 cleaner 注入） */
    public $clean_stats = [];

    public function __construct(array $init = []) {
        foreach ($init as $k => $v) {
            if (property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    /** 快速失败结果 */
    public static function fail($code, $message, array $extra = []) {
        $r = new static(array_merge([
            'success' => false, 'code' => $code, 'message' => $message,
        ], $extra));
        return $r;
    }

    /**
     * 输出为 moxi 兼容的数组（含 jm/js/kfz 等旧字段）。
     */
    public function toArray() {
        return [
            'code'         => $this->code,
            'url'          => $this->url,
            'msg'          => $this->url ?: $this->message,
            'jm'           => $this->title,
            'js'           => $this->episode ?: '正片',
            'official_url' => $this->official_url,
            'replace_url'  => $this->replace_url,
            'channel'      => $this->channel,
            'success'      => $this->success,
            'message'      => $this->message,
            'time'         => date('Y-m-d H:i:s'),
            'elapsed'      => round($this->elapsed, 3),
            'step_trace'   => $this->step_trace,
            'clean_stats'  => $this->clean_stats,
            'debug'        => $this->debug,
            'kfz'          => '沫兮API',
        ];
    }
}