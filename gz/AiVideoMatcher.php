<?php

class AiVideoMatcher {
    private $learnedWeights = [];
    private $synonymDict = [];
    private $matchHistory = [];

    public function __construct() {
        $this->initSynonymDict();
        $this->loadLearnedWeights();
    }

    private function initSynonymDict() {
        $this->synonymDict = [
            '第一季' => ['第1季', 'S1', 'season1', '第一部', '第1部'],
            '第二季' => ['第2季', 'S2', 'season2', '第二部', '第2部'],
            '第三季' => ['第3季', 'S3', 'season3', '第三部', '第3部'],
            '第四季' => ['第4季', 'S4', 'season4', '第四部', '第4部'],
            '第五季' => ['第5季', 'S5', 'season5', '第五部', '第5部'],
            '动画版' => ['动画', '动漫版', '动漫'],
            '真人版' => ['真人', '电视剧版', '网剧版'],
            '电影版' => ['剧场版', '大电影'],
            '完整版' => ['未删减版', '无删减版', '全集'],
            '番外篇' => ['番外', 'OVA', 'OAD', '特别篇', 'SP'],
            '完美世界' => ['完美世界动画', '完美世界动漫'],
            '遮天' => ['遮天动画', '遮天动漫'],
            '斗破苍穹' => ['斗破', '斗破动画', '斗破动漫'],
            '凡人修仙传' => ['凡人', '凡人动画', '凡人修仙'],
            '斗罗大陆' => ['斗罗', '斗罗动画', '斗罗动漫'],
            '庆余年' => ['庆余年电视剧'],
            '三体' => ['三体动画', '三体电视剧'],
        ];
    }

