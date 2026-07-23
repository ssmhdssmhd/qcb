<?php

class M3U8Parser {
    private $baseUrl = '';
    private $maxSegments = 5000;
    private $saveRaw = false;
    private $proxyManager = null;
    private $useProxy = true;
    private $forceProxy = null;
    private $useProxyOnFirstTry = true;
    private $timeout = 60;
    private $connectTimeout = 15;

    public function setMaxSegments($max) {
        $this->maxSegments = intval($max);
    }

    public function setTimeout($seconds) {
        $this->timeout = max(5, intval($seconds));
    }

    public function setConnectTimeout($seconds) {
        $this->connectTimeout = max(2, intval($seconds));
    }

    public function setSaveRaw($save) {
        $this->saveRaw = (bool)$save;
    }

    public function setUseProxy($useProxy) {
        $this->useProxy = (bool)$useProxy;
    }

    public function setProxyManager($proxyManager) {
        $this->proxyManager = $proxyManager;
    }

    public function setUseProxyOnFirstTry($use) {
        $this->useProxyOnFirstTry = (bool)$use;
    }

    public function setForceProxy($proxyUrl) {
        $this->forceProxy = $proxyUrl;
    }

    private function getProxyManager() {
        if ($this->proxyManager === null && $this->useProxy) {
            $proxyFile = __DIR__ . '/../proxy/ProxyManager.php';
            if (file_exists($proxyFile)) {
                require_once $proxyFile;
                $this->proxyManager = new ProxyManager();
            }
        }
        return $this->proxyManager;
    }

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

