<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains additional configuration for the application
    | that can be used in JavaScript and other parts of the application.
    |
    */

    'api' => [
        // Avoid calling url() during config bootstrap to prevent UrlGenerator error
        'base_url' => env('API_BASE_URL', '/api/v1'),
        'timeout' => env('API_TIMEOUT', 30),
        'retry_attempts' => env('API_RETRY_ATTEMPTS', 3),
    ],

    'frontend' => [
        'debug' => env('APP_DEBUG', false),
        'environment' => env('APP_ENV', 'production'),
        'version' => env('APP_VERSION', '1.0.0'),
    ],

    'features' => [
        'enable_api_caching' => env('ENABLE_API_CACHING', true),
        'enable_real_time_updates' => env('ENABLE_REAL_TIME_UPDATES', false),
        'enable_offline_mode' => env('ENABLE_OFFLINE_MODE', false),
    ],
];