    private function loadLearnedWeights() {
        $defaultWeights = [
            'title_exact' => 30,
            'title_similarity' => 25,
            'title_contains' => 15,
            'season_match' => 20,
            'episode_match' => 15,
            'part_match' => 10,
            'version_match' => 5,
            'remarks_quality' => 5,
            'keyword_count' => 10,
            'semantic_similarity' => 15
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

    public function smartMatch($videoInfo, $candidates) {
        if (empty($candidates)) {
            return [
                'best_match' => null,
                'all_matches' => [],
                'method' => 'ai_smart_match',
                'total_candidates' => 0
            ];
        }

        $targetTitle = $videoInfo['title'] ?? $videoInfo['base_title'] ?? '';
        $targetSeason = $videoInfo['season_num'] ?? null;
        $targetEpisode = $videoInfo['episode_num'] ?? null;
        $targetPart = $videoInfo['part'] ?? null;
        $targetVersion = $videoInfo['version'] ?? null;
        $targetBaseTitle = $videoInfo['base_title'] ?? $targetTitle;

        $w = $this->learnedWeights;
        $maxScore = array_sum($w);

        $scored = [];
        foreach ($candidates as $index => $video) {
            $score = 0;
            $details = [];

            $videoName = $video['name'] ?? '';
            $videoRemarks = $video['remarks'] ?? '';
            $videoFullName = $videoName . ' ' . $videoRemarks;

            $videoParsed = $this->parseTitleInfo($videoName);
            $videoBaseTitle = $videoParsed['base_title'] ?? $videoName;
            $videoSeason = $videoParsed['season_num'] ?? null;
            $videoEpisode = $videoParsed['episode_num'] ?? null;
            $videoPart = $videoParsed['part'] ?? null;
            $videoVersion = $videoParsed['version'] ?? null;

            $normTarget = $this->normalizeTitle($targetTitle);
            $normVideo = $this->normalizeTitle($videoName);
            $normTargetBase = $this->normalizeTitle($targetBaseTitle);
            $normVideoBase = $this->normalizeTitle($videoBaseTitle);

            if ($normTargetBase === $normVideoBase) {
                $score += $w['title_exact'];
                $details['title_exact'] = $w['title_exact'];
            }

            $simScore = 0;
            if (!empty($normTargetBase) && !empty($normVideoBase)) {
                $sim1 = $this->mbSimilarText($normTargetBase, $normVideoBase);
                $sim2 = $this->jaccardSimilarity($normTargetBase, $normVideoBase);
                $sim3 = $this->levenshteinSimilarity($normTargetBase, $normVideoBase);
                $simScore = ($sim1 + $sim2 + $sim3) / 3;
                $score += $simScore * ($w['title_similarity'] / 100);
                $details['title_similarity'] = round($simScore * ($w['title_similarity'] / 100), 2);
            }

            if (!empty($normTargetBase) && !empty($normVideoBase)) {
                if (mb_strpos($normVideoBase, $normTargetBase) !== false ||
                    mb_strpos($normTargetBase, $normVideoBase) !== false) {
                    $score += $w['title_contains'];
                    $details['title_contains'] = $w['title_contains'];
                }
            }

            $semanticScore = $this->semanticSimilarity($targetTitle, $videoName);
            $score += $semanticScore * ($w['semantic_similarity'] / 100);
            $details['semantic_similarity'] = round($semanticScore * ($w['semantic_similarity'] / 100), 2);

            $keywordMatches = $this->keywordCountMatch($targetTitle, $videoFullName);
            if ($keywordMatches > 0) {
                $kwScore = min($w['keyword_count'], $keywordMatches * 3);
                $score += $kwScore;
                $details['keyword_count'] = $kwScore;
            }

            $seasonMatchScore = 0;
            if ($targetSeason !== null && $videoSeason !== null) {
                if ($targetSeason == $videoSeason) {
                    $seasonMatchScore = $w['season_match'];
                    $details['season_match'] = $w['season_match'];
                } else {
                    $seasonDiff = abs($targetSeason - $videoSeason);
                    $penalty = min($w['season_match'], $seasonDiff * 5);
                    $seasonMatchScore = -$penalty;
                    $details['season_mismatch'] = -$penalty;
                }
            } elseif ($targetSeason !== null && $videoSeason === null) {
                if ($targetSeason == 1) {
                    $seasonMatchScore = $w['season_match'] * 0.3;
                    $details['season_assumed_1'] = round($w['season_match'] * 0.3, 2);
                } else {
                    $seasonMatchScore = -$w['season_match'] * 0.2;
                    $details['season_missing'] = round(-$w['season_match'] * 0.2, 2);
                }
            }
            $score += max(-$w['season_match'], $seasonMatchScore);

            if ($targetEpisode !== null && $videoEpisode !== null) {
                if ($targetEpisode == $videoEpisode) {
                    $score += $w['episode_match'];
                    $details['episode_match'] = $w['episode_match'];
                }
            }

            if ($targetPart && $videoPart) {
                if ($targetPart == $videoPart) {
                    $score += $w['part_match'];
                    $details['part_match'] = $w['part_match'];
                }
            }

            if ($targetVersion && $videoVersion) {
                if ($targetVersion == $videoVersion) {
                    $score += $w['version_match'];
                    $details['version_match'] = $w['version_match'];
                }
            }

            if (!empty($videoRemarks)) {
                if (preg_match('/更新至|连载|全\d+集|共\d+集|已完结|HD|高清|正片|1080P|4K/u', $videoRemarks)) {
                    $score += $w['remarks_quality'];
                    $details['remarks_quality'] = $w['remarks_quality'];
                }
            }

            $finalScore = round(($score / $maxScore) * 100, 2);
            $finalScore = min(100, max(0, $finalScore));

            $videoCopy = $video;
            $videoCopy['ai_score'] = $finalScore;
            $videoCopy['ai_details'] = $details;
            $videoCopy['parsed_season'] = $videoSeason;
            $videoCopy['parsed_season_num'] = $videoSeason;
            $videoCopy['parsed_episode'] = $videoParsed['episode'] ?? '';
            $videoCopy['parsed_episode_num'] = $videoEpisode;
            $videoCopy['parsed_part'] = $videoPart;
            $videoCopy['parsed_version'] = $videoVersion;
            $videoCopy['total_episodes'] = isset($video['urls']) ? count($video['urls']) : 0;
            $videoCopy['first_url'] = $video['first_url'] ?? $video['url'] ?? '';

            $scored[] = [
                'video' => $videoCopy,
                'score' => $finalScore,
                'base_score' => round($simScore, 2),
                'season_match' => $targetSeason !== null && $videoSeason !== null && $targetSeason == $videoSeason,
                'episode_match' => $targetEpisode !== null && $videoEpisode !== null && $targetEpisode == $videoEpisode,
                'site' => $video['site'] ?? '',
                'video_season' => $videoSeason,
                'video_episode' => $videoEpisode,
                'video_part' => $videoPart,
                'video_version' => $videoVersion,
                'video_name' => $videoBaseTitle,
                'match_details' => $details,
                'match_method' => 'ai_smart'
            ];
        }

        usort($scored, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        $bestMatch = !empty($scored) && $scored[0]['score'] >= 50 ? $scored[0] : null;

        return [
            'best_match' => $bestMatch,
            'all_matches' => $scored,
            'method' => 'ai_smart_match',
            'total_candidates' => count($candidates),
            'weights_used' => $this->learnedWeights
        ];
    }

    public function learnFromMatch($videoInfo, $matchedVideo, $isCorrect) {
        $this->matchHistory[] = [
            'time' => time(),
            'correct' => $isCorrect,
            'target' => $videoInfo['title'] ?? '',
            'matched' => $matchedVideo['name'] ?? ''
        ];

        if (count($this->matchHistory) > 100) {
            $this->matchHistory = array_slice($this->matchHistory, -50);
        }

        $adjustment = $isCorrect ? 0.5 : -0.3;

        $targetSeason = $videoInfo['season_num'] ?? null;
        $videoSeason = $matchedVideo['parsed_season_num'] ?? null;
        if ($targetSeason !== null && $videoSeason !== null) {
            if ($targetSeason == $videoSeason && $isCorrect) {
                $this->learnedWeights['season_match'] += $adjustment * 2;
            }
        }

        $this->learnedWeights['title_similarity'] += $adjustment;
        $this->learnedWeights['semantic_similarity'] += $adjustment * 0.5;

        foreach ($this->learnedWeights as $key => $val) {
            if ($val < 1) $this->learnedWeights[$key] = 1;
            if ($val > 50) $this->learnedWeights[$key] = 50;
        }

        $this->saveLearnedWeights();
    }

    private function mbSimilarText($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;

        $chars1 = $this->mbStrSplit($s1);
        $chars2 = $this->mbStrSplit($s2);

        $len1 = count($chars1);
        $len2 = count($chars2);
        if ($len1 == 0 || $len2 == 0) return 0;

        $intersect = count(array_intersect($chars1, $chars2));
        $union = count(array_unique(array_merge($chars1, $chars2)));

        $jaccard = $union > 0 ? ($intersect / $union) * 100 : 0;

        $lcsLen = $this->lcsLength($chars1, $chars2);
        $lcsPercent = ($lcsLen * 2) / ($len1 + $len2) * 100;

        return ($jaccard + $lcsPercent) / 2;
    }

    private function jaccardSimilarity($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;

        $chars1 = array_unique($this->mbStrSplit($s1));
        $chars2 = array_unique($this->mbStrSplit($s2));

        if (empty($chars1) || empty($chars2)) return 0;

        $intersect = count(array_intersect($chars1, $chars2));
        $union = count(array_unique(array_merge($chars1, $chars2)));

        return $union > 0 ? ($intersect / $union) * 100 : 0;
    }

    private function levenshteinSimilarity($s1, $s2) {
        if (empty($s1) || empty($s2)) return 0;

        $chars1 = $this->mbStrSplit($s1);
        $chars2 = $this->mbStrSplit($s2);
        $len1 = count($chars1);
        $len2 = count($chars2);

        if ($len1 == 0) return 0;
        if ($len2 == 0) return 0;

        $maxLen = max($len1, $len2);

        $distance = 0;
        $shorter = $len1 < $len2 ? $chars1 : $chars2;
        $longer = $len1 < $len2 ? $chars2 : $chars1;

        foreach ($shorter as $i => $char) {
            if ($i >= count($longer) || $char != $longer[$i]) {
                $distance++;
            }
        }
        $distance += abs($len1 - $len2);

        return $maxLen > 0 ? max(0, (1 - $distance / $maxLen) * 100) : 0;
    }

    private function lcsLength($a, $b) {
        $m = count($a);
        $n = count($b);
        if ($m == 0 || $n == 0) return 0;

        $short = $m <= $n ? $a : $b;
        $long = $m <= $n ? $b : $a;
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

    private function semanticSimilarity($title1, $title2) {
        if (empty($title1) || empty($title2)) return 0;

        $score = 0;
        $norm1 = $this->normalizeTitle($title1);
        $norm2 = $this->normalizeTitle($title2);

        foreach ($this->synonymDict as $canonical => $synonyms) {
            $has1 = mb_strpos($norm1, $canonical) !== false;
            $has2 = mb_strpos($norm2, $canonical) !== false;

            if (!$has1 && !$has2) {
                foreach ($synonyms as $syn) {
                    if (mb_strpos($norm1, $syn) !== false) {
                        $has1 = true;
                    }
                    if (mb_strpos($norm2, $syn) !== false) {
                        $has2 = true;
                    }
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

        $normTarget = $this->normalizeTitle($target);
        $normVideo = $this->normalizeTitle($videoName);

        $targetChars = array_unique($this->mbStrSplit($normTarget));
        $videoChars = $this->mbStrSplit($normVideo);

        $matches = 0;
        foreach ($targetChars as $char) {
            if (in_array($char, $videoChars)) {
                $matches++;
            }
        }

        return $matches;
    }

    private function normalizeTitle($title) {
        if (empty($title)) return '';

        $title = trim($title);
        $title = preg_replace('/[\s\-_]+/u', '', $title);
        $title = preg_replace('/第[一二三四五六七八九十百千0-9]+[集季部期話话]/u', '', $title);
        $title = preg_replace('/[S|s]\d+[E|e]\d+/', '', $title);
        $title = preg_replace('/[\(\[（【].*?[\)\]）】]/u', '', $title);
        $title = trim($title);

        return $title;
    }

    private function parseTitleInfo($title) {
        $result = [
            'base_title' => $title,
            'season' => '',
            'season_num' => null,
            'episode' => '',
            'episode_num' => null,
            'part' => '',
            'version' => ''
        ];

        if (empty($title)) {
            return $result;
        }

        if (preg_match('/第([一二三四五六七八九十百千0-9]+)季/u', $title, $m)) {
            $result['season'] = $m[0];
            $result['season_num'] = $this->cn2num($m[1]);
        }
        if (preg_match('/第([一二三四五六七八九十百千0-9]+)集/u', $title, $m)) {
            $result['episode'] = $m[0];
            $result['episode_num'] = $this->cn2num($m[1]);
        }
        if (preg_match('/[Ss](\d+)[Ee](\d+)/', $title, $m)) {
            $result['season_num'] = intval($m[1]);
            $result['episode_num'] = intval($m[2]);
            $result['season'] = '第' . $m[1] . '季';
            $result['episode'] = '第' . $m[2] . '集';
        }
        if (preg_match('/(\d+)集全|全(\d+)集/u', $title, $m)) {
            $num = isset($m[1]) ? intval($m[1]) : intval($m[2]);
            if ($result['episode_num'] === null) {
                $result['episode_num'] = $num;
                $result['episode'] = '第' . $num . '集';
            }
        }
        if (preg_match('/上|中|下|前篇|后篇|上篇|下篇|终章|序章/u', $title, $m)) {
            $result['part'] = $m[0];
        }
        if (preg_match('/动画版|动漫版|真人版|电影版|剧场版|番外篇|特别篇|SP|OVA|OAD|TV版|网络版|台版|港版|美版|日版/u', $title, $m)) {
            $result['version'] = $m[0];
        }

        $base = $title;
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+季/u', '', $base);
        $base = preg_replace('/第[一二三四五六七八九十百千0-9]+集/u', '', $base);
        $base = preg_replace('/[Ss]\d+[Ee]\d+/', '', $base);
        $base = preg_replace('/[\(\[（【].*?[\)\]）】]/u', '', $base);
        $base = preg_replace('/上|中|下|前篇|后篇|上篇|下篇|终章|序章/u', '', $base);
        $base = preg_replace('/动画版|动漫版|真人版|电影版|剧场版|番外篇|特别篇|SP|OVA|OAD|TV版|网络版|台版|港版|美版|日版/u', '', $base);
        $base = preg_replace('/[\s\-_]+/u', ' ', $base);
        $base = trim($base);
        $result['base_title'] = $base;

        return $result;
    }

    private function cn2num($cn) {
        if (is_numeric($cn)) {
            return intval($cn);
        }

        $cnDigits = [
            '零' => 0, '一' => 1, '二' => 2, '两' => 2, '三' => 3,
            '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8, '九' => 9
        ];
        $cnUnits = ['十' => 10, '百' => 100, '千' => 1000];

        $result = 0;
        $temp = 0;
        $chars = preg_split('//u', $cn, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $char) {
            if (isset($cnDigits[$char])) {
                $temp = $cnDigits[$char];
            } elseif (isset($cnUnits[$char])) {
                $unit = $cnUnits[$char];
                if ($temp == 0 && $unit == 10) {
                    $temp = 1;
                }
                $result += $temp * $unit;
                $temp = 0;
            }
        }
        $result += $temp;

        return $result > 0 ? $result : null;
    }
}
