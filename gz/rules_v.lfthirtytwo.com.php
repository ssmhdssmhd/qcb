<?php
/**
 * v.lfthirtytwo.com 域名广告和插播规则
 * 自动生成于: 2026-07-02 14:51:28
 */

return [
    'domain' => 'v.lfthirtytwo.com',
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
    'confidence_score' => 100,
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
                    'start_index' => 500,
                    'end_index' => 501,
                    'duration' => 11.2,
                    'segment_count' => 2,
                    'position_ratio' => 0.404
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
            'count' => 20,
            'duration' => 86.52
        ],
        'mid_roll_ad' => [
            'count' => 87,
            'duration' => 362.28
        ],
        'post_roll_ad' => [
            'count' => 19,
            'duration' => 73.84
        ],
        'marker_based_ad' => [
            'count' => 126,
            'duration' => 522.64
        ],
        'pattern_based_ad' => [
            'count' => 0,
            'duration' => 0
        ],
        'duration_based_ad' => [
            'count' => 2,
            'duration' => 3.64
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 10.25,
        'attention_grab_score' => 15,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 30
    ],
    'marker_stats' => [
        'discontinuity_count' => 127,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-02 14:51:28',
    'analysis_stats' => [
        'totalSegments' => 1239,
        'adSegments' => 127,
        'contentSegments' => 1112,
        'totalDuration' => 4954.24,
        'adDuration' => 522.64,
        'contentDuration' => 4431.6,
        'adPercentage' => 10.55,
        'discontinuityCount' => 127,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 4,
        'adClusters' => 126,
        'confidence' => 100
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000000.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000001.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000002.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000003.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000004.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000005.ts',
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
                        'uri' => '7f2fcf3d543000006.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000007.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000008.ts',
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
                        'duration' => 7.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000009.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000010.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000011.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000012.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 12,
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
                        'uri' => '7f2fcf3d543000013.ts',
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
                        'uri' => '7f2fcf3d543000014.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000015.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000016.ts',
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
                        'uri' => '7f2fcf3d543000017.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000018.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000019.ts',
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
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000020.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000021.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 21,
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
                        'uri' => '7f2fcf3d543000022.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000023.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000024.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000025.ts',
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000026.ts',
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
                        'uri' => '7f2fcf3d543000027.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000028.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000029.ts',
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
                        'uri' => '7f2fcf3d543000030.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000031.ts',
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
                        'uri' => '7f2fcf3d543000032.ts',
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
                        'duration' => 5.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000033.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 33,
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
                        'uri' => '7f2fcf3d543000034.ts',
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
                        'duration' => 2.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000035.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000036.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000037.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000038.ts',
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
                        'uri' => '7f2fcf3d543000039.ts',
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
                        'uri' => '7f2fcf3d543000040.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000041.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000042.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000043.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000044.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 44,
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
                        'uri' => '7f2fcf3d543000045.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000046.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000047.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000048.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 48,
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
                        'uri' => '7f2fcf3d543000049.ts',
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
                        'uri' => '7f2fcf3d543000050.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000051.ts',
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
                        'duration' => 6.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000052.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000053.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000054.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000055.ts',
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
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000056.ts',
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
                        'uri' => '7f2fcf3d543000057.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000058.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000059.ts',
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
                        'uri' => '7f2fcf3d543000060.ts',
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
                        'uri' => '7f2fcf3d543000061.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 61,
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
                        'uri' => '7f2fcf3d543000062.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000063.ts',
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
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000064.ts',
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000065.ts',
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
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000066.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000067.ts',
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
                        'uri' => '7f2fcf3d543000068.ts',
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000069.ts',
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
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000070.ts',
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
                        'uri' => '7f2fcf3d543000071.ts',
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
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000072.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000073.ts',
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
                        'uri' => '7f2fcf3d5434341225.ts',
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
                        'uri' => '7f2fcf3d5434341226.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341227.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341228.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341229.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341230.ts',
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
                        'duration' => 2,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341231.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000074.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 81,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000075.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000076.ts',
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
                        'duration' => 1.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000077.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 84,
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
                        'uri' => '7f2fcf3d543000078.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000079.ts',
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
                        'duration' => 7,
                        'title' => '',
                        'uri' => '7f2fcf3d543000080.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000081.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000082.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000083.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 90,
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
                        'duration' => 1.52,
                        'title' => '',
                        'uri' => '7f2fcf3d543000084.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 91,
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
                        'uri' => '7f2fcf3d543000085.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000086.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000087.ts',
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
                        'uri' => '7f2fcf3d543000088.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000089.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000090.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000091.ts',
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
                        'uri' => '7f2fcf3d543000092.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000093.ts',
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
                        'uri' => '7f2fcf3d543000094.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 101,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000095.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 102,
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
                        'uri' => '7f2fcf3d543000096.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 103,
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
                        'uri' => '7f2fcf3d543000097.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 104,
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
                        'uri' => '7f2fcf3d543000098.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 105,
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
                        'uri' => '7f2fcf3d543000099.ts',
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
                        'uri' => '7f2fcf3d543000100.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 107,
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
                        'uri' => '7f2fcf3d543000101.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 108,
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
                        'uri' => '7f2fcf3d543000102.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 109,
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
                        'uri' => '7f2fcf3d543000103.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000104.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000105.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000106.ts',
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000107.ts',
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
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000108.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000109.ts',
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
                        'duration' => 7.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000110.ts',
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
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000111.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000112.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 119,
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
                        'uri' => '7f2fcf3d543000113.ts',
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
                        'uri' => '7f2fcf3d543000114.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000115.ts',
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
                        'uri' => '7f2fcf3d543000116.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000117.ts',
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
                        'uri' => '7f2fcf3d543000118.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000119.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000120.ts',
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
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000121.ts',
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
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000122.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 129,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000123.ts',
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
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000124.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000125.ts',
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
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000126.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000127.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000128.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000129.ts',
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
                        'uri' => '7f2fcf3d543000130.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000131.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000132.ts',
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
                        'uri' => '7f2fcf3d543000133.ts',
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
                        'uri' => '7f2fcf3d543000134.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000135.ts',
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
                        'uri' => '7f2fcf3d543000136.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000137.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000138.ts',
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
                        'uri' => '7f2fcf3d543000139.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000140.ts',
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
                        'uri' => '7f2fcf3d543000141.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000142.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 149,
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
                        'uri' => '7f2fcf3d543000143.ts',
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
                        'uri' => '7f2fcf3d543000144.ts',
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
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000145.ts',
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
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000146.ts',
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
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000147.ts',
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
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000148.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000149.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 156,
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
                        'uri' => '7f2fcf3d543000150.ts',
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
                        'duration' => 7.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000151.ts',
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
                        'uri' => '7f2fcf3d543000152.ts',
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
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000153.ts',
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
                        'uri' => '7f2fcf3d543000154.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000155.ts',
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
                        'uri' => '7f2fcf3d543000156.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000157.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000158.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000159.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 166,
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
                        'uri' => '7f2fcf3d543000160.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000161.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000162.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000163.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 170,
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
                        'uri' => '7f2fcf3d543000164.ts',
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
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '7f2fcf3d543000165.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000166.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000167.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000168.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000169.ts',
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
                        'uri' => '7f2fcf3d543000170.ts',
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000171.ts',
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000172.ts',
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
                        'uri' => '7f2fcf3d543000173.ts',
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
                        'uri' => '7f2fcf3d543000174.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000175.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000176.ts',
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
                        'duration' => 5.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000177.ts',
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
                        'duration' => 2.52,
                        'title' => '',
                        'uri' => '7f2fcf3d543000178.ts',
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
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000179.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000180.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000181.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000182.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000183.ts',
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
                        'uri' => '7f2fcf3d543000184.ts',
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
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => '7f2fcf3d543000185.ts',
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
                        'duration' => 6.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000186.ts',
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
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000187.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000188.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000189.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000190.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000191.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000192.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000193.ts',
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
                        'duration' => 7.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000194.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000195.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000196.ts',
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
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000197.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 204,
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
                        'uri' => '7f2fcf3d543000198.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000199.ts',
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
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000200.ts',
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
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000201.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000202.ts',
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
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000203.ts',
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
                        'uri' => '7f2fcf3d543000204.ts',
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
                        'uri' => '7f2fcf3d543000205.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000206.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000207.ts',
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
                        'uri' => '7f2fcf3d543000208.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000209.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000210.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000211.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000212.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000213.ts',
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
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000214.ts',
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
                        'uri' => '7f2fcf3d543000215.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000216.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000217.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000218.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000219.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000220.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000221.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000222.ts',
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
                        'uri' => '7f2fcf3d543000223.ts',
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
                        'uri' => '7f2fcf3d543000224.ts',
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
                        'uri' => '7f2fcf3d543000225.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000226.ts',
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
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000227.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000228.ts',
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
                        'duration' => 6.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000229.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000230.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000231.ts',
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
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000232.ts',
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
                        'uri' => '7f2fcf3d543000233.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000234.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000235.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000236.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000237.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000238.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000239.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000240.ts',
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
                        'uri' => '7f2fcf3d543000241.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 248,
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
                        'uri' => '7f2fcf3d543000242.ts',
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
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000243.ts',
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
                        'uri' => '7f2fcf3d543000244.ts',
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
                        'uri' => '7f2fcf3d543000245.ts',
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
                        'duration' => 7.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000246.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000247.ts',
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
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000248.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000249.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000250.ts',
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
                        'uri' => '7f2fcf3d543000251.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000252.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000253.ts',
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
                        'uri' => '7f2fcf3d543000254.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000255.ts',
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
                        'uri' => '7f2fcf3d543000256.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000257.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000258.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000259.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000260.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000261.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 268,
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
                        'uri' => '7f2fcf3d543000262.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000263.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 270,
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000264.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000265.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000266.ts',
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
                        'uri' => '7f2fcf3d543000267.ts',
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000268.ts',
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
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000269.ts',
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
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000270.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000271.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000272.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000273.ts',
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
                        'uri' => '7f2fcf3d543000274.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000275.ts',
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
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000276.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000277.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000278.ts',
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
                        'uri' => '7f2fcf3d543000279.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 286,
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
                        'uri' => '7f2fcf3d543000280.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000281.ts',
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
                        'uri' => '7f2fcf3d543000282.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000283.ts',
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
                        'uri' => '7f2fcf3d543000284.ts',
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
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000285.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000286.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000287.ts',
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
                        'duration' => 4.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000288.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000289.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 296,
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
                        'uri' => '7f2fcf3d543000290.ts',
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
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000291.ts',
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000292.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 299,
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
                        'uri' => '7f2fcf3d543000293.ts',
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
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '7f2fcf3d543000294.ts',
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
                        'duration' => 6.08,
                        'title' => '',
                        'uri' => '7f2fcf3d543000295.ts',
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
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => '7f2fcf3d543000296.ts',
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
                        'duration' => 4.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000297.ts',
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
                        'duration' => 5.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000298.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000299.ts',
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
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000300.ts',
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
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000301.ts',
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
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000302.ts',
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
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000303.ts',
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
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000304.ts',
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
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000305.ts',
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
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '7f2fcf3d543000306.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000307.ts',
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
                        'duration' => 6.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000308.ts',
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
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000309.ts',
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
                        'uri' => '7f2fcf3d543000310.ts',
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
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000311.ts',
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
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000312.ts',
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
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000313.ts',
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
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000314.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000315.ts',
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
                        'uri' => '7f2fcf3d543000316.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 323,
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
                        'uri' => '7f2fcf3d543000317.ts',
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
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000318.ts',
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
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000319.ts',
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
                        'duration' => 5,
                        'title' => '',
                        'uri' => '7f2fcf3d543000320.ts',
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
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000321.ts',
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
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000322.ts',
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
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000323.ts',
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
                        'duration' => 1.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000324.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000325.ts',
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
                        'duration' => 5.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000326.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000327.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000328.ts',
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000329.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000330.ts',
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
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000331.ts',
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
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000332.ts',
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
                        'duration' => 5.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000333.ts',
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000334.ts',
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
                        'duration' => 5,
                        'title' => '',
                        'uri' => '7f2fcf3d543000335.ts',
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
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000336.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 343,
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
                        'uri' => '7f2fcf3d543000337.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000338.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 345,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000339.ts',
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
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000340.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000341.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000342.ts',
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
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000343.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 350,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000344.ts',
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
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000345.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000346.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 353,
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
                        'uri' => '7f2fcf3d543000347.ts',
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
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000348.ts',
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
                        'duration' => 6.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000349.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000350.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 357,
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
                        'uri' => '7f2fcf3d543000351.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000352.ts',
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
                        'duration' => 2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000353.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 360,
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
                        'duration' => 6.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000354.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000355.ts',
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
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => '7f2fcf3d543000356.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 363,
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
                        'uri' => '7f2fcf3d543000357.ts',
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
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000358.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 365,
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
                        'uri' => '7f2fcf3d543000359.ts',
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
                        'duration' => 5.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000360.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000361.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000362.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000363.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 370,
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
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000364.ts',
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
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000365.ts',
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
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000366.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000367.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000368.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000369.ts',
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
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000370.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000371.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 378,
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
                        'uri' => '7f2fcf3d543000372.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000373.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 380,
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
                        'uri' => '7f2fcf3d543000374.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000375.ts',
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
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000376.ts',
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000377.ts',
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
                        'duration' => 7.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000378.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000379.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 386,
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
                        'uri' => '7f2fcf3d543000380.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000381.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000382.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000383.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 390,
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
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000384.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000385.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 392,
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
                        'uri' => '7f2fcf3d543000386.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000387.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000388.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000389.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000390.ts',
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
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000391.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 398,
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
                        'uri' => '7f2fcf3d543000392.ts',
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
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000393.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 400,
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
                        'uri' => '7f2fcf3d543000394.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 401,
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
                        'uri' => '7f2fcf3d543000395.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000396.ts',
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
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000397.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000398.ts',
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
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '7f2fcf3d543000399.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000400.ts',
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
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000401.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000402.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000403.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 410,
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
                        'duration' => 7.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000404.ts',
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
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000405.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000406.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000407.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000408.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 415,
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
                        'uri' => '7f2fcf3d543000409.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000410.ts',
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
                        'duration' => 6.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000411.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000412.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000413.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 420,
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
                        'uri' => '7f2fcf3d543000414.ts',
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
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000415.ts',
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
                        'duration' => 2.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000416.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000417.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000418.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000419.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000420.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000421.ts',
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
                        'duration' => 7.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000422.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000423.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 430,
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543000424.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000425.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000426.ts',
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
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543000427.ts',
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000428.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000429.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000430.ts',
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
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000431.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000432.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 439,
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
                        'uri' => '7f2fcf3d543000433.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 440,
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
                        'uri' => '7f2fcf3d543000434.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 441,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000435.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 442,
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
                        'uri' => '7f2fcf3d543000436.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 443,
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
                        'uri' => '7f2fcf3d543000437.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 444,
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
                        'uri' => '7f2fcf3d543000438.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 445,
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
                        'uri' => '7f2fcf3d543000439.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 446,
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
                        'uri' => '7f2fcf3d543000440.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 447,
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
                        'uri' => '7f2fcf3d543000441.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 448,
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
                        'uri' => '7f2fcf3d543000442.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 449,
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
                        'uri' => '7f2fcf3d543000443.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 450,
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
                        'uri' => '7f2fcf3d543000444.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 451,
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
                        'uri' => '7f2fcf3d543000445.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 452,
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
                        'uri' => '7f2fcf3d543000446.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 453,
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
                        'uri' => '7f2fcf3d543000447.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 454,
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
                        'uri' => '7f2fcf3d543000448.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 455,
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
                        'uri' => '7f2fcf3d543000449.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 456,
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
                        'uri' => '7f2fcf3d543000450.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 457,
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
                        'uri' => '7f2fcf3d543000451.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 458,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000452.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 459,
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
                        'uri' => '7f2fcf3d543000453.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 460,
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
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000454.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 461,
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
                        'uri' => '7f2fcf3d543000455.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 462,
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
                        'uri' => '7f2fcf3d543000456.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 463,
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
                        'uri' => '7f2fcf3d543000457.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 464,
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
                        'uri' => '7f2fcf3d543000458.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 465,
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
                        'uri' => '7f2fcf3d543000459.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 466,
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
                        'uri' => '7f2fcf3d543000460.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 467,
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
                        'uri' => '7f2fcf3d543000461.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 468,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000462.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 469,
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
                        'uri' => '7f2fcf3d543000463.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 470,
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
                        'uri' => '7f2fcf3d543000464.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 471,
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
                        'uri' => '7f2fcf3d543000465.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 472,
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
                        'uri' => '7f2fcf3d543000466.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 473,
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
                        'uri' => '7f2fcf3d543000467.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 474,
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
                        'uri' => '7f2fcf3d543000468.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 475,
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
                        'uri' => '7f2fcf3d543000469.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 476,
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
                        'uri' => '7f2fcf3d543000470.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 477,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000471.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 478,
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
                        'uri' => '7f2fcf3d543000472.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 479,
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
                        'uri' => '7f2fcf3d543000473.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 480,
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
                        'uri' => '7f2fcf3d543000474.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 481,
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
                        'uri' => '7f2fcf3d543000475.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 482,
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
                        'uri' => '7f2fcf3d543000476.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 483,
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
                        'uri' => '7f2fcf3d543000477.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 484,
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
                        'uri' => '7f2fcf3d543000478.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 485,
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
                        'uri' => '7f2fcf3d543000479.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 486,
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
                        'uri' => '7f2fcf3d543000480.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 487,
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
                        'uri' => '7f2fcf3d543000481.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 488,
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
                        'uri' => '7f2fcf3d543000482.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 489,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000483.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 490,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000484.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 491,
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
                        'uri' => '7f2fcf3d543000485.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 492,
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
                        'uri' => '7f2fcf3d543000486.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 493,
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
                        'uri' => '7f2fcf3d543000487.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 494,
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
                        'uri' => '7f2fcf3d543000488.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 495,
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
                        'uri' => '7f2fcf3d543000489.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 496,
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
                        'uri' => '7f2fcf3d543000490.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 497,
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
                        'uri' => '7f2fcf3d543000491.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 498,
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
                        'uri' => '7f2fcf3d543000492.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 499,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000493.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 500,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d5434341232.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 501,
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
                        'uri' => '7f2fcf3d5434341233.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 502,
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
                        'uri' => '7f2fcf3d5434341234.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 503,
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
                        'uri' => '7f2fcf3d5434341235.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 504,
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
                        'uri' => '7f2fcf3d5434341236.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 505,
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
                        'uri' => '7f2fcf3d5434341237.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 506,
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
                        'uri' => '7f2fcf3d5434341238.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 507,
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
                        'uri' => '7f2fcf3d543000494.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 508,
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
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => '7f2fcf3d543000495.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 509,
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
                        'uri' => '7f2fcf3d543000496.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 510,
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
                        'uri' => '7f2fcf3d543000497.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 511,
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
                        'uri' => '7f2fcf3d543000498.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 512,
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
                        'uri' => '7f2fcf3d543000499.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 513,
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
                        'uri' => '7f2fcf3d543000500.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 514,
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
                        'uri' => '7f2fcf3d543000501.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 515,
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
                        'uri' => '7f2fcf3d543000502.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 516,
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
                        'uri' => '7f2fcf3d543000503.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 517,
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
                        'uri' => '7f2fcf3d543000504.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 518,
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
                        'uri' => '7f2fcf3d543000505.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 519,
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
                        'uri' => '7f2fcf3d543000506.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 520,
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
                        'uri' => '7f2fcf3d543000507.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 521,
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
                        'uri' => '7f2fcf3d543000508.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 522,
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
                        'uri' => '7f2fcf3d543000509.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 523,
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
                        'uri' => '7f2fcf3d543000510.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 524,
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
                        'uri' => '7f2fcf3d543000511.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 525,
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
                        'uri' => '7f2fcf3d543000512.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 526,
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
                        'uri' => '7f2fcf3d543000513.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 527,
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
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000514.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 528,
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
                        'uri' => '7f2fcf3d543000515.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 529,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000516.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 530,
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
                        'uri' => '7f2fcf3d543000517.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 531,
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
                        'uri' => '7f2fcf3d543000518.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 532,
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
                        'uri' => '7f2fcf3d543000519.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 533,
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
                        'uri' => '7f2fcf3d543000520.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 534,
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
                        'uri' => '7f2fcf3d543000521.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 535,
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
                        'uri' => '7f2fcf3d543000522.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 536,
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
                        'uri' => '7f2fcf3d543000523.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 537,
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
                        'duration' => 6.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000524.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 538,
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
                        'uri' => '7f2fcf3d543000525.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 539,
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
                        'uri' => '7f2fcf3d543000526.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 540,
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
                        'uri' => '7f2fcf3d543000527.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 541,
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
                        'uri' => '7f2fcf3d543000528.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 542,
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
                        'uri' => '7f2fcf3d543000529.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 543,
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
                        'uri' => '7f2fcf3d543000530.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 544,
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
                        'uri' => '7f2fcf3d543000531.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 545,
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
                        'uri' => '7f2fcf3d543000532.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 546,
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
                        'uri' => '7f2fcf3d543000533.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 547,
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
                        'uri' => '7f2fcf3d543000534.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 548,
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
                        'uri' => '7f2fcf3d543000535.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 549,
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
                        'uri' => '7f2fcf3d543000536.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 550,
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
                        'uri' => '7f2fcf3d543000537.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 551,
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
                        'uri' => '7f2fcf3d543000538.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 552,
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
                        'uri' => '7f2fcf3d543000539.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 553,
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
                        'uri' => '7f2fcf3d543000540.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 554,
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
                        'uri' => '7f2fcf3d543000541.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 555,
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
                        'uri' => '7f2fcf3d543000542.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 556,
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
                        'uri' => '7f2fcf3d543000543.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 557,
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
                        'uri' => '7f2fcf3d543000544.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 558,
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
                        'uri' => '7f2fcf3d543000545.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 559,
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
                        'uri' => '7f2fcf3d543000546.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 560,
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
                        'uri' => '7f2fcf3d543000547.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 561,
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
                        'uri' => '7f2fcf3d543000548.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 562,
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
                        'uri' => '7f2fcf3d543000549.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 563,
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
                        'uri' => '7f2fcf3d543000550.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 564,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000551.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 565,
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
                        'uri' => '7f2fcf3d543000552.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 566,
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
                        'uri' => '7f2fcf3d543000553.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 567,
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
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000554.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 568,
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
                        'uri' => '7f2fcf3d543000555.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 569,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.08,
                        'title' => '',
                        'uri' => '7f2fcf3d543000556.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 570,
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
                        'uri' => '7f2fcf3d543000557.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 571,
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
                        'uri' => '7f2fcf3d543000558.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 572,
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
                        'uri' => '7f2fcf3d543000559.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 573,
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
                        'uri' => '7f2fcf3d543000560.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 574,
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
                        'uri' => '7f2fcf3d543000561.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 575,
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
                        'uri' => '7f2fcf3d543000562.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 576,
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
                        'uri' => '7f2fcf3d543000563.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 577,
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
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000564.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 578,
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
                        'uri' => '7f2fcf3d543000565.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 579,
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
                        'uri' => '7f2fcf3d543000566.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 580,
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
                        'uri' => '7f2fcf3d543000567.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 581,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000568.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 582,
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
                        'uri' => '7f2fcf3d543000569.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 583,
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
                        'uri' => '7f2fcf3d543000570.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 584,
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
                        'uri' => '7f2fcf3d543000571.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 585,
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
                        'uri' => '7f2fcf3d543000572.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 586,
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
                        'uri' => '7f2fcf3d543000573.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 587,
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
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000574.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 588,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000575.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 589,
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
                        'uri' => '7f2fcf3d543000576.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 590,
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
                        'uri' => '7f2fcf3d543000577.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 591,
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
                        'uri' => '7f2fcf3d543000578.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 592,
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
                        'uri' => '7f2fcf3d543000579.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 593,
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
                        'uri' => '7f2fcf3d543000580.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 594,
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
                        'uri' => '7f2fcf3d543000581.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 595,
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
                        'uri' => '7f2fcf3d543000582.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 596,
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
                        'uri' => '7f2fcf3d543000583.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 597,
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
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => '7f2fcf3d543000584.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 598,
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
                        'uri' => '7f2fcf3d543000585.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 599,
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
                        'uri' => '7f2fcf3d543000586.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 600,
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
                        'uri' => '7f2fcf3d543000587.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 601,
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
                        'uri' => '7f2fcf3d543000588.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 602,
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
                        'uri' => '7f2fcf3d543000589.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 603,
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
                        'uri' => '7f2fcf3d543000590.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 604,
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
                        'uri' => '7f2fcf3d543000591.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 605,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000592.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 606,
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
                        'uri' => '7f2fcf3d543000593.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 607,
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
                        'uri' => '7f2fcf3d543000594.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 608,
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
                        'uri' => '7f2fcf3d543000595.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 609,
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
                        'uri' => '7f2fcf3d543000596.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 610,
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
                        'uri' => '7f2fcf3d543000597.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 611,
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
                        'uri' => '7f2fcf3d543000598.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 612,
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
                        'uri' => '7f2fcf3d543000599.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 613,
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
                        'uri' => '7f2fcf3d543000600.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 614,
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
                        'uri' => '7f2fcf3d543000601.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 615,
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
                        'uri' => '7f2fcf3d543000602.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 616,
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
                        'uri' => '7f2fcf3d543000603.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 617,
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
                        'uri' => '7f2fcf3d543000604.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 618,
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
                        'uri' => '7f2fcf3d543000605.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 619,
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
                        'uri' => '7f2fcf3d543000606.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 620,
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
                        'uri' => '7f2fcf3d543000607.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 621,
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
                        'uri' => '7f2fcf3d543000608.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 622,
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
                        'uri' => '7f2fcf3d543000609.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 623,
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
                        'uri' => '7f2fcf3d543000610.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 624,
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
                        'uri' => '7f2fcf3d543000611.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 625,
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
                        'uri' => '7f2fcf3d543000612.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 626,
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
                        'uri' => '7f2fcf3d543000613.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 627,
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
                        'duration' => 7.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000614.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 628,
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
                        'uri' => '7f2fcf3d543000615.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 629,
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
                        'uri' => '7f2fcf3d543000616.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 630,
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
                        'uri' => '7f2fcf3d543000617.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 631,
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
                        'uri' => '7f2fcf3d543000618.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 632,
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
                        'uri' => '7f2fcf3d543000619.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 633,
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
                        'uri' => '7f2fcf3d543000620.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 634,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => '7f2fcf3d543000621.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 635,
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
                        'uri' => '7f2fcf3d543000622.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 636,
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
                        'uri' => '7f2fcf3d543000623.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 637,
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
                        'uri' => '7f2fcf3d543000624.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 638,
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
                        'uri' => '7f2fcf3d543000625.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 639,
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
                        'uri' => '7f2fcf3d543000626.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 640,
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
                        'uri' => '7f2fcf3d543000627.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 641,
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
                        'uri' => '7f2fcf3d543000628.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 642,
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
                        'uri' => '7f2fcf3d543000629.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 643,
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
                        'uri' => '7f2fcf3d543000630.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 644,
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
                        'uri' => '7f2fcf3d543000631.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 645,
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
                        'uri' => '7f2fcf3d543000632.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 646,
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
                        'uri' => '7f2fcf3d543000633.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 647,
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
                        'uri' => '7f2fcf3d543000634.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 648,
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
                        'uri' => '7f2fcf3d543000635.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 649,
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
                        'uri' => '7f2fcf3d543000636.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 650,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000637.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 651,
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
                        'uri' => '7f2fcf3d543000638.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 652,
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
                        'uri' => '7f2fcf3d543000639.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 653,
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
                        'uri' => '7f2fcf3d543000640.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 654,
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
                        'uri' => '7f2fcf3d543000641.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 655,
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
                        'uri' => '7f2fcf3d543000642.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 656,
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
                        'uri' => '7f2fcf3d543000643.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 657,
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
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000644.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 658,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.08,
                        'title' => '',
                        'uri' => '7f2fcf3d543000645.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 659,
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
                        'uri' => '7f2fcf3d543000646.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 660,
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
                        'uri' => '7f2fcf3d543000647.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 661,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543000648.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 662,
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
                        'uri' => '7f2fcf3d543000649.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 663,
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
                        'uri' => '7f2fcf3d543000650.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 664,
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
                        'uri' => '7f2fcf3d543000651.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 665,
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
                        'uri' => '7f2fcf3d543000652.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 666,
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
                        'uri' => '7f2fcf3d543000653.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 667,
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
                        'uri' => '7f2fcf3d543000654.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 668,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000655.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 669,
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
                        'uri' => '7f2fcf3d543000656.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 670,
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
                        'uri' => '7f2fcf3d543000657.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 671,
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
                        'uri' => '7f2fcf3d543000658.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 672,
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
                        'uri' => '7f2fcf3d543000659.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 673,
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
                        'duration' => 6.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000660.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 674,
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
                        'uri' => '7f2fcf3d543000661.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 675,
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
                        'uri' => '7f2fcf3d543000662.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 676,
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
                        'uri' => '7f2fcf3d543000663.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 677,
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000664.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 678,
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
                        'uri' => '7f2fcf3d543000665.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 679,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000666.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 680,
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
                        'uri' => '7f2fcf3d543000667.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 681,
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
                        'uri' => '7f2fcf3d543000668.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 682,
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
                        'uri' => '7f2fcf3d543000669.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 683,
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
                        'uri' => '7f2fcf3d543000670.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 684,
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
                        'uri' => '7f2fcf3d543000671.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 685,
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
                        'uri' => '7f2fcf3d543000672.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 686,
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
                        'uri' => '7f2fcf3d543000673.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 687,
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
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000674.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 688,
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
                        'uri' => '7f2fcf3d543000675.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 689,
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
                        'uri' => '7f2fcf3d543000676.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 690,
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
                        'uri' => '7f2fcf3d543000677.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 691,
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
                        'uri' => '7f2fcf3d543000678.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 692,
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
                        'uri' => '7f2fcf3d543000679.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 693,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000680.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 694,
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
                        'uri' => '7f2fcf3d543000681.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 695,
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
                        'uri' => '7f2fcf3d543000682.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 696,
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
                        'uri' => '7f2fcf3d543000683.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 697,
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
                        'uri' => '7f2fcf3d543000684.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 698,
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
                        'uri' => '7f2fcf3d543000685.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 699,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000686.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 700,
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
                        'uri' => '7f2fcf3d543000687.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 701,
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
                        'uri' => '7f2fcf3d543000688.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 702,
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
                        'uri' => '7f2fcf3d543000689.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 703,
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
                        'uri' => '7f2fcf3d543000690.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 704,
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
                        'uri' => '7f2fcf3d543000691.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 705,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000692.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 706,
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
                        'uri' => '7f2fcf3d543000693.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 707,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000694.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 708,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000695.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 709,
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
                        'uri' => '7f2fcf3d543000696.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 710,
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
                        'uri' => '7f2fcf3d543000697.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 711,
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
                        'uri' => '7f2fcf3d543000698.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 712,
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
                        'uri' => '7f2fcf3d543000699.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 713,
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
                        'uri' => '7f2fcf3d543000700.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 714,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000701.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 715,
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
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000702.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 716,
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
                        'uri' => '7f2fcf3d543000703.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 717,
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
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000704.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 718,
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
                        'uri' => '7f2fcf3d543000705.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 719,
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
                        'uri' => '7f2fcf3d543000706.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 720,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000707.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 721,
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
                        'uri' => '7f2fcf3d543000708.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 722,
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
                        'uri' => '7f2fcf3d543000709.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 723,
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
                        'uri' => '7f2fcf3d543000710.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 724,
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
                        'uri' => '7f2fcf3d543000711.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 725,
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
                        'uri' => '7f2fcf3d543000712.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 726,
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
                        'uri' => '7f2fcf3d543000713.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 727,
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
                        'uri' => '7f2fcf3d543000714.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 728,
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
                        'uri' => '7f2fcf3d543000715.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 729,
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
                        'uri' => '7f2fcf3d543000716.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 730,
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
                        'uri' => '7f2fcf3d543000717.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 731,
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
                        'uri' => '7f2fcf3d543000718.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 732,
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
                        'uri' => '7f2fcf3d543000719.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 733,
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
                        'uri' => '7f2fcf3d543000720.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 734,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000721.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 735,
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
                        'uri' => '7f2fcf3d543000722.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 736,
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
                        'uri' => '7f2fcf3d543000723.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 737,
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
                        'uri' => '7f2fcf3d543000724.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 738,
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
                        'uri' => '7f2fcf3d543000725.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 739,
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
                        'uri' => '7f2fcf3d543000726.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 740,
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
                        'uri' => '7f2fcf3d543000727.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 741,
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
                        'uri' => '7f2fcf3d543000728.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 742,
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
                        'uri' => '7f2fcf3d543000729.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 743,
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
                        'uri' => '7f2fcf3d543000730.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 744,
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
                        'uri' => '7f2fcf3d543000731.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 745,
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
                        'uri' => '7f2fcf3d543000732.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 746,
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
                        'uri' => '7f2fcf3d543000733.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 747,
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
                        'uri' => '7f2fcf3d543000734.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 748,
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
                        'uri' => '7f2fcf3d543000735.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 749,
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
                        'uri' => '7f2fcf3d543000736.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 750,
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
                        'uri' => '7f2fcf3d543000737.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 751,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000738.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 752,
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
                        'uri' => '7f2fcf3d543000739.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 753,
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
                        'uri' => '7f2fcf3d543000740.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 754,
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
                        'uri' => '7f2fcf3d543000741.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 755,
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
                        'uri' => '7f2fcf3d543000742.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 756,
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
                        'uri' => '7f2fcf3d543000743.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 757,
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
                        'uri' => '7f2fcf3d543000744.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 758,
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
                        'uri' => '7f2fcf3d543000745.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 759,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000746.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 760,
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
                        'uri' => '7f2fcf3d543000747.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 761,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000748.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 762,
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
                        'uri' => '7f2fcf3d543000749.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 763,
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
                        'uri' => '7f2fcf3d543000750.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 764,
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
                        'uri' => '7f2fcf3d543000751.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 765,
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
                        'uri' => '7f2fcf3d543000752.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 766,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000753.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 767,
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
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000754.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 768,
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
                        'uri' => '7f2fcf3d543000755.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 769,
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
                        'uri' => '7f2fcf3d543000756.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 770,
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
                        'uri' => '7f2fcf3d543000757.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 771,
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
                        'uri' => '7f2fcf3d543000758.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 772,
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
                        'uri' => '7f2fcf3d543000759.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 773,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000760.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 774,
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
                        'uri' => '7f2fcf3d543000761.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 775,
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
                        'uri' => '7f2fcf3d543000762.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 776,
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
                        'uri' => '7f2fcf3d543000763.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 777,
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
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000764.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 778,
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
                        'uri' => '7f2fcf3d543000765.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 779,
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
                        'uri' => '7f2fcf3d543000766.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 780,
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
                        'uri' => '7f2fcf3d543000767.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 781,
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
                        'uri' => '7f2fcf3d543000768.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 782,
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
                        'uri' => '7f2fcf3d543000769.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 783,
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
                        'uri' => '7f2fcf3d543000770.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 784,
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
                        'uri' => '7f2fcf3d543000771.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 785,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000772.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 786,
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
                        'uri' => '7f2fcf3d543000773.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 787,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000774.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 788,
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
                        'uri' => '7f2fcf3d543000775.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 789,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543000776.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 790,
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
                        'uri' => '7f2fcf3d543000777.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 791,
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
                        'uri' => '7f2fcf3d543000778.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 792,
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
                        'uri' => '7f2fcf3d543000779.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 793,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000780.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 794,
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
                        'uri' => '7f2fcf3d543000781.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 795,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000782.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 796,
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
                        'uri' => '7f2fcf3d543000783.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 797,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000784.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 798,
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
                        'uri' => '7f2fcf3d543000785.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 799,
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
                        'uri' => '7f2fcf3d543000786.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 800,
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
                        'uri' => '7f2fcf3d543000787.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 801,
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
                        'uri' => '7f2fcf3d543000788.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 802,
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
                        'uri' => '7f2fcf3d543000789.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 803,
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
                        'uri' => '7f2fcf3d543000790.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 804,
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
                        'uri' => '7f2fcf3d543000791.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 805,
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
                        'uri' => '7f2fcf3d543000792.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 806,
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
                        'uri' => '7f2fcf3d543000793.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 807,
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
                        'uri' => '7f2fcf3d543000794.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 808,
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
                        'uri' => '7f2fcf3d543000795.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 809,
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
                        'uri' => '7f2fcf3d543000796.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 810,
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
                        'uri' => '7f2fcf3d543000797.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 811,
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
                        'uri' => '7f2fcf3d543000798.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 812,
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
                        'uri' => '7f2fcf3d543000799.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 813,
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
                        'uri' => '7f2fcf3d543000800.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 814,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000801.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 815,
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
                        'uri' => '7f2fcf3d543000802.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 816,
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
                        'uri' => '7f2fcf3d543000803.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 817,
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
                        'uri' => '7f2fcf3d543000804.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 818,
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
                        'uri' => '7f2fcf3d543000805.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 819,
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
                        'uri' => '7f2fcf3d543000806.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 820,
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
                        'uri' => '7f2fcf3d543000807.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 821,
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
                        'uri' => '7f2fcf3d543000808.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 822,
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
                        'uri' => '7f2fcf3d543000809.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 823,
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
                        'uri' => '7f2fcf3d543000810.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 824,
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
                        'uri' => '7f2fcf3d543000811.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 825,
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
                        'uri' => '7f2fcf3d543000812.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 826,
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
                        'uri' => '7f2fcf3d543000813.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 827,
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
                        'uri' => '7f2fcf3d543000814.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 828,
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
                        'uri' => '7f2fcf3d543000815.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 829,
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
                        'uri' => '7f2fcf3d543000816.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 830,
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
                        'uri' => '7f2fcf3d543000817.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 831,
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
                        'uri' => '7f2fcf3d543000818.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 832,
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
                        'uri' => '7f2fcf3d543000819.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 833,
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
                        'uri' => '7f2fcf3d543000820.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 834,
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
                        'uri' => '7f2fcf3d543000821.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 835,
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
                        'uri' => '7f2fcf3d543000822.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 836,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000823.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 837,
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
                        'duration' => 1.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000824.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 838,
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
                        'uri' => '7f2fcf3d543000825.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 839,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000826.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 840,
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
                        'uri' => '7f2fcf3d543000827.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 841,
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
                        'uri' => '7f2fcf3d543000828.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 842,
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
                        'uri' => '7f2fcf3d543000829.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 843,
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
                        'uri' => '7f2fcf3d543000830.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 844,
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
                        'uri' => '7f2fcf3d543000831.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 845,
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
                        'uri' => '7f2fcf3d543000832.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 846,
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
                        'uri' => '7f2fcf3d543000833.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 847,
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
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000834.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 848,
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
                        'uri' => '7f2fcf3d543000835.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 849,
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
                        'uri' => '7f2fcf3d543000836.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 850,
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
                        'uri' => '7f2fcf3d543000837.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 851,
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
                        'uri' => '7f2fcf3d543000838.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 852,
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
                        'uri' => '7f2fcf3d543000839.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 853,
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
                        'uri' => '7f2fcf3d543000840.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 854,
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
                        'uri' => '7f2fcf3d543000841.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 855,
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
                        'uri' => '7f2fcf3d543000842.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 856,
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
                        'uri' => '7f2fcf3d543000843.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 857,
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
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => '7f2fcf3d543000844.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 858,
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
                        'uri' => '7f2fcf3d543000845.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 859,
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
                        'uri' => '7f2fcf3d543000846.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 860,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.6,
                        'title' => '',
                        'uri' => '7f2fcf3d543000847.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 861,
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
                        'uri' => '7f2fcf3d543000848.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 862,
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
                        'uri' => '7f2fcf3d543000849.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 863,
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
                        'uri' => '7f2fcf3d543000850.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 864,
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
                        'uri' => '7f2fcf3d543000851.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 865,
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
                        'uri' => '7f2fcf3d543000852.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 866,
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
                        'uri' => '7f2fcf3d543000853.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 867,
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
                        'uri' => '7f2fcf3d543000854.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 868,
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
                        'uri' => '7f2fcf3d543000855.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 869,
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
                        'uri' => '7f2fcf3d543000856.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 870,
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
                        'uri' => '7f2fcf3d543000857.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 871,
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
                        'uri' => '7f2fcf3d543000858.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 872,
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
                        'uri' => '7f2fcf3d543000859.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 873,
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
                        'uri' => '7f2fcf3d543000860.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 874,
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
                        'uri' => '7f2fcf3d543000861.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 875,
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
                        'uri' => '7f2fcf3d543000862.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 876,
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
                        'uri' => '7f2fcf3d543000863.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 877,
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
                        'uri' => '7f2fcf3d543000864.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 878,
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
                        'uri' => '7f2fcf3d543000865.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 879,
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
                        'uri' => '7f2fcf3d543000866.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 880,
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
                        'uri' => '7f2fcf3d543000867.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 881,
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
                        'uri' => '7f2fcf3d543000868.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 882,
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
                        'uri' => '7f2fcf3d543000869.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 883,
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
                        'uri' => '7f2fcf3d543000870.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 884,
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
                        'uri' => '7f2fcf3d543000871.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 885,
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
                        'uri' => '7f2fcf3d543000872.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 886,
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
                        'uri' => '7f2fcf3d543000873.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 887,
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
                        'uri' => '7f2fcf3d543000874.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 888,
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
                        'uri' => '7f2fcf3d543000875.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 889,
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
                        'uri' => '7f2fcf3d543000876.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 890,
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
                        'uri' => '7f2fcf3d543000877.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 891,
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
                        'uri' => '7f2fcf3d543000878.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 892,
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
                        'uri' => '7f2fcf3d543000879.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 893,
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
                        'uri' => '7f2fcf3d543000880.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 894,
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
                        'uri' => '7f2fcf3d543000881.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 895,
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
                        'uri' => '7f2fcf3d543000882.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 896,
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
                        'uri' => '7f2fcf3d543000883.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 897,
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
                        'uri' => '7f2fcf3d543000884.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 898,
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
                        'uri' => '7f2fcf3d543000885.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 899,
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
                        'uri' => '7f2fcf3d543000886.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 900,
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
                        'uri' => '7f2fcf3d543000887.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 901,
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
                        'uri' => '7f2fcf3d543000888.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 902,
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
                        'uri' => '7f2fcf3d543000889.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 903,
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
                        'uri' => '7f2fcf3d543000890.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 904,
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
                        'uri' => '7f2fcf3d543000891.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 905,
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
                        'uri' => '7f2fcf3d543000892.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 906,
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
                        'uri' => '7f2fcf3d543000893.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 907,
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
                        'uri' => '7f2fcf3d543000894.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 908,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000895.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 909,
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
                        'uri' => '7f2fcf3d543000896.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 910,
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
                        'uri' => '7f2fcf3d543000897.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 911,
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
                        'uri' => '7f2fcf3d543000898.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 912,
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
                        'uri' => '7f2fcf3d543000899.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 913,
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
                        'uri' => '7f2fcf3d543000900.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 914,
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
                        'uri' => '7f2fcf3d543000901.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 915,
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
                        'uri' => '7f2fcf3d543000902.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 916,
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
                        'uri' => '7f2fcf3d543000903.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 917,
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
                        'uri' => '7f2fcf3d543000904.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 918,
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
                        'uri' => '7f2fcf3d543000905.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 919,
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
                        'uri' => '7f2fcf3d543000906.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 920,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000907.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 921,
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
                        'uri' => '7f2fcf3d543000908.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 922,
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
                        'uri' => '7f2fcf3d543000909.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 923,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000910.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 924,
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
                        'uri' => '7f2fcf3d543000911.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 925,
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
                        'uri' => '7f2fcf3d543000912.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 926,
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
                        'uri' => '7f2fcf3d543000913.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 927,
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
                        'uri' => '7f2fcf3d543000914.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 928,
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
                        'uri' => '7f2fcf3d543000915.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 929,
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
                        'uri' => '7f2fcf3d543000916.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 930,
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
                        'uri' => '7f2fcf3d543000917.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 931,
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
                        'uri' => '7f2fcf3d543000918.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 932,
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
                        'uri' => '7f2fcf3d543000919.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 933,
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
                        'uri' => '7f2fcf3d543000920.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 934,
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
                        'uri' => '7f2fcf3d543000921.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 935,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000922.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 936,
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
                        'uri' => '7f2fcf3d543000923.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 937,
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000924.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 938,
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
                        'uri' => '7f2fcf3d543000925.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 939,
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
                        'uri' => '7f2fcf3d543000926.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 940,
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
                        'uri' => '7f2fcf3d543000927.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 941,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543000928.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 942,
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
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543000929.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 943,
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
                        'uri' => '7f2fcf3d543000930.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 944,
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
                        'uri' => '7f2fcf3d543000931.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 945,
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
                        'uri' => '7f2fcf3d543000932.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 946,
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
                        'uri' => '7f2fcf3d543000933.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 947,
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
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => '7f2fcf3d543000934.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 948,
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
                        'uri' => '7f2fcf3d543000935.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 949,
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
                        'uri' => '7f2fcf3d543000936.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 950,
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
                        'uri' => '7f2fcf3d543000937.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 951,
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
                        'uri' => '7f2fcf3d543000938.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 952,
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
                        'uri' => '7f2fcf3d543000939.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 953,
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
                        'uri' => '7f2fcf3d543000940.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 954,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543000941.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 955,
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
                        'uri' => '7f2fcf3d543000942.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 956,
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
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543000943.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 957,
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
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000944.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 958,
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
                        'uri' => '7f2fcf3d543000945.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 959,
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
                        'uri' => '7f2fcf3d543000946.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 960,
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
                        'uri' => '7f2fcf3d543000947.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 961,
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
                        'uri' => '7f2fcf3d543000948.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 962,
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
                        'uri' => '7f2fcf3d543000949.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 963,
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
                        'uri' => '7f2fcf3d543000950.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 964,
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
                        'uri' => '7f2fcf3d543000951.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 965,
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
                        'uri' => '7f2fcf3d543000952.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 966,
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
                        'uri' => '7f2fcf3d543000953.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 967,
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
                        'uri' => '7f2fcf3d543000954.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 968,
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
                        'uri' => '7f2fcf3d543000955.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 969,
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
                        'uri' => '7f2fcf3d543000956.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 970,
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
                        'uri' => '7f2fcf3d543000957.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 971,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000958.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 972,
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
                        'uri' => '7f2fcf3d543000959.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 973,
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
                        'uri' => '7f2fcf3d543000960.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 974,
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
                        'uri' => '7f2fcf3d543000961.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 975,
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
                        'uri' => '7f2fcf3d543000962.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 976,
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
                        'uri' => '7f2fcf3d543000963.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 977,
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
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543000964.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 978,
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
                        'uri' => '7f2fcf3d543000965.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 979,
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
                        'uri' => '7f2fcf3d543000966.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 980,
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
                        'uri' => '7f2fcf3d543000967.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 981,
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
                        'uri' => '7f2fcf3d543000968.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 982,
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
                        'uri' => '7f2fcf3d543000969.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 983,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543000970.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 984,
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
                        'uri' => '7f2fcf3d543000971.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 985,
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
                        'uri' => '7f2fcf3d543000972.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 986,
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
                        'uri' => '7f2fcf3d543000973.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 987,
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
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543000974.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 988,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543000975.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 989,
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
                        'uri' => '7f2fcf3d543000976.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 990,
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
                        'uri' => '7f2fcf3d543000977.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 991,
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
                        'uri' => '7f2fcf3d543000978.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 992,
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
                        'uri' => '7f2fcf3d543000979.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 993,
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
                        'uri' => '7f2fcf3d543000980.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 994,
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
                        'uri' => '7f2fcf3d543000981.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 995,
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
                        'uri' => '7f2fcf3d543000982.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 996,
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
                        'uri' => '7f2fcf3d543000983.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 997,
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
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543000984.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 998,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.44,
                        'title' => '',
                        'uri' => '7f2fcf3d543000985.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 999,
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
                        'uri' => '7f2fcf3d543000986.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1000,
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
                        'uri' => '7f2fcf3d543000987.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1001,
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
                        'uri' => '7f2fcf3d543000988.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1002,
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
                        'uri' => '7f2fcf3d543000989.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1003,
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
                        'uri' => '7f2fcf3d543000990.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1004,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543000991.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1005,
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
                        'uri' => '7f2fcf3d543000992.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1006,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543000993.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1007,
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
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => '7f2fcf3d543000994.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1008,
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
                        'uri' => '7f2fcf3d543000995.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1009,
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
                        'uri' => '7f2fcf3d543000996.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1010,
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
                        'uri' => '7f2fcf3d543000997.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1011,
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
                        'uri' => '7f2fcf3d543000998.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1012,
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
                        'uri' => '7f2fcf3d543000999.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1013,
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
                        'uri' => '7f2fcf3d543001000.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1014,
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
                        'uri' => '7f2fcf3d543001001.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1015,
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
                        'uri' => '7f2fcf3d543001002.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1016,
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
                        'uri' => '7f2fcf3d543001003.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1017,
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
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => '7f2fcf3d543001004.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1018,
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
                        'uri' => '7f2fcf3d543001005.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1019,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543001006.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1020,
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
                        'uri' => '7f2fcf3d543001007.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1021,
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
                        'uri' => '7f2fcf3d543001008.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1022,
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
                        'uri' => '7f2fcf3d543001009.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1023,
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
                        'uri' => '7f2fcf3d543001010.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1024,
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
                        'uri' => '7f2fcf3d543001011.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1025,
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
                        'uri' => '7f2fcf3d543001012.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1026,
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
                        'uri' => '7f2fcf3d543001013.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1027,
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
                        'uri' => '7f2fcf3d543001014.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1028,
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
                        'uri' => '7f2fcf3d543001015.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1029,
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
                        'uri' => '7f2fcf3d543001016.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1030,
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
                        'uri' => '7f2fcf3d543001017.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1031,
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
                        'uri' => '7f2fcf3d543001018.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1032,
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
                        'uri' => '7f2fcf3d543001019.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1033,
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
                        'uri' => '7f2fcf3d543001020.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1034,
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
                        'uri' => '7f2fcf3d543001021.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1035,
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
                        'uri' => '7f2fcf3d543001022.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1036,
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
                        'uri' => '7f2fcf3d543001023.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1037,
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
                        'uri' => '7f2fcf3d543001024.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1038,
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
                        'uri' => '7f2fcf3d543001025.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1039,
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
                        'uri' => '7f2fcf3d543001026.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1040,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543001027.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1041,
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
                        'uri' => '7f2fcf3d543001028.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1042,
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
                        'uri' => '7f2fcf3d543001029.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1043,
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
                        'uri' => '7f2fcf3d543001030.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1044,
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
                        'uri' => '7f2fcf3d543001031.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1045,
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
                        'uri' => '7f2fcf3d543001032.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1046,
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
                        'uri' => '7f2fcf3d543001033.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1047,
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
                        'duration' => 1.84,
                        'title' => '',
                        'uri' => '7f2fcf3d543001034.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1048,
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
                        'uri' => '7f2fcf3d543001035.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1049,
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
                        'uri' => '7f2fcf3d543001036.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1050,
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
                        'uri' => '7f2fcf3d543001037.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1051,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543001038.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1052,
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
                        'uri' => '7f2fcf3d543001039.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1053,
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
                        'uri' => '7f2fcf3d543001040.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1054,
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
                        'uri' => '7f2fcf3d543001041.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1055,
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
                        'uri' => '7f2fcf3d543001042.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1056,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.24,
                        'title' => '',
                        'uri' => '7f2fcf3d543001043.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1057,
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
                        'uri' => '7f2fcf3d543001044.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1058,
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
                        'uri' => '7f2fcf3d543001045.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1059,
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
                        'uri' => '7f2fcf3d543001046.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1060,
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
                        'uri' => '7f2fcf3d543001047.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1061,
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
                        'uri' => '7f2fcf3d543001048.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1062,
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
                        'uri' => '7f2fcf3d543001049.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1063,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543001050.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1064,
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
                        'uri' => '7f2fcf3d543001051.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1065,
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
                        'uri' => '7f2fcf3d543001052.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1066,
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
                        'uri' => '7f2fcf3d543001053.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1067,
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
                        'uri' => '7f2fcf3d543001054.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1068,
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
                        'uri' => '7f2fcf3d543001055.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1069,
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
                        'uri' => '7f2fcf3d543001056.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1070,
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
                        'uri' => '7f2fcf3d543001057.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1071,
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
                        'uri' => '7f2fcf3d543001058.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1072,
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
                        'uri' => '7f2fcf3d543001059.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1073,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543001060.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1074,
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
                        'uri' => '7f2fcf3d543001061.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1075,
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
                        'uri' => '7f2fcf3d543001062.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1076,
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
                        'uri' => '7f2fcf3d543001063.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1077,
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
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543001064.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1078,
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
                        'uri' => '7f2fcf3d543001065.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1079,
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
                        'uri' => '7f2fcf3d543001066.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1080,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543001067.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1081,
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
                        'uri' => '7f2fcf3d543001068.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1082,
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
                        'uri' => '7f2fcf3d543001069.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1083,
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
                        'uri' => '7f2fcf3d543001070.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1084,
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
                        'uri' => '7f2fcf3d543001071.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1085,
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
                        'uri' => '7f2fcf3d543001072.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1086,
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
                        'uri' => '7f2fcf3d543001073.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1087,
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
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => '7f2fcf3d543001074.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1088,
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
                        'uri' => '7f2fcf3d543001075.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1089,
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
                        'uri' => '7f2fcf3d543001076.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1090,
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
                        'uri' => '7f2fcf3d543001077.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1091,
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
                        'uri' => '7f2fcf3d543001078.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1092,
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
                        'uri' => '7f2fcf3d543001079.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1093,
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
                        'uri' => '7f2fcf3d543001080.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1094,
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
                        'uri' => '7f2fcf3d543001081.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1095,
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
                        'uri' => '7f2fcf3d543001082.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1096,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543001083.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1097,
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
                        'uri' => '7f2fcf3d543001084.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1098,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.2,
                        'title' => '',
                        'uri' => '7f2fcf3d543001085.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1099,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543001086.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1100,
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
                        'uri' => '7f2fcf3d543001087.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1101,
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
                        'uri' => '7f2fcf3d543001088.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1102,
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
                        'uri' => '7f2fcf3d543001089.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1103,
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
                        'uri' => '7f2fcf3d543001090.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1104,
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
                        'uri' => '7f2fcf3d543001091.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1105,
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
                        'uri' => '7f2fcf3d543001092.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1106,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.8,
                        'title' => '',
                        'uri' => '7f2fcf3d543001093.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1107,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543001094.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1108,
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
                        'uri' => '7f2fcf3d543001095.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1109,
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
                        'uri' => '7f2fcf3d543001096.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1110,
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
                        'uri' => '7f2fcf3d543001097.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1111,
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
                        'uri' => '7f2fcf3d543001098.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1112,
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
                        'uri' => '7f2fcf3d543001099.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1113,
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
                        'uri' => '7f2fcf3d543001100.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1114,
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
                        'uri' => '7f2fcf3d543001101.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1115,
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
                        'uri' => '7f2fcf3d543001102.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1116,
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
                        'uri' => '7f2fcf3d543001103.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1117,
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
                        'uri' => '7f2fcf3d543001104.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1118,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543001105.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1119,
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
                        'uri' => '7f2fcf3d543001106.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1120,
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
                        'uri' => '7f2fcf3d543001107.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1121,
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
                        'uri' => '7f2fcf3d543001108.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1122,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => '7f2fcf3d543001109.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1123,
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
                        'uri' => '7f2fcf3d543001110.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1124,
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
                        'uri' => '7f2fcf3d543001111.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1125,
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
                        'uri' => '7f2fcf3d543001112.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1126,
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
                        'uri' => '7f2fcf3d543001113.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1127,
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
                        'uri' => '7f2fcf3d543001114.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1128,
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
                        'uri' => '7f2fcf3d543001115.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1129,
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
                        'uri' => '7f2fcf3d543001116.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1130,
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
                        'uri' => '7f2fcf3d543001117.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1131,
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
                        'uri' => '7f2fcf3d543001118.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1132,
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
                        'uri' => '7f2fcf3d543001119.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1133,
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
                        'uri' => '7f2fcf3d543001120.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1134,
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
                        'uri' => '7f2fcf3d543001121.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1135,
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
                        'uri' => '7f2fcf3d543001122.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1136,
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
                        'uri' => '7f2fcf3d543001123.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1137,
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
                        'uri' => '7f2fcf3d543001124.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1138,
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
                        'uri' => '7f2fcf3d543001125.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1139,
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
                        'uri' => '7f2fcf3d543001126.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1140,
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
                        'uri' => '7f2fcf3d543001127.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1141,
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
                        'uri' => '7f2fcf3d543001128.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1142,
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
                        'uri' => '7f2fcf3d543001129.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1143,
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
                        'uri' => '7f2fcf3d543001130.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1144,
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
                        'uri' => '7f2fcf3d543001131.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1145,
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
                        'uri' => '7f2fcf3d543001132.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1146,
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
                        'uri' => '7f2fcf3d543001133.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1147,
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
                        'uri' => '7f2fcf3d543001134.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1148,
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
                        'uri' => '7f2fcf3d543001135.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1149,
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
                        'uri' => '7f2fcf3d543001136.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1150,
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
                        'uri' => '7f2fcf3d543001137.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1151,
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
                        'uri' => '7f2fcf3d543001138.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1152,
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
                        'uri' => '7f2fcf3d543001139.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1153,
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
                        'uri' => '7f2fcf3d543001140.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1154,
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
                        'uri' => '7f2fcf3d543001141.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1155,
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
                        'uri' => '7f2fcf3d543001142.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1156,
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
                        'uri' => '7f2fcf3d543001143.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1157,
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
                        'uri' => '7f2fcf3d543001144.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1158,
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
                        'uri' => '7f2fcf3d543001145.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1159,
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
                        'uri' => '7f2fcf3d543001146.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1160,
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
                        'uri' => '7f2fcf3d543001147.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1161,
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
                        'uri' => '7f2fcf3d543001148.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1162,
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
                        'uri' => '7f2fcf3d543001149.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1163,
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
                        'uri' => '7f2fcf3d543001150.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1164,
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
                        'uri' => '7f2fcf3d543001151.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1165,
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
                        'uri' => '7f2fcf3d543001152.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1166,
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
                        'uri' => '7f2fcf3d543001153.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1167,
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
                        'uri' => '7f2fcf3d543001154.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1168,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 7.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543001155.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1169,
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
                        'uri' => '7f2fcf3d543001156.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1170,
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
                        'uri' => '7f2fcf3d543001157.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1171,
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
                        'uri' => '7f2fcf3d543001158.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1172,
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
                        'uri' => '7f2fcf3d543001159.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1173,
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
                        'uri' => '7f2fcf3d543001160.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1174,
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
                        'uri' => '7f2fcf3d543001161.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1175,
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
                        'uri' => '7f2fcf3d543001162.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1176,
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
                        'uri' => '7f2fcf3d543001163.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1177,
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
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => '7f2fcf3d543001164.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1178,
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
                        'uri' => '7f2fcf3d543001165.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1179,
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
                        'uri' => '7f2fcf3d543001166.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1180,
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
                        'uri' => '7f2fcf3d543001167.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1181,
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
                        'uri' => '7f2fcf3d543001168.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1182,
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
                        'uri' => '7f2fcf3d543001169.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1183,
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
                        'uri' => '7f2fcf3d543001170.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1184,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 5.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543001171.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1185,
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
                        'uri' => '7f2fcf3d543001172.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1186,
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
                        'uri' => '7f2fcf3d543001173.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1187,
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '7f2fcf3d543001174.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1188,
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
                        'uri' => '7f2fcf3d543001175.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1189,
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
                        'uri' => '7f2fcf3d543001176.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1190,
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
                        'uri' => '7f2fcf3d543001177.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1191,
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
                        'uri' => '7f2fcf3d543001178.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1192,
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
                        'uri' => '7f2fcf3d543001179.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1193,
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
                        'uri' => '7f2fcf3d543001180.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1194,
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
                        'uri' => '7f2fcf3d543001181.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1195,
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
                        'uri' => '7f2fcf3d543001182.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1196,
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
                        'uri' => '7f2fcf3d543001183.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1197,
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
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => '7f2fcf3d543001184.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1198,
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
                        'uri' => '7f2fcf3d543001185.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1199,
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
                        'uri' => '7f2fcf3d543001186.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1200,
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
                        'uri' => '7f2fcf3d543001187.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1201,
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
                        'uri' => '7f2fcf3d543001188.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1202,
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
                        'uri' => '7f2fcf3d543001189.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1203,
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
                        'uri' => '7f2fcf3d543001190.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1204,
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
                        'uri' => '7f2fcf3d543001191.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1205,
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
                        'uri' => '7f2fcf3d543001192.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1206,
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
                        'uri' => '7f2fcf3d543001193.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1207,
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
                        'uri' => '7f2fcf3d543001194.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1208,
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
                        'uri' => '7f2fcf3d543001195.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1209,
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
                        'uri' => '7f2fcf3d543001196.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1210,
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
                        'uri' => '7f2fcf3d543001197.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1211,
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
                        'uri' => '7f2fcf3d543001198.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1212,
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
                        'uri' => '7f2fcf3d543001199.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1213,
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
                        'uri' => '7f2fcf3d543001200.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1214,
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
                        'uri' => '7f2fcf3d543001201.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1215,
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
                        'uri' => '7f2fcf3d543001202.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1216,
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
                        'uri' => '7f2fcf3d543001203.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1217,
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
                        'uri' => '7f2fcf3d543001204.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1218,
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
                        'uri' => '7f2fcf3d543001205.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1219,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 6.96,
                        'title' => '',
                        'uri' => '7f2fcf3d543001206.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1220,
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
                        'uri' => '7f2fcf3d543001207.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1221,
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
                        'uri' => '7f2fcf3d543001208.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1222,
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
                        'uri' => '7f2fcf3d543001209.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1223,
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
                        'uri' => '7f2fcf3d543001210.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1224,
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
                        'uri' => '7f2fcf3d543001211.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1225,
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
                        'uri' => '7f2fcf3d543001212.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1226,
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
                        'uri' => '7f2fcf3d543001213.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1227,
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
                        'uri' => '7f2fcf3d543001214.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1228,
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
                        'uri' => '7f2fcf3d543001215.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1229,
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
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => '7f2fcf3d543001216.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1230,
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
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => '7f2fcf3d543001217.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1231,
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
                        'duration' => 7.68,
                        'title' => '',
                        'uri' => '7f2fcf3d543001218.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1232,
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
                        'uri' => '7f2fcf3d543001219.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1233,
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
                        'uri' => '7f2fcf3d543001220.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1234,
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
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => '7f2fcf3d543001221.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1235,
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
                        'uri' => '7f2fcf3d543001222.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1236,
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
                        'uri' => '7f2fcf3d543001223.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1237,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告',
                            'weight' => 80,
                            'category' => 'marker'
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
                        'position' => 40
                    ],
                    'totalWeight' => 120
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => '7f2fcf3d543001224.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 1238,
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
                ]
            ],
            'totalCount' => 1239,
            'adCount' => 127,
            'contentCount' => 1112,
            'totalDuration' => 4954.24,
            'adDuration' => 522.64,
            'contentDuration' => 4431.6,
            'adPercentage' => 10.55,
            'discontinuityCount' => 127,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'sequenceJumps' => [
                [
                    'index' => 74,
                    'prevSeq' => 543000073,
                    'currentSeq' => 5434341225,
                    'jump' => 4891341152,
                    'prevUri' => '7f2fcf3d543000073.ts',
                    'currentUri' => '7f2fcf3d5434341225.ts'
                ],
                [
                    'index' => 81,
                    'prevSeq' => 5434341231,
                    'currentSeq' => 543000074,
                    'jump' => -4891341157,
                    'prevUri' => '7f2fcf3d5434341231.ts',
                    'currentUri' => '7f2fcf3d543000074.ts'
                ],
                [
                    'index' => 501,
                    'prevSeq' => 543000493,
                    'currentSeq' => 5434341232,
                    'jump' => 4891340739,
                    'prevUri' => '7f2fcf3d543000493.ts',
                    'currentUri' => '7f2fcf3d5434341232.ts'
                ],
                [
                    'index' => 508,
                    'prevSeq' => 5434341238,
                    'currentSeq' => 543000494,
                    'jump' => -4891340744,
                    'prevUri' => '7f2fcf3d5434341238.ts',
                    'currentUri' => '7f2fcf3d543000494.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 1.48,
                'max' => 7.8,
                'avg' => 3.9985794995964,
                'buckets' => [
                    '1.4' => 2,
                    '1.5' => 1,
                    '1.6' => 2,
                    '1.7' => 1,
                    '1.8' => 4,
                    '1.9' => 6,
                    2 => 7,
                    '2.1' => 2,
                    '2.2' => 11,
                    '2.3' => 7,
                    '2.4' => 14,
                    '2.5' => 7,
                    '2.6' => 13,
                    '2.7' => 13,
                    '2.8' => 12,
                    '2.9' => 15,
                    3 => 12,
                    '3.1' => 16,
                    '3.2' => 16,
                    '3.3' => 19,
                    '3.4' => 21,
                    '3.5' => 19,
                    '3.6' => 29,
                    '3.7' => 23,
                    '3.8' => 29,
                    '3.9' => 20,
                    4 => 788,
                    '4.1' => 1,
                    '4.2' => 4,
                    '4.3' => 1,
                    '4.4' => 4,
                    '4.6' => 5,
                    '4.7' => 1,
                    '4.8' => 1,
                    '4.9' => 4,
                    5 => 2,
                    '5.1' => 4,
                    '5.2' => 6,
                    '5.3' => 3,
                    '5.4' => 4,
                    '5.5' => 2,
                    '5.6' => 3,
                    '5.7' => 3,
                    '5.8' => 4,
                    '5.9' => 5,
                    6 => 6,
                    '6.1' => 5,
                    '6.2' => 3,
                    '6.3' => 4,
                    '6.4' => 7,
                    '6.5' => 2,
                    '6.6' => 7,
                    '6.7' => 3,
                    '6.8' => 5,
                    '6.9' => 5,
                    7 => 4,
                    '7.1' => 1,
                    '7.2' => 10,
                    '7.3' => 1,
                    '7.4' => 2,
                    '7.6' => 7,
                    '7.8' => 1
                ]
            ],
            'adClusters' => [
                [
                    'start' => 0,
                    'end' => 0,
                    'count' => 1
                ],
                [
                    'start' => 9,
                    'end' => 9,
                    'count' => 1
                ],
                [
                    'start' => 19,
                    'end' => 19,
                    'count' => 1
                ],
                [
                    'start' => 29,
                    'end' => 29,
                    'count' => 1
                ],
                [
                    'start' => 39,
                    'end' => 39,
                    'count' => 1
                ],
                [
                    'start' => 49,
                    'end' => 49,
                    'count' => 1
                ],
                [
                    'start' => 59,
                    'end' => 59,
                    'count' => 1
                ],
                [
                    'start' => 69,
                    'end' => 69,
                    'count' => 1
                ],
                [
                    'start' => 74,
                    'end' => 74,
                    'count' => 1
                ],
                [
                    'start' => 81,
                    'end' => 81,
                    'count' => 1
                ],
                [
                    'start' => 90,
                    'end' => 90,
                    'count' => 1
                ],
                [
                    'start' => 100,
                    'end' => 100,
                    'count' => 1
                ],
                [
                    'start' => 110,
                    'end' => 110,
                    'count' => 1
                ],
                [
                    'start' => 120,
                    'end' => 120,
                    'count' => 1
                ],
                [
                    'start' => 130,
                    'end' => 130,
                    'count' => 1
                ],
                [
                    'start' => 140,
                    'end' => 140,
                    'count' => 1
                ],
                [
                    'start' => 150,
                    'end' => 150,
                    'count' => 1
                ],
                [
                    'start' => 160,
                    'end' => 160,
                    'count' => 1
                ],
                [
                    'start' => 170,
                    'end' => 170,
                    'count' => 1
                ],
                [
                    'start' => 180,
                    'end' => 180,
                    'count' => 1
                ],
                [
                    'start' => 190,
                    'end' => 190,
                    'count' => 1
                ],
                [
                    'start' => 200,
                    'end' => 200,
                    'count' => 1
                ],
                [
                    'start' => 210,
                    'end' => 210,
                    'count' => 1
                ],
                [
                    'start' => 220,
                    'end' => 220,
                    'count' => 1
                ],
                [
                    'start' => 230,
                    'end' => 230,
                    'count' => 1
                ],
                [
                    'start' => 240,
                    'end' => 240,
                    'count' => 1
                ],
                [
                    'start' => 250,
                    'end' => 250,
                    'count' => 1
                ],
                [
                    'start' => 260,
                    'end' => 260,
                    'count' => 1
                ],
                [
                    'start' => 270,
                    'end' => 270,
                    'count' => 1
                ],
                [
                    'start' => 280,
                    'end' => 280,
                    'count' => 1
                ],
                [
                    'start' => 290,
                    'end' => 290,
                    'count' => 1
                ],
                [
                    'start' => 300,
                    'end' => 300,
                    'count' => 1
                ],
                [
                    'start' => 310,
                    'end' => 310,
                    'count' => 1
                ],
                [
                    'start' => 320,
                    'end' => 320,
                    'count' => 1
                ],
                [
                    'start' => 330,
                    'end' => 330,
                    'count' => 1
                ],
                [
                    'start' => 340,
                    'end' => 340,
                    'count' => 1
                ],
                [
                    'start' => 350,
                    'end' => 350,
                    'count' => 1
                ],
                [
                    'start' => 360,
                    'end' => 360,
                    'count' => 1
                ],
                [
                    'start' => 370,
                    'end' => 370,
                    'count' => 1
                ],
                [
                    'start' => 380,
                    'end' => 380,
                    'count' => 1
                ],
                [
                    'start' => 390,
                    'end' => 390,
                    'count' => 1
                ],
                [
                    'start' => 400,
                    'end' => 400,
                    'count' => 1
                ],
                [
                    'start' => 410,
                    'end' => 410,
                    'count' => 1
                ],
                [
                    'start' => 420,
                    'end' => 420,
                    'count' => 1
                ],
                [
                    'start' => 430,
                    'end' => 430,
                    'count' => 1
                ],
                [
                    'start' => 440,
                    'end' => 440,
                    'count' => 1
                ],
                [
                    'start' => 450,
                    'end' => 450,
                    'count' => 1
                ],
                [
                    'start' => 460,
                    'end' => 460,
                    'count' => 1
                ],
                [
                    'start' => 470,
                    'end' => 470,
                    'count' => 1
                ],
                [
                    'start' => 480,
                    'end' => 480,
                    'count' => 1
                ],
                [
                    'start' => 490,
                    'end' => 490,
                    'count' => 1
                ],
                [
                    'start' => 500,
                    'end' => 501,
                    'count' => 2
                ],
                [
                    'start' => 508,
                    'end' => 508,
                    'count' => 1
                ],
                [
                    'start' => 517,
                    'end' => 517,
                    'count' => 1
                ],
                [
                    'start' => 527,
                    'end' => 527,
                    'count' => 1
                ],
                [
                    'start' => 537,
                    'end' => 537,
                    'count' => 1
                ],
                [
                    'start' => 547,
                    'end' => 547,
                    'count' => 1
                ],
                [
                    'start' => 557,
                    'end' => 557,
                    'count' => 1
                ],
                [
                    'start' => 567,
                    'end' => 567,
                    'count' => 1
                ],
                [
                    'start' => 577,
                    'end' => 577,
                    'count' => 1
                ],
                [
                    'start' => 587,
                    'end' => 587,
                    'count' => 1
                ],
                [
                    'start' => 597,
                    'end' => 597,
                    'count' => 1
                ],
                [
                    'start' => 607,
                    'end' => 607,
                    'count' => 1
                ],
                [
                    'start' => 617,
                    'end' => 617,
                    'count' => 1
                ],
                [
                    'start' => 627,
                    'end' => 627,
                    'count' => 1
                ],
                [
                    'start' => 637,
                    'end' => 637,
                    'count' => 1
                ],
                [
                    'start' => 647,
                    'end' => 647,
                    'count' => 1
                ],
                [
                    'start' => 657,
                    'end' => 657,
                    'count' => 1
                ],
                [
                    'start' => 667,
                    'end' => 667,
                    'count' => 1
                ],
                [
                    'start' => 677,
                    'end' => 677,
                    'count' => 1
                ],
                [
                    'start' => 687,
                    'end' => 687,
                    'count' => 1
                ],
                [
                    'start' => 697,
                    'end' => 697,
                    'count' => 1
                ],
                [
                    'start' => 707,
                    'end' => 707,
                    'count' => 1
                ],
                [
                    'start' => 717,
                    'end' => 717,
                    'count' => 1
                ],
                [
                    'start' => 727,
                    'end' => 727,
                    'count' => 1
                ],
                [
                    'start' => 737,
                    'end' => 737,
                    'count' => 1
                ],
                [
                    'start' => 747,
                    'end' => 747,
                    'count' => 1
                ],
                [
                    'start' => 757,
                    'end' => 757,
                    'count' => 1
                ],
                [
                    'start' => 767,
                    'end' => 767,
                    'count' => 1
                ],
                [
                    'start' => 777,
                    'end' => 777,
                    'count' => 1
                ],
                [
                    'start' => 787,
                    'end' => 787,
                    'count' => 1
                ],
                [
                    'start' => 797,
                    'end' => 797,
                    'count' => 1
                ],
                [
                    'start' => 807,
                    'end' => 807,
                    'count' => 1
                ],
                [
                    'start' => 817,
                    'end' => 817,
                    'count' => 1
                ],
                [
                    'start' => 827,
                    'end' => 827,
                    'count' => 1
                ],
                [
                    'start' => 837,
                    'end' => 837,
                    'count' => 1
                ],
                [
                    'start' => 847,
                    'end' => 847,
                    'count' => 1
                ],
                [
                    'start' => 857,
                    'end' => 857,
                    'count' => 1
                ],
                [
                    'start' => 867,
                    'end' => 867,
                    'count' => 1
                ],
                [
                    'start' => 877,
                    'end' => 877,
                    'count' => 1
                ],
                [
                    'start' => 887,
                    'end' => 887,
                    'count' => 1
                ],
                [
                    'start' => 897,
                    'end' => 897,
                    'count' => 1
                ],
                [
                    'start' => 907,
                    'end' => 907,
                    'count' => 1
                ],
                [
                    'start' => 917,
                    'end' => 917,
                    'count' => 1
                ],
                [
                    'start' => 927,
                    'end' => 927,
                    'count' => 1
                ],
                [
                    'start' => 937,
                    'end' => 937,
                    'count' => 1
                ],
                [
                    'start' => 947,
                    'end' => 947,
                    'count' => 1
                ],
                [
                    'start' => 957,
                    'end' => 957,
                    'count' => 1
                ],
                [
                    'start' => 967,
                    'end' => 967,
                    'count' => 1
                ],
                [
                    'start' => 977,
                    'end' => 977,
                    'count' => 1
                ],
                [
                    'start' => 987,
                    'end' => 987,
                    'count' => 1
                ],
                [
                    'start' => 997,
                    'end' => 997,
                    'count' => 1
                ],
                [
                    'start' => 1007,
                    'end' => 1007,
                    'count' => 1
                ],
                [
                    'start' => 1017,
                    'end' => 1017,
                    'count' => 1
                ],
                [
                    'start' => 1027,
                    'end' => 1027,
                    'count' => 1
                ],
                [
                    'start' => 1037,
                    'end' => 1037,
                    'count' => 1
                ],
                [
                    'start' => 1047,
                    'end' => 1047,
                    'count' => 1
                ],
                [
                    'start' => 1057,
                    'end' => 1057,
                    'count' => 1
                ],
                [
                    'start' => 1067,
                    'end' => 1067,
                    'count' => 1
                ],
                [
                    'start' => 1077,
                    'end' => 1077,
                    'count' => 1
                ],
                [
                    'start' => 1087,
                    'end' => 1087,
                    'count' => 1
                ],
                [
                    'start' => 1097,
                    'end' => 1097,
                    'count' => 1
                ],
                [
                    'start' => 1107,
                    'end' => 1107,
                    'count' => 1
                ],
                [
                    'start' => 1117,
                    'end' => 1117,
                    'count' => 1
                ],
                [
                    'start' => 1127,
                    'end' => 1127,
                    'count' => 1
                ],
                [
                    'start' => 1137,
                    'end' => 1137,
                    'count' => 1
                ],
                [
                    'start' => 1147,
                    'end' => 1147,
                    'count' => 1
                ],
                [
                    'start' => 1157,
                    'end' => 1157,
                    'count' => 1
                ],
                [
                    'start' => 1167,
                    'end' => 1167,
                    'count' => 1
                ],
                [
                    'start' => 1177,
                    'end' => 1177,
                    'count' => 1
                ],
                [
                    'start' => 1187,
                    'end' => 1187,
                    'count' => 1
                ],
                [
                    'start' => 1197,
                    'end' => 1197,
                    'count' => 1
                ],
                [
                    'start' => 1207,
                    'end' => 1207,
                    'count' => 1
                ],
                [
                    'start' => 1217,
                    'end' => 1217,
                    'count' => 1
                ],
                [
                    'start' => 1227,
                    'end' => 1227,
                    'count' => 1
                ],
                [
                    'start' => 1237,
                    'end' => 1237,
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
                            'start_index' => 500,
                            'end_index' => 501,
                            'duration' => 11.2,
                            'segment_count' => 2,
                            'position_ratio' => 0.404
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
                    'count' => 20,
                    'duration' => 86.52
                ],
                'mid_roll_ad' => [
                    'count' => 87,
                    'duration' => 362.28
                ],
                'post_roll_ad' => [
                    'count' => 19,
                    'duration' => 73.84
                ],
                'marker_based_ad' => [
                    'count' => 126,
                    'duration' => 522.64
                ],
                'pattern_based_ad' => [
                    'count' => 0,
                    'duration' => 0
                ],
                'duration_based_ad' => [
                    'count' => 2,
                    'duration' => 3.64
                ]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => '频繁插播',
                'ad_density' => 10.25,
                'attention_grab_score' => 15,
                'frequency_score' => 100,
                'user_experience_impact' => '严重',
                'watchability_score' => 30
            ],
            'confidence' => 100
        ]
    ],
    'last_learn_date' => '2026-07-02 14:51:28'
];
