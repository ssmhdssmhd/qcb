<?php
/**
 * v.lzcdn23.com 域名广告和插播规则
 * 清理于: 2026-07-07 22:40:05
 */

return array (
  'domain' => 'v.lzcdn23.com',
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
  'confidence_score' => 78,
  'insertion_patterns' => 
  array (
    'pre_roll' => 
    array (
      'found' => true,
      'start_index' => 39,
      'end_index' => 50,
      'duration' => 47.68,
      'segment_count' => 12,
    ),
    'mid_roll' => 
    array (
      'found' => true,
      'count' => 29,
      'points' => 
      array (
        0 => 
        array (
          'start_index' => 61,
          'end_index' => 63,
          'duration' => 12,
          'segment_count' => 3,
          'position_ratio' => 0.176,
        ),
        1 => 
        array (
          'start_index' => 68,
          'end_index' => 69,
          'duration' => 8,
          'segment_count' => 2,
          'position_ratio' => 0.197,
        ),
        2 => 
        array (
          'start_index' => 71,
          'end_index' => 72,
          'duration' => 8,
          'segment_count' => 2,
          'position_ratio' => 0.205,
        ),
        3 => 
        array (
          'start_index' => 74,
          'end_index' => 79,
          'duration' => 24,
          'segment_count' => 6,
          'position_ratio' => 0.214,
        ),
        4 => 
        array (
          'start_index' => 81,
          'end_index' => 82,
          'duration' => 5.56,
          'segment_count' => 2,
          'position_ratio' => 0.234,
        ),
        5 => 
        array (
          'start_index' => 89,
          'end_index' => 91,
          'duration' => 9.96,
          'segment_count' => 3,
          'position_ratio' => 0.257,
        ),
        6 => 
        array (
          'start_index' => 95,
          'end_index' => 96,
          'duration' => 8,
          'segment_count' => 2,
          'position_ratio' => 0.275,
        ),
        7 => 
        array (
          'start_index' => 99,
          'end_index' => 105,
          'duration' => 28,
          'segment_count' => 7,
          'position_ratio' => 0.286,
        ),
        8 => 
        array (
          'start_index' => 107,
          'end_index' => 110,
          'duration' => 14.24,
          'segment_count' => 4,
          'position_ratio' => 0.309,
        ),
        9 => 
        array (
          'start_index' => 125,
          'end_index' => 126,
          'duration' => 8,
          'segment_count' => 2,
          'position_ratio' => 0.361,
        ),
        10 => 
        array (
          'start_index' => 133,
          'end_index' => 135,
          'duration' => 12,
          'segment_count' => 3,
          'position_ratio' => 0.384,
        ),
        11 => 
        array (
          'start_index' => 148,
          'end_index' => 151,
          'duration' => 16,
          'segment_count' => 4,
          'position_ratio' => 0.428,
        ),
        12 => 
        array (
          'start_index' => 155,
          'end_index' => 157,
          'duration' => 11.96,
          'segment_count' => 3,
          'position_ratio' => 0.448,
        ),
        13 => 
        array (
          'start_index' => 159,
          'end_index' => 161,
          'duration' => 12,
          'segment_count' => 3,
          'position_ratio' => 0.46,
        ),
        14 => 
        array (
          'start_index' => 168,
          'end_index' => 171,
          'duration' => 12.88,
          'segment_count' => 4,
          'position_ratio' => 0.486,
        ),
        15 => 
        array (
          'start_index' => 173,
          'end_index' => 174,
          'duration' => 7.96,
          'segment_count' => 2,
          'position_ratio' => 0.5,
        ),
        16 => 
        array (
          'start_index' => 177,
          'end_index' => 178,
          'duration' => 8,
          'segment_count' => 2,
          'position_ratio' => 0.512,
        ),
        17 => 
        array (
          'start_index' => 185,
          'end_index' => 190,
          'duration' => 24,
          'segment_count' => 6,
          'position_ratio' => 0.535,
        ),
        18 => 
        array (
          'start_index' => 198,
          'end_index' => 204,
          'duration' => 27.8,
          'segment_count' => 7,
          'position_ratio' => 0.572,
        ),
        19 => 
        array (
          'start_index' => 215,
          'end_index' => 217,
          'duration' => 11.96,
          'segment_count' => 3,
          'position_ratio' => 0.621,
        ),
        20 => 
        array (
          'start_index' => 219,
          'end_index' => 220,
          'duration' => 8.88,
          'segment_count' => 2,
          'position_ratio' => 0.633,
        ),
        21 => 
        array (
          'start_index' => 222,
          'end_index' => 227,
          'duration' => 23.8,
          'segment_count' => 6,
          'position_ratio' => 0.642,
        ),
        22 => 
        array (
          'start_index' => 243,
          'end_index' => 244,
          'duration' => 8.12,
          'segment_count' => 2,
          'position_ratio' => 0.702,
        ),
        23 => 
        array (
          'start_index' => 248,
          'end_index' => 250,
          'duration' => 10.64,
          'segment_count' => 3,
          'position_ratio' => 0.717,
        ),
        24 => 
        array (
          'start_index' => 254,
          'end_index' => 256,
          'duration' => 12.4,
          'segment_count' => 3,
          'position_ratio' => 0.734,
        ),
        25 => 
        array (
          'start_index' => 270,
          'end_index' => 271,
          'duration' => 5.64,
          'segment_count' => 2,
          'position_ratio' => 0.78,
        ),
        26 => 
        array (
          'start_index' => 274,
          'end_index' => 278,
          'duration' => 20,
          'segment_count' => 5,
          'position_ratio' => 0.792,
        ),
        27 => 
        array (
          'start_index' => 280,
          'end_index' => 281,
          'duration' => 7.56,
          'segment_count' => 2,
          'position_ratio' => 0.809,
        ),
        28 => 
        array (
          'start_index' => 289,
          'end_index' => 292,
          'duration' => 15.88,
          'segment_count' => 4,
          'position_ratio' => 0.835,
        ),
      ),
    ),
    'post_roll' => 
    array (
      'found' => true,
      'start_index' => 332,
      'end_index' => 336,
      'duration' => 20,
      'segment_count' => 5,
    ),
  ),
  'ad_type_stats' => 
  array (
    'pre_roll_ad' => 
    array (
      'count' => 11,
      'duration' => 146.44,
    ),
    'mid_roll_ad' => 
    array (
      'count' => 64,
      'duration' => 525.8,
    ),
    'post_roll_ad' => 
    array (
      'count' => 17,
      'duration' => 111.64,
    ),
    'marker_based_ad' => 
    array (
      'count' => 35,
      'duration' => 415.4,
    ),
    'pattern_based_ad' => 
    array (
      'count' => 88,
      'duration' => 765.48,
    ),
    'duration_based_ad' => 
    array (
      'count' => 5,
      'duration' => 35,
    ),
  ),
  'psychological_profile' => 
  array (
    'interruption_pattern' => '频繁插播',
    'ad_density' => 57.51,
    'attention_grab_score' => 50,
    'frequency_score' => 100,
    'user_experience_impact' => '严重',
    'watchability_score' => 30,
  ),
  'marker_stats' => 
  array (
    'discontinuity_count' => 36,
    'cue_marker_count' => 0,
    'scte35_count' => 0,
    'ad_tag_count' => 0,
  ),
  'note' => '基于靶机测试分析自动生成的规则',
  'analysis_date' => '2026-07-02 14:53:22',
  'analysis_stats' => 
  array (
    'totalSegments' => 346,
    'adSegments' => 199,
    'contentSegments' => 147,
    'totalDuration' => 1382.6,
    'adDuration' => 783.88,
    'contentDuration' => 598.72,
    'adPercentage' => 56.7,
    'discontinuityCount' => 36,
    'cueMarkerCount' => 0,
    'scte35Count' => 0,
    'adTagCount' => 0,
    'sequenceJumps' => 2,
    'adClusters' => 92,
    'confidence' => 78,
  ),
  'learn_count' => 1,
  'history_stats' => 
  array (
    0 => 
    array (
      'totalCount' => 346,
      'adCount' => 199,
      'adPercentage' => 56.7,
      'discontinuityCount' => 36,
      'cueMarkerCount' => 0,
      'scte35Count' => 0,
      'adTagCount' => 0,
      'confidence' => 78,
      'analyzed_at' => '2026-07-07 22:40:05',
      'adClusterCount' => 92,
      'ad_density' => 57.51,
    ),
  ),
  'last_learn_date' => '2026-07-02 14:53:22',
);
