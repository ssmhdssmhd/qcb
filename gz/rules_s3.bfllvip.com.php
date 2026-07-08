<?php
/**
 * s3.bfllvip.com 域名广告和插播规则
 * 自动生成于: 2026-07-08 00:16:51
 */

return [
    'domain' => 's3.bfllvip.com',
    'duration_rules' => [
        [
            'name' => 'short_segment',
            'enabled' => true,
            'type' => 'duration',
            'operator' => '<',
            'threshold' => 2,
            'reason' => '极短片段 (<2秒) 可能是广告',
            'weight' => 30
        ]
    ],
    'discontinuity_rules' => [
        [
            'name' => 'discontinuity',
            'enabled' => false,
            'type' => 'discontinuity',
            'reason' => 'DISCONTINUITY 标记表示插播切换',
            'weight' => 80
        ]
    ],
    'sequence_jump_rules' => [
        [
            'name' => 'sequence_jump_forward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'forward',
            'threshold' => 100000,
            'reason' => '序列号向前跳跃可能表示广告插播',
            'weight' => 90
        ],
        [
            'name' => 'sequence_jump_backward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'backward',
            'threshold' => 100000,
            'reason' => '序列号向后跳跃可能表示广告结束',
            'weight' => 90
        ]
    ],
    'marker_detection' => [
        'cue_markers' => false,
        'scte35' => false,
        'ad_tags' => false,
        'enabled' => false
    ],
    'filename_patterns' => [],
    'ad_threshold' => 50,
    'confidence' => [
        'high' => 80,
        'medium' => 50,
        'low' => 30
    ],
    'confidence_score' => 83,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 300,
            'end_index' => 309,
            'duration' => 26.92,
            'segment_count' => 10
        ],
        'mid_roll' => [
            'found' => false,
            'count' => 0,
            'points' => []
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 2750,
            'end_index' => 2759,
            'duration' => 8.8,
            'segment_count' => 10
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 2,
            'duration' => 37.16
        ],
        'mid_roll_ad' => [
            'count' => 0,
            'duration' => 0
        ],
        'post_roll_ad' => [
            'count' => 2,
            'duration' => 36.24
        ],
        'marker_based_ad' => [
            'count' => 2,
            'duration' => 54.36
        ],
        'pattern_based_ad' => [
            'count' => 2,
            'duration' => 54.36
        ],
        'duration_based_ad' => [
            'count' => 4,
            'duration' => 73.4
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '中间插播',
        'ad_density' => 1.45,
        'attention_grab_score' => 34,
        'frequency_score' => 60,
        'user_experience_impact' => '中等',
        'watchability_score' => 78
    ],
    'marker_stats' => [
        'discontinuity_count' => 4,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-08 00:16:51',
    'analysis_stats' => [
        'totalSegments' => 2760,
        'adSegments' => 40,
        'contentSegments' => 2720,
        'totalDuration' => 2793.76,
        'adDuration' => 73.4,
        'contentDuration' => 2720.36,
        'adPercentage' => 2.63,
        'discontinuityCount' => 4,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 4,
        'adClusters' => 4,
        'confidence' => 83
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'totalCount' => 2760,
            'adCount' => 40,
            'adPercentage' => 2.63,
            'discontinuityCount' => 4,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 83,
            'analyzed_at' => '2026-07-08 00:16:51',
            'adClusterCount' => 4,
            'ad_density' => 1.45
        ]
    ],
    'last_learn_date' => '2026-07-08 00:16:51'
];
