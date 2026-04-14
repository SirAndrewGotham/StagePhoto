<?php

use App\Models\Category;
use App\Models\CategoryTranslation;

test('category factory creates valid category', function () {
    $category = Category::factory()->create();

    expect($category)->toBeInstanceOf(Category::class);
    expect($category->slug)->not->toBeEmpty();
    expect($category->type)->toBeIn(['music', 'theater', 'dance', 'other']);
    expect($category->is_active)->toBeBool();
});

test('category factory with translations creates translations', function () {
    $category = Category::factory()
        ->withTranslations()
        ->create();

    $category->load('translations');

    expect($category->translations)->toHaveCount(3);
    expect($category->translations->pluck('locale'))->toContain('ru', 'en', 'eo');
});

test('music category factory creates music type', function () {
    $category = Category::factory()->music()->create();

    expect($category->type)->toBe('music');
});

test('theater category factory creates theater type', function () {
    $category = Category::factory()->theater()->create();

    expect($category->type)->toBe('theater');
});

test('inactive category factory creates inactive category', function () {
    $category = Category::factory()->inactive()->create();

    expect($category->is_active)->toBeFalse();
});

test('category translation factory creates valid translation', function () {
    $translation = CategoryTranslationFactory::new()->create();

    expect($translation)->toBeInstanceOf(CategoryTranslation::class);
    expect($translation->locale)->toBeIn(['ru', 'en', 'eo']);
    expect($translation->name)->not->toBeEmpty();
});

test('category translation factory creates russian translation', function () {
    $translation = CategoryTranslationFactory::new()->russian()->create();

    expect($translation->locale)->toBe('ru');
});

test('category translation factory creates complete set of translations', function () {
    $category = Category::factory()->create();

    CategoryTranslationFactory::new()
        ->forCategory($category)
        ->completeSet()
        ->create();

    $category->load('translations');

    expect($category->translations)->toHaveCount(3);
    expect($category->translations->pluck('locale'))->toContain('ru', 'en', 'eo');
});

test('category with specific sort order factory', function () {
    $order = 42;
    $category = Category::factory()->ordered($order)->create();

    expect($category->sort_order)->toBe($order);
});

test('category with specific icon factory', function () {
    $icon = '🎸';
    $category = Category::factory()->withIcon($icon)->create();

    expect($category->icon)->toBe($icon);
});
