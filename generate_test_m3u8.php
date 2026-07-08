<?php
/**
 * 靶机测试 M3U8 文件生成器
 * 
 * 生成各种类型的测试 m3u8 文件，模拟真实视频广告场景
 * 用于测试广告分析、规则学习、去广告等功能
 * 
 * 使用方法:
 *   - generate_test_m3u8.php?type=basic          基础测试
 *   - generate_test_m3u8.php?type=pre_roll        前置广告
 *   - generate_test_m3u8.php?type=mid_roll        中插广告
 *   - generate_test_m3u8.php?type=post_roll       后置广告
 *   - generate_test_m3u8.php?type=mixed           混合广告
 *   - generate_test_m3u8.php?type=short_segments  短片段广告
 *   - generate_test_m3u8.php?type=long_movie      长电影（多段广告）
 *   - generate_test_m3u8.php?type=all             生成全部测试文件
 */

header('Content-Type: text/plain; charset=utf-8');

$type = $_GET['type'] ?? 'basic';
$outputDir = __DIR__ . '/cache/m3u8/test';
$baseUrl = dirname($_SERVER['PHP_SELF'] ?? '') . '/cache/m3u8/test';

/**
 * 生成基础 m3u8 内容
 */
function generateSegments($count, $baseDuration = 10, $prefix = 'segment', $ad = false) {
    $lines = [];
    for ($i = 0; $i < $count; $i++) {
        $duration = $baseDuration + rand(-1, 1) * 0.5;
        $adTag = $ad ? '_ad' : '';
        $lines[] = '#EXTINF:' . number_format($duration, 3) . ',';
        $lines[] = "{$prefix}_{$i}{$adTag}.ts";
    }
    return $lines;
}

/**
 * 生成带前置广告的 m3u8
 */
function generatePreRoll($adSegments = 5, $contentSegments = 50) {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 前置广告 ===',
    ];
    
    $lines = array_merge($lines, generateSegments($adSegments, 8, 'ad/pre', true));
    
    $lines[] = '';
    $lines[] = '// === 正片内容 ===';
    $lines = array_merge($lines, generateSegments($contentSegments, 10, 'content/main'));
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成带中插广告的 m3u8
 */
function generateMidRoll($contentSegments = 100, $adCount = 3, $adSegmentsPerAd = 4) {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
    ];
    
    $segmentsPerBlock = floor($contentSegments / ($adCount + 1));
    $segmentIndex = 0;
    $adIndex = 0;
    
    for ($block = 0; $block <= $adCount; $block++) {
        if ($block > 0) {
            $lines[] = "// === 中插广告 $block ===";
            $lines = array_merge($lines, generateSegments($adSegmentsPerAd, 8, "ad/mid{$adIndex}", true));
            $adIndex++;
            $lines[] = '';
        }
        
        $lines[] = "// === 正片第 " . ($block + 1) . " 段 ===";
        for ($i = 0; $i < $segmentsPerBlock; $i++) {
            $duration = 10 + rand(-1, 1) * 0.5;
            $lines[] = '#EXTINF:' . number_format($duration, 3) . ',';
            $lines[] = "content/block{$block}_{$i}.ts";
            $segmentIndex++;
        }
        $lines[] = '';
    }
    
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成带后置广告的 m3u8
 */
function generatePostRoll($contentSegments = 50, $adSegments = 3) {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 正片内容 ===',
    ];
    
    $lines = array_merge($lines, generateSegments($contentSegments, 10, 'content/main'));
    
    $lines[] = '';
    $lines[] = '// === 后置广告 ===';
    $lines = array_merge($lines, generateSegments($adSegments, 8, 'ad/post', true));
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成混合广告的 m3u8（前、中、后都有）
 */
function generateMixed() {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 前置广告（3个片段） ===',
    ];
    
    $lines = array_merge($lines, generateSegments(3, 8, 'ad/pre', true));
    
    $lines[] = '';
    $lines[] = '// === 正片第1段 ===';
    $lines = array_merge($lines, generateSegments(25, 10, 'content/p1'));
    
    $lines[] = '';
    $lines[] = '// === 中插广告1（5个片段） ===';
    $lines = array_merge($lines, generateSegments(5, 8, 'ad/mid1', true));
    
    $lines[] = '';
    $lines[] = '// === 正片第2段 ===';
    $lines = array_merge($lines, generateSegments(30, 10, 'content/p2'));
    
    $lines[] = '';
    $lines[] = '// === 中插广告2（4个片段） ===';
    $lines = array_merge($lines, generateSegments(4, 8, 'ad/mid2', true));
    
    $lines[] = '';
    $lines[] = '// === 正片第3段 ===';
    $lines = array_merge($lines, generateSegments(20, 10, 'content/p3'));
    
    $lines[] = '';
    $lines[] = '// === 后置广告（2个片段） ===';
    $lines = array_merge($lines, generateSegments(2, 8, 'ad/post', true));
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成长电影测试（多个广告点）
 */
