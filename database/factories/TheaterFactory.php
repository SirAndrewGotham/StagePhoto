<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;

class TheaterFactory extends Factory
{
    protected $model = Theater::class;

    public function definition(): array
    {
        return [
            'capacity' => $this->faker->numberBetween(100, 2000),
            'founded_year' => $this->faker->year(),
            'artistic_director' => $this->faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
