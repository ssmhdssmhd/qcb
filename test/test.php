<?php

require_once __DIR__ . '/../src/M3U8AdSkipper.php';

$passed = 0;
$failed = 0;

function test($name, $fn) {
    global $passed, $failed;
    try {
        $fn();
        echo "  ✅ $name\n";
        $passed++;
    } catch (Exception $e) {
        echo "  ❌ $name\n";
        echo "     Error: " . $e->getMessage() . "\n";
        $failed++;
    }
}

function assertTrue($condition, $message = '') {
    if (!$condition) {
        throw new Exception($message ?: 'Assertion failed');
    }
}

function assertEquals($actual, $expected, $message = '') {
    if ($actual !== $expected) {
        throw new Exception($message ?: "Expected " . var_export($expected, true) . ", got " . var_export($actual, true));
    }
}

echo "\n========================================\n";
echo "  m3u8-ad-skipper (PHP) 测试套件\n";
echo "========================================\n\n";

echo "1. M3U8 解析器测试\n";
echo "-------------------\n";

test('解析带广告的播放列表', function() {
    $parser = new M3U8Parser();
    $content = file_get_contents(__DIR__ . '/sample_with_ads.m3u8');
    $playlist = $parser->parseContent($content);

    assertEquals($playlist['version'], 3, '版本号');
    assertTrue($playlist['targetDuration'] > 0, '目标时长');
    assertTrue(count($playlist['segments']) > 0, '片段数量大于0');
    assertTrue($playlist['endlist'] === true, '结束标记');
});

test('解析纯净播放列表', function() {
    $parser = new M3U8Parser();
    $content = file_get_contents(__DIR__ . '/sample_clean.m3u8');
    $playlist = $parser->parseContent($content);

    assertEquals(count($playlist['segments']), 10, '10个内容片段');
});

test('解析主播放列表', function() {
    $parser = new M3U8Parser();
    $content = file_get_contents(__DIR__ . '/sample_master.m3u8');
    $playlist = $parser->parseContent($content);

    assertTrue($playlist['isMaster'] === true, '是主播放列表');
    assertEquals(count($playlist['variants']), 4, '4个清晰度');
    assertTrue($playlist['variants'][0]['bandwidth'] > 0, '带宽信息');
    assertTrue(!empty($playlist['variants'][0]['resolution']), '分辨率信息');
});

test('解析 EXTINF 时长和标题', function() {
    $parser = new M3U8Parser();
    $content = file_get_contents(__DIR__ . '/sample_with_ads.m3u8');
    $playlist = $parser->parseContent($content);

    $firstSegment = $playlist['segments'][0];
    assertEquals($firstSegment['duration'], 5.0, '时长正确');
    assertTrue(mb_strpos($firstSegment['title'], 'ad') !== false, '标题包含广告关键词');
    assertTrue(!empty($firstSegment['uri']), '有URI');
});

echo "\n2. 广告规则引擎测试\n";
echo "-------------------\n";

test('短时长片段检测', function() {
    $engine = new AdRuleEngine(['minSegmentDuration' => 3]);
    $segment = ['duration' => 2, 'uri' => 'test.ts', 'title' => ''];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($result['isAd'] === true, '短片段被识别为广告');
    $hasShortRule = false;
    foreach ($result['matchedRules'] as $r) {
        if ($r['name'] === 'short-duration') {
            $hasShortRule = true;
            break;
        }
    }
    assertTrue($hasShortRule, '匹配短时长规则');
});

test('长时长片段检测', function() {
    $engine = new AdRuleEngine(['maxSegmentDuration' => 20, 'checkLongSegments' => true]);
    $segment = ['duration' => 25, 'uri' => 'test.ts', 'title' => ''];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($result['isAd'] === true, '长片段被识别为广告');
});

test('关键词匹配检测', function() {
    $engine = new AdRuleEngine(['adKeywords' => ['ad', '广告']]);
    $segment = ['duration' => 10, 'uri' => 'segment_001.ts', 'title' => 'ad_pre_roll'];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($result['isAd'] === true, '关键词匹配成功');
});

test('文件名模式匹配检测', function() {
    $engine = new AdRuleEngine([
        'adFilenamePatterns' => ['/^ad_/i']
    ]);
    $segment = ['duration' => 10, 'uri' => 'ad_001.ts', 'title' => ''];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($result['isAd'] === true, '文件名模式匹配成功');
});

