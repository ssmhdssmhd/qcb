<?php

if (function_exists('ini_set')) {
    @ini_set('display_errors', '0');
    @ini_set('log_errors', '1');
    @ini_set('html_errors', '0');
}
@error_reporting(E_ALL);

$isCli = (php_sapi_name() === 'cli');
$logDate = date('Ymd');
$logDir = __DIR__ . '/cache';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/app_error_cctv_' . $logDate . '.log';
@ini_set('error_log', $logFile);

function cctv_load_config() {
    static $config = null;
    if ($config !== null) {
        return $config;
    }
    $configFile = __DIR__ . '/xt/config.php';
    if (file_exists($configFile)) {
        $cfg = require $configFile;
        if (is_array($cfg)) {
            $config = $cfg;
            return $config;
        }
    }
    $config = [];
    return $config;
}

function cctv_send_cors() {
    if (!headers_sent()) {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Range');
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
}

function cctv_json_response($data, $code = 200) {
    cctv_send_cors();
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cctv_text_response($text, $code = 200, $contentType = 'text/plain; charset=utf-8') {
    cctv_send_cors();
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: ' . $contentType);
        header('Cache-Control: no-cache, must-revalidate');
    }
    echo $text;
    exit;
}

function cctv_m3u_response($content, $filename = 'playlist.m3u') {
    cctv_send_cors();
    if (!headers_sent()) {
        http_response_code(200);
        header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="' . $filename . '"');
    }
    echo $content;
    exit;
}

function cctv_fetch_and_build($force = false) {
    $config = cctv_load_config();

    $mgr = new CctvSourceManager();
    $verifier = new CctvSourceVerifier();
    $generator = new CctvPlaylistGenerator();

    $verifyConcurrent = isset($config['cctv_live']['verify_concurrent'])
        ? intval($config['cctv_live']['verify_concurrent'])
        : 8;
    $filterOnly = isset($config['cctv_live']['filter_cctv_only'])
        ? (bool)$config['cctv_live']['filter_cctv_only']
        : true;

    $startTime = microtime(true);
    $errors = [];

    try {
        $fetched = $mgr->fetchSources($force);
    } catch (Throwable $e) {
        $fetched = [];
        $errors[] = 'fetchSources: ' . $e->getMessage();
    }

    try {
        $filtered = $mgr->filterByCategory($fetched, $filterOnly);
    } catch (Throwable $e) {
        $filtered = is_array($fetched) ? $fetched : [];
        $errors[] = 'filterByCategory: ' . $e->getMessage();
    }

    $verified = $filtered;
    $verifyBeforeSave = isset($config['cctv_live']['verify_before_save'])
        ? (bool)$config['cctv_live']['verify_before_save']
        : true;
    if ($verifyBeforeSave) {
        try {
            $verified = $verifier->verifyBatch($filtered, $verifyConcurrent);
        } catch (Throwable $e) {
            $errors[] = 'verifyBatch: ' . $e->getMessage();
        }
    }

    try {
        $grouped = $verifier->pickBestByChannel($verified);
    } catch (Throwable $e) {
        $grouped = $verified;
        $errors[] = 'pickBestByChannel: ' . $e->getMessage();
    }

    $cctvCacheDir = __DIR__ . '/cache/cctv';
    if (!is_dir($cctvCacheDir)) {
        @mkdir($cctvCacheDir, 0755, true);
    }

    try {
        $saved = $generator->saveAll($cctvCacheDir, $grouped);
    } catch (Throwable $e) {
        $saved = [];
        $errors[] = 'saveAll: ' . $e->getMessage();
    }

    $elapsedMs = round((microtime(true) - $startTime) * 1000, 0);

    return [
        'grouped' => $grouped,
        'fetched_count' => is_array($fetched) ? count($fetched) : 0,
        'filtered_count' => is_array($filtered) ? count($filtered) : 0,
        'verified_count' => is_array($verified) ? count($verified) : 0,
        'grouped_count' => is_array($grouped) ? count($grouped) : 0,
        'saved' => $saved,
        'elapsed_ms' => $elapsedMs,
        'errors' => $errors,
        'cache_dir' => $cctvCacheDir,
    ];
}

try {

    cctv_send_cors();

    $versionFile = __DIR__ . '/version.php';
    if (file_exists($versionFile)) {
        $versionData = include $versionFile;
        if (is_array($versionData)) {
            if (!defined('VERSION') && isset($versionData['version'])) {
                define('VERSION', $versionData['version']);
            }
            if (!defined('VERSION_BUILD') && isset($versionData['build'])) {
                define('VERSION_BUILD', $versionData['build']);
            }
        }
    }

    if (file_exists(__DIR__ . '/db/autoload.php')) {
        include __DIR__ . '/db/autoload.php';
    }

    require_once __DIR__ . '/cctv/CctvSourceManager.php';
    require_once __DIR__ . '/cctv/CctvSourceVerifier.php';
    require_once __DIR__ . '/cctv/CctvPlaylistGenerator.php';

    $config = cctv_load_config();

    $cctvCacheDir = __DIR__ . '/cache/cctv';
    if (!is_dir($cctvCacheDir)) {
        @mkdir($cctvCacheDir, 0755, true);
    }

    $cacheTtl = isset($config['cctv_live']['cache_ttl'])
        ? intval($config['cctv_live']['cache_ttl'])
        : 21600;

    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $secret = isset($_GET['secret']) ? $_GET['secret'] : '';
    $format = isset($_GET['format']) ? $_GET['format'] : '';

    if (!empty($format) && $action === 'list') {
        if ($format === 'm3u') $action = 'm3u';
        elseif ($format === 'txt') $action = 'txt';
    }

    $generator = new CctvPlaylistGenerator();

    if ($action === 'update') {
        $triggerSecret = isset($config['cctv_live']['trigger_secret'])
            ? $config['cctv_live']['trigger_secret']
            : '';
        if ($triggerSecret !== '' && $secret !== $triggerSecret) {
            cctv_json_response([
                'success' => false,
                'msg' => 'secret invalid',
            ], 403);
        }
        $result = cctv_fetch_and_build(true);
        cctv_json_response([
            'success' => empty($result['errors']),
            'count' => $result['grouped_count'],
            'elapsed_ms' => $result['elapsed_ms'],
            'errors' => $result['errors'],
            'fetched_count' => $result['fetched_count'],
            'filtered_count' => $result['filtered_count'],
            'verified_count' => $result['verified_count'],
        ], 200);
    }

    if ($action === 'status') {
        $mgr = new CctvSourceManager();
        $cacheInfo = $mgr->getCacheInfo();
        $meta = $generator->getMeta($cctvCacheDir);
        $genAt = isset($meta['generated_at']) ? intval($meta['generated_at']) : 0;
        $cached = ($genAt > 0);
        $ttlRemain = $cached ? max(0, $cacheTtl - (time() - $genAt)) : 0;
        $totalChannels = isset($meta['count']) ? intval($meta['count']) : 0;
        $verifiedCount = 0;
        $jsonFile = $cctvCacheDir . '/playlist.json';
        if (file_exists($jsonFile)) {
            $jsonData = @json_decode(@file_get_contents($jsonFile), true);
            if (is_array($jsonData)) {
                foreach ($jsonData as $ch) {
                    if (isset($ch['latency_ms']) && intval($ch['latency_ms']) > 0) {
                        $verifiedCount++;
                    }
                }
            }
        }
        cctv_json_response([
            'fetched_at' => isset($cacheInfo['fetched_at']) ? intval($cacheInfo['fetched_at']) : $genAt,
            'source_url' => isset($cacheInfo['source_url']) ? $cacheInfo['source_url'] : '',
            'total_channels' => $totalChannels,
            'verified_count' => $verifiedCount,
            'cache_ttl' => $cacheTtl,
            'ttl_remain' => $ttlRemain,
            'cached' => $cached,
        ], 200);
    }

    $cacheFresh = $generator->isCacheFresh($cctvCacheDir, $cacheTtl);

    if (!$cacheFresh) {
        cctv_fetch_and_build(false);
    }

    if ($action === 'list' || $action === 'json') {
        $jsonContent = $generator->loadFromCache('json', $cctvCacheDir);
        if ($jsonContent === false) {
            $rebuild = cctv_fetch_and_build(true);
            $jsonContent = $generator->generateJson($rebuild['grouped']);
        }
        cctv_json_response(json_decode($jsonContent, true), 200);
    }

    if ($action === 'm3u') {
        $m3uContent = $generator->loadFromCache('m3u', $cctvCacheDir);
        if ($m3uContent === false) {
            $rebuild = cctv_fetch_and_build(true);
            $m3uContent = $generator->generateM3U($rebuild['grouped'], true);
        }
        cctv_m3u_response($m3uContent, 'playlist.m3u');
    }

    if ($action === 'txt') {
        $txtContent = $generator->loadFromCache('txt', $cctvCacheDir);
        if ($txtContent === false) {
            $rebuild = cctv_fetch_and_build(true);
            $txtContent = $generator->generateTxt($rebuild['grouped']);
        }
        cctv_text_response($txtContent, 200, 'text/plain; charset=utf-8');
    }

    if ($action === 'play') {
        if (empty($id)) {
            cctv_text_response('Channel id required', 400, 'text/plain');
        }
        $jsonFile = $cctvCacheDir . '/playlist.json';
        $verifiedSources = [];
        if (file_exists($jsonFile)) {
            $jsonRaw = @file_get_contents($jsonFile);
            $jsonData = @json_decode($jsonRaw, true);
            if (is_array($jsonData)) {
                $verifiedSources = $jsonData;
            }
        }
        if (empty($verifiedSources)) {
            $rebuild = cctv_fetch_and_build(false);
            $verifiedSources = $rebuild['grouped'];
        }
        $playlistContent = $generator->getSingleChannelPlaylist($id, $verifiedSources);
        if (strpos($playlistContent, '#ERROR: Channel not found') !== false) {
            cctv_text_response('Channel not found: ' . $id, 404, 'text/plain');
        }
        if (strpos($playlistContent, '#ERROR:') !== false) {
            cctv_text_response(trim(str_replace('#ERROR:', 'Error:', $playlistContent)), 400, 'text/plain');
        }
        cctv_m3u_response($playlistContent, 'channel_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $id) . '.m3u8');
    }

    cctv_json_response([
        'success' => false,
        'msg' => 'Unknown action, supported: list, m3u, txt, play, update, status',
    ], 400);

} catch (Throwable $e) {
    $errMsg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    @error_log('[cctv] Fatal error: ' . $errMsg);
    if (function_exists('headers_sent') && !headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode([
        'success' => false,
        'msg' => 'Internal error',
        'error' => $errMsg,
    ], JSON_UNESCAPED_UNICODE);
    exit(1);
}
