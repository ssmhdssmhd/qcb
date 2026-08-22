<?php
/**
 * TitleExtractor —— 从视频 URL 推断「剧名 / 集数」
 *
 * 从 parse_internal_moxi 内联闭包抽离而来，供 moxi / parse 等 handler 复用。
 * 逻辑保持一致：优先文件名 → 目录名兜底 → host 兜底。
 *
 * @package handlers
 * @since   5.14.0
 */
class TitleExtractor {

    /** 忽略的目录关键词 */
    const IGNORE_DIRS = ['video', 'videos', 'm3u8', 'movie', 'tv', 'play', 'player'];

    /**
     * 从 URL 提取剧名
     *
     * @param string $url
     * @return string
     */
    public static function title($url) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $host = $parsed['host'] ?? '';
        if (empty($path)) {
            return $host ?: '在线视频';
        }
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        if (empty($pathParts)) {
            return $host ?: '在线视频';
        }

        $fileName = end($pathParts);
        $fileNameWithoutExt = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $fileName);

        $isEpisodeLike = false;
        if (preg_match('/第?\d+[集期话]/u', $fileNameWithoutExt)) $isEpisodeLike = true;
        if (preg_match('/^(episode|ep|e|集|期|话)[_\-]?\d+$/i', $fileNameWithoutExt)) $isEpisodeLike = true;
        if (preg_match('/^\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 4) $isEpisodeLike = true;
        if (preg_match('/[_\-]\d+$/', $fileNameWithoutExt) && strlen($fileNameWithoutExt) <= 15) {
            $prefix = preg_replace('/[_\-]\d+$/', '', $fileNameWithoutExt);
            if (in_array(strtolower($prefix), ['episode', 'ep', 'e', '第', '集', ''])) $isEpisodeLike = true;
        }

        if ($isEpisodeLike || $fileName === 'index.m3u8' || $fileNameWithoutExt === 'index') {
            $candidates = [];
            $dirParts = array_slice($pathParts, 0, -1);
            foreach (array_reverse($dirParts) as $part) {
                if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                if (is_numeric($part)) continue;
                if (strlen($part) < 2) continue;
                if (in_array(strtolower($part), self::IGNORE_DIRS)) continue;
                $candidates[] = $part;
            }
            if (!empty($candidates)) {
                $title = trim(preg_replace('/[_-]+/', ' ', $candidates[0]));
                if (!empty($title)) {
                    if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
                    return $title;
                }
            }
            return $host ?: '在线视频';
        }

        $title = preg_replace('/[_-]+/', ' ', $fileNameWithoutExt);
        $title = preg_replace('/\s*\d+\s*$/', '', $title);
        $title = trim($title);
        if (empty($title) || strlen($title) < 2) {
            $dirParts = array_slice($pathParts, 0, -1);
            foreach (array_reverse($dirParts) as $part) {
                if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
                if (is_numeric($part)) continue;
                if (strlen($part) < 2) continue;
                if (in_array(strtolower($part), self::IGNORE_DIRS)) continue;
                $title = trim(preg_replace('/[_-]+/', ' ', $part));
                if (!empty($title)) {
                    if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
                    return $title;
                }
            }
            return $host ?: '在线视频';
        }
        if (preg_match('/^[a-z\s]+$/i', $title)) return ucwords($title);
        return $title;
    }

    /**
     * 从 URL 提取集数（如 第3集 / 正片）
     *
     * @param string $url
     * @return string
     */
    public static function episode($url) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        if (empty($path)) return '正片';
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        foreach (array_reverse($pathParts) as $part) {
            $part = preg_replace('/\.(m3u8|mp4|mkv|avi|mov|flv|ts|html?)$/i', '', $part);
            if (preg_match('/第(\d+)[集期话]/u', $part, $m)) return '第' . $m[1] . '集';
            if (preg_match('/(?:episode|ep|e)[_\-]?(\d+)/i', $part, $m)) return '第' . intval($m[1]) . '集';
            if (preg_match('/^(\d+)$/', $part, $m)) {
                $num = intval($m[1]);
                if ($num > 0 && $num < 1000) return '第' . $num . '集';
            }
            if (preg_match('/[_\-](\d+)$/', $part, $m)) {
                $num = intval($m[1]);
                if ($num > 0 && $num < 1000) {
                    $prefix = preg_replace('/[_\-]\d+$/', '', $part);
                    if (empty($prefix) || in_array(strtolower($prefix), ['episode', 'ep', 'e'])) return '第' . $num . '集';
                }
            }
        }
        return '正片';
    }

    /**
     * 从 URL 路径中提取「搜索关键词」（用于资源站标题匹配）
     *
     * @param string $url
     * @return string
     */
    public static function searchKeyword($url) {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        $pathParts = array_values(array_filter(explode('/', $path), function($v) { return !empty($v); }));
        foreach ($pathParts as $part) {
            if (preg_match('/\.(m3u8|mp4|mkv|avi|mov|flv|ts)$/i', $part)) continue;
            if (preg_match('/^[a-f0-9]{8,}$/i', $part)) continue;
            if (is_numeric($part)) continue;
            if (strlen($part) < 3) continue;
            if ($part === 'video' || $part === 'videos' || $part === 'm3u8') continue;
            return trim(preg_replace('/[_-]+/', ' ', $part));
        }
        return '';
    }
}
