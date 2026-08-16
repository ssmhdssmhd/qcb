<?php
// ===== v5.13.10-HOTFIX3：三重防重声明（100% 防 Fatal Cannot declare class AdFilter）=====
//   1. 常量 guard：同文件二次 require 立即 return
//   2. class_exists：不同文件（xt/AdFilter.php 先加载了同名完整版）立即 return
//   3. 先 define 再判断，防 OPcache 重排
if (defined('SRC_ADFILTER_PHP_V1')) return;
define('SRC_ADFILTER_PHP_V1', 1);
if (class_exists('AdFilter', false)) {
    if (function_exists('error_log')) {
        @error_log('[src/AdFilter.php:guard] AdFilter 类已存在（可能来自 xt/AdFilter.php），本文件跳过加载，避免 Fatal 同名类。');
    }
    return;
}

/**
 * v5.13.10-HOTFIX3：与 xt/AdFilter.php 同名类冲突防护 + 构造函数签名兼容。
 *   本文件是老版简版 AdFilter（构造签名：AdFilter($ruleEngine)）；
 *   xt/AdFilter.php 是新版完整版（构造签名：AdFilter(array $config, ?callable $aiClient = null)）。
 *   无论谁先加载都不得 Fatal——两份文件开头均加「三重 guard」。
 *   若加载顺序错位导致调用方的 new AdFilter(...) 参数与已加载版本不匹配，
 *   由调用方（M3U8AdSkipper / AiSmartProcessor 等）在自身文件中 try/catch 做适配。
 */

class AdFilter {
    private $ruleEngine;

    public function __construct($ruleEngine) {
        $this->ruleEngine = $ruleEngine;
    }

    public function filter($playlist) {
        if (!empty($playlist['isMaster'])) {
            return $this->filterMasterPlaylist($playlist);
        }
        return $this->filterMediaPlaylist($playlist);
    }

    private function filterMediaPlaylist($playlist) {
        $segments = isset($playlist['segments']) ? $playlist['segments'] : [];
        $segments = $this->preserveOriginalIndices($segments);
        $results = $this->ruleEngine->checkAllSegments($segments);

        $keptSegments = [];
        $removedSegments = [];

        foreach ($results as $result) {
            if ($result['isAd']) {
                $removedSegments[] = array_merge($result['segment'], [
                    'adInfo' => [
                        'matchedRules' => $result['matchedRules']
                    ]
                ]);
            } else {
                $keptSegments[] = $result['segment'];
            }
        }

        $adjustedSegments = $this->adjustSequenceNumbers($keptSegments);

        return array_merge($playlist, [
            'segments' => $adjustedSegments,
            'mediaSequence' => 0,
            'removedSegments' => $removedSegments,
            'adDetected' => count($removedSegments) > 0
        ]);
    }

    private function filterMasterPlaylist($playlist) {
        $variants = [];
        foreach ($playlist['variants'] as $v) {
            $variants[] = array_merge($v, [
                'uri' => $this->buildProxyUri($v['uri'])
            ]);
        }
        return array_merge($playlist, [
            'variants' => $variants
        ]);
    }

    private function adjustSequenceNumbers($segments) {
        $result = [];
        foreach ($segments as $index => $segment) {
            $result[] = array_merge($segment, [
                'sequence' => $index
            ]);
        }
        return $result;
    }

    private function preserveOriginalIndices($segments, $offset = 0) {
        $result = [];
        foreach ($segments as $index => $segment) {
            $result[] = array_merge($segment, [
                'originalIndex' => $offset + $index
            ]);
        }
        return $result;
    }

    private function buildProxyUri($originalUri) {
        return $originalUri;
    }

