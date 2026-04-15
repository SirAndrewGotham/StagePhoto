<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageProcessingService
{
    protected ImageManager $manager;

    protected array $config;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver);
        $this->config = config('image');
    }

    /**
     * Process a single uploaded file
     */
    public function processUpload($file, Album $album, ?string $description = null, ?string $title = null): Photo
    {
        // If no album specified or album doesn't exist, use unsorted
        if (! $album) {
            $unsortedService = app(UnsortedAlbumService::class);
            $album = $unsortedService->getOrCreateUnsortedAlbum(auth()->user());
        }

        // Generate unique ID for the photo
        $photoId = (string) Str::uuid();

        // Get file hash for duplicate detection
        $hash = md5_file($file->getRealPath());

        // Check for duplicate
        $existing = Photo::where('hash', $hash)->first();
        if ($existing) {
            throw new \Exception('This image has already been uploaded to another album.');
        }

        // Store original
        $originalPath = $this->storeOriginal($file, $album->user_id, $album->id, $photoId);

        // Generate WebP variants
        $thumbPath = $this->generateThumbnail($file, $album->user_id, $album->id, $photoId);
        $fullPath = $this->generateFullImage($file, $album->user_id, $album->id, $photoId);

        // Create photo record
        $photo = Photo::create([
            'id' => $photoId,
            'album_id' => $album->id,
            'title' => $title ?? pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME),
            'description' => $description,
            'original_path' => $originalPath,
            'full_path' => $fullPath,
            'thumbnail_path' => $thumbPath,
            'hash' => $hash,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'sort_order' => $album->photos()->max('sort_order') + 1,
        ]);

        // Update album photo count
        $album->increment('photo_count');

        // If this is the first photo, set as album cover
        if ($album->photos()->count() === 1) {
            $this->setAlbumCover($photo, $album);
        }

        return $photo;
    }

    /**
     * Process a ZIP file upload
     */
    public function processZipUpload($zipFile, Album $album): array
    {
        $processed = [];
        $errors = [];

        // Create temp directory
        $tempDir = storage_path('app/temp/'.Str::uuid());
        mkdir($tempDir, 0755, true);

        // Extract ZIP
        $zip = new \ZipArchive;
        if ($zip->open($zipFile->getRealPath()) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            throw new \Exception('Unable to extract ZIP file');
        }

        // Process each image file
        $files = glob($tempDir.'/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);

        foreach ($files as $filePath) {
            try {
                $uploadedFile = new UploadedFile(
                    $filePath,
                    basename($filePath),
                    mime_content_type($filePath),
                    null,
                    true
                );

                $photo = $this->processUpload($uploadedFile, $album);
                $processed[] = $photo;
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => basename($filePath),
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Clean up temp directory
        $this->deleteDirectory($tempDir);

        return [
            'processed' => $processed,
            'errors' => $errors,
            'total' => count($files),
            'success_count' => count($processed),
            'error_count' => count($errors),
        ];
    }

    /**
     * Store original file
     */
    protected function storeOriginal($file, $userId, $albumId, $photoId): string
    {
        $path = "stagephoto/originals/{$userId}/{$albumId}/{$photoId}_original.{$file->getClientOriginalExtension()}";
        Storage::disk('public')->put($path, file_get_contents($file));

        return $path;
    }

    /**
     * Generate thumbnail (600x600 square crop)
     */
    protected function generateThumbnail($file, $userId, $albumId, $photoId): string
    {
        $image = $this->manager->read($file);

        // Crop to square (center)
        $size = min($image->width(), $image->height());
        $image->crop($size, $size, ($image->width() - $size) / 2, ($image->height() - $size) / 2);

        // Resize to 600x600
        $image->resize(600, 600);

        // Apply watermark
        $this->applyWatermark($image);

        // Encode as WebP
        $encoded = $image->toWebp(80);

        $path = "stagephoto/webp/{$userId}/{$albumId}/{$photoId}_thumb.webp";
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Generate full-size image (1600px max side)
     */
    protected function generateFullImage($file, $userId, $albumId, $photoId): string
    {
        $image = $this->manager->read($file);

        // Resize to max 1600px on longest side
        $image->resize(1600, 1600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Apply watermark
        $this->applyWatermark($image);

        // Encode as WebP
        $encoded = $image->toWebp(85);

        $path = "stagephoto/webp/{$userId}/{$albumId}/{$photoId}_full.webp";
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Apply StagePhoto watermark
     */
    protected function applyWatermark($image): void
    {
        $watermarkPath = public_path('images/watermark.png');

        if (! file_exists($watermarkPath)) {
            return;
        }

        $watermark = $this->manager->read($watermarkPath);

        // Resize watermark to be 150px wide
        $watermark->resize(150, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Apply watermark to bottom-right corner
        $image->place($watermark, 'bottom-right', 10, 10);
    }

    /**
     * Set album cover from a photo
     */
    public function setAlbumCover(Photo $photo, Album $album): void
    {
        // Generate square cover (800x800)
        $squareCover = $this->generateAlbumCoverSquare($photo);

        // Generate hero cover (2000x800)
        $heroCover = $this->generateAlbumCoverHero($photo);

        $album->update([
            'cover_image_square' => $squareCover,
            'cover_image_hero' => $heroCover,
        ]);
    }

    /**
     * Generate square album cover
     */
    protected function generateAlbumCoverSquare(Photo $photo): string
    {
        $fullPath = Storage::disk('public')->path($photo->full_path);
        $image = $this->manager->read($fullPath);

        // Crop to square (center)
        $size = min($image->width(), $image->height());
        $image->crop($size, $size, ($image->width() - $size) / 2, ($image->height() - $size) / 2);

        // Resize to 800x800
        $image->resize(800, 800);

        // Encode as WebP (no watermark on album covers)
        $encoded = $image->toWebp(85);

        $path = "stagephoto/albums/{$photo->album->user_id}/{$photo->album_id}/cover_square.webp";
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Generate hero album cover
     */
    protected function generateAlbumCoverHero(Photo $photo): string
    {
        $fullPath = Storage::disk('public')->path($photo->full_path);
        $image = $this->manager->read($fullPath);

        // Resize to 2000x800 (crop if needed)
        $image->resize(2000, 800, function ($constraint) {
            $constraint->aspectRatio();
        });

        // If aspect ratio doesn't match, crop to fit
        if ($image->width() != 2000 || $image->height() != 800) {
            $image->crop(2000, 800);
        }

        // Encode as WebP (no watermark on album covers)
        $encoded = $image->toWebp(85);

        $path = "stagephoto/albums/{$photo->album->user_id}/{$photo->album_id}/cover_hero.webp";
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Delete all images for a photo
     */
    public function deletePhotoImages(Photo $photo): void
    {
        $paths = [
            $photo->original_path,
            $photo->full_path,
            $photo->thumbnail_path,
        ];

        foreach ($paths as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Recursive directory deletion
     */
    protected function deleteDirectory(string $dir): void
    {
        if (! file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Soft delete a photo and optionally remove files
     */
    public function softDeletePhoto(Photo $photo, bool $removeFiles = false): void
    {
        $photo->delete();

        if ($removeFiles) {
            $this->deletePhotoImages($photo);
        }

        // Update album photo count
        $photo->album->decrement('photo_count');
    }

    /**
     * Restore a soft-deleted photo
     */
    public function restorePhoto(Photo $photo): void
    {
        $photo->restore();

        // Update album photo count
        $photo->album->increment('photo_count');
    }

    /**
     * Permanently delete a photo and its files
     */
    public function forceDeletePhoto(Photo $photo): void
    {
        $this->deletePhotoImages($photo);
        $photo->forceDelete();
    }

    /**
     * Soft delete an album and all its photos
     */
    public function softDeleteAlbum(Album $album, bool $removeFiles = false): void
    {
        // Soft delete all photos first
        foreach ($album->photos as $photo) {
            $this->softDeletePhoto($photo, $removeFiles);
        }

        $album->delete();
    }

    /**
     * Restore a soft-deleted album and all its photos
     */
    public function restoreAlbum(Album $album): void
    {
        // Restore all photos first
        foreach ($album->photos()->withTrashed()->get() as $photo) {
            $photo->restore();
        }

        $album->restore();
    }

    /**
     * Permanently delete an album and all its files
     */
    public function forceDeleteAlbum(Album $album): void
    {
        // Permanently delete all photos
        foreach ($album->photos as $photo) {
            $this->forceDeletePhoto($photo);
        }

        $album->forceDelete();
    }
}
