<?php
/**
 * ResourceFirstResolver —— 官方链接 → 资源站优先解析（带全局预算）
 *
 * 策略：官方视频页 → 经「官替」通道在资源站搜索匹配 → 得到可直接播放的 m3u8
 *       → 可选再交给 LocalM3u8Cleaner 去广告/去非正片（skip_clean=true 时跳过，
 *         由 mxjx 在播放时消费占位）。
 *
 * 任一步骤超预算/异常都返回“快速失败 + 明确 message + step_trace”，绝不卡死。
 * 支持注入外部已配置好代理/DB 的 OfficialReplaceManager 实例（orm）。
 *
 * @package parse
 * @since   5.13.9
 */
class ResourceFirstResolver {

    /** @var array */
    protected $cfg;

    /** @var Timer|null */
    protected $timer;

    /** @var object|null 注入的官替管理器实例 */
    protected $orm;

    public function __construct(array $cfg = [], Timer $timer = null, $orm = null) {
        $this->cfg = $cfg;
        $this->timer = $timer;
        $this->orm = $orm;
    }

    /**
     * @param string $url
     * @param array  $opts { skip_clean?:bool }
     * @return ParseResult
     */
    public function resolve($url, array $opts = []) {
        $timer = $this->timer ?: new Timer((float)($this->cfg['global_budget'] ?? 25.0));
        $trace = [];
        $trace[] = 'resource_first:start url=' . $url;

        if (empty($this->cfg['official_replace']['enabled'])) {
            return ParseResult::fail(409, '官方替换通道已停用', ['channel' => 'official_replace', 'step_trace' => $trace]);
        }

        $orm = $this->orm;
        $useInjected = $orm !== null;

        // 尝试复用重型官替引擎（优先注入实例，其次自建）
        if ($useInjected || class_exists('OfficialReplaceManager')) {
            try {
                if (!$useInjected) {
                    $orm = new OfficialReplaceManager();
                }
                // 为重型引擎注入内部预算（治卡死），取官替通道预算
                $attemptBudget = (float)($this->cfg['official_replace']['attempt_budget'] ?? 22.0);
                if (method_exists($orm, 'setBudget')) {
                    $orm->setBudget($attemptBudget);
                }
                $result = $orm->resolve($url);
                $trace[] = 'resource_first:orm.resolve done=' . ($result['success'] ? 'true' : 'false');
                if (!empty($result['step_trace'])) {
                    $trace = array_merge($trace, (array)$result['step_trace']);
                }
                if (!empty($result['success']) && !empty($result['m3u8_url'])) {
                    $m3u8Url = $result['m3u8_url'];
                    $cleanStats = [];
                    if (empty($opts['skip_clean'])) {
                        // 再去广告（预览用，失败不阻塞）
                        $cleaner = new LocalM3u8Cleaner($this->cfg);
                        $clean = $cleaner->clean($m3u8Url, ['url' => $url, 'self' => $opts['self'] ?? '']);
                        $cleanStats = $clean['success'] ? $clean['stats'] : [];
                        $trace[] = 'resource_first:local_clean done=' . ($clean['success'] ? 'true' : 'false');
                    }
                    return new ParseResult([
                        'success'      => true,
                        'code'         => 200,
                        'url'          => $m3u8Url,
                        'replace_url'  => $m3u8Url,
                        'official_url' => $url,
                        'title'        => $result['video_title'] ?? '',
                        'episode'      => $result['target_episode'] ?? ($result['episode'] ?? ''),
                        'channel'      => 'official_replace',
                        'step_trace'   => $trace,
                        'clean_stats'  => $cleanStats,
                        'elapsed'      => round($timer->elapsed(), 3),
                    ]);
                }
                return ParseResult::fail(512, $result['message'] ?? '官替未匹配到片源', [
                    'channel' => 'official_replace', 'step_trace' => $trace,
                ]);
            } catch (\Throwable $e) {
                $trace[] = 'resource_first:error ' . $e->getMessage();
                if (!$timer->ok()) {
                    return ParseResult::fail(504, '解析超时：超全局预算', ['channel' => 'official_replace', 'step_trace' => $trace]);
                }
                return ParseResult::fail(512, '官替引擎异常: ' . $e->getMessage(), ['channel' => 'official_replace', 'step_trace' => $trace]);
            }
        }

        // 重引擎未接入 → 明确快速失败（带预算保护）
        $trace[] = 'resource_first:OfficialReplaceManager 未接入，返回快速失败';
        return ParseResult::fail(501, '官方替换引擎未接入，框架骨架已就绪', [
            'channel' => 'official_replace', 'step_trace' => $trace,
        ]);
    }
}
