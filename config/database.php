<?php

$url = parse_url(env('DATABASE_URL'));

return [
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => env('DB_PREFIX', ''),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => $url['host'],
            'port' => $url['port'],
            'database' => ltrim($url['path'], '/'),
            'username' => $url['user'],
            'password' => $url['pass'],
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => env('DB_PREFIX', ''),
            'strict' => env('DB_STRICT_MODE', true),
            'engine' => env('DB_ENGINE', null),
            'timezone' => env('DB_TIMEZONE', '+00:00'),
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => $url['host'],
            'port' => $url['port'],
            'database' => ltrim($url['path'], '/'),
            'username' => $url['user'],
            'password' => $url['pass'],
            'charset' => env('DB_CHARSET', 'utf8'),
            'prefix' => env('DB_PREFIX', ''),
            'schema' => env('DB_SCHEMA', 'public'),
            'sslmode' => env('DB_SSL_MODE', 'prefer'),
        ],
    ],

    'migrations' => 'migrations',
];
