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
                    $link = $this->extractVideoUrl($response, $api);
                    if ($link && filter_var($link, FILTER_VALIDATE_URL)) {
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
        $httpConfig = $this->config['http'] ?? [];

        curl_setopt_array($ch, [
            CURLOPT_URL            => $requestUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => (int)($httpConfig['timeout'] ?? 15),
            CURLOPT_CONNECTTIMEOUT => (int)($httpConfig['connect_timeout'] ?? 5),
            CURLOPT_USERAGENT      => $httpConfig['user_agent'] ?? 'Mozilla/5.0',
            CURLOPT_SSL_VERIFYPEER => ($httpConfig['ssl_verify'] ?? false) ? true : false,
            CURLOPT_SSL_VERIFYHOST => ($httpConfig['ssl_verify'] ?? false) ? 2 : 0,
            CURLOPT_ENCODING       => 'gzip,deflate',
        ]);

        if (!empty($api['headers']) && is_array($api['headers'])) {
            $headers = [];
            foreach ($api['headers'] as $k => $v) {
                $headers[] = is_numeric($k) ? $v : "{$k}: {$v}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        return $ch;
    }

    /**
     * 构建请求 URL
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
        return $api['url'] . urlencode($videoUrl);
    }

    /**
     * 单个接口串行请求（fallback 用）
     *
     * @param string $videoUrl
     * @param array  $api
     * @return string|null
     */
    private function callApiSingle(string $videoUrl, array $api): ?string
    {
        $ch = $this->createCurlHandle($videoUrl, $api);
        if (!$ch) {
            return null;
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        return $this->extractVideoUrl($response, $api);
    }

    /**
     * 从接口响应中提取视频 URL
     *
     * @param string $response 响应内容
     * @param array  $api      接口配置
     * @return string|null
     */
    private function extractVideoUrl(string $response, array $api): ?string
    {
        $type = $api['type'] ?? 'json';

        switch ($type) {
            case 'json':
                $data = json_decode($response, true);
                if (!$data) {
                    return null;
                }

                $urlField = $api['url_field'] ?? null;
                $url = null;

                if ($urlField && isset($data[$urlField])) {
                    $url = $data[$urlField];
                }

                // 兼容官替接口返回结构
                if (!$url && !empty($data['success'])) {
                    $url = $data['ad_skip_url'] ?? $data['m3u8_url'] ?? null;
                }

                // 通用兜底
                if (!$url) {
                    $url = $data['url'] ?? $data['play_url']
                        ?? $data['data']['url'] ?? $data['data']['play_url']
                        ?? $data['video_url'] ?? null;
                }

                if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                    return $url;
                }

                // 递归搜索 URL
                return $this->findUrlInArray($data);

            case 'redirect':
                // 重定向类型已经通过 curl FOLLOWLOCATION 拿到了最终 URL
                // 这里直接判断 response 是否是 URL
                if (filter_var(trim($response), FILTER_VALIDATE_URL)) {
                    return trim($response);
                }
                return null;

            case 'text':
            default:
                // 文本类型，尝试从响应中提取 URL
                if (preg_match('/https?:\/\/[^\s"\'<>]+/i', $response, $matches)) {
                    $url = $matches[0];
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        return $url;
                    }
                }
                return null;
        }
    }

    /**
     * 递归从数组中查找第一个有效 URL
     *
     * @param mixed $data
     * @return string|null
     */
    private function findUrlInArray($data): ?string
    {
        if (!is_array($data)) {
            return null;
        }
        foreach ($data as $key => $value) {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                return $value;
            }
            if (is_array($value)) {
                $found = $this->findUrlInArray($value);
                if ($found) {
                    return $found;
                }
            }
        }
        return null;
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
