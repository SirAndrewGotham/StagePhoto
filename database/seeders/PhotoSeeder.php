<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Album::all()->each(function ($album) {
            Photo::factory(random_int(12, 45))->create(['album_id' => $album->id]);
        });
    }
}
