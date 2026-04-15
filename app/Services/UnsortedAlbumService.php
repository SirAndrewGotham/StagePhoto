<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Album;
use App\Models\User;

class UnsortedAlbumService
{
    /**
     * Get or create the unsorted album for a photographer
     */
    public function getOrCreateUnsortedAlbum(User $photographer): Album
    {
        $unsorted = Album::where('photographer_id', $photographer->id)
            ->where('is_unsorted', true)
            ->first();

        if (! $unsorted) {
            return Album::create([
                'title' => 'Unsorted',
                'slug' => 'unsorted-'.$photographer->id,
                'description' => 'Automatically created album for unsorted photos. Move photos to other albums to organize them.',
                'photographer_id' => $photographer->id,
                'event_date' => now(),
                'is_published' => false,
                'is_unsorted' => true,
                'badge' => '📁 UNSORTED',
                'badge_gradient' => 'from-gray-500 to-gray-600',
            ]);
        }

        return $unsorted;
    }

    /**
     * Move photo from unsorted to a target album
     */
    public function moveToAlbum($photoId, Album $targetAlbum): bool
    {
        $photo = Photo::find($photoId);

        if (! $photo || ! $photo->album->is_unsorted) {
            return false;
        }

        $photo->album_id = $targetAlbum->id;
        $photo->save();

        // Update photo counts
        $photo->album->decrement('photo_count');
        $targetAlbum->increment('photo_count');

        return true;
    }

    /**
     * Move multiple photos from unsorted to a target album
     */
    public function moveMultipleToAlbum(array $photoIds, Album $targetAlbum): array
    {
        $moved = 0;
        $failed = 0;

        foreach ($photoIds as $photoId) {
            if ($this->moveToAlbum($photoId, $targetAlbum)) {
                $moved++;
            } else {
                $failed++;
            }
        }

        return [
            'moved' => $moved,
            'failed' => $failed,
        ];
    }
}
