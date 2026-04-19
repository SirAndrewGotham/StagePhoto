<?php

namespace App\Services;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ImageProcessingService
{
    protected $manager;
    protected ExifExtractorService $exifExtractor;

    public function __construct(ExifExtractorService $exifExtractor)
    {
        // Intervention Image v4.0.1 requires a driver instance
        $this->manager = new ImageManager(new Driver());
        $this->exifExtractor = $exifExtractor;

        \Log::info('ImageProcessingService initialized with GdDriver');
    }

    /**
     * Process an image - using decodePath for v4.0.1
     */
    private function processImage($filePath, callable $callback)
    {
        \Log::info('processImage: starting', ['filePath' => $filePath]);

        try {
            // For v4.0.1, we need to decode the path to create an image
            $image = $this->manager->decodePath($filePath);

            \Log::info('processImage: image created successfully', [
                'width' => $image->width(),
                'height' => $image->height()
            ]);
            $callback($image);
            return $image;
        } catch (\Exception $e) {
            \Log::error('processImage failed: ' . $e->getMessage());
            throw new \Exception('Unable to initialize image processing: ' . $e->getMessage());
        }
    }

    /**
     * Encode image to WebP using v4 Encoder
     */
    private function encodeToWebp($image, int $quality): string
    {
        // Create WebP encoder with quality setting
        $encoder = new WebpEncoder(quality: $quality);

        // Encode the image
        $encodedImage = $image->encode($encoder);

        // Return as string
        return (string) $encodedImage;
    }

    /**
     * Get the real path from a file
     */
    private function getRealPath($file): string
    {
        if ($file instanceof TemporaryUploadedFile || $file instanceof UploadedFile) {
            return $file->getRealPath();
        }

        if (is_string($file)) {
            return $file;
        }

        throw new \Exception('Invalid file type provided');
    }

    /**
     * Get the original filename
     */
    private function getOriginalName($file): string
    {
        if ($file instanceof TemporaryUploadedFile || $file instanceof UploadedFile) {
            return $file->getClientOriginalName();
        }

        return 'photo';
    }

    /**
     * Get the file extension
     */
    private function getExtension($file): string
    {
        if ($file instanceof TemporaryUploadedFile || $file instanceof UploadedFile) {
            return $file->getClientOriginalExtension();
        }

        return 'jpg';
    }

