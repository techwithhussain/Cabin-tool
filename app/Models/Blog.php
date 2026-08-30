<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Blog – Blog Post Model
 */
class Blog
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $slug,
        public readonly string  $title,
        public readonly string  $summary,
        public readonly string  $content,
        public readonly ?string $coverImage,
        public readonly string  $category,
        public readonly string  $author,
        public readonly ?string $metaTitle,
        public readonly ?string $metaDescription,
        public readonly ?string $metaKeywords,
        public readonly string  $readTime,
        public readonly string  $status,
        public readonly int     $views,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
    ) {}

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id:              (int) $row['id'],
            slug:            $row['slug'],
            title:           $row['title'],
            summary:         $row['summary'] ?? '',
            content:         $row['content'] ?? '',
            coverImage:      $row['cover_image'] ?? null,
            category:        $row['category'] ?? 'Security',
            author:          $row['author'] ?? 'Hussain Lone',
            metaTitle:       $row['meta_title'] ?? null,
            metaDescription: $row['meta_description'] ?? null,
            metaKeywords:    $row['meta_keywords'] ?? null,
            readTime:        $row['read_time'] ?? '3 min read',
            status:          $row['status'] ?? 'published',
            views:           (int) ($row['views'] ?? 0),
            createdAt:       $row['created_at'] ?? date('Y-m-d H:i:s'),
            updatedAt:       $row['updated_at'] ?? date('Y-m-d H:i:s'),
        );
    }
}
