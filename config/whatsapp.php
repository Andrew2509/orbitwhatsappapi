<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp API service including daily limits,
    | anti-ban measures, and message queue priorities.
    |
    */

    // Node.js WhatsApp Service URL
    'service_url' => env('WHATSAPP_SERVICE_URL', 'https://bot.orbitwaapi.dpdns.org'),

    // Secret key for webhook authentication
    'webhook_secret' => env('WHATSAPP_SECRET', 'secret'),

    /*
    |--------------------------------------------------------------------------
    | Daily Message Limits (Anti-Ban)
    |--------------------------------------------------------------------------
    |
    | Default limits per device per day. Conservative limits help prevent
    | WhatsApp from flagging accounts as spam.
    |
    */

    // Maximum messages per device per day
    'daily_message_limit' => env('WHATSAPP_DAILY_LIMIT', 200),

    // Warning threshold percentage (shows warning to user)
    'warning_threshold' => env('WHATSAPP_WARNING_THRESHOLD', 80),

    // Cooldown period in minutes when limit is reached
    'cooldown_minutes' => env('WHATSAPP_COOLDOWN_MINUTES', 60),

    // New device warmup period in days
    'warmup_days' => env('WHATSAPP_WARMUP_DAYS', 7),

    // Warmup daily limits (day 1 = 20, day 2 = 40, etc.)
    'warmup_limits' => [
        1 => 20,
        2 => 40,
        3 => 60,
        4 => 80,
        5 => 120,
        6 => 160,
        7 => 200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Priorities
    |--------------------------------------------------------------------------
    |
    | Messages are sent through different queue priorities to ensure
    | time-sensitive messages (like OTP) are processed first.
    |
    */

    'queue' => [
        'high' => 'high',       // OTP, single API sends
        'default' => 'default', // Campaign messages
        'low' => 'low',         // Analytics, cleanup jobs
    ],

    /*
    |--------------------------------------------------------------------------
    | Message Types Priority Mapping
    |--------------------------------------------------------------------------
    */

    'message_priorities' => [
        'otp' => 'high',
        'api_single' => 'high',
        'auto_reply' => 'default',
        'campaign' => 'default',
        'broadcast' => 'low',
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Configuration
    |--------------------------------------------------------------------------
    */

    // Storage disk for media files
    'media_disk' => env('WHATSAPP_MEDIA_DISK', 'local'),

    // Media retention in hours (after which files are deleted)
    'media_retention_hours' => env('WHATSAPP_MEDIA_RETENTION', 24),

    // Maximum file size in bytes (16MB default)
    'max_file_size' => env('WHATSAPP_MAX_FILE_SIZE', 16 * 1024 * 1024),

    /*
    |--------------------------------------------------------------------------
    | Content Filtering
    |--------------------------------------------------------------------------
    */

    // Enable content filtering (word blacklist)
    'content_filter_enabled' => env('WHATSAPP_CONTENT_FILTER', true),

    // Action when blacklisted content is detected: 'warn', 'block'
    'content_filter_action' => env('WHATSAPP_CONTENT_FILTER_ACTION', 'block'),
];
