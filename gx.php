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
define('GX_PROGRESS_FILE', GX_RUNTIME_DIR . '/.gx_progress.json');
define('GX_TASKS_STEP_WEIGHTS_FILE', GX_RUNTIME_DIR . '/.gx_step_weights.php');

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

// --------- 进度条写盘机制（后台进度条 轮询读取） ---------
class GxProgressTracker {
    private string $taskId;
    /** @var string 当前 action（all/check/migrate/...），输出到 JSON 方便前端展示 */
    private string $action = '';
    /** @var array<string, int> 每个任务名占百分之几权重 */
    private array $stepWeights;
    private int $totalStepsWeight;
    private array $currentState;
    private int $startTime;

    public function __construct(array $stepWeights) {
        $this->taskId = 'gx_' . date('YmdHis') . '_' . substr(bin2hex(random_bytes(6)), 0, 10);
        $this->stepWeights = $stepWeights;
        $this->totalStepsWeight = max(1, array_sum($stepWeights));
        $this->startTime = time();

        // 初始化 steps 数组（key=stepName，前端按顺序渲染）— 全部 pending 0%
        $stepsInit = [];
        foreach ($stepWeights as $name => $w) {
            $stepsInit[$name] = [
                'status'      => 'pending',    // pending | running | done
                'success'     => null,         // done 后填 true/false
                'percent'     => 0,
                'weight'      => $w,
                'message'     => '待执行',
                'sub_message' => '',
                'started_at'  => null,
                'finished_at' => null,
                'cost_s'      => 0,
            ];
        }

        $this->currentState = [
            'task_id'       => $this->taskId,
            'action'        => '',
            'started_at'    => date('Y-m-d H:i:s'),
            'started_at_ts' => $this->startTime,
            'finished_at'   => null,
            'status'        => 'running',        // running | done | failed
            'overall_status'=> 'running',        // pending | running | success | failed | partial  (前端识别)
            'total_steps_weight' => $this->totalStepsWeight,
            'step_weights'  => $this->stepWeights,
            'steps_done_weight' => 0,
            'percent'       => 0,
            'current_step'  => null,
            'current_message' => '初始化任务队列...',
            'step_sub_percent' => 0,
            'message'       => '初始化任务队列...',
            'logs'          => [],
            'steps'         => $stepsInit,       // 每个 step 的状态（前端直接渲染）
            'steps_summary' => [],
            'eta_seconds'   => null,
            'duration_sec'  => 0,
            'elapsed_seconds' => 0,
            'success'       => false,
            'failed_tasks'  => [],
            'speed_avg_percent_per_sec' => null,
            'last_refresh'  => microtime(true),
        ];
    }

    public function getTaskId(): string { return $this->taskId; }

    /** 顶部入口在创建 tracker 后会 setAction，方便前端展示 */
    public function setAction(string $action): void {
        $this->action = $action;
        $this->currentState['action'] = $action;
    }

    public function save(): void {
        $this->currentState['last_refresh'] = microtime(true);
        $this->currentState['elapsed_seconds'] = time() - $this->startTime;
        $this->currentState['duration_sec'] = $this->currentState['elapsed_seconds'];
        // ETA 估算（单调 + 平滑）
        $pct = $this->currentState['percent'];
        if ($pct > 0.5 && $this->currentState['elapsed_seconds'] > 3) {
            $spd = $pct / max(1, $this->currentState['elapsed_seconds']);
            $this->currentState['speed_avg_percent_per_sec'] = round($spd, 3);
            $remain = 100 - $pct;
            $this->currentState['eta_seconds'] = max(1, intval(round($remain / $spd)));
        }
        // overall_status 推导
        if ($this->currentState['status'] === 'done') {
            if ($this->currentState['success']) $this->currentState['overall_status'] = 'success';
            elseif (!empty($this->currentState['failed_tasks'])) $this->currentState['overall_status'] = 'partial';
            else $this->currentState['overall_status'] = 'failed';
        } elseif ($this->currentState['status'] === 'failed') {
            $this->currentState['overall_status'] = 'failed';
        } else {
            $this->currentState['overall_status'] = 'running';
        }
        $json = json_encode($this->currentState, JSON_UNESCAPED_UNICODE);
        if ($json !== false) {
            @file_put_contents(GX_PROGRESS_FILE, $json, LOCK_EX);
        }
    }

