<?php
/**
 * 性能优化器 - 多接口并发 + AI 学习自动排序
 *
 * 功能：
 *   1. curl_multi 并发请求多个官解接口，竞速模式（最快成功立即返回）
 *   2. AI 学习：记录每个接口的成功率、平均耗时，自动调整优先级
 *   3. 失败自动切换：一个接口被禁/失败自动用下一个
 *   4. 性能统计持久化存储（JSON 文件）
 */

class PerformanceOptimizer
{
    /** @var array 全局配置 */
    private $config;

    /** @var string 性能统计文件路径 */
    private $statsFile;

    /** @var array 内存中的性能统计 */
    private $stats;

    /**
     * v5.13.3-D3/D4：记录最后一次 buildApiUrl 的请求URL，以及对应请求的 api 配置，
     *                 供「HTML 播放器页」接口（如 jx.xmflv.cc）命中时，直接把「请求 URL 本身」
     *                 作为最终 play_url 返回给客户端（302 跳转或 iframe 直接 src=）。
     * @var array<string, array{url:string, api:array}>  键 = spl_object_id($ch) 或 'last'
     */
    private $requestContextByHandle = [];

    /**
     * 构造函数
     *
     * @param array $config 全局配置
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $cacheDir = $config['cache']['dir'] ?? (__DIR__ . '/cache');
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }
        $this->statsFile = $cacheDir . '/api_performance_stats.json';
        $this->stats = $this->loadStats();
    }

    /**
     * 从文件加载性能统计
     *
     * @return array
     */
    private function loadStats(): array
    {
        if (file_exists($this->statsFile)) {
            $json = @file_get_contents($this->statsFile);
            if ($json) {
                $data = json_decode($json, true);
                if (is_array($data) && isset($data['apis'])) {
                    return $data;
                }
            }
        }
        return [
            'apis' => [],
            'updated_at' => time(),
            'total_calls' => 0,
        ];
    }

