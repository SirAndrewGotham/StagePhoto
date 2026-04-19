<?php

declare(strict_types=1);

namespace Tests\Feature\Factories;

use App\Models\Album;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Status;
use Tests\TestCase;

class PhotoFactoryTest extends TestCase
{
    public function test_photo_factory_creates_photo_with_exif_data(): void
    {
        $photo = Photo::factory()->create();

        $this->assertNotNull($photo->camera_make);
        $this->assertNotNull($photo->camera_model);
        $this->assertNotNull($photo->lens_model);
        $this->assertNotNull($photo->focal_length);
        $this->assertNotNull($photo->aperture);
        $this->assertNotNull($photo->shutter_speed);
        $this->assertNotNull($photo->iso);
        $this->assertNotNull($photo->exif_data);
    }

    public function test_photo_factory_creates_photo_with_status(): void
    {
        $photo = Photo::factory()->create();

        $this->assertNotNull($photo->status);
        $this->assertContains($photo->status, ['pending', 'approved', 'published', 'rejected', 'blocked']);
    }

    public function test_photo_factory_creates_published_photo(): void
    {
        $photo = Photo::factory()->published()->create();

        $this->assertEquals('published', $photo->status);
    }

    public function test_photo_factory_creates_pending_photo(): void
    {
        $photo = Photo::factory()->pending()->create();

        $this->assertEquals('pending', $photo->status);
    }

    public function test_photo_factory_creates_approved_photo(): void
    {
        $photo = Photo::factory()->approved()->create();

        $this->assertEquals('approved', $photo->status);
    }

    public function test_photo_factory_creates_rejected_photo(): void
    {
        $photo = Photo::factory()->rejected()->create();

        $this->assertEquals('rejected', $photo->status);
    }

    public function test_photo_factory_creates_blocked_photo(): void
    {
        $photo = Photo::factory()->blocked()->create();

        $this->assertEquals('blocked', $photo->status);
    }

    public function test_photo_factory_with_status_history(): void
    {
        $photo = Photo::factory()
            ->withStatusHistory(['pending', 'approved', 'published'])
            ->create();

        $statuses = Status::where('statusable_id', $photo->id)
            ->where('statusable_type', Photo::class)
            ->get();

        $this->assertCount(3, $statuses);
        $this->assertEquals('pending', $statuses[0]->status);
        $this->assertEquals('approved', $statuses[1]->status);
        $this->assertEquals('published', $statuses[2]->status);
        $this->assertEquals('published', $photo->status);
    }

    public function test_photo_factory_with_default_status_history(): void
    {
        $photo = Photo::factory()->withStatusHistory()->create();

        $statuses = Status::where('statusable_id', $photo->id)
            ->where('statusable_type', Photo::class)
            ->get();

        $this->assertCount(3, $statuses);
        $this->assertEquals('pending', $statuses[0]->status);
        $this->assertEquals('approved', $statuses[1]->status);
        $this->assertEquals('published', $statuses[2]->status);
    }

    public function test_photo_factory_status_history_has_comments(): void
    {
        $photo = Photo::factory()->withStatusHistory()->create();

        $status = Status::where('statusable_id', $photo->id)
            ->where('statusable_type', Photo::class)
            ->where('status', 'approved')
            ->first();

        $this->assertNotNull($status->comment);
        $this->assertEquals('Approved by administrator', $status->comment);
    }

    public function test_photo_factory_with_categories(): void
    {
        $category = Category::factory()->create();
        $photo = Photo::factory()
            ->withCategories([$category->id])
            ->create();

        $this->assertDatabaseHas('photo_category', [
            'photo_id' => $photo->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_photo_factory_with_random_categories(): void
    {
        // Create some categories first
        Category::factory()->count(5)->create();

        $photo = Photo::factory()
            ->withRandomCategories(2)
            ->create();

        $this->assertGreaterThanOrEqual(1, $photo->categories()->count());
        $this->assertLessThanOrEqual(2, $photo->categories()->count());
    }

    public function test_photo_factory_with_featured_flag(): void
    {
        $photo = Photo::factory()->featured()->create();

        $this->assertTrue($photo->is_featured);
    }

    public function test_photo_factory_with_trashed(): void
    {
        $photo = Photo::factory()->trashed()->create();

        $this->assertNotNull($photo->deleted_at);
        $this->assertTrue($photo->trashed());
    }

    public function test_photo_factory_for_specific_album(): void
    {
        $album = Album::factory()->create();
        $photo = Photo::factory()->forAlbum($album)->create();

        $this->assertEquals($album->id, $photo->album_id);
    }
}
