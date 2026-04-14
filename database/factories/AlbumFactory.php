<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Album;
use App\Models\Category;
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
            'Royal Blood • Stadium Tour',
            'Muse • Simulation Theory World Tour',
            'Radiohead • A Moon Shaped Pool',
            'The National • Live at Izvestia Hall',
            'Bach Cello Suites • Mstislav Rostropovich',
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
            'Stadium Live, Moscow',
            'Izvestia Hall, Moscow',
            'GlavClub, Moscow',
            'Strelka Institute, Moscow',
            'GES-2, Moscow',
        ];

        $badges = ['✨ NEW', '🔥 FEATURED', '👤 YOUR WORK', null];
        $badgeGradients = [
            'from-pink-500 to-orange-500',
            'from-indigo-600 to-purple-600',
            'from-emerald-500 to-teal-500',
            null,
        ];

        $title = $this->faker->randomElement($titles);

        // Randomly select badge and corresponding gradient
        $badge = $this->faker->randomElement($badges);
        $badgeGradient = $badge ? $this->faker->randomElement(array_filter($badgeGradients)) : null;

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'description' => $this->faker->paragraphs(3, true),
            'cover_image' => 'https://images.unsplash.com/photo-'.$this->faker->randomElement([
                '1501612780327-45045538702b?auto=format&fit=crop&w=600&q=80',
                '1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80',
                '1459749411177-0473ef716170?auto=format&fit=crop&w=600&q=80',
                '1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80',
                '1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=600&q=80',
                '1514320291840-2e0a9bf2f4ae?auto=format&fit=crop&w=600&q=80',
                '1470225620780-dba8ba36b745?auto=format&fit=crop&w=600&q=80',
                '1501281668745-f7f57925c3b4?auto=format&fit=crop&w=600&q=80',
                '1516450360452-93659f5a3f21?auto=format&fit=crop&w=600&q=80',
                '1504609773096-104ff2c73ba4?auto=format&fit=crop&w=600&q=80',
            ]),
            'photographer_id' => User::factory(),
            'venue' => $this->faker->randomElement($venues),
            'event_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'photo_count' => $this->faker->numberBetween(15, 250),
            'rating' => $this->faker->randomFloat(1, 3.5, 5.0),
            'views' => $this->faker->numberBetween(100, 10000),
            'is_published' => $this->faker->boolean(95),
            'badge' => $badge,
            'badge_gradient' => $badgeGradient,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the album is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
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
     * Renamed from new() to fresh() to avoid conflict with Laravel's new() method
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
     * Attach categories to the album after creation.
     */
    public function withCategories($categories): static
    {
        return $this->afterCreating(function (Album $album) use ($categories) {
            if (is_array($categories)) {
                $album->categories()->attach($categories);
            } elseif ($categories instanceof Category) {
                $album->categories()->attach($categories->id);
            } elseif (is_string($categories)) {
                $category = Category::where('slug', $categories)->first();
                if ($category) {
                    $album->categories()->attach($category->id);
                }
            }
        });
    }

    /**
     * Attach random categories to the album.
     */
    public function withRandomCategories(int $count = 1): static
    {
        return $this->afterCreating(function (Album $album) use ($count) {
            $categories = Category::inRandomOrder()->limit($count)->pluck('id');
            $album->categories()->attach($categories);
        });
    }

    /**
     * Create an album for a specific genre.
     */
    public function forGenre(string $genreSlug): static
    {
        return $this->afterCreating(function (Album $album) use ($genreSlug) {
            $category = Category::where('slug', $genreSlug)->first();
            if ($category) {
                $album->categories()->attach($category->id);
            }
        });
    }

    /**
     * Create a music album.
     */
    public function music(): static
    {
        return $this->afterCreating(function (Album $album) {
            $categories = Category::where('type', 'music')->inRandomOrder()->limit(1)->pluck('id');
            $album->categories()->attach($categories);
        });
    }

    /**
     * Create a theater album.
     */
    public function theater(): static
    {
        return $this->afterCreating(function (Album $album) {
            $categories = Category::where('type', 'theater')->inRandomOrder()->limit(1)->pluck('id');
            $album->categories()->attach($categories);
        });
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
     * Create an older album (more than 3 months old).
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_date' => $this->faker->dateTimeBetween('-1 year', '-3 months')->format('Y-m-d'),
        ]);
    }

    /**
     * Configure the factory to create an album with many photos.
     */
    public function manyPhotos(int $count = 100): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_count' => $count,
        ]);
    }

    /**
     * Configure the factory to create an album with few photos.
     */
    public function fewPhotos(int $count = 20): static
    {
        return $this->state(fn (array $attributes) => [
            'photo_count' => $count,
        ]);
    }
}
