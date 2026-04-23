<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Band;
use App\Models\Category;
use App\Models\Entity;
use App\Models\EntityContact;
use App\Models\EntityMembership;
use App\Models\EntityProfile;
use App\Models\Individual;
use App\Models\Photo;
use App\Models\Status;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        // ========== ENTITY SYSTEM ==========
        $this->command->info('Creating entities (theaters, bands, individuals)...');

        // Create Theaters
        $theaters = $this->createTheaters();

        // Create Bands
        $bands = $this->createBands();

        // Create Individuals
        $individuals = $this->createIndividuals();

        // Create memberships (individuals in bands/theaters)
        $this->createMemberships($individuals, $bands);

        // ========== EXISTING ALBUM SYSTEM ==========

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

        // Link entities to albums
        $this->linkEntitiesToAlbums(array_merge($theaters, $bands, $individuals));

        // Tag photos with entities
        $this->tagPhotosWithEntities(array_merge($theaters, $bands, $individuals));

        $this->command->info('✓ Database seeding completed successfully!');
        $this->command->info('Total albums: '.Album::count());
        $this->command->info('Total photos: '.Photo::count());
        $this->command->info('Total status records: '.Status::count());
        $this->command->info('Total entities: '.Entity::count());
    }

    // ========== ENTITY CREATION METHODS ==========

    private function createTheaters(): array
    {
        $theaters = [];
        $theaterData = [
            ['name' => 'Bolshoi Theatre', 'bio' => 'The Bolshoi Theatre is a historic theatre in Moscow, Russia, designed by architect Joseph Bové, which holds ballet and opera performances.', 'capacity' => 2150, 'founded_year' => 1776, 'director' => 'Vladimir Urin'],
            ['name' => 'Moscow Art Theatre', 'bio' => 'The Moscow Art Theatre is a theatre company in Moscow that Konstantin Stanislavski and Vladimir Nemirovich-Danchenko founded in 1898.', 'capacity' => 1000, 'founded_year' => 1898, 'director' => 'Konstantin Stanislavski'],
            ['name' => 'Mariinsky Theatre', 'bio' => 'The Mariinsky Theatre is a historic theatre of opera and ballet in Saint Petersburg, Russia.', 'capacity' => 2000, 'founded_year' => 1860, 'director' => 'Valery Gergiev'],
            ['name' => 'Taganka Theatre', 'bio' => 'The Taganka Theatre is a famous theatre in Moscow, known for its avant-garde productions.', 'capacity' => 800, 'founded_year' => 1964, 'director' => 'Yuri Lyubimov'],
        ];

        foreach ($theaterData as $data) {
            $theater = Theater::create([
                'capacity' => $data['capacity'],
                'founded_year' => $data['founded_year'],
                'artistic_director' => $data['director'],
            ]);

            $entity = Entity::create([
                'entityable_id' => $theater->id,
                'entityable_type' => Theater::class,
                'slug' => Str::slug($data['name']),
                'type' => 'theater',
                'is_published' => true,
            ]);

            // Create profiles for all three languages
            foreach (['ru', 'en', 'eo'] as $locale) {
                EntityProfile::create([
                    'entity_id' => $entity->id,
                    'locale' => $locale,
                    'name' => $this->translateName($data['name'], $locale),
                    'bio' => $locale === 'ru' ? $data['bio'] : $this->translateBio($data['bio'], $locale),
                    'address' => $locale === 'ru' ? 'Москва, Россия' : 'Moscow, Russia',
                ]);
            }

            // Add contact info
            EntityContact::create([
                'entity_id' => $entity->id,
                'contact_type' => 'email',
                'value' => strtolower(str_replace(' ', '', $data['name'])).'@theatre.ru',
                'visibility' => 'public',
            ]);

            $theaters[] = $entity;
        }

        $this->command->info('Created '.count($theaters).' theaters');

        return $theaters;
    }

    private function createBands(): array
    {
        $bands = [];
        $bandData = [
            ['name' => 'Kino', 'bio' => 'Kino was one of the most popular and musically influential rock bands in the history of Russian music.', 'genre' => 'Rock', 'formed_year' => 1981],
            ['name' => 'Aquarium', 'bio' => 'Aquarium is a rock band formed in Leningrad in 1972, one of the longest-existing and most influential rock bands in Russia.', 'genre' => 'Rock', 'formed_year' => 1972],
            ['name' => 'DDT', 'bio' => 'DDT is a Russian rock band founded in Ufa in 1980, led by Yuri Shevchuk.', 'genre' => 'Rock', 'formed_year' => 1980],
            ['name' => 'Splean', 'bio' => 'Splean is a Russian rock band formed in Saint Petersburg in 1994.', 'genre' => 'Rock', 'formed_year' => 1994],
            ['name' => 'Bi-2', 'bio' => 'Bi-2 is a Belarusian-Russian rock band formed in 1988 in Bobruisk.', 'genre' => 'Rock', 'formed_year' => 1988],
            ['name' => 'Mumiy Troll', 'bio' => 'Mumiy Troll is a Russian rock band formed in Vladivostok in 1983.', 'genre' => 'Rock', 'formed_year' => 1983],
        ];

        foreach ($bandData as $data) {
            $band = Band::create([
                'genre' => $data['genre'],
                'formed_year' => $data['formed_year'],
            ]);

            $entity = Entity::create([
                'entityable_id' => $band->id,
                'entityable_type' => Band::class,
                'slug' => Str::slug($data['name']),
                'type' => 'band',
                'is_published' => true,
            ]);

            // Create profiles for all three languages
            foreach (['ru', 'en', 'eo'] as $locale) {
                EntityProfile::create([
                    'entity_id' => $entity->id,
                    'locale' => $locale,
                    'name' => $this->translateName($data['name'], $locale),
                    'bio' => $locale === 'ru' ? $data['bio'] : $this->translateBio($data['bio'], $locale),
                    'genre' => $data['genre'],
                    'founded_year' => (string) $data['formed_year'],
                ]);
            }

            // Add contact info
            EntityContact::create([
                'entity_id' => $entity->id,
                'contact_type' => 'email',
                'value' => strtolower(str_replace(' ', '', $data['name'])).'@band.ru',
                'visibility' => 'photographers',
            ]);

            $bands[] = $entity;
        }

        $this->command->info('Created '.count($bands).' bands');

        return $bands;
    }

    private function createIndividuals(): array
    {
        $individuals = [];
        $individualData = [
            ['name' => 'Viktor Tsoi', 'bio' => 'Viktor Tsoi was a Soviet and Russian singer-songwriter and actor who was the frontman of the rock band Kino.', 'band' => 'Kino', 'role' => 'vocalist'],
            ['name' => 'Boris Grebenshchikov', 'bio' => 'Boris Grebenshchikov is a Russian singer-songwriter and poet, founder and frontman of the rock band Aquarium.', 'band' => 'Aquarium', 'role' => 'vocalist'],
            ['name' => 'Yuri Shevchuk', 'bio' => 'Yuri Shevchuk is a Soviet and Russian rock musician, singer-songwriter, poet, and actor, leader of the rock band DDT.', 'band' => 'DDT', 'role' => 'vocalist'],
            ['name' => 'Alexander Vasiliev', 'bio' => 'Alexander Vasiliev is a Russian musician, leader and frontman of the rock band Splean.', 'band' => 'Splean', 'role' => 'vocalist'],
        ];

        foreach ($individualData as $data) {
            $individual = Individual::create([]);

            $entity = Entity::create([
                'entityable_id' => $individual->id,
                'entityable_type' => Individual::class,
                'slug' => Str::slug($data['name']),
                'type' => 'individual',
                'is_published' => true,
            ]);

            // Create profiles for all three languages
            foreach (['ru', 'en', 'eo'] as $locale) {
                EntityProfile::create([
                    'entity_id' => $entity->id,
                    'locale' => $locale,
                    'name' => $this->translateName($data['name'], $locale),
                    'bio' => $locale === 'ru' ? $data['bio'] : $this->translateBio($data['bio'], $locale),
                ]);
            }

            $individuals[] = $entity;
        }

        $this->command->info('Created '.count($individuals).' individuals');

        return $individuals;
    }

    private function createMemberships(array $individuals, array $bands): void
    {
        // Map band names to entities
        $bandMap = [];
        foreach ($bands as $band) {
            $bandProfile = $band->profile();
            $bandMap[$bandProfile->name] = $band;
        }

        // Create memberships for known individuals
        $memberships = [
            ['individual' => 'Viktor Tsoi', 'parent' => 'Kino', 'role' => 'vocalist'],
            ['individual' => 'Boris Grebenshchikov', 'parent' => 'Aquarium', 'role' => 'vocalist'],
            ['individual' => 'Yuri Shevchuk', 'parent' => 'DDT', 'role' => 'vocalist'],
            ['individual' => 'Alexander Vasiliev', 'parent' => 'Splean', 'role' => 'vocalist'],
        ];

        foreach ($memberships as $membership) {
            $individual = array_find($individuals, fn ($ind) => $ind->profile()->name === $membership['individual']);
            $parent = $bandMap[$membership['parent']] ?? null;

            if ($individual && $parent) {
                EntityMembership::create([
                    'entity_id' => $individual->id,
                    'parent_entity_id' => $parent->id,
                    'role' => $membership['role'],
                    'joined_at' => now()->subYears(random_int(10, 30)),
                ]);
            }
        }

        $this->command->info('Created '.EntityMembership::count().' memberships');
    }

    private function linkEntitiesToAlbums(array $entities): void
    {
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

        $this->command->info('Linked entities to albums');
    }

    private function tagPhotosWithEntities(array $entities): void
    {
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

        $this->command->info('Tagged photos with entities');
    }

    // ========== HELPER METHODS ==========

    private function getRandomRelationship(): string
    {
        $relationships = ['featured', 'dedicated', 'guest'];

        return $relationships[array_rand($relationships)];
    }

    private function translateName(string $name, string $locale): string
    {
        $translations = [
            'Bolshoi Theatre' => ['en' => 'Bolshoi Theatre', 'eo' => 'Bolŝoj-Teatro'],
            'Moscow Art Theatre' => ['en' => 'Moscow Art Theatre', 'eo' => 'Moskva Arta Teatro'],
            'Mariinsky Theatre' => ['en' => 'Mariinsky Theatre', 'eo' => 'Mariinskij-Teatro'],
            'Taganka Theatre' => ['en' => 'Taganka Theatre', 'eo' => 'Taganka-Teatro'],
            'Kino' => ['en' => 'Kino', 'eo' => 'Kino'],
            'Aquarium' => ['en' => 'Aquarium', 'eo' => 'Akvario'],
            'DDT' => ['en' => 'DDT', 'eo' => 'DDT'],
            'Splean' => ['en' => 'Splean', 'eo' => 'Splean'],
            'Bi-2' => ['en' => 'Bi-2', 'eo' => 'Bi-2'],
            'Mumiy Troll' => ['en' => 'Mumiy Troll', 'eo' => 'Mumiy Troll'],
            'Viktor Tsoi' => ['en' => 'Viktor Tsoi', 'eo' => 'Viktor Coj'],
            'Boris Grebenshchikov' => ['en' => 'Boris Grebenshchikov', 'eo' => 'Boris Grebenŝĉikov'],
            'Yuri Shevchuk' => ['en' => 'Yuri Shevchuk', 'eo' => 'Jurij Ŝevĉuk'],
            'Alexander Vasiliev' => ['en' => 'Alexander Vasiliev', 'eo' => 'Aleksandr Vasiljev'],
        ];

        if ($locale === 'ru') {
            return $name;
        }

        return $translations[$name][$locale] ?? $name;
    }

    private function translateBio(string $bio, string $locale): string
    {
        if ($locale !== 'ru') {
            return $bio.' (Translated to '.strtoupper($locale).')';
        }

        return $bio;
    }

    // ========== EXISTING METHODS (unchanged) ==========

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

            $categories = Category::inRandomOrder()->limit(random_int(1, 2))->pluck('id');
            $album->categories()->attach($categories);

            $this->createStatusHistory($album, $status, $photographer, $admin);

            $photoCount = random_int(5, 20);
            for ($j = 0; $j < $photoCount; $j++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->$status()
                    ->create([
                        'sort_order' => $j,
                        'is_featured' => $j === 0,
                    ]);

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
