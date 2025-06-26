<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => env('DB_HOST', '127.0.0.1'),
            'port'      => env('DB_PORT', '3306'),
            'database'  => env('DB_DATABASE', 'getter'),
            'username'  => env('DB_USERNAME', 'root'),
            'password'  => env('DB_PASSWORD', 'root'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
        'tyres' => [
            'driver'    => 'mysql',
            'host'      => env('DB_TYRES_HOST', '127.0.0.1'),
            'port'      => env('DB_TYRES_PORT', '3306'),
            'database'  => env('DB_TYRES_DATABASE', 'tyres'),
            'username'  => env('DB_TYRES_USERNAME', 'root'),
            'password'  => env('DB_TYRES_PASSWORD', 'root'),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ],
];
