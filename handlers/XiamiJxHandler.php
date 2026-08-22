<?php
/**
 * XiamiJxHandler —— 虾米上游官解接口
 *
 * action：xiami_jx / xiami_jx/info
 * 迁移自 mx.php 的 parse_internal_xiami + xiami_jx case。
 * 说明：虾米官解为第三方上游接口，需签名；失败时明确返回 message，
 * 不再阻塞整条链路（上游官解仅作可选兜底）。
 *
 * @package handlers
 * @since   5.14.0
 */
class XiamiJxHandler extends BaseHandler {

    /** 上游 API 端点（按优先级） */
    const API_ENDPOINTS = [
        'https://cache.0567890.xyz:4433/Api',
        'https://cache.hls.one/Api',
    ];

    /** 固定 IV */
    const IV = 'fUU9eRmkYzsgbkEK';

    /**
     * 入口：xiami_jx / xiami_jx/info
     */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut([
                'code' => 400,
                'success' => false,
                'message' => '缺少 url 参数',
            ], 400);
        }

        $result = $this->api($url);

        if (empty($result['success']) || empty($result['play_url'])) {
            $this->jsonOut([
                'code' => 500,
                'success' => false,
                'message' => $result['message'] ?: '未获取到资源',
                'data' => null,
            ], 500);
        }

        $videoType = $result['video_type'] ?? '';
        $label = '';
        if (strpos($videoType, 'm3u8') !== false || strpos($videoType, 'hls') !== false) {
            $label = 'HLS';
        } elseif (strpos($videoType, 'mp4') !== false) {
            $label = 'MP4';
        }

        $this->jsonOut([
            'code' => 200,
            'success' => true,
            'message' => '解析成功',
            'data' => [
                'original_url' => $url,
                'media_url' => $result['play_url'],
                'type' => $videoType,
                'label' => $label,
                'source' => 'xiami',
            ],
        ]);
    }

    /**
     * 虾米官解核心（原 parse_internal_xiami）
     *
     * @param string $url
     * @return array
     */
    public function api($url) {
        $tm = intval(round(microtime(true) * 1000));
        $keyHex = md5($tm . $url);

        $aesKeyHex = md5($keyHex);
        $iv = self::IV;
        $plaintext = $keyHex;
        $blockSize = 16;
        $padLen = $blockSize - (strlen($plaintext) % $blockSize);
        if ($padLen == $blockSize) $padLen = 0;
        $padded = $plaintext . str_repeat("\x00", $padLen);
        $sign = @openssl_encrypt(
            $padded,
            'aes-256-cbc',
            $aesKeyHex,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );
        if ($sign !== false) {
            $sign = base64_encode($sign);
        }

        $playUrl = '';
        $lastError = '';
        $videoType = '';

        if (!empty($sign)) {
            $postData = [
                'tm'   => $tm,
                'url'  => $url,
                'key'  => $keyHex,
                'sign' => $sign,
            ];

            foreach (self::API_ENDPOINTS as $api) {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $api,
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
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curlError = curl_error($ch);
                curl_close($ch);

                if ($response === false || $httpCode !== 200) {
                    $lastError = $curlError ?: "HTTP $httpCode";
                    continue;
                }

                $body = str_replace('tg:@xmflv', '', $response);
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

                $ciphertext = base64_decode($json['data'], true);
                $decKey = $json['key'];
                $decIv = $json['iv'];

                if ($ciphertext === false || strlen($ciphertext) === 0) {
                    $lastError = '解密数据无效';
                    continue;
                }

                $keyLen = strlen($decKey);
                if ($keyLen <= 16) {
                    $method = 'aes-128-cbc';
                } elseif ($keyLen <= 24) {
                    $method = 'aes-192-cbc';
                } else {
                    $method = 'aes-256-cbc';
                }

                if ($keyLen < 16) {
                    $lastError = '密钥长度不足';
                    continue;
                }

                $decrypted = @openssl_decrypt(
                    $ciphertext,
                    $method,
                    $decKey,
                    OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
                    $decIv
                );

                if ($decrypted !== false && strlen($decrypted) > 0) {
                    $decrypted = rtrim($decrypted, "\x00");
                    $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                    $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
                } else {
                    $decrypted = @openssl_decrypt(
                        $ciphertext,
                        $method,
                        $decKey,
                        OPENSSL_RAW_DATA,
                        $decIv
                    );
                    if ($decrypted !== false) {
                        $decrypted = str_replace('tg:@xmflv', '', $decrypted);
                        $decrypted = rtrim($decrypted, "\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");
                    }
                }

                if ($decrypted === false || strlen($decrypted) === 0) {
                    $lastError = '解密失败';
                    continue;
                }

                $resultData = json_decode($decrypted, true);
                if ($resultData === null) {
                    $lastError = '解密数据解析失败';
                    continue;
                }

                $playUrl = isset($resultData['vurl']) ? $resultData['vurl'] : (isset($resultData['url']) ? $resultData['url'] : '');
                $videoType = isset($resultData['type']) ? $resultData['type'] : '';
                break;
            }
        }

        if (empty($playUrl)) {
            return [
                'success' => false,
                'code' => 500,
                'message' => $lastError ?: '未获取到资源',
                'play_url' => '',
                'video_type' => '',
                'label' => '',
            ];
        }

        $label = '';
        if (strpos($videoType, 'm3u8') !== false || strpos($videoType, 'hls') !== false) {
            $label = 'HLS';
        } elseif (strpos($videoType, 'mp4') !== false) {
            $label = 'MP4';
        }

        return [
            'success' => true,
            'code' => 200,
            'message' => '解析成功',
            'play_url' => $playUrl,
            'video_type' => $videoType,
            'label' => $label,
            'original_url' => $url,
            'source' => 'xiami',
        ];
    }
}
