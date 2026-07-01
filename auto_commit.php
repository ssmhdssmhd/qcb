<?php
function autoCommit($commitMsg = null) {
    $rootDir = __DIR__;
    chdir($rootDir);

    if ($commitMsg === null) {
        $commitMsg = 'auto: 自动提交更新';
    }

    exec('git add -A 2>&1', $addOutput, $addCode);
    if ($addCode !== 0) {
        return [
            'success' => false,
            'message' => 'Git add 失败',
            'output' => implode("\n", $addOutput)
        ];
    }

    exec('git diff --cached --quiet 2>&1', $diffOutput, $diffCode);
    if ($diffCode === 0) {
        return [
            'success' => true,
            'message' => '没有需要提交的更改',
            'has_changes' => false
        ];
    }

    exec('git diff --cached --name-only 2>&1', $filesOutput, $filesCode);
    $changedFiles = array_filter($filesOutput);
    $fileCount = count($changedFiles);

    $escapedMsg = escapeshellarg($commitMsg);
    exec("git commit -m $escapedMsg 2>&1", $commitOutput, $commitCode);
    if ($commitCode !== 0) {
        return [
            'success' => false,
            'message' => '提交失败',
            'output' => implode("\n", $commitOutput),
            'changed_files' => $changedFiles,
            'file_count' => $fileCount
        ];
    }

    $currentBranch = trim(exec('git branch --show-current 2>&1'));

    exec('git remote 2>&1', $remotes, $remoteCode);
    if (in_array('origin', $remotes)) {
        exec("git push origin $currentBranch 2>&1", $pushOutput, $pushCode);
        if ($pushCode === 0) {
            $remoteUrl = trim(exec('git remote get-url origin 2>&1'));
            return [
                'success' => true,
                'message' => '提交并推送成功',
                'has_changes' => true,
                'changed_files' => $changedFiles,
                'file_count' => $fileCount,
                'branch' => $currentBranch,
                'remote_url' => $remoteUrl,
                'pushed' => true
            ];
        } else {
            return [
                'success' => false,
                'message' => '本地提交成功，但推送失败',
                'output' => implode("\n", $pushOutput),
                'changed_files' => $changedFiles,
                'file_count' => $fileCount,
                'branch' => $currentBranch,
                'pushed' => false
            ];
        }
    } else {
        return [
            'success' => true,
            'message' => '本地提交成功（未配置远程仓库）',
            'has_changes' => true,
            'changed_files' => $changedFiles,
            'file_count' => $fileCount,
            'branch' => $currentBranch,
            'pushed' => false
        ];
    }
}

if (php_sapi_name() === 'cli') {
    $commitMsg = $argv[1] ?? null;
    $result = autoCommit($commitMsg);
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
    
    exit($result['success'] ? 0 : 1);
}
