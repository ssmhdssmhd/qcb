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
                    <li class="active" onclick="scrollToCategory('index')">
                        <span class="method get">ALL</span>完整接口索引
                    </li>
                    <li onclick="scrollToCategory('analyze')">
                        <span class="method get">GET</span>视频分析
                    </li>
                    <li onclick="scrollToCategory('rules')">
                        <span class="method get">GET</span>规则管理
                    </li>
                    <li onclick="scrollToCategory('sites')">
                        <span class="method get">GET</span>资源站管理
                    </li>
                    <li onclick="scrollToCategory('official_sites')">
                        <span class="method get">GET</span>官方站点
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
                    <li onclick="scrollToCategory('pt')">
                        <span class="method get">GET</span>PT引擎
                    </li>
                    <li onclick="scrollToCategory('ai')">
                        <span class="method get">GET</span>AI智能
                    </li>
                    <li onclick="scrollToCategory('signatures')">
                        <span class="method get">GET</span>广告特征码
                    </li>
                    <li onclick="scrollToCategory('parse')">
                        <span class="method get">GET</span>解析接口
                    </li>
                    <li onclick="scrollToCategory('player')">
                        <span class="method get">GET</span>播放器
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
            <!-- 完整接口索引 -->
            <div class="category" id="category-index">
                <h2 class="category-title"><span class="icon">📑</span> 完整接口索引（所有访问方式）</h2>
                <div class="api-card" data-name="完整接口索引">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">ALL</span>
                        <span class="api-path">全部接口快速索引</span>
                        <span class="api-desc">共 95+ 个接口，20 个功能模块</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">🎬 视频分析（1个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>analyze</code></td><td>分析视频广告结构</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">📋 规则管理（9个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>rules/list</code></td><td>获取所有域名规则列表</td><td>GET</td></tr>
                                    <tr><td><code>rules/get</code></td><td>获取指定域名规则</td><td>GET</td></tr>
                                    <tr><td><code>rules/save</code></td><td>保存域名规则</td><td>POST</td></tr>
                                    <tr><td><code>rules/delete</code></td><td>删除域名规则</td><td>POST</td></tr>
                                    <tr><td><code>rules/generate</code></td><td>根据视频自动生成规则</td><td>GET</td></tr>
                                    <tr><td><code>rules/learn</code></td><td>学习并更新规则</td><td>GET</td></tr>
                                    <tr><td><code>rules/export</code></td><td>导出规则</td><td>GET</td></tr>
                                    <tr><td><code>rules/import</code></td><td>导入规则</td><td>POST</td></tr>
                                    <tr><td><code>rules/clear</code></td><td>清空所有规则</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">📺 资源站管理（18个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>sites/list</code></td><td>获取资源站列表</td><td>GET</td></tr>
                                    <tr><td><code>sites/get</code></td><td>获取单个资源站</td><td>GET</td></tr>
                                    <tr><td><code>sites/add</code></td><td>添加资源站</td><td>POST</td></tr>
                                    <tr><td><code>sites/update</code></td><td>更新资源站</td><td>POST</td></tr>
                                    <tr><td><code>sites/delete</code></td><td>删除资源站</td><td>POST</td></tr>
                                    <tr><td><code>sites/health_check</code></td><td>批量健康检查</td><td>GET</td></tr>
                                    <tr><td><code>sites/update_status</code></td><td>更新资源站状态</td><td>POST</td></tr>
                                    <tr><td><code>sites/fetch_videos</code></td><td>获取视频列表</td><td>GET</td></tr>
                                    <tr><td><code>sites/search</code></td><td>搜索指定资源站</td><td>GET</td></tr>
                                    <tr><td><code>sites/search_all</code></td><td>搜索所有资源站</td><td>GET</td></tr>
                                    <tr><td><code>sites/learn_video</code></td><td>学习视频规则</td><td>GET</td></tr>
                                    <tr><td><code>sites/search_and_learn</code></td><td>搜索并学习一体化</td><td>GET</td></tr>
                                    <tr><td><code>sites/learn_batch</code></td><td>批量学习视频</td><td>POST</td></tr>
                                    <tr><td><code>sites/analyze_batch</code></td><td>批量分析视频</td><td>POST</td></tr>
                                    <tr><td><code>sites/multi_thread/status</code></td><td>多线程状态</td><td>GET</td></tr>
                                    <tr><td><code>sites/auto_learn/config</code></td><td>自动学习配置</td><td>GET</td></tr>
                                    <tr><td><code>sites/auto_learn/config/save</code></td><td>保存自动学习配置</td><td>POST</td></tr>
                                    <tr><td><code>sites/auto_learn/run</code></td><td>执行自动学习</td><td>POST</td></tr>
                                    <tr><td><code>sites/auto_learn/status</code></td><td>自动学习状态</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🏛️ 官方站点管理（12个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>official_sites/status</code></td><td>官方站点状态</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/list</code></td><td>官方站点列表</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/get</code></td><td>获取单个官方站点</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/add</code></td><td>添加官方站点</td><td>POST</td></tr>
                                    <tr><td><code>official_sites/update</code></td><td>更新官方站点</td><td>POST</td></tr>
                                    <tr><td><code>official_sites/delete</code></td><td>删除官方站点</td><td>POST</td></tr>
                                    <tr><td><code>official_sites/fetch_videos</code></td><td>获取官方站点视频</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/search</code></td><td>搜索指定官方站点</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/search_all</code></td><td>搜索所有官方站点</td><td>GET</td></tr>
                                    <tr><td><code>official_sites/set_domain</code></td><td>切换官方站点域名</td><td>POST</td></tr>
                                    <tr><td><code>official_sites/settings/save</code></td><td>保存全局设置</td><td>POST</td></tr>
                                    <tr><td><code>official_sites/toggle</code></td><td>启用/禁用官方站点</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🔄 官方替换（8个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>official_replace/config</code></td><td>官替配置</td><td>GET</td></tr>
                                    <tr><td><code>official_replace/config/save</code></td><td>保存官替配置</td><td>POST</td></tr>
                                    <tr><td><code>official_replace/platforms</code></td><td>官替平台列表</td><td>GET</td></tr>
                                    <tr><td><code>official_replace/platform/add</code></td><td>添加官替平台</td><td>POST</td></tr>
                                    <tr><td><code>official_replace/platform/update</code></td><td>更新官替平台</td><td>POST</td></tr>
                                    <tr><td><code>official_replace/platform/delete</code></td><td>删除官替平台</td><td>POST</td></tr>
                                    <tr><td><code>official_replace/resolve</code></td><td>官替解析完整结果</td><td>GET</td></tr>
                                    <tr><td><code>official_replace/info</code></td><td>官替解析精简信息</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🚀 PT 引擎（3个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>pt/status</code></td><td>PT引擎状态</td><td>GET</td></tr>
                                    <tr><td><code>pt/test</code></td><td>PT引擎匹配测试</td><td>GET</td></tr>
                                    <tr><td><code>pt/adskip</code></td><td>PT去广告处理</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🤖 AI 智能模块（9个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>ai/smart_process</code></td><td>AI智能处理</td><td>GET</td></tr>
                                    <tr><td><code>ai/pro_detect</code></td><td>AI专业检测</td><td>GET</td></tr>
                                    <tr><td><code>ai/skip</code></td><td>AI去广告处理</td><td>GET</td></tr>
                                    <tr><td><code>ai/insert_detect</code></td><td>AI插播检测</td><td>GET</td></tr>
                                    <tr><td><code>ai/watermark</code></td><td>AI水印处理</td><td>GET</td></tr>
                                    <tr><td><code>ai/subtitle_detect</code></td><td>AI滚动字幕分析</td><td>GET</td></tr>
                                    <tr><td><code>ai/md5_analyze</code></td><td>AI-MD5特征码分析</td><td>GET</td></tr>
                                    <tr><td><code>ai/md5_signatures</code></td><td>AI-MD5特征码列表</td><td>GET</td></tr>
                                    <tr><td><code>ai/md5_detect</code></td><td>AI-MD5智能去广告</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🔗 解析接口（10个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>skip</code></td><td>去广告接口</td><td>GET</td></tr>
                                    <tr><td><code>mxjx</code></td><td>去广告M3U8输出</td><td>GET</td></tr>
                                    <tr><td><code>mxjx/info</code></td><td>去广告解析信息</td><td>GET</td></tr>
                                    <tr><td><code>mxjx/deep</code></td><td>深度去广告分析</td><td>GET</td></tr>
                                    <tr><td><code>xiami_jx</code></td><td>虾米解析</td><td>GET</td></tr>
                                    <tr><td><code>xiami_jx/info</code></td><td>虾米解析详情</td><td>GET</td></tr>
                                    <tr><td><code>moxi</code></td><td>沫兮解析</td><td>GET</td></tr>
                                    <tr><td><code>parse/list</code></td><td>统一解析接口列表</td><td>GET</td></tr>
                                    <tr><td><code>parse</code></td><td>统一解析视频</td><td>GET</td></tr>
                                    <tr><td><code>parse/info</code></td><td>统一解析详情</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🔧 系统更新（10个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>update/version</code></td><td>获取当前版本</td><td>GET</td></tr>
                                    <tr><td><code>update/check</code></td><td>检查更新</td><td>GET</td></tr>
                                    <tr><td><code>update/integrity</code></td><td>完整性检查</td><td>GET</td></tr>
                                    <tr><td><code>update/download</code></td><td>下载更新包</td><td>GET</td></tr>
                                    <tr><td><code>update/clear_cache</code></td><td>清理更新缓存</td><td>POST</td></tr>
                                    <tr><td><code>update/system_info</code></td><td>系统信息</td><td>GET</td></tr>
                                    <tr><td><code>update/backup/list</code></td><td>备份文件列表</td><td>GET</td></tr>
                                    <tr><td><code>update/backup/create</code></td><td>创建系统备份</td><td>POST</td></tr>
                                    <tr><td><code>update/backup/restore</code></td><td>恢复备份</td><td>POST</td></tr>
                                    <tr><td><code>update/backup/delete</code></td><td>删除备份</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🔐 授权管理（6个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>auth/info</code></td><td>授权信息</td><td>GET</td></tr>
                                    <tr><td><code>auth/validate</code></td><td>验证授权</td><td>GET</td></tr>
                                    <tr><td><code>auth/config/get</code></td><td>授权配置</td><td>GET</td></tr>
                                    <tr><td><code>auth/config/save</code></td><td>保存授权配置</td><td>POST</td></tr>
                                    <tr><td><code>auth/set</code></td><td>设置授权码</td><td>POST</td></tr>
                                    <tr><td><code>auth/generate</code></td><td>生成授权码</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🗄️ 数据库（6个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>db/status</code></td><td>数据库状态</td><td>GET</td></tr>
                                    <tr><td><code>db/config/save</code></td><td>保存数据库配置</td><td>POST</td></tr>
                                    <tr><td><code>db/test_connection</code></td><td>测试数据库连接</td><td>POST</td></tr>
                                    <tr><td><code>db/migrate</code></td><td>数据库迁移</td><td>POST</td></tr>
                                    <tr><td><code>db/init</code></td><td>初始化数据库</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🏷️ 广告特征码（8个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>signatures/list</code></td><td>特征码列表</td><td>GET</td></tr>
                                    <tr><td><code>signatures/add</code></td><td>添加特征码</td><td>POST</td></tr>
                                    <tr><td><code>signatures/delete</code></td><td>删除特征码</td><td>POST</td></tr>
                                    <tr><td><code>signatures/stats</code></td><td>特征码统计</td><td>GET</td></tr>
                                    <tr><td><code>signatures/clean</code></td><td>清理低置信度特征码</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🎮 播放器配置（2个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>player/config</code></td><td>播放器配置</td><td>GET</td></tr>
                                    <tr><td><code>player/config/save</code></td><td>保存播放器配置</td><td>POST</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">🌐 代理管理（3个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>proxy/list</code></td><td>活跃代理列表</td><td>GET</td></tr>
                                    <tr><td><code>proxy/check</code></td><td>检查代理可用性</td><td>GET</td></tr>
                                    <tr><td><code>proxies/list</code></td><td>全部代理列表</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">📦 其他接口（4个）</div>
                            <table class="param-table">
                                <thead><tr><th>接口路径</th><th>说明</th><th>方法</th></tr></thead>
                                <tbody>
                                    <tr><td><code>info</code></td><td>系统信息</td><td>GET</td></tr>
                                    <tr><td><code>version</code></td><td>版本信息</td><td>GET</td></tr>
                                    <tr><td><code>api/v2</code></td><td>v2统一接口</td><td>GET/POST</td></tr>
                                    <tr><td><code>kz/cache</code></td><td>缓存型M3U8解析</td><td>GET</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                分析 M3U8 视频的广告结构，识别广告片段，统计广告占比，可选自动学习规则。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 m3u8 URL</td></tr>
                                    <tr><td>auto_learn</td><td>int</td><td class="param-optional">否</td><td>是否自动学习，1=是</td></tr>
                                    <tr><td>skip_cache</td><td>int</td><td class="param-optional">否</td><td>是否跳过缓存，1=是</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 分析视频广告结构
