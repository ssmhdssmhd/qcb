<?php

class DomainRuleManager {

    private $gzDir;

    public function __construct() {
        $this->gzDir = __DIR__;
    }

    public function getAllRules() {
        $rules = [];
        $files = glob($this->gzDir . '/rules_*.php');
        foreach ($files as $file) {
            $domainRules = require $file;
            if (is_array($rules) && isset($domainRules['domain'])) {
                $filename = basename($file);
                $domainRules['_filename'] = $filename;
                $domainRules['_filemtime'] = filemtime($file);
                $rules[$domainRules['domain']] = $domainRules;
            }
        }
        return $rules;
    }

    public function getRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            return require $file;
        }
        return null;
    }

    public function saveRules($domain, $ruleData) {
        $ruleData['domain'] = $domain;
        if (!isset($ruleData['analysis_date'])) {
            $ruleData['analysis_date'] = date('Y-m-d H:i:s');
        }
        $file = $this->getRuleFilePath($domain);
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * ' . $domain . ' 域名广告和插播规则' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($ruleData) . ';' . "\n";
        return file_put_contents($file, $content) !== false;
    }

    public function deleteRules($domain) {
        $file = $this->getRuleFilePath($domain);
        if (file_exists($file)) {
            return unlink($file);
        }
        return false;
    }

    public function createFromAnalysis($domain, $analysisResult, $customConfig = []) {
        $defaultRules = [
            'domain' => $domain,
            'duration_rules' => [
                [
                    'name' => 'short_segment',
                    'enabled' => true,
                    'type' => 'duration',
                    'operator' => '<',
                    'threshold' => 2,
                    'reason' => '极短片段 (<2秒) 可能是广告',
                    'weight' => 30
                ]
            ],
            'discontinuity_rules' => [
                [
                    'name' => 'discontinuity',
                    'enabled' => $analysisResult['discontinuityCount'] > 5,
                    'type' => 'discontinuity',
                    'reason' => 'DISCONTINUITY 标记表示插播切换',
                    'weight' => 80
                ]
            ],
            'sequence_jump_rules' => [
                [
                    'name' => 'sequence_jump_forward',
                    'enabled' => count($analysisResult['sequenceJumps']) > 0,
                    'type' => 'sequence_jump',
                    'direction' => 'forward',
                    'threshold' => 100000,
                    'reason' => '序列号向前跳跃可能表示广告插播',
                    'weight' => 90
                ],
                [
                    'name' => 'sequence_jump_backward',
                    'enabled' => count($analysisResult['sequenceJumps']) > 0,
                    'type' => 'sequence_jump',
                    'direction' => 'backward',
                    'threshold' => 100000,
                    'reason' => '序列号向后跳跃可能表示广告结束',
                    'weight' => 90
                ]
            ],
            'filename_patterns' => [],
            'ad_threshold' => 50,
            'confidence' => [
                'high' => 80,
                'medium' => 50,
                'low' => 30
            ],
            'note' => '基于靶机测试分析自动生成的规则',
            'analysis_date' => date('Y-m-d H:i:s'),
            'analysis_stats' => [
                'totalSegments' => $analysisResult['totalCount'],
                'adSegments' => $analysisResult['adCount'],
                'discontinuityCount' => $analysisResult['discontinuityCount'],
                'sequenceJumps' => count($analysisResult['sequenceJumps']),
                'adClusters' => count($analysisResult['adClusters'])
            ]
        ];
        return array_merge($defaultRules, $customConfig);
    }

    private function getRuleFilePath($domain) {
        $safeDomain = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $domain);
        return $this->gzDir . '/rules_' . $safeDomain . '.php';
    }

    private function arrayExport($array, $indent = 0) {
        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        if (empty($array)) {
            return '[]';
        }
        $isList = array_keys($array) === range(0, count($array) - 1);
        $items = [];
        foreach ($array as $key => $value) {
            if ($isList) {
                $items[] = $nextPrefix . $this->exportValue($value, $indent + 1);
            } else {
                $items[] = $nextPrefix . var_export($key, true) . ' => ' . $this->exportValue($value, $indent + 1);
            }
        }
        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }

    private function exportValue($value, $indent) {
        if (is_array($value)) {
            return $this->arrayExport($value, $indent);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        } else {
            return var_export((string)$value, true);
        }
    }
}
