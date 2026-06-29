<?php

class CacheManager
{
    private $cacheDir;
    private $defaultTtl = 300;

    public function __construct($cacheDir = null)
    {
        if ($cacheDir === null) {
            $cacheDir = __DIR__ . '/../cache';
        }
        $this->cacheDir = rtrim($cacheDir, '/');
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
        if (!is_dir($this->cacheDir . '/m3u8')) {
            @mkdir($this->cacheDir . '/m3u8', 0755, true);
        }
    }

    public function get($key)
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return null;
        }
        $data = @file_get_contents($file);
        if ($data === false) {
            return null;
        }
        $cache = @unserialize($data);
        if ($cache === false || !isset($cache['expire']) || !isset($cache['data'])) {
            @unlink($file);
            return null;
        }
        if (time() > $cache['expire']) {
            @unlink($file);
            return null;
        }
        return $cache['data'];
    }

    public function set($key, $data, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }
        $file = $this->getCacheFile($key);
        $cache = [
            'expire' => time() + $ttl,
            'data' => $data
        ];
        $result = @file_put_contents($file, serialize($cache), LOCK_EX);
        return $result !== false;
    }

    public function has($key)
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return false;
        }
        $data = @file_get_contents($file);
        if ($data === false) {
            return false;
        }
        $cache = @unserialize($data);
        if ($cache === false || !isset($cache['expire'])) {
            @unlink($file);
            return false;
        }
        return time() <= $cache['expire'];
    }

    public function delete($key)
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return true;
    }

    public function clear()
    {
        $this->clearDir($this->cacheDir . '/m3u8');
        return true;
    }

    private function clearDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = glob($dir . '/*');
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
}