mx.php?action=analyze&url=https://example.com/video.m3u8

// 分析并自动学习规则
mx.php?action=analyze&url=https://example.com/video.m3u8&auto_learn=1</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "url": "https://example.com/video.m3u8",
  "domain": "example.com",
  "total_segments": 200,
  "ad_segments": 20,
  "kept_segments": 180,
  "ad_percentage": 10.0,
  "original_duration": 3600,
  "filtered_duration": 3240,
  "saved_duration": 360,
  "has_domain_rules": true,
  "duration_stats": {
    "min": 2.5,
    "max": 15.0,
    "avg": 6.0
  },
  "ad_segments_list": [
    { "index": 5, "duration": 10.0, "uri": "https://example.com/ad/5.ts" }
  ],
  "all_segments": [
    { "i": 0, "d": 6.0, "a": false },
    { "i": 5, "d": 10.0, "a": true }
  ]
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应字段说明</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>字段</th><th>类型</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>success</td><td>bool</td><td>是否成功</td></tr>
                                    <tr><td>url</td><td>string</td><td>分析的 M3U8 地址</td></tr>
                                    <tr><td>domain</td><td>string</td><td>视频域名</td></tr>
                                    <tr><td>total_segments</td><td>int</td><td>总片段数</td></tr>
                                    <tr><td>ad_segments</td><td>int</td><td>广告片段数</td></tr>
                                    <tr><td>kept_segments</td><td>int</td><td>保留的正常片段数</td></tr>
                                    <tr><td>ad_percentage</td><td>float</td><td>广告占比百分比</td></tr>
                                    <tr><td>original_duration</td><td>float</td><td>原始总时长（秒）</td></tr>
                                    <tr><td>filtered_duration</td><td>float</td><td>去广告后时长（秒）</td></tr>
                                    <tr><td>saved_duration</td><td>float</td><td>节省时长（秒）</td></tr>
                                    <tr><td>has_domain_rules</td><td>bool</td><td>是否有域名规则</td></tr>
                                    <tr><td>ad_segments_list</td><td>array</td><td>广告片段详情列表</td></tr>
                                    <tr><td>all_segments</td><td>array</td><td>全部片段精简列表（i=索引, d=时长, a=是否广告）</td></tr>
                                </tbody>
                            </table>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取所有已保存的域名规则列表（精简版），返回每个域名的规则概要信息，不包含完整规则配置。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=rules/list</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "rules": {
    "v.lzcdn23.com": {
      "domain": "v.lzcdn23.com",
      "name": "v.lzcdn23.com",
      "note": "",
      "learn_count": 3,
      "ad_threshold": 50,
      "confidence_score": 80,
      "analysis_date": "2026-07-01 10:00:00",
      "last_learn_date": "2026-07-02 12:00:00",
      "duration_rule_count": 2,
      "discontinuity_rule_count": 1,
      "sequence_jump_rule_count": 0,
      "filename_pattern_count": 3,
      "total_segments": 100,
      "ad_segments": 12,
      "ad_percentage": 12,
      "marker_stats": {
        "discontinuity_count": 1,
        "cue_marker_count": 0,
        "scte35_count": 0,
        "ad_tag_count": 0
      }
    }
  }
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取指定域名的完整规则配置，包含时长规则、不连续规则、序列跳变规则、文件名匹配模式等。</p>
                        </div>
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
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=rules/get&domain=v.lzcdn23.com</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "domain": "v.lzcdn23.com",
  "rules": {
    "domain": "v.lzcdn23.com",
    "name": "v.lzcdn23.com",
    "note": "",
    "learn_count": 3,
    "ad_threshold": 50,
    "confidence_score": 80,
    "analysis_date": "2026-07-01 10:00:00",
    "duration_rules": [
      {
        "name": "short_segment",
        "enabled": true,
        "type": "duration",
        "operator": "&lt;",
        "threshold": 2,
        "reason": "极短片段 (&lt;2秒) 可能是广告"
      }
    ],
    "discontinuity_rules": [],
    "sequence_jump_rules": [],
    "filename_patterns": []
  }
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">保存指定域名的规则配置，以 JSON Body 形式提交。若域名已存在则覆盖更新，不存在则新增。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                    <tr><td>rules</td><td>object</td><td class="param-required">是</td><td>规则配置，包含 duration_rules、discontinuity_rules、filename_patterns 等字段</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=rules/save \
  -H "Content-Type: application/json" \
  -d '{
    "domain": "v.lzcdn23.com",
    "rules": {
      "domain": "v.lzcdn23.com",
      "name": "v.lzcdn23.com",
      "duration_rules": [
        {
          "name": "short_segment",
          "enabled": true,
          "type": "duration",
          "operator": "&lt;",
          "threshold": 2
        }
      ],
      "filename_patterns": []
    }
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "规则保存成功",
  "domain": "v.lzcdn23.com"
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">删除指定域名的规则配置，以 JSON Body 形式提交 domain 参数。</p>
                        </div>
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
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=rules/delete \
  -H "Content-Type: application/json" \
  -d '{"domain": "v.lzcdn23.com"}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "规则删除成功"
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">根据视频 URL 解析 M3U8 切片并自动生成规则（不保存），返回生成的规则配置及分析统计信息。会自动从 URL 中提取域名。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 URL（M3U8 地址或播放页地址）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=rules/generate&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "domain": "example.com",
  "rules": {
    "domain": "example.com",
    "duration_rules": [
      {
        "name": "short_segment",
        "enabled": true,
        "type": "duration",
        "operator": "&lt;",
        "threshold": 2
      }
    ],
    "sample_url": "https://example.com/video.m3u8"
  },
  "analysis": {
    "totalSegments": 100,
    "adSegments": 12,
    "discontinuityCount": 1,
    "sequenceJumpCount": 0,
    "adClusterCount": 1
  }
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">从指定视频 URL 学习并更新规则（会保存到规则文件）。会解析 M3U8 切片进行分析，自动提取域名，并累加学习次数。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频 URL（M3U8 地址或播放页地址）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=rules/learn&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "规则学习完成",
  "domain": "example.com",
  "learn_count": 2,
  "stats": {
    "totalSegments": 100,
    "adSegments": 12,
    "discontinuityCount": 1,
    "sequenceJumps": 0,
    "adClusters": 1
  }
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">导出规则为 JSON 数据，支持导出全部规则或指定域名的规则。带 <code>download=1</code> 参数时返回文件下载响应。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-optional">否</td><td>指定域名，仅导出该域名规则；不传则导出全部</td></tr>
                                    <tr><td>download</td><td>any</td><td class="param-optional">否</td><td>传 1 时以附件形式下载 JSON 文件</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 导出全部规则
mx.php?action=rules/export

// 导出指定域名规则
mx.php?action=rules/export&domain=v.lzcdn23.com

