<?php

class CctvPlaylistGenerator
{
    private $cacheDir;

    public function __construct()
    {
        $this->cacheDir = dirname(__DIR__) . '/cache/cctv';
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    public function generateM3U($verifiedSources, $includeFallback = true)
    {
        $output = "#EXTM3U\n";
        $output .= "#EXT-X-VERSION:3\n";
        $output .= "#Generated-At: " . date('Y-m-d H:i:s') . "\n\n";

        $flatList = $this->toChannelList($verifiedSources);

        foreach ($flatList as $channel) {
            $id = $this->esc(isset($channel['id']) ? $channel['id'] : '');
            $name = $this->esc(isset($channel['name']) ? $channel['name'] : '');
            $group = $this->esc(isset($channel['group']) ? $channel['group'] : '');
            $logo = $this->esc(isset($channel['logo']) ? $channel['logo'] : '');
            $mainUrl = isset($channel['url']) ? $channel['url'] : '';

            $urls = array();
            if (isset($channel['urls']) && is_array($channel['urls'])) {
                $urls = $channel['urls'];
            }
            if (isset($channel['sources']) && is_array($channel['sources'])) {
                foreach ($channel['sources'] as $src) {
                    if (isset($src['url']) && !in_array($src['url'], $urls)) {
                        $urls[] = $src['url'];
                    }
                }
            }
            if (!empty($mainUrl) && !in_array($mainUrl, $urls)) {
                array_unshift($urls, $mainUrl);
            }
            $urls = array_values(array_filter($urls));

            if (empty($urls)) {
                continue;
            }

            $extInf = '#EXTINF:-1';
            if (!empty($id)) {
                $extInf .= ' tvg-id="' . $id . '"';
            }
            if (!empty($name)) {
                $extInf .= ' tvg-name="' . $name . '"';
            }
            if (!empty($logo)) {
                $extInf .= ' tvg-logo="' . $logo . '"';
            }
            if (!empty($group)) {
                $extInf .= ' group-title="' . $group . '"';
            }
            $extInf .= ',' . $name;

            $output .= $extInf . "\n";
            $output .= $urls[0] . "\n";

            if ($includeFallback && count($urls) > 1) {
                for ($i = 1; $i < count($urls); $i++) {
                    $output .= $extInf . "\n";
                    $output .= $urls[$i] . "\n";
                }
            }

            $output .= "\n";
        }

        return $output;
    }

    public function generateTxt($verifiedSources)
    {
        $output = '';
        $flatList = $this->toChannelList($verifiedSources);
        $lastGroup = '';

        foreach ($flatList as $channel) {
            $group = isset($channel['group']) ? $channel['group'] : '';
            if ($group !== $lastGroup && !empty($group)) {
                $output .= $group . ",#genre#\n";
                $lastGroup = $group;
            }

            $name = isset($channel['name']) ? $channel['name'] : '';
            $mainUrl = isset($channel['url']) ? $channel['url'] : '';

            $urls = array();
            if (isset($channel['urls']) && is_array($channel['urls'])) {
                $urls = $channel['urls'];
            }
            if (isset($channel['sources']) && is_array($channel['sources'])) {
                foreach ($channel['sources'] as $src) {
                    if (isset($src['url']) && !in_array($src['url'], $urls)) {
                        $urls[] = $src['url'];
                    }
                }
            }
            if (!empty($mainUrl) && !in_array($mainUrl, $urls)) {
                array_unshift($urls, $mainUrl);
            }
            $urls = array_values(array_filter($urls));

            if (empty($urls)) {
                continue;
            }

            $output .= $name . "," . $urls[0] . "\n";
        }

        return $output;
    }

    public function generateJson($verifiedSources)
    {
        $flatList = $this->toChannelList($verifiedSources);
        $result = array();

        foreach ($flatList as $channel) {
            $mainUrl = isset($channel['url']) ? $channel['url'] : '';

            $urls = array();
            if (isset($channel['urls']) && is_array($channel['urls'])) {
                $urls = $channel['urls'];
            }
            if (isset($channel['sources']) && is_array($channel['sources'])) {
                foreach ($channel['sources'] as $src) {
                    if (isset($src['url']) && !in_array($src['url'], $urls)) {
                        $urls[] = $src['url'];
                    }
                }
            }
            if (!empty($mainUrl) && !in_array($mainUrl, $urls)) {
                array_unshift($urls, $mainUrl);
            }
            $urls = array_values(array_filter($urls));

            $entry = array(
                'id' => isset($channel['id']) ? $channel['id'] : '',
                'name' => isset($channel['name']) ? $channel['name'] : '',
                'url' => !empty($urls) ? $urls[0] : '',
                'urls' => $urls,
                'group' => isset($channel['group']) ? $channel['group'] : '',
                'logo' => isset($channel['logo']) ? $channel['logo'] : '',
                'quality' => isset($channel['quality']) ? $channel['quality'] : 'SD'
            );

            if (isset($channel['latency_ms'])) {
                $entry['latency_ms'] = $channel['latency_ms'];
            } else {
                $bestLatency = -1;
                if (isset($channel['sources']) && is_array($channel['sources'])) {
                    foreach ($channel['sources'] as $src) {
                        if (isset($src['latency_ms']) && $src['latency_ms'] > 0) {
                            if ($bestLatency < 0 || $src['latency_ms'] < $bestLatency) {
                                $bestLatency = $src['latency_ms'];
                            }
                        }
                    }
                }
                if ($bestLatency > 0) {
                    $entry['latency_ms'] = $bestLatency;
                }
            }

            $result[] = $entry;
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function saveAll($cacheDir, $verifiedSources)
    {
        if (empty($cacheDir)) {
            $cacheDir = $this->cacheDir;
        }
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        $m3uContent = $this->generateM3U($verifiedSources, true);
        $txtContent = $this->generateTxt($verifiedSources);
        $jsonContent = $this->generateJson($verifiedSources);

        $m3uFile = rtrim($cacheDir, '/') . '/playlist.m3u';
        $txtFile = rtrim($cacheDir, '/') . '/playlist.txt';
        $jsonFile = rtrim($cacheDir, '/') . '/playlist.json';

        @file_put_contents($m3uFile, $m3uContent);
        @file_put_contents($txtFile, $txtContent);
        @file_put_contents($jsonFile, $jsonContent);

        $metaFile = rtrim($cacheDir, '/') . '/_meta.json';
        $meta = array(
            'generated_at' => time(),
            'count' => count($this->toChannelList($verifiedSources)),
            'files' => array(
                'playlist.m3u' => $m3uFile,
                'playlist.txt' => $txtFile,
                'playlist.json' => $jsonFile
            )
        );
        @file_put_contents($metaFile, json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        return array(
            'm3u' => $m3uFile,
            'txt' => $txtFile,
            'json' => $jsonFile,
            'meta' => $metaFile
        );
    }

    public function getSingleChannelPlaylist($id, $verifiedSources)
    {
        if (empty($id)) {
            return "#EXTM3U\n#ERROR: Channel ID required\n";
        }

        $flatList = $this->toChannelList($verifiedSources);
        $found = null;

        foreach ($flatList as $ch) {
            $chid = isset($ch['id']) ? $ch['id'] : '';
            if ($chid === $id) {
                $found = $ch;
                break;
            }
        }

        if ($found === null) {
            $idLower = function_exists('mb_strtolower') ? mb_strtolower($id, 'UTF-8') : strtolower($id);
            foreach ($flatList as $ch) {
                $chid = isset($ch['id']) ? $ch['id'] : '';
                $chidLower = function_exists('mb_strtolower') ? mb_strtolower($chid, 'UTF-8') : strtolower($chid);
                $chnName = isset($ch['name']) ? $ch['name'] : '';
                $chnNameLower = function_exists('mb_strtolower') ? mb_strtolower($chnName, 'UTF-8') : strtolower($chnName);
                if ($chidLower === $idLower || strpos($chnNameLower, $idLower) !== false) {
                    $found = $ch;
                    break;
                }
            }
        }

        if ($found === null) {
            return "#EXTM3U\n#ERROR: Channel not found: " . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . "\n";
        }

        $output = "#EXTM3U\n";
        $output .= "#EXT-X-VERSION:3\n";
        $output .= "#Single-Channel: " . htmlspecialchars(isset($found['name']) ? $found['name'] : $id, ENT_QUOTES, 'UTF-8') . "\n";
        $output .= "#Generated-At: " . date('Y-m-d H:i:s') . "\n\n";

        $cname = $this->esc(isset($found['name']) ? $found['name'] : $id);
        $cgid = $this->esc(isset($found['id']) ? $found['id'] : $id);
        $cgroup = $this->esc(isset($found['group']) ? $found['group'] : '');
        $clogo = $this->esc(isset($found['logo']) ? $found['logo'] : '');

        $mainUrl = isset($found['url']) ? $found['url'] : '';
        $urls = array();
        if (isset($found['urls']) && is_array($found['urls'])) {
            $urls = $found['urls'];
        }
        if (isset($found['sources']) && is_array($found['sources'])) {
            foreach ($found['sources'] as $src) {
                if (isset($src['url']) && !in_array($src['url'], $urls)) {
                    $urls[] = $src['url'];
                }
            }
        }
        if (!empty($mainUrl) && !in_array($mainUrl, $urls)) {
            array_unshift($urls, $mainUrl);
        }
        $urls = array_values(array_filter($urls));

        if (empty($urls)) {
            $output .= "#ERROR: No stream URLs available\n";
            return $output;
        }

        for ($i = 0; $i < count($urls); $i++) {
            $extInf = '#EXTINF:-1';
            if (!empty($cgid)) {
                $extInf .= ' tvg-id="' . $cgid . '"';
            }
            if (!empty($cname)) {
                $extInf .= ' tvg-name="' . $cname . '"';
            }
            if (!empty($clogo)) {
                $extInf .= ' tvg-logo="' . $clogo . '"';
            }
            if (!empty($cgroup)) {
                $extInf .= ' group-title="' . $cgroup . '"';
            }
            if ($i > 0) {
                $extInf .= ' backup="true"';
            }
            $extInf .= ',' . $cname;
            $output .= $extInf . "\n";
            $output .= $urls[$i] . "\n";
        }

        return $output;
    }

    public function outputSingleChannelHttp($id, $verifiedSources)
    {
        $content = $this->getSingleChannelPlaylist($id, $verifiedSources);
        if (!headers_sent()) {
            header('Content-Type: application/vnd.apple.mpegurl; charset=utf-8');
            header('Cache-Control: no-cache, must-revalidate');
            header('Content-Disposition: inline; filename="channel_' . rawurlencode($id) . '.m3u8"');
        }
        return $content;
    }

    private function toChannelList($verifiedSources)
    {
        if (empty($verifiedSources)) {
            return array();
        }

        if (isset($verifiedSources['sources']) && is_array($verifiedSources['sources'])) {
            return $this->toChannelList($verifiedSources['sources']);
        }

        if (isset($verifiedSources[0]) && is_array($verifiedSources[0])) {
            $first = $verifiedSources[0];
            $isGrouped = false;
            if (isset($first['sources']) && is_array($first['sources']) && !isset($first['url'])) {
                $isGrouped = true;
            }
            if (isset($first[0]) && is_array($first[0])) {
                $isGrouped = true;
            }

            if ($isGrouped) {
                $result = array();
                foreach ($verifiedSources as $groupId => $group) {
                    if (is_array($group)) {
                        if (isset($group['sources']) || isset($group['url']) || isset($group['id'])) {
                            $result[] = $group;
                        } else {
                            foreach ($group as $sub) {
                                if (is_array($sub)) {
                                    $result[] = $sub;
                                }
                            }
                        }
                    }
                }
                return $result;
            }
            return $verifiedSources;
        }

        if (is_array($verifiedSources) && (isset($verifiedSources['id']) || isset($verifiedSources['url']))) {
            return array($verifiedSources);
        }

        return array();
    }

    private function esc($str)
    {
        if ($str === null) {
            return '';
        }
        $str = (string)$str;
        $str = str_replace(array('\\', '"'), array('\\\\', '\\"'), $str);
        $str = preg_replace('/[\x00-\x1F\x7F]/u', '', $str);
        return $str;
    }

    public function getMeta($cacheDir = null)
    {
        if (empty($cacheDir)) {
            $cacheDir = $this->cacheDir;
        }
        $metaFile = rtrim($cacheDir, '/') . '/_meta.json';
        if (!file_exists($metaFile)) {
            return null;
        }
        $data = @json_decode(@file_get_contents($metaFile), true);
        return is_array($data) ? $data : null;
    }

    public function isCacheFresh($cacheDir = null, $ttlSeconds = 3600)
    {
        $meta = $this->getMeta($cacheDir);
        if ($meta === null) {
            return false;
        }
        $genAt = isset($meta['generated_at']) ? (int)$meta['generated_at'] : 0;
        if ($genAt <= 0) {
            return false;
        }
        return (time() - $genAt) <= $ttlSeconds;
    }

    public function loadFromCache($format = 'json', $cacheDir = null)
    {
        if (empty($cacheDir)) {
            $cacheDir = $this->cacheDir;
        }
        $cacheDir = rtrim($cacheDir, '/');

        $fileMap = array(
            'm3u' => '/playlist.m3u',
            'txt' => '/playlist.txt',
            'json' => '/playlist.json'
        );

        if (!isset($fileMap[$format])) {
            return false;
        }

        $file = $cacheDir . $fileMap[$format];
        if (!file_exists($file)) {
            return false;
        }

        return @file_get_contents($file);
    }
}
