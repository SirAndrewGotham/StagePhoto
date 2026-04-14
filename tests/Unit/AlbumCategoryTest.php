<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\WithCategoryFixtures;

class AlbumCategoryTest extends TestCase
{
    use WithCategoryFixtures;

    public function test_album_can_have_categories(): void
    {
        $this->createMusicCategory('rock', 'Rock', 10);
        $this->createMusicCategory('metal', 'Metal', 20);

        // Your test logic here
    }
}
