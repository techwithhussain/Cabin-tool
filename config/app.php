<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'name'    => Config::env('APP_NAME', 'Cabin'),
    'url'     => Config::env('APP_URL', 'http://localhost'),
    'env'     => Config::env('APP_ENV', 'production'),
    'debug'   => Config::env('APP_DEBUG', 'false') === 'true',
    'key'     => Config::env('APP_KEY', ''),
    'timezone' => 'UTC',
    'brand'   => 'Tech With Hussain',
    'tagline' => 'Secure Notes. Private Sharing.',
];
