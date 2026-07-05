<?php
/**
 * M3U8 广告分析系统 - 在线更新脚本
 * 
 * 使用方法：
 * 1. 将本文件上传到网站根目录
 * 2. 浏览器访问 https://你的域名/update.php
 * 3. 点击"立即更新"按钮
 * 4. 更新完成后请删除本文件
 * 
 * 功能特性：
 * - 多镜像源下载，确保连接成功
 * - 自动备份旧版本
 * - 智能文件替换（不覆盖配置文件）
 * - 更新进度实时显示
 * - 自动清理临时文件
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);

$githubRepo = 'ssmhdssmhd/qcb';
$branch = 'main';
$rootDir = __DIR__;

$downloadMirrors = [
    'https://github.com/' . $githubRepo . '/archive/refs/heads/' . $branch . '.zip',
    'https://ghproxy.com/https://github.com/' . $githubRepo . '/archive/refs/heads/' . $branch . '.zip',
    'https://mirror.ghproxy.com/https://github.com/' . $githubRepo . '/archive/refs/heads/' . $branch . '.zip',
    'https://gh.api.99988866.xyz/https://github.com/' . $githubRepo . '/archive/refs/heads/' . $branch . '.zip',
];

$apiMirrors = [
    'https://api.github.com/repos/' . $githubRepo . '/commits/' . $branch,
];

$excludeFiles = [
    'sq.php',
    'auth_config.php',
    'update.php',
    'fix_update.php',
    'fix_v2.php',
    'fix_v3.php',
];

$excludeDirs = [
    'backups',
    'cache',
    '.git',
];

$protectedPatterns = [
    '/^gz\/rules_.*\.php$/',
];

$action = $_GET['action'] ?? 'check';

function getSystemInfo() {
    global $rootDir;
    
    $info = [
        'server' => [
            'php_version' => PHP_VERSION,
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_name' => $_SERVER['SERVER_NAME'] ?? 'Unknown',
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
        ],
        'permissions' => [],
        'github' => [],
    ];
    
    $checkDirs = [
        $rootDir,
        $rootDir . '/backups',
        $rootDir . '/cache',
        $rootDir . '/gz',
    ];
    
    foreach ($checkDirs as $dir) {
        $exists = is_dir($dir);
        $writable = $exists ? is_writable($dir) : false;
        $info['permissions'][] = [
            'path' => $dir,
            'exists' => $exists,
            'writable' => $writable,
            'permission' => $exists ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A',
        ];
    }
    
    $githubTest = curlRequest('https://api.github.com/repos/ssmhdssmhd/qcb/commits/main', ['timeout' => 5]);
    $info['github'] = [
        'reachable' => $githubTest['success'],
        'error' => $githubTest['error'] ?? '',
        'mirror' => $githubTest['mirror'] ?? '',
    ];
    
    return ['success' => true, 'data' => $info];
}

function getAuthInfo() {
    global $rootDir;
    
    $authFile = $rootDir . '/sq.php';
    $configFile = $rootDir . '/auth_config.php';
    
    $info = [
        'local' => [
            'file_exists' => file_exists($authFile),
            'file_size' => file_exists($authFile) ? filesize($authFile) : 0,
            'auth_code' => '',
        ],
        'config' => [],
        'remote' => [],
    ];
    
    if (file_exists($authFile)) {
        $content = file_get_contents($authFile);
        if (preg_match('/return \'(.*?)\';/', $content, $m)) {
            $info['local']['auth_code'] = $m[1];
        }
    }
    
    if (file_exists($configFile)) {
        $config = @include $configFile;
        if (is_array($config)) {
            $info['config'] = $config;
        }
    }
    
    if (!empty($info['config']['auth_server_ip']) && !empty($info['config']['auth_file'])) {
        $remoteUrl = 'http://' . $info['config']['auth_server_ip'] . ':' . ($info['config']['auth_server_port'] ?? '9001') . '/' . $info['config']['auth_file'];
        $remoteResult = curlRequest($remoteUrl, ['timeout' => 5]);
        $info['remote'] = [
            'url' => $remoteUrl,
            'reachable' => $remoteResult['success'],
            'content' => $remoteResult['response'] ?? '',
            'error' => $remoteResult['error'] ?? '',
        ];
    }
    
    return ['success' => true, 'data' => $info];
}

function getCurrentVersion() {
    global $rootDir;
    $versionFile = $rootDir . '/version.php';
    if (file_exists($versionFile)) {
        $ver = @include $versionFile;
        if (is_array($ver) && !empty($ver['version'])) {
            return $ver['version'];
        }
        if (is_string($ver)) {
            if (preg_match('/^v?\d+\.\d+\.\d+/', $ver, $m)) {
                return $m[0];
            }
            return substr($ver, 0, 7);
        }
    }
    return '未知';
}

function getCurrentCommit() {
    global $rootDir;
    $versionFile = $rootDir . '/version.php';
    if (file_exists($versionFile)) {
        $ver = @include $versionFile;
        if (is_array($ver) && !empty($ver['commit'])) {
            return $ver['commit'];
        }
        if (is_string($ver)) {
            if (preg_match('/-([a-f0-9]{7,})/', $ver, $m)) {
                return $m[1];
            }
            if (preg_match('/^[a-f0-9]{7,}$/', $ver)) {
                return $ver;
            }
        }
    }
    return '';
}

function parseVersionFromMessage($message) {
    if (preg_match('/v?(\d+\.\d+\.\d+)/', $message, $m)) {
        return 'v' . ltrim($m[1], 'v');
    }
    return null;
}

function curlRequest($url, $options = []) {
    global $downloadMirrors, $apiMirrors;
    
    $defaultOptions = [
        'timeout' => 30,
        'user_agent' => 'M3U8-Ad-Skipper-Updater',
        'follow_location' => true,
        'return_transfer' => true,
        'mirrors' => null,
    ];
    $options = array_merge($defaultOptions, $options);
    
    $mirrors = $options['mirrors'] ?? [$url];
    $tried = [];
    
    foreach ($mirrors as $mirror) {
        for ($retry = 0; $retry < 3; $retry++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $mirror);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, $options['return_transfer']);
            curl_setopt($ch, CURLOPT_USERAGENT, $options['user_agent']);
            curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $options['follow_location']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && $response !== false) {
                return [
                    'success' => true,
                    'response' => $response,
                    'http_code' => $httpCode,
                    'mirror' => $mirror,
                ];
            }
            
            $tried[] = [
                'mirror' => $mirror,
                'retry' => $retry + 1,
                'http_code' => $httpCode,
                'error' => $error ?: 'HTTP ' . $httpCode,
            ];
            
            if ($retry < 2) sleep(1);
        }
    }
    
    $lastError = !empty($tried) ? end($tried)['error'] : '未知错误';
    return [
        'success' => false,
        'error' => $lastError,
        'tried' => $tried,
    ];
}

function checkLatestVersion() {
    global $apiMirrors;
    
    $result = curlRequest($apiMirrors[0], [
        'mirrors' => $apiMirrors,
        'timeout' => 15,
    ]);
    
    if (!$result['success']) {
        return [
            'success' => false,
            'error' => $result['error'],
        ];
    }
    
    $data = json_decode($result['response'], true);
    if (!$data) {
        return [
            'success' => false,
            'error' => '解析版本信息失败',
        ];
    }
    
    $latestCommit = $data['sha'] ?? '';
    $latestMessage = $data['commit']['message'] ?? '无描述';
    $latestVersion = parseVersionFromMessage($latestMessage);
    if (!$latestVersion) {
        $latestVersion = substr($latestCommit, 0, 7);
    }
    
    return [
        'success' => true,
        'latest_commit' => $latestCommit,
        'latest_version' => $latestVersion,
        'latest_message' => $latestMessage,
        'latest_date' => $data['commit']['committer']['date'] ?? '',
        'mirror_used' => $result['mirror'],
    ];
}

function createBackup() {
    global $rootDir, $excludeFiles, $excludeDirs;
    
    if (!extension_loaded('zip')) {
        return ['success' => false, 'error' => 'PHP Zip 扩展未安装'];
    }
    
    $backupDir = $rootDir . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $timestamp = date('Ymd_His');
    $backupFile = $backupDir . '/backup_' . $timestamp . '.zip';
    
    $zip = new ZipArchive();
    if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return ['success' => false, 'error' => '无法创建备份文件'];
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($files as $file) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootDir) + 1);
        $relativePath = str_replace('\\', '/', $relativePath);
        
        $parts = explode('/', $relativePath);
        $firstPart = $parts[0] ?? '';
        if (in_array($firstPart, $excludeDirs)) continue;
        if (in_array(basename($filePath), $excludeFiles)) continue;
        
        if ($file->isDir()) {
            $zip->addEmptyDir($relativePath);
        } else {
            $zip->addFile($filePath, $relativePath);
        }
    }
    
    $zip->close();
    
    return [
        'success' => true,
        'backup_file' => $backupFile,
        'filename' => basename($backupFile),
        'size' => filesize($backupFile),
        'size_formatted' => formatSize(filesize($backupFile)),
    ];
}

function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < 3) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

function downloadUpdate() {
    global $downloadMirrors;
    
    $result = curlRequest($downloadMirrors[0], [
        'mirrors' => $downloadMirrors,
        'timeout' => 120,
    ]);
    
    if (!$result['success']) {
        return ['success' => false, 'error' => '下载失败: ' . $result['error'], 'tried' => $result['tried']];
    }
    
    $tempFile = tempnam(sys_get_temp_dir(), 'm3u8_update_');
    file_put_contents($tempFile, $result['response']);
    
    return [
        'success' => true,
        'temp_file' => $tempFile,
        'size' => strlen($result['response']),
        'size_formatted' => formatSize(strlen($result['response'])),
        'mirror_used' => $result['mirror'],
    ];
}

function extractUpdate($tempFile) {
    global $rootDir;
    
    if (!extension_loaded('zip')) {
        return ['success' => false, 'error' => 'PHP Zip 扩展未安装'];
    }
    
    $zip = new ZipArchive();
    if ($zip->open($tempFile) !== true) {
        return ['success' => false, 'error' => '无法打开更新包'];
    }
    
    $extractDir = sys_get_temp_dir() . '/m3u8_update_' . uniqid();
    mkdir($extractDir);
    $zip->extractTo($extractDir);
    $zip->close();
    
    $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
    if (empty($dirs)) {
        rrmdir($extractDir);
        unlink($tempFile);
        return ['success' => false, 'error' => '更新包格式错误'];
    }
    
    return [
        'success' => true,
        'source_dir' => $dirs[0],
        'extract_dir' => $extractDir,
        'temp_file' => $tempFile,
    ];
}

function applyUpdate($sourceDir) {
    global $rootDir, $excludeFiles, $excludeDirs, $protectedPatterns;
    
    $updated = 0;
    $skipped = 0;
    $errors = [];
    
    $sourceFiles = scanDirRecursive($sourceDir);
    
    foreach ($sourceFiles as $relPath) {
        $parts = explode('/', $relPath);
        $firstPart = $parts[0] ?? '';
        $fileName = basename($relPath);
        
        if (in_array($firstPart, $excludeDirs)) {
            $skipped++;
            continue;
        }
        
        if (in_array($fileName, $excludeFiles)) {
            $skipped++;
            continue;
        }
        
        $isProtected = false;
        foreach ($protectedPatterns as $pattern) {
            if (preg_match($pattern, $relPath)) {
                $isProtected = true;
                break;
            }
        }
        if ($isProtected && file_exists($rootDir . '/' . $relPath)) {
            $skipped++;
            continue;
        }
        
        $sourcePath = $sourceDir . '/' . $relPath;
        $targetPath = $rootDir . '/' . $relPath;
        
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        if (is_dir($sourcePath)) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            continue;
        }
        
        if (copy($sourcePath, $targetPath)) {
            $updated++;
        } else {
            $errors[] = $relPath;
        }
    }
    
    return [
        'success' => true,
        'updated_count' => $updated,
        'skipped_count' => $skipped,
        'errors' => $errors,
        'error_count' => count($errors),
    ];
}

function scanDirRecursive($dir) {
    $results = [];
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($files as $file) {
        $filePath = $file->getRealPath();
        $relPath = substr($filePath, strlen($dir) + 1);
        $relPath = str_replace('\\', '/', $relPath);
        $results[] = $relPath;
    }
    
    return $results;
}

function rrmdir($dir) {
    if (!is_dir($dir)) return;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        if ($fileinfo->isDir()) {
            rmdir($fileinfo->getRealPath());
        } else {
            unlink($fileinfo->getRealPath());
        }
    }
    rmdir($dir);
}

function cleanUp($tempFile, $extractDir) {
    if (file_exists($tempFile)) unlink($tempFile);
    if (is_dir($extractDir)) rrmdir($extractDir);
}

$currentVersion = getCurrentVersion();

if ($action === 'check' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    $latest = checkLatestVersion();
    echo json_encode($latest, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'system_info' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    $info = getSystemInfo();
    echo json_encode($info, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'auth_info' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    $info = getAuthInfo();
    echo json_encode($info, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'do_update' && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    
    $step = $_GET['step'] ?? 'backup';
    
    if ($step === 'backup') {
        $result = createBackup();
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($step === 'download') {
        $result = downloadUpdate();
        if ($result['success']) {
            session_start();
            $_SESSION['update_temp_file'] = $result['temp_file'];
            $_SESSION['update_latest_commit'] = $result['latest_commit'] ?? '';
            $_SESSION['update_latest_version'] = $result['latest_version'] ?? '';
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($step === 'extract') {
        session_start();
        $tempFile = $_SESSION['update_temp_file'] ?? '';
        if (empty($tempFile) || !file_exists($tempFile)) {
            echo json_encode(['success' => false, 'error' => '更新包文件不存在'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $result = extractUpdate($tempFile);
        if ($result['success']) {
            $_SESSION['update_source_dir'] = $result['source_dir'];
            $_SESSION['update_extract_dir'] = $result['extract_dir'];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($step === 'apply') {
        session_start();
        $sourceDir = $_SESSION['update_source_dir'] ?? '';
        $latestCommit = $_SESSION['update_latest_commit'] ?? '';
        $latestVersion = $_SESSION['update_latest_version'] ?? '';
        if (empty($sourceDir) || !is_dir($sourceDir)) {
            echo json_encode(['success' => false, 'error' => '解压目录不存在'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $result = applyUpdate($sourceDir);
        if ($result['success'] && !empty($latestCommit)) {
            global $rootDir;
            $versionFile = $rootDir . '/version.php';
            $versionContent = "<?php\nreturn [\n    'version' => '" . addslashes($latestVersion ?: substr($latestCommit, 0, 7)) . "',\n    'commit' => '" . addslashes($latestCommit) . "',\n    'updated_at' => '" . date('Y-m-d H:i:s') . "',\n];\n";
            file_put_contents($versionFile, $versionContent);
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($step === 'cleanup') {
        session_start();
        $tempFile = $_SESSION['update_temp_file'] ?? '';
        $extractDir = $_SESSION['update_extract_dir'] ?? '';
        cleanUp($tempFile, $extractDir);
        unset($_SESSION['update_temp_file']);
        unset($_SESSION['update_source_dir']);
        unset($_SESSION['update_extract_dir']);
        echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => '未知步骤'], JSON_UNESCAPED_UNICODE);
    exit;
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8 广告分析系统 - 在线更新</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 8px;
        }
        .header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px;
        }
        .version-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
            padding: 16px;
            background: #f5f7fa;
            border-radius: 8px;
        }
        .version-item {
            text-align: center;
            flex: 1;
        }
        .version-label {
            font-size: 12px;
            color: #909399;
            margin-bottom: 6px;
        }
        .version-value {
            font-size: 18px;
            font-weight: 600;
            color: #303133;
        }
        .version-value.latest {
            color: #67c23a;
        }
        .version-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #909399;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-secondary {
            background: #f0f2f5;
            color: #606266;
        }
        .progress-area {
            display: none;
            margin-top: 20px;
        }
        .progress-step {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #ebeef5;
        }
        .progress-step:last-child {
            border-bottom: none;
        }
        .step-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e4e7ed;
            color: #909399;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .step-icon.active {
            background: #409eff;
            color: #fff;
        }
        .step-icon.success {
            background: #67c23a;
            color: #fff;
        }
        .step-icon.error {
            background: #f56c6c;
            color: #fff;
        }
        .step-info {
            flex: 1;
        }
        .step-name {
            font-size: 14px;
            color: #303133;
            font-weight: 500;
        }
        .step-desc {
            font-size: 12px;
            color: #909399;
            margin-top: 2px;
        }
        .step-loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #e4e7ed;
            border-top-color: #409eff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 8px;
            vertical-align: middle;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .result-area {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
        }
        .result-area.success {
            background: #f0f9eb;
            border: 1px solid #e1f3d8;
        }
        .result-area.error {
            background: #fef0f0;
            border: 1px solid #fde2e2;
        }
        .result-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .result-area.success .result-title { color: #67c23a; }
        .result-area.error .result-title { color: #f56c6c; }
        .result-detail {
            font-size: 14px;
            color: #606266;
            line-height: 1.8;
        }
        .result-detail ul {
            margin-left: 20px;
            margin-top: 8px;
        }
        .footer {
            padding: 16px 30px;
            background: #f5f7fa;
            text-align: center;
            font-size: 12px;
            color: #909399;
        }
        .warning-box {
            padding: 12px;
            background: #fdf6ec;
            border: 1px solid #faecd8;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #e6a23c;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 在线更新</h1>
            <p>M3U8 广告分析与去广告系统</p>
        </div>
        <div class="content">
            <div class="version-info">
                <div class="version-item">
                    <div class="version-label">当前版本</div>
                    <div class="version-value" id="currentVersion"><?php echo htmlspecialchars($currentVersion); ?></div>
                </div>
                <div class="version-arrow">→</div>
                <div class="version-item">
                    <div class="version-label">最新版本</div>
                    <div class="version-value latest" id="latestVersion">检查中...</div>
                </div>
            </div>
            
            <div class="warning-box">
                ⚠️ 更新前建议先备份重要数据。更新过程中请勿关闭页面。<br>
                更新完成后请删除本 update.php 文件。
            </div>
            
            <button class="btn btn-primary" id="updateBtn" onclick="startUpdate()" disabled>
                <span id="btnText">正在检查更新...</span>
            </button>
            
            <div class="progress-area" id="progressArea">
                <div class="progress-step" id="step-backup">
                    <div class="step-icon">1</div>
                    <div class="step-info">
                        <div class="step-name">创建备份</div>
                        <div class="step-desc">备份当前版本文件</div>
                    </div>
                </div>
                <div class="progress-step" id="step-download">
                    <div class="step-icon">2</div>
                    <div class="step-info">
                        <div class="step-name">下载更新包</div>
                        <div class="step-desc">从 GitHub 下载最新版本</div>
                    </div>
                </div>
                <div class="progress-step" id="step-extract">
                    <div class="step-icon">3</div>
                    <div class="step-info">
                        <div class="step-name">解压更新包</div>
                        <div class="step-desc">准备更新文件</div>
                    </div>
                </div>
                <div class="progress-step" id="step-apply">
                    <div class="step-icon">4</div>
                    <div class="step-info">
                        <div class="step-name">应用更新</div>
                        <div class="step-desc">替换文件并更新版本</div>
                    </div>
                </div>
                <div class="progress-step" id="step-cleanup">
                    <div class="step-icon">5</div>
                    <div class="step-info">
                        <div class="step-name">清理临时文件</div>
                        <div class="step-desc">完成更新</div>
                    </div>
                </div>
            </div>
            
            <div class="result-area success" id="successResult">
                <div class="result-title">🎉 更新成功！</div>
                <div class="result-detail" id="successDetail"></div>
            </div>
            
            <div class="result-area error" id="errorResult">
                <div class="result-title">❌ 更新失败</div>
                <div class="result-detail" id="errorDetail"></div>
            </div>
        </div>
        <div class="footer">
            仓库地址：<a href="https://github.com/ssmhdssmhd/qcb" target="_blank" style="color:#409eff;text-decoration:none">ssmhdssmhd/qcb</a>
        </div>
    </div>
    
    <script>
        let latestInfo = null;
        let hasUpdate = false;
        
        async function checkUpdate() {
            try {
                const res = await fetch('?action=check', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                
                if (data.success) {
                    latestInfo = data;
                    const displayVersion = data.latest_version || data.latest_commit.substring(0, 7);
                    document.getElementById('latestVersion').textContent = displayVersion;
                    
                    const currentVer = document.getElementById('currentVersion').textContent;
                    const currentCommit = '<?php echo addslashes(getCurrentCommit()); ?>';
                    hasUpdate = !currentCommit || data.latest_commit.indexOf(currentCommit) !== 0;
                    
                    const btn = document.getElementById('updateBtn');
                    const btnText = document.getElementById('btnText');
                    
                    if (hasUpdate) {
                        btn.disabled = false;
                        btnText.textContent = '立即更新';
                    } else {
                        btn.disabled = false;
                        btnText.textContent = '已是最新版本';
                    }
                } else {
                    document.getElementById('latestVersion').textContent = '检查失败';
                    document.getElementById('btnText').textContent = '检查失败，重试';
                    document.getElementById('updateBtn').disabled = false;
                }
            } catch (e) {
                document.getElementById('latestVersion').textContent = '检查失败';
                document.getElementById('btnText').textContent = '检查失败，重试';
                document.getElementById('updateBtn').disabled = false;
            }
        }
        
        function setStepStatus(stepId, status, desc = '') {
            const step = document.getElementById('step-' + stepId);
            if (!step) return;
            
            const icon = step.querySelector('.step-icon');
            const stepDesc = step.querySelector('.step-desc');
            
            icon.className = 'step-icon';
            
            if (status === 'active') {
                icon.classList.add('active');
                icon.innerHTML = '<span class="step-loading"></span>';
            } else if (status === 'success') {
                icon.classList.add('success');
                icon.innerHTML = '✓';
            } else if (status === 'error') {
                icon.classList.add('error');
                icon.innerHTML = '✕';
            }
            
            if (desc) {
                stepDesc.textContent = desc;
            }
        }
        
        async function startUpdate() {
            if (!hasUpdate) {
                checkUpdate();
                return;
            }
            
            const btn = document.getElementById('updateBtn');
            btn.disabled = true;
            document.getElementById('btnText').textContent = '更新中...';
            document.getElementById('progressArea').style.display = 'block';
            document.getElementById('successResult').style.display = 'none';
            document.getElementById('errorResult').style.display = 'none';
            
            try {
                setStepStatus('backup', 'active', '正在备份...');
                const backupRes = await fetch('?action=do_update&step=backup', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const backupData = await backupRes.json();
                
                if (!backupData.success) {
                    setStepStatus('backup', 'error', backupData.error);
                    throw new Error('备份失败: ' + backupData.error);
                }
                setStepStatus('backup', 'success', '备份完成: ' + backupData.size_formatted);
                
                setStepStatus('download', 'active', '正在下载...');
                const dlRes = await fetch('?action=do_update&step=download', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const dlData = await dlRes.json();
                
                if (!dlData.success) {
                    setStepStatus('download', 'error', dlData.error);
                    throw new Error('下载失败: ' + dlData.error);
                }
                setStepStatus('download', 'success', '下载完成: ' + dlData.size_formatted);
                
                setStepStatus('extract', 'active', '正在解压...');
                const exRes = await fetch('?action=do_update&step=extract', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const exData = await exRes.json();
                
                if (!exData.success) {
                    setStepStatus('extract', 'error', exData.error);
                    throw new Error('解压失败: ' + exData.error);
                }
                setStepStatus('extract', 'success', '解压完成');
                
                setStepStatus('apply', 'active', '正在更新...');
                const applyRes = await fetch('?action=do_update&step=apply', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const applyData = await applyRes.json();
                
                if (!applyData.success) {
                    setStepStatus('apply', 'error', applyData.error);
                    throw new Error('更新失败: ' + applyData.error);
                }
                setStepStatus('apply', 'success', '更新完成: ' + applyData.updated_count + ' 个文件');
                
                setStepStatus('cleanup', 'active', '正在清理...');
                await fetch('?action=do_update&step=cleanup', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                setStepStatus('cleanup', 'success', '清理完成');
                
                document.getElementById('successResult').style.display = 'block';
                document.getElementById('successDetail').innerHTML = 
                    '<ul>' +
                    '<li>更新文件数：' + applyData.updated_count + ' 个</li>' +
                    '<li>跳过文件数：' + applyData.skipped_count + ' 个</li>' +
                    '<li>备份文件：' + backupData.filename + '</li>' +
                    '</ul>' +
                    '<p style="margin-top:12px;color:#67c23a;font-weight:500">请删除 update.php 文件以保证安全。</p>';
                
                document.getElementById('btnText').textContent = '更新完成';
                document.getElementById('currentVersion').textContent = latestInfo.latest_version || latestInfo.latest_commit.substring(0, 7);
                
            } catch (e) {
                document.getElementById('errorResult').style.display = 'block';
                document.getElementById('errorDetail').textContent = e.message;
                document.getElementById('btnText').textContent = '更新失败，重试';
                document.getElementById('updateBtn').disabled = false;
                
                fetch('?action=do_update&step=cleanup', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).catch(() => {});
            }
        }
        
        checkUpdate();
    </script>
</body>
</html>
