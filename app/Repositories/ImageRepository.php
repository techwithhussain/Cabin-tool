<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\NoteImage;
use App\Services\ImageService;

/**
 * ImageRepository – Database operations for note images
 */
class ImageRepository
{
    private Database     $db;
    private ImageService $imageService;

    public function __construct()
    {
        $this->db           = Database::getInstance();
        $this->imageService = new ImageService();
    }

    /**
     * Save image metadata to DB after processing
     */
    public function save(int $noteId, string $noteSlug, array $imageData): NoteImage
    {
        $this->db->execute(
            'INSERT INTO note_images
             (note_id, note_slug, filename, original_name, mime_type, size_bytes, width, height, storage_path, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
            [
                $noteId,
                $noteSlug,
                $imageData['filename'],
                $imageData['original_name'],
                $imageData['mime_type'],
                $imageData['size_bytes'],
                $imageData['width']  ?? null,
                $imageData['height'] ?? null,
                $imageData['storage_path'],
            ]
        );

        $id  = (int) $this->db->lastInsertId();
        $row = $this->db->fetchOne('SELECT * FROM note_images WHERE id = ?', [$id]);

        return NoteImage::fromRow($row);
    }

    /**
     * Get all images for a note slug
     * @return NoteImage[]
     */
    public function getBySlug(string $noteSlug): array
    {
        $rows = $this->db->fetchAll(
            'SELECT * FROM note_images WHERE note_slug = ? ORDER BY id ASC',
            [$noteSlug]
        );

        return array_map(fn($row) => NoteImage::fromRow($row), $rows);
    }

    /**
     * Count images for a note
     */
    public function countBySlug(string $noteSlug): int
    {
        $row = $this->db->fetchOne(
            'SELECT COUNT(*) as c FROM note_images WHERE note_slug = ?',
            [$noteSlug]
        );
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Find a single image by slug + filename
     */
    public function findByFilename(string $noteSlug, string $filename): ?NoteImage
    {
        $row = $this->db->fetchOne(
            'SELECT * FROM note_images WHERE note_slug = ? AND filename = ? LIMIT 1',
            [$noteSlug, $filename]
        );

        return $row ? NoteImage::fromRow($row) : null;
    }

    /**
     * Delete all image records (DB + disk) for a note
     */
    public function deleteBySlug(string $noteSlug): void
    {
        // Delete DB records (cascade from notes table handles this too, but belt-and-suspenders)
        $this->db->execute('DELETE FROM note_images WHERE note_slug = ?', [$noteSlug]);

        // Delete files from disk
        $this->imageService->deleteNoteImages($noteSlug);
    }
}
