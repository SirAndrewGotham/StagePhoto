<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Status;
use App\Models\User;
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
            'https://images.unsplash.com/photo-1470225620780-dba8ba36b745',
            'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4',
            'https://images.unsplash.com/photo-1516450360452-93659f5a3f21',
            'https://images.unsplash.com/photo-1504609773096-104ff2c73ba4',
        ];

        $cameraMakes = ['Canon', 'Nikon', 'Sony', 'Fujifilm', 'Panasonic', 'Olympus'];
        $cameraModels = [
            'Canon' => ['EOS R5', 'EOS R6', '5D Mark IV', 'EOS R3'],
            'Nikon' => ['Z9', 'Z8', 'D850', 'Z7 II'],
            'Sony' => ['A1', 'A7 III', 'A7R V', 'A9 III'],
            'Fujifilm' => ['X-T5', 'X-H2S', 'GFX 100 II', 'X100V'],
            'Panasonic' => ['S5 II', 'GH6', 'G9 II'],
            'Olympus' => ['OM-1', 'E-M1 Mark III'],
        ];

        $lensModels = [
            'Canon' => ['RF 24-70mm f/2.8L', 'RF 70-200mm f/2.8L', 'RF 50mm f/1.2L', 'RF 85mm f/1.2L'],
            'Nikon' => ['Z 24-70mm f/2.8 S', 'Z 70-200mm f/2.8 VR S', 'Z 50mm f/1.2 S', 'Z 85mm f/1.2 S'],
            'Sony' => ['FE 24-70mm f/2.8 GM II', 'FE 70-200mm f/2.8 GM OSS II', 'FE 50mm f/1.2 GM', 'FE 85mm f/1.4 GM'],
            'Fujifilm' => ['XF 16-55mm f/2.8 R LM WR', 'XF 50-140mm f/2.8 R LM OIS WR', 'XF 56mm f/1.2 R', 'XF 90mm f/2 R LM WR'],
            'Panasonic' => ['LUMIX S 24-70mm f/2.8', 'LUMIX S 70-200mm f/2.8', 'LUMIX S 50mm f/1.4', 'LUMIX S 85mm f/1.8'],
            'Olympus' => ['M.Zuiko 12-40mm f/2.8 PRO', 'M.Zuiko 40-150mm f/2.8 PRO', 'M.Zuiko 45mm f/1.2 PRO', 'M.Zuiko 75mm f/1.8'],
        ];

        $make = $this->faker->randomElement($cameraMakes);
        $model = $this->faker->randomElement($cameraModels[$make]);
        $lens = $this->faker->randomElement($lensModels[$make]);

        $url = $this->faker->randomElement($photoUrls);
        $photoId = (string) Str::uuid();

        // Simulate EXIF data
        $exifData = [
            'IFD0' => [
                'Make' => $make,
                'Model' => $model,
            ],
            'EXIF' => [
                'LensModel' => $lens,
                'FocalLength' => $this->faker->randomElement([24, 35, 50, 85, 105, 135, 200]),
                'FNumber' => $this->faker->randomFloat(1, 1.2, 5.6),
                'ExposureTime' => $this->faker->randomElement(['1/125', '1/250', '1/500', '1/1000', '1/2000']),
                'ISOSpeedRatings' => $this->faker->randomElement([100, 200, 400, 800, 1600, 3200, 6400]),
                'DateTimeOriginal' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y:m:d H:i:s'),
            ],
        ];

        $statuses = ['pending', 'approved', 'published', 'rejected', 'blocked'];
        $status = $this->faker->randomElement($statuses);

        return [
            'id' => $photoId,
            'album_id' => Album::factory(),
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
            'status' => $status,
            // EXIF data
            'exif_data' => json_encode($exifData),
            'camera_make' => $make,
            'camera_model' => $model,
            'lens_model' => $lens,
            'focal_length' => $exifData['EXIF']['FocalLength'].'mm',
            'aperture' => 'f/'.$exifData['EXIF']['FNumber'],
            'shutter_speed' => $exifData['EXIF']['ExposureTime'],
            'iso' => 'ISO '.$exifData['EXIF']['ISOSpeedRatings'],
            'captured_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'gps_latitude' => $this->faker->optional(0.3)->latitude(),
            'gps_longitude' => $this->faker->optional(0.3)->longitude(),
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

    /**
     * Indicate that the photo is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the photo is pending review.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the photo is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the photo is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    /**
     * Indicate that the photo is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    /**
     * Attach categories to the photo after creation
     */
    public function withCategories($categories): static
    {
        return $this->afterCreating(function (Photo $photo) use ($categories) {
            if (is_array($categories)) {
                $photo->categories()->attach($categories);
            } elseif ($categories instanceof Category) {
                $photo->categories()->attach($categories->id);
            } elseif (is_string($categories)) {
                $category = Category::where('slug', $categories)->first();
                if ($category) {
                    $photo->categories()->attach($category->id);
                }
            }
        });
    }

    /**
     * Attach random categories to the photo
     */
    public function withRandomCategories(int $count = 1): static
    {
        return $this->afterCreating(function (Photo $photo) use ($count) {
            $categories = Category::inRandomOrder()->limit($count)->pluck('id');
            $photo->categories()->attach($categories);
        });
    }

    /**
     * Create status history for the photo.
     */
    public function withStatusHistory(?array $statuses = null): static
    {
        return $this->afterCreating(function (Photo $photo) use ($statuses) {
            $defaultStatuses = $statuses ?? ['pending', 'approved', 'published'];
            $user = User::first();

            foreach ($defaultStatuses as $index => $status) {
                Status::create([
                    'statusable_id' => $photo->id,
                    'statusable_type' => Photo::class,
                    'status' => $status,
                    'comment' => $this->getStatusComment($status),
                    'changed_by' => $user?->id ?? 1,
                    'created_at' => now()->subDays(count($defaultStatuses) - $index),
                ]);
            }

            $photo->update(['status' => end($defaultStatuses)]);
        });
    }

    /**
     * Get comment for status change.
     */
    private function getStatusComment(string $status): string
    {
        return match ($status) {
            'pending' => 'Initial upload awaiting review',
            'approved' => 'Approved by administrator',
            'published' => 'Published to public',
            'rejected' => 'Does not meet quality standards',
            'blocked' => 'Blocked due to policy violation',
            default => 'Status updated',
        };
    }
}
