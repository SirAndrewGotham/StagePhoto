<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['name' => 'Rock', 'slug' => 'rock', 'icon' => '🎸'],
            ['name' => 'Metal', 'slug' => 'metal', 'icon' => '🤘'],
            ['name' => 'Theater', 'slug' => 'theater', 'icon' => '🎭'],
            ['name' => 'Festival', 'slug' => 'festival', 'icon' => '🎪'],
            ['name' => 'Jazz', 'slug' => 'jazz', 'icon' => '🎷'],
            ['name' => 'Classical', 'slug' => 'classical', 'icon' => '🎻'],
            ['name' => 'Electronic', 'slug' => 'electronic', 'icon' => '🎛️'],
            ['name' => 'Folk', 'slug' => 'folk', 'icon' => '🪕'],
        ];
        foreach ($genres as $g) {
            Genre::create($g);
        }
    }
}
