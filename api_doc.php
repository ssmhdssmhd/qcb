<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$versionFile = __DIR__ . '/version.php';
$version = 'v1.0.0';
if (file_exists($versionFile)) {
    $vData = include $versionFile;
    if (is_array($vData) && isset($vData['version'])) {
        $version = $vData['version'];
    }
}

$basePath = dirname(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '');
$basePath = $basePath === '.' ? '' : $basePath;
$apiBase = $basePath . '/mx.php?action=';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API 文档 - M3U8 广告分析系统</title>
    <style>
        :root {
            --primary: #667eea;
            --primary-light: #764ba2;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-bg: #ecf5ff;
            --primary-text: #409eff;
            --bg-page: #f5f7fa;
            --bg-card: #ffffff;
            --text-primary: #303133;
            --text-regular: #606266;
            --text-secondary: #909399;
            --text-placeholder: #c0c4cc;
            --border-base: #dcdfe6;
            --border-light: #e4e7ed;
            --border-lighter: #ebeef5;
            --fill-light: #fafafa;
            --fill-lighter: #f5f7fa;
            --success: #67c23a;
            --success-light: #f0f9eb;
            --success-border: #e1f3d8;
            --warning: #e6a23c;
            --warning-light: #fdf6ec;
            --danger: #f56c6c;
            --danger-light: #fef0f0;
            --danger-border: #fbc4c4;
            --shadow-base: 0 2px 12px rgba(0,0,0,0.05);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.1);
            --code-bg: #282c34;
            --code-text: #abb2bf;
        }

        [data-theme="dark"] {
            --bg-page: #141414;
            --bg-card: #1f1f1f;
            --text-primary: #e8e8e8;
            --text-regular: #bfbfbf;
            --text-secondary: #8c8c8c;
            --border-base: #434343;
            --border-light: #303030;
            --border-lighter: #262626;
            --fill-light: #1f1f1f;
            --fill-lighter: #141414;
            --code-bg: #1e1e1e;
            --code-text: #d4d4d4;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 { font-size: 2em; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .header .version {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            display: flex;
            gap: 24px;
        }

        .sidebar {
            width: 280px;
            flex-shrink: 0;
            position: sticky;
            top: 20px;
            height: calc(100vh - 40px);
            overflow-y: auto;
        }

        .sidebar-card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-base);
            padding: 16px;
        }

        .sidebar-title {
            font-size: 1em;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-lighter);
        }

        .sidebar-list { list-style: none; }
        .sidebar-list li {
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            color: var(--text-regular);
            font-size: 0.9em;
            transition: all 0.2s;
            margin-bottom: 2px;
        }
        .sidebar-list li:hover {
            background: var(--fill-lighter);
            color: var(--primary-text);
        }
        .sidebar-list li.active {
            background: var(--primary-bg);
            color: var(--primary-text);
            font-weight: 500;
        }
        .sidebar-list .method {
            display: inline-block;
            width: 45px;
            font-size: 0.75em;
            font-weight: 600;
            text-align: center;
            padding: 2px 4px;
            border-radius: 3px;
            margin-right: 8px;
        }
        .method.get { background: #e1f3d8; color: #67c23a; }
        .method.post { background: #ecf5ff; color: #409eff; }

        [data-theme="dark"] .method.get { background: rgba(103,194,58,0.15); color: #95d475; }
        [data-theme="dark"] .method.post { background: rgba(64,158,255,0.15); color: #79bbff; }

        .content {
            flex: 1;
            min-width: 0;
        }

        .category {
            margin-bottom: 32px;
            scroll-margin-top: 20px;
        }

        .category-title {
            font-size: 1.5em;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .category-title .icon { font-size: 1.2em; }

        .api-card {
            background: var(--bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-base);
            margin-bottom: 16px;
            overflow: hidden;
            transition: box-shadow 0.2s;
        }
        .api-card:hover { box-shadow: var(--shadow-hover); }

        .api-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-lighter);
            cursor: pointer;
            user-select: none;
        }
        .api-header:hover { background: var(--fill-lighter); }

        .api-method {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 700;
            text-transform: uppercase;
        }
        .api-method.get { background: #f0f9eb; color: #67c23a; border: 1px solid #e1f3d8; }
        .api-method.post { background: #ecf5ff; color: #409eff; border: 1px solid #d9ecff; }

        [data-theme="dark"] .api-method.get { background: rgba(103,194,58,0.1); color: #95d475; border-color: rgba(103,194,58,0.2); }
        [data-theme="dark"] .api-method.post { background: rgba(64,158,255,0.1); color: #79bbff; border-color: rgba(64,158,255,0.2); }

        .api-path {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.95em;
            color: var(--text-primary);
            flex: 1;
        }

        .api-desc {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .api-arrow {
            color: var(--text-placeholder);
            transition: transform 0.2s;
            font-size: 0.8em;
        }
        .api-card.expanded .api-arrow { transform: rotate(90deg); }

        .api-body {
            display: none;
            padding: 20px;
        }
        .api-card.expanded .api-body { display: block; }

        .api-section { margin-bottom: 16px; }
        .api-section-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            font-size: 0.95em;
        }

        .param-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .param-table th, .param-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-lighter);
        }
        .param-table th {
            background: var(--fill-lighter);
            font-weight: 600;
            color: var(--text-regular);
        }
        .param-table td { color: var(--text-regular); }
        .param-required { color: var(--danger); font-weight: 600; }
        .param-optional { color: var(--text-placeholder); }

        .code-block {
            background: var(--code-bg);
            color: var(--code-text);
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.85em;
            line-height: 1.5;
            position: relative;
        }
        .code-block .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(255,255,255,0.1);
            color: var(--code-text);
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background 0.2s;
        }
        .code-block .copy-btn:hover { background: rgba(255,255,255,0.2); }

        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75em;
            font-weight: 500;
        }
        .tag.new { background: #f0f9eb; color: #67c23a; }
        .tag.hot { background: #fef0f0; color: #f56c6c; }
        .tag.stable { background: #ecf5ff; color: #409eff; }

        [data-theme="dark"] .tag.new { background: rgba(103,194,58,0.15); color: #95d475; }
        [data-theme="dark"] .tag.hot { background: rgba(245,108,108,0.15); color: #f89898; }
        [data-theme="dark"] .tag.stable { background: rgba(64,158,255,0.15); color: #79bbff; }

        .search-box {
            margin-bottom: 20px;
        }
        .search-input {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid var(--border-base);
            border-radius: 8px;
            font-size: 0.95em;
            background: var(--bg-card);
            color: var(--text-primary);
            outline: none;
            transition: border-color 0.2s;
        }
        .search-input:focus { border-color: var(--primary); }

        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--bg-card);
            border: 1px solid var(--border-base);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            box-shadow: var(--shadow-base);
            z-index: 100;
        }

        .back-btn {
            display: inline-block;
            color: white;
            text-decoration: none;
            opacity: 0.9;
            margin-bottom: 10px;
            font-size: 0.9em;
        }
        .back-btn:hover { opacity: 1; }

        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .sidebar {
                width: 100%;
                position: static;
                height: auto;
            }
        }

        .hidden { display: none !important; }
    </style>
</head>
<body>
    <div class="header">
        <a href="mxadmin.php" class="back-btn">← 返回后台</a>
        <h1>📚 API 接口文档</h1>
        <p>M3U8 广告分析系统 - 完整 API 参考手册</p>
        <div style="margin-top: 15px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <span class="version">版本 <?php echo $version; ?></span>
            <a href="api_helper.php" class="back-btn" style="background: rgba(255,255,255,0.2); padding: 6px 16px; border-radius: 20px;" download>
                🐘 下载 PHP 调用示例
            </a>
        </div>
    </div>

    <button class="theme-toggle" onclick="toggleTheme()" title="切换主题">🌓</button>

    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-card">
                <div class="sidebar-title">🔍 快速搜索</div>
                <div class="search-box">
                    <input type="text" class="search-input" id="searchInput" placeholder="搜索接口名称..." oninput="filterApis()">
                </div>
                <div class="sidebar-title">🐘 PHP 调用</div>
                <div style="margin-bottom: 16px;">
                    <a href="api_helper.php" download style="display:block;padding:10px 12px;background:var(--primary-bg);color:var(--primary-text);border-radius:6px;text-decoration:none;font-size:0.9em;text-align:center;">
                        📥 下载 PHP 调用示例库
                    </a>
                    <p style="font-size:0.8em;color:var(--text-secondary);margin-top:8px;line-height:1.4">
                        包含 30+ 封装好的接口调用函数，即插即用
                    </p>
                </div>
                <div class="sidebar-title">📑 接口分类</div>
                <ul class="sidebar-list" id="sidebarList">
                    <li class="active" onclick="scrollToCategory('analyze')">
                        <span class="method get">GET</span>视频分析
                    </li>
                    <li onclick="scrollToCategory('rules')">
                        <span class="method get">GET</span>规则管理
                    </li>
                    <li onclick="scrollToCategory('sites')">
                        <span class="method get">GET</span>资源站管理
                    </li>
                    <li onclick="scrollToCategory('learn')">
                        <span class="method post">POST</span>学习相关
                    </li>
                    <li onclick="scrollToCategory('auto_learn')">
                        <span class="method post">POST</span>自动学习
                    </li>
                    <li onclick="scrollToCategory('official_replace')">
                        <span class="method get">GET</span>官方替换
                    </li>
                    <li onclick="scrollToCategory('parse')">
                        <span class="method get">GET</span>解析接口
                    </li>
                    <li onclick="scrollToCategory('update')">
                        <span class="method get">GET</span>系统更新
                    </li>
                    <li onclick="scrollToCategory('auth')">
                        <span class="method get">GET</span>授权管理
                    </li>
                    <li onclick="scrollToCategory('db')">
                        <span class="method post">POST</span>数据库
                    </li>
                    <li onclick="scrollToCategory('other')">
                        <span class="method get">GET</span>其他接口
                    </li>
                </ul>
            </div>
        </aside>

        <main class="content">
            <!-- 视频分析 -->
            <div class="category" id="category-analyze">
                <h2 class="category-title"><span class="icon">🎬</span> 视频分析</h2>

                <div class="api-card" data-name="analyze 分析视频广告">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">analyze</span>
                        <span class="api-desc">分析视频广告结构</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 m3u8 URL</td></tr>
                                    <tr><td>auto_learn</td><td>int</td><td class="param-optional">否</td><td>是否自动学习，1=是</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "url": "https://example.com/video.m3u8",
  "total_segments": 200,
  "ad_segments": 20,
  "ad_percentage": 10,
  "segments": [...]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 规则管理 -->
            <div class="category" id="category-rules">
                <h2 class="category-title"><span class="icon">📋</span> 规则管理</h2>

                <div class="api-card" data-name="rules/list 获取所有域名规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">rules/list</span>
                        <span class="api-desc">获取所有域名规则列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "rules": {
    "example.com": { ... }
  },
  "total": 10
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/get 获取指定域名规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">rules/get</span>
                        <span class="api-desc">获取指定域名的规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/save 保存域名规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/save</span>
                        <span class="api-desc">保存域名规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                    <tr><td>rules</td><td>object</td><td class="param-required">是</td><td>规则配置</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/delete 删除域名规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/delete</span>
                        <span class="api-desc">删除指定域名的规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/generate 根据视频自动生成规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">rules/generate</span>
                        <span class="api-desc">根据视频 URL 自动生成规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/learn 学习并更新规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">rules/learn</span>
                        <span class="api-desc">从指定视频学习并更新规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/export 导出规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">rules/export</span>
                        <span class="api-desc">导出所有规则为 JSON</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="rules/import 导入规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/import</span>
                        <span class="api-desc">导入规则 JSON</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="rules/clear 清空规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/clear</span>
                        <span class="api-desc">清空所有规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 资源站管理 -->
            <div class="category" id="category-sites">
                <h2 class="category-title"><span class="icon">📺</span> 资源站管理</h2>

                <div class="api-card" data-name="sites/list 获取资源站列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/list</span>
                        <span class="api-desc">获取所有资源站列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/get 获取单个资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/get</span>
                        <span class="api-desc">获取单个资源站详情</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-required">是</td><td>资源站名称</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/add 添加资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/add</span>
                        <span class="api-desc">添加新的资源站</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/update 更新资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/update</span>
                        <span class="api-desc">更新资源站配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/delete 删除资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/delete</span>
                        <span class="api-desc">删除资源站</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/search 搜索指定资源站视频">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/search</span>
                        <span class="api-desc">在指定资源站搜索视频</span>
                        <span class="tag hot">常用</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-optional">否</td><td>资源站名称</td></tr>
                                    <tr><td>api_url</td><td>string</td><td class="param-optional">否</td><td>采集接口地址</td></tr>
                                    <tr><td>keyword</td><td>string</td><td class="param-required">是</td><td>搜索关键词</td></tr>
                                    <tr><td>page</td><td>int</td><td class="param-optional">否</td><td>页码，默认1</td></tr>
                                    <tr><td>limit</td><td>int</td><td class="param-optional">否</td><td>每页数量，默认20</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/search_all 搜索所有资源站视频">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/search_all</span>
                        <span class="api-desc">在所有资源站搜索视频</span>
                        <span class="tag hot">常用</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>keyword</td><td>string</td><td class="param-required">是</td><td>搜索关键词</td></tr>
                                    <tr><td>max_sites</td><td>int</td><td class="param-optional">否</td><td>最大站点数，默认5</td></tr>
                                    <tr><td>limit_per_site</td><td>int</td><td class="param-optional">否</td><td>每站数量，默认10</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/fetch_videos 从资源站获取视频列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/fetch_videos</span>
                        <span class="api-desc">从资源站获取视频列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 学习相关 -->
            <div class="category" id="category-learn">
                <h2 class="category-title"><span class="icon">📖</span> 学习相关</h2>

                <div class="api-card" data-name="sites/search_and_learn 搜索并学习一体化">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/search_and_learn</span>
                        <span class="api-desc">搜索影视并自动学习规则</span>
                        <span class="tag new">新</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>默认值</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>keyword</td><td>string</td><td class="param-required">是</td><td>-</td><td>搜索关键词</td></tr>
                                    <tr><td>site_name</td><td>string</td><td class="param-optional">否</td><td>all</td><td>资源站名称，all=全部</td></tr>
                                    <tr><td>max_sites</td><td>int</td><td class="param-optional">否</td><td>5</td><td>最大搜索站点数</td></tr>
                                    <tr><td>limit_per_site</td><td>int</td><td class="param-optional">否</td><td>10</td><td>每站视频数量</td></tr>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>false</td><td>启用多线程</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>5</td><td>并发数(1-10)</td></tr>
                                    <tr><td>min_segments</td><td>int</td><td class="param-optional">否</td><td>50</td><td>最少片段数</td></tr>
                                    <tr><td>max_ad_percentage</td><td>int</td><td class="param-optional">否</td><td>90</td><td>最大广告占比</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "搜索学习完成",
  "keyword": "庆余年",
  "sites_searched": 3,
  "total_found": 25,
  "total_learned": 20,
  "total_failed": 5,
  "total_time": 15234.56,
  "mode": "curl_multi",
  "concurrency": 5,
  "learned_domains": ["v.example.com"],
  "site_results": [...],
  "details": [...]
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>fetch('mx.php?action=sites/search_and_learn', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    keyword: '庆余年',
    site_name: 'all',
    multi_thread: true,
    concurrency: 5
  })
}).then(r => r.json())</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/learn_video 从指定视频URL学习规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/learn_video</span>
                        <span class="api-desc">从指定视频 URL 学习规则</span>
                        <span class="tag stable">稳定</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 URL</td></tr>
                                    <tr><td>min_segments</td><td>int</td><td class="param-optional">否</td><td>最少片段数</td></tr>
                                    <tr><td>max_ad_percentage</td><td>int</td><td class="param-optional">否</td><td>最大广告占比</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/learn_batch 批量学习视频">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/learn_batch</span>
                        <span class="api-desc">批量学习多个视频（支持多线程）</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>urls</td><td>array</td><td class="param-required">是</td><td>视频 URL 数组</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>并发数，默认5</td></tr>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>启用多线程</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/analyze_batch 批量分析视频">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/analyze_batch</span>
                        <span class="api-desc">批量分析多个视频（支持多线程）</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/multi_thread/status 多线程状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/multi_thread/status</span>
                        <span class="api-desc">获取多线程支持状态</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 自动学习 -->
            <div class="category" id="category-auto_learn">
                <h2 class="category-title"><span class="icon">🤖</span> 自动学习</h2>

                <div class="api-card" data-name="sites/auto_learn/config 获取自动学习配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/auto_learn/config</span>
                        <span class="api-desc">获取自动学习配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/auto_learn/config/save 保存自动学习配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/auto_learn/config/save</span>
                        <span class="api-desc">保存自动学习配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="sites/auto_learn/run 执行自动学习">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/auto_learn/run</span>
                        <span class="api-desc">执行自动学习任务</span>
                        <span class="tag hot">常用</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>启用多线程</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>并发数</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/auto_learn/status 自动学习状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/auto_learn/status</span>
                        <span class="api-desc">获取自动学习状态</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 官方替换 -->
            <div class="category" id="category-official_replace">
                <h2 class="category-title"><span class="icon">🔄</span> 官方替换</h2>

                <div class="api-card" data-name="official_replace/config 官替配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace/config</span>
                        <span class="api-desc">获取官替配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="official_replace/config/save 保存官替配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">official_replace/config/save</span>
                        <span class="api-desc">保存官替配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="official_replace/platforms 官替平台列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace/platforms</span>
                        <span class="api-desc">获取官替平台列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="official_replace/resolve 官替解析完整结果">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace/resolve</span>
                        <span class="api-desc">官替解析 - 完整结果</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>官方视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_replace/info 官替解析精简信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace/info</span>
                        <span class="api-desc">官替解析 - 精简信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 解析接口 -->
            <div class="category" id="category-parse">
                <h2 class="category-title"><span class="icon">🔗</span> 解析接口</h2>

                <div class="api-card" data-name="skip 去广告接口">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">skip</span>
                        <span class="api-desc">去广告接口（跳转）</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="mxjx 去广告m3u8输出">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">mxjx</span>
                        <span class="api-desc">去广告 m3u8 直接输出</span>
                        <span class="tag stable">稳定</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>m3u8 视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="mxjx/info 去广告解析信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">mxjx/info</span>
                        <span class="api-desc">去广告解析详细信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="xiami_jx 虾米解析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">xiami_jx</span>
                        <span class="api-desc">虾米解析 - 全网 VIP 视频解析</span>
                        <span class="tag new">新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频播放页 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                支持爱奇艺、腾讯视频、优酷、芒果TV等主流视频平台的 VIP 视频解析。<br>
                                也可以使用独立脚本: <code>xiami_jx.php?url=视频链接</code>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="xiami_jx/info 虾米解析详情">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">xiami_jx/info</span>
                        <span class="api-desc">虾米解析 - 详细信息</span>
                        <span class="tag new">新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="moxi 沫兮API接口">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">moxi</span>
                        <span class="api-desc">沫兮 API 接口</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="img/list 统一解析接口列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">img/list</span>
                        <span class="api-desc">图片视频统一解析 - 接口列表</span>
                        <span class="tag new">新</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                统一解析接口，整合多种解析能力，一键调用，智能识别视频类型。<br>
                                也可以使用独立脚本: <code>img.php?action=list</code>
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">支持的解析类型</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>类型</th><th>名称</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>parse</td><td>智能解析</td><td>自动判断视频类型，选择最佳解析方式</td></tr>
                                    <tr><td>mxjx</td><td>去广告解析</td><td>M3U8 视频去广告，自动识别并移除广告片段</td></tr>
                                    <tr><td>xiami</td><td>虾米解析</td><td>全网 VIP 视频解析，支持腾讯、爱奇艺、优酷等</td></tr>
                                    <tr><td>moxi</td><td>沫兮解析</td><td>沫兮 API 解析，支持官方视频智能替换</td></tr>
                                    <tr><td>official</td><td>官方替换</td><td>官方视频链接智能匹配资源站无广告源</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="img/parse 统一解析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">img/parse</span>
                        <span class="api-desc">图片视频统一解析 - 解析视频</span>
                        <span class="tag new">新</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频链接</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>解析类型，默认 parse（智能解析）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 智能解析
mx.php?action=img/parse&url=https://v.youku.com/v_show/id_xxx.html

// 指定虾米解析
mx.php?action=img/parse&type=xiami&url=https://v.youku.com/v_show/id_xxx.html

// 去广告解析
mx.php?action=img/parse&type=mxjx&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="img/info 统一解析详情">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">img/info</span>
                        <span class="api-desc">图片视频统一解析 - 详细信息</span>
                        <span class="tag new">新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频链接</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>解析类型，默认 parse</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 系统更新 -->
            <div class="category" id="category-update">
                <h2 class="category-title"><span class="icon">🔧</span> 系统更新</h2>

                <div class="api-card" data-name="update/version 获取当前版本">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/version</span>
                        <span class="api-desc">获取当前版本信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="update/check 检查更新">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/check</span>
                        <span class="api-desc">检查是否有新版本</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="update/integrity 完整性检查">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/integrity</span>
                        <span class="api-desc">文件完整性检查</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="update/download 下载更新">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/download</span>
                        <span class="api-desc">下载并安装更新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="update/clear_cache 清理更新缓存">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/clear_cache</span>
                        <span class="api-desc">清理更新缓存</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="update/system_info 系统信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/system_info</span>
                        <span class="api-desc">获取系统信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 授权管理 -->
            <div class="category" id="category-auth">
                <h2 class="category-title"><span class="icon">🔐</span> 授权管理</h2>

                <div class="api-card" data-name="auth/info 授权信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">auth/info</span>
                        <span class="api-desc">获取当前授权信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="auth/validate 验证授权">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/validate</span>
                        <span class="api-desc">验证授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="auth/set 设置授权码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/set</span>
                        <span class="api-desc">设置授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="auth/generate 生成授权码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/generate</span>
                        <span class="api-desc">生成授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 数据库 -->
            <div class="category" id="category-db">
                <h2 class="category-title"><span class="icon">🗄️</span> 数据库</h2>

                <div class="api-card" data-name="db/status 数据库状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">db/status</span>
                        <span class="api-desc">获取数据库连接状态</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="db/config/save 保存数据库配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/config/save</span>
                        <span class="api-desc">保存数据库配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="db/test_connection 测试数据库连接">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/test_connection</span>
                        <span class="api-desc">测试数据库连接</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="db/migrate 数据库迁移">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/migrate</span>
                        <span class="api-desc">执行数据库迁移</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="db/init 初始化数据库">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/init</span>
                        <span class="api-desc">初始化数据库</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <!-- 其他接口 -->
            <div class="category" id="category-other">
                <h2 class="category-title"><span class="icon">📦</span> 其他接口</h2>

                <div class="api-card" data-name="info 系统信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">info</span>
                        <span class="api-desc">系统基本信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="version 版本信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">version</span>
                        <span class="api-desc">版本信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="player/config 播放器配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">player/config</span>
                        <span class="api-desc">播放器配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="proxy/list 代理列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">proxy/list</span>
                        <span class="api-desc">代理列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="official/list 官替站点列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official/list</span>
                        <span class="api-desc">官替站点列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>

                <div class="api-card" data-name="proxies/list 代理配置列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">proxies/list</span>
                        <span class="api-desc">代理配置列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body"></div>
                </div>
            </div>

            <div style="text-align: center; padding: 40px 0; color: var(--text-secondary);">
                <p>--- 文档结束 ---</p>
                <p style="margin-top: 10px; font-size: 0.9em;">
                    M3U8 广告分析系统 API 文档 | 版本 <?php echo $version; ?>
                </p>
            </div>
        </main>
    </div>

    <script>
        const API_BASE = '<?php echo $apiBase; ?>';

        function toggleApi(header) {
            const card = header.parentElement;
            card.classList.toggle('expanded');
        }

        function scrollToCategory(id) {
            const el = document.getElementById('category-' + id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
            document.querySelectorAll('.sidebar-list li').forEach(li => li.classList.remove('active'));
            event.target.closest('li').classList.add('active');
        }

        function filterApis() {
            const keyword = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.api-card');
            const categories = document.querySelectorAll('.category');

            categories.forEach(cat => {
                let hasVisible = false;
                const catCards = cat.querySelectorAll('.api-card');
                catCards.forEach(card => {
                    const name = card.dataset.name.toLowerCase();
                    if (name.includes(keyword)) {
                        card.classList.remove('hidden');
                        hasVisible = true;
                    } else {
                        card.classList.add('hidden');
                    }
                });
                if (hasVisible || keyword === '') {
                    cat.classList.remove('hidden');
                } else {
                    cat.classList.add('hidden');
                }
            });
        }

        function copyCode(btn) {
            const code = btn.parentElement.querySelector('pre').textContent;
            navigator.clipboard.writeText(code).then(() => {
                const oldText = btn.textContent;
                btn.textContent = '已复制 ✓';
                setTimeout(() => btn.textContent = oldText, 1500);
            });
        }

        function toggleTheme() {
            const html = document.documentElement;
            const current = html.getAttribute('data-theme');
            if (current === 'dark') {
                html.removeAttribute('data-theme');
                localStorage.setItem('api-doc-theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('api-doc-theme', 'dark');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const saved = localStorage.getItem('api-doc-theme');
            if (saved === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.id.replace('category-', '');
                        document.querySelectorAll('.sidebar-list li').forEach(li => {
                            li.classList.remove('active');
                            if (li.textContent.includes(entry.target.querySelector('.category-title').textContent)) {
                                li.classList.add('active');
                            }
                        });
                    }
                });
            }, { rootMargin: '-20% 0px -70% 0px' });

            document.querySelectorAll('.category').forEach(cat => observer.observe(cat));
        });
    </script>
</body>
</html>
