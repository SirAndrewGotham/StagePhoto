<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    public function test_categories_can_be_created(): void
    {
        // Create a single category with translations
        $category = Category::factory()
            ->music()
            ->withTranslations()
            ->create();

        $this->assertDatabaseHas('categories', ['slug' => $category->slug]);
        $this->assertDatabaseCount('category_translations', 3);
    }

    public function test_can_create_many_categories(): void
    {
        // Create 10 random categories
        $categories = Category::factory()
            ->count(10)
            ->withTranslations()
            ->create();

        $this->assertCount(10, $categories);
    }

    public function test_can_create_custom_category(): void
    {
        $category = Category::factory()
            ->withSlug('special-event')
            ->withIcon('⭐')
            ->ordered(999)
            ->inactive()
            ->create();

        $this->assertEquals('special-event', $category->slug);
        $this->assertEquals('⭐', $category->icon);
        $this->assertEquals(999, $category->sort_order);
        $this->assertFalse($category->is_active);
    }
}
