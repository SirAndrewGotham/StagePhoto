<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity;
use App\Models\EntityContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityContactFactory extends Factory
{
    protected $model = EntityContact::class;

    public function definition(): array
    {
        $contactTypes = ['email', 'phone', 'telegram', 'vkontakte', 'instagram'];
        $contactType = $this->faker->randomElement($contactTypes);
        $visibilities = ['public', 'registered', 'photographers', 'admin'];

        $values = [
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'telegram' => '@'.$this->faker->userName(),
            'vkontakte' => 'vk.com/'.$this->faker->userName(),
            'instagram' => '@'.$this->faker->userName(),
        ];

        return [
            'entity_id' => Entity::factory(),
            'contact_type' => $contactType,
            'value' => $values[$contactType],
            'visibility' => $this->faker->randomElement($visibilities),
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

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'public',
        ]);
    }

    public function forPhotographers(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'photographers',
        ]);
    }
}
