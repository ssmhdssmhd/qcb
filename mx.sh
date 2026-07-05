#!/bin/bash
#
# mx.sh - M3U8 广告分析系统 一键更新脚本
#
# 使用方法：
#   chmod +x mx.sh
#   ./mx.sh              # 交互式菜单
#   ./mx.sh 1            # 直接更新源码
#
# 功能：
#   1. 更新源码（从 GitHub 下载最新版本，保留配置文件）
#   2. 查看当前版本
#   3. 查看更新日志
#   4. 回滚到备份版本
#   5. 清理缓存
#

# ==================== 配置 ====================
GITHUB_REPO="ssmhdssmhd/gcb"
BRANCH="main"
# 运行目录（脚本所在目录）
RUN_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# 临时目录
TMP_DIR="${RUN_DIR}/.mx_update_tmp"
# 备份目录
BACKUP_DIR="${RUN_DIR}/backups"

# 下载镜像源（按优先级排序）
DOWNLOAD_MIRRORS=(
    "https://github.com/${GITHUB_REPO}/archive/refs/heads/${BRANCH}.zip"
    "https://ghproxy.com/https://github.com/${GITHUB_REPO}/archive/refs/heads/${BRANCH}.zip"
    "https://mirror.ghproxy.com/https://github.com/${GITHUB_REPO}/archive/refs/heads/${BRANCH}.zip"
    "https://gh.api.99988866.xyz/https://github.com/${GITHUB_REPO}/archive/refs/heads/${BRANCH}.zip"
)

# API 镜像源
API_MIRRORS=(
    "https://api.github.com/repos/${GITHUB_REPO}/commits/${BRANCH}"
    "https://gh.api.99988866.xyz/https://api.github.com/repos/${GITHUB_REPO}/commits/${BRANCH}"
    "https://mirror.ghproxy.com/https://api.github.com/repos/${GITHUB_REPO}/commits/${BRANCH}"
)

# 版本文件 URL
VERSION_MIRRORS=(
    "https://raw.githubusercontent.com/${GITHUB_REPO}/${BRANCH}/version.php"
    "https://cdn.jsdelivr.net/gh/${GITHUB_REPO}@${BRANCH}/version.php"
    "https://fastly.jsdelivr.net/gh/${GITHUB_REPO}@${BRANCH}/version.php"
)

# 更新时排除的文件（保留配置文件）
EXCLUDE_FILES=(
    "sq.php"
    "auth_config.php"
    "mx.sh"
    "fix_update.php"
    "fix_v2.php"
    "fix_v3.php"
)

# 更新时排除的目录
EXCLUDE_DIRS=(
    "backups"
    "cache"
    ".git"
    ".mx_update_tmp"
)

# 受保护的文件模式（不覆盖）
PROTECTED_PATTERNS=(
    "gz/rules_*.php"
)

# ==================== 颜色定义 ====================
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m' # No Color

# ==================== 工具函数 ====================

# 打印信息
info()    { echo -e "${GREEN}[INFO]${NC} $1"; }
warn()    { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; }
success() { echo -e "${GREEN}[OK]${NC} $1"; }
step()    { echo -e "${CYAN}[STEP]${NC} $1"; }

# 分隔线
separator() {
    echo -e "${BLUE}========================================${NC}"
}

# 获取当前版本号
get_current_version() {
    local version_file="${RUN_DIR}/version.php"
    if [ -f "$version_file" ]; then
        local version=$(php -r "echo @include '$version_file' ? (@include '$version_file')['version'] ?? '-' : '-';" 2>/dev/null)
        if [ -z "$version" ] || [ "$version" = "-" ]; then
            version=$(grep -oP "'version'\s*=>\s*'\K[^']+" "$version_file" 2>/dev/null || echo "未知")
        fi
        echo "$version"
    else
        echo "未知"
    fi
}

# 获取当前 commit
get_current_commit() {
    local version_file="${RUN_DIR}/version.php"
    if [ -f "$version_file" ]; then
        local commit=$(grep -oP "'commit'\s*=>\s*'\K[^']+" "$version_file" 2>/dev/null || echo "")
        echo "$commit"
    else
        echo ""
    fi
}

