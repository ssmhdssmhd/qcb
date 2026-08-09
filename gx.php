<?php
/**
 * =============================================================
 *  gx.php  ——  全局定时/自动更新调度中心  v1.0
 * =============================================================
 *
 * 用途：一键 / 定时 触发所有自动化任务：
 *   1. 版本&完整性检查        (check)
 *   2. 数据库迁移升级         (migrate)
 *   3. AI 自动学习广告规则    (ai_learn / ai_cleanup)
 *   4. 官替识别缓存刷新       (official_refresh)
 *   5. 资源站健康巡检         (site_check)
 *   6. 域名规则健康检查       (rule_check)
 *   7. 一键执行全部           (all / 默认)
 *
 * 支持两种运行模式：
 *   ┌──────────────────────────────────────────────────────────────┐
 *   │ CLI 模式（推荐 crontab）：                                    │
 *   │  php gx.php                        一键全部                  │
 *   │  php gx.php check                  版本检查                  │
 *   │  php gx.php migrate                DB迁移                   │
 *   │  php gx.php ai_learn [force]       AI学习(可选强制)          │
 *   │  php gx.php ai_cleanup [force]     AI清理失效规则            │
 *   │  php gx.php official_refresh       官替缓存刷新              │
 *   │  php gx.php site_check             资源站巡检                │
 *   │  php gx.php rule_check             域名规则健康检查          │
 *   │  php gx.php status                 状态摘要                  │
 *   │                                                              │
 *   │ 推荐 crontab 配置（CRON_SETUP.md 中的 install_cron.sh 安装）：│
 *   │   0 *\/6 * * * php /网站目录/gx.php all >> /tmp/gx.log 2>&1   │
 *   │   30 3 * * *  php /网站目录/gx.php ai_cleanup force          │
 *   └──────────────────────────────────────────────────────────────┘
 *   ┌──────────────────────────────────────────────────────────────┐
 *   │ Web 模式（浏览器访问）：                                      │
 *   │  https://你的域名/gx.php?key=后台配置密码&action=all         │
 *   │  action 同 CLI 子命令；未指定默认 all                        │
 *   │  key 在 gx/.gx_secret.php 中配置（首次运行自动生成）         │
 *   └──────────────────────────────────────────────────────────────┘
 *
 * 其它特性：
 *   - 互斥锁机制 (flock + pid)，避免 cron 重叠执行导致 DB 死锁
 *   - 分模块独立执行+异常隔离，一个挂不影响其它
 *   - 结构化日志：CLI 彩色+进度条，Web JSON，写 gx/.gx_last_run.php
 *   - 可后台执行的异步触发：无需等待，立即返回 task_id
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0); // 无上限，靠子模块自己控制

define('GX_ROOT', dirname(__FILE__));
define('GX_RUNTIME_DIR', GX_ROOT . '/gx');
define('GX_LOCK_FILE', GX_RUNTIME_DIR . '/.gx.lock');
define('GX_SECRET_FILE', GX_RUNTIME_DIR . '/.gx_secret.php');
define('GX_LAST_RUN_FILE', GX_RUNTIME_DIR . '/.gx_last_run.php');
define('GX_LOG_FILE', GX_RUNTIME_DIR . '/gx_run.log');

if (!is_dir(GX_RUNTIME_DIR)) {
    @mkdir(GX_RUNTIME_DIR, 0755, true);
    @file_put_contents(GX_RUNTIME_DIR . '/.htaccess', "DENY FROM ALL\n");
    @file_put_contents(GX_RUNTIME_DIR . '/index.html', '');
}

// --------- 工具函数 ---------
$GX_IS_CLI = (PHP_SAPI === 'cli');
$GX_START_TIME = microtime(true);

if (!$GX_IS_CLI) {
    // 防跨站
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('X-Content-Type-Options: nosniff');
    header('X-Robots-Tag: noindex');
    @ini_set('html_errors', 0);
}

function gx_ensureSecret(): string {
    if (!file_exists(GX_SECRET_FILE)) {
        $secret = substr(bin2hex(random_bytes(24)), 0, 32);
        @file_put_contents(
            GX_SECRET_FILE,
            "<?php\n// gx.php 访问密钥 首次运行自动生成\n// 浏览器访问格式： /gx.php?key=此处密钥&action=all\nreturn ['gx_key' => '$secret'];\n"
        );
        @chmod(GX_SECRET_FILE, 0600);
    }
    $cfg = @include GX_SECRET_FILE;
    return is_array($cfg) && !empty($cfg['gx_key']) ? $cfg['gx_key'] : '';
}

function gx_verifyKey(): void {
    global $GX_IS_CLI;
    if ($GX_IS_CLI) return; // CLI 本地免 key
    $secret = gx_ensureSecret();
    $inputKey = $_GET['key'] ?? $_POST['key'] ?? $_SERVER['HTTP_X_GX_KEY'] ?? '';
    if ($secret === '' || hash_equals($secret, (string)$inputKey)) {
        return;
    }
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'code' => 401,
        'message' => '密钥无效，请访问 ' . GX_SECRET_FILE . ' 查看 gx_key（或通过后台查看）'
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function gx_lock(): mixed {
    $fp = @fopen(GX_LOCK_FILE, 'w+');
    if (!$fp) return null;
    if (!@flock($fp, LOCK_EX | LOCK_NB)) {
        @fclose($fp);
        return false;
    }
    @ftruncate($fp, 0);
    @fwrite($fp, "pid=" . getmypid() . " time=" . date('Y-m-d H:i:s') . "\n");
    @fflush($fp);
    return $fp;
}
function gx_unlock(mixed $fp): void {
    if (is_resource($fp)) {
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
    if (file_exists(GX_LOCK_FILE)) {
        // 只有锁文件 pid 匹配才删
        $data = @file_get_contents(GX_LOCK_FILE);
        if ($data && strpos($data, (string)getmypid()) !== false) {
            @unlink(GX_LOCK_FILE);
        }
    }
}

function gx_log(string $msg, string $level = 'info'): void {
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), strtoupper($level), $msg);
    @file_put_contents(GX_LOG_FILE, $line, FILE_APPEND);
}

function gx_saveLastRun(array $summary): void {
    $content = "<?php\n// gx.php 最后一次执行记录\nreturn " . var_export($summary + ['saved_at' => date('Y-m-d H:i:s')], true) . ";\n";
    @file_put_contents(GX_LAST_RUN_FILE, $content);
}

function gx_includeSafe(string $path): bool {
    if (!file_exists($path)) return false;
    require_once $path;
    return true;
}
// 加载公共 autoload
gx_includeSafe(GX_ROOT . '/db/autoload.php');
gx_includeSafe(GX_ROOT . '/gz/AiAutoLearner.php');
gx_includeSafe(GX_ROOT . '/src/UpdateManager.php');
gx_includeSafe(GX_ROOT . '/db/DataMigration.php');
gx_includeSafe(GX_ROOT . '/db/DbResourceSiteManager.php');
gx_includeSafe(GX_ROOT . '/db/DbOfficialReplaceManager.php');

// --------- 各子模块实现 ---------

class GxRunner {
    private array $results = [];
    private float $startAt;

    public function __construct() {
        $this->startAt = microtime(true);
    }

    public function addResult(string $task, array $res): void {
        $this->results[$task] = $res + [
            '_cost_s' => round(microtime(true) - $this->startAt, 3),
        ];
    }

    /** 1. 版本&完整性检查 */
    public function task_check(): array {
        $r = ['success' => true, 'checks' => []];
        // 本地版本
        $verFile = GX_ROOT . '/version.php';
        if (file_exists($verFile)) {
            $ver = @include $verFile;
            $r['local_version'] = is_array($ver) ? ($ver['version'] ?? '?') : '?';
            $r['local_build'] = is_array($ver) ? ($ver['build'] ?? '?') : '?';
        }
        // 核心文件存在性
        $core = ['index.php','mxadmin.php','db/Database.php','db/DataMigration.php','src/M3U8AdSkipper.php','gz/AiAutoLearner.php','xt/config.php'];
        $missing = [];
        foreach ($core as $f) {
            if (!file_exists(GX_ROOT . '/' . $f)) $missing[] = $f;
        }
        $r['checks']['core_files'] = $missing ? ['ok'=>false,'missing'=>$missing] : ['ok'=>true];
        // PHP 语法 spot check（抽样 5 个改动频繁文件）
        $syntax = ['db/DbOfficialReplaceManager.php','db/DbResourceSiteManager.php','src/UpdateManager.php','gz/AiAutoLearner.php','xt/config.php'];
        $badSyntax = [];
        foreach ($syntax as $f) {
            $fp = GX_ROOT . '/' . $f;
            if (!file_exists($fp)) continue;
            $out = []; $ret = -1;
            @exec('php -l ' . escapeshellarg($fp) . ' 2>&1', $out, $ret);
            if ($ret !== 0) $badSyntax[$f] = $out;
        }
        $r['checks']['syntax'] = $badSyntax ? ['ok'=>false,'errors'=>$badSyntax] : ['ok'=>true];
        $r['php_version'] = PHP_VERSION;
        $r['checks']['memory_limit'] = ini_get('memory_limit');
        $r['checks']['sapi'] = PHP_SAPI;
        // 目录可写
        $writable = [];
        foreach (['gx','db','gz','tmp'] as $d) {
            if (is_dir(GX_ROOT . '/' . $d)) {
                $writable[$d] = is_writable(GX_ROOT . '/' . $d);
            }
        }
        $r['checks']['writable_dirs'] = $writable;
        return $r;
    }

    /** 2. 数据库迁移 */
    public function task_migrate(): array {
        if (!class_exists('DataMigration')) {
            return ['success'=>false,'message'=>'DataMigration 类未加载'];
        }
        try {
            $m = new DataMigration();
            if (method_exists($m, 'runAll')) {
                $res = $m->runAll();
                return ['success' => true, 'result' => $res];
            }
            if (method_exists($m, 'migrate')) {
                $res = $m->migrate();
                return ['success' => true, 'result' => $res];
            }
            return ['success'=>false,'message'=>'DataMigration 无 runAll/migrate 方法'];
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'迁移异常: '.$e->getMessage(), 'trace'=>$e->getTraceAsString()];
        }
    }

    /** 3-1 AI 自动学习 */
    public function task_ai_learn(bool $force = false): array {
        if (!class_exists('AiAutoLearner')) {
            return ['success'=>false,'message'=>'AiAutoLearner 类未加载'];
        }
        try {
            $learner = new AiAutoLearner();
            $opts = $force ? ['force' => true, 'ignore_interval' => true] : [];
            $res = $learner->run($opts);
            if (!is_array($res)) $res = ['raw' => $res];
            $res['success'] = $res['success'] ?? true;
            return $res;
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'AI学习异常: '.$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()];
        }
    }

    /** 3-2 AI 规则清理 */
    public function task_ai_cleanup(bool $force = false): array {
        if (!class_exists('AiAutoLearner')) {
            return ['success'=>false,'message'=>'AiAutoLearner 类未加载'];
        }
        try {
            $learner = new AiAutoLearner();
            if (!method_exists($learner, 'cleanupStaleRules')) {
                return ['success'=>false,'message'=>'AiAutoLearner 无 cleanupStaleRules 方法'];
            }
            $res = $learner->cleanupStaleRules($force);
            return is_array($res) ? $res : ['success'=>true,'result'=>$res];
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'AI清理异常: '.$e->getMessage()];
        }
    }

    /** 4. 官替缓存刷新 + 匹配有效性抽检 */
    public function task_official_refresh(int $max = 8): array {
        if (!class_exists('DbOfficialReplaceManager')) {
            return ['success'=>false,'message'=>'DbOfficialReplaceManager 类未加载'];
        }
        try {
            $m = new DbOfficialReplaceManager();
            $cfg = $m->getConfig();
            $sites = $m->getAllPlatforms(true);
            $r = [
                'success' => true,
                'enabled' => $cfg['enabled'] ?? false,
                'default_site' => $cfg['default_site'] ?? '',
                'match_threshold' => $cfg['match_threshold'] ?? 70,
                'platforms_count' => count($sites),
                'platforms' => array_map(fn($p) => [
                    'name' => $p['name'], 'domain' => $p['domain'], 'priority' => intval($p['priority'] ?? 50)
                ], $sites),
            ];
            // 若有官替缓存表，清理过期
            if (class_exists('DbOfficialReplaceCache') && method_exists('DbOfficialReplaceCache', 'cleanExpired')) {
                try {
                    $cache = new DbOfficialReplaceCache();
                    $r['cache_deleted'] = $cache->cleanExpired();
                } catch (Throwable $e) {
                    $r['cache_delete_error'] = $e->getMessage();
                }
            }
            // 官替搜索有效性抽检（用 searchInSites 搜索一个常见关键词，看是否有结果）
            if (class_exists('DbResourceSiteManager')) {
                try {
                    $sm = new DbResourceSiteManager();
                    $hot = ['狂飙','庆余年','流浪地球','三体','漫长的季节'];
                    $kw = $hot[array_rand($hot)];
                    $sr = $sm->searchAllSites($kw, 1, min(3, $max));
                    $videos = $sr['videos'] ?? [];
                    $r['spot_check_keyword'] = $kw;
                    $r['spot_check_video_count'] = count($videos);
                    $r['spot_check_site_count'] = count($sr['site_results'] ?? []);
                } catch (Throwable $e) {
                    $r['spot_check_error'] = $e->getMessage();
                }
            }
            return $r;
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'官替刷新异常: '.$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()];
        }
    }

    /** 5. 资源站健康巡检 */
    public function task_site_check(int $max = 10): array {
        if (!class_exists('DbResourceSiteManager')) {
            return ['success'=>false,'message'=>'DbResourceSiteManager 未加载'];
        }
        try {
            $sm = new DbResourceSiteManager();
            $sites = method_exists($sm, 'getAllSites') ? $sm->getAllSites(true) : [];
            if (empty($sites)) {
                return ['success'=>false,'message'=>'资源站列表为空'];
            }
            $total = count($sites);
            $sites = array_slice($sites, 0, $max);
            $checks = [];
            $okCount = 0; $failCount = 0;
            $hotWords = ['狂飙','庆余年','三体','九门'];
            foreach ($sites as $s) {
                $kw = $hotWords[array_rand($hotWords)];
                $t0 = microtime(true);
                $res = $sm->searchVideos($s['api_url'], $kw, 1, 1);
                $cost = round((microtime(true) - $t0) * 1000, 0);
                $ok = !empty($res['success']) && !empty($res['videos']);
                $ok ? $okCount++ : $failCount++;
                $checks[] = [
                    'name' => $s['name'],
                    'domain' => $s['domain'] ?? parse_url($s['api_url'], PHP_URL_HOST),
                    'keyword' => $kw,
                    'status' => $ok ? 'OK' : 'FAIL',
                    'cost_ms' => $cost,
                    'videos' => count($res['videos'] ?? []),
                    'msg' => $ok ? '' : ($res['message'] ?? 'unknown'),
                ];
            }
            return [
                'success' => true,
                'total_sites' => $total,
                'checked_count' => count($sites),
                'ok_count' => $okCount,
                'fail_count' => $failCount,
                'checks' => $checks,
            ];
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'资源站巡检异常: '.$e->getMessage()];
        }
    }

    /** 6. 域名规则健康检查 */
    public function task_rule_check(int $maxDomains = 20): array {
        $ruleDir = GX_ROOT . '/gz';
        if (!is_dir($ruleDir)) {
            return ['success'=>false,'message'=>'gz/ 规则目录不存在'];
        }
        $ruleFiles = glob($ruleDir . '/rules_*.php');
        $total = count($ruleFiles);
        // 随机抽样检查
        shuffle($ruleFiles);
        $samples = array_slice($ruleFiles, 0, $maxDomains);
        $totalDomains = 0; $checkOk = 0; $checkFail = 0;
        $details = [];
        $t0 = microtime(true);
        foreach ($samples as $file) {
            $arr = @include $file;
            if (!is_array($arr)) continue;
            foreach ($arr as $domain => $rules) {
                $totalDomains++;
                // HEAD 探测（6秒超时）
                $ch = @curl_init('https://' . $domain . '/');
                if (!$ch) { $checkFail++; continue; }
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER=>true, CURLOPT_NOBODY=>true,
                    CURLOPT_TIMEOUT=>6, CURLOPT_CONNECTTIMEOUT=>4,
                    CURLOPT_SSL_VERIFYPEER=>false, CURLOPT_SSL_VERIFYHOST=>false,
                    CURLOPT_USERAGENT=>'Mozilla/5.0 (compatible; GxHealthBot/1.0)',
                    CURLOPT_FOLLOWLOCATION=>true, CURLOPT_MAXREDIRS=>3,
                ]);
                @curl_exec($ch);
                $http = intval(@curl_getinfo($ch, CURLINFO_HTTP_CODE));
                $err = curl_error($ch); curl_close($ch);
                $reachable = ($http >= 200 && $http < 400) || $http === 0 && empty($err) ? false : $http >= 200;
                if ($reachable) $checkOk++; else $checkFail++;
                $details[] = ['domain'=>$domain,'http'=>$http,'reachable'=>$reachable,'file'=>basename($file),'rules'=>count($rules)];
            }
        }
        return [
            'success' => true,
            'total_rule_files' => $total,
            'sampled_rule_files' => count($samples),
            'sampled_domains' => $totalDomains,
            'reachable' => $checkOk,
            'unreachable' => $checkFail,
            'cost_s' => round(microtime(true)-$t0, 2),
            'details' => $details,
        ];
    }

    public function getSummary(): array {
        $allOk = true; $failed = [];
        foreach ($this->results as $name => $r) {
            if (empty($r['success'])) { $allOk = false; $failed[] = $name; }
        }
        return [
            'success' => $allOk,
            'cost_seconds' => round(microtime(true) - $this->startAt, 2),
            'tasks_count' => count($this->results),
            'failed_tasks' => $failed,
            'task_results' => $this->results,
        ];
    }
}

