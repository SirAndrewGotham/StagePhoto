<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Individual;
use Illuminate\Database\Eloquent\Factories\Factory;

class IndividualFactory extends Factory
{
    protected $model = Individual::class;

    public function definition(): array
    {
        $birthDate = $this->faker->optional(0.7)->dateTimeBetween('-80 years', '-18 years');

        return [
            'birth_date' => $birthDate,
            'birth_place' => $this->faker->optional(0.5)->city(),
            'death_date' => $this->faker->optional(0.1)->dateTimeBetween('-10 years', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
