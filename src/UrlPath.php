<?php
// ==========================================================================
// v5.14.0  URL / 路径标准化工具类
// 解决核心痛点：dirname('/') 返回 '.'，但 '/' REQUEST_URI 实际是空 base
//          + 拼接时空 '' + '/' => '//'  (播放端误报协议相对 URL)
//          + 多 '///' 规范化为单 '/'
// 对外：buildAbsoluteBase()  +  joinPath()  +  normalize()
// ==========================================================================

class UrlPath
{
    /**
     * 根据 $_SERVER 信息构建当前项目的「绝对基准」(scheme://host[/path_without_script])
     * 保证结尾绝对没有 '/'，后续 join 用 '/' 开头的相对路径不会出现 '//'
     *
     * 例： REQUEST_URI = '/mx.php?action=mxjx'  →  http://host
     *      REQUEST_URI = '/sub/mx.php?x=1'     →  http://host/sub
     *      REQUEST_URI = '/'                  →  http://host
     *      REQUEST_URI = '//evil?x'            →  http://host (防御)
     */
    public static function buildAbsoluteBase(?array $server = null): string
    {
        $server = $server ?? $_SERVER;
        $scheme = (isset($server['HTTPS']) && $server['HTTPS'] !== '' && $server['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $server['HTTP_HOST'] ?? (isset($server['SERVER_NAME']) ? $server['SERVER_NAME'] : 'localhost');
        if ($host === '') $host = 'localhost';

        $reqPath = '';
        if (!empty($server['REQUEST_URI'])) {
            $p = parse_url($server['REQUEST_URI'], PHP_URL_PATH);
            if (is_string($p) && $p !== '') $reqPath = $p;
        }
        // SCRIPT_NAME 比 REQUEST_URI 更可信（当有 rewrite 时也指向真正的 php 文件位置）
        $script = $server['SCRIPT_NAME'] ?? '';

        // 优先用 SCRIPT_NAME 推导「到项目目录」的 base path
        $basePath = '';
        if ($script !== '') {
            $d = dirname($script);
            if ($d === '.' || $d === '/' || $d === '\\') {
                $basePath = '';
            } else {
                $basePath = '/' . ltrim($d, '/');
                $basePath = rtrim($basePath, '/');
            }
        }
        return $scheme . '://' . $host . $basePath;
    }

    /**
     * 路径拼接：结尾无 '/' 的 base + 开头 '/' 的 path = 恰好一个 '/'
     * 支持任意组合 (base 尾 / 或不 /；path 头 / 或不 /) ，永远只出一个 '/'
     * 并且把 '///' 规范化为单 '/'
     */
    public static function join(string $base, string $path): string
    {
        if ($base === '') {
            return '/' . ltrim($path, '/');
        }
        if ($path === '') {
            return rtrim($base, '/');
        }
        return self::normalize(rtrim($base, '/') . '/' . ltrim($path, '/'));
    }

    /**
     * 只规范化路径段中的 '//' / '/./' / '/../'
     * 保留 scheme 和 host 原样不动
     */
    public static function normalize(string $urlOrPath): string
    {
        if ($urlOrPath === '') return '';

        // 分离 scheme://host + 后面的 path/query
        $pathPart = $urlOrPath;
        $prefix   = '';
        if (preg_match('#^(https?://[^/]*)?(.*)$#iD', $urlOrPath, $m)) {
            $prefix   = $m[1] ?? '';
            $pathPart = $m[2] ?? '';
        }
        if ($pathPart === '') return $prefix;

        // 拆 query
        $query = '';
        if (strpos($pathPart, '?') !== false) {
            [$pathPart, $query] = explode('?', $pathPart, 2);
            $query = '?' . $query;
        }

        // 1) 压缩 '///' → '/'
        $pathPart = preg_replace('#/+#', '/', $pathPart);
        // 2) 去掉 '/./'  和 尾 '/.'
        $pathPart = preg_replace('#/\.(?:/|$)#', '/', $pathPart);
        // 3) '/../' 回到上一层 (防御回溯到 DOCROOT 外)
        $parts = explode('/', $pathPart);
        $stack = [];
        $leadingSlash = ($parts[0] === '') ? '/' : '';
        if ($leadingSlash !== '') array_shift($parts);
        foreach ($parts as $seg) {
            if ($seg === '' || $seg === '.') continue;
            if ($seg === '..') {
                if (!empty($stack)) array_pop($stack);
                continue;
            }
            $stack[] = $seg;
        }
        $pathPart = $leadingSlash . implode('/', $stack);
        if ($pathPart === '') $pathPart = '/';

        return $prefix . $pathPart . $query;
    }
}
