<?php
/**
 * v.lzcdn23.com 域名广告和插播规则
 * 自动生成于: 2026-07-02 14:53:22
 */

return [
    'domain' => 'v.lzcdn23.com',
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
            'enabled' => true,
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
    'confidence_score' => 78,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 39,
            'end_index' => 50,
            'duration' => 47.68,
            'segment_count' => 12
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 29,
            'points' => [
                [
                    'start_index' => 61,
                    'end_index' => 63,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.176
                ],
                [
                    'start_index' => 68,
                    'end_index' => 69,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.197
                ],
                [
                    'start_index' => 71,
                    'end_index' => 72,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.205
                ],
                [
                    'start_index' => 74,
                    'end_index' => 79,
                    'duration' => 24,
                    'segment_count' => 6,
                    'position_ratio' => 0.214
                ],
                [
                    'start_index' => 81,
                    'end_index' => 82,
                    'duration' => 5.56,
                    'segment_count' => 2,
                    'position_ratio' => 0.234
                ],
                [
                    'start_index' => 89,
                    'end_index' => 91,
                    'duration' => 9.96,
                    'segment_count' => 3,
                    'position_ratio' => 0.257
                ],
                [
                    'start_index' => 95,
                    'end_index' => 96,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.275
                ],
                [
                    'start_index' => 99,
                    'end_index' => 105,
                    'duration' => 28,
                    'segment_count' => 7,
                    'position_ratio' => 0.286
                ],
                [
                    'start_index' => 107,
                    'end_index' => 110,
                    'duration' => 14.24,
                    'segment_count' => 4,
                    'position_ratio' => 0.309
                ],
                [
                    'start_index' => 125,
                    'end_index' => 126,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.361
                ],
                [
                    'start_index' => 133,
                    'end_index' => 135,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.384
                ],
                [
                    'start_index' => 148,
                    'end_index' => 151,
                    'duration' => 16,
                    'segment_count' => 4,
                    'position_ratio' => 0.428
                ],
                [
                    'start_index' => 155,
                    'end_index' => 157,
                    'duration' => 11.96,
                    'segment_count' => 3,
                    'position_ratio' => 0.448
                ],
                [
                    'start_index' => 159,
                    'end_index' => 161,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.46
                ],
                [
                    'start_index' => 168,
                    'end_index' => 171,
                    'duration' => 12.88,
                    'segment_count' => 4,
                    'position_ratio' => 0.486
                ],
                [
                    'start_index' => 173,
                    'end_index' => 174,
                    'duration' => 7.96,
                    'segment_count' => 2,
                    'position_ratio' => 0.5
                ],
                [
                    'start_index' => 177,
                    'end_index' => 178,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.512
                ],
                [
                    'start_index' => 185,
                    'end_index' => 190,
                    'duration' => 24,
                    'segment_count' => 6,
                    'position_ratio' => 0.535
                ],
                [
                    'start_index' => 198,
                    'end_index' => 204,
                    'duration' => 27.8,
                    'segment_count' => 7,
                    'position_ratio' => 0.572
                ],
                [
                    'start_index' => 215,
                    'end_index' => 217,
                    'duration' => 11.96,
                    'segment_count' => 3,
                    'position_ratio' => 0.621
                ],
                [
                    'start_index' => 219,
                    'end_index' => 220,
                    'duration' => 8.88,
                    'segment_count' => 2,
                    'position_ratio' => 0.633
                ],
                [
                    'start_index' => 222,
                    'end_index' => 227,
                    'duration' => 23.8,
                    'segment_count' => 6,
                    'position_ratio' => 0.642
                ],
                [
                    'start_index' => 243,
                    'end_index' => 244,
                    'duration' => 8.12,
                    'segment_count' => 2,
                    'position_ratio' => 0.702
                ],
                [
                    'start_index' => 248,
                    'end_index' => 250,
                    'duration' => 10.64,
                    'segment_count' => 3,
                    'position_ratio' => 0.717
                ],
                [
                    'start_index' => 254,
                    'end_index' => 256,
                    'duration' => 12.4,
                    'segment_count' => 3,
                    'position_ratio' => 0.734
                ],
                [
                    'start_index' => 270,
                    'end_index' => 271,
                    'duration' => 5.64,
                    'segment_count' => 2,
                    'position_ratio' => 0.78
                ],
                [
                    'start_index' => 274,
                    'end_index' => 278,
                    'duration' => 20,
                    'segment_count' => 5,
                    'position_ratio' => 0.792
                ],
                [
                    'start_index' => 280,
                    'end_index' => 281,
                    'duration' => 7.56,
                    'segment_count' => 2,
                    'position_ratio' => 0.809
                ],
                [
                    'start_index' => 289,
                    'end_index' => 292,
                    'duration' => 15.88,
                    'segment_count' => 4,
                    'position_ratio' => 0.835
                ]
            ]
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 332,
            'end_index' => 336,
            'duration' => 20,
            'segment_count' => 5
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 11,
            'duration' => 146.44
        ],
        'mid_roll_ad' => [
            'count' => 64,
            'duration' => 525.8
        ],
        'post_roll_ad' => [
            'count' => 17,
            'duration' => 111.64
        ],
        'marker_based_ad' => [
            'count' => 35,
            'duration' => 415.4
        ],
        'pattern_based_ad' => [
            'count' => 88,
            'duration' => 765.48
        ],
        'duration_based_ad' => [
            'count' => 5,
            'duration' => 35
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 57.51,
        'attention_grab_score' => 50,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 30
    ],
    'marker_stats' => [
        'discontinuity_count' => 36,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-02 14:53:22',
    'analysis_stats' => [
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
        'confidence' => 78
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 7.16,
                        'title' => '',
                        'uri' => 'ba353d70bd4000000.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 0,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'cluster' => 70,
                        'position' => 40
                    ],
                    'totalWeight' => 190
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000001.ts',
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
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000002.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 2,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 95,
                    'categories' => [
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 95
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'ba353d70bd4000003.ts',
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
                        'duration' => 5.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000004.ts',
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
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000005.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000006.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 6,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 95,
                    'categories' => [
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 95
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000007.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 7,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 95,
                    'categories' => [
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 95
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000008.ts',
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000009.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 9,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 175
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000010.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 10,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000011.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 11,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000012.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 12,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000013.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 13,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000014.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 14,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000015.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 15,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.76,
                        'title' => '',
                        'uri' => 'ba353d70bd4000016.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000017.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 17,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000018.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 18,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000019.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 19,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 150
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000020.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 20,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000021.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 21,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000022.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 22,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000023.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 23,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000024.ts',
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
                        'duration' => 5.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000025.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 25,
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
                        'uri' => 'ba353d70bd4000026.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000027.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 27,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000028.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 28,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000029.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 29,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000030.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 30,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000031.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000032.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 32,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.28,
                        'title' => '',
                        'uri' => 'ba353d70bd4000033.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000034.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 34,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000035.ts',
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
                        'duration' => 1.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000036.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 36,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000037.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 37,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => 'ba353d70bd4000038.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000039.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 39,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000040.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 40,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000041.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 41,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000042.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 42,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000043.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 43,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000044.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 44,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000045.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 45,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000046.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 46,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000047.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 47,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000048.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 48,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000049.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 49,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000050.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 50,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000051.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000052.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 52,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000053.ts',
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
                        'duration' => 1.24,
                        'title' => '',
                        'uri' => 'ba353d70bd4000054.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 54,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000055.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 55,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 7.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000056.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000057.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 57,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000058.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 58,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000059.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 59,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'cluster' => 70
                    ],
                    'totalWeight' => 205
                ],
                [
                    'segment' => [
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000060.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000061.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 61,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000062.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 62,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000063.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 63,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.16,
                        'title' => '',
                        'uri' => 'ba353d70bd4000064.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 64,
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
                        'duration' => 6.28,
                        'title' => '',
                        'uri' => 'ba353d70bd4000065.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000066.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 66,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000067.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000068.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 68,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000069.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 69,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000070.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000071.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 71,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000072.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 72,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000073.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 73,
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
                        'uri' => 'ba353d70bd40074339.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 74,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd40074340.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 75,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd40074341.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 76,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd40074342.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 77,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd40074343.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 78,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd40074344.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 79,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => 'ba353d70bd40074345.ts',
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
                        'duration' => 1.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000074.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 81,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
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
                        'duration' => 30,
                        'marker' => 80
                    ],
                    'totalWeight' => 110
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000075.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 82,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000076.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000077.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 84,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000078.ts',
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
                        'duration' => 6.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000079.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000080.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 87,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000081.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000082.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 89,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000083.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 90,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'duration' => 30,
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 180
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000084.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 91,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000085.ts',
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
                        'duration' => 5.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000086.ts',
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
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000087.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000088.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 95,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000089.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 96,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000090.ts',
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000091.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000092.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 99,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000093.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 100,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000094.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 101,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000095.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 102,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000096.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 103,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000097.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 104,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000098.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 105,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000099.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 106,
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
                        'uri' => 'ba353d70bd4000100.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 107,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000101.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 108,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000102.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 109,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'ba353d70bd4000103.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 110,
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
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000104.ts',
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
                        'duration' => 1.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000105.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 112,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000106.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 113,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000107.ts',
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
                        'duration' => 0.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000108.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 115,
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
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000109.ts',
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
                        'duration' => 6.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000110.ts',
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
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000111.ts',
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
                        'duration' => 0.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000112.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000113.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 120,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'cluster' => 70
                    ],
                    'totalWeight' => 205
                ],
                [
                    'segment' => [
                        'duration' => 5.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000114.ts',
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
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000115.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000116.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 123,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'ba353d70bd4000117.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000118.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 125,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000119.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 126,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000120.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000121.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 128,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000122.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000123.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 130,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'cluster' => 70
                    ],
                    'totalWeight' => 205
                ],
                [
                    'segment' => [
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000124.ts',
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
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000125.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000126.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 133,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000127.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 134,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000128.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 135,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000129.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 136,
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
                        'uri' => 'ba353d70bd4000130.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 137,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'ba353d70bd4000131.ts',
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
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000132.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000133.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 140,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000134.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 141,
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
                        'uri' => 'ba353d70bd4000135.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000136.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 143,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000137.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000138.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000139.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 146,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000140.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000141.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 148,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000142.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 149,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000143.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 150,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000144.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 151,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000145.ts',
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
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000146.ts',
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
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'ba353d70bd4000147.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000148.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 155,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000149.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 156,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000150.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 157,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000151.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000152.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 159,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000153.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 160,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000154.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 161,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000155.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000156.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 163,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000157.ts',
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
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000158.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000159.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 166,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000160.ts',
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
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000161.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 168,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000162.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 169,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000163.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 170,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'duration' => 30,
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 180
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000164.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 171,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000165.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000166.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 173,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000167.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 174,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000168.ts',
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
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000169.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000170.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 177,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000171.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 178,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000172.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000173.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 180,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 6.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000174.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 181,
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
                        'uri' => 'ba353d70bd4000175.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 182,
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
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000176.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000177.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000178.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 185,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000179.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 186,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000180.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 187,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000181.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 188,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000182.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 189,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000183.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 190,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000184.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 191,
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
                        'uri' => 'ba353d70bd4000185.ts',
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
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000186.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000187.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 194,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000188.ts',
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
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000189.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 196,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.24,
                        'title' => '',
                        'uri' => 'ba353d70bd4000190.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 197,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000191.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 198,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000192.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 199,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000193.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 200,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000194.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 201,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000195.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 202,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000196.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 203,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000197.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 204,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000198.ts',
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
                        'duration' => 5.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000199.ts',
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
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000200.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 207,
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
                        'uri' => 'ba353d70bd4000201.ts',
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
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000202.ts',
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
                        'duration' => 7.16,
                        'title' => '',
                        'uri' => 'ba353d70bd4000203.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 210,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 150
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000204.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000205.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 212,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000206.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 213,
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
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000207.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000208.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 215,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000209.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 216,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000210.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 217,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000211.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 218,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000212.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 219,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000213.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 220,
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
                        'duration' => 3,
                        'title' => '',
                        'uri' => 'ba353d70bd4000214.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000215.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 222,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000216.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 223,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000217.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 224,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000218.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 225,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000219.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 226,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000220.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 227,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000221.ts',
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
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000222.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 229,
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
                        'uri' => 'ba353d70bd4000223.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 230,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000224.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000225.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 232,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000226.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000227.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 234,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000228.ts',
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
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000229.ts',
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
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000230.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 237,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000231.ts',
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000232.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000233.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 240,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000234.ts',
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
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000235.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 242,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000236.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 243,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000237.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 244,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'ba353d70bd4000238.ts',
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
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000239.ts',
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000240.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000241.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 248,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000242.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 249,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000243.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 250,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 150
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000244.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000245.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 252,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000246.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 253,
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
                        'uri' => 'ba353d70bd4000247.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 254,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000248.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 255,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000249.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 256,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'ba353d70bd4000250.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000251.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 258,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.16,
                        'title' => '',
                        'uri' => 'ba353d70bd4000252.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 259,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000253.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 260,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'cluster' => 70
                    ],
                    'totalWeight' => 205
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000254.ts',
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
                        'duration' => 5.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000255.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000256.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 263,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000257.ts',
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
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000258.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000259.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 266,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000260.ts',
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
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000261.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 268,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => 'ba353d70bd4000262.ts',
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
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000263.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 270,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告',
                            'weight' => 30,
                            'category' => 'duration'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'duration' => 30,
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 180
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000264.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 271,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000265.ts',
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
                        'duration' => 6.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000266.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 273,
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
                        'uri' => 'ba353d70bd4000267.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 274,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000268.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 275,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000269.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 276,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000270.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 277,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000271.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 278,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000272.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 279,
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
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'ba353d70bd4000273.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 280,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'cluster' => 70
                    ],
                    'totalWeight' => 205
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000274.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 281,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000275.ts',
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
                        'duration' => 5.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000276.ts',
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
                        'duration' => 0.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000277.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 284,
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
                        'duration' => 7.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000278.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000279.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 286,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000280.ts',
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
                        'duration' => 6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000281.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000282.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 289,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000283.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 290,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000284.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 291,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000285.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 292,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 1.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000286.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 293,
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
                        'uri' => 'ba353d70bd4000287.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 294,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000288.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 295,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000289.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 296,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000290.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 297,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000291.ts',
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
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000292.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 299,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000293.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 300,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 5.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000294.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000295.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000296.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 303,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000297.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 304,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 7.24,
                        'title' => '',
                        'uri' => 'ba353d70bd4000298.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 305,
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
                        'uri' => 'ba353d70bd4000299.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 306,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000300.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 307,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000301.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 308,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000302.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000303.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 310,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000304.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 311,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000305.ts',
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
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000306.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 313,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'ba353d70bd4000307.ts',
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
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'ba353d70bd4000308.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 315,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'ba353d70bd4000309.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000310.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 317,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => 'ba353d70bd4000311.ts',
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
                        'duration' => 6.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000312.ts',
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
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'ba353d70bd4000313.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 320,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'ad-cluster-boundary',
                            'description' => '位于广告簇边界（DISCONTINUITY + 时长突变）',
                            'weight' => 70,
                            'category' => 'cluster'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 150
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'ba353d70bd4000314.ts',
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
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000315.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000316.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 323,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000317.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => 'ba353d70bd4000318.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000319.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 326,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'ba353d70bd4000320.ts',
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
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000321.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 328,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000322.ts',
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
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'ba353d70bd4000323.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 330,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55
                    ],
                    'totalWeight' => 135
                ],
                [
                    'segment' => [
                        'duration' => 5.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000324.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 331,
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
                        'uri' => 'ba353d70bd4000325.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 332,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000326.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 333,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000327.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 334,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000328.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 335,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 55,
                    'categories' => [
                        'pattern' => 55
                    ],
                    'totalWeight' => 55
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000329.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 336,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 95,
                    'categories' => [
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 95
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'ba353d70bd4000330.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 337,
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
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'ba353d70bd4000331.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 338,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 95,
                    'categories' => [
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 95
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'ba353d70bd4000332.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 339,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'ba353d70bd4000333.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 340,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的短片段，可能是广告',
                            'weight' => 55,
                            'category' => 'pattern'
                        ],
                        [
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
                            'weight' => 40,
                            'category' => 'position'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'marker' => 80,
                        'pattern' => 55,
                        'position' => 40
                    ],
                    'totalWeight' => 175
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => 'ba353d70bd4000334.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 341,
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
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => 'ba353d70bd4000335.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 342,
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
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => 'ba353d70bd4000336.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 343,
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
                        'duration' => 6.2,
                        'title' => '',
                        'uri' => 'ba353d70bd4000337.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 344,
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
                        'duration' => 0.96,
                        'title' => '',
                        'uri' => 'ba353d70bd4000338.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 345,
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
            'totalCount' => 346,
            'adCount' => 199,
            'contentCount' => 147,
            'totalDuration' => 1382.6,
            'adDuration' => 783.88,
            'contentDuration' => 598.72,
            'adPercentage' => 56.7,
            'discontinuityCount' => 36,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'sequenceJumps' => [
                [
                    'index' => 74,
                    'prevSeq' => 4000073,
                    'currentSeq' => 40074339,
                    'jump' => 36074266,
                    'prevUri' => 'ba353d70bd4000073.ts',
                    'currentUri' => 'ba353d70bd40074339.ts'
                ],
                [
                    'index' => 81,
                    'prevSeq' => 40074345,
                    'currentSeq' => 4000074,
                    'jump' => -36074271,
                    'prevUri' => 'ba353d70bd40074345.ts',
                    'currentUri' => 'ba353d70bd4000074.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 0.44,
                'max' => 7.72,
                'avg' => 3.9959537572254,
                'buckets' => [
                    '0.4' => 1,
                    '0.8' => 1,
                    '0.9' => 2,
                    1 => 2,
                    '1.1' => 2,
                    '1.2' => 4,
                    '1.3' => 1,
                    '1.4' => 3,
                    '1.5' => 1,
                    '1.6' => 2,
                    '1.8' => 2,
                    '1.9' => 2,
                    2 => 2,
                    '2.2' => 4,
                    '2.3' => 2,
                    '2.4' => 2,
                    '2.5' => 2,
                    '2.6' => 5,
                    '2.7' => 5,
                    '2.8' => 4,
                    '2.9' => 6,
                    3 => 5,
                    '3.1' => 3,
                    '3.2' => 2,
                    '3.3' => 8,
                    '3.4' => 9,
                    '3.5' => 8,
                    '3.6' => 4,
                    '3.8' => 4,
                    '3.9' => 6,
                    4 => 154,
                    '4.1' => 1,
                    '4.2' => 2,
                    '4.4' => 6,
                    '4.5' => 4,
                    '4.6' => 7,
                    '4.7' => 2,
                    '4.8' => 8,
                    '4.9' => 3,
                    5 => 5,
                    '5.1' => 3,
                    '5.2' => 6,
                    '5.3' => 5,
                    '5.4' => 1,
                    '5.5' => 2,
                    '5.6' => 3,
                    '5.7' => 2,
                    '5.8' => 2,
                    6 => 2,
                    '6.1' => 1,
                    '6.2' => 2,
                    '6.3' => 1,
                    '6.4' => 4,
                    '6.5' => 2,
                    '6.6' => 3,
                    '6.7' => 1,
                    '6.8' => 4,
                    '6.9' => 1,
                    '7.1' => 2,
                    '7.2' => 1,
                    '7.5' => 1,
                    '7.7' => 1
                ]
            ],
            'adClusters' => [
                [
                    'start' => 0,
                    'end' => 0,
                    'count' => 1
                ],
                [
                    'start' => 2,
                    'end' => 2,
                    'count' => 1
                ],
                [
                    'start' => 6,
                    'end' => 7,
                    'count' => 2
                ],
                [
                    'start' => 9,
                    'end' => 15,
                    'count' => 7
                ],
                [
                    'start' => 17,
                    'end' => 21,
                    'count' => 5
                ],
                [
                    'start' => 23,
                    'end' => 23,
                    'count' => 1
                ],
                [
                    'start' => 27,
                    'end' => 30,
                    'count' => 4
                ],
                [
                    'start' => 32,
                    'end' => 32,
                    'count' => 1
                ],
                [
                    'start' => 34,
                    'end' => 34,
                    'count' => 1
                ],
                [
                    'start' => 37,
                    'end' => 37,
                    'count' => 1
                ],
                [
                    'start' => 39,
                    'end' => 50,
                    'count' => 12
                ],
                [
                    'start' => 52,
                    'end' => 52,
                    'count' => 1
                ],
                [
                    'start' => 55,
                    'end' => 55,
                    'count' => 1
                ],
                [
                    'start' => 57,
                    'end' => 57,
                    'count' => 1
                ],
                [
                    'start' => 59,
                    'end' => 59,
                    'count' => 1
                ],
                [
                    'start' => 61,
                    'end' => 63,
                    'count' => 3
                ],
                [
                    'start' => 66,
                    'end' => 66,
                    'count' => 1
                ],
                [
                    'start' => 68,
                    'end' => 69,
                    'count' => 2
                ],
                [
                    'start' => 71,
                    'end' => 72,
                    'count' => 2
                ],
                [
                    'start' => 74,
                    'end' => 79,
                    'count' => 6
                ],
                [
                    'start' => 81,
                    'end' => 82,
                    'count' => 2
                ],
                [
                    'start' => 84,
                    'end' => 84,
                    'count' => 1
                ],
                [
                    'start' => 87,
                    'end' => 87,
                    'count' => 1
                ],
                [
                    'start' => 89,
                    'end' => 91,
                    'count' => 3
                ],
                [
                    'start' => 95,
                    'end' => 96,
                    'count' => 2
                ],
                [
                    'start' => 99,
                    'end' => 105,
                    'count' => 7
                ],
                [
                    'start' => 107,
                    'end' => 110,
                    'count' => 4
                ],
                [
                    'start' => 113,
                    'end' => 113,
                    'count' => 1
                ],
                [
                    'start' => 120,
                    'end' => 120,
                    'count' => 1
                ],
                [
                    'start' => 123,
                    'end' => 123,
                    'count' => 1
                ],
                [
                    'start' => 125,
                    'end' => 126,
                    'count' => 2
                ],
                [
                    'start' => 128,
                    'end' => 128,
                    'count' => 1
                ],
                [
                    'start' => 130,
                    'end' => 130,
                    'count' => 1
                ],
                [
                    'start' => 133,
                    'end' => 135,
                    'count' => 3
                ],
                [
                    'start' => 137,
                    'end' => 137,
                    'count' => 1
                ],
                [
                    'start' => 140,
                    'end' => 140,
                    'count' => 1
                ],
                [
                    'start' => 143,
                    'end' => 143,
                    'count' => 1
                ],
                [
                    'start' => 146,
                    'end' => 146,
                    'count' => 1
                ],
                [
                    'start' => 148,
                    'end' => 151,
                    'count' => 4
                ],
                [
                    'start' => 155,
                    'end' => 157,
                    'count' => 3
                ],
                [
                    'start' => 159,
                    'end' => 161,
                    'count' => 3
                ],
                [
                    'start' => 163,
                    'end' => 163,
                    'count' => 1
                ],
                [
                    'start' => 166,
                    'end' => 166,
                    'count' => 1
                ],
                [
                    'start' => 168,
                    'end' => 171,
                    'count' => 4
                ],
                [
                    'start' => 173,
                    'end' => 174,
                    'count' => 2
                ],
                [
                    'start' => 177,
                    'end' => 178,
                    'count' => 2
                ],
                [
                    'start' => 180,
                    'end' => 180,
                    'count' => 1
                ],
                [
                    'start' => 185,
                    'end' => 190,
                    'count' => 6
                ],
                [
                    'start' => 194,
                    'end' => 194,
                    'count' => 1
                ],
                [
                    'start' => 196,
                    'end' => 196,
                    'count' => 1
                ],
                [
                    'start' => 198,
                    'end' => 204,
                    'count' => 7
                ],
                [
                    'start' => 210,
                    'end' => 210,
                    'count' => 1
                ],
                [
                    'start' => 212,
                    'end' => 212,
                    'count' => 1
                ],
                [
                    'start' => 215,
                    'end' => 217,
                    'count' => 3
                ],
                [
                    'start' => 219,
                    'end' => 220,
                    'count' => 2
                ],
                [
                    'start' => 222,
                    'end' => 227,
                    'count' => 6
                ],
                [
                    'start' => 230,
                    'end' => 230,
                    'count' => 1
                ],
                [
                    'start' => 232,
                    'end' => 232,
                    'count' => 1
                ],
                [
                    'start' => 234,
                    'end' => 234,
                    'count' => 1
                ],
                [
                    'start' => 237,
                    'end' => 237,
                    'count' => 1
                ],
                [
                    'start' => 240,
                    'end' => 240,
                    'count' => 1
                ],
                [
                    'start' => 243,
                    'end' => 244,
                    'count' => 2
                ],
                [
                    'start' => 248,
                    'end' => 250,
                    'count' => 3
                ],
                [
                    'start' => 252,
                    'end' => 252,
                    'count' => 1
                ],
                [
                    'start' => 254,
                    'end' => 256,
                    'count' => 3
                ],
                [
                    'start' => 258,
                    'end' => 258,
                    'count' => 1
                ],
                [
                    'start' => 260,
                    'end' => 260,
                    'count' => 1
                ],
                [
                    'start' => 263,
                    'end' => 263,
                    'count' => 1
                ],
                [
                    'start' => 266,
                    'end' => 266,
                    'count' => 1
                ],
                [
                    'start' => 268,
                    'end' => 268,
                    'count' => 1
                ],
                [
                    'start' => 270,
                    'end' => 271,
                    'count' => 2
                ],
                [
                    'start' => 274,
                    'end' => 278,
                    'count' => 5
                ],
                [
                    'start' => 280,
                    'end' => 281,
                    'count' => 2
                ],
                [
                    'start' => 286,
                    'end' => 286,
                    'count' => 1
                ],
                [
                    'start' => 289,
                    'end' => 292,
                    'count' => 4
                ],
                [
                    'start' => 294,
                    'end' => 297,
                    'count' => 4
                ],
                [
                    'start' => 299,
                    'end' => 300,
                    'count' => 2
                ],
                [
                    'start' => 303,
                    'end' => 304,
                    'count' => 2
                ],
                [
                    'start' => 306,
                    'end' => 308,
                    'count' => 3
                ],
                [
                    'start' => 310,
                    'end' => 311,
                    'count' => 2
                ],
                [
                    'start' => 313,
                    'end' => 313,
                    'count' => 1
                ],
                [
                    'start' => 315,
                    'end' => 315,
                    'count' => 1
                ],
                [
                    'start' => 317,
                    'end' => 317,
                    'count' => 1
                ],
                [
                    'start' => 320,
                    'end' => 320,
                    'count' => 1
                ],
                [
                    'start' => 323,
                    'end' => 323,
                    'count' => 1
                ],
                [
                    'start' => 326,
                    'end' => 326,
                    'count' => 1
                ],
                [
                    'start' => 328,
                    'end' => 328,
                    'count' => 1
                ],
                [
                    'start' => 330,
                    'end' => 330,
                    'count' => 1
                ],
                [
                    'start' => 332,
                    'end' => 336,
                    'count' => 5
                ],
                [
                    'start' => 338,
                    'end' => 338,
                    'count' => 1
                ],
                [
                    'start' => 340,
                    'end' => 340,
                    'count' => 1
                ],
                [
                    'start' => 345,
                    'end' => 345,
                    'count' => 1
                ]
            ],
            'insertionPoints' => [
                'pre_roll' => [
                    'found' => true,
                    'start_index' => 39,
                    'end_index' => 50,
                    'duration' => 47.68,
                    'segment_count' => 12
                ],
                'mid_roll' => [
                    'found' => true,
                    'count' => 29,
                    'points' => [
                        [
                            'start_index' => 61,
                            'end_index' => 63,
                            'duration' => 12,
                            'segment_count' => 3,
                            'position_ratio' => 0.176
                        ],
                        [
                            'start_index' => 68,
                            'end_index' => 69,
                            'duration' => 8,
                            'segment_count' => 2,
                            'position_ratio' => 0.197
                        ],
                        [
                            'start_index' => 71,
                            'end_index' => 72,
                            'duration' => 8,
                            'segment_count' => 2,
                            'position_ratio' => 0.205
                        ],
                        [
                            'start_index' => 74,
                            'end_index' => 79,
                            'duration' => 24,
                            'segment_count' => 6,
                            'position_ratio' => 0.214
                        ],
                        [
                            'start_index' => 81,
                            'end_index' => 82,
                            'duration' => 5.56,
                            'segment_count' => 2,
                            'position_ratio' => 0.234
                        ],
                        [
                            'start_index' => 89,
                            'end_index' => 91,
                            'duration' => 9.96,
                            'segment_count' => 3,
                            'position_ratio' => 0.257
                        ],
                        [
                            'start_index' => 95,
                            'end_index' => 96,
                            'duration' => 8,
                            'segment_count' => 2,
                            'position_ratio' => 0.275
                        ],
                        [
                            'start_index' => 99,
                            'end_index' => 105,
                            'duration' => 28,
                            'segment_count' => 7,
                            'position_ratio' => 0.286
                        ],
                        [
                            'start_index' => 107,
                            'end_index' => 110,
                            'duration' => 14.24,
                            'segment_count' => 4,
                            'position_ratio' => 0.309
                        ],
                        [
                            'start_index' => 125,
                            'end_index' => 126,
                            'duration' => 8,
                            'segment_count' => 2,
                            'position_ratio' => 0.361
                        ],
                        [
                            'start_index' => 133,
                            'end_index' => 135,
                            'duration' => 12,
                            'segment_count' => 3,
                            'position_ratio' => 0.384
                        ],
                        [
                            'start_index' => 148,
                            'end_index' => 151,
                            'duration' => 16,
                            'segment_count' => 4,
                            'position_ratio' => 0.428
                        ],
                        [
                            'start_index' => 155,
                            'end_index' => 157,
                            'duration' => 11.96,
                            'segment_count' => 3,
                            'position_ratio' => 0.448
                        ],
                        [
                            'start_index' => 159,
                            'end_index' => 161,
                            'duration' => 12,
                            'segment_count' => 3,
                            'position_ratio' => 0.46
                        ],
                        [
                            'start_index' => 168,
                            'end_index' => 171,
                            'duration' => 12.88,
                            'segment_count' => 4,
                            'position_ratio' => 0.486
                        ],
                        [
                            'start_index' => 173,
                            'end_index' => 174,
                            'duration' => 7.96,
                            'segment_count' => 2,
                            'position_ratio' => 0.5
                        ],
                        [
                            'start_index' => 177,
                            'end_index' => 178,
                            'duration' => 8,
                            'segment_count' => 2,
                            'position_ratio' => 0.512
                        ],
                        [
                            'start_index' => 185,
                            'end_index' => 190,
                            'duration' => 24,
                            'segment_count' => 6,
                            'position_ratio' => 0.535
                        ],
                        [
                            'start_index' => 198,
                            'end_index' => 204,
                            'duration' => 27.8,
                            'segment_count' => 7,
                            'position_ratio' => 0.572
                        ],
                        [
                            'start_index' => 215,
                            'end_index' => 217,
                            'duration' => 11.96,
                            'segment_count' => 3,
                            'position_ratio' => 0.621
                        ],
                        [
                            'start_index' => 219,
                            'end_index' => 220,
                            'duration' => 8.88,
                            'segment_count' => 2,
                            'position_ratio' => 0.633
                        ],
                        [
                            'start_index' => 222,
                            'end_index' => 227,
                            'duration' => 23.8,
                            'segment_count' => 6,
                            'position_ratio' => 0.642
                        ],
                        [
                            'start_index' => 243,
                            'end_index' => 244,
                            'duration' => 8.12,
                            'segment_count' => 2,
                            'position_ratio' => 0.702
                        ],
                        [
                            'start_index' => 248,
                            'end_index' => 250,
                            'duration' => 10.64,
                            'segment_count' => 3,
                            'position_ratio' => 0.717
                        ],
                        [
                            'start_index' => 254,
                            'end_index' => 256,
                            'duration' => 12.4,
                            'segment_count' => 3,
                            'position_ratio' => 0.734
                        ],
                        [
                            'start_index' => 270,
                            'end_index' => 271,
                            'duration' => 5.64,
                            'segment_count' => 2,
                            'position_ratio' => 0.78
                        ],
                        [
                            'start_index' => 274,
                            'end_index' => 278,
                            'duration' => 20,
                            'segment_count' => 5,
                            'position_ratio' => 0.792
                        ],
                        [
                            'start_index' => 280,
                            'end_index' => 281,
                            'duration' => 7.56,
                            'segment_count' => 2,
                            'position_ratio' => 0.809
                        ],
                        [
                            'start_index' => 289,
                            'end_index' => 292,
                            'duration' => 15.88,
                            'segment_count' => 4,
                            'position_ratio' => 0.835
                        ]
                    ]
                ],
                'post_roll' => [
                    'found' => true,
                    'start_index' => 332,
                    'end_index' => 336,
                    'duration' => 20,
                    'segment_count' => 5
                ]
            ],
            'adTypes' => [
                'pre_roll_ad' => [
                    'count' => 11,
                    'duration' => 146.44
                ],
                'mid_roll_ad' => [
                    'count' => 64,
                    'duration' => 525.8
                ],
                'post_roll_ad' => [
                    'count' => 17,
                    'duration' => 111.64
                ],
                'marker_based_ad' => [
                    'count' => 35,
                    'duration' => 415.4
                ],
                'pattern_based_ad' => [
                    'count' => 88,
                    'duration' => 765.48
                ],
                'duration_based_ad' => [
                    'count' => 5,
                    'duration' => 35
                ]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => '频繁插播',
                'ad_density' => 57.51,
                'attention_grab_score' => 50,
                'frequency_score' => 100,
                'user_experience_impact' => '严重',
                'watchability_score' => 30
            ],
            'confidence' => 78
        ]
    ],
    'last_learn_date' => '2026-07-02 14:53:22'
];
