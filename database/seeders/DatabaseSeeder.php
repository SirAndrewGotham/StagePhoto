<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\User;
use App\Models\Status;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories first
        $this->call(CategorySeeder::class);

        // Create tags
        $this->call(TagSeeder::class);

        // Create a test photographer
        $photographer = User::first();
        if (!$photographer) {
            $photographer = User::create([
                'name' => 'Test Photographer',
                'email' => 'photographer@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@stagephoto.test'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );

        // Create unsorted album
        Album::create([
            'title' => 'Unsorted',
            'slug' => 'unsorted-' . $photographer->id,
            'description' => 'Automatically created album for unsorted photos.',
            'photographer_id' => $photographer->id,
            'event_date' => now(),
            'is_published' => false,
            'is_unsorted' => true,
            'status' => 'pending',
            'badge' => '📁 UNSORTED',
            'badge_gradient' => 'from-gray-500 to-gray-600',
        ]);

        // Create albums with different statuses
        $this->createAlbumsWithPhotos(10, $photographer, 'published');
        $this->createAlbumsWithPhotos(5, $photographer, 'pending');
        $this->createAlbumsWithPhotos(3, $photographer, 'approved');
        $this->createAlbumsWithPhotos(2, $photographer, 'rejected');

        // Create featured published albums
        $this->createAlbumsWithPhotos(5, $photographer, 'published', true);

        // Create specific genre albums
        $genres = ['rock', 'metal', 'jazz', 'classical', 'folk', 'drama', 'ballet', 'opera'];
        foreach ($genres as $genre) {
            $this->createAlbumsForGenre(3, $photographer, $genre);
        }

        $this->command->info('✓ Database seeding completed successfully!');
        $this->command->info('Total albums: ' . Album::count());
        $this->command->info('Total photos: ' . Photo::count());
        $this->command->info('Total status records: ' . Status::count());
    }

    private function createAlbumsWithPhotos(int $count, User $photographer, string $status, bool $featured = false): void
    {
        for ($i = 0; $i < $count; $i++) {
            $album = Album::factory()
                ->$status()
                ->forPhotographer($photographer)
                ->create();

            if ($featured) {
                $album->update(['badge' => '🔥 FEATURED', 'badge_gradient' => 'from-indigo-600 to-purple-600']);
            }

            // Attach random categories
            $categories = Category::inRandomOrder()->limit(rand(1, 2))->pluck('id');
            $album->categories()->attach($categories);

            // Create status history
            $this->createStatusHistory($album, $status);

            // Create photos
            $photoCount = rand(5, 20);
            for ($j = 0; $j < $photoCount; $j++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->$status()
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);

                // Create photo status history
                $this->createStatusHistory($photo, $status);
            }

            $album->update(['photo_count' => $photoCount]);
        }
    }

    private function createAlbumsForGenre(int $count, User $photographer, string $genreSlug): void
    {
        $category = Category::where('slug', $genreSlug)->first();

        if (!$category) {
            $this->command->warn("Category '{$genreSlug}' not found, skipping...");
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            $album = Album::factory()
                ->published()
                ->forPhotographer($photographer)
                ->create();

            $album->categories()->attach($category->id);

            $photoCount = rand(5, 15);
            for ($j = 0; $j < $photoCount; $j++) {
                Photo::factory()
                    ->forAlbum($album)
                    ->published()
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);
            }

            $album->update(['photo_count' => $photoCount]);
        }
    }

    private function createStatusHistory($model, string $finalStatus): void
    {
        $admin = User::where('email', 'admin@stagephoto.test')->first();
        $photographer = User::where('email', 'photographer@stagephoto.test')->first();

        // Always start with pending
        Status::create([
            'statusable_id' => $model->id,
            'statusable_type' => get_class($model),
            'status' => 'pending',
            'comment' => 'Initial submission',
            'changed_by' => $photographer->id,
            'created_at' => now()->subDays(5),
        ]);

        if ($finalStatus === 'approved' || $finalStatus === 'published') {
            Status::create([
                'statusable_id' => $model->id,
                'statusable_type' => get_class($model),
                'status' => 'approved',
                'comment' => 'Approved by admin',
                'changed_by' => $admin->id,
                'created_at' => now()->subDays(3),
            ]);
        }

        if ($finalStatus === 'published') {
            Status::create([
                'statusable_id' => $model->id,
                'statusable_type' => get_class($model),
                'status' => 'published',
                'comment' => 'Published to public',
                'changed_by' => $admin->id,
                'created_at' => now()->subDays(1),
            ]);
        }

        if ($finalStatus === 'rejected') {
            Status::create([
                'statusable_id' => $model->id,
                'statusable_type' => get_class($model),
                'status' => 'rejected',
                'comment' => 'Does not meet quality standards. Please review guidelines.',
                'changed_by' => $admin->id,
                'created_at' => now()->subDays(2),
            ]);
        }
    }
}
