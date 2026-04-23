<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntityAlbumSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Linking entities to albums...');

        $entities = Entity::where('is_published', true)->get();
        $albums = Album::where('is_published', true)->get();

        foreach ($entities as $entity) {
            // Link to 2-5 random albums
            $randomAlbums = $albums->random(min(random_int(2, 5), $albums->count()));
            foreach ($randomAlbums as $album) {
                \DB::table('entity_album')->insert([
                    'entity_id' => $entity->id,
                    'album_id' => $album->id,
                    'relationship_type' => $this->getRandomRelationship(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Entity-album links seeded successfully!');
    }

    private function getRandomRelationship(): string
    {
        $relationships = ['featured', 'dedicated', 'guest'];

        return $relationships[array_rand($relationships)];
    }
}
