<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryTranslationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id', 'locale', 'name', 'description'])]
class CategoryTranslation extends Model
{
    /** @use HasFactory<CategoryTranslationFactory> */
    use HasFactory;

    public $timestamps = false;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