// 下载为文件
mx.php?action=rules/export&download=1</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "version": "1.0",
  "export_date": "2026-07-08 10:00:00",
  "type": "all",
  "count": 1,
  "rules": {
    "v.lzcdn23.com": {
      "domain": "v.lzcdn23.com",
      "learn_count": 3,
      "duration_rules": []
    }
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/import 导入规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/import</span>
                        <span class="api-desc">导入规则 JSON</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">导入规则 JSON 数据，支持单条（type=single）或批量（type=all）导入。数据格式需与 rules/export 导出格式一致。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>version</td><td>string</td><td class="param-optional">否</td><td>版本号，如 "1.0"</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-required">是</td><td>导入类型：single（单条）/ all（批量）</td></tr>
                                    <tr><td>rules</td><td>object/array</td><td class="param-required">是</td><td>规则数据，single 时为对象，all 时为按域名分组的对象</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=rules/import \
  -H "Content-Type: application/json" \
  -d '{
    "version": "1.0",
    "type": "all",
    "rules": {
      "v.lzcdn23.com": {
        "domain": "v.lzcdn23.com",
        "duration_rules": []
      }
    }
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "imported": 1,
  "updated": 0,
  "errors": [],
  "message": "导入完成：新增 1 条，更新 0 条"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="rules/clear 清空规则">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">rules/clear</span>
                        <span class="api-desc">清空所有规则</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">清空所有已保存的域名规则，该操作不可恢复，请谨慎使用。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=rules/clear</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "已清理 15 条规则",
  "cleared_count": 15
}</pre>
                            </div>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取所有资源站列表，同时返回自动学习配置、最后学习时间及是否应该触发自动学习等状态信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>include_paused</td><td>int</td><td class="param-optional">否</td><td>是否包含已暂停资源站，传 1 包含，默认不包含</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 获取启用的资源站
mx.php?action=sites/list

// 包含已暂停的资源站
mx.php?action=sites/list&include_paused=1</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "sites": [
    {
      "name": "量子",
      "site_url": "http://23.224.101.30",
      "api_url": "https://cj.lziapi.com/api.php/provide/vod/from/lzm3u8/",
      "type": "maccms",
      "status": "active",
      "note": "推荐",
      "priority": 1
    }
  ],
  "total": 1,
  "auto_learn_config": {
    "enabled": false,
    "interval_hours": 24
  },
  "last_learn_time": "",
  "should_auto_learn": false
}</pre>
                            </div>
                        </div>
                    </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">根据名称获取单个资源站的完整配置信息。</p>
                        </div>
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
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=sites/get&name=量子</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "site": {
    "name": "量子",
    "site_url": "http://23.224.101.30",
    "api_url": "https://cj.lziapi.com/api.php/provide/vod/from/lzm3u8/",
    "type": "maccms",
    "status": "active",
    "note": "推荐",
    "priority": 1
  }
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">添加新的资源站，以 JSON Body 形式提交资源站配置。名称和采集接口为必填项，名称不能与已有资源站重复。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-required">是</td><td>资源站名称（唯一）</td></tr>
                                    <tr><td>api_url</td><td>string</td><td class="param-required">是</td><td>采集接口地址</td></tr>
                                    <tr><td>site_url</td><td>string</td><td class="param-optional">否</td><td>资源站官网地址</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>类型，默认 maccms</td></tr>
                                    <tr><td>status</td><td>string</td><td class="param-optional">否</td><td>状态：active/paused，默认 active</td></tr>
                                    <tr><td>note</td><td>string</td><td class="param-optional">否</td><td>备注</td></tr>
                                    <tr><td>priority</td><td>int</td><td class="param-optional">否</td><td>优先级，默认 50</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/add \
  -H "Content-Type: application/json" \
  -d '{
    "name": "新资源站",
    "site_url": "http://example.com",
    "api_url": "https://api.example.com/api.php/provide/vod/from/lzm3u8/",
    "type": "maccms",
    "note": "测试",
    "priority": 10
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "添加成功"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/update 更新资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/update</span>
                        <span class="api-desc">更新资源站配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">更新指定资源站的配置，以 JSON Body 形式提交。只需传入要更新的字段，会与原有配置合并（name 字段不可更改）。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-required">是</td><td>要更新的资源站名称</td></tr>
                                    <tr><td>site_url</td><td>string</td><td class="param-optional">否</td><td>资源站官网地址</td></tr>
                                    <tr><td>api_url</td><td>string</td><td class="param-optional">否</td><td>采集接口地址</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>类型，如 maccms</td></tr>
                                    <tr><td>status</td><td>string</td><td class="param-optional">否</td><td>状态：active/paused</td></tr>
                                    <tr><td>note</td><td>string</td><td class="param-optional">否</td><td>备注</td></tr>
                                    <tr><td>priority</td><td>int</td><td class="param-optional">否</td><td>优先级</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/update \
  -H "Content-Type: application/json" \
  -d '{
    "name": "量子",
    "note": "推荐资源站",
    "priority": 1
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "更新成功"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/delete 删除资源站">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/delete</span>
                        <span class="api-desc">删除资源站</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">删除指定名称的资源站，支持通过 JSON Body 或 GET 参数提交 name。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-required">是</td><td>资源站名称（支持 JSON Body 或 GET 传参）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 方式一：JSON Body
curl -X POST mx.php?action=sites/delete \
  -H "Content-Type: application/json" \
  -d '{"name": "量子"}'

// 方式二：GET 参数
mx.php?action=sites/delete&name=量子</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "删除成功"
}</pre>
                            </div>
                        </div>
                    </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">在指定资源站搜索视频，需提供资源站名称或采集接口地址（二选一），并指定搜索关键词。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-optional">否</td><td>资源站名称（与 api_url 二选一）</td></tr>
                                    <tr><td>api_url</td><td>string</td><td class="param-optional">否</td><td>采集接口地址（与 name 二选一）</td></tr>
                                    <tr><td>keyword</td><td>string</td><td class="param-required">是</td><td>搜索关键词</td></tr>
                                    <tr><td>page</td><td>int</td><td class="param-optional">否</td><td>页码，默认1</td></tr>
                                    <tr><td>limit</td><td>int</td><td class="param-optional">否</td><td>每页数量，默认20</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 通过资源站名称搜索
mx.php?action=sites/search&name=量子&keyword=庆余年&page=1

// 通过采集接口搜索
mx.php?action=sites/search&api_url=https://cj.lziapi.com/api.php/provide/vod/from/lzm3u8/&keyword=庆余年</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "total": 50,
  "pagecount": 3,
  "page": 1,
  "videos": [
    {
      "id": 12345,
      "name": "庆余年",
      "pic": "https://example.com/cover.jpg",
      "remarks": "全46集",
      "urls": [
        {
          "from": "lzm3u8",
          "url": "https://example.com/play/12345.m3u8"
        }
      ],
      "first_url": "https://example.com/play/12345.m3u8",
      "raw_play_url": "第1集$https://example.com/play/1.m3u8#第2集$https://example.com/play/2.m3u8",
      "play_from": "lzm3u8",
      "has_non_m3u8": false,
      "all_urls_count": 1
    }
  ]
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">在所有启用的资源站中搜索视频，按优先级顺序依次搜索前 max_sites 个站点，聚合返回各站搜索结果。</p>
                        </div>
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
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=sites/search_all&keyword=庆余年&max_sites=5&limit_per_site=10</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "keyword": "庆余年",
  "sites_searched": 3,
  "total_videos": 2,
  "results": [
    {
      "site": "量子",
      "site_url": "http://23.224.101.30",
      "count": 1,
      "videos": [
        {
          "id": 12345,
          "name": "庆余年",
          "pic": "https://example.com/cover.jpg",
          "remarks": "全46集",
          "first_url": "https://example.com/play/12345.m3u8",
          "site_name": "量子",
          "site_url": "http://23.224.101.30"
        }
      ]
    },
    {
      "site": "暴风",
      "site_url": "http://bfzv8.lv",
      "count": 0,
      "videos": [],
      "error": "搜索无结果"
    }
  ]
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">从指定资源站获取视频列表（分页），需提供资源站名称或采集接口地址（二选一）。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>name</td><td>string</td><td class="param-optional">否</td><td>资源站名称（与 api_url 二选一）</td></tr>
                                    <tr><td>api_url</td><td>string</td><td class="param-optional">否</td><td>采集接口地址（与 name 二选一）</td></tr>
                                    <tr><td>page</td><td>int</td><td class="param-optional">否</td><td>页码，默认1</td></tr>
                                    <tr><td>limit</td><td>int</td><td class="param-optional">否</td><td>每页数量，默认20</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 通过资源站名称获取
mx.php?action=sites/fetch_videos&name=量子&page=1&limit=20

