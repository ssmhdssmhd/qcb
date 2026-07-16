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

        // 清理常见平台后缀（先清理具体平台名，避免误删）
        $platformSuffixes = [
            '/[-_\s\|]?腾讯视频.*$/iu',
            '/[-_\s\|]?QQ视频.*$/iu',
            '/[-_\s\|]?爱奇艺.*$/iu',
            '/[-_\s\|]?iQIYI.*$/iu',
            '/[-_\s\|]?优酷.*$/iu',
            '/[-_\s\|]?YOUKU.*$/iu',
            '/[-_\s\|]?芒果TV.*$/iu',
            '/[-_\s\|]?MGTV.*$/iu',
            '/[-_\s\|]?哔哩哔哩.*$/iu',
            '/[-_\s\|]?bilibili.*$/iu',
            '/[-_\s\|]?搜狐视频.*$/iu',
            '/[-_\s\|]?sohu.*$/iu',
            '/[-_\s\|]?PP视频.*$/iu',
            '/[-_\s\|]?PPTV.*$/iu',
        ];
        foreach ($platformSuffixes as $pattern) {
            $title = preg_replace($pattern, '', $title);
        }

        // 清理常见描述性后缀
        $title = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/高清.*?$/u', '', $title);
        $title = preg_replace('/完整版.*?$/u', '', $title);
        $title = preg_replace('/最新一期.*?$/u', '', $title);
        $title = preg_replace('/第.*?期.*?$/u', '', $title);
        $title = preg_replace('/第.*?集.*?$/u', '', $title);
        $title = preg_replace('/[Ee][Pp]?\d+.*?$/', '', $title);

        // 清理会员/独播等标记
        $title = preg_replace('/[-_\s]?VIP\s*专享/iu', '', $title);
        $title = preg_replace('/[-_\s]?会员专享/iu', '', $title);
        $title = preg_replace('/[-_\s]?独播/iu', '', $title);
        $title = preg_replace('/[-_\s]?自制/iu', '', $title);
        $title = preg_replace('/[-_\s]?出品/iu', '', $title);

        $title = preg_replace('/\s+/', ' ', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|·");

        if (mb_strlen($title) < 2) return null;

        return $title;
    }

    /**
     * 从标题中提取影视基础信息
     * @param string $title
     * @return array ['base_title' => '', 'season_num' => null, 'episode_num' => null, 'part' => '', 'version' => '']
     */
    public function parseVideoTitle($title)
    {
        $result = [
            'base_title' => '',
            'season_num' => null,
            'episode_num' => null,
            'part' => '',
            'version' => '',
        ];

        $title = trim($title);
        if (empty($title)) {
            return $result;
        }

        $cleaned = $title;

        // 提取季数（多种格式）
        $seasonNum = null;
        $seasonPatterns = [
            '/第\s*(\d+)\s*季/u',
            '/第\s*([一二三四五六七八九十百]+)\s*季/u',
            '/Season\s*(\d+)/i',
            '/\bS(\d{1,2})\b/i',
            '/Ⅱ/u',
            '/Ⅲ/u',
            '/Ⅳ/u',
            '/Ⅴ/u',
        ];

        foreach ($seasonPatterns as $pattern) {
            if (preg_match($pattern, $cleaned, $m)) {
                if (isset($m[1])) {
                    $num = $this->chineseToNumber($m[1]);
                    if ($num !== null) {
                        $seasonNum = $num;
                    }
                } elseif (strpos($m[0], 'Ⅱ') !== false) {
                    $seasonNum = 2;
                } elseif (strpos($m[0], 'Ⅲ') !== false) {
                    $seasonNum = 3;
                } elseif (strpos($m[0], 'Ⅳ') !== false) {
                    $seasonNum = 4;
                } elseif (strpos($m[0], 'Ⅴ') !== false) {
                    $seasonNum = 5;
                }
                break;
            }
        }

        // 提取集数（多种格式）
        $episodeNum = null;
        $episodePatterns = [
            '/第\s*(\d+)\s*集/u',
            '/第\s*([一二三四五六七八九十百]+)\s*集/u',
            '/第\s*(\d+)\s*期/u',
            '/第\s*([一二三四五六七八九十百]+)\s*期/u',
            '/[Ee][Pp]\s*(\d+)/',
            '/\bE\s*(\d+)/i',
            '/\b(\d{1,3})\s*集\b/u',
        ];

        foreach ($episodePatterns as $pattern) {
            if (preg_match($pattern, $cleaned, $m)) {
                if (isset($m[1])) {
                    $num = $this->chineseToNumber($m[1]);
                    if ($num !== null) {
                        $episodeNum = $num;
                    }
                }
                break;
            }
        }

        // 提取版本信息（如 4K、HD、蓝光、1080P 等）
        $version = '';
        if (preg_match('/(4K|8K|1080P|720P|蓝光|高清|超清|标清|HD|FHD|UHD|SHD)/i', $cleaned, $vm)) {
            $version = $vm[1];
        }

        // 提取篇章/部分信息
        $part = '';
        if (preg_match('/(上篇|下篇|前篇|后篇|终章|最终章|特别篇|番外篇|SP|OVA|OAD)/iu', $cleaned, $pm)) {
            $part = $pm[1];
        }

        // 清理标题得到基础标题
        $baseTitle = $cleaned;

        // 移除季数描述
        $baseTitle = preg_replace('/\s*第\s*\d+\s*季\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*第\s*[一二三四五六七八九十百]+\s*季\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*Season\s*\d+\s*/i', ' ', $baseTitle);
        $baseTitle = preg_replace('/\bS\d{1,2}\b/i', ' ', $baseTitle);
        $baseTitle = preg_replace('/[ⅡⅢⅣⅤ]/u', ' ', $baseTitle);

        // 移除集数描述
        $baseTitle = preg_replace('/\s*第\s*\d+\s*集\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*第\s*[一二三四五六七八九十百]+\s*集\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*第\s*\d+\s*期\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*第\s*[一二三四五六七八九十百]+\s*期\s*/u', ' ', $baseTitle);
        $baseTitle = preg_replace('/\s*[Ee][Pp]?\s*\d+\s*/', ' ', $baseTitle);

        // 移除版本信息
        $baseTitle = preg_replace('/\s*(4K|8K|1080P|720P|蓝光|高清|超清|标清|HD|FHD|UHD|SHD)\s*/i', ' ', $baseTitle);

        // 移除篇章信息
        $baseTitle = preg_replace('/\s*(上篇|下篇|前篇|后篇|终章|最终章|特别篇|番外篇|SP|OVA|OAD)\s*/iu', ' ', $baseTitle);

        // 最终清理
        $baseTitle = preg_replace('/[-_|【】《》\[\]（）()].*?$/u', '', $baseTitle);
        $baseTitle = preg_replace('/\s+/', ' ', $baseTitle);
        $baseTitle = trim($baseTitle, " \t\n\r\0\x0B-_—|·");

        if (mb_strlen($baseTitle) < 2) {
            $baseTitle = $cleaned;
        }

        $result['base_title'] = $baseTitle;
        $result['season_num'] = $seasonNum;
        $result['episode_num'] = $episodeNum;
        $result['part'] = $part;
        $result['version'] = $version;

        return $result;
    }

    /**
     * 中文/阿拉伯数字转整数
     * @param string $str
     * @return int|null
     */
    protected function chineseToNumber($str)
    {
        if ($str === '' || $str === null) {
            return null;
        }
        $str = (string)$str;

        if (ctype_digit($str)) {
            return (int)$str;
        }

        $cnNumbers = [
            '零' => 0, '一' => 1, '二' => 2, '两' => 2, '三' => 3,
            '四' => 4, '五' => 5, '六' => 6, '七' => 7, '八' => 8,
            '九' => 9, '十' => 10, '百' => 100, '千' => 1000,
        ];

        $chars = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        $result = 0;
        $temp = 0;

        foreach ($chars as $char) {
            if (!isset($cnNumbers[$char])) {
                continue;
            }
            $val = $cnNumbers[$char];
            if ($val >= 10) {
                if ($temp == 0) {
                    $temp = 1;
                }
                $result += $temp * $val;
                $temp = 0;
            } else {
                $temp = $val;
            }
        }
        $result += $temp;

        return $result > 0 ? $result : null;
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
