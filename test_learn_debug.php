<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/gz/DomainRuleManager.php';
require_once __DIR__ . '/gz/EnhancedAdRuleEngine.php';
require_once __DIR__ . '/src/M3U8Parser.php';
require_once __DIR__ . '/src/AdRuleEngine.php';

$manager = new DomainRuleManager();

$analysisResult = [
    'totalCount' => 100,
    'adCount' => 30,
    'contentCount' => 70,
    'totalDuration' => 500,
    'adDuration' => 150,
    'contentDuration' => 350,
    'discontinuityCount' => 10,
    'sequenceJumps' => [['jump' => 50000, 'direction' => 'forward']],
    'adClusters' => [['start' => 0, 'end' => 10]],
    'cueMarkerCount' => 2,
    'scte35Count' => 1,
    'adTagCount' => 3,
    'confidence' => 75,
    'adPercentage' => 30,
    'insertionPoints' => [
        'pre_roll' => ['found' => true, 'duration' => 15, 'segment_count' => 5],
        'mid_roll' => ['found' => true, 'count' => 2, 'points' => [['duration' => 30], ['duration' => 25]]],
        'post_roll' => ['found' => true, 'duration' => 10, 'segment_count' => 3]
    ],
    'adTypes' => [
        'pre_roll_ad' => ['count' => 1, 'duration' => 15],
        'mid_roll_ad' => ['count' => 2, 'duration' => 55],
        'post_roll_ad' => ['count' => 1, 'duration' => 10]
    ],
    'psychologicalFeatures' => [
        'ad_density' => 0.3,
        'attention_grab_score' => 70,
        'frequency_score' => 50,
        'watchability_score' => 80,
        'interruption_pattern' => 'frequent',
        'user_experience_impact' => 'high'
    ]
];

echo "测试1: 新域名学习...\n";
$testDomain = 'testdebug' . time() . '.com';
try {
    $result = $manager->learnFromAnalysis($testDomain, $analysisResult);
    echo "成功: " . json_encode($result) . "\n";
} catch (Throwable $e) {
    echo "错误: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . " 行: " . $e->getLine() . "\n";
}

echo "\n测试2: 损坏的规则文件学习...\n";
$testDomain2 = 'corruptdebug' . time() . '.com';
$corruptRules = [
    'domain' => $testDomain2,
    'duration_rules' => 'corrupted',
    'discontinuity_rules' => 123,
    'sequence_jump_rules' => null,
    'filename_patterns' => 'not_array',
    'insertion_patterns' => 'bad',
    'ad_type_stats' => 'bad',
    'psychological_profile' => 'bad',
    'history_stats' => 'bad',
    'marker_stats' => 'bad',
    'learn_count' => 2,
    'ad_threshold' => 'fifty',
    'confidence_score' => 'high'
];
$ruleFile = __DIR__ . '/gz/rules_' . str_replace(['.', '-'], '_', $testDomain2) . '.php';
file_put_contents($ruleFile, '<?php return ' . var_export($corruptRules, true) . ';');

try {
    $result2 = $manager->learnFromAnalysis($testDomain2, $analysisResult);
    echo "成功: " . json_encode($result2) . "\n";
} catch (Throwable $e) {
    echo "错误: " . $e->getMessage() . "\n";
    echo "文件: " . $e->getFile() . " 行: " . $e->getLine() . "\n";
    echo "追踪:\n" . $e->getTraceAsString() . "\n";
}

// 清理
$manager->deleteRules($testDomain);
$manager->deleteRules($testDomain2);
echo "\n测试完成\n";
