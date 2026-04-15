<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PhotoFactory extends Factory
{
    protected $model = Photo::class;

    public function definition(): array
    {
        $photoUrls = [
            'https://images.unsplash.com/photo-1501612780327-45045538702b',
            'https://images.unsplash.com/photo-1514525253161-7a46d19cd819',
            'https://images.unsplash.com/photo-1459749411177-0473ef716170',
            'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
            'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad',
            'https://images.unsplash.com/photo-1514320291840-2e0a9bf2f4ae',
        ];

        $url = $this->faker->randomElement($photoUrls);
        $photoId = (string) Str::uuid();
        $filename = $this->faker->word().'.jpg';

        return [
            'id' => $photoId,
            'album_id' => Album::factory(),
            'filename' => $filename,  // Add this
            'path' => $url.'?auto=format&fit=crop&w=1200&q=80',  // Add this
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional(0.7)->paragraph(),
            'original_path' => "stagephoto/originals/{$photoId}_original.jpg",
            'full_path' => "stagephoto/webp/{$photoId}_full.webp",
            'thumbnail_path' => "stagephoto/webp/{$photoId}_thumb.webp",
            'hash' => md5($url.$this->faker->randomNumber()),
            'file_size' => $this->faker->numberBetween(500000, 5000000),
            'mime_type' => 'image/jpeg',
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_featured' => $this->faker->boolean(10),
            'views' => $this->faker->numberBetween(0, 10000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
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
            'is_featured' => true,
        ]);
    }

    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    public function withDescription(?string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }
}
