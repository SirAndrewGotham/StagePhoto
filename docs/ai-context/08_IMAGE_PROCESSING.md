# 🖼️ Image Processing Guide

## Quick Reference

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           IMAGE DIMENSIONS                                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ALBUM COVER (GRID)          ALBUM COVER (HERO)                             │
│  ┌──────────────┐            ┌──────────────────────────────────────────┐   │
│  │   800x800    │            │              2000x800                     │   │
│  │   1:1 Square │            │             2.5:1 Landscape               │   │
│  │   WebP, 85%  │            │             WebP, 85%, No WM              │   │
│  │   No WM      │            │             No WM                         │   │
│  └──────────────┘            └──────────────────────────────────────────┘   │
│                                                                              │
│  PHOTO THUMBNAIL             FULL-SIZE PHOTO                                │
│  ┌──────────────┐            ┌──────────────────────────────────────────┐   │
│  │   600x600    │            │        1600px max side                    │   │
│  │   1:1 Square │            │        Original aspect ratio              │   │
│  │   WebP, 80%  │            │        WebP, 85%, With WM                 │   │
│  │   With WM    │            │        With WM                            │   │
│  └──────────────┘            └──────────────────────────────────────────┘   │
│                                                                              │
│  ORIGINAL UPLOAD (Archival)                                                 │
│  ┌──────────────────────────────────────────────────────────────────────┐   │
│  │              User-uploaded original size                              │   │
│  │              Original format (JPEG/PNG preserved)                     │   │
│  │              No watermark, No conversion, No processing               │   │
│  └──────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Processing Workflow

```php
// 1. Upload → Store original
// 2. Generate WebP variants (full, thumbnail)
// 3. Apply watermark to display images
// 4. If album cover, generate square and hero variants
// 5. Update database with paths
// 6. Increment album photo_count
```

## Soft Delete & File Cleanup

```php
// Soft delete (keeps files)
$photo->delete();

// Restore (files remain)
$photo->restore();

// Force delete (removes files)
$imageService->forceDeletePhoto($photo);
```

## Quick Reference by Use Case

| Where | What Image | Dimensions |
|-------|------------|------------|
| Homepage album grid | Album Cover (Grid) | 800x800 |
| Album page header | Album Cover (Hero) | 2000x800 |
| Album page photo grid | Photo Thumbnail | 600x600 |
| Photo modal | Full-size Photo | 1600px max side |
| Related albums sidebar | Album Cover (Grid) | 800x800 (resized to 64x64) |
| Request confirmation email | Full-size Photo (small) | 400px max side |

## File Naming Convention

```
{photo_id}_{type}.webp

Types:
- _full      → Full-size display (1600px)
- _thumb     → Thumbnail (600x600)
- _original  → Original uploaded file (preserved format)

Examples:
- 12345_full.webp
- 12345_thumb.webp
- 12345_original.jpg
```

## Watermark Specifications

- **Position**: Bottom-right corner
- **Padding**: 10px from edges
- **Opacity**: 30%
- **Size**: 150px width (scaled proportionally)
- **Applied to**: Full-size photos and thumbnails
- **NOT applied to**: Original uploads, album covers

## Implementation Checklist

- [ ] Install Intervention Image: `composer require intervention/image`
- [ ] Create image config file: `config/image.php`
- [ ] Create ImageService with methods:
    - [ ] `processUploadedPhoto($file, $albumId)`
    - [ ] `generateAlbumCover($photo, $album)`
    - [ ] `applyWatermark($image)`
    - [ ] `cleanupOldImages($photo)`
- [ ] Create migration for photo variants tracking
- [ ] Add queue job for async image processing
- [ ] Update AlbumObserver to handle cover generation
- [ ] Create Artisan command for reprocessing existing images
