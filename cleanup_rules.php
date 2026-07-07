<?php
/**
 * 清理规则文件中的大字段，减小文件体积
 */

$gzDir = __DIR__ . '/gz';
$files = glob($gzDir . '/rules_*.php');
if (!is_array($files)) {
    echo "没有找到规则文件\n";
    exit(0);
}

echo "找到 " . count($files) . " 个规则文件\n\n";

$totalSaved = 0;
$cleaned = 0;

foreach ($files as $file) {
    $originalSize = filesize($file);
    $rules = @require $file;
    if (!is_array($rules) || empty($rules['domain'])) {
        echo "  ⚠ " . basename($file) . " - 无效规则文件，跳过\n";
        continue;
    }

    $cleanedRules = filterLargeFields($rules);

    $content = '<?php' . "\n";
    $content .= '/**' . "\n";
    $content .= ' * ' . $rules['domain'] . " 域名广告和插播规则\n";
    $content .= ' * 清理于: ' . date('Y-m-d H:i:s') . "\n";
    $content .= ' */' . "\n\n";
    $content .= 'return ' . var_export($cleanedRules, true) . ';' . "\n";

    file_put_contents($file, $content);
    clearstatcache(true, $file);
    $newSize = filesize($file);
    $saved = $originalSize - $newSize;
    $totalSaved += $saved;
    $cleaned++;

    $pct = $originalSize > 0 ? round($saved / $originalSize * 100, 1) : 0;
    echo "  ✓ " . basename($file) . " - " . formatSize($originalSize) . " → " . formatSize($newSize) . " (节省 {$pct}%)\n";
}

echo "\n清理完成！共清理 $cleaned 个文件，节省 " . formatSize($totalSaved) . "\n";

function filterLargeFields($data) {
    if (isset($data['history_stats']) && is_array($data['history_stats'])) {
        $slimHistory = [];
        foreach ($data['history_stats'] as $stat) {
            if (!is_array($stat)) continue;
            $slimStat = [
                'totalCount' => $stat['totalCount'] ?? 0,
                'adCount' => $stat['adCount'] ?? 0,
                'adPercentage' => $stat['adPercentage'] ?? 0,
                'discontinuityCount' => $stat['discontinuityCount'] ?? 0,
                'cueMarkerCount' => $stat['cueMarkerCount'] ?? 0,
                'scte35Count' => $stat['scte35Count'] ?? 0,
                'adTagCount' => $stat['adTagCount'] ?? 0,
                'confidence' => $stat['confidence'] ?? 0,
                'analyzed_at' => $stat['analyzed_at'] ?? date('Y-m-d H:i:s'),
            ];
            if (isset($stat['adClusters']) && is_array($stat['adClusters'])) {
                $slimStat['adClusterCount'] = count($stat['adClusters']);
            }
            if (isset($stat['stats']) && is_array($stat['stats'])) {
                $slimStat['totalSegments'] = $stat['stats']['totalSegments'] ?? $stat['totalCount'] ?? 0;
                $slimStat['adSegments'] = $stat['stats']['adSegments'] ?? $stat['adCount'] ?? 0;
            }
            if (isset($stat['psychologicalFeatures']) && is_array($stat['psychologicalFeatures'])) {
                $slimStat['ad_density'] = $stat['psychologicalFeatures']['ad_density'] ?? 0;
            }
            $slimHistory[] = $slimStat;
        }
        $data['history_stats'] = $slimHistory;
    }

    $bigFields = ['segments', 'adSegments', 'contentSegments', 'allSegments',
                  'segmentDetails', 'rawSegments', 'parsedSegments',
                  'sequence_jump_details', 'jump_details', 'fullAnalysis'];
    foreach ($bigFields as $field) {
        if (isset($data[$field])) {
            unset($data[$field]);
        }
    }

    if (isset($data['analysis_stats']) && is_array($data['analysis_stats'])) {
        $stats = &$data['analysis_stats'];
        unset($stats['segments']);
        unset($stats['adSegmentList']);
        unset($stats['contentSegmentList']);
    }

    return $data;
}

function formatSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 1) . $units[$i];
}
