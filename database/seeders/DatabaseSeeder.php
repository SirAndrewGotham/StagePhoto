<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Album;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Create 20 categories with all translations for development
        Category::factory()
            ->count(20)
            ->withTranslations()
            ->create();

        // Create a test photographer if none exists
        $photographer = User::first();
        if (! $photographer) {
            $photographer = User::create([
                'name' => 'Test Photographer',
                'email' => 'photographer@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        $this->call(TagSeeder::class);

        // Create 50 random albums using the factory
        Album::factory()
            ->count(50)
            ->published()
            ->forPhotographer($photographer)
            ->withRandomCategories(1) // Attach 1 random category to each album
            ->withPhotos(random_int(5, 20))
            ->create();

        // Create 10 featured albums
        Album::factory()
            ->count(10)
            ->featured()
            ->published()
            ->forPhotographer($photographer)
            ->withRandomCategories(1)
            ->withPhotos(random_int(5, 20))
            ->create();

        // Create 5 NEW albums
        Album::factory()
            ->count(5)
            ->fresh()
            ->recent()
            ->published()
            ->forPhotographer($photographer)
            ->withRandomCategories(1)
            ->withPhotos(random_int(5, 20))
            ->create();

        // Create 10 highly rated albums
        Album::factory()
            ->count(10)
            ->highlyRated()
            ->published()
            ->forPhotographer($photographer)
            ->withRandomCategories(1)
            ->withPhotos(random_int(5, 20))
            ->create();

        // Create 10 popular albums
        Album::factory()
            ->count(10)
            ->popular()
            ->published()
            ->forPhotographer($photographer)
            ->withRandomCategories(1)
            ->withPhotos(random_int(5, 20))
            ->create();

        // Create specific genre albums
        $genres = ['rock', 'metal', 'jazz', 'classical', 'folk', 'drama', 'ballet', 'opera'];
        foreach ($genres as $genre) {
            Album::factory()
                ->count(5)
                ->published()
                ->forPhotographer($photographer)
                ->forGenre($genre)
                ->create();
        }

        // Create 5 unpublished (draft) albums
        Album::factory()
            ->count(5)
            ->unpublished()
            ->forPhotographer($photographer)
            ->withRandomCategories(1)
            ->withPhotos(random_int(5, 20))
            ->create();

        $this->command->info('Albums seeded successfully!');
        $this->command->info('Total albums: '.Album::count());

        $this->call([
            RoleSeeder::class,
            GenreSeeder::class,
            UserSeeder::class,
            //            AlbumSeeder::class,
            //            PhotoSeeder::class,
            BookingRequestSeeder::class,
            CommentSeeder::class,
            RatingSeeder::class,
        ]);
    }
}
