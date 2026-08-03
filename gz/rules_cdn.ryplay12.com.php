<?php
/**
 * cdn.ryplay12.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:53:17
 */

return [
    'domain' => 'cdn.ryplay12.com',
    'duration_rules' => [
        [
            'name' => 'short_segment',
            'enabled' => true,
            'type' => 'duration',
            'operator' => '<',
            'threshold' => 0.5,
            'reason' => '极短片段 (<2秒) 可能是广告',
            'weight' => 30,
            'confidence' => 75,
            'description' => '过滤极短片段（<2秒），通常为广告或片头'
        ]
    ],
    'discontinuity_rules' => [
        [
            'name' => 'discontinuity',
            'enabled' => true,
            'type' => 'discontinuity',
            'reason' => 'DISCONTINUITY 标记表示插播切换',
            'weight' => 80,
            'confidence' => 90,
            'description' => '检测 #EXT-X-DISCONTINUITY 标记，用于识别插播内容'
        ]
    ],
    'sequence_jump_rules' => [
        [
            'name' => 'sequence_jump_forward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'forward',
            'threshold' => 1460,
            'reason' => '序列号向前跳跃可能表示广告插播',
            'weight' => 90,
            'confidence' => 85,
            'description' => '序列号向前跳跃检测，识别广告开始'
        ],
        [
            'name' => 'sequence_jump_backward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'backward',
            'threshold' => 1460,
            'reason' => '序列号向后跳跃可能表示广告结束',
            'weight' => 90,
            'confidence' => 85,
            'description' => '序列号向后跳跃检测，识别广告结束'
        ]
    ],
    'marker_detection' => [
        'cue_markers' => false,
        'scte35' => false,
        'ad_tags' => false,
        'enabled' => false
    ],
    'filename_patterns' => [
        '/^/i'
    ],
    'ad_threshold' => 45,
    'confidence' => [
        'high' => 80,
        'medium' => 50,
        'low' => 30
    ],
    'confidence_score' => 91,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => false,
            'start_index' => -1,
            'end_index' => -1,
            'duration' => 0,
            'segment_count' => 0,
            'detected_count' => 1,
            'avg_duration' => 6.47,
            'avg_segment_count' => 2
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 1,
            'points' => [
                [
                    'start_index' => 35,
                    'end_index' => 37,
                    'duration' => 10.64,
                    'segment_count' => 3,
                    'position_ratio' => 0.38
                ]
            ],
            'detected_count' => 1,
            'avg_clip_count' => 11,
            'avg_duration_per_clip' => 15.83
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 90,
            'end_index' => 91,
            'duration' => 5.8,
            'segment_count' => 2,
            'detected_count' => 1,
            'avg_duration' => 29.6,
            'avg_segment_count' => 7
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 0,
            'duration' => 0,
            'total_count' => 2,
            'total_duration' => 11.7,
            'avg_count_per_video' => 2,
            'sample_count' => 1
        ],
        'mid_roll_ad' => [
            'count' => 1,
            'duration' => 10.64,
            'total_count' => 11,
            'total_duration' => 174.16,
            'avg_count_per_video' => 11,
            'sample_count' => 1
        ],
        'post_roll_ad' => [
            'count' => 2,
            'duration' => 12.28,
            'total_count' => 2,
            'total_duration' => 39.1,
            'avg_count_per_video' => 2,
            'sample_count' => 1
        ],
        'marker_based_ad' => [
            'count' => 16,
            'duration' => 71.32,
            'total_count' => 16,
            'total_duration' => 217.93,
            'avg_count_per_video' => 16,
            'sample_count' => 1
        ],
        'pattern_based_ad' => [
            'count' => 3,
            'duration' => 13.8,
            'total_count' => 22,
            'total_duration' => 249.25,
            'avg_count_per_video' => 22,
            'sample_count' => 1
        ],
        'duration_based_ad' => [
            'count' => 2,
            'duration' => 7.6,
            'total_count' => 7,
            'total_duration' => 122.32,
            'avg_count_per_video' => 7,
            'sample_count' => 1
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => [
            'avg' => 66.67,
            'sample_count' => 1,
            'min' => 66.67,
            'max' => 66.67
        ],
        'attention_grab_score' => [
            'avg' => 100,
            'sample_count' => 1,
            'min' => 100,
            'max' => 100
        ],
        'frequency_score' => [
            'avg' => 100,
            'sample_count' => 1,
            'min' => 100,
            'max' => 100
        ],
        'user_experience_impact' => '严重',
        'watchability_score' => [
            'avg' => 0,
            'sample_count' => 1,
            'min' => 0,
            'max' => 0
        ],
        'pattern_distribution' => [
            '频繁插播' => 1
        ],
        'ux_impact_distribution' => [
            '严重' => 1
        ]
    ],
    'marker_stats' => [
        'discontinuity_count' => 34,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:53:13',
    'analysis_stats' => [
        'totalSegments' => 92,
        'adSegments' => 23,
        'contentSegments' => 69,
        'totalDuration' => 364.44,
        'adDuration' => 82.64,
        'contentDuration' => 281.8,
        'adPercentage' => 22.68,
        'discontinuityCount' => 16,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 41,
        'adClusters' => 19,
        'confidence' => 99
    ],
    'rules' => [
        [
            'name' => 'short_segment',
            'enabled' => true,
            'type' => 'duration',
            'operator' => '<',
            'threshold' => 2,
            'reason' => '极短片段 (<2秒) 可能是广告',
            'weight' => 30,
            'confidence' => 75,
            'description' => '过滤极短片段（<2秒），通常为广告或片头',
            'category' => 'duration'
        ],
        [
            'name' => 'discontinuity',
            'enabled' => true,
            'type' => 'discontinuity',
            'reason' => 'DISCONTINUITY 标记表示插播切换',
            'weight' => 80,
            'confidence' => 90,
            'description' => '检测 #EXT-X-DISCONTINUITY 标记，用于识别插播内容',
            'category' => 'discontinuity'
        ],
        [
            'name' => 'sequence_jump_forward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'forward',
            'threshold' => 100000,
            'reason' => '序列号向前跳跃可能表示广告插播',
            'weight' => 90,
            'confidence' => 85,
            'description' => '序列号向前跳跃检测，识别广告开始',
            'category' => 'sequence'
        ],
        [
            'name' => 'sequence_jump_backward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'backward',
            'threshold' => 100000,
            'reason' => '序列号向后跳跃可能表示广告结束',
            'weight' => 90,
            'confidence' => 85,
            'description' => '序列号向后跳跃检测，识别广告结束',
            'category' => 'sequence'
        ],
        [
            'name' => 'ad_keyword_filename',
            'enabled' => true,
            'type' => 'filename',
            'category' => 'filename',
            'pattern' => '/(?:ad|advert|commercial|promo|sponsor|pre-roll|mid-roll|post-roll)/i',
            'reason' => '文件名包含广告关键词',
            'weight' => 70,
            'confidence' => 80,
            'description' => '检测文件名中包含广告相关关键词'
        ],
        [
            'name' => 'ad_path_keyword',
            'enabled' => true,
            'type' => 'filename',
            'category' => 'filename',
            'pattern' => '/\\/(?:ad|advert|commercial|promo|sponsor)\\//i',
            'reason' => '路径包含广告关键词',
            'weight' => 85,
            'confidence' => 90,
            'description' => '检测 URL 路径中包含广告相关目录名'
        ]
    ],
    'learn_count' => 2,
    'history_stats' => [
        [
            'totalCount' => 92,
            'adCount' => 23,
            'adPercentage' => 22.68,
            'discontinuityCount' => 16,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 99,
            'analyzed_at' => '2026-08-03 02:53:13'
        ],
        [
            'totalCount' => 105,
            'adCount' => 70,
            'adPercentage' => 61.25,
            'discontinuityCount' => 18,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 91,
            'analyzed_at' => '2026-08-03 02:53:17',
            'adClusterCount' => 24,
            'ad_density' => 66.67
        ]
    ],
    'last_learn_date' => '2026-08-03 02:53:17',
    'name' => 'cdn.ryplay12.com'
];
