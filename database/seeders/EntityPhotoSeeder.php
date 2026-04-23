<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class EntityPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Tagging photos with entities...');

        $entities = Entity::where('is_published', true)->get();
        $photos = Photo::where('status', 'published')->get();

        foreach ($entities as $entity) {
            // Tag 5-15 random photos
            $randomPhotos = $photos->random(min(random_int(5, 15), $photos->count()));
            foreach ($randomPhotos as $photo) {
                \DB::table('entity_photos')->insert([
                    'entity_id' => $entity->id,
                    'photo_id' => $photo->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Entity-photo tags seeded successfully!');
    }
}
