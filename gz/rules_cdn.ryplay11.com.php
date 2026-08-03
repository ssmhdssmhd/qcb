<?php
/**
 * cdn.ryplay11.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:52:56
 */

return [
    'domain' => 'cdn.ryplay11.com',
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
            'threshold' => 926,
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
            'threshold' => 926,
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
    'ad_threshold' => 50,
    'confidence' => [
        'high' => 80,
        'medium' => 50,
        'low' => 30
    ],
    'confidence_score' => 91,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 27,
            'end_index' => 28,
            'duration' => 10,
            'segment_count' => 2,
            'detected_count' => 1,
            'avg_duration' => 37.3,
            'avg_segment_count' => 9
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 9,
            'points' => [
                [
                    'start_index' => 40,
                    'end_index' => 43,
                    'duration' => 14.32,
                    'segment_count' => 4,
                    'position_ratio' => 0.198
                ],
                [
                    'start_index' => 72,
                    'end_index' => 76,
                    'duration' => 20,
                    'segment_count' => 5,
                    'position_ratio' => 0.356
                ],
                [
                    'start_index' => 82,
                    'end_index' => 84,
                    'duration' => 9.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.406
                ],
                [
                    'start_index' => 86,
                    'end_index' => 87,
                    'duration' => 14.08,
                    'segment_count' => 2,
                    'position_ratio' => 0.426
                ],
                [
                    'start_index' => 94,
                    'end_index' => 96,
                    'duration' => 15.24,
                    'segment_count' => 3,
                    'position_ratio' => 0.465
                ],
                [
                    'start_index' => 120,
                    'end_index' => 121,
                    'duration' => 13.6,
                    'segment_count' => 2,
                    'position_ratio' => 0.594
                ],
                [
                    'start_index' => 126,
                    'end_index' => 128,
                    'duration' => 13.4,
                    'segment_count' => 3,
                    'position_ratio' => 0.624
                ],
                [
                    'start_index' => 161,
                    'end_index' => 162,
                    'duration' => 5.52,
                    'segment_count' => 2,
                    'position_ratio' => 0.797
                ],
                [
                    'start_index' => 164,
                    'end_index' => 165,
                    'duration' => 8.24,
                    'segment_count' => 2,
                    'position_ratio' => 0.812
                ]
            ],
            'detected_count' => 1,
            'avg_clip_count' => 91,
            'avg_duration_per_clip' => 16.89
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 198,
            'end_index' => 199,
            'duration' => 14.16,
            'segment_count' => 2,
            'detected_count' => 1,
            'avg_duration' => 19.85,
            'avg_segment_count' => 6
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 1,
            'duration' => 10,
            'total_count' => 24,
            'total_duration' => 400.72,
            'avg_count_per_video' => 24,
            'sample_count' => 1
        ],
        'mid_roll_ad' => [
            'count' => 9,
            'duration' => 113.68,
            'total_count' => 91,
            'total_duration' => 1537.22,
            'avg_count_per_video' => 91,
            'sample_count' => 1
        ],
        'post_roll_ad' => [
            'count' => 3,
            'duration' => 38.24,
            'total_count' => 26,
            'total_duration' => 350.97,
            'avg_count_per_video' => 26,
            'sample_count' => 1
        ],
        'marker_based_ad' => [
            'count' => 34,
            'duration' => 259.08,
            'total_count' => 145,
            'total_duration' => 2223.88,
            'avg_count_per_video' => 145,
            'sample_count' => 1
        ],
        'pattern_based_ad' => [
            'count' => 30,
            'duration' => 245.16,
            'total_count' => 176,
            'total_duration' => 2400.94,
            'avg_count_per_video' => 176,
            'sample_count' => 1
        ],
        'duration_based_ad' => [
            'count' => 4,
            'duration' => 14.12,
            'total_count' => 66,
            'total_duration' => 1097.74,
            'avg_count_per_video' => 66,
            'sample_count' => 1
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => [
            'avg' => 70.8,
            'sample_count' => 1,
            'min' => 70.8,
            'max' => 70.8
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
        'discontinuity_count' => 201,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:52:47',
    'analysis_stats' => [
        'totalSegments' => 202,
        'adSegments' => 74,
        'contentSegments' => 128,
        'totalDuration' => 1254.56,
        'adDuration' => 376,
        'contentDuration' => 878.56,
        'adPercentage' => 29.97,
        'discontinuityCount' => 34,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 67,
        'adClusters' => 53,
        'confidence' => 96
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
            'totalCount' => 202,
            'adCount' => 74,
            'adPercentage' => 29.97,
            'discontinuityCount' => 34,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 96,
            'analyzed_at' => '2026-08-03 02:52:47'
        ],
        [
            'totalCount' => 1000,
            'adCount' => 708,
            'adPercentage' => 62.1,
            'discontinuityCount' => 167,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 91,
            'analyzed_at' => '2026-08-03 02:52:56',
            'adClusterCount' => 206,
            'ad_density' => 70.8
        ]
    ],
    'last_learn_date' => '2026-08-03 02:52:56',
    'name' => 'cdn.ryplay11.com'
];
