<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$memoryLimit = @ini_get('memory_limit');
if (return_bytes_func($memoryLimit) < 512 * 1024 * 1024) {
    @ini_set('memory_limit', '512M');
}

function return_bytes_func($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    ob_end_flush();
    exit;
}

function sendJsonResponse($data, $code = 200) {
    http_response_code($code);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function parse_internal_xiami($url) {
    $apiEndpoints = [
        'https://cache.0567890.xyz:4433/Api',
        'https://cache.hls.one/Api',
    ];

    $tm = intval(round(microtime(true) * 1000));
    $keyHex = md5($tm . $url);

    $aesKeyHex = md5($keyHex);
    $iv = 'fUU9eRmkYzsgbkEK';
    $plaintext = $keyHex;
    $blockSize = 16;
    $padLen = $blockSize - (strlen($plaintext) % $blockSize);
    if ($padLen == $blockSize) $padLen = 0;
    $padded = $plaintext . str_repeat("\x00", $padLen);
    $sign = @openssl_encrypt(
        $padded,
        'aes-256-cbc',
        $aesKeyHex,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv
    );
    if ($sign !== false) {
        $sign = base64_encode($sign);
    }

    $playUrl = '';
    $lastError = '';
    $videoType = '';

    if (!empty($sign)) {
        $postData = [
            'tm'   => $tm,
            'url'  => $url,
            'key'  => $keyHex,
            'sign' => $sign,
        ];

        foreach ($apiEndpoints as $api) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $api,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($postData),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 25,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                    'Accept: application/json, text/javascript, */*; q=0.01',
                    'Origin: https://jx.xmflv.cc',
                    'Referer: https://jx.xmflv.cc/',
                    'X-Requested-With: XMLHttpRequest',
                ],
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($response === false || $httpCode !== 200) {
                $lastError = $curlError ?: "HTTP $httpCode";
                continue;
            }

            $body = str_replace('tg:@xmflv', '', $response);
            $json = json_decode($body, true);

            if ($json === null || !isset($json['code'])) {
                $lastError = '响应解析失败';
                continue;
            }

            if ($json['code'] !== 200) {
                $lastError = isset($json['msg']) ? $json['msg'] : '解析失败';
                continue;
            }

            if (empty($json['data']) || empty($json['key']) || empty($json['iv'])) {
                $lastError = '响应缺少 data/key/iv 字段';
                continue;
            }

            $ciphertext = base64_decode($json['data'], true);
            $decKey = $json['key'];
            $decIv = $json['iv'];

            if ($ciphertext === false || strlen($ciphertext) === 0) {
                $lastError = '解密数据无效';
                continue;
            }

            $keyLen = strlen($decKey);
            if ($keyLen <= 16) {
                $method = 'aes-128-cbc';
            } elseif ($keyLen <= 24) {
                $method = 'aes-192-cbc';
            } else {
                $method = 'aes-256-cbc';
            }

            if ($keyLen < 16) {
                $lastError = '密钥长度不足';
                continue;
            }

            $decrypted = @openssl_decrypt(
                $ciphertext,
                $method,
                $decKey,
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $decIv
            );

            if ($decrypted !== false && strlen($decrypted) > 0) {
                $decrypted = rtrim($decrypted, "\x00");
                $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
            } else {
                $decrypted = @openssl_decrypt(
                    $ciphertext,
                    $method,
                    $decKey,
                    OPENSSL_RAW_DATA,
                    $decIv
                );
                if ($decrypted !== false) {
                    $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                    $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
                }
            }

            if ($decrypted === false || strlen($decrypted) === 0) {
                $lastError = '解密失败';
                continue;
            }

            $resultData = json_decode($decrypted, true);
            if ($resultData === null) {
                $lastError = '解密数据解析失败';
                continue;
            }

            $playUrl = isset($resultData['vurl']) ? $resultData['vurl'] : (isset($resultData['url']) ? $resultData['url'] : '');
            $videoType = isset($resultData['type']) ? $resultData['type'] : '';
            break;
        }
    }

    $label = '';
    if (strpos($videoType, 'm3u8') !== false || strpos($videoType, 'hls') !== false) {
        $label = 'HLS';
    } elseif (strpos($videoType, 'mp4') !== false) {
        $label = 'MP4';
    }

    if (empty($playUrl)) {
        return [
            'success' => false,
            'code' => 500,
            'message' => $lastError ?: '未获取到资源',
            'play_url' => '',
            'video_type' => '',
            'label' => '',
        ];
    }

    return [
        'success' => true,
        'code' => 200,
        'message' => '解析成功',
        'play_url' => $playUrl,
        'video_type' => $videoType,
        'label' => $label,
        'original_url' => $url,
        'source' => 'xiami',
    ];
}

function parse_internal_moxi($url, $selfUrl, $officialReplaceMgr = null, $siteManager = null) {
    $officialDomains = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];
    $parsedUrl = parse_url($url);
    $urlHost = $parsedUrl['host'] ?? '';
    $isOfficialUrl = false;

    foreach ($officialDomains as $domain) {
        if (strpos($urlHost, $domain) !== false) {
            $isOfficialUrl = true;
            break;
        }
    }

    $extractTitleFromUrl = function($url) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $host = $parsed['host'] ?? '';
        if (empty($path)) return $host ?: '在线视频';
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        if (empty($pathParts)) return $host ?: '在线视频';
        $fileName = end($pathParts);
        $fileNameWithoutExt = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $fileName);
        $isEpisodeLike = false;
        if (preg_match('/第?\d+[集期话]/u', $fileNameWithoutExt)) $isEpisodeLike = true;
        if (preg_match('/^(episode|ep|e|集|期|话)[_\-]?\d+$/i', $fileNameWithoutExt)) $isEpisodeLike = true;
        if (preg_match('/^\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 4) $isEpisodeLike = true;
        if (preg_match('/[_\-]\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 15) {
            $prefix = preg_replace('/[_\-]\d+$/', '', $fileNameWithoutExt);
            if (in_array(strtolower($prefix), ['episode', 'ep', 'e', '第', '集', ''])) $isEpisodeLike = true;
        }
        if ($isEpisodeLike || $fileName === 'index.m3u8' || $fileNameWithoutExt === 'index') {
            $candidates = [];
            $dirParts = array_slice($pathParts, 0, -1);
            foreach (array_reverse($dirParts) as $part) {
                if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                if (is_numeric($part)) continue;
                if (strlen($part) < 2) continue;
                $lowerPart = strtolower($part);
                if (in_array($lowerPart, ['video', 'videos', 'm3u8', 'movie', 'tv', 'play', 'player'])) continue;
                $candidates[] = $part;
            }
            if (!empty($candidates)) {
                $title = preg_replace('/[_-]+/', ' ', $candidates[0]);
                $title = trim($title);
                if (!empty($title)) {
                    if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
                    return $title;
                }
            }
            return $host ?: '在线视频';
        }
        $title = preg_replace('/[_-]+/', ' ', $fileNameWithoutExt);
        $title = preg_replace('/\s*\d+\s*$/', '', $title);
        $title = trim($title);
        if (empty($title) || strlen($title) < 2) {
            $dirParts = array_slice($pathParts, 0, -1);
            foreach (array_reverse($dirParts) as $part) {
                if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                if (is_numeric($part)) continue;
                if (strlen($part) < 2) continue;
                if (in_array(strtolower($part), ['video', 'videos', 'm3u8', 'movie', 'tv'])) continue;
                $title = preg_replace('/[_-]+/', ' ', $part);
                $title = trim($title);
                if (!empty($title)) {
                    if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
                    return $title;
                }
            }
            return $host ?: '在线视频';
        }
        if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
        return $title;
    };

    $extractEpisodeFromUrl = function($url) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        if (empty($path)) return '正片';
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        foreach (array_reverse($pathParts) as $part) {
            $part = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $part);
            if (preg_match('/第(\d+)[集期话]/u', $part, $m)) return '第' . $m[1] . '集';
            if (preg_match('/(?:episode|ep|e)[_\-]?(\d+)/i', $part, $m)) return '第' . intval($m[1]) . '集';
            if (preg_match('/^(\d+)$/', $part, $m)) {
                $num = intval($m[1]);
                if ($num > 0 && $num < 1000) return '第' . $num . '集';
            }
            if (preg_match('/[_\-](\d+)$/', $part, $m)) {
                $num = intval($m[1]);
                if ($num > 0 && $num < 1000) {
                    $prefix = preg_replace('/[_\-]\d+$/', '', $part);
                    if (empty($prefix) || in_array(strtolower($prefix), ['episode', 'ep', 'e'])) return '第' . $num . '集';
                }
            }
        }
        return '正片';
    };

    $playUrl = '';
    $juMing = '';
    $jiShu = '';
    $code = 200;
    $msg = '解析成功';

    if ($isOfficialUrl && $officialReplaceMgr) {
        $result = $officialReplaceMgr->resolve($url);
        if ($result['success']) {
            $m3u8Url = $result['m3u8_url'] ?? '';
            $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
            $juMing = $result['video_title'] ?? '';
            $jiShu = $result['target_episode'] ?? ($result['episode'] ?? '');
            if (empty($jiShu)) $jiShu = '正片';
        } else {
            $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
            $juMing = $result['video_title'] ?? '';
            if (empty($juMing)) $juMing = $extractTitleFromUrl($url);
            $jiShu = $result['episode'] ?? '';
            if (empty($jiShu)) $jiShu = $extractEpisodeFromUrl($url);
        }
    } else {
        $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
        $juMing = $extractTitleFromUrl($url);
        $jiShu = $extractEpisodeFromUrl($url);

        $searchKeyword = '';
        $path = $parsedUrl['path'] ?? '';
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        foreach ($pathParts as $part) {
            if (preg_match('/\.(m3u8|mp4|mkv|avi|mov|flv|ts)$/i', $part)) continue;
            if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
            if (is_numeric($part)) continue;
            if (strlen($part) < 3) continue;
            if ($part === 'video' || $part === 'videos' || $part === 'm3u8') continue;
            $searchKeyword = $part;
            break;
        }
        if (!empty($searchKeyword) && $searchKeyword !== $juMing) {
            $searchKeyword = trim(preg_replace('/[_-]+/', ' ', $searchKeyword));
        }

        if (!empty($searchKeyword) && $siteManager) {
            try {
                $searchResult = $siteManager->searchAllSites($searchKeyword, 3, 5);
                if ($searchResult['success'] && !empty($searchResult['results'])) {
                    $bestMatch = null;
                    $bestScore = 0;
                    $urlBase = basename($path, '.m3u8');
                    foreach ($searchResult['results'] as $siteResult) {
                        if (empty($siteResult['videos'])) continue;
                        foreach ($siteResult['videos'] as $video) {
                            $videoName = $video['name'] ?? '';
                            if (empty($videoName)) continue;
                            $score = 0;
                            similar_text($searchKeyword, $videoName, $score);
                            $firstUrl = $video['first_url'] ?? $video['url'] ?? '';
                            if (!empty($firstUrl)) {
                                $firstUrlPath = parse_url($firstUrl, PHP_URL_PATH) ?? '';
                                $pathScore = 0;
                                similar_text($path, $firstUrlPath, $pathScore);
                                if ($pathScore > $score) $score = $pathScore;
                            }
                            if ($score > $bestScore && $score > 40) {
                                $bestScore = $score;
                                $bestMatch = $video;
                            }
                        }
                    }
                    if ($bestMatch && $bestScore > 50) {
                        $juMing = $bestMatch['name'] ?? $juMing;
                        if (!empty($bestMatch['remarks'])) $jiShu = $bestMatch['remarks'];
                    }
                }
            } catch (\Exception $e) {}
        }
    }

    return [
        'success' => true,
        'code' => $code,
        'message' => $msg,
        'play_url' => $playUrl,
        'video_name' => $juMing,
        'episode' => $jiShu,
        'original_url' => $url,
        'is_official' => $isOfficialUrl,
        'source' => 'moxi',
    ];
}

function jsonErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (in_array($errno, $fatalTypes)) {
        @sendJsonResponse([
            'success' => false,
            'message' => $errstr,
            'error_detail' => [
                'type' => $errno,
                'file' => basename($errfile),
                'line' => $errline
            ]
        ], 500);
    }
    return true;
}
set_error_handler('jsonErrorHandler');

function jsonFatalHandler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'message' => $error['message'],
            'error_detail' => [
                'file' => basename($error['file']),
                'line' => $error['line']
            ],
            'fatal_error' => true
        ], JSON_UNESCAPED_UNICODE);
    }
}
register_shutdown_function('jsonFatalHandler');

try {
    $rootDir = __DIR__;

    $requiredFiles = [
        $rootDir . '/src/M3U8AdSkipper.php',
        $rootDir . '/src/M3U8Parser.php',
        $rootDir . '/src/UpdateManager.php',
        $rootDir . '/src/CryptoUtil.php',
        $rootDir . '/src/AuthConfig.php',
        $rootDir . '/src/AuthValidator.php',
        $rootDir . '/src/CacheManager.php',
        $rootDir . '/gz/EnhancedAdRuleEngine.php',
        $rootDir . '/gz/DomainRuleManager.php',
        $rootDir . '/gz/ResourceSiteManager.php',
        $rootDir . '/gz/OfficialSiteManager.php',
        $rootDir . '/gz/OfficialReplaceManager.php',
        $rootDir . '/multi_thread/autoload.php',
    ];

    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            sendJsonResponse([
                'success' => false,
                'message' => '文件缺失: ' . basename($file)
            ], 500);
        }
        require_once $file;
    }

    $useDb = false;
    $dbConfigFile = $rootDir . '/db/db_config.php';
    if (file_exists($dbConfigFile)) {
        require_once $rootDir . '/db/autoload.php';
        try {
            $db = Database::getInstance();
            if (!$db->tableExists('sys_config')) {
                $db->initTables();
            }
            $useDb = true;
        } catch (Throwable $e) {
            $useDb = false;
            error_log('数据库初始化失败，降级使用文件存储: ' . $e->getMessage());
        }
    }

    ob_clean();

} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => '初始化失败: ' . $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}

try {
    if ($useDb) {
        $ruleManager = new DbDomainRuleManager();
        $siteManager = new DbResourceSiteManager();
        $officialReplaceMgr = new DbOfficialReplaceManager();
        $officialMgr = new DbOfficialSiteManager();
        $proxyManager = new DbProxyManager();
    } else {
        $ruleManager = new DomainRuleManager();
        $siteManager = new ResourceSiteManager();
        $officialReplaceMgr = new OfficialReplaceManager();
        $officialMgr = new OfficialSiteManager();
        if (file_exists($rootDir . '/proxy/ProxyManager.php')) {
            require_once $rootDir . '/proxy/ProxyManager.php';
            $proxyManager = new ProxyManager($rootDir . '/proxy/proxy_config.php');
        }
    }
    $updateManager = new UpdateManager();
    $authValidator = new AuthValidator();
} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => '类初始化失败: ' . $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function getInputJson() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

function resolveMasterPlaylist($url, $proxy = '') {
    $parser = new M3U8Parser();
    if ($proxy) {
        $parser->setForceProxy($proxy);
    }
    try {
        $playlist = $parser->parse($url);
        if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
            $firstVariant = $playlist['variants'][0]['uri'] ?? '';
            if ($firstVariant) {
                $parsedUrl = parse_url($url);
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;
                if (strpos($firstVariant, '/') === 0) {
                    return $baseUrl . $firstVariant;
                } else {
                    return $baseUrl . $pathDir . '/' . $firstVariant;
                }
            }
        }
    } catch (Exception $e) {
    }
    return $url;
}

