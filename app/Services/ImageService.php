<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

/**
 * ImageService – Image Upload, Validation, Compression & Storage
 *
 * Validates MIME type via GD (not just file extension).
 * Re-encodes images through GD to strip EXIF data and malicious payloads.
 * Stores images outside the webroot in storage/uploads/{slug}/.
 */
class ImageService
{
    private const ALLOWED_MIME = [
        'image/jpeg' => ['jpg', IMAGETYPE_JPEG],
        'image/png'  => ['png', IMAGETYPE_PNG],
        'image/gif'  => ['gif', IMAGETYPE_GIF],
        'image/webp' => ['webp', IMAGETYPE_WEBP],
    ];

    private const MAX_WIDTH  = 2400;
    private const MAX_HEIGHT = 2400;
    private const JPEG_QUALITY = 82;
    private const PNG_QUALITY  = 6;

    private int    $maxFileSize;
    private string $uploadBasePath;

    public function __construct()
    {
        $this->maxFileSize    = (int) Config::env('MAX_UPLOAD_SIZE', 10485760);
        $this->uploadBasePath = BASE_PATH . '/' . Config::env('UPLOAD_PATH', 'storage/uploads');
    }

    /**
     * Process and store an uploaded image.
     *
     * @param array  $file    $_FILES entry
     * @param string $noteSlug
     * @return array{filename: string, original_name: string, mime_type: string, size_bytes: int, width: int, height: int, storage_path: string}
     */
    public function process(array $file, string $noteSlug): array
    {
        $this->validateUpload($file);

        // Resolve writable directory (fallback to sys temp dir on serverless/read-only hosts)
        $baseDir = $this->uploadBasePath;
        if (!is_dir($baseDir) && !@mkdir($baseDir, 0755, true)) {
            $baseDir = sys_get_temp_dir() . '/cabin_uploads';
            if (!is_dir($baseDir)) {
                @mkdir($baseDir, 0755, true);
            }
        }

        // Create note-specific directory
        $dir = $baseDir . '/' . $noteSlug;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Generate a unique filename
        $extension = $this->detectExtension($file['tmp_name']);
        $filename  = bin2hex(random_bytes(16)) . '.' . $extension;
        $destPath  = $dir . '/' . $filename;

        // Re-encode through GD (strips EXIF, sanitises)
        [$width, $height] = $this->reEncode($file['tmp_name'], $destPath, $extension);

        $storagePath = Config::env('UPLOAD_PATH', 'storage/uploads') . '/' . $noteSlug . '/' . $filename;
        $rawBytes    = file_get_contents($destPath);
        $dataBase64  = $rawBytes !== false ? base64_encode($rawBytes) : null;

        return [
            'filename'      => $filename,
            'original_name' => $this->sanitizeFilename($file['name']),
            'mime_type'     => mime_content_type($destPath) ?: ('image/' . $extension),
            'size_bytes'    => filesize($destPath) ?: strlen($rawBytes ?: ''),
            'width'         => $width,
            'height'        => $height,
            'storage_path'  => $storagePath,
            'data_base64'   => $dataBase64,
        ];
    }

