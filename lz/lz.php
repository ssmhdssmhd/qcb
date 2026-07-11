<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

$memoryLimit = @ini_get('memory_limit');
if (return_bytes_lz($memoryLimit) < 256 * 1024 * 1024) {
    @ini_set('memory_limit', '256M');
}

function return_bytes_lz($val) {
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

function sendLzJson($data, $code = 200) {
    http_response_code($code);
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

$rootDir = dirname(__DIR__);

$requiredFiles = [
    $rootDir . '/src/M3U8AdSkipper.php',
    $rootDir . '/src/M3U8Parser.php',
    $rootDir . '/src/CacheManager.php',
    $rootDir . '/gz/EnhancedAdRuleEngine.php',
    $rootDir . '/gz/DomainRuleManager.php',
];

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        sendLzJson([
            'code' => 500,
            'msg' => '文件缺失: ' . basename($file),
            'data' => null
        ], 500);
    }
    require_once $file;
}

$useDb = false;
$dbConfigFile = $rootDir . '/db/db_config.php';
$adSignature = null;
$dbRuleManager = null;

if (file_exists($dbConfigFile)) {
    require_once $rootDir . '/db/autoload.php';
    try {
        $db = Database::getInstance();
        if ($db->tableExists('sys_config')) {
            $useDb = true;
            $adSignature = new DbAdSignature();
            $dbRuleManager = new DbDomainRuleManager();
        }
    } catch (Throwable $e) {
        $useDb = false;
    }
}

$url = $_GET['url'] ?? $_POST['url'] ?? '';
$action = $_GET['action'] ?? 'skip';
$format = $_GET['format'] ?? 'json';
$site = $_GET['site'] ?? '';

if (empty($url) && $action !== 'sites' && $action !== 'signatures' && $action !== 'help') {
    sendLzJson([
        'code' => 400,
        'msg' => '缺少 url 参数',
        'data' => null,
        'example' => '/lz/lz.php?url=https://example.com/playlist.m3u8',
        'usage' => [
            '去广告' => '/lz/lz.php?url=视频链接',
            '获取特征码' => '/lz/lz.php?action=signatures&domain=域名',
            '资源站列表' => '/lz/lz.php?action=sites',
            '帮助' => '/lz/lz.php?action=help'
        ]
    ], 400);
}

function resolveMasterPlaylistLz($url) {
    $parser = new M3U8Parser();
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
                    return $baseUrl . $pathDir . '/' . ltrim($firstVariant, '/');
                }
            }
        }
    } catch (Exception $e) {
    }
    return $url;
}

