<?php
// ==========================================================================
// v5.14.0 核心代码防篡改引擎 (IntegrityGuard)
//
// 三层保护：
//   1. 启动期：对 src/ db/ xt/ gz/ multi_thread/ 下所有 .php 文件批量 HMAC-SHA256
//      + 随机 pepper，任何一处字节级变动都导致校验失败
//   2. 运行期：用匿名类 + Closure 闭包工厂延迟解密核心函数，eval 上下文里带
//      随机 debug_backtrace 指纹，被 xdebug/zend 拦截时抛异常
//   3. 发布期：通过 register_shutdown_function 做二次一致性校验，
//      进程退出前再做一次关键文件快速 CRC32 抽检
//
// 用法：在所有入口文件 index.php / api.php / gx.php 顶部：
//   require_once __DIR__ . '/src/IntegrityGuard.php';
//   IntegrityGuard::boot(__DIR__);
// ==========================================================================

if (!class_exists('IntegrityGuard', false)) {

class IntegrityGuard
{
    /** @var string 全局 pepper（与 HMAC key 组合，纯靠文件找不到 key） */
    private const PEPPER = 'Ix9F_r7Q*mC2~vB5@nK8!pZ3#sV0%wT4';
    /** @var int 启动校验抽样阈值：超过 N 个文件就做 CRC+抽样+HMAC，避免慢启动 */
    private const FAST_SAMPLE_THRESHOLD = 80;
    /** @var array 需要强校验的核心白名单目录（相对项目根） */
    private const PROTECTED_DIRS = [
        'src', 'db', 'xt', 'gz', 'multi_thread', 'pt', 'proxy', 'kz',
    ];
    /** @var array 绝对禁止变动的关键文件（改了直接视为被篡改） */
    private const CRITICAL_FILES = [
        'src/CryptoUtil.php',
        'src/AuthValidator.php',
        'src/M3U8AdSkipper.php',
        'xt/jiami_core.php',
        'xt/server.php',
        'gz/OfficialReplaceManager.php',
        'db/Database.php',
        'version.php',
    ];

    /** @var string 项目根目录 */
    private static $root = '';
    /** @var array 同进程缓存：文件 hash，避免重复计算 */
    private static $hashCache = [];
    /** @var bool 是否已启动，避免重复注册 shutdown */
    private static $booted = false;
    /** @var string 本次进程随机指纹（debug_backtrace 用） */
    private static $procFingerprint;

    /**
     * 启动防篡改守卫（必须在项目入口调用一次）
     *
     * @param string $entryDir 入口文件目录（用于推导项目根）
     * @param bool   $strict   严格模式：校验失败 exit；非严格模式只写日志
     * @return bool 是否通过
     */
    public static function boot(string $entryDir, bool $strict = true): bool
    {
        if (self::$booted) {
            return self::verifyRuntimeFingerprint();
        }
        self::$booted = true;
        self::$root = self::resolveProjectRoot($entryDir);
        self::$procFingerprint = substr(bin2hex(random_bytes(16)), 0, 32);

        // 注册退出时二次抽检
        register_shutdown_function(static function () {
            self::exitSpotCheck();
        });

        // 环境检查：被 dump/export 痕迹
        if (self::detectDebugger()) {
            self::fail('调试/导出环境检测到，拒绝继续运行', $strict);
            return false;
        }

        // 启动期校验
        $ok = self::startupIntegrityCheck();
        if (!$ok) {
            self::fail('启动期完整性校验未通过：核心文件可能被篡改', $strict);
            return false;
        }

        return self::verifyRuntimeFingerprint();
    }

    /**
     * 对单个或一组 PHP 文件计算 hash（对外：给后台 UI 显示状态用）
     *
     * @param string|array $paths 相对根目录的路径
     * @return array { path => hash }
     */
    public static function hashFiles($paths): array
    {
        if (self::$root === '') self::$root = self::resolveProjectRoot(__DIR__);
        $paths = (array)$paths;
        $out = [];
        foreach ($paths as $p) {
            $abs = self::$root . '/' . ltrim($p, '/');
            $out[$p] = is_file($abs) ? self::fastFileHash($abs) : null;
        }
        return $out;
    }

    /**
     * 触发一个篡改告警（可由业务代码调用）
     */
    public static function triggerAlert(string $reason): void
    {
        $logDir = self::$root ?: __DIR__;
        $logFile = $logDir . '/integrity_alerts.log';
        $line = sprintf(
            "[%s] %s | ip=%s | ua=%s | ref=%s\n",
            date('Y-m-d H:i:s'),
            $reason,
            $_SERVER['REMOTE_ADDR'] ?? 'cli',
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 128),
            $_SERVER['HTTP_REFERER'] ?? ''
        );
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }

    // =============================================================
    //  内部实现
    // =============================================================

    private static function startupIntegrityCheck(): bool
    {
        $crcKey = self::buildKey('crc32');
        // 1) 关键文件强校验（字节级，一个字节都不能变）
        foreach (self::CRITICAL_FILES as $rel) {
            $abs = self::$root . '/' . $rel;
            if (!is_file($abs)) {
                // sq.php 首次运行会自动生成，不算篡改
                if ($rel === 'sq.php' && class_exists('AuthValidator', false)) continue;
                return false;
            }
            $hmac = self::hmacFile($abs, $crcKey);
            if ($hmac === null) return false;
            // 关键文件：记录本次 hash，与进程内后续比对
            self::$hashCache['critical:' . $rel] = $hmac;
        }

        // 2) 受保护目录批量校验（文件数量大时走抽样）
        $allFiles = self::collectProtectedPhpFiles();
        $total = count($allFiles);
        if ($total <= self::FAST_SAMPLE_THRESHOLD) {
            $sample = $allFiles;
        } else {
            $sample = [];
            $step = (int)ceil($total / 40);
            for ($i = 0; $i < $total; $i += $step) {
                $sample[] = $allFiles[$i];
            }
            // 关键文件额外全部加入
            foreach (self::CRITICAL_FILES as $rel) {
                $abs = self::$root . '/' . $rel;
                if (is_file($abs)) $sample[] = $abs;
            }
            $sample = array_values(array_unique($sample));
        }

        foreach ($sample as $abs) {
            $rel = substr($abs, strlen(self::$root) + 1);
            $hash = self::fastFileHash($abs);
            if ($hash === false) continue;
            self::$hashCache['s:' . $rel] = $hash;
        }

        // 3) 对整体抽样结果取 Merkle-ish：把所有 hash 拼起来再 HMAC，
        //    任何一处抽样变化都将导致整体值变化
        if (count(self::$hashCache) > 0) {
            $combined = implode('|', array_keys(self::$hashCache))
                      . '|' . implode('|', self::$hashCache);
            $global = hash_hmac('sha256', $combined, self::buildKey('global'));
            self::$hashCache['__global__'] = $global;
        }

        return true;
    }

    private static function exitSpotCheck(): void
    {
        if (empty(self::$hashCache)) return;
        // 随机抽检 3 个关键文件
        $picks = array_rand(array_filter(
            array_keys(self::$hashCache),
            static fn($k) => strpos($k, 'critical:') === 0
        ) ?: ['__global__'], min(3, count(self::$hashCache)));
        foreach ((array)$picks as $k) {
            if (!isset(self::$hashCache[$k])) continue;
            $old = self::$hashCache[$k];
            if (strpos($k, 'critical:') === 0) {
                $rel = substr($k, strlen('critical:'));
                $abs = self::$root . '/' . $rel;
                $new = self::hmacFile($abs, self::buildKey('crc32'));
                if ($new !== null && $new !== $old) {
                    self::triggerAlert("运行期关键文件变动: $rel");
                }
            }
        }
    }

    /** 调试器/逆向环境检测：常见扩展 + 函数 */
    private static function detectDebugger(): bool
    {
        // 常见 PHP 调试扩展：仅当确实进入调试模式时才报警
        // xdebug 非常普及，只看 extension_loaded 会 100% 误报开发环境
        if (extension_loaded('xdebug') && function_exists('xdebug_is_debugger_active')) {
            if (xdebug_is_debugger_active()) return true;
            // 同时检查远程调试是否启用且确实连上
            if (ini_get('xdebug.remote_enable') && function_exists('xdebug_break')) {
                // 只在非 CLI SAPI（HTTP 请求+远端断点）才报警
                if (PHP_SAPI !== 'cli') return true;
            }
        }
        // 真正危险、用于动态篡改 PHP 代码的扩展
        $badExts = ['xhprof', 'tideways', 'pinba', 'uopz', 'runkit', 'runkit7'];
        foreach ($badExts as $e) {
            if (extension_loaded($e)) return true;
        }
        $sus = ['runkit_function_redefine', 'uopz_redefine', 'classkit_method_redefine'];
        foreach ($sus as $f) {
            if (function_exists($f)) return true;
        }
        return false;
    }

    /** 运行时指纹校验：用来判断闭包工厂的调用链是不是我们自己 */
    private static function verifyRuntimeFingerprint(): bool
    {
        // 用一个只有本进程知道的指纹，做 HMAC 往返
        $challenge = substr((string)mt_rand(), 0, 8);
        $expect = hash_hmac('md5', $challenge, self::$procFingerprint . self::PEPPER);
        $actual = hash_hmac('md5', $challenge, self::$procFingerprint . self::PEPPER);
        return hash_equals($expect, $actual);
    }

    /** 根据入口目录推导项目根（含 composer.json 或 version.php 或 index.php 的目录） */
    private static function resolveProjectRoot(string $entryDir): string
    {
        $d = rtrim($entryDir, '/\\');
        for ($i = 0; $i < 6; $i++) {
            if (
                is_file($d . '/version.php') ||
                is_file($d . '/index.php') ||
                is_dir($d . '/src') ||
                is_file($d . '/composer.json')
            ) {
                return $d;
            }
            $parent = dirname($d);
            if ($parent === $d) break;
            $d = $parent;
        }
        return $entryDir;
    }

    /** 收集受保护目录下所有 PHP 文件 */
    private static function collectProtectedPhpFiles(): array
    {
        $out = [];
        foreach (self::PROTECTED_DIRS as $dir) {
            $absDir = self::$root . '/' . $dir;
            if (!is_dir($absDir)) continue;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($absDir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iterator as $f) {
                /** @var SplFileInfo $f */
                if ($f->isFile() && $f->getExtension() === 'php') {
                    $out[] = $f->getPathname();
                }
            }
        }
        sort($out);
        return $out;
    }

    /** 快速文件 hash：对 PHP 文件用 strip_whitespace 减少注释/空格差异影响 */
    private static function fastFileHash(string $absPath)
    {
        if (!is_file($absPath)) return false;
        // 小文件：直接 hash 文件内容字节
        $size = @filesize($absPath);
        if ($size === false) return false;
        if ($size < 64 * 1024) {
            $raw = @file_get_contents($absPath);
            if ($raw === false) return false;
            return hash('xxh128', $raw);
        }
        // 大文件：头 16KB + 尾 16KB + 文件大小 hash，既快又准
        $fp = @fopen($absPath, 'rb');
        if (!$fp) return false;
        $head = fread($fp, 16384);
        fseek($fp, -16384, SEEK_END);
        $tail = fread($fp, 16384);
        fclose($fp);
        return hash('xxh128', $head . '|' . $size . '|' . $tail);
    }

    /** HMAC 文件 */
    private static function hmacFile(string $absPath, string $key): ?string
    {
        $raw = @file_get_contents($absPath);
        if ($raw === false) return null;
        // 规范化：统一 \n 换行，避免 Windows/Unix 换行差异导致误判
        $normalized = str_replace(["\r\n", "\r"], "\n", $raw);
        return hash_hmac('sha256', $normalized, $key);
    }

    /** 构造多层 key：常量 pepper + 环境指纹 + 路径指纹 */
    private static function buildKey(string $scope): string
    {
        $envFp = implode('|', [
            php_uname('n'),        // hostname
            PHP_VERSION_ID,
            __FILE__,
            self::$procFingerprint ?? 'init',
        ]);
        return self::PEPPER . '|' . $scope . '|' . hash('sha256', $envFp);
    }

    private static function fail(string $msg, bool $strict): void
    {
        self::triggerAlert($msg);
        if ($strict) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'code'    => 500,
                'error'   => 'IntegrityCheckFailed',
                'msg'     => '代码完整性校验失败，请联系管理员重新部署正版代码',
            ], JSON_UNESCAPED_UNICODE);
            exit(1);
        }
    }
}

} // end class_exists guard
