<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photographers = User::whereHas('roles', fn ($q) => $q->where('slug', 'photographer'))->get();
        $genres = Genre::all();

        Album::factory(30)->create()->each(function ($album) use ($photographers, $genres) {
            $album->photographer()->associate($photographers->random());
            $album->save();
            $album->genres()->attach($genres->random(2)->pluck('id'));
        });
    }
}
