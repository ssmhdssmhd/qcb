<?php
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// 如果请求的是文件，直接返回
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false;
}

// 默认路由到 index.php
$_SERVER['SCRIPT_NAME'] = '/index.php';
include __DIR__ . '/index.php';
