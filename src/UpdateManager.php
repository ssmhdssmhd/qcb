<?php

require_once __DIR__ . '/AuthValidator.php';

class UpdateManager
{
    private $currentVersion = '1.2.0';
    private $backupDir;
    private $rootDir;
    private $githubRepo = 'ssmhdssmhd/qcb';
    private $githubBranch = 'main';
    private $authValidator;
    private $coreFiles = [
        'index.php',
        'mx.php',
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
        'gz/EnhancedAdRuleEngine.php'
    ];
    private $skipPhpCheckFiles = [
        'mxadmin.php'
    ];

    public function __construct()
    {
        $this->rootDir = dirname(__DIR__);
        $this->backupDir = $this->rootDir . '/backups';
        $this->authValidator = new AuthValidator();
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function validateAuthBeforeUpdate()
    {
        if (!$this->authValidator->validateLocal()) {
            return [
                'success' => false,
                'message' => '授权验证失败: ' . $this->authValidator->getLastError()
            ];
        }
        return ['success' => true];
    }

    public function verifyCoreFiles()
    {
        $missingFiles = [];
        $invalidFiles = [];
        foreach ($this->coreFiles as $file) {
            $filePath = $this->rootDir . '/' . $file;
            if (!file_exists($filePath)) {
                $missingFiles[] = $file;
                continue;
            }
            if (!in_array($file, $this->skipPhpCheckFiles)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if ($ext === 'php') {
                    $content = file_get_contents($filePath);
                    if (strpos($content, '<?php') === false) {
                        $invalidFiles[] = $file;
                    }
                }
            }
        }
        return [
            'success' => empty($missingFiles) && empty($invalidFiles),
            'missing_files' => $missingFiles,
            'invalid_files' => $invalidFiles,
            'total_checked' => count($this->coreFiles)
        ];
    }

    public function checkIntegrity()
    {
        $sqFile = $this->rootDir . '/sq.txt';
        $authConfig = $this->rootDir . '/auth_config.json';
        $issues = [];

        if (!file_exists($sqFile)) {
            $issues[] = '授权文件 sq.txt 不存在';
        } else {
            $sqContent = trim(file_get_contents($sqFile));
            if (empty($sqContent)) {
                $issues[] = '授权文件 sq.txt 内容为空';
            }
            if (!$this->authValidator->validateLocal()) {
                $issues[] = '授权验证失败: ' . $this->authValidator->getLastError();
            }
        }

        if (!file_exists($authConfig)) {
            $issues[] = '授权配置文件 auth_config.json 不存在';
        }

        $coreCheck = $this->verifyCoreFiles();
        if (!$coreCheck['success']) {
            foreach ($coreCheck['missing_files'] as $f) {
                $issues[] = '核心文件缺失: ' . $f;
            }
            foreach ($coreCheck['invalid_files'] as $f) {
                $issues[] = '核心文件损坏: ' . $f;
            }
        }

        return [
            'success' => empty($issues),
            'issues' => $issues,
            'sq_exists' => file_exists($sqFile),
            'auth_valid' => $this->authValidator->validateLocal(),
            'core_files_ok' => $coreCheck['success']
        ];
    }

    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    public function getBackupList()
    {
        $backups = [];
        if (is_dir($this->backupDir)) {
            $files = glob($this->backupDir . '/backup_*.zip');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'size_formatted' => $this->formatSize(filesize($file)),
                    'created' => filemtime($file),
                    'created_formatted' => date('Y-m-d H:i:s', filemtime($file))
                ];
            }
            usort($backups, function($a, $b) {
                return $b['created'] - $a['created'];
            });
        }
        return $backups;
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function checkUpdate()
    {
        $apiUrl = 'https://api.github.com/repos/' . $this->githubRepo . '/commits/' . $this->githubBranch;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return [
                'success' => false,
                'message' => '无法连接到 GitHub 服务器'
            ];
        }

        $data = json_decode($response, true);
        if (!$data) {
            return [
                'success' => false,
                'message' => '解析版本信息失败'
            ];
        }

        $latestCommit = $data['sha'] ?? '';
        $commitMessage = $data['commit']['message'] ?? '无描述';
        $commitDate = $data['commit']['committer']['date'] ?? '';

        $versionFile = $this->rootDir . '/version.txt';
        $currentCommit = '';
        if (file_exists($versionFile)) {
            $currentCommit = trim(file_get_contents($versionFile));
        }

        $hasUpdate = $latestCommit !== $currentCommit;

