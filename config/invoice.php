<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invoice Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the invoice system,
    | including PDF generation, file storage, and logging settings.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | PDF Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF generation using DomPDF.
    |
    */
    'pdf' => [
        'paper' => 'a4',
        'orientation' => 'portrait',
        'options' => [
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for invoice file storage.
    |
    */
    'storage' => [
        'disk' => 'public',
        'path' => 'invoices',
        'cleanup_days' => 30, // Days to keep old invoice files
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for invoice-related logging.
    |
    */
    'logging' => [
        'enabled' => env('INVOICE_LOGGING_ENABLED', true),
        'channel' => env('INVOICE_LOG_CHANNEL', 'daily'),
        'level' => env('INVOICE_LOG_LEVEL', 'info'),
        'log_requests' => env('INVOICE_LOG_REQUESTS', true),
        'log_generation' => env('INVOICE_LOG_GENERATION', true),
        'log_downloads' => env('INVOICE_LOG_DOWNLOADS', true),
        'log_errors' => env('INVOICE_LOG_ERRORS', true),
        'log_performance' => env('INVOICE_LOG_PERFORMANCE', true),
        'slow_request_threshold' => env('INVOICE_SLOW_REQUEST_THRESHOLD', 5000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security settings for invoice access and downloads.
    |
    */
    'security' => [
        'require_auth' => true,
        'check_ownership' => true,
        'rate_limit' => [
            'enabled' => env('INVOICE_RATE_LIMIT_ENABLED', true),
            'max_attempts' => env('INVOICE_RATE_LIMIT_ATTEMPTS', 10),
            'decay_minutes' => env('INVOICE_RATE_LIMIT_DECAY', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for invoice email attachments.
    |
    */
    'email' => [
        'attach_pdf' => env('INVOICE_EMAIL_ATTACH_PDF', true),
        'max_file_size' => env('INVOICE_EMAIL_MAX_SIZE', 5242880), // 5MB in bytes
        'retry_attempts' => env('INVOICE_EMAIL_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Performance-related settings for invoice generation.
    |
    */
    'performance' => [
        'cache_enabled' => env('INVOICE_CACHE_ENABLED', true),
        'cache_ttl' => env('INVOICE_CACHE_TTL', 3600), // seconds
        'max_generation_time' => env('INVOICE_MAX_GENERATION_TIME', 30), // seconds
        'memory_limit' => env('INVOICE_MEMORY_LIMIT', '256M'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for invoice maintenance and cleanup tasks.
    |
    */
    'maintenance' => [
        'auto_cleanup' => env('INVOICE_AUTO_CLEANUP', true),
        'cleanup_schedule' => env('INVOICE_CLEANUP_SCHEDULE', 'weekly'),
        'keep_successful_days' => env('INVOICE_KEEP_SUCCESSFUL_DAYS', 30),
        'keep_failed_days' => env('INVOICE_KEEP_FAILED_DAYS', 7),
        'max_files_per_cleanup' => env('INVOICE_MAX_FILES_PER_CLEANUP', 100),
        'backup_before_cleanup' => env('INVOICE_BACKUP_BEFORE_CLEANUP', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for monitoring invoice system health and performance.
    |
    */
    'monitoring' => [
        'enabled' => env('INVOICE_MONITORING_ENABLED', true),
        'alert_on_errors' => env('INVOICE_ALERT_ON_ERRORS', true),
        'alert_on_slow_requests' => env('INVOICE_ALERT_ON_SLOW_REQUESTS', true),
        'health_check_interval' => env('INVOICE_HEALTH_CHECK_INTERVAL', 300), // seconds
        'metrics' => [
            'track_generation_time' => true,
            'track_file_sizes' => true,
            'track_download_counts' => true,
            'track_error_rates' => true,
        ],
    ],

];