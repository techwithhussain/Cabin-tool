-- ============================================================
-- Cabin – Secure Notes & Private Sharing Platform
-- Database Schema v1.0
-- MySQL 8.0+  |  Character Set: utf8mb4  |  Collation: utf8mb4_unicode_ci
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────
-- Table: notes
-- Core note storage with encrypted content
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `notes` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`             VARCHAR(24)  NOT NULL UNIQUE COMMENT 'Public URL token',
    `content_encrypted`MEDIUMTEXT   NOT NULL     COMMENT 'AES-256-GCM encrypted content',
    `content_iv`       VARCHAR(64)  NOT NULL     COMMENT 'AES encryption IV (hex)',
    `content_tag`      VARCHAR(64)  NOT NULL     COMMENT 'AES-GCM auth tag (hex)',
    `password_hash`    VARCHAR(255) NULL         COMMENT 'Argon2id hash, NULL = no password',
    `expires_at`       DATETIME     NULL         COMMENT 'NULL = never expires',
    `is_expired`       TINYINT(1)   NOT NULL DEFAULT 0,
    `burn_after_read`  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Delete on first view',
    `view_count`       INT UNSIGNED NOT NULL DEFAULT 0,
    `creator_ip_hash`  VARCHAR(64)  NOT NULL     COMMENT 'SHA-256 of creator IP',
    `owner_token_hash` VARCHAR(64)  NOT NULL     COMMENT 'SHA-256 of owner delete token',
    `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME     NULL         COMMENT 'Soft-delete timestamp',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_notes_slug` (`slug`),
    KEY `idx_notes_expires_at` (`expires_at`),
    KEY `idx_notes_is_expired` (`is_expired`),
    KEY `idx_notes_created_at` (`created_at`),
    KEY `idx_notes_creator_ip` (`creator_ip_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Core notes table with AES-256-GCM encrypted content';

-- ─────────────────────────────────────────────
-- Table: note_images
-- Attached images per note
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `note_images` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `note_id`       BIGINT UNSIGNED NOT NULL,
    `note_slug`     VARCHAR(24)  NOT NULL     COMMENT 'Denormalised for easy lookup',
    `filename`      VARCHAR(128) NOT NULL     COMMENT 'Stored filename (UUID-based)',
    `original_name` VARCHAR(255) NOT NULL     COMMENT 'Original upload filename',
    `mime_type`     VARCHAR(64)  NOT NULL,
    `size_bytes`    INT UNSIGNED NOT NULL,
    `width`         SMALLINT UNSIGNED NULL,
    `height`        SMALLINT UNSIGNED NULL,
    `storage_path`  VARCHAR(512) NOT NULL     COMMENT 'Relative path in storage/',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_images_note_id` (`note_id`),
    KEY `idx_images_note_slug` (`note_slug`),
    CONSTRAINT `fk_images_note_id`
        FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Images attached to notes';

-- ─────────────────────────────────────────────
-- Table: rate_limits
-- Database-backed IP rate limiting
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ip_hash`       VARCHAR(64)  NOT NULL,
    `action`        VARCHAR(32)  NOT NULL COMMENT 'create|view|password|admin',
    `attempts`      INT UNSIGNED NOT NULL DEFAULT 1,
    `window_start`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `blocked_until` DATETIME     NULL     COMMENT 'NULL = not blocked',
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_rate_ip_action` (`ip_hash`, `action`),
    KEY `idx_rate_window` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Rate limiting records per IP and action type';

-- ─────────────────────────────────────────────
-- Table: analytics
-- Note view/interaction events
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `analytics` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `note_id`      BIGINT UNSIGNED NOT NULL,
    `note_slug`    VARCHAR(24)  NOT NULL,
    `event_type`   ENUM('view','copy','download','share','password_fail','password_ok') NOT NULL,
    `ip_hash`      VARCHAR(64)  NOT NULL,
    `user_agent`   VARCHAR(512) NULL,
    `referer`      VARCHAR(512) NULL,
    `country_code` CHAR(2)      NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_analytics_note_id`   (`note_id`),
    KEY `idx_analytics_event`     (`event_type`),
    KEY `idx_analytics_created`   (`created_at`),
    CONSTRAINT `fk_analytics_note_id`
        FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Note interaction analytics events';

-- ─────────────────────────────────────────────
-- Table: audit_logs
-- Security audit trail
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `action`        VARCHAR(64)  NOT NULL COMMENT 'note_created|note_viewed|note_deleted|password_failed|rate_limited|admin_login',
    `note_slug`     VARCHAR(24)  NULL     COMMENT 'NULL for non-note actions',
    `ip_hash`       VARCHAR(64)  NOT NULL,
    `user_agent`    VARCHAR(512) NULL,
    `metadata_json` JSON         NULL     COMMENT 'Extra context (error messages, counts, etc.)',
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_audit_action`  (`action`),
    KEY `idx_audit_slug`    (`note_slug`),
    KEY `idx_audit_ip`      (`ip_hash`),
    KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Security audit log for all sensitive actions';

-- ─────────────────────────────────────────────
-- Table: admin_sessions
-- Admin panel sessions (separate from PHP sessions)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_sessions` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `token_hash`  VARCHAR(64)  NOT NULL UNIQUE COMMENT 'SHA-256 of session token',
    `ip_hash`     VARCHAR(64)  NOT NULL,
    `expires_at`  DATETIME     NOT NULL,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_admin_token` (`token_hash`),
    KEY `idx_admin_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin panel session tokens';

SET FOREIGN_KEY_CHECKS = 1;

-- ─────────────────────────────────────────────
-- Cleanup Event (MySQL Event Scheduler)
-- Auto-mark expired notes every 5 minutes
-- Run: SET GLOBAL event_scheduler = ON;
-- ─────────────────────────────────────────────
-- DROP EVENT IF EXISTS `auto_expire_notes`;
-- CREATE EVENT `auto_expire_notes`
--   ON SCHEDULE EVERY 5 MINUTE
--   DO
--     UPDATE `notes`
--     SET `is_expired` = 1
--     WHERE `expires_at` IS NOT NULL
--       AND `expires_at` < NOW()
--       AND `is_expired` = 0
--       AND `deleted_at` IS NULL;