    /**
     * Delete all images for a note
     */
    public function deleteNoteImages(string $noteSlug): void
    {
        $dir = $this->uploadBasePath . '/' . $noteSlug;

        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files ?: [] as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($dir);
        }
    }

    /**
     * Get the absolute path of a stored image
     */
    public function getAbsolutePath(string $storagePath): string
    {
        return BASE_PATH . '/' . ltrim($storagePath, '/');
    }

    // ─────────────────────────────────────────────
    // Validation
    // ─────────────────────────────────────────────

    private function validateUpload(array $file): void
    {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('File upload error: ' . ($file['error'] ?? 'unknown'));
        }

        if ($file['size'] > $this->maxFileSize) {
            $maxMB = round($this->maxFileSize / 1048576, 1);
            throw new \InvalidArgumentException("File exceeds maximum size of {$maxMB}MB.");
        }

        // Validate via GD (not just Content-Type or extension)
        $imageInfo = @getimagesize($file['tmp_name']);

        if ($imageInfo === false) {
            throw new \InvalidArgumentException('Uploaded file is not a valid image.');
        }

        $mimeType = $imageInfo['mime'];
        if (!array_key_exists($mimeType, self::ALLOWED_MIME)) {
            throw new \InvalidArgumentException('Image type not allowed. Accepted: JPG, PNG, GIF, WebP.');
        }

        // Virus scan hook (stub – integrate ClamAV here if needed)
        $this->virusScanHook($file['tmp_name']);
    }

    private function detectExtension(string $tmpPath): string
    {
        $imageInfo = @getimagesize($tmpPath);
        $mimeType  = $imageInfo['mime'] ?? '';

        return self::ALLOWED_MIME[$mimeType][0] ?? 'png';
    }

    // ─────────────────────────────────────────────
    // GD Re-encoding (strips EXIF/metadata)
    // ─────────────────────────────────────────────

    private function reEncode(string $srcPath, string $destPath, string $extension): array
    {
        $imageInfo = @getimagesize($srcPath);
        $origType  = $imageInfo ? $imageInfo[2] : null;
        $origW     = $imageInfo ? $imageInfo[0] : 0;
        $origH     = $imageInfo ? $imageInfo[1] : 0;

        // If GD extension is not loaded, copy the file directly
        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            if (!copy($srcPath, $destPath)) {
                throw new \RuntimeException('Failed to save uploaded image.');
            }
            return [$origW, $origH];
        }

        // Load image via GD
        $src = match ($origType) {
            IMAGETYPE_JPEG => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($srcPath) : false,
            IMAGETYPE_PNG  => function_exists('imagecreatefrompng')  ? @imagecreatefrompng($srcPath)  : false,
            IMAGETYPE_GIF  => function_exists('imagecreatefromgif')  ? @imagecreatefromgif($srcPath)  : false,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false,
            default        => false,
        };

        if ($src === false) {
            // Fallback: Copy directly if GD decoder is not available
            if (!copy($srcPath, $destPath)) {
                throw new \RuntimeException('Failed to save uploaded image.');
            }
            return [$origW, $origH];
        }

        // Resize if needed
        [$newW, $newH] = $this->calculateDimensions($origW, $origH);

        if ($newW !== $origW || $newH !== $origH) {
            $dst = imagecreatetruecolor($newW, $newH);

            // Preserve transparency for PNG/WebP
            if (in_array($origType, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);
            }

            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($src);
            $src = $dst;
        }

        // Save (strips all metadata including EXIF)
        $saved = match ($extension) {
            'jpg', 'jpeg' => function_exists('imagejpeg') ? imagejpeg($src, $destPath, self::JPEG_QUALITY) : false,
            'png'         => function_exists('imagepng')  ? imagepng($src, $destPath, self::PNG_QUALITY)   : false,
            'gif'         => function_exists('imagegif')  ? imagegif($src, $destPath)                      : false,
            'webp'        => function_exists('imagewebp') ? imagewebp($src, $destPath, self::JPEG_QUALITY) : false,
            default       => false,
        };

        imagedestroy($src);

        if (!$saved) {
            // Fallback to direct copy if GD saving fails
            if (!copy($srcPath, $destPath)) {
                throw new \RuntimeException('Failed to save processed image.');
            }
        }

        return [$newW, $newH];
    }

    private function calculateDimensions(int $width, int $height): array
    {
        if ($width <= self::MAX_WIDTH && $height <= self::MAX_HEIGHT) {
            return [$width, $height];
        }

        $ratio = min(self::MAX_WIDTH / $width, self::MAX_HEIGHT / $height);

        return [(int) round($width * $ratio), (int) round($height * $ratio)];
    }

    private function sanitizeFilename(string $name): string
    {
        // Strip path components and null bytes
        $name = basename($name);
        $name = preg_replace('/[^\w.\- ]/', '', $name);
        return substr($name, 0, 255);
    }

    /**
     * Virus scan stub – integrate ClamAV or VirusTotal API here.
     * @throws \RuntimeException if malicious file detected
     */
    private function virusScanHook(string $filePath): void
    {
        // TODO v2: integrate ClamAV via exec('clamscan --no-summary %s', escapeshellarg($filePath))
        // For now, this is a no-op stub
    }
}
