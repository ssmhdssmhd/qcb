<?php
/**
 * v.lz15uu.com 域名广告和插播规则
 * 自动生成于: 2026-07-02 14:51:35
 */

return [
    'domain' => 'v.lz15uu.com',
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
    'confidence_score' => 97,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => false,
            'start_index' => -1,
            'end_index' => -1,
            'duration' => 0,
            'segment_count' => 0
        ],
        'mid_roll' => [
            'found' => false,
            'count' => 0,
            'points' => []
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 136,
            'end_index' => 137,
            'duration' => 2.27,
            'segment_count' => 2
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 3,
            'duration' => 12.87
        ],
        'mid_roll_ad' => [
            'count' => 10,
            'duration' => 43.6
        ],
        'post_roll_ad' => [
            'count' => 3,
            'duration' => 10.27
        ],
        'marker_based_ad' => [
            'count' => 15,
            'duration' => 64.47
        ],
        'pattern_based_ad' => [
            'count' => 0,
            'duration' => 0
        ],
        'duration_based_ad' => [
            'count' => 1,
            'duration' => 2.27
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 12.32,
        'attention_grab_score' => 17,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 30
    ],
    'marker_stats' => [
        'discontinuity_count' => 15,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-02 14:51:35',
    'analysis_stats' => [
        'totalSegments' => 138,
        'adSegments' => 17,
        'contentSegments' => 121,
        'totalDuration' => 547.2,
        'adDuration' => 66.73,
        'contentDuration' => 480.47,
        'adPercentage' => 12.2,
        'discontinuityCount' => 15,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 2,
        'adClusters' => 16,
        'confidence' => 97
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 5.567,
                        'title' => '',
                        'uri' => '83ec1b8e786000000.ts',
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
                        'uri' => '83ec1b8e786000001.ts',
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
                        'uri' => '83ec1b8e786000002.ts',
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
                        'uri' => '83ec1b8e786000003.ts',
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
                        'uri' => '83ec1b8e786000004.ts',
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
                        'uri' => '83ec1b8e786000005.ts',
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
                        'uri' => '83ec1b8e786000006.ts',
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
                        'uri' => '83ec1b8e786000007.ts',
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
                        'uri' => '83ec1b8e786000008.ts',
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
                        'duration' => 3.3,
                        'title' => '',
                        'uri' => '83ec1b8e786000009.ts',
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
                            'name' => 'pre-roll-position',
                            'description' => '位于视频开头，可能是前贴片广告',
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
                        'duration' => 3.867,
                        'title' => '',
                        'uri' => '83ec1b8e786000010.ts',
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
                        'uri' => '83ec1b8e786000011.ts',
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
                        'uri' => '83ec1b8e786000012.ts',
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
                        'uri' => '83ec1b8e786000013.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000014.ts',
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
                        'uri' => '83ec1b8e786000015.ts',
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
                        'uri' => '83ec1b8e786000016.ts',
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
                        'uri' => '83ec1b8e786000017.ts',
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
                        'uri' => '83ec1b8e786000018.ts',
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
                        'uri' => '83ec1b8e786000019.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000020.ts',
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
                        'uri' => '83ec1b8e786000021.ts',
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
                        'uri' => '83ec1b8e786000022.ts',
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
                        'uri' => '83ec1b8e786000023.ts',
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
                        'uri' => '83ec1b8e786000024.ts',
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
                        'uri' => '83ec1b8e786000025.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000026.ts',
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
                        'uri' => '83ec1b8e786000027.ts',
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
                        'uri' => '83ec1b8e786000028.ts',
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
                        'uri' => '83ec1b8e786000029.ts',
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
                        'uri' => '83ec1b8e786000030.ts',
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
                        'uri' => '83ec1b8e786000031.ts',
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
                        'uri' => '83ec1b8e786000032.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000033.ts',
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
                        'uri' => '83ec1b8e786000034.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000035.ts',
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
                        'uri' => '83ec1b8e786000036.ts',
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
                        'uri' => '83ec1b8e786000037.ts',
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
                        'uri' => '83ec1b8e786000038.ts',
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
                        'uri' => '83ec1b8e786000039.ts',
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
                        'uri' => '83ec1b8e786000040.ts',
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
                        'uri' => '83ec1b8e786000041.ts',
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
                        'uri' => '83ec1b8e786000042.ts',
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
                        'uri' => '83ec1b8e786000043.ts',
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
                        'uri' => '83ec1b8e786000044.ts',
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
                        'uri' => '83ec1b8e786000045.ts',
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
                        'uri' => '83ec1b8e786000046.ts',
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
                        'duration' => 3.867,
                        'title' => '',
                        'uri' => '83ec1b8e786000047.ts',
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
                        'uri' => '83ec1b8e786000048.ts',
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
                        'uri' => '83ec1b8e786000049.ts',
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
                        'uri' => '83ec1b8e786000050.ts',
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
                        'uri' => '83ec1b8e786000051.ts',
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
                        'uri' => '83ec1b8e786000052.ts',
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
                        'uri' => '83ec1b8e786000053.ts',
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
                        'uri' => '83ec1b8e786000054.ts',
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
                        'uri' => '83ec1b8e786000055.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000056.ts',
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
                        'uri' => '83ec1b8e786000057.ts',
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
                        'uri' => '83ec1b8e786000058.ts',
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
                        'uri' => '83ec1b8e786000059.ts',
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
                        'uri' => '83ec1b8e786000060.ts',
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
                        'uri' => '83ec1b8e786000061.ts',
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
                        'uri' => '83ec1b8e786000062.ts',
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
                        'uri' => '83ec1b8e786000063.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000064.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000065.ts',
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
                        'uri' => '83ec1b8e786000066.ts',
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
                        'uri' => '83ec1b8e786000067.ts',
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
                        'uri' => '83ec1b8e786000068.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000069.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000070.ts',
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
                        'uri' => '83ec1b8e786000071.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000072.ts',
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
                        'uri' => '83ec1b8e786000073.ts',
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
                        'uri' => '83ec1b8e7860304131.ts',
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
                        'uri' => '83ec1b8e7860304132.ts',
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
                        'uri' => '83ec1b8e7860304133.ts',
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
                        'uri' => '83ec1b8e7860304134.ts',
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
                        'uri' => '83ec1b8e7860304135.ts',
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
                        'uri' => '83ec1b8e7860304136.ts',
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
                        'uri' => '83ec1b8e7860304137.ts',
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
                        'uri' => '83ec1b8e786000074.ts',
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
                        'uri' => '83ec1b8e786000075.ts',
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
                        'uri' => '83ec1b8e786000076.ts',
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
                        'uri' => '83ec1b8e786000077.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000078.ts',
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
                        'uri' => '83ec1b8e786000079.ts',
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
                        'uri' => '83ec1b8e786000080.ts',
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
                        'uri' => '83ec1b8e786000081.ts',
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
                        'uri' => '83ec1b8e786000082.ts',
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
                        'uri' => '83ec1b8e786000083.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000084.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000085.ts',
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
                        'uri' => '83ec1b8e786000086.ts',
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
                        'uri' => '83ec1b8e786000087.ts',
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
                        'uri' => '83ec1b8e786000088.ts',
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
                        'uri' => '83ec1b8e786000089.ts',
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
                        'uri' => '83ec1b8e786000090.ts',
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
                        'uri' => '83ec1b8e786000091.ts',
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
                        'duration' => 3.567,
                        'title' => '',
                        'uri' => '83ec1b8e786000092.ts',
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
                        'duration' => 7.6,
                        'title' => '',
                        'uri' => '83ec1b8e786000093.ts',
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
                        'uri' => '83ec1b8e786000094.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000095.ts',
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
                        'uri' => '83ec1b8e786000096.ts',
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
                        'uri' => '83ec1b8e786000097.ts',
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
                        'uri' => '83ec1b8e786000098.ts',
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
                        'uri' => '83ec1b8e786000099.ts',
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
                        'uri' => '83ec1b8e786000100.ts',
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
                        'uri' => '83ec1b8e786000101.ts',
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
                        'uri' => '83ec1b8e786000102.ts',
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
                        'uri' => '83ec1b8e786000103.ts',
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
                        'uri' => '83ec1b8e786000104.ts',
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
                        'uri' => '83ec1b8e786000105.ts',
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
                        'uri' => '83ec1b8e786000106.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000107.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000108.ts',
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
                        'uri' => '83ec1b8e786000109.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000110.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000111.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000112.ts',
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
                        'uri' => '83ec1b8e786000113.ts',
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
                        'uri' => '83ec1b8e786000114.ts',
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
                        'uri' => '83ec1b8e786000115.ts',
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
                        'duration' => 3.167,
                        'title' => '',
                        'uri' => '83ec1b8e786000116.ts',
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
                        'uri' => '83ec1b8e786000117.ts',
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
                        'uri' => '83ec1b8e786000118.ts',
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
                        'uri' => '83ec1b8e786000119.ts',
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
                        'uri' => '83ec1b8e786000120.ts',
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
                        'uri' => '83ec1b8e786000121.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 128,
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
                        'uri' => '83ec1b8e786000122.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 129,
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
                        'uri' => '83ec1b8e786000123.ts',
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
                        'duration' => 4,
                        'title' => '',
                        'uri' => '83ec1b8e786000124.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 131,
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
                        'uri' => '83ec1b8e786000125.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 132,
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
                        'uri' => '83ec1b8e786000126.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 133,
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
                        'uri' => '83ec1b8e786000127.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 134,
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
                        'uri' => '83ec1b8e786000128.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 135,
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
                        'duration' => 1.6,
                        'title' => '',
                        'uri' => '83ec1b8e786000129.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 136,
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
                        'duration' => 0.667,
                        'title' => '',
                        'uri' => '83ec1b8e786000130.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 137,
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
            'totalCount' => 138,
            'adCount' => 17,
            'contentCount' => 121,
            'totalDuration' => 547.2,
            'adDuration' => 66.73,
            'contentDuration' => 480.47,
            'adPercentage' => 12.2,
            'discontinuityCount' => 15,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'sequenceJumps' => [
                [
                    'index' => 74,
                    'prevSeq' => 786000073,
                    'currentSeq' => 7860304131,
                    'jump' => 7074304058,
                    'prevUri' => '83ec1b8e786000073.ts',
                    'currentUri' => '83ec1b8e7860304131.ts'
                ],
                [
                    'index' => 81,
                    'prevSeq' => 7860304137,
                    'currentSeq' => 786000074,
                    'jump' => -7074304063,
                    'prevUri' => '83ec1b8e7860304137.ts',
                    'currentUri' => '83ec1b8e786000074.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 0.667,
                'max' => 7.6,
                'avg' => 3.965231884058,
                'buckets' => [
                    '0.6' => 1,
                    '1.6' => 1,
                    2 => 1,
                    '3.1' => 1,
                    '3.3' => 1,
                    '3.5' => 1,
                    '3.8' => 2,
                    4 => 128,
                    '5.5' => 1,
                    '7.6' => 1
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
                    'start' => 136,
                    'end' => 137,
                    'count' => 2
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
                    'found' => false,
                    'count' => 0,
                    'points' => []
                ],
                'post_roll' => [
                    'found' => true,
                    'start_index' => 136,
                    'end_index' => 137,
                    'duration' => 2.27,
                    'segment_count' => 2
                ]
            ],
            'adTypes' => [
                'pre_roll_ad' => [
                    'count' => 3,
                    'duration' => 12.87
                ],
                'mid_roll_ad' => [
                    'count' => 10,
                    'duration' => 43.6
                ],
                'post_roll_ad' => [
                    'count' => 3,
                    'duration' => 10.27
                ],
                'marker_based_ad' => [
                    'count' => 15,
                    'duration' => 64.47
                ],
                'pattern_based_ad' => [
                    'count' => 0,
                    'duration' => 0
                ],
                'duration_based_ad' => [
                    'count' => 1,
                    'duration' => 2.27
                ]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => '频繁插播',
                'ad_density' => 12.32,
                'attention_grab_score' => 17,
                'frequency_score' => 100,
                'user_experience_impact' => '严重',
                'watchability_score' => 30
            ],
            'confidence' => 97
        ]
    ],
    'last_learn_date' => '2026-07-02 14:51:35'
];
