<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M3U8 广告分析后台</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/dplayer/1.27.1/DPlayer.min.css">
    <script src="https://cdn.bootcdn.net/ajax/libs/hls.js/1.5.15/hls.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/dplayer/1.27.1/DPlayer.min.js"></script>
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
        }

        [data-theme="gold"] {
            --primary: #9f6d1d;
            --primary-light: #fff89c;
            --primary-gradient: linear-gradient(135deg, #9f6d1d 0%, #d4a017 100%);
            --primary-bg: #fdf6ec;
            --primary-text: #e6a23c;
            --success: #95c44a;
            --success-light: #f0f9eb;
            --success-border: #e1f3d8;
        }

        [data-theme="green"] {
            --primary: #217e25;
            --primary-light: #baff54;
            --primary-gradient: linear-gradient(135deg, #217e25 0%, #52c41a 100%);
            --primary-bg: #f0f9eb;
            --primary-text: #67c23a;
            --success: #52c41a;
        }

        [data-theme="blue"] {
            --primary: #171be1;
            --primary-light: #80f1ff;
            --primary-gradient: linear-gradient(135deg, #171be1 0%, #409eff 100%);
            --primary-bg: #ecf5ff;
            --primary-text: #409eff;
        }

        [data-theme="cyan"] {
            --primary: #03626c;
            --primary-light: #6efaff;
            --primary-gradient: linear-gradient(135deg, #03626c 0%, #13c2c2 100%);
            --primary-bg: #e6fffb;
            --primary-text: #13c2c2;
        }

        [data-theme="red"] {
            --primary: #c41d1d;
            --primary-light: #ff9c9c;
            --primary-gradient: linear-gradient(135deg, #c41d1d 0%, #f56c6c 100%);
            --primary-bg: #fff1f0;
            --primary-text: #f56c6c;
        }

        [data-theme="dark"] {
            --primary: #667eea;
            --primary-light: #764ba2;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --primary-bg: rgba(102, 126, 234, 0.15);
            --primary-text: #85a5ff;
            --bg-page: #141414;
            --bg-card: #1f1f1f;
            --text-primary: #e8e8e8;
            --text-regular: #bfbfbf;
            --text-secondary: #8c8c8c;
            --text-placeholder: #595959;
            --border-base: #434343;
            --border-light: #303030;
            --border-lighter: #262626;
            --fill-light: #262626;
            --fill-lighter: #1f1f1f;
            --success: #52c41a;
            --success-light: rgba(82, 196, 26, 0.15);
            --success-border: rgba(82, 196, 26, 0.3);
            --warning: #faad14;
            --warning-light: rgba(250, 173, 20, 0.15);
            --danger: #ff4d4f;
            --danger-light: rgba(255, 77, 79, 0.15);
            --danger-border: rgba(255, 77, 79, 0.3);
            --shadow-base: 0 2px 12px rgba(0,0,0,0.3);
            --shadow-hover: 0 4px 20px rgba(0,0,0,0.5);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            transition: background 0.3s, color 0.3s;
        }
        .app-layout {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background: var(--bg-card);
            border-right: 1px solid var(--border-light);
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow-y: auto;
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
        }
        .sidebar-logo {
            padding: 8px 12px 16px;
            border-bottom: 1px solid var(--border-lighter);
            margin-bottom: 4px;
        }
        .sidebar-logo h2 {
            font-size: 18px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.3;
        }
        .sidebar-logo p {
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 4px;
        }
        .menu-group {
            background: var(--bg-card);
            border-radius: 10px;
            border: 1px solid var(--border-lighter);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .menu-group:hover {
            border-color: var(--border-base);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .menu-group-title {
            padding: 10px 14px 8px;
            font-size: 11px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: var(--fill-lighter);
            border-bottom: 1px solid var(--border-lighter);
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            cursor: pointer;
            border-left: 3px solid transparent;
            border-bottom: none;
            transition: all 0.2s ease;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-regular);
            white-space: nowrap;
            position: relative;
        }
        .nav-item:last-child {
            border-bottom: none;
        }
        .nav-item + .nav-item {
            border-top: 1px solid var(--border-lighter);
        }
        .nav-item:hover {
            color: var(--primary);
            background: var(--primary-bg);
            border-left-color: var(--primary);
        }
        .nav-item.active {
            color: var(--primary);
            border-left-color: var(--primary);
            border-bottom: none;
            font-weight: 600;
            background: var(--primary-bg);
        }
        .nav-item.active::before {
            display: none;
        }
        .nav-item .menu-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-item .menu-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-item .menu-badge {
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 1px 6px;
            border-radius: 10px;
            font-weight: 600;
        }
        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid var(--border-lighter);
        }
        .sidebar-version {
            text-align: center;
            font-size: 11px;
            color: var(--text-secondary);
        }
        .main-content {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: var(--primary-gradient);
            color: white;
            padding: 24px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }
        .header-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 16px;
        }
        .header-left {
            flex: 1;
        }
        .header h1 { 
            font-size: 26px; 
            font-weight: 700; 
            letter-spacing: -0.5px;
        }
        .header p { 
            opacity: 0.9; 
            margin-top: 6px; 
            font-size: 14px; 
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .theme-switcher {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .theme-label {
            font-size: 13px;
            opacity: 0.9;
        }
        .theme-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid rgba(255,255,255,0.3);
            transition: all 0.2s;
            position: relative;
        }
        .theme-dot:hover { transform: scale(1.15); border-color: white; }
        .theme-dot.active {
            border-color: white;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
        }
        .theme-dot.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .theme-dot[data-theme-name="default"] { background: linear-gradient(135deg, #667eea, #764ba2); }
        .theme-dot[data-theme-name="gold"] { background: linear-gradient(135deg, #9f6d1d, #d4a017); }
        .theme-dot[data-theme-name="green"] { background: linear-gradient(135deg, #217e25, #52c41a); }
        .theme-dot[data-theme-name="blue"] { background: linear-gradient(135deg, #171be1, #409eff); }
        .theme-dot[data-theme-name="cyan"] { background: linear-gradient(135deg, #03626c, #13c2c2); }
        .theme-dot[data-theme-name="red"] { background: linear-gradient(135deg, #c41d1d, #f56c6c); }
        .theme-dot[data-theme-name="dark"] { background: linear-gradient(135deg, #1f1f1f, #434343); }
        .container { padding: 30px; }
        .page { display: none; }
        .page.active { display: block; animation: fadeInPage 0.3s ease; }
        @keyframes fadeInPage {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-base);
            transition: all 0.3s ease;
            border: 1px solid var(--border-lighter);
        }
        .card:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-1px);
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title::before {
            content: '';
            width: 4px;
            height: 18px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }
        .input-group {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .input-group input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--border-base);
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s;
            background: var(--bg-card);
            color: var(--text-primary);
        }
        .input-group input:focus { border-color: var(--primary); }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn:hover::before {
            left: 100%;
        }
        .btn-sm {
            padding: 6px 14px;
            font-size: 12px;
        }
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-primary:active { transform: translateY(-1px); }
        .btn-primary:disabled { 
            opacity: 0.6; 
            cursor: not-allowed; 
            transform: none; 
            box-shadow: none;
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-regular);
            border: 1px solid var(--border-base);
        }
        .btn-secondary:hover { 
            border-color: var(--primary); 
            color: var(--primary);
            transform: translateY(-1px);
        }
        .btn-success { 
            background: var(--success); 
            color: white; 
            box-shadow: 0 4px 12px rgba(103, 194, 58, 0.3);
        }
        .btn-success:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(103, 194, 58, 0.4);
        }
        .btn-danger { 
            background: var(--danger); 
            color: white; 
            box-shadow: 0 4px 12px rgba(245, 108, 108, 0.3);
        }
        .btn-danger:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(245, 108, 108, 0.4);
        }
        .btn-warning { 
            background: var(--warning); 
            color: white; 
            box-shadow: 0 4px 12px rgba(230, 162, 60, 0.3);
        }
        .btn-warning:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(230, 162, 60, 0.4);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-base);
            transition: all 0.3s ease;
            border: 1px solid var(--border-lighter);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        .stat-value.warning { 
            color: var(--warning); 
            -webkit-text-fill-color: var(--warning); 
            background: none; 
        }
        .stat-value.danger { 
            color: var(--danger); 
            -webkit-text-fill-color: var(--danger); 
            background: none; 
        }
        .stat-value.success { 
            color: var(--success); 
            -webkit-text-fill-color: var(--success); 
            background: none; 
        }
        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: var(--text-secondary);
        }
        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid var(--primary);
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 10px;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .segment-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid var(--border-lighter);
            border-radius: 6px;
        }
        .segment-item {
            padding: 10px 14px;
            border-bottom: 1px solid var(--border-lighter);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .segment-item:last-child { border-bottom: none; }
        .segment-item.ad {
            background: var(--danger-light);
            border-left: 3px solid var(--danger);
        }
        .segment-name { font-family: monospace; color: var(--text-primary); }
        .segment-duration { color: var(--text-secondary); }
        .tag {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            margin-left: 6px;
        }
        .tag-red { background: var(--danger-light); color: var(--danger); }
        .tag-blue { background: var(--primary-bg); color: var(--primary-text); }
        .tag-green { background: var(--success-light); color: var(--success); }
        .tag-orange { background: var(--warning-light); color: var(--warning); }
        .tag-gray { background: linear-gradient(135deg, #909399, #c0c4cc); color: white; }
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
        }
        .jump-item {
            padding: 12px;
            background: var(--warning-light);
            border-radius: 6px;
            margin-bottom: 8px;
            font-size: 13px;
            color: var(--text-primary);
        }
        .jump-item .jump-arrow { color: var(--warning); font-weight: bold; }
        .rules-table {
            width: 100%;
            border-collapse: collapse;
        }
        .rules-table th, .rules-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border-lighter);
            font-size: 14px;
        }
        .rules-table th {
            background: var(--fill-light);
            color: var(--text-regular);
            font-weight: 500;
        }
        .rules-table tr:hover { background: var(--fill-light); }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-regular);
        }
        .form-group input[type="checkbox"] + span,
        .form-group label input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }
        .form-group label:has(input[type="checkbox"]) {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 0;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border-base);
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: var(--bg-card);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-bg);
        }
        .form-group textarea { min-height: 100px; font-family: monospace; }
        .form-group input:disabled, .form-group textarea:disabled {
            background: var(--fill-lighter);
            color: var(--text-secondary);
            cursor: not-allowed;
        }
        .rule-section {
            border: 1px solid var(--border-lighter);
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .fast-mode-banner {
            background: var(--success-light);
            border: 1px solid var(--success-border);
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .fast-mode-banner .icon {
            font-size: 24px;
        }
        .fast-mode-banner .content {
            flex: 1;
        }
        .fast-mode-banner .title {
            font-weight: 600;
            color: var(--success);
            font-size: 15px;
            margin-bottom: 4px;
        }
        .fast-mode-banner .desc {
            color: var(--text-regular);
            font-size: 13px;
        }
        .fast-mode-banner .domain-tag {
            display: inline-block;
            background: var(--bg-card);
            padding: 2px 10px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            color: var(--success);
            margin-left: 8px;
            border: 1px solid var(--success-border);
        }
        .rule-section-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--text-primary);
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: var(--text-placeholder);
        }
        .code-block {
            background: #282c34;
            color: #abb2bf;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 12px;
        }
        .tab-bar {
            display: flex;
            gap: 2px;
            margin-bottom: 16px;
            border-bottom: 1px solid var(--border-light);
            overflow-x: auto;
            scrollbar-width: none;
        }
        .tab-bar::-webkit-scrollbar { display: none; }
        .tab-item {
            padding: 10px 16px;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            font-size: 13px;
            color: var(--text-regular);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tab-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            border-radius: 6px;
            color: white;
            font-size: 14px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        .toast.success { background: var(--success); }
        .toast.error { background: var(--danger); }
        .toast.info { background: var(--primary); }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 4px;
            color: white;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 8px;
        }
        .copy-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .copy-btn:active {
            transform: scale(0.95);
        }
        .access-item {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
        }
        .access-item code {
            cursor: pointer;
            user-select: all;
            word-break: break-all;
        }
        .access-item code:hover {
            text-decoration: underline;
        }
        .bar-chart { display: flex; align-items: flex-end; gap: 2px; height: 120px; padding: 10px 0; }
        .bar {
            flex: 1;
            background: var(--primary-gradient);
            border-radius: 2px 2px 0 0;
            min-height: 2px;
            transition: all 0.3s;
        }
        .bar:hover { opacity: 0.8; }
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            font-size: 12px;
            color: var(--text-regular);
        }
        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            margin-right: 6px;
        }

        @media (max-width: 768px) {
            .app-layout { flex-direction: column; }
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 12px;
                gap: 10px;
                flex-direction: row;
                overflow-x: auto;
            }
            .sidebar-logo { display: none; }
            .sidebar-footer { display: none; }
            .menu-group {
                flex-shrink: 0;
                min-width: 140px;
            }
            .menu-group-title { display: none; }
            .header { padding: 16px 20px; }
            .header h1 { font-size: 18px; }
            .header p { font-size: 12px; }
            .header-actions { margin-top: 10px; }
            .theme-label { display: none; }
            .theme-dot { width: 20px; height: 20px; }
            .nav-item { padding: 10px 12px; font-size: 12px; }
            .container { padding: 16px; }
            .card { padding: 16px; margin-bottom: 16px; }
            .card-title { font-size: 15px; margin-bottom: 12px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .stat-card { padding: 14px; }
            .stat-value { font-size: 22px; }
            .stat-label { font-size: 12px; }
            .input-group { flex-direction: column; }
            .input-group .btn { width: 100%; }
            .rules-table { font-size: 12px; }
            .rules-table th, .rules-table td { padding: 8px 6px; font-size: 12px; }
            .rules-table th:nth-child(n+4), .rules-table td:nth-child(n+4) { display: none; }
            .toast { left: 20px; right: 20px; text-align: center; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .card { padding: 12px; }
            .form-group { margin-bottom: 12px; }
            .form-group label { font-size: 13px; }
        }

        /* ============================================
           v3.0 全新设计语言 - PlainAdmin 风格
           ============================================ */
        
        :root {
            --v3-primary: #3b82f6;
            --v3-primary-dark: #2563eb;
            --v3-primary-light: #dbeafe;
            --v3-primary-gradient: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            --v3-success: #10b981;
            --v3-success-light: #d1fae5;
            --v3-warning: #f59e0b;
            --v3-warning-light: #fef3c7;
            --v3-danger: #ef4444;
            --v3-danger-light: #fee2e2;
            --v3-info: #06b6d4;
            --v3-info-light: #cffafe;
            --v3-purple: #8b5cf6;
            --v3-purple-light: #ede9fe;
            --v3-pink: #ec4899;
            --v3-pink-light: #fce7f3;
            --v3-bg-page: #f8fafc;
            --v3-bg-card: #ffffff;
            --v3-bg-hover: #f1f5f9;
            --v3-border: #e2e8f0;
            --v3-border-light: #f1f5f9;
            --v3-text-primary: #0f172a;
            --v3-text-secondary: #475569;
            --v3-text-muted: #94a3b8;
            --v3-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --v3-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --v3-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --v3-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --v3-shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            --v3-radius-sm: 6px;
            --v3-radius: 10px;
            --v3-radius-md: 12px;
            --v3-radius-lg: 16px;
            --v3-radius-xl: 20px;
            --v3-transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-theme="dark"] {
            --v3-bg-page: #0f172a;
            --v3-bg-card: #1e293b;
            --v3-bg-hover: #334155;
            --v3-border: #334155;
            --v3-border-light: #1e293b;
            --v3-text-primary: #f1f5f9;
            --v3-text-secondary: #94a3b8;
            --v3-text-muted: #64748b;
        }

        body { background: var(--v3-bg-page); }

        /* ===== 背景图 v3 ===== */
        body.bg-image-mode {
            background-image: none;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            transition: background-image 0.8s ease-in-out;
        }
        body.bg-image-mode::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg,
                rgba(59, 130, 246, 0.15) 0%,
                rgba(139, 92, 246, 0.15) 50%,
                rgba(236, 72, 153, 0.15) 100%);
            background-size: 300% 300%;
            animation: bgGradientShift 15s ease infinite;
            pointer-events: none;
            z-index: 0;
        }
        body.bg-image-mode::after {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(248, 250, 252, 0.75);
            pointer-events: none;
            z-index: 0;
            backdrop-filter: blur(0px);
        }
        [data-theme="dark"].bg-image-mode::after {
            background: rgba(15, 23, 42, 0.8);
        }
        @keyframes bgGradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        body.bg-image-mode .app-layout,
        body.bg-image-mode .container {
            position: relative;
            z-index: 1;
        }
        body.bg-image-mode .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode .sidebar {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .sidebar {
            background: rgba(30, 41, 59, 0.9);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode .header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .header {
            background: rgba(30, 41, 59, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode .stat-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .stat-card {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode .dashboard-card,
        body.bg-image-mode .quick-action-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .dashboard-card,
        [data-theme="dark"].bg-image-mode .quick-action-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode .mobile-bottom-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid rgba(255, 255, 255, 0.5);
        }
        [data-theme="dark"].bg-image-mode .mobile-bottom-nav {
            background: rgba(30, 41, 59, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.bg-image-mode input,
        body.bg-image-mode select,
        body.bg-image-mode textarea {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        [data-theme="dark"].bg-image-mode input,
        [data-theme="dark"].bg-image-mode select,
        [data-theme="dark"].bg-image-mode textarea {
            background: rgba(30, 41, 59, 0.9);
        }

        /* ===== 背景图切换按钮 ===== */
        .bg-toggle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: var(--v3-bg-hover);
            color: var(--v3-text-secondary);
            transition: all var(--v3-transition);
            margin-left: 8px;
        }
        .bg-toggle-btn:hover {
            background: var(--v3-primary-light);
            color: var(--v3-primary);
            transform: scale(1.1);
        }
        .bg-toggle-btn.active {
            background: var(--v3-primary-gradient);
            color: white;
        }
        .bg-change-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            background: var(--v3-bg-hover);
            color: var(--v3-text-secondary);
            transition: all var(--v3-transition);
            margin-left: 8px;
        }
        .bg-change-btn:hover {
            background: var(--v3-primary-light);
            color: var(--v3-primary);
            transform: rotate(180deg);
        }

        /* ===== 侧边栏 v3 ===== */
        .sidebar {
            width: 250px;
            background: var(--v3-bg-card);
            border-right: 1px solid var(--v3-border);
            padding: 16px 12px;
            box-shadow: none;
        }
        .sidebar-logo {
            padding: 8px 12px 20px;
            border-bottom: 1px solid var(--v3-border-light);
            margin-bottom: 12px;
        }
        .sidebar-logo h2 {
            font-size: 17px;
            font-weight: 700;
            background: var(--v3-primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .sidebar-logo p {
            font-size: 11px;
            color: var(--v3-text-muted);
            margin-top: 2px;
        }
        .menu-group {
            background: transparent;
            border-radius: 0;
            border: none;
            margin-bottom: 8px;
        }
        .menu-group:hover {
            border-color: transparent;
            box-shadow: none;
        }
        .menu-group-title {
            padding: 8px 12px 6px;
            font-size: 10px;
            font-weight: 600;
            color: var(--v3-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: transparent;
            border-bottom: none;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            margin: 2px 0;
            cursor: pointer;
            border-left: 3px solid transparent;
            border-radius: var(--v3-radius);
            border-bottom: none;
            transition: all var(--v3-transition);
            font-size: 13px;
            font-weight: 500;
            color: var(--v3-text-secondary);
            white-space: nowrap;
            position: relative;
        }
        .nav-item + .nav-item {
            border-top: none;
        }
        .nav-item:hover {
            color: var(--v3-primary);
            background: var(--v3-primary-light);
            border-left-color: transparent;
        }
        .nav-item.active {
            color: var(--v3-primary);
            border-left-color: var(--v3-primary);
            font-weight: 600;
            background: var(--v3-primary-light);
            box-shadow: var(--v3-shadow-sm);
        }
        .nav-item .menu-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-item .menu-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nav-item .menu-badge {
            background: var(--v3-danger);
            color: white;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: 700;
        }

        /* ===== 顶部栏 v3 ===== */
        .header {
            background: var(--v3-bg-card);
            color: var(--v3-text-primary);
            padding: 16px 32px;
            box-shadow: var(--v3-shadow-sm);
            border-bottom: 1px solid var(--v3-border);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .header::before { display: none; }
        .header h1 { 
            font-size: 20px; 
            font-weight: 700; 
            color: var(--v3-text-primary);
            letter-spacing: -0.3px;
        }
        .header p { 
            color: var(--v3-text-muted); 
            margin-top: 2px; 
            font-size: 13px; 
        }
        .theme-switcher {
            background: var(--v3-bg-hover);
            padding: 6px 10px;
            border-radius: var(--v3-radius);
        }
        .theme-label {
            font-size: 12px;
            color: var(--v3-text-secondary);
        }
        .theme-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all var(--v3-transition);
            position: relative;
        }
        .theme-dot:hover { transform: scale(1.1); }
        .theme-dot.active {
            border-color: var(--v3-primary);
            box-shadow: 0 0 0 3px var(--v3-primary-light);
        }

        /* ===== 内容区 v3 ===== */
        .container { 
            padding: 24px 32px;
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }
        .page { animation: fadeInUp 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== 卡片 v3 ===== */
        .card {
            background: var(--v3-bg-card);
            border-radius: var(--v3-radius-lg);
            padding: 24px;
            border: 1px solid var(--v3-border-light);
            box-shadow: var(--v3-shadow-sm);
            transition: all var(--v3-transition);
        }
        .card:hover {
            box-shadow: var(--v3-shadow-md);
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--v3-text-primary);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--v3-border-light);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-title::before {
            content: '';
            width: 4px;
            height: 16px;
            background: var(--v3-primary-gradient);
            border-radius: 2px;
        }

        /* ===== 数据统计卡片 v3 ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--v3-bg-card);
            border-radius: var(--v3-radius-lg);
            padding: 20px;
            border: 1px solid var(--v3-border-light);
            box-shadow: var(--v3-shadow-sm);
            transition: all var(--v3-transition);
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--v3-shadow-lg);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--v3-primary-gradient);
            opacity: 0.8;
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--v3-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
            background: var(--v3-primary-light);
        }
        .stat-card.success::before { background: var(--v3-success); }
        .stat-card.success .stat-icon { background: var(--v3-success-light); }
        .stat-card.warning::before { background: var(--v3-warning); }
        .stat-card.warning .stat-icon { background: var(--v3-warning-light); }
        .stat-card.danger::before { background: var(--v3-danger); }
        .stat-card.danger .stat-icon { background: var(--v3-danger-light); }
        .stat-card.purple::before { background: var(--v3-purple); }
        .stat-card.purple .stat-icon { background: var(--v3-purple-light); }
        .stat-card.info::before { background: var(--v3-info); }
        .stat-card.info .stat-icon { background: var(--v3-info-light); }
        .stat-card.pink::before { background: var(--v3-pink); }
        .stat-card.pink .stat-icon { background: var(--v3-pink-light); }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--v3-text-primary);
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 13px;
            color: var(--v3-text-muted);
            font-weight: 500;
        }
        .stat-trend {
            font-size: 12px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .stat-trend.up { color: var(--v3-success); }
        .stat-trend.down { color: var(--v3-danger); }

        /* ===== 按钮 v3 ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: var(--v3-radius);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all var(--v3-transition);
            text-decoration: none;
            white-space: nowrap;
            height: 40px;
        }
        .btn-primary {
            background: var(--v3-primary-gradient);
            color: white;
            border: none;
            box-shadow: 0 1px 3px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .btn-secondary {
            background: var(--v3-bg-card);
            color: var(--v3-text-secondary);
            border: 1px solid var(--v3-border);
        }
        .btn-secondary:hover {
            color: var(--v3-primary);
            border-color: var(--v3-primary);
            background: var(--v3-primary-light);
        }
        .btn-success {
            background: var(--v3-success);
            color: white;
            border: none;
        }
        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        .btn-danger {
            background: var(--v3-danger);
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
            height: 32px;
        }
        .btn-lg {
            padding: 12px 24px;
            font-size: 14px;
            height: 44px;
        }
        .btn-block {
            width: 100%;
        }

        /* ===== 输入框 v3 ===== */
        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        input[type="text"],
        input[type="url"],
        input[type="number"],
        input[type="password"],
        textarea,
        select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--v3-border);
            border-radius: var(--v3-radius);
            font-size: 13px;
            color: var(--v3-text-primary);
            background: var(--v3-bg-card);
            transition: all var(--v3-transition);
            outline: none;
            height: 40px;
        }
        textarea {
            height: auto;
            min-height: 100px;
            resize: vertical;
        }
        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--v3-primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        input::placeholder,
        textarea::placeholder {
            color: var(--v3-text-muted);
        }

        /* ===== 标签/徽章 v3 ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 6px;
            line-height: 1.4;
        }
        .badge-primary {
            background: var(--v3-primary-light);
            color: var(--v3-primary);
        }
        .badge-success {
            background: var(--v3-success-light);
            color: var(--v3-success);
        }
        .badge-warning {
            background: var(--v3-warning-light);
            color: #b45309;
        }
        .badge-danger {
            background: var(--v3-danger-light);
            color: var(--v3-danger);
        }
        .badge-info {
            background: var(--v3-info-light);
            color: var(--v3-info);
        }
        .badge-purple {
            background: var(--v3-purple-light);
            color: var(--v3-purple);
        }

        /* ===== 表格 v3 ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .data-table th {
            background: var(--v3-bg-hover);
            color: var(--v3-text-secondary);
            font-weight: 600;
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid var(--v3-border);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .data-table td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--v3-border-light);
            color: var(--v3-text-primary);
        }
        .data-table tr:hover td {
            background: var(--v3-bg-hover);
        }

        /* ===== 页面标题区 v3 ===== */
        .page-header {
            margin-bottom: 24px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--v3-text-primary);
            margin-bottom: 6px;
        }
        .page-subtitle {
            font-size: 14px;
            color: var(--v3-text-muted);
        }
        .page-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* ===== 仪表盘专用 ===== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }
        .dashboard-card {
            background: var(--v3-bg-card);
            border-radius: var(--v3-radius-lg);
            padding: 20px;
            border: 1px solid var(--v3-border-light);
            box-shadow: var(--v3-shadow-sm);
        }
        .dashboard-card-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--v3-text-primary);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .quick-action-card {
            padding: 16px;
            border-radius: var(--v3-radius);
            background: var(--v3-bg-hover);
            cursor: pointer;
            transition: all var(--v3-transition);
            border: 1px solid transparent;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .quick-action-card:hover {
            background: var(--v3-primary-light);
            border-color: var(--v3-primary);
            transform: translateY(-2px);
            box-shadow: var(--v3-shadow-md);
        }
        .quick-action-icon {
            font-size: 24px;
        }
        .quick-action-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--v3-text-primary);
        }
        .quick-action-desc {
            font-size: 11px;
            color: var(--v3-text-muted);
            line-height: 1.4;
        }

        /* ===== 最近记录列表 ===== */
        .recent-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .recent-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--v3-border-light);
            cursor: pointer;
            transition: all var(--v3-transition);
        }
        .recent-item:last-child { border-bottom: none; }
        .recent-item:hover {
            padding-left: 6px;
        }
        .recent-item-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--v3-radius);
            background: var(--v3-primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .recent-item-content {
            flex: 1;
            min-width: 0;
        }
        .recent-item-title {
            font-size: 13px;
            font-weight: 500;
            color: var(--v3-text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .recent-item-meta {
            font-size: 11px;
            color: var(--v3-text-muted);
            margin-top: 2px;
            display: flex;
            gap: 10px;
        }

        /* ===== 移动端底部导航 ===== */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--v3-bg-card);
            border-top: 1px solid var(--v3-border);
            padding: 6px 0;
            padding-bottom: max(6px, env(safe-area-inset-bottom));
            z-index: 100;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }
        .mobile-nav-items {
            display: flex;
            justify-content: space-around;
        }
        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            padding: 6px 12px;
            cursor: pointer;
            color: var(--v3-text-muted);
            font-size: 10px;
            font-weight: 500;
            transition: all var(--v3-transition);
            flex: 1;
            min-width: 0;
        }
        .mobile-nav-item .nav-icon {
            font-size: 20px;
        }
        .mobile-nav-item.active {
            color: var(--v3-primary);
        }
        .mobile-nav-item.active .nav-icon {
            transform: translateY(-2px);
        }

        /* ===== 移动端菜单按钮 ===== */
        .mobile-menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border-radius: var(--v3-radius);
            background: var(--v3-bg-hover);
            border: none;
            cursor: pointer;
            font-size: 18px;
            align-items: center;
            justify-content: center;
            transition: all var(--v3-transition);
        }
        .mobile-menu-toggle:hover {
            background: var(--v3-primary-light);
        }

        /* ===== 侧边栏遮罩 ===== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 90;
            opacity: 0;
            transition: opacity var(--v3-transition);
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ===== 响应式：平板 ===== */
        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .container { padding: 20px; }
            .header { padding: 14px 20px; }
        }

        /* ===== 响应式：手机 ===== */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                top: 0;
                bottom: 0;
                z-index: 95;
                width: 260px;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: var(--v3-shadow-xl);
            }
            .sidebar.show {
                left: 0;
            }
            .mobile-menu-toggle {
                display: flex;
            }
            .header h1 { font-size: 16px; }
            .header p { font-size: 12px; }
            .theme-label { display: none; }
            .container { 
                padding: 16px;
                padding-bottom: 80px;
            }
            .card { 
                padding: 16px; 
                margin-bottom: 16px;
                border-radius: var(--v3-radius-md);
            }
            .card-title { 
                font-size: 15px; 
                margin-bottom: 14px;
                padding-bottom: 10px;
            }
            .stats-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 12px; 
            }
            .stat-card { 
                padding: 16px;
                border-radius: var(--v3-radius-md);
            }
            .stat-value { font-size: 22px; }
            .stat-label { font-size: 12px; }
            .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 16px;
                margin-bottom: 8px;
            }
            .input-group { flex-direction: column; }
            .input-group .btn { width: 100%; }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .mobile-bottom-nav {
                display: block;
            }
            
            .page-title {
                font-size: 20px;
            }
            
            .dashboard-card {
                padding: 16px;
                border-radius: var(--v3-radius-md);
            }
        }

        @media (max-width: 480px) {
            .stats-grid { 
                grid-template-columns: 1fr 1fr; 
                gap: 10px;
            }
            .stat-value { font-size: 18px; }
            .card { 
                padding: 14px; 
            }
            .btn {
                height: 44px;
                font-size: 14px;
            }
            input[type="text"],
            input[type="url"],
            input[type="number"],
            select {
                height: 44px;
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <div class="app-layout">
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <h2>M3U8 广告分析</h2>
                <p>智能去广告管理后台</p>
            </div>
            <div id="sidebarMenu"></div>
            <div class="sidebar-footer">
                <div class="sidebar-version" id="sidebarVersion">加载中...</div>
            </div>
        </aside>

        <main class="main-content">
            <div class="header">
                <div class="header-content">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()" title="菜单">☰</button>
                    <div class="header-left">
                        <h1>M3U8 广告分析与规则管理后台</h1>
                        <p>靶机测试工具 - 分析视频广告特征，管理域名去广告规则</p>
                    </div>
                    <div class="header-actions">
                        <div class="theme-switcher">
                            <span class="theme-label">主题:</span>
                            <div class="theme-dot active" data-theme-name="default" title="默认紫" onclick="switchTheme('default')"></div>
                            <div class="theme-dot" data-theme-name="gold" title="金色" onclick="switchTheme('gold')"></div>
                            <div class="theme-dot" data-theme-name="green" title="绿色" onclick="switchTheme('green')"></div>
                            <div class="theme-dot" data-theme-name="blue" title="蓝色" onclick="switchTheme('blue')"></div>
                            <div class="theme-dot" data-theme-name="cyan" title="青色" onclick="switchTheme('cyan')"></div>
                            <div class="theme-dot" data-theme-name="red" title="红色" onclick="switchTheme('red')"></div>
                            <div class="theme-dot" data-theme-name="dark" title="深色" onclick="switchTheme('dark')"></div>
                        </div>
                        <button class="bg-toggle-btn" id="bgToggleBtn" onclick="toggleBgImage()" title="背景图">🖼️</button>
                        <button class="bg-change-btn" id="bgChangeBtn" onclick="changeBgImage()" title="换一张" style="display:none">🔄</button>
                    </div>
                </div>
            </div>

    <div id="accessPreview" style="background:var(--primary-gradient);color:white;padding:20px 30px;font-size:13px">
        <div style="display:flex;flex-wrap:wrap;gap:20px">
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">后台管理</div>
                <div class="access-item">
                    <code id="preview-admin" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-admin').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">分析接口</div>
                <div class="access-item">
                    <code id="preview-api" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-api').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">网页播放器已去插播去广告接口</div>
                <div class="access-item">
                    <code id="preview-parse" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-parse').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">信息接口(JSON)</div>
                <div class="access-item">
                    <code id="preview-player" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-player').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">官替API接口</div>
                <div class="access-item">
                    <code id="preview-official-replace" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-official-replace').textContent)">复制</button>
                </div>
            </div>
            <div style="flex:1;min-width:200px">
                <div style="opacity:0.8;font-size:11px;margin-bottom:4px">沫兮API接口</div>
                <div class="access-item">
                    <code id="preview-moxi" onclick="copyText(this.textContent)" title="点击复制"></code>
                    <button class="copy-btn" onclick="copyText(document.getElementById('preview-moxi').textContent)">复制</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page active" id="page-dashboard">
            <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px">
                <div>
                    <h2 class="page-title">📊 数据概览</h2>
                    <p class="page-subtitle">实时掌握系统运行状态与分析统计</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-primary" onclick="navigateTo('ai_skip')">🚀 AI去广告</button>
                    <button class="btn btn-secondary" onclick="navigateTo('batch')">📦 批量分析</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card success">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-value" id="dashTotalAnalyze">0</div>
                    <div class="stat-label">总分析次数</div>
                    <div class="stat-trend up">↑ 今日 +0</div>
                </div>
                <div class="stat-card danger">
                    <div class="stat-icon">🚫</div>
                    <div class="stat-value" id="dashAdRemoved">0</div>
                    <div class="stat-label">已去除广告片段</div>
                    <div class="stat-trend up">↑ 累计</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon">🌐</div>
                    <div class="stat-value" id="dashDomains">0</div>
                    <div class="stat-label">已分析域名</div>
                    <div class="stat-trend up">↑ 新增规则</div>
                </div>
                <div class="stat-card info">
                    <div class="stat-icon">⏱️</div>
                    <div class="stat-value" id="dashAvgTime">0s</div>
                    <div class="stat-label">平均分析耗时</div>
                    <div class="stat-trend down">↓ 极速模式</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon">📝</div>
                    <div class="stat-value" id="dashRules">0</div>
                    <div class="stat-label">广告规则数</div>
                    <div class="stat-trend up">↑ 持续增长</div>
                </div>
                <div class="stat-card pink">
                    <div class="stat-icon">🤖</div>
                    <div class="stat-value" id="dashMd5">0</div>
                    <div class="stat-label">AI去广告</div>
                    <div class="stat-trend up">↑ 智能处理</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <div class="dashboard-card-title">
                        <span>⚡ 快捷操作</span>
                    </div>
                    <div class="quick-actions">
                        <div class="quick-action-card" onclick="navigateTo('ai_skip')">
                            <div class="quick-action-icon">🤖</div>
                            <div class="quick-action-title">AI自动去广告</div>
                            <div class="quick-action-desc">智能识别并去除视频广告</div>
                        </div>
                        <div class="quick-action-card" onclick="navigateTo('analyze')">
                            <div class="quick-action-icon">🎯</div>
                            <div class="quick-action-title">视频分析</div>
                            <div class="quick-action-desc">详细分析视频广告特征</div>
                        </div>
                        <div class="quick-action-card" onclick="navigateTo('batch')">
                            <div class="quick-action-icon">📦</div>
                            <div class="quick-action-title">批量分析</div>
                            <div class="quick-action-desc">批量导入URL快速分析</div>
                        </div>
                        <div class="quick-action-card" onclick="navigateTo('rules')">
                            <div class="quick-action-icon">📋</div>
                            <div class="quick-action-title">规则管理</div>
                            <div class="quick-action-desc">管理域名去广告规则</div>
                        </div>
                        <div class="quick-action-card" onclick="navigateTo('ai_insert')">
                            <div class="quick-action-icon">📺</div>
                            <div class="quick-action-title">插播识别</div>
                            <div class="quick-action-desc">识别视频中间插播内容</div>
                        </div>
                        <div class="quick-action-card" onclick="navigateTo('sites')">
                            <div class="quick-action-icon">🌐</div>
                            <div class="quick-action-title">资源站管理</div>
                            <div class="quick-action-desc">管理资源站配置</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="dashboard-card-title">
                        <span>🕐 最近分析</span>
                        <span style="font-size:12px;color:var(--v3-text-muted);cursor:pointer" onclick="navigateTo('history')">查看全部 →</span>
                    </div>
                    <ul class="recent-list" id="dashRecentList">
                        <li class="recent-item" style="justify-content:center;color:var(--v3-text-muted);padding:20px 0">
                            暂无分析记录，快去分析一个视频吧！
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-title">🔥 热门域名排行</div>
                <div id="dashTopDomains">
                    <div style="text-align:center;color:var(--v3-text-muted);padding:30px">
                        暂无数据，分析更多视频后显示热门域名将在这里展示
                    </div>
                </div>
            </div>
        </div>

        <div class="page" id="page-history">
            <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px">
                <div>
                    <h2 class="page-title">📜 分析历史</h2>
                    <p class="page-subtitle">查看所有分析记录，可搜索和重新分析</p>
                </div>
                <div class="page-actions">
                    <input type="text" id="historySearch" placeholder="搜索URL或域名..." style="width:200px" oninput="filterHistory()">
                    <button class="btn btn-secondary" onclick="clearHistory()">🗑️ 清空历史</button>
                </div>
            </div>

            <div class="card">
                <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                    <div style="font-size:13px;color:var(--v3-text-muted)">
                        共 <span id="historyCount">0</span> 条记录
                    </div>
                    <div style="display:flex;gap:8px">
                        <select id="historyFilter" onchange="filterHistory()" style="width:140px">
                            <option value="all">全部类型</option>
                            <option value="analyze">视频分析</option>
                            <option value="ai_skip">AI去广告</option>
                            <option value="md5">MD5分析</option>
                        </select>
                    </div>
                </div>
                <div id="historyList">
                    <div style="text-align:center;color:var(--v3-text-muted);padding:40px">
                        <div style="font-size:40px;margin-bottom:12px">📭</div>
                        <div>暂无分析记录</div>
                        <div style="font-size:12px;margin-top:4px">分析视频后，记录将显示在这里</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page" id="page-batch">
            <div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px">
                <div>
                    <h2 class="page-title">📦 批量分析</h2>
                    <p class="page-subtitle">批量导入视频链接，一键批量分析去广告</p>
                </div>
                <div class="page-actions">
                    <button class="btn btn-secondary" onclick="loadBatchDemo()">📋 示例数据</button>
                    <button class="btn btn-primary" onclick="startBatchAnalyze()">🚀 开始批量分析</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">📝 输入视频链接</div>
                <div style="margin-bottom:12px;font-size:12px;color:var(--v3-text-muted)">
                    每行一个链接，支持批量粘贴，最多支持 20 个链接同时分析
                </div>
                <textarea id="batchUrls" placeholder="每行一个M3U8链接，例如：&#10;https://example.com/video1/index.m3u8&#10;https://example.com/video2/index.m3u8" style="min-height:160px;font-family:monospace;font-size:12px"></textarea>
                <div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
                    <div style="font-size:12px;color:var(--v3-text-muted)">
                        已输入 <span id="batchUrlCount">0</span> 个链接
                    </div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--v3-text-secondary)">
                            <input type="checkbox" id="batchFastMode" checked>
                            极速模式
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--v3-text-secondary)">
                            <input type="checkbox" id="batchAiMode" checked>
                            AI去广告
                        </label>
                    </div>
                </div>
            </div>

            <div class="card" id="batchResultCard" style="display:none">
                <div class="card-title">📊 分析结果</div>
                <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
                    <div class="stat-card">
                        <div class="stat-value" id="batchTotal">0</div>
                        <div class="stat-label">总链接数</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-value" id="batchSuccess">0</div>
                        <div class="stat-label">成功</div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-value" id="batchFailed">0</div>
                        <div class="stat-label">失败</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-value" id="batchProgress">0%</div>
                        <div class="stat-label">进度</div>
                    </div>
                </div>
                <div id="batchResultList" style="margin-top:16px"></div>
            </div>
        </div>

        <div class="page" id="page-analyze">
            <div class="card">
                <div class="card-title">视频广告分析</div>
                <div class="input-group">
                    <input type="text" id="analyzeUrl" placeholder="输入 M3U8 视频链接，例如：https://example.com/video/index.m3u8"
                        value="https://v.lfthirtytwo.com/20260623/7885_1d9dba16/index.m3u8">
                    <button class="btn btn-primary" onclick="analyzeVideo()">开始分析</button>
                </div>
                <div style="font-size:12px;color:#909399">
                    提示：系统将自动检测 Master Playlist 并追踪到实际视频进行分析
                </div>
            </div>

            <div id="analyzeResult" style="display:none">
                <div id="fastModeBanner" style="display:none"></div>

                <div class="stats-grid" id="statsGrid"></div>

                <div class="detail-grid" id="detailGrid">
                    <div class="card">
                        <div class="card-title">序列号跳跃检测</div>
                        <div id="jumpList"></div>
                    </div>
                    <div class="card">
                        <div class="card-title">时长分布</div>
                        <div class="bar-chart" id="durationChart"></div>
                        <div class="legend">
                            <div class="legend-item"><div class="legend-color" style="background:linear-gradient(to top,#667eea,#764ba2)"></div>片段数量</div>
                        </div>
                        <div id="durationStats" style="margin-top:12px;font-size:13px;color:#606266"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">广告片段详情</div>
                    <div class="tab-bar">
                        <div class="tab-item active" onclick="switchSegmentTab(this, 'ad')">广告片段</div>
                        <div class="tab-item" onclick="switchSegmentTab(this, 'all')">全部片段</div>
                        <div class="tab-item" onclick="switchSegmentTab(this, 'cluster')">广告聚类</div>
                    </div>
                    <div class="segment-list" id="segmentList"></div>
                </div>

                <div class="card">
                    <div class="card-title">无广告播放链接</div>
                    <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
                        <code id="analyzeMxjxUrl" style="background:#f5f7fa;padding:8px 12px;border-radius:4px;word-break:break-all;flex:1;min-width:200px;cursor:pointer" onclick="copyText(this.textContent)" title="点击复制"></code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText(document.getElementById('analyzeMxjxUrl').textContent)">复制链接</button>
                        <button class="btn btn-sm btn-primary" onclick="window.open(document.getElementById('analyzeMxjxUrl').textContent, '_blank')">新窗口播放</button>
                        <button class="btn btn-sm btn-success" onclick="playAnalyzeVideo()">内置播放器播放</button>
                    </div>
                    <div style="margin-top:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                            <input type="checkbox" id="analyzeUseProxy" onchange="updateAnalyzeMxjxUrl()"> 使用代理播放
                        </label>
                        <label id="analyzeAutoProxyLabel" style="display:none;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                            <input type="checkbox" id="analyzeAutoProxy" checked onchange="toggleAutoProxy()"> 自动选最快
                        </label>
                        <select id="analyzeProxyServer" style="padding:4px 8px;border:1px solid #dcdfe6;border-radius:4px;font-size:12px;display:none;min-width:200px" onchange="onProxySelectChange()">
                            <option value="">选择代理服务器（按延迟排序）</option>
                        </select>
                        <button class="btn btn-sm btn-secondary" id="checkProxyBtn" onclick="checkAllProxies()" style="display:none">🔄 测速</button>
                    </div>
                    <div id="analyzePlayerContainer" style="display:none;margin-top:16px">
                        <div id="analyzeVideoPlayer" style="width:100%;height:360px;border-radius:8px;overflow:hidden;background:#000"></div>
                        <div style="margin-top:8px;font-size:12px;color:#909399" id="analyzePlayStatus"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">操作</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="generateRules()">自动生成规则</button>
                        <button class="btn btn-success" onclick="goToRules()">查看规则管理</button>
                        <button class="btn btn-secondary" id="learnBtn" onclick="learnRules()">学习并更新规则</button>
                    </div>
                    <div id="learnStatus" style="margin-top:12px;font-size:13px;color:#606266;display:none"></div>
                </div>
            </div>
        </div>

        <div class="page" id="page-rules">
            <div class="card">
                <div class="card-title">域名规则列表</div>
                <div style="margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="showAddRule()">+ 新增规则</button>
                    <button class="btn btn-secondary" onclick="refreshRules()">刷新列表</button>
                    <button class="btn btn-secondary" onclick="exportAllRules()">导出全部规则</button>
                    <button class="btn btn-secondary" onclick="document.getElementById('importFileInput').click()">导入规则</button>
                    <input type="file" id="importFileInput" accept=".json" style="display:none" onchange="importRulesFromFile(event)">
                    <button class="btn btn-danger" onclick="clearAllRules()">🗑️ 一键清理所有规则</button>
                </div>
                <div id="rulesTable"></div>
            </div>

            <div class="card" id="ruleEditor" style="display:none">
                <div class="card-title" id="ruleEditorTitle">编辑规则</div>
                <div class="form-group">
                    <label>资源名称</label>
                    <input type="text" id="ruleName" placeholder="例如：芒果TV">
                </div>
                <div class="form-group">
                    <label>域名</label>
                    <input type="text" id="ruleDomain" placeholder="例如：v.lfthirtytwo.com">
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">时长规则</div>
                    <div id="durationRules"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addDurationRule()">+ 添加时长规则</button>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">DISCONTINUITY 规则</div>
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="discontinuityEnabled"> 启用 DISCONTINUITY 检测
                    </label>
                    <p style="font-size:12px;color:#909399;margin-top:6px">检测到 #EXT-X-DISCONTINUITY 标记时，标记该片段为广告插播点</p>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">序列号跳跃规则</div>
                    <div id="sequenceJumpRules"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addSeqJumpRule()">+ 添加序列号跳跃规则</button>
                </div>

                <div class="rule-section">
                    <div class="rule-section-title">文件名模式</div>
                    <div id="filenamePatterns"></div>
                    <button class="btn btn-sm btn-secondary" onclick="addFilenamePattern()">+ 添加文件名模式</button>
                </div>

                <div class="form-group">
                    <label>备注</label>
                    <textarea id="ruleNote" placeholder="规则说明备注"></textarea>
                </div>

                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveRule()">保存规则</button>
                    <button class="btn btn-secondary" onclick="cancelRuleEdit()">取消</button>
                </div>
            </div>
        </div>

        <div class="page" id="page-sites">
            <div class="card">
                <div class="card-title">自动学习配置</div>
                <div class="stats-grid" id="autoLearnStats">
                    <div class="stat-card">
                        <div class="stat-value" id="totalSites">-</div>
                        <div class="stat-label">资源站总数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="activeSites">-</div>
                        <div class="stat-label">活跃资源站</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="lastLearnTime">-</div>
                        <div class="stat-label">上次学习时间</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="autoLearnStatus">-</div>
                        <div class="stat-label">自动学习状态</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>启用自动学习</label>
                        <select id="autoLearnEnabled">
                            <option value="true">启用</option>
                            <option value="false">禁用</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>更新间隔 (天)</label>
                        <input type="number" id="intervalDays" min="1" max="30" value="3">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>每站视频数</label>
                        <input type="number" id="videosPerSite" min="1" max="20" value="5">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>每次最大站点数</label>
                        <input type="number" id="maxSitesPerRun" min="1" max="20" value="5">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>最小片段数</label>
                        <input type="number" id="minSegments" min="10" max="500" value="50">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>最大广告占比 (%)</label>
                        <input type="number" id="maxAdPercentage" min="10" max="100" value="90">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>多线程加速</label>
                        <select id="autoLearnMultiThread">
                            <option value="true">启用</option>
                            <option value="false">禁用</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>并发数</label>
                        <select id="autoLearnConcurrency">
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="5" selected>5</option>
                            <option value="8">8</option>
                            <option value="10">10</option>
                        </select>
                    </div>
                </div>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="saveAutoLearnConfig()">保存配置</button>
                    <button class="btn btn-success" onclick="runAutoLearn()">立即执行学习</button>
                    <button class="btn btn-secondary" onclick="refreshSites()">刷新</button>
                </div>
                <div id="autoLearnResult" style="margin-top:16px;display:none"></div>
            </div>

            <div class="card">
                <div class="card-title">搜索影视学习</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">搜索指定或热门影视名称，查看返回的M3U8视频链接，用对应的视频链接进行学习，用M3U8的域名进行学习更新规则。</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
                    <input type="text" id="searchKeyword" placeholder="输入影视名称，如：流浪地球、庆余年..." style="flex:1;min-width:250px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px">
                    <select id="searchSiteSelect" style="padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px;min-width:150px">
                        <option value="all">全部资源站</option>
                    </select>
                    <input type="number" id="searchMaxSites" value="5" min="1" max="20" placeholder="最大站点数" style="width:120px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px">
                    <button class="btn btn-primary" onclick="searchVideos()">🔍 搜索</button>
                    <button class="btn btn-secondary" onclick="clearSearchResults()">清空</button>
                </div>
                <div id="searchResults" style="display:none">
                    <div id="searchSummary" style="padding:10px 12px;background:#ecf5ff;border:1px solid #d9ecff;border-radius:6px;margin-bottom:12px;font-size:13px;color:#409eff"></div>
                    <div id="searchActions" style="display:none;margin-bottom:12px;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                        <button class="btn btn-success" onclick="batchLearnAll()">📚 一键学习全部</button>
                        <button class="btn btn-primary" onclick="batchAnalyzeAll()">🔍 一键分析全部</button>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                            <input type="checkbox" id="enableMultiThread" checked onchange="onMultiThreadToggle()">
                            <span>⚡ 多线程加速</span>
                        </label>
                        <div id="concurrencyWrap" style="display:flex;align-items:center;gap:6px;font-size:13px;color:#606266">
                            <span>并发数:</span>
                            <select id="concurrencyNum" style="padding:4px 8px;border:1px solid #dcdfe6;border-radius:4px;font-size:12px">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="5" selected>5</option>
                                <option value="8">8</option>
                                <option value="10">10</option>
                            </select>
                        </div>
                        <span id="multiThreadBadge" style="font-size:11px;padding:2px 6px;background:#f0f9eb;color:#67c23a;border-radius:4px;display:none">后端加速</span>
                        <span style="font-size:12px;color:#909399;margin-left:auto" id="searchStats">准备就绪</span>
                    </div>
                    <div id="batchResult" style="display:none;margin-bottom:12px;padding:12px;border-radius:6px"></div>
                    <div id="searchVideoList"></div>
                </div>
                <div id="searchLoading" style="display:none;text-align:center;padding:20px;color:#909399">
                    <div class="loading" style="display:inline-block">正在搜索中，请稍候...</div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">
                    资源站列表
                    <span style="margin-left:12px;font-size:12px;color:#909399;font-weight:normal">共 <span id="sitesCount">0</span> 个资源站</span>
                </div>
                <div style="margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="showAddSite()">+ 新增资源站</button>
                    <button class="btn btn-secondary" onclick="checkSitesHealth()" id="healthCheckBtn">🔍 健康检测</button>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" id="showPaused" onchange="refreshSites()"> 显示已暂停
                    </label>
                    <input type="text" id="siteSearch" placeholder="搜索资源站名称..." style="flex:1;min-width:200px;padding:10px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px" oninput="filterSites()">
                </div>
                <div id="sitesTable"></div>
            </div>

            <div class="card" id="siteEditor" style="display:none">
                <div class="card-title" id="siteEditorTitle">新增资源站</div>
                <div class="form-group">
                    <label>资源站名称</label>
                    <input type="text" id="siteName" placeholder="例如：量子">
                </div>
                <div class="form-group">
                    <label>官网地址</label>
                    <input type="text" id="siteUrl" placeholder="例如：https://example.com">
                </div>
                <div class="form-group">
                    <label>采集接口</label>
                    <input type="text" id="siteApiUrl" placeholder="例如：https://example.com/api.php/provide/vod/">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                    <div class="form-group">
                        <label>类型</label>
                        <select id="siteType">
                            <option value="maccms">MacCMS</option>
                            <option value="custom">自定义</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>状态</label>
                        <select id="siteStatus">
                            <option value="active">正常</option>
                            <option value="paused">暂停</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>优先级</label>
                        <input type="number" id="sitePriority" min="1" max="99" value="50">
                    </div>
                </div>
                <div class="form-group">
                    <label>扩展备注</label>
                    <textarea id="siteNote" placeholder="备注信息，如：推荐、卡、停更等"></textarea>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveSite()">保存</button>
                    <button class="btn btn-secondary" onclick="cancelSiteEdit()">取消</button>
                </div>
            </div>

            <div class="card" id="siteVideos" style="display:none">
                <div class="card-title">
                    <span id="siteVideosTitle">视频列表</span>
                    <button class="btn btn-sm btn-secondary" style="float:right" onclick="closeSiteVideos()">关闭</button>
                </div>
                <div id="siteVideosList"></div>
            </div>
        </div>

        <div class="page" id="page-official_sites">
            <div class="card">
                <div class="card-title" style="display:flex;justify-content:space-between;align-items:center">
                    <span>推荐采集资源站列表</span>
                    <div style="display:flex;gap:10px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--text-regular)">
                            <input type="checkbox" id="officialSitesEnabled" onchange="toggleOfficialSites()">
                            启用推荐采集
                        </label>
                        <button class="btn btn-sm btn-primary" onclick="showAddOfficialSite()">+ 添加推荐站</button>
                    </div>
                </div>
                <div id="officialSitesList"></div>
            </div>

            <div class="card">
                <div class="card-title">推荐采集设置</div>
                <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
                    <div class="form-group">
                        <label>自动切换域名</label>
                        <select id="osAutoSwitch">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>每域名最大重试次数</label>
                        <input type="number" id="osMaxRetry" value="2" min="0" max="10">
                    </div>
                    <div class="form-group">
                        <label>请求超时（秒）</label>
                        <input type="number" id="osTimeout" value="10" min="5" max="60">
                    </div>
                    <div class="form-group">
                        <label>默认每页条数</label>
                        <input type="number" id="osDefaultLimit" value="20" min="5" max="100">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="saveOfficialSettings()">保存设置</button>
            </div>

            <div class="card" id="officialSiteVideos" style="display:none">
                <div class="card-title">
                    <span id="officialSiteVideoTitle">视频列表</span>
                    <button class="btn btn-sm btn-secondary" style="float:right" onclick="closeOfficialSiteVideos()">关闭</button>
                </div>
                <div style="margin-bottom:12px;display:flex;gap:10px">
                    <input type="text" id="officialVideoSearch" placeholder="搜索关键词..." style="flex:1;padding:10px 14px;border:1px solid var(--border-base);border-radius:6px;outline:none">
                    <button class="btn btn-primary" onclick="searchOfficialVideos()">搜索</button>
                    <button class="btn btn-secondary" onclick="refreshOfficialVideos()">刷新</button>
                </div>
                <div id="officialSiteVideosList"></div>
            </div>
        </div>

        <div class="page" id="page-official_replace">
            <div class="card">
                <div class="card-title">官替 API 配置</div>
                <div class="stats-grid" id="officialReplaceStats">
                    <div class="stat-card">
                        <div class="stat-value" id="orTotalPlatforms">-</div>
                        <div class="stat-label">支持平台数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orStatus">-</div>
                        <div class="stat-label">功能状态</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orSearchSites">-</div>
                        <div class="stat-label">搜索资源站</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="orThreshold">-</div>
                        <div class="stat-label">匹配阈值</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:16px">
                    <div class="form-group" style="margin-bottom:0">
                        <label>启用官替功能</label>
                        <select id="orEnabled">
                            <option value="true">启用</option>
                            <option value="false">禁用</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>匹配阈值 (0-100)</label>
                        <input type="number" id="orThresholdInput" min="0" max="100" value="60">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>最大搜索站点数</label>
                        <input type="number" id="orMaxSites" min="1" max="20" value="5">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label>搜索资源站 (逗号分隔)</label>
                        <input type="text" id="orSearchSitesInput" placeholder="留空表示搜索全部">
                    </div>
                </div>
                <button class="btn btn-primary" onclick="saveOfficialReplaceConfig()">保存配置</button>
            </div>

            <div class="card">
                <div class="card-title">
                    <span>支持平台列表</span>
                    <button class="btn btn-sm btn-primary" style="float:right" onclick="addOfficialPlatform()">+ 添加平台</button>
                </div>
                <div id="officialPlatformsList"></div>
            </div>

            <div class="card">
                <div class="card-title">官替 API 测试</div>
                <div class="input-group">
                    <input type="text" id="officialTestUrl" placeholder="输入官方视频链接，如：https://v.qq.com/x/cover/xxx.html">
                    <button class="btn btn-primary" onclick="testOfficialReplace()">测试解析</button>
                </div>
                <div style="font-size:12px;color:#909399;margin-bottom:16px">
                    支持：腾讯视频、爱奇艺、优酷、芒果TV、哔哩哔哩 等平台
                </div>
                <div id="officialTestResult" style="display:none">
                    <div id="officialTestInfo"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">API 接口说明</div>
                <div style="font-size:13px;line-height:1.8;color:#606266">
                    <p><strong>完整解析接口：</strong></p>
                    <div style="background:#f5f7fa;padding:12px;border-radius:6px;margin:8px 0">
                        <code id="api-resolve-url"></code>
                    </div>
                    <p><strong>精简信息接口 (JSON)：</strong></p>
                    <div style="background:#f5f7fa;padding:12px;border-radius:6px;margin:8px 0">
                        <code id="api-info-url"></code>
                    </div>
                    <p><strong>参数说明：</strong></p>
                    <ul style="margin-left:20px">
                        <li><code>url</code> - 官方视频播放页面链接</li>
                    </ul>
                    <p><strong>返回示例：</strong></p>
                    <pre style="background:#f5f7fa;padding:12px;border-radius:6px;overflow:auto;font-size:12px">{
  "success": true,
  "platform": "腾讯视频",
  "video_title": "庆余年",
  "match_score": 95.5,
  "site": "量子",
  "m3u8_url": "https://.../index.m3u8",
  "ad_skip_url": "https://你的域名/mx.php?action=mxjx&url=...",
  "target_episode": "第1集"
}</pre>
                </div>
            </div>
        </div>

        <div class="page" id="page-moxi_api">
            <div class="card">
                <div class="card-title" style="display:flex;justify-content:space-between;align-items:center">
                    <span>沫兮API 接口说明</span>
                    <button class="btn btn-secondary" onclick="window.open('api_doc.php', '_blank')">📚 查看完整 API 文档</button>
                </div>
                <div style="font-size:13px;line-height:1.8;color:#606266">
                    <p><strong>沫兮API 解析接口：</strong></p>
                    <div style="background:#f5f7fa;padding:12px;border-radius:6px;margin:8px 0">
                        <code id="moxi-api-url"></code>
                    </div>
                    <p><strong>参数说明：</strong></p>
                    <ul style="margin-left:20px">
                        <li><code>url</code> - 视频播放链接（支持官方视频链接或直接M3U8链接）</li>
                        <li><code>type</code> - （可选）播放类型</li>
                    </ul>
                    <p><strong>返回字段说明：</strong></p>
                    <ul style="margin-left:20px">
                        <li><code>code</code> - 状态码，200表示成功，400表示参数错误，404表示解析失败</li>
                        <li><code>url</code> - 解析后的播放地址</li>
                        <li><code>msg</code> - 返回消息</li>
                        <li><code>jm</code> - 剧名</li>
                        <li><code>js</code> - 集数</li>
                        <li><code>time</code> - 响应时间</li>
                        <li><code>kfz</code> - 开发者/接口标识</li>
                    </ul>
                    <p><strong>返回示例：</strong></p>
                    <pre style="background:#f5f7fa;padding:12px;border-radius:6px;overflow:auto;font-size:12px">{
  "code": 200,
  "url": "https://你的域名/mx.php?action=mxjx&url=...",
  "msg": "解析成功",
  "jm": "庆余年",
  "js": "第1集",
  "time": "2024-01-01 12:00:00",
  "kfz": "沫兮API - 在线视频解析"
}</pre>
                    <p><strong>支持平台：</strong></p>
                    <ul style="margin-left:20px">
                        <li>腾讯视频 (v.qq.com)</li>
                        <li>爱奇艺 (iqiyi.com)</li>
                        <li>优酷 (youku.com)</li>
                        <li>芒果TV (mgtv.com)</li>
                        <li>哔哩哔哩 (bilibili.com)</li>
                        <li>搜狐视频 (sohu.com)</li>
                        <li>PP视频 (pptv.com)</li>
                        <li>直接M3U8链接</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-title">沫兮API 接口测试</div>
                <div class="input-group">
                    <input type="text" id="moxiTestUrl" placeholder="输入视频链接，如：https://v.qq.com/x/cover/xxx.html 或 M3U8链接">
                    <button class="btn btn-primary" onclick="testMoxiApi()">测试解析</button>
                    <button class="btn btn-secondary" onclick="testMxjxApi()">测试去广告</button>
                    <button class="btn btn-secondary" onclick="testAnalyzeApi()">测试分析</button>
                    <button class="btn btn-secondary" onclick="testOfficialInfoApi()">测试官替</button>
                </div>
                <div style="font-size:12px;color:#909399;margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                    <span>支持官方视频链接和直接M3U8链接</span>
                    <span style="color:#ddd">|</span>
                    <span>快捷测试URL：</span>
                    <a href="javascript:void(0)" onclick="document.getElementById('moxiTestUrl').value='https://s3.bfllvip.com/video/qingyuniandiyiji/737c2ec959ce/index.m3u8';testMxjxApi()" style="color:#409eff;text-decoration:none" title="测试去广告">庆余年第1季 M3U8</a>
                    <a href="javascript:void(0)" onclick="document.getElementById('moxiTestUrl').value='https://v.qq.com/x/cover/mzc00200m2v9p9i.html';testOfficialInfoApi()" style="color:#409eff;text-decoration:none" title="测试官替解析">腾讯视频示例</a>
                </div>
                <div id="moxiTestResult" style="display:none">
                    <div id="moxiTestInfo"></div>
                </div>
            </div>
        </div>

        <div class="page" id="page-play">
            <div class="card">
                <div class="card-title">播放器设置</div>
                <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(250px,1fr))">
                    <div class="form-group">
                        <label>选择播放器</label>
                        <select id="playerSelect" onchange="changePlayerPreview()">
                            <option value="dplayer">DPlayer（推荐）</option>
                            <option value="videojs">Video.js</option>
                            <option value="muiplayer">MuiPlayer</option>
                            <option value="artplayer">ArtPlayer</option>
                            <option value="nplayer">NPlayer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>自动播放</label>
                        <select id="playerAutoplay">
                            <option value="false">关闭</option>
                            <option value="true">开启</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>预加载</label>
                        <select id="playerPreload">
                            <option value="auto">自动（推荐）</option>
                            <option value="metadata">仅元数据</option>
                            <option value="none">不预加载</option>
                        </select>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label>API 地址（留空则自动获取当前域名）</label>
                        <input type="text" id="playerApiBaseUrl" placeholder="例如：https://your-domain.com" style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:6px;font-size:14px">
                        <div style="margin-top:6px;font-size:12px;color:#909399">
                            💡 留空为动态获取当前域名；如使用授权IP，填写授权服务器地址，格式：http(s)://IP:端口
                        </div>
                    </div>
                </div>
                <div style="margin-top:12px;font-size:13px;color:#606266">
                    💡 不同播放器特点：DPlayer（弹幕+截图）、Video.js（兼容性好）、MuiPlayer（移动端优化）、ArtPlayer（功能丰富）、NPlayer（轻量高效）
                </div>
                <button class="btn btn-primary" style="margin-top:12px" onclick="savePlayerConfig()">保存播放器设置</button>
            </div>

            <div class="card">
                <div class="card-title">无广告播放测试</div>
                <div class="input-group">
                    <input type="text" id="playUrl" placeholder="输入 M3U8 视频链接">
                    <button class="btn btn-primary" onclick="playVideo()">播放</button>
                </div>
                <div id="playerContainer" style="display:none;margin-top:20px">
                    <div id="videoPlayer" style="width:100%;border-radius:8px;overflow:hidden"></div>
                    <div style="margin-top:12px;font-size:13px;color:#606266" id="playInfo"></div>
                </div>
            </div>
        </div>

        <div class="page" id="page-database">
            <div class="card">
                <div class="card-title">数据库状态</div>
                <div class="stats-grid" id="dbStats">
                    <div class="stat-card">
                        <div class="stat-value" id="dbType">-</div>
                        <div class="stat-label">数据库类型</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="dbStatus">-</div>
                        <div class="stat-label">运行状态</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="dbRuleCount">-</div>
                        <div class="stat-label">规则数量</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="dbSiteCount">-</div>
                        <div class="stat-label">资源站数量</div>
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="checkDbStatus()">刷新状态</button>
                    <button class="btn btn-success" onclick="showDbConfig()">数据库配置</button>
                    <button class="btn btn-warning" onclick="migrateData()">迁移数据</button>
                    <button class="btn btn-secondary" onclick="initDbTables()">初始化表结构</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">表结构检查</div>
                <div id="dbTables" style="font-size:13px;color:#606266">加载中...</div>
            </div>

            <div class="card">
                <div class="card-title">数据库配置</div>
                <div id="dbConfigPanel">
                    <p style="color:#606266;font-size:13px;margin-bottom:12px">
                        配置数据库连接信息。支持 SQLite（文件型，无需安装服务）和 MySQL（需 MySQL 服务）。
                    </p>
                    <div style="display:flex;gap:12px;margin-bottom:12px">
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                            <input type="radio" name="dbType" value="sqlite" checked onchange="toggleDbType('sqlite')"> SQLite
                        </label>
                        <label style="display:flex;align-items:center;gap:6px;cursor:pointer">
                            <input type="radio" name="dbType" value="mysql" onchange="toggleDbType('mysql')"> MySQL
                        </label>
                    </div>
                    <div style="padding:12px;background:#f5f7fa;border-radius:4px;font-size:13px;color:#909399;margin-bottom:12px">
                        ⚠️ 数据库配置从 <code>db/db_config.php</code> 文件读取，如需修改请直接编辑该文件。
                    </div>
                    <div id="sqliteConfig" style="margin-bottom:12px">
                        <div style="margin-bottom:8px;font-size:13px;color:#606266">数据库文件路径:</div>
                        <input type="text" id="sqlitePath" value="db/data.db" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                    </div>
                    <div id="mysqlConfig" style="display:none;display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">主机</div>
                            <input type="text" id="mysqlHost" value="127.0.0.1" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">端口</div>
                            <input type="number" id="mysqlPort" value="3306" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">数据库名</div>
                            <input type="text" id="mysqlDbname" value="m3u8_ad" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">用户名</div>
                            <input type="text" id="mysqlUsername" value="root" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">密码</div>
                            <input type="password" id="mysqlPassword" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                        <div>
                            <div style="margin-bottom:4px;font-size:13px;color:#606266">字符集</div>
                            <input type="text" id="mysqlCharset" value="utf8mb4" readonly style="width:100%;padding:8px 12px;border:1px solid #dcdfe6;border-radius:4px;background:#f5f7fa;color:#909399">
                        </div>
                    </div>
                    <div style="margin-top:16px;display:flex;gap:12px;align-items:center">
                        <button class="btn btn-secondary" onclick="testDbConnection()">测试连接</button>
                        <span id="testConnResult" style="font-size:13px;color:#909399"></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">数据迁移</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">
                    将原有文件存储的数据（规则、资源站、代理等）迁移到数据库中。迁移不会删除原有文件数据。
                </p>
                <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;align-items:center">
                    <button class="btn btn-warning" onclick="migrateData()">开始迁移</button>
                    <span id="migrateStatus" style="font-size:13px;color:#909399"></span>
                </div>
                <div id="migrateResult" style="margin-top:16px;display:none">
                    <pre style="background:#f5f7fa;padding:12px;border-radius:4px;font-size:12px;white-space:pre-wrap;word-wrap:break-word"></pre>
                </div>
            </div>
        </div>

        <div class="page" id="page-update">
            <div class="card">
                <div class="card-title">系统版本信息</div>
                <div class="stats-grid" id="versionStats">
                    <div class="stat-card">
                        <div class="stat-value" id="currentVersion">-</div>
                        <div class="stat-label">当前版本</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="latestVersion">-</div>
                        <div class="stat-label">最新版本</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="updateStatus">检查中...</div>
                        <div class="stat-label">更新状态</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="githubStatus">检查中...</div>
                        <div class="stat-label">GitHub连接</div>
                    </div>
                </div>
                <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-primary" onclick="checkUpdate()">检查更新</button>
                    <button class="btn btn-success" id="updateBtn" onclick="doUpdate()" disabled>立即更新</button>
                    <button class="btn btn-secondary" onclick="createBackup()">创建备份</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">服务器信息</div>
                <div id="serverInfo" style="font-size:13px;color:#606266">加载中...</div>
            </div>

            <div class="card">
                <div class="card-title">文件夹权限</div>
                <div id="permissionInfo" style="font-size:13px;color:#606266">加载中...</div>
            </div>

            <div class="card">
                <div class="card-title">缓存清理</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">清理浏览器缓存、Service Worker、localStorage 等，解决更新后页面不生效问题。</p>
                <div style="display:flex;gap:12px;flex-wrap:wrap">
                    <button class="btn btn-warning" onclick="clearAllCaches()">立即清理缓存</button>
                    <button class="btn btn-secondary" onclick="clearBrowserCache()">清理浏览器缓存</button>
                    <button class="btn btn-secondary" onclick="clearServiceWorker()">清理 Service Worker</button>
                </div>
                <div id="cacheClearResult" style="margin-top:12px;font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">完整性检查</div>
                <p style="color:#606266;font-size:13px;margin-bottom:12px">检查授权文件、核心文件完整性和系统状态。</p>
                <button class="btn btn-primary" onclick="checkIntegrity()">检查系统完整性</button>
                <div id="integrityResult" style="margin-top:12px;font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">更新结果</div>
                <div id="updateResult" style="font-size:12px;color:#606266"></div>
            </div>

            <div class="card">
                <div class="card-title">备份管理</div>
                <div id="backupList"></div>
            </div>
        </div>

        <div class="page" id="page-auth">
            <div class="card">
                <div class="card-title">授权状态概览</div>
                <div class="stats-grid" id="authStats">
                    <div class="stat-card">
                        <div class="stat-value" id="sqFileStatus">检查中...</div>
                        <div class="stat-label">授权文件</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="localAuthStatus">检查中...</div>
                        <div class="stat-label">本地验证</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="remoteAuthStatus">检查中...</div>
                        <div class="stat-label">远程验证</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="authVersion">-</div>
                        <div class="stat-label">当前版本</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-title">本地授权信息</div>
                <div id="localAuthInfo" style="font-size:13px;color:#606266">加载中...</div>
            </div>

            <div class="card">
                <div class="card-title">远程服务器信息</div>
                <div id="remoteAuthInfo" style="font-size:13px;color:#606266">加载中...</div>
            </div>

            <div class="card">
                <div class="card-title">授权配置</div>
                <div class="form-group">
                    <label>授权服务器 IP</label>
                    <input type="text" id="authServerIp" placeholder="例如：114.134.184.91">
                </div>
                <div class="form-group">
                    <label>授权服务器端口</label>
                    <input type="text" id="authServerPort" placeholder="例如：9001">
                </div>
                <div class="form-group">
                    <label>授权文件名</label>
                    <input type="text" id="authFile" placeholder="例如：sq.txt">
                </div>
                <div class="form-group">
                    <label>对比文件名</label>
                    <input type="text" id="authFileCompare" placeholder="例如：sq.txt">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="enableRemoteVerify"> 启用远程验证
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:8px">
                        <input type="checkbox" id="enableTimestampCheck"> 启用时间戳检查
                    </label>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-primary" onclick="saveAuthConfig()">保存配置</button>
                    <button class="btn btn-secondary" onclick="refreshAuthInfo()">刷新状态</button>
                </div>
            </div>

            <div class="card">
                <div class="card-title">设置授权码</div>
                <div class="form-group">
                    <label>输入授权码</label>
                    <textarea id="authCodeInput" placeholder="请输入授权码..."></textarea>
                </div>
                <div style="display:flex;gap:12px">
                    <button class="btn btn-success" onclick="setAuthCode()">设置授权码</button>
                    <button class="btn btn-secondary" onclick="generateAuthCode()">生成测试授权码</button>
                </div>
                <div style="margin-top:12px;font-size:12px;color:#909399">
                    授权异常或需要授权请联系 QQ: 2094332348
                </div>
            </div>
        </div>

        <div class="page" id="page-ai_skip">
            <div class="card">
                <div class="card-title">🤖 AI 智能处理中心</div>
                <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:white;padding:20px;border-radius:12px;margin-bottom:20px">
                    <div style="font-size:18px;font-weight:600;margin-bottom:8px">🧠 AI 智能处理引擎</div>
                    <div style="font-size:13px;opacity:0.9">一站式智能去广告解决方案：自动分析 → 识别广告簇 → 自动生成规则 → 智能过滤 → 自动学习，全流程自动化处理</div>
                </div>
                <div class="input-group">
                    <input type="text" id="aiSkipUrl" placeholder="输入 M3U8 视频链接，例如：https://example.com/video/index.m3u8">
                    <button class="btn btn-primary" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);border:none" onclick="aiSmartProcess()">✨ 一键智能处理</button>
                    <button class="btn btn-success" onclick="aiSkipVideo()">🚀 AI 去广告</button>
                    <button class="btn btn-secondary" onclick="aiSmartAnalyze()">🔍 智能分析</button>
                    <button class="btn btn-warning" onclick="aiProDetect()">🔬 专业检测</button>
                </div>
                <div style="margin-top:12px;display:flex;gap:16px;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiSkipSafeguard" checked> 启用安全守护
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiSkipAutoLearn" checked> 自动学习规则
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiSkipAutoSave"> 自动保存规则
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiSkipDeepAnalysis"> 深度分析模式
                    </label>
                </div>
                <div id="smartProcessSteps" style="display:none;margin-top:16px;padding:14px;background:#f5f7fa;border-radius:8px">
                    <div style="font-weight:600;color:#303133;margin-bottom:10px">⚡ 智能处理进度</div>
                    <div id="smartProcessStepList" style="font-size:13px;color:#606266">
                        <div style="padding:4px 0">⏳ 正在初始化...</div>
                    </div>
                </div>
            </div>

            <div id="aiSkipResult" style="display:none">
                <div class="stats-grid" id="aiSkipStats"></div>

                <div class="card">
                    <div class="card-title">处理结果</div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap">
                        <div style="flex:1;min-width:200px">
                            <div style="font-size:13px;color:#909399;margin-bottom:4px">无广告播放链接</div>
                            <code id="aiSkipOutputUrl" style="background:#f5f7fa;padding:8px 12px;border-radius:4px;word-break:break-all;display:block;cursor:pointer" onclick="copyText(this.textContent)" title="点击复制"></code>
                        </div>
                    </div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="copyText(document.getElementById('aiSkipOutputUrl').textContent)">📋 复制链接</button>
                        <button class="btn btn-primary" onclick="window.open(document.getElementById('aiSkipOutputUrl').textContent, '_blank')">🔗 新窗口播放</button>
                        <button class="btn btn-success" onclick="playAiSkipVideo()">▶️ 内置播放</button>
                        <button class="btn btn-secondary" onclick="downloadAiSkipM3u8()">💾 下载M3U8</button>
                    </div>
                    <div id="aiSkipPlayerContainer" style="display:none;margin-top:16px">
                        <div id="aiSkipVideoPlayer" style="width:100%;height:360px;border-radius:8px;overflow:hidden;background:#000"></div>
                        <div style="margin-top:8px;font-size:12px;color:#909399" id="aiSkipPlayStatus"></div>
                    </div>
                </div>

                <div class="card" id="proDetectCard" style="display:none">
                    <div class="card-title">🔬 专业广告检测报告</div>
                    <div id="proDetectResult" style="margin-bottom:16px"></div>
                </div>

                <div class="card">
                    <div class="card-title">🎯 广告簇分析</div>
                    <div id="aiSkipAdClusters" style="margin-bottom:16px">
                        <div style="text-align:center;color:#909399;padding:20px">加载中...</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">⚙️ 自动生成规则</div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:13px;color:#606266;margin-bottom:12px">基于视频内容智能分析，自动生成多种去广告规则（包括 DISCONTINUITY 正则规则、时长规则、序列号规则等）</div>
                        <div style="display:flex;gap:10px;flex-wrap:wrap">
                            <button class="btn btn-primary" onclick="aiSkipGenerateRules()">🤖 智能生成规则</button>
                            <button class="btn btn-success" onclick="goToRules()">🔧 规则管理</button>
                        </div>
                    </div>
                    <div id="aiSkipGeneratedRules" style="display:none">
                        <div class="tab-bar">
                            <div class="tab-item active" onclick="switchGenRuleTab(this, 'discontinuity')">DISCONTINUITY 正则</div>
                            <div class="tab-item" onclick="switchGenRuleTab(this, 'duration')">时长规则</div>
                            <div class="tab-item" onclick="switchGenRuleTab(this, 'sequence')">序列号规则</div>
                            <div class="tab-item" onclick="switchGenRuleTab(this, 'filename')">文件名规则</div>
                        </div>
                        <div id="genRuleContent" style="margin-top:12px"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">AI 识别详情</div>
                    <div class="tab-bar">
                        <div class="tab-item active" onclick="switchAiSkipTab(this, 'ad')">广告片段</div>
                        <div class="tab-item" onclick="switchAiSkipTab(this, 'content')">内容片段</div>
                        <div class="tab-item" onclick="switchAiSkipTab(this, 'md5')">MD5特征码</div>
                        <div class="tab-item" onclick="switchAiSkipTab(this, 'detail')">识别详情</div>
                    </div>
                    <div class="segment-list" id="aiSkipSegmentList"></div>
                </div>

                <div class="card">
                    <div class="card-title">快捷操作</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="aiSkipGenerateRules()">📋 生成规则</button>
                        <button class="btn btn-success" onclick="goToRules()">🔧 规则管理</button>
                        <button class="btn btn-secondary" onclick="aiSkipToInsert()">📺 检测插播</button>
                        <button class="btn btn-secondary" onclick="aiSkipToWatermark()">💧 水印处理</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="page" id="page-ai_insert">
            <div class="card">
                <div class="card-title">📺 AI 插播识别</div>
                <div style="background:linear-gradient(135deg, #f093fb 0%, #f5576c 100%);color:white;padding:20px;border-radius:12px;margin-bottom:20px">
                    <div style="font-size:18px;font-weight:600;margin-bottom:8px">智能插播检测引擎</div>
                    <div style="font-size:13px;opacity:0.9">自动识别视频中的插播内容，包括片头片尾广告、中间插播广告、跑马灯等，精准定位插播位置和时长</div>
                </div>
                <div class="input-group">
                    <input type="text" id="aiInsertUrl" placeholder="输入 M3U8 视频链接，检测插播内容">
                    <button class="btn btn-primary" onclick="aiInsertDetect()">🔍 检测插播</button>
                    <button class="btn btn-success" onclick="aiInsertMd5Analyze()">🔬 MD5分析</button>
                </div>
                <div style="margin-top:12px;display:flex;gap:16px;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiInsertOpening" checked> 检测片头
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiInsertEnding" checked> 检测片尾
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiInsertMiddle" checked> 检测中间插播
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiInsertMd5Mode" checked> MD5特征识别
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiInsertFastMode" checked> ⚡ 极速模式
                    </label>
                </div>
            </div>

            <div id="aiInsertResult" style="display:none">
                <div class="stats-grid" id="aiInsertStats"></div>

                <div class="card">
                    <div class="card-title">插播检测结果</div>
                    <div id="aiInsertList"></div>
                </div>

                <div class="card" id="aiInsertMd5Card" style="display:none">
                    <div class="card-title">🔬 MD5 特征码分析</div>
                    <div id="aiInsertMd5Content"></div>
                </div>

                <div class="card">
                    <div class="card-title">操作</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-primary" onclick="aiInsertSkip()">🚀 一键跳过插播</button>
                        <button class="btn btn-secondary" onclick="aiInsertToSkip()">🤖 去广告处理</button>
                        <button class="btn btn-secondary" onclick="aiInsertToWatermark()">💧 水印处理</button>
                    </div>
                    <div id="aiInsertOutput" style="margin-top:16px;display:none">
                        <div style="font-size:13px;color:#909399;margin-bottom:4px">纯净版播放链接</div>
                        <code id="aiInsertOutputUrl" style="background:#f5f7fa;padding:8px 12px;border-radius:4px;word-break:break-all;display:block;cursor:pointer" onclick="copyText(this.textContent)" title="点击复制"></code>
                    </div>
                </div>
            </div>
        </div>

        <div class="page" id="page-ai_watermark">
            <div class="card">
                <div class="card-title">💧 AI 水印处理</div>
                <div style="background:linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);color:white;padding:20px;border-radius:12px;margin-bottom:20px">
                    <div style="font-size:18px;font-weight:600;margin-bottom:8px">智能水印处理引擎</div>
                    <div style="font-size:13px;opacity:0.9">自动识别和处理视频中的水印参数，支持 URL 水印参数去除、TS 文件名水印处理、播放链接净化等功能</div>
                </div>
                <div class="input-group">
                    <input type="text" id="aiWatermarkUrl" placeholder="输入视频链接或播放地址，进行水印处理">
                    <button class="btn btn-primary" onclick="aiWatermarkProcess()">✨ 处理水印</button>
                </div>
                <div style="margin-top:12px;display:flex;gap:16px;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiWatermarkUrlParams" checked> 去除URL水印参数
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiWatermarkFilename" checked> 文件名水印处理
                    </label>
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:#606266">
                        <input type="checkbox" id="aiWatermarkReferer" checked> 自动处理Referer
                    </label>
                </div>
            </div>

            <div id="aiWatermarkResult" style="display:none">
                <div class="card">
                    <div class="card-title">处理结果</div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:13px;color:#909399;margin-bottom:8px">原始链接</div>
                        <code id="aiWatermarkOriginalUrl" style="background:#f5f7fa;padding:8px 12px;border-radius:4px;word-break:break-all;display:block;font-size:12px"></code>
                    </div>
                    <div style="margin-bottom:16px">
                        <div style="font-size:13px;color:#909399;margin-bottom:8px">处理后链接</div>
                        <code id="aiWatermarkOutputUrl" style="background:#ecfdf5;padding:8px 12px;border-radius:4px;word-break:break-all;display:block;cursor:pointer;color:#059669" onclick="copyText(this.textContent)" title="点击复制"></code>
                    </div>
                    <div id="aiWatermarkDetails"></div>
                    <div style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap">
                        <button class="btn btn-secondary" onclick="copyText(document.getElementById('aiWatermarkOutputUrl').textContent)">📋 复制链接</button>
                        <button class="btn btn-primary" onclick="window.open(document.getElementById('aiWatermarkOutputUrl').textContent, '_blank')">🔗 打开链接</button>
                        <button class="btn btn-success" onclick="aiWatermarkToSkip()">🤖 去广告处理</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">水印参数库</div>
                    <div style="font-size:13px;color:#606266;margin-bottom:12px">已收录常见水印参数，可自动识别并去除</div>
                    <div id="aiWatermarkLibList"></div>
                </div>
            </div>
        </div>

    </div>

        </main>
    </div>

    <div class="mobile-bottom-nav" id="mobileBottomNav">
        <div class="mobile-nav-items">
            <div class="mobile-nav-item active" data-page="dashboard" onclick="mobileNavTo('dashboard')">
                <div class="nav-icon">🏠</div>
                <div>首页</div>
            </div>
            <div class="mobile-nav-item" data-page="ai_skip" onclick="mobileNavTo('ai_skip')">
                <div class="nav-icon">🤖</div>
                <div>AI去广告</div>
            </div>
            <div class="mobile-nav-item" data-page="analyze" onclick="mobileNavTo('analyze')">
                <div class="nav-icon">🎯</div>
                <div>分析</div>
            </div>
            <div class="mobile-nav-item" data-page="rules" onclick="mobileNavTo('rules')">
                <div class="nav-icon">📋</div>
                <div>规则</div>
            </div>
            <div class="mobile-nav-item" data-page="history" onclick="mobileNavTo('history')">
                <div class="nav-icon">📜</div>
                <div>历史</div>
            </div>
        </div>
    </div>

    <div id="toastContainer"></div>

    <div id="updateModal" class="update-modal-overlay" style="display:none" onclick="if(event.target===this)hideUpdateModal()">
        <div class="update-modal">
            <div class="update-modal-header">
                <div class="update-modal-icon">🎉</div>
                <div class="update-modal-title">发现新版本</div>
                <button class="update-modal-close" onclick="hideUpdateModal()">✕</button>
            </div>
            <div class="update-modal-body">
                <div class="update-version-info">
                    <div class="version-row">
                        <span class="version-label">当前版本</span>
                        <span class="version-value current" id="modalCurrentVersion">-</span>
                    </div>
                    <div class="version-arrow">↓</div>
                    <div class="version-row">
                        <span class="version-label">最新版本</span>
                        <span class="version-value latest" id="modalLatestVersion">-</span>
                    </div>
                </div>
                <div class="update-meta" id="modalUpdateMeta"></div>
                <div class="update-changelog-title">
                    <span>📋 更新内容</span>
                    <span class="changelog-count" id="changelogCount"></span>
                </div>
                <div class="update-changelog" id="modalChangelog">
                    <div style="text-align:center;color:#909399;padding:20px">加载中...</div>
                </div>
            </div>
            <div class="update-modal-footer">
                <button class="btn btn-secondary" onclick="hideUpdateModal()">稍后再说</button>
                <button class="btn btn-primary" onclick="doUpdateFromModal()">立即更新</button>
            </div>
        </div>
    </div>

    <style>
        .update-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.2s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .update-modal {
            background: #fff;
            border-radius: 16px;
            width: 480px;
            max-width: 90vw;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease;
        }
        .update-modal-header {
            background: var(--primary-gradient, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
            color: white;
            padding: 24px 24px 20px;
            position: relative;
        }
        .update-modal-icon {
            font-size: 36px;
            margin-bottom: 8px;
        }
        .update-modal-title {
            font-size: 20px;
            font-weight: 600;
        }
        .update-modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .update-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .update-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
        }
        .update-version-info {
            background: #f5f7fa;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .version-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }
        .version-label {
            color: #909399;
            font-size: 13px;
        }
        .version-value {
            font-family: 'Monaco', 'Consolas', monospace;
            font-size: 13px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .version-value.current {
            color: #909399;
            background: #f4f4f5;
        }
        .version-value.latest {
            color: #67c23a;
            background: #f0f9eb;
        }
        .version-arrow {
            text-align: center;
            color: #67c23a;
            font-size: 18px;
            margin: 2px 0;
        }
        .update-meta {
            color: #909399;
            font-size: 12px;
            margin-bottom: 16px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .update-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .update-changelog-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 600;
            color: #303133;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #ebeef5;
        }
        .changelog-count {
            font-size: 12px;
            color: #909399;
            font-weight: normal;
            background: #ecf5ff;
            color: #409eff;
            padding: 2px 8px;
            border-radius: 10px;
        }
        .update-changelog {
            max-height: 280px;
            overflow-y: auto;
        }
        .changelog-item {
            display: flex;
            gap: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #f2f6fc;
        }
        .changelog-item:last-child {
            border-bottom: none;
        }
        .changelog-type {
            flex-shrink: 0;
            font-size: 11px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
            height: fit-content;
            margin-top: 1px;
        }
        .changelog-content {
            flex: 1;
            min-width: 0;
        }
        .changelog-msg {
            font-size: 13px;
            color: #303133;
            line-height: 1.5;
            word-break: break-all;
        }
        .changelog-meta {
            font-size: 11px;
            color: #c0c4cc;
            margin-top: 2px;
        }
        .changelog-meta span + span::before {
            content: '·';
            margin: 0 4px;
        }
        .update-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid #ebeef5;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>

    <script>
        const API_BASE = (function() {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const path = window.location.pathname;
            const baseDir = path.substring(0, path.lastIndexOf('/'));
            return protocol + '//' + host + baseDir + '/mx.php';
        })();

        const MENU_CONFIG = [
            {
                group: '工作台',
                items: [
                    { page: 'dashboard', icon: '🏠', text: '数据概览', badge: 'NEW' },
                    { page: 'history', icon: '📜', text: '分析历史' },
                    { page: 'batch', icon: '📦', text: '批量分析' },
                ]
            },
            {
                group: 'AI智能处理',
                items: [
                    { page: 'ai_skip', icon: '🤖', text: 'AI自动去广告', badge: 'HOT' },
                    { page: 'ai_insert', icon: '📺', text: 'AI插播识别' },
                    { page: 'ai_watermark', icon: '💧', text: 'AI水印处理' },
                ]
            },
            {
                group: '核心功能',
                items: [
                    { page: 'analyze', icon: '🎯', text: '视频分析' },
                    { page: 'rules', icon: '📋', text: '规则管理' },
                ]
            },
            {
                group: '资源管理',
                items: [
                    { page: 'sites', icon: '🌐', text: '资源站管理' },
                    { page: 'official_sites', icon: '⭐', text: '推荐采集' },
                    { page: 'official_replace', icon: '🔄', text: '官替管理' },
                ]
            },
            {
                group: '接口工具',
                items: [
                    { page: 'moxi_api', icon: '⚡', text: '沫兮API' },
                    { icon: '📚', text: 'API文档', action: "window.open('api_doc.php', '_blank')" },
                    { icon: '🔌', text: '代理池管理', action: "location.href='proxy/proxy_admin.php'" },
                ]
            },
            {
                group: '系统管理',
                items: [
                    { page: 'database', icon: '🗄️', text: '数据库管理' },
                    { page: 'play', icon: '▶️', text: '在线播放' },
                    { page: 'update', icon: '🔧', text: '系统更新' },
                    { page: 'auth', icon: '🔐', text: '授权管理' },
                ]
            }
        ];

        function renderSidebarMenu() {
            const container = document.getElementById('sidebarMenu');
            if (!container) return;
            let html = '';
            MENU_CONFIG.forEach(group => {
                html += '<div class="menu-group">';
                html += '<div class="menu-group-title">' + escapeHtml(group.group) + '</div>';
                group.items.forEach(item => {
                    const dataPage = item.page ? ' data-page="' + item.page + '"' : '';
                    const onclick = item.action ? ' onclick="' + item.action + '"' : '';
                    const activeClass = item.page === 'dashboard' ? ' active' : '';
                    const badgeHtml = item.badge ? '<span class="menu-badge">' + escapeHtml(item.badge) + '</span>' : '';
                    html += '<div class="nav-item' + activeClass + '"' + dataPage + onclick + '>';
                    html += '<span class="menu-icon">' + item.icon + '</span>';
                    html += '<span class="menu-text">' + escapeHtml(item.text) + '</span>';
                    html += badgeHtml;
                    html += '</div>';
                });
                html += '</div>';
            });
            container.innerHTML = html;
        }

        let currentAnalysis = null;
        let currentSegmentTab = 'ad';
        let editingRules = null;
        let dp = null;

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = 'toast ' + type;
            toast.textContent = message;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function handleNavClick(item) {
            if (!item.dataset.page) return;
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
            item.classList.add('active');
            const pageId = 'page-' + item.dataset.page;
            const pageEl = document.getElementById(pageId);
            if (pageEl) pageEl.classList.add('active');
            const page = item.dataset.page;
            if (page === 'rules') refreshRules();
            if (page === 'sites') refreshSites();
            if (page === 'auth') refreshAuthInfo();
            if (page === 'update') { checkUpdate(); loadVersion(); loadBackupList(); }
            if (page === 'database') checkDbStatus();
        }

        document.addEventListener('click', (e) => {
            const item = e.target.closest('.nav-item');
            if (item && item.dataset.page) {
                handleNavClick(item);
            }
        });

        async function analyzeVideo() {
            const url = document.getElementById('analyzeUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '分析中...';
            document.getElementById('analyzeResult').style.display = 'none';
            try {
                const res = await fetch(API_BASE + '?action=analyze&url=' + encodeURIComponent(url));
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message);
                currentAnalysis = data;
                renderAnalysis(data);
                document.getElementById('analyzeResult').style.display = 'block';
                saveToHistory(url, 'analyze', data);
                showToast('分析完成', 'success');
            } catch (e) {
                showToast('分析失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '开始分析';
            }
        }

        let analyzeBaseUrl = '';

        function renderAnalysis(data) {
            const url = document.getElementById('analyzeUrl').value.trim();
            analyzeBaseUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            updateAnalyzeMxjxUrl();

            const bannerEl = document.getElementById('fastModeBanner');
            const detailGrid = document.getElementById('detailGrid');
            const segmentCard = document.querySelector('#page-analyze .card:nth-of-type(4)');
            const actionCard = document.querySelector('#page-analyze .card:nth-of-type(5)');

            if (data.fastMode) {
                bannerEl.style.display = 'flex';
                bannerEl.innerHTML = `
                    <div class="icon">⚡</div>
                    <div class="content">
                        <div class="title">快速模式 - 已有域名规则<span class="domain-tag">${data.domain}</span></div>
                        <div class="desc">${data.message || '检测到已有域名规则，使用规则快速去广告，无需重复分析'}</div>
                    </div>
                    <button class="btn btn-sm btn-secondary" onclick="goToRules()">查看规则</button>
                `;
                if (detailGrid) detailGrid.style.display = 'none';
                if (segmentCard) segmentCard.style.display = 'none';
            } else {
                bannerEl.style.display = 'none';
                if (detailGrid) detailGrid.style.display = 'grid';
                if (segmentCard) segmentCard.style.display = 'block';
            }

            const stats = data.stats;
            const pct = stats.totalSegments > 0 ? (stats.adSegments / stats.totalSegments * 100).toFixed(1) : 0;

            if (data.fastMode) {
                document.getElementById('statsGrid').innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${stats.totalSegments}</div>
                        <div class="stat-label">总片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value danger">${stats.adSegments}</div>
                        <div class="stat-label">广告片段数 (${pct}%)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.keptSegments}</div>
                        <div class="stat-label">保留片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value warning">${(stats.savedDuration / 60).toFixed(1)}分钟</div>
                        <div class="stat-label">节省时长</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.adPercentage}%</div>
                        <div class="stat-label">广告占比</div>
                    </div>
                `;
            } else {
                document.getElementById('statsGrid').innerHTML = `
                    <div class="stat-card">
                        <div class="stat-value">${stats.totalSegments}</div>
                        <div class="stat-label">总片段数</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value danger">${stats.adSegments}</div>
                        <div class="stat-label">广告片段数 (${pct}%)</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value warning">${stats.discontinuityCount}</div>
                        <div class="stat-label">DISCONTINUITY 标记</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">${stats.sequenceJumpCount}</div>
                        <div class="stat-label">序列号跳跃</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value success">${stats.adClusterCount}</div>
                        <div class="stat-label">广告聚类</div>
                    </div>
                `;
            }

            if (data.fastMode) return;

            const jumps = data.sequenceJumps;
            const jumpHtml = jumps.length === 0
                ? '<div class="empty">未检测到明显序列号跳跃</div>'
                : jumps.map(j => `
                    <div class="jump-item">
                        <div style="font-family:monospace;font-size:11px;color:#909399;margin-bottom:4px">
                            索引 ${j.index}
                        </div>
                        <div>
                            <span style="font-family:monospace">${basename(j.prevUri)}</span>
                            <span class="jump-arrow"> → </span>
                            <span style="font-family:monospace">${basename(j.currentUri)}</span>
                        </div>
                        <div style="margin-top:4px;color:#e6a23c">
                            跳跃: ${j.jump > 0 ? '+' : ''}${j.jump} (${j.prevSeq} → ${j.currentSeq})
                        </div>
                    </div>
                `).join('');
            document.getElementById('jumpList').innerHTML = jumpHtml;

            const dist = data.durationDistribution;
            if (dist && dist.buckets) {
                const buckets = Object.entries(dist.buckets).sort((a, b) => parseFloat(a[0]) - parseFloat(b[0]));
                const maxCount = Math.max(...buckets.map(b => b[1]));
                const chartHtml = buckets.map(([dur, count]) => {
                    const h = (count / maxCount * 100);
                    return `<div class="bar" style="height:${h}%" title="时长: ${dur}s, 数量: ${count}"></div>`;
                }).join('');
                document.getElementById('durationChart').innerHTML = chartHtml;
                document.getElementById('durationStats').innerHTML = `
                    最短: ${dist.min.toFixed(2)}s | 最长: ${dist.max.toFixed(2)}s | 平均: ${dist.avg.toFixed(2)}s
                `;
            }

            renderSegmentList();

            const learnStatusEl = document.getElementById('learnStatus');
            const learnBtn = document.getElementById('learnBtn');
            if (learnStatusEl && learnBtn) {
                if (data.fastMode) {
                    learnBtn.style.display = 'none';
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 快速模式：已有域名规则，直接使用规则去广告</span>（学习次数: ' + (data.learn_count || 0) + '次）';
                } else if (data.autoLearned) {
                    learnBtn.style.display = 'none';
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 自动学习完成，规则已更新</span>（学习次数: ' + (data.learn_count || 0) + '次）';
                } else {
                    learnBtn.style.display = 'inline-block';
                    learnStatusEl.style.display = 'none';
                }
            }
        }

        function basename(path) {
            return path.split('/').pop();
        }

        function switchSegmentTab(el, tab) {
            document.querySelectorAll('#page-analyze .tab-item').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            currentSegmentTab = tab;
            renderSegmentList();
        }

        function renderSegmentList() {
            if (!currentAnalysis) return;
            const listEl = document.getElementById('segmentList');

            if (currentSegmentTab === 'ad') {
                const ads = currentAnalysis.adSegments;
                if (!ads || ads.length === 0) {
                    listEl.innerHTML = '<div class="empty">未检测到广告片段</div>';
                    return;
                }
                listEl.innerHTML = ads.map((s, idx) => {
                    const uri = s.uri || (s.segment && s.segment.uri) || '';
                    const duration = s.duration || (s.segment && s.segment.duration) || 0;
                    const matchedRules = s.matchedRules || [];
                    return `
                    <div class="segment-item ad">
                        <div>
                            <span class="segment-name">${basename(uri) || ('片段#' + idx)}</span>
                            ${matchedRules.map(r => `<span class="tag tag-red">${r.name}</span>`).join('')}
                        </div>
                        <span class="segment-duration">${parseFloat(duration).toFixed(2)}s</span>
                    </div>
                `}).join('');
            } else if (currentSegmentTab === 'cluster') {
                const clusters = currentAnalysis.adClusters;
                if (!clusters || clusters.length === 0) {
                    listEl.innerHTML = '<div class="empty">无广告聚类</div>';
                    return;
                }
                let totalAdDuration = 0;
                const ads = currentAnalysis.adSegments || [];
                ads.forEach(s => {
                    const dur = s.duration || (s.segment && s.segment.duration) || 0;
                    totalAdDuration += dur;
                });
                listEl.innerHTML = clusters.map((c, i) => {
                    return `
                        <div class="segment-item ad">
                            <div>
                                <span class="tag tag-red">聚类 #${i + 1}</span>
                                <span style="margin-left:8px">索引 ${c.start} - ${c.end}</span>
                            </div>
                            <span class="segment-duration">${c.count}个片段 / ${parseFloat(c.duration || 0).toFixed(2)}s</span>
                        </div>
                    `;
                }).join('');
            } else {
                const segs = currentAnalysis.allSegments;
                if (!segs || segs.length === 0) {
                    listEl.innerHTML = '<div class="empty">无片段数据</div>';
                    return;
                }
                listEl.innerHTML = segs.map((s, i) => {
                    const index = s.index ?? s.i ?? i;
                    const isAd = s.isAd ?? (s.a === 1) ?? false;
                    const uri = s.uri || (s.segment && s.segment.uri) || '';
                    const duration = s.duration || (s.segment && s.segment.duration) || s.d || 0;
                    const matchedRules = s.matchedRules || [];
                    const discontinuity = s.discontinuity || (s.segment && s.segment.discontinuity) || false;
                    return `
                    <div class="segment-item ${isAd ? 'ad' : ''}">
                        <div>
                            <span style="color:#909399;font-size:11px;margin-right:8px">#${index}</span>
                            <span class="segment-name">${basename(uri) || ('片段#' + index)}</span>
                            ${isAd && matchedRules.length > 0 ? matchedRules.map(r => `<span class="tag tag-red">${r.name}</span>`).join('') : ''}
                            ${discontinuity ? '<span class="tag tag-orange">DISCON</span>' : ''}
                        </div>
                        <span class="segment-duration">${parseFloat(duration).toFixed(2)}s</span>
                    </div>
                `}).join('');
            }
        }

        async function generateRules() {
            if (!currentAnalysis) { showToast('请先分析视频', 'error'); return; }
            try {
                const res = await fetch(API_BASE + '?action=rules/generate&url=' + encodeURIComponent(currentAnalysis.url));
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message);
                editingRules = data.rules;
                document.querySelector('.nav-item[data-page="rules"]').click();
                showRuleEditor(data.rules, true);
                showToast('规则已生成，请编辑保存', 'success');
            } catch (e) {
                showToast('生成规则失败: ' + e.message, 'error');
            }
        }

        async function learnRules() {
            if (!currentAnalysis) { showToast('请先分析视频', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '学习中...';
            try {
                const learnUrl = currentAnalysis.mediaUrl || currentAnalysis.url;
                const res = await fetch(API_BASE + '?action=rules/learn&url=' + encodeURIComponent(learnUrl));
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message);
                const learnStatusEl = document.getElementById('learnStatus');
                if (learnStatusEl) {
                    learnStatusEl.style.display = 'block';
                    learnStatusEl.innerHTML = '<span style="color:#67c23a">✅ 学习完成，规则已更新</span>（学习次数: ' + data.learn_count + '次）';
                }
                btn.style.display = 'none';
                showToast('规则学习成功', 'success');
            } catch (e) {
                showToast('学习失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '学习并更新规则';
            }
        }

        function goToRules() {
            document.querySelector('.nav-item[data-page="rules"]').click();
        }

        let currentAiSkipData = null;
        let aiSkipSegmentTab = 'ad';

        async function aiSmartProcess() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }

            const autoSave = document.getElementById('aiSkipAutoSave').checked;

            document.getElementById('smartProcessSteps').style.display = 'block';
            document.getElementById('smartProcessStepList').innerHTML = '<div style="padding:4px 0">⏳ 正在初始化...</div>';
            document.getElementById('aiSkipResult').style.display = 'none';

            try {
                const params = new URLSearchParams({
                    action: 'ai/smart_process',
                    url: url,
                    mode: 'full',
                    auto_save: autoSave ? '1' : '0'
                });

                const res = await fetch(API_BASE + '?' + params.toString());
                const data = await res.json();

                if (!data.success) throw new Error(data.message || '处理失败');

                const result = data.data;

                if (result.steps && result.steps.length > 0) {
                    document.getElementById('smartProcessStepList').innerHTML = result.steps.map(s =>
                        '<div style="padding:4px 0">' + s + '</div>'
                    ).join('');
                }

                currentAiSkipData = {
                    data: {
                        stats: result.stats || {},
                        process_time: result.process_time ? result.process_time + 'ms' : '0ms',
                        adClusters: result.ad_clusters || [],
                        ad_clusters: result.ad_clusters || [],
                        discontinuityRegexRules: result.discontinuity_regex_rules || [],
                        discontinuity_regex_rules: result.discontinuity_regex_rules || [],
                        rules: result.auto_rules?.rules || result.auto_rules || [],
                        ad_segments: (result.filtered?.removedSegments || []).map(s => ({
                            uri: s.uri,
                            duration: s.duration,
                            mediaSequence: s.mediaSequence,
                            isAd: true,
                            adReasons: s.adInfo?.matchedRules || []
                        })),
                        content_segments: (result.filtered?.segments || []).map(s => ({
                            uri: s.uri,
                            duration: s.duration,
                            mediaSequence: s.mediaSequence,
                            isAd: false
                        }))
                    }
                };

                renderAiSkipResult(currentAiSkipData);
                document.getElementById('aiSkipResult').style.display = 'block';
                document.getElementById('aiSkipOutputUrl').textContent = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);

                saveToHistory(url, 'ai_smart', data);
                showToast('✨ 智能处理完成！', 'success');

            } catch (e) {
                document.getElementById('smartProcessStepList').innerHTML += '<div style="padding:4px 0;color:#f56c6c">❌ 处理失败: ' + e.message + '</div>';
                showToast('处理失败: ' + e.message, 'error');
            }
        }

        async function aiSmartAnalyze() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }

            document.getElementById('smartProcessSteps').style.display = 'block';
            document.getElementById('smartProcessStepList').innerHTML = '<div style="padding:4px 0">🔍 正在进行智能分析...</div>';
            document.getElementById('aiSkipResult').style.display = 'none';

            try {
                const params = new URLSearchParams({
                    action: 'ai/smart_process',
                    url: url,
                    mode: 'analyze'
                });

                const res = await fetch(API_BASE + '?' + params.toString());
                const data = await res.json();

                if (!data.success) throw new Error(data.message || '分析失败');

                const result = data.data;

                document.getElementById('smartProcessStepList').innerHTML =
                    '<div style="padding:4px 0">✅ 解析完成，共 ' + (result.total_segments || 0) + ' 个片段</div>' +
                    '<div style="padding:4px 0">🔍 智能分析完成</div>' +
                    '<div style="padding:4px 0">🎯 识别出 ' + (result.ad_clusters || []).length + ' 个广告片段集群</div>' +
                    '<div style="padding:4px 0">⚙️ 生成 ' + (result.discontinuity_regex_rules || []).length + ' 条 DISCONTINUITY 正则规则</div>' +
                    '<div style="padding:4px 0">✨ 分析完成！</div>';

                currentAiSkipData = {
                    data: {
                        stats: {
                            totalSegments: result.total_segments || 0,
                            adSegments: result.ad_summary?.ad_count || 0,
                            ad_percentage: result.ad_summary?.ad_percentage || 0,
                            discontinuity_count: result.analysis?.discontinuityCount || 0,
                            ad_cluster_count: (result.ad_clusters || []).length
                        },
                        process_time: '0ms',
                        adClusters: result.ad_clusters || [],
                        ad_clusters: result.ad_clusters || [],
                        discontinuityRegexRules: result.discontinuity_regex_rules || [],
                        discontinuity_regex_rules: result.discontinuity_regex_rules || [],
                        rules: result.auto_rules?.rules || result.auto_rules || [],
                        ad_segments: [],
                        content_segments: []
                    }
                };

                renderAiSkipAdClusters(currentAiSkipData);
                renderAiSkipGeneratedRules(currentAiSkipData);
                document.getElementById('aiSkipResult').style.display = 'block';

                saveToHistory(url, 'ai_analyze', data);
                showToast('🔍 智能分析完成！', 'success');

            } catch (e) {
                document.getElementById('smartProcessStepList').innerHTML += '<div style="padding:4px 0;color:#f56c6c">❌ 分析失败: ' + e.message + '</div>';
                showToast('分析失败: ' + e.message, 'error');
            }
        }

        async function aiProDetect() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }

            document.getElementById('smartProcessSteps').style.display = 'block';
            document.getElementById('smartProcessStepList').innerHTML = '<div style="padding:4px 0">🔬 正在进行专业级广告检测...</div>';
            document.getElementById('proDetectCard').style.display = 'none';
            document.getElementById('aiSkipResult').style.display = 'none';

            try {
                const params = new URLSearchParams({
                    action: 'ai/pro_detect',
                    url: url
                });

                const res = await fetch(API_BASE + '?' + params.toString());
                const data = await res.json();

                if (!data.success) throw new Error(data.message || '检测失败');

                const result = data.data;
                const pro = result.professional_analysis || {};

                document.getElementById('smartProcessStepList').innerHTML =
                    '<div style="padding:4px 0">✅ 解析完成，共 ' + (result.total_segments || 0) + ' 个片段</div>' +
                    '<div style="padding:4px 0">🔬 统计学异常检测完成</div>' +
                    '<div style="padding:4px 0">📊 时长聚类分析完成</div>' +
                    '<div style="padding:4px 0">🔀 DISCONTINUITY 上下文分析完成</div>' +
                    '<div style="padding:4px 0">🎯 识别 ' + (pro.ad_segment_count || 0) + ' 个广告片段</div>' +
                    '<div style="padding:4px 0">📦 ' + (pro.ad_cluster_count || 0) + ' 个广告簇</div>' +
                    '<div style="padding:4px 0">✨ 专业检测完成！</div>';

                renderProDetectResult(pro);
                document.getElementById('proDetectCard').style.display = 'block';
                document.getElementById('aiSkipResult').style.display = 'block';

                // 同时渲染广告簇和规则
                currentAiSkipData = {
                    data: {
                        stats: {
                            totalSegments: result.total_segments || 0,
                            adSegments: pro.ad_segment_count || 0,
                            ad_percentage: pro.ad_percentage || 0,
                            ad_cluster_count: pro.ad_cluster_count || 0
                        },
                        process_time: '0ms',
                        adClusters: result.ad_clusters || [],
                        ad_clusters: result.ad_clusters || [],
                        discontinuityRegexRules: result.discontinuity_regex_rules || [],
                        discontinuity_regex_rules: result.discontinuity_regex_rules || [],
                        rules: result.auto_rules?.rules || result.auto_rules || [],
                        ad_segments: (pro.ad_segments || []).map(s => ({
                            uri: s.uri,
                            duration: s.duration,
                            mediaSequence: s.index,
                            isAd: true,
                            adReasons: s.reasons || [],
                            score: s.score,
                            confidence: s.confidence
                        })),
                        content_segments: (pro.content_segments || []).map(s => ({
                            uri: s.uri,
                            duration: s.duration,
                            mediaSequence: s.index,
                            isAd: false,
                            score: s.score
                        }))
                    }
                };
                renderAiSkipAdClusters(currentAiSkipData);
                renderAiSkipGeneratedRules(currentAiSkipData);
                renderAiSkipSegmentList();

                saveToHistory(url, 'pro_detect', data);
                showToast('🔬 专业检测完成！', 'success');

            } catch (e) {
                document.getElementById('smartProcessStepList').innerHTML += '<div style="padding:4px 0;color:#f56c6c">❌ 检测失败: ' + e.message + '</div>';
                showToast('检测失败: ' + e.message, 'error');
            }
        }

        function renderProDetectResult(pro) {
            const container = document.getElementById('proDetectResult');
            if (!pro || !pro.success) {
                container.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂无专业检测数据</div>';
                return;
            }

            const stats = pro.duration_stats || {};
            const conf = pro.confidence_summary || {};
            const details = pro.analysis_details || {};

            let html = '';

            // 统计概览
            html += '<div class="stats-grid" style="margin-bottom:16px">';
            html += '<div class="stat-card"><div class="stat-value" style="color:#667eea">' + (pro.total_segments || 0) + '</div><div class="stat-label">总片段</div></div>';
            html += '<div class="stat-card"><div class="stat-value" style="color:#f56c6c">' + (pro.ad_segment_count || 0) + '</div><div class="stat-label">广告片段</div></div>';
            html += '<div class="stat-card"><div class="stat-value" style="color:#67c23a">' + (pro.content_segment_count || 0) + '</div><div class="stat-label">内容片段</div></div>';
            html += '<div class="stat-card"><div class="stat-value" style="color:#e6a23c">' + (pro.ad_percentage || 0) + '%</div><div class="stat-label">广告占比</div></div>';
            html += '</div>';

            // 时长统计
            html += '<div style="padding:12px;background:#f5f7fa;border-radius:8px;margin-bottom:12px">';
            html += '<div style="font-weight:600;color:#303133;margin-bottom:8px">📊 时长统计分析</div>';
            html += '<div style="font-size:12px;color:#606266;display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:6px">';
            html += '<div>均值: ' + (stats.mean || 0) + 's</div>';
            html += '<div>中位数: ' + (stats.median || 0) + 's</div>';
            html += '<div>标准差: ' + (stats.stddev || 0) + '</div>';
            html += '<div>众数: ' + (stats.mode || 0) + 's</div>';
            html += '<div>最小: ' + (stats.min || 0) + 's</div>';
            html += '<div>最大: ' + (stats.max || 0) + 's</div>';
            html += '<div>Q1: ' + (stats.q1 || 0) + 's</div>';
            html += '<div>Q3: ' + (stats.q3 || 0) + 's</div>';
            html += '</div></div>';

            // 置信度分布
            if (conf.very_high !== undefined) {
                html += '<div style="padding:12px;background:#f5f7fa;border-radius:8px;margin-bottom:12px">';
                html += '<div style="font-weight:600;color:#303133;margin-bottom:8px">🎯 置信度分布</div>';
                html += '<div style="display:flex;gap:8px;flex-wrap:wrap">';
                html += '<span class="badge badge-danger">极高 ' + (conf.very_high || 0) + '</span>';
                html += '<span class="badge badge-warning">高 ' + (conf.high || 0) + '</span>';
                html += '<span class="badge badge-primary">中 ' + (conf.medium || 0) + '</span>';
                html += '<span class="badge badge-info">低 ' + (conf.low || 0) + '</span>';
                html += '<span class="badge badge-success">极低 ' + (conf.very_low || 0) + '</span>';
                html += '</div></div>';
            }

            // 检测维度
            if (details.statistical) {
                const statAnomalies = details.statistical || {};
                const anomalyCount = Object.keys(statAnomalies).length;
                html += '<div style="padding:12px;background:#f5f7fa;border-radius:8px;margin-bottom:12px">';
                html += '<div style="font-weight:600;color:#303133;margin-bottom:8px">🔬 检测维度</div>';
                html += '<div style="font-size:12px;color:#606266;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:6px">';
                html += '<div>📈 统计学异常: ' + anomalyCount + ' 个片段</div>';
                const clusters = details.duration_clusters || {};
                html += '<div>📊 时长聚类: 广告群 ' + (clusters.ad_cluster?.count || 0) + ' / 内容群 ' + (clusters.content_cluster?.count || 0) + '</div>';
                const disc = details.discontinuity || {};
                html += '<div>🔀 DISCONTINUITY: ' + (disc.count || 0) + ' 个标记, ' + (disc.ad_ranges?.length || 0) + ' 个广告范围</div>';
                const seq = details.sequence || {};
                html += '<div>🔢 序列号异常: ' + Object.keys(seq).length + ' 个</div>';
                html += '</div></div>';
            }

            // 广告簇列表
            if (pro.ad_clusters && pro.ad_clusters.length > 0) {
                html += '<div style="font-weight:600;color:#303133;margin-bottom:8px">📦 广告簇详情</div>';
                pro.ad_clusters.forEach((cluster, i) => {
                    const posColor = cluster.position_type === 'opening' ? '#e6a23c' :
                                     cluster.position_type === 'ending' ? '#909399' : '#f56c6c';
                    html += '<div style="padding:12px;background:#fef0f0;border-radius:8px;margin-bottom:8px;border-left:4px solid ' + posColor + '">';
                    html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;flex-wrap:wrap;gap:6px">';
                    html += '<span style="font-weight:600">广告簇 #' + (i + 1) + ' · ' + (cluster.position || '未知') + '</span>';
                    html += '<div style="display:flex;gap:6px">';
                    html += '<span class="badge badge-danger">' + cluster.segment_count + ' 片段</span>';
                    html += '<span class="badge badge-warning">评分 ' + (cluster.avg_score || 0) + '</span>';
                    html += '<span class="badge badge-info">' + (cluster.total_duration || 0).toFixed(1) + 's</span>';
                    html += '</div></div>';
                    html += '<div style="font-size:12px;color:#606266">片段 #' + cluster.start + ' - #' + cluster.end + '，平均时长 ' + (cluster.avg_duration || 0) + 's</div>';
                    if (cluster.reasons && cluster.reasons.length > 0) {
                        html += '<div style="font-size:11px;color:#909399;margin-top:4px">原因: ' + cluster.reasons.join(', ') + '</div>';
                    }
                    html += '</div>';
                });
            }

            container.innerHTML = html;
        }

        async function aiSkipVideo() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'AI处理中...';
            document.getElementById('aiSkipResult').style.display = 'none';
            try {
                const safeguard = document.getElementById('aiSkipSafeguard').checked;
                const autoLearn = document.getElementById('aiSkipAutoLearn').checked;
                const deepAnalysis = document.getElementById('aiSkipDeepAnalysis').checked;
                
                const params = new URLSearchParams({
                    action: 'ai/skip',
                    url: url,
                    safeguard: safeguard ? '1' : '0',
                    auto_learn: autoLearn ? '1' : '0',
                    deep_analysis: deepAnalysis ? '1' : '0'
                });
                
                const res = await fetch(API_BASE + '?' + params.toString());
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message || '处理失败');
                currentAiSkipData = data;
                renderAiSkipResult(data);
                document.getElementById('aiSkipResult').style.display = 'block';
                saveToHistory(url, 'ai_skip', data);
                showToast('AI去广告完成', 'success');
            } catch (e) {
                showToast('处理失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '🚀 AI 去广告';
            }
        }

        function renderAiSkipResult(data) {
            const url = document.getElementById('aiSkipUrl').value.trim();
            const outputUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            document.getElementById('aiSkipOutputUrl').textContent = outputUrl;
            
            const stats = data.data?.stats || data.stats || {};
            const statsHtml = `
                <div class="stat-card">
                    <div class="stat-value" style="color:#667eea">${stats.totalSegments || stats.total_segments || 0}</div>
                    <div class="stat-label">总片段数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#f56c6c">${stats.adSegments || stats.ad_segments || 0}</div>
                    <div class="stat-label">广告片段</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#67c23a">${stats.keptSegments || stats.kept_segments || 0}</div>
                    <div class="stat-label">保留片段</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#e6a23c">${(stats.adPercentage || stats.ad_percentage || 0).toFixed?.(1) || 0}%</div>
                    <div class="stat-label">广告占比</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#409eff">${(stats.savedDuration || stats.saved_duration || 0).toFixed?.(1) || 0}s</div>
                    <div class="stat-label">节省时长</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#909399">${data.data?.processTime || data.process_time || '0'}ms</div>
                    <div class="stat-label">处理耗时</div>
                </div>
            `;
            document.getElementById('aiSkipStats').innerHTML = statsHtml;
            
            renderAiSkipAdClusters(data);
            renderAiSkipGeneratedRules(data);
            renderAiSkipSegmentList();
        }

        function renderAiSkipAdClusters(data) {
            const container = document.getElementById('aiSkipAdClusters');
            const adClusters = data.data?.adClusters || data.data?.ad_clusters || [];
            
            if (adClusters.length === 0) {
                container.innerHTML = '<div style="text-align:center;color:#67c23a;padding:20px">✅ 未检测到明显的广告簇</div>';
                return;
            }

            let html = '<div style="margin-bottom:12px;font-size:13px;color:#606266">共检测到 <b style="color:#f56c6c">' + adClusters.length + '</b> 个广告片段集群</div>';
            html += adClusters.map((cluster, i) => {
                const positionMap = {
                    'opening': '片头',
                    'ending': '片尾',
                    'middle': '中间',
                    'unknown': '未知位置'
                };
                const posText = positionMap[cluster.position] || cluster.position || cluster.position_label || '未知';
                const posColor = cluster.position === 'opening' || cluster.position_type === 'opening' ? '#e6a23c' : 
                                 cluster.position === 'ending' || cluster.position_type === 'ending' ? '#909399' : 
                                 '#f56c6c';

                const confidence = cluster.confidence ?? 70;
                const confColor = confidence >= 90 ? '#67c23a' : confidence >= 75 ? '#e6a23c' : confidence >= 60 ? '#f56c6c' : '#909399';

                let discInfo = '';
                if (cluster.has_discontinuity || cluster.discontinuity_count > 0) {
                    discInfo = `<div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="badge badge-danger">DISCONTINUITY ×${cluster.discontinuity_count || 1}</span>
                        ${cluster.discontinuity_positions && cluster.discontinuity_positions.length > 0 
                            ? '<span class="badge badge-info">位置: #' + cluster.discontinuity_positions.join(',#') + '</span>' 
                            : ''}
                    </div>`;
                }

                let segmentList = '';
                if (cluster.segments && cluster.segments.length > 0) {
                    segmentList = '<div style="margin-top:8px;padding-top:8px;border-top:1px dashed #ebeef5">';
                    segmentList += '<div style="font-size:11px;color:#909399;margin-bottom:4px">片段详情:</div>';
                    segmentList += '<div style="display:flex;flex-wrap:wrap;gap:4px">';
                    cluster.segments.forEach((seg, si) => {
                        const discMark = seg.discontinuity ? '<span style="color:#f56c6c;margin-left:2px">🔀</span>' : '';
                        segmentList += `<span style="font-size:11px;background:#fff;padding:2px 6px;border-radius:4px;border:1px solid #e4e7ed;color:#606266">
                            #${seg.index} ${seg.duration}s${discMark}
                        </span>`;
                    });
                    segmentList += '</div></div>';
                }

                return `
                    <div style="padding:14px;background:#fef0f0;border-radius:10px;margin-bottom:10px;border-left:4px solid ${posColor}">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px">
                            <div style="font-weight:600;color:#303133">第 ${i + 1} 个广告簇 · ${posText}</div>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <span class="badge badge-danger">${cluster.segment_count || cluster.count || 0} 个片段</span>
                                <span class="badge badge-warning">${(cluster.total_duration || cluster.duration || 0).toFixed?.(1) || 0}s</span>
                                <span class="badge" style="background:${confColor};color:white">置信度 ${confidence}%</span>
                            </div>
                        </div>
                        <div style="font-size:12px;color:#606266;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:6px">
                            <div>📊 平均时长: ${(cluster.avg_duration || cluster.avg_segment_duration || 0).toFixed?.(2) || 0}s</div>
                            <div>📍 起始索引: #${cluster.start_index ?? cluster.start ?? '-'}</div>
                            <div>📍 结束索引: #${cluster.end_index ?? cluster.end ?? '-'}</div>
                            ${discInfo}
                        </div>
                        ${segmentList}
                    </div>
                `;
            }).join('');
            container.innerHTML = html;
        }

        let genRuleTab = 'discontinuity';

        function switchGenRuleTab(el, tab) {
            genRuleTab = tab;
            document.querySelectorAll('#aiSkipGeneratedRules .tab-item').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            renderGenRuleContent();
        }

        function renderAiSkipGeneratedRules(data) {
            const container = document.getElementById('aiSkipGeneratedRules');
            const regexRules = data.data?.discontinuityRegexRules || data.data?.discontinuity_regex_rules || [];
            
            if (regexRules.length === 0) {
                container.style.display = 'none';
                return;
            }
            
            container.style.display = 'block';
            currentGenRuleData = data;
            renderGenRuleContent();
        }

        let currentGenRuleData = null;

        function renderGenRuleContent() {
            const container = document.getElementById('genRuleContent');
            const data = currentGenRuleData;
            if (!data) return;

            const regexRules = data.data?.discontinuityRegexRules || data.data?.discontinuity_regex_rules || [];
            const rules = data.data?.rules || [];

            if (genRuleTab === 'discontinuity') {
                if (regexRules.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂未生成 DISCONTINUITY 正则规则</div>';
                    return;
                }
                container.innerHTML = regexRules.map((rule, i) => {
                    const conf = rule.confidence || 80;
                    const confColor = conf === 100 ? '#67c23a' : conf >= 95 ? '#54a0ff' : conf >= 90 ? '#e6a23c' : conf >= 80 ? '#f56c6c' : '#909399';
                    const confLabel = conf === 100 ? '极高' : conf >= 95 ? '很高' : conf >= 90 ? '高' : conf >= 80 ? '中' : '低';

                    let extraInfo = '';
                    if (rule.exact_duration) {
                        extraInfo += `<div style="font-size:11px;color:#54a0ff;margin-top:4px">🎯 精确时长: ${rule.exact_duration}秒（数据来源: ${rule.duration_sources}个片段）</div>`;
                    }
                    if (rule.uniform_duration) {
                        extraInfo += `<div style="font-size:11px;color:#54a0ff;margin-top:4px">📊 统一时长: ${rule.uniform_duration}秒</div>`;
                    }
                    if (rule.expected_count) {
                        extraInfo += `<div style="font-size:11px;color:#54a0ff;margin-top:4px">🔢 预期片段数: ${rule.expected_count}个</div>`;
                    }
                    if (rule.discontinuity_pair_count) {
                        extraInfo += `<div style="font-size:11px;color:#54a0ff;margin-top:4px">🔀 DISCONTINUITY 对: ${rule.discontinuity_pair_count}组</div>`;
                    }

                    return `
                    <div style="padding:14px;background:#f5f7fa;border-radius:10px;margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px">
                            <div style="font-weight:600;color:#303133">${rule.name || '规则 ' + (i + 1)}</div>
                            <div style="display:flex;gap:6px">
                                <span class="badge" style="background:${confColor};color:white;font-weight:bold">${confLabel} ${conf}%</span>
                            </div>
                        </div>
                        <div style="font-size:12px;color:#606266;margin-bottom:8px">${rule.description || ''}</div>
                        <div style="background:#fff;padding:10px 12px;border-radius:6px;border:1px solid #e4e7ed;margin-bottom:8px">
                            <div style="font-size:12px;color:#909399;margin-bottom:4px">正则表达式</div>
                            <code style="font-family:monospace;font-size:12px;color:#f56c6c;word-break:break-all;display:block">${escapeHtml(rule.pattern || '')}</code>
                        </div>
                        ${rule.example ? `<div style="font-size:11px;color:#909399;margin-bottom:8px">示例: ${escapeHtml(rule.example)}</div>` : ''}
                        ${extraInfo}
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-secondary" style="font-size:12px;padding:4px 10px" onclick="copyText('${rule.pattern ? rule.pattern.replace(/'/g, "\\'") : ''}')">📋 复制正则</button>
                            <button class="btn btn-success" style="font-size:12px;padding:4px 10px" onclick="saveGeneratedRule('regex', ${i})">💾 保存规则</button>
                        </div>
                    </div>
                `}).join('');
            } else if (genRuleTab === 'duration') {
                const durationRules = rules.filter(r => r.category === 'duration' || r.type === 'duration');
                if (durationRules.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂未生成时长规则</div>';
                    return;
                }
                container.innerHTML = durationRules.map((rule, i) => {
                    const conf = rule.confidence || 75;
                    const confColor = conf >= 90 ? '#67c23a' : conf >= 75 ? '#e6a23c' : '#f56c6c';
                    return `
                    <div style="padding:14px;background:#f5f7fa;border-radius:10px;margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                            <div style="font-weight:600;color:#303133">${rule.name || '时长规则 ' + (i + 1)}</div>
                            <span class="badge" style="background:${confColor};color:white">${conf}%</span>
                        </div>
                        <div style="font-size:12px;color:#606266">${rule.description || rule.reason || ''}</div>
                        <div style="font-size:11px;color:#909399;margin-top:4px">
                            ${rule.operator ? `条件: 时长 ${rule.operator} ${rule.threshold}秒` : ''}
                            ${rule.weight ? ` · 权重: ${rule.weight}` : ''}
                        </div>
                    </div>
                `}).join('');
            } else if (genRuleTab === 'sequence') {
                const seqRules = rules.filter(r => r.category === 'sequence' || r.type === 'sequence_jump');
                if (seqRules.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂未生成序列号规则</div>';
                    return;
                }
                container.innerHTML = seqRules.map((rule, i) => {
                    const conf = rule.confidence || 80;
                    const confColor = conf >= 90 ? '#67c23a' : conf >= 75 ? '#e6a23c' : '#f56c6c';
                    const dirText = rule.direction === 'forward' ? '向前跳跃' : '向后跳跃';
                    return `
                    <div style="padding:14px;background:#f5f7fa;border-radius:10px;margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                            <div style="font-weight:600;color:#303133">${rule.name || '序列号规则 ' + (i + 1)}</div>
                            <span class="badge" style="background:${confColor};color:white">${conf}%</span>
                        </div>
                        <div style="font-size:12px;color:#606266">${rule.description || rule.reason || ''}</div>
                        <div style="font-size:11px;color:#909399;margin-top:4px">
                            方向: ${dirText} · 阈值: ${rule.threshold || '100000'} · 权重: ${rule.weight || 90}
                        </div>
                    </div>
                `}).join('');
            } else if (genRuleTab === 'filename') {
                const nameRules = rules.filter(r => r.category === 'filename' || r.type === 'filename' || r.type === 'pattern');
                if (nameRules.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂未生成文件名规则</div>';
                    return;
                }
                container.innerHTML = nameRules.map((rule, i) => {
                    const conf = rule.confidence || 80;
                    const confColor = conf >= 90 ? '#67c23a' : conf >= 75 ? '#e6a23c' : '#f56c6c';
                    return `
                    <div style="padding:14px;background:#f5f7fa;border-radius:10px;margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                            <div style="font-weight:600;color:#303133">${rule.name || '文件名规则 ' + (i + 1)}</div>
                            <span class="badge" style="background:${confColor};color:white">${conf}%</span>
                        </div>
                        <div style="font-size:12px;color:#606266">${rule.description || rule.reason || ''}</div>
                        ${rule.pattern ? `
                        <div style="background:#fff;padding:8px 10px;border-radius:6px;border:1px solid #e4e7ed;margin-top:6px">
                            <div style="font-size:11px;color:#909399;margin-bottom:2px">匹配模式</div>
                            <code style="font-family:monospace;font-size:11px;color:#54a0ff">${escapeHtml(rule.pattern)}</code>
                        </div>
                        ` : ''}
                        ${rule.weight ? `<div style="font-size:11px;color:#909399;margin-top:4px">权重: ${rule.weight}</div>` : ''}
                    </div>
                `}).join('');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function saveGeneratedRule(type, index) {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            showToast('正在保存规则...', 'info');
            fetch(API_BASE + '?action=rules/learn&url=' + encodeURIComponent(url))
                .then(res => res.json())
                .then(data => {
                    if (data.success && !data.skipped) {
                        showToast('规则已保存到规则库（学习次数: ' + (data.learn_count || 1) + '）', 'success');
                    } else if (data.skipped) {
                        showToast('保存跳过: ' + (data.reason || '未知原因'), 'warning');
                    } else {
                        showToast('保存失败: ' + (data.message || data.reason || '未知错误'), 'error');
                    }
                })
                .catch(e => showToast('保存失败: ' + e.message, 'error'));
        }

        function switchAiSkipTab(el, tab) {
            aiSkipSegmentTab = tab;
            document.querySelectorAll('#page-ai_skip .tab-item').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            renderAiSkipSegmentList();
        }

        function renderAiSkipSegmentList() {
            const data = currentAiSkipData;
            if (!data) return;
            const container = document.getElementById('aiSkipSegmentList');
            const adSegments = data.data?.adSegments || data.data?.ad_segments || [];
            const contentSegments = data.data?.contentSegments || data.data?.content_segments || [];
            
            if (aiSkipSegmentTab === 'ad') {
                if (adSegments.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#67c23a;padding:30px">✅ 未检测到广告片段</div>';
                    return;
                }
                container.innerHTML = adSegments.slice(0, 50).map((seg, i) => `
                    <div class="segment-item">
                        <div class="segment-index">#${i + 1}</div>
                        <div class="segment-info">
                            <div class="segment-name">${seg.uri || seg.url || 'ad_' + i + '.ts'}</div>
                            <div class="segment-meta">
                                <span>时长: ${(seg.duration || 0).toFixed(3)}s</span>
                                <span>序号: ${seg.mediaSequence ?? seg.sequence ?? '-'}</span>
                            </div>
                        </div>
                        <div class="segment-badges">
                            ${seg.isAd ? '<span class="badge badge-danger">广告</span>' : ''}
                        </div>
                    </div>
                `).join('');
                if (adSegments.length > 50) {
                    container.innerHTML += `<div style="text-align:center;color:#909399;padding:12px;font-size:12px">仅显示前 50 条，共 ${adSegments.length} 条</div>`;
                }
            } else if (aiSkipSegmentTab === 'content') {
                if (contentSegments.length === 0) {
                    container.innerHTML = '<div style="text-align:center;color:#f56c6c;padding:30px">⚠️ 未找到内容片段</div>';
                    return;
                }
                container.innerHTML = contentSegments.slice(0, 50).map((seg, i) => `
                    <div class="segment-item">
                        <div class="segment-index">#${i + 1}</div>
                        <div class="segment-info">
                            <div class="segment-name">${seg.uri || seg.url || 'content_' + i + '.ts'}</div>
                            <div class="segment-meta">
                                <span>时长: ${(seg.duration || 0).toFixed(3)}s</span>
                                <span>序号: ${seg.mediaSequence ?? seg.sequence ?? '-'}</span>
                            </div>
                        </div>
                        <div class="segment-badges">
                            <span class="badge badge-success">内容</span>
                        </div>
                    </div>
                `).join('');
                if (contentSegments.length > 50) {
                    container.innerHTML += `<div style="text-align:center;color:#909399;padding:12px;font-size:12px">仅显示前 50 条，共 ${contentSegments.length} 条</div>`;
                }
            } else if (aiSkipSegmentTab === 'md5') {
                const md5Data = currentMd5Data;
                if (!md5Data) {
                    container.innerHTML = '<div style="text-align:center;color:#909399;padding:30px">点击「🔬 MD5分析」按钮开始分析</div>';
                    return;
                }
                
                const adCandidates = md5Data.ad_candidates || [];
                const contentCandidates = md5Data.content_candidates || [];
                const md5Details = md5Data.md5_details || [];
                
                let md5Html = '<div style="margin-bottom:16px">';
                md5Html += '<div style="font-weight:600;color:#303133;margin-bottom:12px">🎯 广告候选MD5特征码</div>';
                if (adCandidates.length === 0) {
                    md5Html += '<div style="color:#67c23a;padding:12px;background:#f0f9eb;border-radius:6px">未检测到重复的广告候选MD5</div>';
                } else {
                    md5Html += adCandidates.map((cand, i) => `
                        <div style="padding:12px;background:#fef0f0;border-radius:8px;margin-bottom:8px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px">
                                <div style="font-family:monospace;font-size:12px;color:#f56c6c;word-break:break-all">#${i + 1} ${cand.md5}</div>
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <span class="badge badge-danger">重复${cand.count}次</span>
                                    <span class="badge badge-warning">${cand.avg_duration}s/片</span>
                                    <span class="badge badge-info">共${cand.total_duration}s</span>
                                </div>
                            </div>
                            <div style="font-size:12px;color:#606266">
                                出现位置: ${cand.segments?.map(s => '#' + (s.index + 1)).join(', ') || '-'}
                            </div>
                        </div>
                    `).join('');
                }
                md5Html += '</div>';
                
                md5Html += '<div style="margin-bottom:16px">';
                md5Html += '<div style="font-weight:600;color:#303133;margin-bottom:12px">✅ 内容候选MD5特征码</div>';
                if (contentCandidates.length === 0) {
                    md5Html += '<div style="color:#909399;padding:12px;background:#f5f7fa;border-radius:6px">未检测到内容候选</div>';
                } else {
                    md5Html += '<div style="display:flex;gap:6px;flex-wrap:wrap">';
                    md5Html += contentCandidates.slice(0, 20).map((cand, i) => `
                        <div style="padding:6px 10px;background:#f0f9eb;border-radius:4px;font-family:monospace;font-size:11px;color:#67c23a">
                            ${cand.md5.substring(0, 16)}... (${cand.count}次)
                        </div>
                    `).join('');
                    md5Html += '</div>';
                    if (contentCandidates.length > 20) {
                        md5Html += `<div style="margin-top:8px;font-size:12px;color:#909399">仅显示前20个，共${contentCandidates.length}个</div>`;
                    }
                }
                md5Html += '</div>';
                
                md5Html += '<div>';
                md5Html += '<div style="font-weight:600;color:#303133;margin-bottom:12px">📋 片段MD5详情</div>';
                md5Html += '<div style="max-height:300px;overflow-y:auto">';
                md5Html += md5Details.slice(0, 50).map((detail, i) => {
                    const isAd = adCandidates.some(c => c.md5 === detail.md5);
                    return `
                        <div class="segment-item">
                            <div class="segment-index">#${detail.index + 1}</div>
                            <div class="segment-info">
                                <div class="segment-name" style="font-family:monospace;font-size:12px">${detail.md5 || '计算失败'}</div>
                                <div class="segment-meta">
                                    <span>时长: ${(detail.duration || 0).toFixed(3)}s</span>
                                    <span>${detail.uri?.substring?.(0, 30) || ''}${detail.uri?.length > 30 ? '...' : ''}</span>
                                </div>
                            </div>
                            <div class="segment-badges">
                                ${isAd ? '<span class="badge badge-danger">广告候选</span>' : '<span class="badge badge-success">内容</span>'}
                            </div>
                        </div>
                    `;
                }).join('');
                md5Html += '</div>';
                if (md5Details.length > 50) {
                    md5Html += `<div style="text-align:center;color:#909399;padding:12px;font-size:12px">仅显示前 50 条，共 ${md5Details.length} 条</div>`;
                }
                md5Html += '</div>';
                
                container.innerHTML = md5Html;
            } else {
                const analysis = data.data?.analysis || data.analysis || {};
                const methods = data.data?.methods || data.methods || [];
                let detailHtml = '<div style="font-size:13px;color:#606266">';
                detailHtml += '<div style="margin-bottom:12px;font-weight:600;color:#303133">AI识别方式：</div>';
                detailHtml += '<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">';
                detailHtml += '<span class="badge badge-primary">时长检测</span>';
                detailHtml += '<span class="badge badge-warning">不连续标记</span>';
                detailHtml += '<span class="badge badge-danger">序列号跳跃</span>';
                detailHtml += '<span class="badge badge-info">文件名模式</span>';
                detailHtml += '</div>';
                
                if (analysis.durationStats) {
                    detailHtml += '<div style="margin-bottom:12px"><strong>时长统计：</strong></div>';
                    detailHtml += `<div>平均时长: ${analysis.durationStats.avg?.toFixed(3) || 0}s</div>`;
                    detailHtml += `<div>最小时长: ${analysis.durationStats.min?.toFixed(3) || 0}s</div>`;
                    detailHtml += `<div>最大时长: ${analysis.durationStats.max?.toFixed(3) || 0}s</div>`;
                }
                
                detailHtml += '</div>';
                container.innerHTML = detailHtml;
            }
        }

        function playAiSkipVideo() {
            const url = document.getElementById('aiSkipOutputUrl').textContent;
            if (!url) return;
            document.getElementById('aiSkipPlayerContainer').style.display = 'block';
            document.getElementById('aiSkipPlayStatus').textContent = '正在加载视频...';
            if (window.dplayerScriptLoaded) {
                initAiSkipPlayer(url);
            } else {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/dplayer/dist/DPlayer.min.js';
                script.onload = () => {
                    window.dplayerScriptLoaded = true;
                    initAiSkipPlayer(url);
                };
                document.head.appendChild(script);
            }
        }

        function initAiSkipPlayer(url) {
            const container = document.getElementById('aiSkipVideoPlayer');
            const statusEl = document.getElementById('aiSkipPlayStatus');
            container.innerHTML = '';
            
            let firstFrameReady = false;
            let playAttempted = false;
            let posterGenerated = false;
            let hls = null;

            const generatePoster = function(video) {
                if (posterGenerated || !video) return;
                if (video.readyState >= 2 && video.videoWidth > 0) {
                    try {
                        const canvas = document.createElement('canvas');
                        canvas.width = 640;
                        canvas.height = 360;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, 640, 360);
                        const posterUrl = canvas.toDataURL('image/jpeg', 0.8);
                        posterGenerated = true;
                        
                        const posterImg = container.querySelector('.dplayer-video-wrap .dplayer-poster');
                        if (posterImg) {
                            posterImg.style.backgroundImage = 'url(' + posterUrl + ')';
                            posterImg.style.backgroundSize = 'cover';
                            posterImg.style.backgroundPosition = 'center';
                        }
                    } catch(e) {
                        console.warn('生成海报图失败:', e);
                    }
                }
            };

            const tryPlay = function(video) {
                if (playAttempted) return;
                if (firstFrameReady && video && video.paused) {
                    playAttempted = true;
                    video.play().catch(function(e) {
                        console.warn('自动播放被阻止:', e);
                        if (statusEl) statusEl.textContent = '点击播放按钮开始播放';
                    });
                }
            };

            const hlsConfig = {
                enableWorker: true,
                lowLatencyMode: false,
                maxBufferLength: 60,
                maxMaxBufferLength: 900,
                minBufferLength: 5,
                maxBufferSize: 120 * 1000 * 1000,
                maxBufferHole: 0.3,
                highBufferWatchdogPeriod: 0.5,
                startLevel: -1,
                capLevelToPlayerSize: true,
                liveSyncDurationCount: 3,
                liveMaxLatencyDurationCount: 10,
                fragLoadingTimeOut: 30000,
                manifestLoadingTimeOut: 20000,
                levelLoadingTimeOut: 15000,
                backBufferLength: 60,
                startFragPrefetch: true,
                enableSoftwareAES: true,
                abrEwmaDefaultEstimate: 2000000,
                abrBandWidthFactor: 0.75,
                abrEwmaFastLive: 3.0,
                abrEwmaSlowLive: 9.0,
                maxFragLookUpTolerance: 0.2,
                enableCEA708Captions: false,
                enableWebVTT: false,
                enableIMSC1: false,
                renderTextTracksNatively: false,
                xhrSetup: function(xhr) {
                    xhr.withCredentials = false;
                    xhr.timeout = 30000;
                }
            };

            const aiSkipDp = new DPlayer({
                container: container,
                video: {
                    url: url,
                    type: 'customHls',
                    customType: {
                        customHls: function(video, dp) {
                            if (Hls.isSupported()) {
                                hls = new Hls(hlsConfig);
                                hls.loadSource(url);
                                hls.attachMedia(video);
                                dp.hls = hls;

                                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                                    if (statusEl) statusEl.textContent = '视频解析完成，缓冲中...';
                                });

                                hls.on(Hls.Events.FRAG_LOADED, function() {
                                    firstFrameReady = true;
                                    generatePoster(video);
                                    if (!playAttempted && statusEl) {
                                        statusEl.textContent = '首帧加载完成';
                                    }
                                    tryPlay(video);
                                });

                                hls.on(Hls.Events.FRAG_BUFFERED, function() {
                                    generatePoster(video);
                                });

                                hls.on(Hls.Events.ERROR, function(event, data) {
                                    console.error('HLS 错误:', data.type, data.details);
                                    if (data.fatal) {
                                        switch (data.type) {
                                            case Hls.ErrorTypes.NETWORK_ERROR:
                                                if (statusEl) statusEl.textContent = '网络错误，正在恢复...';
                                                try { hls.startLoad(); } catch(e) {}
                                                break;
                                            case Hls.ErrorTypes.MEDIA_ERROR:
                                                if (statusEl) statusEl.textContent = '媒体错误，正在恢复...';
                                                try { hls.recoverMediaError(); } catch(e) {}
                                                break;
                                            default:
                                                if (statusEl) statusEl.textContent = '视频加载失败';
                                        }
                                    }
                                });

                                video.addEventListener('loadedmetadata', function() {
                                    generatePoster(video);
                                });

                                video.addEventListener('canplay', function() {
                                    generatePoster(video);
                                });

                                video.addEventListener('playing', function() {
                                    if (statusEl) statusEl.textContent = '正在播放';
                                });

                                video.addEventListener('waiting', function() {
                                    if (statusEl) statusEl.textContent = '缓冲中...';
                                });

                                video.addEventListener('ended', function() {
                                    if (statusEl) statusEl.textContent = '播放结束';
                                });
                            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                video.src = url;
                                video.addEventListener('loadedmetadata', function() {
                                    generatePoster(video);
                                    firstFrameReady = true;
                                    tryPlay(video);
                                });
                            }
                        }
                    }
                },
                autoplay: false,
                theme: '#667eea',
                lang: 'zh-cn',
                screenshot: true,
                hotkey: true,
                preload: 'auto',
                volume: 0.7,
                mutex: true,
                playbackSpeed: [0.5, 0.75, 1, 1.25, 1.5, 2],
                danmaku: {
                    id: 'ai_skip_player_' + Date.now(),
                    api: 'https://api.prprpr.me/dplayer/',
                    user: '游客'
                }
            });

            aiSkipDp.on('play', function() {
                if (statusEl) statusEl.textContent = '正在播放';
            });

            aiSkipDp.on('pause', function() {
                if (statusEl) statusEl.textContent = '已暂停';
            });

            aiSkipDp.on('loadedmetadata', function() {
                generatePoster(aiSkipDp.video);
            });
        }

        function downloadAiSkipM3u8() {
            const url = document.getElementById('aiSkipOutputUrl').textContent;
            if (!url) return;
            window.open(url, '_blank');
        }

        function aiSkipGenerateRules() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            showToast('正在生成规则...', 'info');
            fetch(API_BASE + '?action=rules/generate&url=' + encodeURIComponent(url))
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showToast('规则生成成功，共 ' + (data.ruleCount || data.data?.ruleCount || 0) + ' 条规则', 'success');
                        currentGenRuleData = { data: data };
                        document.getElementById('aiSkipGeneratedRules').style.display = 'block';
                        renderGenRuleContent();
                    } else {
                        showToast('生成失败: ' + data.message, 'error');
                    }
                })
                .catch(e => showToast('生成失败: ' + e.message, 'error'));
        }

        function aiSkipToInsert() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            document.getElementById('aiInsertUrl').value = url;
            document.querySelector('.nav-item[data-page="ai_insert"]').click();
        }

        function aiSkipToWatermark() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            document.getElementById('aiWatermarkUrl').value = url;
            document.querySelector('.nav-item[data-page="ai_watermark"]').click();
        }

        let currentMd5Data = null;

        async function aiMd5Analyze() {
            const url = document.getElementById('aiSkipUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const saveMd5 = document.getElementById('aiSkipSaveMd5').checked;
            const fastMode = document.getElementById('aiSkipFastMode').checked;
            
            showToast('正在进行MD5特征码分析' + (fastMode ? '（极速模式）' : '') + '，请稍候...', 'info');
            document.getElementById('aiSkipResult').style.display = 'block';
            document.getElementById('aiSkipStats').innerHTML = `
                <div class="stat-card">
                    <div class="stat-value" style="color:#e6a23c">⏳</div>
                    <div class="stat-label">分析中...</div>
                </div>
            `;
            aiSkipSegmentTab = 'md5';
            document.querySelectorAll('#page-ai_skip .tab-item').forEach((t, i) => {
                t.classList.toggle('active', i === 2);
            });
            document.getElementById('aiSkipSegmentList').innerHTML = `
                <div style="text-align:center;color:#909399;padding:40px">
                    <div style="font-size:32px;margin-bottom:16px">🔬</div>
                    <div style="font-size:14px;margin-bottom:8px">极速MD5分析中，通常 2-5 秒...</div>
                    <div style="font-size:12px;color:#c0c4cc">并发下载 + 智能采样 + 仅下载文件头</div>
                </div>
            `;
            
            try {
                const params = new URLSearchParams({
                    action: 'ai/md5_analyze',
                    url: url,
                    save: saveMd5 ? '1' : '0',
                    fast: fastMode ? '1' : '0'
                });
                
                const res = await fetch(API_BASE + '?' + params.toString());
                const data = await res.json();
                
                if (!data.success) throw new Error(data.message || '分析失败');
                currentMd5Data = data.data;
                
                renderMd5Stats(data.data);
                renderAiSkipSegmentList();
                
                showToast('MD5特征码分析完成，耗时 ' + data.data.process_time, 'success');
            } catch (e) {
                document.getElementById('aiSkipSegmentList').innerHTML = '<div style="color:#f56c6c;padding:20px;text-align:center">分析失败: ' + e.message + '</div>';
                showToast('分析失败: ' + e.message, 'error');
            }
        }

        function renderMd5Stats(data) {
            const statsHtml = `
                <div class="stat-card">
                    <div class="stat-value" style="color:#667eea">${data.total_segments || 0}</div>
                    <div class="stat-label">总片段数</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#67c23a">${data.analyzed_segments || 0}</div>
                    <div class="stat-label">已分析</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#409eff">${data.unique_md5 || 0}</div>
                    <div class="stat-label">唯一MD5</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#f56c6c">${data.ad_candidate_count || 0}</div>
                    <div class="stat-label">广告候选</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#e6a23c">${data.content_candidate_count || 0}</div>
                    <div class="stat-label">内容候选</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#909399">${data.process_time || '0ms'}</div>
                    <div class="stat-label">分析耗时</div>
                </div>
            `;
            document.getElementById('aiSkipStats').innerHTML = statsHtml;
        }

        let currentAiInsertData = null;

        async function aiInsertDetect() {
            const url = document.getElementById('aiInsertUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '检测中...';
            document.getElementById('aiInsertResult').style.display = 'none';
            try {
                const opening = document.getElementById('aiInsertOpening').checked;
                const ending = document.getElementById('aiInsertEnding').checked;
                const middle = document.getElementById('aiInsertMiddle').checked;
                
                const params = new URLSearchParams({
                    action: 'ai/insert_detect',
                    url: url,
                    opening: opening ? '1' : '0',
                    ending: ending ? '1' : '0',
                    middle: middle ? '1' : '0'
                });
                
                const res = await fetch(API_BASE + '?' + params.toString());
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message || '检测失败');
                currentAiInsertData = data;
                renderAiInsertResult(data);
                document.getElementById('aiInsertResult').style.display = 'block';
                showToast('插播检测完成', 'success');
            } catch (e) {
                showToast('检测失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '🔍 检测插播';
            }
        }

        function renderAiInsertResult(data) {
            const insertions = data.data?.insertions || data.insertions || [];
            const totalInsertions = insertions.length;
            const totalDuration = insertions.reduce((sum, item) => sum + (item.duration || 0), 0);
            
            const statsHtml = `
                <div class="stat-card">
                    <div class="stat-value" style="color:#f56c6c">${totalInsertions}</div>
                    <div class="stat-label">插播数量</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#e6a23c">${totalDuration.toFixed(1)}s</div>
                    <div class="stat-label">插播总时长</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#67c23a">${data.data?.openingCount || 0}</div>
                    <div class="stat-label">片头插播</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#409eff">${data.data?.middleCount || 0}</div>
                    <div class="stat-label">中间插播</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color:#909399">${data.data?.endingCount || 0}</div>
                    <div class="stat-label">片尾插播</div>
                </div>
            `;
            document.getElementById('aiInsertStats').innerHTML = statsHtml;
            
            const listHtml = insertions.length === 0 
                ? '<div style="text-align:center;color:#67c23a;padding:30px">✅ 未检测到插播内容</div>'
                : insertions.map((item, i) => `
                    <div style="padding:12px;background:#f5f7fa;border-radius:8px;margin-bottom:8px">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                            <div style="font-weight:600;color:#303133">${item.type === 'opening' ? '片头插播' : item.type === 'ending' ? '片尾插播' : '第' + (i+1) + '处插播'}</div>
                            <span class="badge badge-danger">${(item.duration || 0).toFixed(1)}s</span>
                        </div>
                        <div style="font-size:12px;color:#606266">
                            位置: 第 ${item.startIndex || 0} - ${item.endIndex || 0} 片段
                            ${item.reason ? ' | 原因: ' + item.reason : ''}
                        </div>
                    </div>
                `).join('');
            document.getElementById('aiInsertList').innerHTML = listHtml;
        }

        function aiInsertSkip() {
            const url = document.getElementById('aiInsertUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const outputUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            document.getElementById('aiInsertOutputUrl').textContent = outputUrl;
            document.getElementById('aiInsertOutput').style.display = 'block';
            showToast('已生成纯净版链接', 'success');
        }

        function aiInsertToSkip() {
            const url = document.getElementById('aiInsertUrl').value.trim();
            document.getElementById('aiSkipUrl').value = url;
            document.querySelector('.nav-item[data-page="ai_skip"]').click();
        }

        async function aiInsertMd5Analyze() {
            const url = document.getElementById('aiInsertUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            const fastMode = document.getElementById('aiInsertFastMode').checked;
            
            showToast('正在进行MD5特征码分析' + (fastMode ? '（极速模式）' : '') + '，请稍候...', 'info');
            document.getElementById('aiInsertResult').style.display = 'block';
            document.getElementById('aiInsertMd5Card').style.display = 'block';
            document.getElementById('aiInsertMd5Content').innerHTML = `
                <div style="text-align:center;color:#909399;padding:30px">
                    <div style="font-size:24px;margin-bottom:12px">🔬</div>
                    <div>极速分析中，通常 2-5 秒...</div>
                    <div style="margin-top:8px;font-size:12px;color:#c0c4cc">并发下载 + 智能采样</div>
                </div>
            `;
            
            try {
                const params = new URLSearchParams({
                    action: 'ai/md5_analyze',
                    url: url,
                    fast: fastMode ? '1' : '0'
                });
                
                const res = await fetch(API_BASE + '?' + params.toString());
                const data = await res.json();
                
                if (!data.success) throw new Error(data.message || '分析失败');
                
                const md5Data = data.data;
                const adCandidates = md5Data.ad_candidates || [];
                
                let html = '<div style="margin-bottom:12px;display:flex;gap:16px;flex-wrap:wrap">';
                html += `<div><strong>分析片段:</strong> ${md5Data.analyzed_segments || 0}</div>`;
                html += `<div><strong>唯一MD5:</strong> ${md5Data.unique_md5 || 0}</div>`;
                html += `<div><strong>广告候选:</strong> <span style="color:#f56c6c">${md5Data.ad_candidate_count || 0}</span></div>`;
                html += '</div>';
                
                if (adCandidates.length === 0) {
                    html += '<div style="color:#67c23a;padding:12px;background:#f0f9eb;border-radius:6px">未检测到重复的广告候选MD5</div>';
                } else {
                    html += '<div style="max-height:300px;overflow-y:auto">';
                    adCandidates.forEach((cand, i) => {
                        html += `
                            <div style="padding:10px;background:#fef0f0;border-radius:6px;margin-bottom:6px">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:4px">
                                    <div style="font-family:monospace;font-size:11px;color:#f56c6c;word-break:break-all">#${i + 1} ${cand.md5}</div>
                                    <div style="display:flex;gap:4px">
                                        <span class="badge badge-danger">${cand.count}次</span>
                                        <span class="badge badge-warning">${cand.avg_duration}s</span>
                                    </div>
                                </div>
                                <div style="font-size:11px;color:#606266">
                                    位置: ${cand.segments?.map(s => '#' + (s.index + 1)).join(', ') || '-'}
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                }
                
                document.getElementById('aiInsertMd5Content').innerHTML = html;
                showToast('MD5特征码分析完成', 'success');
            } catch (e) {
                document.getElementById('aiInsertMd5Content').innerHTML = '<div style="color:#f56c6c;padding:12px">分析失败: ' + e.message + '</div>';
                showToast('分析失败: ' + e.message, 'error');
            }
        }

        function aiInsertToWatermark() {
            const url = document.getElementById('aiInsertUrl').value.trim();
            document.getElementById('aiWatermarkUrl').value = url;
            document.querySelector('.nav-item[data-page="ai_watermark"]').click();
        }

        const WATERMARK_PARAMS = [
            { name: 'wsip', desc: '水印IP参数' },
            { name: 'wsh' , desc: '水印哈希参数' },
            { name: 'wsTime', desc: '水印时间参数' },
            { name: 'sign', desc: '签名参数' },
            { name: 'wd', desc: '水印域名参数' },
            { name: 'hd', desc: '清晰度参数（非水印）' },
            { name: 'chyuan', desc: '来源参数' },
            { name: 'x-play', desc: '播放器参数' },
            { name: 'k_ft', desc: '防盗链参数' },
            { name: 'k_id', desc: '防盗链ID' },
        ];

        function aiWatermarkProcess() {
            const url = document.getElementById('aiWatermarkUrl').value.trim();
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            document.getElementById('aiWatermarkResult').style.display = 'block';
            document.getElementById('aiWatermarkOriginalUrl').textContent = url;
            
            let processedUrl = url;
            let removedParams = [];
            let addedParams = [];
            
            const removeParams = document.getElementById('aiWatermarkUrlParams').checked;
            const handleReferer = document.getElementById('aiWatermarkReferer').checked;
            
            if (removeParams) {
                try {
                    const urlObj = new URL(url);
                    const paramsToRemove = [];
                    WATERMARK_PARAMS.forEach(wm => {
                        if (urlObj.searchParams.has(wm.name)) {
                            paramsToRemove.push(wm.name + '=' + urlObj.searchParams.get(wm.name));
                            urlObj.searchParams.delete(wm.name);
                        }
                    });
                    removedParams = paramsToRemove;
                    processedUrl = urlObj.toString();
                } catch (e) {
                }
            }
            
            document.getElementById('aiWatermarkOutputUrl').textContent = processedUrl;
            
            let detailsHtml = '';
            if (removedParams.length > 0) {
                detailsHtml += `<div style="margin-bottom:12px"><div style="font-weight:600;color:#f56c6c;margin-bottom:6px">已去除的水印参数:</div>`;
                detailsHtml += `<div style="display:flex;gap:6px;flex-wrap:wrap">`;
                removedParams.forEach(p => {
                    detailsHtml += `<span class="badge badge-danger">${p.substring(0, 30)}${p.length > 30 ? '...' : ''}</span>`;
                });
                detailsHtml += `</div></div>`;
            } else {
                detailsHtml += `<div style="color:#67c23a;margin-bottom:12px">✅ 未检测到水印参数</div>`;
            }
            
            document.getElementById('aiWatermarkDetails').innerHTML = detailsHtml;
            
            const libHtml = WATERMARK_PARAMS.map(wm => `
                <div style="display:inline-block;background:#f5f7fa;padding:6px 12px;border-radius:6px;margin:4px;font-size:12px">
                    <strong>${wm.name}</strong> - ${wm.desc}
                </div>
            `).join('');
            document.getElementById('aiWatermarkLibList').innerHTML = libHtml;
            
            showToast('水印处理完成', 'success');
        }

        function aiWatermarkToSkip() {
            const url = document.getElementById('aiWatermarkOutputUrl').textContent || document.getElementById('aiWatermarkUrl').value.trim();
            document.getElementById('aiSkipUrl').value = url;
            document.querySelector('.nav-item[data-page="ai_skip"]').click();
        }

        function playVideo() {
            const playPageActive = document.querySelector('.nav-item[data-page="play"]')?.classList.contains('active');
            let url;
            if (playPageActive) {
                url = document.getElementById('playUrl')?.value?.trim();
            } else {
                url = document.getElementById('analyzeUrl')?.value?.trim() || document.getElementById('playUrl')?.value?.trim();
            }
            if (!url) { showToast('请输入视频链接', 'error'); return; }
            document.querySelector('.nav-item[data-page="play"]').click();
            document.getElementById('playUrl').value = url;
            const mxjxUrl = API_BASE + '?action=mxjx&url=' + encodeURIComponent(url);
            document.getElementById('playerContainer').style.display = 'block';
            document.getElementById('playInfo').innerHTML = `
                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px">
                    <span style="color:#606266">无广告链接:</span>
                    <code id="playMxjxUrl" style="background:#f5f7fa;padding:4px 8px;border-radius:4px;word-break:break-all;flex:1;min-width:200px;cursor:pointer" onclick="copyText('${mxjxUrl}')" title="点击复制">${mxjxUrl}</code>
                    <button class="btn btn-sm btn-secondary" onclick="copyText('${mxjxUrl}')">复制链接</button>
                    <button class="btn btn-sm btn-primary" onclick="window.open('${mxjxUrl}', '_blank')">新窗口打开</button>
                </div>
                <div id="playStatus" style="margin-top:8px;color:#909399;font-size:12px">正在加载视频...</div>
            `;

            if (dp) {
                try { dp.destroy(); } catch(e) {}
                dp = null;
            }

            if (typeof Hls === 'undefined') {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">错误: hls.js 加载失败，请检查网络或刷新页面</span>';
                showToast('hls.js 加载失败', 'error');
                return;
            }
            if (typeof DPlayer === 'undefined') {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">错误: DPlayer 加载失败，请检查网络或刷新页面</span>';
                showToast('DPlayer 加载失败', 'error');
                return;
            }

            try {
                const hlsConfig = {
                    enableWorker: true,
                    lowLatencyMode: false,
                    maxBufferLength: 60,
                    maxMaxBufferLength: 900,
                    minBufferLength: 5,
                    maxBufferSize: 120 * 1000 * 1000,
                    maxBufferHole: 0.3,
                    highBufferWatchdogPeriod: 0.5,
                    startLevel: -1,
                    capLevelToPlayerSize: true,
                    liveSyncDurationCount: 3,
                    liveMaxLatencyDurationCount: 10,
                    fragLoadingTimeOut: 30000,
                    manifestLoadingTimeOut: 20000,
                    levelLoadingTimeOut: 15000,
                    backBufferLength: 60,
                    startFragPrefetch: true,
                    enableSoftwareAES: true,
                    abrEwmaDefaultEstimate: 2000000,
                    abrBandWidthFactor: 0.75,
                    abrEwmaFastLive: 3.0,
                    abrEwmaSlowLive: 9.0,
                    maxFragLookUpTolerance: 0.2,
                    enableCEA708Captions: false,
                    enableWebVTT: false,
                    enableIMSC1: false,
                    renderTextTracksNatively: false,
                    xhrSetup: function(xhr) {
                        xhr.withCredentials = false;
                        xhr.timeout = 30000;
                    }
                };

                let firstFrameReady = false;
                let playAttempted = false;

                const tryPlay = function() {
                    if (playAttempted) return;
                    if (firstFrameReady && dp && dp.video && dp.video.paused) {
                        playAttempted = true;
                        dp.video.play().catch(function(e) {
                            console.warn('自动播放被阻止:', e);
                            document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频已加载，点击播放按钮开始播放</span>';
                            showToast('点击播放按钮开始播放', 'warning');
                        });
                    }
                };

                const containerEl = document.getElementById('dplayer') || document.getElementById('videoPlayer');
                dp = new DPlayer({
                    container: containerEl,
                    video: {
                        url: mxjxUrl,
                        type: 'customHls',
                        customType: {
                            customHls: function(video, player) {
                                if (Hls.isSupported()) {
                                    const hls = new Hls(hlsConfig);
                                    hls.loadSource(video.src);
                                    hls.attachMedia(video);
                                    player.hls = hls;

                                    hls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                                        console.log('HLS 清单解析完成, 共', data.levels.length, '个清晰度');
                                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">视频解析完成，正在缓冲首帧...</span>';
                                    });

                                    hls.on(Hls.Events.FRAG_LOADED, function(event, data) {
                                        console.log('片段加载完成, 索引:', data.frag.sn, '时长:', data.frag.duration.toFixed(2) + 's');
                                        firstFrameReady = true;
                                        if (!playAttempted) {
                                            document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">首帧加载完成，即将播放...</span>';
                                        }
                                        tryPlay();
                                    });

                                    hls.on(Hls.Events.LEVEL_SWITCHED, function(event, data) {
                                        console.log('清晰度切换到:', data.level);
                                    });

                                    hls.on(Hls.Events.ERROR, function(event, data) {
                                        console.error('HLS 错误:', data.type, data.details, data.fatal ? '(致命)' : '');
                                        if (data.fatal) {
                                            switch (data.type) {
                                                case Hls.ErrorTypes.NETWORK_ERROR:
                                                    document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">网络错误，正在尝试恢复...</span>';
                                                    try {
                                                        hls.startLoad();
                                                    } catch(e) {
                                                        document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">网络错误，请检查网络或尝试刷新</span>';
                                                        showToast('网络错误，视频加载失败', 'error');
                                                    }
                                                    break;
                                                case Hls.ErrorTypes.MEDIA_ERROR:
                                                    document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">媒体错误，正在尝试恢复...</span>';
                                                    try {
                                                        hls.recoverMediaError();
                                                    } catch(e) {
                                                        try {
                                                            hls.swapAudioCodec();
                                                            hls.recoverMediaError();
                                                        } catch(e2) {
                                                            setTimeout(function() {
                                                                if (dp) {
                                                                    try { dp.destroy(); } catch(e) {}
                                                                    dp = null;
                                                                }
                                                                playVideo();
                                                            }, 1000);
                                                        }
                                                    }
                                                    break;
                                                default:
                                                    document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">视频加载失败，请尝试重新加载</span>';
                                                    showToast('视频加载失败', 'error');
                                                    break;
                                            }
                                        }
                                    });
                                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                    video.src = mxjxUrl;
                                    video.addEventListener('loadedmetadata', function() {
                                        firstFrameReady = true;
                                        document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">视频加载完成，即将播放...</span>';
                                        tryPlay();
                                    });
                                    video.addEventListener('playing', function() {
                                        document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                                    });
                                }
                            }
                        }
                    },
                    autoplay: false,
                    preload: 'auto',
                    muted: false,
                    theme: '#667eea',
                    lang: 'zh-cn',
                    screenshot: true,
                    hotkey: true,
                    volume: 0.7,
                    playbackSpeed: [0.5, 0.75, 1, 1.25, 1.5, 2],
                    mutex: true,
                    airplay: true
                });

                let statusUpdated = false;

                dp.on('loadstart', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">开始加载视频...</span>';
                    }
                });

                dp.on('playing', function() {
                    statusUpdated = true;
                    document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                });

                dp.on('pause', function() {
                    if (statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#909399">已暂停</span>';
                    }
                });

                dp.on('waiting', function() {
                    if (statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#e6a23c">缓冲中...</span>';
                    }
                });

                dp.on('play', function() {
                    statusUpdated = true;
                    document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                });

                dp.on('error', function() {
                    if (!statusUpdated) {
                        document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">播放器错误，请检查视频链接</span>';
                        showToast('播放器错误，请检查视频链接', 'error');
                    }
                });

                setTimeout(function() {
                    if (!playAttempted && dp && dp.video && dp.video.readyState >= 2 && dp.video.paused) {
                        playAttempted = true;
                        statusUpdated = true;
                        document.getElementById('playStatus').innerHTML = '<span style="color:#67c23a">视频加载成功，点击播放按钮开始播放</span>';
                        dp.video.play().catch(function() {});
                    }
                }, 10000);
            } catch (e) {
                document.getElementById('playStatus').innerHTML = '<span style="color:#f56c6c">播放器初始化失败: ' + e.message + '</span>';
                showToast('播放失败: ' + e.message, 'error');
            }
        }

        let analyzeDp = null;
        let analyzeHls = null;
        let analyzeLoadTimeout = null;

        function clearAnalyzeLoadTimeout() {
            if (analyzeLoadTimeout) {
                clearTimeout(analyzeLoadTimeout);
                analyzeLoadTimeout = null;
            }
        }

        function getAnalyzeMxjxUrl() {
            if (!analyzeBaseUrl) return '';
            const useProxy = document.getElementById('analyzeUseProxy')?.checked;
            if (useProxy) {
                const proxySel = document.getElementById('analyzeProxyServer');
                const proxy = proxySel?.value;
                if (proxy) {
                    const sep = analyzeBaseUrl.includes('?') ? '&' : '?';
                    return analyzeBaseUrl + sep + 'proxy=' + encodeURIComponent(proxy);
                }
            }
            return analyzeBaseUrl;
        }

        function toggleAutoProxy() {
            autoSwitchProxy = document.getElementById('analyzeAutoProxy')?.checked ?? true;
            if (autoSwitchProxy) {
                autoSelectFastestProxy();
            }
            updateAnalyzeMxjxUrl();
        }

        function onProxySelectChange() {
            updateAnalyzeMxjxUrl();
        }

        async function autoSelectFastestProxy() {
            const autoChecked = document.getElementById('analyzeAutoProxy')?.checked;
            if (!autoChecked) return;
            const proxySel = document.getElementById('analyzeProxyServer');
            if (!proxySel) return;
            const fastest = await getFastestProxy();
            if (fastest && proxySel.value !== fastest) {
                proxySel.value = fastest;
            }
        }

        function updateAnalyzeMxjxUrl() {
            const url = getAnalyzeMxjxUrl();
            const el = document.getElementById('analyzeMxjxUrl');
            if (el && url) {
                el.textContent = url;
            }
            const proxySel = document.getElementById('analyzeProxyServer');
            const useProxy = document.getElementById('analyzeUseProxy')?.checked;
            const checkBtn = document.getElementById('checkProxyBtn');
            const autoProxyLabel = document.getElementById('analyzeAutoProxyLabel');
            if (proxySel) {
                proxySel.style.display = useProxy ? 'inline-block' : 'none';
            }
            if (checkBtn) {
                checkBtn.style.display = useProxy ? 'inline-block' : 'none';
            }
            if (autoProxyLabel) {
                autoProxyLabel.style.display = useProxy ? 'flex' : 'none';
            }
        }

        function playAnalyzeVideo() {
            analyzeProxyRetryCount = 0;
            const url = getAnalyzeMxjxUrl();
            if (!url) {
                showToast('请先分析视频', 'error');
                return;
            }
            const container = document.getElementById('analyzePlayerContainer');
            const statusEl = document.getElementById('analyzePlayStatus');
            if (container) {
                container.style.display = 'block';
            }
            if (statusEl) {
                statusEl.innerHTML = '<span style="color:#e6a23c">正在加载播放器...</span>';
            }

            if (analyzeDp) {
                try { analyzeDp.destroy(); } catch(e) {}
                analyzeDp = null;
            }
            if (analyzeHls) {
                try { analyzeHls.destroy(); } catch(e) {}
                analyzeHls = null;
            }
            clearAnalyzeLoadTimeout();

            if (typeof DPlayer === 'undefined' || typeof Hls === 'undefined') {
                if (statusEl) {
                    statusEl.innerHTML = '<span style="color:#f56c6c">播放器库加载失败，请刷新页面</span>';
                }
                showToast('播放器库加载失败', 'error');
                return;
            }

            try {
                const hlsConfig = {
                    enableWorker: true,
                    lowLatencyMode: false,
                    maxBufferLength: 60,
                    maxMaxBufferLength: 900,
                    minBufferLength: 5,
                    maxBufferSize: 120 * 1000 * 1000,
                    maxBufferHole: 0.3,
                    highBufferWatchdogPeriod: 0.5,
                    startLevel: -1,
                    capLevelToPlayerSize: true,
                    liveSyncDurationCount: 3,
                    liveMaxLatencyDurationCount: 10,
                    fragLoadingTimeOut: 30000,
                    manifestLoadingTimeOut: 20000,
                    levelLoadingTimeOut: 15000,
                    backBufferLength: 60,
                    startFragPrefetch: true,
                    enableSoftwareAES: true,
                    abrEwmaDefaultEstimate: 2000000,
                    abrBandWidthFactor: 0.75,
                    abrEwmaFastLive: 3.0,
                    abrEwmaSlowLive: 9.0,
                    maxFragLookUpTolerance: 0.2,
                    enableCEA708Captions: false,
                    enableWebVTT: false,
                    enableIMSC1: false,
                    renderTextTracksNatively: false,
                    xhrSetup: function(xhr) {
                        xhr.withCredentials = false;
                        xhr.timeout = 30000;
                    }
                };

                let hlsReady = false;
                let firstFrameReady = false;
                let playAttempted = false;

                const tryPlay = function() {
                    if (playAttempted) return;
                    if (firstFrameReady && analyzeDp && analyzeDp.video && analyzeDp.video.paused) {
                        playAttempted = true;
                        analyzeDp.video.play().catch(function(e) {
                            console.warn('自动播放被阻止:', e);
                            if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">视频已加载，点击播放按钮开始播放</span>';
                            showToast('点击播放按钮开始播放', 'warning');
                        });
                    }
                };

                const containerEl = document.getElementById('analyzeVideoPlayer');
                analyzeDp = new DPlayer({
                    container: containerEl,
                    video: {
                        url: url,
                        type: 'customHls',
                        customType: {
                            customHls: function(video, player) {
                                if (Hls.isSupported()) {
                                    analyzeHls = new Hls(hlsConfig);
                                    analyzeHls.loadSource(video.src);
                                    analyzeHls.attachMedia(video);
                                    player.hls = analyzeHls;

                                    analyzeLoadTimeout = setTimeout(function() {
                                        if (video.readyState < 2) {
                                            if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">视频加载较慢，正在努力加载中...</span>';
                                            showToast('视频加载较慢，请耐心等待...', 'warning');
                                        }
                                    }, 6000);

                                    analyzeHls.on(Hls.Events.MANIFEST_PARSED, function(event, data) {
                                        console.log('HLS 清单解析完成, 共', data.levels.length, '个清晰度');
                                        hlsReady = true;
                                        if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">视频解析完成，正在缓冲首帧...</span>';
                                    });

                                    analyzeHls.on(Hls.Events.FRAG_LOADED, function(event, data) {
                                        console.log('片段加载完成, 索引:', data.frag.sn, '时长:', data.frag.duration.toFixed(2) + 's');
                                        clearAnalyzeLoadTimeout();
                                        firstFrameReady = true;
                                        if (statusEl && !playAttempted) {
                                            statusEl.innerHTML = '<span style="color:#67c23a">首帧加载完成，即将播放...</span>';
                                        }
                                        tryPlay();
                                    });

                                    analyzeHls.on(Hls.Events.LEVEL_SWITCHED, function(event, data) {
                                        console.log('清晰度切换到:', data.level);
                                    });

                                    analyzeHls.on(Hls.Events.ERROR, function(event, data) {
                                        console.error('HLS 错误:', data.type, data.details, data.fatal ? '(致命)' : '');
                                        if (data.fatal) {
                                            clearAnalyzeLoadTimeout();
                                            switch (data.type) {
                                                case Hls.ErrorTypes.NETWORK_ERROR:
                                                    if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">网络错误，正在尝试恢复...</span>';
                                                    let recovered = false;
                                                    try {
                                                        analyzeHls.startLoad();
                                                        recovered = true;
                                                    } catch(e) {}
                                                    if (!recovered && trySwitchProxyForAnalyze()) {
                                                        if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">当前代理不可用，正在切换代理...</span>';
                                                        showToast('代理不可用，正在自动切换...', 'warning');
                                                        setTimeout(function() {
                                                            if (analyzeDp) {
                                                                try { analyzeDp.destroy(); } catch(e) {}
                                                                analyzeDp = null;
                                                            }
                                                            playAnalyzeVideo();
                                                        }, 500);
                                                    } else if (!recovered) {
                                                        if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">网络错误，请检查网络或尝试刷新</span>';
                                                        showToast('网络错误，视频加载失败', 'error');
                                                    }
                                                    break;
                                                case Hls.ErrorTypes.MEDIA_ERROR:
                                                    if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">媒体错误，正在尝试恢复...</span>';
                                                    try {
                                                        analyzeHls.recoverMediaError();
                                                    } catch(e) {
                                                        try {
                                                            analyzeHls.swapAudioCodec();
                                                            analyzeHls.recoverMediaError();
                                                        } catch(e2) {
                                                            setTimeout(function() {
                                                                if (analyzeDp) {
                                                                    try { analyzeDp.destroy(); } catch(e) {}
                                                                    analyzeDp = null;
                                                                }
                                                                playAnalyzeVideo();
                                                            }, 1000);
                                                        }
                                                    }
                                                    break;
                                                default:
                                                    if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">视频加载失败，请尝试重新加载</span>';
                                                    showToast('视频加载失败', 'error');
                                                    break;
                                            }
                                        }
                                    });
                                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                                    video.src = url;
                                    video.addEventListener('loadedmetadata', function() {
                                        hlsReady = true;
                                        firstFrameReady = true;
                                        if (statusEl) statusEl.innerHTML = '<span style="color:#67c23a">视频加载完成，即将播放...</span>';
                                        tryPlay();
                                    });
                                    video.addEventListener('playing', function() {
                                        if (statusEl) statusEl.innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                                    });
                                }
                            }
                        }
                    },
                    autoplay: false,
                    preload: 'auto',
                    muted: false,
                    theme: '#667eea',
                    lang: 'zh-cn',
                    screenshot: true,
                    hotkey: true,
                    volume: 0.7,
                    playbackSpeed: [0.5, 0.75, 1, 1.25, 1.5, 2],
                    mutex: true,
                    airplay: true
                });

                analyzeDp.on('loadstart', function() {
                    if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">开始加载视频...</span>';
                });

                analyzeDp.on('playing', function() {
                    clearAnalyzeLoadTimeout();
                    if (statusEl) statusEl.innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                });

                analyzeDp.on('waiting', function() {
                    if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">缓冲中...</span>';
                });

                analyzeDp.on('play', function() {
                    clearAnalyzeLoadTimeout();
                    if (statusEl) statusEl.innerHTML = '<span style="color:#67c23a">正在播放...</span>';
                });

                analyzeDp.on('pause', function() {
                    if (statusEl) statusEl.innerHTML = '<span style="color:#909399">已暂停</span>';
                });

                analyzeDp.on('error', function() {
                    clearAnalyzeLoadTimeout();
                    if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">播放器错误，请检查视频链接</span>';
                    showToast('播放器错误，请检查视频链接', 'error');
                });

                setTimeout(function() {
                    if (analyzeDp && analyzeDp.video && analyzeDp.video.readyState >= 2 && analyzeDp.video.paused && !playAttempted) {
                        if (statusEl) statusEl.innerHTML = '<span style="color:#e6a23c">视频已就绪，点击播放按钮开始播放</span>';
                    }
                }, 10000);
            } catch (e) {
                if (statusEl) statusEl.innerHTML = '<span style="color:#f56c6c">播放器初始化失败: ' + e.message + '</span>';
                showToast('播放失败: ' + e.message, 'error');
            }
        }

        async function loadPlayerConfig() {
            try {
                const res = await fetch(API_BASE + '?action=player/config&_t=' + Date.now());
                const data = await res.json();
                if (data.success && data.config) {
                    const config = data.config;
                    const playerSelect = document.getElementById('playerSelect');
                    const autoplaySelect = document.getElementById('playerAutoplay');
                    const preloadSelect = document.getElementById('playerPreload');
                    const apiBaseUrlInput = document.getElementById('playerApiBaseUrl');
                    
                    if (playerSelect && config.player) {
                        playerSelect.value = config.player;
                    }
                    if (autoplaySelect) {
                        autoplaySelect.value = config.autoplay ? 'true' : 'false';
                    }
                    if (preloadSelect && config.preload) {
                        preloadSelect.value = config.preload;
                    }
                    if (apiBaseUrlInput && config.api_base_url !== undefined) {
                        apiBaseUrlInput.value = config.api_base_url || '';
                    }
                }
            } catch (e) {
                console.error('加载播放器配置失败:', e);
            }
        }

        function changePlayerPreview() {
            const player = document.getElementById('playerSelect')?.value;
            showToast('已选择 ' + player + ' 播放器，点击保存生效', 'info');
        }

        async function savePlayerConfig() {
            const player = document.getElementById('playerSelect')?.value;
            const autoplay = document.getElementById('playerAutoplay')?.value === 'true';
            const preload = document.getElementById('playerPreload')?.value;
            const apiBaseUrl = document.getElementById('playerApiBaseUrl')?.value.trim();
            
            if (!player) {
                showToast('请选择播放器', 'error');
                return;
            }
            
            try {
                const res = await fetch(API_BASE + '?action=player/config/save', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        player: player,
                        autoplay: autoplay,
                        preload: preload,
                        api_base_url: apiBaseUrl
                    })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('保存成功', 'success');
                } else {
                    showToast(data.message || '保存失败', 'error');
                }
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function loadProxyList() {
            try {
                const res = await fetch(API_BASE + '?action=proxy/list&_t=' + Date.now());
                const data = await res.json();
                if (data.success && data.proxies && data.proxies.length > 0) {
                    proxyListCache = data.proxies;
                    const analyzeSel = document.getElementById('analyzeProxyServer');
                    if (analyzeSel) {
                        const currentVal = analyzeSel.value;
                        analyzeSel.innerHTML = '<option value="">选择代理服务器（按延迟排序）</option>';
                        data.proxies.forEach(function(p) {
                            const opt = document.createElement('option');
                            opt.value = p.url;
                            opt.dataset.id = p.id || '';
                            opt.dataset.responseTime = p.response_time || 0;
                            let latencyStr = '';
                            if (p.response_time && p.response_time > 0) {
                                latencyStr = ' ' + formatLatency(p.response_time);
                            } else {
                                latencyStr = ' ⏳未测速';
                            }
                            opt.textContent = p.name + latencyStr + ' (' + p.type.toUpperCase() + ')';
                            analyzeSel.appendChild(opt);
                        });
                        if (currentVal) {
                            analyzeSel.value = currentVal;
                        } else if (document.getElementById('analyzeAutoProxy')?.checked) {
                            autoSelectFastestProxy();
                        }
                    }
                }
            } catch (e) {
                console.warn('加载代理列表失败:', e);
            }
        }

        function formatLatency(ms) {
            if (!ms || ms <= 0) return '—';
            if (ms < 300) return ' 🟢' + Math.round(ms) + 'ms';
            if (ms < 1000) return ' 🟡' + Math.round(ms) + 'ms';
            return ' 🔴' + (ms / 1000).toFixed(1) + 's';
        }

        function getLatencyColor(ms) {
            if (!ms || ms <= 0) return '#909399';
            if (ms < 300) return '#67c23a';
            if (ms < 1000) return '#e6a23c';
            return '#f56c6c';
        }

        async function checkAllProxies() {
            const btn = document.getElementById('checkProxyBtn');
            if (btn) {
                btn.disabled = true;
                const oldText = btn.textContent;
                btn.textContent = '测速中...';
            }
            try {
                const res = await fetch(API_BASE + '?action=proxy/check&_t=' + Date.now());
                const data = await res.json();
                if (data.success && data.results) {
                    await loadProxyList();
                    showToast('测速完成，共检测 ' + data.results.length + ' 个代理', 'success');
                } else {
                    showToast(data.message || '测速失败', 'error');
                }
            } catch (e) {
                console.error('代理测速失败:', e);
                showToast('测速失败: ' + e.message, 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = '🔄 测速';
                }
            }
        }

        let currentProxyIndex = 0;
        let proxyListCache = [];
        let autoSwitchProxy = true;

        async function getFastestProxy() {
            if (proxyListCache.length === 0) {
                try {
                    const res = await fetch(API_BASE + '?action=proxy/list&_t=' + Date.now());
                    const data = await res.json();
                    if (data.success && data.proxies && data.proxies.length > 0) {
                        proxyListCache = data.proxies;
                    }
                } catch (e) {
                    console.warn('获取代理列表失败:', e);
                }
            }
            if (proxyListCache.length > 0) {
                const fast = proxyListCache.find(p => p.response_time && p.response_time > 0 && p.response_time < 3000);
                if (fast) return fast.url;
                return proxyListCache[0].url;
            }
            return '';
        }

        let analyzeProxyRetryCount = 0;
        const MAX_PROXY_RETRIES = 3;

        function trySwitchProxyForAnalyze() {
            const useProxy = document.getElementById('analyzeUseProxy')?.checked;
            const autoSwitch = document.getElementById('analyzeAutoProxy')?.checked;
            if (!useProxy || !autoSwitch) return false;
            if (analyzeProxyRetryCount >= MAX_PROXY_RETRIES) return false;

            const proxySel = document.getElementById('analyzeProxyServer');
            if (!proxySel || !proxySel.value) return false;

            const nextProxy = switchToNextProxy(proxySel.value);
            if (nextProxy && nextProxy !== proxySel.value) {
                analyzeProxyRetryCount++;
                proxySel.value = nextProxy;
                console.log('自动切换代理到:', nextProxy, '(第' + analyzeProxyRetryCount + '次切换)');
                return true;
            }
            return false;
        }

        async function refreshRules() {
            try {
                const res = await fetch(API_BASE + '?action=rules/list&_t=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseErr) {
                    console.error('规则列表响应解析失败:', text.substring(0, 500));
                    throw new Error('服务器返回非JSON数据: ' + text.substring(0, 100));
                }
                if (!data.success) throw new Error(data.message || '获取失败');
                renderRulesTable(data.rules);
            } catch (e) {
                console.error('获取规则列表错误:', e);
                showToast('获取规则列表失败: ' + e.message, 'error');
            }
        }

        function renderRulesTable(rules) {
            const domains = Object.keys(rules);
            if (domains.length === 0) {
                document.getElementById('rulesTable').innerHTML = '<div class="empty">暂无域名规则</div>';
                return;
            }
            let html = '<table class="rules-table"><thead><tr><th>资源名称</th><th>域名</th><th>时长规则</th><th>DISCON规则</th><th>序列号跳跃</th><th>学习次数</th><th>更新时间</th><th>操作</th></tr></thead><tbody>';
            for (const domain of domains) {
                const r = rules[domain];
                const name = escapeHtml(r.name || domain);
                let durCount;
                if (typeof r.duration_rule_count === 'number') {
                    durCount = r.duration_rule_count;
                } else if (Array.isArray(r.duration_rules)) {
                    durCount = r.duration_rules.filter(x => x.enabled).length;
                } else {
                    durCount = 0;
                }
                let disCount;
                if (typeof r.discontinuity_rule_count === 'number') {
                    disCount = r.discontinuity_rule_count;
                } else if (Array.isArray(r.discontinuity_rules)) {
                    disCount = r.discontinuity_rules.filter(x => x.enabled).length;
                } else {
                    disCount = 0;
                }
                let seqCount;
                if (typeof r.sequence_jump_rule_count === 'number') {
                    seqCount = r.sequence_jump_rule_count;
                } else if (Array.isArray(r.sequence_jump_rules)) {
                    seqCount = r.sequence_jump_rules.filter(x => x.enabled).length;
                } else {
                    seqCount = 0;
                }
                const learnCount = r.learn_count || 0;
                const mtime = r.last_learn_date || r.analysis_date || (r._filemtime ? new Date(r._filemtime * 1000).toLocaleString() : '-');
                html += `
                    <tr>
                        <td><span style="color:#606266">${name}</span></td>
                        <td><strong>${escapeHtml(domain)}</strong></td>
                        <td><span class="tag tag-blue">${durCount}条</span></td>
                        <td>${disCount > 0 ? '<span class="tag tag-orange">启用</span>' : '<span style="color:#c0c4cc">未启用</span>'}</td>
                        <td>${seqCount > 0 ? '<span class="tag tag-red">' + seqCount + '条</span>' : '<span style="color:#c0c4cc">无</span>'}</td>
                        <td><span class="tag tag-green">${learnCount}次</span></td>
                        <td style="color:#909399;font-size:12px">${mtime}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="exportSingleRule('${escapeHtml(domain)}')">导出</button>
                            <button class="btn btn-sm btn-secondary" onclick="editRule('${escapeHtml(domain)}')">编辑</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRule('${escapeHtml(domain)}')">删除</button>
                        </td>
                    </tr>
                `;
            }
            html += '</tbody></table>';
            document.getElementById('rulesTable').innerHTML = html;
        }

        function showAddRule() {
            editingRules = {
                domain: '',
                name: '',
                duration_rules: [{ name: 'short_segment', enabled: true, type: 'duration', operator: '<', threshold: 2, reason: '极短片段 (<2秒) 可能是广告', weight: 30 }],
                discontinuity_rules: [{ name: 'discontinuity', enabled: false, type: 'discontinuity', reason: 'DISCONTINUITY 标记表示插播切换', weight: 80 }],
                sequence_jump_rules: [],
                filename_patterns: [],
                note: ''
            };
            showRuleEditor(editingRules, true);
        }

        async function editRule(domain) {
            try {
                const res = await fetch(API_BASE + '?action=rules/get&domain=' + encodeURIComponent(domain));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                editingRules = data.rules;
                showRuleEditor(data.rules, false);
            } catch (e) {
                showToast('获取规则失败: ' + e.message, 'error');
            }
        }

        function showRuleEditor(rules, isNew) {
            document.getElementById('ruleEditor').style.display = 'block';
            document.getElementById('ruleEditorTitle').textContent = isNew ? '新增规则' : '编辑规则';
            document.getElementById('ruleName').value = rules.name || '';
            document.getElementById('ruleDomain').value = rules.domain || '';
            document.getElementById('ruleDomain').disabled = !isNew;
            document.getElementById('ruleNote').value = rules.note || '';

            const disRules = Array.isArray(rules.discontinuity_rules) ? rules.discontinuity_rules : [];
            const disEnabled = disRules.length > 0 && disRules[0].enabled;
            document.getElementById('discontinuityEnabled').checked = disEnabled;

            renderDurationRules(Array.isArray(rules.duration_rules) ? rules.duration_rules : []);
            renderSeqJumpRules(Array.isArray(rules.sequence_jump_rules) ? rules.sequence_jump_rules : []);
            renderFilenamePatterns(Array.isArray(rules.filename_patterns) ? rules.filename_patterns : []);
        }

        function cancelRuleEdit() {
            document.getElementById('ruleEditor').style.display = 'none';
            editingRules = null;
        }

        function renderDurationRules(rules) {
            const container = document.getElementById('durationRules');
            if (rules.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无时长规则</div>';
                return;
            }
            container.innerHTML = rules.map((r, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="checkbox" ${r.enabled ? 'checked' : ''} onchange="editingRules.duration_rules[${i}].enabled = this.checked">
                    <select onchange="editingRules.duration_rules[${i}].operator = this.value">
                        <option value="<" ${r.operator === '<' ? 'selected' : ''}><</option>
                        <option value=">" ${r.operator === '>' ? 'selected' : ''}>></option>
                        <option value="<=" ${r.operator === '<=' ? 'selected' : ''}><=</option>
                        <option value=">=" ${r.operator === '>=' ? 'selected' : ''}>>=</option>
                        <option value="==" ${r.operator === '==' ? 'selected' : ''}>==</option>
                    </select>
                    <input type="number" step="0.1" value="${r.threshold}" style="width:80px;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.duration_rules[${i}].threshold = parseFloat(this.value)">
                    <span style="color:#909399">秒</span>
                    <input type="text" value="${r.reason || ''}" placeholder="说明" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.duration_rules[${i}].reason = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeDurationRule(${i})">删除</button>
                </div>
            `).join('');
        }

        function addDurationRule() {
            if (!editingRules) editingRules = { duration_rules: [] };
            if (!editingRules.duration_rules) editingRules.duration_rules = [];
            editingRules.duration_rules.push({
                name: 'duration_rule_' + Date.now(),
                enabled: true,
                type: 'duration',
                operator: '<',
                threshold: 2,
                reason: '',
                weight: 30
            });
            renderDurationRules(editingRules.duration_rules);
        }

        function removeDurationRule(idx) {
            editingRules.duration_rules.splice(idx, 1);
            renderDurationRules(editingRules.duration_rules);
        }

        function renderSeqJumpRules(rules) {
            const container = document.getElementById('sequenceJumpRules');
            if (rules.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无序列号跳跃规则</div>';
                return;
            }
            container.innerHTML = rules.map((r, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="checkbox" ${r.enabled ? 'checked' : ''} onchange="editingRules.sequence_jump_rules[${i}].enabled = this.checked">
                    <select onchange="editingRules.sequence_jump_rules[${i}].direction = this.value">
                        <option value="forward" ${r.direction === 'forward' ? 'selected' : ''}>向前跳跃</option>
                        <option value="backward" ${r.direction === 'backward' ? 'selected' : ''}>向后跳跃</option>
                        <option value="any" ${r.direction === 'any' ? 'selected' : ''}>任意方向</option>
                    </select>
                    <span style="color:#909399">阈值</span>
                    <input type="number" value="${r.threshold}" style="width:100px;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.sequence_jump_rules[${i}].threshold = parseInt(this.value)">
                    <input type="text" value="${r.reason || ''}" placeholder="说明" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px"
                        onchange="editingRules.sequence_jump_rules[${i}].reason = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeSeqJumpRule(${i})">删除</button>
                </div>
            `).join('');
        }

        function addSeqJumpRule() {
            if (!editingRules) editingRules = { sequence_jump_rules: [] };
            if (!editingRules.sequence_jump_rules) editingRules.sequence_jump_rules = [];
            editingRules.sequence_jump_rules.push({
                name: 'seq_jump_' + Date.now(),
                enabled: true,
                type: 'sequence_jump',
                direction: 'forward',
                threshold: 100000,
                reason: '',
                weight: 90
            });
            renderSeqJumpRules(editingRules.sequence_jump_rules);
        }

        function removeSeqJumpRule(idx) {
            editingRules.sequence_jump_rules.splice(idx, 1);
            renderSeqJumpRules(editingRules.sequence_jump_rules);
        }

        function renderFilenamePatterns(patterns) {
            const container = document.getElementById('filenamePatterns');
            if (patterns.length === 0) {
                container.innerHTML = '<div style="color:#c0c4cc;font-size:13px;padding:8px 0">暂无文件名模式</div>';
                return;
            }
            container.innerHTML = patterns.map((p, i) => `
                <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px">
                    <input type="text" value="${p}" placeholder="正则模式，例如：/ad/i" style="flex:1;padding:6px 8px;border:1px solid #dcdfe6;border-radius:4px;font-family:monospace"
                        onchange="editingRules.filename_patterns[${i}] = this.value">
                    <button class="btn btn-sm btn-danger" onclick="removeFilenamePattern(${i})">删除</button>
                </div>
            `).join('');
        }

        function addFilenamePattern() {
            if (!editingRules) editingRules = { filename_patterns: [] };
            if (!editingRules.filename_patterns) editingRules.filename_patterns = [];
            editingRules.filename_patterns.push('');
            renderFilenamePatterns(editingRules.filename_patterns);
        }

        function removeFilenamePattern(idx) {
            editingRules.filename_patterns.splice(idx, 1);
            renderFilenamePatterns(editingRules.filename_patterns);
        }

        async function saveRule() {
            const domain = document.getElementById('ruleDomain').value.trim();
            if (!domain) { showToast('请输入域名', 'error'); return; }
            if (!editingRules) editingRules = {};
            editingRules.domain = domain;
            editingRules.name = document.getElementById('ruleName').value.trim();
            editingRules.note = document.getElementById('ruleNote').value;
            const disEnabled = document.getElementById('discontinuityEnabled').checked;
            if (!editingRules.discontinuity_rules || editingRules.discontinuity_rules.length === 0) {
                editingRules.discontinuity_rules = [{
                    name: 'discontinuity',
                    enabled: disEnabled,
                    type: 'discontinuity',
                    reason: 'DISCONTINUITY 标记表示插播切换',
                    weight: 80
                }];
            } else {
                editingRules.discontinuity_rules[0].enabled = disEnabled;
            }
            try {
                const res = await fetch(API_BASE + '?action=rules/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domain: domain, rules: editingRules })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('规则保存成功', 'success');
                cancelRuleEdit();
                refreshRules();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function deleteRule(domain) {
            if (!confirm('确定要删除该域名的规则吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=rules/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ domain: domain })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('规则已删除', 'success');
                refreshRules();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function clearAllRules() {
            if (!confirm('⚠️ 确定要清理所有域名规则吗？\n\n此操作不可恢复，建议先导出备份！')) return;
            if (!confirm('再次确认：真的要删除全部规则吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=rules/clear', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('已清理 ' + (data.cleared_count || 0) + ' 条规则', 'success');
                refreshRules();
            } catch (e) {
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function exportAllRules() {
            try {
                const res = await fetch(API_BASE + '?action=rules/export&download=1&_t=' + Date.now());
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'all_rules.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                showToast('导出成功', 'success');
            } catch (e) {
                showToast('导出失败: ' + e.message, 'error');
            }
        }

        async function exportSingleRule(domain) {
            try {
                const res = await fetch(API_BASE + '?action=rules/export&domain=' + encodeURIComponent(domain) + '&download=1&_t=' + Date.now());
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'rules_' + domain + '.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                showToast('导出成功', 'success');
            } catch (e) {
                showToast('导出失败: ' + e.message, 'error');
            }
        }

        async function importRulesFromFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            try {
                const text = await file.text();
                const importData = JSON.parse(text);
                const res = await fetch(API_BASE + '?action=rules/import', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(importData)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast(data.message || '导入成功', 'success');
                refreshRules();
            } catch (e) {
                showToast('导入失败: ' + e.message, 'error');
            } finally {
                event.target.value = '';
            }
        }

        let latestUpdateData = null;

        function showUpdateModal(data) {
            latestUpdateData = data;
            const modal = document.getElementById('updateModal');
            const latestShort = (data.latest_commit || '').substring(0, 7);
            const currentShort = (data.current_commit || '').substring(0, 7);

            const curVerEl = document.getElementById('modalCurrentVersion');
            const latestVerEl = document.getElementById('modalLatestVersion');
            if (curVerEl) {
                curVerEl.textContent = (data.current_version || '-') + (currentShort ? ' (' + currentShort + ')' : '');
            }
            if (latestVerEl) {
                latestVerEl.textContent = (data.latest_version || '-') + (latestShort ? ' (' + latestShort + ')' : '');
            }

            const metaEl = document.getElementById('modalUpdateMeta');
            const metaItems = [];
            if (data.latest_date) {
                metaItems.push('<span>🕐 ' + new Date(data.latest_date).toLocaleString('zh-CN') + '</span>');
            }
            if (data.latest_message) {
                metaItems.push('<span>📝 ' + escapeHtml(data.latest_message.substring(0, 50)) + '</span>');
            }
            if (metaEl) metaEl.innerHTML = metaItems.join('');

            const changelog = data.changelog || [];
            const changelogEl = document.getElementById('modalChangelog');
            document.getElementById('changelogCount').textContent = changelog.length + ' 项更新';

            if (changelog.length === 0) {
                changelogEl.innerHTML = '<div style="text-align:center;color:#909399;padding:20px">暂无更新记录</div>';
            } else {
                changelogEl.innerHTML = changelog.map(item => `
                    <div class="changelog-item">
                        <span class="changelog-type" style="background:${item.type_color}22;color:${item.type_color}">${item.type_label}</span>
                        <div class="changelog-content">
                            <div class="changelog-msg">${escapeHtml(item.message)}</div>
                            <div class="changelog-meta">
                                <span>${item.sha}</span>
                                <span>${item.date_formatted || ''}</span>
                                <span>${escapeHtml(item.author || '')}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            modal.style.display = 'flex';
        }

        function hideUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
        }

        function doUpdateFromModal() {
            hideUpdateModal();
            if (!document.getElementById('page-update').classList.contains('active')) {
                const updateItem = document.querySelector('.nav-item[data-page="update"]');
                if (updateItem) handleNavClick(updateItem);
            }
            setTimeout(() => doUpdate(), 300);
        }

        async function checkUpdate(autoShowModal = false) {
            const checkBtn = document.querySelector('button[onclick="checkUpdate()"]');
            const originalBtnText = checkBtn ? checkBtn.textContent : '';
            if (checkBtn) {
                checkBtn.disabled = true;
                checkBtn.textContent = '检查中...';
            }

            const statusEl = document.getElementById('updateStatus');
            if (statusEl) {
                statusEl.textContent = '检查中...';
                statusEl.className = 'stat-value';
            }

            try {
                const res = await fetch(API_BASE + '?action=update/check');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                const curVerEl = document.getElementById('currentVersion');
                const latestVerEl = document.getElementById('latestVersion');
                if (curVerEl) {
                    const curShort = (data.current_commit || '').substring(0, 7);
                    curVerEl.textContent = (data.current_version || '-') + (curShort ? ' · ' + curShort : '');
                }
                if (latestVerEl) {
                    const latestShort = (data.latest_commit || '').substring(0, 7);
                    latestVerEl.textContent = (data.latest_version || '-') + (latestShort ? ' · ' + latestShort : '');
                }
                
                const updateBtn = document.getElementById('updateBtn');
                const githubStatusEl = document.getElementById('githubStatus');
                
                if (githubStatusEl) {
                    if (data.github_connected) {
                        let mirrorInfo = '';
                        if (data.used_mirror) {
                            try {
                                const m = new URL(data.used_mirror);
                                mirrorInfo = ' (' + m.host + ')';
                            } catch (e) {
                                mirrorInfo = ' (' + data.used_mirror + ')';
                            }
                        }
                        githubStatusEl.textContent = '连接成功' + mirrorInfo;
                        githubStatusEl.className = 'stat-value success';
                    } else {
                        githubStatusEl.textContent = '连接失败';
                        githubStatusEl.className = 'stat-value danger';
                    }
                }
                
                if (data.has_update) {
                    if (statusEl) {
                        statusEl.textContent = '有新版本';
                        statusEl.className = 'stat-value warning';
                    }
                    if (updateBtn) updateBtn.disabled = false;
                    latestUpdateData = data;
                    setTimeout(() => showUpdateModal(data), 300);
                } else {
                    if (statusEl) {
                        statusEl.textContent = '已是最新';
                        statusEl.className = 'stat-value success';
                    }
                    if (updateBtn) updateBtn.disabled = true;
                    if (!autoShowModal) {
                        showToast('已是最新版本', 'success');
                    }
                }
            } catch (e) {
                if (statusEl) {
                    statusEl.textContent = '检查失败';
                    statusEl.className = 'stat-value danger';
                }
                const githubStatusEl = document.getElementById('githubStatus');
                if (githubStatusEl) {
                    githubStatusEl.textContent = '连接失败';
                    githubStatusEl.className = 'stat-value danger';
                }
                loadVersion();
                if (!autoShowModal) {
                    showToast('检查更新失败: ' + e.message, 'error');
                }
            } finally {
                if (checkBtn) {
                    checkBtn.disabled = false;
                    checkBtn.textContent = originalBtnText || '检查更新';
                }
            }
        }

        async function loadVersion() {
            try {
                const res = await fetch(API_BASE + '?action=update/version');
                const data = await res.json();
                if (data.success) {
                    const curEl = document.getElementById('currentVersion');
                    if (curEl && (curEl.textContent === '-' || curEl.textContent === '')) {
                        const curShort = (data.current_commit || '').substring(0, 7);
                        curEl.textContent = (data.current_version || '-') + (curShort ? ' · ' + curShort : '');
                    }
                }
            } catch (e) {}
            loadBackupList();
            loadSystemInfo();
        }

        async function loadSystemInfo() {
            try {
                const res = await fetch(API_BASE + '?action=update/system_info');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                const info = data.data;
                
                let ghMirrorInfo = '';
                if (info.github.reachable && info.github.mirror) {
                    try {
                        const m = new URL(info.github.mirror);
                        ghMirrorInfo = ' (' + m.host + ')';
                    } catch (e) {
                        ghMirrorInfo = ' (' + info.github.mirror + ')';
                    }
                }
                document.getElementById('githubStatus').textContent = info.github.reachable ? ('连接成功' + ghMirrorInfo) : '连接失败';
                document.getElementById('githubStatus').className = 'stat-value ' + (info.github.reachable ? 'success' : 'danger');
                const ghStatusTitle = document.getElementById('githubStatus');
                if (ghStatusTitle) {
                    ghStatusTitle.title = info.github.error || ('共测试 ' + (info.github.tested_mirrors || 0) + ' 个镜像');
                }
                
                document.getElementById('serverInfo').innerHTML = `
                    <div class="detail-grid">
                        <div><strong>PHP版本:</strong> ${escapeHtml(info.server.php_version)}</div>
                        <div><strong>操作系统:</strong> ${escapeHtml(info.server.os)}</div>
                        <div><strong>服务器软件:</strong> ${escapeHtml(info.server.server_software)}</div>
                        <div><strong>服务器名称:</strong> ${escapeHtml(info.server.server_name)}</div>
                        <div><strong>服务器IP:</strong> ${escapeHtml(info.server.server_ip)}</div>
                        <div><strong>内存限制:</strong> ${escapeHtml(info.server.memory_limit)}</div>
                        <div><strong>最大执行时间:</strong> ${escapeHtml(info.server.max_execution_time)}秒</div>
                        <div><strong>文档根目录:</strong> ${escapeHtml(info.server.document_root)}</div>
                    </div>
                `;
                
                document.getElementById('permissionInfo').innerHTML = `
                    <table class="rules-table">
                        <thead>
                            <tr>
                                <th>路径</th>
                                <th>状态</th>
                                <th>权限</th>
                                <th>可写</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${info.permissions.map(p => `
                                <tr>
                                    <td>${escapeHtml(p.path)}</td>
                                    <td>${p.exists ? '<span class="tag tag-green">存在</span>' : '<span class="tag tag-red">不存在</span>'}</td>
                                    <td>${escapeHtml(p.permission)}</td>
                                    <td>${p.writable ? '<span class="tag tag-green">是</span>' : '<span class="tag tag-red">否</span>'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } catch (e) {
                document.getElementById('serverInfo').innerHTML = '<span class="tag tag-red">加载失败:</span> ' + e.message;
                document.getElementById('permissionInfo').innerHTML = '<span class="tag tag-red">加载失败:</span> ' + e.message;
            }
        }

        async function createBackup() {
            try {
                const res = await fetch(API_BASE + '?action=update/backup/create');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份创建成功: ' + data.filename, 'success');
                loadBackupList();
            } catch (e) {
                showToast('创建备份失败: ' + e.message, 'error');
            }
        }

        async function loadBackupList() {
            try {
                const res = await fetch(API_BASE + '?action=update/backup/list');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                const backups = data.backups || [];
                const container = document.getElementById('backupList');
                
                if (backups.length === 0) {
                    container.innerHTML = '<div class="empty">暂无备份文件</div>';
                    return;
                }
                
                container.innerHTML = `
                    <table class="rules-table">
                        <thead>
                            <tr>
                                <th>备份文件名</th>
                                <th>大小</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${backups.map(b => `
                                <tr>
                                    <td><strong>${b.filename}</strong></td>
                                    <td>${b.size_formatted}</td>
                                    <td style="color:#909399;font-size:12px">${b.created_formatted}</td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" onclick="restoreBackup('${b.filename}')">恢复</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteBackup('${b.filename}')">删除</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } catch (e) {
                document.getElementById('backupList').innerHTML = '<div class="empty">加载备份列表失败</div>';
            }
        }

        async function restoreBackup(filename) {
            if (!confirm('确定要恢复该备份吗？当前数据将被覆盖。')) return;
            try {
                const res = await fetch(API_BASE + '?action=update/backup/restore', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename: filename })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份恢复成功', 'success');
            } catch (e) {
                showToast('恢复失败: ' + e.message, 'error');
            }
        }

        async function deleteBackup(filename) {
            if (!confirm('确定要删除该备份吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=update/backup/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ filename: filename })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('备份已删除', 'success');
                loadBackupList();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function clearBrowserCache() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理浏览器缓存...</span>';
            try {
                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                }
                resultEl.innerHTML = '<span style="color:#67c23a">浏览器缓存已清理</span>';
                showToast('浏览器缓存已清理', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function clearServiceWorker() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理 Service Worker...</span>';
            try {
                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(registrations.map(reg => reg.unregister()));
                }
                resultEl.innerHTML = '<span style="color:#67c23a">Service Worker 已清理</span>';
                showToast('Service Worker 已清理', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function clearAllCaches() {
            const resultEl = document.getElementById('cacheClearResult');
            resultEl.innerHTML = '<span style="color:#409eff">正在清理所有缓存...</span>';
            const logs = [];
            try {
                try {
                    const res = await fetch(API_BASE + '?action=update/clear_cache');
                    const data = await res.json();
                    if (data.success) {
                        logs.push('服务器 PHP 缓存: 已清理');
                    } else {
                        logs.push('服务器 PHP 缓存: 清理失败 - ' + (data.message || '未知错误'));
                    }
                } catch (e) {
                    logs.push('服务器 PHP 缓存: 清理失败 - ' + e.message);
                }

                if ('caches' in window) {
                    const cacheNames = await caches.keys();
                    await Promise.all(cacheNames.map(name => caches.delete(name)));
                    logs.push('浏览器缓存: 已清理');
                }
                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    await Promise.all(registrations.map(reg => reg.unregister()));
                    logs.push('Service Worker: 已清理');
                }
                localStorage.removeItem('m3u8_rules_cache');
                sessionStorage.clear();
                logs.push('localStorage/sessionStorage: 已清理');

                const meta = document.createElement('meta');
                meta.httpEquiv = 'Cache-Control';
                meta.content = 'no-cache, no-store, must-revalidate';
                document.head.appendChild(meta);
                logs.push('页面缓存策略: 已禁用');

                resultEl.innerHTML = logs.map(l => '<div style="color:#67c23a">' + l + '</div>').join('');
                showToast('所有缓存已清理完成', 'success');
            } catch (e) {
                resultEl.innerHTML = '<span style="color:#f56c6c">清理失败: ' + e.message + '</span>';
                showToast('清理失败: ' + e.message, 'error');
            }
        }

        async function doUpdate() {
            if (!latestUpdateData) {
                try {
                    const res = await fetch(API_BASE + '?action=update/check');
                    const data = await res.json();
                    if (data.success && data.has_update) {
                        latestUpdateData = data;
                    }
                } catch (e) {}
            }

            const confirmed = await showUpdateConfirm(latestUpdateData);
            if (!confirmed) return;

            const btn = document.getElementById('updateBtn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = '更新中...';
            }

            let statusEl = document.getElementById('updateResult');
            if (!statusEl) {
                statusEl = document.createElement('div');
                statusEl.id = 'updateResult';
                statusEl.style.fontSize = '12px';
                statusEl.style.color = '#606266';
                statusEl.style.padding = '12px';
                statusEl.style.background = '#f5f7fa';
                statusEl.style.borderRadius = '8px';
                statusEl.style.marginTop = '12px';
                const pageUpdate = document.getElementById('page-update');
                if (pageUpdate) {
                    pageUpdate.appendChild(statusEl);
                } else {
                    document.body.appendChild(statusEl);
                }
            }

            try {
                statusEl.innerHTML = '<div style="color:#409eff">正在验证授权...</div>';
                const authRes = await fetch(API_BASE + '?action=update/integrity');
                const authData = await authRes.json();
                if (!authData.auth_valid) {
                    throw new Error('授权验证失败: ' + (authData.issues?.join(', ') || '未知错误'));
                }
                statusEl.innerHTML += '<div style="color:#67c23a">授权验证通过</div>';
                statusEl.innerHTML += '<div style="color:#409eff">正在下载更新...</div>';
                const res = await fetch(API_BASE + '?action=update/download');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                statusEl.innerHTML += '<div style="color:#67c23a">更新完成</div>';
                if (data.cleaned_files && data.cleaned_files.length > 0) {
                    statusEl.innerHTML += '<div style="color:#e6a23c">清理了 ' + data.cleaned_files.length + ' 个差异文件:</div>';
                    data.cleaned_files.forEach(f => {
                        statusEl.innerHTML += '<div style="color:#909399;font-size:11px">' + escapeHtml(f) + '</div>';
                    });
                }
                if (data.integrity_check) {
                    if (data.integrity_check.success) {
                        statusEl.innerHTML += '<div style="color:#67c23a">完整性检查通过</div>';
                    } else {
                        statusEl.innerHTML += '<div style="color:#f56c6c">完整性检查发现问题:</div>';
                        data.integrity_check.issues.forEach(i => {
                            statusEl.innerHTML += '<div style="color:#f56c6c;font-size:11px">' + escapeHtml(i) + '</div>';
                        });
                    }
                }
                showToast('更新成功！备份: ' + data.backup_file, 'success');
                statusEl.innerHTML += '<div style="color:#409eff">正在清理缓存...</div>';
                await clearAllCaches();
                statusEl.innerHTML += '<div style="color:#67c23a">缓存已清理，2秒后刷新页面...</div>';
                setTimeout(() => location.reload(true), 2000);
            } catch (e) {
                statusEl.innerHTML = '<div style="color:#f56c6c">更新失败: ' + escapeHtml(e.message) + '</div>';
                showToast('更新失败: ' + e.message, 'error');
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = '立即更新';
                }
            }
        }

        function showUpdateConfirm(updateData) {
            return new Promise((resolve) => {
                const modalHtml = `
                    <div class="update-modal-overlay" data-confirm-modal>
                        <div class="update-modal" style="width:440px">
                            <div class="update-modal-header">
                                <div class="update-modal-icon">⚠️</div>
                                <div class="update-modal-title">确认更新</div>
                                <button class="update-modal-close" data-action="close">✕</button>
                            </div>
                            <div class="update-modal-body">
                                <p style="color:#606266;font-size:13px;margin-bottom:16px;line-height:1.6">
                                    确定要更新系统吗？更新前会自动创建备份，并自动清理差异文件。
                                </p>
                                ${updateData ? `
                                <div style="background:#f5f7fa;border-radius:8px;padding:12px;font-size:12px">
                                    <div style="margin-bottom:8px"><strong>更新摘要：</strong></div>
                                    <div style="color:#67c23a;margin-bottom:4px">
                                        共 ${updateData.changelog?.length || 0} 项更新
                                    </div>
                                    <div style="color:#909399">
                                        最新版本: ${escapeHtml(updateData.latest_version || '-')}
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                            <div class="update-modal-footer">
                                <button class="btn btn-secondary" data-action="cancel">取消</button>
                                <button class="btn btn-primary" data-action="confirm">确认更新</button>
                            </div>
                        </div>
                    </div>
                `;
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = modalHtml;
                const modal = tempDiv.firstElementChild;
                document.body.appendChild(modal);

                let resolved = false;
                const closeModal = (result) => {
                    if (resolved) return;
                    resolved = true;
                    modal.remove();
                    resolve(result);
                };

                const closeBtn = modal.querySelector('[data-action="close"]');
                const cancelBtn = modal.querySelector('[data-action="cancel"]');
                const confirmBtn = modal.querySelector('[data-action="confirm"]');

                if (closeBtn) closeBtn.addEventListener('click', () => closeModal(false));
                if (cancelBtn) cancelBtn.addEventListener('click', () => closeModal(false));
                if (confirmBtn) confirmBtn.addEventListener('click', () => closeModal(true));

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal(false);
                });
            });
        }

        async function checkIntegrity() {
            const statusEl = document.getElementById('integrityResult');
            statusEl.innerHTML = '<span style="color:#409eff">正在检查...</span>';
            try {
                const res = await fetch(API_BASE + '?action=update/integrity');
                const data = await res.json();
                if (data.success) {
                    statusEl.innerHTML = '<span style="color:#67c23a">完整性检查通过</span>';
                    showToast('系统完整性正常', 'success');
                } else {
                    const issues = data.issues || [];
                    statusEl.innerHTML = '<div style="color:#f56c6c">发现 ' + issues.length + ' 个问题:<br>' +
                        issues.map(i => '<div style="font-size:12px">' + escapeHtml(i) + '</div>').join('') + '</div>';
                    showToast('完整性检查发现问题', 'error');
                }
            } catch (e) {
                statusEl.innerHTML = '<span style="color:#f56c6c">检查失败: ' + escapeHtml(e.message) + '</span>';
            }
        }

        async function refreshAuthInfo() {
            try {
                const [authRes, versionRes] = await Promise.all([
                    fetch(API_BASE + '?action=auth/info'),
                    fetch(API_BASE + '?action=update/version')
                ]);
                
                const authData = await authRes.json();
                const versionData = await versionRes.json();
                
                if (!authData.success) throw new Error(authData.message);
                
                const sqStatusEl = document.getElementById('sqFileStatus');
                const localStatusEl = document.getElementById('localAuthStatus');
                const remoteStatusEl = document.getElementById('remoteAuthStatus');
                
                if (authData.sq_file_exists) {
                    sqStatusEl.textContent = '存在';
                    sqStatusEl.className = 'stat-value success';
                } else {
                    sqStatusEl.textContent = '不存在';
                    sqStatusEl.className = 'stat-value danger';
                }
                
                if (authData.local_valid) {
                    localStatusEl.textContent = '通过';
                    localStatusEl.className = 'stat-value success';
                } else {
                    localStatusEl.textContent = '失败';
                    localStatusEl.className = 'stat-value danger';
                }
                
                if (authData.remote && authData.remote.url) {
                    if (authData.remote.reachable) {
                        remoteStatusEl.textContent = '连接成功';
                        remoteStatusEl.className = 'stat-value success';
                    } else {
                        remoteStatusEl.textContent = '连接失败';
                        remoteStatusEl.className = 'stat-value danger';
                    }
                } else {
                    remoteStatusEl.textContent = '未检查';
                    remoteStatusEl.className = 'stat-value warning';
                }
                
                if (versionData.success) {
                    const curShort = (versionData.current_commit || '').substring(0, 7);
                    document.getElementById('authVersion').textContent = (versionData.current_version || '-') + (curShort ? ' · ' + curShort : '');
                }
                
                if (authData.local) {
                    const info = authData;
                    
                    document.getElementById('localAuthInfo').innerHTML = `
                        <div class="detail-grid">
                            <div><strong>授权文件:</strong> ${info.local.file_exists ? '<span class="tag tag-green">存在</span>' : '<span class="tag tag-red">不存在</span>'}</div>
                            <div><strong>文件大小:</strong> ${info.local.file_size} 字节</div>
                            <div><strong>授权码:</strong> <code>${info.local.auth_code ? info.local.auth_code.substring(0, 20) + '...' : '未设置'}</code></div>
                        </div>
                    `;
                    
                    if (info.remote && info.remote.url) {
                        document.getElementById('remoteAuthInfo').innerHTML = `
                            <div class="detail-grid">
                                <div><strong>服务器地址:</strong> ${escapeHtml(info.remote.url)}</div>
                                <div><strong>连接状态:</strong> ${info.remote.reachable ? '<span class="tag tag-green">可连接</span>' : '<span class="tag tag-red">不可连接</span>'}</div>
                                <div><strong>响应内容:</strong> <code>${info.remote.content ? escapeHtml(info.remote.content).substring(0, 50) + '...' : '无'}</code></div>
                                ${info.remote.error ? `<div><strong>错误信息:</strong> <span class="tag tag-red">${escapeHtml(info.remote.error)}</span></div>` : ''}
                            </div>
                        `;
                    } else {
                        document.getElementById('remoteAuthInfo').innerHTML = '<div class="empty">远程服务器信息未配置</div>';
                    }
                }
                
                if (authData.auth_config) {
                    document.getElementById('authServerIp').value = authData.auth_config.auth_server_ip || '';
                    document.getElementById('authServerPort').value = authData.auth_config.auth_server_port || '';
                    document.getElementById('authFile').value = authData.auth_config.auth_file || '';
                    document.getElementById('authFileCompare').value = authData.auth_config.auth_file_compare || '';
                    document.getElementById('enableRemoteVerify').checked = authData.auth_config.enable_remote_verify ?? true;
                    document.getElementById('enableTimestampCheck').checked = authData.auth_config.enable_timestamp_check ?? true;
                }
            } catch (e) {
                showToast('获取授权信息失败: ' + e.message, 'error');
            }
        }

        async function saveAuthConfig() {
            const config = {
                auth_server_ip: document.getElementById('authServerIp').value.trim(),
                auth_server_port: document.getElementById('authServerPort').value.trim(),
                auth_file: document.getElementById('authFile').value.trim(),
                auth_file_compare: document.getElementById('authFileCompare').value.trim(),
                enable_remote_verify: document.getElementById('enableRemoteVerify').checked,
                enable_timestamp_check: document.getElementById('enableTimestampCheck').checked
            };
            try {
                const res = await fetch(API_BASE + '?action=auth/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ config: config })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('配置保存成功', 'success');
                refreshAuthInfo();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function setAuthCode() {
            const authCode = document.getElementById('authCodeInput').value.trim();
            if (!authCode) {
                showToast('请输入授权码', 'error');
                return;
            }
            try {
                const res = await fetch(API_BASE + '?action=auth/set', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ auth_code: authCode })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('授权码设置成功', 'success');
                refreshAuthInfo();
            } catch (e) {
                showToast('设置失败: ' + e.message, 'error');
            }
        }

        async function generateAuthCode() {
            const domain = prompt('请输入授权域名:', 'localhost');
            if (!domain) return;
            try {
                const res = await fetch(API_BASE + '?action=auth/generate&domain=' + encodeURIComponent(domain));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                document.getElementById('authCodeInput').value = data.auth_code;
                showToast('授权码已生成', 'success');
            } catch (e) {
                showToast('生成失败: ' + e.message, 'error');
            }
        }

        let currentSites = [];
        let editingSite = null;

        async function refreshSites() {
            try {
                const includePaused = document.getElementById('showPaused')?.checked ? '1' : '0';
                const res = await fetch(API_BASE + '?action=sites/list&include_paused=' + includePaused + '&_t=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                currentSites = data.sites || [];
                renderSitesTable(currentSites);
                renderAutoLearnStats(data);
                populateSearchSiteSelect();
                document.getElementById('sitesCount').textContent = currentSites.length;
            } catch (e) {
                console.error('获取资源站列表错误:', e);
                showToast('获取资源站列表失败: ' + e.message, 'error');
            }
        }

        function renderAutoLearnStats(data) {
            const allSites = currentSites.length;
            const activeCount = currentSites.filter(s => s.status === 'active').length;
            document.getElementById('totalSites').textContent = allSites;
            document.getElementById('activeSites').textContent = activeCount;
            document.getElementById('lastLearnTime').textContent = data.last_learn_time || '从未学习';

            const statusEl = document.getElementById('autoLearnStatus');
            const config = data.auto_learn_config || {};
            if (config.enabled) {
                if (data.should_auto_learn) {
                    statusEl.textContent = '待执行';
                    statusEl.className = 'stat-value warning';
                } else {
                    statusEl.textContent = '运行中';
                    statusEl.className = 'stat-value success';
                }
            } else {
                statusEl.textContent = '已禁用';
                statusEl.className = 'stat-value danger';
            }

            document.getElementById('autoLearnEnabled').value = config.enabled ? 'true' : 'false';
            document.getElementById('intervalDays').value = config.interval_days ?? 3;
            document.getElementById('videosPerSite').value = config.videos_per_site ?? 5;
            document.getElementById('maxSitesPerRun').value = config.max_sites_per_run ?? 5;
            document.getElementById('minSegments').value = config.min_segments ?? 50;
            document.getElementById('maxAdPercentage').value = config.max_ad_percentage ?? 90;
        }

        function filterSites() {
            const keyword = document.getElementById('siteSearch').value.trim().toLowerCase();
            const showPaused = document.getElementById('showPaused').checked;
            let filtered = currentSites.filter(site => {
                if (!showPaused && site.status !== 'active') return false;
                return true;
            });
            if (keyword) {
                filtered = filtered.filter(s =>
                    s.name.toLowerCase().includes(keyword) ||
                    (s.note || '').toLowerCase().includes(keyword)
                );
            }
            renderSitesTable(filtered);
        }

        let healthCheckData = {};

        async function checkSitesHealth() {
            const btn = document.getElementById('healthCheckBtn');
            btn.disabled = true;
            btn.textContent = '检测中...';
            
            try {
                const res = await fetch(API_BASE + '?action=sites/health_check&_t=' + Date.now());
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                
                healthCheckData = {};
                data.results.forEach(r => {
                    healthCheckData[r.name] = r;
                });
                
                filterSites();
                showToast('检测完成：' + data.healthy + '/' + data.total + ' 个可用', 'success');
            } catch (e) {
                showToast('检测失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = '🔍 健康检测';
            }
        }

        async function toggleSiteStatus(name, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'paused' : 'active';
            const note = newStatus === 'paused' ? '手动暂停' : '';
            
            try {
                const res = await fetch(API_BASE + '?action=sites/update_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, status: newStatus, note })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('更新成功', 'success');
                refreshSites();
            } catch (e) {
                showToast('更新失败: ' + e.message, 'error');
            }
        }

        function renderSitesTable(sites) {
            const container = document.getElementById('sitesTable');
            if (sites.length === 0) {
                container.innerHTML = '<div class="empty">暂无资源站</div>';
                return;
            }
            let html = '<table class="rules-table"><thead><tr><th>优先级</th><th>名称</th><th>官网</th><th>采集接口</th><th>状态</th><th>响应时间</th><th>扩展备注</th><th>操作</th></tr></thead><tbody>';
            for (const site of sites) {
                const priority = site.priority || 99;
                const health = healthCheckData[site.name];
                let statusTag = site.status === 'active'
                    ? '<span class="tag tag-green">正常</span>'
                    : '<span class="tag tag-orange">暂停</span>';
                let responseTime = '-';
                
                if (health) {
                    if (site.status === 'active' && !health.healthy) {
                        statusTag = '<span class="tag tag-red">异常</span>';
                    } else if (health.healthy) {
                        responseTime = health.response_time + 'ms';
                    }
                }
                
                const siteUrl = site.site_url || '#';
                const apiUrl = site.api_url || '';
                const note = escapeHtml(site.note || '');
                const healthNote = health && !health.healthy ? escapeHtml(health.message) : note;
                const isPaused = site.status !== 'active';
                const videoBtnDisabled = isPaused ? ' disabled style="opacity:0.5;cursor:not-allowed"' : '';
                const videoBtnOnclick = isPaused ? '' : 'onclick="fetchSiteVideos(\'' + escapeHtml(site.name) + '\')"';
                html += `
                    <tr style="${isPaused ? 'opacity:0.6' : ''}">
                        <td><span class="tag tag-blue">${priority}</span></td>
                        <td><strong>${escapeHtml(site.name)}</strong></td>
                        <td>
                            ${site.site_url ? `<a href="${escapeHtml(siteUrl)}" target="_blank" style="color:#409eff;text-decoration:none;font-size:12px">访问官网 ↗</a>` : '-'}
                        </td>
                        <td style="font-size:12px;color:#909399;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${escapeHtml(apiUrl)}">
                            ${escapeHtml(apiUrl)}
                        </td>
                        <td>${statusTag}</td>
                        <td style="font-size:12px;color:#67c23a">${responseTime}</td>
                        <td style="font-size:12px;color:#606266;max-width:130px" title="${healthNote}">${healthNote || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" ${videoBtnOnclick}${videoBtnDisabled} title="${isPaused ? '站点已暂停，无法获取视频' : '获取视频列表'}">视频</button>
                            <button class="btn btn-sm btn-secondary" onclick="toggleSiteStatus('${escapeHtml(site.name)}', '${site.status}')">${isPaused ? '启用' : '暂停'}</button>
                            <button class="btn btn-sm btn-secondary" onclick="editSite('${escapeHtml(site.name)}')">编辑</button>
                        </td>
                    </tr>
                `;
            }
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function showAddSite() {
            editingSite = null;
            document.getElementById('siteEditorTitle').textContent = '新增资源站';
            document.getElementById('siteName').value = '';
            document.getElementById('siteUrl').value = '';
            document.getElementById('siteApiUrl').value = '';
            document.getElementById('siteType').value = 'maccms';
            document.getElementById('siteStatus').value = 'active';
            document.getElementById('sitePriority').value = 50;
            document.getElementById('siteNote').value = '';
            document.getElementById('siteName').disabled = false;
            document.getElementById('siteEditor').style.display = 'block';
            document.getElementById('siteEditor').scrollIntoView({ behavior: 'smooth' });
        }

        async function editSite(name) {
            try {
                const res = await fetch(API_BASE + '?action=sites/get&name=' + encodeURIComponent(name));
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                editingSite = data.site;
                document.getElementById('siteEditorTitle').textContent = '编辑资源站';
                document.getElementById('siteName').value = data.site.name || '';
                document.getElementById('siteUrl').value = data.site.site_url || '';
                document.getElementById('siteApiUrl').value = data.site.api_url || '';
                document.getElementById('siteType').value = data.site.type || 'maccms';
                document.getElementById('siteStatus').value = data.site.status || 'active';
                document.getElementById('sitePriority').value = data.site.priority || 50;
                document.getElementById('siteNote').value = data.site.note || '';
                document.getElementById('siteName').disabled = true;
                document.getElementById('siteEditor').style.display = 'block';
                document.getElementById('siteEditor').scrollIntoView({ behavior: 'smooth' });
            } catch (e) {
                showToast('获取资源站失败: ' + e.message, 'error');
            }
        }

        function cancelSiteEdit() {
            document.getElementById('siteEditor').style.display = 'none';
            editingSite = null;
        }

        async function saveSite() {
            const name = document.getElementById('siteName').value.trim();
            const siteUrl = document.getElementById('siteUrl').value.trim();
            const apiUrl = document.getElementById('siteApiUrl').value.trim();
            const type = document.getElementById('siteType').value;
            const status = document.getElementById('siteStatus').value;
            const priority = parseInt(document.getElementById('sitePriority').value) || 50;
            const note = document.getElementById('siteNote').value.trim();

            if (!name) { showToast('请输入资源站名称', 'error'); return; }
            if (!apiUrl) { showToast('请输入采集接口地址', 'error'); return; }

            const siteData = {
                name: name,
                site_url: siteUrl,
                api_url: apiUrl,
                type: type,
                status: status,
                priority: priority,
                note: note
            };

            try {
                const action = editingSite ? 'sites/update' : 'sites/add';
                const res = await fetch(API_BASE + '?action=' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(siteData)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast(data.message || '保存成功', 'success');
                cancelSiteEdit();
                refreshSites();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function deleteSite(name) {
            if (!confirm('确定要删除该资源站吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=sites/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name: name })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('删除成功', 'success');
                refreshSites();
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function fetchSiteVideos(name) {
            document.getElementById('siteVideosTitle').textContent = name + ' - 视频列表';
            document.getElementById('siteVideos').style.display = 'block';
            document.getElementById('siteVideosList').innerHTML = '<div class="loading">正在获取视频列表...</div>';
            document.getElementById('siteVideos').scrollIntoView({ behavior: 'smooth' });
            try {
                const res = await fetch(API_BASE + '?action=sites/fetch_videos&name=' + encodeURIComponent(name) + '&limit=10');
                const data = await res.json();
                if (!data.success) {
                    const isDnsFailure = data.error_type === 'dns_failure';
                    let errorHtml = '<div class="empty" style="text-align:left;padding:20px">';
                    errorHtml += '<div style="color:#f56c6c;font-weight:500;margin-bottom:8px">❌ ' + escapeHtml(data.message) + '</div>';
                    if (isDnsFailure) {
                        errorHtml += '<div style="font-size:12px;color:#909399;margin-bottom:12px">该资源站API域名无法解析，可能已经失效或被墙。建议将其标记为暂停状态。</div>';
                        errorHtml += '<button class="btn btn-sm btn-warning" onclick="toggleSiteStatus(\'' + escapeHtml(name) + '\', \'active\')">⚠️ 标记为暂停</button>';
                    }
                    errorHtml += '</div>';
                    document.getElementById('siteVideosList').innerHTML = errorHtml;
                    showToast('获取视频失败: ' + data.message, 'error');
                    return;
                }
                renderSiteVideos(data.videos || []);
            } catch (e) {
                document.getElementById('siteVideosList').innerHTML = '<div class="empty">获取失败: ' + escapeHtml(e.message) + '</div>';
                showToast('获取视频失败: ' + e.message, 'error');
            }
        }

        function renderSiteVideos(videos) {
            const container = document.getElementById('siteVideosList');
            if (videos.length === 0) {
                container.innerHTML = '<div class="empty">暂无视频数据</div>';
                return;
            }
            let html = '<div style="max-height:400px;overflow-y:auto">';
            videos.forEach((v, i) => {
                html += `
                    <div class="segment-item" style="border-bottom:1px solid #ebeef5">
                        <div style="flex:1">
                            <div style="font-weight:500;color:#303133">${i + 1}. ${escapeHtml(v.name || '未知')}</div>
                            <div style="font-size:12px;color:#909399;margin-top:4px;word-break:break-all;font-family:monospace">${escapeHtml(v.url || '')}</div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(v.url || '')}')">复制</button>
                            <button class="btn btn-sm btn-primary" onclick="analyzeFromSite('${escapeHtml(v.url || '')}')">分析</button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        function closeSiteVideos() {
            document.getElementById('siteVideos').style.display = 'none';
        }

        function analyzeFromSite(url) {
            document.getElementById('analyzeUrl').value = url;
            document.querySelector('.nav-item[data-page="analyze"]').click();
            setTimeout(() => analyzeVideo(), 300);
        }

        async function saveAutoLearnConfig() {
            const config = {
                enabled: document.getElementById('autoLearnEnabled').value === 'true',
                interval_days: parseInt(document.getElementById('intervalDays').value) || 3,
                videos_per_site: parseInt(document.getElementById('videosPerSite').value) || 5,
                max_sites_per_run: parseInt(document.getElementById('maxSitesPerRun').value) || 5,
                min_segments: parseInt(document.getElementById('minSegments').value) || 50,
                max_ad_percentage: parseInt(document.getElementById('maxAdPercentage').value) || 90
            };
            try {
                const res = await fetch(API_BASE + '?action=sites/auto_learn/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(config)
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                showToast('配置保存成功', 'success');
                refreshSites();
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        async function runAutoLearn() {
            if (!confirm('确定要立即执行自动学习吗？这可能需要一些时间。')) return;
            const useMultiThread = document.getElementById('autoLearnMultiThread').value === 'true';
            const concurrency = parseInt(document.getElementById('autoLearnConcurrency').value) || 5;
            const resultEl = document.getElementById('autoLearnResult');
            resultEl.style.display = 'block';
            resultEl.innerHTML = '<div class="loading">正在执行自动学习，请稍候... (' + (useMultiThread ? '多线程模式，并发 ' + concurrency : '串行模式') + ')</div>';
            try {
                const res = await fetch(API_BASE + '?action=sites/auto_learn/run', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        multi_thread: useMultiThread,
                        concurrency: concurrency
                    })
                });
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    console.error('JSON解析失败，响应内容:', jsonErr.message);
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (!data.success) throw new Error(data.message);
                let html = '<div style="padding:12px;background:#f0f9eb;border:1px solid #c2e7b0;border-radius:6px">';
                html += '<div style="font-weight:600;color:#67c23a;margin-bottom:8px">✅ ' + escapeHtml(data.message || '自动学习完成') + '</div>';
                html += '<div style="font-size:13px;color:#606266">';
                html += '处理站点: ' + data.sites_processed + ' 个 | ';
                html += '学习成功: <span style="color:#67c23a">' + data.total_learned + '</span> 个 | ';
                html += '失败: <span style="color:#f56c6c">' + data.total_failed + '</span> 个';
                if (data.total_time) {
                    html += ' | 耗时: <span style="color:#e6a23c">' + (data.total_time / 1000).toFixed(1) + 's</span>';
                }
                if (data.mode) {
                    html += ' | 模式: <span style="color:#909399">' + data.mode + '</span>';
                }
                html += '</div>';
                if (data.learned_domains && data.learned_domains.length > 0) {
                    html += '<div style="font-size:12px;color:#909399;margin-top:8px">更新域名: ' + data.learned_domains.join(', ') + '</div>';
                }
                if (data.details && data.details.length > 0) {
                    html += '<div style="margin-top:12px">';
                    data.details.forEach(d => {
                        html += '<div style="padding:8px;background:white;border-radius:4px;margin-bottom:6px;font-size:12px">';
                        html += '<strong>' + escapeHtml(d.site) + '</strong>: ';
                        html += '检查 ' + d.videos_checked + ' 个, 学习 ' + d.videos_learned + ' 个, 失败 ' + d.videos_failed + ' 个';
                        if (d.error) {
                            html += ' <span style="color:#f56c6c">(' + escapeHtml(d.error) + ')</span>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
                resultEl.innerHTML = html;
                showToast('自动学习完成', 'success');
                refreshSites();
            } catch (e) {
                resultEl.innerHTML = '<div style="padding:12px;background:#fef0f0;border:1px solid #fbc4c4;border-radius:6px;color:#f56c6c">学习失败: ' + escapeHtml(e.message) + '</div>';
                showToast('学习失败: ' + e.message, 'error');
            }
        }

        function populateSearchSiteSelect() {
            const select = document.getElementById('searchSiteSelect');
            if (!select || currentSites.length === 0) return;
            const currentValue = select.value;
            let html = '<option value="all">全部资源站</option>';
            currentSites.forEach(site => {
                if (site.status === 'active') {
                    html += '<option value="' + escapeHtml(site.name) + '">' + escapeHtml(site.name) + '</option>';
                }
            });
            select.innerHTML = html;
            if (currentValue) select.value = currentValue;
        }

        async function searchVideos() {
            const keyword = document.getElementById('searchKeyword').value.trim();
            if (!keyword) {
                showToast('请输入搜索关键词', 'warning');
                return;
            }

            const siteName = document.getElementById('searchSiteSelect').value;
            const maxSites = parseInt(document.getElementById('searchMaxSites').value) || 5;

            document.getElementById('searchLoading').style.display = 'block';
            document.getElementById('searchResults').style.display = 'none';

            try {
                let url;
                if (siteName === 'all') {
                    url = API_BASE + '?action=sites/search_all&keyword=' + encodeURIComponent(keyword) + '&max_sites=' + maxSites + '&limit_per_site=10&_t=' + Date.now();
                } else {
                    url = API_BASE + '?action=sites/search&name=' + encodeURIComponent(siteName) + '&keyword=' + encodeURIComponent(keyword) + '&limit=20&_t=' + Date.now();
                }

                const res = await fetch(url, {
                    cache: 'no-store',
                    headers: { 'Cache-Control': 'no-cache' }
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);

                renderSearchResults(data, siteName);
                document.getElementById('searchLoading').style.display = 'none';
                document.getElementById('searchResults').style.display = 'block';
            } catch (e) {
                document.getElementById('searchLoading').style.display = 'none';
                showToast('搜索失败: ' + e.message, 'error');
            }
        }

        function renderSearchResults(data, siteName) {
            const summaryEl = document.getElementById('searchSummary');
            const listEl = document.getElementById('searchVideoList');
            const actionsEl = document.getElementById('searchActions');
            const statsEl = document.getElementById('searchStats');

            currentSearchData = data;
            currentSearchSiteName = siteName;

            let totalVideos = 0;
            let sitesCount = 0;
            let successSites = 0;
            let failedSites = 0;

            if (siteName === 'all') {
                totalVideos = data.total_videos || 0;
                sitesCount = data.sites_searched || 0;
                const results = data.results || [];
                successSites = results.filter(r => (r.videos || []).length > 0).length;
                failedSites = results.filter(r => r.error).length;
                summaryEl.innerHTML = `搜索"${escapeHtml(data.keyword || '')}" - 搜索 ${sitesCount} 个站点，成功 ${successSites} 个，找到 ${totalVideos} 个视频`;
            } else {
                totalVideos = (data.videos || []).length;
                sitesCount = 1;
                summaryEl.innerHTML = `搜索"${escapeHtml(data.keyword || '')}" - 找到 ${totalVideos} 个视频`;
            }

            if (totalVideos > 0) {
                actionsEl.style.display = 'flex';
                statsEl.textContent = `共 ${totalVideos} 个视频可学习`;
            } else {
                actionsEl.style.display = 'none';
            }

            let html = '';

            if (siteName === 'all') {
                const results = data.results || [];
                const successResults = results.filter(r => (r.videos || []).length > 0);
                const failedResults = results.filter(r => r.error);
                
                if (successResults.length === 0 && failedResults.length === 0) {
                    html += '<div class="empty">未找到相关视频</div>';
                }
                
                successResults.forEach(siteResult => {
                    const videos = siteResult.videos || [];
                    html += `<div style="margin-bottom:16px">
                        <div style="padding:8px 12px;background:#f5f7fa;border-radius:6px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center">
                            <strong>${escapeHtml(siteResult.site || '')}</strong>
                            <span style="font-size:12px;color:#67c23a">
                                ${videos.length} 个视频
                            </span>
                        </div>
                        <div style="max-height:300px;overflow-y:auto;border:1px solid #ebeef5;border-radius:6px">`;
                    videos.forEach((v, i) => {
                        html += renderSearchVideoItem(v, siteResult.site);
                    });
                    html += '</div></div>';
                });

                if (failedResults.length > 0) {
                    html += `<div style="margin-top:16px;padding:12px;background:#fafafa;border:1px solid #ebeef5;border-radius:6px">
                        <div style="font-size:13px;color:#909399;margin-bottom:8px;cursor:pointer;user-select:none" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='none'?'block':'none'">
                            <span id="failedToggle">▶</span> ${failedResults.length} 个资源站暂不可用 <span style="font-size:11px">(点击展开/收起)</span>
                        </div>
                        <div id="failedSitesList" style="display:none;font-size:12px;color:#909399">`;
                    failedResults.forEach(r => {
                        html += `<div style="padding:4px 0;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between">
                            <span>${escapeHtml(r.site || '')}</span>
                            <span style="color:#c0c4cc">${escapeHtml(r.error || '未知错误')}</span>
                        </div>`;
                    });
                    html += '</div></div>';
                }
            } else {
                const videos = data.videos || [];
                if (videos.length > 0) {
                    html += '<div style="max-height:500px;overflow-y:auto;border:1px solid #ebeef5;border-radius:6px">';
                    videos.forEach((v, i) => {
                        html += renderSearchVideoItem(v, siteName);
                    });
                    html += '</div>';
                } else {
                    html += '<div class="empty">未找到相关视频</div>';
                }
            }

            listEl.innerHTML = html;
        }

        function renderSearchVideoItem(video, siteName) {
            const videoName = video.name || '未知';
            const firstUrl = video.first_url || video.url || '';
            const urls = video.urls || (firstUrl ? [{name: '默认', url: firstUrl}] : []);
            const domain = firstUrl ? (new URL(firstUrl).hostname) : '';

            let html = `<div class="segment-item" style="border-bottom:1px solid #ebeef5;flex-wrap:wrap">
                <div style="flex:1;min-width:200px">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                        <div style="font-weight:500;color:#303133">${escapeHtml(videoName)}</div>
                        <div style="display:flex;gap:4px">
                            <button class="btn btn-sm btn-success" onclick="learnFromVideoUrl('${escapeHtml(firstUrl)}', '${escapeHtml(videoName)}')" style="padding:4px 10px;font-size:11px">📚 学习</button>
                            <button class="btn btn-sm btn-primary" onclick="analyzeFromSite('${escapeHtml(firstUrl)}')" style="padding:4px 10px;font-size:11px">🔍 分析</button>
                        </div>
                    </div>
                    <div style="font-size:12px;color:#909399;margin-top:4px">
                        <span style="background:#ecf5ff;color:#409eff;padding:2px 8px;border-radius:4px;margin-right:8px">${escapeHtml(siteName || '')}</span>
                        ${domain ? '<span style="background:#f0f9eb;color:#67c23a;padding:2px 8px;border-radius:4px">' + escapeHtml(domain) + '</span>' : ''}
                        ${video.remarks ? '<span style="margin-left:8px">' + escapeHtml(video.remarks) + '</span>' : ''}
                    </div>`;

            if (urls.length > 0) {
                const videoId = 'vid_' + Math.random().toString(36).slice(2, 10);
                const total = urls.length;
                html += `<div style="margin-top:8px;font-size:12px" id="${videoId}_container">
                    <div id="${videoId}_visible">`;
                const showCount = Math.min(3, total);
                urls.slice(0, showCount).forEach((u, idx) => {
                    html += `<div style="padding:4px 0;display:flex;align-items:center;gap:8px">
                        <span style="color:#909399;white-space:nowrap">${escapeHtml(u.name || '剧集' + (idx + 1))}:</span>
                        <code style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;background:#f5f7fa;padding:2px 6px;border-radius:4px;font-size:11px" title="${escapeHtml(u.url)}">${escapeHtml(u.url)}</code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(u.url)}')" style="padding:2px 8px;font-size:11px">复制</button>
                    </div>`;
                });
                html += `</div>
                    <div id="${videoId}_hidden" style="display:none">`;
                urls.slice(showCount).forEach((u, idx) => {
                    html += `<div style="padding:4px 0;display:flex;align-items:center;gap:8px">
                        <span style="color:#909399;white-space:nowrap">${escapeHtml(u.name || '剧集' + (showCount + idx + 1))}:</span>
                        <code style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;background:#f5f7fa;padding:2px 6px;border-radius:4px;font-size:11px" title="${escapeHtml(u.url)}">${escapeHtml(u.url)}</code>
                        <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(u.url)}')" style="padding:2px 8px;font-size:11px">复制</button>
                    </div>`;
                });
                html += `</div>`;
                if (total > 3) {
                    html += `<button class="btn btn-sm btn-secondary" onclick="toggleVideoUrls('${videoId}', ${total})" id="${videoId}_btn" style="margin-top:6px;padding:2px 10px;font-size:11px">展开全部 ${total} 个播放源</button>`;
                }
                html += '</div>';
            }

            html += `</div>
            </div>`;

            return html;
        }

        let toggleVideoUrlsState = {};

        function toggleVideoUrls(videoId, total) {
            const hiddenEl = document.getElementById(videoId + '_hidden');
            const btnEl = document.getElementById(videoId + '_btn');
            const visibleEl = document.getElementById(videoId + '_visible');
            const expanded = toggleVideoUrlsState[videoId] || false;

            if (expanded) {
                hiddenEl.style.display = 'none';
                btnEl.textContent = '展开全部 ' + total + ' 个播放源';
                toggleVideoUrlsState[videoId] = false;
            } else {
                hiddenEl.style.display = 'block';
                btnEl.textContent = '收起播放源';
                toggleVideoUrlsState[videoId] = true;
            }
        }

        async function learnFromVideoUrl(url, videoName) {
            if (!url) {
                showToast('视频URL为空', 'error');
                return;
            }

            if (!confirm('确定要学习该视频的广告规则吗？\n\n视频: ' + (videoName || '未知') + '\n域名: ' + (new URL(url).hostname))) return;

            const btn = event.target;
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = '学习中...';

            try {
                const res = await fetch(API_BASE + '?action=sites/learn_video', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ url: url })
                });
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    showToast('学习失败: 服务器返回非JSON响应', 'error');
                    btn.disabled = false;
                    btn.textContent = originalText;
                    return;
                }

                if (data.success) {
                    showToast('学习成功！域名: ' + (data.domain || ''), 'success');
                    console.log('学习结果:', data);
                } else {
                    showToast('学习失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('学习请求失败: ' + e.message, 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        let currentSearchData = null;
        let currentSearchSiteName = 'all';
        let batchLearning = false;

        function collectAllVideoUrls() {
            const urls = [];
            if (!currentSearchData) return urls;

            if (currentSearchSiteName === 'all') {
                const results = currentSearchData.results || [];
                results.forEach(siteResult => {
                    const videos = siteResult.videos || [];
                    videos.forEach(v => {
                        const firstUrl = v.first_url || v.url || '';
                        if (firstUrl) {
                            urls.push({
                                url: firstUrl,
                                name: v.name || '未知',
                                site: siteResult.site || ''
                            });
                        }
                    });
                });
            } else {
                const videos = currentSearchData.videos || [];
                videos.forEach(v => {
                    const firstUrl = v.first_url || v.url || '';
                    if (firstUrl) {
                        urls.push({
                            url: firstUrl,
                            name: v.name || '未知',
                            site: currentSearchSiteName
                        });
                    }
                });
            }
            return urls;
        }

        function onMultiThreadToggle() {
            const enabled = document.getElementById('enableMultiThread').checked;
            const wrap = document.getElementById('concurrencyWrap');
            const badge = document.getElementById('multiThreadBadge');
            if (enabled) {
                wrap.style.opacity = '1';
                checkMultiThreadStatus();
            } else {
                wrap.style.opacity = '0.5';
                badge.style.display = 'none';
            }
        }

        async function checkMultiThreadStatus() {
            try {
                const res = await fetch(API_BASE + '?action=sites/multi_thread/status');
                const data = await res.json();
                const badge = document.getElementById('multiThreadBadge');
                if (data.available) {
                    badge.style.display = 'inline-block';
                    badge.textContent = '✓ 后端加速已就绪';
                } else {
                    badge.style.display = 'none';
                }
            } catch (e) {}
        }

        async function batchLearnAll() {
            if (batchLearning) {
                showToast('正在批量学习中，请稍候...', 'warning');
                return;
            }

            const videos = collectAllVideoUrls();
            if (videos.length === 0) {
                showToast('没有可学习的视频', 'warning');
                return;
            }

            const useMultiThread = document.getElementById('enableMultiThread').checked;
            const concurrency = parseInt(document.getElementById('concurrencyNum').value) || 5;

            let modeText = useMultiThread ? '后端多线程加速' : '前端并发请求';
            if (!confirm(`确定要批量学习 ${videos.length} 个视频吗？\n\n模式: ${modeText}\n并发数: ${concurrency} 个\n学习成功后将自动更新规则管理中的对应域名规则。`)) return;

            batchLearning = true;
            const resultEl = document.getElementById('batchResult');
            resultEl.style.display = 'block';
            resultEl.style.background = '#fffbe6';
            resultEl.style.border = '1px solid #ffe58f';
            resultEl.innerHTML = '<div class="loading">正在批量学习中，请稍候... (0/' + videos.length + ') 并发: ' + concurrency + ' (' + modeText + ')</div>';

            const startTime = Date.now();
            let successCount = 0;
            let failCount = 0;
            let learnedDomains = new Set();
            let results = [];

            if (useMultiThread) {
                try {
                    const urls = videos.map(v => v.url);
                    const res = await fetch(API_BASE + '?action=sites/learn_batch', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            urls: urls,
                            concurrency: concurrency,
                            multi_thread: true
                        })
                    });
                    
                    let data;
                    try {
                        const text = await res.text();
                        data = JSON.parse(text);
                    } catch (jsonErr) {
                        throw new Error('服务器返回非JSON响应');
                    }

                    if (data.success) {
                        successCount = data.success_count || 0;
                        failCount = data.fail_count || 0;
                        learnedDomains = new Set(data.learned_domains || []);
                        results = (data.results || []).map((r, i) => ({
                            name: videos[i]?.name || '',
                            site: videos[i]?.site || '',
                            success: r.success,
                            domain: r.domain || '',
                            segments: r.segments_count || 0,
                            ad_count: r.ad_count || 0,
                            message: r.message || '',
                            duration: r.duration || 0
                        }));

                        const totalTime = data.total_time ? (data.total_time / 1000).toFixed(1) : '?';
                        renderBatchResults(resultEl, videos, results, successCount, failCount, learnedDomains, totalTime, data.mode || 'backend');
                    } else {
                        throw new Error(data.message || '批量学习失败');
                    }
                } catch (e) {
                    resultEl.innerHTML = '<div class="loading">后端批量失败，回退到前端并发模式...</div>';
                    await batchLearnFrontend(videos, concurrency, resultEl);
                }
            } else {
                await batchLearnFrontend(videos, concurrency, resultEl);
            }

            batchLearning = false;
            showToast(`批量学习完成：成功 ${successCount}，失败 ${failCount}`, successCount > 0 ? 'success' : 'error');
        }

        async function batchLearnFrontend(videos, concurrency, resultEl) {
            let completedCount = 0;
            let successCount = 0;
            let failCount = 0;
            const results = [];
            const learnedDomains = new Set();

            async function learnOne(video) {
                try {
                    const res = await fetch(API_BASE + '?action=sites/learn_video', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ url: video.url })
                    });
                    
                    let data;
                    try {
                        const text = await res.text();
                        data = JSON.parse(text);
                    } catch (jsonErr) {
                        return { success: false, message: '服务器返回非JSON响应' };
                    }

                    if (data.success) {
                        successCount++;
                        if (data.domain) {
                            learnedDomains.add(data.domain);
                        }
                        results.push({
                            name: video.name,
                            site: video.site,
                            success: true,
                            domain: data.domain,
                            segments: data.segments_count,
                            ad_count: data.ad_count
                        });
                    } else {
                        failCount++;
                        results.push({
                            name: video.name,
                            site: video.site,
                            success: false,
                            message: data.message
                        });
                    }
                } catch (e) {
                    failCount++;
                    results.push({
                        name: video.name,
                        site: video.site,
                        success: false,
                        message: e.message
                    });
                }

                completedCount++;
                resultEl.innerHTML = '<div class="loading">正在批量学习中，请稍候... (' + completedCount + '/' + videos.length + ') 并发: ' + concurrency + ' (前端模式)</div>';
                document.getElementById('searchStats').textContent = `已完成 ${completedCount}/${videos.length}，成功 ${successCount}，失败 ${failCount}`;
            }

            const queue = [...videos];
            const workers = [];
            for (let w = 0; w < concurrency; w++) {
                workers.push((async () => {
                    while (queue.length > 0 && batchLearning) {
                        const video = queue.shift();
                        await learnOne(video);
                    }
                })());
            }
            await Promise.all(workers);

            renderBatchResults(resultEl, videos, results, successCount, failCount, learnedDomains, null, 'frontend');
        }

        function renderBatchResults(resultEl, videos, results, successCount, failCount, learnedDomains, totalTime, mode) {
            const modeText = mode === 'backend' ? '后端多线程' : '前端并发';
            let html = '<div style="margin-bottom:8px;font-weight:600">';
            if (successCount > 0) {
                html += '<span style="color:#67c23a">✅ 批量学习完成</span>';
                resultEl.style.background = '#f0f9eb';
                resultEl.style.border = '1px solid #c2e7b0';
            } else {
                html += '<span style="color:#f56c6c">❌ 批量学习失败</span>';
                resultEl.style.background = '#fef0f0';
                resultEl.style.border = '1px solid #fbc4c4';
            }
            html += '</div>';
            html += '<div style="font-size:13px;color:#606266;margin-bottom:8px">';
            html += `总计: ${videos.length} 个 | 成功: <span style="color:#67c23a">${successCount}</span> | 失败: <span style="color:#f56c6c">${failCount}</span>`;
            if (learnedDomains.size > 0) {
                html += ` | 更新域名: <span style="color:#409eff">${learnedDomains.size}</span> 个`;
            }
            if (totalTime) {
                html += ` | 耗时: <span style="color:#e6a23c">${totalTime}s</span>`;
            }
            html += ` | 模式: <span style="color:#909399">${modeText}</span>`;
            html += '</div>';

            if (learnedDomains.size > 0) {
                html += '<div style="font-size:12px;color:#909399;margin-bottom:8px">';
                html += '已更新域名: ' + Array.from(learnedDomains).join(', ');
                html += '</div>';
            }

            const failedResults = results.filter(r => !r.success);
            if (failedResults.length > 0 && failedResults.length <= 10) {
                html += '<div style="font-size:12px"><details><summary style="cursor:pointer;color:#909399">查看失败详情</summary><div style="margin-top:8px">';
                failedResults.forEach(r => {
                    html += `<div style="padding:4px 0;color:#f56c6c">${escapeHtml(r.name)} - ${escapeHtml(r.message || '未知错误')}</div>`;
                });
                html += '</div></details></div>';
            }

            resultEl.innerHTML = html;
            document.getElementById('searchStats').textContent = `完成 - 成功 ${successCount}，失败 ${failCount}，更新 ${learnedDomains.size} 个域名`;
        }

        let batchAnalyzing = false;

        async function batchAnalyzeAll() {
            if (batchAnalyzing) {
                showToast('正在批量分析中，请稍候...', 'warning');
                return;
            }

            const videos = collectAllVideoUrls();
            if (videos.length === 0) {
                showToast('没有可分析的视频', 'warning');
                return;
            }

            const useMultiThread = document.getElementById('enableMultiThread').checked;
            const concurrency = parseInt(document.getElementById('concurrencyNum').value) || 5;

            let modeText = useMultiThread ? '后端多线程加速' : '串行分析';
            if (!confirm(`确定要批量分析 ${videos.length} 个视频吗？\n\n模式: ${modeText}\n并发数: ${concurrency} 个`)) return;

            batchAnalyzing = true;
            const resultEl = document.getElementById('batchResult');
            resultEl.style.display = 'block';
            resultEl.style.background = '#fffbe6';
            resultEl.style.border = '1px solid #ffe58f';
            resultEl.innerHTML = '<div class="loading">正在批量分析中，请稍候... (0/' + videos.length + ') 并发: ' + concurrency + ' (' + modeText + ')</div>';

            const startTime = Date.now();

            try {
                const urls = videos.map(v => v.url);
                const res = await fetch(API_BASE + '?action=sites/analyze_batch', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        urls: urls,
                        concurrency: concurrency,
                        multi_thread: useMultiThread
                    })
                });
                const data = await res.json();

                if (data.success) {
                    const successCount = data.success_count || 0;
                    const failCount = data.fail_count || 0;
                    const totalTime = data.total_time ? (data.total_time / 1000).toFixed(1) : '?';
                    const results = data.results || [];

                    let html = '<div style="margin-bottom:8px;font-weight:600">';
                    if (successCount > 0) {
                        html += '<span style="color:#67c23a">✅ 批量分析完成</span>';
                        resultEl.style.background = '#f0f9eb';
                        resultEl.style.border = '1px solid #c2e7b0';
                    } else {
                        html += '<span style="color:#f56c6c">❌ 批量分析失败</span>';
                        resultEl.style.background = '#fef0f0';
                        resultEl.style.border = '1px solid #fbc4c4';
                    }
                    html += '</div>';
                    html += '<div style="font-size:13px;color:#606266;margin-bottom:8px">';
                    html += `总计: ${videos.length} 个 | 成功: <span style="color:#67c23a">${successCount}</span> | 失败: <span style="color:#f56c6c">${failCount}</span>`;
                    html += ` | 耗时: <span style="color:#e6a23c">${totalTime}s</span>`;
                    html += ` | 模式: <span style="color:#909399">${data.mode || 'unknown'}</span>`;
                    html += '</div>';

                    if (results.length > 0 && results.length <= 20) {
                        html += '<div style="font-size:12px"><details open><summary style="cursor:pointer;color:#909399">查看分析详情</summary><div style="margin-top:8px">';
                        results.forEach((r, idx) => {
                            const v = videos[idx] || {};
                            if (r.success) {
                                const stats = r.stats || {};
                                html += `<div style="padding:6px 0;border-bottom:1px solid #ebeef5">`;
                                html += `<div style="font-weight:500">${escapeHtml(v.name || '未知')}</div>`;
                                html += `<div style="font-size:11px;color:#909399;word-break:break-all;font-family:monospace">${escapeHtml(r.url || '')}</div>`;
                                html += `<div style="font-size:12px;color:#606266;margin-top:4px">`;
                                html += `总片段: ${stats.totalSegments || 0} | 广告: <span style="color:#f56c6c">${stats.adSegments || 0}</span>`;
                                if (r.fast_mode) html += ' <span class="tag tag-blue" style="font-size:10px">快速模式</span>';
                                html += `</div></div>`;
                            } else {
                                html += `<div style="padding:6px 0;color:#f56c6c;font-size:12px;border-bottom:1px solid #ebeef5">`;
                                html += `${escapeHtml(v.name || '未知')} - ${escapeHtml(r.message || '未知错误')}`;
                                html += `</div>`;
                            }
                        });
                        html += '</div></details></div>';
                    }

                    resultEl.innerHTML = html;
                    document.getElementById('searchStats').textContent = `完成 - 成功 ${successCount}，失败 ${failCount}，耗时 ${totalTime}s`;
                    showToast(`批量分析完成：成功 ${successCount}，失败 ${failCount}`, successCount > 0 ? 'success' : 'error');
                } else {
                    throw new Error(data.message || '批量分析失败');
                }
            } catch (e) {
                resultEl.style.background = '#fef0f0';
                resultEl.style.border = '1px solid #fbc4c4';
                resultEl.innerHTML = '<div style="color:#f56c6c">批量分析失败: ' + escapeHtml(e.message) + '</div>';
                showToast('批量分析失败: ' + e.message, 'error');
            }

            batchAnalyzing = false;
        }

        function clearSearchResults() {
            document.getElementById('searchKeyword').value = '';
            document.getElementById('searchResults').style.display = 'none';
            document.getElementById('searchLoading').style.display = 'none';
            document.getElementById('batchResult').style.display = 'none';
            document.getElementById('searchActions').style.display = 'none';
            currentSearchData = null;
        }

        function copyText(text) {
            if (!text) return;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast('复制成功', 'success');
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.top = '-1000px';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showToast('复制成功', 'success');
            } catch (e) {
                showToast('复制失败，请手动复制', 'error');
            }
            document.body.removeChild(textarea);
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('mxadmin_theme') || 'default';
            switchTheme(savedTheme, false);
        }

        function switchTheme(themeName, save = true) {
            if (themeName === 'default') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', themeName);
            }

            document.querySelectorAll('.theme-dot').forEach(dot => {
                if (dot.dataset.themeName === themeName) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });

            if (save) {
                localStorage.setItem('mxadmin_theme', themeName);
            }
        }

        function initAccessPreview() {
            const protocol = window.location.protocol;
            const host = window.location.host;
            const path = window.location.pathname;
            const baseDir = path.substring(0, path.lastIndexOf('/'));
            const base = protocol + '//' + host + baseDir;

            document.getElementById('preview-admin').textContent = base + '/mxadmin.php';
            document.getElementById('preview-api').textContent = base + '/mx.php?action=analyze&url=';
            document.getElementById('preview-parse').textContent = base + '/mx.php?action=mxjx&url=';
            document.getElementById('preview-player').textContent = base + '/mx.php?action=mxjx/info&url=';
            document.getElementById('preview-official-replace').textContent = base + '/mx.php?action=official_replace/info&url=';
            document.getElementById('preview-moxi').textContent = base + '/mx.php?action=moxi&url=';
            
            const moxiApiUrl = base + '/mx.php?action=moxi&url=';
            const moxiApiEl = document.getElementById('moxi-api-url');
            if (moxiApiEl) {
                moxiApiEl.textContent = moxiApiUrl;
            }
        }

        let currentOfficialConfig = null;
        let editingPlatformIndex = -1;

        async function loadOfficialReplaceConfig() {
            try {
                const res = await fetch(API_BASE + '?action=official_replace/config&_t=' + Date.now());
                const data = await res.json();
                if (data.success && data.config) {
                    currentOfficialConfig = data.config;
                    const cfg = data.config;
                    document.getElementById('orTotalPlatforms').textContent = (cfg.platforms || []).length;
                    document.getElementById('orStatus').textContent = cfg.enabled ? '已启用' : '已禁用';
                    document.getElementById('orStatus').style.color = cfg.enabled ? '#67c23a' : '#f56c6c';
                    document.getElementById('orSearchSites').textContent = (cfg.search_sites || []).length > 0 ? (cfg.search_sites || []).length + '个' : '全部';
                    document.getElementById('orThreshold').textContent = cfg.match_threshold || 60;

                    document.getElementById('orEnabled').value = cfg.enabled ? 'true' : 'false';
                    document.getElementById('orThresholdInput').value = cfg.match_threshold || 60;
                    document.getElementById('orMaxSites').value = cfg.max_search_sites || 5;
                    document.getElementById('orSearchSitesInput').value = (cfg.search_sites || []).join(',');

                    const base = window.location.protocol + '//' + window.location.host + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'));
                    document.getElementById('api-resolve-url').textContent = base + '/mx.php?action=official_replace/resolve&url=';
                    document.getElementById('api-info-url').textContent = base + '/mx.php?action=official_replace/info&url=';

                    renderOfficialPlatforms(cfg.platforms || []);
                }
            } catch (e) {
                showToast('加载官替配置失败: ' + e.message, 'error');
            }
        }

        function renderOfficialPlatforms(platforms) {
            const listEl = document.getElementById('officialPlatformsList');
            if (platforms.length === 0) {
                listEl.innerHTML = '<div class="empty">暂无平台配置</div>';
                return;
            }

            let html = '<table class="rule-table"><thead><tr><th>平台名称</th><th>域名</th><th>状态</th><th>优先级</th><th>操作</th></tr></thead><tbody>';
            platforms.forEach((p, index) => {
                html += `<tr>
                    <td><strong>${escapeHtml(p.name || '')}</strong></td>
                    <td><code>${escapeHtml(p.domain || '')}</code></td>
                    <td><span style="color:${p.enabled ? '#67c23a' : '#f56c6c'}">${p.enabled ? '启用' : '禁用'}</span></td>
                    <td>${p.priority || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editOfficialPlatform(${index})">编辑</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteOfficialPlatform(${index})">删除</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            listEl.innerHTML = html;
        }

        async function saveOfficialReplaceConfig() {
            if (!currentOfficialConfig) return;
            
            const newConfig = JSON.parse(JSON.stringify(currentOfficialConfig));
            newConfig.enabled = document.getElementById('orEnabled').value === 'true';
            newConfig.match_threshold = parseInt(document.getElementById('orThresholdInput').value) || 60;
            newConfig.max_search_sites = parseInt(document.getElementById('orMaxSites').value) || 5;
            
            const sitesInput = document.getElementById('orSearchSitesInput').value.trim();
            newConfig.search_sites = sitesInput ? sitesInput.split(',').map(s => s.trim()).filter(s => s) : [];

            try {
                const res = await fetch(API_BASE + '?action=official_replace/config/save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(newConfig)
                });
                const data = await res.json();
                if (data.success) {
                    showToast('配置保存成功', 'success');
                    loadOfficialReplaceConfig();
                } else {
                    showToast('保存失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('保存失败: ' + e.message, 'error');
            }
        }

        function addOfficialPlatform() {
            editingPlatformIndex = -1;
            showPlatformEditor({
                name: '',
                domain: '',
                enabled: true,
                pattern: '',
                title_selector: '',
                priority: 10
            });
        }

        function editOfficialPlatform(index) {
            if (!currentOfficialConfig || !currentOfficialConfig.platforms) return;
            editingPlatformIndex = index;
            showPlatformEditor(currentOfficialConfig.platforms[index]);
        }

        function showPlatformEditor(platform) {
            const html = `<div id="platformEditorModal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;z-index:1000">
                <div style="background:white;border-radius:8px;padding:24px;width:90%;max-width:500px;max-height:90vh;overflow-y:auto">
                    <h3 style="margin-bottom:16px">${editingPlatformIndex >= 0 ? '编辑' : '添加'}平台</h3>
                    <div class="form-group"><label>平台名称</label><input type="text" id="pe-name" value="${escapeHtml(platform.name || '')}"></div>
                    <div class="form-group"><label>域名</label><input type="text" id="pe-domain" placeholder="如: v.qq.com" value="${escapeHtml(platform.domain || '')}"></div>
                    <div class="form-group"><label>启用</label><select id="pe-enabled"><option value="true" ${platform.enabled ? 'selected' : ''}>启用</option><option value="false" ${!platform.enabled ? 'selected' : ''}>禁用</option></select></div>
                    <div class="form-group"><label>URL 匹配正则</label><input type="text" id="pe-pattern" placeholder="如: /v\\.qq\\.com\\/.*?(?:vid=|\\/)([a-zA-Z0-9]+)/i" value="${escapeHtml(platform.pattern || '')}"></div>
                    <div class="form-group"><label>标题选择器</label><input type="text" id="pe-title_selector" placeholder="如: meta[property=og:title]" value="${escapeHtml(platform.title_selector || '')}"></div>
                    <div class="form-group"><label>优先级</label><input type="number" id="pe-priority" value="${platform.priority || 10}"></div>
                    <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px">
                        <button class="btn btn-secondary" onclick="closePlatformEditor()">取消</button>
                        <button class="btn btn-primary" onclick="savePlatformEditor()">保存</button>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
        }

        function closePlatformEditor() {
            const modal = document.getElementById('platformEditorModal');
            if (modal) modal.remove();
        }

        async function savePlatformEditor() {
            const platformData = {
                name: document.getElementById('pe-name').value.trim(),
                domain: document.getElementById('pe-domain').value.trim(),
                enabled: document.getElementById('pe-enabled').value === 'true',
                pattern: document.getElementById('pe-pattern').value.trim(),
                title_selector: document.getElementById('pe-title_selector').value.trim(),
                priority: parseInt(document.getElementById('pe-priority').value) || 10
            };

            if (!platformData.name || !platformData.domain) {
                showToast('请填写平台名称和域名', 'error');
                return;
            }

            try {
                const action = editingPlatformIndex >= 0 ? 'official_replace/platform/update' : 'official_replace/platform/add';
                const body = editingPlatformIndex >= 0 
                    ? { index: editingPlatformIndex, ...platformData }
                    : platformData;

                const res = await fetch(API_BASE + '?action=' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();
                if (data.success) {
                    showToast(editingPlatformIndex >= 0 ? '更新成功' : '添加成功', 'success');
                    closePlatformEditor();
                    loadOfficialReplaceConfig();
                } else {
                    showToast('操作失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('操作失败: ' + e.message, 'error');
            }
        }

        async function deleteOfficialPlatform(index) {
            if (!confirm('确定要删除这个平台吗？')) return;
            try {
                const res = await fetch(API_BASE + '?action=official_replace/platform/delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ index: index })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('删除成功', 'success');
                    loadOfficialReplaceConfig();
                } else {
                    showToast('删除失败: ' + (data.message || '未知错误'), 'error');
                }
            } catch (e) {
                showToast('删除失败: ' + e.message, 'error');
            }
        }

        async function testOfficialReplace() {
            const url = document.getElementById('officialTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }

            const resultEl = document.getElementById('officialTestResult');
            const infoEl = document.getElementById('officialTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在解析...</div>';

            try {
                const res = await fetch(API_BASE + '?action=official_replace/resolve&url=' + encodeURIComponent(url) + '&_t=' + Date.now());

                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    console.error('JSON解析失败，响应内容:', jsonErr.message);
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (data.success) {
                    let seasonHtml = '';
                    if (data.season) {
                        seasonHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">季数</span><div style="color:#409eff;font-weight:600">${escapeHtml(data.season)}</div></div>`;
                    }
                    let episodeHtml = '';
                    if (data.episode) {
                        let epTarget = '';
                        if (data.target_episode) {
                            epTarget = `<div style="font-size:11px;color:#67c23a">→ ${escapeHtml(data.target_episode)}</div>`;
                        }
                        episodeHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">集数</span><div style="color:#409eff;font-weight:600">${escapeHtml(data.episode)}${epTarget}</div></div>`;
                    }
                    let partHtml = '';
                    if (data.part) {
                        partHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">篇章</span><div style="color:#e6a23c;font-weight:600">${escapeHtml(data.part)}</div></div>`;
                    }
                    let versionHtml = '';
                    if (data.version) {
                        versionHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">版本</span><div style="color:#909399;font-weight:500">${escapeHtml(data.version)}</div></div>`;
                    }
                    let seasonMatchHtml = '';
                    if (data.season_match !== undefined) {
                        seasonMatchHtml = `<span style="color:${data.season_match ? '#67c23a' : '#f56c6c'};font-size:11px;margin-left:6px">
                            ${data.season_match ? '✓' : '✗'}季
                        </span>`;
                    }
                    let episodeMatchHtml = '';
                    if (data.episode_match !== undefined && data.episode) {
                        episodeMatchHtml = `<span style="color:${data.episode_match ? '#67c23a' : '#f56c6c'};font-size:11px;margin-left:6px">
                            ${data.episode_match ? '✓' : '✗'}集
                        </span>`;
                    }

                    let html = `
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                        <div style="background:#ecf5ff;border:1px solid #d9ecff;border-radius:10px;padding:14px">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                                <div style="width:6px;height:16px;background:#409eff;border-radius:3px"></div>
                                <span style="font-weight:600;color:#409eff;font-size:14px">平台解析</span>
                            </div>
                            <div style="font-size:13px;line-height:1.6">
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">平台</span><div style="font-weight:500">${escapeHtml(data.platform || '')}</div></div>
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">原始标题</span><div style="font-weight:500;word-break:break-all">${escapeHtml(data.video_title || '')}</div></div>
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">基础名称</span><div style="font-weight:600;color:#606266">${escapeHtml(data.base_title || '')}</div></div>
                                ${seasonHtml}
                                ${episodeHtml}
                                ${partHtml}
                                ${versionHtml}
                            </div>
                        </div>
                        <div style="background:#f0f9eb;border:1px solid #e1f3d8;border-radius:10px;padding:14px">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                                <div style="width:6px;height:16px;background:#67c23a;border-radius:3px"></div>
                                <span style="font-weight:600;color:#67c23a;font-size:14px">资源站匹配</span>
                                <span style="margin-left:auto;background:#67c23a;color:white;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:600">${data.match_score || 0}%</span>
                            </div>
                            <div style="font-size:13px;line-height:1.6">
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">资源站</span><div style="font-weight:500">${escapeHtml(data.site || '')}</div></div>
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">匹配度</span><div style="font-weight:600;color:#67c23a;font-size:16px">${data.match_score || 0}% ${seasonMatchHtml}${episodeMatchHtml}</div></div>
                                <div style="margin-bottom:6px;font-size:11px;color:#909399">基础匹配度: ${data.base_score || 0}%</div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:6px">
                                    <div><span style="color:#909399;font-size:12px">目标集数</span><div style="font-weight:600;color:#409eff">${data.episode || '-'}</div></div>
                                    <div><span style="color:#909399;font-size:12px">匹配集数</span><div style="font-weight:600;color:${data.episode_match ? '#67c23a' : '#e6a23c'}">${data.target_episode || '自动匹配'}</div></div>
                                </div>
                                ${data.episodes ? `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">总集数</span><div style="font-weight:500;color:#606266">共 ${data.episodes} 集</div></div>` : ''}
                                <div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">M3U8地址</span><div style="font-size:11px;word-break:break-all;color:#606266;font-family:monospace">${escapeHtml(data.m3u8_url || '')}</div></div>
                            </div>
                        </div>
                    </div>
                    <div style="background:#f0f9eb;padding:12px;border-radius:8px;border:1px solid #e1f3d8;text-align:center">
                        <span style="color:#67c23a;font-weight:600">✓ 解析成功</span>
                    </div>`;
                    
                    if (data.alternatives && data.alternatives.length > 1) {
                        html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px;color:#606266">其他候选结果</div>';
                        data.alternatives.slice(1, 6).forEach(v => {
                            const vEp = v.video_episode ? `第${v.video_episode}集` : '';
                            const vSeason = v.video_season ? `第${v.video_season}季` : '';
                            const vEpInfo = [vSeason, vEp].filter(Boolean).join(' · ');
                            const vTotal = v.video && v.video.total_episodes ? `共${v.video.total_episodes}集` : '';
                            html += `<div style="padding:10px;background:#f5f7fa;border-radius:8px;margin-bottom:8px;display:flex;justify-content:space-between;align-items:center">
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(v.video?.name || v.name || '未知')}</div>
                                    <div style="font-size:12px;color:#909399;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                        <span>${escapeHtml(v.site || '')}</span>
                                        ${vEpInfo ? `<span style="color:#409eff">${escapeHtml(vEpInfo)}</span>` : ''}
                                        ${vTotal ? `<span style="color:#67c23a">${escapeHtml(vTotal)}</span>` : ''}
                                        <span style="color:#e6a23c">匹配度: ${v.score || 0}%</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary" style="margin-left:8px;flex-shrink:0" onclick="learnFromVideoUrl('${escapeHtml(v.video?.first_url || v.video?.url || v.first_url || v.url || '')}', '${escapeHtml(v.video?.name || v.name || '')}')">学习</button>
                            </div>`;
                        });
                        html += '</div>';
                    }
                    infoEl.innerHTML = html;
                } else {
                    let seasonHtml = '';
                    if (data.season) {
                        seasonHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">季数</span><div style="color:#409eff;font-weight:600">${escapeHtml(data.season)}</div></div>`;
                    }
                    let episodeHtml = '';
                    if (data.episode) {
                        episodeHtml = `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">集数</span><div style="color:#409eff;font-weight:600">${escapeHtml(data.episode)}</div></div>`;
                    }

                    let html = `
                    <div style="display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:16px">
                        <div style="background:#ecf5ff;border:1px solid #d9ecff;border-radius:10px;padding:14px">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px">
                                <div style="width:6px;height:16px;background:#409eff;border-radius:3px"></div>
                                <span style="font-weight:600;color:#409eff;font-size:14px">平台解析</span>
                            </div>
                            <div style="font-size:13px;line-height:1.6">
                                ${data.platform ? `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">平台</span><div style="font-weight:500">${escapeHtml(data.platform)}</div></div>` : ''}
                                ${data.video_title ? `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">原始标题</span><div style="font-weight:500;word-break:break-all">${escapeHtml(data.video_title)}</div></div>` : ''}
                                ${data.base_title ? `<div style="margin-bottom:6px"><span style="color:#909399;font-size:12px">基础名称</span><div style="font-weight:600;color:#606266">${escapeHtml(data.base_title)}</div></div>` : ''}
                                ${seasonHtml}
                                ${episodeHtml}
                            </div>
                        </div>
                    </div>
                    <div style="background:#fef0f0;padding:12px;border-radius:8px;border:1px solid #fbc4c4;text-align:center">
                        <span style="color:#f56c6c;font-weight:600">✗ ${escapeHtml(data.message || '解析失败')}</span>
                    </div>`;
                    
                    if (data.candidates && data.candidates.length > 0) {
                        html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">候选结果 (匹配度不足):</div>';
                        data.candidates.forEach(v => {
                            const vEp = v.video_episode ? `第${v.video_episode}集` : '';
                            const vSeason = v.video_season ? `第${v.video_season}季` : '';
                            const vEpInfo = [vSeason, vEp].filter(Boolean).join(' · ');
                            const vTotal = v.video && v.video.total_episodes ? `共${v.video.total_episodes}集` : '';
                            html += `<div style="padding:8px;background:#f5f7fa;border-radius:6px;margin-bottom:6px">
                                <div style="font-weight:500">${escapeHtml(v.video?.name || v.name || '未知')}</div>
                                <div style="font-size:12px;color:#909399;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                    <span>${escapeHtml(v.site || '')}</span>
                                    ${vEpInfo ? `<span style="color:#409eff">${escapeHtml(vEpInfo)}</span>` : ''}
                                    ${vTotal ? `<span style="color:#67c23a">${escapeHtml(vTotal)}</span>` : ''}
                                    <span style="color:#e6a23c">匹配度: ${v.score || 0}%</span>
                                </div>
                            </div>`;
                        });
                        html += '</div>';
                    }
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        async function testMoxiApi() {
            const url = document.getElementById('moxiTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }

            const resultEl = document.getElementById('moxiTestResult');
            const infoEl = document.getElementById('moxiTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在解析...</div>';

            try {
                const res = await fetch(API_BASE + '?action=moxi&url=' + encodeURIComponent(url) + '&_t=' + Date.now());
                const data = await res.json();
                
                if (data.code === 200) {
                    let html = `<div style="background:#f0f9eb;padding:16px;border-radius:8px;border:1px solid #e1f3d8">
                        <div style="color:#67c23a;font-weight:600;margin-bottom:8px">✓ 解析成功</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>状态码:</strong> <span style="color:#67c23a">${data.code}</span></p>
                            <p><strong>剧名 (jm):</strong> ${escapeHtml(data.jm || '')}</p>
                            <p><strong>集数 (js):</strong> ${escapeHtml(data.js || '')}</p>
                            <p><strong>消息 (msg):</strong> ${escapeHtml(data.msg || '')}</p>
                            <p><strong>响应时间 (time):</strong> ${escapeHtml(data.time || '')}</p>
                            <p><strong>开发者 (kfz):</strong> ${escapeHtml(data.kfz || '')}</p>
                            <p><strong>播放地址 (url):</strong></p>
                            <div style="background:#fff;padding:8px;border-radius:4px;margin-top:4px;border:1px solid #e1f3d8">
                                <code style="word-break:break-all;font-size:11px">${escapeHtml(data.url || '')}</code>
                            </div>
                        </div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-sm btn-primary" onclick="window.open('${escapeHtml(data.url || '')}', '_blank')">新窗口播放</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(data.url || '')}')">复制播放地址</button>
                        </div>
                    </div>`;
                    
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    
                    infoEl.innerHTML = html;
                } else {
                    let html = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600;margin-bottom:8px">✗ 解析失败</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>状态码:</strong> <span style="color:#f56c6c">${data.code}</span></p>
                            <p><strong>消息:</strong> ${escapeHtml(data.msg || '未知错误')}</p>
                            <p><strong>响应时间:</strong> ${escapeHtml(data.time || '')}</p>
                            <p><strong>开发者:</strong> ${escapeHtml(data.kfz || '')}</p>
                        </div>
                    </div>`;
                    
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        async function testMxjxApi() {
            const url = document.getElementById('moxiTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }
            const resultEl = document.getElementById('moxiTestResult');
            const infoEl = document.getElementById('moxiTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在去广告处理...</div>';

            try {
                const mxjxUrl = API_BASE + '?action=mxjx/info&url=' + encodeURIComponent(url) + '&_t=' + Date.now();
                const res = await fetch(mxjxUrl);
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    infoEl.innerHTML = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600">✗ 返回非JSON响应</div>
                        <pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:300px;margin-top:8px">${escapeHtml(text.substring(0, 1000))}</pre>
                    </div>`;
                    return;
                }

                if (data.success || data.code === 200) {
                    const innerData = data.data || data;
                    const playUrl = innerData.play_url || (API_BASE + '?action=mxjx&url=' + encodeURIComponent(url));
                    let html = `<div style="background:#f0f9eb;padding:16px;border-radius:8px;border:1px solid #e1f3d8">
                        <div style="color:#67c23a;font-weight:600;margin-bottom:8px">✓ 去广告处理成功</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>原始URL:</strong> <code style="word-break:break-all">${escapeHtml(innerData.original_url || url)}</code></p>
                            <p><strong>媒体URL:</strong> <code style="word-break:break-all">${escapeHtml(innerData.media_url || '')}</code></p>
                            <p><strong>域名:</strong> ${escapeHtml(innerData.domain || '')}</p>
                            <p><strong>有域名规则:</strong> ${innerData.has_domain_rules ? '是' : '否'}</p>
                            ${innerData.stats ? `<p><strong>统计:</strong> 总${innerData.stats.total_segments || 0} 保留${innerData.stats.kept_segments || 0} 移除${innerData.stats.removed_segments || 0} 广告占比${innerData.stats.ad_percentage || 0}%</p>` : ''}
                            <p><strong>无广告播放地址:</strong></p>
                            <div style="background:#fff;padding:8px;border-radius:4px;margin-top:4px;border:1px solid #e1f3d8">
                                <code style="word-break:break-all;font-size:11px">${escapeHtml(playUrl)}</code>
                            </div>
                        </div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-sm btn-primary" onclick="window.open('${escapeHtml(playUrl)}', '_blank')">新窗口播放</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(playUrl)}')">复制播放地址</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(mxjxUrl)}')">复制接口URL</button>
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                } else {
                    let html = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600;margin-bottom:8px">✗ 去广告处理失败</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>消息:</strong> ${escapeHtml(data.message || data.msg || '未知错误')}</p>
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        async function testAnalyzeApi() {
            const url = document.getElementById('moxiTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }
            const resultEl = document.getElementById('moxiTestResult');
            const infoEl = document.getElementById('moxiTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在分析视频（可能需要 30-60 秒）...</div>';

            try {
                const analyzeUrl = API_BASE + '?action=analyze&url=' + encodeURIComponent(url) + '&auto_learn=0&_t=' + Date.now();
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 180000);
                const res = await fetch(analyzeUrl, { signal: controller.signal });
                clearTimeout(timeoutId);
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    infoEl.innerHTML = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600">✗ 返回非JSON响应</div>
                        <pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:300px;margin-top:8px">${escapeHtml(text.substring(0, 1000))}</pre>
                    </div>`;
                    return;
                }

                if (data.success) {
                    const stats = data.stats || {};
                    let html = `<div style="background:#f0f9eb;padding:16px;border-radius:8px;border:1px solid #e1f3d8">
                        <div style="color:#67c23a;font-weight:600;margin-bottom:8px">✓ 分析成功</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>域名:</strong> ${escapeHtml(data.domain || '')}</p>
                            <p><strong>是否主播放列表:</strong> ${data.playlist && data.playlist.isMaster ? '是' : '否'}</p>
                            <p><strong>快速模式:</strong> ${data.fastMode ? '是' : '否'}</p>
                            <p><strong>有域名规则:</strong> ${data.hasDomainRules ? '是' : '否'}</p>
                            <p><strong>学习次数:</strong> ${data.learn_count || 0}</p>
                            <p><strong>片段统计:</strong> 总${stats.totalSegments || 0} 广告${stats.adSegments || 0} 不连续${stats.discontinuityCount || 0} 序列跳跃${stats.sequenceJumpCount || 0} 广告簇${stats.adClusterCount || 0}</p>
                            ${data.mxjxUrl ? `<p><strong>无广告播放:</strong> <code style="word-break:break-all">${escapeHtml(data.mxjxUrl)}</code></p>` : ''}
                        </div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-sm btn-primary" onclick="copyText('${escapeHtml(analyzeUrl)}')">复制接口URL</button>
                            ${data.mxjxUrl ? `<button class="btn btn-sm btn-secondary" onclick="window.open('${escapeHtml(data.mxjxUrl)}', '_blank')">新窗口播放</button>` : ''}
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应（已截断）:</div>';
                    const truncated = JSON.parse(JSON.stringify(data));
                    if (truncated.allSegments) truncated.allSegments = truncated.allSegments.slice(0, 20) + `... (共 ${truncated.allSegments.length} 项)`;
                    if (truncated.durationDistribution && truncated.durationDistribution.buckets) {
                        // keep
                    }
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(truncated, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                } else {
                    let html = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600;margin-bottom:8px">✗ 分析失败</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>消息:</strong> ${escapeHtml(data.message || '未知错误')}</p>
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        async function testOfficialInfoApi() {
            const url = document.getElementById('moxiTestUrl').value.trim();
            if (!url) {
                showToast('请输入视频链接', 'error');
                return;
            }
            const resultEl = document.getElementById('moxiTestResult');
            const infoEl = document.getElementById('moxiTestInfo');
            resultEl.style.display = 'block';
            infoEl.innerHTML = '<div style="text-align:center;padding:20px;color:#909399">正在解析官替地址...</div>';

            try {
                const resUrl = API_BASE + '?action=official_replace/info&url=' + encodeURIComponent(url) + '&_t=' + Date.now();
                const res = await fetch(resUrl);
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    infoEl.innerHTML = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600">✗ 返回非JSON响应</div>
                        <pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:300px;margin-top:8px">${escapeHtml(text.substring(0, 1000))}</pre>
                    </div>`;
                    return;
                }

                if (data.success) {
                    const playUrl = data.ad_skip_url || (API_BASE + '?action=mxjx&url=' + encodeURIComponent(data.m3u8_url || ''));
                    let html = `<div style="background:#f0f9eb;padding:16px;border-radius:8px;border:1px solid #e1f3d8">
                        <div style="color:#67c23a;font-weight:600;margin-bottom:8px">✓ 官替解析成功</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>平台:</strong> ${escapeHtml(data.platform || '')}</p>
                            <p><strong>视频标题:</strong> ${escapeHtml(data.video_title || '')}</p>
                            <p><strong>目标集数:</strong> ${escapeHtml(data.target_episode || '')}</p>
                            <p><strong>匹配度:</strong> ${data.match_score || 0}%</p>
                            <p><strong>来源站点:</strong> ${escapeHtml(data.site || '')}</p>
                            <p><strong>M3U8地址:</strong> <code style="word-break:break-all">${escapeHtml(data.m3u8_url || '')}</code></p>
                            <p><strong>无广告播放地址:</strong></p>
                            <div style="background:#fff;padding:8px;border-radius:4px;margin-top:4px;border:1px solid #e1f3d8">
                                <code style="word-break:break-all;font-size:11px">${escapeHtml(playUrl)}</code>
                            </div>
                        </div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
                            <button class="btn btn-sm btn-primary" onclick="window.open('${escapeHtml(playUrl)}', '_blank')">新窗口播放</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(playUrl)}')">复制播放地址</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(resUrl)}')">复制接口URL</button>
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                } else {
                    let html = `<div style="background:#fef0f0;padding:16px;border-radius:8px;border:1px solid #fbc4c4">
                        <div style="color:#f56c6c;font-weight:600;margin-bottom:8px">✗ 官替解析失败</div>
                        <div style="font-size:13px;line-height:2">
                            <p><strong>消息:</strong> ${escapeHtml(data.message || '未知错误')}</p>
                        </div>
                    </div>`;
                    html += '<div style="margin-top:16px"><div style="font-weight:600;margin-bottom:8px">完整 JSON 响应:</div>';
                    html += `<pre style="background:#282c34;color:#abb2bf;padding:12px;border-radius:6px;overflow:auto;font-size:11px;max-height:400px">${escapeHtml(JSON.stringify(data, null, 2))}</pre>`;
                    html += '</div>';
                    infoEl.innerHTML = html;
                }
            } catch (e) {
                infoEl.innerHTML = `<div style="color:#f56c6c;text-align:center;padding:20px">请求失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        let currentOfficialSite = '';

        async function loadOfficialSites() {
            const res = await fetch(API_BASE + '?action=official_sites/list&include_paused=1&_t=' + Date.now());
            const data = await res.json();
            if (data.success) {
                document.getElementById('officialSitesEnabled').checked = data.enabled;
                if (data.settings) {
                    document.getElementById('osAutoSwitch').value = data.settings.auto_switch_domain ? '1' : '0';
                    document.getElementById('osMaxRetry').value = data.settings.max_retry_per_domain ?? 2;
                    document.getElementById('osTimeout').value = data.settings.timeout ?? 10;
                    document.getElementById('osDefaultLimit').value = data.settings.default_limit ?? 20;
                }
                renderOfficialSites(data.sites || []);
            }
        }

        function renderOfficialSites(sites) {
            const container = document.getElementById('officialSitesList');
            if (sites.length === 0) {
                container.innerHTML = '<div class="empty">暂无推荐采集资源站</div>';
                return;
            }
            let html = '<table class="rules-table">';
            html += '<thead><tr><th>状态</th><th>名称</th><th>域名</th><th>类型</th><th>备注</th><th>优先级</th><th>操作</th></tr></thead>';
            html += '<tbody>';
            sites.forEach(site => {
                const isPaused = site.status === 'paused';
                const domains = site.domains || [];
                const activeIdx = site.active_domain_index ?? 0;
                const domainBadges = domains.map((d, i) => {
                    const isActive = i === activeIdx;
                    return `<span class="tag ${isActive ? 'tag-green' : 'tag-blue'}" style="cursor:pointer" onclick="switchOfficialDomain('${escapeHtml(site.name)}', ${i})" title="点击切换">${escapeHtml(d)}</span>`;
                }).join(' ');

                html += `<tr>
                    <td>${isPaused ? '<span class="tag tag-orange">停用</span>' : '<span class="tag tag-green">运行中</span>'}</td>
                    <td style="font-weight:500">
                        ${escapeHtml(site.name || '')}
                        <span class="tag tag-gray">推荐</span>
                    </td>
                    <td><div style="max-width:280px;display:flex;flex-wrap:wrap;gap:4px">${domainBadges}</div></td>
                    <td>${escapeHtml(site.type || 'maccms')}</td>
                    <td>${escapeHtml(site.note || '')}</td>
                    <td>${site.priority ?? 99}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewOfficialSiteVideos('${escapeHtml(site.name)}')">视频</button>
                        <button class="btn btn-sm btn-primary" onclick="editOfficialSite('${escapeHtml(site.name)}')">编辑</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteOfficialSite('${escapeHtml(site.name)}')">删除</button>
                    </td>
                </tr>`;
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        async function switchOfficialDomain(siteName, domainIndex) {
            const res = await fetch(API_BASE + '?action=official_sites/set_domain', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: siteName, domain_index: domainIndex })
            });
            const data = await res.json();
            if (data.success) {
                showToast('域名切换成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '切换失败', 'error');
            }
        }

        async function toggleOfficialSites() {
            const enabled = document.getElementById('officialSitesEnabled').checked;
            const res = await fetch(API_BASE + '?action=official_sites/toggle', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ enabled: enabled })
            });
            const data = await res.json();
            if (data.success) {
                showToast(enabled ? '推荐采集已启用' : '推荐采集已停用', 'success');
            } else {
                showToast(data.message || '操作失败', 'error');
            }
        }

        async function saveOfficialSettings() {
            const settings = {
                auto_switch_domain: document.getElementById('osAutoSwitch').value === '1',
                max_retry_per_domain: parseInt(document.getElementById('osMaxRetry').value),
                timeout: parseInt(document.getElementById('osTimeout').value),
                default_limit: parseInt(document.getElementById('osDefaultLimit').value)
            };
            const res = await fetch(API_BASE + '?action=official_sites/settings/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings)
            });
            const data = await res.json();
            if (data.success) {
                showToast('设置保存成功', 'success');
            } else {
                showToast(data.message || '保存失败', 'error');
            }
        }

        function showAddOfficialSite() {
            const name = prompt('请输入推荐站名称：');
            if (!name) return;
            const domains = prompt('请输入域名（一行一个）：', 'cj.10010888.xyz\ncj.tianwe.cn\ntianwei.qzz.io');
            if (!domains) return;
            const apiPath = prompt('请输入API路径：', '/api.php/provide/vod/') || '/api.php/provide/vod/';
            const note = prompt('备注：', '推荐采集') || '';
            addOfficialSite({ name, domains, api_path: apiPath, note, type: 'maccms', priority: 1 });
        }

        async function addOfficialSite(siteData) {
            const domainList = siteData.domains.split('\n').map(d => d.trim()).filter(d => d);
            const siteUrl = 'https://' + domainList[0];
            const apiUrl = siteUrl + siteData.api_path;
            const res = await fetch(API_BASE + '?action=official_sites/add', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: siteData.name,
                    code: siteData.name.toLowerCase(),
                    site_url: siteUrl,
                    api_url: apiUrl,
                    type: siteData.type || 'maccms',
                    status: 'active',
                    note: siteData.note || '',
                    priority: siteData.priority || 1,
                    domains: domainList,
                    api_path: siteData.api_path || '/api.php/provide/vod/'
                })
            });
            const data = await res.json();
            if (data.success) {
                showToast('添加成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '添加失败', 'error');
            }
        }

        async function editOfficialSite(name) {
            const res = await fetch(API_BASE + '?action=official_sites/get&name=' + encodeURIComponent(name));
            const data = await res.json();
            if (!data.success) {
                showToast(data.message || '获取失败', 'error');
                return;
            }
            const site = data.site;
            const newNote = prompt('修改备注：', site.note || '');
            if (newNote === null) return;
            const newPriority = prompt('修改优先级（数字越小越靠前）：', site.priority ?? 99);
            if (newPriority === null) return;

            const updateRes = await fetch(API_BASE + '?action=official_sites/update', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    name: name,
                    note: newNote,
                    priority: parseInt(newPriority)
                })
            });
            const updateData = await updateRes.json();
            if (updateData.success) {
                showToast('更新成功', 'success');
                loadOfficialSites();
            } else {
                showToast(updateData.message || '更新失败', 'error');
            }
        }

        async function deleteOfficialSite(name) {
            if (!confirm('确定删除推荐站「' + name + '」吗？')) return;
            const res = await fetch(API_BASE + '?action=official_sites/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: name })
            });
            const data = await res.json();
            if (data.success) {
                showToast('删除成功', 'success');
                loadOfficialSites();
            } else {
                showToast(data.message || '删除失败', 'error');
            }
        }

        async function viewOfficialSiteVideos(siteName) {
            currentOfficialSite = siteName;
            document.getElementById('officialSiteVideoTitle').textContent = siteName + ' - 视频列表';
            document.getElementById('officialSiteVideos').style.display = 'block';
            document.getElementById('officialVideoSearch').value = '';
            document.getElementById('officialSiteVideosList').innerHTML = '<div class="loading">加载中...</div>';
            try {
                const res = await fetch(API_BASE + '?action=official_sites/fetch_videos&name=' + encodeURIComponent(siteName) + '&_t=' + Date.now());
                const data = await res.json();
                renderOfficialVideos(data);
            } catch (e) {
                document.getElementById('officialSiteVideosList').innerHTML = 
                    `<div style="color:#f56c6c;text-align:center;padding:20px">加载失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        function renderOfficialVideos(data) {
            const container = document.getElementById('officialSiteVideosList');
            if (!data.success || !data.videos || data.videos.length === 0) {
                container.innerHTML = `<div class="empty">${data.message || '暂无视频数据'}</div>`;
                return;
            }
            const videos = data.videos;
            let html = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px">';
            videos.forEach(v => {
                html += `<div class="stat-card" style="cursor:pointer" onclick="learnOfficialVideo('${escapeHtml(v.first_url || v.url || '')}', '${escapeHtml(v.name || '')}')">
                    <div style="font-weight:500;margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${escapeHtml(v.name || '未知')}</div>
                    <div style="font-size:12px;color:var(--text-secondary)">${escapeHtml(v.remarks || v.type || '')}</div>
                    <div style="margin-top:8px"><span class="tag tag-blue">${v.total || 0} 集</span></div>
                </div>`;
            });
            html += '</div>';
            if (data.domain_used) {
                html = `<div style="margin-bottom:12px;padding:8px 12px;background:var(--primary-bg);border-radius:6px;font-size:13px;color:var(--primary-text)">
                    当前使用域名: <strong>${escapeHtml(data.domain_used)}</strong>
                </div>` + html;
            }
            container.innerHTML = html;
        }

        async function searchOfficialVideos() {
            const keyword = document.getElementById('officialVideoSearch').value.trim();
            if (!keyword || !currentOfficialSite) return;
            document.getElementById('officialSiteVideosList').innerHTML = '<div class="loading">搜索中...</div>';
            try {
                const res = await fetch(API_BASE + '?action=official_sites/search&name=' + encodeURIComponent(currentOfficialSite) + '&keyword=' + encodeURIComponent(keyword) + '&_t=' + Date.now());
                const data = await res.json();
                renderOfficialVideos(data);
            } catch (e) {
                document.getElementById('officialSiteVideosList').innerHTML = 
                    `<div style="color:#f56c6c;text-align:center;padding:20px">搜索失败: ${escapeHtml(e.message)}</div>`;
            }
        }

        function refreshOfficialVideos() {
            if (currentOfficialSite) {
                viewOfficialSiteVideos(currentOfficialSite);
            }
        }

        function closeOfficialSiteVideos() {
            document.getElementById('officialSiteVideos').style.display = 'none';
            currentOfficialSite = '';
        }

        async function learnOfficialVideo(url, name) {
            if (!confirm('学习视频「' + name + '」的广告规则？')) return;
            try {
                const res = await fetch(API_BASE + '?action=analyze&url=' + encodeURIComponent(url) + '&auto_learn=1&_t=' + Date.now());
                
                let data;
                try {
                    const text = await res.text();
                    data = JSON.parse(text);
                } catch (jsonErr) {
                    throw new Error('服务器返回非JSON响应');
                }
                
                if (data.success) {
                    showToast('学习成功，广告占比: ' + (data.stats?.adPercentage || 0).toFixed(1) + '%', 'success');
                } else {
                    showToast(data.message || '学习失败', 'error');
                }
            } catch (e) {
                showToast('学习失败: ' + e.message, 'error');
            }
        }

        function toggleDbType(type) {
            document.getElementById('sqliteConfig').style.display = type === 'sqlite' ? 'block' : 'none';
            document.getElementById('mysqlConfig').style.display = type === 'mysql' ? 'grid' : 'none';
        }

        function showDbConfig() {
            document.getElementById('dbConfigPanel').scrollIntoView({ behavior: 'smooth' });
        }

        async function checkDbStatus() {
            try {
                const res = await fetch(API_BASE + '?action=db/status');
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                const status = data.status;
                document.getElementById('dbType').textContent = status.use_db ? status.db_type.toUpperCase() : '未启用';
                document.getElementById('dbStatus').textContent = status.use_db ? '运行中' : '未启用';
                document.getElementById('dbStatus').style.color = status.use_db ? '#67c23a' : '#e6a23c';
                document.getElementById('dbRuleCount').textContent = status.rule_count ?? '-';
                document.getElementById('dbSiteCount').textContent = status.site_count ?? '-';

                let tablesHtml = '<table style="width:100%;font-size:13px"><thead><tr><th style="text-align:left;padding:6px">表名</th><th style="text-align:left;padding:6px">状态</th></tr></thead><tbody>';
                if (status.tables) {
                    for (const [table, exists] of Object.entries(status.tables)) {
                        tablesHtml += `<tr><td style="padding:6px">${table}</td><td style="padding:6px">${exists ? '<span style="color:#67c23a">✓ 正常</span>' : '<span style="color:#f56c6c">✗ 缺失</span>'}</td></tr>`;
                    }
                }
                tablesHtml += '</tbody></table>';
                document.getElementById('dbTables').innerHTML = tablesHtml;

                if (status.config) {
                    const cfg = status.config;
                    if (cfg.type === 'mysql') {
                        document.querySelector('input[name="dbType"][value="mysql"]').checked = true;
                        toggleDbType('mysql');
                        document.getElementById('mysqlHost').value = cfg.mysql_host || '127.0.0.1';
                        document.getElementById('mysqlPort').value = cfg.mysql_port || 3306;
                        document.getElementById('mysqlDbname').value = cfg.mysql_dbname || 'm3u8_ad';
                        document.getElementById('mysqlUsername').value = cfg.mysql_username || 'root';
                        document.getElementById('mysqlPassword').value = cfg.mysql_password || '';
                        document.getElementById('mysqlCharset').value = cfg.mysql_charset || 'utf8mb4';
                    } else {
                        document.querySelector('input[name="dbType"][value="sqlite"]').checked = true;
                        toggleDbType('sqlite');
                        document.getElementById('sqlitePath').value = cfg.sqlite_path || 'db/data.db';
                    }
                }
            } catch (e) {
                document.getElementById('dbStatus').textContent = '连接失败';
                document.getElementById('dbStatus').style.color = '#f56c6c';
                document.getElementById('dbTables').textContent = '获取失败: ' + e.message;
            }
        }

        async function saveDbConfig() {
            showToast('数据库配置为只读，请直接编辑 db/db_config.php 文件', 'warning');
        }

        async function testDbConnection() {
            const resultEl = document.getElementById('testConnResult');
            const dbType = document.querySelector('input[name="dbType"]:checked').value;
            const config = { type: dbType };
            if (dbType === 'sqlite') {
                config.sqlite_path = document.getElementById('sqlitePath').value;
            } else {
                config.mysql_host = document.getElementById('mysqlHost').value;
                config.mysql_port = parseInt(document.getElementById('mysqlPort').value) || 3306;
                config.mysql_dbname = document.getElementById('mysqlDbname').value;
                config.mysql_username = document.getElementById('mysqlUsername').value;
                config.mysql_password = document.getElementById('mysqlPassword').value;
                config.mysql_charset = document.getElementById('mysqlCharset').value;
            }

            resultEl.textContent = '测试中...';
            resultEl.style.color = '#909399';

            try {
                const res = await fetch(API_BASE + '?action=db/test_connection', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(config)
                });
                const data = await res.json();
                if (data.success) {
                    let msg = '✓ 连接成功';
                    if (data.info) {
                        msg += ' - ' + data.info.version;
                        if (data.info.table_count !== undefined) {
                            msg += '，' + data.info.table_count + ' 张表';
                        }
                    }
                    resultEl.textContent = msg;
                    resultEl.style.color = '#67c23a';
                    showToast('数据库连接成功！', 'success');
                } else {
                    resultEl.textContent = '✗ ' + data.message;
                    resultEl.style.color = '#f56c6c';
                    showToast('连接失败: ' + data.message, 'error');
                }
            } catch (e) {
                resultEl.textContent = '✗ 测试失败: ' + e.message;
                resultEl.style.color = '#f56c6c';
                showToast('测试失败: ' + e.message, 'error');
            }
        }

        async function migrateData() {
            const statusEl = document.getElementById('migrateStatus');
            const resultEl = document.getElementById('migrateResult');
            statusEl.textContent = '迁移中...';
            resultEl.style.display = 'none';

            try {
                const res = await fetch(API_BASE + '?action=db/migrate', { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    statusEl.textContent = '迁移成功！';
                    let summaryHtml = '迁移统计:\n';
                    for (const [key, info] of Object.entries(data.summary)) {
                        summaryHtml += `  ${key}: 迁移 ${info.migrated} 条, 跳过 ${info.skipped} 条\n`;
                    }
                    if (data.errors && data.errors.length > 0) {
                        summaryHtml += '\n错误:\n';
                        data.errors.forEach(err => {
                            summaryHtml += `  [${err.category}] ${err.message}\n`;
                        });
                    }
                    resultEl.querySelector('pre').textContent = summaryHtml;
                    resultEl.style.display = 'block';
                    showToast('数据迁移成功', 'success');
                    checkDbStatus();
                } else {
                    statusEl.textContent = '迁移失败';
                    showToast('迁移失败: ' + data.message, 'error');
                }
            } catch (e) {
                statusEl.textContent = '迁移失败';
                showToast('迁移失败: ' + e.message, 'error');
            }
        }

        async function initDbTables() {
            try {
                const res = await fetch(API_BASE + '?action=db/init', { method: 'POST' });
                const data = await res.json();
                if (data.success) {
                    showToast('表结构初始化成功', 'success');
                    checkDbStatus();
                } else {
                    showToast('初始化失败: ' + data.message, 'error');
                }
            } catch (e) {
                showToast('初始化失败: ' + e.message, 'error');
            }
        }

        // ===== 背景图功能 =====
        const BG_IMAGES = [];
        for (let i = 1; i <= 20; i++) {
            BG_IMAGES.push('img/bj/bg_' + i + '.jpg');
        }
        let currentBgIndex = -1;
        let bgImageEnabled = localStorage.getItem('bgImageEnabled') === 'true';
        let bgAutoChange = localStorage.getItem('bgAutoChange') === 'true';
        let bgAutoTimer = null;

        function getBasePath() {
            const path = window.location.pathname;
            return path.substring(0, path.lastIndexOf('/') + 1);
        }

        function toggleBgImage() {
            bgImageEnabled = !bgImageEnabled;
            localStorage.setItem('bgImageEnabled', bgImageEnabled);
            applyBgImage();
            updateBgButtons();
            showToast(bgImageEnabled ? '背景图已开启' : '背景图已关闭', 'success');
        }

        function applyBgImage() {
            const body = document.body;
            if (bgImageEnabled) {
                body.classList.add('bg-image-mode');
                if (currentBgIndex < 0) {
                    changeBgImage(true);
                } else {
                    const basePath = getBasePath();
                    body.style.backgroundImage = 'url("' + basePath + BG_IMAGES[currentBgIndex] + '")';
                }
            } else {
                body.classList.remove('bg-image-mode');
                body.style.backgroundImage = 'none';
                if (bgAutoTimer) {
                    clearInterval(bgAutoTimer);
                    bgAutoTimer = null;
                }
            }
        }

        function changeBgImage(silent = false) {
            if (!bgImageEnabled) return;
            let newIndex;
            do {
                newIndex = Math.floor(Math.random() * BG_IMAGES.length);
            } while (newIndex === currentBgIndex && BG_IMAGES.length > 1);
            currentBgIndex = newIndex;
            const basePath = getBasePath();
            document.body.style.backgroundImage = 'url("' + basePath + BG_IMAGES[currentBgIndex] + '")';
            localStorage.setItem('currentBgIndex', currentBgIndex);
            if (!silent) {
                showToast('已更换背景图 ' + (currentBgIndex + 1) + '/' + BG_IMAGES.length, 'success');
            }
        }

        function updateBgButtons() {
            const toggleBtn = document.getElementById('bgToggleBtn');
            const changeBtn = document.getElementById('bgChangeBtn');
            if (toggleBtn) {
                toggleBtn.classList.toggle('active', bgImageEnabled);
                toggleBtn.textContent = bgImageEnabled ? '🖼️' : '🖼️';
            }
            if (changeBtn) {
                changeBtn.style.display = bgImageEnabled ? 'flex' : 'none';
            }
        }

        // ===== v3.0 新功能 =====

        let analysisHistory = JSON.parse(localStorage.getItem('analysisHistory') || '[]');

        function saveToHistory(url, type, result) {
            const domain = (() => { try { return new URL(url).hostname; } catch { return url; } })();
            
            const stats = result?.data?.stats || result?.stats || result || {};
            const filtered = result?.data?.filtered || result?.filtered || {};
            
            const totalSegments = 
                stats.totalSegments || 
                stats.total_segments || 
                filtered.totalSegments ||
                filtered.total_segments ||
                result.totalSegments ||
                0;
                
            const adSegments = 
                stats.adSegments || 
                stats.ad_segments || 
                filtered.removedSegments?.length ||
                filtered.adSegments ||
                result.adSegments ||
                0;
            
            const record = {
                id: Date.now(),
                url: url,
                domain: domain,
                type: type,
                time: new Date().toISOString(),
                result: result ? {
                    success: result.success !== false,
                    totalSegments: totalSegments,
                    adSegments: adSegments,
                    duration: result.duration || 0
                } : null
            };
            analysisHistory.unshift(record);
            if (analysisHistory.length > 100) analysisHistory = analysisHistory.slice(0, 100);
            localStorage.setItem('analysisHistory', JSON.stringify(analysisHistory));
            updateDashboardStats();
            renderDashboardRecent();
        }

        function updateDashboardStats() {
            const total = analysisHistory.length;
            const successCount = analysisHistory.filter(r => r.result && r.result.success).length;
            const domains = new Set(analysisHistory.map(r => r.domain)).size;
            const totalAdRemoved = analysisHistory.reduce((sum, r) => sum + (r.result ? r.result.adSegments || 0 : 0), 0);
            const totalDuration = analysisHistory.reduce((sum, r) => sum + (r.result ? r.result.duration || 0 : 0), 0);
            const avgTime = successCount > 0 ? (totalDuration / successCount / 1000).toFixed(1) + 's' : '0s';
            
            const aiSkipCount = analysisHistory.filter(r => r.type === 'ai_skip').length;
            const md5Count = analysisHistory.filter(r => r.type === 'md5').length;
            
            const today = new Date().toDateString();
            const todayCount = analysisHistory.filter(r => new Date(r.time).toDateString() === today).length;
            const todayAdRemoved = analysisHistory.filter(r => new Date(r.time).toDateString() === today)
                .reduce((sum, r) => sum + (r.result ? r.result.adSegments || 0 : 0), 0);

            const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
            const setTrend = (id, text, isUp = true) => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = (isUp ? '↑ ' : '↓ ') + text;
                    el.className = 'stat-trend ' + (isUp ? 'up' : 'down');
                }
            };
            
            setVal('dashTotalAnalyze', total);
            setVal('dashAdRemoved', totalAdRemoved);
            setVal('dashDomains', domains);
            setVal('dashAvgTime', avgTime);
            setVal('dashRules', successCount);
            setVal('dashMd5', aiSkipCount);
            
            const trendEls = document.querySelectorAll('#page-dashboard .stat-trend');
            if (trendEls.length >= 6) {
                trendEls[0].textContent = '↑ 今日 +' + todayCount;
                trendEls[1].textContent = '↑ 今日 +' + todayAdRemoved;
                trendEls[2].textContent = '↑ 共 ' + domains + ' 个';
                trendEls[3].textContent = '↓ 极速模式';
                trendEls[4].textContent = '↑ 成功率 ' + (total > 0 ? Math.round(successCount / total * 100) : 0) + '%';
                trendEls[5].textContent = '↑ AI 去广告';
            }
            
            renderTopDomains();
        }
        
        function renderTopDomains() {
            const container = document.getElementById('dashTopDomains');
            if (!container) return;
            
            const domainCounts = {};
            analysisHistory.forEach(r => {
                domainCounts[r.domain] = (domainCounts[r.domain] || 0) + 1;
            });
            
            const sorted = Object.entries(domainCounts)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);
            
            if (sorted.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center;color:var(--v3-text-muted);padding:30px">
                        暂无数据，分析更多视频后热门域名将在这里展示
                    </div>`;
                return;
            }
            
            const maxCount = sorted[0][1];
            container.innerHTML = sorted.map(([domain, count], index) => {
                const percent = Math.round((count / maxCount) * 100);
                const rankColors = [
                    'background: linear-gradient(135deg, #f59e0b, #ef4444)',
                    'background: linear-gradient(135deg, #94a3b8, #64748b)',
                    'background: linear-gradient(135deg, #b45309, #92400e)',
                    'background: var(--v3-primary-light)',
                ];
                const rankBg = rankColors[index] || 'background: var(--v3-bg-hover)';
                const rankText = index < 3 ? 'color:white;font-weight:700' : 'color:var(--v3-text-secondary)';
                return `
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--v3-border-light)">
                        <div style="width:28px;height:28px;border-radius:8px;${rankBg};display:flex;align-items:center;justify-content:center;font-size:13px;${rankText};flex-shrink:0">${index + 1}</div>
                        <div style="flex:1;min-width:0">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                                <span style="font-size:13px;font-weight:500;color:var(--v3-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(domain)}</span>
                                <span style="font-size:12px;color:var(--v3-text-muted);font-weight:600">${count} 次</span>
                            </div>
                            <div style="height:6px;background:var(--v3-bg-hover);border-radius:3px;overflow:hidden">
                                <div style="height:100%;width:${percent}%;background:var(--v3-primary-gradient);border-radius:3px;transition:width 0.5s ease"></div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderDashboardRecent() {
            const listEl = document.getElementById('dashRecentList');
            if (!listEl) return;
            const recent = analysisHistory.slice(0, 5);
            if (recent.length === 0) {
                listEl.innerHTML = '<li class="recent-item" style="justify-content:center;color:var(--v3-text-muted);padding:20px 0">暂无分析记录，快去分析一个视频吧！</li>';
                return;
            }
            listEl.innerHTML = recent.map(r => {
                const typeIcon = r.type === 'ai_skip' ? '🤖' : r.type === 'md5' ? '🔬' : '🎯';
                const statusClass = r.result && r.result.success ? 'badge-success' : 'badge-danger';
                const statusText = r.result && r.result.success ? '成功' : '失败';
                const timeStr = formatTimeAgo(r.time);
                return `
                    <li class="recent-item" onclick="openFromHistory(${r.id})">
                        <div class="recent-item-icon">${typeIcon}</div>
                        <div class="recent-item-content">
                            <div class="recent-item-title">${escapeHtml(r.domain)}</div>
                            <div class="recent-item-meta">
                                <span>${timeStr}</span>
                                <span class="badge ${statusClass}">${statusText}</span>
                            </div>
                        </div>
                    </li>
                `;
            }).join('');
        }

        function formatTimeAgo(isoString) {
            const diff = Date.now() - new Date(isoString).getTime();
            const min = Math.floor(diff / 60000);
            if (min < 1) return '刚刚';
            if (min < 60) return min + '分钟前';
            const hr = Math.floor(min / 60);
            if (hr < 24) return hr + '小时前';
            const day = Math.floor(hr / 24);
            if (day < 7) return day + '天前';
            return new Date(isoString).toLocaleDateString();
        }

        function openFromHistory(id) {
            const record = analysisHistory.find(r => r.id === id);
            if (!record) return;
            if (record.type === 'ai_skip') {
                navigateTo('ai_skip');
                const input = document.getElementById('aiSkipUrl');
                if (input) input.value = record.url;
            } else {
                navigateTo('analyze');
                const input = document.getElementById('analyzeUrl');
                if (input) input.value = record.url;
            }
        }

        function renderHistory() {
            const listEl = document.getElementById('historyList');
            const countEl = document.getElementById('historyCount');
            if (!listEl) return;
            const filter = document.getElementById('historyFilter')?.value || 'all';
            const search = document.getElementById('historySearch')?.value?.toLowerCase() || '';
            let filtered = analysisHistory;
            if (filter !== 'all') {
                filtered = filtered.filter(r => r.type === filter);
            }
            if (search) {
                filtered = filtered.filter(r =>
                    r.url.toLowerCase().includes(search) ||
                    r.domain.toLowerCase().includes(search)
                );
            }
            if (countEl) countEl.textContent = filtered.length;
            if (filtered.length === 0) {
                listEl.innerHTML = `
                    <div style="text-align:center;color:var(--v3-text-muted);padding:40px">
                        <div style="font-size:40px;margin-bottom:12px">📭</div>
                        <div>暂无匹配的记录</div>
                    </div>`;
                return;
            }
            listEl.innerHTML = filtered.map(r => {
                const typeIcon = r.type === 'ai_skip' ? '🤖' : r.type === 'md5' ? '🔬' : '🎯';
                const typeText = r.type === 'ai_skip' ? 'AI去广告' : r.type === 'md5' ? 'MD5分析' : '视频分析';
                const statusClass = r.result && r.result.success ? 'badge-success' : 'badge-danger';
                const statusText = r.result && r.result.success ? '成功' : '失败';
                const adCount = r.result ? r.result.adSegments || 0 : 0;
                const segCount = r.result ? r.result.totalSegments || 0 : 0;
                return `
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid var(--v3-border-light)">
                        <div style="width:44px;height:44px;border-radius:10px;background:var(--v3-primary-light);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">${typeIcon}</div>
                        <div style="flex:1;min-width:0">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                <span style="font-size:13px;font-weight:500;color:var(--v3-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1">${escapeHtml(r.domain)}</span>
                                <span class="badge ${statusClass}">${statusText}</span>
                                <span class="badge badge-info">${typeText}</span>
                            </div>
                            <div style="font-size:12px;color:var(--v3-text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(r.url)}</div>
                            <div style="font-size:11px;color:var(--v3-text-muted);margin-top:2px;display:flex;gap:12px">
                                <span>${new Date(r.time).toLocaleString()}</span>
                                ${r.result ? `<span>片段: ${segCount} / 广告: ${adCount}</span>` : ''}
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0">
                            <button class="btn btn-sm btn-secondary" onclick="openFromHistory(${r.id})">重新分析</button>
                            <button class="btn btn-sm btn-secondary" onclick="copyText('${escapeHtml(r.url)}')">复制</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filterHistory() {
            renderHistory();
        }

        function clearHistory() {
            if (!confirm('确定要清空所有分析历史吗？')) return;
            analysisHistory = [];
            localStorage.removeItem('analysisHistory');
            renderHistory();
            updateDashboardStats();
            renderDashboardRecent();
            showToast('历史记录已清空', 'success');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar && overlay) {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }

        function navigateTo(pageName) {
            const navItem = document.querySelector('.nav-item[data-page="' + pageName + '"]');
            if (navItem) {
                handleNavClick(navItem);
            } else {
                document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
                const pageEl = document.getElementById('page-' + pageName);
                if (pageEl) pageEl.classList.add('active');
            }
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
            updateMobileNav(pageName);
            if (pageName === 'history') renderHistory();
            if (pageName === 'dashboard') {
                updateDashboardStats();
                renderDashboardRecent();
                renderTopDomains();
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateMobileNav(pageName) {
            const navItems = document.querySelectorAll('.mobile-nav-item');
            navItems.forEach(item => {
                item.classList.toggle('active', item.dataset.page === pageName);
            });
        }

        function mobileNavTo(pageName) {
            navigateTo(pageName);
        }

        // 批量分析功能
        function updateBatchUrlCount() {
            const textarea = document.getElementById('batchUrls');
            const countEl = document.getElementById('batchUrlCount');
            if (!textarea || !countEl) return;
            const urls = textarea.value.split('\n').filter(u => u.trim().length > 0);
            countEl.textContent = urls.length;
        }

        function loadBatchDemo() {
            const textarea = document.getElementById('batchUrls');
            if (textarea) {
                textarea.value = 'https://example.com/video1/index.m3u8\nhttps://example.com/video2/index.m3u8\nhttps://example.com/video3/index.m3u8';
                updateBatchUrlCount();
                showToast('示例数据已加载', 'success');
            }
        }

        async function startBatchAnalyze() {
            const textarea = document.getElementById('batchUrls');
            if (!textarea) return;
            const urls = textarea.value.split('\n').map(u => u.trim()).filter(u => u.length > 0);
            if (urls.length === 0) { showToast('请先输入视频链接', 'error'); return; }
            if (urls.length > 20) { showToast('最多支持 20 个链接同时分析', 'error'); return; }

            const fastMode = document.getElementById('batchFastMode')?.checked !== false;
            const aiMode = document.getElementById('batchAiMode')?.checked !== false;

            document.getElementById('batchResultCard').style.display = 'block';
            document.getElementById('batchTotal').textContent = urls.length;
            document.getElementById('batchSuccess').textContent = 0;
            document.getElementById('batchFailed').textContent = 0;
            document.getElementById('batchProgress').textContent = '0%';

            const resultList = document.getElementById('batchResultList');
            resultList.innerHTML = '';

            let success = 0;
            let failed = 0;

            for (let i = 0; i < urls.length; i++) {
                const url = urls[i];
                const item = document.createElement('div');
                item.style.cssText = 'display:flex;align-items:center;gap:12px;padding:12px;background:var(--v3-bg-hover);border-radius:10px;margin-bottom:8px';
                item.innerHTML = `
                    <div style="width:32px;height:32px;border-radius:8px;background:var(--v3-warning-light);display:flex;align-items:center;justify-content:center;font-size:16px">⏳</div>
                    <div style="flex:1;min-width:0">
                        <div style="font-size:13px;font-weight:500;color:var(--v3-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escapeHtml(url)}</div>
                        <div style="font-size:11px;color:var(--v3-text-muted);margin-top:2px">等待分析...</div>
                    </div>
                `;
                resultList.appendChild(item);

                document.getElementById('batchProgress').textContent = Math.round(((i + 1) / urls.length) * 100) + '%';

                try {
                    const action = aiMode ? 'ai/skip' : 'analyze';
                    const params = new URLSearchParams({ action, url });
                    if (fastMode && aiMode) params.append('fast', '1');

                    const res = await fetch(API_BASE + '?' + params.toString());
                    const data = await res.json();

                    if (data.success) {
                        success++;
                        item.querySelector('div:nth-child(1)').textContent = '✅';
                        item.querySelector('div:nth-child(1)').style.background = 'var(--v3-success-light)';
                        const adCount = aiMode ? (data.filtered?.removedSegments?.length || data.adSegments?.length || 0) : (data.adSegments || 0);
                        const totalSeg = data.totalSegments || data.filtered?.segments?.length || 0;
                        item.querySelector('div:nth-child(2) div:nth-child(2)').innerHTML =
                            `<span class="badge badge-success">成功</span> <span style="color:var(--v3-text-muted)">片段: ${totalSeg} / 广告: ${adCount}</span>`;
                        saveToHistory(url, aiMode ? 'ai_skip' : 'analyze', data);
                    } else {
                        failed++;
                        item.querySelector('div:nth-child(1)').textContent = '❌';
                        item.querySelector('div:nth-child(1)').style.background = 'var(--v3-danger-light)';
                        item.querySelector('div:nth-child(2) div:nth-child(2)').innerHTML =
                            `<span class="badge badge-danger">失败</span> <span style="color:var(--v3-text-muted)">${escapeHtml(data.message || '未知错误')}</span>`;
                    }
                } catch (e) {
                    failed++;
                    item.querySelector('div:nth-child(1)').textContent = '❌';
                    item.querySelector('div:nth-child(1)').style.background = 'var(--v3-danger-light)';
                    item.querySelector('div:nth-child(2) div:nth-child(2)').innerHTML =
                        `<span class="badge badge-danger">失败</span> <span style="color:var(--v3-text-muted)">${escapeHtml(e.message)}</span>`;
                }

                document.getElementById('batchSuccess').textContent = success;
                document.getElementById('batchFailed').textContent = failed;
            }

            document.getElementById('batchProgress').textContent = '100%';
            showToast(`批量分析完成：成功 ${success} 个，失败 ${failed} 个`, success > failed ? 'success' : 'warning');
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderSidebarMenu();
            initTheme();
            refreshRules();
            initAccessPreview();
            loadOfficialSites();
            loadOfficialReplaceConfig();
            loadPlayerConfig();
            loadProxyList();
            updateDashboardStats();
            renderDashboardRecent();
            renderTopDomains();
            
            const batchTextarea = document.getElementById('batchUrls');
            if (batchTextarea) {
                batchTextarea.addEventListener('input', updateBatchUrlCount);
            }
            
            const savedBgIndex = parseInt(localStorage.getItem('currentBgIndex'));
            if (!isNaN(savedBgIndex) && savedBgIndex >= 0 && savedBgIndex < BG_IMAGES.length) {
                currentBgIndex = savedBgIndex;
            }
            applyBgImage();
            updateBgButtons();
            fetch(API_BASE + '?action=info/version')
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.version) {
                        const v = document.getElementById('sidebarVersion');
                        if (v) v.textContent = '版本 ' + data.version;
                    }
                })
                .catch(() => {});
            setTimeout(() => checkUpdate(true), 2000);
        });
    </script>
</body>
</html>
