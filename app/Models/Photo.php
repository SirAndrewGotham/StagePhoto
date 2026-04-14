<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable(['album_id', 'filename', 'path', 'thumbnail_path', 'description',
    'sort_order', 'is_featured', 'views'])]
class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'views' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('is_approved', true);
    }

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
}
