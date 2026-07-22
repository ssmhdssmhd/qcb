<?php

require_once __DIR__ . '/synonym_config.php';

/**
 * 视频标题标准化器
 *
 * 基于 gz/synonym_config.php 配置驱动的同义词归一化：
 *   symbol → season → episode → quality → language → version → region → 清理空白
 *
 * 每个类别使用「单遍最长匹配」正则替换（preg_replace_callback），
 * 不会对替换后的文本再次扫描，避免 'tv'→'TV版' 然后又被 'TV' 命中的链式副作用。
 *
 * 公共 API（向后兼容）：
 *   - normalize($title)      标准化标题
 *   - getBaseTitle($title)   获取基础剧名（剥离季/集/画质等后缀）
 *   - getSeasonInfo($title)  解析季数（返回 int 或 null）
 *   - getEpisodeInfo($title) 解析集数（返回 int 或 null，新增）
 */
class TitleNormalizer {
    private static $config = null;
    private static $compiled = [];     // category => ['pattern' => regex, 'map' => [...]]
    private static $normalizeCache = []; // md5(title) => normalized，避免重复计算

    /**
     * 标准化标题：依次应用 symbol/season/episode/quality/language/version/region 同义词表
     */
    public static function normalize($title) {
        if (empty($title)) return '';
        $title = (string)$title;
        if (trim($title) === '') return '';

        $cacheKey = md5($title);
        if (isset(self::$normalizeCache[$cacheKey])) {
            return self::$normalizeCache[$cacheKey];
        }

        self::loadConfig();

        $title = trim($title);
        $title = self::applyMap($title, 'symbol');
        $title = self::applyMap($title, 'season');
        $title = self::applyMap($title, 'episode');
        $title = self::applyMap($title, 'quality');
        $title = self::applyMap($title, 'language');
        $title = self::applyMap($title, 'version');
        $title = self::applyMap($title, 'region');

        // 收尾：折叠空白
        $title = preg_replace('/\s+/u', ' ', $title);
        $title = trim($title);

        self::$normalizeCache[$cacheKey] = $title;
        return $title;
    }

    /**
     * 别名：语义化命名
     */
    public static function canonicalize($title) {
        return self::normalize($title);
    }

    /**
     * 获取基础剧名：在标准化基础上剥离残留的季/集/部/篇/画质等后缀
     */
    public static function getBaseTitle($title) {
        $normalized = self::normalize($title);
        $normalized = preg_replace('/\s*(第[一二三四五六七八九十百千\d]+[季部期篇辑卷]|[上下]部|[全]集|合集|完整版|高清|4K|2K|1080P|720P|蓝光|HD|BD|WEB|S\d+|EP?\d+)\s*$/iu', '', $normalized);
        $normalized = preg_replace('/^\s*(第[一二三四五六七八九十百千\d]+[季部期篇辑卷]|[上下]部)\s+/iu', '', $normalized);
        $normalized = trim($normalized);
        return $normalized;
    }

    /**
     * 解析季数（兼容多种写法）
     * 支持：第N季/第N部/第N卷/第N番/S\d+/Ⅰ-Ⅸ
     */
    public static function getSeasonInfo($title) {
        if (empty($title)) return null;
        $title = (string)$title;

        // 第N季/部/卷/番（数字或中文）
        if (preg_match('/第([0-9]+|[一二三四五六七八九十百千]+)[季部期篇辑卷番]/u', $title, $m)) {
            $num = self::cn2num($m[1]);
            if ($num > 0) return $num;
        }
        // S\d+ / Season \d+
        if (preg_match('/\bS(\d+)\b/i', $title, $m) || preg_match('/Season\s*(\d+)/i', $title, $m)) {
            return intval($m[1]);
        }
        // 罗马数字 Ⅰ-Ⅸ（独立出现）
        $romanMap = ['Ⅰ' => 1, 'II' => 2, 'Ⅲ' => 3, 'Ⅳ' => 4, 'Ⅴ' => 5,
                     'Ⅵ' => 6, 'Ⅶ' => 7, 'Ⅷ' => 8, 'Ⅸ' => 9];
        foreach ($romanMap as $roman => $num) {
            if (mb_strpos($title, $roman) !== false) return $num;
        }
        return null;
    }

    /**
     * 解析集数
     * 支持：第N集/N集/EP\d+/E\d+/S\d+E\d+
     */
    public static function getEpisodeInfo($title) {
        if (empty($title)) return null;
        $title = (string)$title;

        if (preg_match('/第([0-9]+|[一二三四五六七八九十百千]+)集/u', $title, $m)) {
            $num = self::cn2num($m[1]);
            if ($num > 0) return $num;
        }
        if (preg_match('/\bS\d+E(\d+)\b/i', $title, $m) || preg_match('/\bS\d+e(\d+)\b/', $title, $m)) {
            return intval($m[1]);
        }
        if (preg_match('/\bEP(\d+)\b/i', $title, $m)) {
            return intval($m[1]);
        }
        if (preg_match('/\bE(\d+)\b/i', $title, $m)) {
            return intval($m[1]);
        }
        if (preg_match('/(\d+)集/u', $title, $m)) {
            return intval($m[1]);
        }
        return null;
    }

    /**
     * 清空内部缓存（长生命周期进程或测试场景可调用）
     */
    public static function clearCache() {
        self::$normalizeCache = [];
    }

    // -------------------- 内部实现 --------------------

    private static function loadConfig() {
        if (self::$config === null) {
            $loaded = require __DIR__ . '/synonym_config.php';
            if (!is_array($loaded)) {
                $loaded = [];
            }
            self::$config = $loaded;
        }
    }

    /**
     * 单遍最长匹配替换：构建 alternation 正则，preg_replace_callback 一次性扫描
     * 不会对替换结果再次扫描，避免链式副作用
     */
    private static function applyMap($title, $category) {
        if (empty($title)) return $title;
        if (!isset(self::$config[$category]) || empty(self::$config[$category])) {
            return $title;
        }

        if (!isset(self::$compiled[$category])) {
            $map = self::$config[$category];
            $keys = array_keys($map);

            // 按长度降序排序，保证最长匹配优先（alternation 顺序敏感）
            usort($keys, function($a, $b) {
                $la = mb_strlen($a, 'UTF-8');
                $lb = mb_strlen($b, 'UTF-8');
                if ($la === $lb) return strcmp($b, $a);
                return $lb - $la;
            });

            $parts = [];
            foreach ($keys as $k) {
                $parts[] = preg_quote($k, '/');
            }
            $pattern = '/' . implode('|', $parts) . '/u';
            self::$compiled[$category] = [
                'pattern' => $pattern,
                'map'     => $map,
            ];
        }

        $compiled = self::$compiled[$category];
        $map = $compiled['map'];

        return preg_replace_callback($compiled['pattern'], function($m) use ($map) {
            return isset($map[$m[0]]) ? $map[$m[0]] : $m[0];
        }, $title);
    }

    /**
     * 中文/阿拉伯数字互转（兼容阿拉伯输入直接返回）
     */
    private static function cn2num($cn) {
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
        return $result > 0 ? $result : 0;
    }
}
