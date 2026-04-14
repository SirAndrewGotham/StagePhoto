<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['slug', 'icon', 'type', 'sort_order', 'is_active'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function getNameAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        return $translation?->name ?? $this->translations->firstWhere('locale', 'ru')?->name ?? $this->slug;
    }

    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();
        $translation = $this->translations->firstWhere('locale', $locale);

        return $translation?->description;
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
