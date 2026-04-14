<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $names = [
            'portrait', 'landscape', 'black-and-white', 'colorful', 'dramatic',
            'intimate', 'crowd', 'backstage', 'close-up', 'wide-angle',
            'long-exposure', 'silhouette', 'smoke', 'lasers', 'acoustic',
        ];

        $colors = ['gray', 'red', 'blue', 'green', 'yellow', 'purple', 'pink', 'indigo'];

        $name = $this->faker->randomElement($names);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => $this->faker->randomElement($colors),
            'usage_count' => $this->faker->numberBetween(0, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
