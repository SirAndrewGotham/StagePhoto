<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entity;
use App\Models\EntityProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntityProfileFactory extends Factory
{
    protected $model = EntityProfile::class;

    public function definition(): array
    {
        $locales = ['ru', 'en', 'eo'];
        $locale = $this->faker->randomElement($locales);

        $entity = Entity::inRandomOrder()->first();

        return [
            'entity_id' => $entity?->id ?? Entity::factory(),
            'locale' => $locale,
            'name' => $this->getRandomName($locale, $entity->type ?? 'band'),
            'bio' => $this->faker->paragraphs(2, true),
            'story' => $this->faker->optional(0.7)->paragraphs(5, true),
            'website' => $this->faker->optional(0.5)->url(),
            'social_links' => json_encode([
                'telegram' => $this->faker->optional(0.6)->userName(),
                'vk' => $this->faker->optional(0.7)->userName(),
                'instagram' => $this->faker->optional(0.5)->userName(),
            ]),
            'email' => $this->faker->optional(0.7)->companyEmail(),
            'phone' => $this->faker->optional(0.4)->phoneNumber(),
            'address' => $this->faker->optional(0.5)->address(),
            'founded_year' => $this->faker->optional(0.6)->year(),
            'genre' => $this->faker->randomElement(['Rock', 'Metal', 'Jazz', 'Classical', 'Folk', 'Pop', 'Drama', 'Comedy', 'Ballet']),
            'avatar_path' => $this->faker->optional(0.7)->imageUrl(400, 400, 'people', true),
            'cover_path' => $this->faker->optional(0.5)->imageUrl(2000, 800, 'nature', true),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function getRandomName(string $locale, string $type): string
    {
        $names = match ($type) {
            'theater' => [
                'ru' => ['Большой театр', 'МХТ им. Чехова', 'Мариинский театр', 'Театр на Таганке'],
                'en' => ['Bolshoi Theatre', 'Moscow Art Theatre', 'Mariinsky Theatre', 'Taganka Theatre'],
                'eo' => ['Bolŝoj-Teatro', 'Moskva Arta Teatro', 'Mariinskij-Teatro', 'Taganka-Teatro'],
            ],
            'band' => [
                'ru' => ['Кино', 'Аквариум', 'ДДТ', 'Сплин', 'Би-2', 'Мумий Тролль'],
                'en' => ['Kino', 'Aquarium', 'DDT', 'Splean', 'Bi-2', 'Mumiy Troll'],
                'eo' => ['Kino', 'Akvario', 'DDT', 'Splean', 'Bi-2', 'Mumiy Troll'],
            ],
            default => [
                'ru' => ['Виктор Цой', 'Борис Гребенщиков', 'Юрий Шевчук', 'Александр Васильев'],
                'en' => ['Viktor Tsoi', 'Boris Grebenshchikov', 'Yuri Shevchuk', 'Alexander Vasiliev'],
                'eo' => ['Viktor Coj', 'Boris Grebenŝĉikov', 'Jurij Ŝevĉuk', 'Aleksandr Vasiljev'],
            ],
        };

        return $this->faker->randomElement($names[$locale] ?? $names['ru']);
    }

    public function forEntity(Entity $entity): static
    {
        return $this->state(fn (array $attributes) => [
            'entity_id' => $entity->id,
        ]);
    }

    public function forLocale(string $locale): static
    {
        return $this->state(fn (array $attributes) => [
            'locale' => $locale,
        ]);
    }
}
