<?php
/**
 * v10.baofeng10.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'v10.baofeng10.com',
  'duration_rules' => 
  array (
    0 => 
    array (
      'name' => 'short_segment',
      'enabled' => true,
      'type' => 'duration',
      'operator' => '<',
      'threshold' => 2,
      'reason' => '极短片段 (<2秒) 可能是广告',
      'weight' => 30,
    ),
  ),
  'discontinuity_rules' => 
  array (
    0 => 
    array (
      'name' => 'discontinuity',
      'enabled' => false,
      'type' => 'discontinuity',
      'reason' => 'DISCONTINUITY 标记表示插播切换',
      'weight' => 80,
    ),
  ),
  'sequence_jump_rules' => 
  array (
    0 => 
    array (
      'name' => 'sequence_jump_forward',
      'enabled' => true,
      'type' => 'sequence_jump',
      'direction' => 'forward',
      'threshold' => 100000,
      'reason' => '序列号向前跳跃可能表示广告插播',
      'weight' => 90,
    ),
    1 => 
    array (
      'name' => 'sequence_jump_backward',
      'enabled' => true,
      'type' => 'sequence_jump',
      'direction' => 'backward',
      'threshold' => 100000,
      'reason' => '序列号向后跳跃可能表示广告结束',
      'weight' => 90,
    ),
  ),
  'marker_detection' => 
  array (
    'cue_markers' => false,
    'scte35' => false,
    'ad_tags' => false,
    'enabled' => false,
  ),
  'filename_patterns' => 
  array (
  ),
  'ad_threshold' => 50,
  'confidence' => 
  array (
    'high' => 80,
    'medium' => 50,
    'low' => 30,
  ),
  'confidence_score' => 85,
  'insertion_patterns' => 
  array (
    'pre_roll' => 
    array (
      'found' => true,
      'start_index' => 300,
      'end_index' => 309,
      'duration' => 27,
      'segment_count' => 10,
    ),
    'mid_roll' => 
    array (
      'found' => false,
      'count' => 0,
      'points' => 
      array (
      ),
    ),
    'post_roll' => 
    array (
      'found' => true,
      'start_index' => 2690,
      'end_index' => 2699,
      'duration' => 8.92,
      'segment_count' => 10,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 2,
      'duration' => 37.68,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 0,
      'duration' => 0,
    ),
    'post_roll_ad' => 
    array (
      'count' => 2,
      'duration' => 35.88,
    ),
    'marker_based_ad' => 
    array (
      'count' => 2,
      'duration' => 53.96,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 2,
      'duration' => 53.96,
    ),
    'duration_based_ad' => 
    array (
      'count' => 4,
      'duration' => 73.56,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 1.48,
    'attention_grab_score' => 100,
    'frequency_score' => 60,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 4,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:51:54',
  'analysis_stats' => 
  array (
    'totalSegments' => 2700,
    'adSegments' => 40,
    'contentSegments' => 2660,
    'totalDuration' => 2733.84,
    'adDuration' => 73.56,
    'contentDuration' => 2660.28,
    'adPercentage' => 2.69,
    'discontinuityCount' => 4,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 4,
    'adClusters' => 4,
    'confidence' => 85,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 2700,
      'adCount' => 40,
      'adPercentage' => 2.69,
      'discontinuityCount' => 4,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 85,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 4,
      'ad_density' => 1.48,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:51:54',
);
