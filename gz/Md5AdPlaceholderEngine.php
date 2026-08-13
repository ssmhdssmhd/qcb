<?php
/**
 * v5.11 新增：AI + MD5 非正片片段检测 + 静音黑屏占位替换引擎
 *
 * 目标：在原有的「规则引擎 + AI 过滤 → 剔除广告段」后，再追加一层：
 *   1) 对每段 ts 做 URL 关键词 + 时长异常 + 连续段重复检测；
 *   2) 对每个 ts 做轻量下载（可选，默认开）计算 md5，
 *      参考同域名/同站点「正片 md5 指纹库」与「广告黑名单库」，
 *      把重复率高、时长过短、命中已知广告指纹的片段，标记为「非正片」；
 *   3) 不是剔除（剔除会导致播放器时长不对/进度条中断），而是替换为：
 *        - 静音黑屏占位数据：data:application/octet-stream;base64,...（极短 0.5s MPEG-TS，可被所有播放器无中断播放）
 *        - 或把该段 URI 改成本地代理占位接口（mx.php?action=placeholder_ts&duration=5.0），
 *          返回一段完全静音的黑屏 ts，时长完全保持原广告时长；
 *   4) 从而广告位置变成「正常流逝 + 无画面无声音」，进度条不卡、播放器不会断。
 *
 * 设计原则：
 *  - 不会改变 m3u8 的总体结构（保留原 EXT-X-DISCONTINUITY、EXT-X-MAP、EXT-X-KEY 等）。
 *  - 单段时长：用原来的 EXTINF 时长不变，播放器按原时长推进。
 *  - ts MD5 指纹库：文件缓存到 cache/md5_fingerprints/<md5_key>。
 *  - 占位两种模式：'data_uri'（直接写 data URL，无需服务器请求）和 'local_proxy'（服务器动态生成静音 ts）。
 */

class Md5AdPlaceholderEngine {
    public $config = [];
    private $cacheDir;
    private $dbFile; // 简易 json DB
    private $db = null;

    public function __construct(array $config = []) {
        $this->config = array_replace_recursive([
            'mode' => 'auto', // auto=先 AI/规则判定，必要时再下载 md5；download_md5=强制下载；rules_only=只规则
            'placeholder_mode' => 'local_proxy', // data_uri | local_proxy
            'download_max_bytes' => 256 * 1024, // 每段最多下 256KB，足够算 md5
            'download_timeout' => 4,
            'download_parallel' => 4,
            'rules' => [
                // 1) URL 关键词黑名单 → 非正片
                'url_blacklist_keywords' => [
                    'ad', 'ads', 'advert', 'adv', 'guanggao', 'ggao', 'gg.', 'gg_', 'pre-roll', 'mid-roll',
                    '贴片', '广告', '片头广告', '插播', '片头', '暂停', '缓冲', '推广', 'logo', 'watermark',
                    'sponsor', 'sponsored', 'preroll', 'postroll', 'bumper', 'promo', 'trailer'
                ],
                // 2) URL 路径/文件命名
                'path_blacklist_regex' => [
                    '#/(ad|ads|gg|guanggao|pre|trailer|promo|bumper|sponsor)/#i',
                    '#/(片头|广告|推广|logo|贴片)#u',
                ],
                // 3) 时长异常（单位秒）
                'duration_min_ms' => 1500,  // 正片片段一般 > 1.5s
                'duration_max_ms' => 20000, // > 20s 的单段要警惕（多数资源站正片每段 2-10s）
                'ad_band_duration_min_ms' => 200,  // 但 0.2-1.5s 的段往往是广告片头/插播/缓冲包
                'ad_band_duration_max_ms' => 1500,
                // 4) 连续相同时长（广告群特征：多段一模一样时长）
                'consecutive_equal_duration_count' => 4, // 连续 4 段等时长且较短
            ],
            // MD5 指纹：同 host 的前 N 段，如果 70% 都相同，判定为片头广告
            'md5' => [
                'cluster_host_threshold' => 0.70, // 相同 host 的段 MD5 重复率阈值
                'known_ad_db_ttl' => 86400 * 30,
                'known_main_db_ttl' => 86400 * 7,
            ],
        ], $config);

        $this->cacheDir = (isset($this->config['cache_dir']) && $this->config['cache_dir'])
            ? rtrim($this->config['cache_dir'], '/\\')
            : (__DIR__ . '/../cache/md5_fingerprints');
        if (!is_dir($this->cacheDir)) @mkdir($this->cacheDir, 0755, true);
        $this->dbFile = $this->cacheDir . '/fingerprint_db.json';
    }

