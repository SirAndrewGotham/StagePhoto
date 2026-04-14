<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function getAllCategories($type = null)
    {
        $cacheKey = $type ? "categories_{$type}" : 'categories_all';

        // Cache as array, not as Eloquent Collection
        return Cache::remember($cacheKey, 3600, function () use ($type) {
            $query = Category::with('translations')
                ->active()
                ->ordered();

            if ($type) {
                $query->ofType($type);
            }

            // Convert to array to avoid serialization issues
            return $query->get()->map(fn ($category) => [
                'id' => $category->id,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'type' => $category->type,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'name' => $category->name, // Uses accessor with current locale
                'description' => $category->description,
                'translations' => $category->translations->map(fn ($trans) => [
                    'locale' => $trans->locale,
                    'name' => $trans->name,
                    'description' => $trans->description,
                ])->toArray(),
            ])->toArray();
        });
    }

    /**
     * @return non-empty-list[]
     */
    public function getFilterBarCategories(): array
    {
        $categories = $this->getAllCategories();

        // Group by type for filter bar
        $grouped = [];
        foreach ($categories as $category) {
            $type = $category['type'];
            if (! isset($grouped[$type])) {
                $grouped[$type] = [];
            }
            $grouped[$type][] = $category;
        }

        return $grouped;
    }

    public function getCategoriesForApi()
    {
        return Cache::remember('categories_api', 3600, function () {
            $categories = Category::with('translations')
                ->active()
                ->ordered()
                ->get();

            return $categories->map(function ($category): array {
                $translations = [];
                foreach (['ru', 'en', 'eo'] as $locale) {
                    $translation = $category->translations->firstWhere('locale', $locale);
                    $translations[$locale] = $translation ? [
                        'name' => $translation->name,
                        'description' => $translation->description,
                    ] : null;
                }

                return [
                    'id' => $category->id,
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                    'type' => $category->type,
                    'translations' => $translations,
                ];
            })->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget('categories_all');
        Cache::forget('categories_api');
        Cache::forget('categories_music');
        Cache::forget('categories_theater');
    }
}
