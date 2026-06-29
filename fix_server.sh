#!/bin/bash
# M3U8 深度修复脚本

echo "=== M3U8 深度修复开始 ==="

cd /www/wwwroot/ssmhd.com_9002

# 1. 下载最新文件
echo "1. 下载最新文件..."
curl -sL https://raw.githubusercontent.com/ssmhdssmhd/qcb/main/mx.php -o mx.php.new
curl -sL https://raw.githubusercontent.com/ssmhdssmhd/qcb/main/index.php -o index.php.new

# 2. 备份旧文件
echo "2. 备份旧文件..."
cp mx.php mx.php.bak.$(date +%Y%m%d_%H%M%S)
cp index.php index.php.bak.$(date +%Y%m%d_%H%M%S)

# 3. 应用新文件
echo "3. 应用新文件..."
mv mx.php.new mx.php
mv index.php.new index.php

# 4. 移除 BOM (如果有)
echo "4. 移除 BOM 头..."
for f in mx.php index.php; do
    if [ -f "$f" ]; then
        # 检查是否有 BOM
        if [ "$(head -c 3 "$f" | od -An -tx1)" = " ef bb bf" ]; then
            echo "  移除 BOM: $f"
            tail -c +4 "$f" > "$f.tmp" && mv "$f.tmp" "$f"
        fi
    fi
done

# 5. 设置权限
echo "5. 设置权限..."
chown www:www mx.php index.php
chmod 644 mx.php index.php

# 6. 清除 PHP 缓存
echo "6. 清除 PHP 缓存..."
php -r "if(function_exists('opcache_reset')){opcache_reset();echo 'OPcache cleared';}else{echo 'No OPcache';}"
php -r "if(function_exists('apc_clear_cache')){apc_clear_cache();echo ' APC cleared';}"
rm -f /tmp/php*

echo ""
echo "=== 修复完成 ==="
echo "请刷新浏览器缓存，然后访问后台"
echo ""

# 7. 测试 API
echo ""
echo "=== API 测试 ==="
curl -s "http://127.0.0.1:9002/mx.php?action=rules/list" | head -c 200
echo ""