# 获取远程最新版本号
get_remote_version() {
    for url in "${VERSION_MIRRORS[@]}"; do
        local response=$(curl -sL --connect-timeout 8 --max-time 15 "$url" 2>/dev/null)
        if [ -n "$response" ]; then
            local version=$(echo "$response" | grep -oP "'version'\s*=>\s*'\K[^']+" 2>/dev/null | head -1)
            if [ -n "$version" ]; then
                echo "$version"
                return 0
            fi
        fi
    done
    echo ""
    return 1
}

# 获取远程最新 commit
get_remote_commit() {
    for url in "${API_MIRRORS[@]}"; do
        local response=$(curl -sL --connect-timeout 8 --max-time 15 \
            -H "User-Agent: mx-update-script" "$url" 2>/dev/null)
        if [ -n "$response" ]; then
            local commit=$(echo "$response" | grep -oP '"sha"\s*:\s*"\K[^"]+' 2>/dev/null | head -1)
            if [ -n "$commit" ]; then
                echo "$commit"
                return 0
            fi
        fi
    done
    echo ""
    return 1
}

# 检查命令是否存在
check_command() {
    if ! command -v "$1" &> /dev/null; then
        error "缺少依赖: $1，请先安装"
        return 1
    fi
    return 0
}

# 检查是否受保护
is_protected() {
    local file="$1"
    local basename=$(basename "$file")

    # 检查排除文件
    for exclude in "${EXCLUDE_FILES[@]}"; do
        if [ "$basename" = "$exclude" ]; then
            return 0
        fi
    done

    # 检查排除目录
    for exclude in "${EXCLUDE_DIRS[@]}"; do
        if [[ "$file" == *"$exclude"* ]]; then
            return 0
        fi
    done

    # 检查受保护模式
    for pattern in "${PROTECTED_PATTERNS[@]}"; do
        # 使用 case 模式匹配
        local basename_pattern=$(basename "$pattern")
        local dirname_pattern=$(dirname "$pattern")
        if [[ "$basename" == $basename_pattern ]] && [[ "$file" == *"$dirname_pattern"* ]]; then
            return 0
        fi
    done

    return 1
}

# ==================== 核心功能 ====================

