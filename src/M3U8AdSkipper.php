<?php

require_once __DIR__ . '/M3U8Parser.php';
require_once __DIR__ . '/AdRuleEngine.php';
require_once __DIR__ . '/AdFilter.php';
require_once __DIR__ . '/OutputGenerator.php';

class M3U8AdSkipper {
    private $options = [];
    private $parser;
    private $ruleEngine;
    private $filter;
    private $outputGenerator;

    public function __construct($options = []) {
        $defaultOptions = [
            'minSegmentDuration' => 2,
            'maxSegmentDuration' => 30,
            'adKeywords' => [
                'ad', 'ads', 'advert', 'advertisement',
                'pre-roll', 'mid-roll', 'post-roll',
                'preroll', 'midroll', 'postroll',
                'commercial', 'promo', 'sponsor',
                '广告', '插播', '贴片', '片头', '片尾'
            ],
            'adFilenamePatterns' => [
                '/ad[s]?[-_]?\d+/i',
                '/advert/i',
                '/commercial/i',
                '/pre[-_]?roll/i',
                '/mid[-_]?roll/i',
                '/post[-_]?roll/i',
                '/sponsor/i',
                '/^ad\//i'
            ],
            'durationTolerance' => 0.5
        ];

        $this->options = array_merge($defaultOptions, $options);

        $this->parser = new M3U8Parser();
        $this->ruleEngine = new AdRuleEngine($this->options);
        $this->filter = new AdFilter($this->ruleEngine);
        $this->outputGenerator = new OutputGenerator();
    }

    public function process($input, $options = []) {
        $playlist = $this->parser->parse($input);
        $filteredPlaylist = $this->filter->filter($playlist);
        $output = $this->outputGenerator->generate($filteredPlaylist, $options);

        return [
            'original' => $playlist,
            'filtered' => $filteredPlaylist,
            'output' => $output,
            'stats' => $this->getStats($playlist, $filteredPlaylist)
        ];
    }

