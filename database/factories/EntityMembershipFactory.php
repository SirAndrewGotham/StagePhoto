<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity;
use App\Models\EntityMembership;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityMembershipFactory extends Factory
{
    protected $model = EntityMembership::class;

    public function definition(): array
    {
        $roles = [
            'band' => ['vocalist', 'guitarist', 'bassist', 'drummer', 'keyboardist', 'violinist', 'cellist'],
            'theater' => ['actor', 'director', 'playwright', 'set designer', 'costume designer', 'stage manager'],
        ];

        // Get an individual and a parent entity (band or theater)
        $individual = Entity::where('type', 'individual')->inRandomOrder()->first() ?? Entity::factory()->ofType('individual')->create();
        $parentEntity = Entity::whereIn('type', ['band', 'theater'])->inRandomOrder()->first() ?? Entity::factory()->ofType('band')->create();

        $roleType = $parentEntity->type;
        $role = $this->faker->randomElement($roles[$roleType] ?? $roles['band']);

        return [
            'entity_id' => $individual->id,
            'parent_entity_id' => $parentEntity->id,
            'role' => $role,
            'joined_at' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'left_at' => $this->faker->optional(0.3)->dateTimeBetween('-5 years', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function forIndividual(Entity $individual): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_id' => $individual->id,
        ]);
    }

    public function forParent(Entity $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_entity_id' => $parent->id,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'left_at' => null,
        ]);
    }
}
