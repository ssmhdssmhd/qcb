<?php
/**
 * pt AI 自动分析和优化引擎
 * 自动学习匹配模式，优化算法参数
 */

class PtAIAnalyzer
{
    private $dataFile;
    private $learningData = [];
    private $weights = [
        'title_exact' => 30,
        'title_similarity' => 25,
        'title_contains' => 15,
        'season_match' => 20,
        'episode_match' => 15,
        'part_match' => 10,
        'version_match' => 5,
        'remarks_quality' => 5,
        'keyword_count' => 10,
        'semantic_similarity' => 15,
    ];

    public function __construct()
    {
        $this->dataFile = __DIR__ . '/data/ai_learning.json';
        $this->loadLearningData();
    }

    /**
     * 智能匹配视频
     * @param array $videoInfo 官方视频信息
     * @param array $candidates 资源站候选列表
     * @return array ['best_match' => null|array, 'all_matches' => array, 'method' => string]
     */
    public function smartMatch($videoInfo, $candidates)
    {
        if (empty($candidates)) {
            return ['best_match' => null, 'all_matches' => [], 'method' => 'no_candidates'];
        }

        $targetTitle = $this->normalizeTitle($videoInfo['title'] ?? '');
        $targetBaseTitle = $this->normalizeTitle($videoInfo['base_title'] ?? $videoInfo['title'] ?? '');
        $targetSeason = $videoInfo['season_num'] ?? null;
        $targetEpisode = $videoInfo['episode_num'] ?? null;
        $targetPart = $videoInfo['part'] ?? null;
        $targetVersion = $videoInfo['version'] ?? null;

        $matches = [];

        foreach ($candidates as $candidate) {
            $score = 0;
            $details = [];

            $candidateName = $candidate['name'] ?? '';
            $candidateRemarks = $candidate['remarks'] ?? '';
            $candidateFull = $candidateName . ' ' . $candidateRemarks;

            $normCandidate = $this->normalizeTitle($candidateName);
            $normCandidateFull = $this->normalizeTitle($candidateFull);

            // 1. 标题精确匹配
            if ($targetBaseTitle === $normCandidate && !empty($targetBaseTitle)) {
                $score += $this->weights['title_exact'];
                $details[] = 'title_exact';
            }

            // 2. 标题相似度
            $similarity = $this->calculateSimilarity($targetBaseTitle, $normCandidate);
            $score += $similarity * $this->weights['title_similarity'] / 100;
            $details[] = 'similarity:' . round($similarity, 1);

            // 3. 包含匹配
            if (!empty($targetBaseTitle) && !empty($normCandidate)) {
                if (mb_strpos($normCandidate, $targetBaseTitle) !== false) {
                    $score += $this->weights['title_contains'];
                    $details[] = 'candidate_contains_target';
                }
                if (mb_strpos($targetBaseTitle, $normCandidate) !== false) {
                    $score += $this->weights['title_contains'] * 0.5;
                    $details[] = 'target_contains_candidate';
                }
            }

            // 4. 季数匹配
            $candidateSeason = $this->extractSeason($candidateFull);
            if ($targetSeason !== null && $candidateSeason !== null) {
                if ($targetSeason == $candidateSeason) {
                    $score += $this->weights['season_match'];
                    $details[] = 'season_match';
                } else {
                    $score -= $this->weights['season_match'] * 0.5;
                    $details[] = 'season_mismatch';
                }
            }

            // 5. 集数匹配
            $candidateEpisode = $this->extractEpisode($candidateFull);
            if ($targetEpisode !== null && $candidateEpisode !== null) {
                if ($targetEpisode == $candidateEpisode) {
                    $score += $this->weights['episode_match'];
                    $details[] = 'episode_match';
                }
            }

            // 6. 版本匹配
            if ($targetVersion && $candidateRemarks) {
                if (strpos($candidateRemarks, $targetVersion) !== false) {
                    $score += $this->weights['version_match'];
                    $details[] = 'version_match';
                }
            }

            // 7. 备注质量
            if (!empty($candidateRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片|蓝光|4K/u', $candidateRemarks)) {
                    $score += $this->weights['remarks_quality'];
                    $details[] = 'quality_remarks';
                }
            }

            // 8. 排除项
            if (preg_match('/电影解说|预告片|片花|花絮|剪辑|解说|速看|混剪|盘点|reaction/i', $candidateName)) {
                $score -= 30;
                $details[] = 'excluded_content';
            }

            $score = max(0, min(100, $score));

            if ($score >= 50) {
                $matches[] = [
                    'video' => $candidate,
                    'score' => round($score, 2),
                    'details' => $details,
                    'method' => 'ai_smart_match',
                ];
            }
        }

        // 按分数排序
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $bestMatch = !empty($matches) ? $matches[0] : null;

        return [
            'best_match' => $bestMatch,
            'all_matches' => $matches,
            'method' => 'ai_smart_match',
            'total_candidates' => count($candidates),
            'weights_used' => $this->weights,
        ];
    }

    /**
     * 从匹配结果中学习，优化权重
     * @param array $videoInfo
     * @param array $matchedVideo
     * @param bool $isCorrect
     */
    public function learnFromMatch($videoInfo, $matchedVideo, $isCorrect)
    {
        $adjustment = $isCorrect ? 0.5 : -0.3;

        $this->learningData[] = [
            'timestamp' => time(),
            'video_title' => $videoInfo['title'] ?? '',
            'matched_name' => $matchedVideo['name'] ?? '',
            'is_correct' => $isCorrect,
        ];

        // 根据匹配正确性微调权重
        if ($isCorrect) {
            $this->weights['title_exact'] = min(40, $this->weights['title_exact'] + $adjustment);
            $this->weights['title_similarity'] = min(35, $this->weights['title_similarity'] + $adjustment);
        } else {
            $this->weights['title_contains'] = max(5, $this->weights['title_contains'] + $adjustment);
        }

        $this->saveLearningData();
    }

