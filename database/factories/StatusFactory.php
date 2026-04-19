<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    protected $model = Status::class;

    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'published', 'rejected', 'blocked'];

        return [
            'status' => $this->faker->randomElement($statuses),
            'comment' => $this->faker->optional(0.5)->sentence(),
            'changed_by' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    public function forAlbum($album): static
    {
        return $this->state(fn (array $attributes) => [
            'statusable_id' => $album->id,
            'statusable_type' => 'App\Models\Album',
        ]);
    }

    public function forPhoto($photo): static
    {
        return $this->state(fn (array $attributes) => [
            'statusable_id' => $photo->id,
            'statusable_type' => 'App\Models\Photo',
        ]);
    }
}
