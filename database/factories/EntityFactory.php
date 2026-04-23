<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    protected $model = Entity::class;

    public function definition(): array
    {
        $types = ['theater', 'band', 'individual'];
        $type = $this->faker->randomElement($types);

        return [
            'entityable_id' => null, // Will be set after creating the specific model
            'entityable_type' => $this->getMorphClass($type),
            'slug' => $this->faker->slug(),
            'type' => $type,
            'is_published' => $this->faker->boolean(80),
            'settings' => json_encode([
                'show_email' => $this->faker->boolean(),
                'show_phone' => $this->faker->boolean(),
                'allow_messages' => $this->faker->boolean(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }

    private function getMorphClass(string $type): string
    {
        return match ($type) {
            'theater' => \App\Models\Theater::class,
            'band' => \App\Models\Band::class,
            'individual' => \App\Models\Individual::class,
            default => Entity::class,
        };
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
            'entityable_type' => $this->getMorphClass($type),
        ]);
    }

    #[\Override]
    public function configure()
    {
        return $this->afterMaking(function (Entity $entity) {
            // Create the specific model based on type
            $specificModel = match ($entity->type) {
                'theater' => Theater::factory()->make(),
                'band' => Band::factory()->make(),
                'individual' => Individual::factory()->make(),
                default => null,
            };

            if ($specificModel) {
                $specificModel->save();
                $entity->entityable_id = $specificModel->id;
                $entity->entityable_type = $specificModel::class;
            }
        });
    }
}
