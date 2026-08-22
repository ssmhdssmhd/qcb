<?php
/**
 * M3u8UrlHelper —— M3U8 地址处理（自包含，不依赖 mx.php 内部函数）
 *
 * 提供：
 *   ① resolveMasterPlaylist()  解析主播放列表并取第一条分片变体
 *   ② parseUrlParts()          拆分 scheme/host/port/目录
 *   ③ rewriteRelativeUrls()    将 m3u8 内相对地址补全为绝对地址
 *
 * @package handlers
 * @since   5.14.0
 */
class M3u8UrlHelper {

    /**
     * 若输入是 Master 播放列表，则解析并返回第一条分片变体地址；
     * 否则原样返回。
     *
     * @param string $url
     * @param string $proxy 可选代理
     * @return string
     */
    public static function resolveMasterPlaylist($url, $proxy = '') {
        $parser = new M3U8Parser();
        if ($proxy) {
            $parser->setForceProxy($proxy);
        }
        try {
            $playlist = $parser->parse($url);
            if (!empty($playlist['isMaster']) && !empty($playlist['variants'])) {
                $firstVariant = $playlist['variants'][0]['uri'] ?? '';
                if ($firstVariant) {
                    $parts = self::parseUrlParts($url);
                    if (strpos($firstVariant, '/') === 0) {
                        return $parts['base'] . $firstVariant;
                    }
                    return $parts['base'] . $parts['path_dir'] . '/' . $firstVariant;
                }
            }
        } catch (Exception $e) {
            // 解析失败按原样返回
        }
        return $url;
    }

    /**
     * 拆分 URL 各部分（含目录，用于相对地址补全）
     *
     * @param string $url
     * @return array{base:string,scheme:string,host:string,port:string,path_dir:string}
     */
    public static function parseUrlParts($url) {
        $parsed = parse_url($url);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? '';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';
        $base = $scheme . '://' . $host . $port;
        $path = $parsed['path'] ?? '';
        $pathDir = dirname($path);
        $pathDir = ($pathDir === '.' || $pathDir === '/') ? '' : $pathDir;
        return [
            'base'     => $base,
            'scheme'   => $scheme,
            'host'     => $host,
            'port'     => $port,
            'path_dir' => $pathDir,
        ];
    }

    /**
     * 将 m3u8 文本中的相对 URI 补全为绝对地址（不处理 # 开头的标签行与绝对 http 行）
     *
     * @param string $content
     * @param string $sourceUrl 该 m3u8 的来源 URL（用于推导目录）
     * @return string
     */
    public static function rewriteRelativeUrls($content, $sourceUrl) {
        $parts = self::parseUrlParts($sourceUrl);
        $base = $parts['base'];
        $pathDir = $parts['path_dir'];

        $lines = explode("\n", $content);
        $newLines = [];
        foreach ($lines as $line) {
            if (!empty(trim($line)) &&
                strpos($line, '#') !== 0 &&
                strpos($line, 'http://') !== 0 &&
                strpos($line, 'https://') !== 0) {
                if ($pathDir === '' || $pathDir === '/') {
                    $line = $base . '/' . ltrim($line, '/');
                } else {
                    $line = $base . $pathDir . '/' . ltrim($line, '/');
                }
            }
            $newLines[] = $line;
        }
        return implode("\n", $newLines);
    }

    /**
     * 从 m3u8 内容中移除指定的广告段 URI（EXTINF 与其数据行成对删除）
     *
     * @param string        $content
     * @param array<string> $adUris 待移除的 URI 集合
     * @return string
     */
    public static function removeSegments($content, array $adUris) {
        if (empty($adUris)) {
            return $content;
        }
        $lines = explode("\n", $content);
        $newLines = [];
        $lineCount = count($lines);
        for ($li = 0; $li < $lineCount; $li++) {
            $line = $lines[$li];
            $trimmed = trim($line);
            if (strpos($trimmed, '#EXTINF:') === 0) {
                $nextLine = ($li + 1 < $lineCount) ? trim($lines[$li + 1]) : null;
                if ($nextLine && isset($adUris[$nextLine])) {
                    continue;
                }
            }
            if (isset($adUris[$trimmed])) {
                continue;
            }
            $newLines[] = $line;
        }
        return implode("\n", $newLines);
    }
}
