<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Environment Configurations
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for different environments.
    | You can define environment-specific settings here.
    |
    */

    'environments' => [
        'development' => [
            'name' => 'Development',
            'description' => 'Local development environment',
            'debug' => true,
            'log_level' => 'debug',
            'cache_config' => false,
            'cache_routes' => false,
            'cache_views' => false,
            'optimize_autoloader' => false,
            'features' => [
                'telescope' => true,
                'debugbar' => true,
                'query_detector' => true,
                'mail_preview' => true,
            ],
            'security' => [
                'force_https' => false,
                'secure_cookies' => false,
                'csrf_protection' => true,
                'rate_limiting' => 'relaxed',
            ],
            'performance' => [
                'opcache' => false,
                'redis_cluster' => false,
                'cdn_enabled' => false,
                'image_optimization' => false,
            ],
        ],

        'staging' => [
            'name' => 'Staging',
            'description' => 'Pre-production testing environment',
            'debug' => false,
            'log_level' => 'info',
            'cache_config' => true,
            'cache_routes' => true,
            'cache_views' => true,
            'optimize_autoloader' => true,
            'features' => [
                'telescope' => true,
                'debugbar' => false,
                'query_detector' => true,
                'mail_preview' => false,
            ],
            'security' => [
                'force_https' => true,
                'secure_cookies' => true,
                'csrf_protection' => true,
                'rate_limiting' => 'moderate',
            ],
            'performance' => [
                'opcache' => true,
                'redis_cluster' => false,
                'cdn_enabled' => true,
                'image_optimization' => true,
            ],
        ],

        'production' => [
            'name' => 'Production',
            'description' => 'Live production environment',
            'debug' => false,
            'log_level' => 'error',
            'cache_config' => true,
            'cache_routes' => true,
            'cache_views' => true,
            'optimize_autoloader' => true,
            'features' => [
                'telescope' => false,
                'debugbar' => false,
                'query_detector' => false,
                'mail_preview' => false,
            ],
            'security' => [
                'force_https' => true,
                'secure_cookies' => true,
                'csrf_protection' => true,
                'rate_limiting' => 'strict',
            ],
            'performance' => [
                'opcache' => true,
                'redis_cluster' => true,
                'cdn_enabled' => true,
                'image_optimization' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configurations
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'relaxed' => [
            'api' => '1000,1',
            'web' => '500,1',
            'auth' => '10,1',
        ],
        'moderate' => [
            'api' => '500,1',
            'web' => '300,1',
            'auth' => '5,1',
        ],
        'strict' => [
            'api' => '100,1',
            'web' => '200,1',
            'auth' => '3,1',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Configurations
    |--------------------------------------------------------------------------
    */
    'upload_limits' => [
        'development' => [
            'max_size' => 10240, // 10MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'],
        ],
        'staging' => [
            'max_size' => 5120, // 5MB
            'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
        ],
        'production' => [
            'max_size' => 2048, // 2MB
            'allowed_types' => ['jpg', 'jpeg', 'png'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configurations
    |--------------------------------------------------------------------------
    */
    'database' => [
        'development' => [
            'default' => 'sqlite',
            'read_write_split' => false,
            'connection_pooling' => false,
        ],
        'staging' => [
            'default' => 'mysql',
            'read_write_split' => false,
            'connection_pooling' => true,
        ],
        'production' => [
            'default' => 'mysql',
            'read_write_split' => true,
            'connection_pooling' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configurations
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'development' => [
            'sentry' => false,
            'new_relic' => false,
            'slack_notifications' => false,
        ],
        'staging' => [
            'sentry' => true,
            'new_relic' => false,
            'slack_notifications' => true,
        ],
        'production' => [
            'sentry' => true,
            'new_relic' => true,
            'slack_notifications' => true,
        ],
    ],
];