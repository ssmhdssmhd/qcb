<?php
/**
 * 缓存型 M3U8 解析器
 * 专门解析带 vkey 鉴权参数的缓存型 M3U8 链接
 * 例如: https://cache.0567890.xyz:4433/Cache/qq/xxx.m3u8?vkey=xxx
 *
 * 功能:
 * 1. 代理请求原始 M3U8 内容（带防盗链头）
 * 2. 重写分片 URL 为绝对路径或代理链接
 * 3. 支持多级 M3U8（master/playlist）
 * 4. 输出可直接播放的 M3U8
 */

class CacheM3u8Parser
{
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    private $timeout = 15;
    private $connectTimeout = 10;

    /**
     * 解析缓存型 M3U8 链接
     *
     * @param string $url 原始 M3U8 URL
     * @param string|null $proxyBase 代理基础URL（用于重写分片），为空则用绝对路径
     * @return array ['success' => bool, 'm3u8' => string, 'segments' => int, 'message' => string]
     */
    public function parse($url, $proxyBase = null)
    {
        $url = trim($url);
        if (empty($url)) {
            return ['success' => false, 'message' => 'URL不能为空'];
        }

        // 验证是否为 M3U8 链接
        if (stripos($url, '.m3u8') === false && stripos($url, 'm3u8') === false) {
            return ['success' => false, 'message' => '不是 M3U8 链接'];
        }

        // 解析 URL 结构
        $urlParts = parse_url($url);
        if (!$urlParts || empty($urlParts['host'])) {
            return ['success' => false, 'message' => 'URL格式无效'];
        }

        $scheme = $urlParts['scheme'] ?? 'https';
        $host = $urlParts['host'];
        $port = isset($urlParts['port']) ? ':' . $urlParts['port'] : '';
        $path = $urlParts['path'] ?? '/';
        $query = isset($urlParts['query']) ? '?' . $urlParts['query'] : '';
        $baseUrl = $scheme . '://' . $host . $port;

        // 请求原始 M3U8
        $rawContent = $this->httpGet($url);
        if ($rawContent === false || empty($rawContent)) {
            return ['success' => false, 'message' => '无法获取M3U8内容，可能链接已失效或需要特定请求头'];
        }

        // 验证是否为有效 M3U8
        if (strpos($rawContent, '#EXTM3U') === false) {
            return ['success' => false, 'message' => '返回内容不是有效的M3U8格式', 'raw_preview' => mb_substr($rawContent, 0, 200)];
        }

        // 判断是 master playlist 还是 media playlist
        $isMaster = strpos($rawContent, '#EXT-X-STREAM-INF') !== false;

        // 重写 M3U8 内容
        $rewritten = $this->rewriteM3u8($rawContent, $baseUrl, $path, $query, $proxyBase);

        return [
            'success' => true,
            'm3u8' => $rewritten['content'],
            'segments' => $rewritten['segments'],
            'is_master' => $isMaster,
            'original_url' => $url,
            'base_url' => $baseUrl,
            'message' => $isMaster ? 'Master playlist 解析成功' : 'Media playlist 解析成功，共 ' . $rewritten['segments'] . ' 个分片',
        ];
    }

    /**
     * 重写 M3U8 内容，将分片 URL 转为绝对路径或代理链接
     */
    private function rewriteM3u8($content, $baseUrl, $basePath, $query, $proxyBase)
    {
        $lines = explode("\n", $content);
        $segments = 0;
        $pathDir = dirname($basePath);

        foreach ($lines as $i => &$line) {
            $line = trim($line);

            // 空行或注释行跳过
            if (empty($line) || $line[0] === '#') {
                // 处理 #EXT-X-KEY 中的 URI
                if (strpos($line, '#EXT-X-KEY') !== false && strpos($line, 'URI=') !== false) {
                    $line = $this->rewriteKeyUri($line, $baseUrl, $pathDir, $query, $proxyBase);
                }
                continue;
            }

            // 这是分片或子 playlist 的 URL 行
            $segments++;
            $line = $this->rewriteUrl($line, $baseUrl, $pathDir, $query, $proxyBase);
        }
        unset($line);

        // 重新组装，确保以换行结尾
        $result = implode("\n", $lines);
        if (substr($result, -1) !== "\n") {
            $result .= "\n";
        }

        return ['content' => $result, 'segments' => $segments];
    }

