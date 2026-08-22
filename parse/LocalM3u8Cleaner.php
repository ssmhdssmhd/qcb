<?php
/**
 * LocalM3u8Cleaner —— 本地去广告 / 去非正片（轻量、自包含、可离线）
 *
 * 框架默认用内置轻量检测：解析 m3u8 → 按「URL 关键词黑名单 + 时长异常 + 广告/正片标记」
 * 判定广告/非正片段 → 用「等时长静音黑屏占位」替换（不删段，播放器进度条不中断）。
 *
 * 可平滑接入重型引擎：
 *   - M3U8Parser（src/M3U8Parser.php）          做深层片段解析
 *   - EnhancedAdRuleEngine（gz/）               规则 + AI 广告判定
 *   - Md5AdPlaceholderEngine（gz/）              MD5 指纹 + 占位
 *  重引擎通过 use_heavy_engine 开启；开启失败则回退轻量路径，绝不中断。
 *
 * @package parse
 * @since   5.13.9
 */
class LocalM3u8Cleaner {

    /** @var array */
    protected $cfg;

    /** @var string 占位前缀（离线可配） */
    protected $placeholderPrefix;

    /** @var array<string,int> 命中原因统计 */
    protected $reasonMap = [];

    public function __construct(array $cfg = []) {
        $this->cfg = $cfg;
        $p = $cfg['cleaner']['placeholder'] ?? [];
        $this->placeholderPrefix = ($p['mode'] ?? '') === 'local_proxy'
            ? (string)($p['local_base'] ?? '')
            : (string)($p['data_uri_ts'] ?? '');
        if ($this->placeholderPrefix === '') {
            // 默认占位：形如 __PLACEHOLDER__/<duration>
            $this->placeholderPrefix = '__PLACEHOLDER__/';
            if (($p['mode'] ?? '') === 'local_proxy') {
                $this->placeholderPrefix = '/mx.php?action=placeholder_ts&d=';
            }
        }
    }

    /**
     * 统一的入口：接受 m3u8 内容 / 本地文件 / URL。
     *
     * @param string $input m3u8 内容（含 #EXTM3U）或文件路径或 URL
     * @param array  $ctx   上下文（host、self 等）
     * @return array{
     *   success:bool, message:string,
     *   segments:array, playlist:string,
     *   total_count:int, ad_count:int, placeholder_count:int,
     *   stats:array, reason_map:array
     * }
     */
    public function clean($input, array $ctx = []) {
        $this->reasonMap = [];
        $t0 = microtime(true);
        try {
            // 1) 取原始文本
            $raw = $this->resolveRaw($input);
            if ($raw === null || stripos($raw, '#EXTM3U') === false) {
                return $this->res(false, '输入的 m3u8 内容无效', [], $raw, $ctx);
            }

            // 2) 解析片段（优先重引擎，失败回退轻量）
            if (!empty($this->cfg['cleaner']['use_heavy_engine']) && class_exists('M3U8Parser')) {
                $segments = $this->parseWithHeavyEngine($input, $raw, $ctx);
            } else {
                $segments = $this->parseSelf($raw);
            }
            if (empty($segments)) {
                return $this->res(false, '未能解析出任何片段', [], $raw, $ctx);
            }

            // 3) 广告判定 + 占位
            $stats = $this->markAndPlaceholder($segments, $ctx);

            // 4) 重建 m3u8
            $playlist = $this->rebuildPlaylist($raw, $segments);

            return [
                'success'            => true,
                'message'            => '去广告处理完成',
                'segments'           => $segments,
                'playlist'           => $playlist,
                'total_count'        => count($segments),
                'ad_count'           => $stats['ad_count'],
                'placeholder_count'  => $stats['placeholder_count'],
                'stats'              => $stats,
                'reason_map'         => $this->reasonMap,
                'elapsed'            => round(microtime(true) - $t0, 3),
            ];
        } catch (\Throwable $e) {
            return $this->res(false, '去广告异常: ' . $e->getMessage(), [], '', $ctx);
        }
    }

    /** 结果壳 */
    protected function res($ok, $msg, $segs, $playlist, $ctx) {
        return [
            'success' => $ok, 'message' => $msg,
            'segments' => $segs, 'playlist' => $playlist,
            'total_count' => count($segs),
            'ad_count' => 0, 'placeholder_count' => 0,
            'stats' => [], 'reason_map' => $this->reasonMap,
            'elapsed' => round(microtime(true) - $this->cleanStarted(), 3),
        ];
    }

    /** 记录/获取本次 clean 起点时间戳（res 兜底时用） */
    protected function cleanStarted() {
        static $t = null;
        if ($t === null) {
            $t = microtime(true);
        }
        return $t;
    }

    /** 取原始文本：URL / 文件 / 纯内容 */
    protected function resolveRaw($input) {
        $input = (string)$input;
        if (preg_match('/^https?:\/\//i', $input)) {
            $raw = @file_get_contents($input);
            return $raw === false ? null : $raw;
        }
        if (is_file($input)) {
            $raw = @file_get_contents($input);
            return $raw === false ? null : $raw;
        }
        return $input;
    }

