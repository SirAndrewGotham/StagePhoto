<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Status;
use App\Models\User;
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
        $photographer = User::firstOrCreate(
            ['email' => 'photographer@stagephoto.test'],
            [
                'name' => 'Test Photographer',
                'password' => bcrypt('password'),
            ]
        );

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@stagephoto.test'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
            ]
        );

        // Create unsorted album
        $unsortedAlbum = Album::create([
            'title' => 'Unsorted',
            'slug' => 'unsorted-'.$photographer->id,
            'description' => 'Automatically created album for unsorted photos.',
            'photographer_id' => $photographer->id,
            'event_date' => now(),
            'is_published' => false,
            'is_unsorted' => true,
            'status' => 'pending',
            'badge' => '📁 UNSORTED',
            'badge_gradient' => 'from-gray-500 to-gray-600',
        ]);

        // Create status history for unsorted album
        $this->createStatusHistory($unsortedAlbum, 'pending', $photographer, $admin);

        // Create albums with different statuses
        $this->createAlbumsWithPhotos(10, $photographer, $admin, 'published');
        $this->createAlbumsWithPhotos(5, $photographer, $admin, 'pending');
        $this->createAlbumsWithPhotos(3, $photographer, $admin, 'approved');
        $this->createAlbumsWithPhotos(2, $photographer, $admin, 'rejected');

        // Create featured published albums
        $this->createAlbumsWithPhotos(5, $photographer, $admin, 'published', true);

        // Create specific genre albums
        $genres = ['rock', 'metal', 'jazz', 'classical', 'folk', 'drama', 'ballet', 'opera'];
        foreach ($genres as $genre) {
            $this->createAlbumsForGenre(3, $photographer, $admin, $genre);
        }

        $this->command->info('✓ Database seeding completed successfully!');
        $this->command->info('Total albums: '.Album::count());
        $this->command->info('Total photos: '.Photo::count());
        $this->command->info('Total status records: '.Status::count());
    }

    private function createAlbumsWithPhotos(int $count, User $photographer, User $admin, string $status, bool $featured = false): void
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
            $categories = Category::inRandomOrder()->limit(random_int(1, 2))->pluck('id');
            $album->categories()->attach($categories);

            // Create status history for album
            $this->createStatusHistory($album, $status, $photographer, $admin);

            // Create photos
            $photoCount = random_int(5, 20);
            for ($j = 0; $j < $photoCount; $j++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->$status()
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);

                // Create status history for photo
                $this->createStatusHistory($photo, $status, $photographer, $admin);
            }

            $album->update(['photo_count' => $photoCount]);
        }
    }

    private function createAlbumsForGenre(int $count, User $photographer, User $admin, string $genreSlug): void
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

            $album->categories()->attach($category->id);

            // Create status history for album
            $this->createStatusHistory($album, 'published', $photographer, $admin);

            $photoCount = random_int(5, 15);
            for ($j = 0; $j < $photoCount; $j++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->published()
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);

                // Create status history for photo
                $this->createStatusHistory($photo, 'published', $photographer, $admin);
            }

            $album->update(['photo_count' => $photoCount]);
        }
    }

    private function createStatusHistory($model, string $finalStatus, User $photographer, User $admin): void
    {
        $statuses = $this->getStatusFlow($finalStatus);
        $currentDate = now();

        foreach ($statuses as $index => $statusData) {
            Status::create([
                'statusable_id' => $model->id,
                'statusable_type' => $model::class,
                'status' => $statusData['status'],
                'comment' => $statusData['comment'],
                'changed_by' => $statusData['changed_by'] === 'admin' ? $admin->id : $photographer->id,
                'created_at' => $currentDate->subDays(count($statuses) - $index),
            ]);
        }
    }

    private function getStatusFlow(string $finalStatus): array
    {
        $flows = [
            'pending' => [
                ['status' => 'pending', 'comment' => 'Initial submission awaiting review', 'changed_by' => 'photographer'],
            ],
            'approved' => [
                ['status' => 'pending', 'comment' => 'Initial submission awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Approved by administrator', 'changed_by' => 'admin'],
            ],
            'published' => [
                ['status' => 'pending', 'comment' => 'Initial submission awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Approved by administrator', 'changed_by' => 'admin'],
                ['status' => 'published', 'comment' => 'Published to public', 'changed_by' => 'admin'],
            ],
            'rejected' => [
                ['status' => 'pending', 'comment' => 'Initial submission awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'rejected', 'comment' => 'Does not meet quality standards', 'changed_by' => 'admin'],
            ],
            'blocked' => [
                ['status' => 'pending', 'comment' => 'Initial submission awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Approved by administrator', 'changed_by' => 'admin'],
                ['status' => 'published', 'comment' => 'Published to public', 'changed_by' => 'admin'],
                ['status' => 'blocked', 'comment' => 'Blocked due to policy violation', 'changed_by' => 'admin'],
            ],
        ];

        return $flows[$finalStatus] ?? $flows['pending'];
    }
}
