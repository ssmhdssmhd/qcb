#!/bin/bash
# AI 自动学习 - 一键部署定时任务
# 部署后运行此脚本即可自动安装 crontab，无需手动配置
#
# 使用方式：
#   chmod +x install_cron.sh
#   ./install_cron.sh
#
# 卸载：
#   ./install_cron.sh uninstall
#
# 查看当前任务：
#   ./install_cron.sh status

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_BIN="$(command -v php || echo /usr/bin/php)"
CRON_SCRIPT="${SCRIPT_DIR}/cron_ai_autolearn.php"
CRON_LOG="${SCRIPT_DIR}/cache/ai_autolearn_cron.log"
MARKER="# MX-AI-AUTOLEARN"

# 确保日志目录存在
mkdir -p "${SCRIPT_DIR}/cache"

# 学习任务：每 4 小时执行一次（0,4,8,12,16,20 点）
LEARN_CRON="0 0,4,8,12,16,20 * * * ${PHP_BIN} ${CRON_SCRIPT} >> ${CRON_LOG} 2>&1 ${MARKER}"
# 规则清理：每天凌晨 3 点执行一次
CLEANUP_CRON="0 3 * * * ${PHP_BIN} ${CRON_SCRIPT} cleanup >> ${CRON_LOG} 2>&1 ${MARKER}"

action="${1:-install}"

uninstall_cron() {
    echo "正在卸载 AI 自动学习定时任务..."
    # 备份当前 crontab，移除标记行
    crontab -l 2>/dev/null | grep -v "${MARKER}" | crontab - 2>/dev/null || true
    echo "✓ 已卸载 AI 自动学习定时任务"
}

install_cron() {
    echo "========================================"
    echo "  AI 自动学习定时任务部署"
    echo "========================================"
    echo "PHP 路径: ${PHP_BIN}"
    echo "脚本路径: ${CRON_SCRIPT}"
    echo "日志路径: ${CRON_LOG}"
    echo ""

    # 检查 PHP 是否可用
    if [ ! -x "${PHP_BIN}" ]; then
        echo "✗ 错误：找不到 PHP 可执行文件 (${PHP_BIN})"
        exit 1
    fi

    # 检查脚本是否存在
    if [ ! -f "${CRON_SCRIPT}" ]; then
        echo "✗ 错误：找不到脚本文件 (${CRON_SCRIPT})"
        exit 1
    fi

    # 先卸载旧任务（避免重复）
    uninstall_cron >/dev/null 2>&1 || true

    # 添加新任务
    (crontab -l 2>/dev/null; echo "${LEARN_CRON}"; echo "${CLEANUP_CRON}") | crontab -

    echo "✓ 已安装定时任务："
    echo "  - 学习任务：每 4 小时执行一次 (0,4,8,12,16,20 点)"
    echo "  - 规则清理：每天凌晨 3 点执行一次"
    echo ""
    echo "部署完成！系统将自动："
    echo "  1. 每隔几小时从全部启用资源站学习热门视频规则"
    echo "  2. 每天清理失效规则，触发重新获取"
    echo "  3. 前端访问时自动懒触发（无需 cron 也能工作）"
    echo ""
    echo "查看日志：tail -f ${CRON_LOG}"
    echo "手动测试：php ${CRON_SCRIPT} force"
    echo "========================================"
}

status_cron() {
    echo "当前 AI 自动学习定时任务："
    echo "----------------------------------------"
    crontab -l 2>/dev/null | grep "${MARKER}" || echo "（未安装任何任务）"
    echo "----------------------------------------"
}

case "${action}" in
    install)
        install_cron
        ;;
    uninstall|remove)
        uninstall_cron
        ;;
    status|list)
        status_cron
        ;;
    *)
        echo "用法: $0 {install|uninstall|status}"
        echo "  install   - 安装定时任务（默认）"
        echo "  uninstall - 卸载定时任务"
        echo "  status    - 查看当前任务"
        exit 1
        ;;
esac
