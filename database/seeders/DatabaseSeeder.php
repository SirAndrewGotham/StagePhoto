<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create categories first
        $this->call(CategorySeeder::class);

        // Create tags
        $this->call(TagSeeder::class);

        // Create a test photographer if none exists
        $photographer = User::first();
        if (! $photographer) {
            $photographer = User::create([
                'name' => 'Test Photographer',
                'email' => 'photographer@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Create an unsorted album for the photographer (direct creation, not factory)
        Album::create([
            'title' => 'Unsorted',
            'slug' => 'unsorted-'.$photographer->id,
            'description' => 'Automatically created album for unsorted photos. Move photos to other albums to organize them.',
            'cover_image' => null,
            'cover_image_square' => null,
            'cover_image_hero' => null,
            'photographer_id' => $photographer->id,
            'event_date' => now(),
            'is_published' => false,
            'is_unsorted' => true,
            'badge' => '📁 UNSORTED',
            'badge_gradient' => 'from-gray-500 to-gray-600',
        ]);

        // Create 50 random published albums with photos
        $this->createAlbumsWithPhotos(50, $photographer, 'published');

        // Create 10 featured albums
        $this->createAlbumsWithPhotos(10, $photographer, 'featured');

        // Create 5 NEW albums (fresh)
        $this->createAlbumsWithPhotos(5, $photographer, 'fresh');

        // Create 10 highly rated albums
        $this->createAlbumsWithPhotos(10, $photographer, 'highlyRated');

        // Create 10 popular albums
        $this->createAlbumsWithPhotos(10, $photographer, 'popular');

        // Create specific genre albums
        $genres = ['rock', 'metal', 'jazz', 'classical', 'folk', 'drama', 'ballet', 'opera'];
        foreach ($genres as $genre) {
            $this->createAlbumsForGenre(5, $photographer, $genre);
        }

        // Create 5 unpublished (draft) albums
        $this->createAlbumsWithPhotos(5, $photographer, 'unpublished');

        // Call remaining seeders
        $this->call([
            RoleSeeder::class,
            GenreSeeder::class,
            UserSeeder::class,
            BookingRequestSeeder::class,
            CommentSeeder::class,
            RatingSeeder::class,
        ]);

        $this->command->info('✓ Database seeding completed successfully!');
        $this->command->info('Total albums: '.Album::count());
        $this->command->info('Total photos: '.Photo::count());
    }

    /**
     * Helper method to create albums with photos
     */
    private function createAlbumsWithPhotos(int $count, User $photographer, string $type = 'published'): void
    {
        for ($i = 0; $i < $count; $i++) {
            // Create the album
            $album = Album::factory()
                ->forPhotographer($photographer);

            // Apply type-specific state
            $album = match ($type) {
                'featured' => $album->featured(),
                'fresh' => $album->fresh()->recent(),
                'highlyRated' => $album->highlyRated(),
                'popular' => $album->popular(),
                'unpublished' => $album->unpublished(),
                default => $album->published(),
            };

            $album = $album->create();

            // Attach 1-2 random categories
            $categories = Category::inRandomOrder()->limit(random_int(1, 2))->pluck('id');
            $album->categories()->attach($categories);

            // Create 5-20 photos for the album
            $photoCount = random_int(5, 20);
            for ($j = 0; $j < $photoCount; $j++) {
                Photo::factory()
                    ->forAlbum($album)
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);
            }

            // Update photo count
            $album->update(['photo_count' => $photoCount]);
        }
    }

    /**
     * Helper method to create albums for specific genre
     */
    private function createAlbumsForGenre(int $count, User $photographer, string $genreSlug): void
    {
        $category = Category::where('slug', $genreSlug)->first();

        if (! $category) {
            $this->command->warn("Category '{$genreSlug}' not found, skipping...");

            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $album = Album::factory()
                ->published()
                ->forPhotographer($photographer)
                ->create();

            // Attach the specific genre category
            $album->categories()->attach($category->id);

            // Create 5-15 photos
            $photoCount = random_int(5, 15);
            for ($j = 0; $j < $photoCount; $j++) {
                Photo::factory()
                    ->forAlbum($album)
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);
            }

            $album->update(['photo_count' => $photoCount]);
        }
    }
}
