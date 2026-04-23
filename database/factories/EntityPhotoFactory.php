<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity;
use App\Models\EntityPhoto;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityPhotoFactory extends Factory
{
    protected $model = EntityPhoto::class;

    public function definition(): array
    {
        return [
            'entity_id' => Entity::factory(),
            'photo_id' => Photo::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forEntity(Entity $entity): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_id' => $entity->id,
        ]);
    }

    public function forPhoto(Photo $photo): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_id' => $photo->id,
        ]);
    }
}
