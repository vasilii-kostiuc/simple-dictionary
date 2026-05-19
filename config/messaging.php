<?php

return [
    'default' => env('MESSAGING_BROKER', 'redis'),
    'redis' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
    ],
    'rabbitmq' => [
        'host' => env('MESSAGING_RABBITMQ_HOST', '127.0.0.1'),
        'port' => env('MESSAGING_RABBITMQ_PORT', 5672),
        'user' => env('MESSAGING_RABBITMQ_USER', 'guest'),
        'password' => env('MESSAGING_RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('MESSAGING_RABBITMQ_VHOST', '/'),
    ],
];
