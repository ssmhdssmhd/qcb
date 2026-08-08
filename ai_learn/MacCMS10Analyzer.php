<?php

class MacCMS10Analyzer {
    private $apiUrl;
    private $opts;
    private $lastHttpError = '';

    public function __construct($apiUrl, $opts = []) {
        $this->apiUrl = $apiUrl;
        $this->opts = array_merge([
            'timeout' => 30,
            'ac' => 'detail',
            'pg' => 1,
            'limit' => 20,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'ssl_verify' => false
        ], $opts);
    }

    public function listVideos($page = 1, $pageSize = 20, $type = null) {
        $params = [
            'ac' => $this->opts['ac'] ?? 'detail',
            'pg' => intval($page),
            'limit' => intval($pageSize)
        ];
        if ($type !== null) {
            $params['t'] = $type;
        }
        $url = $this->buildUrl($this->apiUrl, $params);
        $response = $this->httpGet($url);
        if ($response === false) {
            return [
                'success' => false,
                'message' => 'HTTP请求失败: ' . $this->lastHttpError,
                'list' => [],
                'pagecount' => 0,
                'total' => 0
            ];
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [
                'success' => false,
                'message' => 'JSON解析失败',
                'list' => [],
                'pagecount' => 0,
                'total' => 0
            ];
        }
        $list = [];
        $pagecount = 0;
        $total = 0;
        if (isset($data['code']) && isset($data['data'])) {
            $list = $data['data']['list'] ?? [];
            $pagecount = $data['data']['pagecount'] ?? $data['pagecount'] ?? 0;
            $total = $data['data']['total'] ?? 0;
        } elseif (isset($data['list'])) {
            $list = $data['list'];
            $pagecount = $data['pagecount'] ?? 0;
            $total = $data['total'] ?? count($list);
        }
        if (!is_array($list)) {
            $list = [];
        }
        return [
            'success' => true,
            'list' => $list,
            'pagecount' => intval($pagecount),
            'total' => intval($total)
        ];
    }

    public function normalizeVideo($vodItem) {
        $result = [
            'id' => $vodItem['vod_id'] ?? $vodItem['id'] ?? '',
            'name' => $vodItem['vod_name'] ?? $vodItem['name'] ?? '',
            'cover' => $vodItem['vod_pic'] ?? $vodItem['pic'] ?? '',
            'year' => $vodItem['vod_year'] ?? '',
            'type' => $vodItem['vod_type'] ?? $vodItem['type'] ?? '',
            'area' => $vodItem['vod_area'] ?? '',
            'actor' => $vodItem['vod_actor'] ?? '',
            'director' => $vodItem['vod_director'] ?? '',
            'remarks' => $vodItem['vod_remarks'] ?? $vodItem['remarks'] ?? '',
            'douban_id' => $vodItem['vod_douban_id'] ?? '',
            'play_from' => $vodItem['vod_play_from'] ?? $vodItem['play_from'] ?? '',
            'play_lines' => []
        ];
        $playUrl = $vodItem['vod_play_url'] ?? $vodItem['play_url'] ?? '';
        $playFrom = $result['play_from'];
        if (empty($playUrl)) {
            return $result;
        }
        $fromGroups = [];
        if (!empty($playFrom) && strpos($playFrom, '$$$') !== false && strpos($playUrl, '$$$') !== false) {
            $fromParts = explode('$$$', $playFrom);
            $urlParts = explode('$$$', $playUrl);
            $count = min(count($fromParts), count($urlParts));
            for ($i = 0; $i < $count; $i++) {
                $fromGroups[] = [
                    'from' => trim($fromParts[$i]),
                    'url' => trim($urlParts[$i])
                ];
            }
        } else {
            $fromGroups[] = [
                'from' => trim($playFrom) ?: 'default',
                'url' => $playUrl
            ];
        }
        foreach ($fromGroups as $group) {
            $fromName = $group['from'];
            $groupUrlStr = $group['url'];
            $episodes = $this->parseEpisodes($groupUrlStr);
            if (!empty($episodes)) {
                $result['play_lines'][] = [
                    'from' => $fromName,
                    'episodes' => $episodes
                ];
            }
        }
        return $result;
    }

