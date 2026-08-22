<?php
/**
 * UrlClassifier —— URL 类型判定
 *
 * 统一把输入 URL 归为三类，供门面层 dispatch：
 *   - m3u8      直链 .m3u8/.m3u → 走本地去广告
 *   - official  官方平台页面 → 走资源站优先（官替）
 *   - other     mp4/未知 → 走本地直链/去广告
 *
 * @package parse
 * @since   5.13.9
 */
class UrlClassifier {

    /** @var array */
    protected $cfg;

    public function __construct(array $cfg = []) {
        $this->cfg = $cfg;
    }

    /**
     * @param string $url
     * @return array{type:string, host:string, matched_domain:string}
     *      type ∈ m3u8|official|other
     */
    public function classify($url) {
        $url = trim((string)$url);
        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        $path = strtolower((string)parse_url($url, PHP_URL_PATH));
        $matchedDomain = '';

        // 1) 官方平台域名
        foreach ($this->cfg['official_domains'] ?? [] as $domain) {
            if ($host !== '' && strpos($host, $domain) !== false) {
                $matchedDomain = $domain;
                return ['type' => 'official', 'host' => $host, 'matched_domain' => $domain];
            }
        }

        // 2) m3u8 直链
        foreach ($this->cfg['m3u8_suffix'] ?? ['.m3u8'] as $suf) {
            if ($url !== '' && (substr($url, -strlen($suf)) === $suf || substr($path, -strlen($suf)) === $suf)) {
                return ['type' => 'm3u8', 'host' => $host, 'matched_domain' => ''];
            }
        }

        // 3) 直接视频/其它
        return ['type' => 'other', 'host' => $host, 'matched_domain' => $matchedDomain];
    }

    /** 是否为官方平台域名 */
    public function isOfficial($url) {
        return $this->classify($url)['type'] === 'official';
    }

    /** 是否为 m3u8 直链 */
    public function isM3u8($url) {
        return $this->classify($url)['type'] === 'm3u8';
    }
}