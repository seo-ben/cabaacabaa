<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Convert an uploaded image to WebP and save it.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int $quality
     * @param int|null $maxWidth
     * @return string|false The path to the saved WebP image relative to 'public' disk
     */
    public static function uploadAndConvert($file, $directory, $quality = 80, $maxWidth = 1200)
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $imageName = Str::random(40) . '.webp';
            $filePath = $directory . '/' . $imageName;

            // Load the image based on its type
            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($file->getRealPath());
                    break;
                case 'png':
                    $image = imagecreatefrompng($file->getRealPath());
                    // Preserve transparency for conversion, though WebP handles it
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($file->getRealPath());
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($file->getRealPath());
                    break;
                default:
                    // SECURITY FIX: Never store the file directly if the extension is not explicitly supported.
                    // This prevents RCE attacks where a user uploads a PHP file disguised as an image.
                    return false;
            }

            if (!$image) {
                return false;
            }

            // Resize logic if maxWidth is set
            $width = imagesx($image);
            $height = imagesy($image);

            if ($maxWidth && $width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = floor($height * ($maxWidth / $width));
                $tmpImage = imagecreatetruecolor($newWidth, $newHeight);

                // Keep transparency for PNG/WebP
                imagealphablending($tmpImage, false);
                imagesavealpha($tmpImage, true);

                imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $tmpImage;
            }

            // Save as WebP
            $tempPath = tempnam(sys_get_temp_dir(), 'webp');
            imagewebp($image, $tempPath, $quality);
            imagedestroy($image);

            // Store in Laravel disks
            Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempPath), $imageName);
            unlink($tempPath);

            return $filePath;
        } catch (\Exception $e) {
            // SECURITY FIX: fail gracefully instead of saving unsafe content
            // Log::error('Image conversion failed: ' . $e->getMessage());
            return false;
        }
    }
}
