<?php
/**
 * URL 转 JSON 专用入口（v5.13.9）
 *
 * 用途：将网页播放器 URL / VIP 平台视频页 URL / 直链 URL 统一转为结构化 JSON 输出。
 * 与 jiexi.php / xt.php 使用同一套解析引擎 + 缓存，嗅探配置完全复用 xt/sniffer_config.php。
 *
 * 用法示例：
 *   ① 独立入口（推荐）：
 *     url2json.php?url=https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html&type=json
 *     url2json.php?wd=…&type=302    // 302 跳转直链
 *     url2json.php?v=…&type=api     // 影视 CMS 标准 (code=1)
 *     url2json.php?video=…&type=xml // 老盒子 XML
 *     url2json.php?t=…&callback=cb  // JSONP 回调
 *
 *   ② mx.php 路由（兼容老项目）：
 *     mx.php?action=url2json&url=…  (与上面等价，会直接 require 本文件 exit)
 *
 * 返回（type=json 默认）：
 * {
 *   "code": 200,
 *   "ZT": "解析成功",
 *   "msg": "https://资源站/xxx.m3u8",
 *   "url": "https://资源站/xxx.m3u8",
 *   "time": "2.34s",
 *   "KFZ": "超级嗅探|XT",
 *   "info": "URL转JSON专用解析",
 *   "type": "m3u8",              // m3u8 | html_player | mp4 | flv | other
 *   "is_m3u8": true,
 *   "is_html_player": false,
 *   "source": "replace",         // replace | official | cache | direct | unknown
 *   "official_url": "https://jx.xmflv.cc/?url=…",  // concurrent 模式有
 *   "replace_url":  "https://资源站/xxx.m3u8",      // concurrent 模式有
 *   "video_url": "https://v.youku.com/v_show/id_…", // 原始输入 URL
 *   "platform": "youku"          // youku | iqiyi | tencent | mgtv | bilibili | sohu | pptv | unknown | null
 * }
 */

// HOTFIX5：提前检测 CLI 模式（用于 CORS header / OPTIONS 退出 / 同步主流程 的分段守卫）
//          普通 CLI 下 require url2json.php 不发 header、不读 $_SERVER、不 exit，
//          只加载 u2j_* 函数与异步逻辑，方便第三方脚本与单元测试复用。
$u2j_isCLI = (PHP_SAPI === 'cli');
$u2j_isWorker = false;
if ($u2j_isCLI && !empty($argv) && is_array($argv)) {
    foreach ($argv as $a) {
        if (is_string($a) && strpos($a, '_task_worker=') === 0) { $u2j_isWorker = true; break; }
    }
}

// CORS header 和 OPTIONS 预检只在 HTTP 请求/CLI worker 时发出
if (!$u2j_isCLI || $u2j_isWorker):
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Range');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}
endif;

