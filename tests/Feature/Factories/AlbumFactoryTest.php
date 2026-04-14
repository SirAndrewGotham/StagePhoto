<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Album;
use App\Models\Category;
use App\Models\User;
use Tests\TestCase;

class AlbumFactoryTest extends TestCase
{
    public function test_can_create_single_album(): void
    {
        $album = Album::factory()->create();

        $this->assertDatabaseHas('albums', ['id' => $album->id]);
        $this->assertNotNull($album->title);
        $this->assertNotNull($album->slug);
    }

    public function test_can_create_published_album(): void
    {
        $album = Album::factory()->published()->create();

        $this->assertTrue($album->is_published);
    }

    public function test_can_create_featured_album(): void
    {
        $album = Album::factory()->featured()->create();

        $this->assertEquals('🔥 FEATURED', $album->badge);
    }

    public function test_can_create_album_with_categories(): void
    {
        $category = Category::factory()->create();
        $album = Album::factory()
            ->withCategories([$category->id])
            ->create();

        $this->assertDatabaseHas('album_category', [
            'album_id' => $album->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_can_create_album_for_specific_photographer(): void
    {
        $photographer = User::factory()->create();
        $album = Album::factory()
            ->forPhotographer($photographer)
            ->create();

        $this->assertEquals($photographer->id, $album->photographer_id);
    }

    public function test_can_create_many_albums(): void
    {
        $albums = Album::factory()->count(10)->create();

        $this->assertCount(10, $albums);
    }

    public function test_can_create_album_for_genre(): void
    {
        $album = Album::factory()
            ->forGenre('rock')
            ->create();

        $this->assertTrue($album->categories->contains('slug', 'rock'));
    }

    public function test_can_create_highly_rated_album(): void
    {
        $album = Album::factory()->highlyRated()->create();

        $this->assertGreaterThan(4.5, $album->rating);
    }

    public function test_can_create_popular_album(): void
    {
        $album = Album::factory()->popular()->create();

        $this->assertGreaterThan(5000, $album->views);
    }

    public function test_can_create_recent_album(): void
    {
        $album = Album::factory()->recent()->create();
        $thirtyDaysAgo = now()->subDays(30);

        $this->assertGreaterThan($thirtyDaysAgo, $album->event_date);
    }
}