    public function processUpload(
        $file,
        Album $album,
        ?string $title = null,
        ?string $description = null
    ): Photo {
        \Log::info('Step 1: processUpload started');

        // Get the real file path
        $filePath = $this->getRealPath($file);
        \Log::info('Step 2: got real path', ['path' => $filePath]);

        // Extract EXIF data
        $exifData = $this->exifExtractor->extract($file);
        \Log::info('Step 3: EXIF extracted', ['camera' => $exifData['camera_make'] ?? 'none']);

        $photoId = (string) Str::uuid();
        $hash = md5_file($filePath);
        \Log::info('Step 4: hash generated', ['hash' => $hash]);

        // Check for duplicate
        try {
            $existing = Photo::where('hash', $hash)->first();
            if ($existing) {
                throw new \Exception('This image has already been uploaded.');
            }
            \Log::info('Step 5: no duplicate found');
        } catch (\Exception $e) {
            \Log::error('Step 5 failed: ' . $e->getMessage());
            throw $e;
        }

        // Get original filename and extension
        try {
            $originalName = $this->getOriginalName($file);
            $extension = $this->getExtension($file);
            \Log::info('Step 6: original name', ['name' => $originalName, 'extension' => $extension]);
        } catch (\Exception $e) {
            \Log::error('Step 6 failed: ' . $e->getMessage());
            throw $e;
        }

        // Store original
        try {
            $originalPath = $this->storeOriginal($filePath, $album->photographer_id, $album->id, $photoId, $extension);
            \Log::info('Step 7: original stored', ['original_path' => $originalPath]);
        } catch (\Exception $e) {
            \Log::error('Step 7 failed: ' . $e->getMessage());
            throw $e;
        }

        // Generate WebP variants
        try {
            $thumbPath = $this->generateThumbnail($filePath, $album->photographer_id, $album->id, $photoId);
            \Log::info('Step 8: thumbnail generated', ['thumb_path' => $thumbPath]);
        } catch (\Exception $e) {
            \Log::error('Step 8 failed: ' . $e->getMessage());
            \Log::error('Step 8 exception trace: ' . $e->getTraceAsString());
            throw $e;
        }

        try {
            $fullPath = $this->generateFullImage($filePath, $album->photographer_id, $album->id, $photoId);
            \Log::info('Step 9: full image generated', ['full_path' => $fullPath]);
        } catch (\Exception $e) {
            \Log::error('Step 9 failed: ' . $e->getMessage());
            throw $e;
        }

        // Create photo record
        try {
            $photo = Photo::create([
                'id' => $photoId,
                'album_id' => $album->id,
                'title' => $title ?? pathinfo($originalName, PATHINFO_FILENAME),
                'description' => $description,
                'original_path' => $originalPath,
                'full_path' => $fullPath,
                'thumbnail_path' => $thumbPath,
                'hash' => $hash,
                'file_size' => filesize($filePath),
                'mime_type' => mime_content_type($filePath),
                'sort_order' => $album->photos()->max('sort_order') + 1,
                'exif_data' => json_encode($exifData['raw'] ?? []),
                'camera_make' => $exifData['camera_make'],
                'camera_model' => $exifData['camera_model'],
                'lens_model' => $exifData['lens_model'],
                'focal_length' => $exifData['focal_length'],
                'aperture' => $exifData['aperture'],
                'shutter_speed' => $exifData['shutter_speed'],
                'iso' => $exifData['iso'],
                'captured_at' => $exifData['captured_at'],
                'gps_latitude' => $exifData['gps_latitude'],
                'gps_longitude' => $exifData['gps_longitude'],
            ]);
            \Log::info('Step 10: photo record created', ['photo_id' => $photo->id]);
        } catch (\Exception $e) {
            \Log::error('Step 10 failed: ' . $e->getMessage());
            throw $e;
        }

        try {
            $album->increment('photo_count');
            \Log::info('Step 11: photo count incremented');
        } catch (\Exception $e) {
            \Log::error('Step 11 failed: ' . $e->getMessage());
            throw $e;
        }

        // Set as cover if first photo
        try {
            if ($album->photos()->count() === 1) {
                $this->setAlbumCover($photo, $album);
                \Log::info('Step 12: album cover set');
            }
        } catch (\Exception $e) {
            \Log::error('Step 12 failed: ' . $e->getMessage());
            throw $e;
        }

        \Log::info('Step 13: processUpload completed successfully');

        return $photo;
    }

