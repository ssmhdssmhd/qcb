<?php
/**
 * 虾米解析 (jx.xmflv.cc) API 调用脚本
 * 用法: xiami_jx.php?url=https://v.youku.com/v_show/id_xxx.html
 * 输出 JSON 数组
 *
 * API 来源: jx.xmflv.cc 网页播放器 → cache.0567890.xyz:4433/Api
 */

header('Content-Type: application/json; charset=utf-8');

$targetUrl = isset($_GET['url']) ? $_GET['url'] : '';
if (empty($targetUrl)) {
    echo json_encode([['code' => 400, 'msg' => '缺少 url 参数', 'url' => '']], JSON_UNESCAPED_UNICODE);
    exit;
}

$proxyEnabled = false;
$proxyMgr = null;
if (file_exists(__DIR__ . '/proxy/ProxyManager.php')) {
    require_once __DIR__ . '/proxy/ProxyManager.php';
    $proxyMgr = new ProxyManager();
    // 确保代理可用：如果代理池为空则自动从 proxy.scdn.io 获取（优先中国）
    $proxyMgr->ensureProxyAvailable();
    $proxyEnabled = $proxyMgr->isEnabled();
}

// ========== 配置 ==========
$apiEndpoints = [
    'https://cache.0567890.xyz:4433/Api',
    'https://cache.hls.one/Api',
    'https://jx.xmflv.cc/api.php',
    'https://jx.xmflv.com/api.php',
    'https://api.xmflv.cc/parse',
];

// ========== sign 签名（兼容 CryptoJS AES-256-CBC + ZeroPadding） ==========
function xiami_createSign($keyHex) {
    // aesKey = MD5(keyHex) 的 hex 字符串 → UTF-8 字节 = 32 bytes → AES-256
    $aesKeyHex = md5($keyHex);
    $iv = 'fUU9eRmkYzsgbkEK';

    // plaintext = keyHex (32 bytes, 已是 16 字节对齐)
    $plaintext = $keyHex;

    // ZeroPadding: 补齐到 16 字节倍数
    $blockSize = 16;
    $padLen = $blockSize - (strlen($plaintext) % $blockSize);
    if ($padLen == $blockSize) {
        $padLen = 0;
    }
    $padded = $plaintext . str_repeat("\x00", $padLen);

    $encrypted = openssl_encrypt(
        $padded,
        'aes-256-cbc',
        $aesKeyHex,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv
    );

    if ($encrypted === false) {
        return '';
    }
    return base64_encode($encrypted);
}

