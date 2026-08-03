<?php
/**
 * cdn7.ryplay7.com 域名广告和插播规则
 * 自动生成于: 2026-08-03 02:53:03
 */

return [
    'domain' => 'cdn7.ryplay7.com',
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
    'confidence_score' => 88,
    'insertion_patterns' => [
        'pre_roll' => [
            'found' => true,
            'start_index' => 138,
            'end_index' => 145,
            'duration' => 34.77,
            'segment_count' => 8
        ],
        'mid_roll' => [
            'found' => true,
            'count' => 109,
            'points' => [
                [
                    'start_index' => 155,
                    'end_index' => 156,
                    'duration' => 5.11,
                    'segment_count' => 2,
                    'position_ratio' => 0.155
                ],
                [
                    'start_index' => 158,
                    'end_index' => 159,
                    'duration' => 6.47,
                    'segment_count' => 2,
                    'position_ratio' => 0.158
                ],
                [
                    'start_index' => 167,
                    'end_index' => 168,
                    'duration' => 9.11,
                    'segment_count' => 2,
                    'position_ratio' => 0.167
                ],
                [
                    'start_index' => 174,
                    'end_index' => 175,
                    'duration' => 8.88,
                    'segment_count' => 2,
                    'position_ratio' => 0.174
                ],
                [
                    'start_index' => 180,
                    'end_index' => 181,
                    'duration' => 6.57,
                    'segment_count' => 2,
                    'position_ratio' => 0.18
                ],
                [
                    'start_index' => 186,
                    'end_index' => 187,
                    'duration' => 6.67,
                    'segment_count' => 2,
                    'position_ratio' => 0.186
                ],
                [
                    'start_index' => 189,
                    'end_index' => 190,
                    'duration' => 8.78,
                    'segment_count' => 2,
                    'position_ratio' => 0.189
                ],
                [
                    'start_index' => 198,
                    'end_index' => 199,
                    'duration' => 4.94,
                    'segment_count' => 2,
                    'position_ratio' => 0.198
                ],
                [
                    'start_index' => 204,
                    'end_index' => 205,
                    'duration' => 10.34,
                    'segment_count' => 2,
                    'position_ratio' => 0.204
                ],
                [
                    'start_index' => 212,
                    'end_index' => 213,
                    'duration' => 8.58,
                    'segment_count' => 2,
                    'position_ratio' => 0.212
                ],
                [
                    'start_index' => 215,
                    'end_index' => 216,
                    'duration' => 6.51,
                    'segment_count' => 2,
                    'position_ratio' => 0.215
                ],
                [
                    'start_index' => 221,
                    'end_index' => 222,
                    'duration' => 7.14,
                    'segment_count' => 2,
                    'position_ratio' => 0.221
                ],
                [
                    'start_index' => 227,
                    'end_index' => 229,
                    'duration' => 8.64,
                    'segment_count' => 3,
                    'position_ratio' => 0.227
                ],
                [
                    'start_index' => 234,
                    'end_index' => 235,
                    'duration' => 7.14,
                    'segment_count' => 2,
                    'position_ratio' => 0.234
                ],
                [
                    'start_index' => 243,
                    'end_index' => 244,
                    'duration' => 6.07,
                    'segment_count' => 2,
                    'position_ratio' => 0.243
                ],
                [
                    'start_index' => 246,
                    'end_index' => 249,
                    'duration' => 11.88,
                    'segment_count' => 4,
                    'position_ratio' => 0.246
                ],
                [
                    'start_index' => 252,
                    'end_index' => 255,
                    'duration' => 14.38,
                    'segment_count' => 4,
                    'position_ratio' => 0.252
                ],
                [
                    'start_index' => 257,
                    'end_index' => 258,
                    'duration' => 8.58,
                    'segment_count' => 2,
                    'position_ratio' => 0.257
                ],
                [
                    'start_index' => 263,
                    'end_index' => 264,
                    'duration' => 6.37,
                    'segment_count' => 2,
                    'position_ratio' => 0.263
                ],
                [
                    'start_index' => 274,
                    'end_index' => 276,
                    'duration' => 10.51,
                    'segment_count' => 3,
                    'position_ratio' => 0.274
                ],
                [
                    'start_index' => 284,
                    'end_index' => 285,
                    'duration' => 8.31,
                    'segment_count' => 2,
                    'position_ratio' => 0.284
                ],
                [
                    'start_index' => 288,
                    'end_index' => 290,
                    'duration' => 8.61,
                    'segment_count' => 3,
                    'position_ratio' => 0.288
                ],
                [
                    'start_index' => 293,
                    'end_index' => 294,
                    'duration' => 6.81,
                    'segment_count' => 2,
                    'position_ratio' => 0.293
                ],
                [
                    'start_index' => 302,
                    'end_index' => 303,
                    'duration' => 5.34,
                    'segment_count' => 2,
                    'position_ratio' => 0.302
                ],
                [
                    'start_index' => 306,
                    'end_index' => 307,
                    'duration' => 7.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.306
                ],
                [
                    'start_index' => 326,
                    'end_index' => 328,
                    'duration' => 13.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.326
                ],
                [
                    'start_index' => 330,
                    'end_index' => 332,
                    'duration' => 14.55,
                    'segment_count' => 3,
                    'position_ratio' => 0.33
                ],
                [
                    'start_index' => 334,
                    'end_index' => 338,
                    'duration' => 16.22,
                    'segment_count' => 5,
                    'position_ratio' => 0.334
                ],
                [
                    'start_index' => 348,
                    'end_index' => 349,
                    'duration' => 5.51,
                    'segment_count' => 2,
                    'position_ratio' => 0.348
                ],
                [
                    'start_index' => 353,
                    'end_index' => 355,
                    'duration' => 12.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.353
                ],
                [
                    'start_index' => 360,
                    'end_index' => 364,
                    'duration' => 16.75,
                    'segment_count' => 5,
                    'position_ratio' => 0.36
                ],
                [
                    'start_index' => 366,
                    'end_index' => 369,
                    'duration' => 12.38,
                    'segment_count' => 4,
                    'position_ratio' => 0.366
                ],
                [
                    'start_index' => 372,
                    'end_index' => 374,
                    'duration' => 9.54,
                    'segment_count' => 3,
                    'position_ratio' => 0.372
                ],
                [
                    'start_index' => 377,
                    'end_index' => 378,
                    'duration' => 7.94,
                    'segment_count' => 2,
                    'position_ratio' => 0.377
                ],
                [
                    'start_index' => 381,
                    'end_index' => 382,
                    'duration' => 7.57,
                    'segment_count' => 2,
                    'position_ratio' => 0.381
                ],
                [
                    'start_index' => 384,
                    'end_index' => 387,
                    'duration' => 15.08,
                    'segment_count' => 4,
                    'position_ratio' => 0.384
                ],
                [
                    'start_index' => 398,
                    'end_index' => 400,
                    'duration' => 9.51,
                    'segment_count' => 3,
                    'position_ratio' => 0.398
                ],
                [
                    'start_index' => 406,
                    'end_index' => 409,
                    'duration' => 18.05,
                    'segment_count' => 4,
                    'position_ratio' => 0.406
                ],
                [
                    'start_index' => 411,
                    'end_index' => 412,
                    'duration' => 8.04,
                    'segment_count' => 2,
                    'position_ratio' => 0.411
                ],
                [
                    'start_index' => 414,
                    'end_index' => 415,
                    'duration' => 6.51,
                    'segment_count' => 2,
                    'position_ratio' => 0.414
                ],
                [
                    'start_index' => 418,
                    'end_index' => 420,
                    'duration' => 9.81,
                    'segment_count' => 3,
                    'position_ratio' => 0.418
                ],
                [
                    'start_index' => 425,
                    'end_index' => 434,
                    'duration' => 37.77,
                    'segment_count' => 10,
                    'position_ratio' => 0.425
                ],
                [
                    'start_index' => 444,
                    'end_index' => 446,
                    'duration' => 7.54,
                    'segment_count' => 3,
                    'position_ratio' => 0.444
                ],
                [
                    'start_index' => 453,
                    'end_index' => 454,
                    'duration' => 7.37,
                    'segment_count' => 2,
                    'position_ratio' => 0.453
                ],
                [
                    'start_index' => 456,
                    'end_index' => 460,
                    'duration' => 17.62,
                    'segment_count' => 5,
                    'position_ratio' => 0.456
                ],
                [
                    'start_index' => 462,
                    'end_index' => 463,
                    'duration' => 7.54,
                    'segment_count' => 2,
                    'position_ratio' => 0.462
                ],
                [
                    'start_index' => 465,
                    'end_index' => 468,
                    'duration' => 12.78,
                    'segment_count' => 4,
                    'position_ratio' => 0.465
                ],
                [
                    'start_index' => 471,
                    'end_index' => 472,
                    'duration' => 7.67,
                    'segment_count' => 2,
                    'position_ratio' => 0.471
                ],
                [
                    'start_index' => 474,
                    'end_index' => 475,
                    'duration' => 6.97,
                    'segment_count' => 2,
                    'position_ratio' => 0.474
                ],
                [
                    'start_index' => 477,
                    'end_index' => 478,
                    'duration' => 7.64,
                    'segment_count' => 2,
                    'position_ratio' => 0.477
                ],
                [
                    'start_index' => 483,
                    'end_index' => 484,
                    'duration' => 5.87,
                    'segment_count' => 2,
                    'position_ratio' => 0.483
                ],
                [
                    'start_index' => 486,
                    'end_index' => 488,
                    'duration' => 14.38,
                    'segment_count' => 3,
                    'position_ratio' => 0.486
                ],
                [
                    'start_index' => 492,
                    'end_index' => 493,
                    'duration' => 5.74,
                    'segment_count' => 2,
                    'position_ratio' => 0.492
                ],
                [
                    'start_index' => 498,
                    'end_index' => 499,
                    'duration' => 7.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.498
                ],
                [
                    'start_index' => 501,
                    'end_index' => 504,
                    'duration' => 15.28,
                    'segment_count' => 4,
                    'position_ratio' => 0.501
                ],
                [
                    'start_index' => 506,
                    'end_index' => 507,
                    'duration' => 7.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.506
                ],
                [
                    'start_index' => 512,
                    'end_index' => 513,
                    'duration' => 5.47,
                    'segment_count' => 2,
                    'position_ratio' => 0.512
                ],
                [
                    'start_index' => 515,
                    'end_index' => 516,
                    'duration' => 5.61,
                    'segment_count' => 2,
                    'position_ratio' => 0.515
                ],
                [
                    'start_index' => 522,
                    'end_index' => 524,
                    'duration' => 12.65,
                    'segment_count' => 3,
                    'position_ratio' => 0.522
                ],
                [
                    'start_index' => 530,
                    'end_index' => 531,
                    'duration' => 7.21,
                    'segment_count' => 2,
                    'position_ratio' => 0.53
                ],
                [
                    'start_index' => 533,
                    'end_index' => 541,
                    'duration' => 34.27,
                    'segment_count' => 9,
                    'position_ratio' => 0.533
                ],
                [
                    'start_index' => 551,
                    'end_index' => 552,
                    'duration' => 9.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.551
                ],
                [
                    'start_index' => 555,
                    'end_index' => 558,
                    'duration' => 17.85,
                    'segment_count' => 4,
                    'position_ratio' => 0.555
                ],
                [
                    'start_index' => 561,
                    'end_index' => 567,
                    'duration' => 22.09,
                    'segment_count' => 7,
                    'position_ratio' => 0.561
                ],
                [
                    'start_index' => 569,
                    'end_index' => 570,
                    'duration' => 9.09,
                    'segment_count' => 2,
                    'position_ratio' => 0.569
                ],
                [
                    'start_index' => 572,
                    'end_index' => 573,
                    'duration' => 8.04,
                    'segment_count' => 2,
                    'position_ratio' => 0.572
                ],
                [
                    'start_index' => 575,
                    'end_index' => 576,
                    'duration' => 6.74,
                    'segment_count' => 2,
                    'position_ratio' => 0.575
                ],
                [
                    'start_index' => 578,
                    'end_index' => 579,
                    'duration' => 8.94,
                    'segment_count' => 2,
                    'position_ratio' => 0.578
                ],
                [
                    'start_index' => 581,
                    'end_index' => 586,
                    'duration' => 25.81,
                    'segment_count' => 6,
                    'position_ratio' => 0.581
                ],
                [
                    'start_index' => 588,
                    'end_index' => 590,
                    'duration' => 10.44,
                    'segment_count' => 3,
                    'position_ratio' => 0.588
                ],
                [
                    'start_index' => 596,
                    'end_index' => 601,
                    'duration' => 20.05,
                    'segment_count' => 6,
                    'position_ratio' => 0.596
                ],
                [
                    'start_index' => 603,
                    'end_index' => 604,
                    'duration' => 6.21,
                    'segment_count' => 2,
                    'position_ratio' => 0.603
                ],
                [
                    'start_index' => 606,
                    'end_index' => 609,
                    'duration' => 12.9,
                    'segment_count' => 4,
                    'position_ratio' => 0.606
                ],
                [
                    'start_index' => 611,
                    'end_index' => 612,
                    'duration' => 9.51,
                    'segment_count' => 2,
                    'position_ratio' => 0.611
                ],
                [
                    'start_index' => 622,
                    'end_index' => 626,
                    'duration' => 16.65,
                    'segment_count' => 5,
                    'position_ratio' => 0.622
                ],
                [
                    'start_index' => 629,
                    'end_index' => 630,
                    'duration' => 6.87,
                    'segment_count' => 2,
                    'position_ratio' => 0.629
                ],
                [
                    'start_index' => 634,
                    'end_index' => 636,
                    'duration' => 12.28,
                    'segment_count' => 3,
                    'position_ratio' => 0.634
                ],
                [
                    'start_index' => 638,
                    'end_index' => 639,
                    'duration' => 7.34,
                    'segment_count' => 2,
                    'position_ratio' => 0.638
                ],
                [
                    'start_index' => 642,
                    'end_index' => 646,
                    'duration' => 19.02,
                    'segment_count' => 5,
                    'position_ratio' => 0.642
                ],
                [
                    'start_index' => 654,
                    'end_index' => 655,
                    'duration' => 7.94,
                    'segment_count' => 2,
                    'position_ratio' => 0.654
                ],
                [
                    'start_index' => 657,
                    'end_index' => 658,
                    'duration' => 6.57,
                    'segment_count' => 2,
                    'position_ratio' => 0.657
                ],
                [
                    'start_index' => 660,
                    'end_index' => 661,
                    'duration' => 6.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.66
                ],
                [
                    'start_index' => 665,
                    'end_index' => 666,
                    'duration' => 7.01,
                    'segment_count' => 2,
                    'position_ratio' => 0.665
                ],
                [
                    'start_index' => 672,
                    'end_index' => 675,
                    'duration' => 12.01,
                    'segment_count' => 4,
                    'position_ratio' => 0.672
                ],
                [
                    'start_index' => 677,
                    'end_index' => 678,
                    'duration' => 8.27,
                    'segment_count' => 2,
                    'position_ratio' => 0.677
                ],
                [
                    'start_index' => 683,
                    'end_index' => 684,
                    'duration' => 8.11,
                    'segment_count' => 2,
                    'position_ratio' => 0.683
                ],
                [
                    'start_index' => 689,
                    'end_index' => 691,
                    'duration' => 11.11,
                    'segment_count' => 3,
                    'position_ratio' => 0.689
                ],
                [
                    'start_index' => 693,
                    'end_index' => 696,
                    'duration' => 15.78,
                    'segment_count' => 4,
                    'position_ratio' => 0.693
                ],
                [
                    'start_index' => 698,
                    'end_index' => 702,
                    'duration' => 16.95,
                    'segment_count' => 5,
                    'position_ratio' => 0.698
                ],
                [
                    'start_index' => 707,
                    'end_index' => 712,
                    'duration' => 25.76,
                    'segment_count' => 6,
                    'position_ratio' => 0.707
                ],
                [
                    'start_index' => 726,
                    'end_index' => 728,
                    'duration' => 8.94,
                    'segment_count' => 3,
                    'position_ratio' => 0.726
                ],
                [
                    'start_index' => 737,
                    'end_index' => 741,
                    'duration' => 19.35,
                    'segment_count' => 5,
                    'position_ratio' => 0.737
                ],
                [
                    'start_index' => 743,
                    'end_index' => 744,
                    'duration' => 4.47,
                    'segment_count' => 2,
                    'position_ratio' => 0.743
                ],
                [
                    'start_index' => 746,
                    'end_index' => 747,
                    'duration' => 6.47,
                    'segment_count' => 2,
                    'position_ratio' => 0.746
                ],
                [
                    'start_index' => 749,
                    'end_index' => 750,
                    'duration' => 9.48,
                    'segment_count' => 2,
                    'position_ratio' => 0.749
                ],
                [
                    'start_index' => 756,
                    'end_index' => 762,
                    'duration' => 31.77,
                    'segment_count' => 7,
                    'position_ratio' => 0.756
                ],
                [
                    'start_index' => 765,
                    'end_index' => 770,
                    'duration' => 25.36,
                    'segment_count' => 6,
                    'position_ratio' => 0.765
                ],
                [
                    'start_index' => 774,
                    'end_index' => 775,
                    'duration' => 10.31,
                    'segment_count' => 2,
                    'position_ratio' => 0.774
                ],
                [
                    'start_index' => 778,
                    'end_index' => 786,
                    'duration' => 33.4,
                    'segment_count' => 9,
                    'position_ratio' => 0.778
                ],
                [
                    'start_index' => 788,
                    'end_index' => 789,
                    'duration' => 9.54,
                    'segment_count' => 2,
                    'position_ratio' => 0.788
                ],
                [
                    'start_index' => 791,
                    'end_index' => 793,
                    'duration' => 13.81,
                    'segment_count' => 3,
                    'position_ratio' => 0.791
                ],
                [
                    'start_index' => 797,
                    'end_index' => 801,
                    'duration' => 16.25,
                    'segment_count' => 5,
                    'position_ratio' => 0.797
                ],
                [
                    'start_index' => 804,
                    'end_index' => 805,
                    'duration' => 5.17,
                    'segment_count' => 2,
                    'position_ratio' => 0.804
                ],
                [
                    'start_index' => 810,
                    'end_index' => 811,
                    'duration' => 5.67,
                    'segment_count' => 2,
                    'position_ratio' => 0.81
                ],
                [
                    'start_index' => 813,
                    'end_index' => 816,
                    'duration' => 17.08,
                    'segment_count' => 4,
                    'position_ratio' => 0.813
                ],
                [
                    'start_index' => 820,
                    'end_index' => 824,
                    'duration' => 18.12,
                    'segment_count' => 5,
                    'position_ratio' => 0.82
                ],
                [
                    'start_index' => 826,
                    'end_index' => 828,
                    'duration' => 9.94,
                    'segment_count' => 3,
                    'position_ratio' => 0.826
                ],
                [
                    'start_index' => 831,
                    'end_index' => 834,
                    'duration' => 17.55,
                    'segment_count' => 4,
                    'position_ratio' => 0.831
                ],
                [
                    'start_index' => 846,
                    'end_index' => 848,
                    'duration' => 9.58,
                    'segment_count' => 3,
                    'position_ratio' => 0.846
                ]
            ]
        ],
        'post_roll' => [
            'found' => true,
            'start_index' => 990,
            'end_index' => 992,
            'duration' => 10.51,
            'segment_count' => 3
        ]
    ],
    'ad_type_stats' => [
        'pre_roll_ad' => [
            'count' => 21,
            'duration' => 288.95
        ],
        'mid_roll_ad' => [
            'count' => 109,
            'duration' => 1241.79
        ],
        'post_roll_ad' => [
            'count' => 23,
            'duration' => 292.73
        ],
        'marker_based_ad' => [
            'count' => 162,
            'duration' => 1748.87
        ],
        'pattern_based_ad' => [
            'count' => 231,
            'duration' => 2105.9
        ],
        'duration_based_ad' => [
            'count' => 25,
            'duration' => 249.26
        ]
    ],
    'psychological_profile' => [
        'interruption_pattern' => '频繁插播',
        'ad_density' => 59.5,
        'attention_grab_score' => 100,
        'frequency_score' => 100,
        'user_experience_impact' => '严重',
        'watchability_score' => 0
    ],
    'marker_stats' => [
        'discontinuity_count' => 167,
        'cue_marker_count' => 0,
        'scte35_count' => 0,
        'ad_tag_count' => 0
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-08-03 02:53:03',
    'analysis_stats' => [
        'totalSegments' => 1000,
        'adSegments' => 595,
        'contentSegments' => 405,
        'totalDuration' => 4012.36,
        'adDuration' => 2199.71,
        'contentDuration' => 1812.65,
        'adPercentage' => 54.82,
        'discontinuityCount' => 167,
        'cueMarkerCount' => 0,
        'scte35Count' => 0,
        'adTagCount' => 0,
        'sequenceJumps' => 366,
        'adClusters' => 253,
        'confidence' => 88
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
            'totalCount' => 1000,
            'adCount' => 595,
            'adPercentage' => 54.82,
            'discontinuityCount' => 167,
            'cueMarkerCount' => 0,
            'scte35Count' => 0,
            'adTagCount' => 0,
            'confidence' => 88,
            'analyzed_at' => '2026-08-03 02:53:03',
            'adClusterCount' => 253,
            'ad_density' => 59.5
        ]
    ],
    'last_learn_date' => '2026-08-03 02:53:03'
];