// -------- 工具函数 --------
if (!function_exists('u2j_getVideoUrl')) {
    function u2j_getVideoUrl(): string {
        $params = ['url', 'wd', 'v', 'video', 't', 'u', 'play', 'src'];
        foreach ($params as $p) {
            if (isset($_GET[$p]) && ($s = trim((string)$_GET[$p])) !== '') return $s;
        }
        if (isset($_POST['url']) && is_string($_POST['url'])) return trim($_POST['url']);
        return '';
    }
}
if (!function_exists('u2j_getFormat')) {
    function u2j_getFormat(): string {
        $type = isset($_GET['type']) ? strtolower(trim((string)$_GET['type'])) : '';
        $fmt  = isset($_GET['format']) ? strtolower(trim((string)$_GET['format'])) : '';
        if ($type === '302' || $type === 'redirect' || $type === 'raw' || $fmt === 'm3u8') return '302';
        if ($type === 'api' || $type === 'cms') return 'api';
        if ($type === 'xml') return 'xml';
        return 'json';
    }
}
if (!function_exists('u2j_guessPlatform')) {
    function u2j_guessPlatform(?string $url): ?string {
        if (!$url) return null;
        $host = (string)parse_url($url, PHP_URL_HOST);
        if ($host === '') return null;
        $map = [
            'youku.com' => 'youku',
            'iqiyi.com' => 'iqiyi',
            'qiyi.com'  => 'iqiyi',
            'v.qq.com'  => 'tencent',
            'qq.com'    => 'tencent',
            'mgtv.com'  => 'mgtv',
            'bilibili.com' => 'bilibili',
            'sohu.com'  => 'sohu',
            'le.com'    => 'le',
            'pptv.com'  => 'pptv',
        ];
        foreach ($map as $k => $v) {
            if (stripos($host, $k) !== false) return $v;
        }
        return 'unknown';
    }
}
if (!function_exists('u2j_guessUrlType')) {
    function u2j_guessUrlType(?string $url): array {
        if (!$url) return ['type' => 'other', 'is_m3u8' => false, 'is_html_player' => false];
        $low = strtolower($url);
        $host = (string)parse_url($url, PHP_URL_HOST);
        $isHtmlPlayer = false;
        if (stripos($host, 'xmflv.cc') !== false || stripos($host, 'jmflv') !== false ||
            stripos($host, 'jx.xm') === 0 || stripos($host, 'xmplayer') !== false) {
            $isHtmlPlayer = true;
        }
        if ($isHtmlPlayer) {
            return ['type' => 'html_player', 'is_m3u8' => false, 'is_html_player' => true];
        }
        if (strpos($low, '.m3u8') !== false) return ['type' => 'm3u8', 'is_m3u8' => true, 'is_html_player' => false];
        if (strpos($low, '.mp4') !== false)  return ['type' => 'mp4',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.flv') !== false)  return ['type' => 'flv',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.mkv') !== false)  return ['type' => 'mkv',  'is_m3u8' => false, 'is_html_player' => false];
        if (strpos($low, '.ts') !== false)   return ['type' => 'ts',   'is_m3u8' => false, 'is_html_player' => false];
        return ['type' => 'other', 'is_m3u8' => false, 'is_html_player' => false];
    }
}
if (!function_exists('u2j_outputJson')) {
    function u2j_outputJson(array $data, ?string $callback): void {
        if ($callback !== null && $callback !== '' && preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $callback)) {
            header('Content-Type: application/javascript; charset=utf-8');
            echo $callback . '(' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
            return;
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
if (!function_exists('u2j_outputError')) {
    function u2j_outputError(string $message, string $format, ?string $callback, array $ctx = []): void {
        $parseTime = (string)($ctx['time'] ?? '0s');
        $kfz = (string)($ctx['KFZ'] ?? '超级嗅探|XT');
        $videoUrl = (string)($ctx['video_url'] ?? '');
        switch ($format) {
            case '302':
                header('Content-Type: text/plain; charset=utf-8');
                echo '解析失败: ' . $message;
                exit;
            case 'api':
                u2j_outputJson([
                    'code' => 0,
                    'msg'  => $message,
                    'url'  => '',
                    'video_url' => $videoUrl,
                ], $callback);
                exit;
            case 'xml':
                header('Content-Type: text/xml; charset=utf-8');
                echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
                echo '<result>' . "\n";
                echo '  <code>0</code>' . "\n";
                echo '  <msg>' . htmlspecialchars($message) . '</msg>' . "\n";
                echo '  <url></url>' . "\n";
                if ($videoUrl !== '') echo '  <video_url><![CDATA[' . htmlspecialchars($videoUrl) . ']]></video_url>' . "\n";
                echo '</result>';
                exit;
            default:
                u2j_outputJson([
                    'code' => 400,
                    'ZT'   => $message,
                    'msg'  => $message,
                    'url'  => '',
                    'time' => $parseTime,
                    'KFZ'  => $kfz,
                    'info' => 'URL转JSON专用解析',
                    'video_url' => $videoUrl,
                    'platform' => u2j_guessPlatform($videoUrl ?: null),
                ], $callback);
                exit;
        }
    }
}
if (!function_exists('u2j_currentBaseUrl')) {
    function u2j_currentBaseUrl(): string {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? (($_SERVER['SERVER_NAME'] ?? 'localhost') . (isset($_SERVER['SERVER_PORT']) ? ':' . $_SERVER['SERVER_PORT'] : ''));
        $script = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '/url2json.php';
        $dir = rtrim(dirname($script), '/\\');
        return $scheme . '://' . $host . $dir . '/url2json.php';
    }
}

// -------- 主流程 --------
// HOTFIX5：CLI 模式下如果不是 _task_worker 子进程，跳过同步主流程（不读 $_GET/$_POST 也不输出/exit），
// 这样第三方脚本可以直接 `require url2json.php` 复用 u2j_* 函数，不会触发 Warning/Exit
// 注：$u2j_isCLI / $u2j_isWorker 已在文件开头 CORS 守卫处定义
$videoUrl = !$u2j_isCLI ? u2j_getVideoUrl() : '';
$format   = !$u2j_isCLI ? u2j_getFormat() : 'json';
$callback = !$u2j_isCLI && isset($_GET['callback']) ? trim((string)$_GET['callback']) : null;

// v5.13.10-HOTFIX5（终极防 502）：异步任务模型
//   _async=1              : 快速返回 {_async:true, task_id}，然后后台运行 parseVideo → 写到 cache/tasks/{task_id}.json
//   _task=<id>            : 轮询任务状态 → 返回 {_async:true, task_id, status:"pending|running|done|fail", result: {...}, ...}
//   _task_clean=1         : 删除该 _task 对应的结果文件
if (!function_exists('u2j_getTaskDir')) {
    function u2j_getTaskDir(): string {
        static $dir = null;
        if ($dir !== null) return $dir;
        $c = require __DIR__ . '/xt/config.php';
        $base = $c['cache']['dir'] ?? (__DIR__ . '/xt/cache');
        $dir = rtrim($base, '/\\') . '/tasks';
        @mkdir($dir, 0775, true);
        // 防下载：写入空 .htaccess (Apache) + index.html
        @file_put_contents($dir . '/.htaccess', "Require all denied\n");
        @file_put_contents($dir . '/index.html', '');
        return $dir;
    }
}
if (!function_exists('u2j_cleanupOldTasks')) {
    function u2j_cleanupOldTasks(string $dir): void {
        static $lastRun = 0;
        $now = time();
        if ($now - $lastRun < 60) return;
        $lastRun = $now;
        $files = glob($dir . '/*.json');
        if (!$files) return;
        foreach ($files as $f) {
            if ($now - @filemtime($f) > 86400) @unlink($f);
        }
    }
}
if (!function_exists('u2j_buildTaskPayload')) {
    function u2j_buildTaskPayload(string $status, array $extra = []): array {
        return array_merge([
            '_xt_async' => true,
            'task_id' => $extra['task_id'] ?? null,
            'status'  => $status,   // pending | running | done | fail | unknown
            'created_at' => $extra['created_at'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
            'note'    => 'HOTFIX5 异步任务（避免 nginx 502 Bad Gateway 中断响应）。前端轮询 ?_task=<task_id> 直到 status=done/fail。',
        ], $extra);
    }
}

// 1) ?_task=id：直接返回/轮询任务
if (!empty($_GET['_task']) || !empty($_POST['_task'])) {
    $taskId = trim((string)($_GET['_task'] ?? $_POST['_task'] ?? ''));
    if ($taskId === '' || !preg_match('/^[a-f0-9]{16,64}$/i', $taskId)) {
        u2j_outputError('非法 _task id（必须是 16-64 位 十六进制）', $format, $callback);
    }
    $taskDir = u2j_getTaskDir();
    u2j_cleanupOldTasks($taskDir);
    $taskFile = $taskDir . '/' . $taskId . '.json';
    if (!empty($_GET['_task_clean'])) {
        if (file_exists($taskFile)) @unlink($taskFile);
        u2j_outputJson(u2j_buildTaskPayload('cleaned', ['task_id'=>$taskId]), $callback);
        exit;
    }
    if (!file_exists($taskFile)) {
        u2j_outputJson(u2j_buildTaskPayload('unknown', ['task_id'=>$taskId, 'message'=>'任务不存在（可能已过期，请重新提交 _async=1）']), $callback);
        exit;
    }
    $payload = @json_decode(file_get_contents($taskFile), true);
    if (!is_array($payload)) {
        u2j_outputJson(u2j_buildTaskPayload('fail', ['task_id'=>$taskId, 'message'=>'任务文件损坏']), $callback);
        exit;
    }
    u2j_outputJson($payload, $callback);
    exit;
}

// 2) ?_async=1：创建任务，后台 shell 启动解析子进程，立即返回 task_id（100ms 内）
if (!empty($_GET['_async']) || !empty($_POST['_async'])) {
    if ($videoUrl === '') {
        u2j_outputError('请提供视频链接（支持 url/wd/v/video/t 参数）', $format, $callback);
    }
    if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
        u2j_outputError('链接格式不正确，必须是以 http:// 或 https:// 开头的完整 URL', $format, $callback, ['video_url' => $videoUrl]);
    }
    $taskDir = u2j_getTaskDir();
    u2j_cleanupOldTasks($taskDir);
    $taskId = hash('sha256', $videoUrl . '|' . microtime(true) . '|' . random_bytes(16));
    $taskFile = $taskDir . '/' . $taskId . '.json';
    $createdAt = date('Y-m-d H:i:s');
    $pending = u2j_buildTaskPayload('pending', [
        'task_id'      => $taskId,
        'created_at'   => $createdAt,
        'input' => [
            'video_url' => $videoUrl,
            'format'    => $format,
            'callback'  => $callback,
            '_mode'     => (string)($_GET['_mode'] ?? $_POST['_mode'] ?? ''),
            '_no_direct'=> (string)($_GET['_no_direct'] ?? $_POST['_no_direct'] ?? ''),
            '_timeout'  => (string)($_GET['_timeout'] ?? $_POST['_timeout'] ?? ''),
            '_type'     => (string)($_GET['type']    ?? $_POST['type']    ?? ''),
        ],
        'poll_interval_ms' => 1500,
        'poll_endpoint' => (function_exists('u2j_currentBaseUrl') ? u2j_currentBaseUrl() : '') . '?_task=' . $taskId,
        'ttl_seconds' => 300,
    ]);
    @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    // 后台 worker：PHP-CLI 执行 /workspace/url2json.php _task_worker=<taskId>（不占用当前 FPM）
    $self = __FILE__;
    $phpBin = defined('PHP_BINARY') ? PHP_BINARY : 'php';
    $cmdArgs = [
        escapeshellarg($phpBin),
        escapeshellarg($self),
        escapeshellarg('_task_worker=' . $taskId),
        escapeshellarg('_video=' . $videoUrl),
        escapeshellarg('_format=' . $format),
        $callback !== null ? escapeshellarg('_callback=' . $callback) : '',
        !empty($_GET['_mode'])     ? escapeshellarg('_mode='.$_GET['_mode'])         : '',
        !empty($_GET['_no_direct'])? escapeshellarg('_no_direct='.$_GET['_no_direct']): '',
        !empty($_GET['_timeout'])  ? escapeshellarg('_timeout='.$_GET['_timeout'])   : '',
    ];
    $cmd = implode(' ', array_filter($cmdArgs, fn($s)=>$s!==''));
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $shellCmd = 'start /B "" cmd /C "' . $cmd . ' >NUL 2>NUL"';
    } else {
        $shellCmd = 'nohup ' . $cmd . ' >/dev/null 2>&1 & echo $!';
    }
    $workerPid = null;
    @exec($shellCmd, $_, $exitCode);
    u2j_outputJson($pending, $callback);
    exit;
}

// 3) _task_worker=id：CLI worker（由 nohup/shell 启动），运行 parseVideo，写 running → done/fail
if (!empty($argv) && is_array($argv)) {
    $workerId = null; $workerVideo=''; $workerFormat='json'; $workerCb=null;
    $wMode=''; $wNoDirect=''; $wTimeout='';
    foreach ($argv as $arg) {
        if (strpos($arg, '_task_worker=') === 0) $workerId = substr($arg, strlen('_task_worker='));
        elseif (strpos($arg, '_video=') === 0) $workerVideo = substr($arg, strlen('_video='));
        elseif (strpos($arg, '_format=') === 0) $workerFormat = substr($arg, strlen('_format='));
        elseif (strpos($arg, '_callback=') === 0) $workerCb = substr($arg, strlen('_callback='));
        elseif (strpos($arg, '_mode=') === 0) $wMode = substr($arg, strlen('_mode='));
        elseif (strpos($arg, '_no_direct=') === 0) $wNoDirect = substr($arg, strlen('_no_direct='));
        elseif (strpos($arg, '_timeout=') === 0) $wTimeout = substr($arg, strlen('_timeout='));
    }
    if ($workerId !== null) {
        // 作为后台 CLI worker 运行：不再发 HTTP 头/body（因为 FPM 已经返回 task_id 了）
        $_SERVER["HTTPS"] = $_SERVER["HTTPS"] ?? "off";
        $_SERVER["HTTP_HOST"] = $_SERVER["HTTP_HOST"] ?? "localhost";
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_GET = []; $_POST = [];
        if ($workerVideo !== '') $_GET['url'] = $workerVideo;
        if ($workerFormat !== '') $_GET['type'] = $workerFormat;
        if ($workerCb !== null) $_GET['callback'] = $workerCb;
        if ($wMode !== '') $_GET['_mode'] = $wMode;
        if ($wNoDirect !== '') $_GET['_no_direct'] = $wNoDirect;
        if ($wTimeout !== '') $_GET['_timeout'] = $wTimeout;
        $taskDir = u2j_getTaskDir();
        $taskFile = $taskDir . '/' . $workerId . '.json';
        $pending = @json_decode(@file_get_contents($taskFile) ?: '[]', true) ?: [];
        if (!is_array($pending)) $pending = [];
        $pending['status'] = 'running';
        $pending['pid'] = getmypid();
        $pending['updated_at'] = date('Y-m-d H:i:s');
        @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        // 走原本的解析主流程，但把结果写入 taskFile 而不是 echo
        $videoUrl = u2j_getVideoUrl();
        $format   = $workerFormat ?: u2j_getFormat();
        $callback = $workerCb;
        if ($videoUrl === '' || !filter_var($videoUrl, FILTER_VALIDATE_URL)) {
            $pending['status'] = 'fail';
            $pending['error'] = 'URL 非法';
            @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            exit(0);
        }
        if (!defined('XT_SERVER_PHP_V1') && !function_exists('parseVideo')) {
            require_once __DIR__ . '/xt/server.php';
        }
        if (function_exists('u2j_apply_runtime_config_overrides')) {
            global $config;
            if (is_array($config)) u2j_apply_runtime_config_overrides($config);
        }
        try {
            $result = parseVideo($videoUrl);
        } catch (Throwable $e) {
            $pending['status'] = 'fail';
            $pending['error'] = $e->getMessage();
            $pending['error_file'] = $e->getFile() . ':' . $e->getLine();
            $pending['updated_at'] = date('Y-m-d H:i:s');
            @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            exit(0);
        }
        // 复制 url2json 原本的 enrich 逻辑封装最终结果，存为 data
        $parseTime = (string)($result['time'] ?? '0s');
        $kfz = (string)($result['KFZ'] ?? '超级嗅探|XT');
        $officialUrl = null; $replaceUrl = null;
        if (!empty($GLOBALS['XT_CONCURRENT_RESULTS']) && is_array($GLOBALS['XT_CONCURRENT_RESULTS'])) {
            $cr = $GLOBALS['XT_CONCURRENT_RESULTS'];
            if (!empty($cr['official_url'])) $officialUrl = (string)$cr['official_url'];
            if (!empty($cr['replace_url']))  $replaceUrl  = (string)$cr['replace_url'];
        }
        $code = (int)($result['code'] ?? 500);
        if ($code !== 200 || empty($result['url'])) {
            $pending['status'] = 'fail';
            $pending['error'] = (string)($result['msg'] ?? '解析失败');
            $pending['result'] = $result;
            $pending['updated_at'] = date('Y-m-d H:i:s');
            @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
            exit(0);
        }
        $playUrl = (string)$result['url'];
        $source = 'unknown';
        if (!empty($result['from_cache'])) $source = 'cache';
        elseif ($replaceUrl !== null && $playUrl === $replaceUrl) $source = 'replace';
        elseif ($officialUrl !== null && $playUrl === $officialUrl) $source = 'official';
        else {
            $t = u2j_guessUrlType($playUrl);
            if ($t['is_html_player']) $source = 'official';
            elseif ($t['is_m3u8'] || in_array($t['type'], ['mp4','flv','mkv','ts'], true)) $source = 'direct';
        }
        $ut = u2j_guessUrlType($playUrl);
        $enrich = $result;
        $enrich['info'] = 'URL转JSON专用解析（HOTFIX5 异步任务 worker）';
        if ($officialUrl !== null && !isset($enrich['official_url'])) $enrich['official_url'] = $officialUrl;
        if ($replaceUrl  !== null && !isset($enrich['replace_url']))  $enrich['replace_url']  = $replaceUrl;
        $enrich['source'] = $source;
        $enrich['type']   = $ut['type'];
        $enrich['is_m3u8'] = $ut['is_m3u8'];
        $enrich['is_html_player'] = $ut['is_html_player'];
        $enrich['video_url'] = $videoUrl;
        if (!isset($enrich['platform']) || $enrich['platform'] === null) {
            $enrich['platform'] = u2j_guessPlatform($videoUrl);
        }
        if (empty($enrich['ZT'])) $enrich['ZT'] = '解析成功';
        $pending['status'] = 'done';
        $pending['result'] = $enrich;
        $pending['http_code'] = 200;
        $pending['parse_time'] = $parseTime;
        $pending['kfz'] = $kfz;
        $pending['updated_at'] = date('Y-m-d H:i:s');
        @file_put_contents($taskFile, json_encode($pending, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        exit(0);
    }
}

// v5.13.10-HOTFIX4：HTTP 级「单次请求覆盖参数」用于前端 502 自动重试时强制单通道
//   _mode=official|replace|concurrent    → 临时覆盖 xt/sniffer_config.php 的 mode
//   _no_direct=1                         → 禁用本地官替直调（走 HTTP 官替），避免 PHP CPU 过载 502
//   _timeout=10                          → 单次请求 performance.timeout (秒，1-22)
if (!function_exists('u2j_apply_runtime_config_overrides')) {
    function u2j_apply_runtime_config_overrides(array &$config): void {
        $newMode = isset($_GET['_mode']) ? trim((string)$_GET['_mode']) : (isset($_POST['_mode']) ? trim((string)$_POST['_mode']) : '');
        $noDirect = isset($_GET['_no_direct']) ? (bool)$_GET['_no_direct'] : (isset($_POST['_no_direct']) ? (bool)$_POST['_no_direct'] : false);
        $timeout  = isset($_GET['_timeout']) ? (float)$_GET['_timeout'] : (isset($_POST['_timeout']) ? (float)$_POST['_timeout'] : null);
        if (in_array($newMode, ['official','replace','concurrent'], true)) {
            if (!isset($config['sniffer']) || !is_array($config['sniffer'])) $config['sniffer'] = [];
            $config['sniffer']['mode'] = $newMode;
        }
        if ($noDirect) {
            if (!isset($config['sniffer']['replace_api']) || !is_array($config['sniffer']['replace_api'])) $config['sniffer']['replace_api'] = [];
            // 远端填一个非空的本地动作标识，parseVideo 会判定 !forceReplaceDirect → 不走本地直调
            $config['sniffer']['replace_api']['enabled'] = true;
            $config['sniffer']['replace_api']['url'] = (string)($config['sniffer']['replace_api']['url'] ?? '') === ''
                ? 'official_replace/info' : (string)$config['sniffer']['replace_api']['url'];
        }
        if ($timeout !== null) {
            $t = max(1.0, min(22.0, $timeout));
            if (!isset($config['performance']) || !is_array($config['performance'])) $config['performance'] = [];
            $config['performance']['timeout'] = $t;
        }
    }
}

// HOTFIX5：同步主流程只在「HTTP 请求模式」或「CLI _task_worker 子进程」下执行
//          普通 CLI（require 验证脚本 / 单元测试）只加载函数，不触发输出/exit
if (!$u2j_isCLI || $u2j_isWorker):

if ($videoUrl === '') {
    u2j_outputError('请提供视频链接（支持 url/wd/v/video/t 参数）', $format, $callback);
}
if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    u2j_outputError('链接格式不正确，必须是以 http:// 或 https:// 开头的完整 URL', $format, $callback, ['video_url' => $videoUrl]);
}

// v5.13.10-HOTFIX3：双保险加载 xt/server.php（不管 mx.php / jiexi.php / 其他入口是否已加载）
//   1. defined('XT_SERVER_PHP_V1')：xt/server.php 自己的 guard
//   2. function_exists('parseVideo')：即使 guard 因为 OPcache 未生效，parseVideo 已存在也跳过
if (!defined('XT_SERVER_PHP_V1') && !function_exists('parseVideo')) {
    require_once __DIR__ . '/xt/server.php';
}
// HOTFIX4：加载后根据 _mode / _no_direct 覆盖 global $config（parseVideo 内部读 global）
if (function_exists('u2j_apply_runtime_config_overrides')) {
    global $config;
    if (is_array($config)) u2j_apply_runtime_config_overrides($config);
}

$result = parseVideo($videoUrl);

$parseTime = (string)($result['time'] ?? '0s');
$kfz = (string)($result['KFZ'] ?? '超级嗅探|XT');
$zt  = (string)($result['ZT'] ?? '');

// v5.13.5-G4 concurrent 双通道
$officialUrl = null;
$replaceUrl  = null;
if (!empty($GLOBALS['XT_CONCURRENT_RESULTS']) && is_array($GLOBALS['XT_CONCURRENT_RESULTS'])) {
    $cr = $GLOBALS['XT_CONCURRENT_RESULTS'];
    if (!empty($cr['official_url'])) $officialUrl = (string)$cr['official_url'];
    if (!empty($cr['replace_url']))  $replaceUrl  = (string)$cr['replace_url'];
}

if (($result['code'] ?? 500) !== 200 || empty($result['url'])) {
    u2j_outputError(($result['msg'] ?? '') ?: '解析失败', $format, $callback, [
        'time' => $parseTime,
        'KFZ'  => $kfz,
        'video_url' => $videoUrl,
    ]);
}

$playUrl = (string)$result['url'];

// source 推断
$source = 'unknown';
if (!empty($result['from_cache'])) $source = 'cache';
elseif ($replaceUrl !== null && $playUrl === $replaceUrl) $source = 'replace';
elseif ($officialUrl !== null && $playUrl === $officialUrl) $source = 'official';
else {
    $t = u2j_guessUrlType($playUrl);
    if ($t['is_html_player']) $source = 'official';
    elseif ($t['is_m3u8'] || in_array($t['type'], ['mp4','flv','mkv','ts'], true)) $source = 'direct';
}

// URL 类型
$ut = u2j_guessUrlType($playUrl);

// enrich（保留 parseVideo 已有字段的优先级）
$enrich = $result;
$enrich['info'] = 'URL转JSON专用解析';
if ($officialUrl !== null && !isset($enrich['official_url'])) $enrich['official_url'] = $officialUrl;
if ($replaceUrl  !== null && !isset($enrich['replace_url']))  $enrich['replace_url']  = $replaceUrl;
$enrich['source'] = $source;
$enrich['type']   = $ut['type'];
$enrich['is_m3u8'] = $ut['is_m3u8'];
$enrich['is_html_player'] = $ut['is_html_player'];
$enrich['video_url'] = $videoUrl;
if (!isset($enrich['platform']) || $enrich['platform'] === null) {
    $enrich['platform'] = u2j_guessPlatform($videoUrl);
}
// 失败 ZT 兜底
if (empty($enrich['ZT'])) $enrich['ZT'] = '解析成功';

// -------- 按 format 输出 --------
switch ($format) {
    case '302':
        header('Location: ' . $playUrl, true, 302);
        exit;

    case 'api':
        $out = [
            'code' => 1,
            'msg'  => $enrich['ZT'] ?: '解析成功',
            'url'  => $playUrl,
        ];
        foreach (['official_url','replace_url','source','type','is_m3u8','is_html_player','video_url','platform','time','KFZ'] as $k) {
            if (isset($enrich[$k])) $out[$k] = $enrich[$k];
        }
        u2j_outputJson($out, $callback);
        exit;

    case 'xml':
        header('Content-Type: text/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<result>' . "\n";
        echo '  <code>1</code>' . "\n";
        echo '  <msg>' . htmlspecialchars($enrich['ZT'] ?: '解析成功') . '</msg>' . "\n";
        echo '  <url><![CDATA[' . $playUrl . ']]></url>' . "\n";
        echo '  <video_url><![CDATA[' . htmlspecialchars($videoUrl) . ']]></video_url>' . "\n";
        if (!empty($enrich['official_url'])) echo '  <official_url><![CDATA[' . $enrich['official_url'] . ']]></official_url>' . "\n";
        if (!empty($enrich['replace_url']))  echo '  <replace_url><![CDATA['  . $enrich['replace_url']  . ']]></replace_url>' . "\n";
        echo '  <source>' . htmlspecialchars($enrich['source']) . '</source>' . "\n";
        echo '  <type>' . htmlspecialchars($enrich['type']) . '</type>' . "\n";
        echo '  <is_m3u8>' . ($enrich['is_m3u8'] ? '1' : '0') . '</is_m3u8>' . "\n";
        echo '  <is_html_player>' . ($enrich['is_html_player'] ? '1' : '0') . '</is_html_player>' . "\n";
        if (!empty($enrich['platform'])) echo '  <platform>' . htmlspecialchars($enrich['platform']) . '</platform>' . "\n";
        echo '  <time>' . htmlspecialchars($parseTime) . '</time>' . "\n";
        echo '  <KFZ>' . htmlspecialchars($kfz) . '</KFZ>' . "\n";
        echo '</result>';
        exit;

    default: // json
        // 保持跟 jiexi.php 完全兼容的字段：code/ZT/msg/url/time/KFZ/info + URL2JSON 专有的 enrich 字段
        $out = [
            'code' => 200,
            'ZT'   => $enrich['ZT'],
            'msg'  => $playUrl,
            'url'  => $playUrl,
            'time' => $parseTime,
            'KFZ'  => $kfz,
            'info' => $enrich['info'],
        ];
        foreach (['type','is_m3u8','is_html_player','source','official_url','replace_url','video_url','platform','from_cache'] as $k) {
            if (isset($enrich[$k]) && $enrich[$k] !== null) $out[$k] = $enrich[$k];
        }
        u2j_outputJson($out, $callback);
        exit;
}

endif; // HOTFIX5 同步主流程 CLI 跳过 guard 结束
