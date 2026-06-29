<?php
/**
 * M3U8 深度诊断修复工具 v2
 * 直接在服务器上运行，自动诊断并修复所有问题
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width,initial-scale=1'>";
echo "<title>M3U8 深度诊断修复</title>";
echo "<style>";
echo "body{font-family:Arial,sans-serif;margin:20px;background:#f5f7fa}";
echo "h2{color:#667eea}h3{color:#409eff}";
echo ".success{color:#67c23a}.error{color:#f56c6c}.warn{color:#e6a23c}.info{color:#409eff}";
echo ".card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.1);margin-bottom:15px}";
echo "pre{background:#f5f7fa;padding:10px;border-radius:4px;overflow-x:auto;font-size:12px;max-height:300px}";
echo ".btn{display:inline-block;padding:10px 20px;background:#667eea;color:#fff;text-decoration:none;border-radius:4px;margin:5px}";
echo ".btn-danger{background:#f56c6c}.btn-success{background:#67c23a}";
echo "</style>";
echo "</head><body>";
echo "<div class='card'>";
echo "<h2>M3U8 深度诊断修复工具 v2</h2>";
echo "<p class='info'>自动诊断并修复所有问题</p>";
echo "</div>";

$rootDir = __DIR__;
$issues = [];
$fixes = [];

// ==================== 诊断函数 ====================

function checkBOM($file) {
    $content = file_get_contents($file);
    $bom = substr($content, 0, 3);
    return $bom === "\xEF\xBB\xBF";
}

function checkOutputOnRequire($file) {
    ob_start();
    @include $file;
    $output = ob_get_clean();
    return !empty(trim($output));
}

function checkSyntax($file) {
    $result = exec("php -l " . escapeshellarg($file) . " 2>&1");
    return strpos($result, 'No syntax errors') !== false;
}

function testApi($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $isJson = json_decode($response) !== null;
    return ['response' => $response, 'httpCode' => $httpCode, 'isJson' => $isJson];
}

// ==================== 1. 环境检查 ====================
echo "<div class='card'><h3>1. PHP 环境检查</h3>";
echo "<pre>";
echo "PHP 版本: " . phpversion() . "\n";
echo "display_errors: " . ini_get('display_errors') . "\n";
echo "output_buffering: " . ini_get('output_buffering') . "\n";
echo "allow_url_fopen: " . ini_get('allow_url_fopen') . "\n";
echo "curl 扩展: " . (extension_loaded('curl') ? '✓' : '✗') . "\n";
echo "zip 扩展: " . (extension_loaded('zip') ? '✓' : '✗') . "\n";
echo "</pre></div>";

// ==================== 2. 文件检查 ====================
echo "<div class='card'><h3>2. 文件完整性检查</h3>";

$requiredFiles = [
    'index.php',
    'mx.php',
    'mxadmin.php',
    'router.php',
    'src/M3U8AdSkipper.php',
    'src/M3U8Parser.php',
    'src/AdFilter.php',
    'src/AdRuleEngine.php',
    'src/OutputGenerator.php',
    'src/AuthValidator.php',
    'src/AuthConfig.php',
    'src/CryptoUtil.php',
    'src/UpdateManager.php',
    'gz/DomainRuleManager.php',
    'gz/EnhancedAdRuleEngine.php',
];

$missingFiles = [];
$filesWithBOM = [];
$filesWithOutput = [];
$syntaxErrors = [];

foreach ($requiredFiles as $file) {
    $path = $rootDir . '/' . $file;
    if (!file_exists($path)) {
        $missingFiles[] = $file;
        echo "<p class='error'>✗ 缺失: $file</p>";
    } else {
        if (checkBOM($path)) {
            $filesWithBOM[] = $path;
            echo "<p class='warn'>⚠ BOM头: $file</p>";
        }
        if (checkOutputOnRequire($path)) {
            $filesWithOutput[] = $path;
            echo "<p class='error'>✗ 输出内容: $file</p>";
        }
        if (!checkSyntax($path)) {
            $syntaxErrors[] = $path;
            echo "<p class='error'>✗ 语法错误: $file</p>";
        }
    }
}

if (empty($missingFiles) && empty($filesWithBOM) && empty($filesWithOutput) && empty($syntaxErrors)) {
    echo "<p class='success'>✓ 所有文件检查通过</p>";
}
echo "</div>";

// ==================== 3. 立即修复 BOM ====================
if (!empty($filesWithBOM)) {
    echo "<div class='card'><h3>3. 修复 BOM 头</h3>";
    foreach ($filesWithBOM as $file) {
        $content = file_get_contents($file);
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            if (file_put_contents($file, $content) !== false) {
                echo "<p class='success'>✓ 已移除 BOM: $file</p>";
                $fixes[] = "移除 BOM: $file";
            } else {
                echo "<p class='error'>✗ 无法移除 BOM: $file</p>";
                $issues[] = "BOM 移除失败: $file";
            }
        }
    }
    echo "</div>";
}

// ==================== 4. API 测试 ====================
echo "<div class='card'><h3>4. API 直接测试</h3>";

$testUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
$apiUrl = str_replace('fix_v2.php', 'mx.php', $testUrl) . '?action=rules/list';

echo "<p class='info'>测试 URL: $apiUrl</p>";

$result = testApi($apiUrl);
echo "<p>HTTP 状态码: " . $result['httpCode'] . "</p>";
echo "<p>返回类型: " . ($result['isJson'] ? '✓ JSON' : '✗ 非JSON (可能是HTML错误)') . "</p>";

if (!$result['isJson']) {
    echo "<p class='error'>检测到 API 返回非 JSON 响应，可能原因:</p>";
    echo "<ul>";
    echo "<li>PHP 语法错误</li>";
    echo "<li>文件包含 BOM 头</li>";
    echo "<li>require/include 时有输出</li>";
    echo "<li>PHP 配置问题</li>";
    echo "</ul>";
    echo "<p>响应内容 (前500字符):</p>";
    echo "<pre>" . htmlspecialchars(substr($result['response'], 0, 500)) . "</pre>";
}
echo "</div>";

// ==================== 5. 类加载测试 ====================
echo "<div class='card'><h3>5. 类加载测试</h3>";

$errors = [];
try {
    require_once $rootDir . '/src/AuthConfig.php';
    echo "<p class='success'>✓ AuthConfig 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "AuthConfig: " . $e->getMessage();
    echo "<p class='error'>✗ AuthConfig: " . $e->getMessage() . "</p>";
}

try {
    require_once $rootDir . '/src/CryptoUtil.php';
    echo "<p class='success'>✓ CryptoUtil 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "CryptoUtil: " . $e->getMessage();
    echo "<p class='error'>✗ CryptoUtil: " . $e->getMessage() . "</p>";
}

try {
    require_once $rootDir . '/src/AdRuleEngine.php';
    echo "<p class='success'>✓ AdRuleEngine 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "AdRuleEngine: " . $e->getMessage();
    echo "<p class='error'>✗ AdRuleEngine: " . $e->getMessage() . "</p>";
}

try {
    require_once $rootDir . '/src/M3U8Parser.php';
    echo "<p class='success'>✓ M3U8Parser 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "M3U8Parser: " . $e->getMessage();
    echo "<p class='error'>✗ M3U8Parser: " . $e->getMessage() . "</p>";
}

try {
    require_once $rootDir . '/src/M3U8AdSkipper.php';
    echo "<p class='success'>✓ M3U8AdSkipper 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "M3U8AdSkipper: " . $e->getMessage();
    echo "<p class='error'>✗ M3U8AdSkipper: " . $e->getMessage() . "</p>";
}

try {
    require_once $rootDir . '/gz/DomainRuleManager.php';
    echo "<p class='success'>✓ DomainRuleManager 加载成功</p>";
} catch (Throwable $e) {
    $errors[] = "DomainRuleManager: " . $e->getMessage();
    echo "<p class='error'>✗ DomainRuleManager: " . $e->getMessage() . "</p>";
}

echo "</div>";

// ==================== 6. 规则加载测试 ====================
echo "<div class='card'><h3>6. 规则加载测试</h3>";

try {
    require_once $rootDir . '/gz/DomainRuleManager.php';
    $dm = new DomainRuleManager();
    $rules = $dm->getAllRules();
    echo "<p class='success'>✓ 规则加载成功: " . count($rules) . " 个规则</p>";
    foreach ($rules as $domain => $rule) {
        echo "<p style='font-size:12px;margin:2px 0'>- $domain</p>";
    }
} catch (Throwable $e) {
    echo "<p class='error'>✗ 规则加载失败: " . $e->getMessage() . "</p>";
    $issues[] = "规则加载: " . $e->getMessage();
}
echo "</div>";

// ==================== 7. 手动执行 mx.php 并捕获输出 ====================
echo "<div class='card'><h3>7. mx.php 直接执行测试</h3>";

$_GET['action'] = 'rules/list';
$_SERVER['REQUEST_METHOD'] = 'GET';

ob_start();
try {
    require $rootDir . '/mx.php';
} catch (Throwable $e) {
    echo "<p class='error'>✗ 异常: " . $e->getMessage() . "</p>";
}
$output = ob_get_clean();

if (!empty(trim($output))) {
    $isJson = json_decode($output) !== null;
    echo "<p>输出类型: " . ($isJson ? '✓ JSON' : '✗ 非JSON') . "</p>";
    echo "<p>输出长度: " . strlen($output) . " 字符</p>";
    echo "<p>输出内容 (前1000字符):</p>";
    echo "<pre>" . htmlspecialchars(substr($output, 0, 1000)) . "</pre>";

    if (!$isJson) {
        echo "<p class='error'>✗ mx.php 没有返回 JSON！这是问题的根源。</p>";
        echo "<p>可能原因:</p>";
        echo "<ul>";
        echo "<li>PHP 在 require 时输出了内容</li>";
        echo "<li>文件包含 BOM 头</li>";
        echo "<li>PHP 配置 display_errors 开启</li>";
        echo "<li>某个被 require 的文件有问题</li>";
        echo "</ul>";
    }
} else {
    echo "<p class='warn'>⚠ 无输出（正常应该输出 JSON）</p>";
}
echo "</div>";

// ==================== 8. 强制修复 ====================
echo "<div class='card'><h3>8. 强制修复选项</h3>";
echo "<p class='info'>如果上述测试发现问题，可以使用以下选项:</p>";

echo "<a href='?fix=ob' class='btn'>修复输出缓冲问题</a>";
echo "<a href='?fix=headers' class='btn'>修复 Header 问题</a>";
echo "<a href='?fix=all' class='btn btn-danger'>完整修复 (推荐)</a>";
echo "<a href='?fix=clear_cache' class='btn btn-success'>清除 PHP 缓存</a>";
echo "</div>";

// ==================== 执行修复 ====================
if (isset($_GET['fix'])) {
    echo "<div class='card'><h3>执行修复: " . htmlspecialchars($_GET['fix']) . "</h3>";

    if ($_GET['fix'] === 'clear_cache') {
        if (function_exists('opcache_reset')) {
            opcache_reset();
            echo "<p class='success'>✓ OPcache 已清除</p>";
        }
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
            echo "<p class='success'>✓ APC 缓存已清除</p>";
        }
        clearstatcache(true);
        echo "<p class='success'>✓ 文件状态缓存已清除</p>";
    }

    if ($_GET['fix'] === 'ob' || $_GET['fix'] === 'all') {
        $files = ['mx.php', 'index.php'];
        foreach ($files as $file) {
            $path = $rootDir . '/' . $file;
            if (!file_exists($path)) continue;

            $content = file_get_contents($path);

            // 检查是否有 ob_start
            if (strpos($content, 'ob_start()') === false) {
                // 在 <?php 后面添加 ob_start
                $content = preg_replace('/^<\?php\s*/', "<?php\nob_start();\n", $content, 1);
                echo "<p class='success'>✓ 添加 ob_start(): $file</p>";
            }

            // 确保 header 在 ob_start 之后
            if (preg_match('/^<\?php\s*\nob_start\(\);/', $content)) {
                $content = preg_replace('/(<\?php\s*\nob_start\(\);\s*\n)/', "$1\nini_set('display_errors', 0);\n", $content);
                echo "<p class='success'>✓ 优化错误处理: $file</p>";
            }

            file_put_contents($path, $content);
            $fixes[] = "优化输出缓冲: $file";
        }
    }

    if ($_GET['fix'] === 'headers' || $_GET['fix'] === 'all') {
        $files = ['mx.php', 'index.php'];
        foreach ($files as $file) {
            $path = $rootDir . '/' . $file;
            if (!file_exists($path)) continue;

            $content = file_get_contents($path);

            // 确保 Content-Type header 在最前面
            if (preg_match('/header\s*\(\s*[\'"]Content-Type:/', $content)) {
                // Content-Type 已存在，移到最前面
                $content = preg_replace('/(\n?\s*header\s*\(\s*[\'"]Content-Type:[^\'\"]+[\'\"]\s*\);)/', "\n// Moved header\n//$1\n", $content);
            }

            file_put_contents($path, $content);
            $fixes[] = "修复 Header: $file";
        }
    }

    echo "<p class='success'>✓ 修复完成！</p>";
    echo "<p><a href='fix_v2.php' class='btn'>重新诊断</a></p>";
    echo "<p><a href='mxadmin.php' class='btn btn-success'>前往后台</a></p>";
    echo "</div>";
}

// ==================== 总结 ====================
echo "<div class='card'><h3>诊断总结</h3>";

if (empty($issues)) {
    echo "<p class='success'>✓ 未检测到问题</p>";
} else {
    echo "<p class='error'>✗ 发现 " . count($issues) . " 个问题:</p>";
    foreach ($issues as $issue) {
        echo "<p style='font-size:12px'>- $issue</p>";
    }
}

if (!empty($fixes)) {
    echo "<p class='success'>✓ 已执行 " . count($fixes) . " 个修复:</p>";
    foreach ($fixes as $fix) {
        echo "<p style='font-size:12px'>- $fix</p>";
    }
}

echo "<hr>";
echo "<p><strong>下一步操作:</strong></p>";
echo "<ol>";
echo "<li>点击上面的 <strong>完整修复</strong> 按钮</li>";
echo "<li>然后点击 <strong>清除 PHP 缓存</strong></li>";
echo "<li>最后刷新后台页面</li>";
echo "</ol>";
echo "</div>";

echo "</body></html>";
