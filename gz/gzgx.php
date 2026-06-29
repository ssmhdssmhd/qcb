<?php
@ini_set('display_errors', 0);
@ini_set('html_errors', 0);
error_reporting(0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/DomainRuleManager.php';
require_once __DIR__ . '/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/../src/M3U8Parser.php';
require_once __DIR__ . '/../src/M3U8AdSkipper.php';
require_once __DIR__ . '/../src/CacheManager.php';

$ruleManager = new DomainRuleManager();

function sendJson($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'info';
$domain = $_GET['domain'] ?? $_POST['domain'] ?? '';

switch ($action) {
    case 'info':
        $allRules = $ruleManager->getAllRules();
        $ruleList = [];
        foreach ($allRules as $d => $r) {
            $ruleList[] = [
                'domain' => $d,
                'name' => $r['name'] ?? '',
                'learn_count' => $r['learn_count'] ?? 0,
                'last_learn_date' => $r['last_learn_date'] ?? ($r['analysis_date'] ?? ''),
                'ad_threshold' => $r['ad_threshold'] ?? 50,
                'duration_rules_count' => count($r['duration_rules'] ?? []),
                'discontinuity_enabled' => !empty($r['discontinuity_rules'][0]['enabled']),
                'filename_patterns_count' => count($r['filename_patterns'] ?? [])
            ];
        }
        sendJson([
            'success' => true,
            'message' => '规则更新接口运行正常',
            'version' => '1.0',
            'total_rules' => count($ruleList),
            'rules' => $ruleList
        ]);
        break;

    case 'get':
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $rules = $ruleManager->getRules($domain);
        if ($rules === null) {
            sendJson(['success' => false, 'message' => '该域名规则不存在'], 404);
        }
        unset($rules['_filename'], $rules['_filemtime']);
        sendJson([
            'success' => true,
            'domain' => $domain,
            'rules' => $rules
        ]);
        break;

    case 'update':
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $ruleData = $input['rules'] ?? ($_POST['rules'] ?? null);
        if ($ruleData === null) {
            sendJson(['success' => false, 'message' => '缺少 rules 数据'], 400);
        }
        if (is_string($ruleData)) {
            $ruleData = json_decode($ruleData, true);
        }
        $ruleData['domain'] = $domain;
        $result = $ruleManager->saveRules($domain, $ruleData);
        if ($result) {
            sendJson([
                'success' => true,
                'message' => '规则更新成功',
                'domain' => $domain
            ]);
        } else {
            sendJson(['success' => false, 'message' => '规则更新失败'], 500);
        }
        break;

    case 'learn':
        $url = $_GET['url'] ?? $_POST['url'] ?? '';
        if (empty($url)) {
            sendJson(['success' => false, 'message' => '缺少 url 参数'], 400);
        }
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '无法解析域名'], 400);
        }

        $mediaUrl = $url;
        $parser = new M3U8Parser();
        try {
            $playlist = $parser->parse($mediaUrl);
            if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
                $firstVariant = $playlist['variants'][0]['uri'] ?? '';
                if ($firstVariant) {
                    $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
                    if (isset($parsedUrl['port'])) {
                        $baseUrl .= ':' . $parsedUrl['port'];
                    }
                    $pathDir = dirname($parsedUrl['path'] ?? '');
                    $pathDir = $pathDir === '.' ? '' : $pathDir;
                    if (strpos($firstVariant, '/') === 0) {
                        $mediaUrl = $baseUrl . $firstVariant;
                    } else {
                        $mediaUrl = $baseUrl . $pathDir . '/' . $firstVariant;
                    }
                    $playlist = $parser->parse($mediaUrl);
                }
            }
        } catch (Exception $e) {
            sendJson(['success' => false, 'message' => '解析视频失败: ' . $e->getMessage()], 500);
        }

        if (empty($playlist['segments'])) {
            sendJson(['success' => false, 'message' => '无法解析视频片段'], 400);
        }

        $engine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $engine->setDomain($domain);
        $analysis = $engine->analyzeAllSegments($playlist['segments']);

        $result = $ruleManager->learnFromAnalysis($domain, $analysis);
        $rules = $ruleManager->getRules($domain);

        if ($result) {
            sendJson([
                'success' => true,
                'message' => '规则学习完成',
                'domain' => $domain,
                'learn_count' => $rules['learn_count'] ?? 1,
                'stats' => [
                    'totalSegments' => $analysis['totalCount'],
                    'adSegments' => $analysis['adCount'],
                    'discontinuityCount' => $analysis['discontinuityCount'],
                    'sequenceJumps' => count($analysis['sequenceJumps']),
                    'adClusters' => count($analysis['adClusters'])
                ]
            ]);
        } else {
            sendJson(['success' => false, 'message' => '规则学习失败'], 500);
        }
        break;

    case 'export':
        $exportData = $ruleManager->exportRules($domain ?: null);
        if ($exportData === null) {
            sendJson(['success' => false, 'message' => '规则不存在'], 404);
        }
        if (!empty($_GET['download'])) {
            $filename = $domain ? "rules_{$domain}.json" : 'all_rules.json';
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit;
        }
        sendJson($exportData);
        break;

    case 'import':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input) && !empty($_FILES['file'])) {
            $fileContent = file_get_contents($_FILES['file']['tmp_name']);
            $input = json_decode($fileContent, true);
        }
        if (empty($input)) {
            sendJson(['success' => false, 'message' => '缺少导入数据'], 400);
        }
        $result = $ruleManager->importRules($input);
        sendJson($result, $result['success'] ? 200 : 400);
        break;

    case 'delete':
        if (empty($domain)) {
            sendJson(['success' => false, 'message' => '缺少 domain 参数'], 400);
        }
        $result = $ruleManager->deleteRules($domain);
        if ($result) {
            sendJson(['success' => true, 'message' => '规则删除成功']);
        } else {
            sendJson(['success' => false, 'message' => '规则删除失败或不存在'], 404);
        }
        break;

    default:
        sendJson(['success' => false, 'message' => '未知操作'], 400);
        break;
}
