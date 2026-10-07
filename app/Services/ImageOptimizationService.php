<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    /**
     * Optimize, resize, and convert an uploaded product or device image to WebP
     * Generates Large (1200px), Medium (500px), and Thumbnail (150px)
     * Returns the primary relative storage path (e.g., 'products/prod_1728394_abc123.webp')
     */
    public function optimizeImage(string $absoluteSourcePath, string $targetDirectory = 'products'): ?string
    {
        if (!file_exists($absoluteSourcePath) || !is_readable($absoluteSourcePath)) {
            return null;
        }

        $imageInfo = @getimagesize($absoluteSourcePath);
        if (!$imageInfo) {
            return null;
        }

        $mime = $imageInfo['mime'];
        $srcImage = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($absoluteSourcePath),
            'image/png' => @imagecreatefrompng($absoluteSourcePath),
            'image/webp' => @imagecreatefromwebp($absoluteSourcePath),
            default => null,
        };

        if (!$srcImage) {
            return null;
        }

        // Auto-orient based on smartphone camera EXIF orientation tag
        if (function_exists('exif_read_data') && ($mime === 'image/jpeg' || $mime === 'image/jpg')) {
            $exif = @exif_read_data($absoluteSourcePath);
            if (!empty($exif['Orientation'])) {
                $srcImage = match ((int) $exif['Orientation']) {
                    3 => imagerotate($srcImage, 180, 0),
                    6 => imagerotate($srcImage, -90, 0),
                    8 => imagerotate($srcImage, 90, 0),
                    default => $srcImage,
                };
            }
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Ensure storage directory exists
        $diskPath = storage_path('app/public/' . trim($targetDirectory, '/'));
        if (!is_dir($diskPath)) {
            mkdir($diskPath, 0755, true);
        }

        $baseName = 'img_' . time() . '_' . Str::lower(Str::random(8));

        // 1. Large Version (Max width 1200px) - Primary
        $primaryPath = $this->resizeAndSaveWebp($srcImage, $origWidth, $origHeight, 1200, "{$diskPath}/{$baseName}.webp");

        // 2. Medium Version (Max width 500px)
        $this->resizeAndSaveWebp($srcImage, $origWidth, $origHeight, 500, "{$diskPath}/{$baseName}_med.webp");

        // 3. Thumbnail Version (Max width 150px)
        $this->resizeAndSaveWebp($srcImage, $origWidth, $origHeight, 150, "{$diskPath}/{$baseName}_thumb.webp");

        imagedestroy($srcImage);

        return trim($targetDirectory, '/') . '/' . "{$baseName}.webp";
    }

    /**
     * Resize to max dimension and save as WebP with 82% quality
     */
    protected function resizeAndSaveWebp($srcImage, int $origWidth, int $origHeight, int $maxDim, string $targetFile): bool
    {
        if ($origWidth <= $maxDim && $origHeight <= $maxDim) {
            $newWidth = $origWidth;
            $newHeight = $origHeight;
        } else {
            $ratio = min($maxDim / $origWidth, $maxDim / $origHeight);
            $newWidth = max(1, (int) round($origWidth * $ratio));
            $newHeight = max(1, (int) round($origHeight * $ratio));
        }

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha transparency for PNG / WebP
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefilledrectangle($destImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled(
            $destImage,
            $srcImage,
            0, 0, 0, 0,
            $newWidth,
            $newHeight,
            $origWidth,
            $origHeight
        );

        $saved = imagewebp($destImage, $targetFile, 82);
        imagedestroy($destImage);

        return $saved;
    }
}
