<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/AccelerationNodeManager.php';
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

$nodeManager = new AccelerationNodeManager();
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

        case 'nodes':
        case 'sites':
            $nodes = $nodeManager->getAllNodes();
            echo json_encode([
                'success' => true,
                'data' => $nodes,
                'total' => count($nodes),
                'enabled' => count(array_filter($nodes, fn($n) => $n['enabled']))
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'enabled_nodes':
        case 'enabled_sites':
            $nodes = $nodeManager->getEnabledNodes();
            echo json_encode([
                'success' => true,
                'data' => array_values($nodes),
                'total' => count($nodes)
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'node':
        case 'site':
            $id = intval($_GET['id'] ?? 0);
            $node = $nodeManager->getNodeById($id);
            if ($node) {
                echo json_encode(['success' => true, 'data' => $node], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '节点不存在'], JSON_UNESCAPED_UNICODE);
            }
            break;

        case 'speed_test':
            $result = $nodeManager->testAllNodes();
            $best = $nodeManager->getBestNode($result);
            echo json_encode([
                'success' => true,
                'best_node' => $best,
                'results' => $result,
                'tested_at' => date('Y-m-d H:i:s')
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'test_node':
        case 'test_site':
            $id = intval($_GET['id'] ?? 0);
            $node = $nodeManager->getNodeById($id);
            if ($node) {
                $result = $nodeManager->testNode($node);
                echo json_encode(['success' => true, 'data' => $result], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '节点不存在'], JSON_UNESCAPED_UNICODE);
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
                    'node_used' => $result['node_name'] ?? null,
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
            $node = $nodeManager->getNodeById($id);
            if ($node) {
                $repoInfo = $updateManager->getRepoInfo();
                $url = $nodeManager->buildUrl($node, $repoInfo['owner'], $repoInfo['repo'], $repoInfo['branch'], $path);
                echo json_encode(['success' => true, 'url' => $url, 'node' => $node['name']], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'error' => '节点不存在'], JSON_UNESCAPED_UNICODE);
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
                    'node_used' => $result['node_name'] ?? null,
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
                'node_manager' => class_exists('AccelerationNodeManager'),
                'update_manager' => class_exists('UpdateManager'),
                'cache_writable' => is_writable(DATA_DIR)
            ], JSON_UNESCAPED_UNICODE);
            break;

        default:
            echo json_encode([
                'success' => false,
                'error' => '未知操作',
                'available_actions' => [
                    'status', 'nodes', 'enabled_nodes', 'node', 'speed_test',
                    'test_node', 'check_update', 'fetch_file', 'fetch_config',
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
