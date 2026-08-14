<?php
/**
 * 嗅探统一解析入口 — 完全遵循 mxadmin.php 嗅探设置配置
 *
 * 用法（简洁调用，跟 jiexi.php 一样简单）：
 *   xt.php?url=视频链接                默认 JSON
 *   xt.php?wd=视频链接                 兼容 wd 参数（TVBox 旧配置）
 *   xt.php?v=视频链接                  兼容 v 参数
 *   xt.php?video=视频链接              兼容 video 参数
 *   xt.php?t=视频链接                  兼容 t 参数
 *
 * 可选参数：
 *   &type=json     默认，标准 JSON（含 official_url / replace_url 双通道独立结果）
 *   &type=302      302 跳转直链，播放器直接用
 *   &type=api      影视CMS标准格式 (code=1)
 *   &type=xml      XML 格式（老盒子用）
 *   &callback=xxx  JSONP 回调
 *
 * 注意：此入口 100% 走 xt/server.php 的 parseVideo()，严格使用 mxadmin 嗅探设置：
 *   - 嗅探模式 = concurrent（同时调用）→ 官解+官替一起跑，JSON 里同时输出 official_url + replace_url
 *   - 嗅探模式 = official（仅官解）→ 只跑官解接口
 *   - 嗅探模式 = replace（仅官替）→ 只跑官替接口
 *   官解接口（虾米 jx.xmflv.cc / type=html_player）由嗅探设置配置，无需改代码
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Range');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 兼容多种参数名
function xt_getVideoUrl(): string {
    $keys = ['url', 'wd', 'v', 'video', 't'];
    foreach ($keys as $k) {
        if (isset($_GET[$k]) && is_string($_GET[$k]) && $_GET[$k] !== '') {
            return trim($_GET[$k]);
        }
        if (isset($_POST[$k]) && is_string($_POST[$k]) && $_POST[$k] !== '') {
            return trim($_POST[$k]);
        }
    }
    return '';
}
function xt_getFormat(): string {
    $f = strtolower(trim((string)($_GET['type'] ?? $_POST['type'] ?? 'json')));
    return in_array($f, ['json','302','api','xml'], true) ? $f : 'json';
}
function xt_outputJson(array $data, ?string $callback): void {
    header('Content-Type: application/json; charset=utf-8');
    if ($callback !== null && $callback !== '') {
        $cb = preg_replace('/[^a-zA-Z0-9_.$\[\]]/', '', $callback);
        echo $cb . '(' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ');';
    } else {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
function xt_outputSuccess(string $playUrl, string $format, ?string $callback, string $parseTime = '0s', string $kfz = '超级嗅探|XT', string $zt = '解析成功', array $extraUrls = []): void {
    switch ($format) {
        case '302':
            header('Location: ' . $playUrl, true, 302);
            exit;
        case 'api':
            $data = ['code' => 1, 'msg' => '解析成功', 'url' => $playUrl];
            if (!empty($extraUrls['official_url'])) $data['official_url'] = $extraUrls['official_url'];
            if (!empty($extraUrls['replace_url']))  $data['replace_url']  = $extraUrls['replace_url'];
            xt_outputJson($data, $callback);
            break;
        case 'xml':
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<result>' . "\n";
            echo '  <code>1</code>' . "\n";
            echo '  <msg>解析成功</msg>' . "\n";
            echo '  <url><![CDATA[' . $playUrl . ']]></url>' . "\n";
            if (!empty($extraUrls['official_url'])) echo '  <official_url><![CDATA[' . $extraUrls['official_url'] . ']]></official_url>' . "\n";
            if (!empty($extraUrls['replace_url']))  echo '  <replace_url><![CDATA[' . $extraUrls['replace_url'] . ']]></replace_url>' . "\n";
            echo '</result>';
            break;
        default:
            $data = [
                'code' => 200,
                'ZT'   => $zt,
                'msg'  => $playUrl,
                'url'  => $playUrl,
                'time' => $parseTime,
                'KFZ'  => $kfz,
                'info' => 'XT 嗅探统一解析（遵循 mxadmin 嗅探设置）',
            ];
            if (!empty($extraUrls['official_url'])) $data['official_url'] = $extraUrls['official_url'];
            if (!empty($extraUrls['replace_url']))  $data['replace_url']  = $extraUrls['replace_url'];
            xt_outputJson($data, $callback);
    }
}
function xt_outputError(string $message, string $format, ?string $callback, string $parseTime = '0s', string $kfz = '超级嗅探|XT', string $zt = ''): void {
    switch ($format) {
        case '302':
            header('Content-Type: text/plain; charset=utf-8');
            echo $message; exit;
        case 'api':
            xt_outputJson(['code' => 0, 'msg' => $message, 'url' => ''], $callback); break;
        case 'xml':
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<result>' . "\n";
            echo '  <code>0</code>' . "\n";
            echo '  <msg>' . htmlspecialchars($message) . '</msg>' . "\n";
            echo '  <url></url>' . "\n";
            echo '</result>';
            break;
        default:
            xt_outputJson([
                'code' => 400,
                'ZT'   => $zt ?: $message,
                'msg'  => $message,
                'url'  => '',
                'time' => $parseTime,
                'KFZ'  => $kfz,
                'info' => 'XT 嗅探统一解析',
            ], $callback);
    }
}

$videoUrl = xt_getVideoUrl();
$format = xt_getFormat();
$callback = isset($_GET['callback']) ? trim($_GET['callback']) : null;

if (empty($videoUrl)) {
    xt_outputError('请提供视频链接，示例：xt.php?url=https://v.youku.com/v_show/id_XNjU0MjcxNTM1Ng==.html', $format, $callback);
    exit;
}
if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
    xt_outputError('链接格式不正确', $format, $callback);
    exit;
}

require_once __DIR__ . '/xt/server.php';

$result = parseVideo($videoUrl);

$parseTime = isset($result['time']) ? $result['time'] : '0s';
$kfz = isset($result['KFZ']) ? $result['KFZ'] : '超级嗅探|XT';
$zt  = isset($result['ZT'])  ? $result['ZT']  : '';

// 同时调用模式下，读取双通道独立结果
$extraUrls = [];
if (!empty($GLOBALS['XT_CONCURRENT_RESULTS'])) {
    $cr = $GLOBALS['XT_CONCURRENT_RESULTS'];
    if (!empty($cr['official_url'])) $extraUrls['official_url'] = $cr['official_url'];
    if (!empty($cr['replace_url']))  $extraUrls['replace_url']  = $cr['replace_url'];
}

if ($result['code'] !== 200 || empty($result['url'])) {
    xt_outputError($result['msg'] ?: '解析失败', $format, $callback, $parseTime, $kfz, $zt);
    exit;
}

xt_outputSuccess($result['url'], $format, $callback, $parseTime, $kfz, $zt, $extraUrls);