# 1. 更新源码
do_update() {
    separator
    echo -e "${BOLD}${CYAN}  M3U8 广告分析系统 - 源码更新${NC}"
    separator
    echo ""

    # 检查依赖
    step "检查依赖..."
    check_command "curl" || return 1
    check_command "unzip" || return 1
    check_command "php" || return 1
    success "依赖检查通过"
    echo ""

    # 显示版本信息
    local current_version=$(get_current_version)
    local current_commit=$(get_current_commit)
    info "当前版本: ${current_version}"
    info "当前Commit: $(echo "$current_commit" | cut -c1-7)"
    echo ""

    # 获取远程版本
    step "获取远程版本信息..."
    local remote_version=$(get_remote_version)
    local remote_commit=$(get_remote_commit)

    if [ -n "$remote_version" ]; then
        info "远程版本: ${remote_version}"
    else
        warn "无法获取远程版本号"
    fi

    if [ -n "$remote_commit" ]; then
        info "远程Commit: $(echo "$remote_commit" | cut -c1-7)"
    else
        warn "无法获取远程 Commit"
    fi
    echo ""

    # 判断是否需要更新
    if [ -n "$current_commit" ] && [ -n "$remote_commit" ]; then
        if [ "$current_commit" = "$remote_commit" ]; then
            success "已是最新版本，无需更新"
            return 0
        fi
    fi

    # 确认更新
    if [ "$AUTO_CONFIRM" != "1" ]; then
        read -p "是否开始更新？(y/N): " confirm
        if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
            warn "已取消更新"
            return 0
        fi
    fi
    echo ""

    # 创建备份
    step "创建备份..."
    local backup_time=$(date +%Y%m%d_%H%M%S)
    local backup_file="${BACKUP_DIR}/backup_${backup_time}.zip"

    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
    fi

    if command -v zip &> /dev/null; then
        cd "$RUN_DIR"
        zip -r -q "$backup_file" . \
            -x "backups/*" "cache/*" ".git/*" ".mx_update_tmp/*" 2>/dev/null
        if [ $? -eq 0 ]; then
            success "备份完成: ${backup_file}"
        else
            warn "备份失败，继续更新..."
        fi
    else
        warn "zip 命令不可用，跳过备份"
    fi
    echo ""

    # 下载源码
    step "下载源码..."
    local download_success=false
    local zip_file="${TMP_DIR}/source.zip"

    mkdir -p "$TMP_DIR"

    for url in "${DOWNLOAD_MIRRORS[@]}"; do
        info "尝试下载: $(echo "$url" | cut -c1-60)..."
        curl -sL --connect-timeout 15 --max-time 120 \
            -H "User-Agent: mx-update-script" \
            -o "$zip_file" "$url" 2>/dev/null

        if [ $? -eq 0 ] && [ -f "$zip_file" ] && [ -s "$zip_file" ]; then
            # 验证是否为有效的 zip 文件
            if unzip -tq "$zip_file" &>/dev/null; then
                success "下载成功"
                download_success=true
                break
            fi
        fi
        warn "下载失败，尝试下一个镜像..."
    done

    if [ "$download_success" = false ]; then
        error "所有镜像源下载失败，请检查网络连接"
        rm -rf "$TMP_DIR"
        return 1
    fi
    echo ""

    # 解压源码
    step "解压源码..."
    local extract_dir="${TMP_DIR}/extracted"
    mkdir -p "$extract_dir"

    unzip -q -o "$zip_file" -d "$extract_dir" 2>/dev/null
    if [ $? -ne 0 ]; then
        error "解压失败"
        rm -rf "$TMP_DIR"
        return 1
    fi

    # 找到解压后的源码目录（GitHub 下载的 zip 会包含一层目录）
    local source_dir=$(find "$extract_dir" -maxdepth 1 -type d | tail -1)
    if [ ! -d "$source_dir" ]; then
        error "未找到源码目录"
        rm -rf "$TMP_DIR"
        return 1
    fi
    success "解压完成"
    echo ""

    # 更新文件
    step "更新文件..."
    local updated_count=0
    local skipped_count=0

    # 遍历源码目录中的所有文件
    while IFS= read -r -d '' file; do
        # 获取相对路径
        local rel_path="${file#$source_dir/}"

        # 检查是否受保护
        if is_protected "$rel_path"; then
            skipped_count=$((skipped_count + 1))
            continue
        fi

        # 创建目标目录
        local target_file="${RUN_DIR}/${rel_path}"
        local target_dir=$(dirname "$target_file")
        if [ ! -d "$target_dir" ]; then
            mkdir -p "$target_dir"
        fi

        # 复制文件
        cp -f "$file" "$target_file" 2>/dev/null
        if [ $? -eq 0 ]; then
            updated_count=$((updated_count + 1))
        fi
    done < <(find "$source_dir" -type f -print0)

    # 设置权限
    find "$RUN_DIR" -name "*.php" -exec chmod 644 {} \; 2>/dev/null
    find "$RUN_DIR" -name "*.sh" -exec chmod 755 {} \; 2>/dev/null

    success "更新完成: ${updated_count} 个文件已更新, ${skipped_count} 个文件已跳过"
    echo ""

    # 更新 version.php 中的 commit
    if [ -n "$remote_commit" ]; then
        step "更新版本信息..."
        local version_file="${RUN_DIR}/version.php"
        if [ -f "$version_file" ]; then
            if [[ "$(uname)" == "Darwin" ]]; then
                sed -i '' "s/'commit' => '.*'/'commit' => '${remote_commit}'/" "$version_file" 2>/dev/null
            else
                sed -i "s/'commit' => '.*'/'commit' => '${remote_commit}'/" "$version_file" 2>/dev/null
            fi
            success "版本信息已更新"
        fi
    fi
    echo ""

    # 清理临时文件
    rm -rf "$TMP_DIR"

    # 显示结果
    separator
    success "源码更新成功！"
    info "新版本: $(get_current_version)"
    info "新Commit: $(echo "$(get_current_commit)" | cut -c1-7)"
    if [ -f "$backup_file" ]; then
        info "备份文件: ${backup_file}"
    fi
    separator
    echo ""
    warn "请刷新浏览器查看更新效果"
}