    /**
     * Store original file
     */
    protected function storeOriginal($filePath, $userId, $albumId, $photoId, $extension): string
    {
        $path = "stagephoto/originals/{$userId}/{$albumId}/{$photoId}_original.{$extension}";
        $fullPath = Storage::disk('public')->path($path);

        $dir = dirname($fullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        copy($filePath, $fullPath);

        return $path;
    }

    /**
     * Generate thumbnail (600x600 square crop)
     */
    protected function generateThumbnail($filePath, $userId, $albumId, $photoId): string
    {
        \Log::info('generateThumbnail: starting', ['filePath' => $filePath]);

        $outputPath = null;
        $encoded = null;

        try {
            $this->processImage($filePath, function($image) use (&$outputPath, $userId, $albumId, $photoId, &$encoded) {
                \Log::info('generateThumbnail: image loaded');

                $width = $image->width();
                $height = $image->height();
                \Log::info('generateThumbnail: dimensions', ['width' => $width, 'height' => $height]);

                $size = min($width, $height);
                $x = ($width - $size) / 2;
                $y = ($height - $size) / 2;

                $image->crop($size, $size, (int)$x, (int)$y);
                \Log::info('generateThumbnail: cropped');

                $image->resize(600, 600);
                \Log::info('generateThumbnail: resized');

                $encoded = $this->encodeToWebp($image, 80);
                \Log::info('generateThumbnail: encoded to WebP', ['length' => strlen($encoded)]);

                $outputPath = "stagephoto/webp/{$userId}/{$albumId}/{$photoId}_thumb.webp";
            });

            $fullPath = Storage::disk('public')->path($outputPath);
            $dir = dirname($fullPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
                \Log::info('generateThumbnail: created directory', ['dir' => $dir]);
            }

            file_put_contents($fullPath, $encoded);
            \Log::info('generateThumbnail: file saved', ['fullPath' => $fullPath]);

            return $outputPath;

        } catch (\Exception $e) {
            \Log::error('generateThumbnail failed: ' . $e->getMessage());
            \Log::error('generateThumbnail trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate full-size image (1600px max side)
     */
    protected function generateFullImage($filePath, $userId, $albumId, $photoId): string
    {
        \Log::info('generateFullImage: starting', ['filePath' => $filePath]);

        $outputPath = null;
        $encoded = null;

        $this->processImage($filePath, function($image) use (&$outputPath, $userId, $albumId, $photoId, &$encoded) {
            \Log::info('generateFullImage: image loaded');

            // Resize to max 1600px on longest side
            $image->resize(1600, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            \Log::info('generateFullImage: resized');

            // TODO: Apply watermark - currently disabled due to Intervention v4 compatibility issues
            // $this->applyWatermarkToImage($image);

            // Encode as WebP using v4 encoder
            $encoded = $this->encodeToWebp($image, 85);
            \Log::info('generateFullImage: encoded to WebP', ['length' => strlen($encoded)]);

            $outputPath = "stagephoto/webp/{$userId}/{$albumId}/{$photoId}_full.webp";
        });

        $fullPath = Storage::disk('public')->path($outputPath);
        $dir = dirname($fullPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullPath, $encoded);
        \Log::info('generateFullImage: file saved', ['fullPath' => $fullPath]);

        return $outputPath;
    }

    /**
     * TODO: Implement watermark functionality for Intervention Image v4
     * Currently disabled due to API compatibility issues with v4.0.1
     *
     * Apply StagePhoto watermark to an image object
     */
    protected function applyWatermarkToImage($image): void
    {
        // TODO: Implement watermark for Intervention Image v4.0.1
        // The insert() method signature has changed in v4
        // Need to research correct API for watermark placement
        \Log::info('Watermark skipped - not implemented for v4 yet');

        /*
        $watermarkPath = public_path('images/watermark.png');

        if (!file_exists($watermarkPath)) {
            \Log::info('Watermark file not found, skipping');
            return;
        }

        try {
            $watermark = $this->manager->decodePath($watermarkPath);
            $watermark->resize(150, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            // v4 compatible watermark placement goes here
            // $image->place($watermark, 'bottom-right', 10, 10);

            \Log::info('Watermark applied successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to apply watermark: ' . $e->getMessage());
        }
        */
    }

    /**
     * Set album cover from a photo
     */
    public function setAlbumCover(Photo $photo, Album $album): void
    {
        $squareCover = $this->generateAlbumCoverSquare($photo);
        $heroCover = $this->generateAlbumCoverHero($photo);

        $album->update([
            'cover_image_square' => $squareCover,
            'cover_image_hero' => $heroCover,
            'cover_image' => $squareCover,
        ]);
    }

    /**
     * Generate square album cover (800x800)
     */
    protected function generateAlbumCoverSquare(Photo $photo): string
    {
        $fullPath = Storage::disk('public')->path($photo->full_path);
        $outputPath = null;
        $encoded = null;

        $this->processImage($fullPath, function($image) use ($photo, &$outputPath, &$encoded) {
            // Get dimensions
            $width = $image->width();
            $height = $image->height();

            // Crop to square (center)
            $size = min($width, $height);
            $x = ($width - $size) / 2;
            $y = ($height - $size) / 2;

            $image->crop($size, $size, (int)$x, (int)$y);

            // Resize to 800x800
            $image->resize(800, 800);

            // Encode as WebP using v4 encoder (no watermark)
            $encoded = $this->encodeToWebp($image, 85);

            $outputPath = "stagephoto/albums/{$photo->album->photographer_id}/{$photo->album_id}/cover_square.webp";
        });

        $fullOutputPath = Storage::disk('public')->path($outputPath);
        $dir = dirname($fullOutputPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullOutputPath, $encoded);

        return $outputPath;
    }

    /**
     * Generate hero album cover (2000x800)
     * Note: In Intervention v4, fit() is replaced by cover()
     */
    protected function generateAlbumCoverHero(Photo $photo): string
    {
        $fullPath = Storage::disk('public')->path($photo->full_path);
        $outputPath = null;
        $encoded = null;

        $this->processImage($fullPath, function($image) use ($photo, &$outputPath, &$encoded) {
            // Cover crop to 2000x800 - using cover() instead of fit() for v4
            $image->cover(2000, 800);

            // Encode as WebP using v4 encoder (no watermark)
            $encoded = $this->encodeToWebp($image, 85);

            $outputPath = "stagephoto/albums/{$photo->album->photographer_id}/{$photo->album_id}/cover_hero.webp";
        });

        $fullOutputPath = Storage::disk('public')->path($outputPath);
        $dir = dirname($fullOutputPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fullOutputPath, $encoded);

        return $outputPath;
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
     * Process a ZIP file upload
     */
    public function processZipUpload($zipFile, Album $album): array
    {
        $processed = [];
        $errors = [];
        $skippedCount = 0;

        // Create temp directory
        $tempDir = storage_path("app/temp/" . Str::uuid());
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Extract ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipFile->getRealPath()) === true) {
            $zip->extractTo($tempDir);
            $zip->close();
        } else {
            $this->deleteDirectory($tempDir);
            throw new \Exception('Unable to extract ZIP file. The file may be corrupted.');
        }

        // Find all image files (excluding macOS metadata)
        $imageFiles = $this->findImageFiles($tempDir);

        \Log::info('Found ' . count($imageFiles) . ' valid images in ZIP');

        foreach ($imageFiles as $filePath) {
            try {
                // Check if file is readable and valid
                if (!is_readable($filePath)) {
                    throw new \Exception('File is not readable');
                }

                // Verify it's a valid image
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $filePath);
                finfo_close($finfo);

                $validMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($mimeType, $validMimeTypes)) {
                    throw new \Exception('Invalid image format: ' . $mimeType);
                }

                // Create an UploadedFile instance from the extracted file
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $filePath,
                    basename($filePath),
                    $mimeType,
                    null,
                    true
                );

                // Use filename as title (without extension)
                $title = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

                $photo = $this->processUpload($uploadedFile, $album, $title, null);
                $processed[] = $photo;

            } catch (\Exception $e) {
                $errors[] = [
                    'file' => basename($filePath),
                    'error' => $e->getMessage(),
                ];
                \Log::error('Failed to process file: ' . basename($filePath) . ' - ' . $e->getMessage());
            }
        }

        // Clean up temp directory
        $this->deleteDirectory($tempDir);

        return [
            'processed' => $processed,
            'errors' => $errors,
            'success_count' => count($processed),
            'error_count' => count($errors),
            'skipped_count' => $skippedCount,
        ];
    }

    /**
     * Recursively find all image files in a directory
     */
    private function findImageFiles($directory): array
    {
        $images = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Files and patterns to exclude
        $excludePatterns = [
            '/^\._/',           // macOS resource fork files (._filename)
            '/^\.DS_Store/',    // macOS folder metadata
            '/^\.Spotlight/',   // macOS Spotlight index
            '/^\.Trashes/',     // macOS Trash folder
            '/^\./',            // Any other hidden files
            '/__MACOSX/',       // macOS ZIP metadata folder
        ];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                $relativePath = $file->getPathname();

                // Skip excluded files
                $shouldExclude = false;
                foreach ($excludePatterns as $pattern) {
                    if (preg_match($pattern, $filename) || preg_match($pattern, $relativePath)) {
                        $shouldExclude = true;
                        break;
                    }
                }

                if ($shouldExclude) {
                    \Log::info('Skipping excluded file: ' . $filename);
                    continue;
                }

                $extension = strtolower($file->getExtension());
                if (in_array($extension, $allowedExtensions)) {
                    $images[] = $file->getPathname();
                } else {
                    \Log::info('Skipping non-image file: ' . $filename . ' (extension: ' . $extension . ')');
                }
            }
        }

        return $images;
    }

    /**
     * Recursive directory deletion
     */
    private function deleteDirectory($dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
