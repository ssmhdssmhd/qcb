<?php
/**
 * 平台适配器抽象基类
 * 提供通用功能和工具方法
 */

require_once __DIR__ . '/PlatformAdapterInterface.php';

abstract class AbstractPlatformAdapter implements PlatformAdapterInterface
{
    protected $config = [];
    protected $httpTimeout = 15;
    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    /**
     * HTTP GET 请求
     */
    protected function httpGet($url, $headers = [])
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->httpTimeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $this->userAgent,
            CURLOPT_HTTPHEADER => array_merge(['Accept: */*', 'Accept-Language: zh-CN,zh;q=0.9'], $headers),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400 || $response === false) {
            return null;
        }

        return $response;
    }

    /**
     * 移动端 HTTP GET
     */
    protected function httpGetMobile($url)
    {
        return $this->httpGet($url, [
            'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
        ]);
    }

    /**
     * 从 HTML 提取标题
     */
    protected function extractTitleFromHtml($html)
    {
        $patterns = [
            '/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:title["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<title[^>]*>([^<]+)<\/title>/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $title = trim($matches[1]);
                if (mb_strlen($title) >= 2) {
                    return $title;
                }
            }
        }

        return null;
    }

    /**
     * 从 HTML 提取封面
     */
    protected function extractCoverFromHtml($html)
    {
        $patterns = [
            '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return trim($matches[1]);
            }
        }

        return null;
    }

    /**
     * JSON 安全解码（处理 JSONP 等格式）
     */
    protected function safeJsonDecode($response)
    {
        if (empty($response) || !is_string($response)) {
            return null;
        }

        $cleaned = trim($response);
        $cleaned = preg_replace('/^\/\*[\s\S]*?\*\//', '', $cleaned);
        $cleaned = preg_replace('/^(?:var|let|const)\s+\w+\s*=\s*/', '', $cleaned);
        $cleaned = preg_replace('/^\w+\s*=\s*/', '', $cleaned);
        $cleaned = trim($cleaned);

        if (preg_match('/^\w+\s*\(/', $cleaned)) {
            $cleaned = preg_replace('/^\w+\s*\(/', '', $cleaned);
            $cleaned = preg_replace('/\)\s*;?\s*$/', '', $cleaned);
            $cleaned = trim($cleaned);
        }

        $cleaned = rtrim($cleaned, ';');
        $data = json_decode(trim($cleaned), true);

        return $data;
    }

    /**
     * 通用标题清理
     */
    public function cleanTitle($title)
    {
        $title = trim($title);
        if (empty($title)) return null;

        // 提取书名号内的标题
        if (preg_match('/^《([^《》]+)》/u', $title, $m)) {
            $title = $m[1];
        }

        // 提取引号内的标题
        if (preg_match('/^"([^"]+)"/', $title, $m)) {
            $title = $m[1];
        }

        // 清理后缀描述
        $title = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/高清.*?$/u', '', $title);
        $title = preg_replace('/完整版.*?$/u', '', $title);
        $title = preg_replace('/_腾讯视频/i', '', $title);
        $title = preg_replace('/- 腾讯视频/i', '', $title);
        $title = preg_replace('/-爱奇艺/i', '', $title);
        $title = preg_replace('/-优酷/i', '', $title);
        $title = preg_replace('/最新一期.*?$/u', '', $title);
        $title = preg_replace('/第.*?期.*?$/u', '', $title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|·");

        if (mb_strlen($title) < 2) return null;

        return $title;
    }

    /**
     * 通用匹配分数计算
     */
    protected function calculateBaseScore($str1, $str2)
    {
        $str1 = trim($str1);
        $str2 = trim($str2);

        if (empty($str1) || empty($str2)) return 0;
        if ($str1 === $str2) return 100;

        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);
        $short = $len1 < $len2 ? $str1 : $str2;
        $long = $len1 < $len2 ? $str2 : $str1;

        if (mb_strpos($long, $short) !== false) {
            $ratio = mb_strlen($short) / mb_strlen($long);
            if ($ratio >= 0.85) return 95;
            if ($ratio >= 0.7) return 88;
            if ($ratio >= 0.5) return 75;
            return 60;
        }

        // 字符相似度
        $chars1 = preg_split('//u', $str1, -1, PREG_SPLIT_NO_EMPTY);
        $chars2 = preg_split('//u', $str2, -1, PREG_SPLIT_NO_EMPTY);
        $count1 = array_count_values($chars1);
        $count2 = array_count_values($chars2);
        $common = 0;
        foreach ($count1 as $c => $n) {
            if (isset($count2[$c])) {
                $common += min($n, $count2[$c]);
            }
        }
        $similarity = ($common / max($len1, $len2)) * 100;

        if ($similarity >= 90) return 90;
        if ($similarity >= 80) return 80;

        // 前缀匹配加成
        $prefixLen = 0;
        $minLen = min($len1, $len2);
        for ($i = 0; $i < $minLen; $i++) {
            if (mb_substr($str1, $i, 1) === mb_substr($str2, $i, 1)) {
                $prefixLen++;
            } else break;
        }
        $prefixBonus = $minLen > 0 ? ($prefixLen / $minLen) * 30 : 0;

        return min(100, $similarity * 0.6 + $prefixBonus);
    }

    /**
     * 获取配置
     */
    public function getConfig()
    {
        return $this->config;
    }
}