# 2. 查看当前版本
show_version() {
    separator
    echo -e "${BOLD}${CYAN}  版本信息${NC}"
    separator
    echo ""
    info "当前版本: $(get_current_version)"
    info "当前Commit: $(echo "$(get_current_commit)" | cut -c1-7)"

    local remote_version=$(get_remote_version)
    local remote_commit=$(get_remote_commit)

    if [ -n "$remote_version" ]; then
        info "远程版本: ${remote_version}"
    fi
    if [ -n "$remote_commit" ]; then
        info "远程Commit: $(echo "$remote_commit" | cut -c1-7)"
    fi

    echo ""

    local current_commit=$(get_current_commit)
    if [ -n "$current_commit" ] && [ -n "$remote_commit" ]; then
        if [ "$current_commit" = "$remote_commit" ]; then
            success "已是最新版本"
        else
            warn "有新版本可用，请执行更新"
        fi
    fi
    echo ""
}

# 3. 查看更新日志
show_changelog() {
    separator
    echo -e "${BOLD}${CYAN}  更新日志${NC}"
    separator
    echo ""

    step "获取更新日志..."

    for url in "${API_MIRRORS[@]}"; do
        local response=$(curl -sL --connect-timeout 8 --max-time 15 \
            -H "User-Agent: mx-update-script" "$url" 2>/dev/null)
        if [ -n "$response" ]; then
            local messages=$(echo "$response" | grep -oP '"message"\s*:\s*"\K[^"]+' 2>/dev/null | head -10)
            if [ -n "$messages" ]; then
                echo "$messages" | while IFS= read -r line; do
                    echo -e "  ${GREEN}*${NC} $line"
                done
                echo ""
                return 0
            fi
        fi
    done

    # 尝试读取本地 CHANGELOG.md
    if [ -f "${RUN_DIR}/CHANGELOG.md" ]; then
        info "显示本地 CHANGELOG.md (前50行):"
        echo ""
        head -50 "${RUN_DIR}/CHANGELOG.md"
    else
        error "无法获取更新日志"
    fi
    echo ""
}