    /**
     * 重写分片 URL
     */
    private function rewriteUrl($url, $baseUrl, $pathDir, $query, $proxyBase)
    {
        $url = trim($url);

        // 已经是绝对 URL（http/https 开头）
        if (preg_match('#^https?://#i', $url)) {
            // 如果有代理基础URL，重写为代理链接
            if ($proxyBase) {
                return $proxyBase . '?ts=' . urlencode($url);
            }
            return $url;
        }

        // data URI 直接返回
        if (strpos($url, 'data:') === 0) {
            return $url;
        }

        // 相对路径处理
        $absoluteUrl = '';
        if (strpos($url, '//') === 0) {
            // 协议相对 URL
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
            $absoluteUrl = $scheme . ':' . $url;
        } elseif (strpos($url, '/') === 0) {
            // 根路径相对 URL
            $absoluteUrl = $baseUrl . $url;
        } else {
            // 相对于当前目录的 URL
            $absoluteUrl = $baseUrl . $pathDir . '/' . $url;
        }

        // 如果原始 M3U8 有 vkey 等查询参数，分片可能也需要
        // 但大多数缓存型 M3U8 的分片不需要额外参数，这里不自动追加

        // 如果有代理基础URL，重写为代理链接
        if ($proxyBase) {
            return $proxyBase . '?ts=' . urlencode($absoluteUrl);
        }

        return $absoluteUrl;
    }

    /**
     * 重写 #EXT-X-KEY 中的 URI
     */
    private function rewriteKeyUri($line, $baseUrl, $pathDir, $query, $proxyBase)
    {
        if (preg_match('/URI="([^"]+)"/', $line, $matches)) {
            $keyUri = $matches[1];
            $newUri = $this->rewriteUrl($keyUri, $baseUrl, $pathDir, $query, $proxyBase);
            $line = str_replace('URI="' . $keyUri . '"', 'URI="' . $newUri . '"', $line);
        }
        return $line;
    }

    /**
     * HTTP GET 请求
     */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . $this->userAgent,
            'Accept: */*',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
            'Connection: keep-alive',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return false;
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            return false;
        }

        return $response;
    }

    /**
     * 代理 TS 分片请求（用于防盗链场景）
     *
     * @param string $url 分片 URL
     */
    public function proxyTs($url)
    {
        $url = trim($url);
        if (empty($url)) {
            http_response_code(400);
            echo 'URL不能为空';
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: ' . $this->userAgent,
            'Accept: */*',
            'Referer: ' . dirname($url) . '/',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            http_response_code($httpCode ?: 502);
            echo '代理请求失败: HTTP ' . $httpCode;
            return;
        }

        // 设置适当的内容类型
        if (empty($contentType)) {
            $contentType = 'video/mp2t';
        }
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . strlen($response));
        header('Cache-Control: public, max-age=3600');
        header('Access-Control-Allow-Origin: *');

        echo $response;
    }

    /**
     * 分析 vkey 参数（仅分析不破解）
     */
    public function analyzeVkey($vkey)
    {
        $result = [
            'raw' => $vkey,
            'length' => strlen($vkey),
            'is_hex' => false,
            'hex_decoded' => null,
            'looks_like_base64' => false,
        ];

        // 检查是否为十六进制
        if (ctype_xdigit($vkey) && strlen($vkey) % 2 === 0) {
            $result['is_hex'] = true;
            $decoded = @hex2bin($vkey);
            if ($decoded !== false) {
                $result['hex_decoded'] = $decoded;
                $result['looks_like_base64'] = preg_match('#^[A-Za-z0-9+/=]+$#', $decoded) === 1;
            }
        }

        return $result;
    }

    /**
     * 检测 URL 是否为缓存型 M3U8
     */
    public static function isCacheM3u8($url)
    {
        // 缓存型特征：路径包含 /Cache/ 或 host 包含 cache
        $urlParts = parse_url($url);
        $host = strtolower($urlParts['host'] ?? '');
        $path = $urlParts['path'] ?? '';
        $query = $urlParts['query'] ?? '';

        // host 包含 cache 且有 vkey 参数
        if (strpos($host, 'cache') !== false && strpos($query, 'vkey=') !== false) {
            return true;
        }

        // 路径包含 /Cache/ 且有 vkey 参数
        if (stripos($path, '/Cache/') !== false && strpos($query, 'vkey=') !== false) {
            return true;
        }

        // 有 vkey 参数的 m3u8 链接
        if (stripos($path, '.m3u8') !== false && strpos($query, 'vkey=') !== false) {
            return true;
        }

        return false;
    }
}