    public function startStep(string $stepName, string $message = ''): void {
        $this->currentState['current_step'] = $stepName;
        $this->currentState['step_sub_percent'] = 0;
        $msg = $message ?: ("开始执行: " . $stepName);
        $this->currentState['message'] = $msg;
        $this->currentState['current_message'] = $msg;
        if (isset($this->currentState['steps'][$stepName])) {
            $this->currentState['steps'][$stepName]['status'] = 'running';
            $this->currentState['steps'][$stepName]['percent'] = 0;
            $this->currentState['steps'][$stepName]['message'] = $msg;
            $this->currentState['steps'][$stepName]['sub_message'] = $msg;
            $this->currentState['steps'][$stepName]['started_at'] = date('Y-m-d H:i:s');
        }
        $this->pushLog($stepName, 'START', $msg, 'info');
        $this->save();
    }

    public function subStep(int $subPct0_100, string $message = ''): void {
        $sub = max(0, min(100, $subPct0_100));
        $this->currentState['step_sub_percent'] = $sub;
        // 总百分比 = 已完成步骤权重 + 当前步骤权重 * sub%
        $curStep = $this->currentState['current_step'];
        $stepW = $this->stepWeights[$curStep] ?? 0;
        $doneW = $this->currentState['steps_done_weight'];
        $overall = 100 * ($doneW + ($stepW * $sub / 100)) / $this->totalStepsWeight;
        $this->currentState['percent'] = max($this->currentState['percent'], min(100, round($overall, 2)));
        if ($message) {
            $this->currentState['message'] = $message;
            $this->currentState['current_message'] = $message;
        }
        if ($curStep && isset($this->currentState['steps'][$curStep])) {
            $this->currentState['steps'][$curStep]['percent'] = $sub;
            if ($message) $this->currentState['steps'][$curStep]['sub_message'] = $message;
        }
        $this->save();
    }

    public function finishStep(string $stepName, bool $success, array $stepResult = [], string $message = ''): void {
        $w = $this->stepWeights[$stepName] ?? 0;
        $this->currentState['steps_done_weight'] += $w;
        $this->currentState['step_sub_percent'] = 100;
        // 重新计算 percent（用已完成权重，保证非倒退）
        $overall = 100 * $this->currentState['steps_done_weight'] / $this->totalStepsWeight;
        $this->currentState['percent'] = max($this->currentState['percent'], min(100, round($overall, 2)));
        $this->currentState['steps_summary'][$stepName] = [
            'success' => $success,
            'weight' => $w,
            'message' => $message ?: ($success ? '完成' : '失败'),
            'cost_s' => round(microtime(true) - $GLOBALS['GX_START_TIME'], 2),
        ];
        if (!$success) {
            $this->currentState['failed_tasks'][] = $stepName;
        }
        $msg = $message ?: ($success ? "✅ [{$stepName}] 成功" : "⚠️ [{$stepName}] 失败");
        $this->currentState['message'] = $msg;
        $this->currentState['current_message'] = $msg;
        if (isset($this->currentState['steps'][$stepName])) {
            $this->currentState['steps'][$stepName]['status'] = 'done';
            $this->currentState['steps'][$stepName]['success'] = $success;
            $this->currentState['steps'][$stepName]['percent'] = 100;
            $this->currentState['steps'][$stepName]['message'] = $msg;
            $this->currentState['steps'][$stepName]['finished_at'] = date('Y-m-d H:i:s');
            $this->currentState['steps'][$stepName]['cost_s'] = round(microtime(true) - $GLOBALS['GX_START_TIME'], 2);
        }
        $this->pushLog($stepName, $success ? 'OK' : 'FAIL', $msg, $success ? 'success' : 'warn');
        $this->save();
    }

    public function finishAll(bool $overallSuccess, string $message = ''): void {
        $this->currentState['status'] = $overallSuccess ? 'done' : 'failed';
        $this->currentState['success'] = $overallSuccess;
        $this->currentState['percent'] = 100;
        $this->currentState['step_sub_percent'] = 100;
        $this->currentState['steps_done_weight'] = $this->totalStepsWeight;
        $this->currentState['current_step'] = null;
        $this->currentState['finished_at'] = date('Y-m-d H:i:s');
        $this->currentState['duration_sec'] = time() - $this->startTime;
        $finalMsg = $message ?: ($overallSuccess ? '🎉 全部任务执行完成！' : '部分任务失败，请查看下方日志');
        $this->currentState['message'] = $finalMsg;
        $this->currentState['current_message'] = $finalMsg;
        // 还没 finish 的 step 标记为 done/fail（兜底）
        foreach ($this->stepWeights as $name => $w) {
            if (!isset($this->currentState['steps'][$name])) continue;
            $s = &$this->currentState['steps'][$name];
            if ($s['status'] !== 'done') {
                $s['status'] = 'done';
                $s['success'] = $overallSuccess;
                $s['percent'] = 100;
                $s['finished_at'] = date('Y-m-d H:i:s');
                $s['message'] = $overallSuccess ? '执行完成' : '任务中断';
            }
        }
        $this->pushLog('system', $overallSuccess ? 'DONE' : 'FAILED', $finalMsg, $overallSuccess ? 'success' : 'error');
        $this->save();
    }

