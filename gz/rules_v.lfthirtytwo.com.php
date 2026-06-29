<?php
/**
 * v.lfthirtytwo.com 域名广告和插播规则
 * 自动生成于: 2026-06-29 20:42:53
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
            'reason' => '序列号向前跳跃 > 100000 可能表示广告插播',
            'weight' => 90
        ],
        [
            'name' => 'sequence_jump_backward',
            'enabled' => true,
            'type' => 'sequence_jump',
            'direction' => 'backward',
            'threshold' => 100000,
            'reason' => '序列号向后跳跃 > 100000 可能表示广告结束',
            'weight' => 90
        ]
    ],
    'filename_patterns' => [
        '/^e13c4242/i'
    ],
    'ad_threshold' => 45,
    'confidence' => [
        'high' => 80,
        'medium' => 50,
        'low' => 30
    ],
    'note' => '基于靶机测试分析自动生成的规则',
    'analysis_date' => '2026-06-28',
    'sample_url' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/index.m3u8',
    'learn_count' => 1,
    'history_stats' => [
        [
            'segments' => [
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000000.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000000.ts'
                    ],
                    'index' => 0,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000001.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000001.ts'
                    ],
                    'index' => 1,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000002.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000002.ts'
                    ],
                    'index' => 2,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.48,
                        'title' => '',
                        'uri' => 'e13c424277e000003.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000003.ts'
                    ],
                    'index' => 3,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.12,
                        'title' => '',
                        'uri' => 'e13c424277e000004.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000004.ts'
                    ],
                    'index' => 4,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000005.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000005.ts'
                    ],
                    'index' => 5,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000006.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000006.ts'
                    ],
                    'index' => 6,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000007.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000007.ts'
                    ],
                    'index' => 7,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000008.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000008.ts'
                    ],
                    'index' => 8,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000009.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000009.ts'
                    ],
                    'index' => 9,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000010.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000010.ts'
                    ],
                    'index' => 10,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000011.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000011.ts'
                    ],
                    'index' => 11,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000012.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000012.ts'
                    ],
                    'index' => 12,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000013.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000013.ts'
                    ],
                    'index' => 13,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000014.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000014.ts'
                    ],
                    'index' => 14,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000015.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000015.ts'
                    ],
                    'index' => 15,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.96,
                        'title' => '',
                        'uri' => 'e13c424277e000016.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000016.ts'
                    ],
                    'index' => 16,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000017.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000017.ts'
                    ],
                    'index' => 17,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000018.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000018.ts'
                    ],
                    'index' => 18,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000019.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000019.ts'
                    ],
                    'index' => 19,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000020.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000020.ts'
                    ],
                    'index' => 20,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000021.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000021.ts'
                    ],
                    'index' => 21,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000022.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000022.ts'
                    ],
                    'index' => 22,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000023.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000023.ts'
                    ],
                    'index' => 23,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000024.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000024.ts'
                    ],
                    'index' => 24,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => 'e13c424277e000025.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000025.ts'
                    ],
                    'index' => 25,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.8,
                        'title' => '',
                        'uri' => 'e13c424277e000026.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000026.ts'
                    ],
                    'index' => 26,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'e13c424277e000027.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000027.ts'
                    ],
                    'index' => 27,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000028.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000028.ts'
                    ],
                    'index' => 28,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000029.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000029.ts'
                    ],
                    'index' => 29,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'e13c424277e000030.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000030.ts'
                    ],
                    'index' => 30,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000031.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000031.ts'
                    ],
                    'index' => 31,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000032.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000032.ts'
                    ],
                    'index' => 32,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000033.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000033.ts'
                    ],
                    'index' => 33,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000034.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000034.ts'
                    ],
                    'index' => 34,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000035.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000035.ts'
                    ],
                    'index' => 35,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000036.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000036.ts'
                    ],
                    'index' => 36,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000037.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000037.ts'
                    ],
                    'index' => 37,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'e13c424277e000038.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000038.ts'
                    ],
                    'index' => 38,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000039.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000039.ts'
                    ],
                    'index' => 39,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000040.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000040.ts'
                    ],
                    'index' => 40,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => 'e13c424277e000041.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000041.ts'
                    ],
                    'index' => 41,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000042.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000042.ts'
                    ],
                    'index' => 42,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => 'e13c424277e000043.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000043.ts'
                    ],
                    'index' => 43,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000044.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000044.ts'
                    ],
                    'index' => 44,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000045.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000045.ts'
                    ],
                    'index' => 45,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000046.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000046.ts'
                    ],
                    'index' => 46,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'e13c424277e000047.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000047.ts'
                    ],
                    'index' => 47,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000048.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000048.ts'
                    ],
                    'index' => 48,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.72,
                        'title' => '',
                        'uri' => 'e13c424277e000049.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000049.ts'
                    ],
                    'index' => 49,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000050.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000050.ts'
                    ],
                    'index' => 50,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000051.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000051.ts'
                    ],
                    'index' => 51,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000052.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000052.ts'
                    ],
                    'index' => 52,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.6,
                        'title' => '',
                        'uri' => 'e13c424277e000053.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000053.ts'
                    ],
                    'index' => 53,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.88,
                        'title' => '',
                        'uri' => 'e13c424277e000054.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000054.ts'
                    ],
                    'index' => 54,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000055.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000055.ts'
                    ],
                    'index' => 55,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.08,
                        'title' => '',
                        'uri' => 'e13c424277e000056.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000056.ts'
                    ],
                    'index' => 56,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'e13c424277e000057.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000057.ts'
                    ],
                    'index' => 57,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'e13c424277e000058.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000058.ts'
                    ],
                    'index' => 58,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'e13c424277e000059.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000059.ts'
                    ],
                    'index' => 59,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000060.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000060.ts'
                    ],
                    'index' => 60,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'e13c424277e000061.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000061.ts'
                    ],
                    'index' => 61,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000062.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000062.ts'
                    ],
                    'index' => 62,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => 'e13c424277e000063.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000063.ts'
                    ],
                    'index' => 63,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000064.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000064.ts'
                    ],
                    'index' => 64,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'e13c424277e000065.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000065.ts'
                    ],
                    'index' => 65,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.6,
                        'title' => '',
                        'uri' => 'e13c424277e000066.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000066.ts'
                    ],
                    'index' => 66,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000067.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000067.ts'
                    ],
                    'index' => 67,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => 'e13c424277e000068.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000068.ts'
                    ],
                    'index' => 68,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'e13c424277e000069.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000069.ts'
                    ],
                    'index' => 69,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000070.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000070.ts'
                    ],
                    'index' => 70,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.4,
                        'title' => '',
                        'uri' => 'e13c424277e000071.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000071.ts'
                    ],
                    'index' => 71,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => 'e13c424277e000072.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000072.ts'
                    ],
                    'index' => 72,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000073.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000073.ts'
                    ],
                    'index' => 73,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702681.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702681.ts'
                    ],
                    'index' => 74,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ],
                        [
                            'name' => 'sequence_jump_forward',
                            'description' => '序列号向前跳跃 > 100000 可能表示广告插播'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702682.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702682.ts'
                    ],
                    'index' => 75,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702683.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702683.ts'
                    ],
                    'index' => 76,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702684.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702684.ts'
                    ],
                    'index' => 77,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702685.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702685.ts'
                    ],
                    'index' => 78,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702686.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702686.ts'
                    ],
                    'index' => 79,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => 'e13c424277e0702687.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702687.ts'
                    ],
                    'index' => 80,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => 'e13c424277e000074.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000074.ts'
                    ],
                    'index' => 81,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ],
                        [
                            'name' => 'sequence_jump_backward',
                            'description' => '序列号向后跳跃 > 100000 可能表示广告结束'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000075.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000075.ts'
                    ],
                    'index' => 82,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => 'e13c424277e000076.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000076.ts'
                    ],
                    'index' => 83,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.12,
                        'title' => '',
                        'uri' => 'e13c424277e000077.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000077.ts'
                    ],
                    'index' => 84,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'e13c424277e000078.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000078.ts'
                    ],
                    'index' => 85,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000079.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000079.ts'
                    ],
                    'index' => 86,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000080.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000080.ts'
                    ],
                    'index' => 87,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => 'e13c424277e000081.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000081.ts'
                    ],
                    'index' => 88,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => 'e13c424277e000082.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000082.ts'
                    ],
                    'index' => 89,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.68,
                        'title' => '',
                        'uri' => 'e13c424277e000083.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000083.ts'
                    ],
                    'index' => 90,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'e13c424277e000084.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000084.ts'
                    ],
                    'index' => 91,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000085.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000085.ts'
                    ],
                    'index' => 92,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => 'e13c424277e000086.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000086.ts'
                    ],
                    'index' => 93,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.88,
                        'title' => '',
                        'uri' => 'e13c424277e000087.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000087.ts'
                    ],
                    'index' => 94,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000088.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000088.ts'
                    ],
                    'index' => 95,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000089.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000089.ts'
                    ],
                    'index' => 96,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000090.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000090.ts'
                    ],
                    'index' => 97,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'e13c424277e000091.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000091.ts'
                    ],
                    'index' => 98,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000092.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000092.ts'
                    ],
                    'index' => 99,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => 'e13c424277e000093.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000093.ts'
                    ],
                    'index' => 100,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => 'e13c424277e000094.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000094.ts'
                    ],
                    'index' => 101,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'e13c424277e000095.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000095.ts'
                    ],
                    'index' => 102,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => 'e13c424277e000096.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000096.ts'
                    ],
                    'index' => 103,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'e13c424277e000097.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000097.ts'
                    ],
                    'index' => 104,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'e13c424277e000098.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000098.ts'
                    ],
                    'index' => 105,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000099.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000099.ts'
                    ],
                    'index' => 106,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000100.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000100.ts'
                    ],
                    'index' => 107,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.04,
                        'title' => '',
                        'uri' => 'e13c424277e000101.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000101.ts'
                    ],
                    'index' => 108,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.2,
                        'title' => '',
                        'uri' => 'e13c424277e000102.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000102.ts'
                    ],
                    'index' => 109,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => 'e13c424277e000103.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000103.ts'
                    ],
                    'index' => 110,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'e13c424277e000104.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000104.ts'
                    ],
                    'index' => 111,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'e13c424277e000105.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000105.ts'
                    ],
                    'index' => 112,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.76,
                        'title' => '',
                        'uri' => 'e13c424277e000106.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000106.ts'
                    ],
                    'index' => 113,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => 'e13c424277e000107.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000107.ts'
                    ],
                    'index' => 114,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.24,
                        'title' => '',
                        'uri' => 'e13c424277e000108.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000108.ts'
                    ],
                    'index' => 115,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.6,
                        'title' => '',
                        'uri' => 'e13c424277e000109.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000109.ts'
                    ],
                    'index' => 116,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.88,
                        'title' => '',
                        'uri' => 'e13c424277e000110.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000110.ts'
                    ],
                    'index' => 117,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'e13c424277e000111.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000111.ts'
                    ],
                    'index' => 118,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000112.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000112.ts'
                    ],
                    'index' => 119,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000113.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000113.ts'
                    ],
                    'index' => 120,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => 'e13c424277e000114.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000114.ts'
                    ],
                    'index' => 121,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000115.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000115.ts'
                    ],
                    'index' => 122,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => 'e13c424277e000116.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000116.ts'
                    ],
                    'index' => 123,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000117.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000117.ts'
                    ],
                    'index' => 124,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000118.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000118.ts'
                    ],
                    'index' => 125,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.64,
                        'title' => '',
                        'uri' => 'e13c424277e000119.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000119.ts'
                    ],
                    'index' => 126,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000120.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000120.ts'
                    ],
                    'index' => 127,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000121.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000121.ts'
                    ],
                    'index' => 128,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000122.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000122.ts'
                    ],
                    'index' => 129,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 0.4,
                        'title' => '',
                        'uri' => 'e13c424277e000123.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000123.ts'
                    ],
                    'index' => 130,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'e13c424277e000124.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000124.ts'
                    ],
                    'index' => 131,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'e13c424277e000125.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000125.ts'
                    ],
                    'index' => 132,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => 'e13c424277e000126.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000126.ts'
                    ],
                    'index' => 133,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000127.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000127.ts'
                    ],
                    'index' => 134,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => 'e13c424277e000128.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000128.ts'
                    ],
                    'index' => 135,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000129.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000129.ts'
                    ],
                    'index' => 136,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000130.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000130.ts'
                    ],
                    'index' => 137,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000131.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000131.ts'
                    ],
                    'index' => 138,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.36,
                        'title' => '',
                        'uri' => 'e13c424277e000132.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000132.ts'
                    ],
                    'index' => 139,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 0.48,
                        'title' => '',
                        'uri' => 'e13c424277e000133.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000133.ts'
                    ],
                    'index' => 140,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000134.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000134.ts'
                    ],
                    'index' => 141,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'e13c424277e000135.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000135.ts'
                    ],
                    'index' => 142,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000136.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000136.ts'
                    ],
                    'index' => 143,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000137.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000137.ts'
                    ],
                    'index' => 144,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000138.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000138.ts'
                    ],
                    'index' => 145,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => 'e13c424277e000139.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000139.ts'
                    ],
                    'index' => 146,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000140.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000140.ts'
                    ],
                    'index' => 147,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => 'e13c424277e000141.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000141.ts'
                    ],
                    'index' => 148,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 7.08,
                        'title' => '',
                        'uri' => 'e13c424277e000142.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000142.ts'
                    ],
                    'index' => 149,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'e13c424277e000143.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000143.ts'
                    ],
                    'index' => 150,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000144.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000144.ts'
                    ],
                    'index' => 151,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000145.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000145.ts'
                    ],
                    'index' => 152,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => 'e13c424277e000146.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000146.ts'
                    ],
                    'index' => 153,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => 'e13c424277e000147.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000147.ts'
                    ],
                    'index' => 154,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'e13c424277e000148.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000148.ts'
                    ],
                    'index' => 155,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000149.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000149.ts'
                    ],
                    'index' => 156,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => 'e13c424277e000150.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000150.ts'
                    ],
                    'index' => 157,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.44,
                        'title' => '',
                        'uri' => 'e13c424277e000151.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000151.ts'
                    ],
                    'index' => 158,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'e13c424277e000152.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000152.ts'
                    ],
                    'index' => 159,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => 'e13c424277e000153.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000153.ts'
                    ],
                    'index' => 160,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000154.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000154.ts'
                    ],
                    'index' => 161,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.84,
                        'title' => '',
                        'uri' => 'e13c424277e000155.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000155.ts'
                    ],
                    'index' => 162,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000156.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000156.ts'
                    ],
                    'index' => 163,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000157.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000157.ts'
                    ],
                    'index' => 164,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000158.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000158.ts'
                    ],
                    'index' => 165,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000159.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000159.ts'
                    ],
                    'index' => 166,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000160.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000160.ts'
                    ],
                    'index' => 167,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.44,
                        'title' => '',
                        'uri' => 'e13c424277e000161.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000161.ts'
                    ],
                    'index' => 168,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => 'e13c424277e000162.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000162.ts'
                    ],
                    'index' => 169,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => 'e13c424277e000163.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000163.ts'
                    ],
                    'index' => 170,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000164.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000164.ts'
                    ],
                    'index' => 171,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.48,
                        'title' => '',
                        'uri' => 'e13c424277e000165.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000165.ts'
                    ],
                    'index' => 172,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => 'e13c424277e000166.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000166.ts'
                    ],
                    'index' => 173,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => 'e13c424277e000167.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000167.ts'
                    ],
                    'index' => 174,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000168.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000168.ts'
                    ],
                    'index' => 175,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000169.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000169.ts'
                    ],
                    'index' => 176,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => 'e13c424277e000170.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000170.ts'
                    ],
                    'index' => 177,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000171.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000171.ts'
                    ],
                    'index' => 178,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000172.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000172.ts'
                    ],
                    'index' => 179,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5,
                        'title' => '',
                        'uri' => 'e13c424277e000173.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000173.ts'
                    ],
                    'index' => 180,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'e13c424277e000174.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000174.ts'
                    ],
                    'index' => 181,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000175.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000175.ts'
                    ],
                    'index' => 182,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.52,
                        'title' => '',
                        'uri' => 'e13c424277e000176.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000176.ts'
                    ],
                    'index' => 183,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.84,
                        'title' => '',
                        'uri' => 'e13c424277e000177.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000177.ts'
                    ],
                    'index' => 184,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000178.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000178.ts'
                    ],
                    'index' => 185,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.48,
                        'title' => '',
                        'uri' => 'e13c424277e000179.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000179.ts'
                    ],
                    'index' => 186,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000180.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000180.ts'
                    ],
                    'index' => 187,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.56,
                        'title' => '',
                        'uri' => 'e13c424277e000181.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000181.ts'
                    ],
                    'index' => 188,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000182.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000182.ts'
                    ],
                    'index' => 189,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.16,
                        'title' => '',
                        'uri' => 'e13c424277e000183.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000183.ts'
                    ],
                    'index' => 190,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000184.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000184.ts'
                    ],
                    'index' => 191,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => 'e13c424277e000185.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000185.ts'
                    ],
                    'index' => 192,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000186.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000186.ts'
                    ],
                    'index' => 193,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => 'e13c424277e000187.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000187.ts'
                    ],
                    'index' => 194,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000188.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000188.ts'
                    ],
                    'index' => 195,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.56,
                        'title' => '',
                        'uri' => 'e13c424277e000189.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000189.ts'
                    ],
                    'index' => 196,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'e13c424277e000190.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000190.ts'
                    ],
                    'index' => 197,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000191.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000191.ts'
                    ],
                    'index' => 198,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000192.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000192.ts'
                    ],
                    'index' => 199,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.2,
                        'title' => '',
                        'uri' => 'e13c424277e000193.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000193.ts'
                    ],
                    'index' => 200,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000194.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000194.ts'
                    ],
                    'index' => 201,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000195.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000195.ts'
                    ],
                    'index' => 202,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000196.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000196.ts'
                    ],
                    'index' => 203,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.36,
                        'title' => '',
                        'uri' => 'e13c424277e000197.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000197.ts'
                    ],
                    'index' => 204,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000198.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000198.ts'
                    ],
                    'index' => 205,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000199.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000199.ts'
                    ],
                    'index' => 206,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => 'e13c424277e000200.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000200.ts'
                    ],
                    'index' => 207,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000201.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000201.ts'
                    ],
                    'index' => 208,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.2,
                        'title' => '',
                        'uri' => 'e13c424277e000202.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000202.ts'
                    ],
                    'index' => 209,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => 'e13c424277e000203.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000203.ts'
                    ],
                    'index' => 210,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000204.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000204.ts'
                    ],
                    'index' => 211,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000205.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000205.ts'
                    ],
                    'index' => 212,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.68,
                        'title' => '',
                        'uri' => 'e13c424277e000206.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000206.ts'
                    ],
                    'index' => 213,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000207.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000207.ts'
                    ],
                    'index' => 214,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000208.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000208.ts'
                    ],
                    'index' => 215,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000209.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000209.ts'
                    ],
                    'index' => 216,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.92,
                        'title' => '',
                        'uri' => 'e13c424277e000210.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000210.ts'
                    ],
                    'index' => 217,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000211.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000211.ts'
                    ],
                    'index' => 218,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.04,
                        'title' => '',
                        'uri' => 'e13c424277e000212.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000212.ts'
                    ],
                    'index' => 219,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000213.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000213.ts'
                    ],
                    'index' => 220,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000214.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000214.ts'
                    ],
                    'index' => 221,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'e13c424277e000215.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000215.ts'
                    ],
                    'index' => 222,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000216.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000216.ts'
                    ],
                    'index' => 223,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'e13c424277e000217.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000217.ts'
                    ],
                    'index' => 224,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.72,
                        'title' => '',
                        'uri' => 'e13c424277e000218.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000218.ts'
                    ],
                    'index' => 225,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000219.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000219.ts'
                    ],
                    'index' => 226,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.12,
                        'title' => '',
                        'uri' => 'e13c424277e000220.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000220.ts'
                    ],
                    'index' => 227,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'e13c424277e000221.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000221.ts'
                    ],
                    'index' => 228,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => 'e13c424277e000222.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000222.ts'
                    ],
                    'index' => 229,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000223.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000223.ts'
                    ],
                    'index' => 230,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'e13c424277e000224.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000224.ts'
                    ],
                    'index' => 231,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.6,
                        'title' => '',
                        'uri' => 'e13c424277e000225.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000225.ts'
                    ],
                    'index' => 232,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => 'e13c424277e000226.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000226.ts'
                    ],
                    'index' => 233,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000227.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000227.ts'
                    ],
                    'index' => 234,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000228.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000228.ts'
                    ],
                    'index' => 235,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000229.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000229.ts'
                    ],
                    'index' => 236,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000230.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000230.ts'
                    ],
                    'index' => 237,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000231.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000231.ts'
                    ],
                    'index' => 238,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000232.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000232.ts'
                    ],
                    'index' => 239,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.04,
                        'title' => '',
                        'uri' => 'e13c424277e000233.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000233.ts'
                    ],
                    'index' => 240,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.96,
                        'title' => '',
                        'uri' => 'e13c424277e000234.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000234.ts'
                    ],
                    'index' => 241,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.48,
                        'title' => '',
                        'uri' => 'e13c424277e000235.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000235.ts'
                    ],
                    'index' => 242,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000236.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000236.ts'
                    ],
                    'index' => 243,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'e13c424277e000237.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000237.ts'
                    ],
                    'index' => 244,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000238.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000238.ts'
                    ],
                    'index' => 245,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000239.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000239.ts'
                    ],
                    'index' => 246,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000240.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000240.ts'
                    ],
                    'index' => 247,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000241.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000241.ts'
                    ],
                    'index' => 248,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => 'e13c424277e000242.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000242.ts'
                    ],
                    'index' => 249,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000243.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000243.ts'
                    ],
                    'index' => 250,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'e13c424277e000244.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000244.ts'
                    ],
                    'index' => 251,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'e13c424277e000245.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000245.ts'
                    ],
                    'index' => 252,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000246.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000246.ts'
                    ],
                    'index' => 253,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.96,
                        'title' => '',
                        'uri' => 'e13c424277e000247.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000247.ts'
                    ],
                    'index' => 254,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'e13c424277e000248.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000248.ts'
                    ],
                    'index' => 255,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => 'e13c424277e000249.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000249.ts'
                    ],
                    'index' => 256,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'e13c424277e000250.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000250.ts'
                    ],
                    'index' => 257,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 7.56,
                        'title' => '',
                        'uri' => 'e13c424277e000251.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000251.ts'
                    ],
                    'index' => 258,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000252.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000252.ts'
                    ],
                    'index' => 259,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000253.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000253.ts'
                    ],
                    'index' => 260,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000254.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000254.ts'
                    ],
                    'index' => 261,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.08,
                        'title' => '',
                        'uri' => 'e13c424277e000255.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000255.ts'
                    ],
                    'index' => 262,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000256.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000256.ts'
                    ],
                    'index' => 263,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.48,
                        'title' => '',
                        'uri' => 'e13c424277e000257.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000257.ts'
                    ],
                    'index' => 264,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000258.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000258.ts'
                    ],
                    'index' => 265,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => 'e13c424277e000259.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000259.ts'
                    ],
                    'index' => 266,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000260.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000260.ts'
                    ],
                    'index' => 267,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000261.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000261.ts'
                    ],
                    'index' => 268,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000262.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000262.ts'
                    ],
                    'index' => 269,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000263.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000263.ts'
                    ],
                    'index' => 270,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000264.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000264.ts'
                    ],
                    'index' => 271,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000265.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000265.ts'
                    ],
                    'index' => 272,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000266.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000266.ts'
                    ],
                    'index' => 273,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'e13c424277e000267.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000267.ts'
                    ],
                    'index' => 274,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000268.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000268.ts'
                    ],
                    'index' => 275,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000269.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000269.ts'
                    ],
                    'index' => 276,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'e13c424277e000270.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000270.ts'
                    ],
                    'index' => 277,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000271.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000271.ts'
                    ],
                    'index' => 278,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.04,
                        'title' => '',
                        'uri' => 'e13c424277e000272.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000272.ts'
                    ],
                    'index' => 279,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.12,
                        'title' => '',
                        'uri' => 'e13c424277e000273.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000273.ts'
                    ],
                    'index' => 280,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.52,
                        'title' => '',
                        'uri' => 'e13c424277e000274.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000274.ts'
                    ],
                    'index' => 281,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 0.8,
                        'title' => '',
                        'uri' => 'e13c424277e000275.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000275.ts'
                    ],
                    'index' => 282,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => 'e13c424277e000276.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000276.ts'
                    ],
                    'index' => 283,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.36,
                        'title' => '',
                        'uri' => 'e13c424277e000277.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000277.ts'
                    ],
                    'index' => 284,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000278.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000278.ts'
                    ],
                    'index' => 285,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.36,
                        'title' => '',
                        'uri' => 'e13c424277e000279.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000279.ts'
                    ],
                    'index' => 286,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000280.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000280.ts'
                    ],
                    'index' => 287,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 0.44,
                        'title' => '',
                        'uri' => 'e13c424277e000281.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000281.ts'
                    ],
                    'index' => 288,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.84,
                        'title' => '',
                        'uri' => 'e13c424277e000282.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000282.ts'
                    ],
                    'index' => 289,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000283.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000283.ts'
                    ],
                    'index' => 290,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.28,
                        'title' => '',
                        'uri' => 'e13c424277e000284.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000284.ts'
                    ],
                    'index' => 291,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => 'e13c424277e000285.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000285.ts'
                    ],
                    'index' => 292,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.68,
                        'title' => '',
                        'uri' => 'e13c424277e000286.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000286.ts'
                    ],
                    'index' => 293,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => 'e13c424277e000287.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000287.ts'
                    ],
                    'index' => 294,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000288.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000288.ts'
                    ],
                    'index' => 295,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.04,
                        'title' => '',
                        'uri' => 'e13c424277e000289.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000289.ts'
                    ],
                    'index' => 296,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000290.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000290.ts'
                    ],
                    'index' => 297,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000291.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000291.ts'
                    ],
                    'index' => 298,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => 'e13c424277e000292.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000292.ts'
                    ],
                    'index' => 299,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000293.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000293.ts'
                    ],
                    'index' => 300,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000294.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000294.ts'
                    ],
                    'index' => 301,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.36,
                        'title' => '',
                        'uri' => 'e13c424277e000295.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000295.ts'
                    ],
                    'index' => 302,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => 'e13c424277e000296.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000296.ts'
                    ],
                    'index' => 303,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000297.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000297.ts'
                    ],
                    'index' => 304,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'e13c424277e000298.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000298.ts'
                    ],
                    'index' => 305,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000299.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000299.ts'
                    ],
                    'index' => 306,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000300.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000300.ts'
                    ],
                    'index' => 307,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.72,
                        'title' => '',
                        'uri' => 'e13c424277e000301.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000301.ts'
                    ],
                    'index' => 308,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000302.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000302.ts'
                    ],
                    'index' => 309,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000303.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000303.ts'
                    ],
                    'index' => 310,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => 'e13c424277e000304.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000304.ts'
                    ],
                    'index' => 311,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'e13c424277e000305.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000305.ts'
                    ],
                    'index' => 312,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000306.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000306.ts'
                    ],
                    'index' => 313,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => 'e13c424277e000307.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000307.ts'
                    ],
                    'index' => 314,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.72,
                        'title' => '',
                        'uri' => 'e13c424277e000308.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000308.ts'
                    ],
                    'index' => 315,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000309.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000309.ts'
                    ],
                    'index' => 316,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000310.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000310.ts'
                    ],
                    'index' => 317,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => 'e13c424277e000311.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000311.ts'
                    ],
                    'index' => 318,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000312.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000312.ts'
                    ],
                    'index' => 319,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000313.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000313.ts'
                    ],
                    'index' => 320,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000314.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000314.ts'
                    ],
                    'index' => 321,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => 'e13c424277e000315.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000315.ts'
                    ],
                    'index' => 322,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000316.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000316.ts'
                    ],
                    'index' => 323,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000317.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000317.ts'
                    ],
                    'index' => 324,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'e13c424277e000318.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000318.ts'
                    ],
                    'index' => 325,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000319.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000319.ts'
                    ],
                    'index' => 326,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'e13c424277e000320.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000320.ts'
                    ],
                    'index' => 327,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000321.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000321.ts'
                    ],
                    'index' => 328,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.24,
                        'title' => '',
                        'uri' => 'e13c424277e000322.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000322.ts'
                    ],
                    'index' => 329,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.76,
                        'title' => '',
                        'uri' => 'e13c424277e000323.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000323.ts'
                    ],
                    'index' => 330,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => 'e13c424277e000324.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000324.ts'
                    ],
                    'index' => 331,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000325.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000325.ts'
                    ],
                    'index' => 332,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000326.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000326.ts'
                    ],
                    'index' => 333,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000327.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000327.ts'
                    ],
                    'index' => 334,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000328.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000328.ts'
                    ],
                    'index' => 335,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'e13c424277e000329.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000329.ts'
                    ],
                    'index' => 336,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000330.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000330.ts'
                    ],
                    'index' => 337,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => 'e13c424277e000331.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000331.ts'
                    ],
                    'index' => 338,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => 'e13c424277e000332.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000332.ts'
                    ],
                    'index' => 339,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000333.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000333.ts'
                    ],
                    'index' => 340,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'e13c424277e000334.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000334.ts'
                    ],
                    'index' => 341,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000335.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000335.ts'
                    ],
                    'index' => 342,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'e13c424277e000336.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000336.ts'
                    ],
                    'index' => 343,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.84,
                        'title' => '',
                        'uri' => 'e13c424277e000337.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000337.ts'
                    ],
                    'index' => 344,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.52,
                        'title' => '',
                        'uri' => 'e13c424277e000338.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000338.ts'
                    ],
                    'index' => 345,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000339.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000339.ts'
                    ],
                    'index' => 346,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000340.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000340.ts'
                    ],
                    'index' => 347,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000341.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000341.ts'
                    ],
                    'index' => 348,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'e13c424277e000342.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000342.ts'
                    ],
                    'index' => 349,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'e13c424277e000343.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000343.ts'
                    ],
                    'index' => 350,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000344.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000344.ts'
                    ],
                    'index' => 351,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000345.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000345.ts'
                    ],
                    'index' => 352,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'e13c424277e000346.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000346.ts'
                    ],
                    'index' => 353,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => 'e13c424277e000347.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000347.ts'
                    ],
                    'index' => 354,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.48,
                        'title' => '',
                        'uri' => 'e13c424277e000348.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000348.ts'
                    ],
                    'index' => 355,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => 'e13c424277e000349.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000349.ts'
                    ],
                    'index' => 356,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 0.72,
                        'title' => '',
                        'uri' => 'e13c424277e000350.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000350.ts'
                    ],
                    'index' => 357,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000351.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000351.ts'
                    ],
                    'index' => 358,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000352.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000352.ts'
                    ],
                    'index' => 359,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => 'e13c424277e000353.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000353.ts'
                    ],
                    'index' => 360,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000354.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000354.ts'
                    ],
                    'index' => 361,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => 'e13c424277e000355.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000355.ts'
                    ],
                    'index' => 362,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000356.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000356.ts'
                    ],
                    'index' => 363,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000357.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000357.ts'
                    ],
                    'index' => 364,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000358.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000358.ts'
                    ],
                    'index' => 365,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'e13c424277e000359.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000359.ts'
                    ],
                    'index' => 366,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000360.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000360.ts'
                    ],
                    'index' => 367,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => 'e13c424277e000361.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000361.ts'
                    ],
                    'index' => 368,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000362.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000362.ts'
                    ],
                    'index' => 369,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000363.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000363.ts'
                    ],
                    'index' => 370,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000364.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000364.ts'
                    ],
                    'index' => 371,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000365.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000365.ts'
                    ],
                    'index' => 372,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000366.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000366.ts'
                    ],
                    'index' => 373,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000367.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000367.ts'
                    ],
                    'index' => 374,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => 'e13c424277e000368.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000368.ts'
                    ],
                    'index' => 375,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000369.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000369.ts'
                    ],
                    'index' => 376,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000370.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000370.ts'
                    ],
                    'index' => 377,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000371.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000371.ts'
                    ],
                    'index' => 378,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000372.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000372.ts'
                    ],
                    'index' => 379,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'e13c424277e000373.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000373.ts'
                    ],
                    'index' => 380,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000374.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000374.ts'
                    ],
                    'index' => 381,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.16,
                        'title' => '',
                        'uri' => 'e13c424277e000375.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000375.ts'
                    ],
                    'index' => 382,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000376.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000376.ts'
                    ],
                    'index' => 383,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.8,
                        'title' => '',
                        'uri' => 'e13c424277e000377.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000377.ts'
                    ],
                    'index' => 384,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000378.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000378.ts'
                    ],
                    'index' => 385,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000379.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000379.ts'
                    ],
                    'index' => 386,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.04,
                        'title' => '',
                        'uri' => 'e13c424277e000380.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000380.ts'
                    ],
                    'index' => 387,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => 'e13c424277e000381.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000381.ts'
                    ],
                    'index' => 388,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000382.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000382.ts'
                    ],
                    'index' => 389,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000383.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000383.ts'
                    ],
                    'index' => 390,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000384.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000384.ts'
                    ],
                    'index' => 391,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000385.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000385.ts'
                    ],
                    'index' => 392,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000386.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000386.ts'
                    ],
                    'index' => 393,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000387.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000387.ts'
                    ],
                    'index' => 394,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000388.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000388.ts'
                    ],
                    'index' => 395,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000389.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000389.ts'
                    ],
                    'index' => 396,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000390.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000390.ts'
                    ],
                    'index' => 397,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000391.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000391.ts'
                    ],
                    'index' => 398,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.8,
                        'title' => '',
                        'uri' => 'e13c424277e000392.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000392.ts'
                    ],
                    'index' => 399,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000393.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000393.ts'
                    ],
                    'index' => 400,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000394.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000394.ts'
                    ],
                    'index' => 401,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000395.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000395.ts'
                    ],
                    'index' => 402,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000396.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000396.ts'
                    ],
                    'index' => 403,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000397.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000397.ts'
                    ],
                    'index' => 404,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.44,
                        'title' => '',
                        'uri' => 'e13c424277e000398.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000398.ts'
                    ],
                    'index' => 405,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000399.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000399.ts'
                    ],
                    'index' => 406,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000400.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000400.ts'
                    ],
                    'index' => 407,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000401.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000401.ts'
                    ],
                    'index' => 408,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000402.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000402.ts'
                    ],
                    'index' => 409,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000403.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000403.ts'
                    ],
                    'index' => 410,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.16,
                        'title' => '',
                        'uri' => 'e13c424277e000404.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000404.ts'
                    ],
                    'index' => 411,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000405.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000405.ts'
                    ],
                    'index' => 412,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000406.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000406.ts'
                    ],
                    'index' => 413,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000407.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000407.ts'
                    ],
                    'index' => 414,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => 'e13c424277e000408.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000408.ts'
                    ],
                    'index' => 415,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000409.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000409.ts'
                    ],
                    'index' => 416,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.4,
                        'title' => '',
                        'uri' => 'e13c424277e000410.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000410.ts'
                    ],
                    'index' => 417,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000411.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000411.ts'
                    ],
                    'index' => 418,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000412.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000412.ts'
                    ],
                    'index' => 419,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000413.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000413.ts'
                    ],
                    'index' => 420,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000414.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000414.ts'
                    ],
                    'index' => 421,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000415.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000415.ts'
                    ],
                    'index' => 422,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000416.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000416.ts'
                    ],
                    'index' => 423,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000417.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000417.ts'
                    ],
                    'index' => 424,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000418.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000418.ts'
                    ],
                    'index' => 425,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000419.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000419.ts'
                    ],
                    'index' => 426,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000420.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000420.ts'
                    ],
                    'index' => 427,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000421.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000421.ts'
                    ],
                    'index' => 428,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => 'e13c424277e000422.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000422.ts'
                    ],
                    'index' => 429,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000423.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000423.ts'
                    ],
                    'index' => 430,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3,
                        'title' => '',
                        'uri' => 'e13c424277e000424.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000424.ts'
                    ],
                    'index' => 431,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000425.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000425.ts'
                    ],
                    'index' => 432,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000426.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000426.ts'
                    ],
                    'index' => 433,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000427.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000427.ts'
                    ],
                    'index' => 434,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.2,
                        'title' => '',
                        'uri' => 'e13c424277e000428.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000428.ts'
                    ],
                    'index' => 435,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000429.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000429.ts'
                    ],
                    'index' => 436,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.84,
                        'title' => '',
                        'uri' => 'e13c424277e000430.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000430.ts'
                    ],
                    'index' => 437,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000431.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000431.ts'
                    ],
                    'index' => 438,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000432.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000432.ts'
                    ],
                    'index' => 439,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000433.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000433.ts'
                    ],
                    'index' => 440,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.04,
                        'title' => '',
                        'uri' => 'e13c424277e000434.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000434.ts'
                    ],
                    'index' => 441,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.24,
                        'title' => '',
                        'uri' => 'e13c424277e000435.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000435.ts'
                    ],
                    'index' => 442,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.76,
                        'title' => '',
                        'uri' => 'e13c424277e000436.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000436.ts'
                    ],
                    'index' => 443,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => 'e13c424277e000437.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000437.ts'
                    ],
                    'index' => 444,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000438.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000438.ts'
                    ],
                    'index' => 445,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000439.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000439.ts'
                    ],
                    'index' => 446,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.6,
                        'title' => '',
                        'uri' => 'e13c424277e000440.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000440.ts'
                    ],
                    'index' => 447,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.56,
                        'title' => '',
                        'uri' => 'e13c424277e000441.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000441.ts'
                    ],
                    'index' => 448,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => 'e13c424277e000442.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000442.ts'
                    ],
                    'index' => 449,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000443.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000443.ts'
                    ],
                    'index' => 450,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 0.52,
                        'title' => '',
                        'uri' => 'e13c424277e000444.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000444.ts'
                    ],
                    'index' => 451,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.36,
                        'title' => '',
                        'uri' => 'e13c424277e000445.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000445.ts'
                    ],
                    'index' => 452,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000446.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000446.ts'
                    ],
                    'index' => 453,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000447.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000447.ts'
                    ],
                    'index' => 454,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'e13c424277e000448.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000448.ts'
                    ],
                    'index' => 455,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000449.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000449.ts'
                    ],
                    'index' => 456,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000450.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000450.ts'
                    ],
                    'index' => 457,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000451.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000451.ts'
                    ],
                    'index' => 458,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000452.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000452.ts'
                    ],
                    'index' => 459,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.12,
                        'title' => '',
                        'uri' => 'e13c424277e000453.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000453.ts'
                    ],
                    'index' => 460,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000454.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000454.ts'
                    ],
                    'index' => 461,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000455.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000455.ts'
                    ],
                    'index' => 462,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000456.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000456.ts'
                    ],
                    'index' => 463,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000457.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000457.ts'
                    ],
                    'index' => 464,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000458.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000458.ts'
                    ],
                    'index' => 465,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'e13c424277e000459.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000459.ts'
                    ],
                    'index' => 466,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000460.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000460.ts'
                    ],
                    'index' => 467,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000461.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000461.ts'
                    ],
                    'index' => 468,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000462.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000462.ts'
                    ],
                    'index' => 469,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'e13c424277e000463.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000463.ts'
                    ],
                    'index' => 470,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.4,
                        'title' => '',
                        'uri' => 'e13c424277e000464.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000464.ts'
                    ],
                    'index' => 471,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'e13c424277e000465.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000465.ts'
                    ],
                    'index' => 472,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'e13c424277e000466.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000466.ts'
                    ],
                    'index' => 473,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.24,
                        'title' => '',
                        'uri' => 'e13c424277e000467.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000467.ts'
                    ],
                    'index' => 474,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000468.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000468.ts'
                    ],
                    'index' => 475,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => 'e13c424277e000469.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000469.ts'
                    ],
                    'index' => 476,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => 'e13c424277e000470.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000470.ts'
                    ],
                    'index' => 477,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'e13c424277e000471.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000471.ts'
                    ],
                    'index' => 478,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000472.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000472.ts'
                    ],
                    'index' => 479,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000473.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000473.ts'
                    ],
                    'index' => 480,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000474.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000474.ts'
                    ],
                    'index' => 481,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.92,
                        'title' => '',
                        'uri' => 'e13c424277e000475.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000475.ts'
                    ],
                    'index' => 482,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => 'e13c424277e000476.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000476.ts'
                    ],
                    'index' => 483,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000477.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000477.ts'
                    ],
                    'index' => 484,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000478.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000478.ts'
                    ],
                    'index' => 485,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'e13c424277e000479.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000479.ts'
                    ],
                    'index' => 486,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => 'e13c424277e000480.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000480.ts'
                    ],
                    'index' => 487,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000481.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000481.ts'
                    ],
                    'index' => 488,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.48,
                        'title' => '',
                        'uri' => 'e13c424277e000482.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000482.ts'
                    ],
                    'index' => 489,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'e13c424277e000483.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000483.ts'
                    ],
                    'index' => 490,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.84,
                        'title' => '',
                        'uri' => 'e13c424277e000484.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000484.ts'
                    ],
                    'index' => 491,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000485.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000485.ts'
                    ],
                    'index' => 492,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000486.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000486.ts'
                    ],
                    'index' => 493,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.36,
                        'title' => '',
                        'uri' => 'e13c424277e000487.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000487.ts'
                    ],
                    'index' => 494,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.08,
                        'title' => '',
                        'uri' => 'e13c424277e000488.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000488.ts'
                    ],
                    'index' => 495,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000489.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000489.ts'
                    ],
                    'index' => 496,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.08,
                        'title' => '',
                        'uri' => 'e13c424277e000490.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000490.ts'
                    ],
                    'index' => 497,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'e13c424277e000491.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000491.ts'
                    ],
                    'index' => 498,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000492.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000492.ts'
                    ],
                    'index' => 499,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.88,
                        'title' => '',
                        'uri' => 'e13c424277e000493.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000493.ts'
                    ],
                    'index' => 500,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702688.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702688.ts'
                    ],
                    'index' => 501,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ],
                        [
                            'name' => 'sequence_jump_forward',
                            'description' => '序列号向前跳跃 > 100000 可能表示广告插播'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702689.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702689.ts'
                    ],
                    'index' => 502,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702690.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702690.ts'
                    ],
                    'index' => 503,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702691.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702691.ts'
                    ],
                    'index' => 504,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702692.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702692.ts'
                    ],
                    'index' => 505,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e0702693.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702693.ts'
                    ],
                    'index' => 506,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2,
                        'title' => '',
                        'uri' => 'e13c424277e0702694.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e0702694.ts'
                    ],
                    'index' => 507,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000494.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000494.ts'
                    ],
                    'index' => 508,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ],
                        [
                            'name' => 'sequence_jump_backward',
                            'description' => '序列号向后跳跃 > 100000 可能表示广告结束'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'e13c424277e000495.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000495.ts'
                    ],
                    'index' => 509,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000496.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000496.ts'
                    ],
                    'index' => 510,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.76,
                        'title' => '',
                        'uri' => 'e13c424277e000497.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000497.ts'
                    ],
                    'index' => 511,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000498.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000498.ts'
                    ],
                    'index' => 512,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.32,
                        'title' => '',
                        'uri' => 'e13c424277e000499.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000499.ts'
                    ],
                    'index' => 513,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000500.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000500.ts'
                    ],
                    'index' => 514,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000501.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000501.ts'
                    ],
                    'index' => 515,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000502.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000502.ts'
                    ],
                    'index' => 516,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.68,
                        'title' => '',
                        'uri' => 'e13c424277e000503.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000503.ts'
                    ],
                    'index' => 517,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000504.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000504.ts'
                    ],
                    'index' => 518,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000505.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000505.ts'
                    ],
                    'index' => 519,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000506.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000506.ts'
                    ],
                    'index' => 520,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'e13c424277e000507.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000507.ts'
                    ],
                    'index' => 521,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.76,
                        'title' => '',
                        'uri' => 'e13c424277e000508.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000508.ts'
                    ],
                    'index' => 522,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000509.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000509.ts'
                    ],
                    'index' => 523,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000510.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000510.ts'
                    ],
                    'index' => 524,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.44,
                        'title' => '',
                        'uri' => 'e13c424277e000511.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000511.ts'
                    ],
                    'index' => 525,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000512.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000512.ts'
                    ],
                    'index' => 526,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.72,
                        'title' => '',
                        'uri' => 'e13c424277e000513.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000513.ts'
                    ],
                    'index' => 527,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000514.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000514.ts'
                    ],
                    'index' => 528,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000515.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000515.ts'
                    ],
                    'index' => 529,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.52,
                        'title' => '',
                        'uri' => 'e13c424277e000516.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000516.ts'
                    ],
                    'index' => 530,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 0.48,
                        'title' => '',
                        'uri' => 'e13c424277e000517.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000517.ts'
                    ],
                    'index' => 531,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000518.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000518.ts'
                    ],
                    'index' => 532,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => 'e13c424277e000519.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000519.ts'
                    ],
                    'index' => 533,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.92,
                        'title' => '',
                        'uri' => 'e13c424277e000520.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000520.ts'
                    ],
                    'index' => 534,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000521.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000521.ts'
                    ],
                    'index' => 535,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000522.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000522.ts'
                    ],
                    'index' => 536,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000523.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000523.ts'
                    ],
                    'index' => 537,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.96,
                        'title' => '',
                        'uri' => 'e13c424277e000524.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000524.ts'
                    ],
                    'index' => 538,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000525.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000525.ts'
                    ],
                    'index' => 539,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.32,
                        'title' => '',
                        'uri' => 'e13c424277e000526.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000526.ts'
                    ],
                    'index' => 540,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'e13c424277e000527.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000527.ts'
                    ],
                    'index' => 541,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000528.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000528.ts'
                    ],
                    'index' => 542,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.72,
                        'title' => '',
                        'uri' => 'e13c424277e000529.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000529.ts'
                    ],
                    'index' => 543,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.12,
                        'title' => '',
                        'uri' => 'e13c424277e000530.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000530.ts'
                    ],
                    'index' => 544,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.2,
                        'title' => '',
                        'uri' => 'e13c424277e000531.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000531.ts'
                    ],
                    'index' => 545,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 0.92,
                        'title' => '',
                        'uri' => 'e13c424277e000532.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000532.ts'
                    ],
                    'index' => 546,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'e13c424277e000533.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000533.ts'
                    ],
                    'index' => 547,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.44,
                        'title' => '',
                        'uri' => 'e13c424277e000534.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000534.ts'
                    ],
                    'index' => 548,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.2,
                        'title' => '',
                        'uri' => 'e13c424277e000535.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000535.ts'
                    ],
                    'index' => 549,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'e13c424277e000536.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000536.ts'
                    ],
                    'index' => 550,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.6,
                        'title' => '',
                        'uri' => 'e13c424277e000537.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000537.ts'
                    ],
                    'index' => 551,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000538.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000538.ts'
                    ],
                    'index' => 552,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.88,
                        'title' => '',
                        'uri' => 'e13c424277e000539.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000539.ts'
                    ],
                    'index' => 553,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'e13c424277e000540.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000540.ts'
                    ],
                    'index' => 554,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => 'e13c424277e000541.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000541.ts'
                    ],
                    'index' => 555,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000542.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000542.ts'
                    ],
                    'index' => 556,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000543.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000543.ts'
                    ],
                    'index' => 557,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'e13c424277e000544.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000544.ts'
                    ],
                    'index' => 558,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => 'e13c424277e000545.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000545.ts'
                    ],
                    'index' => 559,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000546.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000546.ts'
                    ],
                    'index' => 560,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.96,
                        'title' => '',
                        'uri' => 'e13c424277e000547.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000547.ts'
                    ],
                    'index' => 561,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000548.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000548.ts'
                    ],
                    'index' => 562,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.44,
                        'title' => '',
                        'uri' => 'e13c424277e000549.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000549.ts'
                    ],
                    'index' => 563,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.56,
                        'title' => '',
                        'uri' => 'e13c424277e000550.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000550.ts'
                    ],
                    'index' => 564,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'e13c424277e000551.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000551.ts'
                    ],
                    'index' => 565,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => 'e13c424277e000552.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000552.ts'
                    ],
                    'index' => 566,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000553.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000553.ts'
                    ],
                    'index' => 567,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.52,
                        'title' => '',
                        'uri' => 'e13c424277e000554.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000554.ts'
                    ],
                    'index' => 568,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.32,
                        'title' => '',
                        'uri' => 'e13c424277e000555.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000555.ts'
                    ],
                    'index' => 569,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.68,
                        'title' => '',
                        'uri' => 'e13c424277e000556.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000556.ts'
                    ],
                    'index' => 570,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000557.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000557.ts'
                    ],
                    'index' => 571,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => 'e13c424277e000558.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000558.ts'
                    ],
                    'index' => 572,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => 'e13c424277e000559.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000559.ts'
                    ],
                    'index' => 573,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000560.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000560.ts'
                    ],
                    'index' => 574,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.48,
                        'title' => '',
                        'uri' => 'e13c424277e000561.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000561.ts'
                    ],
                    'index' => 575,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000562.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000562.ts'
                    ],
                    'index' => 576,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => 'e13c424277e000563.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000563.ts'
                    ],
                    'index' => 577,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000564.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000564.ts'
                    ],
                    'index' => 578,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.36,
                        'title' => '',
                        'uri' => 'e13c424277e000565.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000565.ts'
                    ],
                    'index' => 579,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.48,
                        'title' => '',
                        'uri' => 'e13c424277e000566.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000566.ts'
                    ],
                    'index' => 580,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000567.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000567.ts'
                    ],
                    'index' => 581,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.56,
                        'title' => '',
                        'uri' => 'e13c424277e000568.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000568.ts'
                    ],
                    'index' => 582,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'e13c424277e000569.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000569.ts'
                    ],
                    'index' => 583,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => 'e13c424277e000570.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000570.ts'
                    ],
                    'index' => 584,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5,
                        'title' => '',
                        'uri' => 'e13c424277e000571.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000571.ts'
                    ],
                    'index' => 585,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.72,
                        'title' => '',
                        'uri' => 'e13c424277e000572.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000572.ts'
                    ],
                    'index' => 586,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.4,
                        'title' => '',
                        'uri' => 'e13c424277e000573.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000573.ts'
                    ],
                    'index' => 587,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000574.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000574.ts'
                    ],
                    'index' => 588,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.12,
                        'title' => '',
                        'uri' => 'e13c424277e000575.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000575.ts'
                    ],
                    'index' => 589,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.64,
                        'title' => '',
                        'uri' => 'e13c424277e000576.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000576.ts'
                    ],
                    'index' => 590,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000577.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000577.ts'
                    ],
                    'index' => 591,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000578.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000578.ts'
                    ],
                    'index' => 592,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'e13c424277e000579.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000579.ts'
                    ],
                    'index' => 593,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000580.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000580.ts'
                    ],
                    'index' => 594,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000581.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000581.ts'
                    ],
                    'index' => 595,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.56,
                        'title' => '',
                        'uri' => 'e13c424277e000582.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000582.ts'
                    ],
                    'index' => 596,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => 'e13c424277e000583.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000583.ts'
                    ],
                    'index' => 597,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.8,
                        'title' => '',
                        'uri' => 'e13c424277e000584.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000584.ts'
                    ],
                    'index' => 598,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000585.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000585.ts'
                    ],
                    'index' => 599,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'e13c424277e000586.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000586.ts'
                    ],
                    'index' => 600,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.2,
                        'title' => '',
                        'uri' => 'e13c424277e000587.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000587.ts'
                    ],
                    'index' => 601,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.84,
                        'title' => '',
                        'uri' => 'e13c424277e000588.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000588.ts'
                    ],
                    'index' => 602,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 7.68,
                        'title' => '',
                        'uri' => 'e13c424277e000589.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000589.ts'
                    ],
                    'index' => 603,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.64,
                        'title' => '',
                        'uri' => 'e13c424277e000590.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000590.ts'
                    ],
                    'index' => 604,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000591.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000591.ts'
                    ],
                    'index' => 605,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000592.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000592.ts'
                    ],
                    'index' => 606,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.76,
                        'title' => '',
                        'uri' => 'e13c424277e000593.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000593.ts'
                    ],
                    'index' => 607,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.24,
                        'title' => '',
                        'uri' => 'e13c424277e000594.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000594.ts'
                    ],
                    'index' => 608,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000595.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000595.ts'
                    ],
                    'index' => 609,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.24,
                        'title' => '',
                        'uri' => 'e13c424277e000596.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000596.ts'
                    ],
                    'index' => 610,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000597.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000597.ts'
                    ],
                    'index' => 611,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000598.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000598.ts'
                    ],
                    'index' => 612,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000599.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000599.ts'
                    ],
                    'index' => 613,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.64,
                        'title' => '',
                        'uri' => 'e13c424277e000600.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000600.ts'
                    ],
                    'index' => 614,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'e13c424277e000601.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000601.ts'
                    ],
                    'index' => 615,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'e13c424277e000602.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000602.ts'
                    ],
                    'index' => 616,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 7.04,
                        'title' => '',
                        'uri' => 'e13c424277e000603.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000603.ts'
                    ],
                    'index' => 617,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000604.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000604.ts'
                    ],
                    'index' => 618,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => 'e13c424277e000605.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000605.ts'
                    ],
                    'index' => 619,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'e13c424277e000606.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000606.ts'
                    ],
                    'index' => 620,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 6.16,
                        'title' => '',
                        'uri' => 'e13c424277e000607.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000607.ts'
                    ],
                    'index' => 621,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 0.64,
                        'title' => '',
                        'uri' => 'e13c424277e000608.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000608.ts'
                    ],
                    'index' => 622,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000609.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000609.ts'
                    ],
                    'index' => 623,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000610.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000610.ts'
                    ],
                    'index' => 624,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.08,
                        'title' => '',
                        'uri' => 'e13c424277e000611.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000611.ts'
                    ],
                    'index' => 625,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => 'e13c424277e000612.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000612.ts'
                    ],
                    'index' => 626,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'e13c424277e000613.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000613.ts'
                    ],
                    'index' => 627,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.16,
                        'title' => '',
                        'uri' => 'e13c424277e000614.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000614.ts'
                    ],
                    'index' => 628,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000615.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000615.ts'
                    ],
                    'index' => 629,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000616.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000616.ts'
                    ],
                    'index' => 630,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.16,
                        'title' => '',
                        'uri' => 'e13c424277e000617.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000617.ts'
                    ],
                    'index' => 631,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 7.32,
                        'title' => '',
                        'uri' => 'e13c424277e000618.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000618.ts'
                    ],
                    'index' => 632,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.64,
                        'title' => '',
                        'uri' => 'e13c424277e000619.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000619.ts'
                    ],
                    'index' => 633,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000620.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000620.ts'
                    ],
                    'index' => 634,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.36,
                        'title' => '',
                        'uri' => 'e13c424277e000621.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000621.ts'
                    ],
                    'index' => 635,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000622.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000622.ts'
                    ],
                    'index' => 636,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000623.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000623.ts'
                    ],
                    'index' => 637,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.28,
                        'title' => '',
                        'uri' => 'e13c424277e000624.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000624.ts'
                    ],
                    'index' => 638,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.92,
                        'title' => '',
                        'uri' => 'e13c424277e000625.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000625.ts'
                    ],
                    'index' => 639,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000626.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000626.ts'
                    ],
                    'index' => 640,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000627.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000627.ts'
                    ],
                    'index' => 641,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.92,
                        'title' => '',
                        'uri' => 'e13c424277e000628.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000628.ts'
                    ],
                    'index' => 642,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.04,
                        'title' => '',
                        'uri' => 'e13c424277e000629.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000629.ts'
                    ],
                    'index' => 643,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.24,
                        'title' => '',
                        'uri' => 'e13c424277e000630.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000630.ts'
                    ],
                    'index' => 644,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.6,
                        'title' => '',
                        'uri' => 'e13c424277e000631.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000631.ts'
                    ],
                    'index' => 645,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000632.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000632.ts'
                    ],
                    'index' => 646,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => 'e13c424277e000633.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000633.ts'
                    ],
                    'index' => 647,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => 'e13c424277e000634.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000634.ts'
                    ],
                    'index' => 648,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000635.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000635.ts'
                    ],
                    'index' => 649,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.72,
                        'title' => '',
                        'uri' => 'e13c424277e000636.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000636.ts'
                    ],
                    'index' => 650,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000637.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000637.ts'
                    ],
                    'index' => 651,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.32,
                        'title' => '',
                        'uri' => 'e13c424277e000638.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000638.ts'
                    ],
                    'index' => 652,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'e13c424277e000639.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000639.ts'
                    ],
                    'index' => 653,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000640.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000640.ts'
                    ],
                    'index' => 654,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.88,
                        'title' => '',
                        'uri' => 'e13c424277e000641.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000641.ts'
                    ],
                    'index' => 655,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.96,
                        'title' => '',
                        'uri' => 'e13c424277e000642.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000642.ts'
                    ],
                    'index' => 656,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.48,
                        'title' => '',
                        'uri' => 'e13c424277e000643.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000643.ts'
                    ],
                    'index' => 657,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.28,
                        'title' => '',
                        'uri' => 'e13c424277e000644.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000644.ts'
                    ],
                    'index' => 658,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.12,
                        'title' => '',
                        'uri' => 'e13c424277e000645.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000645.ts'
                    ],
                    'index' => 659,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.36,
                        'title' => '',
                        'uri' => 'e13c424277e000646.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000646.ts'
                    ],
                    'index' => 660,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5.24,
                        'title' => '',
                        'uri' => 'e13c424277e000647.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000647.ts'
                    ],
                    'index' => 661,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.08,
                        'title' => '',
                        'uri' => 'e13c424277e000648.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000648.ts'
                    ],
                    'index' => 662,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.32,
                        'title' => '',
                        'uri' => 'e13c424277e000649.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000649.ts'
                    ],
                    'index' => 663,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000650.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000650.ts'
                    ],
                    'index' => 664,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 6.52,
                        'title' => '',
                        'uri' => 'e13c424277e000651.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000651.ts'
                    ],
                    'index' => 665,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000652.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000652.ts'
                    ],
                    'index' => 666,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000653.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000653.ts'
                    ],
                    'index' => 667,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 1.96,
                        'title' => '',
                        'uri' => 'e13c424277e000654.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000654.ts'
                    ],
                    'index' => 668,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'short-duration',
                            'description' => '片段时长过短，可能是广告'
                        ],
                        [
                            'name' => 'short_segment',
                            'description' => '极短片段 (<2秒) 可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.88,
                        'title' => '',
                        'uri' => 'e13c424277e000655.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000655.ts'
                    ],
                    'index' => 669,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.28,
                        'title' => '',
                        'uri' => 'e13c424277e000656.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000656.ts'
                    ],
                    'index' => 670,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000657.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000657.ts'
                    ],
                    'index' => 671,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.84,
                        'title' => '',
                        'uri' => 'e13c424277e000658.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000658.ts'
                    ],
                    'index' => 672,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.88,
                        'title' => '',
                        'uri' => 'e13c424277e000659.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000659.ts'
                    ],
                    'index' => 673,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.4,
                        'title' => '',
                        'uri' => 'e13c424277e000660.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000660.ts'
                    ],
                    'index' => 674,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.28,
                        'title' => '',
                        'uri' => 'e13c424277e000661.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000661.ts'
                    ],
                    'index' => 675,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 5,
                        'title' => '',
                        'uri' => 'e13c424277e000662.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000662.ts'
                    ],
                    'index' => 676,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.8,
                        'title' => '',
                        'uri' => 'e13c424277e000663.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000663.ts'
                    ],
                    'index' => 677,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.8,
                        'title' => '',
                        'uri' => 'e13c424277e000664.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000664.ts'
                    ],
                    'index' => 678,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 2.32,
                        'title' => '',
                        'uri' => 'e13c424277e000665.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000665.ts'
                    ],
                    'index' => 679,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'e13c424277e000666.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000666.ts'
                    ],
                    'index' => 680,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4.76,
                        'title' => '',
                        'uri' => 'e13c424277e000667.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000667.ts'
                    ],
                    'index' => 681,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.16,
                        'title' => '',
                        'uri' => 'e13c424277e000668.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000668.ts'
                    ],
                    'index' => 682,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.52,
                        'title' => '',
                        'uri' => 'e13c424277e000669.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000669.ts'
                    ],
                    'index' => 683,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.4,
                        'title' => '',
                        'uri' => 'e13c424277e000670.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000670.ts'
                    ],
                    'index' => 684,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.44,
                        'title' => '',
                        'uri' => 'e13c424277e000671.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000671.ts'
                    ],
                    'index' => 685,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 5.76,
                        'title' => '',
                        'uri' => 'e13c424277e000672.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000672.ts'
                    ],
                    'index' => 686,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 3.8,
                        'title' => '',
                        'uri' => 'e13c424277e000673.ts',
                        'byteRange' => '',
                        'discontinuity' => true,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000673.ts'
                    ],
                    'index' => 687,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'discontinuity',
                            'description' => '存在不连续标记，可能是插播广告'
                        ],
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ],
                        [
                            'name' => 'discontinuity',
                            'description' => 'DISCONTINUITY 标记表示插播切换'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 2.64,
                        'title' => '',
                        'uri' => 'e13c424277e000674.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000674.ts'
                    ],
                    'index' => 688,
                    'isAd' => false,
                    'matchedRules' => []
                ],
                [
                    'segment' => [
                        'duration' => 4.68,
                        'title' => '',
                        'uri' => 'e13c424277e000675.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000675.ts'
                    ],
                    'index' => 689,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000676.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000676.ts'
                    ],
                    'index' => 690,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000677.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000677.ts'
                    ],
                    'index' => 691,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000678.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000678.ts'
                    ],
                    'index' => 692,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 4,
                        'title' => '',
                        'uri' => 'e13c424277e000679.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000679.ts'
                    ],
                    'index' => 693,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ],
                [
                    'segment' => [
                        'duration' => 3.6,
                        'title' => '',
                        'uri' => 'e13c424277e000680.ts',
                        'byteRange' => '',
                        'discontinuity' => false,
                        'tags' => [],
                        'absoluteUri' => 'https://v.lfthirtytwo.com/20260623/7885_1d9dba16/2000k/hls/e13c424277e000680.ts'
                    ],
                    'index' => 694,
                    'isAd' => true,
                    'matchedRules' => [
                        [
                            'name' => 'repetitive-duration',
                            'description' => '重复出现相近时长的片段，可能是广告'
                        ]
                    ]
                ]
            ],
            'totalCount' => 695,
            'adCount' => 482,
            'discontinuityCount' => 72,
            'sequenceJumps' => [
                [
                    'index' => 74,
                    'prevSeq' => 73,
                    'currentSeq' => 702681,
                    'jump' => 702608,
                    'prevUri' => 'e13c424277e000073.ts',
                    'currentUri' => 'e13c424277e0702681.ts'
                ],
                [
                    'index' => 81,
                    'prevSeq' => 702687,
                    'currentSeq' => 74,
                    'jump' => -702613,
                    'prevUri' => 'e13c424277e0702687.ts',
                    'currentUri' => 'e13c424277e000074.ts'
                ],
                [
                    'index' => 501,
                    'prevSeq' => 493,
                    'currentSeq' => 702688,
                    'jump' => 702195,
                    'prevUri' => 'e13c424277e000493.ts',
                    'currentUri' => 'e13c424277e0702688.ts'
                ],
                [
                    'index' => 508,
                    'prevSeq' => 702694,
                    'currentSeq' => 494,
                    'jump' => -702200,
                    'prevUri' => 'e13c424277e0702694.ts',
                    'currentUri' => 'e13c424277e000494.ts'
                ]
            ],
            'durationDistribution' => [
                'min' => 0.4,
                'max' => 7.84,
                'avg' => 3.9956834532374,
                'buckets' => [
                    '0.4' => 4,
                    '0.5' => 1,
                    '0.6' => 1,
                    '0.7' => 1,
                    '0.8' => 1,
                    '0.9' => 1,
                    1 => 1,
                    '1.2' => 1,
                    '1.3' => 3,
                    '1.4' => 3,
                    '1.5' => 1,
                    '1.6' => 5,
                    '1.7' => 1,
                    '1.8' => 4,
                    '1.9' => 4,
                    2 => 7,
                    '2.1' => 6,
                    '2.2' => 13,
                    '2.3' => 7,
                    '2.4' => 12,
                    '2.5' => 3,
                    '2.6' => 9,
                    '2.7' => 11,
                    '2.8' => 17,
                    '2.9' => 10,
                    3 => 8,
                    '3.1' => 8,
                    '3.2' => 16,
                    '3.3' => 13,
                    '3.4' => 15,
                    '3.5' => 9,
                    '3.6' => 16,
                    '3.7' => 7,
                    '3.8' => 21,
                    '3.9' => 12,
                    4 => 245,
                    '4.1' => 10,
                    '4.2' => 8,
                    '4.3' => 5,
                    '4.4' => 12,
                    '4.5' => 5,
                    '4.6' => 15,
                    '4.7' => 8,
                    '4.8' => 11,
                    '4.9' => 4,
                    5 => 8,
                    '5.1' => 7,
                    '5.2' => 7,
                    '5.3' => 7,
                    '5.4' => 7,
                    '5.5' => 3,
                    '5.6' => 7,
                    '5.7' => 11,
                    '5.8' => 8,
                    '5.9' => 6,
                    6 => 2,
                    '6.1' => 4,
                    '6.2' => 3,
                    '6.3' => 6,
                    '6.4' => 4,
                    '6.5' => 6,
                    '6.6' => 3,
                    '6.7' => 2,
                    '6.8' => 5,
                    '6.9' => 1,
                    7 => 3,
                    '7.3' => 4,
                    '7.4' => 2,
                    '7.5' => 2,
                    '7.6' => 1,
                    '7.8' => 1
                ]
            ],
            'adClusters' => [
                [
                    'start' => 0,
                    'end' => 2,
                    'count' => 3
                ],
                [
                    'start' => 5,
                    'end' => 17,
                    'count' => 13
                ],
                [
                    'start' => 19,
                    'end' => 23,
                    'count' => 5
                ],
                [
                    'start' => 26,
                    'end' => 29,
                    'count' => 4
                ],
                [
                    'start' => 31,
                    'end' => 37,
                    'count' => 7
                ],
                [
                    'start' => 39,
                    'end' => 42,
                    'count' => 4
                ],
                [
                    'start' => 44,
                    'end' => 49,
                    'count' => 6
                ],
                [
                    'start' => 51,
                    'end' => 52,
                    'count' => 2
                ],
                [
                    'start' => 55,
                    'end' => 56,
                    'count' => 2
                ],
                [
                    'start' => 59,
                    'end' => 60,
                    'count' => 2
                ],
                [
                    'start' => 62,
                    'end' => 62,
                    'count' => 1
                ],
                [
                    'start' => 65,
                    'end' => 65,
                    'count' => 1
                ],
                [
                    'start' => 67,
                    'end' => 79,
                    'count' => 13
                ],
                [
                    'start' => 81,
                    'end' => 82,
                    'count' => 2
                ],
                [
                    'start' => 85,
                    'end' => 85,
                    'count' => 1
                ],
                [
                    'start' => 87,
                    'end' => 90,
                    'count' => 4
                ],
                [
                    'start' => 93,
                    'end' => 93,
                    'count' => 1
                ],
                [
                    'start' => 95,
                    'end' => 96,
                    'count' => 2
                ],
                [
                    'start' => 100,
                    'end' => 102,
                    'count' => 3
                ],
                [
                    'start' => 104,
                    'end' => 105,
                    'count' => 2
                ],
                [
                    'start' => 108,
                    'end' => 110,
                    'count' => 3
                ],
                [
                    'start' => 113,
                    'end' => 114,
                    'count' => 2
                ],
                [
                    'start' => 116,
                    'end' => 116,
                    'count' => 1
                ],
                [
                    'start' => 119,
                    'end' => 125,
                    'count' => 7
                ],
                [
                    'start' => 127,
                    'end' => 131,
                    'count' => 5
                ],
                [
                    'start' => 134,
                    'end' => 138,
                    'count' => 5
                ],
                [
                    'start' => 140,
                    'end' => 146,
                    'count' => 7
                ],
                [
                    'start' => 150,
                    'end' => 153,
                    'count' => 4
                ],
                [
                    'start' => 156,
                    'end' => 156,
                    'count' => 1
                ],
                [
                    'start' => 158,
                    'end' => 158,
                    'count' => 1
                ],
                [
                    'start' => 160,
                    'end' => 168,
                    'count' => 9
                ],
                [
                    'start' => 170,
                    'end' => 171,
                    'count' => 2
                ],
                [
                    'start' => 174,
                    'end' => 175,
                    'count' => 2
                ],
                [
                    'start' => 177,
                    'end' => 178,
                    'count' => 2
                ],
                [
                    'start' => 180,
                    'end' => 181,
                    'count' => 2
                ],
                [
                    'start' => 184,
                    'end' => 186,
                    'count' => 3
                ],
                [
                    'start' => 188,
                    'end' => 191,
                    'count' => 4
                ],
                [
                    'start' => 195,
                    'end' => 195,
                    'count' => 1
                ],
                [
                    'start' => 197,
                    'end' => 197,
                    'count' => 1
                ],
                [
                    'start' => 200,
                    'end' => 201,
                    'count' => 2
                ],
                [
                    'start' => 203,
                    'end' => 204,
                    'count' => 2
                ],
                [
                    'start' => 207,
                    'end' => 212,
                    'count' => 6
                ],
                [
                    'start' => 214,
                    'end' => 218,
                    'count' => 5
                ],
                [
                    'start' => 220,
                    'end' => 221,
                    'count' => 2
                ],
                [
                    'start' => 223,
                    'end' => 223,
                    'count' => 1
                ],
                [
                    'start' => 226,
                    'end' => 226,
                    'count' => 1
                ],
                [
                    'start' => 228,
                    'end' => 231,
                    'count' => 4
                ],
                [
                    'start' => 234,
                    'end' => 234,
                    'count' => 1
                ],
                [
                    'start' => 236,
                    'end' => 240,
                    'count' => 5
                ],
                [
                    'start' => 242,
                    'end' => 248,
                    'count' => 7
                ],
                [
                    'start' => 250,
                    'end' => 251,
                    'count' => 2
                ],
                [
                    'start' => 253,
                    'end' => 253,
                    'count' => 1
                ],
                [
                    'start' => 255,
                    'end' => 255,
                    'count' => 1
                ],
                [
                    'start' => 259,
                    'end' => 265,
                    'count' => 7
                ],
                [
                    'start' => 267,
                    'end' => 268,
                    'count' => 2
                ],
                [
                    'start' => 270,
                    'end' => 273,
                    'count' => 4
                ],
                [
                    'start' => 275,
                    'end' => 278,
                    'count' => 4
                ],
                [
                    'start' => 280,
                    'end' => 280,
                    'count' => 1
                ],
                [
                    'start' => 282,
                    'end' => 282,
                    'count' => 1
                ],
                [
                    'start' => 284,
                    'end' => 285,
                    'count' => 2
                ],
                [
                    'start' => 287,
                    'end' => 288,
                    'count' => 2
                ],
                [
                    'start' => 290,
                    'end' => 292,
                    'count' => 3
                ],
                [
                    'start' => 295,
                    'end' => 295,
                    'count' => 1
                ],
                [
                    'start' => 297,
                    'end' => 301,
                    'count' => 5
                ],
                [
                    'start' => 304,
                    'end' => 304,
                    'count' => 1
                ],
                [
                    'start' => 306,
                    'end' => 306,
                    'count' => 1
                ],
                [
                    'start' => 309,
                    'end' => 310,
                    'count' => 2
                ],
                [
                    'start' => 312,
                    'end' => 313,
                    'count' => 2
                ],
                [
                    'start' => 316,
                    'end' => 318,
                    'count' => 3
                ],
                [
                    'start' => 320,
                    'end' => 321,
                    'count' => 2
                ],
                [
                    'start' => 323,
                    'end' => 324,
                    'count' => 2
                ],
                [
                    'start' => 326,
                    'end' => 330,
                    'count' => 5
                ],
                [
                    'start' => 332,
                    'end' => 337,
                    'count' => 6
                ],
                [
                    'start' => 340,
                    'end' => 340,
                    'count' => 1
                ],
                [
                    'start' => 342,
                    'end' => 342,
                    'count' => 1
                ],
                [
                    'start' => 344,
                    'end' => 350,
                    'count' => 7
                ],
                [
                    'start' => 352,
                    'end' => 353,
                    'count' => 2
                ],
                [
                    'start' => 356,
                    'end' => 361,
                    'count' => 6
                ],
                [
                    'start' => 363,
                    'end' => 363,
                    'count' => 1
                ],
                [
                    'start' => 365,
                    'end' => 366,
                    'count' => 2
                ],
                [
                    'start' => 368,
                    'end' => 372,
                    'count' => 5
                ],
                [
                    'start' => 374,
                    'end' => 374,
                    'count' => 1
                ],
                [
                    'start' => 376,
                    'end' => 382,
                    'count' => 7
                ],
                [
                    'start' => 385,
                    'end' => 386,
                    'count' => 2
                ],
                [
                    'start' => 388,
                    'end' => 388,
                    'count' => 1
                ],
                [
                    'start' => 390,
                    'end' => 390,
                    'count' => 1
                ],
                [
                    'start' => 392,
                    'end' => 393,
                    'count' => 2
                ],
                [
                    'start' => 395,
                    'end' => 398,
                    'count' => 4
                ],
                [
                    'start' => 400,
                    'end' => 403,
                    'count' => 4
                ],
                [
                    'start' => 406,
                    'end' => 410,
                    'count' => 5
                ],
                [
                    'start' => 412,
                    'end' => 414,
                    'count' => 3
                ],
                [
                    'start' => 416,
                    'end' => 416,
                    'count' => 1
                ],
                [
                    'start' => 419,
                    'end' => 430,
                    'count' => 12
                ],
                [
                    'start' => 432,
                    'end' => 436,
                    'count' => 5
                ],
                [
                    'start' => 439,
                    'end' => 442,
                    'count' => 4
                ],
                [
                    'start' => 445,
                    'end' => 447,
                    'count' => 3
                ],
                [
                    'start' => 449,
                    'end' => 451,
                    'count' => 3
                ],
                [
                    'start' => 453,
                    'end' => 454,
                    'count' => 2
                ],
                [
                    'start' => 456,
                    'end' => 457,
                    'count' => 2
                ],
                [
                    'start' => 459,
                    'end' => 464,
                    'count' => 6
                ],
                [
                    'start' => 467,
                    'end' => 470,
                    'count' => 4
                ],
                [
                    'start' => 474,
                    'end' => 474,
                    'count' => 1
                ],
                [
                    'start' => 476,
                    'end' => 476,
                    'count' => 1
                ],
                [
                    'start' => 478,
                    'end' => 483,
                    'count' => 6
                ],
                [
                    'start' => 486,
                    'end' => 491,
                    'count' => 6
                ],
                [
                    'start' => 493,
                    'end' => 493,
                    'count' => 1
                ],
                [
                    'start' => 496,
                    'end' => 497,
                    'count' => 2
                ],
                [
                    'start' => 499,
                    'end' => 506,
                    'count' => 8
                ],
                [
                    'start' => 508,
                    'end' => 510,
                    'count' => 3
                ],
                [
                    'start' => 512,
                    'end' => 512,
                    'count' => 1
                ],
                [
                    'start' => 514,
                    'end' => 520,
                    'count' => 7
                ],
                [
                    'start' => 522,
                    'end' => 522,
                    'count' => 1
                ],
                [
                    'start' => 525,
                    'end' => 525,
                    'count' => 1
                ],
                [
                    'start' => 527,
                    'end' => 529,
                    'count' => 3
                ],
                [
                    'start' => 531,
                    'end' => 533,
                    'count' => 3
                ],
                [
                    'start' => 536,
                    'end' => 537,
                    'count' => 2
                ],
                [
                    'start' => 539,
                    'end' => 543,
                    'count' => 5
                ],
                [
                    'start' => 546,
                    'end' => 548,
                    'count' => 3
                ],
                [
                    'start' => 550,
                    'end' => 550,
                    'count' => 1
                ],
                [
                    'start' => 552,
                    'end' => 559,
                    'count' => 8
                ],
                [
                    'start' => 561,
                    'end' => 561,
                    'count' => 1
                ],
                [
                    'start' => 564,
                    'end' => 565,
                    'count' => 2
                ],
                [
                    'start' => 567,
                    'end' => 571,
                    'count' => 5
                ],
                [
                    'start' => 574,
                    'end' => 574,
                    'count' => 1
                ],
                [
                    'start' => 577,
                    'end' => 579,
                    'count' => 3
                ],
                [
                    'start' => 585,
                    'end' => 585,
                    'count' => 1
                ],
                [
                    'start' => 587,
                    'end' => 595,
                    'count' => 9
                ],
                [
                    'start' => 597,
                    'end' => 600,
                    'count' => 4
                ],
                [
                    'start' => 604,
                    'end' => 609,
                    'count' => 6
                ],
                [
                    'start' => 611,
                    'end' => 611,
                    'count' => 1
                ],
                [
                    'start' => 613,
                    'end' => 613,
                    'count' => 1
                ],
                [
                    'start' => 616,
                    'end' => 618,
                    'count' => 3
                ],
                [
                    'start' => 622,
                    'end' => 630,
                    'count' => 9
                ],
                [
                    'start' => 633,
                    'end' => 635,
                    'count' => 3
                ],
                [
                    'start' => 637,
                    'end' => 638,
                    'count' => 2
                ],
                [
                    'start' => 640,
                    'end' => 643,
                    'count' => 4
                ],
                [
                    'start' => 645,
                    'end' => 645,
                    'count' => 1
                ],
                [
                    'start' => 647,
                    'end' => 647,
                    'count' => 1
                ],
                [
                    'start' => 649,
                    'end' => 649,
                    'count' => 1
                ],
                [
                    'start' => 652,
                    'end' => 655,
                    'count' => 4
                ],
                [
                    'start' => 657,
                    'end' => 657,
                    'count' => 1
                ],
                [
                    'start' => 659,
                    'end' => 659,
                    'count' => 1
                ],
                [
                    'start' => 661,
                    'end' => 661,
                    'count' => 1
                ],
                [
                    'start' => 664,
                    'end' => 664,
                    'count' => 1
                ],
                [
                    'start' => 666,
                    'end' => 671,
                    'count' => 6
                ],
                [
                    'start' => 674,
                    'end' => 674,
                    'count' => 1
                ],
                [
                    'start' => 676,
                    'end' => 677,
                    'count' => 2
                ],
                [
                    'start' => 680,
                    'end' => 681,
                    'count' => 2
                ],
                [
                    'start' => 683,
                    'end' => 683,
                    'count' => 1
                ],
                [
                    'start' => 685,
                    'end' => 685,
                    'count' => 1
                ],
                [
                    'start' => 687,
                    'end' => 687,
                    'count' => 1
                ],
                [
                    'start' => 689,
                    'end' => 694,
                    'count' => 6
                ]
            ]
        ]
    ],
    'last_learn_date' => '2026-06-29 20:42:53'
];