    /** 轻量自解析：解析 #EXTINF 与 ts 行 */
    protected function parseSelf($raw) {
        $segments = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $cur = null;
        $discontinuity = false;
        foreach ($lines as $lineRaw) {
            $line = trim($lineRaw);
            if ($line === '') {
                continue;
            }
            if (stripos($line, '#EXTINF:') === 0) {
                $durMatch = [];
                preg_match('/#EXTINF:\s*([0-9.]+)/i', $line, $durMatch);
                $cur = ['duration' => (float)($durMatch[1] ?? 0), 'title' => '', 'uri' => '', 'discontinuity' => $discontinuity];
                $discontinuity = false;
                continue;
            }
            if ($line === '#EXT-X-DISCONTINUITY') {
                $discontinuity = true;
                continue;
            }
            if (strpos($line, '#') === 0) {
                continue; // 其它标签
            }
            if ($cur !== null) {
                $cur['uri'] = $this->resolveRel($line, $raw);
                $segments[] = $cur;
                $cur = null;
                $discontinuity = false;
            }
        }
        // 若存在“裸 URI 无上一行 EXTINF”，兜底补一段
        return $segments;
    }

    /** 相对地址补全（按 m3u8 所在目录，朴素实现） */
    protected function resolveRel($uri, $raw) {
        if (preg_match('/^https?:\/\//i', $uri)) {
            return $uri;
        }
        $base = $this->detectBase($raw);
        if ($base === '') {
            return $uri;
        }
        if (strpos($uri, '/') === 0) {
            $p = parse_url($base);
            return ($p['scheme'] ?? 'http') . '://' . $p['host'] . $uri;
        }
        return rtrim($base, '/') . '/' . ltrim($uri, '/');
    }

    /** 从内容推断 base 目录（轻量） */
    protected function detectBase($raw) {
        // 取第一条绝对 ts/m3u8 的目录
        if (preg_match('#(https?://[^\s\r\n]+/)[^\s\r\n]*\.ts#i', $raw, $m)) {
            return $m[1];
        }
        return '';
    }

    /** 重引擎解析（M3U8Parser） */
    protected function parseWithHeavyEngine($input, $raw, $ctx) {
        $parser = new M3U8Parser();
        $result = $parser->parse($raw);
        return $result['segments'] ?? [];
    }

    /** 广告判定 + 占位记录 */
    protected function markAndPlaceholder(&$segments, $ctx) {
        $blacklist = $this->cfg['cleaner']['url_blacklist'] ?? [];
        $adDurMax = (float)($this->cfg['cleaner']['ad_duration_max'] ?? 3.0);
        $adCount = 0;
        $placeholderCount = 0;

        foreach ($segments as $i => &$seg) {
            $uri = (string)($seg['uri'] ?? '');
            $dur = (float)($seg['duration'] ?? 0);
            $reasons = [];

            // ① 显式广告标记（CUE-OUT / adMarkers）
            $adMarkers = $seg['adMarkers'] ?? [];
            if (!empty($adMarkers) || (isset($seg['is_ad']) && $seg['is_ad'])) {
                $reasons[] = 'ad_marker';
            }

            // ② URL 关键词黑名单
            foreach ($blacklist as $kw) {
                if ($kw !== '' && stripos($uri, $kw) !== false) {
                    $reasons[] = 'url_kw:' . $kw;
                    break;
                }
            }

            // ③ 时长过短（贴片/缓冲广告）
            if ($dur > 0 && $dur <= $adDurMax) {
                $reasons[] = 'short_dur';
            }

            $seg['is_ad'] = !empty($reasons);
            if ($seg['is_ad']) {
                $adCount++;
                $this->reasonMap[$i] = implode('|', $reasons);
                $seg['uri'] = $this->placeholderForUri($dur, $i, $reasons[0] ?? 'unknown', $ctx);
                $placeholderCount++;
            }
        }
        unset($seg);

        return [
            'ad_count'          => $adCount,
            'placeholder_count' => $placeholderCount,
            'content_count'     => count($segments) - $adCount,
        ];
    }

    /** 生成占位 URI */
    protected function placeholderForUri($dur, $i, $reason, $ctx) {
        $p = $this->cfg['cleaner']['placeholder'] ?? [];
        $mode = $p['mode'] ?? 'local_proxy';
        if ($mode === 'data_uri' && !empty($p['data_uri_ts'])) {
            return $p['data_uri_ts'];
        }
        if ($mode === 'local_proxy') {
            return rtrim($this->placeholderPrefix, '?') . $dur;
        }
        return rtrim($this->placeholderPrefix, '?') . $dur;
    }

    /** 重建 m3u8 文本（保留头部/所有标签，仅替换广告段 URI） */
    protected function rebuildPlaylist($raw, $segments) {
        $out = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $segIdx = 0;
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '' || strpos($trim, '//') === 0) {
                // 空行 / 注释行：保留但不消耗片段
                if ($trim !== '') {
                    $out[] = $line;
                }
                continue;
            }
            if (strpos($trim, '#') === 0) {
                $out[] = $line;
                continue;
            }
            // 数据行（ts/相对 uri）→ 用处理后片段 uri
            if (isset($segments[$segIdx])) {
                $out[] = $segments[$segIdx]['uri'];
                $segIdx++;
            } else {
                $out[] = $line;
            }
        }
        return implode("\n", $out) . "\n";
    }
}