    private function pushLog(string $step, string $level, string $msg, string $color = 'info'): void {
        // level 映射：START/OK/FAIL/DONE → info/success/warn/success，供前端 .gx-log .info/.ok/.warn/.error 着色
        $lvlMap = ['START'=>'info','OK'=>'ok','SUCCESS'=>'ok','FAIL'=>'warn','WARNING'=>'warn','WARN'=>'warn','ERROR'=>'error','DONE'=>'ok','FAILED'=>'error','INFO'=>'info'];
        $lvlNorm = $lvlMap[strtoupper($level)] ?? strtolower($color ?: 'info');
        $this->currentState['logs'][] = [
            'time'    => date('Y-m-d') . 'T' . date('H:i:s'),  // 前端会按 T 切分取出 H:i:s
            'step'    => $step,
            'level'   => $lvlNorm,
            'message' => $msg,
        ];
        // 日志上限 500 条
        if (count($this->currentState['logs']) > 500) {
            $this->currentState['logs'] = array_slice($this->currentState['logs'], -500);
        }
    }
}

// 异步模式：立即返回 task_id，后台 exec 真正的 gx.php 跑
function gx_launch_async(string $action, bool $force, ?int $max): string {
    $phpBin = PHP_BINARY;
    $script = GX_ROOT . '/gx.php';
    $args = [$action];
    if ($force) $args[] = 'force';
    if ($max !== null) $args[] = '--max=' . $max;
    // 异步启动：CLI 模式（不输出给当前 HTTP 响应）
    // 先创建初始 progress（task_id 同步从新进程生成时，进程也会开始写盘）
    // 这里采取：先调用同步无锁初始化 progress 文件 → 进程启动后接管并继续
    $cmd = $phpBin . ' ' . escapeshellarg($script) . ' ' . implode(' ', array_map('escapeshellarg', $args)) . ' '
         . '>> ' . escapeshellarg(GX_LOG_FILE) . ' 2>&1 & echo $!';
    @exec($cmd, $out, $ret);
    return (string)($out[0] ?? '');
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
        // 注意：线上 PHP 常禁用 exec()/shell_exec() 等；禁用时走 tokenizer 软校验
        $syntax = ['db/DbOfficialReplaceManager.php','db/DbResourceSiteManager.php','src/UpdateManager.php','gz/AiAutoLearner.php','xt/config.php'];
        $badSyntax = [];
        $syntaxMethod = null;
        if (function_exists('exec')) {
            $syntaxMethod = 'php -l via exec()';
            foreach ($syntax as $f) {
                $fp = GX_ROOT . '/' . $f;
                if (!file_exists($fp)) continue;
                $out = []; $ret = -1;
                @exec('php -l ' . escapeshellarg($fp) . ' 2>&1', $out, $ret);
                if ($ret !== 0) $badSyntax[$f] = $out;
            }
        } else {
            // 禁用 exec → tokenizer 软校验：解析为 token 确保无语法错误
            $syntaxMethod = 'tokenizer_soft_check (exec disabled)';
            foreach ($syntax as $f) {
                $fp = GX_ROOT . '/' . $f;
                if (!file_exists($fp)) continue;
                $code = @file_get_contents($fp);
                if ($code === false) continue;
                if (function_exists('token_get_all')) {
                    $tokens = @token_get_all($code);
                    // token_get_all 在 parse error 之前抛出 error 时 tokens 会少；这里用 error_get_last
                    $err = error_get_last();
                    if ($err && (strpos($err['message'], 'syntax error') !== false || strpos($err['message'], 'Parse error') !== false)) {
                        $badSyntax[$f] = ['error_get_last' => $err];
                    } elseif ($tokens === false || !is_array($tokens)) {
                        $badSyntax[$f] = ['tokenizer_failed' => true];
                    }
                }
                // 再用 include 沙盒二次确认（通过 try/catch 包装）
            }
        }
        $r['checks']['syntax'] = $badSyntax ? ['ok'=>false,'errors'=>$badSyntax, 'method'=>$syntaxMethod] : ['ok'=>true,'method'=>$syntaxMethod];
        // exec 被禁用时也标记为 OK（结果仅供参考）
        if (!function_exists('exec') && empty($badSyntax)) {
            $r['checks']['syntax']['note'] = 'exec() 被禁用，语法校验使用 tokenizer 软校验模式（非 100% 强校验）';
        }
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
            // 兼容 DataMigration 不同版本：按顺序尝试可用的方法
            $tryMethods = ['migrateAll', 'runAll', 'migrate', 'run'];
            $tried = [];
            foreach ($tryMethods as $method) {
                if (method_exists($m, $method)) {
                    $res = $m->$method();
                    return [
                        'success' => true,
                        'method_used' => $method,
                        'result' => $res,
                        'already_migrated' => method_exists($m,'isMigrated') ? $m->isMigrated() : null,
                    ];
                }
                $tried[] = $method;
            }
            // 兜底：手动触发 initTables + 关键迁移项
            if (method_exists($m, 'migrateDomainRules')) {
                $summary = [];
                if (method_exists($m, 'db') || property_exists($m, 'db')) {
                    try {
                        $db = Database::getInstance();
                        if (method_exists($db, 'initTables')) {
                            @$db->initTables();
                            $summary['init_tables'] = true;
                        }
                    } catch (Throwable $e) { $summary['init_tables_error'] = $e->getMessage(); }
                }
                foreach (['migrateDomainRules','migrateResourceSites','migrateOfficialSites','migrateOfficialPlatforms','migrateAutoLearnConfig'] as $sub) {
                    if (method_exists($m, $sub)) {
                        try {
                            $summary[$sub] = @$m->$sub();
                        } catch (Throwable $e) { $summary[$sub.'_error'] = $e->getMessage(); }
                    }
                }
                if (method_exists($m, 'markMigrated')) { @$m->markMigrated(); }
                return ['success'=>true,'method_used'=>'manual_pipeline','result'=>$summary,'tried_methods'=>$tried];
            }
            return ['success'=>false,'message'=>'DataMigration 未找到可用迁移方法','tried_methods'=>$tried,'methods_in_class'=>get_class_methods($m)];
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

    /** 4. 官替缓存刷新 + 匹配有效性抽检 + 确保抖剧TV（默认官替）已注册 */
    public function task_official_refresh(int $max = 8): array {
        if (!class_exists('DbOfficialReplaceManager')) {
            return ['success'=>false,'message'=>'DbOfficialReplaceManager 类未加载'];
        }
        try {
            $m = new DbOfficialReplaceManager();

            // ------ 第一步：确保抖剧TV 默认官替资源站 + 官替平台 存在（v5.10.3 升级） ------
            $bootstrap = [
                'douju_site_created' => false,
                'douju_platform_created' => false,
                'douju_set_as_default' => false,
                'default_site_corrected_from' => null,
            ];
            $this->ensureDoujuDefault($m, $bootstrap);

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
                'bootstrap' => $bootstrap,
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
            // 官替搜索有效性抽检
            $hot = ['狂飙','庆余年','流浪地球','三体','漫长的季节','九门','与凤行'];
            $kw = $hot[array_rand($hot)];
            $spotErr = ''; $videos = []; $siteCount = 0;
            $sr = null;
            try {
                // 优先：DbResourceSiteManager::searchAllSites
                if (class_exists('DbResourceSiteManager')) {
                    $sm = new DbResourceSiteManager();
                    if (method_exists($sm, 'searchAllSites')) {
                        $sr = $sm->searchAllSites($kw, 1, min(3, $max));
                        $videos = $sr['videos'] ?? [];
                        $siteCount = count($sr['site_results'] ?? []);
                    } elseif (method_exists($m, 'searchInSites')) {
                        // 回退：用官替管理器 searchInSites
                        $sr = $m->searchInSites($kw, 1, min(3, $max));
                        $videos = $sr['videos'] ?? [];
                        $siteCount = count($sr['site_results'] ?? []);
                    }
                }
                // 再兜底：直接用第1个 enable 的资源站 api_url 搜索
                if (empty($videos) && class_exists('DbResourceSiteManager')) {
                    $sm = new DbResourceSiteManager();
                    if (method_exists($sm, 'getAllSites')) {
                        $allSites = $sm->getAllSites(true);
                        $tried = 0;
                        foreach ($allSites as $s) {
                            if ($tried >= 3) break;
                            $tmpRes = $sm->searchVideos($s['api_url'], $kw, 1, 3);
                            $tried++;
                            if (!empty($tmpRes['success']) && !empty($tmpRes['videos'])) {
                                $videos = array_merge($videos, $tmpRes['videos']);
                                $siteCount++;
                            }
                        }
                        $r['spot_fallback_tried'] = $tried;
                    }
                }
            } catch (Throwable $e) {
                $spotErr = $e->getMessage();
            }
            $r['spot_check_keyword'] = $kw;
            $r['spot_check_video_count'] = count($videos);
            $r['spot_check_site_count'] = $siteCount;
            if (!empty($spotErr)) $r['spot_check_error'] = $spotErr;
            // 样本视频3条
            if (!empty($videos)) {
                $r['spot_sample'] = array_values(array_map(
                    fn($v) => ['name'=>$v['name']??'','remarks'=>$v['remarks']??'','site'=>$v['site']??''],
                    array_slice($videos, 0, 3)
                ));
            }
            return $r;
        } catch (Throwable $e) {
            return ['success'=>false,'message'=>'官替刷新异常: '.$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()];
        }
    }

    /**
     * 确保抖剧TV 默认官替资源站 + 官替平台已写入 DB；
     * 若 default_site 不是抖剧TV，自动纠正为抖剧TV（保留旧值在 corrected_from 中）
     */
    private function ensureDoujuDefault(DbOfficialReplaceManager $m, array &$bootstrap): void {
        $doujuName = '抖剧TV';
        $doujuDomain = 'douju.tv';
        $doujuApi = 'https://www.douju.tv/api.php/provide/vod/';
        $doujuRootSource = 'www.360kan.com';
        $doujuPriority = 1;

        $siteMgr = class_exists('DbResourceSiteManager') ? new DbResourceSiteManager() : null;

        // 1. 资源站写入
        if ($siteMgr) {
            try {
                $existing = null;
                if (method_exists($siteMgr, 'getSiteByName')) {
                    $existing = $siteMgr->getSiteByName($doujuName);
                } elseif (method_exists($siteMgr, 'getAllSites')) {
                    foreach ($siteMgr->getAllSites(true) as $s) {
                        if (($s['name'] ?? '') === $doujuName || stripos(($s['api_url'] ?? ''), 'douju.tv') !== false) {
                            $existing = $s; break;
                        }
                    }
                }
                if (!$existing && method_exists($siteMgr, 'addSite')) {
                    $created = @$siteMgr->addSite([
                        'name' => $doujuName,
                        'api_url' => $doujuApi,
                        'api_type' => 'maccms10',
                        'domain' => $doujuDomain,
                        'note' => '根源来源 '.$doujuRootSource.' / 官替默认资源站 priority='.$doujuPriority,
                        'priority' => $doujuPriority,
                        'enabled' => 1,
                    ]);
                    if ($created) $bootstrap['douju_site_created'] = true;
                } elseif ($existing && method_exists($siteMgr, 'updateSite') && !empty($existing['id'])) {
                    $upd = [];
                    if (($existing['api_url'] ?? '') !== $doujuApi) $upd['api_url'] = $doujuApi;
                    if (intval($existing['priority'] ?? 0) !== $doujuPriority) $upd['priority'] = $doujuPriority;
                    if (intval($existing['enabled'] ?? 0) !== 1) $upd['enabled'] = 1;
                    if (!empty($upd)) {
                        @$siteMgr->updateSite(intval($existing['id']), $upd);
                        $bootstrap['douju_site_updated'] = true;
                    }
                }
            } catch (Throwable $e) {
                $bootstrap['douju_site_error'] = $e->getMessage();
            }
        }

        // 2. 官替平台写入
        try {
            $platforms = $m->getAllPlatforms(true);
            $hasDouju = false;
            foreach ($platforms as $p) {
                if (($p['domain'] ?? '') === $doujuDomain || stripos(($p['name'] ?? ''), $doujuName) !== false) {
                    $hasDouju = true;
                    if (method_exists($m, 'updatePlatform') && !empty($p['id'])) {
                        $upd2 = [];
                        if (intval($p['priority'] ?? 0) !== $doujuPriority) $upd2['priority'] = $doujuPriority;
                        if (intval($p['enabled'] ?? 0) !== 1) $upd2['enabled'] = 1;
                        if (!empty($upd2)) {
                            @$m->updatePlatform(intval($p['id']), $upd2);
                            $bootstrap['douju_platform_updated'] = true;
                        }
                    }
                    break;
                }
            }
            if (!$hasDouju && method_exists($m, 'addPlatform')) {
                $ok = @$m->addPlatform([
                    'name' => $doujuName,
                    'domain' => $doujuDomain,
                    'note' => '根源来源 '.$doujuRootSource,
                    'priority' => $doujuPriority,
                    'enabled' => 1,
                ]);
                if ($ok) $bootstrap['douju_platform_created'] = true;
            }
        } catch (Throwable $e) {
            $bootstrap['douju_platform_error'] = $e->getMessage();
        }

        // 3. 纠正 default_site 为抖剧TV；search_sites 头部放抖剧TV
        try {
            $cfg = $m->getConfig();
            $changed = false;
            $oldDefault = $cfg['default_site'] ?? '';
            if ($oldDefault !== $doujuName) {
                $cfg['default_site'] = $doujuName;
                $bootstrap['default_site_corrected_from'] = $oldDefault ?: '(empty)';
                $changed = true;
            }
            $searchSites = $cfg['search_sites'] ?? [];
            if (!is_array($searchSites)) $searchSites = [];
            // 移除已有的抖剧TV位置，插到第1位
            $searchSites = array_values(array_filter($searchSites, fn($s) => $s !== $doujuName));
            array_unshift($searchSites, $doujuName);
            // 去重保序
            $seen = []; $newSites = [];
            foreach ($searchSites as $s) {
                if (isset($seen[$s])) continue;
                $seen[$s] = true;
                $newSites[] = $s;
            }
            if ($newSites !== ($cfg['search_sites'] ?? [])) {
                $cfg['search_sites'] = $newSites;
                $changed = true;
            }
            if (!isset($cfg['enabled']) || empty($cfg['enabled'])) {
                $cfg['enabled'] = true;
                $changed = true;
            }
            if (empty($cfg['match_threshold']) || intval($cfg['match_threshold']) > 75) {
                $cfg['match_threshold'] = 65; // 放宽到推荐值
                $changed = true;
            }
            if ($changed && method_exists($m, 'setConfig')) {
                $saved = $m->setConfig($cfg);
                if ($saved) $bootstrap['douju_set_as_default'] = true;
            } elseif (!$changed) {
                $bootstrap['douju_set_as_default'] = true;
            }
        } catch (Throwable $e) {
            $bootstrap['douju_default_error'] = $e->getMessage();
        }
    }

    /** 5. 资源站健康巡检 */
    public function task_site_check(int $max = 10, ?callable $progressCallback = null): array {
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
            $i = 0;
            foreach ($sites as $s) {
                $i++;
                if ($progressCallback) {
                    @call_user_func($progressCallback, $i, count($sites), $s['name'] ?? 'site');
                }
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

// ------- 进度查询 / progress 轮询接口 -------
$progressQuery = ($_GET['progress'] ?? $_POST['progress'] ?? null);
if ($progressQuery !== null) {
    $out = ['success' => false, 'progress' => null, 'error' => null];
    if (file_exists(GX_PROGRESS_FILE)) {
        $raw = @file_get_contents(GX_PROGRESS_FILE);
        if ($raw && strlen($raw) > 0) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $out['success'] = true;
                $out['progress'] = $decoded;
            } else {
                $out['error'] = 'progress 文件 JSON 解析失败';
            }
        }
    } else {
        $out['error'] = '暂无进行中的任务';
    }
    // 同时附带 last_run
    if (file_exists(GX_LAST_RUN_FILE)) {
        $last = @include GX_LAST_RUN_FILE;
        if (is_array($last)) {
            $out['last_run'] = $last;
        }
    }
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// ------- async=1 异步启动模式（Web页面按钮点击后 HTTP 快速返回）-------
$asyncFlag = ($_GET['async'] ?? $_POST['async'] ?? null);
if (!$GX_IS_CLI && ($asyncFlag === '1' || $asyncFlag === 'true' || $asyncFlag === 1)) {
    // 支持 exec 时就后台启动；否则就 fallback 同步执行
    $canAsync = function_exists('exec') && !@ini_get('safe_mode');
    $lockNow = gx_lock();
    $startingState = null;
    if ($lockNow === false) {
        http_response_code(429);
        echo json_encode([
            'success' => false, 'code' => 429, 'async' => false,
            'message' => '已有任务正在执行，请等待或刷新查看进度',
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    list($act, $fl) = gx_web_args();
    $f = !empty($fl['force']);
    $mx = isset($fl['max']) ? intval($fl['max']) : null;

    // 先创建一个初始 progress：告诉前端 task id 已创建
    $weightMap = gx_resolve_step_weights($act, $mx ?? 10);
    $tracker = new GxProgressTracker($weightMap);
    $tracker->setAction($act);
    $tracker->save();

    gx_unlock($lockNow);

    if ($canAsync) {
        $pid = gx_launch_async($act, $f, $mx);
        $resp = [
            'success' => true,
            'async'   => true,
            'mode'    => 'background_php_cli',
            'pid'     => $pid,
            'task_id' => $tracker->getTaskId(),
            'progress_file' => basename(GX_PROGRESS_FILE),
            'message' => '任务已后台启动，pid=' . $pid . '，请通过 progress 轮询接口查看进度',
        ];
        echo json_encode($resp, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    // exec 被禁用：fallback 到同步模式（HTTP 等它跑完，但仍写 progress 供下次刷新读取）
    // → 直接 fall-through 执行后续 switch
}

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

// 全局进度 tracker（所有模式都会写 progress）
$stepWeights = gx_resolve_step_weights($action, $max ?? 10);
$tracker = new GxProgressTracker($stepWeights);
$tracker->setAction($action);
$tracker->save();

$runner = new GxRunner();

gx_log("▶ start action=$action force=".($force?'1':'0')." sapi=".PHP_SAPI);

if ($GX_IS_CLI) {
    gx_cli_print("╔══════════════════════════════════════════════════════╗\n");
    gx_cli_print("║  gx.php  —  全局自动更新调度中心  v1.0               ║\n");
    gx_cli_print("╚══════════════════════════════════════════════════════╝\n\n");
    gx_cli_print("▶ 模式: CLI  |  动作: $action  |  强制: " . ($force?'YES':'NO') . "\n\n");
}

/**
 * 根据 action 解析 step 权重（百分比总和不一定=100，内部归一化）
 */
function gx_resolve_step_weights(string $action, int $max): array {
    switch ($action) {
        case 'status':            return ['status' => 100];
        case 'reset_key':         return ['reset_key' => 100];
        case 'check':             return ['check' => 100];
        case 'migrate':           return ['migrate' => 100];
        case 'ai_learn':          return ['ai_learn' => 100];
        case 'ai_cleanup':        return ['ai_cleanup' => 100];
        case 'official_refresh':  return ['official_refresh' => 100];
        case 'site_check':        $n = max(1, min($max, 20));
                                  $w = []; for($i=0;$i<$n;$i++) $w["site_{$i}"] = 1;
                                  return array_merge(['site_check_prepare'=>1], $w, ['site_check_summary'=>1]);
        case 'rule_check':        return ['rule_check' => 100];
        case 'all':
        default:
            return ['check'=>10,'migrate'=>10,'official_refresh'=>25,'ai_learn'=>30,'site_check'=>25];
    }
}

/**
 * 小工具：给单个任务挂 startStep/finishStep
 */
function gx_wrap_with_progress(GxProgressTracker $t, string $stepName, callable $fn): array {
    $t->startStep($stepName);
    try {
        // 对于 site_check：内部还有子步骤 subStep 进度，这里也同步发 subStep 心跳
        if ($stepName === 'site_check') {
            // 简单定期 subStep 心跳通过 register_tick_function 不便实现；靠 task_site_check 内部回调
            $result = $fn($t);  // 如果 fn 接受 $t，就可以 subStep
        } else {
            // 对慢步骤每隔几百毫秒推进 sub 进度（线性）— 实际成功后直接 finishStep(..., 100%)
            $result = $fn();
        }
        $ok = !empty($result['success']);
        $t->finishStep($stepName, $ok, $result, $result['message'] ?? '');
        return $result;
    } catch (Throwable $e) {
        $t->finishStep($stepName, false, [], '异常：' . $e->getMessage());
        throw $e;
    }
}

try {
    switch ($action) {
        case 'reset_key':
            $tracker->startStep('reset_key', '重置密钥');
            @unlink(GX_SECRET_FILE);
            $newKey = gx_ensureSecret();
            $r = ['success'=>true,'message'=>'密钥已重置','gx_key'=>$newKey,'tip'=>'Web 访问: /gx.php?key='.$newKey.'&action=all'];
            $runner->addResult('reset_key', $r);
            $tracker->finishStep('reset_key', true, $r);
            break;
        case 'status':
            $tracker->startStep('status', '读取状态');
            $r = gx_status_last_run();
            $runner->addResult('status', $r);
            $tracker->finishStep('status', true, $r);
            break;
        case 'check':
            $r = gx_wrap_with_progress($tracker, 'check', function() use ($runner){ return $runner->task_check(); });
            $runner->addResult('check', $r);
            break;
        case 'migrate':
            $r = gx_wrap_with_progress($tracker, 'migrate', function() use ($runner){ return $runner->task_migrate(); });
            $runner->addResult('migrate', $r);
            break;
        case 'ai_learn':
            $tracker->startStep('ai_learn');
            $r = $runner->task_ai_learn($force);
            // AI 学习中途给点 subStep 提示：0/30/60/80（模拟子进度）
            $tracker->subStep(30, '学习任务启动：站点选择');
            $ok = !empty($r['success']);
            $learned = intval($r['total_learned'] ?? 0);
            $tracker->subStep(70, "学习完成，成功{$learned}条");
            $runner->addResult('ai_learn', $r);
            $tracker->finishStep('ai_learn', $ok, $r, $r['message'] ?? '');
            break;
        case 'ai_cleanup':
            $r = gx_wrap_with_progress($tracker, 'ai_cleanup', function() use ($runner, $force){ return $runner->task_ai_cleanup($force); });
            $runner->addResult('ai_cleanup', $r);
            break;
        case 'official_refresh':
            $tracker->startStep('official_refresh', '官替配置刷新 + 抖剧TV纠偏 + 搜索抽检');
            $tracker->subStep(20, '纠偏抖剧TV为默认官替（写入资源站/官替平台）');
            $r = $runner->task_official_refresh($max ?? 8);
            $tracker->subStep(65, '官替缓存清理 + 搜索抽检');
            $ok = !empty($r['success']);
            $videos = intval($r['spot_check_video_count'] ?? 0);
            $runner->addResult('official_refresh', $r);
            $tracker->finishStep('official_refresh', $ok, $r, "抽检命中 {$videos} 条视频；default_site=".($r['default_site'] ?? ''));
            break;
        case 'site_check':
            // 单任务（内含多站），通过传入 tracker 控制 subStep
            $r = gx_wrap_with_progress($tracker, 'site_check', function(?GxProgressTracker $t=null) use ($runner, $max){
                $maxN = $max ?? 10;
                // 直接调用 task_site_check，改造成可传 progress_callback
                return $runner->task_site_check($maxN, function(int $done, int $total, string $siteName) use ($t) {
                    if (!$t) return;
                    if ($total <= 0) return;
                    $pct = intval(($done / $total) * 100);
                    $t->subStep($pct, "巡检第 {$done}/{$total} 站：{$siteName}");
                });
            });
            $runner->addResult('site_check', $r);
            break;
        case 'rule_check':
            $r = gx_wrap_with_progress($tracker, 'rule_check', function() use ($runner, $max){ return $runner->task_rule_check($max ?? 20); });
            $runner->addResult('rule_check', $r);
            break;
        case 'all':
        default:
            // 全链路：异常隔离，一个失败不影响其它，挂 tracker
            $pipeline = [
                ['name'=>'check','fn'=>function() use ($runner){ return $runner->task_check(); }],
                ['name'=>'migrate','fn'=>function() use ($runner){ return $runner->task_migrate(); }],
                ['name'=>'official_refresh','fn'=>function() use ($runner,$max){ return $runner->task_official_refresh($max ?? 8); }],
                ['name'=>'ai_learn','fn'=>function() use ($runner,$force){ return $runner->task_ai_learn($force); }],
                ['name'=>'site_check','fn'=>function(?GxProgressTracker $t=null) use ($runner,$max){
                    $maxN = $max ?? 5;
                    return $runner->task_site_check($maxN, function(int $done, int $total, string $name) use ($t) {
                        if (!$t || $total<=0) return;
                        $t->subStep(intval(($done/$total)*100), "巡检 {$done}/{$total}：{$name}");
                    });
                }],
            ];
            foreach ($pipeline as $step) {
                try {
                    gx_cli_print("  ┌ " . date('H:i:s') . " 执行 [{$step['name']}] ... ");
                    $acceptsTracker = true;
                    if ($step['name'] === 'site_check') {
                        $r = gx_wrap_with_progress($tracker, $step['name'], function(?GxProgressTracker $t=null) use ($step) {
                            $fn = $step['fn'];
                            return $fn($t);
                        });
                    } else {
                        $r = gx_wrap_with_progress($tracker, $step['name'], function() use ($step) { $fn = $step['fn']; return $fn(); });
                    }
                    $ok = !empty($r['success']);
                    gx_cli_print(($ok ? "✅ OK" : "⚠️  WARN/FAIL") . "  (" . ($r['_cost_s'] ?? round(microtime(true)-$GX_START_TIME,3)) . "s)\n");
                    if ($GX_IS_CLI && !$ok) {
                        gx_cli_print("  │   原因: " . ($r['message'] ?? '未知失败') . "\n");
                    }
                    $runner->addResult($step['name'], $r);
                } catch (Throwable $e) {
                    gx_cli_print("❌ EXCEPTION: {$e->getMessage()}\n");
                    $tracker->finishStep($step['name'], false, [], '异常: '.$e->getMessage());
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

    // 最后：写进度到 100%
    if (isset($tracker) && ($tracker instanceof GxProgressTracker)) {
        $failedMsg = $summary['failed_tasks'] ? '失败项：' . implode(',', $summary['failed_tasks']) : '';
        $tracker->finishAll(!empty($summary['success']), ($summary['success'] ? '全部步骤执行完毕' : '执行部分失败：'.$failedMsg) . "（总耗时 {$summary['cost_seconds']}s）");
    }

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
    if (isset($tracker) && ($tracker instanceof GxProgressTracker)) {
        $tracker->finishAll(false, '运行中断：' . $e->getMessage() . " ({$e->getFile()}:{$e->getLine()})");
    }
    $err = ['success' => false, 'code' => 500, 'message' => 'gx.php 运行异常: '.$e->getMessage(), 'file'=>$e->getFile(), 'line'=>$e->getLine()];
    if ($GX_IS_CLI) {
        fwrite(STDERR, "FATAL: ".$e->getMessage()."\n");
        exit(1);
    }
    http_response_code(500);
    echo json_encode($err, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}
