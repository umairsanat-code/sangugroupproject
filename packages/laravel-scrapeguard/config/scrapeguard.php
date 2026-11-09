<?php

return [
    'enabled' => env('SCRAPEGUARD_ENABLED', true),

    'dashboard' => [
        'enabled' => env('SCRAPEGUARD_DASHBOARD', true),
        'prefix' => env('SCRAPEGUARD_DASHBOARD_PREFIX', 'scrapeguard'),
        'middleware' => ['web', 'auth'],
        'title' => env('SCRAPEGUARD_DASHBOARD_TITLE', 'ScrapeGuard Security'),
    ],

    'thresholds' => [
        'requests_per_minute' => (int) env('SCRAPEGUARD_RPM', 120),
        'sequential_paths' => (int) env('SCRAPEGUARD_SEQUENTIAL', 8),
        'risk_to_notify' => (int) env('SCRAPEGUARD_NOTIFY_SCORE', 70),
        'risk_to_block' => (int) env('SCRAPEGUARD_BLOCK_SCORE', 95),
    ],

    'protection' => [
        'auto_block' => env('SCRAPEGUARD_AUTO_BLOCK', false),
        'block_minutes' => (int) env('SCRAPEGUARD_BLOCK_MINUTES', 60),
        'blocked_response_status' => 403,
    ],

    'capture' => [
        'query' => true,
        'body' => env('SCRAPEGUARD_CAPTURE_BODY', false),
        'headers' => ['user-agent', 'accept', 'referer', 'accept-language', 'x-forwarded-for'],
        'redact_keys' => [
            'password', 'password_confirmation', 'token', 'access_token',
            'refresh_token', 'authorization', 'cookie', 'card_number', 'cvv',
        ],
    ],

    'notifications' => [
        'enabled' => env('SCRAPEGUARD_NOTIFICATIONS', true),
        'mail_to' => env('SCRAPEGUARD_MAIL_TO'),
    ],

    'storage' => [
        'table' => 'scrapeguard_events',
        'blocked_table' => 'scrapeguard_blocked_ips',
        'retention_days' => (int) env('SCRAPEGUARD_RETENTION_DAYS', 30),
    ],

    'exclude' => [
        'paths' => ['livewire/*', 'horizon/*', 'telescope/*', 'scrapeguard/*'],
        'ips' => [],
        'user_agents' => ['Googlebot', 'Bingbot'],
    ],
];
