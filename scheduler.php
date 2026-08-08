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
$logFile = $logDir . '/app_error_scheduler_' . $logDate . '.log';
@ini_set('error_log', $logFile);

function scheduler_load_config() {
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

function scheduler_response($data, $code = 200) {
    global $isCli;
    if ($isCli) {
        if (is_array($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        } else {
            echo (string)$data . PHP_EOL;
        }
    } else {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

function scheduler_safe_require($file, $once = true) {
    if (file_exists($file)) {
        if ($once) {
            include_once $file;
        } else {
            include $file;
        }
        return true;
    }
    return false;
}

try {

    $versionFile = __DIR__ . '/version.php';
    if (file_exists($versionFile)) {
        $versionData = require $versionFile;
        if (is_array($versionData)) {
            if (!defined('VERSION') && isset($versionData['version'])) {
                define('VERSION', $versionData['version']);
            }
            if (!defined('VERSION_BUILD') && isset($versionData['build'])) {
                define('VERSION_BUILD', $versionData['build']);
            }
            if (!defined('VERSION_NAME') && isset($versionData['name'])) {
                define('VERSION_NAME', $versionData['name']);
            }
        }
    }

    scheduler_safe_require(__DIR__ . '/db/autoload.php');

    $srcClasses = [
        'AuthValidator.php',
        'AuthConfig.php',
        'M3U8Parser.php',
        'AdRuleEngine.php',
        'EnhancedAdRuleEngine.php',
        'AdFilter.php',
        'M3U8OutputGenerator.php',
        'M3U8AdSkipper.php',
    ];
    foreach ($srcClasses as $classFile) {
        $fullPath = __DIR__ . '/src/' . $classFile;
        scheduler_safe_require($fullPath);
    }

    require_once __DIR__ . '/ai_learn/TaskScheduler.php';
    require_once __DIR__ . '/ai_learn/MacCMS10Analyzer.php';
    require_once __DIR__ . '/ai_learn/AutoLearnEngine.php';
    require_once __DIR__ . '/ai_learn/AiEndpointRouter.php';

    $cctvClasses = [
        'CctvSourceManager.php',
        'CctvSourceVerifier.php',
        'CctvPlaylistGenerator.php',
    ];
    $cctvClassesLoaded = true;
    foreach ($cctvClasses as $classFile) {
        $fullPath = __DIR__ . '/cctv/' . $classFile;
        if (!scheduler_safe_require($fullPath)) {
            $cctvClassesLoaded = false;
        }
    }

    $config = scheduler_load_config();

    $cacheDir = __DIR__ . '/cache/scheduler';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0755, true);
    }

    $scheduler = new TaskScheduler($cacheDir);

    $autoLearnInterval = isset($config['auto_learn']['interval_seconds'])
        ? intval($config['auto_learn']['interval_seconds'])
        : 14400;
    $autoLearnMaxSites = isset($config['auto_learn']['max_sites_per_run'])
        ? intval($config['auto_learn']['max_sites_per_run'])
        : 5;
    $autoLearnPerSite = isset($config['auto_learn']['videos_per_site'])
        ? intval($config['auto_learn']['videos_per_site'])
        : 5;
    $autoLearnOpts = [
        'min_segments' => isset($config['auto_learn']['min_segments']) ? intval($config['auto_learn']['min_segments']) : 50,
        'max_ad_percentage' => isset($config['auto_learn']['max_ad_percentage']) ? intval($config['auto_learn']['max_ad_percentage']) : 90,
        'min_ad_percentage' => isset($config['auto_learn']['min_ad_percentage']) ? intval($config['auto_learn']['min_ad_percentage']) : 10,
        'safeguard_min_keep_ratio' => isset($config['auto_learn']['safeguard_min_keep_ratio']) ? floatval($config['auto_learn']['safeguard_min_keep_ratio']) : 0.6,
    ];
    $scheduler->registerTask('auto_learn', $autoLearnInterval, function($opts) use ($autoLearnOpts, $autoLearnMaxSites, $autoLearnPerSite) {
        $engineOpts = isset($opts['engine_opts']) && is_array($opts['engine_opts'])
            ? array_merge($autoLearnOpts, $opts['engine_opts'])
            : $autoLearnOpts;
        $engine = new AutoLearnEngine($engineOpts);
        $maxSites = isset($opts['max_sites']) ? intval($opts['max_sites']) : $autoLearnMaxSites;
        $perSite = isset($opts['per_site']) ? intval($opts['per_site']) : $autoLearnPerSite;
        return $engine->run($maxSites, $perSite);
    }, [
        'enabled' => true,
        'max_sites' => $autoLearnMaxSites,
        'per_site' => $autoLearnPerSite,
        'engine_opts' => $autoLearnOpts,
    ]);

    $cctvInterval = isset($config['cctv_live']['update_interval'])
        ? intval($config['cctv_live']['update_interval'])
        : 21600;
    $scheduler->registerTask('cctv_update', $cctvInterval, function() use ($config, $cacheDir) {
        if (class_exists('CctvSourceManager') && class_exists('CctvSourceVerifier') && class_exists('CctvPlaylistGenerator')) {
            $mgr = new CctvSourceManager();
            $verifier = new CctvSourceVerifier();
            $generator = new CctvPlaylistGenerator();
            $verifyConcurrent = isset($config['cctv_live']['verify_concurrent'])
                ? intval($config['cctv_live']['verify_concurrent'])
                : 8;
            $filterOnly = isset($config['cctv_live']['filter_cctv_only'])
                ? (bool)$config['cctv_live']['filter_cctv_only']
                : true;
            $fetched = $mgr->fetchSources(true);
            $filtered = $mgr->filterByCategory($fetched, $filterOnly);
            $verified = $verifier->verifyBatch($filtered, $verifyConcurrent);
            $grouped = $verifier->pickBestByChannel($verified);
            $cctvCacheDir = __DIR__ . '/cache/cctv';
            $saved = $generator->saveAll($cctvCacheDir, $grouped);
            return [
                'fetched_count' => is_array($fetched) ? count($fetched) : 0,
                'filtered_count' => is_array($filtered) ? count($filtered) : 0,
                'verified_count' => is_array($verified) ? count($verified) : 0,
                'grouped_count' => is_array($grouped) ? count($grouped) : 0,
                'saved_files' => $saved,
            ];
        } else {
            error_log('[scheduler] CCTV module skipped: classes not loaded');
            return true;
        }
    }, ['enabled' => true]);

    $aiEndpoints = isset($config['ai']['endpoints']) && is_array($config['ai']['endpoints'])
        ? $config['ai']['endpoints']
        : [];
    $scheduler->registerTask('ai_health_check', 1800, function($opts) use ($aiEndpoints) {
        $eps = isset($opts['endpoints']) && is_array($opts['endpoints'])
            ? $opts['endpoints']
            : $aiEndpoints;
        $router = new AiEndpointRouter($eps);
        $healthy = $router->getHealthyEndpoints();
        try {
            $router->updateConfigStoredOrder();
        } catch (Throwable $e) {
            error_log('[scheduler] ai_health_check updateConfigStoredOrder warning: ' . $e->getMessage());
        }
        return [
            'healthy_count' => is_array($healthy) ? count($healthy) : 0,
            'healthy_endpoints' => $healthy,
        ];
    }, [
        'enabled' => true,
        'endpoints' => $aiEndpoints,
    ]);

    if ($isCli) {
        $argv = isset($argv) ? $argv : [];
        $cmd = isset($argv[1]) ? $argv[1] : '';

        if ($cmd === '' || $cmd === 'help' || $cmd === '--help' || $cmd === '-h') {
            echo "TaskScheduler CLI 使用帮助\n";
            echo "========================\n\n";
            echo "示例:\n";
            echo "  php scheduler.php run auto_learn --force\n";
            echo "  php scheduler.php status\n";
            echo "  php scheduler.php list\n\n";
            echo "命令:\n";
            echo "  help                     显示此帮助\n";
            echo "  list                     列出所有已注册任务\n";
            echo "  status                   显示所有任务运行状态\n";
            echo "  run <task> [--force] [--secret=xxx]\n";
            echo "                           运行指定任务 (--force 忽略时间判断)\n\n";
            echo "可用任务:\n";
            $statuses = $scheduler->getTaskStatus();
            foreach ($statuses as $s) {
                $intSec = intval($s['interval_sec']);
                $h = floor($intSec / 3600);
                $m = floor(($intSec % 3600) / 60);
                $intStr = $h > 0 ? ($h . '小时' . ($m > 0 ? $m . '分' : '')) : ($m . '分钟');
                echo "  - " . $s['name'] . " (间隔: " . $intStr . ", " . ($s['enabled'] ? '启用' : '禁用') . ")\n";
            }
            exit(0);
        }

        if ($cmd === 'list' || $cmd === 'status') {
            $statuses = $scheduler->getTaskStatus();
            echo "+------------------+----------+---------------------+---------------------+----------+--------+\n";
            echo "| Task             | 状态     | 上次运行            | 下次运行            | 失败次数 | 耗时ms |\n";
            echo "+------------------+----------+---------------------+---------------------+----------+--------+\n";
            foreach ($statuses as $s) {
                $name = str_pad($s['name'], 16, ' ', STR_PAD_RIGHT);
                $enabled = $s['enabled'] ? '启用' : '禁用';
                $enabled = str_pad($enabled, 8, ' ', STR_PAD_RIGHT);
                $lastRun = $s['last_run'] ? $s['last_run'] : '未运行';
                $lastRun = str_pad($lastRun, 19, ' ', STR_PAD_RIGHT);
                $nextRun = str_pad($s['next_run'], 19, ' ', STR_PAD_RIGHT);
                $fail = str_pad((string)$s['fail_count'], 8, ' ', STR_PAD_RIGHT);
                $dur = str_pad((string)$s['last_duration_ms'], 6, ' ', STR_PAD_RIGHT);
                echo "| {$name} | {$enabled} | {$lastRun} | {$nextRun} | {$fail} | {$dur} |\n";
                if (!empty($s['last_error'])) {
                    echo "|                  |          | 上次错误: " . mb_substr($s['last_error'], 0, 60) . "\n";
                }
            }
            echo "+------------------+----------+---------------------+---------------------+----------+--------+\n";
            exit(0);
        }

        if ($cmd === 'run') {
            $taskName = isset($argv[2]) ? $argv[2] : '';
            $force = false;
            $secret = '';
            for ($i = 3; $i < count($argv); $i++) {
                $arg = $argv[$i];
                if ($arg === '--force' || $arg === '-f') {
                    $force = true;
                } elseif (strpos($arg, '--secret=') === 0) {
                    $secret = substr($arg, 9);
                }
            }
            if (empty($taskName)) {
                echo "错误: 请指定任务名\n";
                echo "用法: php scheduler.php run <task> [--force] [--secret=xxx]\n";
                exit(1);
            }
            $secretValid = true;
            if (!empty($secret)) {
                $taskSecret = '';
                if ($taskName === 'auto_learn') {
                    $taskSecret = isset($config['auto_learn']['trigger_secret']) ? $config['auto_learn']['trigger_secret'] : '';
                } elseif ($taskName === 'cctv_update') {
                    $taskSecret = isset($config['cctv_live']['trigger_secret']) ? $config['cctv_live']['trigger_secret'] : '';
                } elseif ($taskName === 'ai_health_check') {
                    $s1 = isset($config['auto_learn']['trigger_secret']) ? $config['auto_learn']['trigger_secret'] : '';
                    $s2 = isset($config['cctv_live']['trigger_secret']) ? $config['cctv_live']['trigger_secret'] : '';
                    $secretValid = ($secret === $s1 || $secret === $s2);
                } else {
                    $secretValid = true;
                }
                if ($taskSecret !== '' && $taskName !== 'ai_health_check') {
                    $secretValid = ($secret === $taskSecret);
                }
            }
            if (!$secretValid) {
                echo "错误: secret 无效\n";
                exit(1);
            }
            $result = $scheduler->runTask($taskName, $force);
            echo "=== 任务执行结果: {$taskName} ===\n";
            echo "状态: " . (isset($result['status']) ? $result['status'] : 'unknown') . "\n";
            if (isset($result['duration_ms'])) {
                echo "耗时: " . $result['duration_ms'] . " ms\n";
            }
            if (isset($result['error'])) {
                echo "错误: " . $result['error'] . "\n";
            }
            if (isset($result['message'])) {
                echo "消息: " . $result['message'] . "\n";
            }
            if (isset($result['next_run'])) {
                echo "下次运行: " . $result['next_run'] . "\n";
            }
            if (isset($result['result'])) {
                echo "返回数据:\n";
                if (is_array($result['result'])) {
                    echo json_encode($result['result'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
                } else {
                    echo var_export($result['result'], true) . "\n";
                }
            }
            $exitCode = (isset($result['status']) && ($result['status'] === 'success' || $result['status'] === 'not_due')) ? 0 : 1;
            exit($exitCode);
        }

        echo "未知命令: {$cmd}\n";
        echo "使用: php scheduler.php help 查看帮助\n";
        exit(1);

    } else {

        if (!headers_sent()) {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
        }

        $action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
        $task = isset($_GET['task']) ? $_GET['task'] : (isset($_POST['task']) ? $_POST['task'] : '');
        $secret = isset($_GET['secret']) ? $_GET['secret'] : (isset($_POST['secret']) ? $_POST['secret'] : '');
        $force = !empty($_GET['force']) || !empty($_POST['force']);

        if ($action === '' || $action === 'ping') {
            $statuses = $scheduler->getTaskStatus();
            $taskNames = [];
            foreach ($statuses as $s) {
                $taskNames[] = $s['name'];
            }
            scheduler_response([
                'success' => true,
                'msg' => 'scheduler ok',
                'tasks' => $taskNames,
                'version' => defined('VERSION') ? VERSION : '',
            ], 200);
            exit;
        }

        if ($action === 'status' || $action === 'list') {
            scheduler_response([
                'success' => true,
                'tasks' => $scheduler->getTaskStatus(),
            ], 200);
            exit;
        }

        if ($action === 'run') {
            if (empty($task)) {
                scheduler_response([
                    'success' => false,
                    'msg' => 'task 参数不能为空',
                ], 400);
                exit;
            }
            $secretValid = false;
            $taskSecret = '';
            if ($task === 'auto_learn') {
                $taskSecret = isset($config['auto_learn']['trigger_secret']) ? $config['auto_learn']['trigger_secret'] : '';
                $secretValid = ($secret === $taskSecret);
            } elseif ($task === 'cctv_update') {
                $taskSecret = isset($config['cctv_live']['trigger_secret']) ? $config['cctv_live']['trigger_secret'] : '';
                $secretValid = ($secret === $taskSecret);
            } elseif ($task === 'ai_health_check') {
                $s1 = isset($config['auto_learn']['trigger_secret']) ? $config['auto_learn']['trigger_secret'] : '';
                $s2 = isset($config['cctv_live']['trigger_secret']) ? $config['cctv_live']['trigger_secret'] : '';
                $secretValid = ($secret === $s1 || $secret === $s2);
            } else {
                $secretValid = true;
            }
            if (!$secretValid) {
                scheduler_response([
                    'success' => false,
                    'msg' => 'secret invalid',
                ], 403);
                exit;
            }
            $result = $scheduler->runTask($task, $force);
            scheduler_response([
                'success' => true,
                'task' => $task,
                'force' => $force,
                'result' => $result,
            ], 200);
            exit;
        }

        scheduler_response([
            'success' => false,
            'msg' => '未知 action，支持: run, status, list, (空)',
        ], 400);
        exit;
    }

} catch (Throwable $e) {
    $errMsg = $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    @error_log('[scheduler] Fatal error: ' . $errMsg);
    scheduler_response([
        'success' => false,
        'msg' => 'Internal error',
        'error' => $errMsg,
    ], 500);
    exit(1);
}
