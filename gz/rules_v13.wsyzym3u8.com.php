<?php
/**
 * v13.wsyzym3u8.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'v13.wsyzym3u8.com',
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
  'confidence_score' => 93,
  'insertion_patterns' => 
  array (
    'pre_roll' => 
    array (
      'found' => true,
      'start_index' => 129,
      'end_index' => 130,
      'duration' => 4,
      'segment_count' => 2,
    ),
    'mid_roll' => 
    array (
      'found' => true,
      'count' => 29,
      'points' => 
      array (
        0 => 
        array (
          'start_index' => 145,
          'end_index' => 147,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.152,
        ),
        1 => 
        array (
          'start_index' => 158,
          'end_index' => 159,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.166,
        ),
        2 => 
        array (
          'start_index' => 176,
          'end_index' => 178,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.184,
        ),
        3 => 
        array (
          'start_index' => 181,
          'end_index' => 182,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.19,
        ),
        4 => 
        array (
          'start_index' => 184,
          'end_index' => 185,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.193,
        ),
        5 => 
        array (
          'start_index' => 189,
          'end_index' => 191,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.198,
        ),
        6 => 
        array (
          'start_index' => 213,
          'end_index' => 214,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.223,
        ),
        7 => 
        array (
          'start_index' => 217,
          'end_index' => 218,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.227,
        ),
        8 => 
        array (
          'start_index' => 250,
          'end_index' => 253,
          'duration' => 8,
          'segment_count' => 4,
          'position_ratio' => 0.262,
        ),
        9 => 
        array (
          'start_index' => 266,
          'end_index' => 268,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.279,
        ),
        10 => 
        array (
          'start_index' => 273,
          'end_index' => 274,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.286,
        ),
        11 => 
        array (
          'start_index' => 278,
          'end_index' => 279,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.291,
        ),
        12 => 
        array (
          'start_index' => 331,
          'end_index' => 332,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.347,
        ),
        13 => 
        array (
          'start_index' => 348,
          'end_index' => 349,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.365,
        ),
        14 => 
        array (
          'start_index' => 403,
          'end_index' => 404,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.422,
        ),
        15 => 
        array (
          'start_index' => 409,
          'end_index' => 410,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.429,
        ),
        16 => 
        array (
          'start_index' => 446,
          'end_index' => 447,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.468,
        ),
        17 => 
        array (
          'start_index' => 503,
          'end_index' => 504,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.527,
        ),
        18 => 
        array (
          'start_index' => 511,
          'end_index' => 513,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.536,
        ),
        19 => 
        array (
          'start_index' => 573,
          'end_index' => 574,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.601,
        ),
        20 => 
        array (
          'start_index' => 600,
          'end_index' => 601,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.629,
        ),
        21 => 
        array (
          'start_index' => 604,
          'end_index' => 606,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.633,
        ),
        22 => 
        array (
          'start_index' => 659,
          'end_index' => 660,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.691,
        ),
        23 => 
        array (
          'start_index' => 666,
          'end_index' => 667,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.698,
        ),
        24 => 
        array (
          'start_index' => 682,
          'end_index' => 683,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.715,
        ),
        25 => 
        array (
          'start_index' => 687,
          'end_index' => 688,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.72,
        ),
        26 => 
        array (
          'start_index' => 697,
          'end_index' => 698,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.731,
        ),
        27 => 
        array (
          'start_index' => 715,
          'end_index' => 717,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.749,
        ),
        28 => 
        array (
          'start_index' => 746,
          'end_index' => 747,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.782,
        ),
      ),
    ),
    'post_roll' => 
    array (
      'found' => true,
      'start_index' => 939,
      'end_index' => 941,
      'duration' => 6,
      'segment_count' => 3,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 28,
      'duration' => 64,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 122,
      'duration' => 320.03,
    ),
    'post_roll_ad' => 
    array (
      'count' => 28,
      'duration' => 90,
    ),
    'marker_based_ad' => 
    array (
      'count' => 95,
      'duration' => 282.03,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 63,
      'duration' => 198,
    ),
    'duration_based_ad' => 
    array (
      'count' => 0,
      'duration' => 0,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 24.84,
    'attention_grab_score' => 26,
    'frequency_score' => 100,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 108,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:52:13',
  'analysis_stats' => 
  array (
    'totalSegments' => 954,
    'adSegments' => 237,
    'contentSegments' => 717,
    'totalDuration' => 1907.87,
    'adDuration' => 474.03,
    'contentDuration' => 1433.83,
    'adPercentage' => 24.85,
    'discontinuityCount' => 108,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 340,
    'adClusters' => 178,
    'confidence' => 93,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 954,
      'adCount' => 237,
      'adPercentage' => 24.85,
      'discontinuityCount' => 108,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 93,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 178,
      'ad_density' => 24.84,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:52:13',
);
