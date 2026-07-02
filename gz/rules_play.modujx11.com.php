<?php
/**
 * play.modujx11.com 域名广告和插播规则
 * 自动生成于: 2026-07-02 14:52:40
 */

return [
    'domain' => 'play.modujx11.com',
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
    'confidence_score' => 94,
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
            'start_index' => 736,
            'end_index' => 740,
            'duration' => 7.97,
            'segment_count' => 5
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 10,
            'duration' => 20.27
        ],
        'mid_roll_ad' => [
            'count' => 30,
            'duration' => 60.68
        ],
        'post_roll_ad' => [
            'count' => 7,
            'duration' => 23.1
        ],
        'marker_based_ad' => [
            'count' => 40,
            'duration' => 84.48
        ],
        'pattern_based_ad' => [
            'count' => 1,
            'duration' => 2.09
        ],
        'duration_based_ad' => [
            'count' => 15,
            'duration' => 32.4
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 7.15,
        'attention_grab_score' => 15,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 30
    ],
    'marker_stats' => [
        'discontinuity_count' => 40,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-07-02 14:52:40',
    'analysis_stats' => [
        'totalSegments' => 741,
        'adSegments' => 53,
        'contentSegments' => 688,
        'totalDuration' => 1482.54,
        'adDuration' => 104.04,
        'contentDuration' => 1378.5,
        'adPercentage' => 7.02,
        'discontinuityCount' => 40,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 15,
        'adClusters' => 47,
        'confidence' => 94
    ],
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 2.294,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ul5jXC02.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Yosd7tIG.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CYGms55U.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bG1oAWyX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/U4YOuxnF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gzP7acKB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rUag5EiW.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PJartLfS.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 7,
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
                        'duration' => 3.045,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AXMoYqRA.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sWNnSIHm.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3Zv8MBb2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z8Yfyv45.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2WAcyLA7.ts',
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
                        'duration' => 2.586,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TCGrpHEv.ts',
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
                        'duration' => 2.336,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZHLtyCqq.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fOLq6k0A.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EMTHbBk4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SRwhyQ52.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/n0dDQRvw.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/M7jDmPza.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Xyhv0UOd.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 20,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yEll7c7U.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iEtXT50L.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/W5pLCmMq.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 23,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/h9AeJgBs.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sk31hRK2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rBs6rzMB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KnEYhp8z.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OKcvBMyC.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8QZebLA4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8Pi7YEoB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VfRbDUqc.ts',
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
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/V4FE4wus.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 32,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HPVLqkJI.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/i1HTNxsX.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iforb4iQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 35,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ErmXAEQB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ImLQf6x2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DLxuXZGi.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZmfIuchh.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/umz3n7Wa.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 40,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EXRFABcS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BlKRh7qY.ts',
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
                        'duration' => 1.627,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/u2KmFf1u.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 43,
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KUmEJOUy.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UFOeIAdr.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 45,
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
                        'duration' => 0.876,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CR0S1KZ8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 46,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MMRDNvAX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KV1yOjfH.ts',
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
                        'duration' => 2.544,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oiLGV9Z2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lrWwr9SX.ts',
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
                        'duration' => 1.21,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PVEQJasq.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 51,
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
                        'duration' => 1.126,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hlRUltIm.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 52,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/zgdtw3ae.ts',
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
                        'duration' => 3.545,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/R9QJO86K.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qRAT58ri.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JAJYQ6bz.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0KeGc16O.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 57,
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
                        'duration' => 1.376,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5nlW6g5W.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bIVDIvQW.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VnW9q8r3.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 60,
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
                        'duration' => 3.587,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kIeCqfhK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x975Zmvo.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sTZdQqM7.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/h1PsNpUL.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o2c9jbpd.ts',
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/l9OtPlyl.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 66,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0SCQtvm0.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GoKpWzyJ.ts',
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
                        'duration' => 0.334,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/c4UYpwMn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 69,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/trXzT5z6.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/v4Vc07QR.ts',
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
                        'duration' => 2.753,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qHGR4Dpi.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/r4Y2JI0N.ts',
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
                        'duration' => 0.459,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/guGc8cQJ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 74,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oJZLWA6d.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dReCjNFS.ts',
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
                        'duration' => 2.669,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lTl2Ik0p.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/apq4JUKI.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o5dSszwY.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/k27vx46x.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 80,
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
                        'duration' => 2.669,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/W1FIUM0X.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MH7njk03.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QRUxqHfj.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 83,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vuu6HDUj.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YqPb7vbY.ts',
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
                        'duration' => 0.959,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ThUrbV40.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 86,
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
                        'duration' => 3.333,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/00r6WSPR.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 87,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Yxd8zR8N.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 88,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Skv6RZc8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 89,
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
                        'duration' => 2.933,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/8a34XVZm.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 90,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/0WIqCGfP.ts',
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Vksc9WF9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 92,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/WgTtn6kY.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 93,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/jghAWpf5.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 94,
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
                        'duration' => 1.3,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/qhFKB2uD.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 95,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TPjIw9lU.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 96,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HTpWpK7K.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OBWdhrqq.ts',
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
                        'duration' => 1.627,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/W78OwsYQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 99,
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
                        'duration' => 3.962,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VTZ6dhwk.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 100,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iOi5I214.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xZ2v04Iu.ts',
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
                        'duration' => 1.168,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/URe4yeJa.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 103,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HanGNTDk.ts',
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
                        'duration' => 2.294,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KbSnWHRr.ts',
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
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZYLYqe8f.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 106,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AZVEwDKT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wIfNpawz.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SFGmGeql.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oZiPesee.ts',
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NFsdW3dN.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 111,
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WoJRL4Zp.ts',
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
                        'duration' => 2.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Bpv7o71q.ts',
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
                        'duration' => 2.336,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Pe2apFVF.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4kiAlyBK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/7RlSZAAk.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lb0ZILGj.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VQKufJDA.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wqFKfWqt.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8x9f1QUy.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PiEp7DJs.ts',
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
                        'duration' => 1.71,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QJeifNgW.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 122,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/R6I8iNIb.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YcbwlsmZ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eR7AUXiW.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/cskpw0QP.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XotEpYMY.ts',
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
                        'duration' => 0.375,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YqISZMwE.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 128,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QPhwQxzF.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 129,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/D9EF8gZo.ts',
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
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TzHuQjz9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 131,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/blA1XRdE.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MYHKxW0j.ts',
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
                        'duration' => 3.545,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KRrF1Vi8.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OvnDAlcf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1R6YcWvo.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dPRDRYUI.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 137,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wgp4IDRX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/a19bEf1p.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/78jz1FoZ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 140,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pkArmhHY.ts',
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
                        'duration' => 2.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/z14164KI.ts',
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
                        'duration' => 0.417,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/A5dGsB9k.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 143,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fNxcCa9K.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x9spL60d.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 145,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Ewllz1mm.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SEm5K0F8.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mS6DBj0R.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uaML2ySc.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 149,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Yhryz2t9.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HwBvKBMt.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 151,
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/7GjRULte.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 152,
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
                        'duration' => 3.212,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3SZMdSL4.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pet4wnWu.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 154,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XBbJDWgX.ts',
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
                        'duration' => 0.918,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DYgyirVe.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TCJ3TWrO.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NMWzMhvc.ts',
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
                        'duration' => 2.461,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/26mth6Ed.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vLRfKDvJ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vlDXaJOO.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/deg9i5JT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BRQHKlEl.ts',
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
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bxiETokk.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 164,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4j5l9SF8.ts',
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
                        'duration' => 0.918,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/E8euD8rL.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5X6R5jdS.ts',
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
                        'duration' => 1.126,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JvzVMCjo.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 168,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8LcADgAL.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 169,
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
                        'keyword' => 50,
                        'marker' => 80,
                        'cluster' => 70
                    ],
                    'totalWeight' => 200
                ],
                [
                    'segment' => [
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bQwI0ArF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bWBQ6hDg.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EFaDIUhI.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 172,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/u3ISMFuK.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/q1gjMbzj.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 174,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KYanzD7e.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gBm4Afdf.ts',
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
                        'duration' => 1.126,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2VgoExS4.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 177,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PuVzCKrl.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WXCCDER7.ts',
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
                        'duration' => 3.086,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/R1IyIMNc.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VrMNWr4Q.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5SCtVZsv.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3nzE8y7i.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qeJmnmdH.ts',
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
                        'duration' => 0.792,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1nMQlgos.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 185,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/irOqMW9P.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lMZl9u3z.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 187,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lnPu8ke4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/a7oAbA3J.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 189,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QzGCTR4U.ts',
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
                        'duration' => 2.836,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QA3bQWcR.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/du6h7UVD.ts',
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
                        'duration' => 2.669,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FeAGJttj.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JbKIFk5y.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/9ip5quPo.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GFxtP73x.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 196,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/owDBUM9s.ts',
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0yXcjHav.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 198,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jH6shLHk.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yMCTeXJu.ts',
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
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/O4ZFCkSi.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 201,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/G7yGHKBl.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZeMFbE1i.ts',
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
                        'duration' => 2.878,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8tp8Ux3E.ts',
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
                        'duration' => 1.376,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/21DTz9cy.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 205,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iYpY3VM7.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hv72xgiS.ts',
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
                        'duration' => 1.71,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wjMbBWAF.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 208,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/L4gnfDJ0.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 209,
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
                        'duration' => 1.877,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GmXDO7NP.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 210,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oVWOmkBB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/aE7gWitz.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pXwZf82S.ts',
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
                        'duration' => 1.001,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BBQ176aJ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 214,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/I4vlA96u.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uwLbxloF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LcYkvVCI.ts',
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
                        'duration' => 2.961,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/zUsK4Ruc.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GIPtIJsG.ts',
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
                        'duration' => 1.585,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LwbUS6kF.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 220,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EO1IA9nN.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KKHg27o5.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/avbvreAI.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jLdoRSeR.ts',
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
                        'duration' => 1.001,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fDqQ2SHE.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 225,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sh0Ne0yE.ts',
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
                        'duration' => 3.045,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yYLsn6bT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZSuVKnxn.ts',
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
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Ct4XcaC9.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 229,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/maBURuAZ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dXp7A1iW.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LaZIJjxg.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6BVU5knC.ts',
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
                        'duration' => 0.959,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/44EfmKzy.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 234,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iHXrPZfC.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dKlk6W65.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LCbpcJzh.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FFHWnFmL.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 238,
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
                        'duration' => 2.669,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0aRRmSyV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gmmL8qs6.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UCKURrQu.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qMQB3gAH.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IucfdyWD.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AY55Bcg5.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 244,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3JC0sd8J.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SLtgRtup.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 246,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Bck5WIWY.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lZeozEzY.ts',
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
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/c2N3BySc.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 249,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x7BJ1bki.ts',
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
                        'duration' => 0.417,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Pc9sm66J.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 251,
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JiHOP3ia.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 252,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bLJviniU.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/zRP0TcH7.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4948MeeP.ts',
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
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dusOYwBa.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 256,
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
                        'duration' => 3.462,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2gY18cO1.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3YrKjhzV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MzO54XLw.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8zlDVJMf.ts',
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
                        'duration' => 1.293,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5R8QSmzI.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 261,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gAD4VqqT.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 262,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'keyword-match',
                            'description' => '标题或文件名包含广告关键词',
                            'weight' => 50,
                            'category' => 'keyword'
                        ],
                        [
                            'name' => 'filename-pattern',
                            'description' => '文件名匹配广告命名模式',
                            'weight' => 60,
                            'category' => 'pattern'
                        ]
                    ],
                    'confidence' => 100,
                    'categories' => [
                        'keyword' => 50,
                        'pattern' => 60
                    ],
                    'totalWeight' => 110
                ],
                [
                    'segment' => [
                        'duration' => 1.168,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6wptv8TI.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 263,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qw0JmW9p.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rgGdSn4P.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pvv1Lt2H.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o26owCc5.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ozIQHiJc.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/joDMBSiv.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 269,
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qQNHuMEF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o31tyjtI.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LNqYCqaf.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 272,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ppB6JQlb.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5aL4z2gj.ts',
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
                        'duration' => 0.25,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/163GSWDQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 275,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dDelv1YD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VAURrcod.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iOlSWy7o.ts',
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
                        'duration' => 3.295,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/zMaBVGHW.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/z8FZaH4j.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3n3RxaiQ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/s0SD1Fsm.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/a7dRjtja.ts',
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
                        'duration' => 0.584,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z9vlryXW.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3VvAPZnl.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bA4l7CzO.ts',
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
                        'duration' => 3.253,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QxjRKOTI.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MYjXaTaY.ts',
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
                        'duration' => 0.918,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/T5UtxsW8.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 289,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Jfz4YvmT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LXqGtN9i.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KsJEUJQH.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OwuA8LYY.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/w6UQ29dR.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 294,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qFQIFLLi.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rlf5ObE4.ts',
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
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/M1IUkhPi.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 297,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uFS9aYr4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TfyBm3LX.ts',
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
                        'duration' => 3.545,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5laOrYTB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vc6j7kdo.ts',
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
                        'duration' => 0.834,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Fd9S3YmL.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 302,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/is8vi8SE.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JFIX0Vo1.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kKMlhRjb.ts',
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
                        'duration' => 2.961,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ehMHd3T3.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UbZzXBa1.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 307,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NBsCVMuV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/cNulX0mV.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 309,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EmqSlu3W.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 310,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0sIThXc4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Xazr1u49.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AnzFKZHX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dibajYY2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NWeEreCh.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fTEZ9sr5.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ztxB7LhV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Wzgdsa6b.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/30dCPRda.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6s4nCfXj.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PLVlxpVD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ErdxgPcD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/F4PFR2uO.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uMHsEGpR.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Dhtb6cs3.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OiNFqEzZ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/22mI7qIc.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o4thwnEe.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/idv6eIwU.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 329,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GowG69DD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Qit4PMLL.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZtYYFiFZ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/z7PBu8WH.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/63ZK6l8f.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Zdm6Bxlu.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KMthmXuf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1iAGsUOS.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dDhFxxAb.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 338,
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
                        'duration' => 0.25,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GuPM0pVL.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 339,
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
                        'duration' => 0.25,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4RZocrtv.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 340,
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
                        'duration' => 3.086,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yUi9SSKM.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IUZv5u7n.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3KQna8xh.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/nHVocEcB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lFo9NDMD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Hh7QhG9T.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5WiA78jn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 347,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rgVFY1OI.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/J3Pf9FJe.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 349,
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
                        'duration' => 3.212,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AKNp76z1.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2X7oAcbT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/w1mqPFuq.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/e92HbW7b.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lgemTexi.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3DGJx5vl.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/12kln272.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Kj9puS2j.ts',
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
                        'duration' => 1.585,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1GJy2WlQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 358,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/r3GZPv8C.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Fv2L0a2w.ts',
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
                        'duration' => 0.5,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Pa7X9st4.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 361,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/cXNq5wBN.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vHsVLpVp.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xHaHPmwm.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/43uPYa18.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eWwLm9gB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/aOt9QmsY.ts',
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
                        'duration' => 2.377,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/J863bqyO.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XLtWscxG.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 369,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eFfTganQ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oGKblAAB.ts',
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6hvpRHrY.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 372,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z24fb0au.ts',
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
                        'duration' => 1.627,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5LE2xafQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 374,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TeUdauY5.ts',
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
                        'duration' => 0.876,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0KKHhENB.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 376,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/7bDUWCSe.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/w40bfkdB.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WUmnb6ey.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/C7Xrn0Ua.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/77gsT7eK.ts',
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
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/C68yNnWv.ts',
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
                        'duration' => 1.627,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JCIfiZYH.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 383,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jUltAeoK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IoCaGkuj.ts',
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
                        'duration' => 0.834,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CdxwET59.ts',
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
                        'duration' => 3.378,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hG7YNS5W.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/m3LsTUGw.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VZelc871.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 389,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Ud4HZCN0.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3NrABDRf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mUJfSZNS.ts',
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
                        'duration' => 1.21,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DyMnky0P.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 393,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XKTfgbRM.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Rai5XWPl.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LfzOjJ7A.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NmzzNH1A.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XtHKkscJ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Nsg8FcLd.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Xjrghx4n.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IQrfYtbh.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QJGdFYvn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 402,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2ODKFpHi.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/RbtWjeex.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 404,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GjOY8pn7.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JarUa4vH.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 406,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TsrkOXKH.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/35lgbNt4.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 408,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rjnVHnq6.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 409,
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XkluXeAm.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 410,
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
                        'duration' => 2.461,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ONL7BeRD.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8Z99oPKK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DSHrv2z2.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5xhP5KgP.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0DZD50SA.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fDw88gOa.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 416,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ox7B9dsz.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Pzt5O0fT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/40dTy3dx.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/n2i6LiIU.ts',
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
                        'duration' => 0.542,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YnIlIqoc.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 421,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pdlPu6X5.ts',
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
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8jRulkrz.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yT9BuiSy.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KSWUBJlN.ts',
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
                        'duration' => 0.209,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/tpTsOPud.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 426,
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
                        'duration' => 1.877,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mR660SUl.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 427,
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
                        'duration' => 2.002,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/djKy9wQR.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MZUQz6S2.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 429,
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2vts5Vrw.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 430,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/URaNP9PB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8HNRfajX.ts',
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
                        'duration' => 3.333,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/00r6WSPR.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 433,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Yxd8zR8N.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 434,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Skv6RZc8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 435,
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
                        'duration' => 2.933,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/8a34XVZm.ts',
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/0WIqCGfP.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 437,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Vksc9WF9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 438,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/WgTtn6kY.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 439,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/jghAWpf5.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 440,
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
                        'duration' => 1.3,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/qhFKB2uD.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 441,
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
                        'duration' => 2.169,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uIvUZqrf.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 442,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/40yeT4W5.ts',
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
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DFyJ4iZT.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WelR0Ayw.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BH8tZ8yt.ts',
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
                        'duration' => 1.21,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oIDRg2Ru.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 447,
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
                        'duration' => 1.21,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mwtYbWYj.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 448,
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
                        'duration' => 2.377,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/c51f5QhU.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MOmPE7H6.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 450,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JyjAMSMJ.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YQakgFBv.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 452,
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/o1rWMVpG.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 453,
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
                        'duration' => 1.71,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WjUcHeSr.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 454,
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
                        'duration' => 3.795,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IdjRGptj.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wIg13P3r.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 456,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4LZmVQqg.ts',
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
                        'duration' => 0.876,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fHxZYxcM.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 458,
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KKZaiY6a.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 459,
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
                        'duration' => 3.003,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iC6IufIs.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 460,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TAPSydrT.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 461,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z7PcTwGK.ts',
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
                        'duration' => 3.212,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/tJp6VHJn.ts',
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
                        'duration' => 0.834,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x6LnZyjO.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 464,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TI2OZk3N.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6w3FWZCC.ts',
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
                        'duration' => 2.461,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bE2Vocta.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YUCvGHs6.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gjRNzAX1.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1OaWlxn8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 470,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.585,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hVbToXrL.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 471,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sDfJrG2T.ts',
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XHTKyHrh.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 473,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/y43w0HZ5.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fzdJFqum.ts',
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
                        'duration' => 2.169,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6QBwXMqf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/cCYsbp8R.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4iloTtzV.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 478,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/9iqLmqJA.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ed2Lr3TJ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 480,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ynJyHpi2.ts',
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
                        'duration' => 1.126,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DYQ4uBCJ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 482,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/zc5nAOcX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Lg2G1w53.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vC5s6Qko.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pTsL9kfQ.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/11TNttE6.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 487,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ySQI2JCV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/c51jWbt8.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QCbPlvyN.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 490,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dHR9BLKi.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/62RcOI1l.ts',
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
                        'duration' => 1.168,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FfWDqeK4.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 493,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/890yOjWh.ts',
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
                        'duration' => 2.753,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qPhmyM2Z.ts',
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
                        'duration' => 1.293,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xPfnxdmA.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 496,
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BtgPTkFm.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 497,
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
                        'duration' => 2.336,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XOUNVyhy.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 498,
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
                        'duration' => 2.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/47IeNFbm.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SoPKrixz.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 500,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hFEHQduO.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 501,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FWoZ13vU.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vOIhfZaX.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/9WlYZtrA.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 504,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/uOsptqu3.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x0RzZ7AQ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/J5LwpiBt.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ACEEU1S3.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 508,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IHJeajof.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iBWw1wzc.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 510,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WXnD2ZV8.ts',
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
                        'duration' => 1.627,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IZw4cwxZ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 512,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VG9pX8Zp.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OYdsLH9y.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 514,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0bCRxepW.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ow98zY3m.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 516,
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
                        'duration' => 2.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vUrQQflr.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 517,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eZQlonlG.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 518,
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ARMxZ6jd.ts',
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
                        'duration' => 2.377,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kRm11upH.ts',
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8I1VOJn9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 521,
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
                        'duration' => 2.628,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4gpCX7aJ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AZZknIQ8.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TJaP0kbn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 524,
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
                        'duration' => 2.961,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0T2XY3so.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/woWf1rxq.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FP1hzKlJ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 527,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.5,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/399SCXIp.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 528,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BMzmguKD.ts',
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
                        'duration' => 3.67,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kRRs4iOT.ts',
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
                        'duration' => 1.71,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vQtlTC1w.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 531,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OqCJpvoB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jAN0DHfu.ts',
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
                        'duration' => 0.709,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XcfuY7Tq.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 534,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/tBIFObcF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vcVgJpz0.ts',
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
                        'duration' => 2.544,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jmJsMmvS.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 537,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/boiFT0Ip.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 538,
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CzJPSj3U.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 539,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/d1B5DnjJ.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/a3fgWigI.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 541,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MVJExJq8.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/aBlrUxaP.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yaX2luNN.ts',
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
                        'duration' => 2.836,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BmZBLRrR.ts',
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
                        'duration' => 0.584,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wMWfTlms.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 546,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/s61suUeg.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 547,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OVS6pCf2.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/U6dwEhaB.ts',
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
                        'duration' => 2.002,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DOL67K9D.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kNkEQYSr.ts',
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
                        'duration' => 2.586,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Of0w4hYO.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/k3mja2KG.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Y3phDL4h.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/z89wp4Y8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 555,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HmsmTe2E.ts',
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
                        'duration' => 3.212,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/nI2cqmlt.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 557,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.293,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rGIhYgb8.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 558,
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VTJCCRUs.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 559,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lSXyR9g8.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/aPW1bbG8.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dLhoGcA9.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ApB0mNDw.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Kjicp5xZ.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/QZTBFM6d.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 565,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3UuVtPur.ts',
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
                        'duration' => 1.168,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ni7LVTyg.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 567,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/z58srQyr.ts',
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
                        'duration' => 2.169,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hCPCV3Wb.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/15x3Qpa9.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/C67aYCDR.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 571,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jY8D7NAW.ts',
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
                        'duration' => 2.336,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Et3Q3Aqr.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qEzqqxTg.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 574,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KBMux3GC.ts',
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
                        'duration' => 2.961,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Y7Fxvwa6.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Q9lzfGbn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 577,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/m4w7er9a.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 578,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CTChMWzv.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UkXJqc1I.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 580,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/g72vTOQV.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hT9KqPBA.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/RtJXtlHF.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3r2XH1eC.ts',
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
                        'duration' => 2.586,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HGpcbkQr.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KLsfJCBS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/sLOPQ3il.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 587,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.626,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/vHgr8J9B.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 588,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/TIo84OKk.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/V78KsSL9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 590,
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
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GUeGv4uo.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 591,
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/MEzTkvPV.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 592,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NEeaxAOW.ts',
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
                        'duration' => 2.669,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yC64DJz9.ts',
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
                        'duration' => 1.543,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/w854an9i.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 595,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JZvsyYVa.ts',
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
                        'duration' => 2.419,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/FXuRgbmz.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 597,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.961,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ZHzjBpqs.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 598,
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0RnbbJrS.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 599,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/K93PSGjo.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LaoEAMaQ.ts',
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
                        'duration' => 2.002,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/CSE4vzoh.ts',
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
                        'duration' => 1.877,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/7KiYFJPq.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 603,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wFxpJTfl.ts',
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/oqnao0Py.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 605,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bZCChyU7.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KBrxHG6C.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 607,
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
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/70CSVjrf.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 608,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bRd3ilAD.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 609,
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
                        'duration' => 0.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IDNNN3Ft.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 610,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/bEgrl2Vv.ts',
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
                        'duration' => 1.919,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/N8vAzRhH.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 612,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qn87eNFw.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/cTbXp3So.ts',
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
                        'duration' => 3.17,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WusYHU0o.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jWT9KWC3.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1XDCibgo.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 617,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EFIsUVql.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 618,
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hazoJsJT.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 619,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YTXx2VfS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mzQgPs5l.ts',
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
                        'duration' => 1.877,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KWbhSAqP.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 622,
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
                        'duration' => 1.126,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OYIFpxil.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 623,
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/kjuvFblK.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 624,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3GJyZQ9N.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xjoDxz2i.ts',
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
                        'duration' => 2.002,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/O4mE5juG.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 627,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gEojDOnf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4JD2e4Vf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pO2k1Zjt.ts',
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
                        'duration' => 1.418,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/e2tmKuiF.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 631,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eVA8BqVS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ho7Str57.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fJ4zCIQ4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/tk7oX8Je.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8sklS62f.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GyC7xYUt.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 637,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/shpCXXyb.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 638,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6xTvRfMT.ts',
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
                        'duration' => 1.502,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/GxDxwCwz.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 640,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WOOs2vf0.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/dc08qe7G.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/IyGmmg6f.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WmtfCYZa.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PNFUJOOR.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/p6n5kxqO.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6z3rgk3i.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 647,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hK0b5pB1.ts',
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
                        'duration' => 0.834,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/nCe36Cu6.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 649,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ayM4agqQ.ts',
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
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VWt1MxTG.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1XFeICSs.ts',
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/NIpAlrnY.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 653,
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2TaRpFBi.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 654,
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
                        'duration' => 1.668,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EXaO3uO0.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 655,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/88hxesn1.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hs3YeJjE.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 657,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Dbox8rXQ.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 658,
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
                        'duration' => 1.752,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JmrDMtIF.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 659,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LpPLrzsB.ts',
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
                        'duration' => 3.879,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/H3oNXO7U.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/DKWtbl3r.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/JfQb7OoK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SkGSqEhf.ts',
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
                        'duration' => 1.793,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ItUQwjyx.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 665,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/lSnwu21f.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HTr89S1T.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 667,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2H1cUzAv.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 668,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LZqtsyVS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/d42tIaM0.ts',
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
                        'duration' => 0.834,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/6XiCLS5Q.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 671,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PEOgnYP2.ts',
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
                        'duration' => 2.044,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UmETIf6h.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 673,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.335,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WhiMYRwa.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 674,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AZLdAGV7.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mPKgDns0.ts',
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
                        'duration' => 2.586,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PKOjRai3.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 677,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 1.251,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4QYtyIN1.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 678,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/HDgVqNNG.ts',
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
                        'duration' => 3.503,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/8gmFzTl1.ts',
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
                        'duration' => 0.918,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/E8ydlHPU.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 681,
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
                        'duration' => 3.42,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/P3WMU5Hj.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/g4jJ6DNZ.ts',
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
                        'duration' => 1.001,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/inj0fz3L.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 684,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3XTrIIn7.ts',
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
                        'duration' => 2.461,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/KPjkFkja.ts',
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
                        'duration' => 0.417,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EaJjrcUs.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 687,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2RfjIsns.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/C1uCv6Up.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xreWJc2C.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/5OSebTCZ.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Yt8BZENS.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/02AXLTEX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/yFoExVcA.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/60VsttKf.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/WbGyU0YK.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2Sbysnen.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 697,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pqLKmD6L.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 698,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wwZeSTef.ts',
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/x22o3soG.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 700,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/42Xl7m6I.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/tbUQwDj4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/9seJYKXB.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VGtgQYA8.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/H4xz6wjX.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wsmmvovA.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 706,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qicmbpZP.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 707,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/an4ohfYd.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4R3tRber.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pN8WjUb0.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/BSHntppm.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/T0Zl7p0R.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/XkAipDfs.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/eGeJsKxs.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/iHyyyGrk.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 715,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PELRh2GW.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VSRt904n.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 717,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 0.959,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/jc3PPhpm.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 718,
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pPCLkKt0.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 719,
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/f9WKJgd4.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/7pP6ZSWj.ts',
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
                        'duration' => 1.084,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Or6e2RxT.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 722,
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
                        'duration' => 3.17,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/p3Xf9yTX.ts',
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
                        'duration' => 1.585,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/OU2jq1dl.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 724,
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
                        'duration' => 1.46,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/LhCNB7Jn.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 725,
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
                        'duration' => 1.835,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/qKSTkDxW.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 726,
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
                        'duration' => 2.336,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xICaq3KQ.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 727,
                    'isAd' => false,
                    'matchedRules' => [],
                    'confidence' => 0,
                    'categories' => [],
                    'totalWeight' => 0
                ],
                [
                    'segment' => [
                        'duration' => 3.337,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Cx37VKUr.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Ezk97X4v.ts',
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
                        'duration' => 2.085,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/wqpYz7r8.ts',
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
                        'duration' => 2.086,
                        'title' => '',
                        'uri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/xIFLxcKf.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 731,
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
                        'duration' => 3.333,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/00r6WSPR.ts',
                        'discontinuity' => true,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 732,
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
                            'name' => 'post-roll-position',
                            'description' => '位于视频结尾，可能是后贴片广告',
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Yxd8zR8N.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 733,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Skv6RZc8.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 734,
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
                        'duration' => 2.933,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/8a34XVZm.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 735,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/0WIqCGfP.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 736,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/Vksc9WF9.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 737,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/WgTtn6kY.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 738,
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
                        'duration' => 1.667,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/jghAWpf5.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 739,
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
                        'duration' => 1.3,
                        'title' => '',
                        'uri' => 'https://bf.modujx15.com/20260629/qyBLBThC/10155kb/hls/qhFKB2uD.ts',
                        'discontinuity' => false,
                        'adMarkers' => [],
                        'cueMarkers' => [],
                        'scte35' => ''
                    ],
                    'index' => 740,
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
            'totalCount' => 741,
            'adCount' => 53,
            'contentCount' => 688,
            'totalDuration' => 1482.54,
            'adDuration' => 104.04,
            'contentDuration' => 1378.5,
            'adPercentage' => 7.02,
            'discontinuityCount' => 40,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'sequenceJumps' => [
                [
                    'index' => 11,
                    'prevSeq' => 2,
                    'currentSeq' => 45,
                    'jump' => 43,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/3Zv8MBb2.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z8Yfyv45.ts'
                ],
                [
                    'index' => 12,
                    'prevSeq' => 45,
                    'currentSeq' => 7,
                    'jump' => -38,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Z8Yfyv45.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/2WAcyLA7.ts'
                ],
                [
                    'index' => 17,
                    'prevSeq' => 4,
                    'currentSeq' => 52,
                    'jump' => 48,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/EMTHbBk4.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/SRwhyQ52.ts'
                ],
                [
                    'index' => 307,
                    'prevSeq' => 3,
                    'currentSeq' => 1,
                    'jump' => -2,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ehMHd3T3.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/UbZzXBa1.ts'
                ],
                [
                    'index' => 312,
                    'prevSeq' => 4,
                    'currentSeq' => 49,
                    'jump' => 45,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/0sIThXc4.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Xazr1u49.ts'
                ],
                [
                    'index' => 390,
                    'prevSeq' => 871,
                    'currentSeq' => 0,
                    'jump' => -871,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/VZelc871.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/Ud4HZCN0.ts'
                ],
                [
                    'index' => 409,
                    'prevSeq' => 4,
                    'currentSeq' => 6,
                    'jump' => 2,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/35lgbNt4.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/rjnVHnq6.ts'
                ],
                [
                    'index' => 469,
                    'prevSeq' => 6,
                    'currentSeq' => 1,
                    'jump' => -5,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/YUCvGHs6.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gjRNzAX1.ts'
                ],
                [
                    'index' => 470,
                    'prevSeq' => 1,
                    'currentSeq' => 8,
                    'jump' => 7,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/gjRNzAX1.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/1OaWlxn8.ts'
                ],
                [
                    'index' => 634,
                    'prevSeq' => 57,
                    'currentSeq' => 4,
                    'jump' => -53,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/ho7Str57.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/fJ4zCIQ4.ts'
                ],
                [
                    'index' => 649,
                    'prevSeq' => 1,
                    'currentSeq' => 6,
                    'jump' => 5,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/hK0b5pB1.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/nCe36Cu6.ts'
                ],
                [
                    'index' => 676,
                    'prevSeq' => 7,
                    'currentSeq' => 0,
                    'jump' => -7,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/AZLdAGV7.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mPKgDns0.ts'
                ],
                [
                    'index' => 677,
                    'prevSeq' => 0,
                    'currentSeq' => 3,
                    'jump' => 3,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/mPKgDns0.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PKOjRai3.ts'
                ],
                [
                    'index' => 678,
                    'prevSeq' => 3,
                    'currentSeq' => 1,
                    'jump' => -2,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/PKOjRai3.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/4QYtyIN1.ts'
                ],
                [
                    'index' => 720,
                    'prevSeq' => 0,
                    'currentSeq' => 4,
                    'jump' => 4,
                    'prevUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/pPCLkKt0.ts',
                    'currentUri' => 'https://bf.modujx11.com/20260702/CUAq6w5D/3083kb/hls/f9WKJgd4.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 0.209,
                'max' => 3.962,
                'avg' => 2.0007233468286,
                'buckets' => [
                    '0.2' => 4,
                    '0.3' => 2,
                    '0.4' => 4,
                    '0.5' => 5,
                    '0.6' => 2,
                    '0.7' => 2,
                    '0.8' => 8,
                    '0.9' => 7,
                    1 => 12,
                    '1.1' => 9,
                    '1.2' => 8,
                    '1.3' => 12,
                    '1.4' => 13,
                    '1.5' => 26,
                    '1.6' => 30,
                    '1.7' => 20,
                    '1.8' => 14,
                    '1.9' => 16,
                    2 => 455,
                    '2.1' => 3,
                    '2.2' => 2,
                    '2.3' => 8,
                    '2.4' => 11,
                    '2.5' => 10,
                    '2.6' => 6,
                    '2.7' => 2,
                    '2.8' => 3,
                    '2.9' => 13,
                    3 => 10,
                    '3.1' => 2,
                    '3.2' => 6,
                    '3.3' => 5,
                    '3.4' => 2,
                    '3.5' => 5,
                    '3.6' => 1,
                    '3.7' => 1,
                    '3.8' => 1,
                    '3.9' => 1
                ]
            ],
            'adClusters' => [
                [
                    'start' => 7,
                    'end' => 7,
                    'count' => 1
                ],
                [
                    'start' => 9,
                    'end' => 9,
                    'count' => 1
                ],
                [
                    'start' => 20,
                    'end' => 20,
                    'count' => 1
                ],
                [
                    'start' => 40,
                    'end' => 40,
                    'count' => 1
                ],
                [
                    'start' => 45,
                    'end' => 45,
                    'count' => 1
                ],
                [
                    'start' => 60,
                    'end' => 60,
                    'count' => 1
                ],
                [
                    'start' => 80,
                    'end' => 80,
                    'count' => 1
                ],
                [
                    'start' => 87,
                    'end' => 87,
                    'count' => 1
                ],
                [
                    'start' => 96,
                    'end' => 96,
                    'count' => 1
                ],
                [
                    'start' => 109,
                    'end' => 109,
                    'count' => 1
                ],
                [
                    'start' => 129,
                    'end' => 129,
                    'count' => 1
                ],
                [
                    'start' => 149,
                    'end' => 149,
                    'count' => 1
                ],
                [
                    'start' => 169,
                    'end' => 169,
                    'count' => 1
                ],
                [
                    'start' => 172,
                    'end' => 172,
                    'count' => 1
                ],
                [
                    'start' => 189,
                    'end' => 189,
                    'count' => 1
                ],
                [
                    'start' => 209,
                    'end' => 209,
                    'count' => 1
                ],
                [
                    'start' => 229,
                    'end' => 229,
                    'count' => 1
                ],
                [
                    'start' => 249,
                    'end' => 249,
                    'count' => 1
                ],
                [
                    'start' => 262,
                    'end' => 262,
                    'count' => 1
                ],
                [
                    'start' => 269,
                    'end' => 269,
                    'count' => 1
                ],
                [
                    'start' => 289,
                    'end' => 289,
                    'count' => 1
                ],
                [
                    'start' => 309,
                    'end' => 309,
                    'count' => 1
                ],
                [
                    'start' => 329,
                    'end' => 329,
                    'count' => 1
                ],
                [
                    'start' => 349,
                    'end' => 349,
                    'count' => 1
                ],
                [
                    'start' => 369,
                    'end' => 369,
                    'count' => 1
                ],
                [
                    'start' => 389,
                    'end' => 389,
                    'count' => 1
                ],
                [
                    'start' => 409,
                    'end' => 409,
                    'count' => 1
                ],
                [
                    'start' => 429,
                    'end' => 429,
                    'count' => 1
                ],
                [
                    'start' => 433,
                    'end' => 433,
                    'count' => 1
                ],
                [
                    'start' => 442,
                    'end' => 442,
                    'count' => 1
                ],
                [
                    'start' => 458,
                    'end' => 458,
                    'count' => 1
                ],
                [
                    'start' => 478,
                    'end' => 478,
                    'count' => 1
                ],
                [
                    'start' => 498,
                    'end' => 498,
                    'count' => 1
                ],
                [
                    'start' => 518,
                    'end' => 518,
                    'count' => 1
                ],
                [
                    'start' => 538,
                    'end' => 538,
                    'count' => 1
                ],
                [
                    'start' => 558,
                    'end' => 558,
                    'count' => 1
                ],
                [
                    'start' => 578,
                    'end' => 578,
                    'count' => 1
                ],
                [
                    'start' => 598,
                    'end' => 598,
                    'count' => 1
                ],
                [
                    'start' => 609,
                    'end' => 609,
                    'count' => 1
                ],
                [
                    'start' => 618,
                    'end' => 618,
                    'count' => 1
                ],
                [
                    'start' => 638,
                    'end' => 638,
                    'count' => 1
                ],
                [
                    'start' => 658,
                    'end' => 658,
                    'count' => 1
                ],
                [
                    'start' => 678,
                    'end' => 678,
                    'count' => 1
                ],
                [
                    'start' => 698,
                    'end' => 698,
                    'count' => 1
                ],
                [
                    'start' => 718,
                    'end' => 718,
                    'count' => 1
                ],
                [
                    'start' => 732,
                    'end' => 734,
                    'count' => 3
                ],
                [
                    'start' => 736,
                    'end' => 740,
                    'count' => 5
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
                    'start_index' => 736,
                    'end_index' => 740,
                    'duration' => 7.97,
                    'segment_count' => 5
                ]
            ],
            'adTypes' => [
                'pre_roll_ad' => [
                    'count' => 10,
                    'duration' => 20.27
                ],
                'mid_roll_ad' => [
                    'count' => 30,
                    'duration' => 60.68
                ],
                'post_roll_ad' => [
                    'count' => 7,
                    'duration' => 23.1
                ],
                'marker_based_ad' => [
                    'count' => 40,
                    'duration' => 84.48
                ],
                'pattern_based_ad' => [
                    'count' => 1,
                    'duration' => 2.09
                ],
                'duration_based_ad' => [
                    'count' => 15,
                    'duration' => 32.4
                ]
            ],
            'psychologicalFeatures' => [
                'interruption_pattern' => '频繁插播',
                'ad_density' => 7.15,
                'attention_grab_score' => 15,
                'frequency_score' => 100,
                'user_experience_impact' => '严重',
                'watchability_score' => 30
            ],
            'confidence' => 94
        ]
    ],
    'last_learn_date' => '2026-07-02 14:52:40'
];
