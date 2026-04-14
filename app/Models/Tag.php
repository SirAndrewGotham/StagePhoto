<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'color', 'usage_count'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    #[\Override]
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    public function photos(): MorphToMany
    {
        return $this->morphedByMany(Photo::class, 'taggable');
    }

    public function albums(): MorphToMany
    {
        return $this->morphedByMany(Album::class, 'taggable');
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsage(): void
    {
        $this->decrement('usage_count');
    }

    public function getColorClassAttribute(): string
    {
        $colors = [
            'gray' => 'bg-gray-100 text-gray-800',
            'red' => 'bg-red-100 text-red-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'green' => 'bg-green-100 text-green-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
        ];

        return $colors[$this->color] ?? $colors['gray'];
    }
}
