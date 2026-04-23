<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\EntityFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

// #[Fillable([])]
class Entity extends Model
{
    /** @use HasFactory<EntityFactory> */
    use HasFactory;

    protected $casts = [
        'settings' => 'array',
        'is_published' => 'boolean',
    ];

    public function entityable(): MorphTo
    {
        return $this->morphTo();
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(EntityProfile::class);
    }

    public function profile($locale = null)
    {
        $locale ??= app()->getLocale();

        return $this->profiles()->where('locale', $locale)->first()
            ?? $this->profiles()->where('locale', 'ru')->first();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(EntityContact::class);
    }

    /**
     * Get contacts visible to the current user
     */
    public function visibleContacts($user = null)
    {
        return $this->contacts()->where(function ($query) use ($user) {
            if ($user && $user->albums()->exists()) {  // Check if user has albums instead of isPhotographer()
                $query->whereIn('visibility', ['public', 'registered', 'photographers']);
            } elseif ($user) {
                $query->whereIn('visibility', ['public', 'registered']);
            } else {
                $query->where('visibility', 'public');
            }
        })->get();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(EntityMembership::class, 'entity_id');
    }

    public function parentEntities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_memberships', 'entity_id', 'parent_entity_id');
    }

    public function memberEntities(): BelongsToMany
    {
        return $this->belongsToMany(Entity::class, 'entity_memberships', 'parent_entity_id', 'entity_id');
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'entity_album');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'entity_photos');
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function hasContactsVisibleTo($user = null)
    {
        if ($user) {
            return $this->contacts()
                ->whereIn('visibility', ['public', 'registered', 'photographers'])
                ->exists();
        }

        return $this->contacts()
            ->where('visibility', 'public')
            ->exists();
    }
}
