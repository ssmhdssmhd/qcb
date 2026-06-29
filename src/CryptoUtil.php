<?php

class CryptoUtil
{
    private static $defaultKey = 'm3u8_ad_skipper_secret_key_2024';
    private static $defaultIv = 'm3u8_ad_skipper_iv';

    public static function encrypt($data, $key = null, $iv = null)
    {
        $key = $key ?: self::$defaultKey;
        $iv = $iv ?: substr(self::$defaultIv, 0, 16);
        
        $key = substr(hash('sha256', $key), 0, 32);
        $iv = substr(hash('sha256', $iv), 0, 16);
        
        $encrypted = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        
        return self::base64UrlEncode($encrypted);
    }

    public static function decrypt($data, $key = null, $iv = null)
    {
        $key = $key ?: self::$defaultKey;
        $iv = $iv ?: substr(self::$defaultIv, 0, 16);
        
        $key = substr(hash('sha256', $key), 0, 32);
        $iv = substr(hash('sha256', $iv), 0, 16);
        
        $decrypted = openssl_decrypt(
            self::base64UrlDecode($data),
            'AES-256-CBC',
            $key,
            0,
            $iv
        );
        
        return $decrypted;
    }

    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    public static function generateSignature($data, $key = null)
    {
        $key = $key ?: self::$defaultKey;
        return hash_hmac('sha256', $data, $key);
    }

    public static function verifySignature($data, $signature, $key = null)
    {
        $key = $key ?: self::$defaultKey;
        $expected = hash_hmac('sha256', $data, $key);
        return hash_equals($expected, $signature);
    }

    public static function generateAuthCode($domain, $timestamp = null)
    {
        $timestamp = $timestamp ?: time();
        $data = $domain . '|' . $timestamp;
        $encrypted = self::encrypt($data);
        $signature = self::generateSignature($encrypted);
        return $encrypted . '.' . $signature;
    }

    public static function verifyAuthCode($authCode, &$domain = null, &$timestamp = null)
    {
        $parts = explode('.', $authCode);
        if (count($parts) !== 2) {
            return false;
        }
        
        list($encrypted, $signature) = $parts;
        
        if (!self::verifySignature($encrypted, $signature)) {
            return false;
        }
        
        $data = self::decrypt($encrypted);
        if (!$data) {
            return false;
        }
        
        $parts = explode('|', $data);
        if (count($parts) !== 2) {
            return false;
        }
        
        $domain = $parts[0];
        $timestamp = intval($parts[1]);
        
        return true;
    }
}
