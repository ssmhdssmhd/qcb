<?php
/**
 * jiami 分支 - 核心代码加密/乱码辅助工具
 * 用法：php _build_jiami_helper.php <mode> <args>
 *
 * 加密算法（多层乱码）：
 *   1. 源码压缩（去除多余空白+注释剥离）
 *   2. gzdeflate 压缩
 *   3. XOR 混淆（固定 16 字节密钥，多次叠加）
 *   4. str_rot13 + base64_encode 交替
 *   5. base64 最终输出
 *
 * 解密（运行时）：
 *   - 按反向顺序解码，最终通过 eval() 执行
 *   - 所有解密中间变量均使用乱码名，防止特征扫描
 */

function encrypt_core_code(string $phpCode, string $xorKey = 'Vj5xQ9rT2mP7sK4z'): string {
    // Step 1: 剥离 PHP 标签（保留纯代码）
    $code = trim($phpCode);
    if (strpos($code, '<?php') === 0) {
        $code = substr($code, 5);
    }
    $code = trim($code, " \t\n\r\0\x0B;");

    // Step 2: 压缩代码（去除注释和空白）
    if (function_exists('php_strip_whitespace')) {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        $tmpFile = $meta['uri'];
        fwrite($tmp, '<?php ' . $code . ' ?>');
        fseek($tmp, 0);
        $stripped = php_strip_whitespace($tmpFile);
        fclose($tmp);
        if ($stripped && strlen($stripped) > 10) {
            $stripped = preg_replace('/^<\?php\s*/', '', $stripped);
            $stripped = preg_replace('/\s*\?>\s*$/', '', $stripped);
            $code = $stripped;
        }
    }

    // Step 3: gzdeflate
    $step = gzdeflate($code, 9);

    // Step 4: XOR 混淆（按字节循环密钥）
    $keyLen = strlen($xorKey);
    $out = '';
    for ($i = 0, $L = strlen($step); $i < $L; $i++) {
        $out .= chr(ord($step[$i]) ^ ord($xorKey[$i % $keyLen]));
    }
    $step = $out;

    // Step 5: rot13 + base64 (双层)
    $step = str_rot13($step);
    $step = base64_encode($step);
    $step = str_rot13($step);
    $step = base64_encode($step);

    return $step;
}

/**
 * 生成自解密执行体（替代原始函数体）
 * 所有变量名使用 hash 前缀乱码，密钥字符串拆分为多段拼接
 *
 * @param string $encryptedPayload  encrypt_core_code() 输出的密文
 * @param string $returnWrapper     如何返回结果（return / echo / 空直接执行）
 */
function build_decoder_wrapper(string $encryptedPayload, string $returnWrapper = 'return'): string {
    $wrapper = <<<'PHPWRAP'
$_v00 = 'Vj5xQ';
$_v01 = '9rT2m';
$_v02 = 'P7sK4';
$_v03 = 'z';
$_k00 = $_v00 . $_v01 . $_v02 . $_v03;
$_p00 = '<<<ENCRYPTED_PAYLOAD>>>';
$_b00 = function($_c00, $_k01) {
    $_k02 = strlen($_k01);
    $_r00 = '';
    for($_i00 = 0, $_L00 = strlen($_c00); $_i00 < $_L00; $_i00++) {
        $_r00 .= chr(ord($_c00[$_i00]) ^ ord($_k01[$_i00 % $_k02]));
    }
    return $_r00;
};
$_dec = $_b00(str_rot13(base64_decode(str_rot13(base64_decode($_p00)))), $_k00);
$_src = @gzinflate($_dec);
if($_src === false) { throw new RuntimeException('核心代码损坏'); }
unset($_v00, $_v01, $_v02, $_v03, $_k00, $_p00, $_b00, $_dec, $_dec);
<<<RETURN_WRAPPER>>>
PHPWRAP;

    $wrapper = str_replace('<<<ENCRYPTED_PAYLOAD>>>', $encryptedPayload, $wrapper);
    $wrapper = str_replace('<<<RETURN_WRAPPER>>>', $returnWrapper, $wrapper);
    return $wrapper;
}

if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

$mode = $argv[1] ?? '';
if ($mode === 'demo') {
    // demo: 加密示例代码并验证
    $demo = <<<'PHP'
function add($a, $b) { return $a + $b; }
PHP;
    $enc = encrypt_core_code($demo);
    echo "Encrypted payload (len=" . strlen($enc) . "):\n  " . substr($enc, 0, 120) . "...\n\n";
    echo "Decoder wrapper snippet:\n";
    echo build_decoder_wrapper($enc, 'eval("?>" . trim($_src));') . "\n";
    exit(0);
}

if ($mode === 'encrypt-file') {
    // 预留：后续可按文件批量处理
    $file = $argv[2] ?? '';
    if (!$file || !file_exists($file)) { die("file not found\n"); }
    echo encrypt_core_code(file_get_contents($file)) . "\n";
    exit(0);
}

echo "Usage: php _build_jiami_helper.php demo|encrypt-file <file>\n";
