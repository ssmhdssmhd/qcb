<?php

require_once __DIR__ . '/AuthValidator.php';

class UpdateManager
{
    private $currentVersion = '2.3.0';
    private $backupDir;
    private $rootDir;
    private $githubRepo = 'ssmhdssmhd/qcb';
    private $githubBranch = 'main';
    private $authValidator;
    private $coreFiles = [
        'index.php',
        'mx.php',
        'router.php',
        'cron_autolearn.php',
        'cron_logs.php',
        'src/M3U8AdSkipper.php',
        'src/M3U8Parser.php',
        'src/AdFilter.php',
        'src/AdRuleEngine.php',
        'src/OutputGenerator.php',
        'src/AuthValidator.php',
        'src/AuthConfig.php',
        'src/CryptoUtil.php',
        'src/UpdateManager.php',
        'src/CacheManager.php',
        'gz/DomainRuleManager.php',
        'gz/EnhancedAdRuleEngine.php',
        'gz/ResourceSiteManager.php',
        'gz/sites_config.php',
        'gz/gzgx.php'
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
        $sqFile = $this->rootDir . '/sq.php';
        $authConfig = $this->rootDir . '/auth_config.php';
        $issues = [];

        if (!file_exists($sqFile)) {
            $issues[] = '授权文件 sq.php 不存在';
        } else {
            $sqCode = @include $sqFile;
            if (!is_string($sqCode) || empty(trim($sqCode))) {
                $issues[] = '授权文件 sq.php 内容为空';
            }
            if (!$this->authValidator->validateLocal()) {
                $issues[] = '授权验证失败: ' . $this->authValidator->getLastError();
            }
        }

        if (!file_exists($authConfig)) {
            $issues[] = '授权配置文件 auth_config.php 不存在';
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

        $versionFile = $this->rootDir . '/version.php';
        $currentCommit = '';
        if (file_exists($versionFile)) {
            $ver = @include $versionFile;
            if (is_string($ver)) {
                $currentCommit = trim($ver);
            } else {
                $currentCommit = trim(file_get_contents($versionFile));
            }
        }

        $hasUpdate = $latestCommit !== $currentCommit;

        $changelog = $this->getRecentCommits($currentCommit);

        return [
            'success' => true,
            'current_version' => $this->currentVersion,
            'current_commit' => $currentCommit,
            'latest_commit' => $latestCommit,
            'latest_message' => $commitMessage,
            'latest_date' => $commitDate,
            'has_update' => $hasUpdate,
            'repo_url' => 'https://github.com/' . $this->githubRepo,
            'changelog' => $changelog
        ];
    }

    private function getRecentCommits($sinceCommit = '')
    {
        $changelog = [];
        $apiUrl = 'https://api.github.com/repos/' . $this->githubRepo . '/commits?sha=' . $this->githubBranch . '&per_page=20';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return $changelog;
        }

        $commits = json_decode($response, true);
        if (!is_array($commits)) {
            return $changelog;
        }

        $foundCurrent = false;
        foreach ($commits as $commit) {
            $sha = $commit['sha'] ?? '';
            if (!empty($sinceCommit) && strpos($sha, $sinceCommit) === 0) {
                $foundCurrent = true;
                break;
            }

            $message = $commit['commit']['message'] ?? '';
            $message = explode("\n", trim($message))[0] ?? '';
            $date = $commit['commit']['committer']['date'] ?? '';
            $author = $commit['commit']['author']['name'] ?? 'Unknown';

            $type = 'feat';
            $typeLabel = '功能更新';
            $typeColor = '#67c23a';

            if (preg_match('/^(\w+):/', $message, $m)) {
                $type = strtolower($m[1]);
                $message = trim(substr($message, strlen($m[0])));
            }

            $typeMap = [
                'feat' => ['label' => '✨ 新功能', 'color' => '#67c23a'],
                'fix' => ['label' => '🐛 修复', 'color' => '#f56c6c'],
                'perf' => ['label' => '⚡ 优化', 'color' => '#e6a23c'],
                'refactor' => ['label' => '♻️ 重构', 'color' => '#909399'],
                'docs' => ['label' => '📝 文档', 'color' => '#409eff'],
                'style' => ['label' => '💄 样式', 'color' => '#d87fd2'],
                'chore' => ['label' => '🔧 工具', 'color' => '#909399'],
                'merge' => ['label' => '🔀 合并', 'color' => '#409eff'],
                'wip' => ['label' => '🚧 开发中', 'color' => '#e6a23c'],
            ];

            if (isset($typeMap[$type])) {
                $typeLabel = $typeMap[$type]['label'];
                $typeColor = $typeMap[$type]['color'];
            }

            $changelog[] = [
                'sha' => substr($sha, 0, 7),
                'full_sha' => $sha,
                'message' => $message,
                'date' => $date,
                'date_formatted' => !empty($date) ? date('Y-m-d H:i', strtotime($date)) : '',
                'author' => $author,
                'type' => $type,
                'type_label' => $typeLabel,
                'type_color' => $typeColor
            ];
        }

        return $changelog;
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
        $excludeFiles = ['sq.php'];

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

        $excludeFiles = ['sq.php', 'auth_config.php'];
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

        $versionFile = $this->rootDir . '/version.php';
        $versionContent = "<?php\nreturn '" . $checkResult['latest_commit'] . "';\n";
        file_put_contents($versionFile, $versionContent);

        $this->clearAllCaches();

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
        $skippedFiles = [];
        $excludeDirs = ['backups', '.git'];
        $excludeFiles = ['sq.php', 'auth_config.php', 'fix_update.php'];
        $protectedPatterns = ['/^gz\/rules_.*\.php$/'];
        $systemFiles = [
            '.user.ini',
            '.htaccess',
            '.htpasswd',
            '.well-known',
            'robots.txt',
            'favicon.ico',
            '404.html',
            'index.html',
        ];
        $systemPrefixes = ['.', 'nginx.', 'apache.'];

        $currentFiles = $this->scanDirectory($this->rootDir, $excludeDirs, $excludeFiles);
        $newFiles = $this->scanDirectory($sourceDir, $excludeDirs, $excludeFiles);

        $orphanedFiles = array_diff($currentFiles, $newFiles);

        foreach ($orphanedFiles as $file) {
            $baseName = basename($file);
            $firstPart = explode('/', $file)[0] ?? '';

            $isProtected = false;
            foreach ($protectedPatterns as $pattern) {
                if (preg_match($pattern, $file)) {
                    $isProtected = true;
                    break;
                }
            }
            if ($isProtected) {
                $skippedFiles[] = '跳过用户规则文件: ' . $file;
                continue;
            }

            $isSystemFile = false;
            foreach ($systemFiles as $sf) {
                if (strcasecmp($baseName, $sf) === 0 || strcasecmp($firstPart, $sf) === 0) {
                    $isSystemFile = true;
                    break;
                }
            }
            if (!$isSystemFile) {
                foreach ($systemPrefixes as $prefix) {
                    if (stripos($baseName, $prefix) === 0 || stripos($firstPart, $prefix) === 0) {
                        $isSystemFile = true;
                        break;
                    }
                }
            }

            if ($isSystemFile) {
                $skippedFiles[] = '跳过系统文件: ' . $file;
                continue;
            }

            $filePath = $this->rootDir . '/' . $file;
            if (file_exists($filePath)) {
                if (is_dir($filePath)) {
                    if ($this->safeRemoveDir($filePath)) {
                        $cleanedFiles[] = '删除目录: ' . $file;
                    } else {
                        $skippedFiles[] = '删除目录失败(权限不足): ' . $file;
                    }
                } else {
                    if (@unlink($filePath)) {
                        $cleanedFiles[] = '删除文件: ' . $file;
                    } else {
                        $skippedFiles[] = '删除文件失败(权限不足): ' . $file;
                    }
                }
            }
        }

        return array_merge($cleanedFiles, $skippedFiles);
    }

