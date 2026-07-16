<?php
/**
 * 超级嗅探 - 对外 API 接口
 *
 * 功能：接收前端视频链接，调用 server.php 进行解析+去广告，返回结构化 JSON
 * 用法：api.php?url=VIDEO_URL
 *
 * 返回结构示例：
 * {
 *   "code": 200,
 *   "msg": "解析成功",
 *   "data": {
 *     "original_url": "https://xxx.com/video.m3u8",
 *     "clean_url": "https://你的域名/xt/clean.php?id=abc123",
 *     "format": "m3u8",
 *     "has_ads": true,
 *     "ad_info": {
 *       "has_ads": true,
 *       "total_ads": 2,
 *       "total_ad_duration": 45.0,
 *       "details": [...],
 *       "video_info": {...}
 *     }
 *   }
 * }
 */

// api.php 直接 require server.php 执行（server.php 已处理全部逻辑并输出 JSON）
// $_GET['url'] 参数自动传递给 server.php
require __DIR__ . '/server.php';
