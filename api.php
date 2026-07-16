<?php
/**
 * 超级嗅探 - 对外 API 接口
 * 
 * 功能：接收前端视频链接，调用本地 server.php 进行解析，返回视频播放地址
 * 支持两阶段解析（腾讯视频海外服务器适配）：
 *   阶段1：返回腾讯API请求参数，客户端（国内浏览器）直接调用
 *   阶段2：客户端回传API数据，服务器处理返回最终视频URL
 * 
 * 用法：api.php?url=VIDEO_URL
 *       api.php?url=VIDEO_URL&phase=2&api_data=BASE64_ENCODED_JSON
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

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$serverPhpPath = rtrim($scriptDir, '/') . '/server.php';

$target_url = $protocol . '://' . $host . $serverPhpPath . '?url=' . urlencode($video_url);

if (isset($_GET['phase']) && $_GET['phase'] == '2') {
    $target_url .= '&phase=2';
    if (isset($_GET['api_data'])) {
        $target_url .= '&api_data=' . urlencode($_GET['api_data']);
    }
    if (isset($_GET['guid'])) {
        $target_url .= '&guid=' . urlencode($_GET['guid']);
    }
}

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
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => 500, 'msg' => '解析服务请求失败: ' . $error], JSON_UNESCAPED_UNICODE);
    exit;
}

$responseData = json_decode($response, true);

if ($responseData && $responseData['code'] === 206) {
    header('Content-Type: text/html; charset=utf-8');
    $task = $responseData['task'];
    $requestsJson = json_encode($task['requests']);
    $callbackUrl = $task['callback'];
    $guid = $task['guid'];
    
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
        <div class="step" id="step1">阶段1：获取API参数... ✓</div>
        <div class="step" id="step2">阶段2：客户端直连腾讯API...</div>
        <div class="step" id="step3">阶段3：处理视频数据...</div>
        <div id="result" class="result">
            <div class="result-title" id="resultTitle"></div>
            <div class="video-url" id="videoUrl"></div>
            <button class="copy-btn" id="copyBtn" onclick="copyUrl()">复制链接</button>
        </div>
    </div>

    <script>
        const requests = $requestsJson;
        const callbackUrl = '$callbackUrl';
        const guid = '$guid';

        async function callTencentApi(reqs) {
            for (const req of reqs) {
                try {
                    const resp = await fetch(req.url, {
                        headers: {
                            'User-Agent': req.ua,
                            'Referer': req.referer,
                            'Accept': '*/*',
                        },
                        timeout: 10000,
                    });
                    if (!resp.ok) continue;
                    const text = await resp.text();
                    const jsonStr = text.replace(/^QZOutputJson=/, '').replace(/;$/, '');
                    const data = JSON.parse(jsonStr);
                    if (data.em === 0 && data.vl && data.vl.vi && data.vl.vi.length > 0) {
                        return data;
                    }
                } catch (e) {
                    continue;
                }
            }
            return null;
        }

        async function parse() {
            document.getElementById('step2').textContent = '阶段2：客户端直连腾讯API...';
            
            const apiData = await callTencentApi(requests);
            if (!apiData) {
                showResult(false, '解析失败', '无法获取腾讯API数据，请检查网络连接');
                return;
            }
            
            document.getElementById('step2').textContent = '阶段2：客户端直连腾讯API... ✓';
            document.getElementById('step3').textContent = '阶段3：处理视频数据...';
            
            const apiDataB64 = btoa(encodeURIComponent(JSON.stringify(apiData)));
            const finalResp = await fetch(callbackUrl + '&api_data=' + apiDataB64 + '&guid=' + guid);
            const finalData = await finalResp.json();
            
            if (finalData.code === 200) {
                showResult(true, '解析成功！', finalData.url);
            } else {
                showResult(false, '解析失败', finalData.message || '未知错误');
            }
        }

        function showResult(success, title, content) {
            const result = document.getElementById('result');
            result.className = 'result ' + (success ? 'success' : 'error');
            document.getElementById('resultTitle').textContent = title;
            document.getElementById('videoUrl').textContent = content;
            document.getElementById('copyBtn').style.display = success ? 'block' : 'none';
            document.getElementById('step3').textContent = '阶段3：处理视频数据... ✓';
            result.style.display = 'block';
        }

        function copyUrl() {
            const text = document.getElementById('videoUrl').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('copyBtn');
                btn.textContent = '已复制！';
                setTimeout(() => { btn.textContent = '复制链接'; }, 2000);
            }).catch(() => { alert('复制失败，请手动复制'); });
        }

        window.onload = parse;
    </script>
</body>
</html>
HTML;
    exit;
}

header('Content-Type: application/json; charset=utf-8');
echo $response;