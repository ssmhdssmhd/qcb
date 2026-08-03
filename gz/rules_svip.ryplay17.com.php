<?php
/**
 * svip.ryplay17.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:52:52
 */

return [
    'domain' => 'svip.ryplay17.com',
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
            'threshold' => 1024,
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
            'threshold' => 1024,
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
    'confidence_score' => 89,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 146,
            'end_index' => 148,
            'duration' => 11.24,
            'segment_count' => 3,
            'detected_count' => 1,
            'avg_duration' => 12.04,
            'avg_segment_count' => 3
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 94,
            'points' => [
                [
                    'start_index' => 150,
                    'end_index' => 162,
                    'duration' => 52.04,
                    'segment_count' => 13,
                    'position_ratio' => 0.15
                ],
                [
                    'start_index' => 164,
                    'end_index' => 168,
                    'duration' => 21.08,
                    'segment_count' => 5,
                    'position_ratio' => 0.164
                ],
                [
                    'start_index' => 172,
                    'end_index' => 174,
                    'duration' => 9.04,
                    'segment_count' => 3,
                    'position_ratio' => 0.172
                ],
                [
                    'start_index' => 178,
                    'end_index' => 187,
                    'duration' => 37.8,
                    'segment_count' => 10,
                    'position_ratio' => 0.178
                ],
                [
                    'start_index' => 192,
                    'end_index' => 194,
                    'duration' => 10.52,
                    'segment_count' => 3,
                    'position_ratio' => 0.192
                ],
                [
                    'start_index' => 197,
                    'end_index' => 200,
                    'duration' => 15.76,
                    'segment_count' => 4,
                    'position_ratio' => 0.197
                ],
                [
                    'start_index' => 202,
                    'end_index' => 213,
                    'duration' => 46.8,
                    'segment_count' => 12,
                    'position_ratio' => 0.202
                ],
                [
                    'start_index' => 215,
                    'end_index' => 223,
                    'duration' => 34.2,
                    'segment_count' => 9,
                    'position_ratio' => 0.215
                ],
                [
                    'start_index' => 225,
                    'end_index' => 226,
                    'duration' => 9.84,
                    'segment_count' => 2,
                    'position_ratio' => 0.225
                ],
                [
                    'start_index' => 228,
                    'end_index' => 229,
                    'duration' => 7.88,
                    'segment_count' => 2,
                    'position_ratio' => 0.228
                ],
                [
                    'start_index' => 231,
                    'end_index' => 234,
                    'duration' => 14.88,
                    'segment_count' => 4,
                    'position_ratio' => 0.231
                ],
                [
                    'start_index' => 236,
                    'end_index' => 244,
                    'duration' => 32.6,
                    'segment_count' => 9,
                    'position_ratio' => 0.236
                ],
                [
                    'start_index' => 246,
                    'end_index' => 249,
                    'duration' => 14.92,
                    'segment_count' => 4,
                    'position_ratio' => 0.246
                ],
                [
                    'start_index' => 251,
                    'end_index' => 252,
                    'duration' => 6.68,
                    'segment_count' => 2,
                    'position_ratio' => 0.251
                ],
                [
                    'start_index' => 254,
                    'end_index' => 256,
                    'duration' => 10.64,
                    'segment_count' => 3,
                    'position_ratio' => 0.254
                ],
                [
                    'start_index' => 260,
                    'end_index' => 265,
                    'duration' => 23.12,
                    'segment_count' => 6,
                    'position_ratio' => 0.26
                ],
                [
                    'start_index' => 267,
                    'end_index' => 270,
                    'duration' => 14.2,
                    'segment_count' => 4,
                    'position_ratio' => 0.267
                ],
                [
                    'start_index' => 272,
                    'end_index' => 273,
                    'duration' => 6.56,
                    'segment_count' => 2,
                    'position_ratio' => 0.272
                ],
                [
                    'start_index' => 276,
                    'end_index' => 277,
                    'duration' => 4.8,
                    'segment_count' => 2,
                    'position_ratio' => 0.276
                ],
                [
                    'start_index' => 279,
                    'end_index' => 282,
                    'duration' => 15.16,
                    'segment_count' => 4,
                    'position_ratio' => 0.279
                ],
                [
                    'start_index' => 284,
                    'end_index' => 289,
                    'duration' => 23.8,
                    'segment_count' => 6,
                    'position_ratio' => 0.284
                ],
                [
                    'start_index' => 293,
                    'end_index' => 294,
                    'duration' => 7.36,
                    'segment_count' => 2,
                    'position_ratio' => 0.293
                ],
                [
                    'start_index' => 298,
                    'end_index' => 301,
                    'duration' => 15.92,
                    'segment_count' => 4,
                    'position_ratio' => 0.298
                ],
                [
                    'start_index' => 303,
                    'end_index' => 307,
                    'duration' => 19.6,
                    'segment_count' => 5,
                    'position_ratio' => 0.303
                ],
                [
                    'start_index' => 309,
                    'end_index' => 310,
                    'duration' => 8.24,
                    'segment_count' => 2,
                    'position_ratio' => 0.309
                ],
                [
                    'start_index' => 312,
                    'end_index' => 315,
                    'duration' => 15.84,
                    'segment_count' => 4,
                    'position_ratio' => 0.312
                ],
                [
                    'start_index' => 317,
                    'end_index' => 319,
                    'duration' => 9.6,
                    'segment_count' => 3,
                    'position_ratio' => 0.317
                ],
                [
                    'start_index' => 322,
                    'end_index' => 324,
                    'duration' => 11.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.322
                ],
                [
                    'start_index' => 330,
                    'end_index' => 332,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.33
                ],
                [
                    'start_index' => 334,
                    'end_index' => 337,
                    'duration' => 16.52,
                    'segment_count' => 4,
                    'position_ratio' => 0.334
                ],
                [
                    'start_index' => 342,
                    'end_index' => 343,
                    'duration' => 7.16,
                    'segment_count' => 2,
                    'position_ratio' => 0.342
                ],
                [
                    'start_index' => 345,
                    'end_index' => 361,
                    'duration' => 67.2,
                    'segment_count' => 17,
                    'position_ratio' => 0.345
                ],
                [
                    'start_index' => 363,
                    'end_index' => 375,
                    'duration' => 52.32,
                    'segment_count' => 13,
                    'position_ratio' => 0.363
                ],
                [
                    'start_index' => 377,
                    'end_index' => 378,
                    'duration' => 7.72,
                    'segment_count' => 2,
                    'position_ratio' => 0.377
                ],
                [
                    'start_index' => 382,
                    'end_index' => 390,
                    'duration' => 34.16,
                    'segment_count' => 9,
                    'position_ratio' => 0.382
                ],
                [
                    'start_index' => 392,
                    'end_index' => 394,
                    'duration' => 11.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.392
                ],
                [
                    'start_index' => 396,
                    'end_index' => 397,
                    'duration' => 7.48,
                    'segment_count' => 2,
                    'position_ratio' => 0.396
                ],
                [
                    'start_index' => 399,
                    'end_index' => 400,
                    'duration' => 7.72,
                    'segment_count' => 2,
                    'position_ratio' => 0.399
                ],
                [
                    'start_index' => 402,
                    'end_index' => 404,
                    'duration' => 10.32,
                    'segment_count' => 3,
                    'position_ratio' => 0.402
                ],
                [
                    'start_index' => 413,
                    'end_index' => 414,
                    'duration' => 7.2,
                    'segment_count' => 2,
                    'position_ratio' => 0.413
                ],
                [
                    'start_index' => 416,
                    'end_index' => 417,
                    'duration' => 8,
                    'segment_count' => 2,
                    'position_ratio' => 0.416
                ],
                [
                    'start_index' => 419,
                    'end_index' => 420,
                    'duration' => 7.44,
                    'segment_count' => 2,
                    'position_ratio' => 0.419
                ],
                [
                    'start_index' => 431,
                    'end_index' => 434,
                    'duration' => 14.24,
                    'segment_count' => 4,
                    'position_ratio' => 0.431
                ],
                [
                    'start_index' => 436,
                    'end_index' => 442,
                    'duration' => 25.2,
                    'segment_count' => 7,
                    'position_ratio' => 0.436
                ],
                [
                    'start_index' => 444,
                    'end_index' => 446,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.444
                ],
                [
                    'start_index' => 449,
                    'end_index' => 457,
                    'duration' => 33.56,
                    'segment_count' => 9,
                    'position_ratio' => 0.449
                ],
                [
                    'start_index' => 459,
                    'end_index' => 466,
                    'duration' => 30.12,
                    'segment_count' => 8,
                    'position_ratio' => 0.459
                ],
                [
                    'start_index' => 468,
                    'end_index' => 470,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.468
                ],
                [
                    'start_index' => 472,
                    'end_index' => 475,
                    'duration' => 15.6,
                    'segment_count' => 4,
                    'position_ratio' => 0.472
                ],
                [
                    'start_index' => 480,
                    'end_index' => 484,
                    'duration' => 17.44,
                    'segment_count' => 5,
                    'position_ratio' => 0.48
                ],
                [
                    'start_index' => 486,
                    'end_index' => 493,
                    'duration' => 31.28,
                    'segment_count' => 8,
                    'position_ratio' => 0.486
                ],
                [
                    'start_index' => 497,
                    'end_index' => 511,
                    'duration' => 61.16,
                    'segment_count' => 15,
                    'position_ratio' => 0.497
                ],
                [
                    'start_index' => 516,
                    'end_index' => 526,
                    'duration' => 46.88,
                    'segment_count' => 11,
                    'position_ratio' => 0.516
                ],
                [
                    'start_index' => 534,
                    'end_index' => 543,
                    'duration' => 40.24,
                    'segment_count' => 10,
                    'position_ratio' => 0.534
                ],
                [
                    'start_index' => 545,
                    'end_index' => 558,
                    'duration' => 55.88,
                    'segment_count' => 14,
                    'position_ratio' => 0.545
                ],
                [
                    'start_index' => 560,
                    'end_index' => 571,
                    'duration' => 48.92,
                    'segment_count' => 12,
                    'position_ratio' => 0.56
                ],
                [
                    'start_index' => 575,
                    'end_index' => 578,
                    'duration' => 13.24,
                    'segment_count' => 4,
                    'position_ratio' => 0.575
                ],
                [
                    'start_index' => 580,
                    'end_index' => 585,
                    'duration' => 21.6,
                    'segment_count' => 6,
                    'position_ratio' => 0.58
                ],
                [
                    'start_index' => 587,
                    'end_index' => 594,
                    'duration' => 28.8,
                    'segment_count' => 8,
                    'position_ratio' => 0.587
                ],
                [
                    'start_index' => 596,
                    'end_index' => 601,
                    'duration' => 21.52,
                    'segment_count' => 6,
                    'position_ratio' => 0.596
                ],
                [
                    'start_index' => 604,
                    'end_index' => 607,
                    'duration' => 16.08,
                    'segment_count' => 4,
                    'position_ratio' => 0.604
                ],
                [
                    'start_index' => 609,
                    'end_index' => 610,
                    'duration' => 7.12,
                    'segment_count' => 2,
                    'position_ratio' => 0.609
                ],
                [
                    'start_index' => 612,
                    'end_index' => 614,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.612
                ],
                [
                    'start_index' => 618,
                    'end_index' => 622,
                    'duration' => 17.72,
                    'segment_count' => 5,
                    'position_ratio' => 0.618
                ],
                [
                    'start_index' => 624,
                    'end_index' => 625,
                    'duration' => 5.8,
                    'segment_count' => 2,
                    'position_ratio' => 0.624
                ],
                [
                    'start_index' => 627,
                    'end_index' => 631,
                    'duration' => 20.96,
                    'segment_count' => 5,
                    'position_ratio' => 0.627
                ],
                [
                    'start_index' => 633,
                    'end_index' => 638,
                    'duration' => 25.64,
                    'segment_count' => 6,
                    'position_ratio' => 0.633
                ],
                [
                    'start_index' => 642,
                    'end_index' => 646,
                    'duration' => 17.24,
                    'segment_count' => 5,
                    'position_ratio' => 0.642
                ],
                [
                    'start_index' => 648,
                    'end_index' => 658,
                    'duration' => 43.48,
                    'segment_count' => 11,
                    'position_ratio' => 0.648
                ],
                [
                    'start_index' => 663,
                    'end_index' => 668,
                    'duration' => 22.8,
                    'segment_count' => 6,
                    'position_ratio' => 0.663
                ],
                [
                    'start_index' => 670,
                    'end_index' => 672,
                    'duration' => 12,
                    'segment_count' => 3,
                    'position_ratio' => 0.67
                ],
                [
                    'start_index' => 675,
                    'end_index' => 685,
                    'duration' => 42.68,
                    'segment_count' => 11,
                    'position_ratio' => 0.675
                ],
                [
                    'start_index' => 690,
                    'end_index' => 691,
                    'duration' => 7.04,
                    'segment_count' => 2,
                    'position_ratio' => 0.69
                ],
                [
                    'start_index' => 693,
                    'end_index' => 696,
                    'duration' => 13.36,
                    'segment_count' => 4,
                    'position_ratio' => 0.693
                ],
                [
                    'start_index' => 700,
                    'end_index' => 703,
                    'duration' => 15.44,
                    'segment_count' => 4,
                    'position_ratio' => 0.7
                ],
                [
                    'start_index' => 707,
                    'end_index' => 711,
                    'duration' => 19.2,
                    'segment_count' => 5,
                    'position_ratio' => 0.707
                ],
                [
                    'start_index' => 714,
                    'end_index' => 716,
                    'duration' => 10.08,
                    'segment_count' => 3,
                    'position_ratio' => 0.714
                ],
                [
                    'start_index' => 719,
                    'end_index' => 729,
                    'duration' => 41.72,
                    'segment_count' => 11,
                    'position_ratio' => 0.719
                ],
                [
                    'start_index' => 737,
                    'end_index' => 739,
                    'duration' => 13.4,
                    'segment_count' => 3,
                    'position_ratio' => 0.737
                ],
                [
                    'start_index' => 741,
                    'end_index' => 753,
                    'duration' => 50.36,
                    'segment_count' => 13,
                    'position_ratio' => 0.741
                ],
                [
                    'start_index' => 761,
                    'end_index' => 770,
                    'duration' => 38.4,
                    'segment_count' => 10,
                    'position_ratio' => 0.761
                ],
                [
                    'start_index' => 772,
                    'end_index' => 775,
                    'duration' => 12.76,
                    'segment_count' => 4,
                    'position_ratio' => 0.772
                ],
                [
                    'start_index' => 777,
                    'end_index' => 782,
                    'duration' => 25.52,
                    'segment_count' => 6,
                    'position_ratio' => 0.777
                ],
                [
                    'start_index' => 784,
                    'end_index' => 785,
                    'duration' => 6.48,
                    'segment_count' => 2,
                    'position_ratio' => 0.784
                ],
                [
                    'start_index' => 788,
                    'end_index' => 789,
                    'duration' => 7.76,
                    'segment_count' => 2,
                    'position_ratio' => 0.788
                ],
                [
                    'start_index' => 791,
                    'end_index' => 795,
                    'duration' => 18.2,
                    'segment_count' => 5,
                    'position_ratio' => 0.791
                ],
                [
                    'start_index' => 797,
                    'end_index' => 798,
                    'duration' => 4.48,
                    'segment_count' => 2,
                    'position_ratio' => 0.797
                ],
                [
                    'start_index' => 800,
                    'end_index' => 810,
                    'duration' => 43.4,
                    'segment_count' => 11,
                    'position_ratio' => 0.8
                ],
                [
                    'start_index' => 813,
                    'end_index' => 815,
                    'duration' => 12.72,
                    'segment_count' => 3,
                    'position_ratio' => 0.813
                ],
                [
                    'start_index' => 817,
                    'end_index' => 824,
                    'duration' => 30.52,
                    'segment_count' => 8,
                    'position_ratio' => 0.817
                ],
                [
                    'start_index' => 826,
                    'end_index' => 827,
                    'duration' => 7.08,
                    'segment_count' => 2,
                    'position_ratio' => 0.826
                ],
                [
                    'start_index' => 829,
                    'end_index' => 831,
                    'duration' => 9.32,
                    'segment_count' => 3,
                    'position_ratio' => 0.829
                ],
                [
                    'start_index' => 833,
                    'end_index' => 836,
                    'duration' => 13.52,
                    'segment_count' => 4,
                    'position_ratio' => 0.833
                ],
                [
                    'start_index' => 841,
                    'end_index' => 842,
                    'duration' => 7.08,
                    'segment_count' => 2,
                    'position_ratio' => 0.841
                ]
            ],
            'detected_count' => 1,
            'avg_clip_count' => 36,
            'avg_duration_per_clip' => 17.34
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 993,
            'end_index' => 997,
            'duration' => 17.96,
            'segment_count' => 5,
            'detected_count' => 1,
            'avg_duration' => 5.48,
            'avg_segment_count' => 2
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 24,
            'duration' => 412.24,
            'total_count' => 11,
            'total_duration' => 143.2,
            'avg_count_per_video' => 11,
            'sample_count' => 1
        ],
        'mid_roll_ad' => [
            'count' => 94,
            'duration' => 1937.72,
            'total_count' => 36,
            'total_duration' => 624.32,
            'avg_count_per_video' => 36,
            'sample_count' => 1
        ],
        'post_roll_ad' => [
            'count' => 21,
            'duration' => 444.76,
            'total_count' => 6,
            'total_duration' => 159,
            'avg_count_per_video' => 6,
            'sample_count' => 1
        ],
        'marker_based_ad' => [
            'count' => 133,
            'duration' => 2692.88,
            'total_count' => 48,
            'total_duration' => 859.52,
            'avg_count_per_video' => 48,
            'sample_count' => 1
        ],
        'pattern_based_ad' => [
            'count' => 173,
            'duration' => 2916.96,
            'total_count' => 66,
            'total_duration' => 979.36,
            'avg_count_per_video' => 66,
            'sample_count' => 1
        ],
        'duration_based_ad' => [
            'count' => 21,
            'duration' => 385.52,
            'total_count' => 16,
            'total_duration' => 243.28,
            'avg_count_per_video' => 16,
            'sample_count' => 1
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => [
            'avg' => 73.99,
            'sample_count' => 1,
            'min' => 73.99,
            'max' => 73.99
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
        'discontinuity_count' => 225,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:52:50',
    'analysis_stats' => [
        'totalSegments' => 1000,
        'adSegments' => 769,
        'contentSegments' => 231,
        'totalDuration' => 4001.88,
        'adDuration' => 2934.8,
        'contentDuration' => 1067.08,
        'adPercentage' => 73.34,
        'discontinuityCount' => 167,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 356,
        'adClusters' => 177,
        'confidence' => 84
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
            'totalCount' => 1000,
            'adCount' => 769,
            'adPercentage' => 73.34,
            'discontinuityCount' => 167,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 84,
            'analyzed_at' => '2026-08-03 02:52:50'
        ],
        [
            'totalCount' => 346,
            'adCount' => 256,
            'adPercentage' => 71.51,
            'discontinuityCount' => 58,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 89,
            'analyzed_at' => '2026-08-03 02:52:52',
            'adClusterCount' => 69,
            'ad_density' => 73.99
        ]
    ],
    'last_learn_date' => '2026-08-03 02:52:52',
    'name' => 'svip.ryplay17.com'
];
