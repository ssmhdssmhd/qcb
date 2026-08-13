<?php
/**
 * mini runner: 直接调 OfficialReplaceManager->resolve()，拿 step trace 和 error
 */
declare(strict_types=1);

require_once __DIR__ . '/gz/OfficialReplaceManager.php';
require_once __DIR__ . '/xt/AdFilter.php';
require_once __DIR__ . '/gz/Md5AdPlaceholderEngine.php';
require_once __DIR__ . '/gz/PlaceholderTsGenerator.php';
require_once __DIR__ . '/xt/config.php';

$url = $argv[1] ?? 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';

try {
    $mgr = new \OfficialReplaceManager();
    $t = microtime(true);
    $result = $mgr->resolve($url);
    $elapsed = round((microtime(true) - $t) * 1000, 1);
    echo "======== resolve() =========\n";
    echo "elapsed_ms=$elapsed\n";
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
} catch (Throwable $e) {
    echo "EXCEPTION: ", $e->getMessage(), "\n";
    echo $e->getTraceAsString(), "\n";
    exit(1);
}
