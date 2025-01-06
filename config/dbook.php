<?php

return [
    'dbook' => [
        'jwt' => [
            'secret' => env('JWT_SECRET', 'your-secret-key'),
        ],
        'rabbitmq' => [
            'host' => env('RABBITMQ_HOST', 'rabbitmq'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'pass' => env('RABBITMQ_PASS', 'guest'),
        ],
    ],
];
