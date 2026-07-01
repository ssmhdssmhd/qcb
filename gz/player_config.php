<?php
return [
    'player' => 'dplayer',
    'autoplay' => false,
    'preload' => 'auto',
    'hls_config' => [
        'enableWorker' => true,
        'lowLatencyMode' => false,
        'maxBufferLength' => 30,
        'maxMaxBufferLength' => 600,
        'minBufferLength' => 2,
        'maxBufferSize' => 60 * 1000 * 1000,
        'maxBufferHole' => 0.5,
        'highBufferWatchdogPeriod' => 2,
        'startLevel' => -1,
        'capLevelToPlayerSize' => false,
    ],
];
