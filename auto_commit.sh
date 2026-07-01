#!/bin/bash

cd "$(dirname "$0")"

COMMIT_MSG="${1:-auto: 自动提交更新}"

git add -A

if git diff --cached --quiet; then
    echo "📭 没有需要提交的更改"
    exit 0
fi

CHANGED_FILES=$(git diff --cached --name-only)
FILE_COUNT=$(echo "$CHANGED_FILES" | wc -l)

echo "📝 检测到 $FILE_COUNT 个文件变更:"
echo "$CHANGED_FILES" | while read f; do
    echo "   - $f"
done

git commit -m "$COMMIT_MSG"

if [ $? -eq 0 ]; then
    echo "✅ 本地提交成功"
    
    CURRENT_BRANCH=$(git branch --show-current)
    
    if git remote | grep -q "origin"; then
        echo "🚀 推送到远程仓库 (origin/$CURRENT_BRANCH)..."
        git push origin "$CURRENT_BRANCH"
        
        if [ $? -eq 0 ]; then
            echo "🎉 推送成功！"
            echo "🔗 远程仓库: $(git remote get-url origin)"
            echo "🌿 分支: $CURRENT_BRANCH"
            exit 0
        else
            echo "❌ 推送失败，请检查网络或权限"
            exit 1
        fi
    else
        echo "⚠️  未配置远程仓库，仅本地提交"
        exit 0
    fi
else
    echo "❌ 提交失败"
    exit 1
fi
