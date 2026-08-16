<?php
// ==========================================================================
// v5.14.0 加密工具：AES-256-GCM 认证加密 + HKDF 密钥派生 + XXH128 指纹
// 向后兼容旧的 AES-256-CBC 解密（decrypt 自动识别新旧格式）
// ==========================================================================

class CryptoUtil
{
    /** 旧版兼容 key（保持 100% 向后兼容） */
    private static $legacyKey = 'm3u8_ad_skipper_secret_key_2024';
    private static $legacyIv  = 'm3u8_ad_skipper_iv';

    /** 新版主密钥根（pepper，派生用，不直接加密） */
    private const ROOT_PEPPER = "\x9a\xF7\x1c\xB2\x8e\xD3\x44\xA1\x92\x4f\x0b\xE5\xC6\x3a\x7d\x5f\x1b\x88\x6c\x9e\x2a\x04\xdd\x7b\xc1\x2f\x8a\x63\x5b\x4e\x97\x3c";
    /** 新版 magic header（5B），用于 decrypt 自动识别 */
    private const MAGIC_V2 = "\x00GCM2";
    /** 参数版本 */
    private const ALGO = 'aes-256-gcm';

    // =====================================================
    //  新版 AES-256-GCM（推荐 encryptV2 / decryptV2，带认证标签）
    // =====================================================

    /**
     * AES-256-GCM 认证加密：返回 base64url 安全字符串
     * 结构：MAGIC(5) + salt(16) + nonce(12) + tag(16) + tag_mac(32) + ciphertext
     */
    public static function encryptV2($data, string $context = 'default'): string
    {
        $salt  = random_bytes(16);
        $nonce = random_bytes(12);
        [$encKey, $authKey] = self::hkdfDerive($salt, $context);

        $plain = is_string($data) ? $data : serialize($data);
        $aad   = self::makeAad($context, $salt, $nonce);
        $tag   = '';
        $cipher = openssl_encrypt(
            $plain,
            self::ALGO,
            $encKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad,
            16
        );
        if ($cipher === false || strlen($tag) !== 16) {
            throw new RuntimeException('AES-GCM 加密失败');
        }
        // 绑定 authKey：对 tag + cipher 再做一次 HMAC-SHA256，防御 AES-NI 时序侧信道
        $tagMac = hash_hmac('sha256', $tag . $cipher, $authKey, true);
        if (strlen($tagMac) !== 32) {
            throw new RuntimeException('HMAC 长度异常');
        }
        $blob = self::MAGIC_V2 . $salt . $nonce . $tag . $tagMac . $cipher;
        return self::base64UrlEncode($blob);
    }

    /** AES-256-GCM 解密：成功返回 string，失败抛异常（非静默） */
    public static function decryptV2(string $token, string $context = 'default')
    {
        $blob = self::base64UrlDecode($token);
        if (!is_string($blob) || strlen($blob) < 5 + 16 + 12 + 16 + 32 + 1) {
            throw new RuntimeException('密文格式过短');
        }
        if (substr($blob, 0, 5) !== self::MAGIC_V2) {
            throw new RuntimeException('密文 magic 不匹配');
        }
        $p     = 5;
        $salt  = substr($blob, $p, 16); $p += 16;
        $nonce = substr($blob, $p, 12); $p += 12;
        $tag   = substr($blob, $p, 16); $p += 16;
        $tagMac= substr($blob, $p, 32); $p += 32;
        $cipher= substr($blob, $p);

        [$encKey, $authKey] = self::hkdfDerive($salt, $context);
        $expectMac = hash_hmac('sha256', $tag . $cipher, $authKey, true);
        if (!hash_equals($expectMac, $tagMac)) {
            throw new RuntimeException('密文完整性校验失败（可能被篡改）');
        }
        $aad = self::makeAad($context, $salt, $nonce);
        $plain = openssl_decrypt(
            $cipher,
            self::ALGO,
            $encKey,
            OPENSSL_RAW_DATA,
            $nonce,
            $tag,
            $aad
        );
        if ($plain === false) {
            throw new RuntimeException('AES-GCM 认证解密失败');
        }
        return $plain;
    }

    // =====================================================
    //  旧版 API 兼容（v1）：encrypt/decrypt 自动升级
    // =====================================================

    public static function encrypt($data, $key = null, $iv = null)
    {
        // 优先 V2 GCM（若调用方用默认 key/iv 且数据是字符串）
        if ($key === null && $iv === null) {
            try { return self::encryptV2($data, 'legacy_compat'); } catch (\Throwable $e) { /* 下沉旧版 */ }
        }
        // 旧版 CBC
        $k = $key ?: self::$legacyKey;
        $i = $iv  ?: substr(self::$legacyIv, 0, 16);
        $k = substr(hash('sha256', $k, true), 0, 32);
        $i = substr(hash('sha256', $i,  true), 0, 16);
        $plain = is_string($data) ? $data : serialize($data);
        $enc = @openssl_encrypt($plain, 'AES-256-CBC', $k, 0, $i);
        return self::base64UrlEncode($enc);
    }

    public static function decrypt($data, $key = null, $iv = null)
    {
        // V2 GCM 尝试
        $blob = self::base64UrlDecode($data);
        if (is_string($blob) && strlen($blob) >= 5 && substr($blob, 0, 5) === self::MAGIC_V2) {
            try { return self::decryptV2($data, 'legacy_compat'); } catch (\Throwable $e) { /* fallthrough */ }
        }
        // 旧版 CBC
        $k = $key ?: self::$legacyKey;
        $i = $iv  ?: substr(self::$legacyIv, 0, 16);
        $k = substr(hash('sha256', $k, true), 0, 32);
        $i = substr(hash('sha256', $i,  true), 0, 16);
        $plain = @openssl_decrypt(self::base64UrlDecode($data), 'AES-256-CBC', $k, 0, $i);
        if ($plain === false) return false;
        // 尝试反序列化，失败则返回原字符串
        $try = @unserialize($plain);
        return ($try !== false || $plain === serialize(false)) ? $try : $plain;
    }

