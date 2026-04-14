<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $albums = Album::where('is_published', true)->get();

        foreach ($albums as $album) {
            $numRatings = random_int(3, 15);
            $ratingUsers = $users->random(min($numRatings, $users->count()));

            foreach ($ratingUsers as $user) {
                Rating::create([
                    'rateable_id' => $album->id,
                    'rateable_type' => Album::class,
                    'user_id' => $user->id,
                    'rating' => random_int(3, 5), // Mostly positive ratings
                ]);
            }
        }
    }
}
