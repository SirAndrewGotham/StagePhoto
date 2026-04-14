<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AlbumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[Fillable([
    'title', 'slug', 'description', 'cover_image', 'photographer_id',
    'venue', 'event_date', 'photo_count', 'rating', 'views',
    'is_published', 'badge', 'badge_gradient',
])]

class Album extends Model
{
    /** @use HasFactory<AlbumFactory> */
    use HasFactory;

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
        'rating' => 'decimal:1',
        'views' => 'integer',
    ];

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort_order');
    }

    // Featured photo
    public function featuredPhoto(): HasMany
    {
        return $this->hasMany(Photo::class)->where('is_featured', true);
    }

    // Tags relationship (polymorphic)
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    // Comments relationship (polymorphic)
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    // Approved comments
    public function approvedComments(): MorphMany
    {
        return $this->comments()->where('is_approved', true);
    }

    // Helper to get photos count
    public function getPhotosCountAttribute(): int
    {
        return $this->photos()->count();
    }

    // Accessor to get first category slug for filtering
    public function getGenreAttribute()
    {
        return $this->categories->first()?->slug ?? 'other';
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