// ========== HTTP POST（curl + 浏览器伪装头 + 代理支持 + 自动切换） ==========
function xiami_httpPost($url, $postData, $proxyMgr = null) {
    $maxRetries = 3;

    for ($retry = 0; $retry < $maxRetries; $retry++) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Accept: application/json, text/javascript, */*; q=0.01',
                'Origin: https://jx.xmflv.cc',
                'Referer: https://jx.xmflv.cc/',
                'X-Requested-With: XMLHttpRequest',
            ],
        ]);

        $usedProxyId = null;
        if ($proxyMgr !== null && $proxyMgr->isEnabled()) {
            $proxy = $proxyMgr->getProxy();
            if ($proxy !== null) {
                $usedProxyId = $proxy['id'] ?? null;
                $proxyType = strtoupper($proxy['type']);
                $proxyAuth = '';
                if (!empty($proxy['username'])) {
                    $proxyAuth = urlencode($proxy['username']) . ':' . urlencode($proxy['password']) . '@';
                }
                curl_setopt($ch, CURLOPT_PROXY, "$proxyType://$proxyAuth{$proxy['host']}:{$proxy['port']}");
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error    = curl_error($ch);
        curl_close($ch);

        // 请求成功
        if ($response !== false && $httpCode === 200) {
            if ($proxyMgr !== null && $usedProxyId !== null) {
                $proxyMgr->markProxySuccess($usedProxyId);
            }
            return ['body' => $response];
        }

        // 请求失败：标记代理失败
        if ($proxyMgr !== null && $usedProxyId !== null) {
            $proxyMgr->markProxyFailed($usedProxyId);
        }

        // 检测是否被ban（HTTP 500 + 特征关键词）
        $isBanned = ($httpCode === 500 || (is_string($response) && (
            stripos($response, 'ban') !== false
        )));

        // 非ban错误且非代理连接问题，直接返回错误
        if (!$isBanned && $proxyMgr === null) {
            return ['error' => $error ?: "HTTP $httpCode"];
        }

        // ban错误或代理失败：自动刷新代理并重试
        if ($isBanned && $proxyMgr !== null) {
            $proxyMgr->ensureProxyAvailable();
        }
    }

    return ['error' => $error ?: "HTTP $httpCode（重试{$maxRetries}次后仍失败）"];
}

// ========== 响应解密（AES-CBC + ZeroPadding，兼容 CryptoJS） ==========
function xiami_decryptVideoData($data, $key, $iv) {
    $ciphertext = base64_decode($data, true);
    if ($ciphertext === false || strlen($ciphertext) === 0) {
        return ['_debug' => "base64_decode_failed: data_len=" . strlen($data)];
    }

    // 根据密钥长度选择 AES 算法
    $keyLen = strlen($key);
    if ($keyLen <= 16) {
        $method = 'aes-128-cbc';
    } elseif ($keyLen <= 24) {
        $method = 'aes-192-cbc';
    } else {
        $method = 'aes-256-cbc';
    }

    if ($keyLen < 16) {
        return ['_debug' => "key_too_short: key_len=$keyLen, key_hex=" . bin2hex($key)];
    }

    // 优先尝试 ZeroPadding（匹配 CryptoJS 解密）
    $decrypted = @openssl_decrypt(
        $ciphertext,
        $method,
        $key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
        $iv
    );

    $pad_ok = false;
    if ($decrypted !== false && strlen($decrypted) > 0) {
        // 去掉尾部 \x00（ZeroPadding）+ 尾部控制字符
        $decrypted = rtrim($decrypted, "\x00");
        // 去掉 "tg:@xmflv" 水印（可能在开头或嵌在尾部）
        $decrypted = str_replace('tg:@xmflv', '', $decrypted);
        // 去掉尾部其余不可见字符（\x01-\x08 \x09-\x0d \x0e-\x1f）
        $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
        $pad_ok = true;
    }

    if (!$pad_ok && $decrypted === false) {
        // 降级尝试 PKCS7
        $decrypted = @openssl_decrypt(
            $ciphertext,
            $method,
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );
        if ($decrypted !== false) {
            $decrypted = str_replace('tg:@xmflv', '', $decrypted);
            $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
        }
    }

    if ($decrypted === false || strlen($decrypted) === 0) {
        $err = openssl_error_string();
        return ['_debug' => "decrypt_failed: method=$method, key_len=$keyLen, key_hex=" . bin2hex($key) . ", iv_hex=" . bin2hex($iv) . ", ct_len=" . strlen($ciphertext) . ", ct_hex=" . substr(bin2hex($ciphertext), 0, 64) . ", openssl_err=" . ($err ?: 'unknown')];
    }

    $result = json_decode($decrypted, true);
    if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
        return ['_debug' => "json_decode_failed: " . json_last_error_msg() . ", decrypted_hex=" . bin2hex($decrypted)];
    }
    return $result;
}

// ========== 格式化时间 ==========
function xiami_now() {
    $weekMap = ['日', '一', '二', '三', '四', '五', '六'];
    return date('Y') . '年' . date('m') . '月' . date('d') . '日 星期' . $weekMap[date('w')] . ' ' . date('H:i:s');
}

// ========== 主逻辑 ==========
// PHP 5.x 兼容：用 intval 确保毫秒精度
$tm = intval(round(microtime(true) * 1000));
$keyHex = md5($tm . $targetUrl);
$sign = xiami_createSign($keyHex);

if (empty($sign)) {
    echo json_encode([['code' => 500, 'msg' => '签名计算失败', 'url' => '']], JSON_UNESCAPED_UNICODE);
    exit;
}

$postData = [
    'tm'   => $tm,
    'url'  => $targetUrl,
    'key'  => $keyHex,
    'sign' => $sign,
];

$decrypted = null;
$lastError = '';

foreach ($apiEndpoints as $api) {
    $result = xiami_httpPost($api, $postData, $proxyMgr);
    if (isset($result['error'])) {
        $lastError = $result['error'];
        continue;
    }

    $body = $result['body'];
    // 移除广告干扰字符串
    $body = str_replace('tg:@xmflv', '', $body);
    $json = json_decode($body, true);

    if ($json === null || !isset($json['code'])) {
        $lastError = '响应解析失败';
        continue;
    }

    if ($json['code'] !== 200) {
        $lastError = isset($json['msg']) ? $json['msg'] : '解析失败';
        continue;
    }

    if (empty($json['data']) || empty($json['key']) || empty($json['iv'])) {
        $lastError = '响应缺少 data/key/iv 字段';
        continue;
    }

    $decrypted = xiami_decryptVideoData($json['data'], $json['key'], $json['iv']);
    if ($decrypted !== null && !isset($decrypted['_debug'])) {
        break;
    }
    if (isset($decrypted['_debug'])) {
        $lastError = '解密失败: ' . $decrypted['_debug'];
    } else {
        $lastError = '解密失败';
    }
}

// ========== 输出 ==========
// 响应中播放地址字段可能是 vurl 或 url
$playUrl = isset($decrypted['vurl']) ? $decrypted['vurl'] : (isset($decrypted['url']) ? $decrypted['url'] : '');

if (empty($playUrl)) {
    echo json_encode([[
        'code'  => 500,
        'msg'   => $lastError ?: '未获取到资源',
        'url'   => '',
        'time'  => xiami_now(),
    ]], JSON_UNESCAPED_UNICODE);
    exit;
}

$type  = isset($decrypted['type']) ? $decrypted['type'] : '';
$label = '';
if (strpos($type, 'm3u8') !== false || strpos($type, 'hls') !== false) {
    $label = 'HLS';
} elseif (strpos($type, 'mp4') !== false) {
    $label = 'MP4';
}

echo json_encode([[
    'code'  => 200,
    'msg'   => '解析成功',
    'type'  => $type,
    'label' => $label,
    'url'   => $playUrl,
    'time'  => xiami_now(),
]], JSON_UNESCAPED_UNICODE);