    /**
     * 自动分析匹配失败的原因并给出优化建议
     * @param array $videoInfo
     * @param array $searchResults
     * @return array
     */
    public function analyzeFailure($videoInfo, $searchResults)
    {
        $analysis = [
            'reason' => '',
            'suggestions' => [],
            'confidence' => 0,
        ];

        $targetTitle = $videoInfo['base_title'] ?? $videoInfo['title'] ?? '';

        if (empty($searchResults)) {
            $analysis['reason'] = '资源站无搜索结果';
            $analysis['suggestions'][] = '增加搜索站点数量';
            $analysis['suggestions'][] = '尝试使用去标点关键词搜索';
            $analysis['confidence'] = 90;
            return $analysis;
        }

        // 检查是否有相似但分数不够的结果
        $bestSimilarity = 0;
        $bestCandidate = null;
        foreach ($searchResults as $candidate) {
            $sim = $this->calculateSimilarity($targetTitle, $this->normalizeTitle($candidate['name'] ?? ''));
            if ($sim > $bestSimilarity) {
                $bestSimilarity = $sim;
                $bestCandidate = $candidate;
            }
        }

        if ($bestSimilarity >= 70 && $bestSimilarity < 90) {
            $analysis['reason'] = '存在相似资源但匹配分数未达标';
            $analysis['suggestions'][] = '降低匹配阈值';
            $analysis['suggestions'][] = '检查标题清理是否正确';
            $analysis['suggestions'][] = '尝试使用主标题（冒号前部分）搜索';
            $analysis['confidence'] = 80;
        } elseif ($bestSimilarity < 50) {
            $analysis['reason'] = '资源站中不存在该视频';
            $analysis['suggestions'][] = '更换搜索站点';
            $analysis['suggestions'][] = '检查视频标题是否正确';
            $analysis['confidence'] = 70;
        }

        return $analysis;
    }

    /**
     * 标题归一化
     */
    private function normalizeTitle($title)
    {
        if (empty($title)) return '';

        $title = preg_replace('/[\(\)\[\]\{\}【】「」『』〈〉《》<>〖〗]/u', ' ', $title);
        $title = preg_replace('/[，。；：！？、·〜～\-—_]/u', ' ', $title);
        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title);
        $title = mb_strtolower($title);

        return $title;
    }

    /**
     * 计算标题相似度（综合算法）
     */
    private function calculateSimilarity($str1, $str2)
    {
        if (empty($str1) || empty($str2)) return 0;
        if ($str1 === $str2) return 100;

        // similar_text
        similar_text($str1, $str2, $simPercent);

        // Jaccard 相似度
        $chars1 = array_unique(preg_split('//u', $str1, -1, PREG_SPLIT_NO_EMPTY));
        $chars2 = array_unique(preg_split('//u', $str2, -1, PREG_SPLIT_NO_EMPTY));
        $intersection = count(array_intersect($chars1, $chars2));
        $union = count(array_unique(array_merge($chars1, $chars2)));
        $jaccard = $union > 0 ? ($intersection / $union) * 100 : 0;

        // 综合分数
        return ($simPercent + $jaccard) / 2;
    }

    /**
     * 提取季数
     */
    private function extractSeason($title)
    {
        if (preg_match('/第\s*(\d+)\s*季/u', $title, $m)) return intval($m[1]);
        if (preg_match('/第\s*([一二三四五六七八九十]+)\s*季/u', $title, $m)) return $this->chineseToNumber($m[1]);
        if (preg_match('/S(\d+)/i', $title, $m)) return intval($m[1]);
        if (preg_match('/Ⅱ/u', $title)) return 2;
        if (preg_match('/Ⅲ/u', $title)) return 3;
        return null;
    }

    /**
     * 提取集数
     */
    private function extractEpisode($title)
    {
        if (preg_match('/第\s*(\d+)\s*集/u', $title, $m)) return intval($m[1]);
        if (preg_match('/EP\s*(\d+)/i', $title, $m)) return intval($m[1]);
        if (preg_match('/更新至(\d+)集/u', $title, $m)) return intval($m[1]);
        return null;
    }

    /**
     * 中文数字转阿拉伯数字
     */
    private function chineseToNumber($str)
    {
        $map = ['零' => 0, '一' => 1, '二' => 2, '两' => 2, '三' => 3, '四' => 4,
                '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9, '十' => 10];
        if (isset($map[$str])) return $map[$str];
        if (preg_match('/^十(\w)$/', $str, $m) && isset($map[$m[1]])) return 10 + $map[$m[1]];
        if (preg_match('/^(\w)十(\w)$/', $str, $m) && isset($map[$m[1]]) && isset($map[$m[2]])) return $map[$m[1]] * 10 + $map[$m[2]];
        return 0;
    }

    /**
     * 加载学习数据
     */
    private function loadLearningData()
    {
        $dir = dirname($this->dataFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (file_exists($this->dataFile)) {
            $data = json_decode(file_get_contents($this->dataFile), true);
            if (is_array($data)) {
                $this->learningData = $data['learning_data'] ?? [];
                $savedWeights = $data['weights'] ?? [];
                if (!empty($savedWeights)) {
                    $this->weights = array_merge($this->weights, $savedWeights);
                }
            }
        }
    }

    /**
     * 保存学习数据
     */
    private function saveLearningData()
    {
        $data = [
            'learning_data' => array_slice($this->learningData, -1000),
            'weights' => $this->weights,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        @file_put_contents($this->dataFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /**
     * 获取当前权重
     */
    public function getWeights()
    {
        return $this->weights;
    }
}
