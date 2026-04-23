<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Entity;
use App\Models\EntityAlbum;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityAlbumFactory extends Factory
{
    protected $model = EntityAlbum::class;

    public function definition(): array
    {
        $relationshipTypes = ['featured', 'dedicated', 'guest'];

        return [
            'entity_id' => Entity::factory(),
            'album_id' => Album::factory(),
            'relationship_type' => $this->faker->randomElement($relationshipTypes),
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

    public function forAlbum(Album $album): static
    {
        return $this->state(fn (array $attributes) => [
            'album_id' => $album->id,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_type' => 'featured',
        ]);
    }

    public function dedicated(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_type' => 'dedicated',
        ]);
    }
}
