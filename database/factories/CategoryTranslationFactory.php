<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryTranslationFactory extends Factory
{
    protected $model = CategoryTranslation::class;

    public function definition(): array
    {
        // Generate different names per locale for realism
        $locale = $this->faker->randomElement(['ru', 'en', 'eo']);

        $namesByLocale = [
            'ru' => [
                'Рок', 'Метал', 'Джаз', 'Электроника', 'Классика', 'Фолк',
                'Фестивали', 'Драма', 'Балет', 'Опера', 'Мюзикл', 'Экспериментальный',
                'Альтернатива', 'Панк', 'Блюз', 'Хип-хоп', 'R&B', 'Поп',
            ],
            'en' => [
                'Rock', 'Metal', 'Jazz', 'Electronic', 'Classical', 'Folk',
                'Festivals', 'Drama', 'Ballet', 'Opera', 'Musical', 'Experimental',
                'Alternative', 'Punk', 'Blues', 'Hip Hop', 'R&B', 'Pop',
            ],
            'eo' => [
                'Roko', 'Metalo', 'Ĵazo', 'Elektronika', 'Klasika', 'Folko',
                'Festivaloj', 'Dramo', 'Baleto', 'Opero', 'Muzikalo', 'Eksperimenta',
                'Alternativo', 'Punko', 'Bluso', 'Hiphopo', 'R&B', 'Popo',
            ],
        ];

        $descriptionsByLocale = [
            'ru' => [
                'Концерты и фестивали рок-музыки',
                'Тяжелая музыка и метал-концерты',
                'Джазовые выступления и концерты',
                'Электронная музыка и диджей-сеты',
                'Классические концерты и симфоническая музыка',
                'Фолк-музыка и этнические мотивы',
                'Крупные музыкальные фестивали',
                'Драматические театральные постановки',
                'Балетные спектакли и танцевальные представления',
                'Оперные постановки',
                'Мюзиклы и музыкальные театры',
                'Экспериментальные театральные формы',
            ],
            'en' => [
                'Rock concerts and festivals',
                'Heavy metal music and concerts',
                'Jazz performances and concerts',
                'Electronic music and DJ sets',
                'Classical concerts and symphonic music',
                'Folk music and ethnic performances',
                'Major music festivals',
                'Dramatic theater productions',
                'Ballet performances and dance shows',
                'Opera productions',
                'Musicals and music theater',
                'Experimental theater forms',
            ],
            'eo' => [
                'Rokaj koncertoj kaj festivaloj',
                'Peza metala muziko kaj koncertoj',
                'Ĵazaj prezentoj kaj koncertoj',
                'Elektronika muziko kaj DĴ-aj prezentoj',
                'Klasikaj koncertoj kaj simfonia muziko',
                'Folka muziko kaj etnaj prezentoj',
                'Grandaj muzikaj festivaloj',
                'Dramaj teatraj produktoj',
                'Baletaj prezentoj kaj dancoj',
                'Operaj produktoj',
                'Muzikaloj kaj muzika teatro',
                'Eksperimentaj teatraj formoj',
            ],
        ];

        return [
            'category_id' => Category::factory(),
            'locale' => $locale,
            'name' => $this->faker->randomElement($namesByLocale[$locale]),
            'description' => $this->faker->optional(0.7)->randomElement($descriptionsByLocale[$locale]), // 70% chance of description
        ];
    }

    /**
     * Set the locale for the translation.
     */
    public function forLocale(string $locale): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => $locale,
        ]);
    }

    /**
     * Set the locale to Russian.
     */
    public function russian(): static
    {
        return $this->forLocale('ru');
    }

    /**
     * Set the locale to English.
     */
    public function english(): static
    {
        return $this->forLocale('en');
    }

    /**
     * Set the locale to Esperanto.
     */
    public function esperanto(): static
    {
        return $this->forLocale('eo');
    }

    /**
     * Set the category for this translation.
     */
    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $category->id,
        ]);
    }

    /**
     * Set a specific name for the translation.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

    /**
     * Set a specific description for the translation.
     */
    public function withDescription(?string $description): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => $description,
        ]);
    }

    /**
     * Create a complete set of translations for all three locales.
     */
    public function completeSet(): static
    {
        return $this->afterMaking(function (CategoryTranslation $translation) {
            // This is handled by the state, but we can add additional logic
        })->afterCreating(function (CategoryTranslation $translation) {
            $category = $translation->category;
            $locales = ['ru', 'en', 'eo'];
            $existingLocales = $category->translations->pluck('locale')->toArray();

            foreach ($locales as $locale) {
                if (! in_array($locale, $existingLocales)) {
                    CategoryTranslationFactory::new()->forCategory($category)->forLocale($locale)->create();
                }
            }
        });
    }
}