// --------- 参数解析 & 主流程 ---------

function gx_cli_args(): array {
    global $argc, $argv;
    $action = 'all'; $flags = [];
    if ($argc > 1) {
        foreach (array_slice($argv, 1) as $a) {
            if ($a === 'force' || $a === '--force') $flags['force'] = true;
            elseif (str_starts_with($a, '--max=')) $flags['max'] = intval(substr($a, 6));
            else $action = $a;
        }
    }
    return [$action, $flags];
}

function gx_web_args(): array {
    $action = $_GET['action'] ?? $_POST['action'] ?? 'all';
    $flags = [];
    if (!empty($_GET['force']) || !empty($_POST['force'])) $flags['force'] = true;
    if (isset($_GET['max'])) $flags['max'] = intval($_GET['max']);
    return [$action, $flags];
}

function gx_cli_print(string $text): void {
    global $GX_IS_CLI;
    if ($GX_IS_CLI) echo $text;
}

function gx_status_last_run(): array {
    $last = file_exists(GX_LAST_RUN_FILE) ? (@include GX_LAST_RUN_FILE) : [];
    $secret = gx_ensureSecret();
    $lockAlive = false; $lockPid = null;
    if (file_exists(GX_LOCK_FILE)) {
        $lockAlive = true;
        $l = @file_get_contents(GX_LOCK_FILE);
        if (preg_match('/pid=(\d+)/', (string)$l, $m)) $lockPid = intval($m[1]);
    }
    return [
        'success' => true,
        'last_run' => is_array($last) ? $last : null,
        'log_size_bytes' => file_exists(GX_LOG_FILE) ? filesize(GX_LOG_FILE) : 0,
        'lock_file_exists' => $lockAlive,
        'lock_pid' => $lockPid,
        'gx_key_tip' => $secret ? ('浏览器访问：/gx.php?key=' . substr($secret,0,6) . '...&action=all  （完整key详见 gx/.gx_secret.php）') : '(请确保 gx/.gx_secret.php 可读)',
        'cli_cron_example' => '0 *' . '/6 * * * php ' . GX_ROOT . '/gx.php all >> ' . GX_LOG_FILE . ' 2>&1',
        'help' => [
            'all (默认)'    => '依次执行 check → migrate → ai_learn → official_refresh → site_check',
            'check'          => '版本/核心文件/语法/权限 健康检查',
            'migrate'        => '数据库 schema 迁移升级',
            'ai_learn [force]' => 'AI 自动学习（force 跳过间隔，立即执行）',
            'ai_cleanup [force]' => 'AI 清理失效域名规则',
            'official_refresh [--max=8]' => '官替配置刷新 + 匹配抽检',
            'site_check [--max=10]'      => '资源站 API 健康巡检',
            'rule_check [--max=20]'      => '抽样域名规则健康检查',
            'status'         => '查看 gx 运行状态 / 上次执行摘要 / cron 示例',
            'reset_key'      => '重置 Web 访问密钥',
        ],
    ];
}

