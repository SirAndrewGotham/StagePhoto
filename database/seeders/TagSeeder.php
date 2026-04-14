<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'portrait', 'color' => 'blue'],
            ['name' => 'landscape', 'color' => 'green'],
            ['name' => 'black-and-white', 'color' => 'gray'],
            ['name' => 'colorful', 'color' => 'pink'],
            ['name' => 'dramatic', 'color' => 'purple'],
            ['name' => 'intimate', 'color' => 'red'],
            ['name' => 'crowd', 'color' => 'yellow'],
            ['name' => 'backstage', 'color' => 'indigo'],
            ['name' => 'close-up', 'color' => 'blue'],
            ['name' => 'wide-angle', 'color' => 'green'],
            ['name' => 'long-exposure', 'color' => 'purple'],
            ['name' => 'silhouette', 'color' => 'gray'],
            ['name' => 'smoke', 'color' => 'gray'],
            ['name' => 'lasers', 'color' => 'pink'],
            ['name' => 'acoustic', 'color' => 'green'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }
    }
}
