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
