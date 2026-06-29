<?php

class M3U8Parser {
    private $baseUrl = '';

    public function parse($input) {
        $content = '';
        $this->baseUrl = '';

        if ($this->isUrl($input)) {
            $content = $this->fetchUrl($input);
            $this->baseUrl = $this->getBaseUrl($input);
        } elseif (strpos($input, '#EXTM3U') !== false) {
            $content = $input;
            $this->baseUrl = '';
        } elseif (file_exists($input)) {
            $content = file_get_contents($input);
            $this->baseUrl = '';
        } else {
            throw new Exception('无效的输入：不是URL、文件或M3U8内容');
        }

        return $this->parseContent($content);
    }

    private function isUrl($str) {
        return (bool)preg_match('/^https?:\/\//i', $str);
    }

    private function getBaseUrl($url) {
        $parsed = parse_url($url);
        $pathParts = explode('/', $parsed['path']);
        array_pop($pathParts);
        return $parsed['scheme'] . '://' . $parsed['host'] . implode('/', $pathParts) . '/';
    }

    private function fetchUrl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
        curl_setopt($ch, CURLOPT_TCP_NODELAY, true);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 131072);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception('请求失败: ' . $error);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new Exception('HTTP ' . $httpCode);
        }

        curl_close($ch);
        return $result;
    }

    public function parseContent($content) {
        $lines = explode("\n", $content);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, function($l) {
            return strlen($l) > 0;
        });
        $lines = array_values($lines);

        $playlist = [
            'version' => 3,
            'targetDuration' => 0,
            'mediaSequence' => 0,
            'segments' => [],
            'isMaster' => false,
            'variants' => [],
            'raw' => $content,
            'endlist' => false
        ];

        $currentSegment = null;
        $nextDiscontinuity = false;
        $i = 0;

        while ($i < count($lines)) {
            $line = $lines[$i];

            if ($line === '#EXTM3U') {
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-VERSION:') === 0) {
                $playlist['version'] = (int)substr($line, 15);
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-TARGETDURATION:') === 0) {
                $playlist['targetDuration'] = (float)substr($line, 22);
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-MEDIA-SEQUENCE:') === 0) {
                $playlist['mediaSequence'] = (int)substr($line, 22);
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-STREAM-INF:') === 0) {
                $playlist['isMaster'] = true;
                $attrs = $this->parseAttributes(substr($line, 18));
                $i++;
                if ($i < count($lines) && strpos($lines[$i], '#') !== 0) {
                    $playlist['variants'][] = [
                        'uri' => $lines[$i],
                        'bandwidth' => isset($attrs['BANDWIDTH']) ? (int)$attrs['BANDWIDTH'] : 0,
                        'resolution' => isset($attrs['RESOLUTION']) ? $attrs['RESOLUTION'] : '',
                        'codecs' => isset($attrs['CODECS']) ? $attrs['CODECS'] : '',
                        'name' => isset($attrs['NAME']) ? $attrs['NAME'] : ''
                    ];
                }
                $i++;
                continue;
            }

            if (strpos($line, '#EXTINF:') === 0) {
                $parts = explode(',', substr($line, 8), 2);
                $duration = (float)$parts[0];
                $title = isset($parts[1]) ? trim($parts[1]) : '';
                $currentSegment = [
                    'duration' => $duration,
                    'title' => $title,
                    'uri' => '',
                    'byteRange' => null,
                    'discontinuity' => $nextDiscontinuity,
                    'tags' => []
                ];
                $nextDiscontinuity = false;
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-BYTERANGE:') === 0) {
                if ($currentSegment) {
                    $range = substr($line, 17);
                    $parts = explode('@', $range);
                    $currentSegment['byteRange'] = [
                        'length' => (int)$parts[0],
                        'offset' => isset($parts[1]) ? (int)$parts[1] : 0
                    ];
                }
                $i++;
                continue;
            }

            if ($line === '#EXT-X-DISCONTINUITY') {
                $nextDiscontinuity = true;
                $i++;
                continue;
            }

            if ($line === '#EXT-X-ENDLIST') {
                $playlist['endlist'] = true;
                $i++;
                continue;
            }

            if (strpos($line, '#') === 0) {
                if ($currentSegment) {
                    $currentSegment['tags'][] = $line;
                }
                $i++;
                continue;
            }

            if ($currentSegment && strpos($line, '#') !== 0) {
                $currentSegment['uri'] = $line;
                $currentSegment['absoluteUri'] = $this->resolveUri($line);
                $playlist['segments'][] = $currentSegment;
                $currentSegment = null;
                $i++;
                continue;
            }

            $i++;
        }

        return $playlist;
    }

    private function parseAttributes($attrString) {
        $attrs = [];
        preg_match_all('/([A-Z0-9-]+)=("[^"]*"|[^,]+)/', $attrString, $matches);
        for ($i = 0; $i < count($matches[0]); $i++) {
            $key = $matches[1][$i];
            $value = $matches[2][$i];
            if (strpos($value, '"') === 0 && substr($value, -1) === '"') {
                $value = substr($value, 1, -1);
            }
            $attrs[$key] = $value;
        }
        return $attrs;
    }

    private function resolveUri($uri) {
        if (!$this->baseUrl || preg_match('/^https?:\/\//i', $uri)) {
            return $uri;
        }
        if (strpos($uri, '/') === 0) {
            $parsed = parse_url($this->baseUrl);
            return $parsed['scheme'] . '://' . $parsed['host'] . $uri;
        }
        return $this->baseUrl . $uri;
    }
}