    public function smartFilter($playlist) {
        $segments = isset($playlist['segments']) ? $playlist['segments'] : [];
        if (count($segments) === 0) {
            return array_merge($playlist, [
                'removedSegments' => [],
                'adDetected' => false
            ]);
        }

        $segments = $this->preserveOriginalIndices($segments);
        $results = $this->ruleEngine->checkAllSegments($segments);

        $adIndices = [];
        foreach ($results as $i => $r) {
            if ($r['isAd']) {
                $adIndices[] = $i;
            }
        }

        if (count($adIndices) === 0) {
            return array_merge($playlist, [
                'removedSegments' => [],
                'adDetected' => false
            ]);
        }

        $adClusters = $this->findAdClusters($adIndices, count($segments));

        $validClusters = array_filter($adClusters, function($cluster) use ($segments) {
            return $this->isLikelyAdCluster($cluster, $segments);
        });

        $validAdIndices = [];
        foreach ($validClusters as $cluster) {
            for ($i = $cluster['start']; $i <= $cluster['end']; $i++) {
                $validAdIndices[$i] = true;
            }
        }

        $keptSegments = [];
        $removedSegments = [];

        foreach ($segments as $index => $segment) {
            if (isset($validAdIndices[$index])) {
                $result = $results[$index];
                $clusterIndex = 0;
                $ci = 0;
                foreach ($validClusters as $c) {
                    if ($index >= $c['start'] && $index <= $c['end']) {
                        $clusterIndex = $ci;
                        break;
                    }
                    $ci++;
                }
                $removedSegments[] = array_merge($segment, [
                    'adInfo' => [
                        'matchedRules' => $result['matchedRules'],
                        'cluster' => $clusterIndex
                    ]
                ]);
            } else {
                $keptSegments[] = $segment;
            }
        }

        return array_merge($playlist, [
            'segments' => $this->adjustSequenceNumbers($keptSegments),
            'mediaSequence' => 0,
            'removedSegments' => $removedSegments,
            'adDetected' => count($removedSegments) > 0,
            'adClusters' => array_values($validClusters)
        ]);
    }

    private function findAdClusters($adIndices, $totalSegments) {
        if (count($adIndices) === 0) return [];

        $clusters = [];
        $currentCluster = [
            'start' => $adIndices[0],
            'end' => $adIndices[0],
            'count' => 1
        ];

        for ($i = 1; $i < count($adIndices); $i++) {
            if ($adIndices[$i] - $adIndices[$i - 1] <= 2) {
                $currentCluster['end'] = $adIndices[$i];
                $currentCluster['count']++;
            } else {
                $clusters[] = $currentCluster;
                $currentCluster = [
                    'start' => $adIndices[$i],
                    'end' => $adIndices[$i],
                    'count' => 1
                ];
            }
        }
        $clusters[] = $currentCluster;

        return $clusters;
    }

    private function isLikelyAdCluster($cluster, $segments) {
        if ($cluster['count'] < 2) return false;

        $clusterSegments = array_slice($segments, $cluster['start'], $cluster['end'] - $cluster['start'] + 1);
        $totalDuration = 0;
        foreach ($clusterSegments as $s) {
            $totalDuration += $s['duration'];
        }

        $durations = [];
        foreach ($clusterSegments as $s) {
            $durations[] = $s['duration'];
        }
        $avgDuration = $totalDuration / count($clusterSegments);
        
        $variance = 0;
        foreach ($durations as $d) {
            $variance += pow($d - $avgDuration, 2);
        }
        $variance /= count($durations);
        
        $isUniformDuration = sqrt($variance) < $avgDuration * 0.2;

        $isAtStart = $cluster['start'] <= 2;
        $isAtEnd = $cluster['end'] >= count($segments) - 3;

        $hasDiscontinuity = false;
        foreach ($clusterSegments as $s) {
            if (!empty($s['discontinuity'])) {
                $hasDiscontinuity = true;
                break;
            }
        }

        return (
            $cluster['count'] >= 3 ||
            ($isAtStart && $totalDuration >= 5) ||
            ($isAtEnd && $totalDuration >= 5) ||
            ($hasDiscontinuity && $cluster['count'] >= 2) ||
            ($isUniformDuration && $cluster['count'] >= 4)
        );
    }
}
