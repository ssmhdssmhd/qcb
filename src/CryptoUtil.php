<?php

class CryptoUtil {

    private static $key = 'm3u8_ad_skipper_2026_secret_key!@#';
    private static $iv = 'm3u8_iv_20260628';

    public static function encrypt($data) {
        if (is_array($data)) {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        $method = 'AES-256-CBC';
        $key = hash('sha256', self::$key, true);
        $iv = substr(hash('sha256', self::$iv, true), 0, 16);
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    public static function decrypt($data) {
        $method = 'AES-256-CBC';
        $key = hash('sha256', self::$key, true);
        $iv = substr(hash('sha256', self::$iv, true), 0, 16);
        $decrypted = openssl_decrypt(base64_decode($data), $method, $key, OPENSSL_RAW_DATA, $iv);
        $json = json_decode($decrypted, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }
        return $decrypted;
    }

    public static function encodeUrl($url) {
        return self::base64UrlEncode($url);
    }

    public static function decodeUrl($encoded) {
        return self::base64UrlDecode($encoded);
    }

    public static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function sign($data, $secret = '') {
        if (is_array($data)) {
            ksort($data);
            $data = http_build_query($data);
        }
        $secret = $secret ?: self::$key;
        return md5($data . $secret);
    }

    public static function verifySign($data, $sign, $secret = '') {
        $calculated = self::sign($data, $secret);
        return hash_equals($calculated, $sign);
    }

    public static function generateToken($payload, $expire = 3600) {
        $payload['iat'] = time();
        $payload['exp'] = time() + $expire;
        $payload['sign'] = self::sign($payload);
        return self::base64UrlEncode(json_encode($payload));
    }

    public static function verifyToken($token) {
        try {
            $payload = json_decode(self::base64UrlDecode($token), true);
            if (!$payload || !isset($payload['sign'])) {
                return false;
            }
            $sign = $payload['sign'];
            unset($payload['sign']);
            if (!self::verifySign($payload, $sign)) {
                return false;
            }
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }
            return $payload;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function simpleObfuscate($str) {
        $result = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $result .= chr(ord($str[$i]) ^ ord(self::$key[$i % strlen(self::$key)]));
        }
        return base64_encode($result);
    }

    public static function simpleDeobfuscate($str) {
        $str = base64_decode($str);
        $result = '';
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $result .= chr(ord($str[$i]) ^ ord(self::$key[$i % strlen(self::$key)]));
        }
        return $result;
    }

    public static function hash256($data) {
        return hash('sha256', $data);
    }

    public static function hashMd5($data) {
        return md5($data);
    }

    public static function randomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
}
