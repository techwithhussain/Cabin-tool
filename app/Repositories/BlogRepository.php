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
        } catch (\Throwable $e) {
            error_log("[BlogRepository] Error ensuring table: " . $e->getMessage());
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