    private function safeRemoveDir($dir)
    {
        if (!is_dir($dir)) return false;
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            if ($item->isDir()) {
                if (!@rmdir($item->getRealPath())) return false;
            } else {
                if (!@unlink($item->getRealPath())) return false;
            }
        }
        return @rmdir($dir);
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
        $excludeFiles = ['sq.php', 'auth_config.php'];
        $excludePatterns = ['/^rules_.*\.php$/'];
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;
            if (in_array($file, $excludeFiles)) continue;
            $isExcluded = false;
            foreach ($excludePatterns as $pattern) {
                if (preg_match($pattern, $file)) {
                    $isExcluded = true;
                    break;
                }
            }
            if ($isExcluded) continue;
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

    public function clearAllCaches()
    {
        $results = [];

        if (function_exists('opcache_reset')) {
            $results['opcache_reset'] = opcache_reset();
        }

        if (function_exists('opcache_invalidate')) {
            $phpFiles = $this->scanDirectory($this->rootDir, ['.git', 'backups'], []);
            $invalidated = 0;
            foreach ($phpFiles as $file) {
                $fullPath = $this->rootDir . '/' . $file;
                if (pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
                    if (opcache_invalidate($fullPath, true)) {
                        $invalidated++;
                    }
                }
            }
            $results['opcache_invalidate'] = $invalidated . ' files';
        }

        if (function_exists('apc_clear_cache')) {
            $results['apc_clear_cache'] = apc_clear_cache();
        }

        if (function_exists('apcu_clear_cache')) {
            $results['apcu_clear_cache'] = apcu_clear_cache();
        }

        clearstatcache(true);

        if (function_exists('realpath_cache_size')) {
            $results['realpath_cache_before'] = realpath_cache_size();
        }

        $results['clearstatcache'] = true;

        return $results;
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
