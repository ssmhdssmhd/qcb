<?php
/**
 * v14.wsyzym3u8.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'v14.wsyzym3u8.com',
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
      'found' => true,
      'start_index' => 186,
      'end_index' => 187,
      'duration' => 4,
      'segment_count' => 2,
    ),
    'mid_roll' => 
    array (
      'found' => true,
      'count' => 32,
      'points' => 
      array (
        0 => 
        array (
          'start_index' => 209,
          'end_index' => 210,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.159,
        ),
        1 => 
        array (
          'start_index' => 231,
          'end_index' => 232,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.176,
        ),
        2 => 
        array (
          'start_index' => 293,
          'end_index' => 297,
          'duration' => 10,
          'segment_count' => 5,
          'position_ratio' => 0.223,
        ),
        3 => 
        array (
          'start_index' => 322,
          'end_index' => 323,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.245,
        ),
        4 => 
        array (
          'start_index' => 325,
          'end_index' => 326,
          'duration' => 4.03,
          'segment_count' => 2,
          'position_ratio' => 0.247,
        ),
        5 => 
        array (
          'start_index' => 348,
          'end_index' => 349,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.265,
        ),
        6 => 
        array (
          'start_index' => 357,
          'end_index' => 358,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.272,
        ),
        7 => 
        array (
          'start_index' => 366,
          'end_index' => 367,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.279,
        ),
        8 => 
        array (
          'start_index' => 378,
          'end_index' => 379,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.288,
        ),
        9 => 
        array (
          'start_index' => 409,
          'end_index' => 411,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.311,
        ),
        10 => 
        array (
          'start_index' => 492,
          'end_index' => 493,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.374,
        ),
        11 => 
        array (
          'start_index' => 496,
          'end_index' => 497,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.377,
        ),
        12 => 
        array (
          'start_index' => 535,
          'end_index' => 536,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.407,
        ),
        13 => 
        array (
          'start_index' => 552,
          'end_index' => 553,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.42,
        ),
        14 => 
        array (
          'start_index' => 605,
          'end_index' => 607,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.46,
        ),
        15 => 
        array (
          'start_index' => 610,
          'end_index' => 611,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.464,
        ),
        16 => 
        array (
          'start_index' => 664,
          'end_index' => 665,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.505,
        ),
        17 => 
        array (
          'start_index' => 669,
          'end_index' => 670,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.509,
        ),
        18 => 
        array (
          'start_index' => 684,
          'end_index' => 685,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.521,
        ),
        19 => 
        array (
          'start_index' => 694,
          'end_index' => 695,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.528,
        ),
        20 => 
        array (
          'start_index' => 729,
          'end_index' => 731,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.555,
        ),
        21 => 
        array (
          'start_index' => 759,
          'end_index' => 760,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.578,
        ),
        22 => 
        array (
          'start_index' => 774,
          'end_index' => 776,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.589,
        ),
        23 => 
        array (
          'start_index' => 801,
          'end_index' => 802,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.61,
        ),
        24 => 
        array (
          'start_index' => 822,
          'end_index' => 824,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.626,
        ),
        25 => 
        array (
          'start_index' => 827,
          'end_index' => 828,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.629,
        ),
        26 => 
        array (
          'start_index' => 895,
          'end_index' => 897,
          'duration' => 6,
          'segment_count' => 3,
          'position_ratio' => 0.681,
        ),
        27 => 
        array (
          'start_index' => 910,
          'end_index' => 911,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.693,
        ),
        28 => 
        array (
          'start_index' => 1044,
          'end_index' => 1045,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.795,
        ),
        29 => 
        array (
          'start_index' => 1049,
          'end_index' => 1050,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.798,
        ),
        30 => 
        array (
          'start_index' => 1064,
          'end_index' => 1065,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.81,
        ),
        31 => 
        array (
          'start_index' => 1111,
          'end_index' => 1112,
          'duration' => 4,
          'segment_count' => 2,
          'position_ratio' => 0.846,
        ),
      ),
    ),
    'post_roll' => 
    array (
      'found' => true,
      'start_index' => 1266,
      'end_index' => 1268,
      'duration' => 6,
      'segment_count' => 3,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 38,
      'duration' => 104,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 167,
      'duration' => 416.07,
    ),
    'post_roll_ad' => 
    array (
      'count' => 43,
      'duration' => 105.12,
    ),
    'marker_based_ad' => 
    array (
      'count' => 138,
      'duration' => 374.03,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 99,
      'duration' => 300,
    ),
    'duration_based_ad' => 
    array (
      'count' => 1,
      'duration' => 1.12,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 23.82,
    'attention_grab_score' => 25,
    'frequency_score' => 100,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 152,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:52:21',
  'analysis_stats' => 
  array (
    'totalSegments' => 1314,
    'adSegments' => 313,
    'contentSegments' => 1001,
    'totalDuration' => 2626.25,
    'adDuration' => 625.19,
    'contentDuration' => 2001.07,
    'adPercentage' => 23.81,
    'discontinuityCount' => 152,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 493,
    'adClusters' => 248,
    'confidence' => 94,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 1314,
      'adCount' => 313,
      'adPercentage' => 23.81,
      'discontinuityCount' => 152,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 94,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 248,
      'ad_density' => 23.82,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:52:21',
);
