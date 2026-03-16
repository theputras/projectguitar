<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class ImageService
{
    /**
     * Default compression settings
     */
    protected const DEFAULT_QUALITY = 80;        // JPEG quality (1-100)
    protected const MAX_WIDTH = 1920;             // Max width in pixels
    protected const MAX_HEIGHT = 1080;            // Max height in pixels
    protected const THUMBNAIL_WIDTH = 400;        // Thumbnail width
    protected const THUMBNAIL_HEIGHT = 400;       // Thumbnail height

    /**
     * Upload and optimize an image.
     * Automatically resizes to fit within max dimensions and compresses quality.
     *
     * @param UploadedFile $file        The uploaded file
     * @param string       $folder      Subfolder in public/uploads/ (e.g. 'portfolio', 'tonewoods')
     * @param int|null     $maxWidth    Override max width
     * @param int|null     $maxHeight   Override max height
     * @param int|null     $quality     Override JPEG quality (1-100)
     * @param bool         $makeThumbnail Whether to also create a thumbnail
     * @return array       ['path' => relative path, 'thumbnail' => thumbnail path (if created)]
     */
    public static function upload(
        UploadedFile $file,
        string $folder = 'general',
        ?int $maxWidth = null,
        ?int $maxHeight = null,
        ?int $quality = null,
        bool $makeThumbnail = false
    ): array {
        $maxWidth  = $maxWidth ?? self::MAX_WIDTH;
        $maxHeight = $maxHeight ?? self::MAX_HEIGHT;
        $quality   = $quality ?? self::DEFAULT_QUALITY;

        // Ensure directory exists
        $uploadPath = public_path("uploads/{$folder}");
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Generate unique filename with webp extension for better compression
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
        $filename = time() . '_' . $originalName . '.webp';

        // Process & compress main image
        $image = Image::make($file);

        // Resize while maintaining aspect ratio (only downscale, never upscale)
        $image->resize($maxWidth, $maxHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize(); // Prevent upsizing
        });

        // Save as WebP (best compression ratio for web)
        $image->encode('webp', $quality);
        $image->save("{$uploadPath}/{$filename}");

        $result = [
            'path' => "uploads/{$folder}/{$filename}",
        ];

        // Create thumbnail if requested
        if ($makeThumbnail) {
            $thumbPath = public_path("uploads/{$folder}/thumbs");
            if (!File::isDirectory($thumbPath)) {
                File::makeDirectory($thumbPath, 0755, true);
            }

            $thumbFilename = 'thumb_' . $filename;

            $thumb = Image::make($file);
            $thumb->fit(self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT);
            $thumb->encode('webp', $quality - 10); // Slightly more compressed
            $thumb->save("{$thumbPath}/{$thumbFilename}");

            $result['thumbnail'] = "uploads/{$folder}/thumbs/{$thumbFilename}";
        }

        return $result;
    }

    /**
     * Upload multiple gallery images with optimization.
     *
     * @param array  $files   Array of UploadedFile
     * @param string $folder  Subfolder
     * @return array Array of relative paths
     */
    public static function uploadGallery(array $files, string $folder = 'portfolio'): array
    {
        $paths = [];

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $result = self::upload($file, $folder, null, null, null, true);
                $paths[] = $result['path'];
            }
        }

        return $paths;
    }

    /**
     * Delete an image file from public path.
     *
     * @param string|null $path Relative path (e.g. 'uploads/portfolio/image.webp')
     */
    public static function delete(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }

        // Also try to delete thumbnail
        $dir = dirname($path);
        $basename = basename($path);
        $thumbPath = "{$dir}/thumbs/thumb_{$basename}";
        if (file_exists(public_path($thumbPath))) {
            unlink(public_path($thumbPath));
        }
    }

    /**
     * Delete multiple image files.
     *
     * @param array|null $paths Array of relative paths
     */
    public static function deleteMany(?array $paths): void
    {
        if (!$paths) return;

        foreach ($paths as $path) {
            self::delete($path);
        }
    }
}
