<?php
/**
 * 官替 API 管理器
 * 负责将官方视频平台链接（腾讯、爱奇艺、优酷等）替换为资源站 M3U8 地址
 */

require_once __DIR__ . '/ResourceSiteManager.php';

class OfficialReplaceManager {
    private $configFile;
    private $config;
    private $lastHttpError = '';

    public function __construct() {
        $this->configFile = __DIR__ . '/official_replace_config.php';
        $this->loadConfig();
    }

    private function loadConfig() {
        if (file_exists($this->configFile)) {
            $this->config = require $this->configFile;
        } else {
            $this->config = $this->getDefaultConfig();
            $this->saveConfig();
        }
    }

    private function getDefaultConfig() {
        return [
            'version' => '1.0',
            'update_date' => date('Y-m-d H:i:s'),
            'enabled' => true,
            'default_site' => '量子',
            'max_search_sites' => 5,
            'cache_ttl' => 3600,
            'platforms' => [
                [
                    'name' => '腾讯视频',
                    'domain' => 'v.qq.com',
                    'enabled' => true,
                    'pattern' => '/v\.qq\.com\/.*?(?:vid=|\/)([a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video_title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '爱奇艺',
                    'domain' => 'iqiyi.com',
                    'enabled' => true,
                    'pattern' => '/iqiyi\.com\/.*?([a-zA-Z0-9]{5,})/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .main_title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '优酷',
                    'domain' => 'youku.com',
                    'enabled' => true,
                    'pattern' => '/youku\.com\/.*?id_([a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '芒果TV',
                    'domain' => 'mgtv.com',
                    'enabled' => true,
                    'pattern' => '/mgtv\.com\/.*?\/([a-zA-Z0-9]+)\.html/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .player-title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '哔哩哔哩',
                    'domain' => 'bilibili.com',
                    'enabled' => true,
                    'pattern' => '/bilibili\.com\/video\/(BV[a-zA-Z0-9]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], .video-title, h1',
                    'priority' => 1
                ],
                [
                    'name' => '搜狐视频',
                    'domain' => 'sohu.com',
                    'enabled' => true,
                    'pattern' => '/sohu\.com\/.*?(\d+)\.shtml/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
                    'priority' => 2
                ],
                [
                    'name' => 'PP视频',
                    'domain' => 'pptv.com',
                    'enabled' => true,
                    'pattern' => '/pptv\.com\/showpage\/([a-zA-Z0-9_-]+)/i',
                    'title_selector' => 'meta[property="og:title"], meta[name="twitter:title"], h1',
                    'priority' => 2
                ]
            ],
            'search_sites' => ['量子', '最大', '猫眼', '红牛'],
            'match_threshold' => 60
        ];
    }

    public function getConfig() {
        return $this->config;
    }

    public function saveConfigData($config) {
        $config['update_date'] = date('Y-m-d H:i:s');
        $this->config = $config;
        return $this->saveConfig();
    }

    private function saveConfig() {
        $content = '<?php' . "\n";
        $content .= '/**' . "\n";
        $content .= ' * 官替 API 配置' . "\n";
        $content .= ' * 自动生成于: ' . date('Y-m-d H:i:s') . "\n";
        $content .= ' */' . "\n\n";
        $content .= 'return ' . $this->arrayExport($this->config) . ';' . "\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    public function resolve($url) {
        if (empty($url)) {
            return ['success' => false, 'message' => 'URL不能为空'];
        }

        if (!$this->config['enabled']) {
            return ['success' => false, 'message' => '官替功能已禁用'];
        }

        $platform = $this->detectPlatform($url);
        if (!$platform) {
            return ['success' => false, 'message' => '不支持的视频平台'];
        }

        $videoInfo = $this->fetchVideoInfo($url, $platform);
        if (!$videoInfo || empty($videoInfo['title'])) {
            return ['success' => false, 'message' => '无法获取视频信息', 'platform' => $platform['name']];
        }

        $searchResult = $this->searchInSites($videoInfo['title']);
        if (!$searchResult['success'] || empty($searchResult['videos'])) {
            return [
                'success' => false,
                'message' => '未找到匹配的资源',
                'platform' => $platform['name'],
                'video_title' => $videoInfo['title']
            ];
        }

        $bestMatch = $this->findBestMatch($videoInfo, $searchResult['videos']);
        if (!$bestMatch) {
            return [
                'success' => false,
                'message' => '未找到匹配度足够的资源',
                'platform' => $platform['name'],
                'video_title' => $videoInfo['title'],
                'candidates' => array_slice($searchResult['videos'], 0, 5)
            ];
        }

        return [
            'success' => true,
            'platform' => $platform['name'],
            'original_url' => $url,
            'video_title' => $videoInfo['title'],
            'match_score' => $bestMatch['score'],
            'site' => $bestMatch['site'],
            'video' => $bestMatch['video'],
            'm3u8_url' => $bestMatch['video']['first_url'] ?? $bestMatch['video']['url'] ?? '',
            'all_urls' => $bestMatch['video']['urls'] ?? [],
            'alternatives' => $searchResult['videos']
        ];
    }

