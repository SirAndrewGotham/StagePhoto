<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    */

    'driver' => 'gd', // or 'imagick' if available

    'quality' => [
        'full' => 85,
        'thumbnail' => 80,
        'cover' => 85,
    ],

    'dimensions' => [
        'album_cover_square' => [800, 800],
        'album_cover_hero' => [2000, 800],
        'photo_thumbnail' => [600, 600],
        'photo_full' => [1600, null], // null = auto-calculate
    ],

    'watermark' => [
        'enabled' => true,
        'path' => 'images/watermark.png', // relative to public_path()
        'position' => 'bottom-right',
        'padding' => 10,
        'width' => 150,
        'opacity' => 30, // percentage
    ],

    'allowed_mime_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/avif',
    ],

    'max_file_size' => 50 * 1024 * 1024, // 50MB

    'zip_max_files' => 100, // Maximum images per ZIP
];
