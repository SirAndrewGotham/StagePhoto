<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $types = ['music', 'theater', 'dance', 'other'];
        $icons = ['🎸', '🤘', '🎷', '🎧', '🎻', '🪕', '🎪', '🎭', '🩰', '🎤', '🎵', '🎹', '🥁', '🎺', '🎸', '🎼'];

        // Generate a unique slug from fake word
        $baseName = $this->faker->unique()->word();
        $slug = strtolower((string) preg_replace('/[^a-z0-9]/', '-', $baseName));

        return [
            'slug' => $slug,
            'icon' => $this->faker->randomElement($icons),
            'type' => $this->faker->randomElement($types),
            'sort_order' => $this->faker->numberBetween(0, 500),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the category is for music.
     */
    public function music(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'music',
        ]);
    }

    /**
     * Indicate that the category is for theater.
     */
    public function theater(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'theater',
        ]);
    }

    /**
     * Indicate that the category is for dance.
     */
    public function dance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'dance',
        ]);
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the factory to create a category with all translations.
     */
    public function withTranslations(): static
    {
        return $this->afterCreating(function (Category $category) {
            $locales = ['ru', 'en', 'eo'];

            foreach ($locales as $locale) {
                CategoryTranslationFactory::new()->forCategory($category)->create([
                    'locale' => $locale,
                ]);
            }
        });
    }

    /**
     * Configure the factory to create a category with specific sort order.
     */
    public function ordered(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }

    /**
     * Configure the factory to create a category with a specific icon.
     */
    public function withIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }

    /**
     * Configure the factory to create a category with a specific slug.
     */
    public function withSlug(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'slug' => $slug,
        ]);
    }
}
