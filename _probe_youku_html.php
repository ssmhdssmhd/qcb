<?php
/**
 * Probe: 下载优酷该链接的 HTML，打印所有可能放剧名的字段
 *   - og:title / twitter:title / <title>
 *   - meta description
 *   - application/ld+json
 *   - window.__INITIAL_STATE__ / window.DATA / window.YKU_PRE / ... 等内联 JSON
 *   - 所有 class 包含 "title" 的 h1/h2/div
 */
declare(strict_types=1);

$url = 'https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html';

echo "=== 探测 URL: $url ===\n\n";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (Linux; Android 12; Pixel 6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
    CURLOPT_HTTPHEADER     => [
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language: zh-CN,zh;q=0.9',
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_ENCODING       => 'gzip, deflate, br',
]);
$html = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if (!$html) {
    echo "HTTP FAIL: code=$httpCode err=$err\n";
    exit(1);
}

echo "HTTP $httpCode, length=" . strlen($html) . "\n\n";

// -------- 1. meta tags --------
echo "===== 1. META TAGS =====\n";
$tags = [
    'og:title'       => '~<meta\s+property=["\']og:title["\'][^>]+content=["\']([^"\']+)["\']~si',
    'og:title(rev)'  => '~<meta\s+content=["\']([^"\']+)["\'][^>]+property=["\']og:title["\']~si',
    'twitter:title'  => '~<meta\s+name=["\']twitter:title["\'][^>]+content=["\']([^"\']+)["\']~si',
    'description'    => '~<meta\s+name=["\']description["\'][^>]+content=["\']([^"\']+)["\']~si',
    'og:description' => '~<meta\s+property=["\']og:description["\'][^>]+content=["\']([^"\']+)["\']~si',
    'og:type'        => '~<meta\s+property=["\']og:type["\'][^>]+content=["\']([^"\']+)["\']~si',
    'og:video:show'  => '~<meta\s+property=["\']og:video:show["\'][^>]+content=["\']([^"\']+)["\']~si',
    'og:video:series'=> '~<meta\s+property=["\']og:video:series["\'][^>]+content=["\']([^"\']+)["\']~si',
    'video:show'     => '~<meta\s+property=["\']video:show["\'][^>]+content=["\']([^"\']+)["\']~si',
    'video:series'   => '~<meta\s+property=["\']video:series["\'][^>]+content=["\']([^"\']+)["\']~si',
    'video:album'    => '~<meta\s+property=["\']video:album["\'][^>]+content=["\']([^"\']+)["\']~si',
    'tv:series'      => '~<meta\s+property=["\']tv:series["\'][^>]+content=["\']([^"\']+)["\']~si',
];
foreach ($tags as $k => $re) {
    if (preg_match($re, $html, $m)) {
        echo "[$k] " . trim($m[1]) . "\n";
    }
}

// -------- 2. <title> --------
echo "\n===== 2. <title> =====\n";
if (preg_match('~<title[^>]*>([^<]+)</title>~is', $html, $m)) {
    echo trim($m[1]) . "\n";
}

// -------- 3. ld+json --------
echo "\n===== 3. application/ld+json =====\n";
if (preg_match_all('~<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>~is', $html, $mm)) {
    $idx = 0;
    foreach ($mm[1] as $json) {
        $idx++;
        $data = json_decode(trim($json), true);
        if (!$data) {
            echo "  [ld#$idx] json decode fail\n";
            continue;
        }
        echo "  [ld#$idx] @type=" . ($data['@type'] ?? '?') . "\n";
        walk_ld_print($data, "    ");
    }
}

// -------- 4. 内联 window.* JSON --------
echo "\n===== 4. window.XXX 内联 JSON 关键字段（albumName/showName/seriesName/partOfSeries）=====\n";
$patterns = [
    'albumName'       => '~["\']?albumName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'showName'        => '~["\']?showName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'seriesName'      => '~["\']?seriesName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'videoSeriesName' => '~["\']?videoSeriesName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'tvName'          => '~["\']?tvName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'videoAlbumName'  => '~["\']?videoAlbumName["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'videoAlbum'      => '~["\']?videoAlbum["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'mainTitle'       => '~["\']?mainTitle["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'pageTitle'       => '~["\']?pageTitle["\']?\s*[:=]\s*["\']([^"\'\\\\]{2,60})["\']~',
    'partOfSeries'    => '~["\']partOfSeries["\']\s*:\s*\{[^}]*["\']name["\']\s*:\s*["\']([^"\'\\\\]{2,60})["\']~s',
    'isPartOf'        => '~["\']isPartOf["\']\s*:\s*\{[^}]*["\']name["\']\s*:\s*["\']([^"\'\\\\]{2,60})["\']~s',
];
foreach ($patterns as $k => $re) {
    if (preg_match_all($re, $html, $mm2)) {
        $uniq = array_values(array_unique(array_filter($mm2[1], 'trim')));
        echo "  [$k] => " . implode(' | ', $uniq) . "\n";
    }
}

// -------- 5. h1/h2 class ~ title 等 --------
echo "\n===== 5. h1 / h2 / .title-like DOM =====\n";
$domPatterns = [
    '<h1>'  => '~<h1[^>]*>(.*?)</h1>~is',
    'class title' => '~class=["\'][^"\']*?\btitle\b[^"\']*?["\'][^>]*>(.{2,80}?)<~is',
    'class video-title' => '~class=["\'][^"\']*?\bvideo-title\b[^"\']*?["\'][^>]*>(.{2,80}?)<~is',
];
foreach ($domPatterns as $k => $re) {
    if (preg_match_all($re, $html, $mm3)) {
        $uniq = array_values(array_unique(array_map(function($s){ return trim(strip_tags(html_entity_decode($s,ENT_QUOTES,'UTF-8'))); }, $mm3[1])));
        $uniq = array_values(array_filter($uniq, fn($x)=>mb_strlen($x)>=2 && mb_strlen($x)<=80));
        if ($uniq) echo "  [$k] => " . implode(' || ', array_slice($uniq,0,5)) . "\n";
    }
}

function walk_ld_print($data, $prefix) {
    if (!is_array($data)) return;
    $interesting = ['name','headline','alternateName','partOfSeries','isPartOf','episodeNumber','numberOfEpisodes','description','videoSeason','season'];
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            if (in_array($k, $interesting, true)) {
                echo $prefix . $k . " =>\n";
                walk_ld_print($v, $prefix . "  ");
            } elseif (isset($v[0]) && is_array($v[0])) {
                walk_ld_print($v[0], $prefix);
            }
        } elseif (is_string($v) && in_array($k, $interesting, true)) {
            echo $prefix . $k . " = " . mb_substr($v,0,120) . "\n";
        }
    }
}
