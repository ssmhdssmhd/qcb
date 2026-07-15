<?php
/**
 * pt 核心调度器
 * 统一管理平台适配器、AI 引擎和去广告引擎
 * 官替调用入口通过 PtManager 调用 pt 里的规则
 */

require_once __DIR__ . '/PlatformAdapterInterface.php';
require_once __DIR__ . '/AbstractPlatformAdapter.php';
require_once __DIR__ . '/PtAIAnalyzer.php';
require_once __DIR__ . '/PtAdSkipEngine.php';

class PtManager
{
    /** @var PlatformAdapterInterface[] */
    private $adapters = [];

    /** @var PlatformAdapterInterface|null */
    private $currentAdapter = null;

    /** @var PtAIAnalyzer */
    private $aiAnalyzer;

    /** @var PtAdSkipEngine */
    private $adSkipEngine;

    /** @var array */
    private $config;

    private static $instance = null;

    /**
     * 单例获取
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->aiAnalyzer = new PtAIAnalyzer();
        $this->adSkipEngine = new PtAdSkipEngine();
        $this->config = $this->loadConfig();
        $this->registerAdapters();
    }

    /**
     * 加载配置
     */
    private function loadConfig()
    {
        $configFile = __DIR__ . '/pt_config.php';
        if (file_exists($configFile)) {
            return require $configFile;
        }
        return [
            'version' => '4.0.0',
            'enabled' => true,
            'match_threshold' => 50,
            'search_sites' => ['量子', '暴风', '非凡', '天影', '猫眼', '最大', '索尼', 'OK资源', '红牛'],
            'max_search_sites' => 10,
            'enable_ai' => true,
            'enable_ad_skip' => true,
            'ad_skip_mode' => 'blank',
        ];
    }

    /**
     * 注册所有平台适配器
     */
    private function registerAdapters()
    {
        $adapterFiles = [
            'TencentVideoAdapter.php',
            'IqiyiAdapter.php',
            'YoukuAdapter.php',
            'MgtvAdapter.php',
            'BilibiliAdapter.php',
            'SohuAdapter.php',
        ];

        foreach ($adapterFiles as $file) {
            $path = __DIR__ . '/' . $file;
            if (file_exists($path)) {
                require_once $path;
                $className = preg_replace('/\.php$/', '', $file);
                if (class_exists($className)) {
                    try {
                        $adapter = new $className();
                        if ($adapter instanceof PlatformAdapterInterface) {
                            $this->adapters[$adapter->getPlatformId()] = $adapter;
                        }
                    } catch (Throwable $e) {
                        // 适配器加载失败时静默跳过
                    }
                }
            }
        }
    }

    /**
     * 检测 URL 对应的平台适配器
     * @param string $url
     * @return PlatformAdapterInterface|null
     */
    public function detectAdapter($url)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->matches($url)) {
                $this->currentAdapter = $adapter;
                return $adapter;
            }
        }
        return null;
    }

    /**
     * 解析官方 URL，返回替换信息
     * @param string $url 官方平台 URL
     * @param array $videoInfo 预获取的视频信息（可选）
     * @param array $searchResults 预获取的搜索结果（可选）
     * @return array
     */
    public function resolve($url, $videoInfo = null, $searchResults = null)
    {
        $adapter = $this->detectAdapter($url);
        if (!$adapter) {
            return [
                'success' => false,
                'message' => 'pt: 不支持的视频平台',
                'adapter' => null,
            ];
        }

        // 获取视频信息
        if ($videoInfo === null) {
            $videoIds = $adapter->extractVideoId($url);
            $videoInfo = $adapter->fetchVideoInfo($url, $videoIds);
        }

        if (empty($videoInfo['title'])) {
            return [
                'success' => false,
                'message' => 'pt: 无法获取视频信息',
                'adapter' => $adapter->getPlatformId(),
            ];
        }

        // 清理标题
        $cleanedTitle = $adapter->cleanTitle($videoInfo['title']);
        if (!empty($cleanedTitle)) {
            $videoInfo['title'] = $cleanedTitle;
            $videoInfo['base_title'] = $cleanedTitle;
        }

        // 构建搜索关键词
        $keywords = $adapter->buildSearchKeywords($videoInfo);

        // 计算匹配分数
        $matches = [];
        if (!empty($searchResults)) {
            // AI 智能匹配
            if ($this->config['enable_ai'] ?? true) {
                $aiResult = $this->aiAnalyzer->smartMatch($videoInfo, $searchResults);
                if (!empty($aiResult['best_match'])) {
                    $matches = $aiResult['all_matches'];
                }
            }

            // 平台特定匹配
            if (empty($matches)) {
                foreach ($searchResults as $candidate) {
                    $score = $adapter->calculateMatchScore($videoInfo, $candidate);
                    if ($score >= ($this->config['match_threshold'] ?? 50)) {
                        $matches[] = [
                            'video' => $candidate,
                            'score' => $score,
                            'method' => 'platform_specific',
                        ];
                    }
                }
            }

            // 最佳努力匹配
            if (empty($matches) && !empty($searchResults)) {
                foreach ($searchResults as $candidate) {
                    $score = $adapter->calculateMatchScore($videoInfo, $candidate);
                    if ($score >= 40) {
                        $matches[] = [
                            'video' => $candidate,
                            'score' => $score,
                            'method' => 'best_effort',
                        ];
                    }
                }
            }
        }

        // 排序
        usort($matches, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 去广告规则
        $adRules = $adapter->getAdRules();

        return [
            'success' => !empty($matches),
            'adapter' => $adapter->getPlatformId(),
            'platform' => $adapter->getPlatformName(),
            'video_info' => $videoInfo,
            'keywords' => $keywords,
            'matches' => $matches,
            'best_match' => !empty($matches) ? $matches[0] : null,
            'ad_rules' => $adRules,
            'message' => empty($matches) ? 'pt: 未找到匹配的资源' : 'pt: 匹配成功',
        ];
    }

    /**
     * 对 M3U8 内容进行去广告处理
     * @param string $m3u8Content
     * @param string $platformId
     * @return array
     */
    public function processAdSkip($m3u8Content, $platformId = '')
    {
        $platformRules = [];
        if (isset($this->adapters[$platformId])) {
            $rules = $this->adapters[$platformId]->getAdRules();
            $platformRules = [
                'ad_keywords' => $rules['ad_keywords'] ?? [],
                'ad_uri_patterns' => $rules['ad_uri_patterns'] ?? [],
                'ad_duration_threshold' => $rules['ad_duration_threshold'] ?? 5,
            ];
        }

        return $this->adSkipEngine->process($m3u8Content, $platformRules);
    }

    /**
     * 获取所有已注册的适配器
     */
    public function getAdapters()
    {
        return $this->adapters;
    }

    /**
     * 获取 AI 分析器
     */
    public function getAIAnalyzer()
    {
        return $this->aiAnalyzer;
    }

    /**
     * 获取去广告引擎
     */
    public function getAdSkipEngine()
    {
        return $this->adSkipEngine;
    }

    /**
     * 获取配置
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 分析匹配失败原因
     */
    public function analyzeFailure($videoInfo, $searchResults)
    {
        return $this->aiAnalyzer->analyzeFailure($videoInfo, $searchResults);
    }

    /**
     * 学习反馈
     */
    public function learnFromMatch($videoInfo, $matchedVideo, $isCorrect)
    {
        $this->aiAnalyzer->learnFromMatch($videoInfo, $matchedVideo, $isCorrect);
    }
}
