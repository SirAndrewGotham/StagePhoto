<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@stagephoto.test')->first();
        $photographer = User::where('email', 'photographer@stagephoto.test')->first();

        Album::all()->each(function (Album $album) use ($admin, $photographer) {
            $photoCount = random_int(12, 45);
            $albumStatus = $album->status;

            for ($i = 0; $i < $photoCount; $i++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->{$albumStatus}()  // Set status matching the album
                    ->create([
                        'sort_order' => $i,
                        'is_featured' => $i === 0,
                    ]);

                // Create status history for the photo
                $this->createStatusHistory($photo, $albumStatus, $photographer, $admin);
            }

            $album->update(['photo_count' => $photoCount]);
        });
    }

    private function createStatusHistory($photo, string $finalStatus, User $photographer, User $admin): void
    {
        $statuses = $this->getStatusFlow($finalStatus);
        $currentDate = now();

        foreach ($statuses as $index => $statusData) {
            Status::create([
                'statusable_id' => $photo->id,
                'statusable_type' => Photo::class,
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
                ['status' => 'pending', 'comment' => 'Photo uploaded, awaiting review', 'changed_by' => 'photographer'],
            ],
            'approved' => [
                ['status' => 'pending', 'comment' => 'Photo uploaded, awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Photo approved by administrator', 'changed_by' => 'admin'],
            ],
            'published' => [
                ['status' => 'pending', 'comment' => 'Photo uploaded, awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Photo approved by administrator', 'changed_by' => 'admin'],
                ['status' => 'published', 'comment' => 'Photo published to public', 'changed_by' => 'admin'],
            ],
            'rejected' => [
                ['status' => 'pending', 'comment' => 'Photo uploaded, awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'rejected', 'comment' => 'Photo rejected: Does not meet quality standards', 'changed_by' => 'admin'],
            ],
            'blocked' => [
                ['status' => 'pending', 'comment' => 'Photo uploaded, awaiting review', 'changed_by' => 'photographer'],
                ['status' => 'approved', 'comment' => 'Photo approved by administrator', 'changed_by' => 'admin'],
                ['status' => 'published', 'comment' => 'Photo published to public', 'changed_by' => 'admin'],
                ['status' => 'blocked', 'comment' => 'Photo blocked due to policy violation', 'changed_by' => 'admin'],
            ],
        ];

        return $flows[$finalStatus] ?? $flows['pending'];
    }
}
