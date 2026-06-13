<?php

return [
    'video' => [
        'max_participants' => (int) env('LIVE_VIDEO_MAX_PARTICIPANTS', 12),
        'max_speakers' => (int) env('LIVE_VIDEO_MAX_SPEAKERS', 4),
    ],
    'pk' => [
        'default_duration_seconds' => (int) env('LIVE_PK_DEFAULT_DURATION_SECONDS', 300),
    ],
];