    private function detectPlatform($url) {
        foreach ($this->config['platforms'] as $platform) {
            if (empty($platform['enabled'])) continue;
            if (stripos($url, $platform['domain']) !== false) {
                return $platform;
            }
        }
        return null;
    }

    private function fetchVideoInfo($url, $platform) {
        $html = $this->httpGet($url);
        if (!$html) {
            return null;
        }

        $title = $this->extractTitle($html, $platform);
        $cover = $this->extractCover($html);

        return [
            'title' => $title,
            'cover' => $cover,
            'url' => $url,
            'platform' => $platform['name']
        ];
    }

    private function extractTitle($html, $platform) {
        $patterns = [
            '/<meta[^>]+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:title["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<title[^>]*>([^<]+)<\/title>/i',
            '/<h1[^>]*>([^<]+)<\/h1>/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                $title = trim($matches[1]);
                $title = preg_replace('/\s+/', ' ', $title);
                if (!empty($title) && mb_strlen($title) > 2) {
                    $title = $this->cleanTitle($title);
                    if (!empty($title)) {
                        return $title;
                    }
                }
            }
        }

        return null;
    }

    private function extractCover($html) {
        $patterns = [
            '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i',
            '/<meta[^>]+name=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $html, $matches)) {
                return trim($matches[1]);
            }
        }
        return null;
    }

    private function cleanTitle($title) {
        $title = preg_replace('/[-_|【】\[\]（）()].*?$/u', '', $title);
        $title = preg_replace('/在线观看.*?$/u', '', $title);
        $title = preg_replace('/高清.*?$/u', '', $title);
        $title = preg_replace('/完整版.*?$/u', '', $title);
        $title = trim($title, " \t\n\r\0\x0B-_—|");
        $title = trim($title);

        if (mb_strlen($title) < 2) {
            return null;
        }

        return $title;
    }

    private function searchInSites($keyword) {
        $siteMgr = new ResourceSiteManager();
        $sites = $this->config['search_sites'] ?? [];
        $allVideos = [];
        $searchedSites = 0;

        if (empty($sites)) {
            $result = $siteMgr->searchAllSites($keyword, 3, 5);
            if ($result['success']) {
                foreach (($result['results'] ?? []) as $siteResult) {
                    if (!empty($siteResult['videos'])) {
                        foreach ($siteResult['videos'] as $v) {
                            $v['site'] = $siteResult['site'];
                            $allVideos[] = $v;
                        }
                        $searchedSites++;
                    }
                }
            }
        } else {
            foreach ($sites as $siteName) {
                $result = $siteMgr->searchSite($siteName, $keyword);
                if ($result && $result['success'] && !empty($result['videos'])) {
                    foreach ($result['videos'] as $v) {
                        $v['site'] = $siteName;
                        $allVideos[] = $v;
                    }
                    $searchedSites++;
                }
                if ($searchedSites >= ($this->config['max_search_sites'] ?? 5)) {
                    break;
                }
            }
        }

        return [
            'success' => !empty($allVideos),
            'videos' => $allVideos,
            'searched_sites' => $searchedSites
        ];
    }

    private function findBestMatch($videoInfo, $videos) {
        $keyword = $videoInfo['title'];
        $threshold = $this->config['match_threshold'] ?? 60;
        $bestMatch = null;
        $bestScore = 0;

        foreach ($videos as $video) {
            $videoName = $video['name'] ?? '';
            $score = $this->calculateMatchScore($keyword, $videoName);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = [
                    'video' => $video,
                    'score' => $score,
                    'site' => $video['site'] ?? ''
                ];
            }
        }

        if ($bestScore >= $threshold) {
            return $bestMatch;
        }

        return null;
    }

    private function calculateMatchScore($str1, $str2) {
        $str1 = trim($str1);
        $str2 = trim($str2);

        if (empty($str1) || empty($str2)) {
            return 0;
        }

        if ($str1 === $str2) {
            return 100;
        }

        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);

        if ($len1 == 0 || $len2 == 0) {
            return 0;
        }

        $short = $len1 < $len2 ? $str1 : $str2;
        $long = $len1 < $len2 ? $str2 : $str1;

        if (mb_strpos($long, $short) !== false) {
            return 80 + min(20, (mb_strlen($short) / mb_strlen($long)) * 40);
        }

        $commonChars = 0;
        $chars1 = preg_split('//u', $str1, -1, PREG_SPLIT_NO_EMPTY);
        $chars2 = preg_split('//u', $str2, -1, PREG_SPLIT_NO_EMPTY);
        $charCount1 = array_count_values($chars1);
        $charCount2 = array_count_values($chars2);

        foreach ($charCount1 as $char => $count) {
            if (isset($charCount2[$char])) {
                $commonChars += min($count, $charCount2[$char]);
            }
        }

        $totalChars = max($len1, $len2);
        $charSimilarity = $totalChars > 0 ? ($commonChars / $totalChars) * 100 : 0;

        return round($charSimilarity * 0.7, 2);
    }

    private function httpGet($url, $timeout = 15, $retry = 2) {
        $lastError = '';
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];

        for ($attempt = 0; $attempt <= $retry; $attempt++) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$attempt % count($userAgents)]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8',
                'Accept-Encoding: gzip, deflate, br'
            ]);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookie'));
            curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookie'));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
                return $response;
            }

            $lastError = $error ? $error : ('HTTP ' . $httpCode);
            if ($attempt < $retry) {
                usleep(500000);
            }
        }

        $this->lastHttpError = $lastError;
        return false;
    }

    public function getPlatforms() {
        return $this->config['platforms'] ?? [];
    }

    public function updatePlatform($index, $platformData) {
        if (isset($this->config['platforms'][$index])) {
            $this->config['platforms'][$index] = array_merge($this->config['platforms'][$index], $platformData);
            $this->config['update_date'] = date('Y-m-d H:i:s');
            return $this->saveConfig();
        }
        return false;
    }

    public function addPlatform($platformData) {
        $this->config['platforms'][] = array_merge([
            'name' => '',
            'domain' => '',
            'enabled' => true,
            'pattern' => '',
            'title_selector' => '',
            'priority' => 10
        ], $platformData);
        $this->config['update_date'] = date('Y-m-d H:i:s');
        return $this->saveConfig();
    }

    public function deletePlatform($index) {
        if (isset($this->config['platforms'][$index])) {
            array_splice($this->config['platforms'], $index, 1);
            $this->config['update_date'] = date('Y-m-d H:i:s');
            return $this->saveConfig();
        }
        return false;
    }

    private function arrayExport($array, $indent = 0) {
        if (!is_array($array)) return var_export($array, true);
        if (empty($array)) return '[]';

        $prefix = str_repeat('    ', $indent);
        $nextPrefix = str_repeat('    ', $indent + 1);
        $isList = range(0, count($array) - 1) === array_keys($array);

        $items = [];
        foreach ($array as $key => $value) {
            $keyStr = $isList ? '' : var_export((string)$key, true) . ' => ';
            $items[] = $nextPrefix . $keyStr . $this->arrayExport($value, $indent + 1);
        }

        return "[\n" . implode(",\n", $items) . "\n" . $prefix . "]";
    }
}
