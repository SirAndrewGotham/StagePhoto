<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Photo;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AlbumFactory extends Factory
{
    protected $model = Album::class;

    public function definition(): array
    {
        $titles = [
            'Arctic Monkeys • Live at Luzhniki',
            'Swan Lake • Bolshoi Theater Premiere',
            'Park Live Festival 2025 • Day 2',
            'Igor Butman Quartet • Intimate Session',
            'Slayer Tribute • Final Tour Moscow',
            'Hamlet • Taganka Theater Revival',
            'Molchat Doma • 16 Tons Club',
            'Valery Gergiev • Tchaikovsky Symphony No. 5',
            'Nina Kraviz • Garage Live Set',
            'Pelageya • Open Air Folk Fest',
        ];

        $venues = [
            'Luzhniki Stadium, Moscow',
            'Bolshoi Theater, Moscow',
            'Park Live, Moscow',
            'Jazz Club, Moscow',
            'Arena Moscow',
            'Taganka Theater, Moscow',
            '16 Tons Club, Moscow',
            'Tchaikovsky Hall, Moscow',
            'Garage Club, Moscow',
            'Folk Festival, Moscow',
        ];

        $badges = ['✨ NEW', '🔥 FEATURED', '👤 YOUR WORK', null];
        $badgeGradients = [
            'from-pink-500 to-orange-500',
            'from-indigo-600 to-purple-600',
            'from-emerald-500 to-teal-500',
            null,
        ];

        $statuses = ['pending', 'approved', 'published', 'rejected', 'blocked'];
        $status = $this->faker->randomElement($statuses);

        $title = $this->faker->randomElement($titles);
        $badge = $this->faker->randomElement($badges);
        $badgeGradient = $badge ? $this->faker->randomElement(array_filter($badgeGradients)) : null;

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraphs(3, true),
            'cover_image' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=800&h=800&q=80',
            'cover_image_square' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=800&h=800&q=80',
            'cover_image_hero' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=2000&h=800&q=80',
            'photographer_id' => User::factory(),
            'venue' => $this->faker->randomElement($venues),
            'event_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'photo_count' => $this->faker->numberBetween(15, 250),
            'rating' => $this->faker->randomFloat(1, 3.5, 5.0),
            'views' => $this->faker->numberBetween(100, 10000),
            'is_published' => $status === 'published',
            'is_unsorted' => false,
            'status' => $status,
            'badge' => $badge,
            'badge_gradient' => $badgeGradient,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Create photos for the album with optional categories
     */
    public function withPhotos(int $count = 5, ?array $categoryIds = null): static
    {
        return $this->afterCreating(function (Album $album) use ($count, $categoryIds) {
            for ($i = 0; $i < $count; $i++) {
                $photo = Photo::factory()
                    ->forAlbum($album)
                    ->create([
                        'sort_order' => $i,
                        'is_featured' => $i === 0,
                    ]);

                if ($categoryIds) {
                    $photo->categories()->attach($categoryIds);
                }
            }
        });
    }

    /**
     * Indicate that the album is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'status' => 'published',
        ]);
    }

    /**
     * Indicate that the album is unpublished (draft).
     */
    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the album is pending review.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the album is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the album is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the album is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the album is an unsorted album.
     */
    public function unsorted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unsorted' => true,
            'is_published' => false,
            'status' => 'pending',
            'title' => 'Unsorted',
            'slug' => 'unsorted-'.($attributes['photographer_id'] ?? 1),
            'badge' => '📁 UNSORTED',
            'badge_gradient' => 'from-gray-500 to-gray-600',
        ]);
    }

    /**
     * Set a specific photographer for the album.
     */
    public function forPhotographer(User $photographer): static
    {
        return $this->state(fn (array $attributes) => [
            'photographer_id' => $photographer->id,
        ]);
    }

    /**
     * Set a specific rating for the album.
     */
    public function withRating(float $rating): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $rating,
        ]);
    }

    /**
     * Set a specific number of views.
     */
    public function withViews(int $views): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => $views,
        ]);
    }

    /**
     * Set a specific badge for the album.
     */
    public function withBadge(string $badge, ?string $gradient = null): static
    {
        return $this->state(fn (array $attributes) => [
            'badge' => $badge,
            'badge_gradient' => $gradient ?? 'from-pink-500 to-orange-500',
        ]);
    }

    /**
     * Create an album with a NEW badge.
     */
    public function fresh(): static
    {
        return $this->withBadge('✨ NEW', 'from-pink-500 to-orange-500');
    }

    /**
     * Create an album with a FEATURED badge.
     */
    public function featured(): static
    {
        return $this->withBadge('🔥 FEATURED', 'from-indigo-600 to-purple-600');
    }

    /**
     * Create an album with a YOUR WORK badge.
     */
    public function yourWork(): static
    {
        return $this->withBadge('👤 YOUR WORK', 'from-emerald-500 to-teal-500');
    }

    /**
     * Indicate that the album is soft deleted.
     */
    public function trashed(): static
    {
        return $this->state(fn (array $attributes) => [
            'deleted_at' => now(),
        ]);
    }

    /**
     * Create a highly rated album (rating > 4.5).
     */
    public function highlyRated(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->randomFloat(1, 4.6, 5.0),
        ]);
    }

    /**
     * Create a popular album (views > 5000).
     */
    public function popular(): static
    {
        return $this->state(fn (array $attributes) => [
            'views' => $this->faker->numberBetween(5000, 50000),
        ]);
    }

    /**
     * Create a recent album (within last 30 days).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ]);
    }

    /**
     * Create an album with many photos.
     */
    public function manyPhotos(int $count = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_count' => $count,
        ]);
    }

    /**
     * Create status history for the album.
     */
    public function withStatusHistory(?array $statuses = null): static
    {
        return $this->afterCreating(function (Album $album) use ($statuses) {
            $defaultStatuses = $statuses ?? ['pending', 'approved', 'published'];
            $user = User::first();

            foreach ($defaultStatuses as $index => $status) {
                Status::create([
                    'statusable_id' => $album->id,
                    'statusable_type' => Album::class,
                    'status' => $status,
                    'comment' => $this->getStatusComment($status),
                    'changed_by' => $user?->id ?? 1,
                    'created_at' => now()->subDays(count($defaultStatuses) - $index),
                ]);
            }

            $album->update(['status' => end($defaultStatuses)]);
        });
    }

    /**
     * Get comment for status change.
     */
    private function getStatusComment(string $status): string
    {
        return match ($status) {
            'pending' => 'Initial submission awaiting review',
            'approved' => 'Approved by administrator',
            'published' => 'Published to public',
            'rejected' => 'Does not meet quality standards',
            'blocked' => 'Blocked due to policy violation',
            default => 'Status updated',
        };
    }
}
