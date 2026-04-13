<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['album_id', 'original_path', 'optimized_path', 'thumbnail_path', 'caption', 'sort_order'])]
class Photo extends Model
{
    /** @use HasFactory<PhotoFactory> */
    use HasFactory;

    #[\Override]
    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
