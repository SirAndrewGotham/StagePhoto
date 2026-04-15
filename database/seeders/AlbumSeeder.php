<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create test photographer
        $photographer = User::first();
        if (! $photographer) {
            $photographer = User::create([
                'name' => 'Test Photographer',
                'email' => 'photographer@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Create "Unsorted" album for the photographer
        Album::create([
            'title' => 'Unsorted',
            'slug' => 'unsorted-'.$photographer->id,
            'description' => 'Automatically created album for unsorted photos. Move photos to other albums to organize them.',
            'cover_image' => null,
            'cover_image_square' => null,
            'cover_image_hero' => null,
            'photographer_id' => $photographer->id,
            'venue' => null,
            'event_date' => now(),
            'photo_count' => 0,
            'rating' => 0,
            'views' => 0,
            'is_published' => false,
            'is_unsorted' => true,
            'badge' => '📁 UNSORTED',
            'badge_gradient' => 'from-gray-500 to-gray-600',
        ]);

        // Create regular published albums
        $albumsData = [
            [
                'title' => 'Arctic Monkeys • Live at Luzhniki',
                'slug' => 'arctic-monkeys-live-luzhniki',
                'description' => 'Full concert coverage of Arctic Monkeys at Luzhniki Stadium',
                'venue' => 'Luzhniki Stadium, Moscow',
                'event_date' => '2025-10-15',
                'photo_count' => 5,
                'rating' => 4.9,
                'views' => 1234,
                'badge' => '✨ NEW',
                'badge_gradient' => 'from-pink-500 to-orange-500',
                'category_slug' => 'rock',
            ],
            [
                'title' => 'Swan Lake • Bolshoi Theater Premiere',
                'slug' => 'swan-lake-bolshoi-premiere',
                'description' => 'Opening night of Swan Lake at the historic Bolshoi Theater',
                'venue' => 'Bolshoi Theater, Moscow',
                'event_date' => '2025-11-02',
                'photo_count' => 5,
                'rating' => 5.0,
                'views' => 3456,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'ballet',
            ],
            [
                'title' => 'Park Live Festival 2025 • Day 2',
                'slug' => 'park-live-festival-2025-day2',
                'description' => 'Highlights from the second day of Park Live Festival',
                'venue' => 'Park Live, Moscow',
                'event_date' => '2025-08-20',
                'photo_count' => 5,
                'rating' => 4.8,
                'views' => 5678,
                'badge' => '🔥 FEATURED',
                'badge_gradient' => 'from-indigo-600 to-purple-600',
                'category_slug' => 'festivals',
            ],
            [
                'title' => 'Igor Butman Quartet • Intimate Session',
                'slug' => 'igor-butman-jazz-session',
                'description' => 'Exclusive jazz session with Igor Butman Quartet',
                'venue' => 'Jazz Club, Moscow',
                'event_date' => '2025-09-05',
                'photo_count' => 5,
                'rating' => 4.7,
                'views' => 890,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'jazz',
            ],
            [
                'title' => 'Molchat Doma • 16 Tons Club',
                'slug' => 'molchat-doma-16-tons-club',
                'description' => 'Post-punk night with Molchat Doma',
                'venue' => '16 Tons Club, Moscow',
                'event_date' => '2025-11-18',
                'photo_count' => 5,
                'rating' => 5.0,
                'views' => 4567,
                'badge' => '👤 YOUR WORK',
                'badge_gradient' => 'from-emerald-500 to-teal-500',
                'category_slug' => 'rock',
            ],
        ];

        $coverImages = [
            'https://images.unsplash.com/photo-1501612780327-45045538702b',
            'https://images.unsplash.com/photo-1514525253161-7a46d19cd819',
            'https://images.unsplash.com/photo-1459749411177-0473ef716170',
            'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
            'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad',
        ];

        foreach ($albumsData as $index => $albumData) {
            $categorySlug = $albumData['category_slug'];
            unset($albumData['category_slug']);

            $coverUrl = $coverImages[$index % count($coverImages)];

            $albumData['cover_image'] = $coverUrl.'?auto=format&fit=crop&w=800&h=800&q=80';
            $albumData['cover_image_square'] = $coverUrl.'?auto=format&fit=crop&w=800&h=800&q=80';
            $albumData['cover_image_hero'] = $coverUrl.'?auto=format&fit=crop&w=2000&h=800&q=80';
            $albumData['photographer_id'] = $photographer->id;

            $album = Album::create($albumData);

            // Attach category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $album->categories()->attach($category->id);
            }

            // Create photos for the album
            $this->createPhotosForAlbum($album, $albumData['photo_count']);
        }

        $this->command->info('Albums seeded successfully!');
        $this->command->info('Unsorted album created for photographer ID: '.$photographer->id);
    }

    private function createPhotosForAlbum(Album $album, int $count): void
    {
        $photoUrls = [
            'https://images.unsplash.com/photo-1501612780327-45045538702b',
            'https://images.unsplash.com/photo-1514525253161-7a46d19cd819',
            'https://images.unsplash.com/photo-1459749411177-0473ef716170',
            'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4',
            'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad',
        ];

        $descriptions = [
            'A stunning moment captured during the performance.',
            'The energy on stage was electric!',
            'Beautiful lighting and composition.',
            'An intimate moment between the artist and the audience.',
            'Perfect timing capturing this dramatic moment.',
        ];

        for ($i = 0; $i < $count; $i++) {
            $photoId = (string) Str::uuid();
            $baseUrl = $photoUrls[$i % count($photoUrls)];

            Photo::create([
                'id' => $photoId,
                'album_id' => $album->id,
                'title' => $album->title.' - Photo '.($i + 1),
                'description' => $descriptions[$i % count($descriptions)],
                'original_path' => "stagephoto/originals/{$album->photographer_id}/{$album->id}/{$photoId}_original.jpg",
                'full_path' => "stagephoto/webp/{$album->photographer_id}/{$album->id}/{$photoId}_full.webp",
                'thumbnail_path' => "stagephoto/webp/{$album->photographer_id}/{$album->id}/{$photoId}_thumb.webp",
                'hash' => md5($baseUrl.$photoId),
                'file_size' => random_int(500000, 5000000),
                'mime_type' => 'image/jpeg',
                'sort_order' => $i,
                'is_featured' => $i === 0,
                'views' => random_int(0, 5000),
            ]);
        }
    }

    /**
     * Attach random categories to the album after creation.
     */
    public function withRandomCategories(int $count = 1): static
    {
        return $this->afterCreating(function (Album $album) use ($count) {
            $categories = Category::inRandomOrder()->limit($count)->pluck('id');
            $album->categories()->attach($categories);
        });
    }

    /**
     * Create photos for the album after creation.
     */
    public function withPhotos(int $count = 5): static
    {
        return $this->afterCreating(function (Album $album) use ($count) {
            Photo::factory()
                ->count($count)
                ->forAlbum($album)
                ->create();
        });
    }

    /**
     * Create an album for a specific genre.
     */
    public function forGenre(string $genreSlug): static
    {
        return $this->afterCreating(function (Album $album) use ($genreSlug) {
            $category = Category::where('slug', $genreSlug)->first();
            if ($category) {
                $album->categories()->attach($category->id);
            }
        });
    }

    /**
     * Create an album with a NEW badge.
     */
    public function fresh(): static
    {
        return $this->withBadge('✨ NEW', 'from-pink-500 to-orange-500');
    }
}
