<?php

class CctvSourceManager
{
    private $sources = array(
        'https://raw.githubusercontent.com/ipv6-cn/iptv/main/cctv.m3u',
        'https://raw.githubusercontent.com/SuMaiKaDe/iptv/main/cctv.txt',
        'https://raw.githubusercontent.com/yanG-1101/Auto_iptv/main/m3u/cctv.m3u'
    );

    private $cacheFile;
    private $cacheDir;
    private $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    private $timeout = 15;

    public function __construct()
    {
        $this->cacheDir = dirname(__DIR__) . '/cache';
        $this->cacheFile = $this->cacheDir . '/cctv_sources.json';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    public function fetchSources($forceAll = false)
    {
        if (!$forceAll && ($cached = $this->loadCache())) {
            return $cached;
        }

        $resultList = array();
        $usedSource = '';
        $fetchTime = time();

        foreach ($this->sources as $sourceUrl) {
            $content = $this->curlFetch($sourceUrl);
            if ($content === false) {
                continue;
            }

            $parsed = $this->detectAndParse($content);
            if (!empty($parsed)) {
                $resultList = $parsed;
                $usedSource = $sourceUrl;
                break;
            }
        }

        if (empty($resultList) && $forceAll) {
            foreach ($this->sources as $sourceUrl) {
                $content = $this->curlFetch($sourceUrl);
                if ($content === false) {
                    continue;
                }
                $parsed = $this->detectAndParse($content);
                if (!empty($parsed)) {
                    $resultList = array_merge($resultList, $parsed);
                    if (empty($usedSource)) {
                        $usedSource = $sourceUrl;
                    }
                }
            }
        }

        $resultList = $this->filterByCategory($resultList, true);
        $resultList = $this->deduplicate($resultList);

        $cacheData = array(
            'fetched_at' => $fetchTime,
            'source_url' => $usedSource,
            'count' => count($resultList),
            'channels' => $resultList
        );

        $this->saveCache($cacheData);

        return $resultList;
    }

    private function detectAndParse($content)
    {
        $trimmed = trim($content);
        if (stripos($trimmed, '#EXTM3U') !== false || stripos($trimmed, '#EXTINF') !== false) {
            return $this->parseM3U($content);
        }
        return $this->parseTxt($content);
    }

    public function parseM3U($content)
    {
        $list = array();
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $current = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#EXTM3U') === 0) {
                continue;
            }

            if (strpos($line, '#EXTINF') === 0) {
                $current = $this->parseExtInfLine($line);
                continue;
            }

            if (strpos($line, '#') === 0) {
                continue;
            }

            if ($current !== null && $this->isValidUrl($line)) {
                $current['url'] = $line;
                $current = $this->normalizeChannel($current);
                $list[] = $current;
                $current = null;
            }
        }