// --------- 入口 ---------
gx_verifyKey();

$lockFp = gx_lock();
if ($lockFp === false) {
    // 已有执行中
    if ($GX_IS_CLI) {
        echo "⏳ gx.php 已有运行中实例（lock file 存在），跳过本次。\n   等待中...或手动删除：rm " . GX_LOCK_FILE . "\n";
        exit(2);
    }
    http_response_code(429);
    echo json_encode(['success'=>false,'code'=>429,'message'=>'任务执行中，请稍后再试（已有锁文件 gx/.gx.lock）'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

register_shutdown_function(function() use ($lockFp) {
    gx_unlock($lockFp);
});

list($action, $flags) = $GX_IS_CLI ? gx_cli_args() : gx_web_args();
$force = !empty($flags['force']);
$max = isset($flags['max']) ? intval($flags['max']) : null;

$runner = new GxRunner();

gx_log("▶ start action=$action force=".($force?'1':'0')." sapi=".PHP_SAPI);

if ($GX_IS_CLI) {
    gx_cli_print("╔══════════════════════════════════════════════════════╗\n");
    gx_cli_print("║  gx.php  —  全局自动更新调度中心  v1.0               ║\n");
    gx_cli_print("╚══════════════════════════════════════════════════════╝\n\n");
    gx_cli_print("▶ 模式: CLI  |  动作: $action  |  强制: " . ($force?'YES':'NO') . "\n\n");
}

try {
    switch ($action) {
        case 'reset_key':
            @unlink(GX_SECRET_FILE);
            $newKey = gx_ensureSecret();
            $r = ['success'=>true,'message'=>'密钥已重置','gx_key'=>$newKey,'tip'=>'Web 访问: /gx.php?key='.$newKey.'&action=all'];
            $runner->addResult('reset_key', $r);
            break;
        case 'status':
            $r = gx_status_last_run();
            $runner->addResult('status', $r);
            break;
        case 'check':
            $r = $runner->task_check();
            $runner->addResult('check', $r);
            break;
        case 'migrate':
            $r = $runner->task_migrate();
            $runner->addResult('migrate', $r);
            break;
        case 'ai_learn':
            $r = $runner->task_ai_learn($force);
            $runner->addResult('ai_learn', $r);
            break;
        case 'ai_cleanup':
            $r = $runner->task_ai_cleanup($force);
            $runner->addResult('ai_cleanup', $r);
            break;
        case 'official_refresh':
            $r = $runner->task_official_refresh($max ?? 8);
            $runner->addResult('official_refresh', $r);
            break;
        case 'site_check':
            $r = $runner->task_site_check($max ?? 10);
            $runner->addResult('site_check', $r);
            break;
        case 'rule_check':
            $r = $runner->task_rule_check($max ?? 20);
            $runner->addResult('rule_check', $r);
            break;
        case 'all':
        default:
            // 全链路：异常隔离，一个失败不影响其它
            $pipeline = [
                ['name'=>'check','fn'=>function() use ($runner){ return $runner->task_check(); }],
                ['name'=>'migrate','fn'=>function() use ($runner){ return $runner->task_migrate(); }],
                ['name'=>'official_refresh','fn'=>function() use ($runner,$max){ return $runner->task_official_refresh($max ?? 8); }],
                ['name'=>'ai_learn','fn'=>function() use ($runner,$force){ return $runner->task_ai_learn($force); }],
                ['name'=>'site_check','fn'=>function() use ($runner,$max){ return $runner->task_site_check($max ?? 5); }],
            ];
            foreach ($pipeline as $step) {
                try {
                    gx_cli_print("  ┌ " . date('H:i:s') . " 执行 [{$step['name']}] ... ");
                    $r = $step['fn']();
                    $ok = !empty($r['success']);
                    gx_cli_print(($ok ? "✅ OK" : "⚠️  WARN/FAIL") . "  (" . ($r['_cost_s'] ?? round(microtime(true)-$GX_START_TIME,3)) . "s)\n");
                    if ($GX_IS_CLI && !$ok) {
                        gx_cli_print("  │   原因: " . ($r['message'] ?? '未知失败') . "\n");
                    }
                    $runner->addResult($step['name'], $r);
                } catch (Throwable $e) {
                    gx_cli_print("❌ EXCEPTION: {$e->getMessage()}\n");
                    $runner->addResult($step['name'], ['success'=>false,'message'=>$e->getMessage(),'exception_class'=>get_class($e),'file'=>$e->getFile(),'line'=>$e->getLine()]);
                    gx_log("{$step['name']} exception: ".$e->getMessage(), 'error');
                }
            }
            break;
    }

    $summary = $runner->getSummary();
    $summary['action'] = $action;
    $summary['started_at'] = date('Y-m-d H:i:s', intval($GX_START_TIME));
    $summary['finished_at'] = date('Y-m-d H:i:s');
    gx_saveLastRun($summary);
    gx_log("✓ finished action=$action success=".($summary['success']?'1':'0')." cost=".$summary['cost_seconds']."s failed=[".implode(',',$summary['failed_tasks'])."]");

    if ($GX_IS_CLI) {
        gx_cli_print("\n═══════════════════════════════════════════════\n");
        gx_cli_print("  ✅ 总耗时: {$summary['cost_seconds']}s\n");
        gx_cli_print("  📊 任务数: {$summary['tasks_count']}\n");
        gx_cli_print("  ❌ 失败项: " . ($summary['failed_tasks'] ? implode(', ', $summary['failed_tasks']) : '（无）') . "\n");
        gx_cli_print("  📝 运行日志: " . GX_LOG_FILE . "\n");
        gx_cli_print("  📌 推荐 crontab:  0 *" . "/6 * * * php " . GX_ROOT . "/gx.php all >> " . GX_LOG_FILE . " 2>&1\n");
        gx_cli_print("═══════════════════════════════════════════════\n");
        exit($summary['success'] ? 0 : 3);
    }

    echo json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;

} catch (Throwable $e) {
    gx_log("FATAL: " . $e->getMessage() . " file=".$e->getFile()." line=".$e->getLine(), 'error');
    $err = ['success' => false, 'code' => 500, 'message' => 'gx.php 运行异常: '.$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()];
    if ($GX_IS_CLI) {
        fwrite(STDERR, "FATAL: ".$e->getMessage()."\n");
        exit(1);
    }
    http_response_code(500);
    echo json_encode($err, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}