// 通过采集接口获取
mx.php?action=sites/fetch_videos&api_url=https://cj.lziapi.com/api.php/provide/vod/from/lzm3u8/&page=1</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "total": 100,
  "pagecount": 5,
  "page": 1,
  "videos": [
    {
      "id": 12345,
      "name": "示例视频",
      "pic": "https://example.com/cover.jpg",
      "remarks": "HD",
      "urls": [
        {
          "from": "lzm3u8",
          "url": "https://example.com/play/12345.m3u8"
        }
      ],
      "first_url": "https://example.com/play/12345.m3u8",
      "raw_play_url": "第1集$https://example.com/play/1.m3u8",
      "play_from": "lzm3u8",
      "has_non_m3u8": false,
      "all_urls_count": 1
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                从指定视频 URL 学习广告规则。接口会解析 M3U8 视频片段，使用增强广告规则引擎分析广告特征（不连续点、重复时长、序列跳变等），并更新对应域名的去广告规则。当片段数不足或广告占比过高时会拒绝学习。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>默认值</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>-</td><td>视频 URL（M3U8 或可解析的播放页地址）</td></tr>
                                    <tr><td>min_segments</td><td>int</td><td class="param-optional">否</td><td>50</td><td>最少片段数，低于此值视为无效视频</td></tr>
                                    <tr><td>max_ad_percentage</td><td>int</td><td class="param-optional">否</td><td>90</td><td>最大广告占比（0-100），超过则拒绝学习</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/learn_video \
  -H "Content-Type: application/json" \
  -d '{"url": "https://example.com/video.m3u8", "min_segments": 50, "max_ad_percentage": 90}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "domain": "v.example.com",
  "segments_count": 120,
  "ad_count": 0,
  "ad_percentage": 12.5,
  "rule_updated": true
}</pre>
                            </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                批量学习多个视频的广告规则，支持多线程并发处理以提高效率。启用 multi_thread 后使用 curl_multi 并发请求 sites/learn_video 接口；当多线程失败率超过 80% 或不可用时自动回退为串行模式。并发数会被限制在 1-10 之间。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>默认值</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>urls</td><td>array</td><td class="param-required">是</td><td>-</td><td>视频 URL 数组</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>5</td><td>并发数（1-10）</td></tr>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>false</td><td>是否启用多线程</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/learn_batch \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://example.com/v1.m3u8", "https://example.com/v2.m3u8"], "multi_thread": true, "concurrency": 5}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "mode": "curl_multi",
  "concurrency": 5,
  "total": 2,
  "success_count": 2,
  "fail_count": 0,
  "total_time": 8423.51,
  "learned_domains": ["v.example.com"],
  "results": [
    {
      "url": "https://example.com/v1.m3u8",
      "success": true,
      "domain": "v.example.com",
      "segments_count": 120,
      "ad_count": 0,
      "duration": 4230.12
    }
  ]
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                批量分析多个视频的广告片段分布，仅分析统计不学习规则。启用 multi_thread 后并发请求 analyze 接口；多线程失败率超过 80% 或不可用时自动回退为串行模式，串行模式下直接调用 M3U8Parser 与 EnhancedAdRuleEngine 进行本地分析。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>默认值</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>urls</td><td>array</td><td class="param-required">是</td><td>-</td><td>视频 URL 数组</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>5</td><td>并发数（1-10）</td></tr>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>false</td><td>是否启用多线程</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/analyze_batch \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://example.com/v1.m3u8", "https://example.com/v2.m3u8"], "multi_thread": true, "concurrency": 5}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "mode": "curl_multi",
  "concurrency": 5,
  "total": 2,
  "success_count": 2,
  "fail_count": 0,
  "total_time": 5234.18,
  "results": [
    {
      "url": "https://example.com/v1.m3u8",
      "success": true,
      "domain": "v.example.com",
      "fast_mode": false,
      "stats": {
        "totalSegments": 120,
        "adSegments": 15,
        "discontinuityCount": 3,
        "sequenceJumps": 2,
        "adClusters": 4
      },
      "duration": 2612.34
    }
  ]
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/multi_thread/status 多线程状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">sites/multi_thread/status</span>
                        <span class="api-desc">获取多线程支持状态</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取当前运行环境对多线程的支持状态，返回可用的并发模式列表、推荐模式以及 PHP 运行环境信息（SAPI、pcntl 支持、curl_multi 支持）。供批量任务接口决定是否启用多线程及采用何种模式。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=sites/multi_thread/status</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "available": true,
  "modes": ["serial", "curl_multi"],
  "recommended_mode": "curl_multi",
  "php_sapi": "apache2handler",
  "pcntl_support": false,
  "curl_multi_support": true
}</pre>
                            </div>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取自动学习配置，包含是否启用、间隔天数、每次视频数、最大站点数等参数。同时返回上次学习时间（last_learn_time）及是否应该触发自动学习（should_auto_learn，根据配置的间隔天数判断）。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=sites/auto_learn/config</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "config": {
    "enabled": true,
    "interval_days": 3,
    "videos_per_site": 5,
    "max_sites_per_run": 5,
    "min_segments": 50,
    "max_ad_percentage": 90
  },
  "last_learn_time": "2026-07-05 10:30:00",
  "should_auto_learn": true
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="sites/auto_learn/config/save 保存自动学习配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">sites/auto_learn/config/save</span>
                        <span class="api-desc">保存自动学习配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                保存自动学习配置。提交的配置会与现有配置合并后持久化到数据库（sys_config 表的 auto_learn 键），未提供的字段保留默认值。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>enabled</td><td>bool</td><td class="param-optional">否</td><td>是否启用自动学习</td></tr>
                                    <tr><td>interval_days</td><td>int</td><td class="param-optional">否</td><td>触发间隔天数，默认 3</td></tr>
                                    <tr><td>videos_per_site</td><td>int</td><td class="param-optional">否</td><td>每个站点学习的视频数，默认 5</td></tr>
                                    <tr><td>max_sites_per_run</td><td>int</td><td class="param-optional">否</td><td>每次执行最大站点数，默认 5</td></tr>
                                    <tr><td>min_segments</td><td>int</td><td class="param-optional">否</td><td>最少片段数，默认 50</td></tr>
                                    <tr><td>max_ad_percentage</td><td>int</td><td class="param-optional">否</td><td>最大广告占比，默认 90</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/auto_learn/config/save \
  -H "Content-Type: application/json" \
  -d '{"enabled": true, "interval_days": 3, "videos_per_site": 5, "max_sites_per_run": 5}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "配置已更新"
}</pre>
                            </div>
                        </div>
                    </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                执行一次自动学习任务。根据配置自动从资源站获取视频并学习广告规则，执行完成后会更新 last_learn_time。启用 multi_thread 时使用 curl_multi 并发调用 sites/learn_video；当多线程失败率超过 80% 时自动回退到串行模式（runAutoLearn）。需先在配置中启用自动学习，否则多线程模式会返回错误。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>默认值</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>multi_thread</td><td>bool</td><td class="param-optional">否</td><td>false</td><td>是否启用多线程</td></tr>
                                    <tr><td>concurrency</td><td>int</td><td class="param-optional">否</td><td>5</td><td>并发数（1-10）</td></tr>
                                    <tr><td>max_sites</td><td>int</td><td class="param-optional">否</td><td>配置值</td><td>本次最大站点数，覆盖配置</td></tr>
                                    <tr><td>videos_per_site</td><td>int</td><td class="param-optional">否</td><td>配置值</td><td>每站视频数，覆盖配置</td></tr>
                                    <tr><td>keyword</td><td>string</td><td class="param-optional">否</td><td>-</td><td>搜索关键词，为空时获取最新视频</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=sites/auto_learn/run \
  -H "Content-Type: application/json" \
  -d '{"multi_thread": true, "concurrency": 5, "keyword": "庆余年"}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "自动学习完成（多线程模式）",
  "mode": "curl_multi",
  "concurrency": 5,
  "keyword": "庆余年",
  "sites_processed": 5,
  "total_learned": 18,
  "total_failed": 7,
  "total_time": 23451.89,
  "learned_domains": ["v.example.com", "v.lzcdn23.com"],
  "details": [
    {
      "site": "量子",
      "videos_checked": 5,
      "videos_learned": 4,
      "videos_failed": 1,
      "domains": {"v.example.com": 4},
      "fail_reasons": {"片段数不足": 1}
    }
  ]
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取自动学习运行状态，包含上次学习时间、是否应该触发自动学习（依据配置间隔天数判断）以及当前完整配置。适合用于面板展示或定时任务调度前判断是否需要执行。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=sites/auto_learn/status</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "last_learn_time": "2026-07-05 10:30:00",
  "should_auto_learn": true,
  "config": {
    "enabled": true,
    "interval_days": 3,
    "videos_per_site": 5,
    "max_sites_per_run": 5,
    "min_segments": 50,
    "max_ad_percentage": 90
  }
}</pre>
                            </div>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取官替功能完整配置，包含功能开关、默认站点、最大搜索站点数、缓存时间、平台规则列表（腾讯/爱奇艺/优酷/芒果TV/B站/搜狐/PP视频等）、搜索站点列表及匹配阈值。配置文件位于 gz/official_replace_config.php。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_replace/config</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "config": {
    "version": "1.0",
    "update_date": "2026-07-05 10:30:00",
    "enabled": true,
    "default_site": "量子",
    "max_search_sites": 5,
    "cache_ttl": 3600,
    "platforms": [
      {
        "name": "腾讯视频",
        "domain": "v.qq.com",
        "enabled": true,
        "pattern": "/v\\.qq\\.com\/.*?(?:vid=|\/)([a-zA-Z0-9]+)/i",
        "title_selector": "meta[property=\"og:title\"], meta[name=\"twitter:title\"], .video_title, h1",
        "priority": 1
      }
    ],
    "search_sites": ["量子", "最大", "猫眼", "红牛"],
    "match_threshold": 60
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_replace/config/save 保存官替配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">official_replace/config/save</span>
                        <span class="api-desc">保存官替配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                保存官替配置。提交的配置会整体覆盖现有配置并自动更新 update_date 时间戳，然后写回 gz/official_replace_config.php 文件。建议先通过 GET 接口获取完整配置再修改后提交，避免遗漏字段。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>enabled</td><td>bool</td><td class="param-optional">否</td><td>是否启用官替功能</td></tr>
                                    <tr><td>default_site</td><td>string</td><td class="param-optional">否</td><td>默认资源站名称</td></tr>
                                    <tr><td>max_search_sites</td><td>int</td><td class="param-optional">否</td><td>最大搜索站点数</td></tr>
                                    <tr><td>cache_ttl</td><td>int</td><td class="param-optional">否</td><td>缓存时间（秒）</td></tr>
                                    <tr><td>platforms</td><td>array</td><td class="param-optional">否</td><td>平台规则数组（含 name/domain/enabled/pattern/title_selector/priority）</td></tr>
                                    <tr><td>search_sites</td><td>array</td><td class="param-optional">否</td><td>搜索资源站名称数组</td></tr>
                                    <tr><td>match_threshold</td><td>int</td><td class="param-optional">否</td><td>匹配阈值（0-100），低于此值视为不匹配</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=official_replace/config/save \
  -H "Content-Type: application/json" \
  -d '{"enabled": true, "default_site": "量子", "max_search_sites": 5, "match_threshold": 60, "search_sites": ["量子", "最大"]}'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "保存成功"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_replace/platforms 官替平台列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace/platforms</span>
                        <span class="api-desc">获取官替平台列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取官替支持的视频平台列表及匹配规则，包含平台名称、域名、URL 匹配正则、标题选择器、优先级等。返回数据来自官替配置的 platforms 字段。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_replace/platforms</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platforms": [
    {
      "name": "腾讯视频",
      "domain": "v.qq.com",
      "enabled": true,
      "pattern": "/v\\.qq\\.com\/.*?(?:vid=|\/)([a-zA-Z0-9]+)/i",
      "title_selector": "meta[property=\"og:title\"], meta[name=\"twitter:title\"], .video_title, h1",
      "priority": 1
    },
    {
      "name": "爱奇艺",
      "domain": "iqiyi.com",
      "enabled": true,
      "pattern": "/iqiyi\\.com\/.*?([a-zA-Z0-9]{5,})/i",
      "title_selector": "meta[property=\"og:title\"], meta[name=\"twitter:title\"], .main_title, h1",
      "priority": 1
    }
  ],
  "total": 2
}</pre>
                            </div>
                        </div>
                    </div>
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
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                官替解析完整接口。传入官方视频平台 URL（腾讯/爱奇艺/优酷等），自动识别平台、抓取视频标题、解析季集信息，再到配置的资源站搜索匹配，返回最佳匹配资源站的 M3U8 地址、去广告跳转链接、所有候选结果及匹配详情（含 base_score、season_match、备选项 alternatives）。适合需要展示完整解析过程或调试匹配度的场景。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>官方视频 URL（支持 GET 与 POST 传参）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_replace/resolve&url=https://v.qq.com/x/cover/xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platform": "腾讯视频",
  "original_url": "https://v.qq.com/x/cover/xxx.html",
  "video_title": "庆余年 第二季",
  "video_name": "庆余年 第二季",
  "video_pic": "https://example.com/cover.jpg",
  "video_remarks": "更新至第36集",
  "original_title": "庆余年 第二季",
  "base_title": "庆余年",
  "season": "第二季",
  "season_num": 2,
  "episode": null,
  "episode_num": null,
  "video_id": "xxx",
  "match_score": 95.0,
  "base_score": 80.0,
  "season_match": true,
  "site": "量子",
  "m3u8_url": "https://v.lzcdn23.com/xxx/index.m3u8",
  "ad_skip_url": "https://your-domain.com/mx.php?action=mxjx&url=https%3A%2F%2Fv.lzcdn23.com%2Fxxx%2Findex.m3u8",
  "target_episode": "第1集",
  "all_urls": [
    {"name": "第1集", "url": "https://v.lzcdn23.com/xxx/1.m3u8"},
    {"name": "第2集", "url": "https://v.lzcdn23.com/xxx/2.m3u8"}
  ],
  "episodes": 2,
  "alternatives": [],
  "used_keyword": "庆余年 第二季",
  "request_time": 1783552229
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                官替解析精简版接口。返回去广告播放所需的精简信息（平台、标题、封面、M3U8 地址、去广告跳转链接、集数列表等），适合直接嵌入播放器使用。响应会设置 no-cache 头，每次请求都重新解析。底层调用 official_replace/resolve 的同一逻辑，仅裁剪输出字段。
                            </p>
                        </div>
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
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_replace/info&url=https://v.qq.com/x/cover/xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platform": "腾讯视频",
  "original_url": "https://v.qq.com/x/cover/xxx.html",
  "video_title": "庆余年 第二季",
  "video_name": "庆余年 第二季",
  "video_pic": "https://example.com/cover.jpg",
  "video_remarks": "更新至第36集",
  "match_score": 95.0,
  "site": "量子",
  "m3u8_url": "https://v.lzcdn23.com/xxx/index.m3u8",
  "target_episode": "第1集",
  "ad_skip_url": "https://your-domain.com/mx.php?action=mxjx&url=https%3A%2F%2Fv.lzcdn23.com%2Fxxx%2Findex.m3u8",
  "all_urls": [
    {"name": "第1集", "url": "https://v.lzcdn23.com/xxx/1.m3u8"},
    {"name": "第2集", "url": "https://v.lzcdn23.com/xxx/2.m3u8"}
  ],
  "episodes": 2,
  "timestamp": 1783552229
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 解析接口 -->
            <div class="category" id="category-parse">
                <h2 class="category-title"><span class="icon">🔗</span> 解析接口</h2>

                <div class="api-card" data-name="skip 去广告接口">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">skip</span>
                        <span class="api-desc">去广告接口（302跳转）</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                去广告跳转接口，解析 M3U8 视频并 302 重定向到去广告后的播放地址。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=skip&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                返回 HTTP 302 重定向，Location 头指向去广告后的 M3U8 地址。直接在浏览器或播放器中打开即可播放。
                            </p>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                返回 M3U8 去广告解析的详细信息，包括原始地址、去广告后地址、广告统计等。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频 URL</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=mxjx/info&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "code": 200,
  "success": true,
  "message": "解析成功",
  "data": {
    "original_url": "https://example.com/video.m3u8",
    "media_url": "https://example.com/video.m3u8",
    "domain": "example.com",
    "play_url": "https://your-domain.com/mx.php?action=mxjx&url=xxx",
    "has_domain_rules": true,
    "stats": {
      "total_segments": 120,
      "kept_segments": 100,
      "removed_segments": 20,
      "original_duration": 2400,
      "filtered_duration": 2000,
      "saved_duration": 400,
      "ad_percentage": 16.7
    }
  }
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                返回虾米解析的详细信息，包括原始 URL、媒体地址、视频类型等。
                            </p>
                        </div>
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
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=xiami_jx/info&url=https://v.youku.com/v_show/id_xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "code": 200,
  "success": true,
  "message": "解析成功",
  "data": {
    "original_url": "https://v.youku.com/v_show/id_xxx.html",
    "media_url": "https://example.com/play.m3u8",
    "type": "m3u8",
    "label": "HLS",
    "source": "xiami"
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="parse/list 统一解析接口列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">parse/list</span>
                        <span class="api-desc">统一视频解析 - 接口列表</span>
                        <span class="tag new">新</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                获取统一解析接口支持的所有解析类型和使用说明。所有解析能力全部集成在 mx.php 中，无需额外文件。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=parse/list</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "统一视频解析接口",
  "name": "Parse API - 统一解析接口",
  "version": "v1.0.0",
  "base_url": "https://your-domain.com/mx.php",
  "usage": {
    "智能解析": "mx.php?action=parse&url=视频链接",
    "指定类型": "mx.php?action=parse&type=xiami&url=视频链接",
    "获取详情": "mx.php?action=parse/info&url=视频链接",
    "接口列表": "mx.php?action=parse/list"
  },
  "supported_types": {
    "parse": "智能解析（自动判断类型）",
    "mxjx": "去广告解析（M3U8 去广告）",
    "xiami": "虾米解析（全网 VIP 视频）",
    "moxi": "沫兮解析（官方视频替换）",
    "official": "官方替换（智能匹配资源站）"
  }
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">支持的解析类型</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>类型</th><th>名称</th><th>适用场景</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>parse</td><td>智能解析</td><td>所有场景</td><td>自动判断视频类型，选择最佳解析方式</td></tr>
                                    <tr><td>mxjx</td><td>去广告解析</td><td>M3U8 视频</td><td>M3U8 视频去广告，自动识别并移除广告片段</td></tr>
                                    <tr><td>xiami</td><td>虾米解析</td><td>VIP 视频</td><td>全网 VIP 视频解析，支持腾讯、爱奇艺、优酷等</td></tr>
                                    <tr><td>moxi</td><td>沫兮解析</td><td>官方视频</td><td>沫兮 API 解析，支持官方视频智能替换</td></tr>
                                    <tr><td>official</td><td>官方替换</td><td>官方视频</td><td>官方视频链接智能匹配资源站无广告源</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="parse 统一解析视频">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">parse</span>
                        <span class="api-desc">统一视频解析 - 解析视频</span>
                        <span class="tag new">新</span>
                        <span class="tag hot">推荐</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                统一视频解析入口，支持 5 种解析方式，智能识别视频类型。<br>
                                <strong>全部内部集成，无外部 HTTP 自调用，性能更优。</strong>
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频链接（M3U8 地址或 VIP 视频播放页）</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>解析类型：parse / mxjx / xiami / moxi / official，默认 parse</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 1. 智能解析（推荐，自动选择最佳方式）
mx.php?action=parse&url=https://v.youku.com/v_show/id_xxx.html

// 2. 虾米解析（VIP 视频）
mx.php?action=parse&type=xiami&url=https://v.youku.com/v_show/id_xxx.html

// 3. 去广告解析（M3U8 视频）
mx.php?action=parse&type=mxjx&url=https://example.com/video.m3u8

// 4. 沫兮解析
mx.php?action=parse&type=moxi&url=https://v.qq.com/x/cover/xxx.html

// 5. 官方替换
mx.php?action=parse&type=official&url=https://www.iqiyi.com/v_xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例（成功）</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "code": 200,
  "message": "虾米解析成功",
  "type": "xiami",
  "type_name": "虾米解析",
  "original_url": "https://v.youku.com/v_show/id_xxx.html",
  "play_url": "https://example.com/play/video.m3u8",
  "video_name": "",
  "is_official": true,
  "is_m3u8": false,
  "raw": {
    "success": true,
    "code": 200,
    "message": "解析成功",
    "play_url": "https://example.com/play/video.m3u8",
    "video_type": "m3u8",
    "label": "HLS",
    "original_url": "https://v.youku.com/v_show/id_xxx.html",
    "source": "xiami"
  }
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例（失败）</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": false,
  "code": 500,
  "message": "未获取到资源",
  "type": "xiami",
  "type_name": "虾米解析",
  "original_url": "https://v.youku.com/v_show/id_xxx.html",
  "play_url": "",
  "video_name": "",
  "is_official": true,
  "is_m3u8": false
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应字段说明</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>字段</th><th>类型</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>success</td><td>bool</td><td>是否成功</td></tr>
                                    <tr><td>code</td><td>int</td><td>状态码：200=成功，400=参数错误，500=解析失败</td></tr>
                                    <tr><td>message</td><td>string</td><td>状态信息</td></tr>
                                    <tr><td>type</td><td>string</td><td>使用的解析类型</td></tr>
                                    <tr><td>type_name</td><td>string</td><td>解析类型名称</td></tr>
                                    <tr><td>original_url</td><td>string</td><td>原始视频链接</td></tr>
                                    <tr><td>play_url</td><td>string</td><td>解析后的播放地址</td></tr>
                                    <tr><td>video_name</td><td>string</td><td>视频名称（部分接口支持）</td></tr>
                                    <tr><td>is_official</td><td>bool</td><td>是否为官方视频平台链接</td></tr>
                                    <tr><td>is_m3u8</td><td>bool</td><td>是否为 M3U8 视频</td></tr>
                                    <tr><td>raw</td><td>object</td><td>原始解析接口返回的完整数据（可选）</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">智能解析规则</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                当 <code>type=parse</code> 时，按以下规则自动选择解析方式：
                            </p>
                            <table class="param-table">
                                <thead>
                                    <tr><th>视频类型</th><th>自动选择</th><th>判断条件</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>M3U8 视频</td><td>mxjx（去广告解析）</td><td>URL 包含 .m3u8 后缀</td></tr>
                                    <tr><td>官方 VIP 视频</td><td>xiami（虾米解析）</td><td>域名为腾讯/爱奇艺/优酷/芒果TV/B站等</td></tr>
                                    <tr><td>其他视频</td><td>mxjx（去广告解析）</td><td>以上都不匹配时</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="parse/info 统一解析详情">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">parse/info</span>
                        <span class="api-desc">统一视频解析 - 详细信息</span>
                        <span class="tag new">新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                与 <code>parse</code> 接口功能相同，返回完整的解析详情信息。
                            </p>
                        </div>
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
<pre>// 获取详细解析信息
mx.php?action=parse/info&url=https://v.youku.com/v_show/id_xxx.html

// 指定类型获取详情
mx.php?action=parse/info&type=xiami&url=https://v.youku.com/v_show/id_xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "code": 200,
  "message": "虾米解析成功",
  "type": "xiami",
  "type_name": "虾米解析",
  "original_url": "https://v.youku.com/v_show/id_xxx.html",
  "play_url": "https://example.com/play/video.m3u8",
  "video_name": "",
  "is_official": true,
  "is_m3u8": false,
  "raw": {
    "success": true,
    "code": 200,
    "message": "解析成功",
    "play_url": "https://example.com/play/video.m3u8",
    "video_type": "m3u8",
    "label": "HLS",
    "original_url": "https://v.youku.com/v_show/id_xxx.html",
    "source": "xiami"
  }
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应字段说明</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>字段</th><th>类型</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>success</td><td>bool</td><td>是否成功</td></tr>
                                    <tr><td>code</td><td>int</td><td>状态码：200=成功，400=参数错误，500=解析失败</td></tr>
                                    <tr><td>message</td><td>string</td><td>状态信息</td></tr>
                                    <tr><td>type</td><td>string</td><td>使用的解析类型</td></tr>
                                    <tr><td>type_name</td><td>string</td><td>解析类型名称</td></tr>
                                    <tr><td>original_url</td><td>string</td><td>原始视频链接</td></tr>
                                    <tr><td>play_url</td><td>string</td><td>解析后的播放地址</td></tr>
                                    <tr><td>is_official</td><td>bool</td><td>是否为官方视频平台链接</td></tr>
                                    <tr><td>is_m3u8</td><td>bool</td><td>是否为 M3U8 视频</td></tr>
                                    <tr><td>raw</td><td>object</td><td>原始解析接口返回的完整数据</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="xiami_jx 虾米解析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">xiami_jx</span>
                        <span class="api-desc">虾米解析 - VIP 视频解析</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                虾米解析（jx.xmflv.cc）API，支持全网 VIP 视频解析。<br>
                                采用 AES-256-CBC + Zero Padding 签名加密，多节点轮询。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频播放页链接</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 解析视频
mx.php?action=xiami_jx&url=https://v.youku.com/v_show/id_xxx.html

// 获取详细信息
mx.php?action=xiami_jx/info&url=https://v.qq.com/x/page/xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "code": 200,
  "success": true,
  "message": "解析成功",
  "data": {
    "original_url": "https://v.youku.com/v_show/id_xxx.html",
    "media_url": "https://example.com/play.m3u8",
    "type": "m3u8",
    "label": "HLS",
    "source": "xiami"
  }
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">支持平台</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>平台</th><th>域名</th><th>支持</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>腾讯视频</td><td>v.qq.com</td><td>✅</td></tr>
                                    <tr><td>爱奇艺</td><td>iqiyi.com</td><td>✅</td></tr>
                                    <tr><td>优酷</td><td>youku.com</td><td>✅</td></tr>
                                    <tr><td>芒果TV</td><td>mgtv.com</td><td>✅</td></tr>
                                    <tr><td>哔哩哔哩</td><td>bilibili.com</td><td>✅</td></tr>
                                    <tr><td>搜狐视频</td><td>sohu.com</td><td>✅</td></tr>
                                    <tr><td>PPTV</td><td>pptv.com</td><td>✅</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="moxi 沫兮解析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">moxi</span>
                        <span class="api-desc">沫兮 API - 视频解析</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                沫兮 API 视频解析接口，支持官方视频智能替换，自动识别视频标题和集数。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频链接</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>播放类型</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 沫兮解析
mx.php?action=moxi&url=https://v.qq.com/x/cover/xxx.html

// API 别名
mx.php?action=moxi/api&url=https://www.iqiyi.com/v_xxx.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "code": 200,
  "url": "https://your-domain.com/mx.php?action=mxjx&url=xxx.m3u8",
  "msg": "解析成功",
  "jm": "视频名称",
  "js": "第1集",
  "time": "2026-07-08 12:00:00",
  "kfz": "沫兮API - 在线视频解析"
}</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应字段说明</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>字段</th><th>类型</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>code</td><td>int</td><td>状态码，200=成功</td></tr>
                                    <tr><td>url</td><td>string</td><td>解析后的播放地址（已拼接去广告）</td></tr>
                                    <tr><td>msg</td><td>string</td><td>状态信息</td></tr>
                                    <tr><td>jm</td><td>string</td><td>剧名字段（视频名称）</td></tr>
                                    <tr><td>js</td><td>string</td><td>集数字段</td></tr>
                                    <tr><td>time</td><td>string</td><td>解析时间</td></tr>
                                    <tr><td>kfz</td><td>string</td><td>开发者标识</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="mxjx 去广告解析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">mxjx</span>
                        <span class="api-desc">M3U8 去广告解析</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                M3U8 视频去广告接口，自动识别并移除广告片段，输出纯净的 M3U8 播放列表。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 直接使用（返回 M3U8 内容）
