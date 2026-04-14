<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Database\Factories\CategoryTranslationFactory;
use Illuminate\Database\Seeder;

class TestCategorySeeder extends Seeder
{
    /**
     * Run the database seeds to create test data.
     */
    public function run(): void
    {
        // Create 20 random categories with translations
        Category::factory()
            ->count(20)
            ->withTranslations()
            ->create();

        // Create 5 music categories with specific properties
        Category::factory()
            ->count(5)
            ->music()
            ->withTranslations()
            ->create();

        // Create 5 theater categories with specific properties
        Category::factory()
            ->count(5)
            ->theater()
            ->withTranslations()
            ->create();

        // Create 3 inactive categories
        Category::factory()
            ->count(3)
            ->inactive()
            ->withTranslations()
            ->create();

        // Create categories with specific sort orders
        Category::factory()
            ->ordered(1)
            ->withIcon('🎸')
            ->withSlug('rock-legend')
            ->withTranslations()
            ->create();

        Category::factory()
            ->ordered(2)
            ->withIcon('🎭')
            ->withSlug('drama-master')
            ->withTranslations()
            ->create();

        // Create a category with only Russian translation (for testing edge cases)
        $category = Category::factory()->create(['slug' => 'russian-only']);
        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->russian()
            ->withName('Только по-русски')
            ->withDescription('Эта категория существует только на русском языке')
            ->create();

        // Create a category with custom names per locale
        $customCategory = Category::factory()->create(['slug' => 'custom-names']);

        CategoryTranslationFactory::new()
            ->forCategory($customCategory)
            ->russian()
            ->withName('Особенная категория')
            ->withDescription('Уникальное описание на русском')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($customCategory)
            ->english()
            ->withName('Special Category')
            ->withDescription('Unique description in English')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($customCategory)
            ->esperanto()
            ->withName('Speciala Kategorio')
            ->withDescription('Unika priskribo en Esperanto')
            ->create();
    }
}