    // =====================================================
    //  签名与指纹
    // =====================================================

    public static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64UrlDecode($data)
    {
        return base64_decode(strtr((string)$data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen((string)$data)) % 4));
    }

    public static function generateSignature($data, $key = null)
    {
        $payload = is_array($data) || is_object($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : (string)$data;
        $k = $key ?: self::rootKeyFor('sign');
        return hash_hmac('sha3-256', $payload, $k);
    }

    public static function verifySignature($data, $signature, $key = null)
    {
        $flags1 = JSON_UNESCAPED_UNICODE;
        $flags2 = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        $flags3 = 0;
        $p1 = is_array($data) || is_object($data) ? json_encode($data, $flags1) : (string)$data;
        $p2 = is_array($data) || is_object($data) ? json_encode($data, $flags2) : (string)$data;
        $p3 = is_array($data) || is_object($data) ? json_encode($data, $flags3) : (string)$data;
        $k = $key ?: self::rootKeyFor('sign');

        // ===== 新版（优先）：SHA3-256 + JSON_UNESCAPED_UNICODE =====
        $expected = hash_hmac('sha3-256', $p1, $k);
        $sig = (string)$signature;
        if (hash_equals($expected, $sig)) return true;

        // ===== 旧版兼容：SHA-256 + 多 flags + 多 key =====
        $legacyKeys = [];
        if ($key !== null) {
            $legacyKeys[] = $key;
        } else {
            $legacyKeys[] = self::$legacyKey;                                   // 原始 key 字符串
            $legacyKeys[] = hash('sha256', self::$legacyKey, true);             // hash(SHA-256, key, true)  32B
            $legacyKeys[] = hash('sha256', self::$legacyKey);                   // hash(SHA-256, key)  hex 64B
        }
        foreach ([$p1, $p2, $p3] as $payload) {
            foreach ($legacyKeys as $lk) {
                $legacy = hash_hmac('sha256', $payload, $lk);
                if (hash_equals($legacy, $sig)) return true;
            }
        }
        return false;
    }

    /** 生成内容指纹：极快的 XXH128（PHP 8.1+），退化 SHA1 */
    public static function fingerprint($content): string
    {
        if (function_exists('hash_xxh128')) {
            return hash_xxh128((string)$content, 0xE1234567);
        }
        if (function_exists('hash') && in_array('xxh128', hash_algos(), true)) {
            return hash('xxh128', (string)$content);
        }
        return sha1((string)$content);
    }

    /** 授权码（新版 GCM） */
    public static function generateAuthCode($domain, $timestamp = null)
    {
        $timestamp = $timestamp ?: time();
        $payload = json_encode(['d' => $domain, 't' => $timestamp, 'r' => bin2hex(random_bytes(4))], JSON_UNESCAPED_UNICODE);
        return self::encryptV2($payload, 'authcode') . '.' . self::generateSignature($domain . '|' . $timestamp);
    }

    public static function verifyAuthCode($authCode, &$domain = null, &$timestamp = null)
    {
        $parts = explode('.', (string)$authCode);
        if (count($parts) !== 2) {
            // 兼容旧 CBC 授权码
            return self::verifyLegacyAuthCode($authCode, $domain, $timestamp);
        }
        [$token, $sig] = $parts;
        try {
            $json = self::decryptV2($token, 'authcode');
            $data = json_decode($json, true);
            if (!is_array($data) || empty($data['d']) || empty($data['t'])) return false;
            $domain    = $data['d'];
            $timestamp = (int)$data['t'];
            return self::verifySignature($domain . '|' . $timestamp, $sig);
        } catch (\Throwable $e) {
            return false;
        }
    }

    // =====================================================
    //  内部
    // =====================================================

    /** 旧 CBC 授权码兼容 */
    private static function verifyLegacyAuthCode($authCode, &$domain, &$timestamp): bool
    {
        $enc = (string)$authCode;
        $k = substr(hash('sha256', self::$legacyKey, true), 0, 32);
        $i = substr(hash('sha256', substr(self::$legacyIv, 0, 16), true), 0, 16);
        $plain = @openssl_decrypt(self::base64UrlDecode($enc), 'AES-256-CBC', $k, 0, $i);
        if (!is_string($plain)) return false;
        $ps = explode('|', $plain);
        if (count($ps) !== 2) return false;
        $domain    = $ps[0];
        $timestamp = (int)$ps[1];
        return true;
    }

    /** HKDF：派生 (encKey, authKey) 两键（RFC 5869 轻量实现） */
    private static function hkdfDerive(string $salt, string $context): array
    {
        $prk = hash_hmac('sha256', $salt . self::ROOT_PEPPER, $context . '::prk', true);
        $encKey  = substr(hash_hmac('sha256', chr(1) . 'enc|' . $context, $prk, true), 0, 32);
        $authKey = substr(hash_hmac('sha256', chr(2) . 'mac|' . $context, $prk, true), 0, 32);
        return [$encKey, $authKey];
    }

    private static function makeAad(string $context, string $salt, string $nonce): string
    {
        return 'AD|v1|' . $context . '|' . $salt . '|' . $nonce;
    }

    /** 场景化根 key 派生（不要直接用 pepper） */
    private static function rootKeyFor(string $scope): string
    {
        return hash_hmac('sha256', $scope, self::ROOT_PEPPER, true);
    }
}
