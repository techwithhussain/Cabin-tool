<?php

declare(strict_types=1);

namespace App\Models;

/**
 * NoteImage – Image record attached to a note
 */
class NoteImage
{
    public function __construct(
        public readonly int    $id,
        public readonly int    $noteId,
        public readonly string $noteSlug,
        public readonly string $filename,
        public readonly string $originalName,
        public readonly string $mimeType,
        public readonly int    $sizeBytes,
        public readonly ?int   $width,
        public readonly ?int   $height,
        public readonly string $storagePath,
        public readonly string $createdAt,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id:           (int) $row['id'],
            noteId:       (int) $row['note_id'],
            noteSlug:     $row['note_slug'],
            filename:     $row['filename'],
            originalName: $row['original_name'],
            mimeType:     $row['mime_type'],
            sizeBytes:    (int) $row['size_bytes'],
            width:        isset($row['width']) ? (int) $row['width'] : null,
            height:       isset($row['height']) ? (int) $row['height'] : null,
            storagePath:  $row['storage_path'],
            createdAt:    $row['created_at'],
        );
    }

    public function humanSize(): string
    {
        if ($this->sizeBytes < 1024)       return $this->sizeBytes . ' B';
        if ($this->sizeBytes < 1048576)    return round($this->sizeBytes / 1024, 1) . ' KB';
        return round($this->sizeBytes / 1048576, 2) . ' MB';
    }
}
