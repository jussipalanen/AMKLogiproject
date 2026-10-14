<?php

declare(strict_types=1);

namespace app\components;

/**
 * Scales images down and re-encodes them as JPEG with GD, which also strips metadata
 * and anything that is not image data.
 */
final class ImageResizer
{
    public static function available(): bool
    {
        return function_exists('imagecreatefromstring');
    }

    /**
     * Writes $source as a JPEG that fits inside $maxSize × $maxSize pixels.
     *
     * @return array{0: int, 1: int}|null the written width and height, or null when GD is
     *     not installed or the file is not an image GD can read
     */
    public static function toJpeg(string $source, string $target, int $maxSize, int $quality = 85): ?array
    {
        if (!self::available()) {
            return null;
        }

        $image = @imagecreatefromstring((string) file_get_contents($source));

        if ($image === false) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxSize / max($width, $height));
        $newWidth = max(1, (int) round($width * $scale));
        $newHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        // White background for transparent PNG/GIF/WebP images.
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return imagejpeg($canvas, $target, $quality) ? [$newWidth, $newHeight] : null;
    }
}
