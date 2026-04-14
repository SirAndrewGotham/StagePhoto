<?php

declare(strict_types=1);

if (! function_exists('trans_category')) {
    function trans_category(array $category)
    {
        $key = "album.categories.{$category['slug']}";
        $translation = __($key);

        // If translation exists (doesn't return the key itself), use it
        if ($translation !== $key) {
            return $translation;
        }

        // Otherwise return the category name from database
        return $category['name'];
    }
}
