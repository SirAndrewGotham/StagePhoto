<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Photo;
use Illuminate\Database\Seeder;

class PhotoCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Get all photos and attach random categories
        $photos = Photo::all();
        $categories = Category::all();

        foreach ($photos as $photo) {
            // Attach 1-2 random categories to each photo
            $randomCategories = $categories->random(random_int(1, 2))->pluck('id');
            $photo->categories()->attach($randomCategories);
        }

        $this->command->info('Photo categories seeded successfully!');
    }
}
