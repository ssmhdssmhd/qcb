<?php
/**
 * play.modujx11.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'play.modujx11.com',
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
      'enabled' => true,
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
  'confidence_score' => 94,
  'insertion_patterns' => 
  array (
    'pre_roll' => 
    array (
      'found' => false,
      'start_index' => -1,
      'end_index' => -1,
      'duration' => 0,
      'segment_count' => 0,
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
      'start_index' => 736,
      'end_index' => 740,
      'duration' => 7.97,
      'segment_count' => 5,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 10,
      'duration' => 20.27,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 30,
      'duration' => 60.68,
    ),
    'post_roll_ad' => 
    array (
      'count' => 7,
      'duration' => 23.1,
    ),
    'marker_based_ad' => 
    array (
      'count' => 40,
      'duration' => 84.48,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 1,
      'duration' => 2.09,
    ),
    'duration_based_ad' => 
    array (
      'count' => 15,
      'duration' => 32.4,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 7.15,
    'attention_grab_score' => 15,
    'frequency_score' => 100,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 40,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:52:40',
  'analysis_stats' => 
  array (
    'totalSegments' => 741,
    'adSegments' => 53,
    'contentSegments' => 688,
    'totalDuration' => 1482.54,
    'adDuration' => 104.04,
    'contentDuration' => 1378.5,
    'adPercentage' => 7.02,
    'discontinuityCount' => 40,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 15,
    'adClusters' => 47,
    'confidence' => 94,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 741,
      'adCount' => 53,
      'adPercentage' => 7.02,
      'discontinuityCount' => 40,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 94,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 47,
      'ad_density' => 7.15,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:52:40',
);
