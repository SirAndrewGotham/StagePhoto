<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RatingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['rateable_id', 'rateable_type', 'user_id', 'rating'])]
class Rating extends Model
{
    /** @use HasFactory<RatingFactory> */
    use HasFactory;

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rateable(): MorphTo
    {
        return $this->morphTo();
    }
}
