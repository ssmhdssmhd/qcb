<?php
/**
 * SkipHandler —— 去广告接口（skip）
 *
 * action：skip
 * 迁移自 mx.php 的 skip case。
 * 用增强规则引擎替换默认规则引擎后，执行 M3U8AdSkipper 分析，
 * 返回统计信息 + 可直接播放的去广告 mxjx 地址。
 *
 * @package handlers
 * @since   5.14.0
 */
class SkipHandler extends BaseHandler {

    /** 入口：skip */
    public function handle() {
        $url = $this->param('url', '');
        if (empty($url)) {
            $this->jsonOut(['success' => false, 'message' => '缺少 url 参数'], 400);
        }

        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';

        $skipper = new M3U8AdSkipper();
        $reflection = new ReflectionClass($skipper);
        $ruleEngineProp = $reflection->getProperty('ruleEngine');
        $ruleEngineProp->setAccessible(true);

        $enhancedEngine = new EnhancedAdRuleEngine([
            'checkDiscontinuity' => true,
            'checkRepetitiveDuration' => true
        ]);
        $enhancedEngine->setDomain($domain);

        if ($this->ctx->useDb && $this->ctx->ruleManager) {
            $dbRules = $this->ctx->ruleManager->getRules($domain);
            if (!empty($dbRules)) {
                $engineReflection = new ReflectionClass($enhancedEngine);
                $applyMethod = $engineReflection->getMethod('applyDomainRules');
                $applyMethod->setAccessible(true);
                $applyMethod->invoke($enhancedEngine, $dbRules);
            }
        }
        $ruleEngineProp->setValue($skipper, $enhancedEngine);

        $filterProp = $reflection->getProperty('filter');
        $filterProp->setAccessible(true);
        $filter = $filterProp->getValue($skipper);

        $filterReflection = new ReflectionClass($filter);
        $filterEngineProp = $filterReflection->getProperty('ruleEngine');
        $filterEngineProp->setAccessible(true);
        $filterEngineProp->setValue($filter, $enhancedEngine);

        $result = $skipper->process($url);

        $this->jsonOut([
            'success' => true,
            'url' => $url,
            'mxjx' => SelfUrlHelper::mxjxUrl($url),
            'stats' => $result['stats'] ?? [],
        ]);
    }
}
