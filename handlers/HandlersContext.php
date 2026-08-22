<?php
/**
 * HandlersContext —— 解析 handler 的运行上下文（依赖注入）
 *
 * mx.php 在初始化好各类管理器后，构建一份 Context 传给 handlers 分发器，
 * 避免 handler 直接依赖 mx.php 内部变量，提升可测试性。
 *
 * @package handlers
 * @since   5.14.0
 */
class HandlersContext {

    /** @var string 项目根目录 */
    public $rootDir = '';

    /** @var bool 是否启用数据库存储 */
    public $useDb = false;

    /** @var object|null 规则管理器（DomainRuleManager / DbDomainRuleManager） */
    public $ruleManager = null;

    /** @var object|null 资源站管理器 */
    public $siteManager = null;

    /** @var object|null 官替管理器（OfficialReplaceManager / DbOfficialReplaceManager） */
    public $officialReplaceMgr = null;

    /** @var object|null 官方站点管理器 */
    public $officialMgr = null;

    /** @var object|null 代理管理器 */
    public $proxyManager = null;

    /** @var object|null 更新管理器 */
    public $updateManager = null;

    /** @var bool parse/ 框架是否已加载 */
    public $parseLoaded = false;

    /** @var float|null 全局预算（秒），用于给重型链路兜底 */
    public $globalBudget = 25.0;

    /**
     * 便捷读取 GET/POST 参数
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function param($key, $default = '') {
        return $_GET[$key] ?? $_POST[$key] ?? $default;
    }
}
