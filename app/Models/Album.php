<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AlbumFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    // Accessor to get first category slug for filtering
    public function getGenreAttribute()
    {
        return $this->categories->first()?->slug ?? 'other';
    }
}
