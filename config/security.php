<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'rate_limit' => [
        'create'           => (int) Config::env('RATE_LIMIT_CREATE', 10),
        'view'             => (int) Config::env('RATE_LIMIT_VIEW', 60),
        'password'         => (int) Config::env('RATE_LIMIT_PASSWORD', 5),
        'window'           => (int) Config::env('RATE_LIMIT_WINDOW', 3600),
        'password_window'  => (int) Config::env('RATE_LIMIT_PASSWORD_WINDOW', 900),
    ],
    'upload' => [
        'max_size'   => (int) Config::env('MAX_UPLOAD_SIZE', 10485760),
        'max_images' => (int) Config::env('MAX_IMAGES_PER_NOTE', 5),
        'types'      => explode(',', Config::env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp')),
        'path'       => Config::env('UPLOAD_PATH', 'storage/uploads'),
    ],
    'note' => [
        'max_length'     => (int) Config::env('MAX_NOTE_LENGTH', 50000),
        'default_expiry' => Config::env('DEFAULT_EXPIRY', '24h'),
    ],
    'admin' => [
        'session_lifetime' => (int) Config::env('ADMIN_SESSION_LIFETIME', 7200),
    ],
    'encryption' => [
        'cipher' => 'aes-256-gcm',
    ],
];
