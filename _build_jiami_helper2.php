<?php
/**
 * jiami 分支 - 核心代码加密/乱码辅助工具 V2
 *
 * 核心函数加密思路（极简版，避免复杂 brace 嵌套）：
 *
 *   Original: function Foo($a, string $b = 'x'): ?T { ORIG_BODY }
 *
 *   Stub replaces:
 *      function Foo($a, string $b = 'x'): ?T {
 *          static $__enc_impl = null;
 *          if ($__enc_impl === null) {
 *              // 解码密钥段 (乱码变量名)
 *              $_x0 = 'Vj5xQ'; $_x1 = '9rT2m'; $_x2 = 'P7sK4'; $_x3 = 'z';
 *              $_k = $_x0.$_x1.$_x2.$_x3;
 *              $_p = '<BASE64 PAYLOAD>'; // 加密后的 "function($a, string $b='x'): ?T { BODY }"
 *              // 解码 (多层)
 *              $_xor = function($c,$k){...};
 *              $_raw = gzinflate($_xor(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
 *              // 转成闭包: eval('return function (..sig..){ body };')
 *              $__enc_impl = eval('return '.$_raw.';');
 *              unset($_x,$_k,$_p,$_xor,$_raw);
 *          }
 *          return $__enc_impl(...func_get_args());
 *      }
 *
 * 关键点：
 *  - 加密时把 function 关键字 + 名称去掉，保留 (params): retType 作为匿名函数签名
 *  - 加密 payload 本身就是 "function($x, ...): T { body }" (匿名函数字面量)
 *  - eval('return '.$payload.';') 直接返回该匿名函数作为 Closure 对象
 *  - 完全避免 "if(!function_exists){ function _jm_xxx { body } }" 这种套多层 brace 的代码
 */

require_once __DIR__ . '/_build_jiami_helper.php';

/** 提取函数（签名+完整 body） */
function _extract_function(string $src, string $funcName): ?array {
    $pattern = '/function\s+' . preg_quote($funcName, '/') . '\s*\(/';
    if (!preg_match($pattern, $src, $m, PREG_OFFSET_CAPTURE)) return null;
    $start = $m[0][1];
    $bodyOpen = strpos($src, '{', $start);
    if ($bodyOpen === false) return null;
    $len = strlen($src); $depth = 0; $i = $bodyOpen;
    $inString = null; $inDoc = 0; $escaped = false;
    while ($i < $len) {
        $c = $src[$i]; $n = ($i+1<$len)?$src[$i+1]:'';
        if ($inDoc===1) { if ($c==="\n"||$c==="\r") $inDoc=0; $i++; continue; }
        if ($inDoc===2) { if ($c==='*'&&$n==='/') { $inDoc=0; $i+=2; continue; } $i++; continue; }
        if ($inString) {
            if ($escaped) { $escaped=false; }
            elseif ($c==='\\') { $escaped=true; }
            elseif ($c===$inString) { $inString=null; }
            $i++; continue;
        }
        if ($c==='/'&&$n==='/') { $inDoc=1; $i+=2; continue; }
        if ($c==='/'&&$n==='*') { $inDoc=2; $i+=2; continue; }
        if ($c==='"'||$c==="'"||$c==='`') { $inString=$c; $i++; continue; }
        if ($c==='{') $depth++;
        if ($c==='}') { $depth--; if ($depth===0) { $end=$i; break; } }
        $i++;
    }
    if (!isset($end)) return null;
    // 前缀：可能有 public/private/static
    $prefixStart = $start;
    for ($j=$start-1; $j>=0; $j--) {
        $cc = $src[$j];
        if ($cc==='{'||$cc==='}'||$cc===';'||$cc==="\n"||$cc==="\r") {
            $k = $j + 1;
            while ($k<$start && ctype_space($src[$k])) $k++;
            $prefixStart = $k; break;
        }
    }
    return [
        'full' => substr($src, $prefixStart, $end - $prefixStart + 1),
        'start' => $prefixStart,
        'end' => $end + 1,
        'func_kw' => $start,
        'body_open' => $bodyOpen,
    ];
}

/**
 * 加密一段完整函数源码（含签名+body），返回加密后的 base64
 * 返回的 payload 可被 eval('return '.$decrypted.';') 直接转成 Closure
 * 即：把 function NAME(...) 改成匿名 function(...)
 */
function _encrypt_as_closure(string $fullFuncCode): string {
    // 去除前导空白
    $code = trim($fullFuncCode);

    // 1. 剥离 [public|private|protected] [static] [&(引用返回)] 前缀
    $prefixPattern = '/^((?:public\s+|private\s+|protected\s+)*(?:static\s+)*&?\s*)/';
    if (preg_match($prefixPattern, $code, $pm)) {
        $code = substr($code, strlen($pm[0]));
        // 现在 code 以 "function NAME(" 开头
    }

    // 2. 把 "function NAME(params)" 改成匿名 "function(params)" (去掉名字)
    if (preg_match('/^function\s+([A-Za-z_][\w]*)\s*\(/', $code, $nm, PREG_OFFSET_CAPTURE)) {
        $nameStart = $nm[1][1];
        $nameLen = strlen($nm[1][0]);
        $parenOpen = $nm[0][1] + strlen($nm[0][0]) - 1; // '(' 位置
        // 删除 [nameStart, parenOpen) 区间 (函数名 + 周围空白)
        $code = substr($code, 0, $nameStart) . substr($code, $parenOpen);
    }

    return encrypt_core_code($code);
}

