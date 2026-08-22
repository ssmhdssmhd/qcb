<?php
/**
 * ResourceFirstResolver —— 官方链接 → 资源站优先解析（带全局预算）
 *
 * 策略：官方视频页 → 经「官替」通道在资源站搜索匹配 → 得到可直接播放的 m3u8
 *       → 再交给 LocalM3u8Cleaner 去广告/去非正片。
 *
 * 框架第一版：在预算内尝试复用现有 OfficialReplaceManager::resolve()；
 * 若该重引擎尚未加载/不可用，则返回明确的“未接入”快速失败 + step_trace，
 * 保证链路绝不卡死、绝无未捕获异常。
 *
 * @package parse
 * @since   5.13.9
 */
class ResourceFirstResolver {

    /** @var array */
    protected $cfg;

    /** @var Timer|null */
    protected $timer;

    public function __construct(array $cfg = [], Timer $timer = null) {
        $this->cfg = $cfg;
        $this->timer = $timer;
    }

    /**
     * @param string $url
     * @return ParseResult
     */
    public function resolve($url) {
        $timer = $this->timer ?: new Timer((float)($this->cfg['global_budget'] ?? 25.0));
        $trace = [];
        $trace[] = 'resource_first:start url=' . $url;

        if (empty($this->cfg['official_replace']['enabled'])) {
            return ParseResult::fail(409, '官方替换通道已停用', ['channel' => 'official_replace', 'step_trace' => $trace]);
        }

        // 尝试复用重型官替引擎
        if (class_exists('OfficialReplaceManager')) {
            try {
                $orm = new OfficialReplaceManager();
                $result = $orm->resolve($url);
                $trace[] = 'resource_first:orm.resolve done=' . ($result['success'] ? 'true' : 'false');
                if (!empty($result['step_trace'])) {
                    $trace = array_merge($trace, (array)$result['step_trace']);
                }
                if (!empty($result['success']) && !empty($result['m3u8_url'])) {
                    $m3u8Url = $result['m3u8_url'];
                    // 再去广告
                    $cleaner = new LocalM3u8Cleaner($this->cfg);
                    $clean = $cleaner->clean($m3u8Url, ['url' => $url, 'self' => $ctxSelf ?? '']);
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
                        'clean_stats'  => $clean['success'] ? $clean['stats'] : [],
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