<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Blog;

/**
 * BlogRepository – Database operations for blog posts
 */
class BlogRepository
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->ensureTableExists();
    }

    /**
     * Ensure blogs table exists & seed initial posts if empty
     */
    private function ensureTableExists(): void
    {
        try {
            $isSqlite = $this->db->isSqlite();

            if ($isSqlite) {
                $sql = "CREATE TABLE IF NOT EXISTS blogs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    slug TEXT NOT NULL UNIQUE,
                    title TEXT NOT NULL,
                    summary TEXT,
                    content TEXT NOT NULL,
                    cover_image TEXT,
                    category TEXT NOT NULL DEFAULT 'Security',
                    author TEXT NOT NULL DEFAULT 'Hussain Lone',
                    meta_title TEXT,
                    meta_description TEXT,
                    meta_keywords TEXT,
                    read_time TEXT NOT NULL DEFAULT '4 min read',
                    status TEXT NOT NULL DEFAULT 'published',
                    views INTEGER NOT NULL DEFAULT 0,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                )";
            } else {
                $sql = "CREATE TABLE IF NOT EXISTS `blogs` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `slug` VARCHAR(191) NOT NULL UNIQUE,
                    `title` VARCHAR(255) NOT NULL,
                    `summary` TEXT NULL,
                    `content` LONGTEXT NOT NULL,
                    `cover_image` VARCHAR(512) NULL,
                    `category` VARCHAR(64) NOT NULL DEFAULT 'Security',
                    `author` VARCHAR(128) NOT NULL DEFAULT 'Hussain Lone',
                    `meta_title` VARCHAR(255) NULL,
                    `meta_description` TEXT NULL,
                    `meta_keywords` VARCHAR(512) NULL,
                    `read_time` VARCHAR(32) NOT NULL DEFAULT '4 min read',
                    `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
                    `views` INT UNSIGNED NOT NULL DEFAULT 0,
                    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_blogs_slug` (`slug`),
                    KEY `idx_blogs_status` (`status`),
                    KEY `idx_blogs_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            }

            $this->db->execute($sql);

            // Check if table is empty, if so seed high-quality SEO articles
            $countRow = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM blogs");
            if ((int)($countRow['cnt'] ?? 0) === 0) {
                $this->seedInitialPosts();
            }
        } catch (\Throwable $e) {
            error_log("[BlogRepository] Error ensuring table: " . $e->getMessage());
        }
    }

    /**
     * Seed high-intent SEO blog posts
     */
    private function seedInitialPosts(): void
    {
        $posts = [
            [
                'slug' => 'top-5-privnote-alternatives-2026',
                'title' => 'Top 5 Free Privnote Alternatives for Self-Destructing Notes in 2026',
                'category' => 'Security',
                'author' => 'Hussain Lone',
                'read_time' => '5 min read',
                'summary' => 'Looking for a secure, ad-free, and open Privnote alternative? Here is a breakdown of the best self-destructing note tools with AES-256 encryption.',
                'meta_title' => 'Top 5 Free Privnote Alternatives for Secure Notes (2026)',
                'meta_description' => 'Discover the best Privnote alternatives in 2026 for sending self-destructing notes, private passwords, and confidential links with zero logs and AES-256 encryption.',
                'meta_keywords' => 'privnote alternative, self destructing notes, burn after read online, secure note sharing, onetimesecret alternative',
                'content' => '<h2>Why Look for a Privnote Alternative?</h2>
<p>Privnote has long been a household name for sending self-destructing messages. However, modern users and security professionals now demand higher security standards: modern <strong>AES-256-GCM encryption</strong>, zero-tracker architecture, clean dark UI, password protection using Argon2id, and fast image attachments.</p>

<h3>1. Cabin (cabinn.in) &mdash; Best Overall Modern Tool</h3>
<p><strong>Cabin</strong> is a lightweight, blazing-fast self-destructing note application engineered specifically for developers, privacy enthusiasts, and teams.</p>
<ul>
    <li><strong>Key Features:</strong> End-to-end AES-256 encryption, Burn-After-Read mode, Argon2id password protection, custom short URLs, and automatic database purges.</li>
    <li><strong>Privacy:</strong> 100% anonymous &mdash; no registration, zero trackers, and no log retention.</li>
</ul>

<h3>2. OneTimeSecret</h3>
<p>OneTimeSecret is a classic tool for generating single-use secret URLs. It provides good privacy, though the user interface is dated and feature sets are minimal.</p>

<h3>3. ProtectedText</h3>
<p>ProtectedText offers password-encrypted pads stored in the cloud. It is great for long-term encrypted storage, but lacks ephemeral auto-destruct timers.</p>

<h3>4. ZeroBin / PrivateBin</h3>
<p>PrivateBin is a client-side encrypted pastebin. While robust, setting it up or sharing formatted text can feel cumbersome for everyday users.</p>

<h2>Conclusion</h2>
<p>If you want speed, zero-signup privacy, and military-grade encryption without bloated ads or scripts, <strong>Cabin</strong> is the top recommendation for 2026.</p>'
            ],
            [
                'slug' => 'how-to-share-passwords-securely-online',
                'title' => 'How to Share Passwords & Sensitive API Keys Securely (Never Use Chat or Email)',
                'category' => 'Guides',
                'author' => 'Hussain Lone',
                'read_time' => '4 min read',
                'summary' => 'Sending credentials via Slack, WhatsApp, or Email is dangerous. Learn the safest methods to share passwords, API tokens, and secrets.',
                'meta_title' => 'How to Share Passwords & API Keys Securely Online (Best Practices)',
                'meta_description' => 'Learn how to securely share passwords, database credentials, and secret tokens using burn-after-reading encrypted links. Stop leaking secrets in plain text.',
                'meta_keywords' => 'share passwords securely, send api keys safely, burn after read password, encrypted password sharing, secure credentials transfer',
                'content' => '<h2>The Danger of Sharing Passwords over Email & Chat</h2>
<p>Sending database passwords, SSH keys, or server credentials over Slack, Microsoft Teams, WhatsApp, or Gmail creates a permanent security risk:</p>
<ul>
    <li><strong>Chat History Leaks:</strong> Anyone with account access or third-party integrations can view past messages.</li>
    <li><strong>Data Breaches:</strong> Email inboxes are the primary target in phishing and credential stuffing attacks.</li>
    <li><strong>Compliance Violations:</strong> Storing unencrypted plaintext secrets violates GDPR, SOC2, and ISO 27001 guidelines.</li>
</ul>

<h2>The 3-Step Secure Sharing Method</h2>
<ol>
    <li><strong>Encrypt on Creation:</strong> Use an ephemeral tool like Cabin to generate an AES-256 encrypted note.</li>
    <li><strong>Enable Burn-After-Read:</strong> Ensure the note deletes immediately after the recipient opens the link.</li>
    <li><strong>Add a Password:</strong> Send the password via a different communication channel (e.g. call or SMS) for two-factor security.</li>
</ol>

<h2>Summary</h2>
<p>Never leave plaintext credentials in your chat history. Create a one-time encrypted note on Cabin and protect your team from unexpected leaks.</p>'
            ],
            [
                'slug' => 'aes-256-gcm-encryption-explained',
                'title' => 'What is AES-256-GCM Encryption & Why is it the Gold Standard for Notes?',
                'category' => 'Technology',
                'author' => 'Hussain Lone',
                'read_time' => '4 min read',
                'summary' => 'A clear, developer-friendly guide explaining how AES-256-GCM authenticated encryption protects your private notes from interception and tampering.',
                'meta_title' => 'AES-256-GCM Encryption Explained – How Private Notes Stay Safe',
                'meta_description' => 'Understand how AES-256-GCM authenticated encryption works, why it prevents tampering, and how it keeps private notes secure.',
                'meta_keywords' => 'aes 256 encryption, aes-256-gcm, encrypted notes security, authenticated encryption, data privacy',
                'content' => '<h2>What is AES-256?</h2>
<p><strong>Advanced Encryption Standard (AES)</strong> with a 256-bit key size is the encryption standard used by governments, banks, and cybersecurity institutions worldwide. It would take modern supercomputers billions of years to brute-force a single 256-bit key.</p>

<h2>Why the "GCM" Mode Matters</h2>
<p>Older encryption modes like CBC only encrypt data, but do not protect against data tampering. <strong>Galois/Counter Mode (GCM)</strong> provides <em>Authenticated Encryption with Associated Data (AEAD)</em>.</p>
<ul>
    <li><strong>Confidentiality:</strong> No unauthorized person can read the note content.</li>
    <li><strong>Authenticity & Integrity:</strong> An authentication tag ensures that if even a single bit of the encrypted payload is modified, decryption fails completely.</li>
</ul>

<h2>How Cabin Protects Your Data</h2>
<p>When you create a note on Cabin, your content is encrypted using AES-256-GCM with unique cryptographic Initialization Vectors (IVs) and authentication tags before persistence. Once the expiration time arrives or Burn-After-Read triggers, the payload is permanently wiped from the database.</p>'
            ]
        ];

        foreach ($posts as $p) {
            $this->create($p);
        }
    }

    /**
     * Get all blogs with optional filters
     *
     * @return Blog[]
     */
    public function getAll(string $status = 'published', ?string $category = null, ?string $search = null, int $limit = 50, int $offset = 0): array
    {
        $conditions = [];
        $params     = [];

        if ($status !== 'all') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        if (!empty($category) && $category !== 'all') {
            $conditions[] = "category = :category";
            $params['category'] = $category;
        }

        if (!empty($search)) {
            $conditions[] = "(title LIKE :search OR summary LIKE :search OR content LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql   = "SELECT * FROM blogs {$where} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";

        $rows = $this->db->fetchAll($sql, $params);

        return array_map(fn($row) => Blog::fromRow($row), $rows);
    }

    /**
     * Get total count with filters
     */
    public function count(string $status = 'published', ?string $category = null, ?string $search = null): int
    {
        $conditions = [];
        $params     = [];

        if ($status !== 'all') {
            $conditions[] = "status = :status";
            $params['status'] = $status;
        }

        if (!empty($category) && $category !== 'all') {
            $conditions[] = "category = :category";
            $params['category'] = $category;
        }

        if (!empty($search)) {
            $conditions[] = "(title LIKE :search OR summary LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $sql   = "SELECT COUNT(*) as cnt FROM blogs {$where}";

        $row = $this->db->fetchOne($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    /**
     * Get single blog by slug
     */
    public function getBySlug(string $slug): ?Blog
    {
        $row = $this->db->fetchOne("SELECT * FROM blogs WHERE slug = :slug LIMIT 1", ['slug' => $slug]);
        return $row ? Blog::fromRow($row) : null;
    }

    /**
     * Get single blog by ID
     */
    public function getById(int $id): ?Blog
    {
        $row = $this->db->fetchOne("SELECT * FROM blogs WHERE id = :id LIMIT 1", ['id' => $id]);
        return $row ? Blog::fromRow($row) : null;
    }

    /**
     * Create a new blog post
     */
    public function create(array $data): int
    {
        $slug = $this->sanitizeSlug($data['slug'] ?? $data['title']);

        // Check uniqueness of slug
        $existing = $this->getBySlug($slug);
        if ($existing) {
            $slug .= '-' . time();
        }

        $sql = "INSERT INTO blogs (
            slug, title, summary, content, cover_image, category, author, 
            meta_title, meta_description, meta_keywords, read_time, status
        ) VALUES (
            :slug, :title, :summary, :content, :cover_image, :category, :author,
            :meta_title, :meta_description, :meta_keywords, :read_time, :status
        )";

        $this->db->execute($sql, [
            'slug'             => $slug,
            'title'            => trim($data['title']),
            'summary'          => trim($data['summary'] ?? ''),
            'content'          => $data['content'] ?? '',
            'cover_image'      => !empty($data['cover_image']) ? trim($data['cover_image']) : null,
            'category'         => !empty($data['category']) ? trim($data['category']) : 'Security',
            'author'           => !empty($data['author']) ? trim($data['author']) : 'Hussain Lone',
            'meta_title'       => !empty($data['meta_title']) ? trim($data['meta_title']) : trim($data['title']),
            'meta_description' => !empty($data['meta_description']) ? trim($data['meta_description']) : trim($data['summary'] ?? ''),
            'meta_keywords'    => !empty($data['meta_keywords']) ? trim($data['meta_keywords']) : null,
            'read_time'        => !empty($data['read_time']) ? trim($data['read_time']) : '4 min read',
            'status'           => $data['status'] ?? 'published',
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing blog post
     */
    public function update(int $id, array $data): bool
    {
        $slug = $this->sanitizeSlug($data['slug'] ?? $data['title']);

        $sql = "UPDATE blogs SET
            slug = :slug,
            title = :title,
            summary = :summary,
            content = :content,
            cover_image = :cover_image,
            category = :category,
            author = :author,
            meta_title = :meta_title,
            meta_description = :meta_description,
            meta_keywords = :meta_keywords,
            read_time = :read_time,
            status = :status,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = :id";

        return $this->db->execute($sql, [
            'id'               => $id,
            'slug'             => $slug,
            'title'            => trim($data['title']),
            'summary'          => trim($data['summary'] ?? ''),
            'content'          => $data['content'] ?? '',
            'cover_image'      => !empty($data['cover_image']) ? trim($data['cover_image']) : null,
            'category'         => !empty($data['category']) ? trim($data['category']) : 'Security',
            'author'           => !empty($data['author']) ? trim($data['author']) : 'Hussain Lone',
            'meta_title'       => !empty($data['meta_title']) ? trim($data['meta_title']) : trim($data['title']),
            'meta_description' => !empty($data['meta_description']) ? trim($data['meta_description']) : trim($data['summary'] ?? ''),
            'meta_keywords'    => !empty($data['meta_keywords']) ? trim($data['meta_keywords']) : null,
            'read_time'        => !empty($data['read_time']) ? trim($data['read_time']) : '4 min read',
            'status'           => $data['status'] ?? 'published',
        ]) > 0;
    }

    /**
     * Delete blog post by ID
     */
    public function delete(int $id): bool
    {
        return $this->db->execute("DELETE FROM blogs WHERE id = :id", ['id' => $id]) > 0;
    }

    /**
     * Increment views
     */
    public function incrementViews(int $id): void
    {
        try {
            $this->db->execute("UPDATE blogs SET views = views + 1 WHERE id = :id", ['id' => $id]);
        } catch (\Throwable $e) {
            // ignore
        }
    }

    /**
     * Get distinct categories
     */
    public function getCategories(): array
    {
        $rows = $this->db->fetchAll("SELECT DISTINCT category FROM blogs WHERE status = 'published' ORDER BY category ASC");
        return array_column($rows, 'category');
    }

    /**
     * Get recent posts excluding current ID
     */
    public function getRecent(int $limit = 3, int $excludeId = 0): array
    {
        $sql = "SELECT * FROM blogs WHERE status = 'published' AND id != :excludeId ORDER BY created_at DESC LIMIT {$limit}";
        $rows = $this->db->fetchAll($sql, ['excludeId' => $excludeId]);
        return array_map(fn($row) => Blog::fromRow($row), $rows);
    }

    /**
     * Format slug
     */
    private function sanitizeSlug(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'blog-' . time() : substr($text, 0, 120);
    }
}