        $result = $this->parseContent($content);
        unset($content);
        return $result;
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
        $proxyMgr = $this->getProxyManager();
        $maxRetries = 3;
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_TCP_FASTOPEN, true);
            curl_setopt($ch, CURLOPT_TCP_NODELAY, true);
            curl_setopt($ch, CURLOPT_BUFFERSIZE, 262144);
            curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 1024);
            curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 30);

            $currentProxy = null;
            if ($this->forceProxy) {
                $proxyUrl = $this->forceProxy;
                $parsedProxy = parse_url($proxyUrl);
                if ($parsedProxy && !empty($parsedProxy['host'])) {
                    $proxyType = $parsedProxy['scheme'] ?? 'http';
                    $proxyHost = $parsedProxy['host'];
                    $proxyPort = $parsedProxy['port'] ?? 80;
                    $proxyUser = $parsedProxy['user'] ?? '';
                    $proxyPass = $parsedProxy['pass'] ?? '';
                    
                    if ($proxyType === 'socks5') {
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
                    } else {
                        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
                    }
                    curl_setopt($ch, CURLOPT_PROXY, $proxyHost . ':' . $proxyPort);
                    if ($proxyUser !== '' || $proxyPass !== '') {
                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);
                    }
                    $currentProxy = ['id' => 'force_proxy', 'url' => $proxyUrl];
                }
            } elseif ($proxyMgr && $proxyMgr->isEnabled() && ($this->useProxyOnFirstTry || $attempt > 0)) {
                $currentProxy = $proxyMgr->applyProxyToCurl($ch);
            }

            $startTime = microtime(true);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            curl_close($ch);

            if ($currentProxy) {
                if ($httpCode >= 200 && $httpCode < 300 && $result !== false) {
                    $proxyMgr->markProxySuccess($currentProxy['id'], $responseTime);
                    return $result;
                } else {
                    $proxyMgr->markProxyFailed($currentProxy['id']);
                }
            }

            if ($error) {
                $lastError = $error;
                if (strpos($error, 'Could not resolve') !== false || 
                    strpos($error, 'Connection timed out') !== false ||
                    strpos($error, 'Failed to connect') !== false ||
                    strpos($error, 'SSL_ERROR') !== false ||
                    strpos($error, 'SSL connect') !== false) {
                    if ($attempt < $maxRetries) {
                        usleep(500000);
                        continue;
                    }
                }
                throw new Exception('请求失败: ' . $error . ' (请检查网络连接或配置代理)');
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                $lastError = 'HTTP ' . $httpCode;
                if ($httpCode >= 500 || $httpCode == 429) {
                    if ($attempt < $maxRetries) {
                        usleep(1000000);
                        continue;
                    }
                }
                throw new Exception('HTTP ' . $httpCode);
            }

            return $result;
        }

        throw new Exception('请求失败: ' . $lastError);
    }

    public function parseContent($content) {
        $lines = [];
        $line = strtok($content, "\n");
        while ($line !== false) {
            $trimmed = trim($line);
            if ($trimmed !== '') {
                $lines[] = $trimmed;
            }
            $line = strtok("\n");
        }
        unset($content);

        $playlist = [
            'version' => 3,
            'targetDuration' => 0,
            'mediaSequence' => 0,
            'discontinuitySequence' => 0,
            'segments' => [],
            'isMaster' => false,
            'variants' => [],
            'mediaTags' => [],
            'dateRangeTags' => [],
            'extraAdTags' => [],
            'endlist' => false,
            'adMarkers' => [],
            'cueMarkers' => [],
            'scte35Markers' => [],
            'customTags' => []
        ];

        $currentSegment = null;
        $nextDiscontinuity = false;
        $pendingAdMarkers = [];
        $pendingCueMarkers = [];
        $pendingScte35 = null;
        $i = 0;
        $totalLines = count($lines);

        while ($i < $totalLines) {
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

            if (strpos($line, '#EXT-X-DISCONTINUITY-SEQUENCE:') === 0) {
                $playlist['discontinuitySequence'] = (int)substr($line, 30);
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-MEDIA:') === 0) {
                $attrs = $this->parseAttributes(substr($line, 15));
                $playlist['mediaTags'][] = [
                    'type' => isset($attrs['TYPE']) ? strtoupper($attrs['TYPE']) : '',
                    'uri' => isset($attrs['URI']) ? $attrs['URI'] : '',
                    'group_id' => isset($attrs['GROUP-ID']) ? $attrs['GROUP-ID'] : '',
                    'name' => isset($attrs['NAME']) ? $attrs['NAME'] : '',
                    'language' => isset($attrs['LANGUAGE']) ? $attrs['LANGUAGE'] : '',
                    'autoselect' => isset($attrs['AUTOSELECT']) ? strtoupper($attrs['AUTOSELECT']) === 'YES' : false,
                    'default' => isset($attrs['DEFAULT']) ? strtoupper($attrs['DEFAULT']) === 'YES' : false,
                    'forced' => isset($attrs['FORCED']) ? strtoupper($attrs['FORCED']) === 'YES' : false,
                    'instream_id' => isset($attrs['INSTREAM-ID']) ? $attrs['INSTREAM-ID'] : '',
                    'raw' => $line
                ];
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-STREAM-INF:') === 0) {
                $playlist['isMaster'] = true;
                $attrs = $this->parseAttributes(substr($line, 18));
                $i++;
                if ($i < $totalLines && strpos($lines[$i], '#') !== 0) {
                    $playlist['variants'][] = [
                        'uri' => $lines[$i],
                        'bandwidth' => isset($attrs['BANDWIDTH']) ? (int)$attrs['BANDWIDTH'] : 0,
                        'resolution' => isset($attrs['RESOLUTION']) ? $attrs['RESOLUTION'] : '',
                        'codecs' => isset($attrs['CODECS']) ? $attrs['CODECS'] : '',
                        'name' => isset($attrs['NAME']) ? $attrs['NAME'] : '',
                        'audio' => isset($attrs['AUDIO']) ? $attrs['AUDIO'] : '',
                        'video' => isset($attrs['VIDEO']) ? $attrs['VIDEO'] : '',
                        'subtitles' => isset($attrs['SUBTITLES']) ? $attrs['SUBTITLES'] : '',
                        'closed_captions' => isset($attrs['CLOSED-CAPTIONS']) ? $attrs['CLOSED-CAPTIONS'] : ''
                    ];
                }
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-CUE-OUT:') === 0) {
                $duration = (float)substr($line, 15);
                $cueMarker = [
                    'type' => 'cue-out',
                    'duration' => $duration,
                    'index' => count($playlist['segments']),
                    'raw' => $line
                ];
                $playlist['cueMarkers'][] = $cueMarker;
                $pendingCueMarkers[] = $cueMarker;
                $i++;
                continue;
            }

            if ($line === '#EXT-X-CUE-IN' || strpos($line, '#EXT-X-CUE-IN:') === 0) {
                $cueMarker = [
                    'type' => 'cue-in',
                    'index' => count($playlist['segments']),
                    'raw' => $line
                ];
                $playlist['cueMarkers'][] = $cueMarker;
                $pendingCueMarkers[] = $cueMarker;
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-OATCLS-SCTE35:') === 0) {
                $scte35 = [
                    'type' => 'oatcls-scte35',
                    'data' => substr($line, 21),
                    'index' => count($playlist['segments'])
                ];
                $playlist['scte35Markers'][] = $scte35;
                $pendingScte35 = $scte35;
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-SCTE35:') === 0) {
                $attrs = $this->parseAttributes(substr($line, 16));
                $scte35 = [
                    'type' => 'ext-x-scte35',
                    'attributes' => $attrs,
                    'index' => count($playlist['segments']),
                    'raw' => $line
                ];
                $playlist['scte35Markers'][] = $scte35;
                $pendingScte35 = $scte35;
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-AD:') === 0 || strpos($line, '#EXT-X-AD-') === 0) {
                $adMarker = [
                    'type' => 'ad-tag',
                    'tag' => $line,
                    'index' => count($playlist['segments']),
                    'raw' => $line
                ];
                $playlist['adMarkers'][] = $adMarker;
                $pendingAdMarkers[] = $adMarker;
                $i++;
                continue;
            }

            if (strpos($line, '#EXT-X-DATERANGE:') === 0) {
                $attrs = $this->parseAttributes(substr($line, 17));
                $class = $attrs['CLASS'] ?? '';
                $isAd = (
                    stripos($class, 'ad') !== false ||
                    stripos($class, 'companion') !== false ||
                    stripos($class, 'break') !== false ||
                    isset($attrs['SCTE35-CMD']) ||
                    isset($attrs['SCTE35-OUT']) ||
                    isset($attrs['SCTE35-IN'])
                );
                $dateRangeTag = [
                    'type' => 'daterange',
                    'class' => $class,
                    'is_ad' => $isAd,
                    'start_date' => $attrs['START-DATE'] ?? '',
                    'duration' => $attrs['DURATION'] ?? '',
                    'planned_duration' => $attrs['PLANNED-DURATION'] ?? '',
                    'scte35_cmd' => $attrs['SCTE35-CMD'] ?? '',
                    'scte35_out' => $attrs['SCTE35-OUT'] ?? '',
                    'scte35_in' => $attrs['SCTE35-IN'] ?? '',
                    'raw' => $line
                ];
                $playlist['dateRangeTags'][] = $dateRangeTag;
                if ($isAd) {
                    $pendingAdMarkers[] = $dateRangeTag;
                }
                $i++;
                continue;
            }

            $adTagPrefixes = [
                '#EXT-X-BREAK',
                '#EXT-X-AD-START',
                '#EXT-X-AD-END',
                '#EXT-X-AD-INSERT',
                '#EXT-X-AD-SIGNAL',
                '#EXT-X-AD-OPPORTUNITY',
                '#EXT-X-MARKER',
                '#EXT-X-PLAYOUT',
                '#EXT-X-SPLICE',
                '#EXT-X-CUE-POINT',
                '#EXT-X-PRIV',
                '#EXTOMCL',
                '#EXT-X-ASSET',
                '#EXT-X-CONTENT-STEERING',
            ];
            $matchedAdPrefix = false;
            foreach ($adTagPrefixes as $prefix) {
                if (strpos($line, $prefix) === 0) {
                    $playlist['extraAdTags'][] = [
                        'type' => 'extra-ad',
                        'tag' => $line,
                        'index' => count($playlist['segments']),
                        'raw' => $line
                    ];
                    $pendingAdMarkers[] = [
                        'type' => 'extra-ad',
                        'tag' => $line,
                        'index' => count($playlist['segments']),
                        'raw' => $line
                    ];
                    $matchedAdPrefix = true;
                    break;
                }
            }
            if ($matchedAdPrefix) {
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
                    'discontinuity' => $nextDiscontinuity,
                    'adMarkers' => [],
                    'cueMarkers' => [],
                    'scte35' => null
                ];

                if (!empty($pendingAdMarkers)) {
                    $currentSegment['adMarkers'] = $pendingAdMarkers;
                    $pendingAdMarkers = [];
                }
                if (!empty($pendingCueMarkers)) {
                    $currentSegment['cueMarkers'] = $pendingCueMarkers;
                    $pendingCueMarkers = [];
                }
                if ($pendingScte35 !== null) {
                    $currentSegment['scte35'] = $pendingScte35;
                    $pendingScte35 = null;
                }

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
                if (strpos($line, '#EXT') === 0) {
                    $playlist['customTags'][] = [
                        'tag' => $line,
                        'index' => count($playlist['segments'])
                    ];
                }
                $i++;
                continue;
            }

            if ($currentSegment && strpos($line, '#') !== 0) {
                $currentSegment['uri'] = $line;
                $playlist['segments'][] = $currentSegment;
                $currentSegment = null;

                if (count($playlist['segments']) >= $this->maxSegments) {
                    break;
                }
            }

            $i++;
        }

        unset($lines);
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

    public function resolveUri($uri) {
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
