<?php

require_once __DIR__ . '/TitleNormalizer.php';

/**
 * AI 视频匹配器
 *
 * 基于多维度评分的视频匹配算法，使用 TitleNormalizer 进行标题标准化，
 * 结合同义词语义相似度、季/集匹配、版本匹配等维度综合评分。
 *
 * 优化点（v5.8.0）：
 *   - 标题标准化统一委托 TitleNormalizer（消费 synonym_config.php 同义词表）
 *   - 季/集解析委托 TitleNormalizer::getSeasonInfo / getEpisodeInfo，覆盖 S0N/EP\d+/E\d+/罗马数字
 *   - 新增噪声内容排除模式（电影解说/预告片/片花/花絮/混剪/MV/OST 等）
 *   - 标准化结果缓存（按本次 smartMatch 调用生命周期）
 *   - 评分维度精简与权重再平衡，降低噪声维度影响
 *   - 同义词语义相似度使用标准化后的标题，避免重复归一化
 *   - 当标准化基础剧名完全一致时，给予强匹配奖励
 */
class AiVideoMatcher {
    private $learnedWeights = [];
    private $synonymDict = [];
    private $matchHistory = [];

    // 本次 smartMatch 调用内的标准化缓存（title => normalized）
    private $normCache = [];

    // 噪声内容排除模式（命中即跳过该候选）
    private static $excludePatterns = [
        '/电影解说/i', '/预告片/i', '/片花/i', '/花絮/i', '/剪辑/i',
        '/解说/i', '/速看/i', '/混剪/i', '/盘点/i', '/reaction/i',
        '/\bMV\b/i', '/主题曲/i', '/片尾曲/i', '/片头曲/i', '/\bOST\b/i',
        '/彩蛋/i', '/特辑/i', '/幕后/i', '/番外篇/i',
    ];

    public function __construct() {
        $this->initSynonymDict();
        $this->loadLearnedWeights();
    }

    private function initSynonymDict() {
        // 标题级别别名（特定剧名 → 别名列表），用于语义相似度加分
        $this->synonymDict = [
            '完美世界'     => ['完美世界动画', '完美世界动漫'],
            '遮天'         => ['遮天动画', '遮天动漫'],
            '斗破苍穹'     => ['斗破', '斗破动画', '斗破动漫'],
            '凡人修仙传'   => ['凡人', '凡人动画', '凡人修仙'],
            '斗罗大陆'     => ['斗罗', '斗罗动画', '斗罗动漫'],
            '庆余年'       => ['庆余年电视剧'],
            '三体'         => ['三体动画', '三体电视剧'],
            '动画版'       => ['动画', '动漫版', '动漫'],
            '真人版'       => ['真人', '电视剧版', '网剧版'],
            '电影版'       => ['剧场版', '大电影'],
            '完整版'       => ['未删减版', '无删减版'],
        ];
    }

    private function loadLearnedWeights() {
        $defaultWeights = [
            'title_exact'        => 35,   // 标准化后基础剧名完全一致（强信号）
            'title_similarity'   => 25,   // 多算法相似度均值
            'title_contains'     => 12,   // 一方为另一方前缀
            'semantic_similarity'=> 10,   // 同义词命中加分
            'season_match'       => 20,   // 季数一致
            'season_mismatch'    => 25,   // 季数不一致惩罚基数
            'episode_match'      => 12,   // 集数一致
            'part_match'         => 8,    // 部（上/中/下）一致
            'version_match'      => 5,    // 版本一致
            'remarks_quality'    => 3,    // 备注质量信号
            'keyword_count'      => 8,    // 关键字符命中数
            'exclude_penalty'    => 50,   // 命中排除模式惩罚
        ];

        $weightFile = __DIR__ . '/../data/ai_matcher_weights.json';
        if (file_exists($weightFile)) {
            $saved = @json_decode(file_get_contents($weightFile), true);
            if (is_array($saved) && !empty($saved)) {
                $this->learnedWeights = array_merge($defaultWeights, $saved);
                return;
            }
        }
        $this->learnedWeights = $defaultWeights;
    }

