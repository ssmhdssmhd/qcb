<?php

class CctvSourceVerifier
{
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function verifyOne($url, $timeout = 5)
    {
        if (empty($url)) {
            return false;
        }

        if (!preg_match('/^https?:\/\//i', $url)) {
            return true;
        }

        $startTime = microtime(true);
        $result = $this->verifyUrlWithCurl($url, $timeout);
        $endTime = microtime(true);
        $latency = (int)(($endTime - $startTime) * 1000);

        return array(
            'verified' => $result,
            'latency_ms' => $result ? $latency : -1
        );
    }

    private function verifyUrlWithCurl($url, $timeout)
    {
        if (!function_exists('curl_init')) {
            return $this->verifyUrlWithFsockopen($url, $timeout);
        }

        $ch = curl_init();
        $isM3u8 = (stripos($url, '.m3u8') !== false);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

        if ($isM3u8) {
            curl_setopt($ch, CURLOPT_RANGE, '0-2047');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Accept: */*',
                'Range: bytes=0-2047'
            ));
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
            curl_close($ch);

            if ($httpCode === 206 || $httpCode === 200) {
                if ($size > 0 || !empty($content)) {
                    if (!empty($content) && (strpos($content, '#EXTM3U') !== false || strpos($content, '#EXTINF') !== false || strlen($content) > 10)) {
                        return true;
                    }
                    if ($size > 0) {
                        return true;
                    }
                }
            }
            return false;
        }

        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: */*'));

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if (!empty($error)) {
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 400) {
            return true;
        }

        return $this->verifyUrlWithFsockopen($url, $timeout);
    }

    private function verifyUrlWithFsockopen($url, $timeout)
    {
        $parsed = @parse_url($url);
        if (!is_array($parsed) || !isset($parsed['host'])) {
            return false;
        }

        $host = $parsed['host'];
        $port = isset($parsed['port']) ? (int)$parsed['port'] : ($parsed['scheme'] === 'https' ? 443 : 80);
        $path = isset($parsed['path']) ? $parsed['path'] : '/';
        if (isset($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        $errno = 0;
        $errstr = '';
        $scheme = ($parsed['scheme'] === 'https') ? 'ssl://' : '';
        $fp = @fsockopen($scheme . $host, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            return false;
        }

        stream_set_timeout($fp, $timeout);
        $request = "HEAD " . $path . " HTTP/1.1\r\n";
        $request .= "Host: " . $host . "\r\n";
        $request .= "User-Agent: " . $this->userAgent . "\r\n";
        $request .= "Connection: Close\r\n\r\n";

        @fwrite($fp, $request);
        $response = '';
        while (!feof($fp)) {
            $line = @fgets($fp, 1024);
            if ($line === false) {
                break;
            }
            $response .= $line;
            if (strpos($response, "\r\n\r\n") !== false) {
                break;
            }
        }
        @fclose($fp);

        if (empty($response)) {
            return false;
        }

        if (preg_match('/^HTTP\/\d\.\d\s+(\d{3})/', $response, $m)) {
            $code = (int)$m[1];
            if ($code >= 200 && $code < 400) {
                return true;
            }
            if ($code >= 400 && $code < 500) {
                return $this->verifyUrlWithFsockopenGet($host, $port, $path, $parsed, $timeout);
            }
        }

        return false;
    }

    private function verifyUrlWithFsockopenGet($host, $port, $path, $parsed, $timeout)
    {
        $errno = 0;
        $errstr = '';
        $scheme = (isset($parsed['scheme']) && $parsed['scheme'] === 'https') ? 'ssl://' : '';
        $fp = @fsockopen($scheme . $host, $port, $errno, $errstr, $timeout);
        if (!$fp) {
            return false;
        }

        stream_set_timeout($fp, $timeout);
        $request = "GET " . $path . " HTTP/1.1\r\n";
        $request .= "Host: " . $host . "\r\n";
        $request .= "User-Agent: " . $this->userAgent . "\r\n";
        $request .= "Range: bytes=0-511\r\n";
        $request .= "Connection: Close\r\n\r\n";

        @fwrite($fp, $request);
        $response = '';
        $headerEnd = false;
        $bodySize = 0;
        $startTime = microtime(true);
        $maxTime = $timeout;

        while (!feof($fp)) {
            if ((microtime(true) - $startTime) > $maxTime) {
                break;
            }
            $line = @fgets($fp, 1024);
            if ($line === false) {
                break;
            }
            $response .= $line;

            if (!$headerEnd && strpos($response, "\r\n\r\n") !== false) {
                $headerEnd = true;
                continue;
            }

            if ($headerEnd) {
                $bodySize += strlen($line);
                if ($bodySize >= 64) {
                    break;
                }
            }
        }
        @fclose($fp);

        if (empty($response)) {
            return false;
        }

        if (preg_match('/^HTTP\/\d\.\d\s+(\d{3})/', $response, $m)) {
            $code = (int)$m[1];
            if ($code >= 200 && $code < 400) {
                if ($bodySize > 0 || $code === 206 || $code === 200) {
                    return true;
                }
            }
        }

        return !empty($response);
    }

    public function verifyBatch($sources, $maxConcurrent = 10)
    {
        if (empty($sources)) {
            return array();
        }

        if (!is_array($sources)) {
            return $sources;
        }

        $isMulti = isset($sources[0]) && is_array($sources[0]) && isset($sources[0]['url']);

        if ($isMulti) {
            if (function_exists('curl_multi_init') && $maxConcurrent > 1) {
                return $this->verifyBatchMulti($sources, $maxConcurrent);
            }
            return $this->verifyBatchSequential($sources);
        }

        return $this->verifyBatchSequential($sources);
    }

    private function verifyBatchSequential($sources)
    {
        $results = array();
        foreach ($sources as $idx => $source) {
            if (is_array($source) && isset($source['url'])) {
                $v = $this->verifyOne($source['url'], 5);
                $source['verified'] = $v['verified'];
                $source['latency_ms'] = $v['latency_ms'];
                $results[$idx] = $source;
            } else {
                if (is_string($source)) {
                    $v = $this->verifyOne($source, 5);
                    $results[$idx] = array(
                        'url' => $source,
                        'verified' => $v['verified'],
                        'latency_ms' => $v['latency_ms']
                    );
                } else {
                    $results[$idx] = $source;
                }
            }
        }
        return $results;
    }

    private function verifyBatchMulti($sources, $maxConcurrent)
    {
        $results = $sources;
        $total = count($sources);
        $handled = 0;

        while ($handled < $total) {
            $batchSize = min($maxConcurrent, $total - $handled);
            $multiHandle = curl_multi_init();
            $handles = array();
            $indexMap = array();
            $startTimes = array();

            for ($i = 0; $i < $batchSize; $i++) {
                $idx = $handled + $i;
                $source = $sources[$idx];
                if (!is_array($source) || !isset($source['url'])) {
                    $results[$idx]['verified'] = false;
                    $results[$idx]['latency_ms'] = -1;
                    continue;
                }
                $url = $source['url'];
                if (!preg_match('/^https?:\/\//i', $url)) {
                    $results[$idx]['verified'] = true;
                    $results[$idx]['latency_ms'] = 0;
                    continue;
                }

                $ch = curl_init();
                $isM3u8 = (stripos($url, '.m3u8') !== false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
                curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_NOSIGNAL, 1);

                if ($isM3u8) {
                    curl_setopt($ch, CURLOPT_RANGE, '0-2047');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Accept: */*',
                        'Range: bytes=0-2047'
                    ));
                } else {
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: */*'));
                }

                $handles[$i] = $ch;
                $indexMap[$i] = $idx;
                $startTimes[$i] = microtime(true);
                curl_multi_add_handle($multiHandle, $ch);
            }

            if (!empty($handles)) {
                $active = null;
                do {
                    $mrc = curl_multi_exec($multiHandle, $active);
                } while ($mrc === CURLM_CALL_MULTI_PERFORM);

                while ($active && $mrc === CURLM_OK) {
                    if (curl_multi_select($multiHandle, 5.0) === -1) {
                        usleep(100000);
                    }
                    do {
                        $mrc = curl_multi_exec($multiHandle, $active);
                    } while ($mrc === CURLM_CALL_MULTI_PERFORM);
                }

                foreach ($handles as $i => $ch) {
                    $idx = $indexMap[$i];
                    $endTime = microtime(true);
                    $latency = (int)(($endTime - $startTimes[$i]) * 1000);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $error = curl_error($ch);
                    $content = curl_multi_getcontent($ch);
                    $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

                    $verified = false;
                    if (empty($error) && $httpCode >= 200 && $httpCode < 400) {
                        $source = $sources[$idx];
                        $isM3u8 = (isset($source['url']) && stripos($source['url'], '.m3u8') !== false);
                        if ($isM3u8) {
                            if ($httpCode === 206 || $httpCode === 200) {
                                if ($size > 0 || !empty($content)) {
                                    $verified = true;
                                }
                            }
                        } else {
                            $verified = true;
                        }
                    }

                    if (!$verified && empty($error)) {
                        $fallback = $this->verifyUrlWithFsockopen($sources[$idx]['url'], 3);
                        if ($fallback) {
                            $verified = true;
                        }
                    }

                    $results[$idx]['verified'] = $verified;
                    $results[$idx]['latency_ms'] = $verified ? $latency : -1;

                    curl_multi_remove_handle($multiHandle, $ch);
                    curl_close($ch);
                }
            }

            curl_multi_close($multiHandle);
            $handled += $batchSize;
        }

        return $results;
    }

    public function pickBestByChannel($groupedSources)
    {
        $grouped = $this->groupByChannel($groupedSources);
        $result = array();

        foreach ($grouped as $channelId => $channelData) {
            $sources = isset($channelData['sources']) ? $channelData['sources'] : array();
            $verified = array();
            $unverified = array();

            foreach ($sources as $src) {
                if (isset($src['verified']) && $src['verified'] === true) {
                    $verified[] = $src;
                } else {
                    $unverified[] = $src;
                }
            }

            usort($verified, function ($a, $b) {
                $la = isset($a['latency_ms']) ? (int)$a['latency_ms'] : 999999;
                $lb = isset($b['latency_ms']) ? (int)$b['latency_ms'] : 999999;
                if ($la <= 0) $la = 999999;
                if ($lb <= 0) $lb = 999999;
                return $la - $lb;
            });

            $best3 = array_slice($verified, 0, 3);

            if (count($best3) < 3) {
                foreach ($unverified as $src) {
                    $best3[] = $src;
                    if (count($best3) >= 3) {
                        break;
                    }
                }
            }

            if (empty($best3)) {
                continue;
            }

            $base = $channelData;
            unset($base['sources']);
            $base['url'] = isset($best3[0]['url']) ? $best3[0]['url'] : '';
            $base['urls'] = array();
            foreach ($best3 as $b) {
                if (isset($b['url'])) {
                    $base['urls'][] = $b['url'];
                }
            }
            $base['sources'] = $best3;
            if (isset($best3[0]['latency_ms'])) {
                $base['latency_ms'] = $best3[0]['latency_ms'];
            }

            $result[$channelId] = $base;
        }

        return $result;
    }

    private function groupByChannel($sources)
    {
        $grouped = array();

        if (isset($sources[0]) && is_array($sources[0]) && isset($sources[0]['sources'])) {
            return $sources;
        }

        foreach ($sources as $src) {
            if (!is_array($src)) {
                continue;
            }
            $id = isset($src['id']) ? $src['id'] : '';
            if (empty($id)) {
                $name = isset($src['name']) ? $src['name'] : '';
                $id = md5($name);
            }

            if (!isset($grouped[$id])) {
                $grouped[$id] = array(
                    'id' => isset($src['id']) ? $src['id'] : $id,
                    'name' => isset($src['name']) ? $src['name'] : '',
                    'group' => isset($src['group']) ? $src['group'] : '',
                    'logo' => isset($src['logo']) ? $src['logo'] : '',
                    'quality' => isset($src['quality']) ? $src['quality'] : 'SD',
                    'sources' => array()
                );
            }

            $grouped[$id]['sources'][] = $src;
        }

        return $grouped;
    }
}