        return [
            'success' => true,
            'current_version' => $this->currentVersion,
            'current_commit' => $currentCommit,
            'latest_commit' => $latestCommit,
            'latest_message' => $commitMessage,
            'latest_date' => $commitDate,
            'has_update' => $hasUpdate,
            'repo_url' => 'https://github.com/' . $this->githubRepo
        ];
    }

    public function createBackup()
    {
        $timestamp = date('Ymd_His');
        $backupFile = $this->backupDir . '/backup_' . $timestamp . '.zip';

        if (!extension_loaded('zip')) {
            return [
                'success' => false,
                'message' => 'PHP Zip 扩展未安装'
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return [
                'success' => false,
                'message' => '无法创建备份文件'
            ];
        }

        $excludeDirs = ['backups', 'test', '.git'];
        $excludeFiles = ['sq.txt'];

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->rootDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($this->rootDir) + 1);

            $relativePath = str_replace('\\', '/', $relativePath);
            $parts = explode('/', $relativePath);
            $firstPart = $parts[0] ?? '';

            if (in_array($firstPart, $excludeDirs)) {
                continue;
            }
            if (in_array(basename($filePath), $excludeFiles)) {
                continue;
            }

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
            'size_formatted' => $this->formatSize(filesize($backupFile))
        ];
    }

    public function restoreBackup($filename)
    {
        $backupFile = $this->backupDir . '/' . basename($filename);

        if (!file_exists($backupFile)) {
            return [
                'success' => false,
                'message' => '备份文件不存在'
            ];
        }

        if (!extension_loaded('zip')) {
            return [
                'success' => false,
                'message' => 'PHP Zip 扩展未安装'
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($backupFile) !== true) {
            return [
                'success' => false,
                'message' => '无法打开备份文件'
            ];
        }

        $excludeFiles = ['sq.txt', 'auth_config.json'];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (in_array(basename($filename), $excludeFiles)) {
                continue;
            }
            $zip->extractTo($this->rootDir, $filename);
        }
        $zip->close();

        return [
            'success' => true,
            'message' => '备份恢复成功（已保留授权文件）'
        ];
    }

    public function deleteBackup($filename)
    {
        $backupFile = $this->backupDir . '/' . basename($filename);

        if (!file_exists($backupFile)) {
            return [
                'success' => false,
                'message' => '备份文件不存在'
            ];
        }

        if (unlink($backupFile)) {
            return [
                'success' => true,
                'message' => '备份已删除'
            ];
        }

        return [
            'success' => false,
            'message' => '删除失败'
        ];
    }

    public function downloadUpdate()
    {
        $authCheck = $this->validateAuthBeforeUpdate();
        if (!$authCheck['success']) {
            return $authCheck;
        }

        $checkResult = $this->checkUpdate();
        if (!$checkResult['success']) {
            return $checkResult;
        }

        if (!$checkResult['has_update']) {
            return [
                'success' => false,
                'message' => '当前已是最新版本，无需更新'
            ];
        }

        $backupResult = $this->createBackup();
        if (!$backupResult['success']) {
            return [
                'success' => false,
                'message' => '创建备份失败: ' . $backupResult['message']
            ];
        }

        $downloadUrl = 'https://github.com/' . $this->githubRepo . '/archive/refs/heads/' . $this->githubBranch . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'update_');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $downloadUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $fileContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$fileContent) {
            return [
                'success' => false,
                'message' => '下载更新包失败'
            ];
        }

        file_put_contents($tempFile, $fileContent);

        if (!extension_loaded('zip')) {
            unlink($tempFile);
            return [
                'success' => false,
                'message' => 'PHP Zip 扩展未安装'
            ];
        }

        $zip = new ZipArchive();
        if ($zip->open($tempFile) !== true) {
            unlink($tempFile);
            return [
                'success' => false,
                'message' => '无法打开更新包'
            ];
        }

        $extractDir = sys_get_temp_dir() . '/m3u8_update_' . uniqid();
        mkdir($extractDir);
        $zip->extractTo($extractDir);
        $zip->close();

        $dirs = glob($extractDir . '/*', GLOB_ONLYDIR);
        if (empty($dirs)) {
            unlink($tempFile);
            $this->rrmdir($extractDir);
            return [
                'success' => false,
                'message' => '更新包格式错误'
            ];
        }

        $sourceDir = $dirs[0];
        $cleanedFiles = $this->cleanOrphanedFiles($sourceDir);
        $this->copyDirectory($sourceDir, $this->rootDir);

        $versionFile = $this->rootDir . '/version.txt';
        file_put_contents($versionFile, $checkResult['latest_commit']);

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        clearstatcache(true);

        unlink($tempFile);
        $this->rrmdir($extractDir);

        $integrityCheck = $this->checkIntegrity();

        return [
            'success' => true,
            'message' => '更新成功',
            'backup_file' => $backupResult['filename'],
            'new_version' => $checkResult['latest_commit'],
            'cleaned_files' => $cleanedFiles,
            'integrity_check' => $integrityCheck
        ];
    }

    private function cleanOrphanedFiles($sourceDir)
    {
        $cleanedFiles = [];
        $excludeDirs = ['backups', '.git'];
        $excludeFiles = ['sq.txt', 'auth_config.json', 'fix_update.php'];

        $currentFiles = $this->scanDirectory($this->rootDir, $excludeDirs, $excludeFiles);
        $newFiles = $this->scanDirectory($sourceDir, $excludeDirs, $excludeFiles);

        $orphanedFiles = array_diff($currentFiles, $newFiles);

        foreach ($orphanedFiles as $file) {
            $filePath = $this->rootDir . '/' . $file;
            if (file_exists($filePath)) {
                if (is_dir($filePath)) {
                    $this->rrmdir($filePath);
                    $cleanedFiles[] = '删除目录: ' . $file;
                } else {
                    unlink($filePath);
                    $cleanedFiles[] = '删除文件: ' . $file;
                }
            }
        }

        return $cleanedFiles;
    }

    private function scanDirectory($dir, $excludeDirs = [], $excludeFiles = [])
    {
        $files = [];
        if (!is_dir($dir)) {
            return $files;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $rootLen = strlen($dir);
        foreach ($iterator as $item) {
            $relativePath = substr($item->getRealPath(), $rootLen + 1);
            $relativePath = str_replace('\\', '/', $relativePath);

            $parts = explode('/', $relativePath);
            $firstPart = $parts[0] ?? '';

            if (in_array($firstPart, $excludeDirs) || in_array(basename($relativePath), $excludeFiles)) {
                continue;
            }

            $files[] = $relativePath;
        }

        return $files;
    }

    private function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        $excludeFiles = ['sq.txt', 'auth_config.json'];
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;
            if (in_array($file, $excludeFiles)) continue;
            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;
            if (is_dir($srcPath)) {
                $this->copyDirectory($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
        closedir($dir);
    }

    private function rrmdir($dir)
    {
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
}