function _build_simple_stub(string $origFullCode, string $encryptedPayload, bool $isClassMethod): string {
    $bodyOpen = strpos($origFullCode, '{');
    $header = substr($origFullCode, 0, $bodyOpen);
    if ($isClassMethod) {
        // 类方法：用 Closure::call($this, ...) 绑定实例，可访问 $this
        $callLine = 'return $__enc_impl->call($this, ...func_get_args());';
    } else {
        // 普通函数：直接调用闭包
        $callLine = 'return $__enc_impl(...func_get_args());';
    }
    $tpl = <<<'NOW'
__HEADER__{
    static $__enc_impl = null;
    if ($__enc_impl === null) {
        $_x0 = 'Vj5xQ';
        $_x1 = '9rT2m';
        $_x2 = 'P7sK4';
        $_x3 = 'z';
        $_k = $_x0 . $_x1 . $_x2 . $_x3;
        $_p = '__PAY__';
        $_xo = function ($_c, $_k) {
            $_l = strlen($_k); $_r = '';
            for ($_i = 0, $_L = strlen($_c); $_i < $_L; $_i++) {
                $_r .= chr(ord($_c[$_i]) ^ ord($_k[$_i % $_l]));
            }
            return $_r;
        };
        $_raw = @gzinflate($_xo(str_rot13(base64_decode(str_rot13(base64_decode($_p)))), $_k));
        if ($_raw === false) { throw new \RuntimeException('核心解析代码损坏'); }
        $__enc_impl = eval('return ' . $_raw . ';');
        unset($_x0, $_x1, $_x2, $_x3, $_k, $_p, $_xo, $_raw);
        if (!($__enc_impl instanceof \Closure)) {
            throw new \RuntimeException('核心代码解密失败: 未生成可执行闭包');
        }
    }
    __CALL_LINE__
}
NOW;
    return str_replace(['__HEADER__', '__PAY__', '__CALL_LINE__'],
                       [$header, $encryptedPayload, $callLine],
                       $tpl);
}

/* ========== 动作 ========== */

function _patch_file(string $file, array $targets, bool $isClassMethod, string $aliasPrefix = 'srv_') {
    $src = file_get_contents($file);
    foreach ($targets as $func) {
        $info = _extract_function($src, $func);
        if (!$info) { echo "  [$func] 未找到,跳过\n"; continue; }
        echo "  [$func] 原大小=" . strlen($info['full']) . "字节 (类方法=" . ($isClassMethod?'Y':'N') . ")\n";
        $payload = _encrypt_as_closure($info['full']);
        echo "    加密后=" . strlen($payload) . "字节\n";
        $stub = _build_simple_stub($info['full'], $payload, $isClassMethod);
        $src = substr_replace($src, $stub, $info['start'], $info['end'] - $info['start']);
        file_put_contents($file, $src);
        $src = file_get_contents($file);
        echo "    补丁写入 OK\n";
    }
}

$mode = $argv[1] ?? '';
if ($mode === 'patch-all') {
    echo "--- 加密 xt/server.php (普通函数) ---\n";
    _patch_file(__DIR__ . '/xt/server.php',
        ['findUrlInArray','callOfficialReplaceDirect','getVideoLinkFromApiEntry','callSingleApi'],
        false, 'srv_');
    echo "--- 加密 xt/PerformanceOptimizer.php (类方法: private) ---\n";
    _patch_file(__DIR__ . '/xt/PerformanceOptimizer.php',
        ['extractVideoUrl','findUrlInArray'],
        true, 'po_');
} elseif ($mode === 'test-closure') {
    // Quick unit test
    $sample = 'function add(int $a, int $b): int { return $a + $b; }';
    $enc = _encrypt_as_closure($sample);
    $stub = _build_simple_stub($sample, $enc, false);
    $testFile = tempnam(sys_get_temp_dir(), 'jmtest_');
    file_put_contents($testFile, "<?php\n" . $stub . "\n\$r = add(2, 3); echo \$r===5 ? 'OK(closure pattern works)' : 'FAIL got '.\$r; echo \"\\n\";");
    $out = shell_exec("php " . escapeshellarg($testFile) . " 2>&1");
    echo $out;
    @unlink($testFile);
} elseif ($mode === 'test-class') {
    // Class method test
    $sampleClass = <<<'PHP'
class Calc {
    public $base = 10;
    public function add(int $a): int { return $this->base + $a; }
}
PHP;
    // 提取 add 方法
    $info = _extract_function($sampleClass, 'add');
    if (!$info) { echo "class method extract FAIL\n"; exit(1); }
    $enc = _encrypt_as_closure($info['full']);
    $stub = _build_simple_stub($info['full'], $enc, true);
    // 把 sampleClass 中的 add 方法替换成 stub
    $patchedClass = substr_replace($sampleClass, $stub, $info['start'], $info['end'] - $info['start']);
    $testFile = tempnam(sys_get_temp_dir(), 'jmtest_cls_');
    file_put_contents($testFile, "<?php\n" . $patchedClass . "\n\$c = new Calc; echo \$c->add(5)===15 ? 'OK(class-method closure bind works)' : 'FAIL got '.$c->add(5); echo \"\\n\";");
    $out = shell_exec("php " . escapeshellarg($testFile) . " 2>&1");
    echo $out;
    @unlink($testFile);
} else {
    echo "Usage: php _build_jiami_helper2.php patch-all|test-closure|test-class\n";
}
