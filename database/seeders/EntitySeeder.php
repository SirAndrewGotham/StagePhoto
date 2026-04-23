<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Band;
use App\Models\Entity;
use App\Models\EntityContact;
use App\Models\EntityProfile;
use App\Models\Individual;
use App\Models\Theater;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        // Create Theaters
        $this->command->info('Creating theaters...');
        $theaters = [
            ['name' => 'Bolshoi Theatre', 'type' => 'theater', 'bio' => 'The Bolshoi Theatre is a historic theatre in Moscow, Russia, designed by architect Joseph Bové, which holds ballet and opera performances.'],
            ['name' => 'Moscow Art Theatre', 'type' => 'theater', 'bio' => 'The Moscow Art Theatre is a theatre company in Moscow that Konstantin Stanislavski and Vladimir Nemirovich-Danchenko founded in 1898.'],
            ['name' => 'Mariinsky Theatre', 'type' => 'theater', 'bio' => 'The Mariinsky Theatre is a historic theatre of opera and ballet in Saint Petersburg, Russia.'],
            ['name' => 'Taganka Theatre', 'type' => 'theater', 'bio' => 'The Taganka Theatre is a famous theatre in Moscow, known for its avant-garde productions.'],
        ];

        foreach ($theaters as $theaterData) {
            $this->createEntity($theaterData);
        }

        // Create Bands
        $this->command->info('Creating bands...');
        $bands = [
            ['name' => 'Kino', 'type' => 'band', 'bio' => 'Kino was one of the most popular and musically influential rock bands in the history of Russian music.'],
            ['name' => 'Aquarium', 'type' => 'band', 'bio' => 'Aquarium is a rock band formed in Leningrad in 1972, one of the longest-existing and most influential rock bands in Russia.'],
            ['name' => 'DDT', 'type' => 'band', 'bio' => 'DDT is a Russian rock band founded in Ufa in 1980, led by Yuri Shevchuk.'],
            ['name' => 'Splean', 'type' => 'band', 'bio' => 'Splean is a Russian rock band formed in Saint Petersburg in 1994.'],
            ['name' => 'Bi-2', 'type' => 'band', 'bio' => 'Bi-2 is a Belarusian-Russian rock band formed in 1988 in Bobruisk.'],
            ['name' => 'Mumiy Troll', 'type' => 'band', 'bio' => 'Mumiy Troll is a Russian rock band formed in Vladivostok in 1983.'],
        ];

        foreach ($bands as $bandData) {
            $this->createEntity($bandData);
        }

        // Create Individuals
        $this->command->info('Creating individuals...');
        $individuals = [
            ['name' => 'Viktor Tsoi', 'type' => 'individual', 'bio' => 'Viktor Tsoi was a Soviet and Russian singer-songwriter and actor who was the frontman of the rock band Kino.'],
            ['name' => 'Boris Grebenshchikov', 'type' => 'individual', 'bio' => 'Boris Grebenshchikov is a Russian singer-songwriter and poet, founder and frontman of the rock band Aquarium.'],
            ['name' => 'Yuri Shevchuk', 'type' => 'individual', 'bio' => 'Yuri Shevchuk is a Soviet and Russian rock musician, singer-songwriter, poet, and actor, leader of the rock band DDT.'],
            ['name' => 'Alexander Vasiliev', 'type' => 'individual', 'bio' => 'Alexander Vasiliev is a Russian musician, leader and frontman of the rock band Splean.'],
        ];

        foreach ($individuals as $individualData) {
            $this->createEntity($individualData);
        }

        // Create additional random entities
        $this->command->info('Creating additional random entities...');
        Entity::factory()
            ->count(20)
            ->create()
            ->each(function (Entity $entity) {
                $this->createProfilesForEntity($entity);
                $this->createContactsForEntity($entity);
            });

        $this->command->info('Entities seeded successfully!');
        $this->command->info('Total entities: '.Entity::count());
    }

    private function createEntity(array $data): void
    {
        $specificModel = match ($data['type']) {
            'theater' => Theater::create(['capacity' => random_int(200, 2000), 'founded_year' => random_int(1700, 2000)]),
            'band' => Band::create(['genre' => 'Rock', 'formed_year' => random_int(1960, 2010)]),
            'individual' => Individual::create([]),
            default => null,
        };

        if (! $specificModel) {
            return;
        }

        $entity = Entity::create([
            'entityable_id' => $specificModel->id,
            'entityable_type' => $specificModel::class,
            'slug' => Str::slug($data['name']),
            'type' => $data['type'],
            'is_published' => true,
        ]);

        // Create profile in all three languages
        foreach (['ru', 'en', 'eo'] as $locale) {
            EntityProfile::create([
                'entity_id' => $entity->id,
                'locale' => $locale,
                'name' => $locale === 'ru' ? $data['name'] : $this->translateName($data['name'], $locale),
                'bio' => $locale === 'ru' ? $data['bio'] : $this->translateBio($data['bio'], $locale),
            ]);
        }

        // Create contacts
        EntityContact::create([
            'entity_id' => $entity->id,
            'contact_type' => 'email',
            'value' => strtolower(str_replace(' ', '', $data['name'])).'@stagephoto.ru',
            'visibility' => 'public',
        ]);
    }

    private function createProfilesForEntity(Entity $entity): void
    {
        foreach (['ru', 'en', 'eo'] as $locale) {
            EntityProfile::factory()
                ->forEntity($entity)
                ->forLocale($locale)
                ->create();
        }
    }

    private function createContactsForEntity(Entity $entity): void
    {
        EntityContact::factory()
            ->count(random_int(1, 3))
            ->forEntity($entity)
            ->create();
    }

    private function translateName(string $name, string $locale): string
    {
        // Simple translation mapping for demo purposes
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

        return $translations[$name][$locale] ?? $name;
    }

    private function translateBio(string $bio, string $locale): string
    {
        // For demo purposes, just return the same bio with a note
        if ($locale !== 'ru') {
            return $bio.' (Translated to '.strtoupper($locale).')';
        }

        return $bio;
    }
}