    public function saveLearnedWeights() {
        $dir = __DIR__ . '/../data';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $weightFile = $dir . '/ai_matcher_weights.json';
        @file_put_contents($weightFile, json_encode($this->learnedWeights, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 智能匹配主入口
     *
     * @param array $videoInfo    目标视频信息（title/base_title/season_num/episode_num/part/version）
     * @param array $candidates   候选视频列表
     * @return array              匹配结果（best_match/all_matches/method/...）
     */
    public function smartMatch($videoInfo, $candidates) {
        // 重置本次调用的标准化缓存
        $this->normCache = [];

        if (empty($candidates)) {
            return [
                'best_match'      => null,
                'all_matches'     => [],
                'method'          => 'ai_smart_match',
                'total_candidates'=> 0
            ];
        }

        $targetTitle     = $videoInfo['title'] ?? $videoInfo['base_title'] ?? '';
        $targetSeason    = $videoInfo['season_num'] ?? null;
        $targetEpisode   = $videoInfo['episode_num'] ?? null;
        $targetPart      = $videoInfo['part'] ?? null;
        $targetVersion   = $videoInfo['version'] ?? null;
        $targetBaseTitle = $videoInfo['base_title'] ?? $targetTitle;

        // 目标侧标准化（一次计算，多次复用）
        $normTarget     = $this->normalizeCached($targetTitle);
        $normTargetBase = $this->normalizeCached($targetBaseTitle);

        // 目标季/集兜底解析（若调用方未提供，从标题中提取）
        if ($targetSeason === null) {
            $targetSeason = TitleNormalizer::getSeasonInfo($targetTitle);
        }
        if ($targetEpisode === null) {
            $targetEpisode = TitleNormalizer::getEpisodeInfo($targetTitle);
        }

        $w       = $this->learnedWeights;
        $maxScore = ($w['title_exact'] + $w['title_similarity'] + $w['title_contains']
                   + $w['semantic_similarity'] + $w['season_match'] + $w['episode_match']
                   + $w['part_match'] + $w['version_match'] + $w['remarks_quality']
                   + $w['keyword_count']);
        $maxScore = max(1, $maxScore);

        $scored = [];
        foreach ($candidates as $video) {
            $score   = 0;
            $details = [];

            $videoName     = $video['name'] ?? '';
            $videoRemarks  = $video['remarks'] ?? '';
            $videoFullName = $videoName . ' ' . $videoRemarks;

            // 1) 噪声内容排除（命中即重惩罚，几乎不可能成为最佳匹配）
            if ($this->isExcluded($videoName)) {
                $score -= $w['exclude_penalty'];
                $details['exclude_penalty'] = -$w['exclude_penalty'];
            }

            // 2) 解析候选视频的季/集/部/版本
            $videoParsed   = $this->parseTitleInfo($videoName);
            $videoBase     = $videoParsed['base_title'];
            $videoSeason   = $videoParsed['season_num'];
            $videoEpisode  = $videoParsed['episode_num'];
            $videoPart     = $videoParsed['part'];
            $videoVersion  = $videoParsed['version'];

            // 3) 标准化（缓存）
            $normVideo     = $this->normalizeCached($videoName);
            $normVideoBase = $this->normalizeCached($videoBase);

            // 4) 基础剧名完全一致（标准化后）→ 强信号
            if (!empty($normTargetBase) && $normTargetBase === $normVideoBase) {
                $score += $w['title_exact'];
                $details['title_exact'] = $w['title_exact'];
            }

            // 5) 多算法相似度均值
            $simScore = 0;
            if (!empty($normTargetBase) && !empty($normVideoBase)) {
                $sim1 = $this->mbSimilarText($normTargetBase, $normVideoBase);
                $sim2 = $this->jaccardSimilarity($normTargetBase, $normVideoBase);
                $sim3 = $this->levenshteinSimilarity($normTargetBase, $normVideoBase);
                $simScore = ($sim1 + $sim2 + $sim3) / 3;
                $score += $simScore * ($w['title_similarity'] / 100);
                $details['title_similarity'] = round($simScore * ($w['title_similarity'] / 100), 2);
            }

            // 6) 一方为另一方前缀
            if (!empty($normTargetBase) && !empty($normVideoBase)) {
                if (mb_strpos($normVideoBase, $normTargetBase) !== false
                    || mb_strpos($normTargetBase, $normVideoBase) !== false) {
                    $score += $w['title_contains'];
                    $details['title_contains'] = $w['title_contains'];
                }
            }

            // 7) 同义词语义相似度（基于标准化后的标题，避免重复归一化）
            $semanticScore = $this->semanticSimilarity($normTarget, $normVideo);
            $score += $semanticScore * ($w['semantic_similarity'] / 100);
            $details['semantic_similarity'] = round($semanticScore * ($w['semantic_similarity'] / 100), 2);

            // 8) 关键字符命中
            $keywordMatches = $this->keywordCountMatch($normTargetBase, $normVideoBase);
            if ($keywordMatches > 0) {
                $kwScore = min($w['keyword_count'], $keywordMatches * 2);
                $score += $kwScore;
                $details['keyword_count'] = $kwScore;
            }

            // 9) 季数匹配（强信号，不一致时重惩罚）
            $seasonScore = 0;
            if ($targetSeason !== null && $videoSeason !== null) {
                if ($targetSeason == $videoSeason) {
                    $seasonScore = $w['season_match'];
                    $details['season_match'] = $w['season_match'];
                } else {
                    $diff = abs($targetSeason - $videoSeason);
                    $penalty = min($w['season_mismatch'], $diff * 8);
                    $seasonScore = -$penalty;
                    $details['season_mismatch'] = -$penalty;
                }
            } elseif ($targetSeason !== null && $videoSeason === null) {
                if ($targetSeason == 1) {
                    $seasonScore = $w['season_match'] * 0.3;
                    $details['season_assumed_1'] = round($w['season_match'] * 0.3, 2);
                } else {
                    $seasonScore = -$w['season_mismatch'] * 0.3;
                    $details['season_missing'] = round(-$w['season_mismatch'] * 0.3, 2);
                }
            }
            $score += $seasonScore;

            // 10) 集数匹配
            if ($targetEpisode !== null && $videoEpisode !== null) {
                if ($targetEpisode == $videoEpisode) {
                    $score += $w['episode_match'];
                    $details['episode_match'] = $w['episode_match'];
                }
            }

            // 11) 部匹配
            if ($targetPart && $videoPart) {
                if ($targetPart == $videoPart) {
                    $score += $w['part_match'];
                    $details['part_match'] = $w['part_match'];
                }
            }

            // 12) 版本匹配
            if ($targetVersion && $videoVersion) {
                if ($targetVersion == $videoVersion) {
                    $score += $w['version_match'];
                    $details['version_match'] = $w['version_match'];
                }
            }

            // 13) 备注质量信号
            if (!empty($videoRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片|1080P|4K/u', $videoRemarks)) {
                    $score += $w['remarks_quality'];
                    $details['remarks_quality'] = $w['remarks_quality'];
                }
            }

            // 14) 归一化为 0-100 分
            $finalScore = round(($score / $maxScore) * 100, 2);
            $finalScore = min(100, max(0, $finalScore));

            $videoCopy = $video;
            $videoCopy['ai_score']              = $finalScore;
            $videoCopy['ai_details']            = $details;
            $videoCopy['parsed_season']         = $videoParsed['season'];
            $videoCopy['parsed_season_num']     = $videoSeason;
            $videoCopy['parsed_episode']        = $videoParsed['episode'];
            $videoCopy['parsed_episode_num']    = $videoEpisode;
            $videoCopy['parsed_part']           = $videoPart;
            $videoCopy['parsed_version']        = $videoVersion;
            $videoCopy['total_episodes']        = isset($video['urls']) ? count($video['urls']) : 0;
            $videoCopy['first_url']             = $video['first_url'] ?? $video['url'] ?? '';

            $scored[] = [
                'video'         => $videoCopy,
                'score'         => $finalScore,
                'base_score'    => round($simScore, 2),
                'season_match'  => $targetSeason !== null && $videoSeason !== null && $targetSeason == $videoSeason,
                'episode_match' => $targetEpisode !== null && $videoEpisode !== null && $targetEpisode == $videoEpisode,
                'site'          => $video['site'] ?? '',
                'video_season'  => $videoSeason,
                'video_episode' => $videoEpisode,
                'video_part'    => $videoPart,
                'video_version' => $videoVersion,
                'video_name'    => $videoBase,
                'match_details' => $details,
                'match_method'  => 'ai_smart'
            ];
        }

        usort($scored, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        $bestMatch = !empty($scored) && $scored[0]['score'] >= 50 ? $scored[0] : null;

        return [
            'best_match'       => $bestMatch,
            'all_matches'      => $scored,
            'method'           => 'ai_smart_match',
            'total_candidates' => count($candidates),
            'weights_used'     => $this->learnedWeights
        ];
    }

    /**
     * 从匹配结果反馈学习，微调权重
     */
    public function learnFromMatch($videoInfo, $matchedVideo, $isCorrect) {
        $this->matchHistory[] = [
            'time'    => time(),
            'correct' => $isCorrect,
            'target'  => $videoInfo['title'] ?? '',
            'matched' => $matchedVideo['name'] ?? ''
        ];

        if (count($this->matchHistory) > 100) {
            $this->matchHistory = array_slice($this->matchHistory, -50);
        }

        $adjustment = $isCorrect ? 0.5 : -0.3;

        $targetSeason = $videoInfo['season_num'] ?? null;
        $videoSeason  = $matchedVideo['parsed_season_num'] ?? null;
        if ($targetSeason !== null && $videoSeason !== null) {
            if ($targetSeason == $videoSeason && $isCorrect) {
                $this->learnedWeights['season_match'] += $adjustment * 2;
            }
        }

        $this->learnedWeights['title_similarity']    += $adjustment;
        $this->learnedWeights['semantic_similarity'] += $adjustment * 0.5;

        foreach ($this->learnedWeights as $key => $val) {
            if ($val < 1) $this->learnedWeights[$key] = 1;
            if ($val > 60) $this->learnedWeights[$key] = 60;
        }

        $this->saveLearnedWeights();
    }

    // -------------------- 相似度算法 --------------------

    private function mbSimilarText($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;

        $chars1 = $this->mbStrSplit($s1);
        $chars2 = $this->mbStrSplit($s2);
        $len1 = count($chars1);
        $len2 = count($chars2);
        if ($len1 == 0 || $len2 == 0) return 0;

        $intersect = count(array_intersect($chars1, $chars2));
        $union     = count(array_unique(array_merge($chars1, $chars2)));
        $jaccard   = $union > 0 ? ($intersect / $union) * 100 : 0;

        $lcsLen    = $this->lcsLength($chars1, $chars2);
        $lcsPercent = ($lcsLen * 2) / ($len1 + $len2) * 100;

        return ($jaccard + $lcsPercent) / 2;
    }

    private function jaccardSimilarity($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;
        $chars1 = array_unique($this->mbStrSplit($s1));
        $chars2 = array_unique($this->mbStrSplit($s2));
        if (empty($chars1) || empty($chars2)) return 0;
        $intersect = count(array_intersect($chars1, $chars2));
        $union     = count(array_unique(array_merge($chars1, $chars2)));
        return $union > 0 ? ($intersect / $union) * 100 : 0;
    }

    /**
     * 基于 LCS 的相似度（替代原先不准确的"位置 diff"）
     * 使用真正的最长公共子序列长度计算
     */
    private function levenshteinSimilarity($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;
        $chars1 = $this->mbStrSplit($s1);
        $chars2 = $this->mbStrSplit($s2);
        $len1 = count($chars1);
        $len2 = count($chars2);
        if ($len1 == 0 || $len2 == 0) return 0;

        $lcs = $this->lcsLength($chars1, $chars2);
        $maxLen = max($len1, $len2);
        return $maxLen > 0 ? ($lcs / $maxLen) * 100 : 0;
    }

    private function lcsLength($a, $b) {
        $m = count($a);
        $n = count($b);
        if ($m == 0 || $n == 0) return 0;

        $short = $m <= $n ? $a : $b;
        $long  = $m <= $n ? $b : $a;
        $sl = count($short);
        $ll = count($long);

        $prev = array_fill(0, $sl + 1, 0);
        $curr = array_fill(0, $sl + 1, 0);

        for ($i = 1; $i <= $ll; $i++) {
            for ($j = 1; $j <= $sl; $j++) {
                if ($long[$i - 1] == $short[$j - 1]) {
                    $curr[$j] = $prev[$j - 1] + 1;
                } else {
                    $curr[$j] = max($prev[$j], $curr[$j - 1]);
                }
            }
            $temp = $prev;
            $prev = $curr;
            $curr = $temp;
        }
        return $prev[$sl];
    }

    private function mbStrSplit($str) {
        $result = [];
        $len = mb_strlen($str, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $result[] = mb_substr($str, $i, 1, 'UTF-8');
        }
        return $result;
    }

    /**
     * 同义词语义相似度：检测两个标题是否共享同一语义概念
     * 输入应为已标准化的标题
     */
    private function semanticSimilarity($title1, $title2) {
        if (empty($title1) || empty($title2)) return 0;

        $score = 0;
        foreach ($this->synonymDict as $canonical => $synonyms) {
            $has1 = mb_strpos($title1, $canonical) !== false;
            $has2 = mb_strpos($title2, $canonical) !== false;

            if (!$has1 || !$has2) {
                foreach ($synonyms as $syn) {
                    if (!$has1 && mb_strpos($title1, $syn) !== false) $has1 = true;
                    if (!$has2 && mb_strpos($title2, $syn) !== false) $has2 = true;
                    if ($has1 && $has2) break;
                }
            }

            if ($has1 && $has2) {
                $score += 15;
            }
        }
        return min(100, $score);
    }

    private function keywordCountMatch($target, $videoName) {
        if (empty($target) || empty($videoName)) return 0;
        $targetChars = array_unique($this->mbStrSplit($target));
        $videoChars  = $this->mbStrSplit($videoName);
        $matches = 0;
        foreach ($targetChars as $char) {
            if (in_array($char, $videoChars, true)) {
                $matches++;
            }
        }
        return $matches;
    }

    // -------------------- 标题解析 --------------------

    /**
     * 解析标题中的季/集/部/版本信息
     * 季/集解析委托 TitleNormalizer，确保覆盖 S0N/EP\d+/E\d+/罗马数字 等写法
     */
    private function parseTitleInfo($title) {
        $result = [
            'base_title'  => $title,
            'season'      => '',
            'season_num'  => null,
            'episode'     => '',
            'episode_num' => null,
            'part'        => '',
            'version'     => ''
        ];

        if (empty($title)) {
            return $result;
        }

        // 季数（委托 TitleNormalizer）
        $seasonNum = TitleNormalizer::getSeasonInfo($title);
        if ($seasonNum !== null) {
            $result['season_num'] = $seasonNum;
            $result['season']     = '第' . $seasonNum . '季';
        }

        // 集数（委托 TitleNormalizer）
        $episodeNum = TitleNormalizer::getEpisodeInfo($title);
        if ($episodeNum !== null) {
            $result['episode_num'] = $episodeNum;
            $result['episode']     = '第' . $episodeNum . '集';
        }

        // 部（上/中/下/前篇/后篇 等）
        if (preg_match('/上|中|下|前篇|后篇|上篇|下篇|终章|序章/u', $title, $m)) {
            $result['part'] = $m[0];
        }

        // 版本
        if (preg_match('/动画版|动漫版|真人版|电影版|剧场版|番外篇|特别篇|SP|OVA|OAD|TV版|DVD版|网络版|台版|港版|美版|日版|英版|法版|国版/u', $title, $m)) {
            $result['version'] = $m[0];
        }

        // 基础剧名：剥离所有标记后剩余
        $base = $title;
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+季/u', '', $base);
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+集/u', '', $base);
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+部/u', '', $base);
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+卷/u', '', $base);
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+番/u', '', $base);
        $base = preg_replace('/[Ss]\d+[Ee]\d+/', '', $base);
        $base = preg_replace('/\bS\d+\b/i', '', $base);
        $base = preg_replace('/\bEP\d+\b/i', '', $base);
        $base = preg_replace('/\bE\d+\b/i', '', $base);
        $base = preg_replace('/[\(\[（【].*?[\)\]）】]/u', '', $base);
        $base = preg_replace('/上|中|下|前篇|后篇|上篇|下篇|终章|序章/u', '', $base);
        $base = preg_replace('/动画版|动漫版|真人版|电影版|剧场版|番外篇|特别篇|SP|OVA|OAD|TV版|DVD版|网络版|台版|港版|美版|日版|英版|法版|国版/u', '', $base);
        $base = preg_replace('/[\s\-_]+/u', ' ', $base);
        $base = trim($base);
        $result['base_title'] = $base;

        return $result;
    }

    // -------------------- 工具方法 --------------------

    /**
     * 检测是否命中噪声排除模式
     */
    private function isExcluded($title) {
        if (empty($title)) return false;
        foreach (self::$excludePatterns as $pattern) {
            if (preg_match($pattern, $title)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 带缓存的标准化（本次 smartMatch 调用生命周期内有效）
     */
    private function normalizeCached($title) {
        if (empty($title)) return '';
        $key = md5((string)$title);
        if (!isset($this->normCache[$key])) {
            $this->normCache[$key] = TitleNormalizer::normalize($title);
        }
        return $this->normCache[$key];
    }
}
