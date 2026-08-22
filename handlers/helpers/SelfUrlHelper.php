<?php
/**
 * SelfUrlHelper —— 计算本服务自身的基础 URL
 *
 * 从 $_SERVER 推断协议 / host / 部署目录，供各解析 handler 拼接
 * mxjx / 其它内部接口地址使用。兼容 CLI（php -S）与常见 Web 部署。
 *
 * @package handlers
 * @since   5.14.0
 */
class SelfUrlHelper {

    /**
     * 计算 self base URL（如 https://host/base）
     *
     * @return string
     */
    public static function base() {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $requestUri = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
        $basePath = $requestUri ? dirname($requestUri) : '';
        $basePath = $basePath === '/' ? '' : $basePath;
        return $scheme . '://' . $host . $basePath;
    }

    /**
     * 拼接 mxjx 播放地址
     *
     * @param string $m3u8Url
     * @param bool   $deep    是否深度去广告
     * @return string
     */
    public static function mxjxUrl($m3u8Url, $deep = false) {
        $self = self::base();
        $url = $self . '/mx.php?action=mxjx&url=' . urlencode($m3u8Url);
        if ($deep) {
            $url = $self . '/mx.php?action=mxjx&deep=1&url=' . urlencode($m3u8Url);
        }
        return $url;
    }
}
