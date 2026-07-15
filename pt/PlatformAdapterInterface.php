<?php
/**
 * 平台适配器接口
 * 每个平台实现自己的解析、匹配和去广告策略
 */

interface PlatformAdapterInterface
{
    /**
     * 获取平台标识
     * @return string
     */
    public function getPlatformId();

    /**
     * 获取平台名称
     * @return string
     */
    public function getPlatformName();

    /**
     * 检测 URL 是否属于该平台
     * @param string $url
     * @return bool
     */
    public function matches($url);

    /**
     * 从 URL 提取视频 ID
     * @param string $url
     * @return array ['video_id' => '', 'cover_id' => '']
     */
    public function extractVideoId($url);

    /**
     * 获取视频信息（标题、封面等）
     * @param string $url
     * @param array $videoIds
     * @return array ['title' => '', 'cover' => '', 'episode_info' => []]
     */
    public function fetchVideoInfo($url, $videoIds);

    /**
     * 构建搜索关键词
     * @param array $videoInfo
     * @return array
     */
    public function buildSearchKeywords($videoInfo);

    /**
     * 计算匹配分数（平台特定算法）
     * @param array $videoInfo 官方视频信息
     * @param array $candidate 资源站候选项
     * @return float 0-100
     */
    public function calculateMatchScore($videoInfo, $candidate);

    /**
     * 获取去广告规则
     * @return array
     */
    public function getAdRules();

    /**
     * 获取平台特定的标题清理规则
     * @param string $title
     * @return string
     */
    public function cleanTitle($title);

    /**
     * 获取平台配置
     * @return array
     */
    public function getConfig();
}
