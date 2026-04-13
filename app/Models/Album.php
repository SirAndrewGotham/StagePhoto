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
use Illuminate\Support\Str;

#[Fillable(['photographer_id', 'title', 'slug', 'event_date', 'venue', 'city', 'description', 'is_featured', 'is_published', 'views_count', 'avg_rating'])]
class Album extends Model
{
    /** @use HasFactory<AlbumFactory> */
    use HasFactory;

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[\Override]
    protected function casts(): array
    {
        return ['is_featured' => 'boolean', 'is_published' => 'boolean', 'event_date' => 'date', 'avg_rating' => 'decimal:2', 'views_count' => 'integer'];
    }

    #[\Override]
    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($album) => $album->slug = Str::slug($album->title));
    }

    public function photographer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'photographer_id');
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->orderBy('sort_order');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByGenre($query, string $slug)
    {
        return $query->whereHas('genres', fn ($q) => $q->where('slug', $slug));
    }
}