    /**
     * 保存性能统计到文件
     *
     * @return void
     */
    private function saveStats(): void
    {
        $this->stats['updated_at'] = time();
        @file_put_contents(
            $this->statsFile,
            json_encode($this->stats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            LOCK_EX
        );
    }

    /**
     * 记录一次接口调用结果（供 AI 学习）
     *
     * @param string $apiName  接口名称
     * @param float  $duration 耗时（秒）
     * @param bool   $success  是否成功
     * @return void
     */
    public function recordApiResult(string $apiName, float $duration, bool $success): void
    {
        if (!isset($this->stats['apis'][$apiName])) {
            $this->stats['apis'][$apiName] = [
                'name'           => $apiName,
                'total_calls'    => 0,
                'success_calls'  => 0,
                'fail_calls'     => 0,
                'total_duration' => 0.0,
                'avg_duration'   => 0.0,
                'last_called'    => 0,
                'consecutive_fail' => 0,
            ];
        }

        $api = &$this->stats['apis'][$apiName];
        $api['total_calls']++;
        $api['last_called'] = time();

        if ($success) {
            $api['success_calls']++;
            $api['total_duration'] += $duration;
            $api['avg_duration'] = $api['total_duration'] / $api['success_calls'];
            $api['consecutive_fail'] = 0;
        } else {
            $api['fail_calls']++;
            $api['consecutive_fail']++;
        }

        $this->stats['total_calls']++;

        // 每 10 次调用保存一次（减少 IO）
        if ($this->stats['total_calls'] % 10 === 0) {
            $this->saveStats();
        }
    }

    /**
     * 获取接口优先级评分（AI 学习自动排序）
     *
     * 评分算法：
     *   - 成功率占 50%（成功率越高越好）
     *   - 平均耗时占 40%（越快越好）
     *   - 连续失败惩罚占 10%（连续失败越多分越低）
     *
     * @param string $apiName 接口名称
     * @return float 0-100，越高越优
     */
    public function getApiScore(string $apiName): float
    {
        if (!isset($this->stats['apis'][$apiName])) {
            return 75.0; // 新接口给中等偏上的初始分
        }

        $api = $this->stats['apis'][$apiName];
        if ($api['total_calls'] === 0) {
            return 75.0;
        }

        // 成功率评分（50%）
        $successRate = $api['success_calls'] / $api['total_calls'];
        $successScore = $successRate * 50;

        // 平均耗时评分（40%）- 低于 1s 满分，每多 1s 扣 5 分，最低 0 分
        $avgDur = $api['avg_duration'] > 0 ? $api['avg_duration'] : 5.0;
        $durationScore = max(0, 40 - max(0, ($avgDur - 1.0)) * 5);

        // 连续失败惩罚（10%）- 连续失败 3 次以上开始扣分
        $consecFail = $api['consecutive_fail'];
        $failPenalty = $consecFail >= 3 ? min(10, ($consecFail - 2) * 2.5) : 0;

        $totalScore = $successScore + $durationScore - $failPenalty;
        return max(0, min(100, $totalScore));
    }

    /**
     * 获取排序后的接口列表（AI 学习自动排序）
     *
     * 按评分从高到低排序，评分最高的优先调用
     *
     * @param array $apiList 接口配置数组
     * @return array 排序后的接口数组
     */
    public function sortApisByScore(array $apiList): array
    {
        if (count($apiList) <= 1) {
            return $apiList;
        }

        $scored = [];
        foreach ($apiList as $api) {
            $name = $api['name'] ?? md5($api['url'] ?? 'unknown');
            $score = $this->getApiScore($name);
            $scored[] = ['api' => $api, 'score' => $score, 'name' => $name];
        }

        // 按评分降序排列
        usort($scored, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_column($scored, 'api');
    }

    /**
     * 并发请求多个接口，返回最快成功的结果（竞速模式）
     *
     * 使用 curl_multi 并发请求所有接口，哪个最先成功返回有效结果，
     * 就立即取消其他请求并返回。
     *
     * @param array    $apiList      接口配置数组（已按优先级排序）
     * @param string   $videoUrl     视频页面 URL
     * @param int      $maxConcurrent 最大并发数
     * @param float    $timeout      总超时时间（秒）
     * @return array {
     *     url:    string|null 视频直链
     *     api:    array|null  成功的接口配置
     *     duration: float     总耗时
     * }
     */
    public function concurrentRaceRequest(
        array $apiList,
        string $videoUrl,
        int $maxConcurrent = 3,
        float $timeout = 15.0
    ): array {
        if (empty($apiList)) {
            return ['url' => null, 'api' => null, 'duration' => 0];
        }

        $startTime = microtime(true);

        // 只有一个接口时直接请求
        if (count($apiList) === 1) {
            $api = reset($apiList);
            $apiName = $api['name'] ?? md5($api['url'] ?? 'unknown');
            $link = $this->callApiSingle($videoUrl, $api);
            $duration = microtime(true) - $startTime;
            $success = ($link !== null);
            $this->recordApiResult($apiName, $duration, $success);
            return ['url' => $link, 'api' => $api, 'duration' => $duration];
        }

        // 多接口并发：取前 maxConcurrent 个最高优先级的接口
        $concurrentApis = array_slice($apiList, 0, $maxConcurrent);
        $remainingApis = array_slice($apiList, $maxConcurrent);

        $multiHandle = curl_multi_init();
        $handles = [];
        $handleMap = []; // handle_index => api

        foreach ($concurrentApis as $idx => $api) {
            $ch = $this->createCurlHandle($videoUrl, $api);
            if ($ch) {
                curl_multi_add_handle($multiHandle, $ch);
                $handles[$idx] = $ch;
                $handleMap[$idx] = $api;
            }
        }

        $resultUrl = null;
        $resultApi = null;
        $active = null;

        // 执行并发请求
        do {
            $mrc = curl_multi_exec($multiHandle, $active);
            if ($mrc != CURLM_OK) {
                break;
            }

            // 检查是否有完成的请求
            while ($done = curl_multi_info_read($multiHandle)) {
                // 找到对应的接口
                $foundIdx = null;
                foreach ($handles as $idx => $ch) {
                    if ($ch === $done['handle']) {
                        $foundIdx = $idx;
                        break;
                    }
                }

                if ($foundIdx === null) {
                    continue;
                }

                $api = $handleMap[$foundIdx];
                $apiName = $api['name'] ?? md5($api['url'] ?? 'unknown');
                $callDuration = microtime(true) - $startTime;

                // 检查是否成功
                $httpCode = curl_getinfo($done['handle'], CURLINFO_HTTP_CODE);
                $response = curl_multi_getcontent($done['handle']);

                if ($httpCode === 200 && $response) {
                    // v5.13.2-C3：并发路径同样识别 HTTP 200 业务级 success=false:{message:"验证失败!"}，
                    //            先记录错误原因再 recordApiResult(false)，避免之前并发失败没进入 recordFailedApi。
                    $first = substr(ltrim($response), 0, 1);
                    $bizOk = true;
                    if ($first === '{' || $first === '[') {
                        $data = json_decode($response, true);
                        if (is_array($data)) {
                            $bizOk = false;
                            if (isset($data['code'])    && (int)$data['code'] === 200) $bizOk = true;
                            if (isset($data['success']) && $data['success'] === true) $bizOk = true;
                            if (isset($data['status'])  && (int)$data['status'] === 1) $bizOk = true;
                            if (!$bizOk) {
                                $this->recordFailedApi($api, 200, $response, '并发-业务级错误（success=false/code!=200）');
                            }
                        }
                    }

                    if ($bizOk) {
                        // v5.13.7-I2：并发场景下 requestContextByHandle['last'] 可能指向错误的 handle，
                        //   在调 extractVideoUrl 之前更新为当前完成的 handle 的 context
                        $chInt = (int)$done['handle'];
                        if (isset($this->requestContextByHandle[$chInt])) {
                            $this->requestContextByHandle['last'] = &$this->requestContextByHandle[$chInt];
                        }
                        $link = $this->extractVideoUrl($response, $api, $videoUrl);
                        // v5.13.7-I2：过滤掉 127.0.0.1/localhost 的回环 URL（防止官替 HTTP 自调用返回内部地址）
                        if ($link && filter_var($link, FILTER_VALIDATE_URL)
                            && stripos($link, '127.0.0.1') === false
                            && stripos($link, '://localhost') === false) {
                            // 成功！立即记录并返回
                            $resultUrl = $link;
                            $resultApi = $api;
                            $this->recordApiResult($apiName, $callDuration, true);

                            // 记录其他未完成的接口为失败（但不扣分太重，因为是被取消的）
                            foreach ($handles as $otherIdx => $otherCh) {
                                if ($otherIdx !== $foundIdx) {
                                    $otherApi = $handleMap[$otherIdx];
                                    $otherName = $otherApi['name'] ?? md5($otherApi['url'] ?? 'unknown');
                                    // 竞速中被取消的，不给连续失败惩罚
                                    if (isset($this->stats['apis'][$otherName])) {
                                        $this->stats['apis'][$otherName]['consecutive_fail'] = max(
                                            0,
                                            $this->stats['apis'][$otherName]['consecutive_fail'] - 1
                                        );
                                    }
                                }
                            }

                            break 2; // 跳出所有循环
                        }
                        // 到这里说明 HTTP 200 + 业务 status=ok，但 extractVideoUrl 拿不到视频
                        $this->recordFailedApi($api, 200, $response, '并发-HTTP 200 & 业务成功，但无法提取视频URL（字段='.($api['url_field'] ?? '未指定').'）');
                    }
                } else {
                    // HTTP 非200 / 空响应
                    $this->recordFailedApi(
                        $api,
                        (int)$httpCode,
                        is_string($response) ? $response : null,
                        $response ? '' : '并发-空响应或curl错误('.(curl_error($done['handle']) ?: '无错误信息').')'
                    );
                }

                // 这个接口失败了，记录下来
                $this->recordApiResult($apiName, $callDuration, false);

                // 移除失败的 handle
                curl_multi_remove_handle($multiHandle, $done['handle']);
                curl_close($done['handle']);
                unset($handles[$foundIdx]);

                // 如果还有剩余接口，加入下一个
                if (!empty($remainingApis)) {
                    $nextApi = array_shift($remainingApis);
                    $nextCh = $this->createCurlHandle($videoUrl, $nextApi);
                    if ($nextCh) {
                        $newIdx = count($handleMap);
                        curl_multi_add_handle($multiHandle, $nextCh);
                        $handles[$newIdx] = $nextCh;
                        $handleMap[$newIdx] = $nextApi;
                    }
                }
            }

            // 检查超时
            if ((microtime(true) - $startTime) > $timeout) {
                break;
            }

            if ($active > 0) {
                curl_multi_select($multiHandle, 0.1); // 等待 0.1 秒
            }
        } while ($active > 0 && $mrc == CURLM_OK);

        // 清理剩余的 handle
        foreach ($handles as $ch) {
            curl_multi_remove_handle($multiHandle, $ch);
            curl_close($ch);
        }
        curl_multi_close($multiHandle);

        $totalDuration = microtime(true) - $startTime;

        // 如果并发请求都失败了，尝试剩余的接口（串行 fallback）
        if (!$resultUrl && !empty($remainingApis)) {
            foreach ($remainingApis as $api) {
                $apiName = $api['name'] ?? md5($api['url'] ?? 'unknown');
                $callStart = microtime(true);
                $link = $this->callApiSingle($videoUrl, $api);
                $callDuration = microtime(true) - $callStart;
                if ($link) {
                    $this->recordApiResult($apiName, $callDuration, true);
                    $resultUrl = $link;
                    $resultApi = $api;
                    $totalDuration = microtime(true) - $startTime;
                    break;
                } else {
                    $this->recordApiResult($apiName, $callDuration, false);
                }
                // 超时检查
                if ((microtime(true) - $startTime) > $timeout) {
                    break;
                }
            }
        }

        return [
            'url'      => $resultUrl,
            'api'      => $resultApi,
            'duration' => $totalDuration,
        ];
    }

    /**
     * 创建单个 cURL 句柄
     *
     * @param string $videoUrl 视频 URL
     * @param array  $api      接口配置
     * @return resource|null
     */
    private function createCurlHandle(string $videoUrl, array $api)
    {
        $requestUrl = $this->buildApiUrl($videoUrl, $api);
        if (!$requestUrl) {
            return null;
        }

        $ch = curl_init();
        if (!$ch) {
            return null;
        }
        // v5.13.3-D3：按请求句柄存一份 context，供 extractVideoUrlWrapper 用
        $this->requestContextByHandle[(int)$ch] = ['url' => $requestUrl, 'api' => $api, 'video_url' => $videoUrl];
        $this->requestContextByHandle['last'] = &$this->requestContextByHandle[(int)$ch];

        $httpConfig = $this->config['http'] ?? [];
        // v5.13.3-D4：默认 UA 升级成 Chrome 126（原 'Mozilla/5.0' 太短容易被 Cloudflare 拦截 403）
        $defaultUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36';
        $ua = $httpConfig['user_agent'] ?? null;
        if (!$ua || $ua === 'Mozilla/5.0') {
            $ua = $defaultUA;
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $requestUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => (int)($httpConfig['timeout'] ?? 15),
            CURLOPT_CONNECTTIMEOUT => (int)($httpConfig['connect_timeout'] ?? 5),
            CURLOPT_USERAGENT      => $ua,
            CURLOPT_SSL_VERIFYPEER => ($httpConfig['ssl_verify'] ?? false) ? true : false,
            CURLOPT_SSL_VERIFYHOST => ($httpConfig['ssl_verify'] ?? false) ? 2 : 0,
            CURLOPT_ENCODING       => 'gzip,deflate',
        ]);

        $headers = [];
        if (!empty($api['headers']) && is_array($api['headers'])) {
            foreach ($api['headers'] as $k => $v) {
                $headers[] = is_numeric($k) ? $v : "{$k}: {$v}";
            }
        }
        // v5.13.3-D4：针对 jx.xmflv.cc（Cloudflare WAF 下的虾米新播放器）自动补浏览器头，
        //             防止 403 Forbidden（jmflv / xmflv 这类站点会检测 Origin/Referer/Accept）
        $host = (string)parse_url($requestUrl, PHP_URL_HOST);
        $isXmflv = stripos($host, 'xmflv.cc') !== false || stripos($host, 'jmflv') !== false || stripos($host, 'jx.') === 0;
        if ($isXmflv) {
            $scheme = (parse_url($requestUrl, PHP_URL_SCHEME) ?: 'https') . '://';
            $origin = $scheme . $host;
            $hasReferer = false; $hasOrigin = false; $hasAccept = false;
            foreach ($headers as $h) {
                $lh = strtolower($h);
                if (strpos($lh, 'referer:') === 0) $hasReferer = true;
                if (strpos($lh, 'origin:') === 0)  $hasOrigin = true;
                if (strpos($lh, 'accept:') === 0)  $hasAccept = true;
            }
            if (!$hasAccept)  $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
            if (!$hasOrigin)  $headers[] = 'Origin: ' . $origin;
            if (!$hasReferer) {
                $fallbackRef = $this->guessPlatformReferer($videoUrl);
                $headers[] = 'Referer: ' . ($fallbackRef ?: ($origin . '/'));
            }
            $headers[] = 'sec-ch-ua: "Not/A)Brand";v="8", "Chromium";v="126", "Google Chrome";v="126"';
            $headers[] = 'sec-ch-ua-mobile: ?0';
            $headers[] = 'sec-ch-ua-platform: "Windows"';
            $headers[] = 'Upgrade-Insecure-Requests: 1';
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        return $ch;
    }

    /**
     * v5.13.3-D4：根据视频页 URL 推断一个合理 Referer（jx.xmflv.cc ref 参数和 HTTP Referer 头都用它）
     */
    private function guessPlatformReferer(string $videoUrl): string {
        if ($videoUrl === '') return '';
        $host = (string)parse_url($videoUrl, PHP_URL_HOST);
        if ($host === '') return '';
        $scheme = (string)(parse_url($videoUrl, PHP_URL_SCHEME) ?: 'https');
        if (stripos($host, 'youku.com') !== false || stripos($host, 'ykimg.com') !== false) return $scheme . '://v.youku.com/';
        if (stripos($host, 'iqiyi.com') !== false || stripos($host, 'qiyi') !== false) return $scheme . '://www.iqiyi.com/';
        if (stripos($host, 'qq.com') !== false || stripos($host, 'video.qq') !== false) return $scheme . '://v.qq.com/';
        if (stripos($host, 'mgtv.com') !== false) return $scheme . '://www.mgtv.com/';
        if (stripos($host, 'le.com') !== false || stripos($host, 'letv') !== false) return $scheme . '://www.le.com/';
        if (stripos($host, 'bilibili.com') !== false) return $scheme . '://www.bilibili.com/';
        if (stripos($host, 'sohu.com') !== false) return $scheme . '://tv.sohu.com/';
        if (stripos($host, 'pptv.com') !== false) return $scheme . '://v.pptv.com/';
        return $scheme . '://' . $host . '/';
    }

    /**
     * 构建请求 URL
     * v5.13.3-D3 新增占位符支持：
     *   - {url}   → 原始视频页 URL（urlencode）
     *   - {ref}   → 根据视频页推断的来源站点 Referer（urlencode，传给 jx.xmflv.cc ref 参数）
     *   - {origin}→ 来源 Origin，即 {ref} 去掉 path
     *   - 若没有任何占位符 → 走老逻辑：直接在末尾拼接 urlencode($videoUrl)
     *
     * @param string $videoUrl
     * @param array  $api
     * @return string|null
     */
    private function buildApiUrl(string $videoUrl, array $api): ?string
    {
        if (empty($api['url'])) {
            return null;
        }
        $tpl = (string)$api['url'];
        // 检测占位符：只有出现 {xxx} 时才走占位符替换逻辑
        if (strpos($tpl, '{') !== false) {
            $ref    = $this->guessPlatformReferer($videoUrl);
            $origin = '';
            if ($ref !== '') {
                $p = parse_url($ref);
                $origin = ($p['scheme'] ?? 'https') . '://' . ($p['host'] ?? '') . (!empty($p['port']) ? ':' . $p['port'] : '') . '/';
            }
            $replace = [
                '{url}'    => urlencode($videoUrl),
                '{ref}'    => urlencode($ref),
                '{referer}'=> urlencode($ref),
                '{origin}' => urlencode($origin),
                '{ts}'     => (string)time(),
                '{t}'      => (string)(time() * 1000),
            ];
            return strtr($tpl, $replace);
        }
        // 兼容旧模板：纯前缀，直接后缀拼 url=xxx
        // v5.13.7-I2：如果模板已经包含 url=（如官替 HTTP 并行调用的完整URL），不再追加
        if (stripos($tpl, 'url=') !== false) {
            return $tpl;  // 已是完整 URL，不需要拼接
        }
        return $tpl . urlencode($videoUrl);
    }

    /**
     * v5.13.2-C3：把一条官解接口的失败原因（HTTP 状态码 / 业务级错误消息 / 响应长度）写入全局变量，
     * 供 xt/server.php B4 嗅探诊断时间线读取并展示给用户，避免之前「静默失败」。
     */
    private function recordFailedApi(array $api, int $httpCode, ?string $response, string $extraReason = ''): void {
        // 初始化全局变量（xt/server.php 的 B4 嗅探诊断会读取）
        if (!isset($GLOBALS['XT_FAILED_API_REQUESTS']) || !is_array($GLOBALS['XT_FAILED_API_REQUESTS'])) {
            $GLOBALS['XT_FAILED_API_REQUESTS'] = [];
        }
        $name = (string)($api['name'] ?? '未命名官解');
        $url  = (string)($api['url'] ?? '');
        $shortUrl = strlen($url) > 64 ? substr($url, 0, 64).'…' : $url;

        // 【关键】识别业务级错误：HTTP 200 但响应是 JSON {success:false, message:"验证失败!"} 等
        $bizMsg = '';
        if ($httpCode === 200 && $response && is_string($response)) {
            $first = substr(ltrim($response), 0, 1);
            if ($first === '{' || $first === '[') {
                $data = json_decode($response, true);
                if (is_array($data)) {
                    $ok = false;
                    if (isset($data['code'])  && (int)$data['code'] === 200) $ok = true;
                    if (isset($data['success']) && $data['success'] === true) $ok = true;
                    if (isset($data['status'])  && (int)$data['status'] === 1) $ok = true;
                    if (!$ok) {
                        $bizMsg = (string)($data['message'] ?? $data['msg'] ?? $data['ZT'] ?? '');
                        if ($bizMsg !== '' && strlen($bizMsg) > 128) $bizMsg = substr($bizMsg, 0, 128).'…';
                    }
                }
            }
        }

        if ($httpCode !== 200) {
            $reason = "HTTP {$httpCode}";
        } elseif (!$response) {
            $reason = '空响应(curl_exec返回false或空串，可能是连接超时/上游502/服务器主动断开)';
        } elseif ($bizMsg !== '') {
            $reason = '业务级错误：' . $bizMsg;
        } else {
            $reason = $extraReason !== '' ? $extraReason : '响应无法解析出有效视频地址';
        }
        $GLOBALS['XT_FAILED_API_REQUESTS'][] = [
            'name'         => $name,
            'url_prefix'   => $shortUrl,
            'http_code'    => $httpCode,
            'response_len' => $response ? strlen($response) : 0,
            'reason'       => $reason,
            'biz_message'  => $bizMsg,
            'ts_ms'        => (int)(microtime(true) * 1000),
        ];
    }

    /**
     * 单个接口串行请求（fallback 用）
     *
     * v5.13.2-C3：在此处增加对「HTTP 200 但业务 success=false」的识别，
     *             不再像以前把 {"success":false,"message":"验证失败!"} 当作「无法解析视频URL」的静默失败。
     */
    private function callApiSingle(string $videoUrl, array $api): ?string
    {
        $ch = $this->createCurlHandle($videoUrl, $api);
        if (!$ch) {
            $this->recordFailedApi($api, 0, null, '无法创建 curl 句柄');
            return null;
        }

        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);
        $httpCode = (int)($info['http_code'] ?? 0);
        $curlErr  = curl_error($ch);

        // v5.13.3-E4：把 curl 调试信息写入 requestContext，供 wrapper 排错 / 测试脚本验证
        $key = (int)$ch;
        if (isset($this->requestContextByHandle[$key]) && is_array($this->requestContextByHandle[$key])) {
            $sample = is_string($response) ? substr($response, 0, 8192) : '';
            $fakeHead = '';
            $statuses = [
                200=>'OK',301=>'Moved Permanently',302=>'Found',400=>'Bad Request',401=>'Unauthorized',
                403=>'Forbidden',404=>'Not Found',500=>'Internal Server Error',502=>'Bad Gateway',503=>'Service Unavailable',504=>'Gateway Timeout'
            ];
            if ($httpCode > 0) $fakeHead .= 'HTTP/1.1 ' . $httpCode . ' ' . ($statuses[$httpCode] ?? '') . "\r\n";
            foreach (['content_type','size_download','request_size','total_time','namelookup_time','connect_time','ssl_verify_result','redirect_count','primary_ip'] as $k) {
                if (isset($info[$k]) && $info[$k] !== '' && $info[$k] !== null) {
                    $fakeHead .= ucwords(str_replace('_','-',$k)) . ': ' . (is_scalar($info[$k]) ? (string)$info[$k] : json_encode($info[$k], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . "\r\n";
                }
            }
            $this->requestContextByHandle[$key] = $this->requestContextByHandle[$key] + [
                'http_code'           => $httpCode,
                'content_type'        => (string)($info['content_type'] ?? ''),
                'size_download'       => $info['size_download'] ?? null,
                'request_size'        => $info['request_size'] ?? null,
                'total_time_ms'       => isset($info['total_time']) ? round($info['total_time'] * 1000, 2) : null,
                'curl_error'          => $curlErr ?: null,
                'response_head'       => $fakeHead,
                'response_body_sample'=> $sample,
            ];
        }

        curl_close($ch);

        if ($httpCode !== 200 || $response === false || $response === '') {
            $extra = $curlErr ? ('curl 错误: ' . $curlErr) : '';
            $this->recordFailedApi($api, (int)$httpCode, is_string($response) ? $response : null, $extra);
            return null;
        }

        // v5.13.2-C3：HTTP 200 但业务级错误（success=false / code=500）→ 记录错误原因 + 返回 null
        $first = substr(ltrim($response), 0, 1);
        if ($first === '{' || $first === '[') {
            $data = json_decode($response, true);
            if (is_array($data)) {
                $ok = false;
                if (isset($data['code'])    && (int)$data['code'] === 200) $ok = true;
                if (isset($data['success']) && $data['success'] === true) $ok = true;
                if (isset($data['status'])  && (int)$data['status'] === 1) $ok = true;
                if (!$ok) {
                    $this->recordFailedApi($api, 200, $response, '业务级错误（success=false / code!=200）');
                    return null;
                }
            }
        }

        $link = $this->extractVideoUrl($response, $api, $videoUrl);
        if ($link === null) {
            $this->recordFailedApi($api, 200, $response, 'HTTP 200 & 业务成功，但视频字段（' . ($api['url_field'] ?? '未指定') . '）为空/格式非法');
            return null;
        }
        // v5.13.7-I2：过滤掉 127.0.0.1/localhost 回环 URL
        if (stripos($link, '127.0.0.1') !== false || stripos($link, '://localhost') !== false) {
            $this->recordFailedApi($api, 200, $response, '提取到回环URL(127.0.0.1/localhost)，已过滤');
            return null;
        }
        return $link;
    }

    /**
     * 从接口响应中提取视频 URL
     * 【v5.10.9 安全加固】
     *   - 禁止返回与输入视频页面完全相同的 URL
     *   - 禁止返回原始页面域名相同的 HTML 页面（original_url 陷阱）
     *   - 已知安全字段（url_field / success=true 字段）允许代理地址
     *   - 兜底递归搜索仅接受带视频扩展名的 URL
     *
     * @param string $response 响应内容
     * @param array  $api      接口配置
     * @return string|null
     */
    private function extractVideoUrl(string $response, array $api, string $videoUrl = ''): ?string
        {
        // v5.13.3-D3：先做轻量级前置识别——HTML 播放器页面接口（如 jx.xmflv.cc 虾米新播放器）
        // 特点：HTTP 200，Content-Type text/html；页面 <title> 是「虾米播放器…」，或者有 <div class="Xmflv" id="Xmflv"></div>
        // 这类接口返回的 HTML 本身不含裸视频 URL，视频源由浏览器端混淆 JS runtime 拉取；但播放器页面完整 URL 本身就可以
        // 直接被客户端 <iframe src=...> 打开 / 或 302 跳转播放，因此识别成功后直接返回我们 buildApiUrl 拼好的整段请求 URL。
        $ctx = $this->requestContextByHandle['last'] ?? null;
        $reqUrl = is_array($ctx) ? (string)($ctx['url'] ?? '') : '';
        $apiType = strtolower((string)($api['type'] ?? 'json'));
        $host = $reqUrl ? (string)parse_url($reqUrl, PHP_URL_HOST) : '';
        $isHtmlPlayerType = ($apiType === 'html_player' || $apiType === 'iframe' || $apiType === 'page');
        $isKnownPlayerHost = (stripos($host, 'xmflv.cc') !== false || stripos($host, 'jmflv') !== false
                              || stripos($host, 'jx.xm') === 0 || stripos($host, 'xmplayer') !== false);
        if ($response && is_string($response) && $reqUrl !== '' && ($isHtmlPlayerType || $isKnownPlayerHost)) {
            $lower = substr($response, 0, 4096);
            // 不需要把整个 10KB HTML 转小写；前 4KB 足够包含 <title> 与 <div id="Xmflv"> 锚点
            $hitTitle   = stripos($lower, '<title>虾米播放器') !== false || stripos($lower, '虾米播放器') !== false;
            $hitXmflv   = stripos($lower, 'id="Xmflv"') !== false || stripos($lower, 'class="Xmflv"') !== false;
            $hitSignJs  = stripos($lower, 'Xmflv();') !== false || stripos($lower, 'new Xm') !== false;
            // 兜底 type=html_player 的情况下只要响应像 HTML（不是 JSON/纯文本）就算命中
            $looksHtml  = stripos($lower, '<!doctype html') !== false || stripos($lower, '<html') !== false;
            if (($isHtmlPlayerType && $looksHtml) || $hitTitle || $hitXmflv || ($isKnownPlayerHost && $looksHtml && $hitSignJs)) {
                // 必须确保 URL 合法且不等于原始 videoUrl（防止死循环/回源）
                if (filter_var($reqUrl, FILTER_VALIDATE_URL) && strcasecmp($reqUrl, $videoUrl) !== 0) {
                    // 对请求里的 {ref} 不完整或空的情况，再补一次 ref 参数（给播放器 JS 用作跨域 Referer）
                    if (stripos($reqUrl, 'ref=') === false) {
                        $ref = $this->guessPlatformReferer($videoUrl);
                        if ($ref !== '') {
                            $joiner = (strpos($reqUrl, '?') === false) ? '?' : '&';
                            $reqUrl .= $joiner . 'ref=' . urlencode($ref);
                        }
                    }
                    return $reqUrl;
                }
            }
        }

        // jiami 分支：核心解密逻辑由 xt/jiami_core.php 中的工厂函数提供，
        //            用 Closure::call($this) 绑定实例
        if (!function_exists('_jm_po_extractVideoUrl')) {
            throw new \RuntimeException('[jiami] 缺少 PerformanceOptimizer::extractVideoUrl 解密工厂：请确认 xt/jiami_core.php 已加载。');
        }
        $__c = _jm_po_extractVideoUrl();
        return $__c->call($this, ...func_get_args());
    }

    /**
     * 递归从数组中查找第一个有效视频 URL
     * 【安全加固】跳过 original_url 等非视频字段，仅接受视频扩展名 URL，可排除指定域名
     *
     * @param mixed  $data
     * @param string $excludeDomainPattern
     * @return string|null
     */
    private function findUrlInArray($data, string $excludeDomainPattern = ''): ?string
        {
        if (!function_exists('_jm_po_findUrlInArray')) {
            throw new \RuntimeException('[jiami] 缺少 PerformanceOptimizer::findUrlInArray 解密工厂：请确认 xt/jiami_core.php 已加载。');
        }
        $__c = _jm_po_findUrlInArray();
        return $__c->call($this, ...func_get_args());
    }

    /**
     * 获取性能统计数据
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->stats;
    }

    /**
     * 重置性能统计
     *
     * @return void
     */
    public function resetStats(): void
    {
        $this->stats = [
            'apis' => [],
            'updated_at' => time(),
            'total_calls' => 0,
        ];
        $this->saveStats();
    }

    /**
     * 获取性能统计文件路径
     *
     * @return string
     */
    public function getStatsFilePath(): string
    {
        return $this->statsFile;
    }
}
