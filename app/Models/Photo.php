<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['id', 'album_id', 'title', 'description', 'original_path', 'full_path',
    'thumbnail_path', 'hash', 'file_size', 'mime_type', 'sort_order',
    'is_featured', 'views', 'exif_data', 'camera_make', 'camera_model',
    'lens_model', 'focal_length', 'aperture', 'shutter_speed', 'iso',
    'captured_at', 'gps_latitude', 'gps_longitude'])]
class Photo extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    /** @use HasFactory<PhotoFactory> */
    use HasFactory, SoftDeletes;

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views' => 'integer',
            'sort_order' => 'integer',
            'file_size' => 'integer',
            'captured_at' => 'datetime',
            'exif_data' => 'array',
        ];
    }

    protected $dates = ['deleted_at'];

    // Relationship to Album
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    // Categories (genres) for this photo
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'photo_category');
    }

    // Tags (polymorphic)
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // Comments (polymorphic)
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Approved comments only
    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('is_approved', true);
    }

    // Increment view count
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getFormattedTagsAttribute(): string
    {
        return $this->tags->pluck('name')->implode(', ');
    }

    // Get URL for the photo
    public function getUrlAttribute(): string
    {
        return route('photo.show', $this);
    }

    // Get thumbnail URL
    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path ?? $this->path;
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function userRating()
    {
        return $this->ratings()->where('user_id', auth()->id());
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('rating') ?: 0, 1);
    }

    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function getUserRatingAttribute(): ?int
    {
        if (! auth()->check()) {
            return null;
        }

        return $this->ratings()->where('user_id', auth()->id())->value('rating');
    }

    public function rate($userId, $rating): void
    {
        $this->ratings()->updateOrCreate(
            ['user_id' => $userId],
            ['rating' => $rating]
        );
    }

    /**
     * Get the album that owns the photo (including soft deleted albums)
     */
    public function albumWithTrashed(): BelongsTo
    {
        return $this->belongsTo(Album::class, 'album_id')->withTrashed();
    }
}
