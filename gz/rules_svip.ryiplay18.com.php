<?php
/**
 * svip.ryiplay18.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:53:16
 */

return [
    'domain' => 'svip.ryiplay18.com',
    'duration_rules' => [
        [
            'name' => 'short_segment',
            'enabled' => true,
            'type' => 'duration',
            'operator' => '<',
            'threshold' => 2,
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
            'threshold' => 100000,
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
            'threshold' => 100000,
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
    'filename_patterns' => [],
    'ad_threshold' => 50,
    'confidence' => [
        'high' => 80,
        'medium' => 50,
        'low' => 30
    ],
    'confidence_score' => 87,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 24,
            'end_index' => 36,
            'duration' => 51.2,
            'segment_count' => 13
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 15,
            'points' => [
                [
                    'start_index' => 38,
                    'end_index' => 40,
                    'duration' => 10.6,
                    'segment_count' => 3,
                    'position_ratio' => 0.232
                ],
                [
                    'start_index' => 44,
                    'end_index' => 48,
                    'duration' => 17.92,
                    'segment_count' => 5,
                    'position_ratio' => 0.268
                ],
                [
                    'start_index' => 52,
                    'end_index' => 54,
                    'duration' => 10.2,
                    'segment_count' => 3,
                    'position_ratio' => 0.317
                ],
                [
                    'start_index' => 57,
                    'end_index' => 61,
                    'duration' => 20.76,
                    'segment_count' => 5,
                    'position_ratio' => 0.348
                ],
                [
                    'start_index' => 66,
                    'end_index' => 67,
                    'duration' => 7.36,
                    'segment_count' => 2,
                    'position_ratio' => 0.402
                ],
                [
                    'start_index' => 71,
                    'end_index' => 73,
                    'duration' => 14.2,
                    'segment_count' => 3,
                    'position_ratio' => 0.433
                ],
                [
                    'start_index' => 75,
                    'end_index' => 82,
                    'duration' => 31.4,
                    'segment_count' => 8,
                    'position_ratio' => 0.457
                ],
                [
                    'start_index' => 84,
                    'end_index' => 85,
                    'duration' => 8.72,
                    'segment_count' => 2,
                    'position_ratio' => 0.512
                ],
                [
                    'start_index' => 87,
                    'end_index' => 99,
                    'duration' => 51.24,
                    'segment_count' => 13,
                    'position_ratio' => 0.53
                ],
                [
                    'start_index' => 102,
                    'end_index' => 106,
                    'duration' => 18.88,
                    'segment_count' => 5,
                    'position_ratio' => 0.622
                ],
                [
                    'start_index' => 108,
                    'end_index' => 109,
                    'duration' => 7.44,
                    'segment_count' => 2,
                    'position_ratio' => 0.659
                ],
                [
                    'start_index' => 116,
                    'end_index' => 117,
                    'duration' => 6.08,
                    'segment_count' => 2,
                    'position_ratio' => 0.707
                ],
                [
                    'start_index' => 119,
                    'end_index' => 121,
                    'duration' => 11.4,
                    'segment_count' => 3,
                    'position_ratio' => 0.726
                ],
                [
                    'start_index' => 123,
                    'end_index' => 124,
                    'duration' => 5.56,
                    'segment_count' => 2,
                    'position_ratio' => 0.75
                ],
                [
                    'start_index' => 128,
                    'end_index' => 130,
                    'duration' => 11.24,
                    'segment_count' => 3,
                    'position_ratio' => 0.78
                ]
            ]
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 162,
            'end_index' => 163,
            'duration' => 6.44,
            'segment_count' => 2
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 4,
            'duration' => 131.84
        ],
        'mid_roll_ad' => [
            'count' => 15,
            'duration' => 233
        ],
        'post_roll_ad' => [
            'count' => 6,
            'duration' => 101.52
        ],
        'marker_based_ad' => [
            'count' => 23,
            'duration' => 433.2
        ],
        'pattern_based_ad' => [
            'count' => 30,
            'duration' => 484.96
        ],
        'duration_based_ad' => [
            'count' => 2,
            'duration' => 19.36
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 77.44,
        'attention_grab_score' => 100,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 0
    ],
    'marker_stats' => [
        'discontinuity_count' => 28,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:53:16',
    'analysis_stats' => [
        'totalSegments' => 164,
        'adSegments' => 127,
        'contentSegments' => 37,
        'totalDuration' => 653.88,
        'adDuration' => 488.72,
        'contentDuration' => 165.16,
        'adPercentage' => 74.74,
        'discontinuityCount' => 28,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 61,
        'adClusters' => 32,
        'confidence' => 87
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
    'learn_count' => 1,
    'history_stats' => [
        [
            'totalCount' => 164,
            'adCount' => 127,
            'adPercentage' => 74.74,
            'discontinuityCount' => 28,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 87,
            'analyzed_at' => '2026-08-03 02:53:16',
            'adClusterCount' => 32,
            'ad_density' => 77.44
        ]
    ],
    'last_learn_date' => '2026-08-03 02:53:16'
];
