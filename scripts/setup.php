<?php

/**
 * Cabin – First-Run Setup Script
 *
 * Run this ONCE after deployment:
 *   php scripts/setup.php
 *
 * What it does:
 *  1. Generates APP_KEY if not set
 *  2. Creates .env from .env.example if not exists
 *  3. Creates storage directories
 *  4. Imports database schema
 *  5. Verifies PHP extensions
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

$green  = "\e[32m";
$red    = "\e[31m";
$yellow = "\e[33m";
$blue   = "\e[34m";
$reset  = "\e[0m";

echo "\n{$blue}╔══════════════════════════════════════╗{$reset}\n";
echo "{$blue}║     Cabin Setup Script v1.0          ║{$reset}\n";
echo "{$blue}║     Tech With Hussain                ║{$reset}\n";
echo "{$blue}╚══════════════════════════════════════╝{$reset}\n\n";

// ─────────────────────────────────────────────
// 1. PHP Version Check
// ─────────────────────────────────────────────
step('Checking PHP version...');
if (PHP_VERSION_ID < 80300) {
    fail('Cabin requires PHP 8.3+. Current: ' . PHP_VERSION);
}
pass('PHP ' . PHP_VERSION . ' ✓');

// ─────────────────────────────────────────────
// 2. Check Extensions
// ─────────────────────────────────────────────
step('Checking required PHP extensions...');
$required = ['pdo', 'pdo_mysql', 'openssl', 'gd', 'mbstring', 'json', 'session'];
$missing  = [];

foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}

if ($missing) {
    fail('Missing extensions: ' . implode(', ', $missing));
} else {
    pass('All extensions present ✓');
}

// ─────────────────────────────────────────────
// 3. Create .env if not exists
// ─────────────────────────────────────────────
step('Setting up .env file...');
$envPath     = BASE_PATH . '/.env';
$envExample  = BASE_PATH . '/.env.example';

if (!file_exists($envPath)) {
    if (!file_exists($envExample)) {
        fail('.env.example not found.');
    }
    copy($envExample, $envPath);
    pass('.env created from .env.example');
    warn('⚠ IMPORTANT: Edit .env and set your database credentials and secrets!');
} else {
    info('.env already exists – skipping');
}

// ─────────────────────────────────────────────
// 4. Generate APP_KEY if not set
// ─────────────────────────────────────────────
step('Checking APP_KEY...');
$envContent = file_get_contents($envPath);

if (str_contains($envContent, 'APP_KEY=base64:CHANGE_ME')) {
    $key = 'base64:' . base64_encode(random_bytes(32));
    $envContent = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $key, $envContent);
    file_put_contents($envPath, $envContent);
    pass('APP_KEY generated: ' . substr($key, 0, 24) . '…');
} else {
    info('APP_KEY already configured');
}

// ─────────────────────────────────────────────
// 5. Create storage directories
// ─────────────────────────────────────────────
step('Creating storage directories...');
$dirs = [
    BASE_PATH . '/storage',
    BASE_PATH . '/storage/uploads',
    BASE_PATH . '/storage/logs',
    BASE_PATH . '/storage/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
        pass("Created: $dir");
    } else {
        info("Exists: $dir");
    }
}

// Create .gitignore for storage
$gitignore = BASE_PATH . '/storage/.gitignore';
if (!file_exists($gitignore)) {
    file_put_contents($gitignore, "*\n!.gitignore\n");
}

// ─────────────────────────────────────────────
// 6. Check Autoloader
// ─────────────────────────────────────────────
step('Checking autoloader...');
if (file_exists(BASE_PATH . '/vendor/autoload.php')) {
    pass('Composer autoloader found ✓');
} else {
    info('Composer autoloader not found – using built-in native PSR-4 autoloader ✓');
}

// ─────────────────────────────────────────────
// 7. Database Setup Instructions
// ─────────────────────────────────────────────
echo "\n{$yellow}────────────────────────────────────────{$reset}\n";
echo "{$yellow}Database Setup (manual steps required):{$reset}\n";
echo "{$yellow}────────────────────────────────────────{$reset}\n";
echo "1. Create a MySQL database: {$green}CREATE DATABASE cabin_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;{$reset}\n";
echo "2. Create a user: {$green}CREATE USER 'cabin_user'@'localhost' IDENTIFIED BY 'your_password';{$reset}\n";
echo "3. Grant privileges: {$green}GRANT ALL PRIVILEGES ON cabin_db.* TO 'cabin_user'@'localhost'; FLUSH PRIVILEGES;{$reset}\n";
echo "4. Import schema: {$green}mysql -u cabin_user -p cabin_db < database/schema.sql{$reset}\n";
echo "5. Update .env with your DB credentials\n\n";

// ─────────────────────────────────────────────
// Done
// ─────────────────────────────────────────────
echo "\n{$green}╔══════════════════════════════════════╗{$reset}\n";
echo "{$green}║  Setup complete! Next steps:         ║{$reset}\n";
echo "{$green}╚══════════════════════════════════════╝{$reset}\n";
echo "1. Edit {$yellow}.env{$reset} with your credentials\n";
echo "2. Run {$yellow}composer install{$reset}\n";
echo "3. Import the database schema\n";
echo "4. Set up Hostinger cron: {$yellow}GET /cron/cleanup?key=YOUR_CRON_SECRET{$reset} every 30 minutes\n";
echo "5. Ensure {$yellow}storage/{$reset} directory is writable (chmod 750)\n\n";

// ─────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────
function step(string $msg): void {
    echo "\n\e[34m→ $msg\e[0m\n";
}

function pass(string $msg): void {
    echo "  \e[32m✓ $msg\e[0m\n";
}

function info(string $msg): void {
    echo "  \e[33m· $msg\e[0m\n";
}

function warn(string $msg): void {
    echo "  \e[33m⚠ $msg\e[0m\n";
}

function fail(string $msg): never {
    echo "  \e[31m✗ ERROR: $msg\e[0m\n\n";
    exit(1);
}