    private function parseEpisodes($playUrl) {
        $episodes = [];
        if (empty($playUrl)) {
            return $episodes;
        }
        $lines = [];
        if (strpos($playUrl, "\r\n") !== false || strpos($playUrl, "\n") !== false) {
            $lines = preg_split('/\r\n|\n/', $playUrl);
        } else {
            $lines = [$playUrl];
        }
        $lineNum = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            $lineNum++;
            if (strpos($line, '$') !== false) {
                $parts = explode('$', $line);
                $urlIndices = [];
                foreach ($parts as $i => $part) {
                    $part = trim($part);
                    if (preg_match('/^https?:\/\//i', $part)) {
                        $urlIndices[] = $i;
                    }
                }
                foreach ($urlIndices as $idx) {
                    $url = $parts[$idx];
                    $name = '';
                    if ($idx > 0) {
                        $prev = trim($parts[$idx - 1] ?? '');
                        if (!preg_match('/^https?:\/\//i', $prev) && !empty($prev)) {
                            $name = $prev;
                        }
                    }
                    if (empty($name)) {
                        $name = '第' . (count($episodes) + 1) . '集';
                    }
                    $episodes[] = ['name' => $name, 'url' => $url];
                }
            } else {
                if (preg_match('/^https?:\/\//i', $line)) {
                    $name = '第' . $lineNum . '集';
                    $episodes[] = ['name' => $name, 'url' => $line];
                }
            }
        }
        return $episodes;
    }

    public function isLikely正片($episodeName, $vodName = null) {
        $blacklist = [
            '预告片', '片花', '花絮', '解说', '抢先看', '预告', '终极版',
            '剪辑', 'cut', 'CUT', '片段', '精华', '盘点', '速看', '看点',
            '幕后', '删除', 'NG', '吻戏', '合集', '彩蛋', '发布会',
            '见面会', '首映', '宣传', '主题曲', '片头曲', '片尾曲',
            'MV', 'mv'
        ];
        foreach ($blacklist as $word) {
            if (mb_stripos($episodeName, $word) !== false) {
                return false;
            }
        }
        if ($vodName !== null && $vodName !== '') {
            if ($episodeName === $vodName) {
                return true;
            }
            if (mb_stripos($episodeName, '正片') !== false || mb_stripos($episodeName, '完整版') !== false) {
                return true;
            }
        }
        $whitelistPatterns = [
            '/^第[0-9一二三四五六七八九十百千]+[集话期回部章]/u',
            '/^\d{1,4}$/',
            '/^EP?\s*\d+/i',
            '/^[上下]\s*部$/u',
            '/^HD|BD|1080P|720P|4K|完整版$/i'
        ];
        foreach ($whitelistPatterns as $pattern) {
            if (preg_match($pattern, $episodeName)) {
                return true;
            }
        }
        return false;
    }

    public function fetchEpisodes($apiUrl, $vodId) {
        $params = [
            'ac' => 'detail',
            'ids' => intval($vodId)
        ];
        $url = $this->buildUrl($apiUrl, $params);
        $response = $this->httpGet($url);
        $skipped = [];
        if ($response === false) {
            return [
                'success' => false,
                'message' => 'HTTP请求失败: ' . $this->lastHttpError,
                'episodes' => [],
                'skipped' => $skipped
            ];
        }
        $data = json_decode($response, true);
        if (!is_array($data)) {
            return [
                'success' => false,
                'message' => 'JSON解析失败',
                'episodes' => [],
                'skipped' => $skipped
            ];
        }
        $list = [];
        if (isset($data['code']) && isset($data['data'])) {
            $list = $data['data']['list'] ?? [];
        } elseif (isset($data['list'])) {
            $list = $data['list'];
        }
        if (empty($list) || !is_array($list)) {
            return [
                'success' => false,
                'message' => '无视频数据',
                'episodes' => [],
                'skipped' => $skipped
            ];
        }
        $vodItem = $list[0];
        $normalized = $this->normalizeVideo($vodItem);
        $vodName = $normalized['name'];
        $keptEpisodes = [];
        foreach ($normalized['play_lines'] as $line) {
            $from = $line['from'];
            foreach ($line['episodes'] as $ep) {
                if ($this->isLikely正片($ep['name'], $vodName)) {
                    $keptEpisodes[] = [
                        'name' => $ep['name'],
                        'url' => $ep['url'],
                        'from' => $from
                    ];
                } else {
                    $skipped[] = [
                        'name' => $ep['name'],
                        'url' => $ep['url'],
                        'from' => $from
                    ];
                }
            }
        }
        return [
            'success' => true,
            'vod_name' => $vodName,
            'episodes' => $keptEpisodes,
            'skipped' => $skipped
        ];
    }

    private function buildUrl($baseUrl, $params = []) {
        $parsed = parse_url($baseUrl);
        if ($parsed === false) {
            return $baseUrl;
        }
        $existingParams = [];
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $existingParams);
        }
        $mergedParams = array_merge($existingParams, $params);
        $scheme = $parsed['scheme'] ?? 'https';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $path = $parsed['path'] ?? '/';
        $url = $scheme . '://' . $host . $port . $path;
        if (!empty($mergedParams)) {
            $url .= '?' . http_build_query($mergedParams);
        }
        return $url;
    }

    private function httpGet($url, $timeout = null, $retry = 2) {
        if ($timeout === null) {
            $timeout = $this->opts['timeout'] ?? 30;
        }
        $lastError = '';
        $userAgents = [
            $this->opts['user_agent'] ?? 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];
        for ($attempt = 0; $attempt <= $retry; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->opts['ssl_verify'] ?? false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->opts['ssl_verify'] ?? false ? 2 : 0);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json, text/plain, */*',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                'Referer: ' . (parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST) . '/')
            ]);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                return $response;
            }
            $lastError = $error ? $error : ('HTTP ' . $httpCode);
            if ($attempt < $retry) {
                usleep(300000 + $attempt * 200000);
            }
        }
        $this->lastHttpError = $lastError;
        return false;
    }
}