try {
    switch ($action) {
        case 'analyze':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            $skipCache = isset($_GET['skip_cache']) && ($_GET['skip_cache'] === '1' || $_GET['skip_cache'] === 'true');
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }

            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            // ===== 数据库缓存查询 =====
            // 数据库不可用时安全降级，不影响核心分析功能
            $analysisCache = null;
            $domainStats = null;
            $adSignature = null;
            $dbCacheAvailable = $useDb;
            if ($useDb) {
                try {
                    $analysisCache = new DbAnalysisCache();
                    $domainStats = new DbDomainAnalysisStats();
                    $adSignature = new DbAdSignature();
                } catch (Throwable $e) {
                    $dbCacheAvailable = false;
                    error_log('分析缓存初始化失败（不影响分析功能）: ' . $e->getMessage());
                }
            }

            if (!$skipCache && $analysisCache) {
                $cached = $analysisCache->get($url);
                if ($cached) {
                    // 更新分析统计
                    if ($domainStats) {
                        $domainStats->recordAnalyze($domain, $cached['total_segments'], $cached['ad_segments'], $cached['ad_percentage']);
                    }

                    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                    $basePath = dirname($requestUri);
                    $basePath = $basePath === '/' ? '' : $basePath;
                    $selfUrl = $scheme . '://' . $host . $basePath;
                    $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($cached['media_url']);

                    sendJsonResponse([
                        'success' => true,
                        'url' => $url,
                        'mediaUrl' => $cached['media_url'],
                        'domain' => $domain,
                        'hasDomainRules' => !empty($cached['duration_rules']) || !empty($cached['discontinuity_rules']),
                        'fastMode' => (bool)$cached['fast_mode'],
                        'safeguardTriggered' => (bool)$cached['safeguard_triggered'],
                        'message' => '从数据库缓存获取分析结果',
                        'cached' => true,
                        'mxjxUrl' => $mxjxUrl,
                        'playlist' => [
                            'isMaster' => false,
                            'version' => 3,
                            'targetDuration' => 0,
                            'endlist' => true
                        ],
                        'stats' => [
                            'totalSegments' => (int)$cached['total_segments'],
                            'adSegments' => (int)$cached['ad_segments'],
                            'keptSegments' => (int)($cached['kept_segments'] ?? ($cached['total_segments'] - $cached['ad_segments'])),
                            'originalDuration' => (float)($cached['original_duration'] ?? 0),
                            'filteredDuration' => (float)($cached['filtered_duration'] ?? 0),
                            'savedDuration' => (float)($cached['saved_duration'] ?? 0),
                            'adPercentage' => (float)$cached['ad_percentage']
                        ],
                        'duration_rules' => $cached['duration_rules'],
                        'discontinuity_rules' => $cached['discontinuity_rules'],
                        'sequence_jump_rules' => $cached['sequence_jump_rules'],
                        'filename_patterns' => $cached['filename_patterns']
                    ]);
                    break;
                }
            }

            $domainRules = $ruleManager->getRules($domain);
            $hasDomainRules = $domainRules !== null && !empty($domainRules['duration_rules']) && !empty($domainRules['discontinuity_rules']);

            if ($hasDomainRules) {
                $mediaUrl = resolveMasterPlaylist($url);
                $engine = new EnhancedAdRuleEngine([
                    'checkDiscontinuity' => true,
                    'checkRepetitiveDuration' => true
                ]);
                $engine->setDomain($domain);

                $skipper = new M3U8AdSkipper();
                $reflection = new ReflectionClass($skipper);
                $ruleEngineProp = $reflection->getProperty('ruleEngine');
                $ruleEngineProp->setAccessible(true);
                $ruleEngineProp->setValue($skipper, $engine);

                $filterProp = $reflection->getProperty('filter');
                $filterProp->setAccessible(true);
                $filter = $filterProp->getValue($skipper);

                $filterReflection = new ReflectionClass($filter);
                $filterEngineProp = $filterReflection->getProperty('ruleEngine');
                $filterEngineProp->setAccessible(true);
                $filterEngineProp->setValue($filter, $engine);

                $result = $skipper->processWithSafeguard($mediaUrl);
                $stats = $result['stats'] ?? [];
                $safeguardTriggered = !empty($result['safeguardTriggered']);
                $safeguardReason = $result['safeguardReason'] ?? '';
                $safeguardMethod = $result['safeguardMethod'] ?? '';

                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host . $basePath;
                $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($mediaUrl);

                if ($safeguardTriggered && $safeguardMethod === 'none') {
                    $fallbackToFull = true;
                } else {
                    $fallbackToFull = false;
                }

                $fastModeMessage = '检测到已有域名规则，使用规则快速去广告';
                if ($safeguardTriggered) {
                    if ($safeguardMethod === 'smart_filter') {
                        $fastModeMessage = '规则过于激进，已自动切换智能过滤模式';
                    } elseif ($safeguardMethod === 'threshold_adjustment') {
                        $fastModeMessage = '规则过于激进，已自动调整检测阈值';
                    } elseif ($safeguardMethod === 'none') {
                        $fastModeMessage = '规则不匹配当前视频，将使用完整分析模式';
                    }
                }

                if (!$fallbackToFull) {
                    // 保存到数据库缓存
                    $cacheResult = [
                        'duration_rules' => $domainRules['duration_rules'] ?? [],
                        'discontinuity_rules' => $domainRules['discontinuity_rules'] ?? [],
                        'sequence_jump_rules' => $domainRules['sequence_jump_rules'] ?? [],
                        'filename_patterns' => $domainRules['filename_patterns'] ?? [],
                        'stats' => $stats
                    ];
                    if ($analysisCache) {
                        try {
                            $analysisCache->save($url, $domain, $mediaUrl, $cacheResult, true, $safeguardTriggered);
                        } catch (Throwable $e) {
                            error_log('保存分析缓存失败（不影响分析结果）: ' . $e->getMessage());
                        }
                    }
                    if ($domainStats) {
                        try {
                            $domainStats->recordAnalyze($domain, $stats['totalSegments'] ?? 0, $stats['removedSegments'] ?? $stats['adSegments'] ?? 0, $stats['adPercentage'] ?? 0);
                        } catch (Throwable $e) {
                            error_log('记录分析统计失败（不影响分析结果）: ' . $e->getMessage());
                        }
                    }

                    sendJsonResponse([
                        'success' => true,
                        'url' => $url,
                        'mediaUrl' => $mediaUrl,
                        'domain' => $domain,
                        'hasDomainRules' => true,
                        'fastMode' => true,
                        'safeguardTriggered' => $safeguardTriggered,
                        'safeguardReason' => $safeguardReason,
                        'safeguardMethod' => $safeguardMethod,
                        'learn_count' => $domainRules['learn_count'] ?? 0,
                        'message' => $fastModeMessage,
                        'mxjxUrl' => $mxjxUrl,
                        'playlist' => [
                            'isMaster' => false,
                            'version' => $result['original']['version'] ?? 3,
                            'targetDuration' => $result['original']['targetDuration'] ?? 0,
                            'endlist' => !empty($result['original']['endlist'])
                        ],
                        'stats' => [
                            'totalSegments' => $stats['totalSegments'] ?? 0,
                            'adSegments' => $stats['adSegments'] ?? $stats['removedSegments'] ?? 0,
                            'keptSegments' => $stats['keptSegments'] ?? 0,
                            'originalDuration' => (float)($stats['originalDuration'] ?? 0),
                            'filteredDuration' => (float)($stats['filteredDuration'] ?? 0),
                            'savedDuration' => (float)($stats['savedDuration'] ?? 0),
                            'adPercentage' => (float)($stats['adPercentage'] ?? 0)
                        ],
                        'domainRules' => $domainRules
                    ]);
                    break;
                }
            }

            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $engine->setDomain($domain);

            $parser = new M3U8Parser();
            $parser->setMaxSegments(5000);
            $playlist = $parser->parse($mediaUrl);
            unset($parser);

            if (empty($playlist['segments'])) {
                unset($playlist);
                unset($engine);
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            unset($engine);

            $autoLearn = isset($_GET['auto_learn']) ? $_GET['auto_learn'] === '1' || $_GET['auto_learn'] === 'true' : true;
            $learnResult = null;
            if ($autoLearn && $analysis['adCount'] > 0) {
                $learnResult = $ruleManager->learnFromAnalysis($domain, $analysis);
            }

            $currentRules = $ruleManager->getRules($domain);

            // ===== 提取广告特征码并保存到数据库 =====
            $signatures = [];
            if (!empty($analysis['durationDistribution']) && is_array($analysis['durationDistribution'])) {
                $dist = $analysis['durationDistribution'];
                if (isset($dist['buckets']) && is_array($dist['buckets'])) {
                    foreach ($dist['buckets'] as $dur => $count) {
                        $countVal = is_array($count) ? ($count['count'] ?? 0) : (int)$count;
                        $durVal = is_array($count) ? ($count['duration'] ?? $dur) : $dur;
                        if ((float)$durVal < 3.0 && $countVal > 1) {
                            $signatures[] = ['type' => 'duration', 'value' => (string)$durVal, 'weight' => min(50, $countVal * 5), 'confidence' => min(80, $countVal * 10)];
                        }
                    }
                }
            }
            if (!empty($analysis['adClusters'])) {
                foreach ($analysis['adClusters'] as $cluster) {
                    if (!empty($cluster['avgDuration']) && $cluster['avgDuration'] < 3.0) {
                        $signatures[] = ['type' => 'duration', 'value' => (string)round($cluster['avgDuration'], 2), 'weight' => 40, 'confidence' => 60];
                    }
                }
            }
            if (!empty($analysis['sequenceJumps'])) {
                foreach ($analysis['sequenceJumps'] as $jump) {
                    if (!empty($jump['jump']) && $jump['jump'] > 1) {
                        $signatures[] = ['type' => 'sequence', 'value' => (string)$jump['jump'], 'weight' => 35, 'confidence' => 50];
                    }
                }
            }
            if ($analysis['discontinuityCount'] > 0) {
                $signatures[] = ['type' => 'discontinuity', 'value' => 'true', 'weight' => 30, 'confidence' => 50];
            }
            if ($adSignature && !empty($signatures)) {
                try {
                    $adSignature->addSignatures($domain, $signatures);
                } catch (Throwable $e) {
                    error_log('保存广告特征码失败（不影响分析结果）: ' . $e->getMessage());
                }
            }

            // ===== 保存分析结果到数据库缓存 =====
            $cacheResult = [
                'duration_rules' => $currentRules['duration_rules'] ?? [],
                'discontinuity_rules' => $currentRules['discontinuity_rules'] ?? [],
                'sequence_jump_rules' => $currentRules['sequence_jump_rules'] ?? [],
                'filename_patterns' => $currentRules['filename_patterns'] ?? [],
                'ad_signatures' => $signatures,
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'keptSegments' => $analysis['contentCount'],
                    'originalDuration' => $analysis['totalDuration'] ?? 0,
                    'filteredDuration' => $analysis['contentDuration'] ?? 0,
                    'savedDuration' => $analysis['adDuration'] ?? 0,
                    'adPercentage' => $analysis['totalCount'] > 0 ? round($analysis['adCount'] / $analysis['totalCount'] * 100, 2) : 0
                ]
            ];
            if ($analysisCache) {
                try {
                    $analysisCache->save($url, $domain, $mediaUrl, $cacheResult, false, false);
                } catch (Throwable $e) {
                    error_log('保存分析缓存失败（不影响分析结果）: ' . $e->getMessage());
                }
            }
            if ($domainStats) {
                try {
                    $domainStats->recordAnalyze($domain, $analysis['totalCount'], $analysis['adCount'], $analysis['totalCount'] > 0 ? round($analysis['adCount'] / $analysis['totalCount'] * 100, 2) : 0);
                } catch (Throwable $e) {
                    error_log('记录分析统计失败（不影响分析结果）: ' . $e->getMessage());
                }
            }

            $playlistInfo = [
                'isMaster' => !empty($playlist['isMaster']),
                'version' => $playlist['version'] ?? 3,
                'targetDuration' => $playlist['targetDuration'] ?? 0,
                'endlist' => !empty($playlist['endlist']),
                'variantCount' => count($playlist['variants'] ?? [])
            ];
            unset($playlist);

            $adSegments = array_values(array_filter($analysis['segments'], function($r) {
                return $r['isAd'];
            }));

            $allSegmentsSummary = [];
            $maxSegments = 500;
            $segmentCount = 0;
            foreach ($analysis['segments'] as $idx => $seg) {
                if ($segmentCount >= $maxSegments) break;
                $allSegmentsSummary[] = [
                    'i' => $idx,
                    'd' => $seg['duration'],
                    'a' => !empty($seg['isAd']) ? 1 : 0
                ];
                $segmentCount++;
            }

            sendJsonResponse([
                'success' => true,
                'url' => $url,
                'mediaUrl' => $mediaUrl,
                'domain' => $domain,
                'hasDomainRules' => $currentRules !== null,
                'fastMode' => false,
                'autoLearned' => $learnResult,
                'learn_count' => $currentRules['learn_count'] ?? 0,
                'playlist' => $playlistInfo,
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'keptSegments' => $analysis['contentCount'],
                    'originalDuration' => (float)($analysis['totalDuration'] ?? 0),
                    'filteredDuration' => (float)($analysis['contentDuration'] ?? 0),
                    'savedDuration' => (float)($analysis['adDuration'] ?? 0),
                    'adPercentage' => $analysis['totalCount'] > 0 ? round($analysis['adCount'] / $analysis['totalCount'] * 100, 2) : 0,
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumpCount' => count($analysis['sequenceJumps']),
                    'adClusterCount' => count($analysis['adClusters'])
                ],
                'durationDistribution' => $analysis['durationDistribution'],
                'sequenceJumps' => array_slice($analysis['sequenceJumps'], 0, 20),
                'adClusters' => $analysis['adClusters'],
                'adSegments' => array_slice($adSegments, 0, 50),
                'allSegments' => $allSegmentsSummary,
                'segmentLimit' => $maxSegments,
                'hasMoreSegments' => $analysis['totalCount'] > $maxSegments
            ]);
            unset($analysis);
            break;

        case 'rules/list':
            $rules = $ruleManager->getAllRulesLite();
            sendJsonResponse(['success' => true, 'rules' => $rules]);
            break;

        case 'rules/get':
            $domain = $_GET['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $rules = $ruleManager->getRules($domain);
            if ($rules === null) {
                sendJsonResponse(['success' => false, 'message' => '规则不存在'], 404);
            }
            sendJsonResponse(['success' => true, 'domain' => $domain, 'rules' => $rules]);
            break;

        case 'rules/save':
            $input = getInputJson();
            $domain = $input['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $ruleData = $input['rules'] ?? [];
            $result = $ruleManager->saveRules($domain, $ruleData);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则保存成功' : '规则保存失败',
                'domain' => $domain
            ]);
            break;

        case 'rules/delete':
            $input = getInputJson();
            $domain = $input['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $result = $ruleManager->deleteRules($domain);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则删除成功' : '规则删除失败或不存在'
            ]);
            break;

        case 'rules/generate':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $parser = new M3U8Parser();
            $playlist = $parser->parse($mediaUrl);

            if (empty($playlist['segments'])) {
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            $generatedRules = $ruleManager->createFromAnalysis($domain, $analysis);
            $generatedRules['sample_url'] = $url;

            $discontinuityRegexRules = $ruleManager->generateDiscontinuityRegexRules($analysis, $playlist['segments']);
            $adClusterDetails = $ruleManager->analyzeAdClustersDetail($analysis, $playlist['segments']);

            $ruleCount = count($generatedRules['duration_rules'] ?? [])
                + count($generatedRules['discontinuity_rules'] ?? [])
                + count($generatedRules['sequence_jump_rules'] ?? [])
                + count($generatedRules['filename_patterns'] ?? [])
                + count($discontinuityRegexRules);

            sendJsonResponse([
                'success' => true,
                'domain' => $domain,
                'rules' => $generatedRules,
                'ruleCount' => $ruleCount,
                'discontinuity_regex_rules' => $discontinuityRegexRules,
                'ad_clusters' => $adClusterDetails,
                'analysis' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumpCount' => count($analysis['sequenceJumps']),
                    'adClusterCount' => count($analysis['adClusters'])
                ]
            ]);
            break;

        case 'rules/learn':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);

            $engine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $parser = new M3U8Parser();
            $playlist = $parser->parse($mediaUrl);

            if (empty($playlist['segments'])) {
                sendJsonResponse(['success' => false, 'message' => '无法解析视频片段'], 400);
            }

            $analysis = $engine->analyzeAllSegments($playlist['segments']);
            $result = $ruleManager->learnFromAnalysis($domain, $analysis);
            $rules = $ruleManager->getRules($domain);

            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '规则学习完成' : '规则学习失败',
                'domain' => $domain,
                'learn_count' => $rules['learn_count'] ?? 0,
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumps' => count($analysis['sequenceJumps']),
                    'adClusters' => count($analysis['adClusters'])
                ]
            ]);
            break;

        case 'rules/export':
            $domain = $_GET['domain'] ?? '';
            $exportData = $ruleManager->exportRules($domain ?: null);
            if ($exportData === null) {
                sendJsonResponse(['success' => false, 'message' => '规则不存在'], 404);
            }
            if (!empty($_GET['download'])) {
                $filename = $domain ? "rules_{$domain}.json" : 'all_rules.json';
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Type: application/json; charset=utf-8');
                ob_clean();
                echo json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }
            sendJsonResponse($exportData);
            break;

        case 'rules/import':
            $input = getInputJson();
            if (empty($input)) {
                sendJsonResponse(['success' => false, 'message' => '缺少导入数据'], 400);
            }
            $result = $ruleManager->importRules($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'rules/clear':
            $count = $ruleManager->clearAllRules();
            sendJsonResponse([
                'success' => true,
                'message' => '已清理 ' . $count . ' 条规则',
                'cleared_count' => $count
            ]);
            break;

        case 'skip':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);
            $ruleEngineProp->setValue($skipper, $enhancedEngine);

            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);

            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->process($url);

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);

            sendJsonResponse([
                'success' => true,
                'url' => $url,
                'mxjx' => $mxjxUrl,
                'stats' => $result['stats']
            ]);
            break;

        case 'mxjx':
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            $proxy = $_GET['proxy'] ?? '';
            if (empty($url)) {
                header('Content-Type: application/json; charset=utf-8');
                sendJsonResponse(['success' => false, 'code' => 400, 'message' => '缺少 url 参数'], 400);
            }

            try {
                $cacheManager = new CacheManager($rootDir . '/cache');
                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
                
                // 添加时间戳避免相同URL缓存问题
                $timestamp = $_GET['_t'] ?? '';
                $cacheKey = 'mxjx_' . md5($url . '_' . $domain . '_' . $timestamp . '_' . $proxy);
                $cachedContent = $cacheManager->get($cacheKey);

                if ($cachedContent !== null && is_string($cachedContent) && empty($timestamp)) {
                    header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                    header('Content-Disposition: inline; filename="playlist.m3u8"');
                    header('X-Cache: HIT');
                    if ($proxy) {
                        header('X-Proxy: ' . $proxy);
                    }
                    ob_clean();
                    echo $cachedContent;
                    exit;
                }

                $mediaUrl = resolveMasterPlaylist($url, $proxy);
                if ($mediaUrl !== $url) {
                    $parsedUrl = parse_url($mediaUrl);
                    $domain = $parsedUrl['host'] ?? '';
                }
                $url = $mediaUrl;

                $skipper = new M3U8AdSkipper();
                if ($proxy) {
                    $skipper->getParser()->setForceProxy($proxy);
                }
                $reflection = new ReflectionClass($skipper);
                $ruleEngineProp = $reflection->getProperty('ruleEngine');
                $ruleEngineProp->setAccessible(true);

                $enhancedEngine = new EnhancedAdRuleEngine([
                    'checkDiscontinuity' => true,
                    'checkRepetitiveDuration' => true
                ]);
                $enhancedEngine->setDomain($domain);

                // 从数据库加载域名规则和广告特征码
                if ($useDb) {
                    $dbRules = $ruleManager->getRules($domain);
                    if (!empty($dbRules)) {
                        $engineReflection = new ReflectionClass($enhancedEngine);
                        $applyMethod = $engineReflection->getMethod('applyDomainRules');
                        $applyMethod->setAccessible(true);
                        $applyMethod->invoke($enhancedEngine, $dbRules);
                    }

                    $adSignature = new DbAdSignature();
                    $sigRules = $adSignature->getRulesForDomain($domain);
                    if (!empty($sigRules)) {
                        $engineReflection = new ReflectionClass($enhancedEngine);
                        $applyMethod = $engineReflection->getMethod('applyDomainRules');
                        $applyMethod->setAccessible(true);
                        $applyMethod->invoke($enhancedEngine, $sigRules);
                    }
                }

                $ruleEngineProp->setValue($skipper, $enhancedEngine);

                $filterProp = $reflection->getProperty('filter');
                $filterProp->setAccessible(true);
                $filter = $filterProp->getValue($skipper);

                $filterReflection = new ReflectionClass($filter);
                $filterEngineProp = $filterReflection->getProperty('ruleEngine');
                $filterEngineProp->setAccessible(true);
                $filterEngineProp->setValue($filter, $enhancedEngine);

                $result = $skipper->processWithSafeguard($url);
                $safeguardTriggered = !empty($result['safeguardTriggered']);
                $safeguardReason = $result['safeguardReason'] ?? '';
                $safeguardMethod = $result['safeguardMethod'] ?? '';

                if (!$result['success'] && empty($result['output'])) {
                    header('Content-Type: application/json; charset=utf-8');
                    sendJsonResponse([
                        'success' => false,
                        'code' => 500,
                        'message' => 'M3U8 解析失败',
                        'error' => $result['error'] ?? '未知错误'
                    ], 500);
                }

                $newM3U8Content = $result['output'];

                $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;

                if ($isRemote) {
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (isset($parsedUrl['port'])) {
                        $baseUrl .= ':' . $parsedUrl['port'];
                    }
                    $pathDir = dirname($parsedUrl['path'] ?? '');
                    $pathDir = $pathDir === '.' ? '' : $pathDir;

                    $lines = explode("\n", $newM3U8Content);
                    $newLines = [];
                    foreach ($lines as $line) {
                        if (!empty(trim($line)) &&
                            strpos($line, '#') !== 0 &&
                            strpos($line, 'http://') !== 0 &&
                            strpos($line, 'https://') !== 0) {
                            if ($pathDir === '' || $pathDir === '/') {
                                $line = $baseUrl . '/' . ltrim($line, '/');
                            } else {
                                $line = $baseUrl . $pathDir . '/' . ltrim($line, '/');
                            }
                        }
                        $newLines[] = $line;
                    }
                    $newM3U8Content = implode("\n", $newLines);
                }

                // 仅在无时间戳参数时缓存
                if (empty($timestamp)) {
                    $cacheManager->set($cacheKey, $newM3U8Content, 120);
                }

                header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                header('Content-Disposition: inline; filename="playlist.m3u8"');
                header('X-Cache: MISS');
                header('X-Request-Time: ' . time());
                if ($safeguardTriggered) {
                    header('X-Safeguard: triggered');
                    header('X-Safeguard-Reason: ' . rawurlencode($safeguardReason));
                    header('X-Safeguard-Method: ' . $safeguardMethod);
                } else {
                    header('X-Safeguard: not_triggered');
                }
                ob_clean();
                echo $newM3U8Content;
                exit;

            } catch (Exception $e) {
                header('Content-Type: application/json; charset=utf-8');
                sendJsonResponse([
                    'success' => false,
                    'code' => 500,
                    'message' => '处理失败',
                    'error' => $e->getMessage()
                ], 500);
            }
            break;

        case 'mxjx/info':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse([
                    'code' => 400,
                    'success' => false,
                    'message' => '缺少 url 参数'
                ], 400);
            }
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';
            $mediaUrl = resolveMasterPlaylist($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
            }
            $url = $mediaUrl;

            $skipper = new M3U8AdSkipper();
            $reflection = new ReflectionClass($skipper);
            $ruleEngineProp = $reflection->getProperty('ruleEngine');
            $ruleEngineProp->setAccessible(true);

            $enhancedEngine = new EnhancedAdRuleEngine([
                'checkDiscontinuity' => true,
                'checkRepetitiveDuration' => true
            ]);
            $enhancedEngine->setDomain($domain);
            $ruleEngineProp->setValue($skipper, $enhancedEngine);

            $filterProp = $reflection->getProperty('filter');
            $filterProp->setAccessible(true);
            $filter = $filterProp->getValue($skipper);

            $filterReflection = new ReflectionClass($filter);
            $filterEngineProp = $filterReflection->getProperty('ruleEngine');
            $filterEngineProp->setAccessible(true);
            $filterEngineProp->setValue($filter, $enhancedEngine);

            $result = $skipper->process($url);

            $stats = $result['stats'] ?? [];
            $adPercentage = $stats['adPercentage'] ?? 0;
            if ($adPercentage >= 95 && $stats['totalSegments'] > 10) {
                $newM3U8Content = file_get_contents($url);
                if ($newM3U8Content === false) {
                    $newM3U8Content = $result['output'];
                }
            } else {
                $newM3U8Content = $result['output'];
            }

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;

            if ($isRemote) {
                $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                if (isset($parsedUrl['port'])) {
                    $baseUrl .= ':' . $parsedUrl['port'];
                }
                $pathDir = dirname($parsedUrl['path'] ?? '');
                $pathDir = $pathDir === '.' ? '' : $pathDir;

                $lines = explode("\n", $newM3U8Content);
                $newLines = [];
                foreach ($lines as $line) {
                    if (!empty(trim($line)) &&
                        strpos($line, '#') !== 0 &&
                        strpos($line, 'http://') !== 0 &&
                        strpos($line, 'https://') !== 0) {
                        if ($pathDir === '' || $pathDir === '/') {
                            $line = $baseUrl . '/' . ltrim($line, '/');
                        } else {
                            $line = $baseUrl . $pathDir . '/' . ltrim($line, '/');
                        }
                    }
                    $newLines[] = $line;
                }
                $newM3U8Content = implode("\n", $newLines);
            }

            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? '';
            $selfPath = dirname(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '');
            $selfPath = $selfPath === '.' ? '' : $selfPath;
            $selfBase = $protocol . '://' . $host . $selfPath;
            $playUrl = $selfBase . '/mx.php?action=mxjx&url=' . urlencode($mediaUrl);

            $stats = $result['stats'] ?? [];
            $hasRules = $enhancedEngine->getCurrentDomainRules() !== null;

            sendJsonResponse([
                'code' => 200,
                'success' => true,
                'message' => '解析成功',
                'data' => [
                    'original_url' => $_GET['url'] ?? '',
                    'media_url' => $mediaUrl,
                    'domain' => $domain,
                    'play_url' => $playUrl,
                    'has_domain_rules' => $hasRules,
                    'stats' => [
                        'total_segments' => $stats['totalSegments'] ?? 0,
                        'kept_segments' => $stats['keptSegments'] ?? 0,
                        'removed_segments' => $stats['removedSegments'] ?? 0,
                        'original_duration' => $stats['originalDuration'] ?? 0,
                        'filtered_duration' => $stats['filteredDuration'] ?? 0,
                        'saved_duration' => $stats['savedDuration'] ?? 0,
                        'ad_percentage' => $stats['adPercentage'] ?? 0
                    ]
                ]
            ]);
            break;

        case 'xiami_jx':
        case 'xiami_jx/info':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse([
                    'code' => 400,
                    'success' => false,
                    'message' => '缺少 url 参数'
                ], 400);
            }

            $apiEndpoints = [
                'https://cache.0567890.xyz:4433/Api',
                'https://cache.hls.one/Api',
            ];

            $tm = intval(round(microtime(true) * 1000));
            $keyHex = md5($tm . $url);

            $aesKeyHex = md5($keyHex);
            $iv = 'fUU9eRmkYzsgbkEK';
            $plaintext = $keyHex;
            $blockSize = 16;
            $padLen = $blockSize - (strlen($plaintext) % $blockSize);
            if ($padLen == $blockSize) $padLen = 0;
            $padded = $plaintext . str_repeat("\x00", $padLen);
            $sign = @openssl_encrypt(
                $padded,
                'aes-256-cbc',
                $aesKeyHex,
                OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                $iv
            );
            if ($sign !== false) {
                $sign = base64_encode($sign);
            }

            $playUrl = '';
            $lastError = '';
            $videoType = '';

            if (!empty($sign)) {
                $postData = [
                    'tm'   => $tm,
                    'url'  => $url,
                    'key'  => $keyHex,
                    'sign' => $sign,
                ];

                foreach ($apiEndpoints as $api) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL            => $api,
                        CURLOPT_POST           => true,
                        CURLOPT_POSTFIELDS     => http_build_query($postData),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT        => 25,
                        CURLOPT_CONNECTTIMEOUT => 10,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_HTTPHEADER     => [
                            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                            'Accept: application/json, text/javascript, */*; q=0.01',
                            'Origin: https://jx.xmflv.cc',
                            'Referer: https://jx.xmflv.cc/',
                            'X-Requested-With: XMLHttpRequest',
                        ],
                    ]);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $curlError = curl_error($ch);
                    curl_close($ch);

                    if ($response === false || $httpCode !== 200) {
                        $lastError = $curlError ?: "HTTP $httpCode";
                        continue;
                    }

                    $body = str_replace('tg:@xmflv', '', $response);
                    $json = json_decode($body, true);

                    if ($json === null || !isset($json['code'])) {
                        $lastError = '响应解析失败';
                        continue;
                    }

                    if ($json['code'] !== 200) {
                        $lastError = isset($json['msg']) ? $json['msg'] : '解析失败';
                        continue;
                    }

                    if (empty($json['data']) || empty($json['key']) || empty($json['iv'])) {
                        $lastError = '响应缺少 data/key/iv 字段';
                        continue;
                    }

                    $ciphertext = base64_decode($json['data'], true);
                    $decKey = $json['key'];
                    $decIv = $json['iv'];

                    if ($ciphertext === false || strlen($ciphertext) === 0) {
                        $lastError = '解密数据无效';
                        continue;
                    }

                    $keyLen = strlen($decKey);
                    if ($keyLen <= 16) {
                        $method = 'aes-128-cbc';
                    } elseif ($keyLen <= 24) {
                        $method = 'aes-192-cbc';
                    } else {
                        $method = 'aes-256-cbc';
                    }

                    if ($keyLen < 16) {
                        $lastError = '密钥长度不足';
                        continue;
                    }

                    $decrypted = @openssl_decrypt(
                        $ciphertext,
                        $method,
                        $decKey,
                        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                        $decIv
                    );

                    if ($decrypted !== false && strlen($decrypted) > 0) {
                        $decrypted = rtrim($decrypted, "\x00");
                        $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                        $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
                    } else {
                        $decrypted = @openssl_decrypt(
                            $ciphertext,
                            $method,
                            $decKey,
                            OPENSSL_RAW_DATA,
                            $decIv
                        );
                        if ($decrypted !== false) {
                            $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                            $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
                        }
                    }

                    if ($decrypted === false || strlen($decrypted) === 0) {
                        $lastError = '解密失败';
                        continue;
                    }

                    $resultData = json_decode($decrypted, true);
                    if ($resultData === null) {
                        $lastError = '解密数据解析失败';
                        continue;
                    }

                    $playUrl = isset($resultData['vurl']) ? $resultData['vurl'] : (isset($resultData['url']) ? $resultData['url'] : '');
                    $videoType = isset($resultData['type']) ? $resultData['type'] : '';
                    break;
                }
            }

            if (empty($playUrl)) {
                sendJsonResponse([
                    'code' => 500,
                    'success' => false,
                    'message' => $lastError ?: '未获取到资源',
                    'data' => null
                ], 500);
            }

            $label = '';
            if (strpos($videoType, 'm3u8') !== false || strpos($videoType, 'hls') !== false) {
                $label = 'HLS';
            } elseif (strpos($videoType, 'mp4') !== false) {
                $label = 'MP4';
            }

            sendJsonResponse([
                'code' => 200,
                'success' => true,
                'message' => '解析成功',
                'data' => [
                    'original_url' => $url,
                    'media_url' => $playUrl,
                    'type' => $videoType,
                    'label' => $label,
                    'source' => 'xiami'
                ]
            ]);
            break;

        case 'update/version':
            $versionFile = '';
            if (file_exists(__DIR__ . '/version.php')) {
                $vData = include __DIR__ . '/version.php';
                if (is_array($vData)) {
                    $versionFile = $vData['version'] ?? json_encode($vData, JSON_UNESCAPED_UNICODE);
                } else {
                    $versionFile = trim((string)$vData);
                }
            }
            sendJsonResponse([
                'success' => true,
                'current_version' => $updateManager->getCurrentVersion(),
                'current_commit' => $updateManager->getCurrentCommit(),
                'version_file' => $versionFile
            ]);
            break;

        case 'update/check':
            $result = $updateManager->checkUpdate();
            sendJsonResponse($result);
            break;

        case 'update/integrity':
            $result = $updateManager->checkIntegrity();
            sendJsonResponse($result);
            break;

        case 'update/backup/list':
            $backups = $updateManager->getBackupList();
            sendJsonResponse(['success' => true, 'backups' => $backups]);
            break;

        case 'update/backup/create':
            $result = $updateManager->createBackup();
            sendJsonResponse($result);
            break;

        case 'update/backup/restore':
            $input = getInputJson();
            $filename = $input['filename'] ?? $_GET['filename'] ?? '';
            if (empty($filename)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 filename 参数'], 400);
            }
            $result = $updateManager->restoreBackup($filename);
            sendJsonResponse($result);
            break;

        case 'update/backup/delete':
            $input = getInputJson();
            $filename = $input['filename'] ?? $_GET['filename'] ?? '';
            if (empty($filename)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 filename 参数'], 400);
            }
            $result = $updateManager->deleteBackup($filename);
            sendJsonResponse($result);
            break;

        case 'update/download':
            $result = $updateManager->downloadUpdate();
            sendJsonResponse($result);
            break;

        case 'update/clear_cache':
            $result = $updateManager->clearAllCaches();
            sendJsonResponse([
                'success' => true,
                'message' => '缓存清理成功',
                'cache_info' => $result
            ]);
            break;

        case 'update/system_info':
            sendJsonResponse($updateManager->getSystemInfo());
            break;

        case 'auth/info':
            $info = $authValidator->getAuthInfo();
            $info['success'] = true;
            $info['contact'] = 'QQ2094332348';
            sendJsonResponse($info);
            break;

        case 'auth/validate':
            $localValid = $authValidator->validateLocal();
            $remoteValid = $authValidator->validateRemote();
            sendJsonResponse([
                'success' => true,
                'local_valid' => $localValid,
                'remote_valid' => $remoteValid,
                'all_valid' => $localValid && $remoteValid,
                'error' => $authValidator->getLastError()
            ]);
            break;

        case 'auth/config/get':
            $config = $authValidator->getAuthConfig()->getConfig();
            sendJsonResponse(['success' => true, 'config' => $config]);
            break;

        case 'auth/config/save':
            $input = getInputJson();
            $config = $input['config'] ?? [];
            $result = $authValidator->getAuthConfig()->setConfig($config);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '配置保存成功' : '配置保存失败'
            ]);
            break;

        case 'auth/set':
            $input = getInputJson();
            $authCode = $input['auth_code'] ?? '';
            if (empty($authCode)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 auth_code 参数'], 400);
            }
            $result = $authValidator->setAuthCode($authCode);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '授权码设置成功' : '授权码设置失败'
            ]);
            break;

        case 'auth/generate':
            $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            $authCode = $authValidator->generateAuthCode($domain);
            sendJsonResponse([
                'success' => true,
                'domain' => $domain,
                'auth_code' => $authCode
            ]);
            break;

        case 'sites/list':
            $includePaused = isset($_GET['include_paused']) && $_GET['include_paused'] === '1';
            $sites = $siteManager->getAllSites($includePaused);
            $config = $siteManager->getAutoLearnConfig();
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            sendJsonResponse([
                'success' => true,
                'sites' => $sites,
                'total' => count($sites),
                'auto_learn_config' => $config,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn
            ]);
            break;

        case 'sites/get':
            $name = $_GET['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $site = $siteManager->getSiteByName($name);
            if ($site === null) {
                sendJsonResponse(['success' => false, 'message' => '资源站不存在'], 404);
            }
            sendJsonResponse(['success' => true, 'site' => $site]);
            break;

        case 'sites/add':
            $input = getInputJson();
            $result = $siteManager->addSite($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/update':
            $input = getInputJson();
            $name = $input['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->updateSite($name, $input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/delete':
            $input = getInputJson();
            $name = $input['name'] ?? $_GET['name'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->deleteSite($name);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/health_check':
            $maxSites = isset($_GET['max']) ? intval($_GET['max']) : null;
            $result = $siteManager->batchCheckHealth($maxSites);
            sendJsonResponse(['success' => true] + $result);
            break;

        case 'sites/update_status':
            $input = getInputJson();
            $name = $input['name'] ?? '';
            $status = $input['status'] ?? 'active';
            $note = $input['note'] ?? '';
            if (empty($name)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 name 参数'], 400);
            }
            $result = $siteManager->updateSiteStatus($name, $status, $note);
            sendJsonResponse(['success' => $result, 'message' => $result ? '更新成功' : '更新失败']);
            break;

        case 'sites/fetch_videos':
            $name = $_GET['name'] ?? '';
            $apiUrl = $_GET['api_url'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);

            if (!empty($name)) {
                $site = $siteManager->getSiteByName($name);
                if ($site) {
                    $apiUrl = $site['api_url'];
                }
            }

            if (empty($apiUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请指定资源站名称或采集接口地址'], 400);
            }

            $result = $siteManager->fetchVideos($apiUrl, $page, $limit);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/search':
            $name = $_GET['name'] ?? '';
            $apiUrl = $_GET['api_url'] ?? '';
            $keyword = $_GET['keyword'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);

            if (empty($keyword)) {
                sendJsonResponse(['success' => false, 'message' => '请输入搜索关键词'], 400);
            }

            if (!empty($name)) {
                $site = $siteManager->getSiteByName($name);
                if ($site) {
                    $apiUrl = $site['api_url'];
                }
            }

            if (empty($apiUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请指定资源站名称或采集接口地址'], 400);
            }

            $result = $siteManager->searchVideos($apiUrl, $keyword, $page, $limit);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/search_all':
            $keyword = $_GET['keyword'] ?? '';
            $maxSites = intval($_GET['max_sites'] ?? 5);
            $limitPerSite = intval($_GET['limit_per_site'] ?? 10);

            if (empty($keyword)) {
                sendJsonResponse(['success' => false, 'message' => '请输入搜索关键词'], 400);
            }

            $result = $siteManager->searchAllSites($keyword, $maxSites, $limitPerSite);
            sendJsonResponse($result, 200);
            break;

        case 'sites/learn_video':
            $input = getInputJson();
            $videoUrl = $input['url'] ?? $_GET['url'] ?? '';

            if (empty($videoUrl)) {
                sendJsonResponse(['success' => false, 'message' => '请提供视频URL'], 400);
            }

            $minSegments = isset($input['min_segments']) ? intval($input['min_segments']) : null;
            $maxAdPercentage = isset($input['max_ad_percentage']) ? intval($input['max_ad_percentage']) : null;

            $options = [];
            if ($minSegments !== null) $options['min_segments'] = $minSegments;
            if ($maxAdPercentage !== null) $options['max_ad_percentage'] = $maxAdPercentage;

            $result = $siteManager->learnFromVideoUrl($videoUrl, $ruleManager, $options);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/search_and_learn':
            $input = getInputJson();
            $keyword = $input['keyword'] ?? $_GET['keyword'] ?? '';
            $siteName = $input['site_name'] ?? $_GET['site_name'] ?? 'all';
            $maxSites = isset($input['max_sites']) ? intval($input['max_sites']) : (isset($_GET['max_sites']) ? intval($_GET['max_sites']) : 5);
            $limitPerSite = isset($input['limit_per_site']) ? intval($input['limit_per_site']) : (isset($_GET['limit_per_site']) ? intval($_GET['limit_per_site']) : 10);
            $useMultiThread = !empty($input['multi_thread']) || (isset($_GET['multi_thread']) && $_GET['multi_thread'] === '1');
            $concurrency = isset($input['concurrency']) ? intval($input['concurrency']) : (isset($_GET['concurrency']) ? intval($_GET['concurrency']) : 5);
            $minSegments = isset($input['min_segments']) ? intval($input['min_segments']) : null;
            $maxAdPercentage = isset($input['max_ad_percentage']) ? intval($input['max_ad_percentage']) : null;

            if (empty($keyword)) {
                sendJsonResponse(['success' => false, 'message' => '请输入搜索关键词'], 400);
            }

            $concurrency = max(1, min(10, $concurrency));
            $startTime = microtime(true);

            if ($siteName === 'all') {
                $searchResult = $siteManager->searchAllSites($keyword, $maxSites, $limitPerSite);
            } else {
                $site = $siteManager->getSiteByName($siteName);
                if (!$site) {
                    sendJsonResponse(['success' => false, 'message' => '资源站不存在'], 400);
                }
                $searchResult = $siteManager->searchVideos($site['api_url'], $keyword, 1, $limitPerSite * 3);
                if ($searchResult['success']) {
                    $videos = array_slice($searchResult['videos'] ?? [], 0, $limitPerSite);
                    foreach ($videos as &$v) {
                        $v['site_name'] = $site['name'];
                    }
                    unset($v);
                    $searchResult = [
                        'success' => true,
                        'keyword' => $keyword,
                        'sites_searched' => 1,
                        'total_videos' => count($videos),
                        'results' => [
                            [
                                'site' => $site['name'],
                                'site_url' => $site['site_url'] ?? '',
                                'count' => count($videos),
                                'videos' => $videos
                            ]
                        ]
                    ];
                } else {
                    $searchResult = [
                        'success' => true,
                        'keyword' => $keyword,
                        'sites_searched' => 1,
                        'total_videos' => 0,
                        'results' => [
                            [
                                'site' => $site['name'],
                                'site_url' => $site['site_url'] ?? '',
                                'count' => 0,
                                'videos' => [],
                                'error' => $searchResult['message']
                            ]
                        ]
                    ];
                }
            }

            $allVideos = [];
            $siteVideoMap = [];
            foreach ($searchResult['results'] ?? [] as $siteResult) {
                foreach ($siteResult['videos'] ?? [] as $video) {
                    $videoUrl = $video['url'] ?? $video['first_url'] ?? '';
                    if (!empty($videoUrl)) {
                        $allVideos[] = [
                            'url' => $videoUrl,
                            'site' => $siteResult['site'] ?? '',
                            'name' => $video['name'] ?? '未知'
                        ];
                        if (!isset($siteVideoMap[$siteResult['site']])) {
                            $siteVideoMap[$siteResult['site']] = 0;
                        }
                        $siteVideoMap[$siteResult['site']]++;
                    }
                }
            }

            $totalVideos = count($allVideos);

            if ($totalVideos === 0) {
                sendJsonResponse([
                    'success' => true,
                    'message' => '未找到可学习的视频',
                    'keyword' => $keyword,
                    'sites_searched' => $searchResult['sites_searched'] ?? 0,
                    'total_found' => 0,
                    'total_learned' => 0,
                    'total_failed' => 0,
                    'total_time' => 0,
                    'learned_domains' => [],
                    'search_results' => $searchResult
                ]);
            }

            $learnOptions = [];
            if ($minSegments !== null) $learnOptions['min_segments'] = $minSegments;
            if ($maxAdPercentage !== null) $learnOptions['max_ad_percentage'] = $maxAdPercentage;

            $successCount = 0;
            $failCount = 0;
            $learnedDomains = [];
            $siteResults = [];
            $resultDetails = [];

            if ($useMultiThread && TaskRunner::isMultiThreadAvailable() && $totalVideos > 1) {
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfBase = $scheme . '://' . $host . $basePath . '/mx.php?action=sites/learn_video';

                $tasks = [];
                foreach ($allVideos as $idx => $video) {
                    $postData = ['url' => $video['url']];
                    if ($minSegments !== null) $postData['min_segments'] = $minSegments;
                    if ($maxAdPercentage !== null) $postData['max_ad_percentage'] = $maxAdPercentage;
                    $tasks[] = [
                        'id' => $idx,
                        'url' => $video['url'],
                        'site' => $video['site'],
                        'name' => $video['name'],
                        'post_data' => $postData
                    ];
                }

                $runner = TaskRunner::create([
                    'concurrency' => $concurrency,
                    'mode' => TaskRunner::MODE_CURL_MULTI,
                    'timeout' => 120
                ]);

                $results = $runner->run($tasks, $selfBase);

                foreach ($results as $i => $result) {
                    $video = $allVideos[$i];
                    $siteName = $video['site'] ?? '';
                    if (!isset($siteResults[$siteName])) {
                        $siteResults[$siteName] = [
                            'site' => $siteName,
                            'videos_found' => $siteVideoMap[$siteName] ?? 0,
                            'videos_learned' => 0,
                            'videos_failed' => 0,
                            'domains' => [],
                            'fail_reasons' => []
                        ];
                    }

                    $data = $result->data;
                    if (is_string($data)) {
                        $decoded = json_decode($data, true);
                        if ($decoded !== null) {
                            $data = $decoded;
                        }
                    }

                    if ($result->success && is_array($data) && !empty($data['success'])) {
                        $successCount++;
                        $siteResults[$siteName]['videos_learned']++;
                        if (!empty($data['domain'])) {
                            $learnedDomains[$data['domain']] = true;
                            if (!isset($siteResults[$siteName]['domains'][$data['domain']])) {
                                $siteResults[$siteName]['domains'][$data['domain']] = 0;
                            }
                            $siteResults[$siteName]['domains'][$data['domain']]++;
                        }
                        $resultDetails[] = [
                            'name' => $video['name'],
                            'site' => $siteName,
                            'url' => $video['url'],
                            'success' => true,
                            'domain' => $data['domain'] ?? '',
                            'segments_count' => $data['segments_count'] ?? 0,
                            'ad_percentage' => $data['ad_percentage'] ?? 0,
                            'duration' => $result->duration
                        ];
                    } else {
                        $failCount++;
                        $siteResults[$siteName]['videos_failed']++;
                        $failMsg = '';
                        if (!$result->success) {
                            $failMsg = $result->error ?: '请求失败';
                        } elseif (is_array($data) && !empty($data['message'])) {
                            $failMsg = $data['message'];
                        } elseif (is_string($data)) {
                            $failMsg = '响应解析失败';
                        } else {
                            $failMsg = '未知错误';
                        }
                        if (!isset($siteResults[$siteName]['fail_reasons'][$failMsg])) {
                            $siteResults[$siteName]['fail_reasons'][$failMsg] = 0;
                        }
                        $siteResults[$siteName]['fail_reasons'][$failMsg]++;
                        $resultDetails[] = [
                            'name' => $video['name'],
                            'site' => $siteName,
                            'url' => $video['url'],
                            'success' => false,
                            'message' => $failMsg,
                            'duration' => $result->duration
                        ];
                    }
                }

                $mode = $runner->getActualMode();
            } else {
                foreach ($allVideos as $video) {
                    $siteName = $video['site'] ?? '';
                    if (!isset($siteResults[$siteName])) {
                        $siteResults[$siteName] = [
                            'site' => $siteName,
                            'videos_found' => $siteVideoMap[$siteName] ?? 0,
                            'videos_learned' => 0,
                            'videos_failed' => 0,
                            'domains' => [],
                            'fail_reasons' => []
                        ];
                    }

                    $videoStart = microtime(true);
                    $result = $siteManager->learnFromVideoUrl($video['url'], $ruleManager, $learnOptions);
                    $videoDuration = round((microtime(true) - $videoStart) * 1000, 2);

                    if (!empty($result['success'])) {
                        $successCount++;
                        $siteResults[$siteName]['videos_learned']++;
                        if (!empty($result['domain'])) {
                            $learnedDomains[$result['domain']] = true;
                            if (!isset($siteResults[$siteName]['domains'][$result['domain']])) {
                                $siteResults[$siteName]['domains'][$result['domain']] = 0;
                            }
                            $siteResults[$siteName]['domains'][$result['domain']]++;
                        }
                        $resultDetails[] = [
                            'name' => $video['name'],
                            'site' => $siteName,
                            'url' => $video['url'],
                            'success' => true,
                            'domain' => $result['domain'] ?? '',
                            'segments_count' => $result['segments_count'] ?? 0,
                            'ad_percentage' => $result['ad_percentage'] ?? 0,
                            'duration' => $videoDuration
                        ];
                    } else {
                        $failCount++;
                        $siteResults[$siteName]['videos_failed']++;
                        $failMsg = $result['message'] ?? '未知错误';
                        if (!isset($siteResults[$siteName]['fail_reasons'][$failMsg])) {
                            $siteResults[$siteName]['fail_reasons'][$failMsg] = 0;
                        }
                        $siteResults[$siteName]['fail_reasons'][$failMsg]++;
                        $resultDetails[] = [
                            'name' => $video['name'],
                            'site' => $siteName,
                            'url' => $video['url'],
                            'success' => false,
                            'message' => $failMsg,
                            'duration' => $videoDuration
                        ];
                    }
                }

                $mode = 'serial';
            }

            $totalTime = round((microtime(true) - $startTime) * 1000, 2);

            sendJsonResponse([
                'success' => true,
                'message' => '搜索学习完成',
                'keyword' => $keyword,
                'sites_searched' => $searchResult['sites_searched'] ?? 0,
                'total_found' => $totalVideos,
                'total_learned' => $successCount,
                'total_failed' => $failCount,
                'total_time' => $totalTime,
                'mode' => $mode,
                'concurrency' => $useMultiThread ? $concurrency : 1,
                'learned_domains' => array_keys($learnedDomains),
                'site_results' => array_values($siteResults),
                'details' => $resultDetails
            ]);
            break;

        case 'sites/learn_batch':
            $input = getInputJson();
            $urls = $input['urls'] ?? [];
            $concurrency = isset($input['concurrency']) ? intval($input['concurrency']) : 5;
            $useMultiThread = !empty($input['multi_thread']);

            if (empty($urls) || !is_array($urls)) {
                sendJsonResponse(['success' => false, 'message' => '请提供视频URL列表'], 400);
            }

            $concurrency = max(1, min(10, $concurrency));
            $total = count($urls);

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfBase = $scheme . '://' . $host . $basePath . '/mx.php?action=sites/learn_video';

            $tasks = [];
            foreach ($urls as $idx => $url) {
                $tasks[] = [
                    'id' => $idx,
                    'url' => $url,
                    'post_data' => ['url' => $url]
                ];
            }

            $multiThreadFailed = false;
            if ($useMultiThread && TaskRunner::isMultiThreadAvailable()) {
                try {
                    $runner = TaskRunner::create([
                        'concurrency' => $concurrency,
                        'mode' => TaskRunner::MODE_CURL_MULTI,
                        'timeout' => 120
                    ]);

                    $startTime = microtime(true);
                    $results = $runner->run($tasks, $selfBase);
                    $totalTime = round((microtime(true) - $startTime) * 1000, 2);

                    $successCount = 0;
                    $failCount = 0;
                    $learnedDomains = [];
                    $resultDetails = [];

                    foreach ($results as $i => $result) {
                        $data = $result->data;
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            if ($decoded !== null) {
                                $data = $decoded;
                            }
                        }
                        if ($result->success && is_array($data) && !empty($data['success'])) {
                            $successCount++;
                            if (!empty($data['domain'])) {
                                $learnedDomains[$data['domain']] = true;
                            }
                            $resultDetails[] = [
                                'url' => $urls[$i],
                                'success' => true,
                                'domain' => $data['domain'] ?? '',
                                'segments_count' => $data['segments_count'] ?? 0,
                                'ad_count' => $data['ad_count'] ?? 0,
                                'duration' => $result->duration
                            ];
                        } else {
                            $failCount++;
                            $failMsg = '';
                            if (!$result->success) {
                                $failMsg = $result->error ?: '请求失败';
                            } elseif (is_array($data) && !empty($data['message'])) {
                                $failMsg = $data['message'];
                            } elseif (is_string($data)) {
                                $failMsg = '响应解析失败';
                            } else {
                                $failMsg = '未知错误';
                            }
                            $resultDetails[] = [
                                'url' => $urls[$i],
                                'success' => false,
                                'message' => $failMsg,
                                'duration' => $result->duration
                            ];
                        }
                    }

                    $failRate = $total > 0 ? ($failCount / $total) : 0;
                    if ($failRate > 0.8) {
                        $multiThreadFailed = true;
                    } else {
                        sendJsonResponse([
                            'success' => true,
                            'mode' => $runner->getActualMode(),
                            'concurrency' => $concurrency,
                            'total' => $total,
                            'success_count' => $successCount,
                            'fail_count' => $failCount,
                            'total_time' => $totalTime,
                            'learned_domains' => array_keys($learnedDomains),
                            'results' => $resultDetails
                        ]);
                        break;
                    }
                } catch (Throwable $e) {
                    $multiThreadFailed = true;
                }
            }
            
            if ($multiThreadFailed || !$useMultiThread || !TaskRunner::isMultiThreadAvailable()) {
                $startTime = microtime(true);
                $successCount = 0;
                $failCount = 0;
                $learnedDomains = [];
                $resultDetails = [];

                foreach ($urls as $i => $url) {
                    $result = $siteManager->learnFromVideoUrl($url, $ruleManager, []);
                    if (!empty($result['success'])) {
                        $successCount++;
                        if (!empty($result['domain'])) {
                            $learnedDomains[$result['domain']] = true;
                        }
                    } else {
                        $failCount++;
                    }
                    $resultDetails[] = array_merge($result, ['url' => $url]);
                }
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);

                sendJsonResponse([
                    'success' => true,
                    'mode' => 'serial',
                    'concurrency' => 1,
                    'total' => $total,
                    'success_count' => $successCount,
                    'fail_count' => $failCount,
                    'total_time' => $totalTime,
                    'learned_domains' => array_keys($learnedDomains),
                    'results' => $resultDetails
                ]);
            }
            break;

        case 'sites/analyze_batch':
            $input = getInputJson();
            $urls = $input['urls'] ?? [];
            $concurrency = isset($input['concurrency']) ? intval($input['concurrency']) : 5;
            $useMultiThread = !empty($input['multi_thread']);

            if (empty($urls) || !is_array($urls)) {
                sendJsonResponse(['success' => false, 'message' => '请提供视频URL列表'], 400);
            }

            $concurrency = max(1, min(10, $concurrency));
            $total = count($urls);

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfBase = $scheme . '://' . $host . $basePath . '/mx.php?action=analyze';

            $tasks = [];
            foreach ($urls as $idx => $url) {
                $tasks[] = [
                    'id' => $idx,
                    'url' => $selfBase . '&url=' . urlencode($url)
                ];
            }

            $multiThreadFailed = false;
            if ($useMultiThread && TaskRunner::isMultiThreadAvailable()) {
                try {
                    $runner = TaskRunner::create([
                        'concurrency' => $concurrency,
                        'mode' => TaskRunner::MODE_CURL_MULTI,
                        'timeout' => 120
                    ]);

                    $startTime = microtime(true);
                    $results = $runner->run($tasks, "{url}");
                    $totalTime = round((microtime(true) - $startTime) * 1000, 2);

                    $successCount = 0;
                    $failCount = 0;
                    $resultDetails = [];

                    foreach ($results as $i => $result) {
                        $data = $result->data;
                        if (is_string($data)) {
                            $decoded = json_decode($data, true);
                            if ($decoded !== null) {
                                $data = $decoded;
                            }
                        }
                        if ($result->success && is_array($data) && !empty($data['success'])) {
                            $successCount++;
                            $resultDetails[] = [
                                'url' => $urls[$i],
                                'success' => true,
                                'domain' => $data['domain'] ?? '',
                                'fast_mode' => $data['fastMode'] ?? false,
                                'stats' => $data['stats'] ?? [],
                                'duration' => $result->duration
                            ];
                        } else {
                            $failCount++;
                            $failMsg = '';
                            if (!$result->success) {
                                $failMsg = $result->error ?: '请求失败';
                            } elseif (is_array($data) && !empty($data['message'])) {
                                $failMsg = $data['message'];
                            } elseif (is_string($data)) {
                                $failMsg = '响应解析失败';
                            } else {
                                $failMsg = '未知错误';
                            }
                            $resultDetails[] = [
                                'url' => $urls[$i],
                                'success' => false,
                                'message' => $failMsg,
                                'duration' => $result->duration
                            ];
                        }
                    }

                    $failRate = $total > 0 ? ($failCount / $total) : 0;
                    if ($failRate > 0.8) {
                        $multiThreadFailed = true;
                    } else {
                        sendJsonResponse([
                            'success' => true,
                            'mode' => $runner->getActualMode(),
                            'concurrency' => $concurrency,
                            'total' => $total,
                            'success_count' => $successCount,
                            'fail_count' => $failCount,
                            'total_time' => $totalTime,
                            'results' => $resultDetails
                        ]);
                        break;
                    }
                } catch (Throwable $e) {
                    $multiThreadFailed = true;
                }
            }
            
            if ($multiThreadFailed || !$useMultiThread || !TaskRunner::isMultiThreadAvailable()) {
                $startTime = microtime(true);
                $successCount = 0;
                $failCount = 0;
                $resultDetails = [];

                foreach ($urls as $i => $url) {
                    try {
                        $parsedUrl = parse_url($url);
                        $domain = $parsedUrl['host'] ?? '';
                        $mediaUrl = resolveMasterPlaylist($url);

                        $engine = new EnhancedAdRuleEngine([
                            'checkDiscontinuity' => true,
                            'checkRepetitiveDuration' => true
                        ]);
                        $engine->setDomain($domain);

                        $parser = new M3U8Parser();
                        $playlist = $parser->parse($mediaUrl);

                        if (empty($playlist['segments'])) {
                            $failCount++;
                            $resultDetails[] = ['url' => $url, 'success' => false, 'message' => '无法解析视频片段'];
                            continue;
                        }

                        $analysis = $engine->analyzeAllSegments($playlist['segments']);
                        $successCount++;
                        $resultDetails[] = [
                            'url' => $url,
                            'success' => true,
                            'domain' => $domain,
                            'fast_mode' => false,
                            'stats' => [
                                'totalSegments' => $analysis['totalCount'],
                                'adSegments' => $analysis['adCount'],
                                'discontinuityCount' => $analysis['discontinuityCount'],
                                'sequenceJumps' => count($analysis['sequenceJumps']),
                                'adClusters' => count($analysis['adClusters'])
                            ]
                        ];
                    } catch (Throwable $e) {
                        $failCount++;
                        $resultDetails[] = ['url' => $url, 'success' => false, 'message' => $e->getMessage()];
                    }
                }
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);

                sendJsonResponse([
                    'success' => true,
                    'mode' => 'serial',
                    'concurrency' => 1,
                    'total' => $total,
                    'success_count' => $successCount,
                    'fail_count' => $failCount,
                    'total_time' => $totalTime,
                    'results' => $resultDetails
                ]);
            }
            break;

        case 'sites/multi_thread/status':
            $available = TaskRunner::isMultiThreadAvailable();
            $modes = TaskRunner::getAvailableModes();
            $recommended = TaskRunner::getRecommendedMode();

            sendJsonResponse([
                'success' => true,
                'available' => $available,
                'modes' => $modes,
                'recommended_mode' => $recommended,
                'php_sapi' => PHP_SAPI,
                'pcntl_support' => function_exists('pcntl_fork'),
                'curl_multi_support' => function_exists('curl_multi_init')
            ]);
            break;

        case 'sites/auto_learn/config':
            $config = $siteManager->getAutoLearnConfig();
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            sendJsonResponse([
                'success' => true,
                'config' => $config,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn
            ]);
            break;

        case 'sites/auto_learn/config/save':
            $input = getInputJson();
            $result = $siteManager->setAutoLearnConfig($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'sites/auto_learn/run':
            $input = getInputJson();
            $useMultiThread = !empty($input['multi_thread']);
            $concurrency = isset($input['concurrency']) ? intval($input['concurrency']) : 5;
            $options = [
                'max_sites' => $input['max_sites'] ?? null,
                'videos_per_site' => $input['videos_per_site'] ?? null,
                'keyword' => $input['keyword'] ?? ''
            ];

            if ($useMultiThread && TaskRunner::isMultiThreadAvailable()) {
                $config = $siteManager->getAutoLearnConfig();
                if (empty($config['enabled'])) {
                    sendJsonResponse(['success' => false, 'message' => '自动学习未启用'], 400);
                }

                $maxSites = $options['max_sites'] ?? $config['max_sites_per_run'] ?? 5;
                $videosPerSite = $options['videos_per_site'] ?? $config['videos_per_site'] ?? 5;
                $minSegments = $config['min_segments'] ?? 50;
                $maxAdPercentage = $config['max_ad_percentage'] ?? 90;
                $keyword = $options['keyword'] ?? '';

                $sites = $siteManager->getAllSites(false);
                $sites = array_slice($sites, 0, $maxSites);

                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfBase = $scheme . '://' . $host . $basePath . '/mx.php?action=sites/learn_video';

                $allVideos = [];
                $siteVideoMap = [];

                foreach ($sites as $site) {
                    try {
                        if (!empty($keyword)) {
                            $fetchResult = $siteManager->searchVideos($site['api_url'], $keyword, 1, $videosPerSite * 3);
                        } else {
                            $fetchResult = $siteManager->fetchVideos($site['api_url'], 1, $videosPerSite * 3);
                        }
                        if ($fetchResult['success']) {
                            $videos = $fetchResult['videos'] ?? [];
                            $count = 0;
                            foreach ($videos as $video) {
                                if ($count >= $videosPerSite) break;
                                $videoUrl = $video['url'] ?? $video['first_url'] ?? '';
                                if (!empty($videoUrl)) {
                                    $allVideos[] = [
                                        'id' => count($allVideos),
                                        'url' => $videoUrl,
                                        'site' => $site['name'],
                                        'name' => $video['name'] ?? '未知'
                                    ];
                                    if (!isset($siteVideoMap[$site['name']])) {
                                        $siteVideoMap[$site['name']] = 0;
                                    }
                                    $count++;
                                }
                            }
                        }
                    } catch (Throwable $e) {
                    }
                }

                $concurrency = max(1, min(10, $concurrency));
                $runner = TaskRunner::create([
                    'concurrency' => $concurrency,
                    'mode' => TaskRunner::MODE_CURL_MULTI,
                    'timeout' => 120
                ]);

                $learnTasks = [];
                foreach ($allVideos as $video) {
                    $learnTasks[] = [
                        'id' => $video['id'],
                        'site' => $video['site'],
                        'name' => $video['name'],
                        'url' => $video['url'],
                        'post_data' => ['url' => $video['url']]
                    ];
                }

                $startTime = microtime(true);
                $results = $runner->run($learnTasks, $selfBase);
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);

                $successCount = 0;
                $failCount = 0;
                $learnedDomains = [];
                $siteResults = [];

                foreach ($results as $i => $result) {
                    $video = $allVideos[$i];
                    $siteName = $video['site'] ?? '';
                    if (!isset($siteResults[$siteName])) {
                        $siteResults[$siteName] = [
                            'site' => $siteName,
                            'videos_checked' => 0,
                            'videos_learned' => 0,
                            'videos_failed' => 0,
                            'domains' => [],
                            'fail_reasons' => []
                        ];
                    }
                    $siteResults[$siteName]['videos_checked']++;

                    $data = $result->data;
                    if (is_string($data)) {
                        $decoded = json_decode($data, true);
                        if ($decoded !== null) {
                            $data = $decoded;
                        }
                    }

                    if ($result->success && is_array($data) && !empty($data['success'])) {
                        $successCount++;
                        $siteResults[$siteName]['videos_learned']++;
                        if (!empty($data['domain'])) {
                            $learnedDomains[$data['domain']] = true;
                            if (!isset($siteResults[$siteName]['domains'][$data['domain']])) {
                                $siteResults[$siteName]['domains'][$data['domain']] = 0;
                            }
                            $siteResults[$siteName]['domains'][$data['domain']]++;
                        }
                    } else {
                        $failCount++;
                        $siteResults[$siteName]['videos_failed']++;
                        $failMsg = '';
                        if (!$result->success) {
                            $failMsg = $result->error ?: '请求失败';
                        } elseif (is_array($data) && !empty($data['message'])) {
                            $failMsg = $data['message'];
                        } elseif (is_string($data)) {
                            $failMsg = '响应解析失败';
                        } else {
                            $failMsg = '未知错误';
                        }
                        if (!isset($siteResults[$siteName]['fail_reasons'][$failMsg])) {
                            $siteResults[$siteName]['fail_reasons'][$failMsg] = 0;
                        }
                        $siteResults[$siteName]['fail_reasons'][$failMsg]++;
                    }
                }

                $siteManager->setLastLearnTime();

                $totalVideos = $successCount + $failCount;
                $failRate = $totalVideos > 0 ? ($failCount / $totalVideos) : 0;

                if ($failRate > 0.8 && $failCount > 0) {
                    $result = $siteManager->runAutoLearn($ruleManager, $options);
                    $result['mode_fallback_from'] = $runner->getActualMode();
                    sendJsonResponse($result, $result['success'] ? 200 : 400);
                } else {
                    sendJsonResponse([
                        'success' => true,
                        'message' => '自动学习完成（多线程模式）',
                        'mode' => $runner->getActualMode(),
                        'concurrency' => $concurrency,
                        'keyword' => $keyword,
                        'sites_processed' => count($sites),
                        'total_learned' => $successCount,
                        'total_failed' => $failCount,
                        'total_time' => $totalTime,
                        'learned_domains' => array_keys($learnedDomains),
                        'details' => array_values($siteResults)
                    ]);
                }
            } else {
                $result = $siteManager->runAutoLearn($ruleManager, $options);
                sendJsonResponse($result, $result['success'] ? 200 : 400);
            }
            break;

        case 'sites/auto_learn/status':
            $lastLearn = $siteManager->getLastLearnTime();
            $shouldLearn = $siteManager->shouldAutoLearn();
            $config = $siteManager->getAutoLearnConfig();
            sendJsonResponse([
                'success' => true,
                'last_learn_time' => $lastLearn,
                'should_auto_learn' => $shouldLearn,
                'config' => $config
            ]);
            break;

        case 'official_sites/status':

            sendJsonResponse([
                'success' => true,
                'enabled' => $officialMgr->isEnabled(),
                'settings' => $officialMgr->getSettings()
            ]);
            break;

        case 'official_sites/list':

            $includePaused = isset($_GET['include_paused']) && $_GET['include_paused'] === '1';
            $sites = $officialMgr->getAllSites($includePaused);
            sendJsonResponse([
                'success' => true,
                'sites' => $sites,
                'total' => count($sites),
                'enabled' => $officialMgr->isEnabled(),
                'settings' => $officialMgr->getSettings()
            ]);
            break;

        case 'official_sites/get':

            $name = $_GET['name'] ?? '';
            $site = $officialMgr->getSiteByName($name);
            if ($site) {
                sendJsonResponse(['success' => true, 'site' => $site]);
            } else {
                sendJsonResponse(['success' => false, 'message' => '资源站不存在'], 404);
            }
            break;

        case 'official_sites/add':

            $input = getInputJson();
            $result = $officialMgr->addSite($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/update':

            $input = getInputJson();
            $name = $input['name'] ?? '';
            unset($input['name']);
            $result = $officialMgr->updateSite($name, $input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/delete':

            $input = getInputJson();
            $name = $input['name'] ?? '';
            $result = $officialMgr->deleteSite($name);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/fetch_videos':

            $name = $_GET['name'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $result = $officialMgr->fetchVideos($name, $page, $limit);
            sendJsonResponse($result);
            break;

        case 'official_sites/search':

            $name = $_GET['name'] ?? '';
            $keyword = $_GET['keyword'] ?? '';
            $page = intval($_GET['page'] ?? 1);
            $limit = intval($_GET['limit'] ?? 20);
            $result = $officialMgr->searchVideos($name, $keyword, $page, $limit);
            sendJsonResponse($result);
            break;

        case 'official_sites/search_all':

            $keyword = $_GET['keyword'] ?? '';
            $maxSites = intval($_GET['max_sites'] ?? 5);
            $limitPerSite = intval($_GET['limit_per_site'] ?? 10);
            $result = $officialMgr->searchAllSites($keyword, $maxSites, $limitPerSite);
            sendJsonResponse($result);
            break;

        case 'official_sites/set_domain':

            $input = getInputJson();
            $name = $input['name'] ?? '';
            $domainIndex = intval($input['domain_index'] ?? 0);
            $officialMgr->setActiveDomain($name, $domainIndex);
            sendJsonResponse(['success' => true, 'message' => '已切换域名']);
            break;

        case 'official_sites/settings/save':

            $input = getInputJson();
            $result = $officialMgr->updateSettings($input);
            sendJsonResponse($result, $result['success'] ? 200 : 400);
            break;

        case 'official_sites/toggle':

            $input = getInputJson();
            $enabled = !empty($input['enabled']);
            $result = $officialMgr->setEnabled($enabled);
            sendJsonResponse($result);
            break;

        case 'official_replace/config':
            $config = $officialReplaceMgr->getConfig();
            sendJsonResponse([
                'success' => true,
                'config' => $config
            ]);
            break;

        case 'official_replace/config/save':
            $input = getInputJson();
            $result = $officialReplaceMgr->saveConfigData($input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '保存成功' : '保存失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platforms':
            $platforms = $officialReplaceMgr->getPlatforms();
            sendJsonResponse([
                'success' => true,
                'platforms' => $platforms,
                'total' => count($platforms)
            ]);
            break;

        case 'official_replace/platform/add':
            $input = getInputJson();
            $result = $officialReplaceMgr->addPlatform($input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '添加成功' : '添加失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platform/update':
            $input = getInputJson();
            $index = $input['index'] ?? -1;
            if ($index < 0) {
                sendJsonResponse(['success' => false, 'message' => '缺少 index 参数'], 400);
            }
            $result = $officialReplaceMgr->updatePlatform($index, $input);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '更新成功' : '更新失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/platform/delete':
            $input = getInputJson();
            $index = $input['index'] ?? -1;
            if ($index < 0) {
                sendJsonResponse(['success' => false, 'message' => '缺少 index 参数'], 400);
            }
            $result = $officialReplaceMgr->deletePlatform($index);
            sendJsonResponse([
                'success' => $result,
                'message' => $result ? '删除成功' : '删除失败'
            ], $result ? 200 : 400);
            break;

        case 'official_replace/resolve':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $result = $officialReplaceMgr->resolve($url);
            sendJsonResponse($result, $result['success'] ? 200 : 404);
            break;

        case 'official_replace/info':
            // 禁止缓存，每次都重新解析
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }
            $result = $officialReplaceMgr->resolve($url);
            
            if ($result['success']) {
                $m3u8Url = $result['m3u8_url'] ?? '';
                $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $basePath = dirname($requestUri);
                $basePath = $basePath === '/' ? '' : $basePath;
                $selfUrl = $scheme . '://' . $host . $basePath;
                $mxjxUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
                
                sendJsonResponse([
                    'success' => true,
                    'platform' => $result['platform'],
                    'original_url' => $result['original_url'],
                    'video_title' => $result['video_title'],
                    'video_name' => $result['video_name'] ?? '',
                    'video_pic' => $result['video_pic'] ?? '',
                    'video_remarks' => $result['video_remarks'] ?? '',
                    'match_score' => $result['match_score'],
                    'site' => $result['site'],
                    'm3u8_url' => $m3u8Url,
                    'target_episode' => $result['target_episode'] ?? '',
                    'ad_skip_url' => $mxjxUrl,
                    'all_urls' => $result['all_urls'],
                    'episodes' => $result['episodes'] ?? count($result['all_urls']),
                    'timestamp' => time() // 添加时间戳便于追踪
                ]);
            } else {
                sendJsonResponse($result, 404);
            }
            break;

        case 'player/config':
            $configFile = $rootDir . '/gz/player_config.php';
            $defaultConfig = [
                'player' => 'dplayer',
                'autoplay' => false,
                'preload' => 'auto',
                'api_base_url' => '',
                'hls_config' => [
                    'enableWorker' => true,
                    'lowLatencyMode' => false,
                    'maxBufferLength' => 30,
                    'maxMaxBufferLength' => 600,
                    'minBufferLength' => 2,
                    'maxBufferSize' => 60 * 1000 * 1000,
                    'maxBufferHole' => 0.5,
                    'highBufferWatchdogPeriod' => 2,
                    'startLevel' => -1,
                    'capLevelToPlayerSize' => false,
                ],
            ];
            $config = $defaultConfig;
            if (file_exists($configFile)) {
                $fileConfig = require $configFile;
                if (is_array($fileConfig)) {
                    $config = array_merge($defaultConfig, $fileConfig);
                }
            }
            sendJsonResponse(['success' => true, 'config' => $config]);
            break;

        case 'proxy/list':
            if (!isset($proxyManager)) {
                sendJsonResponse(['success' => false, 'message' => '代理模块未初始化'], 500);
                break;
            }
            $activeProxies = $proxyManager->getActiveProxies();
            $proxies = [];

            foreach ($activeProxies as $p) {
                $proxyUrl = $p['type'] . '://' . $p['host'] . ':' . $p['port'];
                if (!empty($p['username']) && !empty($p['password'])) {
                    $proxyUrl = $p['type'] . '://' . $p['username'] . ':' . $p['password'] . '@' . $p['host'] . ':' . $p['port'];
                }
                $responseTime = isset($p['response_time']) ? round(floatval($p['response_time']), 2) : 0;
                $proxies[] = [
                    'id' => $p['id'] ?? '',
                    'name' => $p['name'] ?: ($p['host'] . ':' . $p['port']),
                    'type' => $p['type'] ?? 'http',
                    'url' => $proxyUrl,
                    'host' => $p['host'],
                    'port' => $p['port'],
                    'response_time' => $responseTime,
                    'success_count' => $p['success_count'] ?? 0,
                    'fail_count' => $p['fail_count'] ?? 0,
                    'priority' => $p['priority'] ?? 100,
                    'last_check' => $p['last_check'] ?? null,
                    'last_success' => $p['last_success'] ?? null,
                ];
            }

            usort($proxies, function($a, $b) {
                $aHas = $a['response_time'] > 0;
                $bHas = $b['response_time'] > 0;
                if ($aHas && !$bHas) return -1;
                if (!$aHas && $bHas) return 1;
                if ($aHas && $bHas) {
                    if ($a['response_time'] != $b['response_time']) {
                        return $a['response_time'] < $b['response_time'] ? -1 : 1;
                    }
                }
                if ($a['priority'] != $b['priority']) {
                    return $a['priority'] - $b['priority'];
                }
                $fa = $a['fail_count'] ?? 0;
                $fb = $b['fail_count'] ?? 0;
                return $fa - $fb;
            });

            $stats = $proxyManager->getStats();
            sendJsonResponse([
                'success' => true,
                'proxies' => $proxies,
                'count' => count($proxies),
                'stats' => $stats,
                'auto_switch' => !empty($stats['auto_switch'])
            ]);
            break;

        case 'proxy/check':
            if (!isset($proxyManager)) {
                sendJsonResponse(['success' => false, 'message' => '代理模块未初始化'], 500);
                break;
            }
            $proxyId = $_GET['id'] ?? '';

            if ($proxyId) {
                $result = $proxyManager->testProxy($proxyId);
                sendJsonResponse($result);
            } else {
                $results = $proxyManager->checkAllProxies();
                usort($results, function($a, $b) {
                    $aOk = $a['success'];
                    $bOk = $b['success'];
                    if ($aOk && !$bOk) return -1;
                    if (!$aOk && $bOk) return 1;
                    if ($aOk && $bOk) {
                        return $a['response_time'] < $b['response_time'] ? -1 : 1;
                    }
                    return 0;
                });
                sendJsonResponse([
                    'success' => true,
                    'results' => $results,
                    'count' => count($results)
                ]);
            }
            break;

        case 'player/config/save':
            $input = getInputJson();
            $configFile = $rootDir . '/gz/player_config.php';
            
            $allowedKeys = ['player', 'autoplay', 'preload', 'api_base_url', 'hls_config'];
            $newConfig = [];
            
            foreach ($allowedKeys as $key) {
                if (isset($input[$key])) {
                    $newConfig[$key] = $input[$key];
                }
            }
            
            if (empty($newConfig)) {
                sendJsonResponse(['success' => false, 'message' => '没有有效的配置项'], 400);
            }
            
            $existingConfig = [];
            if (file_exists($configFile)) {
                $existingConfig = require $configFile;
                if (!is_array($existingConfig)) {
                    $existingConfig = [];
                }
            }
            
            $finalConfig = array_merge($existingConfig, $newConfig);
            
            $configContent = '<?php' . "\nreturn " . var_export($finalConfig, true) . ';';
            $result = file_put_contents($configFile, $configContent);
            
            sendJsonResponse([
                'success' => $result !== false,
                'message' => $result !== false ? '保存成功' : '保存失败',
                'config' => $finalConfig
            ], $result !== false ? 200 : 400);
            break;

        case 'moxi':
        case 'moxi/api':
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $url = $_GET['url'] ?? '';
            $playType = $_GET['type'] ?? '';
            
            if (empty($url)) {
                http_response_code(400);
                echo json_encode([
                    'code' => 400,
                    'url' => '',
                    'msg' => '缺少 url 参数',
                    'jm' => '',
                    'js' => '',
                    'time' => date('Y-m-d H:i:s'),
                    'kfz' => '沫兮API'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
            
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            
            $officialDomains = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];
            $parsedUrl = parse_url($url);
            $urlHost = $parsedUrl['host'] ?? '';
            $isOfficialUrl = false;
            
            foreach ($officialDomains as $domain) {
                if (strpos($urlHost, $domain) !== false) {
                    $isOfficialUrl = true;
                    break;
                }
            }
            
            $playUrl = '';
            $juMing = '';
            $jiShu = '';
            $code = 200;
            $msg = '解析成功';
            
            $extractTitleFromUrl = function($url) use (&$extractEpisodeFromUrl) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                $host = $parsed['host'] ?? '';
                
                if (empty($path)) {
                    return $host ?: '在线视频';
                }
                
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                if (empty($pathParts)) {
                    return $host ?: '在线视频';
                }
                
                $fileName = end($pathParts);
                $fileNameWithoutExt = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $fileName);
                
                $isEpisodeLike = false;
                if (preg_match('/第?\d+[集期话]/u', $fileNameWithoutExt)) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/^(episode|ep|e|集|期|话)[_\-]?\d+$/i', $fileNameWithoutExt)) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/^\d+$/',$fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 4) {
                    $isEpisodeLike = true;
                }
                if (preg_match('/[_\-]\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 15) {
                    $prefix = preg_replace('/[_\-]\d+$/', '', $fileNameWithoutExt);
                    if (in_array(strtolower($prefix), ['episode', 'ep', 'e', '第', '集', ''])) {
                        $isEpisodeLike = true;
                    }
                }
                
                if ($isEpisodeLike || $fileName === 'index.m3u8' || $fileNameWithoutExt === 'index') {
                    $candidates = [];
                    $dirParts = array_slice($pathParts, 0, -1);
                    foreach (array_reverse($dirParts) as $part) {
                        if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                        if (is_numeric($part)) continue;
                        if (strlen($part) < 2) continue;
                        $lowerPart = strtolower($part);
                        if (in_array($lowerPart, ['video', 'videos', 'm3u8', 'movie', 'tv', 'play', 'player'])) continue;
                        $candidates[] = $part;
                    }
                    if (!empty($candidates)) {
                        $title = $candidates[0];
                        $title = preg_replace('/[_-]+/', ' ', $title);
                        $title = trim($title);
                        if (!empty($title)) {
                            if (preg_match('/^[a-z\s]+$/i', $title)) {
                                return ucwords($title);
                            }
                            return $title;
                        }
                    }
                    return $host ?: '在线视频';
                }
                
                $title = $fileNameWithoutExt;
                $title = preg_replace('/[_-]+/', ' ', $title);
                $title = preg_replace('/\s*\d+\s*$/', '', $title);
                $title = trim($title);
                
                if (empty($title) || strlen($title) < 2) {
                    $dirParts = array_slice($pathParts, 0, -1);
                    foreach (array_reverse($dirParts) as $part) {
                        if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                        if (is_numeric($part)) continue;
                        if (strlen($part) < 2) continue;
                        $lowerPart = strtolower($part);
                        if (in_array($lowerPart, ['video', 'videos', 'm3u8', 'movie', 'tv'])) continue;
                        $title = $part;
                        $title = preg_replace('/[_-]+/', ' ', $title);
                        $title = trim($title);
                        if (!empty($title)) {
                            if (preg_match('/^[a-z\s]+$/i', $title)) {
                                return ucwords($title);
                            }
                            return $title;
                        }
                    }
                    return $host ?: '在线视频';
                }
                
                if (preg_match('/^[a-z\s]+$/i', $title)) {
                    return ucwords($title);
                }
                
                return $title;
            };
            
            $extractEpisodeFromUrl = function($url) {
                $parsed = parse_url($url);
                $path = $parsed['path'] ?? '';
                
                if (empty($path)) {
                    return '正片';
                }
                
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                foreach (array_reverse($pathParts) as $part) {
                    $part = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $part);
                    
                    if (preg_match('/第(\d+)[集期话]/u', $part, $matches)) {
                        return '第' . $matches[1] . '集';
                    }
                    
                    if (preg_match('/(?:episode|ep|e)[_\-]?(\d+)/i', $part, $matches)) {
                        return '第' . intval($matches[1]) . '集';
                    }
                    
                    if (preg_match('/^(\d+)$/', $part, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > 0 && $num < 1000) {
                            return '第' . $num . '集';
                        }
                    }
                    
                    if (preg_match('/[_\-](\d+)$/', $part, $matches)) {
                        $num = intval($matches[1]);
                        if ($num > 0 && $num < 1000) {
                            $prefix = preg_replace('/[_\-]\d+$/', '', $part);
                            if (empty($prefix) || in_array(strtolower($prefix), ['episode', 'ep', 'e'])) {
                                return '第' . $num . '集';
                            }
                        }
                    }
                }
                
                return '正片';
            };
            
            if ($isOfficialUrl) {
                $result = $officialReplaceMgr->resolve($url);
                if ($result['success']) {
                    $m3u8Url = $result['m3u8_url'] ?? '';
                    $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
                    $juMing = $result['video_title'] ?? '';
                    $jiShu = $result['target_episode'] ?? ($result['episode'] ?? '');
                    if (empty($jiShu)) {
                        $jiShu = '正片';
                    }
                } else {
                    $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
                    $juMing = $result['video_title'] ?? '';
                    if (empty($juMing)) {
                        $juMing = $extractTitleFromUrl($url);
                    }
                    $jiShu = $result['episode'] ?? '';
                    if (empty($jiShu)) {
                        $jiShu = $extractEpisodeFromUrl($url);
                    }
                    $code = 200;
                    $msg = '解析成功';
                }
            } else {
                $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($url);
                $juMing = $extractTitleFromUrl($url);
                $jiShu = $extractEpisodeFromUrl($url);
                
                $searchKeyword = '';
                $parsedUrl = parse_url($url);
                $path = $parsedUrl['path'] ?? '';
                $pathParts = array_values(array_filter(explode('/', $path), function($v) {
                    return !empty($v);
                }));
                
                foreach ($pathParts as $part) {
                    if (preg_match('/\.(m3u8|mp4|mkv|avi|mov|flv|ts)$/i', $part)) continue;
                    if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                    if (is_numeric($part)) continue;
                    if (strlen($part) < 3) continue;
                    if ($part === 'video' || $part === 'videos' || $part === 'm3u8') continue;
                    $searchKeyword = $part;
                    break;
                }
                
                if (!empty($searchKeyword) && $searchKeyword !== $juMing) {
                    $searchKeyword = preg_replace('/[_-]+/', ' ', $searchKeyword);
                    $searchKeyword = trim($searchKeyword);
                }
                
                if (!empty($searchKeyword) && class_exists('ResourceSiteManager') && isset($siteManager)) {
                    try {
                        $searchResult = $siteManager->searchAllSites($searchKeyword, 3, 5);
                        if ($searchResult['success'] && !empty($searchResult['results'])) {
                            $bestMatch = null;
                            $bestScore = 0;
                            $urlBase = basename($path, '.m3u8');
                            
                            foreach ($searchResult['results'] as $siteResult) {
                                if (empty($siteResult['videos'])) continue;
                                foreach ($siteResult['videos'] as $video) {
                                    $videoName = $video['name'] ?? '';
                                    if (empty($videoName)) continue;
                                    
                                    $score = 0;
                                    similar_text($searchKeyword, $videoName, $score);
                                    
                                    $firstUrl = $video['first_url'] ?? $video['url'] ?? '';
                                    if (!empty($firstUrl)) {
                                        $firstUrlPath = parse_url($firstUrl, PHP_URL_PATH) ?? '';
                                        similar_text($path, $firstUrlPath, $pathScore);
                                        if ($pathScore > $score) {
                                            $score = $pathScore;
                                        }
                                    }
                                    
                                    if ($score > $bestScore && $score > 40) {
                                        $bestScore = $score;
                                        $bestMatch = $video;
                                    }
                                }
                            }
                            
                            if ($bestMatch && $bestScore > 50) {
                                $juMing = $bestMatch['name'] ?? $juMing;
                                if (!empty($bestMatch['remarks'])) {
                                    $jiShu = $bestMatch['remarks'];
                                }
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            
            $response = [
                'code' => $code,
                'url' => $playUrl,
                'msg' => $playUrl,
                'jm' => $juMing,
                'js' => $jiShu,
                'time' => date('Y-m-d H:i:s'),
                'kfz' => '沫兮API - 在线视频解析'
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
            break;

        case 'db/status':
            $status = [
                'use_db' => $useDb,
                'db_type' => $useDb ? $db->getDbType() : 'none',
                'tables' => []
            ];
            if ($useDb) {
                $checkTables = ['sys_config', 'domain_rules', 'resource_sites', 'proxies', 'official_sites', 'official_platforms'];
                foreach ($checkTables as $t) {
                    $status['tables'][$t] = $db->tableExists($t);
                }
                try {
                    $status['rule_count'] = $db->count('domain_rules', 'status = 1');
                    $status['site_count'] = $db->count('resource_sites', 'status = "active"');
                    $status['proxy_count'] = $db->count('proxies', 'status = "active"');
                } catch (Throwable $e) {
                    $status['error'] = $e->getMessage();
                }
                $migration = new DataMigration();
                $status['migrated'] = $migration->isMigrated();
                
                $configFile = __DIR__ . '/db/db_config.php';
                if (file_exists($configFile)) {
                    $status['config'] = require $configFile;
                }
            }
            sendJsonResponse(['success' => true, 'status' => $status]);
            break;

        case 'db/config/save':
            $input = getInputJson();
            $dbType = $input['type'] ?? 'sqlite';
            $config = [
                'type' => $dbType,
                'sqlite_path' => $input['sqlite_path'] ?? (__DIR__ . '/db/data.db'),
                'mysql_host' => $input['mysql_host'] ?? '127.0.0.1',
                'mysql_port' => intval($input['mysql_port'] ?? 3306),
                'mysql_dbname' => $input['mysql_dbname'] ?? 'm3u8_ad',
                'mysql_username' => $input['mysql_username'] ?? 'root',
                'mysql_password' => $input['mysql_password'] ?? '',
                'mysql_charset' => $input['mysql_charset'] ?? 'utf8mb4',
            ];
            $configFile = $rootDir . '/db/db_config.php';
            $content = '<?php' . "\n";
            $content .= 'return ' . var_export($config, true) . ';' . "\n";
            $result = file_put_contents($configFile, $content);
            if ($result === false) {
                sendJsonResponse(['success' => false, 'message' => '写入配置文件失败，请检查权限']);
            }
            try {
                $testDb = new Database($config);
                $testDb->initTables();
                sendJsonResponse(['success' => true, 'message' => '配置保存成功，数据库连接正常']);
            } catch (Throwable $e) {
                sendJsonResponse(['success' => false, 'message' => '配置已保存，但连接失败：' . $e->getMessage()], 400);
            }
            break;

        case 'db/test_connection':
            $input = getInputJson();
            $dbType = $input['type'] ?? 'sqlite';
            $config = [
                'type' => $dbType,
                'sqlite_path' => $input['sqlite_path'] ?? (__DIR__ . '/db/data.db'),
                'mysql_host' => $input['mysql_host'] ?? '127.0.0.1',
                'mysql_port' => intval($input['mysql_port'] ?? 3306),
                'mysql_dbname' => $input['mysql_dbname'] ?? 'm3u8_ad',
                'mysql_username' => $input['mysql_username'] ?? 'root',
                'mysql_password' => $input['mysql_password'] ?? '',
                'mysql_charset' => $input['mysql_charset'] ?? 'utf8mb4',
            ];
            try {
                $testDb = new Database($config);
                $pdo = $testDb->getPdo();
                $info = [
                    'type' => $dbType,
                    'connected' => true,
                ];
                if ($dbType === 'mysql') {
                    $stmt = $pdo->query("SELECT VERSION() as v");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $info['version'] = $row['v'] ?? 'unknown';
                    $stmt = $pdo->query("SHOW TABLES");
                    $info['table_count'] = $stmt->rowCount();
                    $stmt = $pdo->query("SELECT COUNT(*) as c FROM information_schema.tables WHERE table_schema = DATABASE()");
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $info['table_count'] = intval($row['c'] ?? 0);
                } else {
                    $info['version'] = 'SQLite ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                    $info['table_count'] = $stmt->rowCount();
                }
                sendJsonResponse([
                    'success' => true,
                    'message' => '数据库连接成功！',
                    'info' => $info
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => $e->getMessage()
                ], 400);
            }
            break;

        case 'db/migrate':
            if (!$useDb) {
                sendJsonResponse(['success' => false, 'message' => '数据库未启用，无法迁移'], 400);
                break;
            }
            $migration = new DataMigration();
            $result = $migration->migrateAll();
            sendJsonResponse($result, $result['success'] ? 200 : 500);
            break;

        case 'db/init':
            if (!$useDb) {
                sendJsonResponse(['success' => false, 'message' => '数据库未启用'], 400);
                break;
            }
            try {
                $db->initTables();
                sendJsonResponse(['success' => true, 'message' => '数据库表初始化完成']);
            } catch (Throwable $e) {
                sendJsonResponse(['success' => false, 'message' => '初始化失败: ' . $e->getMessage()], 500);
            }
            break;

        case 'info':
            $versionData = file_exists(__DIR__ . '/version.php') ? include __DIR__ . '/version.php' : ['version' => '1.0.0'];
            $version = is_array($versionData) ? ($versionData['version'] ?? '1.0.0') : $versionData;
            $info = [
                'success' => true,
                'name' => 'M3U8广告跳过系统',
                'version' => $version,
                'commit' => is_array($versionData) ? ($versionData['commit'] ?? '') : '',
                'updated_at' => is_array($versionData) ? ($versionData['updated_at'] ?? '') : '',
                'php_version' => PHP_VERSION,
                'database_enabled' => $useDb,
                'database_type' => $useDb ? $db->getDbType() : 'none',
                'features' => [
                    'ad_detection' => true,
                    'multi_thread' => TaskRunner::isMultiThreadAvailable(),
                    'database_cache' => $useDb,
                    'official_replace' => true
                ],
                'timestamp' => time()
            ];
            if ($useDb) {
                $info['stats'] = [
                    'rules' => $db->count('domain_rules', 'status = 1'),
                    'sites' => $db->count('resource_sites', 'status = "active"'),
                    'proxies' => $db->count('proxies', 'status = "active"')
                ];
            }
            sendJsonResponse($info);
            break;

        case 'version':
            $versionData = file_exists(__DIR__ . '/version.php') ? include __DIR__ . '/version.php' : ['version' => '1.0.0'];
            $version = is_array($versionData) ? ($versionData['version'] ?? '1.0.0') : $versionData;
            sendJsonResponse([
                'success' => true,
                'version' => $version,
                'commit' => is_array($versionData) ? ($versionData['commit'] ?? '') : '',
                'updated_at' => is_array($versionData) ? ($versionData['updated_at'] ?? '') : '',
                'version_file' => file_exists(__DIR__ . '/version.php'),
                'php_version' => PHP_VERSION,
                'database_type' => $useDb ? $db->getDbType() : 'none'
            ]);
            break;

        case 'official/list':
            $includePaused = isset($_GET['include_paused']) && $_GET['include_paused'] === '1';
            $sites = $officialMgr->getAllSites($includePaused);
            sendJsonResponse([
                'success' => true,
                'sites' => $sites,
                'total' => count($sites),
                'enabled' => $officialMgr->isEnabled()
            ]);
            break;

        case 'official/platforms':
            $platforms = $officialReplaceMgr->getPlatforms();
            sendJsonResponse([
                'success' => true,
                'platforms' => $platforms,
                'total' => count($platforms)
            ]);
            break;

        case 'parse/list':
        case 'jx/list':
            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            sendJsonResponse([
                'success' => true,
                'message' => '统一视频解析接口',
                'name' => 'Parse API - 统一解析接口',
                'version' => 'v1.0.0',
                'base_url' => $selfUrl . '/mx.php',
                'usage' => [
                    '智能解析' => 'mx.php?action=parse&url=视频链接',
                    '指定类型' => 'mx.php?action=parse&type=xiami&url=视频链接',
                    '获取详情' => 'mx.php?action=parse/info&url=视频链接',
                    '接口列表' => 'mx.php?action=parse/list',
                ],
                'supported_types' => [
                    'parse' => '智能解析（自动判断类型）',
                    'mxjx' => '去广告解析（M3U8 去广告）',
                    'xiami' => '虾米解析（全网 VIP 视频）',
                    'moxi' => '沫兮解析（官方视频替换）',
                    'official' => '官方替换（智能匹配资源站）',
                ]
            ]);
            break;

        case 'parse':
        case 'parse/parse':
        case 'jx':
            $parseUrl = $_GET['url'] ?? $_POST['url'] ?? '';
            $parseType = $_GET['type'] ?? $_POST['type'] ?? 'parse';
            if (empty($parseUrl)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
            }

            $parsedUrl = parse_url($parseUrl);
            $urlHost = $parsedUrl['host'] ?? '';
            $path = $parsedUrl['path'] ?? '';

            $officialDomains = ['v.qq.com', 'iqiyi.com', 'youku.com', 'mgtv.com', 'bilibili.com', 'sohu.com', 'pptv.com'];
            $isOfficialUrl = false;
            foreach ($officialDomains as $domain) {
                if (strpos($urlHost, $domain) !== false) {
                    $isOfficialUrl = true;
                    break;
                }
            }
            $isM3u8Url = (stripos($path, '.m3u8') !== false);

            if ($parseType === 'parse' || $parseType === 'auto' || $parseType === '智能') {
                if ($isM3u8Url) {
                    $parseType = 'mxjx';
                } elseif ($isOfficialUrl) {
                    $parseType = 'xiami';
                } else {
                    $parseType = 'mxjx';
                }
            }

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;

            $playUrl = '';
            $videoName = '';
            $msg = '';
            $code = 200;
            $typeName = '';
            $extra = [];

            switch ($parseType) {
                case 'mxjx':
                case 'adskip':
                case '去广告':
                    $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($parseUrl);
                    $msg = '去广告解析';
                    $typeName = '去广告解析';
                    break;

                case 'xiami':
                case '虾米':
                case '虾米解析':
                    $xiamiResult = parse_internal_xiami($parseUrl);
                    if (!empty($xiamiResult['success'])) {
                        $playUrl = $xiamiResult['play_url'];
                        $videoName = '';
                        $msg = '虾米解析成功';
                        $extra = $xiamiResult;
                    } else {
                        $code = 500;
                        $msg = $xiamiResult['message'] ?? '虾米解析失败';
                    }
                    $typeName = '虾米解析';
                    break;

                case 'moxi':
                case '沫兮':
                case '沫兮解析':
                    $moxiResult = parse_internal_moxi($parseUrl, $selfUrl, $officialReplaceMgr ?? null, $siteManager ?? null);
                    if (!empty($moxiResult['success'])) {
                        $playUrl = $moxiResult['play_url'];
                        $videoName = $moxiResult['video_name'] ?? '';
                        $msg = '沫兮解析成功';
                        $extra = $moxiResult;
                    } else {
                        $code = 500;
                        $msg = $moxiResult['message'] ?? '沫兮解析失败';
                    }
                    $typeName = '沫兮解析';
                    break;

                case 'official':
                case '官替':
                case '官方替换':
                    if (isset($officialReplaceMgr)) {
                        $orResult = $officialReplaceMgr->resolve($parseUrl);
                        if (!empty($orResult['success'])) {
                            $m3u8Url = $orResult['m3u8_url'] ?? '';
                            $playUrl = $selfUrl . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
                            $videoName = $orResult['video_title'] ?? '';
                            $msg = '官方替换成功';
                            $extra = $orResult;
                        } else {
                            $code = 500;
                            $msg = $orResult['message'] ?? '未找到匹配资源';
                        }
                    } else {
                        $code = 500;
                        $msg = '官方替换模块未初始化';
                    }
                    $typeName = '官方替换';
                    break;

                default:
                    $code = 400;
                    $msg = '不支持的解析类型: ' . $parseType;
                    $typeName = '未知类型';
                    break;
            }

            $response = [
                'success' => ($code == 200),
                'code' => $code,
                'message' => $msg,
                'type' => $parseType,
                'type_name' => $typeName,
                'original_url' => $parseUrl,
                'play_url' => $playUrl,
                'video_name' => $videoName,
                'is_official' => $isOfficialUrl,
                'is_m3u8' => $isM3u8Url,
            ];
            if (!empty($extra)) {
                $response['raw'] = $extra;
            }
            sendJsonResponse($response);
            break;

        case 'parse/info':
        case 'jx/info':
            $_GET['action'] = 'parse/parse';
            include __FILE__;
            exit;
            break;

        case 'proxies/list':
            if (!isset($proxyManager)) {
                sendJsonResponse(['success' => false, 'message' => '代理模块未初始化'], 500);
                break;
            }
            $proxies = $proxyManager->getAllProxies();
            $activeProxies = array_filter($proxies, function($p) {
                return ($p['status'] ?? 'active') === 'active';
            });
            sendJsonResponse([
                'success' => true,
                'proxies' => $proxies,
                'total' => count($proxies),
                'active_count' => count($activeProxies)
            ]);
            break;

        case 'signatures/list':
        case 'ad_signatures/list':
            $domain = $_GET['domain'] ?? '';
            $type = $_GET['type'] ?? null;
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
            }
            if (!$useDb) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '数据库未启用，广告特征码功能需要数据库支持',
                    'signatures' => [],
                    'total' => 0
                ]);
                break;
            }
            try {
                $adSig = new DbAdSignature();
                $sigs = $adSig->getByDomain($domain, $type);
                $grouped = $adSig->getGroupedByDomain($domain);
                $stats = $adSig->getStats($domain);
                sendJsonResponse([
                    'success' => true,
                    'domain' => $domain,
                    'total' => count($sigs),
                    'by_type' => array_map(function($items) { return count($items); }, $grouped),
                    'signatures' => array_map(function($sig) {
                        return [
                            'id' => (int)$sig['id'],
                            'type' => $sig['signature_type'],
                            'value' => $sig['signature_value'],
                            'weight' => (int)$sig['weight'],
                            'hit_count' => (int)$sig['hit_count'],
                            'confidence' => (int)$sig['confidence'],
                            'first_seen' => $sig['first_seen'],
                            'last_seen' => $sig['last_seen']
                        ];
                    }, $sigs),
                    'grouped' => $grouped,
                    'stats' => $stats
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '获取广告特征码失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'signatures/add':
        case 'ad_signatures/add':
            $domain = $_GET['domain'] ?? $_POST['domain'] ?? '';
            $type = $_GET['type'] ?? $_POST['type'] ?? '';
            $value = $_GET['value'] ?? $_POST['value'] ?? '';
            $weight = isset($_GET['weight']) ? (int)$_GET['weight'] : (isset($_POST['weight']) ? (int)$_POST['weight'] : 30);
            $confidence = isset($_GET['confidence']) ? (int)$_GET['confidence'] : (isset($_POST['confidence']) ? (int)$_POST['confidence'] : 50);
            if (empty($domain) || empty($type) || empty($value)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain/type/value 参数'], 400);
            }
            if (!$useDb) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '数据库未启用，广告特征码功能需要数据库支持'
                ], 500);
                break;
            }
            try {
                $adSig = new DbAdSignature();
                $id = $adSig->addSignature($domain, $type, $value, $weight, $confidence);
                sendJsonResponse([
                    'success' => $id !== false,
                    'message' => $id !== false ? '特征码添加成功' : '特征码添加失败',
                    'id' => $id
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '添加广告特征码失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'signatures/delete':
        case 'ad_signatures/delete':
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            if (empty($id)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 id 参数'], 400);
            }
            if (!$useDb) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '数据库未启用，广告特征码功能需要数据库支持'
                ], 500);
                break;
            }
            try {
                $db = Database::getInstance();
                $result = $db->execute('UPDATE ad_signatures SET status = 0 WHERE id = ?', [$id]);
                sendJsonResponse([
                    'success' => $result !== false,
                    'message' => $result ? '特征码删除成功' : '特征码删除失败'
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '删除广告特征码失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'signatures/stats':
        case 'ad_signatures/stats':
            $domain = $_GET['domain'] ?? null;
            if (!$useDb) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '数据库未启用，广告特征码功能需要数据库支持',
                    'stats' => ['total' => 0, 'by_type' => []]
                ]);
                break;
            }
            try {
                $adSig = new DbAdSignature();
                $stats = $adSig->getStats($domain);
                sendJsonResponse([
                    'success' => true,
                    'domain' => $domain,
                    'stats' => $stats
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '获取统计失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'signatures/clean':
        case 'ad_signatures/clean':
            $minConfidence = isset($_GET['min_confidence']) ? (int)$_GET['min_confidence'] : 30;
            if (!$useDb) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '数据库未启用，广告特征码功能需要数据库支持'
                ], 500);
                break;
            }
            try {
                $adSig = new DbAdSignature();
                $count = $adSig->cleanLowConfidence($minConfidence);
                sendJsonResponse([
                    'success' => true,
                    'message' => '清理完成',
                    'cleaned_count' => $count,
                    'min_confidence' => $minConfidence
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '清理失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/smart_process':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            $mode = $_GET['mode'] ?? $_POST['mode'] ?? 'full';
            $autoSave = isset($_GET['auto_save']) ? ($_GET['auto_save'] === '1' || $_GET['auto_save'] === 'true') : false;

            try {
                require_once __DIR__ . '/gz/AiSmartProcessor.php';
                $processor = new AiSmartProcessor();

                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
                if ($domain) {
                    $processor->setDomain($domain);
                }

                if ($mode === 'analyze') {
                    $result = $processor->smartAnalyze($url);
                } else {
                    $result = $processor->processUrl($url);
                }

                if ($autoSave && !empty($domain) && !empty($result['auto_rules'])) {
                    require_once __DIR__ . '/gz/DomainRuleManager.php';
                    $ruleManager = new DomainRuleManager();
                    $ruleManager->saveRules($domain, $result['auto_rules']);
                    $result['rules_saved'] = true;
                }

                sendJsonResponse([
                    'success' => $result['success'],
                    'message' => $result['message'] ?? ($result['success'] ? '智能处理完成' : '处理失败'),
                    'data' => $result
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '智能处理失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/pro_detect':
            $url = $_GET['url'] ?? $_POST['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            try {
                require_once __DIR__ . '/gz/AiSmartProcessor.php';
                $processor = new AiSmartProcessor([
                    'zscore_threshold' => floatval($_GET['zscore'] ?? 2.0),
                    'confidence_threshold' => intval($_GET['confidence'] ?? 55),
                ]);
                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
                if ($domain) {
                    $processor->setDomain($domain);
                }
                $result = $processor->smartAnalyze($url);
                sendJsonResponse([
                    'success' => $result['success'],
                    'message' => $result['message'] ?? ($result['success'] ? '专业检测完成' : '检测失败'),
                    'data' => $result
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '专业检测失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/skip':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            $safeguard = isset($_GET['safeguard']) ? ($_GET['safeguard'] === '1' || $_GET['safeguard'] === 'true') : true;
            $autoLearn = isset($_GET['auto_learn']) ? ($_GET['auto_learn'] === '1' || $_GET['auto_learn'] === 'true') : true;
            $deepAnalysis = isset($_GET['deep_analysis']) ? ($_GET['deep_analysis'] === '1' || $_GET['deep_analysis'] === 'true') : false;
            
            $startTime = microtime(true);
            try {
                require_once __DIR__ . '/src/M3U8AdSkipper.php';
                require_once __DIR__ . '/src/M3U8Parser.php';
                require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';
                require_once __DIR__ . '/gz/DomainRuleManager.php';
                $skipper = new M3U8AdSkipper();
                $ruleManager = new DomainRuleManager();
                
                if ($safeguard) {
                    $result = $skipper->processWithSafeguard($url);
                } else {
                    $result = $skipper->process($url);
                }
                
                $processTime = round((microtime(true) - $startTime) * 1000, 2);
                
                $adSegments = [];
                $contentSegments = [];
                
                $removedSegments = $result['filtered']['removedSegments'] ?? [];
                $keptSegments = $result['filtered']['segments'] ?? [];
                $allSegments = $result['original']['segments'] ?? [];
                
                foreach ($removedSegments as $seg) {
                    $adSegments[] = [
                        'uri' => $seg['uri'] ?? '',
                        'duration' => $seg['duration'] ?? 0,
                        'mediaSequence' => $seg['mediaSequence'] ?? null,
                        'isAd' => true,
                        'adReasons' => $seg['adInfo']['matchedRules'] ?? []
                    ];
                }
                
                foreach ($keptSegments as $seg) {
                    $contentSegments[] = [
                        'uri' => $seg['uri'] ?? '',
                        'duration' => $seg['duration'] ?? 0,
                        'mediaSequence' => $seg['mediaSequence'] ?? null,
                        'isAd' => false
                    ];
                }

                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';

                $analysisData = [];
                $adClusterDetails = [];
                $discontinuityRegexRules = [];
                if (!empty($allSegments)) {
                    $engine = new EnhancedAdRuleEngine([
                        'checkDiscontinuity' => true,
                        'checkRepetitiveDuration' => true
                    ]);
                    if ($domain) {
                        $engine->setDomain($domain);
                    }
                    $analysisData = $engine->analyzeAllSegments($allSegments);
                    $adClusterDetails = $ruleManager->analyzeAdClustersDetail($analysisData, $allSegments);
                    $discontinuityRegexRules = $ruleManager->generateDiscontinuityRegexRules($analysisData, $allSegments);
                }
                
                sendJsonResponse([
                    'success' => true,
                    'message' => 'AI去广告处理完成',
                    'data' => [
                        'original_url' => $url,
                        'domain' => $domain,
                        'process_time' => $processTime . 'ms',
                        'safeguard_enabled' => $safeguard,
                        'deep_analysis' => $deepAnalysis,
                        'safeguard_triggered' => $result['safeguardTriggered'] ?? false,
                        'safeguard_reason' => $result['safeguardReason'] ?? '',
                        'safeguard_method' => $result['safeguardMethod'] ?? '',
                        'stats' => [
                            'total_segments' => $result['stats']['totalSegments'] ?? 0,
                            'ad_segments' => $result['stats']['removedSegments'] ?? 0,
                            'kept_segments' => $result['stats']['keptSegments'] ?? 0,
                            'original_duration' => $result['stats']['originalDuration'] ?? 0,
                            'filtered_duration' => $result['stats']['filteredDuration'] ?? 0,
                            'saved_duration' => $result['stats']['savedDuration'] ?? 0,
                            'ad_percentage' => $result['stats']['adPercentage'] ?? 0,
                            'discontinuity_count' => $analysisData['discontinuityCount'] ?? 0,
                            'ad_cluster_count' => count($adClusterDetails)
                        ],
                        'ad_segments' => array_slice($adSegments, 0, 100),
                        'content_segments' => array_slice($contentSegments, 0, 100),
                        'ad_segment_count' => count($adSegments),
                        'content_segment_count' => count($contentSegments),
                        'has_more_segments' => count($adSegments) > 100 || count($contentSegments) > 100,
                        'ad_clusters' => $adClusterDetails,
                        'discontinuity_regex_rules' => $discontinuityRegexRules
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '处理失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/insert_detect':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            $checkOpening = isset($_GET['opening']) ? ($_GET['opening'] === '1' || $_GET['opening'] === 'true') : true;
            $checkEnding = isset($_GET['ending']) ? ($_GET['ending'] === '1' || $_GET['ending'] === 'true') : true;
            $checkMiddle = isset($_GET['middle']) ? ($_GET['middle'] === '1' || $_GET['middle'] === 'true') : true;
            
            $startTime = microtime(true);
            try {
                require_once __DIR__ . '/src/M3U8Parser.php';
                require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';
                
                $parser = new M3U8Parser();
                $playlist = $parser->parse($url);
                $segments = $playlist['segments'] ?? [];
                
                $insertions = [];
                $openingCount = 0;
                $endingCount = 0;
                $middleCount = 0;
                
                if (count($segments) > 0) {
                    $durations = array_column($segments, 'duration');
                    $avgDuration = count($durations) > 0 ? array_sum($durations) / count($durations) : 10;
                    
                    $adClusters = [];
                    $currentCluster = null;
                    
                    foreach ($segments as $i => $seg) {
                        $isAdLike = false;
                        $reason = '';
                        
                        if ($seg['duration'] < $avgDuration * 0.5 && $seg['duration'] > 0.5) {
                            $isAdLike = true;
                            $reason = '时长短';
                        }
                        if (!empty($seg['discontinuity'])) {
                            $isAdLike = true;
                            $reason = $reason ? $reason . '+不连续标记' : '不连续标记';
                        }
                        
                        if ($isAdLike) {
                            if ($currentCluster === null) {
                                $currentCluster = [
                                    'startIndex' => $i,
                                    'endIndex' => $i,
                                    'duration' => $seg['duration'],
                                    'reason' => $reason,
                                    'type' => 'middle'
                                ];
                            } else {
                                $currentCluster['endIndex'] = $i;
                                $currentCluster['duration'] += $seg['duration'];
                            }
                        } else {
                            if ($currentCluster !== null) {
                                $adClusters[] = $currentCluster;
                                $currentCluster = null;
                            }
                        }
                    }
                    if ($currentCluster !== null) {
                        $adClusters[] = $currentCluster;
                    }
                    
                    foreach ($adClusters as $cluster) {
                        $type = 'middle';
                        if ($checkOpening && $cluster['startIndex'] <= 3) {
                            $type = 'opening';
                            $openingCount++;
                        } elseif ($checkEnding && $cluster['endIndex'] >= count($segments) - 4) {
                            $type = 'ending';
                            $endingCount++;
                        } elseif ($checkMiddle) {
                            $middleCount++;
                        } else {
                            continue;
                        }
                        $cluster['type'] = $type;
                        $insertions[] = $cluster;
                    }
                }
                
                $processTime = round((microtime(true) - $startTime) * 1000, 2);
                
                sendJsonResponse([
                    'success' => true,
                    'message' => '插播检测完成',
                    'data' => [
                        'original_url' => $url,
                        'process_time' => $processTime . 'ms',
                        'total_segments' => count($segments),
                        'opening_count' => $openingCount,
                        'ending_count' => $endingCount,
                        'middle_count' => $middleCount,
                        'insertions' => $insertions
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '检测失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/watermark':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            
            $watermarkParams = ['wsip', 'wsh', 'wsTime', 'sign', 'wd', 'chyuan', 'x-play', 'k_ft', 'k_id'];
            
            $removedParams = [];
            $originalUrl = $url;
            $processedUrl = $url;
            
            try {
                $urlParts = parse_url($url);
                if ($urlParts && isset($urlParts['query'])) {
                    parse_str($urlParts['query'], $queryParams);
                    foreach ($watermarkParams as $param) {
                        if (isset($queryParams[$param])) {
                            $removedParams[] = [
                                'name' => $param,
                                'value' => $queryParams[$param]
                            ];
                            unset($queryParams[$param]);
                        }
                    }
                    
                    $newQuery = http_build_query($queryParams);
                    $scheme = $urlParts['scheme'] ?? 'https';
                    $host = $urlParts['host'] ?? '';
                    $path = $urlParts['path'] ?? '';
                    $processedUrl = $scheme . '://' . $host . $path;
                    if ($newQuery) {
                        $processedUrl .= '?' . $newQuery;
                    }
                }
                
                sendJsonResponse([
                    'success' => true,
                    'message' => '水印处理完成',
                    'data' => [
                        'original_url' => $originalUrl,
                        'processed_url' => $processedUrl,
                        'removed_params' => $removedParams,
                        'removed_count' => count($removedParams),
                        'watermark_params_lib' => $watermarkParams
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '处理失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/md5_analyze':
            $url = $_GET['url'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            $saveToDb = isset($_GET['save']) ? ($_GET['save'] === '1' || $_GET['save'] === 'true') : false;
            $fastMode = isset($_GET['fast']) ? ($_GET['fast'] === '1' || $_GET['fast'] === 'true') : true;
            $sampleMode = $_GET['sample'] ?? 'auto';
            $maxCount = isset($_GET['count']) ? intval($_GET['count']) : ($fastMode ? 30 : 60);
            $concurrency = isset($_GET['concurrency']) ? intval($_GET['concurrency']) : ($fastMode ? 15 : 10);
            
            $startTime = microtime(true);
            try {
                require_once __DIR__ . '/src/M3U8Parser.php';
                require_once __DIR__ . '/src/TsMd5Analyzer.php';
                
                $parser = new M3U8Parser();
                $playlist = $parser->parse($url);
                $segments = $playlist['segments'] ?? [];
                
                $parsedUrl = parse_url($url);
                $domain = $parsedUrl['host'] ?? '';
                
                $analyzer = new TsMd5Analyzer($domain);
                $analyzer->setConcurrency($concurrency);
                
                if ($fastMode) {
                    $analyzer->setFastMode(true);
                }
                
                $result = $analyzer->analyzeMd5Signatures($segments, $url, [
                    'max_count' => $maxCount,
                    'sample_mode' => $sampleMode
                ]);
                
                $savedCount = 0;
                if ($saveToDb && !empty($domain) && !empty($result['ad_candidates'])) {
                    $savedCount = $analyzer->saveMd5Signatures($domain, $result['ad_candidates']);
                }
                
                $processTime = round((microtime(true) - $startTime) * 1000, 2);
                
                sendJsonResponse([
                    'success' => true,
                    'message' => 'MD5特征码分析完成',
                    'data' => [
                        'original_url' => $url,
                        'domain' => $domain,
                        'process_time' => $processTime . 'ms',
                        'fast_mode' => $fastMode,
                        'sample_mode' => $result['sample_mode'],
                        'concurrency' => $concurrency,
                        'total_segments' => count($segments),
                        'analyzed_segments' => $result['total_analyzed'],
                        'unique_md5' => $result['unique_md5'],
                        'ad_candidates' => $result['ad_candidates'],
                        'ad_candidate_count' => count($result['ad_candidates']),
                        'content_candidates' => array_slice($result['content_candidates'], 0, 20),
                        'content_candidate_count' => count($result['content_candidates']),
                        'md5_details' => array_slice($result['md5_details'], 0, 50),
                        'saved_to_db' => $saveToDb,
                        'saved_count' => $savedCount
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '分析失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/md5_signatures':
            $domain = $_GET['domain'] ?? '';
            if (empty($domain)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 domain 参数'], 400);
                break;
            }
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
            
            try {
                require_once __DIR__ . '/src/TsMd5Analyzer.php';
                
                $analyzer = new TsMd5Analyzer($domain);
                $signatures = $analyzer->getMd5Signatures($domain, $limit);
                
                sendJsonResponse([
                    'success' => true,
                    'message' => '获取成功',
                    'data' => [
                        'domain' => $domain,
                        'total' => count($signatures),
                        'signatures' => array_map(function($sig) {
                            return [
                                'id' => (int)$sig['id'],
                                'md5' => $sig['md5'],
                                'avg_duration' => (float)$sig['avg_duration'],
                                'ad_type' => $sig['ad_type'],
                                'weight' => (int)$sig['weight'],
                                'hit_count' => (int)$sig['hit_count'],
                                'confidence' => (int)$sig['confidence'],
                                'first_seen' => $sig['first_seen'],
                                'last_seen' => $sig['last_seen']
                            ];
                        }, $signatures)
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '获取失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        case 'ai/md5_detect':
            $url = $_GET['url'] ?? '';
            $domain = $_GET['domain'] ?? '';
            if (empty($url)) {
                sendJsonResponse(['success' => false, 'message' => '缺少 url 参数'], 400);
                break;
            }
            
            $startTime = microtime(true);
            try {
                require_once __DIR__ . '/src/M3U8Parser.php';
                require_once __DIR__ . '/src/TsMd5Analyzer.php';
                require_once __DIR__ . '/src/M3U8AdSkipper.php';
                
                if (empty($domain)) {
                    $parsedUrl = parse_url($url);
                    $domain = $parsedUrl['host'] ?? '';
                }
                
                $parser = new M3U8Parser();
                $playlist = $parser->parse($url);
                $segments = $playlist['segments'] ?? [];
                
                $analyzer = new TsMd5Analyzer($domain);
                $analyzer->setFastMode(true);
                $md5Result = $analyzer->analyzeMd5Signatures($segments, $url);
                
                $adMd5Set = [];
                foreach ($md5Result['ad_candidates'] as $cand) {
                    $adMd5Set[$cand['md5']] = true;
                }
                
                $skipper = new M3U8AdSkipper();
                $baseResult = $skipper->processWithSafeguard($url);
                
                $adSegments = [];
                $contentSegments = [];
                $md5Map = [];
                foreach ($md5Result['md5_details'] as $detail) {
                    $md5Map[$detail['uri']] = $detail['md5'];
                }
                
                $removedSegments = $baseResult['filtered']['removedSegments'] ?? [];
                $keptSegments = $baseResult['filtered']['segments'] ?? [];
                
                foreach ($removedSegments as $seg) {
                    $uri = $seg['uri'] ?? '';
                    $md5 = $md5Map[$uri] ?? null;
                    $adSegments[] = [
                        'uri' => $uri,
                        'duration' => $seg['duration'] ?? 0,
                        'mediaSequence' => $seg['mediaSequence'] ?? null,
                        'isAd' => true,
                        'md5' => $md5,
                        'md5_detected' => isset($adMd5Set[$md5]),
                        'adReasons' => $seg['adInfo']['matchedRules'] ?? []
                    ];
                }
                
                foreach ($keptSegments as $seg) {
                    $uri = $seg['uri'] ?? '';
                    $md5 = $md5Map[$uri] ?? null;
                    $contentSegments[] = [
                        'uri' => $uri,
                        'duration' => $seg['duration'] ?? 0,
                        'mediaSequence' => $seg['mediaSequence'] ?? null,
                        'isAd' => false,
                        'md5' => $md5
                    ];
                }
                
                $processTime = round((microtime(true) - $startTime) * 1000, 2);
                
                sendJsonResponse([
                    'success' => true,
                    'message' => 'MD5智能去广告完成',
                    'data' => [
                        'original_url' => $url,
                        'domain' => $domain,
                        'process_time' => $processTime . 'ms',
                        'safeguard_triggered' => $baseResult['safeguardTriggered'] ?? false,
                        'safeguard_reason' => $baseResult['safeguardReason'] ?? '',
                        'md5_analysis' => [
                            'analyzed_segments' => $md5Result['total_analyzed'],
                            'unique_md5' => $md5Result['unique_md5'],
                            'ad_candidate_count' => count($md5Result['ad_candidates']),
                            'ad_candidates' => $md5Result['ad_candidates']
                        ],
                        'stats' => [
                            'total_segments' => $baseResult['stats']['totalSegments'] ?? 0,
                            'ad_segments' => $baseResult['stats']['removedSegments'] ?? 0,
                            'kept_segments' => $baseResult['stats']['keptSegments'] ?? 0,
                            'original_duration' => $baseResult['stats']['originalDuration'] ?? 0,
                            'filtered_duration' => $baseResult['stats']['filteredDuration'] ?? 0,
                            'saved_duration' => $baseResult['stats']['savedDuration'] ?? 0,
                            'ad_percentage' => $baseResult['stats']['adPercentage'] ?? 0
                        ],
                        'ad_segments' => array_slice($adSegments, 0, 100),
                        'content_segments' => array_slice($contentSegments, 0, 100),
                        'ad_segment_count' => count($adSegments),
                        'content_segment_count' => count($contentSegments)
                    ]
                ]);
            } catch (Throwable $e) {
                sendJsonResponse([
                    'success' => false,
                    'message' => '处理失败: ' . $e->getMessage()
                ], 500);
            }
            break;

        default:
            sendJsonResponse([
                'success' => false,
                'message' => '未知操作',
                'available_actions' => [
                    'ai/skip' => 'AI自动去广告',
                    'ai/insert_detect' => 'AI插播识别检测',
                    'ai/watermark' => 'AI水印处理',
                    'ai/md5_analyze' => 'AI-MD5特征码分析',
                    'ai/md5_signatures' => 'AI-MD5特征码列表',
                    'ai/md5_detect' => 'AI-MD5智能去广告',
                    'analyze' => '分析视频广告',
                    'rules/list' => '获取所有域名规则',
                    'rules/get' => '获取指定域名规则',
                    'rules/save' => '保存域名规则',
                    'rules/delete' => '删除域名规则',
                    'rules/generate' => '根据视频自动生成规则',
                    'sites/list' => '获取资源站列表',
                    'sites/get' => '获取单个资源站',
                    'sites/add' => '添加资源站',
                    'sites/update' => '更新资源站',
                    'sites/delete' => '删除资源站',
                    'sites/fetch_videos' => '从资源站获取视频列表',
                    'sites/search' => '搜索指定资源站视频',
                    'sites/search_all' => '搜索所有资源站视频',
                    'sites/search_and_learn' => '搜索并学习（搜索影视学习一体化）',
                    'sites/learn_video' => '从指定视频URL学习规则',
                    'sites/auto_learn/config' => '获取自动学习配置',
                    'sites/auto_learn/config/save' => '保存自动学习配置',
                    'sites/auto_learn/run' => '执行自动学习',
                    'sites/auto_learn/status' => '自动学习状态',
                    'official_replace/config' => '官替配置',
                    'official_replace/config/save' => '保存官替配置',
                    'official_replace/platforms' => '官替平台列表',
                    'official_replace/platform/add' => '添加官替平台',
                    'official_replace/platform/update' => '更新官替平台',
                    'official_replace/platform/delete' => '删除官替平台',
                    'official_replace/resolve' => '官替解析-完整结果',
                    'official_replace/info' => '官替解析-精简信息',
                    'moxi' => '沫兮API接口',
                    'moxi/api' => '沫兮API接口(别名)',
                    'skip' => '去广告接口',
                    'mxjx' => '去广告m3u8输出',
                    'parse/list' => '统一解析接口列表',
                    'parse' => '统一解析视频（智能解析）',
                    'parse/info' => '统一解析详情',
                    'update/version' => '获取当前版本',
                    'update/check' => '检查更新',
                    'update/integrity' => '完整性检查',
                    'auth/info' => '授权信息',
                    'auth/validate' => '验证授权',
                    'auth/config/get' => '获取授权配置',
                    'auth/config/save' => '保存授权配置',
                    'auth/set' => '设置授权码',
                    'auth/generate' => '生成授权码',
                    'signatures/list' => '获取指定域名广告特征码列表',
                    'signatures/add' => '添加广告特征码',
                    'signatures/delete' => '删除广告特征码',
                    'signatures/stats' => '广告特征码统计',
                    'signatures/clean' => '清理低置信度特征码'
                ]
            ], 400);
            break;
    }
} catch (Throwable $e) {
    sendJsonResponse([
        'success' => false,
        'message' => $e->getMessage(),
        'error_detail' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ], 500);
}
