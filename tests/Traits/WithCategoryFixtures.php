<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Category;
use Database\Factories\CategoryTranslationFactory;

trait WithCategoryFixtures
{
    protected function createMusicCategory(string $slug, string $name, int $sortOrder = 10): Category
    {
        $category = Category::factory()
            ->music()
            ->ordered($sortOrder)
            ->withSlug($slug)
            ->create();

        // Add translations
        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->russian()
            ->withName($name.' (RU)')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->english()
            ->withName($name.' (EN)')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->esperanto()
            ->withName($name.' (EO)')
            ->create();

        return $category;
    }

    protected function createTheaterCategory(string $slug, string $name, int $sortOrder = 100): Category
    {
        $category = Category::factory()
            ->theater()
            ->ordered($sortOrder)
            ->withSlug($slug)
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->russian()
            ->withName($name.' (RU)')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->english()
            ->withName($name.' (EN)')
            ->create();

        CategoryTranslationFactory::new()
            ->forCategory($category)
            ->esperanto()
            ->withName($name.' (EO)')
            ->create();

        return $category;
    }

    protected function createRandomCategories(int $count = 5): void
    {
        Category::factory()
            ->count($count)
            ->withTranslations()
            ->create();
    }

    protected function createCategoryTree(): array
    {
        $parent = Category::factory()
            ->music()
            ->ordered(1)
            ->withSlug('parent-category')
            ->withTranslations()
            ->create();

        $children = Category::factory()
            ->count(3)
            ->music()
            ->withTranslations()
            ->create();

        return [
            'parent' => $parent,
            'children' => $children,
        ];
    }
}
