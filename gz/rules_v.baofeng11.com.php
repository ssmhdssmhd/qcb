<?php
/**
 * v.baofeng11.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'v.baofeng11.com',
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
  'confidence_score' => 76,
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
      'found' => true,
      'count' => 1,
      'points' => 
      array (
        0 => 
        array (
          'start_index' => 100,
          'end_index' => 109,
          'duration' => 29,
          'segment_count' => 10,
          'position_ratio' => 0.223,
        ),
      ),
    ),
    'post_roll' => 
    array (
      'found' => false,
      'start_index' => -1,
      'end_index' => -1,
      'duration' => 0,
      'segment_count' => 0,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 1,
      'duration' => 1.76,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 1,
      'duration' => 29,
    ),
    'post_roll_ad' => 
    array (
      'count' => 2,
      'duration' => 2.72,
    ),
    'marker_based_ad' => 
    array (
      'count' => 1,
      'duration' => 29,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 0,
      'duration' => 0,
    ),
    'duration_based_ad' => 
    array (
      'count' => 3,
      'duration' => 4.48,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 2.9,
    'attention_grab_score' => 34,
    'frequency_score' => 61,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 2,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:51:50',
  'analysis_stats' => 
  array (
    'totalSegments' => 449,
    'adSegments' => 13,
    'contentSegments' => 436,
    'totalDuration' => 1345.88,
    'adDuration' => 33.48,
    'contentDuration' => 1312.4,
    'adPercentage' => 2.49,
    'discontinuityCount' => 2,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 2,
    'adClusters' => 4,
    'confidence' => 76,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 449,
      'adCount' => 13,
      'adPercentage' => 2.49,
      'discontinuityCount' => 2,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 76,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 4,
      'ad_density' => 2.9,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:51:50',
);
