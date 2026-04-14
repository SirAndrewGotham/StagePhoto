<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        CategoryTranslation::truncate();
        Category::truncate();

        $categories = [
            // Music genres
            ['slug' => 'rock', 'icon' => '🎸', 'type' => 'music', 'sort_order' => 10, 'translations' => [
                'ru' => ['name' => 'Рок', 'description' => 'Рок-концерты и фестивали'],
                'en' => ['name' => 'Rock', 'description' => 'Rock concerts and festivals'],
                'eo' => ['name' => 'Roko', 'description' => 'Rokaj koncertoj kaj festivaloj'],
            ]],
            ['slug' => 'metal', 'icon' => '🤘', 'type' => 'music', 'sort_order' => 20, 'translations' => [
                'ru' => ['name' => 'Метал', 'description' => 'Метал-концерты'],
                'en' => ['name' => 'Metal', 'description' => 'Metal concerts'],
                'eo' => ['name' => 'Metalo', 'description' => 'Metalaj koncertoj'],
            ]],
            ['slug' => 'jazz', 'icon' => '🎷', 'type' => 'music', 'sort_order' => 30, 'translations' => [
                'ru' => ['name' => 'Джаз', 'description' => 'Джазовые выступления'],
                'en' => ['name' => 'Jazz', 'description' => 'Jazz performances'],
                'eo' => ['name' => 'Ĵazo', 'description' => 'Ĵazaj prezentoj'],
            ]],
            ['slug' => 'electronic', 'icon' => '🎧', 'type' => 'music', 'sort_order' => 40, 'translations' => [
                'ru' => ['name' => 'Электроника', 'description' => 'Электронная музыка'],
                'en' => ['name' => 'Electronic', 'description' => 'Electronic music'],
                'eo' => ['name' => 'Elektronika', 'description' => 'Elektronika muziko'],
            ]],
            ['slug' => 'classical', 'icon' => '🎻', 'type' => 'music', 'sort_order' => 50, 'translations' => [
                'ru' => ['name' => 'Классика', 'description' => 'Классические концерты'],
                'en' => ['name' => 'Classical', 'description' => 'Classical concerts'],
                'eo' => ['name' => 'Klasika', 'description' => 'Klasikaj koncertoj'],
            ]],
            ['slug' => 'folk', 'icon' => '🪕', 'type' => 'music', 'sort_order' => 60, 'translations' => [
                'ru' => ['name' => 'Фолк', 'description' => 'Фолк-музыка'],
                'en' => ['name' => 'Folk', 'description' => 'Folk music'],
                'eo' => ['name' => 'Folko', 'description' => 'Folka muziko'],
            ]],
            ['slug' => 'festivals', 'icon' => '🎪', 'type' => 'music', 'sort_order' => 70, 'translations' => [
                'ru' => ['name' => 'Фестивали', 'description' => 'Музыкальные фестивали'],
                'en' => ['name' => 'Festivals', 'description' => 'Music festivals'],
                'eo' => ['name' => 'Festivaloj', 'description' => 'Muzikaj festivaloj'],
            ]],

            // Theater genres
            ['slug' => 'drama', 'icon' => '🎭', 'type' => 'theater', 'sort_order' => 100, 'translations' => [
                'ru' => ['name' => 'Драма', 'description' => 'Драматические спектакли'],
                'en' => ['name' => 'Drama', 'description' => 'Drama performances'],
                'eo' => ['name' => 'Dramo', 'description' => 'Dramaj prezentoj'],
            ]],
            ['slug' => 'ballet', 'icon' => '🩰', 'type' => 'theater', 'sort_order' => 110, 'translations' => [
                'ru' => ['name' => 'Балет', 'description' => 'Балетные постановки'],
                'en' => ['name' => 'Ballet', 'description' => 'Ballet performances'],
                'eo' => ['name' => 'Baleto', 'description' => 'Baletaj prezentoj'],
            ]],
            ['slug' => 'opera', 'icon' => '🎤', 'type' => 'theater', 'sort_order' => 120, 'translations' => [
                'ru' => ['name' => 'Опера', 'description' => 'Оперные спектакли'],
                'en' => ['name' => 'Opera', 'description' => 'Opera performances'],
                'eo' => ['name' => 'Opero', 'description' => 'Operaj prezentoj'],
            ]],
            ['slug' => 'musical', 'icon' => '🎵', 'type' => 'theater', 'sort_order' => 130, 'translations' => [
                'ru' => ['name' => 'Мюзикл', 'description' => 'Мюзиклы'],
                'en' => ['name' => 'Musical', 'description' => 'Musicals'],
                'eo' => ['name' => 'Muzikalo', 'description' => 'Muzikaloj'],
            ]],
        ];

        foreach ($categories as $data) {
            $category = Category::create([
                'slug' => $data['slug'],
                'icon' => $data['icon'],
                'type' => $data['type'],
                'sort_order' => $data['sort_order'],
                'is_active' => true,
            ]);

            foreach ($data['translations'] as $locale => $translation) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'],
                ]);
            }
        }

        $this->command->info('Categories seeded successfully!');
    }
}