# 4. 回滚到备份版本
do_rollback() {
    separator
    echo -e "${BOLD}${CYAN}  回滚到备份版本${NC}"
    separator
    echo ""

    if [ ! -d "$BACKUP_DIR" ] || [ -z "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
        error "没有找到备份文件"
        return 1
    fi

    step "可用备份列表:"
    echo ""
    local backups=()
    local i=1
    for file in $(ls -t "$BACKUP_DIR"/backup_*.zip 2>/dev/null); do
        local filename=$(basename "$file")
        local filesize=$(du -h "$file" | cut -f1)
        local filetime=$(echo "$filename" | grep -oP 'backup_\K[0-9_]+')
        echo -e "  ${GREEN}${i}${NC}) ${filetime} (${filesize})"
        backups+=("$file")
        i=$((i + 1))
    done
    echo ""

    if [ ${#backups[@]} -eq 0 ]; then
        error "没有找到备份文件"
        return 1
    fi

    read -p "选择要回滚的备份编号 (1-${#backups[@]}): " choice

    if ! [[ "$choice" =~ ^[0-9]+$ ]] || [ "$choice" -lt 1 ] || [ "$choice" -gt ${#backups[@]} ]; then
        error "无效的选择"
        return 1
    fi

    local backup_file="${backups[$((choice - 1))]}"

    echo ""
    warn "即将回滚到: $(basename "$backup_file")"
    warn "当前代码将被覆盖（配置文件会保留）"
    read -p "确认回滚？(y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        warn "已取消回滚"
        return 0
    fi
    echo ""

    step "解压备份..."
    local extract_dir="${TMP_DIR}/rollback"
    mkdir -p "$extract_dir"
    unzip -q -o "$backup_file" -d "$extract_dir" 2>/dev/null

    if [ $? -ne 0 ]; then
        error "解压备份失败"
        rm -rf "$TMP_DIR"
        return 1
    fi
    success "解压完成"

    step "恢复文件..."
    local restored_count=0
    local skipped_count=0

    while IFS= read -r -d '' file; do
        local rel_path="${file#$extract_dir/}"

        if is_protected "$rel_path"; then
            skipped_count=$((skipped_count + 1))
            continue
        fi

        local target_file="${RUN_DIR}/${rel_path}"
        local target_dir=$(dirname "$target_file")
        if [ ! -d "$target_dir" ]; then
            mkdir -p "$target_dir"
        fi

        cp -f "$file" "$target_file" 2>/dev/null
        if [ $? -eq 0 ]; then
            restored_count=$((restored_count + 1))
        fi
    done < <(find "$extract_dir" -type f -print0)

    rm -rf "$TMP_DIR"

    success "回滚完成: ${restored_count} 个文件已恢复, ${skipped_count} 个文件已跳过"
    separator
}

# 5. 清理缓存
do_clean_cache() {
    separator
    echo -e "${BOLD}${CYAN}  清理缓存${NC}"
    separator
    echo ""

    local cache_dir="${RUN_DIR}/cache"
    local total_size=0

    if [ ! -d "$cache_dir" ]; then
        warn "缓存目录不存在"
        return 0
    fi

    # 计算缓存大小
    if [ -d "$cache_dir/m3u8" ]; then
        total_size=$(du -sh "$cache_dir/m3u8" 2>/dev/null | cut -f1)
    fi

    info "缓存大小: ${total_size}"
    read -p "确认清理缓存？(y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        warn "已取消"
        return 0
    fi

    step "清理缓存..."
    if [ -d "$cache_dir/m3u8" ]; then
        find "$cache_dir/m3u8" -name "*.cache" -delete 2>/dev/null
        # 清理子目录
        find "$cache_dir/m3u8" -mindepth 1 -type d -empty -delete 2>/dev/null
    fi
    success "缓存清理完成"
    echo ""
}

# 显示菜单
show_menu() {
    separator
    echo -e "${BOLD}${CYAN}"
    echo "  ╔══════════════════════════════════════╗"
    echo "  ║   M3U8 广告分析系统 管理脚本 v1.0    ║"
    echo "  ║   GitHub: ssmhdssmhd/gcb             ║"
    echo "  ╚══════════════════════════════════════╝"
    echo -e "${NC}"
    separator
    echo ""
    echo -e "  ${GREEN}当前版本:$(get_current_version)${NC}"
    echo ""
    echo -e "  ${BOLD}请选择操作:${NC}"
    echo -e "    ${GREEN}1${NC}) 更新源码        ${YELLOW}(从 GitHub 下载最新代码)${NC}"
    echo -e "    ${GREEN}2${NC}) 查看当前版本    ${YELLOW}(显示版本和 Commit 信息)${NC}"
    echo -e "    ${GREEN}3${NC}) 查看更新日志    ${YELLOW}(显示最近的更新记录)${NC}"
    echo -e "    ${GREEN}4${NC}) 回滚到备份      ${YELLOW}(恢复到之前的版本)${NC}"
    echo -e "    ${GREEN}5${NC}) 清理缓存        ${YELLOW}(删除 M3U8 缓存文件)${NC}"
    echo -e "    ${RED}0${NC}) 退出"
    echo ""
    separator
}

# 主函数
main() {
    # 如果传入参数，直接执行对应功能
    if [ -n "$1" ]; then
        case "$1" in
            1)
                AUTO_CONFIRM=1
                do_update
                exit $?
                ;;
            2)
                show_version
                exit 0
                ;;
            3)
                show_changelog
                exit 0
                ;;
            4)
                do_rollback
                exit $?
                ;;
            5)
                do_clean_cache
                exit 0
                ;;
            --help|-h)
                echo "用法: $0 [选项]"
                echo ""
                echo "选项:"
                echo "  1    更新源码"
                echo "  2    查看当前版本"
                echo "  3    查看更新日志"
                echo "  4    回滚到备份版本"
                echo "  5    清理缓存"
                echo "  0    退出"
                echo "  -h   显示帮助"
                echo ""
                echo "不带参数运行显示交互式菜单"
                exit 0
                ;;
            *)
                error "未知选项: $1"
                echo "使用 $0 --help 查看帮助"
                exit 1
                ;;
        esac
    fi

    # 交互式菜单
    while true; do
        show_menu
        read -p "  请输入选项 (0-5): " choice

        case "$choice" in
            1)
                echo ""
                do_update
                echo ""
                read -p "按回车键继续..."
                ;;
            2)
                echo ""
                show_version
                read -p "按回车键继续..."
                ;;
            3)
                echo ""
                show_changelog
                read -p "按回车键继续..."
                ;;
            4)
                echo ""
                do_rollback
                echo ""
                read -p "按回车键继续..."
                ;;
            5)
                echo ""
                do_clean_cache
                read -p "按回车键继续..."
                ;;
            0)
                echo ""
                info "再见！"
                exit 0
                ;;
            *)
                echo ""
                error "无效选项，请重新输入"
                sleep 1
                ;;
        esac
    done
}

# 执行主函数
main "$@"
