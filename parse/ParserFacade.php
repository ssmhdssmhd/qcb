<?php
/**
 * ParserFacade —— 统一解析门面（框架入口）
 *
 * 职责：
 *   ① URL 类型判定（m3u8 / 官方 / other）
 *   ② 开启全局预算 Timer
 *   ③ 按类型 dispatch 到对应解析器
 *   ④ 汇总为 ParseResult 归一化结果
 *
 * 任一环节超预算/异常都返回“快速失败 + 明确 message + step_trace”，绝不卡死。
 *
 * 用法：
 *      require __DIR__ . '/parse/autoload.php';
 *      $result = (new \ParserFacade())->parse('https://v.qq.com/.../xxx.html');
 *      echo json_encode($result->toArray());
 *
 * @package parse
 * @since   5.13.9
 */
class ParserFacade {

    /** @var array */
    protected $cfg;

    /** @var Timer|null */
    protected $timer;

    public function __construct(array $cfg = []) {
        $this->cfg = $cfg ?: (defined('PARSE_CFG_LOADED') ? $GLOBALS['_PARSE_CFG'] ?? [] : ($this->loadCfg()));
    }

    protected function loadCfg() {
        $f = __DIR__ . '/config.php';
        return is_file($f) ? (require $f) : [];
    }

    /**
     * 统一的解析入口。
     *
     * @param string $url
     * @param array  $opts { type?:string, self_url?:string, force_channel?:string }
     * @return ParseResult
     */
    public function parse($url, array $opts = []) {
        $url = trim((string)$url);
        $t0 = microtime(true);

        if ($url === '') {
            return ParseResult::fail(400, '缺少 url 参数', ['channel' => 'error']);
        }

        $timer = $this->timer = new Timer((float)($this->cfg['global_budget'] ?? 25.0));
        $classifier = new UrlClassifier($this->cfg);
        $info = $classifier->classify($url);

        // 强制指定通道（便于调试/前端指定）
        $force = $opts['force_channel'] ?? '';

        try {
            if ($force === 'm3u8_clean' || $info['type'] === 'm3u8' || $info['type'] === 'other') {
                $result = $this->dispatchM3u8($url);
            } elseif ($force === 'official_replace' || $info['type'] === 'official') {
                $result = $this->dispatchOfficial($url);
            } else {
                $result = ParseResult::fail(400, '无法识别的 URL 类型', ['channel' => 'error', 'debug' => $info]);
            }
        } catch (\Throwable $e) {
            $result = ParseResult::fail(512, '解析异常: ' . $e->getMessage(), ['channel' => 'error']);
        }

        if (!$timer->ok() && $result->success) {
            $result = ParseResult::fail(504, '解析超时：超全局预算', ['channel' => 'timeout']);
        }
        $result->elapsed = round(microtime(true) - $t0, 3);
        $result->debug['url_type'] = $info['type'];
        $result->debug['host'] = $info['host'];
        return $result;
    }

    /** m3u8 / 直接视频 → 本地去广告 */
    protected function dispatchM3u8($url) {
        $cleaner = new LocalM3u8Cleaner($this->cfg);
        $clean = $cleaner->clean($url, ['url' => $url]);
        if (!$clean['success']) {
            return ParseResult::fail(500, $clean['message'], ['channel' => 'm3u8_clean', 'step_trace' => ['clean:' . $clean['message']]]);
        }
        return new ParseResult([
            'success'      => true,
            'code'         => 200,
            'url'          => $url,          // 仍回原始 m3u8（占位由代理 mxjx 层消费）
            'official_url' => $url,
            'title'        => $this->titleGuess($url),
            'episode'      => '正片',
            'channel'      => 'm3u8_clean',
            'step_trace'   => ['m3u8_clean:segment=' . $clean['total_count'] . ' ad=' . $clean['ad_count'] . ' placeholder=' . $clean['placeholder_count']],
            'clean_stats'  => $clean['stats'],
            'debug'        => ['cleaned_playlist' => $clean['playlist']],
        ]);
    }

    /** 官方链接 → 资源站优先 */
    protected function dispatchOfficial($url) {
        $resolver = new ResourceFirstResolver($this->cfg, $this->timer);
        return $resolver->resolve($url);
    }

    /** 极简标题猜测 */
    protected function titleGuess($url) {
        $host = (string)parse_url($url, PHP_URL_HOST);
        if ($host === '') {
            return '在线视频';
        }
        $host = trim(preg_replace('/^www\./', '', $host));
        $parts = explode('.', $host);
        return ucfirst($parts[0] ?? $host);
    }
}