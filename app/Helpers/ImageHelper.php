<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageHelper
{
    /**
     * Convert an uploaded image to WebP and save it.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @param string $directory
     * @param int $quality
     * @param int|null $maxWidth
     * @param bool $createThumbnail
     * @return string|false The path to the saved WebP image relative to 'public' disk
     */
    public static function uploadAndConvert($file, $directory, $quality = 80, $maxWidth = 1200, $createThumbnail = false)
    {
        try {
            $isFilePath = is_string($file);
            $extension = $isFilePath ? pathinfo($file, PATHINFO_EXTENSION) : strtolower($file->getClientOriginalExtension());
            $realPath = $isFilePath ? $file : $file->getRealPath();
            
            $imageName = Str::random(40) . '.webp';
            $filePath = $directory . '/' . $imageName;

            // Load the image based on its type
            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $image = imagecreatefromjpeg($realPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($realPath);
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($realPath);
                    break;
                case 'webp':
                    $image = imagecreatefromwebp($realPath);
                    break;
                default:
                    return false;
            }

            if (!$image) return false;

            // Resize logic if maxWidth is set
            $width = imagesx($image);
            $height = imagesy($image);

            if ($maxWidth && $width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = floor($height * ($maxWidth / $width));
                $tmpImage = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($tmpImage, false);
                imagesavealpha($tmpImage, true);
                imagecopyresampled($tmpImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $tmpImage;
            }

            // Save Main Image as WebP
            $tempPath = tempnam(sys_get_temp_dir(), 'webp');
            imagewebp($image, $tempPath, $quality);
            Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($tempPath), $imageName);
            
            // Thumbnail Logic
            if ($createThumbnail) {
                $thumbWidth = 400;
                $thumbHeight = floor(imagesy($image) * ($thumbWidth / imagesx($image)));
                $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
                imagealphablending($thumbImage, false);
                imagesavealpha($thumbImage, true);
                imagecopyresampled($thumbImage, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, imagesx($image), imagesy($image));
                
                $thumbTempPath = tempnam(sys_get_temp_dir(), 'thumb');
                imagewebp($thumbImage, $thumbTempPath, 60); // Moins de qualité pour la miniature
                Storage::disk('public')->putFileAs($directory . '/thumbnails', new \Illuminate\Http\File($thumbTempPath), $imageName);
                
                imagedestroy($thumbImage);
                unlink($thumbTempPath);
            }

            imagedestroy($image);
            unlink($tempPath);

            return $filePath;
        } catch (\Exception $e) {
            return false;
        }
    }
}