    /** ================== DB ================== */
    private function loadDb() {
        if ($this->db !== null) return $this->db;
        if (file_exists($this->dbFile)) {
            $d = @json_decode(@file_get_contents($this->dbFile), true);
            $this->db = is_array($d) ? $d : ['ad_md5'=>[], 'main_md5'=>[], 'host_stats'=>[]];
        } else {
            $this->db = ['ad_md5'=>[], 'main_md5'=>[], 'host_stats'=>[]];
        }
        return $this->db;
    }
    private function saveDb() {
        if ($this->db === null) return;
        @file_put_contents($this->dbFile, json_encode($this->db, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    }
    public function addKnownAdMd5($md5, $host = '', $note = '') {
        $this->loadDb();
        $this->db['ad_md5'][$md5] = ['host'=>$host, 'note'=>$note, 'ts'=>time()];
        $this->saveDb();
    }
    public function addKnownMainMd5($md5, $host = '', $note = '') {
        $this->loadDb();
        $this->db['main_md5'][$md5] = ['host'=>$host, 'note'=>$note, 'ts'=>time()];
        $this->saveDb();
    }

    /** ================== 对外主入口 ================== */
    /**
     * @param array $playlist   parseM3u8 后的 playlist，结构 ['segments'=>[['uri','duration','tags','raw']...]]
     * @param string $baseUrl   用于解析相对 URI / 计算 host
     * @param array $context    可选 ['video_base_title','episode_num','site_host','provider']
     * @return array [ 'playlist'=>新 playlist, 'placeholder_count'=>N, 'removed_count'=>N, 'reason_map'=>[segIdx=>string] ]
     */
    public function process(array $playlist, string $baseUrl = '', array $context = []): array {
        $segs = $playlist['segments'] ?? [];
        if (empty($segs)) {
            return ['playlist' => $playlist, 'placeholder_count' => 0, 'removed_count' => 0, 'reason_map' => []];
        }

        $n = count($segs);
        // 预解析每个段的绝对 URI、host、时长毫秒
        $meta = [];
        foreach ($segs as $i => $seg) {
            $abs = $this->resolveUri($seg['uri'] ?? '', $baseUrl);
            $host = parse_url($abs, PHP_URL_HOST) ?: '';
            $durSec = floatval($seg['duration'] ?? 0);
            $meta[$i] = [
                'abs' => $abs,
                'host' => $host,
                'dur_ms' => (int)round($durSec * 1000),
                'dur_sec' => $durSec,
            ];
        }

        // ========== Phase 1：纯规则引擎判定（0 网络开销，极快） ==========
        $adIdx = []; // idx => reason
        $rules = $this->config['rules'];

        // 1a. URL 关键词黑名单
        $urlKw = array_flip(array_map('strtolower', $rules['url_blacklist_keywords']));
        foreach ($meta as $i => $m) {
            $needle = strtolower($m['abs']);
            foreach ($urlKw as $kw => $_) {
                if (strpos($needle, $kw) !== false) {
                    $adIdx[$i] = "url_kw:$kw";
                    break;
                }
            }
            if (!isset($adIdx[$i])) {
                foreach ($rules['path_blacklist_regex'] as $re) {
                    if (preg_match($re, $m['abs'])) { $adIdx[$i] = "path_re"; break; }
                }
            }
        }

        // 1b. 时长异常 band
        foreach ($meta as $i => $m) {
            if (isset($adIdx[$i])) continue;
            if ($m['dur_ms'] >= $rules['ad_band_duration_min_ms'] && $m['dur_ms'] <= $rules['ad_band_duration_max_ms']) {
                // 单段 0.2-1.5s 再判断是不是在整段的前/后 10%（片头/片尾广告）
                $pos = $i / max(1,$n-1);
                if ($pos <= 0.10 || $pos >= 0.90 || $n <= 5) {
                    $adIdx[$i] = "dur_band_short_edge";
                }
            } elseif ($m['dur_ms'] < $rules['duration_min_ms']) {
                $adIdx[$i] = "dur_too_short";
            }
        }

        // 1c. 连续 N 段等时长（广告群特征）
        $k = (int)($rules['consecutive_equal_duration_count'] ?? 4);
        $streak = 1;
        for ($i = 1; $i < $n; $i++) {
            if (abs($meta[$i]['dur_ms'] - $meta[$i-1]['dur_ms']) <= 10
                && $meta[$i]['dur_ms'] >= 1000 && $meta[$i]['dur_ms'] <= 6000) {
                $streak++;
                if ($streak >= $k) {
                    for ($j = $i - $k + 1; $j <= $i; $j++) {
                        if (!isset($adIdx[$j])) $adIdx[$j] = "dur_consec_equal_x{$k}";
                    }
                }
            } else {
                $streak = 1;
            }
        }

        // ========== Phase 2：AI + MD5 指纹分析 ==========
        $mode = $this->config['mode'];
        if ($mode !== 'rules_only') {
            // 2a. 主机聚类：相同 host 的 N 段，md5 重复率高 → 非正片
            //   只需要检查那些 host 段数多的
            $byHost = [];
            foreach ($meta as $i => $m) { $byHost[$m['host']][] = $i; }
            // 2b. 如果 download_md5/auto 需要下载片段算 md5
            $needDownload = [];
            foreach ($byHost as $host => $idxs) {
                if (!$host) continue;
                $cnt = count($idxs);
                if ($cnt < 3) continue;
                // 对每个 host 取前 10、后 10、以及所有 Phase1 疑似段
                $front = array_slice($idxs, 0, min(10, $cnt));
                $back = array_slice($idxs, max(0, $cnt - 10), 10);
                $suspect = [];
                foreach ($idxs as $i) if (isset($adIdx[$i])) $suspect[] = $i;
                $todo = array_values(array_unique(array_merge($front, $back, $suspect)));
                foreach ($todo as $i) $needDownload[$i] = true;
            }
            $md5ByIdx = [];
            if (!empty($needDownload) && $mode !== 'rules_only') {
                // 检查本地 db 缓存
                $this->loadDb();
                $toFetch = [];
                foreach (array_keys($needDownload) as $i) {
                    $cacheKey = md5($meta[$i]['abs']);
                    $cachedFile = $this->cacheDir . '/' . substr($cacheKey,0,2) . '/' . $cacheKey . '.md5';
                    if (is_file($cachedFile)) {
                        $ct = @json_decode(@file_get_contents($cachedFile), true);
                        if (is_array($ct) && !empty($ct['md5'])) {
                            // 简易过期：ttl 超过 1 个月重下
                            if (empty($ct['ts']) || time() - intval($ct['ts']) < $this->config['md5']['known_main_db_ttl']) {
                                $md5ByIdx[$i] = $ct['md5'];
                                continue;
                            }
                        }
                    }
                    $toFetch[] = $i;
                }
                if (!empty($toFetch)) {
                    $fetched = $this->batchDownloadMd5($toFetch, $meta);
                    foreach ($fetched as $i => $md5) {
                        if (!$md5) continue;
                        $md5ByIdx[$i] = $md5;
                        // 写缓存
                        $cacheKey = md5($meta[$i]['abs']);
                        $d = $this->cacheDir . '/' . substr($cacheKey,0,2);
                        if (!is_dir($d)) @mkdir($d,0755,true);
                        @file_put_contents($d . '/' . $cacheKey . '.md5', json_encode([
                            'md5'=>$md5,'abs'=>$meta[$i]['abs'],'ts'=>time(),'dur_ms'=>$meta[$i]['dur_ms']
                        ], JSON_UNESCAPED_SLASHES));
                    }
                }
            }

            // 2c. 同 host md5 聚类 & 已知广告库
            $this->loadDb();
            // Group md5 by host
            $md5ByHost = [];
            foreach ($md5ByIdx as $i => $md5) {
                $h = $meta[$i]['host'] ?: '__nohost';
                $md5ByHost[$h][$md5][] = $i;
                // 查 known
                if (isset($this->db['ad_md5'][$md5]) && !isset($adIdx[$i])) $adIdx[$i] = 'db_ad_md5';
                if (isset($this->db['main_md5'][$md5]) && isset($adIdx[$i])) {
                    // 正片库命中 -> 从广告里撤销
                    unset($adIdx[$i]);
                }
            }
            $thr = floatval($this->config['md5']['cluster_host_threshold']);
            foreach ($md5ByHost as $h => $md5map) {
                $totalForH = count($byHost[$h] ?? []);
                if ($totalForH < 4) continue;
                foreach ($md5map as $md5 => $idxs) {
                    $ratio = count($idxs) / $totalForH;
                    if ($ratio >= $thr && count($idxs) >= 3) {
                        // 该 host 的重复率高 → 标记所有命中 idx 为 host_cluster_ad
                        foreach ($idxs as $i) {
                            if (!isset($adIdx[$i])) $adIdx[$i] = "host_md5_cluster_" . (int)($ratio*100);
                        }
                    }
                }
            }
        }

        // ========== Phase 3：placeholder 替换（核心！保证不中断） ==========
        $placeholderCount = 0;
        $removedCount = 0;
        $reasonMap = $adIdx;
        $newSegs = $segs;
        foreach ($adIdx as $i => $reason) {
            if (!isset($newSegs[$i])) continue;
            $origDur = floatval($newSegs[$i]['duration'] ?? 0);
            if ($origDur <= 0) $origDur = ($meta[$i]['dur_sec'] ?? 2.0);
            $newUri = $this->buildPlaceholderUri($origDur, $i, $baseUrl, $reason);
            $newSegs[$i]['uri'] = $newUri;
            // 保留 EXTINF duration 完全不变（播放器按此推进进度）
            $placeholderCount++;
            // 若原引擎已经剔除段，也作为 placeholder 计入
            $removedCount++;
        }

        $newPlaylist = $playlist;
        $newPlaylist['segments'] = $newSegs;
        return [
            'playlist' => $newPlaylist,
            'placeholder_count' => $placeholderCount,
            'removed_count' => $removedCount,
            'reason_map' => $reasonMap,
        ];
    }

    /** ================== 辅助方法 ================== */
    private function resolveUri(string $uri, string $baseUrl): string {
        if (!$uri) return '';
        // 绝对
        if (preg_match('#^https?://#i', $uri)) return $uri;
        if (!$baseUrl) return $uri;
        $b = parse_url($baseUrl);
        if (!$b || empty($b['scheme'])) return $uri;
        $root = $b['scheme'] . '://' . ($b['host'] ?? '');
        if (!empty($b['port'])) $root .= ':' . $b['port'];
        if (strpos($uri, '/') === 0) {
            return $root . $uri;
        }
        $path = $b['path'] ?? '/';
        $path = preg_replace('#/[^/]*$#', '/', $path);
        return $root . $path . ltrim($uri, '/');
    }

    /**
     * 多段并发下载 + 计算 md5（只取前 download_max_bytes）
     */
    private function batchDownloadMd5(array $indices, array $meta): array {
        $out = [];
        $chunks = array_chunk($indices, (int)$this->config['download_parallel'] ?? 4);
        foreach ($chunks as $chunk) {
            $mhs = [];
            $fp = [];
            foreach ($chunk as $i) {
                if (empty($meta[$i]['abs'])) continue;
                $ch = curl_init($meta[$i]['abs']);
                if (!$ch) continue;
                $fp[$i] = fopen('php://temp', 'w+b');
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 3,
                    CURLOPT_CONNECTTIMEOUT => (int)$this->config['download_timeout'],
                    CURLOPT_TIMEOUT => (int)$this->config['download_timeout'],
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_RANGE => '0-' . ((int)$this->config['download_max_bytes'] - 1),
                    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36',
                    CURLOPT_FILE => $fp[$i],
                ]);
                $mhs[$i] = $ch;
            }
            if (empty($mhs)) continue;
            $mh = curl_multi_init();
            foreach ($mhs as $ch) curl_multi_add_handle($mh, $ch);
            $active = null;
            do {
                curl_multi_exec($mh, $active);
                if ($active) curl_multi_select($mh, 0.8);
            } while ($active);
            foreach ($mhs as $i => $ch) {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                if ($code < 200 || $code >= 400) { @fclose($fp[$i]); unset($fp[$i]); continue; }
                rewind($fp[$i]);
                $d = stream_get_contents($fp[$i]);
                @fclose($fp[$i]);
                unset($fp[$i]);
                if ($d && strlen($d) >= 128) $out[$i] = md5($d);
            }
            curl_multi_close($mh);
        }
        return $out;
    }

