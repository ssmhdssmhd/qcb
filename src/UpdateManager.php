<?php

require_once __DIR__ . '/AuthValidator.php';

class UpdateManager
{
    private $currentVersion = '2.29.2';
    private $backupDir;
    private $rootDir;
    private $githubRepo = 'ssmhdssmhd/qcb';
    private $githubBranch = 'main';
    private $authValidator;
    // GitHub API 镜像列表（按优先级排序，直连 + 多个国内代理）
    private $githubApiMirrors = [
        'https://api.github.com',
        'https://ghp.ci/https://api.github.com',
        'https://ghproxy.net/https://api.github.com',
        'https://gh-proxy.com/https://api.github.com',
        'https://mirror.ghproxy.com/https://api.github.com',
    ];
    // GitHub Raw 文件镜像列表
    private $githubRawMirrors = [
        'https://raw.githubusercontent.com',
        'https://cdn.jsdelivr.net/gh',
        'https://fastly.jsdelivr.net/gh',
        'https://gcore.jsdelivr.net/gh',
        'https://raw.staticdn.net',
        'https://ghp.ci/https://raw.githubusercontent.com',
        'https://ghproxy.net/https://raw.githubusercontent.com',
    ];
    // GitHub 下载镜像列表
    private $githubDownloadMirrors = [
        'https://github.com',
        'https://ghp.ci/https://github.com',
        'https://ghproxy.net/https://github.com',
        'https://gh-proxy.com/https://github.com',
        'https://mirror.ghproxy.com/https://github.com',
    ];
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
        $versionFile = $this->rootDir . '/version.php';
        if (file_exists($versionFile)) {
            $ver = @include $versionFile;
            if (is_array($ver) && !empty($ver['version'])) {
                return $ver['version'];
            }
            if (is_string($ver)) {
                if (preg_match('/^v?\d+\.\d+\.\d+/', $ver, $m)) {
                    return $m[0];
                }
                return $ver;
            }
        }
        return $this->currentVersion;
    }

    public function getCurrentCommit()
    {
        // 优先通过 git 命令动态获取当前 commit 哈希
        $gitDir = dirname($this->rootDir) . '/.git';
        if (is_dir($gitDir) || is_file($gitDir)) {
            $cwd = getcwd();
            @chdir($this->rootDir);
            $hash = @trim(shell_exec('git rev-parse HEAD 2>/dev/null') ?? '');
            @chdir($cwd);
            if ($hash && preg_match('/^[a-f0-9]{40}$/', $hash)) {
                return $hash;
            }
        }

        // 回退到 version.php 中的静态哈希
        $versionFile = $this->rootDir . '/version.php';
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

    private function fetchLatestVersionFromGitHub()
    {
        $result = $this->githubRawGet('version.php', 8);
        if ($result['success'] && $result['data']) {
            if (preg_match('/[\'"]version[\'"]\s*=>\s*[\'"](v?\d+\.\d+\.\d+)[\'"]/', $result['data'], $m)) {
                return $m[1];
            }
        }
        return null;
    }

    private function parseVersionFromMessage($message)
    {
        if (preg_match('/v?(\d+\.\d+\.\d+)/', $message, $m)) {
            return 'v' . ltrim($m[1], 'v');
        }
        return null;
    }

    private function compareVersions($v1, $v2)
    {
        $v1 = ltrim($v1 ?? '', 'v');
        $v2 = ltrim($v2 ?? '', 'v');
        if (!preg_match('/^\d+\.\d+\.\d+$/', $v1) || !preg_match('/^\d+\.\d+\.\d+$/', $v2)) {
            return null;
        }
        return version_compare($v1, $v2);
    }

    private function writeVersionFile($version, $commit)
    {
        $versionFile = $this->rootDir . '/version.php';
        $content = "<?php\nreturn [\n    'version' => '" . addslashes($version) . "',\n    'commit' => '" . addslashes($commit) . "',\n    'updated_at' => '" . date('Y-m-d H:i:s') . "',\n];\n";
        return file_put_contents($versionFile, $content) !== false;
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

    public function getSystemInfo()
    {
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
            $this->rootDir,
            $this->rootDir . '/backups',
            $this->rootDir . '/cache',
            $this->rootDir . '/gz',
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

        $apiPath = '/repos/' . $this->githubRepo . '/commits/' . $this->githubBranch;
        $githubTest = $this->githubCurlGet($apiPath, 5);
        $info['github'] = [
            'reachable' => $githubTest['success'],
            'error' => $githubTest['error'] ?? '',
            'mirror' => $githubTest['mirror'] ?? '',
            'tested_mirrors' => count($this->githubApiMirrors),
        ];

        return ['success' => true, 'data' => $info];
    }

    /**
     * 统一的 GitHub 多镜像 curl GET 请求
     * 依次尝试多个镜像，返回第一个成功的响应
     * @param string $path GitHub API 路径（如 /repos/...）
     * @param int $timeout 单个镜像超时秒数
     * @return array ['success'=>bool, 'data'=>string, 'mirror'=>string, 'error'=>string]
     */
    private function githubCurlGet($path, $timeout = 8)
    {
        $errors = [];
        foreach ($this->githubApiMirrors as $mirror) {
            $url = rtrim($mirror, '/') . $path;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $response !== false) {
                return ['success' => true, 'data' => $response, 'mirror' => $url, 'error' => ''];
            }
            $errors[] = $mirror . ': ' . ($error ?: 'HTTP ' . $httpCode);
        }
        return ['success' => false, 'data' => '', 'mirror' => '', 'error' => implode(' | ', $errors)];
    }

    /**
     * 统一的 GitHub Raw 文件多镜像请求
     * @param string $filePath 仓库内文件路径（如 version.php）
     * @param int $timeout 单个镜像超时秒数
     * @return array ['success'=>bool, 'data'=>string, 'mirror'=>string]
     */
    private function githubRawGet($filePath, $timeout = 8)
    {
        foreach ($this->githubRawMirrors as $i => $mirror) {
            // jsdelivr 格式: cdn.jsdelivr.net/gh/user/repo@branch/file
            if (strpos($mirror, 'jsdelivr.net') !== false) {
                $url = $mirror . '/' . $this->githubRepo . '@' . $this->githubBranch . '/' . $filePath;
            } elseif (strpos($mirror, 'staticdn.net') !== false) {
                $url = $mirror . '/' . $this->githubRepo . '/' . $this->githubBranch . '/' . $filePath;
            } elseif (strpos($mirror, 'ghp.ci') !== false || strpos($mirror, 'ghproxy') !== false) {
                $url = $mirror . '/https://raw.githubusercontent.com/' . $this->githubRepo . '/' . $this->githubBranch . '/' . $filePath;
            } else {
                $url = $mirror . '/' . $this->githubRepo . '/' . $this->githubBranch . '/' . $filePath;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                return ['success' => true, 'data' => $response, 'mirror' => $url];
            }
        }
        return ['success' => false, 'data' => '', 'mirror' => ''];
    }

    /**
     * 统一的 GitHub 下载文件多镜像请求（用于下载 zip 等大文件）
     * @param string $downloadPath GitHub 下载路径（如 /user/repo/archive/refs/heads/main.zip）
     * @param int $timeout 单个镜像超时秒数
     * @return array ['success'=>bool, 'data'=>string, 'mirror'=>string, 'error'=>string]
     */
    private function githubDownloadFile($downloadPath, $timeout = 60)
    {
        $errors = [];
        foreach ($this->githubDownloadMirrors as $mirror) {
            $url = rtrim($mirror, '/') . $downloadPath;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-Ad-Skipper');
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200 && $response !== false && strlen($response) > 1024) {
                return ['success' => true, 'data' => $response, 'mirror' => $url, 'error' => ''];
            }
            $errors[] = $mirror . ': ' . ($error ?: 'HTTP ' . $httpCode);
        }
        return ['success' => false, 'data' => '', 'mirror' => '', 'error' => implode(' | ', $errors)];
    }

    public function checkUpdate()
    {
        $currentVersion = $this->getCurrentVersion();
        $currentCommit = $this->getCurrentCommit();
        $latestVersion = null;
        $latestCommit = '';
        $commitMessage = '无描述';
        $commitDate = '';
        $githubConnected = false;

        $apiPath = '/repos/' . $this->githubRepo . '/commits/' . $this->githubBranch;
        $result = $this->githubCurlGet($apiPath, 8);

        if ($result['success']) {
            $data = json_decode($result['data'], true);
            if ($data) {
                $githubConnected = true;
                $usedMirror = $result['mirror'];
                $latestCommit = $data['sha'] ?? '';
                $commitMessage = $data['commit']['message'] ?? '无描述';
                $commitDate = $data['commit']['committer']['date'] ?? '';
            }
        }

        $latestVersion = $this->fetchLatestVersionFromGitHub();
        if (!$latestVersion && $githubConnected) {
            $latestVersion = $this->parseVersionFromMessage($commitMessage);
        }
        if (!$latestVersion && $latestCommit) {
            $latestVersion = substr($latestCommit, 0, 7);
        }

        $hasUpdate = false;
        $commitDiffers = false;
        if (!empty($currentCommit) && $latestCommit && strpos($latestCommit, $currentCommit) !== 0) {
            $hasUpdate = true;
            $commitDiffers = true;
        }
        if (empty($currentCommit) && $latestVersion) {
            $hasUpdate = true;
        }

        $versionCompare = $this->compareVersions($currentVersion, $latestVersion);
        if ($versionCompare !== null) {
            if ($versionCompare < 0) {
                $hasUpdate = true;
            } elseif ($versionCompare > 0) {
                $hasUpdate = false;
            } elseif ($commitDiffers) {
                $hasUpdate = true;
            }
        }

        $changelog = [];
        if ($githubConnected) {
            $changelog = $this->getRecentCommits($currentCommit);
        }

        return [
            'success' => true,
            'current_version' => $currentVersion,
            'current_commit' => $currentCommit,
            'latest_version' => $latestVersion ?: '未知',
            'latest_commit' => $latestCommit,
            'latest_message' => $commitMessage,
            'latest_date' => $commitDate,
            'has_update' => $hasUpdate,
            'github_connected' => $githubConnected,
            'used_mirror' => $githubConnected ? ($usedMirror ?? '') : '',
            'repo_url' => 'https://github.com/' . $this->githubRepo,
            'changelog' => $changelog
        ];
    }

    private function getRecentCommits($sinceCommit = '')
    {
        $changelog = [];
        $apiPath = '/repos/' . $this->githubRepo . '/commits?sha=' . $this->githubBranch . '&per_page=20';
        $result = $this->githubCurlGet($apiPath, 10);

        if (!$result['success'] || !$result['data']) {
            return $changelog;
        }

        $commits = json_decode($result['data'], true);
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

        $downloadPath = '/' . $this->githubRepo . '/archive/refs/heads/' . $this->githubBranch . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'update_');

        $downloadResult = $this->githubDownloadFile($downloadPath, 60);

        if (!$downloadResult['success'] || !$downloadResult['data']) {
            return [
                'success' => false,
                'message' => '下载更新包失败: ' . $downloadResult['error']
            ];
        }

        file_put_contents($tempFile, $downloadResult['data']);

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

        $latestCommit = $checkResult['latest_commit'] ?? '';
        $latestVersion = $checkResult['latest_version'] ?? '';
        if (!$latestVersion || !preg_match('/^v?\d+\.\d+\.\d+$/', $latestVersion)) {
            $latestVersion = $this->fetchLatestVersionFromGitHub();
        }
        if (!$latestVersion) {
            $latestMessage = $checkResult['latest_message'] ?? '';
            $latestVersion = $this->parseVersionFromMessage($latestMessage);
        }
        if (!$latestVersion) {
            $latestVersion = substr($latestCommit, 0, 7);
        }
        $this->writeVersionFile($latestVersion, $latestCommit);

        $this->clearAllCaches();

        unlink($tempFile);
        $this->rrmdir($extractDir);

        $integrityCheck = $this->checkIntegrity();

        return [
            'success' => true,
            'message' => '更新成功',
            'backup_file' => $backupResult['filename'],
            'new_version' => $latestVersion,
            'new_commit' => $latestCommit,
            'cleaned_files' => $cleanedFiles,
            'integrity_check' => $integrityCheck,
            'download_mirror' => $downloadResult['mirror'] ?? ''
        ];
    }

    private function cleanOrphanedFiles($sourceDir)
    {
        $cleanedFiles = [];
        $skippedFiles = [];
        $excludeDirs = ['backups', '.git'];
        $excludeFiles = [
            'sq.php',
            'auth_config.php',
            'fix_update.php',
            'db_config.php',
            'proxy_config.php',
            'player_config.php',
            'official_replace_config.php',
            'official_sites_config.php',
        ];
        $protectedPatterns = [
            '/^gz\/rules_.*\.php$/',
            '/^db\/db_config\.php$/',
            '/^proxy\/proxy_config\.php$/',
            '/^gz\/player_config\.php$/',
            '/^gz\/official_replace_config\.php$/',
            '/^gz\/official_sites_config\.php$/',
        ];
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
        $excludeFiles = [
            'sq.php',
            'auth_config.php',
            'db_config.php',
            'proxy_config.php',
            'player_config.php',
            'official_replace_config.php',
            'official_sites_config.php',
        ];
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

        $cacheDir = $this->rootDir . '/cache';
        if (is_dir($cacheDir)) {
            $clearedCount = 0;
            $clearedSize = 0;

            $patterns = [
                'm3u8_*.cache',
                'ad_*.cache',
                'analysis_*.cache',
                'official_replace_*.cache',
            ];

            foreach ($patterns as $pattern) {
                $files = glob($cacheDir . '/' . $pattern);
                if ($files) {
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            $clearedSize += filesize($file);
                            if (@unlink($file)) {
                                $clearedCount++;
                            }
                        }
                    }
                }
            }

            $results['file_cache_cleared'] = $clearedCount . ' files';
            $results['file_cache_size'] = round($clearedSize / 1024, 2) . ' KB';
        }

        $results['official_replace_cache'] = $this->clearOfficialReplaceCache();
        $results['analysis_cache'] = $this->clearAnalysisCache();

        $this->logCacheClear($results);

        return $results;
    }

    private function clearOfficialReplaceCache()
    {
        $count = 0;
        $size = 0;

        try {
            $dbConfigFile = $this->rootDir . '/db/db_config.php';
            if (file_exists($dbConfigFile)) {
                require_once $this->rootDir . '/db/autoload.php';
                $db = Database::getInstance();
                if ($db && $db->tableExists('official_replace_cache')) {
                    $stmt = $db->query('SELECT COUNT(*) as cnt FROM official_replace_cache');
                    $count = $stmt ? intval(($stmt[0]['cnt'] ?? 0)) : 0;
                    $db->query('DELETE FROM official_replace_cache');
                }
            }
        } catch (Throwable $e) {
        }

        return [
            'cleared' => $count,
            'size' => $size
        ];
    }

    private function clearAnalysisCache()
    {
        $count = 0;

        try {
            $dbConfigFile = $this->rootDir . '/db/db_config.php';
            if (file_exists($dbConfigFile)) {
                require_once $this->rootDir . '/db/autoload.php';
                $db = Database::getInstance();
                if ($db && $db->tableExists('m3u8_analysis_cache')) {
                    $stmt = $db->query('SELECT COUNT(*) as cnt FROM m3u8_analysis_cache');
                    $count = $stmt ? intval(($stmt[0]['cnt'] ?? 0)) : 0;
                    $db->query('DELETE FROM m3u8_analysis_cache');
                }
            }
        } catch (Throwable $e) {
        }

        return [
            'cleared' => $count
        ];
    }

    private function logCacheClear($results)
    {
        try {
            $logDir = $this->rootDir . '/cache/logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/cache_clear_' . date('Y-m-d') . '.log';
            $logLine = sprintf(
                "[%s] 缓存清理完成 | 文件缓存: %s | 官替缓存: %d 条 | 分析缓存: %d 条\n",
                date('Y-m-d H:i:s'),
                $results['file_cache_cleared'] ?? '0 files',
                $results['official_replace_cache']['cleared'] ?? 0,
                $results['analysis_cache']['cleared'] ?? 0
            );
            @file_put_contents($logFile, $logLine, FILE_APPEND);
        } catch (Throwable $e) {
        }
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
