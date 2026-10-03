<?php

return [
    'face_recognition' => [
        'model_path' => env('FACE_RECOGNITION_MODEL_PATH', storage_path('app/encodings.pickle')),
        'tolerance' => env('FACE_RECOGNITION_TOLERANCE', 0.48),
    ],
    
    'servo' => [
        'channels' => env('SERVO_CHANNELS', 16),
        'barrier_channel' => env('SERVO_BARRIER_CHANNEL', 8),
        'track_channel' => env('SERVO_TRACK_CHANNEL', 0),
    ],
    
    'gamification' => [
        'points_early' => env('GAMIFICATION_POINTS_EARLY', 3),
        'points_on_time' => env('GAMIFICATION_POINTS_ON_TIME', 2),
        'points_late' => env('GAMIFICATION_POINTS_LATE', 1),
        'points_absent' => env('GAMIFICATION_POINTS_ABSENT', -1),
    ],
];