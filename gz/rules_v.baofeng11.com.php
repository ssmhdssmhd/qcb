<?php
/**
 * v.baofeng11.com 域名广告和插播规则
 * 自动生成于: 2026-07-02 14:51:50
 */

return [
    'domain' => 'v.baofeng11.com',
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
    'confidence_score' => 76,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => false,
            'start_index' => -1,
            'end_index' => -1,
            'duration' => 0,
            'segment_count' => 0
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 1,
            'points' => [
                [
                    'start_index' => 100,
                    'end_index' => 109,
                    'duration' => 29,
                    'segment_count' => 10,
                    'position_ratio' => 0.223
                ]
            ]
        ],
        'post_roll' => [
            'found' => false,
            'start_index' => -1,
            'end_index' => -1,
            'duration' => 0,
            'segment_count' => 0
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 1,
            'duration' => 1.76
        ],
        'mid_roll_ad' => [
            'count' => 1,
            'duration' => 29
        ],
        'post_roll_ad' => [
            'count' => 2,
            'duration' => 2.72
        ],
        'marker_based_ad' => [
            'count' => 1,
            'duration' => 29
        ],
        'pattern_based_ad' => [
            'count' => 0,
            'duration' => 0
        ],
        'duration_based_ad' => [
            'count' => 3,
            'duration' => 4.48
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 2.9,
        'attention_grab_score' => 34,
        'frequency_score' => 61,
        'user_experience_impact' => '严重',
        'watchability_score' => 30
    ],
    'marker_stats' => [
        'discontinuity_count' => 2,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-02 14:51:50',
    'analysis_stats' => [
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
        'confidence' => 76
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000000.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 0,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 5,
                        'title' => '',
                        'uri' => '0000001.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000002.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 2,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000003.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 3,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000004.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 4,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000005.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 5,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000006.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 6,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000007.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 7,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '0000008.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 8,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => '0000009.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 9,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 70,
                    'categories' => [
                        'duration' => 30,
                        'position' => 40
                    ],
                    'totalWeight' => 70
                ],
                [
                    'segment' => [
                        'duration' => 2.04,
                        'title' => '',
                        'uri' => '0000010.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 10,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.56,
                        'title' => '',
                        'uri' => '0000011.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 11,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.4,
                        'title' => '',
                        'uri' => '0000012.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 12,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000013.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 13,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '0000014.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 14,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => '0000015.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 15,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '0000016.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 16,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000017.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 17,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '0000018.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 18,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000019.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 19,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => '0000020.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 20,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.36,
                        'title' => '',
                        'uri' => '0000021.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 21,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000022.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 22,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000023.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 23,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => '0000024.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 24,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.6,
                        'title' => '',
                        'uri' => '0000025.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 25,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '0000026.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 26,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000027.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 27,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000028.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 28,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000029.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 29,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000030.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 30,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => '0000031.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 31,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => '0000032.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 32,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.16,
                        'title' => '',
                        'uri' => '0000033.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 33,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => '0000034.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 34,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => '0000035.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 35,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '0000036.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 36,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000037.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 37,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000038.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 38,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000039.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 39,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.36,
                        'title' => '',
                        'uri' => '0000040.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 40,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => '0000041.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 41,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000042.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 42,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000043.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 43,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.32,
                        'title' => '',
                        'uri' => '0000044.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 44,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000045.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 45,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => '0000046.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 46,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.04,
                        'title' => '',
                        'uri' => '0000047.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 47,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.32,
                        'title' => '',
                        'uri' => '0000048.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 48,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000049.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 49,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000050.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 50,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => '0000051.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 51,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000052.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 52,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000053.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 53,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000054.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 54,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '0000055.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 55,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000056.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 56,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => '0000057.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 57,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '0000058.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 58,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '0000059.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 59,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000060.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 60,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.68,
                        'title' => '',
                        'uri' => '0000061.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 61,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => '0000062.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 62,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000063.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 63,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000064.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 64,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000065.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 65,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000066.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 66,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000067.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 67,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000068.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 68,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '0000069.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 69,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000070.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 70,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000071.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 71,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000072.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 72,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.32,
                        'title' => '',
                        'uri' => '0000073.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 73,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => '0000074.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 74,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000075.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 75,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000076.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 76,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000077.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 77,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => '0000078.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 78,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '0000079.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 79,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => '0000080.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 80,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '0000081.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 81,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => '0000082.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 82,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000083.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 83,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '0000084.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 84,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000085.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 85,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000086.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 86,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000087.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 87,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => '0000088.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 88,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => '0000089.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 89,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.52,
                        'title' => '',
                        'uri' => '0000090.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 90,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000091.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 91,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000092.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 92,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => '0000093.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 93,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => '0000094.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 94,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000095.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 95,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000096.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 96,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '0000097.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 97,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => '0000098.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 98,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000099.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 99,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000000.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 100,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'keyword' => 50,
                        'marker' => 80
                    ],
                    'totalWeight' => 130
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000001.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 101,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000002.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 102,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000003.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 103,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000004.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 104,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429940000005.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 105,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429950000006.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 106,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429950000007.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 107,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '/video/adjump/time/17766952429950000008.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 108,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ]
                    ],
                    'confidence' => 50,
                    'categories' => [
                        'keyword' => 50
                    ],
                    'totalWeight' => 50
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000100.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 109,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ]
                    ],
                    'confidence' => 80,
                    'categories' => [
                        'marker' => 80
                    ],
                    'totalWeight' => 80
                ],
                [
                    'segment' => [
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => '0000101.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 110,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000102.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 111,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '0000103.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 112,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => '0000104.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 113,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => '0000105.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 114,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000106.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 115,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '0000107.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 116,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.6,
                        'title' => '',
                        'uri' => '0000108.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 117,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000109.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 118,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.12,
                        'title' => '',
                        'uri' => '0000110.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 119,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '0000111.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 120,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000112.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 121,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000113.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 122,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => '0000114.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 123,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => '0000115.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 124,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '0000116.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 125,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000117.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 126,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '0000118.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 127,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000119.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 128,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.68,
                        'title' => '',
                        'uri' => '0000120.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 129,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000121.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 130,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => '0000122.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 131,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => '0000123.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 132,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000124.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 133,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000125.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 134,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000126.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 135,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.56,
                        'title' => '',
                        'uri' => '0000127.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 136,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000128.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 137,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => '0000129.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 138,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => '0000130.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 139,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000131.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 140,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.92,
                        'title' => '',
                        'uri' => '0000132.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 141,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000133.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 142,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '0000134.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 143,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000135.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 144,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000136.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 145,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => '0000137.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 146,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => '0000138.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 147,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '0000139.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 148,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => '0000140.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 149,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => '0000141.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 150,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => '0000142.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 151,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000143.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 152,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => '0000144.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 153,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => '0000145.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 154,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => '0000146.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 155,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.4,
                        'title' => '',
                        'uri' => '0000147.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 156,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => '0000148.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 157,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000149.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 158,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '0000150.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 159,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000151.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 160,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '0000152.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 161,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.04,
                        'title' => '',
                        'uri' => '0000153.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 162,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => '0000154.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 163,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000155.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 164,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '0000156.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 165,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.72,
                        'title' => '',
                        'uri' => '0000157.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 166,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000158.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 167,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000159.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 168,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000160.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 169,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000161.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 170,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000162.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 171,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => '0000163.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 172,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => '0000164.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 173,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000165.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 174,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000166.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 175,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '0000167.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 176,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000168.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 177,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000169.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 178,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000170.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 179,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000171.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 180,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => '0000172.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 181,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000173.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 182,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000174.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 183,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000175.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 184,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5,
                        'title' => '',
                        'uri' => '0000176.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 185,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000177.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 186,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => '0000178.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 187,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000179.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 188,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000180.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 189,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '0000181.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 190,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.04,
                        'title' => '',
                        'uri' => '0000182.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 191,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000183.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 192,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000184.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 193,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.08,
                        'title' => '',
                        'uri' => '0000185.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 194,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '0000186.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 195,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000187.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 196,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '0000188.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 197,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000189.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 198,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000190.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 199,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '0000191.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 200,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000192.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 201,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000193.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 202,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000194.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 203,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.92,
                        'title' => '',
                        'uri' => '0000195.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 204,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000196.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 205,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => '0000197.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 206,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.24,
                        'title' => '',
                        'uri' => '0000198.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 207,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 4.24,
                        'title' => '',
                        'uri' => '0000199.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 208,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000200.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 209,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000201.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 210,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000202.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 211,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000203.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 212,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000204.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 213,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000205.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 214,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '0000206.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 215,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => '0000207.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 216,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000208.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 217,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => '0000209.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 218,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000210.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 219,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.12,
                        'title' => '',
                        'uri' => '0000211.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 220,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000212.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 221,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => '0000213.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 222,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => '0000214.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 223,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000215.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 224,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000216.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 225,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => '0000217.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 226,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000218.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 227,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => '0000219.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 228,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.4,
                        'title' => '',
                        'uri' => '0000220.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 229,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000221.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 230,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => '0000222.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 231,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000223.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 232,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => '0000224.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 233,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000225.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 234,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '0000226.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 235,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => '0000227.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 236,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000228.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 237,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000229.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 238,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => '0000230.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 239,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '0000231.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 240,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000232.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 241,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.24,
                        'title' => '',
                        'uri' => '0000233.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 242,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000234.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 243,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000235.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 244,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => '0000236.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 245,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000237.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 246,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '0000238.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 247,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.68,
                        'title' => '',
                        'uri' => '0000239.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 248,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000240.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 249,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => '0000241.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 250,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000242.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 251,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000243.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 252,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => '0000244.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 253,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => '0000245.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 254,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => '0000246.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 255,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000247.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 256,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '0000248.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 257,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '0000249.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 258,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000250.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 259,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.96,
                        'title' => '',
                        'uri' => '0000251.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 260,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000252.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 261,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => '0000253.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 262,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000254.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 263,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.24,
                        'title' => '',
                        'uri' => '0000255.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 264,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => '0000256.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 265,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000257.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 266,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000258.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 267,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.24,
                        'title' => '',
                        'uri' => '0000259.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 268,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000260.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 269,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000261.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 270,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => '0000262.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 271,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000263.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 272,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.44,
                        'title' => '',
                        'uri' => '0000264.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 273,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000265.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 274,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.24,
                        'title' => '',
                        'uri' => '0000266.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 275,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000267.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 276,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000268.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 277,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.52,
                        'title' => '',
                        'uri' => '0000269.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 278,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.52,
                        'title' => '',
                        'uri' => '0000270.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 279,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000271.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 280,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000272.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 281,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000273.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 282,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000274.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 283,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '0000275.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 284,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000276.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 285,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.68,
                        'title' => '',
                        'uri' => '0000277.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 286,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000278.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 287,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000279.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 288,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000280.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 289,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000281.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 290,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000282.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 291,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000283.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 292,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000284.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 293,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000285.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 294,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '0000286.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 295,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.72,
                        'title' => '',
                        'uri' => '0000287.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 296,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => '0000288.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 297,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.56,
                        'title' => '',
                        'uri' => '0000289.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 298,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => '0000290.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 299,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => '0000291.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 300,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000292.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 301,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '0000293.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 302,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000294.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 303,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '0000295.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 304,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.68,
                        'title' => '',
                        'uri' => '0000296.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 305,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000297.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 306,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => '0000298.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 307,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000299.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 308,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => '0000300.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 309,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => '0000301.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 310,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000302.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 311,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => '0000303.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 312,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000304.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 313,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000305.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 314,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000306.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 315,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000307.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 316,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '0000308.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 317,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000309.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 318,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000310.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 319,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000311.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 320,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000312.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 321,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => '0000313.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 322,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.96,
                        'title' => '',
                        'uri' => '0000314.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 323,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000315.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 324,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000316.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 325,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000317.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 326,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000318.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 327,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => '0000319.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 328,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000320.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 329,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000321.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 330,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.36,
                        'title' => '',
                        'uri' => '0000322.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 331,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000323.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 332,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000324.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 333,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => '0000325.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 334,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000326.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 335,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000327.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 336,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000328.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 337,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000329.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 338,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000330.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 339,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000331.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 340,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '0000332.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 341,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000333.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 342,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => '0000334.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 343,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => '0000335.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 344,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.32,
                        'title' => '',
                        'uri' => '0000336.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 345,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000337.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 346,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => '0000338.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 347,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.16,
                        'title' => '',
                        'uri' => '0000339.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 348,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000340.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 349,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => '0000341.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 350,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => '0000342.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 351,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000343.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 352,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.44,
                        'title' => '',
                        'uri' => '0000344.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 353,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000345.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 354,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000346.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 355,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.96,
                        'title' => '',
                        'uri' => '0000347.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 356,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.68,
                        'title' => '',
                        'uri' => '0000348.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 357,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000349.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 358,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => '0000350.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 359,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000351.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 360,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '0000352.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 361,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000353.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 362,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.4,
                        'title' => '',
                        'uri' => '0000354.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 363,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => '0000355.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 364,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => '0000356.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 365,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000357.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 366,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '0000358.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 367,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '0000359.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 368,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '0000360.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 369,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000361.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 370,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000362.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 371,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000363.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 372,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => '0000364.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 373,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000365.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 374,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => '0000366.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 375,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000367.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 376,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000368.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 377,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.88,
                        'title' => '',
                        'uri' => '0000369.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 378,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000370.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 379,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => '0000371.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 380,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000372.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 381,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000373.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 382,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000374.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 383,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => '0000375.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 384,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000376.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 385,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.56,
                        'title' => '',
                        'uri' => '0000377.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 386,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000378.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 387,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '0000379.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 388,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => '0000380.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 389,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.76,
                        'title' => '',
                        'uri' => '0000381.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 390,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000382.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 391,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.88,
                        'title' => '',
                        'uri' => '0000383.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 392,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000384.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 393,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => '0000385.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 394,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '0000386.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 395,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000387.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 396,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000388.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 397,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.56,
                        'title' => '',
                        'uri' => '0000389.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 398,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000390.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 399,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => '0000391.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 400,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.68,
                        'title' => '',
                        'uri' => '0000392.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 401,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000393.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 402,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000394.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 403,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000395.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 404,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '0000396.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 405,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000397.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 406,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000398.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 407,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000399.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 408,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => '0000400.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 409,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000401.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 410,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '0000402.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 411,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000403.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 412,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000404.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 413,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => '0000405.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 414,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => '0000406.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 415,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ]
                    ],
                    'confidence' => 30,
                    'categories' => [
                        'duration' => 30
                    ],
                    'totalWeight' => 30
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000407.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 416,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000408.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 417,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '0000409.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 418,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => '0000410.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 419,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000411.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 420,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000412.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 421,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => '0000413.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 422,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => '0000414.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 423,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000415.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 424,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000416.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 425,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => '0000417.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 426,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => '0000418.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 427,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => '0000419.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 428,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '0000420.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 429,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => '0000421.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 430,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '0000422.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 431,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => '0000423.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 432,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.16,
                        'title' => '',
                        'uri' => '0000424.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 433,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '0000425.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 434,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => '0000426.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 435,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000427.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 436,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '0000428.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 437,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '0000429.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 438,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000430.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 439,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => '0000431.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 440,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 70,
                    'categories' => [
                        'duration' => 30,
                        'position' => 40
                    ],
                    'totalWeight' => 70
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => '0000432.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 441,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => '0000433.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 442,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '0000434.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 443,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000435.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 444,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000436.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 445,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000437.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 446,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => '0000438.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 447,
                    'isAd' => false,
                    'matchedRules' => [
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 40,
                    'categories' => [
                        'position' => 40
                    ],
                    'totalWeight' => 40
                ],
                [
                    'segment' => [
                        'duration' => 0.92,
                        'title' => '',
                        'uri' => '0000439.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 448,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 70,
                    'categories' => [
                        'duration' => 30,
                        'position' => 40
                    ],
                    'totalWeight' => 70
                ]
            ],
            'totalCount' => 449,
            'adCount' => 13,
            'contentCount' => 436,
            'totalDuration' => 1345.88,
            'adDuration' => 33.48,
            'contentDuration' => 1312.4,
            'adPercentage' => 2.49,
            'discontinuityCount' => 2,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'sequenceJumps' => [
                [
                    'index' => 100,
                    'prevSeq' => 99,
                    'currentSeq' => 9223372036854775807,
                    'jump' => 9223372036854775708,
                    'prevUri' => '0000099.ts',
                    'currentUri' => '/video/adjump/time/17766952429940000000.ts'
                ],
                [
                    'index' => 109,
                    'prevSeq' => 9223372036854775807,
                    'currentSeq' => 100,
                    'jump' => -9223372036854775707,
                    'prevUri' => '/video/adjump/time/17766952429950000008.ts',
                    'currentUri' => '0000100.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 0.32,
                'max' => 5.84,
                'avg' => 2.9975055679287,
                'buckets' => [
                    '0.3' => 1,
                    '0.4' => 1,
                    '0.5' => 1,
                    '0.6' => 2,
                    '0.7' => 1,
                    '0.8' => 2,
                    '0.9' => 2,
                    1 => 1,
                    '1.1' => 2,
                    '1.2' => 3,
                    '1.3' => 5,
                    '1.4' => 5,
                    '1.5' => 3,
                    '1.6' => 7,
                    '1.7' => 6,
                    '1.8' => 4,
                    '1.9' => 2,
                    2 => 14,
                    '2.1' => 3,
                    '2.2' => 11,
                    '2.3' => 10,
                    '2.4' => 6,
                    '2.5' => 5,
                    '2.6' => 10,
                    '2.7' => 9,
                    '2.8' => 15,
                    '2.9' => 23,
                    3 => 183,
                    '3.1' => 1,
                    '3.2' => 8,
                    '3.3' => 5,
                    '3.4' => 12,
                    '3.5' => 7,
                    '3.6' => 11,
                    '3.7' => 5,
                    '3.8' => 8,
                    '3.9' => 4,
                    4 => 3,
                    '4.1' => 1,
                    '4.2' => 4,
                    '4.3' => 4,
                    '4.4' => 3,
                    '4.5' => 5,
                    '4.6' => 6,
                    '4.7' => 1,
                    '4.8' => 7,
                    '4.9' => 4,
                    5 => 4,
                    '5.2' => 5,
                    '5.5' => 3,
                    '5.8' => 1
                ]
            ],
            'adClusters' => [
                [
                    'start' => 9,
                    'end' => 9,
                    'count' => 1
                ],
                [
                    'start' => 100,
                    'end' => 109,
                    'count' => 10
                ],
                [
                    'start' => 440,
                    'end' => 440,
                    'count' => 1
                ],
                [
                    'start' => 448,
                    'end' => 448,
                    'count' => 1
                ]
            ],
            'insertionPoints' => [
                'pre_roll' => [
                    'found' => false,
                    'start_index' => -1,
                    'end_index' => -1,
                    'duration' => 0,
                    'segment_count' => 0
                ],
                'mid_roll' => [
                    'found' => true,
                    'count' => 1,
                    'points' => [
                        [
                            'start_index' => 100,
                            'end_index' => 109,
                            'duration' => 29,
                            'segment_count' => 10,
                            'position_ratio' => 0.223
                        ]
                    ]
                ],
                'post_roll' => [
                    'found' => false,
                    'start_index' => -1,
                    'end_index' => -1,
                    'duration' => 0,
                    'segment_count' => 0
                ]
            ],
            'adTypes' => [
                'pre_roll_ad' => [
                    'count' => 1,
                    'duration' => 1.76
                ],
                'mid_roll_ad' => [
                    'count' => 1,
                    'duration' => 29
                ],
                'post_roll_ad' => [
                    'count' => 2,
                    'duration' => 2.72
                ],
                'marker_based_ad' => [
                    'count' => 1,
                    'duration' => 29
                ],
                'pattern_based_ad' => [
                    'count' => 0,
                    'duration' => 0
                ],
                'duration_based_ad' => [
                    'count' => 3,
                    'duration' => 4.48
                ]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => '频繁插播',
                'ad_density' => 2.9,
                'attention_grab_score' => 34,
                'frequency_score' => 61,
                'user_experience_impact' => '严重',
                'watchability_score' => 30
            ],
            'confidence' => 76
        ]
    ],
    'last_learn_date' => '2026-07-02 14:51:50'
];
