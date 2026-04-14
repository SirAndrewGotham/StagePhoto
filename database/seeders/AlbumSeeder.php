<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Album;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlbumSeeder extends Seeder
{
    public function run(): void
    {
        // First, make sure you have a test user
        $photographer = User::first();
        if (! $photographer) {
            $photographer = User::create([
                'name' => 'Test Photographer',
                'email' => 'photographer@stagephoto.test',
                'password' => bcrypt('password'),
            ]);
        }

        $albums = [
            [
                'title' => 'Arctic Monkeys • Live at Luzhniki',
                'slug' => 'arctic-monkeys-live-luzhniki',
                'description' => 'Full concert coverage of Arctic Monkeys at Luzhniki Stadium',
                'cover_image' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Luzhniki Stadium, Moscow',
                'event_date' => '2025-10-15',
                'photo_count' => 24,
                'rating' => 4.9,
                'views' => 1234,
                'badge' => '✨ NEW',
                'badge_gradient' => 'from-pink-500 to-orange-500',
                'category_slug' => 'rock',
            ],
            [
                'title' => 'Swan Lake • Bolshoi Theater Premiere',
                'slug' => 'swan-lake-bolshoi-premiere',
                'description' => 'Opening night of Swan Lake at the historic Bolshoi Theater',
                'cover_image' => 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Bolshoi Theater, Moscow',
                'event_date' => '2025-11-02',
                'photo_count' => 45,
                'rating' => 5.0,
                'views' => 3456,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'ballet',
            ],
            [
                'title' => 'Park Live Festival 2025 • Day 2',
                'slug' => 'park-live-festival-2025-day2',
                'description' => 'Highlights from the second day of Park Live Festival',
                'cover_image' => 'https://images.unsplash.com/photo-1459749411177-0473ef716170?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Park Live, Moscow',
                'event_date' => '2025-08-20',
                'photo_count' => 203,
                'rating' => 4.8,
                'views' => 5678,
                'badge' => '🔥 FEATURED',
                'badge_gradient' => 'from-indigo-600 to-purple-600',
                'category_slug' => 'festivals',
            ],
            [
                'title' => 'Igor Butman Quartet • Intimate Session',
                'slug' => 'igor-butman-jazz-session',
                'description' => 'Exclusive jazz session with Igor Butman Quartet',
                'cover_image' => 'https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Jazz Club, Moscow',
                'event_date' => '2025-09-05',
                'photo_count' => 18,
                'rating' => 4.7,
                'views' => 890,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'jazz',
            ],
            [
                'title' => 'Slayer Tribute • Final Tour Moscow',
                'slug' => 'slayer-tribute-final-tour',
                'description' => 'Tribute concert for Slayer\'s final tour in Moscow',
                'cover_image' => 'https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Arena Moscow',
                'event_date' => '2025-10-30',
                'photo_count' => 67,
                'rating' => 4.9,
                'views' => 2345,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'metal',
            ],
            [
                'title' => 'Hamlet • Taganka Theater Revival',
                'slug' => 'hamlet-taganka-theater',
                'description' => 'Modern interpretation of Shakespeare\'s Hamlet',
                'cover_image' => 'https://images.unsplash.com/photo-1514320291840-2e0a9bf2f4ae?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Taganka Theater, Moscow',
                'event_date' => '2025-11-10',
                'photo_count' => 31,
                'rating' => 4.8,
                'views' => 1234,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'drama',
            ],
            [
                'title' => 'Molchat Doma • 16 Tons Club',
                'slug' => 'molchat-doma-16-tons',
                'description' => 'Post-punk night with Molchat Doma',
                'cover_image' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=600&q=80',
                'venue' => '16 Tons Club, Moscow',
                'event_date' => '2025-11-18',
                'photo_count' => 39,
                'rating' => 5.0,
                'views' => 4567,
                'badge' => '👤 YOUR WORK',
                'badge_gradient' => 'from-emerald-500 to-teal-500',
                'category_slug' => 'rock',
            ],
            [
                'title' => 'Valery Gergiev • Tchaikovsky Symphony No. 5',
                'slug' => 'valery-gergiev-tchaikovsky',
                'description' => 'Full symphony performance by Mariinsky Orchestra',
                'cover_image' => 'https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Tchaikovsky Hall, Moscow',
                'event_date' => '2025-10-08',
                'photo_count' => 28,
                'rating' => 4.9,
                'views' => 3456,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'classical',
            ],
            [
                'title' => 'Nina Kraviz • Garage Live Set',
                'slug' => 'nina-kraviz-garage-set',
                'description' => 'Electronic music night with Nina Kraviz',
                'cover_image' => 'https://images.unsplash.com/photo-1516450360452-93659f5a3f21?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Garage Club, Moscow',
                'event_date' => '2025-11-22',
                'photo_count' => 52,
                'rating' => 4.8,
                'views' => 2345,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'electronic',
            ],
            [
                'title' => 'Pelageya • Open Air Folk Fest',
                'slug' => 'pelageya-folk-fest',
                'description' => 'Folk music festival featuring Pelageya',
                'cover_image' => 'https://images.unsplash.com/photo-1504609773096-104ff2c73ba4?auto=format&fit=crop&w=600&q=80',
                'venue' => 'Folk Festival, Moscow',
                'event_date' => '2025-09-28',
                'photo_count' => 34,
                'rating' => 4.7,
                'views' => 1234,
                'badge' => null,
                'badge_gradient' => null,
                'category_slug' => 'folk',
            ],
        ];

        foreach ($albums as $albumData) {
            $categorySlug = $albumData['category_slug'];
            unset($albumData['category_slug']);

            $album = Album::create($albumData + ['photographer_id' => $photographer->id]);

            // Attach category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $album->categories()->attach($category->id);
            }

            $album->comments()->createMany([
                [
                    'user_id' => $photographer->id,
                    'content' => 'This was an amazing show! Check out these highlights.',
                    'is_approved' => true,
                    'likes' => random_int(5, 30),
                    'created_at' => now()->subDays(random_int(1, 10)),
                ],
                [
                    'user_id' => $photographer->id,
                    'content' => 'The lighting was incredible that night.',
                    'is_approved' => true,
                    'likes' => random_int(0, 15),
                    'created_at' => now()->subDays(random_int(1, 10)),
                ],
            ]);
        }

        $this->command->info('Albums seeded successfully!');
    }
}