try {
    switch ($action) {
        case 'help':
            sendLzJson([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'name' => 'LZ去广告接口',
                    'version' => '1.0.0',
                    'description' => '简洁易用的M3U8去广告接口，支持广告特征码查询',
                    'endpoints' => [
                        [
                            'path' => '/lz/lz.php?url=视频链接',
                            'method' => 'GET',
                            'description' => '去广告解析，返回JSON格式结果',
                            'params' => [
                                'url' => 'M3U8视频地址（必填）',
                                'format' => '输出格式：json/m3u8（默认json）',
                                'site' => '指定资源站（可选）'
                            ]
                        ],
                        [
                            'path' => '/lz/lz.php?action=signatures&domain=域名',
                            'method' => 'GET',
                            'description' => '获取指定域名的TS广告特征码',
                            'params' => [
                                'domain' => '域名（必填）',
                                'type' => '特征码类型：duration/discontinuity/sequence/filename（可选）'
                            ]
                        ],
                        [
                            'path' => '/lz/lz.php?action=sites',
                            'method' => 'GET',
                            'description' => '获取支持的资源站列表'
                        ],
                        [
                            'path' => '/lz/lz.php?action=help',
                            'method' => 'GET',
                            'description' => '获取帮助信息'
                        ]
                    ]
                ]
            ]);
            break;

        case 'sites':
            $sitesConfigFile = $rootDir . '/gz/sites_config.php';
            $sites = [];
            if (file_exists($sitesConfigFile)) {
                $config = require $sitesConfigFile;
                foreach ($config['sites'] ?? [] as $s) {
                    if (($s['status'] ?? '') === 'active') {
                        $sites[] = [
                            'name' => $s['name'],
                            'site_url' => $s['site_url'] ?? '',
                            'api_url' => $s['api_url'] ?? '',
                            'type' => $s['type'] ?? 'maccms',
                            'note' => $s['note'] ?? '',
                            'priority' => $s['priority'] ?? 99
                        ];
                    }
                }
            }
            sendLzJson([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'total' => count($sites),
                    'sites' => $sites
                ]
            ]);
            break;

        case 'signatures':
            $domain = $_GET['domain'] ?? '';
            $type = $_GET['type'] ?? null;
            if (empty($domain)) {
                sendLzJson([
                    'code' => 400,
                    'msg' => '缺少 domain 参数',
                    'data' => null
                ], 400);
            }

            $signatures = [];
            if ($useDb && $adSignature) {
                $sigs = $adSignature->getByDomain($domain, $type);
                foreach ($sigs as $sig) {
                    $signatures[] = [
                        'id' => (int)$sig['id'],
                        'type' => $sig['signature_type'],
                        'value' => $sig['signature_value'],
                        'weight' => (int)$sig['weight'],
                        'hit_count' => (int)$sig['hit_count'],
                        'confidence' => (int)$sig['confidence'],
                        'first_seen' => $sig['first_seen'],
                        'last_seen' => $sig['last_seen']
                    ];
                }
            }

            $domainRules = null;
            if ($useDb && $dbRuleManager) {
                $domainRules = $dbRuleManager->getRules($domain);
            } else {
                $ruleMgr = new DomainRuleManager();
                $domainRules = $ruleMgr->getRules($domain);
            }

            sendLzJson([
                'code' => 200,
                'msg' => '成功',
                'data' => [
                    'domain' => $domain,
                    'has_rules' => $domainRules !== null,
                    'learn_count' => $domainRules['learn_count'] ?? 0,
                    'confidence_score' => $domainRules['confidence_score'] ?? 0,
                    'signature_count' => count($signatures),
                    'signatures' => $signatures,
                    'rules' => $domainRules ? [
                        'duration_rules' => $domainRules['duration_rules'] ?? [],
                        'discontinuity_rules' => $domainRules['discontinuity_rules'] ?? [],
                        'sequence_jump_rules' => $domainRules['sequence_jump_rules'] ?? [],
                        'filename_patterns' => $domainRules['filename_patterns'] ?? [],
                        'insertion_patterns' => $domainRules['insertion_patterns'] ?? []
                    ] : null
                ]
            ]);
            break;

        case 'skip':
        default:
            $startTime = microtime(true);
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $mediaUrl = resolveMasterPlaylistLz($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
                $url = $mediaUrl;
            }

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

            $result = $skipper->processWithSafeguard($url);
            $stats = $result['stats'] ?? [];
            $processTime = round((microtime(true) - $startTime) * 1000);

            $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $basePath = dirname($requestUri);
            $basePath = $basePath === '/' ? '' : $basePath;
            $selfUrl = $scheme . '://' . $host . $basePath;
            $m3u8Url = $selfUrl . '/lz.php?action=m3u8&url=' . urlencode($url);

            $adSegments = [];
            foreach ($result['filtered']['removedSegments'] ?? [] as $s) {
                $adSegments[] = [
                    'uri' => $s['uri'],
                    'duration' => (float)$s['duration'],
                    'title' => $s['title'] ?? '',
                    'matched_rules' => array_map(function($r) {
                        return [
                            'name' => $r['name'] ?? '',
                            'weight' => $r['weight'] ?? 0,
                            'category' => $r['category'] ?? ''
                        ];
                    }, $s['adInfo']['matchedRules'] ?? [])
                ];
            }

            $contentSegments = [];
            foreach ($result['filtered']['segments'] ?? [] as $s) {
                $contentSegments[] = [
                    'uri' => $s['uri'],
                    'duration' => (float)$s['duration'],
                    'title' => $s['title'] ?? ''
                ];
            }

            if ($format === 'm3u8') {
                header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
                header('Content-Disposition: inline; filename="playlist.m3u8"');
                header('X-Process-Time: ' . $processTime . 'ms');
                ob_clean();
                echo $result['output'];
                exit;
            }

            sendLzJson([
                'code' => 200,
                'msg' => '解析成功',
                'data' => [
                    'original_url' => $_GET['url'] ?? '',
                    'media_url' => $url,
                    'domain' => $domain,
                    'process_time' => $processTime . 'ms',
                    'safeguard_triggered' => !empty($result['safeguardTriggered']),
                    'safeguard_reason' => $result['safeguardReason'] ?? '',
                    'safeguard_method' => $result['safeguardMethod'] ?? '',
                    'm3u8_url' => $m3u8Url,
                    'stats' => [
                        'total_segments' => (int)($stats['totalSegments'] ?? 0),
                        'ad_segments' => (int)($stats['adSegments'] ?? $stats['removedSegments'] ?? 0),
                        'kept_segments' => (int)($stats['keptSegments'] ?? 0),
                        'original_duration' => (float)($stats['originalDuration'] ?? 0),
                        'filtered_duration' => (float)($stats['filteredDuration'] ?? 0),
                        'saved_duration' => (float)($stats['savedDuration'] ?? 0),
                        'ad_percentage' => (float)($stats['adPercentage'] ?? 0)
                    ],
                    'ad_segments' => array_slice($adSegments, 0, 100),
                    'content_segments' => array_slice($contentSegments, 0, 100),
                    'ad_segment_count' => count($adSegments),
                    'content_segment_count' => count($contentSegments),
                    'has_more_segments' => count($adSegments) > 100 || count($contentSegments) > 100
                ]
            ]);
            break;

        case 'm3u8':
            $parsedUrl = parse_url($url);
            $domain = $parsedUrl['host'] ?? '';

            $mediaUrl = resolveMasterPlaylistLz($url);
            if ($mediaUrl !== $url) {
                $parsedUrl = parse_url($mediaUrl);
                $domain = $parsedUrl['host'] ?? '';
                $url = $mediaUrl;
            }

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

            $result = $skipper->processWithSafeguard($url);

            $isRemote = strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
            $newM3U8Content = $result['output'];

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

            header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
            header('Content-Disposition: inline; filename="playlist.m3u8"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            ob_clean();
            echo $newM3U8Content;
            exit;
            break;
    }

} catch (Throwable $e) {
    sendLzJson([
        'code' => 500,
        'msg' => '处理失败: ' . $e->getMessage(),
        'data' => null,
        'error' => [
            'file' => basename($e->getFile()),
            'line' => $e->getLine()
        ]
    ], 500);
}