        return $list;
    }

    private function parseExtInfLine($line)
    {
        $info = array(
            'id' => '',
            'name' => '',
            'group' => '',
            'logo' => ''
        );

        if (preg_match('/tvg-id="([^"]*)"/i', $line, $m)) {
            $info['id'] = trim($m[1]);
        }
        if (preg_match('/tvg-name="([^"]*)"/i', $line, $m)) {
            $info['name'] = trim($m[1]);
        }
        if (preg_match('/tvg-logo="([^"]*)"/i', $line, $m)) {
            $info['logo'] = trim($m[1]);
        }
        if (preg_match('/group-title="([^"]*)"/i', $line, $m)) {
            $info['group'] = trim($m[1]);
        }

        $lastComma = strrpos($line, ',');
        if ($lastComma !== false) {
            $nameFromComma = trim(substr($line, $lastComma + 1));
            if (!empty($nameFromComma)) {
                $info['name'] = $nameFromComma;
            }
        }

        return $info;
    }

    public function parseTxt($content)
    {
        $list = array();
        $lines = preg_split("/\r\n|\n|\r/", $content);
        $currentGroupName = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }

            if (preg_match('/^(.+?),#genre#$/i', $line, $m)) {
                $currentGroupName = trim($m[1]);
                continue;
            }

            if (strpos($line, ',') === false) {
                continue;
            }

            $lastComma = strrpos($line, ',');
            if ($lastComma === false) {
                continue;
            }

            $urlPart = trim(substr($line, $lastComma + 1));
            $namePart = trim(substr($line, 0, $lastComma));

            if (!$this->isValidUrl($urlPart)) {
                continue;
            }

            $channel = $this->parseTxtNamePart($namePart);
            $channel['url'] = $urlPart;
            if (!empty($currentGroupName) && empty($channel['group'])) {
                $channel['group'] = $currentGroupName;
            }
            $channel = $this->normalizeChannel($channel);
            $list[] = $channel;
        }

        return $list;
    }

    private function parseTxtNamePart($namePart)
    {
        $info = array(
            'id' => '',
            'name' => '',
            'group' => '',
            'logo' => ''
        );

        if (preg_match('/tvg-id="([^"]*)"/i', $namePart, $m)) {
            $info['id'] = trim($m[1]);
        }
        if (preg_match('/tvg-logo="([^"]*)"/i', $namePart, $m)) {
            $info['logo'] = trim($m[1]);
        }
        if (preg_match('/group-title="([^"]*)"/i', $namePart, $m)) {
            $info['group'] = trim($m[1]);
        }

        $cleanName = preg_replace('/\s*tvg-id="[^"]*"/i', '', $namePart);
        $cleanName = preg_replace('/\s*tvg-name="[^"]*"/i', '', $cleanName);
        $cleanName = preg_replace('/\s*tvg-logo="[^"]*"/i', '', $cleanName);
        $cleanName = preg_replace('/\s*group-title="[^"]*"/i', '', $cleanName);
        $cleanName = trim($cleanName);

        if (preg_match('/^(.+?)\s*,\s*(.+)$/', $cleanName, $m)) {
            if (empty($info['group'])) {
                $info['group'] = trim($m[1]);
            }
            $info['name'] = trim($m[2]);
        } else {
            $info['name'] = $cleanName;
        }

        return $info;
    }

    public function filterByCategory($list, $onlyCCTV = true)
    {
        $filtered = array();
        $provincePattern = '/(北京|上海|广东|江苏|浙江|湖南|东方|深圳|天津|重庆|安徽|福建|江西|山东|河南|湖北|四川|河北|山西|辽宁|吉林|黑龙江|广西|海南|贵州|云南|陕西|甘肃|青海|宁夏|新疆|内蒙古|西藏)(卫视|电视台|台)?/u';
        $cctvPattern = '/^CCTV/i';

        foreach ($list as $channel) {
            $name = isset($channel['name']) ? $channel['name'] : '';
            $group = isset($channel['group']) ? $channel['group'] : '';
            $text = $name . ' ' . $group;

            $isCctv = preg_match($cctvPattern, $name);
            $isProvince = preg_match($provincePattern, $text);

            if ($onlyCCTV) {
                if ($isCctv || $isProvince) {
                    $filtered[] = $channel;
                }
            } else {
                $filtered[] = $channel;
            }
        }

        return $filtered;
    }

    private function normalizeChannel($channel)
    {
        $name = isset($channel['name']) ? trim($channel['name']) : '';
        $id = isset($channel['id']) ? trim($channel['id']) : '';
        $group = isset($channel['group']) ? trim($channel['group']) : '';
        $logo = isset($channel['logo']) ? trim($channel['logo']) : '';
        $url = isset($channel['url']) ? trim($channel['url']) : '';

        if (empty($id)) {
            $id = $this->generateChannelId($name);
        }

        if (empty($group)) {
            if (preg_match('/^CCTV/i', $name)) {
                $group = '央视频道';
            } else {
                $group = '卫视频道';
            }
        }

        $quality = 'SD';
        if (stripos($name, 'HD') !== false || stripos($name, '高清') !== false) {
            $quality = 'HD';
        } elseif (stripos($name, '4K') !== false || stripos($name, '超高清') !== false) {
            $quality = '4K';
        }

        return array(
            'id' => $id,
            'name' => $name,
            'url' => $url,
            'group' => $group,
            'logo' => $logo,
            'quality' => $quality
        );
    }

    private function generateChannelId($name)
    {
        $name = preg_replace('/\s+/u', '', $name);
        $name = preg_replace('/[^\p{L}\p{N}]/u', '', $name);
        if (function_exists('mb_strtolower')) {
            $name = mb_strtolower($name, 'UTF-8');
        } else {
            $name = strtolower($name);
        }
        if (empty($name)) {
            $name = 'channel_' . substr(md5(uniqid('', true)), 0, 8);
        }
        return $name;
    }

    private function deduplicate($list)
    {
        $seen = array();
        $result = array();
        foreach ($list as $item) {
            $key = (isset($item['id']) ? $item['id'] : '') . '|' . (isset($item['url']) ? $item['url'] : '');
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $result[] = $item;
            }
        }
        return $result;
    }

    private function curlFetch($url)
    {
        if (!function_exists('curl_init')) {
            return $this->fileGetContentsFetch($url);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: text/plain,application/octet-stream,*/*',
            'Accept-Language: zh-CN,zh;q=0.9,en;q=0.8'
        ));

        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($content === false || $httpCode < 200 || $httpCode >= 400) {
            return false;
        }

        return $content;
    }

    private function fileGetContentsFetch($url)
    {
        $context = stream_context_create(array(
            'http' => array(
                'timeout' => $this->timeout,
                'user_agent' => $this->userAgent,
                'follow_location' => true,
                'max_redirects' => 5,
                'header' => "Accept: text/plain,application/octet-stream,*/*\r\nAccept-Language: zh-CN,zh;q=0.9,en;q=0.8\r\n"
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        ));

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            return false;
        }
        return $content;
    }

    private function isValidUrl($url)
    {
        if (empty($url)) {
            return false;
        }
        if (preg_match('/^(https?|rtmp|rtsp|mms|udp|p3p|flv):\/\//i', $url)) {
            return true;
        }
        if (preg_match('/^\/\//', $url)) {
            return true;
        }
        return false;
    }

    private function loadCache()
    {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        $data = @json_decode(@file_get_contents($this->cacheFile), true);
        if (!is_array($data)) {
            return false;
        }
        if (!isset($data['channels']) || !is_array($data['channels'])) {
            return false;
        }
        return $data['channels'];
    }

    private function saveCache($data)
    {
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        @file_put_contents($this->cacheFile, $json);
    }

    public function getCacheInfo()
    {
        if (!file_exists($this->cacheFile)) {
            return null;
        }
        $data = @json_decode(@file_get_contents($this->cacheFile), true);
        if (!is_array($data)) {
            return null;
        }
        return array(
            'fetched_at' => isset($data['fetched_at']) ? $data['fetched_at'] : 0,
            'source_url' => isset($data['source_url']) ? $data['source_url'] : '',
            'count' => isset($data['count']) ? $data['count'] : 0
        );
    }
}
