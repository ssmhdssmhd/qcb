<?php
// ==========================================================================
// v5.14.0 高性能缓存管理器：APCu(内存 L1) + 文件(L2) 二级缓存
// 目标：热数据 0.1ms 内命中，冷数据 < 1ms 读盘
// ==========================================================================

class CacheManager
{
    private $cacheDir;
    private $defaultTtl = 300;
    private static $apcuEnabled = null;
    private static $memoryCache = [];      // 同请求 L0 静态缓存
    private const MEM_CAP = 1024;          // 同请求最多 1024 条，防内存爆
    private const APCU_PREFIX = 'm3u8cm_'; // APCu key 前缀
    private const TTL_STATIC = 5;          // 同请求 L0 静态缓存 TTL（秒）

    public function __construct($cacheDir = null)
    {
        if ($cacheDir === null) {
            $cacheDir = __DIR__ . '/../cache';
        }
        $this->cacheDir = rtrim($cacheDir, '/');
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        $m3u8Dir = $this->cacheDir . '/m3u8';
        if (!is_dir($m3u8Dir)) {
            @mkdir($m3u8Dir, 0755, true);
        }
        // 一次性检测 APCu
        if (self::$apcuEnabled === null) {
            self::$apcuEnabled = (
                function_exists('apcu_enabled') &&
                @apcu_enabled() &&
                function_exists('apcu_fetch') &&
                function_exists('apcu_store')
            );
        }
    }

    /** 检测是否有内存缓存（对外诊断用） */
    public function hasMemoryCache(): bool { return self::$apcuEnabled; }

    public function get($key)
    {
        $now = time();
        // ========== L0: 同请求静态缓存（0.001ms 级） ==========
        if (isset(self::$memoryCache[$key]) && self::$memoryCache[$key]['e'] > $now) {
            return self::$memoryCache[$key]['d'];
        }

        // ========== L1: APCu 共享内存（0.1ms 级） ==========
        if (self::$apcuEnabled) {
            $apcuKey = self::APCU_PREFIX . $key;
            $succ = false;
            $raw = apcu_fetch($apcuKey, $succ);
            if ($succ && $raw !== false) {
                $cache = @unserialize($raw);
                if (is_array($cache) && isset($cache['e'], $cache['d']) && $cache['e'] > $now) {
                    $this->setL0($key, $cache['d'], $cache['e'] - $now);
                    return $cache['d'];
                }
                // 过期删除
                apcu_delete($apcuKey);
            }
        }

        // ========== L2: 文件（1ms 级） ==========
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) return null;
        $data = @file_get_contents($file);
        if ($data === false || $data === '') { @unlink($file); return null; }
        $cache = @unserialize($data);
        if ($cache === false || !isset($cache['e']) || !isset($cache['d'])) {
            @unlink($file); return null;
        }
        if ($now > (int)$cache['e']) { @unlink($file); return null; }
        $ttlLeft = (int)$cache['e'] - $now;
        // 回填 L1 + L0
        $this->setL0($key, $cache['d'], $ttlLeft);
        if (self::$apcuEnabled && $ttlLeft > 0) {
            @apcu_store(self::APCU_PREFIX . $key, serialize($cache), $ttlLeft);
        }
        return $cache['d'];
    }

    public function set($key, $data, $ttl = null)
    {
        if ($ttl === null) $ttl = $this->defaultTtl;
        $ttl = max(1, (int)$ttl);
        $expire = time() + $ttl;
        $payload = ['e' => $expire, 'd' => $data];

        // L0 回填
        $this->setL0($key, $data, $ttl);

        // L1 APCu 回填
        if (self::$apcuEnabled) {
            @apcu_store(self::APCU_PREFIX . $key, serialize($payload), $ttl);
        }

        // L2 文件
        $file = $this->getCacheFile($key);
        $content = serialize($payload);
        $tmpFile = $file . '.tmp.' . uniqid('', true);
        $bytes = @file_put_contents($tmpFile, $content, LOCK_EX);
        if ($bytes === false) { @unlink($tmpFile); return false; }
        if (function_exists('opcache_invalidate')) @opcache_invalidate($tmpFile, true);
        $ok = @rename($tmpFile, $file);
        if (!$ok) {
            @unlink($tmpFile);
            return @file_put_contents($file, $content, LOCK_EX) !== false;
        }
        return true;
    }

    /** L0 静态缓存写入（限容量 LRU） */
    private function setL0(string $key, $data, int $ttlLeft): void {
        if (count(self::$memoryCache) >= self::MEM_CAP) {
            // 清掉最早 1/4（近似 LRU）
            $drop = (int)ceil(self::MEM_CAP / 4);
            self::$memoryCache = array_slice(self::$memoryCache, $drop, null, true);
        }
        self::$memoryCache[$key] = [
            'd' => $data,
            'e' => time() + min(self::TTL_STATIC, max(1, $ttlLeft)),
        ];
    }

    public function has($key)
    {
        return $this->get($key) !== null;
    }

    public function delete($key)
    {
        unset(self::$memoryCache[$key]);
        if (self::$apcuEnabled) {
            @apcu_delete(self::APCU_PREFIX . $key);
        }
        $file = $this->getCacheFile($key);
        if (file_exists($file)) return @unlink($file);
        return true;
    }

    public function clear()
    {
        self::$memoryCache = [];
        if (self::$apcuEnabled && function_exists('apcu_clear_cache')) {
            // 只清用户缓存
            if (function_exists('apcu_cache_info')) {
                $info = @apcu_cache_info();
                if (!empty($info['list']) && is_array($info['list'])) {
                    foreach ($info['list'] as $entry) {
                        if (isset($entry['info']) && strpos($entry['info'], self::APCU_PREFIX) === 0) {
                            @apcu_delete($entry['info']);
                        }
                    }
                }
            }
        }
        $this->clearDir($this->cacheDir . '/m3u8');
        return true;
    }

    private function clearDir($dir)
    {
        if (!is_dir($dir)) return;
        $files = glob($dir . '/*', GLOB_NOSORT);
        if ($files === false) return;
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->clearDir($file);
                @rmdir($file);
            } else {
                @unlink($file);
            }
        }
    }

    private function getCacheFile($key)
    {
        $hash = md5($key);
        $subDir = substr($hash, 0, 2);
        $dir = $this->cacheDir . '/m3u8/' . $subDir;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir . '/' . $hash . '.cache';
    }

    public function getM3U8CacheKey($url, $domain = '')
    {
        return 'm3u8_' . $domain . '_' . md5($url);
    }

    public function getM3U8Content($url, $domain = '')
    {
        $key = $this->getM3U8CacheKey($url, $domain);
        return $this->get($key);
    }

    public function setM3U8Content($url, $domain, $content, $ttl = 300)
    {
        $key = $this->getM3U8CacheKey($url, $domain);
        return $this->set($key, $content, $ttl);
    }

    /** 批量获取（减少调用开销） */
    public function getMulti(array $keys): array {
        $out = [];
        foreach ($keys as $k) $out[$k] = $this->get($k);
        return $out;
    }

    /** 批量设置 */
    public function setMulti(array $kvs, int $ttl = null): bool {
        $ok = true;
        foreach ($kvs as $k => $v) {
            if (!$this->set($k, $v, $ttl)) $ok = false;
        }
        return $ok;
    }
}
