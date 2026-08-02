<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Horizon Domain & Path
    |--------------------------------------------------------------------------
    |
    | Here you may configure the domain and path where Horizon will be accessible.
    |
    */

    'domain' => env('HORIZON_DOMAIN'),

    'path' => env('HORIZON_PATH', 'horizon'),

    /*
    |--------------------------------------------------------------------------
    | Horizon Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration option defines the storage driver that will be used
    | to store metrics and operational data.
    |
    */

    'use' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Horizon Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing Horizon data in Redis.
    |
    */

    'prefix' => env(
        'HORIZON_PREFIX',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_horizon:'
    ),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached to every Horizon route.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option configures the threshold (in seconds) for queue wait times.
    |
    */

    'waits' => [
        'redis:geolocation' => 10,
        'redis:emails' => 60,
        'redis:default' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration (Environments)
    |--------------------------------------------------------------------------
    |
    | Pools découplés de processus workers pour isoler les tâches critiques.
    |
    */

    'environments' => [
        'production' => [
            'supervisor-geolocation' => [
                'connection' => 'redis',
                'queue' => ['geolocation'],
                'balance' => 'simple',
                'minProcesses' => 3,
                'maxProcesses' => 10,
                'tries' => 2,
                'timeout' => 30,
            ],
            'supervisor-emails' => [
                'connection' => 'redis',
                'queue' => ['emails'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'tries' => 3,
                'timeout' => 60,
            ],
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 5,
                'tries' => 3,
                'timeout' => 90,
            ],
        ],

        'local' => [
            'supervisor-geolocation' => [
                'connection' => 'redis',
                'queue' => ['geolocation'],
                'balance' => 'simple',
                'minProcesses' => 2,
                'maxProcesses' => 5,
                'tries' => 2,
                'timeout' => 30,
            ],
            'supervisor-emails' => [
                'connection' => 'redis',
                'queue' => ['emails'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 2,
                'tries' => 3,
                'timeout' => 60,
            ],
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'tries' => 3,
                'timeout' => 90,
            ],
        ],
    ],
];
