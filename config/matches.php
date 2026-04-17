<?php

return [
    'stale' => [
        'first_action_timeout_seconds' => (int) env('MATCH_FIRST_ACTION_TIMEOUT_SECONDS', 300),
        'steps_inactivity_timeout_seconds' => (int) env('MATCH_STEPS_INACTIVITY_TIMEOUT_SECONDS', 300),
        'scheduler_enabled' => (bool) env('MATCH_STALE_SCHEDULER_ENABLED', true),
    ],
];
