<?php

declare(strict_types=1);

use App\Core\Config;

return [
    'host'     => Config::env('DB_HOST', 'localhost'),
    'port'     => Config::env('DB_PORT', '3306'),
    'database' => Config::env('DB_DATABASE', 'cabin_db'),
    'username' => Config::env('DB_USERNAME', 'root'),
    'password' => Config::env('DB_PASSWORD', ''),
    'charset'  => Config::env('DB_CHARSET', 'utf8mb4'),
];
