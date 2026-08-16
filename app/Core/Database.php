<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Database – PDO Singleton
 *
 * Provides a single shared PDO instance with secure defaults.
 * All queries must use prepared statements.
 */
class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $driver  = Config::env('DB_DRIVER', 'mysql');
        $host    = Config::env('DB_HOST', '127.0.0.1');
        $port    = Config::env('DB_PORT', '3306');
        $dbname  = Config::env('DB_DATABASE', 'cabin_db');
        $charset = Config::env('DB_CHARSET', 'utf8mb4');
        $user    = Config::env('DB_USERNAME', 'root');
        $pass    = Config::env('DB_PASSWORD', '');

        if ($driver === 'sqlite') {
            $this->connectSqlite();
            return;
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";

        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::ATTR_PERSISTENT         => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE utf8mb4_unicode_ci",
        ];

        $ssl = Config::env('DB_SSL', 'false');
        if ($ssl === 'true' || $ssl === true || str_contains((string)$host, 'tidbcloud.com') || str_contains((string)$host, 'aivencloud.com')) {
            $options[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        try {
            $this->pdo = new \PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // Fallback to SQLite in development if MySQL is offline
            if (APP_DEBUG || Config::env('APP_ENV') === 'development') {
                $this->connectSqlite();
                return;
            }
            throw new \RuntimeException('Database connection failed: ' . $e->getMessage());
        }
    }

    private function connectSqlite(): void
    {
        $storageDir = defined('STORAGE_PATH') ? STORAGE_PATH : dirname(__DIR__, 2) . '/storage';
        if (!is_dir($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }
        $dbPath = $storageDir . '/database.sqlite';
        $dsn = "sqlite:" . $dbPath;

        $this->pdo = new \PDO($dsn, null, null, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
        $this->initSqliteSchema();
    }

    private function initSqliteSchema(): void
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug VARCHAR(24) NOT NULL UNIQUE,
            content_encrypted TEXT NOT NULL,
            content_iv VARCHAR(64) NOT NULL,
            content_tag VARCHAR(64) NOT NULL,
            password_hash VARCHAR(255) NULL,
            expires_at DATETIME NULL,
            is_expired INTEGER NOT NULL DEFAULT 0,
            burn_after_read INTEGER NOT NULL DEFAULT 0,
            view_count INTEGER NOT NULL DEFAULT 0,
            creator_ip_hash VARCHAR(64) NOT NULL,
            owner_token_hash VARCHAR(64) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            deleted_at DATETIME NULL
        );

        CREATE TABLE IF NOT EXISTS note_images (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            note_id INTEGER NOT NULL,
            note_slug VARCHAR(24) NOT NULL,
            filename VARCHAR(128) NOT NULL,
            original_name VARCHAR(255) NOT NULL,
            mime_type VARCHAR(64) NOT NULL,
            size_bytes INTEGER NOT NULL,
            width INTEGER NULL,
            height INTEGER NULL,
            storage_path VARCHAR(512) NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_hash VARCHAR(64) NOT NULL,
            action VARCHAR(32) NOT NULL,
            attempts INTEGER NOT NULL DEFAULT 1,
            window_start DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            blocked_until DATETIME NULL,
            UNIQUE (ip_hash, action)
        );

        CREATE TABLE IF NOT EXISTS analytics (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            note_id INTEGER NOT NULL,
            note_slug VARCHAR(24) NOT NULL,
            event_type VARCHAR(32) NOT NULL,
            ip_hash VARCHAR(64) NOT NULL,
            user_agent VARCHAR(512) NULL,
            referer VARCHAR(512) NULL,
            country_code CHAR(2) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (note_id) REFERENCES notes(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS audit_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            action VARCHAR(64) NOT NULL,
            note_slug VARCHAR(24) NULL,
            ip_hash VARCHAR(64) NOT NULL,
            user_agent VARCHAR(512) NULL,
            metadata_json TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS admin_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            token_hash VARCHAR(64) NOT NULL UNIQUE,
            ip_hash VARCHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        ";
        $this->pdo->exec($sql);
    }

    /** Get the singleton instance */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** Get the raw PDO object */
    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    // ─────────────────────────────────────────────
    // Query Helpers
    // ─────────────────────────────────────────────

    /**
     * Execute a prepared statement and return the statement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row
     */
    public function fetchOne(string $sql, array $params = []): array|false
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Execute an insert/update/delete and return affected rows
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Get the last inserted ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Begin a transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit a transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback a transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    // Prevent cloning/serialization of singleton
    private function __clone() {}
    public function __wakeup(): never
    {
        throw new \RuntimeException('Cannot unserialize singleton.');
    }
}
