<?php

class UpdateManager {

    private $versionFile;
    private $backupDir;
    private $rootDir;
    private $versionConfig;
    private $githubRepo;
    private $githubBranch;
    private $githubToken = null;

    public function __construct() {
        $this->rootDir = realpath(__DIR__ . '/..');
        $this->versionFile = $this->rootDir . '/version.php';
        $this->backupDir = $this->rootDir . '/backups';
        $this->versionConfig = require $this->versionFile;
        $this->githubRepo = $this->versionConfig['github_repo'] ?? 'ssmhdssmhd/qcb';
        $this->githubBranch = $this->versionConfig['github_branch'] ?? 'main';
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    public function getCurrentVersion() {
        return $this->versionConfig;
    }

    public function getRemoteVersion() {
        $url = 'https://api.github.com/repos/' . $this->githubRepo . '/commits/' . $this->githubBranch;
        $response = $this->githubRequest($url);
        if (!$response) return false;
        $commit = json_decode($response, true);
        if (!$commit || !isset($commit['sha'])) return false;
        return [
            'sha' => $commit['sha'],
            'short_sha' => substr($commit['sha'], 0, 7),
            'date' => $commit['commit']['committer']['date'] ?? '',
            'message' => $commit['commit']['message'] ?? '',
            'author' => $commit['commit']['author']['name'] ?? ''
        ];
    }

    public function checkUpdate() {
        $current = $this->getCurrentVersion();
        $remote = $this->getRemoteVersion();
        if (!$remote) {
            return ['has_update' => false, 'error' => '无法获取远程版本信息'];
        }
        $localSha = $this->getLocalSha();
        $hasUpdate = ($localSha !== $remote['sha']);
        return [
            'has_update' => $hasUpdate,
            'current' => $current,
            'remote' => $remote,
            'local_sha' => $localSha
        ];
    }

    public function getLocalSha() {
        static $sha = null;
        if ($sha !== null) return $sha;
        $gitDir = $this->rootDir . '/.git';
        if (is_dir($gitDir)) {
            $headFile = $gitDir . '/refs/heads/' . $this->githubBranch;
            if (file_exists($headFile)) {
                $sha = trim(file_get_contents($headFile));
                return $sha;
            }
            $head = trim(file_get_contents($gitDir . '/HEAD'));
            if (strpos($head, 'ref:') === 0) {
                $ref = trim(substr($head, 4));
                $refFile = $gitDir . '/' . $ref;
                if (file_exists($refFile)) {
                    $sha = trim(file_get_contents($refFile));
                    return $sha;
                }
            }
        }
        $shaFile = $this->rootDir . '/.version_sha';
        if (file_exists($shaFile)) {
            $sha = trim(file_get_contents($shaFile));
            return $sha;
        }
        return null;
    }

    public function createBackup() {
        $version = $this->getCurrentVersion();
        $timestamp = date('Ymd_His');
        $sha = $this->getLocalSha() ?: 'unknown';
        $shortSha = substr($sha, 0, 7);
        $backupFile = $this->backupDir . '/backup_' . $version['version'] . '_' . $shortSha . '_' . $timestamp . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['success' => false, 'error' => '无法创建压缩包'];
        }
        $exclude = ['.git', 'backups', '.gitkeep', '.DS_Store'];
        $files = $this->scanDir($this->rootDir, $exclude);
        $totalFiles = 0;
        foreach ($files as $file) {
            $relativePath = substr($file, strlen($this->rootDir) + 1);
            $zip->addFile($file, $relativePath);
            $totalFiles++;
        }
        $zip->close();
        $size = filesize($backupFile);
        return [
            'success' => true,
            'file' => $backupFile,
            'filename' => basename($backupFile),
            'size' => $size,
            'size_formatted' => $this->formatSize($size),
            'file_count' => $totalFiles
        ];
    }

