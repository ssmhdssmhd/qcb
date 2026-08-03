<?php
/**
 * cdn.ryplay10.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:53:10
 */

return [
    'domain' => 'cdn.ryplay10.com',
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
            'threshold' => 1236,
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
            'threshold' => 1236,
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
    'confidence_score' => 90,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 15,
            'end_index' => 33,
            'duration' => 75.92,
            'segment_count' => 19,
            'detected_count' => 1,
            'avg_duration' => 11.36,
            'avg_segment_count' => 3
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 15,
            'points' => [
                [
                    'start_index' => 35,
                    'end_index' => 37,
                    'duration' => 10.76,
                    'segment_count' => 3,
                    'position_ratio' => 0.193
                ],
                [
                    'start_index' => 39,
                    'end_index' => 45,
                    'duration' => 27.76,
                    'segment_count' => 7,
                    'position_ratio' => 0.215
                ],
                [
                    'start_index' => 49,
                    'end_index' => 57,
                    'duration' => 33.88,
                    'segment_count' => 9,
                    'position_ratio' => 0.271
                ],
                [
                    'start_index' => 60,
                    'end_index' => 61,
                    'duration' => 8.92,
                    'segment_count' => 2,
                    'position_ratio' => 0.331
                ],
                [
                    'start_index' => 68,
                    'end_index' => 72,
                    'duration' => 21.48,
                    'segment_count' => 5,
                    'position_ratio' => 0.376
                ],
                [
                    'start_index' => 74,
                    'end_index' => 82,
                    'duration' => 35.24,
                    'segment_count' => 9,
                    'position_ratio' => 0.409
                ],
                [
                    'start_index' => 84,
                    'end_index' => 87,
                    'duration' => 16.52,
                    'segment_count' => 4,
                    'position_ratio' => 0.464
                ],
                [
                    'start_index' => 89,
                    'end_index' => 93,
                    'duration' => 17.04,
                    'segment_count' => 5,
                    'position_ratio' => 0.492
                ],
                [
                    'start_index' => 95,
                    'end_index' => 107,
                    'duration' => 52.48,
                    'segment_count' => 13,
                    'position_ratio' => 0.525
                ],
                [
                    'start_index' => 109,
                    'end_index' => 113,
                    'duration' => 20.44,
                    'segment_count' => 5,
                    'position_ratio' => 0.602
                ],
                [
                    'start_index' => 115,
                    'end_index' => 122,
                    'duration' => 31.48,
                    'segment_count' => 8,
                    'position_ratio' => 0.635
                ],
                [
                    'start_index' => 127,
                    'end_index' => 128,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.702
                ],
                [
                    'start_index' => 132,
                    'end_index' => 135,
                    'duration' => 15.4,
                    'segment_count' => 4,
                    'position_ratio' => 0.729
                ],
                [
                    'start_index' => 137,
                    'end_index' => 140,
                    'duration' => 15,
                    'segment_count' => 4,
                    'position_ratio' => 0.757
                ],
                [
                    'start_index' => 142,
                    'end_index' => 143,
                    'duration' => 5.16,
                    'segment_count' => 2,
                    'position_ratio' => 0.785
                ]
            ],
            'detected_count' => 1,
            'avg_clip_count' => 15,
            'avg_duration_per_clip' => 21.11
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 177,
            'end_index' => 180,
            'duration' => 11.2,
            'segment_count' => 4,
            'detected_count' => 1,
            'avg_duration' => 11.12,
            'avg_segment_count' => 3
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 3,
            'duration' => 105.6,
            'total_count' => 5,
            'total_duration' => 80.88,
            'avg_count_per_video' => 5,
            'sample_count' => 1
        ],
        'mid_roll_ad' => [
            'count' => 15,
            'duration' => 319.56,
            'total_count' => 15,
            'total_duration' => 316.64,
            'avg_count_per_video' => 15,
            'sample_count' => 1
        ],
        'post_roll_ad' => [
            'count' => 5,
            'duration' => 95.48,
            'total_count' => 3,
            'total_duration' => 65.52,
            'avg_count_per_video' => 3,
            'sample_count' => 1
        ],
        'marker_based_ad' => [
            'count' => 25,
            'duration' => 523.32,
            'total_count' => 27,
            'total_duration' => 476.08,
            'avg_count_per_video' => 27,
            'sample_count' => 1
        ],
        'pattern_based_ad' => [
            'count' => 29,
            'duration' => 539.08,
            'total_count' => 27,
            'total_duration' => 478.16,
            'avg_count_per_video' => 27,
            'sample_count' => 1
        ],
        'duration_based_ad' => [
            'count' => 3,
            'duration' => 53,
            'total_count' => 4,
            'total_duration' => 92.96,
            'avg_count_per_video' => 4,
            'sample_count' => 1
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => [
            'avg' => 78.88,
            'sample_count' => 1,
            'min' => 78.88,
            'max' => 78.88
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
        'discontinuity_count' => 70,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:53:08',
    'analysis_stats' => [
        'totalSegments' => 181,
        'adSegments' => 143,
        'contentSegments' => 38,
        'totalDuration' => 722.8,
        'adDuration' => 552.08,
        'contentDuration' => 170.72,
        'adPercentage' => 76.38,
        'discontinuityCount' => 37,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 70,
        'adClusters' => 31,
        'confidence' => 89
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
            'totalCount' => 181,
            'adCount' => 143,
            'adPercentage' => 76.38,
            'discontinuityCount' => 37,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 89,
            'analyzed_at' => '2026-08-03 02:53:08'
        ],
        [
            'totalCount' => 161,
            'adCount' => 127,
            'adPercentage' => 74.8,
            'discontinuityCount' => 33,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 90,
            'analyzed_at' => '2026-08-03 02:53:10',
            'adClusterCount' => 29,
            'ad_density' => 78.88
        ]
    ],
    'last_learn_date' => '2026-08-03 02:53:10',
    'name' => 'cdn.ryplay10.com'
];