    public function processWithSafeguard($input, $options = []) {
        $playlist = $this->parser->parse($input);

        if (!empty($playlist['isMaster'])) {
            $filteredPlaylist = $this->filter->filter($playlist);
            $output = $this->outputGenerator->generate($filteredPlaylist, $options);
            return [
                'original' => $playlist,
                'filtered' => $filteredPlaylist,
                'output' => $output,
                'stats' => $this->getStats($playlist, $filteredPlaylist),
                'safeguardTriggered' => false
            ];
        }

        $segments = $playlist['segments'] ?? [];
        $totalSegments = count($segments);

        $filteredPlaylist = $this->filter->filter($playlist);
        $stats = $this->getStats($playlist, $filteredPlaylist);

        $adPercentage = $stats['adPercentage'] ?? 0;
        $keptSegments = $stats['keptSegments'] ?? 0;

        $needsSafeguard = false;
        $safeguardReason = '';

        if ($totalSegments >= 10 && $adPercentage >= 85) {
            $needsSafeguard = true;
            $safeguardReason = '广告占比过高 (' . $adPercentage . '%)';
        }

        if ($totalSegments >= 20 && $keptSegments < $totalSegments * 0.2) {
            $needsSafeguard = true;
            $safeguardReason = '保留内容过少 (' . $keptSegments . '/' . $totalSegments . ')';
        }

        if ($totalSegments > 0 && $keptSegments === 0) {
            $needsSafeguard = true;
            $safeguardReason = '所有片段均被判定为广告';
        }

        if (!$needsSafeguard) {
            $output = $this->outputGenerator->generate($filteredPlaylist, $options);
            return [
                'original' => $playlist,
                'filtered' => $filteredPlaylist,
                'output' => $output,
                'stats' => $stats,
                'safeguardTriggered' => false
            ];
        }

        $method = $options['safeguardMethod'] ?? 'smart_filter';
        $safeguardResult = null;

        if ($method === 'smart_filter' && method_exists($this->filter, 'smartFilter')) {
            $smartFiltered = $this->filter->smartFilter($playlist);
            $smartStats = $this->getStats($playlist, $smartFiltered);
            $smartKept = $smartStats['keptSegments'] ?? 0;

            if ($smartKept >= $totalSegments * 0.3) {
                $safeguardResult = [
                    'method' => 'smart_filter',
                    'playlist' => $smartFiltered,
                    'stats' => $smartStats
                ];
            }
        }

        if ($safeguardResult === null) {
            $originalThreshold = $this->ruleEngine->getAdThreshold();
            $bestPlaylist = null;
            $bestStats = null;
            $bestKeptRatio = 0;

            $thresholds = [70, 90, 110, 130, 150, 180];
            foreach ($thresholds as $threshold) {
                $this->ruleEngine->setAdThreshold($threshold);
                $tempFiltered = $this->filter->filter($playlist);
                $tempStats = $this->getStats($playlist, $tempFiltered);
                $tempKept = $tempStats['keptSegments'] ?? 0;
                $tempKeptRatio = $tempKept / $totalSegments;

                if ($tempKeptRatio >= 0.3 && $tempKeptRatio <= 0.9) {
                    $bestPlaylist = $tempFiltered;
                    $bestStats = $tempStats;
                    break;
                }

                if ($tempKeptRatio > $bestKeptRatio && $tempKeptRatio < 0.95) {
                    $bestPlaylist = $tempFiltered;
                    $bestStats = $tempStats;
                    $bestKeptRatio = $tempKeptRatio;
                }
            }

            $this->ruleEngine->setAdThreshold($originalThreshold);

            if ($bestPlaylist !== null) {
                $safeguardResult = [
                    'method' => 'threshold_adjustment',
                    'playlist' => $bestPlaylist,
                    'stats' => $bestStats,
                    'adjustedThreshold' => $bestStats['adPercentage'] > 70 ? max($thresholds) : null
                ];
            }
        }

        if ($safeguardResult === null || ($safeguardResult['stats']['keptSegments'] ?? 0) < $totalSegments * 0.1) {
            $output = $this->outputGenerator->generate($playlist, $options);
            return [
                'original' => $playlist,
                'filtered' => $playlist,
                'output' => $output,
                'stats' => [
                    'totalSegments' => $totalSegments,
                    'keptSegments' => $totalSegments,
                    'removedSegments' => 0,
                    'originalDuration' => $stats['originalDuration'] ?? 0,
                    'filteredDuration' => $stats['originalDuration'] ?? 0,
                    'savedDuration' => 0,
                    'adPercentage' => 0
                ],
                'safeguardTriggered' => true,
                'safeguardReason' => $safeguardReason,
                'safeguardAction' => 'fallback_original',
                'safeguardMethod' => 'none'
            ];
        }

        $output = $this->outputGenerator->generate($safeguardResult['playlist'], $options);
        return [
            'original' => $playlist,
            'filtered' => $safeguardResult['playlist'],
            'output' => $output,
            'stats' => $safeguardResult['stats'],
            'safeguardTriggered' => true,
            'safeguardReason' => $safeguardReason,
            'safeguardAction' => 'adjusted_filter',
            'safeguardMethod' => $safeguardResult['method'],
            'originalStats' => $stats
        ];
    }

    private function getStats($original, $filtered) {
        $originalSegments = $original['segments'] ?? [];
        $filteredSegments = $filtered['segments'] ?? [];
        $removedCount = count($originalSegments) - count($filteredSegments);

        $originalDuration = 0;
        foreach ($originalSegments as $s) {
            $originalDuration += $s['duration'] ?? 0;
        }

        $filteredDuration = 0;
        foreach ($filteredSegments as $s) {
            $filteredDuration += $s['duration'] ?? 0;
        }

        return [
            'totalSegments' => count($originalSegments),
            'keptSegments' => count($filteredSegments),
            'removedSegments' => $removedCount,
            'originalDuration' => round($originalDuration * 100) / 100,
            'filteredDuration' => round($filteredDuration * 100) / 100,
            'savedDuration' => round(($originalDuration - $filteredDuration) * 100) / 100,
            'adPercentage' => $originalDuration > 0
                ? round((($originalDuration - $filteredDuration) / $originalDuration) * 10000) / 100
                : 0
        ];
    }

    public function getParser() {
        return $this->parser;
    }

    public function getRuleEngine() {
        return $this->ruleEngine;
    }

    public function getFilter() {
        return $this->filter;
    }

    public function getOutputGenerator() {
        return $this->outputGenerator;
    }
}
