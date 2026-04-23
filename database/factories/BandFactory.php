<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Band;
use Illuminate\Database\Eloquent\Factories\Factory;

class BandFactory extends Factory
{
    protected $model = Band::class;

    public function definition(): array
    {
        $genres = ['Rock', 'Metal', 'Jazz', 'Classical', 'Folk', 'Electronic', 'Pop', 'Punk', 'Blues'];

        return [
            'genre' => $this->faker->randomElement($genres),
            'formed_year' => $this->faker->year(),
            'record_label' => $this->faker->optional(0.6)->company(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