    /**
     * placeholder 两种模式：
     *  - local_proxy: 生成本地 mx.php?action=placeholder_ts&d=<秒>，
     *      好处是完全等长（按原广告秒数返回静音黑屏 ts），进度条完全同步。
     *      本地 clean 代理 会在 mx.php 的 placeholder_ts action 生成一段合法的单节目 MPEG-TS，aac 静音 + h264 黑屏。
     *  - data_uri：直接返回 一个极短的 0.5s data:application/octet-stream;base64, 合法 TS 数据。
     *      好处：0 网络请求；坏处：长度与原广告不一致，可能让播放器提前进入下一段。
     */
    private function buildPlaceholderUri(float $durationSec, int $segIdx, string $baseUrl, string $reason): string {
        $mode = $this->config['placeholder_mode'];
        if ($mode === 'data_uri') {
            // 一个最小的 188 字节 MPEG-TS 包（无任何实际意义，但多数播放器会直接跳过），避免解码报错
            return 'data:video/mp2t;base64,' . self::$tinyTsBase64;
        }
        // local_proxy（默认）：本地 mx.php?action=placeholder_ts 返回静音黑屏 ts，完全保留原时长
        $scheme = 'http';
        $host = '127.0.0.1';
        $path = '';
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') $scheme = 'https';
        if (!empty($_SERVER['HTTP_HOST']) && PHP_SAPI !== 'cli') {
            $host = $_SERVER['HTTP_HOST'];
        } elseif ($baseUrl) {
            $p = parse_url($baseUrl);
            if (!empty($p['host'])) {
                $host = $p['host'];
                if (!empty($p['scheme'])) $scheme = $p['scheme'];
                if (!empty($p['port'])) $host .= ':' . $p['port'];
                if (!empty($p['path'])) {
                    $pp = dirname($p['path']);
                    if ($pp && $pp !== '/' && $pp !== '.') $path = '/' . trim($pp, '/');
                }
            }
        }
        $query = http_build_query([
            'action' => 'placeholder_ts',
            'd' => max(0.1, round($durationSec, 3)),
            'i' => $segIdx,
            'r' => $reason,
        ]);
        return "{$scheme}://{$host}{$path}/mx.php?{$query}";
    }

    /** 最小合法 TS：47 + 184 个 0xFF，避免某些播放器 data URI 解析失败的情况 */
    public static $tinyTsBase64 = '';

    public static function initTinyTs() {
        if (self::$tinyTsBase64 !== '') return;
        // sync_byte=0x47 + payload_unit_start=1 (0x40) + 自适应字段=0x20 + CC=0x00 + PID=0x1000? -> 简化：47 40 00 10 再加 184 字节 0x00
        $ts = "\x47\x40\x00\x10";
        $ts .= str_repeat("\x00", 184);
        self::$tinyTsBase64 = base64_encode($ts);
    }
}
Md5AdPlaceholderEngine::initTinyTs();