    private function scanDir($dir, $exclude = []) {
        $files = [];
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            if (in_array($item, $exclude)) continue;
            $fullPath = $dir . '/' . $item;
            if (is_dir($fullPath)) {
                $subFiles = $this->scanDir($fullPath, $exclude);
                $files = array_merge($files, $subFiles);
            } else {
                $files[] = $fullPath;
            }
        }
        return $files;
    }

    public function downloadUpdate() {
        $remote = $this->getRemoteVersion();
        if (!$remote) return ['success' => false, 'error' => '无法获取远程版本'];
        $downloadUrl = 'https://api.github.com/repos/' . $this->githubRepo . '/zipball/' . $this->githubBranch;
        $tmpFile = $this->backupDir . '/update_' . $remote['short_sha'] . '_' . time() . '.zip';
        $response = $this->githubRequest($downloadUrl, $tmpFile);
        if (!$response) {
            @unlink($tmpFile);
            return ['success' => false, 'error' => '下载更新包失败'];
        }
        $size = filesize($tmpFile);
        if ($size < 1000) {
            @unlink($tmpFile);
            return ['success' => false, 'error' => '更新包过小，下载可能失败'];
        }
        return [
            'success' => true,
            'file' => $tmpFile,
            'filename' => basename($tmpFile),
            'size' => $size,
            'size_formatted' => $this->formatSize($size),
            'sha' => $remote['sha'],
            'short_sha' => $remote['short_sha']
        ];
    }

    public function applyUpdate($zipFile) {
        if (!file_exists($zipFile)) {
            return ['success' => false, 'error' => '更新包不存在'];
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            return ['success' => false, 'error' => '无法打开更新包'];
        }
        $tmpExtractDir = $this->backupDir . '/update_tmp_' . time();
        mkdir($tmpExtractDir, 0755, true);
        $zip->extractTo($tmpExtractDir);
        $zip->close();
        $dirs = scandir($tmpExtractDir);
        $sourceDir = null;
        foreach ($dirs as $d) {
            if ($d !== '.' && $d !== '..' && is_dir($tmpExtractDir . '/' . $d)) {
                $sourceDir = $tmpExtractDir . '/' . $d;
                break;
            }
        }
        if (!$sourceDir) {
            $this->rmdirRecursive($tmpExtractDir);
            return ['success' => false, 'error' => '更新包格式错误'];
        }
        $backupResult = $this->createBackup();
        $updatedCount = 0;
        $items = $this->scanDir($sourceDir, ['backups', '.git']);
        foreach ($items as $srcFile) {
            $relativePath = substr($srcFile, strlen($sourceDir) + 1);
            if (strpos($relativePath, 'backups/') === 0) continue;
            if (strpos($relativePath, '.git/') === 0) continue;
            if ($relativePath === '.git') continue;
            $destFile = $this->rootDir . '/' . $relativePath;
            $destDir = dirname($destFile);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            copy($srcFile, $destFile);
            $updatedCount++;
        }
        $remote = $this->getRemoteVersion();
        if ($remote) {
            file_put_contents($this->rootDir . '/.version_sha', $remote['sha']);
        }
        $this->rmdirRecursive($tmpExtractDir);
        @unlink($zipFile);
        return [
            'success' => true,
            'updated_count' => $updatedCount,
            'backup' => $backupResult,
            'new_version' => $remote ? $remote['short_sha'] : 'unknown'
        ];
    }

    public function doUpdate() {
        $check = $this->checkUpdate();
        if (!$check['has_update']) {
            return ['success' => false, 'error' => '当前已是最新版本，无需更新'];
        }
        $download = $this->downloadUpdate();
        if (!$download['success']) {
            return $download;
        }
        return $this->applyUpdate($download['file']);
    }

    public function getBackupList() {
        $files = glob($this->backupDir . '/*.zip');
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'size_formatted' => $this->formatSize(filesize($file)),
                'created' => filemtime($file),
                'created_formatted' => date('Y-m-d H:i:s', filemtime($file))
            ];
        }
        usort($backups, function($a, $b) {
            return $b['created'] - $a['created'];
        });
        return $backups;
    }

    public function deleteBackup($filename) {
        $file = $this->backupDir . '/' . basename($filename);
        if (file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) === 'zip') {
            return unlink($file);
        }
        return false;
    }

    public function restoreBackup($filename) {
        $file = $this->backupDir . '/' . basename($filename);
        if (!file_exists($file)) {
            return ['success' => false, 'error' => '备份文件不存在'];
        }
        $zip = new ZipArchive();
        if ($zip->open($file) !== true) {
            return ['success' => false, 'error' => '无法打开备份文件'];
        }
        $tmpDir = $this->backupDir . '/restore_tmp_' . time();
        mkdir($tmpDir, 0755, true);
        $zip->extractTo($tmpDir);
        $zip->close();
        $items = $this->scanDir($tmpDir, ['backups', '.git']);
        $restoredCount = 0;
        foreach ($items as $srcFile) {
            $relativePath = substr($srcFile, strlen($tmpDir) + 1);
            if (strpos($relativePath, 'backups/') === 0) continue;
            if (strpos($relativePath, '.git/') === 0) continue;
            $destFile = $this->rootDir . '/' . $relativePath;
            $destDir = dirname($destFile);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }
            copy($srcFile, $destFile);
            $restoredCount++;
        }
        $this->rmdirRecursive($tmpDir);
        return [
            'success' => true,
            'restored_count' => $restoredCount,
            'backup_file' => $filename
        ];
    }

    private function rmdirRecursive($dir) {
        if (!is_dir($dir)) return;
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->rmdirRecursive($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    private function formatSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < 3) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    private function githubRequest($url, $outputFile = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'M3U8-AdSkipper-Update');
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($this->githubToken) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: token ' . $this->githubToken
            ]);
        }
        if ($outputFile) {
            $fp = fopen($outputFile, 'w');
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($outputFile) {
            fclose($fp);
            return $httpCode === 200;
        }
        if ($httpCode !== 200) return false;
        return $result;
    }
}
