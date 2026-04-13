<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'album_id' => Album::factory(),
            'original_path' => 'uploads/original/'.Str::random(10).'.jpg',
            'optimized_path' => 'uploads/optimized/'.Str::random(10).'.webp',
            'thumbnail_path' => 'uploads/thumbnails/'.Str::random(10).'.webp',
            'caption' => $this->faker->sentence(),
            'sort_order' => 0,
        ];
    }
}
