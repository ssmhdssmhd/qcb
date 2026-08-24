<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/ResourceSiteManager.php';
require_once __DIR__ . '/src/UpdateManager.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'status';

$siteManager = new ResourceSiteManager();
$updateManager = new UpdateManager();

try {
    switch ($action) {
        case 'status':
            echo json_encode([
                'success' => true,
                'data' => [
                    'app_name' => APP_NAME,
                    'version' => APP_VERSION,
                    'repo' => DEFAULT_GITHUB_REPO,
                    'branch' => DEFAULT_GITHUB_BRANCH,
                    'time' => date('Y-m-d H:i:s'),
                    'timestamp' => time()
                ]
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'sites':
            $sites = $siteManager->getAllSites();
            echo json_encode([
                'success' => true,
                'data' => $sites,
                'total' => count($sites),
                'enabled' => count(array_filter($sites, fn($s) => $s['enabled']))
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'enabled_sites':
            $sites = $siteManager->getEnabledSites();
            echo json_encode([
                'success' => true,
                'data' => array_values($sites),
                'total' => count($sites)
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'site':
            $id = intval($_GET['id'] ?? 0);
            $site = $siteManager->getSiteById($id);
            if ($site) {
                echo json_encode(['success' => true, 'data' => $site], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '站点不存在'], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'speed_test':
            $results = $siteManager->testAllSites();
            $best = $siteManager->getBestSite($results);
            echo json_encode([
                'success' => true,
                'best_site' => $best,
                'results' => $results,
                'tested_at' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'test_site':
            $id = intval($_GET['id'] ?? 0);
            $site = $siteManager->getSiteById($id);
            if ($site) {
                $result = $siteManager->testSite($site);
                echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '站点不存在'], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'check_update':
            $result = $updateManager->checkUpdate();
            echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
            break;

        case 'fetch_file':
            $path = $_GET['path'] ?? $_POST['path'] ?? 'config.php';
            $result = $updateManager->fetchWithFallback($path);
            echo json_encode([
                'success' => $result['success'],
                'data' => $result['success'] ? [
                    'path' => $path,
                    'content' => $result['data'],
                    'site_used' => $result['site_name'] ?? null,
                    'response_time_ms' => $result['response_time_ms'] ?? null,
                    'http_code' => $result['http_code'] ?? null
                ] : null,
                'error' => $result['error'] ?? null
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'fetch_config':
            $result = $updateManager->getConfigData();
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            break;

        case 'update_status':
            $status = $updateManager->getUpdateStatus();
            echo json_encode(['success' => true, 'data' => $status], JSON_UNESCAPED_UNICODE);
            break;

        case 'repo_info':
            echo json_encode([
                'success' => true,
                'data' => $updateManager->getRepoInfo()
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'build_url':
            $id = intval($_GET['id'] ?? 0);
            $path = $_GET['path'] ?? '';
            $site = $siteManager->getSiteById($id);
            if ($site) {
                $repoInfo = $updateManager->getRepoInfo();
                $url = $siteManager->buildUrl($site, $repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch'], $path);
                echo json_encode(['success' => true, 'url' => $url, 'site' => $site['name']], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '站点不存在'], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'multi_fetch':
            $paths = $_GET['paths'] ?? $_POST['paths'] ?? 'config.php,version.php';
            $pathList = array_filter(array_map('trim', explode(',', $paths)));
            $results = [];
            foreach ($pathList as $path) {
                $result = $updateManager->fetchWithFallback($path);
                $results[] = [
                    'path' => $path,
                    'success' => $result['success'],
                    'site_used' => $result['site_name'] ?? null,
                    'response_time_ms' => $result['response_time_ms'] ?? null,
                    'content' => $result['success'] ? $result['data'] : null,
                    'error' => $result['error'] ?? null
                ];
            }
            echo json_encode([
                'success' => true,
                'data' => $results,
                'total' => count($results),
                'success_count' => count(array_filter($results, fn($r) => $r['success']))
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'health':
            echo json_encode([
                'status' => 'healthy',
                'app' => APP_NAME,
                'version' => APP_VERSION,
                'time' => date('c'),
                'site_manager' => class_exists('ResourceSiteManager'),
                'update_manager' => class_exists('UpdateManager'),
                'cache_writable' => is_writable(DATA_DIR)
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode([
                'success' => false,
                'error' => '未知操作',
                'available_actions' => [
                    'status', 'sites', 'enabled_sites', 'site', 'speed_test',
                    'test_site', 'check_update', 'fetch_file', 'fetch_config',
                    'update_status', 'repo_info', 'build_url', 'multi_fetch', 'health'
                ]
            ], JSON_UNESCAPED_UNICODE);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE);
}