function generateLongMovie() {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 片头广告 ==',
    ];
    
    $lines = array_merge($lines, generateSegments(6, 10, 'ad/opening', true));
    
    $adPositions = [0.15, 0.3, 0.5, 0.7, 0.85];
    $totalContent = 200;
    
    $lastPos = 0;
    foreach ($adPositions as $idx => $pos) {
        $currentPos = floor($totalContent * $pos);
        $segCount = $currentPos - $lastPos;
        
        $lines[] = '';
        $lines[] = "// === 正片片段 " . ($idx + 1) . " ===";
        for ($i = 0; $i < $segCount; $i++) {
            $duration = 10 + rand(-1, 1) * 0.5;
            $lines[] = '#EXTINF:' . number_format($duration, 3) . ',';
            $lines[] = "content/part" . ($idx + 1) . "_{$i}.ts";
        }
        
        $adCount = 4 + rand(0, 2);
        $lines[] = '';
        $lines[] = "// === 广告第 " . ($idx + 1) . " 波 ===";
        $lines = array_merge($lines, generateSegments($adCount, 8, "ad/spot{$idx}", true));
        
        $lastPos = $currentPos;
    }
    
    $remaining = $totalContent - $lastPos;
    $lines[] = '';
    $lines[] = '// === 正片结尾 ===';
    for ($i = 0; $i < $remaining; $i++) {
        $duration = 10 + rand(-1, 1) * 0.5;
        $lines[] = '#EXTINF:' . number_format($duration, 3) . ',';
        $lines[] = "content/ending_{$i}.ts";
    }
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成短片段广告测试（广告片段很短，难以检测）
 */
function generateShortSegmentsAd() {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:5',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 正片内容 ===',
    ];
    
    for ($i = 0; $i < 80; $i++) {
        $duration = 3 + rand(-0.5, 0.5);
        $isAd = ($i % 20 == 15 || $i % 20 == 16 || $i % 20 == 17);
        $prefix = $isAd ? 'ad/short' : 'content/main';
        $lines[] = '#EXTINF:' . number_format($duration, 3) . ',';
        $lines[] = "{$prefix}_{$i}.ts";
    }
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

/**
 * 生成基础测试（纯内容，无广告）
 */
function generateBasic() {
    $lines = [
        '#EXTM3U',
        '#EXT-X-VERSION:3',
        '#EXT-X-TARGETDURATION:12',
        '#EXT-X-MEDIA-SEQUENCE:0',
        '',
        '// === 纯内容测试 ===',
    ];
    
    $lines = array_merge($lines, generateSegments(50, 10, 'content/basic'));
    
    $lines[] = '';
    $lines[] = '#EXT-X-ENDLIST';
    
    return implode("\n", $lines);
}

// 主逻辑
$generators = [
    'basic' => ['name' => '基础测试（无广告）', 'fn' => 'generateBasic'],
    'pre_roll' => ['name' => '前置广告', 'fn' => 'generatePreRoll'],
    'mid_roll' => ['name' => '中插广告', 'fn' => 'generateMidRoll'],
    'post_roll' => ['name' => '后置广告', 'fn' => 'generatePostRoll'],
    'mixed' => ['name' => '混合广告', 'fn' => 'generateMixed'],
    'short_segments' => ['name' => '短片段广告', 'fn' => 'generateShortSegmentsAd'],
    'long_movie' => ['name' => '长电影（多段广告）', 'fn' => 'generateLongMovie'],
];

if ($type === 'all') {
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }
    
    $results = [];
    foreach ($generators as $key => $gen) {
        $content = call_user_func($gen['fn']);
        $filename = $outputDir . '/' . $key . '.m3u8';
        file_put_contents($filename, $content);
        $results[] = [
            'type' => $key,
            'name' => $gen['name'],
            'file' => $filename,
            'url' => $baseUrl . '/' . $key . '.m3u8',
            'size' => strlen($content)
        ];
    }
    
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "          🎬 靶机测试 M3U8 文件生成完成\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "📁 输出目录: $outputDir\n\n";
    echo "📋 生成文件列表:\n";
    echo str_repeat('─', 65) . "\n";
    printf("  %-20s %-25s %10s\n", '类型', '名称', '大小');
    echo str_repeat('─', 65) . "\n";
    foreach ($results as $r) {
        printf("  %-20s %-25s %8d B\n", $r['type'], $r['name'], $r['size']);
    }
    echo str_repeat('─', 65) . "\n\n";
    echo "🔗 测试 URL (请替换为你的域名):\n\n";
    foreach ($results as $r) {
        echo "  {$r['url']}\n";
    }
    echo "\n✅ 共生成 " . count($results) . " 个测试文件\n";
    echo "\n💡 使用提示:\n";
    echo "  - 将上述 URL 中的域名替换为你实际的域名\n";
    echo "  - 复制 URL 到后台视频分析中测试\n";
    echo "  - 或使用 api_helper.php 中的 analyzeVideo() 函数测试\n";
    
} elseif (isset($generators[$type])) {
    $content = call_user_func($generators[$type]['fn']);
    
    if (isset($_GET['download']) && $_GET['download'] === '1') {
        header('Content-Type: application/vnd.apple.mpegurl');
        header('Content-Disposition: attachment; filename="' . $type . '.m3u8"');
        echo $content;
    } else {
        echo $content;
    }
} else {
    echo "═══════════════════════════════════════════════════════════════\n";
    echo "          🎬 靶机测试 M3U8 生成器\n";
    echo "═══════════════════════════════════════════════════════════════\n\n";
    echo "💡 使用方法:\n\n";
    echo "  1. 生成所有测试文件:\n";
    echo "     generate_test_m3u8.php?type=all\n\n";
    echo "  2. 生成单个类型并在线预览:\n";
    echo "     generate_test_m3u8.php?type=pre_roll\n\n";
    echo "  3. 生成单个类型并下载:\n";
    echo "     generate_test_m3u8.php?type=pre_roll&download=1\n\n";
    echo "📋 可用类型:\n";
    echo str_repeat('─', 60) . "\n";
    printf("  %-20s %s\n", '类型', '说明');
    echo str_repeat('─', 60) . "\n";
    foreach ($generators as $key => $gen) {
        printf("  %-20s %s\n", $key, $gen['name']);
    }
    echo str_repeat('─', 60) . "\n";
}
