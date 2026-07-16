<?php
/**
 * 超级嗅探 - 对外 API 接口
 * 
 * 功能：接收前端视频链接，解析视频播放地址
 * 腾讯视频采用JSONP方式绕过CORS：谁调用用谁IP
 * 
 * 用法：api.php?url=VIDEO_URL
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if (!isset($_GET['url']) || empty(trim($_GET['url']))) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 400, 'msg' => '请提供需要解析的链接'], JSON_UNESCAPED_UNICODE);
    exit;
}

$video_url = trim($_GET['url']);

if (!filter_var($video_url, FILTER_VALIDATE_URL)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 400, 'msg' => '链接格式不正确'], JSON_UNESCAPED_UNICODE);
    exit;
}

$host = parse_url($video_url, PHP_URL_HOST) ?? '';

if (preg_match('/v\.qq\.com/i', $host)) {
    handleTencentVideo($video_url);
    exit;
}

handleOtherVideo($video_url);


// ============================================================
//  腾讯视频处理（JSONP方式绕过CORS）
// ============================================================

function handleTencentVideo($video_url)
{
    if (isset($_GET['phase']) && $_GET['phase'] == '2') {
        processTencentApiData($_GET);
        return;
    }

    $vid = null;
    if (preg_match('/\/x\/cover\/\w+\/(\w+)\.html/i', $video_url, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/vid=(\w+)/i', $video_url, $m)) {
        $vid = $m[1];
    } elseif (preg_match('/\/x\/page\/(\w+)\.html/i', $video_url, $m)) {
        $vid = $m[1];
    }

    if (!$vid) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 500, 'msg' => '无法提取视频ID'], JSON_UNESCAPED_UNICODE);
        return;
    }

    $guid = str_pad((string)mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999) . mt_rand(100000, 999999), 32, '0', STR_PAD_LEFT);

    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';
    $mobileUa = 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1';

    $defnList = ['shd', 'fhd', 'hd', 'sd'];
    $apiHosts = [
        ['host' => 'https://vv.video.qq.com',  'ehost' => 'https://v.qq.com',  'ua' => $ua],
        ['host' => 'https://h5vv.video.qq.com', 'ehost' => 'https://m.v.qq.com', 'ua' => $mobileUa],
    ];

    $requests = [];
    foreach ($apiHosts as $apiInfo) {
        foreach ($defnList as $defn) {
            $apiUrl = "{$apiInfo['host']}/getinfo?vids={$vid}&platform=101001&charge=0&otype=json&defn={$defn}&guid={$guid}&ehost=" . urlencode($apiInfo['ehost']);
            $requests[] = $apiUrl;
        }
    }

    $callbackUrl = $_SERVER['REQUEST_URI'] . '&phase=2';

    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>视频解析中...</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; max-width: 400px; width: 100%; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        h2 { color: #333; margin-bottom: 15px; font-size: 20px; }
        .step { color: #666; font-size: 14px; margin-bottom: 5px; }
        .result { margin-top: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px; display: none; text-align: left; }
        .result.success { background: #d4edda; border: 1px solid #c3e6cb; }
        .result.error { background: #f8d7da; border: 1px solid #f5c6cb; }
        .result-title { font-weight: 600; margin-bottom: 10px; font-size: 16px; }
        .result.success .result-title { color: #155724; }
        .result.error .result-title { color: #721c24; }
        .video-url { word-break: break-all; font-size: 13px; color: #333; line-height: 1.6; }
        .copy-btn { margin-top: 15px; padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; }
        .copy-btn:hover { background: #5a6fd6; }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <h2>视频解析中...</h2>
        <div class="step" id="step1">阶段1：加载API参数... ✓</div>
        <div class="step" id="step2">阶段2：JSONP直连腾讯API...</div>
        <div class="step" id="step3">阶段3：处理视频数据...</div>
        <div id="result" class="result">
            <div class="result-title" id="resultTitle"></div>
            <div class="video-url" id="videoUrl"></div>
            <button class="copy-btn" id="copyBtn" onclick="copyUrl()">复制链接</button>
        </div>
    </div>

    <script>
        var requests = ['" . implode("', '", $requests) . "'];
        var callbackUrl = '$callbackUrl';
        var guid = '$guid';
        var vid = '$vid';
        var currentIndex = 0;

        function tryNextApi() {
            if (currentIndex >= requests.length) {
                showResult(false, '解析失败', '所有API端点均失败，请检查网络连接');
                return;
            }

            var url = requests[currentIndex];
            url += '&callback=__tencent_callback_' + currentIndex;

            var script = document.createElement('script');
            script.src = url;
            script.onerror = function() {
                currentIndex++;
                tryNextApi();
            };
            script.onload = function() {
                document.head.removeChild(script);
            };

            window['__tencent_callback_' + currentIndex] = function(data) {
                document.head.removeChild(script);
                if (data.em === 0 && data.vl && data.vl.vi && data.vl.vi.length > 0) {
                    document.getElementById('step2').textContent = '阶段2：JSONP直连腾讯API... ✓';
                    document.getElementById('step3').textContent = '阶段3：处理视频数据...';
                    sendData(data);
                } else {
                    currentIndex++;
                    tryNextApi();
                }
            };

            document.head.appendChild(script);
        }

        function sendData(apiData) {
            var apiDataStr = btoa(encodeURIComponent(JSON.stringify(apiData)));
            fetch(callbackUrl + '&api_data=' + apiDataStr + '&guid=' + guid + '&vid=' + vid)
                .then(function(resp) { return resp.json(); })
                .then(function(data) {
                    if (data.code === 200) {
                        showResult(true, '解析成功！', data.url);
                    } else {
                        showResult(false, '解析失败', data.message || '未知错误');
                    }
                })
                .catch(function(err) {
                    showResult(false, '请求失败', err.message);
                });
        }

        function showResult(success, title, content) {
            var result = document.getElementById('result');
            result.className = 'result ' + (success ? 'success' : 'error');
            document.getElementById('resultTitle').textContent = title;
            document.getElementById('videoUrl').textContent = content;
            document.getElementById('copyBtn').style.display = success ? 'block' : 'none';
            document.getElementById('step3').textContent = '阶段3：处理视频数据... ✓';
            result.style.display = 'block';
        }

        function copyUrl() {
            var text = document.getElementById('videoUrl').textContent;
            navigator.clipboard.writeText(text).then(function() {
                var btn = document.getElementById('copyBtn');
                btn.textContent = '已复制！';
                setTimeout(function() { btn.textContent = '复制链接'; }, 2000);
            }).catch(function() { alert('复制失败，请手动复制'); });
        }

        tryNextApi();
    </script>
</body>
</html>
HTML;
}

function processTencentApiData($params)
{
    header('Content-Type: application/json; charset=utf-8');

    $apiDataBase64 = $params['api_data'] ?? '';
    $apiData = base64_decode($apiDataBase64);
    if (!$apiData) {
        echo json_encode(['code' => 500, 'message' => 'API数据解码失败']);
        return;
    }

    $apiData = urldecode($apiData);
    $data = json_decode($apiData, true);
    if (!$data || !isset($data['vl']['vi'][0])) {
        echo json_encode(['code' => 500, 'message' => 'API数据格式错误']);
        return;
    }

    $vi = $data['vl']['vi'][0];
    $fn = $vi['fn'] ?? '';
    $servers = $vi['ul']['ui'] ?? [];
    $fvkey = $vi['fvkey'] ?? '';
    $vid = $params['vid'] ?? '';
    $guid = $params['guid'] ?? '';

    if (!$fn || empty($servers)) {
        echo json_encode(['code' => 500, 'message' => '未获取到文件名或服务器列表']);
        return;
    }

    $vkey = $fvkey;
    if (!$vkey && $vid && $guid) {
        $format = '2';
        if (preg_match('/\.f(\d+)\.mp4$/i', $fn, $m)) {
            $format = $m[1];
        }
        $keyUrl = "https://vv.video.qq.com/getkey?format={$format}&otype=json&vid={$vid}&guid={$guid}&filename={$fn}&platform=101001";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $keyUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_REFERER        => 'https://v.qq.com/',
            CURLOPT_TIMEOUT        => 10,
        ]);
        $resp2 = curl_exec($ch);
        curl_close($ch);

        if ($resp2) {
            $resp2 = preg_replace('/^QZOutputJson=/', '', $resp2);
            $resp2 = rtrim($resp2, ';');
            $data2 = json_decode($resp2, true);
            if ($data2 && isset($data2['key']) && ($data2['s'] ?? '') === 'o') {
                $vkey = $data2['key'];
            }
        }
    }

    if (!$vkey) {
        echo json_encode(['code' => 500, 'message' => '无法获取vkey']);
        return;
    }

    foreach ($servers as $server) {
        $serverUrl = $server['url'] ?? '';
        if (!$serverUrl) continue;
        $videoLink = $serverUrl . $fn . '?vkey=' . $vkey;
        echo json_encode(['code' => 200, 'url' => $videoLink]);
        return;
    }

    echo json_encode(['code' => 500, 'message' => '无可用CDN服务器']);
}


// ============================================================
//  其他视频平台处理
// ============================================================

function handleOtherVideo($video_url)
{
    header('Content-Type: application/json; charset=utf-8');

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $serverPhpPath = rtrim($scriptDir, '/') . '/server.php';

    $target_url = $protocol . '://' . $host . $serverPhpPath . '?url=' . urlencode($video_url);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $target_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo json_encode(['code' => 500, 'msg' => '解析服务请求失败: ' . $error]);
        return;
    }

    echo $response;
}