test('正常内容不被误判', function() {
    $engine = new AdRuleEngine([
        'minSegmentDuration' => 2,
        'maxSegmentDuration' => 30,
        'adKeywords' => ['ad']
    ]);
    $segment = ['duration' => 8, 'uri' => 'content_001.ts', 'title' => 'main content'];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($result['isAd'] === false, '正常内容不被误判');
});

test('添加自定义规则', function() {
    $engine = new AdRuleEngine();
    $customChecked = false;

    $engine->addRule([
        'name' => 'custom-rule',
        'description' => '自定义规则',
        'check' => function() use (&$customChecked) {
            $customChecked = true;
            return true;
        }
    ]);

    $segment = ['duration' => 5, 'uri' => 'test.ts'];
    $result = $engine->checkSegment($segment, 0, [$segment]);

    assertTrue($customChecked === true, '自定义规则被执行');
    assertTrue($result['isAd'] === true, '自定义规则生效');
});

echo "\n3. 广告过滤测试\n";
echo "---------------\n";

test('过滤带广告的播放列表', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_with_ads.m3u8');

    assertTrue($result['stats']['totalSegments'] > $result['stats']['keptSegments'], '移除了部分片段');
    assertTrue($result['stats']['removedSegments'] > 0, '有广告被移除');
    assertTrue($result['stats']['savedDuration'] > 0, '节省了时长');
});

test('纯净播放列表不被修改', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_clean.m3u8');

    assertEquals($result['stats']['removedSegments'], 0, '没有移除任何片段');
    assertTrue($result['stats']['adPercentage'] == 0, '广告占比为0');
});

test('输出是有效的 M3U8 格式', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_with_ads.m3u8');

    assertTrue(strpos($result['output'], '#EXTM3U') === 0, '以 EXTM3U 开头');
    assertTrue(strpos($result['output'], '#EXTINF:') !== false, '包含 EXTINF 标签');
});

test('主播放列表处理', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_master.m3u8');

    assertTrue($result['filtered']['isMaster'] === true, '仍是主播放列表');
    assertEquals(count($result['filtered']['variants']), 4, '清晰度数量不变');
});

echo "\n4. 统计信息测试\n";
echo "---------------\n";

test('统计信息准确性', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_with_ads.m3u8');
    $stats = $result['stats'];

    assertEquals(
        $stats['totalSegments'],
        $stats['keptSegments'] + $stats['removedSegments'],
        '总数 = 保留 + 移除'
    );
    assertTrue(
        abs($stats['originalDuration'] - $stats['filteredDuration'] - $stats['savedDuration']) < 0.01,
        '原始时长 = 过滤后时长 + 节省时长'
    );
    assertTrue(
        $stats['adPercentage'] >= 0 && $stats['adPercentage'] <= 100,
        '广告占比在0-100之间'
    );
});

echo "\n5. 输出生成器测试\n";
echo "-----------------\n";

test('生成 JSON 格式输出', function() {
    $skipper = new M3U8AdSkipper();
    $result = $skipper->process(__DIR__ . '/sample_with_ads.m3u8');

    $generator = $skipper->getOutputGenerator();
    $jsonOutput = $generator->generateJson($result['filtered']);
    $parsed = json_decode($jsonOutput, true);

    assertTrue(isset($parsed['segments']), '有 segments 字段');
    assertTrue(isset($parsed['removedSegments']), '有 removedSegments 字段');
});

test('输出到文件', function() {
    $parser = new M3U8Parser();
    $content = file_get_contents(__DIR__ . '/sample_clean.m3u8');
    $playlist = $parser->parseContent($content);

    $generator = new OutputGenerator();
    $outputPath = __DIR__ . '/test_output.m3u8';
    $generator->toFile($playlist, $outputPath);

    assertTrue(file_exists($outputPath), '文件已创建');

    $fileContent = file_get_contents($outputPath);
    assertTrue(strpos($fileContent, '#EXTM3U') === 0, '文件内容是有效的 M3U8');

    unlink($outputPath);
});

echo "\n========================================\n";
echo "  测试结果: $passed 通过, $failed 失败\n";
echo "========================================\n\n";

exit($failed > 0 ? 1 : 0);