mx.php?action=mxjx&url=https://example.com/video.m3u8

// 获取详细信息（JSON 格式）
mx.php?action=mxjx/info&url=https://example.com/video.m3u8</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">mxjx/info 响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "code": 200,
  "success": true,
  "message": "解析成功",
  "data": {
    "original_url": "https://example.com/video.m3u8",
    "media_url": "https://example.com/video.m3u8",
    "domain": "example.com",
    "play_url": "https://your-domain.com/mx.php?action=mxjx&url=xxx",
    "has_domain_rules": true,
    "stats": {
      "total_segments": 120,
      "kept_segments": 100,
      "removed_segments": 20,
      "original_duration": 2400,
      "filtered_duration": 2000,
      "saved_duration": 400,
      "ad_percentage": 16.7
    }
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_replace 官方替换">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_replace</span>
                        <span class="api-desc">官方视频智能替换</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">
                                官方视频链接智能匹配资源站无广告源，自动从已配置的资源站中搜索匹配视频。
                            </p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>官方视频播放页链接</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>// 直接解析（302跳转）
mx.php?action=official_replace/resolve&url=https://v.qq.com/x/cover/xxx.html

// 获取详细信息
mx.php?action=official_replace/info&url=https://www.iqiyi.com/v_xxx.html

// 获取支持的平台列表
mx.php?action=official_replace/platforms</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">official_replace/info 响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platform": "iqiyi",
  "original_url": "https://www.iqiyi.com/v_xxx.html",
  "video_title": "视频标题",
  "video_name": "xxx",
  "video_pic": "https://example.com/cover.jpg",
  "video_remarks": "全24集",
  "match_score": 95.5,
  "site": "资源站名称",
  "m3u8_url": "https://example.com/video.m3u8",
  "target_episode": "第1集",
  "ad_skip_url": "https://your-domain.com/mx.php?action=mxjx&url=xxx.m3u8",
  "episodes": 24,
  "all_urls": [
    { "name": "第1集", "url": "https://example.com/ep1.m3u8" },
    { "name": "第2集", "url": "https://example.com/ep2.m3u8" }
  ],
  "timestamp": 1720411200
}</pre>
                            </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取系统当前版本信息，包括当前版本号、Git commit 哈希以及 version.php 文件中记录的内容。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=update/version</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "current_version": "1.0.0",
  "current_commit": "a1b2c3d",
  "version_file": "1.0.0"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/check 检查更新">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/check</span>
                        <span class="api-desc">检查是否有新版本</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">检查远程仓库是否存在新版本，返回是否有更新、当前版本与最新版本信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=update/check</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "has_update": true,
  "current_version": "1.0.0",
  "current_commit": "a1b2c3d",
  "latest_version": "1.1.0",
  "latest_commit": "e4f5g6h",
  "message": "发现新版本"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/integrity 完整性检查">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/integrity</span>
                        <span class="api-desc">文件完整性检查</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">校验系统核心文件是否被篡改或缺失，返回被修改和缺失的文件列表。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=update/integrity</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "完整性检查完成",
  "total_files": 120,
  "modified_files": 0,
  "missing_files": 0,
  "files": []
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/download 下载更新">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/download</span>
                        <span class="api-desc">下载并安装更新</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">从远程仓库下载最新版本文件并安装更新，无需请求参数。建议在执行前先调用 update/check 确认存在新版本。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=update/download</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "更新下载并安装成功",
  "updated_files": 15,
  "new_version": "1.1.0"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/clear_cache 清理更新缓存">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/clear_cache</span>
                        <span class="api-desc">清理更新缓存</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">清理系统运行过程中产生的各类缓存文件，无需请求参数。返回清理结果及缓存统计信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=update/clear_cache</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "缓存清理成功",
  "cache_info": {
    "cleared": true,
    "size": 1048576
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/system_info 系统信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/system_info</span>
                        <span class="api-desc">获取系统信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取服务器运行环境信息，包括 PHP 版本、操作系统、磁盘空间、内存占用等。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=update/system_info</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "php_version": "8.2.0",
  "os": "Linux",
  "disk_free": "10.5G",
  "memory_usage": "32M"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/backup/list 备份文件列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">update/backup/list</span>
                        <span class="api-desc">获取备份文件列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取所有系统备份文件列表，包括文件名、创建时间、文件大小等信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=update/backup/list</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/backup/create 创建系统备份">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/backup/create</span>
                        <span class="api-desc">创建系统备份</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">创建完整的系统备份，包括程序文件、配置文件和数据文件。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=update/backup/create</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/backup/restore 恢复备份">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/backup/restore</span>
                        <span class="api-desc">从备份恢复</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">从指定的备份文件恢复系统。请谨慎操作，恢复后当前数据将被覆盖。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>filename</td><td>string</td><td class="param-required">是</td><td>备份文件名</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="update/backup/delete 删除备份">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">update/backup/delete</span>
                        <span class="api-desc">删除备份文件</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">删除指定的备份文件。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>filename</td><td>string</td><td class="param-required">是</td><td>备份文件名</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取当前系统授权状态信息，包括授权域名、授权码、到期时间及联系方式。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=auth/info</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "authorized": true,
  "domain": "example.com",
  "auth_code": "xxxx-xxxx-xxxx-xxxx",
  "expire_at": "2026-12-31",
  "contact": "QQ2094332348"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="auth/validate 验证授权">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/validate</span>
                        <span class="api-desc">验证授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">同时执行本地与远程授权校验，返回各自校验结果及综合有效性，无需请求参数。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=auth/validate</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "local_valid": true,
  "remote_valid": true,
  "all_valid": true,
  "error": null
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="auth/set 设置授权码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/set</span>
                        <span class="api-desc">设置授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">设置系统授权码，以 JSON Body 形式提交。授权码为空时返回 400 错误。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>auth_code</td><td>string</td><td class="param-required">是</td><td>授权码</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=auth/set \
  -H "Content-Type: application/json" \
  -d '{
    "auth_code": "xxxx-xxxx-xxxx-xxxx"
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "授权码设置成功"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="auth/generate 生成授权码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">auth/generate</span>
                        <span class="api-desc">生成授权码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">根据指定域名生成对应的授权码，参数 domain 可通过 GET 或 POST 方式提交。domain 为空时返回 400 错误。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>需要生成授权码的域名</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=auth/generate \
  -d "domain=example.com"</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "domain": "example.com",
  "auth_code": "xxxx-xxxx-xxxx-xxxx"
}</pre>
                            </div>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取数据库启用状态、数据库类型、各核心表是否存在、规则/资源站/代理数量、是否已迁移及当前配置信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=db/status</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "status": {
    "use_db": true,
    "db_type": "sqlite",
    "tables": {
      "sys_config": true,
      "domain_rules": true,
      "resource_sites": true,
      "proxies": true,
      "official_sites": true,
      "official_platforms": true
    },
    "rule_count": 12,
    "site_count": 5,
    "proxy_count": 3,
    "migrated": true,
    "config": {
      "type": "sqlite",
      "sqlite_path": "/var/www/html/db/data.db"
    }
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="db/config/save 保存数据库配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/config/save</span>
                        <span class="api-desc">保存数据库配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">保存数据库连接配置到 db/db_config.php 文件，保存后会尝试连接并初始化表结构。配置写入失败或连接异常时返回相应错误信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>数据库类型，sqlite 或 mysql，默认 sqlite</td></tr>
                                    <tr><td>sqlite_path</td><td>string</td><td class="param-optional">否</td><td>SQLite 数据库文件路径（type=sqlite 时使用）</td></tr>
                                    <tr><td>mysql_host</td><td>string</td><td class="param-optional">否</td><td>MySQL 主机地址，默认 127.0.0.1</td></tr>
                                    <tr><td>mysql_port</td><td>int</td><td class="param-optional">否</td><td>MySQL 端口，默认 3306</td></tr>
                                    <tr><td>mysql_dbname</td><td>string</td><td class="param-optional">否</td><td>MySQL 数据库名，默认 m3u8_ad</td></tr>
                                    <tr><td>mysql_username</td><td>string</td><td class="param-optional">否</td><td>MySQL 用户名，默认 root</td></tr>
                                    <tr><td>mysql_password</td><td>string</td><td class="param-optional">否</td><td>MySQL 密码，默认空</td></tr>
                                    <tr><td>mysql_charset</td><td>string</td><td class="param-optional">否</td><td>MySQL 字符集，默认 utf8mb4</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=db/config/save \
  -H "Content-Type: application/json" \
  -d '{
    "type": "mysql",
    "mysql_host": "127.0.0.1",
    "mysql_port": 3306,
    "mysql_dbname": "m3u8_ad",
    "mysql_username": "root",
    "mysql_password": "123456",
    "mysql_charset": "utf8mb4"
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "配置保存成功，数据库连接正常"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="db/test_connection 测试数据库连接">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/test_connection</span>
                        <span class="api-desc">测试数据库连接</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">使用提交的配置参数测试数据库连接，不会持久化保存配置。成功时返回数据库类型、版本及表数量等信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>数据库类型，sqlite 或 mysql，默认 sqlite</td></tr>
                                    <tr><td>sqlite_path</td><td>string</td><td class="param-optional">否</td><td>SQLite 数据库文件路径（type=sqlite 时使用）</td></tr>
                                    <tr><td>mysql_host</td><td>string</td><td class="param-optional">否</td><td>MySQL 主机地址，默认 127.0.0.1</td></tr>
                                    <tr><td>mysql_port</td><td>int</td><td class="param-optional">否</td><td>MySQL 端口，默认 3306</td></tr>
                                    <tr><td>mysql_dbname</td><td>string</td><td class="param-optional">否</td><td>MySQL 数据库名，默认 m3u8_ad</td></tr>
                                    <tr><td>mysql_username</td><td>string</td><td class="param-optional">否</td><td>MySQL 用户名，默认 root</td></tr>
                                    <tr><td>mysql_password</td><td>string</td><td class="param-optional">否</td><td>MySQL 密码，默认空</td></tr>
                                    <tr><td>mysql_charset</td><td>string</td><td class="param-optional">否</td><td>MySQL 字符集，默认 utf8mb4</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=db/test_connection \
  -H "Content-Type: application/json" \
  -d '{
    "type": "mysql",
    "mysql_host": "127.0.0.1",
    "mysql_port": 3306,
    "mysql_dbname": "m3u8_ad",
    "mysql_username": "root",
    "mysql_password": "123456"
  }'</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "数据库连接成功！",
  "info": {
    "type": "mysql",
    "connected": true,
    "version": "8.0.30",
    "table_count": 6
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="db/migrate 数据库迁移">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/migrate</span>
                        <span class="api-desc">执行数据库迁移</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">将文件存储的数据迁移到数据库中，无需请求参数。数据库未启用时返回 400 错误，迁移失败返回 500 状态码。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=db/migrate</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "数据迁移完成",
  "migrated": {
    "rules": 12,
    "sites": 5,
    "proxies": 3
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="db/init 初始化数据库">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">db/init</span>
                        <span class="api-desc">初始化数据库</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">初始化数据库表结构，创建所需的核心表，无需请求参数。数据库未启用时返回 400 错误，初始化失败返回 500 状态码。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=db/init</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "message": "数据库表初始化完成"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PT引擎 -->
            <div class="category" id="category-pt">
                <h2 class="category-title"><span class="icon">🚀</span> PT 引擎</h2>

                <div class="api-card" data-name="pt/status PT引擎状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">pt/status</span>
                        <span class="api-desc">PT引擎状态信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取 PT 引擎的完整状态信息，包括平台适配器列表、AI 分析器配置、广告跳过引擎状态及各模块统计数据。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=pt/status</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platforms": [
    {
      "id": "iqiyi",
      "name": "爱奇艺",
      "status": "active",
      "adapter_version": "1.0.0"
    }
  ],
  "ai_analyzer": {
    "enabled": true,
    "weight": 0.7,
    "model": "default"
  },
  "ad_skip_engine": {
    "enabled": true,
    "total_processed": 1200,
    "success_rate": 92.5
  },
  "stats": {
    "total_requests": 5000,
    "avg_process_time": 0.35
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="pt/test PT引擎匹配测试">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">pt/test</span>
                        <span class="api-desc">PT引擎匹配测试</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">使用 PT 引擎对指定视频链接进行匹配测试，返回匹配的平台、匹配度评分及分析结果。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>视频链接（播放页或M3U8地址）</td></tr>
                                    <tr><td>platform</td><td>string</td><td class="param-optional">否</td><td>指定平台ID，不传则自动匹配</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=pt/test&url=https://v.youku.com/v_show/id_XXX.html</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "matched": true,
  "platform": {
    "id": "youku",
    "name": "优酷",
    "confidence": 95.5
  },
  "analysis": {
    "video_title": "视频标题",
    "episode": "第1集",
    "duration": 2700
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="pt/adskip PT去广告处理">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">pt/adskip</span>
                        <span class="api-desc">PT去广告处理</span>
                        <span class="tag new">NEW</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">使用 PT 引擎对 M3U8 内容进行去广告处理，支持平台特定的广告识别算法和智能插播检测。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>platform</td><td>string</td><td class="param-optional">否</td><td>平台ID，不传则自动检测</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=pt/adskip&url=https://example.com/video.m3u8&platform=iqiyi</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "platform": "iqiyi",
  "stats": {
    "total_segments": 200,
    "ad_segments": 15,
    "kept_segments": 185,
    "ad_percentage": 7.5
  },
  "ad_ranges": [
    { "start": 0, "end": 30, "duration": 30, "source": "pt" }
  ],
  "output_m3u8": "#EXTM3U\n..."
}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI智能 -->
            <div class="category" id="category-ai">
                <h2 class="category-title"><span class="icon">🤖</span> AI 智能</h2>

                <div class="api-card" data-name="ai/smart_process AI智能处理">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/smart_process</span>
                        <span class="api-desc">AI智能处理</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI 智能处理接口，支持 analyze（分析）和 full（完整处理）两种模式，可自动保存学习到的规则。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>mode</td><td>string</td><td class="param-optional">否</td><td>模式：analyze（仅分析）/ full（完整处理），默认 analyze</td></tr>
                                    <tr><td>auto_save</td><td>int</td><td class="param-optional">否</td><td>是否自动保存规则，1=是，默认 0</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=ai/smart_process&url=https://example.com/video.m3u8&mode=full&auto_save=1</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/pro_detect AI专业检测">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/pro_detect</span>
                        <span class="api-desc">AI专业检测</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI 专业广告检测，可配置 Z-Score 阈值和置信度参数，提供更精准的广告识别。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>zscore_threshold</td><td>float</td><td class="param-optional">否</td><td>Z-Score 阈值，默认 2.0</td></tr>
                                    <tr><td>min_confidence</td><td>float</td><td class="param-optional">否</td><td>最小置信度，默认 0.6</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/skip AI去广告处理">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/skip</span>
                        <span class="api-desc">AI去广告处理</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI 驱动的去广告处理，带防护机制，支持自动学习和深度分析选项。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>safeguard</td><td>int</td><td class="param-optional">否</td><td>防护机制，1=启用，默认 1</td></tr>
                                    <tr><td>auto_learn</td><td>int</td><td class="param-optional">否</td><td>自动学习，1=启用，默认 0</td></tr>
                                    <tr><td>deep</td><td>int</td><td class="param-optional">否</td><td>深度分析，1=启用，默认 0</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/insert_detect AI插播检测">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/insert_detect</span>
                        <span class="api-desc">AI插播广告检测</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI 插播广告检测，识别片头、片尾及片中插播的广告内容。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>detect_opening</td><td>int</td><td class="param-optional">否</td><td>检测片头，1=是，默认 1</td></tr>
                                    <tr><td>detect_ending</td><td>int</td><td class="param-optional">否</td><td>检测片尾，1=是，默认 1</td></tr>
                                    <tr><td>detect_middle</td><td>int</td><td class="param-optional">否</td><td>检测片中插播，1=是，默认 1</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/subtitle_detect AI字幕广告检测">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/subtitle_detect</span>
                        <span class="api-desc">AI滚动字幕广告分析</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI 滚动字幕广告分析，识别视频中的滚动字幕广告并标记。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/md5_analyze AI-MD5特征分析">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/md5_analyze</span>
                        <span class="api-desc">AI-MD5特征码分析</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI-MD5 特征码分析，通过分析 TS 片段的 MD5 特征识别广告片段。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                    <tr><td>max_count</td><td>int</td><td class="param-optional">否</td><td>最大分析片段数，默认 30</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="ai/md5_detect AI-MD5智能去广告">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">ai/md5_detect</span>
                        <span class="api-desc">AI-MD5智能去广告</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">AI-MD5 智能去广告，结合规则过滤和 MD5 特征双重检测，提供更精准的去广告效果。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>url</td><td>string</td><td class="param-required">是</td><td>M3U8 视频链接</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 广告特征码 -->
            <div class="category" id="category-signatures">
                <h2 class="category-title"><span class="icon">🏷️</span> 广告特征码</h2>

                <div class="api-card" data-name="signatures/list 特征码列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">signatures/list</span>
                        <span class="api-desc">获取广告特征码列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取指定域名的广告特征码列表，支持分页和类型筛选。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-optional">否</td><td>特征码类型：md5 / duration / tag</td></tr>
                                    <tr><td>page</td><td>int</td><td class="param-optional">否</td><td>页码，默认 1</td></tr>
                                    <tr><td>page_size</td><td>int</td><td class="param-optional">否</td><td>每页数量，默认 20</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=signatures/list&domain=example.com&type=md5</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="signatures/add 添加特征码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">signatures/add</span>
                        <span class="api-desc">添加广告特征码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">手动添加广告特征码到数据库。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>domain</td><td>string</td><td class="param-required">是</td><td>域名</td></tr>
                                    <tr><td>type</td><td>string</td><td class="param-required">是</td><td>特征码类型：md5 / duration / tag</td></tr>
                                    <tr><td>value</td><td>string</td><td class="param-required">是</td><td>特征码值</td></tr>
                                    <tr><td>confidence</td><td>float</td><td class="param-optional">否</td><td>置信度 0-100，默认 80</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="signatures/delete 删除特征码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">signatures/delete</span>
                        <span class="api-desc">删除广告特征码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">删除指定 ID 的广告特征码。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>id</td><td>int</td><td class="param-required">是</td><td>特征码 ID</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="signatures/stats 特征码统计">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">signatures/stats</span>
                        <span class="api-desc">特征码统计信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取广告特征码的统计信息，包括各类型数量、域名分布、置信度分布等。</p>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="signatures/clean 清理低置信度特征码">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">signatures/clean</span>
                        <span class="api-desc">清理低置信度特征码</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">清理低于指定置信度的特征码，优化数据库性能。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>min_confidence</td><td>float</td><td class="param-optional">否</td><td>最小置信度阈值，默认 30</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 官方站点管理 -->
            <div class="category" id="category-official_sites">
                <h2 class="category-title"><span class="icon">🏛️</span> 官方站点</h2>

                <div class="api-card" data-name="official_sites/status 官方站点状态">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_sites/status</span>
                        <span class="api-desc">官方站点启用状态</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取官方站点功能的启用状态和全局设置。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_sites/status</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_sites/list 官方站点列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_sites/list</span>
                        <span class="api-desc">官方站点列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取所有官方站点列表，包括站点名称、域名、状态等信息。</p>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_sites/search_all 搜索所有官方站点">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official_sites/search_all</span>
                        <span class="api-desc">搜索所有官方站点视频</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">在所有官方站点中搜索视频，返回各站点的搜索结果。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>keyword</td><td>string</td><td class="param-required">是</td><td>搜索关键词</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official_sites/search_all&keyword=狂飙</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official_sites/toggle 启用禁用官方站点">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">official_sites/toggle</span>
                        <span class="api-desc">启用/禁用官方站点</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">启用或禁用官方站点功能。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>enabled</td><td>int</td><td class="param-required">是</td><td>1=启用，0=禁用</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 播放器配置 -->
            <div class="category" id="category-player">
                <h2 class="category-title"><span class="icon">🎮</span> 播放器</h2>

                <div class="api-card" data-name="player/config/save 保存播放器配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method post">POST</span>
                        <span class="api-path">player/config/save</span>
                        <span class="api-desc">保存播放器配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">保存播放器配置到 gz/player_config.php 文件。支持配置默认播放器、自动播放、预加载、HLS 配置等。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数 (JSON Body)</div>
                            <table class="param-table">
                                <thead><tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr></thead>
                                <tbody>
                                    <tr><td>player</td><td>string</td><td class="param-optional">否</td><td>默认播放器：dplayer / videojs / jwplayer 等</td></tr>
                                    <tr><td>autoplay</td><td>bool</td><td class="param-optional">否</td><td>是否自动播放</td></tr>
                                    <tr><td>preload</td><td>string</td><td class="param-optional">否</td><td>预加载模式：auto / metadata / none</td></tr>
                                    <tr><td>hls_config</td><td>object</td><td class="param-optional">否</td><td>HLS 配置对象</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>curl -X POST mx.php?action=player/config/save \
  -H "Content-Type: application/json" \
  -d '{
    "player": "dplayer",
    "autoplay": false,
    "preload": "auto",
    "hls_config": {
      "enableWorker": true,
      "maxBufferLength": 30
    }
  }'</pre>
                            </div>
                        </div>
                    </div>
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
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取系统基本信息，包括系统名称、版本、PHP 版本、数据库状态、可用特性及统计信息。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=info</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "name": "M3U8广告跳过系统",
  "version": "1.0.0",
  "commit": "a1b2c3d",
  "updated_at": "2026-07-01",
  "php_version": "8.2.0",
  "database_enabled": true,
  "database_type": "sqlite",
  "features": {
    "ad_detection": true,
    "multi_thread": true,
    "database_cache": true,
    "official_replace": true
  },
  "timestamp": 1720411200,
  "stats": {
    "rules": 12,
    "sites": 5,
    "proxies": 3
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="version 版本信息">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">version</span>
                        <span class="api-desc">版本信息</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取系统版本信息，包括版本号、commit、更新时间、version.php 文件是否存在、PHP 版本及数据库类型。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=version</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "version": "1.0.0",
  "commit": "a1b2c3d",
  "updated_at": "2026-07-01",
  "version_file": true,
  "php_version": "8.2.0",
  "database_type": "sqlite"
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="player/config 播放器配置">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">player/config</span>
                        <span class="api-desc">播放器配置</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取播放器配置信息，读取 gz/player_config.php 文件并与默认配置合并后返回。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=player/config</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "config": {
    "player": "dplayer",
    "autoplay": false,
    "preload": "auto",
    "api_base_url": "",
    "hls_config": {
      "enableWorker": true,
      "lowLatencyMode": false,
      "maxBufferLength": 30,
      "maxMaxBufferLength": 600,
      "minBufferLength": 2,
      "maxBufferSize": 60000000,
      "maxBufferHole": 0.5,
      "highBufferWatchdogPeriod": 2,
      "startLevel": -1,
      "capLevelToPlayerSize": false
    }
  }
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="proxy/list 代理列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">proxy/list</span>
                        <span class="api-desc">代理列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取当前可用的代理列表，按响应时间、优先级和失败次数排序，并返回代理统计信息。代理模块未初始化时返回 500 错误。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=proxy/list</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "proxies": [
    {
      "id": "1",
      "name": "代理1",
      "type": "http",
      "url": "http://1.2.3.4:8080",
      "host": "1.2.3.4",
      "port": 8080,
      "response_time": 0.35,
      "success_count": 120,
      "fail_count": 2,
      "priority": 100,
      "last_check": "2026-07-08 10:00:00",
      "last_success": "2026-07-08 10:00:00"
    }
  ],
  "count": 1,
  "stats": {
    "total": 3,
    "active": 1,
    "auto_switch": true
  },
  "auto_switch": true
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="official/list 官替站点列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">official/list</span>
                        <span class="api-desc">官替站点列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取官替（官方替换）资源站列表，返回站点数据、总数及官替功能是否启用。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">请求参数</div>
                            <table class="param-table">
                                <thead>
                                    <tr><th>参数</th><th>类型</th><th>必填</th><th>说明</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>include_paused</td><td>string</td><td class="param-optional">否</td><td>是否包含已暂停站点，传 1 表示包含</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=official/list
mx.php?action=official/list&include_paused=1</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "sites": [
    {
      "name": "资源站1",
      "api_url": "https://example.com/api.php",
      "status": "active"
    }
  ],
  "total": 1,
  "enabled": true
}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="api-card" data-name="proxies/list 代理配置列表">
                    <div class="api-header" onclick="toggleApi(this)">
                        <span class="api-method get">GET</span>
                        <span class="api-path">proxies/list</span>
                        <span class="api-desc">代理配置列表</span>
                        <span class="api-arrow">▶</span>
                    </div>
                    <div class="api-body">
                        <div class="api-section">
                            <div class="api-section-title">说明</div>
                            <p style="color: var(--text-regular); font-size: 0.9em;">获取所有代理配置列表，包括全部代理及活跃代理数量统计。代理模块未初始化时返回 500 错误。</p>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">调用示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>mx.php?action=proxies/list</pre>
                            </div>
                        </div>
                        <div class="api-section">
                            <div class="api-section-title">响应示例</div>
                            <div class="code-block">
                                <button class="copy-btn" onclick="copyCode(this)">复制</button>
<pre>{
  "success": true,
  "proxies": [
    {
      "id": "1",
      "name": "代理1",
      "type": "http",
      "host": "1.2.3.4",
      "port": 8080,
      "status": "active",
      "priority": 100
    }
  ],
  "total": 3,
  "active_count": 1
}</pre>
                            </div>
                        </div>
                    </div>
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
